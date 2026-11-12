<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\OfficerBast;
use App\Models\User;
use App\Services\GoogleSheetService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OfficerBastController extends Controller
{
    protected GoogleSheetService $sheetService;

    public function __construct(GoogleSheetService $sheetService)
    {
        $this->sheetService = $sheetService;
    }

    public function index(Request $request)
    {
        $query = OfficerBast::with(['officer', 'assignment', 'recipients.user']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('bast_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('document_title', 'like', "%{$search}%")
                  ->orWhere('activity_subtitle', 'like', "%{$search}%")
                  ->orWhereHas('officer', fn ($o) => $o->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('recipients', fn ($r) => $r->where('recipient_name', 'like', "%{$search}%"))
                  ->orWhereHas('assignment', fn ($a) => $a->where('title', 'like', "%{$search}%"));
            });
        }

        if ($assignmentId = $request->input('assignment_id')) {
            $query->where('assignment_id', $assignmentId);
        }

        if ($officerId = $request->input('officer_id')) {
            $query->forOfficer((int) $officerId);
        }

        if ($bastType = $request->input('bast_type')) {
            if ($bastType === 'kantor_ke_petugas') {
                $query->where(function ($q) {
                    $q->where('bast_type', 'kantor_ke_petugas')
                      ->orWhereNull('bast_type');
                });
            } else {
                $query->where('bast_type', $bastType);
            }
        }

        $totalBasts = OfficerBast::count();
        $thisMonthBasts = OfficerBast::whereMonth('bast_date', now()->month)
                                     ->whereYear('bast_date', now()->year)
                                     ->count();

        $basts = $query->latest('bast_date')->latest('id')->paginate(10)->withQueryString();

        $assignments = Assignment::with(['officers' => fn ($q) => $q->orderBy('name')])
            ->orderBy('title')
            ->get();
        $officers = User::fieldStaff()->where('status', 'active')->orderBy('name')->get();

        return view('admin.basts.officers.index', compact(
            'basts',
            'totalBasts',
            'thisMonthBasts',
            'assignments',
            'officers'
        ));
    }

    public function store(Request $request)
    {
        [$bastData, $recipients] = $this->validateAndPrepare($request);

        if (empty($bastData['bast_number'])) {
            $bastData['bast_number'] = OfficerBast::generateBastNumber($bastData['bast_type']);
        }

        $bast = DB::transaction(function () use ($bastData, $recipients) {
            $created = OfficerBast::create($bastData);
            $this->syncRecipients($created, $recipients);

            return $created;
        });

        $bast->load(['officer', 'recipients']);
        $this->syncSheetRow($bast, true);

        ActivityLog::log(
            'create',
            'basts',
            "Menerbitkan BAST Petugas nomor {$bast->bast_number} ({$bast->recipient_summary})."
        );

        return redirect()->route('admin.basts.officers.index')
            ->with('success', "BAST Petugas '{$bast->bast_number}' berhasil dibuat dan siap diunduh!");
    }

    public function update(Request $request, OfficerBast $bast)
    {
        [$bastData, $recipients] = $this->validateAndPrepare($request, $bast);

        if (empty($bastData['bast_number'])) {
            $bastData['bast_number'] = $bast->bast_number;
        }

        DB::transaction(function () use ($bast, $bastData, $recipients) {
            $bast->update($bastData);
            $this->syncRecipients($bast, $recipients);
        });

        $bast->load(['officer', 'recipients']);
        $this->syncSheetRow($bast, false);

        ActivityLog::log(
            'update',
            'basts',
            "Memperbarui dokumen BAST Petugas nomor {$bast->bast_number} ({$bast->recipient_summary})."
        );

        return redirect()->route('admin.basts.officers.index')
            ->with('success', "BAST Petugas '{$bast->bast_number}' berhasil diperbarui!");
    }

    public function downloadPdf(OfficerBast $bast)
    {
        return $this->renderPdf($bast)->download($bast->pdfFilename());
    }

    public function previewPdf(OfficerBast $bast)
    {
        return $this->renderPdf($bast)->stream($bast->pdfFilename());
    }

    public function destroy(OfficerBast $bast)
    {
        $number = $bast->bast_number;
        $id = $bast->id;
        $bast->delete();

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->delete('officer_basts', $id, true);
        }

        ActivityLog::log(
            'delete',
            'basts',
            "Menghapus dokumen BAST Petugas nomor {$number}."
        );

        return back()->with('success', "Dokumen BAST '{$number}' berhasil dihapus.");
    }

    private function validateAndPrepare(Request $request, ?OfficerBast $existing = null): array
    {
        $bastType = $request->input('bast_type', $existing?->bast_type ?? 'kantor_ke_petugas');

        $uniqueRule = Rule::unique('officer_basts', 'bast_number');
        if ($existing) {
            $uniqueRule->ignore($existing->id);
        }

        $rules = [
            'assignment_id' => ['required', 'exists:assignments,id'],
            'bast_type' => ['nullable', Rule::in(['kantor_ke_petugas', 'petugas_ke_kantor'])],
            'bast_date' => ['required', 'date'],
            'bast_number' => ['nullable', 'string', 'max:100', $uniqueRule],
            'notes' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['draft', 'finalized', 'archived'])],
        ];

        if ($bastType === 'petugas_ke_kantor') {
            $rules['officer_id'] = ['required', 'exists:users,id'];
            $rules['description'] = ['required', 'string'];
        } else {
            $rules['officer_id'] = ['nullable', 'exists:users,id'];
            $rules['description'] = ['nullable', 'string'];
            $rules['document_title'] = ['nullable', 'string', 'max:255'];
            $rules['activity_subtitle'] = ['nullable', 'string', 'max:255'];
            $rules['institution_name'] = ['nullable', 'string', 'max:255'];
            $rules['document_place'] = ['nullable', 'string', 'max:255'];
            $rules['maker_name'] = ['nullable', 'string', 'max:255'];
            $rules['maker_nip'] = ['nullable', 'string', 'max:100'];
            $rules['items'] = ['nullable', 'array'];
            $rules['items.*'] = ['nullable', 'string', 'max:255'];
            $rules['recipients'] = ['nullable', 'array'];
            $rules['recipients.*.user_id'] = ['nullable', 'exists:users,id'];
            $rules['recipients.*.recipient_name'] = ['nullable', 'string', 'max:255'];
            $rules['recipients.*.position_unit'] = ['nullable', 'string', 'max:255'];
            $rules['recipients.*.origin_role'] = ['nullable', 'string', 'max:255'];
        }

        $validated = $request->validate($rules, [
            'assignment_id.required' => 'Kegiatan wajib dipilih.',
            'officer_id.required' => 'Petugas wajib dipilih.',
            'bast_date.required' => 'Tanggal BAST wajib diisi.',
            'description.required' => 'Rincian serah terima dokumen/perlengkapan wajib diisi.',
            'bast_number.unique' => 'Nomor BAST tersebut sudah terdaftar.',
        ]);

        $assignment = Assignment::findOrFail($validated['assignment_id']);
        $validated['bast_type'] = $bastType;

        if ($bastType === 'petugas_ke_kantor') {
            $bastData = [
                'assignment_id' => $validated['assignment_id'],
                'officer_id' => $validated['officer_id'],
                'bast_type' => 'petugas_ke_kantor',
                'bast_date' => $validated['bast_date'],
                'bast_number' => $validated['bast_number'] ?? null,
                'description' => trim($validated['description']),
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'],
                'document_title' => null,
                'activity_subtitle' => null,
                'institution_name' => null,
                'document_place' => null,
                'maker_name' => null,
                'maker_nip' => null,
            ];

            return [$bastData, []];
        }

        // Kolektif Blanko & Perlengkapan (kantor_ke_petugas)
        $cleanRecipients = [];
        foreach ($request->input('recipients', []) as $row) {
            $name = trim((string) ($row['recipient_name'] ?? ''));
            $userId = !empty($row['user_id']) ? (int) $row['user_id'] : null;

            if ($name === '' && $userId) {
                $user = User::find($userId);
                $name = $user?->name ?? '';
            }
            if ($name === '') {
                continue;
            }

            $pos = trim((string) ($row['position_unit'] ?? ''));
            $origin = trim((string) ($row['origin_role'] ?? ''));

            $cleanRecipients[] = [
                'user_id' => $userId,
                'recipient_name' => $name,
                'position_unit' => $pos !== '' ? $pos : 'Mitra',
                'origin_role' => $origin !== '' ? $origin : ($assignment->location ?? '-'),
                'sort_order' => count($cleanRecipients) + 1,
            ];
        }

        if (empty($cleanRecipients) && !empty($validated['officer_id'])) {
            $singleUser = User::find($validated['officer_id']);
            if ($singleUser) {
                $cleanRecipients[] = [
                    'user_id' => $singleUser->id,
                    'recipient_name' => $singleUser->name,
                    'position_unit' => $singleUser->position ?: ($singleUser->isPegawai() ? 'Pegawai BPS' : 'Mitra'),
                    'origin_role' => $assignment->location ?? '-',
                    'sort_order' => 1,
                ];
            }
        }

        if (empty($cleanRecipients)) {
            throw ValidationException::withMessages([
                'recipients' => ['Minimal 1 petugas / peserta penerima blanko & perlengkapan wajib dipilih.'],
            ]);
        }

        $cleanItems = [];
        foreach ($request->input('items', []) as $itemText) {
            $trimmed = trim((string) $itemText);
            if ($trimmed !== '') {
                $cleanItems[] = $trimmed;
            }
        }

        $descriptionText = !empty($cleanItems)
            ? implode("\n", $cleanItems)
            : trim((string) ($validated['description'] ?? ''));

        if ($descriptionText === '') {
            throw ValidationException::withMessages([
                'items' => ['Minimal 1 rincian barang / blanko perlengkapan wajib diisi.'],
            ]);
        }

        $year = date('Y', strtotime($validated['bast_date']));
        $authUser = Auth::user();

        $bastData = [
            'assignment_id' => $validated['assignment_id'],
            'officer_id' => $cleanRecipients[0]['user_id'] ?? null,
            'bast_type' => 'kantor_ke_petugas',
            'bast_date' => $validated['bast_date'],
            'bast_number' => $validated['bast_number'] ?? null,
            'description' => $descriptionText,
            'document_title' => trim((string) ($validated['document_title'] ?? '')) ?: 'Tanda Terima Blanko & Perlengkapan',
            'activity_subtitle' => trim((string) ($validated['activity_subtitle'] ?? '')) ?: $assignment->title,
            'institution_name' => trim((string) ($validated['institution_name'] ?? '')) ?: "BPS Kabupaten Kolaka Utara Tahun {$year}",
            'document_place' => trim((string) ($validated['document_place'] ?? '')) ?: ($assignment->location ?: 'Lasusua'),
            'maker_name' => trim((string) ($validated['maker_name'] ?? '')) ?: ($authUser?->name ?? 'Koordinator Logistik BPS'),
            'maker_nip' => trim((string) ($validated['maker_nip'] ?? '')),
            'notes' => $validated['notes'] ?? null,
            'status' => $validated['status'],
        ];

        return [$bastData, $cleanRecipients];
    }

    private function syncRecipients(OfficerBast $bast, array $recipients): void
    {
        $bast->recipients()->delete();

        if (!empty($recipients)) {
            $bast->recipients()->createMany($recipients);
        }
    }

    private function renderPdf(OfficerBast $bast)
    {
        $bast->load(['officer', 'assignment', 'recipients']);

        return Pdf::loadView($bast->pdfViewName(), compact('bast'))
            ->setPaper('a4', 'portrait');
    }

    private function syncSheetRow(OfficerBast $bast, bool $isInsert): void
    {
        if (!$this->sheetService->isConfigured()) {
            return;
        }

        $payload = [
            'id' => $bast->id,
            'bast_number' => $bast->bast_number,
            'bast_type' => $bast->bast_type,
            'bast_date' => $bast->bast_date->toDateString(),
            'assignment_id' => $bast->assignment_id,
            'officer_id' => $bast->officer_id,
            'description' => $bast->description,
            'notes' => $bast->notes ?? '',
            'status' => $bast->status,
            'updated_at' => now()->toISOString(),
        ];

        if ($isInsert) {
            $payload['created_at'] = now()->toISOString();
            $this->sheetService->insert('officer_basts', $payload);
        } else {
            $this->sheetService->update('officer_basts', $bast->id, $payload);
        }
    }
}

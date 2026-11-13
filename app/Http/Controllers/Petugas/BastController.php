<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\AssignmentOfficer;
use App\Models\OfficerBast;
use App\Models\Respondent;
use App\Models\RespondentBast;
use App\Models\User;
use App\Services\GoogleSheetService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BastController extends Controller
{
    protected GoogleSheetService $sheetService;

    public function __construct(GoogleSheetService $sheetService)
    {
        $this->sheetService = $sheetService;
    }

    // ==========================================
    // BAST RESPONDEN (PORTAL PETUGAS)
    // ==========================================
    public function respondents(Request $request)
    {
        $userId = Auth::id();

        $basts = RespondentBast::with(['respondent', 'assignment'])
            ->where('officer_id', $userId)
            ->latest('bast_date')
            ->paginate(10);

        // Load all respondents allocated to this officer in stable sample order (No. Resp 1, 2, 3...)
        $myRespondents = Respondent::with('basts')
            ->where('officer_id', $userId)
            ->orderBy('id')
            ->get();

        $completedCount = $myRespondents->filter(fn($r) => $r->basts->isNotEmpty())->count();
        $pendingCount = $myRespondents->filter(fn($r) => $r->basts->isEmpty())->count();

        return view('petugas.basts.respondents', compact('basts', 'myRespondents', 'completedCount', 'pendingCount'));
    }

    public function storeRespondent(Request $request)
    {
        $validated = $request->validate([
            'respondent_id' => ['required', 'exists:respondents,id'],
            'respondent_number' => ['nullable', 'integer', 'min:1'],
            'handover_category' => ['required', Rule::in(['ATK', 'KUES'])],
            'bast_date' => ['required', 'date'],
            'recipient_type' => ['required', Rule::in(['krt', 'representative'])],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'recipient_relation' => ['nullable', 'string', 'max:100'],
            'handover_items' => ['required', 'string'],
            'signature_data' => ['nullable', 'string'],
            'officer_signature_data' => ['nullable', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'respondent_id.required' => 'Responden wajib dipilih.',
            'handover_category.required' => 'Jenis tanda terima (ATK atau KUES) wajib dipilih.',
            'bast_date.required' => 'Tanggal BAST wajib diisi.',
            'recipient_type.required' => 'Pihak penerima wajib dipilih (KRT atau Wakil).',
            'handover_items.required' => 'Rincian berkas wajib diisi.',
        ]);

        $respondent = Respondent::findOrFail($validated['respondent_id']);

        // Jika penerima adalah selain KRT (wakil), pastikan nama dan hubungan terisi
        if ($validated['recipient_type'] === 'representative') {
            if (empty(trim($validated['recipient_name'] ?? ''))) {
                return back()->withInput()->with('error', 'Nama penerima selain KRT (Wakil/Anggota Keluarga) wajib diisi.');
            }
            if (empty(trim($validated['recipient_relation'] ?? ''))) {
                return back()->withInput()->with('error', 'Status hubungan penerima dengan Kepala Keluarga (misal: Pasangan/Anak) wajib diisi.');
            }
        }

        // Simpan Tanda Tangan Digital Penerima / Responden jika ada
        $signaturePath = null;
        if (!empty($request->input('signature_data')) && str_starts_with($request->input('signature_data'), 'data:image')) {
            $data = $request->input('signature_data');
            $data = substr($data, strpos($data, ',') + 1);
            $data = base64_decode($data);
            $fileName = 'signatures/' . uniqid('sign_resp_') . '.png';
            Storage::disk('public')->put($fileName, $data);
            $signaturePath = $fileName;
        }

        // Simpan Tanda Tangan Petugas jika ada
        $officerSignaturePath = null;
        if (!empty($request->input('officer_signature_data')) && str_starts_with($request->input('officer_signature_data'), 'data:image')) {
            $data = $request->input('officer_signature_data');
            $data = substr($data, strpos($data, ',') + 1);
            $data = base64_decode($data);
            $fileName = 'signatures/' . uniqid('sign_off_') . '.png';
            Storage::disk('public')->put($fileName, $data);
            $officerSignaturePath = $fileName;
        }

        $year = now()->format('Y');
        $romanMonths = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        $month = $romanMonths[now()->month - 1];
        $count = RespondentBast::whereYear('created_at', now()->year)->count() + 1;
        $bastNumber = sprintf('BAST-RESP/%s/%s/%04d', $year, $month, $count);

        $respNumber = $validated['respondent_number'] ?? null;
        if (empty($respNumber)) {
            $orderedIds = Respondent::where('officer_id', Auth::id())->orderBy('id')->pluck('id')->values();
            $foundIdx = $orderedIds->search($respondent->id);
            $respNumber = $foundIdx !== false ? ($foundIdx + 1) : 1;
        }

        $bast = RespondentBast::create([
            'bast_number' => $bastNumber,
            'bast_date' => $validated['bast_date'],
            'respondent_id' => $respondent->id,
            'respondent_number' => (int) $respNumber,
            'handover_category' => strtoupper($validated['handover_category']),
            'assignment_id' => $respondent->assignment_id,
            'officer_id' => Auth::id(),
            'handover_items' => $validated['handover_items'],
            'recipient_type' => $validated['recipient_type'],
            'recipient_name' => $validated['recipient_type'] === 'representative' ? trim($validated['recipient_name']) : $respondent->name,
            'recipient_relation' => $validated['recipient_type'] === 'representative' ? trim($validated['recipient_relation']) : 'Kepala Rumah Tangga (KRT)',
            'signature_path' => $signaturePath,
            'officer_signature_path' => $officerSignaturePath,
            'notes' => $validated['notes'] ?? null,
            'status' => 'finalized',
        ]);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->insert('respondent_basts', [
                'id' => $bast->id,
                'bast_number' => $bast->bast_number,
                'bast_date' => $bast->bast_date->toDateString(),
                'respondent_id' => $bast->respondent_id,
                'assignment_id' => $bast->assignment_id ?? '',
                'officer_id' => $bast->officer_id,
                'handover_items' => $bast->handover_items,
                'notes' => $bast->notes ?? '',
                'status' => $bast->status,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ]);
        }

        ActivityLog::log(
            'create',
            'basts',
            "Petugas " . Auth::user()->name . " menerbitkan BAST Responden {$bast->bast_number} untuk {$respondent->name}."
        );

        return redirect()->route('petugas.basts.respondents.index')
            ->with('success', "Dokumen BAST '{$bast->bast_number}' berhasil dibuat dan siap diunduh!");
    }

    public function downloadRespondentPdf(RespondentBast $bast)
    {
        if ($bast->officer_id !== Auth::id()) {
            abort(403);
        }

        $bast->load(['respondent', 'officer', 'assignment']);
        $pdf = Pdf::loadView('pdf.bast_respondent', compact('bast'))->setPaper('a4', 'portrait');

        return $pdf->download('BAST-Responden-' . str_replace(['/', '\\'], '-', $bast->bast_number) . '.pdf');
    }

    public function previewRespondentPdf(RespondentBast $bast)
    {
        if ($bast->officer_id !== Auth::id()) {
            abort(403);
        }

        $bast->load(['respondent', 'officer', 'assignment']);
        $pdf = Pdf::loadView('pdf.bast_respondent', compact('bast'))->setPaper('a4', 'portrait');

        return $pdf->stream('BAST-Responden-' . str_replace(['/', '\\'], '-', $bast->bast_number) . '.pdf');
    }

    // ==========================================
    // BAST PETUGAS (PORTAL PETUGAS)
    // ==========================================
    public function officers(Request $request)
    {
        $userId = Auth::id();
        $currentType = $request->query('type'); // 'kantor_ke_petugas', 'petugas_ke_kantor', or null

        $query = OfficerBast::with(['assignment', 'recipients'])
            ->forOfficer($userId);

        if ($currentType === 'kantor_ke_petugas') {
            $query->where(function ($q) {
                $q->where('bast_type', 'kantor_ke_petugas')
                  ->orWhereNull('bast_type');
            });
        } elseif ($currentType === 'petugas_ke_kantor') {
            $query->where('bast_type', 'petugas_ke_kantor');
        }

        $basts = $query->latest('bast_date')->latest('id')->paginate(10)->withQueryString();

        $countBlanko = OfficerBast::forOfficer($userId)
            ->where(function ($q) {
                $q->where('bast_type', 'kantor_ke_petugas')
                  ->orWhereNull('bast_type');
            })->count();

        $countTerisi = OfficerBast::forOfficer($userId)
            ->where('bast_type', 'petugas_ke_kantor')
            ->count();

        $user = Auth::user();
        $myAssignmentIds = AssignmentOfficer::where('user_id', $userId)->pluck('assignment_id');
        $assignmentsQuery = Assignment::with(['officers' => fn ($q) => $q->orderBy('name')])
            ->orderBy('title');

        if (!$user->isPegawai()) {
            $assignmentsQuery->whereIn('id', $myAssignmentIds);
        }
        $myAssignments = $assignmentsQuery->get();

        $officers = User::fieldStaff()->where('status', 'active')->orderBy('name')->get();

        return view('petugas.basts.officers', compact(
            'basts',
            'myAssignments',
            'officers',
            'currentType',
            'countBlanko',
            'countTerisi'
        ));
    }

    public function storeOfficer(Request $request)
    {
        $bastType = $request->input('bast_type', 'kantor_ke_petugas');

        $rules = [
            'assignment_id' => ['required', 'exists:assignments,id'],
            'bast_type' => ['required', Rule::in(['kantor_ke_petugas', 'petugas_ke_kantor'])],
            'notes' => ['nullable', 'string', 'max:500'],
        ];

        if ($bastType === 'petugas_ke_kantor') {
            $rules['description'] = ['required', 'string'];
        } else {
            $rules['receipt_category'] = ['nullable', Rule::in(['PELATIHAN', 'ATK', 'KUES', 'TIMBANGAN'])];
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
            'bast_type.required' => 'Jenis BAST wajib dipilih.',
            'description.required' => 'Rincian serah terima dokumen/perlengkapan wajib diisi.',
        ]);

        $bastNumber = OfficerBast::generateBastNumber($validated['bast_type']);
        $assignment = Assignment::findOrFail($validated['assignment_id']);
        $user = Auth::user();
        $bastDate = now()->toDateString();

        if ($validated['bast_type'] === 'petugas_ke_kantor') {
            $bast = OfficerBast::create([
                'bast_number' => $bastNumber,
                'bast_type' => 'petugas_ke_kantor',
                'bast_date' => $bastDate,
                'assignment_id' => $validated['assignment_id'],
                'officer_id' => $user->id,
                'description' => trim($validated['description']),
                'notes' => $validated['notes'] ?? null,
                'status' => 'finalized',
            ]);
        } else {
            [$bastAttributes, $recipients] = OfficerBast::prepareCollectivePayload(
                $request,
                $assignment,
                $bastDate,
                $user,
                'Koordinator Logistik BPS',
                ''
            );

            $bastAttributes['bast_number'] = $bastNumber;
            $bastAttributes['notes'] = $validated['notes'] ?? null;
            $bastAttributes['status'] = 'finalized';

            $bast = DB::transaction(function () use ($bastAttributes, $recipients) {
                $created = OfficerBast::create($bastAttributes);
                $created->syncRecipients($recipients);

                return $created;
            });
        }

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->insert('officer_basts', [
                'id' => $bast->id,
                'bast_number' => $bast->bast_number,
                'bast_type' => $bast->bast_type,
                'bast_date' => $bast->bast_date->toDateString(),
                'assignment_id' => $bast->assignment_id,
                'officer_id' => $bast->officer_id,
                'description' => $bast->description,
                'notes' => $bast->notes ?? '',
                'status' => $bast->status,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ]);
        }

        $typeText = $bast->bast_type === 'kantor_ke_petugas' ? 'BAST Blanko & Perlengkapan' : 'BAST Dokumen Terisi';

        ActivityLog::log(
            'create',
            'basts',
            "Petugas " . Auth::user()->name . " menerbitkan {$typeText} {$bast->bast_number}."
        );

        return redirect()->route('petugas.basts.officers.index', ['type' => $bast->bast_type])
            ->with('success', "{$typeText} '{$bast->bast_number}' berhasil dibuat!");
    }

    public function signOfficerReceipt(Request $request, OfficerBast $bast)
    {
        $user = Auth::user();

        if (!$bast->includesOfficer($user->id)) {
            abort(403, 'Anda tidak terdaftar sebagai penerima pada dokumen ini.');
        }

        $validated = $request->validate([
            'verify_goods' => ['required', 'accepted'],
            'signature_data' => ['required', 'string'],
            'verification_note' => ['nullable', 'string', 'max:500'],
        ], [
            'verify_goods.accepted' => 'Anda wajib mencentang persetujuan verifikasi bahwa barang/perlengkapan telah diperiksa dan diterima secara lengkap.',
            'signature_data.required' => 'Goresan tanda tangan digital wajib dibubuhkan.',
        ]);

        if (!str_starts_with($validated['signature_data'], 'data:image')) {
            return back()->with('error', 'Format tanda tangan digital tidak valid.');
        }

        $data = $validated['signature_data'];
        $data = substr($data, strpos($data, ',') + 1);
        $data = base64_decode($data);

        // Auto-crop area transparan canvas agar goresan tanda tangan padat, tebal & tampak besar
        if (function_exists('imagecreatefromstring') && function_exists('imagecrop')) {
            $im = @imagecreatefromstring($data);
            if ($im) {
                $w = imagesx($im);
                $h = imagesy($im);
                $minX = $w; $minY = $h; $maxX = 0; $maxY = 0;
                $found = false;

                for ($x = 0; $x < $w; $x++) {
                    for ($y = 0; $y < $h; $y++) {
                        $rgba = imagecolorat($im, $x, $y);
                        $alpha = ($rgba & 0x7F000000) >> 24;
                        if ($alpha < 120) {
                            $found = true;
                            if ($x < $minX) $minX = $x;
                            if ($x > $maxX) $maxX = $x;
                            if ($y < $minY) $minY = $y;
                            if ($y > $maxY) $maxY = $y;
                        }
                    }
                }

                if ($found) {
                    $pad = 6;
                    $cropX = max(0, $minX - $pad);
                    $cropY = max(0, $minY - $pad);
                    $cropW = min($w - $cropX, ($maxX - $minX + 1) + ($pad * 2));
                    $cropH = min($h - $cropY, ($maxY - $minY + 1) + ($pad * 2));

                    $cropped = @imagecrop($im, ['x' => $cropX, 'y' => $cropY, 'width' => $cropW, 'height' => $cropH]);
                    if ($cropped) {
                        imagesavealpha($cropped, true);
                        ob_start();
                        imagepng($cropped);
                        $croppedData = ob_get_clean();
                        imagedestroy($cropped);
                        if (!empty($croppedData)) {
                            $data = $croppedData;
                        }
                    }
                }
                imagedestroy($im);
            }
        }

        $fileName = 'signatures/' . uniqid('sign_blanko_' . $user->id . '_') . '.png';
        Storage::disk('public')->put($fileName, $data);

        $recipient = $bast->recipientForUser($user->id, $user->name);

        if (!$recipient) {
            $recipient = $bast->recipients()->create([
                'user_id' => $user->id,
                'recipient_name' => $user->name,
                'position_unit' => $user->position ?: ($user->isPegawai() ? 'Pegawai BPS' : 'Mitra'),
                'origin_role' => $user->roleLabel(),
                'sort_order' => $bast->recipients()->count() + 1,
            ]);
        }

        $recipient->update([
            'user_id' => $user->id,
            'signature_path' => $fileName,
            'signed_at' => now(),
            'goods_received' => true,
            'goods_received_at' => now(),
            'verification_note' => $validated['verification_note'] ?? null,
        ]);

        $title = $bast->document_title ?: 'Tanda Terima Petugas';
        ActivityLog::log(
            'update',
            'basts',
            "Petugas {$user->name} memverifikasi penerimaan barang dan menandatangani {$title} ({$bast->bast_number})."
        );

        return back()->with('success', "Penerimaan barang telah diverifikasi dan tanda tangan digital Anda untuk '{$bast->bast_number}' berhasil disimpan!");
    }

    public function downloadOfficerPdf(OfficerBast $bast)
    {
        if (!$bast->includesOfficer(Auth::id())) {
            abort(403);
        }

        $bast->load(['officer', 'assignment', 'recipients']);
        $pdf = Pdf::loadView($bast->pdfViewName(), compact('bast'))->setPaper('a4', 'portrait');

        return $pdf->download($bast->pdfFilename());
    }

    public function previewOfficerPdf(OfficerBast $bast)
    {
        if (!$bast->includesOfficer(Auth::id())) {
            abort(403);
        }

        $bast->load(['officer', 'assignment', 'recipients']);
        $pdf = Pdf::loadView($bast->pdfViewName(), compact('bast'))->setPaper('a4', 'portrait');

        return $pdf->stream($bast->pdfFilename());
    }
}


<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\Respondent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentProgressController extends Controller
{
    /**
     * Tampilan Utama Monitoring Progres Pengolahan Dokumen Kuesioner BPS.
     */
    public function index(Request $request)
    {
        $selectedAssignmentId = $request->input('assignment_id');
        $selectedDistrict = $request->input('district');
        $selectedStatus = $request->input('processing_status');
        $search = $request->input('search');

        // 1. Data Dropdown Pilihan
        $assignments = Assignment::orderBy('title')->get();
        $districts = Respondent::DISTRICTS;

        // 2. Base Query Filter Responden / Dokumen
        $baseQuery = Respondent::query();

        if ($selectedAssignmentId) {
            $baseQuery->where('assignment_id', $selectedAssignmentId);
        }
        if ($selectedDistrict) {
            $baseQuery->where('district', $selectedDistrict);
        }

        // 3. Statistik KPI Ringkasan 4 Pilar Pengolahan Dokumen
        $totalDocs = (clone $baseQuery)->count();
        $fieldCompletedCount = (clone $baseQuery)->where(function ($q) {
            $q->where('progress_status', 'selesai')->orHas('basts');
        })->count();

        // 4 Tahap Pengolahan BPS
        $receivingCount = (clone $baseQuery)->whereIn('processing_status', ['receiving', 'editing_coding', 'entry', 'clean'])->count();
        $editingCount = (clone $baseQuery)->whereIn('processing_status', ['editing_coding', 'entry', 'clean'])->count();
        $entryCount = (clone $baseQuery)->whereIn('processing_status', ['entry', 'clean'])->count();
        $cleanCount = (clone $baseQuery)->where('processing_status', 'clean')->count();
        $notReceivedCount = max(0, $totalDocs - $receivingCount);

        // Persentase Capaian
        $receivingPct = $totalDocs > 0 ? min(100, round(($receivingCount / $totalDocs) * 100, 1)) : 0;
        $editingPct = $totalDocs > 0 ? min(100, round(($editingCount / $totalDocs) * 100, 1)) : 0;
        $entryPct = $totalDocs > 0 ? min(100, round(($entryCount / $totalDocs) * 100, 1)) : 0;
        $cleanPct = $totalDocs > 0 ? min(100, round(($cleanCount / $totalDocs) * 100, 1)) : 0;

        // 4. TAB 1: Daftar Dokumen Kuesioner (Tabel Dokumen Detail)
        $docQuery = (clone $baseQuery)->with(['assignment', 'officer', 'basts']);

        if ($selectedStatus) {
            $docQuery->where('processing_status', $selectedStatus);
        }

        if ($search) {
            $docQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('family_head_name', 'like', "%{$search}%")
                  ->orWhere('informant_name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('agency', 'like', "%{$search}%")
                  ->orWhereHas('officer', fn ($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $documents = $docQuery->latest('id')->paginate(10)->withQueryString();

        // 5. TAB 2: Sebaran Pengolahan per Wilayah Kecamatan (15 Kecamatan Kolaka Utara)
        $districtStats = [];
        foreach ($districts as $d) {
            $dQuery = Respondent::where('district', $d);
            if ($selectedAssignmentId) {
                $dQuery->where('assignment_id', $selectedAssignmentId);
            }
            $dTotal = (clone $dQuery)->count();
            $dReceived = (clone $dQuery)->whereIn('processing_status', ['receiving', 'editing_coding', 'entry', 'clean'])->count();
            $dEdited = (clone $dQuery)->whereIn('processing_status', ['editing_coding', 'entry', 'clean'])->count();
            $dEntered = (clone $dQuery)->whereIn('processing_status', ['entry', 'clean'])->count();
            $dClean = (clone $dQuery)->where('processing_status', 'clean')->count();

            $districtStats[] = [
                'district' => $d,
                'total' => $dTotal,
                'received' => $dReceived,
                'received_pct' => $dTotal > 0 ? round(($dReceived / $dTotal) * 100, 1) : 0,
                'edited' => $dEdited,
                'edited_pct' => $dTotal > 0 ? round(($dEdited / $dTotal) * 100, 1) : 0,
                'entered' => $dEntered,
                'entered_pct' => $dTotal > 0 ? round(($dEntered / $dTotal) * 100, 1) : 0,
                'clean' => $dClean,
                'clean_pct' => $dTotal > 0 ? round(($dClean / $dTotal) * 100, 1) : 0,
            ];
        }

        // 6. TAB 3: Progres per Kegiatan Survei & Sensus
        $assignmentStats = [];
        foreach ($assignments as $asgn) {
            $aQuery = Respondent::where('assignment_id', $asgn->id);
            if ($selectedDistrict) {
                $aQuery->where('district', $selectedDistrict);
            }
            $aTotal = (clone $aQuery)->count();
            $aReceived = (clone $aQuery)->whereIn('processing_status', ['receiving', 'editing_coding', 'entry', 'clean'])->count();
            $aEdited = (clone $aQuery)->whereIn('processing_status', ['editing_coding', 'entry', 'clean'])->count();
            $aEntered = (clone $aQuery)->whereIn('processing_status', ['entry', 'clean'])->count();
            $aClean = (clone $aQuery)->where('processing_status', 'clean')->count();

            $assignmentStats[] = [
                'id' => $asgn->id,
                'title' => $asgn->title,
                'total' => $aTotal,
                'received' => $aReceived,
                'received_pct' => $aTotal > 0 ? round(($aReceived / $aTotal) * 100, 1) : 0,
                'edited' => $aEdited,
                'edited_pct' => $aTotal > 0 ? round(($aEdited / $aTotal) * 100, 1) : 0,
                'entered' => $aEntered,
                'entered_pct' => $aTotal > 0 ? round(($aEntered / $aTotal) * 100, 1) : 0,
                'clean' => $aClean,
                'clean_pct' => $aTotal > 0 ? round(($aClean / $aTotal) * 100, 1) : 0,
            ];
        }

        return view('admin.progress.documents', compact(
            'assignments',
            'districts',
            'selectedAssignmentId',
            'selectedDistrict',
            'selectedStatus',
            'search',
            'totalDocs',
            'fieldCompletedCount',
            'receivingCount',
            'receivingPct',
            'editingCount',
            'editingPct',
            'entryCount',
            'entryPct',
            'cleanCount',
            'cleanPct',
            'notReceivedCount',
            'documents',
            'districtStats',
            'assignmentStats'
        ));
    }

    /**
     * Update Status Pengolahan Dokumen Tunggal.
     */
    public function updateStatus(Request $request, Respondent $respondent)
    {
        $validated = $request->validate([
            'processing_status' => ['required', 'string', 'in:belum_diterima,receiving,editing_coding,entry,clean'],
        ]);

        $status = $validated['processing_status'];
        $updateData = ['processing_status' => $status];

        $now = now();
        if ($status === 'receiving' && !$respondent->received_at) {
            $updateData['received_at'] = $now;
        } elseif ($status === 'editing_coding') {
            if (!$respondent->received_at) $updateData['received_at'] = $now;
            if (!$respondent->edited_at) $updateData['edited_at'] = $now;
        } elseif ($status === 'entry') {
            if (!$respondent->received_at) $updateData['received_at'] = $now;
            if (!$respondent->edited_at) $updateData['edited_at'] = $now;
            if (!$respondent->entered_at) $updateData['entered_at'] = $now;
        } elseif ($status === 'clean') {
            if (!$respondent->received_at) $updateData['received_at'] = $now;
            if (!$respondent->edited_at) $updateData['edited_at'] = $now;
            if (!$respondent->entered_at) $updateData['entered_at'] = $now;
            if (!$respondent->cleaned_at) $updateData['cleaned_at'] = $now;
        }

        $respondent->update($updateData);

        ActivityLog::log(
            'update',
            'respondents',
            "Memperbarui status pengolahan dokumen responden '{$respondent->name}' menjadi '{$respondent->processing_status_label}'."
        );

        return back()->with('success', "Status pengolahan dokumen '{$respondent->name}' berhasil diperbarui menjadi {$respondent->processing_status_label}!");
    }

    /**
     * Update Status Pengolahan Dokumen Massal (Bulk Action).
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'respondent_ids' => ['required', 'array', 'min:1'],
            'respondent_ids.*' => ['exists:respondents,id'],
            'target_status' => ['required', 'string', 'in:belum_diterima,receiving,editing_coding,entry,clean'],
        ]);

        $ids = $validated['respondent_ids'];
        $status = $validated['target_status'];
        $now = now();

        $updateData = ['processing_status' => $status];
        if ($status === 'receiving') {
            $updateData['received_at'] = $now;
        } elseif ($status === 'editing_coding') {
            $updateData['received_at'] = $now;
            $updateData['edited_at'] = $now;
        } elseif ($status === 'entry') {
            $updateData['received_at'] = $now;
            $updateData['edited_at'] = $now;
            $updateData['entered_at'] = $now;
        } elseif ($status === 'clean') {
            $updateData['received_at'] = $now;
            $updateData['edited_at'] = $now;
            $updateData['entered_at'] = $now;
            $updateData['cleaned_at'] = $now;
        }

        Respondent::whereIn('id', $ids)->update($updateData);

        ActivityLog::log(
            'update',
            'respondents',
            "Memperbarui status pengolahan secara massal untuk " . count($ids) . " dokumen menjadi '{$status}'."
        );

        return back()->with('success', "Berhasil memperbarui status " . count($ids) . " dokumen secara massal!");
    }

    /**
     * Unduh Rekapitulasi Progres Pengolahan Dokumen (CSV/Excel).
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $selectedAssignmentId = $request->input('assignment_id');
        $selectedDistrict = $request->input('district');
        $selectedStatus = $request->input('processing_status');

        $query = Respondent::with(['assignment', 'officer']);
        if ($selectedAssignmentId) {
            $query->where('assignment_id', $selectedAssignmentId);
        }
        if ($selectedDistrict) {
            $query->where('district', $selectedDistrict);
        }
        if ($selectedStatus) {
            $query->where('processing_status', $selectedStatus);
        }

        $records = $query->orderBy('district')->orderBy('name')->get();

        $filename = 'Rekap_Progres_Pengolahan_Dokumen_SIMPEL_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($handle, [
                'No',
                'Nama Responden / Kepala Keluarga',
                'Instansi / Kategori',
                'Alamat',
                'Kecamatan',
                'Kegiatan Survei',
                'Petugas Lapangan',
                'Status Lapangan',
                'Status Pengolahan BPS',
                'Tgl Diterima (Receiving)',
                'Tgl Editing / Coding',
                'Tgl Data Entry',
                'Tgl Clean Data',
            ]);

            $no = 1;
            foreach ($records as $r) {
                fputcsv($handle, [
                    $no++,
                    $r->name,
                    $r->agency ?? '-',
                    $r->address ?? '-',
                    $r->district ?? '-',
                    $r->assignment ? $r->assignment->title : '-',
                    $r->officer ? $r->officer->name : '-',
                    $r->progress_status_label,
                    $r->processing_status_label,
                    $r->received_at ? $r->received_at->format('d/m/Y H:i') : '-',
                    $r->edited_at ? $r->edited_at->format('d/m/Y H:i') : '-',
                    $r->entered_at ? $r->entered_at->format('d/m/Y H:i') : '-',
                    $r->cleaned_at ? $r->cleaned_at->format('d/m/Y H:i') : '-',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}

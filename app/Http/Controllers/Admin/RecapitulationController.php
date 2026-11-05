<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\OfficerBast;
use App\Models\OfficerReport;
use App\Models\Respondent;
use App\Models\RespondentBast;
use App\Models\TrainingEvent;
use App\Models\TrainingParticipant;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RecapitulationController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->extractFilters($request);

        $summary = $this->calculateSummary($filters);

        $assignments = Assignment::orderBy('title')->get();
        $officers = User::where('role', 'petugas')->where('status', 'active')->orderBy('name')->get();

        return view('admin.recapitulation.index', compact(
            'summary',
            'assignments',
            'officers',
            'filters'
        ));
    }

    public function exportPdf(Request $request)
    {
        $filters = $this->extractFilters($request);
        $summary = $this->calculateSummary($filters);

        $pdf = Pdf::loadView('pdf.recapitulation', compact('summary', 'filters'))
                  ->setPaper('a4', 'landscape');

        $filename = 'Rekapitulasi-SIMPLE-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $filters = $this->extractFilters($request);
        $type = $request->input('dataset', 'respondents'); // respondents, basts, reports, trainings

        $filename = "Export-{$type}-" . now()->format('Ymd-His') . ".csv";

        $response = new StreamedResponse(function () use ($filters, $type) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            switch ($type) {
                case 'respondents':
                    fputcsv($handle, ['No', 'Nama Responden', 'Instansi', 'Alamat', 'Nomor HP', 'Kegiatan', 'Petugas Penginput', 'Tanggal Input']);
                    $query = Respondent::with(['assignment', 'officer']);
                    $this->applyFilters($query, $filters, 'input_date');
                    foreach ($query->get() as $idx => $r) {
                        fputcsv($handle, [
                            $idx + 1,
                            $r->name,
                            $r->agency,
                            $r->address,
                            $r->phone,
                            $r->assignment ? $r->assignment->title : '-',
                            $r->officer ? $r->officer->name : '-',
                            $r->input_date ? $r->input_date->format('Y-m-d') : '-',
                        ]);
                    }
                    break;

                case 'basts':
                    fputcsv($handle, ['No', 'Nomor BAST', 'Jenis BAST', 'Pihak Terkait', 'Tanggal BAST', 'Kegiatan', 'Status']);
                    $bastsQuery = RespondentBast::with(['respondent', 'assignment']);
                    $this->applyFilters($bastsQuery, $filters, 'bast_date');
                    $counter = 1;
                    foreach ($bastsQuery->get() as $b) {
                        fputcsv($handle, [
                            $counter++,
                            $b->bast_number,
                            'BAST Responden',
                            $b->respondent ? $b->respondent->name : '-',
                            $b->bast_date ? $b->bast_date->format('Y-m-d') : '-',
                            $b->assignment ? $b->assignment->title : '-',
                            $b->status,
                        ]);
                    }
                    $officerBastsQuery = OfficerBast::with(['officer', 'assignment']);
                    $this->applyFilters($officerBastsQuery, $filters, 'bast_date');
                    foreach ($officerBastsQuery->get() as $ob) {
                        fputcsv($handle, [
                            $counter++,
                            $ob->bast_number,
                            'BAST Petugas',
                            $ob->officer ? $ob->officer->name : '-',
                            $ob->bast_date ? $ob->bast_date->format('Y-m-d') : '-',
                            $ob->assignment ? $ob->assignment->title : '-',
                            $ob->status,
                        ]);
                    }
                    break;

                case 'reports':
                    fputcsv($handle, ['No', 'Nomor Laporan', 'Petugas', 'Judul Kegiatan', 'Tanggal', 'Status', 'Catatan Admin']);
                    $reportsQuery = OfficerReport::with(['user', 'assignment']);
                    $this->applyFilters($reportsQuery, $filters, 'activity_date');
                    foreach ($reportsQuery->get() as $idx => $rep) {
                        fputcsv($handle, [
                            $idx + 1,
                            $rep->report_number,
                            $rep->user ? $rep->user->name : '-',
                            $rep->activity_title,
                            $rep->activity_date ? $rep->activity_date->format('Y-m-d') : '-',
                            $rep->status_label,
                            $rep->admin_notes ?? '-',
                        ]);
                    }
                    break;

                case 'trainings':
                    fputcsv($handle, ['No', 'Kegiatan Pelatihan', 'Nama Peserta', 'Kategori', 'Instansi', 'Presensi', 'No Sertifikat']);
                    $participantsQuery = TrainingParticipant::with('trainingEvent');
                    foreach ($participantsQuery->get() as $idx => $tp) {
                        fputcsv($handle, [
                            $idx + 1,
                            $tp->trainingEvent ? $tp->trainingEvent->title : '-',
                            $tp->name,
                            $tp->category,
                            $tp->agency ?? '-',
                            $tp->attendance_status,
                            $tp->certificate_number ?? '-',
                        ]);
                    }
                    break;
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    protected function extractFilters(Request $request): array
    {
        return [
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'assignment_id' => $request->input('assignment_id'),
            'officer_id' => $request->input('officer_id'),
        ];
    }

    protected function applyFilters($query, array $filters, string $dateColumn = 'created_at')
    {
        if (!empty($filters['date_from'])) {
            $query->whereDate($dateColumn, '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate($dateColumn, '<=', $filters['date_to']);
        }
        if (!empty($filters['assignment_id'])) {
            $query->where('assignment_id', $filters['assignment_id']);
        }
        if (!empty($filters['officer_id'])) {
            // Check if column is user_id or officer_id
            $column = in_array('officer_id', $query->getModel()->getFillable()) ? 'officer_id' : 'user_id';
            $query->where($column, $filters['officer_id']);
        }
    }

    protected function calculateSummary(array $filters): array
    {
        // 1. Responden Query
        $respQuery = Respondent::with(['assignment', 'officer']);
        $this->applyFilters($respQuery, $filters, 'input_date');
        $respondents = $respQuery->latest('input_date')->get();

        // 2. BAST Responden & Petugas
        $bastRespQuery = RespondentBast::with(['respondent', 'officer']);
        $this->applyFilters($bastRespQuery, $filters, 'bast_date');
        $respondentBasts = $bastRespQuery->latest('bast_date')->get();

        $bastOfficerQuery = OfficerBast::with(['officer', 'assignment']);
        $this->applyFilters($bastOfficerQuery, $filters, 'bast_date');
        $officerBasts = $bastOfficerQuery->latest('bast_date')->get();

        // 3. Laporan Petugas
        $repQuery = OfficerReport::with(['user', 'assignment']);
        $this->applyFilters($repQuery, $filters, 'activity_date');
        $reports = $repQuery->latest('activity_date')->get();

        // 4. Pelatihan & Peserta
        $trainings = TrainingEvent::withCount('participants')->latest('start_date')->get();
        $participantsByCategory = TrainingParticipant::selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        return [
            'respondents' => $respondents,
            'respondentBasts' => $respondentBasts,
            'officerBasts' => $officerBasts,
            'reports' => $reports,
            'trainings' => $trainings,
            'participantsByCategory' => $participantsByCategory,
            'counts' => [
                'total_respondents' => $respondents->count(),
                'total_bast_resp' => $respondentBasts->count(),
                'total_bast_officer' => $officerBasts->count(),
                'total_reports' => $reports->count(),
                'reports_approved' => $reports->where('status', 'approved')->count(),
                'reports_pending' => $reports->whereIn('status', ['submitted', 'reviewing'])->count(),
                'total_trainings' => $trainings->count(),
                'total_participants' => TrainingParticipant::count(),
            ],
        ];
    }
}


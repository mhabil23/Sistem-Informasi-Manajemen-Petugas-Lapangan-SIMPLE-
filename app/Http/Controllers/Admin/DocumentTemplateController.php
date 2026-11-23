<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DocumentTemplate;
use App\Models\OfficerBast;
use App\Models\OfficerReport;
use App\Models\RespondentBast;
use App\Models\TrainingEvent;
use App\Services\WordDocumentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DocumentTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentTemplate::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $templates = $query->latest('id')->paginate(10)->withQueryString();

        $totalTemplates = DocumentTemplate::count();
        $activeCount = DocumentTemplate::where('is_active', true)->count();

        return view('admin.templates.index', compact('templates', 'totalTemplates', 'activeCount'));
    }

    public function store(Request $request, WordDocumentService $wordService)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:document_templates,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:100'],
            'header_text' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'footer_text' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'word_file' => ['nullable', 'file', 'mimes:docx,doc', 'max:10240'],
        ], [
            'code.required' => 'Kode unik template wajib diisi.',
            'code.unique' => 'Kode template sudah digunakan.',
            'name.required' => 'Nama template wajib diisi.',
            'word_file.mimes' => 'Berkas template harus berformat Microsoft Word (.docx atau .doc).',
            'word_file.max' => 'Ukuran berkas Word maksimal 10MB.',
        ]);

        // Jika upload file word, simpan berkas dan ekstrak konten jika isi masih kosong
        if ($request->hasFile('word_file')) {
            $file = $request->file('word_file');
            $extension = $file->getClientOriginalExtension();
            $safeCode = Str::slug($validated['code'], '_');
            $filename = "template_{$safeCode}_" . time() . '.' . $extension;
            $path = $file->storeAs('templates', $filename, 'public');
            $validated['file_path'] = $path;

            // Jika konten teks kosong, coba auto-ekstrak dari berkas docx
            if (empty($validated['content']) && strtolower($extension) === 'docx') {
                $extracted = $wordService->extractFromDocx($file->getRealPath());
                if (!empty($extracted['content'])) {
                    $validated['content'] = $extracted['content'];
                    if (empty($validated['header_text']) && !empty($extracted['header'])) {
                        $validated['header_text'] = $extracted['header'];
                    }
                    if (empty($validated['footer_text']) && !empty($extracted['footer'])) {
                        $validated['footer_text'] = $extracted['footer'];
                    }
                }
            }
        }

        if (empty($validated['content'])) {
            return back()->withInput()->withErrors(['content' => 'Isi format template wajib diisi atau unggah berkas Word (.docx)']);
        }

        $validated['is_active'] = $request->has('is_active');
        unset($validated['word_file']);

        $template = DocumentTemplate::create($validated);

        ActivityLog::log(
            'create',
            'templates',
            "Membuat template dokumen baru: {$template->name} ({$template->code})."
        );

        return back()->with('success', "Template dokumen '{$template->name}' berhasil ditambahkan!");
    }

    /**
     * AJAX endpoint: Ekstrak isi teks, header, footer, dan tag dinamis dari berkas Word (.docx).
     */
    public function extractWord(Request $request, WordDocumentService $wordService)
    {
        $request->validate([
            'word_file' => ['required', 'file', 'mimes:docx,doc', 'max:10240'],
        ], [
            'word_file.required' => 'Silakan pilih berkas Word terlebih dahulu.',
            'word_file.mimes' => 'Berkas harus berupa dokumen Microsoft Word (.docx atau .doc).',
            'word_file.max' => 'Ukuran berkas maksimal 10MB.',
        ]);

        $file = $request->file('word_file');
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension !== 'docx') {
            return response()->json([
                'success' => false,
                'message' => 'Untuk ekstraksi otomatis teks dan tag dinamis, harap gunakan format Microsoft Word (.docx). Berkas .doc versi lama disarankan disimpan ulang sebagai .docx.',
            ], 422);
        }

        $result = $wordService->extractFromDocx($file->getRealPath());
        $result['filename'] = $file->getClientOriginalName();

        if (empty($result['content'])) {
            return response()->json([
                'success' => false,
                'message' => 'Berkas Word kosong atau tidak memiliki teks yang dapat diekstrak.',
            ], 422);
        }

        return response()->json($result);
    }

    /**
     * Unduh berkas asli Word yang tersimpan pada template.
     */
    public function downloadFile(DocumentTemplate $template)
    {
        if (!$template->hasFile()) {
            return back()->with('error', 'Berkas Word asli tidak tersedia atau belum diunggah untuk template ini.');
        }

        $safeName = Str::slug($template->name, '_') . '.' . pathinfo($template->file_path, PATHINFO_EXTENSION);
        return Storage::disk('public')->download($template->file_path, $safeName);
    }

    public function show(DocumentTemplate $template)
    {
        return response()->json([
            'success' => true,
            'template' => $template,
            'has_file' => $template->hasFile(),
            'file_url' => $template->hasFile() ? route('admin.templates.download-file', $template) : null,
        ]);
    }

    public function update(Request $request, DocumentTemplate $template, WordDocumentService $wordService)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('document_templates', 'code')->ignore($template->id)],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:100'],
            'header_text' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'footer_text' => ['nullable', 'string', 'max:500'],
            'word_file' => ['nullable', 'file', 'mimes:docx,doc', 'max:10240'],
        ]);

        if ($request->hasFile('word_file')) {
            $file = $request->file('word_file');
            $extension = $file->getClientOriginalExtension();
            $safeCode = Str::slug($validated['code'], '_');
            $filename = "template_{$safeCode}_" . time() . '.' . $extension;

            // Hapus file lama jika ada
            if ($template->file_path && Storage::disk('public')->exists($template->file_path)) {
                Storage::disk('public')->delete($template->file_path);
            }

            $path = $file->storeAs('templates', $filename, 'public');
            $validated['file_path'] = $path;

            if (empty($validated['content']) && strtolower($extension) === 'docx') {
                $extracted = $wordService->extractFromDocx($file->getRealPath());
                if (!empty($extracted['content'])) {
                    $validated['content'] = $extracted['content'];
                }
            }
        }

        if (empty($validated['content'])) {
            $validated['content'] = $template->content;
        }

        $validated['is_active'] = $request->has('is_active');
        unset($validated['word_file']);

        $template->update($validated);

        ActivityLog::log(
            'update',
            'templates',
            "Memperbarui template dokumen: {$template->name}."
        );

        return back()->with('success', "Template dokumen '{$template->name}' berhasil diperbarui!");
    }

    public function toggleStatus(DocumentTemplate $template)
    {
        $template->update(['is_active' => !$template->is_active]);

        $status = $template->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Template '{$template->name}' berhasil {$status}.");
    }

    public function destroy(DocumentTemplate $template)
    {
        $name = $template->name;
        $template->delete();

        ActivityLog::log(
            'delete',
            'templates',
            "Menghapus template dokumen: {$name}."
        );

        return back()->with('success', "Template '{$name}' telah dihapus.");
    }

    /**
     * Preview dokumen PDF resmi atau contoh format cetak untuk template ini.
     */
    public function previewPdf(DocumentTemplate $template)
    {
        switch ($template->code) {
            case 'bast_officer':
                $bast = OfficerBast::where('bast_type', 'petugas_ke_kantor')->with(['officer', 'assignment', 'recipients'])->latest()->first();
                if ($bast) {
                    return Pdf::loadView('pdf.bast_officer', compact('bast'))
                        ->setPaper('a4', 'portrait')
                        ->stream('Contoh-BAST-Petugas.pdf');
                }
                break;

            case 'bast_blanko':
                $bast = OfficerBast::where('bast_type', 'kantor_ke_petugas')->with(['officer', 'assignment', 'recipients'])->latest()->first();
                if ($bast) {
                    return Pdf::loadView('pdf.bast_blanko', compact('bast'))
                        ->setPaper('a4', 'portrait')
                        ->stream('Contoh-BAST-Blanko.pdf');
                }
                break;

            case 'bast_respondent':
                $bast = RespondentBast::with(['respondent', 'officer', 'assignment'])->latest()->first();
                if ($bast) {
                    return Pdf::loadView('pdf.bast_respondent', compact('bast'))
                        ->setPaper('a4', 'portrait')
                        ->stream('Contoh-BAST-Responden.pdf');
                }
                break;

            case 'officer_report':
                $report = OfficerReport::with(['officer', 'assignment'])->latest()->first();
                if ($report) {
                    return Pdf::loadView('pdf.officer_report', compact('report'))
                        ->setPaper('a4', 'portrait')
                        ->stream('Contoh-Laporan-Perjalanan-Dinas.pdf');
                }
                break;

            case 'training_attendance':
                $training = TrainingEvent::with(['creator', 'assignment', 'participants'])->latest()->first();
                if ($training) {
                    return Pdf::loadView('pdf.training_attendance', compact('training'))
                        ->setPaper('a4', 'portrait')
                        ->stream('Contoh-Daftar-Hadir.pdf');
                }
                break;
        }

        // Generic fallback preview jika belum ada data riil atau template kustom baru
        $sampleText = str_replace(
            ['{KEGIATAN}', '{NOMOR_KONTRAK}', '{TANGGAL_KONTRAK}', '{JUMLAH_DOKUMEN}', '{INSTANSI}', '{LOKASI}', '{TANGGAL}', '{NAMA}', '{NAMA_PETUGAS}', '{NOMOR_DOKUMEN}'],
            ['Pendataan Lapangan 2026', 'SPK/7408/2026/001', date('d F Y'), '30 Dokumen', 'Badan Pusat Statistik Kabupaten Kolaka Utara', 'Kecamatan Lasusua', date('d F Y'), 'Nama Lengkap Responden', 'Petugas Lapangan BPS', 'DOC/7408/2026/0001'],
            $template->content ?: ''
        );

        $html = "<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>Pratinjau {$template->name}</title>
    <style>
        @page { size: A4 portrait; margin: 2cm; }
        body { font-family: 'Times New Roman', serif; font-size: 11pt; line-height: 1.5; color: #111; }
        .header { text-align: center; font-weight: bold; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 20px; text-transform: uppercase; }
        .title { text-align: center; font-weight: bold; font-size: 13pt; margin-bottom: 15px; text-transform: uppercase; }
        .content { text-align: justify; margin-bottom: 25px; }
        .footer { font-style: italic; font-size: 10pt; color: #555; border-top: 1px dashed #ccc; padding-top: 8px; margin-top: 30px; }
    </style>
</head>
<body>
    " . (!empty($template->header_text) ? "<div class='header'>{$template->header_text}</div>" : "") . "
    <div class='title'>{$template->name}</div>
    <div class='content'>{$sampleText}</div>
    " . (!empty($template->footer_text) ? "<div class='footer'>{$template->footer_text}</div>" : "") . "
</body>
</html>";

        return Pdf::loadHTML($html)->setPaper('a4', 'portrait')->stream(Str::slug($template->name) . '.pdf');
    }
}


<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:document_templates,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:100'],
            'header_text' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'footer_text' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ], [
            'code.required' => 'Kode unik template wajib diisi.',
            'code.unique' => 'Kode template sudah digunakan.',
            'name.required' => 'Nama template wajib diisi.',
            'content.required' => 'Isi format template wajib diisi.',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $template = DocumentTemplate::create($validated);

        ActivityLog::log(
            'create',
            'templates',
            "Membuat template dokumen baru: {$template->name} ({$template->code})."
        );

        return back()->with('success', "Template dokumen '{$template->name}' berhasil ditambahkan!");
    }

    public function show(DocumentTemplate $template)
    {
        return response()->json([
            'success' => true,
            'template' => $template,
        ]);
    }

    public function update(Request $request, DocumentTemplate $template)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('document_templates', 'code')->ignore($template->id)],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:100'],
            'header_text' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'footer_text' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['is_active'] = $request->has('is_active');

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
}


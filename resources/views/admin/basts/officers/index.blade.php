@extends('layouts.app')

@section('title', 'Tanda Terima Petugas')
@section('page_title', 'Tanda Terima Petugas')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tanda Terima Petugas</li>
@endsection

@section('content')
<!-- Header & Quick Actions -->
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h3 class="fw-bold text-dark mb-1">Tanda Terima Petugas (ATK &amp; Kuesioner)</h3>
        <p class="text-muted small mb-0">Arsip resmi penyusunan tanda terima ATK, kuesioner, dan dokumen penugasan untuk verifikasi dan tanda tangan digital petugas lapangan.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        @if(auth()->user()->isAdmin())
            <button type="button" class="btn btn-sp-primary" onclick="openCreateOfficerBastModal()">
                <i class="fa-solid fa-file-circle-plus me-1"></i> Terbitkan Tanda Terima Petugas
            </button>
        @endif
    </div>
</div>

@if(isset($errors) && $errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <div class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Gagal menyimpan dokumen BAST:</div>
        <ul class="mb-0 small">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-solid fa-file-shield"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Total Tanda Terima Petugas</div>
                <div class="sp-stat-value">{{ number_format($totalBasts) }}</div>
                <div class="sp-stat-desc">Dokumen sah</div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Bulan Ini</div>
                <div class="sp-stat-value text-success">{{ number_format($thisMonthBasts) }}</div>
                <div class="sp-stat-desc">Arsip bulan ini</div>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="sp-card mb-4">
    <div class="sp-card-header">
        <h5 class="sp-card-title">
            <i class="fa-solid fa-file-shield text-primary"></i>
            Daftar Dokumen Tanda Terima Petugas ({{ $basts->total() }})
        </h5>
    </div>
    <div class="sp-card-body p-0">
        <div class="table-responsive">
            <table class="table sp-table align-middle">
                <thead>
                    <tr>
                        <th style="min-width: 220px;">Nomor &amp; Tanggal BAST</th>
                        <th style="min-width: 170px;">Alur BAST</th>
                        <th style="min-width: 180px;">Petugas Lapangan</th>
                        <th style="min-width: 200px;">Kegiatan Terkait</th>
                        <th style="min-width: 240px;">Keterangan Serah Terima</th>
                        <th class="text-end" style="min-width: 140px;">Unduh / Cetak</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($basts as $item)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark font-monospace">{{ $item->bast_number }}</div>
                                <div class="small text-muted"><i class="fa-regular fa-calendar me-1"></i> {{ $item->bast_date->translatedFormat('d F Y') }}</div>
                            </td>
                            <td>
                                @if($item->bast_type === 'kantor_ke_petugas' || empty($item->bast_type))
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle py-1 px-2" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-boxes-packing me-1 text-primary"></i> Tanda Terima Petugas
                                    </span>
                                    @php $cat = strtoupper($item->receipt_category ?: 'ATK'); @endphp
                                    <div class="mt-1">
                                        <span class="badge {{ $cat === 'KUES' ? 'bg-primary-subtle text-primary-emphasis border border-primary-subtle' : 'bg-success-subtle text-success-emphasis border border-success-subtle' }}" style="font-size: 0.68rem;">
                                            {{ $cat }}
                                        </span>
                                    </div>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle py-1 px-2" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-file-circle-check me-1 text-warning"></i> BAST (Petugas &rarr; Kantor)
                                    </span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $recCount = $item->recipients->count();
                                    $firstRec = $item->recipients->first();
                                @endphp
                                @if($recCount > 1)
                                    <div class="fw-bold text-dark">
                                        {{ $firstRec->recipient_name }}
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1">+{{ $recCount - 1 }}</span>
                                    </div>
                                    <div class="small text-muted">
                                        <i class="fa-solid fa-users me-1"></i> Kolektif ({{ $recCount }} penerima)
                                    </div>
                                    @if($item->isKantorKePetugas())
                                        @php $signedCount = $item->signedRecipientsCount(); @endphp
                                        <div class="mt-1">
                                            <span class="badge {{ $signedCount === $recCount && $recCount > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' }}" style="font-size: 0.68rem;">
                                                <i class="fa-solid fa-signature me-1"></i> {{ $signedCount }}/{{ $recCount }} TTD
                                            </span>
                                        </div>
                                    @endif
                                @elseif($recCount === 1)
                                    <div class="fw-bold text-dark">{{ $firstRec->recipient_name }}</div>
                                    <div class="small text-muted">
                                        <i class="fa-solid fa-id-badge me-1"></i> {{ $firstRec->origin_role ?: ($firstRec->position_unit ?: '-') }}
                                    </div>
                                    @if($item->isKantorKePetugas())
                                        <div class="mt-1">
                                            @if($firstRec && $firstRec->isSigned())
                                                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.68rem;">
                                                    <i class="fa-solid fa-circle-check me-1"></i> Sudah TTD
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.68rem;">
                                                    <i class="fa-solid fa-clock me-1"></i> Belum TTD
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <div class="fw-bold text-dark">{{ $item->officer ? $item->officer->name : '-' }}</div>
                                    <div class="small text-muted"><i class="fa-solid fa-phone me-1"></i> {{ $item->officer ? ($item->officer->phone ?? $item->officer->username) : '-' }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="small fw-semibold text-dark text-truncate d-block" style="max-width: 240px;">
                                    {{ $item->activity_subtitle ?: ($item->assignment ? $item->assignment->title : '-') }}
                                </span>
                                <small class="text-muted"><i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $item->document_place ?: ($item->assignment ? $item->assignment->location : '-') }}</small>
                            </td>
                            <td>
                                @if($item->isKantorKePetugas() && $item->document_title)
                                    <div class="small fw-semibold text-dark text-truncate" style="max-width: 280px;">{{ $item->document_title }}</div>
                                @endif
                                <div class="small text-secondary text-truncate" style="max-width: 280px;" title="{{ $item->description }}">
                                    {{ Str::limit(str_replace("\n", ', ', $item->description), 55) }}
                                </div>
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="sp-action-btn-group">
                                    <!-- Unduh PDF -->
                                    <a href="{{ route('admin.basts.officers.download', $item) }}" 
                                       class="btn btn-light border sp-action-btn text-success" 
                                       title="Unduh Berkas PDF" 
                                       data-bs-toggle="tooltip">
                                        <i class="fa-solid fa-file-arrow-down"></i>
                                    </a>

                                    <!-- Preview / Cetak -->
                                    <a href="{{ route('admin.basts.officers.preview', $item) }}" 
                                       target="_blank" 
                                       class="btn btn-light border sp-action-btn text-primary" 
                                       title="Lihat / Cetak Dokumen" 
                                       data-bs-toggle="tooltip">
                                        <i class="fa-solid fa-print"></i>
                                    </a>

                                    @if(auth()->user()->isAdmin())
                                        @php
                                            $editPayload = [
                                                'id' => $item->id,
                                                'update_url' => route('admin.basts.officers.update', $item),
                                                'bast_type' => $item->bast_type ?: 'kantor_ke_petugas',
                                                'receipt_category' => $item->receipt_category ?? 'ATK',
                                                'assignment_id' => $item->assignment_id,
                                                'officer_id' => $item->officer_id,
                                                'bast_number' => $item->bast_number,
                                                'bast_date_formatted' => $item->bast_date?->translatedFormat('j F Y'),
                                                'description' => $item->description,
                                                'items' => $item->parsed_items,
                                                'document_title' => $item->document_title,
                                                'activity_subtitle' => $item->activity_subtitle,
                                                'institution_name' => $item->institution_name,
                                                'document_place' => $item->document_place,
                                                'maker_name' => $item->maker_name,
                                                'maker_nip' => $item->maker_nip,
                                                'notes' => $item->notes,
                                                'status' => $item->status,
                                                'recipients' => $item->resolvedRecipients()->map(fn ($r) => [
                                                    'user_id' => $r->user_id,
                                                    'recipient_name' => $r->recipient_name,
                                                    'position_unit' => $r->position_unit ?: 'Mitra',
                                                    'origin_role' => $r->origin_role ?: '',
                                                ])->values()->all(),
                                            ];
                                        @endphp
                                        <!-- Edit -->
                                        <button type="button"
                                                class="btn btn-light border sp-action-btn text-warning"
                                                title="Edit BAST"
                                                data-bs-toggle="tooltip"
                                                onclick='openEditOfficerBastModal(@json($editPayload))'>
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <!-- Hapus -->
                                        <form method="POST" action="{{ route('admin.basts.officers.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Hapus arsip BAST Petugas {{ $item->bast_number }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-light border sp-action-btn text-danger" 
                                                    title="Hapus BAST" 
                                                    data-bs-toggle="tooltip">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-regular fa-folder-open fa-3x mb-3 text-secondary opacity-50"></i>
                                <div class="fw-semibold">Belum ada dokumen Tanda Terima Petugas diterbitkan.</div>
                                <div class="small">Klik tombol "Terbitkan Tanda Terima Petugas" untuk membuat tanda terima perlengkapan/kuesioner atau Berita Acara penyerahan berkas.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($basts->hasPages())
        <div class="sp-card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Menampilkan {{ $basts->firstItem() }} sampai {{ $basts->lastItem() }} dari {{ $basts->total() }} dokumen
            </div>
            <div>
                {{ $basts->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

@if(auth()->user()->isAdmin())
<!-- ==============================================================
     MODAL TERBITKAN / EDIT BAST PETUGAS
     ============================================================== -->
<div class="modal fade" id="officerBastFormModal" tabindex="-1" aria-labelledby="officerBastFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <form id="officerBastForm" method="POST" action="{{ route('admin.basts.officers.store') }}" class="modal-content border-0 shadow" style="max-height: 90vh; display: flex; flex-direction: column;">
            @csrf
            <input type="hidden" name="_method" id="bastFormMethod" value="POST">

            <div class="modal-header bg-light border-bottom sticky-top flex-shrink-0">
                <h5 class="modal-title fw-bold" id="officerBastFormModalLabel">
                    <i class="fa-solid fa-file-circle-plus text-primary me-2"></i> Terbitkan Tanda Terima Petugas Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4" style="flex: 1 1 auto; overflow-y: auto;">
                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">JENIS ALUR SERAH TERIMA BAST <span class="text-danger">*</span></label>
                            <select name="bast_type" id="bastTypeInput" class="form-select fw-semibold" required onchange="toggleBastTypeSections()">
                                <option value="kantor_ke_petugas">1. BAST Blanko &amp; Perlengkapan (Format Tabel Kolektif)</option>
                                <option value="petugas_ke_kantor">3. BAST Dokumen Terisi &amp; Hasil Sensus (Petugas &rarr; Kantor)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">KEGIATAN PENUGASAN <span class="text-danger">*</span></label>
                            <select name="assignment_id" id="bastAssignmentInput" class="form-select" required onchange="onAssignmentSelected()">
                                <option value="">-- Pilih Kegiatan --</option>
                                @foreach($assignments as $assignment)
                                    <option value="{{ $assignment->id }}">{{ $assignment->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">TANGGAL PEMBUATAN BAST</label>
                            <div class="form-control bg-light text-dark small d-flex align-items-center justify-content-between">
                                <span id="bastDateDisplay" class="fw-semibold">{{ now()->translatedFormat('j F Y') }}</span>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle"><i class="fa-solid fa-lock me-1"></i>Tgl Dikunci</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">NOMOR BAST (OPSIONAL)</label>
                            <input type="text" name="bast_number" id="bastNumberInput" class="form-control font-monospace" placeholder="Kosongkan untuk penomoran otomatis">
                            <small class="text-muted" style="font-size: 0.72rem;">Otomatis: <code>BAST-BLANKO/{TAHUN}/{BULAN}/{URUT}</code></small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">CATATAN PENGAWAS (OPSIONAL)</label>
                            <input type="text" name="notes" id="bastNotesInput" class="form-control" placeholder="Catatan tambahan bila diperlukan">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">STATUS ARSIP <span class="text-danger">*</span></label>
                            <select name="status" id="bastStatusInput" class="form-select" required>
                                <option value="finalized" selected>Final / Sah</option>
                                <option value="draft">Draft</option>
                                <option value="archived">Diarsipkan</option>
                            </select>
                        </div>
                    </div>

                    @include('basts.partials._blanko_fields', ['officers' => $officers])

                    <!-- BAGIAN 2: BAST DOKUMEN TERISI (PETUGAS -> KANTOR) -->
                    <div id="terisiSingleSection" class="d-none">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">PILIH PETUGAS PENANGGUNG JAWAB <span class="text-danger">*</span></label>
                            <select name="officer_id" id="bastSingleOfficerInput" class="form-select">
                                <option value="">-- Pilih Petugas --</option>
                                @foreach($officers as $officer)
                                    <option value="{{ $officer->id }}">{{ $officer->name }} ({{ $officer->phone ?? $officer->username }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-0">
                            <label class="form-label small fw-semibold text-secondary">KETERANGAN PENYERAHAN TUGAS &amp; BERKAS <span class="text-danger">*</span></label>
                            <textarea name="description" id="bastSingleDescriptionInput" class="form-control" rows="4" placeholder="Contoh: Telah menyelesaikan pendataan responden dan menyerahkan bundel kuesioner terisi lengkap."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top sticky-bottom flex-shrink-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary" id="bastFormSubmitBtn">
                        <i class="fa-solid fa-file-pdf me-1"></i> Simpan &amp; Buat Dokumen PDF
                    </button>
                </div>
            </form>
        </div>
    </div>

@include('basts.partials._blanko_scripts', [
    'assignments' => $assignments,
    'officers' => $officers,
    'defaultMakerName' => auth()->user()->name ?? '',
    'defaultMakerNip' => auth()->user()->nik ?? '',
    'defaultRecipient' => null,
])

<script>
const ADMIN_TODAY_DISPLAY = @json(now()->translatedFormat('j F Y'));

function openCreateOfficerBastModal() {
    isEditModalMode = false;
    resetBastModalCommon();

    const form = document.getElementById('officerBastForm');
    form.action = @json(route('admin.basts.officers.store'));
    document.getElementById('bastFormMethod').value = 'POST';
    document.getElementById('officerBastFormModalLabel').innerHTML = '<i class="fa-solid fa-file-circle-plus text-primary me-2"></i> Terbitkan Tanda Terima Petugas Baru';
    document.getElementById('bastDateDisplay').textContent = ADMIN_TODAY_DISPLAY;
    document.getElementById('bastNumberInput').value = '';
    document.getElementById('bastStatusInput').value = 'finalized';

    initBlankoDefaultFields('kantor_ke_petugas');
    bootstrap.Modal.getOrCreateInstance(document.getElementById('officerBastFormModal')).show();
}

function openEditOfficerBastModal(data) {
    isEditModalMode = true;
    resetBastModalCommon();

    const form = document.getElementById('officerBastForm');
    form.action = data.update_url;
    document.getElementById('bastFormMethod').value = 'PUT';
    document.getElementById('officerBastFormModalLabel').innerHTML = `<i class="fa-solid fa-pen-to-square text-warning me-2"></i> Edit BAST — ${escapeHtmlAttr(data.bast_number)}`;

    document.getElementById('bastTypeInput').value = data.bast_type || 'kantor_ke_petugas';
    document.getElementById('bastAssignmentInput').value = data.assignment_id || '';
    document.getElementById('bastDateDisplay').textContent = data.bast_date_formatted || ADMIN_TODAY_DISPLAY;
    document.getElementById('bastNumberInput').value = data.bast_number || '';
    document.getElementById('bastNotesInput').value = data.notes || '';
    document.getElementById('bastStatusInput').value = data.status || 'finalized';

    setReceiptCategory(data.receipt_category || 'ATK');
    document.getElementById('bastDocTitleInput').value = data.document_title || 'Tanda Terima Blanko & Perlengkapan';
    document.getElementById('bastActivitySubtitleInput').value = data.activity_subtitle || '';
    document.getElementById('bastInstitutionInput').value = data.institution_name || '';
    document.getElementById('bastDocPlaceInput').value = data.document_place || 'Lasusua';
    document.getElementById('bastMakerNameInput').value = data.maker_name || '';
    document.getElementById('bastMakerNipInput').value = data.maker_nip || '';

    document.getElementById('bastSingleOfficerInput').value = data.officer_id || '';
    document.getElementById('bastSingleDescriptionInput').value = data.description || '';

    (data.recipients || []).forEach(r => addRecipientRow(r));
    const items = (data.items && data.items.length > 0) ? data.items : [''];
    items.forEach(item => addBastItemRow(item));

    toggleBastTypeSections();
    bootstrap.Modal.getOrCreateInstance(document.getElementById('officerBastFormModal')).show();
}
</script>
@endif
@endsection


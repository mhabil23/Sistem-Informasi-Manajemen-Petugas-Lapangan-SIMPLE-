@extends('layouts.app')

@section('title', 'Progres Pendataan Lapangan')
@section('page_title', 'Progres Pendataan Lapangan (Tanda Terima Responden)')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Progres Pendataan Lapangan</li>
@endsection

@push('styles')
<style>
/* Bento Grid Layout & Cards */
.bento-card-clean {
    background: #FFFFFF;
    border-radius: 20px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
    transition: all 0.22s ease;
}

/* Header Action Buttons (Modern, Ergonomic, Non-Deforming) */
.btn-action-primary {
    background: linear-gradient(135deg, #F37021 0%, #E05D0E 100%);
    color: #FFFFFF !important;
    border: none;
    font-weight: 700;
    font-size: 0.8rem;
    border-radius: 12px;
    padding: 0.52rem 1.05rem;
    box-shadow: 0 3px 12px rgba(243, 112, 33, 0.28);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.45rem;
    text-decoration: none;
    white-space: nowrap;
    cursor: pointer;
}
.btn-action-primary:hover {
    background: linear-gradient(135deg, #FA7C30 0%, #E05D0E 100%);
    color: #FFFFFF !important;
    transform: translateY(-1.5px);
    box-shadow: 0 6px 16px rgba(243, 112, 33, 0.38);
}
.btn-action-primary:active {
    transform: scale(0.975);
}

/* Action Buttons */
.btn-bps-orange {
    background: #F37021;
    color: #FFFFFF !important;
    border: none;
    font-weight: 700;
    border-radius: 999px;
    padding: 0.55rem 1.25rem;
    box-shadow: 0 4px 14px rgba(243, 112, 33, 0.25);
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    text-decoration: none;
}
.btn-bps-orange:hover {
    background: #E05D0E;
    color: #FFFFFF !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(243, 112, 33, 0.35);
}

/* Fixed Bento Table Card (Compact, Anti-Geser) */
.bento-table-card {
    background: #FFFFFF;
    border-radius: 20px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.03);
    overflow: hidden;
    width: 100%;
}
.bento-table-header {
    padding: 0.9rem 1.25rem;
    border-bottom: 1px solid #E2E8F0;
    background: #FAFAFA;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
}
.bento-table-container-fixed {
    width: 100%;
    overflow: hidden;
}
.bento-table-fixed {
    table-layout: fixed;
    width: 100%;
    margin-bottom: 0;
    font-size: 0.8rem;
}
.bento-table-fixed th {
    background: #F8FAFC;
    color: #475569;
    font-weight: 700;
    font-size: 0.68rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 0.75rem 0.5rem;
    border-bottom: 1px solid #E2E8F0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.bento-table-fixed td {
    padding: 0.75rem 0.5rem;
    vertical-align: middle;
    border-bottom: 1px solid #F1F5F9;
    font-size: 0.8rem;
    overflow: hidden;
}
.bento-table-fixed tbody tr:hover {
    background-color: #F8FAFC;
}

/* Action buttons in table */
.bento-action-btn {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #E2E8F0;
    background: #FFFFFF;
    transition: all 0.15s ease;
    text-decoration: none;
    font-size: 0.78rem;
    flex-shrink: 0;
}
.bento-action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
}
.bento-action-btn.btn-download {
    color: #16A34A;
}
.bento-action-btn.btn-download:hover {
    background: #F0FDF4;
    border-color: #BBF7D0;
}
.bento-action-btn.btn-print {
    color: #2563EB;
}
.bento-action-btn.btn-print:hover {
    background: #EFF6FF;
    border-color: #BFDBFE;
}
.bento-action-btn.btn-delete {
    color: #DC2626;
}
.bento-action-btn.btn-delete:hover {
    background: #FEF2F2;
    border-color: #FECACA;
}

/* Mobile Responsive */
@media (max-width: 767.98px) {
    .bento-table-header {
        padding: 0.85rem 1rem;
        flex-direction: column;
        align-items: stretch;
        gap: 0.75rem;
    }
    .bento-header-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        gap: 0.5rem;
    }
    .bento-header-actions {
        display: flex;
        width: 100%;
    }
    .btn-action-primary {
        width: 100%;
        padding: 0.55rem 0.5rem;
        font-size: 0.78rem;
        border-radius: 10px;
    }
    .bento-table-card .bento-table-fixed thead {
        display: none;
    }
    .bento-table-card .bento-table-fixed,
    .bento-table-card .bento-table-fixed tbody,
    .bento-table-card .bento-table-fixed tr,
    .bento-table-card .bento-table-fixed td {
        display: block;
        width: 100% !important;
        text-align: left !important;
    }
    .bento-table-card .bento-table-fixed tr {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        margin: 0.75rem;
        padding: 0.85rem;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.02);
    }
    .bento-table-card .bento-table-fixed td {
        border: none;
        padding: 0.25rem 0;
    }
    .bento-table-card .bento-table-fixed td:last-child {
        border-top: 1px dashed #E2E8F0;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        display: flex;
        justify-content: flex-end;
    }
}
</style>
@endpush

@section('content')
<!-- Bento Table Card (Anti-Geser, 100% Pas Layar) -->
<div class="bento-table-card mb-4">
    <div class="bento-table-header">
        <div class="bento-header-top">
            <div>
                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.92rem;">
                    Daftar Dokumen Tanda Terima Responden
                </h6>
                <span class="text-muted" style="font-size: 0.7rem;">Total {{ $basts->total() }} dokumen sah terdaftar dalam sistem</span>
            </div>
            <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill d-md-none flex-shrink-0" style="font-size: 0.68rem;">
                Hal. {{ $basts->currentPage() }}/{{ max(1, $basts->lastPage()) }}
            </span>
        </div>
        <div class="bento-header-actions d-flex align-items-center gap-2">
            <span class="badge bg-light text-secondary border px-2.5 py-1.5 rounded-pill d-none d-md-inline-block" style="font-size: 0.7rem;">
                Halaman {{ $basts->currentPage() }} dari {{ max(1, $basts->lastPage()) }}
            </span>
            @if(auth()->user()->isAdmin())
                <button type="button" class="btn-action-primary" data-bs-toggle="modal" data-bs-target="#createBastModal">
                    <i class="bi bi-file-earmark-plus-fill fs-6"></i>
                    <span>Terbitkan<span class="d-none d-sm-inline"> Tanda Terima Baru</span></span>
                </button>
            @endif
        </div>
    </div>

    <div class="bento-table-container-fixed">
        <table class="table bento-table-fixed align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 19%;"><i class="bi bi-file-earmark-text me-1 text-primary"></i> Nomor &amp; Tanggal BAST</th>
                    <th style="width: 17%;"><i class="bi bi-person-fill me-1 text-primary"></i> Pihak KRT / Responden</th>
                    <th style="width: 17%;"><i class="bi bi-person-check-fill me-1 text-success"></i> Penerima Dokumen</th>
                    <th style="width: 15%;"><i class="bi bi-briefcase me-1 text-muted"></i> Kegiatan Terkait</th>
                    <th style="width: 13%;"><i class="bi bi-person-gear me-1 text-primary"></i> Petugas Pelaksana</th>
                    <th class="text-center" style="width: 6%;"><i class="bi bi-pen me-1 text-info"></i> TTD</th>
                    <th class="text-end pe-3" style="width: 13%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($basts as $item)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark font-monospace text-truncate" style="font-size: 0.8rem;" title="{{ $item->bast_number }}">
                                {{ $item->bast_number }}
                            </div>
                            <div class="text-muted text-truncate d-flex align-items-center gap-1 mt-0.5" style="font-size: 0.72rem;">
                                <i class="bi bi-calendar-event text-secondary flex-shrink-0"></i>
                                <span>{{ $item->bast_date->translatedFormat('d F Y') }}</span>
                            </div>
                            @php $respCat = strtoupper($item->handover_category ?: 'KUES'); @endphp
                            <div class="mt-1 d-flex flex-wrap gap-1">
                                @if(!empty($item->respondent_number))
                                    <span class="badge bg-dark-subtle text-dark border border-dark-subtle rounded-pill px-1.5 py-0.5" style="font-size: 0.65rem;">
                                        No. {{ $item->respondent_number }}
                                    </span>
                                @endif
                                <span class="badge {{ $respCat === 'ATK' ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-primary-subtle text-primary-emphasis border border-primary-subtle' }} rounded-pill px-1.5 py-0.5" style="font-size: 0.65rem;">
                                    {{ $respCat }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark text-truncate mb-0.5" style="font-size: 0.82rem;" title="{{ $item->respondent ? $item->respondent->name : '-' }}">
                                {{ $item->respondent ? $item->respondent->name : '-' }}
                            </div>
                            <div class="small text-muted text-truncate" style="font-size: 0.72rem;" title="{{ $item->respondent ? $item->respondent->agency : '-' }}">
                                {{ $item->respondent ? $item->respondent->agency : '-' }}
                            </div>
                        </td>
                        <td>
                            @if($item->recipient_type === 'representative')
                                <div class="fw-bold text-dark text-truncate mb-0.5" style="font-size: 0.82rem;" title="{{ $item->recipient_name }}">
                                    {{ $item->recipient_name }}
                                </div>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-1.5 py-0.5 text-truncate" style="font-size: 0.68rem;">
                                    <i class="bi bi-people-fill me-0.5"></i> Wakil ({{ $item->recipient_relation ?? 'Keluarga' }})
                                </span>
                            @else
                                <div class="fw-bold text-dark text-truncate mb-0.5" style="font-size: 0.82rem;" title="{{ $item->respondent ? $item->respondent->name : '-' }}">
                                    {{ $item->respondent ? $item->respondent->name : '-' }}
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-1.5 py-0.5" style="font-size: 0.68rem;">
                                    <i class="bi bi-person-check-fill me-0.5"></i> KRT Langsung
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="small fw-semibold text-dark text-truncate d-block" style="font-size: 0.76rem;" title="{{ $item->assignment ? $item->assignment->title : '-' }}">
                                {{ $item->assignment ? $item->assignment->title : '-' }}
                            </span>
                        </td>
                        <td>
                            <div class="small fw-bold text-dark text-truncate" style="font-size: 0.76rem;" title="{{ $item->officer ? $item->officer->name : '-' }}">
                                {{ $item->officer ? $item->officer->name : '-' }}
                            </div>
                            <div class="text-muted" style="font-size: 0.68rem;">Petugas Lapangan</div>
                        </td>
                        <td class="text-center">
                            @if(!empty($item->signature_path))
                                <button type="button" 
                                        class="btn btn-sm btn-light border text-success shadow-none py-0.5 px-1.5 rounded-pill" 
                                        style="font-size: 0.68rem;" 
                                        onclick="previewSignatureModal('{{ route('media.file', ['path' => ltrim($item->signature_path, '/')]) }}', '{{ $item->bast_number }}', '{{ $item->recipient_display_name }}')">
                                    <i class="bi bi-pen-fill me-0.5"></i> TTD
                                </button>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                <!-- Unduh PDF -->
                                <a href="{{ route('admin.basts.respondents.download', $item) }}" 
                                   class="bento-action-btn btn-download" 
                                   title="Unduh Berkas PDF" 
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-download"></i>
                                </a>

                                <!-- Preview / Cetak -->
                                <a href="{{ route('admin.basts.respondents.preview', $item) }}" 
                                   target="_blank" 
                                   class="bento-action-btn btn-print" 
                                   title="Lihat / Cetak Dokumen" 
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-printer"></i>
                                </a>

                                @if(auth()->user()->isAdmin())
                                    <!-- Hapus -->
                                    <form method="POST" action="{{ route('admin.basts.respondents.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Hapus arsip dokumen BAST {{ $item->bast_number }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bento-action-btn btn-delete" 
                                                title="Hapus BAST" 
                                                data-bs-toggle="tooltip">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <div class="d-inline-flex p-3 rounded-circle bg-light text-secondary mb-2">
                                <i class="bi bi-folder2-open fs-1 opacity-50"></i>
                            </div>
                            <div class="fw-semibold text-dark">Belum ada dokumen Tanda Terima Responden diterbitkan.</div>
                            <div class="small text-muted">Klik tombol "Terbitkan Tanda Terima Baru" untuk membuat dokumen resmi.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($basts->hasPages())
        <div class="p-3 bg-white border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="small text-muted" style="font-size: 0.75rem;">
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
     MODAL TERBITKAN TANDA TERIMA RESPONDEN BARU
     ============================================================== -->
<div class="modal fade" id="createBastModal" tabindex="-1" aria-labelledby="createBastModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <form method="POST" action="{{ route('admin.basts.respondents.store') }}" class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden; max-height: 90vh; display: flex; flex-direction: column;">
            @csrf
            <div class="modal-header bg-light border-bottom sticky-top flex-shrink-0 py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: var(--bps-orange, #F37021); width: 32px; height: 32px;">
                        <i class="bi bi-file-earmark-plus-fill"></i>
                    </div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="createBastModalLabel">
                        Terbitkan Tanda Terima Responden Baru
                    </h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="flex: 1 1 auto; overflow-y: auto;">
                <div class="row g-3 mb-3">
                    <div class="col-md-7">
                        <label class="form-label small fw-semibold text-secondary">PILIH RESPONDEN <span class="text-danger">*</span></label>
                        <select name="respondent_id" class="form-select" required>
                            <option value="">-- Pilih Responden --</option>
                            @foreach($respondents as $resp)
                                <option value="{{ $resp->id }}">{{ $resp->name }} — {{ $resp->agency }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold text-secondary d-flex justify-content-between align-items-center">
                            <span>TANGGAL BAST <span class="text-danger">*</span></span>
                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill" style="font-size: 0.65rem;">Tgl Dibuka</span>
                        </label>
                        <input type="date" name="bast_date" class="form-control" value="{{ now()->toDateString() }}" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-secondary">NO. URUT RESPONDEN</label>
                        <input type="number" name="respondent_number" class="form-control" min="1" placeholder="Contoh: 1, 2, ... 10">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold text-secondary">KATEGORI TANDA TERIMA <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="border rounded-3 p-2 d-flex align-items-center gap-2 cursor-pointer bg-light h-100">
                                    <input type="radio" name="handover_category" value="ATK" class="form-check-input mt-0">
                                    <span class="small fw-semibold">ATK <small class="text-muted fw-normal">(Perlengkapan)</small></span>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="border rounded-3 p-2 d-flex align-items-center gap-2 cursor-pointer bg-light h-100">
                                    <input type="radio" name="handover_category" value="KUES" class="form-check-input mt-0" checked>
                                    <span class="small fw-semibold">KUES <small class="text-muted fw-normal">(Kuesioner)</small></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold text-secondary">NOMOR BAST (OPSIONAL)</label>
                        <input type="text" name="bast_number" class="form-control font-monospace" placeholder="Kosongkan untuk penomoran otomatis sistem">
                        <small class="text-muted" style="font-size: 0.72rem;">Format otomatis: <code>BAST-RESP/{TAHUN}/{BULAN}/{NOMOR_URUT}</code></small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-secondary">STATUS BAST <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="finalized" selected>Final / Sah</option>
                            <option value="draft">Draft</option>
                            <option value="archived">Diarsipkan</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label small fw-semibold text-secondary mb-0">RINCIAN DOKUMEN / BARANG YANG DISERAHKAN <span class="text-danger">*</span></label>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0.5 px-2 rounded-pill" style="font-size: 0.68rem;" onclick="setAdminHandoverPreset('KUES')">
                                <i class="bi bi-file-earmark-text me-1 text-primary"></i> Preset Kuesioner
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0.5 px-2 rounded-pill" style="font-size: 0.68rem;" onclick="setAdminHandoverPreset('ATK')">
                                <i class="bi bi-pencil-ruler me-1 text-success"></i> Preset ATK
                            </button>
                        </div>
                    </div>
                    <textarea name="handover_items" id="adminHandoverItemsInput" class="form-control" rows="4" placeholder="Contoh:&#10;1. Kuesioner Formulir Pendataan Lapangan Terpadu (1 Rangkap)&#10;2. Salinan KTP & KK Penanggung Jawab Usaha&#10;3. Surat Keterangan Domisili Usaha" required>1. Formulir Kuesioner Pendataan Lapangan (1 Berkas)
2. Bukti Kelengkapan Berkas Administrasi Responden</textarea>
                </div>

                <div class="mb-0">
                    <label class="form-label small fw-semibold text-secondary">CATATAN TAMBAHAN (OPSIONAL)</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Catatan kondisi serah terima..."></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light border-top sticky-bottom flex-shrink-0 py-2.5 px-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5" data-bs-dismiss="modal" style="font-size: 0.82rem;">Batal</button>
                <button type="submit" class="btn-bps-orange py-1.5 px-3" style="font-size: 0.82rem;">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Terbitkan &amp; Buat Dokumen PDF
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Modal Pratinjau Tanda Tangan Digital -->
<div class="modal fade" id="sigPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom py-2.5 px-3">
                <h6 class="modal-title fw-bold text-dark mb-0" style="font-size: 0.85rem;">
                    <i class="bi bi-pen-fill text-success me-1"></i> Pratinjau Tanda Tangan
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3">
                <div class="p-2 border rounded-3 bg-light mb-2">
                    <img id="sigModalImg" src="" alt="Tanda Tangan" style="max-height: 120px; max-width: 100%;">
                </div>
                <div class="fw-bold text-dark small" id="sigModalName">-</div>
                <div class="text-muted font-monospace" style="font-size: 0.72rem;" id="sigModalBastNo">-</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewSignatureModal(url, bastNo, name) {
        document.getElementById('sigModalImg').src = url;
        document.getElementById('sigModalBastNo').innerText = bastNo;
        document.getElementById('sigModalName').innerText = name;
        const modal = new bootstrap.Modal(document.getElementById('sigPreviewModal'));
        modal.show();
    }

    function setAdminHandoverPreset(cat) {
        const textarea = document.getElementById('adminHandoverItemsInput');
        if (!textarea) return;
        if (cat === 'ATK') {
            textarea.value = "1. Map / Perlengkapan Pencacahan Responden (1 Set)\n2. Bolpoin / Alat Tulis Lapangan (1 Buah)\n3. Bukti Tanda Terima Perlengkapan Responden";
            const atkRadio = document.querySelector('input[name="handover_category"][value="ATK"]');
            if (atkRadio) atkRadio.checked = true;
        } else {
            textarea.value = "1. Formulir Kuesioner Pendataan Lapangan (1 Berkas)\n2. Bukti Kelengkapan Berkas Administrasi Responden";
            const kuesRadio = document.querySelector('input[name="handover_category"][value="KUES"]');
            if (kuesRadio) kuesRadio.checked = true;
        }
    }
</script>
@endpush

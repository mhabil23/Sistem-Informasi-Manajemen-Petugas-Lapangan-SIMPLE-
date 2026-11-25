@extends('layouts.app')

@section('title', 'Tanda Terima Petugas')
@section('page_title', 'Tanda Terima Petugas')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tanda Terima Petugas</li>
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

.btn-action-excel {
    background: #F0FDF4;
    color: #15803D !important;
    border: 1.5px solid #86EFAC;
    font-weight: 700;
    font-size: 0.8rem;
    border-radius: 12px;
    padding: 0.52rem 1.05rem;
    box-shadow: 0 2px 8px rgba(22, 163, 74, 0.08);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.45rem;
    text-decoration: none;
    white-space: nowrap;
    cursor: pointer;
}
.btn-action-excel:hover {
    background: #DCFCE7;
    border-color: #4ADE80;
    color: #14532D !important;
    transform: translateY(-1.5px);
    box-shadow: 0 4px 14px rgba(22, 163, 74, 0.16);
}
.btn-action-excel:active {
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
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
}
.bento-table-fixed {
    table-layout: fixed;
    width: 100%;
    margin-bottom: 0;
    font-size: 0.8rem;
}
@media (min-width: 768px) and (max-width: 1279.98px) {
    .bento-table-card .bento-table-fixed {
        min-width: 820px;
    }
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
.bento-action-btn.btn-edit {
    color: #D97706;
}
.bento-action-btn.btn-edit:hover {
    background: #FFFBEB;
    border-color: #FDE68A;
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
        display: grid !important;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
        width: 100%;
    }
    .btn-action-primary,
    .btn-action-excel {
        width: 100%;
        padding: 0.55rem 0.4rem;
        font-size: 0.75rem;
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
        margin: 0.6rem auto;
        width: calc(100% - 1.2rem) !important;
        padding: 0.85rem;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.02);
        box-sizing: border-box;
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
    .bento-table-card .bento-table-fixed td[colspan] {
        border-top: none;
        margin-top: 0;
        justify-content: center;
        text-align: center !important;
    }
}

/* Bulk Action Buttons - Clean, Modern & Elegant */
.btn-bulk-action {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #334155;
    font-weight: 600;
    font-size: 0.78rem;
    padding: 0.32rem 0.8rem;
    border-radius: 9999px;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    transition: all 0.15s ease;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    cursor: pointer;
}
.btn-bulk-action:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0f172a;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
}
.btn-bulk-action-danger {
    background: #ffffff;
    border: 1px solid #fecaca;
    color: #dc2626;
    font-weight: 600;
    font-size: 0.78rem;
    padding: 0.32rem 0.8rem;
    border-radius: 9999px;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    transition: all 0.15s ease;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    cursor: pointer;
}
.btn-bulk-action-danger:hover {
    background: #fef2f2;
    border-color: #f87171;
    color: #b91c1c;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(220, 38, 38, 0.15);
}
.bento-table-fixed tbody tr.row-selected td {
    background-color: #f0f7ff !important;
}
</style>
@endpush

@section('content')
@if(isset($errors) && $errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-3 rounded-4">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Gagal menyimpan dokumen BAST:</div>
        <ul class="mb-0 small">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Bento Table Card (Anti-Geser, 100% Pas Layar) -->
<div class="bento-table-card mb-4">
    <div class="bento-table-header">
        <div class="bento-header-top">
            <div>
                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.92rem;">
                    Daftar Dokumen Tanda Terima Petugas
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
                <button type="button" class="btn-action-excel" onclick="openCreateOfficerBastModalFromExcel()" title="Generate BAST sekaligus dari berkas Excel penerima">
                    <i class="bi bi-file-earmark-excel-fill text-success fs-6"></i>
                    <span>Upload Excel<span class="d-none d-sm-inline"> Penerima</span></span>
                </button>
                <button type="button" class="btn-action-primary" onclick="openCreateOfficerBastModal()" title="Terbitkan Tanda Terima Petugas Manual">
                    <i class="bi bi-file-earmark-plus-fill fs-6"></i>
                    <span>Terbitkan<span class="d-none d-sm-inline"> Tanda Terima</span><span class="d-none d-lg-inline"> Petugas</span></span>
                </button>
            @endif
        </div>
    </div>

    @if(auth()->user()->canAccessMonitoring())
        <!-- Bulk Action Toolbar (Modern, Bersih & Elegan) -->
        <div id="bulkActionBar" class="d-none px-3 py-2.5 border-bottom align-items-center justify-content-between flex-wrap gap-2" style="background: #f8fafc; border-color: #e2e8f0 !important;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="background: #005BAC; color: #ffffff; font-size: 0.78rem;">
                    <i class="bi bi-check2-circle fs-6"></i>
                    <span id="bulkSelectedCountBadge">0 Dokumen Dipilih</span>
                </span>
                <span class="text-muted small">|</span>
                <button type="button" class="btn btn-link btn-sm text-secondary text-decoration-none p-0 d-inline-flex align-items-center gap-1" onclick="clearAllSelectedBasts()" style="font-size: 0.78rem;">
                    <i class="bi bi-x-circle text-muted"></i> Batal Pilih
                </button>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="btn-bulk-action" onclick="submitBulkAction('set_finalized')" title="Tandai dokumen BAST yang dicentang sebagai Sah / Selesai">
                    <i class="bi bi-check2-circle text-success fs-6"></i> Verifikasi (Sah)
                </button>
                <button type="button" class="btn-bulk-action" onclick="submitBulkAction('set_draft')" title="Kembalikan dokumen BAST yang dicentang ke status Draft">
                    <i class="bi bi-file-earmark-text text-secondary fs-6"></i> Set Draft
                </button>
                @if(auth()->user()->isAdmin())
                    <button type="button" class="btn-bulk-action-danger" onclick="submitBulkAction('delete')" title="Hapus seluruh dokumen BAST yang dicentang">
                        <i class="bi bi-trash fs-6"></i> Hapus
                    </button>
                @endif
            </div>
        </div>
    @endif

    <div class="bento-table-container-fixed">
        <table class="table bento-table-fixed align-middle mb-0">
            <thead>
                <tr>
                    @if(auth()->user()->canAccessMonitoring())
                        <th style="width: 38px;" class="text-center">
                            <input type="checkbox" id="selectAllBastsCheckbox" class="form-check-input" onchange="toggleSelectAllBasts(this)" title="Pilih Semua Dokumen di Halaman Ini">
                        </th>
                    @endif
                    <th style="width: 17%;"><i class="bi bi-file-earmark-text me-1 text-primary"></i> Nomor &amp; Tanggal BAST</th>
                    <th style="width: 16%;"><i class="bi bi-diagram-3 me-1 text-info"></i> Alur &amp; Kategori</th>
                    <th style="width: 17%;"><i class="bi bi-people-fill me-1 text-primary"></i> Petugas Lapangan</th>
                    <th style="width: 17%;"><i class="bi bi-briefcase me-1 text-muted"></i> Kegiatan Terkait</th>
                    <th style="width: 16%;"><i class="bi bi-card-text me-1 text-muted"></i> Keterangan Serah Terima</th>
                    <th class="text-end pe-3" style="width: 14%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($basts as $item)
                    <tr>
                        @if(auth()->user()->canAccessMonitoring())
                            <td class="text-center" data-label="Pilih">
                                <input type="checkbox" 
                                       name="bast_ids[]" 
                                       value="{{ $item->id }}" 
                                       class="form-check-input bast-row-checkbox" 
                                       data-number="{{ $item->bast_number }}"
                                       onchange="onBastRowCheckboxChange()">
                            </td>
                        @endif
                        <td>
                            <div class="fw-bold text-dark font-monospace text-truncate" style="font-size: 0.8rem;" title="{{ $item->bast_number }}">
                                {{ $item->bast_number }}
                            </div>
                            <div class="text-muted text-truncate d-flex align-items-center gap-1 mt-0.5" style="font-size: 0.72rem;">
                                <i class="bi bi-calendar-event text-secondary flex-shrink-0"></i>
                                <span>{{ $item->bast_date->translatedFormat('d F Y') }}</span>
                            </div>
                        </td>
                        <td>
                            @if($item->bast_type === 'kantor_ke_petugas' || empty($item->bast_type))
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2 py-0.5 text-truncate d-inline-block" style="font-size: 0.68rem; max-width: 100%;">
                                    <i class="bi bi-box-seam me-0.5 text-primary"></i> Tanda Terima Petugas
                                </span>
                                @php $cat = strtoupper($item->receipt_category ?: 'PELATIHAN'); @endphp
                                <div class="mt-1">
                                    @if($cat === 'PELATIHAN' || $cat === 'PERLENGKAPAN')
                                        <span class="badge border rounded-pill px-1.5 py-0.5" style="font-size: 0.65rem; background: #faf5ff; color: #7e22ce; border-color: #e9d5ff !important;">
                                            <i class="bi bi-person-badge-fill me-0.5"></i> Perlengkapan Petugas
                                        </span>
                                    @elseif($cat === 'ATK')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-1.5 py-0.5" style="font-size: 0.65rem;">
                                            <i class="bi bi-pencil-ruler me-0.5"></i> ATK Pendataan
                                        </span>
                                    @elseif($cat === 'KUES')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-1.5 py-0.5" style="font-size: 0.65rem;">
                                            <i class="bi bi-file-earmark-text me-0.5"></i> Kuesioner Pendataan
                                        </span>
                                    @elseif($cat === 'ATK_RESPONDEN' || $cat === 'RESPONDEN')
                                        <span class="badge border rounded-pill px-1.5 py-0.5" style="font-size: 0.65rem; background: #fff1f2; color: #e11d48; border-color: #fecdd3 !important;">
                                            <i class="bi bi-box2-heart-fill me-0.5"></i> ATK &amp; Dok. Responden
                                        </span>
                                    @elseif($cat === 'TIMBANGAN')
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-1.5 py-0.5" style="font-size: 0.65rem;">
                                            <i class="bi bi-sliders me-0.5"></i> Timbangan
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-1.5 py-0.5" style="font-size: 0.65rem;">
                                            {{ $cat }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5 text-truncate d-inline-block" style="font-size: 0.68rem; max-width: 100%;">
                                    <i class="bi bi-file-earmark-check me-0.5 text-warning"></i> Petugas &rarr; Kantor
                                </span>
                            @endif
                        </td>
                        <td>
                            @php
                                $recCount = $item->recipients->count();
                                $firstRec = $item->recipients->first();
                            @endphp
                            @if($recCount > 1)
                                <div class="fw-bold text-dark text-truncate mb-0.5" style="font-size: 0.8rem;" title="{{ $firstRec->recipient_name }}">
                                    {{ $firstRec->recipient_name }}
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill ms-0.5" style="font-size: 0.65rem;">+{{ $recCount - 1 }}</span>
                                </div>
                                <div class="small text-muted text-truncate" style="font-size: 0.7rem;">
                                    <i class="bi bi-people me-1"></i> Kolektif ({{ $recCount }} penerima)
                                </div>
                                @if($item->isKantorKePetugas())
                                    @php $signedCount = $item->signedRecipientsCount(); @endphp
                                    <div class="mt-0.5">
                                        <span class="badge {{ $signedCount === $recCount && $recCount > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' }} rounded-pill px-1.5 py-0.5" style="font-size: 0.65rem;">
                                            <i class="bi bi-pen me-0.5"></i> {{ $signedCount }}/{{ $recCount }} TTD
                                        </span>
                                    </div>
                                @endif
                            @elseif($recCount === 1)
                                <div class="fw-bold text-dark text-truncate mb-0.5" style="font-size: 0.8rem;" title="{{ $firstRec->recipient_name }}">
                                    {{ $firstRec->recipient_name }}
                                </div>
                                <div class="small text-muted text-truncate" style="font-size: 0.7rem;">
                                    <i class="bi bi-person-badge me-0.5"></i> {{ $firstRec->origin_role ?: ($firstRec->position_unit ?: '-') }}
                                </div>
                                @if($item->isKantorKePetugas())
                                    <div class="mt-0.5">
                                        @if($firstRec && $firstRec->isSigned())
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                <i class="bi bi-check-circle me-0.5"></i> Sudah TTD
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                <i class="bi bi-clock me-0.5"></i> Belum TTD
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            @else
                                <div class="fw-bold text-dark text-truncate mb-0.5" style="font-size: 0.8rem;" title="{{ $item->officer ? $item->officer->name : '-' }}">
                                    {{ $item->officer ? $item->officer->name : '-' }}
                                </div>
                                <div class="small text-muted text-truncate" style="font-size: 0.7rem;">
                                    <i class="bi bi-telephone me-0.5"></i> {{ $item->officer ? ($item->officer->phone ?? $item->officer->username) : '-' }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="small fw-semibold text-dark text-truncate d-block mb-0.5" style="font-size: 0.76rem;" title="{{ $item->activity_subtitle ?: ($item->assignment ? $item->assignment->title : '-') }}">
                                {{ $item->activity_subtitle ?: ($item->assignment ? $item->assignment->title : '-') }}
                            </span>
                            <div class="text-muted text-truncate" style="font-size: 0.7rem;" title="{{ $item->document_place ?: ($item->assignment ? $item->assignment->location : '-') }}">
                                <i class="bi bi-geo-alt-fill text-danger me-0.5 flex-shrink-0"></i>
                                <span>{{ $item->document_place ?: ($item->assignment ? $item->assignment->location : '-') }}</span>
                            </div>
                        </td>
                        <td>
                            @if($item->isKantorKePetugas() && $item->document_title)
                                <div class="small fw-semibold text-dark text-truncate mb-0.5" style="font-size: 0.76rem;" title="{{ $item->document_title }}">{{ $item->document_title }}</div>
                            @endif
                            <div class="small text-secondary text-truncate" style="font-size: 0.72rem;" title="{{ $item->description }}">
                                {{ Str::limit(str_replace("\n", ', ', $item->description), 50) }}
                            </div>
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                <!-- Unduh PDF -->
                                <a href="{{ route('admin.basts.officers.download', $item) }}" 
                                   class="bento-action-btn btn-download" 
                                   title="Unduh Berkas PDF" 
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-download"></i>
                                </a>

                                <!-- Preview / Cetak -->
                                <a href="{{ route('admin.basts.officers.preview', $item) }}" 
                                   target="_blank" 
                                   class="bento-action-btn btn-print" 
                                   title="Lihat / Cetak Dokumen" 
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-printer"></i>
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
                                            'items' => $item->parsed_items_detailed,
                                            'document_title' => $item->document_title,
                                            'activity_subtitle' => $item->activity_subtitle,
                                            'institution_name' => $item->institution_name,
                                            'document_place' => $item->document_place,
                                            'maker_name' => $item->maker_name,
                                            'maker_nip' => $item->maker_nip,
                                            'maker_signature_path' => $item->maker_signature_path,
                                            'maker_signature_url' => $item->maker_signature_path ? route('media.file', ['path' => ltrim($item->maker_signature_path, '/')]) : null,
                                            'first_party_position' => $item->first_party_position,
                                            'first_party_address' => $item->first_party_address,
                                            'second_party_name' => $item->officer?->name,
                                            'second_party_nik' => $item->second_party_nik ?: ($item->officer?->nik ?? ''),
                                            'second_party_address' => $item->second_party_address ?: ($item->officer?->address ?? ''),
                                            'contract_number' => $item->contract_number,
                                            'contract_date' => $item->contract_date?->format('Y-m-d'),
                                            'doc_count' => $item->doc_count,
                                            'activity_work_name' => $item->activity_work_name,
                                            'notes' => $item->notes,
                                            'status' => $item->status,
                                            'recipients' => $item->resolvedRecipients()->map(fn ($r) => [
                                                'recipient_id' => $r->id ?? null,
                                                'user_id' => $r->user_id,
                                                'recipient_name' => $r->recipient_name,
                                                'position_unit' => $r->position_unit ?: 'Mitra',
                                                'origin_role' => $r->origin_role ?: '',
                                                'is_signed' => !empty($r->signature_path),
                                                'signature_path' => $r->signature_path ?? null,
                                            ])->values()->all(),
                                        ];
                                    @endphp
                                    <!-- Edit -->
                                    <button type="button"
                                            class="bento-action-btn btn-edit"
                                            title="Edit BAST"
                                            data-bs-toggle="tooltip"
                                            onclick='openEditOfficerBastModal(@json($editPayload))'>
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <!-- Hapus -->
                                    <form method="POST" action="{{ route('admin.basts.officers.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Hapus arsip BAST Petugas {{ $item->bast_number }}?');">
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
                        <td colspan="{{ auth()->user()->canAccessMonitoring() ? 7 : 6 }}" class="text-center py-5 text-muted">
                            <div class="d-inline-flex p-3 rounded-circle bg-light text-secondary mb-2">
                                <i class="bi bi-folder2-open fs-1 opacity-50"></i>
                            </div>
                            <div class="fw-semibold text-dark">Belum ada dokumen Tanda Terima Petugas diterbitkan.</div>
                            <div class="small text-muted">Klik tombol "Terbitkan Tanda Terima Petugas" untuk membuat dokumen baru.</div>
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
     MODAL TERBITKAN / EDIT BAST PETUGAS
     ============================================================== -->
<div class="modal fade" id="officerBastFormModal" tabindex="-1" aria-labelledby="officerBastFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <form id="officerBastForm" method="POST" action="{{ route('admin.basts.officers.store') }}" class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden; max-height: 90vh; display: flex; flex-direction: column;">
            @csrf
            <input type="hidden" name="_method" id="bastFormMethod" value="POST">

            <div class="modal-header bg-light border-bottom sticky-top flex-shrink-0 py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: var(--bps-orange, #F37021); width: 32px; height: 32px;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="officerBastFormModalLabel">
                        Terbitkan Tanda Terima Petugas Baru
                    </h6>
                </div>
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
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill"><i class="bi bi-lock-fill me-1"></i>Tgl Dikunci</span>
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

                @include('basts.partials._blanko_fields', ['officers' => $officers, 'pegawaiList' => $pegawaiList])

                <!-- BAGIAN 2: BAST DOKUMEN TERISI (PETUGAS -> KANTOR) -->
                @include('basts.partials._terisi_fields', ['isAdminForm' => true, 'officers' => $officers, 'pegawaiList' => $pegawaiList])
            </div>

            <div class="modal-footer bg-light border-top sticky-bottom flex-shrink-0 py-2.5 px-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5" data-bs-dismiss="modal" style="font-size: 0.82rem;">Batal</button>
                <button type="submit" class="btn-bps-orange py-1.5 px-3" id="bastFormSubmitBtn" style="font-size: 0.82rem;">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Simpan &amp; Buat Dokumen PDF
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
    document.getElementById('officerBastFormModalLabel').innerHTML = '<i class="bi bi-file-earmark-plus-fill text-primary me-2"></i> Terbitkan Tanda Terima Petugas Baru';
    document.getElementById('bastDateDisplay').textContent = ADMIN_TODAY_DISPLAY;
    document.getElementById('bastNumberInput').value = '';
    document.getElementById('bastStatusInput').value = 'finalized';

    initBlankoDefaultFields('kantor_ke_petugas');
    bootstrap.Modal.getOrCreateInstance(document.getElementById('officerBastFormModal')).show();
}

function openCreateOfficerBastModalFromExcel() {
    openCreateOfficerBastModal();
    setTimeout(() => {
        toggleBastExcelUploadPanel(true);
    }, 250);
}

function openEditOfficerBastModal(data) {
    isEditModalMode = true;
    resetBastModalCommon();

    const form = document.getElementById('officerBastForm');
    form.action = data.update_url;
    document.getElementById('bastFormMethod').value = 'PUT';
    document.getElementById('officerBastFormModalLabel').innerHTML = `<i class="bi bi-pencil-square text-warning me-2"></i> Edit BAST — ${escapeHtmlAttr(data.bast_number)}`;

    document.getElementById('bastTypeInput').value = data.bast_type || 'kantor_ke_petugas';
    document.getElementById('bastAssignmentInput').value = data.assignment_id || '';
    document.getElementById('bastDateDisplay').textContent = data.bast_date_formatted || ADMIN_TODAY_DISPLAY;
    document.getElementById('bastNumberInput').value = data.bast_number || '';
    document.getElementById('bastNotesInput').value = data.notes || '';
    document.getElementById('bastStatusInput').value = data.status || 'finalized';

    setReceiptCategory(data.receipt_category || 'PELATIHAN');
    document.getElementById('bastDocTitleInput').value = data.document_title || 'Tanda Terima Blanko & Perlengkapan';
    document.getElementById('bastActivitySubtitleInput').value = data.activity_subtitle || '';
    document.getElementById('bastInstitutionInput').value = data.institution_name || '';
    document.getElementById('bastDocPlaceInput').value = data.document_place || 'Lasusua';
    document.getElementById('bastMakerNameInput').value = data.maker_name || '';
    document.getElementById('bastMakerNipInput').value = data.maker_nip || '';

    if (data.maker_signature_url) {
        const previewImg = document.getElementById('bastMakerExistingSignImg');
        if (previewImg) previewImg.src = data.maker_signature_url;
        document.getElementById('bastMakerSignExistingPreview')?.classList.remove('d-none');
        document.getElementById('bastMakerSignCanvasWrapper')?.classList.add('d-none');
        const sigInput = document.getElementById('bastMakerSignatureData');
        if (sigInput) sigInput.value = '';
    } else {
        document.getElementById('bastMakerSignExistingPreview')?.classList.add('d-none');
        document.getElementById('bastMakerSignCanvasWrapper')?.classList.remove('d-none');
        if (makerSigPad) makerSigPad.clear();
    }

    if (document.getElementById('bastSingleOfficerInput')) document.getElementById('bastSingleOfficerInput').value = data.officer_id || '';
    if (document.getElementById('bastSingleDescriptionInput')) document.getElementById('bastSingleDescriptionInput').value = data.description || '';

    if (document.getElementById('terisiDocTitle')) document.getElementById('terisiDocTitle').value = data.document_title || 'BERITA ACARA SERAH TERIMA HASIL PEKERJAAN PETUGAS PENDATAAN LAPANGAN';
    if (document.getElementById('terisiDocPlace')) document.getElementById('terisiDocPlace').value = data.document_place || 'Badan Pusat Statistik Kabupaten Kolaka Utara';
    if (document.getElementById('terisiActivityWorkName')) document.getElementById('terisiActivityWorkName').value = data.activity_work_name || '';
    if (document.getElementById('terisiMakerName')) document.getElementById('terisiMakerName').value = data.maker_name || 'Hidayatullah, SE';
    if (document.getElementById('terisiMakerNip')) document.getElementById('terisiMakerNip').value = data.maker_nip || '198510292011011009';
    if (document.getElementById('terisiFirstPartyPosition')) document.getElementById('terisiFirstPartyPosition').value = data.first_party_position || 'Pejabat Pembuat Komitmen Badan Pusat Statistik Kabupaten Kolaka Utara';
    if (document.getElementById('terisiInstitutionName')) document.getElementById('terisiInstitutionName').value = data.institution_name || 'BPS Kabupaten Kolaka Utara';
    if (document.getElementById('terisiFirstPartyAddress')) document.getElementById('terisiFirstPartyAddress').value = data.first_party_address || 'Jalan Protokol Poros DPRD, Kec. Lasusua, Kolaka Utara Sulawesi Tenggara';
    if (document.getElementById('terisiSecondPartyName')) document.getElementById('terisiSecondPartyName').value = data.second_party_name || '';
    if (document.getElementById('terisiSecondPartyNik')) document.getElementById('terisiSecondPartyNik').value = data.second_party_nik || '';
    if (document.getElementById('terisiSecondPartyAddress')) document.getElementById('terisiSecondPartyAddress').value = data.second_party_address || '';
    if (document.getElementById('terisiContractNumber')) document.getElementById('terisiContractNumber').value = data.contract_number || '';
    if (document.getElementById('terisiContractDate')) document.getElementById('terisiContractDate').value = data.contract_date || '';
    if (document.getElementById('terisiDocCount')) document.getElementById('terisiDocCount').value = data.doc_count || '30 Dokumen';
    if (document.getElementById('terisiPegawaiSelect')) document.getElementById('terisiPegawaiSelect').value = data.maker_name || '';
    if (document.getElementById('blankoPegawaiSelect')) document.getElementById('blankoPegawaiSelect').value = data.maker_name || '';

    (data.recipients || []).forEach(r => addRecipientRow(r));
    const items = (data.items && data.items.length > 0) ? data.items : [''];
    items.forEach(item => addBastItemRow(item));

    toggleBastTypeSections();
    bootstrap.Modal.getOrCreateInstance(document.getElementById('officerBastFormModal')).show();
}
</script>
@endif

<!-- Form Tersembunyi untuk Eksekusi Aksi Massal Checkbox Tabel BAST Petugas -->
<form id="bulkActionForm" method="POST" action="{{ route('admin.basts.officers.bulk-action') }}" class="d-none">
    @csrf
    <input type="hidden" name="action" id="bulkActionInput">
    <div id="bulkActionBastIdsContainer"></div>
</form>

<script>
    function toggleSelectAllBasts(master) {
        const checkboxes = document.querySelectorAll('.bast-row-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = master.checked;
            const tr = cb.closest('tr');
            if (tr) {
                if (master.checked) tr.classList.add('row-selected');
                else tr.classList.remove('row-selected');
            }
        });
        onBastRowCheckboxChange();
    }

    function onBastRowCheckboxChange() {
        const allCheckboxes = document.querySelectorAll('.bast-row-checkbox');
        const checkedBoxes = document.querySelectorAll('.bast-row-checkbox:checked');
        const count = checkedBoxes.length;
        const bar = document.getElementById('bulkActionBar');
        const badge = document.getElementById('bulkSelectedCountBadge');
        const master = document.getElementById('selectAllBastsCheckbox');

        allCheckboxes.forEach(cb => {
            const tr = cb.closest('tr');
            if (tr) {
                if (cb.checked) tr.classList.add('row-selected');
                else tr.classList.remove('row-selected');
            }
        });

        if (badge) badge.textContent = `${count} Dokumen Dipilih`;

        if (count > 0) {
            if (bar) {
                bar.classList.remove('d-none');
                bar.classList.add('d-flex');
            }
        } else {
            if (bar) {
                bar.classList.add('d-none');
                bar.classList.remove('d-flex');
            }
            if (master) master.checked = false;
        }
    }

    function clearAllSelectedBasts() {
        document.querySelectorAll('.bast-row-checkbox').forEach(cb => {
            cb.checked = false;
            const tr = cb.closest('tr');
            if (tr) tr.classList.remove('row-selected');
        });
        const master = document.getElementById('selectAllBastsCheckbox');
        if (master) master.checked = false;
        onBastRowCheckboxChange();
    }

    function submitBulkAction(actionType) {
        const selected = Array.from(document.querySelectorAll('.bast-row-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) {
            alert('Pilih minimal satu dokumen terlebih dahulu.');
            return;
        }

        const labels = {
            'set_finalized': 'Verifikasi dan tandai sah ' + selected.length + ' dokumen BAST Petugas terpilih?',
            'set_draft': 'Ubah ' + selected.length + ' dokumen BAST Petugas terpilih kembali ke status Draft?',
            'delete': 'Hapus ' + selected.length + ' dokumen BAST Petugas terpilih? Dokumen akan diarsipkan menggunakan soft delete.'
        };

        const confirmMsg = labels[actionType] || 'Jalankan tindakan ini pada ' + selected.length + ' dokumen terpilih?';
        if (!confirm(confirmMsg)) {
            return;
        }

        const form = document.getElementById('bulkActionForm');
        const actionInput = document.getElementById('bulkActionInput');
        const container = document.getElementById('bulkActionBastIdsContainer');

        actionInput.value = actionType;
        container.innerHTML = '';
        selected.forEach(id => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'bast_ids[]';
            hidden.value = id;
            container.appendChild(hidden);
        });

        form.submit();
    }
</script>
@endsection

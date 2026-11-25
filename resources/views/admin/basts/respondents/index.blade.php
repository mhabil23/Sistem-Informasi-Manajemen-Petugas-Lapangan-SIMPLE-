@extends('layouts.app')

@section('title', 'Tanda Terima Responden - SIMPEL')
@section('page_title', 'Tanda Terima Responden (Dokumen BAST)')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Admin</a></li>
    <li class="breadcrumb-item">Administrasi</li>
    <li class="breadcrumb-item active" aria-current="page">Tanda Terima Responden</li>
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
</style>
@endpush

@section('content')
<!-- Bento Search & Filter Card -->
<div class="bento-card-clean p-3 mb-3">
    <form method="GET" action="{{ route('admin.basts.respondents.index') }}" class="row g-2 align-items-center">
        <!-- Search Input -->
        <div class="col-xl-3 col-lg-3 col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" 
                       name="search" 
                       class="form-control border-start-0 ps-1" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nomor BAST, responden, petugas...">
            </div>
        </div>

        <!-- Filter Kegiatan -->
        <div class="col-xl-3 col-lg-3 col-md-6">
            <select name="assignment_id" id="filter_bast_assignment" class="form-select" onchange="syncBastFilterOfficerDropdown()">
                <option value="">-- Pilih Kegiatan --</option>
                @foreach($assignments as $assignment)
                    <option value="{{ $assignment->id }}" {{ request('assignment_id') == $assignment->id ? 'selected' : '' }}>{{ $assignment->title }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter Tim Responden / Pengawas PML -->
        <div class="col-xl-2 col-lg-2 col-md-6">
            <select name="team_id" id="filter_bast_team" class="form-select">
                <option value="">-- Pilih Tim Responden --</option>
                <optgroup label="Kategori Peran Regu">
                    <option value="role_pcl" {{ request('team_id') === 'role_pcl' ? 'selected' : '' }}>Tim Pencacah (PCL / Mitra)</option>
                    <option value="role_pml" {{ request('team_id') === 'role_pml' ? 'selected' : '' }}>Tim Pengawas (PML / Organik)</option>
                </optgroup>
                @if(isset($teams) && count($teams) > 0)
                    <optgroup label="Tim Pengawas (PML)">
                        @foreach($teams as $t)
                            <option value="{{ $t->id }}" {{ request('team_id') == $t->id ? 'selected' : '' }}>
                                Tim {{ $t->name }} (PML{{ $t->district ? ' - ' . $t->district : '' }})
                            </option>
                        @endforeach
                    </optgroup>
                @endif
            </select>
        </div>

        <!-- Filter Petugas PCL -->
        <div class="col-xl-2 col-lg-2 col-md-6">
            <select name="officer_id" id="filter_bast_officer" class="form-select">
                <option value="">-- Pilih Petugas PCL --</option>
                @foreach($officers as $officer)
                    <option value="{{ $officer->id }}" {{ request('officer_id') == $officer->id ? 'selected' : '' }}>{{ $officer->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Tombol Filter & Reset -->
        <div class="col-xl-2 col-lg-2 col-md-12 d-flex gap-1.5">
            <button type="submit" class="btn btn-bps-orange flex-fill justify-content-center py-2" title="Terapkan Filter">
                <i class="bi bi-funnel-fill me-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'assignment_id', 'team_id', 'officer_id', 'date_from', 'date_to']))
                <a href="{{ route('admin.basts.respondents.index') }}" class="btn btn-outline-secondary rounded-pill px-2.5 d-flex align-items-center justify-content-center" title="Reset Filter">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            @endif
        </div>
    </form>
</div>

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
                <button type="button" class="btn btn-outline-success shadow-sm py-1.5 px-3 rounded-pill" style="font-size: 0.8rem;" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                    <i class="bi bi-file-earmark-excel-fill me-1"></i> Import Excel Kegiatan &amp; Sampel
                </button>
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
                <div class="p-3 mb-3 rounded-3 border bg-light-subtle">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">
                                <i class="bi bi-briefcase text-primary me-1"></i> 1. PILIH KEGIATAN <span class="text-danger">*</span>
                            </label>
                            <select name="assignment_id" id="modal_bast_assignment" class="form-select fw-semibold" required onchange="onModalAssignmentChange()">
                                <option value="">-- Pilih Kegiatan --</option>
                                @foreach($assignments as $asgn)
                                    <option value="{{ $asgn->id }}">{{ $asgn->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">
                                <i class="bi bi-person-gear text-primary me-1"></i> 2. PILIH PETUGAS PCL <span class="text-danger">*</span>
                            </label>
                            <select name="officer_id" id="modal_bast_officer" class="form-select fw-semibold" onchange="onModalOfficerChange()">
                                <option value="">-- Pilih Petugas PCL --</option>
                                @foreach($officers as $off)
                                    <option value="{{ $off->id }}">{{ $off->name }}</option>
                                @endforeach
                            </select>
                            <div id="modal_officer_hint" class="form-text small text-muted">Pilih petugas PCL untuk otomatis memunculkan seluruh responden binaannya.</div>
                        </div>
                    </div>
                </div>

                <!-- Live Container: Daftar Responden Milik PCL (Otomatis & Kolektif) -->
                <div id="pclRespondentsSection" class="mb-3 d-none">
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded-2 bg-success-subtle border border-success-subtle">
                        <div class="small text-success-emphasis fw-semibold">
                            <i class="bi bi-check2-circle me-1"></i>
                            Ditemukan <span id="respCountBadge" class="badge bg-success">0</span> responden milik <strong id="pclNameBadge">-</strong>.
                        </div>
                        <div class="form-check form-check-inline mb-0">
                            <input class="form-check-input" type="checkbox" id="checkAllRespondents" checked onchange="toggleSelectAllRespondents(this)">
                            <label class="form-check-label small fw-bold text-dark" for="checkAllRespondents">Pilih Semua Responden</label>
                        </div>
                    </div>

                    <div class="table-responsive border rounded-3" style="max-height: 220px; overflow-y: auto;">
                        <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.78rem;">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="width: 40px;" class="text-center">#</th>
                                    <th>Kepala Keluarga &amp; Responden</th>
                                    <th>Wilayah / Alamat</th>
                                    <th>Instansi</th>
                                    <th class="text-center" style="width: 120px;">Status Progres</th>
                                </tr>
                            </thead>
                            <tbody id="pclRespondentsTableBody">
                                <!-- Populated dynamically by JS -->
                            </tbody>
                        </table>
                    </div>
                    <div class="form-text small text-muted mt-1">
                        <i class="bi bi-info-circle me-1"></i> Sistem akan secara otomatis menerbitkan dokumen Tanda Terima untuk seluruh responden yang dicentang.
                    </div>
                </div>

                <!-- Fallback / Opsi Tunggal -->
                <div id="singleRespondentFallback" class="row g-3 mb-3">
                    <div class="col-md-7">
                        <label class="form-label small fw-semibold text-secondary">ATAU PILIH RESPONDEN TUNGGAL</label>
                        <select name="respondent_id" id="modal_single_respondent_select" class="form-select">
                            <option value="">-- Pilih Responden Tunggal --</option>
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
                            <div class="col-12 col-sm-6">
                                <label class="border rounded-3 p-2 d-flex align-items-center gap-2 cursor-pointer bg-light h-100">
                                    <input type="radio" name="handover_category" value="ATK" class="form-check-input mt-0">
                                    <span class="small fw-semibold">ATK <small class="text-muted fw-normal">(Perlengkapan)</small></span>
                                </label>
                            </div>
                            <div class="col-12 col-sm-6">
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
@include('admin.partials._import_excel_modal')
@endsection

@push('scripts')
<script>
    const assignmentOfficersMap = @json($assignmentOfficersMap ?? []);
    const allOfficers = @json($officers->map(fn($u) => ['id' => $u->id, 'name' => $u->name]));

    function syncBastFilterOfficerDropdown() {
        const asgnSelect = document.getElementById('filter_bast_assignment');
        const offSelect = document.getElementById('filter_bast_officer');
        if (!asgnSelect || !offSelect) return;

        const asgnId = asgnSelect.value;
        const currentVal = offSelect.value;
        offSelect.innerHTML = '<option value="">-- Pilih Petugas PCL --</option>';

        const list = asgnId && assignmentOfficersMap[asgnId] ? assignmentOfficersMap[asgnId] : allOfficers;
        list.forEach(off => {
            const opt = document.createElement('option');
            opt.value = off.id;
            opt.textContent = off.name;
            if (String(off.id) === String(currentVal)) opt.selected = true;
            offSelect.appendChild(opt);
        });
    }

    function onModalAssignmentChange() {
        const asgnSelect = document.getElementById('modal_bast_assignment');
        const offSelect = document.getElementById('modal_bast_officer');
        const hint = document.getElementById('modal_officer_hint');
        if (!asgnSelect || !offSelect) return;

        const asgnId = asgnSelect.value;
        offSelect.innerHTML = '<option value="">-- Pilih Petugas PCL --</option>';

        const list = asgnId && assignmentOfficersMap[asgnId] ? assignmentOfficersMap[asgnId] : allOfficers;
        list.forEach(off => {
            const opt = document.createElement('option');
            opt.value = off.id;
            opt.textContent = off.name;
            offSelect.appendChild(opt);
        });

        if (hint) {
            hint.textContent = asgnId && assignmentOfficersMap[asgnId] 
                ? `Menampilkan ${assignmentOfficersMap[asgnId].length} petugas yang terlibat pada kegiatan ini.`
                : 'Pilih kegiatan terlebih dahulu untuk memfilter petugas.';
        }

        // Reset respondent list
        document.getElementById('pclRespondentsSection').classList.add('d-none');
        document.getElementById('pclRespondentsTableBody').innerHTML = '';
        document.getElementById('singleRespondentFallback').classList.remove('d-none');
    }

    function onModalOfficerChange() {
        const asgnSelect = document.getElementById('modal_bast_assignment');
        const offSelect = document.getElementById('modal_bast_officer');
        if (!offSelect.value) {
            document.getElementById('pclRespondentsSection').classList.add('d-none');
            document.getElementById('singleRespondentFallback').classList.remove('d-none');
            return;
        }

        const officerId = offSelect.value;
        const assignmentId = asgnSelect ? asgnSelect.value : '';
        const officerName = offSelect.options[offSelect.selectedIndex]?.text || '';

        const tbody = document.getElementById('pclRespondentsTableBody');
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Memuat daftar seluruh responden milik PCL...</td></tr>';
        document.getElementById('pclRespondentsSection').classList.remove('d-none');
        document.getElementById('pclNameBadge').textContent = officerName;

        fetch(`{{ route('admin.basts.respondents.by-officer') }}?officer_id=${officerId}&assignment_id=${assignmentId}`)
            .then(res => res.json())
            .then(data => {
                tbody.innerHTML = '';
                if (!data.success || !data.respondents || data.respondents.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-muted"><i class="bi bi-info-circle me-1"></i> Tidak ditemukan responden yang dialokasikan untuk PCL ini. Silakan gunakan opsi responden tunggal di bawah.</td></tr>';
                    document.getElementById('respCountBadge').textContent = '0';
                    document.getElementById('singleRespondentFallback').classList.remove('d-none');
                    return;
                }

                document.getElementById('respCountBadge').textContent = data.count;
                document.getElementById('singleRespondentFallback').classList.add('d-none');
                const singleSelect = document.getElementById('modal_single_respondent_select');
                if (singleSelect) singleSelect.required = false;

                data.respondents.forEach((resp, index) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="text-center">
                            <input type="checkbox" name="respondent_ids[]" value="${resp.id}" class="form-check-input resp-item-checkbox" checked onchange="updateSelectedRespCount()">
                        </td>
                        <td>
                            <div class="fw-bold text-dark">${resp.family_head_name}</div>
                            <small class="text-muted"><i class="bi bi-person me-0.5"></i> ${resp.informant_name} (${resp.respondent_status})</small>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 180px;" title="${resp.address}"><i class="bi bi-geo-alt-fill text-danger me-0.5"></i> ${resp.address}</div>
                            <span class="badge bg-light text-secondary border mt-0.5">${resp.district}</span>
                        </td>
                        <td>
                            <span class="badge bg-light text-primary border">${resp.agency}</span>
                        </td>
                        <td class="text-center">
                            ${resp.has_bast 
                                ? `<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill"><i class="bi bi-check-circle-fill me-0.5"></i> ${resp.bast_count} BAST</span>` 
                                : `<span class="badge bg-secondary-subtle text-secondary border rounded-pill">${resp.progress_status_label || 'Belum Didata'}</span>`}
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(err => {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-3">Gagal memuat responden: ' + err.message + '</td></tr>';
            });
    }

    function toggleSelectAllRespondents(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.resp-item-checkbox');
        checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
        updateSelectedRespCount();
    }

    function updateSelectedRespCount() {
        const total = document.querySelectorAll('.resp-item-checkbox:checked').length;
        document.getElementById('respCountBadge').textContent = total;
    }

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

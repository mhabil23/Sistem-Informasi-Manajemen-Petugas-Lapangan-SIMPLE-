@extends('layouts.app')

@section('title', 'Data Responden')
@section('page_title', 'Manajemen Data Responden')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Data Responden</li>
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

/* Fixed Bento Table Card (Compact / Zoom-out & Anti-Geser) */
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
.bento-action-btn.btn-view {
    color: #2563EB;
}
.bento-action-btn.btn-view:hover {
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

/* Mobile Responsive: Stack without scroll */
@media (max-width: 767.98px) {
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
<!-- Bento Search & Filter Card -->
<div class="bento-card-clean p-3 mb-3">
    <form method="GET" action="{{ route('admin.respondents.index') }}" class="row g-2 align-items-center">
        <!-- Search Input -->
        <div class="col-lg-3 col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" 
                       name="search" 
                       class="form-control border-start-0 ps-1" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nama, KRT, alamat, HP...">
            </div>
        </div>

        <!-- Filter Petugas / PPL -->
        <div class="col-lg-2 col-md-6">
            <select name="officer_id" class="form-select">
                <option value="">-- Semua Petugas --</option>
                @foreach($officers as $officer)
                    <option value="{{ $officer->id }}" {{ request('officer_id') == $officer->id ? 'selected' : '' }}>{{ $officer->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter Kegiatan -->
        <div class="col-lg-2 col-md-4">
            <select name="assignment_id" class="form-select">
                <option value="">-- Semua Kegiatan --</option>
                @foreach($assignments as $assignment)
                    <option value="{{ $assignment->id }}" {{ request('assignment_id') == $assignment->id ? 'selected' : '' }}>{{ $assignment->title }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter Wilayah / Kecamatan -->
        <div class="col-lg-2 col-md-4">
            <select name="district" class="form-select">
                <option value="">-- Semua Kecamatan --</option>
                @foreach($districts as $d)
                    <option value="{{ $d }}" {{ request('district') == $d ? 'selected' : '' }}>{{ $d }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter Status BAST -->
        <div class="col-lg-2 col-md-4">
            <select name="bast_status" class="form-select">
                <option value="">-- Status BAST --</option>
                <option value="with_bast" {{ request('bast_status') === 'with_bast' ? 'selected' : '' }}>Sudah Ada BAST</option>
                <option value="without_bast" {{ request('bast_status') === 'without_bast' ? 'selected' : '' }}>Belum Ada BAST</option>
            </select>
        </div>

        <!-- Tombol Filter & Reset -->
        <div class="col-lg-1 col-md-12 d-flex gap-1.5">
            <button type="submit" class="btn btn-bps-orange flex-fill justify-content-center py-2" title="Terapkan Filter">
                <i class="bi bi-funnel-fill"></i>
            </button>
            @if(request()->hasAny(['search', 'assignment_id', 'officer_id', 'district', 'bast_status', 'date_from', 'date_to']))
                <a href="{{ route('admin.respondents.index') }}" class="btn btn-outline-secondary rounded-pill px-2.5 d-flex align-items-center justify-content-center" title="Reset Filter">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Bento Table Card (Compact, Zoom-out, Anti-Geser) -->
<div class="bento-table-card mb-4">
    <div class="bento-table-header">
        <div>
            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.92rem;">
                Daftar Responden Lapangan
            </h6>
            <span class="text-muted" style="font-size: 0.7rem;">Total {{ $respondents->total() }} responden terdaftar dalam sistem</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-secondary border px-2.5 py-1.5 rounded-pill" style="font-size: 0.7rem;">
                Halaman {{ $respondents->currentPage() }} dari {{ max(1, $respondents->lastPage()) }}
            </span>
            @if(auth()->user()->isAdmin())
                <button type="button" class="btn btn-outline-success shadow-sm py-1.5 px-3 rounded-pill" style="font-size: 0.8rem;" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                    <i class="bi bi-file-earmark-excel-fill me-1"></i> Import Excel Kegiatan &amp; Sampel
                </button>
                <button type="button" class="btn-bps-orange shadow-sm py-1.5 px-3" style="font-size: 0.8rem;" data-bs-toggle="modal" data-bs-target="#createRespondentModal">
                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Responden Baru
                </button>
            @endif
        </div>
    </div>

    <div class="bento-table-container-fixed">
        <table class="table bento-table-fixed align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 16%;"><i class="bi bi-person-badge-fill me-1 text-primary"></i> Petugas / PPL</th>
                    <th style="width: 21%;"><i class="bi bi-person-fill me-1 text-primary"></i> Kepala Keluarga &amp; Responden</th>
                    <th style="width: 12%;"><i class="bi bi-building me-1 text-muted"></i> Instansi</th>
                    <th style="width: 17%;"><i class="bi bi-geo-alt-fill me-1 text-danger"></i> Alamat &amp; Wilayah</th>
                    <th style="width: 14%;"><i class="bi bi-briefcase me-1 text-muted"></i> Kegiatan Terkait</th>
                    <th class="text-center" style="width: 8%;"><i class="bi bi-patch-check me-1 text-success"></i> BAST</th>
                    <th class="text-end pe-2" style="width: 12%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($respondents as $resp)
                    <tr>
                        <!-- 1. Petugas Lapangan / PPL -->
                        <td>
                            <div class="d-flex align-items-center gap-1.5">
                                <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.72rem;">
                                    {{ strtoupper(substr($resp->officer ? $resp->officer->name : 'P', 0, 1)) }}
                                </div>
                                <div class="overflow-hidden">
                                    <div class="small fw-bold text-dark text-truncate" style="font-size: 0.78rem;" title="{{ $resp->officer ? $resp->officer->name : '-' }}">
                                        {{ $resp->officer ? $resp->officer->name : '-' }}
                                    </div>
                                    <div class="text-muted text-truncate" style="font-size: 0.68rem;">
                                        <i class="bi bi-calendar-event me-0.5"></i> {{ $resp->input_date ? $resp->input_date->format('d/m/Y') : '-' }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- 2. Kepala Keluarga & Responden -->
                        <td>
                            <div class="fw-bold text-dark text-truncate mb-0.5" style="font-size: 0.82rem;" title="{{ $resp->family_head_name ?? $resp->name }}">
                                {{ $resp->family_head_name ?? $resp->name }}
                            </div>
                            <div class="d-flex align-items-center gap-1 text-truncate mb-0.5">
                                <span class="badge bg-light text-dark border text-truncate" style="font-size: 0.68rem; max-width: 120px;" title="{{ $resp->informant_name ?? $resp->name }}">
                                    <i class="bi bi-person me-0.5 text-primary"></i> {{ $resp->informant_name ?? $resp->name }}
                                </span>
                                <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.65rem;">
                                    {{ $resp->respondent_status ?? 'KRT' }}
                                </span>
                            </div>
                            <div class="text-muted text-truncate d-flex align-items-center gap-1" style="font-size: 0.7rem;">
                                <i class="bi bi-telephone text-secondary flex-shrink-0"></i>
                                <span class="text-truncate">{{ $resp->phone ?? '-' }}</span>
                            </div>
                        </td>

                        <!-- 3. Instansi -->
                        <td>
                            <span class="badge bg-light text-primary border text-truncate d-inline-block text-start w-100" style="font-size: 0.72rem;" title="{{ $resp->agency }}">
                                {{ $resp->agency }}
                            </span>
                        </td>

                        <!-- 4. Alamat & Wilayah -->
                        <td>
                            <div class="small text-secondary text-truncate d-flex align-items-center gap-1" style="font-size: 0.76rem;" title="{{ $resp->address }}">
                                <i class="bi bi-geo-alt-fill text-danger flex-shrink-0"></i>
                                <span class="text-truncate">{{ $resp->address }}</span>
                            </div>
                            @if($resp->district)
                                <div class="mt-0.5">
                                    <span class="badge bg-light text-secondary border text-truncate" style="font-size: 0.65rem; max-width: 100%;">
                                        <i class="bi bi-pin-map-fill text-primary me-0.5"></i> {{ $resp->district }}
                                    </span>
                                </div>
                            @endif
                        </td>

                        <!-- 5. Kegiatan Terkait -->
                        <td>
                            <span class="small fw-semibold text-dark text-truncate d-block" style="font-size: 0.76rem;" title="{{ $resp->assignment ? $resp->assignment->title : '-' }}">
                                {{ $resp->assignment ? $resp->assignment->title : '-' }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($resp->basts->isNotEmpty())
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-1.5 py-0.5" style="font-size: 0.68rem;" title="Sudah terbit BAST">
                                    <i class="bi bi-check-circle-fill me-0.5"></i> {{ $resp->basts->count() }} BAST
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-1.5 py-0.5" style="font-size: 0.68rem;">
                                    Belum Ada
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-2">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                <!-- Detail Modal Trigger -->
                                <button type="button" 
                                        class="bento-action-btn btn-view" 
                                        title="Lihat Detail Responden" 
                                        data-bs-toggle="tooltip"
                                        onclick="openDetailRespondentModal({{ json_encode($resp) }})">
                                    <i class="bi bi-eye"></i>
                                </button>

                                @if(auth()->user()->isAdmin())
                                    <!-- Edit -->
                                    <button type="button" 
                                            class="bento-action-btn btn-edit" 
                                            title="Edit Data" 
                                            data-bs-toggle="tooltip"
                                            onclick="openEditRespondentModal({{ json_encode($resp) }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <!-- Hapus -->
                                    <form method="POST" action="{{ route('admin.respondents.destroy', $resp) }}" class="d-inline" onsubmit="return confirm('Hapus data responden {{ addslashes($resp->name) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bento-action-btn btn-delete" 
                                                title="Hapus Responden" 
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
                                <i class="bi bi-people fs-1 opacity-50"></i>
                            </div>
                            <div class="fw-semibold text-dark">Belum ada data responden ditemukan.</div>
                            <div class="small text-muted">Klik tombol "Tambah Responden Baru" untuk menginput data responden.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($respondents->hasPages())
        <div class="p-3 bg-white border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="small text-muted" style="font-size: 0.75rem;">
                Menampilkan {{ $respondents->firstItem() }} sampai {{ $respondents->lastItem() }} dari {{ $respondents->total() }} responden
            </div>
            <div>
                {{ $respondents->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

@if(auth()->user()->isAdmin())
<!-- ==============================================================
     MODAL IMPORT KEGIATAN & SAMPEL DARI EXCEL / CSV
     ============================================================== -->
<div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: #198754; width: 32px; height: 32px;">
                        <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="importExcelModalLabel" style="font-size: 1rem;">
                            Import Kegiatan, Penugasan &amp; Sampel Responden dari Excel
                        </h5>
                        <div class="text-muted" style="font-size: 0.72rem;">Unggah berkas spreadsheet (.xlsx / .csv) sesuai format database BPS</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.assignments.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <!-- Template Download Card -->
                    <div class="p-3 mb-4 rounded-3 border bg-light-subtle d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-2.5">
                            <i class="bi bi-file-earmark-excel fs-3 text-success"></i>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 0.85rem;">Gunakan Format Template Resmi (Kolom Kosong Siap Diisi)</div>
                                <div class="text-muted small" style="font-size: 0.75rem;">Template memuat kolom: ID SOBAT, Nama Petugas, Nama Kegiatan, DIPA, Penugasan, Wilayah, &amp; Nama Sampel KRT.</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-1.5 shrink-0">
                            <a href="{{ route('admin.assignments.template.download', ['format' => 'xlsx']) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 d-flex align-items-center gap-1.5" style="font-size: 0.78rem;" title="Unduh Template Excel Kosong (.xlsx)">
                                <i class="bi bi-file-earmark-excel-fill"></i>
                                <span>Unduh Template (.xlsx)</span>
                            </a>
                            <a href="{{ route('admin.assignments.template.download', ['format' => 'csv']) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1.5 d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;" title="Unduh Template CSV Kosong (.csv)">
                                <i class="bi bi-filetype-csv"></i>
                                <span>(.csv)</span>
                            </a>
                        </div>
                    </div>

                    <!-- File Upload Input -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">
                            PILIH BERKAS EXCEL / CSV <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text" style="font-size: 0.72rem;">Mendukung format <code>.xlsx</code> (Excel modern seperti Google Sheets SPKRT) dan <code>.csv</code>. Maksimal 10 MB.</div>
                    </div>

                    <!-- Jadwal Pelaksanaan (Opsional) -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">TANGGAL MULAI KEGIATAN</label>
                            <input type="date" name="start_date" class="form-control" value="{{ now()->toDateString() }}">
                            <div class="form-text" style="font-size: 0.72rem;">Default: Hari ini (dapat diubah)</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">TANGGAL SELESAI KEGIATAN</label>
                            <input type="date" name="end_date" class="form-control" value="{{ now()->addDays(14)->toDateString() }}">
                            <div class="form-text" style="font-size: 0.72rem;">Default: 14 hari ke depan</div>
                        </div>
                    </div>

                    <!-- Informasi Cara Kerja Otomatis -->
                    <div class="alert alert-info border-0 rounded-3 p-3 mb-0" style="font-size: 0.75rem; background-color: #f0fdf4; border-left: 4px solid #16a34a !important;">
                        <div class="fw-bold text-success mb-1 d-flex align-items-center gap-1.5">
                            <i class="bi bi-lightning-charge-fill text-warning"></i>
                            Otomatisasi Sistem Sekali Klik:
                        </div>
                        <ul class="mb-0 ps-3 text-secondary" style="line-height: 1.5;">
                            <li><strong>Nama Kegiatan &amp; DIPA</strong> otomatis dibuatkan atau ditautkan ke kegiatan yang relevan.</li>
                            <li><strong>Petugas Lapangan</strong> otomatis ditugaskan; jika akun petugas belum terdaftar di sistem, akun baru akan dibuatkan secara instan (password default: <code>bps7408</code>).</li>
                            <li><strong>Sampel Kepala Keluarga (KRT)</strong> langsung dialokasikan ke masing-masing petugas sesuai baris wilayah tugasnya.</li>
                            <li>Petugas langsung dapat membuka aplikasi SIMPEL di HP untuk melakukan pendataan dan tanda tangan digital BAST responden.</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-3.5" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold" style="font-size: 0.85rem;">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Mulai Proses Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL TAMBAH RESPONDEN
     ============================================================== -->
<div class="modal fade" id="createRespondentModal" tabindex="-1" aria-labelledby="createRespondentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: var(--bps-orange, #F37021); width: 32px; height: 32px;">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="createRespondentModalLabel">
                        Tambah Data Responden Baru
                    </h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.respondents.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <!-- Baris 1: Nama KRT & No HP -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="bi bi-house-door-fill text-primary me-1"></i> NAMA KEPALA KELUARGA (KRT) <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="admin_create_resp_name" name="name" class="form-control" placeholder="Contoh: Jamaluddin Ali" required>
                            <div class="form-text small">Nama kepala keluarga sesuai daftar sampel sensus/survei.</div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="bi bi-telephone text-muted me-1"></i> NOMOR HP / WHATSAPP
                            </label>
                            <input type="text" name="phone" class="form-control" placeholder="Contoh: 081234567890">
                        </div>
                    </div>

                    <!-- Baris 2: Nama Pemberi Informasi & Status Hubungan -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-semibold text-secondary mb-0">
                                    <i class="bi bi-person-check-fill text-success me-1"></i> NAMA PEMBERI INFORMASI (OPSIONAL)
                                </label>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 rounded-pill" style="font-size: 0.72rem;" onclick="copyHeadNameToInformant('admin_create_resp_name', 'admin_create_informant_name', 'admin_create_status_select')">
                                    <i class="bi bi-copy me-1"></i> Sama dg KRT
                                </button>
                            </div>
                            <input type="text" id="admin_create_informant_name" name="informant_name" class="form-control" placeholder="Otomatis nama KRT jika belum diketahui">
                            <div class="form-text small">Otomatis sama dengan KRT. Penerima aktual dapat ditentukan saat kunjungan lapangan.</div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="bi bi-people-fill text-primary me-1"></i> STATUS RESPONDEN / HUBUNGAN
                            </label>
                            <select id="admin_create_status_select" name="respondent_status" class="form-select">
                                <option value="Kepala Keluarga (KRT)">Kepala Keluarga (KRT)</option>
                                <option value="Istri / Suami (Pasangan)">Istri / Suami (Pasangan)</option>
                                <option value="Anak Kandung">Anak Kandung</option>
                                <option value="Famili Lain / ART">Famili Lain / ART</option>
                                <option value="Pengelola / Perwakilan Usaha">Pengelola / Perwakilan Usaha</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <!-- Baris 3: Instansi & Kegiatan Terkait -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-semibold text-secondary mb-0">
                                    <i class="bi bi-building text-muted me-1"></i> INSTANSI / ASAL LEMBAGA
                                </label>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1.5 rounded-pill" style="font-size: 0.68rem;" onclick="setAgencyValue('admin_create_agency', 'Rumah Tangga')">Ruta</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1.5 rounded-pill" style="font-size: 0.68rem;" onclick="setAgencyValue('admin_create_agency', 'Pelaku Usaha')">Usaha</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1.5 rounded-pill" style="font-size: 0.68rem;" onclick="setAgencyValue('admin_create_agency', 'Instansi')">Instansi</button>
                                </div>
                            </div>
                            <input type="text" id="admin_create_agency" name="agency" class="form-control" value="Rumah Tangga" placeholder="Contoh: Rumah Tangga / Pelaku Usaha Agribisnis">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="bi bi-briefcase text-muted me-1"></i> KEGIATAN TERKAIT
                            </label>
                            <select id="admin_create_assignment" name="assignment_id" class="form-select" onchange="autoFillPurpose(this, 'admin_create_purpose')">
                                <option value="">-- Pilih Kegiatan --</option>
                                @foreach($assignments as $assignment)
                                    <option value="{{ $assignment->id }}" data-title="{{ $assignment->title }}">{{ $assignment->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Baris 4: Alamat & Wilayah -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> ALAMAT LENGKAP <span class="text-danger">*</span>
                            </label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Nama jalan, RT/RW, Dusun/Desa..." required></textarea>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="bi bi-pin-map-fill text-primary me-1"></i> KECAMATAN / WILAYAH
                            </label>
                            <select name="district" id="admin_create_district" class="form-select">
                                <option value="">-- Pilih Kecamatan (Kolaka Utara) --</option>
                                @foreach(\App\Models\Respondent::DISTRICTS as $dist)
                                    <option value="{{ $dist }}">{{ $dist }}</option>
                                @endforeach
                            </select>
                            <div class="form-text small">Masuk ke kolom Domisili di cetak Tanda Terima.</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">TUJUAN KEGIATAN</label>
                        <input type="text" id="admin_create_purpose" name="purpose" class="form-control" placeholder="Contoh: Pendataan Sarana Prasarana Pertanian Terpadu 2026">
                        <div class="form-text small">Otomatis terisi saat Kegiatan Terkait dipilih (dapat diedit bila perlu).</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="bi bi-person-gear text-primary me-1"></i> PETUGAS LAPANGAN / PPL (ALOKASI SAMPEL) <span class="text-danger">*</span>
                            </label>
                            <select name="officer_id" class="form-select" required>
                                <option value="">-- Pilih Petugas Lapangan / PPL --</option>
                                @foreach($officers as $officer)
                                    <option value="{{ $officer->id }}" {{ $officer->id === auth()->id() ? 'selected' : '' }}>{{ $officer->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text small">Petugas lapangan yang ditugaskan mencacah responden ini.</div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">TANGGAL INPUT <span class="text-danger">*</span></label>
                            <input type="date" name="input_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5" data-bs-dismiss="modal" style="font-size: 0.82rem;">Batal</button>
                    <button type="submit" class="btn-bps-orange py-1.5 px-3" style="font-size: 0.82rem;">
                        <i class="bi bi-check-lg me-1"></i> Simpan Responden
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL EDIT RESPONDEN
     ============================================================== -->
<div class="modal fade" id="editRespondentModal" tabindex="-1" aria-labelledby="editRespondentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center bg-warning" style="width: 32px; height: 32px;">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="editRespondentModalLabel">
                        Edit Data Responden
                    </h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRespondentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <!-- Baris 1: Nama KRT & No HP -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="bi bi-house-door-fill text-primary me-1"></i> NAMA KEPALA KELUARGA (KRT) <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="edit_resp_name" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="bi bi-telephone text-muted me-1"></i> NOMOR HP / WHATSAPP
                            </label>
                            <input type="text" id="edit_resp_phone" name="phone" class="form-control">
                        </div>
                    </div>

                    <!-- Baris 2: Nama Pemberi Informasi & Status Hubungan -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-semibold text-secondary mb-0">
                                    <i class="bi bi-person-check-fill text-success me-1"></i> NAMA PEMBERI INFORMASI (OPSIONAL)
                                </label>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 rounded-pill" style="font-size: 0.72rem;" onclick="copyHeadNameToInformant('edit_resp_name', 'edit_resp_informant_name', 'edit_resp_status')">
                                    <i class="bi bi-copy me-1"></i> Sama dg KRT
                                </button>
                            </div>
                            <input type="text" id="edit_resp_informant_name" name="informant_name" class="form-control">
                            <div class="form-text small">Otomatis sama dengan KRT jika dikosongkan.</div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="bi bi-people-fill text-primary me-1"></i> STATUS RESPONDEN / HUBUNGAN
                            </label>
                            <select id="edit_resp_status" name="respondent_status" class="form-select">
                                <option value="Kepala Keluarga (KRT)">Kepala Keluarga (KRT)</option>
                                <option value="Istri / Suami (Pasangan)">Istri / Suami (Pasangan)</option>
                                <option value="Anak Kandung">Anak Kandung</option>
                                <option value="Famili Lain / ART">Famili Lain / ART</option>
                                <option value="Pengelola / Perwakilan Usaha">Pengelola / Perwakilan Usaha</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <!-- Baris 3: Instansi & Kegiatan Terkait -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-semibold text-secondary mb-0">
                                    <i class="bi bi-building text-muted me-1"></i> INSTANSI / ASAL LEMBAGA
                                </label>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1.5 rounded-pill" style="font-size: 0.68rem;" onclick="setAgencyValue('edit_resp_agency', 'Rumah Tangga')">Ruta</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1.5 rounded-pill" style="font-size: 0.68rem;" onclick="setAgencyValue('edit_resp_agency', 'Pelaku Usaha')">Usaha</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1.5 rounded-pill" style="font-size: 0.68rem;" onclick="setAgencyValue('edit_resp_agency', 'Instansi')">Instansi</button>
                                </div>
                            </div>
                            <input type="text" id="edit_resp_agency" name="agency" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="bi bi-briefcase text-muted me-1"></i> KEGIATAN TERKAIT
                            </label>
                            <select id="edit_resp_assignment" name="assignment_id" class="form-select" onchange="autoFillPurpose(this, 'edit_resp_purpose')">
                                <option value="">-- Tidak Terikat Kegiatan Tertentu --</option>
                                @foreach($assignments as $assignment)
                                    <option value="{{ $assignment->id }}" data-title="{{ $assignment->title }}">{{ $assignment->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Baris 4: Alamat Lengkap & Wilayah -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> ALAMAT LENGKAP <span class="text-danger">*</span>
                            </label>
                            <textarea id="edit_resp_address" name="address" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="bi bi-pin-map-fill text-primary me-1"></i> KECAMATAN / WILAYAH
                            </label>
                            <select name="district" id="edit_resp_district" class="form-select">
                                <option value="">-- Pilih Kecamatan (Kolaka Utara) --</option>
                                @foreach(\App\Models\Respondent::DISTRICTS as $dist)
                                    <option value="{{ $dist }}">{{ $dist }}</option>
                                @endforeach
                            </select>
                            <div class="form-text small">Masuk ke kolom Domisili di cetak Tanda Terima.</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">TUJUAN KEGIATAN</label>
                        <input type="text" id="edit_resp_purpose" name="purpose" class="form-control">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="bi bi-person-gear text-primary me-1"></i> PETUGAS LAPANGAN / PPL (ALOKASI SAMPEL) <span class="text-danger">*</span>
                            </label>
                            <select id="edit_resp_officer" name="officer_id" class="form-select" required>
                                <option value="">-- Pilih Petugas Lapangan / PPL --</option>
                                @foreach($officers as $officer)
                                    <option value="{{ $officer->id }}">{{ $officer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">TANGGAL INPUT <span class="text-danger">*</span></label>
                            <input type="date" id="edit_resp_date" name="input_date" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5" data-bs-dismiss="modal" style="font-size: 0.82rem;">Batal</button>
                    <button type="submit" class="btn-bps-orange py-1.5 px-3" style="font-size: 0.82rem;">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- ==============================================================
     MODAL DETAIL RESPONDEN
     ============================================================== -->
<div class="modal fade" id="detailRespondentModal" tabindex="-1" aria-labelledby="detailRespondentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center bg-primary" style="width: 32px; height: 32px;">
                        <i class="bi bi-person-vcard-fill"></i>
                    </div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="detailRespondentModalLabel">
                        Rincian Profil Responden
                    </h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="text-muted small">Nama Kepala Keluarga (KRT):</div>
                        <h5 class="fw-bold text-dark mb-0" id="det_name"></h5>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Pemberi Informasi &amp; Status:</div>
                        <div class="fw-bold text-primary">
                            <span id="det_informant_name">-</span>
                            <span class="badge bg-secondary-subtle text-secondary border ms-1" id="det_status">-</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Instansi / Lembaga:</div>
                        <div class="fw-semibold text-dark" id="det_agency"></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Nomor HP / WhatsApp:</div>
                        <div class="fw-semibold text-dark" id="det_phone"></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Tanggal Input:</div>
                        <div class="fw-semibold text-dark" id="det_date"></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Petugas Lapangan / PPL:</div>
                        <div class="fw-semibold text-dark" id="det_officer">-</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Alamat Lengkap:</div>
                        <div class="text-dark bg-light p-2.5 rounded-3 border small" id="det_address"></div>
                        <div class="mt-1" id="det_district_badge"></div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Tujuan Pendataan / Kegiatan:</div>
                        <div class="text-secondary small fw-medium" id="det_purpose"></div>
                    </div>
                </div>

                <div class="border-top pt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold small text-dark"><i class="bi bi-file-earmark-text text-primary me-1"></i> Histori Dokumen BAST:</span>
                        <a href="{{ route('admin.basts.respondents.index') }}" class="btn btn-sm btn-bps-orange py-1 px-2.5" style="font-size: 0.75rem;">
                            <i class="bi bi-plus-lg me-1"></i> Terbitkan BAST Baru
                        </a>
                    </div>
                    <div id="det_basts_container" class="small text-muted">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-end">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5" data-bs-dismiss="modal" style="font-size: 0.82rem;">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyHeadNameToInformant(headInputId, informantInputId, statusSelectId) {
        const headVal = document.getElementById(headInputId).value;
        if (headVal) {
            document.getElementById(informantInputId).value = headVal;
            const statusSelect = document.getElementById(statusSelectId);
            if (statusSelect) {
                statusSelect.value = 'Kepala Keluarga (KRT)';
            }
        }
    }

    function setAgencyValue(inputId, value) {
        const el = document.getElementById(inputId);
        if (el) {
            el.value = value;
        }
    }

    function autoFillPurpose(selectEl, purposeInputId) {
        const purposeInput = document.getElementById(purposeInputId);
        if (!purposeInput) return;
        const selectedOption = selectEl.options[selectEl.selectedIndex];
        if (selectedOption && selectedOption.dataset.title) {
            purposeInput.value = selectedOption.dataset.title;
        }
    }

    // Auto-sinkronisasi nama KRT ke nama Pemberi Informasi saat diketik jika belum diisi manual
    document.addEventListener('DOMContentLoaded', function() {
        const createNameInput = document.getElementById('admin_create_resp_name');
        const createInformantInput = document.getElementById('admin_create_informant_name');
        if (createNameInput && createInformantInput) {
            createNameInput.addEventListener('input', function() {
                if (!createInformantInput.dataset.manualEdited) {
                    createInformantInput.value = this.value;
                }
            });
            createInformantInput.addEventListener('input', function() {
                this.dataset.manualEdited = this.value.trim() !== '' ? 'true' : '';
            });
        }
    });

    function openEditRespondentModal(resp) {
        document.getElementById('editRespondentForm').action = '/admin/respondents/' + resp.id;
        document.getElementById('edit_resp_name').value = resp.family_head_name || resp.name || '';
        document.getElementById('edit_resp_informant_name').value = resp.informant_name || resp.name || '';
        document.getElementById('edit_resp_status').value = resp.respondent_status || 'Kepala Keluarga (KRT)';
        document.getElementById('edit_resp_phone').value = resp.phone || '';
        document.getElementById('edit_resp_agency').value = resp.agency || 'Rumah Tangga';
        document.getElementById('edit_resp_assignment').value = resp.assignment_id || '';
        document.getElementById('edit_resp_address').value = resp.address;
        document.getElementById('edit_resp_district').value = resp.district || '';
        document.getElementById('edit_resp_purpose').value = resp.purpose || '';
        document.getElementById('edit_resp_officer').value = resp.officer_id;
        document.getElementById('edit_resp_date').value = resp.input_date ? resp.input_date.substring(0, 10) : '';

        const modal = new bootstrap.Modal(document.getElementById('editRespondentModal'));
        modal.show();
    }

    function openDetailRespondentModal(resp) {
        document.getElementById('det_name').innerText = resp.family_head_name || resp.name || '-';
        document.getElementById('det_informant_name').innerText = resp.informant_name || resp.name || '-';
        document.getElementById('det_status').innerText = resp.respondent_status || 'Kepala Keluarga (KRT)';
        document.getElementById('det_agency').innerText = resp.agency || '-';
        document.getElementById('det_phone').innerText = resp.phone || '-';
        document.getElementById('det_date').innerText = resp.input_date ? resp.input_date.substring(0, 10) : '-';
        document.getElementById('det_officer').innerText = resp.officer ? resp.officer.name : '-';
        document.getElementById('det_address').innerText = resp.address;
        const distBadge = document.getElementById('det_district_badge');
        if (resp.district) {
            distBadge.innerHTML = `<span class="badge bg-light text-secondary border"><i class="bi bi-pin-map-fill text-primary me-1"></i> ${resp.district}</span>`;
        } else {
            distBadge.innerHTML = '';
        }
        document.getElementById('det_purpose').innerText = resp.purpose;

        const bastsContainer = document.getElementById('det_basts_container');
        if (resp.basts && resp.basts.length > 0) {
            let html = '<ul class="list-group list-group-flush">';
            resp.basts.forEach(b => {
                html += `<li class="list-group-item d-flex justify-content-between align-items-center py-2 px-0">
                            <div>
                                <strong>${b.bast_number}</strong>
                                <span class="text-muted ms-2">(${b.bast_date.substring(0, 10)})</span>
                            </div>
                            <a href="/admin/basts/respondents/${b.id}/download" class="btn btn-sm btn-outline-success rounded-pill px-2.5">
                                <i class="bi bi-download me-1"></i> Unduh PDF
                            </a>
                         </li>`;
            });
            html += '</ul>';
            bastsContainer.innerHTML = html;
        } else {
            bastsContainer.innerHTML = '<em>Belum ada Berita Acara (BAST) yang diterbitkan untuk responden ini.</em>';
        }

        const modal = new bootstrap.Modal(document.getElementById('detailRespondentModal'));
        modal.show();
    }
</script>
@endpush

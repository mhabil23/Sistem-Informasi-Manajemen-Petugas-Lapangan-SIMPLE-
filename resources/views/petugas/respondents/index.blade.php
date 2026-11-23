@extends('layouts.app')

@section('title', 'Data Responden Lapangan')
@section('page_title', 'Data Responden Lapangan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('petugas.dashboard') }}">Petugas</a></li>
    <li class="breadcrumb-item active" aria-current="page">Data Responden</li>
@endsection

@push('styles')
<style>
    .bento-card {
        background: #FFFFFF;
        border-radius: 18px;
        border: 1px solid rgba(226, 232, 240, 0.85);
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
    }
    .bento-stat-card {
        background: #FFFFFF;
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.85);
        padding: 1.1rem 1.25rem;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.02);
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .bento-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }
    .btn-bps-orange {
        background-color: #F37021;
        border-color: #F37021;
        color: #ffffff;
        font-weight: 600;
        border-radius: 10px;
        padding: 0.45rem 1rem;
        font-size: 0.82rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s ease;
    }
    .btn-bps-orange:hover {
        background-color: #d95d12;
        border-color: #d95d12;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(243, 112, 33, 0.25);
    }
    .bento-table {
        table-layout: fixed;
        width: 100%;
        margin-bottom: 0;
        font-size: 0.82rem;
    }
    .bento-table thead th {
        background-color: #F8FAFC;
        color: #475569;
        font-weight: 700;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.75rem 0.85rem;
        border-bottom: 1px solid #E2E8F0;
        vertical-align: middle;
        white-space: nowrap;
    }
    .bento-table tbody td {
        padding: 0.75rem 0.85rem;
        vertical-align: middle;
        border-bottom: 1px solid #F1F5F9;
    }
    .bento-table tbody tr:hover td {
        background-color: #F8FAFC;
    }
    .bento-action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        padding: 0;
        transition: all 0.15s ease;
    }
    .bento-action-btn:hover {
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="bi bi-houses text-primary"></i> Data Sampel Ruta (Responden)
        </h3>
        <p class="text-muted small mb-0">Daftar sampel rumah tangga (ruta) resmi yang dialokasikan oleh Administrator BPS. Lakukan verifikasi dan terbitkan Berita Acara Serah Terima (BAST).</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="{{ route('petugas.basts.respondents.index') }}" class="btn-bps-orange shadow-sm">
            <i class="bi bi-file-earmark-check-fill"></i> Menu Tanda Terima Responden
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="bento-stat-card">
            <div class="bento-stat-icon" style="background: #EFF6FF; color: #005BAC;">
                <i class="bi bi-bullseye"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold" style="font-size: 0.76rem;">TARGET KUOTA</div>
                <div class="fw-bold text-primary" style="font-size: 1.4rem;">{{ number_format($targetQuota ?? 120) }}</div>
                <div class="text-muted small" style="font-size: 0.72rem;">Alokasi responden Anda</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="bento-stat-card">
            <div class="bento-stat-icon" style="background: #F0FDF4; color: #16A34A;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold" style="font-size: 0.76rem;">RESPONDEN TERDATA</div>
                <div class="fw-bold text-success" style="font-size: 1.4rem;">{{ number_format($totalMyRespondents) }}</div>
                <div class="text-muted small" style="font-size: 0.72rem;">Telah tercatat di sistem</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="bento-stat-card">
            <div class="bento-stat-icon" style="background: #FFFBEB; color: #D97706;">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold" style="font-size: 0.76rem;">SISA TARGET</div>
                <div class="fw-bold text-warning" style="font-size: 1.4rem;">{{ number_format($remainingTarget ?? max(0, ($targetQuota ?? 120) - $totalMyRespondents)) }}</div>
                <div class="text-muted small" style="font-size: 0.72rem;">Belum diverifikasi</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="bento-stat-card">
            <div class="bento-stat-icon" style="background: #FAF5FF; color: #7C3AED;">
                <i class="bi bi-file-earmark-check-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold" style="font-size: 0.76rem;">BAST TERBIT</div>
                <div class="fw-bold" style="font-size: 1.4rem; color: #7C3AED;">{{ number_format($withBastCount) }}</div>
                <div class="text-muted small" style="font-size: 0.72rem;">Berita acara sah</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar & Status Tabs -->
<div class="bento-card mb-4 p-3">
    <!-- Status Filter Pills -->
    <div class="d-flex flex-wrap gap-2 mb-3 pb-2 border-bottom">
        <a href="{{ route('petugas.respondents.index', array_merge(request()->except('progress_status', 'page'), [])) }}" 
           class="btn btn-sm {{ !request('progress_status') ? 'btn-dark' : 'btn-outline-secondary' }} rounded-pill px-3 py-1 font-monospace" style="font-size: 0.78rem;">
            Semua ({{ $totalMyRespondents }})
        </a>
        <a href="{{ route('petugas.respondents.index', array_merge(request()->except('progress_status', 'page'), ['progress_status' => 'belum_didata'])) }}" 
           class="btn btn-sm {{ request('progress_status') === 'belum_didata' ? 'btn-secondary text-white' : 'btn-outline-secondary' }} rounded-pill px-3 py-1" style="font-size: 0.78rem;">
            <i class="bi bi-clock me-1"></i> Belum Didata ({{ $totalBelumDidata }})
        </a>
        <a href="{{ route('petugas.respondents.index', array_merge(request()->except('progress_status', 'page'), ['progress_status' => 'sedang_didata'])) }}" 
           class="btn btn-sm {{ request('progress_status') === 'sedang_didata' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' }} rounded-pill px-3 py-1" style="font-size: 0.78rem;">
            <i class="bi bi-hourglass-split me-1"></i> Sedang Didata ({{ $totalSedangDidata }})
        </a>
        <a href="{{ route('petugas.respondents.index', array_merge(request()->except('progress_status', 'page'), ['progress_status' => 'selesai'])) }}" 
           class="btn btn-sm {{ request('progress_status') === 'selesai' ? 'btn-success text-white' : 'btn-outline-success' }} rounded-pill px-3 py-1" style="font-size: 0.78rem;">
            <i class="bi bi-check-circle me-1"></i> Selesai Didata ({{ $totalSelesai }})
        </a>
    </div>

    <form method="GET" action="{{ route('petugas.respondents.index') }}" class="row g-2 align-items-center">
        @if(request('progress_status'))
            <input type="hidden" name="progress_status" value="{{ request('progress_status') }}">
        @endif
        <div class="col-lg-6 col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" 
                       name="search" 
                       class="form-control border-start-0 ps-0" 
                       placeholder="Cari nama responden, instansi, alamat, nomor HP..." 
                       value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-lg-4 col-md-4">
            <select name="assignment_id" class="form-select form-select-sm">
                <option value="">-- Semua Kegiatan Lapangan --</option>
                @foreach($myAssignments as $asgn)
                    <option value="{{ $asgn->id }}" {{ request('assignment_id') == $asgn->id ? 'selected' : '' }}>
                        {{ $asgn->title }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary rounded-pill w-100 fw-semibold">
                <i class="bi bi-filter me-1"></i> Filter
            </button>
            @if(request('search') || request('assignment_id') || request('progress_status'))
                <a href="{{ route('petugas.respondents.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="bento-card mb-4 overflow-hidden">
    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom bg-white">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #FFF4ED; color: #F37021;">
                <i class="bi bi-people-fill"></i>
            </div>
            <h5 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">
                Daftar Responden Lapangan ({{ $respondents->total() }})
            </h5>
        </div>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 font-monospace" style="font-size: 0.72rem;">
            Halaman {{ $respondents->currentPage() }} dari {{ $respondents->lastPage() ?: 1 }}
        </span>
    </div>

    <!-- MODE DESKTOP & TABLET: Tabel Data Anti-Geser -->
    <div class="d-none d-md-block">
        <table class="table bento-table align-middle">
            <thead>
                <tr>
                    <th style="width: 25%;">Kepala Keluarga &amp; Responden</th>
                    <th style="width: 17%;">Instansi / Lembaga</th>
                    <th style="width: 22%;">Alamat</th>
                    <th style="width: 14%;">Kegiatan</th>
                    <th style="width: 10%; text-align: center;">Status BAST</th>
                    <th style="width: 12%; text-align: right;" class="pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($respondents as $resp)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark text-truncate">{{ $resp->family_head_name ?? $resp->name }}</div>
                            <div class="small my-1 d-flex flex-wrap gap-1 align-items-center">
                                <span class="badge bg-light text-dark border" style="font-size: 0.68rem;">
                                    <i class="bi bi-person text-primary me-0.5"></i> {{ $resp->informant_name ?? $resp->name }}
                                </span>
                                <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.68rem;">
                                    {{ $resp->respondent_status ?? 'KRT' }}
                                </span>
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">
                                <i class="bi bi-telephone me-1"></i> {{ $resp->phone ?? '-' }}
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-light text-primary border text-truncate d-inline-block" style="max-width: 100%; font-size: 0.72rem;">
                                {{ $resp->agency }}
                            </span>
                        </td>
                        <td>
                            <div class="small text-secondary text-truncate" title="{{ $resp->address }}" style="font-size: 0.75rem;">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $resp->address }}
                            </div>
                        </td>
                        <td>
                            <span class="small fw-semibold text-dark text-truncate d-block" style="font-size: 0.75rem;" title="{{ $resp->assignment ? $resp->assignment->title : '-' }}">
                                {{ $resp->assignment ? $resp->assignment->title : '-' }}
                            </span>
                            <span class="text-muted" style="font-size: 0.68rem;">
                                {{ $resp->input_date->format('d/m/Y') }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-column align-items-center gap-1">
                                {!! $resp->progress_status_badge !!}
                                @if($resp->basts->isNotEmpty())
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                        <i class="bi bi-file-earmark-check-fill me-0.5"></i> {{ $resp->basts->count() }} BAST
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-inline-flex gap-1.5 align-items-center">
                                <!-- Verifikasi / Edit Data Lapangan -->
                                <button type="button" 
                                        class="btn btn-outline-warning bento-action-btn" 
                                        title="Verifikasi / Update Lapangan" 
                                        data-bs-toggle="tooltip"
                                        onclick="openEditMyRespondentModal({{ json_encode($resp) }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <!-- Shortcut Tanda Terima Responden -->
                                <a href="{{ route('petugas.basts.respondents.index') }}" 
                                   class="btn btn-outline-success bento-action-btn" 
                                   title="Buka / Terbitkan Tanda Terima" 
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-file-earmark-check"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-people display-6 text-secondary opacity-50 mb-2 d-block"></i>
                            <div class="fw-semibold">
                                @if(request('search') || request('assignment_id'))
                                    Tidak ditemukan data sampel responden yang sesuai filter.
                                @else
                                    Belum ada sampel rumah tangga (ruta) yang dialokasikan untuk Anda.
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- MODE MOBILE / HP: Daftar Kartu Responsif Cepat -->
    <div class="d-block d-md-none p-3">
        @forelse($respondents as $resp)
            <div class="bento-card p-3 mb-3 border">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.92rem;">{{ $resp->family_head_name ?? $resp->name }}</h6>
                        <div class="small text-muted mb-1 d-flex flex-wrap gap-1">
                            <span class="badge bg-light text-dark border" style="font-size: 0.68rem;">
                                <i class="bi bi-person text-primary me-0.5"></i> {{ $resp->informant_name ?? $resp->name }}
                            </span>
                            <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.68rem;">
                                {{ $resp->respondent_status ?? 'KRT' }}
                            </span>
                        </div>
                        <span class="badge bg-light text-primary border" style="font-size: 0.68rem;">
                            <i class="bi bi-building me-1"></i> {{ $resp->agency }}
                        </span>
                    </div>
                    <div class="d-flex gap-1.5">
                        <button type="button" 
                                class="btn btn-sm btn-outline-warning bento-action-btn" 
                                title="Edit Data Responden" 
                                onclick="openEditMyRespondentModal({{ json_encode($resp) }})">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        @if($resp->phone)
                            <a href="tel:{{ $resp->phone }}" class="btn btn-sm btn-outline-success bento-action-btn" title="Telepon">
                                <i class="bi bi-telephone"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="small text-secondary mb-2.5" style="font-size: 0.78rem;">
                    @if($resp->phone)
                        <div class="mb-1">
                            <i class="bi bi-telephone text-muted me-1.5"></i>
                            <span>{{ $resp->phone }}</span>
                        </div>
                    @endif
                    <div class="mb-1">
                        <i class="bi bi-geo-alt-fill text-danger me-1.5"></i>
                        <span>{{ $resp->address }}</span>
                    </div>
                    @if($resp->assignment)
                        <div class="mb-1 text-truncate">
                            <i class="bi bi-briefcase text-primary me-1.5"></i>
                            <span class="fw-semibold text-dark">{{ $resp->assignment->title }}</span>
                        </div>
                    @endif
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <span class="text-muted small" style="font-size: 0.72rem;">
                        <i class="bi bi-calendar me-1"></i> {{ $resp->input_date->format('d/m/Y') }}
                    </span>
                    <div class="d-flex align-items-center gap-1.5">
                        {!! $resp->progress_status_badge !!}
                        @if($resp->basts->isNotEmpty())
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.68rem;">
                                <i class="bi bi-check2-circle me-1"></i> {{ $resp->basts->count() }} BAST Terbit
                            </span>
                        @else
                            <a href="{{ route('petugas.basts.respondents.index') }}" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                <i class="bi bi-file-earmark-plus me-1"></i> Buat BAST
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-muted">
                <i class="bi bi-people display-6 text-secondary opacity-50 mb-2 d-block"></i>
                <div class="fw-semibold small">
                    @if(request('search') || request('assignment_id'))
                        Tidak ada sampel responden sesuai filter.
                    @else
                        Belum ada sampel rumah tangga (ruta) yang dialokasikan untuk Anda.
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    @if($respondents->hasPages())
        <div class="bg-white border-top p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="small text-muted" style="font-size: 0.78rem;">
                Menampilkan {{ $respondents->firstItem() }} sampai {{ $respondents->lastItem() }} dari {{ $respondents->total() }} responden
            </div>
            <div>
                {{ $respondents->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

<!-- ==============================================================
     MODAL EDIT RESPONDEN (PETUGAS)
     ============================================================== -->
<div class="modal fade" id="editMyRespondentModal" tabindex="-1" aria-labelledby="editMyRespondentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom py-2.5 px-3">
                <h6 class="modal-title fw-bold" id="editMyRespondentModalLabel">
                    <i class="bi bi-pencil-square text-warning me-1.5"></i> Edit / Verifikasi Data Responden
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editMyRespondentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-3.5">
                    <!-- Baris 1: Nama KRT & No HP -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary" style="font-size: 0.78rem;">
                                <i class="bi bi-house-door text-primary me-1"></i> NAMA KEPALA KELUARGA (KRT) <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="my_edit_name" name="name" class="form-control" style="font-size: 0.85rem;" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary" style="font-size: 0.78rem;">
                                <i class="bi bi-telephone text-muted me-1"></i> NOMOR HP / WHATSAPP
                            </label>
                            <input type="text" id="my_edit_phone" name="phone" class="form-control" style="font-size: 0.85rem;">
                        </div>
                    </div>

                    <!-- Baris 2: Nama Pemberi Informasi & Status Hubungan -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-semibold text-secondary mb-0" style="font-size: 0.78rem;">
                                    <i class="bi bi-person-check text-success me-1"></i> NAMA PEMBERI INFORMASI <span class="text-danger">*</span>
                                </label>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 rounded-pill" style="font-size: 0.7rem;" onclick="copyHeadNameToInformant('my_edit_name', 'my_edit_informant_name', 'my_edit_status')">
                                    <i class="bi bi-copy me-1"></i> Sama dg KRT
                                </button>
                            </div>
                            <input type="text" id="my_edit_informant_name" name="informant_name" class="form-control" style="font-size: 0.85rem;" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary" style="font-size: 0.78rem;">
                                <i class="bi bi-people text-primary me-1"></i> STATUS RESPONDEN / HUBUNGAN <span class="text-danger">*</span>
                            </label>
                            <select id="my_edit_status" name="respondent_status" class="form-select" style="font-size: 0.85rem;" required>
                                <option value="Kepala Keluarga (KRT)">Kepala Keluarga (KRT)</option>
                                <option value="Istri / Suami (Pasangan)">Istri / Suami (Pasangan)</option>
                                <option value="Anak Kandung">Anak Kandung</option>
                                <option value="Famili Lain / ART">Famili Lain / ART</option>
                                <option value="Pengelola / Perwakilan Usaha">Pengelola / Perwakilan Usaha</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <!-- Baris 3: Instansi & Penugasan -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary" style="font-size: 0.78rem;">
                                <i class="bi bi-building text-muted me-1"></i> INSTANSI / ASAL LEMBAGA <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="my_edit_agency" name="agency" class="form-control" style="font-size: 0.85rem;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary" style="font-size: 0.78rem;">
                                <i class="bi bi-briefcase text-muted me-1"></i> KEGIATAN PENUGASAN
                            </label>
                            <select id="my_edit_assignment" name="assignment_id" class="form-select" style="font-size: 0.85rem;">
                                <option value="">-- Pilih Kegiatan --</option>
                                @foreach($myAssignments as $item)
                                    <option value="{{ $item->id }}">{{ $item->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Baris 4: Alamat Lengkap -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary" style="font-size: 0.78rem;">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> ALAMAT LENGKAP <span class="text-danger">*</span>
                        </label>
                        <textarea id="my_edit_address" name="address" class="form-control" rows="2" style="font-size: 0.85rem;" required></textarea>
                    </div>

                    <!-- Baris 5: Tujuan Kegiatan & Tanggal Input -->
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-secondary" style="font-size: 0.78rem;">TUJUAN KEGIATAN <span class="text-danger">*</span></label>
                            <input type="text" id="my_edit_purpose" name="purpose" class="form-control" style="font-size: 0.85rem;" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary" style="font-size: 0.78rem;">TANGGAL INPUT <span class="text-danger">*</span></label>
                            <input type="date" id="my_edit_date" name="input_date" class="form-control" style="font-size: 0.85rem;" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2 px-3">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal" style="font-size: 0.82rem;">Batal</button>
                    <button type="submit" class="btn-bps-orange rounded-pill px-4">
                        <i class="bi bi-check2-circle"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
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

    function openEditMyRespondentModal(resp) {
        document.getElementById('editMyRespondentForm').action = '/petugas/respondents/' + resp.id;
        document.getElementById('my_edit_name').value = resp.family_head_name || resp.name || '';
        document.getElementById('my_edit_informant_name').value = resp.informant_name || resp.name || '';
        document.getElementById('my_edit_status').value = resp.respondent_status || 'Kepala Keluarga (KRT)';
        document.getElementById('my_edit_phone').value = resp.phone || '';
        document.getElementById('my_edit_agency').value = resp.agency || '';
        document.getElementById('my_edit_assignment').value = resp.assignment_id || '';
        document.getElementById('my_edit_address').value = resp.address || '';
        document.getElementById('my_edit_purpose').value = resp.purpose || '';
        document.getElementById('my_edit_date').value = resp.input_date ? resp.input_date.substring(0, 10) : '';

        const modal = new bootstrap.Modal(document.getElementById('editMyRespondentModal'));
        modal.show();
    }
</script>
@endpush

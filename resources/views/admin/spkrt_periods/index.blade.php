@extends('layouts.app')

@section('title', 'Periode & Kontrol Akses Menu - SIMPEL')
@section('page_title', 'Periode & Kontrol Akses Menu')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Periode &amp; Akses Menu</li>
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

/* Bento Pill Tabs */
.bento-tabs-container {
    background: #F1F5F9;
    padding: 0.35rem;
    border-radius: 999px;
    display: inline-flex;
    border: 1px solid #E2E8F0;
}
.bento-tabs-container .nav-link {
    border-radius: 999px;
    padding: 0.55rem 1.35rem;
    font-size: 0.85rem;
    font-weight: 700;
    color: #475569;
    transition: all 0.2s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    background: transparent;
}
.bento-tabs-container .nav-link:hover {
    color: #0F172A;
}
.bento-tabs-container .nav-link.active {
    background: #FFFFFF;
    color: #0F172A;
    box-shadow: 0 3px 10px rgba(15, 23, 42, 0.08);
}

/* Stat Bento Item */
.bento-stat-item {
    padding: 1.15rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
    overflow: hidden;
    border-radius: 20px;
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
}
.bento-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
}

/* Action Buttons */
.btn-bps-orange {
    background: #F37021;
    color: #FFFFFF !important;
    border: none;
    font-weight: 700;
    border-radius: 999px;
    padding: 0.55rem 1.35rem;
    box-shadow: 0 4px 14px rgba(243, 112, 33, 0.25);
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    text-decoration: none;
    font-size: 0.85rem;
}
.btn-bps-orange:hover {
    background: #E05D0E;
    color: #FFFFFF !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(243, 112, 33, 0.35);
}

.bento-table-card {
    background: #FFFFFF;
    border-radius: 20px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.03);
    overflow: hidden;
    width: 100%;
}
.bento-table-header {
    padding: 1rem 1.35rem;
    border-bottom: 1px solid #E2E8F0;
    background: #FAFAFA;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
}
.bento-table-fixed {
    table-layout: fixed;
    width: 100%;
    margin-bottom: 0;
    font-size: 0.82rem;
}
.bento-table-fixed th {
    background: #F8FAFC;
    color: #475569;
    font-weight: 700;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 0.9rem 0.85rem;
    border-bottom: 1px solid #E2E8F0;
    vertical-align: middle;
}
.bento-table-fixed td {
    padding: 0.9rem 0.85rem;
    border-bottom: 1px solid #F1F5F9;
    vertical-align: middle;
}
.bento-table-fixed tbody tr:hover td {
    background-color: #FBFDFF;
}

.pulse-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
    animation: pulse-green 1.8s infinite;
}
@keyframes pulse-green {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
}

.feature-guide-box {
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    border-radius: 16px;
    padding: 1rem;
    transition: all 0.2s ease;
}
.feature-guide-box:hover {
    background: #FFFFFF;
    border-color: #CBD5E1;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
}
</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    <!-- Header Section -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: #0284C7; width: 34px; height: 34px;">
                    <i class="bi bi-sliders2-vertical fs-5"></i>
                </div>
                <h3 class="fw-bold text-dark mb-0">Periode &amp; Kontrol Akses Menu</h3>
            </div>
            <p class="text-muted small mb-0 ps-0.5">
                Pusat kendali saklar 4 menu Beranda Petugas dan batas waktu resmi pengisian berkas sensus/survei SPKRT.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn-bps-orange shadow-sm" data-bs-toggle="modal" data-bs-target="#createPeriodModal">
                <i class="bi bi-plus-circle-fill"></i> Tambah Periode SPKRT
            </button>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2.5 mb-4 px-3.5 py-2.5" role="alert">
            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
            <div class="fw-medium small">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2.5 mb-4 px-3.5 py-2.5" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
            <div class="fw-medium small">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4 px-3.5 py-2.5" role="alert">
            <div class="fw-bold mb-1 small"><i class="bi bi-exclamation-circle-fill me-1"></i> Terjadi kesalahan input:</div>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Modern Bento Tab Navigation -->
    @php
        $activeTab = $activeTab ?? 'beranda';
    @endphp
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <ul class="nav nav-pills bento-tabs-container shadow-sm" id="accessPeriodTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab === 'beranda' ? 'active' : '' }}" id="tab-beranda-btn" data-bs-toggle="pill" data-bs-target="#tab-beranda-pane" type="button" role="tab" onclick="setTabUrl('beranda')">
                    <i class="bi bi-phone me-1.5 text-primary"></i> 1. Kontrol 4 Menu Beranda
                    <span class="badge {{ ($berandaState['visible_count'] ?? 0) > 0 ? 'bg-success' : 'bg-secondary' }} rounded-pill ms-2" style="font-size: 0.7rem;">
                        {{ $berandaState['visible_count'] ?? 0 }}/{{ $berandaState['total_count'] ?? 4 }} Aktif
                    </span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab === 'spkrt' ? 'active' : '' }}" id="tab-spkrt-btn" data-bs-toggle="pill" data-bs-target="#tab-spkrt-pane" type="button" role="tab" onclick="setTabUrl('spkrt')">
                    <i class="bi bi-calendar-range me-1.5 text-success"></i> 2. Periode Pengisian SPKRT
                    <span class="badge {{ $totalActive > 0 ? 'bg-primary' : 'bg-secondary' }} rounded-pill ms-2" style="font-size: 0.7rem;">
                        {{ $totalActive }} Buka Sekarang
                    </span>
                </button>
            </li>
        </ul>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content" id="accessPeriodTabsContent">
        
        <!-- ==============================================================
             TAB 1: KONTROL 4 MENU BERANDA PETUGAS
             ============================================================== -->
        <div class="tab-pane fade {{ $activeTab === 'beranda' ? 'show active' : '' }}" id="tab-beranda-pane" role="tabpanel">
            <!-- Kontrol Tampil & Periode Menu Beranda Petugas -->
            @include('admin.partials._beranda_menu_control', ['berandaState' => $berandaState])

            <!-- Feature Guide 3-Box -->
            <div class="bento-card-clean p-4 mt-4">
                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle-fill text-primary"></i>
                    <span>Panduan &amp; Aturan Visibilitas Menu di HP Petugas</span>
                </h6>
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <div class="feature-guide-box h-100">
                            <div class="d-flex align-items-center gap-2 mb-1.5">
                                <div class="rounded-3 p-1.5 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                    <i class="bi bi-toggle-on fs-6"></i>
                                </div>
                                <div class="fw-bold text-dark small">Saklar Manual (On / Off)</div>
                            </div>
                            <p class="text-secondary small mb-0" style="font-size: 0.78rem;">
                                Admin dapat menyalakan atau mematikan tombol menu kapan saja secara instan tanpa harus menentukan tanggal.
                            </p>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="feature-guide-box h-100">
                            <div class="d-flex align-items-center gap-2 mb-1.5">
                                <div class="rounded-3 p-1.5 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                    <i class="bi bi-calendar2-check fs-6"></i>
                                </div>
                                <div class="fw-bold text-dark small">Jadwal Kalender Otomatis</div>
                            </div>
                            <p class="text-secondary small mb-0" style="font-size: 0.78rem;">
                                Tombol menu akan otomatis muncul di HP petugas jika tanggal hari ini masuk dalam rentang tanggal yang dijadwalkan.
                            </p>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="feature-guide-box h-100">
                            <div class="d-flex align-items-center gap-2 mb-1.5">
                                <div class="rounded-3 p-1.5 bg-amber-subtle text-warning d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                    <i class="bi bi-shield-check fs-6"></i>
                                </div>
                                <div class="fw-bold text-dark small">Integritas Data Aman</div>
                            </div>
                            <p class="text-secondary small mb-0" style="font-size: 0.78rem;">
                                Mematikan menu hanya menyembunyikan tombolnya di Beranda. Seluruh data kuesioner, BAST, dan laporan tetap tersimpan aman.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==============================================================
             TAB 2: PERIODE PENGISIAN SPKRT (VALIDASI & GEMBOK FORM)
             ============================================================== -->
        <div class="tab-pane fade {{ $activeTab === 'spkrt' ? 'show active' : '' }}" id="tab-spkrt-pane" role="tabpanel">
            
            <!-- Metric Stat Cards -->
            <div class="row g-3 mb-4">
                <!-- 1. Total Periode -->
                <div class="col-6 col-md-3">
                    <div class="bento-stat-item">
                        <div class="bento-stat-icon" style="background: #EFF6FF; color: #1D4ED8;">
                            <i class="bi bi-calendar-range"></i>
                        </div>
                        <div>
                            <div class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Total Periode</div>
                            <div class="fs-4 fw-bolder text-dark">{{ $totalPeriods }}</div>
                        </div>
                    </div>
                </div>

                <!-- 2. Pengisian Dibuka -->
                <div class="col-6 col-md-3">
                    <div class="bento-stat-item">
                        <div class="bento-stat-icon" style="background: #ECFDF5; color: #047857;">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Dibuka Sekarang</div>
                            <div class="fs-4 fw-bolder text-success d-flex align-items-center gap-1.5">
                                {{ $totalActive }}
                                @if($totalActive > 0)
                                    <span class="pulse-dot bg-success ms-1" title="Periode Aktif"></span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Belum Dibuka -->
                <div class="col-6 col-md-3">
                    <div class="bento-stat-item">
                        <div class="bento-stat-icon" style="background: #FFFBEB; color: #B45309;">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div>
                            <div class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Belum Dibuka</div>
                            <div class="fs-4 fw-bolder text-warning">{{ $totalNotStarted }}</div>
                        </div>
                    </div>
                </div>

                <!-- 4. Ditutup / Nonaktif -->
                <div class="col-6 col-md-3">
                    <div class="bento-stat-item">
                        <div class="bento-stat-icon" style="background: #FEF2F2; color: #B91C1C;">
                            <i class="bi bi-lock-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Telah Ditutup</div>
                            <div class="fs-4 fw-bolder text-danger">{{ $totalClosed }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search & Filter Card -->
            <div class="bento-card-clean p-3.5 mb-4">
                <form method="GET" action="{{ route('admin.spkrt-periods.index') }}" class="row g-2.5 align-items-center">
                    <input type="hidden" name="tab" value="spkrt">
                    <div class="col-12 col-md-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control bg-light border-start-0 ps-1" 
                                   placeholder="Cari nama kegiatan atau deskripsi periode..." 
                                   value="{{ request('search') }}" style="font-size: 0.8rem;">
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <select name="status" class="form-select form-select-sm bg-light" onchange="this.form.submit()" style="font-size: 0.8rem;">
                            <option value="">Semua Status Pengisian</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>🟢 Dibuka Sekarang</option>
                            <option value="not_started" {{ request('status') === 'not_started' ? 'selected' : '' }}>🟡 Belum Dibuka</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>🔴 Ditutup / Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-bps-orange rounded-pill px-3 w-100 justify-content-center" style="font-size: 0.78rem;">
                            <i class="bi bi-funnel-fill"></i> Filter
                        </button>
                        @if(request('search') || request('status'))
                            <a href="{{ route('admin.spkrt-periods.index', ['tab' => 'spkrt']) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 d-flex align-items-center justify-content-center" title="Reset Filter">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Main Table Card -->
            <div class="bento-table-card">
                <div class="bento-table-header">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: #EFF6FF; color: #1D4ED8;">
                            <i class="bi bi-list-check fs-6"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Daftar Jadwal Periode Pengisian SPKRT</h6>
                            <span class="text-muted" style="font-size: 0.72rem;">Total {{ $periods->total() }} jadwal periode tercatat</span>
                        </div>
                    </div>
                    <span class="badge bg-light text-secondary border rounded-pill px-3 py-1.5" style="font-size: 0.72rem;">
                        <i class="bi bi-shield-lock me-1 text-primary"></i> Formulir kuesioner terkunci otomatis saat periode berakhir
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table bento-table-fixed align-middle">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center">NO</th>
                                <th style="width: 28%;">NAMA KEGIATAN</th>
                                <th style="width: 24%;">JADWAL WAKTU</th>
                                <th style="width: 19%;">STATUS SISTEM (PETUGAS)</th>
                                <th style="width: 12%;" class="text-center">KONTROL ADMIN</th>
                                <th style="width: 12%;" class="text-end pe-3">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($periods as $index => $period)
                                @php
                                    $computedStatus = $period->currentStatus();
                                    $statusMsg = $period->status_message;
                                    $today = \Carbon\Carbon::today();
                                @endphp
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $periods->firstItem() + $index }}</td>
                                    <td>
                                        <div class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $period->activity_name }}</div>
                                        @if($period->description)
                                            <div class="text-muted small text-truncate mt-0.5" style="max-width: 320px;" title="{{ $period->description }}">
                                                {{ $period->description }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1.5 small mb-1">
                                            <span class="badge bg-light text-dark border px-2 py-0.5 rounded-pill" style="font-size: 0.72rem;">
                                                <i class="bi bi-calendar-event me-1 text-primary"></i>
                                                {{ $period->start_date ? $period->start_date->format('d/m/Y') : '-' }}
                                            </span>
                                            <span class="text-muted" style="font-size: 0.75rem;">&rarr;</span>
                                            <span class="badge bg-light text-dark border px-2 py-0.5 rounded-pill" style="font-size: 0.72rem;">
                                                <i class="bi bi-calendar-check me-1 text-danger"></i>
                                                {{ $period->end_date ? $period->end_date->format('d/m/Y') : '-' }}
                                            </span>
                                        </div>
                                        @if($period->start_date && $period->end_date)
                                            <div class="text-muted" style="font-size: 0.72rem;">
                                                Durasi: {{ $period->start_date->diffInDays($period->end_date) + 1 }} hari
                                                @if($computedStatus === 'active')
                                                    &bull; <span class="text-success fw-bold">Sisa {{ $today->diffInDays($period->end_date, false) }} hari lagi</span>
                                                @elseif($computedStatus === 'not_started')
                                                    &bull; <span class="text-warning fw-bold">Dimulai dalam {{ $today->diffInDays($period->start_date) }} hari</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($computedStatus === 'active')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill d-inline-flex align-items-center gap-1.5" style="font-size: 0.74rem;">
                                                <span class="pulse-dot bg-success"></span>
                                                <strong>{{ $statusMsg }}</strong>
                                            </span>
                                        @elseif($computedStatus === 'not_started')
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill d-inline-flex align-items-center gap-1" style="font-size: 0.74rem;">
                                                <i class="bi bi-clock-history"></i>
                                                <strong>{{ $statusMsg }}</strong>
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill d-inline-flex align-items-center gap-1" style="font-size: 0.74rem;">
                                                <i class="bi bi-lock-fill"></i>
                                                <strong>{{ $statusMsg }}</strong>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.spkrt-periods.toggle-status', $period) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            @if($period->is_active)
                                                <button type="submit" class="btn btn-sm btn-outline-success py-1 px-3 rounded-pill fw-semibold" style="font-size: 0.74rem;" title="Klik untuk menonaktifkan jadwal periode ini">
                                                    <i class="bi bi-toggle-on me-1 fs-6 align-middle"></i> Aktif
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-outline-secondary py-1 px-3 rounded-pill fw-semibold" style="font-size: 0.74rem;" title="Klik untuk mengaktifkan kembali jadwal periode ini">
                                                    <i class="bi bi-toggle-off me-1 fs-6 align-middle"></i> Nonaktif
                                                </button>
                                            @endif
                                        </form>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-inline-flex align-items-center gap-1.5">
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1" 
                                                    title="Edit Periode" 
                                                    onclick="openEditPeriodModal({{ json_encode($period) }})" style="font-size: 0.76rem;">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <form action="{{ route('admin.spkrt-periods.destroy', $period) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal periode ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1" title="Hapus Periode" style="font-size: 0.76rem;">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-2" style="background: #F1F5F9; color: #94A3B8;">
                                                <i class="bi bi-calendar-x fs-1"></i>
                                            </div>
                                            <div class="fw-bold text-dark fs-6 mb-1">Belum Ada Jadwal Periode Pengisian SPKRT</div>
                                            <p class="small text-muted mb-3" style="max-width: 420px; margin: 0 auto;">
                                                Klik tombol di bawah untuk membuat periode baru agar formulir kuesioner dan BAST dapat diisi oleh petugas lapangan.
                                            </p>
                                            <button type="button" class="btn btn-bps-orange shadow-sm" data-bs-toggle="modal" data-bs-target="#createPeriodModal">
                                                <i class="bi bi-plus-circle-fill"></i> Buat Periode Pertama
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($periods->hasPages())
                    <div class="px-4 py-3 border-top bg-light">
                        {{ $periods->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>

<!-- ==============================================================
     MODAL CREATE PERIODE SPKRT
     ============================================================== -->
<div class="modal fade" id="createPeriodModal" tabindex="-1" aria-labelledby="createPeriodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.spkrt-periods.store') }}" class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            @csrf
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: #F37021; width: 32px; height: 32px;">
                        <i class="bi bi-calendar-plus-fill"></i>
                    </div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="createPeriodModalLabel" style="font-size: 0.96rem;">
                        Tambah Periode Pengisian SPKRT
                    </h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">NAMA KEGIATAN LAPANGAN <span class="text-danger">*</span></label>
                    <input type="text" name="activity_name" class="form-control rounded-3" list="assignmentSuggestions" placeholder="Contoh: Survei Pertanian Terintegrasi 2026" required style="font-size: 0.85rem;">
                    <datalist id="assignmentSuggestions">
                        @foreach($assignments as $assign)
                            <option value="{{ $assign->title }}">
                        @endforeach
                    </datalist>
                    <div class="form-text text-muted" style="font-size: 0.72rem;">Ketik nama baru atau pilih kegiatan yang telah terdaftar di sistem.</div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark">TANGGAL MULAI <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control rounded-3" required value="{{ date('Y-m-d') }}" style="font-size: 0.85rem;">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark">TANGGAL SELESAI <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control rounded-3" required value="{{ date('Y-m-d', strtotime('+14 days')) }}" style="font-size: 0.85rem;">
                    </div>
                </div>

                <div class="mb-3 p-3 rounded-3 bg-light border">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="createIsActive" checked>
                        <label class="form-check-label small fw-semibold text-dark" for="createIsActive">
                            Aktifkan Jadwal Periode Langsung
                        </label>
                        <div class="text-muted" style="font-size: 0.7rem;">Petugas dapat langsung mengisi kuesioner dan tanda tangan BAST selama rentang tanggal berlaku.</div>
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label small fw-bold text-dark">CATATAN KHUSUS (OPSIONAL)</label>
                    <textarea name="description" class="form-control rounded-3" rows="2" placeholder="Catatan atau instruksi khusus untuk petugas lapangan..." style="font-size: 0.85rem;"></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2.5 px-4">
                <button type="button" class="btn btn-sm btn-light rounded-pill px-3.5" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-bps-orange rounded-pill px-4">
                    <i class="bi bi-save me-1"></i> Simpan Periode
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================================
     MODAL EDIT PERIODE SPKRT
     ============================================================== -->
<div class="modal fade" id="editPeriodModal" tabindex="-1" aria-labelledby="editPeriodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editPeriodForm" method="POST" class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            @csrf
            @method('PUT')
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: #0284C7; width: 32px; height: 32px;">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="editPeriodModalLabel" style="font-size: 0.96rem;">
                        Edit Periode Pengisian SPKRT
                    </h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">NAMA KEGIATAN <span class="text-danger">*</span></label>
                    <input type="text" name="activity_name" id="editActivityName" class="form-control rounded-3" required style="font-size: 0.85rem;">
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark">TANGGAL MULAI <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="editStartDate" class="form-control rounded-3" required style="font-size: 0.85rem;">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark">TANGGAL SELESAI <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" id="editEndDate" class="form-control rounded-3" required style="font-size: 0.85rem;">
                    </div>
                </div>

                <div class="mb-3 p-3 rounded-3 bg-light border">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editIsActive">
                        <label class="form-check-label small fw-semibold text-dark" for="editIsActive">
                            Status Periode Aktif
                        </label>
                        <div class="text-muted" style="font-size: 0.7rem;">Nonaktifkan jika ingin menutup pengisian sementara sebelum tanggal berakhir.</div>
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label small fw-bold text-dark">CATATAN KHUSUS (OPSIONAL)</label>
                    <textarea name="description" id="editDescription" class="form-control rounded-3" rows="2" style="font-size: 0.85rem;"></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2.5 px-4">
                <button type="button" class="btn btn-sm btn-light rounded-pill px-3.5" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-bps-orange rounded-pill px-4">
                    <i class="bi bi-check2-circle me-1"></i> Perbarui Periode
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function setTabUrl(tab) {
    const url = new URL(window.location);
    url.searchParams.set('tab', tab);
    window.history.replaceState({}, '', url);
}

document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const tabParam = params.get('tab');
    if (tabParam === 'spkrt') {
        const spkrtTabTrigger = document.querySelector('#tab-spkrt-btn');
        if (spkrtTabTrigger) {
            const tab = new bootstrap.Tab(spkrtTabTrigger);
            tab.show();
        }
    } else if (tabParam === 'beranda') {
        const berandaTabTrigger = document.querySelector('#tab-beranda-btn');
        if (berandaTabTrigger) {
            const tab = new bootstrap.Tab(berandaTabTrigger);
            tab.show();
        }
    }
});

function openEditPeriodModal(period) {
    document.getElementById('editPeriodForm').action = '/admin/spkrt-periods/' + period.id;
    document.getElementById('editActivityName').value = period.activity_name || '';
    
    // Format YYYY-MM-DD
    if (period.start_date) {
        document.getElementById('editStartDate').value = period.start_date.substring(0, 10);
    }
    if (period.end_date) {
        document.getElementById('editEndDate').value = period.end_date.substring(0, 10);
    }
    
    document.getElementById('editIsActive').checked = Boolean(period.is_active);
    document.getElementById('editDescription').value = period.description || '';
    
    const modal = new bootstrap.Modal(document.getElementById('editPeriodModal'));
    modal.show();
}
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Dashboard Monitoring & Evaluasi - SIMPLE BPS')
@section('page_title', 'Dashboard Monitoring')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Dashboard Monitoring</li>
@endsection

@push('styles')
<style>
/* ===================================================
   BPS MODERN BENTO DASHBOARD STYLES (CALMLY INSPIRATION)
   =================================================== */
:root {
    --bps-orange: #F37021;
    --bps-orange-hover: #D95D12;
    --bps-orange-dark: #C2410C;
    --bps-orange-soft: #FFF4ED;
    --bps-orange-light: #FFEDE0;
    --bps-orange-glow: rgba(243, 112, 33, 0.18);
    --bps-blue: #005BAC;
    --bps-blue-soft: #EFF6FF;
    --bps-green: #009E49;
    --bps-green-soft: #F0FDF4;
    --bps-purple: #7C3AED;
    --bps-purple-soft: #FAF5FF;
    --bps-amber: #D97706;
    --bps-amber-soft: #FEF3C7;
    --bps-radius-card: 22px;
    --bps-radius-pill: 16px;
    --bps-card-shadow: 0 10px 28px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(15, 23, 42, 0.02);
    --bps-card-hover-shadow: 0 16px 36px rgba(243, 112, 33, 0.12), 0 2px 6px rgba(15, 23, 42, 0.04);
}

.bento-dashboard-wrapper {
    max-width: 1400px;
    margin: 0 auto;
    padding-bottom: 2rem;
}

/* Greeting Header */
.bento-greeting-wrap {
    margin-bottom: 1.5rem;
}
.bento-greeting-title {
    font-size: 1.75rem;
    font-weight: 800;
    color: #0F172A;
    letter-spacing: -0.02em;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}
.bento-greeting-sub {
    color: #64748B;
    font-size: 0.9rem;
    margin-top: 0.25rem;
    font-weight: 500;
}
.bento-role-pill {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    padding: 0.45rem 0.9rem;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 600;
    color: #334155;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

/* Base Bento Card */
.bento-card {
    background: #FFFFFF;
    border-radius: var(--bps-radius-card);
    border: 1px solid rgba(226, 232, 240, 0.85);
    box-shadow: var(--bps-card-shadow);
    padding: 1.5rem;
    transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}
.bento-card:hover {
    box-shadow: var(--bps-card-hover-shadow);
    transform: translateY(-2px);
}

/* Hero Card (Left Top) */
.bento-hero-card {
    background: linear-gradient(135deg, #FFF7F0 0%, #FFFFFF 50%, #FFF2E8 100%);
    border: 1px solid rgba(243, 112, 33, 0.25);
    min-height: 220px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.bento-hero-bg-art {
    position: absolute;
    right: -25px;
    bottom: -25px;
    width: 180px;
    height: 180px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(243, 112, 33, 0.12) 0%, rgba(243, 112, 33, 0) 70%);
    pointer-events: none;
}
.bento-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--bps-orange);
    background: rgba(243, 112, 33, 0.1);
    padding: 0.3rem 0.75rem;
    border-radius: 999px;
}
.bento-hero-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: #1E293B;
    line-height: 1.3;
    margin: 0.75rem 0 0.5rem 0;
    letter-spacing: -0.015em;
}
.bento-hero-desc {
    color: #475569;
    font-size: 0.88rem;
    line-height: 1.5;
    margin-bottom: 1.25rem;
    max-width: 90%;
}
.bento-hero-cta {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    background: var(--bps-orange);
    color: #FFFFFF !important;
    font-size: 0.88rem;
    font-weight: 700;
    padding: 0.65rem 1.4rem;
    border-radius: 999px;
    border: none;
    box-shadow: 0 4px 16px rgba(243, 112, 33, 0.35);
    text-decoration: none;
    transition: all 0.2s ease;
    align-self: flex-start;
}
.bento-hero-cta:hover {
    background: var(--bps-orange-hover);
    transform: translateY(-1px) scale(1.02);
    box-shadow: 0 6px 20px rgba(243, 112, 33, 0.45);
}

/* Today's Progress Card (Right Top) */
.bento-progress-card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.bento-card-header-sm {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}
.bento-card-title-sm {
    font-size: 0.95rem;
    font-weight: 800;
    color: #0F172A;
    margin: 0;
}
.bento-card-link-sm {
    font-size: 0.76rem;
    font-weight: 600;
    color: var(--bps-orange);
    text-decoration: none;
    transition: color 0.15s ease;
}
.bento-card-link-sm:hover {
    color: var(--bps-orange-hover);
    text-decoration: underline;
}

.bento-progress-body {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    flex-wrap: nowrap;
}
.bento-ring-wrap {
    width: 104px;
    height: 104px;
    flex-shrink: 0;
    position: relative;
}
.bento-ring-svg {
    transform: rotate(-90deg);
    width: 100%;
    height: 100%;
}
.bento-ring-track {
    stroke: #FFEADB;
    stroke-width: 8;
    fill: none;
}
.bento-ring-indicator {
    stroke: var(--bps-orange);
    stroke-width: 8;
    stroke-linecap: round;
    fill: none;
    transition: stroke-dashoffset 0.8s ease-in-out;
}
.bento-ring-label {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
}
.bento-ring-percent {
    font-size: 1.35rem;
    font-weight: 800;
    color: #0F172A;
    line-height: 1;
}
.bento-ring-subtext {
    font-size: 0.62rem;
    font-weight: 700;
    color: #94A3B8;
    letter-spacing: 0.05em;
    margin-top: 2px;
}
.bento-progress-info {
    flex-grow: 1;
}
.bento-progress-headline {
    font-size: 0.95rem;
    font-weight: 700;
    color: #0F172A;
    margin-bottom: 0.25rem;
}
.bento-progress-details {
    font-size: 0.82rem;
    color: #64748B;
    line-height: 1.4;
    margin: 0;
}

/* Filter Toolbar Bento Style */
.bento-filter-card {
    background: #FFFFFF;
    border-radius: var(--bps-radius-pill);
    border: 1px solid #E2E8F0;
    padding: 1rem 1.25rem;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.02);
    margin-bottom: 1.25rem;
}
</style>
@endpush

@section('content')
<div class="bento-dashboard-wrapper">

    <!-- ==============================================================
         1. TOP GREETING HEADER & QUICK LINKS
         ============================================================== -->
    <div class="bento-greeting-wrap d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            @php
                $hour = (int) now()->format('H');
                $greetingTime = $hour < 11 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
            @endphp
            <h1 class="bento-greeting-title mb-0">
                {{ $greetingTime }}, Administrator BPS 📊
            </h1>
            <p class="bento-greeting-sub mb-0">
                Monitoring Evaluasi Lapangan, Kepatuhan Petugas, &amp; Rekapitulasi Realisasi per {{ now()->translatedFormat('d F Y') }}.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="bento-role-pill d-none d-md-inline-flex">
                <span class="badge rounded-circle p-1" style="background: var(--bps-orange);"><i class="fa-solid fa-shield-halved text-white" style="font-size: 0.6rem;"></i></span>
                <span>BPS Kab. Kolaka Utara &bull; Portal Monitoring</span>
            </span>
            <a href="{{ route('admin.recapitulation.pdf') }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5" title="Unduh Rekapitulasi PDF">
                <i class="fa-solid fa-file-pdf me-1"></i> Cetak PDF
            </a>
            <a href="{{ route('admin.reports.index', ['status' => 'submitted']) }}" class="btn btn-sm rounded-pill px-3 py-1.5 text-white" style="background: var(--bps-orange); font-weight: 600;">
                <i class="fa-solid fa-clipboard-check me-1"></i> Verifikasi Laporan
                @if(($reportsPending ?? 0) > 0)
                    <span class="badge bg-white text-dark ms-1">{{ $reportsPending }}</span>
                @endif
            </a>
        </div>
    </div>

    <!-- ==============================================================
         2. NOTIFIKASI LAPORAN PERJADIN BARU / MENUNGGU ACC ADMIN
         ============================================================== -->
    @if(isset($incomingApprovalReports) && $incomingApprovalReports->isNotEmpty())
        <div class="bento-card mb-4 p-3 border-warning border-opacity-50" style="background: #FFFDF5;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill">
                        <i class="fa-solid fa-bell me-1"></i> {{ $incomingApprovalReports->count() }} Laporan Masuk
                    </span>
                    <span class="fw-bold text-dark small">
                        Laporan Perjadin Petugas Menunggu Pemeriksaan / ACC Admin
                    </span>
                </div>
                <a href="{{ route('admin.reports.index', ['status' => 'submitted']) }}" class="bento-card-link-sm">
                    Kelola Semua Laporan &rarr;
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0" style="font-size: 0.78rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-2">Petugas Lapangan</th>
                            <th>Judul &amp; No. Laporan</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th class="text-end pe-2">Aksi Cepat Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($incomingApprovalReports->take(5) as $incRep)
                            <tr>
                                <td class="ps-2 fw-semibold text-dark">{{ $incRep->user ? $incRep->user->name : ($incRep->officer_name ?? '-') }}</td>
                                <td>
                                    <span class="fw-bold" style="color: var(--bps-orange);">{{ $incRep->activity_title }}</span>
                                    <span class="text-muted font-monospace ms-1">({{ $incRep->report_number ?? 'LAP-'.$incRep->id }})</span>
                                </td>
                                <td class="text-muted text-nowrap">
                                    {{ $incRep->activity_date ? $incRep->activity_date->format('d/m/Y') : '-' }}
                                    <small class="d-block text-muted" style="font-size: 0.68rem;">{{ $incRep->created_at?->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $incRep->status_badge }}">{{ $incRep->status_label }}</span>
                                </td>
                                <td class="text-end pe-2 text-nowrap">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.reports.index', ['inspect' => $incRep->id]) }}"
                                           class="btn btn-xs btn-outline-info py-0.5 px-2 rounded-pill" style="font-size: 0.72rem;" title="Lihat Laporan">
                                            <i class="fa-solid fa-eye me-1"></i> Lihat
                                        </a>
                                        @if(auth()->user()->isAdmin())
                                            <a href="{{ route('admin.reports.index', ['edit' => $incRep->id]) }}"
                                               class="btn btn-xs btn-outline-warning text-dark py-0.5 px-2 rounded-pill" style="font-size: 0.72rem;" title="Edit Inputan Laporan">
                                                <i class="fa-regular fa-pen-to-square me-1"></i> Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.reports.approve', $incRep) }}" class="d-inline" onsubmit="return confirm('ACC (Setujui) laporan {{ addslashes($incRep->activity_title) }} agar petugas dapat mengunduh PDF?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-xs btn-success py-0.5 px-2.5 rounded-pill" style="font-size: 0.72rem;" title="ACC / Setujui Laporan">
                                                    <i class="fa-solid fa-check-double me-1"></i> ACC
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ==============================================================
         3. BENTO FILTER TOOLBAR (KOMPAK & ELEGAN)
         ============================================================== -->
    <div class="bento-filter-card">
        <form method="GET" action="{{ route('admin.dashboard') }}" id="filterMonitoringForm" class="row g-2 align-items-center">
            <!-- Filter Kegiatan -->
            <div class="col-xl-3 col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white text-muted border-end-0">
                        <i class="fa-solid fa-briefcase" style="color: var(--bps-orange);"></i>
                    </span>
                    <select name="assignment_id" class="form-select form-select-sm border-start-0 ps-1" onchange="this.form.submit()">
                        <option value="">Semua Kegiatan BPS</option>
                        @foreach($assignmentsList as $asg)
                            <option value="{{ $asg->id }}" {{ $selectedAssignmentId == $asg->id ? 'selected' : '' }}>
                                {{ Str::limit($asg->title, 28) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Filter Kecamatan -->
            <div class="col-xl-3 col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white text-muted border-end-0">
                        <i class="fa-solid fa-map-location-dot text-danger"></i>
                    </span>
                    <select name="district" class="form-select form-select-sm border-start-0 ps-1" onchange="this.form.submit()">
                        <option value="">Semua Kecamatan (15 Kec)</option>
                        @foreach($districtsList as $dist)
                            <option value="{{ $dist }}" {{ $selectedDistrict == $dist ? 'selected' : '' }}>
                                {{ $dist }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Filter Petugas Lapangan / PPL -->
            <div class="col-xl-3 col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white text-muted border-end-0">
                        <i class="fa-solid fa-user-gear text-primary"></i>
                    </span>
                    <select name="officer_id" class="form-select form-select-sm border-start-0 ps-1" onchange="this.form.submit()">
                        <option value="">Semua Petugas / PPL ({{ $allOfficersDropdown->count() }})</option>
                        @foreach($allOfficersDropdown as $officer)
                            <option value="{{ $officer->id }}" {{ $selectedOfficerId == $officer->id ? 'selected' : '' }}>
                                {{ $officer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Filter Status Kinerja & Reset -->
            <div class="col-xl-3 col-md-6">
                <div class="d-flex gap-1.5">
                    <select name="progress_status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ $progressFilter === 'all' ? 'selected' : '' }}>Semua Status Capaian</option>
                        <option value="slow" {{ $progressFilter === 'slow' ? 'selected' : '' }}>🔴 Perlu Follow-Up (&lt;50%)</option>
                        <option value="on_track" {{ $progressFilter === 'on_track' ? 'selected' : '' }}>🟡 On Track (50-99%)</option>
                        <option value="completed" {{ $progressFilter === 'completed' ? 'selected' : '' }}>🟢 Selesai (100%)</option>
                    </select>

                    @if($selectedAssignmentId || $selectedDistrict || $selectedOfficerId || $progressFilter !== 'all' || $reportFilter !== 'all')
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center rounded-pill px-2.5" title="Reset Semua Filter">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        @if($selectedAssignmentId || $selectedDistrict || $selectedOfficerId || $progressFilter !== 'all' || $reportFilter !== 'all')
            <div class="mt-2 pt-2 border-top d-flex flex-wrap align-items-center gap-1.5" style="font-size: 0.75rem;">
                <span class="text-muted fw-semibold">Filter Aktif:</span>
                @if($selectedDistrict)
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle py-1 px-2 rounded-pill">
                        <i class="fa-solid fa-location-dot me-1"></i> {{ $selectedDistrict }}
                    </span>
                @endif
                @if($selectedOfficerId)
                    @php $currOff = $allOfficersDropdown->firstWhere('id', $selectedOfficerId); @endphp
                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle py-1 px-2 rounded-pill">
                        <i class="fa-solid fa-user me-1"></i> {{ $currOff ? $currOff->name : 'Petugas #' . $selectedOfficerId }}
                    </span>
                @endif
                @if($progressFilter === 'slow')
                    <span class="badge bg-danger text-white py-1 px-2 rounded-pill">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Lambat / Perlu Follow-Up
                    </span>
                @elseif($progressFilter === 'completed')
                    <span class="badge bg-success text-white py-1 px-2 rounded-pill">
                        <i class="fa-solid fa-circle-check me-1"></i> Selesai 100%
                    </span>
                @endif
                <a href="{{ route('admin.dashboard') }}" class="text-danger ms-auto text-decoration-none small">
                    <i class="fa-solid fa-xmark me-1"></i> Reset Filter
                </a>
            </div>
        @endif
    </div>

    <!-- ==============================================================
         4. TOP ROW: HERO SUMMARY (LEFT) & TODAY'S PROGRESS (RIGHT)
         ============================================================== -->
    <div class="row g-3 mb-4">
        <!-- Hero Card (Left ~60%) -->
        <div class="col-lg-7">
            <div class="bento-card bento-hero-card h-100">
                <div class="bento-hero-bg-art"></div>
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="bento-hero-badge">
                            <i class="fa-solid fa-chart-line"></i> Ringkasan Monitoring Wilayah
                        </span>
                        @if($slowOfficersCount > 0)
                            <span class="badge bg-danger text-white px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $slowOfficersCount }} Petugas Lambat (&lt;50%)
                            </span>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                                <i class="fa-solid fa-circle-check me-1"></i> Seluruh Petugas On Track
                            </span>
                        @endif
                    </div>

                    @php
                        $activeAssignment = ($selectedAssignment ?? null) ?: ($selectedAssignmentId ? ($assignmentsList->firstWhere('id', $selectedAssignmentId) ?? null) : null);
                        $activeTitle = $activeAssignment ? $activeAssignment->title : 'Capaian Sensus & Survei BPS Kab. Kolaka Utara';
                        $activeLoc = $selectedDistrict ? "Kecamatan $selectedDistrict" : ($activeAssignment ? $activeAssignment->location : 'Wilayah Kabupaten Kolaka Utara');
                    @endphp

                    <h2 class="bento-hero-title">
                        {{ $activeTitle }}
                    </h2>
                    <p class="bento-hero-desc">
                        <i class="fa-solid fa-location-dot text-danger me-1"></i> <strong>{{ $activeLoc }}</strong> &bull; Total Realisasi: <strong>{{ number_format($bastCompletedCount) }}</strong> dari <strong>{{ number_format($bastTargetCount) }}</strong> responden telah memiliki Berita Acara (BAST).
                        @if($slowOfficersCount > 0)
                            <span class="d-block text-danger mt-1 fw-semibold"><i class="fa-solid fa-circle-exclamation me-1"></i> Tindakan diperlukan: Ingatkan petugas yang masih di bawah target kuota melalui tombol WhatsApp.</span>
                        @endif
                    </p>
                </div>

                <div class="mt-2 d-flex flex-wrap gap-2 align-items-center">
                    <a href="#tabelPetugas" class="bento-hero-cta">
                        <i class="fa-solid fa-user-gear"></i>
                        <span>Pantau Kinerja Petugas</span>
                        <i class="fa-solid fa-arrow-down small ms-1"></i>
                    </a>
                    <a href="{{ route('admin.recapitulation.excel') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 font-weight-bold" style="font-size: 0.88rem; background: #FFFFFF;">
                        <i class="fa-solid fa-file-excel text-success me-1"></i> Unduh Excel
                    </a>
                </div>
            </div>
        </div>

        <!-- Today's Progress Ring Card (Right ~40%) -->
        <div class="col-lg-5">
            <div class="bento-card bento-progress-card h-100">
                <div class="bento-card-header-sm">
                    <h5 class="bento-card-title-sm">
                        <i class="fa-solid fa-chart-pie me-1.5" style="color: var(--bps-orange);"></i> Realisasi BAST Kabupaten
                    </h5>
                    <a href="{{ route('admin.basts.respondents.index') }}" class="bento-card-link-sm">
                        Kelola BAST &rarr;
                    </a>
                </div>

                @php
                    $clampedBastPct = min(100, max(0, (float) $bastPercentage));
                    $donutBastOffset = 251.327 - (251.327 * ($clampedBastPct / 100));
                @endphp

                <div class="bento-progress-body my-auto py-2">
                    <div class="bento-ring-wrap">
                        <svg class="bento-ring-svg" viewBox="0 0 100 100">
                            <circle class="bento-ring-track" cx="50" cy="50" r="40" />
                            <circle class="bento-ring-indicator" cx="50" cy="50" r="40"
                                    stroke-dasharray="251.327"
                                    stroke-dashoffset="{{ $donutBastOffset }}" />
                        </svg>
                        <div class="bento-ring-label">
                            <span class="bento-ring-percent">{{ $bastPercentage }}%</span>
                            <span class="bento-ring-subtext">REALISASI</span>
                        </div>
                    </div>

                    <div class="bento-progress-info">
                        <div class="bento-progress-headline">
                            @if($bastPercentage >= 100)
                                <span class="text-success"><i class="fa-solid fa-circle-check me-1"></i> Target Tercapai (100%) 🎉</span>
                            @elseif($bastPercentage >= 50)
                                <span class="text-dark"><i class="fa-solid fa-fire text-warning me-1"></i> Progres On Track</span>
                            @else
                                <span class="text-danger"><i class="fa-solid fa-hourglass-half me-1"></i> Perlu Akselerasi</span>
                            @endif
                        </div>
                        <p class="bento-progress-details">
                            <strong>{{ number_format($bastCompletedCount) }}</strong> dari <strong>{{ number_format($bastTargetCount) }}</strong> target responden telah memiliki Berita Acara sah.
                            <span class="d-block text-muted mt-1" style="font-size: 0.76rem;">
                                Sisa <strong>{{ number_format($bastPendingCount) }}</strong> responden belum menyelesaikan tanda terima.
                            </span>
                        </p>
                    </div>
                </div>

                <div class="pt-2 border-top mt-2 d-flex justify-content-between align-items-center small text-muted" style="font-size: 0.75rem;">
                    <span><i class="fa-solid fa-users me-1"></i> {{ $totalOfficers }} Petugas Terdaftar</span>
                    <span class="fw-semibold text-dark">{{ $totalBasts }} Total Dokumen BAST</span>
                </div>
            </div>
        </div>
    </div>



    <!-- ==============================================================
         6. TWO COLUMNS BENTO: REKAP KECAMATAN (LEFT) & KEPATUHAN LAPORAN (RIGHT)
         ============================================================== -->
    <div class="row g-4 mb-4">
        <!-- 1. Ringkasan Progres per Kecamatan (col-lg-6) -->
        <div class="col-lg-6">
            <div class="bento-card h-100 d-flex flex-column">
                <div class="bento-card-header-sm">
                    <h5 class="bento-card-title-sm">
                        <i class="fa-solid fa-map-marked-alt text-danger me-1.5"></i> Rekapitulasi Capaian per Kecamatan
                    </h5>
                    <span class="badge bg-light text-secondary border" style="font-size: 0.72rem;">
                        {{ count($districtSummaries) }} Kecamatan
                    </span>
                </div>

                <div class="d-flex flex-column gap-2" style="max-height: 290px; overflow-y: auto;">
                    @forelse($districtSummaries as $dSum)
                        <div class="p-2.5 border rounded-3 bg-white hover-shadow transition">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['district' => $dSum['name']])) }}" 
                                   class="fw-bold text-dark text-decoration-none"
                                   style="font-size: 0.82rem;"
                                   title="Filter kecamatan {{ $dSum['name'] }}">
                                    <i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $dSum['name'] }}
                                </a>
                                <span class="badge {{ $dSum['percentage'] >= 75 ? 'bg-success-subtle text-success' : ($dSum['percentage'] >= 50 ? 'bg-warning-subtle text-warning-emphasis' : 'bg-danger-subtle text-danger') }} font-monospace" style="font-size: 0.7rem;">
                                    {{ $dSum['percentage'] }}%
                                </span>
                            </div>

                            <div class="progress mb-1" style="height: 6px; border-radius: 999px;">
                                <div class="progress-bar" 
                                     style="width: {{ $dSum['percentage'] }}%; background: {{ $dSum['percentage'] >= 75 ? 'var(--bps-green)' : ($dSum['percentage'] >= 50 ? 'var(--bps-orange)' : '#EF4444') }};"></div>
                            </div>

                            <div class="d-flex justify-content-between text-muted" style="font-size: 0.72rem;">
                                <span>Target: <strong>{{ $dSum['total'] }}</strong></span>
                                <span>BAST: <strong class="text-success">{{ $dSum['completed'] }}</strong></span>
                                <span>Sisa: <strong class="text-danger">{{ $dSum['pending'] }}</strong></span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            <i class="fa-solid fa-map-location-dot fa-2x mb-1 text-secondary opacity-50 d-block"></i>
                            Tidak ada data kecamatan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 2. Monitoring Kepatuhan Laporan Petugas (col-lg-6) -->
        <div class="col-lg-6">
            <div class="bento-card h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="bento-card-header-sm">
                        <h5 class="bento-card-title-sm">
                            <i class="fa-solid fa-clipboard-user text-primary me-1.5"></i> Kepatuhan Laporan Perjadin Petugas
                        </h5>
                        <span class="badge {{ $reportSubmissionRate >= 75 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis' }} border" style="font-size: 0.72rem;">
                            {{ $reportSubmissionRate }}% Kepatuhan
                        </span>
                    </div>

                    <div class="row g-2 mb-3 text-center">
                        <div class="col-6">
                            <div class="p-2.5 rounded-3 bg-light border">
                                <span class="d-block text-muted small" style="font-size: 0.72rem;">Sudah Menyerahkan</span>
                                <span class="fs-5 fw-bold text-success font-monospace">{{ $officersWithReportCount }}</span>
                                <span class="text-muted small">/ {{ $totalOfficersCount }} Petugas</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2.5 rounded-3 bg-light border">
                                <span class="d-block text-muted small" style="font-size: 0.72rem;">Belum Lapor</span>
                                <span class="fs-5 fw-bold text-danger font-monospace">{{ $officersWithoutReportCount }}</span>
                                <span class="text-muted small">Petugas Lapangan</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-dark small" style="font-size: 0.78rem;">
                            Petugas Belum Menyerahkan Laporan:
                        </span>
                        <div class="d-flex gap-2">
                            @if(auth()->user()->isAdmin() && $officersWithoutReports->count() > 0)
                                <button type="button" 
                                        class="btn btn-xs rounded-pill text-white py-0.5 px-2.5" 
                                        style="font-size: 0.7rem; background: var(--bps-orange);"
                                        title="Kirim pesan massal ke seluruh petugas yang belum lapor"
                                        onclick="openSendNoticeModal('all_without_reports', 'Semua Petugas Belum Lapor ({{ $officersWithoutReports->count() }} Orang)', 'report')">
                                    <i class="fa-solid fa-paper-plane me-1"></i> Ingatkan Semua
                                </button>
                            @endif
                            <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['report_status' => 'not_submitted'])) }}" class="bento-card-link-sm" style="font-size: 0.72rem;">
                                Lihat Semua
                            </a>
                        </div>
                    </div>

                    <div style="max-height: 140px; overflow-y: auto;">
                        @forelse($officersWithoutReports->take(5) as $offWithout)
                            <div class="d-flex align-items-center justify-content-between py-1.5 px-2.5 border-bottom bg-white rounded-3 mb-1">
                                <div class="overflow-hidden me-2">
                                    <span class="fw-bold text-dark d-block text-truncate" style="font-size: 0.78rem;">{{ $offWithout->name }}</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">
                                        <i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $offWithout->dominant_district }} &bull; BAST: {{ $offWithout->completed }}/{{ $offWithout->target }}
                                    </small>
                                </div>
                                @if(auth()->user()->isAdmin())
                                    <div class="flex-shrink-0">
                                        <button type="button" 
                                                class="btn btn-xs btn-outline-primary py-0.5 px-2 rounded-pill" 
                                                style="font-size: 0.7rem;" 
                                                title="Kirim pesan ke papan informasi petugas" 
                                                onclick="openSendNoticeModal({{ $offWithout->id }}, '{{ addslashes($offWithout->name) }}', 'report')">
                                            <i class="fa-solid fa-bullhorn me-1"></i> Ingatkan
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-2 text-success small" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-circle-check me-1"></i> Seluruh petugas telah mengumpulkan laporan perjalanan dinas!
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="pt-2 border-top mt-2 d-flex justify-content-between align-items-center small text-muted" style="font-size: 0.75rem;">
                    <span><i class="fa-solid fa-file-invoice me-1"></i> Status Verifikasi Admin</span>
                    <span class="fw-semibold text-dark">{{ $reportsApproved }} Laporan Telah di-ACC</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ==============================================================
         7. TABEL MONITORING KINERJA PETUGAS & IDENTIFIKASI PETUGAS LAMBAT
         ============================================================== -->
    <div class="bento-card mb-4 p-0" id="tabelPetugas">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-3 border-bottom bg-light bg-opacity-40">
            <div>
                <span class="fw-bold text-dark d-block" style="font-size: 0.95rem;">
                    <i class="fa-solid fa-user-gear me-1.5" style="color: var(--bps-orange);"></i> Identifikasi Kinerja Petugas Lapangan / PPL
                </span>
                <span class="text-muted" style="font-size: 0.75rem;">Urutan prioritas: petugas dengan progres terendah berada di paling atas.</span>
            </div>

            <!-- Quick Filter Buttons -->
            <div class="d-flex flex-wrap gap-1.5">
                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['progress_status' => 'all'])) }}" 
                   class="btn btn-xs rounded-pill {{ $progressFilter === 'all' ? 'btn-dark' : 'btn-outline-secondary' }}" style="font-size: 0.72rem; padding: 3px 10px;">
                    Semua ({{ count($filteredOfficersList) }})
                </a>
                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['progress_status' => 'slow'])) }}" 
                   class="btn btn-xs rounded-pill {{ $progressFilter === 'slow' ? 'btn-danger' : 'btn-outline-danger' }}" style="font-size: 0.72rem; padding: 3px 10px;">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Lambat ({{ $slowOfficersCount }})
                </a>
                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['progress_status' => 'on_track'])) }}" 
                   class="btn btn-xs rounded-pill {{ $progressFilter === 'on_track' ? 'btn-warning' : 'btn-outline-warning' }}" style="font-size: 0.72rem; padding: 3px 10px;">
                    On Track ({{ $onTrackOfficersCount }})
                </a>
                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['progress_status' => 'completed'])) }}" 
                   class="btn btn-xs rounded-pill {{ $progressFilter === 'completed' ? 'btn-success' : 'btn-outline-success' }}" style="font-size: 0.72rem; padding: 3px 10px;">
                    Selesai ({{ $completedOfficersCount }})
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table sp-table align-middle mb-0" style="font-size: 0.8125rem;">
                <thead class="table-light">
                    <tr>
                        <th style="min-width: 170px;">Petugas Lapangan / PPL</th>
                        <th style="min-width: 120px;">Wilayah Kerja</th>
                        <th class="text-center" style="width: 70px;">Target</th>
                        <th class="text-center" style="width: 70px;">BAST</th>
                        <th class="text-center" style="width: 70px;">Sisa</th>
                        <th style="min-width: 130px;">Progres</th>
                        <th style="min-width: 120px;">Status Kinerja</th>
                        <th class="text-center" style="width: 90px;">Laporan</th>
                        <th class="text-end pe-3" style="min-width: 110px;">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filteredOfficersList as $off)
                        <tr class="{{ $off->category === 'slow' ? 'bg-danger-subtle bg-opacity-25' : '' }}">
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold {{ $off->category === 'slow' ? 'bg-danger' : ($off->category === 'completed' ? 'bg-success' : 'bg-primary') }}" 
                                         style="width: 32px; height: 32px; font-size: 0.78rem; flex-shrink: 0;">
                                        {{ strtoupper(substr($off->name, 0, 1)) }}
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.8rem;">{{ $off->name }}</div>
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">{{ $off->phone ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border rounded-pill" style="font-size: 0.7rem;">
                                    {{ $off->dominant_district }}
                                </span>
                            </td>
                            <td class="text-center font-monospace fw-semibold">{{ $off->target }}</td>
                            <td class="text-center font-monospace fw-bold text-success">{{ $off->completed }}</td>
                            <td class="text-center font-monospace fw-bold {{ $off->pending > 0 ? 'text-danger' : 'text-muted' }}">
                                {{ $off->pending }}
                            </td>
                            <td>
                                <div class="d-flex justify-content-between text-muted mb-1 font-monospace" style="font-size: 0.68rem;">
                                    <span>{{ $off->completed }}/{{ $off->target }}</span>
                                    <span class="fw-bold text-dark">{{ $off->percentage }}%</span>
                                </div>
                                <div class="progress" style="height: 5px; border-radius: 999px;">
                                    <div class="progress-bar" 
                                         style="width: {{ $off->percentage }}%; background: {{ $off->percentage >= 100 ? 'var(--bps-green)' : ($off->percentage >= 50 ? 'var(--bps-orange)' : '#EF4444') }};"></div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $off->badge_class }} py-1 px-2 rounded-pill" style="font-size: 0.68rem;">
                                    @if($off->category === 'slow')
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                    @elseif($off->category === 'completed')
                                        <i class="fa-solid fa-check me-1"></i>
                                    @endif
                                    {{ $off->status_text }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($off->has_report)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.68rem;" title="Laporan sudah diserahkan">
                                        <i class="fa-solid fa-check me-1"></i> Ada ({{ $off->reports_count }})
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill" style="font-size: 0.68rem;" title="Belum membuat laporan kegiatan">
                                        <i class="fa-solid fa-xmark me-1"></i> Belum
                                    </span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap pe-3">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    @if(auth()->user()->isAdmin())
                                        <button type="button" 
                                                class="btn btn-xs rounded-pill py-1 px-2 text-white" 
                                                style="font-size: 0.72rem; background: var(--bps-orange);"
                                                title="Kirim Pesan ke Papan Informasi Petugas"
                                                onclick="openSendNoticeModal({{ $off->id }}, '{{ addslashes($off->name) }}', '{{ $off->has_report ? 'bast' : 'report' }}', '{{ $off->target }}', '{{ $off->completed }}', '{{ $off->pending }}', '{{ $off->percentage }}')">
                                            <i class="fa-solid fa-bullhorn me-1"></i> Notif
                                        </button>
                                    @endif

                                    <button type="button" 
                                            class="btn btn-xs btn-outline-secondary rounded-pill py-1 px-1.5" 
                                            style="font-size: 0.72rem;"
                                            title="Lihat Draf Pesan Notifikasi"
                                            onclick="openReminderModal('{{ $off->id }}', '{{ addslashes($off->name) }}', '{{ $off->phone ?? '-' }}', '{{ $off->target }}', '{{ $off->completed }}', '{{ $off->pending }}', '{{ $off->percentage }}', '{{ $off->has_report ? 'Sudah Diserahkan' : 'BELUM Diserahkan' }}', '{{ $off->wa_url ?? '' }}')">
                                        <i class="fa-regular fa-comment-dots"></i>
                                    </button>

                                    @if($off->wa_url)
                                        <a href="{{ $off->wa_url }}" 
                                           target="_blank" 
                                           class="btn btn-xs btn-outline-success rounded-pill py-1 px-1.5" 
                                           style="font-size: 0.72rem;"
                                           title="Kirim WhatsApp Langsung">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted small">
                                <i class="fa-solid fa-user-slash fa-2x mb-2 text-secondary opacity-50 d-block"></i>
                                Tidak ada data petugas lapangan yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ==============================================================
         8. GRAFIK TREN BULANAN & LOG AKTIVITAS (BENTO CARDS)
         ============================================================== -->
    <div class="row g-4 mb-2 align-items-stretch">
        <!-- Chart: Tren Penginputan Responden & BAST -->
        <div class="col-lg-8">
            <div class="bento-card h-100 d-flex flex-column">
                <div class="bento-card-header-sm">
                    <h5 class="bento-card-title-sm">
                        <i class="fa-solid fa-chart-column me-1.5" style="color: var(--bps-orange);"></i> Tren Penginputan Responden &amp; BAST Terbit
                    </h5>
                    <span class="badge bg-light text-secondary border" style="font-size: 0.7rem;">6 Bulan Terakhir</span>
                </div>
                <div class="flex-grow-1 p-1">
                    <div style="position: relative; height: 220px; width: 100%;">
                        <canvas id="activityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Log Aktivitas Terkini -->
        <div class="col-lg-4">
            <div class="bento-card h-100 d-flex flex-column">
                <div class="bento-card-header-sm">
                    <h5 class="bento-card-title-sm">
                        <i class="fa-solid fa-bolt text-warning me-1.5"></i> Log Aktivitas Terkini
                    </h5>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.logs.index') }}" class="bento-card-link-sm">Semua &rarr;</a>
                    @endif
                </div>
                <div class="flex-grow-1" style="max-height: 235px; overflow-y: auto;">
                    <div class="list-group list-group-flush">
                        @forelse($recentActivities as $log)
                            <div class="list-group-item py-2 px-1 border-bottom">
                                <div class="d-flex w-100 justify-content-between align-items-center mb-0.5">
                                    <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.65rem;">
                                        {{ strtoupper($log->module) }}
                                    </span>
                                    <small class="text-muted" style="font-size: 0.65rem;">{{ $log->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-0 small text-dark lh-sm text-truncate" style="font-size: 0.74rem;" title="{{ $log->description }}">{{ $log->description }}</p>
                                <small class="text-muted" style="font-size: 0.67rem;">Oleh: <strong class="text-secondary">{{ $log->user ? $log->user->name : 'Sistem' }}</strong></small>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted small">
                                <i class="fa-regular fa-clock fa-2x mb-1 text-secondary opacity-50 d-block"></i>
                                Belum ada aktivitas tercatat.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ==============================================================
     MODAL PREVIEW & SALIN PESAN NOTIFIKASI / REMINDER FOLLOW-UP
     ============================================================== -->
<div class="modal fade" id="reminderFollowUpModal" tabindex="-1" aria-labelledby="reminderFollowUpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom py-2.5 px-3">
                <h6 class="modal-title fw-bold" id="reminderFollowUpModalLabel">
                    <i class="fa-solid fa-paper-plane text-primary me-1"></i> Draf Pesan Notifikasi Petugas
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-0.5">
                        <span class="text-muted small fw-semibold">Penerima:</span>
                        <span class="badge bg-light text-dark border font-monospace" id="modal_officer_phone"></span>
                    </div>
                    <h6 class="fw-bold text-dark mb-0" id="modal_officer_name"></h6>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold text-secondary mb-1">TEKS NOTIFIKASI RESMI</label>
                    <textarea class="form-control font-monospace small bg-light" id="modal_reminder_text" rows="6" readonly style="font-size: 0.75rem;"></textarea>
                </div>

                <div class="alert alert-info py-1.5 px-2.5 small mb-0 d-flex align-items-center gap-2" style="font-size: 0.75rem;">
                    <i class="fa-solid fa-circle-info text-info"></i>
                    <div>Salin pesan atau langsung kirimkan via WhatsApp resmi petugas.</div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2 px-3 d-flex justify-content-between flex-wrap gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="copyReminderText()">
                    <i class="fa-regular fa-copy me-1"></i> <span id="btnCopyText">Salin Teks</span>
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill" data-bs-dismiss="modal">Tutup</button>
                    @if(auth()->user()->isAdmin())
                        <button type="button" class="btn btn-sm rounded-pill text-white" style="background: var(--bps-orange);" onclick="transferReminderToNoticeBoard()">
                            <i class="fa-solid fa-bullhorn me-1"></i> Kirim ke Papan Informasi
                        </button>
                    @endif
                    <a href="#" id="modal_wa_direct_link" target="_blank" class="btn btn-sm btn-outline-success rounded-pill">
                        <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->isAdmin())
<!-- ==============================================================
     MODAL KIRIM PESAN KE PAPAN INFORMASI PETUGAS
     ============================================================== -->
<div class="modal fade" id="sendOfficerNoticeModal" tabindex="-1" aria-labelledby="sendOfficerNoticeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom py-2.5 px-3">
                <h6 class="modal-title fw-bold text-dark" id="sendOfficerNoticeModalLabel">
                    <i class="fa-solid fa-bullhorn me-1.5" style="color: var(--bps-orange);"></i> Kirim Pesan ke Papan Informasi Petugas
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.notices.send') }}" method="POST" id="formSendNotice">
                @csrf
                <input type="hidden" name="user_id" id="notice_user_id" value="">
                <input type="hidden" name="type" id="notice_type" value="report_reminder">

                <div class="modal-body p-3">
                    <div class="mb-2.5 p-2 rounded bg-light border">
                        <span class="text-muted small d-block" style="font-size: 0.7rem;">Target Penerima:</span>
                        <div class="fw-bold text-dark d-flex align-items-center gap-1.5" id="notice_recipient_display">
                            <!-- Populated by JS -->
                        </div>
                    </div>

                    <div class="mb-2.5">
                        <label class="form-label small fw-bold text-secondary mb-1">JUDUL PEMBERITAHUAN <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="notice_title" class="form-control form-control-sm" required value="Peringatan: Belum Menyerahkan Laporan Perjalanan Dinas">
                    </div>

                    <div class="mb-2.5">
                        <label class="form-label small fw-bold text-secondary mb-1">ISI PESAN (TAMPIL DI TAMPILAN AWAL PETUGAS) <span class="text-danger">*</span></label>
                        <textarea name="message" id="notice_message" class="form-control form-control-sm font-monospace" rows="4" required placeholder="Tuliskan pesan atau instruksi untuk petugas lapangan..." style="font-size: 0.8rem;"></textarea>
                        <div class="form-text" style="font-size: 0.7rem;">
                            <i class="fa-solid fa-circle-info text-primary me-1"></i> Pesan ini akan langsung tampil di kotak <strong>"Informasi Terbaru"</strong> pada beranda HP/layar awal petugas yang bersangkutan.
                        </div>
                    </div>

                    <!-- Template Tombol Pilihan Cepat -->
                    <div class="d-flex gap-1.5 align-items-center flex-wrap pt-1">
                        <span class="text-muted" style="font-size: 0.68rem;">Pilihan Cepat:</span>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0.5 px-2 rounded-pill" style="font-size: 0.68rem;" onclick="setNoticePreset('report')">
                            Laporan Perjalanan Dinas
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0.5 px-2 rounded-pill" style="font-size: 0.68rem;" onclick="setNoticePreset('bast')">
                            Tanda Terima Responden
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0.5 px-2 rounded-pill" style="font-size: 0.68rem;" onclick="setNoticePreset('deadline')">
                            Batas Waktu (Deadline)
                        </button>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2 px-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm rounded-pill text-white" style="background: var(--bps-orange);" id="btnSubmitNotice">
                        <i class="fa-solid fa-paper-plane me-1"></i> Kirim ke Papan Informasi Petugas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let currentReminderUserId = null;
    let currentReminderOfficerName = '';
    let currentReminderMessage = '';

    function openReminderModal(userId, name, phone, target, completed, pending, percentage, reportStatus, waUrl) {
        currentReminderUserId = userId;
        currentReminderOfficerName = name;
        document.getElementById('modal_officer_name').innerText = name;
        document.getElementById('modal_officer_phone').innerText = phone || 'Nomor HP tidak ada';

        const message = `Halo Rekan ${name},\n\nMengingatkan terkait progres penugasan SIMPLE Anda saat ini:\n• Target Responden: ${target}\n• BAST Selesai: ${completed} (${percentage}%)\n• Sisa Belum BAST: ${pending}\n• Status Laporan Kegiatan: ${reportStatus}\n\nMohon untuk segera menyelesaikan kunjungan responden & penginputan BAST serta melengkapi Laporan Perjalanan Dinas. Terima kasih.`;
        currentReminderMessage = message;

        document.getElementById('modal_reminder_text').value = message;

        const waBtn = document.getElementById('modal_wa_direct_link');
        if (waUrl && waUrl !== '') {
            waBtn.href = waUrl;
            waBtn.classList.remove('disabled');
        } else {
            waBtn.href = '#';
            waBtn.classList.add('disabled');
        }

        const modal = new bootstrap.Modal(document.getElementById('reminderFollowUpModal'));
        modal.show();
    }

    function transferReminderToNoticeBoard() {
        const reminderModal = bootstrap.Modal.getInstance(document.getElementById('reminderFollowUpModal'));
        if (reminderModal) {
            reminderModal.hide();
        }
        openSendNoticeModal(currentReminderUserId, currentReminderOfficerName, 'report');
        if (currentReminderMessage) {
            document.getElementById('notice_message').value = currentReminderMessage;
        }
    }

    let currentNoticeOfficerName = '';
    function openSendNoticeModal(userId, officerName, presetType = 'report', target = null, completed = null, pending = null, percentage = null) {
        currentNoticeOfficerName = officerName;
        document.getElementById('notice_user_id').value = userId;
        document.getElementById('notice_recipient_display').innerHTML = `
            <i class="fa-solid fa-user-circle text-primary"></i>
            <span>${officerName}</span>
            ${userId === 'all_without_reports' ? '<span class="badge bg-danger ms-1">Broadcast Massal Seluruh Petugas Belum Lapor</span>' : '<span class="badge bg-light text-dark border ms-1">Papan Informasi Petugas</span>'}
        `;

        setNoticePreset(presetType, target, completed, pending, percentage);

        const modal = new bootstrap.Modal(document.getElementById('sendOfficerNoticeModal'));
        modal.show();
    }

    function setNoticePreset(presetType, target = null, completed = null, pending = null, percentage = null) {
        const titleInput = document.getElementById('notice_title');
        const messageInput = document.getElementById('notice_message');
        const typeInput = document.getElementById('notice_type');
        const name = currentNoticeOfficerName || 'Petugas Lapangan';

        if (presetType === 'report') {
            typeInput.value = 'report_reminder';
            titleInput.value = 'Peringatan: Belum Menyerahkan Laporan Perjalanan Dinas';
            messageInput.value = `Halo Rekan ${name},\n\nTercatat di sistem bahwa Anda belum menyerahkan Laporan Kegiatan / Perjalanan Dinas. Mohon untuk segera menyusun dan mengirimkan laporan kegiatan dinas lapangan Anda melalui menu Laporan agar dapat diverifikasi oleh admin. Terima kasih.`;
        } else if (presetType === 'bast') {
            typeInput.value = 'bast_reminder';
            titleInput.value = 'Pengingat: Penyelesaian Tanda Terima Responden';
            let detail = '';
            if (target && completed !== null) {
                detail = ` (Progres: ${completed}/${target} sampel, ${percentage}%)`;
            }
            messageInput.value = `Halo Rekan ${name},\n\nMohon dipercepat proses pendataan dan penerbitan formulir tanda terima responden lapangan Anda${detail}. Pastikan sampel responden yang telah dikunjungi dilengkapi dengan tanda tangan digital resmi. Terima kasih.`;
        } else if (presetType === 'deadline') {
            typeInput.value = 'general';
            titleInput.value = 'Pemberitahuan: Mendekati Batas Akhir (Deadline)';
            messageInput.value = `Halo Rekan ${name},\n\nMengingatkan bahwa batas waktu kegiatan lapangan sudah semakin dekat. Mohon maksimalkan sisa waktu untuk menyelesaikan seluruh target responden, BAST, dan laporan perjalanan dinas sebelum batas akhir. Terima kasih atas dedikasi dan kerja samanya.`;
        }
    }

    function copyReminderText() {
        const textarea = document.getElementById('modal_reminder_text');
        textarea.select();
        navigator.clipboard.writeText(textarea.value).then(() => {
            const btn = document.getElementById('btnCopyText');
            btn.innerText = 'Tersalin!';
            setTimeout(() => {
                btn.innerText = 'Salin Teks';
            }, 2000);
        });
    }

    const ctx = document.getElementById('activityChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($monthlyLabels),
                datasets: [
                    {
                        label: 'Data Responden',
                        data: @json($monthlyRespondents),
                        backgroundColor: '#F37021',
                        borderRadius: 6,
                    },
                    {
                        label: 'BAST Terbit',
                        data: @json($monthlyBasts),
                        backgroundColor: '#009E49',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: 'Plus Jakarta Sans', size: 11 },
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 6
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 50, precision: 0, font: { size: 10 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    }
</script>
@endpush

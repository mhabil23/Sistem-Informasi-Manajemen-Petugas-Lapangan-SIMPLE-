@extends('layouts.app')

@section('title', 'Dashboard Monitoring & Evaluasi - SIMPEL BPS')
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
         1. TOP GREETING HEADER
         ============================================================== -->
    <div class="bento-greeting-wrap mb-4">
        @php
            $hour = (int) now()->format('H');
            $greetingTime = $hour < 11 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
        @endphp
        <h1 class="bento-greeting-title mb-1">
            {{ $greetingTime }}, Administrator BPS 📊
        </h1>
        <p class="bento-greeting-sub mb-0">
            Monitoring Evaluasi Lapangan, Kepatuhan Petugas, &amp; Rekapitulasi Realisasi per {{ now()->translatedFormat('d F Y') }}.
        </p>
    </div>

    <!-- ==============================================================
         2. NOTIFIKASI LAPORAN PERJADIN BARU / MENUNGGU ACC ADMIN
         ============================================================== -->
    @if(isset($incomingApprovalReports) && $incomingApprovalReports->isNotEmpty())
        <div class="bento-card mb-4 p-3 border-warning border-opacity-50" style="background: #FFFDF5;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill">
                        <i class="bi bi-bell-fill me-1"></i> {{ $incomingApprovalReports->count() }} Laporan Masuk
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
                                            <i class="bi bi-eye me-1"></i> Lihat
                                        </a>
                                        @if(auth()->user()->isAdmin())
                                            <a href="{{ route('admin.reports.index', ['edit' => $incRep->id]) }}"
                                               class="btn btn-xs btn-outline-warning text-dark py-0.5 px-2 rounded-pill" style="font-size: 0.72rem;" title="Edit Inputan Laporan">
                                                <i class="bi bi-pencil-square me-1"></i> Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.reports.approve', $incRep) }}" class="d-inline" onsubmit="return confirm('ACC (Setujui) laporan {{ addslashes($incRep->activity_title) }} agar petugas dapat mengunduh PDF?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-xs btn-success py-0.5 px-2.5 rounded-pill" style="font-size: 0.72rem;" title="ACC / Setujui Laporan">
                                                    <i class="bi bi-check2-all me-1"></i> ACC
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
                        <i class="bi bi-briefcase-fill" style="color: var(--bps-orange);"></i>
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
                        <i class="bi bi-geo-alt-fill text-danger"></i>
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
                        <i class="bi bi-person-gear text-primary"></i>
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
                        <option value="slow" {{ $progressFilter === 'slow' ? 'selected' : '' }}>Perlu Follow-Up (&lt;50%)</option>
                        <option value="on_track" {{ $progressFilter === 'on_track' ? 'selected' : '' }}>On Track (50-99%)</option>
                        <option value="completed" {{ $progressFilter === 'completed' ? 'selected' : '' }}>Selesai (100%)</option>
                    </select>

                    @if($selectedAssignmentId || $selectedDistrict || $selectedOfficerId || $progressFilter !== 'all' || $reportFilter !== 'all')
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center rounded-pill px-2.5" title="Reset Semua Filter">
                            <i class="bi bi-arrow-clockwise"></i>
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
                        <i class="bi bi-geo-alt-fill me-1"></i> {{ $selectedDistrict }}
                    </span>
                @endif
                @if($selectedOfficerId)
                    @php $currOff = $allOfficersDropdown->firstWhere('id', $selectedOfficerId); @endphp
                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle py-1 px-2 rounded-pill">
                        <i class="bi bi-person me-1"></i> {{ $currOff ? $currOff->name : 'Petugas #' . $selectedOfficerId }}
                    </span>
                @endif
                @if($progressFilter === 'slow')
                    <span class="badge bg-danger text-white py-1 px-2 rounded-pill">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Lambat / Perlu Follow-Up
                    </span>
                @elseif($progressFilter === 'completed')
                    <span class="badge bg-success text-white py-1 px-2 rounded-pill">
                        <i class="bi bi-check-circle-fill me-1"></i> Selesai 100%
                    </span>
                @endif
                <a href="{{ route('admin.dashboard') }}" class="text-danger ms-auto text-decoration-none small">
                    <i class="bi bi-x-lg me-1"></i> Reset Filter
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
                            <i class="bi bi-graph-up-arrow"></i> Ringkasan Monitoring Wilayah
                        </span>
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
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> <strong>{{ $activeLoc }}</strong> &bull; Total Realisasi: <strong>{{ number_format($bastCompletedCount) }}</strong> dari <strong>{{ number_format($bastTargetCount) }}</strong> responden telah memiliki Berita Acara (BAST).
                    </p>
                </div>

                <div class="mt-2 d-flex flex-wrap gap-2 align-items-center">
                    <button type="button" class="btn btn-outline-success rounded-pill px-3 py-2 fw-bold" style="font-size: 0.88rem; background: #FFFFFF;" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                        <i class="bi bi-file-earmark-excel-fill text-success me-1"></i> Upload Excel Kegiatan &amp; Sampel
                    </button>
                    <a href="{{ route('admin.recapitulation.excel') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 font-weight-bold" style="font-size: 0.88rem; background: #FFFFFF;">
                        <i class="bi bi-file-earmark-spreadsheet-fill text-success me-1"></i> Unduh Excel
                    </a>
                </div>
            </div>
        </div>

        <!-- Today's Progress Ring Card (Right ~40%) -->
        <div class="col-lg-5">
            <div class="bento-card bento-progress-card h-100">
                <div class="bento-card-header-sm">
                    <h5 class="bento-card-title-sm">
                        <i class="bi bi-pie-chart-fill me-1.5" style="color: var(--bps-orange);"></i> Realisasi BAST Kabupaten
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
                                <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Target Tercapai (100%)</span>
                            @elseif($bastPercentage >= 50)
                                <span class="text-dark"><i class="bi bi-fire text-warning me-1"></i> Progres On Track</span>
                            @else
                                <span class="text-danger"><i class="bi bi-hourglass-split me-1"></i> Perlu Akselerasi</span>
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
                    <span><i class="bi bi-people-fill me-1"></i> {{ $totalOfficers }} Petugas Terdaftar</span>
                    <span class="fw-semibold text-dark">{{ $totalBasts }} Total Dokumen BAST</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ==============================================================
         5. DUA SISI MONITORING: RINGKASAN PROGRES LAPANGAN (KIRI) VS ADMINISTRASI (KANAN)
         ============================================================== -->
    <div class="row g-4 mb-4">
        <!-- ================= SISI KIRI: PROGRES LAPANGAN ================= -->
        <div class="col-lg-6">
            <div class="bento-card h-100 border-primary border-opacity-25" style="background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%);">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px; background: #005BAC;">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0" style="font-size: 0.98rem;">
                                Ringkasan Monitoring Progres Lapangan
                            </h5>
                            <span class="text-muted small" style="font-size: 0.72rem;">Pencacahan sampel responden oleh petugas PCL</span>
                        </div>
                    </div>
                    <span class="badge {{ $fieldPercentage >= 75 ? 'bg-success' : ($fieldPercentage >= 50 ? 'bg-warning text-dark' : 'bg-primary') }} rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.78rem;">
                        {{ $fieldPercentage }}% Selesai
                    </span>
                </div>

                <!-- 4 Indikator Sampel Lapangan -->
                <div class="row g-2 mb-3">
                    <div class="col-3">
                        <div class="p-2 rounded-3 bg-white border text-center">
                            <span class="d-block text-muted" style="font-size: 0.68rem; text-transform: uppercase;">Target Beban</span>
                            <span class="fs-6 fw-bold text-dark">{{ number_format($fieldTotalSamples) }}</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 rounded-3 bg-white border text-center">
                            <span class="d-block text-muted" style="font-size: 0.68rem; text-transform: uppercase;">Selesai (BAST)</span>
                            <span class="fs-6 fw-bold text-success">{{ number_format($fieldCompletedSamples) }}</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 rounded-3 bg-white border text-center">
                            <span class="d-block text-muted" style="font-size: 0.68rem; text-transform: uppercase;">Sedang Didata</span>
                            <span class="fs-6 fw-bold text-warning">{{ number_format($fieldInProgressSamples) }}</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 rounded-3 bg-white border text-center">
                            <span class="d-block text-muted" style="font-size: 0.68rem; text-transform: uppercase;">Belum Didata</span>
                            <span class="fs-6 fw-bold text-secondary">{{ number_format($fieldPendingSamples) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar Multi-Segment -->
                @php
                    $pctCompleted = $fieldTotalSamples > 0 ? ($fieldCompletedSamples / $fieldTotalSamples) * 100 : 0;
                    $pctInProgress = $fieldTotalSamples > 0 ? ($fieldInProgressSamples / $fieldTotalSamples) * 100 : 0;
                    $pctPending = $fieldTotalSamples > 0 ? ($fieldPendingSamples / $fieldTotalSamples) * 100 : 0;
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between small text-muted mb-1" style="font-size: 0.72rem;">
                        <span><i class="bi bi-circle-fill text-success me-1" style="font-size: 0.5rem;"></i> Selesai ({{ round($pctCompleted, 1) }}%)</span>
                        <span><i class="bi bi-circle-fill text-warning me-1" style="font-size: 0.5rem;"></i> Sedang Didata ({{ round($pctInProgress, 1) }}%)</span>
                        <span><i class="bi bi-circle-fill text-secondary me-1" style="font-size: 0.5rem;"></i> Belum ({{ round($pctPending, 1) }}%)</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 999px;">
                        <div class="progress-bar bg-success" style="width: {{ $pctCompleted }}%"></div>
                        <div class="progress-bar bg-warning" style="width: {{ $pctInProgress }}%"></div>
                        <div class="progress-bar bg-secondary" style="width: {{ $pctPending }}%"></div>
                    </div>
                </div>

                <!-- Tabel Ringkas Kinerja PCL Terlibat -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-dark small" style="font-size: 0.78rem;">
                        <i class="bi bi-people me-1 text-primary"></i> Kinerja Petugas Lapangan (PCL):
                    </span>
                    <a href="{{ route('admin.respondents.index', ['assignment_id' => $selectedAssignmentId]) }}" class="bento-card-link-sm" style="font-size: 0.72rem;">
                        Detail Responden &rarr;
                    </a>
                </div>

                <div class="table-responsive border rounded-3 bg-white" style="max-height: 220px; overflow-y: auto;">
                    <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.76rem;">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="ps-2">Nama Petugas / PCL</th>
                                <th>Wilayah</th>
                                <th class="text-center">Target</th>
                                <th class="text-center">BAST</th>
                                <th class="text-center">Capaian</th>
                                <th class="text-end pe-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($filteredOfficersList->take(8) as $offItem)
                                <tr>
                                    <td class="ps-2 fw-semibold text-dark text-truncate" style="max-width: 140px;">{{ $offItem->name }}</td>
                                    <td class="text-muted small text-truncate" style="max-width: 90px;">{{ $offItem->dominant_district }}</td>
                                    <td class="text-center font-monospace">{{ $offItem->target }}</td>
                                    <td class="text-center font-monospace text-success fw-bold">{{ $offItem->completed }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $offItem->badge_class }} rounded-pill" style="font-size: 0.65rem;">
                                            {{ $offItem->percentage }}%
                                        </span>
                                    </td>
                                    <td class="text-end pe-2">
                                        @if($offItem->wa_url)
                                            <a href="{{ $offItem->wa_url }}" target="_blank" class="btn btn-xs btn-outline-success py-0.5 px-1.5 rounded-pill" title="Kirim Pengingat WhatsApp" style="font-size: 0.68rem;">
                                                <i class="bi bi-whatsapp"></i>
                                            </a>
                                        @endif
                                        <button type="button" class="btn btn-xs btn-outline-warning text-dark py-0.5 px-1.5 rounded-pill" title="Kirim Notif SIMPEL" style="font-size: 0.68rem;" onclick="openSendNoticeModal({{ $offItem->id }}, '{{ addslashes($offItem->name) }}', 'general')">
                                            <i class="bi bi-megaphone-fill"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3 text-muted">Belum ada alokasi petugas pada kegiatan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ================= SISI KANAN: MONITORING ADMINISTRASI ================= -->
        <div class="col-lg-6">
            <div class="bento-card h-100 border-success border-opacity-25" style="background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%);">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px; background: #16A34A;">
                            <i class="bi bi-folder-check"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0" style="font-size: 0.98rem;">
                                Ringkasan Monitoring Administrasi
                            </h5>
                            <span class="text-muted small" style="font-size: 0.72rem;">Dokumen kelengkapan SPJ, tanda terima &amp; laporan</span>
                        </div>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.78rem;">
                        Administrasi Terpadu
                    </span>
                </div>

                <!-- 5 Checklist Kartu Dokumen Administrasi -->
                <div class="d-flex flex-column gap-2 mb-3">
                    <!-- 1. Tanda Terima Blanko & Petugas -->
                    <div class="p-2.5 border rounded-3 bg-white d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="rounded-2 p-1.5 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                <i class="bi bi-card-checklist"></i>
                            </div>
                            <div>
                                <a href="{{ route('admin.basts.officers.index', ['bast_type' => 'kantor_ke_petugas']) }}" class="fw-bold text-dark text-decoration-none small d-block">
                                    Tanda Terima Petugas (Blanko &amp; ATK)
                                </a>
                                <span class="text-muted" style="font-size: 0.7rem;">Penyerahan berkas fisik kantor &rarr; petugas</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary-subtle text-primary border rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.75rem;">
                                {{ $adminBastBlankoCount }} Berkas
                            </span>
                        </div>
                    </div>

                    <!-- 2. Tanda Terima Responden -->
                    <div class="p-2.5 border rounded-3 bg-white d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="rounded-2 p-1.5 bg-info-subtle text-info-emphasis d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                <i class="bi bi-file-earmark-check"></i>
                            </div>
                            <div>
                                <a href="{{ route('admin.basts.respondents.index') }}" class="fw-bold text-dark text-decoration-none small d-block">
                                    Tanda Terima Responden (BAST Ruta)
                                </a>
                                <span class="text-muted" style="font-size: 0.7rem;">Bukti serah terima dokumen kuesioner ke responden</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-info-subtle text-info-emphasis border rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.75rem;">
                                {{ $adminBastRespondentCount }} Dokumen Sah
                            </span>
                        </div>
                    </div>

                    <!-- 3. Laporan Perjadin Petugas -->
                    <div class="p-2.5 border rounded-3 bg-white d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="rounded-2 p-1.5 bg-warning-subtle text-warning-emphasis d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                <i class="bi bi-journal-text"></i>
                            </div>
                            <div>
                                <a href="{{ route('admin.reports.index') }}" class="fw-bold text-dark text-decoration-none small d-block">
                                    Laporan Perjalanan Dinas Petugas
                                </a>
                                <span class="text-muted" style="font-size: 0.7rem;">{{ $officersWithReportCount }} dari {{ $totalOfficersCount }} petugas sudah lapor &bull; {{ $adminReportsApprovedCount }} di-ACC</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge {{ $officersWithoutReportCount === 0 ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.75rem;">
                                {{ $reportSubmissionRate }}% Terkumpul
                            </span>
                        </div>
                    </div>

                    <!-- 4. BAST Petugas (Penyerahan Dokumen Terisi) -->
                    <div class="p-2.5 border rounded-3 bg-white d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="rounded-2 p-1.5 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div>
                                <a href="{{ route('admin.basts.officers.index', ['bast_type' => 'petugas_ke_kantor']) }}" class="fw-bold text-dark text-decoration-none small d-block">
                                    BAST Petugas (Hasil Sensus &rarr; Kantor)
                                </a>
                                <span class="text-muted" style="font-size: 0.7rem;">Penyerahan dokumen terisi &amp; tuntas dari petugas ke BPS</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success-subtle text-success border rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.75rem;">
                                {{ $adminBastHasilCount }} Dokumen Tuntas
                            </span>
                        </div>
                    </div>

                    <!-- 5. Daftar Hadir Pelatihan / Briefing -->
                    <div class="p-2.5 border rounded-3 bg-white d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="rounded-2 p-1.5 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; background: #FAF5FF; color: #7C3AED;">
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                            <div>
                                <a href="{{ route('admin.trainings.index') }}" class="fw-bold text-dark text-decoration-none small d-block">
                                    Daftar Hadir Pelatihan / Briefing
                                </a>
                                <span class="text-muted" style="font-size: 0.7rem;">Presensi pelatihan &amp; briefing petugas lapangan</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.75rem; background: #FAF5FF; color: #7C3AED; border: 1px solid #E9D5FF;">
                                {{ $adminTrainingsCount }} Agenda Presensi
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Aksi Cepat Administrasi -->
                <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                    @if(auth()->user()->isAdmin() && $officersWithoutReports->count() > 0)
                        <button type="button" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-3 py-1.5" style="font-size: 0.76rem;" onclick="openSendNoticeModal('all_without_reports', 'Semua Petugas Belum Lapor ({{ $officersWithoutReports->count() }} Orang)', 'report')">
                            <i class="bi bi-bell-fill me-1 text-warning"></i> Ingatkan Petugas Belum Lapor ({{ $officersWithoutReports->count() }})
                        </button>
                    @endif
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 ms-auto" style="font-size: 0.76rem;" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                        <i class="bi bi-file-earmark-excel-fill me-1"></i> Upload Excel Kegiatan &amp; Sampel
                    </button>
                </div>
            </div>
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
                        <i class="bi bi-map-fill text-danger me-1.5"></i> Rekapitulasi Capaian per Kecamatan
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
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $dSum['name'] }}
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
                            <i class="bi bi-geo-alt display-6 mb-1 text-secondary opacity-50 d-block"></i>
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
                            <i class="bi bi-clipboard2-check-fill text-primary me-1.5"></i> Kepatuhan Laporan Perjadin Petugas
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
                                    <i class="bi bi-send-fill me-1"></i> Ingatkan Semua
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
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $offWithout->dominant_district }} &bull; BAST: {{ $offWithout->completed }}/{{ $offWithout->target }}
                                    </small>
                                </div>
                                @if(auth()->user()->isAdmin())
                                    <div class="flex-shrink-0">
                                        <button type="button" 
                                                class="btn btn-xs btn-outline-primary py-0.5 px-2 rounded-pill" 
                                                style="font-size: 0.7rem;" 
                                                title="Kirim pesan ke papan informasi petugas" 
                                                onclick="openSendNoticeModal({{ $offWithout->id }}, '{{ addslashes($offWithout->name) }}', 'report')">
                                            <i class="bi bi-megaphone-fill me-1"></i> Ingatkan
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-2 text-success small" style="font-size: 0.75rem;">
                                <i class="bi bi-check-circle-fill me-1"></i> Seluruh petugas telah mengumpulkan laporan perjalanan dinas!
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="pt-2 border-top mt-2 d-flex justify-content-between align-items-center small text-muted" style="font-size: 0.75rem;">
                    <span><i class="bi bi-file-earmark-check-fill me-1"></i> Status Verifikasi Admin</span>
                    <span class="fw-semibold text-dark">{{ $reportsApproved }} Laporan Telah di-ACC</span>
                </div>
            </div>
        </div>
    </div>

         8. GRAFIK TREN BULANAN & LOG AKTIVITAS (BENTO CARDS)
         ============================================================== -->
    <div class="row g-4 mb-2 align-items-stretch">
        <!-- Chart: Tren Penginputan Responden & BAST -->
        <div class="col-lg-8">
            <div class="bento-card h-100 d-flex flex-column">
                <div class="bento-card-header-sm">
                    <h5 class="bento-card-title-sm">
                        <i class="bi bi-bar-chart-fill me-1.5" style="color: var(--bps-orange);"></i> Tren Penginputan Responden &amp; BAST Terbit
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
                        <i class="bi bi-lightning-charge-fill text-warning me-1.5"></i> Log Aktivitas Terkini
                    </h5>
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
                                <i class="bi bi-clock-history display-6 mb-1 text-secondary opacity-50 d-block"></i>
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
                    <i class="bi bi-send-fill text-primary me-1"></i> Draf Pesan Notifikasi Petugas
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
                    <i class="bi bi-info-circle-fill text-info"></i>
                    <div>Salin pesan atau langsung kirimkan via WhatsApp resmi petugas.</div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2 px-3 d-flex justify-content-between flex-wrap gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="copyReminderText()">
                    <i class="bi bi-copy me-1"></i> <span id="btnCopyText">Salin Teks</span>
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill" data-bs-dismiss="modal">Tutup</button>
                    @if(auth()->user()->isAdmin())
                        <button type="button" class="btn btn-sm rounded-pill text-white" style="background: var(--bps-orange);" onclick="transferReminderToNoticeBoard()">
                            <i class="bi bi-megaphone-fill me-1"></i> Kirim ke Papan Informasi
                        </button>
                    @endif
                    <a href="#" id="modal_wa_direct_link" target="_blank" class="btn btn-sm btn-outline-success rounded-pill">
                        <i class="bi bi-whatsapp me-1"></i> WhatsApp
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
                    <i class="bi bi-megaphone-fill me-1.5" style="color: var(--bps-orange);"></i> Kirim Pesan ke Papan Informasi Petugas
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
                            <i class="bi bi-info-circle-fill text-primary me-1"></i> Pesan ini akan langsung tampil di kotak <strong>"Informasi Terbaru"</strong> pada beranda HP/layar awal petugas yang bersangkutan.
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
                        <i class="bi bi-send-fill me-1"></i> Kirim ke Papan Informasi Petugas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@include('admin.partials._import_excel_modal')
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

        const message = `Halo Rekan ${name},\n\nMengingatkan terkait progres penugasan SIMPEL Anda saat ini:\n• Target Responden: ${target}\n• BAST Selesai: ${completed} (${percentage}%)\n• Sisa Belum BAST: ${pending}\n• Status Laporan Kegiatan: ${reportStatus}\n\nMohon untuk segera menyelesaikan kunjungan responden & penginputan BAST serta melengkapi Laporan Perjalanan Dinas. Terima kasih.`;
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
            <i class="bi bi-person-circle text-primary"></i>
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

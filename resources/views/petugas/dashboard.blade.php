@extends('layouts.app')

@section('title', 'Beranda Petugas Lapangan - SIMPLE')
@section('page_title', 'Beranda Petugas')

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
    max-width: 1320px;
    margin: 0 auto;
    padding-bottom: 2rem;
}




/* Base Card Style */
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
    width: 170px;
    height: 170px;
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
    font-size: 0.92rem;
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

/* Section Title */
.bento-section-title {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0F172A;
    margin: 1.75rem 0 1rem 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Stat Bento Cards (4 Mini Cards) */
.bento-stat-card {
    background: #FFFFFF;
    border-radius: var(--bps-radius-pill);
    border: 1px solid #E2E8F0;
    padding: 1.15rem;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03);
    transition: all 0.22s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
}
.bento-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
}
.bento-stat-icon-wrap {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    margin-bottom: 0.85rem;
}
.bento-stat-val {
    font-size: 1.5rem;
    font-weight: 800;
    color: #0F172A;
    line-height: 1.1;
    margin-bottom: 0.2rem;
}
.bento-stat-lbl {
    font-size: 0.76rem;
    font-weight: 600;
    color: #64748B;
    margin-bottom: 0.75rem;
}
.bento-mini-bar-track {
    height: 5px;
    background: #F1F5F9;
    border-radius: 999px;
    overflow: hidden;
    position: relative;
}
.bento-mini-bar-fill {
    height: 100%;
    border-radius: 999px;
}

/* Quick Action Pills (2x2 Grid) */
.bento-action-pill {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 0.95rem 1.15rem;
    border-radius: var(--bps-radius-pill);
    text-decoration: none;
    border: 1px solid transparent;
    transition: all 0.22s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}
.bento-action-pill:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
}
.bento-action-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}
.bento-action-text {
    flex-grow: 1;
    min-width: 0;
}
.bento-action-title {
    font-size: 0.86rem;
    font-weight: 700;
    color: #0F172A;
    line-height: 1.2;
    display: block;
}
.bento-action-sub {
    font-size: 0.7rem;
    color: #64748B;
    line-height: 1.2;
    margin-top: 2px;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.bento-action-arrow {
    color: #94A3B8;
    font-size: 0.8rem;
    transition: transform 0.2s ease;
}
.bento-action-pill:hover .bento-action-arrow {
    transform: translateX(3px);
    color: var(--bps-orange);
}

/* Specific Action Color Tints */
.bento-pill-green {
    background: #F0FDF4;
    border-color: #DCFCE7;
}
.bento-pill-green .bento-action-icon {
    background: #DCFCE7;
    color: #16A34A;
}

.bento-pill-blue {
    background: #F0F9FF;
    border-color: #E0F2FE;
}
.bento-pill-blue .bento-action-icon {
    background: #E0F2FE;
    color: #0284C7;
}

.bento-pill-purple {
    background: #FAF5FF;
    border-color: #F3E8FF;
}
.bento-pill-purple .bento-action-icon {
    background: #F3E8FF;
    color: #7C3AED;
}

.bento-pill-amber {
    background: #FFFBEB;
    border-color: #FEF3C7;
}
.bento-pill-amber .bento-action-icon {
    background: #FEF3C7;
    color: #D97706;
}

/* Upcoming Schedules Timeline */
.bento-timeline-item {
    position: relative;
    padding-left: 1.5rem;
    padding-bottom: 1rem;
}
.bento-timeline-item:last-child {
    padding-bottom: 0;
}
.bento-timeline-item::before {
    content: '';
    position: absolute;
    left: 4px;
    top: 10px;
    bottom: -6px;
    width: 2px;
    background: #E2E8F0;
}
.bento-timeline-item:last-child::before {
    display: none;
}
.bento-timeline-dot {
    position: absolute;
    left: 0;
    top: 4px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--bps-orange);
    border: 2px solid #FFFFFF;
    box-shadow: 0 0 0 2px rgba(243, 112, 33, 0.25);
}

/* Quote Box (Bottom Right) */
.bento-quote-box {
    background: #F8FAFC;
    border-left: 3px solid var(--bps-orange);
    border-radius: 12px;
    padding: 0.85rem 1rem;
    font-size: 0.78rem;
    color: #475569;
    font-style: italic;
    margin-top: 1rem;
}
</style>
@endpush

@section('content')
<div class="bento-dashboard-wrapper">

    <!-- ==============================================================
         1. NOTIFIKASI / PESAN KHUSUS DARI ADMINISTRATOR (JIKA ADA)
         ============================================================== -->
    @if(isset($officerNotices) && $officerNotices->isNotEmpty())
        <div class="mb-3">
            @foreach($officerNotices as $notice)
                <div class="p-3 mb-3 shadow-sm" style="background: #FFFDF9; border: 1px solid rgba(243, 112, 33, 0.25); border-radius: 16px; box-shadow: 0 4px 16px rgba(243, 112, 33, 0.04);">
                    <div class="d-flex justify-content-between align-items-start mb-1.5">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge text-white px-2.5 py-1" style="font-size: 0.72rem; background: #F37021; border-radius: 999px;">
                                <i class="bi bi-megaphone-fill me-1"></i> Pesan Administrator
                            </span>
                            @if(!$notice->is_read)
                                <span class="badge bg-warning text-dark px-2 py-0.5 rounded-pill" style="font-size: 0.65rem; font-weight: 700;">Baru</span>
                            @endif
                            <span class="fw-bold text-dark small">{{ $notice->title }}</span>
                        </div>
                        <span class="text-muted" style="font-size: 0.72rem;">
                            <i class="bi bi-clock me-1"></i> {{ $notice->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <div class="text-dark small my-2" style="line-height: 1.5; white-space: pre-line;">
                        {{ $notice->message }}
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 mt-2 border-top" style="border-color: rgba(243, 112, 33, 0.12) !important;">
                        <div class="d-flex align-items-center gap-2">
                            @if(str_contains($notice->type, 'report'))
                                <a href="{{ route('petugas.reports.index') }}" class="btn btn-sm py-1.5 px-3 rounded-pill text-white fw-bold d-inline-flex align-items-center gap-1.5 shadow-sm" style="font-size: 0.76rem; background: #F37021; border: none; box-shadow: 0 4px 12px rgba(243, 112, 33, 0.25);">
                                    <i class="bi bi-clipboard2-check-fill"></i>
                                    <span>Buka Menu Laporan</span>
                                </a>
                            @elseif(str_contains($notice->type, 'bast'))
                                <a href="{{ route('petugas.basts.respondents.index') }}" class="btn btn-sm py-1.5 px-3 rounded-pill text-white fw-bold d-inline-flex align-items-center gap-1.5 shadow-sm" style="font-size: 0.76rem; background: #F37021; border: none; box-shadow: 0 4px 12px rgba(243, 112, 33, 0.25);">
                                    <i class="bi bi-file-earmark-check-fill"></i>
                                    <span>Buka Tanda Terima Responden</span>
                                </a>
                            @endif
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            @if(!$notice->is_read)
                                <form action="{{ route('petugas.notices.read', $notice) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-xs btn-outline-secondary py-1 px-2.5 rounded-pill" style="font-size: 0.7rem;">
                                        <i class="bi bi-check2 me-1"></i> Tandai Dibaca
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('petugas.notices.destroy', $notice) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pesan ini dari papan informasi?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-link text-danger p-1" style="font-size: 0.85rem;" title="Hapus Pesan">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- ==============================================================
         3. TOP ROW: HERO BANNER (LEFT) & TODAY'S PROGRESS (RIGHT)
         ============================================================== -->
    <div class="row g-3 mb-4">
        <!-- Hero Card (Left ~60%) -->
        <div class="col-lg-7">
            <div class="bento-card bento-hero-card h-100">
                <div class="bento-hero-bg-art"></div>
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="bento-hero-badge">
                            <i class="fa-solid fa-bullhorn"></i> Penugasan Aktif
                        </span>
                        @if($nearestTaskDeadline && $nearestTaskDeadline->assignment && $nearestTaskDeadline->assignment->end_date)
                            <span class="badge {{ $isUrgentDeadline ? 'bg-danger text-white' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' }} px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                                <i class="fa-solid fa-hourglass-half me-1"></i>
                                {{ $daysUntilDeadline > 0 ? 'Sisa ' . $daysUntilDeadline . ' Hari' : ($daysUntilDeadline === 0 ? 'Hari Ini Batas Akhir!' : 'Batas Waktu Lewat') }}
                            </span>
                        @endif
                    </div>

                    @if($nearestTaskDeadline && $nearestTaskDeadline->assignment)
                        <h2 class="bento-hero-title">
                            {{ $nearestTaskDeadline->assignment->title }}
                        </h2>
                        <p class="bento-hero-desc">
                            <i class="fa-solid fa-location-dot text-danger me-1"></i> Lokasi Tugas: <strong>{{ $nearestTaskDeadline->assignment->location ?: 'Kabupaten Kolaka Utara' }}</strong> &bull; Batas Akhir: {{ $nearestTaskDeadline->assignment->end_date ? $nearestTaskDeadline->assignment->end_date->translatedFormat('d F Y') : 'Sesuai Jadwal' }}
                        </p>
                    @else
                        <h2 class="bento-hero-title">
                            Seluruh Dokumen &amp; Penugasan Siap Dilaksanakan
                        </h2>
                        <p class="bento-hero-desc">
                            Pastikan kuesioner terisi lengkap, tanda terima responden bertanda tangan digital, dan laporkan perjalanan dinas tepat waktu.
                        </p>
                    @endif
                </div>

                <div class="mt-2">
                    <a href="{{ route('petugas.basts.respondents.index') }}" class="bento-hero-cta">
                        <i class="fa-solid fa-pen-nib"></i>
                        <span>Mulai Tanda Terima Responden</span>
                        <i class="fa-solid fa-arrow-right small ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Today's Progress Ring Card (Right ~40%) -->
        <div class="col-lg-5">
            <div class="bento-card bento-progress-card h-100">
                <div class="bento-card-header-sm">
                    <h5 class="bento-card-title-sm">
                        <i class="fa-solid fa-chart-pie me-1.5" style="color: var(--bps-orange);"></i> Progres Sampel Responden
                    </h5>
                    <a href="{{ route('petugas.respondents.index') }}" class="bento-card-link-sm">
                        Rincian Sampel &rarr;
                    </a>
                </div>

                @php
                    // Donut SVG circumference for radius 40 = 2 * PI * 40 = 251.327
                    $clampedPct = min(100, max(0, (float) $progressPercentage));
                    $donutOffset = 251.327 - (251.327 * ($clampedPct / 100));
                @endphp

                <div class="bento-progress-body my-auto py-2">
                    <div class="bento-ring-wrap">
                        <svg class="bento-ring-svg" viewBox="0 0 100 100">
                            <circle class="bento-ring-track" cx="50" cy="50" r="40" />
                            <circle class="bento-ring-indicator" cx="50" cy="50" r="40"
                                    stroke-dasharray="251.327"
                                    stroke-dashoffset="{{ $donutOffset }}" />
                        </svg>
                        <div class="bento-ring-label">
                            <span class="bento-ring-percent">{{ $progressPercentage }}%</span>
                            <span class="bento-ring-subtext">TERDATA</span>
                        </div>
                    </div>

                    <div class="bento-progress-info">
                        <div class="bento-progress-headline">
                            @if($progressPercentage >= 100)
                                <span class="text-success"><i class="fa-solid fa-circle-check me-1"></i> Target Tercapai! 🎉</span>
                            @elseif($progressPercentage >= 50)
                                <span class="text-dark"><i class="fa-solid fa-fire text-warning me-1"></i> On Track! Mantap</span>
                            @else
                                <span class="text-dark"><i class="fa-solid fa-person-walking text-primary me-1"></i> Sedang Berjalan</span>
                            @endif
                        </div>
                        <p class="bento-progress-details">
                            <strong>{{ number_format($totalRespondents) }}</strong> dari <strong>{{ number_format($targetRespondents) }}</strong> responden sampel selesai didata.
                            @if($remainingRespondents > 0)
                                <span class="d-block text-muted mt-1" style="font-size: 0.76rem;">Sisa <strong>{{ number_format($remainingRespondents) }}</strong> responden lagi.</span>
                            @else
                                <span class="d-block text-success mt-1 fw-semibold" style="font-size: 0.76rem;">Seluruh kuota telah terpenuhi!</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="pt-2 border-top mt-2 d-flex justify-content-between align-items-center small text-muted" style="font-size: 0.75rem;">
                    <span><i class="fa-regular fa-calendar me-1"></i> {{ now()->translatedFormat('l, d F Y') }}</span>
                    <span class="fw-semibold text-dark">{{ $totalRespondentBasts }} BAST Terbit</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ==============================================================
         4. MIDDLE ROW: 4 STAT BENTO CARDS + 4 QUICK ACTION PILLS
         ============================================================== -->
    <div class="row g-4 mb-4">
        <!-- Sisi Kiri: 4 Kartu Metrik Kinerja (col-lg-7) -->
        <div class="col-lg-7">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="bento-card-title-sm">
                    <i class="fa-solid fa-chart-line me-1.5" style="color: var(--bps-orange);"></i> Metrik Kinerja Lapangan
                </h5>
                <span class="small text-muted" style="font-size: 0.76rem;">Status Real-time</span>
            </div>

            <div class="row g-3">
                <!-- Card 1: Target Kuota Responden -->
                <div class="col-6 col-md-3">
                    <div class="bento-stat-card">
                        <div>
                            <div class="bento-stat-icon-wrap" style="background: var(--bps-orange-soft); color: var(--bps-orange);">
                                <i class="fa-solid fa-house-chimney-user"></i>
                            </div>
                            <div class="bento-stat-val">{{ number_format($targetRespondents) }}</div>
                            <div class="bento-stat-lbl">Target Kuota</div>
                        </div>
                        <div>
                            <div class="bento-mini-bar-track">
                                <div class="bento-mini-bar-fill" style="width: 100%; background: var(--bps-orange);"></div>
                            </div>
                            <span class="text-muted mt-1 d-block" style="font-size: 0.65rem;">Sampel Alokasi</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Tanda Terima Responden Terbit -->
                <div class="col-6 col-md-3">
                    <div class="bento-stat-card">
                        <div>
                            <div class="bento-stat-icon-wrap" style="background: var(--bps-green-soft); color: var(--bps-green);">
                                <i class="fa-solid fa-file-signature"></i>
                            </div>
                            <div class="bento-stat-val" style="color: var(--bps-green);">{{ number_format($totalRespondentBasts) }}</div>
                            <div class="bento-stat-lbl">BAST Terbit</div>
                        </div>
                        <div>
                            <div class="bento-mini-bar-track">
                                <div class="bento-mini-bar-fill" style="width: {{ $progressPercentage }}%; background: var(--bps-green);"></div>
                            </div>
                            <span class="text-success mt-1 d-block fw-semibold" style="font-size: 0.65rem;">{{ $progressPercentage }}% Selesai</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Sisa Sampel Belum BAST -->
                <div class="col-6 col-md-3">
                    <div class="bento-stat-card">
                        <div>
                            <div class="bento-stat-icon-wrap" style="background: var(--bps-amber-soft); color: var(--bps-amber);">
                                <i class="fa-solid fa-hourglass-half"></i>
                            </div>
                            <div class="bento-stat-val" style="color: var(--bps-amber);">{{ number_format($remainingRespondents) }}</div>
                            <div class="bento-stat-lbl">Sisa Lapangan</div>
                        </div>
                        <div>
                            <div class="bento-mini-bar-track">
                                <div class="bento-mini-bar-fill" style="width: {{ max(0, 100 - $progressPercentage) }}%; background: var(--bps-amber);"></div>
                            </div>
                            <span class="text-muted mt-1 d-block" style="font-size: 0.65rem;">Perlu Kunjungan</span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Laporan Perjadin ACC -->
                <div class="col-6 col-md-3">
                    <div class="bento-stat-card">
                        <div>
                            <div class="bento-stat-icon-wrap" style="background: var(--bps-purple-soft); color: var(--bps-purple);">
                                <i class="fa-solid fa-clipboard-user"></i>
                            </div>
                            <div class="bento-stat-val" style="color: var(--bps-purple);">{{ number_format($reportsApproved) }}</div>
                            <div class="bento-stat-lbl">Laporan ACC</div>
                        </div>
                        <div>
                            @if($reportsRevision > 0)
                                <span class="badge bg-danger text-white w-100 py-1" style="font-size: 0.65rem;">{{ $reportsRevision }} Revisi</span>
                            @elseif($reportsApproved > 0)
                                <span class="badge bg-success-subtle text-success border border-success-subtle w-100 py-1" style="font-size: 0.65rem;">ACC Admin</span>
                            @elseif($reportsSubmitted > 0)
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle w-100 py-1" style="font-size: 0.65rem;">Menunggu ACC</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border w-100 py-1" style="font-size: 0.65rem;">Belum Buat</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sisi Kanan: 4 Menu Tombol Cepat Lapangan (col-lg-5) -->
        <div class="col-lg-5">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="bento-card-title-sm">
                    <i class="fa-solid fa-wand-magic-sparkles me-1.5" style="color: var(--bps-orange);"></i> Menu Aksi Lapangan
                </h5>
                <span class="small text-muted" style="font-size: 0.76rem;">Akses Langsung</span>
            </div>

            <div class="row g-2.5">
                <!-- 1. Tanda Terima Resp -->
                <div class="col-sm-6">
                    <a href="{{ route('petugas.basts.respondents.index') }}" class="bento-action-pill bento-pill-green">
                        <div class="bento-action-icon">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>
                        <div class="bento-action-text">
                            <span class="bento-action-title">Tanda Terima Resp</span>
                            <span class="bento-action-sub">KRT &bull; TTD Digital</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5 mt-1" style="font-size: 0.65rem;">
                                {{ $totalRespondentBasts }} Terbit
                            </span>
                        </div>
                        <i class="fa-solid fa-chevron-right bento-action-arrow"></i>
                    </a>
                </div>

                <!-- 2. Tanda Terima Petugas (ATK / KUES) -->
                <div class="col-sm-6">
                    <a href="{{ route('petugas.basts.officers.index', ['type' => 'kantor_ke_petugas']) }}" class="bento-action-pill bento-pill-blue">
                        <div class="bento-action-icon">
                            <i class="fa-solid fa-boxes-packing"></i>
                        </div>
                        <div class="bento-action-text">
                            <span class="bento-action-title">Tanda Terima Petugas</span>
                            <span class="bento-action-sub">ATK, Kues &amp; Timbangan</span>
                            @if(($pendingSignBlankoBastsCount ?? 0) > 0)
                                <span class="badge bg-danger text-white border px-1.5 py-0.5 mt-1" style="font-size: 0.65rem;">
                                    <i class="fa-solid fa-pen-nib me-1"></i> {{ $pendingSignBlankoBastsCount }} TTD
                                </span>
                            @else
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-1.5 py-0.5 mt-1" style="font-size: 0.65rem;">
                                    {{ $totalBlankoBasts }} Sah
                                </span>
                            @endif
                        </div>
                        <i class="fa-solid fa-chevron-right bento-action-arrow"></i>
                    </a>
                </div>

                <!-- 3. Laporan Perjadin -->
                <div class="col-sm-6">
                    <a href="{{ route('petugas.reports.index') }}" class="bento-action-pill bento-pill-purple">
                        <div class="bento-action-icon">
                            <i class="fa-solid fa-clipboard-user"></i>
                        </div>
                        <div class="bento-action-text">
                            <span class="bento-action-title">Laporan Perjadin</span>
                            <span class="bento-action-sub">Perjalanan Dinas &amp; Riil</span>
                            <span class="badge {{ $reportsRevision > 0 ? 'bg-danger text-white' : ($reportsApproved > 0 ? 'bg-success-subtle text-success border' : 'bg-secondary-subtle text-secondary border') }} px-1.5 py-0.5 mt-1" style="font-size: 0.65rem;">
                                {{ $reportsRevision > 0 ? $reportsRevision . ' Revisi' : ($reportsApproved > 0 ? $reportsApproved . ' ACC' : ($reportsSubmitted > 0 ? $reportsSubmitted . ' Menunggu' : 'Draft')) }}
                            </span>
                        </div>
                        <i class="fa-solid fa-chevron-right bento-action-arrow"></i>
                    </a>
                </div>

                <!-- 4. BAST Dokumen Terisi -->
                <div class="col-sm-6">
                    <a href="{{ route('petugas.basts.officers.index', ['type' => 'petugas_ke_kantor']) }}" class="bento-action-pill bento-pill-amber">
                        <div class="bento-action-icon">
                            <i class="fa-solid fa-file-circle-check"></i>
                        </div>
                        <div class="bento-action-text">
                            <span class="bento-action-title">BAST Dokumen Terisi</span>
                            <span class="bento-action-sub">Petugas &rarr; Kantor BPS</span>
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-1.5 py-0.5 mt-1" style="font-size: 0.65rem;">
                                {{ $totalTerisiBasts }} Sah
                            </span>
                        </div>
                        <i class="fa-solid fa-chevron-right bento-action-arrow"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ==============================================================
         5. BOTTOM ROW: KEGIATAN AKTIF (LEFT) & AGENDA / TIMELINE (RIGHT)
         ============================================================== -->
    <div class="row g-4">
        <!-- Kiri: Penugasan Lapangan Aktif (col-lg-7) -->
        <div class="col-lg-7">
            <div class="bento-card h-100">
                <div class="bento-card-header-sm">
                    <h5 class="bento-card-title-sm">
                        <i class="fa-solid fa-list-check me-1.5" style="color: var(--bps-orange);"></i> Penugasan Lapangan Anda
                    </h5>
                    <a href="{{ route('petugas.assignments.index') }}" class="bento-card-link-sm">
                        Semua Penugasan &rarr;
                    </a>
                </div>

                @if(isset($activeTasks) && $activeTasks->isNotEmpty())
                    <div class="d-flex flex-column gap-2.5">
                        @foreach($activeTasks as $task)
                            <div class="p-3 rounded-4 border bg-light bg-opacity-50 d-flex flex-wrap justify-content-between align-items-center gap-2 transition" style="border-color: #E2E8F0 !important;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 p-2 text-white text-center" style="background: var(--bps-orange); width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-briefcase"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small">{{ $task->assignment ? $task->assignment->title : 'Tugas #' . $task->id }}</div>
                                        <div class="text-muted" style="font-size: 0.76rem;">
                                            <i class="fa-solid fa-map-pin text-danger me-1"></i> {{ $task->assignment ? $task->assignment->location : 'Kolaka Utara' }}
                                            @if($task->assignment && $task->assignment->end_date)
                                                &bull; Batas: {{ $task->assignment->end_date->format('d/m/Y') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge {{ $task->status === 'completed' ? 'bg-success' : 'bg-primary-subtle text-primary border border-primary-subtle' }} px-2 py-1" style="font-size: 0.72rem;">
                                        {{ ucfirst($task->status) }}
                                    </span>
                                    <a href="{{ route('petugas.respondents.index') }}" class="btn btn-xs btn-outline-secondary py-1 px-2 rounded-pill" style="font-size: 0.72rem;">
                                        Sampel &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="fa-solid fa-clipboard-check fa-2x mb-2 text-secondary opacity-50"></i>
                        <div class="small fw-semibold">Belum ada penugasan lapangan yang aktif saat ini.</div>
                        <div class="text-muted" style="font-size: 0.72rem;">Penugasan baru yang dialokasikan oleh Administrator akan otomatis muncul di sini.</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Kanan: Timeline Jadwal & Agenda Lapangan BPS (col-lg-5) -->
        <div class="col-lg-5">
            <div class="bento-card h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="bento-card-header-sm">
                        <h5 class="bento-card-title-sm">
                            <i class="fa-regular fa-calendar-days me-1.5" style="color: var(--bps-orange);"></i> Jadwal &amp; Agenda BPS
                        </h5>
                        <a href="{{ route('petugas.schedules.index') }}" class="bento-card-link-sm">
                            Buka Kalender &rarr;
                        </a>
                    </div>

                    @if(isset($upcomingSchedules) && $upcomingSchedules->isNotEmpty())
                        <div class="ps-1 pt-1">
                            @foreach($upcomingSchedules->take(4) as $sch)
                                <div class="bento-timeline-item">
                                    <div class="bento-timeline-dot"></div>
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="fw-bold text-dark" style="font-size: 0.82rem;">
                                            {{ $sch->title }}
                                        </div>
                                        <span class="badge bg-light text-secondary border" style="font-size: 0.65rem;">
                                            {{ $sch->start_time ? $sch->start_time->format('H:i') : '' }}
                                        </span>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem;">
                                        <i class="fa-regular fa-clock me-1"></i> {{ $sch->start_time ? $sch->start_time->translatedFormat('d M Y') : '-' }}
                                        @if($sch->location)
                                            &bull; <i class="fa-solid fa-location-dot me-0.5 text-danger"></i> {{ $sch->location }}
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fa-regular fa-calendar-xmark fa-2x mb-2 text-secondary opacity-50"></i>
                            <div class="small fw-semibold">Tidak ada agenda kegiatan terdekat.</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Agenda pelatihan atau rapat koordinasi akan terjadwal di sini.</div>
                        </div>
                    @endif
                </div>

                <div class="bento-quote-box">
                    <i class="fa-solid fa-quote-left text-muted me-1.5 opacity-50"></i>
                    Kualitas data statistik berawal dari ketelitian dan integritas di setiap kunjungan lapangan.
                    <div class="fw-semibold text-dark mt-1" style="font-size: 0.72rem;">&mdash; BPS Kabupaten Kolaka Utara</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

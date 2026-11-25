@extends('layouts.app')

@section('title', 'Beranda Petugas Lapangan - SIMPEL')
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
    --period-card-bg: linear-gradient(135deg, #FFFFFF 0%, #FEF2F2 100%);
    --period-card-bg-hover: linear-gradient(135deg, #FFF8F8 0%, #FEE2E2 100%);
    --period-card-border: #FECACA;
    --period-card-border-hover: #FCA5A5;
    --period-accent: #DC2626;
    --period-dot-ring: rgba(220, 38, 38, 0.25);
}
.bento-dashboard-wrapper.period-theme-active {
    --period-card-bg: linear-gradient(135deg, #FFFFFF 0%, #F0FDF4 100%);
    --period-card-bg-hover: linear-gradient(135deg, #F8FFFA 0%, #DCFCE7 100%);
    --period-card-border: #86EFAC;
    --period-card-border-hover: #4ADE80;
    --period-accent: #16A34A;
    --period-dot-ring: rgba(22, 163, 74, 0.25);
    --bps-card-hover-shadow: 0 16px 36px rgba(22, 163, 74, 0.14), 0 2px 6px rgba(15, 23, 42, 0.04);
}
.bento-dashboard-wrapper.period-theme-not-started {
    --period-card-bg: linear-gradient(135deg, #FFFFFF 0%, #FFFBEB 100%);
    --period-card-bg-hover: linear-gradient(135deg, #FFFDF5 0%, #FEF3C7 100%);
    --period-card-border: #FDE68A;
    --period-card-border-hover: #FBBF24;
    --period-accent: #D97706;
    --period-dot-ring: rgba(217, 119, 6, 0.25);
    --bps-card-hover-shadow: 0 16px 36px rgba(217, 119, 6, 0.14), 0 2px 6px rgba(15, 23, 42, 0.04);
}
.bento-dashboard-wrapper.period-theme-closed {
    --period-card-bg: linear-gradient(135deg, #FFFFFF 0%, #FEF2F2 100%);
    --period-card-bg-hover: linear-gradient(135deg, #FFF8F8 0%, #FEE2E2 100%);
    --period-card-border: #FECACA;
    --period-card-border-hover: #FCA5A5;
    --period-accent: #DC2626;
    --period-dot-ring: rgba(220, 38, 38, 0.25);
    --bps-card-hover-shadow: 0 16px 36px rgba(220, 38, 38, 0.14), 0 2px 6px rgba(15, 23, 42, 0.04);
}

/* Base Card Style (mengikuti warna & gradasi status Periode SPKRT) */
.bento-card {
    background: var(--period-card-bg);
    border-radius: var(--bps-radius-card);
    border: 1px solid var(--period-card-border);
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

/* Header Title */
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

/* Quick Action Bento Cards (Tactile Action Buttons) */
.bento-action-pill {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 0.85rem 1.1rem;
    border-radius: 16px;
    text-decoration: none;
    border: 1px solid var(--period-card-border);
    background: var(--period-card-bg);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 3px 12px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(15, 23, 42, 0.02);
    min-height: 72px;
    height: 100%;
    position: relative;
    cursor: pointer;
}
.bento-action-pill:hover {
    transform: translateY(-2px);
    box-shadow: var(--bps-card-hover-shadow);
}
.bento-action-pill:active {
    transform: scale(0.985);
}
.bento-action-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
    transition: transform 0.2s ease;
}
.bento-action-pill:hover .bento-action-icon {
    transform: scale(1.06);
}
.bento-action-text {
    flex-grow: 1;
    min-width: 0;
}
.bento-action-title {
    font-size: 0.9rem;
    font-weight: 750;
    color: #0F172A;
    line-height: 1.25;
    white-space: normal;
    word-break: normal;
    display: block;
}
.bento-action-sub {
    font-size: 0.72rem;
    color: #64748B;
    line-height: 1.3;
    display: block;
    margin-top: 0.2rem;
    font-weight: 500;
}
.bento-action-badge {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.18rem 0.55rem;
    border-radius: 999px;
    white-space: nowrap;
    line-height: 1.2;
    display: inline-block;
}
.bento-action-cta {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.38rem 0.75rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 750;
    letter-spacing: 0.01em;
    flex-shrink: 0;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
    white-space: nowrap;
}
.bento-action-pill:hover .bento-action-cta {
    transform: translateX(3px);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.18);
}

/* Specific Action Color Tints & Borders */
.bento-pill-green {
    background: var(--period-card-bg);
    border: 1.5px solid var(--period-card-border);
    border-left: 5px solid #16A34A;
}
.bento-pill-green:hover {
    border-color: var(--period-card-border-hover);
    border-left-color: #16A34A;
    background: var(--period-card-bg-hover);
}
.bento-pill-green .bento-action-icon {
    background: #DCFCE7;
    color: #16A34A;
}
.cta-green {
    background: #16A34A;
    color: #FFFFFF !important;
}
.bento-pill-green:hover .cta-green {
    background: #15803D;
}

.bento-pill-blue {
    background: var(--period-card-bg);
    border: 1.5px solid var(--period-card-border);
    border-left: 5px solid #0284C7;
}
.bento-pill-blue:hover {
    border-color: var(--period-card-border-hover);
    border-left-color: #0284C7;
    background: var(--period-card-bg-hover);
}
.bento-pill-blue .bento-action-icon {
    background: #E0F2FE;
    color: #0284C7;
}
.cta-blue {
    background: #0284C7;
    color: #FFFFFF !important;
}
.bento-pill-blue:hover .cta-blue {
    background: #0369A1;
}

.bento-pill-purple {
    background: var(--period-card-bg);
    border: 1.5px solid var(--period-card-border);
    border-left: 5px solid #7C3AED;
}
.bento-pill-purple:hover {
    border-color: var(--period-card-border-hover);
    border-left-color: #7C3AED;
    background: var(--period-card-bg-hover);
}
.bento-pill-purple .bento-action-icon {
    background: #F3E8FF;
    color: #7C3AED;
}
.cta-purple {
    background: #7C3AED;
    color: #FFFFFF !important;
}
.bento-pill-purple:hover .cta-purple {
    background: #6D28D9;
}

.bento-pill-amber {
    background: var(--period-card-bg);
    border: 1.5px solid var(--period-card-border);
    border-left: 5px solid #D97706;
}
.bento-pill-amber:hover {
    border-color: var(--period-card-border-hover);
    border-left-color: #D97706;
    background: var(--period-card-bg-hover);
}
.bento-pill-amber .bento-action-icon {
    background: #FEF3C7;
    color: #D97706;
}
.cta-amber {
    background: #D97706;
    color: #FFFFFF !important;
}
.bento-pill-amber:hover .cta-amber {
    background: #B45309;
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
    background: var(--period-card-border);
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
    background: var(--period-accent);
    border: 2px solid #FFFFFF;
    box-shadow: 0 0 0 2px var(--period-dot-ring);
}

/* Quote Box (Bottom Right) */
.bento-quote-box {
    background: rgba(255, 255, 255, 0.72);
    border: 1px solid var(--period-card-border);
    border-left: 3px solid var(--period-accent);
    border-radius: 12px;
    padding: 0.85rem 1rem;
    font-size: 0.78rem;
    color: #475569;
    font-style: italic;
    margin-top: 1rem;
}

/* Temporary Administrator Notice Banner (Auto-hide ~5s) */
.officer-notices-container {
    transition: opacity 0.4s ease, transform 0.4s ease, max-height 0.45s ease, margin 0.45s ease;
    overflow: hidden;
}
.officer-notices-container.is-hiding {
    opacity: 0;
    transform: translateY(-6px);
    pointer-events: none;
}
.officer-notice-progress {
    height: 3px;
    background: rgba(243, 112, 33, 0.16);
    border-radius: 999px;
    overflow: hidden;
    margin-top: 0.65rem;
}
.officer-notice-progress-bar {
    height: 100%;
    width: 100%;
    background: linear-gradient(90deg, #F37021 0%, #FB923C 100%);
    transform-origin: left center;
    animation: officerNoticeCountdown 5s linear forwards;
}
.officer-notice-card.officer-notice-approved {
    background: linear-gradient(135deg, #FFFFFF 0%, #F0FDF4 100%) !important;
    border-color: #86EFAC !important;
    box-shadow: 0 6px 20px rgba(22, 163, 74, 0.12) !important;
}
.officer-notice-card.officer-notice-approved .officer-notice-progress {
    background: rgba(22, 163, 74, 0.16);
}
.officer-notice-card.officer-notice-approved .officer-notice-progress-bar {
    background: linear-gradient(90deg, #16A34A 0%, #22C55E 100%);
}
@keyframes officerNoticeCountdown {
    from { transform: scaleX(1); }
    to { transform: scaleX(0); }
}
</style>
@endpush

@section('content')
@php
    $periodStatus = isset($currentPeriod) && $currentPeriod ? $currentPeriod->currentStatus() : 'closed';
    $periodMessage = isset($currentPeriod) && $currentPeriod ? $currentPeriod->status_message : 'Periode pengisian telah ditutup';
    $isPeriodOpen = isset($currentPeriod) && $currentPeriod && $currentPeriod->isOpen();
    $today = \Carbon\Carbon::today();
    $periodThemeClass = match ($periodStatus) {
        'active' => 'period-theme-active',
        'not_started' => 'period-theme-not-started',
        default => 'period-theme-closed',
    };
@endphp
<div class="bento-dashboard-wrapper {{ $periodThemeClass }}">

    <!-- ==============================================================
         1. NOTIFIKASI / PESAN KHUSUS DARI ADMINISTRATOR (TAMPIL SEMENTARA ~5 DETIK)
         ============================================================== -->
    @if(isset($officerNotices) && $officerNotices->isNotEmpty())
        <div id="officerNoticesBanner" class="mb-3 officer-notices-container" data-auto-hide-ms="5000">
            @foreach($officerNotices as $notice)
                @php
                    $isApprovedNotice = ($notice->type === 'report_approved')
                        || str_contains(strtolower((string) $notice->title), 'disetujui')
                        || str_contains(strtolower((string) $notice->title), '(acc)');
                    $noticeAccent = $isApprovedNotice ? '#16A34A' : '#F37021';
                    $noticeShadow = $isApprovedNotice ? 'rgba(22, 163, 74, 0.25)' : 'rgba(243, 112, 33, 0.25)';
                    $noticeDivider = $isApprovedNotice ? 'rgba(22, 163, 74, 0.18)' : 'rgba(243, 112, 33, 0.12)';
                @endphp
                <div class="p-3 mb-3 shadow-sm officer-notice-card {{ $isApprovedNotice ? 'officer-notice-approved' : '' }}" style="background: var(--period-card-bg); border: 1px solid var(--period-card-border); border-radius: 16px; box-shadow: var(--bps-card-shadow);">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-1.5">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge text-white px-2.5 py-1" style="font-size: 0.72rem; background: {{ $noticeAccent }}; border-radius: 999px;">
                                <i class="bi {{ $isApprovedNotice ? 'bi-check-circle-fill' : 'bi-megaphone-fill' }} me-1"></i> Pesan Administrator
                            </span>
                            @if(!$notice->is_read)
                                <span class="badge {{ $isApprovedNotice ? 'bg-success text-white' : 'bg-warning text-dark' }} px-2 py-0.5 rounded-pill" style="font-size: 0.65rem; font-weight: 700;">Baru</span>
                            @endif
                            <span class="fw-bold text-dark small">{{ $notice->title }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <span class="badge {{ $isApprovedNotice ? 'bg-success-subtle text-success border-success-subtle' : 'bg-light text-secondary' }} border rounded-pill px-2 py-0.5 officer-notice-timer" style="font-size: 0.65rem;" title="Pesan tampil sementara 5 detik">
                                5 dtk
                            </span>
                            <span class="text-muted" style="font-size: 0.72rem;">
                                <i class="bi bi-clock me-1"></i> {{ $notice->created_at->diffForHumans() }}
                            </span>
                            <button type="button" class="btn-close" style="font-size: 0.65rem;" aria-label="Tutup pesan sementara" title="Tutup" onclick="dismissOfficerNoticesBanner()"></button>
                        </div>
                    </div>
                    <div class="text-dark small my-2" style="line-height: 1.5; white-space: pre-line;">
                        {{ $notice->message }}
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 mt-2 border-top" style="border-color: {{ $noticeDivider }} !important;">
                        <div class="d-flex align-items-center gap-2">
                            @if(str_contains($notice->type, 'report'))
                                <a href="{{ route('petugas.reports.index') }}" class="btn btn-sm py-1.5 px-3 rounded-pill text-white fw-bold d-inline-flex align-items-center gap-1.5 shadow-sm" style="font-size: 0.76rem; background: {{ $noticeAccent }}; border: none; box-shadow: 0 4px 12px {{ $noticeShadow }};">
                                    <i class="bi bi-clipboard2-check-fill"></i>
                                    <span>Buka Menu Laporan</span>
                                </a>
                            @elseif(str_contains($notice->type, 'bast'))
                                <a href="{{ route('petugas.basts.respondents.index') }}" class="btn btn-sm py-1.5 px-3 rounded-pill text-white fw-bold d-inline-flex align-items-center gap-1.5 shadow-sm" style="font-size: 0.76rem; background: {{ $noticeAccent }}; border: none; box-shadow: 0 4px 12px {{ $noticeShadow }};">
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
                    <div class="officer-notice-progress" aria-hidden="true">
                        <div class="officer-notice-progress-bar"></div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif



    <!-- ==============================================================
         2. STATUS PERIODE PENGISIAN SPKRT (Req 4, 5, 6, 8, 10)
         ============================================================== -->
    <div class="mb-4">
        <div class="bento-card p-3.5 border" style="border-radius: 20px; {{ $periodStatus === 'active' ? 'border-color: #86EFAC !important; background: linear-gradient(135deg, #FFFFFF 0%, #F0FDF4 100%);' : ($periodStatus === 'not_started' ? 'border-color: #FDE68A !important; background: linear-gradient(135deg, #FFFFFF 0%, #FFFBEB 100%);' : 'border-color: #FECACA !important; background: linear-gradient(135deg, #FFFFFF 0%, #FEF2F2 100%);') }}">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 p-2.5 text-center text-white flex-shrink-0" style="width: 52px; height: 52px; display: flex; align-items: center; justify-content: center; background: {{ $periodStatus === 'active' ? '#16A34A' : ($periodStatus === 'not_started' ? '#D97706' : '#DC2626') }}; box-shadow: 0 4px 12px {{ $periodStatus === 'active' ? 'rgba(22, 163, 74, 0.25)' : ($periodStatus === 'not_started' ? 'rgba(217, 119, 6, 0.25)' : 'rgba(220, 38, 38, 0.25)') }};">
                        @if($periodStatus === 'active')
                            <i class="bi bi-calendar-check-fill fs-4"></i>
                        @elseif($periodStatus === 'not_started')
                            <i class="bi bi-clock-history fs-4"></i>
                        @else
                            <i class="bi bi-lock-fill fs-4"></i>
                        @endif
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <span class="badge text-uppercase fw-bold px-2.5 py-1 rounded-pill" style="font-size: 0.72rem; letter-spacing: 0.03em; background: {{ $periodStatus === 'active' ? '#16A34A' : ($periodStatus === 'not_started' ? '#D97706' : '#DC2626') }}; color: #FFFFFF;">
                                @if($periodStatus === 'active')
                                    <i class="bi bi-check-circle-fill me-1"></i> {{ $periodMessage }}
                                @elseif($periodStatus === 'not_started')
                                    <i class="bi bi-hourglass-split me-1"></i> {{ $periodMessage }}
                                @else
                                    <i class="bi bi-x-circle-fill me-1"></i> {{ $periodMessage }}
                                @endif
                            </span>
                            @if(isset($currentPeriod) && $currentPeriod)
                                <span class="fw-bolder text-dark" style="font-size: 0.95rem;">
                                    {{ $currentPeriod->activity_name }}
                                </span>
                            @else
                                <span class="fw-bolder text-dark" style="font-size: 0.95rem;">
                                    Periode Pengisian SPKRT
                                </span>
                            @endif
                        </div>

                        <div class="text-secondary small d-flex flex-wrap align-items-center gap-2" style="font-size: 0.78rem;">
                            @if(isset($currentPeriod) && $currentPeriod)
                                <span>
                                    <i class="bi bi-calendar-range me-1 text-primary"></i>
                                    Jadwal: <strong>{{ $currentPeriod->start_date ? $currentPeriod->start_date->format('d M Y') : '-' }}</strong> s.d. <strong>{{ $currentPeriod->end_date ? $currentPeriod->end_date->format('d M Y') : '-' }}</strong>
                                </span>
                                @if($periodStatus === 'active')
                                    <span class="text-success fw-bold">
                                        &bull; Sisa waktu: {{ $today->diffInDays($currentPeriod->end_date, false) }} hari lagi
                                    </span>
                                @elseif($periodStatus === 'not_started')
                                    <span class="text-warning-emphasis fw-bold">
                                        &bull; Dibuka dalam {{ $today->diffInDays($currentPeriod->start_date) }} hari lagi
                                    </span>
                                @else
                                    <span class="text-danger fw-bold">
                                        &bull; Sistem otomatis mengunci formulir
                                    </span>
                                @endif
                            @else
                                <span>Belum ada jadwal periode pengisian aktif yang ditetapkan oleh Administrator.</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    @if($isPeriodOpen)
                        <a href="{{ route('petugas.basts.respondents.index') }}" class="btn btn-sm btn-success rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-sm">
                            <i class="bi bi-pencil-square"></i>
                            <span>Buka Formulir SPKRT</span>
                        </a>
                    @else
                        <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill small">
                            <i class="bi bi-lock-fill me-1 text-danger"></i> Formulir Dikunci
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ==============================================================
         3. MENU AKSI LAPANGAN (DIKONTROL PER MENU OLEH ADMIN / PERIODE)
         ============================================================== -->
    @php
        $berandaMenusMap = $berandaMenuState['menus'] ?? [];
        $showRespondenMenu = (bool) ($berandaMenusMap['tanda_terima_responden']['is_visible'] ?? false);
        $showBlankoMenu = (bool) ($berandaMenusMap['tanda_terima_blanko']['is_visible'] ?? false);
        $showTerisiMenu = (bool) ($berandaMenusMap['bast_dokumen_terisi']['is_visible'] ?? false);
        $showLaporanPerjadinMenu = (bool) ($berandaMenusMap['laporan_perjadin']['is_visible'] ?? false);

        $visibleMenuCount = (int) $showRespondenMenu + (int) $showBlankoMenu + (int) $showTerisiMenu + (int) $showLaporanPerjadinMenu;
        $showMenuAksiLapangan = $visibleMenuCount > 0;
        $actionCardColClass = match ($visibleMenuCount) {
            1 => 'col-12',
            2 => 'col-12 col-md-6',
            3 => 'col-12 col-lg-4',
            default => 'col-12 col-sm-6 col-lg-3',
        };
    @endphp
    @if($showMenuAksiLapangan)
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
            <h5 class="bento-card-title-sm mb-0">
                <i class="bi bi-lightning-charge-fill me-1.5" style="color: var(--bps-orange);"></i> Menu Aksi Lapangan
            </h5>
            @if(!empty($berandaMenuState['status_label']))
                <span class="badge {{ $berandaMenuState['status_badge_class'] ?? 'bg-success-subtle text-success' }} rounded-pill px-2.5 py-1" style="font-size: 0.68rem;">
                    {{ $berandaMenuState['status_label'] }}
                </span>
            @endif
        </div>

        <div class="row g-2 g-lg-3">
            {{-- 1. Tanda Terima Responden (BAST) --}}
            @if($showRespondenMenu)
            <div class="{{ $actionCardColClass }}">
                <a href="{{ route('petugas.basts.respondents.index') }}" class="bento-action-pill bento-pill-green">
                    <div class="bento-action-icon">
                        <i class="bi bi-file-earmark-check-fill"></i>
                    </div>
                    <div class="bento-action-text">
                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                            <span class="bento-action-title">Tanda Terima Responden (BAST)</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle bento-action-badge">
                                {{ $totalRespondentBasts }} Terbit
                            </span>
                            @if(!$isPeriodOpen)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle bento-action-badge">
                                    <i class="bi bi-lock-fill"></i> Terkunci
                                </span>
                            @endif
                        </div>
                        <span class="bento-action-sub">Input tanda tangan &amp; serah terima responden</span>
                    </div>
                    <span class="bento-action-cta cta-green">
                        <span>Buka</span>
                        <i class="bi bi-arrow-right-short" style="font-size: 1.15rem; line-height: 1;"></i>
                    </span>
                </a>
            </div>
            @endif

            {{-- 2. Tanda Terima Blanko Petugas --}}
            @if($showBlankoMenu)
            <div class="{{ $actionCardColClass }}">
                <a href="{{ route('petugas.basts.officers.index', ['type' => 'kantor_ke_petugas']) }}" class="bento-action-pill bento-pill-blue">
                    <div class="bento-action-icon">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                    <div class="bento-action-text">
                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                            <span class="bento-action-title">Tanda Terima Blanko</span>
                            @if(($pendingSignBlankoBastsCount ?? 0) > 0)
                                <span class="badge bg-danger text-white border bento-action-badge">
                                    <i class="bi bi-pen me-1"></i> {{ $pendingSignBlankoBastsCount }} TTD
                                </span>
                            @else
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle bento-action-badge">
                                    {{ $totalBlankoBasts ?? 0 }} Sah
                                </span>
                            @endif
                        </div>
                        <span class="bento-action-sub">Penerimaan blanko kuesioner dari kantor</span>
                    </div>
                    <span class="bento-action-cta cta-blue">
                        <span>Buka</span>
                        <i class="bi bi-arrow-right-short" style="font-size: 1.15rem; line-height: 1;"></i>
                    </span>
                </a>
            </div>
            @endif

            {{-- 3. BAST Dokumen Terisi Petugas --}}
            @if($showTerisiMenu)
            <div class="{{ $actionCardColClass }}">
                <a href="{{ route('petugas.basts.officers.index', ['type' => 'petugas_ke_kantor']) }}" class="bento-action-pill bento-pill-amber">
                    <div class="bento-action-icon">
                        <i class="bi bi-file-earmark-arrow-up-fill"></i>
                    </div>
                    <div class="bento-action-text">
                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                            <span class="bento-action-title">BAST Dokumen Terisi</span>
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle bento-action-badge">
                                {{ $totalTerisiBasts ?? 0 }} Sah
                            </span>
                        </div>
                        <span class="bento-action-sub">Serah terima kuesioner &amp; berkas ke kantor</span>
                    </div>
                    <span class="bento-action-cta cta-amber">
                        <span>Buka</span>
                        <i class="bi bi-arrow-right-short" style="font-size: 1.15rem; line-height: 1;"></i>
                    </span>
                </a>
            </div>
            @endif

            {{-- 4. Laporan Perjadin --}}
            @if($showLaporanPerjadinMenu)
            <div class="{{ $actionCardColClass }}">
                <a href="{{ route('petugas.reports.index') }}" class="bento-action-pill bento-pill-purple">
                    <div class="bento-action-icon">
                        <i class="bi bi-clipboard2-check-fill"></i>
                    </div>
                    <div class="bento-action-text">
                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                            <span class="bento-action-title">Laporan Perjadin</span>
                            <span class="badge {{ $reportsRevision > 0 ? 'bg-danger text-white' : ($reportsApproved > 0 ? 'bg-success-subtle text-success border' : 'bg-secondary-subtle text-secondary border') }} bento-action-badge">
                                {{ $reportsRevision > 0 ? $reportsRevision . ' Revisi' : ($reportsApproved > 0 ? $reportsApproved . ' ACC' : ($reportsSubmitted > 0 ? $reportsSubmitted . ' Menunggu' : 'Draft')) }}
                            </span>
                        </div>
                        <span class="bento-action-sub">Ajukan &amp; pantau persetujuan laporan perjadin</span>
                    </div>
                    <span class="bento-action-cta cta-purple">
                        <span>Buka</span>
                        <i class="bi bi-arrow-right-short" style="font-size: 1.15rem; line-height: 1;"></i>
                    </span>
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- ==============================================================
         5. BOTTOM ROW: KEGIATAN AKTIF (MUNCUL SAAT ADA TUGAS DARI ADMIN) & AGENDA BPS
         ============================================================== -->
    @php
        $showPenugasanLapangan = isset($activeTasks) && $activeTasks->isNotEmpty();
        $showJadwalAgenda = true;
    @endphp
    <div class="row g-4">
        @if($showPenugasanLapangan)
        {{-- Penugasan Lapangan Aktif --}}
        <div class="{{ $showJadwalAgenda ? 'col-lg-7' : 'col-12' }}">
            <div class="bento-card h-100">
                <div class="bento-card-header-sm">
                    <h5 class="bento-card-title-sm">
                        <i class="bi bi-list-check me-1.5" style="color: var(--bps-orange);"></i> Penugasan Lapangan Anda
                    </h5>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <a href="{{ route('petugas.assignments.index') }}" class="bento-card-link-sm">
                            Semua Penugasan &rarr;
                        </a>
                    </div>
                </div>

                @if(isset($activeTasks) && $activeTasks->isNotEmpty())
                    <div class="d-flex flex-column gap-2.5">
                        @foreach($activeTasks as $task)
                            <div class="p-3 rounded-4 border d-flex flex-wrap justify-content-between align-items-center gap-2 transition" style="background: rgba(255, 255, 255, 0.72); border-color: var(--period-card-border) !important;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 p-2 text-white text-center" style="background: var(--bps-orange); width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-briefcase-fill"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small">{{ $task->assignment ? $task->assignment->title : 'Tugas #' . $task->id }}</div>
                                        <div class="text-muted" style="font-size: 0.76rem;">
                                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $task->assignment ? $task->assignment->location : 'Kolaka Utara' }}
                                            @if($task->assignment && $task->assignment->end_date)
                                                &bull; Batas: {{ $task->assignment->end_date->format('d/m/Y') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @if($task->assignment)
                                        <span class="badge {{ $task->assignment->visibility_status_badge_class }} px-2 py-1" style="font-size: 0.68rem;">
                                            {{ $task->assignment->visibility_status_label }}
                                        </span>
                                    @endif
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
                        <i class="bi {{ isset($isAssignmentPortalEnabled) && !$isAssignmentPortalEnabled ? 'bi-lock-fill text-danger' : 'bi-clipboard2-check text-secondary' }} display-6 mb-2 opacity-50 d-block"></i>
                        <div class="small fw-semibold text-dark">
                            {{ $assignmentEmptyMessage ?? 'Belum ada penugasan lapangan yang tersedia saat ini.' }}
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.72rem;">
                            @if(isset($isAssignmentPortalEnabled) && !$isAssignmentPortalEnabled)
                                Silakan tunggu konfirmasi dari Administrator untuk membuka akses penugasan lapangan.
                            @else
                                Penugasan baru akan tampil otomatis ketika diaktifkan Admin atau periode penugasan sedang berjalan.
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Kanan: Timeline Jadwal & Agenda Lapangan BPS -->
        @if($showJadwalAgenda)
        <div class="{{ $showPenugasanLapangan ? 'col-lg-5' : 'col-12' }}">
            <div class="bento-card h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="bento-card-header-sm">
                        <h5 class="bento-card-title-sm">
                            <i class="bi bi-calendar3 me-1.5" style="color: var(--bps-orange);"></i> Jadwal &amp; Agenda BPS
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
                                        <i class="bi bi-clock me-1"></i> {{ $sch->start_time ? $sch->start_time->translatedFormat('d M Y') : '-' }}
                                        @if($sch->location)
                                            &bull; <i class="bi bi-geo-alt-fill me-0.5 text-danger"></i> {{ $sch->location }}
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-x display-6 mb-2 text-secondary opacity-50 d-block"></i>
                            <div class="small fw-semibold">Tidak ada agenda kegiatan terdekat.</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Agenda pelatihan atau rapat koordinasi akan terjadwal di sini.</div>
                        </div>
                    @endif
                </div>

                <div class="bento-quote-box">
                    <i class="bi bi-quote text-muted me-1.5 opacity-50"></i>
                    BPS Kolut Bersih, BerAKHLAK, Bersinergi dan Hadir Melayani.
                    <div class="fw-semibold text-dark mt-1" style="font-size: 0.72rem;">&mdash; BPS Kabupaten Kolaka Utara</div>
                </div>
            </div>
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const banner = document.getElementById('officerNoticesBanner');
    if (!banner) return;

    const durationMs = parseInt(banner.dataset.autoHideMs || '5000', 10) || 5000;
    let remainingSec = Math.max(1, Math.ceil(durationMs / 1000));
    const timerBadges = banner.querySelectorAll('.officer-notice-timer');
    let dismissed = false;

    window.dismissOfficerNoticesBanner = function () {
        if (dismissed) return;
        dismissed = true;
        clearInterval(countdownInterval);
        clearTimeout(hideTimeout);

        banner.style.maxHeight = banner.scrollHeight + 'px';
        void banner.offsetHeight;
        banner.classList.add('is-hiding');
        banner.style.maxHeight = '0px';
        banner.style.marginBottom = '0px';

        setTimeout(function () {
            banner.classList.add('d-none');
        }, 450);
    };

    const countdownInterval = setInterval(function () {
        remainingSec = Math.max(0, remainingSec - 1);
        timerBadges.forEach(function (el) {
            el.textContent = remainingSec > 0 ? remainingSec + ' dtk' : 'Menutup...';
        });
    }, 1000);

    const hideTimeout = setTimeout(window.dismissOfficerNoticesBanner, durationMs);
})();
</script>
@endpush

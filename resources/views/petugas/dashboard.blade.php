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

/* Quick Action Bento Cards */
.bento-action-pill {
    display: flex;
    align-items: center;
    gap: 0.95rem;
    padding: 0.95rem 1.25rem;
    border-radius: 18px;
    text-decoration: none;
    border: 1px solid transparent;
    transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.02);
    min-height: 74px;
    height: 100%;
}
.bento-action-pill:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
}
.bento-action-icon {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}
.bento-action-text {
    flex-grow: 1;
    min-width: 0;
}
.bento-action-title {
    font-size: 0.92rem;
    font-weight: 750;
    color: #0F172A;
    line-height: 1.25;
    white-space: normal;
    word-break: normal;
    display: block;
}
.bento-action-badge {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.2rem 0.6rem;
    border-radius: 999px;
    white-space: nowrap;
    line-height: 1.2;
    display: inline-block;
}
.bento-action-arrow {
    color: #94A3B8;
    font-size: 0.9rem;
    margin-left: auto;
    flex-shrink: 0;
    transition: transform 0.2s ease, color 0.2s ease;
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
         3. MENU AKSI LAPANGAN (4 TOMBOL UTAMA)
         ============================================================== -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2.5">
            <h5 class="bento-card-title-sm mb-0">
                <i class="bi bi-lightning-charge-fill me-1.5" style="color: var(--bps-orange);"></i> Menu Aksi Lapangan
            </h5>
        </div>

        <div class="row g-2.5 g-lg-3">
            <!-- 1. Tanda Terima Responden -->
            <div class="col-12 col-lg-3">
                <a href="{{ route('petugas.basts.respondents.index') }}" class="bento-action-pill bento-pill-green">
                    <div class="bento-action-icon">
                        <i class="bi bi-file-earmark-check-fill"></i>
                    </div>
                    <div class="bento-action-text">
                        <span class="bento-action-title">Tanda Terima Responden</span>
                        <div class="mt-0.5">
                            <span class="badge bg-success-subtle text-success border border-success-subtle bento-action-badge">
                                {{ $totalRespondentBasts }} Terbit
                            </span>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right bento-action-arrow"></i>
                </a>
            </div>

            <!-- 2. Tanda Terima Petugas -->
            <div class="col-12 col-lg-3">
                <a href="{{ route('petugas.basts.officers.index', ['type' => 'kantor_ke_petugas']) }}" class="bento-action-pill bento-pill-blue">
                    <div class="bento-action-icon">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                    <div class="bento-action-text">
                        <span class="bento-action-title">Tanda Terima Petugas</span>
                        <div class="mt-0.5">
                            @if(($pendingSignBlankoBastsCount ?? 0) > 0)
                                <span class="badge bg-danger text-white border bento-action-badge">
                                    <i class="bi bi-pen me-1"></i> {{ $pendingSignBlankoBastsCount }} TTD
                                </span>
                            @else
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle bento-action-badge">
                                    {{ $totalBlankoBasts }} Sah
                                </span>
                            @endif
                        </div>
                    </div>
                    <i class="bi bi-chevron-right bento-action-arrow"></i>
                </a>
            </div>

            <!-- 3. Laporan Perjadin -->
            <div class="col-12 col-lg-3">
                <a href="{{ route('petugas.reports.index') }}" class="bento-action-pill bento-pill-purple">
                    <div class="bento-action-icon">
                        <i class="bi bi-clipboard2-check-fill"></i>
                    </div>
                    <div class="bento-action-text">
                        <span class="bento-action-title">Laporan Perjadin</span>
                        <div class="mt-0.5">
                            <span class="badge {{ $reportsRevision > 0 ? 'bg-danger text-white' : ($reportsApproved > 0 ? 'bg-success-subtle text-success border' : 'bg-secondary-subtle text-secondary border') }} bento-action-badge">
                                {{ $reportsRevision > 0 ? $reportsRevision . ' Revisi' : ($reportsApproved > 0 ? $reportsApproved . ' ACC' : ($reportsSubmitted > 0 ? $reportsSubmitted . ' Menunggu' : 'Draft')) }}
                            </span>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right bento-action-arrow"></i>
                </a>
            </div>

            <!-- 4. BAST Dokumen Terisi -->
            <div class="col-12 col-lg-3">
                <a href="{{ route('petugas.basts.officers.index', ['type' => 'petugas_ke_kantor']) }}" class="bento-action-pill bento-pill-amber">
                    <div class="bento-action-icon">
                        <i class="bi bi-file-earmark-arrow-up-fill"></i>
                    </div>
                    <div class="bento-action-text">
                        <span class="bento-action-title">BAST Dokumen Terisi</span>
                        <div class="mt-0.5">
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle bento-action-badge">
                                {{ $totalTerisiBasts ?? 0 }} Sah
                            </span>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right bento-action-arrow"></i>
                </a>
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
                        <i class="bi bi-list-check me-1.5" style="color: var(--bps-orange);"></i> Penugasan Lapangan Anda
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
                        <i class="bi bi-clipboard2-check display-6 mb-2 text-secondary opacity-50 d-block"></i>
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
                    Kualitas data statistik berawal dari ketelitian dan integritas di setiap kunjungan lapangan.
                    <div class="fw-semibold text-dark mt-1" style="font-size: 0.72rem;">&mdash; BPS Kabupaten Kolaka Utara</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

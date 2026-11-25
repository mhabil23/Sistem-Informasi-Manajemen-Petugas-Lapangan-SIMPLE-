@extends('layouts.app')

@section('title', 'Jadwal Kegiatan Lapangan')
@section('page_title', 'Jadwal Kegiatan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('petugas.dashboard') }}">Petugas</a></li>
    <li class="breadcrumb-item active" aria-current="page">Jadwal Kegiatan</li>
@endsection

@push('styles')
<style>
    .bento-card {
        background: #FFFFFF;
        border-radius: 18px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
        padding: 1.15rem 1.2rem;
        overflow: hidden;
        transition: all 0.2s ease;
    }
    @media (min-width: 768px) {
        .bento-card {
            padding: 1.4rem 1.5rem;
        }
    }
    .bento-card:hover {
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.07);
        transform: translateY(-2px);
    }
    .bento-stat-card {
        background: #FFFFFF;
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        padding: 1.05rem 1.2rem;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
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
    .schedule-card-header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.5rem;
        margin-bottom: 0.85rem;
    }
    .schedule-badges {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.4rem;
        max-width: 100%;
    }
    .schedule-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        max-width: 100%;
        white-space: normal;
        text-align: left;
        line-height: 1.25;
        font-size: 0.72rem;
        padding: 0.36rem 0.7rem;
    }
    .schedule-title {
        font-size: 1.02rem;
        font-weight: 700;
        color: #0F172A;
        line-height: 1.35;
        margin-bottom: 0.7rem;
        overflow-wrap: anywhere;
    }
    .schedule-meta-row {
        display: flex;
        align-items: flex-start;
        gap: 0.55rem;
        margin-bottom: 0.45rem;
        min-width: 0;
        font-size: 0.84rem;
        line-height: 1.4;
    }
    .schedule-meta-row i {
        flex-shrink: 0;
        margin-top: 0.15rem;
    }
    .schedule-meta-text {
        min-width: 0;
        overflow-wrap: anywhere;
    }
    .schedule-notes-box {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 0.7rem 0.85rem;
        font-size: 0.78rem;
        color: #475569;
        margin-top: 0.75rem;
        overflow-wrap: anywhere;
    }
</style>
@endpush

@section('content')
<div class="pb-3">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-calendar3 text-primary"></i> Jadwal &amp; Agenda Lapangan Saya
            </h3>
            <p class="text-muted small mb-0">Agenda pengarahan, jadwal kunjungan lapangan, dan pertemuan koordinasi penugasan Anda.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('petugas.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-4">
            <div class="bento-stat-card">
                <div class="bento-stat-icon" style="background: #EFF6FF; color: #005BAC;">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold" style="font-size: 0.76rem;">TOTAL AGENDA</div>
                    <div class="fw-bold text-dark" style="font-size: 1.4rem;">{{ number_format($totalSchedules) }}</div>
                    <div class="text-muted small" style="font-size: 0.72rem;">Semua jadwal Anda</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="bento-stat-card">
                <div class="bento-stat-icon" style="background: #FFF4ED; color: #F37021;">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold" style="font-size: 0.76rem;">AGENDA TERDEKAT</div>
                    <div class="fw-bold" style="font-size: 1.4rem; color: #F37021;">{{ number_format($upcomingSchedules) }}</div>
                    <div class="text-muted small" style="font-size: 0.72rem;">Wajib dihadiri</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule List / Cards -->
    <div class="row g-3">
        @forelse($schedules as $item)
            @php
                $statusBadge = match ($item->status) {
                    'completed' => ['class' => 'bg-success-subtle text-success border-success-subtle', 'icon' => 'bi-check-circle-fill', 'label' => 'Selesai'],
                    'in_progress' => ['class' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle', 'icon' => 'bi-hourglass-split', 'label' => 'Berlangsung'],
                    default => ['class' => 'bg-primary-subtle text-primary border-primary-subtle', 'icon' => 'bi-clock', 'label' => 'Terjadwal'],
                };
            @endphp
            <div class="col-12 col-lg-6">
                <div class="bento-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="schedule-card-header">
                            <span class="badge bg-light text-primary border rounded-pill fw-semibold schedule-pill">
                                <i class="bi bi-calendar-date"></i>
                                <span>{{ $item->start_time ? $item->start_time->translatedFormat('l, d F Y') : '-' }}</span>
                            </span>
                            <div class="schedule-badges">
                                @if(($item->officer_status ?? 'active') === 'inactive')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill fw-bold schedule-pill" title="Status Penugasan Anda: Tidak Aktif">
                                        <i class="bi bi-person-x-fill"></i>
                                        <span>Tidak Aktif</span>
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fw-bold schedule-pill" title="Status Penugasan Anda: Aktif Bertugas">
                                        <i class="bi bi-person-check-fill"></i>
                                        <span>Aktif Bertugas</span>
                                    </span>
                                @endif

                                <span class="badge {{ $statusBadge['class'] }} border rounded-pill fw-bold schedule-pill">
                                    <i class="bi {{ $statusBadge['icon'] }}"></i>
                                    <span>{{ $statusBadge['label'] }}</span>
                                </span>
                            </div>
                        </div>

                        <h5 class="schedule-title">{{ $item->title }}</h5>

                        <div class="schedule-meta-row text-primary fw-semibold">
                            <i class="bi bi-clock"></i>
                            <span class="schedule-meta-text">
                                {{ $item->start_time ? $item->start_time->format('H:i') : '-' }}
                                @if($item->end_time)
                                    - {{ $item->end_time->format('H:i') }} WIB
                                @endif
                            </span>
                        </div>

                        <div class="schedule-meta-row text-secondary">
                            <i class="bi bi-geo-alt-fill text-danger"></i>
                            <span class="schedule-meta-text"><strong>Lokasi:</strong> {{ $item->location ?: '-' }}</span>
                        </div>

                        @if($item->assignment)
                            <div class="schedule-meta-row text-muted mb-0">
                                <i class="bi bi-briefcase"></i>
                                <span class="schedule-meta-text"><strong>Kegiatan:</strong> {{ $item->assignment->title }}</span>
                            </div>
                        @endif
                    </div>

                    @if($item->notes)
                        <div class="schedule-notes-box">
                            <i class="bi bi-info-circle text-primary me-1"></i>
                            <strong>Catatan:</strong> {{ $item->notes }}
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="bento-card text-center py-5">
                    <i class="bi bi-calendar-x display-5 text-secondary opacity-50 mb-3 d-block"></i>
                    <h6 class="fw-bold text-dark">Belum Ada Agenda Jadwal</h6>
                    <p class="text-muted small mx-auto mb-0" style="max-width: 420px;">
                        Tidak ada jadwal kegiatan atau briefing aktif untuk penugasan Anda saat ini.
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    @if($schedules->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $schedules->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

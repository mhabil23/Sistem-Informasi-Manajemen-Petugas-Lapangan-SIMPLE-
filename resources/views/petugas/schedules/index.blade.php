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
        border: 1px solid rgba(226, 232, 240, 0.85);
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
        transition: all 0.2s ease;
    }
    .bento-card:hover {
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        transform: translateY(-2px);
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
</style>
@endpush

@section('content')
<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="bi bi-calendar3 text-primary"></i> Jadwal &amp; Agenda Lapangan Saya
        </h3>
        <p class="text-muted small mb-0">Agenda pengarahan, jadwal kunjungan lapangan, dan pertemuan koordinasi penugasan Anda.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('petugas.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold">
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
                <div class="fw-bold text-bps-orange" style="font-size: 1.4rem; color: #F37021;">{{ number_format($upcomingSchedules) }}</div>
                <div class="text-muted small" style="font-size: 0.72rem;">Wajib dihadiri</div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule List / Cards -->
<div class="row g-3">
    @forelse($schedules as $item)
        <div class="col-lg-6">
            <div class="bento-card h-100 p-3.5 p-md-4">
                <div class="d-flex justify-content-between align-items-start mb-2.5">
                    <span class="badge bg-light text-primary border rounded-pill px-3 py-1.5 fw-semibold small">
                        <i class="bi bi-calendar-date me-1"></i>
                        {{ $item->start_time->translatedFormat('l, d F Y') }}
                    </span>
                    @if($item->status === 'completed')
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold">
                            <i class="bi bi-check-circle-fill me-1"></i> Selesai
                        </span>
                    @elseif($item->status === 'in_progress')
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 fw-bold">
                            <i class="bi bi-hourglass-split me-1"></i> Berlangsung
                        </span>
                    @else
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-bold">
                            <i class="bi bi-clock me-1"></i> Terjadwal
                        </span>
                    @endif
                </div>

                <h5 class="fw-bold text-dark mb-2" style="font-size: 1.02rem;">{{ $item->title }}</h5>

                <div class="d-flex align-items-center gap-2 text-primary fw-semibold small mb-2">
                    <i class="bi bi-clock"></i>
                    <span>{{ $item->start_time->format('H:i') }} - {{ $item->end_time->format('H:i') }} WIB</span>
                </div>

                <div class="d-flex align-items-center gap-2 text-secondary small mb-2">
                    <i class="bi bi-geo-alt-fill text-danger"></i>
                    <span><strong>Lokasi:</strong> {{ $item->location }}</span>
                </div>

                @if($item->assignment)
                    <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                        <i class="bi bi-briefcase"></i>
                        <span><strong>Kegiatan:</strong> {{ $item->assignment->title }}</span>
                    </div>
                @endif

                @if($item->notes)
                    <div class="bg-light rounded-3 p-2.5 small text-secondary border mt-3" style="font-size: 0.78rem;">
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
                <p class="text-muted small max-w-md mx-auto mb-0">
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
@endsection

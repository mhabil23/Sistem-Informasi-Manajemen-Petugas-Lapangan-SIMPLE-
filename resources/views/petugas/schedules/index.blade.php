@extends('layouts.app')

@section('title', 'Jadwal Kegiatan Lapangan')
@section('page_title', 'Jadwal Kegiatan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('petugas.dashboard') }}">Petugas</a></li>
    <li class="breadcrumb-item active" aria-current="page">Jadwal Kegiatan</li>
@endsection

@section('content')
<!-- Header -->
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h3 class="fw-bold text-dark mb-1">Jadwal & Agenda Lapangan Saya</h3>
        <p class="text-muted small mb-0">Berikut adalah agenda pengarahan, jadwal kunjungan lapangan, dan pertemuan koordinasi yang terkait dengan penugasan Anda.</p>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-4">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-regular fa-calendar-days"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Total Agenda</div>
                <div class="sp-stat-value">{{ number_format($totalSchedules) }}</div>
                <div class="sp-stat-desc">Semua jadwal Anda</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-4">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0e7ff; color: #4338ca;">
                <i class="fa-regular fa-clock"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Agenda Terdekat</div>
                <div class="sp-stat-value text-primary">{{ number_format($upcomingSchedules) }}</div>
                <div class="sp-stat-desc">Wajib dihadiri</div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule List / Cards -->
<div class="row g-4">
    @forelse($schedules as $item)
        <div class="col-lg-6">
            <div class="sp-card h-100 mb-0">
                <div class="sp-card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-light text-primary border small">
                            <i class="fa-regular fa-calendar me-1"></i>
                            {{ $item->start_time->translatedFormat('l, d F Y') }}
                        </span>
                        @if($item->status === 'completed')
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                Selesai
                            </span>
                        @elseif($item->status === 'in_progress')
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                Sedang Berlangsung
                            </span>
                        @else
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                Terjadwal
                            </span>
                        @endif
                    </div>

                    <h5 class="fw-bold text-dark mb-2">{{ $item->title }}</h5>

                    <div class="text-primary fw-semibold small mb-2">
                        <i class="fa-regular fa-clock me-1"></i>
                        {{ $item->start_time->format('H:i') }} - {{ $item->end_time->format('H:i') }} WIB
                    </div>

                    <div class="text-secondary small mb-2">
                        <i class="fa-solid fa-location-dot me-1 text-danger"></i>
                        <strong>Lokasi:</strong> {{ $item->location }}
                    </div>

                    @if($item->assignment)
                        <div class="text-muted small mb-2">
                            <i class="fa-solid fa-briefcase me-1 text-muted"></i>
                            <strong>Kegiatan:</strong> {{ $item->assignment->title }}
                        </div>
                    @endif

                    @if($item->notes)
                        <div class="bg-light rounded p-2 small text-muted border mt-3">
                            <strong>Instruksi / Catatan:</strong> {{ $item->notes }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="sp-card text-center py-5">
                <i class="fa-regular fa-calendar-check fa-3x mb-3 text-secondary opacity-50"></i>
                <h5 class="fw-bold text-dark">Belum Ada Agenda Jadwal</h5>
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


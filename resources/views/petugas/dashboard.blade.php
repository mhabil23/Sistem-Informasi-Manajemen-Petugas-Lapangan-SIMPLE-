@extends('layouts.app')

@section('title', 'Beranda Petugas Lapangan - SIMPLE')
@section('page_title', 'Beranda Petugas')

@section('content')
<div class="petugas-container">

    <!-- ==============================================================
         1. HERO WELCOME CARD (MOBILE-FIRST GREETING BANNER)
         ============================================================== -->
    <div class="petugas-hero-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-white text-primary fw-bold px-2.5 py-1" style="font-size: 0.72rem;">
                        <i class="fa-solid fa-id-badge me-1"></i> Mitra Lapangan BPS
                    </span>
                    <span class="text-white-50 small">&bull; @<span>{{ auth()->user()->username }}</span></span>
                </div>
                <h4 class="fw-bold mb-1 text-white">
                    Halo, {{ auth()->user()->name }} 👋
                </h4>
                <div class="text-white-50 small d-flex align-items-center gap-3 flex-wrap">
                    <span>
                        <i class="fa-solid fa-location-dot text-info me-1"></i>
                        {{ $nearestTaskDeadline->assignment->location ?? 'Wilayah Penugasan Kabupaten' }}
                    </span>
                    <span>
                        <i class="fa-solid fa-list-check text-warning me-1"></i>
                        Target: <strong class="text-white">{{ number_format($totalRespondents) }} / {{ number_format($targetRespondents) }}</strong> Ruta Selesai
                    </span>
                </div>
            </div>
            <div class="d-none d-sm-block text-sm-end">
                <div class="badge bg-white-subtle border border-light-subtle text-white px-3 py-2 rounded-3 text-start">
                    <div class="small fw-semibold text-white">
                        <i class="fa-regular fa-calendar-check text-info me-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </div>
                    <div class="text-white-50" style="font-size: 0.7rem;">Sistem SIMPLE BPS Terpadu</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==============================================================
         2. KOTAK INFORMASI TERBARU (LIVE BROADCAST & STATUS ALERT)
         ============================================================== -->
    <div class="petugas-notif-box mb-4">
        <div class="petugas-notif-header">
            <h5 class="petugas-notif-title d-flex align-items-center gap-2">
                <span class="pulsing-indicator">
                    <span class="pulsing-dot"></span>
                </span>
                <i class="fa-solid fa-bullhorn text-primary"></i>
                <span>Papan Informasi &amp; Status Kegiatan</span>
            </h5>
            <div class="text-muted small">
                <i class="fa-regular fa-bell me-1"></i> Notifikasi Aktif
            </div>
        </div>

        {{-- Pesan Khusus / Broadcast dari Administrator BPS --}}
        @if(isset($officerNotices) && $officerNotices->isNotEmpty())
            <div class="mb-3">
                @foreach($officerNotices as $notice)
                    <div class="p-3 mb-2 rounded-3 border {{ !$notice->is_read ? 'bg-danger-subtle border-danger' : 'bg-light border-secondary-subtle' }} shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                <span class="badge {{ !$notice->is_read ? 'bg-danger text-white' : 'bg-secondary text-white' }} px-2 py-1" style="font-size: 0.72rem;">
                                    <i class="fa-solid fa-bullhorn me-1"></i> Pesan dari Administrator
                                </span>
                                @if(!$notice->is_read)
                                    <span class="badge bg-warning text-dark px-1.5 py-0.5" style="font-size: 0.65rem;">
                                        Baru
                                    </span>
                                @endif
                                <span class="fw-bold text-dark small">{{ $notice->title }}</span>
                            </div>
                            <span class="text-muted" style="font-size: 0.68rem;">
                                <i class="fa-regular fa-clock me-1"></i> {{ $notice->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <div class="text-dark small my-1.5" style="line-height: 1.5; white-space: pre-line;">
                            {{ $notice->message }}
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 mt-2 border-top border-light-subtle">
                            <div class="d-flex align-items-center gap-2">
                                @if(str_contains($notice->type, 'report'))
                                    <a href="{{ route('petugas.reports.index') }}" class="btn btn-xs btn-sp-primary py-1 px-2.5" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-clipboard-user me-1"></i> Buka Menu Laporan
                                    </a>
                                @elseif(str_contains($notice->type, 'bast'))
                                    <a href="{{ route('petugas.basts.respondents.index') }}" class="btn btn-xs btn-success py-1 px-2.5" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-file-signature me-1"></i> Buka BAST Responden
                                    </a>
                                @endif
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                @if(!$notice->is_read)
                                    <form action="{{ route('petugas.notices.read', $notice) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-xs btn-outline-secondary py-1 px-2" style="font-size: 0.7rem;" title="Tandai Sudah Dibaca">
                                            <i class="fa-solid fa-check me-1"></i> Tandai Dibaca
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('petugas.notices.destroy', $notice) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pesan ini dari papan informasi?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-link text-danger p-0" style="font-size: 0.7rem;" title="Hapus Pesan">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <ul class="petugas-notif-list mb-0">
            <!-- Bullet 1: Penugasan Aktif & Deadline -->
            @if($nearestTaskDeadline && $nearestTaskDeadline->assignment)
                <li class="petugas-notif-item">
                    <span class="petugas-notif-bullet bullet-blue"></span>
                    <div>
                        <strong>{{ $nearestTaskDeadline->assignment->title }}</strong> &bull;
                        Wilayah: <span>{{ $nearestTaskDeadline->assignment->location }}</span>
                        @if($nearestTaskDeadline->assignment->end_date)
                            <span class="badge {{ $isUrgentDeadline ? 'bg-danger text-white' : 'bg-warning text-dark' }} ms-1 px-2 py-0.5">
                                <i class="fa-solid fa-hourglass-half me-1"></i>
                                Batas Waktu: {{ $nearestTaskDeadline->assignment->end_date->translatedFormat('d F Y') }}
                                ({{ $daysUntilDeadline > 0 ? 'Sisa ' . $daysUntilDeadline . ' Hari' : ($daysUntilDeadline === 0 ? 'Hari Ini Terakhir!' : 'Lewat Batas') }})
                            </span>
                        @endif
                    </div>
                </li>
            @else
                <li class="petugas-notif-item">
                    <span class="petugas-notif-bullet bullet-blue"></span>
                    <div>
                        Belum ada jadwal penugasan kegiatan aktif saat ini.
                    </div>
                </li>
            @endif

            <!-- Bullet 2: Laporan Perjalanan Dinas -->
            <li class="petugas-notif-item">
                @if($reportsRevision > 0)
                    <span class="petugas-notif-bullet bullet-red"></span>
                    <div class="text-danger fw-semibold">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        Perhatian: Ada <strong>{{ $reportsRevision }} laporan perjalanan dinas</strong> yang memerlukan revisi dari admin.
                    </div>
                @elseif($reportsDraft > 0 || $reportsSubmitted == 0)
                    <span class="petugas-notif-bullet bullet-amber"></span>
                    <div>
                        Laporan perjalanan dinas: <strong>{{ $reportsDraft > 0 ? $reportsDraft . ' laporan masih draft' : 'Belum membuat laporan kegiatan lapangan' }}</strong>.
                    </div>
                @else
                    <span class="petugas-notif-bullet bullet-green"></span>
                    <div>
                        Laporan perjalanan dinas lengkap ({{ $reportsApproved }} disetujui admin).
                    </div>
                @endif
            </li>

            <!-- Bullet 3: Status Responden / Ruta -->
            <li class="petugas-notif-item">
                @if($remainingRespondents > 0)
                    <span class="petugas-notif-bullet bullet-amber"></span>
                    <div>
                        Masih ada <strong class="text-danger">{{ number_format($remainingRespondents) }} sampel ruta</strong> yang belum selesai dari target {{ number_format($targetRespondents) }} ruta.
                    </div>
                @else
                    <span class="petugas-notif-bullet bullet-green"></span>
                    <div>
                        <strong class="text-success"><i class="fa-solid fa-circle-check me-1"></i> Target tercapai!</strong> Seluruh {{ number_format($totalRespondents) }} sampel responden selesai diverifikasi.
                    </div>
                @endif
            </li>
        </ul>
    </div>

    <!-- ==============================================================
         3. EMPAT TAHAPAN KERJA BPS (JOURNEY 4 STEPS WORKFLOW)
         Sesuai arahan resmi Ibu Siti Rogayah BPS:
         1. BAST blanko dokumen dan perlengkapan dari kantor ke petugas
         2. BAST blanko dokumen dan perlengkapan petugas ke responden
         3. BAST dokumen terisi dari petugas ke kantor
         4. Pengisian laporan perjadin
         ============================================================== -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="fw-bold text-dark mb-0">
                <i class="fa-solid fa-diagram-project text-primary me-2"></i>Tahapan Kerja Lapangan
            </h5>
            <small class="text-muted">4 Siklus Pelaksanaan Tugas Sensus &amp; Survei BPS</small>
        </div>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle d-none d-sm-inline-block">
            <i class="fa-solid fa-route me-1"></i> Alur Kerja Terintegrasi
        </span>
    </div>

    <div class="row g-3 mb-4">
        <!-- TAHAP 1: BAST Blanko Dokumen & Perlengkapan (Kantor ke Petugas) -->
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('petugas.basts.officers.index', ['type' => 'kantor_ke_petugas']) }}" 
               class="petugas-journey-card journey-card-step1">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="journey-step-badge text-primary" style="background: #e0f2fe;">
                        Tahap 01 &bull; Persiapan
                    </span>
                    <span class="text-primary small fw-bold">
                        <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </div>
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="sketch-btn-icon flex-shrink-0" style="width: 48px; height: 48px; background: #e0f2fe; color: #0284c7; border-radius: 14px; font-size: 1.3rem;">
                        <i class="fa-solid fa-boxes-packing"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 0.98rem; line-height: 1.25;">BAST Blanko &amp; Alat</div>
                        <small class="text-muted" style="font-size: 0.72rem;">Kantor &rarr; Petugas</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace" style="font-size: 0.68rem;">
                        {{ $totalBlankoBasts }} Dokumen Sah
                    </span>
                    <small class="text-muted" style="font-size: 0.7rem;">Distribusi Logistik</small>
                </div>
            </a>
        </div>

        <!-- TAHAP 2: BAST Responden (Petugas ke Responden + TTD Digital) -->
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('petugas.basts.respondents.index') }}" 
               class="petugas-journey-card journey-card-step2">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="journey-step-badge text-success" style="background: #dcfce7;">
                        Tahap 02 &bull; Pelaksanaan
                    </span>
                    <span class="text-success small fw-bold">
                        <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </div>
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="sketch-btn-icon flex-shrink-0" style="width: 48px; height: 48px; background: #dcfce7; color: #16a34a; border-radius: 14px; font-size: 1.3rem;">
                        <i class="fa-solid fa-file-signature"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 0.98rem; line-height: 1.25;">BAST Responden</div>
                        <small class="text-muted" style="font-size: 0.72rem;">Petugas &rarr; Responden (TTD)</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace" style="font-size: 0.68rem;">
                        {{ $totalRespondentBasts }} Dokumen Terbit
                    </span>
                    <small class="text-muted" style="font-size: 0.7rem;">Kunjungan Ruta</small>
                </div>
            </a>
        </div>

        <!-- TAHAP 3: BAST Dokumen Terisi (Petugas ke Kantor) -->
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('petugas.basts.officers.index', ['type' => 'petugas_ke_kantor']) }}" 
               class="petugas-journey-card journey-card-step3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="journey-step-badge text-warning-emphasis" style="background: #fef3c7;">
                        Tahap 03 &bull; Pengembalian
                    </span>
                    <span class="text-warning-emphasis small fw-bold">
                        <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </div>
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="sketch-btn-icon flex-shrink-0" style="width: 48px; height: 48px; background: #fef3c7; color: #d97706; border-radius: 14px; font-size: 1.3rem;">
                        <i class="fa-solid fa-file-circle-check"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 0.98rem; line-height: 1.25;">BAST Dokumen Terisi</div>
                        <small class="text-muted" style="font-size: 0.72rem;">Petugas &rarr; Kantor BPS</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle font-monospace" style="font-size: 0.68rem;">
                        {{ $totalTerisiBasts }} Dokumen Sah
                    </span>
                    <small class="text-muted" style="font-size: 0.7rem;">Kuesioner Lengkap</small>
                </div>
            </a>
        </div>

        <!-- TAHAP 4: Pengisian Laporan Perjalanan Dinas (Perjadin) -->
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('petugas.reports.index') }}" 
               class="petugas-journey-card journey-card-step4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="journey-step-badge text-purple" style="background: #ede9fe; color: #7c3aed;">
                        Tahap 04 &bull; Laporan SPJ
                    </span>
                    <span class="text-primary small fw-bold" style="color: #7c3aed !important;">
                        <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </div>
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="sketch-btn-icon flex-shrink-0" style="width: 48px; height: 48px; background: #ede9fe; color: #7c3aed; border-radius: 14px; font-size: 1.3rem;">
                        <i class="fa-solid fa-clipboard-user"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 0.98rem; line-height: 1.25;">Laporan Perjadin</div>
                        <small class="text-muted" style="font-size: 0.72rem;">SPD &amp; Dokumentasi Lapangan</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <span class="badge {{ $reportsRevision > 0 ? 'bg-danger text-white' : ($reportsApproved > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border') }} font-monospace" style="font-size: 0.68rem;">
                        {{ $reportsRevision > 0 ? $reportsRevision . ' Perlu Revisi' : ($reportsApproved > 0 ? $reportsApproved . ' Disetujui' : 'Draft / Buat') }}
                    </span>
                    <small class="text-muted" style="font-size: 0.7rem;">Auto PDF SPD</small>
                </div>
            </a>
        </div>
    </div>

    <!-- ==============================================================
         4. RINGKASAN CAPAIAN TARGET RESPONDEN (KOMPAK & MODERN)
         ============================================================== -->
    <div class="sp-card mb-4 border-0 shadow-sm" style="border-radius: 20px;">
        <div class="sp-card-body p-3 p-md-4">
            <div class="row align-items-center g-3">
                <div class="col-md-5">
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="fs-2 fw-black font-monospace text-dark">{{ $totalRespondents }}</span>
                        <span class="text-muted fw-semibold">/ {{ $targetRespondents }} Responden Target</span>
                        <span class="badge {{ $progressPercentage >= 100 ? 'bg-success' : ($progressPercentage >= 50 ? 'bg-primary' : 'bg-warning text-dark') }} ms-auto ms-md-2 px-2.5 py-1" style="font-size: 0.75rem;">
                            {{ $progressPercentage }}% Selesai
                        </span>
                    </div>
                    <div class="progress" style="height: 10px; border-radius: 999px; background: #e2e8f0;">
                        <div class="progress-bar {{ $progressPercentage >= 100 ? 'bg-success' : 'bg-primary' }}" 
                             style="width: {{ max(4, $progressPercentage) }}%; border-radius: 999px;"></div>
                    </div>
                    <div class="mt-2.5">
                        <a href="{{ route('petugas.respondents.index') }}" class="btn btn-sm btn-outline-primary py-1 px-3 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-users-viewfinder me-1"></i> Buka Daftar Sampel Ruta Saya &rarr;
                        </a>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div class="p-2.5 rounded-3 bg-light border">
                                <div class="fs-5 fw-bold text-primary font-monospace">{{ number_format($totalRespondents) }}</div>
                                <div class="text-muted fw-semibold" style="font-size: 0.68rem; text-transform: uppercase;">Sudah Didata</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2.5 rounded-3 bg-light border">
                                <div class="fs-5 fw-bold text-warning font-monospace">{{ number_format($remainingRespondents) }}</div>
                                <div class="text-muted fw-semibold" style="font-size: 0.68rem; text-transform: uppercase;">Sisa Target</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2.5 rounded-3 bg-light border">
                                <div class="fs-5 fw-bold text-success font-monospace">{{ number_format($totalBasts) }}</div>
                                <div class="text-muted fw-semibold" style="font-size: 0.68rem; text-transform: uppercase;">BAST Sah</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@extends('layouts.app')

@section('title', 'Beranda Petugas Lapangan - SIMPLE')
@section('page_title', 'Beranda Petugas')

@section('content')
<div class="petugas-container">
    <!-- ==============================================================
         1. KOTAK INFORMASI TERBARU (SESUAI SKETSA KERTAS DARI KLIEN)
         "Di sini ada satu pemberitahuan informasi terbaru, kayak notif-notif"
         ============================================================== -->
    <div class="petugas-notif-box">
        <div class="petugas-notif-header">
            <h5 class="petugas-notif-title">
                <i class="fa-solid fa-bullhorn text-primary"></i>
                <span>Informasi Terbaru</span>
            </h5>
            <div class="text-muted small">
                <i class="fa-regular fa-calendar-check me-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </div>
        </div>

        <ul class="petugas-notif-list">
            <!-- Bullet 1: Kunjungan / Penugasan & Deadline (Sketsa: Kunjungan 1 dead 15 Jan) -->
            @if($nearestTaskDeadline && $nearestTaskDeadline->assignment)
                <li class="petugas-notif-item">
                    <span class="petugas-notif-bullet bullet-blue"></span>
                    <div>
                        <strong>{{ $nearestTaskDeadline->assignment->title }}</strong> &bull;
                        Wilayah: <span>{{ $nearestTaskDeadline->assignment->location }}</span>
                        @if($nearestTaskDeadline->assignment->end_date)
                            <span class="badge {{ $isUrgentDeadline ? 'bg-danger text-white' : 'bg-warning text-dark' }} ms-1 px-2 py-0.5">
                                <i class="fa-solid fa-hourglass-half me-1"></i>
                                Deadline: {{ $nearestTaskDeadline->assignment->end_date->translatedFormat('d F Y') }}
                                ({{ $daysUntilDeadline > 0 ? 'Sisa ' . $daysUntilDeadline . ' Hari' : ($daysUntilDeadline === 0 ? 'Hari Ini Batas Akhir!' : 'Batas Waktu Lewat') }})
                            </span>
                        @endif
                    </div>
                </li>
            @else
                <li class="petugas-notif-item">
                    <span class="petugas-notif-bullet bullet-blue"></span>
                    <div>
                        Belum ada jadwal penugasan lapangan aktif saat ini.
                    </div>
                </li>
            @endif

            <!-- Bullet 2: Laporan yang belum terisi / revisi (Sketsa: Lp blm terisi 2 laporan) -->
            <li class="petugas-notif-item">
                @if($reportsRevision > 0)
                    <span class="petugas-notif-bullet bullet-red"></span>
                    <div class="text-danger fw-semibold">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        Perhatian: Ada <strong>{{ $reportsRevision }} laporan perjalanan dinas</strong> yang perlu diperbaiki / direvisi.
                    </div>
                @elseif($reportsDraft > 0 || $reportsSubmitted == 0)
                    <span class="petugas-notif-bullet bullet-amber"></span>
                    <div>
                        Laporan perjalanan dinas: <strong>{{ $reportsDraft > 0 ? $reportsDraft . ' laporan masih draft' : 'Belum membuat laporan kegiatan lapangan' }}</strong>. Harap submit laporan setelah kunjungan selesai.
                    </div>
                @else
                    <span class="petugas-notif-bullet bullet-green"></span>
                    <div>
                        Laporan perjalanan dinas lengkap ({{ $reportsApproved }} disetujui admin).
                    </div>
                @endif
            </li>

            <!-- Bullet 3: Sisa Responden yang belum didata -->
            <li class="petugas-notif-item">
                @if($remainingRespondents > 0)
                    <span class="petugas-notif-bullet bullet-amber"></span>
                    <div>
                        Masih ada <strong class="text-danger">{{ number_format($remainingRespondents) }} responden</strong> yang belum selesai diverifikasi dari target {{ number_format($targetRespondents) }} responden.
                    </div>
                @else
                    <span class="petugas-notif-bullet bullet-green"></span>
                    <div>
                        <strong class="text-success"><i class="fa-solid fa-circle-check me-1"></i> Target tercapai!</strong> Seluruh {{ number_format($totalRespondents) }} responden telah selesai diverifikasi.
                    </div>
                @endif
            </li>
        </ul>
    </div>

    <!-- ==============================================================
         2. EMPAT TOMBOL MENU UTAMA (SESUAI 4 KOTAK DI SKETSA KERTAS)
         "Di sini ada empat menu tadi. Empat tombol empat menu ini."
         ============================================================== -->
    <div class="row g-3 mb-4">
        <!-- Tombol 1: BAST Responden (Sketsa: BAST respon) -->
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('petugas.basts.respondents.index') }}" class="petugas-sketch-btn btn-bast-resp">
                <div class="sketch-btn-content">
                    <div class="sketch-btn-icon" style="background: #dcfce7; color: #16a34a;">
                        <i class="fa-solid fa-file-signature"></i>
                    </div>
                    <div class="sketch-btn-text">
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="sketch-btn-title">BAST Responden</span>
                        </div>
                        <div class="sketch-btn-sub">Penerbitan BAST &amp; TTD Digital</div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle mt-1 font-monospace" style="font-size: 0.68rem;">
                            {{ $totalRespondentBasts }} Dokumen Terbit
                        </span>
                    </div>
                </div>
                <div class="sketch-btn-arrow">
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </a>
        </div>

        <!-- Tombol 2: Data Responden -->
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('petugas.respondents.index') }}" class="petugas-sketch-btn btn-respondent">
                <div class="sketch-btn-content">
                    <div class="sketch-btn-icon" style="background: #e0f2fe; color: #0284c7;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="sketch-btn-text">
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="sketch-btn-title">Data Responden</span>
                        </div>
                        <div class="sketch-btn-sub">Daftar sampel &amp; entri responden</div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle mt-1 font-monospace" style="font-size: 0.68rem;">
                            {{ $totalRespondents }} / {{ $targetRespondents }} Selesai
                        </span>
                    </div>
                </div>
                <div class="sketch-btn-arrow">
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </a>
        </div>

        <!-- Tombol 3: BAST Petugas -->
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('petugas.basts.officers.index') }}" class="petugas-sketch-btn btn-bast-off">
                <div class="sketch-btn-content">
                    <div class="sketch-btn-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fa-solid fa-file-shield"></i>
                    </div>
                    <div class="sketch-btn-text">
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="sketch-btn-title">BAST Petugas</span>
                        </div>
                        <div class="sketch-btn-sub">Serah terima berkas lapangan</div>
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle mt-1 font-monospace" style="font-size: 0.68rem;">
                            {{ $totalOfficerBasts }} Dokumen Sah
                        </span>
                    </div>
                </div>
                <div class="sketch-btn-arrow">
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </a>
        </div>

        <!-- Tombol 4: Laporan Kegiatan / Perjalanan Dinas -->
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('petugas.reports.index') }}" class="petugas-sketch-btn btn-report">
                <div class="sketch-btn-content">
                    <div class="sketch-btn-icon" style="background: #ede9fe; color: #7c3aed;">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <div class="sketch-btn-text">
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="sketch-btn-title">Laporan Kegiatan</span>
                        </div>
                        <div class="sketch-btn-sub">Laporan perjalanan &amp; auto PDF</div>
                        <span class="badge {{ $reportsRevision > 0 ? 'bg-danger text-white' : ($reportsApproved > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border') }} mt-1" style="font-size: 0.68rem;">
                            {{ $reportsRevision > 0 ? $reportsRevision . ' Perlu Revisi' : ($reportsApproved > 0 ? $reportsApproved . ' Disetujui' : 'Draft / Buat') }}
                        </span>
                    </div>
                </div>
                <div class="sketch-btn-arrow">
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </a>
        </div>
    </div>

    <!-- ==============================================================
         3. RINGKASAN CAPAIAN TARGET RESPONDEN (KOMPAK & PENUH)
         ============================================================== -->
    <div class="sp-card mb-0">
        <div class="sp-card-body p-3 p-md-4">
            <div class="row align-items-center g-3">
                <div class="col-md-5">
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="fs-3 fw-black font-monospace text-dark">{{ $totalRespondents }}</span>
                        <span class="text-muted fw-semibold">/ {{ $targetRespondents }} Responden Target</span>
                        <span class="badge {{ $progressPercentage >= 100 ? 'bg-success' : ($progressPercentage >= 50 ? 'bg-primary' : 'bg-warning text-dark') }} ms-auto ms-md-2" style="font-size: 0.72rem;">
                            {{ $progressPercentage }}% Selesai
                        </span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 999px;">
                        <div class="progress-bar {{ $progressPercentage >= 100 ? 'bg-success' : 'bg-primary' }}" style="width: {{ max(4, $progressPercentage) }}%;"></div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div class="p-2 rounded bg-light border">
                                <div class="fs-6 fw-bold text-primary font-monospace">{{ number_format($totalRespondents) }}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">Sudah Didata</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-light border">
                                <div class="fs-6 fw-bold text-warning font-monospace">{{ number_format($remainingRespondents) }}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">Sisa Target</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-light border">
                                <div class="fs-6 fw-bold text-success font-monospace">{{ number_format($totalBasts) }}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">BAST Sah</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

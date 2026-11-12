@extends('layouts.app')

@section('title', 'Beranda Petugas Lapangan - SIMPLE')
@section('page_title', 'Beranda Petugas')

@section('content')
<div class="petugas-container">
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

        {{-- Pesan Khusus / Notifikasi dari Administrator ke Papan Informasi Petugas --}}
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

        <ul class="petugas-notif-list">
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
    <div class="row g-3 mb-4">
        <!-- 1. BAST blanko dokumen dan perlengkapan dari kantor ke petugas -->
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('petugas.basts.officers.index', ['type' => 'kantor_ke_petugas']) }}" class="petugas-sketch-btn btn-respondent">
                <div class="sketch-btn-content">
                    <div class="sketch-btn-icon" style="background: #e0f2fe; color: #0284c7;">
                        <i class="fa-solid fa-boxes-packing"></i>
                    </div>
                    <div class="sketch-btn-text">
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="sketch-btn-title">BAST Blanko &amp; Perlengkapan</span>
                        </div>
                        <div class="sketch-btn-sub">Kantor &rarr; Petugas</div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle mt-1 font-monospace" style="font-size: 0.68rem;">
                            {{ $totalBlankoBasts }} Dokumen Sah
                        </span>
                    </div>
                </div>
                <div class="sketch-btn-arrow">
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </a>
        </div>

        <!-- 2. BAST blanko dokumen dan perlengkapan petugas ke responden -->
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('petugas.basts.respondents.index') }}" class="petugas-sketch-btn btn-bast-resp">
                <div class="sketch-btn-content">
                    <div class="sketch-btn-icon" style="background: #dcfce7; color: #16a34a;">
                        <i class="fa-solid fa-file-signature"></i>
                    </div>
                    <div class="sketch-btn-text">
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="sketch-btn-title">BAST Blanko &amp; Perlengkapan</span>
                        </div>
                        <div class="sketch-btn-sub">Petugas &rarr; Responden</div>
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

        <!-- 3. BAST dokumen terisi dari petugas ke kantor -->
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('petugas.basts.officers.index', ['type' => 'petugas_ke_kantor']) }}" class="petugas-sketch-btn btn-bast-off">
                <div class="sketch-btn-content">
                    <div class="sketch-btn-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fa-solid fa-file-circle-check"></i>
                    </div>
                    <div class="sketch-btn-text">
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="sketch-btn-title">BAST Dokumen Terisi</span>
                        </div>
                        <div class="sketch-btn-sub">Petugas &rarr; Kantor</div>
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle mt-1 font-monospace" style="font-size: 0.68rem;">
                            {{ $totalTerisiBasts }} Dokumen Sah
                        </span>
                    </div>
                </div>
                <div class="sketch-btn-arrow">
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </a>
        </div>

        <!-- 4. Pengisian laporan perjadin -->
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('petugas.reports.index') }}" class="petugas-sketch-btn btn-report">
                <div class="sketch-btn-content">
                    <div class="sketch-btn-icon" style="background: #ede9fe; color: #7c3aed;">
                        <i class="fa-solid fa-clipboard-user"></i>
                    </div>
                    <div class="sketch-btn-text">
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="sketch-btn-title">Pengisian Laporan Perjadin</span>
                        </div>
                        <div class="sketch-btn-sub">Laporan Perjadin &amp; Pengeluaran Riil</div>
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
                    <div class="mt-2">
                        <a href="{{ route('petugas.respondents.index') }}" class="btn btn-xs btn-outline-primary py-1 px-2.5 rounded-pill" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-users-viewfinder me-1"></i> Buka Daftar Sampel Ruta Saya &rarr;
                        </a>
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

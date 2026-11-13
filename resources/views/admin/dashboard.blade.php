@extends('layouts.app')

@section('title', 'Dashboard Monitoring & Evaluasi')
@section('page_title', 'Dashboard Monitoring & Evaluasi')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Dashboard Monitoring</li>
@endsection

@section('content')
<!-- Header Welcome & Quick Info (Compact) -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
        <h4 class="fw-bold text-dark mb-0">Monitoring &amp; Evaluasi Lapangan 👋</h4>
        <p class="text-muted small mb-0" style="font-size: 0.8rem;">
            Ringkasan progres BAST, kepatuhan laporan petugas, dan status wilayah per {{ now()->translatedFormat('d F Y') }}.
        </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.basts.respondents.index') }}" class="btn btn-sm btn-outline-success">
            <i class="fa-solid fa-file-signature me-1"></i> Data BAST
        </a>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-primary position-relative">
            <i class="fa-solid fa-clipboard-check me-1"></i> Verifikasi Laporan
            @if(($reportsPending ?? 0) > 0)
                <span class="badge bg-warning text-dark ms-1">{{ $reportsPending }}</span>
            @endif
        </a>
    </div>
</div>

<!-- Pemberitahuan Laporan Perjadin Baru / Menunggu ACC Admin -->
@if(isset($incomingApprovalReports) && $incomingApprovalReports->isNotEmpty())
    <div class="sp-card mb-3 border-warning border-opacity-50 shadow-sm">
        <div class="sp-card-header py-2 px-3 bg-warning bg-opacity-10 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark">
                    <i class="fa-solid fa-bell me-1"></i> {{ $incomingApprovalReports->count() }} Masuk
                </span>
                <span class="fw-bold text-dark small">
                    Informasi Laporan Perjadin Petugas (Menunggu Pemeriksaan / ACC Admin)
                </span>
            </div>
            <a href="{{ route('admin.reports.index', ['status' => 'submitted']) }}" class="btn btn-xs btn-outline-primary py-0.5 px-2" style="font-size: 0.72rem;">
                Kelola Semua Laporan &rarr;
            </a>
        </div>
        <div class="sp-card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0" style="font-size: 0.78rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Petugas</th>
                            <th>Judul &amp; No. Laporan</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Aksi Cepat Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($incomingApprovalReports->take(5) as $incRep)
                            <tr>
                                <td class="ps-3 fw-semibold text-dark">{{ $incRep->user ? $incRep->user->name : ($incRep->officer_name ?? '-') }}</td>
                                <td>
                                    <span class="fw-bold text-primary">{{ $incRep->activity_title }}</span>
                                    <span class="text-muted font-monospace ms-1">({{ $incRep->report_number ?? 'LAP-'.$incRep->id }})</span>
                                </td>
                                <td class="text-muted text-nowrap">
                                    {{ $incRep->activity_date ? $incRep->activity_date->format('d/m/Y') : '-' }}
                                    <small class="d-block" style="font-size: 0.68rem;">{{ $incRep->created_at?->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $incRep->status_badge }}">{{ $incRep->status_label }}</span>
                                </td>
                                <td class="text-end pe-3 text-nowrap">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.reports.index', ['inspect' => $incRep->id]) }}"
                                           class="btn btn-xs btn-outline-info py-0.5 px-2" style="font-size: 0.72rem;" title="Lihat Laporan">
                                            <i class="fa-solid fa-eye me-1"></i> Lihat
                                        </a>
                                        @if(auth()->user()->isAdmin())
                                            <a href="{{ route('admin.reports.index', ['edit' => $incRep->id]) }}"
                                               class="btn btn-xs btn-outline-warning text-dark py-0.5 px-2" style="font-size: 0.72rem;" title="Edit Inputan Laporan">
                                                <i class="fa-regular fa-pen-to-square me-1"></i> Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.reports.approve', $incRep) }}" class="d-inline" onsubmit="return confirm('ACC (Setujui) laporan {{ addslashes($incRep->activity_title) }} agar petugas dapat mengunduh PDF?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-xs btn-success py-0.5 px-2" style="font-size: 0.72rem;" title="ACC / Setujui Laporan">
                                                    <i class="fa-solid fa-check-double me-1"></i> ACC
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
    </div>
@endif

<!-- ==============================================================
     FILTER MONITORING TOOLBAR (KOMPAK & FIT 100% ZOOM)
     ============================================================== -->
<div class="sp-card mb-3 border-0 shadow-sm" style="background: #f8fafc;">
    <div class="sp-card-body p-2 p-md-3">
        <form method="GET" action="{{ route('admin.dashboard') }}" id="filterMonitoringForm" class="row g-2 align-items-center">
            <!-- Filter Kegiatan -->
            <div class="col-xl-3 col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white text-muted border-end-0">
                        <i class="fa-solid fa-briefcase text-primary"></i>
                    </span>
                    <select name="assignment_id" class="form-select form-select-sm border-start-0 ps-1" onchange="this.form.submit()">
                        <option value="">Semua Kegiatan</option>
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
                        <i class="fa-solid fa-map-location-dot text-danger"></i>
                    </span>
                    <select name="district" class="form-select form-select-sm border-start-0 ps-1" onchange="this.form.submit()">
                        <option value="">Semua Kecamatan</option>
                        @foreach($districtsList as $dist)
                            <option value="{{ $dist }}" {{ $selectedDistrict == $dist ? 'selected' : '' }}>
                                {{ $dist }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Filter Petugas -->
            <div class="col-xl-3 col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white text-muted border-end-0">
                        <i class="fa-solid fa-user-tie text-info"></i>
                    </span>
                    <select name="officer_id" class="form-select form-select-sm border-start-0 ps-1" onchange="this.form.submit()">
                        <option value="">Semua Petugas ({{ $allOfficersDropdown->count() }})</option>
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
                <div class="d-flex gap-1">
                    <select name="progress_status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ $progressFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="slow" {{ $progressFilter === 'slow' ? 'selected' : '' }}>🔴 Perlu Follow-Up (&lt;50%)</option>
                        <option value="on_track" {{ $progressFilter === 'on_track' ? 'selected' : '' }}>🟡 On Track (50-99%)</option>
                        <option value="completed" {{ $progressFilter === 'completed' ? 'selected' : '' }}>🟢 Selesai (100%)</option>
                    </select>

                    @if($selectedAssignmentId || $selectedDistrict || $selectedOfficerId || $progressFilter !== 'all' || $reportFilter !== 'all')
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center" title="Reset Semua Filter">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        @if($selectedAssignmentId || $selectedDistrict || $selectedOfficerId || $progressFilter !== 'all' || $reportFilter !== 'all')
            <div class="mt-2 pt-2 border-top d-flex flex-wrap align-items-center gap-1" style="font-size: 0.75rem;">
                <span class="text-muted fw-semibold">Filter:</span>
                @if($selectedDistrict)
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle py-1">
                        <i class="fa-solid fa-location-dot me-1"></i> {{ $selectedDistrict }}
                    </span>
                @endif
                @if($selectedOfficerId)
                    @php $currOff = $allOfficersDropdown->firstWhere('id', $selectedOfficerId); @endphp
                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle py-1">
                        <i class="fa-solid fa-user me-1"></i> {{ $currOff ? $currOff->name : 'Petugas #' . $selectedOfficerId }}
                    </span>
                @endif
                @if($progressFilter === 'slow')
                    <span class="badge bg-danger text-white py-1">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Lambat / Perlu Follow-Up
                    </span>
                @elseif($progressFilter === 'completed')
                    <span class="badge bg-success text-white py-1">
                        <i class="fa-solid fa-circle-check me-1"></i> Selesai 100%
                    </span>
                @endif
                <a href="{{ route('admin.dashboard') }}" class="text-danger ms-auto text-decoration-none">
                    <i class="fa-solid fa-xmark me-1"></i> Reset
                </a>
            </div>
        @endif
    </div>
</div>

<!-- ==============================================================
     EMPAT KARTU METRIK BAST (SEJAJAR, KOMPAK, 100% ZOOM FRIENDLY)
     ============================================================== -->
<div class="row g-2.5 mb-3">
    <!-- 1. Realisasi BAST (Persentase) -->
    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card py-2 px-3">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div>
                    <div class="sp-stat-label" style="font-size: 0.68rem;">Realisasi BAST</div>
                    <div class="d-flex align-items-baseline gap-1 my-0.5">
                        <span class="fs-5 fw-black font-monospace text-dark">{{ $bastPercentage }}%</span>
                        <span class="badge {{ $bastPercentage >= 80 ? 'bg-success' : ($bastPercentage >= 50 ? 'bg-warning text-dark' : 'bg-danger') }}" style="font-size: 0.65rem;">
                            {{ $bastPercentage >= 100 ? 'Selesai' : ($bastPercentage >= 50 ? 'Berjalan' : 'Lambat') }}
                        </span>
                    </div>
                    <div class="progress" style="height: 4px; width: 110px;">
                        <div class="progress-bar bg-success" style="width: {{ $bastPercentage }}%;"></div>
                    </div>
                </div>
                <div class="sp-stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Target Responden -->
    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card py-2 px-3">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div>
                    <div class="sp-stat-label" style="font-size: 0.68rem;">Target Responden</div>
                    <div class="fs-5 fw-black font-monospace text-dark my-0.5">{{ number_format($bastTargetCount) }}</div>
                    <div class="sp-stat-desc" style="font-size: 0.66rem;"><i class="fa-solid fa-crosshairs text-primary me-1"></i> Total sasaran BAST</div>
                </div>
                <div class="sp-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. BAST Masuk (Respon) -->
    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card py-2 px-3">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div>
                    <div class="sp-stat-label text-success" style="font-size: 0.68rem;">BAST Masuk (Respons)</div>
                    <div class="fs-5 fw-black font-monospace text-success my-0.5">{{ number_format($bastCompletedCount) }}</div>
                    <div class="sp-stat-desc" style="font-size: 0.66rem;"><i class="fa-solid fa-circle-check text-success me-1"></i> Respon telah terbit</div>
                </div>
                <div class="sp-stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                    <i class="fa-solid fa-file-circle-check"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Belum Selesai -->
    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card py-2 px-3">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div>
                    <div class="sp-stat-label text-danger" style="font-size: 0.68rem;">Belum Selesai</div>
                    <div class="fs-5 fw-black font-monospace text-danger my-0.5">{{ number_format($bastPendingCount) }}</div>
                    <div class="sp-stat-desc" style="font-size: 0.66rem;"><i class="fa-solid fa-clock text-warning me-1"></i> Sisa kunjungan BAST</div>
                </div>
                <div class="sp-stat-icon-wrap" style="background: #fee2e2; color: #dc2626;">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================================
     DUA KOLOM: MONITORING LAPORAN & REKAP KECAMATAN
     ============================================================== -->
<div class="row g-3 mb-3">
    <!-- 1. Monitoring Pengumpulan Laporan Petugas -->
    <div class="col-lg-6">
        <div class="sp-card h-100 mb-0 d-flex flex-column">
            <div class="sp-card-header py-2.5 px-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark small">
                    <i class="fa-solid fa-clipboard-user text-primary me-1"></i>
                    Monitoring Kepatuhan Laporan Petugas
                </span>
                <span class="badge bg-light text-dark border" style="font-size: 0.7rem;">
                    {{ $totalOfficersCount }} Petugas
                </span>
            </div>
            <div class="sp-card-body p-3 flex-grow-1">
                <!-- Mini Metric Chips -->
                <div class="row g-2 text-center mb-2">
                    <div class="col-4">
                        <div class="p-1.5 rounded bg-light border">
                            <div class="text-muted" style="font-size: 0.68rem;">TOTAL PETUGAS</div>
                            <div class="fs-6 fw-bold text-dark font-monospace">{{ $totalOfficersCount }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-1.5 rounded bg-success-subtle border border-success-subtle">
                            <div class="text-success fw-bold" style="font-size: 0.68rem;">SUDAH LAPOR</div>
                            <div class="fs-6 fw-bold text-success font-monospace">{{ $officersWithReportCount }} <small class="text-muted fw-normal" style="font-size: 0.65rem;">({{ $reportSubmissionRate }}%)</small></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-1.5 rounded bg-danger-subtle border border-danger-subtle">
                            <div class="text-danger fw-bold" style="font-size: 0.68rem;">BELUM LAPOR</div>
                            <div class="fs-6 fw-bold text-danger font-monospace">{{ $officersWithoutReportCount }}</div>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar Laporan -->
                <div class="mb-2">
                    <div class="d-flex justify-content-between small text-muted mb-1" style="font-size: 0.7rem;">
                        <span>Kepatuhan Laporan Perjalanan Dinas</span>
                        <span class="fw-bold text-dark">{{ $reportSubmissionRate }}%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-primary" style="width: {{ $reportSubmissionRate }}%;"></div>
                    </div>
                </div>

                <!-- Petugas yang Belum Lapor (Tindakan Cepat) -->
                <div class="border rounded p-2 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-1 px-1">
                        <span class="fw-bold text-danger" style="font-size: 0.72rem;">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i> Perlu Follow-Up Laporan ({{ $officersWithoutReports->count() }} Petugas):
                        </span>
                        <div class="d-flex align-items-center gap-1.5">
                            @if(auth()->user()->isAdmin() && $officersWithoutReports->isNotEmpty())
                                <button type="button" 
                                        class="btn btn-xs btn-outline-danger py-0.5 px-1.5" 
                                        style="font-size: 0.68rem;"
                                        title="Kirim pesan serentak ke papan informasi seluruh petugas yang belum lapor"
                                        onclick="openSendNoticeModal('all_without_reports', 'Semua Petugas Belum Lapor ({{ $officersWithoutReports->count() }} Orang)', 'report')">
                                    <i class="fa-solid fa-paper-plane me-1"></i> Ingatkan Semua
                                </button>
                            @endif
                            <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['report_status' => 'not_submitted'])) }}" class="text-primary text-decoration-none" style="font-size: 0.7rem;">
                                Lihat Semua
                            </a>
                        </div>
                    </div>

                    <div style="max-height: 140px; overflow-y: auto;">
                        @forelse($officersWithoutReports->take(5) as $offWithout)
                            <div class="d-flex align-items-center justify-content-between py-1 px-2 border-bottom bg-white rounded mb-1">
                                <div class="overflow-hidden me-2">
                                    <span class="fw-bold text-dark d-block text-truncate" style="font-size: 0.75rem;">{{ $offWithout->name }}</span>
                                    <small class="text-muted" style="font-size: 0.68rem;">
                                        <i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $offWithout->dominant_district }} &bull; BAST: {{ $offWithout->completed }}/{{ $offWithout->target }}
                                    </small>
                                </div>
                                @if(auth()->user()->isAdmin())
                                    <div class="flex-shrink-0">
                                        <button type="button" 
                                                class="btn btn-xs btn-primary py-0.5 px-2" 
                                                style="font-size: 0.68rem;" 
                                                title="Kirim pesan ke papan informasi petugas" 
                                                onclick="openSendNoticeModal({{ $offWithout->id }}, '{{ addslashes($offWithout->name) }}', 'report')">
                                            <i class="fa-solid fa-bullhorn me-1"></i> Ingatkan
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-2 text-success small" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-circle-check me-1"></i> Semua petugas lapangan telah menyerahkan laporan!
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Ringkasan Progres per Kecamatan -->
    <div class="col-lg-6">
        <div class="sp-card h-100 mb-0 d-flex flex-column">
            <div class="sp-card-header py-2.5 px-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark small">
                    <i class="fa-solid fa-map-marked-alt text-danger me-1"></i>
                    Rekapitulasi Capaian per Kecamatan
                </span>
                <span class="badge bg-light text-secondary border" style="font-size: 0.7rem;">
                    {{ count($districtSummaries) }} Kecamatan
                </span>
            </div>
            <div class="sp-card-body p-3 flex-grow-1">
                <div class="d-flex flex-column gap-2" style="max-height: 245px; overflow-y: auto;">
                    @forelse($districtSummaries as $dSum)
                        <div class="p-2 border rounded bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['district' => $dSum['name']])) }}" 
                                   class="fw-bold text-dark text-decoration-none"
                                   style="font-size: 0.78rem;"
                                   title="Filter kecamatan {{ $dSum['name'] }}">
                                    <i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $dSum['name'] }}
                                </a>
                                <span class="badge {{ $dSum['percentage'] >= 75 ? 'bg-success-subtle text-success' : ($dSum['percentage'] >= 50 ? 'bg-warning-subtle text-warning-emphasis' : 'bg-danger-subtle text-danger') }} font-monospace" style="font-size: 0.68rem;">
                                    {{ $dSum['percentage'] }}%
                                </span>
                            </div>

                            <div class="progress mb-1" style="height: 5px;">
                                <div class="progress-bar {{ $dSum['percentage'] >= 75 ? 'bg-success' : ($dSum['percentage'] >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                     style="width: {{ $dSum['percentage'] }}%;"></div>
                            </div>

                            <div class="d-flex justify-content-between text-muted" style="font-size: 0.7rem;">
                                <span>Target: <strong>{{ $dSum['total'] }}</strong></span>
                                <span>BAST: <strong class="text-success">{{ $dSum['completed'] }}</strong></span>
                                <span>Sisa: <strong class="text-danger">{{ $dSum['pending'] }}</strong></span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            <i class="fa-solid fa-map-location-dot fa-2x mb-1 text-secondary opacity-50 d-block"></i>
                            Tidak ada data kecamatan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================================
     TABEL KINERJA PETUGAS & IDENTIFIKASI PETUGAS LAMBAT
     ============================================================== -->
<div class="sp-card mb-3 border-0 shadow-sm">
    <div class="sp-card-header d-flex flex-wrap align-items-center justify-content-between gap-2 py-2.5 px-3">
        <div>
            <span class="fw-bold text-dark small d-block">
                <i class="fa-solid fa-user-gear text-primary me-1"></i>
                Identifikasi Kinerja Petugas &amp; Follow-Up Langsung
            </span>
            <span class="text-muted" style="font-size: 0.72rem;">Urutan prioritas: petugas dengan progres terendah berada di paling atas.</span>
        </div>

        <!-- Quick Filter Buttons -->
        <div class="d-flex flex-wrap gap-1">
            <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['progress_status' => 'all'])) }}" 
               class="btn btn-xs {{ $progressFilter === 'all' ? 'btn-dark' : 'btn-outline-secondary' }}" style="font-size: 0.72rem; padding: 2px 8px;">
                Semua ({{ count($filteredOfficersList) }})
            </a>
            <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['progress_status' => 'slow'])) }}" 
               class="btn btn-xs {{ $progressFilter === 'slow' ? 'btn-danger' : 'btn-outline-danger' }}" style="font-size: 0.72rem; padding: 2px 8px;">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> Lambat ({{ $slowOfficersCount }})
            </a>
            <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['progress_status' => 'on_track'])) }}" 
               class="btn btn-xs {{ $progressFilter === 'on_track' ? 'btn-warning' : 'btn-outline-warning' }}" style="font-size: 0.72rem; padding: 2px 8px;">
                On Track ({{ $onTrackOfficersCount }})
            </a>
            <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['progress_status' => 'completed'])) }}" 
               class="btn btn-xs {{ $progressFilter === 'completed' ? 'btn-success' : 'btn-outline-success' }}" style="font-size: 0.72rem; padding: 2px 8px;">
                Selesai ({{ $completedOfficersCount }})
            </a>
        </div>
    </div>

    <div class="sp-card-body p-0">
        <div class="table-responsive">
            <table class="table sp-table align-middle mb-0" style="font-size: 0.8125rem;">
                <thead class="table-light">
                    <tr>
                        <th style="min-width: 170px;">Petugas Lapangan</th>
                        <th style="min-width: 120px;">Wilayah Kerja</th>
                        <th class="text-center" style="width: 70px;">Target</th>
                        <th class="text-center" style="width: 70px;">BAST</th>
                        <th class="text-center" style="width: 70px;">Sisa</th>
                        <th style="min-width: 130px;">Progres</th>
                        <th style="min-width: 120px;">Status Kinerja</th>
                        <th class="text-center" style="width: 90px;">Laporan</th>
                        <th class="text-end" style="min-width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filteredOfficersList as $off)
                        <tr class="{{ $off->category === 'slow' ? 'bg-danger-subtle bg-opacity-25' : '' }}">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold {{ $off->category === 'slow' ? 'bg-danger' : ($off->category === 'completed' ? 'bg-success' : 'bg-primary') }}" 
                                         style="width: 30px; height: 30px; font-size: 0.75rem; flex-shrink: 0;">
                                        {{ strtoupper(substr($off->name, 0, 1)) }}
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.78rem;">{{ $off->name }}</div>
                                        <small class="text-muted d-block" style="font-size: 0.68rem;">{{ $off->phone ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border" style="font-size: 0.68rem;">
                                    {{ $off->dominant_district }}
                                </span>
                            </td>
                            <td class="text-center font-monospace fw-semibold">{{ $off->target }}</td>
                            <td class="text-center font-monospace fw-bold text-success">{{ $off->completed }}</td>
                            <td class="text-center font-monospace fw-bold {{ $off->pending > 0 ? 'text-danger' : 'text-muted' }}">
                                {{ $off->pending }}
                            </td>
                            <td>
                                <div class="d-flex justify-content-between text-muted mb-1 font-monospace" style="font-size: 0.68rem;">
                                    <span>{{ $off->completed }}/{{ $off->target }}</span>
                                    <span class="fw-bold text-dark">{{ $off->percentage }}%</span>
                                </div>
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar {{ $off->percentage >= 100 ? 'bg-success' : ($off->percentage >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                         style="width: {{ $off->percentage }}%;"></div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $off->badge_class }} py-1 px-1.5" style="font-size: 0.68rem;">
                                    @if($off->category === 'slow')
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                    @elseif($off->category === 'completed')
                                        <i class="fa-solid fa-check me-1"></i>
                                    @endif
                                    {{ $off->status_text }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($off->has_report)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.68rem;" title="Laporan sudah diserahkan">
                                        <i class="fa-solid fa-check me-1"></i> Ada ({{ $off->reports_count }})
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.68rem;" title="Belum membuat laporan kegiatan">
                                        <i class="fa-solid fa-xmark me-1"></i> Belum
                                    </span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    @if(auth()->user()->isAdmin())
                                        <button type="button" 
                                                class="btn btn-xs btn-primary py-1 px-2" 
                                                style="font-size: 0.72rem;"
                                                title="Kirim Pesan ke Papan Informasi Petugas"
                                                onclick="openSendNoticeModal({{ $off->id }}, '{{ addslashes($off->name) }}', '{{ $off->has_report ? 'bast' : 'report' }}', '{{ $off->target }}', '{{ $off->completed }}', '{{ $off->pending }}', '{{ $off->percentage }}')">
                                            <i class="fa-solid fa-bullhorn me-1"></i> Notif
                                        </button>
                                    @endif

                                    <button type="button" 
                                            class="btn btn-xs btn-outline-secondary py-1 px-1.5" 
                                            style="font-size: 0.72rem;"
                                            title="Lihat Draf Pesan Notifikasi"
                                            onclick="openReminderModal('{{ $off->id }}', '{{ addslashes($off->name) }}', '{{ $off->phone ?? '-' }}', '{{ $off->target }}', '{{ $off->completed }}', '{{ $off->pending }}', '{{ $off->percentage }}', '{{ $off->has_report ? 'Sudah Diserahkan' : 'BELUM Diserahkan' }}', '{{ $off->wa_url ?? '' }}')">
                                        <i class="fa-regular fa-comment-dots"></i>
                                    </button>

                                    @if($off->wa_url)
                                        <a href="{{ $off->wa_url }}" 
                                           target="_blank" 
                                           class="btn btn-xs btn-outline-success py-1 px-1.5" 
                                           style="font-size: 0.72rem;"
                                           title="Kirim WhatsApp (Opsional)">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted small">
                                <i class="fa-solid fa-user-slash fa-2x mb-2 text-secondary opacity-50 d-block"></i>
                                Tidak ada data petugas lapangan yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==============================================================
     GRAFIK TREN BULANAN & LOG AKTIVITAS (KOMPAK)
     ============================================================== -->
<div class="row g-3 mb-2 align-items-stretch">
    <!-- Chart: Tren Penginputan Responden & BAST -->
    <div class="col-lg-8">
        <div class="sp-card h-100 mb-0 d-flex flex-column">
            <div class="sp-card-header py-2.5 px-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark small">
                    <i class="fa-solid fa-chart-line text-primary me-1"></i>
                    Tren Penginputan Responden &amp; BAST Terbit
                </span>
                <span class="badge bg-light text-secondary border" style="font-size: 0.68rem;">6 Bulan Terakhir</span>
            </div>
            <div class="sp-card-body flex-grow-1 p-2.5">
                <div style="position: relative; height: 210px; width: 100%;">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Feed: Log Aktivitas Terkini -->
    <div class="col-lg-4">
        <div class="sp-card h-100 mb-0 d-flex flex-column">
            <div class="sp-card-header py-2.5 px-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark small">
                    <i class="fa-solid fa-bolt text-warning me-1"></i>
                    Log Aktivitas Terkini
                </span>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.logs.index') }}" class="btn btn-sm btn-link text-decoration-none p-0" style="font-size: 0.72rem;">Semua</a>
                @endif
            </div>
            <div class="sp-card-body p-0 flex-grow-1">
                <div class="list-group list-group-flush" style="max-height: 235px; overflow-y: auto;">
                    @forelse($recentActivities as $log)
                        <div class="list-group-item py-1.5 px-2.5 border-bottom">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-0.5">
                                <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.65rem;">
                                    {{ strtoupper($log->module) }}
                                </span>
                                <small class="text-muted" style="font-size: 0.65rem;">{{ $log->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-0 small text-dark lh-sm text-truncate" style="font-size: 0.72rem;" title="{{ $log->description }}">{{ $log->description }}</p>
                            <small class="text-muted" style="font-size: 0.67rem;">Oleh: <strong class="text-secondary">{{ $log->user ? $log->user->name : 'Sistem' }}</strong></small>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            <i class="fa-regular fa-clock fa-2x mb-1 text-secondary opacity-50 d-block"></i>
                            Belum ada aktivitas tercatat.
                        </div>
                    @endforelse
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
                    <i class="fa-solid fa-paper-plane text-primary me-1"></i> Draf Pesan Notifikasi Petugas
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
                    <i class="fa-solid fa-circle-info text-info"></i>
                    <div>Salin pesan atau langsung kirimkan via WhatsApp resmi.</div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2 px-3 d-flex justify-content-between flex-wrap gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyReminderText()">
                    <i class="fa-regular fa-copy me-1"></i> <span id="btnCopyText">Salin Teks</span>
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Tutup</button>
                    @if(auth()->user()->isAdmin())
                        <button type="button" class="btn btn-sm btn-sp-primary" onclick="transferReminderToNoticeBoard()">
                            <i class="fa-solid fa-bullhorn me-1"></i> Kirim ke Papan Informasi Petugas
                        </button>
                    @endif
                    <a href="#" id="modal_wa_direct_link" target="_blank" class="btn btn-sm btn-outline-success">
                        <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp
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
                    <i class="fa-solid fa-bullhorn text-primary me-1.5"></i> Kirim Pesan ke Papan Informasi Petugas
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
                            <i class="fa-solid fa-circle-info text-primary me-1"></i> Pesan ini akan langsung tampil di kotak <strong>"Informasi Terbaru"</strong> pada beranda HP/layar awal petugas yang bersangkutan.
                        </div>
                    </div>

                    <!-- Template Tombol Pilihan Cepat -->
                    <div class="d-flex gap-1.5 align-items-center flex-wrap pt-1">
                        <span class="text-muted" style="font-size: 0.68rem;">Pilihan Cepat:</span>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0.5 px-1.5" style="font-size: 0.68rem;" onclick="setNoticePreset('report')">
                            Laporan Perjalanan Dinas
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0.5 px-1.5" style="font-size: 0.68rem;" onclick="setNoticePreset('bast')">
                            Tanda Terima Responden
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0.5 px-1.5" style="font-size: 0.68rem;" onclick="setNoticePreset('deadline')">
                            Batas Waktu (Deadline)
                        </button>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2 px-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-sp-primary" id="btnSubmitNotice">
                        <i class="fa-solid fa-paper-plane me-1"></i> Kirim ke Papan Informasi Petugas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
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

        const message = `Halo Rekan ${name},\n\nMengingatkan terkait progres penugasan SIMPLE Anda saat ini:\n• Target Responden: ${target}\n• BAST Selesai: ${completed} (${percentage}%)\n• Sisa Belum BAST: ${pending}\n• Status Laporan Kegiatan: ${reportStatus}\n\nMohon untuk segera menyelesaikan kunjungan responden & penginputan BAST serta melengkapi Laporan Perjalanan Dinas. Terima kasih.`;
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
            <i class="fa-solid fa-user-circle text-primary"></i>
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
                        backgroundColor: '#0284c7',
                        borderRadius: 4,
                    },
                    {
                        label: 'BAST Terbit',
                        data: @json($monthlyBasts),
                        backgroundColor: '#10b981',
                        borderRadius: 4,
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

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
        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-primary">
            <i class="fa-solid fa-clipboard-check me-1"></i> Verifikasi Laporan
        </a>
    </div>
</div>

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
                        <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['report_status' => 'not_submitted'])) }}" class="text-primary text-decoration-none" style="font-size: 0.7rem;">
                            Lihat Semua
                        </a>
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
                                <div class="flex-shrink-0">
                                    @if($offWithout->wa_url)
                                        <a href="{{ $offWithout->wa_url }}" target="_blank" class="btn btn-xs btn-outline-success py-0.5 px-1.5" style="font-size: 0.68rem;" title="Kirim Pengingat WhatsApp">
                                            <i class="fa-brands fa-whatsapp me-1"></i> Ingatkan
                                        </a>
                                    @endif
                                </div>
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
                                    <button type="button" 
                                            class="btn btn-xs btn-outline-secondary py-1 px-1.5" 
                                            style="font-size: 0.72rem;"
                                            title="Lihat Draf Pesan Notifikasi"
                                            onclick="openReminderModal('{{ addslashes($off->name) }}', '{{ $off->phone ?? '-' }}', '{{ $off->target }}', '{{ $off->completed }}', '{{ $off->pending }}', '{{ $off->percentage }}', '{{ $off->has_report ? 'Sudah Diserahkan' : 'BELUM Diserahkan' }}', '{{ $off->wa_url ?? '' }}')">
                                        <i class="fa-regular fa-comment-dots"></i>
                                    </button>

                                    @if($off->wa_url)
                                        <a href="{{ $off->wa_url }}" 
                                           target="_blank" 
                                           class="btn btn-xs btn-success py-1 px-2" 
                                           style="font-size: 0.72rem;"
                                           title="Kirim follow-up WhatsApp">
                                            <i class="fa-brands fa-whatsapp me-1"></i> WA
                                        </a>
                                    @else
                                        <button class="btn btn-xs btn-light border py-1 px-2 text-muted" style="font-size: 0.72rem;" disabled>
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </button>
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
                <a href="{{ route('admin.logs.index') }}" class="btn btn-sm btn-link text-decoration-none p-0" style="font-size: 0.72rem;">Semua</a>
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
            <div class="modal-footer bg-light border-top py-2 px-3 d-flex justify-content-between">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyReminderText()">
                    <i class="fa-regular fa-copy me-1"></i> <span id="btnCopyText">Salin Teks</span>
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Tutup</button>
                    <a href="#" id="modal_wa_direct_link" target="_blank" class="btn btn-sm btn-success">
                        <i class="fa-brands fa-whatsapp me-1"></i> Kirim WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function openReminderModal(name, phone, target, completed, pending, percentage, reportStatus, waUrl) {
        document.getElementById('modal_officer_name').innerText = name;
        document.getElementById('modal_officer_phone').innerText = phone || 'Nomor HP tidak ada';

        const message = `Halo Rekan ${name},\n\nMengingatkan terkait progres penugasan SIMPLE Anda saat ini:\n• Target Responden: ${target}\n• BAST Selesai: ${completed} (${percentage}%)\n• Sisa Belum BAST: ${pending}\n• Status Laporan Kegiatan: ${reportStatus}\n\nMohon untuk segera menyelesaikan kunjungan responden & penginputan BAST serta melengkapi Laporan Perjalanan Dinas. Terima kasih.`;

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

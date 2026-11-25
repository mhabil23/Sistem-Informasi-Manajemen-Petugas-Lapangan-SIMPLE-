@extends('layouts.app')

@section('title', 'Progres Pendataan Lapangan - SIMPEL')
@section('page_title', 'Progres Pendataan Lapangan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Admin</a></li>
    <li class="breadcrumb-item">Monitoring Progres</li>
    <li class="breadcrumb-item active" aria-current="page">Progres Pendataan Lapangan</li>
@endsection

@push('styles')
<style>
/* Bento Cards & Stat Cards */
.bento-card-clean {
    background: #FFFFFF;
    border-radius: 20px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
}
.bento-stat-card {
    background: #FFFFFF;
    border-radius: 18px;
    border: 1px solid #E2E8F0;
    padding: 1.25rem;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.02);
    display: flex;
    align-items: center;
    gap: 1.1rem;
    height: 100%;
}
.bento-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
}
.btn-bps-orange {
    background: #F37021;
    color: #FFFFFF !important;
    border: none;
    font-weight: 700;
    border-radius: 999px;
    padding: 0.55rem 1.25rem;
    box-shadow: 0 4px 14px rgba(243, 112, 33, 0.25);
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    text-decoration: none;
}
.btn-bps-orange:hover {
    background: #E05D0E;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(243, 112, 33, 0.35);
}
.bento-table-card {
    background: #FFFFFF;
    border-radius: 20px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.03);
    overflow: hidden;
}
.bento-table-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #E2E8F0;
    background: #FAFAFA;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
}
.bento-table-fixed {
    table-layout: fixed;
    width: 100%;
    margin-bottom: 0;
    font-size: 0.82rem;
}
.bento-table-fixed th {
    background: #F8FAFC;
    color: #475569;
    font-weight: 700;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 0.8rem 0.6rem;
    border-bottom: 1px solid #E2E8F0;
}
.bento-table-fixed td {
    padding: 0.85rem 0.6rem;
    vertical-align: middle;
    border-bottom: 1px solid #F1F5F9;
    font-size: 0.82rem;
}
.bento-table-fixed tbody tr:hover {
    background-color: #F8FAFC;
}
.progress-thin {
    height: 7px;
    border-radius: 999px;
    background-color: #E2E8F0;
    overflow: hidden;
}
.bento-table-fixed {
    min-width: 820px;
}
@media (max-width: 767.98px) {
    .bento-stat-card {
        padding: 0.9rem;
        gap: 0.75rem;
    }
    .bento-table-header {
        padding: 0.85rem 1rem;
        flex-direction: column;
        align-items: stretch;
    }
    #progressTabs {
        flex-wrap: wrap;
        gap: 0.4rem !important;
    }
    #progressTabs .nav-item {
        flex: 1 1 100%;
    }
    #progressTabs .nav-link {
        width: 100%;
        text-align: left;
        font-size: 0.78rem;
        padding: 0.5rem 0.85rem !important;
    }
    .bento-table-fixed {
        min-width: 0 !important;
        table-layout: auto !important;
    }
    .bento-table-fixed thead {
        display: none;
    }
    .bento-table-fixed,
    .bento-table-fixed tbody,
    .bento-table-fixed tr,
    .bento-table-fixed td {
        display: block;
        width: 100% !important;
    }
    .bento-table-fixed tbody tr {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        margin: 0.65rem auto;
        width: calc(100% - 1.3rem) !important;
        padding: 0.75rem 0.9rem;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
        box-sizing: border-box;
    }
    .bento-table-fixed tbody td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        text-align: right !important;
        padding: 0.4rem 0;
        border-bottom: 1px solid #F1F5F9;
        white-space: normal !important;
    }
    .bento-table-fixed tbody td::before {
        content: attr(data-label);
        font-weight: 700;
        font-size: 0.72rem;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        text-align: left;
        flex-shrink: 0;
    }
    .bento-table-fixed tbody td[colspan]::before {
        display: none;
    }
    .bento-table-fixed tbody td[colspan] {
        display: block;
        text-align: center !important;
        border-bottom: none;
    }
    .bento-table-fixed tbody td:last-child {
        border-bottom: none;
        padding-bottom: 0.1rem;
    }
    .bento-table-fixed tbody td > div {
        max-width: 100%;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    <!-- ==============================================================
         1. FILTER & SEARCH BAR
         ============================================================== -->
    <div class="bento-card-clean p-3 mb-4">
        <form method="GET" action="{{ route('admin.progress.field') }}" class="row g-2 align-items-center">
            <!-- Filter Kegiatan -->
            <div class="col-xl-3 col-lg-3 col-md-6">
                <select name="assignment_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Pilih Kegiatan Lapangan --</option>
                    @foreach($assignments as $asgn)
                        <option value="{{ $asgn->id }}" {{ $selectedAssignmentId == $asgn->id ? 'selected' : '' }}>
                            {{ $asgn->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Wilayah Kecamatan -->
            <div class="col-xl-2 col-lg-2 col-md-6">
                <select name="district" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Pilih Wilayah Kecamatan --</option>
                    @foreach($districts as $d)
                        <option value="{{ $d }}" {{ $selectedDistrict == $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Peran Petugas -->
            <div class="col-xl-2 col-lg-2 col-md-4">
                <select name="officer_role" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Pilih Peran Petugas --</option>
                    <option value="PCL" {{ $selectedRole === 'PCL' ? 'selected' : '' }}>Pencacah Lapangan (PCL)</option>
                    <option value="PPL" {{ $selectedRole === 'PPL' ? 'selected' : '' }}>Pendata Lapangan (PPL)</option>
                    <option value="PML" {{ $selectedRole === 'PML' ? 'selected' : '' }}>Pengawas Lapangan (PML)</option>
                </select>
            </div>

            <!-- Filter Status Capaian -->
            <div class="col-xl-2 col-lg-2 col-md-4">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Pilih Status Capaian --</option>
                    <option value="completed" {{ $statusFilter === 'completed' ? 'selected' : '' }}>Selesai (100%)</option>
                    <option value="in_progress" {{ $statusFilter === 'in_progress' ? 'selected' : '' }}>Sedang Berjalan (&gt;0%)</option>
                    <option value="slow" {{ $statusFilter === 'slow' ? 'selected' : '' }}>Perlu Follow-Up (&lt;50%)</option>
                    <option value="not_started" {{ $statusFilter === 'not_started' ? 'selected' : '' }}>Belum Berjalan (0%)</option>
                </select>
            </div>

            <!-- Cari Petugas & Tombol Aksi -->
            <div class="col-xl-3 col-lg-3 col-md-8">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama, HP, NIP..." value="{{ $search }}">
                    <button type="submit" class="btn text-white fw-bold px-3 d-flex align-items-center gap-1" style="background: var(--bps-orange, #F37021);" title="Terapkan Pencarian">
                        <i class="bi bi-search"></i>
                        <span>Cari</span>
                    </button>
                    @if($selectedAssignmentId || $selectedDistrict || $selectedRole || $statusFilter || $search)
                        <a href="{{ route('admin.progress.field') }}" class="btn btn-outline-secondary px-2.5 d-flex align-items-center justify-content-center" title="Reset Semua Filter">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- ==============================================================
         2. DUA TAB UTAMA: MONITORING PER PETUGAS VS PER KECAMATAN
         ============================================================== -->
    <div class="bento-table-card mb-4">
        <div class="bento-table-header">
            <!-- Nav Tabs -->
            <ul class="nav nav-pills gap-2" id="progressTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill px-3 py-1.5 fw-bold" id="tab-officers-link" data-bs-toggle="pill" data-bs-target="#tab-officers" type="button" role="tab">
                        <i class="bi bi-person-lines-fill me-1.5"></i> Progres per Petugas Lapangan ({{ count($officerProgressList) }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-1.5 fw-bold" id="tab-teams-link" data-bs-toggle="pill" data-bs-target="#tab-teams" type="button" role="tab">
                        <i class="bi bi-people-fill me-1.5 text-primary"></i> Progres per Tim Lapangan ({{ count($teamProgressList) }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-1.5 fw-bold" id="tab-districts-link" data-bs-toggle="pill" data-bs-target="#tab-districts" type="button" role="tab">
                        <i class="bi bi-geo-alt-fill me-1.5 text-danger"></i> Sebaran per Kecamatan (15 Wilayah)
                    </button>
                </li>
            </ul>

            <!-- Tombol Export Excel -->
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.progress.field.export', ['assignment_id' => $selectedAssignmentId, 'district' => $selectedDistrict]) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 0.8rem;">
                    <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i> Unduh Rekap (CSV/Excel)
                </a>
            </div>
        </div>

        <div class="tab-content" id="progressTabsContent">

            <!-- ======================================================
                 TAB 1: PROGRES PER PETUGAS LAPANGAN
                 ====================================================== -->
            <div class="tab-pane fade show active" id="tab-officers" role="tabpanel">
                <div class="table-responsive">
                    <table class="table bento-table-fixed align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 25%;"><i class="bi bi-person-fill text-primary me-1"></i> Petugas Lapangan</th>
                                <th style="width: 17%;"><i class="bi bi-pin-map-fill text-danger me-1"></i> Wilayah Tugas</th>
                                <th class="text-center" style="width: 11%;"><i class="bi bi-bullseye me-1"></i> Target</th>
                                <th class="text-center" style="width: 11%;"><i class="bi bi-check-circle-fill text-success me-1"></i> Realisasi</th>
                                <th style="width: 18%;"><i class="bi bi-graph-up me-1 text-primary"></i> Capaian (%)</th>
                                <th class="text-center" style="width: 10%;">Status</th>
                                <th class="text-end pe-3" style="width: 8%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($officerProgressList as $item)
                                @php
                                    $off = $item['officer'];
                                    $pct = $item['percentage'];
                                    $barColor = $pct >= 100 ? 'bg-success' : ($pct >= 50 ? 'bg-primary' : ($pct > 0 ? 'bg-warning' : 'bg-danger'));
                                    $badgeRole = $item['role'] === 'PML' ? 'bg-info-subtle text-info border-info-subtle' : 'bg-primary-subtle text-primary border-primary-subtle';
                                @endphp
                                <tr>
                                    <!-- Nama Petugas -->
                                    <td data-label="Petugas">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-light text-dark fw-bold border d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; font-size: 0.78rem;">
                                                {{ strtoupper(substr($off->name, 0, 1)) }}
                                            </div>
                                            <div class="text-truncate">
                                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.84rem;">
                                                    {{ $off->name }}
                                                </div>
                                                <div class="d-flex align-items-center gap-1.5 mt-0.5">
                                                    <span class="badge border rounded-pill px-2 py-0.5" style="font-size: 0.65rem; {{ $item['role'] === 'PML' ? 'background: #EFF6FF; color: #1D4ED8;' : 'background: #F8FAFC; color: #475569;' }}">
                                                        {{ $item['role'] }}
                                                    </span>
                                                    @if($off->phone)
                                                        <span class="text-muted font-monospace" style="font-size: 0.68rem;">
                                                            <i class="bi bi-telephone text-secondary me-0.5"></i>{{ $off->phone }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Wilayah Tugas -->
                                    <td data-label="Wilayah">
                                        @if(!empty($item['districts']))
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($item['districts'] as $dist)
                                                    <span class="badge bg-light text-dark border rounded-pill px-2 py-0.5" style="font-size: 0.67rem;">
                                                        <i class="bi bi-geo-alt-fill text-danger me-0.5"></i>{{ $dist }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted" style="font-size: 0.75rem;">Semua Wilayah</span>
                                        @endif
                                    </td>

                                    <!-- Target -->
                                    <td class="text-center" data-label="Target">
                                        <span class="fw-bold font-monospace text-dark" style="font-size: 0.88rem;">{{ $item['target'] }}</span>
                                        <span class="text-muted d-block" style="font-size: 0.65rem;">Sampel</span>
                                    </td>

                                    <!-- Realisasi -->
                                    <td class="text-center" data-label="Realisasi">
                                        <span class="fw-bold font-monospace text-success" style="font-size: 0.88rem;">{{ $item['completed'] }}</span>
                                        <span class="text-muted d-block" style="font-size: 0.65rem;">BAST</span>
                                    </td>

                                    <!-- Persentase & Bar -->
                                    <td data-label="Capaian">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold" style="font-size: 0.78rem;">{{ $pct }}%</span>
                                            <span class="text-muted" style="font-size: 0.68rem;">Sisa: {{ $item['pending'] }}</span>
                                        </div>
                                        <div class="progress progress-thin">
                                            <div class="progress-bar {{ $barColor }}" role="progressbar" style="width: {{ $pct }}%;" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="text-center" data-label="Status">
                                        @if($pct >= 100)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1" style="font-size: 0.68rem;">
                                                <i class="bi bi-check-circle-fill me-1"></i> Tuntas
                                            </span>
                                        @elseif($pct > 0)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1" style="font-size: 0.68rem;">
                                                <i class="bi bi-arrow-repeat me-1"></i> Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1" style="font-size: 0.68rem;">
                                                <i class="bi bi-exclamation-circle-fill me-1"></i> Belum
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Aksi -->
                                    <td class="text-end pe-3" data-label="Aksi">
                                        <div class="d-flex align-items-center justify-content-end gap-1">
                                            @if($item['wa_url'])
                                                <a href="{{ $item['wa_url'] }}" target="_blank" class="btn btn-sm btn-outline-success rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" title="Follow-up WhatsApp">
                                                    <i class="bi bi-whatsapp"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('admin.respondents.index', ['assignment_id' => $selectedAssignmentId, 'officer_id' => $off->id]) }}" class="btn btn-sm btn-outline-primary rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" title="Lihat Responden Petugas Ini">
                                                <i class="bi bi-arrow-right-short" style="font-size: 1.1rem;"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-person-x display-6 mb-2 text-secondary opacity-50 d-block"></i>
                                        Tidak ada data petugas lapangan yang sesuai dengan filter pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ======================================================
                 TAB 2: MONITORING PROGRES PER TIM LAPANGAN (PML & PCL)
                 ====================================================== -->
            <div class="tab-pane fade" id="tab-teams" role="tabpanel">
                <div class="table-responsive">
                    <table class="table bento-table-fixed align-middle">
                        <thead>
                            <tr>
                                <th style="width: 4%;">#</th>
                                <th style="width: 25%;">KETUA TIM (PML / PENGAWAS)</th>
                                <th style="width: 20%;">ANGGOTA REGU (PCL)</th>
                                <th style="width: 13%;">WILAYAH</th>
                                <th style="width: 10%;" class="text-center">TARGET RES</th>
                                <th style="width: 10%;" class="text-center">REALISASI</th>
                                <th style="width: 18%;">CAPAIAN TIM (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teamProgressList as $index => $t)
                                @php
                                    $tPct = $t['percentage'];
                                    $tBar = $tPct >= 100 ? 'bg-success' : ($tPct >= 50 ? 'bg-primary' : ($tPct > 0 ? 'bg-warning' : 'bg-secondary'));
                                @endphp
                                <tr>
                                    <td class="text-center text-muted fw-bold" style="font-size: 0.78rem;" data-label="No">{{ $index + 1 }}</td>
                                    <td data-label="Ketua Tim (PML)">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle p-2 bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                {{ substr($t['pml']->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <strong class="text-dark d-block" style="font-size: 0.85rem;">{{ $t['pml']->name }}</strong>
                                                <span class="badge bg-primary-subtle text-primary border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                                    Ketua Tim / PML
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Anggota PCL">
                                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                            <span class="badge bg-light text-dark border rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                                                <i class="bi bi-person-fill text-muted me-1"></i>{{ count($t['members']) }} Anggota PCL
                                            </span>
                                        </div>
                                    </td>
                                    <td data-label="Wilayah">
                                        <span class="text-muted" style="font-size: 0.78rem;">
                                            <i class="bi bi-geo-alt me-0.5 text-danger"></i>{{ $t['district'] }}
                                        </span>
                                    </td>
                                    <td class="text-center" data-label="Target">
                                        <span class="fw-bold font-monospace text-dark" style="font-size: 0.88rem;">{{ number_format($t['target']) }}</span>
                                    </td>
                                    <td class="text-center" data-label="Realisasi">
                                        <span class="fw-bold font-monospace text-success" style="font-size: 0.88rem;">{{ number_format($t['completed']) }}</span>
                                    </td>
                                    <td data-label="Capaian Tim">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-dark" style="font-size: 0.78rem;">{{ $tPct }}%</span>
                                            <a href="{{ route('admin.basts.respondents.index', ['team_id' => $t['pml']->id, 'assignment_id' => $selectedAssignmentId]) }}" class="text-decoration-none text-primary fw-semibold" style="font-size: 0.7rem;">
                                                Lihat BAST Tim <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                        <div class="progress progress-thin">
                                            <div class="progress-bar {{ $tBar }}" role="progressbar" style="width: {{ $tPct }}%;" aria-valuenow="{{ $tPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-people display-6 d-block mb-2 text-muted opacity-50"></i>
                                        Tidak ada data tim pendataan lapangan yang sesuai filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ======================================================
                 TAB 3: SEBARAN PER WILAYAH KECAMATAN (15 KECAMATAN)
                 ====================================================== -->
            <div class="tab-pane fade" id="tab-districts" role="tabpanel">
                <div class="table-responsive">
                    <table class="table bento-table-fixed align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 6%;" class="text-center">No</th>
                                <th style="width: 28%;"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Kecamatan (Kolaka Utara)</th>
                                <th class="text-center" style="width: 14%;"><i class="bi bi-people-fill text-primary me-1"></i> Petugas Terlibat</th>
                                <th class="text-center" style="width: 14%;"><i class="bi bi-bullseye me-1"></i> Target Sampel</th>
                                <th class="text-center" style="width: 14%;"><i class="bi bi-check-circle-fill text-success me-1"></i> Realisasi BAST</th>
                                <th style="width: 16%;"><i class="bi bi-graph-up text-primary me-1"></i> Capaian (%)</th>
                                <th class="text-center" style="width: 8%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($districtSummaries as $index => $dist)
                                @php
                                    $dPct = $dist['percentage'];
                                    $dBar = $dPct >= 100 ? 'bg-success' : ($dPct >= 50 ? 'bg-primary' : ($dPct > 0 ? 'bg-warning' : 'bg-secondary'));
                                @endphp
                                <tr>
                                    <td class="text-center text-muted fw-bold" style="font-size: 0.78rem;" data-label="No">{{ $index + 1 }}</td>
                                    <td data-label="Kecamatan">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-2 p-1 bg-light border text-danger d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                                <i class="bi bi-geo-alt-fill"></i>
                                            </div>
                                            <strong class="text-dark" style="font-size: 0.85rem;">Kecamatan {{ $dist['name'] }}</strong>
                                        </div>
                                    </td>
                                    <td class="text-center" data-label="Petugas">
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 font-monospace" style="font-size: 0.72rem;">
                                            {{ $dist['officers_count'] }} Petugas
                                        </span>
                                    </td>
                                    <td class="text-center" data-label="Target">
                                        <span class="fw-bold font-monospace text-dark" style="font-size: 0.88rem;">{{ number_format($dist['total']) }}</span>
                                    </td>
                                    <td class="text-center" data-label="Realisasi BAST">
                                        <span class="fw-bold font-monospace text-success" style="font-size: 0.88rem;">{{ number_format($dist['completed']) }}</span>
                                    </td>
                                    <td data-label="Capaian">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-dark" style="font-size: 0.78rem;">{{ $dPct }}%</span>
                                            <span class="text-muted" style="font-size: 0.68rem;">Sisa: {{ $dist['pending'] }}</span>
                                        </div>
                                        <div class="progress progress-thin">
                                            <div class="progress-bar {{ $dBar }}" role="progressbar" style="width: {{ $dPct }}%;" aria-valuenow="{{ $dPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td class="text-center" data-label="Aksi">
                                        <a href="{{ route('admin.respondents.index', ['assignment_id' => $selectedAssignmentId, 'district' => $dist['name']]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1" style="font-size: 0.72rem;" title="Lihat Data Responden di Kecamatan Ini">
                                            Detail <i class="bi bi-arrow-right ms-0.5"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

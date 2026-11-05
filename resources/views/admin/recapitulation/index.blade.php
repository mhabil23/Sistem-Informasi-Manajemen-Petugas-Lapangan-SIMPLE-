@extends('layouts.app')

@section('title', 'Rekapitulasi & Export Data')
@section('page_title', 'Rekapitulasi Administrasi & Pelaporan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Rekapitulasi & Export</li>
@endsection

@section('content')
<!-- Header & Export Actions -->
<div class="row align-items-center mb-4">
    <div class="col-lg-6">
        <h3 class="fw-bold text-dark mb-1">Rekapitulasi & Export Data</h3>
        <p class="text-muted small mb-0">Analisis komprehensif data responden, dokumen BAST, kinerja laporan kegiatan lapangan, dan peserta pelatihan SPKRT.</p>
    </div>
    <div class="col-lg-6 text-lg-end mt-3 mt-lg-0">
        <div class="dropdown d-inline-block me-2">
            <button class="btn btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-file-excel me-1"></i> Export Excel / CSV
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><a class="dropdown-item" href="{{ route('admin.recapitulation.excel', array_merge(request()->query(), ['dataset' => 'respondents'])) }}"><i class="fa-solid fa-users me-2 text-primary"></i> Data Responden</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.recapitulation.excel', array_merge(request()->query(), ['dataset' => 'basts'])) }}"><i class="fa-solid fa-file-contract me-2 text-info"></i> Seluruh BAST</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.recapitulation.excel', array_merge(request()->query(), ['dataset' => 'reports'])) }}"><i class="fa-solid fa-clipboard-check me-2 text-warning"></i> Laporan Petugas</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.recapitulation.excel', array_merge(request()->query(), ['dataset' => 'trainings'])) }}"><i class="fa-solid fa-graduation-cap me-2 text-success"></i> Peserta Pelatihan</a></li>
            </ul>
        </div>
        <a href="{{ route('admin.recapitulation.pdf', request()->query()) }}" class="btn btn-outline-danger">
            <i class="fa-solid fa-file-pdf me-1"></i> Export PDF Rekapitulasi
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="sp-card mb-4">
    <div class="sp-card-body p-3">
        <form method="GET" action="{{ route('admin.recapitulation.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">DARI TANGGAL:</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">SAMPAI TANGGAL:</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">KEGIATAN:</label>
                <select name="assignment_id" class="form-select">
                    <option value="">-- Semua --</option>
                    @foreach($assignments as $asgn)
                        <option value="{{ $asgn->id }}" {{ request('assignment_id') == $asgn->id ? 'selected' : '' }}>{{ $asgn->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">PETUGAS:</label>
                <select name="officer_id" class="form-select">
                    <option value="">-- Semua --</option>
                    @foreach($officers as $off)
                        <option value="{{ $off->id }}" {{ request('officer_id') == $off->id ? 'selected' : '' }}>{{ $off->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2 pt-4">
                <button type="submit" class="btn btn-sp-primary flex-fill">
                    <i class="fa-solid fa-filter me-1"></i> Terapkan
                </button>
                @if(request()->hasAny(['date_from', 'date_to', 'assignment_id', 'officer_id']))
                    <a href="{{ route('admin.recapitulation.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-2 mb-3">
    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Total Responden</div>
                <div class="sp-stat-value">{{ number_format($summary['counts']['total_respondents']) }}</div>
                <div class="sp-stat-desc">Sesuai filter aktif</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                <i class="fa-solid fa-file-signature"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Total Dokumen BAST</div>
                <div class="sp-stat-value text-success">{{ number_format($summary['counts']['total_bast_resp'] + $summary['counts']['total_bast_officer']) }}</div>
                <div class="sp-stat-desc">{{ $summary['counts']['total_bast_resp'] }} Responden / {{ $summary['counts']['total_bast_officer'] }} Petugas</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0e7ff; color: #4338ca;">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Laporan Disetujui</div>
                <div class="sp-stat-value text-primary">{{ number_format($summary['counts']['reports_approved']) }}</div>
                <div class="sp-stat-desc">Dari {{ $summary['counts']['total_reports'] }} total laporan</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #fef3c7; color: #d97706;">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Peserta Pelatihan</div>
                <div class="sp-stat-value text-warning">{{ number_format($summary['counts']['total_participants']) }}</div>
                <div class="sp-stat-desc">Terdata di agenda bimtek</div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Rekapitulasi -->
<div class="sp-card">
    <div class="sp-card-header p-0 bg-light border-bottom">
        <ul class="nav nav-tabs border-0 px-3 pt-2" id="recapTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="resp-tab" data-bs-toggle="tab" data-bs-target="#resp-pane" type="button" role="tab">
                    <i class="fa-solid fa-users me-1 text-primary"></i> Rekap Responden ({{ $summary['respondents']->count() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="bast-tab" data-bs-toggle="tab" data-bs-target="#bast-pane" type="button" role="tab">
                    <i class="fa-solid fa-file-contract me-1 text-success"></i> Rekap BAST ({{ $summary['respondentBasts']->count() + $summary['officerBasts']->count() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="rep-tab" data-bs-toggle="tab" data-bs-target="#rep-pane" type="button" role="tab">
                    <i class="fa-solid fa-clipboard-user me-1 text-warning"></i> Rekap Laporan Petugas ({{ $summary['reports']->count() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="train-tab" data-bs-toggle="tab" data-bs-target="#train-pane" type="button" role="tab">
                    <i class="fa-solid fa-graduation-cap me-1 text-info"></i> Rekap Pelatihan SPKRT
                </button>
            </li>
        </ul>
    </div>
    <div class="sp-card-body p-4">
        <div class="tab-content" id="recapTabsContent">
            
            <!-- TAB 1: RESPONDEN -->
            <div class="tab-pane fade show active" id="resp-pane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table sp-table align-middle">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Responden</th>
                                <th>Instansi / Lembaga</th>
                                <th>Alamat</th>
                                <th>Kegiatan</th>
                                <th>Petugas Lapangan</th>
                                <th>Tanggal Input</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($summary['respondents']->take(50) as $i => $r)
                                <tr>
                                    <td class="text-muted small">{{ $i + 1 }}</td>
                                    <td class="fw-bold text-dark">{{ $r->name }}</td>
                                    <td><span class="badge bg-light text-primary border">{{ $r->agency }}</span></td>
                                    <td class="small text-secondary">{{ \Illuminate\Support\Str::limit($r->address, 30) }}</td>
                                    <td class="small text-dark">{{ $r->assignment ? $r->assignment->title : '-' }}</td>
                                    <td class="small fw-semibold text-dark">{{ $r->officer ? $r->officer->name : '-' }}</td>
                                    <td class="small text-muted">{{ $r->input_date ? $r->input_date->format('d/m/Y') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Tidak ada data responden pada rentang filter ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: BAST -->
            <div class="tab-pane fade" id="bast-pane" role="tabpanel">
                <h6 class="fw-bold text-dark mb-3">1. Berita Acara Serah Terima (BAST) Responden</h6>
                <div class="table-responsive mb-4">
                    <table class="table sp-table align-middle">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nomor BAST</th>
                                <th>Nama Responden</th>
                                <th>Tanggal</th>
                                <th>Petugas</th>
                                <th>Status Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($summary['respondentBasts']->take(30) as $i => $b)
                                <tr>
                                    <td class="text-muted small">{{ $i + 1 }}</td>
                                    <td class="fw-bold font-monospace text-primary">{{ $b->bast_number }}</td>
                                    <td class="text-dark">{{ $b->respondent ? $b->respondent->name : '-' }}</td>
                                    <td class="small text-muted">{{ $b->bast_date ? $b->bast_date->format('d/m/Y') : '-' }}</td>
                                    <td class="small text-dark">{{ $b->officer ? $b->officer->name : '-' }}</td>
                                    <td><span class="badge bg-success-subtle text-success border border-success-subtle">{{ ucfirst($b->status) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-3 text-muted">Tidak ada data BAST Responden.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <h6 class="fw-bold text-dark mb-3">2. Berita Acara Serah Terima (BAST) Petugas</h6>
                <div class="table-responsive">
                    <table class="table sp-table align-middle">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nomor BAST</th>
                                <th>Petugas Lapangan</th>
                                <th>Kegiatan</th>
                                <th>Tanggal</th>
                                <th>Status Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($summary['officerBasts']->take(30) as $i => $ob)
                                <tr>
                                    <td class="text-muted small">{{ $i + 1 }}</td>
                                    <td class="fw-bold font-monospace text-primary">{{ $ob->bast_number }}</td>
                                    <td class="text-dark">{{ $ob->officer ? $ob->officer->name : '-' }}</td>
                                    <td class="small text-secondary">{{ $ob->assignment ? $ob->assignment->title : '-' }}</td>
                                    <td class="small text-muted">{{ $ob->bast_date ? $ob->bast_date->format('d/m/Y') : '-' }}</td>
                                    <td><span class="badge bg-success-subtle text-success border border-success-subtle">{{ ucfirst($ob->status) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-3 text-muted">Tidak ada data BAST Petugas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: LAPORAN -->
            <div class="tab-pane fade" id="rep-pane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table sp-table align-middle">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nomor Laporan</th>
                                <th>Petugas</th>
                                <th>Judul Kegiatan</th>
                                <th>Tanggal</th>
                                <th>Status Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($summary['reports']->take(50) as $i => $rep)
                                <tr>
                                    <td class="text-muted small">{{ $i + 1 }}</td>
                                    <td class="fw-bold font-monospace text-dark">{{ $rep->report_number }}</td>
                                    <td class="fw-semibold text-dark">{{ $rep->user ? $rep->user->name : '-' }}</td>
                                    <td class="text-primary">{{ $rep->activity_title }}</td>
                                    <td class="small text-muted">{{ $rep->activity_date ? $rep->activity_date->format('d/m/Y') : '-' }}</td>
                                    <td><span class="badge {{ $rep->status_badge }} px-2 py-1">{{ $rep->status_label }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada laporan petugas yang sesuai filter.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 4: PELATIHAN -->
            <div class="tab-pane fade" id="train-pane" role="tabpanel">
                <div class="row g-4 mb-4">
                    <div class="col-md-5">
                        <div class="p-3 bg-light rounded border h-100">
                            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-chart-pie text-primary me-2"></i> Distribusi Kategori Peserta</h6>
                            <ul class="list-group list-group-flush small">
                                @forelse($summary['participantsByCategory'] as $cat => $total)
                                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-2">
                                        <span class="fw-semibold">{{ $cat }}</span>
                                        <span class="badge bg-primary rounded-pill">{{ $total }} Peserta</span>
                                    </li>
                                @empty
                                    <li class="list-group-item bg-transparent text-muted fst-italic">Belum ada data peserta pelatihan.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="p-3 bg-light rounded border h-100">
                            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-list-check text-primary me-2"></i> Agenda Pelatihan & Bimtek</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless small align-middle">
                                    <thead>
                                        <tr class="text-muted border-bottom">
                                            <th>Pelatihan</th>
                                            <th>Jadwal</th>
                                            <th>Lokasi</th>
                                            <th>Peserta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($summary['trainings'] as $t)
                                            <tr class="border-bottom">
                                                <td class="fw-bold text-dark">{{ $t->title }}</td>
                                                <td>{{ $t->start_date->format('d/m/y') }}</td>
                                                <td>{{ $t->location }}</td>
                                                <td><span class="badge bg-info text-dark">{{ $t->participants_count }}</span></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-muted">Belum ada agenda pelatihan.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection


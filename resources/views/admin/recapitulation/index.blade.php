@extends('layouts.app')

@section('title', 'Rekapitulasi & Export Data')
@section('page_title', 'Rekapitulasi & Export')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Rekapitulasi & Export</li>
@endsection

@push('styles')
<style>
    .bento-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    .bento-table-container-fixed {
        width: 100%;
        overflow: hidden;
    }
    .bento-table {
        table-layout: fixed;
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    .bento-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
        border-top: none;
        white-space: nowrap;
        vertical-align: middle;
    }
    .bento-table tbody td {
        padding: 12px 14px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.82rem;
        color: #334155;
        background: #ffffff;
        transition: background 0.2s ease;
    }
    .bento-table tbody tr:hover td {
        background-color: #f8fafc;
    }
    .bento-table tbody tr:last-child td {
        border-bottom: none;
    }
    .btn-bps-orange {
        background-color: #F37021;
        border-color: #F37021;
        color: #ffffff;
        font-weight: 600;
    }
    .btn-bps-orange:hover, .btn-bps-orange:focus {
        background-color: #d95d13;
        border-color: #d95d13;
        color: #ffffff;
    }
    .nav-tabs-bento {
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        padding: 8px 16px 0 16px;
    }
    .nav-tabs-bento .nav-link {
        border: none;
        color: #64748b;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 10px 16px;
        border-radius: 8px 8px 0 0;
        transition: all 0.2s ease;
    }
    .nav-tabs-bento .nav-link.active {
        color: #F37021;
        background: #ffffff;
        border-bottom: 2px solid #F37021;
    }
    @media (max-width: 767.98px) {
        .bento-table thead {
            display: none;
        }
        .bento-table, .bento-table tbody, .bento-table tr, .bento-table td {
            display: block;
            width: 100% !important;
        }
        .bento-table tr {
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.5rem;
            background: #fff;
        }
        .bento-table td {
            text-align: right;
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .bento-table td:last-child {
            border-bottom: none;
        }
        .bento-table td::before {
            content: attr(data-label);
            float: left;
            font-weight: 600;
            color: #64748b;
            font-size: 0.75rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Filter Bar -->
<div class="bento-card mb-4">
    <div class="p-3">
        <form method="GET" action="{{ route('admin.recapitulation.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1 fw-semibold">DARI TANGGAL:</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1 fw-semibold">SAMPAI TANGGAL:</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1 fw-semibold">KEGIATAN:</label>
                <select name="assignment_id" class="form-select">
                    <option value="">-- Semua --</option>
                    @foreach($assignments as $asgn)
                        <option value="{{ $asgn->id }}" {{ request('assignment_id') == $asgn->id ? 'selected' : '' }}>{{ $asgn->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1 fw-semibold">PETUGAS:</label>
                <select name="officer_id" class="form-select">
                    <option value="">-- Semua --</option>
                    @foreach($officers as $off)
                        <option value="{{ $off->id }}" {{ request('officer_id') == $off->id ? 'selected' : '' }}>{{ $off->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2 pt-md-4">
                <button type="submit" class="btn btn-bps-orange flex-fill">
                    <i class="bi bi-funnel me-1"></i> Terapkan
                </button>
                @if(request()->hasAny(['date_from', 'date_to', 'assignment_id', 'officer_id']))
                    <a href="{{ route('admin.recapitulation.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tabs Rekapitulasi & Export Card -->
<div class="bento-card mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center p-3 border-bottom gap-2">
        <div>
            <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">Rekapitulasi Administrasi &amp; Pelaporan</h5>
            <span class="badge bg-light text-secondary border mt-1">Data Terintegrasi</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <div class="dropdown d-inline-block">
                <button class="btn btn-sm btn-outline-success dropdown-toggle rounded-pill px-3 py-1.5" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.82rem;">
                    <i class="bi bi-file-earmark-excel me-1"></i> Export Excel / CSV
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px;">
                    <li><a class="dropdown-item py-2" href="{{ route('admin.recapitulation.excel', array_merge(request()->query(), ['dataset' => 'respondents'])) }}"><i class="bi bi-people me-2 text-primary"></i> Data Responden</a></li>
                    <li><a class="dropdown-item py-2" href="{{ route('admin.recapitulation.excel', array_merge(request()->query(), ['dataset' => 'basts'])) }}"><i class="bi bi-file-earmark-check me-2 text-info"></i> Seluruh BAST</a></li>
                    <li><a class="dropdown-item py-2" href="{{ route('admin.recapitulation.excel', array_merge(request()->query(), ['dataset' => 'reports'])) }}"><i class="bi bi-journal-check me-2 text-warning"></i> Laporan Petugas</a></li>
                    <li><a class="dropdown-item py-2" href="{{ route('admin.recapitulation.excel', array_merge(request()->query(), ['dataset' => 'trainings'])) }}"><i class="bi bi-mortarboard me-2 text-success"></i> Peserta Pelatihan</a></li>
                </ul>
            </div>
            <a href="{{ route('admin.recapitulation.pdf', request()->query()) }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5" style="font-size: 0.82rem;">
                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF Rekapitulasi
            </a>
        </div>
    </div>

    <!-- Tab navigation -->
    <ul class="nav nav-tabs nav-tabs-bento" id="recapTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="resp-tab" data-bs-toggle="tab" data-bs-target="#resp-pane" type="button" role="tab">
                <i class="bi bi-people me-1 text-primary"></i> Rekap Responden ({{ $summary['respondents']->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="bast-tab" data-bs-toggle="tab" data-bs-target="#bast-pane" type="button" role="tab">
                <i class="bi bi-file-earmark-check me-1 text-success"></i> Rekap BAST ({{ $summary['respondentBasts']->count() + $summary['officerBasts']->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="rep-tab" data-bs-toggle="tab" data-bs-target="#rep-pane" type="button" role="tab">
                <i class="bi bi-journal-text me-1 text-warning"></i> Rekap Laporan Petugas ({{ $summary['reports']->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="train-tab" data-bs-toggle="tab" data-bs-target="#train-pane" type="button" role="tab">
                <i class="bi bi-mortarboard me-1 text-info"></i> Rekap Pelatihan SPKRT
            </button>
        </li>
    </ul>

    <div class="p-0">
        <div class="tab-content" id="recapTabsContent">
            
            <!-- TAB 1: RESPONDEN -->
            <div class="tab-pane fade show active" id="resp-pane" role="tabpanel">
                <div class="bento-table-container-fixed">
                    <table class="bento-table align-middle">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center">NO</th>
                                <th style="width: 22%;">NAMA RESPONDEN</th>
                                <th style="width: 18%;">INSTANSI / LEMBAGA</th>
                                <th style="width: 20%;">ALAMAT</th>
                                <th style="width: 15%;">KEGIATAN</th>
                                <th style="width: 12%;">PETUGAS</th>
                                <th style="width: 8%;" class="text-center">TGL INPUT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($summary['respondents']->take(50) as $i => $r)
                                <tr>
                                    <td class="text-muted small text-center" data-label="No">{{ $i + 1 }}</td>
                                    <td data-label="Nama Responden" class="fw-bold text-dark text-truncate">{{ $r->name }}</td>
                                    <td data-label="Instansi / Lembaga"><span class="badge bg-light text-primary border text-truncate d-inline-block" style="max-width: 160px;">{{ $r->agency }}</span></td>
                                    <td data-label="Alamat" class="small text-secondary text-truncate">{{ $r->address }}</td>
                                    <td data-label="Kegiatan" class="small text-dark text-truncate">{{ $r->assignment ? $r->assignment->title : '-' }}</td>
                                    <td data-label="Petugas" class="small fw-semibold text-dark text-truncate">{{ $r->officer ? $r->officer->name : '-' }}</td>
                                    <td data-label="Tgl Input" class="small text-muted text-center">{{ $r->input_date ? $r->input_date->format('d/m/Y') : '-' }}</td>
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
            <div class="tab-pane fade p-3" id="bast-pane" role="tabpanel">
                <h6 class="fw-bold text-dark mb-2 small"><i class="bi bi-file-earmark-person text-success me-1"></i> 1. Berita Acara Serah Terima (BAST) Responden</h6>
                <div class="bento-table-container-fixed border rounded-3 mb-4">
                    <table class="bento-table align-middle">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center">NO</th>
                                <th style="width: 25%;">NOMOR BAST</th>
                                <th style="width: 25%;">NAMA RESPONDEN</th>
                                <th style="width: 15%;">TANGGAL</th>
                                <th style="width: 20%;">PETUGAS</th>
                                <th style="width: 10%;" class="text-center">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($summary['respondentBasts']->take(30) as $i => $b)
                                <tr>
                                    <td class="text-muted small text-center" data-label="No">{{ $i + 1 }}</td>
                                    <td data-label="Nomor BAST" class="fw-bold font-monospace text-primary text-truncate">{{ $b->bast_number }}</td>
                                    <td data-label="Nama Responden" class="text-dark text-truncate">{{ $b->respondent ? $b->respondent->name : '-' }}</td>
                                    <td data-label="Tanggal" class="small text-muted">{{ $b->bast_date ? $b->bast_date->format('d/m/Y') : '-' }}</td>
                                    <td data-label="Petugas" class="small text-dark text-truncate">{{ $b->officer ? $b->officer->name : '-' }}</td>
                                    <td data-label="Status" class="text-center"><span class="badge bg-success-subtle text-success border border-success-subtle">{{ ucfirst($b->status) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-3 text-muted">Tidak ada data BAST Responden.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <h6 class="fw-bold text-dark mb-2 small"><i class="bi bi-file-earmark-text text-primary me-1"></i> 2. Berita Acara Serah Terima (BAST) Petugas</h6>
                <div class="bento-table-container-fixed border rounded-3">
                    <table class="bento-table align-middle">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center">NO</th>
                                <th style="width: 25%;">NOMOR BAST</th>
                                <th style="width: 25%;">PETUGAS LAPANGAN</th>
                                <th style="width: 20%;">KEGIATAN</th>
                                <th style="width: 15%;">TANGGAL</th>
                                <th style="width: 10%;" class="text-center">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($summary['officerBasts']->take(30) as $i => $ob)
                                <tr>
                                    <td class="text-muted small text-center" data-label="No">{{ $i + 1 }}</td>
                                    <td data-label="Nomor BAST" class="fw-bold font-monospace text-primary text-truncate">{{ $ob->bast_number }}</td>
                                    <td data-label="Petugas" class="text-dark text-truncate">{{ $ob->officer ? $ob->officer->name : '-' }}</td>
                                    <td data-label="Kegiatan" class="small text-secondary text-truncate">{{ $ob->assignment ? $ob->assignment->title : '-' }}</td>
                                    <td data-label="Tanggal" class="small text-muted">{{ $ob->bast_date ? $ob->bast_date->format('d/m/Y') : '-' }}</td>
                                    <td data-label="Status" class="text-center"><span class="badge bg-success-subtle text-success border border-success-subtle">{{ ucfirst($ob->status) }}</span></td>
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
                <div class="bento-table-container-fixed">
                    <table class="bento-table align-middle">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center">NO</th>
                                <th style="width: 20%;">NOMOR LAPORAN</th>
                                <th style="width: 22%;">PETUGAS</th>
                                <th style="width: 28%;">JUDUL KEGIATAN</th>
                                <th style="width: 12%;">TANGGAL</th>
                                <th style="width: 13%;" class="text-center">STATUS VERIFIKASI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($summary['reports']->take(50) as $i => $rep)
                                <tr>
                                    <td class="text-muted small text-center" data-label="No">{{ $i + 1 }}</td>
                                    <td data-label="Nomor Laporan" class="fw-bold font-monospace text-dark text-truncate">{{ $rep->report_number }}</td>
                                    <td data-label="Petugas" class="fw-semibold text-dark text-truncate">{{ $rep->user ? $rep->user->name : '-' }}</td>
                                    <td data-label="Judul Kegiatan" class="text-primary text-truncate">{{ $rep->activity_title }}</td>
                                    <td data-label="Tanggal" class="small text-muted">{{ $rep->activity_date ? $rep->activity_date->format('d/m/Y') : '-' }}</td>
                                    <td data-label="Status" class="text-center"><span class="badge {{ $rep->status_badge }} px-2 py-1">{{ $rep->status_label }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada laporan petugas yang sesuai filter.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 4: PELATIHAN -->
            <div class="tab-pane fade p-3" id="train-pane" role="tabpanel">
                <div class="row g-3">
                    <div class="col-md-5">
                        <div class="p-3 bg-light rounded-3 border h-100">
                            <h6 class="fw-bold text-dark mb-3 small"><i class="bi bi-pie-chart text-primary me-2"></i> Distribusi Kategori Peserta</h6>
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
                        <div class="p-3 bg-light rounded-3 border h-100">
                            <h6 class="fw-bold text-dark mb-3 small"><i class="bi bi-list-check text-primary me-2"></i> Agenda Pelatihan & Bimtek</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless small align-middle">
                                    <thead>
                                        <tr class="text-muted border-bottom">
                                            <th>Pelatihan</th>
                                            <th>Jadwal</th>
                                            <th>Lokasi</th>
                                            <th class="text-center">Peserta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($summary['trainings'] as $t)
                                            <tr class="border-bottom">
                                                <td class="fw-bold text-dark text-truncate" style="max-width: 180px;">{{ $t->title }}</td>
                                                <td>{{ $t->start_date->format('d/m/y') }}</td>
                                                <td class="text-secondary text-truncate" style="max-width: 120px;">{{ $t->location }}</td>
                                                <td class="text-center"><span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">{{ $t->participants_count }}</span></td>
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

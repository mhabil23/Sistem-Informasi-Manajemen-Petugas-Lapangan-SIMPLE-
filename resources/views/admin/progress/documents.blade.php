@extends('layouts.app')

@section('title', 'Progres Pengolahan Dokumen - SIMPEL')
@section('page_title', 'Progres Pengolahan Dokumen Kuesioner')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Admin</a></li>
    <li class="breadcrumb-item">Monitoring Progres</li>
    <li class="breadcrumb-item active" aria-current="page">Progres Pengolahan Dokumen</li>
@endsection

@push('styles')
<style>
/* Bento Cards & Stat Cards */
.bento-card-clean {
    background: #FFFFFF;
    border-radius: 18px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
}
.bento-stat-card {
    background: #FFFFFF;
    border-radius: 18px;
    border: 1px solid #E2E8F0;
    padding: 1.15rem;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.02);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.bento-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(15, 23, 42, 0.05);
}
.bento-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
}
.bento-table-card {
    background: #FFFFFF;
    border-radius: 18px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.03);
    overflow: hidden;
    width: 100%;
}
.bento-table-header {
    padding: 0.9rem 1.25rem;
    border-bottom: 1px solid #E2E8F0;
    background: #FAFAFA;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
}
.bento-table-container-fixed {
    width: 100%;
    overflow-x: auto;
}
.bento-table-fixed {
    table-layout: fixed;
    width: 100%;
    margin-bottom: 0;
    font-size: 0.8rem;
}
.bento-table-fixed th {
    background: #F8FAFC;
    color: #475569;
    font-weight: 700;
    font-size: 0.68rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 0.75rem 0.5rem;
    border-bottom: 1px solid #E2E8F0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.bento-table-fixed td {
    padding: 0.75rem 0.5rem;
    vertical-align: middle;
    border-bottom: 1px solid #F1F5F9;
    font-size: 0.8rem;
    overflow: hidden;
}
.bento-table-fixed tbody tr:hover {
    background-color: #F8FAFC;
}
.progress-thin {
    height: 6px;
    border-radius: 999px;
    background-color: #E2E8F0;
    overflow: hidden;
}
.stage-pipeline-badge {
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.25rem 0.6rem;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}
.pipeline-step-box {
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    border-radius: 14px;
    padding: 0.85rem 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    position: relative;
}
.pipeline-step-num {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.8rem;
    flex-shrink: 0;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    <!-- ==============================================================
         1. FILTER & SEARCH BAR
         ============================================================== -->
    <div class="bento-card-clean p-3 mb-4">
        <form method="GET" action="{{ route('admin.progress.documents') }}" class="row g-2 align-items-center">
            <!-- Filter Kegiatan Sensus / Survei -->
            <div class="col-xl-3 col-lg-3 col-md-6">
                <select name="assignment_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Kegiatan Survei / Sensus --</option>
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
                    <option value="">-- Semua Kecamatan --</option>
                    @foreach($districts as $d)
                        <option value="{{ $d }}" {{ $selectedDistrict == $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status Pengolahan -->
            <div class="col-xl-2 col-lg-2 col-md-4">
                <select name="processing_status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Status Pengolahan --</option>
                    <option value="belum_diterima" {{ $selectedStatus === 'belum_diterima' ? 'selected' : '' }}>Belum Diterima</option>
                    <option value="receiving" {{ $selectedStatus === 'receiving' ? 'selected' : '' }}>1. Penerimaan (Receiving)</option>
                    <option value="editing_coding" {{ $selectedStatus === 'editing_coding' ? 'selected' : '' }}>2. Editing & Coding (E-C)</option>
                    <option value="entry" {{ $selectedStatus === 'entry' ? 'selected' : '' }}>3. Perekaman Data (Entry)</option>
                    <option value="clean" {{ $selectedStatus === 'clean' ? 'selected' : '' }}>4. Clean Data (Siap Rilis)</option>
                </select>
            </div>

            <!-- Pencarian Responden / Petugas & Tombol Aksi -->
            <div class="col-xl-5 col-lg-5 col-md-8">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama responden, KRT, petugas, alamat..." value="{{ $search }}">
                    <button type="submit" class="btn text-white fw-bold px-3 d-flex align-items-center gap-1" style="background: var(--bps-orange, #F37021);" title="Terapkan Pencarian">
                        <i class="bi bi-search"></i>
                        <span>Cari</span>
                    </button>
                    @if($selectedAssignmentId || $selectedDistrict || $selectedStatus || $search)
                        <a href="{{ route('admin.progress.documents') }}" class="btn btn-outline-secondary px-2.5 d-flex align-items-center justify-content-center" title="Reset Filter">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>


    <!-- ==============================================================
         3. ALUR TAHAPAN PENGOLAHAN DOKUMEN BPS (PIPELINE CARD)
         ============================================================== -->
    <div class="bento-card-clean p-3 mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-diagram-3-fill text-primary fs-5"></i>
                <h6 class="mb-0 fw-bold text-dark">Alur Baku 4 Tahap Pengolahan Dokumen Kuesioner BPS</h6>
            </div>
            <span class="badge bg-light text-muted border" style="font-size: 0.72rem;">Standar SOP Pengolahan Data BPS</span>
        </div>
        <div class="row g-2">
            <div class="col-xl-3 col-md-6">
                <div class="pipeline-step-box">
                    <div class="pipeline-step-num bg-info-subtle text-info">1</div>
                    <div class="overflow-hidden">
                        <div class="fw-bold text-dark small lh-1">Penerimaan (Receiving)</div>
                        <div class="text-muted" style="font-size: 0.7rem;">Dokumen fisik/digital kuesioner diserahkan PML &amp; di-batching di BPS.</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="pipeline-step-box">
                    <div class="pipeline-step-num bg-warning-subtle text-warning">2</div>
                    <div class="overflow-hidden">
                        <div class="fw-bold text-dark small lh-1">Editing &amp; Coding (E-C)</div>
                        <div class="text-muted" style="font-size: 0.7rem;">Pemeriksaan konsistensi isian, validasi rumus, &amp; pemberian kode wilayah/KBLI.</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="pipeline-step-box">
                    <div class="pipeline-step-num bg-primary-subtle text-primary">3</div>
                    <div class="overflow-hidden">
                        <div class="fw-bold text-dark small lh-1">Entri Data Komputer</div>
                        <div class="text-muted" style="font-size: 0.7rem;">Penginputan data kuesioner oleh Operator Entri ke aplikasi pengolahan.</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="pipeline-step-box">
                    <div class="pipeline-step-num bg-success text-white">4</div>
                    <div class="overflow-hidden">
                        <div class="fw-bold text-success small lh-1">Validasi &amp; Clean Data</div>
                        <div class="text-muted" style="font-size: 0.7rem;">Pengecekan anomali data (error-free) hingga siap dianalisis dan dirilis.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==============================================================
         4. TIGA TAB UTAMA: DOKUMEN, SEBARAN WILAYAH, DAN KEGIATAN
         ============================================================== -->
    <div class="bento-table-card mb-4">
        <div class="bento-table-header">
            <!-- Nav Tabs -->
            <ul class="nav nav-pills gap-2" id="docProgressTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill px-3 py-1.5 fw-bold" id="tab-documents-link" data-bs-toggle="pill" data-bs-target="#tab-documents" type="button" role="tab">
                        <i class="bi bi-file-earmark-text me-1.5"></i> Daftar Dokumen Kuesioner ({{ $documents->total() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-1.5 fw-bold" id="tab-districts-link" data-bs-toggle="pill" data-bs-target="#tab-districts" type="button" role="tab">
                        <i class="bi bi-geo-alt-fill me-1.5 text-danger"></i> Sebaran 15 Kecamatan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-1.5 fw-bold" id="tab-assignments-link" data-bs-toggle="pill" data-bs-target="#tab-assignments" type="button" role="tab">
                        <i class="bi bi-journal-check me-1.5 text-primary"></i> Progres per Kegiatan Survei ({{ count($assignmentStats) }})
                    </button>
                </li>
            </ul>

            <!-- Tombol Export Excel / CSV -->
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.progress.documents.export', ['assignment_id' => $selectedAssignmentId, 'district' => $selectedDistrict, 'processing_status' => $selectedStatus]) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 0.8rem;">
                    <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i> Unduh Rekap (CSV/Excel)
                </a>
            </div>
        </div>

        <div class="tab-content" id="docProgressTabsContent">

            <!-- ======================================================
                 TAB 1: DAFTAR DOKUMEN KUESIONER & BULK ACTION
                 ====================================================== -->
            <div class="tab-pane fade show active" id="tab-documents" role="tabpanel">

                <!-- Bulk Action Toolbar -->
                <form id="bulkActionForm" method="POST" action="{{ route('admin.progress.documents.bulk-update') }}">
                    @csrf
                    <div class="px-3 py-2 bg-light border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small"><i class="bi bi-check2-square me-1"></i> Aksi Massal Dokumen Terpilih:</span>
                            <select name="processing_status" id="bulkStatusSelect" class="form-select form-select-sm" style="width: auto; min-width: 180px;" required>
                                <option value="">-- Ubah Status Terpilih Ke --</option>
                                <option value="belum_diterima">Belum Diterima</option>
                                <option value="receiving">1. Penerimaan (Receiving)</option>
                                <option value="editing_coding">2. Editing & Coding (E-C)</option>
                                <option value="entry">3. Perekaman Data (Entry)</option>
                                <option value="clean">4. Clean Data (Siap Rilis)</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary px-3 fw-bold rounded-pill" onclick="return confirmBulkAction()">
                                <i class="bi bi-arrow-right-circle me-1"></i> Terapkan
                            </button>
                        </div>
                        <div class="small text-muted">
                            Menampilkan <strong class="text-dark">{{ $documents->firstItem() ?? 0 }} - {{ $documents->lastItem() ?? 0 }}</strong> dari <strong class="text-dark">{{ $documents->total() }}</strong> dokumen
                        </div>
                    </div>

                    <div class="bento-table-container-fixed">
                        <table class="table bento-table-fixed align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 3.5%;" class="text-center">
                                        <input type="checkbox" class="form-check-input" id="checkAllDocs" title="Pilih Semua Dokumen di Halaman Ini">
                                    </th>
                                    <th style="width: 4%;" class="text-center">No</th>
                                    <th style="width: 20%;">Dokumen / Responden</th>
                                    <th style="width: 17%;">Kegiatan Sensus/Survei</th>
                                    <th style="width: 15%;">Petugas Lapangan</th>
                                    <th style="width: 14%;">Wilayah</th>
                                    <th style="width: 9%;" class="text-center">Status Lapangan</th>
                                    <th style="width: 11%;" class="text-center">Tahap Pengolahan</th>
                                    <th style="width: 6.5%;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documents as $index => $doc)
                                    <tr>
                                        <!-- Checkbox -->
                                        <td class="text-center">
                                            <input type="checkbox" name="respondent_ids[]" value="{{ $doc->id }}" class="form-check-input doc-checkbox">
                                        </td>

                                        <!-- Nomor Urut -->
                                        <td class="text-center text-muted fw-bold">
                                            {{ $documents->firstItem() + $index }}
                                        </td>

                                        <!-- Responden & Kontak -->
                                        <td>
                                            <div class="fw-bold text-dark text-truncate" title="{{ $doc->name }}">
                                                {{ $doc->name }}
                                            </div>
                                            <div class="d-flex align-items-center gap-1.5 flex-wrap mt-0.5 text-muted" style="font-size: 0.7rem;">
                                                @if($doc->family_head_name)
                                                    <span class="badge bg-light text-muted border text-truncate" style="max-width: 110px; font-size: 0.65rem;" title="Kepala Keluarga: {{ $doc->family_head_name }}">
                                                        <i class="bi bi-house-door me-0.5"></i> {{ Str::limit($doc->family_head_name, 14) }}
                                                    </span>
                                                @endif
                                                @if($doc->phone)
                                                    <span class="text-nowrap" style="font-size: 0.68rem;" title="Telepon">
                                                        <i class="bi bi-telephone text-secondary"></i> {{ $doc->phone }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Kegiatan Survei -->
                                        <td>
                                            <div class="text-truncate fw-semibold text-primary" title="{{ $doc->assignment?->title ?? '-' }}">
                                                {{ $doc->assignment?->title ?? '-' }}
                                            </div>
                                            <div class="text-muted text-truncate" style="font-size: 0.68rem;">
                                                Kode: {{ $doc->assignment?->survey_code ?? 'BPS-KOLUT' }}
                                            </div>
                                        </td>

                                        <!-- Petugas Pencacah & PML -->
                                        <td>
                                            @if($doc->officer)
                                                <div class="fw-semibold text-dark text-truncate" title="{{ $doc->officer->name }}">
                                                    {{ $doc->officer->name }}
                                                </div>
                                                <span class="badge bg-light text-secondary border px-1.5 py-0.2" style="font-size: 0.64rem;">
                                                    {{ $doc->officer->roleLabel() }}
                                                </span>
                                            @else
                                                <span class="text-muted fst-italic" style="font-size: 0.72rem;">Belum Ditugaskan</span>
                                            @endif
                                        </td>

                                        <!-- Wilayah -->
                                        <td>
                                            <div class="fw-semibold text-dark text-truncate" title="{{ $doc->district }}">{{ $doc->district }}</div>
                                            <div class="text-muted text-truncate" style="font-size: 0.68rem;" title="{{ $doc->village ?? $doc->address }}">
                                                {{ $doc->village ?? $doc->address }}
                                            </div>
                                        </td>

                                        <!-- Status Lapangan & BAST -->
                                        <td class="text-center">
                                            @if($doc->progress_status === 'selesai' || $doc->basts->count() > 0)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 fw-semibold text-nowrap" style="font-size: 0.68rem;">
                                                    <i class="bi bi-check2-circle me-0.5"></i> Selesai
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-0.5 fw-normal text-nowrap" style="font-size: 0.68rem;">
                                                    Belum Didata
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Tahapan Pengolahan Dokumen -->
                                        <td class="text-center">
                                            @php
                                                $badgeClass = match($doc->processing_status) {
                                                    'clean' => 'bg-success-subtle text-success border border-success-subtle',
                                                    'entry' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                                    'editing_coding' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                                    'receiving' => 'bg-info-subtle text-info border border-info-subtle',
                                                    default => 'bg-light text-secondary border',
                                                };
                                                $statusIcon = match($doc->processing_status) {
                                                    'clean' => 'bi-check2-all',
                                                    'entry' => 'bi-keyboard-fill',
                                                    'editing_coding' => 'bi-pencil-fill',
                                                    'receiving' => 'bi-inbox-fill',
                                                    default => 'bi-clock',
                                                };
                                                $statusShortLabel = match($doc->processing_status) {
                                                    'clean' => 'Clean Data',
                                                    'entry' => 'Data Entry',
                                                    'editing_coding' => 'Editing & Coding',
                                                    'receiving' => 'Receiving',
                                                    default => 'Belum Diterima',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }} rounded-pill px-2 py-1 fw-semibold text-nowrap" style="font-size: 0.68rem;">
                                                <i class="bi {{ $statusIcon }} me-1"></i> {{ $statusShortLabel }}
                                            </span>
                                        </td>

                                        <!-- Tombol Aksi Update Cepat -->
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0.5 d-inline-flex align-items-center gap-1 text-nowrap shadow-none" style="font-size: 0.72rem; font-weight: 600;" 
                                                    onclick="openStatusModal({{ $doc->id }}, '{{ addslashes($doc->name) }}', '{{ $doc->processing_status ?? 'belum_diterima' }}')"
                                                    title="Ubah Tahapan Pengolahan">
                                                <i class="bi bi-pencil-square"></i>
                                                <span>Ubah</span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                            <div class="fw-bold">Tidak ada data dokumen kuesioner yang sesuai filter.</div>
                                            <small class="text-muted">Coba ubah filter pencarian atau pilih kegiatan lain.</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>

                <!-- Pagination Footer -->
                @if($documents->hasPages())
                    <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <small class="text-muted">
                            Halaman {{ $documents->currentPage() }} dari {{ $documents->lastPage() }}
                        </small>
                        <div>
                            {{ $documents->links() }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- ======================================================
                 TAB 2: SEBARAN PENGOLAHAN 15 KECAMATAN KOLAKA UTARA
                 ====================================================== -->
            <div class="tab-pane fade" id="tab-districts" role="tabpanel">
                <div class="bento-table-container-fixed">
                    <table class="table bento-table-fixed align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 4%;" class="text-center">No</th>
                                <th style="width: 18%;">Wilayah Kecamatan</th>
                                <th style="width: 10%;" class="text-center">Target Dokumen</th>
                                <th style="width: 17%;" class="text-center">1. Receiving</th>
                                <th style="width: 17%;" class="text-center">2. Editing/Coding</th>
                                <th style="width: 17%;" class="text-center">3. Entri Komputer</th>
                                <th style="width: 17%;" class="text-center">4. Clean Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $distNo = 1; @endphp
                            @foreach($districtStats as $dStat)
                                <tr>
                                    <td class="text-center text-muted fw-bold">{{ $distNo++ }}</td>
                                    <td>
                                        <div class="fw-bold text-dark text-truncate">{{ $dStat['district'] }}</div>
                                        <small class="text-muted" style="font-size: 0.68rem;">Kab. Kolaka Utara</small>
                                    </td>
                                    <td class="text-center fw-bold">{{ number_format($dStat['total']) }}</td>
                                    <td class="text-center">
                                        <div class="small fw-semibold text-info">{{ $dStat['received'] }} / {{ $dStat['total'] }}</div>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-0.2" style="font-size: 0.65rem;">{{ $dStat['received_pct'] }}%</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="small fw-semibold text-warning">{{ $dStat['edited'] }} / {{ $dStat['total'] }}</div>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.2" style="font-size: 0.65rem;">{{ $dStat['edited_pct'] }}%</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="small fw-semibold text-primary">{{ $dStat['entered'] }} / {{ $dStat['total'] }}</div>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.2" style="font-size: 0.65rem;">{{ $dStat['entered_pct'] }}%</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="small fw-semibold text-success">{{ $dStat['clean'] }} / {{ $dStat['total'] }}</div>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.2" style="font-size: 0.65rem;">{{ $dStat['clean_pct'] }}%</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ======================================================
                 TAB 3: PROGRES PER KEGIATAN SENSUS & SURVEI
                 ====================================================== -->
            <div class="tab-pane fade" id="tab-assignments" role="tabpanel">
                <div class="bento-table-container-fixed">
                    <table class="table bento-table-fixed align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 4%;" class="text-center">No</th>
                                <th style="width: 24%;">Kegiatan Sensus / Survei</th>
                                <th style="width: 10%;" class="text-center">Target Dokumen</th>
                                <th style="width: 15.5%;" class="text-center">1. Receiving</th>
                                <th style="width: 15.5%;" class="text-center">2. Editing/Coding</th>
                                <th style="width: 15.5%;" class="text-center">3. Entri Komputer</th>
                                <th style="width: 15.5%;" class="text-center">4. Clean Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignmentStats as $aIndex => $aStat)
                                <tr>
                                    <td class="text-center text-muted fw-bold">{{ $aIndex + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark text-truncate" title="{{ $aStat['title'] }}">{{ $aStat['title'] }}</div>
                                        <small class="text-muted" style="font-size: 0.68rem;">ID: #{{ $aStat['id'] }}</small>
                                    </td>
                                    <td class="text-center fw-bold">{{ number_format($aStat['total']) }}</td>
                                    <td class="text-center">
                                        <div class="small fw-semibold text-info">{{ $aStat['received'] }} / {{ $aStat['total'] }}</div>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-0.2" style="font-size: 0.65rem;">{{ $aStat['received_pct'] }}%</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="small fw-semibold text-warning">{{ $aStat['edited'] }} / {{ $aStat['total'] }}</div>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.2" style="font-size: 0.65rem;">{{ $aStat['edited_pct'] }}%</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="small fw-semibold text-primary">{{ $aStat['entered'] }} / {{ $aStat['total'] }}</div>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.2" style="font-size: 0.65rem;">{{ $aStat['entered_pct'] }}%</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="small fw-semibold text-success">{{ $aStat['clean'] }} / {{ $aStat['total'] }}</div>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.2" style="font-size: 0.65rem;">{{ $aStat['clean_pct'] }}%</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Belum ada kegiatan yang terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- ==============================================================
     MODAL: UPDATE TAHAP PENGOLAHAN DOKUMEN TUNGGAL
     ============================================================== -->
<div class="modal fade" id="modalUpdateStatus" tabindex="-1" aria-labelledby="modalUpdateStatusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formUpdateStatus" method="POST" action="" class="modal-content border-0 shadow rounded-4">
            @csrf
            @method('PUT')
            <div class="modal-header border-bottom px-4 py-3 bg-light rounded-top-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="bento-stat-icon bg-primary text-white" style="width: 36px; height: 36px; font-size: 1rem;">
                        <i class="bi bi-clipboard2-check"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="modalUpdateStatusLabel">Update Tahap Dokumen</h6>
                        <small class="text-muted" id="modalRespondentName">Responden</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">Pilih Tahapan Pengolahan Saat Ini:</label>
                    <div class="list-group gap-2">
                        <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 rounded-3 border p-2.5 cursor-pointer">
                            <input class="form-check-input flex-shrink-0" type="radio" name="processing_status" value="belum_diterima" id="status_belum_diterima">
                            <div>
                                <div class="fw-bold small text-dark">Belum Diterima</div>
                                <div class="text-muted" style="font-size: 0.72rem;">Dokumen kuesioner masih dipegang petugas di lapangan.</div>
                            </div>
                        </label>
                        <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 rounded-3 border p-2.5 cursor-pointer">
                            <input class="form-check-input flex-shrink-0" type="radio" name="processing_status" value="receiving" id="status_receiving">
                            <div>
                                <div class="fw-bold small text-info"><i class="bi bi-inbox-fill me-1"></i> 1. Penerimaan (Receiving)</div>
                                <div class="text-muted" style="font-size: 0.72rem;">Dokumen fisik/digital telah diserahkan dan di-batching di BPS.</div>
                            </div>
                        </label>
                        <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 rounded-3 border p-2.5 cursor-pointer">
                            <input class="form-check-input flex-shrink-0" type="radio" name="processing_status" value="editing_coding" id="status_editing_coding">
                            <div>
                                <div class="fw-bold small text-warning"><i class="bi bi-pencil-square me-1"></i> 2. Editing &amp; Coding (E-C)</div>
                                <div class="text-muted" style="font-size: 0.72rem;">Pemeriksaan isian kuesioner dan penyandian kode statistik.</div>
                            </div>
                        </label>
                        <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 rounded-3 border p-2.5 cursor-pointer">
                            <input class="form-check-input flex-shrink-0" type="radio" name="processing_status" value="entry" id="status_entry">
                            <div>
                                <div class="fw-bold small text-primary"><i class="bi bi-keyboard-fill me-1"></i> 3. Perekaman Data (Entry)</div>
                                <div class="text-muted" style="font-size: 0.72rem;">Penginputan data kuesioner ke aplikasi pengolahan komputer BPS.</div>
                            </div>
                        </label>
                        <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 rounded-3 border p-2.5 cursor-pointer">
                            <input class="form-check-input flex-shrink-0" type="radio" name="processing_status" value="clean" id="status_clean">
                            <div>
                                <div class="fw-bold small text-success"><i class="bi bi-check2-all me-1"></i> 4. Validasi &amp; Clean Data</div>
                                <div class="text-muted" style="font-size: 0.72rem;">Lolos validasi bersih (error-free) dan siap rilis/tabulasi.</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-2.5 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Master Checkbox Toggle
    const checkAll = document.getElementById('checkAllDocs');
    const docCheckboxes = document.querySelectorAll('.doc-checkbox');

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            docCheckboxes.forEach(cb => cb.checked = checkAll.checked);
        });
    }

    // Modal Status Handler
    function openStatusModal(respondentId, respondentName, currentStatus) {
        const form = document.getElementById('formUpdateStatus');
        form.action = "{{ url('admin/document-progress') }}/" + respondentId + "/status";

        document.getElementById('modalRespondentName').textContent = 'Kuesioner: ' + respondentName;

        const radio = document.getElementById('status_' + currentStatus);
        if (radio) {
            radio.checked = true;
        } else {
            const defaultRadio = document.getElementById('status_belum_diterima');
            if (defaultRadio) defaultRadio.checked = true;
        }

        const modal = new bootstrap.Modal(document.getElementById('modalUpdateStatus'));
        modal.show();
    }

    // Bulk Action Confirmation
    function confirmBulkAction() {
        const selected = Array.from(docCheckboxes).filter(cb => cb.checked);
        const statusSelect = document.getElementById('bulkStatusSelect');

        if (selected.length === 0) {
            alert('Silakan pilih minimal 1 dokumen kuesioner pada tabel terlebih dahulu!');
            return false;
        }

        if (!statusSelect.value) {
            alert('Silakan pilih status tahapan yang ingin diterapkan!');
            statusSelect.focus();
            return false;
        }

        return confirm('Yakin ingin mengubah status tahapan untuk ' + selected.length + ' dokumen kuesioner sekaligus?');
    }
</script>
@endpush

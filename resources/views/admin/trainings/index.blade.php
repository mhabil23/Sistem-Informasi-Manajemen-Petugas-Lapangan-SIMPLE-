@extends('layouts.app')

@section('title', 'Daftar Hadir Kegiatan & Pelatihan')
@section('page_title', 'Daftar Hadir')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Daftar Hadir</li>
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
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .bento-table-fixed {
        table-layout: fixed;
        width: 100%;
        min-width: 960px;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.8rem;
    }
    .bento-table-fixed thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.75rem 0.55rem;
        border-bottom: 1px solid #e2e8f0;
        border-top: none;
        white-space: nowrap;
        vertical-align: middle;
    }
    .bento-table-fixed tbody td {
        padding: 0.75rem 0.55rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.8rem;
        color: #334155;
        background: #ffffff;
        transition: background 0.2s ease;
    }
    .bento-table-fixed tbody tr:hover td {
        background-color: #f8fafc;
    }
    .bento-table-fixed tbody tr:last-child td {
        border-bottom: none;
    }
    .btn-bps-orange {
        background-color: #F37021;
        border-color: #F37021;
        color: #ffffff;
        font-weight: 600;
        border-radius: 9999px;
    }
    .btn-bps-orange:hover, .btn-bps-orange:focus {
        background-color: #d95d13;
        border-color: #d95d13;
        color: #ffffff;
    }
    /* Compact Action Buttons */
    .bento-action-btn {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #E2E8F0;
        background: #FFFFFF;
        transition: all 0.15s ease;
        font-size: 0.8rem;
        padding: 0;
        flex-shrink: 0;
    }
    .bento-action-btn.btn-view { color: #0284C7; border-color: #BAE6FD; background: #F0F9FF; }
    .bento-action-btn.btn-view:hover { background: #0284C7; color: #FFFFFF; }
    .bento-action-btn.btn-qr { color: #4F46E5; border-color: #C7D2FE; background: #EEF2FF; }
    .bento-action-btn.btn-qr:hover { background: #4F46E5; color: #FFFFFF; }
    .bento-action-btn.btn-download { color: #0D9488; border-color: #99F6E4; background: #F0FDFA; }
    .bento-action-btn.btn-download:hover { background: #0D9488; color: #FFFFFF; }
    .bento-action-btn.btn-edit { color: #D97706; border-color: #FDE68A; background: #FFFBEB; }
    .bento-action-btn.btn-edit:hover { background: #D97706; color: #FFFFFF; }
    .bento-action-btn.btn-delete { color: #DC2626; border-color: #FECACA; background: #FEF2F2; }
    .bento-action-btn.btn-delete:hover { background: #DC2626; color: #FFFFFF; }

    /* Canvas Tanda Tangan */
    .signature-pad-container {
        border: 1.5px dashed #CBD5E1;
        border-radius: 12px;
        background: #F8FAFC;
        position: relative;
        touch-action: none;
    }
    .signature-pad-canvas {
        width: 100%;
        height: 120px;
        display: block;
        border-radius: 10px;
        cursor: crosshair;
    }
</style>
@endpush

@section('content')
<!-- Search & Filter Card -->
<div class="bento-card mb-4">
    <div class="p-3">
        <form method="GET" action="{{ route('admin.trainings.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="form-control border-start-0 ps-0" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nomor dokumen, judul kegiatan, peserta, lokasi...">
                </div>
            </div>

            <div class="col-md-4">
                <select name="assignment_id" class="form-select">
                    <option value="">-- Pilih Kegiatan Terkait --</option>
                    @foreach($assignments as $assign)
                        <option value="{{ $assign->id }}" {{ request('assignment_id') == $assign->id ? 'selected' : '' }}>
                            {{ \Illuminate\Support\Str::limit($assign->title, 45) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-bps-orange flex-fill">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'assignment_id']))
                    <a href="{{ route('admin.trainings.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Main Table Bento Card -->
<div class="bento-card mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center p-3 border-bottom gap-2">
        <div>
            <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">Daftar Hadir Kegiatan &amp; Pelatihan</h5>
            <span class="text-muted" style="font-size: 0.72rem;">Total {{ $trainings->total() }} lembar daftar hadir terdaftar dalam sistem</span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="badge bg-light text-secondary border px-2.5 py-1.5 rounded-pill" style="font-size: 0.7rem;">
                Halaman {{ $trainings->currentPage() }} dari {{ max(1, $trainings->lastPage()) }}
            </span>
            <button type="button" class="btn btn-outline-success shadow-sm py-1.5 px-3 rounded-pill" style="font-size: 0.8rem; font-weight: 600;" onclick="openCreateFromExcelModal()" title="Upload berkas Excel peserta untuk langsung dibuatkan Daftar Hadir">
                <i class="bi bi-file-earmark-excel-fill text-success me-1"></i> Upload Excel Peserta
            </button>
            <button type="button" class="btn btn-bps-orange shadow-sm py-1.5 px-3" style="font-size: 0.8rem;" onclick="openCreateAttendanceModal()">
                <i class="bi bi-file-earmark-plus-fill me-1"></i> Buat Daftar Hadir Baru
            </button>
        </div>
    </div>

    <div class="bento-table-container-fixed">
        <table class="table bento-table-fixed align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 17%; min-width: 150px;"><i class="bi bi-file-earmark-text me-1 text-primary"></i> Nomor &amp; Tanggal Dokumen</th>
                    <th style="width: 24%; min-width: 190px;"><i class="bi bi-briefcase me-1 text-muted"></i> Judul &amp; Kegiatan Terkait</th>
                    <th style="width: 16%; min-width: 130px;"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Tempat Pelaksanaan</th>
                    <th style="width: 14%; min-width: 120px;"><i class="bi bi-people-fill me-1 text-primary"></i> Peserta Hadir</th>
                    <th style="width: 12%; min-width: 110px;"><i class="bi bi-person-badge me-1 text-info"></i> Penanggung Jawab</th>
                    <th class="text-end pe-3" style="width: 17%; min-width: 175px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trainings as $tr)
                    @php
                        $docNum = $tr->document_number ?: ('DH/' . $tr->start_date->format('Y') . '/' . str_pad($tr->id, 4, '0', STR_PAD_LEFT));
                        $participantsCount = $tr->participants->count();
                        $firstParticipant = $tr->participants->first();
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-bold text-dark font-monospace text-truncate" style="font-size: 0.8rem;" title="{{ $docNum }}">
                                {{ $docNum }}
                            </div>
                            <div class="text-muted text-truncate d-flex align-items-center gap-1 mt-0.5" style="font-size: 0.72rem;">
                                <i class="bi bi-calendar-event text-secondary flex-shrink-0"></i>
                                <span>{{ $tr->start_date->translatedFormat('d F Y') }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark text-truncate mb-0.5" style="font-size: 0.82rem;" title="{{ $tr->activity_subtitle ?: $tr->title }}">
                                {{ $tr->activity_subtitle ?: $tr->title }}
                            </div>
                            @if($tr->assignment)
                                <div class="small text-muted text-truncate" style="font-size: 0.7rem;" title="{{ $tr->assignment->title }}">
                                    <i class="bi bi-folder2-open me-1"></i> {{ $tr->assignment->title }}
                                </div>
                            @elseif($tr->document_title)
                                <div class="small text-secondary text-truncate" style="font-size: 0.7rem;">
                                    {{ $tr->document_title }}
                                </div>
                            @endif
                            <div class="mt-1 d-flex align-items-center gap-1.5 flex-wrap">
                                <button type="button" 
                                        class="btn btn-xs btn-primary rounded-pill py-0.5 px-2 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs" 
                                        style="font-size: 0.68rem;" 
                                        onclick="openShareLinkModal('{{ $tr->public_attendance_url }}', '{{ addslashes($tr->activity_subtitle ?: $tr->title) }}')">
                                    <i class="bi bi-qr-code"></i> Link &amp; QR Presensi
                                </button>
                                <a href="{{ route('admin.trainings.show', $tr) }}" 
                                   class="btn btn-xs btn-light text-secondary border rounded-pill py-0.5 px-2 fw-semibold d-inline-flex align-items-center gap-1" 
                                   style="font-size: 0.68rem;">
                                    <i class="bi bi-people"></i> Detail Peserta
                                </a>
                            </div>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark text-truncate" style="font-size: 0.78rem;" title="{{ $tr->document_place ?: $tr->location }}">
                                <i class="bi bi-geo-alt text-danger me-1"></i> {{ $tr->document_place ?: $tr->location }}
                            </div>
                            @if($tr->start_date && $tr->end_date && $tr->start_date != $tr->end_date)
                                <div class="small text-muted mt-0.5" style="font-size: 0.68rem;">
                                    Sampai {{ $tr->end_date->translatedFormat('d F Y') }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($participantsCount > 1)
                                <div class="fw-bold text-dark text-truncate mb-0.5" style="font-size: 0.8rem;" title="{{ $firstParticipant->name }}">
                                    {{ $firstParticipant->name }}
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill ms-0.5" style="font-size: 0.65rem;">+{{ $participantsCount - 1 }}</span>
                                </div>
                                <div class="small text-muted text-truncate" style="font-size: 0.7rem;">
                                    <i class="bi bi-people me-1"></i> Kolektif ({{ $participantsCount }} peserta)
                                </div>
                            @elseif($participantsCount === 1)
                                <div class="fw-bold text-dark text-truncate mb-0.5" style="font-size: 0.8rem;" title="{{ $firstParticipant->name }}">
                                    {{ $firstParticipant->name }}
                                </div>
                                <div class="small text-muted text-truncate" style="font-size: 0.7rem;">
                                    <i class="bi bi-person me-1"></i> {{ $firstParticipant->category ?: 'Peserta' }}
                                </div>
                            @else
                                <div class="small text-muted fst-italic">Belum ada peserta</div>
                            @endif
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark text-truncate" title="{{ $tr->maker_name ?: ($tr->creator ? $tr->creator->name : '-') }}">
                                {{ $tr->maker_name ?: ($tr->creator ? $tr->creator->name : '-') }}
                            </div>
                            @if($tr->maker_nip)
                                <div class="text-muted text-truncate font-monospace" style="font-size: 0.68rem;">
                                    NIP: {{ $tr->maker_nip }}
                                </div>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-flex align-items-center justify-content-end gap-1 flex-nowrap">
                                <!-- Link & QR Presensi Online -->
                                <button type="button" 
                                        class="bento-action-btn btn-qr" 
                                        title="Link &amp; QR Presensi Online" 
                                        data-bs-toggle="tooltip"
                                        onclick="openShareLinkModal('{{ $tr->public_attendance_url }}', '{{ addslashes($tr->activity_subtitle ?: $tr->title) }}')">
                                    <i class="bi bi-qr-code"></i>
                                </button>

                                <!-- Detail Peserta & TTD -->
                                <a href="{{ route('admin.trainings.show', $tr) }}" 
                                   class="bento-action-btn btn-view" 
                                   title="Detail Peserta &amp; Tanda Tangan" 
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-people"></i>
                                </a>

                                <!-- Unduh PDF -->
                                <a href="{{ route('admin.trainings.attendance.pdf', $tr) }}" 
                                   class="bento-action-btn btn-download" 
                                   title="Unduh Berkas PDF" 
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-download"></i>
                                </a>

                                <!-- Preview / Cetak PDF -->
                                <a href="{{ route('admin.trainings.preview', $tr) }}" 
                                   target="_blank" 
                                   class="bento-action-btn btn-view" 
                                   title="Lihat / Cetak Dokumen" 
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-printer"></i>
                                </a>

                                <!-- Edit Daftar Hadir (Blanko) -->
                                <button type="button" 
                                        class="bento-action-btn btn-edit" 
                                        title="Edit Lembar Daftar Hadir" 
                                        data-bs-toggle="tooltip"
                                        onclick="openEditAttendanceModal({{ json_encode($tr->load('participants')) }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- Hapus Dokumen -->
                                <form method="POST" 
                                      action="{{ route('admin.trainings.destroy', $tr) }}" 
                                      class="d-inline m-0 p-0"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus lembar Daftar Hadir nomor {{ addslashes($docNum) }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bento-action-btn btn-delete" 
                                            title="Hapus Dokumen" 
                                            data-bs-toggle="tooltip">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-clipboard2-x display-5 text-secondary opacity-50 d-block mb-2"></i>
                            <div class="fw-bold text-dark">Belum ada dokumen Daftar Hadir yang dibuat.</div>
                            <div class="small">Klik tombol "Buat Daftar Hadir Baru" atau "Upload Excel Peserta" untuk membuat lembar presensi resmi BPS.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ==============================================================
     MODAL UTAMA: BUAT / EDIT LEMBAR DAFTAR HADIR (BLANKO)
     ============================================================== -->
<div class="modal fade" id="attendanceSheetModal" tabindex="-1" aria-labelledby="attendanceSheetModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <form id="attendanceSheetForm" method="POST" action="{{ route('admin.trainings.store') }}" class="modal-content border-0 shadow-lg" style="border-radius: 20px; max-height: calc(100vh - 3.5rem); display: flex; flex-direction: column;">
            @csrf
            <div id="attendanceMethodSpoof"></div>

            <div class="modal-header bg-light border-bottom px-4 py-3 flex-shrink-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: #F37021; width: 34px; height: 34px;">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="attendanceSheetModalLabel" style="font-size: 1.05rem;">
                            Terbitkan Lembar Daftar Hadir Baru
                        </h5>
                        <small class="text-muted" style="font-size: 0.72rem;">Format resmi Badan Pusat Statistik dengan tanda tangan selang-seling (1. ... / 2. ...)</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4" style="overflow-y: auto; flex: 1 1 auto;">
                    <!-- HEADER CETAK (4 BARIS RESMI) -->
                    <div class="card border bg-light-subtle mb-3 rounded-3">
                        <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center">
                            <span class="fw-bold small text-primary">
                                <i class="bi bi-type-h1 me-1"></i> HEADER CETAK DAFTAR HADIR (4 BARIS ATAS DOKUMEN)
                            </span>
                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill">Standar Format A4 BPS</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1">BARIS 1: JUDUL DOKUMEN</label>
                                    <input type="text" name="document_title" id="attDocTitle" class="form-control form-control-sm" value="DAFTAR HADIR PESERTA" placeholder="Contoh: DAFTAR HADIR PESERTA / PETUGAS">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1">BARIS 2: SUBJUDUL KEGIATAN / PELATIHAN <span class="text-danger">*</span></label>
                                    <input type="text" name="activity_subtitle" id="attActivitySubtitle" class="form-control form-control-sm" placeholder="Contoh: Briefing Lapangan Petugas Survei Pola Konsumsi (SPKI)" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1">BARIS 3: INSTANSI &amp; TAHUN</label>
                                    <input type="text" name="institution_name" id="attInstitution" class="form-control form-control-sm" value="BPS KABUPATEN KOLAKA UTARA TAHUN {{ now()->format('Y') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1">BARIS 4: TEMPAT PELAKSANAAN</label>
                                    <input type="text" name="document_place" id="attPlace" class="form-control form-control-sm" value="Kantor BPS Kabupaten Kolaka Utara" placeholder="Contoh: Hotel Utama / Kantor BPS Kolaka Utara">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-secondary mb-1">NOMOR DOKUMEN</label>
                                    <input type="text" name="document_number" id="attDocNumber" class="form-control form-control-sm font-monospace" placeholder="Otomatis jika dikosongkan (contoh: DH-BPS/{{ now()->year }}/...)">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-secondary mb-1">TANGGAL PELAKSANAAN <span class="text-danger">*</span></label>
                                    <input type="date" name="attendance_date" id="attDate" class="form-control form-control-sm" value="{{ now()->toDateString() }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-secondary mb-1">KEGIATAN SURVEI TERKAIT (OPSIONAL)</label>
                                    <select name="assignment_id" id="attAssignmentSelect" class="form-select form-select-sm" onchange="onAssignmentSelected(this.value)">
                                        <option value="">-- Tanpa Kaitan Kegiatan Tertentu --</option>
                                        @foreach($assignments as $assign)
                                            <option value="{{ $assign->id }}" data-title="{{ $assign->title }}" data-location="{{ $assign->location }}">
                                                {{ $assign->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TABEL DAFTAR PESERTA HADIR (KOLEKTIF) -->
                    <div class="card border rounded-3 mb-3">
                        <div class="card-header bg-white py-2.5 px-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <span class="fw-bold small text-dark">
                                        <i class="bi bi-people-fill text-primary me-1"></i> DAFTAR PESERTA / PETUGAS YANG HADIR
                                    </span>
                                    <span class="badge bg-light text-secondary border rounded-pill ms-1" id="attParticipantCountBadge">0 Peserta</span>
                                </div>
                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                    <!-- Dropdown Pilih Petugas -->
                                    <select id="attOfficerDropdown" class="form-select form-select-sm" style="width: 190px;">
                                        <option value="">-- Pilih Petugas --</option>
                                        @foreach($officers as $off)
                                            <option value="{{ $off->id }}" 
                                                    data-name="{{ $off->name }}" 
                                                    data-position="{{ $off->position ?: 'Petugas Pendataan Lapangan' }}" 
                                                    data-origin="{{ $off->district ?: ($off->village ?: 'Kabupaten Kolaka Utara') }}">
                                                {{ $off->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-2.5" onclick="addParticipantFromDropdown()">
                                        <i class="bi bi-plus-lg me-0.5"></i> Tambah
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-2.5" onclick="openMultiSelectOfficersModal()">
                                        <i class="bi bi-check2-all me-0.5"></i> Pilih Banyak
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm rounded-pill px-2.5" onclick="importFromSelectedAssignment()">
                                        <i class="bi bi-box-arrow-in-down me-0.5"></i> Dari Kegiatan
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-2.5" onclick="addManualParticipantRow()">
                                        <i class="bi bi-pencil me-0.5"></i> Manual
                                    </button>
                                    <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-2.5" onclick="openUploadExcelModalForCurrentForm()">
                                        <i class="bi bi-file-earmark-excel me-0.5"></i> Upload Excel
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive" style="max-height: 380px;">
                            <table class="table table-bordered table-hover align-middle mb-0" id="attendanceParticipantsTable" style="font-size: 0.82rem;">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="width: 5%;" class="text-center">No.</th>
                                        <th style="width: 32%;">Nama Lengkap Peserta</th>
                                        <th style="width: 28%;">Jabatan &amp; Unit Kerja</th>
                                        <th style="width: 25%;">Asal / Bertugas Sebagai / Instansi</th>
                                        <th style="width: 10%;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="attendanceParticipantsTbody">
                                    <!-- Dynamic rows inserted by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-light py-2 px-3">
                            <small class="text-muted fst-italic">
                                <i class="bi bi-info-circle me-1 text-primary"></i> Kolom Tanda Tangan pada hasil cetak PDF otomatis memakai format garis titik-titik selang-seling kiri/kanan (1. .......... / 2. ..........).
                            </small>
                        </div>
                    </div>

                    <!-- FOOTER: CATATAN (KIRI) & PENANGGUNG JAWAB (KANAN) -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card border rounded-3 h-100 p-3">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    <i class="bi bi-card-text me-1 text-primary"></i> CATATAN / AGENDA PELAKSANAAN (KIRI BAWAH)
                                </label>
                                <textarea name="notes" id="attNotes" class="form-control" rows="4" placeholder="Tuliskan catatan pelaksanaan pertemuan, materi yang dibahas, atau informasi penting lainnya..."></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border rounded-3 p-3">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    <i class="bi bi-pen me-1 text-primary"></i> PEMBUAT DAFTAR / PENANGGUNG JAWAB (KANAN BAWAH)
                                </label>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-0">NAMA PEMBUAT DAFTAR</label>
                                        <input type="text" name="maker_name" id="attMakerName" class="form-control form-control-sm" value="{{ auth()->user()->name }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-0">NIP PEMBUAT DAFTAR (OPSIONAL)</label>
                                        <input type="text" name="maker_nip" id="attMakerNip" class="form-control form-control-sm font-monospace" value="{{ auth()->user()->nik ?? '' }}" placeholder="Contoh: 199501012020121001">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label small text-muted mb-0">TANDA TANGAN DIGITAL (OPSIONAL)</label>
                                    <button type="button" class="btn btn-link btn-sm text-danger text-decoration-none p-0" style="font-size: 0.72rem;" onclick="clearSignaturePad()">
                                        <i class="bi bi-arrow-counterclockwise"></i> Ulangi TTD
                                    </button>
                                </div>
                                <div class="signature-pad-container">
                                    <canvas id="attSignatureCanvas" class="signature-pad-canvas"></canvas>
                                </div>
                                <input type="hidden" name="maker_signature_data" id="attMakerSignatureData">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top px-4 py-3 d-flex justify-content-between flex-shrink-0">
                    <button type="button" class="btn btn-light rounded-pill px-3.5" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-bps-orange px-4 py-2 shadow-sm" id="btnSubmitAttendance">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Simpan &amp; Buat Dokumen PDF
                    </button>
                </div>
        </form>
    </div>
</div>

<!-- ==============================================================
     MODAL UPLOAD EXCEL PESERTA
     ============================================================== -->
<div class="modal fade" id="uploadAttendanceExcelModal" tabindex="-1" aria-labelledby="uploadAttendanceExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: #10B981; width: 32px; height: 32px;">
                        <i class="bi bi-file-earmark-excel-fill"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="uploadAttendanceExcelModalLabel" style="font-size: 1rem;">
                        Upload Berkas Excel Daftar Peserta
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info border-0 rounded-3 p-3 mb-3" style="background: #eff6ff; color: #1e40af; font-size: 0.82rem;">
                    <div class="fw-bold mb-1"><i class="bi bi-download me-1"></i> Unduh Format Berkas Resmi:</div>
                    <div>Gunakan template berikut agar pembacaan nama, jabatan, dan asal instansi berjalan otomatis tanpa kendala:</div>
                    <div class="d-flex gap-2 mt-2">
                        <a href="{{ route('admin.trainings.template.download', ['format' => 'xlsx']) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-file-earmark-excel me-1"></i> Unduh Template (.XLSX)
                        </a>
                        <a href="{{ route('admin.trainings.template.download', ['format' => 'csv']) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="bi bi-filetype-csv me-1"></i> Unduh Template (.CSV)
                        </a>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">PILIH BERKAS EXCEL / CSV PESERTA <span class="text-danger">*</span></label>
                    <input type="file" id="excelParticipantsFileInput" class="form-control" accept=".xlsx,.xls,.csv" onchange="previewExcelParticipantsFile()">
                    <div class="form-text" style="font-size: 0.72rem;">Mendukung format .xlsx, .xls, dan .csv maksimal 10 MB.</div>
                </div>

                <!-- Preview Area -->
                <div id="excelPreviewLoading" class="text-center py-3 text-muted" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary me-1" role="status"></div> Membaca berkas Excel...
                </div>

                <div id="excelPreviewContainer" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-bold text-success" id="excelPreviewCountText">0 Peserta Terbaca</span>
                        <span class="text-muted" style="font-size: 0.72rem;">Pratinjau 5 baris pertama</span>
                    </div>
                    <div class="table-responsive border rounded-3 mb-3" style="max-height: 200px;">
                        <table class="table table-sm table-striped mb-0" style="font-size: 0.78rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Peserta</th>
                                    <th>Jabatan / Unit Kerja</th>
                                    <th>Asal / Instansi</th>
                                </tr>
                            </thead>
                            <tbody id="excelPreviewTbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top px-4 py-3">
                <button type="button" class="btn btn-light rounded-pill px-3.5" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success rounded-pill px-4" id="btnApplyExcelParticipants" onclick="applyExcelParticipantsToForm()" disabled>
                    <i class="bi bi-check2-circle me-1"></i> Gunakan Peserta Ini
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL MULTI-SELECT PETUGAS ("PILIH BANYAK")
     ============================================================== -->
<div class="modal fade" id="selectMultipleOfficersModal" tabindex="-1" aria-labelledby="selectMultipleOfficersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: #3B82F6; width: 32px; height: 32px;">
                        <i class="bi bi-check2-all"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="selectMultipleOfficersModalLabel" style="font-size: 1rem;">
                            Pilih Banyak Petugas / Pegawai
                        </h5>
                        <small class="text-muted" style="font-size: 0.72rem;">Centang petugas yang akan ditambahkan ke lembar daftar hadir</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                    <div class="input-group input-group-sm" style="max-width: 300px;">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="multiSelectSearchInput" class="form-control border-start-0 ps-1" placeholder="Cari nama petugas / kecamatan..." oninput="filterMultiSelectOfficers()">
                    </div>
                    <div class="form-check form-check-inline m-0">
                        <input class="form-check-input" type="checkbox" id="checkAllOfficers" onchange="toggleCheckAllOfficers(this.checked)">
                        <label class="form-check-label small fw-bold text-dark" for="checkAllOfficers">Pilih Semua</label>
                    </div>
                </div>

                <div class="list-group rounded-3 border" style="max-height: 350px; overflow-y: auto;" id="multiOfficersListGroup">
                    @foreach($officers as $off)
                        <label class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2.5 officer-checkbox-item" style="cursor: pointer;">
                            <div class="d-flex align-items-center gap-2 overflow-hidden">
                                <input class="form-check-input officer-check flex-shrink-0" 
                                       type="checkbox" 
                                       value="{{ $off->id }}" 
                                       data-name="{{ $off->name }}" 
                                       data-position="{{ $off->position ?: 'Petugas Pendataan Lapangan' }}" 
                                       data-origin="{{ $off->district ?: ($off->village ?: 'Kabupaten Kolaka Utara') }}">
                                <div class="overflow-hidden">
                                    <div class="fw-semibold text-dark text-truncate" style="font-size: 0.84rem;">{{ $off->name }}</div>
                                    <div class="text-muted text-truncate" style="font-size: 0.72rem;">
                                        <i class="bi bi-geo-alt me-0.5"></i> {{ $off->district ?: ($off->village ?: 'Kolaka Utara') }}
                                        &bull; {{ $off->position ?: 'Petugas Lapangan' }}
                                    </div>
                                </div>
                            </div>
                            <span class="badge bg-light text-secondary border rounded-pill">{{ $off->role === 'pegawai' ? 'Pegawai' : 'Mitra' }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer bg-light border-top px-4 py-3 d-flex justify-content-between">
                <span class="small text-muted" id="selectedOfficersCountText">0 petugas dipilih</span>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-3.5" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4" onclick="addSelectedMultipleOfficers()">
                        <i class="bi bi-plus-circle me-1"></i> Tambahkan ke Daftar Hadir
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL SHARE LINK & QR CODE PRESENSI ONLINE
     ============================================================== -->
<div class="modal fade" id="shareLinkModal" tabindex="-1" aria-labelledby="shareLinkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-qr-code text-white"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0" id="shareLinkModalLabel">QR Code & Link Presensi</h6>
                        <small class="text-white-50">Tanda tangan digital langsung dari HP peserta</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <span id="shareModalActivityTitle" class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill small fw-bold text-truncate" style="max-width: 90%;">
                        -
                    </span>
                </div>

                <!-- QR Code Box -->
                <div class="p-3 bg-light rounded-4 d-inline-block border shadow-sm mb-3">
                    <img id="shareModalQrImage" src="" 
                         alt="QR Code Presensi" 
                         class="img-fluid rounded-3" 
                         style="width: 220px; height: 220px;"
                         loading="lazy">
                    <div class="small text-muted mt-2">
                        <i class="bi bi-camera me-1"></i> Pindai kamera HP untuk mengisi presensi
                    </div>
                </div>

                <!-- Link Input Copy -->
                <div class="text-start mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1">
                        <i class="bi bi-link-45deg me-1"></i> Tautan / Link Presensi Langsung
                    </label>
                    <div class="input-group">
                        <input type="text" id="publicAttendanceUrlInput" class="form-control form-control-sm font-monospace bg-light border-end-0" 
                               value="" readonly>
                        <button class="btn btn-primary btn-sm px-3 fw-bold" type="button" onclick="copyModalAttendanceLink()">
                            <i class="bi bi-clipboard me-1"></i> Salin
                        </button>
                    </div>
                    <div class="form-text small text-muted">
                        Bisa dibagikan ke WhatsApp grup peserta atau ditampilkan di proyektor ruangan.
                    </div>
                </div>

                <div class="alert alert-info py-2 px-3 text-start small mb-0 rounded-3 border-0 bg-info-subtle text-info-emphasis">
                    <i class="bi bi-info-circle-fill me-1 text-info"></i>
                    <strong>Cara Kerja:</strong> Peserta membuka tautan, memilih nama mereka, menandatangani langsung di layar HP (touchscreen), lalu tanda tangan otomatis tercetak di format PDF BPS.
                </div>
            </div>
            <div class="modal-footer bg-light border-top px-4 py-2.5 d-flex justify-content-between">
                <a id="shareModalDirectLink" href="#" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Tes Buka Form
                </a>
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openShareLinkModal(url, title) {
        document.getElementById('shareModalActivityTitle').textContent = title || 'Daftar Hadir';
        document.getElementById('shareModalQrImage').src = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&margin=10&data=' + encodeURIComponent(url);
        document.getElementById('publicAttendanceUrlInput').value = url;
        document.getElementById('shareModalDirectLink').href = url;

        const modal = new bootstrap.Modal(document.getElementById('shareLinkModal'));
        modal.show();
    }

    function copyModalAttendanceLink() {
        const url = document.getElementById('publicAttendanceUrlInput').value;
        if (!url) return;

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(() => {
                alert('Tautan presensi berhasil disalin ke clipboard!\n\nAnda dapat membagikannya ke WhatsApp grup peserta atau membukanya di browser.');
            }).catch(() => {
                fallbackCopyUrl(url);
            });
        } else {
            fallbackCopyUrl(url);
        }
    }

    function fallbackCopyUrl(url) {
        const input = document.getElementById('publicAttendanceUrlInput');
        if (input) {
            input.select();
            document.execCommand('copy');
            alert('Tautan presensi berhasil disalin!');
        }
    }
    // Database Assignments & Officers Cache
    const assignmentsCache = @json($assignments);
    const officersCache = @json($officers);

    let parsedExcelData = [];
    let canvas, ctx;
    let isDrawing = false;
    let hasDrawnSignature = false;

    document.addEventListener('DOMContentLoaded', function () {
        initSignatureCanvas();
    });

    function initSignatureCanvas() {
        canvas = document.getElementById('attSignatureCanvas');
        if (!canvas) return;
        ctx = canvas.getContext('2d');

        function resizeCanvas() {
            const rect = canvas.getBoundingClientRect();
            if (rect.width > 0) {
                canvas.width = rect.width;
                canvas.height = 120;
                ctx.lineWidth = 2.2;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                ctx.strokeStyle = '#0F172A';
            }
        }

        window.addEventListener('resize', resizeCanvas);
        setTimeout(resizeCanvas, 300);

        function startDrawing(e) {
            isDrawing = true;
            hasDrawnSignature = true;
            ctx.beginPath();
            const pos = getPos(e);
            ctx.moveTo(pos.x, pos.y);
        }

        function draw(e) {
            if (!isDrawing) return;
            e.preventDefault();
            const pos = getPos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
        }

        function stopDrawing() {
            if (!isDrawing) return;
            isDrawing = false;
            updateSignatureData();
        }

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return {
                x: clientX - rect.left,
                y: clientY - rect.top
            };
        }

        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        window.addEventListener('mouseup', stopDrawing);

        canvas.addEventListener('touchstart', startDrawing, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        window.addEventListener('touchend', stopDrawing);
    }

    function clearSignaturePad() {
        if (!canvas || !ctx) return;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hasDrawnSignature = false;
        document.getElementById('attMakerSignatureData').value = '';
    }

    function updateSignatureData() {
        if (!canvas || !hasDrawnSignature) return;
        document.getElementById('attMakerSignatureData').value = canvas.toDataURL('image/png');
    }

    // Modal Create New Attendance
    function openCreateAttendanceModal() {
        const form = document.getElementById('attendanceSheetForm');
        form.action = "{{ route('admin.trainings.store') }}";
        document.getElementById('attendanceMethodSpoof').innerHTML = '';
        document.getElementById('attendanceSheetModalLabel').innerText = 'Terbitkan Lembar Daftar Hadir Baru';

        document.getElementById('attDocTitle').value = 'DAFTAR HADIR PESERTA';
        document.getElementById('attActivitySubtitle').value = '';
        document.getElementById('attInstitution').value = 'BPS KABUPATEN KOLAKA UTARA TAHUN {{ now()->format("Y") }}';
        document.getElementById('attPlace').value = 'Kantor BPS Kabupaten Kolaka Utara';
        document.getElementById('attDocNumber').value = '';
        document.getElementById('attDate').value = '{{ now()->toDateString() }}';
        document.getElementById('attAssignmentSelect').value = '';
        document.getElementById('attNotes').value = '';
        document.getElementById('attMakerName').value = '{{ auth()->user()->name }}';
        document.getElementById('attMakerNip').value = '{{ auth()->user()->nik ?? "" }}';

        document.getElementById('attendanceParticipantsTbody').innerHTML = '';
        updateParticipantCountBadge();
        clearSignaturePad();

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('attendanceSheetModal'));
        modal.show();
        setTimeout(() => {
            if (canvas) {
                canvas.width = canvas.getBoundingClientRect().width;
                canvas.height = 120;
            }
        }, 300);
    }

    // Modal Edit Existing Attendance
    function openEditAttendanceModal(tr) {
        const form = document.getElementById('attendanceSheetForm');
        form.action = '/admin/trainings/' + tr.id;
        document.getElementById('attendanceMethodSpoof').innerHTML = '@method("PUT")';
        document.getElementById('attendanceSheetModalLabel').innerText = 'Edit Lembar Daftar Hadir: ' + (tr.document_number || tr.title);

        document.getElementById('attDocTitle').value = tr.document_title || 'DAFTAR HADIR PESERTA';
        document.getElementById('attActivitySubtitle').value = tr.activity_subtitle || tr.title;
        document.getElementById('attInstitution').value = tr.institution_name || 'BPS KABUPATEN KOLAKA UTARA TAHUN {{ now()->format("Y") }}';
        document.getElementById('attPlace').value = tr.document_place || tr.location || 'Kantor BPS Kabupaten Kolaka Utara';
        document.getElementById('attDocNumber').value = tr.document_number || '';
        document.getElementById('attDate').value = tr.start_date ? tr.start_date.substring(0, 10) : '{{ now()->toDateString() }}';
        document.getElementById('attAssignmentSelect').value = tr.assignment_id || '';
        document.getElementById('attNotes').value = tr.notes || tr.description || '';
        document.getElementById('attMakerName').value = tr.maker_name || '{{ auth()->user()->name }}';
        document.getElementById('attMakerNip').value = tr.maker_nip || '';

        // Tampilkan peserta yang ada
        const tbody = document.getElementById('attendanceParticipantsTbody');
        tbody.innerHTML = '';
        if (tr.participants && tr.participants.length > 0) {
            tr.participants.forEach((p, idx) => {
                appendParticipantRow({
                    id: p.id,
                    name: p.name,
                    category: p.category || 'Petugas Lapangan',
                    agency: p.agency || 'Kabupaten Kolaka Utara',
                    nip_or_id: p.nip_or_id || ''
                });
            });
        }
        updateParticipantCountBadge();
        clearSignaturePad();

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('attendanceSheetModal'));
        modal.show();
        setTimeout(() => {
            if (canvas) {
                canvas.width = canvas.getBoundingClientRect().width;
                canvas.height = 120;
            }
        }, 300);
    }

    // Ketika Assignment / Kegiatan dipilih
    function onAssignmentSelected(assignId) {
        if (!assignId) return;
        const sel = document.getElementById('attAssignmentSelect');
        const opt = sel.options[sel.selectedIndex];
        if (opt) {
            const titleInput = document.getElementById('attActivitySubtitle');
            if (!titleInput.value || titleInput.value.trim() === '') {
                titleInput.value = opt.getAttribute('data-title') || '';
            }
            const placeInput = document.getElementById('attPlace');
            const loc = opt.getAttribute('data-location');
            if (loc && (!placeInput.value || placeInput.value === 'Kantor BPS Kabupaten Kolaka Utara')) {
                placeInput.value = loc;
            }
        }
    }

    // Import semua petugas dari Kegiatan terpilih
    function importFromSelectedAssignment() {
        const assignId = document.getElementById('attAssignmentSelect').value;
        if (!assignId) {
            alert('Pilih Kegiatan Survei Terkait terlebih dahulu di atas.');
            document.getElementById('attAssignmentSelect').focus();
            return;
        }

        const assign = assignmentsCache.find(a => a.id == assignId);
        if (!assign || !assign.officers || assign.officers.length === 0) {
            alert('Kegiatan ini belum memiliki petugas lapangan yang ditugaskan.');
            return;
        }

        let added = 0;
        assign.officers.forEach(off => {
            appendParticipantRow({
                name: off.name,
                category: off.position || 'Petugas Pendataan Lapangan',
                agency: off.district || (off.village || 'Kabupaten Kolaka Utara'),
                nip_or_id: off.nik || ''
            });
            added++;
        });

        updateParticipantCountBadge();
        alert('Berhasil menarik ' + added + ' petugas dari kegiatan ' + assign.title);
    }

    // Tambah peserta dari dropdown
    function addParticipantFromDropdown() {
        const sel = document.getElementById('attOfficerDropdown');
        const opt = sel.options[sel.selectedIndex];
        if (!opt || !opt.value) {
            alert('Pilih nama petugas terlebih dahulu.');
            return;
        }

        appendParticipantRow({
            name: opt.getAttribute('data-name'),
            category: opt.getAttribute('data-position'),
            agency: opt.getAttribute('data-origin')
        });

        sel.value = '';
        updateParticipantCountBadge();
    }

    // Tambah baris manual
    function addManualParticipantRow() {
        appendParticipantRow({
            name: '',
            category: 'Petugas Lapangan',
            agency: 'Kabupaten Kolaka Utara'
        });
        updateParticipantCountBadge();
    }

    // Append baris ke tabel peserta
    function appendParticipantRow(data) {
        const tbody = document.getElementById('attendanceParticipantsTbody');
        const rowIdx = tbody.children.length;
        const tr = document.createElement('tr');
        tr.className = 'participant-row';

        tr.innerHTML = `
            <td class="text-center fw-bold row-number" style="vertical-align: middle;">${rowIdx + 1}</td>
            <td>
                <input type="hidden" name="participants[${rowIdx}][id]" value="${data.id || ''}">
                <input type="hidden" name="participants[${rowIdx}][nip_or_id]" value="${escapeHtml(data.nip_or_id || '')}">
                <input type="text" name="participants[${rowIdx}][name]" class="form-control form-control-sm fw-bold text-dark participant-name-input" value="${escapeHtml(data.name || '')}" placeholder="Nama lengkap peserta..." required>
            </td>
            <td>
                <input type="text" name="participants[${rowIdx}][category]" class="form-control form-control-sm" value="${escapeHtml(data.category || 'Petugas Lapangan')}" placeholder="Jabatan / Peran...">
            </td>
            <td>
                <input type="text" name="participants[${rowIdx}][agency]" class="form-control form-control-sm" value="${escapeHtml(data.agency || 'Kabupaten Kolaka Utara')}" placeholder="Asal instansi / kecamatan...">
            </td>
            <td class="text-center">
                <div class="d-inline-flex gap-1 align-items-center">
                    <button type="button" class="btn btn-sm btn-light border py-0 px-1 text-secondary" title="Pindah Naik" onclick="moveRowUp(this)">
                        <i class="bi bi-arrow-up"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-light border py-0 px-1 text-secondary" title="Pindah Turun" onclick="moveRowDown(this)">
                        <i class="bi bi-arrow-down"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-light border py-0 px-1 text-danger" title="Hapus Baris" onclick="removeParticipantRow(this)">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </td>
        `;

        tbody.appendChild(tr);
        reindexRows();
    }

    function removeParticipantRow(btn) {
        const row = btn.closest('tr');
        row.remove();
        reindexRows();
        updateParticipantCountBadge();
    }

    function moveRowUp(btn) {
        const row = btn.closest('tr');
        const prev = row.previousElementSibling;
        if (prev) {
            row.parentNode.insertBefore(row, prev);
            reindexRows();
        }
    }

    function moveRowDown(btn) {
        const row = btn.closest('tr');
        const next = row.nextElementSibling;
        if (next) {
            row.parentNode.insertBefore(next, row);
            reindexRows();
        }
    }

    function reindexRows() {
        const rows = document.querySelectorAll('#attendanceParticipantsTbody tr');
        rows.forEach((row, idx) => {
            const numCell = row.querySelector('.row-number');
            if (numCell) numCell.innerText = idx + 1;

            row.querySelectorAll('input').forEach(inp => {
                const name = inp.getAttribute('name');
                if (name) {
                    inp.setAttribute('name', name.replace(/participants\[\d+\]/, `participants[${idx}]`));
                }
            });
        });
    }

    function updateParticipantCountBadge() {
        const count = document.querySelectorAll('#attendanceParticipantsTbody tr').length;
        const badge = document.getElementById('attParticipantCountBadge');
        if (badge) badge.innerText = count + ' Peserta';
    }

    // Modal Multi-Select Petugas ("Pilih Banyak")
    function openMultiSelectOfficersModal() {
        document.getElementById('multiSelectSearchInput').value = '';
        document.getElementById('checkAllOfficers').checked = false;
        document.querySelectorAll('.officer-check').forEach(chk => chk.checked = false);
        filterMultiSelectOfficers();
        updateMultiSelectCount();

        const modal = new bootstrap.Modal(document.getElementById('selectMultipleOfficersModal'));
        modal.show();
    }

    function filterMultiSelectOfficers() {
        const q = (document.getElementById('multiSelectSearchInput').value || '').toLowerCase().trim();
        const items = document.querySelectorAll('.officer-checkbox-item');
        items.forEach(item => {
            const txt = item.innerText.toLowerCase();
            item.style.display = (!q || txt.includes(q)) ? '' : 'none';
        });
    }

    function toggleCheckAllOfficers(checked) {
        document.querySelectorAll('.officer-checkbox-item').forEach(item => {
            if (item.style.display !== 'none') {
                const chk = item.querySelector('.officer-check');
                if (chk) chk.checked = checked;
            }
        });
        updateMultiSelectCount();
    }

    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('officer-check')) {
            updateMultiSelectCount();
        }
    });

    function updateMultiSelectCount() {
        const count = document.querySelectorAll('.officer-check:checked').length;
        document.getElementById('selectedOfficersCountText').innerText = count + ' petugas dipilih';
    }

    function addSelectedMultipleOfficers() {
        const checked = document.querySelectorAll('.officer-check:checked');
        if (checked.length === 0) {
            alert('Pilih minimal satu petugas terlebih dahulu.');
            return;
        }

        checked.forEach(chk => {
            appendParticipantRow({
                name: chk.getAttribute('data-name'),
                category: chk.getAttribute('data-position'),
                agency: chk.getAttribute('data-origin')
            });
        });

        updateParticipantCountBadge();
        bootstrap.Modal.getInstance(document.getElementById('selectMultipleOfficersModal')).hide();
    }

    // Upload Excel Modal
    function openCreateFromExcelModal() {
        openUploadExcelModalForCurrentForm(true);
    }

    function openUploadExcelModalForCurrentForm(isNewDoc = false) {
        document.getElementById('excelParticipantsFileInput').value = '';
        document.getElementById('excelPreviewContainer').style.display = 'none';
        document.getElementById('excelPreviewLoading').style.display = 'none';
        document.getElementById('btnApplyExcelParticipants').disabled = true;
        parsedExcelData = [];

        window._excelIsNewDoc = isNewDoc;

        const modal = new bootstrap.Modal(document.getElementById('uploadAttendanceExcelModal'));
        modal.show();
    }

    function previewExcelParticipantsFile() {
        const input = document.getElementById('excelParticipantsFileInput');
        if (!input.files || input.files.length === 0) return;

        const file = input.files[0];
        const formData = new FormData();
        formData.append('excel_file', file);
        formData.append('_token', '{{ csrf_token() }}');

        document.getElementById('excelPreviewLoading').style.display = 'block';
        document.getElementById('excelPreviewContainer').style.display = 'none';
        document.getElementById('btnApplyExcelParticipants').disabled = true;

        fetch('{{ route("admin.trainings.parse-excel") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('excelPreviewLoading').style.display = 'none';
            if (data.success && data.participants && data.participants.length > 0) {
                parsedExcelData = data.participants;
                document.getElementById('excelPreviewCountText').innerText = `Berhasil membaca ${data.count} peserta`;

                const tbody = document.getElementById('excelPreviewTbody');
                tbody.innerHTML = '';
                data.participants.slice(0, 5).forEach((p, idx) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${idx + 1}</td>
                        <td class="fw-bold">${escapeHtml(p.name)}</td>
                        <td>${escapeHtml(p.category)}</td>
                        <td>${escapeHtml(p.agency)}</td>
                    `;
                    tbody.appendChild(row);
                });

                document.getElementById('excelPreviewContainer').style.display = 'block';
                document.getElementById('btnApplyExcelParticipants').disabled = false;
            } else {
                alert(data.message || 'Gagal membaca data dari berkas Excel.');
            }
        })
        .catch(err => {
            document.getElementById('excelPreviewLoading').style.display = 'none';
            alert('Terjadi kesalahan saat memproses file: ' + err.message);
        });
    }

    function applyExcelParticipantsToForm() {
        if (!parsedExcelData || parsedExcelData.length === 0) return;

        bootstrap.Modal.getInstance(document.getElementById('uploadAttendanceExcelModal')).hide();

        if (window._excelIsNewDoc) {
            openCreateAttendanceModal();
        }

        parsedExcelData.forEach(p => {
            appendParticipantRow({
                name: p.name,
                category: p.category || 'Petugas Lapangan',
                agency: p.agency || 'Kabupaten Kolaka Utara',
                nip_or_id: p.nip_or_id || ''
            });
        });

        updateParticipantCountBadge();
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
</script>
@endpush
@endsection

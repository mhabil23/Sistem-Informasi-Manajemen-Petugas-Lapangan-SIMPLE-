@extends('layouts.app')

@section('title', 'Pengisian Laporan Perjadin')
@section('page_title', 'Pengisian Laporan Perjadin')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('petugas.dashboard') }}">Petugas</a></li>
    <li class="breadcrumb-item active" aria-current="page">Pengisian Laporan Perjadin</li>
@endsection

@section('content')
<!-- Header & Action -->
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h3 class="fw-bold text-dark mb-1">Pengisian Laporan Perjadin &amp; Pengeluaran Riil</h3>
        <p class="text-muted small mb-0">Satu kali pengisian &amp; satu kali tanda tangan digital menghasilkan <strong>1 File PDF Resmi</strong> berisi <em>Laporan Perjalanan Dinas (+ Dokumentasi)</em> dan <em>Daftar Pengeluaran Riil</em>.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <button type="button" class="btn btn-sp-primary" data-bs-toggle="modal" data-bs-target="#createReportModal">
            <i class="fa-solid fa-plus me-1"></i> Isi Laporan Perjadin Baru
        </button>
    </div>
</div>

<!-- Banner Cepat Cetak PDF Gabungan setelah Berhasil Simpan -->
@if(session('created_report_id'))
    <div class="alert alert-success border-success d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 shadow-sm" role="alert">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-file-circle-check fa-2x me-3 text-success"></i>
            <div>
                <h6 class="alert-heading fw-bold mb-1">Dokumen Gabungan (1 PDF) Siap Dicetak!</h6>
                <div class="small">Laporan Perjalanan Dinas dan Daftar Pengeluaran Riil beserta tanda tangan digital Anda telah digabung dalam 1 file PDF.</div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('petugas.reports.preview', session('created_report_id')) }}" target="_blank" class="btn btn-sm btn-primary fw-semibold">
                <i class="fa-solid fa-print me-1"></i> Preview / Cetak 1 File PDF
            </a>
            <a href="{{ route('petugas.reports.download', session('created_report_id')) }}" class="btn btn-sm btn-success fw-semibold">
                <i class="fa-solid fa-file-pdf me-1"></i> Unduh PDF
            </a>
        </div>
    </div>
@endif

<!-- Alert Perlu Revisi (Jika ada) -->
@if($revisionCount > 0)
    <div class="alert alert-warning border-warning d-flex align-items-center mb-4 shadow-sm" role="alert">
        <i class="fa-solid fa-triangle-exclamation fa-2x me-3 text-warning"></i>
        <div>
            <h6 class="alert-heading fw-bold mb-1">Perhatian: Ada {{ $revisionCount }} Laporan Memerlukan Perbaikan / Revisi!</h6>
            <div class="small">Administrator telah mengembalikan laporan Anda dengan catatan evaluasi. Silakan klik tombol perbaiki (ikon pensil kuning) pada tabel di bawah untuk membaca catatan dan mengirimkan revisi.</div>
        </div>
    </div>
@endif

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Total Laporan</div>
                <div class="sp-stat-value">{{ number_format($totalReports) }}</div>
                <div class="sp-stat-desc">Aktivitas saya</div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #f1f5f9; color: #64748b;">
                <i class="fa-regular fa-file-lines"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Draft</div>
                <div class="sp-stat-value text-secondary">{{ number_format($draftCount) }}</div>
                <div class="sp-stat-desc">Belum diajukan</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0e7ff; color: #4338ca;">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Diajukan / Diperiksa</div>
                <div class="sp-stat-value text-primary">{{ number_format($submittedCount) }}</div>
                <div class="sp-stat-desc">Menunggu evaluasi</div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-6 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #fee2e2; color: #dc2626;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Perlu Revisi</div>
                <div class="sp-stat-value text-danger">{{ number_format($revisionCount) }}</div>
                <div class="sp-stat-desc">Perlu diperbaiki</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Disetujui</div>
                <div class="sp-stat-value text-success">{{ number_format($approvedCount) }}</div>
                <div class="sp-stat-desc">Laporan valid</div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="sp-card mb-4">
    <div class="sp-card-body p-3">
        <form method="GET" action="{{ route('petugas.reports.index') }}" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="form-control border-start-0 ps-0" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nomor laporan, judul, uraian pekerjaan...">
                </div>
            </div>

            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="all">-- Semua Status --</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Diajukan</option>
                    <option value="reviewing" {{ request('status') == 'reviewing' ? 'selected' : '' }}>Dalam Pemeriksaan</option>
                    <option value="revision_needed" {{ request('status') == 'revision_needed' ? 'selected' : '' }}>Perlu Revisi</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sp-primary flex-fill">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('petugas.reports.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="sp-card mb-4">
    <div class="sp-card-header d-flex justify-content-between align-items-center">
        <h5 class="sp-card-title mb-0">
            <i class="fa-solid fa-clipboard-user text-primary"></i>
            Daftar Laporan Aktivitas Saya ({{ $reports->total() }})
        </h5>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle d-md-none" style="font-size: 0.72rem;">
            {{ $reports->total() }} Laporan
        </span>
    </div>
    <div class="sp-card-body p-0">
        <!-- MODE DESKTOP & TABLET: Tabel Lengkap -->
        <div class="table-responsive d-none d-md-block">
            <table class="table sp-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="min-width: 140px;">No. Laporan</th>
                        <th style="min-width: 200px;">Judul & Kegiatan</th>
                        <th style="min-width: 130px;">Tanggal</th>
                        <th>Uraian Pekerjaan</th>
                        <th>Lampiran</th>
                        <th>Status</th>
                        <th class="text-end" style="min-width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $rep)
                        <tr>
                            <td>
                                <span class="fw-bold font-monospace text-dark">{{ $rep->report_number ?? 'LAP-'.$rep->id }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">{{ $rep->activity_title }}</div>
                                <small class="text-muted">
                                    {{ $rep->assignment ? $rep->assignment->title : 'Kegiatan Mandiri' }}
                                </small>
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark">
                                    <i class="fa-regular fa-calendar me-1 text-primary"></i> {{ $rep->activity_date->format('d/m/Y') }}
                                </div>
                            </td>
                            <td>
                                <div class="small text-secondary text-truncate" style="max-width: 250px;" title="{{ $rep->work_description }}">
                                    {{ $rep->work_description }}
                                </div>
                                @if($rep->admin_notes && $rep->status === 'revision_needed')
                                    <div class="small text-danger fw-semibold mt-1">
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Catatan Admin: {{ \Illuminate\Support\Str::limit($rep->admin_notes, 45) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($rep->attachments->isNotEmpty())
                                    <span class="badge bg-light text-dark border">
                                        <i class="fa-solid fa-paperclip text-primary me-1"></i> {{ $rep->attachments->count() }} Berkas
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $rep->status_badge }} px-2 py-1">
                                    {{ $rep->status_label }}
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="sp-action-btn-group">
                                    <!-- Detail Modal Preview -->
                                    <button type="button" 
                                            class="btn btn-light border sp-action-btn text-info" 
                                            title="Lihat Format Dokumen" 
                                            data-bs-toggle="tooltip"
                                            onclick="openDetailModal({{ json_encode($rep) }})">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    <!-- Preview & Cetak PDF -->
                                    <a href="{{ route('petugas.reports.preview', $rep) }}" 
                                       target="_blank"
                                       class="btn btn-light border sp-action-btn text-primary" 
                                       title="Cetak / Preview PDF" 
                                       data-bs-toggle="tooltip">
                                        <i class="fa-solid fa-print"></i>
                                    </a>

                                    <!-- Unduh PDF Resmi -->
                                    <a href="{{ route('petugas.reports.download', $rep) }}" 
                                       class="btn btn-light border sp-action-btn text-danger" 
                                       title="Unduh PDF Resmi" 
                                       data-bs-toggle="tooltip">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </a>

                                    <!-- Edit / Perbaiki jika Draft atau Perlu Revisi -->
                                    @if(in_array($rep->status, ['draft', 'revision_needed']))
                                        <button type="button" 
                                                class="btn btn-light border sp-action-btn text-warning" 
                                                title="{{ $rep->status === 'revision_needed' ? 'Perbaiki Laporan' : 'Edit Draft' }}" 
                                                data-bs-toggle="tooltip"
                                                onclick="openEditModal({{ json_encode($rep) }})">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>
                                    @endif

                                    <!-- Hapus jika Draft -->
                                    @if($rep->status === 'draft')
                                        <form method="POST" action="{{ route('petugas.reports.destroy', $rep) }}" class="d-inline" onsubmit="return confirm('Hapus draft laporan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-light border sp-action-btn text-danger" 
                                                    title="Hapus Draft" 
                                                    data-bs-toggle="tooltip">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-clipboard-user fa-3x mb-3 text-secondary opacity-50"></i>
                                <div class="fw-semibold">Belum ada laporan kegiatan.</div>
                                <div class="small">Klik tombol "Pengisian Laporan Perjadin" di kanan atas untuk membuat dokumen gabungan (Laporan Perjadin &amp; Daftar Pengeluaran Riil).</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MODE MOBILE / HP: Daftar Kartu Laporan Responsif (No Horizontal Scroll) -->
        <div class="d-block d-md-none p-3">
            @forelse($reports as $rep)
                <div class="card border rounded-3 mb-3 shadow-none bg-white">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-light text-dark font-monospace border mb-1" style="font-size: 0.75rem;">
                                    {{ $rep->report_number ?? 'LAP-'.$rep->id }}
                                </span>
                                <h6 class="fw-bold text-dark mb-0">{{ $rep->activity_title }}</h6>
                                <small class="text-primary fw-medium">
                                    {{ $rep->assignment ? $rep->assignment->title : 'Kegiatan Mandiri' }}
                                </small>
                            </div>
                            <span class="badge {{ $rep->status_badge }} px-2 py-1" style="font-size: 0.72rem;">
                                {{ $rep->status_label }}
                            </span>
                        </div>

                        <div class="small text-secondary mb-2">
                            <div class="mb-1">
                                <i class="fa-regular fa-calendar text-primary me-1.5" style="width: 14px;"></i>
                                <span class="fw-medium text-dark">{{ $rep->activity_date->translatedFormat('d F Y') }}</span>
                            </div>
                            <div class="mb-1">
                                <i class="fa-solid fa-align-left text-muted me-1.5" style="width: 14px;"></i>
                                <span>{{ Str::limit($rep->work_description, 80) }}</span>
                            </div>
                            @if($rep->attachments->isNotEmpty())
                                <div class="mb-1">
                                    <i class="fa-solid fa-paperclip text-primary me-1.5" style="width: 14px;"></i>
                                    <span class="badge bg-light text-dark border" style="font-size: 0.7rem;">
                                        {{ $rep->attachments->count() }} Berkas Lampiran
                                    </span>
                                </div>
                            @endif
                        </div>

                        @if($rep->admin_notes && $rep->status === 'revision_needed')
                            <div class="alert alert-warning py-1.5 px-2 mb-2 small border-warning">
                                <i class="fa-solid fa-triangle-exclamation text-warning me-1"></i>
                                <strong>Catatan Admin:</strong> {{ $rep->admin_notes }}
                            </div>
                        @endif

                        <div class="d-flex justify-content-end align-items-center gap-1.5 pt-2 border-top">
                            <button type="button" 
                                    class="btn btn-sm btn-light border text-info shadow-sm" 
                                    title="Detail" 
                                    onclick="openDetailModal({{ json_encode($rep) }})">
                                <i class="fa-solid fa-eye me-1"></i> Detail
                            </button>
                            <a href="{{ route('petugas.reports.preview', $rep) }}" 
                               target="_blank" 
                               class="btn btn-sm btn-light border text-primary shadow-sm" 
                               title="Cetak">
                                <i class="fa-solid fa-print"></i>
                            </a>
                            <a href="{{ route('petugas.reports.download', $rep) }}" 
                               class="btn btn-sm btn-light border text-danger shadow-sm" 
                               title="Unduh PDF">
                                <i class="fa-solid fa-file-pdf"></i>
                            </a>
                            @if(in_array($rep->status, ['draft', 'revision_needed']))
                                <button type="button" 
                                        class="btn btn-sm btn-light border text-warning shadow-sm" 
                                        title="{{ $rep->status === 'revision_needed' ? 'Perbaiki' : 'Edit' }}" 
                                        onclick="openEditModal({{ json_encode($rep) }})">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </button>
                            @endif
                            @if($rep->status === 'draft')
                                <form method="POST" action="{{ route('petugas.reports.destroy', $rep) }}" class="d-inline" onsubmit="return confirm('Hapus draft laporan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger shadow-sm" title="Hapus">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <i class="fa-solid fa-clipboard-user fa-3x mb-2 text-secondary opacity-50"></i>
                    <div class="fw-semibold">Belum ada laporan kegiatan.</div>
                    <div class="small">Klik tombol "Pengisian Laporan Perjadin" di atas.</div>
                </div>
            @endforelse
        </div>
    </div>

    @if($reports->hasPages())
        <div class="sp-card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Menampilkan {{ $reports->firstItem() }} sampai {{ $reports->lastItem() }} dari {{ $reports->total() }} laporan
            </div>
            <div>
                {{ $reports->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

<!-- ==============================================================
     MODAL BUAT LAPORAN BARU (FORMAT RESMI SPD BPS)
     ============================================================== -->
<div class="modal fade" id="createReportModal" tabindex="-1" aria-labelledby="createReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="createReportModalLabel">
                        <i class="fa-solid fa-file-signature text-primary me-2"></i> Pengisian Laporan Perjadin &amp; Pengeluaran Riil
                    </h5>
                    <div class="small text-muted">Menghasilkan 1 File PDF Gabungan: Laporan Perjalanan Dinas (+ Dokumentasi) &amp; Daftar Pengeluaran Riil</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createReportForm" method="POST" action="{{ route('petugas.reports.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-primary bg-primary bg-opacity-10 border-primary border-opacity-25 py-2 px-3 mb-3 d-flex align-items-center">
                        <i class="fa-solid fa-circle-info text-primary me-2 fa-lg"></i>
                        <div class="small text-dark">
                            <strong>Terintegrasi 1 File PDF:</strong> Nama &amp; NIK terisi otomatis dari akun Anda, dan cukup <strong>1 kali tanda tangan digital</strong> untuk muncul otomatis di <em>Laporan Perjalanan Dinas</em> maupun <em>Daftar Pengeluaran Riil</em>.
                        </div>
                    </div>

                    <!-- Bagian A: Yang Melakukan Perjalanan Dinas -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2 px-3 fw-bold text-dark small d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-user-tie text-primary me-1"></i> A. IDENTITAS PETUGAS (TERISI OTOMATIS)</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Otomatis dari Profil Akun</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label small fw-semibold text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="officer_name" class="form-control bg-light fw-semibold" value="{{ auth()->user()->name }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold text-secondary">Jenis Identitas</label>
                                    <select name="id_type" class="form-select">
                                        <option value="NIK" selected>NIK (KTP)</option>
                                        <option value="NIP">NIP (Pegawai/PNS)</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-secondary">Nomor NIK / NIP <span class="text-danger">*</span></label>
                                    <input type="text" name="officer_nip" class="form-control bg-light fw-semibold font-monospace" value="{{ auth()->user()->nik ?? '' }}" placeholder="Masukkan NIK / NIP Anda" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Pangkat / Golongan</label>
                                    <input type="text" name="officer_rank" class="form-control" value="{{ auth()->user()->rank ?? '-' }}" placeholder="Contoh: - atau Pengatur, II/d">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Jabatan</label>
                                    <input type="text" name="officer_position" class="form-control" value="{{ auth()->user()->position ?? 'Mitra BPS Kabupaten Kolaka Utara' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bagian B: Dasar Pelaksanaan -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2 px-3 fw-bold text-dark small">
                            <i class="fa-solid fa-scale-balanced text-primary me-1"></i> B. DASAR PELAKSANAAN (LAPORAN &amp; PENGELUARAN RIIL)
                        </div>
                        <div class="card-body p-3">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">1. DIPA Satuan Kerja</label>
                                <textarea name="basis_dipa" class="form-control" rows="2">{{ 'DIPA Satuan Kerja Badan Pusat Statistik Kabupaten Kolaka Utara Tahun Anggaran ' . date('Y') . ' Nomor SP DIPA-054.01.2.667477/' . date('Y') . ' tanggal 1 Desember ' . (date('Y') - 1) . '.' }}</textarea>
                            </div>
                            <div class="row g-3 mb-2">
                                <div class="col-md-7">
                                    <label class="form-label small fw-semibold text-secondary">2a. Nomor Surat Tugas <span class="text-danger">*</span></label>
                                    <input type="text" name="st_number" id="create_st_number" class="form-control" value="B-1812/74080/HM.300/{{ date('Y') }}" oninput="syncCreateBasisSt()" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small fw-semibold text-secondary">2b. Tanggal Surat Tugas <span class="text-danger">*</span></label>
                                    <input type="date" name="st_date" id="create_st_date" class="form-control" value="{{ date('Y-m-d') }}" oninput="syncCreateBasisSt()" required>
                                </div>
                            </div>
                            <div>
                                <label class="form-label small fw-semibold text-secondary">Teks Lengkap Dasar Surat Tugas (Otomatis Terbentuk)</label>
                                <textarea name="basis_st" id="create_basis_st" class="form-control" rows="2">{{ 'Surat Tugas Kepala Badan Pusat Statistik Kabupaten Kolaka Utara Nomor B-1812/74080/HM.300/' . date('Y') . ' Tanggal ' . \Carbon\Carbon::now()->translatedFormat('d F Y') . '.' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Bagian C & D: Tujuan & Waktu Perjalanan Dinas -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2 px-3 fw-bold text-dark small">
                            <i class="fa-solid fa-map-location-dot text-primary me-1"></i> C &amp; D. TUJUAN &amp; WAKTU PERJALANAN DINAS
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label class="form-label small fw-semibold text-secondary">Judul Kegiatan / Agenda <span class="text-danger">*</span></label>
                                    <input type="text" name="activity_title" class="form-control" placeholder="Contoh: Pertemuan Tim Sensus Ekonomi {{ date('Y') }}" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small fw-semibold text-secondary">Penugasan Terkait</label>
                                    <select name="assignment_id" id="create_assignment_id" class="form-select" onchange="autoFillLocationAndExpense(this, 'create_activity_location', 'create_expense_list')">
                                        <option value="" data-location="">-- Kegiatan Mandiri / Umum --</option>
                                        @foreach($myAssignments as $asgn)
                                            <option value="{{ $asgn->id }}" data-location="{{ $asgn->location }}">{{ $asgn->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-secondary">
                                        <i class="fa-solid fa-location-dot text-primary me-1"></i> Lokasi / Wilayah Tugas / Asal Keberangkatan
                                    </label>
                                    <input type="text" name="activity_location" id="create_activity_location" class="form-control" placeholder="Contoh: Kecamatan Batu Putih" oninput="syncDefaultExpenseDesc('create_activity_location', 'create_expense_list')">
                                    <div class="form-text small">Digunakan pada tujuan perjalanan dinas dan rincian asal biaya transport Daftar Pengeluaran Riil.</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">C. Tujuan Perjalanan Dinas (Teks Lengkap)</label>
                                <textarea name="destination_purpose" class="form-control" rows="2" placeholder="Contoh: Perjalanan Dinas Biasa dalam kota sampai dengan 8 jam dalam rangka melaksanakan pendataan di Kecamatan Batu Putih."></textarea>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">D. Durasi / Lama Waktu Perjalanan</label>
                                    <input type="text" name="duration_text" class="form-control" value="Selama 1 (satu) Hari">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Tanggal Pelaksanaan Kegiatan <span class="text-danger">*</span></label>
                                    <input type="date" name="activity_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bagian E & F: Hasil Perjalanan Dinas, Kendala, Solusi & Saran -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2 px-3 fw-bold text-dark small">
                            <i class="fa-solid fa-list-check text-primary me-1"></i> E &amp; F. HASIL PERJALANAN DINAS, KENDALA, SOLUSI &amp; SARAN
                        </div>
                        <div class="card-body p-3">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">E. Uraian Hasil Perjalanan Dinas <span class="text-danger">*</span></label>
                                <textarea name="work_description" class="form-control" rows="4" placeholder="Menuju lokasi kegiatan..., melakukan koordinasi..., melaksanakan pendataan..., submit data..." required></textarea>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Kendala (Opsional)</label>
                                    <textarea name="obstacles" class="form-control" rows="3" placeholder="- Masih ada petugas yang belum...&#10;- Beberapa desa belum memiliki jaringan yang baik."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Solusi (Opsional)</label>
                                    <textarea name="solutions" class="form-control" rows="3" placeholder="- Menyarankan petugas melakukan submit data...&#10;- Melakukan log in terlebih dahulu di daerah dengan jaringan bagus."></textarea>
                                </div>
                            </div>
                            <div>
                                <label class="form-label small fw-semibold text-secondary">F. Saran dan Rekomendasi / Tindak Lanjut</label>
                                <textarea name="recommendations" class="form-control" rows="2" placeholder="Ketik '-' jika tidak ada">-</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- DOKUMEN 2: DAFTAR PENGELUARAN RIIL -->
                    <div class="card mb-3 border border-success">
                        <div class="card-header bg-success-subtle text-success-emphasis py-2 px-3 fw-bold small d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-receipt me-1"></i> DAFTAR PENGELUARAN RIIL (DIGABUNG OTOMATIS KE 1 PDF)</span>
                            <button type="button" class="btn btn-xs btn-success py-0.5 px-2" style="font-size: 0.72rem;" onclick="addExpenseRow('create_expense_list', 'create_expense_total', 'create_expense_terbilang')">
                                <i class="fa-solid fa-plus me-1"></i> Tambah Baris Biaya
                            </button>
                        </div>
                        <div class="card-body p-3">
                            <div class="table-responsive mb-2">
                                <table class="table table-bordered table-sm align-middle mb-0">
                                    <thead class="table-light text-center small">
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th>Perincian Biaya</th>
                                            <th style="width: 180px;">Jumlah (Rp)</th>
                                            <th style="width: 45px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="create_expense_list">
                                        <tr>
                                            <td class="text-center small fw-bold">1</td>
                                            <td>
                                                <input type="text" name="expense_descriptions[]" class="form-control form-control-sm expense-desc-input" value="Biaya transport dari Wilayah Tugas ke Kantor BPS Kabupaten Kolaka Utara (P.P.) selama 1 hari" required>
                                            </td>
                                            <td>
                                                <input type="number" name="expense_amounts[]" class="form-control form-control-sm text-end fw-semibold expense-amt-input" value="50000" min="0" oninput="recalcExpenses('create_expense_list', 'create_expense_total', 'create_expense_terbilang')" required>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1.5" onclick="removeExpenseRow(this, 'create_expense_list', 'create_expense_total', 'create_expense_terbilang')" title="Hapus baris"><i class="fa-solid fa-xmark"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light">
                                            <th colspan="2" class="text-center small">
                                                JUMLAH (<span id="create_expense_terbilang" class="fst-italic fw-semibold text-success">Lima Puluh Ribu Rupiah</span>)
                                            </th>
                                            <th class="text-end font-monospace text-dark" id="create_expense_total">Rp. 50.000,-</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="row g-2 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Pejabat Pembuat Komitmen (Menyetujui)</label>
                                    <input type="text" name="ppk_name" class="form-control form-control-sm" value="Hidayatullah, SE">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1">NIP Pejabat Pembuat Komitmen</label>
                                    <input type="text" name="ppk_nip" class="form-control form-control-sm font-monospace" value="198510292011011009">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tanda Tangan & Penandatanganan -->
                    <div class="card mb-3 border border-primary">
                        <div class="card-header bg-primary text-white py-2 px-3 fw-bold small d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-signature me-1"></i> TANDA TANGAN DIGITAL (1x INPUT UNTUK KEDUA DOKUMEN)</span>
                            <span class="badge bg-light text-primary">Muncul di Perjadin &amp; Pengeluaran Riil</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Kota Tempat Penandatanganan</label>
                                    <input type="text" name="signing_city" class="form-control" value="Lasusua" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Tanggal Tanda Tangan</label>
                                    <input type="date" name="signing_date" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>

                            <label class="form-label small fw-semibold text-dark mb-1">
                                <i class="fa-solid fa-pen-nib text-primary me-1"></i> Input Tanda Tangan Pelaksana (1 Kali Saja):
                            </label>
                            
                            <!-- Tab Pilihan Tanda Tangan -->
                            <ul class="nav nav-pills nav-fill mb-2" id="createSigTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active py-1 px-3 small" id="create-draw-tab" data-bs-toggle="pill" data-bs-target="#create-draw-pane" type="button" role="tab">
                                        <i class="fa-solid fa-pen-nib me-1"></i> Gores Tanda Tangan (Layar HP / Mouse)
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-1 px-3 small" id="create-upload-tab" data-bs-toggle="pill" data-bs-target="#create-upload-pane" type="button" role="tab">
                                        <i class="fa-solid fa-upload me-1"></i> Unggah File Gambar Tanda Tangan
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="createSigTabContent">
                                <!-- Gores Langsung -->
                                <div class="tab-pane fade show active" id="create-draw-pane" role="tabpanel">
                                    <div class="p-2 bg-light border rounded text-center">
                                        <canvas id="create_sig_canvas" class="border bg-white rounded shadow-sm" width="500" height="160" style="touch-action: none; cursor: crosshair; max-width: 100%; display: inline-block;"></canvas>
                                        <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                                            <small class="text-muted"><i class="fa-solid fa-hand-pointer me-1"></i> Goreskan tanda tangan di kotak atas — otomatis tampil di 2 dokumen sekaligus</small>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearCreateCanvas()">
                                                <i class="fa-solid fa-rotate-left me-1"></i> Hapus / Ulangi
                                            </button>
                                        </div>
                                        <input type="hidden" name="signature_data" id="create_signature_data">
                                    </div>
                                </div>
                                <!-- Upload File -->
                                <div class="tab-pane fade" id="create-upload-pane" role="tabpanel">
                                    <div class="p-3 bg-light border rounded">
                                        <input type="file" name="signature_file" class="form-control" accept="image/png,image/jpeg">
                                        <div class="form-text small mt-1">Pilih file tanda tangan (format PNG/JPG latar transparan atau putih, maks. 3MB).</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lampiran Foto Dokumentasi -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2 px-3 fw-bold text-dark small">
                            <i class="fa-solid fa-camera text-primary me-1"></i> FOTO DOKUMENTASI KEGIATAN (HALAMAN DOKUMENTASI)
                        </div>
                        <div class="card-body p-3">
                            <label class="form-label small fw-semibold text-secondary">
                                Unggah Foto Bukti Lapangan / Kegiatan (Maks. 5MB per file)
                            </label>
                            <input type="file" name="photos[]" class="form-control" multiple accept="image/*,.pdf">
                            <div class="form-text small">Foto akan ditampilkan pada halaman Dokumentasi berkop BPS Kabupaten Kolaka Utara.</div>
                        </div>
                    </div>

                    <!-- Opsi Tambahan Surat Pernyataan Tidak Menggunakan Kendaraan Dinas -->
                    <div class="card mb-3 border">
                        <div class="card-body p-3">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="include_vehicle_statement" value="1" id="create_vehicle_stmt">
                                <label class="form-check-label fw-semibold text-dark small" for="create_vehicle_stmt">
                                    <i class="fa-solid fa-file-contract text-secondary me-1"></i> Lampirkan juga Surat Pernyataan Tidak Menggunakan Kendaraan Dinas (Opsional)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Opsi Tindakan Simpan -->
                    <div class="p-3 bg-light rounded border">
                        <label class="form-label small fw-semibold text-secondary mb-2">PILIH TINDAKAN PENYIMPANAN:</label>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="action_type" id="actionSubmit" value="submitted" checked>
                                <label class="form-check-label fw-bold text-primary" for="actionSubmit">
                                    <i class="fa-solid fa-paper-plane me-1"></i> Ajukan &amp; Buat 1 PDF Gabungan
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="action_type" id="actionDraft" value="draft">
                                <label class="form-check-label text-muted" for="actionDraft">
                                    <i class="fa-regular fa-bookmark me-1"></i> Simpan Sebagai Draft Sementara
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary">
                        <i class="fa-solid fa-file-pdf me-1"></i> Simpan &amp; Gabung Jadi 1 PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL EDIT / PERBAIKI LAPORAN (FORMAT RESMI SPD BPS)
     ============================================================== -->
<div class="modal fade" id="editReportModal" tabindex="-1" aria-labelledby="editReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="editReportModalLabel">
                        <i class="fa-solid fa-pen-to-square text-warning me-2"></i> Perbaiki & Lengkapi Laporan Perjalanan Dinas
                    </h5>
                    <div class="small text-muted">Sesuaikan data laporan perjalanan dinas resmi BPS Kabupaten Kolaka Utara</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editReportForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <!-- Catatan Evaluasi Admin (jika status revision_needed) -->
                    <div id="edit_admin_notes_box" class="alert alert-danger border-danger p-3 mb-3 d-none">
                        <div class="fw-bold small text-danger mb-1">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i> CATATAN REVISI DARI ADMINISTRATOR:
                        </div>
                        <div id="edit_admin_notes_text" class="small text-dark"></div>
                    </div>

                    <!-- Bagian A: Yang Melakukan Perjalanan Dinas -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2 px-3 fw-bold text-dark small">
                            <i class="fa-solid fa-user-tie text-primary me-1"></i> A. YANG MELAKUKAN PERJALANAN DINAS
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label small fw-semibold text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" id="edit_officer_name" name="officer_name" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold text-secondary">Jenis Identitas</label>
                                    <select id="edit_id_type" name="id_type" class="form-select">
                                        <option value="NIK">NIK (KTP)</option>
                                        <option value="NIP">NIP (Pegawai/PNS)</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-secondary">Nomor NIK / NIP</label>
                                    <input type="text" id="edit_officer_nip" name="officer_nip" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Pangkat / Golongan</label>
                                    <input type="text" id="edit_officer_rank" name="officer_rank" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Jabatan</label>
                                    <input type="text" id="edit_officer_position" name="officer_position" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bagian B: Dasar Pelaksanaan -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2 px-3 fw-bold text-dark small">
                            <i class="fa-solid fa-scale-balanced text-primary me-1"></i> B. DASAR PELAKSANAAN (LAPORAN &amp; PENGELUARAN RIIL)
                        </div>
                        <div class="card-body p-3">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">1. DIPA Satuan Kerja</label>
                                <textarea id="edit_basis_dipa" name="basis_dipa" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="row g-3 mb-2">
                                <div class="col-md-7">
                                    <label class="form-label small fw-semibold text-secondary">2a. Nomor Surat Tugas</label>
                                    <input type="text" id="edit_st_number" name="st_number" class="form-control" oninput="syncEditBasisSt()">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small fw-semibold text-secondary">2b. Tanggal Surat Tugas</label>
                                    <input type="date" id="edit_st_date" name="st_date" class="form-control" oninput="syncEditBasisSt()">
                                </div>
                            </div>
                            <div>
                                <label class="form-label small fw-semibold text-secondary">2. Teks Lengkap Surat Tugas Kepala BPS</label>
                                <textarea id="edit_basis_st" name="basis_st" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Bagian C & D: Tujuan & Waktu Perjalanan Dinas -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2 px-3 fw-bold text-dark small">
                            <i class="fa-solid fa-map-location-dot text-primary me-1"></i> C &amp; D. TUJUAN &amp; WAKTU PERJALANAN DINAS
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label class="form-label small fw-semibold text-secondary">Judul Kegiatan / Agenda <span class="text-danger">*</span></label>
                                    <input type="text" id="edit_title" name="activity_title" class="form-control" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small fw-semibold text-secondary">Penugasan Terkait</label>
                                    <select id="edit_assignment" name="assignment_id" class="form-select" onchange="autoFillLocationAndExpense(this, 'edit_activity_location', 'edit_expense_list')">
                                        <option value="" data-location="">-- Kegiatan Mandiri / Umum --</option>
                                        @foreach($myAssignments as $asgn)
                                            <option value="{{ $asgn->id }}" data-location="{{ $asgn->location }}">{{ $asgn->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-secondary">
                                        <i class="fa-solid fa-location-dot text-primary me-1"></i> Lokasi / Tempat Pelaksanaan &amp; Asal Keberangkatan
                                    </label>
                                    <input type="text" id="edit_activity_location" name="activity_location" class="form-control" placeholder="Contoh: Kecamatan Batu Putih" oninput="syncDefaultExpenseDesc('edit_activity_location', 'edit_expense_list')">
                                    <div class="form-text small">Digunakan pada tujuan perjalanan dinas, halaman Dokumentasi, dan rincian asal biaya Daftar Pengeluaran Riil.</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">C. Tujuan Perjalanan Dinas (Teks Lengkap SPD)</label>
                                <textarea id="edit_destination_purpose" name="destination_purpose" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">D. Durasi / Lama Waktu Perjalanan</label>
                                    <input type="text" id="edit_duration_text" name="duration_text" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Tanggal Pelaksanaan Kegiatan <span class="text-danger">*</span></label>
                                    <input type="date" id="edit_date" name="activity_date" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bagian E & F: Hasil Perjalanan Dinas, Kendala, Solusi & Saran -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2 px-3 fw-bold text-dark small">
                            <i class="fa-solid fa-list-check text-primary me-1"></i> E &amp; F. HASIL PERJALANAN DINAS, KENDALA, SOLUSI &amp; SARAN
                        </div>
                        <div class="card-body p-3">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">E. Uraian Hasil Perjalanan Dinas <span class="text-danger">*</span></label>
                                <textarea id="edit_description" name="work_description" class="form-control" rows="4" required></textarea>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Kendala (Opsional)</label>
                                    <textarea id="edit_obstacles" name="obstacles" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Solusi (Opsional)</label>
                                    <textarea id="edit_solutions" name="solutions" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div>
                                <label class="form-label small fw-semibold text-secondary">F. Saran dan Rekomendasi / Tindak Lanjut</label>
                                <textarea id="edit_recommendations" name="recommendations" class="form-control" rows="2">-</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- DOKUMEN 2: DAFTAR PENGELUARAN RIIL -->
                    <div class="card mb-3 border border-success">
                        <div class="card-header bg-success-subtle text-success-emphasis py-2 px-3 fw-bold small d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-receipt me-1"></i> DAFTAR PENGELUARAN RIIL (DIGABUNG OTOMATIS KE 1 PDF)</span>
                            <button type="button" class="btn btn-xs btn-success py-0.5 px-2" style="font-size: 0.72rem;" onclick="addExpenseRow('edit_expense_list', 'edit_expense_total', 'edit_expense_terbilang')">
                                <i class="fa-solid fa-plus me-1"></i> Tambah Baris Biaya
                            </button>
                        </div>
                        <div class="card-body p-3">
                            <div class="table-responsive mb-2">
                                <table class="table table-bordered table-sm align-middle mb-0">
                                    <thead class="table-light text-center small">
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th>Perincian Biaya</th>
                                            <th style="width: 180px;">Jumlah (Rp)</th>
                                            <th style="width: 45px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="edit_expense_list"></tbody>
                                    <tfoot>
                                        <tr class="table-light">
                                            <th colspan="2" class="text-center small">
                                                JUMLAH (<span id="edit_expense_terbilang" class="fst-italic fw-semibold text-success">Nol Rupiah</span>)
                                            </th>
                                            <th class="text-end font-monospace text-dark" id="edit_expense_total">Rp. 0,-</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="row g-2 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Pejabat Pembuat Komitmen (Menyetujui)</label>
                                    <input type="text" id="edit_ppk_name" name="ppk_name" class="form-control form-control-sm" value="Hidayatullah, SE">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1">NIP Pejabat Pembuat Komitmen</label>
                                    <input type="text" id="edit_ppk_nip" name="ppk_nip" class="form-control form-control-sm font-monospace" value="198510292011011009">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tanda Tangan & Penandatanganan -->
                    <div class="card mb-3 border border-warning">
                        <div class="card-header bg-warning text-dark py-2 px-3 fw-bold small d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-signature me-1"></i> TANDA TANGAN DIGITAL (1x INPUT UNTUK KEDUA DOKUMEN)</span>
                            <span class="badge bg-dark text-white">Muncul di Perjadin &amp; Pengeluaran Riil</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Kota Tempat Penandatanganan</label>
                                    <input type="text" id="edit_signing_city" name="signing_city" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Tanggal Tanda Tangan</label>
                                    <input type="date" id="edit_signing_date" name="signing_date" class="form-control">
                                </div>
                            </div>

                            <!-- Preview Tanda Tangan Saat Ini jika ada -->
                            <div id="edit_current_sig_container" class="mb-3 d-none">
                                <label class="form-label small fw-semibold text-muted">Tanda Tangan Saat Ini:</label>
                                <div class="p-2 border rounded bg-light text-center d-inline-block">
                                    <img id="edit_current_sig_img" src="" style="max-height: 60px; max-width: 160px;" alt="Tanda Tangan">
                                </div>
                                <div class="small text-muted mt-1 fst-italic">Abaikan goresan di bawah jika tidak ingin mengganti tanda tangan ini.</div>
                            </div>

                            <label class="form-label small fw-semibold text-dark mb-1">
                                <i class="fa-solid fa-pen-nib text-primary me-1"></i> Perbarui Tanda Tangan (Opsional):
                            </label>

                            <!-- Tab Pilihan Tanda Tangan Edit -->
                            <ul class="nav nav-pills nav-fill mb-2" id="editSigTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active py-1 px-3 small" id="edit-draw-tab" data-bs-toggle="pill" data-bs-target="#edit-draw-pane" type="button" role="tab">
                                        <i class="fa-solid fa-pen-nib me-1"></i> Gores Ulang Tanda Tangan
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-1 px-3 small" id="edit-upload-tab" data-bs-toggle="pill" data-bs-target="#edit-upload-pane" type="button" role="tab">
                                        <i class="fa-solid fa-upload me-1"></i> Unggah File Gambar Baru
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="editSigTabContent">
                                <!-- Gores Langsung Edit -->
                                <div class="tab-pane fade show active" id="edit-draw-pane" role="tabpanel">
                                    <div class="p-2 bg-light border rounded text-center">
                                        <canvas id="edit_sig_canvas" class="border bg-white rounded shadow-sm" width="500" height="160" style="touch-action: none; cursor: crosshair; max-width: 100%; display: inline-block;"></canvas>
                                        <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                                            <small class="text-muted"><i class="fa-solid fa-hand-pointer me-1"></i> Goreskan tanda tangan baru jika ingin mengganti</small>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearEditCanvas()">
                                                <i class="fa-solid fa-rotate-left me-1"></i> Hapus / Ulangi
                                            </button>
                                        </div>
                                        <input type="hidden" name="signature_data" id="edit_signature_data">
                                    </div>
                                </div>
                                <!-- Upload File Edit -->
                                <div class="tab-pane fade" id="edit-upload-pane" role="tabpanel">
                                    <div class="p-3 bg-light border rounded">
                                        <input type="file" name="signature_file" class="form-control" accept="image/png,image/jpeg">
                                        <div class="form-text small mt-1">Pilih gambar scan tanda tangan baru jika ingin mengganti.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Opsi Surat Pernyataan Tidak Menggunakan Kendaraan Dinas -->
                    <div class="card mb-3 border border-info bg-info bg-opacity-10">
                        <div class="card-body p-3">
                            <div class="form-check form-switch mb-1">
                                <input class="form-check-input" type="checkbox" name="include_vehicle_statement" value="1" id="edit_vehicle_stmt">
                                <label class="form-check-label fw-bold text-dark" for="edit_vehicle_stmt">
                                    <i class="fa-solid fa-file-contract text-primary me-1"></i> Sertakan Surat Pernyataan Tidak Menggunakan Kendaraan Dinas
                                </label>
                            </div>
                            <div class="small text-muted ms-4">
                                Otomatis menghasilkan lampiran resmi Surat Pernyataan Tidak Menggunakan Kendaraan Dinas bertanda tangan pelaksana untuk klaim biaya transpor BPS Kolaka Utara.
                            </div>
                        </div>
                    </div>

                    <!-- Lampiran Foto Dokumentasi -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2 px-3 fw-bold text-dark small">
                            <i class="fa-solid fa-camera text-primary me-1"></i> TAMBAH FOTO DOKUMENTASI BARU
                        </div>
                        <div class="card-body p-3">
                            <input type="file" name="photos[]" class="form-control" multiple accept="image/*,.pdf">
                            <div class="form-text small">Pilih berkas baru jika ingin menambahkan dokumentasi kegiatan.</div>
                        </div>
                    </div>

                    <!-- Tindakan Penyimpanan -->
                    <div class="p-3 bg-light rounded border">
                        <label class="form-label small fw-semibold text-secondary mb-2">TINDAKAN PENYIMPANAN:</label>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="action_type" id="editActionSubmit" value="submitted" checked>
                                <label class="form-check-label fw-bold text-primary" for="editActionSubmit">
                                    <i class="fa-solid fa-paper-plane me-1"></i> Ajukan ke Admin untuk Diperiksa Ulang
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="action_type" id="editActionDraft" value="draft">
                                <label class="form-check-label text-muted" for="editActionDraft">
                                    <i class="fa-regular fa-bookmark me-1"></i> Simpan Sebagai Draft Sementara
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary">
                        <i class="fa-solid fa-check me-1"></i> Simpan Perubahan Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL DETAIL & PREVIEW DOKUMEN LAPORAN RESMI BPS
     ============================================================== -->
<div class="modal fade" id="detailReportModal" tabindex="-1" aria-labelledby="detailReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="modal-title fw-bold text-dark mb-0" id="detailReportModalLabel">
                        <i class="fa-solid fa-file-lines text-info me-2"></i> Pratinjau Dokumen Laporan Perjalanan Dinas
                    </h5>
                    <span id="det_status_badge" class="badge"></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <!-- Catatan Evaluasi Admin jika ada -->
                <div id="det_admin_notes_box" class="alert alert-warning border-warning p-3 mb-3 d-none">
                    <div class="fw-bold small text-dark mb-1">
                        <i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> Catatan Evaluasi Administrator:
                    </div>
                    <div id="det_admin_notes_text" class="small text-secondary"></div>
                </div>

                <!-- Simulation of Official Document Paper -->
                <div class="bg-white p-4 p-md-5 rounded shadow-sm border mx-auto mb-4" style="max-width: 820px; font-family: Arial, Helvetica, sans-serif; color: #000; line-height: 1.45;">
                    <!-- KOP SURAT BPS -->
                    <div class="d-flex align-items-center pb-2 border-bottom border-dark border-2 mb-3">
                        <div style="width: 85px;" class="me-3 text-center">
                            <img src="{{ asset('images/logo.png') }}" style="width: 70px; height: auto;" alt="Logo BPS" onerror="this.style.display='none'">
                        </div>
                        <div class="flex-grow-1 text-center">
                            <div class="fw-bold text-uppercase" style="font-size: 13pt; color: #2b3990; letter-spacing: 0.5px;">BADAN PUSAT STATISTIK</div>
                            <div class="fw-bold text-uppercase" style="font-size: 13pt; color: #333; letter-spacing: 0.5px;">KABUPATEN KOLAKA UTARA</div>
                            <div style="font-size: 8pt; color: #444; line-height: 1.35;">
                                Jalan Protokol Poros DPRD Lasusua 93911 Telp. (0405) 2330029; Fax. (0405) 2330029,<br>
                                Homepage: http://kolutkab.bps.go.id E-mail: bps7408@bps.go.id
                            </div>
                        </div>
                    </div>

                    <!-- JUDUL DOKUMEN -->
                    <div class="text-center my-3">
                        <h5 class="fw-bold text-decoration-underline mb-0 text-dark" style="font-size: 12pt;">Laporan Perjalanan Dinas</h5>
                    </div>

                    <!-- A. YANG MELAKUKAN PERJALANAN DINAS -->
                    <div class="fw-bold mb-1 mt-3" style="font-size: 10.5pt;">A. Yang Melakukan Perjalanan Dinas</div>
                    <table class="table table-borderless table-sm mb-2 ms-3" style="font-size: 10.5pt; width: auto;">
                        <tr>
                            <td style="width: 170px; padding: 2px 0;">Nama</td>
                            <td style="width: 15px; padding: 2px 0;">:</td>
                            <td class="fw-semibold text-dark" id="det_officer_name" style="padding: 2px 0;">-</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;" id="det_label_id">NIK</td>
                            <td style="padding: 2px 0;">:</td>
                            <td id="det_officer_nip" style="padding: 2px 0;">-</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">Pangkat/Golongan</td>
                            <td style="padding: 2px 0;">:</td>
                            <td id="det_officer_rank" style="padding: 2px 0;">-</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">Jabatan</td>
                            <td style="padding: 2px 0;">:</td>
                            <td id="det_officer_position" style="padding: 2px 0;">-</td>
                        </tr>
                    </table>

                    <!-- B. DASAR PELAKSANAAN -->
                    <div class="fw-bold mb-1 mt-2" style="font-size: 10.5pt;">B. Dasar Pelaksanaan</div>
                    <ol class="ms-3 mb-2" style="font-size: 10.5pt; padding-left: 20px;">
                        <li id="det_basis_dipa" class="mb-1 text-justify">-</li>
                        <li id="det_basis_st" class="text-justify">-</li>
                    </ol>

                    <!-- C. TUJUAN PERJALANAN DINAS -->
                    <div class="fw-bold mb-1 mt-2" style="font-size: 10.5pt;">C. Tujuan Perjalanan Dinas</div>
                    <div id="det_destination_purpose" class="ms-3 mb-2 text-justify" style="font-size: 10.5pt;">-</div>

                    <!-- D. WAKTU PERJALANAN DINAS -->
                    <div class="fw-bold mb-1 mt-2" style="font-size: 10.5pt;">D. Waktu Perjalanan Dinas</div>
                    <div id="det_duration_info" class="ms-3 mb-2" style="font-size: 10.5pt;">-</div>

                    <!-- E. HASIL PERJALANAN DINAS -->
                    <div class="fw-bold mb-1 mt-2" style="font-size: 10.5pt;">E. Hasil Perjalanan Dinas</div>
                    <div id="det_work_description" class="ms-3 mb-2 text-justify p-2 bg-light rounded" style="font-size: 10.5pt; white-space: pre-line;">-</div>
                    <div id="det_obstacles_box" class="ms-3 mb-2 d-none" style="font-size: 10.5pt;">
                        <div class="fw-bold">Kendala :</div>
                        <div id="det_obstacles" class="ms-2" style="white-space: pre-line;"></div>
                    </div>
                    <div id="det_solutions_box" class="ms-3 mb-3 d-none" style="font-size: 10.5pt;">
                        <div class="fw-bold">Solusi :</div>
                        <div id="det_solutions" class="ms-2" style="white-space: pre-line;"></div>
                    </div>

                    <!-- F. SARAN DAN REKOMENDASI / TINDAK LANJUT -->
                    <div class="fw-bold mb-1 mt-2" style="font-size: 10.5pt;">F. Saran dan Rekomendasi / Tindak Lanjut</div>
                    <div id="det_recommendations" class="ms-3 mb-3 text-justify" style="font-size: 10.5pt; white-space: pre-line;">-</div>

                    <!-- TANDA TANGAN -->
                    <div class="row mt-4 pt-2">
                        <div class="col-6"></div>
                        <div class="col-6 text-center" style="font-size: 10.5pt;">
                            <div id="det_signing_place_date">Lasusua, -</div>
                            <div>Pelaksana Perjalanan Dinas,</div>
                            <div id="det_sign_img_container" style="min-height: 65px; display: flex; align-items: center; justify-content: center; margin: 4px 0;">
                                <!-- Signature img or space -->
                            </div>
                            <div class="fw-bold text-dark" id="det_sign_name">-</div>
                            <div style="font-size: 9.5pt;" id="det_sign_nip"></div>
                        </div>
                    </div>

                    <!-- FOTO DOKUMENTASI -->
                    <div class="border-top pt-3 mt-4">
                        <div class="fw-bold small text-secondary mb-2">
                            <i class="fa-solid fa-camera text-primary me-1"></i> DOKUMENTASI KEGIATAN:
                        </div>
                        <div id="det_attachments_container" class="row g-2"></div>
                    </div>
                </div>

                <!-- Simulation of Document 2: Daftar Pengeluaran Riil (Tanpa Kop Surat) -->
                <div id="det_dpr_paper" class="bg-white p-4 p-md-5 rounded shadow-sm border mx-auto mb-4" style="max-width: 820px; font-family: Arial, Helvetica, sans-serif; color: #000; line-height: 1.45;">
                    <div class="text-center mb-4">
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 12pt;">Daftar Pengeluaran Riil</h5>
                    </div>

                    <table class="table table-borderless table-sm mb-3" style="font-size: 10.5pt; width: auto;">
                        <tr>
                            <td style="width: 210px; padding: 2px 0;">Yang bertanda tangan dibawah ini</td>
                            <td style="width: 15px; padding: 2px 0;">:</td>
                            <td></td>
                        </tr>
                        <tr><td colspan="3" style="height: 6px; padding: 0;"></td></tr>
                        <tr>
                            <td style="padding: 2px 0;">Nama</td>
                            <td style="padding: 2px 0;">:</td>
                            <td class="fw-semibold text-dark" id="dpr_officer_name" style="padding: 2px 0;">-</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">NIK</td>
                            <td style="padding: 2px 0;">:</td>
                            <td id="dpr_officer_nik" style="padding: 2px 0;">-</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">Jabatan</td>
                            <td style="padding: 2px 0;">:</td>
                            <td id="dpr_officer_position" style="padding: 2px 0;">-</td>
                        </tr>
                    </table>

                    <p style="text-align: justify; font-size: 10.5pt; margin-bottom: 14px;">
                        berdasarkan <span id="dpr_st_text">Surat Tugas</span>, dengan ini kami menyatakan dengan sesungguhnya bahwa :
                    </p>

                    <div style="font-size: 10.5pt; margin-bottom: 10px;">
                        1.&nbsp;&nbsp;Biaya transport pegawai di bawah ini yang tidak dapat diperoleh bukti-bukti pengeluarannya, meliputi:
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered border-dark table-sm mb-0" style="font-size: 10pt; border-color: #000 !important;">
                            <thead class="text-center align-middle" style="border-color: #000;">
                                <tr>
                                    <th style="width: 45px; font-weight: normal; border-color: #000;">No</th>
                                    <th style="font-weight: normal; border-color: #000;">Perincian Biaya</th>
                                    <th style="width: 165px; font-weight: normal; border-color: #000;">Jumlah (Rp)</th>
                                </tr>
                                <tr>
                                    <td style="font-size: 9pt; border-color: #000; padding: 2px;">(1)</td>
                                    <td style="font-size: 9pt; border-color: #000; padding: 2px;">(2)</td>
                                    <td style="font-size: 9pt; border-color: #000; padding: 2px;">(3)</td>
                                </tr>
                            </thead>
                            <tbody id="dpr_expense_tbody" style="border-color: #000;"></tbody>
                            <tfoot style="border-color: #000;">
                                <tr>
                                    <td colspan="2" class="text-center fw-bold" style="border-color: #000;">JUMLAH</td>
                                    <td rowspan="2" id="dpr_total_amount" class="text-center align-middle fw-bold" style="border-color: #000;">Rp. 0,-</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-center fw-bold fst-italic" style="border-color: #000;">
                                        (<span id="dpr_total_terbilang">Nol Rupiah</span>)
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div style="font-size: 10.5pt; text-align: justify; margin-bottom: 12px;">
                        2.&nbsp;&nbsp;Jumlah uang tersebut pada angka 1 diatas benar-benar dikeluarkan untuk pelaksanaan perjalanan dinas dimaksud dan apabila di kemudian hari terdapat kelebihan atas pembayaran, kami bersedia untuk menyetorkan kelebihan tersebut ke Kas Negara.
                    </div>

                    <p style="font-size: 10.5pt; margin-left: 22px; margin-bottom: 24px;">
                        Demikian pernyataan ini kami buat dengan sebenarnya, untuk dipergunakan sebagaimana mestinya.
                    </p>

                    <div class="row mt-4" style="font-size: 10.5pt;">
                        <div class="col-6 text-center">
                            <div>Mengetahui/Menyetujui:</div>
                            <div>Pejabat Pembuat Komitmen</div>
                            <div>BPS Kabupaten Kolaka Utara</div>
                            <div style="height: 65px;"></div>
                            <div class="fw-bold text-decoration-underline text-dark" id="dpr_ppk_name">Hidayatullah, SE</div>
                            <div>NIP. <span id="dpr_ppk_nip">198510292011011009</span></div>
                        </div>
                        <div class="col-6 text-center">
                            <div id="dpr_signing_place_date">Lasusua, -</div>
                            <div>Pegawai</div>
                            <div>Yang melakukan perjalanan dinas,</div>
                            <div id="dpr_sign_img_container" style="min-height: 65px; display: flex; align-items: center; justify-content: center; margin: 4px 0;">
                                <!-- Signature img -->
                            </div>
                            <div class="fw-bold text-decoration-underline text-dark" id="dpr_sign_name">-</div>
                            <div id="dpr_sign_nip">-</div>
                        </div>
                    </div>
                </div>

                <!-- Simulation of Surat Pernyataan Paper -->
                <div id="det_vehicle_stmt_paper" class="bg-white p-4 p-md-5 rounded shadow-sm border mx-auto d-none" style="max-width: 820px; font-family: Arial, Helvetica, sans-serif; color: #000; line-height: 1.45;">
                    <!-- KOP SURAT BPS -->
                    <div class="d-flex align-items-center pb-2 border-bottom border-dark border-2 mb-3">
                        <div style="width: 85px;" class="me-3 text-center">
                            <img src="{{ asset('images/logo.png') }}" style="width: 70px; height: auto;" alt="Logo BPS" onerror="this.style.display='none'">
                        </div>
                        <div class="flex-grow-1 text-center">
                            <div class="fw-bold text-uppercase" style="font-size: 13pt; color: #2b3990; letter-spacing: 0.5px;">BADAN PUSAT STATISTIK</div>
                            <div class="fw-bold text-uppercase" style="font-size: 13pt; color: #333; letter-spacing: 0.5px;">KABUPATEN KOLAKA UTARA</div>
                            <div style="font-size: 8pt; color: #444; line-height: 1.35;">
                                Jalan Protokol Poros DPRD Lasusua 93911 Telp. (0405) 2330029; Fax. (0405) 2330029,<br>
                                Homepage: http://kolutkab.bps.go.id E-mail: bps7408@bps.go.id
                            </div>
                        </div>
                    </div>

                    <div class="text-center my-3">
                        <h6 class="fw-bold mb-0" style="font-size: 11pt;">SURAT PERNYATAAN</h6>
                        <h6 class="fw-bold text-decoration-underline mb-0" style="font-size: 11pt;">TIDAK MENGGUNAKAN KENDARAAN DINAS</h6>
                    </div>

                    <div class="mb-2" style="font-size: 10.5pt;">Yang bertanda tangan di bawah ini:</div>
                    <table class="table table-borderless table-sm mb-3 ms-3" style="font-size: 10.5pt; width: auto;">
                        <tr>
                            <td style="width: 170px; padding: 2px 0;">Nama</td>
                            <td style="width: 15px; padding: 2px 0;">:</td>
                            <td class="fw-semibold text-dark" id="stmt_officer_name">-</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">NIP/NIK</td>
                            <td style="padding: 2px 0;">:</td>
                            <td id="stmt_officer_nip">-</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">Pangkat/Golongan</td>
                            <td style="padding: 2px 0;">:</td>
                            <td id="stmt_officer_rank">-</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">Jabatan</td>
                            <td style="padding: 2px 0;">:</td>
                            <td id="stmt_officer_position">-</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">Unit Kerja</td>
                            <td style="padding: 2px 0;">:</td>
                            <td>BPS Kabupaten Kolaka Utara</td>
                        </tr>
                    </table>

                    <p style="text-align: justify; font-size: 10.5pt; line-height: 1.5; margin-bottom: 12px;" id="stmt_body_text">
                        Menerangkan bahwa dalam rangka pelaksanaan tugas perjalanan dinas biasa dalam kota, saya benar-benar tidak menggunakan kendaraan dinas.
                    </p>

                    <p style="text-align: justify; font-size: 10.5pt; line-height: 1.5; margin-bottom: 25px;">
                        Demikian pernyataan ini kami buat dengan sebenar-benarnya untuk dipergunakan sebagaimana mestinya. Apabila terdapat kekeliruan dalam pertanggungjawaban SPD dan mengakibatkan kerugian negara, kami bersedia dituntut sesuai peraturan yang berlaku dan mengembalikan biaya transpor yang sudah terlanjur saya terima ke kas negara.
                    </p>

                    <div class="row mt-4 pt-2">
                        <div class="col-6"></div>
                        <div class="col-6 text-center" style="font-size: 10.5pt;">
                            <div id="stmt_signing_place_date">Lasusua, -</div>
                            <div>Pelaksana Perjalanan Dinas,</div>
                            <div id="stmt_sign_img_container" style="min-height: 65px; display: flex; align-items: center; justify-content: center; margin: 4px 0;">
                                <!-- Signature img or space -->
                            </div>
                            <div class="fw-bold text-dark" id="stmt_sign_name">-</div>
                            <div style="font-size: 9.5pt;" id="stmt_sign_nip"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <div class="d-flex gap-2">
                    <a id="det_preview_link" href="#" target="_blank" class="btn btn-outline-primary">
                        <i class="fa-solid fa-print me-1"></i> Cetak / Preview PDF
                    </a>
                    <a id="det_download_link" href="#" class="btn btn-sp-primary">
                        <i class="fa-solid fa-file-pdf me-1"></i> Unduh PDF Resmi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let createSigPad = null;
    let editSigPad = null;

    function setupSignatureCanvas(canvasId, hiddenInputId) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return null;
        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let hasDrawn = false;

        ctx.lineWidth = 2.5;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.strokeStyle = '#0d2142';

        function getCoords(e) {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            return {
                x: (clientX - rect.left) * scaleX,
                y: (clientY - rect.top) * scaleY
            };
        }

        function startDraw(e) {
            isDrawing = true;
            hasDrawn = true;
            const pos = getCoords(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
            if (e.cancelable && e.type.startsWith('touch')) e.preventDefault();
        }

        function draw(e) {
            if (!isDrawing) return;
            const pos = getCoords(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            if (e.cancelable && e.type.startsWith('touch')) e.preventDefault();
        }

        function stopDraw() {
            if (!isDrawing) return;
            isDrawing = false;
            ctx.closePath();
            const hiddenInput = document.getElementById(hiddenInputId);
            if (hiddenInput && hasDrawn) {
                hiddenInput.value = canvas.toDataURL('image/png');
            }
        }

        // Mouse events
        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDraw);
        canvas.addEventListener('mouseleave', stopDraw);

        // Touch events
        canvas.addEventListener('touchstart', startDraw, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        canvas.addEventListener('touchend', stopDraw);

        return {
            clear: function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                hasDrawn = false;
                const hiddenInput = document.getElementById(hiddenInputId);
                if (hiddenInput) hiddenInput.value = '';
            },
            hasDrawn: function() { return hasDrawn; },
            toDataURL: function() { return canvas.toDataURL('image/png'); }
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        createSigPad = setupSignatureCanvas('create_sig_canvas', 'create_signature_data');
        editSigPad = setupSignatureCanvas('edit_sig_canvas', 'edit_signature_data');

        // Form submit handlers to guarantee signature sync
        const createForm = document.getElementById('createReportForm');
        if (createForm) {
            createForm.addEventListener('submit', function() {
                if (createSigPad && createSigPad.hasDrawn()) {
                    document.getElementById('create_signature_data').value = createSigPad.toDataURL();
                }
            });
        }

        const editForm = document.getElementById('editReportForm');
        if (editForm) {
            editForm.addEventListener('submit', function() {
                if (editSigPad && editSigPad.hasDrawn()) {
                    document.getElementById('edit_signature_data').value = editSigPad.toDataURL();
                }
            });
        }
    });

    const ID_MONTHS = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    function formatIndonesianDate(dateStr) {
        if (!dateStr) return '-';
        const clean = dateStr.substring(0, 10);
        const parts = clean.split('-');
        if (parts.length !== 3) return dateStr;
        const year = parts[0];
        const monthIdx = parseInt(parts[1], 10) - 1;
        const day = parseInt(parts[2], 10);
        if (isNaN(day) || monthIdx < 0 || monthIdx > 11) return dateStr;
        return day + ' ' + ID_MONTHS[monthIdx] + ' ' + year;
    }

    function formatRupiah(angka) {
        const num = Math.max(0, Math.round(Number(angka) || 0));
        return 'Rp. ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ',-';
    }

    function terbilangInt(angka) {
        const n = Math.floor(Math.abs(Number(angka) || 0));
        const words = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        if (n < 12) return words[n];
        if (n < 20) return terbilangInt(n - 10) + ' Belas';
        if (n < 100) return (terbilangInt(Math.floor(n / 10)) + ' Puluh ' + terbilangInt(n % 10)).trim();
        if (n < 200) return ('Seratus ' + terbilangInt(n - 100)).trim();
        if (n < 1000) return (terbilangInt(Math.floor(n / 100)) + ' Ratus ' + terbilangInt(n % 100)).trim();
        if (n < 2000) return ('Seribu ' + terbilangInt(n - 1000)).trim();
        if (n < 1000000) return (terbilangInt(Math.floor(n / 1000)) + ' Ribu ' + terbilangInt(n % 1000)).trim();
        if (n < 1000000000) return (terbilangInt(Math.floor(n / 1000000)) + ' Juta ' + terbilangInt(n % 1000000)).trim();
        if (n < 1000000000000) return (terbilangInt(Math.floor(n / 1000000000)) + ' Miliar ' + terbilangInt(n % 1000000000)).trim();
        return n.toString();
    }

    function terbilangRupiah(angka) {
        const n = Math.round(Number(angka) || 0);
        if (n <= 0) return 'Nol Rupiah';
        return terbilangInt(n).replace(/\s+/g, ' ').trim() + ' Rupiah';
    }

    function syncBasisSt(numInputId, dateInputId, targetTextareaId) {
        const numEl = document.getElementById(numInputId);
        const dateEl = document.getElementById(dateInputId);
        const outEl = document.getElementById(targetTextareaId);
        if (!outEl) return;

        const num = (numEl && numEl.value.trim()) ? numEl.value.trim() : '-';
        const dateFormatted = (dateEl && dateEl.value) ? formatIndonesianDate(dateEl.value) : '-';
        outEl.value = `Surat Tugas Kepala Badan Pusat Statistik Kabupaten Kolaka Utara Nomor ${num} Tanggal ${dateFormatted}.`;
    }

    function syncCreateBasisSt() {
        syncBasisSt('create_st_number', 'create_st_date', 'create_basis_st');
    }

    function syncEditBasisSt() {
        syncBasisSt('edit_st_number', 'edit_st_date', 'edit_basis_st');
    }

    function buildDefaultExpenseDesc(location) {
        const origin = (location && location.trim()) ? location.trim() : 'Wilayah Tugas';
        return `Biaya transport dari ${origin} ke Kantor BPS Kabupaten Kolaka Utara (P.P.) selama 1 hari`;
    }

    function syncDefaultExpenseDesc(locInputId, tbodyId) {
        const locEl = document.getElementById(locInputId);
        const tbody = document.getElementById(tbodyId);
        if (!locEl || !tbody) return;
        const firstDesc = tbody.querySelector('tr:first-child .expense-desc-input');
        if (!firstDesc) return;
        const val = firstDesc.value.trim();
        if (!val || (val.startsWith('Biaya transport dari ') && val.includes('ke Kantor BPS Kabupaten Kolaka Utara'))) {
            firstDesc.value = buildDefaultExpenseDesc(locEl.value);
        }
    }

    function autoFillLocation(selectElem, targetInputId) {
        const selectedOpt = selectElem.options[selectElem.selectedIndex];
        if (selectedOpt) {
            const loc = selectedOpt.getAttribute('data-location');
            const targetInput = document.getElementById(targetInputId);
            if (targetInput && loc) {
                targetInput.value = loc;
            }
        }
    }

    function autoFillLocationAndExpense(selectElem, targetInputId, tbodyId) {
        autoFillLocation(selectElem, targetInputId);
        syncDefaultExpenseDesc(targetInputId, tbodyId);
    }

    function renumberExpenseRows(tbodyId) {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;
        tbody.querySelectorAll('tr').forEach((tr, idx) => {
            const noCell = tr.querySelector('td:first-child');
            if (noCell) noCell.textContent = idx + 1;
        });
    }

    function addExpenseRow(tbodyId, totalId, terbilangId, desc = '', amount = 0) {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;
        const rowNum = tbody.querySelectorAll('tr').length + 1;

        const tr = document.createElement('tr');
        const tdNo = document.createElement('td');
        tdNo.className = 'text-center small fw-bold';
        tdNo.textContent = rowNum;

        const tdDesc = document.createElement('td');
        const inpDesc = document.createElement('input');
        inpDesc.type = 'text';
        inpDesc.name = 'expense_descriptions[]';
        inpDesc.className = 'form-control form-control-sm expense-desc-input';
        inpDesc.value = desc;
        inpDesc.placeholder = 'Contoh: Biaya transport dari Kantor ke lokasi kegiatan (P.P.) selama 1 hari';
        inpDesc.required = true;
        tdDesc.appendChild(inpDesc);

        const tdAmt = document.createElement('td');
        const inpAmt = document.createElement('input');
        inpAmt.type = 'number';
        inpAmt.name = 'expense_amounts[]';
        inpAmt.className = 'form-control form-control-sm text-end fw-semibold expense-amt-input';
        inpAmt.min = '0';
        inpAmt.value = Number(amount) || 0;
        inpAmt.required = true;
        inpAmt.addEventListener('input', function() {
            recalcExpenses(tbodyId, totalId, terbilangId);
        });
        tdAmt.appendChild(inpAmt);

        const tdAct = document.createElement('td');
        tdAct.className = 'text-center';
        const btnDel = document.createElement('button');
        btnDel.type = 'button';
        btnDel.className = 'btn btn-sm btn-outline-danger py-0 px-1.5';
        btnDel.title = 'Hapus baris';
        btnDel.innerHTML = '<i class="fa-solid fa-xmark"></i>';
        btnDel.addEventListener('click', function() {
            removeExpenseRow(btnDel, tbodyId, totalId, terbilangId);
        });
        tdAct.appendChild(btnDel);

        tr.appendChild(tdNo);
        tr.appendChild(tdDesc);
        tr.appendChild(tdAmt);
        tr.appendChild(tdAct);
        tbody.appendChild(tr);

        recalcExpenses(tbodyId, totalId, terbilangId);
    }

    function removeExpenseRow(btn, tbodyId, totalId, terbilangId) {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;
        const rows = tbody.querySelectorAll('tr');
        if (rows.length <= 1) {
            const amt = rows[0].querySelector('.expense-amt-input');
            if (amt) amt.value = 0;
        } else {
            const tr = btn.closest('tr');
            if (tr) tr.remove();
            renumberExpenseRows(tbodyId);
        }
        recalcExpenses(tbodyId, totalId, terbilangId);
    }

    function recalcExpenses(tbodyId, totalId, terbilangId) {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;
        let total = 0;
        tbody.querySelectorAll('.expense-amt-input').forEach(inp => {
            total += Math.max(0, Math.round(Number(inp.value) || 0));
        });
        const totalEl = document.getElementById(totalId);
        const terbEl = document.getElementById(terbilangId);
        if (totalEl) totalEl.textContent = formatRupiah(total);
        if (terbEl) terbEl.textContent = terbilangRupiah(total);
    }

    function clearCreateCanvas() {
        if (createSigPad) createSigPad.clear();
    }

    function clearEditCanvas() {
        if (editSigPad) editSigPad.clear();
    }

    function buildStPhrase(rep) {
        if (rep.resolved_st_text) return rep.resolved_st_text;
        const actDate = rep.activity_date ? rep.activity_date.substring(0, 10) : '';
        if (rep.st_number) {
            const stDate = rep.st_date ? rep.st_date.substring(0, 10) : actDate;
            return `Surat Tugas Nomor ${rep.st_number} Tanggal ${formatIndonesianDate(stDate)}`;
        }
        const raw = (rep.basis_st || '').trim().replace(/\.$/, '');
        if (!raw) {
            const year = actDate ? actDate.substring(0, 4) : new Date().getFullYear();
            return `Surat Tugas Nomor B-1812/74080/HM.300/${year} Tanggal ${formatIndonesianDate(actDate)}`;
        }
        return raw.replace(/^(Surat Tugas|Berdasarkan Surat Tugas)\s+(Kepala BPS[^N]*?)?(?=Nomor)/i, 'Surat Tugas ');
    }

    function openEditModal(rep) {
        document.getElementById('editReportForm').action = '/petugas/reports/' + rep.id;
        
        // Pelaksana (fallback ke profil user bila kolom kosong)
        document.getElementById('edit_officer_name').value = rep.resolved_officer_name || rep.officer_name || (rep.user ? rep.user.name : '') || '';
        document.getElementById('edit_id_type').value = rep.id_type || 'NIK';
        document.getElementById('edit_officer_nip').value = rep.resolved_officer_nik || rep.officer_nip || (rep.user ? rep.user.nik : '') || '';
        document.getElementById('edit_officer_rank').value = rep.officer_rank || (rep.user ? rep.user.rank : '') || '-';
        document.getElementById('edit_officer_position').value = rep.officer_position || (rep.user ? rep.user.position : '') || 'Mitra BPS Kabupaten Kolaka Utara';

        // Dasar
        document.getElementById('edit_basis_dipa').value = rep.basis_dipa || '';
        document.getElementById('edit_st_number').value = rep.st_number || '';
        document.getElementById('edit_st_date').value = rep.st_date ? rep.st_date.substring(0, 10) : (rep.activity_date ? rep.activity_date.substring(0, 10) : '');
        document.getElementById('edit_basis_st').value = rep.basis_st || '';

        // Tujuan & Waktu
        document.getElementById('edit_title').value = rep.activity_title || '';
        document.getElementById('edit_assignment').value = rep.assignment_id || '';
        const actLoc = rep.activity_location || (rep.assignment ? rep.assignment.location : '') || '';
        document.getElementById('edit_activity_location').value = actLoc;
        document.getElementById('edit_destination_purpose').value = rep.destination_purpose || '';
        document.getElementById('edit_duration_text').value = rep.duration_text || 'Selama 1 (satu) Hari';
        document.getElementById('edit_date').value = rep.activity_date ? rep.activity_date.substring(0, 10) : '';

        // Uraian, Kendala, Solusi & Rekomendasi
        document.getElementById('edit_description').value = rep.work_description || '';
        document.getElementById('edit_obstacles').value = rep.obstacles || '';
        document.getElementById('edit_solutions').value = rep.solutions || '';
        document.getElementById('edit_recommendations').value = rep.recommendations || '-';

        // Dokumen 2: Daftar Pengeluaran Riil
        const editTbody = document.getElementById('edit_expense_list');
        editTbody.innerHTML = '';
        const items = (Array.isArray(rep.resolved_expense_items) && rep.resolved_expense_items.length)
            ? rep.resolved_expense_items
            : (Array.isArray(rep.expense_items) && rep.expense_items.length
                ? rep.expense_items
                : [{ description: buildDefaultExpenseDesc(actLoc), amount: 50000 }]);
        items.forEach(item => {
            addExpenseRow('edit_expense_list', 'edit_expense_total', 'edit_expense_terbilang', item.description || '', Number(item.amount || 0));
        });
        document.getElementById('edit_ppk_name').value = rep.ppk_name || 'Hidayatullah, SE';
        document.getElementById('edit_ppk_nip').value = rep.ppk_nip || '198510292011011009';

        // Penandatanganan
        document.getElementById('edit_signing_city').value = rep.signing_city || 'Lasusua';
        document.getElementById('edit_signing_date').value = rep.signing_date ? rep.signing_date.substring(0, 10) : (rep.activity_date ? rep.activity_date.substring(0, 10) : '');

        // Surat Pernyataan Checkbox (opsional)
        document.getElementById('edit_vehicle_stmt').checked = (rep.include_vehicle_statement === true || rep.include_vehicle_statement === 1);

        // Preview Signature jika sudah ada
        const sigContainer = document.getElementById('edit_current_sig_container');
        const sigImg = document.getElementById('edit_current_sig_img');
        if (rep.signature_path) {
            sigContainer.classList.remove('d-none');
            sigImg.src = '/storage/' + rep.signature_path;
        } else {
            sigContainer.classList.add('d-none');
        }

        // Reset canvas gores
        if (editSigPad) editSigPad.clear();

        const notesBox = document.getElementById('edit_admin_notes_box');
        const notesText = document.getElementById('edit_admin_notes_text');
        if (rep.admin_notes && rep.status === 'revision_needed') {
            notesBox.classList.remove('d-none');
            notesText.innerText = rep.admin_notes;
        } else {
            notesBox.classList.add('d-none');
        }

        const modal = new bootstrap.Modal(document.getElementById('editReportModal'));
        modal.show();
    }

    function openDetailModal(rep) {
        // Badges & Status
        const badge = document.getElementById('det_status_badge');
        badge.className = 'badge ' + (rep.status_badge || 'bg-secondary');
        badge.innerText = rep.status_label || rep.status;

        const officerName = rep.resolved_officer_name || rep.officer_name || (rep.user ? rep.user.name : '-') || '-';
        const officerNik = rep.resolved_officer_nik || rep.officer_nip || (rep.user ? rep.user.nik : '-') || '-';
        const officerRank = rep.officer_rank || (rep.user ? rep.user.rank : '-') || '-';
        const officerPosition = rep.officer_position || (rep.user ? rep.user.position : '') || 'Mitra BPS Kabupaten Kolaka Utara';
        const idType = rep.id_type || 'NIK';

        // Dokumen 1 - Section A
        document.getElementById('det_officer_name').innerText = officerName;
        document.getElementById('det_label_id').innerText = idType;
        document.getElementById('det_officer_nip').innerText = officerNik;
        document.getElementById('det_officer_rank').innerText = officerRank;
        document.getElementById('det_officer_position').innerText = officerPosition;

        // Section B
        const year = rep.activity_date ? new Date(rep.activity_date).getFullYear() : new Date().getFullYear();
        const actDateRaw = rep.activity_date ? rep.activity_date.substring(0, 10) : '';
        const actDateFormatted = actDateRaw ? formatIndonesianDate(actDateRaw) : '-';
        document.getElementById('det_basis_dipa').innerText = rep.basis_dipa || ('DIPA Satuan Kerja Badan Pusat Statistik Kabupaten Kolaka Utara Tahun Anggaran ' + year + ' Nomor SP DIPA-054.01.2.667477/' + year + ' tanggal 1 Desember ' + (year - 1) + '.');
        document.getElementById('det_basis_st').innerText = rep.basis_st || ('Surat Tugas Kepala Badan Pusat Statistik Kabupaten Kolaka Utara Nomor: B-1812/74080/HM.300/' + year + ' Tanggal ' + actDateFormatted + '.');

        // Section C
        document.getElementById('det_destination_purpose').innerText = rep.destination_purpose || ('Perjalanan Dinas Biasa dalam kota lebih dari 8 jam dalam rangka pendataan ' + rep.activity_title + '.');

        // Section D
        document.getElementById('det_duration_info').innerText = (rep.duration_text || 'Selama 1 (satu) Hari') + ', pada tanggal ' + actDateFormatted;

        // Section E & F
        document.getElementById('det_work_description').innerText = rep.work_description || '-';

        const obsBox = document.getElementById('det_obstacles_box');
        if (rep.obstacles && rep.obstacles.trim()) {
            obsBox.classList.remove('d-none');
            document.getElementById('det_obstacles').innerText = rep.obstacles;
        } else {
            obsBox.classList.add('d-none');
        }

        const solBox = document.getElementById('det_solutions_box');
        if (rep.solutions && rep.solutions.trim()) {
            solBox.classList.remove('d-none');
            document.getElementById('det_solutions').innerText = rep.solutions;
        } else {
            solBox.classList.add('d-none');
        }

        document.getElementById('det_recommendations').innerText = rep.recommendations || '-';

        // Signer & Tanda Tangan (1x input tampil di Dokumen 1 & Dokumen 2)
        const signCity = rep.signing_city || 'Lasusua';
        const signDateRaw = rep.signing_date ? rep.signing_date.substring(0, 10) : actDateRaw;
        const signPlaceDate = signCity + ', ' + formatIndonesianDate(signDateRaw);
        const signImgHtml = rep.signature_path
            ? `<img src="/storage/${rep.signature_path}" style="max-height: 60px; max-width: 170px;" alt="Tanda Tangan">`
            : `<div style="height: 55px;"></div>`;

        document.getElementById('det_signing_place_date').innerText = signPlaceDate;
        document.getElementById('det_sign_name').innerText = officerName;
        document.getElementById('det_sign_nip').innerText = (officerNik && officerNik !== '-') ? (idType + '. ' + officerNik) : '';
        document.getElementById('det_sign_img_container').innerHTML = signImgHtml;

        // Dokumen 2 - Daftar Pengeluaran Riil
        document.getElementById('dpr_officer_name').innerText = officerName;
        document.getElementById('dpr_officer_nik').innerText = officerNik;
        document.getElementById('dpr_officer_position').innerText = officerPosition;
        document.getElementById('dpr_st_text').innerText = buildStPhrase(rep);

        const dprTbody = document.getElementById('dpr_expense_tbody');
        dprTbody.innerHTML = '';
        const locFallback = rep.activity_location || (rep.assignment ? rep.assignment.location : '') || '';
        const expenseItems = (Array.isArray(rep.resolved_expense_items) && rep.resolved_expense_items.length)
            ? rep.resolved_expense_items
            : (Array.isArray(rep.expense_items) && rep.expense_items.length
                ? rep.expense_items
                : [{ description: buildDefaultExpenseDesc(locFallback), amount: 50000 }]);

        let totalExpense = 0;
        expenseItems.forEach((item, idx) => {
            const amt = Math.max(0, Math.round(Number(item.amount) || 0));
            totalExpense += amt;
            const tr = document.createElement('tr');

            const tdNo = document.createElement('td');
            tdNo.className = 'text-center';
            tdNo.style.borderColor = '#000';
            tdNo.textContent = idx + 1;

            const tdDesc = document.createElement('td');
            tdDesc.style.borderColor = '#000';
            tdDesc.textContent = item.description || '-';

            const tdAmt = document.createElement('td');
            tdAmt.className = 'text-center';
            tdAmt.style.borderColor = '#000';
            tdAmt.textContent = formatRupiah(amt);

            tr.appendChild(tdNo);
            tr.appendChild(tdDesc);
            tr.appendChild(tdAmt);
            dprTbody.appendChild(tr);
        });

        document.getElementById('dpr_total_amount').innerText = formatRupiah(totalExpense);
        document.getElementById('dpr_total_terbilang').innerText = terbilangRupiah(totalExpense);
        document.getElementById('dpr_ppk_name').innerText = rep.ppk_name || 'Hidayatullah, SE';
        document.getElementById('dpr_ppk_nip').innerText = rep.ppk_nip || '198510292011011009';
        document.getElementById('dpr_signing_place_date').innerText = signPlaceDate;
        document.getElementById('dpr_sign_img_container').innerHTML = signImgHtml;
        document.getElementById('dpr_sign_name').innerText = officerName;
        document.getElementById('dpr_sign_nip').innerText = (officerNik && officerNik !== '-') ? ('NIK/NIP. ' + officerNik) : '';

        // Surat Pernyataan Kendaraan Dinas Preview (opsional)
        const stmtPaper = document.getElementById('det_vehicle_stmt_paper');
        if (rep.include_vehicle_statement === true || rep.include_vehicle_statement === 1) {
            stmtPaper.classList.remove('d-none');
            document.getElementById('stmt_officer_name').innerText = officerName;
            document.getElementById('stmt_officer_nip').innerText = officerNik;
            document.getElementById('stmt_officer_rank').innerText = officerRank;
            document.getElementById('stmt_officer_position').innerText = officerPosition;

            const loc = locFallback || 'Kabupaten Kolaka Utara';
            document.getElementById('stmt_body_text').innerText = `Menerangkan bahwa dalam rangka ${rep.activity_title} di ${loc} sesuai Surat Perjalanan Dinas ${rep.basis_st || 'Nomor Surat Tugas BPS Kolaka Utara'}. Saya benar-benar tidak menggunakan kendaraan dinas pada tanggal ${actDateFormatted}.`;

            document.getElementById('stmt_signing_place_date').innerText = signPlaceDate;
            document.getElementById('stmt_sign_name').innerText = officerName;
            document.getElementById('stmt_sign_nip').innerText = (officerNik && officerNik !== '-') ? (idType + '. ' + officerNik) : '';
            document.getElementById('stmt_sign_img_container').innerHTML = signImgHtml;
        } else {
            stmtPaper.classList.add('d-none');
        }

        // Admin Notes
        const notesBox = document.getElementById('det_admin_notes_box');
        const notesText = document.getElementById('det_admin_notes_text');
        if (rep.admin_notes) {
            notesBox.classList.remove('d-none');
            notesText.innerText = rep.admin_notes;
        } else {
            notesBox.classList.add('d-none');
        }

        // Attachments
        const container = document.getElementById('det_attachments_container');
        if (rep.attachments && rep.attachments.length > 0) {
            let html = '';
            rep.attachments.forEach(att => {
                const isImg = att.file_type && att.file_type.startsWith('image/');
                html += `<div class="col-6 col-md-4">
                            <div class="card h-100 border text-center p-2 shadow-sm">
                                ${isImg 
                                    ? `<a href="/storage/${att.file_path}" target="_blank">
                                         <img src="/storage/${att.file_path}" class="img-fluid rounded mb-2" style="max-height: 110px; object-fit: cover;" alt="${att.file_name}">
                                       </a>` 
                                    : `<div class="py-3"><i class="fa-regular fa-file-pdf fa-3x text-danger"></i></div>`
                                }
                                <div class="small text-truncate" title="${att.file_name}">${att.file_name}</div>
                                <a href="/storage/${att.file_path}" target="_blank" class="btn btn-sm btn-link text-decoration-none mt-1">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka File
                                </a>
                            </div>
                         </div>`;
            });
            container.innerHTML = html;
        } else {
            container.innerHTML = '<div class="col-12 text-muted small fst-italic">Tidak ada lampiran foto dokumentasi.</div>';
        }

        // Set action download / preview links
        document.getElementById('det_preview_link').href = '/petugas/reports/' + rep.id + '/preview';
        document.getElementById('det_download_link').href = '/petugas/reports/' + rep.id + '/download';

        const modal = new bootstrap.Modal(document.getElementById('detailReportModal'));
        modal.show();
    }
</script>
@endpush


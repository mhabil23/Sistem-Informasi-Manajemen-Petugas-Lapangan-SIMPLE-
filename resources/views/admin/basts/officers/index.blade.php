@extends('layouts.app')

@section('title', 'BAST Petugas')
@section('page_title', 'BAST Petugas Lapangan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">BAST Petugas</li>
@endsection

@section('content')
<!-- Header & Quick Actions -->
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h3 class="fw-bold text-dark mb-1">Berita Acara Serah Terima (BAST) Petugas</h3>
        <p class="text-muted small mb-0">Arsip resmi penyerahan hasil tugas dan bundel berkas administrasi dari petugas lapangan kepada koordinator/Administrator.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <button type="button" class="btn btn-sp-primary" data-bs-toggle="modal" data-bs-target="#createOfficerBastModal">
            <i class="fa-solid fa-file-circle-plus me-1"></i> Terbitkan BAST Petugas
        </button>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-solid fa-file-shield"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Total BAST Petugas</div>
                <div class="sp-stat-value">{{ number_format($totalBasts) }}</div>
                <div class="sp-stat-desc">Dokumen sah</div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Bulan Ini</div>
                <div class="sp-stat-value text-success">{{ number_format($thisMonthBasts) }}</div>
                <div class="sp-stat-desc">Arsip bulan ini</div>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="sp-card mb-4">
    <div class="sp-card-header">
        <h5 class="sp-card-title">
            <i class="fa-solid fa-file-shield text-primary"></i>
            Daftar Dokumen BAST Petugas ({{ $basts->total() }})
        </h5>
    </div>
    <div class="sp-card-body p-0">
        <div class="table-responsive">
            <table class="table sp-table align-middle">
                <thead>
                    <tr>
                        <th style="min-width: 220px;">Nomor &amp; Tanggal BAST</th>
                        <th style="min-width: 170px;">Alur BAST</th>
                        <th style="min-width: 180px;">Petugas Lapangan</th>
                        <th style="min-width: 200px;">Kegiatan Terkait</th>
                        <th style="min-width: 240px;">Keterangan Serah Terima</th>
                        <th class="text-end" style="min-width: 140px;">Unduh / Cetak</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($basts as $item)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark font-monospace">{{ $item->bast_number }}</div>
                                <div class="small text-muted"><i class="fa-regular fa-calendar me-1"></i> {{ $item->bast_date->translatedFormat('d F Y') }}</div>
                            </td>
                            <td>
                                @if($item->bast_type === 'kantor_ke_petugas' || empty($item->bast_type))
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle py-1 px-2" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-boxes-packing me-1 text-primary"></i> Kantor &rarr; Petugas (Blanko)
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle py-1 px-2" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-file-circle-check me-1 text-warning"></i> Petugas &rarr; Kantor (Terisi)
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->officer ? $item->officer->name : '-' }}</div>
                                <div class="small text-muted"><i class="fa-solid fa-phone me-1"></i> {{ $item->officer ? ($item->officer->phone ?? $item->officer->username) : '-' }}</div>
                            </td>
                            <td>
                                <span class="small fw-semibold text-dark text-truncate d-block" style="max-width: 240px;">
                                    {{ $item->assignment ? $item->assignment->title : '-' }}
                                </span>
                                <small class="text-muted"><i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $item->assignment ? $item->assignment->location : '-' }}</small>
                            </td>
                            <td>
                                <div class="small text-secondary text-truncate" style="max-width: 280px;" title="{{ $item->description }}">
                                    {{ Str::limit($item->description, 50) }}
                                </div>
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="sp-action-btn-group">
                                    <!-- Unduh PDF -->
                                    <a href="{{ route('admin.basts.officers.download', $item) }}" 
                                       class="btn btn-light border sp-action-btn text-success" 
                                       title="Unduh Berkas PDF" 
                                       data-bs-toggle="tooltip">
                                        <i class="fa-solid fa-file-arrow-down"></i>
                                    </a>

                                    <!-- Preview / Cetak -->
                                    <a href="{{ route('admin.basts.officers.preview', $item) }}" 
                                       target="_blank" 
                                       class="btn btn-light border sp-action-btn text-primary" 
                                       title="Lihat / Cetak Dokumen" 
                                       data-bs-toggle="tooltip">
                                        <i class="fa-solid fa-print"></i>
                                    </a>

                                    <!-- Hapus -->
                                    <form method="POST" action="{{ route('admin.basts.officers.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Hapus arsip BAST Petugas {{ $item->bast_number }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-light border sp-action-btn text-danger" 
                                                title="Hapus BAST" 
                                                data-bs-toggle="tooltip">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-regular fa-folder-open fa-3x mb-3 text-secondary opacity-50"></i>
                                <div class="fw-semibold">Belum ada dokumen BAST Petugas diterbitkan.</div>
                                <div class="small">Klik tombol "Terbitkan BAST Petugas" untuk membuat Berita Acara penyerahan berkas tugas.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($basts->hasPages())
        <div class="sp-card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Menampilkan {{ $basts->firstItem() }} sampai {{ $basts->lastItem() }} dari {{ $basts->total() }} dokumen
            </div>
            <div>
                {{ $basts->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

<!-- ==============================================================
     MODAL TERBITKAN BAST PETUGAS
     ============================================================== -->
<div class="modal fade" id="createOfficerBastModal" tabindex="-1" aria-labelledby="createOfficerBastModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="createOfficerBastModalLabel">
                    <i class="fa-solid fa-file-circle-plus text-primary me-2"></i> Terbitkan BAST Petugas Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.basts.officers.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">JENIS ALUR SERAH TERIMA BAST <span class="text-danger">*</span></label>
                        <select name="bast_type" class="form-select fw-semibold" required>
                            <option value="kantor_ke_petugas">1. BAST Blanko Dokumen dan Perlengkapan (Kantor &rarr; Petugas)</option>
                            <option value="petugas_ke_kantor">3. BAST Dokumen Terisi &amp; Berkas Hasil Sensus (Petugas &rarr; Kantor)</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">PILIH KEGIATAN PENUGASAN <span class="text-danger">*</span></label>
                            <select name="assignment_id" class="form-select" required>
                                <option value="">-- Pilih Kegiatan --</option>
                                @foreach($assignments as $assignment)
                                    <option value="{{ $assignment->id }}">{{ $assignment->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">PILIH PETUGAS PENANGGUNG JAWAB <span class="text-danger">*</span></label>
                            <select name="officer_id" class="form-select" required>
                                <option value="">-- Pilih Petugas --</option>
                                @foreach($officers as $officer)
                                    <option value="{{ $officer->id }}">{{ $officer->name }} ({{ $officer->phone ?? $officer->username }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary">NOMOR BAST (OPSIONAL)</label>
                            <input type="text" name="bast_number" class="form-control font-monospace" placeholder="Kosongkan untuk penomoran otomatis sistem">
                            <small class="text-muted" style="font-size: 0.72rem;">Format otomatis: <code>BAST-PETUGAS/{TAHUN}/{BULAN}/{NOMOR_URUT}</code></small>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">TANGGAL BAST <span class="text-danger">*</span></label>
                            <input type="date" name="bast_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">KETERANGAN PENYERAHAN TUGAS & BERKAS <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Contoh: Telah menyelesaikan pendataan 30 responden lapangan di Desa Sukamaju dan menyerahkan bundel formulir kuesioner fisik beserta dokumen verifikasi digital secara lengkap." required></textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-secondary">CATATAN PENGAWAS (OPSIONAL)</label>
                            <input type="text" name="notes" class="form-control" placeholder="Contoh: Berkas telah diperiksa dan disetujui koordinator.">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">STATUS ARSIP <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="finalized" selected>Final / Sah</option>
                                <option value="draft">Draft</option>
                                <option value="archived">Diarsipkan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary">
                        <i class="fa-solid fa-file-pdf me-1"></i> Terbitkan & Buat Dokumen PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@extends('layouts.app')

@section('title', 'BAST Petugas Lapangan')
@section('page_title', 'BAST Petugas')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('petugas.dashboard') }}">Petugas</a></li>
    <li class="breadcrumb-item active" aria-current="page">BAST Petugas</li>
@endsection

@section('content')
<!-- Header -->
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h3 class="fw-bold text-dark mb-1">Berita Acara Serah Terima (BAST) Penugasan</h3>
        <p class="text-muted small mb-0">Arsip dokumen serah terima penyelesaian tugas lapangan kepada koordinator/Administrator kegiatan.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <button type="button" class="btn btn-sp-primary" data-bs-toggle="modal" data-bs-target="#createMyOfficerBastModal">
            <i class="fa-solid fa-file-circle-plus me-1"></i> Buat BAST Penugasan
        </button>
    </div>
</div>

<!-- Table Card -->
<div class="sp-card mb-4">
    <div class="sp-card-header d-flex justify-content-between align-items-center">
        <h5 class="sp-card-title mb-0">
            <i class="fa-solid fa-file-shield text-primary"></i>
            Daftar BAST Penugasan Anda ({{ $basts->total() }})
        </h5>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle d-md-none" style="font-size: 0.72rem;">
            {{ $basts->total() }} Dokumen
        </span>
    </div>
    <div class="sp-card-body p-0">
        <!-- MODE DESKTOP & TABLET: Tabel Lengkap -->
        <div class="table-responsive d-none d-md-block">
            <table class="table sp-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="min-width: 220px;">Nomor & Tanggal BAST</th>
                        <th style="min-width: 220px;">Kegiatan Terkait</th>
                        <th style="min-width: 250px;">Keterangan Serah Terima</th>
                        <th class="text-end" style="min-width: 140px;">Aksi</th>
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
                                    <a href="{{ route('petugas.basts.officers.download', $item) }}" 
                                       class="btn btn-light border sp-action-btn text-success" 
                                       title="Unduh PDF" 
                                       data-bs-toggle="tooltip">
                                        <i class="fa-solid fa-file-arrow-down"></i>
                                    </a>
                                    <a href="{{ route('petugas.basts.officers.preview', $item) }}" 
                                       target="_blank" 
                                       class="btn btn-light border sp-action-btn text-primary" 
                                       title="Cetak / Preview" 
                                       data-bs-toggle="tooltip">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fa-regular fa-folder-open fa-3x mb-3 text-secondary opacity-50"></i>
                                <div class="fw-semibold">Anda belum menerbitkan BAST Petugas.</div>
                                <div class="small">Klik tombol "Buat BAST Penugasan" di atas untuk menyerahkan berkas resmi kegiatan.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MODE MOBILE / HP: Daftar Kartu BAST Petugas Responsif -->
        <div class="d-block d-md-none p-3">
            @forelse($basts as $item)
                <div class="card border rounded-3 mb-3 shadow-none bg-white">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-light text-dark font-monospace border mb-1" style="font-size: 0.75rem;">
                                    {{ $item->bast_number }}
                                </span>
                                @if($item->assignment)
                                    <h6 class="fw-bold text-dark mb-0">{{ $item->assignment->title }}</h6>
                                    <small class="text-muted"><i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $item->assignment->location }}</small>
                                @endif
                            </div>
                            <div class="d-flex gap-1.5">
                                <a href="{{ route('petugas.basts.officers.download', $item) }}" 
                                   class="btn btn-sm btn-light border text-success shadow-sm" 
                                   title="Unduh PDF">
                                    <i class="fa-solid fa-file-arrow-down"></i>
                                </a>
                                <a href="{{ route('petugas.basts.officers.preview', $item) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-light border text-primary shadow-sm" 
                                   title="Cetak">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                            </div>
                        </div>

                        <div class="small text-secondary mb-2">
                            <div class="mb-1">
                                <i class="fa-solid fa-align-left text-muted me-1.5" style="width: 14px;"></i>
                                <span>{{ Str::limit($item->description, 70) }}</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="text-muted small" style="font-size: 0.75rem;">
                                <i class="fa-regular fa-calendar me-1"></i> {{ $item->bast_date->translatedFormat('d F Y') }}
                            </span>
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1" style="font-size: 0.72rem;">
                                <i class="fa-solid fa-file-shield me-1"></i> BAST Petugas
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <i class="fa-regular fa-folder-open fa-2x mb-2 text-secondary opacity-50"></i>
                    <div class="fw-semibold small">Anda belum menerbitkan BAST Petugas</div>
                </div>
            @endforelse
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
     MODAL BUAT BAST PETUGAS (PETUGAS)
     ============================================================== -->
<div class="modal fade" id="createMyOfficerBastModal" tabindex="-1" aria-labelledby="createMyOfficerBastModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="createMyOfficerBastModalLabel">
                    <i class="fa-solid fa-file-circle-plus text-primary me-2"></i> Buat BAST Penyerahan Tugas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('petugas.basts.officers.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary">PILIH KEGIATAN <span class="text-danger">*</span></label>
                            <select name="assignment_id" class="form-select" required>
                                <option value="">-- Pilih Kegiatan Anda --</option>
                                @foreach($myAssignments as $item)
                                    <option value="{{ $item->id }}">{{ $item->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">TANGGAL BAST <span class="text-danger">*</span></label>
                            <input type="date" name="bast_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">KETERANGAN PENYERAHAN TUGAS & BERKAS <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Contoh: Telah menyelesaikan seluruh kunjungan responden lapangan dan menyerahkan bundel formulir kuesioner fisik yang telah terisi lengkap kepada koordinator." required></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold text-secondary">CATATAN TAMBAHAN (OPSIONAL)</label>
                        <input type="text" name="notes" class="form-control" placeholder="Catatan kondisi penyerahan...">
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary">
                        <i class="fa-solid fa-file-pdf me-1"></i> Terbitkan & Download PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@extends('layouts.app')

@section('title', 'BAST Responden')
@section('page_title', 'BAST Responden')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">BAST Responden</li>
@endsection

@section('content')
<!-- Header & Quick Actions -->
<div class="row align-items-center mb-3">
    <div class="col-md-7">
        <h4 class="fw-bold text-dark mb-1">Berita Acara Serah Terima (BAST) Responden</h4>
        <p class="text-muted small mb-0">Kelola dokumen resmi serah terima berkas kuesioner, formulir verifikasi, dan arsip legalitas dengan pihak responden.</p>
    </div>
    <div class="col-md-5 text-md-end mt-2 mt-md-0">
        @if(auth()->user()->isAdmin())
            <button type="button" class="btn btn-sm btn-sp-primary" data-bs-toggle="modal" data-bs-target="#createBastModal">
                <i class="fa-solid fa-file-circle-plus me-1"></i> Terbitkan BAST Baru
            </button>
        @endif
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-2 mb-3">
    <div class="col-md-6 col-xl-3">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-solid fa-file-contract"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Total BAST Responden</div>
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
                <div class="sp-stat-desc">Diterbitkan bulan ini</div>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="sp-card mb-3">
    <div class="sp-card-header">
        <h5 class="sp-card-title">
            <i class="fa-solid fa-file-lines text-primary"></i>
            Daftar Dokumen BAST Responden ({{ $basts->total() }})
        </h5>
    </div>
    <div class="sp-card-body p-0">
        <div class="table-responsive">
            <table class="table sp-table align-middle">
                <thead>
                    <tr>
                        <th style="min-width: 180px;">Nomor &amp; Tanggal BAST</th>
                        <th style="min-width: 170px;">Pihak KRT / Responden</th>
                        <th style="min-width: 170px;">Penerima Dokumen</th>
                        <th style="min-width: 160px;">Kegiatan Terkait</th>
                        <th style="min-width: 150px;">Petugas Pelaksana</th>
                        <th style="min-width: 100px;">Tanda Tangan</th>
                        <th class="text-end" style="min-width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($basts as $item)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark font-monospace">{{ $item->bast_number }}</div>
                                <div class="small text-muted"><i class="fa-regular fa-calendar me-1"></i> {{ $item->bast_date->translatedFormat('d F Y') }}</div>
                                @php $respCat = strtoupper($item->handover_category ?: 'KUES'); @endphp
                                <div class="mt-1 d-flex flex-wrap gap-1">
                                    @if(!empty($item->respondent_number))
                                        <span class="badge bg-dark-subtle text-dark border border-dark-subtle" style="font-size: 0.68rem;">
                                            No. Resp: {{ $item->respondent_number }}
                                        </span>
                                    @endif
                                    <span class="badge {{ $respCat === 'ATK' ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-primary-subtle text-primary-emphasis border border-primary-subtle' }}" style="font-size: 0.68rem;">
                                        {{ $respCat }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->respondent ? $item->respondent->name : '-' }}</div>
                                <div class="small text-muted">{{ $item->respondent ? $item->respondent->agency : '-' }}</div>
                            </td>
                            <td>
                                @if($item->recipient_type === 'representative')
                                    <div class="fw-bold text-dark">{{ $item->recipient_name }}</div>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.7rem;">
                                        <i class="fa-solid fa-users me-1"></i> Wakil ({{ $item->recipient_relation ?? 'Keluarga' }})
                                    </span>
                                @else
                                    <div class="fw-bold text-dark">{{ $item->respondent ? $item->respondent->name : '-' }}</div>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.7rem;">
                                        <i class="fa-solid fa-user-check me-1"></i> KRT Langsung
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="small fw-semibold text-dark text-truncate d-block" style="max-width: 180px;">
                                    {{ $item->assignment ? $item->assignment->title : '-' }}
                                </span>
                            </td>
                            <td>
                                <div class="small fw-bold text-dark">{{ $item->officer ? $item->officer->name : '-' }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">Petugas Lapangan</div>
                            </td>
                            <td>
                                @if(!empty($item->signature_path))
                                    <button type="button" 
                                            class="btn btn-sm btn-light border text-success shadow-none py-1 px-2" 
                                            style="font-size: 0.75rem;" 
                                            onclick="previewSignatureModal('{{ route('media.file', ['path' => ltrim($item->signature_path, '/')]) }}', '{{ $item->bast_number }}', '{{ $item->recipient_display_name }}')">
                                        <i class="fa-solid fa-signature me-1"></i> Lihat TTD
                                    </button>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="sp-action-btn-group">
                                    <!-- Unduh PDF -->
                                    <a href="{{ route('admin.basts.respondents.download', $item) }}" 
                                       class="btn btn-light border sp-action-btn text-success" 
                                       title="Unduh Berkas PDF" 
                                       data-bs-toggle="tooltip">
                                        <i class="fa-solid fa-file-arrow-down"></i>
                                    </a>

                                    <!-- Preview / Cetak -->
                                    <a href="{{ route('admin.basts.respondents.preview', $item) }}" 
                                       target="_blank" 
                                       class="btn btn-light border sp-action-btn text-primary" 
                                       title="Lihat / Cetak Dokumen" 
                                       data-bs-toggle="tooltip">
                                        <i class="fa-solid fa-print"></i>
                                    </a>

                                    @if(auth()->user()->isAdmin())
                                        <!-- Hapus -->
                                        <form method="POST" action="{{ route('admin.basts.respondents.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Hapus arsip dokumen BAST {{ $item->bast_number }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-light border sp-action-btn text-danger" 
                                                    title="Hapus BAST" 
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
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-regular fa-folder-open fa-3x mb-3 text-secondary opacity-50"></i>
                                <div class="fw-semibold">Belum ada dokumen BAST Responden diterbitkan.</div>
                                <div class="small">Klik tombol "Terbitkan BAST Baru" untuk membuat Berita Acara resmi.</div>
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

@if(auth()->user()->isAdmin())
<!-- ==============================================================
     MODAL TERBITKAN BAST RESPONDEN BARU
     ============================================================== -->
<div class="modal fade" id="createBastModal" tabindex="-1" aria-labelledby="createBastModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <form method="POST" action="{{ route('admin.basts.respondents.store') }}" class="modal-content border-0 shadow" style="max-height: 90vh; display: flex; flex-direction: column;">
            @csrf
            <div class="modal-header bg-light border-bottom sticky-top flex-shrink-0">
                <h5 class="modal-title fw-bold" id="createBastModalLabel">
                    <i class="fa-solid fa-file-circle-plus text-primary me-2"></i> Terbitkan BAST Responden Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="flex: 1 1 auto; overflow-y: auto;">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary">PILIH RESPONDEN <span class="text-danger">*</span></label>
                            <select name="respondent_id" class="form-select" required>
                                <option value="">-- Pilih Responden --</option>
                                @foreach($respondents as $resp)
                                    <option value="{{ $resp->id }}">{{ $resp->name }} — {{ $resp->agency }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary d-flex justify-content-between align-items-center">
                                <span>TANGGAL BAST <span class="text-danger">*</span></span>
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle" style="font-size: 0.65rem;">Tgl Dibuka</span>
                            </label>
                            <input type="date" name="bast_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">NO. URUT RESPONDEN</label>
                            <input type="number" name="respondent_number" class="form-control" min="1" placeholder="Contoh: 1, 2, ... 10">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-secondary">KATEGORI TANDA TERIMA <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="border rounded-3 p-2 d-flex align-items-center gap-2 cursor-pointer bg-light h-100">
                                        <input type="radio" name="handover_category" value="ATK" class="form-check-input mt-0">
                                        <span class="small fw-semibold">ATK <small class="text-muted fw-normal">(Perlengkapan)</small></span>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <label class="border rounded-3 p-2 d-flex align-items-center gap-2 cursor-pointer bg-light h-100">
                                        <input type="radio" name="handover_category" value="KUES" class="form-check-input mt-0" checked>
                                        <span class="small fw-semibold">KUES <small class="text-muted fw-normal">(Kuesioner)</small></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-secondary">NOMOR BAST (OPSIONAL)</label>
                            <input type="text" name="bast_number" class="form-control font-monospace" placeholder="Kosongkan untuk penomoran otomatis sistem">
                            <small class="text-muted" style="font-size: 0.72rem;">Format otomatis: <code>BAST-RESP/{TAHUN}/{BULAN}/{NOMOR_URUT}</code></small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">STATUS BAST <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="finalized" selected>Final / Sah</option>
                                <option value="draft">Draft</option>
                                <option value="archived">Diarsipkan</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">RINCIAN DOKUMEN / BARANG YANG DISERAHKAN <span class="text-danger">*</span></label>
                        <textarea name="handover_items" class="form-control" rows="4" placeholder="Contoh:&#10;1. Kuesioner Formulir Pendataan Lapangan Terpadu (1 Rangkap)&#10;2. Salinan KTP & KK Penanggung Jawab Usaha&#10;3. Surat Keterangan Domisili Usaha" required></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold text-secondary">CATATAN TAMBAHAN (OPSIONAL)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Catatan kondisi serah terima..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top sticky-bottom flex-shrink-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary">
                        <i class="fa-solid fa-file-pdf me-1"></i> Terbitkan & Buat Dokumen PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

<!-- Modal Pratinjau Tanda Tangan Digital -->
<div class="modal fade" id="sigPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom py-2">
                <h6 class="modal-title fw-bold text-dark mb-0">
                    <i class="fa-solid fa-signature text-success me-1"></i> Pratinjau Tanda Tangan
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3">
                <div class="p-2 border rounded bg-light mb-2">
                    <img id="sigModalImg" src="" alt="Tanda Tangan" style="max-height: 120px; max-width: 100%;">
                </div>
                <div class="fw-bold text-dark small" id="sigModalName">-</div>
                <div class="text-muted font-monospace" style="font-size: 0.72rem;" id="sigModalBastNo">-</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewSignatureModal(url, bastNo, name) {
        document.getElementById('sigModalImg').src = url;
        document.getElementById('sigModalBastNo').innerText = bastNo;
        document.getElementById('sigModalName').innerText = name;
        const modal = new bootstrap.Modal(document.getElementById('sigPreviewModal'));
        modal.show();
    }
</script>
@endpush


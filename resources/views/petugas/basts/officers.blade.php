@extends('layouts.app')

@section('title', $currentType === 'kantor_ke_petugas' ? 'BAST Blanko & Perlengkapan' : ($currentType === 'petugas_ke_kantor' ? 'BAST Dokumen Terisi' : 'BAST Petugas Lapangan'))
@section('page_title', $currentType === 'kantor_ke_petugas' ? 'BAST Blanko & Perlengkapan' : ($currentType === 'petugas_ke_kantor' ? 'BAST Dokumen Terisi' : 'BAST Petugas'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('petugas.dashboard') }}">Petugas</a></li>
    <li class="breadcrumb-item active" aria-current="page">
        {{ $currentType === 'kantor_ke_petugas' ? 'BAST Blanko (Kantor ke Petugas)' : ($currentType === 'petugas_ke_kantor' ? 'BAST Dokumen Terisi (Petugas ke Kantor)' : 'BAST Petugas') }}
    </li>
@endsection

@section('content')
<!-- Header -->
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        @if($currentType === 'kantor_ke_petugas')
            <h3 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-boxes-packing text-primary me-2"></i>BAST Blanko &amp; Perlengkapan
            </h3>
            <p class="text-muted small mb-0">Arsip serah terima penerimaan blanko kuesioner dan perlengkapan survei/sensus dari Kantor Satker BPS kepada Petugas Lapangan.</p>
        @elseif($currentType === 'petugas_ke_kantor')
            <h3 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-file-circle-check text-warning me-2"></i>BAST Dokumen Terisi
            </h3>
            <p class="text-muted small mb-0">Arsip serah terima pengembalian berkas dokumen/kuesioner yang telah terisi lengkap hasil pencacahan lapangan kepada Kantor Satker BPS.</p>
        @else
            <h3 class="fw-bold text-dark mb-1">Berita Acara Serah Terima (BAST) Penugasan</h3>
            <p class="text-muted small mb-0">Arsip dokumen serah terima blanko/perlengkapan maupun pengembalian dokumen terisi antara Petugas dan Kantor Satker BPS.</p>
        @endif
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <button type="button" class="btn btn-sp-primary" data-bs-toggle="modal" data-bs-target="#createMyOfficerBastModal">
            <i class="fa-solid fa-file-circle-plus me-1"></i> Buat BAST Baru
        </button>
    </div>
</div>

<!-- Tab Filter BAST Petugas (Sesuai 4 Alur BPS) -->
<div class="mb-4">
    <div class="nav nav-pills gap-2 flex-wrap" role="tablist">
        <a href="{{ route('petugas.basts.officers.index') }}" 
           class="btn btn-sm {{ empty($currentType) ? 'btn-sp-primary' : 'btn-outline-secondary' }} rounded-pill px-3 py-1.5 fw-semibold">
            <i class="fa-solid fa-list me-1"></i> Semua BAST ({{ $countBlanko + $countTerisi }})
        </a>
        <a href="{{ route('petugas.basts.officers.index', ['type' => 'kantor_ke_petugas']) }}" 
           class="btn btn-sm {{ $currentType === 'kantor_ke_petugas' ? 'btn-sp-primary' : 'btn-outline-primary' }} rounded-pill px-3 py-1.5 fw-semibold">
            <i class="fa-solid fa-boxes-packing me-1"></i> 1. Blanko &amp; Perlengkapan (Kantor &rarr; Petugas)
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1 font-monospace">{{ $countBlanko }}</span>
        </a>
        <a href="{{ route('petugas.basts.officers.index', ['type' => 'petugas_ke_kantor']) }}" 
           class="btn btn-sm {{ $currentType === 'petugas_ke_kantor' ? 'btn-sp-primary' : 'btn-outline-warning' }} rounded-pill px-3 py-1.5 fw-semibold">
            <i class="fa-solid fa-file-circle-check me-1"></i> 3. Dokumen Terisi (Petugas &rarr; Kantor)
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle ms-1 font-monospace">{{ $countTerisi }}</span>
        </a>
    </div>
</div>

<!-- Table Card -->
<div class="sp-card mb-4">
    <div class="sp-card-header d-flex justify-content-between align-items-center">
        <h5 class="sp-card-title mb-0">
            <i class="fa-solid fa-file-shield text-primary"></i>
            Daftar Dokumen BAST ({{ $basts->total() }})
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
                        <th style="min-width: 220px;">Nomor &amp; Tanggal BAST</th>
                        <th style="min-width: 180px;">Jenis Alur BAST</th>
                        <th style="min-width: 200px;">Kegiatan Terkait</th>
                        <th style="min-width: 240px;">Keterangan Serah Terima</th>
                        <th class="text-end" style="min-width: 130px;">Aksi</th>
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
                                <span class="small fw-semibold text-dark text-truncate d-block" style="max-width: 200px;">
                                    {{ $item->activity_subtitle ?: ($item->assignment ? $item->assignment->title : '-') }}
                                </span>
                                <small class="text-muted"><i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $item->document_place ?: ($item->assignment ? $item->assignment->location : '-') }}</small>
                                @if($item->recipients->count() > 1)
                                    <div>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle mt-1" style="font-size: 0.65rem;">
                                            <i class="fa-solid fa-users me-1"></i> Kolektif ({{ $item->recipients->count() }} Petugas)
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($item->isKantorKePetugas() && $item->document_title)
                                    <div class="small fw-semibold text-dark text-truncate" style="max-width: 260px;">{{ $item->document_title }}</div>
                                @endif
                                <div class="small text-secondary text-truncate" style="max-width: 260px;" title="{{ $item->description }}">
                                    {{ Str::limit(str_replace("\n", ', ', $item->description), 55) }}
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
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-regular fa-folder-open fa-3x mb-3 text-secondary opacity-50"></i>
                                <div class="fw-semibold">Belum ada dokumen BAST pada kategori ini.</div>
                                <div class="small">Klik tombol "Buat BAST Baru" di atas untuk menerbitkan dokumen resmi kegiatan.</div>
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
                                @if($item->bast_type === 'kantor_ke_petugas' || empty($item->bast_type))
                                    <div>
                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-1.5 py-0.5" style="font-size: 0.65rem;">
                                            <i class="fa-solid fa-boxes-packing me-1"></i> Kantor &rarr; Petugas (Blanko)
                                        </span>
                                    </div>
                                @else
                                    <div>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-1.5 py-0.5" style="font-size: 0.65rem;">
                                            <i class="fa-solid fa-file-circle-check me-1"></i> Petugas &rarr; Kantor (Terisi)
                                        </span>
                                    </div>
                                @endif
                                @if($item->assignment || $item->activity_subtitle)
                                    <h6 class="fw-bold text-dark mt-1 mb-0">{{ $item->activity_subtitle ?: $item->assignment->title }}</h6>
                                    <small class="text-muted"><i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $item->document_place ?: ($item->assignment->location ?? '-') }}</small>
                                @endif
                                @if($item->recipients->count() > 1)
                                    <div class="mt-1">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.65rem;">
                                            <i class="fa-solid fa-users me-1"></i> Kolektif ({{ $item->recipients->count() }} Petugas)
                                        </span>
                                    </div>
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
                            @if($item->isKantorKePetugas() && $item->document_title)
                                <div class="fw-semibold text-dark mb-0.5">{{ $item->document_title }}</div>
                            @endif
                            <div class="mb-1">
                                <i class="fa-solid fa-align-left text-muted me-1.5" style="width: 14px;"></i>
                                <span>{{ Str::limit(str_replace("\n", ', ', $item->description), 70) }}</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="text-muted small" style="font-size: 0.75rem;">
                                <i class="fa-regular fa-calendar me-1"></i> {{ $item->bast_date->translatedFormat('d F Y') }}
                            </span>
                            <span class="text-muted small" style="font-size: 0.75rem;">
                                Status: <strong class="text-success">Sah / Selesai</strong>
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <i class="fa-regular fa-folder-open fa-2x mb-2 text-secondary opacity-50"></i>
                    <div class="fw-semibold small">Belum ada dokumen BAST pada kategori ini</div>
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
     MODAL BUAT BAST PETUGAS (SESUAI 4 ALUR BPS)
     ============================================================== -->
<div class="modal fade" id="createMyOfficerBastModal" tabindex="-1" aria-labelledby="createMyOfficerBastModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="createMyOfficerBastModalLabel">
                    <i class="fa-solid fa-file-circle-plus text-primary me-2"></i> Buat Dokumen BAST
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('petugas.basts.officers.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <!-- Pilihan Alur BAST Resmi BPS -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">JENIS ALUR SERAH TERIMA BAST <span class="text-danger">*</span></label>
                        <select name="bast_type" id="bastTypeSelect" class="form-select fw-semibold" required onchange="updateBastDescriptionPlaceholder()">
                            <option value="kantor_ke_petugas" {{ ($currentType === 'kantor_ke_petugas' || empty($currentType)) ? 'selected' : '' }}>
                                1. BAST Blanko Dokumen dan Perlengkapan (Kantor &rarr; Petugas)
                            </option>
                            <option value="petugas_ke_kantor" {{ $currentType === 'petugas_ke_kantor' ? 'selected' : '' }}>
                                3. BAST Dokumen Terisi &amp; Hasil Pendataan (Petugas &rarr; Kantor)
                            </option>
                        </select>
                        <div class="form-text small" id="bastTypeHelp">
                            Pilih tahap alur serah terima yang sedang dilaksanakan dengan kantor satker BPS.
                        </div>
                    </div>

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
                        <label class="form-label small fw-semibold text-secondary">RINCIAN DOKUMEN &amp; PERLENGKAPAN YANG DISERAHKAN <span class="text-danger">*</span></label>
                        <textarea name="description" id="bastDescriptionInput" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold text-secondary">CATATAN TAMBAHAN (OPSIONAL)</label>
                        <input type="text" name="notes" class="form-control" placeholder="Catatan kondisi dokumen/perlengkapan...">
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary">
                        <i class="fa-solid fa-file-circle-check me-1"></i> Terbitkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateBastDescriptionPlaceholder() {
    const type = document.getElementById('bastTypeSelect').value;
    const desc = document.getElementById('bastDescriptionInput');
    if (type === 'kantor_ke_petugas') {
        desc.placeholder = "Contoh:\n- Formulir Kuesioner Sensus/Survei Blanko: 120 Eksemplar\n- Buku Pedoman Petugas Pencacah: 1 Buku\n- Tanda Pengenal / ID Card Petugas Lapangan: 1 Buah\n- Rompi / Tas Perlengkapan Dinas BPS: 1 Paket\n- Alat Tulis Kantor (ATK) & Papan Alas: 1 Set";
    } else {
        desc.placeholder = "Contoh:\n- Bundel Kuesioner Sensus/Survei yang Telah Terisi Lengkap: 120 Eksemplar\n- Berkas Berita Acara Serah Terima (BAST) Responden Bertanda Tangan: Lengkap\n- Pengembalian Perlengkapan Inventaris Satker (Rompi & Tas Dinas): Kondisi Baik";
    }
}
document.addEventListener('DOMContentLoaded', updateBastDescriptionPlaceholder);
</script>
@endsection



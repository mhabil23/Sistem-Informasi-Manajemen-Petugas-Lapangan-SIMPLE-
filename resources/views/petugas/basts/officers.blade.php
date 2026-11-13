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
                <i class="fa-solid fa-boxes-packing text-primary me-2"></i>Tanda Terima Petugas (ATK / KUES)
            </h3>
            <p class="text-muted small mb-0">Daftar tanda terima ATK dan Kuesioner yang disusun oleh Administrator. Silakan periksa kelengkapan fisik barang yang Anda terima, lalu bubuhkan tanda tangan digital Anda pada dokumen penugasan.</p>
        @elseif($currentType === 'petugas_ke_kantor')
            <h3 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-file-circle-check text-warning me-2"></i>BAST Dokumen Terisi
            </h3>
            <p class="text-muted small mb-0">Arsip BAST pengembalian berkas dokumen/kuesioner yang telah terisi lengkap hasil pencacahan lapangan kepada Kantor Satker BPS.</p>
        @else
            <h3 class="fw-bold text-dark mb-1">Tanda Terima Petugas &amp; BAST</h3>
            <p class="text-muted small mb-0">Arsip Tanda Terima Petugas (ATK / KUES) serta BAST pengembalian dokumen terisi antara Petugas dan Kantor Satker BPS.</p>
        @endif
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        @if($currentType !== 'kantor_ke_petugas')
            <button type="button" class="btn btn-sp-primary" onclick="openCreateMyOfficerBastModal()">
                <i class="fa-solid fa-file-circle-plus me-1"></i> Buat BAST Terisi
            </button>
        @else
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle py-2 px-3 fw-semibold">
                <i class="fa-solid fa-user-shield me-1"></i> Disusun oleh Administrator
            </span>
        @endif
    </div>
</div>

@if(isset($errors) && $errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <div class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Gagal menyimpan dokumen BAST:</div>
        <ul class="mb-0 small">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Tab Filter Tanda Terima & BAST Petugas -->
<div class="mb-4">
    <div class="nav nav-pills gap-2 flex-wrap" role="tablist">
        <a href="{{ route('petugas.basts.officers.index') }}" 
           class="btn btn-sm {{ empty($currentType) ? 'btn-sp-primary' : 'btn-outline-secondary' }} rounded-pill px-3 py-1.5 fw-semibold">
            <i class="fa-solid fa-list me-1"></i> Semua ({{ $countBlanko + $countTerisi }})
        </a>
        <a href="{{ route('petugas.basts.officers.index', ['type' => 'kantor_ke_petugas']) }}" 
           class="btn btn-sm {{ $currentType === 'kantor_ke_petugas' ? 'btn-sp-primary' : 'btn-outline-primary' }} rounded-pill px-3 py-1.5 fw-semibold">
            <i class="fa-solid fa-boxes-packing me-1"></i> Tanda Terima Petugas (ATK / KUES)
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1 font-monospace">{{ $countBlanko }}</span>
        </a>
        <a href="{{ route('petugas.basts.officers.index', ['type' => 'petugas_ke_kantor']) }}" 
           class="btn btn-sm {{ $currentType === 'petugas_ke_kantor' ? 'btn-sp-primary' : 'btn-outline-warning' }} rounded-pill px-3 py-1.5 fw-semibold">
            <i class="fa-solid fa-file-circle-check me-1"></i> BAST Dokumen Terisi
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
                        <th style="min-width: 200px;">Nomor &amp; Tanggal BAST</th>
                        <th style="min-width: 170px;">Jenis Alur BAST</th>
                        <th style="min-width: 200px;">Kegiatan Terkait</th>
                        <th style="min-width: 220px;">Keterangan Serah Terima</th>
                        <th style="min-width: 150px;">Status TTD Saya</th>
                        <th class="text-end" style="min-width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($basts as $item)
                        @php
                            $myRecipient = $item->recipientForUser(auth()->id(), auth()->user()->name);
                            $hasSigned = $myRecipient && $myRecipient->isSigned();
                            $isKantorKePetugas = $item->isKantorKePetugas();
                            $cat = strtoupper($item->receipt_category ?: 'ATK');
                            $signPayload = [
                                'id' => $item->id,
                                'bast_number' => $item->bast_number,
                                'document_title' => $item->document_title ?: 'Tanda Terima Petugas',
                                'category' => $cat,
                                'activity' => $item->activity_subtitle ?: ($item->assignment ? $item->assignment->title : '-'),
                                'place_date' => $item->place_date_line,
                                'maker_name' => $item->maker_name,
                                'maker_nip' => $item->maker_nip,
                                'items' => $item->parsed_items,
                                'my_name' => $myRecipient->recipient_name ?? auth()->user()->name,
                                'sign_url' => route('petugas.basts.officers.sign', $item),
                            ];
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-bold text-dark font-monospace">{{ $item->bast_number }}</div>
                                <div class="small text-muted"><i class="fa-regular fa-calendar me-1"></i> {{ $item->bast_date->translatedFormat('d F Y') }}</div>
                            </td>
                            <td>
                                @if($item->bast_type === 'kantor_ke_petugas' || empty($item->bast_type))
                                    @php $cat = strtoupper($item->receipt_category ?: 'ATK'); @endphp
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle py-1 px-2" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-boxes-packing me-1 text-primary"></i> Tanda Terima Petugas
                                    </span>
                                    <div class="mt-1">
                                        <span class="badge {{ $cat === 'KUES' ? 'bg-primary-subtle text-primary-emphasis border border-primary-subtle' : 'bg-success-subtle text-success-emphasis border border-success-subtle' }}" style="font-size: 0.68rem;">
                                            {{ $cat }}
                                        </span>
                                    </div>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle py-1 px-2" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-file-circle-check me-1 text-warning"></i> BAST (Petugas &rarr; Kantor)
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
                            <td>
                                @if($isKantorKePetugas)
                                    @if($hasSigned)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-2" style="font-size: 0.72rem;">
                                            <i class="fa-solid fa-circle-check me-1"></i> Sudah TTD
                                        </span>
                                        <div class="text-muted" style="font-size: 0.68rem;">
                                            {{ $myRecipient->signed_at ? $myRecipient->signed_at->translatedFormat('d M Y') : 'Terverifikasi' }}
                                        </div>
                                    @else
                                        <button type="button" 
                                                class="btn btn-sm btn-warning text-dark fw-bold py-1 px-2.5 shadow-sm" 
                                                style="font-size: 0.75rem;"
                                                onclick='openOfficerSignModal(@json($signPayload))'>
                                            <i class="fa-solid fa-pen-nib me-1"></i> Tanda Tangan
                                        </button>
                                    @endif
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.72rem;">
                                        Dokumen Terisi
                                    </span>
                                @endif
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
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-regular fa-folder-open fa-3x mb-3 text-secondary opacity-50"></i>
                                <div class="fw-semibold">Belum ada dokumen BAST pada kategori ini.</div>
                                <div class="small">Dokumen Tanda Terima Petugas (ATK/KUES) akan tampil di sini setelah disusun oleh Administrator.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MODE MOBILE / HP: Daftar Kartu BAST Petugas Responsif -->
        <div class="d-block d-md-none p-3">
            @forelse($basts as $item)
                @php
                    $myRecipientMobile = $item->recipientForUser(auth()->id(), auth()->user()->name);
                    $hasSignedMobile = $myRecipientMobile && $myRecipientMobile->isSigned();
                    $isKantorKePetugasMobile = $item->isKantorKePetugas();
                    $catMobile = strtoupper($item->receipt_category ?: 'ATK');
                    $signPayloadMobile = [
                        'id' => $item->id,
                        'bast_number' => $item->bast_number,
                        'document_title' => $item->document_title ?: 'Tanda Terima Petugas',
                        'category' => $catMobile,
                        'activity' => $item->activity_subtitle ?: ($item->assignment ? $item->assignment->title : '-'),
                        'place_date' => $item->place_date_line,
                        'maker_name' => $item->maker_name,
                        'maker_nip' => $item->maker_nip,
                        'items' => $item->parsed_items,
                        'my_name' => $myRecipientMobile->recipient_name ?? auth()->user()->name,
                        'sign_url' => route('petugas.basts.officers.sign', $item),
                    ];
                @endphp
                <div class="card border rounded-3 mb-3 shadow-none bg-white">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-light text-dark font-monospace border mb-1" style="font-size: 0.75rem;">
                                    {{ $item->bast_number }}
                                </span>
                                @if($item->bast_type === 'kantor_ke_petugas' || empty($item->bast_type))
                                    @php $cat = strtoupper($item->receipt_category ?: 'ATK'); @endphp
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-1.5 py-0.5" style="font-size: 0.65rem;">
                                            <i class="fa-solid fa-boxes-packing me-1"></i> Tanda Terima Petugas
                                        </span>
                                        <span class="badge {{ $cat === 'KUES' ? 'bg-primary-subtle text-primary-emphasis border border-primary-subtle' : 'bg-success-subtle text-success-emphasis border border-success-subtle' }} px-1.5 py-0.5" style="font-size: 0.65rem;">
                                            {{ $cat }}
                                        </span>
                                    </div>
                                @else
                                    <div>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-1.5 py-0.5" style="font-size: 0.65rem;">
                                            <i class="fa-solid fa-file-circle-check me-1"></i> BAST (Petugas &rarr; Kantor)
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

                        @if($isKantorKePetugasMobile)
                            <div class="pt-2 pb-1 border-top">
                                @if($hasSignedMobile)
                                    <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded-2">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.72rem;">
                                            <i class="fa-solid fa-circle-check me-1"></i> Sudah TTD
                                        </span>
                                        <small class="text-muted" style="font-size: 0.68rem;">
                                            {{ $myRecipientMobile->signed_at?->translatedFormat('d M Y H:i') }}
                                        </small>
                                    </div>
                                @else
                                    <button type="button" 
                                            class="btn btn-warning btn-sm w-100 fw-bold text-dark shadow-sm py-1.5"
                                            onclick='openOfficerSignModal(@json($signPayloadMobile))'>
                                        <i class="fa-solid fa-pen-nib me-1"></i> Verifikasi Barang &amp; Tanda Tangan
                                    </button>
                                @endif
                            </div>
                        @endif

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
<div class="modal fade" id="officerBastFormModal" tabindex="-1" aria-labelledby="officerBastFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="officerBastFormModalLabel">
                    <i class="fa-solid fa-file-circle-plus text-primary me-2"></i> Buat Dokumen BAST
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="officerBastForm" method="POST" action="{{ route('petugas.basts.officers.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">JENIS ALUR SERAH TERIMA BAST <span class="text-danger">*</span></label>
                            <select name="bast_type" id="bastTypeInput" class="form-select fw-semibold" required onchange="toggleBastTypeSections()">
                                <option value="kantor_ke_petugas" {{ ($currentType === 'kantor_ke_petugas' || empty($currentType)) ? 'selected' : '' }}>
                                    Tanda Terima Petugas (ATK / KUES - Tgl Dikunci)
                                </option>
                                <option value="petugas_ke_kantor" {{ $currentType === 'petugas_ke_kantor' ? 'selected' : '' }}>
                                    BAST Dokumen Terisi (Petugas &rarr; Kantor)
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">PILIH KEGIATAN <span class="text-danger">*</span></label>
                            <select name="assignment_id" id="bastAssignmentInput" class="form-select" required onchange="onAssignmentSelected()">
                                <option value="">-- Pilih Kegiatan Anda --</option>
                                @foreach($myAssignments as $item)
                                    <option value="{{ $item->id }}">{{ $item->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">TANGGAL PEMBUATAN BAST</label>
                            <div class="form-control bg-light text-dark small d-flex align-items-center justify-content-between">
                                <span class="fw-semibold">{{ now()->translatedFormat('j F Y') }}</span>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle"><i class="fa-solid fa-lock me-1"></i>Tgl Dikunci</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">CATATAN TAMBAHAN (OPSIONAL)</label>
                        <input type="text" name="notes" id="bastNotesInput" class="form-control" placeholder="Catatan kondisi dokumen/perlengkapan...">
                    </div>

                    @include('basts.partials._blanko_fields', ['officers' => $officers])

                    <div id="terisiSingleSection" class="d-none">
                        <div class="mb-0">
                            <label class="form-label small fw-semibold text-secondary">RINCIAN DOKUMEN &amp; PERLENGKAPAN YANG DISERAHKAN <span class="text-danger">*</span></label>
                            <textarea name="description" id="bastSingleDescriptionInput" class="form-control" rows="4" placeholder="Contoh:&#10;- Bundel Kuesioner Sensus/Survei yang Telah Terisi Lengkap: 120 Eksemplar&#10;- Berkas BAST Responden Bertanda Tangan: Lengkap&#10;- Pengembalian Perlengkapan Inventaris Satker: Kondisi Baik"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary">
                        <i class="fa-solid fa-file-circle-check me-1"></i> Terbitkan &amp; Buat PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@php
    $currentUser = auth()->user();
    $defaultRecipient = $currentUser ? [
        'user_id' => $currentUser->id,
        'recipient_name' => $currentUser->name,
        'position_unit' => $currentUser->position ?: ($currentUser->isPegawai() ? 'Pegawai BPS' : 'Mitra'),
        'origin_role' => '',
    ] : null;
@endphp

@include('basts.partials._blanko_scripts', [
    'assignments' => $myAssignments,
    'officers' => $officers,
    'defaultMakerName' => 'Koordinator Logistik BPS',
    'defaultMakerNip' => '',
    'defaultRecipient' => $defaultRecipient,
])

<script>
const PETUGAS_DEFAULT_BAST_TYPE = @json($currentType === 'petugas_ke_kantor' ? 'petugas_ke_kantor' : 'kantor_ke_petugas');

function openCreateMyOfficerBastModal() {
    isEditModalMode = false;
    resetBastModalCommon();
    initBlankoDefaultFields(PETUGAS_DEFAULT_BAST_TYPE);
    bootstrap.Modal.getOrCreateInstance(document.getElementById('officerBastFormModal')).show();
}

let officerSigPad = null;
let officerHasDrawn = false;

function setupOfficerSignaturePad(canvasId, inputId) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return null;
    const ctx = canvas.getContext('2d');
    let isDrawing = false;

    function resize() {
        const rect = canvas.getBoundingClientRect();
        if (rect.width > 0 && rect.width !== canvas.width) {
            const temp = officerHasDrawn ? canvas.toDataURL() : null;
            canvas.width = rect.width;
            canvas.height = 150;
            ctx.lineWidth = 2.5;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#0d2142';
            if (temp) {
                const img = new Image();
                img.onload = () => ctx.drawImage(img, 0, 0);
                img.src = temp;
            }
        } else {
            ctx.lineWidth = 2.5;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#0d2142';
        }
    }

    resize();

    function getCoords(e) {
        const rect = canvas.getBoundingClientRect();
        if (e.touches && e.touches.length > 0) {
            return {
                x: e.touches[0].clientX - rect.left,
                y: e.touches[0].clientY - rect.top
            };
        }
        return {
            x: e.clientX - rect.left,
            y: e.clientY - rect.top
        };
    }

    function startDraw(e) {
        const cb = document.getElementById('verifyGoodsCheckbox');
        if (!cb || !cb.checked) return;
        isDrawing = true;
        officerHasDrawn = true;
        const ph = document.getElementById('signCanvasPlaceholder');
        if (ph) ph.style.display = 'none';
        const pos = getCoords(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
        updateSubmitButtonState();
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
        const input = document.getElementById(inputId);
        if (input && officerHasDrawn) {
            input.value = canvas.toDataURL('image/png');
        }
        updateSubmitButtonState();
    }

    canvas.addEventListener('mousedown', startDraw);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDraw);
    canvas.addEventListener('mouseleave', stopDraw);

    canvas.addEventListener('touchstart', startDraw, { passive: false });
    canvas.addEventListener('touchmove', draw, { passive: false });
    canvas.addEventListener('touchend', stopDraw);

    return {
        clear: function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            officerHasDrawn = false;
            const input = document.getElementById(inputId);
            if (input) input.value = '';
            const ph = document.getElementById('signCanvasPlaceholder');
            if (ph) ph.style.display = 'block';
            updateSubmitButtonState();
        },
        resize: resize,
        hasDrawn: function() { return officerHasDrawn; },
        toDataURL: function() { return canvas.toDataURL('image/png'); }
    };
}

function openOfficerSignModal(data) {
    document.getElementById('officerSignForm').action = data.sign_url;
    document.getElementById('signModalBastNumber').textContent = data.bast_number;
    document.getElementById('signModalDocTitle').textContent = data.document_title;
    document.getElementById('signModalCategory').textContent = data.category || 'ATK';
    document.getElementById('signModalActivity').textContent = data.activity || '-';
    document.getElementById('signModalPlaceDate').textContent = data.place_date || '-';
    document.getElementById('signModalMaker').textContent = (data.maker_name || '-') + (data.maker_nip ? ' (' + data.maker_nip + ')' : '');

    const itemsList = document.getElementById('signModalItemsList');
    if (itemsList) {
        if (data.items && data.items.length > 0) {
            itemsList.innerHTML = '<ol class="mb-0 ps-3">' + data.items.map(function(it) {
                return '<li class="mb-1 fw-semibold text-dark">' + it + '</li>';
            }).join('') + '</ol>';
        } else {
            itemsList.innerHTML = '<span class="text-muted fst-italic">1 Set Dokumen / Formulir / Perlengkapan Kegiatan</span>';
        }
    }

    const cb = document.getElementById('verifyGoodsCheckbox');
    if (cb) {
        cb.checked = false;
        onVerifyGoodsChanged(false);
    }

    const modalEl = document.getElementById('officerSignModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

function onVerifyGoodsChanged(isChecked) {
    const card = document.getElementById('signCanvasCard');
    if (card) {
        card.style.opacity = isChecked ? '1' : '0.5';
        card.style.pointerEvents = isChecked ? 'auto' : 'none';
    }
    updateSubmitButtonState();
}

function updateSubmitButtonState() {
    const cb = document.getElementById('verifyGoodsCheckbox');
    const isChecked = cb ? cb.checked : false;
    const btn = document.getElementById('btnSubmitOfficerSign');
    if (btn) {
        btn.disabled = !(isChecked && officerHasDrawn);
    }
}

function clearOfficerSignPad() {
    if (officerSigPad) {
        officerSigPad.clear();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('officerSignModal');
    if (modal) {
        modal.addEventListener('shown.bs.modal', function () {
            officerSigPad = setupOfficerSignaturePad('officerSignatureCanvas', 'officerSignatureData');
            if (officerSigPad) {
                officerSigPad.clear();
                officerSigPad.resize();
            }
        });
    }

    const form = document.getElementById('officerSignForm');
    if (form) {
        form.addEventListener('submit', function() {
            if (officerSigPad && officerSigPad.hasDrawn()) {
                document.getElementById('officerSignatureData').value = officerSigPad.toDataURL();
            }
        });
    }
});
</script>

<!-- ==============================================================
     MODAL VERIFIKASI PENERIMAAN BARANG & TANDA TANGAN PETUGAS
     ============================================================== -->
<div class="modal fade" id="officerSignModal" tabindex="-1" aria-labelledby="officerSignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="officerSignModalLabel">
                        <i class="fa-solid fa-pen-nib text-primary me-2"></i> Verifikasi Barang &amp; Tanda Tangan
                    </h5>
                    <div class="font-monospace small text-muted" id="signModalBastNumber"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="officerSignForm" method="POST" action="">
                @csrf
                <input type="hidden" name="signature_data" id="officerSignatureData">

                <div class="modal-body p-3 p-md-4">
                    <!-- Info Dokumen -->
                    <div class="p-3 rounded-3 bg-light border mb-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-primary text-white mb-1" id="signModalCategory">ATK</span>
                                <h6 class="fw-bold text-dark mb-0" id="signModalDocTitle">Tanda Terima Petugas</h6>
                                <small class="text-muted" id="signModalActivity"></small>
                            </div>
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size: 0.7rem;">
                                <i class="fa-solid fa-lock me-1"></i> Tgl Dikunci
                            </span>
                        </div>
                        <div class="row g-2 pt-2 border-top small text-secondary">
                            <div class="col-sm-6">
                                <i class="fa-regular fa-calendar me-1"></i> <span id="signModalPlaceDate"></span>
                            </div>
                            <div class="col-sm-6 text-sm-end">
                                <i class="fa-solid fa-user-pen me-1"></i> Pembuat: <strong id="signModalMaker"></strong>
                            </div>
                        </div>
                    </div>

                    <!-- 1. VERIFIKASI PENERIMAAN BARANG -->
                    <div class="card border border-primary-subtle shadow-none mb-3">
                        <div class="card-header bg-primary-subtle py-2 px-3">
                            <span class="fw-bold small text-primary-emphasis">
                                <i class="fa-solid fa-boxes-stacked me-1"></i> 1. VERIFIKASI PENERIMAAN BARANG / PERLENGKAPAN
                            </span>
                        </div>
                        <div class="card-body p-3">
                            <p class="small text-muted mb-2">Pastikan fisik barang / perlengkapan berikut telah Anda terima sebelum membubuhkan tanda tangan:</p>
                            <div id="signModalItemsList" class="p-2.5 bg-light rounded-2 mb-3 small"></div>

                            <div class="form-check p-3 rounded-3 border border-2 border-primary bg-white shadow-sm mb-2">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="verify_goods" id="verifyGoodsCheckbox" value="1" required onchange="onVerifyGoodsChanged(this.checked)">
                                <label class="form-check-label fw-bold text-dark small cursor-pointer" for="verifyGoodsCheckbox">
                                    <i class="fa-solid fa-check-double text-success me-1"></i>
                                    Saya telah memeriksa dan menyatakan telah menerima seluruh barang / perlengkapan di atas secara lengkap dan dalam keadaan baik.
                                </label>
                            </div>

                            <div>
                                <label class="form-label small text-secondary mb-1">Catatan Penerimaan (Opsional):</label>
                                <input type="text" name="verification_note" class="form-control form-control-sm" placeholder="Contoh: Barang diterima lengkap dalam kondisi baik">
                            </div>
                        </div>
                    </div>

                    <!-- 2. TANDA TANGAN DIGITAL -->
                    <div class="card border shadow-none mb-0" id="signCanvasCard" style="opacity: 0.5; pointer-events: none; transition: opacity 0.2s;">
                        <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center">
                            <span class="fw-bold small text-dark">
                                <i class="fa-solid fa-signature text-primary me-1"></i> 2. TANDA TANGAN DIGITAL PETUGAS
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="clearOfficerSignPad()">
                                <i class="fa-solid fa-rotate-left me-1"></i> Ulangi
                            </button>
                        </div>
                        <div class="card-body p-3 text-center">
                            <div class="border rounded-2 bg-light mb-2 position-relative" style="height: 150px; overflow: hidden;">
                                <canvas id="officerSignatureCanvas" class="w-100 h-100" style="cursor: crosshair; touch-action: none;"></canvas>
                                <div id="signCanvasPlaceholder" class="position-absolute top-50 start-50 translate-middle text-muted small user-select-none pointer-events-none">
                                    <i class="fa-solid fa-pen-fancy mb-1 d-block opacity-50 fa-2x"></i>
                                    Bubuhkan tanda tangan di area ini (layar sentuh / mouse)
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center small text-muted">
                                <span>Penerima: <strong class="text-dark">{{ auth()->user()->name }}</strong> ({{ auth()->user()->roleLabel() }})</span>
                                <span class="badge bg-secondary-subtle text-secondary">Tersimpan ke Dokumen Sah</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSubmitOfficerSign" class="btn btn-sp-primary" disabled>
                        <i class="fa-solid fa-file-signature me-1"></i> Simpan Tanda Tangan &amp; Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection



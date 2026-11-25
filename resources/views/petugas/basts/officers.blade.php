@extends('layouts.app')

@section('title', $currentType === 'kantor_ke_petugas' ? 'Tanda Terima Petugas' : ($currentType === 'petugas_ke_kantor' ? 'BAST Dokumen Terisi' : 'Tanda Terima Petugas'))
@section('page_title', $currentType === 'kantor_ke_petugas' ? 'Tanda Terima Petugas' : ($currentType === 'petugas_ke_kantor' ? 'BAST Dokumen Terisi' : 'Tanda Terima Petugas'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('petugas.dashboard') }}">Petugas</a></li>
    <li class="breadcrumb-item active" aria-current="page">
        {{ $currentType === 'kantor_ke_petugas' ? 'Tanda Terima Petugas (ATK / KUES)' : ($currentType === 'petugas_ke_kantor' ? 'BAST Dokumen Terisi' : 'Tanda Terima Petugas') }}
    </li>
@endsection

@push('styles')
<style>
    .bento-card {
        background: #FFFFFF;
        border-radius: 18px;
        border: 1px solid rgba(226, 232, 240, 0.85);
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
    }
    .btn-bps-orange {
        background-color: #F37021;
        border-color: #F37021;
        color: #ffffff;
        font-weight: 600;
        border-radius: 999px;
        padding: 0.45rem 1rem;
        font-size: 0.82rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        transition: all 0.2s ease;
    }
    .btn-bps-orange:hover, .btn-bps-orange:focus {
        background-color: #d95d12;
        border-color: #d95d12;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(243, 112, 33, 0.25);
    }
    .bento-tab-pill {
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.45rem 1.15rem;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border: 1px solid #E2E8F0;
        background: #FFFFFF;
        color: #64748B;
    }
    .bento-tab-pill:hover {
        background: #FFF7ED;
        color: #F37021;
        border-color: #FDBA74;
    }
    .bento-tab-pill.active {
        background: #F37021;
        border-color: #F37021;
        color: #FFFFFF !important;
        box-shadow: 0 4px 12px rgba(243, 112, 33, 0.25);
    }
    .bento-tab-pill .badge {
        background: #F1F5F9;
        color: #475569;
        font-size: 0.72rem;
        padding: 0.2rem 0.5rem;
        border: none;
    }
    .bento-tab-pill.active .badge {
        background: rgba(255, 255, 255, 0.25) !important;
        color: #FFFFFF !important;
    }
    .bento-table {
        table-layout: fixed;
        width: 100%;
        margin-bottom: 0;
        font-size: 0.82rem;
    }
    @media (min-width: 768px) and (max-width: 1279.98px) {
        .bento-table {
            min-width: 800px;
        }
    }
    .bento-table thead th {
        background-color: #F8FAFC;
        color: #475569;
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.75rem 0.85rem;
        border-bottom: 1px solid #E2E8F0;
        vertical-align: middle;
        white-space: nowrap;
    }
    .bento-table tbody td {
        padding: 0.75rem 0.85rem;
        vertical-align: middle;
        border-bottom: 1px solid #F1F5F9;
    }
    .bento-table tbody tr:hover td {
        background-color: #F8FAFC;
    }
    .bento-action-btn {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        padding: 0;
        border: 1px solid #E2E8F0;
        background: #FFFFFF;
        transition: all 0.15s ease;
        text-decoration: none;
        flex-shrink: 0;
    }
    .bento-action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }
    .bento-action-btn.btn-print { color: #4F46E5; border-color: #C7D2FE; background: #EEF2FF; }
    .bento-action-btn.btn-print:hover { background: #4F46E5; color: #FFFFFF; }
    .bento-action-btn.btn-pdf { color: #DC2626; border-color: #FECACA; background: #FEF2F2; }
    .bento-action-btn.btn-pdf:hover { background: #DC2626; color: #FFFFFF; }
</style>
@endpush

@section('content')
@if(isset($errors) && $errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-3">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Gagal menyimpan dokumen BAST:</div>
        <ul class="mb-0 small">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Tab Filter Tanda Terima & BAST Petugas -->
<div class="mb-4">
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('petugas.basts.officers.index') }}" 
           class="bento-tab-pill {{ empty($currentType) ? 'active' : '' }}">
            <i class="bi bi-list-ul"></i> Semua
            <span class="badge rounded-pill ms-1 font-monospace">{{ $countBlanko + $countTerisi }}</span>
        </a>
        <a href="{{ route('petugas.basts.officers.index', ['type' => 'kantor_ke_petugas']) }}" 
           class="bento-tab-pill {{ $currentType === 'kantor_ke_petugas' ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Tanda Terima Petugas
            <span class="badge rounded-pill ms-1 font-monospace">{{ $countBlanko }}</span>
        </a>
        <a href="{{ route('petugas.basts.officers.index', ['type' => 'petugas_ke_kantor']) }}" 
           class="bento-tab-pill {{ $currentType === 'petugas_ke_kantor' ? 'active' : '' }}">
            <i class="bi bi-file-earmark-check"></i> BAST Dokumen Terisi
            <span class="badge rounded-pill ms-1 font-monospace">{{ $countTerisi }}</span>
        </a>
    </div>
</div>

<!-- Table Card -->
<div class="bento-card mb-4 overflow-hidden">
    <div class="d-flex flex-wrap justify-content-between align-items-center p-3 border-bottom gap-2 bg-white">
        <div>
            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.98rem;">
                Daftar Dokumen Tanda Terima &amp; BAST
            </h6>
            <span class="text-muted" style="font-size: 0.72rem;">Total {{ $basts->total() }} berkas dokumen terdaftar</span>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="badge bg-light text-secondary border rounded-pill px-3 py-1.5 font-monospace" style="font-size: 0.75rem;">
                Halaman {{ $basts->currentPage() }} dari {{ $basts->lastPage() ?: 1 }}
            </span>
            @if($currentType !== 'kantor_ke_petugas')
                <button type="button" class="btn btn-bps-orange rounded-pill px-3 py-1.5 shadow-sm" onclick="openCreateMyOfficerBastModal()" style="font-size: 0.82rem;">
                    <i class="bi bi-file-earmark-plus me-1"></i> Buat BAST Terisi
                </button>
            @endif
        </div>
    </div>

    <!-- MODE DESKTOP & TABLET: Tabel Anti-Geser -->
    <div class="d-none d-md-block table-responsive">
        <table class="table bento-table align-middle">
            <thead>
                <tr>
                    <th style="width: 22%;">NOMOR &amp; TANGGAL BAST</th>
                    <th style="width: 18%;">JENIS ALUR</th>
                    <th style="width: 20%;">KEGIATAN TERKAIT</th>
                    <th style="width: 22%;">KETERANGAN</th>
                    <th class="text-center" style="width: 9%;">STATUS TTD</th>
                    <th class="text-end pe-3" style="width: 9%;">AKSI</th>
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
                            <div class="fw-bold text-dark font-monospace text-truncate" style="font-size: 0.8rem;">{{ $item->bast_number }}</div>
                            <div class="small text-muted" style="font-size: 0.72rem;">
                                <i class="bi bi-calendar3 me-1"></i> {{ $item->bast_date->translatedFormat('d F Y') }}
                            </div>
                        </td>
                        <td>
                            @if($item->bast_type === 'kantor_ke_petugas' || empty($item->bast_type))
                                @php $cat = strtoupper($item->receipt_category ?: 'PELATIHAN'); @endphp
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle py-1 px-2" style="font-size: 0.7rem;">
                                    <i class="bi bi-box-seam text-primary me-0.5"></i> Tanda Terima Petugas
                                </span>
                                <div class="mt-1">
                                    @if($cat === 'PELATIHAN' || $cat === 'PERLENGKAPAN')
                                        <span class="badge border" style="font-size: 0.65rem; background: #faf5ff; color: #7e22ce; border-color: #e9d5ff !important;">
                                            1. Perlengkapan Petugas
                                        </span>
                                    @elseif($cat === 'ATK')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.65rem;">
                                            2. ATK Pendataan
                                        </span>
                                    @elseif($cat === 'KUES')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.65rem;">
                                            3. Kuesioner Pendataan
                                        </span>
                                    @elseif($cat === 'ATK_RESPONDEN' || $cat === 'RESPONDEN')
                                        <span class="badge border" style="font-size: 0.65rem; background: #fff1f2; color: #e11d48; border-color: #fecdd3 !important;">
                                            4. ATK &amp; Dok. Responden
                                        </span>
                                    @elseif($cat === 'TIMBANGAN')
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size: 0.65rem;">
                                            5. Timbangan
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.65rem;">
                                            {{ $cat }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle py-1 px-2" style="font-size: 0.7rem;">
                                    <i class="bi bi-file-earmark-check text-warning me-0.5"></i> BAST (Petugas &rarr; Kantor)
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="small fw-semibold text-dark text-truncate d-block" style="font-size: 0.75rem;" title="{{ $item->activity_subtitle ?: ($item->assignment ? $item->assignment->title : '-') }}">
                                {{ $item->activity_subtitle ?: ($item->assignment ? $item->assignment->title : '-') }}
                            </span>
                            <small class="text-muted d-block text-truncate" style="font-size: 0.7rem;">
                                <i class="bi bi-geo-alt-fill text-danger me-0.5"></i> {{ $item->document_place ?: ($item->assignment ? $item->assignment->location : '-') }}
                            </small>
                            @if($item->recipients->count() > 1)
                                <div>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle mt-0.5" style="font-size: 0.62rem;">
                                        <i class="bi bi-people me-0.5"></i> Kolektif ({{ $item->recipients->count() }} Petugas)
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($item->isKantorKePetugas() && $item->document_title)
                                <div class="small fw-semibold text-dark text-truncate" style="font-size: 0.75rem;">{{ $item->document_title }}</div>
                            @endif
                            <div class="small text-secondary text-truncate" style="font-size: 0.72rem;" title="{{ $item->description }}">
                                {{ Str::limit(str_replace("\n", ', ', $item->description), 55) }}
                            </div>
                        </td>
                        <td class="text-center">
                            @if($isKantorKePetugas)
                                @if($hasSigned)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-2 rounded-pill" style="font-size: 0.68rem;">
                                        <i class="bi bi-check-circle-fill me-0.5"></i> Sudah TTD
                                    </span>
                                @else
                                    <button type="button" 
                                            class="btn btn-sm btn-bps-orange text-white fw-bold py-1 px-2.5 rounded-pill shadow-sm" 
                                            style="font-size: 0.72rem;"
                                            onclick='openOfficerSignModal(@json($signPayload))'>
                                        <i class="bi bi-pen-fill me-0.5"></i> TTD Digital
                                    </button>
                                @endif
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border rounded-pill" style="font-size: 0.68rem;">
                                    Dokumen Terisi
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-inline-flex gap-1 align-items-center">
                                <a href="{{ route('petugas.basts.officers.download', $item) }}" 
                                   class="bento-action-btn btn-pdf" 
                                   title="Unduh PDF Resmi" 
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                                <a href="{{ route('petugas.basts.officers.preview', $item) }}" 
                                   target="_blank" 
                                   class="bento-action-btn btn-print" 
                                   title="Cetak / Preview" 
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="py-4">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow-sm" style="width: 64px; height: 64px; background: rgba(243, 112, 33, 0.08); color: #F37021;">
                                    <i class="bi bi-file-earmark-text fs-2"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 1rem;">Belum Ada Dokumen BAST</h6>
                                <p class="text-muted small mb-3" style="max-width: 440px; margin: 0 auto; line-height: 1.5;">
                                    Belum ada berkas tanda terima atau BAST pada kategori ini. Dokumen akan tampil secara otomatis setelah disusun.
                                </p>
                                @if($currentType !== 'kantor_ke_petugas')
                                    <button type="button" class="btn btn-bps-orange rounded-pill px-4 py-2 shadow-sm" onclick="openCreateMyOfficerBastModal()">
                                        <i class="bi bi-file-earmark-plus me-1.5"></i> Buat BAST Terisi Sekarang
                                    </button>
                                @endif
                            </div>
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
            <div class="bento-card p-3 mb-3 border">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="badge bg-light text-dark font-monospace border mb-1" style="font-size: 0.72rem;">
                            {{ $item->bast_number }}
                        </span>
                        @if($item->bast_type === 'kantor_ke_petugas' || empty($item->bast_type))
                            @php $cat = strtoupper($item->receipt_category ?: 'PELATIHAN'); @endphp
                            <div class="d-flex flex-wrap gap-1">
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-1.5 py-0.5" style="font-size: 0.65rem;">
                                    <i class="bi bi-box-seam me-0.5"></i> Tanda Terima Petugas
                                </span>
                                @if($cat === 'PELATIHAN' || $cat === 'PERLENGKAPAN')
                                    <span class="badge border px-1.5 py-0.5" style="font-size: 0.65rem; background: #faf5ff; color: #7e22ce; border-color: #e9d5ff !important;">
                                        1. Perlengkapan Petugas
                                    </span>
                                @elseif($cat === 'ATK')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-1.5 py-0.5" style="font-size: 0.65rem;">
                                        2. ATK
                                    </span>
                                @elseif($cat === 'KUES')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5" style="font-size: 0.65rem;">
                                        3. Kuesioner
                                    </span>
                                @elseif($cat === 'ATK_RESPONDEN' || $cat === 'RESPONDEN')
                                    <span class="badge border px-1.5 py-0.5" style="font-size: 0.65rem; background: #fff1f2; color: #e11d48; border-color: #fecdd3 !important;">
                                        4. ATK &amp; Dok. Responden
                                    </span>
                                @elseif($cat === 'TIMBANGAN')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-1.5 py-0.5" style="font-size: 0.65rem;">
                                        5. Timbangan
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border px-1.5 py-0.5" style="font-size: 0.65rem;">
                                        {{ $cat }}
                                    </span>
                                @endif
                            </div>
                        @else
                            <div>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-1.5 py-0.5" style="font-size: 0.65rem;">
                                    <i class="bi bi-file-earmark-check me-0.5"></i> BAST (Petugas &rarr; Kantor)
                                </span>
                            </div>
                        @endif
                        @if($item->assignment || $item->activity_subtitle)
                            <h6 class="fw-bold text-dark mt-1 mb-0" style="font-size: 0.88rem;">{{ $item->activity_subtitle ?: $item->assignment->title }}</h6>
                            <small class="text-muted" style="font-size: 0.72rem;"><i class="bi bi-geo-alt-fill text-danger me-0.5"></i> {{ $item->document_place ?: ($item->assignment->location ?? '-') }}</small>
                        @endif
                        @if($item->recipients->count() > 1)
                            <div class="mt-1">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.65rem;">
                                    <i class="bi bi-people me-0.5"></i> Kolektif ({{ $item->recipients->count() }} Petugas)
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="d-flex gap-1.5">
                        <a href="{{ route('petugas.basts.officers.download', $item) }}" 
                           class="bento-action-btn btn-pdf" 
                           title="Unduh PDF Resmi">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </a>
                        <a href="{{ route('petugas.basts.officers.preview', $item) }}" 
                           target="_blank" 
                           class="bento-action-btn btn-print" 
                           title="Cetak">
                            <i class="bi bi-printer"></i>
                        </a>
                    </div>
                </div>

                <div class="small text-secondary mb-2" style="font-size: 0.75rem;">
                    @if($item->isKantorKePetugas() && $item->document_title)
                        <div class="fw-semibold text-dark mb-0.5">{{ $item->document_title }}</div>
                    @endif
                    <div class="mb-1 text-truncate">
                        <i class="bi bi-card-text text-muted me-1"></i>
                        <span>{{ Str::limit(str_replace("\n", ', ', $item->description), 70) }}</span>
                    </div>
                </div>

                @if($isKantorKePetugasMobile)
                    <div class="pt-2 pb-1 border-top">
                        @if($hasSignedMobile)
                            <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded-3">
                                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.7rem;">
                                    <i class="bi bi-check-circle-fill me-0.5"></i> Sudah TTD
                                </span>
                                <small class="text-muted" style="font-size: 0.68rem;">
                                    {{ $myRecipientMobile->signed_at?->translatedFormat('d M Y H:i') }}
                                </small>
                            </div>
                        @else
                            <button type="button" 
                                    class="btn btn-bps-orange btn-sm w-100 fw-bold shadow-sm py-1.5 rounded-pill"
                                    style="font-size: 0.75rem;"
                                    onclick='openOfficerSignModal(@json($signPayloadMobile))'>
                                <i class="bi bi-pen-fill me-1"></i> Verifikasi Barang &amp; Tanda Tangan
                            </button>
                        @endif
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <span class="text-muted small" style="font-size: 0.72rem;">
                        <i class="bi bi-calendar me-1"></i> {{ $item->bast_date->translatedFormat('d F Y') }}
                    </span>
                    <span class="text-muted small" style="font-size: 0.72rem;">
                        Status: <strong class="text-success">Sah / Selesai</strong>
                    </span>
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-muted">
                <i class="bi bi-folder2-open display-6 text-secondary opacity-50 mb-2 d-block"></i>
                <div class="fw-semibold small">Belum ada dokumen BAST pada kategori ini</div>
            </div>
        @endforelse
    </div>

    @if($basts->hasPages())
        <div class="bg-white border-top p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="small text-muted" style="font-size: 0.78rem;">
                Menampilkan {{ $basts->firstItem() }} sampai {{ $basts->lastItem() }} dari {{ $basts->total() }} dokumen
            </div>
            <div>
                {{ $basts->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

<!-- ==============================================================
     MODAL BUAT BAST PETUGAS
     ============================================================== -->
<div class="modal fade" id="officerBastFormModal" tabindex="-1" aria-labelledby="officerBastFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <form id="officerBastForm" method="POST" action="{{ route('petugas.basts.officers.store') }}" class="modal-content border-0 shadow" style="border-radius: 18px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden;">
            @csrf
            <div class="modal-header bg-light border-bottom sticky-top flex-shrink-0 py-2.5 px-3">
                <h6 class="modal-title fw-bold text-dark mb-0" id="officerBastFormModalLabel">
                    <i class="bi bi-file-earmark-plus text-primary me-1.5"></i> Buat Dokumen BAST
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3.5" style="flex: 1 1 auto; overflow-y: auto;">
                <div class="row g-3 mb-3">
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold text-secondary" style="font-size: 0.78rem;">JENIS ALUR SERAH TERIMA BAST <span class="text-danger">*</span></label>
                        <select name="bast_type" id="bastTypeInput" class="form-select form-select-sm fw-semibold" required onchange="toggleBastTypeSections()">
                            <option value="kantor_ke_petugas" {{ ($currentType === 'kantor_ke_petugas' || empty($currentType)) ? 'selected' : '' }}>
                                Tanda Terima Petugas (ATK / KUES)
                            </option>
                            <option value="petugas_ke_kantor" {{ $currentType === 'petugas_ke_kantor' ? 'selected' : '' }}>
                                BAST Dokumen Terisi (Petugas &rarr; Kantor)
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-secondary" style="font-size: 0.78rem;">PILIH KEGIATAN <span class="text-danger">*</span></label>
                        <select name="assignment_id" id="bastAssignmentInput" class="form-select form-select-sm" required onchange="onAssignmentSelected()">
                            <option value="">-- Pilih Kegiatan Anda --</option>
                            @foreach($myAssignments as $item)
                                <option value="{{ $item->id }}">{{ $item->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-secondary" style="font-size: 0.78rem;">TANGGAL PEMBUATAN BAST</label>
                        <div class="form-control form-control-sm bg-light text-dark small d-flex align-items-center justify-content-between">
                            <span class="fw-semibold">{{ now()->translatedFormat('j F Y') }}</span>
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size: 0.65rem;"><i class="bi bi-lock me-0.5"></i>Tgl Dikunci</span>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary" style="font-size: 0.78rem;">CATATAN TAMBAHAN (OPSIONAL)</label>
                    <input type="text" name="notes" id="bastNotesInput" class="form-control form-control-sm" placeholder="Catatan kondisi dokumen/perlengkapan...">
                </div>

                @include('basts.partials._blanko_fields', ['officers' => $officers, 'pegawaiList' => $pegawaiList])

                @include('basts.partials._terisi_fields', ['isAdminForm' => false, 'pegawaiList' => $pegawaiList])
            </div>
            <div class="modal-footer bg-light border-top sticky-bottom flex-shrink-0 py-2 px-3">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal" style="font-size: 0.82rem;">Batal</button>
                <button type="submit" class="btn-bps-orange rounded-pill px-4">
                    <i class="bi bi-check2-circle"></i> Terbitkan &amp; Buat PDF
                </button>
            </div>
        </form>
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
            canvas.height = 175;
            ctx.lineWidth = 3.5;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.strokeStyle = '#02182b';
            if (temp) {
                const img = new Image();
                img.onload = () => ctx.drawImage(img, 0, 0);
                img.src = temp;
            }
        } else {
            ctx.lineWidth = 3.5;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.strokeStyle = '#02182b';
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
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <form id="officerSignForm" method="POST" action="" class="modal-content border-0 shadow" style="border-radius: 18px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden;">
            @csrf
            <input type="hidden" name="signature_data" id="officerSignatureData">

            <div class="modal-header bg-light border-bottom sticky-top flex-shrink-0 py-2.5 px-3">
                <div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="officerSignModalLabel">
                        <i class="bi bi-pen-fill text-primary me-1.5"></i> Verifikasi Barang &amp; Tanda Tangan
                    </h6>
                    <div class="font-monospace text-muted" id="signModalBastNumber" style="font-size: 0.72rem;"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-3 p-md-3.5" style="flex: 1 1 auto; overflow-y: auto;">
                <!-- Info Dokumen -->
                <div class="p-3 rounded-3 bg-light border mb-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-primary text-white mb-1" id="signModalCategory">ATK</span>
                            <h6 class="fw-bold text-dark mb-0" id="signModalDocTitle" style="font-size: 0.95rem;">Tanda Terima Petugas</h6>
                            <small class="text-muted" id="signModalActivity" style="font-size: 0.75rem;"></small>
                        </div>
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size: 0.68rem;">
                            <i class="bi bi-lock me-1"></i> Tgl Dikunci
                        </span>
                    </div>
                    <div class="row g-2 pt-2 border-top small text-secondary" style="font-size: 0.75rem;">
                        <div class="col-sm-6">
                            <i class="bi bi-calendar3 me-1"></i> <span id="signModalPlaceDate"></span>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <i class="bi bi-person-badge me-1"></i> Pembuat: <strong id="signModalMaker"></strong>
                        </div>
                    </div>
                </div>

                <!-- 1. VERIFIKASI PENERIMAAN BARANG -->
                <div class="bento-card mb-3 p-3 border">
                    <div class="d-flex align-items-center gap-1.5 mb-2">
                        <i class="bi bi-boxes text-primary"></i>
                        <span class="fw-bold small text-dark" style="font-size: 0.82rem;">1. VERIFIKASI PENERIMAAN BARANG / PERLENGKAPAN</span>
                    </div>
                    <p class="small text-muted mb-2" style="font-size: 0.75rem;">Pastikan fisik barang / perlengkapan berikut telah Anda terima sebelum menandatangani:</p>
                    <div id="signModalItemsList" class="p-2.5 bg-light rounded-3 mb-3 small" style="font-size: 0.8rem;"></div>

                    <div class="form-check p-3 rounded-3 border border-2 border-primary bg-white shadow-sm mb-2">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="verify_goods" id="verifyGoodsCheckbox" value="1" required onchange="onVerifyGoodsChanged(this.checked)">
                        <label class="form-check-label fw-bold text-dark small cursor-pointer" for="verifyGoodsCheckbox" style="font-size: 0.8rem; cursor: pointer;">
                            <i class="bi bi-check2-all text-success me-1"></i>
                            Saya telah memeriksa dan menyatakan telah menerima seluruh barang / perlengkapan di atas secara lengkap dan dalam keadaan baik.
                        </label>
                    </div>

                    <div>
                        <label class="form-label small text-secondary mb-1" style="font-size: 0.75rem;">Catatan Penerimaan (Opsional):</label>
                        <input type="text" name="verification_note" class="form-control form-control-sm" placeholder="Contoh: Barang diterima lengkap dalam kondisi baik">
                    </div>
                </div>

                <!-- 2. TANDA TANGAN DIGITAL -->
                <div class="bento-card mb-0 p-3 border" id="signCanvasCard" style="opacity: 0.5; pointer-events: none; transition: opacity 0.2s;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold small text-dark" style="font-size: 0.82rem;">
                            <i class="bi bi-pen-fill text-primary me-1"></i> 2. TANDA TANGAN DIGITAL PETUGAS
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 rounded-pill" style="font-size: 0.72rem;" onclick="clearOfficerSignPad()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Ulangi
                        </button>
                    </div>
                    <div class="text-center">
                        <div class="border rounded-3 bg-light mb-2 position-relative" style="height: 175px; overflow: hidden;">
                            <canvas id="officerSignatureCanvas" class="w-100 h-100" style="cursor: crosshair; touch-action: none;"></canvas>
                            <div id="signCanvasPlaceholder" class="position-absolute top-50 start-50 translate-middle text-muted small user-select-none pointer-events-none" style="font-size: 0.78rem;">
                                <i class="bi bi-pen display-6 mb-1 d-block opacity-50"></i>
                                Bubuhkan tanda tangan di area ini (layar sentuh / mouse)
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small text-muted" style="font-size: 0.72rem;">
                            <span>Penerima: <strong class="text-dark">{{ auth()->user()->name }}</strong> ({{ auth()->user()->roleLabel() }})</span>
                            <span class="badge bg-secondary-subtle text-secondary">Tersimpan ke Dokumen Sah</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light border-top sticky-bottom flex-shrink-0 py-2 px-3">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal" style="font-size: 0.82rem;">Batal</button>
                <button type="submit" id="btnSubmitOfficerSign" class="btn-bps-orange rounded-pill px-4" disabled>
                    <i class="bi bi-check2-circle"></i> Simpan Tanda Tangan &amp; Verifikasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

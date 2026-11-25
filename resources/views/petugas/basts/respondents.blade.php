@extends('layouts.app')

@section('title', 'Tanda Terima Responden')
@section('page_title', 'Tanda Terima Responden')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('petugas.dashboard') }}">Petugas</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tanda Terima Responden</li>
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
        border-radius: 10px;
        padding: 0.45rem 1rem;
        font-size: 0.82rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s ease;
    }
    .btn-bps-orange:hover {
        background-color: #d95d12;
        border-color: #d95d12;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(243, 112, 33, 0.25);
    }
    .bento-table {
        table-layout: fixed;
        width: 100%;
        margin-bottom: 0;
        font-size: 0.82rem;
    }
    .bento-table thead th {
        background-color: #F8FAFC;
        color: #475569;
        font-weight: 700;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        padding: 0;
        transition: all 0.15s ease;
    }
    .bento-action-btn:hover {
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
@php
    $isPeriodOpen = isset($currentPeriod) && $currentPeriod && $currentPeriod->isOpen();
    $periodStatusMsg = isset($currentPeriod) && $currentPeriod ? $currentPeriod->status_message : 'Periode pengisian telah ditutup';
    $periodStatus = isset($currentPeriod) && $currentPeriod ? $currentPeriod->currentStatus() : 'closed';
@endphp

<!-- Status Periode Pengisian SPKRT (Req 3, 4, 5, 6, 8) -->
<div class="mb-4">
    @if($periodStatus === 'active')
        <div class="p-3 rounded-4 shadow-sm border border-success-subtle bg-white d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 p-2 text-white text-center" style="background: #16A34A; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-calendar-check-fill fs-5"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-0.5">
                        <span class="badge bg-success text-white px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                            <i class="bi bi-check-circle-fill me-1"></i> {{ $periodStatusMsg }}
                        </span>
                        <span class="fw-bold text-dark small">{{ $currentPeriod->activity_name }}</span>
                    </div>
                    <div class="text-muted" style="font-size: 0.76rem;">
                        Jadwal: {{ $currentPeriod->start_date->format('d M Y') }} s.d. {{ $currentPeriod->end_date->format('d M Y') }}
                        &bull; <span class="text-success fw-bold">Formulir siap diisi</span>
                    </div>
                </div>
            </div>
            <div class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 small">
                <i class="bi bi-unlock-fill me-1"></i> Pengisian Dibuka
            </div>
        </div>
    @elseif($periodStatus === 'not_started')
        <div class="p-3 rounded-4 shadow-sm border border-warning-subtle bg-white d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 p-2 text-dark text-center" style="background: #FEF3C7; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-hourglass-split fs-5 text-warning-emphasis"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-0.5">
                        <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                            <i class="bi bi-clock-history me-1"></i> {{ $periodStatusMsg }}
                        </span>
                        <span class="fw-bold text-dark small">{{ $currentPeriod->activity_name }}</span>
                    </div>
                    <div class="text-muted" style="font-size: 0.76rem;">
                        Pengisian baru dibuka pada tanggal <strong>{{ $currentPeriod->start_date->format('d M Y') }}</strong> s.d. {{ $currentPeriod->end_date->format('d M Y') }}.
                    </div>
                </div>
            </div>
            <div class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1.5 small">
                <i class="bi bi-lock-fill me-1"></i> Formulir Terkunci
            </div>
        </div>
    @else
        <div class="p-3 rounded-4 shadow-sm border border-danger-subtle bg-white d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 p-2 text-white text-center" style="background: #DC2626; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-lock-fill fs-5"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-0.5">
                        <span class="badge bg-danger text-white px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                            <i class="bi bi-x-circle-fill me-1"></i> {{ $periodStatusMsg }}
                        </span>
                        @if(isset($currentPeriod) && $currentPeriod)
                            <span class="fw-bold text-dark small">{{ $currentPeriod->activity_name }}</span>
                        @endif
                    </div>
                    <div class="text-muted" style="font-size: 0.76rem;">
                        @if(isset($currentPeriod) && $currentPeriod)
                            Batas waktu pengisian telah berakhir pada <strong>{{ $currentPeriod->end_date->format('d M Y') }}</strong> atau dinonaktifkan Admin.
                        @else
                            Belum ada jadwal periode pengisian SPKRT yang aktif. Silakan hubungi Administrator.
                        @endif
                    </div>
                </div>
            </div>
            <div class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1.5 small">
                <i class="bi bi-lock-fill me-1"></i> Formulir Terkunci
            </div>
        </div>
    @endif
</div>

<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-check-fill text-primary"></i> Tanda Terima Responden Lapangan
        </h3>
        <p class="text-muted small mb-0">Terbitkan dokumen resmi tanda terima responden atau wakil penerima yang Anda kunjungi, lengkap dengan tanda tangan digital.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        @if($isPeriodOpen)
            <button type="button" class="btn-bps-orange shadow-sm" data-bs-toggle="modal" data-bs-target="#createMyBastModal">
                <i class="bi bi-file-earmark-plus"></i> Buat Tanda Terima Responden
            </button>
        @else
            <button type="button" class="btn btn-secondary shadow-sm rounded-pill px-3 py-2 disabled" style="font-size: 0.82rem; cursor: not-allowed;" title="{{ $periodStatusMsg }}">
                <i class="bi bi-lock-fill me-1"></i> Formulir Terkunci ({{ $periodStatusMsg }})
            </button>
        @endif
    </div>
</div>

<!-- Table Card -->
<div class="bento-card mb-4 overflow-hidden">
    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom bg-white">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #EFF6FF; color: #005BAC;">
                <i class="bi bi-file-earmark-check"></i>
            </div>
            <h5 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">
                Daftar BAST yang Anda Terbitkan ({{ $basts->total() }})
            </h5>
        </div>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 font-monospace" style="font-size: 0.72rem;">
            Halaman {{ $basts->currentPage() }} dari {{ $basts->lastPage() ?: 1 }}
        </span>
    </div>

    <!-- MODE DESKTOP & TABLET: Tabel Anti-Geser -->
    <div class="d-none d-md-block">
        <table class="table bento-table align-middle">
            <thead>
                <tr>
                    <th style="width: 24%;">Nomor &amp; Tanggal BAST</th>
                    <th style="width: 22%;">Responden / KRT</th>
                    <th style="width: 20%;">Penerima Dokumen</th>
                    <th style="width: 14%;">Kegiatan</th>
                    <th style="width: 8%; text-align: center;">TTD</th>
                    <th style="width: 12%; text-align: right;" class="pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($basts as $item)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark font-monospace text-truncate" style="font-size: 0.8rem;">{{ $item->bast_number }}</div>
                            <div class="small text-muted" style="font-size: 0.72rem;">
                                <i class="bi bi-calendar3 me-1"></i> {{ $item->bast_date->translatedFormat('d F Y') }}
                            </div>
                            @php $respCat = strtoupper($item->handover_category ?: 'KUES'); @endphp
                            <div class="mt-1 d-flex flex-wrap gap-1 align-items-center">
                                @if(!empty($item->respondent_number))
                                    <span class="badge bg-dark-subtle text-dark border border-dark-subtle" style="font-size: 0.65rem;">
                                        No. {{ $item->respondent_number }}
                                    </span>
                                @endif
                                <span class="badge {{ $respCat === 'ATK' ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-primary-subtle text-primary-emphasis border border-primary-subtle' }}" style="font-size: 0.65rem;">
                                    {{ $respCat }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark text-truncate">{{ $item->respondent ? $item->respondent->name : '-' }}</div>
                            <div class="small text-muted text-truncate" style="font-size: 0.72rem;">{{ $item->respondent ? $item->respondent->agency : '-' }}</div>
                        </td>
                        <td>
                            @if($item->recipient_type === 'representative')
                                <div class="fw-bold text-dark text-truncate">{{ $item->recipient_name }}</div>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size: 0.68rem;">
                                    <i class="bi bi-people me-1"></i> Wakil ({{ $item->recipient_relation ?? 'Keluarga' }})
                                </span>
                            @else
                                <div class="fw-bold text-dark text-truncate">{{ $item->respondent ? $item->respondent->name : '-' }}</div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.68rem;">
                                    <i class="bi bi-person-check me-1"></i> KRT Langsung
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="small fw-semibold text-dark text-truncate d-block" style="font-size: 0.75rem;" title="{{ $item->assignment ? $item->assignment->title : '-' }}">
                                {{ $item->assignment ? $item->assignment->title : '-' }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if(!empty($item->signature_path))
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;" title="Ada TTD Digital">
                                    <i class="bi bi-pen-fill"></i> Ada
                                </span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-inline-flex gap-1.5 align-items-center">
                                <a href="{{ route('petugas.basts.respondents.download', $item) }}" 
                                   class="btn btn-outline-success bento-action-btn" 
                                   title="Unduh PDF" 
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-download"></i>
                                </a>
                                <a href="{{ route('petugas.basts.respondents.preview', $item) }}" 
                                   target="_blank" 
                                   class="btn btn-outline-primary bento-action-btn" 
                                   title="Cetak / Preview" 
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-folder2-open display-6 text-secondary opacity-50 mb-2 d-block"></i>
                            <div class="fw-semibold">Anda belum menerbitkan Tanda Terima Responden.</div>
                            <div class="small mt-1">Klik tombol "Buat Tanda Terima Responden" di atas untuk membuat dokumen.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- MODE MOBILE / HP: Daftar Kartu BAST Responsif -->
    <div class="d-block d-md-none p-3">
        @forelse($basts as $item)
            <div class="bento-card p-3 mb-3 border">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        @php $respCat = strtoupper($item->handover_category ?: 'KUES'); @endphp
                        <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                            <span class="badge bg-light text-dark font-monospace border" style="font-size: 0.72rem;">
                                {{ $item->bast_number }}
                            </span>
                            @if(!empty($item->respondent_number))
                                <span class="badge bg-dark-subtle text-dark border border-dark-subtle" style="font-size: 0.65rem;">
                                    No. {{ $item->respondent_number }}
                                </span>
                            @endif
                            <span class="badge {{ $respCat === 'ATK' ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-primary-subtle text-primary-emphasis border border-primary-subtle' }}" style="font-size: 0.65rem;">
                                {{ $respCat }}
                            </span>
                        </div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.92rem;">{{ $item->respondent ? $item->respondent->name : '-' }}</h6>
                        <small class="text-primary fw-medium" style="font-size: 0.75rem;">{{ $item->respondent ? $item->respondent->agency : '-' }}</small>
                    </div>
                    <div class="d-flex gap-1.5">
                        <a href="{{ route('petugas.basts.respondents.download', $item) }}" 
                           class="btn btn-sm btn-outline-success bento-action-btn" 
                           title="Unduh PDF">
                            <i class="bi bi-download"></i>
                        </a>
                        <a href="{{ route('petugas.basts.respondents.preview', $item) }}" 
                           target="_blank" 
                           class="btn btn-sm btn-outline-primary bento-action-btn" 
                           title="Cetak">
                            <i class="bi bi-printer"></i>
                        </a>
                    </div>
                </div>

                <!-- Penerima Info -->
                <div class="p-2 mb-2 bg-light rounded-3 small" style="font-size: 0.75rem;">
                    <span class="text-muted">Penerima:</span>
                    @if($item->recipient_type === 'representative')
                        <strong class="text-dark">{{ $item->recipient_name }}</strong>
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle ms-1" style="font-size: 0.65rem;">
                            Wakil ({{ $item->recipient_relation ?? 'Keluarga' }})
                        </span>
                    @else
                        <strong class="text-dark">{{ $item->respondent ? $item->respondent->name : '-' }}</strong>
                        <span class="badge bg-success-subtle text-success border border-success-subtle ms-1" style="font-size: 0.65rem;">
                            KRT Langsung
                        </span>
                    @endif
                </div>

                <div class="small text-secondary mb-2" style="font-size: 0.75rem;">
                    @if($item->assignment)
                        <div class="mb-1 text-truncate">
                            <i class="bi bi-briefcase text-primary me-1.5"></i>
                            <span>{{ $item->assignment->title }}</span>
                        </div>
                    @endif
                    <div class="mb-1 text-truncate">
                        <i class="bi bi-box-seam text-muted me-1.5"></i>
                        <span>{{ Str::limit($item->handover_items, 60) }}</span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <span class="text-muted small" style="font-size: 0.72rem;">
                        <i class="bi bi-calendar me-1"></i> {{ $item->bast_date->translatedFormat('d F Y') }}
                    </span>
                    <div class="d-flex gap-1 align-items-center">
                        @if(!empty($item->signature_path))
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size: 0.68rem;">
                                <i class="bi bi-pen-fill me-0.5"></i> Ada TTD
                            </span>
                        @endif
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size: 0.68rem;">
                            <i class="bi bi-check-circle-fill me-0.5"></i> Terbit
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-muted">
                <i class="bi bi-folder2-open display-6 text-secondary opacity-50 mb-2 d-block"></i>
                <div class="fw-semibold small">Anda belum menerbitkan Tanda Terima Responden</div>
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
     MODAL BUAT TANDA TERIMA RESPONDEN (SESUAI ARAHAN CLIENT)
     ============================================================== -->
<div class="modal fade" id="createMyBastModal" tabindex="-1" aria-labelledby="createMyBastModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <form method="POST" action="{{ route('petugas.basts.respondents.store') }}" id="bastCreateForm" class="modal-content border-0 shadow" style="border-radius: 18px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden;">
            @csrf
            <div class="modal-header bg-light border-bottom sticky-top flex-shrink-0 py-2.5 px-3">
                <div>
                    <h6 class="modal-title fw-bold text-dark mb-0.5" id="createMyBastModalLabel">
                        <i class="bi bi-file-earmark-plus text-primary me-1.5"></i> Tanda Terima Responden Lapangan
                    </h6>
                    <div class="text-muted" style="font-size: 0.72rem;">
                        <i class="bi bi-shield-check text-secondary me-1"></i> Rincian berkas dibakukan Admin. Tentukan penerima (KRT / Wakil) dan ambil tanda tangan digital saat kunjungan.
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3.5" style="flex: 1 1 auto; overflow-y: auto;">
                @php
                    $orderedRespondents = $myRespondents->values();
                    $sampleNumberById = $orderedRespondents->mapWithKeys(fn($r, $idx) => [$r->id => $idx + 1]);
                    $uncompletedRespondents = $orderedRespondents->filter(fn($r) => $r->basts->isEmpty());
                    $completedRespondents = $orderedRespondents->filter(fn($r) => $r->basts->isNotEmpty());
                @endphp

                <!-- 0. Pilih Cepat Nomor Urut Responden (1, 2, 3 ... 10+) -->
                @if($orderedRespondents->isNotEmpty())
                    <div class="mb-3 p-3 rounded-3 border bg-light-subtle">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-semibold text-secondary" style="font-size: 0.76rem;">PILIH NOMOR SAMPEL RESPONDEN</span>
                            <span class="small text-muted" style="font-size: 0.72rem;">Hijau = sudah TTD/BAST</span>
                        </div>
                        <div class="d-flex flex-wrap gap-1.5" id="respondentNumberPills">
                            @foreach($orderedRespondents as $idx => $resp)
                                @php
                                    $sampleNo = $idx + 1;
                                    $isDone = $resp->basts->isNotEmpty();
                                @endphp
                                <button type="button"
                                        class="btn btn-sm {{ $isDone ? 'btn-success text-white' : 'btn-outline-primary' }} fw-semibold rounded-pill px-2.5 py-1"
                                        style="font-size: 0.72rem;"
                                        data-sample-pill="{{ $resp->id }}"
                                        data-sample-number="{{ $sampleNo }}"
                                        title="No. {{ $sampleNo }}: {{ $resp->family_head_name ?? $resp->name }}"
                                        onclick="selectSamplePill({{ $resp->id }}, {{ $sampleNo }})">
                                    No. {{ $sampleNo }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- 1. Pemilihan Sampel Responden & Tanggal Dibuka -->
                <div class="row g-3 mb-3">
                    <div class="col-md-7">
                        <label class="form-label small fw-semibold text-secondary" style="font-size: 0.78rem;">
                            PILIH SAMPEL RESPONDEN <span class="text-danger">*</span>
                        </label>
                        <select name="respondent_id" id="bastRespondentSelect" class="form-select form-select-sm" required onchange="handleRespondentSelect(this)">
                            <option value="">-- Pilih Sampel Responden --</option>

                            @if($uncompletedRespondents->isNotEmpty())
                                <optgroup label="📋 BELUM BAST (Silakan Pilih)">
                                    @foreach($uncompletedRespondents as $resp)
                                        @php $sampleNo = $sampleNumberById[$resp->id] ?? null; @endphp
                                        <option value="{{ $resp->id }}" 
                                                data-number="{{ $sampleNo }}"
                                                data-name="{{ $resp->family_head_name ?? $resp->name }}" 
                                                data-informant="{{ $resp->informant_name ?? $resp->name }}"
                                                data-status="{{ $resp->respondent_status ?? 'Kepala Keluarga (KRT)' }}"
                                                data-agency="{{ $resp->agency }}"
                                                data-completed="0">
                                            No. {{ $sampleNo }} — {{ $resp->family_head_name ?? $resp->name }} @if($resp->informant_name && $resp->informant_name !== ($resp->family_head_name ?? $resp->name)) (Pemberi Info: {{ $resp->informant_name }}) @endif — {{ $resp->agency }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif

                            @if($completedRespondents->isNotEmpty())
                                <optgroup label="✅ SUDAH SELESAI BAST (Warna Hijau)">
                                    @foreach($completedRespondents as $resp)
                                        @php $sampleNo = $sampleNumberById[$resp->id] ?? null; @endphp
                                        <option value="{{ $resp->id }}" 
                                                data-number="{{ $sampleNo }}"
                                                data-name="{{ $resp->family_head_name ?? $resp->name }}" 
                                                data-informant="{{ $resp->informant_name ?? $resp->name }}"
                                                data-status="{{ $resp->respondent_status ?? 'Kepala Keluarga (KRT)' }}"
                                                data-agency="{{ $resp->agency }}"
                                                data-completed="1" 
                                                class="text-success fw-semibold">
                                            No. {{ $sampleNo }} — {{ $resp->family_head_name ?? $resp->name }} — [Sudah BAST]
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                        <div class="form-text small mt-1" id="respCompletedHint" style="display: none;">
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.72rem;">
                                <i class="bi bi-check-circle-fill me-1"></i> Responden ini sudah pernah dibuatkan BAST sebelumnya.
                            </span>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label small fw-semibold text-secondary d-flex justify-content-between align-items-center" style="font-size: 0.78rem;">
                            <span>TANGGAL TANDA TERIMA <span class="text-danger">*</span></span>
                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle" style="font-size: 0.65rem;">
                                <i class="bi bi-unlock me-1"></i>Tgl Dibuka
                            </span>
                        </label>
                        <input type="date" name="bast_date" class="form-control form-control-sm" value="{{ now()->toDateString() }}" required>
                    </div>
                </div>

                <!-- 1b. Nomor Responden & Kategori ATK / KUES -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-secondary d-flex justify-content-between align-items-center" style="font-size: 0.78rem;">
                            <span>NO. RESP (1, 2, ... 10)</span>
                            <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.65rem;">
                                <i class="bi bi-lock me-1"></i> Otomatis
                            </span>
                        </label>
                        <input type="number" name="respondent_number" id="respondentNumberInput" class="form-control form-control-sm fw-semibold" min="1" placeholder="Pilih responden" readonly style="background-color: #f8fafc; cursor: not-allowed;">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold text-secondary d-flex justify-content-between align-items-center" style="font-size: 0.78rem;">
                            <span>KATEGORI TANDA TERIMA <span class="text-danger">*</span></span>
                            <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.65rem;">
                                <i class="bi bi-lock me-1"></i> Standar Baku Admin
                            </span>
                        </label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="border rounded-3 p-2.5 d-flex align-items-start gap-2 cursor-pointer bg-light h-100" id="respCatAtkLabel" style="cursor: pointer;">
                                    <input type="radio" name="handover_category" id="respCatAtk" value="ATK" class="form-check-input mt-1" onchange="onRespCategoryChanged('ATK')">
                                    <div>
                                        <div class="fw-bold text-dark small" style="font-size: 0.8rem;"><i class="bi bi-pencil-fill text-success me-1"></i> ATK</div>
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">Paket perlengkapan responden.</small>
                                    </div>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="border rounded-3 p-2.5 d-flex align-items-start gap-2 cursor-pointer bg-white border-primary h-100" id="respCatKuesLabel" style="cursor: pointer;">
                                    <input type="radio" name="handover_category" id="respCatKues" value="KUES" class="form-check-input mt-1" checked onchange="onRespCategoryChanged('KUES')">
                                    <div>
                                        <div class="fw-bold text-dark small" style="font-size: 0.8rem;"><i class="bi bi-file-text-fill text-primary me-1"></i> KUES</div>
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">Paket kuesioner pendataan.</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Pihak yang Menerima (KRT vs Selain KRT) -->
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary d-block" style="font-size: 0.78rem;">
                        PIHAK PENERIMA BERKAS / PENANDATANGAN <span class="text-danger">*</span>
                    </label>
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <label class="border rounded-3 p-2.5 d-flex align-items-center w-100 bg-white border-primary" id="krtRadioLabel" style="cursor: pointer;">
                                <input class="form-check-input me-2 mt-0" type="radio" name="recipient_type" id="recipientTypeKrt" value="krt" checked onchange="toggleRecipientType()">
                                <div>
                                    <div class="fw-bold text-dark small" style="font-size: 0.82rem;"><i class="bi bi-person-fill text-primary me-1"></i> Sesuai Kepala Keluarga (KRT)</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Nama penerima otomatis sama dg KRT</div>
                                </div>
                            </label>
                        </div>
                        <div class="col-sm-6">
                            <label class="border rounded-3 p-2.5 d-flex align-items-center w-100 bg-light" id="repRadioLabel" style="cursor: pointer;">
                                <input class="form-check-input me-2 mt-0" type="radio" name="recipient_type" id="recipientTypeRep" value="representative" onchange="toggleRecipientType()">
                                <div>
                                    <div class="fw-bold text-dark small" style="font-size: 0.82rem;"><i class="bi bi-people-fill text-warning me-1"></i> Selain Kepala Keluarga (Wakil)</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Diterima oleh pasangan / anak / ART</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Input: Jika Penerima adalah Selain KRT (Wakil) -->
                <div id="representativeFields" class="p-3 mb-3 border rounded-3 bg-light border-warning" style="display: none;">
                    <h6 class="fw-bold text-dark mb-2 small" style="font-size: 0.82rem;">
                        <i class="bi bi-person-badge text-warning me-1"></i> Data Identitas Penerima (Wakil KRT)
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary" style="font-size: 0.78rem;">NAMA PENERIMA YANG MENANDATANGANI <span class="text-danger">*</span></label>
                            <input type="text" name="recipient_name" id="recipientNameInput" class="form-control form-control-sm" placeholder="Contoh: Nama Keluarga">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary" style="font-size: 0.78rem;">STATUS / HUBUNGAN DENGAN KRT <span class="text-danger">*</span></label>
                            <input type="text" name="recipient_relation" id="recipientRelationInput" class="form-control form-control-sm" placeholder="Contoh: Istri / Pasangan / Anak">
                        </div>
                    </div>
                </div>

                <!-- 3. Rincian Dokumen / Barang Serah Terima (Terkunci dari Admin) -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label small fw-semibold text-secondary mb-0" style="font-size: 0.78rem;">
                            RINCIAN DOKUMEN / BARANG YANG DISERAHKAN <span class="text-danger">*</span>
                        </label>
                        <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.65rem;">
                            <i class="bi bi-lock me-1"></i> Terkunci dari Admin
                        </span>
                    </div>
                    <textarea name="handover_items" id="respHandoverItemsInput" class="form-control" rows="2" readonly style="background-color: #f8fafc; cursor: not-allowed; border-color: #cbd5e1; font-size: 0.82rem;" required>1. Formulir Kuesioner Pendataan Lapangan (1 Berkas)
2. Bukti Kelengkapan Berkas Administrasi Responden</textarea>
                </div>

                <!-- 4. Tanda Tangan Digital Touch Canvas -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label small fw-semibold text-secondary mb-0" style="font-size: 0.78rem;">
                            TANDA TANGAN DIGITAL PENERIMA / RESPONDEN
                        </label>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill" style="font-size: 0.72rem;" onclick="clearBastSignaturePad()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Hapus / Ulangi
                        </button>
                    </div>
                    <div class="border rounded-3 p-1 bg-light text-center">
                        <canvas id="bastSignatureCanvas" width="450" height="150" class="border bg-white rounded-2 w-100" style="touch-action: none; cursor: crosshair; max-height: 160px;"></canvas>
                        <input type="hidden" name="signature_data" id="bastSignatureData">
                        <div class="text-muted py-1" style="font-size: 0.72rem;">
                            <i class="bi bi-fingerprint text-primary me-1"></i> Bubuhkan tanda tangan langsung menggunakan jari di layar HP
                        </div>
                    </div>
                </div>

                <!-- 5. Catatan Tambahan (Opsional) -->
                <div class="mb-0">
                    <label class="form-label small fw-semibold text-secondary" style="font-size: 0.78rem;">CATATAN TAMBAHAN (OPSIONAL)</label>
                    <input type="text" name="notes" class="form-control form-control-sm" placeholder="Catatan kondisi berkas serah terima...">
                </div>
            </div>
            <div class="modal-footer bg-light border-top sticky-bottom flex-shrink-0 py-2 px-3">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal" style="font-size: 0.82rem;">Batal</button>
                @if($isPeriodOpen)
                    <button type="submit" class="btn-bps-orange rounded-pill px-4">
                        <i class="bi bi-send-fill"></i> Kirim &amp; Terbitkan BAST
                    </button>
                @else
                    <button type="button" class="btn btn-secondary rounded-pill px-4" disabled>
                        <i class="bi bi-lock-fill"></i> Formulir Terkunci ({{ $periodStatusMsg }})
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle Pihak Penerima (KRT vs Wakil)
    function toggleRecipientType() {
        const isRep = document.getElementById('recipientTypeRep').checked;
        const repFields = document.getElementById('representativeFields');
        const krtLabel = document.getElementById('krtRadioLabel');
        const repLabel = document.getElementById('repRadioLabel');
        const nameInput = document.getElementById('recipientNameInput');
        const relationInput = document.getElementById('recipientRelationInput');

        if (isRep) {
            repFields.style.display = 'block';
            krtLabel.classList.remove('border-primary', 'bg-white');
            krtLabel.classList.add('bg-light');
            repLabel.classList.add('border-warning', 'bg-white');
            repLabel.classList.remove('bg-light');
            nameInput.setAttribute('required', 'required');
            relationInput.setAttribute('required', 'required');
        } else {
            repFields.style.display = 'none';
            repLabel.classList.remove('border-warning', 'bg-white');
            repLabel.classList.add('bg-light');
            krtLabel.classList.add('border-primary', 'bg-white');
            krtLabel.classList.remove('bg-light');
            nameInput.removeAttribute('required');
            relationInput.removeAttribute('required');
        }
    }

    const RESP_CATEGORY_TEMPLATES = {
        ATK: "1. Map / Perlengkapan Pencacahan Responden (1 Set)\n2. Bolpoin / Alat Tulis Lapangan (1 Buah)\n3. Bukti Tanda Terima Perlengkapan Responden",
        KUES: "1. Formulir Kuesioner Pendataan Lapangan (1 Berkas)\n2. Bukti Kelengkapan Berkas Administrasi Responden"
    };

    function selectSamplePill(respondentId, sampleNumber) {
        const select = document.getElementById('bastRespondentSelect');
        if (select) {
            select.value = String(respondentId);
            handleRespondentSelect(select);
        }
        const numInput = document.getElementById('respondentNumberInput');
        if (numInput && sampleNumber) {
            numInput.value = sampleNumber;
        }
    }

    function highlightSamplePill(respondentId) {
        document.querySelectorAll('#respondentNumberPills [data-sample-pill]').forEach((btn) => {
            const isSelected = String(btn.getAttribute('data-sample-pill')) === String(respondentId);
            btn.classList.toggle('shadow', isSelected);
            btn.classList.toggle('border-2', isSelected);
            btn.classList.toggle('border-dark', isSelected);
        });
    }

    function onRespCategoryChanged(cat) {
        const normalized = (cat || 'KUES').toUpperCase();
        const atkLabel = document.getElementById('respCatAtkLabel');
        const kuesLabel = document.getElementById('respCatKuesLabel');
        if (atkLabel && kuesLabel) {
            atkLabel.classList.toggle('bg-white', normalized === 'ATK');
            atkLabel.classList.toggle('border-success', normalized === 'ATK');
            atkLabel.classList.toggle('bg-light', normalized !== 'ATK');
            kuesLabel.classList.toggle('bg-white', normalized === 'KUES');
            kuesLabel.classList.toggle('border-primary', normalized === 'KUES');
            kuesLabel.classList.toggle('bg-light', normalized !== 'KUES');
        }
        const textarea = document.getElementById('respHandoverItemsInput');
        if (textarea) {
            textarea.value = RESP_CATEGORY_TEMPLATES[normalized] || RESP_CATEGORY_TEMPLATES.KUES;
        }
    }

    // Handle Respondent Select Change
    function handleRespondentSelect(select) {
        const selectedOpt = select.options[select.selectedIndex];
        if (!selectedOpt || !selectedOpt.value) {
            highlightSamplePill('');
            return;
        }

        highlightSamplePill(selectedOpt.value);
        const sampleNum = selectedOpt.getAttribute('data-number');
        const numInput = document.getElementById('respondentNumberInput');
        if (numInput && sampleNum) {
            numInput.value = sampleNum;
        }

        const isCompleted = selectedOpt.getAttribute('data-completed') === '1';
        const hint = document.getElementById('respCompletedHint');
        if (hint) {
            hint.style.display = isCompleted ? 'block' : 'none';
        }

        const informant = selectedOpt.getAttribute('data-informant');
        const status = selectedOpt.getAttribute('data-status');
        const headName = selectedOpt.getAttribute('data-name');

        if (informant && headName && informant.trim().toLowerCase() !== headName.trim().toLowerCase()) {
            const repRadio = document.getElementById('recipientTypeRep');
            if (repRadio) repRadio.checked = true;
            const nameInp = document.getElementById('recipientNameInput');
            if (nameInp) nameInp.value = informant;
            const relInp = document.getElementById('recipientRelationInput');
            if (relInp) relInp.value = status || 'Wakil Keluarga';
            toggleRecipientType();
        } else {
            const krtRadio = document.getElementById('recipientTypeKrt');
            if (krtRadio) krtRadio.checked = true;
            toggleRecipientType();
        }
    }

    // Touch-Friendly HTML5 Canvas Signature Pad
    let bastSigPad = null;
    function setupBastSignaturePad(canvasId, inputId) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return null;
        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let hasDrawn = false;

        function resize() {
            const rect = canvas.getBoundingClientRect();
            if (rect.width > 0 && rect.width !== canvas.width) {
                const temp = canvas.toDataURL();
                canvas.width = rect.width;
                canvas.height = 150;
                ctx.lineWidth = 2.5;
                ctx.lineCap = 'round';
                ctx.strokeStyle = '#0d2142';
                if (hasDrawn) {
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

        setTimeout(resize, 300);

        function getCoords(e) {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return {
                x: clientX - rect.left,
                y: clientY - rect.top
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
            const input = document.getElementById(inputId);
            if (input && hasDrawn) {
                input.value = canvas.toDataURL('image/png');
            }
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
                hasDrawn = false;
                const input = document.getElementById(inputId);
                if (input) input.value = '';
            },
            hasDrawn: function() { return hasDrawn; },
            toDataURL: function() { return canvas.toDataURL('image/png'); }
        };
    }

    function clearBastSignaturePad() {
        if (bastSigPad) bastSigPad.clear();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('createMyBastModal');
        if (modal) {
            modal.addEventListener('shown.bs.modal', function () {
                bastSigPad = setupBastSignaturePad('bastSignatureCanvas', 'bastSignatureData');
            });
        }

        const form = document.getElementById('bastCreateForm');
        if (form) {
            form.addEventListener('submit', function() {
                if (bastSigPad && bastSigPad.hasDrawn()) {
                    document.getElementById('bastSignatureData').value = bastSigPad.toDataURL();
                }
            });
        }
    });
</script>
@endpush

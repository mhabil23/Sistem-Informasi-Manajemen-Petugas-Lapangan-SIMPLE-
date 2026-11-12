@extends('layouts.app')

@section('title', 'BAST Responden Saya')
@section('page_title', 'BAST Responden')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('petugas.dashboard') }}">Petugas</a></li>
    <li class="breadcrumb-item active" aria-current="page">BAST Responden</li>
@endsection

@section('content')
<!-- Header -->
<div class="row align-items-center mb-3">
    <div class="col-md-7">
        <h3 class="fw-bold text-dark mb-1">Berita Acara Serah Terima (BAST) Responden</h3>
        <p class="text-muted small mb-0">Terbitkan dokumen resmi BAST dengan responden atau wakil penerima yang Anda kunjungi, lengkap dengan tanda tangan digital.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <button type="button" class="btn btn-sp-primary" data-bs-toggle="modal" data-bs-target="#createMyBastModal">
            <i class="fa-solid fa-file-circle-plus me-1"></i> Buat BAST Responden
        </button>
    </div>
</div>

<!-- Stat Cards (Status Penyelesaian Sampel Responden) -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-solid fa-file-signature"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Total BAST Terbit</div>
                <div class="sp-stat-value">{{ number_format($basts->total()) }}</div>
                <div class="sp-stat-desc">Dokumen selesai</div>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Sampel Selesai BAST</div>
                <div class="sp-stat-value text-success">{{ number_format($completedCount ?? 0) }}</div>
                <div class="sp-stat-desc">Sudah ada berita acara</div>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #fef3c7; color: #d97706;">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Sampel Belum BAST</div>
                <div class="sp-stat-value text-warning">{{ number_format($pendingCount ?? 0) }}</div>
                <div class="sp-stat-desc">Perlu dibuatkan BAST</div>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="sp-card mb-4">
    <div class="sp-card-header d-flex justify-content-between align-items-center">
        <h5 class="sp-card-title mb-0">
            <i class="fa-solid fa-file-contract text-primary"></i>
            Daftar BAST yang Anda Terbitkan ({{ $basts->total() }})
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
                        <th style="min-width: 200px;">Nomor & Tanggal BAST</th>
                        <th style="min-width: 200px;">Responden / KRT</th>
                        <th style="min-width: 200px;">Penerima Dokumen</th>
                        <th>Kegiatan Terkait</th>
                        <th>Tanda Tangan</th>
                        <th class="text-end" style="min-width: 130px;">Aksi</th>
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
                                @if(!empty($item->signature_path))
                                    <span class="badge bg-light text-success border border-success-subtle">
                                        <i class="fa-solid fa-signature me-1"></i> Ada TTD
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="sp-action-btn-group">
                                    <a href="{{ route('petugas.basts.respondents.download', $item) }}" 
                                       class="btn btn-light border sp-action-btn text-success" 
                                       title="Unduh PDF" 
                                       data-bs-toggle="tooltip">
                                        <i class="fa-solid fa-file-arrow-down"></i>
                                    </a>
                                    <a href="{{ route('petugas.basts.respondents.preview', $item) }}" 
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
                                <div class="fw-semibold">Anda belum menerbitkan BAST Responden.</div>
                                <div class="small">Klik tombol "Buat BAST Responden" di atas untuk membuat dokumen.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MODE MOBILE / HP: Daftar Kartu BAST Responsif (No Horizontal Scroll) -->
        <div class="d-block d-md-none p-3">
            @forelse($basts as $item)
                <div class="card border rounded-3 mb-3 shadow-none bg-white">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                @php $respCat = strtoupper($item->handover_category ?: 'KUES'); @endphp
                                <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                                    <span class="badge bg-light text-dark font-monospace border" style="font-size: 0.75rem;">
                                        {{ $item->bast_number }}
                                    </span>
                                    @if(!empty($item->respondent_number))
                                        <span class="badge bg-dark-subtle text-dark border border-dark-subtle" style="font-size: 0.68rem;">
                                            No. {{ $item->respondent_number }}
                                        </span>
                                    @endif
                                    <span class="badge {{ $respCat === 'ATK' ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-primary-subtle text-primary-emphasis border border-primary-subtle' }}" style="font-size: 0.68rem;">
                                        {{ $respCat }}
                                    </span>
                                </div>
                                <h6 class="fw-bold text-dark mb-0">{{ $item->respondent ? $item->respondent->name : '-' }}</h6>
                                <small class="text-primary fw-medium">{{ $item->respondent ? $item->respondent->agency : '-' }}</small>
                            </div>
                            <div class="d-flex gap-1.5">
                                <a href="{{ route('petugas.basts.respondents.download', $item) }}" 
                                   class="btn btn-sm btn-light border text-success shadow-sm" 
                                   title="Unduh PDF">
                                    <i class="fa-solid fa-file-arrow-down"></i>
                                </a>
                                <a href="{{ route('petugas.basts.respondents.preview', $item) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-light border text-primary shadow-sm" 
                                   title="Cetak">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Penerima Info -->
                        <div class="p-2 mb-2 bg-light rounded-2 small">
                            <span class="text-muted">Penerima:</span>
                            @if($item->recipient_type === 'representative')
                                <strong>{{ $item->recipient_name }}</strong>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle ms-1" style="font-size: 0.68rem;">
                                    Wakil ({{ $item->recipient_relation ?? 'Keluarga' }})
                                </span>
                            @else
                                <strong>{{ $item->respondent ? $item->respondent->name : '-' }}</strong>
                                <span class="badge bg-success-subtle text-success border border-success-subtle ms-1" style="font-size: 0.68rem;">
                                    KRT Langsung
                                </span>
                            @endif
                        </div>

                        <div class="small text-secondary mb-2">
                            @if($item->assignment)
                                <div class="mb-1 text-truncate">
                                    <i class="fa-solid fa-briefcase text-primary me-1.5" style="width: 14px;"></i>
                                    <span>{{ $item->assignment->title }}</span>
                                </div>
                            @endif
                            <div class="mb-1">
                                <i class="fa-solid fa-box-archive text-muted me-1.5" style="width: 14px;"></i>
                                <span>{{ Str::limit($item->handover_items, 60) }}</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="text-muted small" style="font-size: 0.75rem;">
                                <i class="fa-regular fa-calendar me-1"></i> {{ $item->bast_date->translatedFormat('d F Y') }}
                            </span>
                            <div>
                                @if(!empty($item->signature_path))
                                    <span class="badge bg-light text-success border border-success-subtle px-2 py-1" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-signature me-1"></i> Ada TTD
                                    </span>
                                @endif
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 0.72rem;">
                                    <i class="fa-solid fa-circle-check me-1"></i> Terbit
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <i class="fa-regular fa-folder-open fa-2x mb-2 text-secondary opacity-50"></i>
                    <div class="fw-semibold small">Anda belum menerbitkan BAST Responden</div>
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
     MODAL BUAT BAST RESPONDEN (SESUAI ARAHAN CLIENT)
     ============================================================== -->
<div class="modal fade" id="createMyBastModal" tabindex="-1" aria-labelledby="createMyBastModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="createMyBastModalLabel">
                        <i class="fa-solid fa-file-circle-plus text-primary me-2"></i> Buat BAST Responden Baru
                    </h5>
                    <div class="small text-muted">Pilih sampel responden, tentukan penerima (KRT vs Wakil), dan bubuhkan tanda tangan digital</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('petugas.basts.respondents.store') }}" id="bastCreateForm">
                @csrf
                <div class="modal-body p-4">
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
                                <span class="small fw-semibold text-secondary">PILIH NOMOR SAMPEL RESPONDEN</span>
                                <span class="small text-muted">Hijau = sudah TTD/BAST</span>
                            </div>
                            <div class="d-flex flex-wrap gap-1.5" id="respondentNumberPills">
                                @foreach($orderedRespondents as $idx => $resp)
                                    @php
                                        $sampleNo = $idx + 1;
                                        $isDone = $resp->basts->isNotEmpty();
                                    @endphp
                                    <button type="button"
                                            class="btn btn-sm {{ $isDone ? 'btn-success text-white' : 'btn-outline-primary' }} fw-semibold rounded-pill px-2.5 py-1"
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
                            <label class="form-label small fw-semibold text-secondary">
                                PILIH SAMPEL RESPONDEN <span class="text-danger">*</span>
                            </label>
                            <select name="respondent_id" id="bastRespondentSelect" class="form-select" required onchange="handleRespondentSelect(this)">
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

                                {{-- Prioritas 2: Responden yang SUDAH Selesai BAST (Warna Hijau) --}}
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
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="fa-solid fa-circle-check me-1"></i> Responden ini sudah pernah dibuatkan BAST sebelumnya.
                                </span>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary d-flex justify-content-between align-items-center">
                                <span>TANGGAL TANDA TERIMA <span class="text-danger">*</span></span>
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle" style="font-size: 0.65rem;">
                                    <i class="fa-solid fa-lock-open me-1"></i>Tgl Dibuka
                                </span>
                            </label>
                            <input type="date" name="bast_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                    </div>

                    <!-- 1b. Nomor Responden & Kategori ATK / KUES -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">NO. RESP (1, 2, ... 10)</label>
                            <input type="number" name="respondent_number" id="respondentNumberInput" class="form-control fw-semibold" min="1" placeholder="Otomatis / isi manual">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-secondary">KATEGORI TANDA TERIMA <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="border rounded-3 p-2.5 d-flex align-items-start gap-2 cursor-pointer bg-light h-100" id="respCatAtkLabel">
                                        <input type="radio" name="handover_category" id="respCatAtk" value="ATK" class="form-check-input mt-1" onchange="onRespCategoryChanged('ATK')">
                                        <div>
                                            <div class="fw-bold text-dark small"><i class="fa-solid fa-pen-ruler text-success me-1"></i> ATK</div>
                                            <small class="text-muted d-block" style="font-size: 0.72rem;">Alat tulis &amp; perlengkapan responden.</small>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <label class="border rounded-3 p-2.5 d-flex align-items-start gap-2 cursor-pointer bg-white border-primary h-100" id="respCatKuesLabel">
                                        <input type="radio" name="handover_category" id="respCatKues" value="KUES" class="form-check-input mt-1" checked onchange="onRespCategoryChanged('KUES')">
                                        <div>
                                            <div class="fw-bold text-dark small"><i class="fa-solid fa-file-lines text-primary me-1"></i> KUES</div>
                                            <small class="text-muted d-block" style="font-size: 0.72rem;">Kuesioner / formulir pendataan.</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Pihak yang Menerima (KRT vs Selain KRT) -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary d-block">
                            PIHAK PENERIMA BERKAS / PENANDATANGAN <span class="text-danger">*</span>
                        </label>
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <label class="border rounded-3 p-3 d-flex align-items-center w-100 cursor-pointer bg-light" id="krtRadioLabel" style="cursor: pointer;">
                                    <input class="form-check-input me-2.5 mt-0" type="radio" name="recipient_type" id="recipientTypeKrt" value="krt" checked onchange="toggleRecipientType()">
                                    <div>
                                        <div class="fw-bold text-dark small"><i class="fa-solid fa-user me-1 text-primary"></i> Sesuai Kepala Keluarga (KRT)</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">Nama penerima otomatis sama dengan nama responden</div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="border rounded-3 p-3 d-flex align-items-center w-100 cursor-pointer bg-light" id="repRadioLabel" style="cursor: pointer;">
                                    <input class="form-check-input me-2.5 mt-0" type="radio" name="recipient_type" id="recipientTypeRep" value="representative" onchange="toggleRecipientType()">
                                    <div>
                                        <div class="fw-bold text-dark small"><i class="fa-solid fa-users me-1 text-warning"></i> Selain Kepala Keluarga (Wakil)</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">Diterima oleh pasangan / anak / anggota keluarga lain</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Input: Jika Penerima adalah Selain KRT (Wakil) -->
                    <div id="representativeFields" class="p-3 mb-3 border rounded-3 bg-light border-warning" style="display: none;">
                        <h6 class="fw-bold text-dark mb-2 small">
                            <i class="fa-solid fa-address-card text-warning me-1"></i> Data Identitas Penerima (Wakil KRT)
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">NAMA PENERIMA YANG MENANDATANGANI <span class="text-danger">*</span></label>
                                <input type="text" name="recipient_name" id="recipientNameInput" class="form-control" placeholder="Contoh: Siti Rogayah">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">STATUS / HUBUNGAN DENGAN KRT <span class="text-danger">*</span></label>
                                <input type="text" name="recipient_relation" id="recipientRelationInput" class="form-control" placeholder="Contoh: Istri / Pasangan / Anak">
                            </div>
                        </div>
                    </div>

                    <!-- 3. Rincian Dokumen / Barang Serah Terima -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">RINCIAN DOKUMEN / BARANG YANG DISERAHKAN <span class="text-danger">*</span></label>
                        <textarea name="handover_items" id="respHandoverItemsInput" class="form-control" rows="3" placeholder="Contoh:&#10;1. Kuesioner Formulir Pendataan Lapangan Terpadu (1 Rangkap)&#10;2. Salinan KTP & KK Penanggung Jawab Usaha&#10;3. Surat Keterangan Domisili Usaha" required oninput="handoverItemsDirty = true">1. Formulir Kuesioner Pendataan Lapangan (1 Berkas)
2. Bukti Kelengkapan Berkas Administrasi Responden</textarea>
                    </div>

                    <!-- 4. Tanda Tangan Digital Touch Canvas -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-semibold text-secondary mb-0">
                                TANDA TANGAN DIGITAL PENERIMA / RESPONDEN
                            </label>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;" onclick="clearBastSignaturePad()">
                                <i class="fa-solid fa-rotate-left me-1"></i> Hapus / Ulangi
                            </button>
                        </div>
                        <div class="border rounded-3 p-1 bg-light text-center">
                            <canvas id="bastSignatureCanvas" width="450" height="150" class="border bg-white rounded-2 w-100" style="touch-action: none; cursor: crosshair; max-height: 160px;"></canvas>
                            <input type="hidden" name="signature_data" id="bastSignatureData">
                            <div class="small text-muted py-1" style="font-size: 0.72rem;">
                                <i class="fa-solid fa-fingerprint me-1 text-primary"></i> Bubuhkan tanda tangan langsung menggunakan jari di layar HP
                            </div>
                        </div>
                    </div>

                    <!-- 5. Catatan Tambahan (Opsional) -->
                    <div class="mb-0">
                        <label class="form-label small fw-semibold text-secondary">CATATAN TAMBAHAN (OPSIONAL)</label>
                        <input type="text" name="notes" class="form-control" placeholder="Catatan kondisi berkas serah terima...">
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary">
                        <i class="fa-solid fa-paper-plane me-1"></i> Kirim & Terbitkan BAST
                    </button>
                </div>
            </form>
        </div>
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
            repLabel.classList.add('border-warning', 'bg-white');
            nameInput.setAttribute('required', 'required');
            relationInput.setAttribute('required', 'required');
        } else {
            repFields.style.display = 'none';
            repLabel.classList.remove('border-warning', 'bg-white');
            krtLabel.classList.add('border-primary', 'bg-white');
            nameInput.removeAttribute('required');
            relationInput.removeAttribute('required');
        }
    }

    let handoverItemsDirty = false;

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
        if (textarea && !handoverItemsDirty) {
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

        // Resize canvas to match display width
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

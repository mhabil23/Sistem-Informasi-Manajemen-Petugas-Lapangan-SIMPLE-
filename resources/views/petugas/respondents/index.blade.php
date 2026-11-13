@extends('layouts.app')

@section('title', 'Data Responden Lapangan')
@section('page_title', 'Data Responden Lapangan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('petugas.dashboard') }}">Petugas</a></li>
    <li class="breadcrumb-item active" aria-current="page">Data Responden</li>
@endsection

@section('content')
<!-- Header -->
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-house-chimney-user text-primary me-2"></i>Daftar Sampel Ruta (Responden)
        </h3>
        <p class="text-muted small mb-0">Daftar sampel rumah tangga (ruta) resmi yang dialokasikan oleh Administrator BPS. Lakukan verifikasi data lapangan dan terbitkan Berita Acara Serah Terima (BAST).</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex justify-content-md-end gap-2 align-items-center">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle py-2 px-3 fw-semibold d-none d-sm-inline-block">
            <i class="fa-solid fa-database me-1"></i> Alokasi Sampel BPS
        </span>
        <a href="{{ route('petugas.basts.respondents.index') }}" class="btn btn-success shadow-sm">
            <i class="fa-solid fa-file-signature me-1"></i> Menu Tanda Terima Responden
        </a>
    </div>
</div>

<!-- Stat Cards (4 Cards Balanced) -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-solid fa-bullseye"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Target Kuota</div>
                <div class="sp-stat-value text-primary">{{ number_format($targetQuota ?? 120) }}</div>
                <div class="sp-stat-desc">Alokasi responden Anda</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Responden Terdata</div>
                <div class="sp-stat-value text-success">{{ number_format($totalMyRespondents) }}</div>
                <div class="sp-stat-desc">Telah diinput di sistem</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #fef3c7; color: #d97706;">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Sisa Target</div>
                <div class="sp-stat-value text-warning">{{ number_format($remainingTarget ?? max(0, ($targetQuota ?? 120) - $totalMyRespondents)) }}</div>
                <div class="sp-stat-desc">Belum diverifikasi</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0e7ff; color: #4338ca;">
                <i class="fa-solid fa-file-signature"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">BAST Terbit</div>
                <div class="sp-stat-value text-indigo" style="color: #4338ca;">{{ number_format($withBastCount) }}</div>
                <div class="sp-stat-desc">Sudah ada berita acara</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="sp-card mb-4">
    <div class="sp-card-body p-3">
        <form method="GET" action="{{ route('petugas.respondents.index') }}" class="row g-2 align-items-center">
            <div class="col-lg-6 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="form-control border-start-0 ps-0" 
                           placeholder="Cari nama responden, instansi, alamat, nomor HP..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <select name="assignment_id" class="form-select">
                    <option value="">-- Semua Kegiatan Lapangan --</option>
                    @foreach($myAssignments as $asgn)
                        <option value="{{ $asgn->id }}" {{ request('assignment_id') == $asgn->id ? 'selected' : '' }}>
                            {{ $asgn->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if(request('search') || request('assignment_id'))
                    <a href="{{ route('petugas.respondents.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
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
            <i class="fa-solid fa-id-card text-primary"></i>
            Daftar Responden yang Anda Input ({{ $respondents->total() }})
        </h5>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle d-md-none" style="font-size: 0.72rem;">
            {{ $respondents->total() }} Data
        </span>
    </div>
    <div class="sp-card-body p-0">
        <!-- MODE DESKTOP & TABLET: Tabel Data Lengkap -->
        <div class="table-responsive d-none d-md-block">
            <table class="table sp-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="min-width: 220px;">Kepala Keluarga &amp; Responden</th>
                        <th style="min-width: 170px;">Instansi / Lembaga</th>
                        <th style="min-width: 220px;">Alamat</th>
                        <th>Kegiatan Terkait</th>
                        <th>Tanggal Input</th>
                        <th>Status BAST</th>
                        <th class="text-end" style="min-width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($respondents as $resp)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $resp->family_head_name ?? $resp->name }}</div>
                                <div class="small my-1">
                                    <span class="badge bg-light text-dark border me-1">
                                        <i class="fa-solid fa-user me-1 text-primary"></i> {{ $resp->informant_name ?? $resp->name }}
                                    </span>
                                    <span class="badge bg-secondary-subtle text-secondary border">
                                        {{ $resp->respondent_status ?? 'KRT' }}
                                    </span>
                                </div>
                                <small class="text-muted"><i class="fa-solid fa-phone me-1"></i> {{ $resp->phone ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border">
                                    {{ $resp->agency }}
                                </span>
                            </td>
                            <td>
                                <div class="small text-secondary text-truncate" style="max-width: 240px;" title="{{ $resp->address }}">
                                    <i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $resp->address }}
                                </div>
                            </td>
                            <td>
                                <span class="small fw-semibold text-dark text-truncate d-block" style="max-width: 180px;">
                                    {{ $resp->assignment ? $resp->assignment->title : '-' }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ $resp->input_date->format('d/m/Y') }}</td>
                            <td>
                                @if($resp->basts->isNotEmpty())
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="fa-solid fa-check me-1"></i> {{ $resp->basts->count() }} BAST
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                        Belum Ada
                                    </span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="sp-action-btn-group">
                                    <!-- Verifikasi / Edit Data Lapangan -->
                                    <button type="button" 
                                            class="btn btn-light border sp-action-btn text-warning" 
                                            title="Verifikasi / Update Lapangan" 
                                            data-bs-toggle="tooltip"
                                            onclick="openEditMyRespondentModal({{ json_encode($resp) }})">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>
                                    <!-- Shortcut Tanda Terima Responden -->
                                    <a href="{{ route('petugas.basts.respondents.index') }}" 
                                       class="btn btn-light border sp-action-btn text-success" 
                                       title="Buka / Terbitkan Tanda Terima" 
                                       data-bs-toggle="tooltip">
                                        <i class="fa-solid fa-file-signature"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-users-slash fa-3x mb-3 text-secondary opacity-50"></i>
                                <div class="fw-semibold">
                                    @if(request('search') || request('assignment_id'))
                                        Tidak ditemukan data sampel responden yang sesuai dengan filter pencarian.
                                    @else
                                        Belum ada sampel rumah tangga (ruta) yang dialokasikan oleh Administrator untuk Anda.
                                    @endif
                                </div>
                                <div class="small mt-1">
                                    @if(request('search') || request('assignment_id'))
                                        Silakan reset filter atau cari dengan kata kunci lain.
                                    @else
                                        Daftar sampel responden akan muncul otomatis setelah Administrator Satker BPS mengalokasikan sampel kegiatan kepada akun Anda.
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MODE MOBILE / HP: Daftar Kartu Responsif (Pas di Layar HP & Tanpa Geser ke Kanan) -->
        <div class="d-block d-md-none p-3">
            @forelse($respondents as $resp)
                <div class="card border rounded-3 mb-3 shadow-none bg-white">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold text-dark mb-1">{{ $resp->family_head_name ?? $resp->name }}</h6>
                                <div class="small text-muted mb-1">
                                    <span class="badge bg-light text-dark border" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-user me-1 text-primary"></i> {{ $resp->informant_name ?? $resp->name }}
                                    </span>
                                    <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.72rem;">
                                        {{ $resp->respondent_status ?? 'KRT' }}
                                    </span>
                                </div>
                                <span class="badge bg-light text-primary border" style="font-size: 0.72rem;">
                                    <i class="fa-solid fa-building me-1"></i> {{ $resp->agency }}
                                </span>
                            </div>
                            <div class="d-flex gap-1.5">
                                <button type="button" 
                                        class="btn btn-sm btn-light border text-warning shadow-sm" 
                                        title="Edit Data Responden" 
                                        onclick="openEditMyRespondentModal({{ json_encode($resp) }})">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </button>
                                @if($resp->phone)
                                    <a href="tel:{{ $resp->phone }}" class="btn btn-sm btn-light border text-success shadow-sm" title="Telepon">
                                        <i class="fa-solid fa-phone"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="small text-secondary mb-2.5">
                            @if($resp->phone)
                                <div class="mb-1">
                                    <i class="fa-solid fa-phone text-muted me-1.5" style="width: 14px;"></i>
                                    <span>{{ $resp->phone }}</span>
                                </div>
                            @endif
                            <div class="mb-1">
                                <i class="fa-solid fa-location-dot text-danger me-1.5" style="width: 14px;"></i>
                                <span>{{ $resp->address }}</span>
                            </div>
                            @if($resp->assignment)
                                <div class="mb-1 text-truncate">
                                    <i class="fa-solid fa-briefcase text-primary me-1.5" style="width: 14px;"></i>
                                    <span class="fw-semibold text-dark">{{ $resp->assignment->title }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="text-muted small" style="font-size: 0.75rem;">
                                <i class="fa-regular fa-calendar me-1"></i> {{ $resp->input_date->format('d/m/Y') }}
                            </span>
                            <div>
                                @if($resp->basts->isNotEmpty())
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-check me-1"></i> {{ $resp->basts->count() }} BAST Terbit
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1" style="font-size: 0.75rem;">
                                        Belum Ada BAST
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <i class="fa-solid fa-users-slash fa-2x mb-2 text-secondary opacity-50"></i>
                    <div class="fw-semibold small">
                        @if(request('search') || request('assignment_id'))
                            Tidak ada sampel responden sesuai filter pencarian.
                        @else
                            Belum ada sampel rumah tangga (ruta) yang dialokasikan oleh Administrator untuk Anda.
                        @endif
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    @if($respondents->hasPages())
        <div class="sp-card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Menampilkan {{ $respondents->firstItem() }} sampai {{ $respondents->lastItem() }} dari {{ $respondents->total() }} responden
            </div>
            <div>
                {{ $respondents->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

<!-- ==============================================================
     MODAL TAMBAH RESPONDEN (PETUGAS)
     ============================================================== -->
<div class="modal fade" id="createMyRespondentModal" tabindex="-1" aria-labelledby="createMyRespondentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="createMyRespondentModalLabel">
                    <i class="fa-solid fa-user-plus text-primary me-2"></i> Input Data Responden Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('petugas.respondents.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <!-- Baris 1: Nama KRT & No HP -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="fa-solid fa-house-user text-primary me-1"></i> NAMA KEPALA KELUARGA (KRT) <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="create_resp_name" name="name" class="form-control" placeholder="Contoh: Jamaluddin Ali" required>
                            <div class="form-text small">Nama kepala keluarga sesuai daftar sampel sensus/survei.</div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="fa-solid fa-phone text-muted me-1"></i> NOMOR HP / WHATSAPP
                            </label>
                            <input type="text" name="phone" class="form-control" placeholder="Contoh: 082112345678">
                        </div>
                    </div>

                    <!-- Baris 2: Nama Pemberi Informasi & Status Hubungan -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-semibold text-secondary mb-0">
                                    <i class="fa-solid fa-user-check text-success me-1"></i> NAMA PEMBERI INFORMASI <span class="text-danger">*</span>
                                </label>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.72rem;" onclick="copyHeadNameToInformant('create_resp_name', 'create_informant_name', 'create_status_select')">
                                    <i class="fa-solid fa-copy me-1"></i> Sama dg KRT
                                </button>
                            </div>
                            <input type="text" id="create_informant_name" name="informant_name" class="form-control" placeholder="Contoh: Siti Rogayah (atau nama KRT jika ditemui langsung)" required>
                            <div class="form-text small">Orang yang diwawancarai / memberikan data di lapangan.</div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="fa-solid fa-users text-primary me-1"></i> STATUS RESPONDEN / HUBUNGAN <span class="text-danger">*</span>
                            </label>
                            <select id="create_status_select" name="respondent_status" class="form-select" required>
                                <option value="Kepala Keluarga (KRT)">Kepala Keluarga (KRT)</option>
                                <option value="Istri / Suami (Pasangan)">Istri / Suami (Pasangan)</option>
                                <option value="Anak Kandung">Anak Kandung</option>
                                <option value="Famili Lain / ART">Famili Lain / ART</option>
                                <option value="Pengelola / Perwakilan Usaha">Pengelola / Perwakilan Usaha</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <!-- Baris 3: Instansi & Penugasan Terkait -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="fa-solid fa-building text-muted me-1"></i> INSTANSI / ASAL LEMBAGA <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="agency" class="form-control" placeholder="Contoh: Rumah Tangga / Usaha Tani" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="fa-solid fa-briefcase text-muted me-1"></i> KEGIATAN PENUGASAN TERKAIT
                            </label>
                            <select name="assignment_id" class="form-select">
                                <option value="">-- Pilih Kegiatan Anda --</option>
                                @foreach($myAssignments as $item)
                                    <option value="{{ $item->id }}">{{ $item->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Baris 4: Alamat Lengkap -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">
                            <i class="fa-solid fa-location-dot text-danger me-1"></i> ALAMAT LENGKAP <span class="text-danger">*</span>
                        </label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Nama jalan, RT/RW, Dusun/Desa, Kecamatan..." required></textarea>
                    </div>

                    <!-- Baris 5: Tujuan Kegiatan & Tanggal Input -->
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-secondary">TUJUAN KEGIATAN <span class="text-danger">*</span></label>
                            <input type="text" name="purpose" class="form-control" placeholder="Contoh: Pendataan Sarana Prasarana & Verifikasi Legalitas" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">TANGGAL INPUT <span class="text-danger">*</span></label>
                            <input type="date" name="input_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Data Responden
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL EDIT RESPONDEN (PETUGAS)
     ============================================================== -->
<div class="modal fade" id="editMyRespondentModal" tabindex="-1" aria-labelledby="editMyRespondentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="editMyRespondentModalLabel">
                    <i class="fa-regular fa-pen-to-square text-warning me-2"></i> Edit Data Responden
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editMyRespondentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <!-- Baris 1: Nama KRT & No HP -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="fa-solid fa-house-user text-primary me-1"></i> NAMA KEPALA KELUARGA (KRT) <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="my_edit_name" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="fa-solid fa-phone text-muted me-1"></i> NOMOR HP / WHATSAPP
                            </label>
                            <input type="text" id="my_edit_phone" name="phone" class="form-control">
                        </div>
                    </div>

                    <!-- Baris 2: Nama Pemberi Informasi & Status Hubungan -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-semibold text-secondary mb-0">
                                    <i class="fa-solid fa-user-check text-success me-1"></i> NAMA PEMBERI INFORMASI <span class="text-danger">*</span>
                                </label>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.72rem;" onclick="copyHeadNameToInformant('my_edit_name', 'my_edit_informant_name', 'my_edit_status')">
                                    <i class="fa-solid fa-copy me-1"></i> Sama dg KRT
                                </button>
                            </div>
                            <input type="text" id="my_edit_informant_name" name="informant_name" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="fa-solid fa-users text-primary me-1"></i> STATUS RESPONDEN / HUBUNGAN <span class="text-danger">*</span>
                            </label>
                            <select id="my_edit_status" name="respondent_status" class="form-select" required>
                                <option value="Kepala Keluarga (KRT)">Kepala Keluarga (KRT)</option>
                                <option value="Istri / Suami (Pasangan)">Istri / Suami (Pasangan)</option>
                                <option value="Anak Kandung">Anak Kandung</option>
                                <option value="Famili Lain / ART">Famili Lain / ART</option>
                                <option value="Pengelola / Perwakilan Usaha">Pengelola / Perwakilan Usaha</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <!-- Baris 3: Instansi & Penugasan -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="fa-solid fa-building text-muted me-1"></i> INSTANSI / ASAL LEMBAGA <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="my_edit_agency" name="agency" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="fa-solid fa-briefcase text-muted me-1"></i> KEGIATAN PENUGASAN
                            </label>
                            <select id="my_edit_assignment" name="assignment_id" class="form-select">
                                <option value="">-- Pilih Kegiatan --</option>
                                @foreach($myAssignments as $item)
                                    <option value="{{ $item->id }}">{{ $item->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Baris 4: Alamat Lengkap -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">
                            <i class="fa-solid fa-location-dot text-danger me-1"></i> ALAMAT LENGKAP <span class="text-danger">*</span>
                        </label>
                        <textarea id="my_edit_address" name="address" class="form-control" rows="2" required></textarea>
                    </div>

                    <!-- Baris 5: Tujuan Kegiatan & Tanggal Input -->
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-secondary">TUJUAN KEGIATAN <span class="text-danger">*</span></label>
                            <input type="text" id="my_edit_purpose" name="purpose" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">TANGGAL INPUT <span class="text-danger">*</span></label>
                            <input type="date" id="my_edit_date" name="input_date" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyHeadNameToInformant(headInputId, informantInputId, statusSelectId) {
        const headVal = document.getElementById(headInputId).value;
        if (headVal) {
            document.getElementById(informantInputId).value = headVal;
            const statusSelect = document.getElementById(statusSelectId);
            if (statusSelect) {
                statusSelect.value = 'Kepala Keluarga (KRT)';
            }
        }
    }

    function openEditMyRespondentModal(resp) {
        document.getElementById('editMyRespondentForm').action = '/petugas/respondents/' + resp.id;
        document.getElementById('my_edit_name').value = resp.family_head_name || resp.name || '';
        document.getElementById('my_edit_informant_name').value = resp.informant_name || resp.name || '';
        document.getElementById('my_edit_status').value = resp.respondent_status || 'Kepala Keluarga (KRT)';
        document.getElementById('my_edit_phone').value = resp.phone || '';
        document.getElementById('my_edit_agency').value = resp.agency || '';
        document.getElementById('my_edit_assignment').value = resp.assignment_id || '';
        document.getElementById('my_edit_address').value = resp.address || '';
        document.getElementById('my_edit_purpose').value = resp.purpose || '';
        document.getElementById('my_edit_date').value = resp.input_date ? resp.input_date.substring(0, 10) : '';

        const modal = new bootstrap.Modal(document.getElementById('editMyRespondentModal'));
        modal.show();
    }
</script>
@endpush


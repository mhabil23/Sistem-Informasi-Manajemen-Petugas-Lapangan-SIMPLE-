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
        @if(auth()->user()->isAdmin())
            <button type="button" class="btn btn-sp-primary" onclick="openCreateOfficerBastModal()">
                <i class="fa-solid fa-file-circle-plus me-1"></i> Terbitkan BAST Petugas
            </button>
        @endif
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <div class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Gagal menyimpan dokumen BAST:</div>
        <ul class="mb-0 small">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

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
                                @php
                                    $recCount = $item->recipients->count();
                                    $firstRec = $item->recipients->first();
                                @endphp
                                @if($recCount > 1)
                                    <div class="fw-bold text-dark">
                                        {{ $firstRec->recipient_name }}
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1">+{{ $recCount - 1 }}</span>
                                    </div>
                                    <div class="small text-muted">
                                        <i class="fa-solid fa-users me-1"></i> Kolektif ({{ $recCount }} penerima)
                                    </div>
                                @elseif($recCount === 1)
                                    <div class="fw-bold text-dark">{{ $firstRec->recipient_name }}</div>
                                    <div class="small text-muted">
                                        <i class="fa-solid fa-id-badge me-1"></i> {{ $firstRec->origin_role ?: ($firstRec->position_unit ?: '-') }}
                                    </div>
                                @else
                                    <div class="fw-bold text-dark">{{ $item->officer ? $item->officer->name : '-' }}</div>
                                    <div class="small text-muted"><i class="fa-solid fa-phone me-1"></i> {{ $item->officer ? ($item->officer->phone ?? $item->officer->username) : '-' }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="small fw-semibold text-dark text-truncate d-block" style="max-width: 240px;">
                                    {{ $item->activity_subtitle ?: ($item->assignment ? $item->assignment->title : '-') }}
                                </span>
                                <small class="text-muted"><i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $item->document_place ?: ($item->assignment ? $item->assignment->location : '-') }}</small>
                            </td>
                            <td>
                                @if($item->isKantorKePetugas() && $item->document_title)
                                    <div class="small fw-semibold text-dark text-truncate" style="max-width: 280px;">{{ $item->document_title }}</div>
                                @endif
                                <div class="small text-secondary text-truncate" style="max-width: 280px;" title="{{ $item->description }}">
                                    {{ Str::limit(str_replace("\n", ', ', $item->description), 55) }}
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

                                    @if(auth()->user()->isAdmin())
                                        @php
                                            $editPayload = [
                                                'id' => $item->id,
                                                'update_url' => route('admin.basts.officers.update', $item),
                                                'bast_type' => $item->bast_type ?: 'kantor_ke_petugas',
                                                'assignment_id' => $item->assignment_id,
                                                'officer_id' => $item->officer_id,
                                                'bast_number' => $item->bast_number,
                                                'bast_date' => $item->bast_date?->format('Y-m-d'),
                                                'description' => $item->description,
                                                'items' => $item->parsed_items,
                                                'document_title' => $item->document_title,
                                                'activity_subtitle' => $item->activity_subtitle,
                                                'institution_name' => $item->institution_name,
                                                'document_place' => $item->document_place,
                                                'maker_name' => $item->maker_name,
                                                'maker_nip' => $item->maker_nip,
                                                'notes' => $item->notes,
                                                'status' => $item->status,
                                                'recipients' => $item->resolvedRecipients()->map(fn ($r) => [
                                                    'user_id' => $r->user_id,
                                                    'recipient_name' => $r->recipient_name,
                                                    'position_unit' => $r->position_unit ?: 'Mitra',
                                                    'origin_role' => $r->origin_role ?: '',
                                                ])->values()->all(),
                                            ];
                                        @endphp
                                        <!-- Edit -->
                                        <button type="button"
                                                class="btn btn-light border sp-action-btn text-warning"
                                                title="Edit BAST"
                                                data-bs-toggle="tooltip"
                                                onclick='openEditOfficerBastModal(@json($editPayload))'>
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

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
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-regular fa-folder-open fa-3x mb-3 text-secondary opacity-50"></i>
                                <div class="fw-semibold">Belum ada dokumen BAST Petugas diterbitkan.</div>
                                <div class="small">Klik tombol "Terbitkan BAST Petugas" untuk membuat Berita Acara penyerahan berkas atau tanda terima perlengkapan.</div>
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
@php
    $assignmentPayload = $assignments->mapWithKeys(fn ($a) => [
        $a->id => [
            'title' => $a->title,
            'location' => $a->location,
            'officers' => $a->officers->map(fn ($o) => [
                'user_id' => $o->id,
                'recipient_name' => $o->name,
                'position_unit' => $o->position ?: ($o->isPegawai() ? 'Pegawai BPS' : 'Mitra'),
                'origin_role' => $a->location ?? '',
            ])->values()->all(),
        ],
    ])->all();

    $officersPayload = $officers->map(fn ($o) => [
        'user_id' => $o->id,
        'recipient_name' => $o->name,
        'position_unit' => $o->position ?: ($o->isPegawai() ? 'Pegawai BPS' : 'Mitra'),
        'phone' => $o->phone ?: $o->username,
        'role_label' => $o->roleLabel(),
    ])->values()->all();
@endphp

<!-- ==============================================================
     MODAL TERBITKAN / EDIT BAST PETUGAS
     ============================================================== -->
<div class="modal fade" id="officerBastFormModal" tabindex="-1" aria-labelledby="officerBastFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="officerBastFormModalLabel">
                    <i class="fa-solid fa-file-circle-plus text-primary me-2"></i> Terbitkan BAST Petugas Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="officerBastForm" method="POST" action="{{ route('admin.basts.officers.store') }}">
                @csrf
                <input type="hidden" name="_method" id="bastFormMethod" value="POST">

                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">JENIS ALUR SERAH TERIMA BAST <span class="text-danger">*</span></label>
                            <select name="bast_type" id="bastTypeInput" class="form-select fw-semibold" required onchange="toggleBastTypeSections()">
                                <option value="kantor_ke_petugas">1. BAST Blanko &amp; Perlengkapan (Format Tabel Kolektif)</option>
                                <option value="petugas_ke_kantor">3. BAST Dokumen Terisi &amp; Hasil Sensus (Petugas &rarr; Kantor)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">KEGIATAN PENUGASAN <span class="text-danger">*</span></label>
                            <select name="assignment_id" id="bastAssignmentInput" class="form-select" required onchange="onAssignmentSelected()">
                                <option value="">-- Pilih Kegiatan --</option>
                                @foreach($assignments as $assignment)
                                    <option value="{{ $assignment->id }}">{{ $assignment->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">TANGGAL BAST / TANDA TERIMA <span class="text-danger">*</span></label>
                            <input type="date" name="bast_date" id="bastDateInput" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">NOMOR BAST (OPSIONAL)</label>
                            <input type="text" name="bast_number" id="bastNumberInput" class="form-control font-monospace" placeholder="Kosongkan untuk penomoran otomatis">
                            <small class="text-muted" style="font-size: 0.72rem;">Otomatis: <code>BAST-BLANKO/{TAHUN}/{BULAN}/{URUT}</code></small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">CATATAN PENGAWAS (OPSIONAL)</label>
                            <input type="text" name="notes" id="bastNotesInput" class="form-control" placeholder="Catatan tambahan bila diperlukan">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">STATUS ARSIP <span class="text-danger">*</span></label>
                            <select name="status" id="bastStatusInput" class="form-select" required>
                                <option value="finalized" selected>Final / Sah</option>
                                <option value="draft">Draft</option>
                                <option value="archived">Diarsipkan</option>
                            </select>
                        </div>
                    </div>

                    <!-- =========================================================
                         BAGIAN 1: BAST BLANKO & PERLENGKAPAN (FORMAT TABEL KOLEKTIF)
                         ========================================================= -->
                    <div id="blankoCollectiveSection">
                        <div class="card border bg-light-subtle mb-3">
                            <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center">
                                <span class="fw-bold small text-primary">
                                    <i class="fa-solid fa-heading me-1"></i> HEADER CETAK TANDA TERIMA (4 BARIS ATAS)
                                </span>
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">Sesuai Format Resmi A4</span>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-secondary">BARIS 1: JUDUL DOKUMEN</label>
                                        <input type="text" name="document_title" id="bastDocTitleInput" class="form-control form-control-sm" placeholder="Contoh: Tanda Terima ATK Peserta / Blanko & Perlengkapan">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-secondary">BARIS 2: SUBJUDUL KEGIATAN / PELATIHAN</label>
                                        <input type="text" name="activity_subtitle" id="bastActivitySubtitleInput" class="form-control form-control-sm" placeholder="Contoh: Pelatihan Petugas Sensus Ekonomi 2026 Gelombang II Kelas C">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-secondary">BARIS 3: INSTANSI &amp; TAHUN</label>
                                        <input type="text" name="institution_name" id="bastInstitutionInput" class="form-control form-control-sm" placeholder="Contoh: BPS Kabupaten Kolaka Utara Tahun 2026">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-secondary">BARIS 4: TEMPAT PELAKSANAAN (DIGABUNG TANGGAL OTOMATIS)</label>
                                        <input type="text" name="document_place" id="bastDocPlaceInput" class="form-control form-control-sm" placeholder="Contoh: Hotel Berlian Lasusua">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DAFTAR PENERIMA -->
                        <div class="card border mb-3">
                            <div class="card-header bg-white py-2 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <span class="fw-bold small text-dark">
                                        <i class="fa-solid fa-users text-primary me-1"></i> DAFTAR PENERIMA BLANKO &amp; PERLENGKAPAN
                                    </span>
                                    <span class="badge bg-primary ms-2" id="recipientCountBadge">0 penerima</span>
                                </div>
                                <div class="d-flex flex-wrap gap-1.5 align-items-center">
                                    <select id="quickOfficerSelect" class="form-select form-select-sm" style="width: 210px;">
                                        <option value="">-- Pilih 1 Petugas --</option>
                                        @foreach($officers as $officer)
                                            <option value="{{ $officer->id }}">{{ $officer->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addSelectedSingleOfficer()">
                                        <i class="fa-solid fa-plus me-1"></i> Tambah
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info" onclick="toggleMultiPicker()">
                                        <i class="fa-solid fa-list-check me-1"></i> Pilih Banyak
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="loadAssignmentOfficers()" title="Masukkan semua petugas dari kegiatan yang dipilih">
                                        <i class="fa-solid fa-download me-1"></i> Dari Kegiatan
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addRecipientRow({})">
                                        <i class="fa-solid fa-user-plus me-1"></i> Manual
                                    </button>
                                </div>
                            </div>

                            <!-- Panel Pilih Banyak Petugas -->
                            <div id="multiOfficerPickerPanel" class="p-3 bg-light border-bottom d-none">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                    <input type="text" id="multiPickerSearch" class="form-control form-control-sm" style="max-width: 260px;" placeholder="Cari nama petugas..." oninput="filterMultiPicker()">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-xs btn-light border" onclick="selectAllVisibleMultiPicker(true)">Centang Semua</button>
                                        <button type="button" class="btn btn-xs btn-light border" onclick="selectAllVisibleMultiPicker(false)">Kosongkan</button>
                                        <button type="button" class="btn btn-xs btn-primary" onclick="applyMultiPickerSelection()">
                                            <i class="fa-solid fa-check me-1"></i> Masukkan ke Tabel
                                        </button>
                                    </div>
                                </div>
                                <div class="row g-2" style="max-height: 175px; overflow-y: auto;" id="multiPickerList">
                                    @foreach($officers as $officer)
                                        <div class="col-md-4 col-sm-6 multi-picker-item" data-name="{{ strtolower($officer->name) }}">
                                            <label class="d-flex align-items-center gap-2 p-2 bg-white border rounded small cursor-pointer mb-0">
                                                <input type="checkbox" class="form-check-input mt-0 multi-officer-cb" value="{{ $officer->id }}">
                                                <span class="text-truncate" title="{{ $officer->name }}">{{ $officer->name }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="table-responsive" style="max-height: 290px; overflow-y: auto;">
                                <table class="table table-sm table-bordered align-middle mb-0">
                                    <thead class="table-info sticky-top">
                                        <tr class="text-center small">
                                            <th style="width: 45px;">No.</th>
                                            <th style="min-width: 190px;">Nama</th>
                                            <th style="min-width: 150px;">Jabatan &amp; Unit Kerja</th>
                                            <th style="min-width: 170px;">Asal / Bertugas Sebagai</th>
                                            <th style="width: 90px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recipientTableBody"></tbody>
                                </table>
                            </div>
                            <div class="px-3 py-2 bg-light border-top small text-muted">
                                <i class="fa-solid fa-signature me-1 text-info"></i>
                                Kolom <strong>Tanda Tangan (5)</strong> pada hasil cetak PDF otomatis memakai format garis titik-titik selang-seling kiri/kanan (<code>1. .........</code> / <code>2. .........</code>).
                            </div>
                        </div>

                        <!-- BARANG & PEMBUAT DAFTAR -->
                        <div class="row g-3">
                            <div class="col-md-7">
                                <div class="card border h-100">
                                    <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center">
                                        <span class="fw-bold small text-dark">
                                            <i class="fa-solid fa-boxes-packing text-primary me-1"></i> DAFTAR PERLENGKAPAN / ATK (KIRI BAWAH) <span class="text-danger">*</span>
                                        </span>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addBastItemRow('')">
                                            <i class="fa-solid fa-plus me-1"></i> Tambah Item
                                        </button>
                                    </div>
                                    <div class="card-body p-3">
                                        <div id="bastItemsContainer" class="d-flex flex-column gap-2"></div>
                                        <small class="text-muted d-block mt-2" style="font-size: 0.72rem;">
                                            Ditampilkan berurutan otomatis di pojok kiri bawah (contoh: <code>1. Map Zipper</code>, <code>2. Blocknote</code>, <code>3. Bolpoin</code>).
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="card border h-100">
                                    <div class="card-header bg-white py-2 px-3">
                                        <span class="fw-bold small text-dark">
                                            <i class="fa-solid fa-user-pen text-primary me-1"></i> PEMBUAT DAFTAR (KANAN BAWAH)
                                        </span>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-secondary">NAMA PEMBUAT DAFTAR</label>
                                            <input type="text" name="maker_name" id="bastMakerNameInput" class="form-control form-control-sm" placeholder="Contoh: Muh. Robbith Dinak, S.Tr.Stat.">
                                        </div>
                                        <div>
                                            <label class="form-label small fw-semibold text-secondary">NIP PEMBUAT DAFTAR (OPSIONAL)</label>
                                            <input type="text" name="maker_nip" id="bastMakerNipInput" class="form-control form-control-sm" placeholder="Contoh: 199907202023021001">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- =========================================================
                         BAGIAN 2: BAST DOKUMEN TERISI (PETUGAS -> KANTOR)
                         ========================================================= -->
                    <div id="terisiSingleSection" class="d-none">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">PILIH PETUGAS PENANGGUNG JAWAB <span class="text-danger">*</span></label>
                            <select name="officer_id" id="bastSingleOfficerInput" class="form-select">
                                <option value="">-- Pilih Petugas --</option>
                                @foreach($officers as $officer)
                                    <option value="{{ $officer->id }}">{{ $officer->name }} ({{ $officer->phone ?? $officer->username }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-0">
                            <label class="form-label small fw-semibold text-secondary">KETERANGAN PENYERAHAN TUGAS &amp; BERKAS <span class="text-danger">*</span></label>
                            <textarea name="description" id="bastSingleDescriptionInput" class="form-control" rows="4" placeholder="Contoh: Telah menyelesaikan pendataan responden dan menyerahkan bundel kuesioner terisi lengkap."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary" id="bastFormSubmitBtn">
                        <i class="fa-solid fa-file-pdf me-1"></i> Simpan &amp; Buat Dokumen PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const BAST_ASSIGNMENTS_MAP = @json($assignmentPayload);
const BAST_OFFICERS_LIST = @json($officersPayload);
const BAST_DEFAULT_MAKER = {
    name: @json(auth()->user()->name ?? ''),
    nip: @json(auth()->user()->nik ?? ''),
};
let isEditModalMode = false;

function escapeHtmlAttr(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function getOfficerById(userId) {
    return BAST_OFFICERS_LIST.find(o => String(o.user_id) === String(userId)) || null;
}

function toggleBastTypeSections() {
    const type = document.getElementById('bastTypeInput').value;
    const isBlanko = (type === 'kantor_ke_petugas');

    document.getElementById('blankoCollectiveSection').classList.toggle('d-none', !isBlanko);
    document.getElementById('terisiSingleSection').classList.toggle('d-none', isBlanko);

    const singleOfficer = document.getElementById('bastSingleOfficerInput');
    const singleDesc = document.getElementById('bastSingleDescriptionInput');
    singleOfficer.disabled = isBlanko;
    singleDesc.disabled = isBlanko;
    singleOfficer.required = !isBlanko;
    singleDesc.required = !isBlanko;

    document.querySelectorAll('#blankoCollectiveSection input, #blankoCollectiveSection select').forEach(el => {
        if (!el.classList.contains('multi-officer-cb') && el.id !== 'quickOfficerSelect' && el.id !== 'multiPickerSearch') {
            el.disabled = !isBlanko;
        }
    });
}

function onAssignmentSelected() {
    const assignId = document.getElementById('bastAssignmentInput').value;
    const data = BAST_ASSIGNMENTS_MAP[assignId];
    if (!data) return;

    const subtitleEl = document.getElementById('bastActivitySubtitleInput');
    const placeEl = document.getElementById('bastDocPlaceInput');

    if (!isEditModalMode && subtitleEl.value.trim() === '') {
        subtitleEl.value = data.title || '';
    }
    if (!isEditModalMode && (placeEl.value.trim() === '' || placeEl.value.trim() === 'Lasusua')) {
        placeEl.value = data.location || 'Lasusua';
    }
}

function addRecipientRow(rowData) {
    const tbody = document.getElementById('recipientTableBody');
    const tr = document.createElement('tr');
    const nameVal = rowData.recipient_name || '';
    const posVal = rowData.position_unit || 'Mitra';
    const originVal = rowData.origin_role || '';
    const userIdVal = rowData.user_id || '';

    tr.innerHTML = `
        <td class="text-center fw-semibold small rec-no-cell"></td>
        <td>
            <input type="hidden" class="rec-userid-input" value="${escapeHtmlAttr(userIdVal)}">
            <input type="text" class="form-control form-control-sm rec-name-input" value="${escapeHtmlAttr(nameVal)}" placeholder="Nama penerima">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm rec-pos-input" value="${escapeHtmlAttr(posVal)}" placeholder="Mitra / Jabatan">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm rec-origin-input" value="${escapeHtmlAttr(originVal)}" placeholder="Contoh: Katoi / PML">
        </td>
        <td class="text-center text-nowrap">
            <button type="button" class="btn btn-xs btn-light border" title="Naik" onclick="moveRecipientRow(this, -1)">
                <i class="fa-solid fa-arrow-up"></i>
            </button>
            <button type="button" class="btn btn-xs btn-light border" title="Turun" onclick="moveRecipientRow(this, 1)">
                <i class="fa-solid fa-arrow-down"></i>
            </button>
            <button type="button" class="btn btn-xs btn-light border text-danger" title="Hapus" onclick="removeRecipientRow(this)">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    renumberRecipientRows();
}

function renumberRecipientRows() {
    const rows = document.querySelectorAll('#recipientTableBody tr');
    rows.forEach((tr, idx) => {
        tr.querySelector('.rec-no-cell').textContent = idx + 1;
        tr.querySelector('.rec-userid-input').name = `recipients[${idx}][user_id]`;
        tr.querySelector('.rec-name-input').name = `recipients[${idx}][recipient_name]`;
        tr.querySelector('.rec-pos-input').name = `recipients[${idx}][position_unit]`;
        tr.querySelector('.rec-origin-input').name = `recipients[${idx}][origin_role]`;
    });
    document.getElementById('recipientCountBadge').textContent = `${rows.length} penerima`;
}

function moveRecipientRow(btn, dir) {
    const tr = btn.closest('tr');
    const tbody = tr.parentNode;
    if (dir < 0 && tr.previousElementSibling) {
        tbody.insertBefore(tr, tr.previousElementSibling);
    } else if (dir > 0 && tr.nextElementSibling) {
        tbody.insertBefore(tr.nextElementSibling, tr);
    }
    renumberRecipientRows();
}

function removeRecipientRow(btn) {
    btn.closest('tr').remove();
    renumberRecipientRows();
}

function hasExistingUserId(userId) {
    if (!userId) return false;
    return Array.from(document.querySelectorAll('.rec-userid-input'))
        .some(input => String(input.value) === String(userId));
}

function addSelectedSingleOfficer() {
    const sel = document.getElementById('quickOfficerSelect');
    const officer = getOfficerById(sel.value);
    if (!officer) return;

    if (hasExistingUserId(officer.user_id)) {
        alert(`Petugas "${officer.recipient_name}" sudah ada di tabel penerima.`);
        return;
    }

    const assignId = document.getElementById('bastAssignmentInput').value;
    const assignLoc = BAST_ASSIGNMENTS_MAP[assignId]?.location || '';

    addRecipientRow({
        user_id: officer.user_id,
        recipient_name: officer.recipient_name,
        position_unit: officer.position_unit,
        origin_role: assignLoc,
    });
    sel.value = '';
}

function loadAssignmentOfficers() {
    const assignId = document.getElementById('bastAssignmentInput').value;
    if (!assignId || !BAST_ASSIGNMENTS_MAP[assignId]) {
        alert('Pilih Kegiatan Penugasan terlebih dahulu.');
        return;
    }

    const officers = BAST_ASSIGNMENTS_MAP[assignId].officers || [];
    if (officers.length === 0) {
        alert('Belum ada petugas yang terdaftar pada kegiatan ini.');
        return;
    }

    let added = 0;
    officers.forEach(item => {
        if (!hasExistingUserId(item.user_id)) {
            addRecipientRow(item);
            added++;
        }
    });

    if (added === 0) {
        alert('Semua petugas pada kegiatan ini sudah berada di tabel.');
    }
}

function toggleMultiPicker() {
    document.getElementById('multiOfficerPickerPanel').classList.toggle('d-none');
}

function filterMultiPicker() {
    const q = (document.getElementById('multiPickerSearch').value || '').toLowerCase().trim();
    document.querySelectorAll('.multi-picker-item').forEach(el => {
        const name = el.getAttribute('data-name') || '';
        el.classList.toggle('d-none', q !== '' && !name.includes(q));
    });
}

function selectAllVisibleMultiPicker(checked) {
    document.querySelectorAll('.multi-picker-item:not(.d-none) .multi-officer-cb').forEach(cb => {
        cb.checked = checked;
    });
}

function applyMultiPickerSelection() {
    const assignId = document.getElementById('bastAssignmentInput').value;
    const assignLoc = BAST_ASSIGNMENTS_MAP[assignId]?.location || '';

    document.querySelectorAll('.multi-officer-cb:checked').forEach(cb => {
        const officer = getOfficerById(cb.value);
        if (officer && !hasExistingUserId(officer.user_id)) {
            addRecipientRow({
                user_id: officer.user_id,
                recipient_name: officer.recipient_name,
                position_unit: officer.position_unit,
                origin_role: assignLoc,
            });
        }
        cb.checked = false;
    });
    document.getElementById('multiOfficerPickerPanel').classList.add('d-none');
}

function addBastItemRow(value) {
    const container = document.getElementById('bastItemsContainer');
    const row = document.createElement('div');
    row.className = 'input-group input-group-sm bast-item-row';
    const placeholders = ['Contoh: Map Zipper', 'Contoh: Blocknote', 'Contoh: Bolpoin'];
    const idx = container.children.length;
    const placeholder = placeholders[idx] || 'Nama barang / blanko / perlengkapan';

    row.innerHTML = `
        <span class="input-group-text fw-semibold item-no-label">${idx + 1}.</span>
        <input type="text" name="items[]" class="form-control bast-item-input" value="${escapeHtmlAttr(value)}" placeholder="${placeholder}">
        <button type="button" class="btn btn-outline-danger" title="Hapus item" onclick="removeBastItemRow(this)">
            <i class="fa-solid fa-xmark"></i>
        </button>
    `;
    container.appendChild(row);
    renumberBastItems();
}

function removeBastItemRow(btn) {
    const container = document.getElementById('bastItemsContainer');
    if (container.children.length <= 1) {
        btn.closest('.bast-item-row').querySelector('.bast-item-input').value = '';
        return;
    }
    btn.closest('.bast-item-row').remove();
    renumberBastItems();
}

function renumberBastItems() {
    document.querySelectorAll('#bastItemsContainer .bast-item-row').forEach((row, idx) => {
        row.querySelector('.item-no-label').textContent = `${idx + 1}.`;
    });
}

function resetBastModalCommon() {
    document.getElementById('recipientTableBody').innerHTML = '';
    document.getElementById('bastItemsContainer').innerHTML = '';
    document.getElementById('multiOfficerPickerPanel').classList.add('d-none');
}

function openCreateOfficerBastModal() {
    isEditModalMode = false;
    resetBastModalCommon();

    const form = document.getElementById('officerBastForm');
    form.action = @json(route('admin.basts.officers.store'));
    document.getElementById('bastFormMethod').value = 'POST';
    document.getElementById('officerBastFormModalLabel').innerHTML = '<i class="fa-solid fa-file-circle-plus text-primary me-2"></i> Terbitkan BAST Petugas Baru';

    document.getElementById('bastTypeInput').value = 'kantor_ke_petugas';
    document.getElementById('bastAssignmentInput').value = '';
    document.getElementById('bastDateInput').value = @json(now()->toDateString());
    document.getElementById('bastNumberInput').value = '';
    document.getElementById('bastNotesInput').value = '';
    document.getElementById('bastStatusInput').value = 'finalized';

    document.getElementById('bastDocTitleInput').value = 'Tanda Terima Blanko & Perlengkapan';
    document.getElementById('bastActivitySubtitleInput').value = '';
    document.getElementById('bastInstitutionInput').value = @json('BPS Kabupaten Kolaka Utara Tahun ' . now()->format('Y'));
    document.getElementById('bastDocPlaceInput').value = 'Lasusua';
    document.getElementById('bastMakerNameInput').value = BAST_DEFAULT_MAKER.name || '';
    document.getElementById('bastMakerNipInput').value = BAST_DEFAULT_MAKER.nip || '';

    document.getElementById('bastSingleOfficerInput').value = '';
    document.getElementById('bastSingleDescriptionInput').value = '';

    addBastItemRow('');
    renumberRecipientRows();
    toggleBastTypeSections();

    bootstrap.Modal.getOrCreateInstance(document.getElementById('officerBastFormModal')).show();
}

function openEditOfficerBastModal(data) {
    isEditModalMode = true;
    resetBastModalCommon();

    const form = document.getElementById('officerBastForm');
    form.action = data.update_url;
    document.getElementById('bastFormMethod').value = 'PUT';
    document.getElementById('officerBastFormModalLabel').innerHTML = `<i class="fa-solid fa-pen-to-square text-warning me-2"></i> Edit BAST — ${escapeHtmlAttr(data.bast_number)}`;

    document.getElementById('bastTypeInput').value = data.bast_type || 'kantor_ke_petugas';
    document.getElementById('bastAssignmentInput').value = data.assignment_id || '';
    document.getElementById('bastDateInput').value = data.bast_date || '';
    document.getElementById('bastNumberInput').value = data.bast_number || '';
    document.getElementById('bastNotesInput').value = data.notes || '';
    document.getElementById('bastStatusInput').value = data.status || 'finalized';

    document.getElementById('bastDocTitleInput').value = data.document_title || 'Tanda Terima Blanko & Perlengkapan';
    document.getElementById('bastActivitySubtitleInput').value = data.activity_subtitle || '';
    document.getElementById('bastInstitutionInput').value = data.institution_name || '';
    document.getElementById('bastDocPlaceInput').value = data.document_place || 'Lasusua';
    document.getElementById('bastMakerNameInput').value = data.maker_name || '';
    document.getElementById('bastMakerNipInput').value = data.maker_nip || '';

    document.getElementById('bastSingleOfficerInput').value = data.officer_id || '';
    document.getElementById('bastSingleDescriptionInput').value = data.description || '';

    (data.recipients || []).forEach(r => addRecipientRow(r));
    const items = (data.items && data.items.length > 0) ? data.items : [''];
    items.forEach(item => addBastItemRow(item));

    toggleBastTypeSections();
    bootstrap.Modal.getOrCreateInstance(document.getElementById('officerBastFormModal')).show();
}

document.getElementById('officerBastForm')?.addEventListener('submit', function (e) {
    const type = document.getElementById('bastTypeInput').value;
    if (type === 'kantor_ke_petugas') {
        const names = Array.from(document.querySelectorAll('.rec-name-input'))
            .map(i => i.value.trim())
            .filter(Boolean);
        if (names.length === 0) {
            e.preventDefault();
            alert('Tambahkan minimal 1 petugas / peserta penerima ke dalam daftar.');
            return;
        }

        const items = Array.from(document.querySelectorAll('.bast-item-input'))
            .map(i => i.value.trim())
            .filter(Boolean);
        if (items.length === 0) {
            e.preventDefault();
            alert('Isi minimal 1 item perlengkapan / blanko (misalnya: Map Zipper).');
            document.querySelector('.bast-item-input')?.focus();
            return;
        }
    }
});
</script>
@endif
@endsection


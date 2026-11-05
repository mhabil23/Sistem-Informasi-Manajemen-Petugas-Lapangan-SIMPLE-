@extends('layouts.app')

@section('title', 'Data Responden')
@section('page_title', 'Manajemen Data Responden')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Data Responden</li>
@endsection

@section('content')
<!-- Header & Quick Actions -->
<div class="row align-items-center mb-3">
    <div class="col-md-7">
        <h4 class="fw-bold text-dark mb-1">Data Responden Lapangan</h4>
        <p class="text-muted small mb-0">Kelola basis data responden, profil instansi/pelaku usaha, alamat wilayah, serta histori penerbitan Berita Acara Serah Terima (BAST).</p>
    </div>
    <div class="col-md-5 text-md-end mt-2 mt-md-0">
        <button type="button" class="btn btn-sm btn-sp-primary" data-bs-toggle="modal" data-bs-target="#createRespondentModal">
            <i class="fa-solid fa-user-plus me-1"></i> Tambah Responden Baru
        </button>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-2 mb-3">
    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Total Responden</div>
                <div class="sp-stat-value">{{ number_format($totalRespondents) }}</div>
                <div class="sp-stat-desc">Terdata di sistem</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0e7ff; color: #4338ca;">
                <i class="fa-regular fa-calendar-check"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Bulan Ini</div>
                <div class="sp-stat-value text-primary">{{ number_format($thisMonthCount) }}</div>
                <div class="sp-stat-desc">Input terbaru</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #fef3c7; color: #d97706;">
                <i class="fa-solid fa-building-flag"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Lembaga / Instansi</div>
                <div class="sp-stat-value text-warning">{{ number_format($uniqueAgencies) }}</div>
                <div class="sp-stat-desc">Kelompok & instansi</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                <i class="fa-solid fa-file-signature"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Telah Terbit BAST</div>
                <div class="sp-stat-value text-success">{{ number_format($withBastCount) }}</div>
                <div class="sp-stat-desc">Dokumen sah</div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="sp-card mb-3">
    <div class="sp-card-body p-2.5">
        <form method="GET" action="{{ route('admin.respondents.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="form-control form-control-sm border-start-0 ps-0" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama, instansi, alamat, nomor HP...">
                </div>
            </div>

            <div class="col-md-3">
                <select name="assignment_id" class="form-select form-select-sm">
                    <option value="">-- Semua Kegiatan --</option>
                    @foreach($assignments as $assignment)
                        <option value="{{ $assignment->id }}" {{ request('assignment_id') == $assignment->id ? 'selected' : '' }}>{{ $assignment->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <select name="officer_id" class="form-select form-select-sm">
                    <option value="">-- Semua Petugas --</option>
                    @foreach($officers as $officer)
                        <option value="{{ $officer->id }}" {{ request('officer_id') == $officer->id ? 'selected' : '' }}>{{ $officer->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-sp-primary flex-fill">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'assignment_id', 'officer_id']))
                    <a href="{{ route('admin.respondents.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="sp-card mb-3">
    <div class="sp-card-header">
        <h5 class="sp-card-title">
            <i class="fa-solid fa-id-card-clip text-primary"></i>
            Daftar Responden Lapangan ({{ $respondents->total() }})
        </h5>
    </div>
    <div class="sp-card-body p-0">
        <div class="table-responsive">
            <table class="table sp-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 22%;">Nama Responden</th>
                        <th style="width: 16%;">Instansi / Lembaga</th>
                        <th style="width: 20%;">Alamat &amp; Wilayah</th>
                        <th style="width: 16%;">Kegiatan Terkait</th>
                        <th style="width: 13%;">Petugas Penginput</th>
                        <th class="text-center" style="width: 7%;">Status BAST</th>
                        <th class="text-end" style="width: 6%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($respondents as $resp)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark text-truncate" style="max-width: 180px;" title="{{ $resp->name }}">{{ $resp->name }}</div>
                                <small class="text-muted"><i class="fa-solid fa-phone me-1"></i> {{ $resp->phone ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border text-truncate d-inline-block text-start" style="max-width: 140px; font-size: 0.72rem;" title="{{ $resp->agency }}">
                                    {{ $resp->agency }}
                                </span>
                            </td>
                            <td>
                                <div class="small text-secondary text-truncate" style="max-width: 175px;" title="{{ $resp->address }}">
                                    <i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $resp->address }}
                                </div>
                                @if($resp->district)
                                    <span class="badge bg-light text-secondary border mt-1" style="font-size: 0.65rem;">
                                        <i class="fa-solid fa-map-pin text-primary me-1"></i> {{ $resp->district }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="small fw-semibold text-dark text-truncate d-block" style="max-width: 140px;" title="{{ $resp->assignment ? $resp->assignment->title : '-' }}">
                                    {{ $resp->assignment ? $resp->assignment->title : '-' }}
                                </span>
                            </td>
                            <td>
                                <div class="small fw-bold text-dark text-truncate" style="max-width: 120px;" title="{{ $resp->officer ? $resp->officer->name : '-' }}">
                                    {{ $resp->officer ? $resp->officer->name : '-' }}
                                </div>
                                <div class="text-muted" style="font-size: 0.7rem;">{{ $resp->input_date->format('d/m/Y') }}</div>
                            </td>
                            <td class="text-center">
                                @if($resp->basts->isNotEmpty())
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5" style="font-size: 0.7rem;" title="Sudah terbit BAST">
                                        <i class="fa-solid fa-check me-0.5"></i> {{ $resp->basts->count() }} BAST
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-1.5 py-0.5" style="font-size: 0.7rem;">
                                        Belum Ada
                                    </span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="sp-action-btn-group">
                                    <!-- Detail Modal Trigger -->
                                    <button type="button" 
                                            class="btn btn-light border sp-action-btn text-info" 
                                            title="Lihat Detail Responden" 
                                            data-bs-toggle="tooltip"
                                            onclick="openDetailRespondentModal({{ json_encode($resp) }})">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    <!-- Edit -->
                                    <button type="button" 
                                            class="btn btn-light border sp-action-btn text-warning" 
                                            title="Edit Data" 
                                            data-bs-toggle="tooltip"
                                            onclick="openEditRespondentModal({{ json_encode($resp) }})">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>

                                    <!-- Hapus -->
                                    <form method="POST" action="{{ route('admin.respondents.destroy', $resp) }}" class="d-inline" onsubmit="return confirm('Hapus data responden {{ addslashes($resp->name) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-light border sp-action-btn text-danger" 
                                                title="Hapus Responden" 
                                                data-bs-toggle="tooltip">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-users-slash fa-3x mb-3 text-secondary opacity-50"></i>
                                <div class="fw-semibold">Belum ada data responden ditemukan.</div>
                                <div class="small">Klik tombol "Tambah Responden Baru" untuk menginput data responden.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
     MODAL TAMBAH RESPONDEN
     ============================================================== -->
<div class="modal fade" id="createRespondentModal" tabindex="-1" aria-labelledby="createRespondentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="createRespondentModalLabel">
                    <i class="fa-solid fa-user-plus text-primary me-2"></i> Tambah Data Responden Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.respondents.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary">NAMA LENGKAP RESPONDEN <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Kelompok Tani Sejahtera (Bpk. Mulyadi)" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">NOMOR HP / WHATSAPP</label>
                            <input type="text" name="phone" class="form-control" placeholder="Contoh: 081234567890">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">INSTANSI / ASAL LEMBAGA <span class="text-danger">*</span></label>
                            <input type="text" name="agency" class="form-control" placeholder="Contoh: Pelaku Usaha Agribisnis / Desa Sukamaju" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">KEGIATAN TERKAIT</label>
                            <select name="assignment_id" class="form-select">
                                <option value="">-- Pilih Kegiatan --</option>
                                @foreach($assignments as $assignment)
                                    <option value="{{ $assignment->id }}">{{ $assignment->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary">ALAMAT LENGKAP <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Nama jalan, RT/RW, Dusun/Desa..." required></textarea>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">KECAMATAN / WILAYAH</label>
                            <input type="text" name="district" class="form-control" placeholder="Contoh: Kecamatan Sukamaju">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">TUJUAN KEGIATAN <span class="text-danger">*</span></label>
                        <input type="text" name="purpose" class="form-control" placeholder="Contoh: Pendataan Sarana Prasarana Pertanian Terpadu 2026" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">PETUGAS PENGINPUT <span class="text-danger">*</span></label>
                            <select name="officer_id" class="form-select" required>
                                <option value="">-- Pilih Petugas --</option>
                                @foreach($officers as $officer)
                                    <option value="{{ $officer->id }}" {{ $officer->id === auth()->id() ? 'selected' : '' }}>{{ $officer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">TANGGAL INPUT <span class="text-danger">*</span></label>
                            <input type="date" name="input_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Responden
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL EDIT RESPONDEN
     ============================================================== -->
<div class="modal fade" id="editRespondentModal" tabindex="-1" aria-labelledby="editRespondentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="editRespondentModalLabel">
                    <i class="fa-regular fa-pen-to-square text-warning me-2"></i> Edit Data Responden
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRespondentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary">NAMA LENGKAP RESPONDEN <span class="text-danger">*</span></label>
                            <input type="text" id="edit_resp_name" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">NOMOR HP / WHATSAPP</label>
                            <input type="text" id="edit_resp_phone" name="phone" class="form-control">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">INSTANSI / ASAL LEMBAGA <span class="text-danger">*</span></label>
                            <input type="text" id="edit_resp_agency" name="agency" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">KEGIATAN TERKAIT</label>
                            <select id="edit_resp_assignment" name="assignment_id" class="form-select">
                                <option value="">-- Tidak Terikat Kegiatan Tertentu --</option>
                                @foreach($assignments as $assignment)
                                    <option value="{{ $assignment->id }}">{{ $assignment->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary">ALAMAT LENGKAP <span class="text-danger">*</span></label>
                            <textarea id="edit_resp_address" name="address" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">KECAMATAN / WILAYAH</label>
                            <input type="text" id="edit_resp_district" name="district" class="form-control" placeholder="Contoh: Kecamatan Sukamaju">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">TUJUAN KEGIATAN <span class="text-danger">*</span></label>
                        <input type="text" id="edit_resp_purpose" name="purpose" class="form-control" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">PETUGAS PENGINPUT <span class="text-danger">*</span></label>
                            <select id="edit_resp_officer" name="officer_id" class="form-select" required>
                                @foreach($officers as $officer)
                                    <option value="{{ $officer->id }}">{{ $officer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">TANGGAL INPUT <span class="text-danger">*</span></label>
                            <input type="date" id="edit_resp_date" name="input_date" class="form-control" required>
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

<!-- ==============================================================
     MODAL DETAIL RESPONDEN
     ============================================================== -->
<div class="modal fade" id="detailRespondentModal" tabindex="-1" aria-labelledby="detailRespondentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="detailRespondentModalLabel">
                    <i class="fa-solid fa-address-card text-info me-2"></i> Rincian Profil Responden
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="text-muted small">Nama Responden:</div>
                        <h5 class="fw-bold text-dark mb-0" id="det_name"></h5>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Instansi / Lembaga:</div>
                        <div class="fw-bold text-primary" id="det_agency"></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Nomor HP / WhatsApp:</div>
                        <div class="fw-semibold text-dark" id="det_phone"></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Tanggal Input:</div>
                        <div class="fw-semibold text-dark" id="det_date"></div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Alamat Lengkap:</div>
                        <div class="text-dark bg-light p-2 rounded border small" id="det_address"></div>
                        <div class="mt-1" id="det_district_badge"></div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Tujuan Pendataan / Kegiatan:</div>
                        <div class="text-secondary small fw-medium" id="det_purpose"></div>
                    </div>
                </div>

                <div class="border-top pt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold small text-dark"><i class="fa-solid fa-file-contract text-primary me-1"></i> Histori Dokumen BAST:</span>
                        <a href="{{ route('admin.basts.respondents.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-plus me-1"></i> Terbitkan BAST Baru
                        </a>
                    </div>
                    <div id="det_basts_container" class="small text-muted">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditRespondentModal(resp) {
        document.getElementById('editRespondentForm').action = '/admin/respondents/' + resp.id;
        document.getElementById('edit_resp_name').value = resp.name;
        document.getElementById('edit_resp_phone').value = resp.phone || '';
        document.getElementById('edit_resp_agency').value = resp.agency;
        document.getElementById('edit_resp_assignment').value = resp.assignment_id || '';
        document.getElementById('edit_resp_address').value = resp.address;
        document.getElementById('edit_resp_district').value = resp.district || '';
        document.getElementById('edit_resp_purpose').value = resp.purpose;
        document.getElementById('edit_resp_officer').value = resp.officer_id;
        document.getElementById('edit_resp_date').value = resp.input_date ? resp.input_date.substring(0, 10) : '';

        const modal = new bootstrap.Modal(document.getElementById('editRespondentModal'));
        modal.show();
    }

    function openDetailRespondentModal(resp) {
        document.getElementById('det_name').innerText = resp.name;
        document.getElementById('det_agency').innerText = resp.agency;
        document.getElementById('det_phone').innerText = resp.phone || '-';
        document.getElementById('det_date').innerText = resp.input_date ? resp.input_date.substring(0, 10) : '-';
        document.getElementById('det_address').innerText = resp.address;
        const distBadge = document.getElementById('det_district_badge');
        if (resp.district) {
            distBadge.innerHTML = `<span class="badge bg-light text-secondary border"><i class="fa-solid fa-map-pin text-primary me-1"></i> ${resp.district}</span>`;
        } else {
            distBadge.innerHTML = '';
        }
        document.getElementById('det_purpose').innerText = resp.purpose;

        const bastsContainer = document.getElementById('det_basts_container');
        if (resp.basts && resp.basts.length > 0) {
            let html = '<ul class="list-group list-group-flush">';
            resp.basts.forEach(b => {
                html += `<li class="list-group-item d-flex justify-content-between align-items-center py-2 px-0">
                            <div>
                                <strong>${b.bast_number}</strong>
                                <span class="text-muted ms-2">(${b.bast_date.substring(0, 10)})</span>
                            </div>
                            <a href="/admin/basts/respondents/${b.id}/download" class="btn btn-sm btn-outline-success">
                                <i class="fa-solid fa-download me-1"></i> Unduh PDF
                            </a>
                         </li>`;
            });
            html += '</ul>';
            bastsContainer.innerHTML = html;
        } else {
            bastsContainer.innerHTML = '<em>Belum ada Berita Acara (BAST) yang diterbitkan untuk responden ini.</em>';
        }

        const modal = new bootstrap.Modal(document.getElementById('detailRespondentModal'));
        modal.show();
    }
</script>
@endpush

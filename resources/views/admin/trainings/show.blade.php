@extends('layouts.app')

@section('title', 'Daftar Hadir - ' . ($training->document_number ?: $training->title))
@section('page_title', 'Rincian Daftar Hadir')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.trainings.index') }}">Daftar Hadir</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ \Illuminate\Support\Str::limit($training->title, 25) }}</li>
@endsection

@push('styles')
<style>
    .bento-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    .bento-table-container-fixed {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .bento-table {
        table-layout: fixed;
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.8rem;
    }
    .bento-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.75rem 0.55rem;
        border-bottom: 1px solid #e2e8f0;
        border-top: none;
        white-space: nowrap;
        vertical-align: middle;
    }
    .bento-table tbody td {
        padding: 0.75rem 0.55rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.8rem;
        color: #334155;
        background: #ffffff;
        transition: background 0.2s ease;
    }
    .bento-table tbody tr:hover td {
        background-color: #f8fafc;
    }
    .bento-table tbody tr:last-child td {
        border-bottom: none;
    }
    .btn-bps-orange {
        background-color: #F37021;
        border-color: #F37021;
        color: #ffffff;
        font-weight: 600;
    }
    .btn-bps-orange:hover, .btn-bps-orange:focus {
        background-color: #d95d13;
        border-color: #d95d13;
        color: #ffffff;
    }
    /* Compact Action Buttons */
    .bento-action-btn {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #E2E8F0;
        background: #FFFFFF;
        transition: all 0.15s ease;
        font-size: 0.8rem;
        padding: 0;
        flex-shrink: 0;
    }
    .bento-action-btn.btn-edit { color: #D97706; border-color: #FDE68A; background: #FFFBEB; }
    .bento-action-btn.btn-edit:hover { background: #D97706; color: #FFFFFF; }
    .bento-action-btn.btn-delete { color: #DC2626; border-color: #FECACA; background: #FEF2F2; }
    .bento-action-btn.btn-delete:hover { background: #DC2626; color: #FFFFFF; }
    @media (min-width: 768px) and (max-width: 1279.98px) {
        .bento-table {
            min-width: 820px;
        }
    }
    @media (max-width: 767.98px) {
        .bento-table {
            table-layout: auto !important;
        }
        .bento-table thead {
            display: none;
        }
        .bento-table, .bento-table tbody, .bento-table tr, .bento-table td {
            display: block;
            width: 100% !important;
        }
        .bento-table tbody tr {
            margin: 0.65rem auto;
            width: calc(100% - 1.3rem) !important;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 0;
            background: #fff;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
            overflow: hidden;
            box-sizing: border-box;
        }
        .bento-table tbody td {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            text-align: right !important;
            padding: 0.6rem 0.85rem !important;
            border-bottom: 1px solid #f1f5f9;
            white-space: normal !important;
        }
        .bento-table tbody td > div,
        .bento-table tbody td > span {
            max-width: 66%;
            min-width: 0;
        }
        .bento-table tbody td::before {
            content: attr(data-label);
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            font-size: 0.68rem;
            letter-spacing: 0.03em;
            text-align: left;
            flex-shrink: 0;
        }
        .bento-table tbody td:last-child {
            border-bottom: none;
            background: #f8fafc;
            justify-content: flex-end;
        }
        .bento-table tbody td[colspan] {
            display: block;
            text-align: center !important;
            background: transparent !important;
        }
        .bento-table tbody td[colspan]::before {
            display: none;
        }
    }
</style>
@endpush

@section('content')
<!-- Header Card Detail Pelatihan -->
<div class="bento-card mb-4">
    <div class="p-4">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <a href="{{ route('admin.trainings.index') }}" class="btn btn-sm btn-outline-secondary mb-2 rounded-pill px-3 py-1" style="font-size: 0.8rem;">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Agenda
                </a>
                <h4 class="fw-bold text-dark mb-1">{{ $training->title }}</h4>
                <div class="d-flex flex-wrap gap-3 text-secondary small mt-2">
                    <div><i class="bi bi-calendar-check text-primary me-1"></i> <strong>{{ $training->start_date?->format('d M Y') ?? '-' }} - {{ $training->end_date?->format('d M Y') ?? '-' }}</strong></div>
                    <div><i class="bi bi-geo-alt text-danger me-1"></i> {{ $training->location }}</div>
                    <div><i class="bi bi-people text-info me-1"></i> Kuota: <strong>{{ $training->quota ?: 'Tak Terbatas' }}</strong></div>
                    <div>
                        @php
                            $statusBadges = [
                                'upcoming' => 'bg-secondary',
                                'ongoing' => 'bg-warning text-dark',
                                'completed' => 'bg-success',
                                'cancelled' => 'bg-danger',
                            ];
                        @endphp
                        <span class="badge {{ $statusBadges[$training->status] ?? 'bg-secondary' }}">
                            {{ ucfirst($training->status) }}
                        </span>
                    </div>
                </div>
                @if($training->description)
                    <p class="text-muted small mt-2 mb-0">{{ $training->description }}</p>
                @endif
            </div>
            <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
                <div class="d-flex flex-wrap justify-content-lg-end gap-2">
                    <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#shareLinkModal" style="font-size: 0.85rem;">
                        <i class="bi bi-qr-code-scan"></i> Link &amp; QR Presensi
                    </button>
                    <button type="button" class="btn btn-bps-orange rounded-pill px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#createParticipantModal" style="font-size: 0.85rem;">
                        <i class="bi bi-person-plus me-1"></i> Tambah Peserta
                    </button>
                    <a href="{{ route('admin.trainings.attendance.pdf', $training) }}" class="btn btn-outline-danger rounded-pill px-3 py-1.5" style="font-size: 0.85rem;">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Cetak PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Banner Link Presensi Online Peserta (WhatsApp & Proyektor) -->
<div class="bento-card mb-4 border shadow-sm p-3.5" style="border-radius: 20px; background: linear-gradient(135deg, #FFFFFF 0%, #F0FDF4 100%); border-color: #BBF7D0 !important;">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3" style="min-width: 0; flex: 1 1 260px;">
            <div class="rounded-4 d-flex align-items-center justify-content-center text-white flex-shrink-0 shadow-sm" style="width: 46px; height: 46px; background: #16A34A;">
                <i class="bi bi-link-45deg fs-4"></i>
            </div>
            <div style="min-width: 0; flex: 1 1 auto;">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-0.5">
                    <span class="fw-bold text-dark" style="font-size: 0.94rem;">Link Presensi &amp; Tanda Tangan Digital Peserta</span>
                    <span class="badge bg-success text-white rounded-pill px-2.5 py-0.5" style="font-size: 0.68rem;">Siap Dibagikan</span>
                </div>
                <div class="text-secondary small font-monospace text-truncate" style="font-size: 0.78rem; max-width: min(480px, 100%);">
                    {{ $training->public_attendance_url }}
                </div>
            </div>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" onclick="copyAttendanceLink('{{ $training->public_attendance_url }}')">
                <i class="bi bi-clipboard2-check"></i> <span id="copyBtnText">Salin Link</span>
            </button>
            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#shareLinkModal">
                <i class="bi bi-qr-code"></i> Tampilkan QR Code
            </button>
            <a href="{{ $training->public_attendance_url }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5" title="Buka Halaman Presensi di Tab Baru">
                <i class="bi bi-box-arrow-up-right me-1"></i> Buka Link
            </a>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="bento-card mb-4">
    <div class="p-3">
        <form method="GET" action="{{ route('admin.trainings.show', $training) }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="form-control border-start-0 ps-0" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama peserta, NIP/ID, instansi, sertifikat...">
                </div>
            </div>

            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="all">-- Pilih Kategori Peserta --</option>
                    @foreach($availableCategories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="attendance" class="form-select">
                    <option value="all">-- Pilih Status Hadir --</option>
                    <option value="present" {{ request('attendance') == 'present' ? 'selected' : '' }}>Hadir</option>
                    <option value="excused" {{ request('attendance') == 'excused' ? 'selected' : '' }}>Izin</option>
                    <option value="absent" {{ request('attendance') == 'absent' ? 'selected' : '' }}>Tidak Hadir</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-bps-orange flex-fill">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'category', 'attendance']))
                    <a href="{{ route('admin.trainings.show', $training) }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Participants Table Card -->
<div class="bento-card mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center p-3 border-bottom gap-2">
        <div>
            <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">Daftar Peserta & Presensi ({{ $participants->total() }})</h5>
            <span class="badge bg-light text-secondary border mt-1">Halaman {{ $participants->currentPage() }} dari {{ $participants->lastPage() ?: 1 }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                Hadir: {{ $presentCount }}
            </span>
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                Izin: {{ $excusedCount }}
            </span>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                Alpa: {{ $absentCount }}
            </span>
        </div>
    </div>

    <div class="bento-table-container-fixed">
        <table class="bento-table align-middle">
            <thead>
                <tr>
                    <th style="width: 4%;" class="text-center">NO</th>
                    <th style="width: 24%;">NAMA PESERTA</th>
                    <th style="width: 13%;">KATEGORI</th>
                    <th style="width: 19%;">INSTANSI / ASAL</th>
                    <th style="width: 14%;">KONTAK</th>
                    <th style="width: 10%;" class="text-center">PRESENSI</th>
                    <th style="width: 7%;">NO. SERTIFIKAT</th>
                    <th class="text-end pe-3" style="width: 9%;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participants as $idx => $p)
                    <tr>
                        <td class="text-muted small text-center" data-label="No">{{ $participants->firstItem() + $idx }}</td>
                        <td data-label="Nama Peserta">
                            <div class="fw-bold text-dark text-truncate">{{ $p->name }}</div>
                            @if($p->nip_or_id)
                                <small class="text-muted font-monospace">NIP/ID: {{ $p->nip_or_id }}</small>
                            @endif
                        </td>
                        <td data-label="Kategori">
                            @php
                                $catBadges = [
                                    'DPW' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                    'Magang Mitra' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
                                    'Pegawai' => 'bg-success-subtle text-success border border-success-subtle',
                                ];
                            @endphp
                            <span class="badge {{ $catBadges[$p->category] ?? 'bg-secondary-subtle text-dark border' }} px-2 py-1">
                                {{ $p->category }}
                            </span>
                        </td>
                        <td data-label="Instansi / Asal">
                            <div class="small text-secondary text-truncate">{{ $p->agency ?: '-' }}</div>
                        </td>
                        <td data-label="Kontak">
                            <div class="small text-dark">{{ $p->phone ?: '-' }}</div>
                            @if($p->email)
                                <div class="text-muted text-truncate" style="font-size: 0.72rem;">{{ $p->email }}</div>
                            @endif
                        </td>
                        <td class="text-center" data-label="Presensi">
                            @if($p->attendance_status === 'present')
                                <div class="d-flex flex-column align-items-center gap-1">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size: 0.7rem;">
                                        <i class="bi bi-check2-circle me-1"></i> Hadir
                                    </span>
                                    @if($p->signature_path && file_exists(storage_path('app/public/' . $p->signature_path)))
                                        <div class="border rounded bg-white px-1 py-0.5 shadow-xs" style="max-width: 70px;" title="Tanda tangan digital peserta">
                                            <img src="{{ asset('storage/' . $p->signature_path) }}" alt="TTD" style="max-height: 20px; max-width: 60px;">
                                        </div>
                                    @endif
                                    @if($p->signed_at)
                                        <span class="text-muted" style="font-size: 0.65rem;">{{ $p->signed_at->format('H:i') }} WITA</span>
                                    @endif
                                </div>
                            @elseif($p->attendance_status === 'excused')
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                    <i class="bi bi-clock me-1"></i> Izin
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-0.5" style="font-size: 0.7rem;">
                                    Belum Hadir
                                </span>
                            @endif
                        </td>
                        <td data-label="No. Sertifikat">
                            <span class="small font-monospace text-secondary text-truncate d-block">{{ $p->certificate_number ?: '-' }}</span>
                        </td>
                        <td class="text-end text-nowrap pe-3" data-label="Aksi">
                            <div class="d-inline-flex align-items-center justify-content-end gap-1">
                                <!-- Edit -->
                                <button type="button" 
                                        class="bento-action-btn btn-edit" 
                                        title="Edit Data Peserta" 
                                        data-bs-toggle="tooltip"
                                        onclick="openEditParticipantModal({{ json_encode($p) }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- Hapus -->
                                <form method="POST" action="{{ route('admin.trainings.participants.destroy', [$training, $p]) }}" class="d-inline" onsubmit="return confirm('Hapus peserta {{ addslashes($p->name) }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bento-action-btn btn-delete" 
                                            title="Hapus Peserta" 
                                            data-bs-toggle="tooltip">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-person-x fs-1 mb-3 text-secondary opacity-50 d-block"></i>
                            <div class="fw-semibold">Belum ada peserta yang didaftarkan.</div>
                            <div class="small">Klik tombol "Tambah Peserta" untuk menambahkan data peserta pelatihan.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($participants->hasPages())
        <div class="p-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="small text-muted">
                Menampilkan {{ $participants->firstItem() }} sampai {{ $participants->lastItem() }} dari {{ $participants->total() }} peserta
            </div>
            <div>
                {{ $participants->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

<!-- ==============================================================
     MODAL TAMBAH PESERTA
     ============================================================== -->
<div class="modal fade" id="createParticipantModal" tabindex="-1" aria-labelledby="createParticipantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="createParticipantModalLabel">
                    <i class="bi bi-person-plus text-primary me-2"></i> Tambah Peserta Pelatihan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.trainings.participants.store', $training) }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary">NAMA LENGKAP PESERTA <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama lengkap dan gelar (jika ada)" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">NIP / NOMOR IDENTITAS</label>
                            <input type="text" name="nip_or_id" class="form-control" placeholder="Nomor Induk Pegawai / KTP / ID">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">KATEGORI PESERTA <span class="text-danger">*</span></label>
                            <input type="text" name="category" class="form-control" list="categoryOptions" placeholder="Pilih atau ketik kategori..." required>
                            <datalist id="categoryOptions">
                                <option value="DPW">
                                <option value="Magang Mitra">
                                <option value="Pegawai">
                                <option value="Fasilitator Lapangan">
                                <option value="Pelaku Usaha">
                            </datalist>
                            <div class="form-text small">Kategori dapat disesuaikan (misal: DPW, Magang Mitra, Pegawai).</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">INSTANSI / ASAL LEMBAGA</label>
                            <input type="text" name="agency" class="form-control" placeholder="Contoh: Dinas Pertanian / Universitas Brawijaya">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">NOMOR HP / WHATSAPP</label>
                            <input type="text" name="phone" class="form-control" placeholder="08123456789">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">ALAMAT EMAIL</label>
                            <input type="email" name="email" class="form-control" placeholder="peserta@domain.com">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">STATUS PRESENSI KEHADIRAN <span class="text-danger">*</span></label>
                            <select name="attendance_status" class="form-select" required>
                                <option value="present">Hadir</option>
                                <option value="excused">Izin / Sakit</option>
                                <option value="absent">Tidak Hadir (Alpa)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">NOMOR SERTIFIKAT (OPSIONAL)</label>
                            <input type="text" name="certificate_number" class="form-control" placeholder="Contoh: SERT/2026/XI/001">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-bps-orange">
                        <i class="bi bi-floppy me-1"></i> Simpan Peserta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL EDIT PESERTA
     ============================================================== -->
<div class="modal fade" id="editParticipantModal" tabindex="-1" aria-labelledby="editParticipantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="editParticipantModalLabel">
                    <i class="bi bi-pencil-square text-warning me-2"></i> Edit Data Peserta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editParticipantForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary">NAMA LENGKAP PESERTA <span class="text-danger">*</span></label>
                            <input type="text" id="edit_part_name" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">NIP / NOMOR IDENTITAS</label>
                            <input type="text" id="edit_part_nip" name="nip_or_id" class="form-control">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">KATEGORI PESERTA <span class="text-danger">*</span></label>
                            <input type="text" id="edit_part_cat" name="category" class="form-control" list="categoryOptions" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">INSTANSI / ASAL LEMBAGA</label>
                            <input type="text" id="edit_part_agency" name="agency" class="form-control">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">NOMOR HP / WHATSAPP</label>
                            <input type="text" id="edit_part_phone" name="phone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">ALAMAT EMAIL</label>
                            <input type="email" id="edit_part_email" name="email" class="form-control">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">STATUS PRESENSI KEHADIRAN <span class="text-danger">*</span></label>
                            <select id="edit_part_att" name="attendance_status" class="form-select" required>
                                <option value="present">Hadir</option>
                                <option value="excused">Izin / Sakit</option>
                                <option value="absent">Tidak Hadir (Alpa)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">NOMOR SERTIFIKAT</label>
                            <input type="text" id="edit_part_cert" name="certificate_number" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-bps-orange">
                        <i class="bi bi-floppy me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL SHARE LINK & QR CODE PRESENSI ONLINE
     ============================================================== -->
<div class="modal fade" id="shareLinkModal" tabindex="-1" aria-labelledby="shareLinkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-qr-code text-white"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0" id="shareLinkModalLabel">QR Code &amp; Link Presensi</h6>
                        <small class="text-white-50">Tanda tangan digital langsung dari HP peserta</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill small fw-bold">
                        {{ Str::limit($training->title, 40) }}
                    </span>
                </div>

                <!-- Mode Selector: Akses HP (Wi-Fi) vs Laptop -->
                <div class="d-inline-flex p-1 bg-light rounded-pill border mb-3">
                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-bold btn-primary" id="modeHpBtn" onclick="switchShareMode('hp')">
                        <i class="bi bi-phone me-1"></i> Akses HP (Wi-Fi)
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-bold btn-outline-secondary" id="modeLocalBtn" onclick="switchShareMode('local')">
                        <i class="bi bi-laptop me-1"></i> Laptop Sendiri
                    </button>
                </div>

                <!-- Pengaturan IP Wi-Fi Laptop -->
                <div id="lanIpSettingsBox" class="p-2.5 rounded-3 bg-light border text-start mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <label class="form-label small fw-bold text-dark mb-0" style="font-size: 0.74rem;">
                            <i class="bi bi-wifi me-1 text-primary"></i> IP Wi-Fi Laptop untuk HP Peserta:
                        </label>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.65rem;">Wi-Fi Aktif</span>
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white text-muted" style="font-size: 0.75rem;">http://</span>
                        <input type="text" id="customLanIpInput" class="form-control font-monospace" value="192.168.1.81" placeholder="192.168.1.81" oninput="updateShareUrl()">
                        <span class="input-group-text bg-white text-muted" style="font-size: 0.75rem;">:</span>
                        <input type="text" id="customLanPortInput" class="form-control font-monospace" style="max-width: 70px;" value="8000" placeholder="8000" oninput="updateShareUrl()">
                    </div>
                    <div class="text-muted mt-1" style="font-size: 0.68rem;">
                        HP peserta tidak bisa mengakses alamat <code class="text-dark">127.0.0.1</code>, sehingga alamat otomatis diganti menggunakan IP Wi-Fi laptop.
                    </div>
                </div>

                <!-- QR Code Box -->
                <div class="p-3 bg-light rounded-4 d-inline-block border shadow-sm mb-3">
                    <img id="shareModalQrImage" src="https://api.qrserver.com/v1/create-qr-code/?size=210x210&margin=10&data={{ urlencode(str_replace(['127.0.0.1', 'localhost'], '192.168.1.81', $training->public_attendance_url)) }}" 
                         alt="QR Code Presensi {{ $training->title }}" 
                         class="img-fluid rounded-3" 
                         style="width: 210px; height: 210px;"
                         loading="lazy">
                    <div class="small text-muted mt-2 fw-semibold">
                        <i class="bi bi-camera me-1"></i> Pindai dengan kamera HP peserta
                    </div>
                </div>

                <!-- Link Input Copy -->
                <div class="text-start mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1">
                        <i class="bi bi-link-45deg me-1"></i> Tautan / Link Presensi
                    </label>
                    <div class="input-group">
                        <input type="text" id="publicAttendanceUrlInput" class="form-control form-control-sm font-monospace bg-light border-end-0" 
                               value="{{ str_replace(['127.0.0.1', 'localhost'], '192.168.1.81', $training->public_attendance_url) }}" readonly>
                        <button class="btn btn-primary btn-sm px-3 fw-bold" type="button" onclick="copyAttendanceLink()">
                            <i class="bi bi-clipboard me-1"></i> Salin
                        </button>
                    </div>
                </div>

                <!-- Panduan Koneksi HP -->
                <div class="alert alert-warning py-2.5 px-3 text-start small mb-0 rounded-3 border-0 bg-warning-subtle text-warning-emphasis">
                    <div class="fw-bold mb-1" style="font-size: 0.76rem;"><i class="bi bi-info-circle-fill me-1 text-warning"></i> Syarat Agar HP Peserta Bisa Membuka:</div>
                    <ol class="mb-0 ps-3" style="font-size: 0.72rem; line-height: 1.5;">
                        <li>HP peserta dan Laptop harus terhubung ke <strong>Wi-Fi / Hotspot yang sama</strong>.</li>
                        <li>Pastikan perintah di terminal laptop dijalankan dengan opsi:<br>
                            <code class="bg-white px-1.5 py-0.5 rounded text-dark fw-bold border d-inline-block mt-0.5">php artisan serve --host=0.0.0.0 --port=8000</code>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="modal-footer bg-light border-top px-4 py-2.5 d-flex justify-content-between">
                <a id="shareModalDirectLink" href="{{ str_replace(['127.0.0.1', 'localhost'], '192.168.1.81', $training->public_attendance_url) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka di Tab Baru
                </a>
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const originalLocalUrl = @json($training->public_attendance_url);
    const attendanceToken = @json($training->public_token);
    let currentShareMode = 'hp';

    function switchShareMode(mode) {
        currentShareMode = mode;
        const hpBtn = document.getElementById('modeHpBtn');
        const localBtn = document.getElementById('modeLocalBtn');
        const ipBox = document.getElementById('lanIpSettingsBox');

        if (mode === 'hp') {
            hpBtn.className = 'btn btn-sm rounded-pill px-3 py-1 fw-bold btn-primary';
            localBtn.className = 'btn btn-sm rounded-pill px-3 py-1 fw-bold btn-outline-secondary';
            if (ipBox) ipBox.style.display = 'block';
        } else {
            localBtn.className = 'btn btn-sm rounded-pill px-3 py-1 fw-bold btn-secondary';
            hpBtn.className = 'btn btn-sm rounded-pill px-3 py-1 fw-bold btn-outline-primary';
            if (ipBox) ipBox.style.display = 'none';
        }

        updateShareUrl();
    }

    function updateShareUrl() {
        let finalUrl = '';
        if (currentShareMode === 'hp') {
            const ipInput = document.getElementById('customLanIpInput');
            const portInput = document.getElementById('customLanPortInput');
            const ip = (ipInput ? ipInput.value.trim() : '') || '192.168.1.81';
            const port = (portInput ? portInput.value.trim() : '') || '8000';
            const portStr = (port && port !== '80' && port !== '443') ? (':' + port) : '';
            finalUrl = 'http://' + ip + portStr + '/presensi/' + attendanceToken;
        } else {
            finalUrl = originalLocalUrl;
        }

        document.getElementById('publicAttendanceUrlInput').value = finalUrl;
        document.getElementById('shareModalDirectLink').href = finalUrl;
        document.getElementById('shareModalQrImage').src = 'https://api.qrserver.com/v1/create-qr-code/?size=210x210&margin=10&data=' + encodeURIComponent(finalUrl);
    }

    function copyAttendanceLink(customUrl = null) {
        const inputEl = document.getElementById('publicAttendanceUrlInput');
        const url = customUrl || (inputEl ? inputEl.value : '');
        if (!url) return;

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(() => {
                alert('Tautan presensi berhasil disalin ke clipboard!\n\nLink: ' + url);
            }).catch(() => {
                fallbackCopy(url);
            });
        } else {
            fallbackCopy(url);
        }
    }

    function fallbackCopy(text) {
        const input = document.getElementById('publicAttendanceUrlInput');
        if (input) {
            input.select();
            document.execCommand('copy');
            alert('Tautan presensi berhasil disalin!');
        }
    }

    function openEditParticipantModal(p) {
        document.getElementById('editParticipantForm').action = '/admin/trainings/{{ $training->id }}/participants/' + p.id;
        document.getElementById('edit_part_name').value = p.name;
        document.getElementById('edit_part_nip').value = p.nip_or_id || '';
        document.getElementById('edit_part_cat').value = p.category;
        document.getElementById('edit_part_agency').value = p.agency || '';
        document.getElementById('edit_part_phone').value = p.phone || '';
        document.getElementById('edit_part_email').value = p.email || '';
        document.getElementById('edit_part_att').value = p.attendance_status;
        document.getElementById('edit_part_cert').value = p.certificate_number || '';

        const modal = new bootstrap.Modal(document.getElementById('editParticipantModal'));
        modal.show();
    }
</script>
@endpush

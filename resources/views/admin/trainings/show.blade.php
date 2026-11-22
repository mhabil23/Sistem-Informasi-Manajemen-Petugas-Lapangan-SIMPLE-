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
        overflow: hidden;
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
    @media (max-width: 767.98px) {
        .bento-table thead {
            display: none;
        }
        .bento-table, .bento-table tbody, .bento-table tr, .bento-table td {
            display: block;
            width: 100% !important;
        }
        .bento-table tr {
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.5rem;
            background: #fff;
        }
        .bento-table td {
            text-align: right;
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .bento-table td:last-child {
            border-bottom: none;
        }
        .bento-table td::before {
            content: attr(data-label);
            float: left;
            font-weight: 600;
            color: #64748b;
            font-size: 0.75rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Header Card Detail Pelatihan -->
<div class="bento-card mb-4">
    <div class="p-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <a href="{{ route('admin.trainings.index') }}" class="btn btn-sm btn-outline-secondary mb-2 rounded-pill px-3 py-1" style="font-size: 0.8rem;">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Agenda
                </a>
                <h4 class="fw-bold text-dark mb-1">{{ $training->title }}</h4>
                <div class="d-flex flex-wrap gap-3 text-secondary small mt-2">
                    <div><i class="bi bi-calendar-check text-primary me-1"></i> <strong>{{ $training->start_date->format('d M Y') }} - {{ $training->end_date->format('d M Y') }}</strong></div>
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
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <div class="d-flex flex-wrap justify-content-lg-end gap-2">
                    <button type="button" class="btn btn-bps-orange rounded-pill px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#createParticipantModal" style="font-size: 0.85rem;">
                        <i class="bi bi-person-plus me-1"></i> Tambah Peserta
                    </button>
                    <a href="{{ route('admin.trainings.attendance.pdf', $training) }}" class="btn btn-outline-danger rounded-pill px-3 py-1.5" style="font-size: 0.85rem;">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Cetak Daftar Hadir PDF
                    </a>
                </div>
            </div>
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
                    <option value="all">-- Semua Kategori Peserta --</option>
                    @foreach($availableCategories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="attendance" class="form-select">
                    <option value="all">-- Semua Status Hadir --</option>
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
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check2 me-1"></i> Hadir
                                </span>
                            @elseif($p->attendance_status === 'excused')
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                    <i class="bi bi-clock me-1"></i> Izin
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                    <i class="bi bi-x me-1"></i> Alpa
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
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
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
@endsection

@push('scripts')
<script>
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

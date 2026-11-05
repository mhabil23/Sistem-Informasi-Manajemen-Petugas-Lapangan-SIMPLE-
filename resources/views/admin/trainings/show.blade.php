@extends('layouts.app')

@section('title', 'Kelola Peserta Pelatihan - ' . $training->title)
@section('page_title', 'Administrasi Peserta Pelatihan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.trainings.index') }}">Pelatihan</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ \Illuminate\Support\Str::limit($training->title, 25) }}</li>
@endsection

@section('content')
<!-- Header Card -->
<div class="sp-card mb-4">
    <div class="sp-card-body p-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <a href="{{ route('admin.trainings.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar Agenda
                </a>
                <h3 class="fw-bold text-dark mb-1">{{ $training->title }}</h3>
                <div class="d-flex flex-wrap gap-3 text-secondary small mt-2">
                    <div><i class="fa-regular fa-calendar-check text-primary me-1"></i> <strong>{{ $training->start_date->format('d M Y') }} - {{ $training->end_date->format('d M Y') }}</strong></div>
                    <div><i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $training->location }}</div>
                    <div><i class="fa-solid fa-users text-info me-1"></i> Kuota: <strong>{{ $training->quota ?: 'Tak Terbatas' }}</strong></div>
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
                    <button type="button" class="btn btn-sp-primary" data-bs-toggle="modal" data-bs-target="#createParticipantModal">
                        <i class="fa-solid fa-user-plus me-1"></i> Tambah Peserta
                    </button>
                    <a href="{{ route('admin.trainings.attendance.pdf', $training) }}" class="btn btn-outline-danger">
                        <i class="fa-solid fa-file-pdf me-1"></i> Cetak Daftar Hadir PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stat Cards Kehadiran -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Total Peserta</div>
                <div class="sp-stat-value">{{ number_format($participants->total()) }}</div>
                <div class="sp-stat-desc">Terdaftar di kegiatan</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Hadir Presensi</div>
                <div class="sp-stat-value text-success">{{ number_format($presentCount) }}</div>
                <div class="sp-stat-desc">Mengikuti pelatihan</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #fef3c7; color: #d97706;">
                <i class="fa-solid fa-user-clock"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Izin / Sakit</div>
                <div class="sp-stat-value text-warning">{{ number_format($excusedCount) }}</div>
                <div class="sp-stat-desc">Pemberitahuan resmi</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #fee2e2; color: #dc2626;">
                <i class="fa-solid fa-user-xmark"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Tidak Hadir (Alpa)</div>
                <div class="sp-stat-value text-danger">{{ number_format($absentCount) }}</div>
                <div class="sp-stat-desc">Tanpa keterangan</div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="sp-card mb-4">
    <div class="sp-card-body p-3">
        <form method="GET" action="{{ route('admin.trainings.show', $training) }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
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
                <button type="submit" class="btn btn-sp-primary flex-fill">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'category', 'attendance']))
                    <a href="{{ route('admin.trainings.show', $training) }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Participants Table Card -->
<div class="sp-card mb-4">
    <div class="sp-card-header">
        <h5 class="sp-card-title">
            <i class="fa-solid fa-id-card text-primary"></i>
            Daftar Peserta & Presensi ({{ $participants->total() }})
        </h5>
    </div>
    <div class="sp-card-body p-0">
        <div class="table-responsive">
            <table class="table sp-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">No.</th>
                        <th style="min-width: 200px;">Nama Peserta</th>
                        <th style="min-width: 130px;">Kategori</th>
                        <th style="min-width: 170px;">Instansi / Asal</th>
                        <th style="min-width: 140px;">Kontak (HP/Email)</th>
                        <th>Presensi</th>
                        <th>No. Sertifikat</th>
                        <th class="text-end" style="min-width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($participants as $idx => $p)
                        <tr>
                            <td class="text-muted small">{{ $participants->firstItem() + $idx }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $p->name }}</div>
                                @if($p->nip_or_id)
                                    <small class="text-muted font-monospace">NIP/ID: {{ $p->nip_or_id }}</small>
                                @endif
                            </td>
                            <td>
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
                            <td>
                                <div class="small text-secondary">{{ $p->agency ?: '-' }}</div>
                            </td>
                            <td>
                                <div class="small text-dark">{{ $p->phone ?: '-' }}</div>
                                @if($p->email)
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $p->email }}</div>
                                @endif
                            </td>
                            <td>
                                @if($p->attendance_status === 'present')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="fa-solid fa-check me-1"></i> Hadir
                                    </span>
                                @elseif($p->attendance_status === 'excused')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                        <i class="fa-solid fa-clock me-1"></i> Izin
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i class="fa-solid fa-xmark me-1"></i> Alpa
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="small font-monospace text-secondary">{{ $p->certificate_number ?: '-' }}</span>
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="sp-action-btn-group">
                                    <!-- Edit -->
                                    <button type="button" 
                                            class="btn btn-light border sp-action-btn text-warning" 
                                            title="Edit Data Peserta" 
                                            data-bs-toggle="tooltip"
                                            onclick="openEditParticipantModal({{ json_encode($p) }})">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>

                                    <!-- Hapus -->
                                    <form method="POST" action="{{ route('admin.trainings.participants.destroy', [$training, $p]) }}" class="d-inline" onsubmit="return confirm('Hapus peserta {{ addslashes($p->name) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-light border sp-action-btn text-danger" 
                                                title="Hapus Peserta" 
                                                data-bs-toggle="tooltip">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-users-slash fa-3x mb-3 text-secondary opacity-50"></i>
                                <div class="fw-semibold">Belum ada peserta yang didaftarkan.</div>
                                <div class="small">Klik tombol "Tambah Peserta" untuk menambahkan data peserta pelatihan.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($participants->hasPages())
        <div class="sp-card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
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
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="createParticipantModalLabel">
                    <i class="fa-solid fa-user-plus text-primary me-2"></i> Tambah Peserta Pelatihan
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
                    <button type="submit" class="btn btn-sp-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Peserta
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
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="editParticipantModalLabel">
                    <i class="fa-solid fa-pen-to-square text-warning me-2"></i> Edit Data Peserta
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


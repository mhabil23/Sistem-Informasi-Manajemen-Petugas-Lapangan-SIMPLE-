@extends('layouts.app')

@section('title', 'Riwayat Penugasan Lapangan')
@section('page_title', 'Riwayat Penugasan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.assignments.index') }}" class="text-decoration-none">Kegiatan &amp; Penugasan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Riwayat Penugasan</li>
@endsection

@push('styles')
<style>
.bento-card-clean {
    background: #FFFFFF;
    border-radius: 20px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
}
</style>
@endpush

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-primary"></i> Riwayat &amp; Log Penugasan Lapangan
        </h4>
        <p class="text-muted small mb-0">
            Arsip penugasan yang telah berakhir / selesai beserta catatan aktivitas pengaturan status &amp; periode oleh Administrator.
        </p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <span class="badge {{ $globalAssignmentEnabled ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} rounded-pill px-3 py-1.5 small">
            Status Global: {{ $globalAssignmentEnabled ? 'AKTIF' : 'NONAKTIF' }}
        </span>
        <a href="{{ route('admin.assignments.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Penugasan
        </a>
    </div>
</div>

<!-- Filter -->
<div class="bento-card-clean p-3 mb-4">
    <form method="GET" action="{{ route('admin.assignments.history') }}" class="row g-2 align-items-center">
        <div class="col-md-9">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0" value="{{ request('search') }}" placeholder="Cari nama penugasan atau lokasi dalam riwayat...">
            </div>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 flex-fill">Cari Riwayat</button>
            @if(request('search'))
                <a href="{{ route('admin.assignments.history') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Reset</a>
            @endif
        </div>
    </form>
</div>

<!-- Daftar Penugasan Berakhir / Selesai / Diarsipkan -->
<div class="bento-card-clean overflow-hidden mb-4">
    <div class="px-3 px-md-4 py-3 border-bottom bg-light d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-archive me-1.5 text-secondary"></i> Daftar Penugasan Berakhir / Selesai / Diarsipkan</h6>
        <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2.5 py-1 small">{{ $historicalAssignments->total() }} Kegiatan</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Nama Penugasan</th>
                    <th>Lokasi</th>
                    <th>Periode Tanggal</th>
                    <th>Status Tampil</th>
                    <th class="text-center">Petugas</th>
                    <th class="text-center">Sampel</th>
                    <th class="pe-4 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($historicalAssignments as $item)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $item->title }}</div>
                            @if($item->deleted_at)
                                <span class="badge bg-danger-subtle text-danger border rounded-pill" style="font-size: 0.65rem;">Diarsipkan ({{ $item->deleted_at->format('d/m/Y') }})</span>
                            @endif
                        </td>
                        <td>{{ $item->location }}</td>
                        <td>
                            {{ $item->start_date ? $item->start_date->format('d/m/Y') : '-' }}
                            s.d.
                            {{ $item->end_date ? $item->end_date->format('d/m/Y') : '-' }}
                        </td>
                        <td>
                            <span class="badge {{ $item->visibility_status_badge_class }} rounded-pill px-2.5 py-1" style="font-size: 0.7rem;">
                                {{ $item->visibility_status_label }}
                            </span>
                        </td>
                        <td class="text-center">{{ $item->officers_count }}</td>
                        <td class="text-center">{{ $item->respondents_count }}</td>
                        <td class="pe-4 text-end">
                            @if(!$item->deleted_at)
                                <a href="{{ route('admin.assignments.show', $item) }}" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1">
                                    Detail
                                </a>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            Belum ada riwayat penugasan yang berakhir atau diarsipkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($historicalAssignments->hasPages())
        <div class="p-3 border-top bg-white">
            {{ $historicalAssignments->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Log Aktivitas Pengaturan Penugasan -->
<div class="bento-card-clean overflow-hidden">
    <div class="px-4 py-3 border-bottom bg-light d-flex justify-content-between align-items-center">
        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-journal-text me-1.5 text-primary"></i> Riwayat Perubahan Pengaturan &amp; Kontrol Admin</h6>
        <span class="text-muted small">{{ $activityLogs->total() }} Catatan</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Waktu</th>
                    <th>Pengguna</th>
                    <th>Aksi</th>
                    <th class="pe-4">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activityLogs as $log)
                    <tr>
                        <td class="ps-4 text-muted text-nowrap">{{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="fw-semibold text-dark">{{ $log->user ? $log->user->name : 'Sistem' }}</td>
                        <td><span class="badge bg-light text-dark border rounded-pill px-2">{{ $log->action }}</span></td>
                        <td class="pe-4">{{ $log->description }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Belum ada catatan aktivitas penugasan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($activityLogs->hasPages())
        <div class="p-3 border-top bg-white">
            {{ $activityLogs->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

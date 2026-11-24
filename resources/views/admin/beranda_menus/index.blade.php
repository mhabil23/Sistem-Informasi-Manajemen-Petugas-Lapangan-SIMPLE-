@extends('layouts.app')

@section('title', 'Kontrol Menu Beranda Petugas - SIMPEL')
@section('page_title', 'Kontrol Menu Beranda Petugas')

@push('styles')
<style>
.bento-card-clean {
    background: #FFFFFF;
    border-radius: 20px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
}
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-sliders text-primary"></i> Kontrol Tampil Menu Beranda Petugas
            </h3>
            <p class="text-muted small mb-0">
                Nyalakan, matikan, atau jadwalkan periode masing-masing menu Beranda Petugas secara terpisah satu per satu maupun seluruhnya sekaligus.
            </p>
        </div>
        <a href="{{ route('admin.spkrt-periods.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold">
            <i class="bi bi-calendar-range me-1"></i> Kelola Periode SPKRT
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle-fill me-1"></i> Terjadi kesalahan input:</div>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @include('admin.partials._beranda_menu_control', ['berandaState' => $berandaState])

    <div class="bento-card-clean p-4">
        <h6 class="fw-bold text-dark mb-2">
            <i class="bi bi-info-circle-fill text-primary me-1.5"></i> Aturan Tampil di Beranda Petugas
        </h6>
        <ul class="text-secondary small mb-0 ps-3 d-flex flex-column gap-1.5">
            <li>Secara bawaan (<strong>Nonaktif</strong> &amp; belum ada periode aktif), ke-4 menu tersebut <strong>disembunyikan</strong> dari halaman Beranda Petugas.</li>
            <li>Setiap menu memiliki tombol <strong>Nyalakan / Matikan</strong> dan <strong>Periode Tanggal</strong> sendiri-sendiri, sehingga Admin dapat menyalakan 1, 2, 3, atau seluruh 4 menu sesuai kebutuhan.</li>
            <li>Sebuah menu tampil di Beranda apabila <strong>tombol menu tersebut dinyalakan Admin</strong> <strong>ATAU</strong> <strong>tanggal hari ini masuk ke rentang periode menu tersebut</strong> (<code>Mulai &le; Hari Ini &le; Selesai</code>).</li>
            <li>Seluruh route, controller, model, dan data tetap aktif serta tidak ada fitur yang dihapus.</li>
        </ul>
    </div>
</div>
@endsection

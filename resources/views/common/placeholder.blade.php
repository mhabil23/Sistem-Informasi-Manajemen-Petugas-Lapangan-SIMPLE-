@extends('layouts.app')

@section('title', $title ?? 'Modul SIMPLE')
@section('page_title', $title ?? 'Modul SIMPLE')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ $title ?? 'Modul' }}</li>
@endsection

@section('content')
<div class="sp-card text-center py-5">
    <div class="sp-card-body">
        <div class="mb-3 text-primary opacity-75">
            <i class="fa-solid fa-screwdriver-wrench fa-3x"></i>
        </div>
        <h4 class="fw-bold text-dark">{{ $title ?? 'Modul SIMPLE' }}</h4>
        <p class="text-muted max-w-md mx-auto mb-4" style="max-width: 500px;">
            Struktur basis data, model Eloquent, dan relasi tabel untuk modul ini telah berhasil disiapkan pada <strong>Tahap 1</strong>. Modul ini siap dilanjutkan pada tahap berikutnya sesuai urutan instruksi.
        </p>
        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('petugas.dashboard') }}" class="btn btn-sp-primary">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection


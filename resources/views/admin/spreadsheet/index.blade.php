@extends('layouts.app')

@section('title', 'Database Google Spreadsheet')
@section('page_title', 'Konfigurasi Database Spreadsheet')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Database Spreadsheet</li>
@endsection

@section('content')
<div class="row g-4 mb-4">
    <!-- Status & Konfigurasi URL -->
    <div class="col-lg-6">
        <div class="sp-card h-100 mb-0">
            <div class="sp-card-header">
                <h5 class="sp-card-title">
                    <i class="fa-solid fa-table-cells text-success"></i>
                    Status Koneksi Google Spreadsheet
                </h5>
                @if($isConfigured)
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                        <i class="fa-solid fa-circle-check me-1"></i> Terkonfigurasi
                    </span>
                @else
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Belum Dihubungkan
                    </span>
                @endif
            </div>
            <div class="sp-card-body">
                <p class="text-secondary small mb-3">
                    Website SIMPLE dapat membaca dan menulis seluruh data transaksi (pengguna, responden, penugasan, BAST, jadwal, hingga pelatihan) langsung ke <strong>Google Spreadsheet</strong> via Web App Apps Script.
                </p>

                <form method="POST" action="{{ route('admin.spreadsheet.update') }}" class="mb-4">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">URL WEB APP GOOGLE APPS SCRIPT</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="fa-solid fa-link"></i>
                            </span>
                            <input type="url" 
                                   name="webapp_url" 
                                   class="form-control border-start-0 @error('webapp_url') is-invalid @enderror" 
                                   value="{{ old('webapp_url', $webAppUrl) }}" 
                                   placeholder="https://script.google.com/macros/s/.../exec"
                                   required>
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">
                            Pastikan URL berakhiran <code>/exec</code> hasil deployment Web App.
                        </small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sp-primary">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan URL
                        </button>
                    </div>
                </form>

                @if($isConfigured)
                    <div class="border-top pt-3">
                        <div class="d-flex flex-wrap gap-2">
                            <form method="POST" action="{{ route('admin.spreadsheet.test') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    <i class="fa-solid fa-satellite-dish me-1"></i> Uji Koneksi (Ping)
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.spreadsheet.init') }}" class="d-inline" onsubmit="return confirm('Inisialisasi 14 tab sheet pada Google Spreadsheet sekarang?');">
                                @csrf
                                <button type="submit" class="btn btn-outline-success btn-sm">
                                    <i class="fa-solid fa-layer-group me-1"></i> Inisialisasi 14 Tab Sheet
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 14 Tabel yang Disinkronkan -->
    <div class="col-lg-6">
        <div class="sp-card h-100 mb-0">
            <div class="sp-card-header">
                <h5 class="sp-card-title">
                    <i class="fa-solid fa-sheet-plastic text-primary"></i>
                    14 Tab Sheet Database
                </h5>
                <span class="badge bg-light text-secondary border">Skema Relasional</span>
            </div>
            <div class="sp-card-body p-0">
                <div class="table-responsive" style="max-height: 290px; overflow-y: auto;">
                    <table class="table sp-table table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Tab Sheet</th>
                                <th>Keterangan Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>1</td><td><strong class="text-primary">users</strong></td><td>Akun login (Admin, Petugas), peran & status</td></tr>
                            <tr><td>2</td><td><strong class="text-primary">assignments</strong></td><td>Daftar kegiatan & penugasan lapangan</td></tr>
                            <tr><td>3</td><td><strong class="text-primary">assignment_officers</strong></td><td>Petugas penanggung jawab & target tugas</td></tr>
                            <tr><td>4</td><td><strong class="text-primary">respondents</strong></td><td>Data responden, alamat, kontak & instansi</td></tr>
                            <tr><td>5</td><td><strong class="text-primary">respondent_basts</strong></td><td>Nomor & arsip Berita Acara Responden</td></tr>
                            <tr><td>6</td><td><strong class="text-primary">officer_basts</strong></td><td>Berita Acara Serah Terima tugas Petugas</td></tr>
                            <tr><td>7</td><td><strong class="text-primary">officer_reports</strong></td><td>Laporan kerja harian, status revisi/approval</td></tr>
                            <tr><td>8</td><td><strong class="text-primary">report_attachments</strong></td><td>Foto bukti dokumentasi laporan kegiatan</td></tr>
                            <tr><td>9</td><td><strong class="text-primary">training_events</strong></td><td>Kegiatan pelatihan bimtek SP4RT & kuota</td></tr>
                            <tr><td>10</td><td><strong class="text-primary">training_participants</strong></td><td>Peserta pelatihan (Pegawai, Magang, DPW)</td></tr>
                            <tr><td>11</td><td><strong class="text-primary">document_templates</strong></td><td>Template format kop & isi dokumen resmi</td></tr>
                            <tr><td>12</td><td><strong class="text-primary">generated_documents</strong></td><td>Arsip PDF dokumen yang telah dicetak</td></tr>
                            <tr><td>13</td><td><strong class="text-primary">schedules</strong></td><td>Jadwal kegiatan & penugasan waktu</td></tr>
                            <tr><td>14</td><td><strong class="text-primary">activity_logs</strong></td><td>Audit trail aktivitas pengguna di sistem</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Petunjuk Pemasangan Google Apps Script -->
<div class="sp-card mb-4">
    <div class="sp-card-header">
        <h5 class="sp-card-title">
            <i class="fa-solid fa-code text-warning"></i>
            Panduan 2 Menit Menghubungkan Google Spreadsheet
        </h5>
        <button type="button" class="btn btn-sm btn-sp-accent" id="copyCodeBtn" onclick="copyScriptCode()">
            <i class="fa-regular fa-copy me-1"></i> Salin Kode Apps Script
        </button>
    </div>
    <div class="sp-card-body">
        <div class="row g-4">
            <div class="col-lg-5">
                <ol class="small text-secondary ps-3 mb-0" style="line-height: 1.8;">
                    <li>Buka <a href="https://drive.google.com" target="_blank" class="fw-semibold">Google Drive</a> dan buat <strong>Google Spreadsheet baru</strong> (beri nama misalnya: <code>Database SIMPLE</code>).</li>
                    <li>Pada menu atas Spreadsheet, klik <strong>Ekstensi (Extensions)</strong> &gt; <strong>Apps Script</strong>.</li>
                    <li>Hapus kode bawaan yang ada, lalu klik tombol <strong>"Salin Kode Apps Script"</strong> di sebelah kanan dan tempelkan ke editor Apps Script.</li>
                    <li>Klik ikon <strong>Simpan</strong> (ikon disket).</li>
                    <li>Klik tombol biru <strong>Terapkan (Deploy)</strong> &gt; <strong>Deployment Baru (New deployment)</strong>.</li>
                    <li>Pilih jenis <strong>Aplikasi Web (Web app)</strong>:
                        <ul>
                            <li>Jalankan sebagai: <strong>Saya (Me)</strong></li>
                            <li>Akses: <strong class="text-danger">Siapa saja (Anyone)</strong> <em>(wajib agar server web SIMPLE dapat terhubung)</em>.</li>
                        </ul>
                    </li>
                    <li>Klik <strong>Deploy</strong>, lalu salin <strong>URL Aplikasi Web</strong> yang berakhiran <code>/exec</code>.</li>
                    <li>Tempelkan URL tersebut ke kolom konfigurasi di atas lalu klik <strong>Simpan URL</strong>.</li>
                </ol>
            </div>
            <div class="col-lg-7">
                <div class="position-relative">
                    <pre class="bg-dark text-white p-3 rounded small mb-0" style="max-height: 290px; overflow-y: auto; font-family: Consolas, monospace;"><code id="gasCode">{{ $scriptContent }}</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyScriptCode() {
        const code = document.getElementById('gasCode').innerText;
        navigator.clipboard.writeText(code).then(() => {
            const btn = document.getElementById('copyCodeBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Tersalin!';
            btn.classList.remove('btn-sp-accent');
            btn.classList.add('btn-success');
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-sp-accent');
            }, 2500);
        }).catch(err => {
            alert('Gagal menyalin kode secara otomatis: ' + err);
        });
    }
</script>
@endpush


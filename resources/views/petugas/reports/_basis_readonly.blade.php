<!-- Bagian B: Dasar Pelaksanaan (Read-Only - Diisi oleh Role Admin) -->
<div class="card mb-3 border border-secondary-subtle rounded-3">
    <div class="card-header bg-light py-2 px-3 fw-bold text-dark small d-flex flex-wrap justify-content-between align-items-center gap-1">
        <span><i class="bi bi-file-earmark-ruled text-primary me-1"></i> B. DASAR PELAKSANAAN (LAPORAN &amp; PENGELUARAN RIIL)</span>
        <span class="badge bg-secondary-subtle text-dark border border-secondary-subtle rounded-pill">
            <i class="bi bi-lock me-1"></i> Diisi oleh Role Admin
        </span>
    </div>
    <div class="card-body p-3 bg-light-subtle">
        <div class="mb-2">
            <div class="text-muted small fw-semibold mb-1" style="font-size: 0.75rem;">1. DIPA Satuan Kerja</div>
            <div id="{{ $prefix }}_ro_basis_dipa" class="p-2 bg-white border rounded-3 small text-dark">{{ $defaults['basis_dipa'] ?? '-' }}</div>
        </div>
        <div class="row g-2 mb-2">
            <div class="col-md-7">
                <div class="text-muted small fw-semibold mb-1" style="font-size: 0.75rem;">2a. Nomor Surat Tugas</div>
                <div id="{{ $prefix }}_ro_st_number" class="p-2 bg-white border rounded-3 small font-monospace text-dark">{{ $defaults['st_number'] ?? '-' }}</div>
            </div>
            <div class="col-md-5">
                <div class="text-muted small fw-semibold mb-1" style="font-size: 0.75rem;">2b. Tanggal Surat Tugas</div>
                <div id="{{ $prefix }}_ro_st_date" class="p-2 bg-white border rounded-3 small text-dark">{{ !empty($defaults['st_date']) ? \Carbon\Carbon::parse($defaults['st_date'])->translatedFormat('d F Y') : '-' }}</div>
            </div>
        </div>
        <div>
            <div class="text-muted small fw-semibold mb-1" style="font-size: 0.75rem;">Teks Lengkap Dasar Surat Tugas</div>
            <div id="{{ $prefix }}_ro_basis_st" class="p-2 bg-white border rounded-3 small text-dark">{{ $defaults['basis_st'] ?? '-' }}</div>
        </div>
        <div class="form-text small mt-2 text-muted" style="font-size: 0.72rem;">
            <i class="bi bi-info-circle text-primary me-1"></i> Dasar pelaksanaan (DIPA &amp; Surat Tugas) ditetapkan oleh Administrator dan otomatis masuk ke 1 PDF gabungan.
        </div>
    </div>
</div>

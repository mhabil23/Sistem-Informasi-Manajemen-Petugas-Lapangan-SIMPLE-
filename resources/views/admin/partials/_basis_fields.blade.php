<div class="card mb-3 border border-primary-subtle">
    <div class="card-header bg-light py-2 px-3 fw-bold text-dark small d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-scale-balanced text-primary me-1"></i> B. DASAR PELAKSANAAN (LAPORAN &amp; PENGELUARAN RIIL)</span>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Diisi Role Admin</span>
    </div>
    <div class="card-body p-3">
        <div class="mb-3">
            <label class="form-label small fw-semibold text-secondary">1. DIPA Satuan Kerja @if(!empty($required)) <span class="text-danger">*</span> @endif</label>
            <textarea id="{{ $prefix }}_basis_dipa" name="basis_dipa" class="form-control" rows="2" @if(!empty($required)) required @endif>{{ $defaults['basis_dipa'] ?? '' }}</textarea>
        </div>
        <div class="row g-3 mb-2">
            <div class="col-md-7">
                <label class="form-label small fw-semibold text-secondary">2a. Nomor Surat Tugas @if(!empty($required)) <span class="text-danger">*</span> @endif</label>
                <input type="text" id="{{ $prefix }}_st_number" name="st_number" class="form-control" value="{{ $defaults['st_number'] ?? '' }}" oninput="syncAdminBasisSt('{{ $prefix }}')" @if(!empty($required)) required @endif>
            </div>
            <div class="col-md-5">
                <label class="form-label small fw-semibold text-secondary">2b. Tanggal Surat Tugas @if(!empty($required)) <span class="text-danger">*</span> @endif</label>
                <input type="date" id="{{ $prefix }}_st_date" name="st_date" class="form-control" value="{{ $defaults['st_date'] ?? '' }}" oninput="syncAdminBasisSt('{{ $prefix }}')" @if(!empty($required)) required @endif>
            </div>
        </div>
        <div>
            <label class="form-label small fw-semibold text-secondary">Teks Lengkap Dasar Surat Tugas (Otomatis Terbentuk)</label>
            <textarea id="{{ $prefix }}_basis_st" name="basis_st" class="form-control" rows="2">{{ $defaults['basis_st'] ?? '' }}</textarea>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
    function syncAdminBasisSt(prefix) {
        const numEl = document.getElementById(prefix + '_st_number');
        const dateEl = document.getElementById(prefix + '_st_date');
        const outEl = document.getElementById(prefix + '_basis_st');
        if (!outEl) return;

        const months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        const num = (numEl && numEl.value.trim()) ? numEl.value.trim() : '-';
        let dateFormatted = '-';
        if (dateEl && dateEl.value) {
            const parts = dateEl.value.substring(0, 10).split('-');
            if (parts.length === 3) {
                const m = parseInt(parts[1], 10) - 1;
                const d = parseInt(parts[2], 10);
                if (!isNaN(d) && m >= 0 && m <= 11) {
                    dateFormatted = `${d} ${months[m]} ${parts[0]}`;
                }
            }
        }
        outEl.value = `Surat Tugas Kepala Badan Pusat Statistik Kabupaten Kolaka Utara Nomor ${num} Tanggal ${dateFormatted}.`;
    }
</script>
@endpush
@endonce

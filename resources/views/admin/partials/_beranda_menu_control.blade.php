@php
    $berandaState = $berandaState ?? app(\App\Services\BerandaMenuVisibilityService::class)->getState();
    $menus = $berandaState['menus'] ?? [];
    $visibleCount = (int) ($berandaState['visible_count'] ?? 0);
    $totalCount = (int) ($berandaState['total_count'] ?? count($menus));
    $allManualOn = (bool) ($berandaState['all_manually_enabled'] ?? false);
    $anyManualOn = (bool) ($berandaState['is_manually_enabled'] ?? false);
@endphp

@once
<style>
.beranda-date-input {
    width: 122px;
    flex: 0 0 auto;
}
@media (min-width: 768px) and (max-width: 1399.98px) {
    .beranda-control-table {
        min-width: 860px;
    }
}
@media (max-width: 767.98px) {
    .beranda-control-card-summary {
        padding: 1.15rem !important;
    }
    .beranda-control-table {
        table-layout: auto !important;
        min-width: 0 !important;
    }
    .beranda-control-table thead {
        display: none;
    }
    .beranda-control-table,
    .beranda-control-table tbody,
    .beranda-control-table tr,
    .beranda-control-table td {
        display: block;
        width: 100% !important;
    }
    .beranda-control-table tbody tr {
        margin: 0.65rem auto;
        width: calc(100% - 1.25rem) !important;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        overflow: hidden;
        box-sizing: border-box;
    }
    .beranda-control-table tbody td {
        display: block;
        padding: 0.75rem 0.9rem !important;
        border-bottom: 1px solid #F1F5F9;
        text-align: left !important;
        white-space: normal !important;
    }
    .beranda-control-table tbody td::before {
        content: attr(data-label);
        display: block;
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748B;
        letter-spacing: 0.03em;
        margin-bottom: 0.35rem;
    }
    .beranda-control-table tbody td:last-child {
        border-bottom: none;
        background: #F8FAFC;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }
    .beranda-control-table tbody td:last-child::before {
        margin-bottom: 0;
    }
    .beranda-date-input {
        width: auto !important;
        flex: 1 1 115px;
        min-width: 110px;
    }
}
</style>
@endonce

<div class="mb-4 beranda-control-wrapper">
    <!-- Master Summary & Global Action Bento Card -->
    <div class="bento-card-clean beranda-control-card-summary p-4 mb-4 border shadow-sm" 
         style="border-radius: 20px; {{ $visibleCount > 0 ? 'border-color: #86EFAC !important; background: linear-gradient(135deg, #FFFFFF 0%, #F0FDF4 100%);' : 'border-color: #FECACA !important; background: linear-gradient(135deg, #FFFFFF 0%, #FEF2F2 100%);' }}">
        
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3" style="min-width: 0; flex: 1 1 260px;">
                <div class="rounded-4 d-flex align-items-center justify-content-center text-white flex-shrink-0 shadow-sm" 
                     style="width: 50px; height: 50px; background: {{ $visibleCount > 0 ? '#16A34A' : '#DC2626' }};">
                    <i class="bi {{ $visibleCount > 0 ? 'bi-eye-fill' : 'bi-eye-slash-fill' }} fs-4"></i>
                </div>
                <div style="min-width: 0; flex: 1 1 auto;">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">
                            Kontrol Tampil Menu Beranda Petugas
                        </h5>
                        <span class="badge {{ $berandaState['status_badge_class'] }} rounded-pill px-3 py-1 font-monospace" style="font-size: 0.75rem;">
                            {{ $berandaState['status_label'] }}
                        </span>
                    </div>
                    <div class="text-secondary small d-flex flex-wrap align-items-center gap-1.5" style="font-size: 0.82rem;">
                        <span>{{ $berandaState['status_description'] }}</span>
                        <span class="text-muted">&bull;</span>
                        <span class="fw-semibold {{ $visibleCount > 0 ? 'text-success' : 'text-danger' }}">
                            {{ $visibleCount }} dari {{ $totalCount }} Menu Ditampilkan di HP Petugas
                        </span>
                    </div>
                </div>
            </div>

            <!-- Global Action Switchers di Samping Kanan -->
            <div class="d-flex flex-wrap align-items-center gap-2 flex-shrink-0">
                <form method="POST" action="{{ route('admin.beranda-menus.toggle') }}" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="enabled" value="1">
                    <button type="submit" class="btn btn-sm {{ $allManualOn ? 'btn-success' : 'btn-outline-success' }} rounded-pill px-3.5 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-sm" {{ $allManualOn ? 'disabled' : '' }} style="font-size: 0.8rem;">
                        <i class="bi bi-power"></i> Nyalakan Semua ({{ $totalCount }})
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.beranda-menus.toggle') }}" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="enabled" value="0">
                    <button type="submit" class="btn btn-sm {{ !$anyManualOn ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill px-3.5 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-sm" {{ !$anyManualOn ? 'disabled' : '' }} style="font-size: 0.8rem;">
                        <i class="bi bi-eye-slash"></i> Matikan Semua
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Systematic Table: Seluruh Tombol On/Off Terletak Pasti di Samping Kanan (Tidak Pernah Pindah ke Bawah) -->
    <div class="bento-table-card mb-4 shadow-sm border" style="border-radius: 20px;">
        <div class="bento-table-header px-3 px-md-4 py-3 bg-light border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2.5">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background: #EFF6FF; color: #1D4ED8;">
                    <i class="bi bi-toggles fs-6"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.92rem;">Pengaturan Saklar &amp; Jadwal 4 Menu Beranda</h6>
                    <span class="text-muted" style="font-size: 0.72rem;">Tombol On/Off selalu berada konsisten di sisi samping kanan.</span>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table bento-table-fixed beranda-control-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 30%;" class="ps-4">MENU BERANDA PETUGAS</th>
                        <th style="width: 18%;">STATUS TAMPIL</th>
                        <th style="width: 34%;">JADWAL BUKA OTOMATIS (OPSIONAL)</th>
                        <th style="width: 18%;" class="text-end pe-4 text-nowrap">SAKLAR ON / OFF</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($menus as $menuKey => $menu)
                        @php
                            $isVisible = (bool) ($menu['is_visible'] ?? false);
                            $isManualOn = (bool) ($menu['is_manually_enabled'] ?? false);
                        @endphp
                        <tr style="background: {{ $isVisible ? '#FAFCFA' : '#FFFFFF' }};">
                            <!-- Kolom 1: Info Menu -->
                            <td class="ps-4 py-3" data-label="Menu Beranda Petugas">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-4 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" 
                                         style="width: 42px; height: 42px; background: {{ $menu['soft_bg'] }}; color: {{ $menu['color'] }};">
                                        <i class="bi {{ $menu['icon'] }} fs-5"></i>
                                    </div>
                                    <div class="overflow-hidden" style="min-width: 0;">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.88rem;" title="{{ $menu['label'] }}">{{ $menu['label'] }}</div>
                                        <div class="text-muted small text-truncate" style="font-size: 0.74rem;" title="{{ $menu['subtitle'] }}">{{ $menu['subtitle'] }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Kolom 2: Status Tampil -->
                            <td class="py-3" data-label="Status Tampil">
                                <div>
                                    <span class="badge {{ $menu['status_badge_class'] }} rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                        {{ $menu['status_label'] }}
                                    </span>
                                </div>
                                @if(!empty($menu['start_date_formatted']) && !empty($menu['end_date_formatted']))
                                    <div class="mt-1">
                                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                            <i class="bi bi-calendar-range me-1 text-primary"></i>{{ $menu['start_date_formatted'] }} - {{ $menu['end_date_formatted'] }}
                                        </span>
                                    </div>
                                @endif
                            </td>

                            <!-- Kolom 3: Jadwal Tanggal Inline -->
                            <td class="py-3" data-label="Jadwal Buka Otomatis (Opsional)">
                                <form method="POST" action="{{ route('admin.beranda-menus.update-period') }}" class="d-flex align-items-center gap-1.5 flex-wrap">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="menu_key" value="{{ $menuKey }}">
                                    <div class="beranda-date-input">
                                        <input type="date" name="start_date" class="form-control form-control-sm bg-white rounded-3 py-1 px-1.5" value="{{ $menu['start_date'] ?? '' }}" required style="font-size: 0.73rem;" title="Tanggal Mulai">
                                    </div>
                                    <span class="text-muted small flex-shrink-0">&rarr;</span>
                                    <div class="beranda-date-input">
                                        <input type="date" name="end_date" class="form-control form-control-sm bg-white rounded-3 py-1 px-1.5" value="{{ $menu['end_date'] ?? '' }}" required style="font-size: 0.73rem;" title="Tanggal Selesai">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm flex-shrink-0" style="font-size: 0.72rem;" title="Simpan Jadwal">
                                        <i class="bi bi-check2"></i> Simpan
                                    </button>
                                    @if(!empty($menu['start_date']) || !empty($menu['end_date']))
                                        <button type="submit" name="clear_period" value="1" formnovalidate class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 flex-shrink-0" title="Hapus Jadwal" style="font-size: 0.72rem;">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @endif
                                </form>
                            </td>

                            <!-- Kolom 4: SAKLAR ON / OFF SELALU TETAP DI SAMPING KANAN (TIDAK PINDAH KE BAWAH) -->
                            <td class="text-end pe-4 py-3 text-nowrap" data-label="Saklar On / Off">
                                <form method="POST" action="{{ route('admin.beranda-menus.toggle') }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="menu_key" value="{{ $menuKey }}">
                                    <input type="hidden" name="enabled" value="{{ $isManualOn ? '0' : '1' }}">
                                    @if($isManualOn)
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1.5 fw-bold shadow-sm d-inline-flex align-items-center justify-content-center gap-1.5 text-nowrap" title="Klik untuk mematikan menu ini" style="width: 120px; font-size: 0.76rem;">
                                            <i class="bi bi-toggle-on fs-6"></i> Menyala
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center justify-content-center gap-1.5 text-nowrap" title="Klik untuk menyalakan menu ini secara manual" style="width: 120px; font-size: 0.76rem; background: #F8FAFC;">
                                            <i class="bi bi-toggle-off fs-6 text-secondary"></i> Nonaktif
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Bar Jadwal Serentak di Bawah Tabel -->
        <div class="p-3 bg-light border-top px-3 px-md-4">
            <form method="POST" action="{{ route('admin.beranda-menus.update-period') }}" class="row g-2 align-items-center">
                @csrf
                @method('PATCH')
                <div class="col-12 col-lg-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-calendar2-range text-primary fs-6"></i>
                        <span class="fw-bold text-dark" style="font-size: 0.82rem;">Atur Jadwal Sekaligus untuk Seluruh 4 Menu:</span>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <input type="date" name="start_date" class="form-control form-control-sm bg-white rounded-3 py-1" required style="font-size: 0.74rem;" placeholder="Mulai Serentak">
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <input type="date" name="end_date" class="form-control form-control-sm bg-white rounded-3 py-1" required style="font-size: 0.74rem;" placeholder="Selesai Serentak">
                </div>
                <div class="col-12 col-md-4 col-lg-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 fw-semibold w-100" style="font-size: 0.72rem;">
                        Terapkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

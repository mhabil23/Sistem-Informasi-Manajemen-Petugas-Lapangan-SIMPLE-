@php
    $berandaState = $berandaState ?? app(\App\Services\BerandaMenuVisibilityService::class)->getState();
    $menus = $berandaState['menus'] ?? [];
    $visibleCount = (int) ($berandaState['visible_count'] ?? 0);
    $totalCount = (int) ($berandaState['total_count'] ?? count($menus));
    $allManualOn = (bool) ($berandaState['all_manually_enabled'] ?? false);
    $anyManualOn = (bool) ($berandaState['is_manually_enabled'] ?? false);
@endphp

<div class="bento-card-clean p-3.5 mb-4 border" style="{{ $visibleCount > 0 ? 'border-color: #86EFAC !important; background: linear-gradient(135deg, #FFFFFF 0%, #F0FDF4 100%);' : 'border-color: #FECACA !important; background: linear-gradient(135deg, #FFFFFF 0%, #FEF2F2 100%);' }}">
    <!-- Header & Batch Controls -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 pb-3 border-bottom" style="border-color: rgba(15, 23, 42, 0.08) !important;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-4 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width: 46px; height: 46px; background: {{ $visibleCount > 0 ? '#16A34A' : '#DC2626' }}; box-shadow: 0 4px 12px {{ $visibleCount > 0 ? 'rgba(22, 163, 74, 0.25)' : 'rgba(220, 38, 38, 0.25)' }};">
                <i class="bi {{ $visibleCount > 0 ? 'bi-eye-fill' : 'bi-eye-slash-fill' }} fs-5"></i>
            </div>
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-0.5">
                    <span class="fw-bold text-dark" style="font-size: 0.96rem;">Kontrol Tampil Menu Beranda Petugas</span>
                    <span class="badge {{ $berandaState['status_badge_class'] }} rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                        {{ $berandaState['status_label'] }}
                    </span>
                </div>
                <div class="text-secondary small" style="font-size: 0.78rem;">
                    {{ $berandaState['status_description'] }}
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <form method="POST" action="{{ route('admin.beranda-menus.toggle') }}" class="d-inline">
                @csrf
                @method('PATCH')
                <input type="hidden" name="enabled" value="1">
                <button type="submit" class="btn btn-sm {{ $allManualOn ? 'btn-success' : 'btn-outline-success' }} rounded-pill px-3 py-1.5 fw-semibold" {{ $allManualOn ? 'disabled' : '' }}>
                    <i class="bi bi-power me-1"></i> Nyalakan Semua ({{ $totalCount }})
                </button>
            </form>
            <form method="POST" action="{{ route('admin.beranda-menus.toggle') }}" class="d-inline">
                @csrf
                @method('PATCH')
                <input type="hidden" name="enabled" value="0">
                <button type="submit" class="btn btn-sm {{ !$anyManualOn ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill px-3 py-1.5 fw-semibold" {{ !$anyManualOn ? 'disabled' : '' }}>
                    <i class="bi bi-eye-slash me-1"></i> Matikan Semua
                </button>
            </form>
        </div>
    </div>

    <!-- Individual 4-Menu Control Grid -->
    <div class="row g-3">
        @foreach($menus as $menuKey => $menu)
            @php
                $isVisible = (bool) ($menu['is_visible'] ?? false);
                $isManualOn = (bool) ($menu['is_manually_enabled'] ?? false);
            @endphp
            <div class="col-12 col-lg-6">
                <div class="p-3 rounded-4 bg-white border h-100 d-flex flex-column justify-content-between" style="border-color: {{ $isVisible ? '#BBF7D0' : '#E2E8F0' }} !important;">
                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-2.5">
                        <div class="d-flex align-items-start gap-2.5">
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; background: {{ $menu['soft_bg'] }}; color: {{ $menu['color'] }};">
                                <i class="bi {{ $menu['icon'] }} fs-6"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $menu['label'] }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $menu['subtitle'] }}</div>
                                <div class="mt-1 d-flex flex-wrap align-items-center gap-1.5">
                                    <span class="badge {{ $menu['status_badge_class'] }} rounded-pill px-2 py-0.5" style="font-size: 0.66rem;">
                                        {{ $menu['status_label'] }}
                                    </span>
                                    @if(!empty($menu['start_date_formatted']) && !empty($menu['end_date_formatted']))
                                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5" style="font-size: 0.66rem;">
                                            <i class="bi bi-calendar-range me-1"></i>{{ $menu['start_date_formatted'] }} - {{ $menu['end_date_formatted'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.beranda-menus.toggle') }}" class="d-inline flex-shrink-0">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="menu_key" value="{{ $menuKey }}">
                            <input type="hidden" name="enabled" value="{{ $isManualOn ? '0' : '1' }}">
                            @if($isManualOn)
                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-semibold" title="Klik untuk mematikan tombol menu ini" style="font-size: 0.74rem;">
                                    <i class="bi bi-toggle-on me-1"></i> Menyala (Matikan)
                                </button>
                            @else
                                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold" title="Klik untuk menyalakan menu ini secara manual" style="font-size: 0.74rem;">
                                    <i class="bi bi-toggle-off me-1"></i> Mati (Nyalakan)
                                </button>
                            @endif
                        </form>
                    </div>

                    <form method="POST" action="{{ route('admin.beranda-menus.update-period') }}" class="row g-1.5 align-items-end pt-2 border-top">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="menu_key" value="{{ $menuKey }}">
                        <div class="col-5 col-sm-4">
                            <label class="form-label text-muted mb-0.5" style="font-size: 0.68rem;">Mulai Periode</label>
                            <input type="date" name="start_date" class="form-control form-control-sm bg-light" value="{{ $menu['start_date'] ?? '' }}" required>
                        </div>
                        <div class="col-5 col-sm-4">
                            <label class="form-label text-muted mb-0.5" style="font-size: 0.68rem;">Selesai Periode</label>
                            <input type="date" name="end_date" class="form-control form-control-sm bg-light" value="{{ $menu['end_date'] ?? '' }}" required>
                        </div>
                        <div class="col-12 col-sm-4 d-flex gap-1">
                            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-2.5 py-1 fw-semibold flex-grow-1" style="font-size: 0.72rem;">
                                <i class="bi bi-calendar-check me-1"></i> Simpan
                            </button>
                            @if(!empty($menu['start_date']) || !empty($menu['end_date']))
                                <button type="submit" name="clear_period" value="1" formnovalidate class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" title="Reset Periode" style="font-size: 0.72rem;">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Optional Bulk Period Bar -->
    <div class="mt-3 pt-3 border-top" style="border-color: rgba(15, 23, 42, 0.08) !important;">
        <form method="POST" action="{{ route('admin.beranda-menus.update-period') }}" class="row g-2 align-items-end">
            @csrf
            @method('PATCH')
            <div class="col-12 col-md-4">
                <div class="fw-semibold text-dark" style="font-size: 0.78rem;">
                    <i class="bi bi-calendar2-week text-primary me-1"></i> Atur Periode Sekaligus (Semua 4 Menu)
                </div>
                <div class="text-muted" style="font-size: 0.7rem;">Opsional jika ingin menyamakan jadwal buka ke-4 menu sekaligus.</div>
            </div>
            <div class="col-6 col-md-3">
                <input type="date" name="start_date" class="form-control form-control-sm bg-white" required>
            </div>
            <div class="col-6 col-md-3">
                <input type="date" name="end_date" class="form-control form-control-sm bg-white" required>
            </div>
            <div class="col-12 col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold w-100" style="font-size: 0.74rem;">
                    Terapkan Semua
                </button>
            </div>
        </form>
    </div>
</div>

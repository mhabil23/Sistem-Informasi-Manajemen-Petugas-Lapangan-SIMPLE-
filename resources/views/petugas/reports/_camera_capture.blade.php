<div class="card mb-3 border border-primary-subtle">
    <div class="card-header bg-light py-2 px-3 fw-bold text-dark small d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span>
            <i class="fa-solid fa-camera text-primary me-1"></i> DOKUMENTASI LANGSUNG KAMERA (LOKASI &amp; WAKTU REAL-TIME)
        </span>
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
            <i class="fa-solid fa-ban me-1"></i> Tanpa Upload Galeri
        </span>
    </div>
    <div class="card-body p-3">
        @if(!empty($showExisting))
            <div id="{{ $prefix }}_existing_photos_box" class="mb-3 d-none">
                <div class="small fw-semibold text-secondary mb-2">
                    <i class="fa-solid fa-images text-primary me-1"></i> Foto Kamera Tersimpan Sebelumnya:
                </div>
                <div id="{{ $prefix }}_existing_photos_list" class="row g-2"></div>
            </div>
        @endif

        <div class="alert alert-info py-2 px-3 mb-3 small border-info border-opacity-25">
            <i class="fa-solid fa-location-crosshairs text-info me-1"></i>
            Dokumentasi <strong>wajib diambil langsung dari kamera</strong>. Sistem otomatis mengunci koordinat GPS, nama lokasi, serta tanggal &amp; waktu real-time langsung ke dalam foto.
        </div>

        <!-- Bar Telemetry Real-Time -->
        <div class="p-2.5 bg-light border rounded mb-3">
            <div class="row g-2 align-items-center small">
                <div class="col-md-5">
                    <div class="text-muted" style="font-size: 0.72rem;">WAKTU REAL-TIME:</div>
                    <div id="{{ $prefix }}_live_clock_text" class="fw-bold text-dark font-monospace">Memuat jam...</div>
                </div>
                <div class="col-md-7">
                    <div class="text-muted" style="font-size: 0.72rem;">PELACAKAN LOKASI GPS REAL-TIME:</div>
                    <div class="d-flex flex-wrap align-items-center gap-1.5">
                        <span id="{{ $prefix }}_gps_badge" class="badge bg-warning text-dark">
                            <i class="fa-solid fa-spinner fa-spin me-1"></i> Mencari sinyal GPS...
                        </span>
                        <span id="{{ $prefix }}_gps_address_text" class="text-secondary text-truncate" style="max-width: 100%;"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kontrol Kamera & GPS -->
        <div class="d-flex flex-wrap gap-2 mb-2">
            <button type="button" id="{{ $prefix }}_btn_start_cam" class="btn btn-sm btn-primary" onclick="perjadinCamera.start('{{ $prefix }}')">
                <i class="fa-solid fa-video me-1"></i> Aktifkan Kamera
            </button>
            <button type="button" id="{{ $prefix }}_btn_switch_cam" class="btn btn-sm btn-outline-secondary d-none" onclick="perjadinCamera.switchFacing('{{ $prefix }}')">
                <i class="fa-solid fa-camera-rotate me-1"></i> Ganti Kamera (Depan/Belakang)
            </button>
            <button type="button" id="{{ $prefix }}_btn_refresh_gps" class="btn btn-sm btn-outline-info" onclick="perjadinCamera.refreshGps('{{ $prefix }}')">
                <i class="fa-solid fa-location-dot me-1"></i> Perbarui GPS
            </button>
        </div>

        <!-- Viewfinder Kamera Langsung -->
        <div class="position-relative bg-dark rounded overflow-hidden mb-3 text-center" style="min-height: 230px;">
            <div id="{{ $prefix }}_cam_placeholder" class="d-flex flex-column align-items-center justify-content-center text-white p-4" style="min-height: 230px;">
                <i class="fa-solid fa-camera fa-2x text-info mb-2"></i>
                <div id="{{ $prefix }}_cam_status_msg" class="small text-light mb-2">Kamera siap diaktifkan untuk mengambil dokumentasi lapangan.</div>
                <button type="button" class="btn btn-sm btn-info text-dark fw-semibold" onclick="perjadinCamera.start('{{ $prefix }}')">
                    <i class="fa-solid fa-power-off me-1"></i> Buka Kamera &amp; Lacak GPS
                </button>
            </div>

            <video id="{{ $prefix }}_cam_video" autoplay playsinline muted class="w-100 d-none" style="max-height: 360px; object-fit: cover; display: block;"></video>

            <!-- Live HUD Watermark Preview -->
            <div id="{{ $prefix }}_cam_hud" class="position-absolute bottom-0 start-0 end-0 text-start text-white px-3 py-2 d-none" style="background: rgba(0, 0, 0, 0.65); font-size: 0.74rem; line-height: 1.35;">
                <div class="fw-bold text-warning" id="{{ $prefix }}_hud_loc">Lokasi: Mendeteksi...</div>
                <div class="font-monospace" id="{{ $prefix }}_hud_coords">GPS: Menunggu koordinat...</div>
                <div class="font-monospace" id="{{ $prefix }}_hud_time">-</div>
            </div>
        </div>

        <!-- Tombol Jepret -->
        <div id="{{ $prefix }}_snap_wrapper" class="d-grid mb-3 d-none">
            <button type="button" id="{{ $prefix }}_btn_snap" class="btn btn-success fw-bold py-2" onclick="perjadinCamera.capture('{{ $prefix }}')">
                <i class="fa-solid fa-camera me-1"></i> Ambil Foto Kamera Sekarang
            </button>
        </div>

        <!-- Hasil Foto Kamera -->
        <div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="form-label small fw-semibold text-secondary mb-0">
                    Hasil Foto Kamera (Tercap Lokasi &amp; Waktu):
                </label>
                <span id="{{ $prefix }}_captured_count" class="badge bg-secondary-subtle text-dark border">0 Foto Baru</span>
            </div>
            <div id="{{ $prefix }}_captured_empty" class="border border-dashed rounded p-3 text-center text-muted small bg-light">
                Belum ada foto baru yang diambil dari kamera.
            </div>
            <div id="{{ $prefix }}_captured_grid" class="row g-2"></div>
            <div id="{{ $prefix }}_captured_inputs"></div>
        </div>
    </div>
</div>

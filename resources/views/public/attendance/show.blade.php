<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Presensi Online - {{ $training->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #F8FAFC;
            color: #1E293B;
            min-height: 100vh;
            padding-bottom: 3rem;
        }
        .main-card {
            background: #FFFFFF;
            border-radius: 24px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }
        .btn-bps-orange {
            background: #F37021;
            color: #FFFFFF;
            border: none;
            font-weight: 700;
            border-radius: 999px;
            padding: 0.75rem 1.5rem;
            box-shadow: 0 4px 14px rgba(243, 112, 33, 0.28);
            transition: all 0.2s ease;
        }
        .btn-bps-orange:hover, .btn-bps-orange:focus {
            background: #E05D0E;
            color: #FFFFFF;
            transform: translateY(-1px);
        }
        .btn-bps-orange:disabled {
            background: #CBD5E1;
            box-shadow: none;
        }
        .canvas-container {
            border: 2px dashed #CBD5E1;
            border-radius: 18px;
            background: #FFFFFF;
            position: relative;
            touch-action: none;
            cursor: crosshair;
            overflow: hidden;
        }
        .canvas-container.active {
            border-color: #3B82F6;
            background: #FFFFFF;
        }
        #signatureCanvas {
            width: 100%;
            height: 180px;
            display: block;
        }
        .canvas-hint {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #94A3B8;
            pointer-events: none;
            font-size: 0.85rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container py-4" style="max-width: 600px;">
        
        <!-- Header Branding -->
        <div class="text-center mb-3">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill bg-white border shadow-sm mb-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 24px; height: 24px; background: #F37021; font-size: 0.75rem;">
                    S
                </div>
                <span class="fw-bold text-dark" style="font-size: 0.82rem; letter-spacing: 0.05em;">SIMPEL BPS &bull; PRESENSI DIGITAL</span>
            </div>
        </div>

        <!-- Main Presensi Form Card -->
        <div class="main-card">
            <!-- Event Banner Header -->
            <div class="p-4 border-bottom" style="background: linear-gradient(135deg, #0284C7 0%, #0369A1 100%); color: #FFFFFF;">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-white text-primary rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.7rem;">
                        <i class="bi bi-file-earmark-check me-1"></i> DAFTAR HADIR RESMI
                    </span>
                    @if($training->document_number)
                        <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                            {{ $training->document_number }}
                        </span>
                    @endif
                </div>
                <h4 class="fw-bold mb-1" style="font-size: 1.25rem; line-height: 1.35;">{{ $training->title }}</h4>
                @if($training->description)
                    <p class="text-white text-opacity-75 small mb-2" style="font-size: 0.8rem;">{{ $training->description }}</p>
                @endif
                <div class="d-flex flex-wrap align-items-center gap-3 pt-2 text-white text-opacity-90 small" style="font-size: 0.78rem;">
                    <div><i class="bi bi-calendar3 me-1"></i> {{ $training->start_date ? $training->start_date->format('d M Y') : '-' }}</div>
                    <div><i class="bi bi-geo-alt me-1"></i> {{ $training->location ?: 'Kabupaten Kolaka Utara' }}</div>
                </div>
            </div>

            <!-- Live Progress Bar -->
            @php
                $hasSignatureCount = $participants->whereNotNull('signature_path')->count();
            @endphp
            <div class="px-4 py-2.5 bg-light border-bottom d-flex align-items-center justify-content-between small">
                <span class="text-secondary fw-medium" style="font-size: 0.76rem;">
                    <i class="bi bi-people-fill me-1 text-primary"></i> Presensi Peserta:
                </span>
                <span class="fw-bold text-success" style="font-size: 0.78rem;">
                    {{ $hasSignatureCount }} dari {{ $totalCount }} peserta telah bertanda tangan ({{ $totalCount > 0 ? round(($hasSignatureCount / $totalCount) * 100) : 0 }}%)
                </span>
            </div>

            <form method="POST" action="{{ route('attendance.public.submit', $training->public_token) }}" id="attendanceForm" class="p-4">
                @csrf
                <input type="hidden" name="signature_data" id="signatureDataInput" required>

                <!-- Step 1: Pilih Nama Peserta -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.9rem;">
                        1. Pilih Nama Anda <span class="text-danger">*</span>
                    </label>
                    <select name="participant_id" id="participantSelect" class="form-select form-select-lg rounded-3 border-secondary-subtle" required onchange="onParticipantChanged(this)">
                        <option value="">-- Ketuk untuk memilih nama Anda --</option>
                        @foreach($participants as $p)
                            @php
                                $hasSig = !empty($p->signature_path);
                            @endphp
                            <option value="{{ $p->id }}" 
                                    data-category="{{ $p->category ?: 'Peserta' }}"
                                    data-agency="{{ $p->agency ?: '-' }}"
                                    data-nip="{{ $p->nip_or_id ?: '-' }}"
                                    data-signed="{{ $hasSig ? '1' : '0' }}">
                                {{ $p->name }} {{ $hasSig ? '✓ [Sudah TTD]' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text text-muted" style="font-size: 0.74rem;">
                        Pilih nama Anda yang telah didaftarkan oleh panitia kegiatan.
                    </div>
                </div>

                <!-- Info Card Peserta Terpilih -->
                <div id="participantDetailCard" class="p-3 rounded-4 bg-light border mb-4" style="display: none;">
                    <div class="d-flex align-items-center gap-2.5 mb-2">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem;" id="partInitial">
                            P
                        </div>
                        <div>
                            <div class="fw-bold text-dark small" id="partNameDisplay">-</div>
                            <div class="text-muted" style="font-size: 0.72rem;" id="partNipDisplay">NIP / ID: -</div>
                        </div>
                    </div>
                    <div class="row g-2 pt-2 border-top small" style="font-size: 0.74rem;">
                        <div class="col-6">
                            <span class="text-muted">Jabatan/Peran:</span>
                            <div class="fw-semibold text-dark text-truncate" id="partCategoryDisplay">-</div>
                        </div>
                        <div class="col-6">
                            <span class="text-muted">Instansi/Asal:</span>
                            <div class="fw-semibold text-dark text-truncate" id="partAgencyDisplay">-</div>
                        </div>
                    </div>
                    <div id="partSignedAlert" class="alert alert-info py-2 px-3 mb-0 mt-2.5 small rounded-3 border-0 bg-info-subtle text-info-emphasis" style="display: none;">
                        <i class="bi bi-info-circle-fill me-1 text-info"></i>
                        <span>Peserta ini sudah pernah bertanda tangan. Mengisi tanda tangan di bawah akan memperbarui tanda tangan sebelumnya.</span>
                    </div>
                </div>

                <!-- Step 2: Kanvas Tanda Tangan Digital Sentuh Jari -->
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                        <label class="form-label fw-bold text-dark mb-0" style="font-size: 0.9rem;">
                            2. Tanda Tangan Digital <span class="text-danger">*</span>
                        </label>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-0.5" onclick="clearSignature()" style="font-size: 0.72rem;">
                            <i class="bi bi-arrow-counterclockwise"></i> Ulangi / Hapus
                        </button>
                    </div>

                    <div class="canvas-container" id="canvasContainer">
                        <canvas id="signatureCanvas"></canvas>
                        <div class="canvas-hint" id="canvasHint">
                            <i class="bi bi-pencil-fill d-block mb-1 fs-5 text-secondary"></i>
                            Goreskan tanda tangan Anda di sini menggunakan jari
                        </div>
                    </div>
                    <div class="form-text text-muted" style="font-size: 0.72rem;">
                        Gunakan jari atau stylus langsung pada layar sentuh HP Anda.
                    </div>
                </div>

                <!-- Pernyataan Kehadiran -->
                <div class="form-check p-3 rounded-3 bg-light border mb-4">
                    <input class="form-check-input ms-0 me-2" type="checkbox" id="confirmCheck" required>
                    <label class="form-check-label small text-secondary" for="confirmCheck" style="font-size: 0.78rem;">
                        Saya menyatakan hadir secara langsung dan mengikuti kegiatan ini dengan data yang sebenar-benarnya.
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="btnSubmit" class="btn btn-bps-orange w-100 py-2.5 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <span>Kirim Presensi &amp; Tanda Tangan</span>
                </button>
            </form>
        </div>

        <div class="text-center text-muted mt-4 small" style="font-size: 0.74rem;">
            &copy; {{ date('Y') }} SIMPEL BPS Kabupaten Kolaka Utara &bull; Seluruh hak cipta dilindungi.
        </div>
    </div>

    <script>
        const canvas = document.getElementById('signatureCanvas');
        const ctx = canvas.getContext('2d');
        const container = document.getElementById('canvasContainer');
        const hint = document.getElementById('canvasHint');
        let isDrawing = false;
        let hasDrawn = false;

        function resizeCanvas() {
            const rect = container.getBoundingClientRect();
            canvas.width = rect.width * 2;
            canvas.height = 180 * 2;
            ctx.scale(2, 2);
            ctx.lineWidth = 2.5;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.strokeStyle = '#0F172A';
        }

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return {
                x: clientX - rect.left,
                y: clientY - rect.top
            };
        }

        function startDrawing(e) {
            e.preventDefault();
            isDrawing = true;
            const pos = getPos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
            hint.style.display = 'none';
            container.classList.add('active');
        }

        function draw(e) {
            if (!isDrawing) return;
            e.preventDefault();
            const pos = getPos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            hasDrawn = true;
        }

        function stopDrawing(e) {
            if (!isDrawing) return;
            isDrawing = false;
        }

        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        window.addEventListener('mouseup', stopDrawing);

        canvas.addEventListener('touchstart', startDrawing, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        window.addEventListener('touchend', stopDrawing);

        function clearSignature() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            hasDrawn = false;
            hint.style.display = 'block';
            container.classList.remove('active');
            document.getElementById('signatureDataInput').value = '';
        }

        function onParticipantChanged(select) {
            const card = document.getElementById('participantDetailCard');
            const alertBox = document.getElementById('partSignedAlert');
            const opt = select.options[select.selectedIndex];
            if (!select.value) {
                card.style.display = 'none';
                return;
            }
            card.style.display = 'block';
            document.getElementById('partNameDisplay').textContent = opt.textContent.replace('✓ [Sudah TTD]', '').trim();
            document.getElementById('partInitial').textContent = opt.textContent.trim().substring(0, 1).toUpperCase();
            document.getElementById('partNipDisplay').textContent = 'NIP / ID: ' + (opt.dataset.nip || '-');
            document.getElementById('partCategoryDisplay').textContent = opt.dataset.category || 'Peserta';
            document.getElementById('partAgencyDisplay').textContent = opt.dataset.agency || '-';

            if (opt.dataset.signed === '1') {
                alertBox.style.display = 'block';
            } else {
                alertBox.style.display = 'none';
            }
        }

        document.getElementById('attendanceForm').addEventListener('submit', function(e) {
            if (!hasDrawn) {
                e.preventDefault();
                alert('Silakan bubuhkan tanda tangan Anda terlebih dahulu pada kotak kanvas.');
                return false;
            }
            document.getElementById('signatureDataInput').value = canvas.toDataURL('image/png');
            document.getElementById('btnSubmit').disabled = true;
            document.getElementById('btnSubmit').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan Presensi...';
        });
    </script>
</body>
</html>

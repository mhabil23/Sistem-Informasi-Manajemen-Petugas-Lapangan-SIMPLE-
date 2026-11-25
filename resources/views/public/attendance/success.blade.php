<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Presensi Berhasil - {{ $training->title }}</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .success-card {
            background: #FFFFFF;
            border-radius: 28px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
            width: 100%;
            max-width: 480px;
            overflow: hidden;
            text-align: center;
        }
        .success-icon-box {
            width: 84px;
            height: 84px;
            border-radius: 50%;
            background: #DCFCE7;
            color: #16A34A;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.75rem;
            margin: 0 auto 1.5rem auto;
            box-shadow: 0 8px 24px rgba(22, 163, 74, 0.2);
        }
    </style>
</head>
<body>
    <div class="success-card p-4 p-md-5">
        <div class="success-icon-box">
            <i class="bi bi-check-lg"></i>
        </div>

        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold mb-2" style="font-size: 0.76rem;">
            <i class="bi bi-patch-check-fill me-1"></i> PRESENSI BERHASIL TERCATAT
        </span>

        <h4 class="fw-bold text-dark mb-1" style="font-size: 1.35rem;">
            Terima Kasih{{ $participant ? ', ' . $participant->name : '' }}!
        </h4>
        <p class="text-secondary small mb-4" style="font-size: 0.84rem;">
            Tanda tangan digital dan kehadiran Anda pada kegiatan <strong>{{ $training->title }}</strong> telah resmi tersimpan ke sistem daftar hadir BPS.
        </p>

        @if($participant)
            <div class="p-3 rounded-4 bg-light border text-start mb-4 small" style="font-size: 0.78rem;">
                <div class="d-flex justify-content-between mb-1 pb-1 border-bottom">
                    <span class="text-muted">Nama Peserta:</span>
                    <span class="fw-bold text-dark">{{ $participant->name }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1 pb-1 border-bottom">
                    <span class="text-muted">Waktu TTD:</span>
                    <span class="fw-semibold text-dark">{{ $participant->signed_at ? $participant->signed_at->format('d/m/Y H:i') . ' WITA' : now()->format('d/m/Y H:i') . ' WITA' }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Status Kehadiran:</span>
                    <span class="badge bg-success text-white rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">Hadir Sah</span>
                </div>
            </div>
        @endif

        <div class="alert alert-secondary py-3 px-3 text-start small mb-4 rounded-4 border-0 bg-light text-secondary">
            <div class="d-flex align-items-center gap-2 mb-1.5 fw-bold text-dark" style="font-size: 0.82rem;">
                <i class="bi bi-shield-lock-fill text-success fs-6"></i>
                <span>Formulir Terkunci (Hanya 1x Pengisian)</span>
            </div>
            <div style="font-size: 0.76rem; line-height: 1.45;">
                Perangkat ini telah menyelesaikan presensi resmi. Demi menjaga integritas data kehadiran, setiap peserta/perangkat hanya dapat melakukan presensi <strong>satu kali</strong> dan formulir telah ditutup agar tidak dapat digunakan untuk mengabsenkan orang lain.
            </div>
        </div>

        <div class="d-flex flex-column gap-2">
            <button type="button" class="btn btn-secondary rounded-pill py-2.5 small fw-bold" onclick="alert('Presensi Anda telah tersimpan secara resmi. Anda dapat menutup tab / browser ini.');">
                <i class="bi bi-check2-circle me-1"></i> Presensi Selesai (Tutup Halaman)
            </button>
        </div>
    </div>

    <script>
        @if($participant)
            try {
                localStorage.setItem('signed_dh_{{ $training->public_token }}', '{{ $participant->id }}');
            } catch(e) {}
        @endif
    </script>
</body>
</html>

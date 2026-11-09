<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $bast->bast_number }} - BAST Responden SIMPLE</title>
    <style>
        @page {
            margin: 1.2cm 1.5cm 1.2cm 1.5cm;
            size: A4 portrait;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #000;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px double #000;
            padding-bottom: 4px;
            margin-bottom: 10px;
        }

        .header-title {
            text-align: center;
        }

        .instansi-utama {
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .instansi-sub {
            font-size: 13.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0d2142;
        }

        .instansi-detail {
            font-size: 8.5pt;
            font-style: italic;
            color: #333;
        }

        .doc-title {
            text-align: center;
            margin-top: 8px;
            margin-bottom: 10px;
        }

        .doc-title h3 {
            font-size: 11.5pt;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }

        .doc-title p {
            margin: 2px 0 0;
            font-size: 9.5pt;
        }

        .content-table {
            width: 100%;
            margin-bottom: 6px;
        }

        .content-table td {
            vertical-align: top;
            padding: 1.5px 0;
        }

        .label-col {
            width: 25%;
        }

        .sep-col {
            width: 3%;
        }

        .val-col {
            width: 72%;
        }

        .items-box {
            background-color: #fdfdfd;
            border: 1px solid #777;
            padding: 6px 12px;
            margin: 8px 0;
            font-size: 9.5pt;
            white-space: pre-line;
        }

        .signature-table {
            width: 100%;
            margin-top: 15px;
            page-break-inside: avoid;
        }

        .signature-col {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .sign-space {
            height: 70px;
            text-align: center;
            margin: 3px 0;
        }

        .signer-name {
            font-weight: bold;
            text-decoration: underline;
        }

        .signer-title {
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <!-- KOP SURAT RESMI -->
    <table class="header-table">
        <tr>
            <td class="header-title">
                <div class="instansi-utama">PEMERINTAH REPUBLIK INDONESIA</div>
                <div class="instansi-sub">SISTEM INFORMASI MANAJEMEN PETUGAS LAPANGAN (SIMPLE)</div>
                <div class="instansi-detail">Sistem Administrasi Terpadu, Dokumen BAST & Manajemen Pelaporan Lapangan</div>
            </td>
        </tr>
    </table>

    <!-- JUDUL DOKUMEN -->
    <div class="doc-title">
        <h3>BERITA ACARA SERAH TERIMA DOKUMEN RESPONDEN</h3>
        <p>Nomor: <strong>{{ $bast->bast_number }}</strong></p>
    </div>

    <p style="text-align: justify;">
        Pada hari ini, <strong>{{ $bast->bast_date->translatedFormat('l') }}</strong> tanggal <strong>{{ $bast->bast_date->translatedFormat('d F Y') }}</strong>, bertempat di <strong>{{ $bast->respondent->address ?? 'Lokasi Responden' }}</strong>, kami yang bertanda tangan di bawah ini:
    </p>

    <!-- PIHAK PERTAMA -->
    <table class="content-table">
        <tr>
            <td class="label-col"><strong>Nama Petugas</strong></td>
            <td class="sep-col">:</td>
            <td class="val-col"><strong>{{ $bast->officer->name }}</strong></td>
        </tr>
        <tr>
            <td class="label-col">Username / ID</td>
            <td class="sep-col">:</td>
            <td class="val-col">{{ $bast->officer->username }}</td>
        </tr>
        <tr>
            <td class="label-col">Nomor Kontak / HP</td>
            <td class="sep-col">:</td>
            <td class="val-col">{{ $bast->officer->phone ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Jabatan / Peran</td>
            <td class="sep-col">:</td>
            <td class="val-col">Petugas Lapangan SIMPLE</td>
        </tr>
    </table>
    <p style="margin: 0 0 10px 0; font-size: 10pt; font-style: italic;">
        Selanjutnya disebut sebagai <strong>PIHAK PERTAMA (Yang Menyerahkan / Memverifikasi)</strong>.
    </p>

    <!-- PIHAK KEDUA -->
    <table class="content-table">
        @if($bast->recipient_type === 'representative' && !empty($bast->recipient_name))
            <tr>
                <td class="label-col"><strong>Nama Penerima</strong></td>
                <td class="sep-col">:</td>
                <td class="val-col"><strong>{{ $bast->recipient_name }}</strong></td>
            </tr>
            <tr>
                <td class="label-col">Status / Hubungan</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $bast->recipient_relation ?? 'Wakil / Anggota Keluarga' }} (Mewakili Kepala Rumah Tangga)</td>
            </tr>
            <tr>
                <td class="label-col">Kepala Rumah Tangga</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $bast->respondent->name }}</td>
            </tr>
        @else
            <tr>
                <td class="label-col"><strong>Nama Responden</strong></td>
                <td class="sep-col">:</td>
                <td class="val-col"><strong>{{ $bast->respondent->name }}</strong> (Kepala Rumah Tangga Langsung)</td>
            </tr>
        @endif
        <tr>
            <td class="label-col">Instansi / Lembaga</td>
            <td class="sep-col">:</td>
            <td class="val-col">{{ $bast->respondent->agency }}</td>
        </tr>
        <tr>
            <td class="label-col">Alamat Lengkap</td>
            <td class="sep-col">:</td>
            <td class="val-col">{{ $bast->respondent->address }}</td>
        </tr>
        <tr>
            <td class="label-col">Nomor Telepon / HP</td>
            <td class="sep-col">:</td>
            <td class="val-col">{{ $bast->respondent->phone ?? '-' }}</td>
        </tr>
    </table>
    <p style="margin: 0 0 10px 0; font-size: 10pt; font-style: italic;">
        Selanjutnya disebut sebagai <strong>PIHAK KEDUA (Yang Menerima / Menyerahkan Berkas)</strong>.
    </p>

    <!-- ISI SERAH TERIMA -->
    <p style="text-align: justify; margin-top: 15px;">
        Bahwa PIHAK PERTAMA dan PIHAK KEDUA telah melaksanakan serah terima kelengkapan berkas administrasi dan dokumen kegiatan <strong>{{ $bast->assignment ? $bast->assignment->title : 'Pendataan Lapangan Terpadu' }}</strong> dengan rincian sebagai berikut:
    </p>

    <div class="items-box">
{{ $bast->handover_items }}
    </div>

    @if($bast->notes)
        <p style="font-size: 10pt; margin: 8px 0;">
            <strong>Catatan Tambahan:</strong> {{ $bast->notes }}
        </p>
    @endif

    <p style="text-align: justify;">
        Demikian Berita Acara Serah Terima ini dibuat dengan sebenarnya dalam keadaan sadar dan tanpa paksaan dari pihak manapun untuk dipergunakan sebagaimana mestinya.
    </p>

    <!-- TANDA TANGAN -->
    @php
        $getTrimmedSignatureBase64 = function($path) {
            if (empty($path)) return '';
            
            $p = storage_path('app/public/' . $path);
            if (!file_exists($p)) $p = storage_path('app/' . $path);
            if (!file_exists($p)) $p = public_path('storage/' . $path);
            if (!file_exists($p)) return '';

            // Auto-crop area transparan canvas agar goresan tanda tangan tampak besar & proporsional
            if (function_exists('imagecreatefrompng') && function_exists('imagecrop')) {
                $im = @imagecreatefrompng($p);
                if ($im) {
                    $w = imagesx($im);
                    $h = imagesy($im);
                    $minX = $w; $minY = $h; $maxX = 0; $maxY = 0;
                    $found = false;

                    for ($x = 0; $x < $w; $x++) {
                        for ($y = 0; $y < $h; $y++) {
                            $rgba = imagecolorat($im, $x, $y);
                            $alpha = ($rgba & 0x7F000000) >> 24;
                            $r = ($rgba >> 16) & 0xFF;
                            $g = ($rgba >> 8) & 0xFF;
                            $b = $rgba & 0xFF;

                            if ($alpha < 120 && ($r < 240 || $g < 240 || $b < 240)) {
                                $found = true;
                                if ($x < $minX) $minX = $x;
                                if ($x > $maxX) $maxX = $x;
                                if ($y < $minY) $minY = $y;
                                if ($y > $maxY) $maxY = $y;
                            }
                        }
                    }

                    if ($found) {
                        $pad = 10;
                        $cropX = max(0, $minX - $pad);
                        $cropY = max(0, $minY - $pad);
                        $cropW = min($w - $cropX, ($maxX - $minX + 1) + ($pad * 2));
                        $cropH = min($h - $cropY, ($maxY - $minY + 1) + ($pad * 2));

                        $cropped = @imagecrop($im, ['x' => $cropX, 'y' => $cropY, 'width' => $cropW, 'height' => $cropH]);
                        if ($cropped) {
                            imagesavealpha($cropped, true);
                            ob_start();
                            imagepng($cropped);
                            $data = ob_get_clean();
                            imagedestroy($cropped);
                            imagedestroy($im);
                            if (!empty($data)) {
                                return base64_encode($data);
                            }
                        }
                    }
                    imagedestroy($im);
                }
            }

            return base64_encode(file_get_contents($p));
        };

        $recipientSignData = $getTrimmedSignatureBase64($bast->signature_path);
        $officerSignData = $getTrimmedSignatureBase64($bast->officer_signature_path);
    @endphp
    <table class="signature-table">
        <tr>
            <td class="signature-col">
                <div>PIHAK KEDUA,</div>
                <div style="font-size: 9.5pt;">
                    @if($bast->recipient_type === 'representative')
                        Wakil Responden / Penerima
                    @else
                        Kepala Rumah Tangga / Responden
                    @endif
                </div>
                <div class="sign-space">
                    @if($recipientSignData)
                        <img src="data:image/png;base64,{{ $recipientSignData }}" style="height: 60px; max-height: 65px; max-width: 190px; width: auto; display: inline-block; vertical-align: middle;" alt="Tanda Tangan Penerima">
                    @endif
                </div>
                <div class="signer-name">{{ $bast->recipient_type === 'representative' ? $bast->recipient_name : $bast->respondent->name }}</div>
                <div class="signer-title">
                    @if($bast->recipient_type === 'representative')
                        Status: {{ $bast->recipient_relation ?? 'Wakil' }}
                    @else
                        {{ $bast->respondent->agency }}
                    @endif
                </div>
            </td>
            <td class="signature-col">
                <div>PIHAK PERTAMA,</div>
                <div style="font-size: 9.5pt;">Petugas Lapangan SIMPLE</div>
                <div class="sign-space">
                    @if($officerSignData)
                        <img src="data:image/png;base64,{{ $officerSignData }}" style="height: 60px; max-height: 65px; max-width: 190px; width: auto; display: inline-block; vertical-align: middle;" alt="Tanda Tangan Petugas">
                    @endif
                </div>
                <div class="signer-name">{{ $bast->officer->name }}</div>
                <div class="signer-title">NIP/ID: {{ $bast->officer->username }}</div>
            </td>
        </tr>
    </table>
</body>
</html>


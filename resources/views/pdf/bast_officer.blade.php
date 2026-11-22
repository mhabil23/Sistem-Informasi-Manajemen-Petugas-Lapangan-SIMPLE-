<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $bast->bast_number }} - BAST Hasil Pekerjaan Petugas Lapangan</title>
    <style>
        @page {
            margin: 1.8cm 2cm 1.8cm 2.2cm;
            size: A4 portrait;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10.5pt;
            line-height: 1.45;
            color: #000;
        }

        .header-title-terisi {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            line-height: 1.35;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .header-nomor-terisi {
            text-align: center;
            font-size: 10.5pt;
            margin-bottom: 16px;
        }

        .party-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
            font-size: 10.5pt;
        }

        .party-table td {
            vertical-align: top;
            padding: 1px 0;
            line-height: 1.35;
        }

        .ol-pernyataan {
            margin: 0;
            padding-left: 20px;
            text-align: justify;
            line-height: 1.4;
        }

        .ol-pernyataan li {
            margin-bottom: 7px;
        }

        .signature-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }

        .signature-col {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .sign-space {
            height: 65px;
            margin: 4px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .signer-name {
            font-weight: bold;
        }

        /* Generic header fallback for kantor_ke_petugas if used */
        .header-table-legacy {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
@if($bast->bast_type === 'petugas_ke_kantor')
    @php
        $yearStr = $bast->bast_date ? $bast->bast_date->format('Y') : now()->format('Y');
        $assignmentTitle = $bast->assignment ? $bast->assignment->title : 'Survei / Sensus Lapangan';
        $workName = $bast->activity_work_name
            ?: ($bast->activity_subtitle ?: "Pendataan Lapangan {$assignmentTitle} Tahun {$yearStr} pada Badan Pusat Statistik Kabupaten Kolaka Utara");
        $institutionName = $bast->institution_name ?: 'Badan Pusat Statistik Kabupaten Kolaka Utara';
        $firstPartyName = $bast->maker_name ?: 'Hidayatullah, SE';
        $firstPartyNip = $bast->maker_nip ?: '198510292011011009';
        $firstPartyPosition = $bast->first_party_position ?: 'Pejabat Pembuat Komitmen Badan Pusat Statistik Kabupaten Kolaka Utara';
        $firstPartyUnit = $bast->institution_name ?: 'BPS Kabupaten Kolaka Utara';
        $firstPartyAddress = $bast->first_party_address ?: 'Jalan Protokol Poros DPRD, Kec. Lasusua, Kolaka Utara Sulawesi Tenggara';

        $secondPartyName = $bast->officer ? $bast->officer->name : 'Petugas Lapangan';
        $secondPartyNik = $bast->second_party_nik ?: ($bast->officer->nik ?? '7408011212840002');
        $secondPartyAddress = $bast->second_party_address ?: ($bast->officer->address ?? 'Desa Watuliu, Kecamatan Lasusua');

        $contractNumber = $bast->contract_number ?: 'PPK 7408/06/10/2026/001';
        $contractDateStr = $bast->contract_date ? $bast->contract_date->translatedFormat('j F Y') : '6 Oktober 2026';
        $docCount = $bast->doc_count ?: '30 Dokumen';

        // Helper Auto Crop tanda tangan
        $getTrimmedSignBase64 = function (?string $path): ?string {
            if (empty($path)) return null;
            $p = storage_path('app/public/' . $path);
            if (!file_exists($p)) $p = public_path('storage/' . $path);
            if (!file_exists($p)) return null;

            if (function_exists('imagecreatefrompng') && function_exists('imagecrop')) {
                $im = @imagecreatefrompng($p);
                if ($im) {
                    $w = imagesx($im); $h = imagesy($im);
                    $minX = $w; $minY = $h; $maxX = 0; $maxY = 0;
                    for ($x = 0; $x < $w; $x++) {
                        for ($y = 0; $y < $h; $y++) {
                            $rgb = imagecolorat($im, $x, $y);
                            $alpha = ($rgb >> 24) & 0x7F;
                            if ($alpha < 125) {
                                if ($x < $minX) $minX = $x;
                                if ($x > $maxX) $maxX = $x;
                                if ($y < $minY) $minY = $y;
                                if ($y > $maxY) $maxY = $y;
                            }
                        }
                    }
                    if ($maxX >= $minX && $maxY >= $minY) {
                        $pad = 6;
                        $minX = max(0, $minX - $pad);
                        $minY = max(0, $minY - $pad);
                        $maxX = min($w - 1, $maxX + $pad);
                        $maxY = min($h - 1, $maxY + $pad);
                        $cropped = imagecrop($im, ['x' => $minX, 'y' => $minY, 'width' => $maxX - $minX + 1, 'height' => $maxY - $minY + 1]);
                        if ($cropped) {
                            ob_start();
                            imagepng($cropped);
                            $data = ob_get_clean();
                            imagedestroy($cropped);
                            imagedestroy($im);
                            return 'data:image/png;base64,' . base64_encode($data);
                        }
                    }
                    imagedestroy($im);
                }
            }
            return 'data:image/png;base64,' . base64_encode(file_get_contents($p));
        };

        $secondPartySignBase64 = ($bast->officer && $bast->officer->signature_path) ? $getTrimmedSignBase64($bast->officer->signature_path) : null;
        $firstPartySignBase64 = $bast->maker_signature_path ? $getTrimmedSignBase64($bast->maker_signature_path) : null;
    @endphp

    <!-- HEADER RESMI BAST TERISI -->
    <div class="header-title-terisi">
        BERITA ACARA SERAH TERIMA<br>
        HASIL PEKERJAAN PETUGAS PENDATAAN LAPANGAN<br>
        {{ strtoupper($workName) }}<br>
        PADA {{ strtoupper($institutionName) }}
    </div>
    <div class="header-nomor-terisi">
        Nomor: {{ $bast->bast_number }}
    </div>

    <!-- PEMBUKA TANGGAL & TEMPAT -->
    <p style="text-align: justify; margin: 0 0 10px 0;">
        {{ $bast->spelled_date_phrase }}
    </p>

    <!-- 1. PIHAK PERTAMA -->
    <table class="party-table">
        <tr>
            <td style="width: 20px;">1.</td>
            <td style="width: 140px;">Nama</td>
            <td style="width: 15px;">:</td>
            <td>{{ $firstPartyName }}</td>
        </tr>
        <tr>
            <td></td>
            <td>NIP</td>
            <td>:</td>
            <td>{{ $firstPartyNip }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Jabatan</td>
            <td>:</td>
            <td>{{ $firstPartyPosition }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Unit Kerja</td>
            <td>:</td>
            <td>{{ $firstPartyUnit }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Alamat Unit Kerja</td>
            <td>:</td>
            <td>{{ $firstPartyAddress }}</td>
        </tr>
    </table>
    <div style="margin: 3px 0 10px 20px; text-align: justify;">
        bertindak untuk dan atas nama {{ $firstPartyUnit }}, selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.
    </div>

    <!-- 2. PIHAK KEDUA -->
    <table class="party-table">
        <tr>
            <td style="width: 20px;">2.</td>
            <td style="width: 140px;">Nama</td>
            <td style="width: 15px;">:</td>
            <td>{{ $secondPartyName }}</td>
        </tr>
        <tr>
            <td></td>
            <td>NIK</td>
            <td>:</td>
            <td>{{ $secondPartyNik }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Alamat</td>
            <td>:</td>
            <td>{{ $secondPartyAddress }}, yang selanjutnya disebut sebagai PIHAK KEDUA.</td>
        </tr>
    </table>
    <div style="margin: 3px 0 12px 20px; text-align: justify;">
        bertindak untuk dan atas namanya sendiri, selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.
    </div>

    <!-- PERNYATAAN (5 POIN) -->
    <p style="margin: 0 0 6px 0;">Dengan ini menyatakan:</p>
    <ol class="ol-pernyataan">
        <li>
            <strong>PIHAK KEDUA</strong> telah melaksanakan pekerjaan {{ $workName }} berdasarkan Perjanjian Kerja Petugas Pendataan Lapangan {{ $workName }} Nomor {{ $contractNumber }}, tanggal {{ $contractDateStr }}.
        </li>
        <li>
            <strong>PIHAK KEDUA</strong> telah menyelesaikan pekerjaan {{ $workName }} berdasarkan hasil pemeriksaan dan evaluasi pekerjaan sebagaimana tercantum dalam lampiran.
        </li>
        <li>
            Berdasarkan angka 2 tersebut di atas, <strong>PIHAK KEDUA</strong> menyerahkan hasil pekerjaan {{ $workName }} kepada <strong>PIHAK PERTAMA</strong>, dan <strong>PIHAK PERTAMA</strong> menerima hasil pekerjaan tersebut yang telah sesuai dengan seharusnya.
        </li>
        <li>
            Hasil pekerjaan {{ $workName }} sebagaimana dimaksud dalam angka 3 di atas, berupa data hasil {{ $workName }}, sejumlah {{ $docCount }}.
        </li>
        <li>
            Untuk hasil pendataan lapangan sebagaimana dimaksud pada angka 4 yang memerlukan pemeriksaan lanjutan, akan dilakukan pengecekan, perubahan, dan/atau kunjungan kembali ke lapangan merujuk pada perjanjian yang ditandatangani oleh PARA PIHAK.
        </li>
    </ol>

    <!-- PENUTUP -->
    <p style="text-align: justify; margin: 12px 0 18px 0;">
        Demikian Berita Acara ini dibuat dengan sebenarnya dalam rangkap 2 (dua) dan menjadi sah berlaku setelah ditandatangani oleh PARA PIHAK.
    </p>

    <!-- TANDA TANGAN PARA PIHAK -->
    <table class="signature-table">
        <tr>
            <td class="signature-col">
                <div>PIHAK KEDUA,</div>
                <div class="sign-space">
                    @if($secondPartySignBase64)
                        <img src="{{ $secondPartySignBase64 }}" style="max-height: 60px; max-width: 170px;">
                    @else
                        <div style="height: 55px;"></div>
                    @endif
                </div>
                <div class="signer-name">{{ $secondPartyName }}</div>
            </td>
            <td class="signature-col">
                <div>PIHAK PERTAMA,</div>
                <div class="sign-space">
                    @if($firstPartySignBase64)
                        <img src="{{ $firstPartySignBase64 }}" style="max-height: 60px; max-width: 170px;">
                    @else
                        <div style="height: 55px;"></div>
                    @endif
                </div>
                <div class="signer-name">{{ $firstPartyName }}</div>
                @if($firstPartyNip)
                    <div style="font-size: 10pt;">NIP. {{ $firstPartyNip }}</div>
                @endif
            </td>
        </tr>
    </table>

@else
    <!-- TAMPILAN LEGACY UNTUK KANTOR KE PETUGAS (JIKA BUKAN DOKUMEN TERISI) -->
    <div style="text-align: center; margin-bottom: 20px;">
        <h3 style="margin: 0; text-transform: uppercase;">BERITA ACARA SERAH TERIMA BLANKO &amp; PERLENGKAPAN</h3>
        <p style="margin: 4px 0 0;">Nomor: <strong>{{ $bast->bast_number }}</strong></p>
    </div>
    <p style="text-align: justify;">
        Pada hari ini, <strong>{{ $bast->bast_date->translatedFormat('l') }}</strong> tanggal <strong>{{ $bast->bast_date->translatedFormat('d F Y') }}</strong>, bertempat di Kantor BPS, telah diserahterimakan perlengkapan tugas lapangan.
    </p>
    <div style="background-color: #fdfdfd; border: 1px solid #777; padding: 10px; margin: 12px 0; white-space: pre-line;">
        {{ $bast->description }}
    </div>
    <table class="signature-table">
        <tr>
            <td class="signature-col">
                <div>PIHAK KEDUA (Penerima),</div>
                <div class="sign-space"></div>
                <div class="signer-name">{{ $bast->officer->name ?? 'Petugas Lapangan' }}</div>
            </td>
            <td class="signature-col">
                <div>PIHAK PERTAMA (Penyerah),</div>
                <div class="sign-space"></div>
                <div class="signer-name">{{ $bast->maker_name ?: 'Koordinator Logistik BPS' }}</div>
            </td>
        </tr>
    </table>
@endif
</body>
</html>

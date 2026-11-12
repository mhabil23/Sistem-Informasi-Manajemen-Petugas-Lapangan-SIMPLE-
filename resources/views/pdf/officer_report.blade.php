<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Perjadin &amp; Daftar Pengeluaran Riil - {{ $report->resolved_officer_name }}</title>
    <style>
        @page {
            margin: 1.5cm 2cm 1.8cm 2cm;
            size: A4 portrait;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10.5pt;
            line-height: 1.45;
            color: #000;
        }

        /* Kop Surat BPS Kolaka Utara */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }

        .kop-logo {
            width: 82px;
            vertical-align: middle;
            text-align: left;
        }

        .kop-text {
            vertical-align: middle;
            text-align: left;
            padding-left: 6px;
        }

        .kop-instansi {
            font-size: 13pt;
            font-weight: bold;
            font-style: italic;
            color: #2b3990;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin: 0;
            line-height: 1.2;
        }

        .kop-kabupaten {
            font-size: 13pt;
            font-weight: bold;
            font-style: italic;
            color: #2b3990;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin: 0 0 3px 0;
            line-height: 1.2;
        }

        .kop-alamat {
            font-size: 8pt;
            color: #222;
            line-height: 1.3;
            margin: 0;
        }

        .kop-line {
            border: 0;
            border-top: 1.5px solid #000;
            margin-top: 6px;
            margin-bottom: 16px;
        }

        .doc-title {
            text-align: center;
            margin-bottom: 18px;
        }

        .doc-title h3 {
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
        }

        .section-title {
            font-weight: normal;
            margin-top: 10px;
            margin-bottom: 4px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-left: 18px;
            margin-bottom: 8px;
        }

        .info-table td {
            vertical-align: top;
            padding: 2px 0;
            font-size: 10.5pt;
        }

        .label-col {
            width: 165px;
        }

        .sep-col {
            width: 16px;
            text-align: center;
        }

        .val-col {
            width: auto;
        }

        .list-dasar {
            margin: 2px 0 10px 18px;
            padding-left: 18px;
        }

        .list-dasar li {
            margin-bottom: 4px;
            text-align: justify;
        }

        .section-desc {
            margin-left: 18px;
            margin-bottom: 8px;
            text-align: justify;
        }

        .sub-heading {
            font-weight: normal;
            margin-top: 6px;
            margin-bottom: 2px;
        }

        .signature-section {
            width: 100%;
            margin-top: 22px;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            vertical-align: top;
        }

        .sign-box {
            height: 62px;
            margin: 4px 0;
            text-align: center;
        }

        .signer-name-underline {
            font-weight: bold;
            text-decoration: underline;
        }

        .page-break {
            page-break-before: always;
        }

        /* Dokumentasi Page */
        .photo-single {
            text-align: center;
            margin-top: 12px;
        }

        .photo-single img {
            max-width: 94%;
            max-height: 520px;
            border: 1px solid #ccc;
            padding: 3px;
        }

        .photo-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .photo-cell {
            width: 50%;
            text-align: center;
            padding: 8px;
            vertical-align: top;
        }

        .photo-img {
            max-width: 96%;
            max-height: 240px;
            border: 1px solid #ccc;
            padding: 3px;
        }

        /* Daftar Pengeluaran Riil */
        .dpr-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin-top: 6px;
            margin-bottom: 20px;
        }

        .dpr-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0 14px 0;
        }

        .dpr-table th,
        .dpr-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 10pt;
            vertical-align: middle;
        }

        .dpr-table th {
            font-weight: normal;
            text-align: center;
        }

        .dpr-num-row td {
            text-align: center;
            font-size: 9.5pt;
            padding: 2px 6px;
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('images/logo.png');
        $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';

        // Helper auto-crop area transparan/putih di sekitar goresan TTD agar tampil jelas & besar di kedua dokumen
        $getTrimmedSignBase64 = function ($path) {
            if (empty($path)) return '';
            $p = storage_path('app/public/' . $path);
            if (!file_exists($p)) $p = storage_path('app/' . $path);
            if (!file_exists($p)) $p = public_path('storage/' . $path);
            if (!file_exists($p)) return '';

            if (function_exists('imagecreatefrompng') && function_exists('imagecrop')) {
                $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                $im = $ext === 'png' ? @imagecreatefrompng($p) : @imagecreatefromjpeg($p);
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

        $signData = $getTrimmedSignBase64($report->signature_path);
        $officerName = $report->resolved_officer_name;
        $officerNik = $report->resolved_officer_nik;
        $idLabel = $report->id_type ?: 'NIK';
        $actDateObj = $report->activity_date ? \Carbon\Carbon::parse($report->activity_date) : now();
        $signDateObj = $report->signing_date ? \Carbon\Carbon::parse($report->signing_date) : $actDateObj;
        $signDateStr = $signDateObj->translatedFormat('d F Y');
        $actYear = $actDateObj->format('Y');

        $imageAttachments = $report->attachments ? $report->attachments->filter(fn ($a) => str_starts_with($a->file_type, 'image/')) : collect();
        $otherAttachments = $report->attachments ? $report->attachments->reject(fn ($a) => str_starts_with($a->file_type, 'image/')) : collect();
    @endphp

    <!-- =========================================================================
         DOKUMEN 1: LAPORAN PERJALANAN DINAS (+ DOKUMENTASI BERKOP BPS)
         ========================================================================= -->
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                @if($logoData)
                    <img src="data:image/png;base64,{{ $logoData }}" style="width: 72px; height: auto;" alt="Logo BPS">
                @endif
            </td>
            <td class="kop-text">
                <div class="kop-instansi">BADAN PUSAT STATISTIK</div>
                <div class="kop-kabupaten">KABUPATEN KOLAKA UTARA</div>
                <div class="kop-alamat">
                    Jalan Protokol Poros DPRD Lasusua 93911 Telp. (0405) 2330029; Fax. (0405) 2330029,<br>
                    Homepage: <span style="color: #1d4ed8; text-decoration: underline;">http://kolutkab.bps.go.id</span> E-mail: <span style="color: #1d4ed8; text-decoration: underline;">bps7408@bps.go.id</span>
                </div>
            </td>
        </tr>
    </table>
    <hr class="kop-line">

    <div class="doc-title">
        <h3>Laporan Perjalanan Dinas</h3>
    </div>

    <div class="section-title">A. Yang Melakukan Perjalanan Dinas</div>
    <table class="info-table">
        <tr>
            <td class="label-col">Nama</td>
            <td class="sep-col">:</td>
            <td class="val-col">{{ $officerName }}</td>
        </tr>
        <tr>
            <td class="label-col">{{ $idLabel }}</td>
            <td class="sep-col">:</td>
            <td class="val-col">{{ $officerNik }}</td>
        </tr>
        <tr>
            <td class="label-col">Pangkat/Golongan</td>
            <td class="sep-col">:</td>
            <td class="val-col">{{ $report->officer_rank ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Jabatan</td>
            <td class="sep-col">:</td>
            <td class="val-col">{{ $report->officer_position ?: 'Mitra BPS Kabupaten Kolaka Utara' }}</td>
        </tr>
    </table>

    <div class="section-title">B. Dasar Pelaksanaan</div>
    <ol class="list-dasar">
        <li>
            {{ $report->basis_dipa ?: ("DIPA Satuan Kerja Badan Pusat Statistik Kabupaten Kolaka Utara Tahun Anggaran {$actYear} Nomor SP DIPA-054.01.2.667477/{$actYear} tanggal 1 Desember " . ($actYear - 1) . ".") }}
        </li>
        <li>
            @if(!empty($report->basis_st))
                {{ $report->basis_st }}
            @else
                Surat Tugas Kepala Badan Pusat Statistik Kabupaten Kolaka Utara Nomor {{ $report->st_number ?: "B-1812/74080/HM.300/{$actYear}" }} Tanggal {{ $report->st_date ? $report->st_date->translatedFormat('d F Y') : $actDateObj->translatedFormat('d F Y') }}.
            @endif
        </li>
    </ol>

    <div class="section-title">C. Tujuan Perjalanan Dinas</div>
    <div class="section-desc">
        {{ $report->destination_purpose ?: ("Perjalanan Dinas Biasa dalam kota sampai dengan 8 jam dalam rangka {$report->activity_title} di {$report->display_location}.") }}
    </div>

    <div class="section-title">D. Waktu Perjalanan Dinas</div>
    <div class="section-desc">
        {{ $report->duration_text ?: 'Selama 1 (satu) Hari' }}, pada tanggal {{ $actDateObj->translatedFormat('d F Y') }}
    </div>

    <div class="section-title">E. Hasil perjalanan dinas</div>
    <div class="section-desc">
        <div style="font-weight: bold; margin-bottom: 4px;">Tanggal {{ $actDateObj->translatedFormat('d F Y') }}</div>
        <div style="white-space: pre-line;">{{ $report->work_description }}</div>

        @if(!empty($report->obstacles))
            <div class="sub-heading">Kendala :</div>
            <div style="white-space: pre-line;">{{ $report->obstacles }}</div>
        @endif

        @if(!empty($report->solutions))
            <div class="sub-heading">Solusi :</div>
            <div style="white-space: pre-line;">{{ $report->solutions }}</div>
        @endif
    </div>

    <div class="section-title">F. Saran dan Rekomendasi /Tindak Lanjut</div>
    <div class="section-desc" style="white-space: pre-line;">{{ ($report->recommendations && $report->recommendations !== '-') ? $report->recommendations : '-' }}</div>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td style="width: 52%;"></td>
                <td style="width: 48%; text-align: center;">
                    <div>{{ $report->signing_city ?: 'Lasusua' }}, {{ $signDateStr }}</div>
                    <div>Pelaksana Perjalanan Dinas,</div>
                    <div class="sign-box">
                        @if($signData)
                            <img src="data:image/png;base64,{{ $signData }}" style="height: 56px; max-height: 60px; max-width: 185px; width: auto; display: inline-block; vertical-align: middle;" alt="Tanda Tangan Pelaksana">
                        @endif
                    </div>
                    <div class="signer-name-underline">{{ $officerName }}</div>
                    @if($officerNik && $officerNik !== '-')
                        <div style="font-size: 9.5pt;">{{ $idLabel }}. {{ $officerNik }}</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- HALAMAN DOKUMENTASI KEGIATAN (SESUAI HALAMAN 3 PDF CONTOH) -->
    @if($report->attachments && $report->attachments->isNotEmpty())
        <div class="page-break"></div>
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    @if($logoData)
                        <img src="data:image/png;base64,{{ $logoData }}" style="width: 72px; height: auto;" alt="Logo BPS">
                    @endif
                </td>
                <td class="kop-text">
                    <div class="kop-instansi">BADAN PUSAT STATISTIK</div>
                    <div class="kop-kabupaten">KABUPATEN KOLAKA UTARA</div>
                    <div class="kop-alamat">
                        Jalan Protokol Poros DPRD Lasusua 93911 Telp. (0405) 2330029; Fax. (0405) 2330029,<br>
                        Homepage: <span style="color: #1d4ed8; text-decoration: underline;">http://kolutkab.bps.go.id</span> E-mail: <span style="color: #1d4ed8; text-decoration: underline;">bps7408@bps.go.id</span>
                    </div>
                </td>
            </tr>
        </table>
        <hr class="kop-line">

        <div style="margin-bottom: 10px; font-size: 10.5pt;">Dokumentasi :</div>

        @if($imageAttachments->count() === 1)
            @php
                $att = $imageAttachments->first();
                $attPath = storage_path('app/public/' . $att->file_path);
                $attData = file_exists($attPath) ? base64_encode(file_get_contents($attPath)) : '';
            @endphp
            @if($attData)
                <div class="photo-single">
                    <img src="data:{{ $att->file_type }};base64,{{ $attData }}" alt="{{ $att->file_name }}">
                    @if($att->caption || ($att->latitude && $att->longitude) || $att->location_address)
                        <div style="font-size: 8.5pt; color: #333; margin-top: 6px; line-height: 1.35;">
                            @if($att->caption)
                                <div>{{ $att->caption }}</div>
                            @elseif($att->location_address || ($att->latitude && $att->longitude))
                                <div>
                                    {{ $att->location_address }}
                                    @if($att->latitude && $att->longitude)
                                        ({{ number_format($att->latitude, 6, '.', '') }}, {{ number_format($att->longitude, 6, '.', '') }})
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        @elseif($imageAttachments->count() > 1)
            <table class="photo-grid">
                @foreach($imageAttachments->chunk(2) as $row)
                    <tr>
                        @foreach($row as $att)
                            <td class="photo-cell">
                                @php
                                    $attPath = storage_path('app/public/' . $att->file_path);
                                    $attData = file_exists($attPath) ? base64_encode(file_get_contents($attPath)) : '';
                                @endphp
                                @if($attData)
                                    <img src="data:{{ $att->file_type }};base64,{{ $attData }}" class="photo-img" alt="{{ $att->file_name }}">
                                    <div style="font-size: 8.5pt; margin-top: 4px; color: #444;">{{ $att->caption ?: $att->file_name }}</div>
                                @endif
                            </td>
                        @endforeach
                        @if($row->count() < 2)
                            <td class="photo-cell"></td>
                        @endif
                    </tr>
                @endforeach
            </table>
        @endif

        @if($otherAttachments->isNotEmpty())
            <div style="margin-top: 12px; font-size: 9.5pt;">
                <strong>Lampiran Berkas Lainnya:</strong>
                <ul style="margin: 4px 0; padding-left: 18px;">
                    @foreach($otherAttachments as $att)
                        <li>{{ $att->file_name }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif

    <!-- =========================================================================
         DOKUMEN 2: DAFTAR PENGELUARAN RIIL (DIGABUNG MENJADI 1 FILE PDF)
         ========================================================================= -->
    <div class="page-break"></div>

    <div class="dpr-title">Daftar Pengeluaran Riil</div>

    <div style="margin-bottom: 10px;">Yang bertanda tangan dibawah ini &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</div>

    <table class="info-table" style="margin-left: 0; margin-bottom: 12px;">
        <tr>
            <td style="width: 170px;">Nama</td>
            <td class="sep-col">:</td>
            <td>{{ $officerName }}</td>
        </tr>
        <tr>
            <td>{{ $idLabel }}</td>
            <td class="sep-col">:</td>
            <td>{{ $officerNik }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td class="sep-col">:</td>
            <td>{{ $report->officer_position ?: 'Mitra BPS Kabupaten Kolaka Utara' }}</td>
        </tr>
    </table>

    <div style="text-align: justify; margin-bottom: 12px;">
        berdasarkan {{ $report->resolved_st_text }}, dengan ini kami menyatakan dengan sesungguhnya bahwa :
    </div>

    <div style="margin-bottom: 4px;">
        1. &nbsp; Biaya transport pegawai di bawah ini yang tidak dapat diperoleh bukti-bukti pengeluarannya, meliputi:
    </div>

    <table class="dpr-table">
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 67%;">Perincian Biaya</th>
                <th style="width: 25%;">Jumlah (Rp)</th>
            </tr>
            <tr class="dpr-num-row">
                <td>(1)</td>
                <td>(2)</td>
                <td>(3)</td>
            </tr>
        </thead>
        <tbody>
            @foreach($report->resolved_expense_items as $idx => $item)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td>{{ $item['description'] }}</td>
                    <td style="text-align: center;">Rp. {{ number_format($item['amount'], 0, ',', '.') }},-</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" style="text-align: center; font-weight: bold;">JUMLAH</td>
                <td rowspan="2" style="text-align: center; vertical-align: middle; font-weight: bold;">
                    Rp. {{ number_format($report->total_expense, 0, ',', '.') }},-
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; font-weight: bold; font-style: italic;">
                    ({{ $report->total_expense_terbilang }})
                </td>
            </tr>
        </tbody>
    </table>

    <div style="text-align: justify; margin-bottom: 12px;">
        2. &nbsp; Jumlah uang tersebut pada angka 1 diatas benar-benar dikeluarkan untuk pelaksanaan perjalanan dinas dimaksud dan apabila di kemudian hari terdapat kelebihan atas pembayaran, kami bersedia untuk menyetorkan kelebihan tersebut ke Kas Negara.
    </div>

    <div style="text-align: justify; margin-bottom: 24px;">
        Demikian pernyataan ini kami buat dengan sebenarnya, untuk dipergunakan sebagaimana mestinya.
    </div>

    <div class="signature-section" style="margin-top: 20px;">
        <table class="signature-table">
            <tr>
                <td style="width: 50%; text-align: center;">
                    <div>Mengetahui/Menyetujui:</div>
                    <div>Pejabat Pembuat Komitmen</div>
                    <div>BPS Kabupaten Kolaka Utara</div>
                    <div class="sign-box"></div>
                    <div class="signer-name-underline">{{ $report->ppk_name ?: 'Hidayatullah, SE' }}</div>
                    <div>NIP. {{ $report->ppk_nip ?: '198510292011011009' }}</div>
                </td>
                <td style="width: 50%; text-align: center;">
                    <div>{{ $report->signing_city ?: 'Lasusua' }}, {{ $signDateStr }}</div>
                    <div>Pegawai</div>
                    <div>Yang melakukan perjalanan dinas,</div>
                    <div class="sign-box">
                        @if($signData)
                            <img src="data:image/png;base64,{{ $signData }}" style="height: 56px; max-height: 60px; max-width: 185px; width: auto; display: inline-block; vertical-align: middle;" alt="Tanda Tangan Pegawai">
                        @endif
                    </div>
                    <div class="signer-name-underline">{{ $officerName }}</div>
                    @if($officerNik && $officerNik !== '-')
                        <div>{{ $idLabel }}. {{ $officerNik }}</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- =========================================================================
         OPSIONAL: SURAT PERNYATAAN TIDAK MENGGUNAKAN KENDARAAN DINAS
         ========================================================================= -->
    @if($report->include_vehicle_statement)
        <div class="page-break"></div>
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    @if($logoData)
                        <img src="data:image/png;base64,{{ $logoData }}" style="width: 72px; height: auto;" alt="Logo BPS">
                    @endif
                </td>
                <td class="kop-text">
                    <div class="kop-instansi">BADAN PUSAT STATISTIK</div>
                    <div class="kop-kabupaten">KABUPATEN KOLAKA UTARA</div>
                    <div class="kop-alamat">
                        Jalan Protokol Poros DPRD Lasusua 93911 Telp. (0405) 2330029; Fax. (0405) 2330029,<br>
                        Homepage: <span style="color: #1d4ed8; text-decoration: underline;">http://kolutkab.bps.go.id</span> E-mail: <span style="color: #1d4ed8; text-decoration: underline;">bps7408@bps.go.id</span>
                    </div>
                </td>
            </tr>
        </table>
        <hr class="kop-line">

        <div class="doc-title" style="margin-bottom: 18px;">
            <h3 style="font-size: 11.5pt;">SURAT PERNYATAAN</h3>
            <h3 style="font-size: 11pt; text-decoration: underline;">TIDAK MENGGUNAKAN KENDARAAN DINAS</h3>
        </div>

        <div style="margin-bottom: 10px;">Yang bertanda tangan di bawah ini:</div>
        <table class="info-table" style="margin-bottom: 15px;">
            <tr>
                <td class="label-col">Nama</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $officerName }}</td>
            </tr>
            <tr>
                <td class="label-col">{{ $idLabel }}</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $officerNik }}</td>
            </tr>
            <tr>
                <td class="label-col">Pangkat/Golongan</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $report->officer_rank ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">Jabatan</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $report->officer_position ?: 'Mitra BPS Kabupaten Kolaka Utara' }}</td>
            </tr>
            <tr>
                <td class="label-col">Unit Kerja</td>
                <td class="sep-col">:</td>
                <td class="val-col">BPS Kabupaten Kolaka Utara</td>
            </tr>
        </table>

        <p style="text-align: justify; line-height: 1.5; margin-bottom: 12px;">
            Menerangkan bahwa dalam rangka {{ $report->activity_title }} di {{ $report->display_location }} sesuai {{ $report->resolved_st_text }}. Saya benar-benar tidak menggunakan kendaraan dinas pada tanggal {{ $actDateObj->translatedFormat('d F Y') }}.
        </p>

        <p style="text-align: justify; line-height: 1.5; margin-bottom: 25px;">
            Demikian pernyataan ini kami buat dengan sebenar-benarnya untuk dipergunakan sebagaimana mestinya. Apabila terdapat kekeliruan dalam pertanggungjawaban SPD dan mengakibatkan kerugian negara, kami bersedia dituntut sesuai peraturan yang berlaku dan mengembalikan biaya transpor yang sudah terlanjur saya terima ke kas negara.
        </p>

        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td style="width: 50%;"></td>
                    <td style="width: 50%; text-align: center;">
                        <div>{{ $report->signing_city ?: 'Lasusua' }}, {{ $signDateStr }}</div>
                        <div>Pelaksana Perjalanan Dinas,</div>
                        <div class="sign-box">
                            @if($signData)
                                <img src="data:image/png;base64,{{ $signData }}" style="height: 56px; max-height: 60px; max-width: 185px; width: auto; display: inline-block; vertical-align: middle;" alt="Tanda Tangan Pelaksana">
                            @endif
                        </div>
                        <div class="signer-name-underline">{{ $officerName }}</div>
                        @if($officerNik && $officerNik !== '-')
                            <div style="font-size: 9.5pt;">{{ $idLabel }}. {{ $officerNik }}</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    @endif
</body>
</html>

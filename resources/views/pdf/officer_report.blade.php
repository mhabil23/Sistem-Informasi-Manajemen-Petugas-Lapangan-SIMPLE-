<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Perjalanan Dinas - {{ $report->officer_name ?: $report->user->name }}</title>
    <style>
        @page {
            margin: 1.5cm 2cm 2cm 2cm;
            size: A4 portrait;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10.5pt;
            line-height: 1.45;
            color: #000;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }

        .kop-logo {
            width: 85px;
            vertical-align: middle;
            text-align: left;
        }

        .kop-text {
            vertical-align: middle;
            text-align: center;
        }

        .kop-instansi {
            font-size: 13pt;
            font-weight: bold;
            color: #2b3990;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            line-height: 1.2;
        }

        .kop-kabupaten {
            font-size: 13pt;
            font-weight: bold;
            color: #333333;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 4px 0;
            line-height: 1.2;
        }

        .kop-alamat {
            font-size: 7.8pt;
            color: #333333;
            line-height: 1.35;
            margin: 0;
        }

        .kop-line {
            border: 0;
            border-top: 1.5px solid #000;
            margin-top: 6px;
            margin-bottom: 18px;
        }

        .doc-title {
            text-align: center;
            margin-bottom: 22px;
        }

        .doc-title h3 {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
        }

        .section-title {
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 4px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-left: 20px;
            margin-bottom: 8px;
        }

        .info-table td {
            vertical-align: top;
            padding: 2px 0;
            font-size: 10.5pt;
        }

        .label-col {
            width: 190px;
        }

        .sep-col {
            width: 18px;
            text-align: center;
        }

        .val-col {
            width: auto;
        }

        .list-dasar {
            margin: 2px 0 10px 20px;
            padding-left: 18px;
        }

        .list-dasar li {
            margin-bottom: 4px;
            text-align: justify;
        }

        .section-desc {
            margin-left: 20px;
            margin-bottom: 8px;
            text-align: justify;
        }

        .signature-section {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            vertical-align: top;
        }

        .sign-space {
            height: 65px;
        }

        .signer-name {
            font-weight: bold;
            text-decoration: none;
        }

        .attachment-page {
            page-break-before: always;
            margin-top: 20px;
        }

        .attachment-title {
            font-weight: bold;
            text-align: center;
            font-size: 11pt;
            text-decoration: underline;
            margin-bottom: 15px;
        }

        .photo-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .photo-cell {
            width: 50%;
            text-align: center;
            padding: 8px;
            vertical-align: top;
        }

        .photo-img {
            max-width: 95%;
            max-height: 180px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 3px;
        }
    </style>
</head>
<body>
    <!-- KOP SURAT BPS RESMI -->
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                @php
                    $logoPath = public_path('images/logo.png');
                    $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
                @endphp
                @if($logoData)
                    <img src="data:image/png;base64,{{ $logoData }}" style="width: 75px; height: auto;" alt="Logo BPS">
                @endif
            </td>
            <td class="kop-text">
                <div class="kop-instansi">BADAN PUSAT STATISTIK</div>
                <div class="kop-kabupaten">KABUPATEN KOLAKA UTARA</div>
                <div class="kop-alamat">
                    Jalan Protokol Poros DPRD Lasusua 93911 Telp. (0405) 2330029; Fax. (0405) 2330029,<br>
                    Homepage: http://kolutkab.bps.go.id E-mail: bps7408@bps.go.id
                </div>
            </td>
        </tr>
    </table>
    <hr class="kop-line">

    <!-- JUDUL LAPORAN -->
    <div class="doc-title">
        <h3>Laporan Perjalanan Dinas</h3>
    </div>

    <!-- A. YANG MELAKUKAN PERJALANAN DINAS -->
    <div class="section-title">A. Yang Melakukan Perjalanan Dinas</div>
    <table class="info-table">
        <tr>
            <td class="label-col">Nama</td>
            <td class="sep-col">:</td>
            <td class="val-col">{{ $report->officer_name ?: $report->user->name }}</td>
        </tr>
        <tr>
            <td class="label-col">{{ $report->id_type ?: 'NIK' }}</td>
            <td class="sep-col">:</td>
            <td class="val-col">{{ $report->officer_nip ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Pangkat/Golongan</td>
            <td class="sep-col">:</td>
            <td class="val-col">{{ $report->officer_rank ?: '- / -' }}</td>
        </tr>
        <tr>
            <td class="label-col">Jabatan</td>
            <td class="sep-col">:</td>
            <td class="val-col">{{ $report->officer_position ?: 'Mitra BPS Kabupaten Kolaka Utara' }}</td>
        </tr>
    </table>

    <!-- B. DASAR PELAKSANAAN -->
    <div class="section-title">B. Dasar Pelaksanaan</div>
    <ol class="list-dasar">
        <li>
            {{ $report->basis_dipa ?: ('DIPA Satuan Kerja Badan Pusat Statistik Kabupaten Kolaka Utara Tahun Anggaran ' . ($report->activity_date ? $report->activity_date->format('Y') : date('Y')) . ' Nomor SP DIPA-054.01.2.667477/' . ($report->activity_date ? $report->activity_date->format('Y') : date('Y')) . ' tanggal 2 Desember ' . (($report->activity_date ? $report->activity_date->format('Y') : date('Y')) - 1) . '.') }}
        </li>
        <li>
            {{ $report->basis_st ?: ('Surat Tugas Kepala Badan Pusat Statistik Kabupaten Kolaka Utara Nomor: B-205/74080/VS.330/' . ($report->activity_date ? $report->activity_date->format('Y') : date('Y')) . ' Tanggal ' . ($report->activity_date ? $report->activity_date->translatedFormat('d F Y') : date('d F Y')) . '.') }}
        </li>
    </ol>

    <!-- C. TUJUAN PERJALANAN DINAS -->
    <div class="section-title">C. Tujuan Perjalanan Dinas</div>
    <div class="section-desc">
        {{ $report->destination_purpose ?: ('Perjalanan Dinas Biasa dalam kota lebih dari 8 jam dalam rangka pendataan ' . $report->activity_title . ' di ' . $report->display_location . '.') }}
    </div>

    <!-- D. WAKTU PERJALANAN DINAS -->
    <div class="section-title">D. Waktu Perjalanan Dinas</div>
    <div class="section-desc">
        {{ $report->duration_text ?: 'Selama 1 (satu) Hari' }}, pada tanggal {{ $report->activity_date ? $report->activity_date->translatedFormat('d F Y') : '-' }}
    </div>

    <!-- E. HASIL PELAKSANAAN KEGIATAN -->
    @if($report->work_description)
        <div class="section-title">E. Hasil Perjalanan Dinas</div>
        <div class="section-desc" style="white-space: pre-line;">{{ $report->work_description }}</div>
    @endif

    <!-- F. SARAN DAN REKOMENDASI / TINDAK LANJUT -->
    <div class="section-title">F. Saran dan Rekomendasi / Tindak Lanjut</div>
    <div class="section-desc" style="white-space: pre-line;">{{ $report->recommendations ?: '-' }}</div>

    <!-- TANDA TANGAN -->
    @php
        $signPath = $report->signature_path ? storage_path('app/public/' . $report->signature_path) : null;
        $signData = ($signPath && file_exists($signPath)) ? base64_encode(file_get_contents($signPath)) : '';
    @endphp
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%; text-align: center;">
                    <div>{{ $report->signing_city ?: 'Lasusua' }}, {{ $report->signing_date ? $report->signing_date->translatedFormat('d F Y') : ($report->activity_date ? $report->activity_date->translatedFormat('d F Y') : date('d F Y')) }}</div>
                    <div>Pelaksana Perjalanan Dinas,</div>
                    @if($signData)
                        <div style="height: 65px; margin: 3px 0; text-align: center;">
                            <img src="data:image/png;base64,{{ $signData }}" style="max-height: 60px; max-width: 170px;" alt="Tanda Tangan">
                        </div>
                    @else
                        <div class="sign-space"></div>
                    @endif
                    <div class="signer-name">{{ $report->officer_name ?: $report->user->name }}</div>
                    @if($report->officer_nip && $report->officer_nip !== '-')
                        <div style="font-size: 9.5pt;">{{ $report->id_type ?: 'NIK' }}. {{ $report->officer_nip }}</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- LAMPIRAN FOTO DOKUMENTASI (JIKA ADA) -->
    @if($report->attachments && $report->attachments->isNotEmpty())
        <div class="attachment-page">
            <div class="attachment-title">DOKUMENTASI KEGIATAN PERJALANAN DINAS</div>
            <div style="text-align: center; font-size: 9.5pt; margin-top: -10px; margin-bottom: 15px; color: #333;">
                Tanggal {{ $report->activity_date ? $report->activity_date->translatedFormat('d F Y') : '' }} di {{ $report->display_location }}
            </div>
            <table class="photo-grid">
                @foreach($report->attachments->chunk(2) as $row)
                    <tr>
                        @foreach($row as $att)
                            <td class="photo-cell">
                                @php
                                    $attPath = storage_path('app/public/' . $att->file_path);
                                    $attData = (file_exists($attPath) && str_starts_with($att->file_type, 'image/')) ? base64_encode(file_get_contents($attPath)) : '';
                                @endphp
                                @if($attData)
                                    <img src="data:{{ $att->file_type }};base64,{{ $attData }}" class="photo-img" alt="{{ $att->file_name }}">
                                    <div style="font-size: 8.5pt; margin-top: 4px; color: #555;">{{ $att->caption ?: $att->file_name }}</div>
                                @else
                                    <div style="border: 1px dashed #999; padding: 20px; font-size: 9pt; color: #666;">
                                        Dokumen Lampiran: {{ $att->file_name }}
                                    </div>
                                @endif
                            </td>
                        @endforeach
                        @if($row->count() < 2)
                            <td class="photo-cell"></td>
                        @endif
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    <!-- SURAT PERNYATAAN TIDAK MENGGUNAKAN KENDARAAN DINAS (JIKA DIAKTIFKAN) -->
    @if($report->include_vehicle_statement)
        <div class="attachment-page">
            <!-- KOP SURAT BPS RESMI -->
            <table class="kop-table">
                <tr>
                    <td class="kop-logo">
                        @if($logoData)
                            <img src="data:image/png;base64,{{ $logoData }}" style="width: 75px; height: auto;" alt="Logo BPS">
                        @endif
                    </td>
                    <td class="kop-text">
                        <div class="kop-instansi">BADAN PUSAT STATISTIK</div>
                        <div class="kop-kabupaten">KABUPATEN KOLAKA UTARA</div>
                        <div class="kop-alamat">
                            Jalan Protokol Poros DPRD Lasusua 93911 Telp. (0405) 2330029; Fax. (0405) 2330029,<br>
                            Homepage: http://kolutkab.bps.go.id E-mail: bps7408@bps.go.id
                        </div>
                    </td>
                </tr>
            </table>
            <hr class="kop-line">

            <!-- JUDUL SURAT PERNYATAAN -->
            <div class="doc-title" style="margin-bottom: 18px;">
                <h3 style="font-size: 11.5pt; text-decoration: none;">SURAT PERNYATAAN</h3>
                <h3 style="font-size: 11pt; text-decoration: underline;">TIDAK MENGGUNAKAN KENDARAAN DINAS</h3>
            </div>

            <div style="margin-bottom: 10px;">Yang bertanda tangan di bawah ini:</div>
            <table class="info-table" style="margin-bottom: 15px;">
                <tr>
                    <td class="label-col">Nama</td>
                    <td class="sep-col">:</td>
                    <td class="val-col">{{ $report->officer_name ?: $report->user->name }}</td>
                </tr>
                <tr>
                    <td class="label-col">NIP/NIK</td>
                    <td class="sep-col">:</td>
                    <td class="val-col">{{ $report->officer_nip ?: '-' }}</td>
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
                Menerangkan bahwa dalam rangka {{ $report->activity_title }} di {{ $report->display_location }} sesuai Surat Perjalanan Dinas {{ $report->basis_st ?: ('Nomor: B-969/74080/VS.330/' . ($report->activity_date ? $report->activity_date->format('Y') : date('Y')) . ' Tanggal ' . ($report->activity_date ? $report->activity_date->translatedFormat('d F Y') : date('d F Y'))) }}. Saya benar-benar tidak menggunakan kendaraan dinas pada tanggal {{ $report->activity_date ? $report->activity_date->translatedFormat('d F Y') : date('d F Y') }}.
            </p>

            <p style="text-align: justify; line-height: 1.5; margin-bottom: 25px;">
                Demikian pernyataan ini kami buat dengan sebenar-benarnya untuk dipergunakan sebagaimana mestinya. Apabila terdapat kekeliruan dalam pertanggungjawaban SPD dan mengakibatkan kerugian negara, kami bersedia dituntut sesuai peraturan yang berlaku dan mengembalikan biaya transpor yang sudah terlanjur saya terima ke kas negara.
            </p>

            <div class="signature-section">
                <table class="signature-table">
                    <tr>
                        <td style="width: 50%;"></td>
                        <td style="width: 50%; text-align: center;">
                            <div>{{ $report->signing_city ?: 'Lasusua' }}, {{ $report->signing_date ? $report->signing_date->translatedFormat('d F Y') : ($report->activity_date ? $report->activity_date->translatedFormat('d F Y') : date('d F Y')) }}</div>
                            <div>Pelaksana Perjalanan Dinas,</div>
                            @if($signData)
                                <div style="height: 65px; margin: 3px 0; text-align: center;">
                                    <img src="data:image/png;base64,{{ $signData }}" style="max-height: 60px; max-width: 170px;" alt="Tanda Tangan">
                                </div>
                            @else
                                <div class="sign-space"></div>
                            @endif
                            <div class="signer-name">{{ $report->officer_name ?: $report->user->name }}</div>
                            @if($report->officer_nip && $report->officer_nip !== '-')
                                <div style="font-size: 9.5pt;">{{ $report->id_type ?: 'NIK' }}. {{ $report->officer_nip }}</div>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @endif
</body>
</html>


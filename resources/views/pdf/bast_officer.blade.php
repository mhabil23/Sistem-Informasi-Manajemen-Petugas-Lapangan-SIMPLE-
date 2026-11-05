<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $bast->bast_number }} - BAST Petugas Lapangan SIMPLE</title>
    <style>
        @page {
            margin: 2cm 2cm 2cm 2cm;
            size: A4 portrait;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
        }

        .header-table {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }

        .header-title {
            text-align: center;
        }

        .instansi-utama {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .instansi-sub {
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0d2142;
        }

        .instansi-detail {
            font-size: 9pt;
            font-style: italic;
            color: #333;
        }

        .doc-title {
            text-align: center;
            margin-top: 15px;
            margin-bottom: 20px;
        }

        .doc-title h3 {
            font-size: 13pt;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }

        .doc-title p {
            margin: 3px 0 0;
            font-size: 10.5pt;
        }

        .content-table {
            width: 100%;
            margin-bottom: 12px;
        }

        .content-table td {
            vertical-align: top;
            padding: 3px 0;
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
            padding: 10px 14px;
            margin: 12px 0;
            font-size: 10.5pt;
            white-space: pre-line;
        }

        .signature-table {
            width: 100%;
            margin-top: 35px;
        }

        .signature-col {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .sign-space {
            height: 70px;
        }

        .signer-name {
            font-weight: bold;
            text-decoration: underline;
        }

        .signer-title {
            font-size: 9.5pt;
        }

        .doc-footer {
            margin-top: 35px;
            border-top: 1px dashed #aaa;
            padding-top: 6px;
            font-size: 8pt;
            color: #666;
            text-align: center;
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
                <div class="instansi-detail">Berita Acara Penyerahan Berkas Penugasan Resmi Petugas Lapangan</div>
            </td>
        </tr>
    </table>

    <!-- JUDUL DOKUMEN -->
    <div class="doc-title">
        <h3>BERITA ACARA SERAH TERIMA PENUGASAN PETUGAS</h3>
        <p>Nomor: <strong>{{ $bast->bast_number }}</strong></p>
    </div>

    <p style="text-align: justify;">
        Pada hari ini, <strong>{{ $bast->bast_date->translatedFormat('l') }}</strong> tanggal <strong>{{ $bast->bast_date->translatedFormat('d F Y') }}</strong>, bertempat di Kantor Wilayah SIMPLE, telah dilaksanakan serah terima hasil penugasan lapangan antara:
    </p>

    <!-- PIHAK PERTAMA -->
    <table class="content-table">
        <tr>
            <td class="label-col"><strong>Nama Petugas</strong></td>
            <td class="sep-col">:</td>
            <td class="val-col"><strong>{{ $bast->officer->name }}</strong></td>
        </tr>
        <tr>
            <td class="label-col">Username / NIP</td>
            <td class="sep-col">:</td>
            <td class="val-col">{{ $bast->officer->username }}</td>
        </tr>
        <tr>
            <td class="label-col">Nomor Telepon</td>
            <td class="sep-col">:</td>
            <td class="val-col">{{ $bast->officer->phone ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Jabatan</td>
            <td class="sep-col">:</td>
            <td class="val-col">Petugas Lapangan Terpilih</td>
        </tr>
    </table>
    <p style="margin: 0 0 10px 0; font-size: 10pt; font-style: italic;">
        Selanjutnya disebut sebagai <strong>PIHAK PERTAMA (Petugas Lapangan)</strong>.
    </p>

    <!-- PIHAK KEDUA -->
    <table class="content-table">
        <tr>
            <td class="label-col"><strong>Koordinator / Admin</strong></td>
            <td class="sep-col">:</td>
            <td class="val-col"><strong>Koordinator Administrasi SIMPLE</strong></td>
        </tr>
        <tr>
            <td class="label-col">Kegiatan Terkait</td>
            <td class="sep-col">:</td>
            <td class="val-col"><strong>{{ $bast->assignment->title }}</strong></td>
        </tr>
        <tr>
            <td class="label-col">Lokasi Pelaksanaan</td>
            <td class="sep-col">:</td>
            <td class="val-col">{{ $bast->assignment->location }}</td>
        </tr>
    </table>
    <p style="margin: 0 0 10px 0; font-size: 10pt; font-style: italic;">
        Selanjutnya disebut sebagai <strong>PIHAK KEDUA (Penerima Berkas / Koordinator)</strong>.
    </p>

    <p style="text-align: justify; margin-top: 15px;">
        Dengan ini menyatakan bahwa PIHAK PERTAMA telah menyelesaikan kewajiban tugas lapangan dan menyerahkan bundel berkas hasil pendataan kegiatan kepada PIHAK KEDUA dengan uraian keterangan sebagai berikut:
    </p>

    <div class="items-box">
{{ $bast->description }}
    </div>

    @if($bast->notes)
        <p style="font-size: 10pt; margin: 8px 0;">
            <strong>Catatan Pengawas:</strong> {{ $bast->notes }}
        </p>
    @endif

    <p style="text-align: justify;">
        Demikian Berita Acara ini dibuat rangkap 2 (dua) yang masing-masing mempunyai kekuatan hukum yang sama untuk digunakan sebagai bukti penyelesaian kewajiban penugasan administrasi.
    </p>

    <!-- TANDA TANGAN -->
    <table class="signature-table">
        <tr>
            <td class="signature-col">
                <div>PIHAK PERTAMA,</div>
                <div style="font-size: 9.5pt;">Petugas Lapangan</div>
                <div class="sign-space"></div>
                <div class="signer-name">{{ $bast->officer->name }}</div>
                <div class="signer-title">ID: {{ $bast->officer->username }}</div>
            </td>
            <td class="signature-col">
                <div>PIHAK KEDUA,</div>
                <div style="font-size: 9.5pt;">Koordinator Pengawas Lapangan</div>
                <div class="sign-space"></div>
                <div class="signer-name">Koordinator Wilayah</div>
                <div class="signer-title">Portal Administrasi SIMPLE</div>
            </td>
        </tr>
    </table>

    <div class="doc-footer">
        Diterbitkan dan diverifikasi secara digital oleh Sistem Informasi SIMPLE pada {{ now()->translatedFormat('d F Y H:i') }} WIB.
        <br>Nomor Seri BAST: {{ $bast->bast_number }}
    </div>
</body>
</html>


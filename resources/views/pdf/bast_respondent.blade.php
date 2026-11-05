<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $bast->bast_number }} - BAST Responden SIMPLE</title>
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
            letter-spacing: 0.5px;
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
        <tr>
            <td class="label-col"><strong>Nama Responden</strong></td>
            <td class="sep-col">:</td>
            <td class="val-col"><strong>{{ $bast->respondent->name }}</strong></td>
        </tr>
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
    <table class="signature-table">
        <tr>
            <td class="signature-col">
                <div>PIHAK KEDUA,</div>
                <div style="font-size: 9.5pt;">Responden / Penanggung Jawab</div>
                <div class="sign-space"></div>
                <div class="signer-name">{{ $bast->respondent->name }}</div>
                <div class="signer-title">{{ $bast->respondent->agency }}</div>
            </td>
            <td class="signature-col">
                <div>PIHAK PERTAMA,</div>
                <div style="font-size: 9.5pt;">Petugas Lapangan SIMPLE</div>
                <div class="sign-space"></div>
                <div class="signer-name">{{ $bast->officer->name }}</div>
                <div class="signer-title">NIP/ID: {{ $bast->officer->username }}</div>
            </td>
        </tr>
    </table>

    <div class="doc-footer">
        Dokumen ini diterbitkan secara resmi melalui Sistem Informasi Manajemen SIMPLE pada {{ now()->translatedFormat('d F Y H:i') }} WIB.
        <br>Nomor Arsip Digital: {{ $bast->bast_number }}
    </div>
</body>
</html>


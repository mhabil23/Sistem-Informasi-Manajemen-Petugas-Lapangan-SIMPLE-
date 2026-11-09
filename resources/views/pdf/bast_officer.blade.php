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
        @if($bast->bast_type === 'kantor_ke_petugas')
            <h3>BERITA ACARA SERAH TERIMA BLANKO DOKUMEN DAN PERLENGKAPAN</h3>
            <p style="font-size: 9.5pt; font-weight: bold; color: #444; margin-top: 2px;">(DISTRIBUSI LOGISTIK SENSUS / SURVEI DARI KANTOR SATKER KE PETUGAS LAPANGAN)</p>
        @else
            <h3>BERITA ACARA SERAH TERIMA DOKUMEN TERISI DAN PENYELESAIAN TUGAS</h3>
            <p style="font-size: 9.5pt; font-weight: bold; color: #444; margin-top: 2px;">(PENYERAHAN BERKAS HASIL PENCACAHAN DARI PETUGAS LAPANGAN KE KANTOR SATKER)</p>
        @endif
        <p style="margin-top: 4px;">Nomor: <strong>{{ $bast->bast_number }}</strong></p>
    </div>

    <p style="text-align: justify;">
        Pada hari ini, <strong>{{ $bast->bast_date->translatedFormat('l') }}</strong> tanggal <strong>{{ $bast->bast_date->translatedFormat('d F Y') }}</strong>, bertempat di Kantor Satuan Kerja SIMPLE / BPS, telah dilaksanakan serah terima {{ $bast->bast_type === 'kantor_ke_petugas' ? 'blanko dokumen dan perlengkapan lapangan' : 'dokumen kuesioner yang telah terisi lengkap hasil pendataan lapangan' }} antara pihak-pihak sebagai berikut:
    </p>

    @if($bast->bast_type === 'kantor_ke_petugas')
        <!-- KANTOR KE PETUGAS: Pihak Pertama adalah Kantor Satker, Pihak Kedua adalah Petugas Lapangan -->
        <!-- PIHAK PERTAMA (KANTOR / SATKER BPS) -->
        <table class="content-table">
            <tr>
                <td class="label-col"><strong>Nama Instansi / Satker</strong></td>
                <td class="sep-col">:</td>
                <td class="val-col"><strong>Badan Pusat Statistik (BPS) / Satker SIMPLE</strong></td>
            </tr>
            <tr>
                <td class="label-col">Koordinator / Petugas Logistik</td>
                <td class="sep-col">:</td>
                <td class="val-col">Koordinator Administrasi Logistik Satker</td>
            </tr>
            <tr>
                <td class="label-col">Kegiatan Penugasan</td>
                <td class="sep-col">:</td>
                <td class="val-col"><strong>{{ $bast->assignment->title ?? 'Kegiatan Lapangan Terkait' }}</strong></td>
            </tr>
            <tr>
                <td class="label-col">Wilayah Tugas</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $bast->assignment->location ?? '-' }}</td>
            </tr>
        </table>
        <p style="margin: 0 0 10px 0; font-size: 10pt; font-style: italic;">
            Selanjutnya disebut sebagai <strong>PIHAK PERTAMA (Yang Menyerahkan Blanko &amp; Perlengkapan)</strong>.
        </p>

        <!-- PIHAK KEDUA (PETUGAS LAPANGAN) -->
        <table class="content-table">
            <tr>
                <td class="label-col"><strong>Nama Petugas Lapangan</strong></td>
                <td class="sep-col">:</td>
                <td class="val-col"><strong>{{ $bast->officer->name }}</strong></td>
            </tr>
            <tr>
                <td class="label-col">Username / ID Petugas</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $bast->officer->username }}</td>
            </tr>
            <tr>
                <td class="label-col">Nomor HP / WhatsApp</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $bast->officer->phone ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">Peran / Jabatan</td>
                <td class="sep-col">:</td>
                <td class="val-col">Petugas Pencacah Lapangan (PCL / Mitra)</td>
            </tr>
        </table>
        <p style="margin: 0 0 10px 0; font-size: 10pt; font-style: italic;">
            Selanjutnya disebut sebagai <strong>PIHAK KEDUA (Yang Menerima Blanko &amp; Perlengkapan)</strong>.
        </p>

        <p style="text-align: justify; margin-top: 15px;">
            Dengan ini menyatakan bahwa PIHAK PERTAMA telah menyerahkan paket perlengkapan pendataan dan blanko dokumen survei/sensus kepada PIHAK KEDUA dalam kondisi baik dan lengkap, dengan rincian serah terima sebagai berikut:
        </p>
    @else
        <!-- PETUGAS KE KANTOR: Pihak Pertama adalah Petugas Lapangan, Pihak Kedua adalah Kantor Satker -->
        <!-- PIHAK PERTAMA (PETUGAS LAPANGAN) -->
        <table class="content-table">
            <tr>
                <td class="label-col"><strong>Nama Petugas Lapangan</strong></td>
                <td class="sep-col">:</td>
                <td class="val-col"><strong>{{ $bast->officer->name }}</strong></td>
            </tr>
            <tr>
                <td class="label-col">Username / ID Petugas</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $bast->officer->username }}</td>
            </tr>
            <tr>
                <td class="label-col">Nomor HP / WhatsApp</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $bast->officer->phone ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">Peran / Jabatan</td>
                <td class="sep-col">:</td>
                <td class="val-col">Petugas Pencacah Lapangan (PCL / Mitra)</td>
            </tr>
        </table>
        <p style="margin: 0 0 10px 0; font-size: 10pt; font-style: italic;">
            Selanjutnya disebut sebagai <strong>PIHAK PERTAMA (Yang Menyerahkan Dokumen Terisi)</strong>.
        </p>

        <!-- PIHAK KEDUA (KANTOR / SATKER BPS) -->
        <table class="content-table">
            <tr>
                <td class="label-col"><strong>Penerima / Koordinator Satker</strong></td>
                <td class="sep-col">:</td>
                <td class="val-col"><strong>Koordinator Pengawas Administrasi Satker</strong></td>
            </tr>
            <tr>
                <td class="label-col">Kegiatan Penugasan</td>
                <td class="sep-col">:</td>
                <td class="val-col"><strong>{{ $bast->assignment->title ?? 'Kegiatan Lapangan Terkait' }}</strong></td>
            </tr>
            <tr>
                <td class="label-col">Wilayah Tugas</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $bast->assignment->location ?? '-' }}</td>
            </tr>
        </table>
        <p style="margin: 0 0 10px 0; font-size: 10pt; font-style: italic;">
            Selanjutnya disebut sebagai <strong>PIHAK KEDUA (Yang Menerima Dokumen Terisi)</strong>.
        </p>

        <p style="text-align: justify; margin-top: 15px;">
            Dengan ini menyatakan bahwa PIHAK PERTAMA telah menyelesaikan seluruh kewajiban pencacahan lapangan dan menyerahkan bundel kuesioner/dokumen yang telah terisi lengkap beserta perlengkapan tugas kepada PIHAK KEDUA dengan rincian serah terima sebagai berikut:
        </p>
    @endif

    <div class="items-box">
{{ $bast->description }}
    </div>

    @if($bast->notes)
        <p style="font-size: 10pt; margin: 8px 0;">
            <strong>Catatan Pengawas / Administrasi:</strong> {{ $bast->notes }}
        </p>
    @endif

    <p style="text-align: justify;">
        Demikian Berita Acara ini dibuat dan ditandatangani dengan sebenarnya untuk dipergunakan sebagaimana mestinya sebagai bukti sah serah terima administrasi kegiatan.
    </p>

    <!-- TANDA TANGAN -->
    <table class="signature-table">
        <tr>
            @if($bast->bast_type === 'kantor_ke_petugas')
                <td class="signature-col">
                    <div>PIHAK PERTAMA,</div>
                    <div style="font-size: 9.5pt;">Pengelola Logistik Satker BPS</div>
                    <div class="sign-space"></div>
                    <div class="signer-name">Koordinator Logistik BPS</div>
                    <div class="signer-title">NIP. Satuan Kerja BPS</div>
                </td>
                <td class="signature-col">
                    <div>PIHAK KEDUA,</div>
                    <div style="font-size: 9.5pt;">Petugas Lapangan Penerima</div>
                    <div class="sign-space"></div>
                    <div class="signer-name">{{ $bast->officer->name }}</div>
                    <div class="signer-title">ID Petugas: {{ $bast->officer->username }}</div>
                </td>
            @else
                <td class="signature-col">
                    <div>PIHAK PERTAMA,</div>
                    <div style="font-size: 9.5pt;">Petugas Lapangan Yang Menyerahkan</div>
                    <div class="sign-space"></div>
                    <div class="signer-name">{{ $bast->officer->name }}</div>
                    <div class="signer-title">ID Petugas: {{ $bast->officer->username }}</div>
                </td>
                <td class="signature-col">
                    <div>PIHAK KEDUA,</div>
                    <div style="font-size: 9.5pt;">Penerima Berkas Satker BPS</div>
                    <div class="sign-space"></div>
                    <div class="signer-name">Koordinator Wilayah</div>
                    <div class="signer-title">Administrasi Satuan Kerja BPS</div>
                </td>
            @endif
        </tr>
    </table>

    <div class="doc-footer">
        Diterbitkan dan diverifikasi secara digital oleh Sistem Informasi SIMPLE pada {{ now()->translatedFormat('d F Y H:i') }} WIB.
        <br>Nomor Seri BAST: {{ $bast->bast_number }}
    </div>
</body>
</html>


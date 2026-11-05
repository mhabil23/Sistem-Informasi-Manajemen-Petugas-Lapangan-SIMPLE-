<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir - {{ $training->title }}</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
            size: A4 portrait;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #000;
        }

        .header-table {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 15px;
        }

        .header-title {
            text-align: center;
        }

        .instansi-utama {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .instansi-sub {
            font-size: 14pt;
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
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .doc-title h3 {
            font-size: 12pt;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
            font-weight: bold;
        }

        .info-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 10pt;
        }

        .info-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9.5pt;
        }

        .table-data th, .table-data td {
            border: 1px solid #000;
            padding: 5px 6px;
        }

        .table-data th {
            background-color: #f0f0f0;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9pt;
        }

        .ttd-box {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .ttd-box td {
            text-align: center;
            vertical-align: top;
            width: 50%;
        }

        .signature-line {
            display: inline-block;
            width: 180px;
            border-bottom: 1px solid #000;
            margin-top: 55px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- KOP RESMI -->
    <table class="header-table">
        <tr>
            <td class="header-title">
                <div class="instansi-utama">SISTEM INFORMASI MANAJEMEN PETUGAS LAPANGAN (SIMPLE)</div>
                <div class="instansi-sub">ADMINISTRASI PELATIHAN & BIMTEK PETUGAS</div>
                <div class="instansi-detail">Sekretariat Pengelolaan Data Responden & Pelatihan Teknis Lapangan</div>
            </td>
        </tr>
    </table>

    <!-- JUDUL DAFTAR HADIR -->
    <div class="doc-title">
        <h3>DAFTAR HADIR PESERTA PELATIHAN / BIMBINGAN TEKNIS</h3>
    </div>

    <!-- DETAIL KEGIATAN -->
    <table class="info-table">
        <tr>
            <td style="width: 130px;"><strong>Nama Kegiatan</strong></td>
            <td style="width: 10px;">:</td>
            <td><strong>{{ $training->title }}</strong></td>
        </tr>
        <tr>
            <td><strong>Tanggal Pelaksanaan</strong></td>
            <td>:</td>
            <td>{{ $training->start_date->isoFormat('D MMMM Y') }} s/d {{ $training->end_date->isoFormat('D MMMM Y') }}</td>
        </tr>
        <tr>
            <td><strong>Tempat / Lokasi</strong></td>
            <td>:</td>
            <td>{{ $training->location }}</td>
        </tr>
        <tr>
            <td><strong>Jumlah Peserta</strong></td>
            <td>:</td>
            <td>{{ $training->participants->count() }} Orang Terdaftar</td>
        </tr>
    </table>

    <!-- TABEL DAFTAR HADIR -->
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 28px;">No.</th>
                <th>Nama Peserta</th>
                <th style="width: 110px;">NIP / ID</th>
                <th style="width: 85px;">Kategori</th>
                <th>Instansi / Lembaga</th>
                <th style="width: 60px;">Presensi</th>
                <th style="width: 90px;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($training->participants as $i => $p)
                <tr>
                    <td style="text-align: center;">{{ $i + 1 }}</td>
                    <td>
                        <strong>{{ $p->name }}</strong>
                    </td>
                    <td style="text-align: center;">{{ $p->nip_or_id ?: '-' }}</td>
                    <td style="text-align: center;">{{ $p->category }}</td>
                    <td>{{ $p->agency ?: '-' }}</td>
                    <td style="text-align: center;">
                        {{ $p->attendance_status === 'present' ? 'HADIR' : ($p->attendance_status === 'excused' ? 'IZIN' : 'ALPA') }}
                    </td>
                    <td style="height: 25px; vertical-align: bottom; font-size: 8pt; color: #777;">
                        {{ $i + 1 }}. ............
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 15px; font-style: italic;">
                        Belum ada data peserta yang terdaftar pada kegiatan pelatihan ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TANDA TANGAN PENANGGUNG JAWAB -->
    <table class="ttd-box">
        <tr>
            <td>
                Mengetahui,<br>
                Penanggung Jawab Kegiatan Pelatihan<br>
                <div class="signature-line">{{ $training->creator ? $training->creator->name : 'Administrator SIMPLE' }}</div>
                <div style="font-size: 8.5pt;">Administrator / Koordinator</div>
            </td>
            <td>
                Ditetapkan di: {{ $training->location }}<br>
                Pada Tanggal: {{ now()->isoFormat('D MMMM Y') }}<br>
                Instruktur / Notulis Presensi<br>
                <div class="signature-line">( ........................................ )</div>
                <div style="font-size: 8.5pt;">NIP: ........................................</div>
            </td>
        </tr>
    </table>

</body>
</html>


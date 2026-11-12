<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Administrasi & Pelaporan - SIMPLE</title>
    <style>
        @page {
            margin: 1.5cm;
            size: A4 landscape;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 9.5pt;
            line-height: 1.35;
            color: #000;
        }

        .header-table {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }

        .header-title {
            text-align: center;
        }

        .instansi-utama {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .instansi-sub {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0d2142;
        }

        .instansi-detail {
            font-size: 8pt;
            font-style: italic;
            color: #333;
        }

        .doc-title {
            text-align: center;
            margin-top: 6px;
            margin-bottom: 12px;
        }

        .doc-title h3 {
            font-size: 11.5pt;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
            font-weight: bold;
        }

        .stat-grid {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }

        .stat-grid td {
            border: 1px solid #333;
            padding: 6px 10px;
            text-align: center;
            background-color: #f8fafc;
        }

        .stat-val {
            font-size: 12pt;
            font-weight: bold;
            color: #0284c7;
        }

        .stat-lbl {
            font-size: 8pt;
            text-transform: uppercase;
            color: #555;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 15px;
            font-size: 9pt;
        }

        .table-data th, .table-data td {
            border: 1px solid #000;
            padding: 4px 6px;
        }

        .table-data th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5pt;
        }

        .section-header {
            font-size: 10pt;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 4px;
            text-transform: uppercase;
            color: #0d2142;
        }

        .ttd-box {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .ttd-box td {
            text-align: center;
            vertical-align: top;
            width: 50%;
        }

        .signature-line {
            display: inline-block;
            width: 200px;
            border-bottom: 1px solid #000;
            margin-top: 50px;
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
                <div class="instansi-sub">LAPORAN REKAPITULASI PROGRAM TERPADU</div>
                <div class="instansi-detail">Dicetak secara sistematis pada tanggal: {{ now()->isoFormat('D MMMM Y, HH:mm') }} WIB</div>
            </td>
        </tr>
    </table>

    <div class="doc-title">
        <h3>REKAPITULASI DATA RESPONDEN, BAST, LAPORAN & PELATIHAN</h3>
    </div>

    <!-- STAT SUMMARY -->
    <table class="stat-grid">
        <tr>
            <td>
                <div class="stat-val">{{ $summary['counts']['total_respondents'] }}</div>
                <div class="stat-lbl">Total Responden</div>
            </td>
            <td>
                <div class="stat-val">{{ $summary['counts']['total_bast_resp'] + $summary['counts']['total_bast_officer'] }}</div>
                <div class="stat-lbl">Dokumen BAST Terbit</div>
            </td>
            <td>
                <div class="stat-val">{{ $summary['counts']['reports_approved'] }} / {{ $summary['counts']['total_reports'] }}</div>
                <div class="stat-lbl">Laporan Disetujui</div>
            </td>
            <td>
                <div class="stat-val">{{ $summary['counts']['total_participants'] }}</div>
                <div class="stat-lbl">Peserta Bimtek</div>
            </td>
        </tr>
    </table>

    <!-- 1. DATA RESPONDEN -->
    <div class="section-header">1. Sampel Data Responden Terdata</div>
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 25px;">No.</th>
                <th>Nama Responden</th>
                <th>Instansi / Lembaga</th>
                <th>Alamat</th>
                <th>Kegiatan Terkait</th>
                <th>Petugas Penginput</th>
                <th style="width: 80px;">Tanggal Input</th>
            </tr>
        </thead>
        <tbody>
            @forelse($summary['respondents']->take(15) as $i => $r)
                <tr>
                    <td style="text-align: center;">{{ $i + 1 }}</td>
                    <td><strong>{{ $r->name }}</strong></td>
                    <td>{{ $r->agency }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($r->address, 35) }}</td>
                    <td>{{ $r->assignment ? $r->assignment->title : '-' }}</td>
                    <td>{{ $r->officer ? $r->officer->name : '-' }}</td>
                    <td style="text-align: center;">{{ $r->input_date ? $r->input_date->format('d/m/Y') : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align: center; font-style: italic;">Tidak ada data responden.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- 2. DATA DOKUMEN BAST -->
    <div class="section-header">2. Sampel Berita Acara Serah Terima (BAST) Terbit</div>
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 25px;">No.</th>
                <th>Nomor BAST</th>
                <th>Jenis BAST</th>
                <th>Pihak Terkait</th>
                <th>Kegiatan</th>
                <th style="width: 80px;">Tanggal</th>
                <th style="width: 70px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $bastNo = 1; @endphp
            @foreach($summary['respondentBasts']->take(8) as $b)
                <tr>
                    <td style="text-align: center;">{{ $bastNo++ }}</td>
                    <td><strong>{{ $b->bast_number }}</strong></td>
                    <td>BAST Responden</td>
                    <td>{{ $b->respondent ? $b->respondent->name : '-' }}</td>
                    <td>{{ $b->assignment ? $b->assignment->title : '-' }}</td>
                    <td style="text-align: center;">{{ $b->bast_date ? $b->bast_date->format('d/m/Y') : '-' }}</td>
                    <td style="text-align: center;">{{ strtoupper($b->status) }}</td>
                </tr>
            @endforeach
            @foreach($summary['officerBasts']->take(7) as $ob)
                <tr>
                    <td style="text-align: center;">{{ $bastNo++ }}</td>
                    <td><strong>{{ $ob->bast_number }}</strong></td>
                    <td>BAST Petugas</td>
                    <td>{{ $ob->recipient_summary }}</td>
                    <td>{{ $ob->assignment ? $ob->assignment->title : '-' }}</td>
                    <td style="text-align: center;">{{ $ob->bast_date ? $ob->bast_date->format('d/m/Y') : '-' }}</td>
                    <td style="text-align: center;">{{ strtoupper($ob->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TANDA TANGAN -->
    <table class="ttd-box">
        <tr>
            <td>
                Mengetahui,<br>
                Koordinator Wilayah Lapangan SPKRT<br>
                <div class="signature-line">( .................................................... )</div>
                <div style="font-size: 8pt;">NIP: ....................................................</div>
            </td>
            <td>
                Dibuat Pada: {{ now()->isoFormat('D MMMM Y') }}<br>
                Administrator Pengelola Sistem SIMPLE<br>
                <div class="signature-line">{{ auth()->user()->name ?? 'Administrator SIMPLE' }}</div>
                <div style="font-size: 8pt;">Administrator Sistem</div>
            </td>
        </tr>
    </table>

</body>
</html>


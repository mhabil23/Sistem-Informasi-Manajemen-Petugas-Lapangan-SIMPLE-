<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $training->document_title ?: 'Daftar Hadir Peserta' }} - {{ $training->document_number ?: $training->title }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 14mm 12mm 14mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            color: #000000;
            line-height: 1.25;
            margin: 0;
            padding: 0;
        }
        .doc-header {
            text-align: center;
            font-weight: bold;
            font-size: 9.5pt;
            line-height: 1.35;
            margin-bottom: 8px;
        }
        .doc-meta {
            text-align: right;
            font-size: 7.8pt;
            font-family: monospace;
            color: #444;
            margin-bottom: 6px;
        }
        table.receipt-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border: 1px solid #000000;
        }
        table.receipt-table thead {
            display: table-header-group;
        }
        table.receipt-table thead th {
            background-color: #a6dde8;
            border: 1px solid #000000;
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
            font-size: 8.5pt;
            padding: 5px 4px;
        }
        table.receipt-table thead tr.col-numbers th {
            font-style: italic;
            font-weight: normal;
            font-size: 7.8pt;
            padding: 2px 4px;
            border-bottom: 1.2px solid #000000;
        }
        table.receipt-table tbody tr {
            page-break-inside: avoid;
        }
        table.receipt-table tbody td {
            border-left: 1px solid #000000;
            border-right: 1px solid #000000;
            border-top: 1px solid #a0a0a0;
            border-bottom: 1px solid #a0a0a0;
            text-align: center;
            vertical-align: middle;
            padding: 5px 5px;
            font-size: 8.3pt;
            word-wrap: break-word;
        }
        table.receipt-table tbody tr:last-child td {
            border-bottom: 1px solid #000000;
        }
        table.sign-inner {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        table.sign-inner td {
            border: none !important;
            padding: 0 3px !important;
            text-align: left !important;
            vertical-align: middle !important;
            white-space: nowrap;
            font-size: 8.3pt;
        }
        table.footer-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
            page-break-inside: avoid;
        }
        table.footer-grid td {
            border: none;
            vertical-align: top;
            font-size: 8.5pt;
        }
        .item-line {
            line-height: 1.45;
        }
        .maker-box {
            text-align: center;
            line-height: 1.3;
        }
        .signature-img-container {
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 2px auto;
        }
        .signature-img-container img {
            max-height: 50px;
            max-width: 150px;
        }
    </style>
</head>
<body>

    @php
        $docTitle = strtoupper($training->document_title ?: 'DAFTAR HADIR PESERTA');
        $subtitle = strtoupper($training->activity_subtitle ?: $training->title);
        $institution = strtoupper($training->institution_name ?: ('BPS KABUPATEN KOLAKA UTARA TAHUN ' . ($training->start_date ? $training->start_date->format('Y') : now()->format('Y'))));
        $place = strtoupper($training->document_place ?: ($training->location ?: 'KANTOR BPS KABUPATEN KOLAKA UTARA'));
        $dateStr = $training->start_date ? $training->start_date->translatedFormat('d F Y') : now()->translatedFormat('d F Y');
    @endphp

    <!-- HEADER RESMI 4 BARIS SESUAI STANDAR BPS -->
    <div class="doc-header">
        <div>{{ $docTitle }}</div>
        <div>{{ $subtitle }}</div>
        <div>{{ $institution }}</div>
        <div>TEMPAT: {{ $place }} ({{ $dateStr }})</div>
    </div>

    @if(!empty($training->document_number))
        <div class="doc-meta">
            Nomor: <strong>{{ $training->document_number }}</strong>
        </div>
    @endif

    <!-- TABEL UTAMA DAFTAR HADIR -->
    <table class="receipt-table">
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 32%;">NAMA</th>
                <th style="width: 25%;">JABATAN &amp; UNIT KERJA</th>
                <th style="width: 20%;">ASAL / BERTUGAS SEBAGAI</th>
                <th style="width: 18%;">TANDA TANGAN</th>
            </tr>
            <tr class="col-numbers">
                <th>(1)</th>
                <th>(2)</th>
                <th>(3)</th>
                <th>(4)</th>
                <th>(5)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($training->participants as $idx => $p)
                @php
                    $rowNum = $idx + 1;
                    $isOdd = ($rowNum % 2 !== 0);
                    $hasSignature = !empty($p->signature_path) && file_exists(storage_path('app/public/' . $p->signature_path));
                @endphp
                <tr>
                    <td>{{ $rowNum }}</td>
                    <td style="text-align: left; font-weight: bold;">
                        {{ $p->name }}
                        @if(!empty($p->nip_or_id))
                            <div style="font-size: 7.2pt; color: #555; font-weight: normal;">ID/NIP: {{ $p->nip_or_id }}</div>
                        @endif
                    </td>
                    <td style="text-align: left;">{{ $p->category ?: 'Petugas Lapangan' }}</td>
                    <td style="text-align: left;">{{ $p->agency ?: 'Kabupaten Kolaka Utara' }}</td>
                    <td style="padding: 2px 4px;">
                        @if($hasSignature)
                            <div style="text-align: center; height: 32px; display: flex; align-items: center; justify-content: center;">
                                <img src="{{ storage_path('app/public/' . $p->signature_path) }}" alt="TTD" style="max-height: 30px; max-width: 90px;">
                            </div>
                        @else
                            {{-- Format garis titik-titik selang-seling BPS resmi --}}
                            <table class="sign-inner">
                                <tr>
                                    @if($isOdd)
                                        <td style="width: 100%; text-align: left;">
                                            {{ $rowNum }}. ....................
                                        </td>
                                    @else
                                        <td style="width: 100%; text-align: right; padding-right: 6px;">
                                            {{ $rowNum }}. ....................
                                        </td>
                                    @endif
                                </tr>
                            </table>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px; color: #777; font-style: italic;">
                        Belum ada data peserta yang terdaftar pada lembar daftar hadir ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- FOOTER: CATATAN (KIRI) & PENANGGUNG JAWAB (KANAN) -->
    <table class="footer-grid">
        <tr>
            <!-- Bagian Kiri: Keterangan / Catatan Pertemuan -->
            <td style="width: 58%; padding-right: 15px;">
                <div class="item-line" style="font-weight: bold; text-decoration: underline; margin-bottom: 4px;">
                    Catatan / Agenda Pelaksanaan:
                </div>
                <div class="item-line" style="font-size: 8pt; color: #222;">
                    {!! nl2br(e($training->notes ?: ($training->description ?: "Kegiatan berjalan tertib sesuai jadwal yang telah ditentukan. Seluruh peserta menandatangani daftar hadir ini sebagai bukti keikutsertaan resmi."))) !!}
                </div>
                <div style="margin-top: 10px; font-size: 7.8pt; color: #555;">
                    Total Peserta Terdaftar: <strong>{{ $training->participants->count() }} orang</strong>
                </div>
            </td>

            <!-- Bagian Kanan: Pembuat Daftar / Penanggung Jawab -->
            <td style="width: 42%;">
                <div class="maker-box">
                    <div>{{ $training->document_place ?: ($training->location ?: 'Lasusua') }}, {{ $dateStr }}</div>
                    <div style="font-weight: bold; margin-bottom: 2px;">Penanggung Jawab / Pembuat Daftar,</div>

                    <div class="signature-img-container">
                        @if(!empty($training->maker_signature_path) && file_exists(storage_path('app/public/' . $training->maker_signature_path)))
                            <img src="{{ storage_path('app/public/' . $training->maker_signature_path) }}" alt="Tanda Tangan Pembuat">
                        @else
                            <div style="height: 48px;"></div>
                        @endif
                    </div>

                    <div style="font-weight: bold; text-decoration: underline;">
                        {{ $training->maker_name ?: ($training->creator ? $training->creator->name : 'Penanggung Jawab Kegiatan') }}
                    </div>
                    @if(!empty($training->maker_nip))
                        <div style="font-size: 7.8pt;">NIP: {{ $training->maker_nip }}</div>
                    @else
                        <div style="font-size: 7.8pt;">BPS Kabupaten Kolaka Utara</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

</body>
</html>

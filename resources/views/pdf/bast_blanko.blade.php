<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $bast->document_title ?: 'Tanda Terima Blanko & Perlengkapan' }} - {{ $bast->bast_number }}</title>
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
            margin-bottom: 10px;
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
        .maker-space {
            height: 48px;
        }
        .maker-name {
            font-weight: bold;
        }
    </style>
</head>
<body>
    @php
        $recipients = $bast->resolvedRecipients();
        $items = $bast->parsed_items;
        $yearStr = $bast->bast_date ? $bast->bast_date->format('Y') : now()->format('Y');
        $docTitle = trim((string) $bast->document_title) !== ''
            ? $bast->document_title
            : 'Tanda Terima Blanko & Perlengkapan';
        $activitySubtitle = trim((string) $bast->activity_subtitle) !== ''
            ? $bast->activity_subtitle
            : ($bast->assignment->title ?? '-');
        $institutionName = trim((string) $bast->institution_name) !== ''
            ? $bast->institution_name
            : "BPS Kabupaten Kolaka Utara Tahun {$yearStr}";
    @endphp

    <div class="doc-header">
        <div>{{ $docTitle }}</div>
        <div>{{ $activitySubtitle }}</div>
        <div>{{ $institutionName }}</div>
        <div>{{ $bast->place_date_line }}</div>
    </div>

    <table class="receipt-table">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 27%;">Nama</th>
                <th style="width: 20%;">Jabatan dan Unit Kerja</th>
                <th style="width: 22%;">Asal / Bertugas Sebagai</th>
                <th style="width: 26%;">Tanda Tangan</th>
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
            @forelse($recipients as $idx => $recipient)
                @php
                    $rowNum = $idx + 1;
                    $isOdd = ($rowNum % 2) === 1;
                @endphp
                <tr>
                    <td>{{ $rowNum }}</td>
                    <td>{{ $recipient->recipient_name }}</td>
                    <td>{{ $recipient->position_unit ?: 'Mitra' }}</td>
                    <td>{{ $recipient->origin_role ?: '-' }}</td>
                    <td style="padding: 2px 4px;">
                        @php
                            $hasSign = !empty($recipient->signature_path);
                            $signBase64 = null;
                            if ($hasSign) {
                                $fullPath = storage_path('app/public/' . $recipient->signature_path);
                                if (!file_exists($fullPath)) {
                                    $fullPath = public_path('storage/' . $recipient->signature_path);
                                }
                                if (file_exists($fullPath)) {
                                    $signBase64 = base64_encode(file_get_contents($fullPath));
                                }
                            }
                        @endphp
                        <table class="sign-inner">
                            <tr>
                                <td style="width: 50%;">
                                    @if($isOdd)
                                        <span style="font-weight: 500;">{{ $rowNum }}.</span>
                                        @if($signBase64)
                                            <img src="data:image/png;base64,{{ $signBase64 }}" style="max-height: 22px; max-width: 58px; vertical-align: middle; display: inline-block;">
                                        @else
                                            <span>...........</span>
                                        @endif
                                    @else
                                        &nbsp;
                                    @endif
                                </td>
                                <td style="width: 50%; padding-left: 6px !important;">
                                    @if(!$isOdd)
                                        <span style="font-weight: 500;">{{ $rowNum }}.</span>
                                        @if($signBase64)
                                            <img src="data:image/png;base64,{{ $signBase64 }}" style="max-height: 22px; max-width: 58px; vertical-align: middle; display: inline-block;">
                                        @else
                                            <span>...........</span>
                                        @endif
                                    @else
                                        &nbsp;
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding: 12px; color: #555555;">Belum ada data penerima yang dicantumkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-grid">
        <tr>
            <td style="width: 60%; padding-left: 12px;">
                @foreach($items as $idx => $itemText)
                    <div class="item-line">{{ $idx + 1 }}. {{ $itemText }}</div>
                @endforeach
                @if($bast->notes)
                    <div style="margin-top: 8px; font-size: 7.8pt; color: #333333;">Catatan: {{ $bast->notes }}</div>
                @endif
            </td>
            <td style="width: 40%;" class="maker-box">
                <div>Pembuat Daftar</div>
                <div class="maker-space"></div>
                <div class="maker-name">{{ $bast->maker_name ?: 'Koordinator Satker BPS' }}</div>
                @if($bast->resolved_maker_nip !== '')
                    <div>NIP. {{ $bast->resolved_maker_nip }}</div>
                @endif
            </td>
        </tr>
    </table>
</body>
</html>


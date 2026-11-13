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

        // Auto-crop area transparan canvas agar goresan tanda tangan tampil besar, tebal & proporsional
        $getTrimmedSignBase64 = function (?string $path): ?string {
            if (empty($path)) return null;

            $p = storage_path('app/public/' . $path);
            if (!file_exists($p)) {
                $p = public_path('storage/' . $path);
            }
            if (!file_exists($p)) return null;

            if (function_exists('imagecreatefrompng') && function_exists('imagecrop')) {
                $im = @imagecreatefrompng($p);
                if ($im) {
                    $w = imagesx($im);
                    $h = imagesy($im);
                    $minX = $w; $minY = $h; $maxX = 0; $maxY = 0;
                    $found = false;

                    for ($x = 0; $x < $w; $x++) {
                        for ($y = 0; $y < $h; $y++) {
                            $rgba = imagecolorat($im, $x, $y);
                            $alpha = ($rgba & 0x7F000000) >> 24;
                            if ($alpha < 120) {
                                $found = true;
                                if ($x < $minX) $minX = $x;
                                if ($x > $maxX) $maxX = $x;
                                if ($y < $minY) $minY = $y;
                                if ($y > $maxY) $maxY = $y;
                            }
                        }
                    }

                    if ($found) {
                        $pad = 6;
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
                <th style="width: 25%;">Nama</th>
                <th style="width: 20%;">Jabatan dan Unit Kerja</th>
                <th style="width: 20%;">Asal / Bertugas Sebagai</th>
                <th style="width: 30%;">Tanda Tangan</th>
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
                    $signBase64 = $getTrimmedSignBase64($recipient->signature_path);
                @endphp
                <tr>
                    <td>{{ $rowNum }}</td>
                    <td>{{ $recipient->recipient_name }}</td>
                    <td>{{ $recipient->position_unit ?: 'Mitra' }}</td>
                    <td>{{ $recipient->origin_role ?: '-' }}</td>
                    <td style="padding: 1px 3px; vertical-align: middle;">
                        <table class="sign-inner">
                            <tr>
                                <td style="width: 50%; vertical-align: middle;">
                                    @if($isOdd)
                                        <span style="font-weight: 500; font-size: 8.5pt;">{{ $rowNum }}.</span>
                                        @if($signBase64)
                                            <img src="data:image/png;base64,{{ $signBase64 }}" style="max-height: 28px; max-width: 88px; vertical-align: middle; display: inline-block; margin-left: 2px;">
                                        @else
                                            <span>...........</span>
                                        @endif
                                    @else
                                        &nbsp;
                                    @endif
                                </td>
                                <td style="width: 50%; padding-left: 6px !important; vertical-align: middle;">
                                    @if(!$isOdd)
                                        <span style="font-weight: 500; font-size: 8.5pt;">{{ $rowNum }}.</span>
                                        @if($signBase64)
                                            <img src="data:image/png;base64,{{ $signBase64 }}" style="max-height: 28px; max-width: 88px; vertical-align: middle; display: inline-block; margin-left: 2px;">
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


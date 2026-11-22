<?php

namespace App\Services;

use Exception;
use SimpleXMLElement;
use ZipArchive;

class SimpleExcelParser
{
    /**
     * Parse Excel (.xlsx) or CSV file into an array of associative rows.
     *
     * @param string $filePath
     * @param string|null $extension
     * @return array<int, array<string, string>>
     */
    public function parse(string $filePath, ?string $extension = null): array
    {
        $rawRows = $this->getRawRows($filePath, $extension);
        return $this->mapRowsToAssociative($rawRows);
    }

    /**
     * Parse Excel (.xlsx) or CSV file into generic associative rows using the file's original header row.
     *
     * @param string $filePath
     * @param string|null $extension
     * @return array<int, array<string, string>>
     */
    public function parseGeneric(string $filePath, ?string $extension = null): array
    {
        $rawRows = $this->getRawRows($filePath, $extension);
        return $this->mapRowsGeneric($rawRows);
    }

    /**
     * Get raw cell-indexed rows (['A' => val, 'B' => val]) from CSV or XLSX.
     */
    public function getRawRows(string $filePath, ?string $extension = null): array
    {
        $ext = strtolower($extension ?: pathinfo($filePath, PATHINFO_EXTENSION));

        if ($ext === 'csv' || $ext === 'txt') {
            return $this->getRawCsvRows($filePath);
        }

        if ($ext === 'xlsx' || $ext === 'xls') {
            return $this->getRawXlsxRows($filePath);
        }

        // Fallback: try xlsx first, then csv
        try {
            return $this->getRawXlsxRows($filePath);
        } catch (Exception) {
            return $this->getRawCsvRows($filePath);
        }
    }

    /**
     * Parse XLSX file using native ZipArchive and SimpleXML (no third-party dependencies required).
     */
    public function parseXlsx(string $filePath): array
    {
        return $this->mapRowsToAssociative($this->getRawXlsxRows($filePath));
    }

    /**
     * Extract raw indexed rows from XLSX file.
     */
    public function getRawXlsxRows(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new Exception("Tidak dapat membaca berkas Excel. Pastikan berkas berformat .xlsx yang valid.");
        }

        // 1. Extract shared strings
        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml !== false) {
            $xml = @simplexml_load_string($sharedStringsXml);
            if ($xml) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $str = '';
                        foreach ($si->r as $r) {
                            $str .= (string) $r->t;
                        }
                        $sharedStrings[] = $str;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Discover sheets and relationships
        $workbookXml = $zip->getFromName('xl/workbook.xml');
        $relsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');
        $sheetTargets = [];

        if ($workbookXml !== false && $relsXml !== false) {
            $wb = @simplexml_load_string($workbookXml);
            $rels = @simplexml_load_string($relsXml);

            $relMap = [];
            if ($rels) {
                foreach ($rels->Relationship as $rel) {
                    $relMap[(string) $rel['Id']] = (string) $rel['Target'];
                }
            }

            if ($wb && isset($wb->sheets->sheet)) {
                foreach ($wb->sheets->sheet as $s) {
                    $sName = (string) $s['name'];
                    $rId = (string) $s->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
                    if (isset($relMap[$rId])) {
                        $target = $relMap[$rId];
                        $sheetPath = (str_starts_with($target, '/') || str_starts_with($target, 'xl/'))
                            ? ltrim($target, '/')
                            : 'xl/' . $target;
                        $sheetTargets[$sName] = $sheetPath;
                    }
                }
            }
        }

        if (empty($sheetTargets)) {
            $sheetTargets['Sheet1'] = 'xl/worksheets/sheet1.xml';
        }

        // Find candidate sheet: prefer sheets containing 'SIMPLE', 'Database', 'SPKRT', 'Sampel', 'Mitra'
        $chosenTarget = null;
        foreach ($sheetTargets as $name => $path) {
            if (preg_match('/(simple|database|spkrt|sampel|responden|kegiatan|petugas|peserta|mitra)/i', $name)) {
                $chosenTarget = $path;
                break;
            }
        }

        // If no preferred sheet name matched, look inside sheets to find one with our expected headers
        if ($chosenTarget === null) {
            foreach ($sheetTargets as $path) {
                $testXml = $zip->getFromName($path);
                if ($testXml !== false && stripos($testXml, 'Sampel') !== false) {
                    $chosenTarget = $path;
                    break;
                }
            }
        }

        if ($chosenTarget === null) {
            $chosenTarget = reset($sheetTargets);
        }

        $sheetXmlContent = $zip->getFromName($chosenTarget);
        if ($sheetXmlContent === false) {
            // fallback to sheet1
            $sheetXmlContent = $zip->getFromName('xl/worksheets/sheet1.xml');
        }

        if ($sheetXmlContent === false) {
            $zip->close();
            throw new Exception("Lembar kerja (worksheet) tidak ditemukan dalam berkas Excel.");
        }

        $xml = @simplexml_load_string($sheetXmlContent);
        $rawRows = [];

        if ($xml && isset($xml->sheetData->row)) {
            foreach ($xml->sheetData->row as $row) {
                $rowCells = [];
                foreach ($row->c as $cell) {
                    $ref = (string) $cell['r'];
                    $col = preg_replace('/[0-9]/', '', $ref);
                    $type = (string) $cell['t'];
                    $val = isset($cell->v) ? (string) $cell->v : '';

                    if ($type === 's' && isset($sharedStrings[(int) $val])) {
                        $val = $sharedStrings[(int) $val];
                    } elseif ($type === 'inlineStr' && isset($cell->is->t)) {
                        $val = (string) $cell->is->t;
                    }

                    $rowCells[$col] = trim($val);
                }
                if (!empty(array_filter($rowCells, fn($v) => $v !== ''))) {
                    $rawRows[] = $rowCells;
                }
            }
        }

        $zip->close();

        return $rawRows;
    }

    /**
     * Parse CSV file (handles comma, semicolon, tab, and UTF-8 BOM).
     */
    public function parseCsv(string $filePath): array
    {
        return $this->mapRowsToAssociative($this->getRawCsvRows($filePath));
    }

    /**
     * Extract raw indexed rows from CSV file.
     */
    public function getRawCsvRows(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new Exception("Gagal membuka berkas CSV.");
        }

        // Read first line to detect delimiter
        $firstLine = fgets($handle);
        rewind($handle);

        $delimiter = ',';
        if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
            $delimiter = ';';
        } elseif (substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
            $delimiter = "\t";
        }

        $rawRows = [];
        $isFirst = true;

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($isFirst) {
                // Strip UTF-8 BOM if present
                if (isset($data[0])) {
                    $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', $data[0]);
                }
                $isFirst = false;
            }

            $indexedRow = [];
            foreach ($data as $i => $val) {
                $colKey = $this->indexToColumnLetter($i);
                $indexedRow[$colKey] = trim($val);
            }

            if (!empty(array_filter($indexedRow, fn($v) => $v !== ''))) {
                $rawRows[] = $indexedRow;
            }
        }

        fclose($handle);

        return $rawRows;
    }

    /**
     * Map raw indexed rows into generic associative arrays using the first non-empty row as headers.
     */
    public function mapRowsGeneric(array $rawRows): array
    {
        if (empty($rawRows)) {
            return [];
        }

        // Find header row: scan first 8 rows for a row with at least 2 non-empty columns
        $headerRowIdx = -1;
        $headers = [];

        foreach (array_slice($rawRows, 0, 8, true) as $idx => $row) {
            $nonEmpty = array_filter($row, fn($v) => trim((string)$v) !== '');
            if (count($nonEmpty) >= 2) {
                $headerRowIdx = $idx;
                $headers = $row;
                break;
            }
        }

        if ($headerRowIdx === -1) {
            return [];
        }

        $result = [];
        $dataRows = array_slice($rawRows, $headerRowIdx + 1);

        foreach ($dataRows as $row) {
            $item = [];
            $hasAnyValue = false;
            foreach ($headers as $colKey => $headerName) {
                $trimmedHeader = trim((string) $headerName);
                if ($trimmedHeader === '') {
                    continue;
                }
                $val = isset($row[$colKey]) ? trim((string) $row[$colKey]) : '';
                $item[$trimmedHeader] = $val;
                if ($val !== '') {
                    $hasAnyValue = true;
                }
            }

            if ($hasAnyValue) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Maps raw indexed rows into clean structured data using column headers.
     */
    protected function mapRowsToAssociative(array $rawRows): array
    {
        if (empty($rawRows)) {
            return [];
        }

        // Find header row: scan first 5 rows for keywords like "Nama", "Kegiatan", "Sampel"
        $headerRowIdx = -1;
        $headerMap = [];

        foreach (array_slice($rawRows, 0, 8, true) as $idx => $row) {
            $matchedKeys = 0;
            $tempMap = [];

            foreach ($row as $col => $cellText) {
                $clean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $cellText));
                $stdField = $this->matchStandardColumn($clean);
                if ($stdField) {
                    $tempMap[$col] = $stdField;
                    $matchedKeys++;
                }
            }

            // If row has at least 2 key headers (e.g. Nama Petugas, Nama Kegiatan, or Sampel)
            if ($matchedKeys >= 2) {
                $headerRowIdx = $idx;
                $headerMap = $tempMap;
                break;
            }
        }

        if ($headerRowIdx === -1) {
            // Default mapping assuming columns A..G as in the BPS SPKRT template:
            // A: ID SOBAT, B: Nama, C: Nama Kegiatan, D: Pembebanan Anggaran, E: Penugasan, F: Wilayah Tugas, G: Nama Kepala Keluarga Sampel
            $headerMap = [
                'A' => 'id_sobat',
                'B' => 'nama_petugas',
                'C' => 'nama_kegiatan',
                'D' => 'pembebanan_anggaran',
                'E' => 'penugasan',
                'F' => 'wilayah_tugas',
                'G' => 'nama_sampel',
            ];
            $dataRows = $rawRows;
        } else {
            $dataRows = array_slice($rawRows, $headerRowIdx + 1);
        }

        $structured = [];

        foreach ($dataRows as $row) {
            $entry = [
                'id_sobat' => '',
                'nama_petugas' => '',
                'nama_kegiatan' => '',
                'pembebanan_anggaran' => '',
                'penugasan' => 'Petugas Pendataan Lapangan',
                'wilayah_tugas' => '',
                'nama_sampel' => '',
                'jenis_kelamin' => '',
                'kelas' => '',
                'bank' => '',
            ];

            foreach ($headerMap as $col => $field) {
                if (isset($row[$col])) {
                    $entry[$field] = $row[$col];
                }
            }

            // Only add if at least nama_kegiatan OR nama_petugas OR nama_sampel is not empty
            if (!empty($entry['nama_kegiatan']) || !empty($entry['nama_petugas']) || !empty($entry['nama_sampel'])) {
                $structured[] = $entry;
            }
        }

        return $structured;
    }

    /**
     * Map fuzzy header text to standard field name.
     */
    protected function matchStandardColumn(string $cleanText): ?string
    {
        if (str_contains($cleanText, 'idsobat') || str_contains($cleanText, 'sobatid') || $cleanText === 'sobat') {
            return 'id_sobat';
        }

        if (str_contains($cleanText, 'namakegiatan') || str_contains($cleanText, 'judulkegiatan') || $cleanText === 'kegiatan') {
            return 'nama_kegiatan';
        }

        if (str_contains($cleanText, 'pembebanan') || str_contains($cleanText, 'anggaran') || str_contains($cleanText, 'dipa')) {
            return 'pembebanan_anggaran';
        }

        if (str_contains($cleanText, 'wilayah') || str_contains($cleanText, 'kecamatan') || str_contains($cleanText, 'lokasi')) {
            return 'wilayah_tugas';
        }

        if (str_contains($cleanText, 'penugasan') || $cleanText === 'tugas' || str_contains($cleanText, 'peran') || str_contains($cleanText, 'jabatan')) {
            return 'penugasan';
        }

        if (str_contains($cleanText, 'jeniskelamin') || $cleanText === 'jk' || str_contains($cleanText, 'gender') || str_contains($cleanText, 'kelamin')) {
            return 'jenis_kelamin';
        }

        if (str_contains($cleanText, 'kelas')) {
            return 'kelas';
        }

        if (str_contains($cleanText, 'bank') || str_contains($cleanText, 'rekening')) {
            return 'bank';
        }

        if (str_contains($cleanText, 'kepalakeluarga') || str_contains($cleanText, 'sampel') || str_contains($cleanText, 'krt') || str_contains($cleanText, 'responden')) {
            return 'nama_sampel';
        }

        if ($cleanText === 'nama' || str_contains($cleanText, 'namapetugas') || str_contains($cleanText, 'petugas') || str_contains($cleanText, 'pcl') || str_contains($cleanText, 'pml') || str_contains($cleanText, 'ppl')) {
            return 'nama_petugas';
        }

        return null;
    }

    /**
     * Helper to convert 0-based index to Excel column letter (0 => A, 1 => B, 26 => AA, etc.)
     */
    protected function indexToColumnLetter(int $index): string
    {
        $letters = '';
        while ($index >= 0) {
            $letters = chr(($index % 26) + 65) . $letters;
            $index = intdiv($index, 26) - 1;
        }
        return $letters;
    }

    /**
     * Parse Excel / CSV containing Mitra BPS and map into clean array of mitra records.
     * Supports both standard BPS Mitra export structure and generic tabular spreadsheets.
     *
     * @param string $filePath
     * @param string|null $extension
     * @return array<int, array<string, mixed>>
     */
    public function parseMitraRows(string $filePath, ?string $extension = null): array
    {
        $rawRows = $this->getRawRows($filePath, $extension);
        if (empty($rawRows)) {
            return [];
        }

        // 1. Detect header row
        $headerRowIdx = -1;
        $matchedMap = [];

        foreach (array_slice($rawRows, 0, 10, true) as $idx => $row) {
            $tempMap = [];
            foreach ($row as $col => $cellText) {
                $clean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string) $cellText));
                if (empty($clean)) {
                    continue;
                }

                if ($clean === 'sobatid' || $clean === 'idsobat' || str_contains($clean, 'sobat')) {
                    $tempMap['sobat_id'] = $col;
                } elseif (str_contains($clean, 'namalengkap') || $clean === 'nama' || str_contains($clean, 'namamitra') || str_contains($clean, 'namapetugas')) {
                    $tempMap['name'] = $col;
                } elseif (str_contains($clean, 'email') || str_contains($clean, 'surel')) {
                    $tempMap['email'] = $col;
                } elseif (str_contains($clean, 'notelp') || str_contains($clean, 'nohp') || str_contains($clean, 'telepon') || str_contains($clean, 'phone') || str_contains($clean, 'kontak') || $clean === 'hp') {
                    $tempMap['phone'] = $col;
                } elseif (str_contains($clean, 'kecamatan') || $clean === 'kec') {
                    $tempMap['district'] = $col;
                } elseif (str_contains($clean, 'desa') || str_contains($clean, 'kelurahan') || str_contains($clean, 'desakel')) {
                    $tempMap['village'] = $col;
                } elseif (str_contains($clean, 'alamatdetail') || $clean === 'alamat') {
                    $tempMap['address'] = $col;
                } elseif (str_contains($clean, 'jeniskelamin') || $clean === 'kelamin' || $clean === 'gender' || $clean === 'jk') {
                    $tempMap['gender'] = $col;
                } elseif (str_contains($clean, 'tempattanggal') || str_contains($clean, 'tanggallahir') || $clean === 'ttl' || str_contains($clean, 'lahir')) {
                    $tempMap['birth_info'] = $col;
                } elseif (str_contains($clean, 'pendidikan')) {
                    $tempMap['education'] = $col;
                } elseif (str_contains($clean, 'posisi') || str_contains($clean, 'jabatan')) {
                    $tempMap['position'] = $col;
                } elseif (str_contains($clean, 'nik') || str_contains($clean, 'noktp')) {
                    $tempMap['nik'] = $col;
                }
            }

            // If header row has at least 'name' and ('sobat_id' or 'email' or 'phone' or 'district')
            if (isset($tempMap['name']) && (isset($tempMap['sobat_id']) || isset($tempMap['email']) || isset($tempMap['phone']) || isset($tempMap['district']))) {
                $headerRowIdx = $idx;
                $matchedMap = $tempMap;
                break;
            }
        }

        // 2. Fallback to standard BPS SOBAT export column positions if no header was matched
        if ($headerRowIdx === -1) {
            $matchedMap = [
                'name' => 'A',
                'position' => 'B',
                'address' => 'E',
                'district' => 'H',
                'village' => 'I',
                'birth_info' => 'J',
                'gender' => 'L',
                'education' => 'N',
                'phone' => 'T',
                'sobat_id' => 'U',
                'email' => 'V',
            ];
            $dataRows = $rawRows;
        } else {
            $dataRows = array_slice($rawRows, $headerRowIdx + 1);
        }

        $records = [];
        foreach ($dataRows as $row) {
            $colName = $matchedMap['name'] ?? 'A';
            $name = trim((string) ($row[$colName] ?? ''));

            // Ignore empty rows, header repeats, or total/footer notes
            if ($name === '' || str_starts_with($name, '*') || str_starts_with(strtolower($name), 'umur') || str_starts_with(strtolower($name), 'total') || strtolower($name) === 'nama lengkap' || strtolower($name) === 'nama') {
                continue;
            }

            // Extract Sobat ID
            $sobatId = '';
            if (isset($matchedMap['sobat_id']) && isset($row[$matchedMap['sobat_id']])) {
                $sobatId = preg_replace('/[^\w]/', '', trim((string) $row[$matchedMap['sobat_id']]));
            }

            // Extract Email & clean trailing junk like "Sjaa" at end of .com
            $email = '';
            if (isset($matchedMap['email']) && isset($row[$matchedMap['email']])) {
                $rawEmail = strtolower(trim((string) $row[$matchedMap['email']]));
                $email = preg_replace('/(\.(?:com|co\.id|id|org|net|gov|edu)).*$/i', '$1', $rawEmail);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $email = '';
                }
            }

            // Extract & normalize Phone
            $phone = '';
            if (isset($matchedMap['phone']) && isset($row[$matchedMap['phone']])) {
                $phone = preg_replace('/[^\d+]/', '', trim((string) $row[$matchedMap['phone']]));
                if (str_starts_with($phone, '+62')) {
                    $phone = '0' . substr($phone, 3);
                } elseif (str_starts_with($phone, '62')) {
                    $phone = '0' . substr($phone, 2);
                }
            }

            // Extract & clean Kecamatan (remove leading code e.g. "(020) LASUSUA" -> "Lasusua")
            $district = '';
            if (isset($matchedMap['district']) && isset($row[$matchedMap['district']])) {
                $rawKec = trim((string) $row[$matchedMap['district']]);
                $cleanKec = preg_replace('/^\(\d+\)\s*/', '', $rawKec);
                $district = ucwords(strtolower(trim($cleanKec)));
            }

            // Extract & clean Desa
            $village = '';
            if (isset($matchedMap['village']) && isset($row[$matchedMap['village']])) {
                $rawDesa = trim((string) $row[$matchedMap['village']]);
                $cleanDesa = preg_replace('/^\(\d+\)\s*/', '', $rawDesa);
                $village = ucwords(strtolower(trim($cleanDesa)));
            }

            // Extract Address
            $address = '';
            if (isset($matchedMap['address']) && isset($row[$matchedMap['address']])) {
                $address = trim((string) $row[$matchedMap['address']]);
            }

            // Extract & normalize Gender
            $gender = '';
            if (isset($matchedMap['gender']) && isset($row[$matchedMap['gender']])) {
                $rawG = strtolower(trim((string) $row[$matchedMap['gender']]));
                if (str_starts_with($rawG, 'p') || str_contains($rawG, 'wanita')) {
                    $gender = 'Perempuan';
                } elseif (str_starts_with($rawG, 'l') || str_contains($rawG, 'pria')) {
                    $gender = 'Laki-Laki';
                }
            }

            // Extract Birth Info (TTL)
            $birthInfo = '';
            if (isset($matchedMap['birth_info']) && isset($row[$matchedMap['birth_info']])) {
                $birthInfo = trim((string) $row[$matchedMap['birth_info']]);
            }

            // Extract Education
            $education = '';
            if (isset($matchedMap['education']) && isset($row[$matchedMap['education']])) {
                $education = trim((string) $row[$matchedMap['education']]);
            }

            // Extract Position
            $position = 'Mitra Pendataan';
            if (isset($matchedMap['position']) && isset($row[$matchedMap['position']])) {
                $pos = trim((string) $row[$matchedMap['position']]);
                if (!empty($pos)) {
                    $position = $pos;
                }
            }

            // Extract NIK if present
            $nik = null;
            if (isset($matchedMap['nik']) && isset($row[$matchedMap['nik']])) {
                $nik = trim((string) $row[$matchedMap['nik']]);
            }

            $records[] = [
                'name' => $name,
                'sobat_id' => $sobatId,
                'email' => $email,
                'phone' => $phone,
                'district' => $district,
                'village' => $village,
                'address' => $address,
                'gender' => $gender,
                'birth_info' => $birthInfo,
                'education' => $education,
                'position' => $position,
                'nik' => $nik,
            ];
        }

        return $records;
    }
}

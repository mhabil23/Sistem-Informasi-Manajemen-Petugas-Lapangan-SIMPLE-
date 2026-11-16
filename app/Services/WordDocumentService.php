<?php

namespace App\Services;

use ZipArchive;
use DOMDocument;
use DOMXPath;
use Exception;

class WordDocumentService
{
    /**
     * Ekstrak teks, header, footer, dan tag dinamis dari file Microsoft Word (.docx).
     *
     * @param string $filePath
     * @return array
     */
    public function extractFromDocx(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [
                'success' => false,
                'message' => 'Berkas Word tidak ditemukan.',
                'content' => '',
                'header' => '',
                'footer' => '',
                'title' => '',
                'tags' => [],
            ];
        }

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [
                'success' => false,
                'message' => 'Gagal membuka berkas Word. Pastikan berkas berformat .docx yang valid.',
                'content' => '',
                'header' => '',
                'footer' => '',
                'title' => '',
                'tags' => [],
            ];
        }

        // 1. Ekstrak teks isi dokumen utama (word/document.xml)
        $documentXml = $zip->getFromName('word/document.xml');
        if (!$documentXml) {
            $zip->close();
            return [
                'success' => false,
                'message' => 'Format berkas .docx tidak memiliki struktur dokumen standar (word/document.xml).',
                'content' => '',
                'header' => '',
                'footer' => '',
                'title' => '',
                'tags' => [],
            ];
        }

        $content = $this->parseXmlToText($documentXml);

        // 2. Coba ekstrak header (word/header1.xml atau header2.xml)
        $headerText = '';
        for ($i = 1; $i <= 3; $i++) {
            $headerXml = $zip->getFromName("word/header{$i}.xml");
            if ($headerXml) {
                $parsedHeader = $this->parseXmlToText($headerXml);
                if (!empty($parsedHeader)) {
                    $headerText = $parsedHeader;
                    break;
                }
            }
        }

        // 3. Coba ekstrak footer (word/footer1.xml atau footer2.xml)
        $footerText = '';
        for ($i = 1; $i <= 3; $i++) {
            $footerXml = $zip->getFromName("word/footer{$i}.xml");
            if ($footerXml) {
                $parsedFooter = $this->parseXmlToText($footerXml);
                if (!empty($parsedFooter)) {
                    $footerText = $parsedFooter;
                    break;
                }
            }
        }

        $zip->close();

        // 4. Deteksi tag dinamis seperti {TANGGAL}, {NAMA}, {NIP}, dll.
        preg_match_all('/\{[A-Za-z0-9_\- ]+\}/', $content . ' ' . $headerText . ' ' . $footerText, $tagMatches);
        $tags = !empty($tagMatches[0]) ? array_values(array_unique($tagMatches[0])) : [];

        // 5. Coba tebak judul template dari baris pertama non-kosong
        $lines = explode("\n", trim($content));
        $firstLine = trim($lines[0] ?? '');
        $suggestedTitle = mb_substr($firstLine, 0, 100);

        return [
            'success' => true,
            'message' => 'Berkas Word berhasil diekstrak.',
            'content' => $content,
            'header' => trim($headerText),
            'footer' => trim($footerText),
            'title' => $suggestedTitle,
            'tags' => $tags,
            'lines_count' => count($lines),
        ];
    }

    /**
     * Parse XML OpenXML WordprocessingML menjadi teks bersih terformat.
     */
    protected function parseXmlToText(string $xmlContent): string
    {
        try {
            $dom = new DOMDocument();
            // Suppress warnings for malformed namespaces/attributes
            libxml_use_internal_errors(true);
            $dom->loadXML($xmlContent, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
            libxml_clear_errors();

            $xpath = new DOMXPath($dom);
            $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

            $paragraphs = [];
            // Ambil semua paragraf w:p
            $pNodes = $xpath->query('//w:p');

            if ($pNodes && $pNodes->length > 0) {
                foreach ($pNodes as $pNode) {
                    $pText = '';
                    // Ambil teks w:t atau break w:br di dalam paragraf ini
                    $tNodes = $xpath->query('.//w:t | .//w:br', $pNode);
                    foreach ($tNodes as $tNode) {
                        if ($tNode->nodeName === 'w:br') {
                            $pText .= "\n";
                        } else {
                            $pText .= $tNode->nodeValue;
                        }
                    }

                    $trimmed = trim($pText);
                    if ($trimmed !== '') {
                        $paragraphs[] = $trimmed;
                    }
                }
            }

            if (!empty($paragraphs)) {
                return implode("\n\n", $paragraphs);
            }

            // Fallback jika XPath tidak mengembalikan node (misal namespace berbeda)
            return $this->fallbackRegexTextExtraction($xmlContent);
        } catch (Exception $e) {
            return $this->fallbackRegexTextExtraction($xmlContent);
        }
    }

    /**
     * Fallback ekstraksi berbasis regex jika DOMDocument gagal.
     */
    protected function fallbackRegexTextExtraction(string $xml): string
    {
        $xml = preg_replace('/<w:br[^>]*\/>/i', "\n", $xml);
        $xml = preg_replace('/<\/w:p>/i', "\n\n", $xml);
        $xml = preg_replace('/<\/w:tr>/i', "\n", $xml);
        $xml = preg_replace('/<\/w:tc>/i', " | ", $xml);
        
        $text = strip_tags($xml);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }
}


<?php

namespace Database\Seeders;

use App\Models\DocumentTemplate;
use Illuminate\Database\Seeder;

class DocumentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'code' => 'bast_officer',
                'name' => 'BAST Hasil Pekerjaan Petugas Lapangan (BAST Terisi)',
                'type' => 'bast_officer',
                'header_text' => 'BERITA ACARA SERAH TERIMA HASIL PEKERJAAN PETUGAS PENDATAAN LAPANGAN',
                'footer_text' => 'Demikian Berita Acara ini dibuat dengan sebenarnya dalam rangkap 2 (dua) dan menjadi sah berlaku setelah ditandatangani oleh PARA PIHAK.',
                'content' => "<ol class=\"ol-pernyataan\">\n    <li><strong>PIHAK KEDUA</strong> telah melaksanakan pekerjaan {KEGIATAN} berdasarkan Perjanjian Kerja Petugas Pendataan Lapangan {KEGIATAN} Nomor {NOMOR_KONTRAK}, tanggal {TANGGAL_KONTRAK}.</li>\n    <li><strong>PIHAK KEDUA</strong> telah menyelesaikan pekerjaan {KEGIATAN} berdasarkan hasil pemeriksaan dan evaluasi pekerjaan sebagaimana tercantum dalam lampiran.</li>\n    <li>Berdasarkan angka 2 tersebut di atas, <strong>PIHAK KEDUA</strong> menyerahkan hasil pekerjaan {KEGIATAN} kepada <strong>PIHAK PERTAMA</strong>, dan <strong>PIHAK PERTAMA</strong> menerima hasil pekerjaan tersebut yang telah sesuai dengan seharusnya.</li>\n    <li>Hasil pekerjaan {KEGIATAN} sebagaimana dimaksud dalam angka 3 di atas, berupa data hasil {KEGIATAN}, sejumlah {JUMLAH_DOKUMEN}.</li>\n    <li>Untuk hasil pendataan lapangan sebagaimana dimaksud pada angka 4 yang memerlukan pemeriksaan lanjutan, akan dilakukan pengecekan, perubahan, dan/atau kunjungan kembali ke lapangan merujuk pada perjanjian yang ditandatangani oleh PARA PIHAK.</li>\n</ol>",
                'is_active' => true,
            ],
            [
                'code' => 'bast_blanko',
                'name' => 'BAST Penyerahan Blanko & Perlengkapan Lapangan',
                'type' => 'bast_blanko',
                'header_text' => 'TANDA TERIMA PENYERAHAN DOKUMEN DAN PERLENGKAPAN LAPANGAN',
                'footer_text' => 'Dibuat dan diserahkan dalam keadaan lengkap untuk dipergunakan sebagaimana mestinya.',
                'content' => '<p>Pada hari ini {TANGGAL}, telah diserahkan dokumen kuesioner dan kelengkapan lapangan untuk kegiatan {KEGIATAN} kepada petugas lapangan sebagaimana tercantum dalam daftar rincian penerima.</p>',
                'is_active' => true,
            ],
            [
                'code' => 'bast_respondent',
                'name' => 'Tanda Terima Responden Lapangan',
                'type' => 'bast_respondent',
                'header_text' => 'TANDA TERIMA RESPONDEN KEGIATAN SENSUS / SURVEI BPS',
                'footer_text' => 'Dokumen ini merupakan bukti sah pelaksanaan kunjungan pendataan lapangan oleh petugas BPS.',
                'content' => '<p>Format tanda terima penerimaan dokumen / souvenir dari petugas pendataan BPS kepada responden (KRT atau Anggota Rumah Tangga) di wilayah penugasan {LOKASI}.</p>',
                'is_active' => true,
            ],
            [
                'code' => 'officer_report',
                'name' => 'Laporan Perjalanan Dinas / Kegiatan Petugas',
                'type' => 'officer_report',
                'header_text' => 'LAPORAN PERJALANAN DINAS / PELAKSANAAN TUGAS LAPANGAN',
                'footer_text' => 'Demikian laporan perjalanan dinas ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.',
                'content' => '<p>Laporan pelaksanaan tugas perjalanan dinas dalam rangka pendataan lapangan, pengawasan, atau koordinasi kegiatan BPS sesuai Surat Tugas yang telah diterbitkan.</p>',
                'is_active' => true,
            ],
            [
                'code' => 'training_attendance',
                'name' => 'Daftar Hadir Pelatihan & Briefing Petugas',
                'type' => 'training_attendance',
                'header_text' => 'DAFTAR HADIR PESERTA PELATIHAN / BRIEFING PETUGAS',
                'footer_text' => 'Daftar hadir ini dibuat sebagai bukti fisik pertanggungjawaban kehadiran peserta kegiatan.',
                'content' => '<p>Daftar kehadiran peserta dan instruktur dalam rangka kegiatan pelatihan / briefing persiapan pelaksanaan survei dan sensus BPS di {LOKASI}.</p>',
                'is_active' => true,
            ],
        ];

        foreach ($templates as $tpl) {
            DocumentTemplate::updateOrCreate(
                ['code' => $tpl['code']],
                $tpl
            );
        }
    }
}

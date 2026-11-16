<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ImportMitraPetugasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:mitra-petugas 
                            {--file=database/data/mitra_bps.json : Path ke file JSON sumber data mitra}
                            {--password=bps7408 : Password default untuk akun petugas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import akun Petugas Lapangan dari data Mitra BPS (SOBAT ID)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = base_path($this->option('file'));
        $defaultPassword = $this->option('password');

        if (!file_exists($filePath)) {
            $this->error("File data mitra tidak ditemukan pada: {$filePath}");
            return Command::FAILURE;
        }

        $content = file_get_contents($filePath);
        $records = json_decode($content, true);

        if (!is_array($records) || empty($records)) {
            $this->error("Format JSON tidak valid atau data kosong.");
            return Command::FAILURE;
        }

        $this->info("Memulai import " . count($records) . " akun Petugas Lapangan...");
        $this->info("Password default: <comment>{$defaultPassword}</comment>");

        $hashedPassword = Hash::make($defaultPassword);
        $createdCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;

        $bar = $this->output->createProgressBar(count($records));
        $bar->start();

        foreach ($records as $item) {
            $sobatId = trim($item['sobat_id'] ?? '');
            $email = strtolower(trim($item['email'] ?? ''));
            $name = trim($item['name'] ?? '');

            if (empty($sobatId) || empty($name)) {
                $skippedCount++;
                $bar->advance();
                continue;
            }

            // Jika email kosong / tidak valid, generate email default @bps.go.id
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $cleanUsername = preg_replace('/[^a-z0-9]/', '', strtolower($name));
                $email = "mitra.{$sobatId}@bps.go.id";
            }

            // Normalisasi nomor telepon
            $phone = trim($item['phone'] ?? '');
            if (!empty($phone) && !str_starts_with($phone, '0') && !str_starts_with($phone, '+')) {
                $phone = '0' . $phone;
            }

            $userData = [
                'name' => $name,
                'username' => $sobatId,
                'email' => $email,
                'phone' => $phone ?: null,
                'nik' => $sobatId,
                'sobat_id' => $sobatId,
                'rank' => '-',
                'position' => !empty($item['posisi']) ? $item['posisi'] : 'Mitra Pendataan',
                'district' => $item['kecamatan'] ?? null,
                'village' => $item['desa'] ?? null,
                'address' => $item['alamat_detail'] ?? null,
                'gender' => $item['jenis_kelamin'] ?? null,
                'birth_info' => $item['ttl'] ?? null,
                'education' => $item['pendidikan'] ?? null,
                'role' => 'petugas',
                'status' => 'active',
            ];

            // Cek apakah akun dengan username (sobat_id) atau email sudah ada
            $existingUser = User::where('username', $sobatId)
                ->orWhere('sobat_id', $sobatId)
                ->orWhere('email', $email)
                ->first();

            if ($existingUser) {
                // Jangan timpa role jika user adalah admin / pegawai
                if ($existingUser->isAdmin() || $existingUser->isPegawai()) {
                    $skippedCount++;
                    $bar->advance();
                    continue;
                }

                $existingUser->update($userData);
                $updatedCount++;
            } else {
                $userData['password'] = $hashedPassword;
                User::create($userData);
                $createdCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Import Akun Petugas Lapangan Selesai!");
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Total Data di File', count($records)],
                ['Akun Baru Dibuat', $createdCount],
                ['Akun Diperbarui', $updatedCount],
                ['Dilewati (Admin/Pegawai/Tidak Lengkap)', $skippedCount],
                ['Password Default Akun', $defaultPassword],
            ]
        );

        // Tampilkan 5 contoh akun yang baru dibuat untuk verifikasi
        $sampleUsers = User::where('role', 'petugas')
            ->whereNotNull('sobat_id')
            ->latest('id')
            ->take(5)
            ->get(['name', 'username', 'email', 'district', 'village', 'phone']);

        if ($sampleUsers->isNotEmpty()) {
            $this->newLine();
            $this->info("Contoh Akun Petugas Terdaftar:");
            $this->table(
                ['Nama Lengkap', 'Username (SOBAT ID)', 'Email', 'Kecamatan', 'Desa/Kel', 'No. HP'],
                $sampleUsers->toArray()
            );
        }

        return Command::SUCCESS;
    }
}


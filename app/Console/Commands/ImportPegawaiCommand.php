<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ImportPegawaiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:pegawai 
                            {--file=database/data/pegawai_bps.json : Path ke file JSON database pegawai}
                            {--password=bps7408 : Password default untuk akun pegawai}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import akun Pegawai Organik BPS Kabupaten Kolaka Utara beserta hak Dual Role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = base_path($this->option('file'));
        $defaultPassword = $this->option('password');

        if (!file_exists($filePath)) {
            $this->error("Berkas database pegawai tidak ditemukan pada: {$filePath}");
            return Command::FAILURE;
        }

        $records = json_decode(file_get_contents($filePath), true);

        if (!is_array($records) || empty($records)) {
            $this->error("Format JSON tidak valid atau data pegawai kosong.");
            return Command::FAILURE;
        }

        $this->info("Memulai import " . count($records) . " akun Pegawai BPS Kolaka Utara...");
        $this->info("Password default: <comment>{$defaultPassword}</comment>");

        $hashedPassword = Hash::make($defaultPassword);
        $createdCount = 0;
        $updatedCount = 0;

        $bar = $this->output->createProgressBar(count($records));
        $bar->start();

        foreach ($records as $item) {
            $nip = trim($item['nip'] ?? '');
            $name = trim($item['name'] ?? '');
            $username = trim($item['username'] ?? '');
            $email = strtolower(trim($item['email'] ?? ''));

            if (empty($name) || empty($username)) {
                $bar->advance();
                continue;
            }

            $userData = [
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'nik' => $nip ?: null,
                'sobat_id' => !empty($item['sobat_id']) ? (string) $item['sobat_id'] : null,
                'rank' => $item['rank'] ?? '-',
                'position' => $item['position'] ?? 'Pegawai BPS',
                'gender' => $item['gender'] ?? null,
                'role' => 'pegawai',
                'can_switch_role' => (bool) ($item['can_switch_role'] ?? false),
                'status' => 'active',
            ];

            // Cek apakah user sudah ada berdasarkan username, NIP (nik), atau email
            $existingUser = User::where('username', $username)
                ->orWhere('email', $email)
                ->when($nip, function ($q) use ($nip) {
                    $q->orWhere('nik', $nip);
                })
                ->first();

            if ($existingUser) {
                // Pertahankan password jika user sudah pernah ganti password
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

        $this->info("Import Akun Pegawai Selesai!");
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Total Data Pegawai', count($records)],
                ['Akun Baru Dibuat', $createdCount],
                ['Akun Diperbarui', $updatedCount],
                ['Password Default Akun', $defaultPassword],
            ]
        );

        return Command::SUCCESS;
    }
}

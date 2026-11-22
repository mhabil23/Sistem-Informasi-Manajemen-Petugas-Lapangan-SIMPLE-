<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = database_path('data/pegawai_bps.json');
        if (!file_exists($filePath)) {
            return;
        }

        $records = json_decode(file_get_contents($filePath), true);
        if (!is_array($records)) {
            return;
        }

        $defaultPassword = Hash::make('bps7408');

        foreach ($records as $item) {
            $nip = trim($item['nip'] ?? '');
            $username = trim($item['username'] ?? '');
            $email = strtolower(trim($item['email'] ?? ''));

            $userData = [
                'name' => $item['name'],
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

            $user = User::where('username', $username)
                ->orWhere('email', $email)
                ->when($nip, function ($q) use ($nip) {
                    $q->orWhere('nik', $nip);
                })
                ->first();

            if ($user) {
                $user->update($userData);
            } else {
                $userData['password'] = $defaultPassword;
                User::create($userData);
            }
        }
    }
}

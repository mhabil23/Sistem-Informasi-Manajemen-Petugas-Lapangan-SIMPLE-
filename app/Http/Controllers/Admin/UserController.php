<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\GoogleSheetService;
use App\Services\SimpleExcelParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    protected GoogleSheetService $sheetService;
    protected SimpleExcelParser $excelParser;

    public function __construct(GoogleSheetService $sheetService, SimpleExcelParser $excelParser)
    {
        $this->sheetService = $sheetService;
        $this->excelParser = $excelParser;
    }

    /**
     * Display a listing of users with search, filter, and pagination.
     */
    public function index(Request $request)
    {
        // Auto-heal: Ensure migration is applied if can_switch_role column is missing
        if (!Schema::hasColumn('users', 'can_switch_role')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {
                // Ignore if migration fails
            }
        }

        // Auto-import 25 pegawai from DB_MRK JSON data if no pegawai accounts exist yet
        try {
            if (User::where('role', 'pegawai')->count() === 0) {
                Artisan::call('import:pegawai');
            }
        } catch (\Throwable $e) {
            // Ignore if import fails
        }

        $hasDualRoleCol = Schema::hasColumn('users', 'can_switch_role');

        $query = User::query();

        // 1. Search (Name, Username, Email, Phone, NIK, Sobat ID, Kecamatan, Desa)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('sobat_id', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%")
                  ->orWhere('village', 'like', "%{$search}%");
            });
        }

        // 2. Filter by Role
        if ($role = $request->input('role')) {
            if ($role === 'dual_role') {
                if ($hasDualRoleCol) {
                    $query->where('can_switch_role', true);
                } else {
                    $query->whereRaw('0 = 1');
                }
            } else {
                $query->where('role', $role);
            }
        }

        // 3. Filter by Status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // 4. Statistics Counters
        $totalUsers = User::count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalOfficers = User::where('role', 'petugas')->count();
        $totalPegawai = User::where('role', 'pegawai')->count();
        $totalDualRole = $hasDualRoleCol ? User::where('can_switch_role', true)->count() : 0;
        $totalInactive = User::where('status', 'inactive')->count();
        $allPegawai = User::where('role', 'pegawai')->orderBy('name')->get();

        // 5. Fetch Paginated Users
        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.users.index', compact(
            'users',
            'totalUsers',
            'totalAdmins',
            'totalOfficers',
            'totalPegawai',
            'totalDualRole',
            'totalInactive',
            'allPegawai'
        ));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'nik' => ['nullable', 'string', 'max:32'],
            'rank' => ['nullable', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:150'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', Rule::in(['admin', 'petugas', 'pegawai'])],
            'can_switch_role' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda minus, dan garis bawah.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'role.required' => 'Role pengguna wajib dipilih.',
            'status.required' => 'Status akun wajib dipilih.',
        ]);

        $userData = [
            'name' => $validated['name'],
            'username' => strtolower($validated['username']),
            'email' => strtolower($validated['email']),
            'phone' => $validated['phone'] ?? null,
            'nik' => $validated['nik'] ?? null,
            'rank' => $validated['rank'] ?? null,
            'position' => $validated['position'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
        ];

        if (Schema::hasColumn('users', 'can_switch_role')) {
            $userData['can_switch_role'] = $request->boolean('can_switch_role');
        }

        $user = User::create($userData);

        // Sync to Google Sheet if configured
        if ($this->sheetService->isConfigured()) {
            $this->sheetService->insert('users', [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'password' => $user->password,
                'role' => $user->role,
                'status' => $user->status,
                'created_at' => $user->created_at->toISOString(),
                'updated_at' => $user->updated_at->toISOString(),
            ]);
        }

        ActivityLog::log(
            'create',
            'users',
            "Menambahkan pengguna baru: {$user->name} ({$user->username}) sebagai " . ucfirst($user->role) . "."
        );

        return redirect()->route('admin.users.index')->with('success', "Pengguna {$user->name} berhasil ditambahkan!");
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'nik' => ['nullable', 'string', 'max:32'],
            'rank' => ['nullable', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:150'],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', Rule::in(['admin', 'petugas', 'pegawai'])],
            'can_switch_role' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'password.min' => 'Password baru minimal 6 karakter.',
        ]);

        // Protect Master Admin from modification by any other admin / admin petugas
        if ($user->isMasterAdmin()) {
            if (!Auth::user()->isMasterAdmin()) {
                return back()->with('error', 'Akun Administrator SIMPLE (Master Admin) dilindungi dan tidak dapat diubah oleh akun admin lain!');
            }

            // Even if Master Admin updates their own account, protect their role & status
            if ($validated['role'] !== 'admin') {
                return back()->with('error', 'Role Master Admin dilindungi permanen sebagai Administrator!');
            }

            if ($validated['status'] === 'inactive') {
                return back()->with('error', 'Akun Master Admin tidak dapat dinonaktifkan!');
            }
        }

        // Prevent admin from deactivating their own account
        if ($user->id === Auth::id() && $validated['status'] === 'inactive') {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri!');
        }

        // Prevent admin from changing their own role
        if ($user->id === Auth::id() && $validated['role'] !== 'admin') {
            return back()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri!');
        }

        $updateData = [
            'name' => $validated['name'],
            'username' => strtolower($validated['username']),
            'email' => strtolower($validated['email']),
            'phone' => $validated['phone'] ?? null,
            'nik' => $validated['nik'] ?? null,
            'rank' => $validated['rank'] ?? null,
            'position' => $validated['position'] ?? null,
            'role' => $validated['role'],
            'status' => $validated['status'],
        ];

        if (Schema::hasColumn('users', 'can_switch_role')) {
            $updateData['can_switch_role'] = $request->boolean('can_switch_role');
        }

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Sync to Google Sheet if configured
        if ($this->sheetService->isConfigured()) {
            $this->sheetService->update('users', $user->id, [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'role' => $user->role,
                'status' => $user->status,
                'updated_at' => now()->toISOString(),
            ]);
        }

        ActivityLog::log(
            'update',
            'users',
            "Memperbarui data pengguna: {$user->name} ({$user->username})."
        );

        return redirect()->route('admin.users.index')->with('success', "Data pengguna {$user->name} berhasil diperbarui!");
    }

    /**
     * Remove the specified user from storage (Soft Delete).
     */
    public function destroy(User $user)
    {
        if ($user->isMasterAdmin()) {
            return back()->with('error', 'Akun Administrator SIMPLE (Master Admin) dilindungi permanen dan tidak dapat dihapus!');
        }

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif!');
        }

        $userName = $user->name;
        $userId = $user->id;
        $user->delete();

        // Sync delete to Google Sheet if configured
        if ($this->sheetService->isConfigured()) {
            $this->sheetService->delete('users', $userId, true);
        }

        ActivityLog::log(
            'delete',
            'users',
            "Menghapus pengguna: {$userName}."
        );

        return redirect()->route('admin.users.index')->with('success', "Pengguna {$userName} berhasil dihapus.");
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(User $user)
    {
        if ($user->isMasterAdmin()) {
            return back()->with('error', 'Akun Administrator SIMPLE (Master Admin) dilindungi dan tidak dapat dinonaktifkan!');
        }

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat mengubah status akun Anda sendiri!');
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->update('users', $user->id, [
                'status' => $newStatus,
                'updated_at' => now()->toISOString(),
            ]);
        }

        $statusLabel = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::log(
            'update',
            'users',
            "Mengubah status pengguna {$user->name} menjadi {$newStatus}."
        );

        return back()->with('success', "Akun {$user->name} berhasil {$statusLabel}!");
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user)
    {
        if ($user->isMasterAdmin() && !Auth::user()->isMasterAdmin()) {
            return back()->with('error', 'Hanya Master Admin yang berhak mereset password akun Master Admin!');
        }

        $request->validate([
            'new_password' => ['required', 'string', 'min:6'],
        ], [
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
        ]);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->update('users', $user->id, [
                'password' => $user->password,
                'updated_at' => now()->toISOString(),
            ]);
        }

        ActivityLog::log(
            'update',
            'users',
            "Mereset password pengguna {$user->name} ({$user->username})."
        );

        return back()->with('success', "Password untuk akun {$user->name} berhasil direset!");
    }

    /**
     * Toggle dual role permission for an officer/pegawai.
     */
    public function toggleDualRole(User $user)
    {
        if ($user->isMasterAdmin()) {
            return back()->with('error', 'Akun Administrator SIMPLE (Master Admin) memiliki hak akses Administrator penuh permanen!');
        }

        if (!Schema::hasColumn('users', 'can_switch_role')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        if (Schema::hasColumn('users', 'can_switch_role')) {
            $newVal = !$user->can_switch_role;
            $user->update(['can_switch_role' => $newVal]);
        } else {
            $newVal = false;
        }

        $statusText = $newVal ? 'diaktifkan (dapat beralih jadi Administrator)' : 'dinonaktifkan (akses admin ditutup)';

        ActivityLog::log(
            'update',
            'users',
            "Akses Dual Role untuk {$user->name} {$statusText}."
        );

        return back()->with('success', "Akses Dual Role untuk {$user->name} berhasil {$statusText}!");
    }

    /**
     * Explicitly revoke/close administrator access for an employee or user.
     */
    public function revokeAdmin(User $user)
    {
        if ($user->isMasterAdmin() || $user->username === 'admin') {
            return back()->with('error', 'Akun Administrator SIMPLE (Master Admin) dilindungi permanen. Hak akses Administrator tidak dapat dicabut!');
        }

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menutup akses Administrator untuk akun Anda sendiri!');
        }

        if (!Schema::hasColumn('users', 'can_switch_role')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        // Revoke admin access: if role was admin, change to pegawai; turn off can_switch_role
        $updatePayload = [
            'role' => $user->role === 'admin' ? 'pegawai' : $user->role,
        ];
        if (Schema::hasColumn('users', 'can_switch_role')) {
            $updatePayload['can_switch_role'] = false;
        }

        $user->update($updatePayload);

        ActivityLog::log(
            'update',
            'users',
            "Menutup akses Administrator untuk pengguna {$user->name} ({$user->username})."
        );

        return back()->with('success', "Akses Administrator untuk {$user->name} telah ditutup! Pengguna kini berstatus Pegawai biasa tanpa hak akses Admin.");
    }

    /**
     * Explicitly grant administrator (dual role) access to an employee.
     */
    public function grantAdmin(User $user)
    {
        if (!Schema::hasColumn('users', 'can_switch_role')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        if (Schema::hasColumn('users', 'can_switch_role')) {
            $user->update(['can_switch_role' => true]);
        }

        ActivityLog::log(
            'update',
            'users',
            "Membuka hak akses Administrator (Dual Role) untuk {$user->name} ({$user->username})."
        );

        return back()->with('success', "Akses Administrator (Dual Role) untuk {$user->name} berhasil dibuka!");
    }

    /**
     * Revoke all dual-role admin access for all pegawai at once.
     */
    public function revokeAllPegawaiAdmin(Request $request)
    {
        if (!Schema::hasColumn('users', 'can_switch_role')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        $count = 0;
        if (Schema::hasColumn('users', 'can_switch_role')) {
            $count = User::where('role', 'pegawai')->where('can_switch_role', true)->count();
            User::where('role', 'pegawai')->where('can_switch_role', true)->update(['can_switch_role' => false]);
        }

        ActivityLog::log(
            'update',
            'users',
            "Menutup seluruh akses Administrator untuk {$count} pegawai secara serentak."
        );

        return back()->with('success', "Akses Administrator untuk seluruh pegawai ({$count} akun) berhasil ditutup serentak!");
    }

    /**
     * Re-sync / Upsert all 25 Pegawai BPS Kolaka Utara accounts from database/data/pegawai_bps.json.
     */
    public function syncPegawai()
    {
        try {
            if (!Schema::hasColumn('users', 'can_switch_role')) {
                Artisan::call('migrate', ['--force' => true]);
            }

            Artisan::call('import:pegawai');

            ActivityLog::log(
                'sync',
                'users',
                "Menyinkronkan dan memperbarui 25 akun Pegawai BPS Kolaka Utara dari database DB_MRK."
            );

            return back()->with('success', '25 Akun Pegawai BPS Kabupaten Kolaka Utara berhasil disinkronkan dan siap digunakan!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menyinkronkan data pegawai: ' . $e->getMessage());
        }
    }

    /**
     * Aksi massal (pilih lebih dari satu pengguna via checkbox tabel).
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', Rule::in(['grant_admin', 'revoke_admin', 'activate', 'deactivate', 'delete'])],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ], [
            'user_ids.required' => 'Pilih minimal satu pengguna untuk melakukan aksi massal.',
            'user_ids.min' => 'Pilih minimal satu pengguna untuk melakukan aksi massal.',
        ]);

        $action = $validated['action'];
        $userIds = array_unique($validated['user_ids']);
        $authId = Auth::id();

        $users = User::whereIn('id', $userIds)->get();
        $affectedCount = 0;
        $skippedMasterCount = 0;

        foreach ($users as $u) {
            // Proteksi Master Admin
            if ($u->isMasterAdmin() && in_array($action, ['revoke_admin', 'deactivate', 'delete'], true)) {
                $skippedMasterCount++;
                continue;
            }

            // Proteksi akun sendiri untuk delete & deactivate
            if ($u->id === $authId && in_array($action, ['deactivate', 'delete'], true)) {
                continue;
            }

            switch ($action) {
                case 'grant_admin':
                    $u->update(['can_switch_role' => true]);
                    $affectedCount++;
                    break;

                case 'revoke_admin':
                    $update = [];
                    if ($u->role === 'admin') {
                        $update['role'] = 'pegawai';
                    }
                    $update['can_switch_role'] = false;
                    $u->update($update);
                    $affectedCount++;
                    break;

                case 'activate':
                    $u->update(['status' => 'active']);
                    $affectedCount++;
                    break;

                case 'deactivate':
                    $u->update(['status' => 'inactive']);
                    $affectedCount++;
                    break;

                case 'delete':
                    $u->delete();
                    $affectedCount++;
                    break;
            }
        }

        $actionLabels = [
            'grant_admin' => 'diberikan hak akses Administrator (Dual Role)',
            'revoke_admin' => 'dicabut hak akses Administrator-nya',
            'activate' => 'diaktifkan',
            'deactivate' => 'dinonaktifkan',
            'delete' => 'dihapus',
        ];

        $msg = "Sebanyak {$affectedCount} pengguna berhasil {$actionLabels[$action]}.";
        if ($skippedMasterCount > 0) {
            $msg .= " Akun Master Admin dilindungi dan dilewati.";
        }

        ActivityLog::log(
            'bulk_update',
            'users',
            "Melakukan aksi massal '{$action}' pada {$affectedCount} pengguna."
        );

        return back()->with('success', $msg);
    }

    /**
     * Perbarui hak akses Administrator (Dual Role) untuk banyak pegawai sekaligus melalui checklist modal.
     */
    public function updateMultiplePegawaiAdmin(Request $request)
    {
        $selectedPegawaiIds = $request->input('pegawai_ids', []);
        if (!is_array($selectedPegawaiIds)) {
            $selectedPegawaiIds = [];
        }

        // Ambil seluruh pegawai
        $pegawaiList = User::where('role', 'pegawai')->get();
        $grantedCount = 0;
        $revokedCount = 0;

        foreach ($pegawaiList as $pegawai) {
            $shouldHaveAdmin = in_array($pegawai->id, $selectedPegawaiIds);
            if ($shouldHaveAdmin) {
                if (!$pegawai->can_switch_role) {
                    $pegawai->update(['can_switch_role' => true]);
                }
                $grantedCount++;
            } else {
                if ($pegawai->can_switch_role) {
                    $pegawai->update(['can_switch_role' => false]);
                    $revokedCount++;
                }
            }
        }

        ActivityLog::log(
            'bulk_update',
            'users',
            "Memperbarui hak akses admin pegawai massal: {$grantedCount} aktif dual role, {$revokedCount} dicabut."
        );

        return back()->with('success', "Hak akses Administrator pegawai berhasil diperbarui! ({$grantedCount} pegawai aktif Dual Role).");
    }

    /**
     * Pratinjau dan ekstrak data mitra lapangan dari berkas Excel/CSV via AJAX.
     * Mengembalikan daftar mitra yang terbaca beserta status deteksi (Akun Baru / Akun Terdaftar).
     */
    public function previewMitra(Request $request)
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'extensions:xlsx,xls,csv,txt', 'max:20480'],
        ], [
            'excel_file.required' => 'Pilih berkas Excel (.xlsx) atau CSV terlebih dahulu.',
            'excel_file.extensions' => 'Format berkas harus berupa .xlsx, .xls, atau .csv.',
            'excel_file.max' => 'Ukuran berkas maksimal 20 MB.',
        ]);

        $file = $request->file('excel_file');
        $ext = $file->getClientOriginalExtension();

        try {
            $parsedMitra = $this->excelParser->parseMitraRows($file->getRealPath(), $ext);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca berkas Excel: ' . $e->getMessage(),
            ], 422);
        }

        if (empty($parsedMitra)) {
            return response()->json([
                'success' => false,
                'message' => 'Berkas Excel kosong atau tidak ada data mitra yang valid ditemukan.',
            ], 422);
        }

        // Cache existing users in memory for instant matching
        $existingBySobat = User::whereNotNull('sobat_id')
            ->where('sobat_id', '!=', '')
            ->get()
            ->keyBy('sobat_id');

        $existingByEmail = User::whereNotNull('email')
            ->where('email', '!=', '')
            ->get()
            ->keyBy(fn ($u) => strtolower($u->email));

        $previewList = [];
        $newCount = 0;
        $existingCount = 0;

        foreach ($parsedMitra as $idx => $row) {
            $sobatId = $row['sobat_id'] ?: null;
            $email = $row['email'] ?: null;

            $matchedUser = null;
            if ($sobatId && isset($existingBySobat[$sobatId])) {
                $matchedUser = $existingBySobat[$sobatId];
            } elseif ($email && isset($existingByEmail[strtolower($email)])) {
                $matchedUser = $existingByEmail[strtolower($email)];
            }

            $isExisting = $matchedUser !== null;
            if ($isExisting) {
                $existingCount++;
            } else {
                $newCount++;
            }

            $previewList[] = [
                'index' => $idx,
                'name' => $row['name'],
                'sobat_id' => $row['sobat_id'] ?: '',
                'nik' => $row['nik'] ?: '',
                'email' => $row['email'] ?: '',
                'phone' => $row['phone'] ?: '',
                'district' => $row['district'] ?: '',
                'village' => $row['village'] ?: '',
                'address' => $row['address'] ?: '',
                'gender' => $row['gender'] ?: '',
                'birth_info' => $row['birth_info'] ?: '',
                'education' => $row['education'] ?: '',
                'position' => $row['position'] ?: 'Mitra Pendataan',
                'is_existing' => $isExisting,
                'existing_id' => $matchedUser?->id,
                'existing_name' => $matchedUser?->name,
                'existing_username' => $matchedUser?->username,
            ];
        }

        return response()->json([
            'success' => true,
            'count' => count($previewList),
            'new_count' => $newCount,
            'existing_count' => $existingCount,
            'mitra' => $previewList,
        ]);
    }

    /**
     * Import daftar mitra lapangan dari berkas Excel (.xlsx / .csv) atau daftar terpilih.
     * Password bawaan: bps7408, login dapat menggunakan ID SOBAT atau Email.
     */
    public function importMitra(Request $request)
    {
        $parsedMitra = [];

        // 1. Cek apakah ada data mitra terpilih (dari checklist pratinjau modal)
        $selectedJson = $request->input('selected_mitra_json');
        if (!empty($selectedJson)) {
            $decoded = json_decode($selectedJson, true);
            if (is_array($decoded) && !empty($decoded)) {
                $parsedMitra = $decoded;
            }
        }

        // 2. Fallback: jika belum ada seleksi, baca langsung dari berkas excel_file
        if (empty($parsedMitra)) {
            $request->validate([
                'excel_file' => ['required', 'file', 'extensions:xlsx,xls,csv,txt', 'max:20480'],
            ], [
                'excel_file.required' => 'Pilih berkas Excel (.xlsx) atau CSV daftar mitra yang akan diunggah.',
                'excel_file.extensions' => 'Format berkas harus berupa .xlsx, .xls, atau .csv.',
                'excel_file.max' => 'Ukuran berkas maksimal adalah 20 MB.',
            ]);

            $file = $request->file('excel_file');
            $ext = $file->getClientOriginalExtension();

            try {
                $parsedMitra = $this->excelParser->parseMitraRows($file->getRealPath(), $ext);
            } catch (\Throwable $e) {
                return back()->with('error', 'Gagal memproses berkas Excel: ' . $e->getMessage());
            }
        }

        if (empty($parsedMitra)) {
            return back()->with('error', 'Tidak ada data mitra yang dipilih atau valid untuk diimpor.');
        }

        // Cache existing users in memory for instant matching without N+1 queries
        $existingBySobat = User::whereNotNull('sobat_id')
            ->where('sobat_id', '!=', '')
            ->get()
            ->keyBy('sobat_id');

        $existingByEmail = User::whereNotNull('email')
            ->where('email', '!=', '')
            ->get()
            ->keyBy(fn ($u) => strtolower($u->email));

        $existingByUsername = User::all()
            ->keyBy(fn ($u) => strtolower($u->username));

        $createdCount = 0;
        $updatedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($parsedMitra as $row) {
                $name = $row['name'];
                $sobatId = $row['sobat_id'] ?: null;
                $email = $row['email'] ?: null;
                $phone = $row['phone'] ?: null;
                $district = $row['district'] ?: null;
                $village = $row['village'] ?: null;
                $address = $row['address'] ?: null;
                $gender = $row['gender'] ?: null;
                $birthInfo = $row['birth_info'] ?: null;
                $education = $row['education'] ?: null;
                $position = $row['position'] ?: 'Mitra Pendataan';
                $nik = $row['nik'] ?: null;

                // 1. Check existing user by SOBAT ID or Email
                $user = null;
                if ($sobatId && isset($existingBySobat[$sobatId])) {
                    $user = $existingBySobat[$sobatId];
                } elseif ($email && isset($existingByEmail[strtolower($email)])) {
                    $user = $existingByEmail[strtolower($email)];
                }

                if ($user) {
                    // Update existing mitra
                    $updateData = [
                        'name' => $name,
                        'phone' => $phone ?: $user->phone,
                        'district' => $district ?: $user->district,
                        'village' => $village ?: $user->village,
                        'address' => $address ?: $user->address,
                        'gender' => $gender ?: $user->gender,
                        'birth_info' => $birthInfo ?: $user->birth_info,
                        'education' => $education ?: $user->education,
                        'position' => $position ?: ($user->position ?: 'Mitra Pendataan'),
                        'status' => 'active',
                    ];

                    if ($sobatId && empty($user->sobat_id)) {
                        $updateData['sobat_id'] = $sobatId;
                        $existingBySobat[$sobatId] = $user;
                    }
                    if ($nik && empty($user->nik)) {
                        $updateData['nik'] = $nik;
                    }

                    $user->update($updateData);
                    $updatedCount++;
                } else {
                    // Create new mitra account
                    // Username priority: sobat_id if unique, else slug of name
                    $username = null;
                    if ($sobatId && !isset($existingByUsername[strtolower($sobatId)])) {
                        $username = $sobatId;
                    } else {
                        $baseSlug = Str::slug($name, '');
                        if (empty($baseSlug)) {
                            $baseSlug = 'mitra' . rand(100, 999);
                        }
                        $cleanUsername = $baseSlug;
                        $uIdx = 1;
                        while (isset($existingByUsername[strtolower($cleanUsername)])) {
                            $cleanUsername = $baseSlug . $uIdx++;
                        }
                        $username = $cleanUsername;
                    }

                    // Email priority: valid excel email if not taken, else fallback
                    $userEmail = null;
                    if ($email && !isset($existingByEmail[strtolower($email)])) {
                        $userEmail = $email;
                    } else {
                        $baseEmail = $sobatId ? "sobat_{$sobatId}@bps.go.id" : Str::slug($name, '.') . '@bps.go.id';
                        $cleanEmail = $baseEmail;
                        $eIdx = 1;
                        while (isset($existingByEmail[strtolower($cleanEmail)])) {
                            $cleanEmail = Str::slug($name, '.') . $eIdx++ . '@bps.go.id';
                        }
                        $userEmail = $cleanEmail;
                    }

                    $newUser = User::create([
                        'name' => $name,
                        'username' => $username,
                        'email' => $userEmail,
                        'password' => Hash::make('bps7408'),
                        'sobat_id' => $sobatId,
                        'phone' => $phone,
                        'nik' => $nik,
                        'role' => 'petugas',
                        'status' => 'active',
                        'district' => $district,
                        'village' => $village,
                        'address' => $address,
                        'gender' => $gender,
                        'birth_info' => $birthInfo,
                        'education' => $education,
                        'position' => $position,
                    ]);

                    if ($newUser->sobat_id) {
                        $existingBySobat[$newUser->sobat_id] = $newUser;
                    }
                    $existingByEmail[strtolower($newUser->email)] = $newUser;
                    $existingByUsername[strtolower($newUser->username)] = $newUser;
                    $createdCount++;
                }
            }

            DB::commit();

            ActivityLog::log(
                'import',
                'users',
                "Mengimpor {$createdCount} akun mitra baru dan memperbarui {$updatedCount} akun mitra dari berkas Excel/CSV."
            );

            return back()->with('success', "Import berhasil! Sebanyak {$createdCount} akun mitra baru berhasil dibuat dan {$updatedCount} akun diperbarui. Seluruh akun dapat login dengan ID SOBAT atau Email dan password bawaan: bps7408.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data mitra: ' . $e->getMessage());
        }
    }

    /**
     * Download format template spreadsheet (.csv) untuk import daftar mitra.
     */
    public function downloadMitraTemplate(Request $request)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=Template_Daftar_Mitra_SIMPEL.csv',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens properly
            fwrite($handle, "\xEF\xBB\xBF");

            // Header columns
            fputcsv($handle, [
                'SOBAT ID',
                'Nama Lengkap',
                'Email',
                'No Telp',
                'Posisi',
                'Alamat Kecamatan',
                'Alamat Desa/Kel',
                'Alamat Detail',
                'Jenis Kelamin',
                'Tempat, Tanggal Lahir',
                'Pendidikan',
            ]);

            // Sample rows
            fputcsv($handle, [
                '740822020003',
                'Ahmad Fauzi',
                'ahmadfauziliwang@gmail.com',
                '082188167081',
                'Mitra Pendataan',
                'Lasusua',
                'Lasusua',
                'Dusun 2 Desa Babussalam',
                'Laki-Laki',
                'JENEPONTO, 23 Januari 1993',
                'Tamat SMA/Sederajat',
            ]);

            fputcsv($handle, [
                '740822020017',
                'Marjainah',
                'marjaina@gmail.com',
                '082393703204',
                'Mitra Pendataan',
                'Watunohu',
                'Watunohu',
                'Dusun 1 Desa Watunohu',
                'Perempuan',
                'KOLAKA UTARA, 15 Oktober 1990',
                'Tamat SMA/Sederajat',
            ]);

            fclose($handle);
        }, 200, $headers);
    }
}


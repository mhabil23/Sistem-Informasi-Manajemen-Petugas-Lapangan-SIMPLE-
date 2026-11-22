<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\GoogleSheetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    protected GoogleSheetService $sheetService;

    public function __construct(GoogleSheetService $sheetService)
    {
        $this->sheetService = $sheetService;
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
}


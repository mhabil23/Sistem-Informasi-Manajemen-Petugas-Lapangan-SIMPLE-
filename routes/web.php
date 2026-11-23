<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Root redirect
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->canAccessMonitoring()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('petugas.dashboard');
    }
    return redirect()->route('login');
});

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/switch-role', [\App\Http\Controllers\RoleSwitchController::class, 'switch'])->name('role.switch');

    // Profile & Password settings (accessible to both roles)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Penyajian berkas dokumentasi & tanda tangan dari public storage secara aman
    Route::get('/media/{path}', [\App\Http\Controllers\StorageFileController::class, 'show'])
        ->where('path', '.*')
        ->name('media.file');
});

// Admin Area (Manajemen Sistem & Penetapan: khusus role:admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->as('admin.')->group(function () {
    // Google Spreadsheet Database Engine Settings
    Route::get('/spreadsheet', [\App\Http\Controllers\Admin\SpreadsheetController::class, 'index'])->name('spreadsheet.index');
    Route::put('/spreadsheet', [\App\Http\Controllers\Admin\SpreadsheetController::class, 'update'])->name('spreadsheet.update');
    Route::post('/spreadsheet/test', [\App\Http\Controllers\Admin\SpreadsheetController::class, 'testConnection'])->name('spreadsheet.test');
    Route::post('/spreadsheet/init', [\App\Http\Controllers\Admin\SpreadsheetController::class, 'initSheets'])->name('spreadsheet.init');

    // Manajemen Pengguna
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::post('/users/import-mitra', [\App\Http\Controllers\Admin\UserController::class, 'importMitra'])->name('users.import-mitra');
    Route::post('/users/preview-mitra', [\App\Http\Controllers\Admin\UserController::class, 'previewMitra'])->name('users.preview-mitra');
    Route::get('/users/template-mitra', [\App\Http\Controllers\Admin\UserController::class, 'downloadMitraTemplate'])->name('users.template-mitra');
    Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::patch('/users/{user}/toggle-dual-role', [\App\Http\Controllers\Admin\UserController::class, 'toggleDualRole'])->name('users.toggle-dual-role');
    Route::patch('/users/{user}/revoke-admin', [\App\Http\Controllers\Admin\UserController::class, 'revokeAdmin'])->name('users.revoke-admin');
    Route::patch('/users/{user}/grant-admin', [\App\Http\Controllers\Admin\UserController::class, 'grantAdmin'])->name('users.grant-admin');
    Route::post('/users/revoke-all-pegawai-admin', [\App\Http\Controllers\Admin\UserController::class, 'revokeAllPegawaiAdmin'])->name('users.revoke-all-pegawai-admin');
    Route::post('/users/bulk-action', [\App\Http\Controllers\Admin\UserController::class, 'bulkAction'])->name('users.bulk-action');
    Route::post('/users/update-multiple-pegawai-admin', [\App\Http\Controllers\Admin\UserController::class, 'updateMultiplePegawaiAdmin'])->name('users.update-multiple-pegawai-admin');
    Route::post('/users/sync-pegawai', [\App\Http\Controllers\Admin\UserController::class, 'syncPegawai'])->name('users.sync-pegawai');
    Route::patch('/users/{user}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('users.reset-password');

    // Aksi Penugasan (CUD Admin)
    Route::get('/assignments/template/download', [\App\Http\Controllers\Admin\AssignmentController::class, 'downloadTemplate'])->name('assignments.template.download');
    Route::post('/assignments/preview-excel', [\App\Http\Controllers\Admin\AssignmentController::class, 'previewExcel'])->name('assignments.preview-excel');
    Route::post('/assignments/import', [\App\Http\Controllers\Admin\AssignmentController::class, 'importExcel'])->name('assignments.import');
    Route::post('/assignments', [\App\Http\Controllers\Admin\AssignmentController::class, 'store'])->name('assignments.store');
    Route::put('/assignments/{assignment}', [\App\Http\Controllers\Admin\AssignmentController::class, 'update'])->name('assignments.update');
    Route::delete('/assignments/{assignment}', [\App\Http\Controllers\Admin\AssignmentController::class, 'destroy'])->name('assignments.destroy');
    Route::post('/assignments/bulk-action', [\App\Http\Controllers\Admin\AssignmentController::class, 'bulkAction'])->name('assignments.bulk-action');
    Route::post('/assignments/{assignment}/officers', [\App\Http\Controllers\Admin\AssignmentController::class, 'assignOfficer'])->name('assignments.officers.assign');
    Route::put('/assignments/{assignment}/officers/{assignmentOfficer}', [\App\Http\Controllers\Admin\AssignmentController::class, 'updateOfficerTask'])->name('assignments.officers.update');
    Route::delete('/assignments/{assignment}/officers/{assignmentOfficer}', [\App\Http\Controllers\Admin\AssignmentController::class, 'removeOfficer'])->name('assignments.officers.remove');

    // Aksi Jadwal Kegiatan (CUD Admin)
    Route::get('/schedules/template/download', [\App\Http\Controllers\Admin\ScheduleController::class, 'downloadTemplate'])->name('schedules.template.download');
    Route::post('/schedules/preview-excel', [\App\Http\Controllers\Admin\ScheduleController::class, 'previewExcel'])->name('schedules.preview-excel');
    Route::post('/schedules/import-excel', [\App\Http\Controllers\Admin\ScheduleController::class, 'importExcel'])->name('schedules.import-excel');
    Route::post('/schedules', [\App\Http\Controllers\Admin\ScheduleController::class, 'store'])->name('schedules.store');
    Route::put('/schedules/{schedule}', [\App\Http\Controllers\Admin\ScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/schedules/{schedule}', [\App\Http\Controllers\Admin\ScheduleController::class, 'destroy'])->name('schedules.destroy');

    // Aksi Data Responden (CUD Admin)
    Route::post('/respondents', [\App\Http\Controllers\Admin\RespondentController::class, 'store'])->name('respondents.store');
    Route::put('/respondents/{respondent}', [\App\Http\Controllers\Admin\RespondentController::class, 'update'])->name('respondents.update');
    Route::delete('/respondents/{respondent}', [\App\Http\Controllers\Admin\RespondentController::class, 'destroy'])->name('respondents.destroy');

    // Aksi BAST Responden (Create & Delete Admin)
    Route::post('/basts/respondents', [\App\Http\Controllers\Admin\RespondentBastController::class, 'store'])->name('basts.respondents.store');
    Route::delete('/basts/respondents/{bast}', [\App\Http\Controllers\Admin\RespondentBastController::class, 'destroy'])->name('basts.respondents.destroy');

    // Aksi BAST Petugas (Create, Update & Delete Admin)
    Route::get('/basts/officers/template/download', [\App\Http\Controllers\Admin\OfficerBastController::class, 'downloadTemplate'])->name('basts.officers.template.download');
    Route::post('/basts/officers/parse-excel', [\App\Http\Controllers\Admin\OfficerBastController::class, 'parseExcelRecipients'])->name('basts.officers.parse-recipients-excel');
    Route::post('/basts/officers', [\App\Http\Controllers\Admin\OfficerBastController::class, 'store'])->name('basts.officers.store');
    Route::put('/basts/officers/{bast}', [\App\Http\Controllers\Admin\OfficerBastController::class, 'update'])->name('basts.officers.update');
    Route::delete('/basts/officers/{bast}', [\App\Http\Controllers\Admin\OfficerBastController::class, 'destroy'])->name('basts.officers.destroy');
    Route::post('/basts/officers/bulk-action', [\App\Http\Controllers\Admin\OfficerBastController::class, 'bulkAction'])->name('basts.officers.bulk-action');

    // Aksi Laporan Petugas & ACC/Basis Pelaksanaan (Admin)
    Route::put('/reports/basis-settings', [\App\Http\Controllers\Admin\ReportController::class, 'updateBasisSettings'])->name('reports.basis-settings');
    Route::put('/reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'update'])->name('reports.update');
    Route::patch('/reports/{report}/approve', [\App\Http\Controllers\Admin\ReportController::class, 'approve'])->name('reports.approve');
    Route::put('/reports/{report}/review', [\App\Http\Controllers\Admin\ReportController::class, 'review'])->name('reports.review');
    Route::delete('/reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'destroy'])->name('reports.destroy');
    Route::post('/reports/bulk-action', [\App\Http\Controllers\Admin\ReportController::class, 'bulkAction'])->name('reports.bulk-action');

    // Notifikasi Admin (Laporan Perjadin Baru)
    Route::get('/notifications/poll', [\App\Http\Controllers\Admin\NotificationController::class, 'poll'])->name('notifications.poll');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::patch('/notifications/{notification}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Daftar Hadir Kegiatan & Pelatihan (CUD Admin)
    Route::get('/trainings/template/download', [\App\Http\Controllers\Admin\TrainingController::class, 'downloadTemplate'])->name('trainings.template.download');
    Route::post('/trainings/parse-excel', [\App\Http\Controllers\Admin\TrainingController::class, 'parseExcelParticipants'])->name('trainings.parse-excel');
    Route::post('/trainings', [\App\Http\Controllers\Admin\TrainingController::class, 'store'])->name('trainings.store');
    Route::put('/trainings/{training}', [\App\Http\Controllers\Admin\TrainingController::class, 'update'])->name('trainings.update');
    Route::delete('/trainings/{training}', [\App\Http\Controllers\Admin\TrainingController::class, 'destroy'])->name('trainings.destroy');
    Route::post('/trainings/{training}/participants', [\App\Http\Controllers\Admin\TrainingController::class, 'storeParticipant'])->name('trainings.participants.store');
    Route::put('/trainings/{training}/participants/{participant}', [\App\Http\Controllers\Admin\TrainingController::class, 'updateParticipant'])->name('trainings.participants.update');
    Route::delete('/trainings/{training}/participants/{participant}', [\App\Http\Controllers\Admin\TrainingController::class, 'destroyParticipant'])->name('trainings.participants.destroy');

    // Template Dokumen (Admin)
    Route::post('/templates/extract-word', [\App\Http\Controllers\Admin\DocumentTemplateController::class, 'extractWord'])->name('templates.extract-word');
    Route::get('/templates/{template}/download-file', [\App\Http\Controllers\Admin\DocumentTemplateController::class, 'downloadFile'])->name('templates.download-file');
    Route::get('/templates/{template}/preview-pdf', [\App\Http\Controllers\Admin\DocumentTemplateController::class, 'previewPdf'])->name('templates.preview-pdf');
    Route::get('/templates', [\App\Http\Controllers\Admin\DocumentTemplateController::class, 'index'])->name('templates.index');
    Route::post('/templates', [\App\Http\Controllers\Admin\DocumentTemplateController::class, 'store'])->name('templates.store');
    Route::get('/templates/{template}', [\App\Http\Controllers\Admin\DocumentTemplateController::class, 'show'])->name('templates.show');
    Route::put('/templates/{template}', [\App\Http\Controllers\Admin\DocumentTemplateController::class, 'update'])->name('templates.update');
    Route::patch('/templates/{template}/toggle-status', [\App\Http\Controllers\Admin\DocumentTemplateController::class, 'toggleStatus'])->name('templates.toggle-status');
    Route::delete('/templates/{template}', [\App\Http\Controllers\Admin\DocumentTemplateController::class, 'destroy'])->name('templates.destroy');

    // Notifikasi ke Papan Informasi Petugas (Admin)
    Route::post('/notices/send', [\App\Http\Controllers\Admin\NoticeController::class, 'send'])->name('notices.send');
});

// Monitoring Lapangan & Rekapitulasi (Akses: Admin & Pegawai / PML)
Route::middleware(['auth', 'role:admin,pegawai'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Kegiatan & Penugasan (Read/Monitoring)
    Route::get('/assignments', [\App\Http\Controllers\Admin\AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/{assignment}', [\App\Http\Controllers\Admin\AssignmentController::class, 'show'])->name('assignments.show');

    // Jadwal Kegiatan (Read/Monitoring)
    Route::get('/schedules', [\App\Http\Controllers\Admin\ScheduleController::class, 'index'])->name('schedules.index');

    // Data Responden (Read/Monitoring)
    Route::get('/respondents', [\App\Http\Controllers\Admin\RespondentController::class, 'index'])->name('respondents.index');
    Route::get('/respondents/{respondent}', [\App\Http\Controllers\Admin\RespondentController::class, 'show'])->name('respondents.show');

    // BAST Responden (Read/Preview/Download)
    Route::get('/basts/respondents/get-by-officer', [\App\Http\Controllers\Admin\RespondentBastController::class, 'getRespondentsByOfficer'])->name('basts.respondents.by-officer');
    Route::get('/basts/respondents', [\App\Http\Controllers\Admin\RespondentBastController::class, 'index'])->name('basts.respondents.index');
    Route::get('/basts/respondents/{bast}/download', [\App\Http\Controllers\Admin\RespondentBastController::class, 'downloadPdf'])->name('basts.respondents.download');
    Route::get('/basts/respondents/{bast}/preview', [\App\Http\Controllers\Admin\RespondentBastController::class, 'previewPdf'])->name('basts.respondents.preview');

    // BAST Petugas (Read/Preview/Download)
    Route::get('/basts/officers', [\App\Http\Controllers\Admin\OfficerBastController::class, 'index'])->name('basts.officers.index');
    Route::get('/basts/officers/{bast}/download', [\App\Http\Controllers\Admin\OfficerBastController::class, 'downloadPdf'])->name('basts.officers.download');
    Route::get('/basts/officers/{bast}/preview', [\App\Http\Controllers\Admin\OfficerBastController::class, 'previewPdf'])->name('basts.officers.preview');

    // Laporan Petugas (Read/Preview/Download)
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{report}/download', [\App\Http\Controllers\Admin\ReportController::class, 'downloadPdf'])->name('reports.download');
    Route::get('/reports/{report}/preview', [\App\Http\Controllers\Admin\ReportController::class, 'previewPdf'])->name('reports.preview');

    // Daftar Hadir Kegiatan & Pelatihan (Read/Preview/Download)
    Route::get('/trainings', [\App\Http\Controllers\Admin\TrainingController::class, 'index'])->name('trainings.index');
    Route::get('/trainings/{training}', [\App\Http\Controllers\Admin\TrainingController::class, 'show'])->name('trainings.show');
    Route::get('/trainings/{training}/preview-pdf', [\App\Http\Controllers\Admin\TrainingController::class, 'previewAttendancePdf'])->name('trainings.preview');
    Route::get('/trainings/{training}/attendance-pdf', [\App\Http\Controllers\Admin\TrainingController::class, 'exportAttendancePdf'])->name('trainings.attendance.pdf');

    // Rekapitulasi & Export
    Route::get('/recapitulation', [\App\Http\Controllers\Admin\RecapitulationController::class, 'index'])->name('recapitulation.index');
    Route::get('/recapitulation/pdf', [\App\Http\Controllers\Admin\RecapitulationController::class, 'exportPdf'])->name('recapitulation.pdf');
    Route::get('/recapitulation/excel', [\App\Http\Controllers\Admin\RecapitulationController::class, 'exportExcel'])->name('recapitulation.excel');
});

// Portal Lapangan (Protected by role:petugas,pegawai)
Route::middleware(['auth', 'role:petugas,pegawai'])->prefix('petugas')->as('petugas.')->group(function () {
    Route::get('/dashboard', [PetugasDashboardController::class, 'index'])->name('dashboard');

    // Tugas Saya (Petugas Lapangan)
    Route::get('/assignments', [\App\Http\Controllers\Petugas\AssignmentController::class, 'index'])->name('assignments.index');
    Route::put('/assignments/{assignmentOfficer}/progress', [\App\Http\Controllers\Petugas\AssignmentController::class, 'updateProgress'])->name('assignments.progress.update');
    Route::post('/assignments/bulk-progress', [\App\Http\Controllers\Petugas\AssignmentController::class, 'bulkProgress'])->name('assignments.bulk-progress');
    // Jadwal Kegiatan (Petugas Lapangan)
    Route::get('/schedules', [\App\Http\Controllers\Petugas\ScheduleController::class, 'index'])->name('schedules.index');

    // Data Responden (Petugas Lapangan)
    Route::get('/respondents', [\App\Http\Controllers\Petugas\RespondentController::class, 'index'])->name('respondents.index');
    Route::post('/respondents', [\App\Http\Controllers\Petugas\RespondentController::class, 'store'])->name('respondents.store');
    Route::put('/respondents/{respondent}', [\App\Http\Controllers\Petugas\RespondentController::class, 'update'])->name('respondents.update');

    // BAST Responden (Petugas Lapangan)
    Route::get('/basts/respondents', [\App\Http\Controllers\Petugas\BastController::class, 'respondents'])->name('basts.respondents.index');
    Route::post('/basts/respondents', [\App\Http\Controllers\Petugas\BastController::class, 'storeRespondent'])->name('basts.respondents.store');
    Route::get('/basts/respondents/{bast}/download', [\App\Http\Controllers\Petugas\BastController::class, 'downloadRespondentPdf'])->name('basts.respondents.download');
    Route::get('/basts/respondents/{bast}/preview', [\App\Http\Controllers\Petugas\BastController::class, 'previewRespondentPdf'])->name('basts.respondents.preview');

    // BAST Petugas (Petugas Lapangan)
    Route::get('/basts/officers', [\App\Http\Controllers\Petugas\BastController::class, 'officers'])->name('basts.officers.index');
    Route::post('/basts/officers', [\App\Http\Controllers\Petugas\BastController::class, 'storeOfficer'])->name('basts.officers.store');
    Route::post('/basts/officers/{bast}/sign', [\App\Http\Controllers\Petugas\BastController::class, 'signOfficerReceipt'])->name('basts.officers.sign');
    Route::get('/basts/officers/{bast}/download', [\App\Http\Controllers\Petugas\BastController::class, 'downloadOfficerPdf'])->name('basts.officers.download');
    Route::get('/basts/officers/{bast}/preview', [\App\Http\Controllers\Petugas\BastController::class, 'previewOfficerPdf'])->name('basts.officers.preview');

    // Laporan Kegiatan (Petugas Lapangan)
    Route::get('/reports', [\App\Http\Controllers\Petugas\ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports', [\App\Http\Controllers\Petugas\ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{report}', [\App\Http\Controllers\Petugas\ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{report}/download', [\App\Http\Controllers\Petugas\ReportController::class, 'downloadPdf'])->name('reports.download');
    Route::get('/reports/{report}/preview', [\App\Http\Controllers\Petugas\ReportController::class, 'previewPdf'])->name('reports.preview');
    Route::put('/reports/{report}', [\App\Http\Controllers\Petugas\ReportController::class, 'update'])->name('reports.update');
    Route::delete('/reports/{report}', [\App\Http\Controllers\Petugas\ReportController::class, 'destroy'])->name('reports.destroy');

    // Papan Informasi & Notifikasi Petugas
    Route::patch('/notices/{notice}/read', [\App\Http\Controllers\Petugas\NoticeController::class, 'markAsRead'])->name('notices.read');
    Route::delete('/notices/{notice}', [\App\Http\Controllers\Petugas\NoticeController::class, 'destroy'])->name('notices.destroy');
});

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
        return Auth::user()->isAdmin()
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

    // Profile & Password settings (accessible to both roles)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

// Admin Area (Protected by role:admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Google Spreadsheet Database Engine Settings
    Route::get('/spreadsheet', [\App\Http\Controllers\Admin\SpreadsheetController::class, 'index'])->name('spreadsheet.index');
    Route::put('/spreadsheet', [\App\Http\Controllers\Admin\SpreadsheetController::class, 'update'])->name('spreadsheet.update');
    Route::post('/spreadsheet/test', [\App\Http\Controllers\Admin\SpreadsheetController::class, 'testConnection'])->name('spreadsheet.test');
    Route::post('/spreadsheet/init', [\App\Http\Controllers\Admin\SpreadsheetController::class, 'initSheets'])->name('spreadsheet.init');

    // Manajemen Pengguna
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::patch('/users/{user}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('users.reset-password');
    // Kegiatan & Penugasan (Admin)
    Route::get('/assignments', [\App\Http\Controllers\Admin\AssignmentController::class, 'index'])->name('assignments.index');
    Route::post('/assignments', [\App\Http\Controllers\Admin\AssignmentController::class, 'store'])->name('assignments.store');
    Route::get('/assignments/{assignment}', [\App\Http\Controllers\Admin\AssignmentController::class, 'show'])->name('assignments.show');
    Route::put('/assignments/{assignment}', [\App\Http\Controllers\Admin\AssignmentController::class, 'update'])->name('assignments.update');
    Route::delete('/assignments/{assignment}', [\App\Http\Controllers\Admin\AssignmentController::class, 'destroy'])->name('assignments.destroy');
    Route::post('/assignments/{assignment}/officers', [\App\Http\Controllers\Admin\AssignmentController::class, 'assignOfficer'])->name('assignments.officers.assign');
    Route::put('/assignments/{assignment}/officers/{assignmentOfficer}', [\App\Http\Controllers\Admin\AssignmentController::class, 'updateOfficerTask'])->name('assignments.officers.update');
    Route::delete('/assignments/{assignment}/officers/{assignmentOfficer}', [\App\Http\Controllers\Admin\AssignmentController::class, 'removeOfficer'])->name('assignments.officers.remove');
    // Jadwal Kegiatan (Admin)
    Route::get('/schedules', [\App\Http\Controllers\Admin\ScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/schedules', [\App\Http\Controllers\Admin\ScheduleController::class, 'store'])->name('schedules.store');
    Route::put('/schedules/{schedule}', [\App\Http\Controllers\Admin\ScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/schedules/{schedule}', [\App\Http\Controllers\Admin\ScheduleController::class, 'destroy'])->name('schedules.destroy');
    // Data Responden (Admin)
    Route::get('/respondents', [\App\Http\Controllers\Admin\RespondentController::class, 'index'])->name('respondents.index');
    Route::post('/respondents', [\App\Http\Controllers\Admin\RespondentController::class, 'store'])->name('respondents.store');
    Route::get('/respondents/{respondent}', [\App\Http\Controllers\Admin\RespondentController::class, 'show'])->name('respondents.show');
    Route::put('/respondents/{respondent}', [\App\Http\Controllers\Admin\RespondentController::class, 'update'])->name('respondents.update');
    Route::delete('/respondents/{respondent}', [\App\Http\Controllers\Admin\RespondentController::class, 'destroy'])->name('respondents.destroy');

    // BAST Responden (Admin)
    Route::get('/basts/respondents', [\App\Http\Controllers\Admin\RespondentBastController::class, 'index'])->name('basts.respondents.index');
    Route::post('/basts/respondents', [\App\Http\Controllers\Admin\RespondentBastController::class, 'store'])->name('basts.respondents.store');
    Route::get('/basts/respondents/{bast}/download', [\App\Http\Controllers\Admin\RespondentBastController::class, 'downloadPdf'])->name('basts.respondents.download');
    Route::get('/basts/respondents/{bast}/preview', [\App\Http\Controllers\Admin\RespondentBastController::class, 'previewPdf'])->name('basts.respondents.preview');
    Route::delete('/basts/respondents/{bast}', [\App\Http\Controllers\Admin\RespondentBastController::class, 'destroy'])->name('basts.respondents.destroy');

    // BAST Petugas (Admin)
    Route::get('/basts/officers', [\App\Http\Controllers\Admin\OfficerBastController::class, 'index'])->name('basts.officers.index');
    Route::post('/basts/officers', [\App\Http\Controllers\Admin\OfficerBastController::class, 'store'])->name('basts.officers.store');
    Route::get('/basts/officers/{bast}/download', [\App\Http\Controllers\Admin\OfficerBastController::class, 'downloadPdf'])->name('basts.officers.download');
    Route::get('/basts/officers/{bast}/preview', [\App\Http\Controllers\Admin\OfficerBastController::class, 'previewPdf'])->name('basts.officers.preview');
    Route::delete('/basts/officers/{bast}', [\App\Http\Controllers\Admin\OfficerBastController::class, 'destroy'])->name('basts.officers.destroy');
    // Laporan Petugas & Verifikasi (Admin)
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{report}/download', [\App\Http\Controllers\Admin\ReportController::class, 'downloadPdf'])->name('reports.download');
    Route::get('/reports/{report}/preview', [\App\Http\Controllers\Admin\ReportController::class, 'previewPdf'])->name('reports.preview');
    Route::put('/reports/{report}/review', [\App\Http\Controllers\Admin\ReportController::class, 'review'])->name('reports.review');
    Route::delete('/reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'destroy'])->name('reports.destroy');

    // Administrasi Pelatihan SPKRT (Admin)
    Route::get('/trainings', [\App\Http\Controllers\Admin\TrainingController::class, 'index'])->name('trainings.index');
    Route::post('/trainings', [\App\Http\Controllers\Admin\TrainingController::class, 'store'])->name('trainings.store');
    Route::get('/trainings/{training}', [\App\Http\Controllers\Admin\TrainingController::class, 'show'])->name('trainings.show');
    Route::put('/trainings/{training}', [\App\Http\Controllers\Admin\TrainingController::class, 'update'])->name('trainings.update');
    Route::delete('/trainings/{training}', [\App\Http\Controllers\Admin\TrainingController::class, 'destroy'])->name('trainings.destroy');
    Route::post('/trainings/{training}/participants', [\App\Http\Controllers\Admin\TrainingController::class, 'storeParticipant'])->name('trainings.participants.store');
    Route::put('/trainings/{training}/participants/{participant}', [\App\Http\Controllers\Admin\TrainingController::class, 'updateParticipant'])->name('trainings.participants.update');
    Route::delete('/trainings/{training}/participants/{participant}', [\App\Http\Controllers\Admin\TrainingController::class, 'destroyParticipant'])->name('trainings.participants.destroy');
    Route::get('/trainings/{training}/attendance-pdf', [\App\Http\Controllers\Admin\TrainingController::class, 'exportAttendancePdf'])->name('trainings.attendance.pdf');

    // Rekapitulasi & Export (Admin)
    Route::get('/recapitulation', [\App\Http\Controllers\Admin\RecapitulationController::class, 'index'])->name('recapitulation.index');
    Route::get('/recapitulation/pdf', [\App\Http\Controllers\Admin\RecapitulationController::class, 'exportPdf'])->name('recapitulation.pdf');
    Route::get('/recapitulation/excel', [\App\Http\Controllers\Admin\RecapitulationController::class, 'exportExcel'])->name('recapitulation.excel');

    // Template Dokumen (Admin)
    Route::get('/templates', [\App\Http\Controllers\Admin\DocumentTemplateController::class, 'index'])->name('templates.index');
    Route::post('/templates', [\App\Http\Controllers\Admin\DocumentTemplateController::class, 'store'])->name('templates.store');
    Route::get('/templates/{template}', [\App\Http\Controllers\Admin\DocumentTemplateController::class, 'show'])->name('templates.show');
    Route::put('/templates/{template}', [\App\Http\Controllers\Admin\DocumentTemplateController::class, 'update'])->name('templates.update');
    Route::patch('/templates/{template}/toggle-status', [\App\Http\Controllers\Admin\DocumentTemplateController::class, 'toggleStatus'])->name('templates.toggle-status');
    Route::delete('/templates/{template}', [\App\Http\Controllers\Admin\DocumentTemplateController::class, 'destroy'])->name('templates.destroy');

    // Audit Log Aktivitas (Admin)
    Route::get('/logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('logs.index');
    Route::get('/logs/{log}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('logs.show');
    Route::post('/logs/clear', [\App\Http\Controllers\Admin\ActivityLogController::class, 'clearOld'])->name('logs.clear');
});

// Petugas Lapangan Area (Protected by role:petugas)
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->as('petugas.')->group(function () {
    Route::get('/dashboard', [PetugasDashboardController::class, 'index'])->name('dashboard');

    // Tugas Saya (Petugas Lapangan)
    Route::get('/assignments', [\App\Http\Controllers\Petugas\AssignmentController::class, 'index'])->name('assignments.index');
    Route::put('/assignments/{assignmentOfficer}/progress', [\App\Http\Controllers\Petugas\AssignmentController::class, 'updateProgress'])->name('assignments.progress.update');
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
});

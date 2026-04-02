<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReviewerController;
use App\Http\Controllers\Admin\AcademicPeriodController;
use App\Http\Controllers\Admin\JadwalController;

// Guest routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Change Password routes (authenticated)
Route::middleware(['auth'])->group(function () {
    Route::get('/change-password', [LoginController::class, 'showChangePasswordForm'])->name('change-password');
    Route::post('/change-password', [LoginController::class, 'changePassword'])->name('change-password.store');
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('reviewer')) {
            return redirect()->route('reviewer.dashboard');
        } else {
            return redirect()->route('mahasiswa.dashboard');
        }
    })->name('dashboard');
});

// Admin routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'manageUsers'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/mahasiswa', [AdminController::class, 'mahasiswaList'])->name('mahasiswa.index');
    Route::get('/mahasiswa/import', [AdminController::class, 'importMahasiswa'])->name('mahasiswa.import');
    Route::post('/mahasiswa/import', [AdminController::class, 'importMahasiswaProcess'])->name('mahasiswa.import.process');
    Route::get('/mahasiswa/template', [AdminController::class, 'exportTemplate'])->name('mahasiswa.template');
    Route::get('/mahasiswa/{id}/detail', [AdminController::class, 'mahasiswaDetail'])->name('mahasiswa.detail');
    Route::get('/pendaftaran', [AdminController::class, 'allPendaftaran'])->name('pendaftaran');
    Route::get('/pendaftaran/skripsi/{id}', [AdminController::class, 'showSkripsi'])->name('pendaftaran.skripsi.show');
    Route::get('/pendaftaran/metodologi/{id}', [AdminController::class, 'showMetodologi'])->name('pendaftaran.metodologi.show');
    
    Route::get('/pendaftaran/{type}/{id}/assign', [AdminController::class, 'showAssignForm'])->name('pendaftaran.assign-form');
    Route::post('/pendaftaran/{type}/{id}/assign', [AdminController::class, 'assignReviewer'])->name('pendaftaran.assign');
    
    Route::resource('academic-periods', AcademicPeriodController::class);
    Route::get('/academic-periods/{id}/set-active', [AcademicPeriodController::class, 'setActive'])->name('academic-periods.set-active');
    Route::post('/users/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('users.reset-password');
    Route::get('/reviewers/stats', [AdminController::class, 'reviewerStats'])->name('reviewers.stats');
    Route::get('/mahasiswa/{id}/periods', [AdminController::class, 'getMahasiswaPeriods'])->name('admin.mahasiswa.periods');

    Route::prefix('jadwal')->name('jadwal.')->group(function () {
    Route::get('/', [JadwalController::class, 'index'])->name('index');

    Route::get('/skripsi/create/{id}', [JadwalController::class, 'createSkripsi'])->name('skripsi.create');
    Route::post('/skripsi/store/{id}', [JadwalController::class, 'storeSkripsi'])->name('skripsi.store');
    Route::get('/skripsi/edit/{id}', [JadwalController::class, 'editSkripsi'])->name('skripsi.edit');
    Route::put('/skripsi/update/{id}', [JadwalController::class, 'updateSkripsi'])->name('skripsi.update');
    Route::delete('/skripsi/destroy/{id}', [JadwalController::class, 'destroySkripsi'])->name('skripsi.destroy');

    Route::get('/metodologi/create/{id}', [JadwalController::class, 'createMetodologi'])->name('metodologi.create');
    Route::post('/metodologi/store/{id}', [JadwalController::class, 'storeMetodologi'])->name('metodologi.store');
    Route::get('/metodologi/edit/{id}', [JadwalController::class, 'editMetodologi'])->name('metodologi.edit');
    Route::put('/metodologi/update/{id}', [JadwalController::class, 'updateMetodologi'])->name('metodologi.update');
    Route::delete('/metodologi/destroy/{id}', [JadwalController::class, 'destroyMetodologi'])->name('metodologi.destroy');
  
});
});

// Reviewer routes
Route::middleware(['auth'])->prefix('reviewer')->name('reviewer.')->group(function () {
    Route::get('/dashboard', [ReviewerController::class, 'dashboard'])->name('dashboard');
    Route::get('/skripsi/{id}', [ReviewerController::class, 'reviewSkripsi'])->name('skripsi.review');
    Route::post('/skripsi/{id}/review', [ReviewerController::class, 'submitSkripsiReview'])->name('skripsi.submit');
    Route::post('/skripsi/{id}/approve', [ReviewerController::class, 'approveSkripsi'])->name('skripsi.approve');
    Route::post('/skripsi/{id}/reject', [ReviewerController::class, 'rejectSkripsi'])->name('skripsi.reject');
    Route::get('/metodologi/{id}', [ReviewerController::class, 'reviewMetodologi'])->name('metodologi.review');
    Route::post('/metodologi/{id}/review', [ReviewerController::class, 'submitMetodologiReview'])->name('metodologi.submit');
    Route::post('/metodologi/{id}/approve', [ReviewerController::class, 'approveMetodologi'])->name('metodologi.approve');
    Route::post('/metodologi/{id}/reject', [ReviewerController::class, 'rejectMetodologi'])->name('metodologi.reject');
});

// Mahasiswa routes
Route::middleware(['auth'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');
    Route::get('/daftar-skripsi', [MahasiswaController::class, 'daftarSkripsi'])->name('daftar-skripsi');
    Route::post('/daftar-skripsi', [MahasiswaController::class, 'storeSkripsi'])->name('store-skripsi');
    Route::get('/daftar-metodologi', [MahasiswaController::class, 'daftarMetodologi'])->name('daftar-metodologi');
    Route::post('/daftar-metodologi', [MahasiswaController::class, 'storeMetodologi'])->name('store-metodologi');
    Route::get('/skripsi/{id}', [MahasiswaController::class, 'showSkripsi'])->name('show-skripsi');
    Route::get('/metodologi/{id}', [MahasiswaController::class, 'showMetodologi'])->name('show-metodologi');
});
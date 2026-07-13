<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IncomingLetterController;
use App\Http\Controllers\DispositionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Halaman Dashboard
     Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

    // Routing untuk Surat Masuk (CRUD)
    Route::resource('surat-masuk', IncomingLetterController::class);
    
    // Routing untuk Disposisi
    Route::get('/disposisi/{surat_id}', [DispositionController::class, 'create'])->name('disposisi.create');
    Route::post('/disposisi', [DispositionController::class, 'store'])->name('disposisi.store');
    Route::put('/disposisi/{id}/status', [DispositionController::class, 'updateStatus'])->name('disposisi.updateStatus');
    Route::get('/disposisi/{id}/cetak', [DispositionController::class, 'cetak'])->name('disposisi.cetak');

    // Routing Profile Bawaan Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::resource('surat-masuk', IncomingLetterController::class);
    Route::put('/surat-masuk/{id}', [IncomingLetterController::class, 'update'])->name('surat-masuk.update');
    ///
    Route::put('/surat-masuk/{id}/catatan', [IncomingLetterController::class, 'updateCatatan'])->name('surat-masuk.catatan');
    ///
    // Rute untuk Update dan Hapus Surat
    Route::delete('/surat-masuk/{id}', [IncomingLetterController::class, 'destroy'])->name('surat-masuk.destroy');

    Route::get('/disposisi', [DispositionController::class, 'index'])->name('disposisi.index');

    Route::put('/disposisi/{id}', [DispositionController::class, 'update'])->name('disposisi.update');
    Route::get('/disposisi/cetak/{id}', [DispositionController::class, 'cetak'])->name('disposisi.cetak');

    // Rute sementara untuk Riwayat Aksi (CCTV) Admin
    Route::get('/riwayat-aksi', function () {
        return 'Halaman Riwayat Aksi sedang dibangun...';
    })->name('riwayat-aksi.index');

    Route::get('/riwayat-aksi', [ActivityLogController::class, 'index'])->name('riwayat-aksi.index');


});

require __DIR__.'/auth.php';
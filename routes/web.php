<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

use App\Http\Controllers\WebController;

Route::get('/', [WebController::class, 'index'])->name('home');
Route::get('/peta', [WebController::class, 'map'])->name('peta');

use App\Http\Controllers\Admin\KecamatanController as AdminKecamatanController;

use App\Http\Controllers\Admin\LandController as AdminLandController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/kecamatan', [AdminKecamatanController::class, 'index'])->name('kecamatan');
        Route::post('/kecamatan', [AdminKecamatanController::class, 'store'])->name('kecamatan.store');
        Route::put('/kecamatan/{gid}', [AdminKecamatanController::class, 'update'])->name('kecamatan.update');
        Route::delete('/kecamatan/{gid}', [AdminKecamatanController::class, 'destroy'])->name('kecamatan.destroy');
        
        Route::get('/lahan-sawah', [AdminLandController::class, 'index'])->defaults('type', 'sawah')->name('lahan-sawah');
        Route::get('/lahan-ladang', [AdminLandController::class, 'index'])->defaults('type', 'ladang')->name('lahan-ladang');
        Route::get('/lahan-kebun', [AdminLandController::class, 'index'])->defaults('type', 'kebun')->name('lahan-kebun');
        Route::post('/lahan-{type}', [AdminLandController::class, 'store'])->name('lahan.store');
        Route::put('/lahan-{type}/{gid}', [AdminLandController::class, 'update'])->name('lahan.update');
        Route::delete('/lahan-{type}/{gid}', [AdminLandController::class, 'destroy'])->name('lahan.destroy');
    });
});

require __DIR__.'/settings.php';

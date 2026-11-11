
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminReportController;

// Halaman landing
Route::get('/', function () {
    return view('landing');
});

// Route untuk pengguna yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Route untuk pengguna yang sudah login
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Homepage
    Route::get('/homepage', function () {
        return view('homepage.index');
    })->name('homepage');

    // Aduin
    Route::get('/aduin', function () {
        return view('aduin.index');
    })->name('aduin.index');

    // Aktivitas Saya
    Route::get('/aktivitas', function () {
        return view('aktivitas.index');
    })->name('aktivitas.index');

    // Backend laporan
    Route::post('/reports', [ReportController::class, 'store'])
        ->name('reports.store');

    Route::get('/aktivitas-saya', [ReportController::class, 'index'])
        ->name('reports.index');

    Route::get('/reports/{id}', [ReportController::class, 'show'])
        ->name('reports.show');

    Route::get('/reports/{id}/history', [ReportController::class, 'history'])
        ->name('reports.history');

    Route::get('/dashboard', [ReportController::class, 'dashboard'])
        ->name('dashboard');

    // Jelajahi
    Route::get('/jelajahi', [ReportController::class, 'explore'])
        ->name('reports.explore');

    // Wawasan
    Route::get('/wawasan', function () {
        return view('wawasan.index');
    })->name('wawasan.index');

    Route::get('/wawasan-kota', [ReportController::class, 'insights'])
        ->name('reports.insights');

    // Profil backend
    Route::get('/profil', [ProfileController::class, 'show'])
        ->name('profile.show');

    Route::put('/profil', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password');

    // Halaman profil frontend
    Route::get('/profile', function () {
        return view('profile.index');
    })->name('profile.index');

    // Route khusus Admin dan Petugas
    Route::prefix('admin')->name('admin.')->group(function () {

        Route::get('/reports', [AdminReportController::class, 'index'])
            ->name('reports.index');

        Route::get('/reports/{id}', [AdminReportController::class, 'show'])
            ->name('reports.show');

        Route::put('/reports/{id}/status', [AdminReportController::class, 'updateStatus'])
            ->name('reports.status');
    });
});

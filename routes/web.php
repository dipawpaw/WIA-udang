
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminReportController;

// Halaman Landing
Route::get('/', function () {
    return view('landing');
});

// Pengguna yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Pengguna yang sudah login
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Homepage
    Route::get('/homepage', function () {
        return view('homepage.index');
    })->name('homepage');

    // Aduin
    Route::get('/aduin', function () {
        return view('aduin.index');
    })->name('aduin.index');

    // Jelajahi (Frontend Statis)
    Route::get('/jelajahi', function () {
        return view('jelajahi.index');
    })->name('jelajahi.index');

    // Aktivitas
    Route::get('/aktivitas', function () {
        return view('aktivitas.index');
    })->name('aktivitas.index');

    // Wawasan (Frontend Statis)
    Route::get('/wawasan', function () {
        return view('wawasan.index');
    })->name('wawasan.index');

    // Backend Laporan
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

    // Endpoint Backend Eksplorasi
    Route::get('/api/jelajahi', [ReportController::class, 'explore'])
        ->name('reports.explore');

    // Backend Wawasan
    Route::get('/wawasan-kota', [ReportController::class, 'insights'])
        ->name('reports.insights');

    // Profil Backend
    Route::get('/profil', [ProfileController::class, 'show'])
        ->name('profile.show');

    Route::put('/profil', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password');

    // Halaman Profil Frontend
    Route::get('/profile', function () {
        return view('profile.index');
    })->name('profile.index');

    // Admin dan Petugas
    Route::prefix('admin')->name('admin.')->group(function () {

        Route::get('/reports', [AdminReportController::class, 'index'])
            ->name('reports.index');

        Route::get('/reports/{id}', [AdminReportController::class, 'show'])
            ->name('reports.show');

        Route::put('/reports/{id}/status', [AdminReportController::class, 'updateStatus'])
            ->name('reports.status');
    });
});

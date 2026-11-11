
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Homepage
    Route::get('/homepage', function () {
        return view('homepage.index');
    })->name('homepage');

    // ADUIN
    Route::get('/aduin', function () {
        return view('aduin.index');
    })->name('aduin.index');

    // JELAJAHI
    Route::get('/jelajahi', function () {
        return view('jelajahi.index');
    })->name('jelajahi.index');

    // AKTIVITAS
    Route::get('/aktivitas', function () {
        return view('aktivitas.index');
    })->name('aktivitas.index');

    // WAWASAN
    Route::get('/wawasan', function () {
        return view('wawasan.index');
    })->name('wawasan.index');
});

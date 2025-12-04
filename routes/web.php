<?php

use App\Http\Controllers\Auth\PasswordlessAuthController;
use App\Http\Controllers\Pages\EmployerController;
use App\Http\Controllers\Pages\HomeController;
use App\Http\Controllers\Pages\StudentController;
use App\Http\Controllers\Pages\UniversityController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('homepage');
Route::get('/students', [StudentController::class, 'index'])->name('students');
Route::get('/employers', [EmployerController::class, 'index'])->name('employers');
Route::get('/universities', [UniversityController::class, 'index'])->name('universities');

// Passwordless Authentication Routes
Route::middleware('guest')->group(function () {
    // Specific routes must come before the catch-all route
    // Rate limit OTP requests: 3 requests per 15 minutes per IP
    Route::post('/login/otp', [PasswordlessAuthController::class, 'requestOtp'])
        ->middleware('throttle:3,15')
        ->name('login.otp');

    Route::get('/login/verify', [PasswordlessAuthController::class, 'showOtpVerification'])->name('login.verify.show');

    // Rate limit OTP verification: 10 attempts per 15 minutes per IP
    Route::post('/login/verify', [PasswordlessAuthController::class, 'verifyOtp'])
        ->middleware('throttle:10,15')
        ->name('login.verify');

    // Catch-all route for login forms (must be last)
    Route::get('/login/{userType?}', [PasswordlessAuthController::class, 'showLoginForm'])->name('login');
});

// Logout route
Route::middleware('auth')->group(function () {
    Route::post('/logout', [PasswordlessAuthController::class, 'logout'])->name('logout');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

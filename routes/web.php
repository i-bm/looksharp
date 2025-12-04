<?php

use App\Http\Controllers\Auth\PasswordlessAuthController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Pages\EmployerController;
use App\Http\Controllers\Pages\HomeController;
use App\Http\Controllers\Pages\StudentController;
use App\Http\Controllers\Pages\UniversityController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('homepage');
Route::get('/students', [StudentController::class, 'index'])->name('students');
Route::get('/employers', [EmployerController::class, 'index'])->name('employers');
Route::get('/universities', [UniversityController::class, 'index'])->name('universities');

// Registration Routes
Route::middleware('guest')->group(function () {
    // Specific routes must come before the catch-all route
    // Rate limit registration OTP requests: 3 requests per 15 minutes per IP
    Route::post('/register/otp', [RegistrationController::class, 'requestRegistrationOtp'])
        ->middleware('throttle:3,15')
        ->name('register.otp');

    Route::get('/register/verify', [RegistrationController::class, 'showOtpVerification'])->name('register.verify.show');

    // Rate limit registration OTP verification: 10 attempts per 15 minutes per IP
    Route::post('/register/verify', [RegistrationController::class, 'verifyRegistrationOtp'])
        ->middleware('throttle:10,15')
        ->name('register.verify');

    Route::get('/register/email', [RegistrationController::class, 'showEmailRegistration'])->name('register.email');

    // Catch-all route for registration forms (must be last)
    Route::get('/register/{userType?}', [RegistrationController::class, 'showRegistrationForm'])->name('register');
});

// Passwordless Authentication Routes
Route::middleware('guest')->group(function () {
    // Specific routes must come before the catch-all route
    // Rate limit OTP requests: 3 requests per 15 minutes per IP
    Route::post('/login/otp', [PasswordlessAuthController::class, 'requestOtp'])
        ->middleware('throttle:10,15')
        ->name('login.otp');

    Route::get('/login/verify', [PasswordlessAuthController::class, 'showOtpVerification'])->name('login.verify.show');

    // Rate limit OTP verification: 10 attempts per 15 minutes per IP
    Route::post('/login/verify', [PasswordlessAuthController::class, 'verifyOtp'])
        ->middleware('throttle:10,15')
        ->name('login.verify');

    // Catch-all route for login forms (must be last)
    Route::get('/login/{userType?}', [PasswordlessAuthController::class, 'showLoginForm'])->name('login');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Logout route (accessible to all authenticated users)
    Route::post('/logout', [PasswordlessAuthController::class, 'logout'])->name('logout');

    // Profile building routes (accessible even with incomplete profile)
    Route::get('/profile/build', [ProfileController::class, 'showWizard'])->name('profile.build');
    Route::get('/profile/build/step/{step}', [ProfileController::class, 'step'])->name('profile.build.step');
    Route::post('/profile/build/step/{step}', [ProfileController::class, 'saveStep'])->name('profile.build.save');
    Route::post('/profile/photo', [ProfileController::class, 'uploadPhoto'])->name('profile.photo.upload');
    Route::delete('/profile/education/{id}', [ProfileController::class, 'removeEducation'])->name('profile.education.remove');
    Route::delete('/profile/skill/{id}', [ProfileController::class, 'removeSkill'])->name('profile.skill.remove');
    Route::get('/profile/complete', [ProfileController::class, 'complete'])->name('profile.complete');

    // Routes that require complete profile (for talent users)
    Route::middleware('talent.profile.complete')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

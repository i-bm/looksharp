<?php

use App\Http\Controllers\Pages\AboutController;
use App\Http\Controllers\Pages\CareerController;
use App\Http\Controllers\Pages\ContactController;
use App\Http\Controllers\Pages\HomeController;
use App\Http\Controllers\Pages\ServicesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about-us', [AboutController::class, 'index'])->name('about');
Route::get('/services', [ServicesController::class, 'index'])->name('services');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::get('/services/consulting', [ServicesController::class, 'consulting'])->name('services.consulting');
Route::get('/services/software', [ServicesController::class, 'software'])->name('services.software');
Route::get('/services/it-infrastructure', [ServicesController::class, 'itInfrastructure'])->name('services.it-infrastructure');
Route::get('/services/cybersecurity', [ServicesController::class, 'cybersecurity'])->name('services.cybersecurity');
Route::get('/services/ai-analytics', [ServicesController::class, 'aiAnalytics'])->name('services.ai-analytics');
Route::get('/careers', [CareerController::class, 'index'])->name('careers');
Route::get('/careers/{id}', [CareerController::class, 'show'])->name('careers.show');

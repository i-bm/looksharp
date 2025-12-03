<?php

use App\Http\Controllers\Pages\EmployerController;
use App\Http\Controllers\Pages\HomeController;
use App\Http\Controllers\Pages\StudentController;
use App\Http\Controllers\Pages\UniversityController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('homepage');
Route::get('/students', [StudentController::class, 'index'])->name('students');
Route::get('/employers', [EmployerController::class, 'index'])->name('employers');
Route::get('/universities', [UniversityController::class, 'index'])->name('universities');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

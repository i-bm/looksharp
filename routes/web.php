<?php

use App\Http\Controllers\Auth\PasswordlessAuthController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployerProfileController;
use App\Http\Controllers\Pages\EmployerController;
use App\Http\Controllers\Pages\HomeController;
use App\Http\Controllers\Pages\StudentController;
use App\Http\Controllers\Pages\UniversityController;
use App\Http\Controllers\TalentProfileController;
use App\Http\Controllers\UniversityProfileController;
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
        ->middleware('throttle:'.env('REGISTRATION_OTP_THROTTLE', 3).','.env('REGISTRATION_OTP_THROTTLE_INTERVAL', 15))
        ->name('register.otp');

    Route::get('/register/verify', [RegistrationController::class, 'showOtpVerification'])->name('register.verify.show');

    // Rate limit registration OTP verification: 3 attempts per 15 minutes per IP
    Route::post('/register/verify', [RegistrationController::class, 'verifyRegistrationOtp'])
        ->middleware('throttle:'.env('REGISTRATION_OTP_VERIFICATION_THROTTLE', 3).','.env('REGISTRATION_OTP_VERIFICATION_THROTTLE_INTERVAL', 15))
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
        ->middleware('throttle:'.env('LOGIN_OTP_THROTTLE', 10).','.env('LOGIN_OTP_THROTTLE_INTERVAL', 15))
        ->name('login.otp');

    Route::get('/login/verify', [PasswordlessAuthController::class, 'showOtpVerification'])->name('login.verify.show');

    // Rate limit OTP verification: 10 attempts per 15 minutes per IP
    Route::post('/login/verify', [PasswordlessAuthController::class, 'verifyOtp'])
        ->middleware('throttle:'.env('LOGIN_OTP_VERIFICATION_THROTTLE', 10).','.env('LOGIN_OTP_VERIFICATION_THROTTLE_INTERVAL', 15))
        ->name('login.verify');

    // Catch-all route for login forms (must be last)
    Route::get('/login/{userType?}', [PasswordlessAuthController::class, 'showLoginForm'])->name('login');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Logout route (accessible to all authenticated users)
    Route::post('/logout', [PasswordlessAuthController::class, 'logout'])->name('logout');

    // Talent-specific profile routes
    Route::middleware('role:talent')->prefix('talent')->name('talent.')->group(function () {
        // Profile building routes (accessible even with incomplete profile)
        // Redirects to profile page if profile is already complete
        Route::middleware('redirect.if.profile.complete')->group(function () {
            Route::get('/profile/build', [TalentProfileController::class, 'showWizard'])->name('profile.build');
            Route::get('/profile/build/step/{step}', [TalentProfileController::class, 'step'])->name('profile.build.step');
            Route::post('/profile/build/step/{step}', [TalentProfileController::class, 'saveStep'])->name('profile.build.save');
            Route::get('/profile/complete', [TalentProfileController::class, 'complete'])->name('profile.complete');
        });

        Route::post('/profile/photo', [TalentProfileController::class, 'uploadPhoto'])->name('profile.photo.upload');
        Route::post('/profile/resume', [TalentProfileController::class, 'uploadResume'])->name('profile.resume.upload');
        
        // AJAX routes for section updates
        Route::put('/profile/about-me', [TalentProfileController::class, 'updateAboutMe'])->name('profile.about-me.update');
        Route::put('/profile/video-introduction', [TalentProfileController::class, 'updateVideoIntroduction'])->name('profile.video-introduction.update');
        Route::put('/profile/fun-fact', [TalentProfileController::class, 'updateFunFact'])->name('profile.fun-fact.update');
        Route::put('/profile/passion', [TalentProfileController::class, 'updatePassion'])->name('profile.passion.update');
        Route::put('/profile/hobbies', [TalentProfileController::class, 'updateHobbies'])->name('profile.hobbies.update');
        Route::put('/profile/social-links', [TalentProfileController::class, 'updateSocialLinks'])->name('profile.social-links.update');
        Route::put('/profile/work-preferences', [TalentProfileController::class, 'updateWorkPreferences'])->name('profile.work-preferences.update');
        
        // Education routes (AJAX)
        Route::post('/profile/education', [TalentProfileController::class, 'addEducationAjax'])->name('profile.education.add');
        Route::delete('/profile/education/{id}', [TalentProfileController::class, 'removeEducationAjax'])->name('profile.education.remove');
        
        // Skill routes (AJAX)
        Route::post('/profile/skill', [TalentProfileController::class, 'addSkillAjax'])->name('profile.skill.add');
        Route::delete('/profile/skill/{id}', [TalentProfileController::class, 'removeSkillAjax'])->name('profile.skill.remove');
        
        // Work history routes (AJAX)
        Route::post('/profile/work-history', [TalentProfileController::class, 'addWorkHistoryAjax'])->name('profile.work-history.add');
        Route::delete('/profile/work-history/{id}', [TalentProfileController::class, 'removeWorkHistoryAjax'])->name('profile.work-history.remove');
        
        // Language routes (AJAX)
        Route::post('/profile/language', [TalentProfileController::class, 'addLanguageAjax'])->name('profile.language.add');
        Route::delete('/profile/language/{id}', [TalentProfileController::class, 'removeLanguageAjax'])->name('profile.language.remove');
        
        // Certification routes (AJAX)
        Route::post('/profile/certification', [TalentProfileController::class, 'addCertificationAjax'])->name('profile.certification.add');
        Route::delete('/profile/certification/{id}', [TalentProfileController::class, 'removeCertificationAjax'])->name('profile.certification.remove');
        
        // Volunteer experience routes (AJAX)
        Route::post('/profile/volunteer-experience', [TalentProfileController::class, 'addVolunteerExperienceAjax'])->name('profile.volunteer-experience.add');
        Route::delete('/profile/volunteer-experience/{id}', [TalentProfileController::class, 'removeVolunteerExperienceAjax'])->name('profile.volunteer-experience.remove');
        
        // Leadership experience routes (AJAX)
        Route::post('/profile/leadership-experience', [TalentProfileController::class, 'addLeadershipExperienceAjax'])->name('profile.leadership-experience.add');
        Route::delete('/profile/leadership-experience/{id}', [TalentProfileController::class, 'removeLeadershipExperienceAjax'])->name('profile.leadership-experience.remove');
        
        // Gigs/Freelance routes (AJAX)
        Route::post('/profile/gigs-freelance', [TalentProfileController::class, 'addGigsFreelanceAjax'])->name('profile.gigs-freelance.add');
        Route::delete('/profile/gigs-freelance/{id}', [TalentProfileController::class, 'removeGigsFreelanceAjax'])->name('profile.gigs-freelance.remove');

        // Profile view and edit routes
        Route::get('/profile', [TalentProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [TalentProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [TalentProfileController::class, 'update'])->name('profile.update');
        Route::patch('/profile', [TalentProfileController::class, 'update']);
    });

    // Employer-specific profile routes
    Route::middleware('role:employer')->prefix('employer')->name('employer.')->group(function () {
        Route::get('/company', [EmployerProfileController::class, 'show'])->name('company.show');
        Route::get('/company/edit', [EmployerProfileController::class, 'edit'])->name('company.edit');
        Route::put('/company', [EmployerProfileController::class, 'update'])->name('company.update');
        Route::patch('/company', [EmployerProfileController::class, 'update']);
    });

    // University-specific profile routes
    Route::middleware('role:university')->prefix('university')->name('university.')->group(function () {
        Route::get('/profile', [UniversityProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [UniversityProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [UniversityProfileController::class, 'update'])->name('profile.update');
        Route::patch('/profile', [UniversityProfileController::class, 'update']);
    });

    // Routes that require complete profile (for talent users)
    Route::middleware('talent.profile.complete')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
});

// Public profile routes (accessible without authentication)
Route::get('/profile/{slug}', [TalentProfileController::class, 'public'])->name('talent.profile.public');
Route::get('/company/{id}', [EmployerProfileController::class, 'public'])->name('employer.company.public');
Route::get('/university/{id}', [UniversityProfileController::class, 'public'])->name('university.profile.public');

// Redirect /home to /dashboard for backward compatibility
Route::get('/home', function () {
    return redirect()->route('dashboard');
})->name('home');

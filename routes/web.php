<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvitationController;
use App\Livewire\InvitationEditor;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Marketing Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/templates', [HomeController::class, 'templates'])->name('templates.index');
Route::get('/templates/{slug}/preview', [HomeController::class, 'previewTemplate'])->name('templates.preview');
Route::get('/pricing', [HomeController::class, 'pricing'])->name('pricing');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:6,1');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('throttle:6,1');
});

// Language Switcher
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Customer Dashboard (Protected Routes)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('dashboard')->name('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    
    // Invitations Management
    Route::get('/invitations', [DashboardController::class, 'invitations'])->name('.invitations.index');
    Route::get('/invitations/create', [DashboardController::class, 'create'])->name('.invitations.create');
    Route::post('/invitations', [DashboardController::class, 'store'])->name('.invitations.store');
    Route::get('/invitations/{id}/editor', InvitationEditor::class)->name('.invitations.editor');
    Route::delete('/invitations/{id}', [DashboardController::class, 'destroy'])->name('.invitations.destroy');
    
    // RSVP Guest Management
    Route::get('/invitations/{id}/guests', [DashboardController::class, 'guests'])->name('.invitations.guests');
    Route::get('/invitations/{id}/guests/export', [DashboardController::class, 'exportGuestsCsv'])->name('.invitations.guests.export');

    // Profile & Settings
    Route::get('/profile', [DashboardController::class, 'profile'])->name('.profile');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('.profile.update');
});

/*
|--------------------------------------------------------------------------
| Public Invitation Page
|--------------------------------------------------------------------------
*/
Route::get('/invitation/{slug}', [InvitationController::class, 'show'])->name('invitation.show');

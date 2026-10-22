<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RazorpayController;
use App\Http\Controllers\SuperAdminController;
use App\Support\HtmlPageRenderer;
use Illuminate\Support\Facades\Route;

// ─── Public Pages ──────────────────────────────────────────────────────────────
Route::get('/', [PageController::class, 'home']);

Route::middleware('role:customer')->group(function () {
    Route::post('/api/razorpay/create-order', [RazorpayController::class, 'createOrder']);
    Route::post('/api/razorpay/verify', [RazorpayController::class, 'verify']);
});
Route::post('/api/razorpay/webhook', [RazorpayController::class, 'webhook']);

$pages = implode('|', array_values(array_filter(
    HtmlPageRenderer::PAGES,
    fn (string $page) => $page !== 'index'
)));

Route::get('/{page}', [PageController::class, 'show'])
    ->where('page', $pages);

// ─── Auth Routes ───────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegistration'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── SuperAdmin Routes ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
});

// ─── Admin Routes ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
});

// ─── Customer Routes ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
});

<?php

use App\Http\Controllers\AccountProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RazorpayController;
use App\Http\Controllers\SuperAdmin\AssignmentController;
use App\Http\Controllers\SuperAdmin\EnquiryController as SuperAdminEnquiryController;
use App\Http\Controllers\SuperAdmin\HomePageController;
use App\Http\Controllers\SuperAdmin\PlanPriceController;
use App\Http\Controllers\SuperAdmin\ProfileController;
use App\Http\Controllers\SuperAdmin\SitePageController;
use App\Http\Controllers\SuperAdmin\UserController;
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
Route::post('/enquiries', [EnquiryController::class, 'store'])
    ->middleware('throttle:8,1')
    ->name('enquiries.store');

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
    Route::get('/home', [HomePageController::class, 'edit'])->name('home.edit');
    Route::put('/home', [HomePageController::class, 'update'])->name('home.update');
    Route::get('/pages', [SitePageController::class, 'index'])->name('pages.index');
    Route::get('/pages/{slug}/edit', [SitePageController::class, 'edit'])->name('pages.edit');
    Route::put('/pages/{slug}', [SitePageController::class, 'update'])->name('pages.update');
    Route::delete('/pages/{slug}', [SitePageController::class, 'restore'])->name('pages.restore');
    Route::get('/plan-prices', [PlanPriceController::class, 'edit'])->name('prices.edit');
    Route::put('/plan-prices', [PlanPriceController::class, 'update'])->name('prices.update');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/enquiries', [SuperAdminEnquiryController::class, 'index'])->name('enquiries.index');
    Route::get('/enquiries/{enquiry}', [SuperAdminEnquiryController::class, 'show'])->name('enquiries.show');
    Route::delete('/enquiries/{enquiry}', [SuperAdminEnquiryController::class, 'destroy'])->name('enquiries.destroy');
    Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    Route::put('/assignments', [AssignmentController::class, 'update'])->name('assignments.update');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// ─── Admin Routes ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/messages', [AdminController::class, 'messages'])->name('messages.index');
    Route::post('/messages', [AdminController::class, 'replyMessage'])
        ->middleware('throttle:20,1')
        ->name('messages.reply');
    Route::get('/nutrition', [AdminController::class, 'nutrition'])->name('nutrition.index');
    Route::post('/nutrition', [AdminController::class, 'shareNutrition'])
        ->middleware('throttle:10,1')
        ->name('nutrition.store');
    Route::delete('/nutrition/{nutritionPlan}', [AdminController::class, 'destroyNutrition'])
        ->name('nutrition.destroy');
    Route::get('/trainees', [AdminController::class, 'trainees'])->name('trainees');
    Route::get('/profile', [AccountProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [AccountProfileController::class, 'update'])->name('profile.update');
});

// ─── Customer Routes ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/trainer', [CustomerController::class, 'trainer'])->name('trainer');
    Route::get('/program', [CustomerController::class, 'program'])->name('program');
    Route::get('/schedule', [CustomerController::class, 'schedule'])->name('schedule');
    Route::get('/progress', [CustomerController::class, 'progress'])->name('progress');
    Route::get('/nutrition', [CustomerController::class, 'nutrition'])->name('nutrition');
    Route::get('/messages', [CustomerController::class, 'messages'])->name('messages.index');
    Route::post('/messages', [CustomerController::class, 'storeMessage'])
        ->middleware('throttle:20,1')
        ->name('messages.store');
    Route::get('/subscription', [CustomerController::class, 'subscription'])->name('subscription');
    Route::get('/notifications', [CustomerController::class, 'notifications'])->name('notifications');
    Route::get('/profile', [AccountProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [AccountProfileController::class, 'update'])->name('profile.update');
});

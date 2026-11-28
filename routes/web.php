<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SuperAdmin\HelpArticleController;
use App\Http\Controllers\SuperAdmin\LandingSettingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::get('/help', [HelpController::class, 'index'])->name('help.index');
Route::get('/help/{helpArticle:slug}', [HelpController::class, 'show'])->name('help.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    Route::get('/transactions/results', [TransactionController::class, 'results'])->name('transactions.results');
    Route::get('/transactions/{transaction}/receipt', [TransactionController::class, 'receipt'])
        ->name('transactions.receipt');
    Route::resource('transactions', TransactionController::class);

    Route::resource('categories', CategoryController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::get('/reports/results', [ReportController::class, 'results'])->name('reports.results');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
    Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export');
    Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');

    Route::middleware('super_admin')->group(function () {
        Route::resource('users', UserController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        Route::get('/super-admin/landing', [LandingSettingController::class, 'edit'])->name('super-admin.landing.edit');
        Route::put('/super-admin/landing', [LandingSettingController::class, 'update'])->name('super-admin.landing.update');

        Route::resource('/super-admin/help', HelpArticleController::class)
            ->names('super-admin.help')
            ->parameters(['help' => 'helpArticle'])
            ->except(['show']);
    });
});

<?php

use App\Http\Controllers\AnalysisReportController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/reports/{report}', [AnalysisReportController::class, 'show'])
    ->name('reports.show');

require __DIR__ . '/auth.php';

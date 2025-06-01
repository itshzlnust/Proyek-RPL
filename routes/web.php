<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TreatmentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;

// Authentication Routes
Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Treatments
    Route::resource('treatments', TreatmentController::class);
    
    // Customers
    Route::resource('customers', CustomerController::class);
    
    // Orders
    Route::resource('orders', OrderController::class);
    
    // Reports
    Route::get('/orders/{order}/invoice', [ReportController::class, 'generateInvoice'])->name('orders.invoice');
});

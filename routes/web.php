<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});
Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}/csv', [ReportController::class, 'csv'])->name('reports.csv');
    Route::resources(['customers' => CustomerController::class, 'suppliers' => SupplierController::class, 'products' => ProductController::class]);
    Route::resource('budgets', BudgetController::class)->except(['show']);
    Route::get('/budgets/{budget}', [BudgetController::class, 'show'])->name('budgets.show');
    Route::resource('expenses', ExpenseController::class)->except(['show']);
    Route::get('/invoices/{type}', [InvoiceController::class, 'index'])->whereIn('type', ['sales', 'purchase'])->name('invoices.index');
    Route::get('/invoices/{type}/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices/{type}', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{type}/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::put('/invoices/{type}/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('/invoices/{type}/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::get('/invoices/{type}/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::post('/invoices/{type}/{invoice}/payments', [InvoiceController::class, 'payment'])->name('invoices.payments.store');
    Route::delete('/invoices/{type}/{invoice}/payments/{payment}', [InvoiceController::class, 'destroyPayment'])->name('invoices.payments.destroy');
});

// payment deletion route is defined in the authenticated invoice routes

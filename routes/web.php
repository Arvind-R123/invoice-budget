<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, DashboardController, InvoiceController};

Route::redirect('/', '/dashboard');
Route::middleware('guest')->group(function(){Route::get('/login',[AuthController::class,'showLogin'])->name('login');Route::post('/login',[AuthController::class,'login'])->name('login.store');});
Route::middleware(['auth','admin'])->group(function(){Route::post('/logout',[AuthController::class,'logout'])->name('logout');Route::get('/dashboard',DashboardController::class)->name('dashboard');Route::get('/invoices/{type}',[InvoiceController::class,'index'])->whereIn('type',['sales','purchase'])->name('invoices.index');Route::get('/invoices/{type}/create',[InvoiceController::class,'create'])->name('invoices.create');Route::post('/invoices/{type}',[InvoiceController::class,'store'])->name('invoices.store');Route::get('/invoices/{type}/{invoice}',[InvoiceController::class,'show'])->name('invoices.show');});

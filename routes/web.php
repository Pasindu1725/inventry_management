<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\InventoryTransactionController; // Added
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Item Management Routes
    Route::resource('items', ItemController::class);

    // Inventory Transaction Routes
    Route::post('items/{item}/add-stock', [InventoryTransactionController::class, 'addStock'])->name('items.add-stock');
    Route::post('items/{item}/deduct-stock', [InventoryTransactionController::class, 'deductStock'])->name('items.deduct-stock');
});

require __DIR__.'/auth.php';

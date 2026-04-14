<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

//Katalog (Kategorie i Produkty) - dostępne dla wszystkich
Route::get('/kategoria/{slug}', [HomeController::class, 'show'])->name('category.show');// po -> jest nazwa dla widoku
Route::get('/produkt/{slug}', [HomeController::class, 'product'])->name('product.show');
Route::post('/koszyk/dodaj', [CartController::class, 'add'])->name('cart.add');
Route::get('/koszyk', [CartController::class, 'index'])->name('cart.show');
Route::delete('/koszyk/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

//Trasy chronione dla zalogowanych (Profil)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard (Breeze go tu zostawił)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

//Panel Admina (tylko dla zalogowanych ADMINÓW)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', function () {
        return "Witaj w tajnym panelu admina! 🧀";
    })->name('admin.dashboard');

});

require __DIR__.'/auth.php';

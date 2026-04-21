<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController; // wrzucamy alias, bo wywala błąd

Route::get('/', [HomeController::class, 'index'])->name('home');

//Katalog (Kategorie i Produkty) - dostępne dla wszystkich
Route::get('/kategoria/{slug}', [HomeController::class, 'show'])->name('category.show');// po -> jest nazwa dla widoku
Route::get('/produkt/{slug}', [HomeController::class, 'product'])->name('product.show');
Route::post('/koszyk/dodaj', [CartController::class, 'add'])->name('cart.add');
Route::get('/koszyk', [CartController::class, 'index'])->name('cart.show');
Route::delete('/koszyk/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::post('/zamowienie/stworz', [OrderController::class, 'store'])->name('order.store');

//Trasy chronione dla zalogowanych (Profil)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/zamowienia', [OrderController::class, 'index'])->name('orders.index');

    // Dashboard (Breeze go tu zostawił)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware('signed')->group(function () {// weryfikuj hash w url dla niezalogowanych
    Route::get('/zamowienie/status/{uuid}', [OrderController::class, 'showForGuest'])->name('order.guest_show');
});

//Panel Admina (tylko dla zalogowanych ADMINÓW)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () { //sprawdzić dlaczego dokłądnie admin
    Route::get('/admin', function () {
        return "Witaj w tajnym panelu admina! 🧀";
    })->name('admin.dashboard');
    Route::patch('/order/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('order.updateStatus');
    Route::get('/order/{order}', [AdminOrderController::class, 'show'])->name('order.show');
    Route::get('/orders',[AdminOrderController::class, 'index'])->name('order.index');
});

require __DIR__.'/auth.php';

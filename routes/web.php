<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController; // wrzucamy alias, bo wywala błąd
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\Auth\GoogleController;




Route::get('/', [HomeController::class, 'index'])->name('home');

//Katalog (Kategorie i Produkty) - dostępne dla wszystkich
Route::get('/kategoria/{slug}', [HomeController::class, 'show'])->name('category.show'); // po -> jest nazwa dla widoku
Route::get('/produkt/{slug}', [HomeController::class, 'product'])->name('product.show');
Route::post('/koszyk/dodaj', [CartController::class, 'add'])->name('cart.add');
Route::get('/koszyk', [CartController::class, 'index'])->name('cart.show');
Route::delete('/koszyk/{id}', [CartController::class, 'destroy'])->name('cart.destroy')->whereNumber('id');
Route::post('/koszyk/kod', [CartController::class, 'applyDiscount'])->name('cart.discount.apply');
Route::delete('/koszyk/kod', [CartController::class, 'removeDiscount'])->name('cart.discount.remove');
Route::post('/stripe/webhook', [WebhookController::class, 'handle'])->name('stripe.webhook');
Route::get('/sklep', [ShopController::class, 'index'])->name('shop.index');
// Logowanie przez Google (OAuth)
Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');




//Trasy chronione dla zalogowanych (Profil)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/zamowienia', [OrderController::class, 'index'])->name('orders.index');
    Route::delete('/profil/adresy/{address}', [AddressController::class, 'destroy'])->name('address.destroy');

    // Dashboard (Breeze go tu zostawił)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware('signed')->group(function () { // weryfikuj hash w url dla niezalogowanych
    Route::get('/zamowienie/status/{uuid}', [OrderController::class, 'showForGuest'])->name('order.guest_show');
});

//Panel Admina (tylko dla zalogowanych ADMINÓW)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () { //sprawdzić dlaczego dokłądnie admin
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::patch('/order/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('order.updateStatus');
    Route::get('/order/{order}', [AdminOrderController::class, 'show'])->name('order.show');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('order.index');
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class); // tutaj zaciąga mi wszystko na raz do 1, bo taki standard laravela
    Route::resource('discounts', \App\Http\Controllers\Admin\DiscountCodeController::class)->except(['show']);
});

Route::prefix('zamowienie/kasa')->name('checkout.')->group(function () {
    Route::get('/adres', [CheckoutController::class, 'address'])->name('address');
    Route::post('/adres', [CheckoutController::class, 'storeAddress'])->name('address.store');
    Route::get('/dostawa', [CheckoutController::class, 'shipping'])->name('shipping');
    Route::post('/dostawa', [CheckoutController::class, 'storeShipping'])->name('shipping.store');
    Route::get('/podsumowanie', [CheckoutController::class, 'summary'])->name('summary');
    Route::post('/finalizuj', [CheckoutController::class, 'placeOrder'])->name('place');
    Route::get('/sukces/{order}', [CheckoutController::class, 'success'])->name('success');
    Route::get('/anulowano/{order}', [CheckoutController::class, 'cancel'])->name('cancel');
    Route::post('/zaplac/{order}', [CheckoutController::class, 'payAgain'])->name('pay');
});

require __DIR__ . '/auth.php';

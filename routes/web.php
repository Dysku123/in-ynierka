<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController; // alias, bo wyżej jest już OrderController sklepu (ta sama nazwa klasy)
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\Auth\GoogleController;




Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/regulamin', 'pages.terms')->name('terms');
Route::view('/polityka-prywatnosci', 'pages.privacy')->name('privacy');

// Katalog, koszyk, webhook: dostępne dla wszystkich (->name() nadaje trasie nazwę używaną w route('...'))
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




// Trasy tylko dla zalogowanych (profil, zamówienia, adresy)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/zamowienia', [OrderController::class, 'index'])->name('orders.index');
    Route::delete('/profil/adresy/{address}', [AddressController::class, 'destroy'])->name('address.destroy');

    // Breeze po potwierdzeniu hasła / weryfikacji maila kieruje na trasę 'dashboard', a u nas to strona główna
    Route::redirect('/dashboard', '/')->name('dashboard');
});

Route::middleware('signed')->group(function () { // podpisany link: śledzenie zamówienia bez logowania (zmiana adresu = 403)
    Route::get('/zamowienie/status/{uuid}', [OrderController::class, 'showForGuest'])->name('order.guest_show');
});

// Panel admina: 'auth' = zalogowany, 'admin' = alias middleware CheckAdminRole (bootstrap/app.php), czyli tylko rola admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::patch('/order/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('order.updateStatus');
    Route::get('/order/{order}', [AdminOrderController::class, 'show'])->name('order.show');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('order.index');
    // resource = komplet tras CRUD jedną linią (index, create, store, edit, update, destroy); bez 'show', bo admin edytuje, nie ogląda
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)->except(['show']);
    Route::resource('discounts', \App\Http\Controllers\Admin\DiscountCodeController::class)->except(['show']);
});

Route::prefix('zamowienie/kasa')->name('checkout.')->group(function () {
    Route::get('/adres', [CheckoutController::class, 'address'])->name('address');
    Route::post('/adres', [CheckoutController::class, 'storeAddress'])->name('address.store');
    Route::get('/dostawa', [CheckoutController::class, 'shipping'])->name('shipping');
    Route::post('/dostawa', [CheckoutController::class, 'storeShipping'])->name('shipping.store');
    Route::get('/podsumowanie', [CheckoutController::class, 'summary'])->name('summary');
    Route::post('/finalizuj', [CheckoutController::class, 'placeOrder'])->name('place');
    Route::get('/sukces/{order}', [CheckoutController::class, 'success'])->name('success')->middleware('signed');
    Route::get('/anulowano/{order}', [CheckoutController::class, 'cancel'])->name('cancel')->middleware('signed');
    Route::post('/zaplac/{order}', [CheckoutController::class, 'payAgain'])->name('pay')->middleware('signed');

});

require __DIR__ . '/auth.php';

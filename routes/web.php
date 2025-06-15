<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\MyFavoriteSubjectController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PokemonController;

// Public routes
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['web'])->group(function () {
    Route::get('/home', function () {
        return view('home');
    })->name('home');
    
    // Product routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Weather routes
    Route::get('/weather', [WeatherController::class, 'showForm'])->name('weather.form');
    Route::get('/weather/search', [WeatherController::class, 'getWeather'])->name('weather.search');
    
    // Pokemon routes
    Route::get('/myfavoritesubject', [PokemonController::class, 'index'])->name('myfavoritesubject');
    Route::post('/pokemon', [PokemonController::class, 'store'])->name('pokemon.store');
    Route::post('/pokemon/add-api', [PokemonController::class, 'addApiPokemon'])->name('pokemon.add-api');

    // Map routes
    Route::get('/map', [MapController::class, 'index'])->name('map');
    Route::get('/map/search', [MapController::class, 'search'])->name('map.search');

    // Shop routes
    Route::get('/shop', [ShopController::class, 'index'])->name('shop');
    Route::get('/shop/category/{category}', [ShopController::class, 'category'])->name('shop.category');
    Route::get('/shop/product/{id}', [ShopController::class, 'show'])->name('shop.product.show');
    Route::get('/shop/product/{id}/edit', [ShopController::class, 'edit'])->name('shop.product.edit');
    Route::put('/shop/product/{id}', [ShopController::class, 'update'])->name('shop.product.update');
    Route::delete('/shop/product/{id}', [ShopController::class, 'destroy'])->name('shop.product.destroy');

    // Blog routes
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/create', [BlogController::class, 'create'])->name('blog.create');
    Route::post('/blog', [BlogController::class, 'store'])->name('blog.store');
    Route::get('/blog/{id}', [BlogController::class, 'show'])->name('blog.show');
    Route::get('/blog/{id}/edit', [BlogController::class, 'edit'])->name('blog.edit');
    Route::put('/blog/{id}', [BlogController::class, 'update'])->name('blog.update');
    Route::delete('/blog/{id}', [BlogController::class, 'destroy'])->name('blog.destroy');

    // Blog comment routes
    Route::post('/blog/{id}/comments', [BlogController::class, 'storeComment'])->name('blog.comments.store');
    Route::delete('/blog/{postId}/comments/{commentId}', [BlogController::class, 'destroyComment'])->name('blog.comments.destroy');

    // Payment routes
    Route::get('/payment', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
    Route::post('/payment/create-intent', [PaymentController::class, 'createPaymentIntent'])->name('payment.create-intent');
    Route::post('/payment/webhook', [PaymentController::class, 'handleWebhook'])->name('payment.webhook');
    Route::get('/payment/success', function () {
        return view('payment.success');
    })->name('payment.success');
});
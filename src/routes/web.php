<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\MylistController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ShippingAddressController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ItemController::class, 'index']) 
    ->middleware('profile.completed')
    ->name('items.index');

Route::middleware(['auth', 'verified', 'profile.completed'])
    ->prefix('mypage')
    ->name('mypage.')
    ->group(function () {
        Route::get('/', [MypageController::class, 'index'])->name('index');
        Route::get('/profile', [MypageController::class, 'editProfile'])->name('profile.edit');
        Route::post('/profile', [MypageController::class, 'updateProfile'])->name('profile.update');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');
});

Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');

Route::middleware('auth', 'verified')->group(function () {
    Route::post('/items/{item}/mylist/toggle', [MylistController::class, 'toggle'])
        ->name('items.mylist.toggle');
});

Route::middleware('auth', 'verified')->group(function () {
    Route::post('/item/{item}/comments', [CommentController::class, 'store'])
        ->name('comments.store');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/purchase/{item}', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/purchase/{item}/shipping', [ShippingAddressController::class, 'edit'])
        ->name('purchase.shipping.edit');

    Route::post('/purchase/{item}/shipping', [ShippingAddressController::class, 'update'])
        ->name('purchase.shipping.update');
});

Route::get('/purchase/{item}/success', [PurchaseController::class, 'success'])
    ->name('purchase.success');
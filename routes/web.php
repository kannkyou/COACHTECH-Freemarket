<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\MylistController;

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

Route::middleware(['auth', 'profile.completed'])
    ->prefix('mypage')
    ->name('mypage.')
    ->group(function () {
        Route::get('/', [MypageController::class, 'index'])->name('index');
        Route::get('/profile', [MypageController::class, 'editProfile'])->name('profile.edit');
        Route::post('/profile', [MypageController::class, 'updateProfile'])->name('profile.update');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');
});

Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');

Route::middleware('auth')->group(function () {
    Route::post('/items/{item}/mylist/toggle', [MylistController::class, 'toggle'])
        ->name('items.mylist.toggle');
});
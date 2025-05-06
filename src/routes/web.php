<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::middleware('auth')->group(function () {
    Route::get('/mypage/profile', [ItemController::class, 'update'])->name('profile.show');
    Route::post('/mypage/profile', [ItemController::class, 'updateProfile']);
    Route::middleware('address.complete')->group(function () {
        Route::post('/item/{item_id}/comment', [ItemController::class, 'comment']);
        Route::post('/like', [ItemController::class, 'store']);
        Route::get('/purchase/{item_id}', [ItemController::class, 'buy']);
        Route::post('/purchase/{item_id}', [ItemController::class, 'buyItem']);
        Route::get('/purchase/address/{item_id}', [ItemController::class, 'change']);
        Route::post('/purchase/address/{item_id}', [ItemController::class, 'changeAddress']);
        Route::get('/sell', [ItemController::class, 'sell']);
        Route::post('/sell', [ItemController::class, 'sellItem']);
        Route::get('/mypage', [ItemController::class, 'profile']);
        Route::get('/mypage/{page}', [ItemController::class, 'deal']);
    });
});

Route::get('/', [ItemController::class, 'index'])->name('top');
Route::get('/item/{item_id}', [ItemController::class, 'detail']);
Route::get('/mylist', [ItemController::class, 'mylist'])->name('mylist');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');

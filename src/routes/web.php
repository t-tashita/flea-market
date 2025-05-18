<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::middleware('auth')->group(function () {
    Route::get('/mypage/profile', [ItemController::class, 'update'])->name('profile.show');
    Route::post('/mypage/profile', [ItemController::class, 'updateProfile']);
    Route::post('/item/{item_id}/comment', [ItemController::class, 'comment']);
    Route::post('/item/{item_id}/like', [ItemController::class, 'like']);
    Route::get('/purchase/{item_id}', [ItemController::class, 'buy']);
    Route::post('/purchase/{item_id}', [ItemController::class, 'buyItem']);
    Route::get('/purchase/address/{item_id}', [ItemController::class, 'change']);
    Route::post('/purchase/address/{item_id}', [ItemController::class, 'changeAddress']);
    Route::get('/sell', [ItemController::class, 'sell']);
    Route::post('/sell', [ItemController::class, 'sellItem']);
    Route::get('/mypage', [ItemController::class, 'profile']);
    Route::get('/mypage/{page}', [ItemController::class, 'deal'])->name('mypage.page');
});

Route::get('/', [ItemController::class, 'index'])->name('top');
Route::get('/{page}', [ItemController::class, 'mylist'])->name('mylist');
Route::get('/item/{item_id}', [ItemController::class, 'detail']);
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');

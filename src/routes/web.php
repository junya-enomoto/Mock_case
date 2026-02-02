<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use Laravel\Fortify\Fortify;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [ItemController::class, 'index'])->name('item.index');
Route::get('/item/{item_id}', [ItemController::class, 'detail'])->name('item.detail');


Route::middleware(['auth:web', 'verified'])->group(function () {
  
    Route::get('/sell', [ItemController::class, 'showSell'])->name('item.sell');
    Route::post('/sell', [ItemController::class, 'store'])->name('item.store');

    Route::get('/purchase/{item_id}', [PurchaseController::class, 'index'])->name('purchase.index');
    Route::post('/purchase/{item_id}/process', [PurchaseController::class, 'store'])->name('purchase.process'); 
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'showPurchaseAddress'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
    Route::get('/purchase/{item_id}/success', [PurchaseController::class, 'success'])->name('purchase.success');

    Route::get('/mypage', [MyPageController::class, 'index'])->name('mypage');
    Route::get('/mypage/profile', [MyPageController::class, 'edit'])->name('mypage.edit');
    Route::post('/mypage/profile', [MyPageController::class, 'update'])->name('mypage.update');

    Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])->name('comment.store');

    Route::post('/like/{item_id}', [LikeController::class, 'store'])->name('like.store');
    Route::delete('/like/{item_id}', [LikeController::class, 'destroy'])->name('like.destroy');
});

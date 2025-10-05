<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;


Route::controller(AuthController::class)->group(function(){
    Route::get('/login','create')->name('login');
});

Route::middleware('auth')->group(function () {
    Route::controller(DashboardController::class)->group(function(){
        Route::get('/','index')->name('dashboard');
    });

    Route::controller(UserController::class)->name('user.')->group(function(){
        Route::get('/user-list','index')->name('user-list');
        Route::post('/save-user','saveUser')->name('save-user');
        Route::put('/update-user/{id}','updateUser')->name('update-user');
        Route::get('/delete-user/{id}','deleteUser')->name('delete-user');
    });

    Route::controller(ProductController::class)->name('product.')->group(function(){
        Route::get('/product-list','index')->name('product-list');
        Route::get('/product-create','createProduct')->name('create-product');
        Route::get('/product-details/{slug}','editProduct')->name('product-details');
        Route::post('/save-product','saveProduct')->name('save-product');
        Route::put('/update-product/{slug}','updateProduct')->name('update-product');
        Route::get('/delete-product/{id}','deleteProduct')->name('delete-product');
        Route::get('/product-restore/{id}', 'restore')->name('product-restore');

    });
});


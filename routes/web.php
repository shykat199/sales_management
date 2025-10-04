<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;


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
});


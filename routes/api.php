<?php

use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:user')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::prefix('/posts')->name('post.')->group(function() {
        Route::get('/show', [PostController::class, 'show'])->name('show');
        Route::post('/register', [PostController::class, 'store'])->name('store');
        Route::post('/update', [PostController::class, 'update'])->name('update');
        Route::delete('/delete', [PostController::class, 'delete'])->name('delete');    
    });
});

Route::prefix('/posts')->name('post.')->group(function() {
Route::get('/', [PostController::class, 'index'])->name('index');
} );
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');


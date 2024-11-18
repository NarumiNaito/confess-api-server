<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\ForgiveController;
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
    Route::get('/user', [AuthController::class, 'user'])->name('user');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::prefix('/posts')->name('post.')->group(function() {
        Route::get('/myIndex', [PostController::class, 'myIndex'])->name('myIndex');
        Route::get('/fulfillmentIndex', [PostController::class, 'fulfillmentIndex'])->name('fulfillmentIndex');
        Route::get('/show', [PostController::class, 'show'])->name('show');
        Route::post('/register', [PostController::class, 'store'])->name('store');
        Route::post('/update', [PostController::class, 'update'])->name('update');
        Route::delete('/delete', [PostController::class, 'delete'])->name('delete');  
    });  
    Route::prefix('/comments')->name('comment.')->group(function() {
        Route::get('/index/{id}', [CommentController::class, 'index'])->name('index');
        Route::post('/register', [CommentController::class, 'store'])->name('store');
        Route::delete('/delete', [CommentController::class, 'delete'])->name('delete');  
    });
    Route::prefix('/forgives')->name('forgive.')->group(function() {
        Route::post('/toggle', [ForgiveController::class, 'toggle'])->name('toggle');
    });
});

Route::prefix('/forgives')->name('forgive.')->group(function() {
    Route::get('/index', [ForgiveController::class, 'index'])->name('index');
});

Route::prefix('/posts')->name('post.')->group(function() {
    Route::get('/', [PostController::class, 'index'])->name('index');
} );

Route::prefix('/comments')->name('comment.')->group(function() {
    Route::get('/show/{id}', [CommentController::class, 'show'])->name('show');
});

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');


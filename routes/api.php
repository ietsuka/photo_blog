<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PhotoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/user', fn() => Auth::user())->name('user');


Route::get('/photos', [PhotoController::class, 'index'])->name('photo.index');
Route::get('/photos/{id}', [PhotoController::class, 'show'])->name('photo.show');
Route::post('/photos', [PhotoController::class, 'create'])->name('photo.create');

Route::post('/photos/{photo}/comments', [PhotoController::class, 'addComment'])->name('photo.comment');

Route::put('/photos/{id}/like', [PhotoController::class, 'like'])->name('photo.like');
Route::delete('/photos/{id}/like', [PhotoController::class, 'unlike']);

Route::get('/reflesh-token', function (Illuminate\Http\Request $request) {
  $request->session()->regenerateToken();

  return response()->json();
});
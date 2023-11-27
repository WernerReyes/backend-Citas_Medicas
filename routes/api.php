<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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

Route::prefix('/usuarios')->group(function () {
  Route::get('/', [UserController::class, 'index']);
  Route::get('/{id}', [UserController::class, 'show']);
  Route::post('/', [UserController::class, 'store']);
  Route::put('/{id}',[UserController::class, 'update'])->middleware('auth.sanctum');
  Route::delete('/{id}',[UserController::class, 'destroy'])->middleware('auth.sanctum');
});

Route::prefix('/auth')->group(function () {
  Route::post('/login', [AuthController::class, 'login']);
  Route::get('/verificar-token', [AuthController::class, 'verificarToken'])->middleware('auth.sanctum');
  Route::get('/renovar-token', [AuthController::class, 'renovarToken'])->middleware('auth.sanctum');
});



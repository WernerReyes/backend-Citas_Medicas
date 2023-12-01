<?php

use App\Http\Controllers\AdministratorControlller;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserController;
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

Route::prefix('/auth')->group(function () {
  Route::post('/login', [AuthController::class, 'login']);
  Route::post('/login-personal-clinica', [AuthController::class, 'loginPersonalClinica']);
  Route::get('/verificar-token', [AuthController::class, 'verificarToken'])->middleware('auth.sanctum');
  Route::get('/renovar-token', [AuthController::class, 'renovarToken'])->middleware('auth.sanctum');
});

Route::prefix('/user')->group(function () {
  Route::get('/', [UserController::class, 'index']);
  Route::get('/{id}', [UserController::class, 'show']);
  Route::post('/', [UserController::class, 'store']);
  Route::put('/{id}',[UserController::class, 'update'])->middleware('auth.sanctum');
  Route::delete('/{id}',[UserController::class, 'destroy'])->middleware('auth.sanctum');
});

Route::prefix('/doctor')->group(function () {
  Route::get('/', [DoctorController::class, 'index']);
  Route::get('/{id}', [DoctorController::class, 'show']);
  Route::post('/', [DoctorController::class, 'store']);
  Route::put('/{id}',[DoctorController::class, 'update'])->middleware('auth.sanctum');
  Route::delete('/{id}',[DoctorController::class, 'destroy'])->middleware('auth.sanctum');
});

Route::prefix('/administrator')->group(function () {
  Route::get('/', [AdministratorControlller::class, 'index']);
  Route::get('/{id}', [AdministratorControlller::class, 'show']);
  Route::post('/', [AdministratorControlller::class, 'store']);
  Route::put('/{id}',[AdministratorControlller::class, 'update'])->middleware('auth.sanctum');
  Route::delete('/{id}',[AdministratorControlller::class, 'destroy'])->middleware('auth.sanctum');
});

Route::prefix('/medical-appointment')->group(function () {
  Route::get('/', [MedicalAppointmentController::class, 'index']);
  Route::get('/{id}', [MedicalAppointmentController::class, 'show']);
  Route::post('/', [MedicalAppointmentController::class, 'store']);
  Route::put('/{id}',[MedicalAppointmentController::class, 'update'])->middleware('auth.sanctum');
  Route::delete('/{id}',[MedicalAppointmentController::class, 'destroy'])->middleware('auth.sanctum');
});

Route::prefix('/especialty')->group(function () {
  Route::get('/', [SpecialtyController::class, 'index']);
  Route::get('/{id}', [SpecialtyController::class, 'show']);
  Route::post('/', [SpecialtyController::class, 'store']);
  Route::put('/{id}',[SpecialtyController::class, 'update'])->middleware('auth.sanctum');
  Route::delete('/{id}',[SpecialtyController::class, 'destroy'])->middleware('auth.sanctum');
});

Route::prefix('/upload')->middleware('auth.sanctum')->group(function(){
 Route::get('/{id}', [UploadController::class, 'show']);
 Route::post('/{folder}/{model}/{id}', [UploadController::class, 'store']);
 Route::put('/{folder}/{model}/{id}', [UploadController::class, 'update']);
});



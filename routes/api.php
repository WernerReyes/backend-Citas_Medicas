<?php

use App\Http\Controllers\AdministratorControlller;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\MedicalAppintmentHistoryController;
use App\Http\Controllers\MedicalAppointmentController;
use App\Http\Controllers\MedicalScheduleController;
use App\Http\Controllers\PaymentController;
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
  Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth.sanctum');
  Route::get('/verificar-token', [AuthController::class, 'verificarToken'])->middleware('auth.sanctum');
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

Route::prefix('/schedule')->group(function () {
  Route::get('/', [MedicalScheduleController::class, 'index']);
  Route::get('/{id}', [MedicalScheduleController::class, 'show']);
  Route::post('/', [MedicalScheduleController::class, 'store']);
  Route::put('/{id}',[MedicalScheduleController::class, 'update']);
  Route::delete('/{id}',[MedicalScheduleController::class, 'destroy']);
});

Route::prefix('/medical-appointment')->group(function () {
  Route::get('/', [MedicalAppointmentController::class, 'index']);
  Route::get('/{id}', [MedicalAppointmentController::class, 'show']);
  Route::post('/', [MedicalAppointmentController::class, 'store']);
  Route::put('/{id}',[MedicalAppointmentController::class, 'update'])->middleware('auth.sanctum');
  Route::put('/complete/{id}',[MedicalAppointmentController::class, 'complete'])->middleware('auth.sanctum');
  Route::delete('/{id}/{idSchedule}',[MedicalAppointmentController::class, 'destroy'])->middleware('auth.sanctum');
});

Route::prefix('/specialty')->group(function () {
  Route::get('/', [SpecialtyController::class, 'index']);
  Route::get('/{id}', [SpecialtyController::class, 'show']);
  Route::post('/', [SpecialtyController::class, 'store']);
  Route::put('/{id}',[SpecialtyController::class, 'update'])->middleware('auth.sanctum');
  Route::delete('/{id}',[SpecialtyController::class, 'destroy'])->middleware('auth.sanctum');
});

Route::prefix('/upload')->middleware('auth.sanctum')->group(function(){
 Route::post('/{folder}/{id}/{model}', [UploadController::class, 'store']);
 Route::put('/{folder}/{id}/{model}', [UploadController::class, 'update']);
});

Route::prefix('/payment')->group(function () {
  Route::get('/', [PaymentController::class, 'index']);
  Route::get('/earnings', [PaymentController::class, 'earnings']);
  Route::get('/{id}', [PaymentController::class, 'show']);
  Route::post('/', [PaymentController::class, 'store']);
  // Route::put('/{id}',[PaymentController::class, 'update'])->middleware('auth.sanctum');
  // Route::delete('/{id}',[PaymentController::class, 'destroy'])->middleware('auth.sanctum');
});

Route::prefix('/medical-appointment-history')->group(function () {
  Route::get('/', [MedicalAppintmentHistoryController::class, 'index']);
  Route::get('/{id}', [MedicalAppintmentHistoryController::class, 'show']);
  // Route::post('/', [MedicalAppintmentHistoryController::class, 'store']);
  // Route::put('/{id}',[MedicalAppintmentHistoryController::class, 'update'])->middleware('auth.sanctum');
  // Route::delete('/{id}',[MedicalAppintmentHistoryController::class, 'destroy'])->middleware('auth.sanctum');
});





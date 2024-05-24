<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::middleware('auth')->group(function () {
    Route::get('/', [AttendanceController::class, 'index']);
    Route::post('/work', [AttendanceController::class, 'work_start']);
    Route::patch('/work', [AttendanceController::class, 'work_end']);
    Route::post('/rest', [RestController::class, 'rest_start']);
    Route::patch('/rest', [RestController::class, 'rest_end']);
    Route::get('/attendance', [AttendanceController::class, 'date']);
    Route::post('/before', [AttendanceController::class, 'before_date']);
    Route::post('/after', [AttendanceController::class, 'after_date']);
});

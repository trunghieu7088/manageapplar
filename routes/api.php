<?php

use App\Http\Controllers\StudentController;
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

/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/addstudent', [StudentController::class, 'addstudent']);

    Route::post('/logout', [UserController::class, 'logout']);    

    Route::get('/getallstudent/{status_record}', [StudentController::class, 'get_all_student']);    

    Route::get('/getstudent/{id}', [StudentController::class, 'get_student']);    

    Route::put('/movestudent/{id}', [StudentController::class, 'move_student']);    
    
});

Route::post('/authlogin', [UserController::class, 'authlogin']);

Route::get('/getuserinfo', [UserController::class, 'getUserInfo']);


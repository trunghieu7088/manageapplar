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

//Route::get('students', [StudentController::class, 'show']);

Route::resource('students', StudentController::class);

Route::get('/generatetoken', [UserController::class, 'generateToken']);

Route::post('/authlogin', [UserController::class, 'authlogin']);

/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/testauth', [StudentController::class, 'testauth']);

    Route::post('/addstudent', [StudentController::class, 'addstudent']);

    Route::post('/logout', [UserController::class, 'logout']);    
    
});

Route::get('/getuserinfo', [UserController::class, 'getUserInfo']);

Route::get('/testcarbon', [UserController::class, 'testcarbon']);
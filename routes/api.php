<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::group(['prefix' => 'auth'], function() {
    Route::post('login', [UserController::class, 'login']);
    Route::post('register', [UserController::class, 'store']);
});
Route::resource('users', UserController::class);
Route::resource('formations', 'App\Http\Controllers\FormationController');
Route::resource('cours', 'App\Http\Controllers\CoursController');
Route::resource('inscription', 'App\Http\Controllers\InscriptionController');
Route::resource('lier', 'App\Http\Controllers\CoursFormationsController');
Route::post('/searchFormation', 'App\Http\Controllers\FormationController@Search'); // fonction search dans formation
Route::post('/searchCour', 'App\Http\Controllers\CoursController@Search'); // fonction search dans cours

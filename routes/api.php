<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\SupplierController;
use App\Http\Controllers\API\MachineController;
use App\Http\Controllers\API\PreventionController;
use App\Http\Controllers\API\CorrectionController;
use App\Http\Controllers\API\EventController;

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

Route::put('prevention/state/{id}', [PreventionController::class, 'updatePreventionState'])->middleware('auth:sanctum');
Route::put('correction/state/{id}', [CorrectionController::class, 'updateCorrectionState'])->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResources([
        'user' => UserController::class,
        'supplier' => SupplierController::class,
        'machine' => MachineController::class,
        'prevention' => PreventionController::class,
        'correction' => CorrectionController::class,
    ]);
});
<?php

use App\Http\Controllers\Api\V1\Admin\CropController;
use App\Http\Controllers\Api\V1\Admin\CropVarietyController;
use App\Http\Controllers\Api\V1\Admin\GrowthStageController;
use App\Http\Controllers\Api\V1\Farmer\CropCatalogController;
use App\Http\Controllers\Api\V1\Farmer\FarmController;
use App\Http\Controllers\Api\V1\Farmer\FieldController;
use App\Http\Controllers\Api\V1\Farmer\FieldMediaController;
use App\Http\Controllers\Api\V1\Farmer\MediaController;
use App\Http\Controllers\Api\V1\Farmer\PlantProductionController;
use App\Http\Controllers\Api\V1\Farmer\PlantProductionMediaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    Route::prefix('farmer')->group(function () {
        Route::apiResource('farms', FarmController::class);
        Route::apiResource('farms.fields', FieldController::class)->shallow();

        Route::get('crops', [CropCatalogController::class, 'index']);
        Route::get('crops/{crop}', [CropCatalogController::class, 'show']);

        Route::get('fields/{field}/media', [FieldMediaController::class, 'index']);
        Route::post('fields/{field}/media', [FieldMediaController::class, 'store']);

        Route::post('media/{media}/replace', [MediaController::class, 'replace']);
        Route::delete('media/{media}', [MediaController::class, 'destroy']);

        Route::apiResource('fields.plant-productions', PlantProductionController::class)->shallow();

        Route::post('plant-productions/{plant_production}/stage', [PlantProductionController::class, 'changeStage']);
        Route::post('plant-productions/{plant_production}/harvest', [PlantProductionController::class, 'harvest']);
        Route::post('plant-productions/{plant_production}/fail', [PlantProductionController::class, 'fail']);

        Route::get('plant-productions/{plant_production}/media', [PlantProductionMediaController::class, 'index']);
        Route::post('plant-productions/{plant_production}/media', [PlantProductionMediaController::class, 'store']);

    });

    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::apiResource('crops', CropController::class);
        Route::apiResource('crops.varieties', CropVarietyController::class)->shallow();
        Route::apiResource('crops.growth-stages', GrowthStageController::class)->shallow();
    });

});

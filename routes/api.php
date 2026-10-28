<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\LaboratoryController;
use App\Http\Controllers\Admin\LabSampleTypeController;
use App\Http\Controllers\Admin\LabTestController;
use App\Http\Controllers\Admin\PharmacyController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\RegistrationReviewController;
use App\Http\Controllers\Admin\StaffAccountController;
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
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistrationLookupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication (colleague)
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('register',   [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('login',      [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:10,1');
    Route::post('resend-otp', [AuthController::class, 'resendOtp'])->middleware('throttle:3,1');

    Route::middleware(['auth:sanctum', 'role'])->post('logout', [AuthController::class, 'logout']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('addresses', AddressController::class);
});

// قوائم شاشة التسجيل (مناطق، اختصاصات، أنواع إنتاج، صيدليات، مخابر)
Route::get('lookups/registration', RegistrationLookupController::class)->middleware('throttle:30,1');

/*
|--------------------------------------------------------------------------
| Admin: pharmacy, laboratory, registrations (colleague)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware(['auth:sanctum', 'role:admin'])
    ->group(function () {
        // الصيدلية
        Route::apiResource('product-categories', ProductCategoryController::class)
            ->parameters(['product-categories' => 'product_category']);
        Route::apiResource('products', ProductController::class);
        Route::apiResource('pharmacies', PharmacyController::class);
        Route::apiResource('laboratories', LaboratoryController::class);

        // المخبر
        Route::apiResource('lab-sample-types', LabSampleTypeController::class)
            ->parameters(['lab-sample-types' => 'sample_type']);
        Route::apiResource('lab-tests', LabTestController::class)
            ->parameters(['lab-tests' => 'lab_test']);
        Route::apiResource('regions', RegionController::class);

        Route::get('registrations', [RegistrationReviewController::class, 'index']);
        Route::post('registrations/{user}/approve', [RegistrationReviewController::class, 'approve']);
        Route::post('registrations/{user}/reject', [RegistrationReviewController::class, 'reject']);

        Route::post('pharmacies/{pharmacy}/staff', [StaffAccountController::class, 'storePharmacyStaff']);
        Route::post('laboratories/{laboratory}/staff', [StaffAccountController::class, 'storeLabEmployee']);
    });

/*
|--------------------------------------------------------------------------
| API v1: farms, fields, crop catalog, plant production (ours)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    Route::prefix('farmer')->middleware('role:farmer')->group(function () {
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

    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::apiResource('crops', CropController::class);
        Route::apiResource('crops.varieties', CropVarietyController::class)->shallow();
        Route::apiResource('crops.growth-stages', GrowthStageController::class)->shallow();
    });
});

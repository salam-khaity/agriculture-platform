<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use App\Models\Pharmacy;
use App\Models\ProductionType;
use App\Models\Region;
use App\Models\Specialization;
use Illuminate\Support\Facades\Cache;

class RegistrationLookupController extends Controller
{
    /**
     * بيانات القوائم المنسدلة لشاشة التسجيل
     * (عامة، بدون بيانات حساسة).
     */
    public function __invoke()
    {
        $data = Cache::remember(
            'lookups:registration',
            now()->addHour(),
            fn () => [
                'regions' => Region::active()
                    ->orderBy('name')
                    ->get(['id', 'name']),

                'specializations' => Specialization::active()
                    ->orderBy('name')
                    ->get(['id', 'name']),

                'production_types' => ProductionType::active()
                    ->orderBy('name')
                    ->get(['id', 'name']),

                'pharmacies' => Pharmacy::active()
                    ->orderBy('name')
                    ->get(['id', 'name']),

                'laboratories' => Laboratory::active()
                    ->orderBy('name')
                    ->get(['id', 'name']),
            ]
        );

        return response()->json(['status' => 200] + $data);
    }
}

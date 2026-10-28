<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\DeliveryAgentProfile;
use App\Models\Specialization;
use App\Models\SpecialistProfile;
use App\Models\User;
use App\Services\RegistrationReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegistrationReviewController extends Controller
{
    public function __construct(
        protected RegistrationReviewService $service
    ) {}

    /** GET /admin/registrations?status=pending&role=delivery_agent */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(['pending', 'rejected', 'active'])],
            'role'   => ['nullable', Rule::in(RegistrationReviewService::reviewableValues())],
        ]);

        $page = User::query()
            ->whereIn('role', isset($filters['role'])
                ? [$filters['role']]
                : RegistrationReviewService::reviewableValues())
            ->where('status', $filters['status'] ?? UserStatus::Pending->value)
            ->orderBy('created_at')
            ->paginate(20);

        $users = collect($page->items());
        $ids   = $users->pluck('id');

        $specialists = SpecialistProfile::with(['productionTypes', 'regions'])
            ->whereIn('user_id', $ids)->get()->keyBy('user_id');

        $agents = DeliveryAgentProfile::with('regions')
            ->whereIn('user_id', $ids)->get()->keyBy('user_id');

        $specializations = Specialization::whereIn('id', $specialists->pluck('specialization_id')->filter())
            ->pluck('name', 'id');

        $named = fn ($items) => $items->map(fn ($i) => ['id' => $i->id, 'name' => $i->name])->values();

        $data = $users->map(function (User $u) use ($specialists, $agents, $specializations, $named) {
            $row = [
                'id'             => $u->id,
                'name'           => $u->name,
                'email'          => $u->email,
                'role'           => $u->role->value,
                'status'         => $u->status->value,
                'email_verified' => (bool) $u->email_verified_at,
                'registered_at'  => $u->created_at,
            ];

            if ($u->role === UserRole::AgriculturalSpecialist && ($p = $specialists->get($u->id))) {
                $row['specialization']   = $specializations->get($p->specialization_id);
                $row['production_types'] = $named($p->productionTypes);
                $row['work_regions']     = $named($p->regions);
            }

            if ($u->role === UserRole::DeliveryAgent && ($p = $agents->get($u->id))) {
                $row['work_regions'] = $named($p->regions);
            }

            return $row;
        });

        return response()->json([
            'status' => 200,
            'data'   => $data,
            'meta'   => [
                'current_page' => $page->currentPage(),
                'last_page'    => $page->lastPage(),
                'total'        => $page->total(),
            ],
        ]);
    }

    public function approve(User $user): JsonResponse
    {
        return $this->respond($this->service->approve($user));
    }

    public function reject(User $user): JsonResponse
    {
        return $this->respond($this->service->reject($user));
    }

    private function respond(array $result): JsonResponse
    {
        return response()->json($result, $result['status'] ?? 200);
    }
}

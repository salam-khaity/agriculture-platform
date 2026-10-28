<?php

namespace App\Services;

use App\Models\Address;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class AddressService
{
    public function index(
        User $user,
        array $filters = []
    ): LengthAwarePaginator {
        return $this->accessibleAddresses($user)
            ->with('region')
            ->when(
                !empty($filters['search']),
                function (Builder $query) use ($filters) {
                    $search = $filters['search'];

                    $query->where(function (Builder $query) use ($search) {
                        $query->where('label', 'like', "%{$search}%")
                            ->orWhere(
                                'address_line',
                                'like',
                                "%{$search}%"
                            );
                    });
                }
            )
            ->when(
                !empty($filters['region_id']),
                fn (Builder $query) =>
                $query->where('region_id', $filters['region_id'])
            )
            ->latest('id')
            ->paginate(
                min(max((int) ($filters['per_page'] ?? 15), 1), 100)
            );
    }

    public function store(User $user, array $data): Address
    {
        $address = Address::query()->create([
            ...$data,
            'user_id' => $user->id,
        ]);

        return $address->load('region');
    }

    public function show(User $user, int $id): Address
    {
        return $this->findAccessibleAddress($user, $id)
            ->load('region');
    }

    public function update(
        User $user,
        int $id,
        array $data
    ): Address {
        $address = $this->findAccessibleAddress($user, $id);

        $address->update($data);

        return $address->refresh()->load('region');
    }

    public function destroy(User $user, int $id): void
    {
        $address = $this->findAccessibleAddress($user, $id);

        if (
            $address->pharmacies()->exists()
            || $address->laboratories()->exists()
        ) {
            throw ValidationException::withMessages([
                'address' => [
                    'Cannot delete this address because it is linked to a pharmacy or laboratory.',
                ],
            ]);
        }

        $address->delete();
    }

    private function accessibleAddresses(User $user): Builder
    {
        if ($this->isAdmin($user)) {
            return Address::query();
        }

        return Address::query()->where('user_id', $user->id);
    }

    private function findAccessibleAddress(
        User $user,
        int $id
    ): Address {
        return $this->accessibleAddresses($user)->findOrFail($id);
    }

    private function isAdmin(User $user): bool
    {
        $role = $user->role;

        if ($role instanceof \BackedEnum) {
            $role = $role->value;
        }

        return in_array(
            $role,
            ['admin', 'super_admin'],
            true
        );
    }
}

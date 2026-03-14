<?php

namespace App\Services;

use App\Repositories\RoleRepository;
use Illuminate\Support\Facades\DB;

class RoleService
{

    public function __construct(
        protected RoleRepository $roleRepository,
    ) {}

    public function list()
    {
        return $this->roleRepository->list();
    }

    public function createRole($dto)
    {
        return DB::transaction(function () use ($dto) {
            $role = $this->roleRepository->create([
                'name' => $dto->name
            ]);
            return $role;
        });
    }

    public function updateRole($id, $dto)
    {
        return DB::transaction(function () use ($id, $dto) {
            $role = $this->roleRepository->findOrFail($id);
            $role = $this->roleRepository->update($role, [
                'name' => $dto->name
            ]);
            return $role;
        });
    }

    public function deleteRole($id)
    {
        return DB::transaction(function () use ($id) {
            $role = $this->roleRepository->findOrFail($id);
            $this->roleRepository->delete($role);
            return true;
        });
    }

    public function getRole($id)
    {
        $role = $this->roleRepository->findOrFail($id);
        return $role;
    }
}

<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository
{
    public function list()
    {
        return Role::all();
    } 

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function findById(int $roleId): ?Role
    {
        return Role::find($roleId);
    }

    public function update(Role $role, array $data): Role
    {
        $role->update($data);
        return $role;
    }

    public function delete(Role $role): bool
    {
        return $role->delete();
    }

    public function findOrFail(int $roleId): Role
    {
        return Role::findOrFail($roleId);
    }
}

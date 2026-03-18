<?php

namespace App\Http\Controllers;

use App\DTO\CreateHeroDTO;
use App\Http\Requests\CreateHeroRequest;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function __construct(
        protected RoleService $roleService
    ) {}

    public function index()
    {
        try {
            $roles = $this->roleService->list();
            return $this->success($roles, 'Roles retrieved successfully');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateHeroRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $dto = new CreateHeroDTO(
                $validated['name']
            );
            $role = $this->roleService->createRole($dto);
            return $this->success($role, 'Role created successfully');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $role = $this->roleService->getRole($id);
            return $this->success($role, 'Role retrieved successfully');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateHeroRequest $request, int $id)
    {
        try {
            $this->roleService->updateRole($id, $request->all());
            return $this->success($request->all(), 'Role updated successfully');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $this->roleService->deleteRole($id);
            return $this->success(null, 'Role deleted successfully');
        } catch (\Throwable $th) {
             return $this->error($th->getMessage(), 400);
        }
    }
}

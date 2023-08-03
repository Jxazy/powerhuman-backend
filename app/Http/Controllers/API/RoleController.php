<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

class RoleController extends Controller
{
    public function fetch(Request $request) 
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        $roleQuery = Role::query();
        $with_responsibilities = $request->input('with_responsibilities', false);

        // get single data
        if ($id) {
            $role = $roleQuery->with('responsibilities')->find($id);

            if ($role) {
                return ResponseFormatter::success(
                    $role,
                    'Role found'
                );
            }
            return ResponseFormatter::error(
                'Role not found',
                404
            );
        }

        // get multiple data
        $roles = $roleQuery->where('company_id', $request->company_id);

        if ($name) {
            $roles->where('name', 'like', '%' . $name . '%');
        }

        if ($with_responsibilities) {
            $roles->with('responsibilities');
        }

        return ResponseFormatter::success(
            $roles->paginate($limit),
            'Roles found'
        );
        
    }

    public function create(CreateRoleRequest $request)
    {
        try {

            // Create Team
            $role = Role::create([
                'name' => $request->input('name'),
                'company_id' => $request->company_id,
            ]);

            if (!$role) {
                throw new Exception('Failed to create Role');
            }

            return ResponseFormatter::success(
                $role,
                'Role created'
            );
        } catch (\Exception $th) {
            return ResponseFormatter::error(
                $th->getMessage(),
                500
            );
        }
    }


    public function update(UpdateRoleRequest $request, $id)
    {
        try {

            // Get Role by ID
            $role = Role::find($id);

            if (!$role) {
                throw new Exception('Role not found');
            }

            // Update Role
            $role->update([
                'name' => $request->input('name'),
                'company_id' => $request->company_id,
            ]);

            return ResponseFormatter::success(
                $role,
                'Role updated'
            );
        } catch (\Exception $th) {
            return ResponseFormatter::error(
                $th->getMessage(),
                500
            );
        }
    }

    public function destroy(Request $request, $id)
    {
        try {

            // Get Role by ID
            $role = Role::find($id);

            // Check if Role exists
            if (!$role) {
                throw new Exception('Role not found');
            }

            // Delete Role
            $role->delete();

            return ResponseFormatter::success(
                null,
                'Role deleted'
            );
        } catch (\Exception $th) {
            return ResponseFormatter::error(
                $th->getMessage(),
                500
            );
        }
    }
}

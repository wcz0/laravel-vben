<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Lauthz\Facades\Enforcer;
use stdClass;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'required|integer|min:1',
            'pageSize' => 'required|integer',
            'name' => 'nullable|string',
            'value' => 'nullable|string',
            'status' => 'nullable|integer',
        ]);
        if ($validator->fails())
        {
            return $this->fails($validator->errors());
        }
        $query = Role::query();
        if ($request->filled('name'))
        {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('value'))
        {
            $query->where('value', 'like', '%' . $request->value . '%');
        }
        if ($request->filled('status'))
        {
            $query->where('status', $request->status);
        }
        $result = new stdClass();
        $result->total = $query->count();
        $roles = $query->offset(($request->page - 1) * $request->pageSize)
            ->limit($request->pageSize)
            ->get([
                'id',
                'name',
                'value',
                'desc',
                'status',
                'created_at',
            ]);
        foreach ($roles as $role)
        {
            $permissions = Enforcer::getPermissionsForUser($role->value);
            $p_ids = Permission::whereIn('permission', array_column($permissions, 2))->pluck('id');
            $role->permissions = $p_ids;
        }
        $result->items = $roles;
        return $this->success('success', $result);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'name' => 'required|string',
            'value' => 'required|string',
            'desc' => 'nullable|string',
            'status' => 'required|integer',
            'permissions' => 'array',
        ]);
        if ($validator->fails())
        {
            return $this->fails($validator->errors());
        }
        $role = Role::find($request->id);
        if (!$role)
        {
            return $this->fails('更新失败, 角色不存在');
        }
        DB::beginTransaction();
        try
        {
            // 删除角色所有权限
            Enforcer::deletePermissionsForUser($request->value);
            // 添加角色权限
            $permissions = Permission::whereIn('id', $request->permissions)
                ->get([
                'id',
                'permission'
            ]);
            foreach ($permissions as $permission)
            {
                Enforcer::addPermissionForUser($request->value, '', $permission->permission);
            }
            $role->name = $request->name;
            $role->value = $request->value;
            $role->desc = $request->desc;
            $role->status = $request->status;
            $role->save();
            DB::commit();
        }
        catch (\Throwable $th)
        {
            DB::rollBack();
            return $this->fails('更新失败');
        }
        return $this->success('更新成功', []);
    }

    public function setStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'status' => 'required|integer',
        ]);
        if ($validator->fails())
        {
            return $this->fails($validator->errors());
        }
        $role = Role::find($request->id);
        if (!$role)
        {
            return $this->fails('设置失败, 角色不存在');
        }
        $role->status = $request->status;
        $role->save();
        return $this->success('设置成功', []);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'value' => 'required|string',
            'desc' => 'nullable|string',
            'status' => 'required|integer',
        ]);
        if ($validator->fails())
        {
            return $this->fails($validator->errors());
        }
        DB::beginTransaction();
        try
        {
            // 添加角色
            Enforcer::addRoleForUser($request->name, $request->value);
            // 添加角色权限
            $permissions = Permission::whereIn('id', $request->permissions)
                ->get([
                'id',
                'permission'
            ]);
            foreach ($permissions as $permission)
            {
                Enforcer::addPermissionForUser($request->value, '', $permission->permission);
            }
            $role = new Role();
            $role->name = $request->name;
            $role->value = $request->value;
            $role->desc = $request->desc;
            $role->status = $request->status;
            $role->save();
            DB::commit();
        }
        catch (\Throwable $th)
        {
            DB::rollBack();
            return $this->fails('添加失败');
        }
        
        return $this->success('添加成功');
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails())
        {
            return $this->fails($validator->errors());
        }
        $role = Role::find($request->id);
        DB::beginTransaction();
        try
        {
            $role->delete();
            Enforcer::deleteRole($role->value);
            DB::commit();
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return $this->fails('删除失败');
        }
        return $this->success('删除成功');
    }

    public function getRoles()
    {
        $roles = Role::where('status', 1)
            ->get([
                'name',
                'value',
            ]);
        return $this->success('success', $roles);
    }
}
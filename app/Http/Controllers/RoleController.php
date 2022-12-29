<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Lauthz\Facades\Enforcer;

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
        $query = Role::offset(($request->page - 1) * $request->pageSize)
            ->limit($request->pageSize);
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
        $result = $query->get([
                'id',
                'name',
                'value',
                'desc',
                'status',
                'created_at',
                'updated_at',
            ]);
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
        ]);
        if ($validator->fails())
        {
            return $this->fails($validator->errors());
        }
        $role = Role::find($request->id);
        if (!$role)
        {
            return $this->fails('角色不存在');
        }
        $role->name = $request->name;
        $role->value = $request->value;
        $role->desc = $request->desc;
        $role->status = $request->status;
        $role->save();
        return $this->success('success', []);
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
        $role = new Role();
        $role->name = $request->name;
        $role->value = $request->value;
        $role->desc = $request->desc;
        $role->status = $request->status;
        $role->save();
        return $this->success('success', []);
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
        if (!$role)
        {
            return $this->fails('角色不存在');
        }
        $role->delete();
        return $this->success('删除成功', []);
    }

    public function getPermission(Request $request)
    {
        $admin = $request->user('admin');
        $roles = Enforcer::getRolesForUser($admin->id);
        $permissions = [];
        foreach ($roles as $role)
        {
            $permissions[] = array_column(Enforcer::getPermissionsForUser($role), 2);
        }
        $permissions = array_unique(array_merge(...$permissions));
        $one = Permission::where('status', 1)
            ->whereIn('permission', $permissions)
            ->get([
                'id',
                'title',
                'status',
                '_lft',
                '_rgt',
                'permission',
            ]);
        $all = Permission::where('status', 1)
            ->get([
                'id',
                'title',
                'status',
                'parent_id',
                '_lft',
                '_rgt',
            ])
            ->toTree();
        $this->buildMenu($all);
        return $this->success('success', $all);
    }

    public function buildMenu($menus)
    {
        foreach ($menus as $menu)
        {
            unset($menu->parent_id);
            unset($menu->_lft);
            unset($menu->_rgt);
            if (count($menu->children))
            {
                $this->buildMenu($menu->children);
            }
        }
    }
}
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
        $query = new Role();
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
        foreach ($roles as $role) {
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
        $all = Permission::where('status', 1)
            ->get([
                'id',
                'title',
                'parent_id',
                'icon',
                '_lft',
                '_rgt',
            ])
            ->toTree();
        $this->treeFormat($all);
        return $this->success('success', $all);
    }

    public function treeFormat($obj)
    {
        foreach ($obj as $v)
        {
            unset($v->parent_id);
            unset($v->_lft);
            unset($v->_rgt);
            if (count($v->children))
            {
                $this->treeFormat($v->children);
            }
        }
    }
}
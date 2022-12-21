<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            return $this->fails(400, $validator->errors());
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
        return $this->success(200, 'success', $result);
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
            return $this->fails(400, $validator->errors());
        }
        $role = Role::find($request->id);
        if (!$role)
        {
            return $this->fails(400, '角色不存在');
        }
        $role->name = $request->name;
        $role->value = $request->value;
        $role->desc = $request->desc;
        $role->status = $request->status;
        $role->save();
        return $this->success(200, 'success', []);
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
            return $this->fails(400, $validator->errors());
        }
        $role = new Role();
        $role->name = $request->name;
        $role->value = $request->value;
        $role->desc = $request->desc;
        $role->status = $request->status;
        $role->save();
        return $this->success(200, 'success', []);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails())
        {
            return $this->fails(400, $validator->errors());
        }
        $role = Role::find($request->id);
        if (!$role)
        {
            return $this->fails(400, '角色不存在');
        }
        $role->delete();
        return $this->success(200, 'success', []);
    }

    public function getPermission()
    {
        $permissions = Permission::where('status', 1)
            ->get([
                'id',
                'title',
                'parent_id',
                '_lft',
                '_rgt',
                
            ])
            ->toTree();
        $this->buildMenu($permissions);
        return $this->success(200, 'success', $permissions);
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
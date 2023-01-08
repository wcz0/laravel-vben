<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use stdClass;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string',
            'status' => 'nullable|integer',
        ]);
        if ($validator->fails())
        {
            return $this->fails($validator->errors());
        }
        $query = Permission::query();
        if ($request->filled('title'))
        {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        if ($request->filled('status'))
        {
            $query->where('status', $request->status);
        }
        $result = $query->get([
                'id',
                'title',
                'icon',
                'component',
                'permission',
                'sort',
                'type',
                'status',
                'created_at',
                'parent_id',
                '_lft',
                '_rgt',
            ])
            ->toTree();
        $this->treeFormat($result);
        return $this->success('success', $result);
    }


    public function getTree()
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

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1',
            'parent_id' => 'nullable|integer',
            'title' => 'required|string',
            'name' => 'nullable|string',
            'redirect' => 'nullable|string',
            'icon' => 'nullable|string',
            'component' => 'nullable|string',
            'permission' => 'required|string|unique:permissions',
            'affix' => 'nullable|integer',
            'sort' => 'nullable|integer',
            'status' => 'nullable|integer',
            'type' => 'required|integer',
        ]);
        if ($validator->fails())
        {
            return $this->fails($validator->errors());
        }
        $permission = Permission::find($request->id);
        if (!$permission)
        {
            return $this->fails('permission not found');
        }
        $permission->parent_id = $request->parent_id;
        $permission->title = $request->title;
        $permission->name = $request->name;
        $permission->redirect = $request->redirect;
        $permission->icon = $request->icon;
        $permission->component = $request->component;
        $permission->permission = $request->permission;
        $permission->affix = $request->affix;
        $permission->sort = $request->sort;
        $permission->status = $request->status;
        $permission->type = $request->type;
        $permission->save();
        return $this->success('success');
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parent_id' => 'nullable|integer',
            'title' => 'required|string',
            'name' => 'nullable|string',
            'redirect' => 'nullable|string',
            'icon' => 'nullable|string',
            'component' => 'nullable|string',
            'permission' => 'required|string|unique:permissions',
            'affix' => 'nullable|integer',
            'sort' => 'nullable|integer',
            'status' => 'nullable|integer',
            'type' => 'required|integer',
        ]);
        if ($validator->fails())
        {
            return $this->fails($validator->errors());
        }
        $permission = new Permission();
        $permission->parent_id = $request->parent_id;
        $permission->title = $request->title;
        $permission->name = $request->name;
        $permission->redirect = $request->redirect;
        $permission->icon = $request->icon;
        $permission->component = $request->component;
        $permission->permission = $request->permission;
        $permission->affix = $request->affix;
        $permission->sort = $request->sort;
        $permission->status = $request->status;
        $permission->type = $request->type;
        $permission->save();
        return $this->success('success');
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1',
        ]);
        if ($validator->fails())
        {
            return $this->fails($validator->errors());
        }
        $permission = Permission::find($request->id);
        if (!$permission)
        {
            return $this->fails('permission not found');
        }
        $permission->delete();
        return $this->success('success');
    }
}

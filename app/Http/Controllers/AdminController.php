<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use Lauthz\Facades\Enforcer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class AdminController extends Controller
{
    public function menu(Request $request)
    {
        $admin = $request->user('admin');
        $roles = Enforcer::getRolesForUser($admin->id);
        $permissions = [];
        foreach ($roles as $role)
        {
            $permissions[] = array_column(Enforcer::getPermissionsForUser($role), 2);
        }
        $permissions = array_unique(array_merge(...$permissions));
        $menus = Permission::where('type', 0)
            ->where('status', 1)
            ->whereIn('permission', $permissions)
            ->get([
                'id',
                'path',
                'name',
                'redirect',
                'component',
                'parent_id',
                'title',
                'affix',
                'icon',
                'sort',
                '_lft',
                '_rgt',
                'permission',
            ])
            ->toTree();
        $this->treeFormat($menus);
        return $this->success('success', $menus);
    }

    /**
     * @param Permission $menus
     * @return void
     */
    public function treeFormat($menus)
    {
        foreach ($menus as $menu)
        {
            $menu->meta = [
                'title' => $menu->title,
                'icon' => $menu->icon,
                'affix' => $menu->affix == 1 ? true : false,
                'sort' => $menu->sort,
            ];
            unset($menu->parent_id);
            unset($menu->_lft);
            unset($menu->_rgt);
            unset($menu->title);
            unset($menu->icon);
            unset($menu->affix);
            if (count($menu->children))
            {
                $this->treeFormat($menu->children);
            }
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        if ($validator->fails())
        {
            return $this->fails($validator->errors());
        }
        $credentials = request(['username', 'password']);
        if (!$token = auth('admin')->setTTL(9999999999)->attempt($credentials))
        {
            return $this->fails('Username or password is wrong!');
        }
        $user = auth('admin')->user();
        $roles = Enforcer::getRolesForUser($user->id);
        $roles = Role::where('status', 1)
            ->whereIn('value', $roles)
            ->get(['name', 'value',]);
        $result = new stdClass();
        $result->id = $user->id;
        $result->username = $user->username;
        $result->name = $user->name;
        $result->avatar = $user->avatar;
        $result->email = $user->email;
        $result->phone = $user->phone;
        $result->token = $token;
        $result->role = $roles->toArray();
        return $this->success('success', $result);
    }

    public function refresh()
    {
        return $this->success('success', auth('admin')->refresh());
    }

    public function admin(Request $request)
    {
        $user = $request->user();
        $roles = Enforcer::getRolesForUser($user->id);
        $roles = Role::where('status', 1)
            ->whereIn('value', $roles)
            ->get(['name', 'value',]);
        $user->role = $roles->toArray();
        return $this->success('success', $user);
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'required|integer',
            'limit' => 'required|integer',
            'username' => 'nullable|string',
            'name' => 'nullable|string',
            'phone' => 'nullable|string',
            'status' => 'nullable|integer',
        ]);
        if ($validator->fails())
        {
            return $this->fails($validator->errors());
        }
        $query = Admin::query();
        if ($request->filled('username'))
        {
            $query->where('username', 'like', '%' . $request->input('username') . '%');
        }
        if ($request->filled('name'))
        {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }
        if ($request->filled('phone'))
        {
            $query->where('phone', 'like', '%' . $request->input('phone') . '%');
        }
        if ($request->filled('status'))
        {
            $query->where('status', $request->input('status'));
        }
        $result = new StdClass();
        $result->total = $query->count();
        $result->items = $query->offset(($request->input('page') - 1) * $request->input('limit'))
            ->limit($request->input('limit'))
            ->get();
        return $this->success('success', $result);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:admins',
            'password' => 'required|string',
            'gender' => 'nullable|integer',
            'avatar' => 'nullable|string',
            'name' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'status' => 'nullable|integer',
            'birthday' => 'nullable|date',
            'roles' => 'nullable|array',
        ]);
        if ($validator->fails())
        {
            return $this->fails($validator->errors());
        }
        $admin = new Admin();
        DB::beginTransaction();
        try
        {
            $admin->username = $request->input('username');
            $admin->password = bcrypt($request->input('password'));
            $admin->gender = $request->gender;
            $admin->birthday = $request->birthday;
            $admin->avatar = $request->input('avatar');
            $admin->name = $request->input('name');
            $admin->phone = $request->input('phone');
            $admin->email = $request->input('email');
            $admin->status = $request->input('status');
            $admin->save();
            foreach ($request->input('roles') as $role)
            {
                Enforcer::addRoleForUser($admin->id, $role);
            }
            DB::commit();
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return $this->fails('添加失败');
        }
        return $this->success('添加成功');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:admins',
            'password' => 'required|string',
            'gender' => 'nullable|integer',
            'avatar' => 'nullable|string',
            'name' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'status' => 'nullable|integer',
            'birthday' => 'nullable|date',
            'roles' => 'nullable|array',
        ]);
        if ($validator->fails())
        {
            return $this->fails($validator->errors());
        }
        $admin = Admin::find($id);
        DB::beginTransaction();
        try
        {
            $admin->username = $request->input('username');
            $admin->password = bcrypt($request->input('password'));
            $admin->gender = $request->gender;
            $admin->birthday = $request->birthday;
            $admin->avatar = $request->input('avatar');
            $admin->name = $request->input('name');
            $admin->phone = $request->input('phone');
            $admin->email = $request->input('email');
            $admin->status = $request->input('status');
            $admin->save();
            foreach ($request->input('roles') as $role)
            {
                Enforcer::addRoleForUser($admin->id, $role);
            }
            DB::commit();
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return $this->fails('修改失败');
        }
        return $this->success('修改成功');
    }

    public function delete($id)
    {
        $admin = Admin::find($id);
        if (!$admin)
        {
            return $this->fails('用户不存在');
        }
        DB::beginTransaction();
        try
        {
            $admin->delete();
            Enforcer::deleteRolesForUser($admin->id);
            DB::commit();
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return $this->fails('删除失败');
        }
        return $this->success('删除成功');
    }
}
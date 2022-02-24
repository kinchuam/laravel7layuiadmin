<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Logs\ActivityLog;
use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function index()
    {
        return view('admin.system.user.index');
    }

    public function data(Request $request)
    {
        $res = System\User::query()->select(['id', 'username', 'display_name', 'created_at','updated_at'])->paginate($request->get('limit', 10))->toArray();
        return $this->adminJson([
            'count' => $res['total'],
            'data' => $res['data']
        ]);
    }

    public function create()
    {
        return view('admin.system.user.create');
    }

    public function store(Requests\UserCreateRequest $request)
    {
        $data = $request->only(['username', 'display_name']);
        if ($model = System\User::query()->create([
            'username' => $data['username'],
            'display_name' => $data['display_name'],
            'password' => bcrypt($data['username']),
            'uuid' =>  \Faker\Provider\Uuid::uuid(),
        ])){
            ActivityLog::CreateSyslog('添加管理员账号', $data, $model);
            return $this->adminJson(['TableRefresh' => true,], 0,'添加成功');
        }
        return $this->adminJson([], -2, '系统错误');
    }

    public function edit($id, Request $request)
    {
        if ($request->ajax()) {
            $user = System\User::query()->findOrFail($id);
            return $this->adminJson($user);
        }
        return view('admin.system.user.edit');
    }

    public function update(Requests\UserUpdateRequest $request, $id)
    {
        $user = System\User::query()->findOrFail($id);
        $data = $request->only(['username', 'display_name']);
        if ($user->update($data)){
            System\User::clearData($id);
            ActivityLog::CreateSyslog('更新管理员账号', $data, $user);
            return $this->adminJson(['TableRefresh' => true,], 0, '更新成功');
        }
        return $this->adminJson([], -2, '系统错误');
    }

    public function password()
    {
        return view('admin.system.user._password');
    }

    public function updatePassword(Requests\UserPasswordRequest $request, $id)
    {
        $user = System\User::query()->findOrFail($id);
        $password = $request->input('password');
        if ($user->update(['password' => bcrypt($password)])){
            System\User::clearData($id);
            ActivityLog::CreateSyslog('更新管理员账号密码', [], $user);
            return $this->adminJson([], 0, '更新成功');
        }
        return $this->adminJson([], -2, '系统错误');
    }

    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        $ids = is_array($ids)?$ids:[$ids];
        if (empty($ids)) {
            return $this->adminJson([], 1,'请选择删除项');
        }
        $list = System\User::query()->whereIn('id', $ids)->get(['id', 'username', 'display_name', 'password', 'uuid']);
        if ($list->isEmpty()) {
            return $this->adminJson([], 1, '记录不存在');
        }
        try {
            DB::transaction(function () use ($list) {
                foreach ($list as $model) {
                    ActivityLog::CreateSyslog('删除管理员账号', $model, $model);
                    $model->delete();
                }
                return true;
            });
            return $this->adminJson([], 0, '删除成功');
        }catch (\Exception $e) {
            return $this->adminJson([], -2, '系统错误');
        }
    }

    public function role($id, Request $request)
    {
        if ($request->ajax()) {
            $user = System\User::query()->findOrFail($id);
            $roles = System\Role::query()->get(['id', 'name', 'display_name']);
            //$hasRoles = $user->roles();
            if (!empty($roles)) {
                foreach ($roles as $role){
                    $role->own = (bool) $user->hasRole($role);
                }
            }
            return $this->adminJson([
                'roles' => $roles,
            ]);
        }
        return view('admin.system.user.role');
    }

    public function assignRole(Request $request, $id)
    {
        $user = System\User::query()->findOrFail($id);
        $roles = $request->get('roles', []);
        if ($user->syncRoles($roles)){
            ActivityLog::CreateSyslog('更新管理员账号角色权限', $roles, $user);
            return $this->adminJson([], 0,'更新用户角色成功');
        }
        return $this->adminJson([], -2, '系统错误');
    }

    public function permission($id, Request $request)
    {
        if ($request->ajax()) {
            $user = System\User::query()->findOrFail($id);
            $permissions = $this->GetPermissions();
            if (!empty($permissions)) {
                foreach ($permissions as $key1 => $item1){
                    $permissions[$key1]['own'] = $user->hasDirectPermission($item1['name']) ? 'checked' : false;
                    if (isset($item1['_child'])){
                        foreach ($item1['_child'] as $key2 => $item2){
                            $permissions[$key1]['_child'][$key2]['own'] = $user->hasDirectPermission($item2['name']) ? 'checked' : false;
                            if (isset($item2['_child'])){
                                foreach ($item2['_child'] as $key3 => $item3){
                                    $permissions[$key1]['_child'][$key2]['_child'][$key3]['own'] = $user->hasDirectPermission($item3['name']) ? 'checked' : false;
                                }
                            }
                        }
                    }
                }
            }
            return $this->adminJson([
                'permissions' => $permissions,
            ]);
        }
        return view('admin.system.user.permission');
    }

    public function assignPermission($id, Request $request)
    {
        $user = System\User::query()->findOrFail($id);
        $permissions = $request->get('permissions');
        empty($permissions) ? $user->permissions()->detach() : $user->syncPermissions($permissions);
        ActivityLog::CreateSyslog('更新管理员账号权限', $permissions, $user);
        return $this->adminJson([], 0,'已更新用户直接权限');
    }

}

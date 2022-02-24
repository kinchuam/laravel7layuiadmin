<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Requests;
use App\Models\Logs\ActivityLog;
use App\Models\System;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        return view('admin.system.role.index');
    }

    public function data(Request $request)
    {
        $res = System\Role::query()->select(['id', 'name', 'display_name', 'created_at', 'updated_at'])->paginate($request->get('limit', 10))->toArray();
        return $this->adminJson([
            'count' => $res['total'],
            'data' => $res['data'],
        ]);
    }

    public function create()
    {
        return view('admin.system.role.create');
    }

    public function store(Requests\RoleCreateRequest $request)
    {
        $data = $request->only(['name', 'display_name']);
        $data['guard_name'] = 'admin';
        if ($model = System\Role::query()->create($data)){
            ActivityLog::CreateSyslog('添加角色', $data, $model);
            return $this->adminJson(['TableRefresh' => true,], 0,'添加成功');
        }
        return $this->adminJson([], -2,'系统错误');
    }

    public function edit($id, Request $request)
    {
        if ($request->ajax()) {
            $role = System\Role::query()->findOrFail($id);
            return $this->adminJson($role);
        }
        return view('admin.system.role.edit');
    }

    public function update(Requests\RoleUpdateRequest $request, $id)
    {
        $data = $request->only(['name', 'display_name']);
        $role = System\Role::query()->findOrFail($id);
        if ($role->update($data)){
            ActivityLog::CreateSyslog('更新角色', $data, $role);
            return $this->adminJson(['TableRefresh' => true,], 0, '更新成功');
        }
        return $this->adminJson([], -2,'系统错误');
    }

    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        $ids = is_array($ids)?$ids:[$ids];
        if (empty($ids)) {
            return $this->adminJson([], 1,'请选择删除项');
        }
        $list = System\Role::query()->whereIn('id', $ids)->get(['id', 'name', 'guard_name', 'display_name']);
        if ($list->isEmpty()) {
            return $this->adminJson([], 1, '记录不存在');
        }
        try {
            DB::transaction(function () use ($list) {
                foreach ($list as $model) {
                    ActivityLog::CreateSyslog('删除角色', $model, $model);
                    $model->delete();
                }
                return true;
            });
            return $this->adminJson([], 0, '删除成功');
        }catch (\Exception $e) {
            return $this->adminJson([], -2, '系统错误');
        }
    }

    public function permission($id, Request $request)
    {
        if ($request->ajax()) {
            $role = System\Role::query()->findOrFail($id);
            $permissions = $this->GetPermissions();
            if (!empty($permissions)) {
                foreach ($permissions as $key1 => $item1){
                    $permissions[$key1]['own'] = $role->hasPermissionTo($item1['name']) ? 'checked' : false;
                    if (!empty($item1['_child'])){
                        foreach ($item1['_child'] as $key2 => $item2){
                            $permissions[$key1]['_child'][$key2]['own'] = $role->hasPermissionTo($item2['name']) ? 'checked' : false;
                            if (!empty($item2['_child'])){
                                foreach ($item2['_child'] as $key3 => $item3){
                                    $permissions[$key1]['_child'][$key2]['_child'][$key3]['own'] = $role->hasPermissionTo($item3['name']) ? 'checked' : false;
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
        return view('admin.system.role.permission');
    }

    public function assignPermission($id, Request $request)
    {
        $role = System\Role::query()->findOrFail($id);
        $permissions = $request->get('permissions');
        empty($permissions) ? $role->permissions()->detach() : $role->syncPermissions($permissions);
        ActivityLog::CreateSyslog('更新角色权限', $permissions, $role);
        return $this->adminJson([], 0, '已更新角色权限');
    }

}

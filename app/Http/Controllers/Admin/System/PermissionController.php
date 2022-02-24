<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Requests;
use App\Models\Logs\ActivityLog;
use App\Models\System\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{

    public function index()
    {
        return view('admin.system.permission.index');
    }

    public function data(Request $request)
    {
        $model = Permission::query()->select(['id', 'name', 'display_name', 'parent_id', 'genre', 'created_at']);
        if ($keywords = escapeLike($request->get('keywords'))){
            $model->whereRaw("( LOCATE('".$keywords ."', `display_name`) > 0 )");
        }
        $parent_id = $request->get('parent_id');
        if (is_numeric($parent_id)) {
            $model->where('parent_id', $parent_id);
        }
        $withType = $request->get('with_type', 'count');
        if ($withType == 'parent') {
            $model->with('children:id,name,display_name,parent_id');
        }else if ($withType == 'count') {
            $model->withCount('children');
        }
        $list = $model->get()->toArray();
        if (!empty($list) && $withType == 'count') {
            foreach ($list as $k => $v) {
                $list[$k]['haveChild'] = $v['children_count'] > 0;
            }
        }
        return $this->adminJson(['data' => $list]);
    }

    public function create()
    {
        return view('admin.system.permission.create');
    }

    public function store(Requests\PermissionCreateRequest $request)
    {
        $data = $request->only(['parent_id', 'name', 'display_name', 'genre']);
        $data['parent_id'] = intval($data['parent_id']);
        $data['guard_name'] = 'admin';
        if ($model = Permission::query()->create($data)){
            ActivityLog::CreateSyslog('添加权限', $data, $model);
            return $this->adminJson(['TableRefresh' => true,], 0,'添加成功');
        }
        return $this->adminJson([], -1, '添加失败');
    }

    public function edit($id, Request $request)
    {
        if ($request->ajax()) {
            $permission = Permission::query()->findOrFail($id);
            return $this->adminJson($permission);
        }
        return view('admin.system.permission.edit');
    }

    public function update(Requests\PermissionUpdateRequest $request, $id)
    {
        $data = $request->only(['parent_id', 'name', 'display_name', 'genre']);
        $data['parent_id'] = intval($data['parent_id']);
        $permission = Permission::query()->findOrFail($id);
        if ($permission->update($data)){
            ActivityLog::CreateSyslog('更新权限', $data, $permission);
            return $this->adminJson(['TableRefresh' => true,], 0, '更新权限成功');
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
        $list = Permission::query()->whereIn('id', $ids)->withCount('children')->get(['id', 'name', 'guard_name', 'display_name', 'parent_id', 'genre']);
        if ($list->isEmpty()){
            return ['code' => 1, 'message' => '记录不存在'];
        }
        try {
            $res = DB::transaction(function () use ($list) {
                foreach ($list as $model) {
                    if ($model->children_count > 0){
                        return ['code' => 2, 'message' => '存在子权限禁止删除'];
                    }
                    ActivityLog::CreateSyslog('删除【'.$model['name'].'】权限', $model, $model);
                    $model->delete();
                }
                return ['code' => 0, 'message' => '删除成功'];
            });
            return $this->adminJson([], $res['code'], $res['message']);
        }catch (\Exception $e) {
            return $this->adminJson([], -2, '系统错误');
        }
    }
}

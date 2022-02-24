<?php
namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Logs\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FilesGroupController extends Controller
{

    public function index()
    {
        return view("admin.content.files_group.index");
    }

    public function data(Request $request)
    {
        $model = Content\AttachmentGroup::query()->select(['id', 'name', 'sort', 'created_at', 'updated_at']);
        if ($keywords = trim($request->get('keywords'))) {
            $model->whereRaw("( LOCATE('".escapeLike($keywords)."', `name`) > 0 )");
        }
        $model->orderBy('sort','desc');
        if ($request->get('is_all')) {
            return $this->adminJson([
                'data'  => $model->get()->toArray()
            ]);
        }
        $res = $model->paginate($request->get('limit',10))->toArray();
        return $this->adminJson([
            'count' => $res['total'],
            'data'  => $res['data']
        ]);
    }

    public function create()
    {
        return view('admin.content.files_group.create');
    }

    public function store(Request $request)
    {
        $data = $request->only(['sort', 'group_name']);
        $data['sort'] = intval($data['sort']);
        $data['name'] = trim($data['group_name']);
        if ($model = Content\AttachmentGroup::query()->create($data)) {
            ActivityLog::CreateSyslog('添加附件分组', $data, $model);
            return $this->adminJson([
                'TableRefresh' => true,
                'group_id' => $model['id'],
                'group_name' => $data['group_name']
            ], 0,'添加成功');
        }
        return $this->adminJson([], -1, '添加失败');
    }

    public function edit(Request $request)
    {
        if ($request->ajax()) {
            $item = Content\AttachmentGroup::query()->findOrFail(intval($request->get('id')));
            return $this->adminJson($item);
        }
        return view('admin.content.files_group.edit');
    }

    public function update(Request $request)
    {
        $id = intval($request->get('id'));
        $data = $request->only(['sort', 'group_name']);
        $data['sort'] = intval($data['sort']);
        $data['name'] = trim($data['group_name']);
        $item = Content\AttachmentGroup::query()->findOrFail($id);
        if ($item->update($data)){
            ActivityLog::CreateSyslog('修改附件分组', $data, $item);
            return $this->adminJson([
                'TableRefresh' => true,
                'group_id' => $id,
                'group_name' => $data['group_name']
            ], 0, '更新成功');
        }
        return $this->adminJson([], -1, '更新失败');
    }

    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        $ids = is_array($ids)?$ids:[$ids];
        if (empty($ids)) {
            return $this->adminJson([], 1,'请选择删除项');
        }
        $list = Content\AttachmentGroup::query()->whereIn('id', $ids)->withCount('files')->get(['id', 'name', 'sort']);
        if ($list->isEmpty()){
            return $this->adminJson([], 1, '记录不存在');
        }
        try {
            $res = DB::transaction(function () use ($list) {
                foreach ($list as $model) {
                    if ($model->files_count > 0) {
                        return ['code' => 1, 'message' => '分组下存在附件'];
                    }
                    ActivityLog::CreateSyslog('删除附件分组', $model, $model);
                    $model->delete();
                }
                return ['code' => 0, 'message' => '删除成功'];
            });
            return $this->adminJson([], $res['code'], $res['message']);
        }catch (\Exception $exception) {
            return $this->adminJson([], -2, '系统错误');
        }
    }

    public function moveFiles(Request $request)
    {
        $data = $request->only(['group_id', 'fileIds']);
        $fileIds = $data['fileIds'];
        if (!is_array($fileIds)) {
            return $this->adminJson([], 1,'请选择文件');
        }
        if (Content\Attachment::query()->whereIn('id', $fileIds)->update(['group_id' => intval($data['group_id'])])) {
            ActivityLog::CreateSyslog('附件变更分组', ['fileIds' => $fileIds]);
            return $this->adminJson([], 0, '移动成功');
        }
        return $this->adminJson([], -1, '操作失败');
    }

}

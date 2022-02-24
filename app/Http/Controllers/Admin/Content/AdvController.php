<?php


namespace App\Http\Controllers\Admin\Content;


use App\Http\Controllers\Controller;
use App\Models\Content\Adv;
use App\Models\Logs\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdvController extends Controller
{

    public function index()
    {
        return view('admin.content.adv.index');
    }

    public function data(Request $request)
    {
        $model = Adv::query()->select(['id', 'sort', 'name', 'thumb', 'status', 'created_at']);
        if ($keywords = $request->get('keywords')) {
            $model->whereRaw("( LOCATE('".escapeLike($keywords)."', `name`) > 0 )");
        }
        $res = $model->orderBy('sort')->paginate($request->get('limit',10))->toArray();
        $res['data'] = set_medias($res['data'],'thumb');
        return $this->adminJson([
            'count' => $res['total'],
            'data'  => $res['data']
        ]);
    }

    public function create()
    {
        return view('admin.content.adv.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:120'
        ]);
        $data = $request->only(['name', 'thumb', 'url', 'sort', 'status']);
        $data['url'] = trim($data['url']);
        $data['thumb'] = trim($data['thumb']);
        $data['sort'] = intval($data['sort']);
        $data['status'] = intval($data['status']);
        if ($model = Adv::query()->create($data)){
            ActivityLog::CreateSyslog('添加广告位', $data, $model);
            return $this->adminJson(['TableRefresh' => true,],0,'添加成功');
        }
        return $this->adminJson([],-2,'系统错误');
    }

    public function edit($id, Request $request)
    {
        if ($request->ajax()) {
            $item = Adv::query()->findOrFail($id);
            return $this->adminJson($item);
        }
        return view('admin.content.adv.edit');
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:120'
        ]);
        $data = $request->only(['name', 'thumb', 'url', 'sort', 'status']);
        $data['thumb'] = trim($data['thumb']);
        $data['sort'] = intval($data['sort']);
        $data['url'] = trim($data['url']);
        $data['status'] = intval($data['status']);
        $item = Adv::query()->findOrFail($id);
        if ($item->update($data)) {
            ActivityLog::CreateSyslog('更新广告位' , $data, $item);
            return $this->adminJson(['TableRefresh' => true,],0, '更新成功');
        }
        return $this->adminJson([],-2,'系统错误');
    }

    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        $ids = is_array($ids)?$ids:[$ids];
        if (empty($ids)){
            return $this->adminJson([],1,'请选择删除项');
        }
        $list = Adv::query()->whereIn('id', $ids)->get(['id', 'sort', 'name', 'thumb', 'url', 'status']);
        if ($list->isEmpty()) {
            return $this->adminJson([], 1, '记录不存在');
        }
        try {
            DB::transaction(function () use ($list) {
                foreach ($list as $model) {
                    ActivityLog::CreateSyslog('删除广告位', $model, $model);
                    $model->delete();
                }
                return true;
            });
            return $this->adminJson([], 0, '删除成功');
        }catch (\Exception $e) {
            return $this->adminJson([], 1, '删除失败');
        }
    }

}

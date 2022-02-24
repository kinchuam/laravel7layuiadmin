<?php


namespace App\Http\Controllers\Admin\Content;


use App\Http\Controllers\Controller;
use App\Models\Content\Category;
use App\Models\Logs\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{

    public function index()
    {
        return view('admin.content.category.index');
    }

    public function data(Request $request)
    {
        $model = Category::query()->select(['id', 'sort', 'name', 'status', 'created_at']);
        if ($keywords = $request->get('keywords')) {
            $model->whereRaw("( LOCATE('".escapeLike($keywords)."', `name`) > 0 )");
        }
        $res = $model->orderBy('sort','desc')->paginate($request->get('limit',10))->toArray();
        return $this->adminJson([
            'count' => $res['total'],
            'data'  => $res['data']
        ]);
    }

    public function create()
    {
        return view('admin.content.category.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'unique:article_category', 'max:120']
        ]);
        $data = $request->only(['sort', 'name', 'status']);
        if ($model = Category::query()->create($data)) {
            ActivityLog::CreateSyslog('添加文章栏目', $data, $model);
            return $this->adminJson(['TableRefresh' => true,],0,'添加成功');
        }
        return $this->adminJson([],-2,'系统错误');
    }

    public function edit($id, Request $request)
    {
        if ($request->ajax()) {
            $item = Category::query()->findOrFail($id);
            return $this->adminJson($item);
        }
        return view('admin.content.category.edit');
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'name' => ['required', 'unique:article_category,name,'.$id.',id', 'max:120']
        ]);
        $data = $request->only(['sort', 'name', 'status']);
        $item = Category::query()->findOrFail($id);
        if ($item->update($data)) {
            ActivityLog::CreateSyslog('更新文章栏目', $data, $item);
            return $this->adminJson(['TableRefresh' => true,],0,'更新成功');
        }
        return $this->adminJson([],-2,'系统错误');
    }

    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        $ids = is_array($ids)?$ids:[$ids];
        if (empty($ids)){
            return $this->adminJson([], 1,'请选择删除项');
        }
        $list = Category::query()->whereIn('id', $ids)->get(['id', 'sort', 'name', 'status']);
        if ($list->isEmpty()) {
            return $this->adminJson([], 1, '记录不存在');
        }
        try {
            DB::transaction(function () use ($list) {
                foreach ($list as $model) {
                    ActivityLog::CreateSyslog('删除文章栏目', $model, $model);
                    $model->delete();
                }
                return true;
            });
            return $this->adminJson([], 0, '删除成功');
        }catch (\Exception $e) {
            return $this->adminJson([], -2, '系统错误');
        }
    }

}


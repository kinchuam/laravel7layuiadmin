<?php


namespace App\Http\Controllers\Admin\Content;


use App\Http\Controllers\Controller;
use App\Models\Content\Article;
use App\Models\Content\Category;
use App\Models\Logs\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{

    public function index()
    {
        $category = Category::query()->get(['id', 'name']);
        return view('admin.content.article.index', compact('category'));
    }

    public function data(Request $request)
    {
        $model = Article::query()->select(['id', 'sort', 'category_id', 'title', 'thumb', 'status', 'created_at']);
        if ($category_id = intval($request->get('category_id'))) {
            $model->where('category_id', $category_id);
        }
        if ($keywords = $request->get('keywords')) {
            $model->whereRaw("( LOCATE('".escapeLike($keywords)."', `title`) > 0 )");
        }
        $res = $model->orderBy('sort')->orderBy('created_at','desc')->paginate($request->get('limit',10))->toArray();
        $res['data'] = set_medias($res['data'],'thumb');
        return $this->adminJson([
            'count' => $res['total'],
            'data'  => $res['data']
        ]);
    }

    public function get_category()
    {
        $category = Category::query()->orderBy('sort','desc')->get(['id', 'name as title'])->toArray();
        return $this->adminJson([
            'data'  => $category,
        ]);
    }

    public function create()
    {
        return view('admin.content.article.create');
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'title' => 'required|unique:articles|max:120',
            'category_id' => 'required',
        ]);
        $data = $request->only(['sort', 'category_id', 'title', 'desc', 'thumb', 'content', 'url', 'status',]);
        $data['sort'] = intval($data['sort']);
        $data['thumb'] = trim($data['thumb']);
        $data['desc'] = trim($data['desc']);
        $data['content'] = htmlentities($data['content']);
        $data['url'] = trim($data['url']);
        if ($model = Article::query()->create($data)){
            ActivityLog::CreateSyslog('新增文章', $data, $model);
            return $this->adminJson(['TableRefresh' => true,],0,'添加成功');
        }
        return $this->adminJson([],-2,'系统错误');
    }

    public function edit($id, Request $request)
    {
        if ($request->ajax()) {
            $item = Article::query()->findOrFail($id);
            if (!empty($item['content'])) {
                $item['content'] = htmlspecialchars_decode($item['content']);
            }
            return $this->adminJson($item);
        }
        return view('admin.content.article.edit');
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required|unique:articles,title,'.$id.',id|max:120',
            'category_id' => 'required',
        ]);
        $data = $request->only(['sort', 'category_id', 'title', 'desc', 'thumb', 'content', 'url', 'status',]);
        $data['sort'] = intval($data['sort']);
        $data['content'] = htmlentities($data['content']);
        $data['thumb'] = trim($data['thumb']);
        $data['desc'] = trim($data['desc']);
        $data['url'] = trim($data['url']);
        $item = Article::query()->findOrFail($id);
        if ($item->update($data)) {
            ActivityLog::CreateSyslog('更新文章', $data, $item);
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
        $list = Article::query()->whereIn('id', $ids)->get(['id', 'sort', 'category_id', 'title', 'desc', 'thumb', 'content', 'url', 'view_count', 'status',]);
        if ($list->isEmpty()) {
            return $this->adminJson([], 1, '记录不存在');
        }
        try {
             DB::transaction(function () use ($list) {
                foreach ($list as $model) {
                    ActivityLog::CreateSyslog('删除文章', $model, $model);
                    $model->delete();
                }
                return ['code' => 0, 'message' => '删除成功'];
            });
            return $this->adminJson([], 0, '删除成功');
        }catch (\Exception $e) {
            return $this->adminJson([], -2, '系统错误');
        }
    }

}

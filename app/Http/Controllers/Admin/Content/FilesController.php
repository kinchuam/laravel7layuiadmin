<?php
namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Controller;
use App\Models\Sites;
use App\Models\Content;
use App\Models\Logs\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FilesController extends Controller
{

    public function index()
    {
        return view("admin.content.files.index");
    }

    public function data(Request $request)
    {
        $model = Content\Attachment::query()->select(['id','group_id','filename','path','suffix','type','storage','size','created_at','deleted_at']);
        if (!empty($request->get('recycle'))) {
            $model->onlyTrashed();
        }
        $group_id = $request->get('group_id','all');
        if ($group_id != 'all' && $group_id >= 0) {
            $model->where('group_id', intval($group_id));
        }
        if ($keywords = trim($request->get('keywords'))) {
            $model->whereRaw("( LOCATE('".escapeLike($keywords)."', `filename`) > 0 )");
        }
        $type = $request->get('type');
        if (!empty($type) && $type == 'image') {
            $model->whereIn('suffix', Content\Attachment::$image_type);
        }
        $res = $model->with(['group:id,name'])->orderBy('created_at','desc')->paginate($request->get('limit',10))->toArray();
        $list = $res['data'];
        if (!empty($list)) {
            foreach ($list as $ke => $row) {
                $list[$ke]['file_url'] = ToMedia($row['path']);
                $list[$ke]['file_type'] = $row['type'];
            }
        }
        return $this->adminJson([
            'count' => $res['total'],
            'data'  => $list
        ]);
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            $config = Sites::GetPluginSet('uploadConfig');
            $image_type = isset($config['image_type']) ? explode('|', $config['image_type']) : Content\Attachment::$image_type;
            $file_type = isset($config['file_type']) ? explode('|', $config['file_type']) : Content\Attachment::$file_type;
            $files_type = implode('|', array_unique(array_merge($image_type, $file_type)));
            return $this->adminJson($files_type);
        }
        return view('admin.content.files.create');
    }

    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        $ids = is_array($ids)?$ids:[$ids];
        if (empty($ids)){
            return $this->adminJson([], 1, '请选择删除项');
        }
        $list = Content\Attachment::query()->whereIn('id', $ids)->get(['id', 'group_id', 'filename', 'path', 'suffix', 'type', 'storage', 'size', 'uuid']);
        if ($list->isEmpty()) {
            return $this->adminJson([], 1, '记录不存在');
        }
        try {
            DB::transaction(function () use ($list) {
                foreach ($list as $model) {
                    ActivityLog::CreateSyslog('附件移入回收站', $model, $model);
                    $model->delete();
                }
                return true;
            });
            return $this->adminJson([], 0, '删除成功');
        }catch (\Exception $exception) {
            logger()->error($exception->getMessage());
            return $this->adminJson([], -2, '系统错误');
        }
    }

    public  function recycle()
    {
        return view("admin.content.files.recycle");
    }

    public function recover(Request $request)
    {
        $ids = $request->get('ids');
        $ids = is_array($ids)?$ids:[$ids];
        if (empty($ids)) {
            return $this->adminJson([], 1, '请选择恢复项');
        }
        $list = Content\Attachment::query()->onlyTrashed()->whereIn('id', $ids)->get(['id', 'group_id', 'filename', 'path', 'suffix', 'type', 'storage', 'size', 'uuid']);
        if ($list->isEmpty()){
            return $this->adminJson([], 1, '记录不存在');
        }
        try {
            DB::transaction(function () use ($list) {
                foreach ($list as $model) {
                    ActivityLog::CreateSyslog('附件移出回收站', $model, $model);
                    $model->restore();
                }
                return true;
            });
            return $this->adminJson([], 0, '恢复成功');
        }catch (\Exception $exception) {
            logger()->error($exception->getMessage());
            return $this->adminJson([], -2, '系统错误');
        }
    }

    public function expurgate(Request $request)
    {
        $ids = $request->get('ids');
        $ids = is_array($ids)?$ids:[$ids];
        if (empty($ids)) {
            return $this->adminJson([], 1, '请选择删除项');
        }
        $list = Content\Attachment::query()->onlyTrashed()->whereIn('id', $ids)->get(['id', 'group_id', 'filename', 'path', 'suffix', 'type', 'storage', 'size', 'uuid']);
        if ($list->isEmpty()){
            return $this->adminJson([], 1, '记录不存在');
        }
        try {
            DB::transaction(function () use ($list) {
                foreach ($list as $model) {
                    //删除储存文件
                    (new UploadController)->delFile($model->path, $model->storage);
                    ActivityLog::CreateSyslog('删除附件', $model, $model);
                    $model->forceDelete();
                }
                return true;
            });
            return $this->adminJson([], 0, '删除成功');
        }catch (\Exception $exception) {
            logger()->error($exception->getMessage());
            return $this->adminJson([], -2,'系统错误');
        }
    }

    public function download(Request $request)
    {
        $pathToFile = $request->get('pathToFile');
        return !empty($pathToFile)?response()->download($pathToFile):'fail';
    }
}

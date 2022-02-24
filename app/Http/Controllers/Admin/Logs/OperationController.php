<?php

namespace App\Http\Controllers\Admin\Logs;

use App\Models\Logs\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OperationController extends Controller
{
    public function index()
    {
        return view('admin.logs.operation.index');
    }

    public function data(Request $request)
    {
        $model = ActivityLog::query()->select(['id', 'log_name', 'description', 'subject_type', 'causer_id', 'created_at']);
        if ($causer_id = $request->get('causer_id')) {
            $model->where('causer_id', intval($causer_id));
        }
        $res = $model->with('user:id,display_name')
            ->orderBy('id','desc')
            ->paginate($request->get('limit',10))->toArray();
        return $this->adminJson([
            'count' => $res['total'],
            'data' => $res['data']
        ]);
    }

    public function show($id, Request $request)
    {
        if ($request->ajax()) {
            $item = ActivityLog::query()->findOrFail($id);
            return $this->adminJson($item);
        }
        return view('admin.logs.operation.show');
    }
}

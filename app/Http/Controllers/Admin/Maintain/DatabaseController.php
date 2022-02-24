<?php


namespace App\Http\Controllers\Admin\Maintain;


use App\Http\Controllers\Controller;
use App\Models\Logs\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    public function index()
    {
        return view('admin.maintain.database.index');
    }

    public function data()
    {
        $list = array_map('array_change_key_case', array_map('get_object_vars', DB::select('SHOW TABLE STATUS')));
        return $this->adminJson([
            'data' => $list,
        ]);
    }

    public function optimize(Request $request)
    {
        $tables = $request->get('tables');
        if (!empty($tables) && is_array($tables)) {
            $arr = implode('`,`', $tables);
            if (DB::statement("OPTIMIZE TABLE `{$arr}`")) {
                ActivityLog::CreateSyslog('优化数据表', $tables);
                return $this->adminJson([], 0 , '优化完成');
            }
            return $this->adminJson([], 1 , '优化出错请重试');
        }
        return $this->adminJson([], 1 , '请指定要优化的表');
    }

    public function repair(Request $request)
    {
        $tables = $request->get('tables');
        if (!empty($tables) && is_array($tables)) {
            $arr = implode('`,`', $tables);
            if (DB::statement("REPAIR TABLE `{$arr}`")) {
                ActivityLog::CreateSyslog('修复数据表', $tables);
                return $this->adminJson([], 0 , '修复完成');
            }
            return $this->adminJson([], 1 , '修复出错请重试');
        }
        return $this->adminJson([], 1 , '请指定要修复的表');
    }

    public function clear(Request $request)
    {
        if ($table = $request->get('table')) {
            if (DB::statement("TRUNCATE TABLE `{$table}`")) {
                ActivityLog::CreateSyslog('清空数据表', $table);
                return $this->adminJson([], 0 , '清空成功');
            }
            return $this->adminJson([], 1 , '出错请重试');
        }
        return $this->adminJson([], 1,'请指定要清空的表');
    }

    public function destroy(Request $request)
    {
        if ($table = $request->get('table')) {
            if (DB::statement("DROP TABLE `{$table}`")) {
                ActivityLog::CreateSyslog('删除数据表', $table);
                return $this->adminJson([], 0 , '删除成功');
            }
            return $this->adminJson([], 1 , '出错请重试');
        }
        return $this->adminJson([], 1,'请指定要删除的表');
    }
}
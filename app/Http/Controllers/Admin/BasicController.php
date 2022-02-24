<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Logs\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BasicController extends Controller
{

    public function index()
    {
        return view('admin.system.basic.info');
    }

    public function UpdateInfo(Request $request)
    {
        $this->validate($request, [
            'display_name'  => 'required',
        ]);
        $data = $request->only(['display_name']);
        $user = $this->guard()->user();
        if ($data['display_name'] != $user->display_name) {
            $user->save([ 'display_name' => trim($data['display_name']) ]);
            ActivityLog::CreateSyslog('管理员修改信息', $data, $user);
        }
        return $this->adminJson([], 0, '保存成功');
    }

    public function password()
    {
        return view('admin.system.basic.password');
    }

    public function UpdatePassword(Request $request)
    {
        $this->validate($request,[
            'password' => 'required|string|min:6|confirmed',
            'old_password' => 'required|string',
        ],[
            'password.required' => '新密码不能为空',
            'password.min' => '新密码不能少于6个字符',
            'password.confirmed' => '两次输入新密码不符',
            'old_password.required' => '原始密码不能为空',
        ]);
        $password = $request->get('password');
        $old_password = $request->get('old_password');
        if ($password == $old_password){
            return $this->adminJson([], 1, '不能使用原始密码');
        }
        $user = $this->guard()->user();
        if (Hash::check($old_password, $user->password)) {
            $user->save([
                'password' => bcrypt($password)
            ]);
            ActivityLog::CreateSyslog('管理员修改密码', [], $user);
            return $this->adminJson([], 0,'更新成功');
        }
        return $this->adminJson([], 1, '密码不正确');
    }

    private function guard()
    {
        return auth()->guard('admin');
    }
}

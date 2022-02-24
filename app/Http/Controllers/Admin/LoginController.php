<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Logs\LoginLog;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function showLoginForm()
    {
        return view('admin.login_register.login');
    }

    public function checkLogin()
    {
        return $this->adminJson(['isCheck' => $this->guard()->check(), 'url' => $this->redirectPath()]);
    }

    public function login(Request $request)
    {
        $request->merge([
            'username' => base64_decode($request->get('username')),
            'password' => base64_decode($request->get('password')),
        ]);
        $this->validateLogin($request);
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            LoginLog::CreateLoginLog('密码错误次数已达'.$this->maxAttempts().'次，已锁定!', $request->get('username'));
            $this->sendLockoutResponse($request);
        }
        if ($this->attemptLogin($request)) {
            $this->SessionToken($request);
            return $this->sendLoginResponse($request);
        }
        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();
        $this->clearLoginAttempts($request);
        if ($request->expectsJson()) {
            return $this->authenticated($request, $this->guard()->user()) ?: $this->adminJson([
                'url' => $this->redirectPath()
            ]);
        }
        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath());
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => trans('auth.failed')];
        if ($request->expectsJson()) {
            return $this->adminJson([], 422, trans('auth.failed'));
        }
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }

    public function username()
    {
        return 'username';
    }

    public function redirectTo()
    {
        return route('admin.layout');
    }

    public function logout(Request $request)
    {
        LoginLog::CreateLoginLog('退出登录');
        $this->guard()->logout();
        $request->session()->invalidate();
        return redirect(route('admin.loginForm'));
    }

    protected function guard()
    {
        return auth()->guard('admin');
    }

    protected function SessionToken($request)
    {
        $user = $this->guard()->user();
        $time = time();
        $singleToken = md5(getClientIp().$user->id.$time);
        Cache::tags(['System', 'LoginToken'])->forever($user->id, $time);
        $request->session()->put('LOGIN-TOKEN', $singleToken);
        LoginLog::CreateLoginLog('登录成功', $user->username);
    }
}

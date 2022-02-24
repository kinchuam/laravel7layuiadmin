<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Logs\AccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AccessRecordMiddleware
{
    public function handle($request, Closure $next)
    {
        if ($this->shouldLogOperation($request)) {
            AccessLog::CreateAccessLog($request);
        }
        return $next($request);
    }

    private function shouldLogOperation(Request $request)
    {
        return config('custom.operation_log.enable') && !$this->inExceptArray($request) && $this->inAllowedMethods($request->method());
    }


    private function inAllowedMethods($method)
    {
        $allowedMethods = collect(config('custom.operation_log.allowed_methods'))->filter();
        if ($allowedMethods->isEmpty()) {
            return true;
        }
        return $allowedMethods->map(function ($method) {
            return strtoupper($method);
        })->contains($method);
    }

    private function inExceptArray($request)
    {
        foreach (config('custom.operation_log.except') as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }
            $methods = [];
            if (Str::contains($except, ':')) {
                list($methods, $except) = explode(':', $except);
                $methods = explode(',', $methods);
            }
            $methods = array_map('strtoupper', $methods);
            if ($request->is($except) && (empty($methods) || in_array($request->method(), $methods))) {
                return true;
            }
        }
        return false;
    }

}

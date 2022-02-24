<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $e
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        return ($request->expectsJson() || $request->is('api'))
            ? $this->AjaxResponse($e)
            : parent::render($request, $e);
    }

    private function AjaxResponse($e): \Illuminate\Http\JsonResponse
    {
        $response = [
            "status" => 'error',
            "message" => 'Unknown exception',
        ];
        if ($e instanceof \Illuminate\Validation\ValidationException) {
            $response["code"] = 422;
            $response["message"] = $e->validator->errors()->first();
            return response()->json($response);
        }
        $error = $this->convertExceptionToResponse($e);
        $response["code"] = $error->getStatusCode();
        if(config('app.debug')) {
            $response["message"] = $e->getMessage();
        }
        return response()->json($response);
    }

    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? response()->json(["code" => 401, "status" => 'error', 'message' => $exception->getMessage()])
            : redirect()->guest(route('admin.login'));
    }
}

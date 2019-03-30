<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        //接管api的错误提示
        if ($exception instanceof ApiException){
            $error_info = $exception->getMessage();
            $return = array(
                'code' => '10000',
                'status_code' => '200',
                'message' => $error_info
            );
            if (strpos($error_info, '|')) {
                $error_info = explode('|', $error_info);
                $return['code'] = $error_info[0];
                $return['message'] = $error_info[1];
            }
            return response()->json($return, 200, array('Access-Control-Allow-Origin' => '*'), JSON_UNESCAPED_UNICODE);
        }
        return parent::render($request, $exception);
    }
}

<?php
namespace app\common\exception;
use Exception;
use think\exception\Handle;
use think\facade\Config;
use think\exception\HttpException;
use think\exception\ValidateException;
class Http extends Handle
{

    public function render(Exception $e)
    {
        // 在生产环境下返回code信息
        if (!config('app.app_debug')) {
            $statuscode = $code = 500;
            $msg = 'An error occurred';
            // 验证异常
            if ($e instanceof \think\exception\ValidateException) {
                $code = 0;
                $statuscode = 200;
                $msg = $e->getError();
            }
            // Http异常
            if ($e instanceof \think\exception\HttpException) {
                $statuscode = $code = $e->getStatusCode();
            }
            // 业务异常
            if ($e instanceof \app\common\exception\BusinessException) {
                $code = $e->getCode();
                $msg = $e->getMessage();
            }
            // 其它异常
            if ($e instanceof \Exception) {
                $msg = $e->getMessage();
            }
            return json(['code' => $code, 'msg' => $msg, 'time' => time(), 'data' => null], $statuscode);
        }

        //其它此交由系统处理
        return parent::render($e);
    }

}
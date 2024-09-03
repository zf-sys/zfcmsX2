<?php

namespace app\http\middleware;

class TriggerCron
{
    public function handle($request, \Closure $next)
    {
        // 异步触发 Cron 任务
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        run_cron_tasks();

        return $next($request);
    }
}

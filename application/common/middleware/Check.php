<?php

namespace app\common\middleware;

class Check
{
    public function handle($request, \Closure $next)
    {
        // if ($request->param('name') == 'think') {
        //     // return redirect('index/think');
        // }
        return $next($request);
    }
}
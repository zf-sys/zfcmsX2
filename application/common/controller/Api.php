<?php
namespace app\common\controller;

class Api
{
    public function __construct(){

    }
    public function common_act(){
        do_action('common_act',$this);
    }

    /**
     * @return void
     * 清除cookie
     */
    public function clear_cookie()
    {
        $admin = session('admin');
        if(!$admin){
            return '未登录';
        }
        cookie('sys_authhost_token', null, -3600);
        // 重定向或返回响应
        return redirect('/',302);
    }
}
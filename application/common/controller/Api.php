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

    /**
     * 获取系统版本
     * 子枫内容管理系统X2 v0.0.8
     */
    final public function version(){
        $version_conf = config()['version'];
        echo $version_conf['ver_name'].' '.$version_conf['version'];die;
    }
    public function update_license(){
        $file = base64_decode('Li9ydW50aW1lL1pGQ01T');
        if(file_exists($file)){
            @unlink($file);
        }
        echo 'update success!';die;
    }
}
<?php
// +----------------------------------------------------------------------
// | 子枫CMS管理系统
// +----------------------------------------------------------------------
// | Copyright (c)  http://store.zf-sys.com/
// | 子枫CMS管理系统提供免费使用,可使用此框架进行二次开发
// +----------------------------------------------------------------------
// | Author: 子枫 <287851074@qq.com>
// | 子枫社区:  http://bbs.90ckm.com/
// +----------------------------------------------------------------------
namespace app\admin\controller;
use app\common\controller\Base as Zfb;
use think\facade\Request;
use think\Db;
use zf\ZfAuth;
use think\Controller;

class Api extends Zfb
{
    public function __construct (){
        parent::__construct();
    }
    //通过token自动登录
    public function auto_login(){
        $token_site = config()['web']['site_token'];
        $token = input('token','');
        if($token==''){
            echo 'token不能为空';die;
        }
        if($token!=$token_site){
            echo 'token错误';die;
        }
        $userInfo = ZFTB('admin')->where('name', 'admin')->where('status', 1)->find();
        $admin  = $userInfo;
        session('admin', $admin);
        $web_config = config()['web'];
        if(isset($web_config['site_path']) && $web_config['site_path']!=''){
            $site_path = '/'.$web_config['site_path'].'/admin';
        }else{
            $site_path = '/admin';
        }
        $this->redirect($site_path);
    }
    public function store_install(){
        $this->zfapi_store_install();
    }
    //更新授权
    public function update_sq(){
        $_SESSION['_zf_temp_remote'] = '1';
         $this->zfauth = new ZfAuth();
        $this->zfauth->plugin_check('@update_sq@','alert');
        $this->success('更新成功');
    }
    public function plugin_act(){
        $this->zfapi_plugin_act();
    }

    public function site_file_md5(){
        $path = input('path','');
        $path_str = base64_decode($path);
        $this->temp_dir_list = explode(',', $path_str);
        $sys_list = $this->_api_get_sys_all_file();
        return json_encode($sys_list);
    }

    


    
    
  
    
}

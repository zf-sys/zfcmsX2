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

namespace app\index\controller;
use think\facade\Request;
use think\Db;
class Admin extends Base
{
    public function __construct (){
        parent::__construct();
        if(!session('admin'))
        {
            session('zf_login_tap_url',request()->url());
            $this->redirect('/admin/login/index');die; 
        }
        $this->tpl = $this->zf_tpl_suffix.'/admin/'.request()->action();
    } 
    public function _empty(){
        $this->error('没有此方法');
    }

    public function index()
    {
        if(request()->isPost()){
            $data = input('post.');
            try{
                // foreach($data as $k=>$vo){
                //     $data[$k] = base64_encode($vo);
                // }
                $res = $this->Yun->save_plugin_config_db($data,'theme','',$this->zf_tpl_suffix);
                return ZFRetMsg($res,'保存成功','保存失败');
            }catch(\Exception $e){
                return ZFRetMsg(0,'',$e->getMessage());
            }
            
        }
        $data = $this->Yun->get_plugin_config_db('theme','',$this->zf_tpl_suffix);//保存在数据库
        $this->assign("config",$data);
        $this->assign("type",'基本设置');
        return view($this->tpl);
    }
    
    
    

}

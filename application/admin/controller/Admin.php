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
namespace app\admin\Controller;
use app\common\controller\Base as Zfb;
use think\Controller;
use think\facade\Request;
use think\Db;
class Admin extends Zfb
{
    public function __construct ()
    {
        parent::__construct();
        $admin = session('admin');
        if(!$admin){
            if(request()->isPost()){
                return jserror('请先登录后再操作');
            }
            if(request()->controller()!='Index' &&  request()->action()=='index'){
                session('zf_login_tap_url',get_url());
            }
            $this->redirect('/admin/login/index');die; 
        }
        $this->assign('admin',$admin);
        $web_config = ZFC('webconfig','db','arr');
        $this->web_config = $web_config;
        $this->assign('web_config',$web_config);

        //判断是否修改密码
        $this->check_password_token($admin);
        //判断是否认证
        if(!isset(config()['zf_auth']['key']) || !isset(config()['zf_auth']['sc']) || !isset(config()['zf_auth']['email']) ||  config()['zf_auth']['key']=='' ||  config()['zf_auth']['sc']=='' ||  config()['zf_auth']['email']=='' ){
            $this->assign('is_zf_auth','n');
        }else{
            $this->assign('is_zf_auth','y');
        }
        $this->common_tag = [];
        $this->common_select_tag = [];
        /*
        //demo:
        $this->common_tag = ['wsid'=>11,'wsname'=>'aaa'];//通用的数据
        $this->common_select_tag = [['blog_id','=',$blog['id']]];
        */
        // 读取权限
        $this->z_role_list = get_admin_role(session("admin.gid"));
        // 默认给个权限
        // $role_list[9999] = 'Index/index'; 
        $m = strtolower(request()->module());
        $c = strtolower(request()->controller());
        $a = strtolower(request()->action());
        // $nodeStr =  ucwords($c) . '/' . $a;
        $this->mca =  $m.'/'.ucwords($c) . '/' . $a;

        //插入日志
        if(isset_arr_key($web_config,'is_log') ==1){
            $log['action'] = $m.'/'.$c.'/'.$a ;
            $log['ctime'] = time() ;
            $log['ip'] = request()->ip();
            $log['uid'] = $admin['id'] ;
            $log['post'] = json_encode(input('param.'));
            $log['method'] = request()->method();
            ZFTB('admin_log')->insert($log);
        }
        if(isset($web_config['site_path']) && $web_config['site_path']!=''){
            $this->site_path = '/'.$web_config['site_path'].'/';
        }else{
            $this->site_path = '/';
        }
        $this->assign('site_path',$this->site_path);
        
        $lang = session('zf_lang');
        if(!isset($lang)){ 
            $lang = '1'; 
            $this->common_tag['lang'] = '';
            $this->common_select_tag[] = ['lang','=',''];
        }else{
            $lang = strtolower($lang);
            if($lang==1){
                $this->common_tag['lang'] = '';
                $this->common_select_tag[] = ['lang','=',''];
            }else{
                $this->common_tag['lang'] =$lang;
                $this->common_select_tag[] = ['lang','=',$lang];
            }
        }
        $this->lang = $lang;
        $this->assign('lang',$lang);
        // $data = array_merge($data,$this->common_tag);
        // $where = array_merge($where, $this->common_select_tag);

        if(($c=='index' && ($a=='index' || $a=='welcome' || $a=='get_menu' || $a=='two_password'))  || $c=='zfyun' || $c=='api'){
        }else{
            // 判断是否输入二级秘密
            if(!isset($admin['two_pwd']) || $admin['two_pwd']=='' ){
                session('admin_two_pwd','11');
            }else{
                $_two_pwd = session('admin_two_pwd');
                if(!$_two_pwd){
                    session('zadmin_two_tap_url',get_url());
                    $this->redirect('/admin/index/two_password');die; 
                }else{
                }
            }
            
            
            
        }

    }
    /**
     * 检测是否修改密码,重新登录
     */
    private function check_password_token($admin){
        //每隔10s检测一次
        $old_time = session('admin_token_time');
        $new_time = time();
        if($new_time - $old_time > 100){
            session('admin_token_time',$new_time);
            //检测
            // echo '重新检测';
            $admin_db = db('admin')->where(['id'=>$admin['id']])->find();
            if($admin_db['token']!=$admin['token']){
                session('admin',null);
                $this->redirect('/admin/login/index');die; 
            }
        }
    }



}
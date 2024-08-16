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
use think\Controller;
use think\facade\Request;
use think\Db;
use Wmc1125\TpFast\GoogleAuthenticator; 
use zf\ZfAuth;
use think\captcha\Captcha;

class Login extends Controller
{
    public function __construct (){
        parent::__construct();
        zf_to_site_url();
        $web_config = ZFC('webconfig','db','arr');
        $this->web_config = $web_config;
        $this->assign('web_config',$web_config);
        if(isset_arr_key($web_config,'site_path','')!=''){
            $this->site_path = '/'.isset_arr_key($web_config,'site_path','').'/';
        }else{
            $this->site_path = '/';
        }
        
        $this->assign('site_path',$this->site_path);
        doZfActionInit();
    }
    /**
     * @Notes:登录页
     * @Interface index
     * @return \think\response\View
     * @author: 子枫
     * @Time: 2019/11/13   10:56 下午
     */
    public function index()
    {
        if(session('admin')){
            $this->error('你已登录,跳转中...','index/index'); 
        }
        $admin_path = isset_arr_key($this->web_config,'admin_path','');
        if(isset($admin_path) && $admin_path!=''){
            $token = input('token','');
            if($token!=$admin_path){
                die;
            }
        }
        $right_img =  ZFC('webconfig.admin_login_right_pic')==''?'//static.zf-sys.com/zfcms/image/dlbox.svg':ZFC('webconfig.admin_login_right_pic');
        $this->assign('right_img',$right_img);
        do_action('admin_login_before',$this);
        return view('login/index');
    }
    
   

    /**
     * @Notes:后台登录
     * @Interface login
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   10:56 下午
     */
    public function login()
    {
        $admin_path = isset_arr_key($this->web_config,'admin_path','');
        if(isset($admin_path) && $admin_path!=''){
            if(isset($_SERVER['HTTP_REFERER'])){
                $is_ok = strpos($_SERVER['HTTP_REFERER'], $admin_path);
                if(!$is_ok){
                    return jserror('后台登录路径错误');
                }
            }else{
                return jserror('err');
            }
        }

        if(request()->isPost()){
            $data = input('post.');
            if($data['name']==''){
                return jserror('用户名不能为空');
            }
            $userInfo = ZFTB('admin')->where('name', $data['name'])->where('pwd', md5('zfcms-'.$data['pwd']))->where('status', 1)->find();
            do_action('admin_login_after',$this,$data,$userInfo);
            if (!$userInfo) {
                return jserror('用户名或者密码不正确 或没有权限');
            }
            $admin  = $userInfo;
            session('admin', $admin);
            if(!session('zf_login_tap_url')){
                $url= url('admin/index/index');
            }else{
                $url= session('zf_login_tap_url');
                session('zf_login_tap_url',null);
            }
            return jssuccess($url);
        }else{
            return jserror('异常访问');
        }
    }

    /**
     * @Notes:后台管理员退出
     * @Interface loginout
     * @author: 子枫
     * @Time: 2019/11/13   10:57 下午
     */
    public function loginout(){
        session('admin',null);
        session('admin_two_pwd',null);
        $url_tmp = url('admin/login/loginout');
        $url_login = url('/');
        if(session('admin')){
            $this->redirect($url_tmp,302);
        }else{
            $this->redirect($url_login,302);
        }
    }
  

}

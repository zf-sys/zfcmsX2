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
        $this->assign('web_config',config());
        if(config('web.site_path')){
            if(config('web.site_path')!=''){
              $this->site_path = '/'.config('web.site_path').'/';
            }else{
              $this->site_path = '/';
            }
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
        $admin_path = config('web.admin_path');
        if(isset($admin_path) && $admin_path!=''){
            $token = input('token','');
            if($token!=$admin_path){
                die;
            }
        }
        return view('login/index3');
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
        $admin_path = config('web.admin_path');
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
            $userInfo = ZFTB('admin')->where('name', $data['name'])->where('pwd', md5('zfcms-'.$data['pwd']))->where('status', 1)->find();
            if (!$userInfo) {
                return jserror('用户名或者密码不正确 或没有权限');
            }
            doZfAction('sys_adminlogin_parm',['type'=>'action','data'=>$data]);
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
        $url_tmp = url('admin/login/loginout');
        $url_login = url('/');
        if(session('admin')){
            $this->redirect($url_tmp,302);
        }else{
            $this->redirect($url_login,302);
        }
    }
  

}

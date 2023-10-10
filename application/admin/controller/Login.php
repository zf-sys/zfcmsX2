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
        $this->assign('web_config',config());
        if(ZFC("webconfig.site_path")!=''){
            $this->site_path = '/'.ZFC("webconfig.site_path").'/';
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
        $admin_path = ZFC("webconfig.admin_path");
        if(isset($admin_path) && $admin_path!=''){
            $token = input('token','');
            if($token!=$admin_path){
                die;
            }
        }
        $init_sql = ZFC("zf_auth.init_sql",'file');
        if($init_sql!=1){
            $this->redirect('/common/base/upgrade_sys_sql');
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
        $admin_path = ZFC("webconfig.admin_path");
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
            $max_login_err_num = intval(ZFC("webconfig.max_login_err_num"));
            if($max_login_err_num==0){
                $max_login_err_num = 3;
            }
            $login_interval_time = intval(ZFC("webconfig.login_interval_time"));
            if($login_interval_time==0){
                $login_interval_time = 5;
            }
            try{
                $err_login_num = db('admin_login_log')->where([['ip','=',request()->ip()],['ctime','between time',[date("Y-m-d H:i:s",time()-$login_interval_time*60), date("Y-m-d H:i:s")]]])->order('id asc')->count();
                if($err_login_num>=$max_login_err_num){
                    return jserror('登录错误超过'.$max_login_err_num.'次,请'.$login_interval_time.'分钟后重试');
                }
                $userInfo = ZFTB('admin')->where('name', $data['name'])->where('pwd', md5('zfcms-'.$data['pwd']))->where('status', 1)->find();
                if (!$userInfo) {
                    save_admin_login($data['name'],$data,0);
                    return jserror('用户名或者密码不正确 或没有权限');
                }
                doZfAction('sys_adminlogin_parm',['type'=>'action','data'=>$data]);
                save_admin_login($data['name'],$data,1);
                $admin  = $userInfo;
                session('admin', $admin);
                if(!session('zf_login_tap_url')){
                    $url= url('admin/index/index');
                }else{
                    $url= session('zf_login_tap_url');
                    session('zf_login_tap_url',null);
                }
                return jssuccess($url);
            } catch (\Exception $e) {
                return jserror($e->getMessage());
            }
            
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

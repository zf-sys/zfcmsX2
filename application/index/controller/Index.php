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
use think\Db;
use think\facade\Request;
use think\facade\Hook;
class Index extends Base
{

	public function __construct (){
        parent::__construct();
        $this->zf_tpl_suffix = ZFC('zf_tpl_suffix');
        if($this->zf_tpl_suffix==''){
            $this->zf_tpl_suffix = '';
        }
        if(input('theme')){
            if(input('theme')=='-1'){
                cookie('theme',null);
            }else{
                cookie('theme',input('theme'));
            }
        }
        if(cookie('theme')){
            $this->zf_tpl_suffix = cookie('theme');
        }
        $this->zf_tpl_suffix  = strtolower($this->zf_tpl_suffix);

        
    }
    
   
    public function hook($func=''){   
        
        $theme_name = $this->zf_tpl_suffix;
        $route_info= request()->routeInfo();
        if(!isset($route_info['option']['append']['controller'])){ die('未配置controller'); }
        if(!isset($route_info['option']['append']['menu_type'])){ die('未配置menu_type'); }
        $_controller = $route_info['option']['append']['controller'];
        $_arr_router = explode('@',$_controller);
        $controller = $_arr_router[0];
        $action = $_arr_router[1];
        \think\Loader::addAutoLoadDir( './theme');

        $controller_name = '\\'.$theme_name.'\controller\\'.ucwords($controller);
        $base = new $controller_name();
        $ret = $base->$action();
        $this->tpl = $theme_name.'/'.$controller.'/'.$action;
        
        if(is_object($ret)){
            $obj_name = get_class($ret);
            if($obj_name=='think\response\View'){
                if($ret->getData()!=$theme_name.'/index/hook'){
                    return view($ret->getData());
                }else{
                    return view($this->tpl);
                }
            }elseif($obj_name=='think\response\Redirect'){
                return redirect($ret->getData());
            }
            die;
        }else{
            return $ret;
        }
    }
    public function index(){
       	$this->assign('page','');
       	//最新文章
        $where = [['p.status','=',1],['c.status','=',1],['p.ctime','<',time()]];
        $where[] =['p.lang','=',$this->lang] ;
        $list = Db::name('post p')
                ->field('p.*,c.name as c_name')
                ->where($where)
                ->join('category c','c.cid=p.cid')
                ->order('p.ctime desc,p.id desc')->paginate(15);
       	$this->assign('list',$list);
        
        $seo['title'] = ZFC("webconfig.site_title");
        $seo['keywords'] = ZFC("webconfig.site_keywords");
        $seo['description'] = ZFC("webconfig.site_description");
        $this->assign('seo', $seo);
        // doZfAction('home_tdk',['type'=>'index','data'=>'','request'=>request()]);

        if(isset($this->theme_config['template_home']) && $this->theme_config['template_home']!=''){
            $tpl = $this->zf_tpl_suffix.'/index/'.$this->theme_config['template_home'];
        }else{
            $tpl = $this->zf_tpl_suffix.'/index/index';
        }
        if(isset($this->cms_config['site_home_url']) && $this->cms_config['site_home_url']!=''){
           $this->redirect($this->cms_config['site_home_url'],302);
        }
        return view($tpl);
    }
    public function reg(){
        if(session('home')!=null){
            $this->redirect('/user',302);
        }
        $t = input('t','0');
        if(request()->isAjax()){
            if($t=='register'){
                $data = input('post.');
                //验证数据
                \Wmc1125\TpFast\ZfTool::check_data('emptyy',$data['name'],'用户名不能为空');
                \Wmc1125\TpFast\ZfTool::check_data('emptyy',$data['pwd'],'密码不能为空');
                \Wmc1125\TpFast\ZfTool::check_data('emptyy',$data['repwd'],'确认密码不能为空');
                if($data['pwd']!=$data['repwd']){ return jserror('两次密码输入不一致'); }
                unset($data['repwd']);
                unset($data['action']);
                //查询是否已存在
                $is_res = Db::name('user')->where(['name'=>$data['name']])->find();
                if($is_res){ 
                    if($is_res['status']==1){
                        return jserror('该账号已被注册'); 
                    }elseif($is_res['status']=='0'){
                        return jserror('该账号已加入小黑屋'); 
                    }else{
                        $res = null;
                    }
                }else{
                    $data['pwd'] = md5('zfcms-'.$data['pwd']);
                    $data['ctime'] = time();
                    $data['status'] = 2;
                    $data['ip'] = request()->ip();
                    $data['login_act_code'] = md5('zfcms-'.json_encode($data));
                    $res = Db::name('user')->insertGetId($data);
                    $uid = $res;
                }
                if($res){
                    $user = Db::name('user')->where(['id'=>$res])->find();
                    session('home',$user);
                    return jssuccess('注册成功');
                }else{
                    return jserror('注册失败');
                }
            }
            // elseif($t=='tel'){
            //     session('home_tel_code',null);
            //     $tel = input('tel',0);
            //     \Wmc1125\TpFast\ZfTool::check_data('tel',$tel,'手机号错误');                
            //     if($tel==0){ return jserror('请输入手机号');  }
            //     if(Db::name('user')->where(['tel'=>$tel])->find()){ return jserror('该手机号已被注册'); }
            //     //获取验证码
            //     // $data['code'] = mt_rand(1000,9999);
            //     $data['code'] = 1234;
            //     $data['tel'] = $tel;
            //     ###########执行发送验证码###########
            //     // $send_res = send_aliyun_sms($data['tel'],['code'=> $data['code']],'用户注册');
            //     $send_res = 'ok';
            //     if($send_res=='ok'){
            //         session('home_tel_code',$data);
            //         if(session('home_tel_code')!=null){ 
            //             return jssuccess('发送成功');  
            //         }else{
            //             return jserror('发送失败');  
            //         }
            //     }else{
            //             return jserror($send_res);  
            //     }
                
            // }elseif($t=='email'){
            //     session('home_email_code',null);
            //     $email = input('email',0);
            //     // \Wmc1125\TpFast\ZfTool::check_data('email',$email,'手机号错误');                
            //     if($email=='0'){ return jserror('请输入用户邮箱');  }
            //     if(Db::name('user')->where(['email'=>$email])->find()){ return jserror('该邮箱已被注册'); }
            //     //获取验证码
            //     $data['code'] = mt_rand(1000,9999);
            //     $data['email'] = $email;
            //     ###########执行发送验证码###########
            //     $code = '注册内容 你的验证码是'.$data['code'];
            //     $title = '用户注册';
            //     $send_res = send_email($email,$title,$code);

            //     if($send_res=='ok'){
            //         session('home_email_code',$data);
            //         if(session('home_email_code')!=null){ 
            //             return jssuccess('发送成功');  
            //         }else{
            //             return jserror('发送失败');  
            //         }
            //     }else{
            //             return $send_res;  
            //     }
                
            // }
               
        }
        $this->assign('two_cur','register');
        $this->assign('tjm',input('tjm',''));
        if(session('home')==null){
            return view($this->tpl);
        }else{
            return redirect('user/index');
        }

    }
   
    public function login(){
        if(session('home')!=null){
            $this->redirect('/user',302);
        }
        if(request()->isAjax()){
            $data = input('post.');
            //验证数据
            \Wmc1125\TpFast\ZfTool::check_data('emptyy',$data['pwd'],'密码不能为空');
            //查询是否已存在
            $is_login = Db::name('user')->where(['name'=>$data['name']])->find();
            if(!$is_login){ return jserror('账号未注册'); }
      
            if ($is_login['pwd'] != md5('zfcms-'.$data['pwd'])) {
                return jserror('密码错误');
            }
            if($is_login['status']=='0'){
                return jserror('该账号已被暂停使用');
            }
            session('home',$is_login);
            if(session('home')!=null){
                Db::name('user')->where(['name'=>$data['name']])->update(['login_time'=>time()]);
                $is_login['login_time'] = time();
                session('home',$is_login);
                Db::name('login_log')->insert(['ctime'=>time(),'uid'=>$is_login['id'],'ip'=>request()->ip(),'type'=>'登陆']);
                $msg['msg'] = '登录成功';
                return jssuccess($msg);
            }else{
                $msg['msg'] = '登录失败';
                return jserror($msg);
            }
               
        }
        $this->assign('two_cur','login');
        if(session('home')==null){
    		if(is_weixin()){
                //公众号登录(微信端)
                $token = md5(request()->ip().mt_rand(1,999999));
                session('gzh_wxlogin_token',$token);
                $url_token = [
                    'user'=>'11',
                    'type'=>'wx_client',
                    'token'=>$token,
                    'callbak'=>get_domain().'/gzh_login_callback',
                ];
                $url_token = base64_encode(zf_encrypt(base64_encode(json_encode($url_token))));
                $url = "//api.wangmingchang.com/addons/zf_api.wx/login_gzh/url_token/".$url_token;
            }else{
                $url = "";
            }
            $this->assign('url',$url);

            $gzh_url = '//'.request()->host().'/login_gzh';
            $this->assign('gzh_url',$gzh_url);
            // dd($this->tpl);
            return view($this->tpl);
        }else{
            session('home',null);
            return redirect('user');
        }

    }
    public function forget(){
        if(request()->isAjax()){
            $t = input('t','0');
            if($t=='forget'){
                $data = input('post.');
                if(session('home_email_code')==null){ return jserror('请先获取验证码');  }
                if(session('home_email_code')['email']!=$data['email'] || session('home_email_code')['code']!=$data['code']){
                    return jserror('验证码错误');
                }
                //验证数据
                \Wmc1125\TpFast\ZfTool::check_data('email',$data['email'],'账号错误');
                \Wmc1125\TpFast\ZfTool::check_data('emptyy',$data['pwd'],'密码不能为空');
                \Wmc1125\TpFast\ZfTool::check_data('emptyy',$data['repwd'],'确认密码不能为空');
                if($data['pwd']!=$data['repwd']){ return jserror('两次密码输入不一致'); }
                //判断验证码
                if($data['email'] == session('home_email_code')['email']){
                    if($data['code']!=session('home_email_code')['code']){
                        return jserror('验证码错误');
                    }
                }else{
                    return jserror('账号验证失败');
                }
                unset($data['repwd']);
                unset($data['code']);
                //查询是否已存在
                $is_user = Db::name('user')->where(['email'=>$data['email']])->find();
                if(!$is_user){ return jserror('该账号未注册'); }

                $data['pwd'] = md5('zfcms-'.$data['pwd']);
                $res = Db::name('user')->where(['email'=>$data['email']])->update($data);
                if($res){
                    $msg['msg'] = '密码修改成功';
                    return jssuccess($msg);
                }else{
                    $msg['msg'] = '密码修改失败';
                    return jserror($msg);
                }
            }elseif($t=='email'){
                session('home_email_code',null);
                $email = input('email',0);
                \Wmc1125\TpFast\ZfTool::check_data('email',$email,'账号错误');                
                if($email==0){ return jserror('请输入账号');  }
                if(!Db::name('user')->where(['email'=>$email])->find()){ return jserror('该账号未被注册'); }
                //获取验证码
                $data['code'] = 1234;
                // $data['code'] = mt_rand(1000,9999);
                $data['email'] = $email;
                ###########执行发送验证码###########
                $send_res = 'ok';
                // $send_res = send_aliyun_sms($data['email'],['code'=> $data['code']],'找回密码');
                if($send_res=='ok'){
                    session('home_email_code',$data);
                    if(session('home_email_code')!=null){ 
                        return jssuccess('发送成功');  
                    }else{
                        return jserror('发送失败');  
                    }
                }else{
                        return jserror($send_res);  
                }
            }
        }
        $this->assign('two_cur','forget');
        $t = input('t','0');
        if($t==1){
            //发送邮件
            $email = input('email');
            $res = db('user')->where(['email'=>$email])->find();
            if(!$res){
                $this->error('邮箱不存在');
            }
            $forget_email_data['code'] = mt_rand(1000,9999);
            $forget_email_data['email'] = $email;
            if(!session('forget_email_data')){
                session('forget_email_data', $forget_email_data);
                ###########执行发送验证码###########
                $code = '找回密码内容 你的验证码是'.$forget_email_data['code'];
                $title = '用户找回密码';
                $send_res = send_email($email,$title,$code);
                if($send_res!='ok'){
                    $this->error('发送邮件错误',$send_res);
                }
                $this->assign('forget_email_data',$forget_email_data);
            }else{
                if(session('forget_email_data.email')!=$email){
                   session('forget_email_data', $forget_email_data);
                    ###########执行发送验证码###########
                    $code = '找回密码内容 你的验证码是'.$forget_email_data['code'];
                    $title = '用户找回密码';
                    $send_res = send_email($email,$title,$code);
                    if($send_res!='ok'){
                        $this->error('发送邮件错误',$send_res);
                    }
                    $this->assign('forget_email_data',$forget_email_data); 
                }else{
                    $this->assign('forget_email_data',session('forget_email_data')); 
                }
            }
            $this->tpl = 'def/index/forget1';
        }elseif($t==2){
            if(!session('forget_email_data')){
                $this->error('error','/');
            }
            //判断验证码是否正确
            $sess_data = session('forget_email_data');
            $email = input('email');
            $code = input('code');
            if($sess_data['email']!=$email  ||$sess_data['code']!=$code  ){
                $this->error('验证失败','/forget?t=1&email='.$email);
            }


            $this->tpl = 'def/index/forget2';
        }elseif($t==3){
            if(!session('forget_email_data')){
                $this->error('error','/');
            }
            //判断验证码是否正确
            $sess_data = session('forget_email_data');
            $email = input('email');
            $code = input('code');
            if($sess_data['email']!=$email  ||$sess_data['code']!=$code  ){
                $this->error('验证失败','/forget?t=1&email='.$email);
            }
            $pwd = input('pwd');
            $repwd = input('repwd');
            if($pwd!=$repwd){
                $this->error("两次密码不一样",'/forget?t=2&email='.$email.'&code='.$code);
            }
            $up_res = db('user')->where(['email'=>$email])->update(['pwd'=>md5('zfcms-'.$pwd)]);
            if(!$up_res){
                $this->error("修改失败",'/forget?t=2&email='.$email.'&code='.$code);
            }else{
                $this->error("修改成功,请重新登录",'/login');
            }
        }else{
            
        }

        return view($this->tpl);

    }

    //公众号登录
    public function login_gzh(){ 
		//生成token
		$token = md5(request()->ip().mt_rand(1,999999));
		session('gzh_login_token',$token);
		$url_token = [ 
            'user'=>'11',
			'token'=>$token,
			'type'=>'wx_scan',
			'callbak'=>get_domain().'/gzh_login_callback',
		];
        $url_token = base64_encode(zf_encrypt(base64_encode(json_encode($url_token))));

		// $url_token_arr = json_decode(base64_decode(zf_decrypt(base64_decode($url_token))),true);
		$url = "https://mctool.wangmingchang.com/api/tool/create_qr_code?msg=http://api.wangmingchang.com/addons/zf_api.wx/login_gzh/url_token/".$url_token;
		$this->assign('url',$url);
		// dd($url);
		return  view($this->tpl);
	}
	public function gzh_login_is(){
	    $act_code =  session("gzh_login_token");
	    $res = Db::name('user')->where("token='".$act_code."'")->find();
	    if($res){
			session('home',$res);
	        return jssuccess("登录成功");
	    }else{
	        return jserror("error");
	    }
	}
	public function gzh_login_callback(){
        $token = input('token','');
		$type = input('type','');
		if($type=='wx_client'){
			$act_code =  session("gzh_wxlogin_token");
			if($token!=$act_code){
				return 'login token error';
			}
			$data_str = input('d','');
			$_data = base64_decode($data_str);
			$user = json_decode($_data,true);
		}elseif($type=='wx_scan'){
			//微信扫码
			$user = input('post.');
		}else{
			return 'eeee...';
		}
		$token = $user['token'];
		$user_data['openid'] = $user['openid'];
		$user_data['name'] = $user['name'];
		$user_data['pic'] = $user['pic'];
		$user_data['ctime'] = time();
		$user_data['ip'] = request()->ip();
		$is = Db::name('user')->where(['openid'=>$user_data['openid']])->order('id desc')->find();
		if(!$is){
			$user_data['token'] = $token;
			$res = Db::name('user')->insert($user_data);
		}else{
			$is = Db::name('user')->where(['openid'=>$user_data['openid']])->update(['token'=>$token]);
		}
		$is = Db::name('user')->where(['openid'=>$user_data['openid']])->order('id desc')->find();
        if($token==$is['token']){
    		if($type=='wx_client'){
    			session('home',$is);
                $this->success('登录成功','/user');
            }else{
                return '登录成功';
            }
		}else{
            if($type=='wx_client'){
				$this->error('error','/');
            }else{
                return 'error';
            }
		}
	}

    public function loginout(){
        // Db::name('login_log')->insert(['ctime'=>time(),'uid'=>session('home')['id'],'ip'=>request()->ip(),'type'=>'退出']);
        session('home',null);
        $url_tmp = Request::instance()->domain().'/loginout';
        $url_login = Request::instance()->domain().'/index';
        if(session('home')){
            $this->redirect($url_tmp,302);
        }else{
            $this->redirect($url_login,302);
        }
    }
}



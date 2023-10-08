<?php
// WebSite: http://www.zf-sys.com/
// Document:  http://bbs.90ckm.com/
// Bbs:  http://bbs.90ckm.com/
// Github: https://github.com/zf-sys/zfcmsX2
// Gitee:https://gitee.com/zf-sys/zfcmsX2
// Feedback: https://support.qq.com/products/166743
namespace app\common\controller;
use think\Controller;
use think\Db;
use GuzzleHttp\Client;
use think\facade\Request;
use think\captcha\Captcha;
class Base extends Controller
{
    public function __construct ($load = true){
        if(is_file('./extend/zf/Yun.php')){
            if(!extension_loaded("IonCube Loader")) {     
                echo base64_decode('VGhlIGlvbmN1YmUgZXh0ZW5zaW9uIGlzIG5vdCBjdXJyZW50bHkgaW5zdGFsbGVkIGFuZCBjYW5ub3QgYmUgdXNlZCAh')."  <a href='".ZFC('version.api_domain','file')."/question_list.html#ioncube_install' target='_blank'>".base64_decode('PHNwYW4gc3R5bGU9ImNvbG9yOnJlZDsiPklvbmN1YmXmianlsZXmnKrlronoo4Us54K55Ye75p+l55yL5a6J6KOF5pWZ56iLPC9zcGFuPg==')."</a>";die;
            }
            if($load){
                $this->Yun = new \zf\Yun(1); //检测全
            }else{
                $this->Yun = new \zf\Yun(2);//yun工具
            }
            $this->is_professional_edition = true;
        }else{
            $this->is_professional_edition = false;
        }
        if(extension_loaded("IonCube Loader")) {     
            $this->sqb_error_msg = base64_decode(base64_encode("<div>".base64_decode('VGhlIGNvbW11bml0eSB2ZXJzaW9uIGRvZXMgbm90IHN1cHBvcnQgdGhpcyBmZWF0dXJlLiBDbGljayB0byB1cGdyYWRlIHRvIHRoZSBwcm9mZXNzaW9uYWwgdmVyc2lvbiA=')."<a href='".ZFC('version.api_domain','file').'/yun_down_list?v='.ZFC('version.version','file')."' target='_blank'>".base64_decode('PHNwYW4gc3R5bGU9ImNvbG9yOnJlZDsiPueCueWHu+S4i+i9vVl1bi5waHA8L3NwYW4+')."</a></div>"));
        }else{
            $this->sqb_error_msg = base64_decode(base64_encode("<div>".base64_decode('VGhlIGNvbW11bml0eSB2ZXJzaW9uIGRvZXMgbm90IHN1cHBvcnQgdGhpcyBmZWF0dXJlLiBDbGljayB0byB1cGdyYWRlIHRvIHRoZSBwcm9mZXNzaW9uYWwgdmVyc2lvbiA=')."<a href='".ZFC('version.api_domain','file').'/yun_down_list?v='.ZFC('version.version','file')."' target='_blank'>".base64_decode('PHNwYW4gc3R5bGU9ImNvbG9yOnJlZDsiPueCueWHu+S4i+i9vVl1bi5waHA8L3NwYW4+')."</a>|<a href='".ZFC('version.api_domain','file')."/question_list.html#ioncube_install' target='_blank'>".base64_decode('PHNwYW4gc3R5bGU9ImNvbG9yOnJlZDsiPklvbmN1YmXmianlsZXmnKrlronoo4Us54K55Ye75p+l55yL5a6J6KOF5pWZ56iLPC9zcGFuPg==')."</a></div>"));
        }
        $this->module = strtolower(request()->module());
        $this->controller = strtolower(request()->controller());
        $this->action = strtolower(request()->action());
        $this->u_key = config()['zf_auth']['key'];
        $this->s_ver = config()['version']['version'];
        $this->s_soft_id = config()['version']['soft_id'];

        if($this->module=='addons'){
            if(!$this->is_professional_edition){
                echo $this->sqb_error_msg; die;
            }
        }else{
            $this->plugin_name = ''; 
        }

        //模板
        if($load){
            parent::__construct();
            //初始化钩子(index/admin)
            if($load){
                if(in_array($this->module,['index','admin'])){
                    doZfActionInit();
                }
            }
            if($this->module=='index'){
                if($this->is_professional_edition){
                    $this->home_init();
                }else{
                    $this->_home_init();
                }
            }
            if($this->module=='admin'){
                if($this->controller=='api' ){
                    if($this->action!='site_file_md5'){
                        if($this->is_professional_edition){
                            $this->admin_api_init();
                        }else{
                            $this->_admin_api_init();
                        }
                    }
                }else{
                    if($this->is_professional_edition){
                        $this->admin_init();
                    }else{
                        $this->_admin_init();
                    }
                }
            }
            if($this->module=='addons'){
                if($this->is_professional_edition){
                    $this->addons_init();
                }else{
                    echo $this->sqb_error_msg; die;
                }
            }
        }
        if($this->module=='common'){
            if($this->is_professional_edition){
                $methodName = $this->action;
                $this->Yun->$methodName();
            }else{
                echo $this->sqb_error_msg; die;
            }
        }
        
    }
    
    
    public function __call( $methodName, $arguments){
        if($this->is_professional_edition){
            if(empty($arguments)){
                return $this->Yun->$methodName();
            }else{
                $count =  count($arguments);
                switch ($count){
                    case 1:
                        return $this->Yun->$methodName($arguments[0]);
                        break;
                    case 2:
                        return $this->Yun->$methodName($arguments[0],$arguments[1]);
                        break;
                    case 3:
                        return $this->Yun->$methodName($arguments[0],$arguments[1],$arguments[2]);
                        break;
                    case 4:
                        return $this->Yun->$methodName($arguments[0],$arguments[1],$arguments[2],$arguments[3]);
                        break;
                    case 5:
                        return $this->Yun->$methodName($arguments[0],$arguments[1],$arguments[2],$arguments[3],$arguments[4]);
                        break;
                    case 6:
                        return $this->Yun->$methodName($arguments[0],$arguments[1],$arguments[2],$arguments[3],$arguments[4],$arguments[5]);
                        break;
                    default:
                        return $this->Yun->$methodName();
                }
            }
        }
    }
    
    public function zf_down_load(){
        $this->zfyun_down_load();
    }
    /**
     * gourl跳转
     * 两种使用方式
     * /gourl?url=bd
     * /gourl?url=https://www.baidu.com
     */
    public function gourl(){
        $t_url = input('url','');
        // gourl_tokens
        $gourl_tokens = ZFC('gourl_tokens');
        $cars = [];
        //自定义跳转地址,token特殊字符
        if($gourl_tokens!=''){
            $cars = explode("\r\n", $gourl_tokens);
            foreach($cars as $k=>$val){
                $cars[$k] = explode('@', $val);
            }
        }
        if(strlen($_SERVER['REQUEST_URI']) > 384 || strpos($_SERVER['REQUEST_URI'], "eval(") || strpos($_SERVER['REQUEST_URI'], "base64")) {
            @header("HTTP/1.1 414 Request-URI Too Long");
            @header("Status: 414 Request-URI Too Long");
            @header("Connection: Close");
            @exit;
        }
        foreach($cars as $k=>$val){
            if($t_url==$val[0] ) {
                $t_url = $val[1];
                $t_vip = 1;
            }
        }
        if(!empty($t_url)) {
            //判断取值是否加密
            if ($t_url == base64_encode(base64_decode($t_url))) {
                $t_url = base64_decode($t_url);
            }
            //对取值进行网址校验和判断
            preg_match('/^(http|https|thunder|qqdl|ed2k|Flashget|qbrowser):\/\//i',$t_url,$matches);
            if($matches){
                $url=$t_url;
                $title='页面加载中,请稍候...';
            } else {
                preg_match('/\./i',$t_url,$matche);
                if($matche){
                    $url='http://'.$t_url;
                    $title='页面加载中,请稍候...';
                } else {
                    $url = 'http://'.$_SERVER['HTTP_HOST'];
                    $title='参数错误，正在返回首页...';
                }
            }
        } else {
            $title = '参数缺失，正在返回首页...';
            $url = 'http://'.$_SERVER['HTTP_HOST'];
        }
        $this->assign('url',$url);
        $this->assign('title',$title);
        return view();
    }

    private function _home_init(){
        zf_to_site_url();
    }
    private function _admin_init(){
        if(!isset(config()['zf_auth']['key']) || !isset(config()['zf_auth']['sc']) || !isset(config()['zf_auth']['email']) ||  config()['zf_auth']['key']=='' ||  config()['zf_auth']['sc']=='' ||  config()['zf_auth']['email']=='' ){
            $this->assign('is_zf_auth','n');
        }else{
            $this->assign('is_zf_auth','y');
        }
    }
    private function _admin_api_init(){
        
        $this->site_token = input('token','');
        if($this->site_token==''){
            $this->site_token = input('site_token','');
        }
        if( $this->site_token==''){
            return jserror('token无权限,请在网站后台打开商店');
        } 
        if($this->site_token!=config()['web']['site_token']){
            return jserror('站点token错误,请在网站后台打开商店');
        }
        $this->temp_plugin = '';
    }
}


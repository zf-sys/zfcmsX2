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
        $zfcommon = new \zf\ZfCommon();
        $zfcommon->load_plug_tag();

        $this->module = strtolower(request()->module());
        $this->controller = strtolower(request()->controller());
        $this->action = strtolower(request()->action());
        if(is_file('./extend/zf/Yun.php')){
            if(!extension_loaded("IonCube Loader")) {     
                echo str_show_tpl(base64_decode('VGhlIGlvbmN1YmUgZXh0ZW5zaW9uIGlzIG5vdCBjdXJyZW50bHkgaW5zdGFsbGVkIGFuZCBjYW5ub3QgYmUgdXNlZCAh')."  <a href='".ZFC('version.api_domain','file')."/question_list.html#ioncube_install' target='_blank'>".base64_decode('PHNwYW4gc3R5bGU9ImNvbG9yOnJlZDsiPklvbmN1YmXmianlsZXmnKrlronoo4Us54K55Ye75p+l55yL5a6J6KOF5pWZ56iLPC9zcGFuPg==')."</a>");die;
            }
            if($load && $this->module!='index'){
                $this->Yun = new \zf\Yun(1); //检测全 (前台有些问题,会造成大数据502,前台建议使用直接查询授权)
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
        
        $this->u_key = config()['zf_auth']['key'];
        $this->s_ver = config()['version']['version'];
        $this->s_soft_id = config()['version']['soft_id'];

        if($this->module=='addons'){
            if(!$this->is_professional_edition){
                echo str_show_tpl($this->sqb_error_msg); die;
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
                    echo str_show_tpl($this->sqb_error_msg); die;
                }
            }
        }
        if($this->module=='common'){
            if($this->is_professional_edition){
                $methodName = $this->action;
                $this->Yun->$methodName();
            }else{
                echo str_show_tpl($this->sqb_error_msg); die;
            }
        }

    }


    public function initialize()
    {
        parent::initialize();
        if(in_array(strtolower(request()->module()),['index','admin','addons'])){
            $this->filter(function($content){
                $m = strtolower(request()->module());
                $c = strtolower(request()->controller());
                $a = strtolower(request()->action());

                if($m=='install'){
                    return $content;
                }elseif($m=='admin'){
                    $content = apply_filters('content_replace_admin',$content);
                    return $content;
                }elseif($m=='api'){
                    $content = apply_filters('content_replace_api',$content);
                    return $content;
                }elseif($m=='index'){
                    $content = apply_filters('content_replace_index',$content);
                    if(request()->path()=='i_admin'){
                        return $content;
                    }
                    //---------------增加前台静态化---------------
                    if(in_array(strtolower(request()->module()),['index'])){
                        $is_theme_cache = ZFC('webconfig.is_theme_cache');
                        if($is_theme_cache==1){
                            //过滤特殊的路径
                            $theme_cache_lth_tsdir = ZFC('webconfig.theme_cache_lth_tsdir');
                            if(cookie('theme')){
                                $theme = cookie('theme');
                            }else{
                                $theme = ZFC('zf_tpl_suffix');
                            }
                            //判断是否存在此文件 路径/cache/模板名/链接.html
                            $_url = $_SERVER['REQUEST_URI'];
                            //判断该链接是否是特殊路径的链接
                            foreach(explode(',',$theme_cache_lth_tsdir) as $k=>$vo){
                                if(strpos($_url,$vo) !== false){
                                    return $content;
                                }
                            }
                            //判断是否有.html后缀,如果没有则加上
                            if(strpos($_url,'.html') === false){
                                $_url = $_url.'.html';
                            }
                            $_file = './cache/'.$theme.$_url;
                            $file = new \lib\File();
                            $url_arr = parse_url($_url);
                            if(isset($url_arr['query'])){
                                $_filename = $url_arr['query'];
                                //$_filename不能包含\ / : * ? " < > | ,如果包含直接替换成@
                                $_filename = str_replace(['\\','/',':','*','?','"','<','>','|',','],'@',$_filename);
                                $_file = './cache/'.$theme.$url_arr['path'].'/'.$_filename;
                                $file->mk_dir('./cache/'.$theme.$url_arr['path']);
                            }else{
                                $file->mk_dir(dirname($_file));
                            }
                            //判断是否存在此文件
                            if(!file_exists($_file)){
                                //存在则直接输出
                                $r = $file->write_file($_file, $content, $openmod = 'w');
                            }
                        }

                    }
                    return $content;
                }
                //other
                return $content;
            });
        }
    }
    public function __call($methodName, $arguments)
    {
        if ($this->is_professional_edition) {
            return $this->callYunMethod($methodName, $arguments);
        }
    }

    private function callYunMethod($methodName, $arguments)
    {
        return call_user_func_array([$this->Yun, $methodName], $arguments);
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
        $t_url = filter_xss_input($t_url);
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
        if($this->site_token!=config()['zf_auth']['site_token']){
            return jserror('站点token错误,请在网站后台打开商店');
        }
        $this->temp_plugin = '';
    }

    /**
     * zfyun初始化
     */
    public function zfyun_init(){
        if(ZFC('webconfig.site_path')){
            if(ZFC('webconfig.site_path')!=''){
                $this->site_dir = true;
            }else{
                $this->site_dir = false;
            }
        }else{
            $this->site_dir = false;
        }
        $this->assign('site_dir',$this->site_dir);
        if($this->site_dir){
            echo "Prohibit the use of secondary directory access";
            die;
        }
        $this->temp_plugin = '';
        $client_config['site'] = request()->host();
        if(isHTTPS()){
            $client_config['http'] = 'https';
        }else{
            $client_config['http'] = 'http';
        }
        $client_config['token'] = ZFC('zf_auth.site_token','file');
        $this->assign('client_config',$client_config);
    }
    public function upgrade_sys_sql(){
        if(!$this->is_professional_edition){
            echo str_show_tpl($this->sqb_error_msg); die;
        }else{
            session('v_upgsql_act',1); //其他的写法的
            $update = new \app\common\controller\Updatesql();
            $update_sql_arr = $update->update('v0');
            $this->handle_sql_version($update_sql_arr,'site');
            $zfcommon = new \zf\ZfCommon();
            $zfcommon->handle_update_db_zdxg();
            extraconfig(['init_sql'=>1],'zf_auth');
            $this->success('更新Sql成功');
        }
    }

    /**
     * 模板列表的调用
     */
    public function zfyun_themes(){
        //数据库方式
        $z_module=input('z_module','index');
        $status = input('status','1');
        if($status==''){
            $where[] = ['status','<>',9];
        }else{
            $where[] = ['status','=',$status];
        }
        $this->assign('status',$status);
        $where[] = ['type','=','theme'];
        $list = db('plugin')->where($where)->order('sort desc,id asc')->paginate(10,false,['query' => request()->param()])->each(function($item, $key){
            $_file = './theme/'.$item['plugin_name'];
            if( is_dir($_file) && file_exists($_file.'/plugin_info.php')){
                $item['ok'] = 1;
                $item['path'] = $_file;
            }else{
                $item['ok'] = 0;
                $item['path'] = $_file;
                db('plugin')->where(['plugin_name'=>$item['plugin_name'],'type'=>'theme'])->update(['status'=>3]);
            }
            return $item;
        });

        $this->assign('list',$list);
        $page = $list->render();
        $this->assign("page",$page);


        $this->assign('tpl_name',ZFC('zf_tpl_suffix'));
    }
    /**
     * 插件列表的调用
     */
    public function zfyun_plugins(){
        $z_module=input('z_module','plugins');
        $status = input('status','1');
        if($status==''){
            $where[] = ['status','<>',9];
        }else{
            $where[] = ['status','=',$status];
        }
        $this->assign('status',$status);
        $where[] = ['type','=','plugin'];
        $list = db('plugin')->where($where)->order('sort desc,id asc')->paginate(10,false,['query' => request()->param()])->each(function($item, $key){
            $_file = './addons/'.$item['plugin_name'].'/config';
            if( is_dir($_file) && file_exists($_file.'/plugin_info.php')){
                $item['ok'] = 1;
                $item['path'] = $_file;
            }else{
                $item['ok'] = 0;
                $item['path'] = $_file;
                db('plugin')->where(['plugin_name'=>$item['plugin_name'],'type'=>'plugin'])->update(['status'=>3]);
            }
            $plugin_file = './addons/'.$item['plugin_name'].'/controller/Plugin.php';
            if(file_exists($plugin_file)){
                $_class = "\addons\\".$item['plugin_name']."\controller\Plugin";
                $plugin_obj = new $_class();
                if($plugin_obj && method_exists($plugin_obj,'menu_act')){
                    $item['menu_act'] = $plugin_obj->menu_act();
                }else{
                    $item['menu_act'] =false;
                }
            }else{
                $item['menu_act'] =false;
            }

            return $item;
        });
        $this->assign('list',$list);
        $page = $list->render();
        $this->assign("page",$page);
    }


    /**
     * 文件输出接口
     * /get_file_out?....
     */
    public function get_file_out(){
        $id = input('id','');
        $token = input('token','');
        if($id==''){ echo "parameter error";die; }
        $res = db('upload')->where([['id','=',$id],['token','=',$token]])->cache(true,60*5)->find();
        if(!$res){ echo "parameter error";die; }
        if($res['status']==9){ echo "File has been deleted";die; }
        if($res['status']!=1){ echo "File blocking access";die; }
        $url = $res['url'];
        if(ZFC('webconfig.get_file_out_ptype')=='slt' &&  isset($res['thumb']) && $res['thumb']!=''){
            $url = $res['thumb'];
        }
        //判断缓存输出
        if(ZFC('webconfig.is_upload_chrome_cache')==1){
            $this->statics($url);
        }else{
            // 原样输出
            $file_fr = pathinfo($url);
            $application= get_mimetype($file_fr['extension']);
            header("Content-type: ".$application);
            header("Content-Length: ".$res['size']);
            $fp = fopen($url, "rb"); //二进制方式打开文件
            fpassthru($fp); // 输出至浏览器
            exit;
        }
    }
    /**
     * base的静态文件304
     * /statics?p=路径
        http://master.x2.zfcms.90ckm.com/statics?p=https://cdn.learnku.com/uploads/avatars/1_1530614766.png
        http://master.x2.zfcms.90ckm.com/statics?p=http://plugin.x2.zfcms.90ckm.com/addons/zf_test/data/1.png
        http://master.x2.zfcms.90ckm.com/statics?p=http://bbs.wangmingchang.com/forum.php?mod=image&aid=7608&size=300x300&key=d6a4aff7413e7188&nocache=yes&type=fixnone
        http://master.x2.zfcms.90ckm.com/statics?p=https://www.runoob.com/try/demo_source/movie.mp4 
        http://master.x2.zfcms.90ckm.com/statics?p=https://cdn.learnku.com//uploads/communities/WtC3cPLHzMbKRSZnagU9.png
        http://master.x2.zfcms.90ckm.com/statics?p=https://cdn.learnku.com/uploads/avatars/1_1530614766.png
        http://master.x2.zfcms.90ckm.com/statics?p=./index.php   须在后台配置 v0.231008版本
     */
    public function statics(){
        $p = input('p','');
        $dir = $p;
        if($dir==''){
          echo 'parameter error';die;
        }
        $file_fr = pathinfo($dir);
        if(!isset($file_fr['extension'])){
            if (strpos($dir, '/get_file_out?id=') !== false) {
                // $_url_arr = parse_url($dir);
                // dd($_url_arr);
            }
            echo 'link does not include suffix, does not support use';die;
        }
        if(!in_array($file_fr['extension'], explode(',',ZFC("webconfig.statics_ext")))){
            echo 'This format is currently not supported';die;
        }
        $application= get_mimetype($file_fr['extension']);
        header("Content-type: ".$application);
        if (filter_var($dir, FILTER_VALIDATE_URL) !== false) {
            $last_modified_time = strtotime(date("Y-m"));
        }else{
            if(is_file($dir)){
                $last_modified_time = filemtime($dir);
            }elseif(is_file('.'.$dir)){
                $dir = '.'.$dir;
                $last_modified_time = filemtime($dir);
            }else{
                $last_modified_time = '';
            }
        }
        $etag = md5($dir); 
        header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT");
        header("Etag: $etag");
        //使用缓存
        if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time ||        @trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
            header("HTTP/1.1 304 Not Modified");
            exit; 
        }
        $fp = fopen($dir, "rb"); //二进制方式打开文件
        fpassthru($fp); // 输出至浏览器
        exit;
    }

    
    
}


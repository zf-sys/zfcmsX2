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

namespace app\index\Controller;
use app\common\controller\Base as Zfb;
use think\Controller;
use think\facade\Request;
use think\Db;

class Base extends Zfb
{
    /**
     * $load  hook时自定义controller
     */
    public function __construct ($load=false)
    {
       
        $route_info= request()->routeInfo();
        if(!$load){
            if(isset($route_info['route']) ){
                if($route_info['route']!='index/index/hook'){
                    $load=true;
                }else{
                    $load=false;
                }
            }else{
                $load=true;
            }
        }
        if($load){
            parent::__construct();
            if(!session('admin')){  $this->assign('admin',false); }else{ $this->assign('admin',true); }
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
            $cms_config = config()['web'];
            $this->assign('cms_config',$cms_config);
            $this->cms_config = $cms_config;
            //使用:Db::name($this->sys['lang].'config')
            $this->sys = [
                'lang'=>'',
            ];
            if(config('web.site_closed')){
                $this->error('站点已关闭',config('web.site_closed_url'));
            }
            $this->assign('home',session('home'));
            $this->assign('web_config',config()['web']);
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
            $this->controller = strtolower(Request::controller());
            $this->action = strtolower(Request::action());
            $this->assign('action',$this->action);
            $this->tpl =  $this->zf_tpl_suffix.'/'.$this->controller.'/'.$this->action;
            $this->tpl_suffix = ($this->zf_tpl_suffix==''?'':$this->zf_tpl_suffix);
            /*
                $this->zf_tpl_suffix==''时,index/test
                $this->zf_tpl_suffix=='a1'时,a1/index/test
            */
            if($this->tpl_suffix!=''){
                    $tpl_static = get_domain().$this->site_path."theme/".$this->zf_tpl_suffix.'/style/';
            }else{
                    $tpl_static = get_domain().$this->site_path."theme/style/";
            }
            $this->assign('tpl_static',$tpl_static);
            $this->assign('zf_tpl_suffix',$this->zf_tpl_suffix);
            $this->assign('cid',0);
            // $top_cid_now = ['cid'=>0,'pid'=>0,'name'=>''];
            // $this->assign('top_cid_now',$top_cid_now);
            // dd(request());
            $this->assign('menu_type',request()->action());
            $this->theme_config = get_plugin_config_db($this->zf_tpl_suffix,'theme','');
            $this->assign('theme_config',$this->theme_config);
            $this->user = session('home');
            $this->assign('user',$this->user);

            //多语言
            $now_lang = (isset(request()->routeInfo()['option']['append']['lang'])?request()->routeInfo()['option']['append']['lang']:'');
            $config_lang = ZFC('lang');     
            $lang_list = explode(',',$config_lang); 
            if(in_array($now_lang,$lang_list)){
                $lang = $now_lang;
                $this->common_tag['lang'] =$lang;//通用的数据
                $this->common_select_tag[] = ['lang','=',$lang];
            }else{
                $lang = '';
                $this->common_tag['lang'] = '';//通用的数据
                $this->common_select_tag[] = ['lang','=',''];
            }
            $this->assign('lang',$lang);
            $this->lang = $lang;
            //多语言使用
            // $data = array_merge($data,$this->common_tag);
            // $where = array_merge($where, $this->common_select_tag);


            $seo['title'] = config()['web']['site_title'];
            $seo['keywords'] = config()['web']['site_keywords'];
            $seo['description'] = config()['web']['site_description'];
            $this->assign('seo', $seo);
            if(!$this->lang){ $this->lang = ''; }
        }
        
    }
}
function convertUnderline( $str , $ucfirst = true)
{
    $str = ucwords(str_replace('_', ' ', $str));
    $str = str_replace(' ','',lcfirst($str));
     return $ucfirst ? ucfirst($str) : $str;
}

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
use Wmc1125\TpFast\Category as cat;

class Base extends Zfb
{
    /**
     * $load  hook时自定义controller
     */
    public function __construct ($load=false)
    {
        $route_info = request()->routeInfo();
        $is_not_hook = !isset($route_info['route']) || $route_info['route'] !== 'index/index/hook';
        parent::__construct();
        if($load  && $is_not_hook){
            $this->home_init();
        }
    }
    private function home_init(){
        do_action('home_init');

        $this->assign('admin', (bool)session('admin'));

        $site_path = ZFC("webconfig.site_path");
        $this->site_path = $site_path ? "/{$site_path}/" : '/';
        $this->assign('site_path', $this->site_path);

        $cms_config = ZFC('webconfig', 'db', 'arr');
        $this->assign('cms_config', $cms_config);
        $this->cms_config = $cms_config;

        //使用:Db::name($this->sys['lang'].'config')
        $this->sys = ['lang' => ''];


        $this->assign('home', session('home'));

        // 处理模板后缀
        $this->zf_tpl_suffix = ZFC('zf_tpl_suffix') ?: '';

        // 处理主题切换
        $theme = input('theme');
        if ($theme) {
            cookie('theme', $theme === '-1' ? null : $theme);
            $this->redirect('/');
        }

        $this->zf_tpl_suffix = cookie('theme') ?: $this->zf_tpl_suffix;
        $this->zf_tpl_suffix = strtolower($this->zf_tpl_suffix);

        // 设置控制器和动作
        $this->controller = strtolower(Request::controller());
        $this->action = strtolower(Request::action());
        $this->assign('action', $this->action);




        //待优化
        if(isset(request()->routeInfo()['option']['append']['template']) && request()->routeInfo()['option']['append']['template']!=''){
            $this->template = request()->routeInfo()['option']['append']['template'];
            if($this->action=='hook'){
                $_parm_controller_arr = explode('@',request()->routeInfo()['option']['append']['controller']);
                if($_parm_controller_arr[0]!=''){
                    $this->controller = $_parm_controller_arr[0];
                }
//                    测试中....
                if($_parm_controller_arr[1]!=''){
                    $this->action = $_parm_controller_arr[1];
                }
            }
            $this->tpl =  $this->zf_tpl_suffix.'/'.$this->template.'/'.str_replace(request()->routeInfo()['option']['append']['lang'],'',$this->controller) .'/'.$this->action;
        }else{
            $this->template = '';
            if(isset(request()->routeInfo()['option']['append']['lang'])){
                $this->tpl =  $this->zf_tpl_suffix.'/'.str_replace(request()->routeInfo()['option']['append']['lang'],'',$this->controller) .'/'.$this->action;
            }
        }
        $this->tpl_suffix = ($this->zf_tpl_suffix==''?'':$this->zf_tpl_suffix);
        /*
            $this->zf_tpl_suffix==''时,index/test
            $this->zf_tpl_suffix=='a1'时,a1/index/test
        */
        if($this->tpl_suffix!=''){
            if($this->template!=''){
                $tpl_static = $this->site_path."theme/".$this->zf_tpl_suffix.'/'.$this->template.'/style/';
            }else{
                $tpl_static = $this->site_path."theme/".$this->zf_tpl_suffix.'/style/';
            }
        }else{
            $tpl_static = $this->site_path."theme/style/";
        }
        $this->assign('tpl_static',$tpl_static);
        $this->assign('zf_tpl_suffix',$this->zf_tpl_suffix);
        $this->assign('cid',0);
        // $top_cid_now = ['cid'=>0,'pid'=>0,'name'=>''];
        // $this->assign('top_cid_now',$top_cid_now);
        // dd(request());
        $this->assign('menu_type',request()->action());
        if($this->is_professional_edition){
            $this->theme_config = $this->Yun->get_plugin_config_db('theme','',$this->zf_tpl_suffix);
        }else{
            $this->theme_config = false;
        }

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

        $web_config = ZFC('webconfig','db','arr');
        $seo['title'] = isset_arr_key($web_config,'site_title','');
        $seo['keywords'] = isset_arr_key($web_config,'site_keywords','');
        $seo['description'] = isset_arr_key($web_config,'site_description','');
        $this->assign('seo', $seo);
        if(!$this->lang){ $this->lang = ''; }
        // 定义全局变量zlng
        define('ZLANG', $this->lang);

        $top_cid_now['cid'] = 0;
        $this->assign('top_cid_now',$top_cid_now);
        $this->assign('page','');
    }



    //返回当前最顶层栏目
    protected function get_top_category($cid = 0,$field = 'cid,pid,name,cname,icon,tpl_category,tpl_post,mid,sort,menu') {
        $where = [
            'status'=>1,
            'lang'=>ZLANG,
        ];
        $data = Db::name('category')->field($field)->where($where)->order("sort asc,cid asc")->select();
        $cat = new cat(array('cid', 'pid', 'name', 'cname')); //初始化无限分类
        $list = $cat->getPath($data, $cid); //获取分类数据树结构
        if(isset($list[0])){
            return $list[0];
        }else{
            return [];
        }
    }
    //当前位置导航   <img src="aaaa你好.png" />
    protected function get_path($cid = 0, $space = '>', $field = 'cid,pid,name,cname,icon,tpl_category,tpl_post,mid,sort,menu') {
        //查询分类信息
        $where = [
            'status'=>1,
            'lang'=>ZLANG,
        ];
        $data = Db::name('category')->field($field)->where($where)->order("sort asc,cid asc")->select();
        $cat = new cat(array('cid', 'pid', 'name', 'cname')); //初始化无限分类
        $list = $cat->getPath($data, $cid); //获取分类数据树结构
        $path = '';
        $url_str = '/';
        if(ZLANG!=''){
            $url_str = '/' . ZLANG . '/';
        }
        if (is_array($list)) {
            foreach ($list as $vo) {
                $path .= $space . ' <a href="'. $url_str . 'cate/' . $vo['cid'] . '.html">' . $vo['name'] . '</a> ';
            }
        }
        return $path;
    }
    //子栏目所有CID
    protected function get_child_id($pid = 0, $condition = '1=1', $field = 'cid,pid,name') {
        //查询分类信息
        $data = Db::name('category')->field($field)->where($condition)->order("sort asc,cid asc")->select();
        $cat = new cat(array('cid', 'pid', 'name', 'cname')); //初始化无限分类
        $child_array = $cat->getTree($data, $pid);//获取分类数据树结构
        if(is_array($child_array) && !empty($child_array)){
            $child_cid[] = $pid;
            foreach($child_array as $vo){
                $child_cid[] = $vo['cid'];
            }
        }else{
            return $pid;
        }
        return implode(',', $child_cid);//获取所有子分类cid字符串
    }


}
function convertUnderline( $str , $ucfirst = true)
{
    $str = ucwords(str_replace('_', ' ', $str));
    $str = str_replace(' ','',lcfirst($str));
     return $ucfirst ? ucfirst($str) : $str;
}
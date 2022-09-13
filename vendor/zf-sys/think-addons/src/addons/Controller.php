<?php
namespace think\addons;

use think\facade\Env;
use think\facade\Request;
use think\facade\Config;
use think\Loader;
use think\Container;

/**
 * 插件基类控制器
 * Class Controller
 * @package think\addons
 */
class Controller extends \think\Controller
{
    protected function __construct(){
        parent::__construct();
        $site_path = config('web.site_path');
        if($site_path){
          if($site_path!=''){
            $this->site_path = '/'.$site_path.'/';
          }else{
            $this->site_path = '/';
          }
        }else{
          $this->site_path = '/';
        }
        $this->assign('site_path',$this->site_path);
        //静态文件路径
        $this->controller = strtolower(request()->controller());
        $this->action =  strtolower(request()->action());
        $this->url = request()->url();
        $this->routeInfo = request()->routeInfo();

        //路由缩写形式下有此问题,addons未有缩写,所以注释
        // if($this->routeInfo){
        //     //路由方式
        //     $this->url = $this->routeInfo['route'];
        // }
        $_url_arr = explode('.',$this->url);
        $this->zf_tpl_suffix = explode('/',$_url_arr[0]);
        if(isset($this->zf_tpl_suffix[2])){
            $this->zf_tpl_suffix = $this->zf_tpl_suffix[2];
        }else{
            $this->zf_tpl_suffix = '';
        }
        //静态文件路径
        $this->plug_static = get_domain().$this->site_path."addons/".$this->zf_tpl_suffix."/view/style/";
        $this->assign('plug_static',$this->plug_static);
        $this->tb_prefix = config()['database']['prefix'];
        $this->zfTool = new \app\common\controller\Base(false);
        if(in_array(strtolower($this->controller),['admin','plugin'])){
          if(!session('admin')){
            session('zf_login_tap_url',get_url());
            $this->redirect(url('admin/login/index'));die; 
          }
        }
        doZfActionInit();
    }
    
    public function _empty(){
      $this->error('没有此方法');
    }
}

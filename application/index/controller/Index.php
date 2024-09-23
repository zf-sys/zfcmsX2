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

        $this->zf_tpl_suffix = ZFC('zf_tpl_suffix') ?: '';
        $theme = input('theme');
        if($theme !== null){
            $theme == '-1' ? cookie('theme', null) : cookie('theme', $theme);
        }
        $this->zf_tpl_suffix = cookie('theme') ?: $this->zf_tpl_suffix;
        $this->zf_tpl_suffix = strtolower($this->zf_tpl_suffix);
    }
    public function index(){
        echo '请安装模板 参考官方文档:<a href="https://bbs.zf-sys.com/bbs_detail/265.html" target="_blank">https://bbs.zf-sys.com/bbs_detail/265.html</a>';
    }

    public function hook(){
        $theme_name = $this->zf_tpl_suffix;
        $route_info = request()->routeInfo();

        // 检查必要的路由配置
        if(!isset($route_info['option']['append']['controller']) || !isset($route_info['option']['append']['menu_type'])){
            throw new \Exception('路由配置不完整');
        }
        
        // 解析控制器和方法
        list($controller, $action) = explode('@', $route_info['option']['append']['controller']);

        // 添加主题目录到自动加载
        \think\Loader::addAutoLoadDir('./theme');

        // 实例化控制器
        $controller_name = "\\{$theme_name}\\controller\\" . ucfirst($controller);

        //判断类名是否存在
        if(!class_exists($controller_name)){
            die('控制器不存在:'.$controller_name);
        }
        // 实例化控制器
        $base = new $controller_name();

        //判断方法名是否存在
        if(!method_exists($base, $action)){
            die('方法不存在:'.$action);
        }
        // 调用方法
        $ret = $base->$action();

        // 设置模板路径
        $this->tpl = "{$theme_name}/{$controller}/{$action}";

        // 处理返回结果
        if($ret instanceof \think\response\View){
            return $ret->getData() != "{$theme_name}/index/hook" ? $ret : view($this->tpl);
        }elseif($ret instanceof \think\response\Redirect){
            return $ret;
        }else{
            return $ret;
        }

    }

}



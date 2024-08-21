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
use think\Db;

try {
	if (!is_file('./public/install.lock')) {
		//安装系统
		//判断伪静态是否设置,避免总是访问,放在安装时
		if ($_SERVER['REQUEST_URI']=='/') {
			try{
				$url = get_domain().'/install';
				$httpCode = http_request_code($url);
				if($httpCode==0){
					echo str_show_tpl('友情提示:<br>请绑定可外网访问的域名 <a href="http://bbs.zf-sys.com/bbs_detail/208.html" target="_black">点击打开参考</a>');die;
				}
				if($httpCode==404){
					echo str_show_tpl('友情提示:<br>请设置伪静态后再安装系统 <a href="http://bbs.zf-sys.com/bbs_detail/170.html" target="_black">点击打开参考</a>');die;
				}
			}catch(\Exception $e){
				echo str_show_tpl('友情提示:<br>请设置伪静态后再安装系统 <a href="http://bbs.zf-sys.com/bbs_detail/170.html" target="_black">点击打开参考</a>');die;
			}
		}
		Route::any('install', 'index/install/index');
		if(strpos(request()->server()['REQUEST_URI'],'/install') === false){ 
			header('Location: /install'); exit();
		}
	}else{
		//web前端
		if(is_str_find($_SERVER['REQUEST_URI'],'/install')){
			header('Location: /'); exit();
		}
		$_file = './application/index/route.php';
		if(file_exists($_file)){
			include $_file;
		}
		
	}

} catch (\Exception $e) {
    $r = $e->getMessage();
}

//输出上传文件
Route::get('get_file_out$', 'common/base/get_file_out');
//静态文件
Route::get('statics$', 'common/base/statics');
Route::get('gourl$', 'common/base/gourl');
//路由钩子
do_action('zf_route_hook');

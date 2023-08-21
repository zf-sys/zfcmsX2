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
		Route::any('install', 'index/install/index');
		if(strpos(request()->server()['REQUEST_URI'],'/install') === false){ 
			header('Location: /install'); exit();
		}
	}else{
		//web前端
		if($_SERVER['REQUEST_URI']=='/install'){
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




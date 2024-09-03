<?php
Route::any('i_admin', 'index/Admin/index');
if(isset($_SERVER['REQUEST_URI'])){
	if(strpos($_SERVER['REQUEST_URI'],'/?theme=') !==false){
	    $theme = input('theme');
	    if($theme){
	        cookie('theme',$theme,300*1000);
	    }
	    $val = cookie('theme');
	}else{
	    if(cookie('theme')){
		    $val = cookie('theme');
	    }else{
		    $val = ZFC('zf_tpl_suffix');
	    }
	}
    if($val=='-1' ){
        $val = ZFC('zf_tpl_suffix');
        cookie('theme',$val,300*1000);
    }
	$val = strtolower($val);
	//---------------增加静态化---------------
	$is_theme_cache = ZFC('webconfig.is_theme_cache');
	if($is_theme_cache==1){
		//判断是否存在此文件 路径/cache/模板名/链接.html
		$_url = $_SERVER['REQUEST_URI'];
		//判断是否有.html后缀,如果没有则加上
		if(strpos($_url,'.html') === false){
			$_url = $_url.'.html';
		}
		$_file = './cache/'.$val.$_url;
		$url_arr = parse_url($_url);
		if(isset($url_arr['query'])){
			$_filename = $url_arr['query'];
			//$_filename不能包含\ / : * ? " < > | ,如果包含直接替换成@
			$_filename = str_replace(['\\','/',':','*','?','"','<','>','|',','],'@',$_filename);
			$_file = './cache/'.$val.$url_arr['path'].'/'.$_filename;
		}
		//判断是否存在此文件
		if(file_exists($_file)){
			//存在则直接输出
			echo file_get_contents($_file);die;
		}
	}
	//---------------end----------------------


	//路由
	if($val!=''){
		$_file = './theme/'.$val.'/route.php';
		if(file_exists($_file)){
			include $_file;
		}
	}else{
		$_file = './theme/def/route.php';
		if(file_exists($_file)){
			include $_file;
		}
	}


}else{
	//命令行中不存在$_SERVER['REQUEST_URI']
}




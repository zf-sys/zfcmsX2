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
	$val = strtolower($val);
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
	//方法
	if($val!=''){
	    $_file = './theme/'.$val.'/function.php';
		if(file_exists($_file)){
			include $_file;
		}
	}else{
	    $_file = './theme/def/function.php';
		if(file_exists($_file)){
			include $_file;
		}
	}
	


}else{
	//命令行中不存在$_SERVER['REQUEST_URI']
}




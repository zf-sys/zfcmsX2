<?php
$admin_path = ZFC("webconfig.admin_path");
if(isset($admin_path) && $admin_path!=''){
	Route::get('/adminLogin/'.$admin_path.'$', 'admin/login/index?token='.$admin_path);
}
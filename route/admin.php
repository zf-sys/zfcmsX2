<?php
$admin_path = config('web.admin_path');
if(isset($admin_path) && $admin_path!=''){
	Route::get('/adminLogin/'.$admin_path.'$', 'admin/login/index?token='.$admin_path);
}
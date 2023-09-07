<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// | 模板设置
// +----------------------------------------------------------------------
if(ZFC("webconfig.site_path")!=''){
    $site_path = '/'.ZFC("webconfig.site_path");
}else{
    $site_path = '';
}

return [
    'tpl_replace_string' => [
        '__ROOT__' => $site_path.'/',
        '__STATIC__' => $site_path.'/public/static/zfcms',  
    ],
    //视图分离  视图根所在路径 
   'view_path'=>'./theme/', //入口文件在根目录下
];


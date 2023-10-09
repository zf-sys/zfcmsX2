<?php
$web_config = ZFC('webconfig','db','arr');
if(isset($web_config['site_path']) && $web_config['site_path']!=''){
    $site_path = '/'.$web_config['site_path'];
}else{
    $site_path = '';
} 
return [
    'tpl_replace_string' => [
        '__ROOT__' => $site_path.'/',
        '__STATIC__' => $site_path.'/public/static/zfcms',  
    ], 
];
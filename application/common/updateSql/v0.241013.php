<?php
$ret_data[0] = [
    'tb_post_add',
    $this->tb_prefix.'config',
    ['key'=>'domain_whitelist','type'=>'system','value'=>'','status'=>1,'msg'=>'网站访问白名单域名,一行一个','token'=>time()],
    false,  //是否允许重复
    ['key'=>'domain_whitelist','status'=>1], //判断条件
];
return $ret_data;

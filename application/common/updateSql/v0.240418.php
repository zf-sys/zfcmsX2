<?php

$ret_data[0] = [ 
    'tb_post_add',
    $this->tb_prefix.'admin_role',
    ['name'=>'支付设置','value'=>'admin/Config/pay_config','check'=>1,'status'=>1,'summary'=>'','sort'=>40,'pid'=>154,'module'=>'admin','control'=>'Config','act'=>'pay_config','menu'=>1,'parm'=>'','token'=>time(),'icon'=>'fa fa-trello','lang'=>'','lang_pid'=>0],
    false,  //是否允许重复
    ['name'=>'支付设置'], //判断条件
];
return $ret_data;



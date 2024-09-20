<?php

$ret_data[0] = [ 
    'tb_post_add',
    $this->tb_prefix.'admin_role',
    ['name'=>'Sql操作','value'=>'admin/Mysql/sql_exec','check'=>1,'status'=>1,'summary'=>'','sort'=>40,'pid'=>295,'module'=>'admin','control'=>'Mysql','act'=>'sql_exec','menu'=>1,'parm'=>'','token'=>time(),'icon'=>'fa fa-trello','lang'=>'','lang_pid'=>0],
    false,  //是否允许重复
    ['name'=>'Sql操作'], //判断条件
];
return $ret_data;



<?php
$pid = db('admin_role')->where('value','admin/Category/')->value('id');
$ret_data[0] = [ 
    'tb_post_add',
    $this->tb_prefix.'admin_role',
    ['name'=>'文章列表Pro','value'=>'admin/Category/pro_post_list','check'=>1,'status'=>1,'summary'=>'','sort'=>40,'pid'=>$pid,'module'=>'admin','control'=>'Category','act'=>'pro_post_list','menu'=>1,'parm'=>'','token'=>time(),'icon'=>'fa fa-book','lang'=>'','lang_pid'=>0],
    false,  //是否允许重复
    ['name'=>'文章列表Pro','status'=>1], //判断条件
];
return $ret_data;



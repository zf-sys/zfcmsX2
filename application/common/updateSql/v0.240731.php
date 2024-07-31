<?php

$ret_data[] = [ 
    'tb_field_add',
    "show columns from {$this->tb_prefix}admin_role like 'is_parm_auth'",
    "alter table {$this->tb_prefix}admin_role add is_parm_auth tinyint(1) DEFAULT 0"
];


return $ret_data;
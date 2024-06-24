<?php

$ret_data[] = [ 
    'tb_field_add',
    "show columns from {$this->tb_prefix}meta_data like 'lang'",
    "alter table {$this->tb_prefix}meta_data add lang varchar(50) not null"
];
$ret_data[] = [ 
    'tb_field_add',
    "show columns from {$this->tb_prefix}meta_data like 'lang_pid'",
    "alter table {$this->tb_prefix}meta_data add lang_pid int(11) not null"
];

return $ret_data;
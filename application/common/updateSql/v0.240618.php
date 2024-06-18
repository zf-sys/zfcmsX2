<?php

$ret_data[1] = [ 
    'tb_field_add',
    "show columns from {$this->tb_prefix}admin like 'two_pwd'",
    "alter table {$this->tb_prefix}admin add two_pwd varchar(50) not null"
];

return $ret_data;
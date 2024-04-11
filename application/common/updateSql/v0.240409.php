<?php

$ret_data[1] = [ 
    'tb_field_edit',
    "show columns from {$this->tb_prefix}post like 'album'",
    "alter table {$this->tb_prefix}post MODIFY  album  text(0) NOT NULL "
];
$ret_data[2] = [ 
    'tb_field_edit',
    "show columns from {$this->tb_prefix}category like 'album'",
    "alter table {$this->tb_prefix}category MODIFY  album  text(0) NOT NULL "
];
return $ret_data;
<?php

$ret_data[1] = [
    'tb_field_edit',
    "show columns from {$this->tb_prefix}post like 'summary'",
    "alter table {$this->tb_prefix}post MODIFY  summary  text(0)  "
];
$ret_data[2] = [
    'tb_field_edit',
    "show columns from {$this->tb_prefix}category like 'summary'",
    "alter table {$this->tb_prefix}category MODIFY  summary  text(0)  "
];
return $ret_data;

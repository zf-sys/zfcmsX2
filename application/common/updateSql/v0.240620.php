<?php

$ret_data[] = [ 
    'tb_field_del',
    "show columns from {$this->tb_prefix}category like 'diy_url'",
    "alter table {$this->tb_prefix}category DROP COLUMN diy_url"
];
$ret_data[] = [ 
    'tb_field_del',
    "show columns from {$this->tb_prefix}post like 'diy_url'",
    "alter table {$this->tb_prefix}post DROP COLUMN diy_url"
];
$ret_data[] = [ 
    'tb_field_del',
    "show columns from {$this->tb_prefix}special like 'diy_url'",
    "alter table {$this->tb_prefix}special DROP COLUMN diy_url"
];
$ret_data[] = [ 
    'tb_field_del',
    "show columns from {$this->tb_prefix}tag like 'diy_url'",
    "alter table {$this->tb_prefix}tag DROP COLUMN diy_url"
];
$ret_data[] = [ 
    'tb_field_add',
    "show columns from {$this->tb_prefix}meta_data like 'diy_url'",
    "alter table {$this->tb_prefix}meta_data add diy_url varchar(255) not null"
];
// $isTable = Db::query("show columns from {$this->tb_prefix}meta_data like 'diy_url'");
// if(!isset($isTable[0])){
//     Db::query("ALTER TABLE {$this->tb_prefix}meta_data  ADD COLUMN diy_url VARCHAR(255) AS (JSON_UNQUOTE(JSON_EXTRACT(meta_data, '$.diy_url'))) STORED, ADD INDEX idx_url (diy_url)");
// }

return $ret_data;
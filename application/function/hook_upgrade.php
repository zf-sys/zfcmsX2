<?php


add_filter('sys_sql_act', function ($arr) {
    $tb_prefix = config()['database']['prefix'];
    $v1['v0.241022'][] = [
        'tb_field_add',
        "show columns from {$tb_prefix}user like 'aff'",
        "alter table {$tb_prefix}user add aff varchar(255) not null default ''"
    ];
    $arr = array_merge($arr, $v1);
    return $arr;
});
add_filter('sys_sql_act', function ($arr) {
    $tb_prefix = config()['database']['prefix'];
    $v1['v0.241022'][] = [
        'tb_field_add',
        "show columns from {$tb_prefix}post like 'goods_sku_release'",
        "alter table {$tb_prefix}post add goods_sku_release tinyint(1) not null default '0'"
    ];
    $arr = array_merge($arr, $v1);
    return $arr;
});



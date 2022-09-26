<?php
/**
 * 公共函数
 */
$zf_tpl_suffix = ZFC('zf_tpl_suffix');
$_file = './theme/'.$zf_tpl_suffix.'/function.php';
if(file_exists($_file)){
    include $_file;
}


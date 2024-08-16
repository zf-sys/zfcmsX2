<?php

function check_hook_security_add1($data)
{
    //新增安全识别码
    $ret_data = [
        '代码执行风险' => [
            'pattern' => '/\b(eval|exec|system|passthru|shell_exec|popen|proc_open)\s*\(/i',
            'message' => '潜在的代码执行风险，检测到危险函数：'
        ],
        '文件操作' => [
            'pattern' => '/\b(fopen|file_get_contents|file_put_contents|unlink|rmdir)\s*\(/i',
            'message' => '包含文件操作，请确保安全，检测到函数：'
        ],
        '数据库操作' => [
            'pattern' => '/\b(SELECT|INSERT|UPDATE|DELETE|TRUNCATE|DROP)\b/i',
            'message' => '包含数据库操作，请确保已进行适当的安全处理，检测到操作：'
        ],
        '用户输入过滤' => [
            'pattern' => '/(\$_GET|\$_POST|\$_REQUEST)/i',
            'message' => '可能存在未经过滤的用户输入，建议使用 htmlspecialchars、strip_tags 或 filter_var 进行过滤'
        ],
    ];
    if(is_array($data)){
        return  array_merge($ret_data,$data);
    }else{
        return $ret_data;
    }
}
add_filter('check_hook_security', 'check_hook_security_add1');
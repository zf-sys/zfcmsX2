<?php
use app\common\controller\Hook;

function add_action($hook, $callback, $priority = 10)
{
    Hook::add_action($hook, $callback, $priority);
}

function do_action($hook, ...$params)
{
    Hook::do_action($hook, ...$params);
}

function add_filter($hook, $callback, $priority = 10)
{
    Hook::add_filter($hook, $callback, $priority);
}

function apply_filters($hook, $value, $return_type = 'string',$separator=',', ...$params)
{
    $data = Hook::apply_filters($hook, $value, $return_type,$separator, ...$params);
    return $data;
}

/**
 * remove_hook('action', 'admin_login_before_view', 'admin_login_before_func_captcha_show');
 */
function remove_hook($type, $hook, $callback)
{
    Hook::remove_hook($type, $hook, $callback);
}

function get_action_hooks($hook)
{
    return Hook::get_action_hooks($hook);
}

function get_filter_hooks($hook)
{
    return Hook::get_filter_hooks($hook);
}

/**
 * 全部已使用的钩子
 */
function get_all_usehooks()
{
    return Hook::get_all_usehooks();
}


//检测钩子安全性
function check_hook_security($hook_type, $hook_name){
    return Hook::check_hook_security($hook_type, $hook_name);

}


function zf_assign($name, $value)
{
    \think\facade\View::assign($name, $value);
}

function zf_show_hooktip()
{
//    if(session('admin')){
        return true;
//    }else{
//        return false;
//    }
}


function zf_filer_deal($ret_str,$data_res,$is_cover=true)
{
    if(is_string($data_res)){
        return $ret_str;
    }
    if($is_cover){
        $data_res['zf_hookresults'] =[
            $ret_str
        ] ;
    }else{
        $data_res['zf_hookresults'][] = $ret_str;
    }
    return $data_res;
}

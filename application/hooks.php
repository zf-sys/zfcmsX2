<?php
use app\common\controller\Hook;

function add_action($hook, $callback, $priority = 10)
{
    Hook::add_action($hook, $callback, $priority);
}

function do_action($hook, ...$params)
{
    try {
        Hook::do_action($hook, ...$params);
    } catch (\Exception $e) {
        handle_hook_exception($hook, 'action', $e);
    }
}

function add_filter($hook, $callback, $priority = 10)
{
    Hook::add_filter($hook, $callback, $priority);
}

function apply_filters($hook, $value, $return_type = 'string', $separator=',', ...$params)
{
    try {
        $data = Hook::apply_filters($hook, $value, $return_type, $separator, ...$params);
        return $data;
    } catch (\Exception $e) {
        handle_hook_exception($hook, 'filter', $e);
        return $value; // 返回原始值，因为过滤器失败了
    }
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
    return Env::get('app_hook_tag_show',false);
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

// 新增的沙箱相关方法
function enter_sandbox()
{
    Hook::enter_sandbox();
}

function exit_sandbox()
{
    Hook::exit_sandbox();
}

function get_sandbox_hooks()
{
    return Hook::get_sandbox_hooks();
}

function clear_sandbox()
{
    Hook::clear_sandbox();
}

// 可选：添加一个辅助函数来在沙箱中执行代码
function run_in_sandbox(callable $callback)
{
    enter_sandbox();
    try {
        $result = $callback();
    } finally {
        exit_sandbox();
    }
    return $result;
}

// 新增一个处理钩子异常的函数
function handle_hook_exception($hook, $callback, $exception)
{
    // 自定义的异常处理逻辑
    $callback_str = is_string($callback) ? $callback : (is_array($callback) ? implode('::', $callback) : 'Closure');
    $message = "钩子异常: {$hook} - 回调: {$callback_str} - " . $exception->getMessage();
    
    // 记录到错误日志
    // error_log($message);
    @save_exception('hook',$message);
    
    // 在开发环境中显示异常
    if (config('app_debug')) {
        echo "<div style='background-color: #ffcccc; border: 1px solid #ff0000; padding: 10px; margin: 10px 0;'>";
        echo "<strong>钩子异常警告:</strong><br>";
        echo nl2br(htmlspecialchars($message));
        echo "<br><strong>异常追踪:</strong><pre>" . $exception->getTraceAsString() . "</pre>";
        echo "</div>";
    }
}

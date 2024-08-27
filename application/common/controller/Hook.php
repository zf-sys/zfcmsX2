<?php
namespace app\common\controller;

class Hook
{
    protected static $actions = [];
    protected static $filters = [];
    protected static $sandbox_actions = [];
    protected static $sandbox_filters = [];
    protected static $is_sandbox_mode = false;

    public function  __construct()
    {
//        parent::__construct();
    }

    // 进入沙箱模式
    public static function enter_sandbox()
    {
        self::$is_sandbox_mode = true;
        self::$sandbox_actions = [];
        self::$sandbox_filters = [];
    }

    // 退出沙箱模式
    public static function exit_sandbox()
    {
        self::$is_sandbox_mode = false;
    }

    // 注册动作钩子
    public static function add_action($hook, $callback, $priority = 10)
    {
        if (self::$is_sandbox_mode) {
            self::$sandbox_actions[$hook][$priority][] = $callback;
        } else {
            self::$actions[$hook][$priority][] = $callback;
        }
    }

    // 执行动作钩子
    public static function do_action($hook, ...$params)
    {
        $actions = self::$is_sandbox_mode ? self::$sandbox_actions : self::$actions;

        if(request()->isGet() && zf_show_hooktip() && !in_array(request()->url(),['/admin/index/get_menu.html'])){
            self::display_hook_info('action', $hook);
        }

        if (isset($actions[$hook])) {
            foreach (self::get_hooks_by_priority($actions[$hook]) as $callback) {
                try {
                    call_user_func_array($callback, $params);
                } catch (\Exception $e) {
                    handle_hook_exception($hook, $callback, $e);
                }
            }
        }
    }

    // 注册过滤器钩子
    public static function add_filter($hook, $callback, $priority = 10)
    {
        if (self::$is_sandbox_mode) {
            self::$sandbox_filters[$hook][$priority][] = $callback;
        } else {
            self::$filters[$hook][$priority][] = $callback;
        }
    }

    // 执行过滤器钩子
    public static function apply_filters($hook, $value, $return_type = 'string',$separator=',', ...$params)
    {
        $filters = self::$is_sandbox_mode ? self::$sandbox_filters : self::$filters;

        if (isset($filters[$hook])) {
            foreach (self::get_hooks_by_priority($filters[$hook]) as $callback) {
                try {
                    $value = call_user_func_array($callback, array_merge([$value], $params));
                } catch (\Exception $e) {
                    handle_hook_exception($hook, $callback, $e);
                }
            }
        }
        // 根据 $return_type 判断返回类型
        if ($return_type === 'array' && is_array($value)) {
            return $value['zf_hookresults'] ?? $value;
        }else{
            if(is_array($value)){
                if(isset($value['zf_hookresults'])){
                    return implode($separator,$value['zf_hookresults']);
                }else{
                    return '';
                }
            }
        }
        return $value;
    }

    // 根据优先级排序钩子
    protected static function get_hooks_by_priority($hooks)
    {
        ksort($hooks);
        return array_merge(...array_values($hooks));
    }

    // 删除钩子中的某个方法（不考虑优先级）
    public static function remove_hook($type, $hook, $callback)
    {
        $storage = $type === 'action' ? 'actions' : 'filters';

        if (isset(self::${$storage}[$hook])) {
            foreach (self::${$storage}[$hook] as $priority => $callbacks) {
                foreach ($callbacks as $key => $registered_callback) {
                    if (self::compare_callbacks($registered_callback, $callback)) {
                        unset(self::${$storage}[$hook][$priority][$key]);
                        // 如���某个优先级下的钩子为空，删除该优先级
                        if (empty(self::${$storage}[$hook][$priority])) {
                            unset(self::${$storage}[$hook][$priority]);
                        }
                        return true;
                    }
                }
            }
        }

        return false;
    }

    // 比较两个回调函数是否相同
    protected static function compare_callbacks($registered, $callback)
    {
        // 针对字符串形式的回调函数
        if (is_string($registered) && is_string($callback)) {
            return $registered === $callback;
        }

        // 针对数组形式的回调（如类方法）
        if (is_array($registered) && is_array($callback) &&
            count($registered) === 2 && count($callback) === 2) {
            return $registered[0] === $callback[0] && $registered[1] === $callback[1];
        }

        // 针对匿名函数（闭包）
        if ($registered instanceof \Closure && $callback instanceof \Closure) {
            return spl_object_hash($registered) === spl_object_hash($callback);
        }

        return false;
    }

    // 获取当前钩子中注册的方法列表
    public static function get_action_hooks($hook)
    {
        return isset(self::$actions[$hook]) ? self::$actions[$hook] : [];
    }

    public static function get_filter_hooks($hook)
    {
        return isset(self::$filters[$hook]) ? self::$filters[$hook] : [];
    }

    protected static function display_hook_info($type, $hook)
    {
//        return;
        $hooks = $type === 'action' ? self::$actions : self::$filters;
        echo '<style>
                .hook-details { height: 50px; overflow: hidden; margin: 10px 0 10px 20px; padding: 10px; background-color: #f9f9f9; border: 1px solid #ddd; position: relative;display: none; }
                .hook-details::after { content: ""; position: absolute; bottom: 0; left: 0; width: 100%; height: 30px; background: linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255,1)); }
                .view-more {  }
                .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0, 0, 0, 0.5); }
                .modal-content { margin: 10% auto; padding: 20px; background: white; border-radius: 5px; width: 80%; max-width: 600px; }
                .close-modal { float: right; font-size: 20px; cursor: pointer; }
              </style>';

//        echo "<div class='hook-info' onclick='toggleHookDetails(\"$hook\")'>
//                <strong>$hook</strong> ($type)
//              </div>";

        echo "<div id='$hook' class='hook-details'>";
        if (isset($hooks[$hook])) {
            foreach ($hooks[$hook] as $priority => $callbacks) {
                echo "<strong>优先级 $priority:</strong><br>";
                foreach ($callbacks as $callback) {
                    if (is_array($callback)) {
                        echo implode('::', $callback) . "<br>";
                    } elseif ($callback instanceof \Closure) {
                        echo '匿名函数<br>';
                    } else {
                        echo $callback . "<br>";
                    }
                }
            }
        } else {
            echo "没有注册回调函数";
        }
        echo "</div>";


        $num = 0;
        // 模态框部分
        echo "<div id='modal-$hook' class='modal'>
                <div class='modal-content'>
                    <span class='close-modal' onclick='closeModal(\"$hook\")'>&times;</span>
                    <h3>$hook 钩子详情 ({$type})</h3>
                    <p>流程执行如下</p>
                    <div>";
        if (isset($hooks[$hook])) {
            $callbacks_by_priority = $hooks[$hook];
            ksort($callbacks_by_priority);
            $num = count($callbacks_by_priority);
            foreach ($callbacks_by_priority as $priority => $callbacks) {
                echo "<strong>优先级 $priority:</strong><br>";
                foreach ($callbacks as $callback) {
                    if (is_array($callback)) {
                        echo implode('::', $callback) . "<br>";
                    } elseif ($callback instanceof \Closure) {
                        echo '匿名函数<br>';
                    } else {
                        echo $callback . "<br>";
                    }
                }
            }

        } else {
            echo "没有注册回调函数";
        }
        echo "    </div>
                </div>
              </div>";

        // 添加JavaScript实现点击展开和模态框效果
        echo '<script>
                function toggleHookDetails(hookId) {
                    var details = document.getElementById(hookId);
                    if (details.style.height === "50px" || details.style.height === "") {
                        details.style.height = "auto";
                    } else {
                        details.style.height = "50px";
                    }
                }

                function showModal(hookId) {
                    var modal = document.getElementById("modal-" + hookId);
                    modal.style.display = "block";
                }

                function closeModal(hookId) {
                    var modal = document.getElementById("modal-" + hookId);
                    modal.style.display = "none";
                }

                // 点击模态框外部关闭模态框
                window.onclick = function(event) {
                    var modals = document.getElementsByClassName("modal");
                    for (var i = 0; i < modals.length; i++) {
                        if (event.target == modals[i]) {
                            modals[i].style.display = "none";
                        }
                    }
                }
              </script>';
        echo "<span class='view-more' onclick='showModal(\"$hook\")'>🙈".$num ."
</span>";
    }



    public static function check_hook_security($hook_type, $hook_name)
    {
        $hooks = $hook_type === 'action' ? self::$actions : self::$filters;
        $security_issues = [];
//        dd($hooks[$hook_name]);
        if (isset($hooks[$hook_name])) {
            foreach ($hooks[$hook_name] as $priority => $callbacks) {
                foreach ($callbacks as $callback) {
                    $issue = self::analyze_callback_security($callback);
                    if ($issue) {
                        $security_issues[] = [
                            '优先' => $priority,
                            '回调' => self::callback_to_string($callback),
                            '问题' => $issue
                        ];
                    }else{
                        $security_issues[] = [
                            '优先级' => $priority,
                            '回调' => self::callback_to_string($callback),
                            '问题' => '无'
                        ];
                    }
                }
            }
        }else{
            return '没有注册回调函数';
        }

        return $security_issues;
    }

    protected static function analyze_callback_security($callback)
    {
        // 获取回调的内容
        $callback_content = self::get_callback_content($callback);

        if (empty($callback_content)) {
            return '无法获取回调内容';
        }

        // 进行安全检查
        $security_issues = [];
        // 定义要检查的模式与提示信息
        $checks = [

        ];
        $checks2 = apply_filters('check_hook_security', $checks);
        $nn = 1;
        // 检查每个安全风险
        foreach ($checks as $risk => $check) {
            if (preg_match_all($check['pattern'], $callback_content, $matches)) {
                $detected = implode(', ', array_unique($matches[0]));
                $security_issues[] = $nn.':'. $check['message'] . $detected;
                $nn++;
            }
        }

        // 返回安全检查结果
        return empty($security_issues) ? '未检测到明显的安全风险' : implode('; ', $security_issues);
    }
    protected static function get_callback_content($callback)
    {
        if (is_string($callback)) {
            // 如果是字符串，假设它是一个函数名
            if (function_exists($callback)) {
                $reflection = new \ReflectionFunction($callback);
                return static::get_code_from_reflection($reflection);
            }
        } elseif (is_array($callback) && count($callback) === 2) {
            // 如果是数组，假设它是一个类方法
            $class = is_object($callback[0]) ? get_class($callback[0]) : $callback[0];
            if (method_exists($class, $callback[1])) {
                $reflection = new \ReflectionMethod($class, $callback[1]);
                return static::get_code_from_reflection($reflection);
            }
        } elseif ($callback instanceof \Closure) {
            // 如果是闭包函数
            $reflection = new \ReflectionFunction($callback);
            return static::get_code_from_reflection($reflection);
        }

        return null;
    }

    protected static function get_code_from_reflection($reflection)
    {
        $file = new \SplFileObject($reflection->getFileName());
        $file->seek($reflection->getStartLine() - 1); // 移动到开始行
        $code = '';

        while ($file->key() < $reflection->getEndLine()) {
            $code .= $file->current();
            $file->next(); // 移动到下一行
        }

        return $code;
    }

    protected static function callback_to_string($callback)
    {
        if (is_string($callback)) {
            return $callback;
        } elseif (is_array($callback) && count($callback) === 2) {
            return is_object($callback[0]) ? get_class($callback[0]) . '->' . $callback[1] : $callback[0] . '::' . $callback[1];
        } elseif ($callback instanceof \Closure) {
            return '匿名函数';
        }
        return '未知回调类型';
    }
    public static function get_all_usehooks(){
        $all = [];
        foreach (self::$actions as $key => $value) {
            $all['action'][] = $key;
        }
        foreach (self::$filters as $key => $value) {
            $all['filter'][] = $key;
        }
        return $all;
    }

    // 新增方法：获取沙箱中的钩子
    public static function get_sandbox_hooks()
    {
        return [
            'actions' => self::$sandbox_actions,
            'filters' => self::$sandbox_filters
        ];
    }

    // 新增方法：清除沙箱中的钩子
    public static function clear_sandbox()
    {
        self::$sandbox_actions = [];
        self::$sandbox_filters = [];
    }
}
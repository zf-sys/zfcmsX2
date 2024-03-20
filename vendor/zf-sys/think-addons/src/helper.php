<?php

use think\facade\App;
use think\facade\Env;
use think\facade\Hook;
use think\facade\Config;
use think\Loader;
use think\facade\Cache;
use think\facade\Route;

// 插件目录
$appPath = App::getAppPath();
$addons_path = dirname($appPath) . DIRECTORY_SEPARATOR . 'addons' . DIRECTORY_SEPARATOR;
Env::set('addons_path', $addons_path);

// 插件访问路由配置
Route::group('addons', function () {
    if (!isset($_SERVER['REQUEST_URI'])) {
        return 'error addons';
    }
    // 请求位置
    $path = ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    if ($ext) {
        $path = substr($path, 0, strlen($path) - (strlen($ext) + 1));
    }
    // $path = 'addons/zf_product_pm.index/detail/id/10';
    $pathinfo = explode('/', $path);
    // 路由地址
    if ($pathinfo[0] == 'addons' && isset($pathinfo[2])) {
        // 获取路由地址
        $route = explode('.', $pathinfo[1]); 
        $module = $pathinfo[0];
        $className = array_shift($route);
        $className2 = ucfirst(array_pop($route));
        array_push($route, $className);
        array_push($route, $className2);
        $controller = $route[1];
        $type = array_shift($route);
        // 生成view_path
        $view_path = Env::get('addons_path') . $className . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR;
        Config::set('template.view_path', $view_path);
        // 中间件
        $middleware = [];
        $config = Config::get('addons.middleware');
        if (is_array($config) && isset($config[$type])) {
            $middleware = (array)$config[$type];
        }
        // 请求转入
        Route::rule(':rule', "\\addons\\{$className}\\controller\\{$controller}@{$pathinfo[2]}")
            ->middleware($middleware);
    }
})->middleware(function ($request, \Closure $next) {
    // 路由地址
    $pathinfo = explode('/', $request->path());
    $rules = explode('.', $pathinfo[1]);
    $request->setModule($pathinfo[0]);
    $request->setController($rules[1]);
    // $request->setController(join('/', $rules));
    $request->setAction($pathinfo[2]);
    $_name = $pathinfo[2];
    // unset($request->$_name);
    for($iii=0;$iii<count($pathinfo);$iii++){
        if($iii>=3 && isset($pathinfo[$iii+1])){
            $_name = $pathinfo[$iii];
            $request->$_name  = $pathinfo[$iii+1];
        }
    }
    $get_arr = $request->get();
    if(is_array($get_arr)){
        foreach($get_arr as $k=>$vo){
            $_name = $k;
            $request->$_name  = $vo;
        }
    }
    $request->plugin_name = $rules[0];
    $module = $request->module();
    $_file2 = './'.$module.'/'.$request->plugin_name.'/config/function.php';
    if(is_dir('./'.$module.'/'.$request->plugin_name) && file_exists($_file2) ){
        include $_file2;
    }
    return $next($request);
});


// 注册类的根命名空间
Loader::addNamespace('addons', $addons_path);
//addons 路由
try {
    $parent_dir = scandir('./addons');
    foreach($parent_dir as $vo){
        $_file = './addons/'.$vo.'/config/route.php';
        if(is_dir('./addons/'.$vo) && file_exists($_file) ){ include $_file; }
    }
} catch (\Exception $e) {
    $r = $e->getMessage();
}

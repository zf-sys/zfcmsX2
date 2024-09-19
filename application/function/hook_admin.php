<?php

use think\captcha\Captcha;
/**
 * 登录前判断是否初始化sql
 */
function admin_login_before_func_initsql($ti)
{
    $init_sql = ZFC("zf_auth.init_sql",'file');
    if($init_sql!=1){
        redirect('/common/base/upgrade_sys_sql')->send();
    }
}
add_action('admin_login_before', 'admin_login_before_func_initsql',9999);


function admin_login_before_func_right_pic($ti)
{
    $right_img = 'http://bbs.zf-sys.com/upload/common/filesystem/fp/20231105/b89ee6e4cbdf9e7288b291fffd9d95e0.png';
    zf_assign('right_img', $right_img);
}
add_action('admin_login_before', 'admin_login_before_func_right_pic',99);


/**
 * 统计用户输入错误的次数
 */
function admin_login_after_func_check_err($ti,$userInfo)
{
    $data = input('post.');
    $max_login_err_num = intval(ZFC("webconfig.max_login_err_num"));
    if($max_login_err_num==0){
        $max_login_err_num = 3;
    }
    $login_interval_time = intval(ZFC("webconfig.login_interval_time"));
    if($login_interval_time==0){
        $login_interval_time = 5;
    }
    try{
        $err_login_num = db('admin_login_log')->where([['ip','=',request()->ip()],['ctime','between time',[date("Y-m-d H:i:s",time()-$login_interval_time*60), date("Y-m-d H:i:s")]],['err_num','<>','0'],['name','=',$data['name']]])->order('id asc')->count();
        if($err_login_num>=$max_login_err_num){
            return jserror('登录错误超过'.$max_login_err_num.'次,请'.$login_interval_time.'分钟后重试');
        }
        $data['err_login_num'] = $err_login_num;
        if (!$userInfo) {
            save_admin_login($data['name'],$data,0);
            return jserror('用户名或者密码不正确 或没有权限');
        }
        save_admin_login($data['name'],$data,1);
    } catch (\Exception $e) {
        return jserror($e->getMessage());die;
    }
}
add_action('admin_login_after', 'admin_login_after_func_check_err');



function admin_captcha_show()
{
    $t = input('t','');
    if($t=='admin_captcha_show'){
        $config =    [
            // 验证码字体大小
            'fontSize'    =>    30,
            // 验证码位数
            'length'      =>    3,
            // 关闭验证码杂点
            'useNoise'    =>    false,
        ];
        ob_clean();
        $captcha = new Captcha($config);
        return  $captcha->entry()->send();
    }

}
add_action('common_act', 'admin_captcha_show');
//页面显示验证码
function admin_login_before_func_captcha_show()
{
//    dd(get_action_hooks('admin_login_before_view'));
    $html = '<div class="form-group">
                <div class="col-sm-12" style="display: inline-flex">
                    <input class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white mt-5" type="text" style="width: 60%;" name="captcha" placeholder="验证码">
                    <img src="'.url('common/api/common_act',['t'=>'admin_captcha_show']) .'" alt="captcha" class="captcha mt-5" onclick="this.src=\''.url('common/api/common_act',['t'=>'admin_captcha_show']) .'\'" style="width: 40%;">
                </div>
            </div>';
    echo $html;
}
add_action('admin_login_before_view', 'admin_login_before_func_captcha_show');

//后台验证验证码验证
function admin_login_before_func_captcha($ti)
{
    $captcha = input('post.captcha');
    if(!captcha_check($captcha)){
        return jserror('验证码错误');
    }
}
add_action('admin_login_after', 'admin_login_before_func_captcha');




function admin_login_before_func_input_show()
{
    $html = '<input class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white" type="text" name="name" placeholder="账号">
<input class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white mt-5" type="password" name="pwd" placeholder="密码">
';
    echo $html;
}
add_action('admin_login_before_view', 'admin_login_before_func_input_show',1);


//后台菜单
function admin_menu_append_filter($arr)
{
    return $arr;
}
add_filter('admin_menu_append','admin_menu_append_filter',9);


//后台的内容
add_action('admin_field_append_view_left',function ($hook_data,$form_widget){
//    $hook_db,$hook_id,$hook_type,$hook_data
        $html = '<div class="layui-card">';
        $html .= $form_widget->form_input(['title'=>'SEO(标题)','name'=>'meta[seo_t]','data'=>isset_arr_key($hook_data[3]['meta'],'seo_t',''),'theme'=>1]);
        $html .= $form_widget->form_input(['title'=>'SEO(描述)','name'=>'meta[seo_d]','data'=>isset_arr_key($hook_data[3]['meta'],'seo_d',''),'theme'=>1]);
        $html .= $form_widget->form_input(['title'=>'SEO(关键词)','name'=>'meta[seo_k]','data'=>isset_arr_key($hook_data[3]['meta'],'seo_k',''),'theme'=>1]);
        $html .= '</div>';
        echo $html;
});

add_action('zf_home_js',function (){
    $html = ZFC("webconfig.site_js");
    echo $html;
});


//后台加载效果
function admin_head_html1()
{
    $html = '<style>';
    $html .= '.loading-overlay {
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.8);
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;
}

/* 转圈圈样式 */
.loading-spinner {
    border: 5px solid #f3f3f3;
  border-top: 5px solid #3498db;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>';
    echo $html;
}
add_action('admin_head', 'admin_head_html1',1);
function admin_js_html1()
{
    $html = '<script>
      // 创建加载动画
function createLoadingOverlay() {
    var overlay = document.createElement("div");
    overlay.className = "loading-overlay";

    var spinner = document.createElement("div");
    spinner.className = "loading-spinner";

    overlay.appendChild(spinner);
    document.body.appendChild(overlay);

    return overlay;
}

// 显示加载动画
var loadingOverlay = createLoadingOverlay();

// 在页面加载完成后延迟隐藏加载动画
window.addEventListener("load", function() {
    setTimeout(function() {
        if (loadingOverlay && loadingOverlay.parentNode) {
            loadingOverlay.parentNode.removeChild(loadingOverlay);
        }
    }, 200); // 2000毫秒 = 2秒
});
</script>';
    echo $html;
}
add_action('admin_js', 'admin_js_html1',1);

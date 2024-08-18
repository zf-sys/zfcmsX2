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

//测试匿名函数
add_action('admin_login_before',function (){
    echo '--';
});
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



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
    if($hook_data[0]=='category' && in_array($hook_data[3]['cid'],explode(',',ZFC("webconfig.diyurl_cates") ))){
        return '';
    }
    if($hook_data[0]=='post' && in_array($hook_data[3]['cid'],explode(',',ZFC("webconfig.diyurl_posts") )) ){
        return '';
    }
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

.pagination>li img{
width: 20px !important;
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


//ai写url
add_action('admin_diy_url_view',function ($hook_data){
    $html = '';
    $html.='<a class="layui-btn ai_url_act" item-t="diy_url">AI生成URL</a>';
    $html.='<script>
  $(document).on("click", ".ai_url_act", function(){
    var index = layer.load(2);
    var t = $(this).attr("item-t");
    var data = $(".info_tj input,.info_tj textarea,.info_tj select").serialize();
    $.ajax({
        type:"post",
        url:"/common/api/common_act?z_type=ai_write_url&ai_w_type="+t,
        data:data,
        dataType:"json",
        success:function(res){
            layer.close(index);
            if(res.result==1){
                layer.msg(res.msg, {icon: 1});
                if(t=="diy_url"){
                    $("input[name=\'meta[diy_url]\']").val(res.data.diy_url);
                    $(".diy_url_a").attr("href","/"+res.data.diy_url+".html")
                    $(".diy_url").text(res.data.diy_url)
                }else{
                    layer.msg("未知操作", {icon: 2});
                }
            }else{
                if(res.msg.indexOf("只支持ZFSYS授权的中转")!=-1 || res.msg.indexOf("获取数据错误,请查看提示词是否正")!=-1){
                    layer.confirm(res.msg, {
                            btn: ["关闭","查看说明"] //按钮
                        }, function(){
                        layer.closeAll();
                    }, function(){
                        window.open("//bbs.zf-sys.com/bbs_detail/188.html")
                        });
                    }else{
                    layer.confirm(res.msg, {
                            btn: ["关闭"] //按钮
                        }, function(){
                        layer.closeAll();
                    })
                }
            }
        }
    })
});
    </script>';
    echo $html;
});
//ai写tdk  操作方法
add_action('common_act', function ($thi){
    $z_type = input('z_type','');
    if($z_type=='ai_write_url'){
        $zfai = new \zf\ZfAi();
        $ai_w_type = input('ai_w_type','');
        $data = input('post.');
        if($ai_w_type=='diy_url'){
            $sys_message = '通过下面提供的标题生成适合SEO的URL,url链接不要存在空格等特殊符号开头不要加/,直接返回json数据,例如: {title: "网页标题", diy_url: "网站访问链接" }';
            if(isset($data['title'])){
                $old_content = html_out_par($data['title'],1000);
            }elseif(isset($data['name'])){
                $old_content = html_out_par($data['name'],1000);
            }else{
                return ZFRetMsg(false,'参数错误,请联系开发者');
            }

//            dd($old_content);
            $_data = $zfai->zfyun_openai($old_content,$sys_message);
            if($_data['code']==0){
                return ZFRetMsg(false,'',$_data['msg']);
            }
            $_ret_data_arr = json_decode($_data['msg'],true);
            if(!$_ret_data_arr){
                return ZFRetMsg(false,'','获取数据错误,请查看提示词是否正确或稍后再试');
            }
            if(isset($_ret_data_arr['title']) && isset($_ret_data_arr['diy_url']) )
            {
                $ret_data['title'] = $_ret_data_arr['title'];
                $ret_data['diy_url'] = $_ret_data_arr['diy_url'];
            }   else{
                return ZFRetMsg(false,'','获取数据错误,请查看提示词是否正确或稍后再试2');
            }

            echo json_encode(array("msg" => '获取成功,请查看是否匹配', "data" => $ret_data, "result" => '1'));exit;

        }else{
            return jserror('该类型不存在');
        }
    }

});


//后台index.html  fa图标 白色
//add_action('admin_head', function () {
//    $html = '<style>
//    .fa{color: #fff !important;}
//    .fa_black{color: #000 !important;}
//    </style>';
//    echo $html;
//},1);

function add_category_edit_l_view_default($res,$form_widget,$hook_data){
     $html = tpl_diy_url_v($res,$hook_data,$form_widget);
      $html .= $form_widget->form_input(['title'=>'栏目名称','name'=>'name','data'=>$res['name'],'notes'=>'','theme'=>2]);
      $html .= $form_widget->form_input(['title'=>'英文名称','name'=>'ename','data'=>$res['ename'],'notes'=>'','theme'=>2]);
      $html .= $form_widget->form_input(['title'=>'外部链接','name'=>'url','data'=>$res['url'],'notes'=>'','theme'=>2]);
      $html .= $form_widget->form_input(['title'=>'栏目模板名','name'=>'tpl_category','data'=>$res['tpl_category'],'notes'=>'','theme'=>2]);
      $html .= $form_widget->form_input(['title'=>'内容模板名','name'=>'tpl_post','data'=>$res['tpl_post'],'notes'=>'','theme'=>2]);
      $html .= $form_widget->form_input(['title'=>'每页显示数','name'=>'page','data'=>$res['page'],'notes'=>'','theme'=>2]);
      $html .= $form_widget->form_textarea(['title'=>'栏目描述','name'=>'summary','data'=>$res['summary'],'notes'=>'','theme'=>2]);
      $html .= $form_widget->form_ueditor(['title'=>'内容详情','name'=>'content','data'=>$res['content'],'notes'=>'','theme'=>1]);
      
    echo $html;
}
add_action('add_category_edit_l_view', 'add_category_edit_l_view_default',1);


//add_action('admin_js', function () {
//    $html = '';
//    if(request()->url()=='/admin/index/welcome.html'){
//
//    $html .= '<script>
//    layui.use([ "element","layer"], function(){
//        var $ = layui.$;
//        var layer = layui.layer;
//
//        layer.open({
//            type: 1,
//            title: "提示",
//            area: ["800px", "600px"],
//            content: "<div style=\'padding: 30px;\'>" +
//             "ZFCMS使用说明:" +
//             "<br><br>" +
//             "当前有两种版本:社区版和授权版"+
//             "<br><br>" +
//             "社区版：免费使用，但无法升级，<span style=\'color:red\'>不提供技术支持</span>,无法使用插件，无法使用付费功能。" +
//             "<br><br>" +
//             "授权版：需要绑定授权信息，才能使用付费功能，使用插件，使用付费功能。" +
//             "<br><br>" +
//             "<hr>"+
//             "<br><br>" +
//
//              "</div>",
//            btn: ["我已了解"],
//            yes: function(index, layero){
//                layer.close(index);
//            },
//        });
//    });
//</script>';
//    }
//    echo $html;
//},1);
function admin_welcome_after_func_initsql($ti)
{
    if(ZFC("zf_auth.key",'file')=='' && ZFC("zf_auth.sc",'file')=='' && file_exists('./extend/zf/Yun.php')){
        redirect('/common/Sysauth/auth')->send();
    }
}
add_action('admin_welcome_after', 'admin_welcome_after_func_initsql',9999);



function admin_pay_setting_epay($form_widget,$config,$type){
    $html = '';
    $html .='<menu>易支付</menu>';
    if($type=='易支付'){
        if(!is_dir('./extend/epay')){
            $html .= '<blockquote class="layui-elem-quote" style="text-align: center;">
                        使用此功能需先安装扩展
                        <a class="layui-btn layui-btn-sm" onclick=\'zfAdminShow("下载","'.url('/admin/config/tool').'")\'>点击下载(功能下载==>易支付类库)</a>
                      </blockquote>';
        }
        $html .= '<div class="list_item">'.
                 $form_widget->form_input(['title'=>'<a class="zf-layui-tips_">API地址</a>','name'=>'epay_apiurl','data'=>isset_arr_key($config,'epay_apiurl',''),'notes'=>'例如: http://pay.jianshe2.com/     使用:','theme'=>2]).
                 '</div>';
        $html .= '<div class="list_item">'.
                 $form_widget->form_input(['title'=>'<a class="zf-layui-tips_">商户号</a>','name'=>'epay_partner','data'=>isset_arr_key($config,'epay_partner',''),'notes'=>'例如: 123456','theme'=>2]).
                 '</div>';
        $html .= '<div class="list_item">'.
                 $form_widget->form_input(['title'=>'<a class="zf-layui-tips_">Key</a>','name'=>'epay_key','data'=>isset_arr_key($config,'epay_key',''),'notes'=>'例如: saaaaaaa','theme'=>2]).
                 '</div>';
    }
    echo $html;
}
add_action('admin_pay_setting', 'admin_pay_setting_epay');


function admin_web_setting_def($form_widget,$config,$type){
    $html = '';
    $html .='<menu>网站设置</menu>';
    if($type=='网站设置'){
        $html .= '<div class="list_item">'.
                 $form_widget->form_input(['title'=>'<a class="zf-layui-tips">ICP备案</a>','name'=>'site_icp','data'=>isset_arr_key($config,'site_icp',''),'notes'=>'','theme'=>2]).
                 $form_widget->form_note(['data'=>'<div class="zf-tip-content-hidden"><span class="layui-badge_  layui-bg-cyan_ ">ZFC("webconfig.site_icp") ICP</span></div>','theme'=>2]).
                 '</div>';
        $html .= '<div class="list_item">'.
                 $form_widget->form_input(['title'=>'<a class="zf-layui-tips">公安备案</a>','name'=>'site_ga','data'=>isset_arr_key($config,'site_ga',''),'notes'=>'','theme'=>2]).
                 $form_widget->form_note(['data'=>'<div class="zf-tip-content-hidden"><span class="layui-badge_  layui-bg-cyan_ ">ZFC("webconfig.site_ga") 公安备案</span></div>','theme'=>2]).
                 '</div>';
        $html .= '<div class="list_item">'.
                 $form_widget->form_textarea(['title'=>'<a class="zf-layui-tips">版权信息</a>','name'=>'site_copyright','data'=>isset_arr_key($config,'site_copyright',''),'notes'=>'','theme'=>2]).
                 $form_widget->form_note(['data'=>'<div class="zf-tip-content-hidden"><span class="layui-badge_  layui-bg-cyan_ ">ZFC("webconfig.site_copyright") 版权信息</span></div>','theme'=>2]).
                 '</div>';
        $html .= '<div class="list_item">'.
                 $form_widget->form_textarea(['title'=>'<a class="zf-layui-tips">统计代码/JS</a>','name'=>'site_js','data'=>isset_arr_key($config,'site_js',''),'notes'=>'','theme'=>2]).
                 $form_widget->form_note(['data'=>'<div class="zf-tip-content-hidden"><span class="layui-badge_  layui-bg-cyan_ ">ZFC("webconfig.site_js") 统计代码/JS</span></div>','theme'=>2]).
                 '</div>';
    }
    echo $html;
}
add_action('admin_web_setting', 'admin_web_setting_def');

function admin_web_setting_tdk($form_widget,$config,$type){
    $html = '';
    $html .='<menu>网站TDK</menu>';
    if($type=='网站TDK'){
        $html .= '<div class="list_item">'.
                 $form_widget->form_input(['title'=>'<a class="zf-layui-tips">网站名称</a>','name'=>'site_name','data'=>isset_arr_key($config,'site_name',''),'notes'=>'','theme'=>2]).
                 $form_widget->form_note(['data'=>'<div class="zf-tip-content-hidden"><span class="layui-badge_  layui-bg-cyan_ ">ZFC("webconfig.site_name") 网站名称</span></div>','theme'=>2]).
                 '</div>';
        $html .= '<div class="list_item">'.
                 $form_widget->form_textarea(['title'=>'<a class="zf-layui-tips">META关键词</a>','name'=>'site_keywords','data'=>isset_arr_key($config,'site_keywords',''),'notes'=>'','theme'=>2]).
                 $form_widget->form_note(['data'=>'<div class="zf-tip-content-hidden"><span class="layui-badge_  layui-bg-cyan_ ">ZFC("webconfig.site_keywords") META关键词</span></div>','theme'=>2]).
                 '</div>';
        $html .= '<div class="list_item">'.
                 $form_widget->form_textarea(['title'=>'<a class="zf-layui-tips">META描述</a>','name'=>'site_description','data'=>isset_arr_key($config,'site_description',''),'notes'=>'','theme'=>2]).
                 $form_widget->form_note(['data'=>'<div class="zf-tip-content-hidden"><span class="layui-badge_  layui-bg-cyan_ ">ZFC("webconfig.site_description") META描述</span></div>','theme'=>2]).
                 '</div>';
        foreach(explode(',',ZFC('lang')) as $k=>$vo){
            if($vo!=''){
                $html .= '<div class="list_item">'.
                         $form_widget->form_input(['title'=>'<a class="zf-layui-tips">网站名称'.$vo.'</a>','name'=>'site_name_'.$vo,'data'=>isset_arr_key($config,'site_name_'.$vo,''),'notes'=>'','theme'=>2]).
                         $form_widget->form_note(['data'=>'<div class="zf-tip-content-hidden"><span class="layui-badge_  layui-bg-cyan_ ">ZFC("webconfig.site_name_'.$vo.'") 网站名称'.$vo.'</span></div>','theme'=>2]).
                         '</div>';
                $html .= '<div class="list_item">'.
                         $form_widget->form_textarea(['title'=>'<a class="zf-layui-tips">META关键词'.$vo.'</a>','name'=>'site_keywords_'.$vo,'data'=>isset_arr_key($config,'site_keywords_'.$vo,''),'notes'=>'','theme'=>2]).
                         $form_widget->form_note(['data'=>'<div class="zf-tip-content-hidden"><span class="layui-badge_  layui-bg-cyan_ ">ZFC("webconfig.site_keywords_'.$vo.'") META关键词'.$vo.'</span></div>','theme'=>2]).
                         '</div>';
                $html .= '<div class="list_item">'.
                         $form_widget->form_textarea(['title'=>'<a class="zf-layui-tips">META描述'.$vo.'</a>','name'=>'site_description_'.$vo,'data'=>isset_arr_key($config,'site_description_'.$vo,''),'notes'=>'','theme'=>2]).
                         $form_widget->form_note(['data'=>'<div class="zf-tip-content-hidden"><span class="layui-badge_  layui-bg-cyan_ ">ZFC("webconfig.site_description_'.$vo.'") META描述'.$vo.'</span></div>','theme'=>2]).
                         '</div>';
            }
        }
    }
    echo $html;
}
add_action('admin_web_setting', 'admin_web_setting_tdk');

function admin_web_setting_dev($form_widget,$config,$type){
    $html = '';
    $html .='<menu>开发者</menu>';
    if($type=='开发者'){
        $html .= '<div class="list_item">'.
                 $form_widget->form_radio(['title'=>'Diy_form显示','name'=>'isshow_form_parm','data'=>isset_arr_key($config,'isshow_form_parm',''),'parm_data'=>['0'=>'关闭','1'=>'开启'],'notes'=>'','theme'=>2]).
                 $form_widget->form_note(['data'=>'<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan ">ZFC("webconfig.isshow_form_parm")</span>自定义参数</div>','theme'=>2]).
                 '</div>';
        $html .= '<div class="list_item">'.
            $form_widget->form_radio(['title'=>'Copy显示','name'=>'isshow_admincopy','data'=>isset_arr_key($config,'isshow_admincopy',''),'parm_data'=>['0'=>'关闭','1'=>'开启'],'notes'=>'','theme'=>2]).
            $form_widget->form_note(['data'=>'<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan ">ZFC("webconfig.isshow_admincopy")</span>自定义参数</div>','theme'=>2]).
            '</div>';
    }
    echo $html;
}
add_action('admin_web_setting', 'admin_web_setting_dev');
function admin_web_setting_ai($form_widget,$config,$type){
    $api_domain = ZFC('version.api_domain','file');
    $html = '';
    $html .='<menu>AI中转站</menu>';
    if($type=='AI中转站'){
        $html .= '<div class="list_item">' .
        $form_widget->form_input_select(['title' => '<a href="//api.bigmodel.org/" target="_blank">AI中转站</a>', 'name' => 'ai_gpt_host', 'data' => isset_arr_key($config, 'ai_gpt_host', 'http://api.bigmodel.org'), 'notes' => '只支持ZFSYS中转站,<a href="//api.bigmodel.org/" target="_blank">点击跳转注册</a>', 'url' => $api_domain . '/addons/zf_store_softclientv2.api/zfcms_ai_init/t/domain', 'theme' => 4]);
        $html .= $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan "></span></div>', 'theme' => 2]);
        $html .= '</div>';

        $html .= '<div class="list_item">' .
                $form_widget->form_input_select(['title' => '文字模型', 'name' => 'ai_gpt_model', 'data' => isset_arr_key($config, 'ai_gpt_model', ''), 'list_arr' => [], 'id_t' => 'id', 'name_t' => 'name', 'notes' => '', 'url' => $api_domain . '/addons/zf_store_softclientv2.api/zfcms_ai_init/t/text_ai', 'theme' => 4]);
        $html .= $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan "></span></div>', 'theme' => 2]);
        $html .= '</div>';

        $html .= '<div class="list_item">' .
                $form_widget->form_textarea(['title' => '<a class="zf-layui-tips">KEY</a>', 'name' => 'ai_gpt_key', 'data' => isset_arr_key($config, 'ai_gpt_key', ''), 'notes' => '', 'theme' => 2]);
        $html .= $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan ">ZFC("webconfig.ai_gpt_key") </span></div>', 'theme' => 2]);
        $html .= '</div>';

        $html .= '<div class="list_item">' .
                $form_widget->form_input_select(['title' => '图片模型', 'name' => 'ai_gpt_pic_model', 'data' => isset_arr_key($config, 'ai_gpt_pic_model', ''), 'list_arr' => [], 'id_t' => 'id', 'name_t' => 'name', 'notes' => '', 'url' => $api_domain . '/addons/zf_store_softclientv2.api/zfcms_ai_init/t/image_ai', 'theme' => 4]);
        $html .= $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan "></span></div>', 'theme' => 2]);
        $html .= '</div>';

        $html .= '<div class="list_item">' .
                $form_widget->form_input_select(['title' => '图片大小', 'name' => 'ai_gpt_pic_size', 'data' => isset_arr_key($config, 'ai_gpt_pic_size', ''), 'list_arr' => [], 'id_t' => 'id', 'name_t' => 'name', 'notes' => '', 'url' => $api_domain . '/addons/zf_store_softclientv2.api/zfcms_ai_init/t/image_ai_size', 'theme' => 4]);
        $html .= $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan "></span></div>', 'theme' => 2]);
        $html .= '</div>';
    }
 
    echo $html;
}
add_action('admin_web_setting', 'admin_web_setting_ai');


function admin_web_setting_admin($form_widget,$config,$type){
    $html = '';
    $html .='<menu>后台配置</menu>';
    if($type=='后台配置'){
        $html .= '<div class="list_item">' .
                 $form_widget->filesystem_pic(['title' => '登录右侧图片', 'name' => 'admin_login_right_pic', 'data' => isset_arr_key($config, 'admin_login_right_pic', ''), 'notes' => '', 'theme' => 3]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan ">ZFC("webconfig.admin_login_right_pic")</span>后台登录右侧的图片</div>', 'theme' => 2]) .
                 '</div>';
        $html .= '<div class="list_item">' .
                 $form_widget->filesystem_pic(['title' => '后台Logo', 'name' => 'admin_logo_pic', 'data' => isset_arr_key($config, 'admin_logo_pic', ''), 'notes' => '', 'theme' => 3]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan ">ZFC("webconfig.admin_logo_pic")</span>后台左上角的logo</div>', 'theme' => 2]) .
                 '</div>';
        $html .= '<div class="list_item">' .
                 $form_widget->form_input(['title' => '<a class="zf-layui-tips"><a class="zf-layui-tips">最大登录错误数</a></a>', 'name' => 'max_login_err_num', 'data' => isset_arr_key($config, 'max_login_err_num', ''), 'notes' => '', 'theme' => 2]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge_  layui-bg-cyan_ ">ZFC("webconfig.max_login_err_num")<br>一定时间内(可配置)最大登录错误数,默认3次</span></div>', 'theme' => 2]) .
                 '</div>';
        $html .= '<div class="list_item">' .
                 $form_widget->form_input(['title' => '<a class="zf-layui-tips"><a class="zf-layui-tips">最大间隔时间</a></a>', 'name' => 'login_interval_time', 'data' => isset_arr_key($config, 'login_interval_time', ''), 'notes' => '', 'theme' => 2]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge_  layui-bg-cyan_ ">ZFC("webconfig.login_interval_time")<br>这里单位是分钟,直接填数字,默认5</span></div>', 'theme' => 2]) .
                 '</div>';
        $html .= '<div class="list_item">' .
                 $form_widget->form_textarea(['title' => '<a class="zf-layui-tips">后台登录提示文字</a>', 'name' => 'login_html_notice', 'data' => isset_arr_key($config, 'login_html_notice', ''), 'notes' => '', 'theme' => 2]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge_  layui-bg-cyan_ ">ZFC("webconfig.login_html_notice") 后台登录提示文字</span></div>', 'theme' => 2]) .
                 '</div>';
        $html .= '<div class="list_item">' .
                 $form_widget->form_input(['title' => '<a class="zf-layui-tips">默认图片</a>', 'name' => 'def_pic', 'data' => isset_arr_key($config, 'def_pic', ''), 'notes' => '', 'theme' => 2]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge_  layui-bg-cyan_ ">ZFC("webconfig.def_pic") 默认图片</span></div>', 'theme' => 2]) .
                 '</div>';
    }
    echo $html;
}
add_action('admin_web_setting', 'admin_web_setting_admin');


function admin_web_setting_back($form_widget,$config,$type){
    $html = '';
    $html .='<menu>备份相关</menu>';
    if($type=='备份相关'){
        $html .= '<div class="list_item">' .
                 $form_widget->form_radio(['title' => '插件升级备份', 'name' => 'is_back_addons', 'data' => isset_arr_key($config, 'is_back_addons', '1'), 'parm_data' => ['0' => '关闭', '1' => '开启'], 'notes' => '', 'theme' => 2]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan ">ZFC("webconfig.is_back_addons")</span>插件升级备份,该功能仅用于升级时使用</div>', 'theme' => 2]) .
                 '</div>';
        $html .= '<div class="list_item">' .
                 $form_widget->form_radio(['title' => '模板升级备份', 'name' => 'is_back_theme', 'data' => isset_arr_key($config, 'is_back_theme', '1'), 'parm_data' => ['0' => '关闭', '1' => '开启'], 'notes' => '', 'theme' => 2]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan ">ZFC("webconfig.is_back_theme")</span>模板升级备份,该功能仅用于升级时使用</div>', 'theme' => 2]) .
                 '</div>';
        $html .= '<div class="list_item">' .
                 $form_widget->form_radio(['title' => '系统升级备份', 'name' => 'is_back_system', 'data' => isset_arr_key($config, 'is_back_system', '1'), 'parm_data' => ['0' => '关闭', '1' => '开启'], 'notes' => '', 'theme' => 2]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan ">ZFC("webconfig.is_back_system")</span>系统升级备份,该功能仅用于升级时使用</div>', 'theme' => 2]) .
                 '</div>';
    }
    echo $html;
}
add_action('admin_web_setting', 'admin_web_setting_back');


function admin_web_setting_diyurl($form_widget,$config,$type){
    $html = '';
    $html .='<menu>URL显示过滤</menu>';
    if($type=='URL显示过滤'){
        $html .= '<div class="list_item">' .
            $form_widget->form_textarea(['title' => '<a class="zf-layui-tips">分类</a>', 'name' => 'diyurl_cates', 'data' => isset_arr_key($config, 'diyurl_cates', ''), 'notes' => '', 'theme' => 2]) .
            $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge_  layui-bg-cyan_ ">ZFC("webconfig.diyurl_cates") 过滤的分类id  英文,隔开</span></div>', 'theme' => 2]) .
            '</div>';
        $html .= '<div class="list_item">' .
            $form_widget->form_textarea(['title' => '<a class="zf-layui-tips">内容</a>', 'name' => 'diyurl_posts', 'data' => isset_arr_key($config, 'diyurl_posts', ''), 'notes' => '', 'theme' => 2]) .
            $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge_  layui-bg-cyan_ ">ZFC("webconfig.diyurl_posts") 过滤的内容id  英文,隔开</span></div>', 'theme' => 2]) .
            '</div>';
    }
    echo $html;
}
add_action('admin_web_setting', 'admin_web_setting_diyurl');

function admin_web_setting_js($form_widget,$config,$type){
    $api_domain = ZFC('version.api_domain','file');
    $html = '';
    $html .='<menu>JS设置</menu>';
    if($type=='JS设置'){
        $html .= '<div class="list_item">' .
                 $form_widget->form_select(['title' => 'JS版本更新:', 'name' => 'js_version_type', 'data' => isset_arr_key($config, 'js_version_type', ''), 'list_arr' => [['id' => '1', 'name' => '固定版本'], ['id' => 'time', 'name' => '实时更新'], ['id' => 'ymd', 'name' => '每日更新'], ['id' => 'ym', 'name' => '每月更新'], ['id' => 'y', 'name' => '每年更新']], 'id_t' => 'id', 'name_t' => 'name', 'notes' => '', 'theme' => 4]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan ">JS版本更新,仅支持使用widget_st方法引用的文件</span></div>', 'theme' => 2]) .
                 '</div>';
        $html .= '<div class="list_item">' .
                 $form_widget->form_select(['title' => '弹框类型:', 'name' => 'js_tan_type', 'data' => isset_arr_key($config, 'js_tan_type', ''), 'list_arr' => [['id' => 'now', 'name' => '当前窗口'], ['id' => 'newwindow', 'name' => '新窗口打开']], 'id_t' => 'id', 'name_t' => 'name', 'notes' => '', 'theme' => 4]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan ">仅支持使用zfAdminShow方法</span></div>', 'theme' => 2]) .
                 '</div>';
        $html .= '<div class="list_item">' .
                 $form_widget->form_radio(['title' => '开启外部样式', 'name' => 'is_web_static', 'data' => isset_arr_key($config, 'is_web_static', '0'), 'parm_data' => ['0' => '关闭', '1' => '开启'], 'notes' => '是否使用外部样式  css/js ', 'theme' => 2]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan ">ZFC("webconfig.is_web_static")</span>是否使用外部样式</div>', 'theme' => 2]) .
                 '</div>';
        $html .= '<div class="list_item">' .
                 $form_widget->form_input_select(['title' => '外部样式引用', 'name' => 'web_static_host', 'data' => isset_arr_key($config, 'web_static_host', ''), 'notes' => '', 'url' => $api_domain . '/addons/zf_store_softclientv2.api/zfcms_web_static/t/domain', 'notes' => '  仅限于/public/static/zfcms/style下的静态文件  ', 'theme' => 4]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan "></span></div>', 'theme' => 2]) .
                 '</div>';
    }
    echo $html;
}
add_action('admin_web_setting', 'admin_web_setting_js');

function admin_web_setting_moren($form_widget,$config,$type){
    $html = '';
    $html .='<menu>默认状态</menu>';
    if($type=='默认状态'){
        $html .= '<div class="list_item">' .
                 $form_widget->form_radio(['title' => '栏目新增状态', 'name' => 'category_status', 'data' => isset_arr_key($config, 'category_status', ''), 'parm_data' => ['0' => '关闭', '1' => '打开'], 'notes' => '', 'theme' => 2]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan ">ZFC("webconfig.category_status")</span>栏目新增时默认的状态</div>', 'theme' => 2]) .
                 '</div>';
    }
    echo $html;
}
add_action('admin_web_setting', 'admin_web_setting_moren');

function admin_web_setting_tongzhi($form_widget,$config,$type){
    $html = '';
    $html .='<menu>通知</menu>';
    if($type=='通知'){
        $html .= '<div class="list_item">' .
                 $form_widget->form_select(['title' => '选择通知方式:', 'name' => 'notice_type', 'data' => isset_arr_key($config, 'notice_type', ''), 'list_arr' => [['id' => '', 'name' => '未配置'], ['id' => 'notice_bark', 'name' => 'bark'], ['id' => 'notice_feishu', 'name' => '飞书'], ['id' => 'notice_dingding', 'name' => '钉钉'], ['id' => 'notice_qiwei', 'name' => '企业微信'], ['id' => 'email', 'name' => '邮箱']], 'id_t' => 'id', 'name_t' => 'name', 'notes' => '', 'theme' => 4]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan ">通知方式</span></div>', 'theme' => 2]) .
                 '</div>';
        $html .= '<div class="list_item">' .
                 $form_widget->form_input(['title' => '<a class="zf-layui-tips">Bark</a>', 'name' => 'notice_bark', 'data' => isset_arr_key($config, 'notice_bark', ''), 'notes' => '', 'theme' => 2]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan ">ZFC("webconfig.notice_bark")<br>Bark通知</span></div>', 'theme' => 2]) .
                 '</div>';
        $html .= '<div class="list_item">' .
                 $form_widget->form_input(['title' => '<a class="zf-layui-tips">飞书</a>', 'name' => 'notice_feishu', 'data' => isset_arr_key($config, 'notice_feishu', ''), 'notes' => '', 'theme' => 2]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan ">ZFC("webconfig.notice_feishu")<br>飞书通知</span></div>', 'theme' => 2]) .
                 '</div>';
        $html .= '<div class="list_item">' .
                 $form_widget->form_input(['title' => '<a class="zf-layui-tips">钉钉</a>', 'name' => 'notice_dingding', 'data' => isset_arr_key($config, 'notice_dingding', ''), 'notes' => '', 'theme' => 2]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan ">ZFC("webconfig.notice_dingding")<br>钉钉通知(钉钉上需设置你的服务器地址)</span></div>', 'theme' => 2]) .
                 '</div>';
        $html .= '<div class="list_item">' .
                 $form_widget->form_input(['title' => '<a class="zf-layui-tips">企微</a>', 'name' => 'notice_qiwei', 'data' => isset_arr_key($config, 'notice_qiwei', ''), 'notes' => '', 'theme' => 2]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan ">ZFC("webconfig.notice_qiwei")<br>企微通知</span></div>', 'theme' => 2]) .
                 '</div>';
        $html .= '<div class="list_item">' .
                 $form_widget->form_input(['title' => '<a class="zf-layui-tips">接收邮箱地址</a>', 'name' => 'notice_email', 'data' => isset_arr_key($config, 'notice_email', ''), 'notes' => '', 'theme' => 2]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan ">ZFC("webconfig.notice_email")<br>接收邮箱地址</span></div>', 'theme' => 2]) .
                 '</div>';
    }
    echo $html;
}
add_action('admin_web_setting', 'admin_web_setting_tongzhi');

function admin_web_setting_seo($form_widget,$config,$type){
    $html = '';
    $html .='<menu>SEO</menu>';
    if($type=='SEO'){
        $html .= '<div class="list_item">' .
                 $form_widget->form_select(['title' => 'seo标题方式:', 'name' => 'seo_title_type', 'data' => isset_arr_key($config, 'seo_title_type', ''), 'list_arr' => [['id' => '', 'name' => '保持现状'], ['id' => '1', 'name' => '尾部加系统站点名']], 'id_t' => 'id', 'name_t' => 'name', 'notes' => '', 'theme' => 4]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan ">seo标题方式,用于前台的seo标题显示</span></div>', 'theme' => 2]) .
                 '</div>';
        $html .= '<div class="list_item">' .
                 $form_widget->form_radio(['title' => '前台静态化', 'name' => 'is_theme_cache', 'data' => isset_arr_key($config, 'is_theme_cache', ''), 'parm_data' => ['0' => '关闭', '1' => '打开'], 'notes' => '', 'theme' => 2]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan ">ZFC("webconfig.is_theme_cache")</span>是否开启前台静态化,缓存路径/cache/模板/</div>', 'theme' => 2]) .
                 '</div>';
        $html .= '<div class="list_item">' .
                 $form_widget->form_textarea(['title' => '<a class="zf-layui-tips">静态化过滤路径</a>', 'name' => 'theme_cache_lth_tsdir', 'data' => isset_arr_key($config, 'theme_cache_lth_tsdir', 'search'), 'notes' => '', 'theme' => 2]) .
                 $form_widget->form_note(['data' => '<div class="zf-tip-content-hidden"><span class="layui-badge  layui-bg-cyan ">ZFC("webconfig.theme_cache_lth_tsdir") 使用,间隔</span></div>', 'theme' => 2]) .
                 '</div>';
    }
    echo $html;
}
add_action('admin_web_setting', 'admin_web_setting_seo');


function admin_web_setting_xitong($form_widget,$config,$type){
    $html = '';
    $html .='<menu>系统设置</menu>';
    echo $html;
}
add_action('admin_web_setting', 'admin_web_setting_xitong');

function admin_web_setting_water($form_widget,$config,$type){
    $html = '';
    $html .='<menu>水印配置</menu>';
    echo $html;
}
add_action('admin_web_setting', 'admin_web_setting_water');

function admin_web_setting_upload($form_widget,$config,$type){
    $html = '';
    $html .='<menu>上传设置</menu>';
    echo $html;
}
add_action('admin_web_setting', 'admin_web_setting_upload');


//ZFC("webconfig.isshow_admincopy")
//z更加copy按钮
function admin_action_btn_copy($tb,$res){
    $isshow_admincopy = ZFC("webconfig.isshow_admincopy");
    if($isshow_admincopy){
        if($tb=='category'){
            $url = url('common/api/common_act',['t'=>'admin_act_btn_copy_act','tb'=>$tb,'id'=>$res['cid']]);
        }else{
            $url = url('common/api/common_act',['t'=>'admin_act_btn_copy_act','tb'=>$tb,'id'=>$res['id']]);
        }
            echo '<br><a class="layui-btn layui-btn-xs" href="'.$url.'" target="_blank">复制</a>';
    }
}
add_action('admin_action_btn', 'admin_action_btn_copy');


function admin_act_btn_copy_act()
{
    $t = input('t','');
    $tb = input('tb','');
    if($t=='admin_act_btn_copy_act'){
        if(!session('admin')){
            echo  '<script> alert("未登录")</script>';die;
        }
        $isshow_admincopy = ZFC("webconfig.isshow_admincopy");
        if(!$isshow_admincopy) {
            echo  '<script> alert("未开启复制功能")</script>';die;
        }
        if($tb=='category_model'){
            $id = input('id','');
            $data = Db::name('category_model')->where(['id'=>$id])->find();
            if(!$data){
                echo  '<script> alert("数据不存在")</script>';die;
            }
            if($data['is_parm']==1){
                //获取下一级的数据
                $data2 = Db::name('category_model_parm')->where(['mid'=>$data['id']])->select();
            }
            //复制数据
            unset($data['id']);
            $data['name'] = $data['name'].'-copy';
            $data['model'] = $data['model'].'-copy';
            Db::startTrans();
            try{
                $add_id = Db::name('category_model')->insertGetId($data);
                if($add_id){
                    if(isset($data2) && count($data2)>0){
                        foreach ($data2 as $key => $value) {
                            $value['mid'] = $add_id;
                            unset($value['id']);
                            Db::name('category_model_parm')->insert($value);
                        }
                    }
                    Db::commit();
                    echo '<script> alert("复制成功，请刷新页面查看")</script>';die;
                }else{
                    Db::rollback();
                    echo '<script> alert("复制失败，请刷新页面查看")</script>';die;
                }
            }catch (\Exception $e){
                Db::rollback();
                echo '<script> alert("复制失败2，请刷新页面查看")</script>';die;
            }
            
        }elseif($tb=='category'){
            $id = input('id','');
            $data = Db::name('category')->where(['cid'=>$id])->find();
            if(!$data){
                echo  '<script> alert("数据不存在")</script>';die;
            }
            $data2 = ZFTB('meta_data')->where([['tb','=',$tb],['post_id','=',$data['cid']],['status','<>',9]])->find();
            unset($data['cid']);
            $data['name'] = $data['name'].'-copy';
            $data['ename'] = $data['ename'].'-copy';
            Db::startTrans();
            try{
                $add_id = Db::name('category')->insertGetId($data);
                if($add_id){
                    if(isset($data2) && $data2){
                        unset($data2['meta_id']);
                        $data2['meta_data'] = str_replace($data2['diy_url'],$data2['diy_url'].'-copy',$data2['meta_data']);
                        $data2['diy_url'] = $data2['diy_url'].'-copy';
                        $data2['post_id'] = $add_id;
                        Db::name('meta_data')->insert($data2);
                    }
                    Db::commit();
                    echo '<script> alert("复制成功，请刷新页面查看")</script>';die;
                }else{
                    Db::rollback();
                    echo '<script> alert("复制失败，请刷新页面查看")</script>';die;
                }
            }catch (\Exception $e){
                Db::rollback();
                echo '<script> alert("复制失败2，请刷新页面查看")</script>';die;
            }
        }elseif($tb=='post'){
            $id = input('id','');
            $data = Db::name('post')->where(['id'=>$id])->find();
            if(!$data){
                echo  '<script> alert("数据不存在")</script>';die;
            }
            $data2 = ZFTB('meta_data')->where([['tb','=',$tb],['post_id','=',$data['id']],['status','<>',9]])->find();
            unset($data['id']);
            $data['title'] = $data['title'].'-copy';
            Db::startTrans();
            try{
                $add_id = Db::name('post')->insertGetId($data);
                if($add_id){
                    if(isset($data2) && $data2){
                        unset($data2['meta_id']);
                        $data2['meta_data'] = str_replace($data2['diy_url'],$data2['diy_url'].'-copy',$data2['meta_data']);
                        $data2['diy_url'] = $data2['diy_url'].'-copy';
                        $data2['post_id'] = $add_id;
                        Db::name('meta_data')->insert($data2);
                    }
                    Db::commit();
                    echo '<script> alert("复制成功，请刷新页面查看")</script>';die;
                }else{
                    Db::rollback();
                    echo '<script> alert("复制失败，请刷新页面查看")</script>';die;
                }
            }catch (\Exception $e){
                Db::rollback();
                echo '<script> alert("复制失败2，请刷新页面查看")</script>';die;
            }
        }elseif($tb=='advert'){
            $id = input('id','');
            $data = Db::name('advert')->where(['id'=>$id])->find();
            if(!$data){
                echo  '<script> alert("数据不存在")</script>';die;
            }
            unset($data['id']);
            if($data['pid']=='0'){
                $data2 =  Db::name('advert')->where(['pid'=>$id])->select();
            }
            $data['name'] = $data['name'].'-copy';
            Db::startTrans();
            try{
                $add_id = Db::name('advert')->insertGetId($data);
                if($add_id){
                    if(isset($data2) && count($data2)>0){
                        foreach ($data2 as $key => $value){
                            unset($value['id']);
                            $value['name'] = $value['name'].'-copy';
                            $value['pid'] = $add_id;
                            Db::name('advert')->insert($value);
                        }
                    }
                    Db::commit();
                    echo '<script> alert("复制成功，请刷新页面查看")</script>';die;
                }else{
                    Db::rollback();
                    echo '<script> alert("复制失败，请刷新页面查看")</script>';die;
                }
            }catch (\Exception $e){
                Db::rollback();
                echo '<script> alert("复制失败2，请刷新页面查看")</script>';die;
            }

        }else{
            echo '<script> alert("暂不支持")</script>';die;
        }
    }

}
add_action('common_act', 'admin_act_btn_copy_act');


add_action('zfcms_config_act', function ($config){
    if(isset($config['site_host']) && $config['site_host']!=''){
        //判断最后一个字符如是/,则返回报错
        if(substr($config['site_host'],-1)=='/'){
            return jserror('网站域名不能以/结尾');
        }
        //开头必须是http
        if(substr($config['site_host'],0,4)!='http'){
            return jserror('网站域名必须以http开头');
        }
    }
});
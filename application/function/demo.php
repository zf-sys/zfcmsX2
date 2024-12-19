<?php

//remove_hook('action', 'admin_web_setting', 'admin_web_setting_water');
//remove_hook('action', 'admin_web_setting', 'admin_web_setting_tongzhi');
//remove_hook('action', 'admin_web_setting', 'admin_web_setting_js');
//remove_hook('action', 'admin_web_setting', 'admin_web_setting_back');
//remove_hook('action', 'admin_web_setting', 'admin_web_setting_ai');
//remove_hook('action', 'admin_web_setting', 'admin_web_setting_dev');
//remove_hook('action', 'admin_web_setting', 'admin_web_setting_upload');

//add_action('add_guessbook_edit_view',function($res,$form_widget){
//    $html = '';
//    if($res['ctype']=='liuyan'){
//        $html.='您的姓名:'.$res['name'].'<br>';
//        $html.='您的国家:'.$res['country'].'<br>';
//        $html.='您的邮箱:'.$res['email'].'<br>';
//        $html.='公司名称:'.$res['company'].'<br>';
//        $html.='电话号码:'.$res['qh'].'   '.$res['tel'].'<br>';
//        $pro = db('post')->where(['id'=>$res['pro_id']])->find();
//        $html.='产品系列:'.$pro['title'].'  ID:'.$pro['id'].'<br>';
//        $html.='留言内容:'.$res['content'].'<br>';
//    }
//    if($res['ctype']=='资料索取'){
//        $html.='您的姓名:'.$res['name'].'<br>';
//        $html.='您的国家:'.$res['country'].'<br>';
//        $html.='您的邮箱:'.$res['email'].'<br>';
//        $html.='购买来源:'.$res['buy_origin'].'<br>';
//        $html.='电话号码:'.$res['qh'].'   '.$res['tel'].'<br>';
//        $pro = db('post')->where(['id'=>$res['pro_id']])->find();
//        $html.='产品系列:'.$pro['title'].'  ID:'.$pro['id'].'<br>';
//        $html.='留言内容:'.$res['content'].'<br>';
//    }
//    echo $html;
//});
//
//
//
//function admin_web_setting_def2($form_widget,$config,$type){
//    $html = '';
//    // $html .='<menu>网站设置</menu>';
//    if($type=='网站设置'){
//        $html .= '<div class="list_item">'.
//            $form_widget->form_input(['title'=>'<a class="zf-layui-tips">手机号</a>','name'=>'site_tel','data'=>isset_arr_key($config,'site_tel',''),'notes'=>'','theme'=>2]).
//            $form_widget->form_note(['data'=>'<div class="zf-tip-content-hidden"><span class="layui-badge_  layui-bg-cyan_ ">ZFC("webconfig.site_tel") 手机号</span></div>','theme'=>2]).
//            '</div>';
//        $html .= '<div class="list_item">'.
//            $form_widget->form_input(['title'=>'<a class="zf-layui-tips">邮箱</a>','name'=>'site_email','data'=>isset_arr_key($config,'site_email',''),'notes'=>'','theme'=>2]).
//            $form_widget->form_note(['data'=>'<div class="zf-tip-content-hidden"><span class="layui-badge_  layui-bg-cyan_ ">ZFC("webconfig.site_email") 邮箱</span></div>','theme'=>2]).
//            '</div>';
//        $html .= '<div class="list_item">'.
//            $form_widget->form_input(['title'=>'<a class="zf-layui-tips">地址</a>','name'=>'site_address','data'=>isset_arr_key($config,'site_address',''),'notes'=>'','theme'=>2]).
//            $form_widget->form_note(['data'=>'<div class="zf-tip-content-hidden"><span class="layui-badge_  layui-bg-cyan_ ">ZFC("webconfig.site_address") 地址</span></div>','theme'=>2]).
//            '</div>';
//    }
//    echo $html;
//}
//add_action('admin_web_setting', 'admin_web_setting_def2');



////文章/栏目等过滤参数判断
//function deal_data_edit1($tb,$data){
//    if($tb=='post'){
//        if($data['summary']==''){
//            return ZFRetMsg(false,'','summary不能为空');
//        }
//    }
//}
//add_action('deal_data_edit', 'deal_data_edit1');
//function deal_data_add1($tb,$data){
//    if($tb=='post'){
//        if($data['summary']==''){
//            return ZFRetMsg(false,'','summary不能为空');
//        }
//    }
//}
//add_action('deal_data_add', 'deal_data_add1');
////修改数据 (从小到大)
//function filter_data_edit1($ret_data){
//    $tb = $ret_data['tb'];
//    $data = $ret_data['data'];
//    if($tb=='post'){
//        $data['summary'].='--tag';
//    }
//    $ret_data['data'] = $data;
//    return $ret_data;
//}
//add_filter('filter_data_edit', 'filter_data_edit1',10);
//function filter_data_add2($ret_data){
//    $tb = $ret_data['tb'];
//    $data = $ret_data['data'];
//    if($tb=='post'){
//        $data['summary'].='--@@';
//    }
//    $ret_data['data'] = $data;
//    return $ret_data;
//}
//add_filter('filter_data_add', 'filter_data_add2',9);


//栏目和post 关联tags select
function add_category_edit_l_view_default2($res,$form_widget,$hook_data){
        $html = $form_widget->form_textarea(['title'=>'TAGS','name'=>'tags','data'=>$res['tags'],'notes'=>'','theme'=>1]);
        echo $html;
}
add_action('add_category_edit_l_view', 'add_category_edit_l_view_default2');


add_action('post_add_hook', function($cid) {
    $list_tags = explode(',',get_tb_field('category','tags',$cid));
    zf_assign('list_tags', $list_tags);
});

//function admin_menu_append_filter_defaultmenu($arr)
//{
//    $temp_arr[] = [
//        'title'=>'内容',
//        'icon'=>'fa fa-file-text-o',
//        'href'=>'javascript:;',
//        'child'=>[
//            [
//                'title'=>'CMS',
//                'icon'=>'fa fa-file-text-o',
//                'href'=>'',
//                'child'=>[
//                    ['title'=>'内容板块','href'=>'/admin/Category/index'],
//                    ['title'=>'内容模板','href'=>'/admin/Category/category_model'],
//                    ['title'=>'内容列表','href'=>'/admin/Category/post_all_list'],
//                    ['title'=>'广告管理','href'=>'/admin/Rests/advert'],
//                    ['title'=>'超链管理','href'=>'/admin/Rests/link'],
//                    ['title'=>'留言模板','href'=>'/admin/Rests/guessbook'],
//                    ['title'=>'文章列表Pro','href'=>'/admin/Category/pro_post_list'],
//                ]
//            ],
//            [
//                'title'=>'网站系统',
//                'icon'=>'fa fa-file-text-o',
//                'href'=>'',
//                'child'=>[
//                    ['title'=>'网站设置','href'=>'admin/Config/index'],
//                    ['title'=>'系统参数','href'=>'admin/Config/custom_config'],
//                    ['title'=>'系统管理员','href'=>'admin/Config/admin_index'],
//                    ['title'=>'系统权限','href'=>'admin/Config/admin_role'],
//                    ['title'=>'SQL执行','href'=>'admin/Mysql/sql_exec'],
//                ]
//            ]
//        ]
//    ];
//    $arr = array_merge($arr,$temp_arr);
//    return $arr;
//}
//add_filter('admin_menu_append','admin_menu_append_filter_defaultmenu',9);
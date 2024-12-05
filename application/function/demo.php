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

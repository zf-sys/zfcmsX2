<?php
add_action('home_init',function (){
    // 检查站点是否关闭
    if (ZFC("webconfig.site_closed")) {
//        $this->error('站点已关闭', ZFC("webconfig.site_closed_url"));
        redirect(ZFC("webconfig.site_closed_url"))->send();
    }
});

//tdk
add_action('index_tdk',function ($seo){
    $html = '<title>'.$seo['title'].'</title>';
    $html.= '<meta name="keywords" content="'.$seo['keywords'].'" />';
    $html.= '<meta name="description" content="'.$seo['description'].'" />';
    echo $html;
});
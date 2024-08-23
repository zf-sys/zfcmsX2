<?php
add_action('home_init',function (){
    // 检查站点是否关闭
    if (ZFC("webconfig.site_closed")) {
        redirect(ZFC("webconfig.site_closed_url"))->send();
    }
});


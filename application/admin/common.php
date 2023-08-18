<?php





/* 
* 检测网页是否被百度收录，返回1则表示收录 返回0表示没有收录 
* @ param string $url 待检测的网址 
*/
if (!function_exists('checkBaiduInclude')) {
    function checkBaiduInclude($url){ 
        $res = https_get('https://api.uomg.com/api/collect.baidu?url='.$url);
        $res_arr = json_decode($res,true);
        if(isset($res_arr['code']) && $res_arr['code']==1){
            return 1; 
        }else{
            return 0;
        }
    }
} 


/**
 * content2keyword(): 替换正文中出现的TAG标签为超链接，要求：将 <?php echo $log_content; ?>的地方，改为 <?php echo content2keyword($log_content); ?>
 * 此函数效率相对较低，但暂时没有更好方法实现，主要是避免 将 a、img 标签中的 alt、title 等内容与TAG重复时的处理
 * @param unknown_type $content
 * @return unknown|mixed
 */
if (!function_exists('content2keyword')) {
    function content2keyword($content,$tags){
        // global $CACHE;
        // $tags = $CACHE->readCache('tags');
        if($tags==''){
            return $content;
        }
        $tags = explode(',',$tags);
        if( !is_array($tags) ){
            return $content;
        }
        
        //避免在链接、IMG标签内重复添加，先将A标签和 IMG标签匹配并保存起来
        preg_match_all('/<([a|img]).*>.*<\/\\1>/Uis', $content, $d);
        if( is_array( $d[0] ) ){
                foreach( $d[0] as $key=>$val ){
                        $flag = "{======{$key}======}";
                        $content = str_replace_limit($val, $flag, $content,3);
                }
        }
        
        //替换tags链接
        foreach( $tags as $k=>$val ){
            if(!in_array($val,['','/','1']) ){
                $tagurl = "<a title='".$val."' href='/tag/".$val.".html' >".$val."</a>";
                $content = str_replace_limit( $val, $tagurl, $content,3);
            }
        }
        //将之前保存的A、IMG标签，再替换回原样
        if( is_array( $d[0] ) ){
                foreach( $d[0] as $key=>$val ){
                    $flag = "{======{$key}======}";
                    $content = str_replace_limit($flag, $val, $content,3);
                }
        }
        return $content;
    }
}

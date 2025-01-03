<?php
// +----------------------------------------------------------------------
// | 子枫CMS管理系统
// +----------------------------------------------------------------------
// | Copyright (c)  http://store.zf-sys.com/
// | 子枫CMS管理系统提供免费使用,可使用此框架进行二次开发
// +----------------------------------------------------------------------
// | Author: 子枫 <287851074@qq.com>
// | 子枫社区:  http://bbs.90ckm.com/
// +----------------------------------------------------------------------
// 应用公共文件
use think\Controller;
use think\Db;
use think\facade\Hook;
use app\common\controller\CronManager;
include './application/hooks.php';
include './application/common_db.php';

// 应用公共文件
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use GuzzleHttp\Client;
/**
 * @Notes: 后台权限  0 get ajax 全部验证  1 只验证ajax
 * @Interface admin_role_check
 * @param array $z_role_list
 * @param string $mca
 * @param int $type
 * @author: 子枫
 * @Time: 2019/11/13   11:05 下午
 */
if(!function_exists('admin_role_check')){
    function admin_role_check($z_role_list=[],$mca='',$type=''){
        $methods = request()->method();
        if(!session('admin')){
            if($methods=="GET"){
                echo "<script>alert('请先登录后台,在进行操作');</script>";die;
            }else{
                return jserror('请登录后操作');
            }
        }
        $arr_filter = [
            'admin/Index/welcome',
            'admin/Index/index',
            'admin/Index/get_menu',
            ''
        ];
        if(in_array($mca,$arr_filter)) return ;
        if(session("admin.gid")==1) return ;
        if($methods=="GET"){
            if (!in_array($mca, $z_role_list)) {
                if($type=='1'){
                    echo "<script>alert('当前用户无权限');</script>";die;
                }else{
                    echo "<script>alert('当前用户无权限');</script>";die;
                }
            }
        }else{
            //post  ajax
            if (!in_array($mca, $z_role_list)) {
                return jserror('当前用户无修改权限3');
            }
        }
    }

}



/**
 * @Notes:读取权限,并组成数组
 * @Interface get_admin_role
 * @param $gid
 * @return mixed
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 * @author: 子枫
 * @Time: 2019/11/13   11:07 下午
 */
if(!function_exists('get_admin_role')){
    function get_admin_role($gid){
        $info =ZFTB('admin_group')->where('id',$gid)->find();
        $_role_list = ZFTB('admin_role')->field('value')->where([['id' ,'in', $info['role']]])->select();
        foreach($_role_list as $k=>$vo){
            $role_list[$k] = $vo['value'];
        }
        return $role_list;
    }
}
/**
 * @Notes:通过id,获取权限value(控制器/方法)
 * @Interface get_role_value
 * @param $id
 * @return mixed
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 * @author: 子枫
 * @Time: 2019/11/13   11:07 下午
 */
if(!function_exists('get_role_value')){
    function get_role_value($id){
        $info =ZFTB('admin_role')->where('id',$id)->value('value');
        return $info;
    }
}

/**
 * 返回文件格式
 * @param  string $str 文件名
 * @return string      文件格式
 */
if(!function_exists('file_format')){
    function file_format($str){
        // 取文件后缀名
        $str=strtolower(pathinfo($str, PATHINFO_EXTENSION));
        // 图片格式
        $image=array('webp','jpg','png','ico','bmp','gif','tif','pcx','tga','bmp','pxc','tiff','jpeg','exif','fpx','svg','psd','cdr','pcd','dxf','ufo','eps','ai','hdri');
        // 视频格式
        $video=array('mp4','avi','3gp','rmvb','gif','wmv','mkv','mpg','vob','mov','flv','swf','mp3','ape','wma','aac','mmf','amr','m4a','m4r','ogg','wav','wavpack');
        // 压缩格式
        $zip=array('rar','zip','tar','cab','uue','jar','iso','z','7-zip','ace','lzh','arj','gzip','bz2','tz');
        // 文档格式
        $text=array('exe','doc','ppt','xls','wps','txt','lrc','wfs','torrent','html','htm','java','js','css','less','php','pdf','pps','host','box','docx','word','perfect','dot','dsf','efe','ini','json','lnk','log','msi','ost','pcs','tmp','xlsb');
        // 匹配不同的结果
        switch ($str) {
            case in_array($str, $image):
                return 'image';
                break;
            case in_array($str, $video):
                return 'video';
                break;
            case in_array($str, $zip):
                return 'zip';
                break;
            case in_array($str, $text):
                return 'text';
                break;
            default:
                return 'other';
                break;
        }
    }
}

/**
 * 解析sql语句
 * @param  string $content sql内容
 * @param  int $limit  如果为1，则只返回一条sql语句，默认返回所有
 * @param  array $prefix 替换表前缀
 * @return array|string 除去注释之后的sql语句数组或一条语句
 */
if(!function_exists('parse_sql')){
    function parse_sql($sql = '', $limit = 0, $prefix = []) {
        // 被替换的前缀
        $from = '';
        // 要替换的前缀
        $to = '';
        // 替换表前缀
        if (!empty($prefix)) {
            $to   = current($prefix);
            $from = current(array_flip($prefix));
        }
        if ($sql != '') {
            // 纯sql内容
            $pure_sql = [];
            // 多行注释标记
            $comment = false;
            // 按行分割，兼容多个平台
            $sql = str_replace(["\r\n", "\r"], "\n", $sql);
            $sql = explode("\n", trim($sql));
            // 循环处理每一行
            foreach ($sql as $key => $line) {
                // 跳过空行
                if ($line == '') {
                    continue;
                }
                // 跳过以#或者--开头的单行注释
                if (preg_match("/^(#|--)/", $line)) {
                    continue;
                }
                // 跳过以/**/包裹起来的单行注释
                if (preg_match("/^\/\*(.*?)\*\//", $line)) {
                    continue;
                }
                // 多行注释开始
                if (substr($line, 0, 2) == '/*') {
                    $comment = true;
                    continue;
                }
                // 多行注释结束
                if (substr($line, -2) == '*/') {
                    $comment = false;
                    continue;
                }
                // 多行注释没有结束，继续跳过
                if ($comment) {
                    continue;
                }
                // 替换表前缀
                if ($from != '') {
                    $line = str_replace('`'.$from, '`'.$to, $line);
                }
                if ($line == 'BEGIN;' || $line =='COMMIT;') {
                    continue;
                }
                // sql语句
                array_push($pure_sql, $line);
            }
            // 只返回一条语句
            if ($limit == 1) {
                return implode($pure_sql, "");
            }
            // 以数组形式返回sql语句
            $php_version = PHP_VERSION;
            if($php_version>=7.4){
                $pure_sql = implode("\n",$pure_sql);
            }else{
                $pure_sql = implode($pure_sql, "\n");
            }
            $pure_sql = explode(";\n", $pure_sql);
            return $pure_sql;
        } else {
            return $limit == 1 ? '' : [];
        }
    }
}




//计算整个目录文件大小/文件数量
if(!function_exists('getDirInfo')){
    function getDirInfo($dir, $f = 'size') {
        $result['size'] = '';
        $result['count'] = '';
        $handle = opendir($dir); //打开文件流
        while (($FolderOrFile = readdir($handle)) !== false) {//循环判断文件是否可读
            if ($FolderOrFile != "." && $FolderOrFile != "..") {
                if (is_dir("$dir/$FolderOrFile")) {//判断是否是目录
                    $result['size'] += getDirSize("$dir/$FolderOrFile"); //递归调用
                } else {
                    $result['size'] += filesize("$dir/$FolderOrFile");
                    $result['count']++;
                }
            }
        }
        closedir($handle); //关闭文件流
        $result = ($f == 'size') ? $result['size'] : $result['count']; //返回大小或数量
        return $result;
    }
}
// 单位自动转换函数
if(!function_exists('getRealSize')){
    function getRealSize($size) {
        $kb = 1024;         // Kilobyte
        $mb = 1024 * $kb;   // Megabyte
        $gb = 1024 * $mb;   // Gigabyte
        $tb = 1024 * $gb;   // Terabyte
        if ($size < $kb) {
            return $size . " B";
        } else if ($size < $mb) {
            return round($size / $kb, 2) . " KB";
        } else if ($size < $gb) {
            return round($size / $mb, 2) . " MB";
        } else if ($size < $tb) {
            return round($size / $gb, 2) . " GB";
        } else {
            return round($size / $tb, 2) . " TB";
        }
    }
}
//汉字转拼音
if(!function_exists('get_pinyin')){
    function get_pinyin($srt = '') {
        $py = new Pinyin();
        return $py->output($srt); //输出
    }
}
//遍历删除目录和目录下所有文件
if(!function_exists('del_dir')){
    function del_dir($dir){
        if (!is_dir($dir)){
            return false;
        }
        $handle = opendir($dir);
        while (($file = readdir($handle)) !== false){
            if ($file != "." && $file != ".."){
                is_dir("$dir/$file")? del_dir("$dir/$file"):@unlink("$dir/$file");
            }
        }
        if (readdir($handle) == false){
            closedir($handle);
            @rmdir($dir);
        }
    }
}


/*
彩虹字符串
 */
if(!function_exists('color_txt')){
    function color_txt($str){
        $len        = mb_strlen($str);
        $colorTxt   = '';
        for($i=0; $i<$len; $i++) {
            $colorTxt .=  '<span style="color:'.rand_color().'">'.mb_substr($str,$i,1,'utf-8').'</span>';
        }
        return $colorTxt;
    }
}
// 随机颜色
if(!function_exists('rand_color')){
    function rand_color(){
        return '#'.sprintf("%02X",mt_rand(0,255)).sprintf("%02X",mt_rand(0,255)).sprintf("%02X",mt_rand(0,255));
    }
}
/**
 * 是否是移动端
 *
 * User: cyf
 * Time: 2018/7/3 0003 11:01
 * @return bool
 */
if(!function_exists('isMobile')){
    function isMobile()
    {
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        {
            return true;
        }
        if (isset ($_SERVER['HTTP_VIA']))
        {
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        if (isset ($_SERVER['HTTP_USER_AGENT']))
        {
            $clientkeywords = array ('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile');
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))){
                return true;
            }
        }
        if (isset ($_SERVER['HTTP_ACCEPT']))
        {
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
            {
                return true;
            }
        }
        return false;
    }
}
// 验证邮箱
if(!function_exists('check_email')){
    function check_email($email){
        if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/",$email,$arr)){
            return $arr;
        }else{
            return false;
        }
    }
}

//判断url地址是否完整,不完整进行拼接
if(!function_exists('zf_joint_url')){
    function zf_joint_url($domain='',$url=''){
        $isurl=@get_headers($url);
        if(!$isurl){
            if($url[0].$url[1]=='//'){
                return $url; // 合法
            }else{
                if($url[0]=='/'){
                    return $domain.$url;
                }else{
                    return $domain.'/'.$url;
                }
            }
        }else{
            return $url;
        }
    }
}

// 笛卡尔积
/*
$arr = array(
  array(2),
  array(6,7),
  array('a','b','c')
);
 */
if(!function_exists('dikaer')){
    function dikaer($arr){
        $arr1 = array();
        $result = array_shift($arr);
        while($arr2 = array_shift($arr)){
            $arr1 = $result;
            $result = array();
            foreach($arr1 as $v){
                foreach($arr2 as $v2){
                    if(!is_array($v))$v = array($v);
                    if(!is_array($v2))$v2 = array($v2);
                    $result[] = array_merge_recursive($v,$v2);
                }
            }
        }
        return $result;
    }
}
//dikaerj  以字符串形式输出
if(!function_exists('dikaer_str')){
    function dikaer_str($arr){
        $arr1 = array();
        $result = array_shift($arr);
        while($arr2 = array_shift($arr)){
            $arr1 = $result;
            $result = array();
            foreach($arr1 as $v){
                foreach($arr2 as $v2){
                    $result[] = $v.','.$v2;
                }
            }
        }
        return $result;
    }
}


/**
 * 获取当前域名
 * @param bool $http true 返回http协议头,false 只返回域名
 * @return string
 */
if (!function_exists('get_domain')) {
    function get_domain($http = true) {
        $host = input('server.http_host');
        $port = input('server.server_port');
        if ($port != 80 && $port != 443 && strpos($host, ':') === false) {
            $host .= ':'.input('server.server_port');
        }
        if ($http) {
            if (input('server.https') && input('server.https') == 'on') {
                return 'https://'.$host;
            }
            return 'http://'.$host;
        }
        return $host;
    }
}



// excel
if (!function_exists('zf_excel_export')) {
    function zf_excel_export($head,$keys,$data,$name){
        ob_end_clean();
        $count = count($head);  //计算表头数量
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        for ($i = 65; $i < $count + 65; $i++) {     //数字转字母从65开始，循环设置表头：
            $sheet->setCellValue(strtoupper(chr($i)) . '1', $head[$i - 65]);
        }
        /*--------------开始从数据库提取信息插入Excel表中------------------*/
        foreach ($data as $key => $item) {             //循环设置单元格：
            //$key+2,因为第一行是表头，所以写到表格时   从第二行开始写
            for ($i = 65; $i < $count + 65; $i++) {     //数字转字母从65开始：
                $z_value = str_replace(['+','\\','/','='],'*',$item[$keys[$i - 65]]);
                $sheet->setCellValue(strtoupper(chr($i)) . ($key + 2), $z_value);
                $spreadsheet->getActiveSheet()->getColumnDimension(strtoupper(chr($i)))->setWidth(40); //固定列宽
            }
        }
        // header('Content-Type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename="' . $name . '.xlsx"');
        // header('Cache-Control: max-age=0');
        // $writer = new Xlsx($spreadsheet);
        // $writer->save('php://output');
        // //删除清空：
        // $spreadsheet->disconnectWorksheets();
        // unset($spreadsheet);
        // exit;
        $writer = IOFactory::createWriter($spreadsheet,'Csv');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.$name.'.csv');
        header('Cache-Control: max-age=0');
        $writer->setUseBOM(true);
        $writer->save('php://output');
    }
}


/**
 * 修改扩展配置文件
 * @param array  $arr  需要更新或添加的配置
 * @param string $file 配置文件名(不需要后辍)
 * @return bool
 */
if (!function_exists('extraconfig')) {
    function extraconfig($arr = [], $file = ''){
        if (is_array($arr)) {
            $filename = $file . '.php';
            $filepath ='./config/' . $filename;
            if (!file_exists($filepath)) {
                $conf = "<?php return [];";
                file_put_contents($filepath, $conf);
            }
            $conf = include $filepath;
            // dd($arr);
            foreach ($arr as $key => $value) {
                $value = str_replace("'","\'",$value);
                $conf[$key] = $value;
            }
            $time = date('Y/m/d H:i:s');
            $str = '<?php
  // +----------------------------------------------------------------------
  // | 子枫后台管理系统(TpFast系列)[基于ThinkPHP5.1开发]
  // +----------------------------------------------------------------------
  // | Copyright (c)  https://store.zf-sys.com/
  // | 子枫后台管理系统提供免费使用,可使用此框架进行二次开发
  // +----------------------------------------------------------------------
  // | Author: 子枫 <287851074@qq.com>
  // +----------------------------------------------------------------------
  // | github:https://github.com/wmc1125/zfAdmin_tpfast
  // | 码云:  https://gitee.com/wmc1125/zfAdmin_tpfast
  // | Mc技术论坛: http://bbs.wangmingchang.com/forum.php?mod=forumdisplay&fid=77
  // +----------------------------------------------------------------------
  // 站点信息最后修改于 '.$time;
            $str .= "  \r\n  \r\n return [\r\n";

            foreach ($conf as $key => $value) {
                $value = str_replace("'","\'",$value);
                $str .= "\t'$key' => '$value',";
                $str .= "\r\n";
            }
            $str .= '];';
            file_put_contents($filepath, $str);
            return true;
        } else {
            return false;
        }
    }
}
/**
 *获取某个目录下的php文件名的函数
 */
if (!function_exists('getControllers')) {
    function getControllers($dir) {
        $pathList = glob($dir . '/*.php');
        $res = [];
        foreach($pathList as $key => $value) {
            $res[] = basename($value, '.php');
        }
        return $res;
    }
}
/**
 *获取某个控制器的方法名的函数
 *此方法过滤父级Base控制器的方法，只保留自己的
 */
if (!function_exists('getActions')) {
    function getActions($className, $base='\app\admin\controller\Admin') {
        $methods = get_class_methods(new $className());//当前控制器方法
        $baseMethods = get_class_methods(new $base());//通用方法
        unset($baseMethods[1]);
        $res = array_diff($methods, $baseMethods);
        return $res;
    }
}


/*
* 发起POST网络提交
* @params string $url : 网络地址
* @params json $data ： 发送的json格式数据
*/
if (!function_exists('https_post')) {
    function https_post($url,$data,$aHeader=FALSE)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        if($aHeader){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $aHeader);
        }else{
            curl_setopt($curl, CURLOPT_HEADER, FALSE) ;
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//避免https 访问http返回false
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}
/*
* 发起GET网络提交
* @params string $url : 网络地址
*/
if (!function_exists('https_get')) {
    function https_get($url,$aHeader=FALSE){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_TIMEOUT,60);
        if($aHeader){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $aHeader);
        }else{
            curl_setopt($curl, CURLOPT_HEADER, FALSE) ;
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 0);

        if (curl_errno($curl)) {
            return 'Errno'.curl_error($curl);
        }
        else{$result=curl_exec($curl);}
        curl_close($curl);
        return $result;
    }
}
//成功之后返回json
if (!function_exists('jssuccess')) {
    function jssuccess($msg, $url = 'back') {
        echo json_encode(array("msg" => $msg, "url" => $url, "result" => '1'));exit;
    }
}
//失败之后返回json
if (!function_exists('jserror')) {
    function jserror($msg, $url = 'back') {
        echo json_encode(array("msg" => $msg, "url" => $url, "result" => '0'));exit;
    }
}
// 返回json整合成功/失败
if (!function_exists('ZFRetMsg')) {
    function ZFRetMsg($is,$success_msg,$error_msg){
        if($is){
            return jssuccess($success_msg);
        }else{
            return jserror($error_msg);
        }

    }
}

/**
 * 数组 转 对象
 *
 * @param array $arr 数组
 * @return object
 */
if (!function_exists('array_to_object')) {
    function array_to_object($arr) {
        if (gettype($arr) != 'array') {
            return;
        }
        foreach ($arr as $k => $v) {
            if (gettype($v) == 'array' || getType($v) == 'object') {
                $arr[$k] = (object)array_to_object($v);
            }
        }
        return (object)$arr;
    }
}
/**
 * 对象 转 数组
 *
 * @param object $obj 对象
 * @return array
 */
if (!function_exists('object_to_array')) {
    function object_to_array($obj) {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)object_to_array($v);
            }
        }

        return $obj;
    }
}



// 输出日志
if (!function_exists('logOutput')) {
    function logOutput($data,$filename='') {
        //数据类型检测
        if (is_array($data)) {
            $data = json_encode($data);
        }
        $dir = './runtime/log/';
        if(!is_dir($dir)) {
            mkdir($dir, 0777,true);
        }
        if($filename==''){
            $filename = $dir.date("Y-m-d").".log";
        }else{
            $filename = $dir.$filename.".log";
        }
        if(!file_exists($filename)){
            file_put_contents($filename, '');
        }
        $str = date("Y-m-d H:i:s")."   $data"."\n";
        file_put_contents($filename, $str, FILE_APPEND|LOCK_EX);
        return null;
    }
}
/**
 * 获取替换文章中的图片路径
 * @param string $xstr 内容
 * @param string $keyword 创建照片的文件名
 * @param string $oriweb 网址
 * @return string
 *
 */
if (!function_exists('replaceimg')) {
    function replaceimg($xstr, $oriweb,$param_src='src',$keyword='caiji'){
        //保存路径
        $d = date('Ymd', time());
        $dirslsitss = './public/upload/'.$keyword.'/'.$d;//分类是否存在
        if(!is_dir($dirslsitss)) {
            mkdir($dirslsitss, 0755,true);
        }
        //匹配图片的src
        preg_match_all('#<img.*?'.$param_src.'="([^"]*)"[^>]*>#i', $xstr, $match);
        foreach($match[1] as $imgurl){
            $imgurl = $imgurl;
            if(is_int(strpos($imgurl, 'http'))){
                $arcurl = $imgurl;
            } else {
                $arcurl = $oriweb.$imgurl;
            }
            $img=file_get_contents($arcurl);
            if(!empty($img)) {
                //保存图片到服务器
                $fileimgname = time()."-".rand(1000,9999).".jpg";
                $filecachs=$dirslsitss."/".$fileimgname;
                if (!file_exists($dirslsitss)) {
                    mkdir($dirslsitss);
                }
                $fanhuistr = file_put_contents( $filecachs, $img );
                $saveimgfile = "/public/upload/$keyword"."/".$d."/".$fileimgname;


                $xstr=str_replace($imgurl,$saveimgfile,$xstr);
            }
        }
        return $xstr;
    }
}
//获取文章的第一个图片
if (!function_exists('rand_post_first_pic')) {
    function rand_post_first_pic($content){
        $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
        preg_match_all($pattern,$content,$matchContent);
        if(isset($matchContent[1][0])){
            return $matchContent[1][0];
        }else{
            return "https://mctool.wangmingchang.com/api/api/sinaimg/t/large/sid/007goYVsgy1g5m2rdby9hj30ku0am74j";
        }
    }
}

//dd
if (!function_exists('dd')) {
    function dd($msg){
        echo "<pre>";
        var_dump($msg);die;
    }
}

// 保存文件到服务器
if (!function_exists('saveFileService')) {
    function saveFileService($url, $save_dir = '', $filename = '', $type = 0) {
        if (trim($url) == '') {
            return false;
        }
        if (trim($save_dir) == '') {
            $save_dir = './';
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir.= '/';
        }
        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return false;
        }
        //获取远程文件所采用的方法
        if ($type) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $content = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $content = ob_get_contents();
            ob_end_clean();
        }
        $size = strlen($content);
        //文件大小
        $fp2 = @fopen($save_dir . $filename, 'a');
        fwrite($fp2, $content);
        fclose($fp2);
        unset($content, $url);
        return array(
            'file_name' => $filename,
            'save_path' => $save_dir . $filename,
            'file_size' => $size
        );
    }
}

// 判断后台是否登录
if (!function_exists('admin_auth')) {
    function admin_auth(){
        if(!session('admin'))
        {
            echo "请先登录";die;
        }
    }
}


if (!function_exists('zf_controller_func_fast')) {
    function zf_controller_func_fast($controller,$function,$parm=[]){
        $controller = new $controller;
        $ret = $controller->$function($parm);
        return $ret;
    }
}


//加密
if (!function_exists('zf_encrypt')) {
    function zf_encrypt($data, $key='zf'){
        $key    =    md5($key);
        $x        =    0;
        $len    =    strlen($data);
        $l        =    strlen($key);
        $char = '';
        $str = '';
        for ($i = 0; $i < $len; $i++)
        {
            if ($x == $l)
            {
                $x = 0;
            }
            $char .= $key[$x];
            $x++;
        }
        for ($i = 0; $i < $len; $i++)
        {
            $str .= chr(ord($data[$i]) + (ord($char[$i])) % 256);
        }
        return base64_encode($str);
    }
}
//解密
if (!function_exists('zf_decrypt')) {
    function zf_decrypt($data, $key='zf'){
        $key = md5($key);
        $x = 0;
        $data = base64_decode($data);
        $len = strlen($data);
        $l = strlen($key);
        $char = '';
        $str = '';
        for ($i = 0; $i < $len; $i++)
        {
            if ($x == $l)
            {
                $x = 0;
            }
            $char .= substr($key, $x, 1);
            $x++;
        }
        for ($i = 0; $i < $len; $i++)
        {
            if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1)))
            {
                $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            }
            else
            {
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return $str;
    }
}


/**
 * 返回文件格式(附件类型)
 * @param  string $file 文件名
 * @return string  文件格式(1：文件、2：压缩包、3：图片、4：视频、5：音频、6、其他)
 */
if (!function_exists('file_format_cn')) {
    function file_format_cn($file){
        // 取文件后缀名
        $str = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        //strtolower 将所有字符转换为小写
        //pathinfo 获取文件信息，详细用法见下面我的补充
        // 文档格式
        $text = array('exe','doc','docx','ppt','xls','xlsx','wps','txt','lrc','wfs','torrent','html','htm','java','js','css','less','php','pdf','pps','host','box','word','perfect','dot','dsf','efe','ini','json','lnk','log','msi','ost','pcs','tmp','xlsb');
        // 压缩格式
        $zip = array('rar','zip','tar','cab','uue','jar','iso','z','7-zip','ace','lzh','arj','gzip','bz2','tz');
        // 图片格式
        $image = array('webp','jpg','png','ico','bmp','gif','tif','pcx','tga','bmp','pxc','tiff','jpeg','exif','fpx','svg','psd','cdr','pcd','dxf','ufo','eps','ai','hdri');
        $video = array('mp4','avi','3gp','rmvb','gif','wmv','mkv','mpg','vob','mov','flv','swf','ape','m4a','m4r','ogg','wavpack');
        //音频格式
        $audio = array('wav','aif','au','mp3','ram','wma','mmf','amr','aac','flac');
        // 匹配不同的结果
        if(in_array($str, $text)){
            return '文本';
        }elseif(in_array($str, $zip)){
            return '压缩';
        }elseif(in_array($str, $image)){
            return '图片';
        }elseif(in_array($str, $video)){
            return '视频';
        }elseif(in_array($str, $audio)){
            return '音频';
        }else{
            return '其他';
        }
    }
}

//判断是否HTTPS
if (!function_exists('isHTTPS')) {
    function isHTTPS()
    {
        if (defined('HTTPS') && HTTPS) return true;
        if (!isset($_SERVER)) return FALSE;
        if (!isset($_SERVER['HTTPS'])) return FALSE;
        if ($_SERVER['HTTPS'] === 1) {  //Apache
            return TRUE;
        } elseif ($_SERVER['HTTPS'] === 'on') { //IIS
            return TRUE;
        } elseif ($_SERVER['SERVER_PORT'] == 443) { //其他
            return TRUE;
        }
        return FALSE;
    }
}

if (!function_exists('siteUrl')) {
    /**
     * 生成url
     * @param string $url 路由地址,类似于tp5的url()函数的第一个参数 如：index/index/hello
     * @param string $vars 路由参数
     * @param string $weDoor 微擎入口 web-Web端入口,app-App端入口
     * @return array|string
     */
    function siteUrl($url = '', $vars = '', $weDoor = '')
    {
        return url($url,$vars,$weDoor);
    }
}



/**
 * 对字符串执行指定次数替换
 * @param  Mixed $search   查找目标值
 * @param  Mixed $replace  替换值
 * @param  Mixed $subject  执行替换的字符串／数组
 * @param  Int   $limit    允许替换的次数，默认为-1，不限次数
 * @return Mixed
 */
if (!function_exists('str_replace_limit')) {
    function str_replace_limit($search, $replace, $subject, $limit=-1){
        if(is_array($search)){
            foreach($search as $k=>$v){
                $search[$k] = '`'. preg_quote($search[$k], '`'). '`';
            }
        }else{
            $search = '`'. preg_quote($search, '`'). '`';
        }
        return preg_replace($search, $replace, $subject, $limit);
    }
}


//返回重定向后的地址
if (!function_exists('get_location')) {
    function get_location($url,$ua=0){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $httpheader[] = "Accept:*/*";
        $httpheader[] = "Accept-Encoding:gzip,deflate,sdch";
        $httpheader[] = "Accept-Language:zh-CN,zh;q=0.8";
        $httpheader[] = "Connection:close";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        curl_setopt($ch, CURLOPT_HEADER, true);
        if ($ua) {
            curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        } else {
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; U; Android 4.0.4; es-mx; HTC_One_X Build/IMM76D) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0");
        }
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($ch);
        curl_close($ch);
        preg_match("/Location: (.*?)\r\n/iU",$ret,$location);
        return $location[1];
    }
}
// 判断是否为链接
if (!function_exists('or_url')) {
    function or_url($url){
        $preg = "/http[s]?:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is";
        if(preg_match($preg,$url)){
            return true;
        }else{
            return false;
        }
    }
}
// ping 地址
if (!function_exists('pingAddress')) {
    function pingAddress($address) {
        $status = -1;
        if (strcasecmp(PHP_OS, 'WINNT') === 0) {
            // Windows 服务器下
            $pingresult = exec("ping -n 1 {$address}", $outcome, $status);
        } elseif (strcasecmp(PHP_OS, 'Linux') === 0) {
            // Linux 服务器下
            $pingresult = exec("ping -c 1 {$address}", $outcome, $status);
        }
        if (0 == $status) {
            return true;
        } else {
            return false;
        }
    }
}
// 获取指定长度的随机字符串
if (!function_exists('zf_rand_str')) {
    function zf_rand_str($length=8){
        // 密码字符集，可任意添加你需要的字符
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = "";
        for ( $i = 0; $i < $length; $i++ )
        {
            $str .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
        return $str ;
    }
}


// 驼峰转下划线
if (!function_exists('toUnderScore')) {
    function toUnderScore($str)
    {
        $dstr = preg_replace_callback('/([A-Z]+)/',function($matchs)
        {
            return '_'.strtolower($matchs[0]);
        },$str);
        return trim(preg_replace('/_{2,}/','_',$dstr),'_');
    }
}

//下划线命名到驼峰命名
if (!function_exists('toCamelCase')) {
    function toCamelCase($str)
    {
        $array = explode('_', $str);
        $result = $array[0];
        $len=count($array);
        if($len>1)
        {
            for($i=1;$i<$len;$i++)
            {
                $result.= ucfirst($array[$i]);
            }
        }
        return $result;
    }
}
//钩子
if (!function_exists('doZfActionInit')) {
    function doZfActionInit($action_name=''){
        try {
            $module = strtolower(request()->module());
            $list = db('hook')->where([['status','=',1],['controller','<>',''],['action','<>',''],['position','like','%'.$module.'%']])->order('sort desc,id asc')->select();
            foreach($list as $k=>$vo){
                \think\facade\Hook::add($vo['type'],[
                    [$vo['controller'],$vo['action']]
                ]);
            }
        } catch (\Exception $e) {
            Log::info(json_encode($e->getMessage()));
        }
    }
}
if (!function_exists('doZfAction')) {
    function doZfAction($name='',$params=''){
        try {
            Hook::listen($name,$params);
        } catch (\Exception $e) {
            Log::info(json_encode($e->getMessage()));
        }
    }
}
/**
 * 判断密码重点级别
 * @return [type] [description]
 */
if (!function_exists('judgepassword')) {
    function judgepassword($code){
        $score = 0;
        if(!empty($code)){
            $str = $code;
        } else{
            $str = '';
        }
        if(preg_match("/[0-9]+/",$str)){
            $score ++;
        }
        if(preg_match("/[0-9]{3,}/",$str)){
            $score ++;
        }
        if(preg_match("/[a-z]+/",$str)){
            $score ++;
        }
        if(preg_match("/[a-z]{3,}/",$str)){
            $score ++;
        }
        if(preg_match("/[A-Z]+/",$str)){
            $score ++;
        }
        if(preg_match("/[A-Z]{3,}/",$str)){
            $score ++;
        }
        if(preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]+/",$str)){
            $score += 2;
        }
        if(preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]{3,}/",$str)){
            $score ++ ;
        }
        if(strlen($str) >= 10){
            $score ++;
        }
        if($score<=3){
            return '弱';
        }
        if($score>3 && $score<=6){
            return '中等';
        }
        if($score>6 && $score<=8){
            return '强';
        }
        if($score>8){
            return '极强';
        }

    }
}

//获取模板data数据
// if (!function_exists('get_theme_config')) {
//     function get_theme_config($theme_name){
//         $file = './theme/index/'.$theme_name.'/data.php';
//         if(file_exists($file)){
//             $data = require $file;
//         }else{
//             $data = [];
//         }
//         foreach($data as $k=>$vo){
//             $data[$k] = base64_decode($vo);
//         }
//         return $data;
//     }
// }


if (!function_exists('zf_wjt_rule')) {
    function zf_wjt_rule($url='',$type=''){
        return $url;
    }
}

if (!function_exists('get_url')) {
    function get_url()
    {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
    }
}

//20220610新增
if (!function_exists('zf_jump_error')) {
    function zf_jump_error($msg,$path){
        echo $msg;
        echo "<a href='/'>返回上一级</a>";
    }
}
if(!function_exists('is_weixin')){
    function is_weixin(){
        if ( strpos($_SERVER['HTTP_USER_AGENT'],
                'MicroMessenger') !== false ) {
            return true;
        }
        return false;
    }
}

if(!function_exists('is_top_menu_cur')){
    function is_top_menu_cur($vo=[],$_two_menu=[],$menu_type='',$cid=''){
        $is = false;
        if($vo['item_id']==$cid  && $vo['action']==$menu_type){
            return true;
        }
        foreach($_two_menu as $kk=>$vv){
            if($vv['action']==$menu_type && $cid==$vv['item_id']){
                $is = true;
                break;
            }
        }
        return $is;
    }
}
//判断数组是否有某一个键,没有则返回默认值
if(!function_exists('isset_arr_key')){
    function isset_arr_key($arr,$key,$default=''){
        if(isset($arr[$key])){
            return $arr[$key];
        }else{
            return $default;
        }
    }
}

/**
 * 20220722新增
 * 返回消息
 * 20230217 新增version字段
 * 20230830 新增字段encry  是否加密
 */
if (!function_exists('jsonPro')) {
    function jsonPro($data,$msg,$code=1,$url='',$version='',$encry=0){
        echo json_encode(array('code'=>$code,'data'=> $data,"msg" => $msg, "url" => $url,'version'=>$version,'encry'=>$encry));exit;
    }
}


// 20220812 新增
//判断是否为ie浏览器
if (!function_exists('is_ie_browser')) {
    function is_ie_browser() {
        $userbrowser = $_SERVER['HTTP_USER_AGENT'];
        if(preg_match( '/MSIE/i', $userbrowser ) ) {
            return true;
        }
        if(strpos($userbrowser,"Triden")) {
            return true;
        }
        return false;
    }
}

// 前台路由
// 230926重写
if (!function_exists('route_home')) {
    // function route_home($type,$router,$controller,$menu_type='',$template='default') {
    //     Route::rule($router, 'index/index/hook',$type)
    //     ->middleware(app\common\middleware\Check::class)
    //     ->append(['controller'=>$controller,'menu_type'=>$menu_type,'template'=>$template]);
    // }
    function route_home($arr=[]) {
        foreach($arr as $k=>$vo){
            $type = isset($vo[0])?$vo[0]:'';
            $router = isset($vo[1])?$vo[1]:'';
            $controller = isset($vo[2])?$vo[2]:'';
            $menu_type = isset($vo[3])?$vo[3]:'';
            $template = isset($vo[4])?$vo[4]:'default';
            $lang = isset($vo[5])?$vo[5]:'';
            $parm_str = isset($vo[6])?$vo[6]:'zz=1';
            Route::rule($router, 'index/index/hook?'. $parm_str,$type)
                 ->middleware(app\common\middleware\Check::class)
                 ->append(['controller'=>$controller,'menu_type'=>$menu_type,'template'=>$template,'lang'=>$lang]);
        }
    }
}

/**
 * // 20220927新增
 * 获取html内容中指定的字数的内容
 * @param string $str 内容
 * @param int $num 字数
 */
if (!function_exists('html_out_par')) {
    function html_out_par($str,$num){
        if(function_exists('htmlspecialchars_decode'))
            $str=htmlspecialchars_decode($str);
        else
            $str=html_entity_decode($str);
        $str = stripslashes($str);
        return msubstr(preg_replace ( "/(?!<(sup|\/sup).*?>)(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", ' ', $str),0,$num,'utf-8',false);
    }
}
/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
if (!function_exists('msubstr')) {
    function msubstr($str, $start=0, $length=100, $suffix=true,$showall=0, $charset="utf-8") {
        $oldlen=strlen($str);
        if(function_exists("mb_substr"))
            $slice = mb_substr($str, $start, $length, $charset);
        elseif(function_exists('iconv_substr')) {
            $slice = iconv_substr($str,$start,$length,$charset);
            if(false === $slice) {
                $slice = '';
            }
        }else{
            $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("",array_slice($match[0], $start, $length));
        }
        if($oldlen<=strlen($slice)){
            $suffix=false;
        }
        if($showall==1){
            return $suffix ? $slice.'... <a href="javascript:;" class="f-yellow">显示全部</a>' : $slice;
        }else{
            return $suffix ? $slice.'...' : $slice;
        }
    }
}
// 20221115新增  修改env文件
//只支持一级
// env_edit(['app_debug'=>'true']);
if (!function_exists('env_edit')) {
    function env_edit($arr = []){
        if(!is_array($arr)) return false;
        $data = file_get_contents('.env');
        $_arr = explode(PHP_EOL,$data);
        $ret_arr = [];
        foreach($_arr as $k=>$vo){
            $_arr_item = explode('=',$vo);
            if(isset($_arr_item[1])){
                $ret_arr[strtoupper(trim($_arr_item[0]))] = trim($_arr_item[1]);
            }
        }
        $str = '';
        foreach($arr as $k=>$vo){
            $ret_arr[strtoupper($k)]=$vo;
        }
        $i = 1;
        foreach($ret_arr as $k=>$vo){
            if($i>=(count($ret_arr))){
                $str.= $k.'='.$vo;
            }else{
                $str.= $k.'='.$vo.PHP_EOL;
            }
            $i++;
        }
        file_put_contents('.env', $str);
        return true;
    }
}


/**
 * 根据文件后缀获取其mine类型
 *20221118新增
 * @param string $extension
 * @return string
 */
function get_mimetype($extension) {
    $ct = [
        'htm'=>'text/html',
        'html'=>'text/html',
        'txt'=>'text/plain',
        'asc'=>'text/plain',
        'bmp'=>'image/bmp',
        'gif'=>'image/gif',
        'jpeg'=>'image/jpeg',
        'jpg'=>'image/jpeg',
        'jpe'=>'image/jpeg',
        'png'=>'image/png',
        'ico'=>'image/vnd.microsoft.icon',
        'mp4'=>'video/mp4',
        'mpeg'=>'video/mpeg',
        'mpg'=>'video/mpeg',
        'mpe'=>'video/mpeg',
        'qt'=>'video/quicktime',
        'mov'=>'video/quicktime',
        'avi'=>'video/x-msvideo',
        'wmv'=>'video/x-ms-wmv',
        'mp2'=>'audio/mpeg',
        'mp3'=>'audio/mpeg',
        'rm'=>'audio/x-pn-realaudio',
        'ram'=>'audio/x-pn-realaudio',
        'rpm'=>'audio/x-pn-realaudio-plugin',
        'ra'=>'audio/x-realaudio',
        'wav'=>'audio/x-wav',
        'css'=>'text/css',
        'zip'=>'application/zip',
        'pdf'=>'application/pdf',
        'doc'=>'application/msword',
        'bin'=>'application/octet-stream',
        'exe'=>'application/octet-stream',
        'class'=>'application/octet-stream',
        'dll'=>'application/octet-stream',
        'xls'=>'application/vnd.ms-excel',
        'ppt'=>'application/vnd.ms-powerpoint',
        'wbxml'=>'application/vnd.wap.wbxml',
        'wmlc'=>'application/vnd.wap.wmlc',
        'wmlsc'=>'application/vnd.wap.wmlscriptc',
        'dvi'=>'application/x-dvi',
        'spl'=>'application/x-futuresplash',
        'gtar'=>'application/x-gtar',
        'gzip'=>'application/x-gzip',
        'js'=>'application/x-javascript',
        'swf'=>'application/x-shockwave-flash',
        'tar'=>'application/x-tar',
        'xhtml'=>'application/xhtml+xml',
        'au'=>'audio/basic',
        'snd'=>'audio/basic',
        'midi'=>'audio/midi',
        'mid'=>'audio/midi',
        'm3u'=>'audio/x-mpegurl',
        'tiff'=>'image/tiff',
        'tif'=>'image/tiff',
        'rtf'=>'text/rtf',
        'wml'=>'text/vnd.wap.wml',
        'wmls'=>'text/vnd.wap.wmlscript',
        'xsl'=>'text/xml',
        'xml'=>'text/xml',
    ];
    return isset($ct[strtolower($extension)]) ? $ct[strtolower($extension)] : '';
}

/**
 * 检测上传图片是否包含有非法代码
 * @return mixed
 */
if (!function_exists('check_illegal')) {
    function check_illegal($image)
    {
        try {
            if (file_exists($image)) {
                $resource = fopen($image, 'rb');
                $fileSize = filesize($image);
                fseek($resource, 0);
                $hexCode = fread($resource, $fileSize);
                fclose($resource);
                if (preg_match('#__HALT_COMPILER()#i', $hexCode) || preg_match('#/script>#i', $hexCode) || preg_match('#<([^?]*)\?php#i', $hexCode) || preg_match('#<\?\=(\s+)#i', $hexCode)) {
                    return false;
                }
            }
        } catch (\Exception $e) {}

        return true;
    }
}


/**
 * 20230217新增
 * addons自定义输出日志
 * $plugin_name=''   插件名
 * $data=[]   数据
 * $state=''   状态(success/fail/自定义)
 * $save_log_tag=''    log/后的自定义文件夹,避免被他人恶意通过http获取数据
 * $filename=''   保存的文件名,如果为空则根据规则生成
 *
 */
if (!function_exists('logOutputAddons')) {
    function logOutputAddons($plugin_name='',$data=[],$state='',$save_log_tag='',$filename='') {
        //数据类型检测
        if (is_array($data)) {
            $data = json_encode($data);
        }elseif(is_string($data)){
            $data = $data;
        }
        if($save_log_tag==''){
            $diy_save_dir = '/log/';
        }else{
            $diy_save_dir = '/log/'.$save_log_tag.'/';
        }
        if($state==''){
            $dir = './addons/'.$plugin_name.$diy_save_dir.date("Ym").'/';
        }else{
            $dir = './addons/'.$plugin_name.$diy_save_dir.$state.'/'.date("Ym").'/';
        }
        if(!is_dir($dir)) {
            mkdir($dir, 0777,true);
        }
        if($filename==''){
            $filename = $dir.date("d").'_'.date('H').'_'.md5($plugin_name).".log";
        }else{
            $filename = $dir.$filename.".log";
        }
        $str = date("Y-m-d H:i:s")."   $data";
        $server = request()->server();
        $str .= '  HTTP_USER_AGENT: '.$server['HTTP_USER_AGENT'];
        $str .= '  IP: '.$server['REMOTE_ADDR']."\n";
        file_put_contents($filename, $str, FILE_APPEND|LOCK_EX);
        return null;
    }
}
/**
 * 20230227
 * 字符串在另一个字符串中是否含有
 */
if(!function_exists('is_str_find')){
    function is_str_find($str='',$find_str='') {
        if(strpos($str,$find_str) !== false){
            return true;
        }else{
            return false;
        }
    }
}
/**
 * 20230328新增
 * 发送邮件
 */
if(!function_exists('send_email')){
    function send_email($address,$email_content=[],$debug=0){
        date_default_timezone_set("PRC");
        $mail = new PHPMailer(true);
        $data = ZFC("email_config",'db','arr');
        if(!isset($data['host']) || !isset($data['send_nickname']) || !isset($data['send_email']) || !isset($data['password']) || !isset($data['secure']) || !isset($data['e_number'])  || $data['host']=='' || $data['send_email']==''  || $data['password']=='' || $data['secure']=='' || $data['e_number']==''){
            return jserror('邮件参数设置不完整');
        }

        try {
            if($debug==1){
                $mail->SMTPDebug = 2;   // Enable verbose debug output
            }
            $mail->isSMTP();
            $mail->CharSet = "UTF-8";
            $mail->Host       = $data['host'];  // Specify main and backup SMTP servers
            $mail->SMTPAuth   = true;             // Enable SMTP authentication
            $mail->Username   = $data['send_email'];         // SMTP username
            $mail->Password   = $data['password'];       // SMTP password
            $mail->SMTPSecure = $data['secure'];           // Enable TLS encryption, `ssl` also accepted
            $mail->Port       = $data['e_number'];          // TCP port to connect to
            //Recipients
            $mail->setFrom($data['send_email'], $data['send_nickname']);//发送方
            $address_arr = explode(',',$address);
            foreach($address_arr as $k=>$vo){
                $mail->addAddress($vo, $vo);     // Add a recipient
            }
            $mail->isHTML(true);                                  // Set email format to HTML
            if(isset($email_content['title'])){
                $mail->Subject = $email_content['title'];
            }else{
                $mail->Subject = '来自'.$data['send_nickname'].'的邮件';
            }
            // $subject = $data['subject'];
            // $mail->Subject  = "=?UTF-8?B?".base64_encode($subject)."?=";
            if(isset($email_content['body'])){
                $mail->Body    = $email_content['body'];
                $mail->AltBody = $email_content['body'];//该属性的设置是在邮件正文不支持HTML的备用显示
            }else{
                $mail->Body    = '来自'.$data['send_nickname'].'的邮件';
                $mail->AltBody = '来自'.$data['send_nickname'].'的邮件';//该属性的设置是在邮件正文不支持HTML的备用显示
            }
            $mail->send();
            return 'ok';
        } catch (Exception $e) {
            return  $mail->ErrorInfo;
        }
    }
}

/**
 * 20230228
 * 高精度计算加减乘除
 */
if(!function_exists('num_compute')){
    function num_compute($num1=1,$type='+',$num2=1,$jd=2){
        if($type=='+'){
            return bcadd($num1,$num2,$jd);
        }elseif($type=='-'){
            return bcsub($num1,$num2,$jd);
        }elseif($type=='*'){
            return bcmul($num1,$num2,$jd);
        }elseif($type=='/'){
            return bcdiv($num1,$num2,$jd);
        }
    }
}
/**
 * 20230414
 * 输出当前程序运行所用的时间
 */
if(!function_exists('zf_runtime')){
    function zf_runtime($type,$starttime=[]){
        if($type=='start'){
            $starttime = explode(' ',microtime());
            echo microtime().'<br>';
            return $starttime;
        }elseif($type=='end'){
            $endtime = explode(' ',microtime());
            $thistime = $endtime[0]+$endtime[1]-($starttime[0]+$starttime[1]);
            $thistime = round($thistime,3);
            echo "执行耗时：".$thistime." 秒。";die;
        }else{
            return '参数错误';
        }
    }
}
/**
 * 20230426
 * 跳转到自己设置的站点
 * 不填写http或https   ------>  //dev.zfcmsx2.90ckm.com/bbs_cate/4.html
 * 填写http后面加了/   ------>  http://dev.zfcmsx2.90ckm.com/bbs_cate/4.html
 * 填写http后面没加/   ------>  http://dev.zfcmsx2.90ckm.com/bbs_cate/4.html
 *
 * 当前链接 http://dev2.zfcmsx2.90ckm.com   ----> 跳转到https://dev2.zfcmsx2.90ckm.com
 *
 */
if(!function_exists('zf_to_site_url')){
    function zf_to_site_url(){
        $site_url = ZFC("webconfig.site_host");
        if($site_url && $site_url!='' && !is_str_find(get_url(),$site_url)){
            if(substr($site_url,-1)=='/'){
                $site_url = substr($site_url,0,-1);
            }
            $url = $site_url.request()->url();
            if(!is_str_find($url,'http')){
                $url = '//'.$url;
            }
            Header("Location: $url");die;
        }
    }
}
/**
 * 20230510
 * 检测插件模板是否一致
 * [插件,模板]
 */
if(!function_exists('plugin_update_check_show')){
    function plugin_update_check_show(){
        $db_theme_count = db('plugin')->where([['type','=','theme'],['status','in','1,2']])->group('plugin_name')->count();
        $db_plugin_count = db('plugin')->where([['type','=','plugin'],['status','in','1,2']])->group('plugin_name')->count();
        $theme_count = 0;
        $plugin_count = 0;

        $dir_arr = scandir('./theme');
        $theme_count = 0;
        foreach ($dir_arr as $k => $vo) {
            if($vo!='.' && $vo!='..' && $vo!='.DS_Store'  && is_dir('./theme/'.$vo) && is_file('./theme/'.$vo.'/plugin_info.php')){
                $_plugin_info = require './theme/'.$vo.'/plugin_info.php';
                if($_plugin_info['plugin_name']==$vo){
                    $theme_count++;
                }
            }
        }
        $dir_arr = scandir('./addons');
        $plugin_count = 0;
        foreach ($dir_arr as $k => $vo) {
            if($vo!='.' && $vo!='..' && $vo!='.DS_Store' && is_dir('./addons/'.$vo) && is_dir('./addons/'.$vo.'/config') && is_file('./addons/'.$vo.'/config/plugin_info.php')){
                $_plugin_info = require './addons/'.$vo.'/config/plugin_info.php';
                if($_plugin_info['plugin_name']==$vo){
                    $plugin_count++;
                }
            }
        }

        $ret_data = [true,true];
        if($db_plugin_count==$plugin_count){
            $ret_data[0] = false;
        }
        if($db_theme_count==$theme_count){
            $ret_data[1] = false;
        }
//         echo "<br> 插件db".$db_plugin_count;
//         echo "<br> 本地插件".$plugin_count;
//         echo "<br>模板db ".$db_theme_count;
//         echo "<br> 本地模板".$theme_count;
//         die;
        return $ret_data;
    }
}
/**
 * 20230511
 * 判断文件或文件夹是否可写.
 * @param  string  $file  文件或目录
 * @return bool
 */
if(!function_exists('is_really_writable')){
    function is_really_writable($file){
        if (DIRECTORY_SEPARATOR === '/') {
            return is_writable($file);
        }
        if (is_dir($file)) {
            $file = rtrim($file, '/') . '/' . md5(mt_rand());
            if (($fp = @fopen($file, 'ab')) === false) {
                return false;
            }
            fclose($fp);
            @chmod($file, 0777);
            @unlink($file);

            return true;
        } elseif (!is_file($file) or ($fp = @fopen($file, 'ab')) === false) {
            return false;
        }
        fclose($fp);
        return true;
    }
}
/**
 * 20230517
 * 创建uuid
 */
if(!function_exists('create_uuid')){
    function create_uuid() {
        $bytes = random_bytes(16);
        $uuid = sprintf('%08s-%04s-%04x-%04x-%12s',
            bin2hex(substr($bytes, 0, 4)),
            bin2hex(substr($bytes, 4, 2)),
            hexdec(bin2hex(substr($bytes, 6, 2))) & 0x0fff | 0x4000,
            hexdec(bin2hex(substr($bytes, 8, 2))) & 0x3fff | 0x8000,
            bin2hex(substr($bytes, 10, 6))
        );
        return  $uuid;
    }
}

/**
 * 20230824
 * 组件引用
 */
/**
{:widget_st('layui','css')}
{:widget_st('admin','css')}
{:widget_st('jq','js')}
{:widget_st('laydate','js')}
{:widget_st('webuploader')}
{:widget_st('layui','js')}
{:widget_st('common','js')}
{:widget_st('ueditor')}
{:widget_st('bootstrap')}
{:widget_st('input-tag')}
{:widget_st($tpl_static.'js/jquery-1.11.0.min.js','diy_js')}
{:widget_st($tpl_static.'css/media.css','diy_css')}
{:widget_st('input-select')}

 */
if(!function_exists('widget_st')){
    function widget_st($name='',$type='widget') {
        $_static = config('template.tpl_replace_string.__STATIC__');
        $_v = ZFC('webconfig.js_version_type');
        // $_host_api = ZFC('version.api_domain','file');
        if($_v=='time'){
            $v = 'v='.time();
        }elseif($_v=='ymd'){
            $v = 'v='.date('Ymd');
        }elseif($_v=='ym'){
            $v = 'v='.date('Ym');
        }elseif($_v=='y'){
            $v = 'v='.date('Y');
        }else{
            $v = 'v=1';
        }
        $_is_web = ZFC('webconfig.is_web_static');
        $_web_host = ZFC('webconfig.web_static_host');




        if(in_array($name,['admin','common'])){
            $_static = $_static.'/system';
        }else{
            $_static = $_static.'/style';
            if($_is_web==1 && $_web_host!=''){
                $_static = $_web_host.'/zfcms/style';
            }
        }


        if($type=='css'){
            if(in_array($name,['layui'])){
                return "<link rel='stylesheet' href='$_static/layui/css/layui.css?$v' media='all'>\n";
            }
            if(in_array($name,['admin'])){
                return "<link rel='stylesheet' href='$_static/style/admin.css?$v' media='all'>\n";
            }
            if(in_array($name,['tailwind'])){
                return "<link rel='stylesheet' href='$_static/tailwind/tailwind.min.css?$v' media='all'>\n";
            }
        }
        if($type=='js'){
            if(in_array($name,['jq183'])){
                return "<script type='text/javascript' src='$_static/jquery-1.8.3/jquery-1.8.3.min.js?$v'></script>\n";
            }
            if(in_array($name,['jq351','jq'])){
                return "<script type='text/javascript' src='$_static/jquery-3.5.1/jquery-3.5.1.min.js?$v'></script>\n";
            }
            if(in_array($name,['layui'])){
                return "<script type='text/javascript' src='$_static/layui/layui.js?$v'></script>\n";
            }
            if(in_array($name,['laydate'])){
                return "<script type='text/javascript' src='$_static/laydate/laydate.js?$v'></script>\n";
            }
            if(in_array($name,['layer'])){
                return "<script type='text/javascript' src='$_static/layer/layer.js?$v'></script>\n";
            }
            if(in_array($name,['common'])){
                $tan_type = ZFC('webconfig.js_tan_type');
                if($tan_type!='newwindow'){
                    $tan_type = '';
                }
                return "<script type='text/javascript' src='$_static/common.js?$v&tan_type=$tan_type'></script>\n";
            }
        }
        if($type=='widget'){
            if(in_array($name,['webuploader'])){
                return "<link rel='stylesheet' type='text/css' href='$_static/webuploader/webuploader.css?$v'>\n<script type='text/javascript' src='$_static/webuploader/webuploader.js?$v'></script>\n";
            }
            if(in_array($name,['ueditor'])){
                $str = '';
                $str .= "<script type='text/javascript' src='$_static/ueditor/ueditor.config.js?$v'></script>\n<script type='text/javascript' src='$_static/ueditor/ueditor.all.js?$v'></script>\n<link rel='stylesheet' href='$_static/ueditor/themes/default/css/ueditor.css?$v' media='all'>\n";
                $str .= "<script type='text/javascript' src='$_static/xiumi/xiumi-ue-dialog-v5.js?$v'></script>\n<link rel='stylesheet' href='$_static/xiumi/xiumi-ue-v5.css?$v' media='all'>\n";
                return $str;
            }
            if(in_array($name,['bootstrap','bootstrap334'])){
                return "<script src='$_static/bootstrap/bootstrap-3.3.4.js?$v'></script>\n<link rel='stylesheet' href='$_static/bootstrap/bootstrap-3.3.4.css?$v'>\n";
            }
            if(in_array($name,['input-tag'])){
                return "<link rel='stylesheet' href='$_static/input-tag/inputTag.css?$v' media='all'>\n";
            }
            if(in_array($name,['viewer'])){
                return "<link rel='stylesheet' type='text/css' href='$_static/viewer/viewer.mini.css?$v'>\n<script type='text/javascript' src='$_static/viewer/viewer.mini.js?$v'></script>\n";
            }
            if(in_array($name,['tinymce'])){
                return "<script src='$_static/tinymce515/tinymce.min.js?$v'></script>\n";
                //<script src='https://cdn.bootcdn.net/ajax/libs/tinymce/5.7.1/jquery.tinymce.min.js?$v'></script>\n
            }
            if(in_array($name,['meditor'])){
                return "<link rel='stylesheet' type='text/css' href='$_static/meditor/css/editormd.css?$v'>\n<script src='$_static/meditor/editormd.js?$v'></script>\n";
            }
            if(in_array($name,['wangEditor'])){
                return "<script src='$_static/wangEditor/wangEditor.min.js?$v'></script>\n";
            }
            if(in_array($name,['vditor'])){
                // return "<link rel='stylesheet' href='$_static/vditor/dist/index.css?$v' />\n<script src='$_static/vditor/dist/index.min.js?$v'></script>\n";
                // <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vditor/dist/index.css" />
                // <script src="https://cdn.jsdelivr.net/npm/vditor/dist/index.min.js"></script>
            }
            if(in_array($name,['autosize'])){
                return "<script src='$_static/autosize/dist/autosize.js?$v'></script>\n";
            }
            if(in_array($name,['echarts'])){
                return "<script src='$_static/echarts/echarts.min.js?$v'></script>\n";
            }
            if(in_array($name,['input-select'])){
                return "<script src='$_static/input-select/selectInput.js?$v'></script>\n";
            }
            if(in_array($name,['fcup'])){
                return "<script src='$_static/fcup/fcup.min.js?$v'></script>\n";
            }
            if(in_array($name,['ztree'])){
                return "<link rel='stylesheet' href='$_static/ztree/css/zTreeStyle/zTreeStyle.css?$v' type='text/css'>\n<script type='text/javascript' src='$_static/ztree/js/jquery.ztree.core.min.js?$v'></script>\n";
            }
            //裁剪图片
            if(in_array($name,['cropper'])){
                return "<link rel='stylesheet' href='$_static/cropper1.5.12/cropper.min.css' type='text/css'>\n<script src='$_static/cropper1.5.12/cropper.min.js'></script>\n";
            }
            if(in_array($name,['sortable'])){
                return "<script src='$_static/sortable1.14.0/Sortable.min.js'></script>\n";
            }
        }

        if($type=='diy_css'){
            return "<link rel='stylesheet' href='$name?$v' media='all'>\n";
        }
        if($type=='diy_js'){
            return "<script type='text/javascript' src='$name?$v'></script>\n";
        }
    }
}
/**
 * 20230829
 * 处理加密数据,返回原始数组
 */
if(!function_exists('deal_post_message')){
    function deal_post_message($data){
        if(!is_array($data)){
            $str = aes_decrypt($data,'1234567890123456','1234567890123450');
            $data = json_decode($str,true);
            return $data;
        }
        return $data;
    }
}


/**
 * 20230829
 * 加密返回
 * 20230830新增字段$url ,$version,$encry
 */
if(!function_exists('jsonProJm')){
    function jsonProJm($arr,$msg='',$code='',$url = '',$version='',$encry=0){
        if($encry==1){
            $jm = aes_encrypt(json_encode($arr),'1234567890123456','1234567890123450');
        }else{
            $jm = $arr;
        }
        return jsonPro($jm,$msg,$code,$url,$version,$encry);
    }
}
/**
 * 20230829
 * aes加密
 */
if(!function_exists('aes_encrypt')){
    function aes_encrypt($plainText,$keyStr='1234567890123456',$ivStr='1234567890123450') {
        try {
            $key = utf8_encode($keyStr);
            $iv = utf8_encode($ivStr);
            $cipherText = openssl_encrypt($plainText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
            return strtoupper(bin2hex($cipherText));
        } catch (\Exception $e) {
            return "aes encode error: ".$e->getMessage();
        }
    }
}
/**
 * 20230829
 * aes解密
 */
if(!function_exists('aes_decrypt')){
    function aes_decrypt($encrypted,$keyStr='1234567890123456',$ivStr='1234567890123450') {
        try {
            $key = utf8_encode($keyStr);
            $iv = utf8_encode($ivStr);
            // dd($encrypted);
            $encryptedHexStr = hex2bin($encrypted);
            $decrypt = openssl_decrypt($encryptedHexStr, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
            return $decrypt;
        } catch (\Exception $e) {
            return "aes encode error: ".$e->getMessage();
        }
    }
}
/**
 * 20230919
 * 增加通知
 *
 * demo
$send_type='notice_bark';//为空则调用系统内置的
$content = [
'title'=>'你好',
'send_content'=>'这里是内容'
];
send_notice($content,$send_type);
 */
if(!function_exists('send_notice')){
    function send_notice($data=[],$send_type=''){
        try {
            $title = isset_arr_key($data,'title','通知消息');
            $send_content = isset_arr_key($data,'send_content','');
            if($send_type==''){
                $notice_type = ZFC('webconfig.notice_type');
            }else{
                $notice_type = $send_type;
            }
            if($notice_type==''){
                return false;
            }
            if($notice_type=='notice_bark'){
                $url = ZFC('webconfig.notice_bark');
                if($url==''){
                    save_exception('send_notice','未配置',['type'=>'notice_bark','msg'=>'未配置','content'=>['data'=>$data,'send_type'=>$notice_type]]);
                    return false;
                }
                //判断$url最后一个字符是否是/
                if(substr($url,-1)=='/'){
                    $url = substr($url,0,-1);
                }
                $url = $url.'/'.$send_content;
                $r = https_get($url);
                $_json_arr = json_decode($r,true);
                if(isset($_json_arr['message']) && $_json_arr['message']=='success'){
                    save_admin_log('send_notice','-1',['content'=>['data'=>$data,'send_type'=>$notice_type]],$notice_type);
                }else{
                    save_exception('send_notice','消息发送失败',['type'=>'notice_bark','msg'=>'消息发送失败','content'=>['data'=>$data,'send_type'=>$notice_type]]);
                }
            }elseif($notice_type=='notice_feishu'){
                $url = ZFC('webconfig.notice_feishu');
                if($url==''){
                    save_exception('send_notice','未配置',['type'=>'notice_feishu','msg'=>'未配置','content'=>['data'=>$data,'send_type'=>$notice_type]]);
                    return false;
                }
                // 构建请求数据
                $data = [
                    'msg_type' => 'text',
                    'content' => [
                        'text' => $send_content
                    ]
                ];
                // 发送 HTTP POST 请求
                $client = new Client();
                $response = $client->request('POST', $url, [
                    'json' => $data,
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ]
                ]);
                // 检查响应状态码并输出结果
                if ($response->getStatusCode() === 200) {
                    save_admin_log('send_notice','-1',['content'=>['data'=>$data,'send_type'=>$notice_type]],$notice_type);
                } else {
                    save_exception('send_notice','消息发送失败',['type'=>'notice_bark','msg'=>'消息发送失败','content'=>['data'=>$data,'send_type'=>$send_type,'err'=>"发送告警消息失败，错误代码：".$response->getStatusCode()]]);
                }


            }elseif($notice_type=='notice_dingding'){
                $url = ZFC('webconfig.notice_dingding');
                if($url==''){
                    save_exception('send_notice','未配置',['type'=>'notice_dingding','msg'=>'未配置','content'=>['data'=>$data,'send_type'=>$notice_type]]);
                    return false;
                }
                $msg = [
                    'msgtype' => 'text',//这是文件发送类型，可以根据需求调整
                    'text'    => [
                        'content' => $send_content,
                    ],
                ];
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($msg));
                curl_setopt($curl, CURLOPT_HEADER, 0);
                curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                $r = curl_exec($curl);
                curl_close($curl);
                $_json_arr = json_decode($r,true);
                if(isset($_json_arr['errmsg']) && $_json_arr['errmsg']=='ok'){
                    save_admin_log('send_notice','-1',['content'=>['data'=>$data,'send_type'=>$notice_type]],$notice_type);
                }else{
                    save_exception('send_notice','消息发送失败',['type'=>'notice_bark','msg'=>'消息发送失败','content'=>['data'=>$data,'send_type'=>$notice_type]]);
                }
            }elseif($notice_type=='notice_qiwei'){
                $url = ZFC('webconfig.notice_qiwei');
                if($url==''){
                    save_exception('send_notice','未配置',['type'=>'notice_qiwei','msg'=>'未配置','content'=>['data'=>$data,'send_type'=>$notice_type]]);
                    return false;
                }
                $msg = [
                    'msgtype' => 'text',//这是文件发送类型，可以根据需求调整
                    'text'    => [
                        'content' => $send_content,
                    ],
                ];
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($msg));
                curl_setopt($curl, CURLOPT_HEADER, 0);
                curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                $r = curl_exec($curl);
                curl_close($curl);
                $_json_arr = json_decode($r,true);
                if(isset($_json_arr['errmsg']) && $_json_arr['errmsg']=='ok'){
                    save_admin_log('send_notice','-1',['content'=>['data'=>$data,'send_type'=>$notice_type]],$notice_type);
                }else{
                    save_exception('send_notice','消息发送失败',['type'=>'notice_bark','msg'=>'消息发送失败','content'=>['data'=>$data,'send_type'=>$notice_type]]);
                }
            }elseif($notice_type=='email'){
                $email = ZFC('webconfig.notice_email');
                if($email==''){
                    save_exception('send_notice','接收邮箱未配置',['type'=>'notice_email','msg'=>'接收邮箱未配置','content'=>['data'=>$data,'send_type'=>$notice_type]]);
                    return false;
                }
                $email_content = [
                    'title'=>$title,
                    'body'=>$send_content,
                ];
                $r = send_email($email,$email_content);
                if($r=='ok'){
                    save_admin_log('send_notice','-1',['content'=>['data'=>$data,'send_type'=>$notice_type]],$notice_type);
                }else{
                    save_exception('send_notice','消息发送失败',['type'=>'notice_bark','msg'=>'消息发送失败','content'=>['data'=>$data,'send_type'=>$notice_type]]);
                }

            }else{
                save_exception('send_notice','发送类型不存在',['type'=>'notice_bark','msg'=>'发送类型不存在','content'=>['data'=>$data,'send_type'=>$notice_type]]);
            }
        } catch (\Exception $e) {
            save_exception('send_notice','异常',['type'=>'notice_bark','msg'=>'发送类型不存在','content'=>['data'=>$data,'send_type'=>$send_type,'exception'=>$e->getMessage()]]);
        }
    }
}
/**
 * 转换模板的meta key数据
 * 20231018
 */
if(!function_exists('change_meta_data')){
    function change_meta_data($data=[],$meta_key='',$default=''){
        //判断meta_key是否包含了meta[
        if(is_str_find($meta_key,'meta[')){
            $meta_key_arr = explode('meta[',$meta_key);
            if(!isset($meta_key_arr[1])){
                return $default;
            }
            $meta_key_arr = explode(']',$meta_key_arr[1]);
            if(!isset($meta_key_arr[0])){
                return $default;
            }
            $meta_key = $meta_key_arr[0];
            if(isset($data['meta'][$meta_key])){
                return $data['meta'][$meta_key];
            }else{
                return $default;
            }
        }else{
            if(isset($data[$meta_key])){
                return $data[$meta_key];
            }else{
                return $default;
            }
        }
    }
}
/**
 * 更新meta_data中的某个数据中的某个字段
 * 20231018
 * update_meta_data('category','2','bd_sl','99');
 * update_meta_data('category','2','aa','1');

 */
if(!function_exists('update_meta_data')){
    function update_meta_data($tb,$post_id,$key,$val=''){
        $is = db('meta_data')->where([['tb','=',$tb],['post_id','=',$post_id]])->find();
        if(!$is){
            $meta_data['tb'] = $tb;
            $meta_data['post_id'] = $post_id;
            $meta_data['ctime'] = time();
            $meta_data['utime'] = time();
            $meta_data['token'] = time();
            $meta_data['meta_data'] = json_encode([$key=>$val]);
            $res = ZFTB('meta_data')->insert($meta_data);
        }else{
            $meta_data_arr = json_decode($is['meta_data'],true);
            $meta_data_arr[$key] = $val;
            $res =ZFTB('meta_data')->where(['meta_id'=>$is['meta_id']])->update(['meta_data'=>json_encode($meta_data_arr),'utime'=>time()]);
        }
        if($res){
            return true;
        }
        return false;
    }
}
/**
 * 读取meta_data中的某个数据中的某个字段
 * 20231018
 * $key为空,则返回数组
 *
 * echo get_meta_data('category','1',$key='bd_sl',$def='0');
 * echo get_meta_data('category2','1',$key='bd_sl',$def='0');
 * echo get_meta_data('category','1',$key='bd_sl3',$def='0');
 * echo get_meta_data('category','2',$key='bd_sl',$def='0');
 * // dd(get_meta_data('category','2','',$def='0'));
 */
if(!function_exists('get_meta_data')){
    function get_meta_data($tb,$post_id,$key='',$def=''){
        $is = db('meta_data')->where([['tb','=',$tb],['post_id','=',$post_id]])->find();
        if(!$is){
            return $def;
        }else{
            $meta_data_arr = json_decode($is['meta_data'],true);
            if($key==''){
                return $meta_data_arr;
            }elseif(isset($meta_data_arr[$key])){
                return $meta_data_arr[$key];
            }
            return  $def;
        }
    }
}
/**
 * 后台中meta数据处理-新增
 * 20231018
 */
if(!function_exists('deal_meta_data_add')){
    function deal_meta_data_add($tb,$data=[]){
        if(isset($data['_temp_arr_key'])){
            $_temp_arr_key =array_unique($data['_temp_arr_key']);
            foreach ($_temp_arr_key as $k => $vo) {
                if(!isset($data[$vo])){
                    $data[$vo] = '';
                }
            }
            unset($data['_temp_arr_key']);
        }
        $ret_data = apply_filters('filter_data_add',['tb'=>$tb,'data'=>$data],'array');
        $data = $ret_data['data'];
        do_action('deal_data_add',$tb,$data);
        //判断diy_url是否存在
        if(isset($data['meta']['diy_url'])){
            if(db('meta_data')->where([['tb','=',$tb],['diy_url','=',$data['meta']['diy_url']],['status','=',1]])->find() && $data['meta']['diy_url']!=''){
                return ZFRetMsg(false,'','自定义URL已存在,请换个链接');
            }
            //判断是否字符中有空格
            if(strpos($data['meta']['diy_url'], ' ') !== false){
                return ZFRetMsg(false, '', '自定义URL不能包含空格');
            }
            //首字符不能为/,不能包含.
            if(strpos($data['meta']['diy_url'], '/') === 0 || strpos($data['meta']['diy_url'], '.') !== false){
                return ZFRetMsg(false, '', '自定义URL不能以/开头,不能包含.号');
            }
        }
        Db::startTrans();
        try {
            if(isset($data['meta']) && is_array($data['meta'])){
                $meta_data['meta_data'] = json_encode($data['meta']);
                $_meta = $data['meta'];
                unset($data['meta']);
                $res = ZFTB($tb)->insertGetId($data);
                if(!$res){
                    Db::rollback();
                    return ZFRetMsg(false,'','新增失败');
                }
                $meta_data['tb'] = $tb;
                $meta_data['post_id'] = $res;
                $meta_data['ctime'] = time();
                $meta_data['token'] = time();
                $meta_data['diy_url'] = isset_arr_key($_meta,'diy_url','');
                $res = ZFTB('meta_data')->insert($meta_data);
                if(!$res){
                    Db::rollback();
                    return ZFRetMsg(false,'','新增失败');
                }
            }else{
                $res = ZFTB($tb)->insertGetId($data);
            }
            Db::commit();
            return ZFRetMsg($res,'新增成功','新增失败');
        }catch (Exception $e) {
            Db::rollback();
            return jserror($e->getMessage());
        }
    }
}
/**
 * 后台中meta数据处理-修改
 * 20231018
 */
if(!function_exists('deal_meta_data_edit')){
    function deal_meta_data_edit($tb,$data=[],$field_id='id'){
        if(isset($data['_temp_arr_key'])){
            $_temp_arr_key =array_unique($data['_temp_arr_key']);
            foreach ($_temp_arr_key as $k => $vo) {
                if(!isset($data[$vo])){
                    $data[$vo] = '';
                }
            }
            unset($data['_temp_arr_key']);
        }
        $ret_data = apply_filters('filter_data_edit',['tb'=>$tb,'data'=>$data],'array');
        $data = $ret_data['data'];
        do_action('deal_data_edit',$tb,$data);
        //判断diy_url是否存在
        if(isset($data['meta']['diy_url'])){
            if(db('meta_data')->where([['tb','=',$tb],['diy_url','=',$data['meta']['diy_url']],['status','=',1],['post_id','<>',$data[$field_id]]])->find() && $data['meta']['diy_url']!=''){
                return ZFRetMsg(false,'','自定义URL已存在,请换个链接');
            }
            //判断是否字符中有空格
            if(strpos($data['meta']['diy_url'], ' ') !== false){
                return ZFRetMsg(false, '', '自定义URL不能包含空格');
            }
            //首字符不能为/,不能包含.
            if(strpos($data['meta']['diy_url'], '/') === 0 || strpos($data['meta']['diy_url'], '.') !== false){
                return ZFRetMsg(false, '', '自定义URL不能以/开头,不能包含.号');
            }
        }
        Db::startTrans();
        try {
            if(isset($data['meta']) && is_array($data['meta'])){
                $meta_data['meta_data'] = json_encode($data['meta']);
                $_meta = $data['meta'];
                unset($data['meta']);
                if(!isset($data['token'])){
                    $data['token'] = time();
                }
                $res_main = ZFTB($tb)->where([$field_id=>$data[$field_id]])->update($data);
                if(!$res_main){
                    Db::rollback();
                    return ZFRetMsg(false,'','修改失败');
                }
                $meta_data['tb'] = $tb;
                $meta_data['post_id'] = $data[$field_id];
                $meta_data['utime'] = time();
                $meta_data['token'] = time();
                $meta_data['diy_url'] = isset_arr_key($_meta,'diy_url','');
                $is = ZFTB('meta_data')->where([['status','<>',9],['tb','=',$meta_data['tb']],['post_id','=',$meta_data['post_id']]])->find();
                if($is){
                    $res = ZFTB('meta_data')->where(['post_id'=>$meta_data['post_id']])->update($meta_data);
                }else{
                    $res = ZFTB('meta_data')->insert($meta_data);
                }
                if(!$res){
                    Db::rollback();
                    return ZFRetMsg(false,'','修改失败/没有内容修改');
                }
            }else{
                $res = ZFTB($tb)->where([$field_id=>$data[$field_id]])->update($data);
            }
            Db::commit();
            return ZFRetMsg($res,'修改成功','修改失败');
        }catch (Exception $e) {
            Db::rollback();
            return jserror($e->getMessage());
        }
    }
}
/**
 * 请求http,判断是否状态code
 * 20231031新增
 */
if(!function_exists('http_request_code')){
    function http_request_code($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpCode;
    }
}
/**
 * 字段显示模板
 * 20231106新增
 */
if(!function_exists('str_show_tpl')){
    function str_show_tpl($str,$is_logo = false,$logo_src = ''){
        $html_str = '';
        if($is_logo && $logo_src!=''){
            $html_str .= '<div class="img-box"><img src="'.$logo_src.'" alt=""></div>';
        }
        $html_str.='<div class="text-box">'.$str.'</div>';
        $str_style = '.inner-box{
                width: 500px;
                border: 1px dashed #ccc;
                padding:10px;
            }';
        $str_style = '
        .inner-box{
            width: 500px;
            padding:10px;
            border: 5px solid transparent;
            background: linear-gradient(white,white) padding-box,repeating-linear-gradient(-45deg, red 0, red 12.5%, transparent 0, transparent 25%, #58a 0, #58a 37.5%, transparent 0, transparent 50%) 0/5em 5em;
        }';
        $html = <<<EOL
        <html>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <head>
            <title></title>
        </head>
        <style type="text/css">
            .out-box{
                display: flex;
                justify-content: center;
                align-items: center;
                margin-top: -100px;
            }
            $str_style
            .img-box{
                display: flex;
                margin-bottom: 10px;
            }
            .inner-box img{
                margin:10px auto;
                height: 80px;
                width: 80px;
            }
            .text-box{
                text-align: center;
            }
        </style>
        <body class="out-box">
            <div class="inner-box">
                $html_str
            </div>
        </body>
        </html>
EOL;
        return $html;
    }
}
/**
 * seo模板
 * 20231107新增
 * seo_tpl(); //首页,系统默认
 * seo_tpl('theme_tpl',['t'=>'标题','k'=>'关键词','d'=>'描述']); //模板默认
 * seo_tpl('search',['t'=>'标题','k'=>'关键词','d'=>'描述']); //搜索默认
 * seo_tpl('post',['id'=>9999,'t'=>'标题','k'=>'关键词','d'=>'描述']); //id不存在,返回默认
 * seo_tpl('post',[]); //id不存在,返回默认
 * seo_tpl('post',['id'=>5,'t'=>'标题','k'=>'关键词','d'=>'描述']); //id存在,返回数据
 *
 * 使用方法
 * $this->assign('seo', seo_tpl()); //首页
 * $this->assign('seo', seo_tpl('category',['id'=>$cate_res['cid'],'t'=>$cate_res['name'],'k'=>$cate_res['name'],'d'=>$cate_res['summary']]));  //栏目
 * $this->assign('seo', seo_tpl('post',['id'=>$content['id'],'t'=>$content['title'],'k'=>$content['title'],'d'=>$content['summary']]));  //文章详情
 */
if(!function_exists('seo_tpl')){
    function seo_tpl($tb='',$content=['id'=>"",'t'=>'','d'=>'','k'=>'']){
        $lang = ZLANG;
        $title = isset_arr_key($content,'t','');
        $description = isset_arr_key($content,'d','');
        $keywords = isset_arr_key($content,'k','');
        $id = isset_arr_key($content,'id','');
        if($lang!=''){
            $lang = '_'.$lang;
        }
        //系统默认
        if($tb==''){
            $seo['title'] = ZFC('webconfig.site_name'.$lang);
            $seo['keywords'] = ZFC('webconfig.site_keywords'.$lang);
            $seo['description'] = ZFC('webconfig.site_description'.$lang);
            return $seo;
        }elseif($tb=='theme_tpl'){
            $seo['title'] = $title;
            $seo['keywords'] = $keywords;
            $seo['description'] = $description;
            return $seo;
        }elseif($tb=='search'){
            $seo['title'] = $title;
            $seo['keywords'] = $keywords;
            $seo['description'] = $description;
            return $seo;
        }else{
            //自定义的
            $meta_json = ZFTB('meta_data')->where([['tb','=',$tb],['post_id','=',$id],['status','<>',9]])->value('meta_data');
            //不存在,返回默认
            if(!$meta_json){
                if($title==''){
                    $seo['title'] = ZFC('webconfig.site_name'.$lang);
                }else{
                    $seo['title'] = $title;
                }
                if($keywords==''){
                    $seo['keywords'] = ZFC('webconfig.site_keywords'.$lang);
                }else{
                    $seo['keywords'] = $keywords;
                }
                if($description==''){
                    $seo['description'] = ZFC('webconfig.site_description'.$lang);
                }else{
                    $seo['description'] = $description;
                }
            }else{
                $meta = json_decode($meta_json,true);
                $seo['title'] = isset_arr_key($meta,'seo_t',$title);
                if($seo['title']==''){
                    $seo['title']  = $title;
                }
                $seo['keywords'] = isset_arr_key($meta,'seo_k',$keywords);
                if($seo['keywords']==''){
                    $seo['keywords']  = $keywords;
                }
                $seo['description'] = isset_arr_key($meta,'seo_d',$description);
                if($seo['description']==''){
                    $seo['description']  = $description;
                }
            }
        }
        $seo_title_type = ZFC('webconfig.seo_title_type');//1 默认  2 尾部加上网站名称
        if($seo_title_type==1){
            $seo['title'].= ' - '.ZFC('webconfig.site_name'.$lang);
        }
        return $seo;


    }

}
/**
 * meta自定义DIY路由链接
 * 20231114新增
 * meta_url_route(1); //路由
 * meta_url_route(2); //输出链接
 *
 * //多语言
 *	meta_url_route(1,['','en']);
 *	meta_url_route(2,['','en']);
 *
 */
if(!function_exists('meta_url_route')){
    function meta_url_route($type=1,$theme_arr=[]){
        if($type==1){
            //路由
            $list = ZFTB('meta_data')->field('tb,post_id,meta_data,diy_url')->where([['status','<>',9],['diy_url','<>','']])->select();
            $arr = [];
            foreach($list as $k=>$vo){
                $meta_data = json_decode($vo['meta_data'],true);
                $diy_url = isset_arr_key($vo,'diy_url','');
                if($diy_url==''){
                    continue;
                }
                if($vo['tb']=='category'){
                    $controller = 'cate@list';
                    $parm_str = 'cid='.$vo['post_id'];
                }elseif($vo['tb']=='post'){
                    $controller = 'cate@detail';
                    $parm_str = 'id='.$vo['post_id'];
                }elseif($vo['tb']=='special'){
                    $controller = 'cate@special_list';
                    $parm_str = 'id='.$vo['post_id'];
                }elseif($vo['tb']=='tag'){
                    $controller = 'cate@tag';
                    $parm_str = 'id='.$vo['post_id'];
                }else{
                    continue;
                }
                $type = 'get';
                $menu_type = '';
                if($theme_arr==[]){
                    $router = $diy_url.'$';
                    $template = 'default';
                    Route::rule($router, 'index/index/hook?'.$parm_str,$type)
                         ->middleware(app\common\middleware\Check::class)
                         ->append(['controller'=>$controller,'menu_type'=>$menu_type,'template'=>$template]);
                }else{
                    foreach($theme_arr as $k2=>$vo2){
                        $_explode = explode('/',$diy_url);
                        if($_explode[0]==$vo2){
                            $router = $diy_url.'$';
                            $template = $vo2.'default';
                            $controller = $vo2.$controller;
                            // 判断是否存在
                            $arr[] =    [$type,$router,$controller,'',$template,$vo2,$parm_str];
                        }else{
                            if($vo2=='' && !in_array($_explode[0],$theme_arr)){
                                $router = $diy_url.'$';
                                $template = 'default';
                                $arr[] =    [$type,$router,$controller,'',$template,$vo2,$parm_str];
                            }

                        }
                        continue;
                    }

                }
            }
            route_home($arr);

        }else{
            //输出链接
            $list = ZFTB('meta_data')->field('tb,post_id,meta_data')->where([['status','<>',9],['meta_data','like','%"diy_url":"%']])->group('meta_data')->select();
            $arr = [];
            foreach($list as $k=>$vo){
                $meta_data = json_decode($vo['meta_data'],true);
                $diy_url = isset_arr_key($meta_data,'diy_url','');
                if($diy_url==''){
                    continue;
                }
                if($theme_arr==[]){
                    $url = '/'.$diy_url;
                    $arr[] = ['tb'=>$vo['tb'],'post_id'=>$vo['post_id'],'url'=>$url];
                }else{
                    foreach($theme_arr as $k2=>$vo2){
                        if($vo2==''){
                            $url = '/'.$diy_url;
                        }else{
                            $url = '/'.$vo2.'/'.$diy_url;
                        }
                        $arr[] = ['tb'=>$vo['tb'],'post_id'=>$vo['post_id'],'url'=>$url];
                    }
                }
            }
            return $arr;
        }
    }
}

/**
 * 20231214新增
 * 便携生成tpl路径
 */
if(!function_exists('view_tpl_act')){
    function view_tpl_act($url,$str='',$find_str='hook'){
        if($str==''){
            return $url;
        }
        $url = str_replace($find_str,$str,$url);
        return $url;
    }
}

/**
 * 20240129新增
 * 转化自定义URL
 * change_diy_url($vo,'id','/cate/');
 */
if(!function_exists('change_diy_url')){
    function change_diy_url($vo,$id_type='id',$diy='/cate/'){
        if($vo['url']==''){
            $url = $diy.$vo[$id_type].'.html';
            return $url;
        }else{
            return $vo['url'];
        }
    }
}
/**
 * 20240321新增
 * 回车符\r\n统一转换为\n（针对Windows与Unix/Linux的差异），并移除文件开头和结尾的空白字符
 */
if(!function_exists('preprocessAndCalculateMd5')){
    function preprocessAndCalculateMd5($filePath) {
        // 读取文件内容
        $content = file_get_contents($filePath);
        // 将回车符\r\n统一转换为\n（针对Windows与Unix/Linux的差异），并移除文件开头和结尾的空白字符
        $content = str_replace("\r\n", "\n", $content);
        $content = trim($content);

        // 计算处理后内容的MD5
        $md5Hash = md5($content).'-'.md5($filePath);
        return $md5Hash;
    }
}
//列出目录
if(!function_exists('listdir')){
    function listdir($start_dir='.') {
        $files = array();
        if (is_dir($start_dir)) {
            $fh = opendir($start_dir);
            while (($file = readdir($fh)) !== false) {
                if (strcmp($file, '.')==0 || strcmp($file, '..')==0) continue;
                $filepath = $start_dir . '/' . $file;
                if ( is_dir($filepath) )
                    $files = array_merge($files, listdir($filepath));
                else
                    array_push($files, $filepath);
            }
            closedir($fh);
        } else {
            $files = false;
        }
        return $files;
    }
}


/**
 * 20240622新增
 * 语言包函数
 * @param string $func 函数名
 * @param string $lang 语言包
 * @param array $params 参数
 * @return string
 */
function ZfLangFunc($func,$lang='', ...$params) {
    // 使用反射机制获取函数信息
    $reflectionFunction = new \ReflectionFunction($func);
    $args = [];

    // 遍历参数，找出lang并修改其值为'en'
    foreach ($reflectionFunction->getParameters() as $index => $param) {
        $paramName = $param->getName();
        if ($paramName == 'lang') {
            $args[] = $lang;
        } else {
            // 检查参数是否在提供的参数数组中
            $args[] = array_key_exists($index, $params) ? $params[$index] : $param->getDefaultValue();
        }
    }
    // 调用原始函数并获取结果
    $result = $reflectionFunction->invokeArgs($args);
    // 返回原始函数的结果
    return $result;
}

/**
 * 20240622新增
 * 测试效率函数
 * $original_time = test_performance(function() { return t121212a(0); });
 */
function test_performance($func, ...$params) {
    $start_time = microtime(true);
    for ($i = 0; $i < 1000; $i++) { // 进行大量调用以测量性能
        $func(...$params);
    }
    $end_time = microtime(true);
    return $end_time - $start_time;
}


/**
 * @param $seo
 * @return void
 * 20240821
 * 输出首页tdk
 */
if(!function_exists('index_tdk')){
    function index_tdk($seo){
        $html = '<title>'.$seo['title'].'</title>';
        $html.= '<meta name="keywords" content="'.$seo['keywords'].'" />';
        $html.= '<meta name="description" content="'.$seo['description'].'" />';
        echo $html;
    }
}
if (!function_exists('the_link')) {
    function the_link($type, $vo) {
        static $metaCache = [];
        static $linkCache = [];

        $lang = ZLANG;
        $id = isset($vo['id']) ? $vo['id'] : (isset($vo['cid']) ? $vo['cid'] : null);

        if ($id === null) {
            return '';
        }
        $cacheKey = $type . '_' . $id . '_' . $lang;

        if (isset($linkCache[$cacheKey])) {
            return $linkCache[$cacheKey];
        }

        if (!isset($metaCache[$type])) {
            $metaCache[$type] = db('meta_data')
                ->where(['tb' => $type, 'status' => 1, 'lang' => $lang])
                ->column('diy_url', 'post_id');
        }

        if (isset($metaCache[$type][$id]) && $metaCache[$type][$id] !== '') {
            $link = '/' . $metaCache[$type][$id];
        } elseif (isset($vo['url']) && $vo['url'] !== '' && $type!= 'post') {
            $link = $vo['url'];
        } else {
            if ($type == 'category') {
                $link = ($lang ? '/' . $lang : '') . '/cate/' . $id . '.html';
            } elseif ($type == 'post') {
                $link = ($lang ? '/' . $lang : '') . '/detail/' . $id . '.html';
            } else {
                $link = '';
            }
        }

        $linkCache[$cacheKey] = $link;
        return $link;
    }
}
/**
 * 20240634新增
 * 默认值(共多语言使用)
 */
function def_cate_id($id=''){
    return  $id;
}
function def_post_id($id=''){
    return  $id;
}

/**
 * 20240830新增
 */
// 添加到现有文件末尾
// 添加 Cron 相关函数
// 添加任务
function add_cron_task($name, $interval, $func)
{
    CronManager::addTask($name, $interval, $func);
}
// 运行所有任务
function run_cron_tasks()
{
    CronManager::runDueTasks();
}

// 运行单个任务
function run_single_cron_task($name)
{
//    CronManager::runTask($name);
    ob_start(); // 开始输出缓冲
    $result = CronManager::runTask($name);
    ob_end_clean(); // 清理并结束输出缓冲

}
// 移除不再需要的任务
function destroy_cron_task($name)
{
    CronManager::destroyTask($name);
}
// 获取所有任务
function get_all_cron_tasks()
{
    return CronManager::getAllTasks();
}



/**
 * 20240926新增
 * 后台专用 自定义URL 页面
 */
function tpl_diy_url_v($res, $hook_data, $form_widget)
{
    if($hook_data[0]=='category' && in_array($hook_data[3]['cid'],explode(',',ZFC("webconfig.diyurl_cates") ))){
        return '';
    }
    if($hook_data[0]=='post' && in_array($hook_data[3]['cid'],explode(',',ZFC("webconfig.diyurl_posts") )) ){
        return '';
    }
    $html = '';
    $html .= do_action('admin_diy_url_view', $hook_data);
    $html .= '<blockquote class="layui-elem-quote">';
    
    $diy_url = isset($res['meta']['diy_url']) ? $res['meta']['diy_url'] : '';
    
    $html .= '<div style="margin-left: 15px;">';
    $html .= '当前页面URL:<a style="color:red" href="/' . $diy_url . '.html" class="diy_url_a" target="_blank">/<span class="diy_url">' . $diy_url . '</span>.html</a>';
    $html .= '</div>';
    
    $html .= '<div style="margin-left: 15px;">';
    $html .= '根据标题自动生成url:<input class="auto_active_url" lay-filter="auto_active_url" type="checkbox" value="1" ' . (isset($res['meta']) && !empty($res['meta']) ? '' : 'checked') . ' />';
    $html .= '</div>';
    
    $display_style = isset($res['meta']) && !empty($res['meta']) ? 'display: none;' : '';
    $html .= '<div style="' . $display_style . '">';
    $html .= $form_widget->form_input([
        'title' => '自定义URL',
        'name' => 'meta[diy_url]',
        'data' => $diy_url,
        'placeholder' => '如：welcome',
        'theme' => 1
    ]);
    $html .= '</div>';
    
    $html .= '</blockquote>';
    return $html;
}
if (!function_exists('filter_xss_input')) {
    /**
     * 过滤可能导致XSS攻击的特殊字符
     * @param string $input 需要过滤的输入
     * @return string 过滤后的字符串
     */
    function filter_xss_input($input) {
        if (is_string($input)) {
            // 定义需要过滤的特殊字符
            $patterns = [
                '/[<>]/',
                '/["]/',  // 双引号
                '/[\']/', // 单引号
                '/[%]/',
                '/[;]/',
                '/[\(\)]/',
                '/[&]/',
                '/[+]/'
            ];

            // 使用空字符替换特殊字符
            $input = preg_replace($patterns, '', $input);

            return $input;
        }
        return $input;
    }
}

function validateHost()
{
    try {
        // 获取当前请求的host
        $host = request()->host(true);
        // 如果host为空，记录日志并抛出异常
        if (empty($host)) {
            echo str_show_tpl('无效的Host');die;
        }
        // 获取域名白名单
        $white_str = ZFC('domain_whitelist');
        $whitelist = explode(PHP_EOL, $white_str);
        // 如果白名单为空，记录日志并抛出异常
        if (empty($whitelist) || $white_str=='') {
            //域名白名单未配置
            return;
        }
        // 将host转换为小写进行比较
        $host = strtolower($host);
        // 检查host是否在白名单中
        if (!in_array($host, array_map('strtolower', $whitelist))) {
            // 记录非法Host的访问
            echo str_show_tpl('非法的Host, 请在后台<a  href="/admin/index/index.html#//admin/Config/custom_config?">配置域名白名单</a>');die;
        }
        // 可选：检查是否使用HTTPS
//         if (!request()->isSsl()) {
//             dd('必须使用HTTPS访问');
//         }
        // 可选：检查是否存在X-Forwarded-Host头，并进行验证
        $forwardedHost = request()->header('X-Forwarded-Host');
        if ($forwardedHost && strtolower($forwardedHost) !== $host) {
            echo str_show_tpl('非法的X-Forwarded-Host');die;
        }
        // Host验证通过
        return;
    } catch (Exception $e) {
        // 重新抛出异常
        echo str_show_tpl($e->getMessage());die;
    }
}

if(!function_exists('funcTra')) {
    function funcTra($str, $func_replace, $ids, $lang)
    {
        // 构造正则表达式模式来匹配函数调用
        $pattern = '/(' . implode('|', array_map('preg_quote', $func_replace)) . ')\s*\(([^)]*)\)/';

        // 使用闭包进行参数替换
        $replacement = function ($matches) use ($ids, $lang) {
            $funcName = $matches[1];
            $params = $matches[2];
            $mappingArray = $ids[$funcName] ?? []; // 使用 null 合并运算符简化检查
            $paramsArray = array_map('trim', explode(',', $params));

            // 替换第一个参数
            if (isset($paramsArray[0])) {
                $cleanedParam = trim($paramsArray[0], '\'"');
                if (array_key_exists($cleanedParam, $mappingArray)) {
                    $paramsArray[0] = $mappingArray[$cleanedParam];
                }
            }

            // 重新组合参数并形成新的函数调用
            return "ZfLangFunc('$funcName', '$lang'" . (empty($paramsArray) ? '' : ', ' . implode(', ', $paramsArray)) . ")";
        };

        return preg_replace_callback($pattern, $replacement, $str);
    }
}

// 定义预设的 Cron 任务
// function define_cron_tasks()
// {
//     // 示例任务
//     add_cron_task('daily_cleanup', 86400, function() {
//         do_action('daily_cleanup');
//     });

//     add_cron_task('hourly_check', 3600, function() {
//         do_action('hourly_check');
//     });

//     // 可以添加更多任务...
// }

// // 调用函数来定义任务
// define_cron_tasks();

//dd(get_all_cron_tasks());
//
//run_single_cron_task('test_http');//执行指定的定时任务


if(is_dir('./application/function')){
    $func_list = listdir("./application/function");
    if($func_list){
        foreach($func_list as $k=>$vo){
            if(strpos($vo,'.php') !== false){
                include_once($vo);
            }
        }
    }

}

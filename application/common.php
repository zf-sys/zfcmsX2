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
include './application/common_db.php';
   
// 应用公共文件
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
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
    $role = explode(',',$info['role']);
    foreach($role as $k=>$vo){
      $role_list[$k] = get_role_value($vo);
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
//获取模板data数据(保存到数据库)
if (!function_exists('get_plugin_config_db')) {
    function get_plugin_config_db($plugin_name,$type='',$tag=''){
        $res = db('plugin_data')->where([['plugin_name','=',$plugin_name],['type','=',$type],['tag','=',$tag],['status','=',1]])->order('id desc')->value('data');
        if(!$res){
            $data = [];
        }else{
            $data = json_decode($res,true);
        }
        foreach($data as $k=>$vo){
            $data[$k] = base64_decode($vo);
        }
        return $data;
    }
}
if (!function_exists('save_plugin_config_db')) {
    function save_plugin_config_db($arr = [], $plugin_name='',$type='',$tag=''){
        if (is_array($arr)) {
            $res = db('plugin_data')->where([['plugin_name','=',$plugin_name],['type','=',$type],['tag','=',$tag],['status','=',1]])->order('id desc')->value('data');
            if(!$res){
                $data['plugin_name'] = $plugin_name;
                $data['status'] = 1;
                $data['type'] = $type;
                $data['tag'] = $tag;
                $data['ctime'] = time();
                $data['status'] = 1;
                $data['data'] = json_encode($arr);
                $is = db('plugin_data')->insert($data);
            }else{
                $is = db('plugin_data')->where([['plugin_name','=',$plugin_name],['type','=',$type],['tag','=',$tag],['status','=',1]])->order('id desc')->update(['data'=>json_encode($arr)]);
            }
            if($is){
                return true;
            }else{
                return false;
            }
        } else {
            return false;
        }
    }
}

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

//20220805新增
/**加密
 * @param $data
 * @return string
 * $key = "1234567887654321";//秘钥必须为：8/16/32位
 * $iv = "1234567887654321";
 */
if (!function_exists('aes_encrypt')) {
    function aes_encrypt($data,$key='1234567887654321',$iv='1234567887654321')
    {
        $base64_str = base64_encode(json_encode($data));
        $encrypted = openssl_encrypt($base64_str, "aes-128-cbc", $key, OPENSSL_ZERO_PADDING, $iv);
        return base64_encode($encrypted);
    }
}
/**解密
 * @param $data
 * @return mixed
 * $key = "1234567887654321";//秘钥必须为：8/16/32位
 * $iv = "1234567887654321";
 */
if (!function_exists('aes_decrypt')) {
    function aes_decrypt($data,$key='1234567887654321',$iv='1234567887654321')
    {
        $encrypted = base64_decode($data);
        $decrypted = openssl_decrypt($encrypted, 'aes-128-cbc', $key, OPENSSL_ZERO_PADDING, $iv);
        return json_decode(base64_decode($decrypted), true);
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
if (!function_exists('route_home')) {
    function route_home($type,$router,$controller,$menu_type='') {
        Route::rule($router, 'index/index/hook',$type)
        ->middleware(app\common\middleware\Check::class)
        ->append(['controller'=>$controller,'menu_type'=>$menu_type]);
    }
}

// 20220927新增
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
    function msubstr($str, $start=0, $length, $suffix=true,$showall=0, $charset="utf-8") {
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
        $data = config()['email'];
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
    function num_compute($num1,$type='+',$num2,$jd=2){
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
        $site_url = config('web.site_url');
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
        $db_theme_count = db('plugin')->where([['type','=','theme'],['status','<>',9]])->group('plugin_name')->count();
        $db_plugin_count = db('plugin')->where([['type','=','plugin'],['status','<>',9]])->group('plugin_name')->count();
        $theme_count = 0;
        $plugin_count = 0;

        $dir_arr = scandir('./theme');
        $theme_count = 0;
        foreach ($dir_arr as $k => $vo) {
            if($vo!='.' && $vo!='..' && $vo!='.DS_Store'  && is_dir('./theme/'.$vo) && is_file('./theme/'.$vo.'/plugin_info.php')){
                $theme_count++;
            }
        }
        $dir_arr = scandir('./addons');
        $plugin_count = 0;
        foreach ($dir_arr as $k => $vo) {
            if($vo!='.' && $vo!='..' && $vo!='.DS_Store' && is_dir('./addons/'.$vo) && is_dir('./addons/'.$vo.'/config') && is_file('./addons/'.$vo.'/config/plugin_info.php')){
                $plugin_count++;
            }
        } 
       
        $ret_data = [true,true];
        if($db_plugin_count==$plugin_count){
            $ret_data[0] = false;
        }
        if($db_theme_count==$theme_count){
            $ret_data[1] = false;
        }
        // echo "<br>.".$db_plugin_count;
        // echo "<br>.".$plugin_count;
        // echo "<br>.".$db_theme_count;
        // echo "<br>.".$theme_count;
        // dd($ret_data);
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
  */
if(!function_exists('widget_st')){
    function widget_st($name='',$type='widget') {
        $_static = config('template.tpl_replace_string.__STATIC__');
        $v = 'v=1';
        if($type=='css'){
            if(in_array($name,['layui'])){
                return "<link rel='stylesheet' href='$_static/style/layui/css/layui.css?$v' media='all'>\n";
            }
            if(in_array($name,['admin'])){
                return "<link rel='stylesheet' href='$_static/system/style/admin.css?$v' media='all'>\n";
            }

        }
        if($type=='js'){
            if(in_array($name,['jq183','jq'])){
                return "<script type='text/javascript' src='$_static/style/jquery-1.8.3.min.js?$v'></script>\n";
            }
            if(in_array($name,['layui'])){
                return "<script type='text/javascript' src='$_static/style/layui/layui.js?$v'></script>\n";
            }
            if(in_array($name,['laydate'])){
                return "<script type='text/javascript' src='$_static/style/laydate/laydate.js?$v'></script>\n";
            }
            if(in_array($name,['layer'])){
                return "<script type='text/javascript' src='$_static/style/layer/layer.js?$v'></script>\n";
            }
            if(in_array($name,['common'])){
                return "<script type='text/javascript' src='$_static/system/common.js?$v'></script>\n";
            }
        }
        if($type=='widget'){
            if(in_array($name,['webuploader'])){
                return "<link rel='stylesheet' type='text/css' href='$_static/style/webuploader/webuploader.css?$v'>\n<script type='text/javascript' src='$_static/style/webuploader/webuploader.js?$v'></script>\n";
            }
            if(in_array($name,['ueditor'])){
                return "<script type='text/javascript' src='$_static/style/ueditor/ueditor.config.js?$v'></script>\n<script type='text/javascript' src='$_static/style/ueditor/ueditor.all.js?$v'></script>\n<link rel='stylesheet' href='$_static/style/ueditor/themes/default/css/ueditor.css?$v' media='all'>\n";
            }
            if(in_array($name,['bootstrap','bootstrap334'])){
               return "<script src='$_static/style/bootstrap/bootstrap-3.3.4.js?$v'></script>\n<link rel='stylesheet' href='$_static/style/bootstrap/bootstrap-3.3.4.css?$v'>\n";
            }
            if(in_array($name,['input-tag'])){
                return "<link rel='stylesheet' href='$_static/style/input-tag/inputTag.css?$v' media='all'>\n";
            }
            if(in_array($name,['viewer'])){
                return "<link rel='stylesheet' type='text/css' href='$_static/style/viewer/viewer.mini.css?$v'>\n<script type='text/javascript' src='$_static/style/viewer/viewer.mini.js?$v'></script>\n";
            }
            if(in_array($name,['tinymce'])){
                return "<script src='$_static/style/tinymce515/tinymce.min.js?$v'></script>\n";
                //<script src='https://cdn.bootcdn.net/ajax/libs/tinymce/5.7.1/jquery.tinymce.min.js?$v'></script>\n
            }
            if(in_array($name,['meditor'])){
                return "<link rel='stylesheet' type='text/css' href='$_static/style/meditor/css/editormd.css?$v'>\n<script src='$_static/style/meditor/editormd.js?$v'></script>\n";
            }
            if(in_array($name,['wangEditor'])){
                return "<script src='$_static/style/wangEditor/wangEditor.min.js?$v'></script>\n";
            }
            if(in_array($name,['vditor'])){
                // return "<link rel='stylesheet' href='$_static/style/vditor/dist/index.css?$v' />\n<script src='$_static/style/vditor/dist/index.min.js?$v'></script>\n";
                // <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vditor/dist/index.css" />
                // <script src="https://cdn.jsdelivr.net/npm/vditor/dist/index.min.js"></script>
            }
            if(in_array($name,['autosize'])){
                return "<script src='$_static/style/autosize/dist/autosize.js?$v'></script>\n";
            }
            if(in_array($name,['echarts'])){
                return "<script src='$_static/style/echarts.min.js?$v'></script>\n";
            }
            if(in_array($name,['input-select'])){
                return "<script src='$_static/style/input-select/selectInput.js?$v'></script>\n";
            }
            if(in_array($name,['fcup'])){
                return "<script src='$_static/style/fcup.min.js?$v'></script>\n";
            }

        }
        // <!-- 拖动排序 -->
        // <!-- <script src="http://libs.baidu.com/jquery/2.1.4/jquery.min.js"></script> -->
        // <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.8.3/Sortable.min.js"></script> -->
    }
}
/**
 * 20230829
 * 处理加密数据,返回原始数组
 */
if(!function_exists('deal_post_message')){
    function deal_post_message($data){
        if($data!=[]){
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

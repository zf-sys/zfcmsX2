<?php
namespace app\common\controller;
use think\Controller;
use think\Db;
use think\facade\Request;
use think\captcha\Captcha;
// 七牛云oss
use League\Flysystem\Filesystem;
use Overtrue\Flysystem\Qiniu\QiniuAdapter;
use Overtrue\Flysystem\Qiniu\Plugins\FetchFile;
//阿里云
use OSS\OssClient as AliOssClient;

//又拍云
use Upyun\Upyun;
use Upyun\Config as UpyConfig;


class Upload extends Controller{
    public function __construct (){
      $this->upload_type = ZFC("webconfig.upload_type");
      $this->is_upload_compress = ZFC("webconfig.is_upload_compress");
      $this->upload_pic_max_w = ZFC("webconfig.upload_pic_max_w");
      $this->is_file_dlj = ZFC("webconfig.is_file_dlj");

      if(ZFC("webconfig.site_path")!=''){
        $this->site_path = '/'.ZFC("webconfig.site_path").'/';
      }else{
        $this->site_path = '/';
      }

    }
    private function _check_pic_mm($file){
      
      $ext = file_format_cn($file['name']);
      if($ext=='图片'){
        if (false === check_illegal($file['tmp_name'])) {
          $errmsg = '疑似木马图片';
          return jserror($errmsg);
        }
      }
    }
    public function upload_one(){
      try {
        $file = request()->file('file');
        $this->file = $file;
        $tmp_name = $file->getInfo()['tmp_name'];
        $this->_check_pic_mm($file->getInfo());
        if($this->upload_type!=''){
          $name = date("Ymd",time()).'_'.rand(1,99999).$file->getInfo()['name'];
          $url = $this->oss_upload($this->upload_type,$name,$tmp_name);
          if(!$url){ 
            return jserror('上传方式不存在');
          }
        }else{
          $info = $file->validate(['ext'=>ZFC("webconfig.pic_ext")])->move('.'.$this->site_path.'upload/common/image');
          $getSaveName = str_replace('\\', '/', $info->getSaveName());
          $url = (isHTTPS()?'https':'http').'://'.request()->host().$this->site_path.'upload/common/image/'.$getSaveName;
          
          
        }
        $this->save_upload_info($file,$url);
      }catch (Exception $e) {
        return jserror($e);
      }
    }
    /**
     * 图片缩率图处理
     * file_path：文件路径
     * path：文件保存目录 : 需事先手动创建
     */
    public function file_upload_thumb($file_url){
        try {
          // $file_url = $this->site_path.'upload/common/image/'.$file_path;
          $image = \think\Image::open('.'.$file_url);
          $size = $image->size(); 
          if($this->upload_pic_max_w){
            $max_s = intval($this->upload_pic_max_w);
          }else{
            $max_s = 1000;
          }
          if($size[0]<$max_s){
            return 1;
          }
          $bl = $max_s/$size[0];
          $width = $max_s;
          $height = $size[1]*$bl;
        //   if(!file_exists($this->site_path.'upload/common/image/')){
        //         mkdir($this->site_path.'upload/common/image/',0777,true);
        //     }
    
        //   $save_url = $this->site_path.'upload/common/image/'.sha1($file_url)."_".$width."_".$height.".".$image->type();
        $file_url = str_replace(basename($file_url),strtolower(basename($file_url)),$file_url);
        $save_url = str_replace('.'.$image->type(),'_thumb_'.intval($width).'_'.intval($height).'.'.$image->type(),$file_url);
          $save_name = ".".$save_url;
          $image->thumb($width, $height)->save($save_name);
          return (isHTTPS()?'https':'http').'://'.request()->host().$save_url;
        }catch (Exception $e) {
            return 1;
          }
    }

    public function upload_one_file(){
      try {
        $file = request()->file('file');
        $this->file = $file;
        $tmp_name = $file->getInfo()['tmp_name'];
        $this->_check_pic_mm($file->getInfo());
        if($this->upload_type!=''){
          $name = date("Ymd",time()).'_'.rand(1,99999).$file->getInfo()['name'];
          $url = $this->oss_upload($this->upload_type,$name,$tmp_name);
          if(!$url){ 
            return jserror('上传方式不存在');
          }
        }else{
          $info = $file->validate(['ext'=>ZFC("webconfig.file_ext")])->move('.'.$this->site_path.'upload/common/file');
          $getSaveName = str_replace('\\', '/', $info->getSaveName());
          $url = (isHTTPS()?'https':'http').'://'.request()->host().$this->site_path.'upload/common/file/'.$getSaveName;
        }
        //保存上传数据
        $this->save_upload_info($file,$url);
      }catch (Exception $e) {
        return jserror($e);
      }
    }

   
    public function meditor_upload_one(){
      try {
        $file = request()->file('editormd-image-file');
        $tmp_name = $file->getInfo()['tmp_name'];
        $this->_check_pic_mm($file->getInfo());
        if($this->upload_type!=''){
          $name = date("Ymd",time()).'_'.rand(1,99999).$file->getInfo()['name'];
          $url = $this->oss_upload($this->upload_type,$name,$tmp_name);
          if(!$url){ 
            return jserror('上传方式不存在');
          }
        }else{
          $info = $file->move( '.'.$this->site_path.'upload/common/file');
          $getSaveName = str_replace('\\', '/', $info->getSaveName());
          $url = (isHTTPS()?'https':'http').'://'.request()->host().$this->site_path.'upload/common/file/'.$getSaveName;
        }
        $url = $this->save_upload_info($file,$url,0,true);
        if($url){
            return json_encode(array(
               'success'    => 1, 
               'url'       => $url,
               'message'    =>  'success',
            ));
        }else{
            return json_encode(array(
               'success'    => 0, 
               'url'       => '',
               'message'    =>  'error',
            ));
        }
      }catch (Exception $e) {
        return json_encode(array(
          'success'    => 0, 
          'url'       => '',
          'message'    =>  $e,
       ));
      }
    }
//     public function upload_pic_liu(){
// //目录的upload文件夹下
//         $up_dir = "upload/common/file/".date('Ymd', time()) . "/";  //创建目录
//         if(!file_exists($up_dir)){
//             mkdir($up_dir,0777,true);
//         }
//         $base64_img = input('img','');
 
//         if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_img, $result)){
//             $this->upload_type = $result[2];
//             if(in_array($this->upload_type,array('pjpeg','jpeg','jpg','gif','bmp','png'))){
//                 $new_file = $up_dir.time().'.'.$this->upload_type;
//                 if(file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_img)))){
//                     $img_path = str_replace('../../..', '', $new_file);
//                     $msg = (isHTTPS()?'https':'http').'://'..'/web/'.$new_file;
//                     return jssuccess($msg);
//                 }else{
//                     return jserror('图片上传失败');
//                 }
//             }else{
//                 //文件类型错误
//                 return jserror('图片上传类型错误');
//             }
//         }else{
//                 return jserror('上传错误');
//         }
//     }
    
  
    public function upload_one_filesystem(){
      try {
        $file = request()->file('file');
        $tmp_name = $file->getInfo()['tmp_name'];
        $this->_check_pic_mm($file->getInfo());
        if($this->upload_type!=''){
          $name = date("Ymd",time()).'_'.rand(1,99999).$file->getInfo()['name'];
          $url = $this->oss_upload($this->upload_type,$name,$tmp_name);
          if(!$url){ 
            return jserror('上传方式不存在');
          }
        }else{
          $info = $file->validate(['ext'=>ZFC("webconfig.file_ext")])->move('.'.$this->site_path.'upload/common/filesystem/fp');
          $getSaveName = str_replace('\\', '/', $info->getSaveName());
          $url = (isHTTPS()?'https':'http').'://'.request()->host().$this->site_path.'upload/common/filesystem/fp/'.$getSaveName;
        }
        $cid = input('cid',0);
        $this->save_upload_info($file,$url,$cid);
      }catch (Exception $e) {
        return jserror($e);
      }
    }
    public function fenpian_one_upload(){
      try {
        // 设置跨域头
        $tag = input('tag','');
        $_file = request()->file('file_data');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:PUT,POST,GET,DELETE,OPTIONS');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        header('Content-Type:application/json; charset=utf-8');
        $file = isset($_FILES['file_data']) ? $_FILES['file_data']:null; //分段的文件
        $name = isset($_POST['file_name']) ? $_POST['file_name']:null; //要保存的文件名
        $total = isset($_POST['file_total']) ? $_POST['file_total']:0; //总片数
        $index = isset($_POST['file_index']) ? $_POST['file_index']:0; //当前片数
        $md5   = isset($_POST['file_md5']) ? $_POST['file_md5'] : 0; //文件的md5值
        $md5 = $md5.'-'.$tag;
        // dd($md5);
        $size  = isset($_POST['file_size']) ?  $_POST['file_size'] : null; //文件大小
        $chunksize  = isset($_POST['file_chunksize']) ?  $_POST['file_chunksize'] : null; //当前切片的文件大小
        $suffix  = isset($_POST['file_suffix']) ?  $_POST['file_suffix'] : null; //当前上传的文件后缀
        //echo '总片数：'.$total.'当前片数：'.$index;
        // 在实际使用中，用md5来给文件命名，这样可以减少冲突
        // 简单的判断文件类型s
        $info = pathinfo($name);
        // 取得文件后缀
        $ext = isset($info['extension'])?$info['extension']:'';
        $file_name = $md5.'.'.$ext;
        $newfile = '.'.$this->site_path.'upload/common/filesystem/fp/'.$file_name;
        $log_file = '.'.$this->site_path.'upload/common/filesystem/fp/'.$md5.'.txt';
        // 文件可访问的地址
        $url = (isHTTPS()?'https':'http').'://'.request()->host().$this->site_path.'upload/common/filesystem/fp/'.$file_name;
        // 这里判断有没有上传的文件流
        if ($file['error'] == 0) {
          file_put_contents($log_file,$index);	
          // 如果文件不存在，就创建
          if (!file_exists($newfile)) {
            if (!move_uploaded_file($file['tmp_name'], $newfile)) {
              FpjsonMsg(0,'无法移动文件');
            }
            // 片数相等，等于完成了
            if($index == $total ){  
              $cid = input('cid',0);
              $this->save_upload_info($_file,$url,$cid,false,true);
              // FpjsonMsg(2,'上传完成',$url,$index);
            }        
            FpjsonMsg(1,'正在上传','',$index);
          }     
          // 如果当前片数小于等于总片数,就在文件后继续添加
          if($index <= $total){
            $content = file_get_contents($file['tmp_name']);
            if (!file_put_contents($newfile, $content, FILE_APPEND)) {
            FpjsonMsg(0,'无法写入文件');
            }
            // 片数相等，等于完成了
            if($index == $total ){  
              // $url = (isHTTPS()?'https':'http').'://'.request()->host().$this->site_path.'upload/common/filesystem/fp/'.$getSaveName;
              $cid = input('cid',0);
              // dd($url);
              $this->save_upload_info($_file,$url,$cid,false,true);
              // FpjsonMsg(2,'上传完成',$url,$index);
            }
            FpjsonMsg(1,'正在上传','',$index);
          }   
        } else {
          FpjsonMsg(0,'没有上传文件');
        }
      }catch (Exception $e) {
        return jserror($e);
      }
    }
    public function fenpian_one_check(){
      $tag = input('tag','');
      // 设置跨域头
      header('Access-Control-Allow-Origin:*');
      header('Access-Control-Allow-Methods:PUT,POST,GET,DELETE,OPTIONS');
      header('Access-Control-Allow-Headers:x-requested-with,content-type');
      header('Content-Type:application/json; charset=utf-8');
      $name  = isset($_POST['file_name']) ? $_POST['file_name']:null; // 文件名
      $md5   = isset($_POST['file_md5']) ? $_POST['file_md5'] :''; //文件的md5值
      $md5 = $md5.'-'.$tag;
      $size   = isset($_POST['file_size']) ? $_POST['file_size'] :''; //文件大小
      //FpjsonMsg(0,'','',201);  
      if(!$md5){
        FpjsonMsg(1,'没有文件');
      }
      // 简单的判断文件类型s
      $info = pathinfo($name);
      // 取得文件后缀
      $ext = isset($info['extension'])?$info['extension']:'';
      /* 判断文件类型 */
      // $imgarr = array('jpeg','jpg','png','gif');
      // if(!in_array($ext,$imgarr)){
      // 	FpjsonMsg(1,'文件类型出错');
      // }
      // 在实际使用中，用md5来给文件命名，这样可以减少冲突
      $file_name = $md5.'.'.$ext;
      $newfile = '.'.$this->site_path.'upload/common/filesystem/fp/'.$file_name;
      $log_file = '.'.$this->site_path.'upload/common/filesystem/fp/'.$md5.'.txt';
      // 文件可访问的地址
      $url = $this->site_path.'upload/common/filesystem/fp/'.$file_name;
      if (!file_exists('.'.$this->site_path.'upload/common/filesystem/fp/')) {
        mkdir('.'.$this->site_path.'upload/common/filesystem/fp/', 0777, true);
      }
      /** 判断是否重复上传 **/
      // 清除文件状态
      clearstatcache($newfile);
      // 文件大小一样的，说明已经上传过了
      if(is_file($newfile) && ($size == filesize($newfile))){
        FpjsonMsg(2,'已经上传过了',$url);          
      }
      if(is_file($log_file)){
        // 读取当前片数的时候要向前偏移1个
        $index = file_get_contents($log_file);
        $index = $index + 1;
      }else{
        $index = 1;
      }
      FpjsonMsg(0,'','',$index);   
    }

    private function save_upload_info($file,$url='',$cid='0', $return=false,$fp=false){
        $req_file = $file->getInfo();
        if($fp){
          $file_name = substr($url , strrpos($url, '/')+1);
          $save_data['name'] = $file_name;
          $url_arr = parse_url($url);
          $save_data['size'] = filesize('.'.$url_arr['path']);
        }else{
          $save_data['size'] =$req_file['size'];
          $save_data['name'] = $req_file['name'];
        }

        $save_data['mine'] =$req_file['type'];
        $save_data['token'] =date("Ymd").zf_rand_str(10);
        $save_data['type'] = file_format_cn($save_data['name']);
        if($save_data['type']=='图片'){
          //缩略图
          if($this->is_upload_compress==1){
            //'/upload/common/image/'.
            if(is_str_find($url,request()->host())){
              $_cq_arra = explode(request()->host(),$url);
              if(isset($_cq_arra[1])){
                $thumb_url = $this->file_upload_thumb($_cq_arra[1]);
                if($thumb_url!='1'){
                  $save_data['thumb'] = $thumb_url;
                }
              }
              
            }
            
          }
        }
        
        $save_data['url'] = $url;
        $save_data['ctime'] = time();
        $save_data['ip'] = request()->ip();
        $save_data['status'] = 1;
        $save_data['uniacid'] = session('uniacid');
        $save_data['session_data'] = json_encode(session(""));
        if(session('zf_admin')){
            $save_data['uid'] = 'zf_admin-'.session('zf_admin')['id'];
        }elseif(session('admin')){
            $save_data['uid'] = 'admin-'.session('admin')['id'];
        }elseif(session('home')){
            $save_data['uid'] = 'home-'.session('home')['id'];
        }elseif(session('user')){
            $save_data['uid'] = 'user-'.session('user')['id'];
        }else{
            $save_data['uid'] = '非登录上传';
        }
        $save_data['cid'] = '';
        $res = Db::name('upload')->insertGetId($save_data);
        if($res){
          if(is_file('./extend/zf/Yun.php') && isset($this->is_file_dlj) && $this->is_file_dlj==1 ){
            $url = get_domain().'/get_file_out?id='.$res.'&token='.$save_data['token'];
          }
          // if($fp){
          //     FpjsonMsg(2,'上传完成',$url,'999');
          // }
          if($return){
            return $url;
          }else{
            return jssuccess($url,$save_data['type']);
          }
        }else{
          if($return){
            return false;
          }else{
            return jserror("error");
          }
        }
    }

    private function oss_upload($upload_type,$name,$tmp_name){
        if($upload_type=='ali'){
            return $this->ali_upload($name,$tmp_name);
        }elseif($upload_type=='qny'){
            return $this->qiniu_upload($name,$tmp_name);
        }elseif($upload_type=='upy'){
            return $this->upy_upload($name,$tmp_name);
        }else{
            return false;
        }
    }
    private function upy_upload($name,$tmp_name){
      $domain = config()['oss']['upy_domain']; 
      $serviceConfig = new UpyConfig(config()['oss']['upy_name'], config()['oss']['upy_user'], config()['oss']['upy_pwd']);
      $client = new Upyun($serviceConfig);
      $file = fopen($tmp_name, 'r');
      $res = $client->write('/'.$name, $file);
      if($res){
        return $domain.'/'.$name;
      }else{
        return null;
      }
    }
   

    private function qiniu_upload($name,$tmp_name){
        $accessKey = config()['oss']['qny_ak'];
        $secretKey = config()['oss']['qny_sk'];
        $bucket = config()['oss']['qny_bucket'];
        $domain = config()['oss']['qny_domain']; 
        $adapter = new QiniuAdapter($accessKey, $secretKey, $bucket, $domain);
        $flysystem = new \League\Flysystem\Filesystem($adapter);
        $r = $flysystem->writeStream($name, fopen($tmp_name, 'r'));
        if($r){
            return $domain.'/'.$name;
        }else{
            return null;
        }
    }
    private function ali_upload($name,$tmp_name){
        $ossconfig = [
            'KeyId'      => config()['oss']['ali_ak'],  
            'KeySecret'  => config()['oss']['ali_sk'],  
            'Endpoint'   => config()['oss']['ali_domain'], 
            'Bucket'     => config()['oss']['ali_bucket'],  
            'ali_domain_diy'     => config()['oss']['ali_domain_diy'],  
        ];
        $ossClient = new AliOssClient($ossconfig['KeyId'], $ossconfig['KeySecret'], $ossconfig['Endpoint']);
        try {
            $result = $ossClient->uploadFile($ossconfig['Bucket'],'uploads/'.md5($_SERVER["SERVER_NAME"]).'/'. $name, $tmp_name);
            $url = $result['info']['url'];
            if($ossconfig['ali_domain_diy']!=''){
                $url = str_replace('http://'.$ossconfig['Bucket'].'.'.$ossconfig['Endpoint'],$ossconfig['ali_domain_diy'],$url);
            }
            return $url;
        } catch (OssException $e) {
            return false;
        }
    }


    





   
}

// 输出json信息
if(!function_exists('FpjsonMsg')){
  function FpjsonMsg($status,$message,$url='',$index=0){
  	$arr['status'] = $status;
  	$arr['message'] = $message;
  	$arr['url'] = $url;
  	$arr['file_index'] = $index;
  	echo json_encode($arr);
  	die();
  }
}
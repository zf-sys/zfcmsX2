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
      $this->upload_type = config('web.upload_type');
      if(config('web.site_path')){
        if(config('web.site_path')!=''){
          $this->site_path = '/'.config('web.site_path').'/';
        }else{
          $this->site_path = '/';
        }
      }else{
        $this->site_path = '/';
      }
    }
    public function upload_one(){
      try {
        $file = request()->file('file');
        $this->file = $file;
        if($this->upload_type!=''){
          $tmp_name = $file->getInfo()['tmp_name'];
          $name = date("Ymd",time()).'_'.rand(1,99999).$file->getInfo()['name'];
          $url = $this->oss_upload($this->upload_type,$name,$tmp_name);
          if(!$url){ 
            return jserror('上传方式不存在');
          }
        }else{
          $info = $file->validate(['ext'=>config()['web']['pic_ext']])->move('.'.$this->site_path.'upload/common/image');
          $getSaveName = str_replace('\\', '/', $info->getSaveName());
          $url = (isHTTPS()?'https':'http').'://'.request()->host().$this->site_path.'upload/common/image/'.$getSaveName;
        }
        $this->save_upload_info($file,$url);
      }catch (Exception $e) {
        return jserror($e);
      }
    }
    public function upload_one_file(){
      try {
        $file = request()->file('file');
        $this->file = $file;
        if($this->upload_type!=''){
          $tmp_name = $file->getInfo()['tmp_name'];
          $name = date("Ymd",time()).'_'.rand(1,99999).$file->getInfo()['name'];
          $url = $this->oss_upload($this->upload_type,$name,$tmp_name);
          if(!$url){ 
            return jserror('上传方式不存在');
          }
        }else{
          $info = $file->validate(['ext'=>config()['web']['file_ext']])->move('.'.$this->site_path.'upload/common/file');
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
        if($this->upload_type!=''){
          $tmp_name = $file->getInfo()['tmp_name'];
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
        if($this->upload_type!=''){
          $tmp_name = $file->getInfo()['tmp_name'];
          $name = date("Ymd",time()).'_'.rand(1,99999).$file->getInfo()['name'];
          $url = $this->oss_upload($this->upload_type,$name,$tmp_name);
          if(!$url){ 
            return jserror('上传方式不存在');
          }
        }else{
          $info = $file->validate(['ext'=>config()['web']['file_ext']])->move('.'.$this->site_path.'upload/common/filesystem');
          $getSaveName = str_replace('\\', '/', $info->getSaveName());
          $url = (isHTTPS()?'https':'http').'://'.request()->host().$this->site_path.'upload/common/filesystem/'.$getSaveName;
        }
        $cid = input('cid',0);
        $this->save_upload_info($file,$url,$cid);
      }catch (Exception $e) {
        return jserror($e);
      }
    }
    private function save_upload_info($file,$url='',$cid='0', $return=false){
        $req_file = $file->getInfo();
        $save_data['size'] =$req_file['size'];
        $save_data['mine'] =$req_file['type'];
        $save_data['name'] = $req_file['name'];
        $save_data['token'] =date("Ymd").zf_rand_str(10);
        $save_data['type'] = file_format_cn($save_data['name']);
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
          $url = get_domain().'/get_file_out?id='.$res.'&token='.$save_data['token'];
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


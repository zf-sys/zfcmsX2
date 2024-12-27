<?php
namespace app\common\controller;
use think\Controller;
use think\Db;

class Fileupload extends Controller
{
    public function __construct(){
        parent::__construct();
        $this->upload_pannel_type = ZFC("webconfig.upload_pannel_type");
        if($this->upload_pannel_type==''){
            echo str_show_tpl('禁止使用,请在后台开启');die;
        }elseif($this->upload_pannel_type==1 ){
            //后台专享
            if(!session('admin')){
                $this->error('请先登录后再操作');
            }
        }else{
            echo str_show_tpl('未知参数');die;
        }
    }
    public function index(){
        echo 'Fileupload';
    }
    
    public function test(){
        return view();
    }
    public function upload(){
        $this->is_file_dlj = ZFC("webconfig.is_file_dlj");
        $cid = input('cid','0');
        $t = input('t',1);
        $kk = input('kk','');
        $zf_class = input('zf_class','.zf_list');
        $this->assign('zf_class',$zf_class);
        $this->assign('cid',$cid);
        $this->assign('t',$t);
        $this->assign('kk',$kk);
        $cate_list =Db::name('upload_cate')->where(['status'=>1,'uniacid'=>session('uniacid')])->order('id desc')->select();
        $this->assign('cate_list',$cate_list);
        $where[] = ['status','=',1];
        if($cid!='0'){
            $where[] = ['cid','=',$cid];
        }
        $where[] = ['uniacid','=',session('uniacid')];
        $pic_list =Db::name('upload')->where($where)->order('id desc')
        ->paginate(12,false,['query' => request()->param()])->each(function($item, $key){
            if(is_file('./extend/zf/Yun.php') && isset($this->is_file_dlj) && $this->is_file_dlj==1 ){
                $item['url'] = get_domain().'/get_file_out?id='.$item['id'].'&token='.$item['token'];
            }
            return $item;
        });
        $page = $pic_list->render();
        $this->assign("page",$page);
        $this->assign('pic_list',$pic_list);
        $this->assign('cid',$cid);
        $this->assign('zf_class',$zf_class);
        return view();
    }
    public function cate_add()
    {
        if(request()->isPost()){
            $data = input("post.");
            if($data['name']=='' ){
                return jserror('请填写信息');exit;
            }
            $data['uniacid'] = session('uniacid');
            $res =Db::name('upload_cate')->insert($data);
            if($res){
                return jssuccess('分组新增成功');
            }else{
                return jserror('分组新增失败');exit;
            }
        }
        return view();
    }
     public function cate_edit()
    {
        if(request()->isPost()){
            $data = input("post.");
            $res =  Db::name('upload_cate')->where(['id'=>$data['id'],'uniacid'=>session('uniacid')])->update($data);
            if($res){
                return jssuccess('分组修改成功');
            }else{
                return jserror('分组修改失败');
            }
        }
    }
     public function cate_del($id)
    {
        $upload_file_num = DB::name('upload')->where(['cid'=>$id])->count();
        if($upload_file_num>=1){
            return jserror('分类下还有文件,请移除后重试');die;
        }
        $res = Db::name('upload_cate')->where(['id'=>$id,'uniacid'=>session('uniacid')])->update(['status'=>9]);
        if($res){
            return jssuccess('分类删除成功');
        }else{
            return jserror('分类删除失败');
        }
        $this->assign('res',$res);
        return view();
    }
    

    public function file_del(){
        $data = input('post.');
        $ids = $data['ids'];
        $ids = array_filter($ids);
        if(count($ids)==0){
            return jserror("请选择文件");
        }
        $res = Db::name('upload')->where([['id','in',$ids],['uniacid','=',session('uniacid')]])->update(['status'=>9]);
        ###伪删除,如需删除文件请手动###
        #unlink()
        if($res){
            return jssuccess('文件删除成功');
        }else{
            return jserror("文件删除失败");
        }
    }
    public function file_move(){
        $data = input('post.');
        $ids = $data['ids'];
        $cid = input('cid','0');
        $ids = array_filter($ids);
        if(count($ids)==0){
            return jserror("请选择文件");
        }
        $res = Db::name('upload')->where([['id','in',$ids],['uniacid','=',session('uniacid')]])->update(['cid'=>$cid]);
        if($res){
            return jssuccess('文件移动成功');
        }else{
            return jserror("文件移动失败");
        }
    }
    //cropImage
    public function cropimage(){
        $cid = input('cid','0');
        $this->assign('cid',$cid);
        $url = input('url','');
        $this->assign('url',$url);
        // 获取文件名并添加随机数
        if($url!=''){
            $filename = $this->handleFileUrl($url);
        }else{
            $filename = '';
        }
        $this->assign('filename',$filename);

        return view();
    }
    public function handleFileUrl($url) {
        $pathinfo = pathinfo($url);
        $filename = $pathinfo['filename'] . '_' . mt_rand(1000, 9999);
        
        if (isset($pathinfo['extension'])) {
            $filename .= '.' . $pathinfo['extension'];
        } else {
            // 尝试获取文件类型
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_exec($ch);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            curl_close($ch);
            // 根据 Content-Type 设置扩展名
            $extension = $this->getExtensionFromMimeType($contentType);
            
            if ($extension) {
                $filename .= '.' . $extension;
            } else {
                // 如果不是图片类型，可以抛出异常或返回错误信息
               $this->error('不支持的文件类型/不支持短链接');
                // 或者
                // return false; 
            }
        }
        
        return $filename;
    }
    
    private function getExtensionFromMimeType($mimeType) {
        $imageTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/bmp' => 'bmp',
            'image/webp' => 'webp',
            'image/tiff' => 'tiff',
            'image/svg+xml' => 'svg'
        ];
        
        return isset($imageTypes[$mimeType]) ? $imageTypes[$mimeType] : null;
    }






}




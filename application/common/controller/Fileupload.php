<?php
namespace app\common\controller;
use think\Controller;
use think\Db;

class Fileupload extends Controller
{
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
        $zf_class = input('zf_class','.zf_list');
        $this->assign('zf_class',$zf_class);
        $this->assign('cid',$cid);
        $this->assign('t',$t);
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





}




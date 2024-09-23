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

namespace app\admin\controller;
use think\facade\Request;
use think\Db;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Wmc1125\TpFast\GoogleAuthenticator;

class User extends Admin
{
    public function __construct (){
        parent::__construct();
    }

    /**
     * @Notes:用户列表
     * @Interface index
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   11:02 下午
     */
    public function index()
    {
        admin_role_check($this->z_role_list,$this->mca);
        $this->_update_sql(1);
        $type = input('type','');
        if($type=='add_group'){
            $tag = input('tag','');
            $is = ZFTB('user_group')->where([['status','<>',9],['tag','=',$tag]])->order("id asc")->find();
            if(!$is){
                try {
                    $is_add = ZFTB('user_group')->insert(['status'=>1,'tag'=>$tag,'name'=>$tag,'ctime'=>time()]);
                }catch (Exception $e) {
                    return jserror($e->getMessage());
                }
                if(!$is_add){
                    dd('添加失败');
                }
                $is = ZFTB('user_group')->where([['status','<>',9],['tag','=',$tag]])->order("id asc")->find();
            }
            $gid = $is['id'];
            $this->redirect(url('admin/user/index',['gid'=>$gid]));
        }

        $where[] = ['status','<>',9];
        $key = input('key','');
        if($key!=''){
            $where[] = ['name|email|tel','like','%'.$key.'%'];
        }
        $gid = input('gid','');
        if($gid!=''){
            $where[] = ['gid','=',$gid];
        }
        $list= Db::name('user')->where($where)->order("sort desc,id desc")->paginate(10,false, ['query' => request()->param()]);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        $glist = ZFTB('user_group')->where(['status'=>1])->select();
        $this->assign("glist",$glist);
        return view();
    }

    /**
     * @Notes:添加新用户
     * @Interface add
     * @return \think\response\View|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   11:02 下午
     */
    public function add()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(!request()->isPost()){
            $glist = ZFTB('user_group')->where(['status'=>1])->select();
            $this->assign("glist",$glist);
            return view();
        }
        $data = input('post.');
        $data['ctime'] = time();
        if($data['pwd']!=''){
            $data['pwd'] = md5('zfcms-'.$data['pwd']);
        }
        // $data = array_merge($data,$this->common_tag);
        //判断是否存在
        $is_user =  ZFTB('user')->where(['name'=>$data['name']])->find();
        if($is_user){
            return jserror('用户名已存在');exit;
        }
        deal_meta_data_add('user',$data);


    }

    /**
     * @Notes:用户修改
     * @Interface edit
     * @return \think\response\View|void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author: 子枫
     * @Time: 2019/11/13   11:02 下午
     */
    public function edit()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isGet()){
            $res =  ZFTB('user')->where(['id'=>input('id')])->find();
            if(!$res){
                $this->error('用户不存在');
            }
            $meta_json = ZFTB('meta_data')->where([['tb','=','user'],['post_id','=',$res['id']],['status','<>',9]])->value('meta_data');
            $res['meta'] = json_decode($meta_json,true);
            $this->assign("res",$res);
            $glist =  ZFTB('user_group')->where(['status'=>1])->select();
            $this->assign("glist",$glist);
            return view('user/add');
        }
        if(request()->isPost()){
            $data = input('post.');
            if($data['pwd']!= ''){
                $data['pwd'] = md5('zfcms-'.$data['pwd']);
            }else{
                unset($data["pwd"]);
            }
            $data['utime'] = time();
            $is_user =  ZFTB('user')->where([['name','=',$data['name']],['id','<>',$data['id']]])->find();
            if($is_user){
                return jserror('用户名已存在');exit;
            }
            deal_meta_data_edit('user',$data);
        }
    }

    /**
     * @Notes:用户分类
     * @Interface group
     * @return \think\response\View
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   11:03 下午
     */
    public function group()
    {
        admin_role_check($this->z_role_list,$this->mca);
        $this->_update_sql(1);
        $group_list = ZFTB('user_group')->where('status!=9')->order("id asc")->paginate(10);
        $page = $group_list->render();
        $this->assign("group_list",$group_list);
        $this->assign("page",$page);
        return view();
    }

    /**
     * @Notes:添加分类
     * @Interface group_add
     * @return \think\response\View|void
     * @author: 子枫
     * @Time: 2019/11/13   11:03 下午
     */
    public function group_add()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isPost()){
            $data = input('post.');
            $data['ctime'] = time();
            // $data = array_merge($data,$this->common_tag);
            deal_meta_data_add('user_group',$data);
        }
        return view();
    }

    /**
     * @Notes:分类修改
     * @Interface group_edit
     * @return \think\response\View|void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author: 子枫
     * @Time: 2019/11/13   11:03 下午
     */
    public function group_edit()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isPost()){
            $data = input('post.');
            deal_meta_data_edit('user_group',$data);
        }
        $res =  ZFTB('user_group')->where(['id'=>input('id')])->find();
        $meta_json = ZFTB('meta_data')->where([['tb','=','user_group'],['post_id','=',$res['id']],['status','<>',9]])->value('meta_data');
        $res['meta'] = json_decode($meta_json,true);
        $this->assign("res",$res);
        return view('user/group_add');
    }

    /**
     * @Notes:密码修改
     * @Interface pwd_edit
     * @return \think\response\View|void
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author: 子枫
     * @Time: 2019/11/13   11:03 下午
     */
    public function pwd_edit()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isPost()){
            $data = input('post.');
            $data['pwd'] = md5('zfcms-'.$data['pwd']);
            $data['token'] = time();
            try {
                $res = ZFTB('admin')->where(['id'=>$data['id']])->update($data);
            }catch (Exception $e) {
                return jserror($e->getMessage());
            }


            if($res){
                session('admin',null);
                return jssuccess('修改成功');
            }else{
                return jserror('修改失败');
            }
        } else{
            $res = session('admin');
            $this->assign('res',$res);
            return view();
        }
    }

    /**
     * @Notes:后台用户信息
     * @Interface admin_info
     * @return \think\response\View|void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author: 子枫
     * @Time: 2019/11/13   11:04 下午
     */
    public function admin_info()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isPost()){
            $data = input('post.');
            try {
                $res = ZFTB('admin')->where(['id'=>$data['id']])->update($data);
                return ZFRetMsg($res,'修改成功','修改失败');
            }catch (Exception $e) {
                return jserror($e->getMessage());
            }
        }
        $id = session('admin.id');
        $res = ZFTB('admin')->where(['id'=>$id])->find();
        $this->assign('res',$res);
        $ga = new GoogleAuthenticator();
        if($res['google_secret']!=''){
            $secret = $res['google_secret'];
        }else{
            $secret = $ga->createSecret();
        }
        $qrCodeUrl = 'http://mctool.wangmingchang.com/api/tool/create_qr_code?t=google&name=zf-'.$id.'&secret='.$secret;
        $this->assign('secret',$secret);
        $this->assign('qrCodeUrl',$qrCodeUrl);
        return view();
    }

    /**
     * @Notes:导出
     * @Interface export
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   11:04 下午
     */
    public function export(){
        admin_role_check($this->z_role_list,$this->mca);
        if (!is_dir('./vendor/phpoffice/phpspreadsheet')) {
            $this->error('PhpSpreadsheet扩展(phpoffice/phpspreadsheet)未安装,请先安装后使用');
        }
        $name='用户表'.date("Y-m-d H-i-s",time());
        // $data=[['aa','aa','cc','dd','ee'],['bb','bb','cc','dd','ee']];
        $data = ZFTB('user')->where(['status'=>1])->select();
        //设置表头：
        $head = ['用户ID', '用户名', '性别', '地址', '注册日期'];
        //数据中对应的字段，用于读取相应数据：
        $keys = ['id','name', 'sex', 'address', 'ctime'];
        zf_excel_export($head,$keys,$data,$name) ;
    }


    private function _update_sql($t=''){
        if($t==1 && $this->is_professional_edition){
            $this->tb_prefix = config()['database']['prefix'];
            $Yun = new \zf\Yun();
            $Yun->tb_field_add("show columns from {$this->tb_prefix}user_group like 'tag'","alter table {$this->tb_prefix}user_group add tag varchar(50) not null");

        }
    }


}

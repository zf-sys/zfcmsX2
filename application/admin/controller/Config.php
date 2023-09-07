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
use think\facade\Config as TConfig;
use Wmc1125\TpFast\Category;
class Config extends Admin
{
    public function __construct (){
        parent::__construct();
        $form_widget = new \app\common\widget\Form();
        $this->assign('form_widget',$form_widget);
    } 

    /**
     * @Notes:网站设置
     * @Interface index
     * @return \think\response\View|void
     * @author: 子枫
     * @Time: 2019/11/13   10:47 下午
     */
    public function index()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        $config = ZFC('webconfig','db','arr');
        if(request()->isPost()){
            $data = input('post.');
            foreach($data as $k=>$vo){
                $config[$k] = $vo;
            }
            $res = ZFTB('config')->where(['key'=>'webconfig'])->cache('webconfig')->update(['value'=>json_encode($config),'token'=>time()]);
            return ZFRetMsg($res,'保存成功','保存失败');
        }
        $type = input('type','网站设置');
        $this->assign("type",$type);
        
        $this->assign("config",$config);
        return view();
    }

    /**
     * @Notes:管理员列表
     * @Interface admin_index
     * @return \think\response\View
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   10:48 下午
     */
    public function admin_index()
    {
        admin_role_check($this->z_role_list,$this->mca);
        $user_list = ZFTB('admin')->where([['status','<>',9]])->order("id asc")->paginate(6);
        $page = $user_list->render();
        $this->assign("user_list",$user_list);
        $this->assign("page",$page);
        return view();
    }

    /**
     * @Notes:管理员增加
     * @Interface admin_add
     * @return \think\response\View|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   10:48 下午
     */
    public function admin_add()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(!request()->isPost()){
            $group_list =  ZFTB('admin_group')->where(['status'=>1])->select(); 
            $this->assign("group_list",$group_list);
            return view();   
        }  
        $data = input('post.');
        $data = array_merge($data,$this->common_tag);
        $data['pwd'] = md5('zfcms-'.$data['pwd']);
        $data['ctime'] = time();
        //判断是否存在
        $is_user =  ZFTB('admin')->where(['name'=>$data['name']])->find();
        if($is_user){
            return jserror('用户名已存在');exit;
        }
        try {
            $res =ZFTB('admin')->insert($data); 
            return ZFRetMsg($res,'新增成功','新增失败');
        }catch (Exception $e) {
            return jserror($e);
        }
    }

    /**
     * @Notes:管理员修改
     * @Interface admin_edit
     * @return \think\response\View|void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author: 子枫
     * @Time: 2019/11/13   10:48 下午
     */
    public function admin_edit()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
    	if(request()->isGet()){
            $res = ZFTB('admin')->where(['id'=>input('id')])->find();
            $this->assign("res",$res);
            $group_list = ZFTB('admin_group')->where(['status'=>1])->select();
            $this->assign("group_list",$group_list);
            return view("config/admin_add");

        } 
        if(request()->isPost()){
            $data = input('post.');
            if($data['name']=='admin' && session('admin')['name']!='admin' ){
                  return jserror('admin账号只能由Admin管理员修改');
            }
            if($data['pwd']!=''){
                $data['pwd'] = md5('zfcms-'.$data['pwd']);
            }else{
                unset($data['pwd']);
            }
            $is_user =  ZFTB('admin')->where(['name'=>$data['name']])->find();
            if($is_user){
                if($is_user['id']!=$data['id']){
                    return jserror('用户名已存在');exit;
                }
            }
            try {
                $res = ZFTB('admin')->where(['id'=>$data['id']])->update($data);
                return ZFRetMsg($res,'更新成功','更新失败');
            }catch (Exception $e) {
                return jserror($e);
            }
        } 
    }

    /**
     * @Notes:管理员分类
     * @Interface admin_group
     * @return \think\response\View
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   10:48 下午
     */
    public function admin_group()
    {
        admin_role_check($this->z_role_list,$this->mca);
        $t = input('t','');
        if($t=='copy'){
            $id = input('id','');
            if($id==''){
                $this->error('参数错误',url('config/admin_group'));
            }
            $res = ZFTB('admin_group')->field('id,mid',true)->where(['id'=>$id])->find();
            if(!$res){
                $this->error('参数错误',url('config/admin_group'));
            }
            $res['name'] = 'copy_'.$res['name'];
            $res['ctime'] = time();
            $res['status'] = 1;
            $is_add = ZFTB('admin_group')->insert($res);
            if($is_add){
                $this->success('复制成功',url('config/admin_group'));
            }else{
                $this->error('复制失败',url('config/admin_group'));
            }
        }
        $group_list = ZFTB('admin_group')->where([['status','<>',9]])->order("id asc")->paginate(10);
        $page = $group_list->render();
        $this->assign("group_list",$group_list);
        $this->assign("page",$page);
        return view();
    }

    /**
     * @Notes:添加分类
     * @Interface admin_group_add
     * @return \think\response\View|void
     * @author: 子枫
     * @Time: 2019/11/13   10:48 下午
     */
    public function admin_group_add()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(!request()->isPost()){
            return view();   
        } 
        $data = input('post.');
        $data = array_merge($data,$this->common_tag);
        $data['ctime'] = time();
        try {
            $res =ZFTB('admin_group')->insert($data);
            return ZFRetMsg($res,'新增成功','新增失败');
        }catch (Exception $e) {
            return jserror($e);
        }
    }

    /**
     * @Notes:分类修改
     * @Interface admin_group_edit
     * @return \think\response\View|void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author: 子枫
     * @Time: 2019/11/13   10:49 下午
     */
    public function admin_group_edit()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
    	if(request()->isGet()){
            $res = ZFTB('admin_group')->where(['id'=>input('id')])->find(); 
            $this->assign("res",$res);
            return view("config/admin_group_add");

        } 
        if(request()->isPost()){
            $data = input('post.');
            try {
                $res = ZFTB('admin_group')->where(['id'=>$data['id']])->update($data);
                return ZFRetMsg($res,'更新成功','更新失败');
            }catch (Exception $e) {
                return jserror($e);
            }
        } 
    }

    /**
     * @Notes:权限设置
     * @Interface admin_group_role
     * @return \think\response\View|void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author: 子枫
     * @Time: 2019/11/13   10:49 下午
     */
    public function admin_group_role()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
    	if(request()->isGet()){
            $res = ZFTB('admin_role')->where(['status'=>1])->order('sort asc,id asc')->select();
            $cat = new category(array('id', 'pid', 'name', 'value')); //初始化无限分类
            $list = $cat->getTree($res); //获取分类数据树结构
            $this->assign("list",$list);
            $res = ZFTB('admin_group')->where(['id'=>input('id')])->find();
            $this->assign("res",$res);
            $role_list = explode(',',$res['role']);
            $this->assign("role_list",$role_list);
            return view();
        } 
        if(request()->isPost()){
            $data = input('post.');
            $data['role'] = implode(',',  $data['role']);
            try {
                $res = ZFTB('admin_group')->where(['id'=>$data['id']])->update($data);
                return ZFRetMsg($res,'更新成功','更新失败');
            }catch (Exception $e) {
                return jserror($e);
            }
        } 
    }

    /**
     * @Notes:权限列表
     * @Interface admin_role
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   10:50 下午
     */
    public function admin_role()
    {
        admin_role_check($this->z_role_list,$this->mca);
        //读取权限数据
        $res = ZFTB('admin_role')->where(['status'=>1])->order('menu desc,sort asc, id asc')->select(); 
        $cat = new category(array('id', 'pid', 'name', 'cname')); //初始化无限分类
        $list = $cat->getTree($res); //获取分类数据树结构
        $this->assign("list",$list);

        $controllers = getControllers('./application/admin/controller');// 控制器
        $controllers = array_merge(array_diff($controllers,['Api','Updatesql','Login']));
        $this->assign("controllers",$controllers);
        return view();
    }

    /**
     * 获取类
     */
    public function get_controller()
    {
        $module = input('module','admin');
        $now_controller_now = [];
        if($module=='admin'){
            $controllers = getControllers('./application/admin/controller');// 控制器
            $controllers = array_merge(array_diff($controllers,['Api','Updatesql','Login']));
            foreach($controllers as $k=>$vo){
                $now_controller_now[$k] = $vo;
            }
        } 
        echo json_encode(array_values($now_controller_now));
    }
    /**
     * @Notes:获取方法
     * @Interface get_action
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   10:51 下午
     */
    public function get_action()
    {
        $control = input('get.control');
        //通过控制器查找已存在的方法
        $now_act = db('admin_role')->field("act")->where('control', $control)->select();
        if(!empty($now_act)){
            foreach($now_act as $k=>$vo){
                $now_act_now[$k] = $vo['act'];
            }
            $now_act_now[] = '__call';
            $now_act_now[] = 'get_menu';
            $actions = getActions('app\admin\controller' . '\\' . $control);
        }else{
            $now_act_now[] = '__call';
            $now_act_now[] = 'get_menu';
            $actions = getActions('app\admin\controller' . '\\' . $control);
        }  
        //筛选后的方法(未添加的)
        $fin_act = array_merge(array_diff($actions,$now_act_now));

        echo json_encode(array_values($fin_act));
    }

    /**
     * @Notes:权限增加
     * @Interface admin_role_add
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   10:52 下午
     */
    public function admin_role_add()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        //判断是否存在
        $val = input('post.');
        if($val['_control']!=''){
            $val['control'] = $val['_control'];
        }
        if($val['_act']!=''){
            $val['act'] = $val['_act'];
        }
        unset($val['_control']);
        unset($val['_act']);

        $value = $val['module'].'/'.$val['control'].'/'.$val['act'];
        $res1 = ZFTB('admin_role')->where(["value"=> $value,'parm'=>$val['parm']])->find();
        if($res1){
            if(in_array($val['act'], ['','0/0','/0','0/','/'])){
                return jserror('已存在该权限');exit;
            }
        }
        $data = $val;
        $data['value'] = $value;
        $data = array_merge($data,$this->common_tag);
        try {
            $res =ZFTB('admin_role')->insert($data); 
            return ZFRetMsg($res,'新增成功','新增失败');
        }catch (Exception $e) {
            return jserror($e);
        }
    }

    /**
     * @Notes:权限修改
     * @Interface admin_role_edit
     * @return \think\response\View|void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author: 子枫
     * @Time: 2019/11/13   10:52 下午
     */
    public function admin_role_edit()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
    	if(request()->isGet()){
            $res = ZFTB('admin_role')->where(['status'=>1])->order('menu desc,sort asc, id asc')->select();
            $cat = new category(array('id', 'pid', 'name', 'cname')); //初始化无限分类
            $list = $cat->getTree($res); //获取分类数据树结构
            $list[999] =[
                'id'=>0,
                'name'=>'顶级目录',
                'cname'=>'顶级目录'
            ];
            $this->assign("list",$list);           
            $info = ZFTB('admin_role')->where(["id"=> input('id')])->find();
            $this->assign("res",$info);
            return view("config/admin_role_add");

        } 
        if(request()->isPost()){
            $data = input('post.');
            $data['token'] = time();
            try {
                $res = ZFTB('admin_role')->where(['id'=>$data['id']])->update($data);
                return ZFRetMsg($res,'更新成功','更新失败');  
            }catch (Exception $e) {
                return jserror($e);
            }
        } 
    }

    
    

    /**
     * @Notes:操作日志
     * @Interface action_log
     * @return \think\response\View
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   10:54 下午
     */
    public function action_log()
    {
        admin_role_check($this->z_role_list,$this->mca);
        $list = ZFTB('admin_log')->where(['status'=>1])->order("id desc")->paginate(10);
        $page = $list->render();
        $this->assign("list",$list);
        $this->assign("page",$page);
        return view();
    }


//自定义参数
    public function custom_config()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isPost()){
            $data = input("post.");
            if($data['key']=='' ){
                return jserror('key不能为空');exit;
            }
            //判断键是否存在
            $is = ZFTB('config')->where([['key','=',$data['key']],['status','<>','9']])->find();
            if($is){
                return jserror('该键已存在');exit;
            }
            try {
                $res =ZFTB('config')->insert($data);
                return ZFRetMsg($res,'新增成功','新增失败');
            }catch (Exception $e) {
                return jserror($e);
            }
        }

        $list = ZFTB('config')->where([['status','<>',9]])->order("sort desc,id asc")->select();
        $this->assign('list',$list);
        return view();
    }

    public function custom_config_edit()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isPost()){
            $data = input("post.");
            try {
                $res =  ZFTB('config')->where(['id'=>$data['id']])->update($data);
                return ZFRetMsg($res,'修改成功','修改失败'); 
            }catch (Exception $e) {
                return jserror($e);
            }
        }
        $id = input('id','');
        $key = input('key','');
        if($id!=''){
            $where[] = ['id','=',$id];
        }elseif($key!=''){
            $where[] = ['key','=',$key];
        }
        $res = ZFTB('config')->where($where)->find();
        $this->assign('res',$res);
        return view();
    }
     /*
    oss参数设置
    */
    public function oss_config()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isPost()){
            $data = input('post.');
            $res = extraconfig(input('post.'),'oss');
            return ZFRetMsg($res,'保存成功','保存失败');
        }
        $type = input('type','阿里云');
        $this->assign("type",$type);
        $this->assign("config",config()['oss']);
        return view();
    }

    public function email()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isPost()){
            $data = input('post.');
            $res = extraconfig(input('post.'),'email');
            return ZFRetMsg($res,'保存成功','保存失败');
        }
       
        $this->assign("config",config()['email']);
        return view();
    }
    public function test_email(){
        admin_role_check($this->z_role_list,$this->mca,1);
         if(request()->isPost()){
            $email = input('ee');
            $code = '测试邮件内容';
            $title = '测试邮件';
            $res = send_email($email,$title,$code);
            if($res=='ok'){
                return jssuccess('发送成功');
            }else{
                return $res;
            }
        } 
    }
    public function version()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isPost()){
            $data = input('post.');
            $res = extraconfig(input('post.'),'version');
            return ZFRetMsg($res,'保存成功','保存失败');
        }
       
        $this->assign("config",config()['version']);
        return view();
    }
    public function zf_auth()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isPost()){
            $data = input('post.');
            $res = extraconfig(input('post.'),'zf_auth');
            return ZFRetMsg($res,'保存成功','保存失败');
        }
        $this->assign("config",config()['zf_auth']);
        return view();
    }



    
}

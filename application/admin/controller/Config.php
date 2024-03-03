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
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
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
        $_config_json = ZFTB('config')->where(['key'=>'webconfig'])->value('value');
        // 匹配是否有html标签,如果有,查看其中是否有"  如果有修改html标签中的双引号为\"
        $_config_json = preg_replace_callback('/<[^>]*>/',function($matches){
            if(strpos($matches[0],'"') && strpos($matches[0],'\"')===false){
                $matches[0] = str_replace('"','\\"',$matches[0]);
            }
            return $matches[0];
        },$_config_json);
        $config = json_decode($_config_json,true);

        if(request()->isPost()){
            if($config==''){
                $config = [];
            }
            $data = input('post.');
            foreach($data as $k=>$vo){
                if(substr($k,0,1)=='_'){
                    unset($data[$k]);
                    continue;
                }
                $config[$k] = $vo;
            }
            // dd($config);
            $res = ZFTB('config')->where(['key'=>'webconfig'])->cache('webconfig')->update(['value'=>json_encode($config),'token'=>time()]);
            return ZFRetMsg($res,'保存成功','保存失败');
        }
        $type = input('type','网站设置');
        $this->assign("type",$type);
        $_t = input('_t','');
        $this->assign("_t",$_t);
        $this->assign("config",$config);
        $api_domain = config()['version']['api_domain'];
        $this->assign("api_domain",$api_domain);
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
        $user_list = ZFTB('admin')->where([['status','<>',9]])->order("id asc")->paginate(10,false,['query' => request()->param()]);
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
            return jserror($e->getMessage());
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
                return jserror($e->getMessage());
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
        $group_list = ZFTB('admin_group')->where([['status','<>',9]])->order("id asc")->paginate(10,false,['query' => request()->param()]);
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
            return jserror($e->getMessage());
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
                return jserror($e->getMessage());
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
                return jserror($e->getMessage());
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
            return jserror($e->getMessage());
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
                return jserror($e->getMessage());
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
        $list = ZFTB('admin_log')->where(['status'=>1])->order("id desc")->paginate(10,false,['query' => request()->param()]);
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
                return jserror($e->getMessage());
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
                return jserror($e->getMessage());
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
        $config = ZFC('oss_config','db','arr');
        if(request()->isPost()){
            if($config==''){
                $config = [];
            }
            $data = input('post.');
            foreach($data as $k=>$vo){
                $config[$k] = $vo;
            }
            if(ZFTB('config')->where(['key'=>'oss_config'])->find()){
                $res = ZFTB('config')->where(['key'=>'oss_config'])->cache('oss_config')->update(['value'=>json_encode($config),'token'=>time()]);
            }else{
                $res = ZFTB('config')->cache('oss_config')->insert(['key'=>'oss_config','type'=>'system','value'=>json_encode($config),'token'=>time()]);
            }
            return ZFRetMsg($res,'保存成功','保存失败');
        }
        $type = input('type','阿里云');
        $this->assign("type",$type);
        $_t = input('_t','');
        $this->assign("_t",$_t);
        $this->assign("config",$config);
        return view();
    }

    public function email()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        // if(request()->isPost()){
        //     $data = input('post.');
        //     $res = extraconfig(input('post.'),'email');
        //     return ZFRetMsg($res,'保存成功','保存失败');
        // }
       
        // $this->assign("config",config()['email']);
        $config = ZFC('email_config','db','arr');
        if(request()->isPost()){
            if($config==''){
                $config = [];
            }
            $data = input('post.');
            foreach($data as $k=>$vo){
                $config[$k] = $vo;
            }
            if(ZFTB('config')->where(['key'=>'email_config'])->find()){
                $res = ZFTB('config')->where(['key'=>'email_config'])->cache('email_config')->update(['value'=>json_encode($config),'token'=>time()]);
            }else{
                $res = ZFTB('config')->cache('email_config')->insert(['key'=>'email_config','type'=>'system','value'=>json_encode($config),'token'=>time()]);
            }
            return ZFRetMsg($res,'保存成功','保存失败');
        }
        $type = input('type','');
        $this->assign("type",$type);
        $_t = input('_t','');
        $this->assign("_t",$_t);
        $this->assign("config",$config);

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
            if(!$this->is_professional_edition){
                return ZFRetMsg(false,'','Yun.php文件不存在');
            }
            $is_sq = $this->Yun->_save_license_convert_sc();
            if($is_sq['code']==0){
                return ZFRetMsg(false,'',$is_sq['msg']);
            }
            if($this->Yun->_get_site_auth('','',1)){
                return ZFRetMsg(true,'更新成功','');
            } else{
                return ZFRetMsg(false,'','更新失败');
            }
        }
        $this->assign("config",config()['zf_auth']);
        return view();
    }

    /**
     * 登录日志
     * 230909新增
     */
    public function admin_login_log()
    {
        admin_role_check($this->z_role_list,$this->mca);
        $list = ZFTB('admin_login_log')->where(['status'=>1])->order("id desc")->paginate(10,false,['query' => request()->param()]);
        $page = $list->render();
        $this->assign("list",$list);
        $this->assign("page",$page);
        return view();
    }
    /**
     * 异常日志
     * 230909新增
     */
    public function exception_log()
    {
        admin_role_check($this->z_role_list,$this->mca);
        $list = ZFTB('exception_log')->where(['status'=>1])->order("id desc")->paginate(10,false,['query' => request()->param()]);
        $page = $list->render();
        $this->assign("list",$list);
        $this->assign("page",$page);
        return view();
    }
    /**
     * 新增meta字段列表
     * 20231018新增
     */
    public function meta_key_list(){
        admin_role_check($this->z_role_list,$this->mca);
        $list = ZFTB('meta_key')->where([['status','<>',9]])->order("id desc")->paginate(10,false,['query' => request()->param()]);
        $page = $list->render();
        $this->assign("list",$list);
        $this->assign("page",$page);
        return view();
    }
    /**
     * 新增meta_key_add
     * 20231018新增
     */
    public function meta_key_add(){
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isPost()){
            $data = input('post.');
            $data['ctime'] = time();
            $data['token'] = time();
            $data['status'] = 1;
            if($data['key']==''){
                return jserror('key不能为空');exit;
            }
            if($data['name']==''){
                return jserror('名称不能为空');exit;
            }
            //判断key只能为英文和数字和_,不能数字开头
            if(!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/',$data['key'])){
                return jserror('key只能为英文数字和_,不能数字开头');exit;
            } 
            //判断此key和tb是否存在
            $is = db('meta_key')->where([['key','=',$data['key']],['tb','=',$data['tb']],['status','<>','9']])->find();
            if($is){
                return jserror('该表内此key已存在');exit;
            }
            $res = ZFTB('meta_key')->insert($data);
            return ZFRetMsg($res,'新增成功','新增失败');
        }
        $tb_list = [
            ['id'=>'post','name'=>'内容表'],
            ['id'=>'category','name'=>'栏目表'],
            // ['id'=>'user','name'=>'用户表'],
        ];
        $this->assign("tb_list",$tb_list);        
        return view();
    }
    /**
     * 编辑meta_key_edit
     */
    public function meta_key_edit(){
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isPost()){
            $data = input('post.');
            $data['token'] = time();
            $data['utime'] = time();
            if($data['key']==''){
                return jserror('key不能为空');exit;
            }
            if($data['name']==''){
                return jserror('名称不能为空');exit;
            }
            //判断key只能为英文和数字和_,不能数字开头
            if(!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/',$data['key'])){
                return jserror('key只能为英文数字和_,不能数字开头');exit;
            } 
            //判断此key和tb是否存在
            $is = db('meta_key')->where([['key','=',$data['key']],['tb','=',$data['tb']],['status','<>','9'],['id','<>',$data['id']]])->find();
            if($is){
                return jserror('该表内此key已存在');exit;
            }
            $res = ZFTB('meta_key')->where(['id'=>$data['id']])->update($data);
            return ZFRetMsg($res,'更新成功','更新失败');
        }
        $id = input('id','');
        $res = ZFTB('meta_key')->where(['id'=>$id])->find();
        $this->assign("res",$res);
        $tb_list = [
            ['id'=>'post','name'=>'内容表'],
            ['id'=>'category','name'=>'栏目表'],
            // ['id'=>'user','name'=>'用户表'],
        ];
        $this->assign("tb_list",$tb_list);        
        return view('config/meta_key_add');
    }
    /**
     * 工具页面
     */
    public function tool(){
        $t = input('t','');
        if(in_array($t,['del_temp','del_log','del_backup','del_upgrade','del_theme_cache','del_zfcms','del_cache_aiauth'])){
            if($t=='del_temp'){
                $dir = './runtime/temp';
            }elseif($t=='del_log'){
                $dir = './runtime/log';
            }elseif($t=='del_backup'){
                $dir = './backup';
            }elseif($t=='del_upgrade'){
                $dir = './upgrade';
            }elseif($t=='del_theme_cache'){
                $dir = './cache';
            }elseif($t=='del_zfcms'){
                $dir = './runtime/ZFCMS';
            }elseif($t=='del_cache_aiauth'){
                session('aihost_authhost_token',null);
                return jssuccess('操作成功');
            }
                
            $file = new \lib\File();
            if(!file_exists($dir)){
                return jserror('文件夹不存在');
            }
            if($t=='del_zfcms'){
                if (file_exists($dir)) {
                    $res = extraconfig(['email'=>'','key'=>'','sc'=>''],'zf_auth');
                    unlink($dir);
                    return ZFRetMsg($res,'操作成功','操作失败');
                } else {
                    return jserror('文件不存在');
                }
            }else{
                $r = $file->del_dir($dir);
            }
            if($r){
                mkdir($dir);
                return jssuccess('操作成功');
            }else{
                return jserror('操作失败');
            }
        }

        $ys_list = [
            'autosize'=>[
              'name'=>'自动大小',
              'dir'=>'./public/static/zfcms/style/autosize',
              'static_name'=>'autosize',
              'msg'=>'这个样式可以自动适应屏幕大小 路径:./public/static/zfcms/style/autosize'
            ],
            'echarts'=>[
                'name'=>'echarts插件',
                'dir'=>'./public/static/zfcms/style/echarts',
                'static_name'=>'echarts',
                'msg'=>'图表插件 路径:./public/static/zfcms/style/echarts'
            ],
            'bootstrap'=>[
                'name'=>'bootstrap',
                'dir'=>'./public/static/zfcms/style/bootstrap',
                'static_name'=>'bootstrap',
                'msg'=>'bootstrap样式 路径:./public/static/zfcms/style/bootstrap'
            ],
            'wangEditor'=>[
                'name'=>'wangEditor',
                'dir'=>'./public/static/zfcms/style/wangEditor',
                'static_name'=>'wangEditor',
                'msg'=>'富文本编辑器    路径:./public/static/zfcms/style/wangEditor'
            ],
            'ztree'=>[
                'name'=>'ztree',
                'dir'=>'./public/static/zfcms/style/ztree',
                'static_name'=>'ztree',
                'msg'=>'树形菜单   路径:./public/static/zfcms/style/ztree'
            ],
            'tinymce515'=>[
                'name'=>'tinymce515',
                'dir'=>'./public/static/zfcms/style/tinymce515',
                'static_name'=>'tinymce515',
                'msg'=>'富文本编辑器 路径:./public/static/zfcms/style/tinymce515'
            ],
            'webuploader'=>[
                'name'=>'webuploader',
                'dir'=>'./public/static/zfcms/style/webuploader',
                'static_name'=>'webuploader',
                'msg'=>'文件上传 路径:./public/static/zfcms/style/webuploader'
            ],
            'xiumi'=>[
                'name'=>'xiumi',
                'dir'=>'./public/static/zfcms/style/xiumi',
                'static_name'=>'xiumi',
                'msg'=>'秀米编辑器 路径:./public/static/zfcms/style/xiumi'
            ],
            'layui'=>[
                'name'=>'layui',
                'dir'=>'./public/static/zfcms/style/layui',
                'static_name'=>'layui',
                'msg'=>'layui样式 路径:./public/static/zfcms/style/layui'
            ],
            'ueditor'=>[
                'name'=>'ueditor',
                'dir'=>'./public/static/zfcms/style/ueditor',
                'static_name'=>'ueditor',
                'msg'=>'百度编辑器 路径:./public/static/zfcms/style/ueditor'
            ],
            'input-select'=>[
                'name'=>'input-select',
                'dir'=>'./public/static/zfcms/style/input-select',
                'static_name'=>'input-select',
                'msg'=>'下拉选择 路径:./public/static/zfcms/style/input-select'
            ],
            'meditor'=>[
                'name'=>'meditor',
                'dir'=>'./public/static/zfcms/style/meditor',
                'static_name'=>'meditor',
                'msg'=>'markdown编辑器 路径:./public/static/zfcms/style/meditor'
            ],
            'viewer'=>[
                'name'=>'viewer',
                'dir'=>'./public/static/zfcms/style/viewer',
                'static_name'=>'viewer',
                'msg'=>'图片查看器 路径:./public/static/zfcms/style/viewer'
            ],
            'laydate'=>[
                'name'=>'laydate',
                'dir'=>'./public/static/zfcms/style/laydate',
                'static_name'=>'laydate',
                'msg'=>'日期选择器 路径:./public/static/zfcms/style/laydate'
            ],
            'layer'=>[
                'name'=>'layer',
                'dir'=>'./public/static/zfcms/style/layer',
                'static_name'=>'layer',
                'msg'=>'弹出层 路径:./public/static/zfcms/style/layer'
            ],
            'input-tag'=>[
                'name'=>'input-tag',
                'dir'=>'./public/static/zfcms/style/input-tag',
                'static_name'=>'input-tag',
                'msg'=>'标签输入 路径:./public/static/zfcms/style/input-tag'
            ],
            'fcup'=>[
                'name'=>'fcup',
                'dir'=>'./public/static/zfcms/style/fcup',
                'static_name'=>'fcup',
                'msg'=>'文件上传 路径:./public/static/zfcms/style/fcup'
            ],
            'jquery-1.8.3'=>[
                'name'=>'jquery-1.8.3',
                'dir'=>'./public/static/zfcms/style/jquery-1.8.3',
                'static_name'=>'jquery-1.8.3',
                'msg'=>'jquery-1.8.3 路径:./public/static/zfcms/style/jquery-1.8.3'
            ],
            'tailwind'=>[
                'name'=>'tailwind',
                'dir'=>'./public/static/zfcms/style/tailwind',
                'static_name'=>'tailwind',
                'msg'=>'tailwind 路径:./public/static/zfcms/style/tailwind'
            ],

            // "autosize","bootstrap","wangEditor","ztree","images","tinymce515","webuploader","xiumi","layui","ueditor","input-select","meditor","viewer","laydate","layer","input-tag",'fcup','jquery-1.8.3'
            
        ];
        if($t=='down_static'){
            $static_name = input('static_name','');
            if(!isset($ys_list[$static_name])){
                return jserror('参数错误');
            }
            $static_res = $ys_list[$static_name];
            $url = config('version.api_domain').'/addons/zf_store_softclientv2.api/down_static?name='.$static_res['static_name'].'&site_version='.config('version.version');
            $client = new Client([
                'headers'=>[
                    'User-Agent' => 'okhttp/3.8.1',
                    'Host' => 'api.aa.com',
                    'Connection' => 'Keep-Alive',
                    'Accept-Encoding' => 'gzip',
                    "Content-type" => 'application/json',
                    "Origin" => get_domain(),
                ]
            ]);
            try{
                $result = $client->get($url);
                $static_url = $result->getBody()->getContents();
            } catch (\GuzzleHttp\Exception\ServerException $e) {
                return jserror('获取下载地址失败1');
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                return jserror('获取下载地址失败2');
            }catch (Exception $e) {
                return jserror('获取下载地址失败');
            }
            // 下载到runtime/static/样式名, 然后解压并覆盖
            $_name =$static_res['static_name'];
            // $static_content = file_get_contents($static_url);
            try{
                $static_result = $client->get($static_url);
                $static_content = $static_result->getBody()->getContents();
            } catch (\GuzzleHttp\Exception\ServerException $e) {
                return jserror('获取下载内容失败1');
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                return jserror('获取下载内容失败2');
            } catch (Exception $e) {
                return jserror('获取下载内容失败');
            } 
            //清空目录
            $file = new \lib\File();
            $file->del_dir('./runtime/statics/'.$static_res['static_name']);
            if(!is_dir('./runtime/statics/'.$static_res['static_name'])){
                mkdir('./runtime/statics/'.$static_res['static_name'],0777,true);
            }
            if(!file_put_contents('./runtime/statics/'.$static_res['static_name'].'/down_file.zip',$static_content)){
                return jserror('./runtime/statics/'.$static_res['static_name'].'/down_file.zip 无写入权限');
            }
            //解压
            $zip = new \ZipArchive();
            if ($zip->open('./runtime/statics/'.$static_res['static_name'].'/down_file.zip') === TRUE) {
                $zip->extractTo('./runtime/statics/'.$static_res['static_name']);
                $zip->close();
            } else {
                return jserror('解压失败');
            }
            //删除zip
            unlink('./runtime/statics/'.$static_res['static_name'].'/down_file.zip');
            // 转移
            $is_copy = $file->copy_dir('./runtime/statics/'.$static_res['static_name'],$static_res['dir']);
            if($is_copy){
                $file->del_dir('./runtime/statics/'.$static_res['static_name']);
                return jssuccess('操作成功');
            }else{
                return jserror('操作失败');
            }
        }
        if($t=='del_static'){
            $static_name = input('static_name','');
            if(!isset($ys_list[$static_name])){
                return jserror('参数错误');
            }
            $static_res = $ys_list[$static_name];
            $file = new \lib\File();
            $r = $file->del_dir($static_res['dir']);
            if($r){
                return jssuccess('操作成功');
            }else{
                return jserror('操作失败');
            }
        }
        $this->assign('ys_list',$ys_list);



        $addons_list = [
            'zfcms_ai'=>[
                'name'=>'AI插件',
                'dir'=>'./addons/zfcms_ai',
                'addons_name'=>'zfcms_ai',
                'msg'=>'超强AI插件'
            ],
            'zfcms_plugin_store'=>[
                'name'=>'插件商城',
                'dir'=>'./addons/zfcms_plugin_store',
                'addons_name'=>'zfcms_plugin_store',
                'msg'=>'插件商城中心'
            ],
            'zf_developer2'=>[
                'name'=>'开发者工具',
                'dir'=>'./addons/zf_developer2',
                'addons_name'=>'zf_developer2',
                'msg'=>'开发者工具'
            ]
        ];
        $this->assign('addons_list',$addons_list);
        if($t=='down_addons'){
            $addons_name = input('addons_name','');
            if(!isset($addons_list[$addons_name])){
                return jserror('参数错误');
            }
            $addons_res = $addons_list[$addons_name];
            $url = config('version.api_domain').'/addons/zf_store_softclientv2.api/down_addons?name='.$addons_res['addons_name'].'&site_version='.config('version.version');
            $client = new Client([
                'headers'=>[
                    'User-Agent' => 'okhttp/3.8.1',
                    'Host' => 'api.aa.com',
                    'Connection' => 'Keep-Alive',
                    'Accept-Encoding' => 'gzip',
                    "Content-type" => 'application/json',
                    "Origin" => get_domain(),
                ]
            ]);
            try{
                $result = $client->get($url);
                $addons_url = $result->getBody()->getContents();
            } catch (\GuzzleHttp\Exception\ServerException $e) {
                return jserror('获取下载地址失败1');
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                return jserror('获取下载地址失败2');
            }catch (Exception $e) {
                return jserror('获取下载地址失败');
            }
            // 下载到runtime/addons/样式名, 然后解压并覆盖
            $_name =$addons_res['addons_name'];
            // $addons_content = file_get_contents($addons_url);
            try{
                $addons_result = $client->get($addons_url);
                $addons_content = $addons_result->getBody()->getContents();
            } catch (\GuzzleHttp\Exception\ServerException $e) {
                return jserror('获取下载内容失败1');
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                return jserror('获取下载内容失败2');
            } catch (Exception $e) {
                return jserror('获取下载内容失败');
            }
            //清空目录
            $file = new \lib\File();
            $file->del_dir('./runtime/addons/'.$addons_res['addons_name']);
            if(!is_dir('./runtime/addons/'.$addons_res['addons_name'])){
                mkdir('./runtime/addons/'.$addons_res['addons_name'],0777,true);
            }
            if(!file_put_contents('./runtime/addons/'.$addons_res['addons_name'].'/down_file.zip',$addons_content)){
                return jserror('./runtime/addons/'.$addons_res['addons_name'].'/down_file.zip 无写入权限');
            }
            //解压
            $zip = new \ZipArchive();
            if ($zip->open('./runtime/addons/'.$addons_res['addons_name'].'/down_file.zip') === TRUE) {
                $zip->extractTo('./runtime/addons/'.$addons_res['addons_name']);
                $zip->close();
            } else {
                return jserror('解压失败');
            }
            //删除zip
            unlink('./runtime/addons/'.$addons_res['addons_name'].'/down_file.zip');
            // 转移
            $is_copy = $file->copy_dir('./runtime/addons/'.$addons_res['addons_name'],$addons_res['dir']);
            if($is_copy){
                $file->del_dir('./runtime/addons/'.$addons_res['addons_name']);
                return jssuccess('操作成功');
            }else{
                return jserror('操作失败');
            }
        }
        if($t=='del_addons'){
            $addons_name = input('addons_name','');
            if(!isset($addons_list[$addons_name])){
                return jserror('参数错误');
            }
            $static_res = $addons_list[$addons_name];
            $file = new \lib\File();
            $r = $file->del_dir($static_res['dir']);
            if($r){
                return jssuccess('操作成功');
            }else{
                return jserror('操作失败');
            }
        }
        
        
        



        return view();
    }




    
}

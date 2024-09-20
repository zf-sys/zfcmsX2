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
use app\admin\model\roleModel;
use think\facade\Session;
use think\facade\Cache;
use think\facade\Request;
use think\Db;
use Wmc1125\TpFast\Database as dbOper;
use app\admin\controller\Common;

class Index extends Admin
{
    public function __construct (){
        parent::__construct();

    }

    /**
     * @Notes:首页
     * @Interface index
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   10:55 下午
     */
    public function index()
    {
        $menu = ZFTB('admin_role')->order("sort asc")->where("status!=9")->select();
        $role_list =  ZFTB('admin_group')->where(['id'=>session('admin.gid')])->value('role');
        foreach($menu as $k=>$vo){
            if(!in_array($vo['id'], explode(',', $role_list))){
                unset($menu[$k]);
            }
        }
        $this->assign("menu",$menu);
        return view("index");
    }

    /**
     * @Notes:欢迎页
     * @Interface welcome
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   10:55 下午
     */
    public function welcome()
    {
        admin_role_check($this->z_role_list,$this->mca);
        $tt = input('tt','');
        if($tt=='getData'){
            if (!request()->isAjax()) {
                return json(['code' => 0, 'msg' => '非法请求']);
            }
            try {
                $ZfAuth = new \zf\ZfAuth();
                $site_org_res = $ZfAuth->get_location_auth_info();
                return json(['code' => 1, 'msg' => 'success', 'data' => $site_org_res]);
            } catch (\Exception $e) {
                return json(['code' => 0, 'msg' => $e->getMessage()]);
            }
        }
        
       
        $post_list = ZFTB('post')
                    ->where([['status','=',1],['ctime','<>','']])
                    ->field("DATE_FORMAT(FROM_UNIXTIME(ctime),'%Y-%m-%d') as formatted_date, count(*) as total")
                    ->group("formatted_date")
                    ->order('formatted_date asc')
                    ->select();
        $this->assign('post_list',$post_list);
        $site_version = config()['version'];
        $this->assign('site_version',$site_version);
        //统计  用户/文章/浏览量
        $sum['user'] = db('user')->where([['status','<>',9]])->count();
        $sum['post'] = db('post')->where([['status','<>',9]])->count();
        $sum['hits'] = db('post')->where([['status','<>',9]])->sum('hits');
        $this->assign('sum',$sum);

        // $welcome_type = ZFC("webconfig.admin_welcome_type");
        // if($welcome_type){
        //     $tpl = 'index/welcome'.$welcome_type;
        // }else{
        //     $tpl = 'index/welcome1';
        // }
        if($this->is_professional_edition){
            $tpl = 'index/welcome_authorize';
        }else{
            $tpl = 'index/welcome_community';
        }
        do_action('admin_welcome_after',$this);
        return view($tpl);
    }
    /**
     * @Notes:清除数据库的垃圾箱文件
     * @Interface db_clear
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author: 子枫
     * @Time: 2019/11/13   10:56 下午
     */
    public function db_clear(){
        admin_role_check($this->z_role_list,$this->mca);
        $t = input('t','');
        if($t=='log'){
                ZFTB('admin_log')->where(['status'=>1])->update(['status'=>9]);
                $this->success('清除完毕');
        }else{
            // $config=array(
            //     'path'     => './db/',//数据库备份路径
            // );
            $tables = Db::query("SHOW TABLE STATUS");
            foreach($tables as $k=>$vo){
                Db::table($vo['Name'])->where(['status'=>9])->delete();
            }
            $this->success('清除完毕');
        }
    }
    public function temp_clear(){
        admin_role_check($this->z_role_list,$this->mca);
        del_dir('./runtime/temp');
        $this->success('清除完毕');
    }
    public function change_lang(){
        admin_role_check($this->z_role_list,$this->mca);
        $lang = input('lang','');
        session('zf_lang',$lang);
        $this->success('切换语言中');
    }
    public function get_menu(){
        if(ZFC("webconfig.site_path")!='' ){
            $site_path = '/'.ZFC("webconfig.site_path");
        }else{
            $site_path = '';
        }

        $arr['homeInfo'] = [
            'title'=>'首页',
            'href'=>url('index/welcome')
        ];
        $admin_logo_pic = ZFC("webconfig.admin_logo_pic");
        if($admin_logo_pic==''){
            $admin_logo_pic = '//storage-x1.90ckm.com/zfcms/logo_white.png';
        }
        $arr['logoInfo'] = [
            'title'=>'内容管理系统',
            'image'=>$admin_logo_pic ,
            'href'=>'//www.zf-sys.com/',
        ];
        //用户权限
        $admin = session('admin');
        $role_str = db('admin_group')->where(['id'=>$admin['gid']])->value('role');
        $role_arr = explode(',', $role_str);
        $_menu = ZFTB('admin_role')->field("id,pid, name as title,icon,value,parm,'_self' as target")->order("sort asc")->where([['menu','=',1],['status','=',1],['pid','=','0']])->select();
        $menu = [];
        $select_menu = [];
        foreach($_menu as $k=>$vo){
            if($admin['gid']!='1' && !in_array($vo['id'], $role_arr)){
                unset($_menu[$k]);
                continue;
            }
            $select_menu[] = $vo['id'];

            $_menu[$k]['href'] = $site_path.'/'.$vo['value'].'?'.$vo['parm'];
            $_menu[$k]['child'] = ZFTB('admin_role')->field("id,pid, name as title,icon,value,parm,'_self' as target")->order("sort asc")->where([['menu','=',1],['status','=',1],['pid','=',$vo['id']]])->select();
            foreach($_menu[$k]['child'] as $kk=>$vv){
                if($admin['gid']!='1' && !in_array($vv['id'], $role_arr)){
                    unset($_menu[$k]['child'][$kk]);
                    continue;
                }
                $_menu[$k]['href'] = '';
                $_menu[$k]['child'][$kk]['href'] = $site_path.'/'.$vv['value'].'?'.$vv['parm'];
                $_menu[$k]['child'][$kk]['child'] = ZFTB('admin_role')->field("id,pid, name as title,icon,value,parm,'_self' as target")->order("sort asc")->where([['menu','=',1],['status','=',1],['pid','=',$vv['id']]])->select();
                foreach($_menu[$k]['child'][$kk]['child'] as $kkk=>$vvv){
                    if($admin['gid']!='1' && !in_array($vvv['id'], $role_arr)){
                        unset($_menu[$k]['child'][$kk]['child'][$kkk]);
                        continue;
                    }
                    $_menu[$k]['child'][$kk]['href']='';
                    $_menu[$k]['child'][$kk]['child'][$kkk]['href'] = $site_path.'/'.$vvv['value'].'?'.$vvv['parm'];
                }

            }
            $menu[] = $_menu[$k];
        }
        $menu = array_values($menu);
        foreach($menu as $k=>$vo){
            $menu[$k]['child'] = array_values($vo['child']);
            foreach($menu[$k]['child'] as $kk=>$vv){
                $menu[$k]['child'][$kk]['child'] = array_values($vv['child']);
            }

        }

        $ret_menu = apply_filters('admin_menu_append',$menu,'array');
        $arr['menuInfo'] = $ret_menu;
        return $arr;
    }
    /**
     * 设置二级密码
     */
    public function two_password(){
        if(request()->isPost()){
            $admin = session('admin');
            $two_pwd = input('two_pwd','');
            if($two_pwd==''){
                return jserror('请输入二级密码');
            }
            $admin = db('admin')->where(['id'=>$admin['id']])->find();
            if(!$admin){
                return jserror('请先登录');
            }
            if($admin['two_pwd']!=$two_pwd){
                return jserror('二级密码错误');
            }
            session('admin_two_pwd','11');
            $_two_pwd = session('admin_two_pwd');
            if(!$_two_pwd){
                return jserror('session未开启');
            }
            $url = session('zadmin_two_tap_url');
            return jssuccess('二级验证通过',$url);

        }
        return view();
    }


}
class AMenu{
    public function __construct ($menu_ini=[]){
        $this->menu = $menu_ini;
    }
    public function set($menu){
        array_push($this->menu,$menu);
    }
    public function get(){
        return $this->menu;
    }

}
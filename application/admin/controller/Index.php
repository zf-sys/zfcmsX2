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
        if((config('zf_auth.email')=='' || config('zf_auth.key')=='' || config('zf_auth.sc')=='') && $this->is_professional_edition){
            $this->redirect(url('zfyun/authentication_sys'));
        }
        if($this->is_professional_edition){
        	//授权查询
	        $ZfAuth = new \zf\ZfAuth();
	        $upg_msg = $ZfAuth->get_location_auth_info();
	        $site_info = $ZfAuth->get_siteplugin_info();
            $this->assign('upg_msg',$upg_msg);
            if(isset($site_info['msg'])){
                $this->assign('site_info',$site_info['msg']);
            }else{
    	        $this->assign('site_info',null);
            }
        }else{
        	$this->assign('upg_msg',['code'=>2,'msg'=>'<span style="color:red">社区版</span>']);
	        $this->assign('site_info',null);
        }
       
        $post_list = ZFTB('post')
                    ->where([['status','=',1],['ctime','<>','']])
                    ->field("DATE_FORMAT(FROM_UNIXTIME(ctime),'%Y-%m-%d') as date,count(*) as total")
                    ->group("DATE_FORMAT(FROM_UNIXTIME(ctime),'%Y-%m-%d')")    
                    ->order('ctime asc')
                    ->select();
        $this->assign('post_list',$post_list);
        $site_version = config()['version'];
        $this->assign('site_version',$site_version);
        //统计  用户/文章/浏览量
        $sum['user'] = db('user')->where([['status','<>',9]])->count();
        $sum['post'] = db('post')->where([['status','<>',9]])->count();
        $sum['hits'] = db('post')->where([['status','<>',9]])->sum('hits');
        $this->assign('sum',$sum);

        $welcome_type = config('web.admin_welcome_type');
        if($welcome_type){
            $tpl = 'index/welcome'.$welcome_type;
        }else{
            $tpl = 'index/welcome1';
        }
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
        if(isset(config()['web']['site_path']) && config()['web']['site_path']!='' ){
            $site_path = '/'.config()['web']['site_path'];
        }else{
            $site_path = '';
        }

        $arr['homeInfo'] = [
            'title'=>'首页',
            'href'=>url('index/welcome')
        ];
        if( !isset(config()['web']['admin_logo_pic']) || config()['web']['admin_logo_pic']==''){ 
            $admin_logo_pic = 'http://oss002.wangmingchang.com/uploads/0bf88d0eaaa69d2bd0cdcd974e190115/20211202_4279820211202091846.png';
        }else{ 
            $admin_logo_pic = config()['web']['admin_logo_pic'];
        }
        $arr['logoInfo'] = [
            'title'=>'内容管理系统',
            'image'=>$admin_logo_pic ,
            'href'=>'http://www.zf-sys.com/',
        ];
        // $menu = ZFTB('admin_role')->field("id,pid, name as title,icon,value,parm,'_self' as target")->order("sort asc")->where([['menu','=',1],['status','=',1],['pid','=','0']])->select();
        // foreach($menu as $k=>$vo){
        //     $menu[$k]['href'] = $site_path.'/'.$vo['value'].'?'.$vo['parm'];
        //     $menu[$k]['child'] = ZFTB('admin_role')->field("id,pid, name as title,icon,value,parm,'_self' as target")->order("sort asc")->where([['menu','=',1],['status','=',1],['pid','=',$vo['id']]])->select();
        //     foreach($menu[$k]['child'] as $kk=>$vv){
        //         $menu[$k]['href'] = '';
        //         $menu[$k]['child'][$kk]['href'] = $site_path.'/'.$vv['value'].'?'.$vv['parm'];
        //         $menu[$k]['child'][$kk]['child'] = ZFTB('admin_role')->field("id,pid, name as title,icon,value,parm,'_self' as target")->order("sort asc")->where([['menu','=',1],['status','=',1],['pid','=',$vv['id']]])->select();
        //         foreach($menu[$k]['child'][$kk]['child'] as $kkk=>$vvv){
        //           $menu[$k]['child'][$kk]['href']='';
        //           $menu[$k]['child'][$kk]['child'][$kkk]['href'] = $site_path.'/'.$vvv['value'].'?'.$vvv['parm'];
        //         }

        //     }
        // }

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
        
        //插件菜单
        $application_menu = [
            'title'=>'插件',
            'href'=>'',
            'child'=>[]
        ];
        //顶部菜单
        $application_menu_top = [];
        //插件/顶部菜单
        if(is_dir('./addons')){
            $data = db('plugin')->where([['status','=',1],['type','=','plugin']])->select();
            foreach ($data as $k => $vo) {
               if($vo['plugin_name']!='.' && $vo['plugin_name']!='..' && is_dir('./addons/'.$vo['plugin_name']) && is_dir('./addons/'.$vo['plugin_name'].'/controller')){
                    $_file = './addons/'.$vo['plugin_name'].'/controller/Plugin.php';
                    if(file_exists($_file)){
                       $_namespace = '\addons\\'.$vo['plugin_name'].'\controller\Plugin';
                       if(class_exists($_namespace)){
                            $_obj = new $_namespace;
                            $_is_menu = method_exists($_obj,'menu');
                            if($_is_menu){
                                 $plugin_menu_arr = $_obj->menu();
                                 if(is_array($plugin_menu_arr) && $plugin_menu_arr!=[]){
                                     array_push($application_menu['child'],$plugin_menu_arr);
                                 }
                            }
                            $_is_menu_top = method_exists($_obj,'menu_top');
                            if($_is_menu_top){
                                 $plugin_menu_top_arr = $_obj->menu_top();
                                 if(is_array($plugin_menu_top_arr) && $plugin_menu_top_arr!=[]){
                                     array_push($application_menu_top,$plugin_menu_top_arr);
                                 }
                            }
                        }
                    }
               }
            }
        }
        // 插件
        array_push($menu,$application_menu);
        $amenu = new AMenu($menu);
        doZfAction('admin_menu_append',$amenu);
        $ret_menu = $amenu->menu;
        // //顶部
        foreach($application_menu_top as $k=>$vo){
            $ret_menu[] = $vo;
        }
        $arr['menuInfo'] = $ret_menu;
        return $arr;
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
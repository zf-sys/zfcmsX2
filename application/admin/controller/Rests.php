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
use Wmc1125\TpFast\Category as cat; 

class Rests extends Admin
{
    public function __construct (){
        parent::__construct();
    }

    /**
     * @Notes:广告总列表
     * @Interface advert
     * @return \think\response\View
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   10:59 下午
     */
    public function advert()
    {
        admin_role_check($this->z_role_list,$this->mca);
        $where[] = ['status','<>',9];
        $where = array_merge($where, $this->common_select_tag);
        $tag = input('tag','');
        $type = input('type','');
        try {
            if($type=='child'){
                $pid = input('id','');
                if($tag!=''){
                    $is = ZFTB('advert')->where([['status','<>',9],['tag','=',$tag],['pid','=','0']])->order("id asc")->find();
                    if(!$is){
                        $is_add = ZFTB('advert')->insert(['status'=>1,'tag'=>$tag,'name'=>$tag.date("YmdHis"),'pid'=>'0']);
                        if(!$is_add){
                            dd('添加失败');
                        }
                        $is = ZFTB('advert')->where([['status','<>',9],['tag','=',$tag],['pid','=','0']])->order("id asc")->find();
                    }
                    $pid = $is['id'];
                }
                $tpl='rests/advert_child';
                $list = ZFTB('advert')->where('pid',$pid)->where($where)->order("sort desc,id desc")->paginate(10,false,['query' => request()->param()]);
                $this->assign("pid",$pid);
            }else{
                $tpl='';
                $list = ZFTB('advert')->where('pid',0)->where($where)->order("sort desc,id desc")->paginate(10,false,['query' => request()->param()]);
            }
        }catch (Exception $e) {
            return jserror($e);
        }
        
        $page = $list->render();
        $this->assign("list",$list);
        $this->assign("page",$page);
        return view($tpl);
    }

    /**
     * @Notes:广告添加
     * @Interface advert_add
     * @return \think\response\View|void
     * @author: 子枫
     * @Time: 2019/11/13   10:59 下午
     */
    public function advert_add()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(!request()->isPost()){
            if(input('type')=='child'){
                $tpl="rests/advert_add_child";   
                $this->assign("pid",input("pid"));
            }else{
                $tpl='';
            }
            return view($tpl);
        }  
        $data = input("post.");
        if($data['name']==''){
            return jserror('名称不能为空');exit;
        }
        $data = array_merge($data,$this->common_tag);
        try {
            $res = ZFTB('advert')->insert($data);
            return ZFRetMsg($res,'新增成功','新增失败');
        }catch (Exception $e) {
            return jserror($e);
        }
        
    }

    /**
     * @Notes:广告修改
     * @Interface advert_edit
     * @return \think\response\View|void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author: 子枫
     * @Time: 2019/11/13   10:59 下午
     */
    public function advert_edit()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
    	if(request()->isGet()){
            $id = input('id','');
            $tag = input('tag','');
            try {
                if($tag!=''){
                    $is = ZFTB('advert')->where([['status','<>',9],['tag','=',$tag],['pid','=','0']])->order("id asc")->find();
                    if(!$is){
                        $is_add = ZFTB('advert')->insert(['status'=>1,'tag'=>$tag,'name'=>$tag.date("YmdHis"),'pid'=>'0']);
                        if(!$is_add){
                            dd('添加失败');
                        }
                        $is = ZFTB('advert')->where([['status','<>',9],['tag','=',$tag],['pid','=','0']])->order("id asc")->find();
                    }
                    $id = $is['id'];
                }
            }catch (Exception $e) {
                return jserror($e);
            }
            
            $res =  ZFTB('advert')->where(['id'=>$id])->find(); 
            $this->assign("res",$res);
            if(input('type')=='child'){
                $tpl="rests/advert_add_child";   
                $this->assign("pid",input("pid"));
            }else{
                $tpl='rests/advert_add';
            }
            return view($tpl);
        } 
        if(request()->isPost()){
            $data = input('post.');
            if($data['name']==''){
                return jserror('名称不能为空');exit;
            }
            try {
                $res = ZFTB('advert')->where(['id'=>$data['id']])->update($data);
                return ZFRetMsg($res,'修改成功','修改失败');
            }catch (Exception $e) {
                return jserror($e);
            }
        } 
    }

    /**
     * @Notes:友情链接
     * @Interface link
     * @return \think\response\View
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   10:59 下午
     */
     public function link()
    {
        admin_role_check($this->z_role_list,$this->mca);
        $where[] = ['status','<>',9];
        $where = array_merge($where, $this->common_select_tag);
        $list = ZFTB('link')->where($where)->order("sort desc,id desc")->paginate(10,false,['query' => request()->param()]);
        $page = $list->render();
        $this->assign("list",$list);
        $this->assign("page",$page);
        return view();
    }

    /**
     * @Notes:友情链接-增加
     * @Interface link_add
     * @return \think\response\View|void
     * @author: 子枫
     * @Time: 2019/11/13   11:00 下午
     */
    public function link_add()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(!request()->isPost()){
            return view();
        }  
        $data = input("post.");
        if($data['name']==''){
            return jserror('请填写信息');exit;
        }
        $data['ctime'] = time();
        $data = array_merge($data,$this->common_tag);
        try {
            $res = ZFTB('link')->insert($data);
            return ZFRetMsg($res,'新增成功','新增失败');
        }catch (Exception $e) {
            return jserror($e);
        }
        
    }

    /**
     * @Notes:友情链接-修改
     * @Interface link_edit
     * @return \think\response\View|void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author: 子枫
     * @Time: 2019/11/13   11:00 下午
     */
    public function link_edit()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isGet()){
            $res =  ZFTB('link')->where(['id'=>input('id')])->find(); 
            $this->assign("res",$res);
            return view('rests/link_add');
        } 
        if(request()->isPost()){
            $data = input('post.');
            try {
                $res = ZFTB('link')->where(['id'=>$data['id']])->update($data);
                return ZFRetMsg($res,'修改成功','修改失败');
            }catch (Exception $e) {
                return jserror($e);
            }
        } 
    }

    /**
     * @Notes:留言列表
     * @Interface guessbook
     * @return \think\response\View
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   11:00 下午
     */
     public function guessbook()
    {   
        admin_role_check($this->z_role_list,$this->mca);
        $list = ZFTB('guessbook')->where([['status','<>',9]])->order("ctime desc,sort desc,id desc")->paginate(10,false,['query' => request()->param()]);
        $page = $list->render();
        $this->assign("list",$list);
        $this->assign("page",$page);
        return view();
    }

    /**
     * @Notes:留言增加
     * @Interface guessbook_add
     * @return \think\response\View|void
     * @author: 子枫
     * @Time: 2019/11/13   11:01 下午
     */
    public function guessbook_add()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(!request()->isPost()){
            return view();
        }  
        $data = input("post.");
        if($data['name']==''){
            return jserror('请填写信息');exit;
        }
        $data['ctime'] = time();
        $data = array_merge($data,$this->common_tag);
        try {
            $res = ZFTB('guessbook')->insert($data);
            return ZFRetMsg($res,'新增成功','新增失败');
        }catch (Exception $e) {
            return jserror($e);
        }
        
    }

    /**
     * @Notes:留言修改
     * @Interface guessbook_edit
     * @return \think\response\View|void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author: 子枫
     * @Time: 2019/11/13   11:01 下午
     */
    public function guessbook_edit()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isGet()){
            $res =  ZFTB('guessbook')->where(['id'=>input('id')])->find(); 
            $this->assign("res",$res);
            return view('rests/guessbook_add');
        } 
        if(request()->isPost()){
            $data = input('post.');
            try {
                $res = ZFTB('guessbook')->where(['id'=>$data['id']])->update($data);
                return ZFRetMsg($res,'修改成功','修改失败');
            }catch (Exception $e) {
                return jserror($e);
            }
        } 
    }

    public function menu()
    {
        admin_role_check($this->z_role_list,$this->mca);
        $type = input('type','');
        $tag = input('tag','');
        if($type=='child'){
            $pid = input('id');
            if($tag!=''){
                try {
                    $is = ZFTB('menu')->where([['status','<>',9],['tag','=',$tag],['pid','=','0']])->order("id asc")->find();
                    if(!$is){
                        $is_add = ZFTB('menu')->insert(['status'=>1,'tag'=>$tag,'name'=>$tag.date("YmdHis"),'pid'=>'0']);
                        if(!$is_add){
                            dd('添加失败');
                        }
                        $is = ZFTB('menu')->where([['status','<>',9],['tag','=',$tag],['pid','=','0']])->order("id asc")->find();
                    }
                    $pid = $is['id'];
                }catch (Exception $e) {
                    return jserror($e);
                }
                
            }
            $tpl='rests/menu_child';
            $where_parant[] = ['status','<>',9];
            $where_parant[] = ['pid','=',$pid];
            $arr_partent = ZFTB('menu')->where($where_parant)->order("sort asc,id asc")->select();
            $cat = new cat(array('id', 'menu_pid', 'name', 'cname')); 
            $list = $cat->getTree($arr_partent,0); 
            if(!$list){
                $list = [];
            }
            $this->assign("pid",$pid);
            $this->assign("list",$list);
            $this->assign("page",'');
            return view($tpl);
        }else{
            $tpl='';
            $where[] = ['status','<>',9];
            $where = array_merge($where, $this->common_select_tag);
            $list = ZFTB('menu')->where('pid',0)->where($where)->order("sort desc,id desc")->paginate(10,false,['query' => request()->param()]);
        }
        $page = $list->render();
        $this->assign("list",$list);
        $this->assign("page",$page);
        return view($tpl);
    }

    
    public function menu_add()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        $pid = input('pid',0);
        if(!request()->isPost()){
            if(input('type')=='child'){
                $tpl="rests/menu_add_child";   
                $this->assign("pid",$pid);
                $where_parant[] = ['status','=',1];
                $where_parant[] = ['pid','=',$pid];
                $arr_partent = ZFTB('menu')->field('id,pid,name,cname,menu_pid')->where($where_parant)->order("sort asc,id asc")->select();
                $cat = new cat(array('id', 'menu_pid', 'name', 'cname')); 
                $plist = $cat->getTree($arr_partent,0); 
                if(!$plist){
                    $plist = [];
                }
                $plist[999] =[
                    'id'=>0,
                    'name'=>'顶级目录',
                    'cname'=>'顶级目录'
                ];
                // dd($plist);;
                $this->assign("plist",$plist);
                $action_list = ['','index','list','detail','search','liuyan','tag','special','special_list','page'];
                $this->assign("action_list",$action_list);
            }else{
                $tpl='';
            }
            return view($tpl);
        }  
        $data = input("post.");
        if($data['name']==''){
            return jserror('名称不能为空');exit;
        }
        $data = array_merge($data,$this->common_tag);
        try {
            $res = ZFTB('menu')->insert($data);
            return ZFRetMsg($res,'新增成功','新增失败');
        }catch (Exception $e) {
            return jserror($e);
        }
        
    }

   
    public function menu_edit()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        $pid = input('pid',0);
    	if(request()->isGet()){
            $res =  ZFTB('menu')->where(['id'=>input('id')])->find(); 
            $this->assign("res",$res);
            if(input('type')=='child'){
                $tpl="rests/menu_add_child";   
                $this->assign("pid",input("pid"));
                $where_parant[] = ['status','=',1];
                $where_parant[] = ['pid','=',$pid];
                $arr_partent = ZFTB('menu')->field('id,pid,name,cname,menu_pid')->where($where_parant)->order("sort asc,id asc")->select();
                $cat = new cat(array('id', 'menu_pid', 'name', 'cname')); 
                $plist = $cat->getTree($arr_partent,0); 
                if(!$plist){
                    $plist = [];
                }
                $plist[999] =[
                    'id'=>0,
                    'name'=>'顶级目录',
                    'cname'=>'顶级目录'
                ];
                $this->assign("plist",$plist);
                $action_list = ['','index','list','detail','search','liuyan','tag','special','special_list','page'];
                $this->assign("action_list",$action_list);
            }else{
                $tpl='rests/menu_add';
            }
            return view($tpl);
        } 
        if(request()->isPost()){
            $data = input('post.');
            if($data['name']==''){
                return jserror('名称不能为空');exit;
            }
            try {
                $res = ZFTB('menu')->where(['id'=>$data['id']])->update($data);
                return ZFRetMsg($res,'修改成功','修改失败');
            }catch (Exception $e) {
                return jserror($e);
            }
        } 
    }
  


}

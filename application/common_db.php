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
use think\Db;
use Wmc1125\TpFast\Category as cat;
/**
 * @Author: Eric-枫
 * @Date:   2019-09-18 13:28:30
 * @Last Modified by:   Eric-枫
 * @Last Modified time: 2019-09-25 11:02:05
 */
// ------------数据库操作-----------------
if (!function_exists('ZFTB')) {
	function ZFTB($tb){
			return Db::name($tb);
	}
}
if (!function_exists('ZFJoinStrLang')) {
	function ZFJoinStrLang($str=''){
	    return $str;
	}
}
if (!function_exists('get_cate_content')) {
  function get_cate_content($cid,$tb='category'){
    $res =ZFTB($tb)->where('cid',$cid)->value('content');
    return $res;
  }
}
if (!function_exists('get_cate_summary')) {
  function get_cate_summary($cid,$tb='category'){
    $res =ZFTB($tb)->where('cid',$cid)->value('summary');
    return $res;
  }
}
if (!function_exists('get_cate_res')) {
  function get_cate_res($cid,$tb='category'){
    $res =ZFTB($tb)->where('cid',$cid)->find();
    if($res){
      return $res;
    }else{
      return false;
    }
  }
}
if (!function_exists('get_cate_list')) {
  function get_cate_list($cid,$tb='category'){
      $menu_r =ZFTB($tb)->where(['pid'=>$cid,'status'=>1])->order("sort asc")->select();
      return $menu_r;
  }
}
//查询文章名称
if (!function_exists('post_info')) {
  function post_info($id){
      $res =ZFTB('post')->where(['status'=>1,'id'=>$id])->order("id desc")->find();
      return $res;
  }
}

//查询用户名字
if (!function_exists('user_name')) {
  function  user_name($id){
    $name =ZFTB('user')->where('id',$id)->value('name');
    if($name){
      return $name;
    }else{
      return false;
    }
  }
}
if (!function_exists('get_order_goods_list')) {
  function get_order_goods_list($oid,$limit='4'){
      $list =ZFTB('order_goods')->where('oid',$oid)->limit($limit)->order("id asc")->select();
      return $list;
  }
}
if (!function_exists('get_post_res')) {
  function get_post_res($id){
      $list =ZFTB('post')->field("title,append,pic,id,price")->where('id',$id)->find();
      return $list;
  }
}
if (!function_exists('get_post_name')) {
  function get_post_name($id){
    if($id==-1){
      return "全部";
    }
    $res =ZFTB('post')->where('id',$id)->value('title');
    return $res;
  }
}
// 获取post
if (!function_exists('get_post_list')) {
  function get_post_list($cid,$limit=10,$tb='post',$recommend='',$child='',$cate_tb='',$like='',$order='',$lang=''){
    if($cate_tb=='') $cate_tb='category';
    if($child!=''){
      $where[] = ['p.cid','in',get_child_id($cid,$cate_tb)];
    }else{
      $where[] = ['p.cid','=',$cid];
    }
    if($like!=''){
      $where[] = ['p.title|p.content|p.summary','like','%'.$like.'%'];
    }
    $where[] = ['p.lang','=',$lang];
    $where[] = ['p.status','=',1];
    $where[] = ['c.status','=',1];
    $where[] =['p.ctime','<',time()];
    if($order==''){
      $order = 'p.sort desc,p.id desc';
    }
    if($recommend==''){
      $list =ZFTB($tb.' p')
      ->join($cate_tb.' c','p.cid=c.cid')->field('c.*,p.*')->where($where)->limit($limit)->order($order)->select();
    }else{
      $where[] = ['p.recommend','=',1];
      $list =ZFTB($tb.' p')
      ->join($cate_tb.' c','p.cid=c.cid')->field('c.*,p.*')->where($where)->limit($limit)->order($order)->select();
    }
    return $list;
  }
}

if (!function_exists('get_special_list')) {
  function get_special_list($tb='',$limit=10,$lang=''){
    if($tb=='') $tb = 'special';
    $list = db($tb)->where(['status'=>1,'lang'=>$lang])->order('id desc')->limit($limit)->select();
    return $list;
  }
}
if (!function_exists('get_tag_list')) {
  function get_tag_list($tb='',$limit=10,$lang=''){
    if($tb=='') $tb = 'tag';
    $list = db($tb)->where(['status'=>1,'lang'=>$lang])->order('id desc')->limit($limit)->select();
    return $list;
  }
}
if (!function_exists('get_links_list')) {
  function get_links_list($tb='',$limit=10,$lang=''){
    if($tb=='') $tb = 'link';
    $list = db($tb)->where(['status'=>1,'lang'=>$lang])->order('id desc')->limit($limit)->select();
    return $list;
  }
}
if (!function_exists('get_menu_list')) {
  function get_menu_list($pid='',$menu_pid='0',$tb=''){
    if($tb=='') $tb = 'menu';
    $where[] = ['status','=',1];
    if($menu_pid=='0'){
      $where2[] = ['status','=',1];
      if(is_int($pid)){
        $where2[] = ['pid','=',$pid];
      }else{
        $where2[] = ['tag','=',$pid];
      }
      $pid = db($tb)->where($where2)->order('sort asc,id asc')->value('id');
      $where[] = ['pid','=',$pid];
      $where[] = ['menu_pid','=',0];
    }else{
      $where[] = ['menu_pid','=',$menu_pid];
    }
    $list = db($tb)->where($where)->order('sort asc,id asc')->select();
    return $list;
  }
}

if (!function_exists('get_child_id')) {
  function get_child_id($pid = 0,$cate_tb='', $condition = '1=1 and status=1') {
    //查询分类信息
    $data = db($cate_tb)->field('cid,pid,name')->where($condition)->order("sort asc,cid asc")->select();
    $cat = new cat(array('cid', 'pid', 'name', 'cname')); //初始化无限分类
    $child_array = $cat->getTree($data, $pid);//获取分类数据树结构
    if(is_array($child_array) && !empty($child_array)){
        $child_cid[] = $pid;
        foreach($child_array as $vo){
            $child_cid[] = $vo['cid'];
        }
    }else{
        return $pid;
    }
    return implode(',', $child_cid);//获取所有子分类cid字符串
  }
}




//通过模型id查询模型名称
if (!function_exists('m_name')) {
  function  m_name($id,$tb='category_model'){
    $res =ZFTB($tb)->where('id',$id)->find();
    if($res){
      return $res['name'];
    }else{
      return false;
    }
  }
}
//通过权限id查询权限名称
if (!function_exists('r_name')) {
  function  r_name($id){
    $res =ZFTB('admin_role')->where('id',$id)->find();
    if($res){
      return $res['name'];
    }else{
      return false;
    }
  }
}
//通过用户组id查询用户组名称
if (!function_exists('user_group_name')) {
  function  user_group_name($id){
    $res =ZFTB('user_group')->where('id',$id)->find();
    if($res){
      return $res['name'];
    }else{
      return false;
    }
  }
}

if (!function_exists('get_cate_name')) {
  function get_cate_name($cate_tb,$id){
    $res =ZFTB($cate_tb)->where('cid',$id)->value('name');
    return $res;
  }
}
if (!function_exists('get_post_number')) {
  function get_post_number($cate_tb,$id,$t_cid='cid',$lang=''){
    if($t_cid=='cid'){
      $res =ZFTB($cate_tb)->where([['cid','=',$id],['status','<>',9],['lang','=',$lang]])->count();
    }else{
      $res =ZFTB($cate_tb)->where([['id','=',$id],['status','<>',9],['lang','=',$lang]])->count();
    }
    if($res){
      return $res;
    }else{
      return 0;
    }
  }
}







/**
 * @Notes:查询子菜单
 * @Interface get_two_menu
 * @param $id
 * @return array|PDOStatement|string|\think\Collection
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 * @author: 子枫
 * @Time: 2019/11/13   11:06 下午
 */
if (!function_exists('get_two_menu')) {
  function get_two_menu($id,$tb='admin_role'){
    $menu_r =ZFTB($tb)->where([['pid','=',$id],['menu','=',1],['status','<>','9']])->order("sort asc")->select();
    return $menu_r;
  }
}

/**
 * @Notes:查询所有的文章
 * @Interface post_list_all
 * @return array|PDOStatement|string|\think\Collection
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 * @author: 子枫
 * @Time: 2019/11/13   11:06 下午
 */
if (!function_exists('post_list_all')) {
  function post_list_all($lang=''){
      $where[] = ['status','=',1];
      $where[] = ['lang','=',$lang];
      $list =ZFTB('post')->where($where)->order("id desc")->select();
      return $list;
  }
}

/**
 * @Notes:查询文章名称
 * @Interface post_name
 * @param $id
 * @return mixed
 * @author: 子枫
 * @Time: 2019/11/13   11:07 下午
 */
if (!function_exists('post_name')) {
  function post_name($id){
      $res =ZFTB('post')->where(['status'=>1,'id'=>$id])->order("id desc")->value('title');
      return $res;
  }
}
/**
 * @Notes:通过id查询管理员的分类名
 * @Interface get_admin_group_name
 * @param $id
 * @return mixed
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 * @author: 子枫
 * @Time: 2019/11/13   11:07 下午
 */
if (!function_exists('get_admin_group_name')) {
  function get_admin_group_name($id){
    $info =ZFTB('admin_group')->where('id',$id)->find();
    return $info['name'];
  }
}



/**
 * @Notes:判断该栏目是否有子类
 * @Interface if_pid
 * @param $cid
 * @return bool
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 * @author: 子枫
 * @Time: 2019/11/13   11:07 下午
 */
if (!function_exists('if_pid')) {
  function  if_pid($cid){
    $res =ZFTB('category')->where([['pid','=',$cid],['status','<>',9]])->find();
    if($res){
      return true;
    }else{
      return false;
    }
  }
}


//根据tag获取广告
if (!function_exists('get_adv_list')) {
  function get_adv_list($tag,$tb='',$limit='',$lang=''){
    if($tb==''){
      $tb='advert';
    }
    $res = db($tb)->where(['status'=>1,'tag'=>$tag,'lang'=>$lang])->find();
    if(!$res){
      return false;
    }
    $data['res'] = $res; 
    if($limit==''){
      $data['list'] = Db::name($tb)->where(['status'=>1,'pid'=>$res['id']])->order('sort asc,id asc')->select();
    }else{
      $data['list'] = Db::name($tb)->where(['status'=>1,'pid'=>$res['id']])->limit(intval($limit))->order('sort asc,id asc')->select();
    }
    return $data;
  }
}



if (!function_exists('ZFC')) {
  function ZFC($key=''){
    if(config('database.database')==''){
      return '';
    }
    try{
      if(!ZFTBExist('config')){ return ''; }
      $res =ZFTB('config')->where(['key'=>$key])->value('value');
      if(!$res){
        return '';
      }else{
        return $res;
      }
    } catch (\Exception $e) { 
      return '';
    }


    
  }
}

if (!function_exists('ZFTBExist')) {
  function ZFTBExist($tb){
    $prefix = config('database.prefix');
    $table = $prefix.$tb;
    $rs =DB()->query("SHOW TABLES LIKE '".$table."'");
    if($rs){
      return true;
    }
    return false;
  }
}
if (!function_exists('model_val')) {
  function model_val($id){
    $res =ZFTB('category_model_parm')->where(['status'=>1,'id'=>$id])->order("id desc")->value('value');
    return $res;
  }
}

if (!function_exists('user_oauth_res')) {
  function user_oauth_res($uid,$type=''){
    if($type==''){
      $res =ZFTB('user_oauth')->where(['uid'=>$uid,'status'=>1])->order("id desc")->select();
    }else{
      $res =ZFTB('user_oauth')->where(['uid'=>$uid,'status'=>1,'type'=>$type])->order("id desc")->find();
    }
    return $res;
  }
}


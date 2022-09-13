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
use app\common\controller\ZfAuth;
use GuzzleHttp\Client;
use think\Controller;

class Zfyun extends Admin
{
    public function __construct (){
        parent::__construct();
        $this->zfyun_init();
    }
    public function store(){
        $this->zfyun_store();
        return view();
    }
    public function themes(){
        $this->zfyun_themes();
        return view();
    }
    public function plugins(){
        $this->zfyun_plugins();
        return view();
    }
    public function themes_upload(){
        $this->zfyun_themes_upload();
    }
    public function plugin_upload(){
        $this->zfyun_plugin_upload();
    }
    public function plugin_uninstall(){
      $this->zfyun_plugin_uninstall();
    }
    public function themes_uninstall(){
        $this->zfyun_themes_uninstall();
    }
    public function plugin_backup(){
        $this->zfyun_plugin_backup();
    }
    public function theme_backup(){
        $this->zfyun_theme_backup();
    }
    public function update_sq(){
        $_SESSION['_zf_temp_remote'] = '1';
        $this->zfauth = new ZfAuth();
        $this->zfauth->plugin_check('@update_sq@','alert');
        $this->success('更新成功');
    }
    public function plugin_act(){
        $this->zfyun_plugin_act();
    }
    public function upgrade(){
        $this->zfyun_upgrade();
        $_SESSION['_zf_temp_remote'] = '1';
        $this->zfauth = new ZfAuth();
        $this->zfauth->plugin_check('@update_sq@','alert');
        return view();
    }
    public function upgrade_act(){
        $type = request()->get('type','');
        $this->zfyun_upgrade_act($type);
        // if(!in_array($type,['replace_one','check_version','replace_all','del_file','bak_old','yun_update'])){
        return view();
        // }
    }
    //升级配置
    public function upgrade_setting(){
      if(request()->isPost()){
          $res = extraconfig(input('post.'),'version');
          if($res){
              return jssuccess('保存成功');die;
          }else{
              return jserror('保存失败');die;
          }
      }
      $data = config()['version'];
      $this->assign("data",$data);
      return view();
    }
    public function upgrade_sql(){
        $update = new \app\admin\controller\Updatesql();
        $update_sql = $update->update();
        $this->success('更新Sql成功');
    }
    public function authentication_sys(){
        $t = input('t','');
        if($t=='status'){
            // 判断是否正确
            $auth_info['sc'] = config()['zf_auth']['sc'];
            $auth_info['key'] = config()['zf_auth']['key'];
            $auth_info['soft_id'] = config()['version']['soft_id'];
            $this->zfauth = new ZfAuth();
            $this->zfauth->vfast_check($auth_info,'alert');
            $this->zfauth->plugin_check($auth_info,'alert');
            if(config()['zf_auth']['key']!='' &&  config()['zf_auth']['sc']!='' &&  config()['zf_auth']['email']!='' ){
                return jssuccess('授权成功');
            }
        }
        if($t=='save'){
            $data = input('post.');
            $res = extraconfig($data,'zf_auth');
            if($res){
                $auth_info['email'] = $data['email'];
                $auth_info['sc'] = $data['sc'];
                $auth_info['key'] = $data['key'];
                $auth_info['soft_id'] = config()['version']['soft_id'];
                $this->zfauth = new ZfAuth();
                $this->zfauth->vfast_check($auth_info);
                if($data['key']!='' &&  $data['sc']!='' &&  $data['email']!='' ){
                    return jssuccess('授权成功');
                }else{
                    return jserror('授权失败,请查看填写内容是否正确');die;
                }
            }else{
                return jserror('保存失败,请查看是config文件夹是否有保存权限');die;
            }  


        }
        // if($data['key']!='' && $data['sc']!=''){
        //     $this->redirect(url('login/index'));
        // }
        $data =  config()['zf_auth'];
        $version =  config()['version'];
        $this->assign('data',$data);
        $this->assign('version',$version);
        return view();
    }
    //导出sql
    public function dbBackups() {
        $sPath='backup/sql/';
        if(!is_dir($sPath)){
            mkdir($sPath,0777,true);
        }
        $db_data = config('database.');
        header("Content-type:text/html;charset=utf-8");

        //配置信息
        $cfg_dbhost = $db_data['hostname'];
        $cfg_dbname = $db_data['database'];
        $cfg_dbuser = $db_data['username'];
        $cfg_dbpwd = $db_data['password'];
        $cfg_db_language = $db_data['charset'];
        $to_file_name = $sPath.date("Y-m-d",time())."-".mt_rand(1,9999)."-db.sql";
        // $to_file_name = date("Ymd")."-test.sql";
        // END 配置
        //链接数据库
        $link = mysqli_connect($cfg_dbhost,$cfg_dbuser,$cfg_dbpwd,$cfg_dbname);
        //选择编码
        mysqli_set_charset($link,$cfg_db_language);
        //数据库中有哪些表
        $tables = mysqli_query($link,"show tables");
        //将这些表记录到一个数组
        $tabList = array();
        while($row = mysqli_fetch_row($tables)){
        $tabList[] = $row[0];
        }
        // echo "运行中，请耐心等待...<br/>";
        $info = "-- ----------------------------\r\n";
        $info .= "-- 日期：".date("Y-m-d H:i:s",time())."\r\n";
        $info .= "-- ZFCMS 导出数据库插件 \r\n";
        $info .= "-- ----------------------------\r\n\r\n";
        file_put_contents($to_file_name,$info,FILE_APPEND);
        //将每个表的表结构导出到文件
        foreach($tabList as $val){
        $sql = "show create table ".$val;
        $res = mysqli_query($link,$sql);
        $row = mysqli_fetch_array($res);
        $info = "-- ----------------------------\r\n";
        $info .= "-- Table structure for `".$val."`\r\n";
        $info .= "-- ----------------------------\r\n";
        $info .= "DROP TABLE IF EXISTS `".$val."`;\r\n";
        $sqlStr = $info.$row[1].";\r\n\r\n";
        //追加到文件
        file_put_contents($to_file_name,$sqlStr,FILE_APPEND);
        //释放资源
        mysqli_free_result($res);
        }
        //将每个表的数据导出到文件
        foreach($tabList as $val){
        $sql = "select * from ".$val;
        $res = mysqli_query($link,$sql);
        //如果表中没有数据，则继续下一张表
        if(mysqli_num_rows($res)<1) continue;
        //
        $info = "-- ----------------------------\r\n";
        $info .= "-- Records for `".$val."`\r\n";
        $info .= "-- ----------------------------\r\n";
        file_put_contents($to_file_name,$info,FILE_APPEND);
        //读取数据
        while($row = mysqli_fetch_row($res)){
        $sqlStr = "INSERT INTO `".$val."` VALUES (";
        foreach($row as $zd){
        $sqlStr .= "'".$zd."', ";
        }
        //去掉最后一个逗号和空格
        $sqlStr = substr($sqlStr,0,strlen($sqlStr)-2);
        $sqlStr .= ");\r\n";
        file_put_contents($to_file_name,$sqlStr,FILE_APPEND);
        }
        //释放资源
        mysqli_free_result($res);
        file_put_contents($to_file_name,"\r\n",FILE_APPEND);
        }
        echo "导出 OK!<br>";
        echo "文件:".$to_file_name;
    }
    //hook
    public function hook()
    {
        admin_role_check($this->z_role_list,$this->mca);
        $list = ZFTB('hook')->where([['status','<>',9]])->order("sort desc,id desc")->paginate(10,false,['query' => request()->param()]);
        $page = $list->render();
        $this->assign("list",$list);
        $this->assign("page",$page);
        return view();
    }
    public function hook_add()
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
        $data['token'] = time();
        if(isset($data['position'])){
            $data['position'] = implode(',',$data['position']);
        }
        $data = array_merge($data,$this->common_tag);
        $res = ZFTB('hook')->insert($data);
        return ZFRetMsg($res,'新增成功','新增失败');
    }

    
    public function hook_edit()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isGet()){
            $res =  ZFTB('hook')->where(['id'=>input('id')])->find(); 
            $this->assign("res",$res);
            return view('zfyun/hook_add');
        } 
        if(request()->isPost()){
           $data = input('post.');
            if(isset($data['position'])){
                $data['position'] = implode(',',$data['position']);
            }else{
                $data['position'] = '';
            }
            $data['token'] = time();
            $res = ZFTB('hook')->where(['id'=>$data['id']])->update($data);
            return ZFRetMsg($res,'修改成功','修改失败');
        } 
    }
    public function task()
    {
        admin_role_check($this->z_role_list,$this->mca);
        $list = ZFTB('task')->where([['status','<>',9]])->order("sort desc,id desc")->paginate(10,false,['query' => request()->param()]);
        $page = $list->render();
        $this->assign("list",$list);
        $this->assign("page",$page);
        return view();
    }
    public function task_add()
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
        $data['stime'] = strtotime($data['stime']);
        $data['etime'] = strtotime($data['etime']);
        $data['token'] = time();
        if(isset($data['hours'])){
            $data['hours'] = implode(',',$data['hours']);
        }
        $res = ZFTB('task')->insert($data);
        return ZFRetMsg($res,'新增成功','新增失败');
        
    }
    public function task_edit()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isGet()){
            $res =  ZFTB('task')->where(['id'=>input('id')])->find(); 
            $this->assign("res",$res);
            return view('zfyun/task_add');
        } 
        if(request()->isPost()){
           $data = input('post.');
            $data['stime'] = strtotime($data['stime']);
            $data['etime'] = strtotime($data['etime']);
            $data['token'] = time();
            $data['utime'] = time();
            if(isset($data['hours'])){
                $data['hours'] = implode(',',$data['hours']);
            }
            $res = ZFTB('task')->where(['id'=>$data['id']])->update($data);
            return ZFRetMsg($res,'修改成功','修改失败');
        } 
    }
    public function task_log()
    {
        admin_role_check($this->z_role_list,$this->mca);
        $task_id = input('task_id','');
        $list = ZFTB('task_log')->where([['status','<>',9],['task_id','=',$task_id]])->order("id desc")->paginate(10,false,['query' => request()->param()]);
        $page = $list->render();
        $this->assign("list",$list);
        $this->assign("page",$page);
        return view();
    }
    
}




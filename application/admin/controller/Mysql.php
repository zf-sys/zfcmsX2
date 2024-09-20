<?php
namespace app\admin\controller;
use lib\File;
use think\facade\Request;
use think\Db;

class Mysql extends Admin
{
    public function __construct ()
    {
        parent::__construct();
        $this->db_dir = './backup/db/';
        
    }
	//列表
	public function index()
	{
        admin_role_check($this->z_role_list,$this->mca);   
        if(file_exists($this->db_dir) == false){
            mkdir($this->db_dir, 0777, true);
        }
        $files = new File();
        $list = $files->listFile($this->db_dir);
        foreach($list as $k=>$v){
            if($v['type']=='dir'){
                $list[$k]['size'] = getRealSize($files->get_size($this->db_dir.$v['filename']));
            }else{
                $list[$k]['size'] = getRealSize($v['size']);
            }
        }
        $this->assign('list', $list);
	    return view();
	}
	// 数据库备份
    public function backup()
    {
        admin_role_check($this->z_role_list,$this->mca,1);   
    	if(request()->isPost()){
    		$data['filename_dir'] = input('post.filename_dir');
    		if (empty($data['filename_dir'])) {
    		    $data['filename_dir'] = date('YmdHis', time());
			}
            //判断$data['filename_dir']目录是否规范
            if(!preg_match('/^[0-9a-zA-Z_]+$/',$data['filename_dir'])){
                return jserror('目录名不规范,只能是数字字母下划线');
            }
            //Dbbak.php
            $db = new \lib\Dbbak(config('database.hostname'),config('database.username'),config('database.password'),config('database.database'),config('database.charset'),$this->db_dir.$data['filename_dir']);
            //查找数据库内所有数据表
            $tableArry = $db->getTables();
            //备份并生成sql文件
            if(!$db->exportSql($tableArry)){
                return jserror('备份失败');die;
            }else{
                return jssuccess('备份完成');die;
            }
    	}
	    return view();
    }
    // 数据库还原
    public function restore()
    {
        admin_role_check($this->z_role_list,$this->mca);   
        $name = input("name");
        $t = input("t");
        if($t == 'exec'){
            $data = input('post.');
            $sql = $data['sql'];
            $sql = base64_decode($sql);
            if(!$sql){
                return jserror('参数错误');
            }
            $db = new \lib\Dbbak(config('database.hostname'),config('database.username'),config('database.password'),config('database.database'),config('database.charset'),$this->db_dir.$name);
            if($db->exec($sql)){
                return jssuccess('Sql执行成功');
            }else{
                return jserror('Sql执行失败');
            }

        }

        if(!$name){
            $this->error('参数错误');
        }
        $db = new \lib\Dbbak(config('database.hostname'),config('database.username'),config('database.password'),config('database.database'),config('database.charset'),$this->db_dir.$name);
        if($t=='get_sql'){
            $sql = $db->importSql($this->db_dir.$name,'get_sql');
            $this->assign('sql',$sql);
            return view();
        }
        if($db->importSql($this->db_dir.$name)){
            $this->success('还原成功');
        }else{
            $this->error('还原失败');
        }
        
    }
    public function delete()
    {
        admin_role_check($this->z_role_list,$this->mca);   
        $name =input("name");
        if($name) {
            $files = new File();
            // $is_del = unlink($this->db_dir.$name.'/all.sql.php');
            //删除文件夹
            $is_del = $files->del_dir($this->db_dir.$name);
            if($is_del){
                return jssuccess("数据库备份文件删除成功！");
            }else{
                return jserror("删除的数据库备份文件失败！");
            }
        } else {
            return jssuccess("删除的数据库备份文件失败！");
            
        }
    }


    public function sql_exec()
    {
        admin_role_check($this->z_role_list,$this->mca);
        if(request()->isPost()){
            $data = input('post.');
            $sql = $data['sql'];
            $sql = base64_decode($sql);
            if(!$sql){
                return jserror('参数错误');
            }
            $db = new \lib\Dbbak(config('database.hostname'),config('database.username'),config('database.password'),config('database.database'),config('database.charset'),$this->db_dir);
            $result = $db->exec_return($sql);
            if($result['code']==1){
                return jssuccess($result['data']);
            }else{
                return jserror($result['data']);
            }
        }
        return view();
    }




    
}

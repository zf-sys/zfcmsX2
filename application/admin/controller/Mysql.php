<?php
namespace app\admin\controller;
use lib\Dbbak;
use lib\DatabaseTool;
use lib\File;
use think\facade\Request;


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
        $list = $files->listFile('./backup/db/');
        $this->assign('list', $list);
	    return view();
	}
	// 数据库备份
    public function backup()
    {
        admin_role_check($this->z_role_list,$this->mca,1);   
    	if(request()->isPost()){
    		$data['filename'] = input('post.filename');
    		if (empty($data['filename'])) {
    		    $data['filename'] = date('Ymd', time()).'.sql';
			}
            // $data['filename'] =time();
			// error_reporting(E_ALL ^ E_DEPRECATED);
            $db = new DatabaseTool(array(
                'host' => config('database.hostname'),
                'port' => 3306,
                'user' => config('database.username'),
                'password' =>  config('database.password'),
                'database' =>config('database.database'),
                'charset' => config('database.charset'),
                'target' => $this->db_dir.$data['filename']
              ));
            $is_back = $db->backup();
            if($is_back){
                return jssuccess('备份完成');die;
            }else{
                return jserror('备份失败');die;
            }
    	}
	    return view();
    }
    // 数据库还原
    public function restore()
    {
        admin_role_check($this->z_role_list,$this->mca);   
        $name = input("name");
        if(!$name){
            $this->error('参数错误');
        }
    	error_reporting(E_ALL ^ E_DEPRECATED);
        $db = new DatabaseTool(array(
            'host' => config('database.hostname'),
            'port' => 3306,
            'user' => config('database.username'),
            'password' =>  config('database.password'),
            'database' =>config('database.database'),
            'charset' => config('database.charset'),
            'target' => $this->db_dir.$name
          ));
        $is_restore = $db->restore($this->db_dir.$name);
        if($is_restore){
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
            $is_del = unlink($this->db_dir.$name);
            if($is_del){
                return jssuccess("数据库备份文件删除成功！");
            }else{
                return jserror("删除的数据库备份文件失败！");
            }
        } else {
            return jssuccess("删除的数据库备份文件失败！");
            
        }
    }






    
}

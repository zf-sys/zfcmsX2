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

class Updatesql extends Admin
{
    public function __construct (){
        parent::__construct();
        // $this->pugin_tool = new \app\plugins\controller\Base();
        $this->tb_prefix = config()['database']['prefix'];
    }

    public function index(){
        echo  '更新系统sql';die;
    }
    public function update()
    {
        $this->v1_0_0();
        $this->v1_0_0_4();
        $this->v1_0_0_5();
        return true;
    }
    /**
     * 版本v1.0.0
     */
    public function v1_0_0(){
        //修改advert表 summary类型为text
        $this->tb_field_other_edit("show columns from {$this->tb_prefix}advert like 'summary'","alter table {$this->tb_prefix}advert MODIFY  summary  text(0) NOT NULL ");
    }
    /**
     * 版本v1.0.0.4
     */
    public function v1_0_0_4(){
        //新增钩子表    
        $sql_v1_0_0_4[0] = <<<INFO
        CREATE TABLE `zf_hook` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(255) DEFAULT NULL,
            `controller` varchar(255) DEFAULT NULL,
            `action` varchar(255) DEFAULT NULL,
            `sort` int(5) NOT NULL DEFAULT '0',
            `status` tinyint(1) NOT NULL DEFAULT '1',
            `overlay` tinyint(1) NOT NULL DEFAULT '0',
            `ctime` varchar(50) DEFAULT NULL,
            `type` varchar(255) DEFAULT NULL,
            `position` varchar(255) DEFAULT NULL,
            `token` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
INFO;
        $sql_v1_0_0_4[0] = str_replace('zf_hook',$this->tb_prefix.'hook',$sql_v1_0_0_4[0]);
        $this->tb_add($this->tb_prefix.'hook',$sql_v1_0_0_4[0]);
        //增加钩子权限
        if(!db('admin_role')->where(['value'=>'admin/Zfyun/hook'])->find()){
            db('admin_role')->insert(['name'=> '钩子', 'value'=>'admin/Zfyun/hook' , 'check'=> 1, 'status'=>1 , 'summary'=>NULL , 'sort'=> 3, 'pid'=>198 , 'module'=> 'admin' , 'control'=>'Zfyun' , 'act'=>'hook' , 'menu'=>1 , 'parm'=>'' , 'token'=>'1648289288' , 'icon'=>'fa fa-500px']);
        }
        //权限的顺序
        db('admin_role')->where(['id'=>199])->update(['sort'=>1]);
        db('admin_role')->where(['id'=>200])->update(['sort'=>2]);
        db('admin_role')->where(['id'=>209])->update(['sort'=>4]);
        db('admin_role')->where(['id'=>215])->update(['sort'=>5]);

        $this->tb_field_add("show columns from {$this->tb_prefix}tag like 'bd_sl'","alter table {$this->tb_prefix}tag add bd_sl tinyint(1) not null");

    }
     /**
     * 版本v1.0.0.5
     * 多语言版本的实现
     */
    public function v1_0_0_5(){
        // echo '版本5';die;
        //增加lang配置
        $is_lang = db('config')->where(['key'=>'lang'])->find();
        if(!$is_lang){
            db('config')->insert(['key'=>'lang','value'=>'','type'=>'system','msg'=>'语言版本']);
        }
        
        $field_str = 'lang';//表新增lang字段
         $field_str2 = 'lang_pid';//表新增lang_pid字段
        $list = Db::query("SHOW TABLE STATUS");
        // dd($list);
        foreach ($list as $k => $vo) {
            $is = Db::query("Describe `".$vo['Name']."` ".$field_str." ");
            if(!isset($is[0])){
                $res = Db::query("ALTER TABLE `".$vo['Name']."` ADD `".$field_str."` varchar(50) NOT NULL default '' ");
            }
            $is2 = Db::query("Describe `".$vo['Name']."` ".$field_str2." ");
            if(!isset($is2[0])){
                $res = Db::query("ALTER TABLE `".$vo['Name']."` ADD `".$field_str2."` int(11) NOT NULL default '0' ");
            }
        }
       


    }

}

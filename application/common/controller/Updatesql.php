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

namespace app\common\controller;
use think\facade\Request;
use think\Db;
use think\Controller;

class Updatesql extends Controller
{
    public function __construct (){
        parent::__construct();
        $this->tb_prefix = config()['database']['prefix'];
        $this->site_version = strtolower(config()['version']['version']);
        $this->version_arr = [
            'v0.0.0.1'=>[],
            'v0.0.0.2'=>[
                [
                    'tb_field_add',
                    "show columns from {$this->tb_prefix}user_group like 'tag'",
                    "alter table {$this->tb_prefix}user_group add tag varchar(50) not null"
                ],
            ],
            'v0.0.3'=>$this->v0_0_3(),
            'v0.0.4'=>$this->v0_0_4(),
            'v0.0.6'=>$this->v0_0_6(),
            'v0.0.8'=>$this->v0_0_8(),
            'v0.0.9'=>$this->v0_0_9(),
            'v0.230809'=>$this->v0_230809(),
            'v0.230902'=>$this->v0_230902(),
        ]; 
    }

   
    public function update($sql_version='')
    {
        // echo "当前sql :".$sql_version;
        // echo '当前系统: '.$this->site_version;
        $up_arr = [];
        foreach($this->version_arr as $k=>$vo){
            if($k>$this->site_version){
                break;
            }
            if($sql_version<$k){
                $up_arr[$k] = $vo;
            }
        }
        return $up_arr;

    }
    public function v0_0_0_2(){
        $ret_data[1] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}user_group like 'tag'",
            "alter table {$this->tb_prefix}user_group add tag varchar(50) not null"
        ];
    }
    
    public function v0_0_3(){
        $ret_data[1] = [ 
            'tb_post_add',
            $this->tb_prefix.'config',
            ['key'=>'link_type','type'=>'system','msg'=>'超链类型','value'=>'友情链接,首页,内页,其他'],
            false,  //是否允许重复
            ['key'=>'link_type','type'=>'system'],
        ];
        return $ret_data;
    }
    public function v0_0_4(){
        $ret_data[1] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}plugin like 'sql_version'",
            "alter table {$this->tb_prefix}plugin add sql_version varchar(50) not null"
        ];

        return $ret_data;
    }
    /**
     * 1. upload新增thumb字段
     */
    public function v0_0_6(){
        $ret_data[1] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}upload like 'thumb'",
            "alter table {$this->tb_prefix}upload add thumb varchar(255) not null "
        ];
        // dd(2);
        return $ret_data;
    }
    public function v0_0_8(){
        $ret_data[1] = [ 
            'tb_post_add',
            $this->tb_prefix.'config',
            ['key'=>'default_theme_index_content','type'=>'system','msg'=>'默认首页显示信息','value'=>''],
            false,  //是否允许重复
            ['key'=>'default_theme_index_content','type'=>'system'],
        ];
        return $ret_data;
    }
    public function v0_0_9(){
        $ret_data[1] = [ 
            'tb_post_add',
            $this->tb_prefix.'config',
            ['key'=>'gourl_tokens','type'=>'system','msg'=>'goUrl中特殊的tokens 每行一个,token@跳转链接','value'=>'bd@https://www.baidu.com'],
            false,  //是否允许重复
            ['key'=>'gourl_tokens','type'=>'system'],
        ];
        return $ret_data;
    }
    public function v0_230809(){
        $ret_data[1] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}category like 'form_parm'",
            "alter table {$this->tb_prefix}category add form_parm text(0)"
        ];
        return $ret_data;
    }
    public function v0_230902(){
        $ret_data[1] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}category like 'cate_gl_arr_dx'",
            "alter table {$this->tb_prefix}category add cate_gl_arr_dx text(0)"
        ];
        $ret_data[2] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}category like 'post_gl_arr_dx'",
            "alter table {$this->tb_prefix}category add post_gl_arr_dx text(0)"
        ];
        return $ret_data;
    }

    
    




    public function index(){
        die;
    }
    //参考
    private function test(){
        $ret_data = [];
        //新增表
        $ret_data[0][0] = 'tb_add';
        $ret_data[0][1] = $this->tb_prefix.'test';
        $ret_data[0][2] = <<<INFO
        CREATE TABLE `zf_test` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(255) DEFAULT NULL,
            `content` varchar(255) DEFAULT NULL,
            `token` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
INFO;
        $ret_data[0][2] = str_replace('zf_test',$this->tb_prefix.'test',$ret_data[0][2]);

        // 新增表字段 tb_field_add($sql_is,$sql_add)
        $ret_data[1] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}test like 'append'",
            "alter table {$this->tb_prefix}test add append varchar(50) not null"
        ];
        //修改表字段 tb_field_name_edit($sql_is,$sql_add)
        $ret_data[2] = [ 
            'tb_field_edit',
            "show columns from {$this->tb_prefix}test like 'name'",
            "alter table {$this->tb_prefix}test MODIFY  name  varchar(50) NOT NULL "
        ];
        //字段是否存在
        // tb_field_is($sql_is)
        //show columns from {$this->tb_prefix}test like 'name'
        //表字段删除
        // tb_field_del($sql_is,$sql_add)
        $ret_data[3] = [ 
            'tb_field_del',
            "show columns from {$this->tb_prefix}test like 'name'",
            "alter table {$this->tb_prefix}test DROP  name"
        ];
        //数据的增
        $ret_data[3] = [ 
            'tb_post_add',
            $this->tb_prefix.'test',
            ['content'=>1,'token'=>date("Y-m-d",time()),'append'=>11],
            true    //默认允许重复
        ];
        $ret_data[4] = [ 
            'tb_post_add',
            $this->tb_prefix.'test',
            ['content'=>22222,'token'=>date("Y-m-d",time()),'append'=>11],
            false,  //是否允许重复
            ['content'=>22222], //判断条件
        ];
        // 改
        $ret_data[5] = [ 
            'tb_post_edit',
            $this->tb_prefix.'test',
            ['content'=>1],
            ['content'=>'修改啦','token'=>date("Y-m-d",time()),'append'=>11],
        ];
        // 删
        $ret_data[6] = [ 
            'tb_post_del',
            $this->tb_prefix.'test',
            ['id'=>7],
        ];
        return $ret_data;
    }
}

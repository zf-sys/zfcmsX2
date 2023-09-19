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
            'v0.230907'=>$this->v0_230907(),
            'v0.230910'=>$this->v0_230910(),
            'v0.230919'=>$this->v0_230919(),
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
        session('v_upgsql_act',null);
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
    public function v0_230907(){
        $ret_data[1] = [ 
            'tb_post_add',
            $this->tb_prefix.'config',
            ['key'=>'webconfig','type'=>'system','msg'=>'网站配置','value'=>'{"site_name":"","site_url":"","site_mail":"","site_hotline":"","site_fax":"","site_qq":"","site_wx":"","site_address":"","site_copyright":"","site_icp":"","site_title":"","site_keywords":"","site_description":"","site_closed":"0","is_log":"0","key_filter":"","theme_name":"","pic_ext":"pjpeg,jpeg,jpg,gif,bmp,png","file_ext":"txt,pdf,doc,xls,ppt,mp4,zip,jpg,pjpeg,jpeg,jpg,gif,bmp,png,log","site_logo":"","site_icon":"","site_qrcode":"","upload_type":"","site_token":"ea85849590a5c8cadd45baab3af68744","tongji_code":"","site_closed_url":"","cate_page_type":"1","cate_lm":"cate","site_more":"0","tb":"xcxzk","bd_tsjk":"","editor_def":"ueditor","update_config_time":"1618492674","cms_default_pic":"","cate_editor_def":"ueditor","template_home":"index","template_home_cid":"6","cms_oss_sites":"oss002.wangmingchang.com,oss001.wangmingchang.com,oss.wangmingchang.com,oss1.wangmingchang.com","cms_post_copyright":"\u672c\u7ad9\u6587\u7ae0\u5982\u672a\u6ce8\u660e\u51fa\u5904\u5219\u4e3a\u539f\u521b,\u8f6c\u8f7d\u8bf7\u6ce8\u660e\u51fa\u5904\uff0c\u5982\u6709\u4fb5\u6743\u8bf7\u90ae\u4ef6\u8054\u7cfb\u7ad9\u957f ","site_path":"","site_admin_token":"1692615450","site_home_url":""}'],
            false,  //是否允许重复
            ['key'=>'webconfig','type'=>'system'],
        ];
        return $ret_data;
    }
    public function v0_230910(){
        $ret_data[1] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}admin_log like 'method'",
            "alter table {$this->tb_prefix}admin_log add method varchar(50) not null"
        ];
        return $ret_data;
    }
    public function v0_230919(){
        $ret_data[0][0] = 'tb_add';
        $ret_data[0][1] = $this->tb_prefix.'admin_login_log';
        $ret_data[0][2] = <<<INFO
        CREATE TABLE `zf_admin_login_log` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(50) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
            `ctime` int(11) NOT NULL DEFAULT '0',
            `ip` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
            `err_num` int(11) NOT NULL DEFAULT '0',
            `post` text,
            `status` tinyint(1) NOT NULL DEFAULT '1',
            `token` varchar(255) NOT NULL DEFAULT '',
            `lang` varchar(50) NOT NULL DEFAULT '',
            `lang_pid` int(11) NOT NULL DEFAULT '0',
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台登录日志表';
INFO;
        $ret_data[0][2] = str_replace('zf_admin_login_log',$this->tb_prefix.'admin_login_log',$ret_data[0][2]);

        $ret_data[1][0] = 'tb_add';
        $ret_data[1][1] = $this->tb_prefix.'exception_log';
        $ret_data[1][2] = <<<INFO
        CREATE TABLE `zf_exception_log` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(50) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
            `ctime` int(11) NOT NULL DEFAULT '0',
            `ip` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
            `err_msg` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
            `post` text,
            `status` tinyint(1) NOT NULL DEFAULT '1',
            `token` varchar(255) NOT NULL DEFAULT '',
            `lang` varchar(50) NOT NULL DEFAULT '',
            `lang_pid` int(11) NOT NULL DEFAULT '0',
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='异常报错日志表';
INFO;
        $ret_data[1][2] = str_replace('zf_exception_log',$this->tb_prefix.'exception_log',$ret_data[1][2]);
        //修改配置
        $ret_data[2] = [ 
            'tb_post_edit',
            $this->tb_prefix.'admin_role',
            ['value'=>'admin/Config/version'],
            ['icon'=>'fa fa-500px','token'=>time()],
        ];
        $ret_data[3] = [ 
            'tb_post_edit',
            $this->tb_prefix.'admin_role',
            ['value'=>'admin/Config/zf_auth'],
            ['icon'=>'fa fa-cloud','token'=>time()],
        ];
        if($this->site_version<='v0.230919' || session('v_upgsql_act')==1){
            //判断日志是否存在
            $rz_id = db('admin_role')->where(['name'=>'网站日志'])->value('id');
            if(!$rz_id){
                $rz_id = db('admin_role')->insertGetId(['name'=>'网站日志','value'=>'admin/0/','check'=>1,'status'=>1,'summary'=>'','sort'=>40,'pid'=>218,'module'=>'admin','control'=>'0','act'=>'','menu'=>1,'parm'=>'','token'=>time(),'icon'=>'fa fa-clock-o','lang'=>'','lang_pid'=>0]);
            }
            if($rz_id!=''){
                // 改
                $ret_data[4] = [ 
                    'tb_post_edit',
                    $this->tb_prefix.'admin_role',
                    ['value'=>'admin/Config/action_log'],
                    ['pid'=>$rz_id],
                ];
                $ret_data[5] = [ 
                    'tb_post_add',
                    $this->tb_prefix.'admin_role',
                    ['name'=>'后台登录','value'=>'admin/Config/admin_login_log','check'=>1,'status'=>1,'summary'=>'','sort'=>40,'pid'=>$rz_id,'module'=>'admin','control'=>'Config','act'=>'admin_login_log','menu'=>1,'parm'=>'','token'=>time(),'icon'=>'fa fa-trello','lang'=>'','lang_pid'=>0],
                    false,  //是否允许重复
                    ['name'=>'后台登录'], //判断条件
                ];
                $ret_data[6] = [ 
                    'tb_post_add',
                    $this->tb_prefix.'admin_role',
                    ['name'=>'异常日志','value'=>'admin/Config/exception_log','check'=>1,'status'=>1,'summary'=>'','sort'=>40,'pid'=>$rz_id,'module'=>'admin','control'=>'Config','act'=>'exception_log','menu'=>1,'parm'=>'','token'=>time(),'icon'=>'fa fa-heartbeat','lang'=>'','lang_pid'=>0],
                    false,  //是否允许重复
                    ['name'=>'异常日志'], //判断条件
                ];
            }

        }
        
        

        
        
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

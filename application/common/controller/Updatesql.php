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
            'v0.0.3'=>['type'=>'function','name'=>'v0_0_3'],
            'v0.0.4'=>['type'=>'function','name'=>'v0_0_4'],
            'v0.0.6'=>['type'=>'function','name'=>'v0_0_6'],
            'v0.0.8'=>['type'=>'function','name'=>'v0_0_8'],
            'v0.0.9'=>['type'=>'function','name'=>'v0_0_9'],
            'v0.230809'=>['type'=>'function','name'=>'v0_230809'],
            'v0.230902'=>['type'=>'function','name'=>'v0_230902'],
            'v0.230907'=>['type'=>'function','name'=>'v0_230907'],
            'v0.230910'=>['type'=>'function','name'=>'v0_230910'],
            'v0.230919'=>['type'=>'function','name'=>'v0_230919'],
            'v0.231007'=>['type'=>'function','name'=>'v0_231007'],
            'v0.231018'=>['type'=>'function','name'=>'v0_231018'],
            'v0.231024'=>['type'=>'function','name'=>'v0_231024'],
            'v0.231102'=>['type'=>'function','name'=>'v0_231102'],
            'v0.231129'=>['type'=>'function','name'=>'v0_231129'],
            'v0.240125'=>['type'=>'function','name'=>'v0_240125'],
            'v0.240315'=>['type'=>'function','name'=>'v0_240315'], 
            'v0.240319'=>['type'=>'function','name'=>'v0_240319'], 
        ]; 
        $file = new \lib\File();
        $files = $file->listFile('./application/common/updateSql/');
        foreach($files as $k=>$vo){
            $file_name = str_replace('.php','',$vo['filename']);
            $this->version_arr[$file_name] = ['type'=>'file','name'=>'./application/common/updateSql/'.$vo['filename']];
        }
        //sql钩子
        do_action('sys_sql_act',$this->tb_prefix,$this->site_version);
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
                if(isset($vo['type'])){
                    if($vo['type']=='file'){
                        $up_arr[$k] = include $vo['name'];
                    }elseif($vo['type']=='function'){
                        $name = $vo['name'];
                        $up_arr[$k] = $this->$name();
                    }
                }else{
                    $up_arr[$k] = $vo;
                }
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
    public function v0_231007(){
        $ret_data[1] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}plugin like 'act_status'",
            "alter table {$this->tb_prefix}plugin add act_status tinyint(1) not null default 1"
        ];
        if($this->site_version<='v0.231007' || session('v_upgsql_act')==1){
            //判断日志是否存在
            $is_actstatus = db('admin_role')->where(['value'=>'admin/Common/is_switch_actstatus'])->find();
            if(!$is_actstatus){
                $rz_id = db('admin_role')->insertGetId(['name'=>'转换活动的状态','value'=>'admin/Common/is_switch_actstatus','check'=>1,'status'=>1,'summary'=>'','sort'=>40,'pid'=>249,'module'=>'admin','control'=>'Common','act'=>'is_switch_actstatus','menu'=>0,'parm'=>'','token'=>time(),'icon'=>'fa fa-trello','lang'=>'','lang_pid'=>0]);
            }
        }
        return $ret_data;
    }
    public function v0_231102(){
        if($this->site_version<='v0.231102' || session('v_upgsql_act')==1){
            $ret_data[] = [ 
                'tb_post_add',
                $this->tb_prefix.'admin_role',
                ['name'=>'系统工具','value'=>'admin/Config/tool','check'=>1,'status'=>1,'summary'=>'','sort'=>0,'pid'=>244,'module'=>'admin','control'=>'Config','act'=>'tool','menu'=>0,'parm'=>'','token'=>time(),'icon'=>'fa fa-trello','lang'=>'','lang_pid'=>0],
                false,  //是否允许重复
                ['name'=>'系统工具'], //判断条件
            ];
            return $ret_data;
        }
        
    }
    public function v0_231129(){
        $ret_data = [];
        if($this->site_version<='v0.231129' || session('v_upgsql_act')==1){
            //判断日志是否存在
            $rz_id = db('admin_role')->where(['value'=>'admin/mysql/'])->value('id');
            if(!$rz_id){
                $rz_id = db('admin_role')->insertGetId(['name'=>'数据库管理','value'=>'admin/mysql/','check'=>1,'status'=>1,'summary'=>'','sort'=>40,'pid'=>218,'module'=>'admin','control'=>'0','act'=>'','menu'=>1,'parm'=>'','token'=>time(),'icon'=>'fa fa-clock-o','lang'=>'','lang_pid'=>0]);
            }
            if($rz_id!=''){
                $ret_data[1] = [ 
                    'tb_post_add',
                    $this->tb_prefix.'admin_role',
                    ['name'=>'数据库备份','value'=>'admin/Mysql/backup','check'=>1,'status'=>1,'summary'=>'','sort'=>40,'pid'=>$rz_id,'module'=>'admin','control'=>'Mysql','act'=>'backup','menu'=>1,'parm'=>'','token'=>time(),'icon'=>'fa fa-trello','lang'=>'','lang_pid'=>0],
                    false,  //是否允许重复
                    ['value'=>'admin/Mysql/backup'], //判断条件
                ];
                $rz_id2 = db('admin_role')->where(['value'=>'admin/Mysql/index'])->value('id');
                if(!$rz_id2){
                    $rz_id2 = db('admin_role')->insertGetId(['name'=>'备份列表','value'=>'admin/Mysql/index','check'=>1,'status'=>1,'summary'=>'','sort'=>40,'pid'=>$rz_id,'module'=>'admin','control'=>'Mysql','act'=>'index','menu'=>1,'parm'=>'','token'=>time(),'icon'=>'fa fa-clock-o','lang'=>'','lang_pid'=>0]);
                }
                if($rz_id2!=''){

                    $ret_data[2] = [ 
                        'tb_post_add',
                        $this->tb_prefix.'admin_role',
                        ['name'=>'数据库还原','value'=>'admin/Mysql/restore','check'=>1,'status'=>1,'summary'=>'','sort'=>40,'pid'=>$rz_id2,'module'=>'admin','control'=>'Mysql','act'=>'restore','menu'=>1,'parm'=>'','token'=>time(),'icon'=>'fa fa-heartbeat','lang'=>'','lang_pid'=>0],
                        false,  //是否允许重复
                        ['value'=>'admin/Mysql/restore'], //判断条件
                    ];
                    $ret_data[3] = [ 
                        'tb_post_add',
                        $this->tb_prefix.'admin_role',
                        ['name'=>'删除数据库','value'=>'admin/Mysql/delete','check'=>1,'status'=>1,'summary'=>'','sort'=>40,'pid'=>$rz_id2,'module'=>'admin','control'=>'Mysql','act'=>'delete','menu'=>1,'parm'=>'','token'=>time(),'icon'=>'fa fa-heartbeat','lang'=>'','lang_pid'=>0],
                        false,  //是否允许重复
                        ['value'=>'admin/Mysql/delete'], //判断条件
                    ];
                }
            }
        }
        
        return $ret_data;
    }
    public function v0_240125(){
        $ret_data[1] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}category_model_parm like 'notes'",
            "alter table {$this->tb_prefix}category_model_parm add notes text(0)"
        ];
        return $ret_data;
    }
    public function v0_240315(){
        $ret_data[1] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}advert like 'form_parm'",
            "alter table {$this->tb_prefix}advert add form_parm text(0)"
        ];
        $ret_data[2] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}advert like 'utime'",
            "alter table {$this->tb_prefix}advert add utime int(11)"
        ];
        $ret_data[3] = [ 
            'tb_field_edit',
            "show columns from {$this->tb_prefix}advert like 'content'",
            "alter table {$this->tb_prefix}advert MODIFY  content  text(0) "
        ];
        $ret_data[4] = [ 
            'tb_field_edit',
            "show columns from {$this->tb_prefix}category like 'summary'",
            "alter table {$this->tb_prefix}category MODIFY  summary  text(0) "
        ];
        return $ret_data;
    }
    public function v0_240319(){
        $ret_data[] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}advert like 'form_parm_static'",
            "alter table {$this->tb_prefix}advert add form_parm_static text(0)"
        ];
        $ret_data[] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}category like 'form_parm_static'",
            "alter table {$this->tb_prefix}category add form_parm_static text(0)"
        ];
        $ret_data[] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}category_model like 'form_parm_static'",
            "alter table {$this->tb_prefix}category_model add form_parm_static text(0)"
        ];
        $ret_data[] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}category_model like 'utime'",
            "alter table {$this->tb_prefix}category_model add utime int(11)"
        ];

        $ret_data[] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}special like 'form_parm'",
            "alter table {$this->tb_prefix}special add form_parm text(0)"
        ];
        $ret_data[] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}tag like 'form_parm'",
            "alter table {$this->tb_prefix}tag add form_parm text(0)"
        ];
        $ret_data[] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}special like 'utime'",
            "alter table {$this->tb_prefix}special add utime int(11)"
        ];
        $ret_data[] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}tag like 'utime'",
            "alter table {$this->tb_prefix}tag add utime int(11)"
        ];
        $ret_data[] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}special like 'form_parm_static'",
            "alter table {$this->tb_prefix}special add form_parm_static text(0)"
        ];
        $ret_data[] = [
            'tb_field_add',
            "show columns from {$this->tb_prefix}tag like 'form_parm_static'",
            "alter table {$this->tb_prefix}tag add form_parm_static text(0)"
        ];
        return $ret_data;
    }



    /**
     * 新增
     * meta扩展表   zf_meta_data
     * meta字段表   zf_meta_key
     * 
    meta_id   tb  post_id meta_data ctime  utime status token
    id   tb   key   name  append  ctime utime   token sort status
     */
    public function v0_231018(){
        $ret_data[0][0] = 'tb_add';
        $ret_data[0][1] = $this->tb_prefix.'meta_data';
        $ret_data[0][2] = <<<INFO
        CREATE TABLE `zf_meta_data` (
            `meta_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `tb` varchar(50) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
            `post_id` int(11) NOT NULL DEFAULT '0',
            `meta_data` text,
            `ctime` int(11) NOT NULL DEFAULT '0',
            `utime` int(11) NOT NULL DEFAULT '0',
            `status` tinyint(1) NOT NULL DEFAULT '1',
            `token` varchar(255) NOT NULL DEFAULT '',
            PRIMARY KEY (`meta_id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='meta扩展表';
INFO;
        $ret_data[0][2] = str_replace('zf_meta_data',$this->tb_prefix.'meta_data',$ret_data[0][2]);
        $ret_data[1][0] = 'tb_add';
        $ret_data[1][1] = $this->tb_prefix.'meta_key';
        $ret_data[1][2] = <<<INFO
        CREATE TABLE `zf_meta_key` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `tb` varchar(50) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
            `key` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
            `name` varchar(50) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
            `append` text,
            `ctime` int(11) NOT NULL DEFAULT '0',
            `utime` int(11) NOT NULL DEFAULT '0',
            `token` varchar(255) NOT NULL DEFAULT '',
            `sort` tinyint(1) NOT NULL DEFAULT '1',
            `status` tinyint(1) NOT NULL DEFAULT '1',
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='meta字段表';
INFO;
        $ret_data[1][2] = str_replace('zf_meta_key',$this->tb_prefix.'meta_key',$ret_data[1][2]);
        $ret_data[2] = [ 
            'tb_post_add',
            $this->tb_prefix.'admin_role',
            ['name'=>'Meta字段','value'=>'admin/Config/meta_key_list','check'=>1,'status'=>1,'summary'=>'','sort'=>20,'pid'=>154,'module'=>'admin','control'=>'Config','act'=>'meta_key_list','menu'=>1,'parm'=>'','token'=>time(),'icon'=>'fa fa-american-sign-language-interpreting','lang'=>'','lang_pid'=>0],
            false,  //是否允许重复
            ['name'=>'Meta字段'], //判断条件
        ];
        return $ret_data;
    }

    public function v0_231024(){
        $ret_data[1] = [ 
            'tb_field_edit',
            "show columns from {$this->tb_prefix}user like 'login_act_code'",
            "alter table {$this->tb_prefix}user MODIFY  login_act_code  text "
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

<?php
// WebSite: http://www.zf-sys.com/
// Document:  http://bbs.90ckm.com/
// Bbs:  http://bbs.90ckm.com/
// Github: https://github.com/zf-sys/zfcmsX2
// Gitee:https://gitee.com/zf-sys/zfcmsX2
// Feedback: https://support.qq.com/products/166743
namespace zf;
use think\Controller;
use think\Db;
use GuzzleHttp\Client;
use think\facade\Request;
use think\captcha\Captcha;
class ZfCommon extends Controller
{
    public function __construct (){
    }


    
    public function handle_update_db_zdxg(){
        try{
            //指定表,隐藏则所有表
            // if($this->db_tbs!=''){
            //     $change_tbs = explode(',',$this->db_tbs);
            // }
            // if($this->db_fields!=''){
            //     $change_fields = explode(',',$this->db_fields);
            // }
            $list = Db::query("SHOW TABLE STATUS");
            foreach($list as $k=>$vo){
                if(isset($vo['Name'])){
                    foreach($vo as $_kk=>$_vv){
                        $vo[strtolower($_kk)] = $_vv;
                    }
                }
                if(isset($change_tbs) && !in_array($vo['name'],$change_tbs)){
                    continue;
                }
                $tb_fields = Db::query("show columns from ".$vo['name']);
                foreach($tb_fields as $kk=>$vv){
                    if(isset($vv['Field'])){
                        foreach($vv as $kkk=>$vvv){
                            $vv[strtolower($kkk)] = $vvv;
                        }
                    }
                    //指定某个字段
                    if(isset($change_fields) && !in_array($vv['field'],$change_fields)){
                        continue;
                    }
                    // if($vv['null']=='NO' && $vv['default']===NULL && $vv['key']!='PRI' && $vv['type']!='text' && !is_str_find($vv['type'],'text')){ 
                    if($vv['null']=='NO' && $vv['default']===NULL && $vv['key']!='PRI') {
                        $fieldType = strtolower($vv['type']);
                        
                        if (strpos($fieldType, 'varchar') !== false) {
                            Db::query('ALTER TABLE '.$vo['name'].' ALTER COLUMN `'.$vv['field'].'` SET DEFAULT ""');
                        } elseif (strpos($fieldType, 'int') !== false || strpos($fieldType, 'tinyint') !== false) {
                            Db::query('ALTER TABLE '.$vo['name'].' ALTER COLUMN `'.$vv['field'].'` SET DEFAULT 0');
                        } elseif (strpos($fieldType, 'text') !== false || strpos($fieldType, 'longtext') !== false) {
                            Db::query('ALTER TABLE '.$vo['name'].' CHANGE `'.$vv['field'].'` `'.$vv['field'].'` '.$fieldType.' COLLATE utf8mb4_general_ci NULL');
                        } elseif (strpos($fieldType, 'date') !== false || strpos($fieldType, 'datetime') !== false || strpos($fieldType, 'timestamp') !== false) {
                            Db::query('ALTER TABLE '.$vo['name'].' CHANGE `'.$vv['field'].'` `'.$vv['field'].'` '.$fieldType.' NULL');
                        } elseif (strpos($fieldType, 'decimal') !== false || strpos($fieldType, 'float') !== false || strpos($fieldType, 'double') !== false) {
                            preg_match('/(\w+)\((\d+),(\d+)\)/', $fieldType, $matches);
                            if (!empty($matches)) {
                                $precision = $matches[3];
                                $defaultValue = number_format(0, $precision, '.', '');
                                Db::query('ALTER TABLE '.$vo['name'].' ALTER COLUMN `'.$vv['field'].'` SET DEFAULT '.$defaultValue);
                            }
                        } elseif (strpos($fieldType, 'enum') !== false || strpos($fieldType, 'set') !== false) {
                            preg_match('/\((.*?)\)/', $fieldType, $matches);
                            if (!empty($matches)) {
                                $options = explode(',', str_replace("'", '', $matches[1]));
                                $defaultValue = "'".$options[0]."'";
                                Db::query('ALTER TABLE '.$vo['name'].' ALTER COLUMN `'.$vv['field'].'` SET DEFAULT '.$defaultValue);
                            }
                        } elseif (strpos($fieldType, 'blob') !== false || strpos($fieldType, 'binary') !== false) {
                            Db::query('ALTER TABLE '.$vo['name'].' CHANGE `'.$vv['field'].'` `'.$vv['field'].'` '.$fieldType.' NULL');
                        } else {
                            // 对于其他未知类型，记录日志
                            logOutput('未处理的字段类型: '.$fieldType.' 表名: '.$vo['name'].' 字段名: '.$vv['field'], 'db_field_types');
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            if($this->yun_trylog_is){ logOutput('a:_update_plugind_db  msg:'.$e->getMessage(),'zfcommon_try'); }
        }
    }


    //加载插件/模板中钩子
    public function load_plug_tag(){
        $list = db('plugin')->where(['status'=>1,'type'=>'plugin','act_status'=>1])->order('sort desc,id asc')->select();
        foreach($list as $k=>$vo){
            $func_file = './addons/'.$vo['plugin_name'].'/function.php';
            if(file_exists($func_file)){
                include_once($func_file);
            }
        }
        //模板
        $mb_name = ZFC('zf_tpl_suffix');
        if($mb_name!='' && file_exists('./theme/'.$mb_name.'/function.php')){
            include_once('./theme/'.$mb_name.'/function.php');
        }
    }
}

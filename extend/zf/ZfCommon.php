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
                    if($vv['null']=='NO' && $vv['default']===NULL && $vv['key']!='PRI' ){ 
                        if(is_str_find($vv['type'],'varchar')){
                            Db::query('ALTER TABLE '.$vo['name'].' ALTER COLUMN `'.$vv['field'].'` SET DEFAULT ""');
                        }elseif(is_str_find($vv['type'],'int')){
                            Db::query('ALTER TABLE '.$vo['name'].' ALTER COLUMN `'.$vv['field'].'` SET DEFAULT "0"');
                        }elseif(is_str_find($vv['type'],'text')){
                            Db::query('ALTER TABLE '.$vo['name'].' CHANGE `'.$vv['field'].'` `'.$vv['field'].'` text COLLATE "utf8_general_ci" NULL');
                        }elseif(is_str_find($vv['type'],'decimal')){
                            // decimal(11,2)
                            $isMatched = preg_match_all('/decimal(.*?),(.*?)\)/', $vv['type'], $matches);
                            if($isMatched==1 && isset($matches[2]) && $matches[2]!=''){
                                $_str = str_pad('0.',intval($matches[2][0])+2,'0');
                                Db::query('ALTER TABLE '.$vo['name'].' ALTER COLUMN `'.$vv['field'].'` SET DEFAULT "'.$_str.'"');
                            }
                        }else{
                            // dd($vv);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            if($this->yun_trylog_is){ logOutput('a:_update_plugind_db  msg:'.$e->getMessage(),'zfcommon_try'); }
        }
    }

}
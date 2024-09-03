<?php
namespace zf;
use think\Db;

class ZfDb
{
    /**
     * 导入数据表 sql_tb-->tb_add
     */
    public function tb_add($tb,$sql){
        $isTable = Db::query("SHOW TABLES LIKE '".$tb."'");
        if(!$isTable){
            $r = Db::execute($sql);
            if(!$r){
                return false;exit;
            }
        }
        return true;
    }
    /**
     * tb名字修改
     */
    public function tb_name_edit($tb1,$tb2){
        dd($tb1);
        $isTable = Db::query("SHOW TABLES LIKE '".$tb1."'");
        $isTable_new = Db::query("SHOW TABLES LIKE '".$tb2."'");
        if(isset($isTable[0]) && !isset($isTable_new[0])){
            $sql = "ALTER TABLE ".$tb1." RENAME TO ".$tb2.";";
            Db::query($sql);
            return true;
        }else{
            return false;
        }
    }
    /**
     * 表字段新增
     */
    public function tb_field_add($sql_is,$sql_add){
        $isTable = Db::query($sql_is);
        if(!isset($isTable[0])){
            Db::query($sql_add);
        } 
        return true;
    }
    /**
     * 表字段修改
     */ 
    public function tb_field_edit($sql_is,$sql_add){
        $isTable = Db::query($sql_is);
        if(isset($isTable[0])){
            Db::query($sql_add);
        }
        return true;
    }
    /**
     * 字段是否存在
     */
    public function tb_field_is($sql_is){
        $isTable = Db::query($sql_is);
        if(isset($isTable[0])){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 表字段删除
     */
    public function tb_field_del($sql_is,$sql_add){
        $isTable = Db::query($sql_is);
        if(isset($isTable[0])){
            Db::execute($sql_add);
        }
        return true;
    }
    //数据修改(可直接使用db操作)
    


}
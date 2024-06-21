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
use think\facade\Session;
use think\facade\Request;
use think\Db;
use think\facade\Image;
use OSS\OssClient as AliOssClient;
use think\captcha\Captcha;
use lib\Pinyin;

class Common extends Admin
{
    public function __construct(){
        parent::__construct();
    }
    /**
     * @Author   zf
     * @DateTime 2021-01-25
     * @Name     验证码
     * @return   [type]     [description]
     */
     public function verify(){
        $config =    [
            // 验证码字体大小
            'fontSize'    =>    30,    
            // 验证码位数
            'length'      =>    3,   
            // 关闭验证码杂点
            'useNoise'    =>    false, 
        ];
        ob_clean();
        $captcha = new Captcha($config);
        return $captcha->entry();
    }
    /**
     * @Notes:显示是与否的转换 (dbname status id )
     * @Interface is_switch
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author: 子枫
     * @Time: 2019/11/13   10:39 下午
     */
    public function is_switch(){
        admin_role_check($this->z_role_list,$this->mca,1);
        $dbname = input('dbname');
        $is_show = input('status');
        $id = input('id');
        try {
            if($dbname=='category' || $dbname=='product_cate'){
                $res = ZFTB($dbname)->where(['cid'=> $id])->update(['status' => $is_show]);
            }else{
                $res = ZFTB($dbname)->where(['id'=>$id])->update(['status' => $is_show]);            
            }
            return ZFRetMsg($res,'更新成功','更新失败');
        }catch (Exception $e) {
            return jserror($e->getMessage());
        }
    }
   
    
    /**
     * @Notes    是否推荐
     * @Author   子枫
     * @DateTime 2020-10-15
     * @Email    287851074@qq.com
     * @return   boolean          [description]
     */
     public function is_recommend(){
        admin_role_check($this->z_role_list,$this->mca,1);
        $dbname = input('dbname');
        $is_show = input('status');
        $id = input('id');
        try {
            $res = ZFTB($dbname)->where(['id'=>$id])->update(['recommend' => $is_show]);            
            return ZFRetMsg($res,'更新成功','更新失败');
        }catch (Exception $e) {
            return jserror($e->getMessage());
        }
    }
    public function is_istop(){
        admin_role_check($this->z_role_list,$this->mca,1);
        $dbname = input('dbname');
        $is_show = input('status');
        $id = input('id');
        try {
            $res = ZFTB($dbname)->where(['id'=>$id])->update(['istop' => $is_show]);            
            return ZFRetMsg($res,'更新成功','更新失败');
        }catch (Exception $e) {
            return jserror($e->getMessage());
        }
    }

    /**
     * @Notes:菜单的转换
     * @Interface is_menu
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author: 子枫
     * @Time: 2019/11/13   10:41 下午
     */
    public function is_menu(){
        admin_role_check($this->z_role_list,$this->mca,1);
        $dbname = input('dbname');
        $is_show = input('menu');
        $id = input('id');
        try {
            if($dbname=='category' || $dbname=='product_cate'){
                $res = ZFTB($dbname)->where(['cid'=> $id])->update(['menu' => $is_show]);
            }else{
                $res = ZFTB($dbname)->where(['id'=>$id])->update(['menu' => $is_show]);            
            }     
            return ZFRetMsg($res,'更新成功','更新失败');
        }catch (Exception $e) {
            return jserror($e->getMessage());
        }
    }
    /**
     * @Notes:删除内容
     * @Interface del_post
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author: 子枫
     * @Time: 2019/11/13   10:41 下午
     */
    public function del_post(){
        admin_role_check($this->z_role_list,$this->mca,1);
        $dbname = input('db');
        $id = input('id');
        try {
            if($dbname=='category' || $dbname=='product_cate'){
                $res = ZFTB($dbname)->where(['cid'=> $id])->update(['status' => 9]);
                if($dbname=='category'){
                    ZFTB('post')->where(['cid'=> $id])->update(['status' => 9]);
                }
            }else{
                $res = ZFTB($dbname)->where(['id'=>$id])->update(['status' => 9]);            
            }
            return ZFRetMsg($res,'删除成功','删除失败');
        }catch (Exception $e) {
            return jserror($e->getMessage());
        }
    }

    /**
     * @Notes:批量删除
     * @Interface more_del
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author: 子枫
     * @Time: 2019/11/13   10:43 下午
     */
    public function more_del(){
        admin_role_check($this->z_role_list,$this->mca,1);
        $dbname = input('dbname');
        $ids = input('ids');
        if(!isset($ids) || empty($ids)){
            return jserror('请选择要删除的内容');
        }
        if(!is_array($ids)){
            $ids_list = explode(',',$ids);
        }else{
            $ids_list = $ids;
        }
        try {
            foreach($ids_list as $k=>$vo){
                if($dbname=='category' || $dbname=='product_cate'){
                    ZFTB($dbname)->where(['cid'=>$vo])->update(['status' => 9]);                
                }else{
                    ZFTB($dbname)->where(['id'=> $vo])->update(['status' => 9]);            
                }
            }
            return jssuccess('更新成功');
        }catch (Exception $e) {
            return jserror($e->getMessage());
        }
    }

    /**
     * @Notes:修改值
     * @Interface value_edit
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author: 子枫
     * @Time: 2019/11/13   10:42 下午
     */
    public function value_edit(){
        admin_role_check($this->z_role_list,$this->mca,1);
        $dbname = input('dbname');
        $id = input('id');
        $field = input('field');
        $value = input('value');
        try {
            if($dbname=='category' || $dbname=='product_cate'){
                $res = ZFTB($dbname)->where(['cid'=> $id])->update([$field => $value]);      
            }else{
                $res = ZFTB($dbname)->where(['id'=>$id])->update([$field => $value]);      
            }
            return ZFRetMsg($res,'更新成功','更新失败');
        }catch (Exception $e) {
            return jserror($e->getMessage());
        }
    }

    /**
     * @Notes:上传图片
     * @Interface upload_one
     * @author: 子枫
     * @Time: 2019/11/13   10:43 下午
     */
    // public function upload_one(){
    //     admin_role_check($this->z_role_list,$this->mca,1);
    //     $file = request()->file('file');
    //     $info = $file->validate(['ext'=>ZFC("webconfig.pic_ext")])->move( './public/upload/admin/image');
    //     $getSaveName = str_replace('\\', '/', $info->getSaveName());//win下反斜杠替换成斜杠
    //     $msg = 'http://'.$_SERVER['SERVER_NAME'].'/public/upload/admin/image/'.$getSaveName;
    //     if($msg){
    //         return jssuccess($msg);
    //     }else{
    //         return jserror("error");
    //     }

    // }

    /**
     * @Notes:上传文件
     * @Interface upload_one_file
     * @author: 子枫
     * @Time: 2019/11/13   10:43 下午
     */
    // public function upload_one_file(){
    //     admin_role_check($this->z_role_list,$this->mca,1);
    //     $file2 = request()->file('file');
    //     $info = $file2->validate(['ext'=>ZFC("webconfig.file_ext")])->move('./public/upload/admin/file');
    //     $getSaveName = str_replace('\\', '/', $info->getSaveName());//win下反斜杠替换成斜杠
    //     $msg = 'http://'.$_SERVER['SERVER_NAME'].'/public/upload/admin/file/'.$getSaveName;
    //     return ZFRetMsg($msg,$msg,'error');

    // }

    

    /**
     * @Notes:config配置修改
     * @Interface config_edit
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author: 子枫
     * @Time: 2019/11/13   10:44 下午
     */
    public function config_edit(){
        admin_role_check($this->z_role_list,$this->mca,1);
        $key = input('key');
        $value = input('value');
        //执行转换
        try {
            $res = ZFTB('config')->where(['key'=>$key])->update(['value' => $value]);            
            return ZFRetMsg($res,'更新成功','更新失败');
        }catch (Exception $e) {
            return jserror($e->getMessage());
        }
    }

    /**
     * 230927新增
     * 转换当前状态
     */
    public function is_switch_actstatus(){
        admin_role_check($this->z_role_list,$this->mca,1);
        $dbname = input('dbname');
        $is_show = input('act_status');
        $id = input('id');
        try {
            if($dbname=='category' || $dbname=='product_cate'){
                $res = ZFTB($dbname)->where(['cid'=> $id])->update(['act_status' => $is_show]);
            }else{
                $res = ZFTB($dbname)->where(['id'=>$id])->update(['act_status' => $is_show]);            
            }
            return ZFRetMsg($res,'更新成功','更新失败');
        }catch (Exception $e) {
            return jserror($e->getMessage());
        }
    }

    /**
     * 汉字转拼音
     */
    public function generate_to_title(){
        $title = input('title','');
        $Pinyin = new Pinyin();
        $str = $Pinyin->getAllPY($title,'-');
        $str = str_replace('+','-',$str);
        $str = str_replace('/','-',$str);
        $str = str_replace('?','-',$str);
        $str = str_replace('%','-',$str);
        $str = str_replace('#','-',$str);
        $str = str_replace('&','-',$str);
        $str = str_replace('=','-',$str);
        $str = str_replace('--','-',$str);
        $str = str_replace('--','-',$str);
        return $str;
    }

    
}

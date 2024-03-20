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
use think\facade\Config as TConfig;
use Wmc1125\TpFast\Category;
class Setting extends Admin
{
    public function __construct (){
        parent::__construct();
        $form_widget = new \app\common\widget\Form();
        $this->assign('form_widget',$form_widget);
    } 

    public function category_form_parm()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        $tb_name = input('tb_name','zf_category');
        $t = input('t','1');
        $this->assign('t',$t);
        $id = input('id','');
        if(request()->isPost()){
            $_data = input('post.');
            if($t==1){
                $parm_data = [];
                foreach($_data['name'] as $k=>$vo){
                    $parm_data[$vo] = [
                        'name'=>$vo,
                        'model'=>$_data['model'][$k],
                        'checked'=>$_data['checked'][$k],
                        'sort'=>$_data['sort'][$k],
                        'postion'=>$_data['postion'][$k],
                        'comment'=>$_data['comment'][$k],
                        'theme'=>$_data['theme'][$k],
                        'append'=>$_data['append'][$k],
                        'notes'=>$_data['notes'][$k],
                        'append1'=>$_data['append1'][$k],
                        'append2'=>$_data['append2'][$k],
                        'append3'=>$_data['append3'][$k],
                        'readonly'=>$_data['readonly'][$k],
                    ];
                }
                // dd($parm_data);
                //是否双栏目
                $parm_data['zf_two_column'] = isset_arr_key($_data,'zf_two_column',1);
                //是否开启
                $parm_data['zf_form_status'] = isset_arr_key($_data,'zf_form_status',0);
                // dd($parm_data);
                $save_data['form_parm'] = json_encode($parm_data);
            }elseif($t==2){
                $_form_parm = $_data['form_parm'];
                $_form_parm_arr = explode('----------',$_form_parm);
                if(!in_array($tb_name,['zf_category_model'])){
                    if(isset($_form_parm_arr[0])){
                        $save_data['form_parm'] = trim($_form_parm_arr[0]);
                    }
                    if(isset($_form_parm_arr[1])){
                        $save_data['form_parm_static'] = trim($_form_parm_arr[1]);
                    }
                }else{
                    $save_data['form_parm_static'] = trim($_form_parm_arr[0]);
                }
                
            }elseif($t==4){
                if(!isset($_data['zf_list_form_parm_static'])){
                    $_data['zf_list_form_parm_static'] = [];
                }
                $save_data['form_parm_static'] = implode(',',$_data['zf_list_form_parm_static']);
            }else{
                return ZFRetMsg(false,'','参数不支持'); 
            }
            $save_data['utime'] = time();
            $id = input('id','');
            if($tb_name=='zf_category'){
                $res = db('category')->where('cid',$id)->update($save_data);
            }elseif(in_array($tb_name,['zf_advert','zf_category_model','zf_special','zf_tag'])){
                $res =  Db::table($tb_name)->where('id',$id)->update($save_data);
            }else{
                return ZFRetMsg(false,'','暂不支持数据表');
            }
            
            return ZFRetMsg($res,'保存成功','保存失败');
        }



        if($t==1){
            ###############################
            //过滤key数组
            $gl_key = [
                'cid','form_parm'
            ];
            ###############################
            if($tb_name=='zf_category'){
                $form_parm = db('category')->where('cid',$id)->value('form_parm');
                $meta_key_list = db('meta_key')->where([['tb','=','category'],['status','=',1]])->order('sort asc,id asc')->group('key')->select();
            }elseif(in_array($tb_name,['zf_advert','zf_category_model','zf_special','zf_tag'])){
                $form_parm = Db::table($tb_name)->where('id',$id)->value('form_parm');
                $meta_key_list = db('meta_key')->where([['tb','=',substr($tb_name,3)],['status','=',1]])->order('sort asc,id asc')->group('key')->select();
            }else{
                $this->error('暂不支持');
            }
            if($form_parm==''){
                $form_parm_arr = false;
            }else{
                $form_parm_arr = json_decode($form_parm,true);
            }
            $zf_two_column = isset_arr_key($form_parm_arr,'zf_two_column',1);
            $zf_form_status = isset_arr_key($form_parm_arr,'zf_form_status',0);
            $this->assign('zf_two_column',$zf_two_column);
            $this->assign('zf_form_status',$zf_form_status);
            $_list = Db::query("show full columns from ".$tb_name);
            foreach($meta_key_list as $k=>$vo){
                $_list[] = [
                    'Field'=>'meta['.$vo['key'].']',
                    'Type'=>'',
                    'Null'=>'YES',
                    'Key'=>'',
                    'Default'=>'',
                    'Extra'=>'',
                    'comment'=>$vo['name'],
                ];
            }
            $list_left = [];
            foreach($_list as $k=>$vo){
                if(isset($vo['Field'])){
                    $_name = strtolower($vo['Field']);
                }else{
                    $_name = strtolower($vo['field']);
                }
                if(isset($vo['Comment'])){
                    $vo['comment'] = strtolower($vo['Comment']);
                }else{
                    $vo['comment'] = strtolower($vo['comment']);
                }
                if(!in_array($_name,$gl_key)){
                    
                    //判断是否存在
                    if(isset($form_parm_arr[$_name])){
                        $_model = $form_parm_arr[$_name]['model'];
                        $_checked = $form_parm_arr[$_name]['checked'];
                        $_sort = $form_parm_arr[$_name]['sort'];
                        $_comment = $form_parm_arr[$_name]['comment'];
                        if($_comment==''){
                            $_comment = $vo['comment'];
                        }
                        if(isset($form_parm_arr[$_name]['theme'])){
                            $_theme = $form_parm_arr[$_name]['theme'];
                            // $_theme = 1;

                        }else{
                            $_theme = 1;
                        }
                        if(isset($form_parm_arr[$_name]['append'])){
                            $_append = $form_parm_arr[$_name]['append'];
                        }else{
                            $_append = '';
                        }
                        if(isset($form_parm_arr[$_name]['notes'])){
                            $_notes = $form_parm_arr[$_name]['notes'];
                        }else{
                            $_notes = '';
                        }
                        if(isset($form_parm_arr[$_name]['append1'])){
                            $_append1 = $form_parm_arr[$_name]['append1'];
                        }else{
                            $_append1 = '';
                        }
                        if(isset($form_parm_arr[$_name]['append2'])){
                            $_append2 = $form_parm_arr[$_name]['append2'];
                        }else{
                            $_append2 = '';
                        }
                        if(isset($form_parm_arr[$_name]['append3'])){
                            $_append3 = $form_parm_arr[$_name]['append3'];
                        }else{
                            $_append3 = '';
                        }
                        if(isset($form_parm_arr[$_name]['readonly'])){
                            $_readonly = $form_parm_arr[$_name]['readonly'];
                        }else{
                            $_readonly = '';
                        }
                    }else{
                        $_model = '';
                        $_checked = '0';
                        $_sort = '0';
                        $_comment = $vo['comment'];
                        $_theme = 1;
                        $_append = '';
                        $_notes = '';
                        $_append1 = '';
                        $_append2 = '';
                        $_append3 = '';
                        $_readonly = '';
                    }
                    if($_comment==''){
                        $_checked = '0';
                    }
                    if(isset($vo['Type'])){
                        $_type = $vo['Type'];
                    }else{
                        $_type = $vo['type'];
                    }
                    if(isset($form_parm_arr[$_name]['postion']) && $form_parm_arr[$_name]['postion']=='right'){
                        $list_right[] = [
                            'name'=>$_name,
                            'model'=>$_model,
                            'checked'=>$_checked,
                            'sort'=>$_sort,
                            'postion'=>'right',
                            'comment'=>$_comment,
                            'type'=>$_type,
                            'theme'=>$_theme,
                            'append'=>$_append,
                            'notes'=>$_notes,
                            'append1'=>$_append1,
                            'append2'=>$_append2,
                            'append3'=>$_append3,
                            'readonly'=>$_readonly,
                        ];
                    }else{
                        $list_left[] = [
                            'name'=>$_name,
                            'model'=>$_model,
                            'checked'=>$_checked,
                            'sort'=>$_sort,
                            'postion'=>'left',
                            'comment'=>$_comment,
                            'type'=>$_type,
                            'theme'=>$_theme,
                            'append'=>$_append,
                            'notes'=>$_notes,
                            'append1'=>$_append1,
                            'append2'=>$_append2,
                            'append3'=>$_append3,
                            'readonly'=>$_readonly,
                        ];
                    }
                }
            }
            // 'append1'=>$_data['append1'][$k],
            // 'append2'=>$_data['append2'][$k],
            // 'append3'=>$_data['append3'][$k],
            // 'readonly'=>$_data['readonly'][$k],

            $list = [
                'left'=>(isset($list_left)?$list_left:false),
                'right'=>(isset($list_right)?$list_right:false)
            ];
            $this->assign("list",$list);
        }elseif($t==2){
            //导入参数
        }elseif($t==3){
            //当前参数
            if($tb_name=='zf_category'){
                $res = db('category')->where('cid',$id)->find();
            }elseif(in_array($tb_name,['zf_advert','zf_category_model','zf_special','zf_tag'])){
                $res = Db::table($tb_name)->where('id',$id)->find();
            }else{
                $this->error('暂不支持');
            }
            if(!in_array($tb_name,['zf_category_model'])){
                $form_parm = $res['form_parm'];
                $form_parm .= PHP_EOL.'----------'.PHP_EOL;
            }else{
                $form_parm = '';
            }
            $form_parm .= $res['form_parm_static'];
            $this->assign('form_parm',$form_parm);
        }elseif($t==4){
            //当前参数
            if($tb_name=='zf_category'){
                $form_parm_static = db('category')->where('cid',$id)->value('form_parm_static');
            }elseif(in_array($tb_name,['zf_advert','zf_category_model','zf_special','zf_tag'])){
                $form_parm_static = Db::table($tb_name)->where('id',$id)->value('form_parm_static');
            }else{
                $this->error('暂不支持');
            }
            $this->assign('form_parm_static',$form_parm_static);
        }
        $this->assign('tb_name',$tb_name);
        return view();
    }

    


    
}

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
                    ];
                }
                // dd($parm_data);
                $save_data['form_parm'] = json_encode($parm_data);
            }elseif($t==2){
                $save_data['form_parm'] = $_data['form_parm'];
            }else{
                return ZFRetMsg(false,'','参数不支持'); 
            }
            $save_data['utime'] = time();
            $id = input('id','');
            if($tb_name=='zf_category'){
                $res = db('category')->where('cid',$id)->update($save_data);
            }elseif($tb_name=='zf_advert'){
                $res = db('advert')->where('id',$id)->update($save_data);
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
            }elseif($tb_name=='zf_advert'){
                $form_parm = db('advert')->where('id',$id)->value('form_parm');
                $meta_key_list = db('meta_key')->where([['tb','=','advert'],['status','=',1]])->order('sort asc,id asc')->group('key')->select();
            }else{
                $this->error('暂不支持');
            }
            if($form_parm==''){
                $form_parm_arr = false;
            }else{
                $form_parm_arr = json_decode($form_parm,true);
            }
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

                    }else{
                        $_model = '';
                        $_checked = '0';
                        $_sort = '0';
                        $_comment = $vo['comment'];
                        $_theme = 1;
                        $_append = '';
                        $_notes = '';
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
                        ];
                    }
                }
            }
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
                $form_parm = db('category')->where('cid',$id)->value('form_parm');
            }elseif($tb_name=='zf_advert'){
                $form_parm = db('advert')->where('id',$id)->value('form_parm');
            }else{
                $this->error('暂不支持');
            }
            $this->assign('form_parm',$form_parm);
        }
        return view();
    }

    


    
}

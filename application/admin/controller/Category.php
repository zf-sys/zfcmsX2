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
use Wmc1125\TpFast\Category as cat; 
use think\Db;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Wmc1125\TpFast\GetImgSrc;
  
class Category extends Admin
{
    public function __construct (){
        parent::__construct();
        // $form_widget = new \app\common\widget\Form();
        // $this->assign('form_widget',$form_widget);
    }
 
    /**
     * @Notes:栏目列表
     * @Interface index
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   10:28 下午
     */
    public function index()
    {
        $t = input('t','');
        if($t=='get_theme_cate_file'){
            $theme_name = ZFC('zf_tpl_suffix');
            $theme_path = './theme/'.$theme_name.'/default/cate/';
            $theme_arr = [];
            if(is_dir($theme_path)){
                //获取$theme_path的所有文件
                $theme_arr = [];
                $_theme_arr = scandir($theme_path);
                foreach($_theme_arr as $k=>$vo){
                    if(!is_dir($theme_path.$vo)){
                        if($vo=='.' || $vo=='..'){
                            unset($_theme_arr[$k]);
                        }else{
                            $theme_arr[] = ['value'=>str_replace('.html','',$vo),'name'=>str_replace('.html','',$vo)];
                        }
                    }
                }
            }
            return json_encode(['code'=>1,'message'=>'OK','data'=>$theme_arr,'timestamp'=>time()]);
            // {$form_widget->form_input_select(['title'=>'内容模板名','name'=>'tpl_post','data'=>isset_arr_key($res,'tpl_post',''),'list_arr'=>[],'id_t'=>'id','name_t'=>'name','notes'=>'','url'=>get_domain().'/admin/category/index/t/get_theme_cate_file','theme'=>4])|raw}
        }
        admin_role_check($this->z_role_list,$this->mca);
        $pid =input('pid','0');
        $where[] = ['status','<>',9];
        $type =input('type','');
        $where[] = ['type','=',$type];
        $where = array_merge($where, $this->common_select_tag);
        $this->assign("type",$type);
        // if($pid!='0'){
        //     $where[] = ['pid','=' ,$pid];
        // }
        // $pid = 0;
        $res = ZFTB('category')->field('cid,pid,name,cname,icon,tpl_category,tpl_post,mid,sort,status,menu,form_parm')->where($where)->order("sort asc,cid asc")->select();
        $cat = new cat(array('cid', 'pid', 'name', 'cname')); 
        $list = $cat->getTree($res,$pid); 
        if(!$list){
            $list = [];
        }
        $this->assign("list",$list);
        $where_mlist[] = ['status','=',1];
        $mlist = ZFTB('category_model')->where($where_mlist)->order("id asc")->select();
        if(!$mlist){
            $mlist = [];
        }
        $this->assign("mlist",$mlist);
        $this->assign("pid",$pid);
    	return view();
    }
    /**
     * 批量编辑栏目内容
     */
    public function cate_all_edit(){
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isAjax()){
            $data = input('post.');
            try {
                foreach($data['cid'] as $k=>$vo){
                    $save_data['name'] = $data['name'][$k];
                    $save_data['tpl_category'] = $data['tpl_category'][$k];
                    $save_data['tpl_post'] = $data['tpl_post'][$k];
                    $save_data['mid'] = $data['mid'][$k];
                    $save_data['sort'] = $data['sort'][$k];
                    $save_data['page'] = $data['page'][$k];
                    $save_data['pic'] = $data['pic'][$k];
                    ZFTB('category')->where(['cid'=>$vo])->update($save_data);
                }
                return ZFRetMsg(true,'修改成功','修改失败');
            }catch (Exception $e) {
                return jserror($e->getMessage());
            }
        }

        $pid =input('pid','0');
        $where[] = ['status','<>',9];
        $type =input('type','');
        $where[] = ['type','=',$type];
        $this->assign("type",$type);
        // if($pid!='0'){
        //     $where[] = ['pid','=' ,$pid];
        // }
        // $pid = 0;
        $where = array_merge($where, $this->common_select_tag);
        $res = ZFTB('category')->field('cid,pid,name,cname,icon,tpl_category,tpl_post,mid,sort,status,menu,page,pic')->where($where)->order("sort asc,cid asc")->select();
        $cat = new cat(array('cid', 'pid', 'name', 'cname')); 
        $list = $cat->getTree($res,$pid); 
        if(!$list){
            $list = [];
        }
        $this->assign("list",$list);
        $where_mlist[] = ['status','=',1];
        $mlist = ZFTB('category_model')->where($where_mlist)->order("id asc")->select();
        if(!$mlist){
            $mlist = [];
        }
        $this->assign("mlist",$mlist);
        $this->assign("pid",$pid);
        return view();
    }

    /**
     * @Notes:增加栏目
     * @Interface category_add
     * @author: 子枫
     * @Time: 2019/11/13   10:32 下午
     */
    public function category_add()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        $data = input('post.');
        $data['ctime'] = time();
        if($data['name']==''){
            return jserror('栏目名不能为空');exit;
        }
        $data['utime'] = time();
        $data['status'] = ZFC('webconfig.category_status');
        if($data['status']==''){
            $data['status'] = 0;
        }
        $data = array_merge($data,$this->common_tag);
        //父类的部分参数
        if($data['pid']!='0'){
            $partent_res = db('category')->where(['cid'=>$data['pid']])->find();
            if($data['tpl_category']==''){
                $data['tpl_category'] = $partent_res['tpl_category'];
            }
            if($data['tpl_post']==''){
                $data['tpl_post'] = $partent_res['tpl_post'];
            }
        }
        deal_meta_data_add('category',$data);
        
    }

    /**
     * @Notes:修改栏目
     * @Interface category_edit
     * @return \think\response\View|void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author: 子枫
     * @Time: 2019/11/13   10:32 下午
     */
    public function category_edit()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isAjax()){
            $data_r = input('post.');
            if(isset($data_r['editor_type']) &&  $data_r['editor_type']=='tinymce'){
                //tinymce 编辑器
                $data= [];
                foreach($data_r['data'] as $k=>$vo){
                    $data[$vo['name']] = $vo['value'];
                }
                $data['content'] = $data_r['content'];
            }elseif(isset($data_r['editor_type']) &&  $data_r['editor_type']=='meditor'){
                //meditor 编辑器
                $data = $data_r;
                //删除编辑器多余字符
                unset($data['content-html-code']);
                unset($data['editor_type']);
            }else{
                $data = $data_r;
            }

            if($data['t']==1){
                $msg['code'] = 1;
            }else{
                $msg['code']=0;
            }
            unset($data['t']);
            if(isset($data['ctime']) && $data['ctime']!=''){
                $data['ctime'] =  strtotime($data['ctime']);
            }else{
                $data['ctime'] =  time();
            }
            $key_list = array_keys($data);
            $_temp_list = [];
            foreach($key_list as $k=>$vo){
                if(strpos($vo,'zf_list_') === 0){
                    $_temp_list[] = $vo;  
                }
            }
            if($_temp_list){
                foreach($_temp_list as $k=>$vo){
                    if(isset($data[$vo]) && is_array($data[$vo])){
                        $data[explode('zf_list_',$vo)[1]] = implode(",", $data[$vo]);
                        unset($data[$vo]);
                    }
                }
            }
            #########过滤多选时,如果删除了全部时的问题#########
            $cate_gl_arr_dx = ZFTB('category')->where(['cid'=>$data['cid']])->value('cate_gl_arr_dx');
            if($cate_gl_arr_dx && $cate_gl_arr_dx!=''){
                $gl_arr_dx = explode(',',$cate_gl_arr_dx);
                foreach($gl_arr_dx as $k=>$vo){
                    if(!isset($data[$vo])){
                        $data[$vo] = '';
                    }
                }
            }
            $data['utime'] = time();
            deal_meta_data_edit('category',$data,'cid');
            

        }
        $res =ZFTB('category')->where(['cid'=>input('cid')])->find();
        $meta_json = ZFTB('meta_data')->where([['tb','=','category'],['post_id','=',$res['cid']],['status','<>',9]])->value('meta_data');
        $res['meta'] = json_decode($meta_json,true);
        $this->assign("t",0);
        $this->assign("res",$res);
        $type =input('type','');
        $where[] = ['type','=',$res['type']];
        $where[] = ['status','<>',9];
        $where[] = ['cid','<>',$res['cid']];
        $this->assign("type",$type);
        $p_list = ZFTB('category')->where($where)->select();
        $cat = new cat(array('cid', 'pid', 'name', 'cname')); 
        $plist = $cat->getTree($p_list); 
        if(!$plist){
            $plist = [];
        }
        $plist[999] =
        [
            'cid'=>0,
            'name'=>'顶级目录',
            'cname'=>'顶级目录'
        ];
        $this->assign("plist",$plist);
        $mlist = ZFTB('category_model')->where(['status'=>1])->select();
        if(!$mlist){
            $mlist = [];
        }
        $this->assign("mlist",$mlist);

        #####参数模式
        $form_parm_arr = getFormParams($res);
        $this->assign('form_parm_arr',$form_parm_arr);


        //获取模板文件名
        $now_theme = ZFC('zf_tpl_suffix');
        $theme_arr = [['name'=>'默认','id'=>'']];
        if($now_theme!=''){
            //获取对应目录下的文件
            $theme_path = './theme/'.$now_theme.'/cate/';
            //判断目录是否存在
            if(is_dir($theme_path)){
                $_theme_arr = scandir($theme_path);
                foreach($_theme_arr as $k=>$vo){
                    //判断是否是文件,非文件夹
                    if(!is_dir($theme_path.$vo)){
                        if($vo=='.' || $vo=='..'){
                            unset($_theme_arr[$k]);
                        }else{
                            $theme_arr[] = ['name'=>$vo,'id'=>str_replace('.html','',$vo)];
                        }
                    }
                }
            }
        }
        $this->assign('theme_arr',$theme_arr);

        $this->assign('hook_data',['category',$res['cid'],'edit',$res]);
        return view();
    }

    /**
     * @Notes:模型列表
     * @Interface category_model
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   10:33 下午
     */
    public function category_model()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        $t = input('t','');
        if($t=='export'){
            $mid = input('id','');
            $list = db('category_model_parm')->field('id,mid',true)->where([['status','<>',9],['mid','=',$mid]])->select();
            echo json_encode($list);
            die;
        }elseif($t=='import'){
            if(request()->isPost()){
                $data = input('post.');
                $content_json = trim($data['content']);
                $list = json_decode($content_json,true);
                if(!$list){
                    return ZFRetMsg(false,'','json数据错误');
                }
                foreach($list as $k=>$vo){
                    $is_res = db('category_model_parm')->where([['mid','=',$data['mid']],['key','=',$vo['key']],['status','<>',9]])->find();
                    if($is_res){
                        unset($list[$k]);
                    }else{
                        $list[$k]['mid'] = $data['mid'];
                    }
                }
                
                $res = ZFTB('category_model_parm')->insertAll($list);
                if($res){
                    return ZFRetMsg(true,'导入成功','');
                }
                if(count($list)==0){
                    return ZFRetMsg(false,'','没有字段可供更新');
                }
            }
            $mid = input('id','');
            return view('category/category_model_import',['mid'=>$mid]);
        }

         //读取
        $list = ZFTB('category_model')->where([['status','<>',9]])->order("id asc")->select();
        $this->assign("list",$list);
        return view();
    }


    /**
     * @Notes:模型列表-增加
     * @Interface category_model_add
     * @return \think\response\View|void
     * @author: 子枫
     * @Time: 2019/11/13   10:33 下午
     */
    public function category_model_add()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isPost()){ 
            $data = input('post.');
            if($data['name']=='' || $data['model']==''){
                return jserror('请填写信息');exit;
            }
            // $data = array_merge($data,$this->common_tag);
            try {
                $res =ZFTB('category_model')->insert($data);
                return ZFRetMsg($res,'新增成功','新增失败');
            }catch (Exception $e) {
                return jserror($e->getMessage());
            }
        }  
        return view();   
    }

    /**
     * @Notes:模型列表-修改
     * @Interface category_model_edit
     * @return \think\response\View|void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author: 子枫
     * @Time: 2019/11/13   10:33 下午
     */
    public function category_model_edit()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isPost()){
            $data = input('post.');
            try {
                $res =  ZFTB('category_model')->where(['id'=>$data['id']])->update($data);
                return ZFRetMsg($res,'修改成功','修改失败');
            }catch (Exception $e) {
                return jserror($e->getMessage());
            }
        } 
        $res = ZFTB('category_model')->where(['id'=>input('id')])->find();
        $this->assign("res",$res);
        return view();
    }

    /**
     * @Notes:根据mid跳转相应页面
     * @Interface post_list
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   10:34 下午
     */
    public function post_list()
    {
        admin_role_check($this->z_role_list,$this->mca);
        // 栏目id
        $cid = input('cid',0);
        $mid = input('mid');
        $t = input('t',0);
        $this->assign('t',$t);

        if(!$mid){
            die("模型未选择");
        }
        //查询该模型是否开启
        $m_res =  ZFTB('category_model')->where(['id'=>$mid])->find();
        $this->assign("cid",$cid);

        if(!$m_res || $m_res['status']==0){
            die("该模型未找到或该模型未开启");
        }
        //如果是单页,加载编辑页面
        if($mid==1){
            $this->assign("m_res",$m_res);
            $pres =ZFTB('category')->where(['status'=>1])->select();
            $cat = new cat(array('cid', 'pid', 'name', 'cname')); 
            $plist = $cat->getTree($pres); 
            if(!$plist){
                $plist = [];
            }
            $plist[999] =
            [
                'cid'=>0,
                'name'=>'顶级目录',
                'cname'=>'顶级目录'
            ];
            $this->assign("plist",$plist);
            $mlist =  ZFTB('category_model')->where(['status'=>1])->select();
            if(!$mlist){
                $mlist = [];
            }
            $this->assign("mlist",$mlist);
            $res = ZFTB('category')->where(['cid'=>$cid])->find();
            $meta_json = ZFTB('meta_data')->where([['tb','=','category'],['post_id','=',$res['cid']],['status','<>',9]])->value('meta_data');
            $res['meta'] = json_decode($meta_json,true);
            $this->assign("res",$res);
            #####参数模式
            $form_parm_arr = getFormParams($res);
            $this->assign('form_parm_arr',$form_parm_arr);

            //获取模板文件名
            $now_theme = ZFC('zf_tpl_suffix');
            $theme_arr = [['name'=>'默认','id'=>'']];
            if($now_theme!=''){
                //获取对应目录下的文件
                $theme_path = './theme/'.$now_theme.'/cate/';
                //判断目录是否存在
                if(is_dir($theme_path)){
                    $_theme_arr = scandir($theme_path);
                    foreach($_theme_arr as $k=>$vo){
                        //判断是否是文件,非文件夹
                        if(!is_dir($theme_path.$vo)){
                            if($vo=='.' || $vo=='..'){
                                unset($_theme_arr[$k]);
                            }else{
                                $theme_arr[] = ['name'=>$vo,'id'=>str_replace('.html','',$vo)];
                            }
                        }
                    }
                }
            }
            $this->assign('theme_arr',$theme_arr);
            $this->assign('hook_data',['category',$cid,'edit',$res]);

            return view('/category/category_edit');
        }else{
            //如果是内容页,加载列表页
            $where_type = input('where_type','');
            if($where_type=='wsl'){
                $where[] = ['bd_sl','=','0'];
            }elseif($where_type=='sl'){
                $where[] = ['bd_sl','=',1];
            }elseif($where_type=='fb'){
                $where[] = ['status','=',1];
            }elseif($where_type=='wfb'){
                $where[] = ['status','=','0'];
            }
            $where[] = ['status','<>',9];
            $where[] = ['cid','in',$this->get_child_id($cid)];
            $keyword = input("get.keyword".'');
            if($keyword!=''){
                $where[] = ['title|content|summary','like','%'.$keyword.'%'];
            }
            $list = ZFTB('post')->where($where)->order("recommend desc,sort desc,id desc")->paginate(10,false,['query' => request()->param()]);
            if(!$list){ 
                $list = [];
            }
            $page = $list->render();
            $this->assign("list",$list);
            $this->assign("page",$page);
            $res =  ZFTB('category')->where(['cid'=>$cid])->find();
            $this->assign("res",$res);
            $this->assign("mid",$mid);
            $this->assign("keyword",$keyword);

            if($m_res['is_parm']==1){
                $tpl = '/category/zf_tpl/list_'.$m_res['model'];
            }else{
                $tpl = '/category/'.$m_res['model'].'/index';
            }
            if(!is_file('./application/admin/view/'.$tpl.'.html')){
                $tpl = 'category/zf_tpl/index';
            }
            //栏目
            $pres =ZFTB('category')->where(['status'=>1])->select();
            $cat = new cat(array('cid', 'pid', 'name', 'cname'));
            $plist = $cat->getTree($pres);
            if(!$plist){
                $plist = [];
            }
            $plist[999] =
            [
                'cid'=>0,
                'name'=>'顶级目录',
                'cname'=>'顶级目录'
            ];
            $this->assign("plist",$plist);
            return view($tpl);
        }
    }

    /**
     * @Notes:内容列表(主页面)
     * @Interface post_all_list
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   10:34 下午
     */
    public function post_all_list()
    {
        admin_role_check($this->z_role_list,$this->mca);
        $where[] = ['status','=',1];
        $where = array_merge($where, $this->common_select_tag);
        $data =ZFTB('category')->where($where)->order("sort asc,cid asc")->select();
        $cat = new cat(array('cid', 'pid', 'name', 'cname','mid')); 
        $pro_menu_list = $cat->getTree($data); 
        $this->assign('pro_menu_list', $pro_menu_list);
        return view();   

    }

    /**
     * @Notes:内容增加
     * @Interface post_add_pl
     * @return \think\response\View|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   10:35 下午
     */
    
    public function post_add_pl(){
        if(request()->isPost()){
            $data = input('post.');
            $title_arr = explode(',',$data['titles']);
            $arr = [];
            foreach($title_arr as $k=>$vo){
                if($vo!=''){
                    $arr[] = [
                        'cid'=>$data['cid'],
                        'title'=>$vo,
                        'status'=>0,
                        'ctime'=>time(),
                        'lang'=>$this->lang,
                    ];
                }   
            }
            if(count($arr)==0){
                $res = false;
            }else{
                try {
                    $res = ZFTB('post')->insertAll($arr);
                }catch (Exception $e) {
                    return jserror($e->getMessage());
                }
            }
            return ZFRetMsg($res,'新增成功','新增失败'); 
        }

        $cid = input('cid','');
        $this->assign('cid',$cid);
        return view();
    }
    public function post_add()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
            
        if(request()->isPost()){
            $data_r = input('post.');
            if(isset($data_r['editor_type']) &&  $data_r['editor_type']=='tinymce'){
                //tinymce 编辑器
                $data= [];
                foreach($data_r['data'] as $k=>$vo){
                    $data[$vo['name']] = $vo['value'];
                }
                unset($data_r['data']);
                unset($data_r['editor_type']);
                foreach($data_r as $k=>$vo){
                    if(strpos($k,'@tinymce') !== false){ 
                       $_temp_key =  substr($k,0,strlen($k)-8);
                        $data[$_temp_key] = $vo;

                    } 
                }
            }elseif(isset($data_r['editor_type']) &&  $data_r['editor_type']=='meditor'){
                //meditor 编辑器
                $data = $data_r;
                //删除编辑器多余字符
                unset($data['content-html-code']);
                unset($data['editor_type']);
            }else{
                $data = $data_r;
            }

            if($data['id']!=''){
                //编辑
                if(isset($data['ctime']) && $data['ctime']!=''){
                    $data['ctime'] =  strtotime($data['ctime']);
                }else{
                    $data['ctime'] =  time();
                }
                if(isset($data['pic']) && $data['pic']=='' && isset($data['content'])){
                    $data['pic'] = GetImgSrc::src($data['content'], 1);
                }
                if(isset($data['relevan_id'])){
                    unset($data['keyword']);
                }
                $key_list = array_keys($data);
                $_temp_list = [];
                foreach($key_list as $k=>$vo){
                    if(strpos($vo,'zf_list_') === 0){
                        $_temp_list[] = $vo;  
                    }
                }
                // dd($_temp_list);
                if($_temp_list){
                    foreach($_temp_list as $k=>$vo){
                        if(isset($data[$vo]) && is_array($data[$vo])){
                            $data[explode('zf_list_',$vo)[1]] = implode(",", $data[$vo]);
                            unset($data[$vo]);
                        }
                    }
                }else{
                    // //查询字段
                    // $mid = ZFTB('post p')
                    //     ->where(['p.id'=>$data['id']])
                    //     ->join(ZFJoinStrLang('category c'),'c.cid = p.cid')
                    //     ->value('c.mid');
                    // $tb_parm_list = ZFTB('category_model_parm')->where([['status','<>',9],['is_multi','=',1],['mid','=',$mid]])->order("position asc,sort asc, id asc")->select();
                    // // 判断是否含有该字段,没有则为空
                    // foreach($tb_parm_list as $k=>$vo){
                    //     if(!isset($data[$vo['key']])){
                    //         $data[$vo['key']] = '';
                    //     }
                    // }
                }
                // if(isset($data['tags']) && isset($data['content'])){
                //     $data['content'] = content2keyword($data['content'],$data['tags']);
                // }
                // if(isset($data['summary']) && $data['summary']=='' &&isset($data['content']) && $data['content']!='' ){
                //     $data['summary'] = mb_substr($data['content'],0,150,'utf8');
                // }

                #########过滤多选时,如果删除了全部时的问题#########
                $post_gl_arr_dx = ZFTB('category')->where(['cid'=>$data['cid']])->value('post_gl_arr_dx');
                if($post_gl_arr_dx && $post_gl_arr_dx!=''){
                    $gl_arr_dx = explode(',',$post_gl_arr_dx);
                    foreach($gl_arr_dx as $k=>$vo){
                        if(!isset($data[$vo])){
                            $data[$vo] = '';
                        }
                    }
                }

                doZfAction('sys_post_edit',['type'=>'controller','data'=>$data]);
                unset($data['temp']);
                $data['utime'] = time();
                $data = apply_filters('admin_post_save',$data,'array');

                deal_meta_data_edit('post',$data,'id');
                
            }else{
                $data = array_merge($data,$this->common_tag);
                if(isset($data['ctime']) && $data['ctime']!=''){
                    $data['ctime'] =  strtotime($data['ctime']);
                }else{
                    $data['ctime'] =  time();
                }
                // if(isset($data['pic']) && $data['pic']=='' && isset($data['content'])){
                //     $data['pic'] = GetImgSrc::src($data['content'], 1);
                // }
                // 关联
                if(isset($data['relevan_id'])){
                    unset($data['keyword']);
                }
                $key_list = array_keys($data);
                $_temp_list = [];
                foreach($key_list as $k=>$vo){
                    if(strpos($vo,'zf_list_') === 0){
                        $_temp_list[] = $vo;  
                    }
                }
                if($_temp_list){
                    foreach($_temp_list as $k=>$vo){
                        if(isset($data[$vo]) && is_array($data[$vo])){
                            $data[explode('zf_list_',$vo)[1]] = implode(",", $data[$vo]);
                            unset($data[$vo]);
                        }
                    }
                }elseif(count($_temp_list)==0){
                    //数组为空,不做操作
                }else{
                    //查询字段 暂不使用
                    // $mid = ZFTB('post p')
                    //     ->where(['p.id'=>$data['id']])
                    //     ->join(ZFJoinStrLang('category c'),'c.cid = p.cid')
                    //     ->value('c.mid');
                    // $tb_parm_list = ZFTB('category_model_parm')->where([['status','<>',9],['is_multi','=',1],['mid','=',$mid]])->order("position asc,sort asc, id asc")->select();
                    // // 判断是否含有该字段,没有则为空
                    // foreach($tb_parm_list as $k=>$vo){
                    //     if(!$data[$vo['name']]){
                    //         $data[$vo['key']] = '';
                    //     }
                    // }
                }
                // if(isset($data['tags']) && isset($data['content'])){
                //     $data['content'] = content2keyword($data['content'],$data['tags']);
                // }
                // if(isset($data['summary']) && $data['summary']=='' &&isset($data['content']) && $data['content']!='' ){
                //     $data['summary'] = mb_substr($data['content'],0,150,'utf8');
                // }


                doZfAction('sys_post_add',['type'=>'controller','data'=>$data]);
                unset($data['temp']);
                $data['utime'] = time();
                deal_meta_data_add('post',$data,'id');

            }
        } 
        $id = input("id",'');
        $cj_id = input("cj_id",'');
        if($id=='' && $cj_id==''){
            $this->assign("act",'add');
            $this->assign("data_res",[]);
            $mid = input("mid",'14');
            $cid = input("cid",'');
            $data_res['cid'] = $cid;
            $data_res['meta'] = false;
            $this->assign("data_res",$data_res);
            $content_act = 'add';
        }else{
            //编辑
            if($cj_id==''){
                $data_res = ZFTB('post')->where(['id'=>input('id')])->find();
            }else{
                $data_res = ZFTB('post')->where(['cj_id'=>$cj_id])->find();
                if(!$data_res){
                    $this->error('文章不存在');die;
                }
            }
            $meta_json = ZFTB('meta_data')->where([['tb','=','post'],['post_id','=',$data_res['id']],['status','<>',9]])->value('meta_data');
            $data_res['meta'] = json_decode($meta_json,true);
            $this->assign("data_res",$data_res);
            $cid = input("cid",$data_res['cid']);
            $mid = ZFTB('category')->where(['cid'=>$cid])->value('mid');
            $this->assign("act",'edit');
            $content_act = 'edit';
        }
        $m_res =ZFTB('category_model')->field('model,is_two,is_parm,id,form_parm_static')->where(['id'=>$mid])->find();
        if($m_res['is_parm']==1){
            if(is_file('./application/admin/view/category/zf_tpl/add_'.$m_res['model'].'.html')){
                $tpl = 'category/zf_tpl/add_'.$m_res['model'];
            }else{
                $tpl = 'category/zf_tpl/add';
            }
            // $tpl = '/category/zf_tpl/add';
            $this->assign('cid',$cid);
            $this->assign('mid',$mid);
            $m_list =ZFTB('category_model_parm')->where(['mid'=>$mid,'status'=>1])->order('sort asc,id asc')->select();
            $this->assign('m_list',$m_list);
            $this->assign('m_res',$m_res);
        }else{
            $tpl = '/category/'.$m_res['model'].'/add';
            $this->assign('cid',$cid);
            $this->assign('mid',$mid);
            $this->assign('m_res',$m_res);
        }

        $plist_where[] = ['status','<>',9];
        $plist_where = array_merge($plist_where, $this->common_select_tag);
        $pres =ZFTB('category')->where($plist_where)->select();
        $cat = new cat(array('cid', 'pid', 'name', 'cname')); 
        $plist = $cat->getTree($pres); 
        if(!$plist){
            $plist = [];
        }
        $this->assign("plist",$plist);


        #####参数模式
        $form_parm_arr = getFormParams($m_res);
        $this->assign('form_parm_arr',$form_parm_arr);
        do_action('admin_post_after',$content_act,$data_res);
        $this->assign('hook_data',['post',$id,$content_act,$data_res]);
        return view($tpl);
    }

    // public function tj_bdts($url=''){
    //     $bd_tsjk = $this->parm_data['bd_tsjk'];
    //     $urls = $url;
    //     $api = 'http://data.zz.baidu.com/urls?'.$bd_tsjk;
    //     $ch = curl_init();
    //     $options =  array(
    //         CURLOPT_URL => $api,
    //         CURLOPT_POST => true,
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_POSTFIELDS => implode("\n", $urls),
    //         CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
    //     );
    //     curl_setopt_array($ch, $options);
    //     $result = curl_exec($ch);
    // }

    /**
     * @Notes:导入内容
     * @Interface import
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   10:35 下午
     */
    public function import(){
        admin_role_check($this->z_role_list,$this->mca);
        $cid = input("cid");
        //获取表格的大小，限制上传表格的大小5M
        $file_size = $_FILES['file']['size'];
        if ($file_size > 5 * 1024 * 1024) {
            $this->error('文件大小不能超过5M');
            exit();
        }
        //限制上传表格类型
        $fileExtendName = substr(strrchr($_FILES['file']["name"], '.'), 1);
        //application/vnd.ms-excel  为xls文件类型
        if ($fileExtendName == 'csv') {
            $this->error('须为xls或xlsx格式,不能是csv格式！');
            exit();
        }
        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            if ($fileExtendName =='xlsx') {
                $objReader = IOFactory::createReader('Xlsx');
                $filename = $_FILES['file']['tmp_name'];
            }elseif ($fileExtendName =='xls') {
                $objReader = IOFactory::createReader('Xls');
                $filename = $_FILES['file']['tmp_name'];
            }elseif ($fileExtendName=='csv') {
                $objReader = IOFactory::createReader('Csv');
                $filename = $_FILES['file']['tmp_name'];
            }
            $objPHPExcel = $objReader->load($filename,$encode = 'utf-8');  //$filename可以是上传的表格，或者是指定的表格
            $sheet = $objPHPExcel->getSheet(0);   //excel中的第一张sheet
            $highestRow = $sheet->getHighestRow();       // 取得总行数
            //定义$usersExits，循环表格的时候，找出已存在的。
            $usersExits = [];
            //循环读取excel表格，整合成数组。如果是不指定key的二维，就用$data[i][j]表示。
            for ($j = 2; $j <= $highestRow; $j++) {
                $data[$j - 2] = [
                    'title' => $objPHPExcel->getActiveSheet()->getCell("A" . $j)->getValue(),
                    'append' => $objPHPExcel->getActiveSheet()->getCell("B" . $j)->getValue(),
                    'cid' => $cid,
                    'ctime' => time()
                ];
                // 看下用户名是否存在。将存在的用户名保存在数组里。
                $userExist = ZFTB('post')->where(['title'=>$data[$j - 2]['title'],'append'=>$data[$j-2]['append']])->find();
                if ($userExist) {
                    unset($data[$j-2]);
                }
            }
            //如果有已存在的用户名，就不插入数据库了。
            // if ($usersExits != []) {
            //     //把数组变成字符串，向前端输出。
            //     $c = implode(" / ", $usersExits);
            // }else{
            //     $c = "无";
            // }
            //插入数据库
            try {
                $res = ZFTB('post')->insertAll($data);
                if ($res) {
                    return jssuccess("导入成功!!");
                }else{
                    return jserror("error");
                }
            }catch (Exception $e) {
                return jserror($e->getMessage());
            }
        }
    }

    /**
     * @Notes:根据关键字搜索内容
     * @Interface search_post
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   10:36 下午
     */
    public function ajax_search_post(){
        admin_role_check($this->z_role_list,$this->mca);
        $kwd = input('key','');
        $where[] = ['status','=',1];
        $where[] = ['relevan_id','=',0];
        if($kwd!='all'){
            $where[] = ['title','like','%'.$kwd.'%'];
        }
        $list =  ZFTB('post')->where($where)->order('id desc')->select();
        return ZFRetMsg($list,$list,'error');

    }

    /**
     * @Notes:获取内容中的图片并保存到 post_parm_pic
     * @Interface get_content_pic_list
     * @param $id
     * @author: 子枫
     * @Time: 2019/11/13   10:36 下午
     */
    // public function get_content_pic_list($id){
    //     admin_role_check($this->z_role_list,$this->mca);
    //     $id = input('id',$id);
    //     $content = ZFTB('post')->where(['id'=>$id])->value('content');
    //     for($i=1;$i<=100;$i++){
    //         $parm_list_src[$i]['pid'] = $id;
    //         $parm_list_src[$i]['ctime'] = time() ;
    //         $parm_list_src[$i]['status'] = 1;
    //         $parm_list_src[$i]['pic'] = GetImgSrc::src($content, $i);  
    //         if(empty($parm_list_src[$i]['pic'])){
    //           unset($parm_list_src[$i]);
    //           break;
    //         }
    //     }
    //     try {
    //         foreach($parm_list_src as $k=>$vo){
    //             $_is = ZFTB('post_parm_pic')->where(['pic'=>$vo['pic'],'pid'=>$vo['pid']])->value('id');
    //             if(!$_is){
    //                 ZFTB('post_parm_pic')->insert($vo);
    //             }
    //         }
    //     }catch (Exception $e) {
    //         return jserror($e->getMessage());
    //     }
    //     return jssuccess('已保存');

    // }

    /**
     * @Notes:模型的参数列表
     * @Interface category_model_parm
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   10:37 下午
     */
    public function category_model_parm()
    {
        admin_role_check($this->z_role_list,$this->mca);
        $all_list = Db::query("SHOW FULL COLUMNS FROM zf_".'post');
        $meta_key_list = db('meta_key')->where([['tb','=','post'],['status','=',1]])->order('sort asc,id asc')->group('key')->select();
        foreach($meta_key_list as $k=>$vo){
            $all_list[] = [
                'Field'=>'meta['.$vo['key'].']',
                'Type'=>'',
                'Null'=>'YES',
                'Key'=>'',
                'Default'=>'',
                'Extra'=>'',
                'comment'=>$vo['name'],
            ];
        }
        $this->assign("all_list",$all_list);
         //读取
        $mid = input('mid',0);
        $where[] = ['status','<>',9];
        $where[] = ['mid','=',$mid];
        $list = ZFTB('category_model_parm')->where($where)->order("position asc,sort asc, id asc")->select();
        $this->assign("list",$list);
        $this->assign("mid",$mid);
        $key_list = $all_list;
        foreach($key_list as $k1=>$vo1){
            foreach($list as $k2=>$vo2){
                if(!isset($vo1['Field'])){
                    $vo1['Field'] = $vo1['field'];
                    $key_list[$k1]['Field'] = $vo1['field'];
                }
                if($vo2['key']==$vo1['Field'] || $vo1['Field']=='id'){
                    unset($key_list[$k1]);
                }
            }
        }
        $this->assign("key_list",$key_list);

        return view();

    }

    /**
     * @Notes:模型的参数-增加
     * @Interface category_model_parm_add
     * @return \think\response\View|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: 子枫
     * @Time: 2019/11/13   10:37 下午
     */
     public function category_model_parm_add()
     {
        //  admin_role_check($this->z_role_list,$this->mca,1);
         if(request()->isPost()){ 
             $data = input('post.');
             if($data['name']==''){
                 return jserror('请填写信息');exit;
             }
             //判断是否存在
             $where[] = ['status','<>',9];
             $where[] = ['mid','=',$data['mid']];
             $where[] = ['key','=',$data['key']];
             $is_res =ZFTB('category_model_parm')->where($where)->find();
             if($is_res){
                return jserror('该字段已存在');exit;
             }

            //  $data = array_merge($data,$this->common_tag);
            try {
                $res =ZFTB('category_model_parm')->insert($data);
                return ZFRetMsg($res,'新增成功','新增失败');
            }catch (Exception $e) {
                return jserror($e->getMessage());
            }
         }  
         return view();   
     }

    /**
     * @Notes:模型的参数-修改
     * @Interface category_model_parm_edit
     * @return \think\response\View|void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author: 子枫
     * @Time: 2019/11/13   10:38 下午
     */
     public function category_model_parm_edit()
     {
        //  admin_role_check($this->z_role_list,$this->mca,1);
         if(request()->isPost()){
            $data = input('post.');
            try {
                $res =  ZFTB('category_model_parm')->where(['id'=>$data['id']])->update($data);
                return ZFRetMsg($res,'修改成功','修改失败');
            }catch (Exception $e) {
                return jserror($e->getMessage());
            }
         } 
         $res = ZFTB('category_model_parm')->where(['id'=>input('id')])->find();
         $this->assign("res",$res);
         return view();
     }

     public function special()
    {
        admin_role_check($this->z_role_list,$this->mca);
        $where[] = ['status','<>',9];
        $where = array_merge($where, $this->common_select_tag);
        $group_list = ZFTB('special')->where($where)->order("id asc")->paginate(10,false,['query' => request()->param()]);
        $page = $group_list->render();
        $this->assign("group_list",$group_list);
        $this->assign("page",$page);
        return view();
    }

    public function special_add()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isPost()){ 
            $data = input('post.');
            $data['ctime'] = time();
            $data = array_merge($data,$this->common_tag);
            deal_meta_data_add('special',$data,'id');

        }  
        $res['meta'] = false;
        $this->assign("res",$res);
        #####参数模式
        $form_parm_arr = getFormParams($res);
        $this->assign('form_parm_arr',$form_parm_arr);
        $this->assign('hook_data',['special','','add',$res]);
        return view();   

         
    }


    public function special_edit()
    {
        admin_role_check($this->z_role_list,$this->mca,1);   
        if(request()->isPost()){
            // $data = input('post.');
            $data_r = input('post.');
            if(isset($data_r['editor_type']) &&  $data_r['editor_type']=='tinymce'){
                //tinymce 编辑器
                $data= [];
                foreach($data_r['data'] as $k=>$vo){
                    $data[$vo['name']] = $vo['value'];
                }
                $data['content'] = $data_r['content'];
            }else{
                $data = $data_r;
            }
            if(isset($data['ctime']) && $data['ctime']!=''){
                $data['ctime'] =  strtotime($data['ctime']);
            }else{
                $data['ctime'] =  time();
            }
            deal_meta_data_edit('special',$data,'id');

        } 
        $res =  ZFTB('special')->where(['id'=>input('id')])->find();
        $meta_json = ZFTB('meta_data')->where([['tb','=','special'],['post_id','=',$res['id']],['status','<>',9]])->value('meta_data');
        $res['meta'] = json_decode($meta_json,true);
        $this->assign("res",$res);
        #####参数模式
        $form_parm_arr = getFormParams($res);
        $this->assign('form_parm_arr',$form_parm_arr);
        $this->assign('hook_data',['special','','edit',$res]);
        return view('/category/special_add');
    }
    public function special_post_edit()
    {
        admin_role_check($this->z_role_list,$this->mca,1);   
        // if(request()->isPost()){
        //     $data = input('post.');
        //     if(!isset($data['ids'])){
        //         return ZFRetMsg(null,'','请勾选相关联内容');
        //     }
        //     $arr = []; 
        //     foreach($data['ids'] as $k=>$vo){
        //         $arr[$k]['post_id'] =  $vo;
        //         $arr[$k]['special_id'] =$data['id'];
        //     }
        //     ZFTB('special_post')->where(['special_id'=>$data['id']])->delete();

        //     $res =ZFTB('special_post')->insertAll($arr);
        //     return ZFRetMsg($res,'修改成功','修改失败');
        // } 
        


        if(request()->isPost()){
            $data = input('post.');
            $data['ctime'] = time();
            $data['status'] = 1;
            if(isset($data['post_id']) && $data['post_id']!=''){
                try {
                    $res = ZFTB('special_post')->insert($data);
                }catch (Exception $e) {
                    return jserror($e->getMessage());
                }
            }else{
                $res = false;
            }
            return ZFRetMsg($res,'提交成功','提交失败');
        } 

        $id = input('id','');
        $this->assign("special_id",$id);
        
        $list =  ZFTB('special_post sp')
                ->field('sp.*,p.title as p_title,s.name as s_name')
                ->where([['sp.special_id','=',$id],['sp.status','<>',9]])
                ->join('special s','s.id=sp.special_id')
                ->join('post p','p.id=sp.post_id')
                ->paginate(10,false,['query' => request()->param()]);
        $page = $list->render();
        $this->assign("page",$page);
        $this->assign("list",$list);

        $list2 =  ZFTB('special_post')->where([['special_id','=',$id],['status','<>',9]])->select();
        $check_ids = [];
        foreach($list2 as $k=>$vo){
            $check_ids[] = $vo['post_id'];
        }
        $this->assign("check_ids",$check_ids);

        $post_where[] = ['status','=',1];
        $post_where = array_merge($post_where, $this->common_select_tag);
        $post_list = ZFTB('post')->where($post_where)->limit(100)->order("id desc")->select();
        $this->assign("post_list",$post_list);
        return view();
    }

    public function tag()
    {
        admin_role_check($this->z_role_list,$this->mca);
        $where[] = ['status','<>',9];
        $where = array_merge($where, $this->common_select_tag);
        $group_list = ZFTB('tag')->where($where)->order("id asc")->paginate(10,false,['query' => request()->param()]);
        $page = $group_list->render();
        $this->assign("group_list",$group_list);
        $this->assign("page",$page);
        return view();
    }

    public function tag_add()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isPost()){ 
            $data = input('post.');
            $data['ctime'] = time();
            $data = array_merge($data,$this->common_tag);
            deal_meta_data_add('tag',$data,'id');

        }  
        $res['meta'] = false;
        $this->assign("res",$res);
        #####参数模式
        $form_parm_arr = getFormParams($res);
        $this->assign('form_parm_arr',$form_parm_arr);
        $this->assign('hook_data',['tag','','add',$res]);
        return view();   

         
    }


    public function tag_edit()
    {
        admin_role_check($this->z_role_list,$this->mca,1);   
        if(request()->isPost()){
            // $data = input('post.');
            $data_r = input('post.');
            if(isset($data_r['editor_type']) &&  $data_r['editor_type']=='tinymce'){
                //tinymce 编辑器
                $data= [];
                foreach($data_r['data'] as $k=>$vo){
                    $data[$vo['name']] = $vo['value'];
                }
                $data['content'] = $data_r['content'];
            }else{
                $data = $data_r;
            }
            if(isset($data['ctime']) && $data['ctime']!=''){
                $data['ctime'] =  strtotime($data['ctime']);
            }else{
                $data['ctime'] =  time();
            }
            deal_meta_data_edit('tag',$data,'id');

        } 
        $res =  ZFTB('tag')->where(['id'=>input('id')])->find();
        $meta_json = ZFTB('meta_data')->where([['tb','=','tag'],['post_id','=',$res['id']],['status','<>',9]])->value('meta_data');
        $res['meta'] = json_decode($meta_json,true);
        $this->assign("res",$res);
        #####参数模式
        $form_parm_arr = getFormParams($res);
        $this->assign('form_parm_arr',$form_parm_arr);
        $this->assign('hook_data',['tag','','edit',$res]);
        return view('/category/tag_add');
    }

    /**
     * 导入/导出/列表
     */
    protected function get_child_id($pid = 0, $condition = '1=1') {
        //查询分类信息
        $data = Db::name('category')->field('cid,pid,name')->where($condition)->order("sort asc,cid asc")->select();
        $cat = new cat(array('cid', 'pid', 'name', 'cname')); //初始化无限分类
        $child_array = $cat->getTree($data, $pid);//获取分类数据树结构
        if(is_array($child_array) && !empty($child_array)){
            $child_cid[] = $pid;
            foreach($child_array as $vo){
                $child_cid[] = $vo['cid'];
            }
        }else{
            return $pid;
        }
        return implode(',', $child_cid);//获取所有子分类cid字符串
    }
    public function pro_post_list()
    {
        admin_role_check($this->z_role_list,$this->mca);
        // 栏目id
        $cid = input('cid',0);
        $t = input('t',0);
        $this->assign('t',$t);
        $this->assign("cid",$cid);
        //如果是内容页,加载列表页
        $where_type = input('where_type','');
        $where[] = ['status','<>',9];
        $where[] = ['cid','in',$this->get_child_id($cid)];
        $keyword = input("get.keyword".'');
        if($keyword!=''){
            $where[] = ['title|content|summary','like','%'.$keyword.'%'];
        }
        $list = ZFTB('post')->where($where)->order("sort desc,id desc")->paginate(10,false,['query' => request()->param()]);
        if(!$list){ 
            $list = [];
        }
        $page = $list->render();
        $this->assign("list",$list);
        $this->assign("page",$page);
        $res =  ZFTB('category')->where(['cid'=>$cid])->find();
        $this->assign("res",$res);
        $this->assign("keyword",$keyword);
        return view();
    }

    //pro文章的导入(开发者自行修改)
    public function import_pro(){
        return jserror('自定义部分,开发者修改开发...');
        // 设置PHP默认字符集为UTF-8
        ini_set('default_charset', 'UTF-8');
        // $upload_file = $_FILES['file']['tmp_name'];
        //文件类型
        //$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        $upload_file = $_FILES['file']['tmp_name'];
        //自动获取文件类型 (xlsx,xls,csv)
            $spreadsheet = IOFactory::load($upload_file);
        //直接转为数组,避免上一种方法取值会出现表格内数据格式错误的问题
        $data = $spreadsheet->getSheet(0)->toArray();  
        $num_all= 0;
        $num_success= 0;
        $save_data = [];
        foreach($data as $k=>$vo){
            // dd()
            if($k>0){
                $num_all++;
                $save_data[$k] = [
                    'cid'=>$vo[0],
                    'title'=>mb_convert_encoding(isset_arr_key($vo,1,''), 'UTF-8', 'auto'),
                    'ctime'=>time()
                ];
                //移除首字母C-
                $save_data[$k]['cid'] =  str_replace('C-','',$save_data[$k]['cid']);
                // if(isset_arr_key($vo,2,'')!=''){
                //     // $save_data[$k]['file'] = '/upload/pdf/'.isset_arr_key($vo,2,'');
                //     $save_data[$k]['file'] = isset_arr_key($vo,2,'');
                // }else{
                //     $save_data[$k]['file'] = '';
                // }
               
            }
        }
        $is = db('post')->insertAll($save_data);
        if(!$is){
            return jserror('导入失败');
        }else{
            return jssuccess('导入成功');
        }

    }
    //导出产品(开发者自行修改)
    public function export_pro(){
        if (!is_dir('./vendor/phpoffice/phpspreadsheet')) {
            $this->error('PhpSpreadsheet扩展(phpoffice/phpspreadsheet)未安装,请先安装后使用');
        }
        ini_set('memory_limit', '256M');
        $where[] = ['status','<>',9];
        $where[] = ['cid','in',$this->get_child_id(1)];
        $keyword = input("get.keyword".'');
        if($keyword!=''){
            $where[] = ['title|content|summary','like','%'.$keyword.'%'];
        }
        $list = ZFTB('post')->where($where)->order("sort desc,id desc")->select();
        
        $name='导出'.date("Y-m-d H-i-s",time());
        //设置表头：
        $head = [ 'CID','标题','ID'];
        //数据中对应的字段，用于读取相应数据：
        $keys = ['cid','title','id'];    
        zf_excel_export($head,$keys,$list,$name) ;
        die;
    }
    //编辑导入(开发者自行修改)
    public function import_pro_edit(){
        return jserror('自定义部分,开发者修改开发...');

        $upload_file = $_FILES['file']['tmp_name'];
        //文件类型
        //$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        $upload_file = $_FILES['file']['tmp_name'];
        //自动获取文件类型 (xlsx,xls,csv)
        $spreadsheet = IOFactory::load($upload_file);
        //直接转为数组,避免上一种方法取值会出现表格内数据格式错误的问题
        $data = $spreadsheet->getSheet(0)->toArray();  
        $num_all= 0;
        $num_success= 0;
        $save_data = [];
        // dd($data);
        foreach($data as $k=>$vo){
            // dd()
            if($k>0){
                $num_all++;
                $save_data[$k] = [
                    'cid'=>intval($vo[0]),
                    'title'=>isset_arr_key($vo,1,''),
                    'id'=>isset_arr_key($vo,25,''),
                    // 'ctime'=>time()
                ];
                // dd($save_data[$k]);
                if(isset_arr_key($vo,2,'')!=''){
                    // $save_data[$k]['file'] = '/upload/pdf/'.isset_arr_key($vo,2,'');
                    $save_data[$k]['file'] = isset_arr_key($vo,2,'');
                }
                if($save_data[$k]['id']!=''){
                    $is_update = db('post')->where(['id'=>$save_data[$k]['id']])->update($save_data[$k]);
                    if($is_update){
                        $num_success++;
                    }
                }
               
            }
        }
            return jssuccess('导入成功,修改数量:'.$num_success);

    }



   
    


    
    
    
}


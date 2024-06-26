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
use zf\ZfAuth;
use GuzzleHttp\Client;
use think\Controller;
use lib\OpenAi;

class Zfyun extends Admin
{
    public function __construct (){
        parent::__construct();
        $this->zfyun_init();
        if(extension_loaded("IonCube Loader")) {     
            $this->sqb_error_msg = base64_decode(base64_encode("<div>".base64_decode('VGhlIGNvbW11bml0eSB2ZXJzaW9uIGRvZXMgbm90IHN1cHBvcnQgdGhpcyBmZWF0dXJlLiBDbGljayB0byB1cGdyYWRlIHRvIHRoZSBwcm9mZXNzaW9uYWwgdmVyc2lvbiA=')."<a href='".ZFC('version.api_domain','file').'/yun_down_list?v='.ZFC('version.version','file')."' target='_blank'>".base64_decode('PHNwYW4gc3R5bGU9ImNvbG9yOnJlZDsiPueCueWHu+S4i+i9vVl1bi5waHA8L3NwYW4+')."</a></div>"));
        }else{
            $this->sqb_error_msg = base64_decode(base64_encode("<div>".base64_decode('VGhlIGNvbW11bml0eSB2ZXJzaW9uIGRvZXMgbm90IHN1cHBvcnQgdGhpcyBmZWF0dXJlLiBDbGljayB0byB1cGdyYWRlIHRvIHRoZSBwcm9mZXNzaW9uYWwgdmVyc2lvbiA=')."<a href='".ZFC('version.api_domain','file').'/yun_down_list?v='.ZFC('version.version','file')."' target='_blank'>".base64_decode('PHNwYW4gc3R5bGU9ImNvbG9yOnJlZDsiPueCueWHu+S4i+i9vVl1bi5waHA8L3NwYW4+')."</a>|<a href='".ZFC('version.api_domain','file')."/question_list.html#ioncube_install' target='_blank'>".base64_decode('PHNwYW4gc3R5bGU9ImNvbG9yOnJlZDsiPklvbmN1YmXmianlsZXmnKrlronoo4Us54K55Ye75p+l55yL5a6J6KOF5pWZ56iLPC9zcGFuPg==')."</a></div>"));
        }
        if(is_file('extend/zf/Yun.php')){
            $this->is_professional_edition = true;
        }else{
            $this->is_professional_edition = false;
        }
        $this->assign('is_professional_edition',$this->is_professional_edition);

    }
    public function store(){
        admin_role_check($this->z_role_list,$this->mca);
        if(is_file('./addons/zfcms_plugin_store/controller/Zfplu.php')){
            $this->redirect('/addons/zfcms_plugin_store.zfplu/index');
        }else{
            echo str_show_tpl('Please use the plugin version upgrade tool<a target="_blank" href="'.config('version.api_domain').'/addons/zf_store_softclientv2.api/cmsurl?t=zfcms_plugin_store">Click</a>');die;
        }
    }
    public function themes(){
        admin_role_check($this->z_role_list,$this->mca);
        if($this->is_professional_edition){
            $this->zfyun_themes();
        }else{
            $z_module=input('z_module','index');
            $start_dir = './theme';  
            $dirs = [];
            if (is_dir($start_dir)) {    
                $fh = opendir($start_dir);    
                while (($file = readdir($fh)) !== false) {    
                if (strcmp($file, '.')==0 || strcmp($file, '..')==0) continue;    
                $filepath = $start_dir . '/' . $file;    
                array_push($dirs, $filepath);    
                }    
                closedir($fh);    
            } 
            $list = [];
            foreach ($dirs as $k => $vo) {
                try {
                    $_plugin_info = $vo.'/plugin_info.php';
                    if(file_exists($_plugin_info)){
                        $_plugin_data = include $_plugin_info;
                        // $list[$k]['data'] = 
                        $list[$k]['name'] = isset_arr_key($_plugin_data,'name','');
                        $list[$k]['plugin_name'] = isset_arr_key($_plugin_data,'plugin_name','');
                        $list[$k]['soft_id'] = isset_arr_key($_plugin_data,'soft_id','');
                        $list[$k]['pic'] = isset_arr_key($_plugin_data,'pic','');
                        $list[$k]['ok'] = 1;
                        $is_version = db('plugin')->where(['plugin_name'=>$list[$k]['plugin_name']])->value('version');
                        if($is_version){
                            $list[$k]['version'] =$is_version;
                        }else{
                            $list[$k]['version'] =$list[$k]['data']['version'];
                        }
                        $list[$k]['path'] = $vo; 
                    }
                } catch (\Exception $e) {
                    $r = $e->getMessage();
                }
            }
            
            $this->assign('list',$list);
            $this->assign('page',false);
            //查询当前的模板
            $this->assign('tpl_name',ZFC('zf_tpl_suffix'));
            // client_config
            $client_config['site'] = request()->host();
            if(isHTTPS()){
                $client_config['http'] = 'https';
            }else{
                $client_config['http'] = 'http';
            }
            $client_config['token'] = config()["zf_auth"]['site_token'];
            $this->assign('client_config',$client_config);

        }
        return view();
    }
    public function plugins(){
        admin_role_check($this->z_role_list,$this->mca);
        if(!$this->is_professional_edition){
            echo str_show_tpl($this->sqb_error_msg); die;
        }
        $this->zfyun_plugins();
        return view();
    }
    public function themes_upload(){
        admin_role_check($this->z_role_list,$this->mca);
        $this->zfyun_themes_upload();
    }
    public function plugin_upload(){
        admin_role_check($this->z_role_list,$this->mca);
        $this->zfyun_plugin_upload();
    }
    public function plugin_uninstall(){
        admin_role_check($this->z_role_list,$this->mca);
      $this->zfyun_plugin_uninstall();
    }
    public function themes_uninstall(){
        admin_role_check($this->z_role_list,$this->mca);
        $this->zfyun_themes_uninstall();
    }
    public function plugin_backup(){
        admin_role_check($this->z_role_list,$this->mca);
        $this->zfyun_plugin_backup();
    }
    public function theme_backup(){
        admin_role_check($this->z_role_list,$this->mca);
        $this->zfyun_theme_backup();
    }
   
    public function plugin_act(){
        admin_role_check($this->z_role_list,$this->mca);
        $this->zfyun_plugin_act();
    }
    public function upgrade(){
        admin_role_check($this->z_role_list,$this->mca);
        if(is_file('./addons/zfcms_plugin_store/controller/Zfcms.php')){
            $this->redirect('/addons/zfcms_plugin_store.zfcms/index');
        }else{
            echo str_show_tpl('Please use the plugin version upgrade tool<a target="_blank" href="'.config('version.api_domain').'/addons/zf_store_softclientv2.api/cmsurl?t=zfcms_plugin_store">Click</a>');die;
        }
    }
    /**
     * 用于插件->更新插件授权
     */
    public function update_sq(){
        admin_role_check($this->z_role_list,$this->mca);
        if(!$this->is_professional_edition){
            echo str_show_tpl($this->sqb_error_msg); die;
        }else{
            $t = input('t','');
            $_SESSION['_zf_temp_remote'] = '1';
            $this->zfauth = new ZfAuth(2);
            if($t==1){
                $this->zfauth->plugin_check('@update_sq@','alert');
                return jssuccess('更新授权成功');
            }elseif($t==2){
                //更新插件sql
                $this->zfauth->_update_plugind_db();  
                return jssuccess('更新插件Sql成功');
            }elseif($t==3){
                //更新模板
                $this->zfauth->_update_theme_db();
                return jssuccess('更新模板Sql成功');
            }elseif($t==4){
                //更新插件/模板钩子
                $this->zfauth->_update_plugind_hook(); 
                return jssuccess('更新插件/模板钩子成功');
            }elseif($t==5){
                //更新数据库存在,但是文件不存在的插件
                $this->update_plugind_file();
                return jssuccess('更新数据库/文件统一性成功');
            }else{
                return view();
            }
        }
       
    }
    /**
     * 更新数据库存在,但是文件不存在的插件
     */
    private function update_plugind_file(){
        $db_plugin = db('plugin')->field('plugin_name,status')->where([['type','in','plugin,theme'],['status','in','1,2']])->group('plugin_name')->select();
        if($db_plugin){
            foreach ($db_plugin as $k => $vo) {
                $db_plugin[$k] = $vo['plugin_name'];
            }
        }else{
            $db_plugin = [];
        }
        $dir_arr = scandir('./theme');
        foreach ($dir_arr as $k => $vo) {
            if($vo!='.' && $vo!='..' && $vo!='.DS_Store'  && is_dir('./theme/'.$vo) && is_file('./theme/'.$vo.'/plugin_info.php')){
                if(in_array($vo,$db_plugin)){
                    unset($db_plugin[array_search($vo,$db_plugin)]);
                }
            }
        }
        $dir_arr = scandir('./addons');
        foreach ($dir_arr as $k => $vo) {
            if($vo!='.' && $vo!='..' && $vo!='.DS_Store' && is_dir('./addons/'.$vo) && is_dir('./addons/'.$vo.'/config') && is_file('./addons/'.$vo.'/config/plugin_info.php')){
                if(in_array($vo,$db_plugin)){
                    unset($db_plugin[array_search($vo,$db_plugin)]);
                }
            }
        } 
        $is = db('plugin')->where([['plugin_name','in',$db_plugin]])->update(['status'=>3]);
    }
    public function upgrade_sql(){
        admin_role_check($this->z_role_list,$this->mca);
        if(!$this->is_professional_edition){
            echo str_show_tpl($this->sqb_error_msg); die;
        }else{
            session('v_upgsql_act',1); //其他的写法的
            $update = new \app\common\controller\Updatesql();
            $update_sql_arr = $update->update('v0');
            $this->handle_sql_version($update_sql_arr,'site');
            $zfcommon = new \zf\ZfCommon();
            $zfcommon->handle_update_db_zdxg();
            $this->success('更新Sql成功');
        }
    }
    
    
    //hook
    public function hook()
    {
        admin_role_check($this->z_role_list,$this->mca);
        $this->zfyun_hook();
        $list = ZFTB('hook')->where([['status','<>',9]])->order("sort desc,id desc")->paginate(10,false,['query' => request()->param()]);
        $page = $list->render();
        $this->assign("list",$list);
        $this->assign("page",$page);
        return view();
    }
    public function hook_add()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(!request()->isPost()){
            return view();
        }  
        $data = input("post.");
        if($data['name']==''){
            return jserror('请填写信息');exit;
        }
        $data['ctime'] = time();
        $data['token'] = time();
        if(isset($data['zf_list_position'])){
            $data['position'] = implode(',',$data['zf_list_position']);
        }
        unset($data['zf_list_position']);
        $data = array_merge($data,$this->common_tag);
        $res = ZFTB('hook')->insert($data);
        return ZFRetMsg($res,'新增成功','新增失败');
    }

    
    public function hook_edit()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isGet()){
            $res =  ZFTB('hook')->where(['id'=>input('id')])->find(); 
            $this->assign("res",$res);
            return view('zfyun/hook_add');
        } 
        if(request()->isPost()){
           $data = input('post.');
            if(isset($data['zf_list_position'])){
                $data['position'] = implode(',',$data['zf_list_position']);
            }else{
                $data['position'] = '';
            }
            unset($data['zf_list_position']);
            $data['token'] = time();
            $res = ZFTB('hook')->where(['id'=>$data['id']])->update($data);
            return ZFRetMsg($res,'修改成功','修改失败');
        } 
    }
    public function task()
    {
        admin_role_check($this->z_role_list,$this->mca);
        $list = ZFTB('task')->where([['status','<>',9]])->order("sort desc,id desc")->paginate(10,false,['query' => request()->param()]);
        $page = $list->render();
        $this->assign("list",$list);
        $this->assign("page",$page);
            
        $start_time = file_get_contents('./public/task_lock.txt');
        $this->assign("start_time",$start_time);
        //当前运行状态
        if(is_file('./runtime/Task/Log/'.date("Y_m_d").'.log')){
            // 获取文件最后一行
            $rs = './runtime/Task/Log/'.date("Y_m_d").'.log';
            $fp = fopen($rs, 'r');
            fseek($fp,-1,SEEK_END);
            $task_str = '';
            while(($c = fgetc($fp)) !== false){
                if($c == "\n" && $task_str) break;
                $task_str = $c . $task_str;
                fseek($fp, -2, SEEK_CUR);
            }
            fclose($fp);
            if(strpos($task_str,'listened status command, this manager is reported') !== false){ 
                //开启
                $task_status = '定时任务运行中';
            }elseif(strpos($task_str,'listened exit command, this manager is exiting unsafely') !== false){ 
                // 关闭
                $task_status = '定时任务已关闭';
            }else{
                $task_status = '定时任务异常';
            }
        }else{
            $task_status = '定时任务未开启';
        }
        $this->assign("task_status",$task_status);

        return view();
    }
    public function task_add()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(!request()->isPost()){
            return view();
        }  
        $data = input("post.");
        if($data['name']==''){
            return jserror('请填写信息');exit;
        }
        $data['ctime'] = time();
        $data['stime'] = strtotime($data['stime']);
        $data['etime'] = strtotime($data['etime']);
        $data['token'] = time();
        if(isset($data['zf_list_hours'])){
            $data['hours'] = implode(',',$data['zf_list_hours']);
            unset($data['zf_list_hours']);
        }else{
            $data['hours'] = '';
        }
        $res = ZFTB('task')->insert($data);
        return ZFRetMsg($res,'新增成功','新增失败');
        
    }
    public function task_edit()
    {
        admin_role_check($this->z_role_list,$this->mca,1);
        if(request()->isGet()){
            $res =  ZFTB('task')->where(['id'=>input('id')])->find(); 
            $this->assign("res",$res);
            return view('zfyun/task_add');
        } 
        if(request()->isPost()){
           $data = input('post.');
            $data['stime'] = strtotime($data['stime']);
            $data['etime'] = strtotime($data['etime']);
            $data['token'] = time();
            $data['utime'] = time();
            if(isset($data['zf_list_hours'])){
                $data['hours'] = implode(',',$data['zf_list_hours']);
                unset($data['zf_list_hours']);
            }else{
                $data['hours'] = '';
            }
            $res = ZFTB('task')->where(['id'=>$data['id']])->update($data);
            return ZFRetMsg($res,'修改成功','修改失败');
        } 
    }
    public function task_log()
    {
        admin_role_check($this->z_role_list,$this->mca);
        $task_id = input('task_id','');
        $list = ZFTB('task_log')->where([['status','<>',9],['task_id','=',$task_id]])->order("id desc")->paginate(10,false,['query' => request()->param()]);
        $page = $list->render();
        $this->assign("list",$list);
        $this->assign("page",$page);
        return view();
    }
    public function jump_theme_config(){
        admin_role_check($this->z_role_list,$this->mca);
        $theme = input('theme','');
        if($theme!=ZFC('zf_tpl_suffix')){
            $this->error('请先设置为站点模板');
        }else{
            $this->redirect('/i_admin');
        }
    }
    public function task_active(){
        $t = input('t',''); 
        if($t = 'start'){
            $shell = 'php think task start ';
        }elseif ($t=='stop') {
            $shell = 'php think task stop ';
        }elseif ($t=='status') {
            $shell = 'php think task status ';
        }else{
            $shell = 'ls ';
            
            // dd('err');
        }

        //方法是否禁用
        if(!function_exists('shell_exec')){
            return jserror('shell_exec方法被禁用,请开启');
            //不需要重启
        }
        if(!function_exists('proc_open')){
            return jserror('proc_open方法被禁用,请开启');
            //不需要重启
        }
        //获取当前觉得目录
        $dir = dirname(dirname(dirname(dirname(__FILE__))));

        // dd($dir);
        echo "开发中";die;

        // $output = shell_exec($shell);
        // $output = shell_exec($shell." > /dev/null 2>&1 &");
        // dd($output);
        $descriptorspec = array(
            0 => array("pipe", "r"),   // 标准输入管道，写入到子进程的stdin中
            1 => array("pipe", "w"),   // 标准输出管道，从子进程的stdout中读取
            2 => array("pipe", "w")    // 标准错误输出管道，从子进程的stderr中读取
        );
        $process = proc_open($shell, $descriptorspec, $pipes);
        if (is_resource($process)) {
            // 向子进程写入数据
            fwrite($pipes[0], "input data");
            // 关闭标准输入管道
            fclose($pipes[0]);
            // 读取子进程的输出
            $output = stream_get_contents($pipes[1]);
            // 关闭标准输出管道
            fclose($pipes[1]);
            // 读取子进程的错误输出
            $error = stream_get_contents($pipes[2]);
            // 关闭标准错误输出管道
            fclose($pipes[2]);
            // 等待子进程结束并获取退出码
            $exit_code = proc_close($process);
        }
        dd($output);

        

    }


   
    public function down_load(){
        if(!$this->is_professional_edition){
            return jserror('不支持此版本');
        }
        $t = input('t','init');
        if($t=='init'){
            $uname = input('uname','');
            $this->assign('uname',$uname);
            // dd($zfcms_down_data['msg']);
            return view();
        }
        $this->zf_down_load();
	}

   
    /**
     * 20231109新增
     * ai写内容
     */
    public function ai_write(){
        if(!$this->is_professional_edition){
            return ZFRetMsg(false,'','yun.php文件不存在,不支持此功能');
        }
        $t = input('ai_w_type','');
        $data = input('post.');
        if($t=='tdk'){
            $sys_message = '通过下面提供的内容写网页的标题、关键词、描述,让搜索引擎更好的收录你的网站,给出的内容使用@@@间隔,例如: 标题@@@关键词@@@描述';
            $old_content = html_out_par($data['content'],1000);
            $_data = $this->zfyun_openai($old_content,$sys_message);
            if($_data['code']==0){
                return ZFRetMsg(false,'',$_data['msg']);
            }
            $_ret_data_arr = explode("@@@",$_data['msg']);
            if(isset($_ret_data_arr[2])){
                $ret_data['title'] = $_ret_data_arr[0];
                $ret_data['keywords'] = $_ret_data_arr[1];
                $ret_data['desc'] = $_ret_data_arr[2];
                echo json_encode(array("msg" => '获取成功,请查看是否匹配', "data" => $ret_data, "result" => '1'));exit; 
            }else{
                return ZFRetMsg(false,'','获取数据错误,请查看提示词是否正确或稍后再试:内容如下,你可以复制到对应位置:<br>'.$_data['msg']);
            }
        }elseif($t=='xuxie_content'){
            $sys_message = '通过下面提供的标题续写一段内容,让搜索引擎更好的收录你的网站,并对文章进行润色';
            if(isset($data['title'])){
                $old_content = html_out_par($data['title'],1000);
            }elseif(isset($data['name'])){
                $old_content = html_out_par($data['name'],1000);
            }else{
                return ZFRetMsg(false,'','页面input不存在name或title');
            }
            $_data = $this->zfyun_openai($old_content,$sys_message);
            if($_data['code']==0){
                return ZFRetMsg(false,'',$_data['msg']);
            }
            $ret_data['content'] = $_data['msg'];
            echo json_encode(array("msg" => '获取成功,请查看是否匹配', "data" => $ret_data, "result" => '1'));exit; 
            
        }else{
            return jserror('该类型不存在');
        }
    }

    
}



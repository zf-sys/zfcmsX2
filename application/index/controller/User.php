<?php
namespace app\index\controller;
use think\Db;
use think\facade\Request;
use Wmc1125\TpFast\GetConfig;
use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;
use Wmc1125\TpFast\Category as cat;



class User extends Base
{

	public function __construct ( Request $request = null ){
        parent::__construct();

        $action = strtolower(request()->action());
        if($action!='wx_notify'){
            if(session('home')==null){
                $this->redirect('/login',302);
            }
            $this->user = Db::name('user')->where(['id'=>session('home')['id']])->find();
            session('home',$this->user);
            if($this->user['name']=='' && $this->action!='set'){
                $this->redirect('/user/set',302);
            }
        }
        $seo['title'] = config()['web']['site_title'];
        $seo['keywords'] = config()['web']['site_keywords'];
        $seo['description'] = config()['web']['site_description'];
        $this->assign('seo', $seo);

        
        
    }
    // 首页
    public function index()
    {
        //我发表的帖子
        $list_me = db('post')->where([['status','<>',9],['author','=',$this->user['id']]])->order('ctime desc')->paginate(10,false, ['query' => request()->param()]);     
        $list_me_num = db('post')->where([['status','<>',9],['author','=',$this->user['id']]])->order('ctime desc')->count();     
        $this->assign('list_me', $list_me);
        $page = $list_me->render();
        $this->assign('page', $page);
        $this->assign('list_me_num', $list_me_num);
        $this->assign('two_cur', 'index');
        //获取最近登陆时间
        // $data['end_login'] =Db::name('login_log')->where(['uid'=>$this->user['id']])->order('id desc')->value('ctime'); 
        $data['user'] = $this->user;
        $this->assign('data', $data);
        return view($this->tpl);
    }
    public function home(){
        $this->assign('user',$this->user);
        $id = input('id','');
        if($id==''){
            $res = $this->user;
            $list_tz = db('post')->where([['status','=',1],['author','=',$this->user['id']]])->order('ctime desc')->paginate(10,false, ['query' => request()->param()]);     

        }else{
            $res = Db::name('user')->where(['id'=>$id])->find();
            $list_tz = db('post')->where([['status','=',1],['author','=',$id]])->order('ctime desc')->paginate(10,false, ['query' => request()->param()]);     

        }
        $this->assign('res',$res);
        $this->assign('list_tz', $list_tz);

        return view($this->tpl);
    }
    public function welcome(){
        $this->assign('user',$this->user);
        return view($this->tpl);
    }
    public function userinfo(){
        if(request()->isAjax()){
            $data = input('post.');
            //验证数据
            if($data['hospital_email']!=''){
                \Wmc1125\TpFast\ZfTool::check_data('hospital_email',$data['hospital_email'],'邮箱格式不正确');
            }
            //查询是否已存在
            $res = Db::name('user')->where(['id'=>session('home')['id']])->update($data);
            
            if($res){
                $u_data = Db::name('user')->where(['id'=>session('home')['id']])->find();
                session('home',$u_data);
                $msg['msg'] = '修改成功';
                return jssuccess($msg);
            }else{
                $msg['msg'] = '修改失败';
                return jserror($msg);
            }
        }
        //查询用户信息
        $user = Db::name('user')->where(['id'=>session('home')['id']])->find();
        $this->assign('data',$user);
        $this->assign('two_cur', 'userinfo');
        return view($this->tpl);
    }

    public function set(){
        if(request()->isPost()){
            $data = input('post.');
            if($this->user['name']!=''){
                if($data['name']!=''){
                    return jserror('昵称禁止修改'); 
                }
            }else{
                if($data['name']=='') return jserror('昵称不能为空'); 
                if(strpos($data['name'],' ') !== false) return jserror('昵称不能含有空格'); 
                if(strpos($data['name'],'@') !== false) return jserror('昵称不能含有@'); 

                //检测昵称
                $is = db('user')->where(['name'=>$data['name']])->find();
                if($is) return jserror('昵称已存在,请换一个'); 
            }
            //过滤违禁词
            $wjc = explode(',',config('web.key_filter'));
            $data['sign'] = str_replace($wjc,'*',$data['sign']);
            $data['utime'] = time();
            $data['ip'] = request()->ip();
            $data['token'] = time();
            $is = db('user')->where([['id','=',$this->user['id']]])->update($data);
            if($is){
                return jssuccess('修改成功'); 
            }else{
                return jserror('修改失败'); 
            }   
        }
        $this->assign('user',$this->user);
        return view($this->tpl);
    }
    public function message(){
        $list = db('user_msg')->where([['status','<>',9],['uid','=',$this->user['id']]])->order('ctime desc')->paginate(10,false, ['query' => request()->param()]);     
        $this->assign('list', $list);
        $page = $list->render();
        $this->assign('page', $page);

        $this->assign('user',$this->user);
        return view($this->tpl);
    }
    public function write(){
        if(request()->isPost()){
            $data = input('post.');
            $data['author'] = $this->user['id'];
            if($data['title']=='') return jserror('标题不能为空'); 
            if($data['content']=='') return jserror('内容不能为空'); 

            //过滤违禁词
            $wjc = explode(',',config('web.key_filter'));
            $data['content'] = str_replace($wjc,'*',$data['content']);
            $data['title'] = str_replace($wjc,'*',$data['title']);
            if($data['id']==''){
                $is = db('post')->where($data)->find();
                if($is) return jserror('请勿重复提交'); 
                $data['ip'] = request()->ip();
                $data['ctime'] = time();
                $is = db('post')->insert($data);
                if($is){
                   return jssuccess('提交成功'); 
                }else{
                   return jserror('提交失败'); 
                }
            }else{
                $res = db('post')->where([['id','=',$data['id']],['author','=',$this->user['id']]])->find();
                if(!$res) $this->error('非法操作');
                $data['utime'] = time();
                $data['token'] = time();
                $is = db('post')->where([['id','=',$data['id']],['author','=',$this->user['id']]])->update($data);
                if($is){
                   return jssuccess('修改成功'); 
                }else{
                   return jserror('修改失败'); 
                }
            }
        }
        $id = input('id','');
        $t = input('t','');
        if($id!=''){
            //编辑
            $res = db('post')->where([['id','=',$id],['status','<>',9],['author','=',$this->user['id']]])->find();
            if(!$res) $this->error('非法访问');
            $this->assign('res',$res);
        }else{
            //判断发文数
            $fw_num = db('post')->whereTime('ctime', 'today')->where([['author','=',$this->user['id']]])->count();
            if($fw_num>=10){
                $this->error('每天限制发文10篇');
            }
        }
        $this->assign('user',$this->user);
        $type = explode(',',model_val(42));
        $this->assign('type',$type);
        if($t==''){
            return view($this->tpl);
        }else{
            return view($this->tpl.'_'.$t);
        }
    }
    public function bbs_reply(){
        if(request()->isPost()){
            $data = input('post.');
            $data['uid'] = $this->user['id'];
            $data['zan'] = 0;
            $data['ip'] = request()->ip();
            if($data['content']==''){
               return jserror('内容不能为空'); 
            }
            //获取@的人
            if(strpos($data['content'],'@') !== false){ 
                // echo '包含jb51'; 
                $user_rep = explode(' ',$data['content']);
                foreach($user_rep as $k=>$vo){
                    if(strpos($vo,'@') !== false){ 
                        // 给用户发消息
                        $url = '/bbs_detail/'.$data['post_id'].'.html';
                        bbs_send_msg(substr($vo, 1),"<a href='".$url."'>".$url."</a>");
                    }
                }
            }
            $wjc = explode(',',config('web.key_filter'));
            $data['content'] = str_replace($wjc,'*',$data['content']);
            $is = db('post_reply')->where($data)->find();
            if($is){
               return jserror('请勿重复评论'); 
            }
            $data['ctime'] = time();
            $is = db('post_reply')->insert($data);
            if($is){
               return jssuccess('评论成功'); 
            }else{
               return jserror('评论失败'); 
            }
        }
    }
    
    public function repwd(){
        if(request()->isAjax()){
            $data = input('post.');
            //验证数据
            $is_u = Db::name('user')->where(['id'=>session('home')['id']])->find();
            if ($is_u['pwd']!= md5($data['old_pwd'])) {
                return jserror('原密码无效');
            }
            if($is_u['pwd']==md5($data['pwd'])){ return jserror('密码不能为原密码'); }
            if($data['pwd']=='' || $data['pwd']!=$data['repwd']){
                return jserror('两次密码不相同');
            }
            unset($data['old_pwd']);
            unset($data['repwd']);
            $data['pwd'] = md5($data['pwd']);
            //查询是否已存在
            $res = Db::name('user')->where(['id'=>session('home')['id']])->update($data);
            if($res){
                session('home',null);
                $msg['msg'] = '修改成功';
                return jssuccess($msg);
            }else{
                $msg['msg'] = '修改失败';
                return jserror($msg);
            }
        }
        //查询用户信息
        $user = Db::name('user')->where(['id'=>session('home')['id']])->find();
        $this->assign('data',$user);
        $this->assign('two_cur', 'repwd');
        return view($this->tpl);
    }
    

    
     /**
     * 获取顶级域名
     * @param $url
     * @return string
     */
    public static function getDoMain($url){
        if(empty($url)){
            return '';
        }
        if(strpos($url,'http://') !== false){
            $url = str_replace('http://','',$url);
        }
        if(strpos($url,'https://') !== false){
            $url = str_replace('https://','',$url);
        }
        $n = 0;
        for($i = 1;$i <= 3;$i++) {
            $n = strpos($url, '/', $n);
            $i != 3 && $n++;
        }
 
        $nn = strpos($url, '?');
        $mix_num =  min($n,$nn);
        if($mix_num > 0 || !empty($mix_num)){
            //防止链接带有点 （.） 导致出错
            $url = mb_substr($url,0,$mix_num);
        }
        $data = explode('.', $url);
 
        $co_ta = count($data);
        //判断是否是双后缀
        $no_tow = true;
        $host_cn = 'com.cn,net.cn,org.cn,gov.cn';
        $host_cn = explode(',', $host_cn);
        foreach($host_cn as $val){
            if(strpos($url,$val)){
                $no_tow = false;
            }
        }
        //截取域名后的目录
        $del = strpos($data[$co_ta-1], '/');
        if($del > 0 || !empty($del)){
            $data[$co_ta-1] = mb_substr($data[$co_ta-1],0,$del);
        }
        //如果是返回FALSE ，如果不是返回true
        if($no_tow == true){
            $host = $data[$co_ta-2].'.'.$data[$co_ta-1];
        }else{
            $host = $data[$co_ta-3].'.'.$data[$co_ta-2].'.'.$data[$co_ta-1];
        }
 
        return $host;
    }


}

function bbs_send_msg($name,$url){
    $res = db('user')->where(['name'=>$name])->find();
    if($res){
        db('user_msg')->insert(['title'=>'有新的回复消息','content'=>'回复啦,点击链接查看'.$url,'uid'=>$res['id'],'ctime'=>time()]);
    }
    // echo $res['id'];
}
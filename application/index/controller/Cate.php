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
namespace app\index\controller;
use think\Db;
use think\facade\Request;
use Wmc1125\TpFast\Category as cat;
class Cate extends Base
{
	public function __construct ( Request $request = null ){
        parent::__construct();
    }
    // 首页
    public function index()
    {
        echo 'index';die;

    }
    //列表
    public function list()
    {
        $cid = input('cid',1);
        $this->assign('cid',$cid);
        $top_cid_now = $this->get_top_category($cid);
        $this->assign('top_cid_now',$top_cid_now);
        $cate_res = Db::name('category')->where(['status'=>1,'cid'=>$cid])->find();
        if($cate_res['tpl_category']==''){ $cate_res['tpl_category'] = 'tpl_list';}
        $where[] = ['p.status','=',1];
        $where[] = ['c.status','=',1];
        $where[] = ['c.ctime','<',time()];
        $where[] =['p.lang','=',$this->lang];
        if($cid==$top_cid_now['cid']){
            $where[] = ['p.cid','in',$this->get_child_id($cid)];
        }else{
            $where[] = ['p.cid','=',$cid];
        }
        $where[] = ['p.ctime','<',time()];
        $list = Db::name('post p')
            ->field('p.*,c.name as c_name')
            ->where($where)
            ->join('category'.' c','c.cid=p.cid')
            ->order('p.sort desc,p.ctime desc,p.id desc')
            ->paginate($cate_res['page'],false,['query' => request()->param()]);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign("page",$page);
        $this->assign('list',$list);
        $this->assign('cid',$cid);
        if($cate_res['url']!=''){
            return redirect($cate_res['url']);
        }
        $this->assign('cate_res',$cate_res);
        //当前位置{{面包屑导航}}
        $currentPath = '<a href="/" class="zfcss_mbx">首页</a> ' . $this->get_path($cid);
        $this->assign('currentPath', $currentPath);
        $seo['title'] = ($cate_res['seo_t']==''?$cate_res['name'].'-'.config()['web']['site_name']:$cate_res['seo_t']);
        $seo['description'] = ($cate_res['seo_d']==''?config()['web']['site_description']:$cate_res['seo_d']);
        $seo['keywords'] = ($cate_res['seo_k']==''?config()['web']['site_keywords']:$cate_res['seo_k']);
        $this->assign('seo', $seo);
        doZfAction('home_tdk',['type'=>$this->action,'data'=>$cate_res,'request'=>request()]);
        $this->assign('act', 'list');
        $tpl = $this->zf_tpl_suffix.($this->lang==''?'':'/'.$this->lang).'/cate/'.$cate_res['tpl_category'];
        return view($tpl); 
    }
    // detail
    public function detail()
    {
        $id = input('id',0);
        $this->id = $id;
        $t = input('t','');
        if($t=='show_admin'){
            if(!session('admin')){
                $this->error('错误的请求',$this->site_path);
            }
        }else{
            $where[] = ['status','=',1];
            $where[] = ['ctime','<',time()];
        }
        $where[] = ['id','=',$id];
        $where[] =['lang','=',$this->lang];
        
        $content = Db::name('post')->where($where)->find();
        if(!$content){
            $this->error('未找到相关内容',$this->site_path);
        }
        $content['content'] = $this->_seo($content['content']);
        $this->assign('content',$content);
        $cid = $content['cid'];
        $this->assign('cid',$cid);
        $top_cid_now = $this->get_top_category($content['cid']);
        $this->assign('top_cid_now',$top_cid_now);

        $cate_res = Db::name('category')->field('cid,name,tpl_category,tpl_post,content,alt_banner,banner,summary')->where(['status'=>1,'cid'=>$content['cid']])->find();
        if($cate_res['tpl_post']=='')  $cate_res['tpl_post'] = 'tpl_detail';
        if(!$cate_res) $this->error('栏目已关闭'); 
        $this->assign('cate_res',$cate_res);
        //增加浏览次数
        Db::name('post')->where(["id"=>$id])->setInc('hits');
        $prev = Db::name('post')->where([['lang','=',$this->lang],['status','=',1],['ctime','<',time()],['cid','=',$content['cid']],['id','>',$id]])->order('id asc')->find();
        $next = Db::name('post')->where([['lang','=',$this->lang],['status','=',1],['ctime','<',time()],['cid','=',$content['cid']],['id','<',$id]])->order('id desc')->find();
        $this->assign('prev', empty($prev) ? '<a class="zfcss_prev preview" href="#">【上一篇】：没有了</a>' : ' <a class="zfcss_prev preview" href="' .$this->site_path.'detail/'.$prev['id']. '.html"  >【上一篇】：' . $prev['title'] . '</a>');
        $this->assign('next', empty($next) ? '<a class="zfcss_next next" href="#">【下一篇】：没有了</a>' : '<a class="zfcss_next next" href="' .$this->site_path.'detail/'.$next['id'].'.html"  >【下一篇】：' . $next['title'] . '</a>');


        $relevant = Db::name('post')->where([['lang','=',$this->lang],['status','=',1],['cid','=',$content['cid']],['ctime','<',time()]])->order('hits desc,ctime asc')->limit(10)->select();
        $this->assign('relevant',$relevant);

        //当前位置{{面包屑导航}}
        $currentPath = '<a href="/">首页</a> ' . $this->get_path($cid);
        $this->assign('currentPath', $currentPath);

        //相关专题
        $special = Db::name("special s")
                ->field('s.id,s.name')
                ->where([['s.lang','=',$this->lang],['sp.post_id','=',$content['id']]])
                ->join("special_post sp",'s.id=sp.special_id')
                ->group('s.id')
                ->select();
        $this->assign('special',$special);

        //推荐
        if($content['main_keys_m']!=''){
            $_main_keys_m_arr = explode(',', $content['main_keys_m']) ;
            foreach ($_main_keys_m_arr as $k => $vo) {
                $where_key_a[] = ['title|content','like','%'.$vo.'%'];
            }
            $where_key_a[] = ['status','=','1'];
            $where_key_a[] =['lang','=',$this->lang];
            $tui_arr['key_m_arr'] = Db::name('post')->where($where_key_a)->order('ctime desc,id desc')->limit(10)->select();
        }else{
            $tui_arr['key_m_arr'] = [];
        }
        if($content['main_keys_c']!=''){
            $_main_keys_c_arr = explode(',', $content['main_keys_c']) ;
            foreach ($_main_keys_c_arr as $k => $vo) {
                $where_key_c[] = ['title|content','like','%'.$vo.'%'];
            }
            $where_key_c[] = ['status','=','1'];
            $where_key_c[] =['lang','=',$this->lang];

            $tui_arr['key_c_arr'] = Db::name('post')->where($where_key_c)->order('ctime desc,id desc')->limit(10)->select();
        }else{
            $tui_arr['key_c_arr'] = [];
        }
        $this->assign('tui_arr', $tui_arr);
        
        $seo['title'] = ($content['seo_t']==''?$content['title'].'-'.config()['web']['site_name']:$content['seo_t']);
        $seo['description'] = ($content['seo_d']==''?config()['web']['site_description']:$content['seo_d']);
        $seo['keywords'] = ($content['seo_k']==''?config()['web']['site_keywords']:$content['seo_k']);
        $this->assign('seo', $seo);
        doZfAction('home_tdk',['type'=>$this->action,'data'=>$content,'request'=>request()]);
        $tpl =$this->zf_tpl_suffix.($this->lang==''?'':'/'.$this->lang).'/cate/'.$cate_res['tpl_post'];
        return view($tpl); 
    }
    //search
    public function search()
    {
        $keyword = input('keyword','');
        $t = input('t','');
        if($t!=''){
            $this->redirect($this->site_path.'search/'.$keyword.'.html');
        }
        $where[] = ['p.title','like','%'.$keyword.'%'];
        $where[] = ['p.status','=','1'];
        $where[] = ['p.ctime','<',time()];
        $where[] =['p.lang','=',$this->lang];
        $list = Db::name('post p')
        ->field('p.*,c.name as c_name')
        ->where($where)
        ->join('category'.' c','c.cid=p.cid')
        ->order('p.sort desc,p.ctime desc,p.id desc')->paginate(15);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);

        $this->assign('cid','search');
        $this->assign('keyword',$keyword);
        $this->assign('act', 'list');

        $seo['title'] = $keyword . ' - ' . config()['web']['site_name'];
        $seo['description'] = config()['web']['site_description'];
        $seo['keywords'] =   $keyword ;
        $this->assign('seo', $seo);
        doZfAction('home_tdk',['type'=>$this->action,'data'=>$keyword,'request'=>request()]);
        $tpl = $this->zf_tpl_suffix.($this->lang==''?'':'/'.$this->lang).'/cate/search';
        return view($tpl); 
    }
    public function liuyan(){
        if(request()->isPost()){
            $data = input('post.');
            // if(!check_email($data['email'])){
            //     return jserror('邮箱错误');
            // }
            if($data['content']==''){
                return jserror('内容不能为空');
            }
            //判断是否已提交
            if(Db::name('guessbook')->where(['tel'=>$data['tel'],'content'=>$data['content']])->count() >0){
                return jserror('请勿重复提交');
            }
            $data['ctime'] = time();
            $data = array_merge($data,$this->common_tag);
            $res = Db::name('guessbook')->insert($data);
            if($res){            
                return jssuccess('提交成功');
            }else{
                return jserror('提交失败');
            }
        }
        $tpl = $this->zf_tpl_suffix.($this->lang==''?'':'/'.$this->lang).'/cate/liuyan';
        return view($tpl); 
    }
    public function tag($tag=''){
        $where = [['ctime','<',time()],['status','=',1],['title|content|summary','like','%'.$tag.'%']];
        $where = array_merge($where, $this->common_select_tag);
        $list = db('post')->where($where)->order('sort desc,ctime desc,id desc')->paginate(10);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('tag', $tag);
        
        $seo['title'] = $tag.'-'.config()['web']['site_name'];
        $seo['description'] = config()['web']['site_description'];
        $seo['keywords'] =$tag.','.config()['web']['site_keywords'];
        $this->assign('seo', $seo);
        doZfAction('home_tdk',['type'=>$this->action,'data'=>$tag,'request'=>request()]);
        $tpl = $this->zf_tpl_suffix.($this->lang==''?'':'/'.$this->lang).'/cate/tag';
        return view($tpl); 
    }
    public function special(){
        $where = [['status','=',1]];
        $where = array_merge($where, $this->common_select_tag);
        $list = db('special')->where($where)->order('sort desc,ctime desc,id desc')->paginate(100,false, ['query' => request()->param()]);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        
        $seo['title'] = '专题-'.config()['web']['site_name'];
        $seo['description'] = config()['web']['site_description'];
        $seo['keywords'] =config()['web']['site_keywords'];
        $this->assign('seo', $seo);
        doZfAction('home_tdk',['type'=>$this->action,'data'=>'','request'=>request()]);
        $tpl = $this->zf_tpl_suffix.($this->lang==''?'':'/'.$this->lang).'/cate/special';
        return view($tpl); 
    }

    public function special_list()
    {
        $id = input('id',1);
        $this->assign('id',$id);
        //查询专题
        $special_res = db('special')->where(['id'=>$id,'status'=>1])->find();
        if(!$special_res){
            $this->error('专题不存在');
        }
        Db::name('special')->where("id=".$id)->setInc('hits');
        $this->assign('special_res',$special_res);
        //专题下的文章
        $where[] = ['sp.special_id','=',$id];
        $where[] = ['p.status','=',1];
        $where[] = ['p.ctime','<',time()];
        $list = Db::name('special_post sp')
                ->field('p.*')
                ->where($where)
                ->join('post p','p.id=sp.post_id')
                ->order('p.sort desc,p.ctime desc,p.id desc')
                ->paginate(10);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);

        $relevant = Db::name('post')->where([['ctime','<',time()],['status','=',1]])->order('hits desc,ctime asc')->limit(10)->select();
        $this->assign('relevant',$relevant);

        $seo['title'] = ($special_res['seo_t']==''?$special_res['name']:$special_res['seo_t']).'-'.config()['web']['site_name'];
        $seo['description'] = ($special_res['seo_d']==''?config()['web']['site_description']:$special_res['seo_d']);
        $seo['keywords'] = ($special_res['seo_k']==''?config()['web']['site_keywords']:$special_res['seo_k']);
        $this->assign('seo', $seo);
        doZfAction('home_tdk',['type'=>$this->action,'data'=>$special_res,'request'=>request()]);
        $this->assign('act', 'list');
        $tpl = ($this->zf_tpl_suffix==''?'':$this->zf_tpl_suffix.'/').'cate/tpl_special_list';
        return view($tpl); 
    }
    public function page(){
        $cid = input('cid',0);
        $content = Db::name('category')->where(['status'=>1,'cid'=>$cid])->find();

        $this->assign('content',$content);
        $this->assign('cid',$cid);
        $seo['title'] = $content['name'].'-'.config()['web']['site_name'];
        $seo['keywords'] = config()['web']['site_keywords'];
        $seo['description'] = config()['web']['site_description'];
        $this->assign('seo', $seo);
        doZfAction('home_tdk',['type'=>$this->action,'data'=>$content,'request'=>request()]);
        $tpl = $this->zf_tpl_suffix.($this->lang==''?'':'/'.$this->lang).'/cate/page';
        return view($tpl); 
    }
     //返回当前最顶层栏目
    protected function get_top_category($cid = 0) {
        $data = Db::name('category')->field('cid,pid,name,cname,icon,tpl_category,tpl_post,mid,sort,menu')->where(['status'=>1])->order("sort asc,cid asc")->select();
        $cat = new cat(array('cid', 'pid', 'name', 'cname')); //初始化无限分类
        $list = $cat->getPath($data, $cid); //获取分类数据树结构
        return $list[0];
    }
    //当前位置导航
    protected function get_path($cid = 0, $space = '>') {
        //查询分类信息
        $data = Db::name('category')->field('cid,pid,name,cname,icon,tpl_category,tpl_post,mid,sort,menu')->where(['status'=>1])->order("sort asc,cid asc")->select();
        $cat = new cat(array('cid', 'pid', 'name', 'cname')); //初始化无限分类
        $list = $cat->getPath($data, $cid); //获取分类数据树结构
        $path = '';
        if (is_array($list)) {
            foreach ($list as $vo) {
                $path .= $space . ' <a href="/cate/' . $vo['cid'] . '.html">' . $vo['name'] . '</a> ';
            }
        }
        return $path;
    }
    //子栏目所有CID
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
    public function _seo($content_str){
        return  $content_str;
    }
   

   
   


}


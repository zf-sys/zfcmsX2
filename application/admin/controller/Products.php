<?php
namespace app\admin\controller;
use think\Controller;
use Wmc1125\TpFast\Category as cat;
use think\Db;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Products extends Admin
{
    public function __construct (){
        parent::__construct();
    }
    public function index()
    {
        echo '<a target="_blank" href="/admin/products/product">产品</a><br>';
        echo '<a target="_blank" href="/admin/products/cate">产品分组</a><br>';
    }
    public function product()
    {
        $admin = session('admin');
        if($admin['gid']==3){
            $where[] = ['uid','=',$admin['id']];
        }
        // 栏目id
        $cid = input('cid',0);
        $where[] = ['status','<>',9];
        $where[] = ['is_product','=',1];

        if($cid!=0){
            $where[] = ['cid','=',$cid];
        }
        $title = input("title",'');
        if($title!=''){
            $where[] = ['title|id','like','%'.$title.'%'];
        }
        $this->assign("title",$title);

        $list = Db::name('post')->where($where)->order("id desc")->paginate(6,false,['query' => request()->param()]);
        if(!$list){
            $list = [];
        }

        $page = $list->render();
        $this->assign("list",$list);
        $this->assign("page",$page);
        $res =  Db::name('product_cate')->where(['cid'=>$cid])->find();
        $this->assign("res",$res);
        return view();

    }
    // 内容修改
    public function product_edit()
    {
        if(request()->isGet()){
            $res = Db::name('post')->where(['id'=>input('id')])->find();
            $this->assign("res",$res);

            $cate_where[] = ['status','=',1];
            $res2 = Db::name('product_cate')->where($cate_where)->order("sort asc,cid asc")->select();
            $cat = new cat(array('cid', 'pid', 'name', 'cname')); //初始化无限分类
            $plist = $cat->getTree($res2); //获取分类数据树结构
            if(!$plist){
                $plist = [];
            }
            $plist[999] =
                [
                    'cid'=>0,
                    'name'=>'顶级目录',
                    'cname'=>'顶级目录'
                ];
            $this->assign("mlist",$plist);
            $brand_list = Db::name('product_brands')->where([['status','<>',9]])->order("id asc")->select();
            $this->assign("brand_list",$brand_list);
            return view('products/product_add');
        }
        if(request()->isPost()){
            $data = input('post.');
            unset($data['content-html-code']);
            if(isset($data['relevan_id'])){
                unset($data['keyword']);
            }
            if(isset($data['album_list']) && is_array($data['album_list'])){
                $data['album'] = implode(",", $data['album_list']);
                unset($data['album_list']);
            }
            // if($data['ctime']!=''){
            //     $data['ctime'] =  strtotime($data['ctime']);
            // }else{
            //     $data['ctime'] =  time();
            // }

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
            unset($data['_temp_arr_key']);
            $res =  Db::name('post')->where(['id'=>$data['id']])->update($data);
            if($res)
            {
                return jssuccess('修改成功');
            }else{
                return jserror('修改失败');
            }
        }
    }

    // 内容增加
    public function product_add()
    {
        if(request()->isGet()){
            if(!session('_gid')){
                session('_gid',mt_rand(10000,99999));
            }

            $res2 = Db::name('product_cate')->where('status!=9')->order("sort asc,cid asc")->select();
            $cat = new cat(array('cid', 'pid', 'name', 'cname')); //初始化无限分类
            $plist = $cat->getTree($res2); //获取分类数据树结构
            if(!$plist){
                $plist = [];
            }
            $plist[999] =
                [
                    'cid'=>0,
                    'name'=>'顶级目录',
                    'cname'=>'顶级目录'
                ];
            $this->assign("mlist",$plist);
            $brand_list = Db::name('product_brands')->where([['status','<>',9]])->order("id asc")->select();
            $this->assign("brand_list",$brand_list);

            return view();
        }
        if(request()->isPost()){
            $data = input('post.');
            unset($data['content-html-code']);
            if(isset($data['album_list']) && is_array($data['album_list'])){
                $data['album'] = implode(",", $data['album_list']);
                unset($data['album_list']);
            }
            // if($data['ctime']!=''){
            //     $data['ctime'] =  strtotime($data['ctime']);
            // }else{
            $data['ctime'] =  time();
            // }

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
            unset($data['_temp_arr_key']);
            $data['uid'] = session('admin')['id'];

            $res = Db::name('post')->insert($data);
            if($res)
            {
                return jssuccess('新增成功');
            }else{
                return jserror('新增失败');
            }
        }
    }
    public function product_sku_edit(){
        $step = input('step',1);
        $this->assign("step",$step);
        // $arr = array(
        //   array('6s','6sp'),
        //   array('黑色','白色'),
        //   array('68G','128G'),
        //   // array('a','b','c')
        // );
        // $sku = dikaer($arr);
        $id = input('id',0);
        $res = Db::name('post')->field('id,title,goods_sku_release')->where(['id'=>input('id')])->find();
        $this->assign("res",$res);
        $sku = Db::name('product_sku')->field("id,sku_name as k,GROUP_CONCAT(sku_value) as v")->where(['gid'=>$id,'status'=>1])->group('sku_name')->order('p_sort asc,id asc')->select();
        $_arr = [];
        $sku_parm = [];
        foreach($sku as $k=>$vo){
            $sku_parm[$k] = $vo['k'];
            $_arr[$k] = Db::name('product_sku')->field('sku_value as title,id')->where(['gid'=>$id,'status'=>1,'sku_name'=>$vo['k'],'sku_name'=>$vo['k'],'status'=>1])->order('sort asc,id asc')->select();
        }
        $sku = dikaer($_arr);
        $this->assign('sku_parm',$sku_parm);
        $this->assign('sku_parm_value',$_arr);
        $this->assign('sku',$sku);
//        dd($sku);
        //查询常用的sku名
        $sku_default = $sku_parm;
        $this->assign('sku_default',$sku_default);
        $sku_name_def = session('sku_name');
        $this->assign('sku_name_def',$sku_name_def);
        return view();
    }
    public function sku_list_value(){
        return view();
    }
    public function sku_add(){
        $data = input('post.');
        $data['_gid'] = 0;
        $data['uid'] = session('admin')['id'];
        if($data['sku_name']=='' || $data['sku_value']==''){
            return jserror('不能为空');
        }
        //判断是否重复提交
        if(Db::name('product_sku')->field('id')->where(['gid'=>$data['gid'],'uid'=>$data['uid'],'sku_name'=>$data['sku_name'],'sku_value'=>$data['sku_value'],'status'=>1])->find()){
            return jserror('请勿重复提交');
        }
        $data['sort'] = intval(Db::name('product_sku')->where(['gid'=>$data['gid'],'sku_name'=>$data['sku_name'],'status'=>1])->count())+1;
        $res = Db::name('product_sku')->insert($data);
        if($res){
            Db::name('post')->where(['id'=>$data['gid']])->update(['goods_sku_release'=>0]);
            session('sku_name',$data['sku_name']);
            return jssuccess('ok');
        }else{
            return jserror('保存失败');
        }
    }
    public function sku_del(){
        $data = input('post.');
        // $data['uid'] = session('admin')['id'];

        //判断是否重复提交
        if(!Db::name('product_sku')->field('id')->where(['gid'=>$data['gid'],'sku_value'=>$data['sku_value']])->find()){
            return jserror('不存在');
        }
        $res = Db::name('product_sku')->where($data)->update(['status'=>9]);
        if($res){
            Db::name('post')->where(['id'=>$data['gid']])->update(['goods_sku_release'=>0]);
            return jssuccess('ok');
        }else{
            return jserror('删除失败');
        }
    }
    //提交商品规格详情
    public function product_sku_parm_edit(){
        $data = input('post.');
        $_parm = $data['parm'];
        //提交主要信息
        $num = count($data['pic']);
        $parm_num = count($_parm);
        try {
            Db::startTrans();
            //判断是否存在,存在则删除
            if(Db::name('product_sku_info')->where(['gid'=>$data['gid']])->find()){
                Db::name('product_sku_info')->where(['gid'=>$data['gid']])->update(['status'=>9]);
            }
            if(Db::name('product_sku_info_parm')->where(['gid'=>$data['gid']])->find()){
                Db::name('product_sku_info_parm')->where(['gid'=>$data['gid']])->update(['status'=>9]);
            }
            for($i=0;$i<$num;$i++){
                $_info['gid'] = $data['gid'];
                $_info['pic'] = $data['pic'][$i];
                $_info['code'] = $data['code'][$i];
                $_info['skuu'] = $data['skuu'][$i];
                $_info['price'] = $data['price'][$i];
                $_info['price_line'] = $data['price_line'][$i];
                $_info['stock'] = $data['stock'][$i];
//                $_info['kg'] = $data['kg'][$i];
                $_info['uid'] = session('admin')['id'];
                $info_id = Db::name('product_sku_info')->insertGetId($_info);
                //插入数据库,获得info_id
                //
                if(isset($data['parm'][0][1])){
                    //多参数
                    for($t=0;$t<$parm_num;$t++){
                        $_parm_data['info_id'] = $info_id;
                        $_parm_data['sku_id'] = $data['parm'][$t][$i];
                        $_parm_data['gid'] = $data['gid'];
                        $_parm_data['uid'] = session('admin')['id'];
                        //插入数据库
                        Db::name('product_sku_info_parm')->insert($_parm_data);
                    }
                }else{
                    $_parm_data['info_id'] = $info_id;
                    $_parm_data['sku_id'] = $data['parm'][$i][0];
                    $_parm_data['gid'] = $data['gid'];
                    $_parm_data['uid'] = session('admin')['id'];
                    //插入数据库
                    Db::name('product_sku_info_parm')->insert($_parm_data);
                }
                //

            }
            Db::name('post')->where(['id'=>$data['gid']])->update(['goods_sku_release'=>1]);
            Db::commit();
            return jssuccess('提交成功');
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return jserror('失败'.$e->getMessage());
        }

    }

    public function cate(){
        $where[] = ['status','<>',9];
        $res = Db::name('product_cate')->where($where)->order("sort asc,cid asc")->select();
        $cat = new cat(array('cid', 'pid', 'name', 'cname')); //初始化无限分类
        $list = $cat->getTree($res); //获取分类数据树结构
        if(!$list){
            $list = [];
        }
        $this->assign("list",$list);


        return view();
    }
    public function cate_add()
    {
        if(request()->isPost()){
            $data = input('post.');
            if($data['name']==''){
                return jserror('请填写信息');exit;
            }
            $data['status'] = ZFC('webconfig.category_status');
            if($data['status']==''){
                $data['status'] = 0;
            }else{
                $data['status'] = 1;
            }
            $res =Db::name('product_cate')->insert($data);
            if($res){
                return jssuccess('新增成功');
            }else{
                return jserror('新增失败');exit;
            }
        }
        return view();
    }
    //修改
    public function cate_edit()
    {
        if(request()->isPost()){
            $data = input('post.');
            $res =  Db::name('product_cate')->where(['cid'=>$data['cid']])->update($data);
            if($res){
                return jssuccess('修改成功');
            }else{
                return jserror('修改失败');
            }
        }
        $cid = input('cid','');
        $res = Db::name('product_cate')->where(['cid'=>$cid])->find();
        $this->assign("res",$res);

        $res2 = Db::name('product_cate')->where('status!=9')->order("sort asc,cid asc")->select();
        $cat = new cat(array('cid', 'pid', 'name', 'cname')); //初始化无限分类
        $plist = $cat->getTree($res2); //获取分类数据树结构
        if(!$plist){
            $plist = [];
        }
        $this->assign("plist",$plist);

        return view();
    }


    public function chanage_switch()
    {
        $data = input("");
        $is_show = input('status');
        $id = input('id');

        $res = db('order')->where('id', $id)->update([$data['field'] => $is_show]);
        if($data['field']=='pay_status'){
            db('order_goods')->where('oid', $id)->update([$data['field'] => $is_show]);
        }
        if($res){
            return jssuccess('更新成功');
        }else{
            return jserror('更新失败');
        }
    }



    public function tags(){
        $where[] = ['status','<>',9];
        $store_id = session('store_id');
        $where[] = ['store_id','=',$store_id];
        $list = Db::name('product_tags')->where($where)->order("id asc")->select();
        $this->assign("list",$list);
        return view();
    }
    public function tags_add()
    {
        if(request()->isPost()){
            $data = input('post.');
            if($data['tag_name']==''){
                return jserror('请填写信息');exit;
            }
            $store_id = session('store_id');
            $data['store_id'] = $store_id;
            $data['ctime'] = time();
            $res =Db::name('product_tags')->insert($data);
            if($res){
                return jssuccess('新增成功');
            }else{
                return jserror('新增失败');exit;
            }
        }
        return view();
    }
    //修改
    public function tags_edit()
    {
        if(request()->isPost()){
            $data = input('post.');
            $data['utime'] = time();
            $res =  Db::name('product_tags')->where(['id'=>$data['id']])->update($data);
            if($res){
                return jssuccess('修改成功');
            }else{
                return jserror('修改失败');
            }
        }
        $id = input('id','');
        $res = Db::name('product_tags')->where(['id'=>$id])->find();
        $this->assign("res",$res);
        return view();
    }
    public function brands(){
        $where[] = ['status','<>',9];
        // $store_id = session('store_id');
        // $where[] = ['store_id','=',$store_id];
        $list = Db::name('product_brands')->where($where)->order("id asc")->select();
        $this->assign("list",$list);
        return view();
    }
    public function brands_add()
    {
        if(request()->isPost()){
            $data = input('post.');
            if($data['brand_name']==''){
                return jserror('请填写信息');exit;
            }
            $data['ctime'] = time();
            // $store_id = session('store_id');
            // $data['store_id'] = $store_id;
            $res =Db::name('product_brands')->insert($data);
            if($res){
                return jssuccess('新增成功');
            }else{
                return jserror('新增失败');exit;
            }
        }
        return view();
    }
    //修改
    public function brands_edit()
    {
        if(request()->isPost()){
            $data = input('post.');
            $data['utime'] = time();
            $res =  Db::name('product_brands')->where(['id'=>$data['id']])->update($data);
            if($res){
                return jssuccess('修改成功');
            }else{
                return jserror('修改失败');
            }
        }
        $id = input('id','');
        $res = Db::name('product_brands')->where(['id'=>$id])->find();
        $this->assign("res",$res);
        return view();
    }

    //复制销售价到多规格
    public function copy_price(){
        $gid = input('gid');
        $price = db('post')->where('id',$gid)->value('price');
        // dd($price);
        $res = db('product_sku_info')->where(['gid'=>$gid,'status'=>1])->update(['price'=>$price]);
        if($res){
            $this->success('更新成功');
        }else{
            $this->jserror('更新失败');
        }
    }

    public function product_sku_name_list()
    {
//        {"code":1,"message":"OK","data":[{"value":"dall-e-3","name":"dall-e-3"}],"timestamp":1735025003}
        $sku_list = [
            ['value'=>'颜色','name'=>'颜色'],
            ['value'=>'大小','name'=>'大小'],
            ['value'=>'年级','name'=>'年级'],
        ];
        return json(['code'=>1,'message'=>'OK','data'=>$sku_list,'timestamp'=>time()]);
    }
    public function sku_value_sort(){
        $data = input('post.');
        $gid = input('gid');
//        array(1) {
//            ["guigeData"]=>
//              array(4) {
//                            [0]=>
//                array(2) {
//                                ["name"]=>
//                  string(6) "大小"
//                                ["sli_val_list_data"]=>
//                  array(2) {
//                                    [0]=>
//                    string(5) "12pro"
//                                    [1]=>
//                    string(5) "15pro"
//                  }
//                }
//                [1]=>
//                array(2) {
//                                ["name"]=>
//                  string(6) "年级"
//                                ["sli_val_list_data"]=>
//                  array(2) {
//                                    [0]=>
//                    string(6) "初中"
//                                    [1]=>
//                    string(6) "高中"
//                  }
//                }
//              }
//            }
        //根据这个数组排序
        $arr = false;
        if(isset($data['guigeData'])){
            foreach($data['guigeData'] as $k=>$vo){
                 if(isset($vo['sli_val_list_data'])){
                    foreach($vo['sli_val_list_data'] as $k2=>$vo2){
                        $arr = ['sku_name'=>$vo['name'],'sku_value'=>$vo2,'p_sort'=>$k,'sort'=>$k2,'gid'=>$gid];
                        db('product_sku')->where([['gid','=',$arr['gid']],['sku_name','=',$arr['sku_name']],['sku_value','=',$arr['sku_value']],['status','=',1]])->update($arr);
                    }
                 }
            }
            return jssuccess('更新成功');

        } else{
            return jserror('数据格式错误');
        }

    }
    public function product_sku_pl(){
        $data = input('post.');
        if($data['pl_val']==''){
//            return jserror('请填写值信息');exit;
        }
        $info_id = db('product_sku_info_parm')->where([['status','=',1],['gid','=',$data['gid']],['sku_id','=',$data['pl_sku_id']]])->column('info_id');
        if(count($info_id)<=0){
            return jserror('没有找到对应的参数');
        }
        $is_up = Db::name('product_sku_info')->where([['id','in',$info_id],['gid','=',$data['gid']]])->update([$data['pl_field']=>$data['pl_val'],'utime'=>time()]);
        if($is_up){
            return jssuccess('更新成功');
        }else{
            return jserror('更新失败');
        }
    }




}

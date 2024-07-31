<?php

namespace general;
/**
 * 生成表格类
 * 使用了layui类,主要包括表头(新增/搜索/下载等),表格数据,分页
 *
 *
 *
 * 示例:
 *
* $pres =ZFTB('category')->where(['status'=>1])->select();
* $cat = new cat(array('cid', 'pid', 'name', 'cname'));
* $plist = $cat->getTree($pres);
* if(!$plist){
* $plist = [];
* }
* $plist[999] =
* [
* 'cid'=>0,
* 'name'=>'顶级目录',
* 'cname'=>'顶级目录'
* ];
* $general_tb = new \general\table(['htmlName'=>'测试管理中心','formConfig'=>['method'=>'get','action'=>'/admin/category/post_list','id'=>'form_search','class'=>''],'batchDel'=>true,'idType'=>'id']);
* //            head
* $html = $general_tb->setHtmlHead([widget_st('layui','css')])
* ->setHtmlHead([widget_st('admin','css')])
* ->setHtmlHead(widget_st('bootstrap'))
* ->setHtmlHead([widget_st('jq','js')])
* ->setHtmlHead([widget_st('layui','js')])
* ->setHtmlHead([widget_st('common','js')])
* ->setDiyCss(" .zf_all_check{color:#ccc;}")
* //                表单
* ->setFormItem('mid','hidden','模型id',$mid)
 * ->setFormItem('cid','hidden','栏目id',$cid)
 * ->setFormItem('keyword','text','关键词',$keyword,['theme_type'=>2,'col'=>4,'placeholder'=>'请输入标题或内容或摘要'])
 * ->setFormItem('cid','select','类别',$cid,['theme_type'=>2,'options'=>$plist,'option_id'=>'cid','option_name'=>'name','placeholder'=>'请选择类别'])
 * ->setFormItem('is_sl','select_simple','是否收录','--',['options'=>['是','否'],'placeholder'=>'请选择是否收录'])
* ->setFormItem('ctime','time','创建时间','',['col'=>4,'placeholder'=>'请选择时间'])
 *
* //                按钮
* ->setBtnItem('onclick','新增内容',url('admin/category/post_add',['cid' => $cid,'mid' => $mid] ))
* ->setBtnItem('onclick','批量新增',url('admin/category/post_add_pl',['cid' => $cid,'mid' => $mid] ))
* ->setBtnItem('href','全部',url('admin/category/post_list',['cid' => $cid,'mid' => $mid] ),['target'=>'_self','class'=>'layui-btn-normal'])
* ->setBtnItem('href','新增(新页面打开)',url('admin/category/post_add',['cid' => $cid,'mid' => $mid] ),['target'=>'_blank'])
* //                 表格
* ->setTableField('id','ID')
* ->setTableField('title','标题','',['edit_id_type'=>'id','edit_dbname'=>'post'])
* ->setTableField('pic','图片','pic',['append'=>'<img src="{pic}" style="width: 50px;height: 50px;">'])
* ->setTableField('bd_sl','百度收录')
* ->setTableField('sort','排序','edit',['edit_id_type'=>'id','edit_dbname'=>'post'])
* ->setTableField('hits','点击量')
* ->setTableField('recommend','推荐','checkbox',['edit_id_type'=>'id','edit_dbname'=>'post'])
* ->setTableField('status','状态','checkbox',['edit_id_type'=>'id','edit_dbname'=>'post'])
* ->setTableField('ctime','时间','time',['append'=>'Y-m-d H:i:s'])
* //操作
* ->setTableField('--','操作','act',['append'=>[
* ['del','删除','/admin/common/del_post?db=post&id=<id>&field=status',['target'=>'_self','width'=>800,'height'=>600]],
* ['onclick','编辑','/admin/category/post_add?id=<id>&cid=<cid>&mid='.$mid,['target'=>'_self','width'=>800,'height'=>600]],
* ['href','编辑(新页面打开)','/admin/category/post_add?id=<id>&cid=<cid>&mid='.$mid,['target'=>'_blank'  ]],
* ['href','预览','/detail/<id>.html?t=show_admin',['target'=>'_blank'  ]],
* ]])
* ->setTableData($list)
* ->setDiyJs(" $('.zf_all_check').click(function(){
* alert('123');
* });")
* ->build();
* echo $html;die;
 */
class table
{
    //html
    private $htmlName='管理中心';
    private $htmlHead=[];

    //头部
    private $formConfig=[];
    private $formList=[];
    private $btnList=[];


    //批量删除
    private $batchDel=false;
    private $tableName='tb';
    private $tableData;
    private $tableField=[];
    private $idType='id';

    private $diy_js='';
    private $diy_css='';

    public function __construct($config = []){
        $this->htmlName = isset_arr_key($config,'htmlName','管理中心');
        $this->htmlHead = isset_arr_key($config,'htmlHead',[]);
        $this->tableName = isset_arr_key($config,'tableName','tb');
        $this->formConfig = isset_arr_key($config,'formConfig',[]);
        $this->batchDel = isset_arr_key($config,'batchDel',false);
        $this->idType = isset_arr_key($config,'idType','id');

        if(!isset($this->formConfig['class'])){
            $this->formConfig['class']= '';
        }
        return $this;
    }

    /**
     * @param $arr
     * @return $this
     *
     * 自定义设置head
     * ->setHtmlHead([widget_st('layui','css')])
     * ->setHtmlHead([widget_st('admin','css')])
     * ->setHtmlHead(widget_st('bootstrap'))
     */
    public function setHtmlHead($arr){
        $_arr = $this->htmlHead;
        //判断是否存在,存在则合并
        if(is_array($arr)){
            $_arr = array_merge($_arr , $arr);
        }elseif (is_string($arr)){
            $_arr[] = $arr;
        }else{
            return $this;
        }
        //合并存在的项
        $_arr = array_unique($_arr);
        $this->htmlHead = $_arr;
        return $this;
    }
    public function setFormItem($name='',$type='hidden',$label='标题',$value='',$arr=[]){
//        $arr = ['name'=>'keyword','type'=>'text','label'=>'搜索','placeholder'=>'请输入标题或内容或摘要','value'=>$keyword,'style'=>'']
        $arr['name'] = $name;
        $arr['type'] = $type;
        $arr['label'] = $label;
        $arr['value'] = $value;
        $def_arr = [
            'col'=>'2',
        ];
        foreach ($def_arr as $k=> $item) {
            if(!isset($arr[$k]) || $arr[$k]==''){
                $arr[$k] = $def_arr[$k];
            }
        }
        $_arr = $this->formList;
        $_arr[] = $arr;
        $this->formList = $_arr;
        return $this;
    }
    public function setBtnItem($type='',$name='',$href='',$arr=[]){
        $arr['type'] = $type;
        $arr['name'] = $name;
        $arr['href'] = $href;
        $def_arr = [
            'class'=>'',
            'style'=>'',
            'width'=>800,
            'height'=>600
        ];
        foreach ($def_arr as $k=> $item) {
            if(!isset($arr[$k]) || $arr[$k]==''){
                $arr[$k] = $def_arr[$k];
            }
        }
        $_arr = $this->btnList;
        $_arr[] = $arr;
//        $_arr = array_unique($_arr);
        $this->btnList = $_arr;
        return $this;
    }
    public function setTableField($field,$title,$ztype='',$arr=[]){
        $arr['field'] = $field;
        $arr['title'] = $title;
        $arr['ztype'] = $ztype;
        $def_arr = [
            'edit_id_type'=>'id',
            'edit_dbname'=>'post',
            'class'=>'',
            'style'=>'',
            'append'=>''
        ];
        foreach ($def_arr as $k=> $item) {
            if(!isset($arr[$k]) || $arr[$k]==''){
                $arr[$k] = $def_arr[$k];
            }
        }
        if($arr['ztype']=='act'){
            foreach ($arr['append'] as $k1=>$v1){
                $_act_arr = [];
                if(isset($v1[3])){
                    $_act_arr = $v1[3];
                }
                $_act_arr['type'] = $v1[0];
                $_act_arr['name'] = $v1[1];
                $_act_arr['href'] = $v1[2];
                $_act_arr_def_arr = [
                    'class'=>'',
                    'style'=>'',
                    'width'=>800,
                    'height'=>600
                ];
                foreach ($_act_arr_def_arr as $k=> $item) {
                    if(!isset($_act_arr[$k]) || $_act_arr[$k]==''){
                        $_act_arr[$k] = $_act_arr_def_arr[$k];
                    }
                }

                $arr['append'][$k1] = $_act_arr;

            }


//            dd($arr['append']);


        }
        $_arr = $this->tableField;
        $_arr[] = $arr;
        $this->tableField = $_arr;
        return $this;
    }
    public function setTableData($obj){
        $this->tableData = $obj;
        return $this;
    }
    public function setDiyJs($js)
    {
        $this->diy_js = $js;
        return $this;
    }
    public function setDiyCss($css)
    {
        $this->diy_css = $css;
        return $this;
    }
    private function generalForm()
    {
        $html = '';
        //按断$this->formConfig是否为[]
        if(!empty($this->formConfig)){
            $html.='<form method="'.$this->formConfig['method'].'" action="'.$this->formConfig['action'].'" id="'.$this->formConfig['id'].'" class="layui-form layui-row layui-col-space16 '.$this->formConfig['class'].' ">';
            foreach ($this->formList as $k=>$v){
                if(!isset($v['name'])){
                    continue;
                }
                if($v['type']=='hidden'){
                    $html.='<input type="'.$v['type'].'" name="'.$v['name'].'" value="'.$v['value'].'" />';
                }elseif($v['type']=='select'){
                    $html.='<div class="layui-col-md'.$v['col'].'">
                    <div class="layui-input-wrap">
                    ';
                    if(isset($v['theme_type']) && $v['theme_type']==2){
                        $html.=$v['label'];
                    }
                        $html.='
                      <select name="'.$v['name'].'" lay-search>';
                    if(isset($v['options'])){
                        foreach ($v['options'] as $k1=>$v1){
                            $html.='<option value="'.$v1[$v['option_id']].'" '.($v1[$v['option_id']]==$v['value']?'selected':'').' >'.$v1[$v['option_name']].'</option>';
                        }
                    }
                      $html.='</select>
                    </div>
                    </div>';
                }elseif($v['type']=='select_simple'){
                    $html.='<div class="layui-col-md'.$v['col'].'">
                    <div class="layui-input-wrap">
                    ';
                    if(isset($v['theme_type']) && $v['theme_type']==2){
                        $html.=$v['label'];
                    }
                    $html.='
                      <select name="'.$v['name'].'" lay-search>';
                    if(isset($v['options'])){
                        foreach ($v['options'] as $k1=>$v1){
                            $html.='<option value="'.$v1.'" '.($v1==$v['value']?'selected':'').' >'.$v1.'</option>';
                        }
                    }
                    $html.='</select>
                    </div>
                    </div>';
                }elseif($v['type']=='time'){
                    $html.='<div class="layui-col-md'.$v['col'].'">
                    <div class="layui-input-wrap">
                     待添加
                    </div>
                    </div>';
                }else{
                    $html.='<div class="layui-col-md'.$v['col'].'">
                    <div class="layui-input-wrap">
                    ';
                    if(isset($v['theme_type']) && $v['theme_type']==2){
                        $html.=$v['label'];
                    }
                    $html.='
                      <input type="'.$v['type'].'" name="'.$v['name'].'" value="'.$v['value'].'" placeholder="'.$v['placeholder'].'" class="layui-input" lay-affix="clear" >
                    </div>
                    </div>';
                }


            }
            $html.=' <div class="layui-btn-container layui-col-xs12">
              <button class="layui-btn" lay-submit lay-filter="demo-table-search">搜索</button>
            </div>';
            $html.='</form>';

        }else{
//            $html.='no存在form';

        }
        return $html;
    }
    private function generalBtn(){
        $html = '';
        if(!empty($this->btnList)){
            $html.='<div class="layui-inline">';
            foreach ($this->btnList as $k=>$v){
                if($v['type']=='href'){
                    $html.='<a class="layui-btn layui-btn-sm '.$v['class'].'" '.$v['style'].'  href="'.$v['href'].'" target="'.$v['target'].'">'.$v['name'].'</a>';
                }else{
                    $html.='<a class="layui-btn layui-btn-sm '.$v['class'].'" '.$v['style'].'  onclick="zfAdminShow('."'".$v['name']."'".' ,'."'".$v['href']."'".','.$v['width'].','.$v['height'].')">'.$v['name'].'</a>';
                }
            }
            $html.='</div>';
        }else{
//            $html.='no存在btn';
        }
        return $html;
    }
    private function generalTable(){
        $html = '';
        $html.='<div class="" style="line-height: 24px;">';
        if($this->batchDel){
            $html.='<a class="zf_all_check">全选</a>
            <span class="pl_del" style="color: red;">批量删除</span>';
        }
        $html.='<table class="layui-table">';
        $html.='<colgroup> <col></colgroup>';
        $html.='<thead><tr>';
        foreach ($this->tableField as $k=>$v){
//            if($v['show']){
               $html.='<th>'.$v['title'].'</th>';
//            }
        }
        $html.='</tr></thead>';
        $html.='<tbody>';
            foreach ($this->tableData as $k=>$v){
                    $html.='<tr>';
//                    dd($v);
//                dd($this->tableField);
                    foreach ($this->tableField as $k1=>$v1){
                        $html.='<td>';
                        if($this->batchDel && $k1==0 && ( !isset($v1['ztype'])|| $v1['ztype']!='act')){
                            $html.='<input class="zf_ids" type="checkbox" name="ids[]" value="'.$v[$this->idType].'" lay-skin="primary">';
                        }
                        if(isset($v1['ztype']) && $v1['ztype']=='act'){
                            foreach ($v1['append'] as $k2=>$v2){
                                //转换链接
                                $pattern = '/<(\w+)>/';
                                $v2['href'] = preg_replace_callback($pattern, function ($matches) use ($v) {
                                    $key = $matches[1];
                                    return isset($v[$key]) ? $v[$key] : $matches[0];
                                }, $v2['href']);

                                if($v2['type']=='href'){
                                    $html.='<a class="layui-btn layui-btn-sm '.$v2['class'].'" '.$v2['style'].'  href="'.$v2['href'].'" target="'.$v2['target'].'">'.$v2['name'].'</a>';
                                }elseif($v2['type']=='del'){
                                    //删除
                                    $html.='<a class="layui-btn  layui-btn-danger layui-btn-sm zf_btn_del '.$v2['class'].'" rel="'.$v2['href'].'" href="#">删除</a>';
                                }else{
                                    $html.='<a class="layui-btn layui-btn-sm '.$v2['class'].'" '.$v2['style'].'  onclick="zfAdminShow('."'".$v2['name']."'".' ,'."'".$v2['href']."'".','.$v2['width'].','.$v2['height'].')">'.$v2['name'].'</a>';

                                }
                            }
                        }else{
                            if(isset($v1['ztype'])){
                                if($v1['ztype']=='edit'){
                                    $html.='<input type="text" name="'.$v1['field'].'"  autocomplete="off" class="layui-input edit_sort"  value="'.$v[$v1['field']].'" item_f="'.$v1['field'].'" item-id="'.$v[$v1['edit_id_type']].'" item-dbname="'.$v1['edit_dbname'].'" >';
                                }elseif($v1['ztype']=='pic'){
                                    $_html = str_replace('{pic}', $v[$v1['field']], $v1['append']);
                                    $html.=$_html;
                                }elseif($v1['ztype']=='time') {
                                    $html .= date($v1['append'], $v[$v1['field']]);
                                }elseif($v1['ztype']=='checkbox'){
                                    $html.'<div class="layui-form" lay-filter="component-form-element">';
                                    $html.='<input type="checkbox" name="'.$v1['field'].'"  '.($v[$v1['field']]==1?'checked':'').'  lay-skin="switch" lay-text="开启|关闭" lay-filter="zstatus_change" item_f="'.$v1['field'].'" item_id="'.$v[$v1['edit_id_type']].'" item_dbname="'.$v1['edit_dbname'].'" >';
                                    $html.='</div>';

                                }else{
                                    $html.=$v[$v1['field']];
                                }
                            }else{
                                $html.=$v[$v1['field']];
                            }


                        }
                        $html.='</td>';
                    }

                    $html.='</tr>';
            }
        $html.='</tbody>';





        $html.='</table>';
        $html.= ($this->tableData->render());
        return $html;
    }
    private function generalHead()
    {
        $html= '';
        foreach ($this->htmlHead as $k=>$v){
            $html.=$v;
        }
        return $html;
    }
    public function build(){
        $html = '<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>'.$this->htmlName.'</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
  ';
        $html.=$this->generalHead();
        $html.='</head>
';
        if ($this->diy_css!=''){
            $html.='<style>
              '.$this->diy_css.'
</style>';
        }
$html.='<body>
  <div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
          <div class="layui-form-item">
            ';
                $html.=$this->generalForm();
                $html.=$this->generalBtn();
       $html.=$this->generalTable();




          $html.='
        
        
        
        
        </div>
        </div>
        
</body>
</html>';
          if ($this->diy_js!=''){
              $html.='<script>
              '.$this->diy_js.'
</script>';
          }
        return $html;
    }

}
function replacePlaceholders($matches) {
    global $vo;
    $key = $matches[1];
    return isset($vo[$key]) ? $vo[$key] : $matches[0];
}
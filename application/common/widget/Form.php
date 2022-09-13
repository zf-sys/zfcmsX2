<?php
namespace app\common\widget;
use think\Controller;
class Form {
   public function __construct (){
        $this->upload_one  = siteUrl('common/upload/upload_one');
        $this->upload_one_file  = siteUrl('common/upload/upload_one_file');
        $this->filesystem_upload  = siteUrl('common/Fileupload/upload');
        $this->meditor_upload  = siteUrl('common/upload/meditor_upload_one');

    }

    /**
     * @Notes    input输入框
     * @Author   子枫
     * @DateTime 2020-08-21
     * @Email    287851074@qq.com
     * @param    string           $title [description]
     * @param    string           $name  [description]
     * @param    string           $data  [description]
     * @return   [type]                  [description]
     */
    public function form_input($title='',$name='',$data='')
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $zf_html = <<<INFO
 <div class="layui-card-header">$title</div>
<div class="layui-card-body layui-row layui-col-space8">
    <div class="layui-col-md12">
      <input  class="layui-input " type="text" name="$name" lay-verify="required" placeholder="" autocomplete="off"  value="$data">
    </div>
</div>
INFO;
        return $zf_html;
    }
    public function form_radio($title='',$name='',$data='')
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $zf_html = '
 <div class="layui-card-header">'.$title.'</div>
 <div class="layui-card-body layui-row layui-col-space8 ">
    <div class="layui-col-md12 layui-form-item">
      <div class="layui-input-inline layui-form" >
        <input type="radio" name="'.$name.'" value="1" title="是" '.($data==1?'checked':'').'  ><div class="layui-unselect layui-form-radio"><i class="layui-anim layui-icon"></i><div>是</div></div>
        <input type="radio" name="'.$name.'" value="0" title="否"  '.($data=='0'?'checked':'').' ><div class="layui-unselect layui-form-radio"><i class="layui-anim layui-icon"></i><div>否</div></div>
      </div>
    </div>
  </div>
  <script>
  layui.use(["form","element"], function(){
  var $ = layui.$
  ,form = layui.form
  ,element = layui.element;
    
  });
  </script>';
        return $zf_html;
    }
                  
      


    /**
     * @Notes    text输入框
     * @Author   子枫
     * @DateTime 2020-08-21
     * @Email    287851074@qq.com
     * @param    string           $title [description]
     * @param    string           $name  [description]
     * @param    string           $data  [description]
     * @return   [type]                  [description]
     */
    public function form_textarea($title='',$name='',$data='')
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $zf_html = <<<INFO
 <div class="layui-card-header">$title</div>
<div class="layui-card-body layui-row layui-col-space8">
    <div class="layui-col-md12">
      <textarea name="$name" placeholder="请输入" class="layui-textarea">{$data}</textarea>
    </div>
</div>
INFO;
        return $zf_html;
    }
    public function form_time($title='',$name='',$data='')
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $zf_html = <<<INFO
 <div class="layui-card-header">$title</div>
<div class="layui-card-body layui-row layui-col-space8">
    <div class="layui-col-md12">
      <input type="text" name="$name" id="$name" lay-verify="date" placeholder="yyyy-MM-dd HH:mm:ss" autocomplete="off" class="layui-input" lay-key="1" value="{$data}">
    </div>
</div>
 <script>
 layui.use(['form','upload','laydate'], function(){
    var $ = layui.$
    ,element = layui.element
    ,form = layui.form
    ,laydate = layui.laydate;
    laydate.render({
      elem: "#$name"
      ,type: 'datetime'
    });
  });
</script>

INFO;
        return $zf_html;
    }
    public function form_select($title='',$name='',$data='',$list=[],$id_t='id')
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $zf_html = '
        <div class="layui-card-header">'.$title.'</div>
                  <div class="layui-card-body layui-row layui-col-space10">
                     <select class="layui-input" name="'.$name.'" >';
                      foreach($list as $k=>$vo){
                           $zf_html .= '<option value="'.$vo[$id_t].'" ';
                           if($data==$vo[$id_t]){
                            $zf_html.='selected';
                           }
                            $zf_html.='> ┃'.str_repeat('━━', substr_count($vo['cname'],'  ')).$vo['name'].'</option>';
                      }
                      $zf_html.='</select>
                  </div>
                  ';
        return $zf_html;
    }

    public function form_select_simple($title='',$name='',$data='',$list=[],$id_t='id')
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $zf_html = '
        <div class="layui-card-header">'.$title.'</div>
                  <div class="layui-card-body layui-row layui-col-space10">
                     <select name="'.$name.'" >';
                      foreach($list as $k=>$vo){
                           $zf_html .= '<option value="'.$vo.'" ';
                           if($data==$vo){
                            $zf_html.='selected';
                           }
                            $zf_html.='> '.$vo.'</option>';
                      }
                      $zf_html.='</select>
                  </div>
                  ';
        return $zf_html;
    }
    public function form_select_arr($title='',$name='',$data='',$list=[],$id_t='id')
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $zf_html = '
        <div class="layui-card-header">'.$title.'</div>
                  <div class="layui-card-body layui-row layui-col-space10">
                     <select name="'.$name.'" >';
                      foreach($list as $k=>$vo){
                           $zf_html .= '<option value="'.$vo[$id_t].'" ';
                           if($data==$vo[$id_t]){
                            $zf_html.='selected';
                           }
                            $zf_html.='> '.$vo['title'].'</option>';
                      }
                      $zf_html.='</select>
                  </div>
                  ';
        return $zf_html;
    }
    /**
     * @Notes    图片上传
     * @Author   子枫
     * @DateTime 2020-08-21
     * @Email    287851074@qq.com
     * @param    string           $title [description]
     * @param    string           $name  [description]
     * @param    string           $data  [description]
     * @return   [type]                  [description]
     */
    public function upload_pic($title='',$name='',$data='')
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $zf_html = <<<INFO
 <div class="layui-card-header">$title</div>
<div class="layui-card-body layui-row layui-col-space8">
    <div class="layui-col-md12">
      <input  class="layui-input" type="text" name="$name" lay-verify="required" placeholder="" autocomplete="off"  value="$data">
        <div class="layui-upload">
          <button type="button" class="layui-btn" id="$tpl_id">上传图片</button>
          <div class="layui-upload-list">
            <img class="layui-upload-img $tpl_id" style="max-height:200px;width:100%;" src="$data" >
            <p id="demoText"></p>
          </div>
        </div> 
    </div>
</div>
 <script>
 layui.use(['form','upload','laydate'], function(){
    var $ = layui.$
    ,element = layui.element
    ,form = layui.form
    ,upload = layui.upload;
    
    upload.render({
      elem: '#$tpl_id'
      ,url: "$this->upload_one"
      ,done: function(res){
        console.log(res)
        if(res.result==1){
            layer.msg("上传成功", {icon: 1});
            $('input[name="$name"]').val(res.msg);
            $('.$tpl_id').attr('src', res.msg);
        }else{
          layer.msg(res.msg, {icon: 2});
        }
      }
    });
  });
</script>
INFO;
        return $zf_html;
    }
    
    /**
     * @Notes    文件上传
     * @Author   子枫
     * @DateTime 2020-08-21
     * @Email    287851074@qq.com
     * @param    string           $title [description]
     * @param    string           $name  [description]
     * @param    string           $data  [description]
     * @return   [type]                  [description]
     */
    public function upload_file($title='',$name='',$data='')
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $zf_html = <<<INFO
 <div class="layui-card-header">$title</div>
<div class="layui-card-body layui-row layui-col-space8">
    <div class="layui-col-md12">
      <input  class="layui-input $tpl_id" type="text" name="$name" lay-verify="required" placeholder="" autocomplete="off"  value="$data">
    </div>
    <span type="button" class="layui-btn layui-btn-sm" id="$tpl_id">上传文件</span>
</div>
 <script>
 layui.use(['form','upload','laydate'], function(){
    var $ = layui.$
    ,element = layui.element
    ,form = layui.form
    ,upload = layui.upload;
    
    upload.render({
      elem: '#$tpl_id'
      ,url: "$this->upload_one_file"
      ,accept: 'file'
      ,done: function(res){
        console.log(res)
        if(res.result==1){
            layer.msg("上传成功", {icon: 1});
            $(".$tpl_id").val(res.msg);
        }else{
          layer.msg(res.msg, {icon: 2});
        }
      }
    });
  });
</script>
INFO;
        return $zf_html;
    }
    /**
     * @Notes    图集上传
     * @Author   子枫
     * @DateTime 2020-08-21
     * @Email    287851074@qq.com
     * @param    string           $title [description]
     * @param    string           $name  [description]
     * @param    string           $data  [description]
     * @return   [type]                  [description]
     */
    public function upload_album($title='',$name='',$data='')
    {
        $tpl_id='zf_'.mt_rand().'_'.time();

        if($data!='' && $data!=[]){
          $pics=explode(',',$data);
          $count=count($pics);
        }else{
          $count=0;
        }
        $zf_html ='';

        $zf_html .='
 <div class="layui-card-header">'.$title.'</div>
<div class="layui-card-body layui-row layui-col-space8">
    <div class="layui-col-md12">
        <div class="layui-upload">
          <button type="button" class="layui-btn" id="'.$tpl_id.'">上传'.$title.'</button>
          <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
              预览图：
              <div class="layui-row '.$tpl_id.'">';

                  for($i=0;$i<$count;$i++){
                    $zf_html .='<div class="layui-col-sm3" style="padding:10px;height:200px;"><div> <img src="'.$pics[$i].'" class="layui-upload-img"  style="width:100%;height:auto;" > </div>
                      <input type="hidden" name="zf_list_'.$name.'" value="'.$pics[$i].'" /><span style="text-align:center;display:block;cursor:pointer;" onclick="deleteFile(this)">删除</span>
                      </div>';
                  }
              $zf_html .='</div>
          </blockquote>
        </div> 
    </div>
</div>';
$zf_html .=<<<INFO
 <script>
 layui.use(['form','upload','laydate'], function(){
    var $ = layui.$
    ,element = layui.element
    ,form = layui.form
    ,upload = layui.upload;
    
     upload.render({
      elem: '#$tpl_id'
      ,url: "$this->upload_one"
      ,multiple: true
      ,before: function(obj){
      }
      ,done: function(res){
        console.log(res)
        if(res.result==1){
            layer.msg('上传成功', {icon: 1});
            console.log('---start---')
            $('.$tpl_id').append('<div class="layui-col-sm3" style="padding:10px;height:200px;"><div class> <img src="'+ res.msg +'" class="layui-upload-img"  style="width:100%;height:auto;" ></div><input type="hidden" name="zf_list_$name" value="'+ res.msg +'" /><span style="text-align:center;display:block;cursor:pointer;" onclick="deleteFile(this)">删除</span></div> ')
            console.log('---end---')
        }else{
          layer.msg(res.msg, {icon: 2});
        }
      }

    });

  });
</script>
INFO;
        return $zf_html;
    }
    /**
     * @Notes    图集上传
     * @Author   子枫
     * @DateTime 2020-08-21
     * @Email    287851074@qq.com
     * @param    string           $title [description]
     * @param    string           $name  [description]
     * @param    string           $data  [description]
     * @return   [type]                  [description]
     */
    public function upload_album_title($title='',$name='',$data='',$data_title='')
    {
        $tpl_id='zf_'.mt_rand().'_'.time();

        if($data!='' && $data!=[]){
          $pics=explode(',',$data);
          $titles = explode(',',$data_title);
          $count=count($pics);
        }else{
          $count=0;
        }
        $zf_html ='';

        $zf_html .='
 <div class="layui-card-header">'.$title.'</div>
<div class="layui-card-body layui-row layui-col-space8">
    <div class="layui-col-md12">
        <div class="layui-upload">
          <button type="button" class="layui-btn" id="'.$tpl_id.'">上传'.$title.'</button>
          <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
              预览图：
              <div class="layui-row '.$tpl_id.'">';

                  for($i=0;$i<$count;$i++){ 
                    $zf_html .='<div class="layui-col-sm3" style="padding:10px;height:200px;"><div> <img src="'.$pics[$i].'" class="layui-upload-img"  style="width:100%;height:auto;" /></div>
                      <input type="hidden" name="zf_list_'.$name.'" value="'.$pics[$i].'" />
                      <input type="text" class="layui-input" name="zf_list_title_'.$name.'" value="'.(isset($titles[$i])?$titles[$i]:'').'" />
                      <span style="text-align:center;display:block;cursor:pointer;" onclick="deleteFile(this)">删除</span></div>';
                  }
              $zf_html .='</div>
          </blockquote>
        </div> 
    </div>
</div>';
$zf_html .=<<<INFO
 <script>
 layui.use(['form','upload','laydate'], function(){
    var $ = layui.$
    ,element = layui.element
    ,form = layui.form
    ,upload = layui.upload;
    
     upload.render({
      elem: '#$tpl_id'
      ,url: "$this->upload_one"
      ,multiple: true
      ,before: function(obj){
      }
      ,done: function(res){
        console.log(res)
        if(res.result==1){
            layer.msg('上传成功', {icon: 1});
            console.log('---start---')
            $('.$tpl_id').append('<div class="layui-col-sm3" style="padding:10px;height:200px;"><div> <img src="'+ res.msg +'" class="layui-upload-img"  style="width:100%;height:auto;" ><input type="hidden" name="zf_list_$name" value="'+ res.msg +'" /><input type="text"  class="layui-input" name="zf_list_title_$name" value="" /><span style="text-align:center;display:block; cursor:pointer;" onclick="deleteFile(this)">删除</span></div> </div>')
            console.log('---end---')
        }else{
          layer.msg(res.msg, {icon: 2});
        }
      }

    });

  });
</script>
INFO;
        return $zf_html;
    }

    /**
     * @Notes    图片上传(从列表中上传)
     * @Author   子枫
     * @DateTime 2020-08-21
     * @Email    287851074@qq.com
     * @param    string           $title [description]
     * @param    string           $name  [description]
     * @param    string           $data  [description]
     * @return   [type]                  [description]
     */
    public function filesystem_pic($title='',$name='',$data='')
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $zf_html = <<<INFO
 <div class="layui-card-header">$title</div>
<div class="layui-card-body layui-row layui-col-space8">
    <div class="layui-col-md12">
      <input  class="layui-input $tpl_id" type="text" name="$name" lay-verify="required" placeholder="" autocomplete="off"  value="$data">
        <div class="layui-upload">
          <button type="button" class="layui-btn" id="$tpl_id">选择图片</button>
          <div class="layui-upload-list">
            <img class="layui-upload-img $tpl_id" style="height:auto;width:100%;" src="$data" >
            <p id="demoText"></p>
          </div>
        </div> 
    </div>
</div>
 <script>
 layui.use(['form','upload','laydate'], function(){
    var $ = layui.$
    ,element = layui.element
    ,form = layui.form
    ,upload = layui.upload;
    $('#$tpl_id').on('click',function(){
      layer.open({
        type: 2,
        area: ['1100px', '700px'],
        fixed: false,
        maxmin: true,
        content: "$this->filesystem_upload&cid=0&t=1&zf_class=.$tpl_id"
      });
    })


  });
</script>
INFO;
        return $zf_html;
    }
    /**
     * @Notes    图集上传(从列表中上传)
     * @Author   子枫
     * @DateTime 2020-08-21
     * @Email    287851074@qq.com
     * @param    string           $title [description]
     * @param    string           $name  [description]
     * @param    string           $data  [description]
     * @return   [type]                  [description]
     */
    public function filesystem_album($title='',$name='',$data='')
    {
        $tpl_id='zf_'.mt_rand().'_'.time();

        if($data!='' && $data!=[]){
          $pics=explode(',',$data);
          $count=count($pics);
        }else{
          $count=0;
        }
        $zf_html ='';

        $zf_html .='
 <div class="layui-card-header">'.$title.'</div>
<div class="layui-card-body layui-row layui-col-space8">
    <div class="layui-col-md12">
        <div class="layui-upload">
          <button type="button" class="layui-btn" id="'.$tpl_id.'">上传'.$title.'</button>
          <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
              预览图：
              <div class="layui-row '.$tpl_id.'">';
                for($i=0;$i<$count;$i++){
                  $zf_html .='<div class="layui-col-sm3" style="padding:10px;height:200px;"><div> <img src="'.$pics[$i].'" class="layui-upload-img"  style="width:100%;height:auto;" > </div>
                    <input type="hidden" name="zf_list_'.$name.'" value="'.$pics[$i].'" /><span style="text-align:center;display:block;cursor:pointer;" onclick="deleteFile(this)">删除</span>
                    </div>';
                }
              $zf_html .='</div>
          </blockquote>
        </div> 
    </div>
</div>';
$zf_html .=<<<INFO
 <script>
 layui.use(['form','upload','laydate'], function(){
    var $ = layui.$
    ,element = layui.element
    ,form = layui.form
    ,upload = layui.upload;

    $('#$tpl_id').on('click',function(){
      layer.open({
        type: 2,
        area: ['1100px', '700px'],
        fixed: false,
        maxmin: true,
        content: "$this->filesystem_upload&cid=0&t=2&name=$name&zf_class=.$tpl_id"
      });
    })
  });
</script>
INFO;
        return $zf_html;
    }
    /**
     * @Notes    文件上传(从列表中上传)
     * @Author   子枫
     * @DateTime 2020-08-21
     * @Email    287851074@qq.com
     * @param    string           $title [description]
     * @param    string           $name  [description]
     * @param    string           $data  [description]
     * @return   [type]                  [description]
     */
    public function filesystem_file($title='',$name='',$data='')
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $zf_html = <<<INFO
 <div class="layui-card-header">$title</div>
<div class="layui-card-body layui-row layui-col-space8">
    <div class="layui-col-md12">
      <input  class="layui-input $tpl_id" type="text" name="$name" lay-verify="required" placeholder="" autocomplete="off"  value="$data">
        <div class="layui-upload">
          <button type="button" class="layui-btn" id="$tpl_id">选择文件</button>
        </div> 
    </div>
</div>
 <script>
 layui.use(['form','upload','laydate'], function(){
    var $ = layui.$
    ,element = layui.element
    ,form = layui.form
    ,upload = layui.upload;
    
    $('#$tpl_id').on('click',function(){
      layer.open({
        type: 2,
        area: ['1100px', '700px'],
        fixed: false,
        maxmin: true,
        content: "$this->filesystem_upload&cid=0&t=3&zf_class=.$tpl_id"
      });
    })


  });
</script>
INFO;
        return $zf_html;
    }
    /**
     * @Notes    ueditor编辑器
     * @Author   子枫
     * @DateTime 2020-08-21
     * @Email    287851074@qq.com
     * @param    string           $title [description]
     * @param    string           $name  [description]
     * @param    string           $data  [description]
     * @return   [type]                  [description]
     */
    public function form_ueditor($title='',$name='',$data=''){
        $tpl_id='zf_'.mt_rand().'_'.time();
        $zf_html = <<<INFO
            <div class="layui-card-header">
              <fieldset class="layui-elem-field layui-field-title site-title">
                <legend><a name="quickstart">$title</a></legend>
              </fieldset>
            </div>
            <div class="layui-card-body">
                <script id="$tpl_id" name="$name" type="text/plain" >$data</script>
            </div>
<script type="text/javascript"> 
var ue = UE.getEditor("$tpl_id",{
  initialFrameHeight:350,
  scaleEnabled:false
});
</script>
INFO;
        return $zf_html;
    }
    /**
     * @Notes    tinymce编辑器
     * @Author   子枫
     * @DateTime 2020-08-21
     * @Email    287851074@qq.com
     * @param    string           $title [description]
     * @param    string           $name  [description]
     * @param    string           $data  [description]
     * @return   [type]                  [description]
     */
    public function form_tinymce($title='',$name='',$data=''){
        $tpl_id='zf_'.mt_rand().'_'.time();
        $zf_html = <<<INFO
            <div class="layui-card-header">
              <fieldset class="layui-elem-field layui-field-title site-title">
                <legend><a name="quickstart">$title</a></legend>
              </fieldset>
            </div>
            <div class="layui-card-body">
                <textarea name="$name" id="$tpl_id">
                     $data
                </textarea>
            </div>
<script type="text/javascript"> 
tinymce.init({
    selector: '#$tpl_id',
    menubar:false,
    height: 500,
    language: 'zh_CN',
    convert_urls: false,
    plugins : ['advlist','autolink','link','code','image','preview','searchreplace','table','wordcount','media','fullscreen','codesample','axupimgs','powerpaste','bullist','numlist'], 
    toolbar: ' undo redo | fontselect styleselect | forecolor bold italic searchreplace |  alignleft aligncenter alignright  | bullist numlist link image axupimgs media table codesample | code preview fullscreen',
    
    powerpaste_word_import: 'propmt',
    powerpaste_html_import: 'propmt',
    powerpaste_allow_local_images: true,
    paste_data_images: true,

    images_upload_handler: function (blobInfo, succFun, failFun) {
        var xhr, formData;
        var file = blobInfo.blob();
        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', "$this->upload_one");
        var token = 'wx:zifeng1788';
        xhr.setRequestHeader("X-CSRF-Token", token);
        xhr.onload = function() {
            var json;
            if (xhr.status != 200) {
                failFun('HTTP Error: ' + xhr.status);
                return;
            }
            json = JSON.parse(xhr.responseText);
            if (!json ||  json.result != '1') {
                failFun('Invalid JSON: ' + xhr.responseText);
                return;
            }
            succFun(json.msg);
        };
        formData = new FormData();
        formData.append('file', file, file.name );
        xhr.send(formData);
    },
    mobile: {
      menubar: false
  },
    file_picker_types: 'media', 
    file_picker_callback: function(cb, value, meta) {
      if (meta.filetype == 'media'){
            let input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.onchange = function(){
                let file = this.files[0];
                let xhr, formData;
                xhr = new XMLHttpRequest();
                xhr.open('POST', "$this->upload_one_file");
                var token = 'wx:zifeng1788';
              xhr.setRequestHeader("X-CSRF-Token", token);
                xhr.withCredentials = self.credentials;
                xhr.upload.onprogress = function (e) {
                };
                xhr.onerror = function () {
                  console.log(xhr.status);
                  return;
                };
                xhr.onload = function () {
                  let json;
                  if (xhr.status < 200 || xhr.status >= 300) {
                    console.log('HTTP 错误: ' + xhr.status);
                    return;
                  }
                  json = JSON.parse(xhr.responseText);
                  if(json.result==1){
                    let mediaLocation=json.msg;
                    cb(mediaLocation, { title: file.name });
                  }else{
                    console.log(json.msg);
                    return;
                  }
                };
                formData = new FormData();
                formData.append('file', file);
                xhr.send(formData);
            }
            input.click();
        }
    }
  });


</script>
INFO;
        return $zf_html;
    }

    /**
     * @Notes    wangeditor编辑器
     * @Author   子枫
     * @DateTime 2020-08-21
     * @Email    287851074@qq.com
     * @param    string           $title [description]
     * @param    string           $name  [description]
     * @param    string           $data  [description]
     * @return   [type]                  [description]
     */
    public function form_wangeditor($title='',$name='',$data=''){
        $tpl_id='zf_'.mt_rand().'_'.time();
        $zf_html = <<<INFO
            <div class="layui-card-header">
              <fieldset class="layui-elem-field layui-field-title site-title">
                <legend><a name="quickstart">$title</a></legend>
              </fieldset>
            </div>
            <div class="layui-card-body">
                <div  class="fabu_editor " id="$tpl_id">$data</div>
                <textarea class="$tpl_id" style="width:100%; height:200px;" name="$name" hidden="">$data</textarea>
            </div>
<script>
$(function (){
    var E = window.wangEditor
    var $tpl_id = new E("#$tpl_id")
    // var text1 = $(".$tpl_id")
    $tpl_id.config.onchange = function (html) {
      $(".$tpl_id").val(html)
    }
    $tpl_id.config.debug = true;
    $tpl_id.config.pasteFilterStyle = false
    $tpl_id.config.pasteIgnoreImg = true
    $tpl_id.config.uploadFileName = 'file'; 
    $tpl_id.config.uploadImgServer = "$this->upload_one"; 
    $tpl_id.config.uploadImgMaxSize = 3 * 1024 * 1024; 
    $tpl_id.config.uploadImgHooks = {
      customInsert: function (insertImg, result, $tpl_id) {
        if(result.result==1){
          var url = result.msg
          insertImg(url)
        }else{
          layer.msg(上传失败,{icon: 2})
        }
      }
    }
    // 配置 server 接口地址
    $tpl_id.config.uploadVideoServer = "$this->upload_one_file";
    $tpl_id.config.uploadVideoName = 'file';
    $tpl_id.config.uploadVideoHooks = {
      // 上传视频之前
      before: function(xhr) {
          console.log(xhr)
          // 可阻止视频上传
      },
      // 视频上传并返回了结果，视频插入已成功
      success: function(xhr) {
          console.log('success', xhr)
      },
      // 视频上传并返回了结果，但视频插入时出错了
      fail: function(xhr, editor, resData) {
          console.log('fail', resData)
      },
      // 上传视频出错，一般为 http 请求的错误
      error: function(xhr, editor, resData) {
          console.log('error', xhr, resData)
      },
      // 上传视频超时
      timeout: function(xhr) {
          console.log('timeout')
      },
      // 视频上传并返回了结果，想要自己把视频插入到编辑器中
      // 例如服务器端返回的不是 { errno: 0, data: { url : '.....'} } 这种格式，可使用 customInsert
      customInsert: function(insertVideoFn, result) {
          // result 即服务端返回的接口
          console.log('customInsert', result)
          if(result.result==1){
            // insertVideoFn 可把视频插入到编辑器，传入视频 src ，执行函数即可
            insertVideoFn(result.msg)
          }else{
            alert(result.msg)
          }
      }
    }


    $tpl_id.create()
    $(".$tpl_id").val($tpl_id.txt.html())
});
</script>


INFO;
        return $zf_html;
    }

    /**
     * @Notes    meditor编辑器
     * @Author   子枫
     * @DateTime 2020-08-21
     * @Email    287851074@qq.com
     * @param    string           $title [description]
     * @param    string           $name  [description]
     * @param    string           $data  [description]
     * @return   [type]                  [description]
     */
     public function form_meditor($title='',$name='',$data=''){
        $tpl_id='zf_'.mt_rand().'_'.time();
        $zf_html = <<<INFO
            <div class="layui-card-header">
              <fieldset class="layui-elem-field layui-field-title site-title">
                <legend><a name="quickstart">$title</a></legend>
              </fieldset>
            </div>
            <div class="layui-card-body">
            <input name='editor_type' value='meditor' type='hidden' />
                <div id="$name">
                    <textarea name="$name" style="display: none;">$data</textarea>
                </div>
            </div>
 <script type="text/javascript">
var $name;
$(function() {
$name = editormd("$name", {
    width: "100%",
    height: 540,
    // autoHeight:true,
    path : "/vendor/wmc1125/tpfast-public/public/static/style/meditor/lib/",
    toolbarIcons : function() {
      // Or return editormd.toolbarModes[name]; // full, simple, mini
      // Using "||" set icons align right.
      return [
        "undo", "redo", "|", 
        "bold", "del", "italic", "quote", "ucwords", "uppercase", "lowercase", "|", 
        "h1", "h2", "h3", "h4", "h5", "h6", "|", 
        "list-ul", "list-ol", "hr", "|",
        "link", "reference-link", "image", "code", "preformatted-text", "code-block", "table", "datetime", "emoji", "html-entities", "pagebreak", "|",
        "goto-line", "watch", "clear", "search", "|",
        "zfFullscreen"
        ]
    },
    toolbarIconsClass : {
      zfFullscreen : "fa-arrows-alt"  // 指定一个FontAawsome的图标类
    },
    // 自定义工具栏按钮的事件处理
    toolbarHandlers : {
        zfFullscreen : function(cm, icon, cursor, selection) {
            var docElm = document.documentElement;  
            //W3C   
            if (docElm.requestFullscreen) {  
                docElm.requestFullscreen();  
            }  
                //FireFox   
            else if (docElm.mozRequestFullScreen) {  
                docElm.mozRequestFullScreen();  
            }  
                //Chrome等   
            else if (docElm.webkitRequestFullScreen) {  
                docElm.webkitRequestFullScreen();  
            }  
                //IE11   
            else if (elem.msRequestFullscreen) {  
                elem.msRequestFullscreen();  
            }  
        },
    },
    theme : "default",
    previewTheme : "default",
    editorTheme : "default",
    codeFold : true,
    saveHTMLToTextarea : true,   
    searchReplace : true,
    htmlDecode : "style,script,iframe|on*",           
    emoji : true,
    taskList : true,
    tocm            : true,        
    tex : true,                  
    flowChart : true,            
    sequenceDiagram : true,      

    imageUpload : true,
    imageFormats : ["jpg", "jpeg", "gif", "png", "bmp", "webp"],
    imageUploadURL : "$this->meditor_upload",
    onload : function() {
        console.log('onload', this);
    }
});
  
  $("#toc-menu-btn").click(function(){
      tes.config({
          tocDropdown   : true,
          tocTitle      : "目录 Table of Contents",
      });
  });
  
  $("#toc-default-btn").click(function() {
      tes.config("tocDropdown", false);
  });
});
</script>

INFO;
        return $zf_html;
    }

    public function form_vditor($title='',$name='',$data='',$vditor_num=1){
      $tpl_id='zf_'.mt_rand().'_'.time();
      $vditor_num = 'vditor_'.$vditor_num;
      $zf_html = <<<INFO
          <div class="layui-card-header">
            <fieldset class="layui-elem-field layui-field-title site-title">
              <legend><a name="quickstart">$title</a></legend>
            </fieldset>
          </div>
          <div class="layui-card-body">
              <div id="$tpl_id"></div>
              <textarea class="$tpl_id" name="$name" hidden >$data</textarea>
          </div>
<script>
const $vditor_num = new Vditor("$tpl_id", {
  after: () => {
    var md =  $vditor_num.html2md(`$data`);
    $vditor_num.setValue(md)
  },
  'windth':"70%",
  "height": 800,
  "placeholder":'请填写内容',
  "icon": "material",
  "lang": 'zh_CN',
  "cache": {
      "enable": false
  },
  "outline": {  // 显示大纲
       "enable": true,
       "position":'left'
  },
  'markdown': {
      'toc': true  // 展示目录
  },
  "preview": {
      "theme": {
          "current": "dark"
      },
  },
  mode: 'ir', //wysiwyg 所见即所得   //ir 及时渲染  //分屏sv
  theme: 'dark',  // 编辑器主题
  toolbar:[ 'undo' , 'redo'  , 'headings' , 'bold' , 'italic' , 'strike' , 'line' , 'quote' , 'list' , 'ordered-list' ,'check' ,'outdent' ,'indent' , 'code' , 'inline-code' , 'insert-after' , 'insert-before','upload' , 'link' , 'table' , 'record' , '|','edit-mode' , 'both'  , 'content-theme' ,'preview'  , 'help' ,{
      hotkey: '⌘-⇧-S',
      name:'sponsor',
      tipPosition: 's',
      tip: '关于作者',
      className: 'right',
      icon: '<svg t="1589994565028" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2808" width="32" height="32"><path d="M506.6 423.6m-29.8 0a29.8 29.8 0 1 0 59.6 0 29.8 29.8 0 1 0-59.6 0Z" fill="#0F0F0F" p-id="2809"></path><path d="M717.8 114.5c-83.5 0-158.4 65.4-211.2 122-52.7-56.6-127.7-122-211.2-122-159.5 0-273.9 129.3-273.9 288.9C21.5 562.9 429.3 913 506.6 913s485.1-350.1 485.1-509.7c0.1-159.5-114.4-288.8-273.9-288.8z" fill="#FAFCFB" p-id="2810"></path><path d="M506.6 926c-22 0-61-20.1-116-59.6-51.5-37-109.9-86.4-164.6-139-65.4-63-217.5-220.6-217.5-324 0-81.4 28.6-157.1 80.6-213.1 53.2-57.2 126.4-88.8 206.3-88.8 40 0 81.8 14.1 124.2 41.9 28.1 18.4 56.6 42.8 86.9 74.2 30.3-31.5 58.9-55.8 86.9-74.2 42.5-27.8 84.3-41.9 124.2-41.9 79.9 0 153.2 31.5 206.3 88.8 52 56 80.6 131.7 80.6 213.1 0 103.4-152.1 261-217.5 324-54.6 52.6-113.1 102-164.6 139-54.8 39.5-93.8 59.6-115.8 59.6zM295.4 127.5c-72.6 0-139.1 28.6-187.3 80.4-47.5 51.2-73.7 120.6-73.7 195.4 0 64.8 78.3 178.9 209.6 305.3 53.8 51.8 111.2 100.3 161.7 136.6 56.1 40.4 88.9 54.8 100.9 54.8s44.7-14.4 100.9-54.8c50.5-36.3 108-84.9 161.7-136.6 131.2-126.4 209.6-240.5 209.6-305.3 0-74.9-26.2-144.2-73.7-195.4-48.2-51.9-114.7-80.4-187.3-80.4-61.8 0-127.8 38.5-201.7 117.9-2.5 2.6-5.9 4.1-9.5 4.1s-7.1-1.5-9.5-4.1C423.2 166 357.2 127.5 295.4 127.5z" fill="#141414" p-id="2811"></path><path d="M353.9 415.6m-33.8 0a33.8 33.8 0 1 0 67.6 0 33.8 33.8 0 1 0-67.6 0Z" fill="#0F0F0F" p-id="2812"></path><path d="M659.3 415.6m-33.8 0a33.8 33.8 0 1 0 67.6 0 33.8 33.8 0 1 0-67.6 0Z" fill="#0F0F0F" p-id="2813"></path><path d="M411.6 538.5c0 52.3 42.8 95 95 95 52.3 0 95-42.8 95-95v-31.7h-190v31.7z" fill="#5B5143" p-id="2814"></path><path d="M506.6 646.5c-59.6 0-108-48.5-108-108v-31.7c0-7.2 5.8-13 13-13h190.1c7.2 0 13 5.8 13 13v31.7c0 59.5-48.5 108-108.1 108z m-82-126.7v18.7c0 45.2 36.8 82 82 82s82-36.8 82-82v-18.7h-164z" fill="#141414" p-id="2815"></path><path d="M450.4 578.9a54.7 27.5 0 1 0 109.4 0 54.7 27.5 0 1 0-109.4 0Z" fill="#EA64F9" p-id="2816"></path><path d="M256 502.7a32.1 27.5 0 1 0 64.2 0 32.1 27.5 0 1 0-64.2 0Z" fill="#EFAFF9" p-id="2817"></path><path d="M703.3 502.7a32.1 27.5 0 1 0 64.2 0 32.1 27.5 0 1 0-64.2 0Z" fill="#EFAFF9" p-id="2818"></path></svg>',
      click () {
              window.open("http://www.wangmingchang.com", "_blank");
      }
  }, '|','fullscreen', 'export'],
  upload: {
      // accept: 'image/*,.mp3, .wav, .rar',
      max: 10 * 1024 * 1024,
      token: 'test',
      fieldName:"file",
      url: "$this->upload_one_file",
      // linkToImgUrl: '',
      filename (name) {
          return name.replace(/[^(a-zA-Z0-9\u4e00-\u9fa5\.)]/g, '').
          replace(/[\?\\/:|<>\*\[\]\(\)\$%\{\}@~]/g, '').
          replace('/\\s/g', '')
      },
      //上传成功时执行
      success(editor, msg) {
          let responseData = JSON.parse(msg)
          console.log(responseData)
          if(responseData.result==1){
              let imageUrl = responseData.msg;
              let succFileText = "";
              if ($vditor_num && $vditor_num.vditor.currentMode === "wysiwyg") {
                  if (responseData.url == "图片") {
                      succFileText += `\\n <img alt=\${imageUrl} src="\${imageUrl}">`;
                  } else {
                      succFileText += `\\n <a href="\${imageUrl}">\${imageUrl} </a>`;
                  }
              }else{
                  if (responseData.url == "图片") {
                      succFileText += `\\n![\${imageUrl}](\${imageUrl})`;
                  } else {
                      succFileText += `\\n[\${imageUrl}](\${imageUrl})`;
                  }
              }
              //将图片路径写入文本
              document.execCommand("insertHTML", false, succFileText);
          }else{
              alert('上传失败');
          }
      },
      error() {
          alert('上传失败');
      },  
  },
  blur(e){
    $(".$tpl_id").val($vditor_num.getHTML())
  }
})

</script>


INFO;
      return $zf_html;
  }


//      public function form_layui_checkbox($title='',$name='',$list_str='',$id_t='id')
//     {
//         $tpl_id='zf_'.mt_rand().'_'.time();
//         $list = explode(',', $list_str);
//         $zf_html = '
//         <div class="layui-card-header">'.$title.'</div>
//             <div class="layui-card-body layui-row layui-col-space8 ">
//               <div class="layui-col-md12 layui-form-item">
//                 <div class="layui-input-inline" style="width: 100%">';
//                 foreach($list as $k=>$vo){
//                     $zf_html.= $vo.':<input type="checkbox" name="'.$name.'[]" lay-skin="primary" title="'.$vo.'" value="'.$vo.'" />';
//                  }
//                   $zf_html.='</div>
//               </div>
//             </div>
//             <script>
// layui.use(["form"], function(){
// var $ = layui.$
// ,form = layui.form
// ,element = layui.element;
// });
// </script>
//                   ';
//         return $zf_html;
//     }






    
   


   
}
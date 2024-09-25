<?php
namespace app\common\widget;
use think\Controller;
class FormPro {

   public function __construct (){
        $this->upload_one  = siteUrl('common/upload/upload_one');
        $this->upload_one_file  = siteUrl('common/upload/upload_one_file');
        $this->filesystem_upload  = siteUrl('common/Fileupload/upload').'?ttttt=1';
        // $this->filesystem_upload  = siteUrl('common/Fileupload/upload');
        $this->meditor_upload  = siteUrl('common/upload/meditor_upload_one');
        $this->zfcms_static = '/public/static/zfcms';
    }


    private function append_notes($str=''){
      if($str==''){
        return '';
      }
      return '<div style="font-size: 10px;color: #ccc;">'.$str.'</div>';
    }
    public function form_input($request_data=array())
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $type = isset($request_data['type'])?$request_data['type']:'text';
        $placeholder = isset($request_data['placeholder'])?$request_data['placeholder']:'';
        $readonly = isset($request_data['readonly'])?$request_data['readonly']:'';
        if($readonly!=''){
          $readonly = 'readonly=""';
        }
        $zf_html = '';
        $data = str_replace('"',"&quot;",$data);
        if($theme==1){
          $zf_html = <<<INFO
          <div class="layui-card-header">$title</div>
          <div class="layui-card-body layui-row layui-col-space8">
             <div class="layui-col-md12">
               <input  class="layui-input " type="$type" name="$name" $readonly  placeholder="$placeholder" autocomplete="off"  value="$data">
                $append_notes
              </div>
          </div>
INFO;
        }elseif($theme==2){
          $zf_html = <<<INFO
          <div class="layui-form-item">
          <label class="layui-form-label">$title:</label>
          <div class="layui-input-block">
            <input type="$type" name="$name" $readonly  placeholder="$placeholder" autocomplete="off" class="layui-input" value="$data">
            $append_notes
          </div>
        </div>
INFO;
        }
        
        return $zf_html;
    }
    public function form_input_tag($request_data=array())
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $type = isset($request_data['type'])?$request_data['type']:'text';
        $placeholder = isset($request_data['placeholder'])?$request_data['placeholder']:'';
        $zf_html = '';
        $static_dir = config('template.tpl_replace_string')['__STATIC__'];
        $data = str_replace('"',"&quot;",$data);
        $data_arr = explode(',',$data);
        // dd($data_arr);
        if($theme==1){
          $zf_html = '
          <div class="layui-card-header">'.$title.'</div>
          <div class="layui-card-body layui-row layui-col-space8">
             <div class="layui-col-md12 fairy-tag-container">
                <input type="text" class="fairy-tag-input" id="'.$tpl_id.'"  value="">
                '.$append_notes.'
             </div>
             <input  class="layui-input '.$tpl_id.'" type="hidden" name="'.$name.'"  placeholder="'.$placeholder.'" autocomplete="off"  value="'.$data.'">
          </div>';
          $zf_html .= '<script>
              layui.config({
                  base: "'.$static_dir.'/style/input-tag/"
              }).use(["inputTag", "jquery"], function () {
                  var $ = layui.jquery, inputTag = layui.inputTag;
                  inputTag.render({
                      elem: "#'.$tpl_id.'",
                      data: [';
                      foreach($data_arr as $k=>$vo){ 
                        if($data!=''){
                          if($k<count($data_arr)-1){
                            $zf_html .=  "'".$vo."',"; 
                          }else{
                            $zf_html .=  "'".$vo."'"; 
                          }
                        }
                      }
                      $zf_html.='],
                      beforeCreate: function (data, value){ 
                        return value; 
                      },
                      onChange: function (data, value, type) {
                        var _str = "";
                        for (let _index = 0; _index < data.length; _index++) {
                          if(_index<data.length-1){
                            _str += data[_index]+",";
                          }else{
                            _str += data[_index];
                          }
                        }
                        $(".'.$tpl_id.'").val(_str)
                      }
                  });
              })
          </script>';
        }elseif($theme==2){
          $zf_html = '
          <div class="layui-form-item">
            <label class="layui-form-label">'.$title.':</label>
            <div class="layui-input-block fairy-tag-container">
              <input type="text" class="fairy-tag-input" id="'.$tpl_id.'"  value="">
              '.$append_notes.'
            </div>
            <input  class="layui-input '.$tpl_id.'" type="hidden" name="'.$name.'"  placeholder="'.$placeholder.'" autocomplete="off"  value="'.$data.'">
          </div>';
          $zf_html .= '<script>
              layui.config({
                  base: "'.$static_dir.'/style/input-tag/"
              }).use(["inputTag", "jquery"], function () {
                  var $ = layui.jquery, inputTag = layui.inputTag;
                  inputTag.render({
                      elem: "#'.$tpl_id.'",
                      data: [';
                      foreach($data_arr as $k=>$vo){ 
                        if($data!=''){
                          if($k<count($data_arr)-1){
                            $zf_html .=  "'".$vo."',"; 
                          }else{
                            $zf_html .=  "'".$vo."'"; 
                          }
                        }
                      }
                      $zf_html.='],
                      beforeCreate: function (data, value){ 
                        return value; 
                      },
                      onChange: function (data, value, type) {
                        var _str = "";
                        for (let _index = 0; _index < data.length; _index++) {
                          if(_index<data.length-1){
                            _str += data[_index]+",";
                          }else{
                            _str += data[_index];
                          }
                        }
                        $(".'.$tpl_id.'").val(_str)
                      }
                  });
              })
          </script>';
        }
        
        return $zf_html;
    }
    public function form_input_color($request_data=array())
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $placeholder = isset($request_data['placeholder'])?$request_data['placeholder']:'';
        $zf_html='';
        if($theme==1){
          $zf_html = <<<INFO
          <div class="layui-card-header">$title</div>
         <div class="layui-card-body layui-row layui-col-space8">
             <div class="layui-col-md12">
               <input  class="layui-input $tpl_id" type="hidden" name="$name"  placeholder="$placeholder" autocomplete="off"  value="$data">
                 <div class="layui-upload">
                   <div  id="$tpl_id"></div>
                   $append_notes
                 </div> 
             </div>
         </div>
          <script>
          layui.use( function(){
              var colorpicker = layui.colorpicker;
              colorpicker.render({ 
                elem: '#$tpl_id',
                color: '$data', // hex
                alpha: true, // 开启透明度
                format: 'rgb',
                done: function(color){
                  $('.$tpl_id').val(color)
                }
              });
           });
         </script>
INFO;
        }elseif($theme==2){
          $zf_html = <<<INFO
          <div class="layui-form-item">
            <label class="layui-form-label">$title:</label>
            <div class="layui-input-block">
            <input  class="layui-input $tpl_id" type="hidden" name="$name"  placeholder="$placeholder" autocomplete="off"  value="$data">
                 <div class="layui-upload">
                   <div  id="$tpl_id"></div>
                   $append_notes
                 </div> 
            </div>
          </div>
          <script>
          layui.use( function(){
            var colorpicker = layui.colorpicker;
            colorpicker.render({ 
              elem: '#$tpl_id',
              color: '$data', // hex
              alpha: true, // 开启透明度
              format: 'rgb',
              done: function(color){
                $('.$tpl_id').val(color)
              }
            });
         });
         </script>
INFO;
        }
        
        return $zf_html;
    }
    public function form_note($request_data=array())
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $data = isset($request_data['data'])?$request_data['data']:'';
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $zf_html = '';
        if($theme==1){
          $zf_html = <<<INFO
          <div class="layui-card-body layui-row layui-col-space8">
             <div class="layui-col-md12">
               $data
             </div>
          </div>
INFO;
        }elseif($theme==2){
          $zf_html = <<<INFO
          <div class="layui-form-item">
          <label class="layui-form-label"></label>
          <div class="layui-input-block">
            $data
          </div>
        </div>
INFO;
        }elseif($theme==3){
          $zf_html = <<<INFO
          <label class="layui-form-label"></label>
          <div class="layui-input-block">
            $data
          </div>
INFO;
        }
        return $zf_html;
    }

    /**
ok   
     */
    public function form_textarea($request_data=array())
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $placeholder = isset($request_data['placeholder'])?$request_data['placeholder']:'';
        $readonly = isset($request_data['readonly'])?$request_data['readonly']:'';
        if($readonly!=''){
          $readonly = 'readonly=""';
        }
        $zf_html = '';
        if($theme==1){
        $zf_html = <<<INFO
 <div class="layui-card-header">$title</div>
<div class="layui-card-body layui-row layui-col-space8">
    <div class="layui-col-md12">
      <textarea name="$name"  $readonly placeholder="$placeholder" class="layui-textarea">{$data}</textarea>
      $append_notes
    </div>
</div>
INFO;
      }elseif($theme==2){
        $zf_html = <<<INFO
        <div class="layui-form-item">
        <label class="layui-form-label">$title:</label>
        <div class="layui-input-block">
          <textarea name="$name"  $readonly placeholder="$placeholder" class="layui-textarea">{$data}</textarea>
          $append_notes
        </div>
      </div>
INFO;
      }

        return $zf_html;
    }

 
    public function form_radio($request_data=array())
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $parm_data = isset($request_data['parm_data'])?$request_data['parm_data']:['0'=>'否','1'=>'是'];
        if(is_string($parm_data)){
          eval("\$parm_data = ".$request_data['parm_data'].'; ');
        }
        $zf_html='';
        if($theme==1){
            $zf_html = '
            <div class="layui-card-header">'.$title.'</div>
            <div class="layui-card-body layui-row layui-col-space8 ">
                <div class="layui-col-md12 layui-form-item">
                  <div class="layui-form" >';
                  foreach($parm_data as $k=>$vo){
                    $zf_html.='<input type="radio" name="'.$name.'" value="'.$k.'" title="'.$vo.'" '.($data==$k?'checked':'').'  ><div class="layui-unselect layui-form-radio"><div>'.$vo.'</div></div>';
                  }
            $zf_html.='</div>
            '.$append_notes.'
                </div>
              </div>';
        }elseif($theme==2){
            $zf_html = '
            <div class="layui-form-item">
              <label class="layui-form-label">'.$title.':</label>
              <div class="layui-input-block layui-form">';
                  foreach($parm_data as $k=>$vo){
                    $zf_html.='<input type="radio" name="'.$name.'" value="'.$k.'" title="'.$vo.'" '.($data==$k?'checked':'').'  ><div class="layui-unselect layui-form-radio"><div>'.$vo.'</div></div>';
                  }
                
          $zf_html.='</div>
          '.$append_notes.'
            </div>';
        }elseif($theme==3){
          $zf_html = '
          <div class="layui-card-header">'.$title.'</div>
          <div class="layui-card-body layui-row layui-col-space8 ">
              <div class="layui-col-md12 layui-form-item">
                <div class="layui-input-inline " >';
                foreach($parm_data as $k=>$vo){
                  $zf_html.='<input type="radio" name="'.$name.'" value="'.$vo.'" title="'.$vo.'" '.($data==$vo?'checked':'').'  ><div class="layui-unselect layui-form-radio"><div>'.$vo.'</div></div>';
                }
          $zf_html.='</div>
          '.$append_notes.'
              </div>
            </div>';
      }elseif($theme==4){
          $zf_html = '
          <div class="layui-form-item">
            <label class="layui-form-label">'.$title.':</label>
            <div class="layui-input-block ">';
                foreach($parm_data as $k=>$vo){
                  $zf_html.='<input type="radio" name="'.$name.'" value="'.$k.'" title="'.$vo.'" '.($data==$k?'checked':'').'  ><div class="layui-unselect layui-form-radio"><div>'.$vo.'</div></div>';
                }
              
        $zf_html.='</div>
        '.$append_notes.'
          </div>';
      }else if($theme==5){
        $zf_html = '
        <div class="layui-card-header">'.$title.'</div>
        <div class="layui-card-body layui-row layui-col-space8 ">
            <div class="layui-col-md12 layui-form-item">
              <div class="layui-form" >';
              foreach($parm_data as $k=>$vo){
                $zf_html.='<input type="radio" name="'.$name.'" value="'.$vo.'" title="'.$vo.'" '.($data==$vo?'checked':'').'  ><div class="layui-unselect layui-form-radio"><div>'.$vo.'</div></div>';
              }
        $zf_html.='</div>
        '.$append_notes.'
            </div>
          </div>';
    }elseif($theme==6){
        $zf_html = '
        <div class="layui-form-item">
          <label class="layui-form-label">'.$title.':</label>
          <div class="layui-input-block layui-form">';
              foreach($parm_data as $k=>$vo){
                $zf_html.='<input type="radio" name="'.$name.'" value="'.$vo.'" title="'.$vo.'" '.($data==$vo?'checked':'').'  ><div class="layui-unselect layui-form-radio"><div>'.$vo.'</div></div>';
              }
            
      $zf_html.='</div>
      '.$append_notes.'
        </div>';
    }
       
        return $zf_html;
    }
    public function layui_switch($request_data=array()){
      $tpl_id='zf_'.mt_rand().'_'.time();
      $title = isset($request_data['title'])?$request_data['title']:'';
      $name = isset($request_data['name'])?$request_data['name']:'';
      $data = isset($request_data['data'])?$request_data['data']:'';
      $theme = isset($request_data['theme'])?$request_data['theme']:'1';
      $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
      $parm_data = isset($request_data['parm_data'])?$request_data['parm_data']:['是','否'];
      $parm_data_arr = implode('|', $parm_data);
      $zf_html='';
      if($theme==1){
          $zf_html = '<div class="layui-card-header">'.$title.'</div>
          <div class="layui-card-body layui-row layui-col-space8 ">
            <div class="layui-col-md12 layui-form-item">
              <div class="layui-input-inline layui-form">';
              $zf_html .=   '<input type="checkbox" name="'.$name.'" lay-skin="switch" lay-text="'.$parm_data_arr.'" ';
              if($data==1){
                $zf_html .='checked';
              }
              $zf_html .='  lay-filter="'.$name.'_change"   >';
              $zf_html .= '</div>
              '.$append_notes.'
            </div>
          </div>';
      }elseif($theme==2){
          $zf_html = '<div class="layui-form-item">
            <label class="layui-form-label">'.$title.':</label>
            <div class="layui-input-block layui-form">';
              $zf_html .=   '<input type="checkbox" name="'.$name.'" lay-skin="switch" lay-text="'.$parm_data_arr.'" ';
              if($data==1){
                $zf_html .='checked';
              }
              $zf_html .='  lay-filter="'.$name.'_change"   >';
          $zf_html.='</div>
          '.$append_notes.'
          </div>';
      }
      $zf_html.='<script>
      layui.use(["form","element"], function(){
      var $ = layui.$
      ,form = layui.form
      ,element = layui.element;
        form.on("switch('.$name.'_change)", function(data){
          $(data.othis).siblings("input").remove();
          $(data.othis).after("<input type=\'hidden\' name="+ $(data.elem).attr("name") +" value="+ (data.elem.checked ? "1" : "0") +">");
        });
      });
      </script>';
      return $zf_html;

    }
    public function form_checkbox($request_data=array())
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $id_t = isset($request_data['id_t'])?$request_data['id_t']:'id';
        $name_t = isset($request_data['name_t'])?$request_data['name_t']:'name';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $list = isset($request_data['list_arr'])?$request_data['list_arr']:'';
        if(is_string($list)){
          eval("\$list = ".$request_data['list_arr'].'; ');
        }
        
        $zf_html='';
        $check_data = explode(',', $data);
        if($theme==1){
          $zf_html = '
          <div class="layui-card-header">'.$title.'</div>
              <div class="layui-card-body layui-row layui-col-space8 ">
                <div class="layui-col-md12 layui-form-item layui-form">
                  <div class="layui-input-inline">';
                  foreach($list as $k=>$vo){
                      $zf_html.= '<input type="checkbox" name="zf_list_'.$name.'[]" lay-skin="primary" title="'.$vo[$name_t].'" value="'.$vo[$id_t].'" ';
                      if(in_array($vo[$id_t],$check_data)){
                        $zf_html.=  'checked';
                      }
                      $zf_html.= ' />';
                  }
                    $zf_html.='</div>
              '.$append_notes.'
                </div>
                <input name="_temp_arr_key[]"  type="hidden" value="'.$name.'" />
              </div>';
        }elseif($theme==2){
          $zf_html = '
          <div class="layui-form-item">
            <label class="layui-form-label">'.$title.':</label>
            <div class="layui-input-block layui-form">';
              foreach($list as $k=>$vo){
                $zf_html.= '<input type="checkbox" name="zf_list_'.$name.'[]" lay-skin="primary" title="'.$vo[$name_t].'" value="'.$vo[$id_t].'" ';
                if(in_array($vo[$id_t],$check_data)){
                  $zf_html.=  'checked';
                }
                $zf_html.= ' />';
              }
          $zf_html.='</div>
          '.$append_notes.'
          </div>
          <input name="_temp_arr_key[]"  type="hidden" value="'.$name.'" />
          ';
        }
        return $zf_html;
    }

    public function upload_album($request_data=array())
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $_name = $name;
        $name = $name.'[]';

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
                    $zf_html .='<div class="layui-col-sm2" style="layui-col-sm2" style="border:0px solid #ccc;height:150px; display: flex; flex-direction: column; align-items: center; justify-content: center;"><div style="width: 100%; height: 80%; display: flex; align-items: center; justify-content: center;"> <img src="'.$pics[$i].'" class="layui-upload-img"  style="max-width:100%; max-height:100%;" > </div>
                      <input type="hidden" name="zf_list_'.$name.'" value="'.$pics[$i].'" /><span style="cursor: pointer;" onclick="deleteFile(this)">删除</span>
                      </div>';
                  }
              $zf_html .='</div>
              '.$append_notes.'
          </blockquote>
          <input name="_temp_arr_key[]"  type="hidden" value="'.$_name.'" />
          </div> 
    </div>
</div>';
$zf_html .=<<<INFO
 <script>
 layui.use(['upload'], function(){
    var $ = layui.$
    ,element = layui.element
    ,upload = layui.upload;
    
     upload.render({
      elem: '#$tpl_id'
      ,url: "$this->upload_one"
      ,multiple: true
      ,before: function(obj){
        var index = layer.load();
      }
      ,error: function(index, upload){
                  setTimeout(function(){
                    layer.closeAll();
                  }, 2000);
                }
      ,done: function(res){
        layer.closeAll(); 
        if(res.result==1){
            layer.msg('上传成功', {icon: 1});
            console.log('---start---')
            $('.$tpl_id').append('<div class="layui-col-sm2" style="layui-col-sm2" style="border:0px solid #ccc;height:150px; display: flex; flex-direction: column; align-items: center; justify-content: center;"><div style="width: 100%; height: 80%; display: flex; align-items: center; justify-content: center;"> <img src="'+ res.msg +'" class="layui-upload-img"  style="max-width:100%; max-height:100%;" ></div><input type="hidden" name="zf_list_$name" value="'+ res.msg +'" /><span style="cursor: pointer;" onclick="deleteFile(this)">删除</span></div> ')
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
    
    public function upload_album_title($request_data=array())
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $data_title = isset($request_data['data_title'])?$request_data['data_title']:'';
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $_name = $name;
        $name = $name.'[]';
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
                    $zf_html .='<div class="layui-col-sm2" style="border:0px solid #ccc;height:150px; display: flex; flex-direction: column; align-items: center; justify-content: center;"><div style="width: 100%; height: 70%; display: flex; align-items: center; justify-content: center;"> <img src="'.$pics[$i].'" class="layui-upload-img"  style="max-width:100%; max-height:100%;" /></div>
                      <input type="hidden" name="zf_list_'.$name.'" value="'.$pics[$i].'" />
                      <input type="text" class="layui-input" name="zf_list_title_'.$name.'" value="'.(isset($titles[$i])?$titles[$i]:'').'" />
                      <span style="cursor: pointer;" onclick="deleteFile(this)">删除</span></div>';
                  }
              $zf_html .='</div>
              '.$append_notes.'
          </blockquote>
          <input name="_temp_arr_key[]"  type="hidden" value="'.$_name.'" />
        </div> 
    </div>
</div>';
$zf_html .=<<<INFO
 <script>
 layui.use(['upload'], function(){
    var $ = layui.$
    ,element = layui.element
    ,upload = layui.upload;
    
     upload.render({
      elem: '#$tpl_id'
      ,url: "$this->upload_one"
      ,multiple: true
      ,before: function(obj){
        var index = layer.load();
      }
      ,error: function(index, upload){
                  setTimeout(function(){
                    layer.closeAll();
                  }, 2000);
                }
      ,done: function(res){
        layer.closeAll();  
        if(res.result==1){
            layer.msg('上传成功', {icon: 1});
            console.log('---start---')
            $('.$tpl_id').append('<div class="layui-col-sm2" style="border:0px solid #ccc;height:150px; display: flex; flex-direction: column; align-items: center; justify-content: center;"><div style="width: 100%; height: 80%; display: flex; align-items: center; justify-content: center;"> <img src="'+ res.msg +'" class="layui-upload-img"  style="max-width:100%; max-height:100%;" ></div><input type="hidden" name="zf_list_$name" value="'+ res.msg +'" /><input type="text"  class="layui-input" name="zf_list_title_$name" value="" /><span style="cursor: pointer;" onclick="deleteFile(this)">删除</span></div> </div>')
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



    public function filesystem_album($request_data=array())
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $_name = $name;
        $name = $name.'[]';
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
                  $zf_html .='<div class="layui-col-sm2" style="border:0px solid #ccc;height:150px; display: flex; flex-direction: column; align-items: center; justify-content: center;"><div style="width: 100%; height: 80%; display: flex; align-items: center; justify-content: center;"> <img src="'.$pics[$i].'" class="layui-upload-img"  style="max-width:100%; max-height:100%;" > </div>
                    <input type="hidden" name="zf_list_'.$name.'" value="'.$pics[$i].'" /><span style="cursor: pointer;" onclick="deleteFile(this)">删除</span>
                    </div>';
                }
              $zf_html .='</div>
              '.$append_notes.'
          </blockquote>
          <input name="_temp_arr_key[]"  type="hidden" value="'.$_name.'" />
        </div> 
    </div>
</div>';
$zf_html .=<<<INFO
 <script>
 layui.use(['upload'], function(){
    var $ = layui.$
    ,element = layui.element
    ,upload = layui.upload;

    $('#$tpl_id').on('click',function(){
      layer.open({
        type: 2,
        area: ['90%', '90%'],
        fixed: true,
        maxmin: true,
        content: "$this->filesystem_upload&cid=0&t=2&name=$name&zf_class=.$tpl_id"
      });
    })
  });
</script>
INFO;
        return $zf_html;
    }


    public function form_time($request_data=array())
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $time_type = isset($request_data['time_type'])?$request_data['time_type']:'datetime';
        $placeholder = isset($request_data['placeholder'])?$request_data['placeholder']:'';

        if($theme==1){
        $zf_html = <<<INFO
 <div class="layui-card-header">$title</div>
<div class="layui-card-body layui-row layui-col-space8">
    <div class="layui-col-md12">
      <input type="text" name="$name" id="$name"  placeholder="$placeholder" autocomplete="off" class="layui-input" lay-key="1" value="{$data}">
      $append_notes
    </div>
</div>
 <script>
    laydate.render({
      elem: "#$name"
      ,type: "$time_type"
    });
</script>
INFO;
        }else{
          $zf_html = <<<INFO
          <div class="layui-form-item">
          <label class="layui-form-label">$title:</label>
          <div class="layui-input-block">
          <input type="text" name="$name" id="$tpl_id"  placeholder="$placeholder" autocomplete="off" class="layui-input" lay-key="1" value="{$data}">
      $append_notes
          </div>
        </div>
<script>
    laydate.render({
      elem: "#$tpl_id"
      ,type: "$time_type"
    });
</script>
INFO;
        }
        return $zf_html;
    }

    public function upload_pic($request_data=array())
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $placeholder = isset($request_data['placeholder'])?$request_data['placeholder']:'';
        $zf_html='';
        if($theme==1){
          $zf_html = <<<INFO
          <div class="layui-card-header">$title</div>
         <div class="layui-card-body layui-row layui-col-space8">
             <div class="layui-col-md12">
               <input  class="layui-input $tpl_id" type="text" name="$name"  placeholder="$placeholder" autocomplete="off"  value="$data">
               $append_notes
                 <div class="layui-upload">
                   <button type="button" class="layui-btn" id="$tpl_id">上传</button>
                   <div class="layui-upload-list">
                     <img class="layui-upload-img $tpl_id" style="height: auto;width: 200px;" src="$data" >
                     <p id="demoText"></p>
                   </div>
                 </div> 
             </div>
         </div>
          <script>
          layui.use(['upload'], function(){
             var $ = layui.$
             ,element = layui.element
             ,upload = layui.upload;
             upload.render({
               elem: '#$tpl_id'
               ,url: "$this->upload_one"
               ,before: function(obj){
                var index = layer.load();
                }
                ,error: function(index, upload){
                  setTimeout(function(){
                    layer.closeAll();
                  }, 2000);
                }
               ,done: function(res){
                layer.closeAll();  
                 if(res.result==1){
                     layer.msg("上传成功", {icon: 1});
                     $('.$tpl_id').val(res.msg);
                     $('.$tpl_id').attr('src', res.msg);
                 }else{
                   layer.msg(res.msg, {icon: 2});
                 }
               }
             });
           });
         </script>
INFO;
        }elseif($theme==2){
          $zf_html = <<<INFO
          <div class="layui-card-header">$title</div>
         <div class="layui-card-body layui-row layui-col-space8">
             <div class="layui-col-md12">
               <input  class="layui-input" type="text" name="$name"  placeholder="$placeholder" autocomplete="off"  value="$data">
               $append_notes
                 <div class="layui-upload">
                   <button type="button" class="layui-btn" id="$tpl_id">上传</button>
                 </div> 
             </div>
         </div>
          <script>
          layui.use(['upload'], function(){
             var $ = layui.$
             ,element = layui.element
             ,upload = layui.upload;
             upload.render({
               elem: '#$tpl_id'
               ,url: "$this->upload_one"
               ,before: function(obj){
                var index = layer.load();
                }
                ,error: function(index, upload){
                  setTimeout(function(){
                    layer.closeAll();
                  }, 2000);
                }
               ,done: function(res){
                layer.closeAll();  
                 if(res.result==1){
                     layer.msg("上传成功", {icon: 1});
                     $('.$tpl_id').val(res.msg);
                     $('.$tpl_id').attr('src', res.msg);
                 }else{
                   layer.msg(res.msg, {icon: 2});
                 }
               }
             });
           });
         </script>
INFO;
        }elseif($theme==3){
          $zf_html = <<<INFO
          <div class="layui-form-item">
            <label class="layui-form-label">$title:</label>
            <div class="layui-input-block">
            <input  class="layui-input" type="text" name="$name"  placeholder="$placeholder" autocomplete="off"  value="$data">
            $append_notes
                 <div class="layui-upload">
                   <button type="button" class="layui-btn" id="$tpl_id">上传</button>
                   <div class="layui-upload-list">
                     <img class="layui-upload-img $tpl_id" style="height: auto;width: 200px;" src="$data" >
                     <p id="demoText"></p>
                   </div>
                 </div> 
            </div>
          </div>
          <script>
          layui.use(['upload'], function(){
             var $ = layui.$
             ,element = layui.element
             ,upload = layui.upload;
             upload.render({
               elem: '#$tpl_id'
               ,url: "$this->upload_one"
               ,before: function(obj){
                var index = layer.load();
                }
                ,error: function(index, upload){
                  setTimeout(function(){
                    layer.closeAll();
                  }, 2000);
                }
               ,done: function(res){
                layer.closeAll();  
                 if(res.result==1){
                     layer.msg("上传成功", {icon: 1});
                     $('.$tpl_id').val(res.msg);
                     $('.$tpl_id').attr('src', res.msg);
                 }else{
                   layer.msg(res.msg, {icon: 2});
                 }
               }
             });
           });
         </script>
INFO;
        }
        
        return $zf_html;
    }
    public function upload_dragpic($request_data=array())
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $placeholder = isset($request_data['placeholder'])?$request_data['placeholder']:'';
        $zf_html='';
        if($theme==1){
          $zf_html = <<<INFO
          <div class="layui-card-header">$title</div>
         <div class="layui-card-body layui-row layui-col-space8">
             <div class="layui-col-md12">
                <div class="layui-upload-drag" style="display: block;" id="$tpl_id">
                  <i class="layui-icon layui-icon-upload"></i> 
                  <div>点击上传，或将文件拖拽到此处</div>
                  <div class="layui-hide_" >
                    <hr> <img class="$tpl_id" src="$data"  style="max-width: 100%">
                  </div>
                </div>
                <input  class="layui-input $tpl_id" type="text" name="$name"  placeholder="$placeholder" autocomplete="off"  value="$data">
               $append_notes
             </div>
         </div>
          <script>
          layui.use(['upload'], function(){
             var $ = layui.$
             ,element = layui.element
             ,upload = layui.upload;
             upload.render({
               elem: '#$tpl_id'
               ,url: "$this->upload_one"
               ,before: function(obj){
                var index = layer.load();
                }
                ,error: function(index, upload){
                  setTimeout(function(){
                    layer.closeAll();
                  }, 2000);
                }
               ,done: function(res){
                layer.closeAll();  
                 if(res.result==1){
                     layer.msg("上传成功", {icon: 1});
                     $('.$tpl_id').val(res.msg);
                     $('.$tpl_id').attr('src', res.msg);
                 }else{
                   layer.msg(res.msg, {icon: 2});
                 }
               }
             });
           });
         </script>
INFO;
        }elseif($theme==2){
          $zf_html = <<<INFO
          <div class="layui-card-header">$title</div>
         <div class="layui-card-body layui-row layui-col-space8">
             <div class="layui-col-md12">
                <div class="layui-upload-drag" style="display: block;" id="$tpl_id">
                  <i class="layui-icon layui-icon-upload"></i> 
                  <div>点击上传，或将文件拖拽到此处</div>
                  <div class="layui-hide_" >
                    <hr> <img class="$tpl_id" src="$data"  style="max-width: 100%">
                  </div>
                </div>
                <input  class="layui-input" type="hidden" name="$name"  placeholder="$placeholder" autocomplete="off"  value="$data">
               $append_notes
             </div>
         </div>
          <script>
          layui.use(['upload'], function(){
             var $ = layui.$
             ,element = layui.element
             ,upload = layui.upload;
             upload.render({
               elem: '#$tpl_id'
               ,url: "$this->upload_one"
               ,before: function(obj){
                var index = layer.load();
                }
                ,error: function(index, upload){
                  setTimeout(function(){
                    layer.closeAll();
                  }, 2000);
                }
               ,done: function(res){
                layer.closeAll();  
                 if(res.result==1){
                     layer.msg("上传成功", {icon: 1});
                     $('.$tpl_id').val(res.msg);
                     $('.$tpl_id').attr('src', res.msg);
                 }else{
                   layer.msg(res.msg, {icon: 2});
                 }
               }
             });
           });
         </script>
INFO;
        }elseif($theme==3){
          $zf_html = <<<INFO
          <div class="layui-form-item">
            <label class="layui-form-label">$title:</label>
            <div class="layui-input-block">
                <div class="layui-upload-drag" style="display: block;" id="$tpl_id">
                  <i class="layui-icon layui-icon-upload"></i> 
                  <div>点击上传，或将文件拖拽到此处</div>
                  <div class="layui-hide_" >
                    <hr> <img class="$tpl_id" src="$data"  style="max-width: 100%">
                  </div>
                </div>
                <input  class="layui-input" type="text" name="$name"  placeholder="$placeholder" autocomplete="off"  value="$data">
               $append_notes
            </div>
          </div>
          <script>
          layui.use(['upload'], function(){
             var $ = layui.$
             ,element = layui.element
             ,upload = layui.upload;
             upload.render({
               elem: '#$tpl_id'
               ,url: "$this->upload_one"
               ,before: function(obj){
                var index = layer.load();
                }
                ,error: function(index, upload){
                  setTimeout(function(){
                    layer.closeAll();
                  }, 2000);
                }
               ,done: function(res){
                layer.closeAll();  
                 if(res.result==1){
                     layer.msg("上传成功", {icon: 1});
                     $('.$tpl_id').val(res.msg);
                     $('.$tpl_id').attr('src', res.msg);
                 }else{
                   layer.msg(res.msg, {icon: 2});
                 }
               }
             });
           });
         </script>
INFO;
        }elseif($theme==4){
          $zf_html = <<<INFO
          <div class="layui-form-item">
            <label class="layui-form-label">$title:</label>
            <div class="layui-input-block">
                <div class="layui-upload-drag" style="display: block;" id="$tpl_id">
                  <i class="layui-icon layui-icon-upload"></i> 
                  <div>点击上传，或将文件拖拽到此处</div>
                  <div class="layui-hide_" >
                    <hr> <img class="$tpl_id" src="$data"  style="max-width: 100%">
                  </div>
                </div>
                <input  class="layui-input" type="hidden" name="$name"  placeholder="$placeholder" autocomplete="off"  value="$data">
               $append_notes
            </div>
          </div>
          <script>
          layui.use(['upload'], function(){
            var $ = layui.$
            ,element = layui.element
            ,upload = layui.upload;
            upload.render({
              elem: '#$tpl_id'
              ,url: "$this->upload_one"
              ,before: function(obj){
                var index = layer.load();
                }
                ,error: function(index, upload){
                  setTimeout(function(){
                    layer.closeAll();
                  }, 2000);
                }
              ,done: function(res){
                layer.closeAll();  
                if(res.result==1){
                    layer.msg("上传成功", {icon: 1});
                    $('.$tpl_id').val(res.msg);
                    $('.$tpl_id').attr('src', res.msg);
                }else{
                  layer.msg(res.msg, {icon: 2});
                }
              }
            });
          });
        </script>
INFO;
        }
        return $zf_html;
    }

    public function filesystem_pic($request_data=array())
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $placeholder = isset($request_data['placeholder'])?$request_data['placeholder']:'';
        $zf_html ='';
        if($theme==1){
          $zf_html = <<<INFO
          <div class="layui-card-header">$title</div>
         <div class="layui-card-body layui-row layui-col-space8">
             <div class="layui-col-md12">
               <input  class="layui-input $tpl_id" type="text" name="$name"  placeholder="$placeholder" autocomplete="off"  value="$data">
               $append_notes
                 <div class="layui-upload">
                   <button type="button" class="layui-btn" id="$tpl_id">上传</button>
                   <div class="layui-upload-list">
                     <img class="layui-upload-img $tpl_id" style="height:auto;width:200px;" src="$data" >
                     <p id="demoText"></p>
                   </div>
                 </div> 
             </div>
         </div>
          <script>
          layui.use(['upload'], function(){
             var $ = layui.$
             ,element = layui.element
             ,upload = layui.upload;
             $('#$tpl_id').on('click',function(){
               layer.open({
                 type: 2,
                 area: ['90%', '90%'],
                 fixed: true,
                 maxmin: true,
                 content: "$this->filesystem_upload&cid=0&t=1&zf_class=.$tpl_id"
               });
             })
           });
         </script>
INFO;
        }elseif($theme==2){
          $zf_html = <<<INFO
          <div class="layui-card-header">$title</div>
         <div class="layui-card-body layui-row layui-col-space8">
             <div class="layui-col-md12">
               <input  class="layui-input $tpl_id" type="text" name="$name"  placeholder="$placeholder" autocomplete="off"  value="$data">
               $append_notes
                 <div class="layui-upload">
                   <button type="button" class="layui-btn" id="$tpl_id">上传</button>
                 </div> 
             </div>
         </div>
          <script>
          layui.use(['upload'], function(){
             var $ = layui.$
             ,element = layui.element
             ,upload = layui.upload;
             $('#$tpl_id').on('click',function(){
               layer.open({
                 type: 2,
                 area: ['90%', '90%'],
                 fixed: true,
                 maxmin: true,
                 content: "$this->filesystem_upload&cid=0&t=1&zf_class=.$tpl_id"
               });
             })
           });
         </script>
INFO;
        }elseif($theme==3){
          $zf_html = <<<INFO
          <div class="layui-form-item">
            <label class="layui-form-label">$title:</label>
            <div class="layui-input-block">
              <input  class="layui-input $tpl_id" type="text" name="$name"  placeholder="$placeholder" autocomplete="off"  value="$data">
              $append_notes
              <div class="layui-upload">
                <button type="button" class="layui-btn" id="$tpl_id">上传</button>
                <div class="layui-upload-list">
                  <img class="layui-upload-img $tpl_id" style="height:auto;width:200px;" src="$data" >
                  <p id="demoText"></p>
                </div>
              </div> 
            </div>
          </div>
          <script>
          layui.use(['upload'], function(){
             var $ = layui.$
             ,element = layui.element
             ,upload = layui.upload;
             $('#$tpl_id').on('click',function(){
               layer.open({
                 type: 2,
                 area: ['90%', '90%'],
                 fixed: true,
                 maxmin: true,
                 content: "$this->filesystem_upload&cid=0&t=1&zf_class=.$tpl_id"
               });
             })
           });
         </script>
INFO;
        }elseif($theme==4){
          $zf_html = <<<INFO
          <div class="layui-form-item">
            <label class="layui-form-label">$title:</label>
            <div class="layui-input-block">
              <input  class="layui-input $tpl_id" type="text" name="$name"  placeholder="$placeholder" autocomplete="off"  value="$data">
              $append_notes
              <div class="layui-upload">
                <button type="button" class="layui-btn" id="$tpl_id">上传</button>
              </div> 
            </div>
          </div>
          <script>
          layui.use(['upload'], function(){
             var $ = layui.$
             ,element = layui.element
             ,upload = layui.upload;
             $('#$tpl_id').on('click',function(){
               layer.open({
                 type: 2,
                 area: ['90%', '90%'],
                 fixed: true,
                 maxmin: true,
                 content: "$this->filesystem_upload&cid=0&t=1&zf_class=.$tpl_id"
               });
             })
           });
         </script>
INFO;
        }
       
        return $zf_html;
    }

    public function upload_file($request_data=array())
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $placeholder = isset($request_data['placeholder'])?$request_data['placeholder']:'';
        $zf_html = <<<INFO
 <div class="layui-card-header">$title</div>
<div class="layui-card-body layui-row layui-col-space8">
    <div class="layui-col-md12">
      <input  class="layui-input $tpl_id" type="text" name="$name"  placeholder="$placeholder" autocomplete="off"  value="$data">
    </div>
    $append_notes
    <span type="button" class="layui-btn layui-btn-sm" id="$tpl_id">上传文件</span>
</div>
 <script>
 layui.use(['upload'], function(){
    var $ = layui.$
    ,element = layui.element
    ,upload = layui.upload;
    
    upload.render({
      elem: '#$tpl_id'
      ,url: "$this->upload_one_file"
      ,accept: 'file'
      ,before: function(obj){
        var index = layer.load();
      }
      ,error: function(index, upload){
                  setTimeout(function(){
                    layer.closeAll();
                  }, 2000);
                }
      ,done: function(res){
        layer.closeAll();  
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


    
    public function filesystem_file($request_data=array())
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $placeholder = isset($request_data['placeholder'])?$request_data['placeholder']:'';
        if($theme==1){
          $zf_html = <<<INFO
  <div class="layui-card-header">$title</div>
  <div class="layui-card-body layui-row layui-col-space8">
      <div class="layui-col-md12">
        <input  class="layui-input $tpl_id" type="text" name="$name"  placeholder="$placeholder" autocomplete="off"  value="$data">
      $append_notes
          <div class="layui-upload">
            <button type="button" class="layui-btn" id="$tpl_id">选择文件</button>
          </div> 
      </div>
  </div>
  <script>
  layui.use(['upload'], function(){
      var $ = layui.$
      ,element = layui.element
      ,upload = layui.upload;
      
      $('#$tpl_id').on('click',function(){
        layer.open({
          type: 2,
          area: ['90%', '90%'],
          fixed: true,
          maxmin: true,
          content: "$this->filesystem_upload&cid=0&t=3&zf_class=.$tpl_id"
        });
      })


    });
  </script>
INFO;
        }elseif($theme==2){
          $zf_html = <<<INFO
          <div class="layui-form-item">
            <label class="layui-form-label">$title:</label>
            <div class="layui-input-block">
              <input  class="layui-input $tpl_id" type="text" name="$name"  placeholder="$placeholder" autocomplete="off"  value="$data">
              $append_notes
              <div class="layui-upload">
                <button type="button" class="layui-btn" id="$tpl_id">上传</button>
              </div> 
            </div>
          </div>
          <script>
          layui.use(['upload'], function(){
             var $ = layui.$
             ,element = layui.element
             ,upload = layui.upload;
             $('#$tpl_id').on('click',function(){
               layer.open({
                 type: 2,
                 area: ['90%', '90%'],
                 fixed: true,
                 maxmin: true,
                 content: "$this->filesystem_upload&cid=0&t=1&zf_class=.$tpl_id"
               });
             })
           });
         </script>
INFO;
      }

        return $zf_html;
    }


    public function form_select($request_data=array())
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $id_t = isset($request_data['id_t'])?$request_data['id_t']:'id';
        $name_t = isset($request_data['name_t'])?$request_data['name_t']:'name';
        $list = isset($request_data['list_arr'])?$request_data['list_arr']:[];
        if(is_string($list)){
          eval("\$list = ".$request_data['list_arr'].'; ');
        }
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $zf_html='';
        if($theme==1){
          $zf_html = '
        <div class="layui-card-header">'.$title.'</div>
                  <div class="layui-card-body layui-row layui-col-space10">
                     <select lay-filter="'.$name.'" class="layui-input" name="'.$name.'" >';
                      foreach($list as $k=>$vo){
                           $zf_html .= '<option value="'.$vo[$id_t].'" ';
                           if($data==$vo[$id_t]){
                            $zf_html.='selected';
                           }
                            $zf_html.='> ┃'.str_repeat('━━', substr_count($vo['cname'],'  ')).$vo['name'].'</option>';
                      }
                      $zf_html.='</select>
            '.$append_notes.'
                  </div>
                  ';

        }elseif($theme==2){
          $zf_html = '
        <div class="layui-card-header">'.$title.'</div>
                  <div class="layui-card-body layui-row layui-col-space10">
                     <select lay-filter="'.$name.'" class="layui-input" name="'.$name.'" >';
                      foreach($list as $k=>$vo){
                           $zf_html .= '<option value="'.$vo.'" ';
                           if($data==$vo){
                            $zf_html.='selected';
                           }
                            $zf_html.='> '.$vo.'</option>';
                      }
                      $zf_html.='</select>
            '.$append_notes.'
                  </div>
                  ';
        }elseif($theme==3){
          $zf_html = '
        <div class="layui-card-header">'.$title.'</div>
                  <div class="layui-card-body layui-row layui-col-space10">
                     <select lay-filter="'.$name.'" name="'.$name.'" class="layui-input '.$name.'" >';
                      foreach($list as $k=>$vo){
                           $zf_html .= '<option value="'.$vo[$id_t].'" ';
                           if($data==$vo[$id_t]){
                            $zf_html.='selected';
                           }
                            $zf_html.='> '.$vo[$name_t].'</option>';
                      }
                      $zf_html.='</select>
            '.$append_notes.'
                  </div>
                  ';
        }elseif($theme==4){
          $zf_html = '
          <div class="layui-form-item">
            <label class="layui-form-label">'.$title.'</label>
            <div class="layui-input-block">
            <select lay-filter="'.$name.'" class="layui-input" name="'.$name.'" >';
            foreach($list as $k=>$vo){
                 $zf_html .= '<option value="'.$vo[$id_t].'" ';
                 if($data==$vo[$id_t]){
                  $zf_html.='selected';
                 }
                  $zf_html.='> '.$vo[$name_t].'</option>';
            }
            $zf_html.='</select>
            '.$append_notes.'
            </div>
          </div>
                    ';
        }elseif($theme==5){
          $zf_html = '
          <div class="layui-form-item">
            <label class="layui-form-label">'.$title.'</label>
            <div class="layui-input-block">
            <select lay-filter="'.$name.'" class="layui-input" name="'.$name.'" >';
            foreach($list as $k=>$vo){
              $zf_html .= '<option value="'.$vo.'" ';
              if($data==$vo){
               $zf_html.='selected';
              }
               $zf_html.='> '.$vo.'</option>';
          }
          $zf_html.='</select>
            '.$append_notes.'
            </div>
          </div>
                  ';
        }elseif($theme==6){
          $zf_html = '
          <div class="layui-form-item">
            <label class="layui-form-label">'.$title.'</label>
            <div class="layui-input-block">
            <select lay-filter="'.$name.'" class="layui-input" name="'.$name.'" >';
            foreach($list as $k=>$vo){
                  $zf_html .= '<option value="'.$vo[$id_t].'" ';
                  if($data==$vo[$id_t]){
                  $zf_html.='selected';
                  }
                  $zf_html.='> ┃'.str_repeat('━━', substr_count($vo['cname'],'  ')).$vo['name'].'</option>';
            }
            $zf_html.='</select>
            '.$append_notes.'
            </div>
          </div>
                  ';
        }elseif($theme==7){
          $zf_html = '
          <div class="layui-card-header">'.$title.'</div>
          <div class="layui-card-body layui-row layui-col-space10">
            <select class="layui-input" name="'.$name.'" >';
            foreach($list as $k=>$vo){
              $zf_html .= '<option value="'.$vo.'" ';
              if($data==$vo){
              $zf_html.='selected';
              }
              $zf_html.='> '.$vo.'</option>';
          }
          $zf_html.='</select>
            '.$append_notes.'
            </div>
                  ';
        }
        
        return $zf_html;
    }

    
    
  
    
   
    
   
  
   
    

    public function form_ueditor($request_data=array()){
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $zf_html ='';
        $zf_html = <<<INFO
            <div class="layui-card-header">
              <fieldset class="layui-elem-field layui-field-title site-title">
                <legend><a name="quickstart">$title</a></legend>
              </fieldset>
            </div>
            <div class="layui-card-body">
                <script id="$tpl_id" name="$name" type="text/plain" >$data</script>
                $append_notes
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

    public function form_tinymce($request_data=array()){
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $zf_html ='';
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
                $append_notes
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


    public function form_wangeditor($request_data=array()){
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $zf_html ='';
        $zf_html = <<<INFO
            <div class="layui-card-header">
              <fieldset class="layui-elem-field layui-field-title site-title">
                <legend><a name="quickstart">$title</a></legend>
              </fieldset>
            </div>
            <div class="layui-card-body">
                <div  class="fabu_editor " id="$tpl_id">$data</div>
                <textarea class="$tpl_id" style="width:100%; height:200px;" name="$name" hidden="">$data</textarea>
                $append_notes
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


     public function form_meditor($request_data=array()){
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $zf_html ='';
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
                $append_notes
            </div>
 <script type="text/javascript">
var $name;
$(function() {
$name = editormd("$name", {
    width: "100%",
    height: 540,
    // autoHeight:true,
    path : "$this->zfcms_static/style/meditor/lib/",
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

    public function form_vditor($request_data=array()){
      $tpl_id='zf_'.mt_rand().'_'.time();
      $title = isset($request_data['title'])?$request_data['title']:'';
      $name = isset($request_data['name'])?$request_data['name']:'';
      $data = isset($request_data['data'])?$request_data['data']:'';
      $vditor_num = isset($request_data['vditor_num'])?$request_data['vditor_num']:'1';
      $theme = isset($request_data['theme'])?$request_data['theme']:'1';
      $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
      $zf_html ='';
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
              $append_notes
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


  public function form_input_select($request_data=array())
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $_data = isset($request_data['_data'])?$request_data['_data']:'';
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $placeholder = isset($request_data['placeholder'])?$request_data['placeholder']:'';
        $list = isset($request_data['list_arr'])?$request_data['list_arr']:[];
        $url = isset($request_data['url'])?$request_data['url']:'';
        if(is_string($list)){
          eval("\$list = ".$request_data['list_arr'].'; ');
        }
        if($url!=''){
          $url_js = "url: '".$url."',";
          $data_js = false;
        }else{
          $url_js = false;
          //本地
          $data_js = " data:".json_encode($list).",";
        }
        $zf_html='';

        if($theme==1){
          $zf_html = <<<INFO
          <div class="layui-card-header">$title</div>
         <div class="layui-card-body layui-row layui-col-space8">
             <div class="layui-col-md12">
                 <div class="$tpl_id">
             </div>
             $append_notes
         </div>
          <script>
           window.selectInput.render({
              elem: ".$tpl_id",
              $url_js
              statusOK: 1,
              name: "$name",
              initValue: "$data",
              localSearch:true,
              $data_js
              hasSelectIcon: true,
              hasInitShow: false,
              paging: true,
              remoteSearch: false,
              pageRemote: false,
              ignoreCase:true,
              pageSize: 10,
              invisibleMode:true,
              onFocus(item) {
                  console.log(item)
              },
              onBlur(item) {
                  console.log(item)
              },
              onClick(item) {
                // console.log('click')
                  console.log(item)
              },
              done() {
                  // console.log('我渲染完成了哦')
              }
          })

         </script>
INFO;
        }elseif($theme==2){
          $zf_html = <<<INFO
          <div class="layui-form-item">
            <label class="layui-form-label">$title:</label>
            <div class="layui-input-block">
              <div class="$tpl_id">
            </div>
            $append_notes
          </div>
          <script>
          window.selectInput.render({
            elem: ".$tpl_id",
            $url_js
            statusOK: 1,
            name: "$name",
            initValue: "$data",
            localSearch:true,
            $data_js
            hasSelectIcon: true,
            hasInitShow: false,
            paging: true,
            remoteSearch: false,
            pageRemote: false,
            ignoreCase:true,
            pageSize: 10,
            invisibleMode:true,
            onFocus(item) {
                console.log(item)
            },
            onBlur(item) {
                console.log(item)
            },
            onClick(item) {
              // console.log('click')
                console.log(item)
            },
            done() {
                // console.log('我渲染完成了哦')
            }
        })
         </script>
INFO;
        }elseif($theme==3){
          $zf_html = <<<INFO
          <div class="layui-card-header">$title</div>
         <div class="layui-card-body layui-row layui-col-space8">
             <div class="layui-col-md12">
                 <div class="$tpl_id">
                <input id="$tpl_id" type="hidden" name="_$name" value="$_data" />
             </div>
             $append_notes
         </div>
          <script>
           window.selectInput.render({
              elem: ".$tpl_id",
              $url_js
              statusOK: 1,
              name: "$name",
              initValue: "$data",
              localSearch:true,
              $data_js
              hasSelectIcon: true,
              hasInitShow: false,
              paging: true,
              remoteSearch: false,
              pageRemote: false,
              ignoreCase:true,
              pageSize: 10,
              invisibleMode:true,
              onFocus(item) {
                  console.log(item)
              },
              onBlur(item) {
                  console.log(item)
              },
              onClick(item) {
                // console.log('click')
                $("#$tpl_id").val(item.value)
              },
              done() {
                  // console.log('我渲染完成了哦')
              }
          })

         </script>
INFO;
        }elseif($theme==4){
          $zf_html = <<<INFO
          <div class="layui-form-item">
            <label class="layui-form-label">$title:</label>
            <div class="layui-input-block">
              <div class="$tpl_id">
              <input id="$tpl_id" type="hidden" name="_$name" value="$_data" />
            </div>
            $append_notes
          </div>
          <script>
          window.selectInput.render({
            elem: ".$tpl_id",
            $url_js
            statusOK: 1,
            name: "$name",
            initValue: "$data",
            localSearch:true,
            $data_js
            hasSelectIcon: true,
            hasInitShow: false,
            paging: true,
            remoteSearch: false,
            pageRemote: false,
            ignoreCase:true,
            pageSize: 10,
            invisibleMode:true,
            onFocus(item) {
                console.log(item)
            },
            onBlur(item) {
                console.log(item)
            },
            onClick(item) {
              // console.log('click')
              $("#$tpl_id").val(item.value)
            },
            done() {
                // console.log('我渲染完成了哦')
            }
        })
        </script>
INFO;
        }


        return $zf_html;
    }


    /**
     * 多分组复选框
     * 20231208新增
     *{$form_widget->form_cate_checkbox(['title'=>'多分组','name'=>'cids','data'=>$data_res['cids'],'id_t'=>'cid', 'name_t'=>'name','list_arr'=>$plist,'theme'=>1])|raw}
     * 
     */
    public function form_cate_checkbox($request_data=array())
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $title = isset($request_data['title'])?$request_data['title']:'';
        $name = isset($request_data['name'])?$request_data['name']:'';
        $data = isset($request_data['data'])?$request_data['data']:'';
        $data_arr = explode(',',$data);
        $id_t = isset($request_data['id_t'])?$request_data['id_t']:'id';
        $name_t = isset($request_data['name_t'])?$request_data['name_t']:'name';
        $list = isset($request_data['list_arr'])?$request_data['list_arr']:[];
        if(is_string($list)){
          eval("\$list = ".$request_data['list_arr'].'; ');
        }
        $theme = isset($request_data['theme'])?$request_data['theme']:'1';
        $append_notes = $this->append_notes((isset($request_data['notes'])?$request_data['notes']:''));//备注提示
        $zf_html='';
        if($theme==1){
          $zf_html = '
        <div class="layui-card-header">'.$title.'</div>
                  <div class="layui-card-body layui-row layui-col-space10">
                    <div style="height: 200px;overflow: auto;">';
                      foreach($list as $k=>$vo){
                          $zf_html .= '<input type="checkbox" name="zf_list_'.$name.'[]" value="'.$vo[$id_t].'" title="'.$vo[$name_t].'" lay-skin="primary" ';
                           if(in_array($vo[$id_t],$data_arr)){
                            $zf_html.='checked';
                           }
                            $zf_html.=' /> ┃'.str_repeat('━━', substr_count($vo['cname'],'  ')).$vo['name'].' <br>';
                      }
                      $zf_html.='
                      </div>
                '.$append_notes.'
                  </div>
                  <input name="_temp_arr_key[]"  type="hidden" value="'.$name.'" />
                  ';
        }elseif($theme==2){
          $zf_html = '
          <div class="layui-form-item">
            <label class="layui-form-label">'.$title.'</label>
            <div class="layui-input-block">
              <div style="height: 200px;overflow: auto;">';
              foreach($list as $k=>$vo){
                  $zf_html .= '<input type="checkbox" name="zf_list_'.$name.'[]" value="'.$vo[$id_t].'" title="'.$vo[$name_t].'" lay-skin="primary" ';
                    if(in_array($vo[$id_t],$data_arr)){
                    $zf_html.='checked';
                    }
                    $zf_html.=' /> ┃'.str_repeat('━━', substr_count($vo['cname'],'  ')).$vo['name'].' <br>';
              }
              $zf_html.='
              </div>
              '.$append_notes.'
            </div>
          </div>
          <input name="_temp_arr_key[]"  type="hidden" value="'.$name.'" />
                  ';
        }
        
        return $zf_html;
    }


    
   


   
}

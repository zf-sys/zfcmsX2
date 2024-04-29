<?php
// WebSite: http://www.zf-sys.com/
// Document:  http://bbs.90ckm.com/
// Bbs:  http://bbs.90ckm.com/
// Github: https://github.com/zf-sys/zfcmsX2
// Gitee:https://gitee.com/zf-sys/zfcmsX2
// Feedback: https://support.qq.com/products/166743
namespace app\common\controller;
use think\Controller;
use think\Db;
use GuzzleHttp\Client;
use think\facade\Request;
use think\captcha\Captcha;
class Sysauth extends Controller
{
    public function __construct ($load = true){
        if(is_file('./extend/zf/Yun.php')){
            if(!extension_loaded("IonCube Loader")) {     
                echo str_show_tpl(base64_decode('VGhlIGlvbmN1YmUgZXh0ZW5zaW9uIGlzIG5vdCBjdXJyZW50bHkgaW5zdGFsbGVkIGFuZCBjYW5ub3QgYmUgdXNlZCAh')."  <a href='".ZFC('version.api_domain','file')."/question_list.html#ioncube_install' target='_blank'>".base64_decode('PHNwYW4gc3R5bGU9ImNvbG9yOnJlZDsiPklvbmN1YmXmianlsZXmnKrlronoo4Us54K55Ye75p+l55yL5a6J6KOF5pWZ56iLPC9zcGFuPg==')."</a>");die;
            }
            // if($load){
            //     $this->Yun = new \zf\Yun(1); //检测全
            // }else{
            //     $this->Yun = new \zf\Yun(2);//yun工具
            // }
            $this->is_professional_edition = true;
        }else{
            $this->is_professional_edition = false;
        }
        if(extension_loaded("IonCube Loader")) {     
            $this->sqb_error_msg = base64_decode(base64_encode("<div>".base64_decode('VGhlIGNvbW11bml0eSB2ZXJzaW9uIGRvZXMgbm90IHN1cHBvcnQgdGhpcyBmZWF0dXJlLiBDbGljayB0byB1cGdyYWRlIHRvIHRoZSBwcm9mZXNzaW9uYWwgdmVyc2lvbiA=')."<a href='".ZFC('version.api_domain','file').'/yun_down_list?v='.ZFC('version.version','file')."' target='_blank'>".base64_decode('PHNwYW4gc3R5bGU9ImNvbG9yOnJlZDsiPueCueWHu+S4i+i9vVl1bi5waHA8L3NwYW4+')."</a></div>"));
        }else{
            $this->sqb_error_msg = base64_decode(base64_encode("<div>".base64_decode('VGhlIGNvbW11bml0eSB2ZXJzaW9uIGRvZXMgbm90IHN1cHBvcnQgdGhpcyBmZWF0dXJlLiBDbGljayB0byB1cGdyYWRlIHRvIHRoZSBwcm9mZXNzaW9uYWwgdmVyc2lvbiA=')."<a href='".ZFC('version.api_domain','file').'/yun_down_list?v='.ZFC('version.version','file')."' target='_blank'>".base64_decode('PHNwYW4gc3R5bGU9ImNvbG9yOnJlZDsiPueCueWHu+S4i+i9vVl1bi5waHA8L3NwYW4+')."</a>|<a href='".ZFC('version.api_domain','file')."/question_list.html#ioncube_install' target='_blank'>".base64_decode('PHNwYW4gc3R5bGU9ImNvbG9yOnJlZDsiPklvbmN1YmXmianlsZXmnKrlronoo4Us54K55Ye75p+l55yL5a6J6KOF5pWZ56iLPC9zcGFuPg==')."</a></div>"));
        }
        $this->module = strtolower(request()->module());
        $this->controller = strtolower(request()->controller());
        $this->action = strtolower(request()->action());
        $this->u_key = config()['zf_auth']['key'];
        $this->s_ver = config()['version']['version'];
        $this->s_soft_id = config()['version']['soft_id'];
        parent::__construct();
    }
    
    
    
    public function auth(){
        $this->Yun = new \zf\Yun(2);
        //是否存在某个文件
        $t = input('t','');
        if($t=='status'){
            // 判断是否正确
            $auth_info['sc'] = config()['zf_auth']['sc'];
            $auth_info['key'] = config()['zf_auth']['key'];
            $auth_info['soft_id'] = config()['version']['soft_id'];
            $this->zfauth = new ZfAuth();
            // $this->zfauth->vfast_check($auth_info,'alert');
            $this->zfauth->plugin_check($auth_info,'alert');
            if(config()['zf_auth']['key']!='' &&  config()['zf_auth']['sc']!='' &&  config()['zf_auth']['email']!='' ){
                return jssuccess('授权成功');
            }
        }
        if($t=='save'){
            $data = input('post.');
            $res = extraconfig($data,'zf_auth');
            if($res){
                $auth_info['email'] = $data['email'];
                $auth_info['sc'] = $data['sc'];
                $auth_info['key'] = $data['key'];
                $auth_info['soft_id'] = config()['version']['soft_id'];
                $this->zfauth = new ZfAuth();
                if($data['key']!='' &&  $data['sc']!='' &&  $data['email']!='' ){
                    return jssuccess('授权成功');
                }else{
                    return jserror('授权失败,请查看填写内容是否正确');die;
                }
            }else{
                return jserror('保存失败,请查看是config文件夹是否有保存权限');die;
            }  
        }
        if($t == 'upload_authfile'){
            $file = request()->file('file');
            $info = $file->move('./runtime','');
            if(!file_exists('./runtime/'.$info->getSaveName())){
                return jserror('上传文件失败,请检查runtime文件夹是否有写入权限');die;
            }
            //修改文件名
            $file_name = './runtime/'.str_replace('.','',$info->getSaveName());
            rename('./runtime/'.$info->getSaveName(),$file_name);
            $is_sq = $this->Yun->_save_license_convert_sc();
            if($is_sq['code']==0){
                return ZFRetMsg(false,'',$is_sq['msg']);
            }
            if($this->Yun->_get_site_auth('','',1)){
                return ZFRetMsg(true,'更新成功','');
            } else{
                return ZFRetMsg(false,'','授权文件错误');
            }

        }
           
            
        // if($data['key']!='' && $data['sc']!=''){
        //     $this->redirect(url('login/index'));
        // }
        $data =  config()['zf_auth'];
        $version =  config()['version'];
        $this->assign('data',$data);
        $this->assign('version',$version);
        $is_auth = $this->Yun->_get_site_auth('','',1);
        if($is_auth){
            $this->redirect('/','无须重复授权');
        }
        return view();
    }
    
    
}


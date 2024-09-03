<?php
namespace zf;
use think\Db;

class ZfAi
{
    private $webconfig;
    private $openAi;

    public function __construct()
    {
        $this->webconfig = json_decode(ZFC('webconfig'), true);
        $this->initOpenAi();
    }

    private function initOpenAi()
    {
        $apikey = $this->getConfigValue('ai_gpt_key');
        $ai_gpt_host = $this->getConfigValue('ai_gpt_host');
        
        if (!$apikey || !$ai_gpt_host) {
            return ['code'=>0,'msg'=>'请先在网站设置->基本设置->AI中配置KEY和AI中转站'];
        }
        $this->ai_gpt_host = $ai_gpt_host;

        $this->openAi = new \lib\OpenAi($apikey);
        $this->openAi->setBaseURL($ai_gpt_host);
    }

    private function getConfigValue($key, $default = '')
    {
        return isset($this->webconfig[$key]) ? $this->webconfig[$key] : $default;
    }

    /**
     * 20231109新增
     * openai的使用
     */
    public function zfyun_openai($content,$sys_message=''){
        
//        $ai_gpt_model = isset_arr_key($this->webconfig,'ai_gpt_model','');
        $ai_gpt_model = 'zfcms_seo_v1';
//        $ai_gpt_model = 'gpt-4o-mini';
        if($ai_gpt_model==''){
            return ['code'=>0,'msg'=>'请先在网站设置->基本设置->AI中配置文字模型'];
        }
        $messages = [
            [
                'role'=>'system',
                'content'=>$sys_message
            ],
            [
                'role'=>'user',
                'content'=>'参考如下: '.$content

            ]
        ];
        try{
            // $this->openAi->setProxy("http://127.0.0.1:1086");
            //全量输出模式
            $_gpt_init = [
                'model' => $ai_gpt_model,
                'messages' =>$messages,
                'temperature' => 0.01,
                'frequency_penalty' => 0,
                'presence_penalty' => 0.6,
                'response_format'=>["type"=> "json_object"]
            ];
            $complete = $this->openAi->chat($_gpt_init);
//            dd($complete);
            $rr = json_decode($complete,true);
            if(!$complete){
                return ['code'=>0,'msg'=>'请求异常,请检查网络,或域名是否正确'];
            }
            if(isset($rr['choices'][0]['message']['content'])){
                $choices = $rr['choices'][0]['message']['content'];
            }else{
                if(is_str_find($this->ai_gpt_host,'api2d') && $rr['object']=='error' ){
                    return ['code'=>0,'msg'=>'err2 '. $rr['message']];
                }elseif(isset($rr['error'])){
                    return ['code'=>0,'msg'=>'err2 '. $rr['error']['message']];
                }else{
                    return ['code'=>0,'msg'=>'err3 '. json_encode($rr)];
                }
            }
            $message =  $choices;
            return ['code'=>1,'msg'=>$message];
        } catch (\Exception $e) {
            return ['code'=>0,'msg'=>$e->getMessage()];
        }

    }
    public function zfyun_openai_pic($content,$sys_message=''){
        
        $ai_gpt_pic_model = isset_arr_key($this->webconfig,'ai_gpt_pic_model','');
        if($ai_gpt_pic_model==''){
            return ['code'=>0,'msg'=>'请先在网站设置->基本设置->AI中配置图片模型'];
        }

        $ai_gpt_pic_size = isset_arr_key($this->webconfig,'ai_gpt_pic_size','');
        if($ai_gpt_pic_size==''){
            return ['code'=>0,'msg'=>'请先在网站设置->基本设置->AI中配置图片大小'];
        }
        $text = $sys_message.' : '.$content;
        try{
            $_gpt_init = [
                'model' => $ai_gpt_pic_model,
                'prompt' => $text,
                // 'num_images' => 1,//已弃用
                'n'=>1,
                'size' => $ai_gpt_pic_size,// 1024x1024
            ];
            $complete = $this->openAi->image($_gpt_init);
            $rr = json_decode($complete,true);

            // dd($rr);
            if(!$complete){
                return ['code'=>0,'msg'=>'请求异常,请检查网络,或域名是否正确'];
            }
            if(isset($rr['data'][0]['url'])){
                $choices = $rr['data'][0]['url'];
            }else{
                if(isset($rr['error']['message'])){
                    return ['code'=>0,'msg'=>'err '. $rr['error']['message']];
                }else{
                    return ['code'=>0,'msg'=>'err '. $rr['message']];
                }
            }
            $message =  $choices;
            return ['code'=>1,'msg'=>$message];
        } catch (\Exception $e) {
            return ['code'=>0,'msg'=>$e->getMessage()];
        }

    }
    /**
     * 远程获取openai认证的中转域名
     */
    private function aihost_authhost_token(){

        $this->aihost_auth_domain_arr = [
            'https://gptapi.jianshe2.com'
        ];
    }


}
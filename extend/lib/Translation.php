<?php
namespace lib;
use Exception;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use AlibabaCloud\Alimt\Alimt;
class Translation {
    // 配置信息 - 现在这些配置信息可以方便地修改
    private $accessKeyId;
    private $accessKeySecret;
    private $regionId;
    private $sourceLanguage;
    private $targetLanguage;
    private $scene;
    private $formatType;

    // 构造函数用于初始化配置信息
    public function __construct($accessKeyId = '******', 
                                $accessKeySecret = '8RITtl4y****J6UcZr', 
                                $regionId = 'cn-hongkong', 
                                $sourceLanguage = 'zh', 
                                $targetLanguage = 'en', 
                                $scene = 'general', 
                                $formatType = 'text') {
        $this->accessKeyId = $accessKeyId;
        $this->accessKeySecret = $accessKeySecret;
        $this->regionId = $regionId;
        $this->sourceLanguage = $sourceLanguage;
        $this->targetLanguage = $targetLanguage;
        $this->scene = $scene;
        $this->formatType = $formatType;
    }

    

    function aliy_tra($txt) {
        require_once './extend/alimt/vendor/autoload.php';
        AlibabaCloud::accessKeyClient($this->accessKeyId, $this->accessKeySecret) // 使用配置变量
                    ->regionId($this->regionId) // 使用配置变量
                    ->asDefaultClient();

        try {
            $request = Alimt::v20181012()->translateGeneral();
            $result = $request->withSourceLanguage($this->sourceLanguage) // 使用配置变量
                              ->withTargetLanguage($this->targetLanguage) // 使用配置变量
                              ->withSourceText($txt)
                              ->withScene($this->scene) // 使用配置变量
                              ->withFormatType($this->formatType) // 使用配置变量
                              ->debug(false)
                              ->request();

            $res = $result->toArray();
            return $res['Code'] === '200'
                ? ['code' => 1, 'ret_txt' => $res['Data']['Translated']]
                : ['code' => 0, 'msg' => 'err1'];
        } catch (ClientException $exception) {
            return ['code' => 0, 'msg' => $exception->getMessage()];
        } catch (ServerException $exception) {
            return ['code' => 0, 'msg' => $exception->getMessage()];
        }
    }
    function aliy_tra_bigmodel($text,$sourceLanguage='cn',$targetLanguage='en') {
        $sys_message = <<<EOF
你是一个拥有多年经验的翻译专家, 拥有丰富的经验，精通多国语言,可以按照用户的要求进行翻译。

## 返回数据结构
{
  "text": "翻译后的内容"
}

## 返回解释
返回json,
text:翻译后的内容

## 案例
User question: 你翻译成英文:你好世界
Model answer: 
{
  "text": "Hello World"
}
EOF;
        $content_str = '请将下面的文本由'.$sourceLanguage.'翻译成'.$targetLanguage;
        $content_str = '我要翻译的内容是:'.$text;
        $zfai = new \zf\ZfAi();
        $_data = $zfai->zfyun_openai($content_str,$sys_message);
        if($_data['code']==0){
            return ['code' => 0, 'msg' => 'err-返回数据错误'];
        }
        $_ret_data_arr = json_decode($_data['msg'],true);
        if(!$_ret_data_arr){
            dd($_data['msg']);
            return ['code'=>0,'msg'=>'err2-返回结构错误'];
        }
        if( !isset($_ret_data_arr['text'])){
            return ['code'=>0,'msg'=>'err3-返回结构错误'];
        }
        return ['code'=>1,'msg'=>$_ret_data_arr['text']];
    }

    /**
     * 批量翻译中文文本
     */
    function batchTranslateToEnglish(array $chineseTexts) {
        if (empty($chineseTexts)) {
            return []; // 如果没有文本直接返回空数组
        }
        $fany_arr = $this->aliy_tra(implode(PHP_EOL, $chineseTexts)); // 添加 $this 以确保方法调用正确

        return $fany_arr['code'] === 1
            ? explode(PHP_EOL, $fany_arr['ret_txt'])
            : $chineseTexts; // 返回原文本以防翻译失败
    }

    /**
     * 提取中文并翻译
     */
    function extractAndTranslateChinese($html, $gl_str_arr) {
        $pattern = '/[\x{4e00}-\x{9fa5}]+/u';
        preg_match_all($pattern, $html, $matches);

        if (empty($matches[0])) {
            return $html; // 如果没有匹配的中文，直接返回原 HTML
        }

        // 去重和过滤
        $chineseTexts = array_values(array_unique(array_diff($matches[0], $gl_str_arr)));
        usort($chineseTexts, function ($a, $b) {
            return strlen($b) - strlen($a); // 按长度排序
        });

        // 批量翻译
        $translatedBatch = $this->batchTranslateToEnglish($chineseTexts); // 添加 $this 以确保方法调用正确

        // 替换翻译后的文本
        foreach ($chineseTexts as $index => $chinese) {
            $html = str_replace($chinese, $translatedBatch[$index] ?? $chinese, $html);
        }

        // 处理标点
        return str_replace(['。', '，', ', ,'], ['.', ',', ','], $html);
    }

}
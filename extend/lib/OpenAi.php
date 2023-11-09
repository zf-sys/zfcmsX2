<?php
namespace lib;
use Exception;

/**
 * 支持
 * 1. 内容补全接口
 * 2. 图片生成接口
 * 3. 向量生成接口
 * 4. 聊天接口
 */
class OpenAi {
    private string $engine = "davinci";
    private string $model = "text-davinci-002";
    private string $chatModel = "gpt-3.5-turbo";
    private array $headers;
    private array $contentTypes;
    private int $timeout = 0;
    private object $stream_method;
    private string $customUrl = "";
    private string $proxy = "";
    private array $curlInfo = [];
    private string $host = "https://api.openai.com";

     public function __construct($OPENAI_API_KEY)
    {
        $this->contentTypes = [
            "application/json"    => "Content-Type: application/json",
            "multipart/form-data" => "Content-Type: multipart/form-data",
        ];

        $this->headers = [
            $this->contentTypes["application/json"],
            "Authorization: Bearer $OPENAI_API_KEY",
        ];
    }


    /**
     * OpenAI 聊天接口( /v1/chat/completions )
     */
    public function chat($opts, $stream = null)
    {
        if ($stream != null && array_key_exists('stream', $opts)) {
            if (!$opts['stream']) {
                throw new Exception(
                    'Please provide a stream function. Check https://github.com/orhanerday/open-ai#stream-example for an example.'
                );
            }

            $this->stream_method = $stream;
        }

        $opts['model'] = $opts['model'] ?? $this->chatModel;
        // $url           = Url::chatUrl();
        $url = $this->host.'/v1/chat/completions';
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }
    /**
     * OpenAI 向量生成接口( /v1/embeddings )
     */
    public function embeddings($opts)
    {
        $url = $this->host.'/v1/embeddings';
        $this->baseUrl($url);
        return $this->sendRequest($url, 'POST', $opts);
    }
    /**
     * OpenAI 图片生成接口( /v1/images/generations )
     */
    public function image($opts)
    {
        // $url = Url::imageUrl()."/generations";
        $url = $this->host.'/v1/images/generations';
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }

    

    /**
     * OpenAI 内容补全接口
     *  /v1/completions
     */
    public function completion($opts, $stream = null)
    {
        if ($stream != null && array_key_exists('stream', $opts)) {
            if (!$opts['stream']) {
                throw new Exception(
                    'Please provide a stream function. Check https://github.com/orhanerday/open-ai#stream-example for an example.'
                );
            }

            $this->stream_method = $stream;
        }

        $opts['model'] = $opts['model'] ?? $this->model;
        // $url           = Url::completionsURL();
        $url = $this->host.'/v1/completions';
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }


    private function sendRequest(string $url, string $method, array $opts = [])
    {
        $post_fields = json_encode($opts);

        if (array_key_exists('file', $opts) || array_key_exists('image', $opts)) {
            $this->headers[0] = $this->contentTypes["multipart/form-data"];
            $post_fields      = $opts;
        } else {
            $this->headers[0] = $this->contentTypes["application/json"];
        }
        $curl_info = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_POSTFIELDS     => $post_fields,
            CURLOPT_HTTPHEADER     => $this->headers,
        ];

        if ($opts == []) {
            unset($curl_info[CURLOPT_POSTFIELDS]);
        }

        if (!empty($this->proxy)) {
            $curl_info[CURLOPT_PROXY] = $this->proxy;
        }

        if (array_key_exists('stream', $opts) && $opts['stream']) {
            $curl_info[CURLOPT_WRITEFUNCTION] = $this->stream_method;
        }

        $curl = curl_init();

        curl_setopt_array($curl, $curl_info);
        $response = curl_exec($curl);

        $info           = curl_getinfo($curl);
        $this->curlInfo = $info;

        curl_close($curl);

        return $response;
    }
    public function setBaseURL(string $customUrl)
    {
        if ($customUrl != '') {
            $this->host = $customUrl;
        }
    }
    public function setProxy(string $proxy)
    {
        if ($proxy && strpos($proxy, '://') === false) {
            $proxy = 'https://'.$proxy;
        }
        $this->proxy = $proxy;
    }
    private function baseUrl(string &$url)
    {
        if ($this->customUrl != "") {
            $url = str_replace(Url::ORIGIN, $this->customUrl, $url);
        }
    }
}


/**
 
 */
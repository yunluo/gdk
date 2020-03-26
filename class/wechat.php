<?php
//error_reporting(0);

/**
 * 微信(或易信)公共平台处理类
 *
 * 用于创建微信(或易信)公共平台服务
 *
 * @author: Specs
 * Email: specs@9iphp.com
 * Blog: http://www.9iphp.com
 */
class WeChat
{

    private $__token             = ""; //TOKEN值
    private $__callback_function = null; //回调函数名称

    public $fromUser = ""; //当前消息的发送者
    public $toUser   = ""; //当前消息的接收者

    /**
     * 构造函数
     *
     * @param string $token 设置在公共平台的TOKEN值
     * @param callable $callback_function_name 回调函数名称
     */
    public function __construct($token, $callback_function_name)
    {
        $this->token             = $token;
        $this->callback_function = $callback_function_name;
    }

    /**
     * 检查签名是否正确
     *
     * @return boolean 正确返回true,否则返回false
     */
    private function __checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce     = $_GET["nonce"];

        $token = $this->token;

        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * 验证签名是否有效
     */
    protected function _valid()
    {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if ($this->checkSignature()) {
            echo $echoStr;
            exit;
        } else {
            echo 'error signature';
        }
    }

    /**
     * 处理来自微信服务器的消息
     */
    public function process()
    {
        //如果是验证请求,则执行签名验证并退出
        if (!empty($_GET["echostr"])) {
            $this->valid(); //验证签名是否有效
            return; //返回退出
        }

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo '';
            return;
        }

        //如果不是验证请求，则
        //首先，取得POST原始数据(XML格式)
        //$postData = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postData = file_get_contents('php://input');
        if (empty($postData)) {
            echo '';
            return;
        } //如果没有POST数据，则退出

        //解析POST数据(XML格式)
        $object         = simplexml_load_string($postData, 'SimpleXMLElement', LIBXML_NOCDATA);
        $messgeType     = trim($object->MsgType); //取得消息类型
        $this->fromUser = $object->FromUserName; //记录消息发送方(不是发送者的微信号，而是一个加密后的OpenID)
        $this->toUser   = $object->ToUserName; //记录消息接收方(就是公共平台的OpenID)

        //如果回调函数没有设置，则退出
        if (!is_callable($this->callback_function)) {
            return;
        }

        if ($messgeType == "text") {
            call_user_func($this->callback_function, $this, "text", $object->Content, "", "");
        }
    }

    /**
     * 形成 文本消息响应值
     *
     * @param string $toUser
     * @param string $fromUser
     * @param string $content
     * @param integer $flag
     *
     * @return string
     */
    protected function _textResponse($toUser, $fromUser, $content, $flag = 0)
    {
        $xmlTemplate = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>%d</FuncFlag>
                    </xml>";

        $xmlText = sprintf($xmlTemplate, $toUser, $fromUser, time(), $content, $flag);

        return $xmlText;
    }

    /**
     * 发送文本内容
     *
     * @param string $content 文本内容
     */
    public function sendText($content)
    {
        echo $this->textResponse($this->fromUser, $this->toUser, $content);
    }

}

add_action('pre_get_posts', 'wx_preprocess', 4);
/**
 * 预处理函数
 *
 * @param $wp_query
 */
function wx_preprocess($wp_query)
{
    global $object;
    $wx_token = trim(gdk_option('gdk_wxmp_token'));
    if (!isset($object)) {
        //创建一个WeChat类的实例, 回调函数名称为"CaptchaMessage",即消息处理函数
        $object = new WeChat($wx_token, "CaptchaMessage");
        $object->process(); //处理消息
        return;
    }
}

/**
 * 消息处理函数
 *
 * @param WeChat $object
 * @param string $messageType
 * @param string $content
 * @param string $arg1
 * @param string $arg2
 */
function CaptchaMessage(WeChat $object, $messageType, $content, $arg1, $arg2)
{
    if ($messageType == "text") {
        $keyword = trim($content);
        if (in_string($keyword, '验证码')) {
            $object->sendText('您的验证码为：【' . wx_captcha() . '】，验证码有效期为2分钟，请抓紧使用，过期需重新申请');
        }
    }
}

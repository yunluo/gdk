<?php
//error_reporting(0);

class Wechat_Captcha
{
    public function __construct($wx_token, $wx_captcha)
    {
        $this->token   = $wx_token;
        $this->captcha = $wx_captcha;
    }
    private function __checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce     = $_GET["nonce"];
        $token     = $this->token;
        $tmpArr    = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        return $tmpStr == $signature ? true : false;
    }
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
    public function responseMsg()
    {
        //如果是验证请求,则执行签名验证并退出
        if (!empty($_GET["echostr"])) {
            $this->valid();
            //验证签名是否有效
            return;
            //返回退出
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
        }
        //如果没有POST数据，则退出
        if (!empty($postData)) {
            //解析POST数据(XML格式)
            $object         = simplexml_load_string($postData, 'SimpleXMLElement', LIBXML_NOCDATA);
            $messgeType     = trim($object->MsgType); //取得消息类型
            $this->fromUser = $object->FromUserName;
            $this->toUser   = $object->ToUserName;
            $keyword        = trim($object->Content);
            if ($messgeType == 'text' && $keyword == '验证码') {
                $response_content = '您的验证码为：【' . $this->captcha . '】，验证码有效期为2分钟，请抓紧使用，过期需重新申请';
                $xmlTemplate      = "<xml>
					 <ToUserName><![CDATA[%s]]></ToUserName>
					 <FromUserName><![CDATA[%s]]></FromUserName>
					 <CreateTime>%s</CreateTime>
					 <MsgType><![CDATA[text]]></MsgType>
					 <Content><![CDATA[%s]]></Content>
					 <FuncFlag>%d</FuncFlag>
					 </xml>";
                $xmlText = sprintf($xmlTemplate, $this->fromUser, $this->toUser, time(), $response_content, 0);
                echo $xmlText;
            }
        } else {
            echo "";
            exit;
        }
    }
}

function wx_process() {
	if(isset($_GET["signature"])) {
		global $wx_captchas;
		if(!isset($wx_captchas)) {
			$wx_token = trim(gdk_option('gdk_wxmp_token'));
			$wx_captchas   = new Wechat_Captcha($wx_token, wx_captcha());
			$wx_captchas->responseMsg();
			exit;
		}
	}
}
add_action('parse_request', 'wx_process', 4);

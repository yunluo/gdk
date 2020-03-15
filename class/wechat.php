<?php
error_reporting(0);

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
    private $__articles          = array(); //图文信息array

    public $debug    = false; //是否调试状态
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
        $this->fromUser = "" . $object->FromUserName; //记录消息发送方(不是发送者的微信号，而是一个加密后的OpenID)
        $this->toUser   = "" . $object->ToUserName; //记录消息接收方(就是公共平台的OpenID)

        //如果回调函数没有设置，则退出
        if (!is_callable($this->callback_function)) {
            return;
        }

        //根据不同的消息类型，分别处理
        switch ($messgeType) {
            case "text": //文本消息
                //调用回调函数
                call_user_func($this->callback_function, $this, "text", $object->Content, "", "");
                break;
            case "event": //事件
                switch ($object->Event) {
                    case "subscribe": //订阅事件
                        call_user_func($this->callback_function, $this, "subscribe", $object->FromUserName, "", "");
                        break;
                    default:
                        //Unknow Event
                        break;
                }
                break;
            default:
                //Unknow msg type
                break;
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
     * 形成 图文消息响应值
     *
     * @param string $toUser
     * @param string $fromUser
     * @param array $articles 一个array，每个元素保存一条图文信息；每个元素也是一个array, 有Title,Description,PicUrl,Url四个键值
     *
     * @return string
     */
    protected function _newsResponse($toUser, $fromUser, $articles)
    {
        $xmlTemplate = "<xml>
    			    <ToUserName><![CDATA[%s]]></ToUserName>
    			    <FromUserName><![CDATA[%s]]></FromUserName>
    			    <CreateTime>%s</CreateTime>
    			    <MsgType><![CDATA[news]]></MsgType>
    			    ";
        $xmlText = sprintf($xmlTemplate, $toUser, $fromUser, time());
        $xmlText .= '<ArticleCount>' . count($articles) . '</ArticleCount>';
        $xmlText .= '<Articles>';

        foreach ($articles as $article) {
            $xmlText .= '<item>';
            $xmlText .= '<Title><![CDATA[' . $article['Title'] . ']]></Title>';
            $xmlText .= '<Description><![CDATA[' . $article['Description'] . ']]></Description>';
            $xmlText .= '<PicUrl><![CDATA[' . $article['PicUrl'] . ']]></PicUrl>';
            $xmlText .= '<Url><![CDATA[' . $article['Url'] . ']]></Url>';
            $xmlText .= '</item>';
        }

        $xmlText .= '</Articles> </xml>';

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

    /**
     * 添加一条图文信息
     *
     * @param string $title 标题
     * @param string $description 内容
     * @param string $url 网页链接URL
     * @param string $pictureUrl 图片的URL
     */
    public function addNews($title, $description, $url, $pictureUrl)
    {
        $article = array('Title' => $title,
            'Description'            => $description,
            'PicUrl'                 => $pictureUrl,
            'Url'                    => $url);
        $this->articles[] = $article;
    }

    /**
     * 发送图文信息
     * 用法：首先用addNews()函数一条一条地添加图文信息，添加完成后用本函数发送
     */
    public function sendNews()
    {
        echo $this->newsResponse($this->fromUser, $this->toUser, $this->articles);
    }

}

define("WX_WELCOME", '欢迎关注极客公园'); //欢迎词
define("POSTNUM", '5'); //文章数量
define("DEFAULT_THUMB", ''); //封面

add_action('pre_get_posts', 'wm_preprocess', 4);
/**
 * 预处理函数
 *
 * @param $wp_query
 */
function wm_preprocess($wp_query)
{
    global $object;
    $wx_token = trim(gdk_option('gdk_wxmp_token'));
    if (!isset($object)) {
        //创建一个WeChat类的实例, 回调函数名称为"onMessage",即消息处理函数
        $object = new WeChat($wx_token, "onMessage");
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
function onMessage(WeChat $object, $messageType, $content, $arg1, $arg2)
{

    //处理subscribe消息
    switch ($messageType) {
        case "subscribe": //当用户关注
            $object->addNews(WX_WELCOME, "", "", "");
            $object->sendNews();
            break;
        case "text":
            $keyword = trim($content);
            switch ($keyword) {
                case 'yzm':
                case 'Yzm':
                case 'yZm':
                case 'yzM':
                case 'YZM':
                case '验证码':
                    $object->sendText('您的验证码为：【' . wx_captcha() . '】，验证码有效期为2分钟，请抓紧使用，过期需重新申请');
                    break;
                case 'r':
                    send_post($object, 'r');
                    break;
                case "help":
                case "h":
                case "?":
                case "？":
                case "？？？":
                    $object->sendText(WX_WELCOME);
                    break;
                default:
                    send_post($object, 'r');
                    break;
            }
            break;
        default:
            $object->sendText("暂无设置此功能"); //否则，显示出错信息
    }
}

//获取博客文章
function wm_query_posts($q, $s = "")
{
    global $wp_query;
    $articles   = [];
    $query_base = array(
        'ignore_sticky_posts' => true,
        'posts_per_page'      => POSTNUM,
        'post_status'         => 'publish',
    );
    if (empty($s)) {
        switch ($q) {
            case "n":
                $query_more = array(
                    "order"   => "DESC",
                    "orderby" => "date",
                );
                break;
            case "r":
                $query_more = array(
                    "orderby" => "rand",
                );
                break;
            default:
                $query_more = [];
                break;
        }
    } else {
        $query_more = array(
            's' => $s,
        );
    }
    $weixin_query_array = array_merge($query_base, $query_more);
    $wp_query->query($weixin_query_array);
    if (have_posts()) {
        while (have_posts()) {
            the_post();
            global $post;
            $title        = get_the_title();
            $excerpt      = gdk_print_excerpt(120, $post, false);
            $thumbnail_id = get_post_thumbnail_id($post->ID);
            if ($thumbnail_id) {
                $thumb = wp_get_attachment_image_src($thumbnail_id, 'full');
                $thumb = $thumb[0];
            } else {
                $thumb = gdk_thumbnail_src();
            }
            if (empty(DEFAULT_THUMB) && !empty(DEFAULT_THUMB)) {
                $thumb = DEFAULT_THUMB;
            }
            $link       = get_permalink();
            $articles[] = array($title, $excerpt, $link, $thumb);
        }
    }

    return $articles;
}

function send_post(WeChat $object, $type = '', $value = '')
{
    $articles = wm_query_posts($type, $value);
    if (empty($articles)) {
        $no_post = '暂无相关文章';
        $object->sendText($no_post);
    }
    foreach ($articles as $v) {
        $object->addNews($v['0'], $v['1'], $v['2'], $v['3']);
    }
    $object->sendNews();
}

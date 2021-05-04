<?php
if (!defined('ABSPATH')) exit;

//百度收录提示
if (gdk_option('gdk_baidurecord_b') && function_exists('curl_init')) {
    function baidu_check($url, $post_id)
    {
        $baidu_record = get_post_meta($post_id, 'baidu_record', true);
        if ($baidu_record != 1) {
            $url  = 'http://www.baidu.com/s?wd=' . $url;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $rs = curl_exec($curl);
            curl_close($curl);
            if (!strpos($rs, '没有找到该URL，您可以直接访问') && !strpos($rs, '很抱歉，没有找到与')) {
                update_post_meta($post_id, 'baidu_record', 1) || add_post_meta($post_id, 'baidu_record', 1, true);
                return 1;
            } else {
                return 0;
            }
        } else {
            return 1;
        }
    }
    function baidu_record()
    {
        global $wpdb;
        $post_id = null === $post_id ? get_the_ID() : $post_id;
        if (baidu_check(get_permalink($post_id), $post_id) == 1) {
            echo '<a target="_blank" title="点击查看" rel="external nofollow" href="http://www.baidu.com/s?wd=' . get_the_title() . '">已收录</a>';
        } else {
            echo '<a style="color:red;" rel="external nofollow" title="点击提交，谢谢您！" target="_blank" href="http://zhanzhang.baidu.com/sitesubmit/index?sitename=' . get_permalink() . '">未收录</a>';
        }
    }
}

//百度主动推送
if (gdk_option('gdk_sitemap_api')) {
    function Git_Baidu_Submit($post_ID)
    {
        if (get_post_meta($post_ID, 'gdk_baidu_submit', true) == 1) {
            return;
        }

        $url     = get_permalink($post_ID);
        $api     = gdk_option('gdk_sitemap_api');
        $request = new WP_Http;
        $result  = $request->request($api, array(
            'method'  => 'POST',
            'body'    => $url,
            'headers' => 'Content-Type: text/plain',
        ));
        if (is_array($result) && !is_wp_error($result) && $result['response']['code'] == '200') {
            error_log('baidu_submit_result：' . $result['body']);
            $result = json_decode($result['body'], true);
        }
        if (array_key_exists('success', $result)) {
            add_post_meta($post_ID, 'gdk_baidu_submit', 1, true);
        }
    }
    add_action('publish_post', 'Git_Baidu_Submit', 0);
}

//强制微信登录
function force_weauth_login_url($login_url, $redirect, $force_reauth)
{
    $login_url = get_permalink(gdk_page_id('weauth'));
    if (!empty($redirect)) {
        $login_url = add_query_arg('redirect_to', urlencode($redirect), $login_url);
    }
    if ($force_reauth) {
        $login_url = add_query_arg('reauth', '1', $login_url);
    }
    return $login_url;
}if (gdk_option('gdk_weauth_oauth') && gdk_option('gdk_weauth_oauth_force')) {
    add_filter('login_url', 'force_weauth_login_url', 10, 3);
}

//评论微信推送
if (gdk_option('gdk_Server') && !is_admin()) {
    function sc_send($comment_id)
    {
        $text    = '网站上有新的评论，请及时查看'; //微信推送信息标题
        $comment = get_comment($comment_id);
        $desp    = '' . $comment->comment_content . '
***
<br>
* 评论人 ：' . get_comment_author($comment_id) . '
* 文章标题 ：' . get_the_title() . '
* 文章链接 ：' . get_the_permalink($comment->comment_post_ID) . '
	'; //微信推送内容正文
        $key      = gdk_option('gdk_Server_key');
        $postdata = http_build_query(array(
            'text' => $text,
            'desp' => $desp,
        ));
        $opts = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata,
            ),
        );
        $context       = stream_context_create($opts);
        return $result = file_get_contents('https://sctapi.ftqq.com/' . $key . '.send', false, $context);
    }
    add_action('comment_post', 'sc_send', 19, 2);
}

//增加B站视频
wp_embed_unregister_handler('bili');
function wp_bili($matches, $attr, $url, $rawattr)
{
    if (gdk_is_mobile()) {
        $height = 200;
    } else {
        $height = 480;
    }
    $iframe = '<iframe width=100% height=' . $height . 'px src="//www.bilibili.com/blackboard/player.html?aid=' . esc_attr($matches[1]) . '" scrolling="no" border="0" framespacing="0" frameborder="no"></iframe>';
    return apply_filters('iframe_bili', $iframe, $matches, $attr, $url, $ramattr);
}
wp_embed_register_handler('bili_iframe', '#https://www.bilibili.com/video/av(.*?)/#i', 'wp_bili');

//bing美图自定义登录页面背景
function custom_login_head()
{
    if (gdk_option('gdk_loginbg')) {
        $imgurl = gdk_option('gdk_loginbg');
    } else {
        $imgurl = get_transient('Bing_img');
        if (false === $imgurl) {
            $arr    = json_decode(curl_post('https://cn.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1')['data']);
            $imgurl = 'http://cn.bing.com' . $arr->images[0]->url;
            set_transient('Bing_img', $imgurl, 60 * 60 * 24);
        }
    }
    if (defined('UM_DIR')) {
        echo '<style type="text/css">#um_captcha{width:170px!important;}</style>';
    }
    echo '<style type="text/css">#reg_passmail{display:none!important}body{background: url(' . $imgurl . ') center center no-repeat;-moz-background-size: cover;-o-background-size: cover;-webkit-background-size: cover;background-size: cover;background-attachment: fixed;}.login label,a {font-weight: bold;}.login-action-register #login{padding: 5% 0 0;}.login p {line-height: 1;}.login form {margin-top: 10px;padding: 16px 24px 16px;}h1 a { background-image:url(' . home_url() . '/favicon.ico)!important;width:32px;height:32px;-webkit-border-radius:50px;-moz-border-radius:50px;border-radius:50px;}#registerform,#loginform {background-color:rgba(251,251,251,0.3)!important;}.login label,a{color:#000!important;}form label input{margin-top:10px!important;}@media screen and (max-width:600px){.login-action-register h1 {display: none;}.login-action-register #login{top:50%!important;}}</style>';
}
add_action('login_head', 'custom_login_head');

// add youku using iframe
function wp_iframe_handler_youku($matches, $attr, $url, $rawattr)
{
    if (gdk_is_mobile()) {
        $height = 200;
    } else {
        $height = 485;
    }
    $iframe = '<iframe width=100% height=' . $height . 'px src="http://player.youku.com/embed/' . esc_attr($matches[1]) . '" frameborder=0 allowfullscreen></iframe>';
    return apply_filters('iframe_youku', $iframe, $matches, $attr, $url, $ramattr);
}
wp_embed_register_handler('youku_iframe', '#http://v.youku.com/v_show/id_(.*?).html#i', 'wp_iframe_handler_youku');
wp_embed_unregister_handler('youku');

////////////////weauth//////////////
//接受奶子微信的账号信息
function get_weauth_oauth()
{
    if (in_string($_SERVER['REQUEST_URI'], 'weauth')) {
        $weauth_user = isset($_GET['user']) ? sanitize_text_field($_GET['user']) : false; //weauth发来用户信息
        $weauth_sk   = isset($_GET['sk']) ? sanitize_text_field($_GET['sk']) : false; //weauth返回的12位sk信息
        $weauth_res  = get_transient($weauth_sk . '-OK');
        if (empty($weauth_res) && $weauth_res !== 1) {
            return;
        }

        $weauth_user  = stripslashes($weauth_user);
        $weauth_user  = json_decode($weauth_user, true);
        $oauth_result = implode('|', $weauth_user);
        set_transient($weauth_sk . '-info', $oauth_result, 60); //1分钟缓存
        header('goauth: ok');
        echo 'success'; //给对方服务器打个招呼
        exit;
    }
}
add_action('parse_request', 'get_weauth_oauth');
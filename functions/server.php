<?php

//百度收录提示
if (gdk_option('gdk_baidurecord_b') && function_exists('curl_init')) {
    function gdk_baidu_check($url, $post_id)
    {
        $baidu_record = get_post_meta($post_id, 'baidu_record', true);
        if (1 != $baidu_record) {
            $url = 'http://www.baidu.com/s?wd='.$url;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $rs = curl_exec($curl);
            curl_close($curl);
            if (!strpos($rs, '没有找到该URL，您可以直接访问') && !strpos($rs, '很抱歉，没有找到与')) {
                update_post_meta($post_id, 'baidu_record', 1) || add_post_meta($post_id, 'baidu_record', 1, true);

                return 1;
            }

            return 0;
        }

        return 1;
    }
    function baidu_record()
    {
        $post_id = get_the_ID() ?? null;
        if (1 == gdk_baidu_check(get_permalink($post_id), $post_id)) {
            echo '<a target="_blank" title="点击查看" rel="external nofollow" href="http://www.baidu.com/s?wd='.get_the_title().'">已收录</a>';
        } else {
            echo '<a style="color:red;" rel="external nofollow" title="点击提交，谢谢您！" target="_blank" href="http://zhanzhang.baidu.com/sitesubmit/index?sitename='.get_permalink().'">未收录</a>';
        }
    }
}

//百度主动推送
if (gdk_option('gdk_sitemap_api')) {
    function gdk_Baidu_Submit($post_ID)
    {
        if (1 == get_post_meta($post_ID, 'gdk_baidu_submit', true)) {
            return;
        }

        $url = get_permalink($post_ID);
        $api = gdk_option('gdk_sitemap_api');
        $request = new WP_Http();
        $result = $request->request($api, [
            'method' => 'POST',
            'body' => $url,
            'headers' => 'Content-Type: text/plain',
        ]);
        if (is_array($result) && !is_wp_error($result) && '200' == $result['response']['code']) {
            error_log('baidu_submit_result：'.$result['body']);
            $result = json_decode($result['body'], true);
        }
        if (array_key_exists('success', $result)) {
            add_post_meta($post_ID, 'gdk_baidu_submit', 1, true);
        }
    }
    add_action('publish_post', 'gdk_Baidu_Submit', 0);
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
} if (gdk_option('gdk_weauth_oauth') && gdk_option('gdk_weauth_oauth_force')) {
    add_filter('login_url', 'force_weauth_login_url', 10, 3);
}

//在登录框添加额外的微信登录
function weixin_login_button()
{
    echo '<p><a class="button button-large" href="'.get_permalink(gdk_page_id('weauth')).'">微信登录</a></p><br>';
} if (gdk_option('gdk_weauth_oauth')) {
    add_action('login_form', 'weixin_login_button');
}

//评论微信推送
if (gdk_option('gdk_Server') && !is_admin()) {
    function sc_send($comment_id)
    {
        $text = '网站上有新的评论，请及时查看'; //微信推送信息标题
        $comment = get_comment($comment_id);
        $desp = ''.$comment->comment_content.'
***
<br>
* 评论人 ：'.get_comment_author($comment_id).'
* 文章标题 ：'.get_the_title().'
* 文章链接 ：'.get_the_permalink($comment->comment_post_ID).'
	'; //微信推送内容正文
        $key = gdk_option('gdk_Server_key');
        $postdata = http_build_query([
            'text' => $text,
            'desp' => $desp,
        ]);
        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata,
            ],
        ];
        $context = stream_context_create($opts);

        return $result = file_get_contents('http://sc.ftqq.com/'.$key.'.send', false, $context);
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
    $iframe = '<iframe width=100% height='.$height.'px src="//www.bilibili.com/blackboard/player.html?aid='.esc_attr($matches[1]).'" scrolling="no" border="0" framespacing="0" frameborder="no"></iframe>';

    return apply_filters('iframe_bili', $iframe, $matches, $attr, $url, $ramattr);
}
wp_embed_register_handler('bili_iframe', '#https://www.bilibili.com/video/av(.*?)/#i', 'wp_bili');

//bing美图自定义登录页面背景
function gdk_custom_login_head()
{
    if (gdk_option('gdk_loginbg')) {
        $imgurl = gdk_get_bing_img();
    }
    echo '<style type="text/css">#um_captcha{width:170px!important;}#reg_passmail{display:none!important}body{background: url('.$imgurl.') center center no-repeat;-moz-background-size: cover;-o-background-size: cover;-webkit-background-size: cover;background-size: cover;background-attachment: fixed;}.login label,a {font-weight: bold;}.login-action-register #login{padding: 5% 0 0;}.login p {line-height: 1;}.login form {margin-top: 10px;padding: 16px 24px 16px;}h1 a { background-image:url('.home_url().'/favicon.ico)!important;width:32px;height:32px;-webkit-border-radius:50px;-moz-border-radius:50px;border-radius:50px;}#registerform,#loginform {background-color:rgba(251,251,251,0.3)!important;}.login label,a{color:#000!important;}form label input{margin-top:10px!important;}@media screen and (max-width:600px){.login-action-register h1 {display: none;}.login-action-register #login{top:50%!important;}}</style>';
}
add_action('login_head', 'gdk_custom_login_head');

// add youku using iframe
function wp_iframe_handler_youku($matches, $attr, $url, $rawattr)
{
    if (gdk_is_mobile()) {
        $height = 200;
    } else {
        $height = 485;
    }
    $iframe = '<iframe width=100% height='.$height.'px src="http://player.youku.com/embed/'.esc_attr($matches[1]).'" frameborder=0 allowfullscreen></iframe>';

    return apply_filters('iframe_youku', $iframe, $matches, $attr, $url, $ramattr);
}
wp_embed_register_handler('youku_iframe', '#http://v.youku.com/v_show/id_(.*?).html#i', 'wp_iframe_handler_youku');
wp_embed_unregister_handler('youku');

////////////////weauth//////////////
/*
function weauth_oauth_redirect()
{
    wp_redirect(home_url());
    exit;
}

function get_weauth_token()
{
    $sk = date("YmdHis") . mt_rand(10, 99);
    set_transient($sk, 1, 60 * 6);
    $key = $_SERVER['HTTP_HOST'] . '@' . $sk;
    return $key;
}

function get_weauth_qr()
{
    $qr64           = [];
    $qr64['key']    = get_weauth_token();
    $qr64['qrcode'] = json_decode(file_get_contents('https://wa.isdot.net/qrcode?str=' . $qr64['key']), true)['qrcode'];
    return $qr64;
}

function weauth_rewrite_rules($wp_rewrite)
{
    if ($ps = get_option('permalink_structure')) {
        $new_rules['^weauth'] = 'index.php?user=$matches[1]&sk=$matches[2]';
        $wp_rewrite->rules    = $new_rules + $wp_rewrite->rules;
    }
}
add_action('generate_rewrite_rules', 'weauth_rewrite_rules');

function weauth_oauth()
{
    $weauth_user = $_GET['user'];
    $weauth_sk   = esc_attr($_GET['sk']);
    $weauth_res  = get_transient($weauth_sk);
    if (empty($weauth_res)) {
        return;
    }
    $weauth_user = stripslashes($weauth_user);
    $weauth_user = json_decode($weauth_user, true);
    $nickname    = $weauth_user['nickName'];
    $wxavatar    = $weauth_user['avatarUrl'];
    $openid      = $weauth_user['openid'];
    $login_name  = 'wx_' . wp_create_nonce($openid);
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        update_user_meta($user_id, 'wx_openid', $openid);
        update_user_meta($user_id, 'simple_local_avatar', $wxavatar);
    } else {
        $weauth_user = get_users(array(
            'meta_key '  => 'wx_openid',
            'meta_value' => $openid,
        )
        );
        if (is_wp_error($weauth_user) || !count($weauth_user)) {
            $random_password = wp_generate_password(12, false);
            $userdata        = array(
                'user_login'   => $login_name,
                'display_name' => $nickname,
                'user_pass'    => $random_password,
                'nickname'     => $nickname,
            );
            $user_id = wp_insert_user($userdata);
            update_user_meta($user_id, 'wx_openid', $openid);
            update_user_meta($user_id, 'simple_local_avatar', $wxavatar);
        } else {
            $user_id = $weauth_user[0]->ID;
        }
    }
    set_transient($weauth_sk . 'ok', $user_id, 60); //用于登录的随机数，有效期为一分钟
    weauth_oauth_redirect();
}
//初始化
/*
function weauth_oauth_init()
{
    if (isset($_GET['user']) && isset($_GET['sk'])) {
        weauth_oauth();
    }
}
add_action('init', 'weauth_oauth_init');

/*
//GET自动登录
function gdk_weauth_oauth_login()
{
    $key = isset($_GET['spam']) ? $_GET['spam'] : false;
    if ($key) {
        $user_id = get_transient($key . 'ok');
        if ($user_id != 0) {
            wp_set_auth_cookie($user_id);
        }
    }
}
add_action('init', 'gdk_weauth_oauth_login');

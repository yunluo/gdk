<?php

if (!defined('ABSPATH')) {
    exit;
}

if (gdk_option('gdk_cdn')) {
    add_action('wp_loaded', 'gdk_cdn_start');
}
//七牛CDN
if (gdk_option('gdk_link_go')) {
    add_filter('the_content', 'gdk_link_go', 999);
}
// 外链GO跳转
if (gdk_option('gdk_smtp')) {
    add_action('phpmailer_init', 'gdk_smtp');
}
//SMTP
if (gdk_option('gdk_cdn_water')) {
    add_filter('the_content', 'gdk_cdn_water');
}
//CDN水印

//文章首尾添加自定义内容
function gdk_add_content($content)
{
    if (is_single()) {
        $before = gdk_option('gdk_artical_top');
        $after = gdk_option('gdk_artical_bottom');
        if (!empty($before) && !empty($after)) {
            return $before.'<br>'.$content.'<br>'.$after;
        }
        if (!empty($before)) {
            return $before.'<br>'.$content;
        }
        if (!empty($after)) {
            return $content.'<br>'.$after;
        }

        return $content;
    }

    return $content;
}
add_filter('the_content', 'gdk_add_content');

//接受奶子微信的账号信息
function get_weauth_oauth()
{
    if (in_string($_SERVER['REQUEST_URI'], 'weauth')) {
        $weauth_user = isset($_GET['user']) ? sanitize_text_field($_GET['user']) : false; //weauth发来用户信息
        $weauth_sk = isset($_GET['sk']) ? sanitize_text_field($_GET['sk']) : false; //weauth返回的12位sk信息
        $weauth_res = get_transient($weauth_sk.'-OK');
        if (empty($weauth_res) && 1 !== $weauth_res) {
            return;
        }

        $weauth_user = stripslashes($weauth_user);
        $weauth_user = json_decode($weauth_user, true);
        $oauth_result = implode('|', $weauth_user);
        set_transient($weauth_sk.'-info', $oauth_result, 60); //1分钟缓存
        header('goauth: ok');
        echo 'success'; //给对方服务器打个招呼

        exit;
    }
}
add_action('parse_request', 'get_weauth_oauth');

//社交头像
function gdk_wx_avatar($avatar, $id_or_email, $size, $default, $alt)
{
    $user = false;
    if (is_numeric($id_or_email)) {
        $id = (int) $id_or_email;
        $user = get_user_by('id', $id);
    } elseif (is_object($id_or_email)) {
        if (!empty($id_or_email->user_id)) {
            $id = (int) $id_or_email->user_id;
            $user = get_user_by('id', $id);
        }
    } else {
        $user = get_user_by('email', $id_or_email);
    }
    if ($user && is_object($user)) {
        if (get_user_meta($user->data->ID, 'wx_avatar', true)) {
            $avatar = get_user_meta($user->data->ID, 'wx_avatar', true);
            $avatar = "<img alt='{$alt}' src='{$avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
        }
    }

    return $avatar;
}
add_filter('get_avatar', 'gdk_wx_avatar', 19, 5);

//头像解决方案
function gdk_switch_get_avatar($avatar)
{
    switch (gdk_option('gdk_switch_get_avatar')) {
        case 1:
            $rand_avatar = 'https://cdn.jsdelivr.net/gh/yunluo/GitCafeApi/avatar/'.mt_rand(1, 1999).'.jpg';
            $avatar = "<img src=\"{$rand_avatar}\" class='avatar rand_avatar photo' />";

            break;

        case 2:
            $avatar = preg_replace('/http[s]{0,1}:\\/\\/(secure|www|\\d).gravatar.com\\/avatar\\//', '//cdn.v2ex.com/gravatar/', $avatar);

            break;

        case 3:
            $avatar = preg_replace('/http[s]{0,1}:\\/\\/(secure|www|\\d).gravatar.com\\/avatar\\//', '//dn-qiniu-avatar.qbox.me/avatar/', $avatar);

            break;

        default:
            $avatar = preg_replace('/http[s]{0,1}:\\/\\/(secure|www|\\d).gravatar.com\\/avatar\\//', '//cravatar.cn/avatar/', $avatar);
    }

    return $avatar;
}
add_filter('get_avatar', 'gdk_switch_get_avatar');

//懒加载
if (gdk_option('gdk_lazyload')) {
    function gdk_lazyload($content)
    {
        if ('post' != get_post_type()) {
            return $content;
        }
        if (!is_feed() || !is_robots()) {
            $content = preg_replace('/<img(.+)src=[\'"]([^\'"]+)[\'"](.*)>/i', "<img\$1data-original=\"\$2\" \$3>\n<noscript>\$0</noscript>", $content);
        }

        return $content;
    }
    add_filter('the_content', 'gdk_lazyload');
}

//强制兼容<pre>,和下面转义代码搭配使用
function gdk_prettify_replace($text)
{
    $replace = [
        '<pre>' => '<pre class="prettyprint linenums">',
    ];

    return strtr($text, $replace);
}
add_filter('content_save_pre', 'gdk_prettify_replace');
add_filter('the_content', 'gdk_prettify_replace');

//强制阻止WordPress代码转义,适用于<pre class="prettyprint linenums"> </pre>
function gdk_esc_html($content)
{
    $regex = '/(<pre\s+[^>]*?class\s*?=\s*?[",\'].*?prettyprint.*?[",\'].*?>)(.*?)(<\/pre>)/sim';

    return preg_replace_callback($regex, 'gdk_esc_callback', $content);
}
function gdk_esc_callback($matches)
{
    $tag_open = $matches[1];
    $content = $matches[2];
    $tag_close = $matches[3];
    $content = esc_html($content);

    return $tag_open.$content.$tag_close;
}
add_filter('the_content', 'gdk_esc_html', 2);
add_filter('comment_text', 'gdk_esc_html', 2);

//fancybox图片灯箱效果
if (gdk_option('gdk_lazyload')) {
    function gdk_fancybox($content)
    {
        $pattern = "/<a(.*?)href=('|\")([^>]*).(bmp|gif|jpeg|jpg|png|swf)('|\")(.*?)>(.*?)<\\/a>/i";
        $replacement = '<a$1href=$2$3.$4$5 data-fancybox="gallery" rel="box" class="fancybox"$6>$7</a>';

        return preg_replace($pattern, $replacement, $content);
    }
    add_filter('the_content', 'gdk_fancybox');
}

//GO跳转
function gdk_link_go($content)
{
    preg_match_all('/<a(.*?)href="(.*?)"(.*?)>/', $content, $matches);
    if ($matches) {
        foreach ($matches[2] as $val) {
            if (in_string($val, '://') && !in_string($val, home_url()) && !preg_match('/\.(jpg|jepg|png|ico|bmp|gif|zip|rar|tiff)/i', $val) && !preg_match('/(ed2k|thunder|Flashget|flashget|qqdl):\/\//i', $val)) {
                $content = str_replace("href=\"{$val}\"", 'href="'.home_url()."?go={$val}\" ", $content);
            }
        }
    }

    return $content;
}

//邮箱SMTP设置
function gdk_smtp($phpmailer)
{
    $phpmailer->FromName = gdk_option('gdk_smtp_username'); //昵称
    $phpmailer->Host = gdk_option('gdk_smtp_host'); //服务器地址
    $phpmailer->Port = gdk_option('gdk_smtp_port'); //端口
    $phpmailer->Username = gdk_option('gdk_smtp_mail'); //邮箱地址
    $phpmailer->Password = gdk_option('gdk_smtp_password'); //密码
    $phpmailer->From = gdk_option('gdk_smtp_mail'); //邮箱地址
    $phpmailer->SMTPAuth = true;
    $phpmailer->SMTPSecure = 'ssl';
    $phpmailer->IsSMTP();
}

// CDN
function gdk_cdn_start()
{
    ob_start('gdk_cdn_replace');
}
function gdk_cdn_replace($html)
{
    $local_host = home_url(); //博客域名
    $cdn_host = gdk_option('gdk_cdn_host'); //cdn域名
    $cdn_exts = gdk_option('gdk_cdn_ext'); //扩展名（使用|分隔）
    $cdn_dirs = gdk_option('gdk_cdn_dir'); //目录（使用|分隔）
    $cdn_dirs = str_replace('-', '\-', $cdn_dirs);
    if (isset( $cdn_dirs)) {
        $regex = '/'.str_replace('/', '\/', $local_host).'\/(('.$cdn_dirs.')\/[^\s\?\\\'\"\;\>\<]{1,}.('.$cdn_exts.'))([\"\\\'\s\?]{1})/';
        $html = preg_replace($regex, $cdn_host.'/$1$4', $html);
    } else {
        $regex = '/'.str_replace('/', '\/', $local_host).'\/([^\s\?\\\'\"\;\>\<]{1,}.('.$cdn_exts.'))([\"\\\'\s\?]{1})/';
        $html = preg_replace($regex, $cdn_host.'/$1$3', $html);
    }

    return $html;
}

//CDN水印
function gdk_cdn_water($content)
{
    if ('post' == get_post_type()) {
        $pattern = "/<img(.*?)src=('|\")(.*?).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>/i";
        $replacement = '<img$1src=$2$3.$4!water.jpg$5$6>';
        $content = preg_replace($pattern, $replacement, $content);
    }

    return $content;
}

//压缩html代码
if (gdk_option('gdk_compress')) {
    function gdk_compress_html()
    {
        function gdk_compress_html_callback($buffer)
        {
            if ('<?xml' == substr(ltrim($buffer), 0, 5)) {
                return $buffer;
            }

            $initial = strlen($buffer);
            $buffer = explode('<!--wp-compress-html-->', $buffer);
            $count = count($buffer);
            $i = '';
            for ($i = 0; $i <= $count; ++$i) {
                if (stristr($buffer[$i], '<!--wp-compress-html no compression-->')) {
                    $buffer[$i] = str_replace('<!--wp-compress-html no compression-->', ' ', $buffer[$i]);
                } else {
                    $buffer[$i] = str_replace("\t", ' ', $buffer[$i]);
                    $buffer[$i] = str_replace("\n\n", "\n", $buffer[$i]);
                    $buffer[$i] = str_replace("\n", '', $buffer[$i]);
                    $buffer[$i] = str_replace("\r", '', $buffer[$i]);
                    while (stristr($buffer[$i], '  ')) {
                        $buffer[$i] = str_replace('  ', ' ', $buffer[$i]);
                    }
                }
                $buffer_out .= $buffer[$i];
            }
            $final = strlen($buffer_out);
            if (0 !== $initial) {
                $savings = ($initial - $final) / $initial * 100;
            } else {
                $savings = 0;
            }
            $savings = round($savings, 2);
            $buffer_out .= "\n<!--压缩前的大小: {$initial} bytes; 压缩后的大小: {$final} bytes; 节约：{$savings}% -->";

            return $buffer_out;
        }
        ob_start('gdk_compress_html_callback');
    }
    add_action('get_header', 'gdk_compress_html');

    function gdk_unCompress($content)
    {
        if (preg_match_all('/(crayon-|<?xml|script|textarea|<\\/pre>)/i', $content, $matches)) {
            $content = '<!--wp-compress-html--><!--wp-compress-html no compression-->'.$content;
            $content .= '<!--wp-compress-html no compression--><!--wp-compress-html-->';
        }

        return $content;
    }
    add_filter('the_content', 'gdk_unCompress');
}

//只搜索文章标题
function gdk_search_by_title($search, $wp_query)
{
    if (!empty($search) && !empty($wp_query->query_vars['search_terms'])) {
        global $wpdb;
        $q = $wp_query->query_vars;
        $n = !empty($q['exact']) ? '' : '%';
        $search = [];
        foreach ((array) $q['search_terms'] as $term) {
            $search[] = $wpdb->prepare("{$wpdb->posts}.post_title LIKE %s", $n.$wpdb->esc_like($term).$n);
        }
        if (!is_user_logged_in()) {
            $search[] = "{$wpdb->posts}.post_password = ''";
        }
        $search = ' AND '.implode(' AND ', $search);
    }

    return $search;
}
add_filter('posts_search', 'gdk_search_by_title', 10, 2);

//评论地址更换
function gdk_comment_author($query_vars)
{
    if (array_key_exists('author_name', $query_vars)) {
        global $wpdb;
        $author_id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = 'first_name' AND meta_value = %s", $query_vars['author_name']));
        if ($author_id) {
            $query_vars['author'] = $author_id;
            unset($query_vars['author_name']);
        }
    }

    return $query_vars;
}
add_filter('request', 'gdk_comment_author');

function gdk_comment_author_link($link, $author_id, $author_nicename)
{
    $my_name = get_user_meta($author_id, 'first_name', true);
    if (isset($my_name)) {
        $link = strtr($link, [$author_nicename => $my_name]);
    }

    return $link;
}
add_filter('author_link', 'gdk_comment_author_link', 10, 3);

function gdk_weauth_page_activate()
{
    $awesome_page_id = get_option('weixin_page_id');
    if (!$awesome_page_id) {
        $post = [
            'post_title' => '微信登录', //这里是自动生成页面的页面标题
            'post_content' => '[gdk_login_btn]', //这里是页面的内容
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_name' => 'weixin',
        ];
        $postID = wp_insert_post($post);
        update_post_meta($postID, '_wp_page_template', ''); //这里是生成页面的模板类型
        update_option('weixin_page_id', $postID);
    }
}
add_action('admin_init', 'gdk_weauth_page_activate');

//强制微信登录
function gdk_force_weauth_login_url($login_url, $redirect, $force_reauth)
{
    $login_url = get_permalink(get_option('weixin_page_id'));
    if (!empty($redirect)) {
        $login_url = add_query_arg('redirect_to', urlencode($redirect), $login_url);
    }
    if ($force_reauth) {
        $login_url = add_query_arg('reauth', '1', $login_url);
    }

    return $login_url;
}

function gdk_change_register_url($url)
{
    if (is_admin()) {
        return $url;
    }

    return wp_login_url();
}

if (gdk_option('gdk_weauth_oauth') && gdk_option('gdk_weauth_force')) {
    add_filter('login_url', 'gdk_force_weauth_login_url', 10, 3);
    add_filter('register_url', 'gdk_change_register_url');
}

/**
 * 清除文章相关的缓存.
 */
function gdk_clear_post_cache()
{
    delete_transient('gdk-sitemap');
    delete_transient('gdk-sitemap-html');
}
add_action('save_post', 'gdk_clear_post_cache');
add_action('deleted_post', 'gdk_clear_post_cache');
add_action('publish_post', 'gdk_clear_post_cache');
add_action('publish_page', 'gdk_clear_post_cache');

/**
 * 清除链接缓存.
 */
function gdk_clear_link_cache()
{
    delete_transient('gdk-daohang-html');
}
add_action('edit_link', 'gdk_clear_link_cache');
add_action('add_link', 'gdk_clear_link_cache');

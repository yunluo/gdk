<?php
/**
 * 字符串截取，支持中文和其他编码
 *
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断字符串后缀
 * @return string
 */
function nc_substr_ext($str, $start = 0, $length = 0, $charset = 'utf-8', $suffix = '')
{
    if (function_exists("mb_substr")) {
        return mb_substr($str, $start, $length, $charset).$suffix;
    } elseif (function_exists('iconv_substr')) {
        return iconv_substr($str, $start, $length, $charset).$suffix;
    }
    $re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("", array_slice($match[0], $start, $length));
    return $slice.$suffix;
}

function nc_reverse_strrchr($haystack, $needle, $trail)
{
    $length = (strrpos($haystack, $needle) + $trail);
    return strrpos($haystack, $needle) ? substr($haystack, 0, $length) : false;
}

/**
 * 获取完整的句子
 */
function nc_print_excerpt($length, $post = null, $echo = true)
{
    global $post;
    $text = $post->post_excerpt;

    if ('' == $text) {
        $text = get_the_content();
        $text = strip_shortcodes($text);
        $text = apply_filters('the_content', $text);
        $text = str_replace(']]>', ']]>', $text);
    }

    $text = strip_shortcodes($text);
    $text = strip_tags($text);

    $text = nc_substr_ext($text, 0, $length);
    $excerpt = nc_reverse_strrchr($text, '。', 3);

    if ($excerpt) {
        $result = strip_tags(apply_filters('the_excerpt', $excerpt)).'...';
    } else {
        $result = strip_tags(apply_filters('the_excerpt', $text)).'...';
    }
    if ($echo == true) {
        echo $result;
    } else {
        return $result;
    }
}

function nc_comment_add_at($comment_text, $comment = '')
{
    if (!empty($comment) && $comment->comment_parent > 0) {
        $comment_text = '<a rel="nofollow" class="comment_at" href="#comment-' . $comment->comment_parent . '">@'.get_comment_author($comment->comment_parent) . '</a> ' . $comment_text;
    }
    return $comment_text;
}

function nc_record_visitors()
{
    if (is_singular()) {
        global $post;
        $post_ID = $post->ID;
        if ($post_ID) {
            $post_views = (int)get_post_meta($post_ID, 'views', true);
            if (!update_post_meta($post_ID, 'views', ($post_views+1))) {
                add_post_meta($post_ID, 'views', 1, true);
            }
        }
    }
}

function nc_post_views($before = '(点击 ', $after = ' 次)', $echo = 1)
{
    global $post;
    $post_ID = $post->ID;
    $views = (int)get_post_meta($post_ID, 'views', true);
    if ($echo) {
        echo $before, number_format($views), $after;
    } else {
        return $views;
    }
}

/**
 * Load a component into a template while supplying data.
 *
 * @param string $slug The slug name for the generic template.
 * @param array $params An associated array of data that will be extracted into the templates scope
 * @param bool $output Whether to output component or return as string.
 * @return string
 */
function nc_get_template_part_with_vars($slug, array $params = array(), $output = true)
{
    if (!$output) {
        ob_start();
    }
    $template_file = locate_template("{$slug}.php", false, false);
    extract(array('template_params' => $params), EXTR_SKIP);
    require($template_file);
    if (!$output) {
        return ob_get_clean();
    }
}

function nc_ajax_comment_callback()
{
    global $wpdb;
    $comment_post_ID = isset($_POST['comment_post_ID']) ? (int) $_POST['comment_post_ID'] : 0;
    $post = get_post($comment_post_ID);
    $post_author = $post->post_author;
    if (empty($post->comment_status)) {
        do_action('comment_id_not_found', $comment_post_ID);
        nc_ajax_comment_err('Invalid comment status.');
    }
    $status = get_post_status($post);
    $status_obj = get_post_status_object($status);
    if (!comments_open($comment_post_ID)) {
        do_action('comment_closed', $comment_post_ID);
        nc_ajax_comment_err(__('Sorry, comments are closed.', 'jimu'));
    } elseif ('trash' == $status) {
        do_action('comment_on_trash', $comment_post_ID);
        nc_ajax_comment_err(__('Unknown error.', 'jimu'));
    } elseif (!$status_obj->public && !$status_obj->private) {
        do_action('comment_on_draft', $comment_post_ID);
        nc_ajax_comment_err(__('Unknown error.', 'jimu'));
    } elseif (post_password_required($comment_post_ID)) {
        do_action('comment_on_password_protected', $comment_post_ID);
        nc_ajax_comment_err(__('Password protected.', 'jimu'));
    } else {
        do_action('pre_comment_on_post', $comment_post_ID);
    }

    $comment_author       = (isset($_POST['author']))  ? trim(strip_tags($_POST['author'])) : null;
    $comment_author_email = (isset($_POST['email']))   ? trim($_POST['email']) : null;
    $comment_author_url   = (isset($_POST['url']))     ? trim($_POST['url']) : null;
    $comment_content      = (isset($_POST['comment'])) ? trim($_POST['comment']) : null;
    $user = wp_get_current_user();
    $user_ID = $user->ID;
    if ($user->exists()) {
        if (empty($user->display_name)) {
            $user->display_name=$user->user_login;
        }
        $comment_author       = esc_sql($user->display_name);
        $comment_author_email = esc_sql($user->user_email);
        $comment_author_url   = esc_sql($user->user_url);
        $user_ID              = esc_sql($user->ID);
    } else {
        if (get_option('comment_registration') || 'private' == $status) {
            nc_ajax_comment_err('<p>'.__('Sorry, you must be logged in to leave a comment', 'jimu').'</p>');
        } // 抱歉，您必须登录后才能发表评论。
    }
    $comment_type = '';
    if (get_option('require_name_email') && !$user->exists()) {
        if (6 > strlen($comment_author_email) || '' == $comment_author) {
            nc_ajax_comment_err('<p>'.__('Please fill in the required options (Name, Email).', 'jimu').'</p>');
        } // 错误：请填写必须的选项（姓名，电子邮件）。
        elseif (!is_email($comment_author_email)) {
            nc_ajax_comment_err('<p>'.__('Please input a valid email address.', 'jimu').'</p>');
        } // 错误：请输入有效的电子邮件地址。
    }
    if ('' == $comment_content) {
        nc_ajax_comment_err('<p>'.__('Say something...', 'jimu').'</p>');
    } // 说点什么吧
    $dupe = "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = '$comment_post_ID' AND ( comment_author = '$comment_author' ";
    if ($comment_author_email) {
        $dupe .= "OR comment_author_email = '$comment_author_email' ";
    }
    $dupe .= ") AND comment_content = '$comment_content' LIMIT 1";
    if ($wpdb->get_var($dupe)) {
        nc_ajax_comment_err('<p>'.__('Please do not repeat your comments. :)', 'jimu').'</p>'); // Do not repeat comments aha~似乎说过这句话了
    }

    if ($lasttime = $wpdb->get_var($wpdb->prepare("SELECT comment_date_gmt FROM $wpdb->comments WHERE comment_author = %s ORDER BY comment_date DESC LIMIT 1", $comment_author))) {
        $time_lastcomment = mysql2date('U', $lasttime, false);
        $time_newcomment  = mysql2date('U', current_time('mysql', 1), false);
        $flood_die = apply_filters('comment_flood_filter', false, $time_lastcomment, $time_newcomment);
        if ($flood_die) {
            nc_ajax_comment_err('<p>'.__('You reply too fast. Take it easy.', 'jimu').'</p>'); // 你回复太快啦。慢慢来。
        }
    }
    $comment_parent = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;
    $commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID');

    $comment_id = wp_new_comment($commentdata);


    $comment = get_comment($comment_id);
    do_action('set_comment_cookies', $comment, $user, true);
    $comment_depth = 1;
    $tmp_c = $comment;
    while ($tmp_c->comment_parent != 0) {
        $comment_depth++;
        $tmp_c = get_comment($tmp_c->comment_parent);
    }
    $GLOBALS['comment'] = $comment;
    get_template_part('comment'); ?>
    <?php
        die();
}

function nc_ajax_comment_err($a)
{
    header('HTTP/1.0 500 Internal Server Error');
    header('Content-Type: text/plain;charset=UTF-8');
    echo $a;
    exit;
}

function nc_ajax_load_comments()
{
    global $wp_query;

    $type  = sanitize_text_field($_POST['type']);
    $paged = sanitize_text_field($_POST['paged']);

    $q = sanitize_text_field($_POST['query']);

    if ($paged < 1 || $paged > $_POST['commentcount']) {
        wp_die();
    }

    if ($type === 'page') {
        $wp_query = new WP_Query(array( 'page_id' => $q, 'cpage' => $paged ));
    }

    if ($type === 'post') {
        $wp_query = new WP_Query(array( 'p' => $q, 'cpage' => $paged ));
    }

    if (have_posts()) {
        while (have_posts()) {
            the_post();
            comments_template();
        }
    }

    wp_reset_postdata();
    wp_die();
}

/**
 * 获取评论下一页页码
 */
function nc_get_next_page_number()
{
    $page_number = get_comment_pages_count();
    if (get_option('default_comments_page') == 'newest') {
        $next_page = $page_number - 1;
    } else {
        $next_page = 2;
    }
    return $next_page;
}

function nc_like_init($key, $direct = false)
{
    // $direct === true 时不计 cookie
    $id = $_POST["id"];
    $action = $_POST["do_action"];
    $lh_raters = get_post_meta($id, $key, true);
    $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;

    if ($action == 'do') {
        $expire = time() + 99999999;
        if (!isset($_COOKIE[$key.'_'.$id]) || $direct) {
            setcookie($key.'_'.$id, $id, $expire, '/', $domain, false);
            if (!$lh_raters || !is_numeric($lh_raters)) {
                update_post_meta($id, $key, 1);
            } else {
                update_post_meta($id, $key, ($lh_raters + 1));
            }
        }
    }
    if ($action == 'undo' && !$direct) {
        $expire = time() - 1;
        if (isset($_COOKIE[$key.'_'.$id])) {
            setcookie($key.'_'.$id, $id, $expire, '/', $domain, false);
            update_post_meta($id, $key, ($lh_raters - 1));
        }
    }
    echo get_post_meta($id, $key, true);
    die;
}

function nc_timeago($ptime = null, $post = null)
{
    if ($post === null) {
        global $post;
    }
    $ptime = $ptime ?: get_post_time('G', false, $post);
    return human_time_diff($ptime, current_time('timestamp')) . '前';
}

function nc_get_translated_role_name($user_id)
{
    $data  = get_userdata($user_id);
    $roles = $data->roles;
    if (in_array('administrator', $roles)) {
        return __('Administrator', 'jimu');
    } elseif (in_array('editor', $roles)) {
        return __('Certified Editor', 'jimu');
    } elseif (in_array('author', $roles)) {
        return __('Special Author', 'jimu');
    } elseif (in_array('subscriber', $roles)) {
        return __('Subscriber', 'jimu');
    }

    return __('Contributor', 'jimu');
}

function nc_get_meta($key, $single = true) {
    global $post;
    return get_post_meta($post->ID, $key, $single);
}

function nc_the_meta($key, $placeholder = '') {
    echo nc_get_meta($key, true) ?: $placeholder;
}

function gdk_is_mobile() {
    $ua = $_SERVER['HTTP_USER_AGENT'];
    if (empty($ua)) {
        return false;
    } elseif ((in_string($ua, 'Mobile') && strpos($ua, 'iPad') === false) // many mobile devices (all iPh, etc.)
     || in_string($ua, 'Android') || in_string($ua, 'NetType/') || in_string($ua, 'Kindle') || in_string($ua, 'MQQBrowser') || in_string($ua, 'Opera Mini') || in_string($ua, 'Opera Mobi') || in_string($ua, 'HUAWEI') || in_string($ua, 'TBS/') || in_string($ua, 'Mi') || in_string($ua, 'iPhone')) {
        return true;
    } else {
        return false;
    }
}

if (function_exists('curl_init')) {
    function curl_post($url, $postfields = '', $headers = '', $timeout = 20, $file = 0) {
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_NOBODY => false,
            CURLOPT_POST => true,
            CURLOPT_MAXREDIRS => 20,
            CURLOPT_USERAGENT => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36',
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        );
        if (is_array($postfields) && $file == 0) {
            $options[CURLOPT_POSTFIELDS] = http_build_query($postfields);
        } else {
            $options[CURLOPT_POSTFIELDS] = $postfields;
        }
        curl_setopt_array($ch, $options);
        if (is_array($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $result = curl_exec($ch);
        $code = curl_errno($ch);
        $msg = curl_error($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return array(
            'data' => $result,
            'code' => $code,
            'msg' => $msg,
            'info' => $info
        );
    }
}

//判断是否是登陆页面
function is_login() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}

//判断字符串内是否有指定字符串
function in_string($text,$find) {
	if(strpos($text,$find) !== false) {
		return true;
	} else {
		return false;
	}
}

function getBrowser() {
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = $u_agent;
    $platform = '';
    $version= '';

    //First get the platform?
    if ( preg_match( '/linux/i', $u_agent ) ) {
        $platform = 'Linux';
    }
    elseif ( preg_match( '/macintosh|mac os x/i', $u_agent ) ) {
        $platform = 'Mac';
    }
    elseif ( preg_match( '/windows|win32/i', $u_agent ) ) {
        $platform = 'Windows';
    }

    // Next get the name of the useragent yes seperately and for good reason
    if ( preg_match( '/MSIE/i',$u_agent) && ! preg_match( '/Opera/i',$u_agent ) ) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    } elseif( preg_match( '/Firefox/i',$u_agent ) ) {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif( preg_match( '/Chrome/i',$u_agent ) ) {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    } elseif( preg_match( '/Safari/i',$u_agent ) ) {
        $bname = 'Apple Safari';
        $ub = "Safari";
    } elseif( preg_match( '/Opera/i',$u_agent ) ) {
        $bname = 'Opera';
        $ub = "Opera";
    } elseif( preg_match( '/Netscape/i',$u_agent ) ) {
        $bname = 'Netscape';
        $ub = "Netscape";
    }

    // finally get the correct version number
    $known = array( 'Version', $ub, 'other');
    $pattern = '#( ?<browser>' . join( '|', $known) .
    ')[/ ]+( ?<version>[0-9.|a-zA-Z.]*)#';

    if ( ! preg_match_all( $pattern, $u_agent, $matches ) ) {
        // we have no matching number just continue
    }

    if ( isset( $matches['browser'] ) && is_array($matches['browser'])  ) {
        // see how many we have
        $i = count( $matches['browser'] );

        if ( $i != 1) {

            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if ( strripos( $u_agent,"Version") < strripos( $u_agent,$ub ) ){
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }
    } else {
        $version="?";
    }

    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'   => $pattern
    );
}


function get_ip_address( ) {
    // check for shared internet/ISP IP
    if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) && validate_ip( $_SERVER['HTTP_CLIENT_IP'] ) ) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    // check for IPs passing through proxies
    if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {

        // check if multiple ips exist in var
        if ( in_string( $_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
            $iplist = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ( $iplist as $ip) {
                if ( validate_ip( $ip ) )
                    return $ip;
            }
        } else {
            if ( validate_ip( $_SERVER['HTTP_X_FORWARDED_FOR'] ) )
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    }
    if ( ! empty( $_SERVER['HTTP_X_FORWARDED']) && validate_ip( $_SERVER['HTTP_X_FORWARDED'] ) ) {
        return $_SERVER['HTTP_X_FORWARDED'];
    }
    if ( ! empty( $_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && validate_ip( $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'] ) ) {
        return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    }
    if ( ! empty( $_SERVER['HTTP_FORWARDED_FOR']) && validate_ip( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
        return $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if ( ! empty( $_SERVER['HTTP_FORWARDED']) && validate_ip( $_SERVER['HTTP_FORWARDED'] ) ) {
        return $_SERVER['HTTP_FORWARDED'];
    }

    // return unreliable ip since all else failed
    return $_SERVER['REMOTE_ADDR'];
}

/**
 * Ensures an ip address is both a valid IP and does not fall within
 * a private network range.
 */
function validate_ip( $ip) {
    if ( strtolower( $ip) === 'unknown') {
        return false;
    }

    // generate ipv4 network address
    $ip = ip2long( $ip);

    // if the ip is set and not equivalent to 255.255.255.255
    if ( $ip !== false && $ip !== -1) {
        // make sure to get unsigned long representation of ip
        // due to discrepancies between 32 and 64 bit OSes and
        // signed numbers ( ints default to signed in PHP)
        $ip = sprintf( '%u', $ip);
        // do private network range checking
        if ( $ip >= 0 && $ip <= 50331647) return false;
        if ( $ip >= 167772160 && $ip <= 184549375) return false;
        if ( $ip >= 2130706432 && $ip <= 2147483647) return false;
        if ( $ip >= 2851995648 && $ip <= 2852061183) return false;
        if ( $ip >= 2886729728 && $ip <= 2887778303) return false;
        if ( $ip >= 3221225984 && $ip <= 3221226239) return false;
        if ( $ip >= 3232235520 && $ip <= 3232301055) return false;
        if ( $ip >= 4294967040) return false;
    }
    return true;
}

//Ajax报错方式
function gdk_die($ErrMsg) {
    header('HTTP/1.1 405 Method Not Allowed');
    echo $ErrMsg;
    exit;
}
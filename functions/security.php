<?php

//define('DISALLOW_FILE_MODS',true);

//阻止异常请求
function gdk_prevent_requst()
{
    global $user_ID;
    if (!current_user_can('level_10')) {
        if (strlen($_SERVER['REQUEST_URI']) > 255
            || stripos($_SERVER['REQUEST_URI'], 'eval(')
            || stripos($_SERVER['REQUEST_URI'], 'CONCAT')
            || stripos($_SERVER['REQUEST_URI'], 'UNION+SELECT')
            || stripos($_SERVER['REQUEST_URI'], 'GLOBALS(')
            || stripos($_SERVER['REQUEST_URI'], '_REQUEST')
            || stripos($_SERVER['REQUEST_URI'], '/localhost')
            || stripos($_SERVER['QUERY_STRING'], '127.0.0.1')
            || stripos($_SERVER['REQUEST_URI'], '/config.')
            || stripos($_SERVER['REQUEST_URI'], 'wp-config.')
            || stripos($_SERVER['REQUEST_URI'], 'etc/passwd')
            || stripos($_SERVER['REQUEST_URI'], '<')
            || stripos($_SERVER['REQUEST_URI'], 'base64')) {
            @header('HTTP/1.1 403 Forbidden');
            @header('Status: 403 Forbidden');
            @header('Connection: Close');
            @exit;
        }
    }
}
if (gdk_option('gdk_block_requst')) {
    add_action('wp', 'gdk_prevent_requst'); //阻止乱七八糟的请求
}

//禁用 XML-RPC 接口
if (gdk_option('gdk_disable_xmlrpc')) {
    add_filter('xmlrpc_enabled', '__return_false');
    remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
}

//彻底关闭 pingback
if (gdk_option('gdk_disable_trackbacks')) {
    add_filter('xmlrpc_methods', 'gdk_xmlrpc_methods');
    function gdk_xmlrpc_methods($methods)
    {
        unset($methods['system.multicall']);
        $methods['pingback.ping'] = '__return_false';
        $methods['pingback.extensions.getPingbacks'] = '__return_false';

        return $methods;
    }

    //阻止站内PingBack
    function gdk_noself_ping(&$links)
    {
        $home = home_url();
        foreach ($links as $l => $link) {
            if (0 === strpos($link, $home)) {
                unset($links[$l]);
            }
        }
    }
    add_action('pre_ping', 'gdk_noself_ping');
    //禁用 pingbacks, enclosures, trackbacks
    remove_action('do_pings', 'do_all_pings', 10);
    //去掉 _encloseme 和 do_ping 操作。
    remove_action('publish_post', '_publish_post_hook', 5);
}

//隐藏用户昵称
add_filter('redirect_canonical', 'security_stop_user_enumeration', 10, 2);
if (!function_exists('security_stop_user_enumeration')) {
    function security_stop_user_enumeration($redirect, $request)
    {
        if (preg_match('/\?author=([0-9]*)(\/*)/i', $request)) {
            wp_redirect(get_site_url(), 301);

            exit();
        }

        return $redirect;
    }
}

//登陆错误锁定
if (!class_exists('GDK_Limit_Login_Attempts')) {
    class GDK_Limit_Login_Attempts
    {
        public $transient_name = 'attempted_login'; //Transient used
        private $__failed_login_limit; //登录失败的次数限制
        private $__lockout_duration; //暂停登陆时间

        public function __construct($config = null)
        {
            $this->failed_login_limit = $config['failed_login_limit'];
            $this->lockout_duration = $config['lockout_duration'];
            add_filter('authenticate', [$this, 'check_attempted_login'], 30, 3);
            add_action('wp_login_failed', [$this, 'login_failed'], 10, 1);
        }

        public function check_attempted_login($user, $username, $password)
        {
            if (get_transient($this->transient_name)) {
                $datas = get_transient($this->transient_name);
                if ($datas['tried'] >= $this->failed_login_limit) {
                    $until = get_option('_transient_timeout_'.$this->transient_name);
                    $time = $this->when($until);
                    //Display error message to the user when limit is reached
                    return new WP_Error('too_many_tried', sprintf(esc_attr('ERROR：您已触发登陆安全保护，请在 %1$s 后再次尝试.'), $time));
                }
            }

            return $user;
        }

        public function login_failed($username)
        {
            if (get_transient($this->transient_name)) {
                $datas = get_transient($this->transient_name);
                ++$datas['tried'];
                if ($datas['tried'] <= $this->failed_login_limit) {
                    set_transient($this->transient_name, $datas, $this->lockout_duration);
                }
            } else {
                $datas = ['tried' => 1];
                set_transient($this->transient_name, $datas, $this->lockout_duration);
            }
        }

        private function when($time)
        {
            if (!$time) {
                return;
            }

            $right_now = time();
            $diff = abs($right_now - $time);
            $second = 1;
            $minute = $second * 60;
            $hour = $minute * 60;
            $day = $hour * 24;
            if ($diff < $minute) {
                return floor($diff / $second).' '.esc_attr('秒');
            }

            if ($diff < $minute * 2) {
                return esc_attr('about 1 minute ago');
            }

            if ($diff < $hour) {
                return floor($diff / $minute).' '.esc_attr('分钟');
            }

            if ($diff < $hour * 2) {
                return esc_attr('about 1 hour');
            }

            return floor($diff / $hour).' '.esc_attr('小时');
        }
    }
}
//Enable it:
$LLA_config = [
    'failed_login_limit' => gdk_option('gdk_failed_login_limit'), // 登录失败的次数限制
    'lockout_duration' => gdk_option('gdk_lockout_duration'), // 暂停登陆时间
];
if (gdk_option('gdk_lock_login')) {
    new GDK_Limit_Login_Attempts($LLA_config);
}

//禁用登陆错误信息
function gdk_disable_login_errors($error)
{
    global $errors;
    $err_codes = $errors->get_error_codes();
    if (!array_key_exists('too_many_tried', $err_codes)) {
        // For security reason
        return esc_attr('Access Denied!');
    }

    return $error;
}
//add_filter('login_errors', 'gdk_disable_login_errors');

//网站维护代码
function gdk_maintenance_mode()
{
    if (!current_user_can('edit_themes') || !is_user_logged_in()) {
        wp_die('网站维护中ing,   没事儿您就别来啦……', 'Maintenance - Could you please not disturb me ', ['response' => '503']);
    }
}
if (gdk_option('gdk_maintenance_mode')) {
    add_action('get_header', 'gdk_maintenance_mode');
}

//各种措施拦截垃圾评论
if (gdk_option('gdk_fuck_spam')) {
    //拦截无来路的评论
    function gdk_comment_check_referrer()
    {
        if (!isset($_SERVER['HTTP_REFERER']) || '' == $_SERVER['HTTP_REFERER']) {
            wp_die(esc_attr('Please enable referrers in your browser!'));
        }
    }
    add_action('check_comment_flood', 'gdk_comment_check_referrer');
    //拦截超长链接垃圾评论
    function gdk_url_spamcheck($approved, $commentdata)
    {
        return (strlen($commentdata['comment_author_url']) > 50) ? 'spam' : $approved;
    }
    add_filter('pre_comment_approved', 'gdk_url_spamcheck', 99, 2);
    function gdk_comment_lang($commentdata)
    {
        if (is_user_logged_in()) {
            return $commentdata;
        }

        $pattern = '/[一-龥]/u';
        // 禁止全英文评论
        if (!preg_match($pattern, $commentdata['comment_content'])) {
            gdk_die('您的评论中必须包含汉字!');
        }
        $pattern = '/[あ-んア-ン]/u';
        // 禁止日文评论
        if (preg_match($pattern, $commentdata['comment_content'])) {
            gdk_die('评论禁止包含日文!');
        }
        //屏蔽评论里面黑名单内容
        if (wp_check_comment_disallowed_list($commentdata['comment_author'], $commentdata['comment_author_email'], $commentdata['comment_author_url'], $commentdata['comment_content'], $commentdata['comment_author_IP'], $commentdata['comment_agent'])) {
            gdk_die('不好意思，您的评论违反本站评论规则');
        }

        return $commentdata;
    }
    add_filter('preprocess_comment', 'gdk_comment_lang');
}

//隐藏用户名字
if (gdk_option('gdk_hide_user_name')) {
    // 文本加密
    function gdk_text_encrypt($string, $operation, $key = '')
    {
        $string = 'D' == $operation ? str_replace(['!', '-', '_'], ['=', '+', '/'], $string) : $string;
        $key = md5($key);
        $key_length = strlen($key);
        $string = 'D' == $operation ? base64_decode($string) : substr(md5($string.$key), 0, 8).$string;
        $string_length = strlen($string);
        $rndkey = $box = [];
        $result = '';
        for ($i = 0; $i <= 255; ++$i) {
            $rndkey[$i] = ord($key[$i % $key_length]);
            $box[$i] = $i;
        }
        for ($j = $i = 0; $i < 256; ++$i) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $string_length; ++$i) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ('D' == $operation) {
            if (substr($result, 0, 8) == substr(md5(substr($result, 8).$key), 0, 8)) {
                return substr($result, 8);
            }

            return '';
        }

        return strtr(base64_encode($result), '=+/', '!-_');
    }

    function gdk_custom_author_link_request($query_vars)
    {
        if (array_key_exists('author_name', $query_vars)) {
            global $wpdb;
            $author_id = gdk_text_encrypt($query_vars['author_name'], 'D', AUTH_KEY);
            if (isset($author_id)) {
                $query_vars['author'] = $author_id;
                unset($query_vars['author_name']);
            }
        }

        return $query_vars;
    }
    add_filter('request', 'gdk_custom_author_link_request');

    function gdk_custom_author_link($link, $author_id)
    {
        global $wp_rewrite;
        $author_id = (int) $author_id;
        $link = $wp_rewrite->get_author_permastruct();
        if (empty($link)) {
            $link = home_url('/').'?author='.gdk_text_encrypt($author_id, 'E', AUTH_KEY);
        } else {
            $link = str_replace('%author%', gdk_text_encrypt($author_id, 'E', AUTH_KEY), $link);
            $link = home_url().user_trailingslashit($link);
        }

        return $link;
    }
    add_filter('author_link', 'gdk_custom_author_link', 10, 2);

    // wp-rest 可能暴露用户名
    function gdk_custom_rest_prepare_user($response, $user, $request)
    {
        $response->data['slug'] = gdk_text_encrypt($user->ID, 'E', AUTH_KEY);

        return $response;
    }
    add_filter('rest_prepare_user', 'gdk_custom_rest_prepare_user', 10, 3);
}

//记录登陆失败发邮件
if (gdk_option('gdk_login_email')) {
    add_action('wp_authenticate', 'gdk_log_login', 10, 2);
}
function gdk_log_login($username, $password)
{
    if (isset($username, $password)) {
        $check = wp_authenticate_username_password(null, $username, $password);
        if (is_wp_error($check)) {
            $ua = gdk_getBrowser();
            $agent = $ua['name'];

            $referrer = $_SERVER['HTTP_REFERER'] ?? $_SERVER['PHP_SELF'];
            if (strstr($referrer, 'wp-login')) {
                $ref = 'wp-login.php';
            }

            if (strstr($referrer, 'wp-admin')) {
                $ref = 'wp-admin/';
            }

            $contact_errors = false;
            // get the posted data
            $name = get_bloginfo('name');
            $email_address = get_bloginfo('admin_email');

            // write the email content
            $header = "MIME-Version: 1.0\n";
            $header .= "Content-Type: text/html; charset=utf-8\n";
            $header .= "From: {$name} <{$email_address}>\n";

            $message = "<a href='".home_url()."' target='_blank'>".$name.'</a> 登陆失败提醒<br>'.PHP_EOL;
            $message .= 'Browser: '.$agent.'<br>'.PHP_EOL;
            $message .= 'OS: '.$ua['platform'].'<br>'.PHP_EOL;
            $message .= 'IP:  <a href="https://www.ipip.net/ip/'.gdk_get_ip().'.html" target="_blank">'.gdk_get_ip().'</a><br>'.PHP_EOL;
            $message .= 'Date: '.date('Y-m-d H:i:s').'<br>'.PHP_EOL;
            $message .= 'Referrer: '.$referrer.'<br>'.PHP_EOL;
            $message .= 'User Agent: '.$ua['userAgent'].'<br>'.PHP_EOL;
            $message .= 'Username: '.$username.'<br>'.PHP_EOL;
            $message .= 'Password: '.$password.'<br>'.PHP_EOL;
            $subject = '登陆失败提醒 - '.$name;
            $mail_content = gdk_mail_temp($subject, $message, home_url(), $name);
            if (isset($email_address)) {
                // send the email using wp_mail()
                if (!wp_mail($email_address, $subject, $mail_content, $header)) {
                    $contact_errors = true;
                }
            }
        }
    }
}

//封禁用户
function gdk_edit_user_profile($user)
{
    if (!current_user_can('edit_users')) {
        return;
    }

    if (get_current_user_id() == $user->ID) {
        return;
    } ?>
<table class="form-table">
    <tr>
        <th scope="row">封禁用户</th>
        <td>
            <label for="gdk_ban">
                <input name="gdk_ban" type="checkbox" id="gdk_ban" <?php checked(gdk_is_user_banned($user->ID), true); ?>
                value="1">
                封禁此用户</label>
        </td>
    </tr>
</table>
<?php
}

function gdk_edit_user_profile_update($user_id)
{
    if (!current_user_can('edit_users')) {
        return;
    }

    if (get_current_user_id() == $user_id) {
        return;
    }

    if (empty($_POST['gdk_ban'])) {
        gdk_unban_user($user_id);
    } else {
        gdk_ban_user($user_id);
    }
}

function gdk_ban_user($user_id)
{
    if (!gdk_is_user_banned($user_id)) {
        update_user_option($user_id, 'gdk_banned', true, false);
    }
}

function gdk_unban_user($user_id)
{
    if (gdk_is_user_banned($user_id)) {
        update_user_option($user_id, 'gdk_banned', false, false);
    }
}

function gdk_is_user_banned($user_id)
{
    return get_user_option('gdk_banned', $user_id);
}

function gdk_authenticate_user($user, $password)
{
    if (is_wp_error($user)) {
        return $user;
    }

    if (get_user_option('gdk_banned', $user->ID, false)) {
        return new WP_Error(
            'gdk_banned',
            '<strong>ERROR</strong>: 此账号已被封禁.'
        );
    }

    return $user;
}

add_action('edit_user_profile', 'gdk_edit_user_profile');
add_action('edit_user_profile_update', 'gdk_edit_user_profile_update');
add_filter('wp_authenticate_user', 'gdk_authenticate_user', 10, 2);

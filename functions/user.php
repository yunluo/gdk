<?php



//修复 WordPress 找回密码提示“抱歉，该key似乎无效”
function gdk_reset_password_message($message, $key) {
    if (strpos($_POST['user_login'], '@')) {
        $user_data = get_user_by('email', trim($_POST['user_login']));
    } else {
        $login = trim($_POST['user_login']);
        $user_data = get_user_by('login', $login);
    }
    $user_login = $user_data->user_login;
    $msg = "有人要求重设如下帐号的密码：\r\n\r\n";
    $msg.= network_site_url() . "\r\n\r\n";
    $msg.= sprintf('用户名：%s', $user_login) . "\r\n\r\n";
    $msg.= "若这不是您本人要求的，请忽略本邮件，一切如常。\r\n\r\n";
    $msg.= "要重置您的密码，请打开下面的链接：\r\n\r\n";
    $msg.= wp_login_url() . "?action=rp&key=$key&login=" . rawurlencode($user_login);
    return $msg;
}
add_filter('retrieve_password_message', 'gdk_reset_password_message', null, 2);



if (!defined('UM_DIR')) { /*判断是否按照UM插件*/
    //注册表单
    function gdk_show_extra_register_fields() {
?>
    <p>
    <label for="password">密码<br/>
    <input id="password" class="input" type="password" tabindex="30" size="25" value="" name="password" />
    </label>
    </p>
    <p>
    <label for="repeat_password">确认密码<br/>
    <input id="repeat_password" class="input" type="password" tabindex="40" size="25" value="" name="repeat_password" />
    </label>
    </p>
    <?php
    }
    add_action('register_form', 'gdk_show_extra_register_fields');
    /*
     * Check the form for errors
    */
    function gdk_check_extra_register_fields($login, $email, $errors) {
        if ($_POST['password'] !== $_POST['repeat_password']) {
            $errors->add('passwords_not_matched', "<strong>错误提示</strong>: 两次填写密码不一致");
        }
        if (strlen($_POST['password']) < 8) {
            $errors->add('password_too_short', "<strong>错误提示</strong>: 密码必须大于8个字符");
        }
    }
    add_action('register_post', 'gdk_check_extra_register_fields', 10, 3);
    /*
     * 提交用户密码进数据库
    */
    function gdk_register_extra_fields($user_id) {
        $userdata = array();
        $userdata['ID'] = $user_id;
        if ($_POST['password'] !== '') {
            $userdata['user_pass'] = $_POST['password'];
        }
        $pattern = '/[一-龥]/u';
        if (preg_match($pattern, $_POST['user_login'])) {
            $userdata['user_nicename'] = $user_id;
        }
        $new_user_id = wp_update_user($userdata);
    }
    add_action('user_register', 'gdk_register_extra_fields', 100);
}

//后台登陆数学验证码
if (gdk_option('gdk_login_verify')) {
    function gdk_login_verify(){
        $num1 = mt_rand(0, 20);
        $num2 = mt_rand(0, 20);
        echo "<p><label for='sum'> {$num1} + {$num2} = ?<br /><input type='text' name='sum' class='input' value='' size='25' tabindex='4'>" . "<input type='hidden' name='num1' value='{$num1}'>" . "<input type='hidden' name='num2' value='{$num2}'></label></p>";
    }
    add_action('login_form', 'gdk_login_verify');
	add_action('register_form', 'gdk_login_verify');
	
    function gdk_login_verify_val(){
        $sum = $_POST['sum'];
        switch ($sum) {
            case $_POST['num1'] + $_POST['num2']:
                break;
            case null:
                wp_die('错误: 请输入验证码&nbsp; <a href="javascript:;" onclick="javascript:history.back();">返回上页</a>');
                break;
            default:
                wp_die('错误: 验证码错误,请重试&nbsp; <a href="javascript:;" onclick="javascript:history.back();">返回上页</a>');
        }
    }
    add_action('login_form_login', 'gdk_login_verify_val');
    add_action('register_post', 'gdk_login_verify_val');
}

//仅显示作者自己的文章
function gdk_show_mypost($wp_query) {
    if (strpos($_SERVER['REQUEST_URI'], '/wp-admin/edit.php') !== false) {
        if (!current_user_can('manage_options')) {
            $wp_query->set('author', get_current_user_id());
        }
    }
}
add_filter('parse_query', 'gdk_show_mypost');

//在文章编辑页面的[添加媒体]只显示用户自己上传的文件
function gdk_show_myupload($wp_query_obj) {
    global $pagenow;
    if (!is_a(wp_get_current_user(), 'WP_User')) return;
    if ('admin-ajax.php' != $pagenow || $_REQUEST['action'] != 'query-attachments') return;
    if (!current_user_can('manage_options') && !current_user_can('manage_media_library')) $wp_query_obj->set('author', get_current_user_id());
    return;
}
add_action('pre_get_posts', 'gdk_show_myupload');

//在[媒体库]只显示用户上传的文件
function gdk_show_myupload_library($wp_query) {
    if (strpos($_SERVER['REQUEST_URI'], '/wp-admin/upload.php') !== false) {
        if (!current_user_can('manage_options') && !current_user_can('manage_media_library')) {
            $wp_query->set('author', get_current_user_id());
        }
    }
}
add_filter('parse_query', 'gdk_show_myupload_library');


//添加后台个人信息
function gdk_contact_fields($contactmethods) {
    $contactmethods['qq'] = 'QQ';
    $contactmethods['sina_weibo'] = '新浪微博';
    $contactmethods['weixin'] = '微信';
    unset($contactmethods['yim']);
    unset($contactmethods['aim']);
    unset($contactmethods['jabber']);
    return $contactmethods;
}
add_filter('user_contactmethods', 'gdk_contact_fields');


//支持中文名注册，来自肚兜
function gdk_sanitize_user($username, $raw_username, $strict) {
    $username = wp_strip_all_tags($raw_username);
    $username = remove_accents($username);
    $username = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '', $username);
    $username = preg_replace('/&.+?;/', '', $username); // Kill entities
    if ($strict) {
        $username = preg_replace('|[^a-z\p{Han}0-9 _.\-@]|iu', '', $username);
    }
    $username = trim($username);
    $username = preg_replace('|\s+|', ' ', $username);
    return $username;
}
add_filter('sanitize_user', 'gdk_sanitize_user', 10, 3);

// 添加一个新的列 ID
function gdk_userid_column($cols) {
    $cols['ssid'] = 'ID';
    return $cols;
}
add_action('manage_users_columns', 'gdk_userid_column');
function gdk_userid_value($value, $column_name, $id) {
    if ($column_name == 'ssid') $value = $id;
    return $value;
}
add_filter('manage_users_custom_column', 'gdk_userid_value', 30, 3);
/**
 * WordPress 后台用户列表显示用户昵称
 * https://www.wpdaxue.com/add-user-nickname-column.html
 */
add_filter('manage_users_columns', 'gdk_add_user_nickname');
function gdk_add_user_nickname($columns) {
	$columns['user_nickname'] = '昵称';
	return $columns;
}
add_action('manage_users_custom_column',  'gdk_show_user_nickname_val', 20, 3);
function gdk_show_user_nickname_val($value, $column_name, $user_id) {
	$user = get_userdata( $user_id );
	$user_nickname = $user->nickname;
	if ( 'user_nickname' == $column_name )
		return $user_nickname;
	return $value;
}
//用户列表显示积分
add_filter('manage_users_columns', 'gdk_points_columns');
function gdk_points_columns($columns) {
    $columns['points'] = '金币';
    return $columns;
}
function gdk_points_value($value, $column_name, $user_id) {
    if ($column_name == 'points') {
        $jinbi = GDK_Points::get_user_total_points($user_id, 'accepted');
        if ($jinbi != "") {
            $ret = $jinbi;
            return $ret;
        } else {
            $ret = '暂无充值';
            return $ret;
        }
    }
    return $value;
}
add_action('manage_users_custom_column', 'gdk_points_value', 10, 3);

//用户增加评论数量
function gdk_users_comments($columns) {
    $columns['comments'] = '评论';
    return $columns;
}
add_filter('manage_users_columns', 'gdk_users_comments');
function gdk_show_users_comments($value, $column_name, $user_id) {
    if ($column_name == 'comments') {
        $comments_counts = get_comments(array(
            'status' => '1',
            'user_id' => $user_id,
            'count' => true
        ));
        if ($comments_counts != "") {
            $ret = $comments_counts;
            return $ret;
        } else {
            $ret = '暂未评论';
            return $ret;
        }
    }
    return $value;
}
add_action('manage_users_custom_column', 'gdk_show_users_comments', 10, 3);
// 添加一个字段保存IP地址
function gdk_log_ip($user_id) {
    $ip = gdk_get_ip();
    update_user_meta($user_id, 'signup_ip', $ip);
}
add_action('user_register', 'gdk_log_ip');
// 添加IP地址这个栏目
function gdk_signup_ip($column_headers) {
    $column_headers['signup_ip'] = 'IP地址';
    return $column_headers;
}
add_filter('manage_users_columns', 'gdk_signup_ip');
function gdk_ripms_columns($value, $column_name, $user_id) {
    if ($column_name == 'signup_ip') {
        $ip = get_user_meta($user_id, 'signup_ip', true);
        if ($ip != "") {
            $ret = $ip;
            return $ret;
        } else {
            $ret = '没有记录';
            return $ret;
        }
    }
    return $value;
}
add_action('manage_users_custom_column', 'gdk_ripms_columns', 10, 3);
// 创建一个新字段存储用户登录时间
function gdk_insert_last_login($login) {
    $user = get_user_by('login', $login);
    update_user_meta($user->ID, 'last_login', current_time('mysql'));
}
add_action('wp_login', 'gdk_insert_last_login');
// 添加一个新栏目上次登录
function gdk_add_last_login_column($columns) {
    $columns['last_login'] = '上次登录';
    unset($columns['name']);
    return $columns;
}
add_filter('manage_users_columns', 'gdk_add_last_login_column');
// 显示登录时间到新增栏目
function gdk_add_last_login($value, $column_name, $user_id) {
    if ($column_name == 'last_login') {
        $login = get_user_meta($user_id, 'last_login', true);
        if ($login != "") {
            $ret = $login;
            return $ret;
        } else {
            $ret = '暂未登录';
            return $ret;
        }
    }
    return $value;
}
add_action('manage_users_custom_column', 'gdk_add_last_login', 10, 3);
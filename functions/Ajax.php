<?php
/*
 *Ajax操作文件
 */

/**
 * 200 ok
 * 400 fail
 */

//后台邮箱检测
function gdk_test_email()
{
    $is_error = !wp_mail('donotreply@mywpku.com', '测试发信', 'WP 积木测试发信');
    if ($is_error) {
        exit('500');
    } else {
        exit('200');
    }
}
add_action('wp_ajax_nopriv_gdk_test_email', 'gdk_test_email');
add_action('wp_ajax_gdk_test_email', 'gdk_test_email');

//检测插件更新
function gdk_ajax_get_update()
{
    $response = wp_remote_get('https://u.gitcafe.net/api/gdk.json');

    if (!is_array($response) || is_wp_error($response)) {
        exit('400');
    }
    $plugin_info = json_decode($response['body'], true);
    $version     = $plugin_info['version'];

    if (version_compare($version, GDK_PLUGIN_VER, '>')) {
        exit('<span class="get_update_res">插件有更新，<a href="' . $plugin_info['details_url'] . '" target="_blank">请及时查看！</a></span>');
    } else {
        exit('<span class="get_update_res">你的插件目前已经是最新版了！</span>');
    }
}
add_action('wp_ajax_nopriv_get_new_version', 'gdk_ajax_get_update');
add_action('wp_ajax_get_new_version', 'gdk_ajax_get_update');

//粘贴上传图片
function gdk_pasteup_imag()
{
    if (!isset($_POST['pui_nonce']) || !wp_verify_nonce($_POST['pui_nonce'], 'pui-nonce')) {
        exit('400');
    }

    if ($_FILES) {
        global $post;
        $post_ID       = $post->ID;
        $wp_upload_dir = wp_upload_dir();
        $file          = $_FILES['imageFile'];
        $result        = array('success' => false, 'message' => 'Null');
        if (in_array($file['type'], array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png'))) {
            if ($file['error'] > 0) {
                $result['message'] = 'error';
            } else {
                $file_name = md5_file($file['tmp_name']) . str_replace('image/', '.', $file['type']); //img name
                $file_url  = $wp_upload_dir['url'] . '/' . $file_name;
                $file_path = $wp_upload_dir['path'] . '/' . $file_name;
                if (!file_exists($file_path)) {
                    move_uploaded_file($file['tmp_name'], $file_path);
                    $attachment = [
                        'guid'           => $wp_upload_dir['url'] . '/' . basename($file_path),
                        'post_mime_type' => $file['type'],
                        'post_title'     => $file_name,
                        'post_content'   => '',
                        'post_status'    => 'inherit',
                    ];
                    $attach_id = wp_insert_attachment($attachment, $file_name, $post_ID);
                    //这是wp内置的上传附件的函数
                    require_once ABSPATH . 'wp-admin/includes/image.php';
                    $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
                    wp_update_attachment_metadata($attach_id, $attach_data);
                }
                $result['success'] = true;
                $result['message'] = $file_url;
            }
        } else {
            $result['message'] = '400';
        }
        echo (json_encode($result));
        exit();
    }
}
add_action('wp_ajax_nopriv_gdk_pasteup_imag', 'gdk_pasteup_imag');
add_action('wp_ajax_gdk_pasteup_imag', 'gdk_pasteup_imag');

//密码可见
function gdk_pass_view()
{
    if (!isset($_POST['pass_nonce']) || !wp_verify_nonce($_POST['pass_nonce'], 'pass_nonce')) {
        exit('400');
    }

    $action    = $_POST['action'];
    $post_id   = $_POST['id'];
    $pass      = $_POST['pass'];
    $wxcaptcha = wx_captcha();
    if (!isset($action) || !isset($post_id) || !isset($pass)) {
        exit('400');
    }

    if ($pass == $wxcaptcha) {
        $pass_content = get_post_meta($post_id, '_pass_content')[0];
        exit($pass_content);
    } else {
        exit('400');
    }
}
add_action('wp_ajax_nopriv_gdk_pass_view', 'gdk_pass_view');
add_action('wp_ajax_gdk_pass_view', 'gdk_pass_view');

//密码可见end

//在线积分充值开始
function pay_points()
{
    if (!isset($_POST['action']) || $_POST['action'] !== 'pay_points') {
        exit('400');
    }

    if (!isset($_POST['money']) || !isset($_POST['way'])) {
        exit('400');
    }
//无脑输出400错误
    if (isset($_POST['id'])) {
        payjs_action('积分充值', $_POST['id']);
    }
}
add_action('wp_ajax_pay_points', 'pay_points');
add_action('wp_ajax_nopriv_pay_points', 'pay_points');

//检查积分充值
function check_pay_points()
{
    if (!isset($_POST['check_pay_points']) || !wp_verify_nonce($_POST['check_pay_points'], 'check_pay_points')) {
        exit('400');
    }

    if (!isset($_POST['id']) || !isset($_POST['orderid'])) {
        exit('400');
    }
//无脑输出400错误
    if ($_POST['action'] == 'check_pay_points') {
        if (gdk_check($_POST['orderid'], $_POST['id'])) {
            exit('200');
        } else {
            exit('400');
        }
    }
}
add_action('wp_ajax_check_pay_points', 'check_pay_points');
add_action('wp_ajax_nopriv_check_pay_points', 'check_pay_points');

//积分充值结束

//游客付费可见开始
function pay_view()
{
    if (!isset($_POST['action']) || $_POST['action'] !== 'pay_view') {
        exit('400');
    }

    if (!isset($_POST['money']) || !isset($_POST['way'])) {
        exit('400');
    }
//无脑输出400错误
    if (isset($_POST['id'])) {
        payjs_action('在线付费查看', 'PP' . $_POST['id']); //标题,文章id
    }
}
add_action('wp_ajax_pay_view', 'pay_view');
add_action('wp_ajax_nopriv_pay_view', 'pay_view');

//添加提取码
function add_code()
{
    $id   = $_POST['id'];
    $code = $_POST['code'];
    if (empty($id) || empty($code)) {
        exit('400');
    }

    if ($_POST['action'] == 'add_code') {
        $code    = trim($code); //清理一下
        $pay_log = get_post_meta($id, 'pay_log', true); //获取旧的购买记录数据
        add_post_meta($id, 'pay_log', $code, true) or update_post_meta($id, 'pay_log', $pay_log . ',' . $code); //没有新建,有就更新
        $pay_log = get_post_meta($id, 'pay_log', true); //获取新的购买记录数据
        $pay_arr = explode(",", $pay_log);
        if (in_array($code, $pay_arr)) {
            exit('200'); //OK
        } else {
            exit('400'); //NO
        }
    }
}
add_action('wp_ajax_add_code', 'add_code');
add_action('wp_ajax_nopriv_add_code', 'add_code');

//检测提取码
function check_code()
{
    $check_code_nonce = $_POST['check_code'];
    $code             = $_POST['code'];
    $action           = $_POST['action'];
    $id               = $_POST['id'];
    if (!isset($check_code_nonce) || !wp_verify_nonce($check_code_nonce, 'check_code')) {
        exit('400');
    }

    if (!isset($code) || !isset($action) || !isset($id)) {
        exit('400');
    }
//无脑输出400错误
    if ($action == 'check_code') {
        $code    = trim($code); //清理一下
        $pay_log = get_post_meta($id, 'pay_log', true); //购买记录数据
        $pay_arr = explode(",", $pay_log);
        if (in_array($code, $pay_arr)) {
            exit('200');
        } else {
            exit('400');
        }
    }
}
add_action('wp_ajax_check_code', 'check_code');
add_action('wp_ajax_nopriv_check_code', 'check_code');

//获取加密内容
function get_content()
{
    if (!isset($_POST['action']) || $_POST['action'] !== 'get_content') {
        exit('400');
    }
//无脑输出400错误
    if (isset($_POST['id'])) {
        $pay_content = get_post_meta($_POST['id'], '_pay_content', true);
        exit($pay_content);
    }
}
add_action('wp_ajax_get_content', 'get_content');
add_action('wp_ajax_nopriv_get_content', 'get_content');

//检查付费可见订单
function check_pay_view()
{
    if (!isset($_POST['check_pay_view']) || !wp_verify_nonce($_POST['check_pay_view'], 'check_pay_view')) {
        exit('400');
    }

    if (!isset($_POST['id']) || !isset($_POST['orderid'])) {
        exit('400');
    }
//无脑输出400错误
    if ($_POST['action'] == 'check_pay_view') {
        $sid = get_transient('PP' . $_POST['id']);
        if (in_string($sid, 'E20') && $_POST['orderid'] == $sid) {
            exit('200'); //OK
        } else {
            exit('400'); //no
        }
    }
}
add_action('wp_ajax_check_pay_view', 'check_pay_view');
add_action('wp_ajax_nopriv_check_pay_view', 'check_pay_view');

/**END */

//开始微信登陆
//ajax生成登录二维码
function gdk_weauth_qr_gen()
{
    if (isset($_POST['action']) && $_POST['action'] == 'gdk_weauth_qr_gen') {
        $rest = implode('|', gdk_weauth_qr());
        exit($rest);
    } else {
        exit('400');
    }
}
add_action('wp_ajax_gdk_weauth_qr_gen', 'gdk_weauth_qr_gen');
add_action('wp_ajax_nopriv_gdk_weauth_qr_gen', 'gdk_weauth_qr_gen');

//检查微信登录状况
function gdk_weauth_check()
{
    if (!isset($_POST['key']) || !isset($_POST['gdk_weauth_check']) || !wp_verify_nonce($_POST['gdk_weauth_check'], 'gdk_weauth_check')) {
        exit('400');
    }

    if (!in_string($_POST['key'], '@') || $_POST['action'] !== 'gdk_weauth_check') {
        exit('400');
    }

    $sk        = substr(trim($_POST['key']), -12); //sk
    $user_info = get_transient($sk . '-info'); //user_info
    if (!empty($user_info)) {
        exit($user_info); //user_info
    }
}
add_action('wp_ajax_gdk_weauth_check', 'gdk_weauth_check');
add_action('wp_ajax_nopriv_gdk_weauth_check', 'gdk_weauth_check');

//开始自动登陆
function gdk_auto_login()
{
    if (!isset($_POST['data']) || $_POST['action'] !== 'gdk_auto_login') {
        exit('400');
    }

    $mail     = $_POST['email'] ?? '';
    $userdata = gdk_str2arr($_POST['data'], '|');
    $user_id  = create_user_id($userdata);
    if (is_numeric($user_id) && $user_id) {
        $user = get_user_by('id', $user_id);
        wp_set_current_user($user_id, $user->user_login);
        wp_set_auth_cookie($user_id, true);
        do_action('wp_login', $user->user_login);
        if ($mail && !empty($mail) && is_email($mail)) {
            wp_update_user(array('ID' => $user_id, 'user_email' => $mail)); //绑定邮箱
        }
        exit('200');
    }
}
add_action('wp_ajax_gdk_auto_login', 'gdk_auto_login');
add_action('wp_ajax_nopriv_gdk_auto_login', 'gdk_auto_login');

//邮箱绑定
function bind_email_check()
{
    $mail = isset($_POST['email']) ? $_POST['email'] : false;
    if ($mail && $_POST['action'] == 'bind_email_check') {
        $user_id = email_exists($email);
        if ($user_id) {
            exit('200');
        }
    }
}
add_action('wp_ajax_bind_email_check', 'bind_email_check');
add_action('wp_ajax_nopriv_bind_email_check', 'bind_email_check');

function point_buy()
{
    if (isset($_POST['point']) && isset($_POST['userid']) && isset($_POST['id']) && $_POST['action'] == 'gdk_pay_buy') {
        GDK_Points::set_points(-$_POST['point'],
            $_POST['userid'],
            array(
                'description' => $_POST['id'],
                'status'      => get_option('points-points_status', 'accepted'),
            )
        ); //扣除金币
        $pay_content = get_post_meta($_POST['id'], '_point_content', true);
        exit($pay_content);
    }
}
add_action('wp_ajax_gdk_pay_buy', 'point_buy');
add_action('wp_ajax_nopriv_gdk_pay_buy', 'point_buy');

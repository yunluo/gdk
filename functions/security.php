<?php

//define('DISALLOW_FILE_MODS',true);

//阻止异常请求
function gdk_prevent_requst() {
	global $user_ID;
	if( ! current_user_can( 'level_10' )) {
		if ( strlen( $_SERVER['REQUEST_URI'] ) > 255 ||
		                stripos( $_SERVER['REQUEST_URI'], 'eval(' ) ||
		                stripos( $_SERVER['REQUEST_URI'], 'CONCAT' ) ||
		                stripos( $_SERVER['REQUEST_URI'], 'UNION+SELECT' ) ||
		                stripos( $_SERVER['REQUEST_URI'], 'GLOBALS(' ) ||
		                stripos( $_SERVER['REQUEST_URI'], '_REQUEST' ) ||
		                stripos( $_SERVER['REQUEST_URI'], '/localhost' ) ||
		                stripos( $_SERVER['QUERY_STRING'], '127.0.0.1' ) ||
		                stripos( $_SERVER['REQUEST_URI'], '/config.' ) ||
		                stripos( $_SERVER['REQUEST_URI'], 'wp-config.' ) ||
		                stripos( $_SERVER['REQUEST_URI'], 'etc/passwd' ) ||
		                stripos( $_SERVER['REQUEST_URI'], '<' ) ||
		                stripos( $_SERVER['REQUEST_URI'], 'base64' ) ) {
			@header( 'HTTP/1.1 403 Forbidden' );
			@header( 'Status: 403 Forbidden') ;
			@header( 'Connection: Close' );
			@exit;
		}
	}
}
if(gdk_option('gdk_block_requst')) {
	add_action( 'wp', 'gdk_prevent_requst' );//阻止乱七八糟的请求
}

//禁用 XML-RPC 接口
if (gdk_option('gdk_disable_xmlrpc')) {
	add_filter('xmlrpc_enabled', '__return_false');
	remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
}

//彻底关闭 pingback
if (gdk_option('gdk_disable_trackbacks')) {
	add_filter('xmlrpc_methods', 'gdk_xmlrpc_methods');
	function gdk_xmlrpc_methods($methods) {
		unset($methods['system.multicall']);
		$methods['pingback.ping']                    = '__return_false';
		$methods['pingback.extensions.getPingbacks'] = '__return_false';
		return $methods;
    }
    
//阻止站内PingBack
function gdk_noself_ping(&$links) {
	$home = home_url();
	foreach ($links as $l => $link) if (0 === strpos($link, $home)) unset($links[$l]);
}
add_action('pre_ping', 'gdk_noself_ping');
//禁用 pingbacks, enclosures, trackbacks
remove_action('do_pings', 'do_all_pings', 10);
//去掉 _encloseme 和 do_ping 操作。
remove_action('publish_post', '_publish_post_hook', 5);
}


//隐藏用户昵称
add_filter('redirect_canonical', 'security_stop_user_enumeration', 10, 2);
if ( ! function_exists( 'security_stop_user_enumeration' ) ) {
    function security_stop_user_enumeration( $redirect, $request ) {
        if ( preg_match( '/\?author=([0-9]*)(\/*)/i', $request ) ) {
            wp_redirect( get_site_url(), 301 );
            die();
        } else {
            return $redirect;
        }
    }
}


//登陆错误锁定
if ( ! class_exists( 'GDK_Limit_Login_Attempts' ) ) {
	class GDK_Limit_Login_Attempts {
		private $failed_login_limit;//登录失败的次数限制
		private $lockout_duration;//暂停登陆时间
		var $transient_name     = 'attempted_login';//Transient used
		public function __construct($config = null) {
			$this->failed_login_limit = $config['failed_login_limit'];
			$this->lockout_duration   = $config['lockout_duration'];
			add_filter( 'authenticate', array( $this, 'check_attempted_login' ), 30, 3 );
			add_action( 'wp_login_failed', array( $this, 'login_failed' ), 10, 1 );
		}

		public function check_attempted_login( $user, $username, $password ) {
			if ( get_transient( $this->transient_name ) ) {
				$datas = get_transient( $this->transient_name );
				if ( $datas['tried'] >= $this->failed_login_limit ) {
					$until = get_option( '_transient_timeout_' . $this->transient_name );
					$time = $this->when( $until );
					//Display error message to the user when limit is reached
					return new WP_Error( 'too_many_tried', sprintf( esc_attr( 'ERROR：您已触发登陆安全保护，请在 %1$s 后再次尝试.' ) , $time ) );
				}
			}
			return $user;
		}

		public function login_failed( $username ) {
			if ( get_transient( $this->transient_name ) ) {
				$datas = get_transient( $this->transient_name );
				$datas['tried']++;
				if ( $datas['tried'] <= $this->failed_login_limit )
				    set_transient( $this->transient_name, $datas , $this->lockout_duration );
			} else {
				$datas = array('tried' => 1 );
				set_transient( $this->transient_name, $datas , $this->lockout_duration );
			}
		}

		private function when( $time ) {
			if ( ! $time ) return;
			$right_now = time();
			$diff = abs( $right_now - $time );
			$second = 1;
			$minute = $second * 60;
			$hour = $minute * 60;
			$day = $hour * 24;
			if ( $diff < $minute )
			    return floor( $diff / $second ) . ' ' . esc_attr( '秒' );
			if ( $diff < $minute * 2 )
			    return esc_attr( 'about 1 minute ago' );
			if ( $diff < $hour )
			    return floor( $diff / $minute ) . ' ' . esc_attr( '分钟' );
			if ( $diff < $hour * 2 )
			    return esc_attr( 'about 1 hour');
			return floor( $diff / $hour ) . ' ' . esc_attr( '小时' );
		}
	}
}
//Enable it:
$LLA_config = [
            'failed_login_limit' => gdk_option('gdk_failed_login_limit'),   // 登录失败的次数限制
			'lockout_duration'   => gdk_option('gdk_lockout_duration'),   // 暂停登陆时间
];
if(gdk_option('gdk_lock_login')) {
	new GDK_Limit_Login_Attempts($LLA_config);
}

//禁用登陆错误信息
function gdk_disable_login_errors( $error ) {
	global $errors;
	$err_codes = $errors->get_error_codes();
	if ( ! in_array( 'too_many_tried', $err_codes ) ) {
		// For security reason
		return esc_attr('Access Denied!');
	}
	return $error;
}
add_filter( 'login_errors', 'gdk_disable_login_errors' );

//网站维护代码
function gdk_maintenance_mode() {
	if (!current_user_can('edit_themes') || !is_user_logged_in()) {
		wp_die('网站维护中ing,   没事儿您就别来啦……', 'Maintenance - Could you please not disturb me ', array('response' => '503'));
	}
}
if(gdk_option('gdk_maintenance_mode')){
	add_action('get_header', 'gdk_maintenance_mode');
} 

//各种措施拦截垃圾评论
if(gdk_option('gdk_fuck_spam')) {
	//拦截无来路的评论
	function gdk_comment_check_referrer() {
		if ( ! isset( $_SERVER['HTTP_REFERER'] ) || $_SERVER['HTTP_REFERER'] == '' ) {
			wp_die( esc_attr( 'Please enable referrers in your browser!' ) );
		}
	}
	add_action('check_comment_flood', 'gdk_comment_check_referrer');
	//拦截超长链接垃圾评论
	function gdk_url_spamcheck($approved, $commentdata) {
		return (strlen($commentdata['comment_author_url']) > 50) ? 'spam' : $approved;
	}
	add_filter('pre_comment_approved', 'gdk_url_spamcheck', 99, 2);
	function gdk_comment_lang($commentdata) {
		if (is_user_logged_in()) return $commentdata;
		$pattern = '/[一-龥]/u';
		// 禁止全英文评论
		if (!preg_match($pattern, $commentdata['comment_content'])) {
			gdk_die("您的评论中必须包含汉字!");
		}
		$pattern = '/[あ-んア-ン]/u';
		// 禁止日文评论
		if (preg_match($pattern, $commentdata['comment_content'])) {
			gdk_die("评论禁止包含日文!");
		}
		//屏蔽评论里面黑名单内容
		if (wp_blacklist_check($commentdata['comment_author'], $commentdata['comment_author_email'], $commentdata['comment_author_url'], $commentdata['comment_content'], $commentdata['comment_author_IP'], $commentdata['comment_agent'])) {
			gdk_die('不好意思，您的评论违反本站评论规则');
		}
		return $commentdata;
	}
	add_filter('preprocess_comment', 'gdk_comment_lang');
}

//隐藏用户名字
if( gdk_option('gdk_hide_user_name') ){
	function gdk_text_encrypt($string, $operation, $key = '') {
	    $string = $operation == 'D' ? str_replace( array('!','-','_'), array('=','+','/'), $string ) : $string;
	    $key = md5($key);
	    $key_length = strlen($key);
	    $string = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key) , 0, 8) . $string;
	    $string_length = strlen($string);
	    $rndkey = $box = array();
	    $result = '';
	    for ($i = 0; $i <= 255; $i++) {
	        $rndkey[$i] = ord($key[$i % $key_length]);
	        $box[$i] = $i;
	    }
	    for ($j = $i = 0; $i < 256; $i++) {
	        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
	        $tmp = $box[$i];
	        $box[$i] = $box[$j];
	        $box[$j] = $tmp;
	    }
	    for ($a = $j = $i = 0; $i < $string_length; $i++) {
	        $a = ($a + 1) % 256;
	        $j = ($j + $box[$a]) % 256;
	        $tmp = $box[$a];
	        $box[$a] = $box[$j];
	        $box[$j] = $tmp;
	        $result.= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	    }
	    if ($operation == 'D') {
	        if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key) , 0, 8)) {
	            return substr($result, 8);
	        } else {
	            return '';
	        }
	    } else {
	        return str_replace(array('=','+','/'), array('!','-','_'), base64_encode($result));
	    }
	}

	function gdk_custom_author_link_request( $query_vars ) {
	    if ( array_key_exists( 'author_name', $query_vars ) ) {
	        global $wpdb;
	        $author_id = gdk_text_encrypt( $query_vars['author_name'], 'D', AUTH_KEY );
	        if ( $author_id ) {
				$query_vars['author'] = $author_id;				
	            unset( $query_vars['author_name'] );    
	        }
	    }
	    return $query_vars;
	}
	add_filter( 'request', 'gdk_custom_author_link_request' );

	function gdk_custom_author_link( $link, $author_id) {
	    global $wp_rewrite;
	    $author_id = (int) $author_id;
	    $link = $wp_rewrite->get_author_permastruct();
	    if ( empty($link) ) {
	        $file = home_url( '/' );
	        $link = $file . '?author=' . gdk_text_encrypt($author_id, 'E',AUTH_KEY);
	    } else {
	        $link = str_replace('%author%', gdk_text_encrypt($author_id, 'E',AUTH_KEY), $link);
	        $link = home_url() . user_trailingslashit( $link );
	    }
	 
	    return $link;
	}
	add_filter( 'author_link', 'gdk_custom_author_link', 10, 2 );

	// wp-rest 可能暴露用户名
	function gdk_custom_rest_prepare_user( $response, $user, $request ){
		$response->data['slug'] = gdk_text_encrypt( $user->ID, 'E', AUTH_KEY );
		return $response;
	}
	add_filter( 'rest_prepare_user', 'gdk_custom_rest_prepare_user', 10, 3 );
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


//记录登陆失败发邮件
add_action( 'wp_authenticate', 'log_login', 10, 2 );
function log_login( $username, $password ) {

    if ( ! empty( $username ) && ! empty( $password ) ) {

        $check = wp_authenticate_username_password( NULL, $username, $password );
        if ( is_wp_error( $check ) ) {

            $ua = getBrowser();
            $agent = $ua['name'] . " " . $ua['version'];

            $referrer = ( isset( $_SERVER['HTTP_REFERER'] ) ) ? $_SERVER['HTTP_REFERER'] : $_SERVER['PHP_SELF'];
            if ( strstr( $referrer, 'wp-login' ) ) {
                $ref = 'wp-login.php';
            }

            if ( strstr( $referrer, 'wp-admin' ) ) {
                $ref = 'wp-admin/';
            }

            $contact_errors = false;
            // get the posted data
            $name = "WordPress " . get_bloginfo( 'name' );
            $email_address = get_bloginfo('admin_email' );

            // write the email content
            $header = "MIME-Version: 1.0\n";
            $header .= "Content-Type: text/html; charset=utf-8\n";
            $header .= "From: $name <$email_address>\n";

            $message = "Failed login attempt on <a href='" . get_site_url() . "/" . $ref . "' target='_blank'>" . $name . "</a><br>" . PHP_EOL;
            $message .= 'IP: <a href="http://whatismyipaddress.com/ip/' . gdk_get_ip() . '" target="_blank">' . gdk_get_ip() . "</a><br>" . PHP_EOL;
            $message .= 'WhoIs: <a href="https://who.is/whois-ip/ip-address/' . gdk_get_ip() . '" target="_blank">' . gdk_get_ip() . "</a><br>" . PHP_EOL;
            $message .= "Browser: " . $agent . "<br>" . PHP_EOL;
            $message .= "OS: " . $ua['platform'] . "<br>" . PHP_EOL;
            $message .= "Date: " . date('Y-m-d H:i:s') . "<br>" . PHP_EOL;
            $message .= "Referrer: " . $referrer . "<br>" . PHP_EOL;
            $message .= "User Agent: " . $ua['userAgent'] . "<br>" . PHP_EOL;
            $message .= "Username: " . $username . "<br>" . PHP_EOL;
            $message .= "Password: " . $password . "<br>" . PHP_EOL;

            $subject = "Failed login attempt - " . $name;
            $subject = "=?utf-8?B?" . base64_encode($subject) . "?=";
            $to = $email_address;
            if ( ! empty( $to ) ) {
                // send the email using wp_mail()
                if ( ! wp_mail( $to, $subject, $message, $header ) ) {
                    $contact_errors = true;
                }
            }

        }
    }
}
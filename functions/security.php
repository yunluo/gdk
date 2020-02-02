<?php

//define('DISALLOW_FILE_MODS',true);


if(gdk_option('gdk_block_requst'))      add_action( 'wp', 'gdk_prevent_requst' );//阻止乱七八糟的请求
if(gdk_option('gdk_maintenance_mode'))  add_action('get_header', 'gdk_maintenance_mode');//维护模式


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


//登陆错误锁定
if ( ! class_exists( 'GDK_Limit_Login_Attempts' ) ) {
	class GDK_Limit_Login_Attempts {
		private $failed_login_limit;
		//登录失败的次数限制
		private $lockout_duration;
		//暂停登陆时间
		var $transient_name     = 'attempted_login';
		//Transient used
		public function __construct($config = null) {
			$this->failed_login_limit = $config['failed_login_limit'];
			$this->lockout_duration   = $config['lockout_duration'];
			add_filter( 'authenticate', array( $this, 'check_attempted_login' ), 30, 3 );
			add_action( 'wp_login_failed', array( $this, 'login_failed' ), 10, 1 );
		}
		/**
                * Lock login attempts of failed login limit is reached
                */
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
		/**
                * Add transient
                */
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
		/**
                * Return difference between 2 given dates
                * @param  int      $time   Date as Unix timestamp
                * @return string           Return string
                */
		private function when( $time ) {
			if ( ! $time )
			                                   return;
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
$config = [
            'failed_login_limit' => gdk_option('gdk_failed_login_limit'),   // 登录失败的次数限制
'lockout_duration'   => gdk_option('gdk_lockout_duration'),   // 暂停登陆时间
];
if(gdk_option('gdk_lock_login')) {
	new GDK_Limit_Login_Attempts($config);
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





//拦截无来路的评论
add_action('check_comment_flood', 'gdk_comment_check_referrer');
    function gdk_comment_check_referrer() {
        if ( ! isset( $_SERVER['HTTP_REFERER'] ) || $_SERVER['HTTP_REFERER'] == '' ) {
            wp_die( esc_attr( 'Please enable referrers in your browser!' ) );
        }
    }


//拦截垃圾评论
function gdk_url_spamcheck($approved, $commentdata) {
	return (strlen($commentdata['comment_author_url']) > 50) ? 'spam' : $approved;
}
add_filter('pre_comment_approved', 'gdk_url_spamcheck', 99, 2);

function gdk_comment_lang($incoming_comment) {
	$pattern = '/[一-龥]/u';
	// 禁止全英文评论
	if (!preg_match($pattern, $incoming_comment['comment_content'])) {
		gdk_die("您的评论中必须包含汉字!");
	}
	$pattern = '/[あ-んア-ン]/u';
	// 禁止日文评论
	if (preg_match($pattern, $incoming_comment['comment_content'])) {
		gdk_die("评论禁止包含日文!");
	}
	return($incoming_comment);
}
add_filter('preprocess_comment', 'gdk_comment_lang');


//禁止使用admin登录
add_filter( 'wp_authenticate',  function ($user){
    if($user == 'admin') wp_die('Access Denied!');
});
add_filter('sanitize_user', function ($username, $raw_username, $strict){
    if($raw_username == 'admin' || $username == 'admin'){
        wp_die('Access Denied!');
    }
    return $username;
}, 10, 3);


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
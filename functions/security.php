<?php

define('DISALLOW_FILE_MODS',true);

//阻止乱七八糟的请求
add_action( 'wp', 'gdk_prevent_script_injection' );
    function gdk_prevent_script_injection() {
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


    if ( ! class_exists( 'Gdk_Limit_Login_Attempts' ) ) {
        class Gdk_Limit_Login_Attempts {
            var $failed_login_limit = 3;
            //登录失败的次数限制
            var $lockout_duration   = 60;
            //暂停登陆时间
            var $transient_name     = 'attempted_login';
            //Transient used
            public function __construct() {
                $failed_login_limit = gdk_get_option('gdk_failed_login_limit');
                $lockout_duration   = gdk_get_option('gdk_lockout_duration');
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
                        return new WP_Error( 'too_many_tried', sprintf( esc_attr( 'ERROR: 您已达到登陆错误次数，请在 %1$s 后再次尝试登陆.' ) , $time ) );
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
                    $datas = array(
                                            'tried'     => 1
                                        );
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
                                    return floor( $diff / $minute ) . ' ' . esc_attr( '分' );
                if ( $diff < $hour * 2 )
                                    return esc_attr( 'about 1 hour');
                return floor( $diff / $hour ) . ' ' . esc_attr( '小时' );
            }
        }
    }
    //Enable it:
    new Gdk_Limit_Login_Attempts();



//禁用登陆错误信息
function gdk_disable_login_errors( $error ) {
	global $errors;
	$err_codes = $errors->get_error_codes();
	if ( ! in_array( 'too_many_tried', $err_codes ) ) {
		// For security reason
		return esc_attr('Access denied!');
	}
	return $error;
}
add_filter( 'login_errors', 'gdk_disable_login_errors' );


//禁用找回密码
class Gdk_Password_Reset_Removed
{
	function __construct() {
		add_filter( 'show_password_fields', array( $this, 'disable' ) );
		add_filter( 'allow_password_reset', array( $this, 'disable' ) );
		add_filter( 'gettext',              array( $this, 'remove' ) );
	}

	function disable() {
		if ( is_admin() ) {
			$user_id = get_current_user_id();
			$user = get_user_by('id' , $user_id );
			if ( !empty( $user->roles ) && is_array( $user->roles ) && $user->roles[0] == 'administrator' )
			return true;
		}
		return false;
	}

	function remove( $translated_text, $untranslated_text ) {
        if ( is_login() && in_string($untranslated_text,'Lost your password?')  ){
        return str_replace('Lost your password?','',$untranslated_text);
    }else{
        return $translated_text;
    }
    }


}

new Gdk_Password_Reset_Removed();

//拦截无来路的评论
add_action('check_comment_flood', 'gdk_comment_check_referrer');
    function gdk_comment_check_referrer() {
        if ( ! isset( $_SERVER['HTTP_REFERER'] ) || $_SERVER['HTTP_REFERER'] == '' ) {
            wp_die( esc_attr( 'Please enable referrers in your browser!' ) );
        }
    }

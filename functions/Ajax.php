<?php

/*
*Ajax操作文件
*/

function gdk_test_email() {
    $is_error = !wp_mail('donotreply@mywpku.com', '测试发信', 'WP 积木测试发信');
    if ($is_error) {
        exit('0');
    }else{
        exit('1');
    }
}
add_action('wp_ajax_nopriv_gdk_test_email', 'gdk_test_email');
add_action('wp_ajax_gdk_test_email', 'gdk_test_email');


//粘贴上传图片
function gdk_pasteup_imag() {
    if( !isset( $_POST['pui_nonce'] ) || !wp_verify_nonce($_POST['pui_nonce'], 'pui-nonce') ) exit('Permissions check failed');
	if($_FILES) {
		global $post;
		$post_ID = $post->ID;
		$wp_upload_dir = wp_upload_dir();
		$file = $_FILES["imageFile"];
		$result=array ("success"=>false,"message"=>"Null");
		if (in_array($file["type"],array ("image/gif","image/jpeg","image/pjpeg","image/png"))) {
			if ($file["error"]>0) {
				$result['message']="error";
			} else {
				$file_name = md5_file($file["tmp_name"]).str_replace("image/",".",$file["type"]);//img name
				$file_url = $wp_upload_dir['url']."/".$file_name;
				$file_path = $wp_upload_dir['path']."/".$file_name;
				if (!file_exists($file_path)) {
					move_uploaded_file($file["tmp_name"],$file_path);
					$attachment = [
					                'guid'           => $wp_upload_dir['url'] . '/' . basename( $file_path ),
					                'post_mime_type' => $file['type'],
					                'post_title'     => $file_name,
					                'post_content'   => '',
					                'post_status'    => 'inherit'
                    			];
					$attach_id = wp_insert_attachment( $attachment, $file_name, $post_ID);
					//这是wp内置的上传附件的函数
					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
					wp_update_attachment_metadata( $attach_id, $attach_data );
				}
				$result['success']= true;
				$result['message']= $file_url;
			}
		} else {
			$result['message']="Invalid file";
		}
		echo(json_encode($result));
		exit();
	}
}
add_action('wp_ajax_nopriv_gdk_pasteup_imag', 'gdk_pasteup_imag');
add_action('wp_ajax_gdk_pasteup_imag', 'gdk_pasteup_imag');


function gdk_pass_view() {
	if( !isset( $_POST['pass_nonce'] ) || !wp_verify_nonce($_POST['pass_nonce'], 'pass_nonce') ) return;
	$action = $_POST['action'];
	$post_id = $_POST['id'];
	$pass = $_POST['pass'];
	if(!isset( $action )  ||  !isset( $post_id )  ||  !isset( $pass )   ) return;
	if($pass == '2233') {
	$pass_content = get_post_meta($post_id, '_pass_content')[0];
	exit($pass_content);
	}else{
		exit('0');
	}
}
add_action('wp_ajax_nopriv_gdk_pass_view', 'gdk_pass_view');
add_action('wp_ajax_gdk_pass_view', 'gdk_pass_view');

//weauth自动登录
function bind_email_check(){
    $mail = isset($_POST['email']) ? $_POST['email'] : false;
    if($mail && $_POST['action'] == 'bind_email_check'){
        $user_id = email_exists( $email );
        if ($user_id) {
            exit(1);
        }
    }
}
add_action( 'wp_ajax_bind_email_check', 'bind_email_check' );
add_action( 'wp_ajax_nopriv_bind_email_check', 'bind_email_check' );

//weauth自动登录
function weauth_oauth_login(){
    $key = isset($_POST['spam']) ? $_POST['spam'] : false;
    $mail = isset($_POST['email']) ? $_POST['email'] : false;
    if($key && $_POST['action'] == 'weauth_oauth_login'){
        $user_id = get_transient($key.'ok');
        if ($user_id != 0) {
            wp_set_auth_cookie($user_id,true);
            if($mail && !empty($mail) && is_email($mail)){
                wp_update_user( array( 'ID' => $user_id, 'user_email' => $mail ) );
            }
            exit(wp_unique_id());
        }
    }
}
add_action( 'wp_ajax_weauth_oauth_login', 'weauth_oauth_login' );
add_action( 'wp_ajax_nopriv_weauth_oauth_login', 'weauth_oauth_login' );

//付费可见
function pay_buy(){
    if (isset($_POST['point']) && isset($_POST['userid']) &&isset($_POST['id']) && $_POST['action'] == 'pay_buy') {
            Points::set_points( -$_POST['point'],
                    $_POST['userid'],
                    array(
                        'description' => $_POST['id'],
                        'status' => get_option( 'points-points_status', 'accepted' )
                    )
            );//扣除金币
			$pay_content = get_post_meta($_POST['id'], 'pay_content', true);
            exit($pay_content);
    }
}
add_action( 'wp_ajax_pay_buy', 'pay_buy' );
add_action( 'wp_ajax_nopriv_pay_buy', 'pay_buy' );

/*免登陆购买开始*/

//获取加密内容
function getcontent() {
	$id = $_POST["id"];
	$action = $_POST["action"];
	if ( isset($id) && $_POST['action'] == 'getcontent') {
		$pay_content = get_post_meta($id, 'gdk_pay_content', true);
		exit($pay_content);
	}
}
add_action( 'wp_ajax_getcontent', 'getcontent' );
add_action( 'wp_ajax_nopriv_getcontent', 'getcontent' );

///提取码检测
function check_code() {
	$id = $_POST['id'];
	$code = $_POST['code'];
	if (isset($code) && isset($id) && $_POST['action'] == 'check_code') {
		$pay_log = get_post_meta($id, 'pay_log', true);
		//购买记录数据
		$pay_arr = explode(",", $pay_log);
		if(in_array($code,$pay_arr)) {
			exit('1');
		} else {
			exit('0');
		}
	}
}
add_action( 'wp_ajax_check_code', 'check_code' );
add_action( 'wp_ajax_nopriv_check_code', 'check_code' );

//在线充值
function payjs_view(){
        $id = $_POST['id'];
        $money = $_POST['money'];
        $way = $_POST['way'];
    if (isset($id) && isset($money) && isset($way) && $_POST['action'] == 'payjs_view') {
    $config = [
        'mchid' => gdk_option('gdk_payjs_id'),   // 配置商户号
        'key'   => gdk_option('gdk_payjs_key'),   // 配置通信密钥
    ];
    // 初始化
    $payjs = new Payjs($config);
    $data = [
        'body' => '在线付费查看',   // 订单标题
        'attach' => 'P'.$id,
        'out_trade_no' => gdk_order_id(),       // 订单号
        'total_fee' => intval($money)*100,             // 金额,单位:分
        'notify_url' => GDK_BASE_URL.'/public/push.php',
        'hide' => '1'
    ];

    if($way == 1) $data['type'] = 'alipay';
    $result_money = intval($money);
    $result_trade_no = $data['out_trade_no'];
    if(gdk_is_mobile()){
        $rst = $payjs->cashier($data);//手机使用
        $result_img = $rst;
    }else{
        $rst = $payjs->native($data);//电脑使用
        $result_img = $rst['code_url'];
    }
    $result = $result_money.'|'. $result_img.'|'. $result_trade_no;
    }
    exit($result);
}
add_action( 'wp_ajax_payjs_view', 'payjs_view' );
add_action( 'wp_ajax_nopriv_payjs_view', 'payjs_view' );

function checkpayjs(){
        $id = $_POST['id'];
        $orderid = $_POST['orderid'];
        if (isset($id) && isset($orderid) && $_POST['action'] == 'checkpayjs') {
            $sid = get_transient('P'.$id);
            if(strpos($sid,'E20') !== false && $orderid == $sid){
                exit('1');//OK
            }else{
                exit('0');//no
            }

        }
}
add_action( 'wp_ajax_checkpayjs', 'checkpayjs' );
add_action( 'wp_ajax_nopriv_checkpayjs', 'checkpayjs' );


function addcode(){
        $id = $_POST['id'];
        $code = $_POST['code'];
        if (isset($id) && isset($code) && $_POST['action'] == 'addcode') {
            $pay_log = get_post_meta($id, 'pay_log', true);//购买记录数据
            if(empty($pay_log)){
                add_post_meta($id, 'pay_log', $code, true);
            }else{
                update_post_meta($id, 'pay_log', $pay_log.','.$code);
            }
            $pay_log = get_post_meta($id, 'pay_log', true);//购买记录数据
            $pay_arr = explode(",", $pay_log);
                if(in_array($code,$pay_arr)){
                    exit('1');//OK
                }else{
                    exit('0');//NO
                }
        }
}
add_action( 'wp_ajax_addcode', 'addcode' );
add_action( 'wp_ajax_nopriv_addcode', 'addcode' );

/*免登陆购买结束*/

//在线充值
function pay_chongzhi() {
	if (isset($_POST['jine']) && $_POST['action'] == 'pay_chongzhi') {
		$config = [
		        'mchid' => gdk_option('gdk_payjs_id'),   // 配置商户号
				'key'   => gdk_option('gdk_payjs_secret'),   // 配置通信密钥
		];
		// 初始化
		$payjs = new Payjs($config);
		$data = [
		'body' => '积分充值',   // 订单标题
		'attach' => get_current_user_id(),   // 订单备注
		'out_trade_no' => gdk_order_id(),       // 订单号
		'total_fee' => intval($_POST['jine'])*100,             // 金额,单位:分
		'notify_url' => GDK_BASE_URL.'/public/push.php',
		'hide' => '1'
		];
		$result_money = intval($_POST['jine']);
		$result_trade_no = $data['out_trade_no'];
		if( gdk_option('gdk_payjs_alipay') && $_POST['way'] =='alipay' ) {
			$data['type'] = 'alipay';
			$result_way = '支付宝';
		} else {
			$result_way = '微信';
		}
		if(gdk_is_mobile()) {
			$rst = $payjs->cashier($data);//手机使用
			$result_img = $rst;
		} else {
			$rst = $payjs->native($data);//电脑使用
			$result_img = $rst['code_url'];
		}
		$result = $result_money.'|'.$result_way.'|'. $result_img.'|'. $result_trade_no;
		exit($result);
	}
}
add_action( 'wp_ajax_pay_chongzhi', 'pay_chongzhi' );
add_action( 'wp_ajax_nopriv_pay_chongzhi', 'pay_chongzhi' );

//检查付款情况
function payrest(){
    if (isset($_POST['check_trade_no']) && $_POST['action'] == 'payrest') {
        if (gdk_check($_POST['check_trade_no'])) {
            exit('1');
        } else {
            exit('0');
        }
    }
}
add_action( 'wp_ajax_payrest', 'payrest' );
add_action( 'wp_ajax_nopriv_payrest', 'payrest' );

//ajax生成登录二维码
function weauth_qr_gen(){
    if (isset($_POST['wastart']) && $_POST['action'] == 'weauth_qr_gen') {
        if (!empty($_POST['wastart'])) {
            $rest = implode("|", get_weauth_qr());
            exit($rest);
        }
    }
}
add_action( 'wp_ajax_weauth_qr_gen', 'weauth_qr_gen' );
add_action( 'wp_ajax_nopriv_weauth_qr_gen', 'weauth_qr_gen' );

//检查登录状况
function weauth_check(){
    if (isset($_POST['sk']) && $_POST['action'] == 'weauth_check') {
        $rest = substr($_POST['sk'],-16);//key
        $weauth_cache = get_transient($rest.'ok');
        if (!empty($weauth_cache)) {
            exit($rest);//key
        }
    }
}
add_action( 'wp_ajax_weauth_check', 'weauth_check' );
add_action( 'wp_ajax_nopriv_weauth_check', 'weauth_check' );
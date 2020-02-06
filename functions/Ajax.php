<?php

/*
*Ajax操作文件
*/

/**
 * 200 ok
 * 400 fail
 */

//后台邮箱检测
function gdk_test_email() {
    $is_error = !wp_mail('donotreply@mywpku.com', '测试发信', 'WP 积木测试发信');
    if ($is_error) {
        exit('500');
    }else{
        exit('200');
    }
}
add_action('wp_ajax_nopriv_gdk_test_email', 'gdk_test_email');
add_action('wp_ajax_gdk_test_email', 'gdk_test_email');


//粘贴上传图片
function gdk_pasteup_imag() {
    if( !isset( $_POST['pui_nonce'] ) || !wp_verify_nonce($_POST['pui_nonce'], 'pui-nonce') ) exit('400');
	if($_FILES) {
		global $post;
		$post_ID = $post->ID;
		$wp_upload_dir = wp_upload_dir();
		$file = $_FILES['imageFile'];
		$result=array ('success'=>false,'message'=>'Null');
		if (in_array($file['type'],array ('image/gif','image/jpeg','image/pjpeg','image/png'))) {
			if ($file['error']>0) {
				$result['message']='error';
			} else {
				$file_name = md5_file($file['tmp_name']).str_replace('image/','.',$file['type']);//img name
				$file_url = $wp_upload_dir['url'].'/'.$file_name;
				$file_path = $wp_upload_dir['path'].'/'.$file_name;
				if (!file_exists($file_path)) {
					move_uploaded_file($file['tmp_name'],$file_path);
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
				$result['success'] = true;
				$result['message'] = $file_url;
			}
		} else {
			$result['message'] = '400';
		}
		echo(json_encode($result));
		exit();
	}
}
add_action('wp_ajax_nopriv_gdk_pasteup_imag', 'gdk_pasteup_imag');
add_action('wp_ajax_gdk_pasteup_imag', 'gdk_pasteup_imag');

//密码可见
function gdk_pass_view() {
	if( !isset( $_POST['pass_nonce'] ) || !wp_verify_nonce($_POST['pass_nonce'], 'pass_nonce') ) exit('400');
	$action = $_POST['action'];
	$post_id = $_POST['id'];
	$pass = $_POST['pass'];
	if(!isset( $action )  ||  !isset( $post_id )  ||  !isset( $pass )   ) exit('400');
	if($pass == '2233') {
	$pass_content = get_post_meta($post_id, '_pass_content')[0];
	exit($pass_content);
	}else{
		exit('400');
	}
}
add_action('wp_ajax_nopriv_gdk_pass_view', 'gdk_pass_view');
add_action('wp_ajax_gdk_pass_view', 'gdk_pass_view');

//密码可见end



//在线积分充值
function pay_points() {
    if( !isset( $_POST['action'] ) || $_POST['action'] !== 'pay_points' ) exit('400');
    if (!isset($_POST['money']) || !isset($_POST['way'])) exit('400');//无脑输出400错误
	if (isset($_POST['id'])) {
        payjs_action('积分充值',$_POST['id']);
	}
}
add_action( 'wp_ajax_pay_points', 'pay_points' );
add_action( 'wp_ajax_nopriv_pay_points', 'pay_points' );

//检查积分充值
function check_pay_points(){
    if( !isset( $_POST['check_pay_points'] ) || !wp_verify_nonce($_POST['check_pay_points'], 'check_pay_points') ) exit('400');
    if (!isset($_POST['id']) || !isset($_POST['orderid'])) exit('400');//无脑输出400错误
    if ( $_POST['action'] == 'check_pay_points') {
        if(gdk_check( $_POST['orderid'] , $_POST['id'])){
            exit('200');
        }else{
            exit('400');
        }
    }
}
add_action( 'wp_ajax_check_pay_points', 'check_pay_points' );
add_action( 'wp_ajax_nopriv_check_pay_points', 'check_pay_points' );

//积分充值end

//游客付费可见
function pay_view() {
    if( !isset( $_POST['action'] ) || $_POST['action'] !== 'pay_view' ) exit('400');
    if (!isset($_POST['money']) || !isset($_POST['way'])) exit('400');//无脑输出400错误
	if (isset($_POST['id'])) {
        payjs_action('在线付费查看',$_POST['id']);//标题,文章id
	}
}
add_action( 'wp_ajax_pay_view', 'pay_view' );
add_action( 'wp_ajax_nopriv_pay_view', 'pay_view' );


//检查付费可见订单
function check_pay_view() {
	if( !isset( $_POST['check_pay_view'] ) || !wp_verify_nonce($_POST['check_pay_view'], 'check_pay_view') ) exit('400');
	if (!isset($_POST['id']) || !isset($_POST['orderid'])) exit('400');
	//无脑输出400错误
	if ( $_POST['action'] == 'check_pay_view') {
		$sid = get_transient('PP'.$_POST['id']);
		if(in_string($sid,'E20') && $orderid == $sid) {
			exit('200');//OK
		} else {
			exit('400');//no
		}
	}
}
add_action( 'wp_ajax_check_pay_view', 'check_pay_view' );
add_action( 'wp_ajax_nopriv_check_pay_view', 'check_pay_view' );
/**END */

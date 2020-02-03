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
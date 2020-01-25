<?php
$nc_option = get_option('nc_option');

if (!function_exists('nc_mail_smtp')):
    add_action('phpmailer_init', 'nc_mail_smtp');
    function nc_mail_smtp( $phpmailer ) {
        $nc_option = get_option('nc_option');
        $smtp = $nc_option['smtp'];
        $phpmailer->FromName = $smtp['smtp_fromname']; 
        $phpmailer->Host = $smtp['smtp_host'];
        $phpmailer->Port = $smtp['smtp_port']; 
        $phpmailer->Username = $smtp['smtp_username']; 
        $phpmailer->Password = $smtp['smtp_password']; 
        $phpmailer->From = $smtp['smtp_display_mail']; 
        $phpmailer->SMTPAuth = true; 
        $phpmailer->SMTPSecure = $smtp['smtp_secure'] == 'none' ? '' : $smtp['smtp_secure'];
        $phpmailer->IsSMTP();
    }
endif;

if (!function_exists('nc_test_email')):
	add_action('wp_ajax_nopriv_nc_test_email', 'nc_test_email');
	add_action('wp_ajax_nc_test_email', 'nc_test_email');
	function nc_test_email() {
        $is_error = !wp_mail('donotreply@mywpku.com', '测试发信', 'WP 积木测试发信');
        
		if ($is_error) {
			echo json_encode(array(
                's' => 400,
                'm' => '发信失败，请检查配置是否正确'
			));
			die();
		}
		echo json_encode(array(
			's' => 200,
			'm' => '测试成功'
		));
		die();
	}
endif;
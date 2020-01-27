<?php

/*
*Ajax操作
*/



function nc_test_email() {
    $is_error = !wp_mail('donotreply@mywpku.com', '测试发信', 'WP 积木测试发信');
    if ($is_error) {
        exit('0');
    }else{
        exit('1');
    }
}
add_action('wp_ajax_nopriv_nc_test_email', 'nc_test_email');
add_action('wp_ajax_nc_test_email', 'nc_test_email');
<?php
if( !is_user_logged_in() && is_admin() && !( defined('DOING_AJAX') && DOING_AJAX ) ){

    if( isset( $_GET[$GLOBALS['nice-security-access-path']] ) ){
        add_filter('site_url', 'nice_security_access_path', 10, 1);
        add_filter('network_site_url', 'nice_security_access_path', 10, 1 );
    }else{
        status_header(404);
        header('HTTP/1.0 404 Not Found');
        exit;
    }

}

if( !is_user_logged_in() &&  $GLOBALS['pagenow'] === 'wp-login.php' ){

    if( isset( $_GET[$GLOBALS['nice-security-access-path']] ) ){
        add_filter('site_url', 'nice_security_access_path', 10, 1);
        add_filter( 'network_site_url', 'nice_security_access_path', 10, 1 );
    }else{
        status_header(404);
        header('HTTP/1.0 404 Not Found');
        exit;
    }

    

}
add_filter( 'wp_redirect', 'nice_security_access_path', 10, 1 );

function nice_security_access_path( $url ){

    if( strpos( $url, 'wp-login.php' ) !== false  ){

        return add_query_arg( $GLOBALS['nice-security-access-path'], '', $url );

    }

    return $url;
   

}
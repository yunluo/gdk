<?php

if( is_admin() && is_user_logged_in() && !empty( $GLOBALS['wp-admin-access-group'] ) && !(defined('DOING_AJAX') && DOING_AJAX) ){

    $user = wp_get_current_user();
    $user_roles = $user->roles;
    
    $GLOBALS['wp-admin-access-group'][] = 'administrator';

    if( empty( array_intersect( $GLOBALS['wp-admin-access-group'], $user_roles  ) ) ){
        wp_die('无权访问');
    }

}

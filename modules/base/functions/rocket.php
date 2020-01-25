<?php
$nc_option = get_option('nc_option');
$general_options = $nc_option['general'];

if (!function_exists('nc_admin_logo')):
    function nc_admin_logo() {
        $logo = get_field('admin_login_logo', 'options'); // trick
        if( isset( $logo ) && !empty( $logo ) ){
            echo sprintf('
                <style type="text/css">
                #login h1 a {
                    width: auto;
                    background-image: none,url(%s);
                    background-size: auto;
                }
                </style>
            ', $logo);
        }
    }
    add_action('login_head', 'nc_admin_logo');
endif;












//禁用 Auto OEmbed
// if ($general_options['disable_auto_embeds']) {
//     remove_filter('the_content', array($GLOBALS['wp_embed'], 'autoembed'), 8);
// }

// if ($general_options['disable_post_embed']) {
//     add_filter('embed_oembed_discover', '__return_false');

//     remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
//     remove_filter('oembed_response_data', 'get_oembed_response_data_rich', 10, 4);

//     remove_action('wp_head', 'wp_oembed_add_discovery_links');
//     remove_action('wp_head', 'wp_oembed_add_host_js');


//     if (!function_exists('nc_disable_post_embed_tiny_mce_plugin')):
//         add_filter('tiny_mce_plugins', 'nc_disable_post_embed_tiny_mce_plugin');
//         function nc_disable_post_embed_tiny_mce_plugin($plugins)
//         {
//             return array_diff($plugins, array('wpembed'));
//         }
//     endif;

//     if (!function_exists('nc_disable_post_embed_query_var')):
//         add_filter('query_vars', 'nc_disable_post_embed_query_var');
//         function nc_disable_post_embed_query_var($public_query_vars)
//         {
//             return array_diff($public_query_vars, array('embed'));
//         }
//     endif;
// }

if ($general_options['gravatar_speedup']) {
    if (!function_exists('nc_v2ex_get_avatar')):
        function nc_v2ex_get_avatar( $avatar ) {
            $avatar = preg_replace("/http[s]{0,1}:\/\/(secure|www|\d).gravatar.com\/avatar\//","//gravatar.loli.net/avatar/",$avatar);

            return $avatar;
        }
        add_filter('get_avatar', 'nc_v2ex_get_avatar');
    endif;
}




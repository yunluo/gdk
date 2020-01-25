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

//移除 WP_Head 无关紧要的代码
if ($general_options['remove_wp_head_useless']) {
	remove_action('wp_head', 'wp_generator'); //删除 head 中的 WP 版本号
	foreach (array('rss2_head', 'commentsrss2_head', 'rss_head', 'rdf_header', 'atom_head', 'comments_atom_head', 'opml_head', 'app_head') as $action) {
	    remove_action($action, 'the_generator');
	}

	remove_action('wp_head', 'rsd_link'); //删除 head 中的 RSD LINK
	remove_action('wp_head', 'wlwmanifest_link'); //删除 head 中的 Windows Live Writer 的适配器？

	remove_action('wp_head', 'feed_links_extra', 3); //删除 head 中的 Feed 相关的link
	//remove_action( 'wp_head', 'feed_links', 2 );

	remove_action('wp_head', 'index_rel_link'); //删除 head 中首页，上级，开始，相连的日志链接
	remove_action('wp_head', 'parent_post_rel_link', 10);
	remove_action('wp_head', 'start_post_rel_link', 10);
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);

	remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0); //删除 head 中的 shortlink

	remove_action('wp_head', 'rest_output_link_wp_head', 10); // 删除头部输出 WP RSET API 地址

	remove_action('template_redirect', 'wp_shortlink_header', 11); //禁止短链接 Header 标签。
	remove_action('template_redirect', 'rest_output_link_header', 11); // 禁止输出 Header Link 标签。
}

//禁用日志修订功能
if ($general_options['post_revision']) {
    if (!function_exists('nc_disable_post_revisions')):
        function nc_disable_post_revisions() {
            foreach ( get_post_types() as $post_type ) {
                remove_post_type_support( $post_type, 'revisions' );
            }
        }
        add_action( 'init', 'nc_disable_post_revisions', 999 );
    endif;
}

if (isset($general_options['core_update']) && $general_options['core_update']) {
    add_filter('site_transient_update_core', 'nc_core_update_cdn');
    function nc_core_update_cdn($value) {
        foreach ($value->updates as &$update) {
            $update->download = str_replace('downloads.wordpress.org', 'downloads.wordpress.org.nicetheme.xyz', $update->download);
            $update->packages->full	= str_replace('downloads.wordpress.org', 'downloads.wordpress.org.nicetheme.xyz', $update->packages->full);
        }

        return $value;
    }
}

if ($general_options['disable_trackbacks']) {
    //彻底关闭 pingback
    if (!function_exists('nc_xmlrpc_methods')):
        add_filter('xmlrpc_methods', 'nc_xmlrpc_methods');
        function nc_xmlrpc_methods($methods)
        {
            $methods['pingback.ping']                    = '__return_false';
            $methods['pingback.extensions.getPingbacks'] = '__return_false';

            return $methods;
        }
    endif;
    //禁用 pingbacks, enclosures, trackbacks
    remove_action('do_pings', 'do_all_pings', 10);

    //去掉 _encloseme 和 do_ping 操作。
    remove_action('publish_post', '_publish_post_hook', 5);
}


//禁用 XML-RPC 接口
if ($general_options['disable_xmlrpc']) {
    add_filter('xmlrpc_enabled', '__return_false');
    remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
}

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

if ($general_options['disable_emoji']) {
    if (!function_exists('nc_disable_emojis')):
        function nc_disable_emojis() {
            remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
            remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
            remove_action( 'wp_print_styles', 'print_emoji_styles' );
            remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
            remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
            remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
            remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
            add_filter( 'tiny_mce_plugins', 'nc_disable_emojis_tinymce' );
        }
        add_action( 'init', 'nc_disable_emojis' );
    endif;
    if (!function_exists('nc_disable_emojis_tinymce')):
        function nc_disable_emojis_tinymce( $plugins ) {
            if ( is_array( $plugins ) ) return array_diff( $plugins, array( 'wpemoji' ) );
            return array();
        }
    endif;
}

if ($general_options['emoji_cdn']) {
    if (!function_exists('nc_change_wp_emoji_baseurl')):
        function nc_change_wp_emoji_baseurl() {
            return set_url_scheme('//twemoji.maxcdn.com/2/72x72/');
        }
        add_action( 'emoji_url', 'nc_change_wp_emoji_baseurl' );
    endif;
    if (!function_exists('nc_change_wp_emoji_baseurl')):
        function nc_change_wp_emoji_svgurl($url) {
            return set_url_scheme('//twemoji.maxcdn.com/svg/');
        }
        add_filter('emoji_svg_url', 'nc_change_wp_emoji_svgurl');
    endif;
}

if ($general_options['disable_wp_widgets']) {
    if (!function_exists('nc_unregister_rss_widget')):
        function nc_unregister_rss_widget(){
            unregister_widget('WP_Widget_Pages');
            unregister_widget('WP_Nav_Menu_Widget');
            unregister_widget('WP_Widget_Categories');
            unregister_widget('WP_Widget_Meta');
            unregister_widget('WP_Widget_RSS');
            unregister_widget('WP_Widget_Calendar');
            unregister_widget('WP_Widget_Links');
            unregister_widget('WP_Widget_Recent_Comments');
        }
        add_action('widgets_init', 'nc_unregister_rss_widget');
    endif;
}

if ($general_options['disable_core_update']) {
    add_filter('automatic_updater_disabled', '__return_true');	

    remove_action('init', 'wp_schedule_update_checks');	
    wp_clear_scheduled_hook('wp_version_check');
    wp_clear_scheduled_hook('wp_maybe_auto_update');

    remove_action( 'admin_init', '_maybe_update_core' );	
}

if ($general_options['disable_theme_update']) {
    wp_clear_scheduled_hook('wp_update_themes');
    remove_action( 'load-themes.php', 'wp_update_themes' );	
    remove_action( 'load-update.php', 'wp_update_themes' );
    remove_action( 'load-update-core.php', 'wp_update_themes' );
    remove_action( 'admin_init', '_maybe_update_themes' );
}

if ($general_options['disable_plugin_update']) {
    wp_clear_scheduled_hook('wp_update_plugins');
    remove_action( 'load-plugins.php', 'wp_update_plugins' );	
    remove_action( 'load-update.php', 'wp_update_plugins' );
    remove_action( 'load-update-core.php', 'wp_update_plugins' );
    remove_action( 'admin_init', '_maybe_update_plugins' );
}
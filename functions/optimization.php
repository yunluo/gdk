<?php



//禁用新版编辑器
if(gdk_option('gdk_diasble_gutenberg')){
add_filter('use_block_editor_for_post', '__return_false');
remove_action( 'wp_enqueue_scripts', 'wp_common_block_scripts_and_styles' );
}

// 友情链接扩展
add_filter('pre_option_link_manager_enabled', '__return_true');

//移除 WP_Head 无关紧要的代码
if(gdk_option('gdk_diasble_head_useless')){
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
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
}


/**  开始关闭WordPress更新  **/
if (gdk_option('gdk_diasble_wp_update')) {
    add_filter('automatic_updater_disabled', '__return_true');	
    remove_action('init', 'wp_schedule_update_checks');	
    wp_clear_scheduled_hook('wp_version_check');
    wp_clear_scheduled_hook('wp_maybe_auto_update');
    remove_action( 'admin_init', '_maybe_update_core' );	
    //禁用主题更新
    wp_clear_scheduled_hook('wp_update_themes');
    remove_action( 'load-themes.php', 'wp_update_themes' );	
    remove_action( 'load-update.php', 'wp_update_themes' );
    remove_action( 'load-update-core.php', 'wp_update_themes' );
    remove_action( 'admin_init', '_maybe_update_themes' );
    //	禁用插件更新
    wp_clear_scheduled_hook('wp_update_plugins');
    remove_action( 'load-plugins.php', 'wp_update_plugins' );	
    remove_action( 'load-update.php', 'wp_update_plugins' );
    remove_action( 'load-update-core.php', 'wp_update_plugins' );
    remove_action( 'admin_init', '_maybe_update_plugins' );
}

//禁用自带p标签的
remove_filter( 'the_content', 'wpautop' );
add_filter( 'the_content', 'wpautop' , 12);

//禁用emoji功能
if (gdk_option('gdk_disable_emojis')) {
        function gdk_disable_emojis_link() {
            remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
            remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
            remove_action( 'wp_print_styles', 'print_emoji_styles' );
            remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
            remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
            remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
            remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
            add_filter( 'tiny_mce_plugins', 'gdk_disable_emojis_tinymce' );
        }
        add_action( 'init', 'gdk_disable_emojis_link' );

        function gdk_disable_emojis_tinymce( $plugins ) {
            if ( is_array( $plugins ) ) return array_diff( $plugins, array( 'wpemoji' ) );
            return array();
        }
}


//禁用 XML-RPC 接口
if (gdk_option('gdk_disable_xmlrpc')) {
	add_filter('xmlrpc_enabled', '__return_false');
	remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
}


//禁用日志修订功能
if (gdk_option('gdk_disable_revision')) {
	function gdk_disable_post_revisions() {
		foreach ( get_post_types() as $post_type ) {
			remove_post_type_support( $post_type, 'revisions' );
		}
	}
	add_action( 'init', 'gdk_disable_post_revisions', 999 );
}

//彻底关闭 pingback
if (gdk_option('gdk_disable_trackbacks')) {
	add_filter('xmlrpc_methods', 'gdk_xmlrpc_methods');
	function gdk_xmlrpc_methods($methods) {
		unset($methods['system.multicall']);
		$methods['pingback.ping']                    = '__return_false';
		$methods['pingback.extensions.getPingbacks'] = '__return_false';
		return $methods;
	}
	//禁用 pingbacks, enclosures, trackbacks
	remove_action('do_pings', 'do_all_pings', 10);
	//去掉 _encloseme 和 do_ping 操作。
	remove_action('publish_post', '_publish_post_hook', 5);
}

//国内更新word press加速
if (gdk_option('gdk_porxy_update') && !gdk_option('gdk_diasble_wp_update')) {
	add_filter('site_transient_update_core',function($value) {
		foreach($value->updates as &$update) {
			if($update->locale == 'zh_CN') {
				$update->download = 'http://cn.wp101.net/latest-zh_CN.zip';
				$update->packages->full = 'http://cn.wp101.net/latest-zh_CN.zip';
			}
		}
		return $value;
	}
	);
}

//页面伪静态
function gdk_page_permalink() {
	global $wp_rewrite;
	if (!strpos($wp_rewrite->get_page_permastruct(), '.html')) {
		$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.html';
	}
}
add_action('init', 'gdk_page_permalink', -1);


//中文文件重命名
function gdk_upload_rename($file) {
    $time = date("YmdHis");
    $file['name'] = $time . "" . mt_rand(1, 100) . "." . pathinfo($file['name'], PATHINFO_EXTENSION);
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'gdk_upload_rename');


// 搜索结果为1时候自动跳转到对应页面
if ( ! function_exists( 'gdk_redirect_single_search_result' ) ) {
function gdk_redirect_single_search_result() {
		if ( is_search() ) {
			global $wp_query;
			if ($wp_query->post_count == 1) {
				wp_redirect( get_permalink( $wp_query->posts['0']->ID ) );
				exit();
			}
		}
	}
}
add_action('template_redirect', 'gdk_redirect_single_search_result');


//搜索链接伪静态
if ( ! function_exists( 'gdk_redirect_search' ) ) {
	function gdk_redirect_search() {
		if ( is_search() && ! empty( $_GET['s'] ) ) {
			wp_redirect( home_url( "/search/" ) . urlencode( get_query_var( 's' ) ) );
			exit();
		}
	}
}
add_action('template_redirect', 'gdk_redirect_search' );


//小工具运行短代码
add_filter( 'widget_text', 'shortcode_unautop' );
add_filter( 'widget_text', 'do_shortcode' );





//替换后台默认的底部文字内容
function gdk_replace_footer_admin() {
	$result = apply_filters('gdk_filter_admin_footer_text', '由GDK插件提供底层支持');
	echo $result;
}
add_filter('admin_footer_text', 'gdk_replace_footer_admin');


//隐藏用户昵称
add_filter('redirect_canonical', 'security_stop_user_enumeration', 10, 2);
if ( ! function_exists( 'security_stop_user_enumeration' ) ) {
    function security_stop_user_enumeration( $redirect, $request ) {
        if ( preg_match( '/\?author=([0-9]*)(\/*)/i', $request ) ) {
            wp_redirect( get_site_url(), 301 );
            die();
        } else {
            return $redirect;
        }
    }
}


//禁用REST API功能
add_action( 'rest_pre_dispatch', 'deactivate_rest_api' );
add_action( 'rest_authentication_errors', 'deactivate_rest_api' );
function deactivate_rest_api() {
    status_header( 405 );
    die( '{"code":"rest_api_disabled","message":"REST API services are disabled on this site.","data":{"status":405}}' );
}

// Remove the REST API endpoint.
remove_action( 'rest_api_init', 'wp_oembed_register_route' );





//记录登陆失败发邮件
add_action( 'wp_authenticate', 'log_login', 10, 2 );
function log_login( $username, $password ) {

    if ( ! empty( $username ) && ! empty( $password ) ) {

        $check = wp_authenticate_username_password( NULL, $username, $password );
        if ( is_wp_error( $check ) ) {

            $ua = getBrowser();
            $agent = $ua['name'] . " " . $ua['version'];

            $referrer = ( isset( $_SERVER['HTTP_REFERER'] ) ) ? $_SERVER['HTTP_REFERER'] : $_SERVER['PHP_SELF'];
            if ( strstr( $referrer, 'wp-login' ) ) {
                $ref = 'wp-login.php';
            }

            if ( strstr( $referrer, 'wp-admin' ) ) {
                $ref = 'wp-admin/';
            }

            $contact_errors = false;

            // get the posted data
            $name = "WordPress " . get_bloginfo( 'name' );
            $email_address = get_bloginfo('admin_email' );

            // write the email content
            $header = "MIME-Version: 1.0\n";
            $header .= "Content-Type: text/html; charset=utf-8\n";
            $header .= "From: $name <$email_address>\n";

            $message = "Failed login attempt on <a href='" . get_site_url() . "/" . $ref . "' target='_blank'>" . $name . "</a><br>" . PHP_EOL;
            $message .= 'IP: <a href="http://whatismyipaddress.com/ip/' . get_ip_address() . '" target="_blank">' . get_ip_address() . "</a><br>" . PHP_EOL;
            $message .= 'WhoIs: <a href="https://who.is/whois-ip/ip-address/' . get_ip_address() . '" target="_blank">' . get_ip_address() . "</a><br>" . PHP_EOL;
            $message .= "Browser: " . $agent . "<br>" . PHP_EOL;
            $message .= "OS: " . $ua['platform'] . "<br>" . PHP_EOL;
            $message .= "Date: " . date('Y-m-d H:i:s') . "<br>" . PHP_EOL;
            $message .= "Referrer: " . $referrer . "<br>" . PHP_EOL;
            $message .= "User Agent: " . $ua['userAgent'] . "<br>" . PHP_EOL;
            $message .= "Username: " . $username . "<br>" . PHP_EOL;
            $message .= "Password: " . $password . "<br>" . PHP_EOL;

            $subject = "Failed login attempt - " . $name;
            $subject = "=?utf-8?B?" . base64_encode($subject) . "?=";

            $to = $email_address;

            if ( ! empty( $to ) ) {

                // send the email using wp_mail()
                if ( ! wp_mail( $to, $subject, $message, $header ) ) {
                    $contact_errors = true;
                }
            }

        }
    }
}
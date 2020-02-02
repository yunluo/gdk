<?php


function wpjam_feed_disabled() {
    wp_die('Feed已经关闭, 请访问网站<a href="'.get_bloginfo('url').'">首页</a>！');
}

add_action('do_feed',		'wpjam_feed_disabled', 1);
add_action('do_feed_rdf',	'wpjam_feed_disabled', 1);
add_action('do_feed_rss',	'wpjam_feed_disabled', 1);
add_action('do_feed_rss2',	'wpjam_feed_disabled', 1);
add_action('do_feed_atom',	'wpjam_feed_disabled', 1);

//禁用新版编辑器
if(gdk_option('gdk_diasble_gutenberg')){
add_filter('use_block_editor_for_post', '__return_false');
remove_action( 'wp_enqueue_scripts', 'wp_common_block_scripts_and_styles' );
}

add_filter('user_can_richedit','__return_false');  

//禁用响应式图片
function gdk_disable_srcset_img(){
	return 1;
}
add_filter('max_srcset_image_width', 'gdk_disable_srcset_img');

//移除默认的图片宽度以及高度
function gdk_remove_img_width($html) {
    $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
    return $html;
}
add_filter('post_thumbnail_html', 'gdk_remove_img_width', 10);
add_filter('image_send_to_editor', 'gdk_remove_img_width', 10);

//取消后台登陆错误的抖动提示
function gdk_wps_login_error() {
    remove_action('login_head', 'wp_shake_js', 12);
}
add_action('login_head', 'gdk_wps_login_error');

//清除wp_footer带入的embed.min.js
function gdk_deregister_embed_script() {
    wp_deregister_script('wp-embed');
}
add_action('wp_footer', 'gdk_deregister_embed_script');

//禁用默认的附件页面
function gdk_disable_attachment_pages() {
    global $post;
    if (is_attachment()) {
        if (!empty($post->post_parent)) {
            wp_redirect(get_permalink($post->post_parent) , 301);
            exit;
        } else {
            wp_redirect(home_url());
            exit;
        }
    }
}
add_action('template_redirect', 'gdk_disable_attachment_pages', 1);

// 友情链接扩展
add_filter('pre_option_link_manager_enabled', '__return_true');
//隐藏顶部工具栏
add_filter('show_admin_bar', '__return_false');
//关闭格式化
add_filter('run_wptexturize', '__return_false');
//禁用找回密码
add_filter('allow_password_reset', '__return_false' );
add_filter('wp_password_change_notification_email', '__return_false'); //关闭密码修改站长邮件
add_filter('password_change_email', '__return_false'); //关闭密码修改用户邮件
add_filter('wp_new_user_notification_email', '__return_false'); //关闭新用户注册用户邮件
//使链接自动可点击
add_filter('the_content', 'make_clickable');
//分类，标签描述添加图片
remove_filter('pre_term_description', 'wp_filter_kses');
remove_filter('pre_link_description', 'wp_filter_kses');
remove_filter('pre_link_notes', 'wp_filter_kses');
remove_filter('term_description', 'wp_kses_data');

//自动中英文空格
if (gdk_option('gdk_auto_space')) {
    function gdk_auto_space($data){
        $data = preg_replace('/([\\x{4e00}-\\x{9fa5}]+)([A-Za-z0-9_]+)/u', '${1} ${2}', $data);
        $data = preg_replace('/([A-Za-z0-9_]+)([\\x{4e00}-\\x{9fa5}]+)/u', '${1} ${2}', $data);
        return $data;
    }
    add_filter('the_content', 'gdk_auto_space');
}

function gdk_after_init_theme() {
	update_option( 'image_default_align', 'center' );//居中显示
	update_option( 'image_default_link_type', 'file' );//连接到媒体文件本身
    update_option( 'image_default_size', 'full' );//完整尺寸

    update_option( 'large_size_h', '0' );//关闭默认缩略图
    update_option( 'large_size_w', '0' );//关闭默认缩略图
    update_option( 'medium_large_size_h', '0' );//关闭默认缩略图
    update_option( 'medium_large_size_w', '0' );//关闭默认缩略图
    update_option( 'medium_size_h', '0' );//关闭默认缩略图
    update_option( 'medium_size_w', '0' );//关闭默认缩略图
    if(get_option('permalink_structure') == ''){//如果是默认连接格式
    update_option( 'permalink_structure', '/archives/%post_id%.html' );//固定链接格式
    }
    update_option( 'posts_per_page', '30' );//每页文章数目
}
add_action( 'after_setup_theme', 'gdk_after_init_theme' );

//新标签打开顶部网站链接
function gdk_blank_site_bar( $wp_admin_bar ) {
    $node = $wp_admin_bar->get_node('view-site');
    $node->meta['target'] = '_blank';
    $wp_admin_bar->add_node($node);
}
add_action( 'admin_bar_menu', 'gdk_blank_site_bar', 80 );

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
    add_filter('automatic_updater_disabled', '__return_true');  // 彻底关闭自动更新
    remove_action('init', 'wp_schedule_update_checks'); // 关闭更新检查定时作业
    wp_clear_scheduled_hook('wp_version_check');            // 移除已有的版本检查定时作业
    wp_clear_scheduled_hook('wp_update_plugins');       // 移除已有的插件更新定时作业
    wp_clear_scheduled_hook('wp_update_themes');            // 移除已有的主题更新定时作业
    wp_clear_scheduled_hook('wp_maybe_auto_update');        // 移除已有的自动更新定时作业
    remove_action( 'admin_init', '_maybe_update_core' );        // 移除后台内核更新检查
    remove_action( 'load-plugins.php', 'wp_update_plugins' );   // 移除后台插件更新检查
    remove_action( 'load-update.php', 'wp_update_plugins' );
    remove_action( 'load-update-core.php', 'wp_update_plugins' );
    remove_action( 'admin_init', '_maybe_update_plugins' );
    remove_action( 'load-themes.php', 'wp_update_themes' );     // 移除后台主题更新检查
    remove_action( 'load-update.php', 'wp_update_themes' );
    remove_action( 'load-update-core.php', 'wp_update_themes' );
    remove_action( 'admin_init', '_maybe_update_themes' );
    add_filter( 'pre_site_transient_update_core', function (){return null;} );
    add_filter( 'pre_site_transient_update_plugins', function (){return null;} );
    add_filter( 'pre_site_transient_update_themes', function (){return null;} );
}

//禁用自带p标签的
remove_filter( 'the_content', 'wpautop' );
add_filter( 'the_content', 'wpautop' , 12);

// 禁止后台加载谷歌字体
function gdk_remove_open_sans() {
	wp_deregister_style( 'open-sans' );
	wp_register_style( 'open-sans', false );
	wp_enqueue_style('open-sans','');
}
add_action( 'init', 'gdk_remove_open_sans' );

// 禁止dns-prefetch
function gdk_remove_dns( $hints, $relation_type ) {
	if ( 'dns-prefetch' === $relation_type ) {
		return array_diff( wp_dependencies_unique_hosts(), $hints );
	}
	return $hints;
}
add_filter( 'wp_resource_hints', 'gdk_remove_dns', 10, 2 );

//强制阻止WordPress代码转义
function gdk_esc_html($content) {
    $regex = '/(<pre\s+[^>]*?class\s*?=\s*?[",\'].*?prettyprint.*?[",\'].*?>)(.*?)(<\/pre>)/sim';
    return preg_replace_callback($regex, 'gdk_esc_callback', $content);
}
function gdk_esc_callback($matches) {
    $tag_open = $matches[1];
    $content = $matches[2];
    $tag_close = $matches[3];
    $content = esc_html($content);
    return $tag_open . $content . $tag_close;
}
add_filter('the_content', 'gdk_esc_html', 2);
add_filter('comment_text', 'gdk_esc_html', 2);
//强制兼容<pre>
function gdk_prettify_replace($text) {
    $replace = array(
        '<pre>' => '<pre class="prettyprint linenums">'
    );
    $text = str_replace(array_keys($replace) , $replace, $text);
    return $text;
}
add_filter('the_content', 'gdk_prettify_replace');



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
	add_filter( 'wp_revisions_to_keep', 'gdk_revisions_to_keep', 10, 2 );
	function gdk_revisions_to_keep( $num, $post ) {
		return 0;
	}
	add_action('wp_print_scripts','gdk_disable_autosave');
	function gdk_disable_autosave() {
		wp_deregister_script('autosave');
	}
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
    
//阻止站内PingBack
function gdk_noself_ping(&$links) {
	$home = home_url();
	foreach ($links as $l => $link) if (0 === strpos($link, $home)) unset($links[$l]);
}
add_action('pre_ping', 'gdk_noself_ping');
//禁用 pingbacks, enclosures, trackbacks
remove_action('do_pings', 'do_all_pings', 10);
//去掉 _encloseme 和 do_ping 操作。
remove_action('publish_post', '_publish_post_hook', 5);
}


//禁用WordPress活动
function gdk_dweandw_remove() {
	remove_meta_box('dashboard_primary', get_current_screen() , 'side');
}
add_action('wp_network_dashboard_setup', 'gdk_dweandw_remove', 20);
add_action('wp_user_dashboard_setup', 'gdk_dweandw_remove', 20);
add_action('wp_dashboard_setup', 'gdk_dweandw_remove', 20);

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


//文件自动重命名
if(gdk_option('gdk_upload_rename')) {
	function gdk_upload_rename( $file ) {
		$info = pathinfo($file['name']);
		$ext = $info['extension'];
		$ignore_exts = ['zip', 'rar', '7z'];
		//被忽略的文件格式
		if (!in_array($ext, $ignore_exts)) {
			$filedate = date('YmdHis').mt_rand(100, 999);
			$file['name'] = $filedate.'.'.$ext;
		}
		return $file;
	}
	add_filter('wp_handle_upload_prefilter', 'gdk_upload_rename' );
}

// 禁用自动生成的图片尺寸
function gdk_disable_image_sizes($sizes) {
	unset($sizes['thumbnail']);// disable thumbnail size
	unset($sizes['medium']);// disable medium size
	unset($sizes['large']);// disable large size
	unset($sizes['medium_large']);// disable medium-large size
	unset($sizes['1536x1536']);// disable 2x medium-large size
	unset($sizes['2048x2048']);// disable 2x large size
	return $sizes;
}
add_action('intermediate_image_sizes_advanced', 'gdk_disable_image_sizes');
// 禁用缩放尺寸
add_filter('big_image_size_threshold', '__return_false');
// 禁用其他图片尺寸
function gdk_disable_other_image_sizes() {
	remove_image_size('post-thumbnail');// disable images added via set_post_thumbnail_size() 
	remove_image_size('another-size');// disable any other added image sizes
}
add_action('init', 'gdk_disable_other_image_sizes');

// 搜索结果为1时候自动跳转到对应页面
function gdk_redirect_single_search_result() {
	if ( is_search() ) {
		global $wp_query;
		if ($wp_query->post_count == 1) {
			wp_redirect( get_permalink( $wp_query->posts['0']->ID ) );
			exit();
		}
	}
}
add_action('template_redirect', 'gdk_redirect_single_search_result');

//搜索链接伪静态
function gdk_redirect_search() {
	if ( is_search() && ! empty( $_GET['s'] ) ) {
		wp_redirect( home_url( "/search/" ) . urlencode( get_query_var( 's' ) ) );
		exit();
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

//前台禁止加载语言包
add_filter('locale', function($locale) {
    $locale = ( is_admin() ) ? $locale : 'en_US';
    return $locale;
});

// 定制登录页面链接的连接
add_filter('login_headerurl', function (){
	return home_url();
});


// 定制登录页面链接的标题
add_filter('login_headertext', function (){
	return get_bloginfo('name');
});




    
    function gdk_custom_head_code() {
        $gdk_option = get_option('gdk_option');
        $site = $gdk_option['site'];
        echo $site['custom_head_code'];
    }
    add_action('wp_head', 'gdk_custom_head_code');



    function gdk_custom_footer_code() {
        $gdk_option = get_option('gdk_option');
        $site = $gdk_option['site'];
        echo $site['custom_footer_code'];
    }
    add_action('wp_footer', 'gdk_custom_footer_code');




if(gdk_option('gdk_no_category')){
    if (!function_exists('gdk_no_category_base_refresh_rules')):
        add_action('load-themes.php',  'gdk_no_category_base_refresh_rules');
        add_action('created_category', 'gdk_no_category_base_refresh_rules');
        add_action('edited_category', 'gdk_no_category_base_refresh_rules');
        add_action('delete_category', 'gdk_no_category_base_refresh_rules');
        function gdk_no_category_base_refresh_rules() {
            global $wp_rewrite;
            $wp_rewrite -> flush_rules();
        }
        add_action('init', 'gdk_no_category_base_permastruct');
        function gdk_no_category_base_permastruct() {
            global $wp_rewrite, $wp_version;
            if (version_compare($wp_version, '3.4', '<')) {
                // For pre-3.4 support
                $wp_rewrite -> extra_permastructs['category'][0] = '%category%';
            } else {
                $wp_rewrite -> extra_permastructs['category']['struct'] = '%category%';
            }
        }
        // Add our custom category rewrite rules
        add_filter('category_rewrite_rules', 'gdk_no_category_base_rewrite_rules');
        function gdk_no_category_base_rewrite_rules($category_rewrite) {
            //var_dump($category_rewrite); // For Debugging
            $category_rewrite = array();
            $categories = get_categories(array('hide_empty' => false));
            foreach ($categories as $category) {
                $category_nicename = $category -> slug;
                if ($category -> parent == $category -> cat_ID)// recursive recursion
                    $category -> parent = 0;
                elseif ($category -> parent != 0)
                    $category_nicename = get_category_parents($category -> parent, false, '/', true) . $category_nicename;
                $category_rewrite['(' . $category_nicename . ')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
                $category_rewrite['(' . $category_nicename . ')/page/?([0-9]{1,})/?$'] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
                $category_rewrite['(' . $category_nicename . ')/?$'] = 'index.php?category_name=$matches[1]';
            }
            // Redirect support from Old Category Base
            global $wp_rewrite;
            $old_category_base = get_option('category_base') ? get_option('category_base') : 'category';
            $old_category_base = trim($old_category_base, '/');
            $category_rewrite[$old_category_base . '/(.*)$'] = 'index.php?category_redirect=$matches[1]';
            
            return $category_rewrite;
        }
            
        // Add 'category_redirect' query variable
        add_filter('query_vars', 'gdk_no_category_base_query_vars');
        function gdk_no_category_base_query_vars($public_query_vars) {
            $public_query_vars[] = 'category_redirect';
            return $public_query_vars;
        }

        add_filter('request', 'gdk_no_category_base_request');
        function gdk_no_category_base_request($query_vars) {
            if (isset($query_vars['category_redirect'])) {
                $catlink = trailingslashit(get_option('home')) . user_trailingslashit($query_vars['category_redirect'], 'category');
                status_header(301);
                header("Location: $catlink");
                exit();
            }
            return $query_vars;
        }
    endif;
}


//站长评论邮件添加评论链接
function gdk_notify_postauthor($notify_message,$comment_ID) {
    $notify = $notify_message;
    $notify.= '<br/> 快速回复此评论: ' . admin_url("edit-comments.php").'#comment-'.$comment_ID;
    return $notify;
}
add_filter('comment_notification_text', 'gdk_notify_postauthor', 10, 2);



//添加后台个人信息
function gdk_contact_fields($contactmethods) {
    $contactmethods['qq'] = 'QQ';
    $contactmethods['sina_weibo'] = '新浪微博';
    $contactmethods['weixin'] = '微信';
    unset($contactmethods['yim']);
    unset($contactmethods['aim']);
    unset($contactmethods['jabber']);
    return $contactmethods;
}
add_filter('user_contactmethods', 'gdk_contact_fields');


//支持中文名注册，来自肚兜
function git_sanitize_user($username, $raw_username, $strict) {
    $username = wp_strip_all_tags($raw_username);
    $username = remove_accents($username);
    $username = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '', $username);
    $username = preg_replace('/&.+?;/', '', $username); // Kill entities
    if ($strict) {
        $username = preg_replace('|[^a-z\p{Han}0-9 _.\-@]|iu', '', $username);
    }
    $username = trim($username);
    $username = preg_replace('|\s+|', ' ', $username);
    return $username;
}
add_filter('sanitize_user', 'git_sanitize_user', 10, 3);

//仅显示作者自己的文章
function mypo_query_useronly($wp_query) {
    if (strpos($_SERVER['REQUEST_URI'], '/wp-admin/edit.php') !== false) {
        if (!current_user_can('manage_options')) {
            $wp_query->set('author', get_current_user_id());
        }
    }
}
add_filter('parse_query', 'mypo_query_useronly');
//在文章编辑页面的[添加媒体]只显示用户自己上传的文件
function only_my_upload_media($wp_query_obj) {
    global $pagenow;
    if (!is_a(wp_get_current_user(), 'WP_User')) return;
    if ('admin-ajax.php' != $pagenow || $_REQUEST['action'] != 'query-attachments') return;
    if (!current_user_can('manage_options') && !current_user_can('manage_media_library')) $wp_query_obj->set('author', get_current_user_id());
    return;
}
add_action('pre_get_posts', 'only_my_upload_media');
//在[媒体库]只显示用户上传的文件
function only_my_media_library($wp_query) {
    if (strpos($_SERVER['REQUEST_URI'], '/wp-admin/upload.php') !== false) {
        if (!current_user_can('manage_options') && !current_user_can('manage_media_library')) {
            $wp_query->set('author', get_current_user_id());
        }
    }
}
add_filter('parse_query', 'only_my_media_library');

// 添加一个新的列 ID
function ssid_column($cols) {
    $cols['ssid'] = 'ID';
    return $cols;
}
add_action('manage_users_columns', 'ssid_column');
function ssid_return_value($value, $column_name, $id) {
    if ($column_name == 'ssid') $value = $id;
    return $value;
}
add_filter('manage_users_custom_column', 'ssid_return_value', 10, 3);

//用户列表显示积分
add_filter('manage_users_columns', 'my_users_columns');
function my_users_columns($columns) {
    $columns['points'] = '金币';
    return $columns;
}
function output_my_users_columns($value, $column_name, $user_id) {
    if ($column_name == 'points') {
        $jinbi = Points::get_user_total_points($user_id, POINTS_STATUS_ACCEPTED);
        if ($jinbi != "") {
            $ret = $jinbi;
            return $ret;
        } else {
            $ret = '穷逼一个';
            return $ret;
        }
    }
    return $value;
}
add_action('manage_users_custom_column', 'output_my_users_columns', 10, 3);


//后台登陆数学验证码
if (gdk_option('gdk_login_verify')) {
    function gdk_login_verify(){
        $num1 = mt_rand(0, 20);
        $num2 = mt_rand(0, 20);
        echo "<p><label for='sum'> {$num1} + {$num2} = ?<br /><input type='text' name='sum' class='input' value='' size='25' tabindex='4'>" . "<input type='hidden' name='num1' value='{$num1}'>" . "<input type='hidden' name='num2' value='{$num2}'></label></p>";
    }
    add_action('login_form', 'gdk_login_verify');
    add_action('register_form', 'gdk_login_verify');
    function gdk_login_verify_val(){
        $sum = $_POST['sum'];
        switch ($sum) {
            case $_POST['num1'] + $_POST['num2']:
                break;
            case null:
                wp_die('错误: 请输入验证码&nbsp; <a href="javascript:;" onclick="javascript:history.back();">返回上页</a>');
                break;
            default:
                wp_die('错误: 验证码错误,请重试&nbsp; <a href="javascript:;" onclick="javascript:history.back();">返回上页</a>');
        }
    }
    add_action('login_form_login', 'gdk_login_verify_val');
    add_action('register_post', 'gdk_login_verify_val');
}


//用户增加评论数量
function gdk_users_comments($columns) {
    $columns['comments'] = '评论';
    return $columns;
}
add_filter('manage_users_columns', 'gdk_users_comments');
function gdk_show_users_comments($value, $column_name, $user_id) {
    if ($column_name == 'comments') {
        $comments_counts = get_comments(array(
            'status' => '1',
            'user_id' => $user_id,
            'count' => true
        ));
        if ($comments_counts != "") {
            $ret = $comments_counts;
            return $ret;
        } else {
            $ret = '暂未评论';
            return $ret;
        }
    }
    return $value;
}
add_action('manage_users_custom_column', 'gdk_show_users_comments', 10, 3);
// 添加一个字段保存IP地址
function gdk_log_ip($user_id) {
    $ip = $_SERVER['REMOTE_ADDR'];
    update_user_meta($user_id, 'signup_ip', $ip);
}
add_action('user_register', 'gdk_log_ip');
// 添加“IP地址”这个栏目
function gdk_signup_ip($column_headers) {
    $column_headers['signup_ip'] = 'IP地址';
    return $column_headers;
}
add_filter('manage_users_columns', 'gdk_signup_ip');
function gdk_ripms_columns($value, $column_name, $user_id) {
    if ($column_name == 'signup_ip') {
        $ip = get_user_meta($user_id, 'signup_ip', true);
        if ($ip != "") {
            $ret = $ip;
            return $ret;
        } else {
            $ret = '没有记录';
            return $ret;
        }
    }
    return $value;
}
add_action('manage_users_custom_column', 'gdk_ripms_columns', 10, 3);
// 创建一个新字段存储用户登录时间
function gdk_insert_last_login($login) {
    $user = get_user_by('login', $login);
    update_user_meta($user->ID, 'last_login', current_time('mysql'));
}
add_action('wp_login', 'gdk_insert_last_login');
// 添加一个新栏目“上次登录”
function gdk_add_last_login_column($columns) {
    $columns['last_login'] = '上次登录';
    unset($columns['name']);
    return $columns;
}
add_filter('manage_users_columns', 'gdk_add_last_login_column');
// 显示登录时间到新增栏目
function gdk_add_last_login_column_value($value, $column_name, $user_id) {
    if ($column_name == 'last_login') {
        $login = get_user_meta($user_id, 'last_login', true);
        if ($login != "") {
            $ret = $login;
            return $ret;
        } else {
            $ret = '暂未登录';
            return $ret;
        }
    }
    return $value;
}
add_action('manage_users_custom_column', 'gdk_add_last_login_column_value', 10, 3);
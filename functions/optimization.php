<?php


function gdk_feed_disabled() {
    wp_die('Feed已经关闭, 请访问网站<a href="'.get_bloginfo('url').'">首页</a>！');
}

add_action('do_feed',		'gdk_feed_disabled', 1);
add_action('do_feed_rdf',	'gdk_feed_disabled', 1);
add_action('do_feed_rss',	'gdk_feed_disabled', 1);
add_action('do_feed_rss2',	'gdk_feed_disabled', 1);
add_action('do_feed_atom',	'gdk_feed_disabled', 1);

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
//关闭WordPress欢迎
remove_action('welcome_panel', 'wp_welcome_panel');

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
    update_option( 'thumbnail_size_w', '0' );//关闭默认缩略图
    update_option( 'thumbnail_size_h', '0' );//关闭默认缩略图
    update_option( 'default_ping_status', 'closed' );//关闭默认ping状态
    update_option( 'comment_order', 'desc' );//关闭默认评论显示顺序
    if(get_option('permalink_structure') == ''|| define( 'GDK_HTML_LINK', true ) ){//如果是默认连接格式或者主题声明 define( 'GDK_HTML_LINK', true ); 
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

//去掉后台帮助
add_action('in_admin_header', function(){
	global $current_screen;
	$current_screen->remove_help_tabs();
});
//清理菜单类
add_filter( 'nav_menu_css_class', function ( $var ) {
	return is_array( $var ) ? array_intersect( $var, [ 'current-menu-item', 'menu-item', 'menu-item-has-children' ] ) : '';
}
, 100, 1 );

//移除前台加载jquery-migrate
function gdk_disable_migrate( $scripts ) {
    if ( ! is_admin() && ! empty( $scripts->registered['jquery'] ) ) {
        $scripts->registered['jquery']->deps = array_diff(
            $scripts->registered['jquery']->deps,
            [ 'jquery-migrate' ]
        );
    }
}
add_action( 'wp_default_scripts', 'gdk_disable_migrate' );

//移除 WordPress 标记
add_filter( 'the_generator', function () { return '';});

//移除标题中的空字符
add_filter( 'wp_title', function ( $title ) { return trim( $title );});

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

add_action( 'wp_before_admin_bar_render', function () {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu( 'wp-logo' );
	$wp_admin_bar->remove_menu( 'about' );
	$wp_admin_bar->remove_menu( 'wporg' );
	$wp_admin_bar->remove_menu( 'documentation' );
	$wp_admin_bar->remove_menu( 'support-forums' );
	$wp_admin_bar->remove_menu( 'feedback' );
} );

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

//WordPress 彻底移除后台“隐私”设置功能
add_filter( 'map_meta_cap', 'ds_disable_core_privacy_tools', 10, 2 );
remove_action( 'init', 'wp_schedule_delete_old_privacy_export_files' );
remove_action( 'wp_privacy_delete_old_export_files', 'wp_privacy_delete_old_export_files' );
function ds_disable_core_privacy_tools( $caps, $cap ) {
	switch ( $cap ) {
		case 'export_others_personal_data':
		case 'erase_others_personal_data':
		case 'manage_privacy_options':
			$caps[] = 'do_not_allow';
			break;
	}
	return $caps;
}

// 禁止dns-prefetch
function gdk_remove_dns( $hints, $relation_type ) {
	if ( 'dns-prefetch' === $relation_type ) {
		return array_diff( wp_dependencies_unique_hosts(), $hints );
	}
	return $hints;
}
add_filter( 'wp_resource_hints', 'gdk_remove_dns', 10, 2 );


//第一段
function first_paragraph( $content ) {
    return preg_replace( '/<p([^>]+)?>/', '<p$1 class="lead">', $content, 1 );
}
add_filter( 'the_content', 'first_paragraph' );


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

//文章最多保存 5 个版本,默认的
if ( ! defined( 'WP_POST_REVISIONS' ) ) {
	define( 'WP_POST_REVISIONS', 5 );
}

//前台禁用dashicon和editor
if (gdk_option('gdk_disable_dashicons')) {
	add_action( 'init', function () {
		if ( ! is_user_logged_in() ) {
			wp_deregister_style( 'dashicons' );
			wp_register_style( 'dashicons', false );
			wp_enqueue_style( 'dashicons', '' );
			wp_deregister_style( 'editor-buttons' );
			wp_register_style( 'editor-buttons', false );
			wp_enqueue_style( 'editor-buttons', '' );
		}
	}
	);
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

//替换后台默认的底部文字内容
function gdk_replace_footer_admin() {
	$result = apply_filters('gdk_filter_admin_footer_text', '由GDK插件提供底层支持');
	echo $result;
}
add_filter('admin_footer_text', 'gdk_replace_footer_admin');


//禁用REST API功能
if(gdk_option('gdk_disable_restapi')) {
add_action( 'rest_pre_dispatch', 'deactivate_rest_api' );
add_action( 'rest_authentication_errors', 'deactivate_rest_api' );
function deactivate_rest_api() {
    status_header( 405 );
    wp_die( '{"code":"rest_api_disabled","message":"REST API services are disabled on this site.","data":{"status":405}}' );
}
// Remove the REST API endpoint.
remove_action( 'rest_api_init', 'wp_oembed_register_route' );
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
	$gdk_custom_head_code = gdk_option('gdk_custom_head_code');
	echo $gdk_custom_head_code;
}
add_action('wp_head', 'gdk_custom_head_code');

function gdk_custom_footer_code() {
	$gdk_custom_footer_code = gdk_option('gdk_custom_foot_code');
	echo $gdk_custom_footer_code;
}
add_action('wp_footer', 'gdk_custom_footer_code',400);

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
            global $wp_rewrite;
                $wp_rewrite -> extra_permastructs['category']['struct'] = '%category%';
        }
        // Add our custom category rewrite rules
        add_filter('category_rewrite_rules', 'gdk_no_category_base_rewrite_rules');
        function gdk_no_category_base_rewrite_rules($category_rewrite) {
            //var_dump($category_rewrite); // For Debugging
            $category_rewrite = array();
            $categories = get_categories(array('hide_empty' => false));
            foreach ($categories as $category) {
                $gdk_category = $category -> slug;
                if ($category -> parent == $category -> cat_ID)// recursive recursion
                    $category -> parent = 0;
                elseif ($category -> parent != 0)
                    $gdk_category = get_category_parents($category -> parent, false, '/', true) . $gdk_category;
                $category_rewrite['(' . $gdk_category . ')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
                $category_rewrite['(' . $gdk_category . ')/page/?([0-9]{1,})/?$'] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
                $category_rewrite['(' . $gdk_category . ')/?$'] = 'index.php?category_name=$matches[1]';
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
function gdk_notify_admin($notify_message,$comment_ID) {
    $notify = $notify_message;
    $notify.= '<br/> 快速回复此评论: ' . admin_url("edit-comments.php").'#comment-'.$comment_ID;
    return $notify;
}
add_filter('comment_notification_text', 'gdk_notify_admin', 10, 2);

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
function gdk_sanitize_user($username, $raw_username, $strict) {
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
add_filter('sanitize_user', 'gdk_sanitize_user', 10, 3);

// 添加一个新的列 ID
function gdk_userid_column($cols) {
    $cols['ssid'] = 'ID';
    return $cols;
}
add_action('manage_users_columns', 'gdk_userid_column');
function gdk_userid_value($value, $column_name, $id) {
    if ($column_name == 'ssid') $value = $id;
    return $value;
}
add_filter('manage_users_custom_column', 'gdk_userid_value', 10, 3);

//用户列表显示积分
add_filter('manage_users_columns', 'gdk_points_columns');
function gdk_points_columns($columns) {
    $columns['points'] = '金币';
    return $columns;
}
function gdk_points_value($value, $column_name, $user_id) {
    if ($column_name == 'points') {
        $jinbi = GDK_Points::get_user_total_points($user_id, 'accepted');
        if ($jinbi != "") {
            $ret = $jinbi;
            return $ret;
        } else {
            $ret = '暂无充值';
            return $ret;
        }
    }
    return $value;
}
add_action('manage_users_custom_column', 'gdk_points_value', 10, 3);

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
    $ip = gdk_get_ip();
    update_user_meta($user_id, 'signup_ip', $ip);
}
add_action('user_register', 'gdk_log_ip');
// 添加IP地址这个栏目
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
// 添加一个新栏目上次登录
function gdk_add_last_login_column($columns) {
    $columns['last_login'] = '上次登录';
    unset($columns['name']);
    return $columns;
}
add_filter('manage_users_columns', 'gdk_add_last_login_column');
// 显示登录时间到新增栏目
function gdk_add_last_login($value, $column_name, $user_id) {
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
add_action('manage_users_custom_column', 'gdk_add_last_login', 10, 3);

// 评论添加@，来自：http://www.ludou.org/wordpress-comment-reply-add-at.html
function gdk_comment_add_at($comment_text, $comment = '') {
    if ($comment->comment_parent > 0) {
        $comment_text = '@<a href="#comment-' . $comment->comment_parent . '">' . get_comment_author($comment->comment_parent) . '</a> ' . $comment_text;
    }
    return $comment_text;
}
add_filter('comment_text', 'gdk_comment_add_at', 20, 2);

//搜索结果排除所有页面
function gdk_search_filter_page($query) {
    if ($query->is_search && !$query->is_admin) {
        $query->set('post_type', 'post');
    }
    return $query;
}
add_filter('pre_get_posts', 'gdk_search_filter_page');
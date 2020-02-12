<?php
/**
 * 各页面重写规则
 */

//页面伪静态规则
function gdk_page_permalink() {
	global $wp_rewrite;
	if (!strpos($wp_rewrite->get_page_permastruct(), '.html')) {
		$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.html';
	}
}
add_action('init', 'gdk_page_permalink', -1);


// 更新重写规则
function gdk_rewrite_flush_rules(){
    $rules = get_option( 'rewrite_rules' );
    if ( !isset( $rules['^sitemap(.*?)\.xml$'] , $rules['^sitemap(.*?)\.html$'], $rules['^daohang(.*?)\.html$'] ) ) {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }
}
add_action( 'wp_loaded', 'gdk_rewrite_flush_rules' );


// 添加自定义URL重写
function gdk_add_custom_rewrite_rule() {
    add_rewrite_rule('^sitemap(.*?)\.xml$','index.php?sitemap=gdk$matches[1]','top');//xml地图规则
    add_rewrite_rule('^sitemap(.*?)\.html$','index.php?sitemap=gdkk$matches[1]','top');//html地图规则
    add_rewrite_rule('^daohang(.*?)\.html$','index.php?daohang=gdkk$matches[1]','top');//导航页面规则
    add_rewrite_rule('^weauth','index.php?user=$matches[1]&sk=$matches[2]','top');//WeAuth微信登陆二维码规则
    add_rewrite_rule('^goauth','index.php?userinfo=$matches[1]&sk=$matches[2]','top');//GoAuth微信登陆二维码规则
}
add_action( 'init', 'gdk_add_custom_rewrite_rule' , 10, 0);


//自定义链接跳转
function gdk_custom_cancel_redirect( $redirect_url ) {
    $api_sitemap = get_query_var('sitemap');
    $api_daohang = get_query_var('daohang');
	if ( !empty($api_sitemap) || !empty($api_daohang) ){
		return false;
	}else{
		return $redirect_url; 
	}
}
add_filter( 'redirect_canonical', 'gdk_custom_cancel_redirect' );


//自定义路由参数,GO跳转和dl下载
function gdk_public_query_vars($public_query_vars){
    $public_query_vars[] = 'go';
    $public_query_vars[] = 'dl';
    $public_query_vars[] = 'sitemap';
    $public_query_vars[] = 'daohang';
    return $public_query_vars;
}
add_action('query_vars', 'gdk_public_query_vars');


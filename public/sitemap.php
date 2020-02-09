<?php
/**
 * SiteMap HTML 版
 */
// ----------------------
// 开一个api的统一URL
function gdk_sitemap_html_flush_rules(){
    $rules = get_option( 'rewrite_rules' );
    if ( !isset( $rules['^sitemap(.*?)\.html$'] ) ) {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }
}

// 添加自定义URL重写
function gdk_sitemap_html_custom_rewrite_rule() {
    add_rewrite_rule('^sitemap(.*?)\.html$','index.php?sitemap=gdkk$matches[1]','top');
}

function gdk_sitemap_html_insert_query_vars( $vars ){
    array_push($vars, 'sitemap');
    return $vars;
}

function gdk_sitemap_html_cancel_redirect( $redirect_url ) {
	$api_type = get_query_var('sitemap');
	if ( !empty($api_type) ){
		return false;
	}else{
		return $redirect_url; 
	}
}

function gdk_sitemap_html_api_handlers( $template ){

    $hook = explode('-', get_query_var( 'sitemap' ) );
    
    if( isset( $hook[0] ) && $hook[0] === 'gdkk' ){

        if( isset( $hook[1] ) ){
            status_header(404);
            header('HTTP/1.0 404 Not Found');
            $GLOBALS['wp_query']->set_404();
            include( get_query_template( '404' ) );      
            exit; 
        }

        $sitemap = get_transient('gdk-sitemap-html');
        
        if( false === $sitemap || empty( $sitemap ) ){
            $sitemap = gdk_create_html_sitemap();
            set_transient( 'gdk-sitemap-html', $sitemap );
        }
        header("Content-type: text/html");
        $sitemap;
        return;
    }

	return $template;
}

add_action( 'init', 'gdk_sitemap_html_custom_rewrite_rule' , 10, 0);
add_filter( 'query_vars', 'gdk_sitemap_html_insert_query_vars' );
add_filter( 'redirect_canonical', 'gdk_sitemap_html_cancel_redirect' );
add_action( 'wp_loaded', 'gdk_sitemap_html_flush_rules' );
add_filter( 'template_include', 'gdk_sitemap_html_api_handlers', 99 );

function gdk_create_html_sitemap() {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head profile="http://gmpg.org/xfn/11">
        <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>"/>
        <title>站点地图 -<?php bloginfo( 'name'); ?></title>
        <meta name="keywords" content="站点地图,<?php bloginfo('name'); ?>" />
        <meta name="copyright" content="<?php bloginfo('name'); ?>" />
        <link rel="canonical" href="<?php echo get_permalink(); ?>" />
        <style type="text/css">
            body {font-family: Microsoft Yahei,Verdana;font-size:13px;margin:0 auto;color:
            #000000;background: #ffffff;width: 990px;margin: 0 auto}a:link,a:visited
            {color:#000;text-decoration:none;}a:hover {color:#08d;text-decoration:none;}h1,h2,h3,h4,h5,h6
            {font-weight:normal;}img {border:0;}li {margin-top: 8px;}.page {padding:
            4px; border-top: 1px #EEEEEE solid}.author {background-color:#EEEEFF; padding:
            6px; border-top: 1px #ddddee solid}#nav, #content, #footer {padding: 8px;
            border: 1px solid #EEEEEE; clear: both; width: 95%; margin: auto; margin-top:
            10px;}
        </style>
    </head>

    <body vlink="#333333" link="#333333">
        <h2 style="text-align: center; margin-top: 20px">
            <?php bloginfo( 'name'); ?>'s SiteMap</h2>
        <div id="nav">
            <a href="<?php echo esc_url( home_url() ); ?>/">
                <strong><?php bloginfo( 'name'); ?></strong>
            </a>
            &raquo;
            <a href="<?php echo get_permalink(); ?>">站点地图</a>
        </div>
        <div id="content">
            <h3>最新文章</h3>
            <ul>
                <?php 
                    $previous_year = $year = 0;
                    $previous_month = $month = 0;
                    $ul_open = false;
                    $myposts = get_posts('numberposts=-1&orderby=post_date&order=DESC');
                    foreach($myposts as $post):?>
                    <li>
                        <a href="<?php the_permalink($post->ID); ?>" title="<?php echo $post->post_title; ?>" target="_blank"><?php echo $post->post_title; ?></a>
                    </li>
                <?php endforeach;?>
            </ul>
        </div>
        <div id="content">
            <h3>分类目录</h3>
            <ul><?php wp_list_categories( 'title_li='); ?></ul>
        </div>
        <div id="content">
            <h3>单页面</h3>
            <?php wp_page_menu( $args ); ?>
        </div>
        <div id="footer">
            查看博客首页:
            <strong>
                <a href="<?php echo esc_url( home_url() ); ?>/"><?php bloginfo( 'name'); ?></a>
            </strong>
        </div>
        <br />
        <div style="text-align: center; font-size: 11px">
            Latest Update:
            <?php 
            global $wpdb;
                $last = $wpdb->get_results("SELECT MAX(post_modified) AS MAX_m FROM $wpdb->posts WHERE (post_type = 'post' OR post_type = 'page') AND (post_status = 'publish' OR post_status = 'private')");
                $last = date('Y-m-d G:i:s', strtotime($last[0]->MAX_m));
                echo $last;
            ?>
                <br />
                <br />
        </div>
    </body>
</html>
<?php
}

function gdk_clear_sitemap_html_cache(){
    delete_transient( 'gdk-sitemap-html' );
}
add_action('publish_post', 'gdk_clear_sitemap_html_cache');
add_action('publish_page', 'gdk_clear_sitemap_html_cache');
add_action('save_post', 'gdk_clear_sitemap_html_cache' );
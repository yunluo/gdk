<?php
// ----------------------
// 开一个api的统一URL
function sitemap_xml_flush_rules(){
    $rules = get_option( 'rewrite_rules' );
    if ( !isset( $rules['^sitemap(.*?)\.xml$'] ) ) {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }
}

// 添加自定义URL重写
function sitemap_xml_custom_rewrite_rule() {
    add_rewrite_rule('^sitemap(.*?)\.xml$','index.php?sitemap=gdk$matches[1]','top');
}

function sitemap_xml_insert_query_vars( $vars ){
    array_push($vars, 'sitemap');
    return $vars;
}

function sitemap_xml_cancel_redirect( $redirect_url ) {
	$api_type = get_query_var('sitemap');
	if ( !empty($api_type) ){
		return false;
	}else{
		return $redirect_url; 
	}
}

function sitemap_xml_api_handlers( $template ){

    $hook = explode('-', get_query_var( 'sitemap' ) );
    
    if( isset( $hook[0] ) && $hook[0] === 'gdk' ){

        if( isset( $hook[1] ) ){
            status_header(404);
            header('HTTP/1.0 404 Not Found');
            $GLOBALS['wp_query']->set_404();
            include( get_query_template( '404' ) );      
            exit; 
        }

        $sitemap = get_transient('gdk-sitemap');
        
        if( false === $sitemap || empty( $sitemap ) ){
            $sitemap = gdk_create_sitemap();
            set_transient( 'gdk-sitemap', $sitemap );
        }

        header("Content-type: text/xml");
        echo $sitemap;
        return;
    }

	return $template;
}

add_action( 'init', 'sitemap_xml_custom_rewrite_rule' , 10, 0);
add_filter( 'query_vars', 'sitemap_xml_insert_query_vars' );
add_filter( 'redirect_canonical', 'sitemap_xml_cancel_redirect' );
add_action( 'wp_loaded', 'sitemap_xml_flush_rules' );
add_filter( 'template_include', 'sitemap_xml_api_handlers', 99 );

function gdk_create_sitemap() {

    if ( str_replace( '-', '', get_option( 'gmt_offset' ) ) < 10 ) {
        $tempo = '-0' . str_replace( '-', '', get_option( 'gmt_offset' ) );
    } else {
        $tempo = get_option( 'gmt_offset' );
    }
    if( strlen( $tempo ) == 3 ) { $tempo = $tempo . ':00'; }
    $postsForSitemap = get_posts( array(
        'numberposts' => -1,
        'orderby'     => 'modified',
        'post_type'   => array('post'),
        'order'       => 'DESC',
    ) );
    $sitemap = '';
    $sitemap .= '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap .= "\n" . '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    $sitemap .= "\t" . '<url>' . "\n" .
        "\t\t" . '<loc>' . esc_url( home_url( '/' ) ) . '</loc>' .
        "\n\t\t" . '<lastmod>' . date( "Y-m-d\TH:i:s", current_time( 'timestamp', 0 ) ) . $tempo . '</lastmod>' .
        "\n\t\t" . '<changefreq>daily</changefreq>' .
        "\n\t\t" . '<priority>1.0</priority>' .
        "\n\t" . '</url>' . "\n";
    foreach( $postsForSitemap as $post ) {
        setup_postdata( $post);
        $postdate = explode( " ", $post->post_modified );
        $sitemap .= "\t" . '<url>' . "\n" .
            "\t\t" . '<loc>' . get_permalink( $post->ID ) . '</loc>' .
            "\n\t\t" . '<lastmod>' . $postdate[0] . 'T' . $postdate[1] . $tempo . '</lastmod>' .
            "\n\t\t" . '<changefreq>Weekly</changefreq>' .
            "\n\t\t" . '<priority>0.5</priority>' .
            "\n\t" . '</url>' . "\n";
    }
    $sitemap .= '</urlset>';
    return $sitemap;
}

function gdk_clear_sitemap_cache(){
    delete_transient( 'gdk-sitemap' );
}
add_action("publish_post", "gdk_clear_sitemap_cache");
add_action("publish_page", "gdk_clear_sitemap_cache");
add_action( "save_post", "gdk_clear_sitemap_cache" );

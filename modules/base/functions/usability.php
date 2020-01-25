<?php
$nc_option = get_option('nc_option');
$usability = $nc_option['usability'];

if ($usability['friendly_images']) {
  //给文章图片自动添加alt和title信息
    if (!function_exists('nc_imagesalt')):
        function nc_imagesalt($content) {
            global $post;
            $pattern ="/<a(.*?)href=('|\")(.*?).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>/i";
            $replacement = '<a$1href=$2$3.$4$5 alt="'.strip_tags($post->post_title).'" title="'.strip_tags($post->post_title).'"$6>';
            $content = preg_replace($pattern, $replacement, $content);
            return $content;
        }
        add_filter('the_content', 'nc_imagesalt');
    endif;
    if (!function_exists('nc_image_alt_tag')):
        function nc_image_alt_tag($content) {
            global $post;
            preg_match_all('/<img (.*?)\/>/', $content, $images);
            if(!is_null($images)) {
            foreach($images[1] as $index => $value){
                $new_img = str_replace('<img', '<img alt="'.strip_tags($post->post_title).'-'.get_option('blogname').'"', $images[0][$index]);
                $content = str_replace($images[0][$index], $new_img, $content);
            }
            }
            return $content;
        }
        add_filter('the_content', 'nc_image_alt_tag', 99999);
    endif;
}

if ($usability['no_category_base']) {
    if (!function_exists('nc_no_category_base_refresh_rules')):
        add_action('load-themes.php',  'nc_no_category_base_refresh_rules');
        add_action('created_category', 'nc_no_category_base_refresh_rules');
        add_action('edited_category', 'nc_no_category_base_refresh_rules');
        add_action('delete_category', 'nc_no_category_base_refresh_rules');
        function nc_no_category_base_refresh_rules() {
            global $wp_rewrite;
            $wp_rewrite -> flush_rules();
        }
        add_action('init', 'nc_no_category_base_permastruct');
        function nc_no_category_base_permastruct() {
            global $wp_rewrite, $wp_version;
            if (version_compare($wp_version, '3.4', '<')) {
                // For pre-3.4 support
                $wp_rewrite -> extra_permastructs['category'][0] = '%category%';
            } else {
                $wp_rewrite -> extra_permastructs['category']['struct'] = '%category%';
            }
        }
        // Add our custom category rewrite rules
        add_filter('category_rewrite_rules', 'nc_no_category_base_rewrite_rules');
        function nc_no_category_base_rewrite_rules($category_rewrite) {
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
        add_filter('query_vars', 'nc_no_category_base_query_vars');
        function nc_no_category_base_query_vars($public_query_vars) {
            $public_query_vars[] = 'category_redirect';
            return $public_query_vars;
        }

        add_filter('request', 'nc_no_category_base_request');
        function nc_no_category_base_request($query_vars) {
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

if ($usability['auto_rename_media_files']) {
    if (!function_exists('nc_rename_upload_filter')):
        add_filter('wp_handle_upload_prefilter', 'nc_rename_upload_filter' );

        function nc_rename_upload_filter( $file ){
            $info = pathinfo($file['name']);
            $ext = $info['extension'];
            $ignore_exts = ['zip', 'rar', '7z'];

            if (!in_array($ext, $ignore_exts)) {
                $filedate = date('YmdHis').rand(10,99); //为了避免时间重复，再加一段2位的随机数
                $file['name'] = $filedate.'.'.$ext;
            }
            return $file;
        }
    endif;
}
if (!function_exists('nc_custom_head_code')):
    add_action('wp_head', 'nc_custom_head_code');
    function nc_custom_head_code() {
        $nc_option = get_option('nc_option');
        $site = $nc_option['site'];
        echo $site['custom_head_code'];
    }
endif;

if (!function_exists('nc_custom_footer_code')):
    add_action('wp_footer', 'nc_custom_footer_code');
    function nc_custom_footer_code() {
        $nc_option = get_option('nc_option');
        $site = $nc_option['site'];
        echo $site['custom_footer_code'];
    }
endif;

if ($usability['disable_admin_bar']) {
    add_filter('show_admin_bar', '__return_false');
}

if( $usability['nc_highlight'] == 1 ){

	function usability_highlight(){
		if( is_single() || is_singular() ){

			wp_register_script( 'ncHighlightJs', NC_OPTIMIZEUP_URL . 'assets/highlight/highlight.pack.js', array(), NC_OPTIMIZEUP_VERSION, true );
			wp_register_style( 'ncHighlightCss', NC_OPTIMIZEUP_URL . 'assets/highlight/styles/a11y-dark.css', array(), NC_OPTIMIZEUP_VERSION, 'all' );

			wp_enqueue_style('ncHighlightCss');
		    wp_enqueue_script('ncHighlightJs');


		}
	}
	add_action( 'wp_enqueue_scripts', 'usability_highlight' );
}

if( $usability['nc_highlight'] == 1 && is_admin() ){

	function nicetheme_highlight_register_mce_button( $buttons ) {
		array_push( $buttons, 'nicetheme_highlight_button' );
		return $buttons;
	}
	add_filter( 'mce_buttons', 'nicetheme_highlight_register_mce_button' );

	function nicetheme_highlight_tinymce_plugin( $plugin_array ) {
		$plugin_array['nicetheme_highlight_button'] = NC_OPTIMIZEUP_URL .'assets/highlight/admin.js';
		return $plugin_array;
	}

	add_filter( 'mce_external_plugins', 'nicetheme_highlight_tinymce_plugin' );
    

}

if( $usability['nc_lazyload'] == 1 && !is_admin() ){

	require_once( NC_OPTIMIZEUP_DIR . '/lib/phpQuery/phpQuery.php' );

	function usability_lazyload(){

		wp_register_script( 'ncLazyloadJs', NC_OPTIMIZEUP_URL . 'assets/lazyload/lazyload.min.js', array(), NC_OPTIMIZEUP_VERSION, true );
		wp_enqueue_script('ncLazyloadJs');

	}
	add_action( 'wp_enqueue_scripts', 'usability_lazyload' );

	add_action( 'wp', 'nc_lazyload_start');

}


function nc_lazyload_start(){
	ob_start( 'lazyload_replaces' );
}

function lazyload_replaces( $buffer ){

	$html = phpQuery::newDocument( $buffer );
	phpQuery::selectDocument($html);
	pq('body pre *,body code *')->addClass('nc-no-lazy');
	$images = pq('body img:not(.nc-no-lazy, .post_cover_image, .loading)');
	$other = pq('body *[style]:not(.nc-no-lazy, .post_cover_image, .loading)');
	pq('body pre *,body code *')->removeClass('nc-no-lazy');

	foreach ($images as $key => $image) {

		$src = pq( $image )->attr( 'src' );

		if( $src ){
			pq( $image )
			->attr( 'src', 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' )
			->attr( 'data-src', $src )
			->attr( 'data-nclazyload', 'true');
		}

		$srcset = pq( $image )->attr( 'srcset' );
		if( $srcset ){
			pq( $image )->attr('data-srcset', $srcset )->removeAttr('srcset');
		}
		
		$sizes = pq( $image )->attr( 'sizes' );
		if( $sizes ){
			pq( $image )->attr('data-sizes', $sizes )->removeAttr('sizes');
		}

	}

	foreach ($other as $key => $item) {

		$style = pq($item)->attr('style');

		preg_match('/(.*?)background(-image)?:(\s?url\(.*?\))(.*?)/im', $style, $matches );

		if( isset( $matches[3] ) ){
			pq($item)->attr('style', $matches[1] . 'background' . $matches[2] . ':url("data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7")' . $matches[4] );
			pq($item)->attr('data-bg', $matches[3])->attr( 'data-nclazyload', 'true');
		}


	}

	return $html->htmlOuter();

}

if( $usability['nc_lightgallery'] == 1 && !is_admin() ){

	function usability_lightgallery(){
		if( is_single() || is_singular() ){
		    wp_register_style( 'ncLightgalleryCss', NC_OPTIMIZEUP_URL . 'assets/lightGallery/css/lightgallery.min.css', array(), NC_OPTIMIZEUP_VERSION, 'all' );
		    wp_register_script( 'ncPicturefill', NC_OPTIMIZEUP_URL . 'assets/lightGallery/lib/picturefill.min.js', array(), NC_OPTIMIZEUP_VERSION, true );
		    wp_register_script( 'ncLightgallery', NC_OPTIMIZEUP_URL . 'assets/lightGallery/js/lightgallery-all.min.js', array('jquery'), NC_OPTIMIZEUP_VERSION, true );
		    wp_register_script( 'ncMousewheel', NC_OPTIMIZEUP_URL . 'assets/lightGallery/lib/jquery.mousewheel.min.js', array('jquery'), NC_OPTIMIZEUP_VERSION, true );

		    wp_enqueue_style('ncLightgalleryCss');
		    wp_enqueue_script('ncPicturefill');
		    wp_enqueue_script('ncLightgallery');
		    wp_enqueue_script('ncMousewheel');
		}
	}
	add_action( 'wp_enqueue_scripts', 'usability_lightgallery' );

	function lightgallery_box( $content ){
		return '<div class="nc-light-gallery">' . $content . '</div>';
	}
	add_filter( 'the_content', 'lightgallery_box' );

}

if ($nc_option['wechat_qq_share_custom']) {
	include( NC_STORE_ROOT_PATH . 'modules/pluggable/custom_share.php' );
}

if( isset( $usability['hide_author_url_user_name'] ) && $usability['hide_author_url_user_name'] == 1 ){
	
	function nice_text_encrypt($string, $operation, $key = '') {
	    $string = $operation == 'D' ? str_replace( array('!','-','_'), array('=','+','/'), $string ) : $string;
	    $key = md5($key);
	    $key_length = strlen($key);
	    $string = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key) , 0, 8) . $string;
	    $string_length = strlen($string);
	    $rndkey = $box = array();
	    $result = '';
	    for ($i = 0; $i <= 255; $i++) {
	        $rndkey[$i] = ord($key[$i % $key_length]);
	        $box[$i] = $i;
	    }
	    for ($j = $i = 0; $i < 256; $i++) {
	        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
	        $tmp = $box[$i];
	        $box[$i] = $box[$j];
	        $box[$j] = $tmp;
	    }
	    for ($a = $j = $i = 0; $i < $string_length; $i++) {
	        $a = ($a + 1) % 256;
	        $j = ($j + $box[$a]) % 256;
	        $tmp = $box[$a];
	        $box[$a] = $box[$j];
	        $box[$j] = $tmp;
	        $result.= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	    }
	    if ($operation == 'D') {
	        if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key) , 0, 8)) {
	            return substr($result, 8);
	        } else {
	            return '';
	        }
	    } else {
	        return str_replace(array('=','+','/'), array('!','-','_'), base64_encode($result));
	    }
	}

	
	function nice_custom_author_link_request( $query_vars ) {
		
	    if ( array_key_exists( 'author_name', $query_vars ) ) {
	        global $wpdb;
	        $author_id = nice_text_encrypt( $query_vars['author_name'], 'D', AUTH_KEY );
	        if ( $author_id ) {
				$query_vars['author'] = $author_id;				
	            unset( $query_vars['author_name'] );    
	        }
	    }
	    return $query_vars;
	}
	add_filter( 'request', 'nice_custom_author_link_request' );

	function nice_custom_author_link( $link, $author_id) {
	    global $wp_rewrite;
	    $author_id = (int) $author_id;
	    $link = $wp_rewrite->get_author_permastruct();
	 
	    if ( empty($link) ) {
	        $file = home_url( '/' );
	        $link = $file . '?author=' . nice_text_encrypt($author_id, 'E',AUTH_KEY);
	    } else {
	    	
	        $link = str_replace('%author%', nice_text_encrypt($author_id, 'E',AUTH_KEY), $link);
	        $link = home_url() . user_trailingslashit( $link );
	    }
	 
	    return $link;
	}
	add_filter( 'author_link', 'nice_custom_author_link', 10, 2 );

	// wp-rest 可能暴露用户名
	function nice_custom_rest_prepare_user( $response, $user, $request ){

		$response->data['slug'] = nice_text_encrypt( $user->ID, 'E', AUTH_KEY );

		return $response;
	}
	add_filter( 'rest_prepare_user', 'nice_custom_rest_prepare_user', 10, 3 );
}
	

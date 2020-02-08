<?php
/**
 * 字符串截取，支持中文和其他编码
 *
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断字符串后缀
 * @return string
 */
function nc_substr_ext($str, $start = 0, $length = 0, $charset = 'utf-8', $suffix = '')
{
    if (function_exists("mb_substr")) {
        return mb_substr($str, $start, $length, $charset).$suffix;
    } elseif (function_exists('iconv_substr')) {
        return iconv_substr($str, $start, $length, $charset).$suffix;
    }
    $re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("", array_slice($match[0], $start, $length));
    return $slice.$suffix;
}

function nc_reverse_strrchr($haystack, $needle, $trail)
{
    $length = (strrpos($haystack, $needle) + $trail);
    return strrpos($haystack, $needle) ? substr($haystack, 0, $length) : false;
}

/**
 * 获取完整的句子
 */
function nc_print_excerpt($length, $post = null, $echo = true)
{
    global $post;
    $text = $post->post_excerpt;

    if ('' == $text) {
        $text = get_the_content();
        $text = strip_shortcodes($text);
        $text = apply_filters('the_content', $text);
        $text = str_replace(']]>', ']]>', $text);
    }

    $text = strip_shortcodes($text);
    $text = strip_tags($text);

    $text = nc_substr_ext($text, 0, $length);
    $excerpt = nc_reverse_strrchr($text, '。', 3);

    if ($excerpt) {
        $result = strip_tags(apply_filters('the_excerpt', $excerpt)).'...';
    } else {
        $result = strip_tags(apply_filters('the_excerpt', $text)).'...';
    }
    if ($echo == true) {
        echo $result;
    } else {
        return $result;
    }
}

function nc_comment_add_at($comment_text, $comment = '')
{
    if (!empty($comment) && $comment->comment_parent > 0) {
        $comment_text = '<a rel="nofollow" class="comment_at" href="#comment-' . $comment->comment_parent . '">@'.get_comment_author($comment->comment_parent) . '</a> ' . $comment_text;
    }
    return $comment_text;
}

function nc_record_visitors() {
	if (is_singular()) {
		global $post;
		$post_ID = $post->ID;
		if ($post_ID) {
			$post_views = (int)get_post_meta($post_ID, 'views', true);
			if (!update_post_meta($post_ID, 'views', ($post_views+1))) {
				add_post_meta($post_ID, 'views', 1, true);
			}
		}
	}
}

function nc_post_views($before = '(点击 ', $after = ' 次)', $echo = 1)
{
    global $post;
    $post_ID = $post->ID;
    $views = (int)get_post_meta($post_ID, 'views', true);
    if ($echo) {
        echo $before, number_format($views), $after;
    } else {
        return $views;
    }
}

/**
 * Load a component into a template while supplying data.
 *
 * @param string $slug The slug name for the generic template.
 * @param array $params An associated array of data that will be extracted into the templates scope
 * @param bool $output Whether to output component or return as string.
 * @return string
 */
function nc_get_template_part_with_vars($slug, array $params = array(), $output = true)
{
    if (!$output) {
        ob_start();
    }
    $template_file = locate_template("{$slug}.php", false, false);
    extract(array('template_params' => $params), EXTR_SKIP);
    require($template_file);
    if (!$output) {
        return ob_get_clean();
    }
}

function nc_ajax_comment_callback()
{
    global $wpdb;
    $comment_post_ID = isset($_POST['comment_post_ID']) ? (int) $_POST['comment_post_ID'] : 0;
    $post = get_post($comment_post_ID);
    $post_author = $post->post_author;
    if (empty($post->comment_status)) {
        do_action('comment_id_not_found', $comment_post_ID);
        nc_ajax_comment_err('Invalid comment status.');
    }
    $status = get_post_status($post);
    $status_obj = get_post_status_object($status);
    if (!comments_open($comment_post_ID)) {
        do_action('comment_closed', $comment_post_ID);
        nc_ajax_comment_err(__('Sorry, comments are closed.', 'jimu'));
    } elseif ('trash' == $status) {
        do_action('comment_on_trash', $comment_post_ID);
        nc_ajax_comment_err(__('Unknown error.', 'jimu'));
    } elseif (!$status_obj->public && !$status_obj->private) {
        do_action('comment_on_draft', $comment_post_ID);
        nc_ajax_comment_err(__('Unknown error.', 'jimu'));
    } elseif (post_password_required($comment_post_ID)) {
        do_action('comment_on_password_protected', $comment_post_ID);
        nc_ajax_comment_err(__('Password protected.', 'jimu'));
    } else {
        do_action('pre_comment_on_post', $comment_post_ID);
    }

    $comment_author       = (isset($_POST['author']))  ? trim(strip_tags($_POST['author'])) : null;
    $comment_author_email = (isset($_POST['email']))   ? trim($_POST['email']) : null;
    $comment_author_url   = (isset($_POST['url']))     ? trim($_POST['url']) : null;
    $comment_content      = (isset($_POST['comment'])) ? trim($_POST['comment']) : null;
    $user = wp_get_current_user();
    $user_id = $user->ID;
    if ($user->exists()) {
        if (empty($user->display_name)) {
            $user->display_name=$user->user_login;
        }
        $comment_author       = esc_sql($user->display_name);
        $comment_author_email = esc_sql($user->user_email);
        $comment_author_url   = esc_sql($user->user_url);
        $user_id              = esc_sql($user->ID);
    } else {
        if (get_option('comment_registration') || 'private' == $status) {
            nc_ajax_comment_err('<p>'.__('Sorry, you must be logged in to leave a comment', 'jimu').'</p>');
        } // 抱歉，您必须登录后才能发表评论。
    }
    $comment_type = '';
    if (get_option('require_name_email') && !$user->exists()) {
        if (6 > strlen($comment_author_email) || '' == $comment_author) {
            nc_ajax_comment_err('<p>'.__('Please fill in the required options (Name, Email).', 'jimu').'</p>');
        } // 错误：请填写必须的选项（姓名，电子邮件）。
        elseif (!is_email($comment_author_email)) {
            nc_ajax_comment_err('<p>'.__('Please input a valid email address.', 'jimu').'</p>');
        } // 错误：请输入有效的电子邮件地址。
    }
    if ('' == $comment_content) {
        nc_ajax_comment_err('<p>'.__('Say something...', 'jimu').'</p>');
    } // 说点什么吧
    $dupe = "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = '$comment_post_ID' AND ( comment_author = '$comment_author' ";
    if ($comment_author_email) {
        $dupe .= "OR comment_author_email = '$comment_author_email' ";
    }
    $dupe .= ") AND comment_content = '$comment_content' LIMIT 1";
    if ($wpdb->get_var($dupe)) {
        nc_ajax_comment_err('<p>'.__('Please do not repeat your comments. :)', 'jimu').'</p>'); // Do not repeat comments aha~似乎说过这句话了
    }

    if ($lasttime = $wpdb->get_var($wpdb->prepare("SELECT comment_date_gmt FROM $wpdb->comments WHERE comment_author = %s ORDER BY comment_date DESC LIMIT 1", $comment_author))) {
        $time_lastcomment = mysql2date('U', $lasttime, false);
        $time_newcomment  = mysql2date('U', current_time('mysql', 1), false);
        $flood_die = apply_filters('comment_flood_filter', false, $time_lastcomment, $time_newcomment);
        if ($flood_die) {
            nc_ajax_comment_err('<p>'.__('You reply too fast. Take it easy.', 'jimu').'</p>'); // 你回复太快啦。慢慢来。
        }
    }
    $comment_parent = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;
    $commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID');

    $comment_id = wp_new_comment($commentdata);


    $comment = get_comment($comment_id);
    do_action('set_comment_cookies', $comment, $user, true);
    $comment_depth = 1;
    $tmp_c = $comment;
    while ($tmp_c->comment_parent != 0) {
        $comment_depth++;
        $tmp_c = get_comment($tmp_c->comment_parent);
    }
    $GLOBALS['comment'] = $comment;
    get_template_part('comment'); ?>
    <?php
        die();
}

function nc_ajax_comment_err($a)
{
    header('HTTP/1.0 500 Internal Server Error');
    header('Content-Type: text/plain;charset=UTF-8');
    echo $a;
    exit;
}

function nc_ajax_load_comments()
{
    global $wp_query;

    $type  = sanitize_text_field($_POST['type']);
    $paged = sanitize_text_field($_POST['paged']);

    $q = sanitize_text_field($_POST['query']);

    if ($paged < 1 || $paged > $_POST['commentcount']) {
        wp_die();
    }

    if ($type === 'page') {
        $wp_query = new WP_Query(array( 'page_id' => $q, 'cpage' => $paged ));
    }

    if ($type === 'post') {
        $wp_query = new WP_Query(array( 'p' => $q, 'cpage' => $paged ));
    }

    if (have_posts()) {
        while (have_posts()) {
            the_post();
            comments_template();
        }
    }

    wp_reset_postdata();
    wp_die();
}

/**
 * 获取评论下一页页码
 */
function nc_get_next_page_number()
{
    $page_number = get_comment_pages_count();
    if (get_option('default_comments_page') == 'newest') {
        $next_page = $page_number - 1;
    } else {
        $next_page = 2;
    }
    return $next_page;
}

function nc_like_init($key, $direct = false)
{
    // $direct === true 时不计 cookie
    $id = $_POST["id"];
    $action = $_POST["do_action"];
    $lh_raters = get_post_meta($id, $key, true);
    $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;

    if ($action == 'do') {
        $expire = time() + 99999999;
        if (!isset($_COOKIE[$key.'_'.$id]) || $direct) {
            setcookie($key.'_'.$id, $id, $expire, '/', $domain, false);
            if (!$lh_raters || !is_numeric($lh_raters)) {
                update_post_meta($id, $key, 1);
            } else {
                update_post_meta($id, $key, ($lh_raters + 1));
            }
        }
    }
    if ($action == 'undo' && !$direct) {
        $expire = time() - 1;
        if (isset($_COOKIE[$key.'_'.$id])) {
            setcookie($key.'_'.$id, $id, $expire, '/', $domain, false);
            update_post_meta($id, $key, ($lh_raters - 1));
        }
    }
    echo get_post_meta($id, $key, true);
    die;
}

function nc_timeago($ptime = null, $post = null)
{
    if ($post === null) {
        global $post;
    }
    $ptime = $ptime ?: get_post_time('G', false, $post);
    return human_time_diff($ptime, current_time('timestamp')) . '前';
}

function nc_get_translated_role_name($user_id)
{
    $data  = get_userdata($user_id);
    $roles = $data->roles;
    if (in_array('administrator', $roles)) {
        return __('Administrator', 'jimu');
    } elseif (in_array('editor', $roles)) {
        return __('Certified Editor', 'jimu');
    } elseif (in_array('author', $roles)) {
        return __('Special Author', 'jimu');
    } elseif (in_array('subscriber', $roles)) {
        return __('Subscriber', 'jimu');
    }

    return __('Contributor', 'jimu');
}

function nc_get_meta($key, $single = true) {
    global $post;
    return get_post_meta($post->ID, $key, $single);
}

function nc_the_meta($key, $placeholder = '') {
    echo nc_get_meta($key, true) ?: $placeholder;
}

//判定是否是手机
function gdk_is_mobile() {
    $ua = $_SERVER['HTTP_USER_AGENT'];
    if (empty($ua)) {
        return false;
    } elseif ((in_string($ua, 'Mobile') && strpos($ua, 'iPad') === false) // many mobile devices (all iPh, etc.)
     || in_string($ua, 'Android') || in_string($ua, 'NetType/') || in_string($ua, 'Kindle') || in_string($ua, 'MQQBrowser') || in_string($ua, 'Opera Mini') || in_string($ua, 'Opera Mobi') || in_string($ua, 'HUAWEI') || in_string($ua, 'TBS/') || in_string($ua, 'Mi') || in_string($ua, 'iPhone')) {
        return true;
    } else {
        return false;
    }
}

//判断是否是登陆页面
function is_login() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}

//判断字符串内是否有指定字符串
function in_string($text,$find) {
	if(strpos($text,$find) !== false) {
		return true;
	} else {
		return false;
	}
}

//判断是否是微信
function gdk_is_weixin(){
    if(in_string($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')){
        return true;
    }else{
        return false;
    }
}
//获取浏览器信息
function getBrowser() {
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$bname = $u_agent;
	$platform = '';
	$version= '';
	//First get the platform?
	if ( preg_match( '/linux/i', $u_agent ) ) {
		$platform = 'Linux';
	} elseif ( preg_match( '/macintosh|mac os x/i', $u_agent ) ) {
		$platform = 'Mac';
	} elseif ( preg_match( '/windows|win32/i', $u_agent ) ) {
		$platform = 'Windows';
	}
	// Next get the name of the useragent yes seperately and for good reason
	if ( preg_match( '/MSIE/i',$u_agent) && ! preg_match( '/Opera/i',$u_agent ) ) {
		$bname = 'Internet Explorer';
		$ub = "MSIE";
	} elseif( preg_match( '/Firefox/i',$u_agent ) ) {
		$bname = 'Mozilla Firefox';
		$ub = "Firefox";
	} elseif( preg_match( '/Chrome/i',$u_agent ) ) {
		$bname = 'Google Chrome';
		$ub = "Chrome";
	} elseif( preg_match( '/Safari/i',$u_agent ) ) {
		$bname = 'Apple Safari';
		$ub = "Safari";
	} elseif( preg_match( '/Opera/i',$u_agent ) ) {
		$bname = 'Opera';
		$ub = "Opera";
	} elseif( preg_match( '/Netscape/i',$u_agent ) ) {
		$bname = 'Netscape';
		$ub = "Netscape";
	}
	// finally get the correct version number
	$known = array( 'Version', $ub, 'other');
	$pattern = '#( ?<browser>' . join( '|', $known) .
	    ')[/ ]+( ?<version>[0-9.|a-zA-Z.]*)#';
	if ( ! preg_match_all( $pattern, $u_agent, $matches ) ) {
		// we have no matching number just continue
	}
	if ( isset( $matches['browser'] ) && is_array($matches['browser'])  ) {
		// see how many we have
		$i = count( $matches['browser'] );
		if ( $i != 1) {
			//we will have two since we are not using 'other' argument yet
			//see if version is before or after the name
			if ( strripos( $u_agent,"Version") < strripos( $u_agent,$ub ) ) {
				$version = $matches['version'][0];
			} else {
				$version = $matches['version'][1];
			}
		} else {
			$version = $matches['version'][0];
		}
	} else {
		$version="?";
	}
	return array(
	        'userAgent' => $u_agent,
	        'name'      => $bname,
	        'version'   => $version,
	        'platform'  => $platform,
	        'pattern'   => $pattern
	    );
}

//获取IP地址
function gdk_get_ip( ) {
	// check for shared internet/ISP IP
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) && gdk_validate_ip( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		return $_SERVER['HTTP_CLIENT_IP'];
	}
	// check for IPs passing through proxies
	if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		// check if multiple ips exist in var
		if ( in_string( $_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
			$iplist = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			foreach ( $iplist as $ip) {
				if ( gdk_validate_ip( $ip ) )
				    return $ip;
			}
		} else {
			if ( gdk_validate_ip( $_SERVER['HTTP_X_FORWARDED_FOR'] ) )
			        return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
	}
	if ( ! empty( $_SERVER['HTTP_X_FORWARDED']) && gdk_validate_ip( $_SERVER['HTTP_X_FORWARDED'] ) ) {
		return $_SERVER['HTTP_X_FORWARDED'];
	}
	if ( ! empty( $_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && gdk_validate_ip( $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'] ) ) {
		return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
	}
	if ( ! empty( $_SERVER['HTTP_FORWARDED_FOR']) && gdk_validate_ip( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
		return $_SERVER['HTTP_FORWARDED_FOR'];
	}
	if ( ! empty( $_SERVER['HTTP_FORWARDED']) && gdk_validate_ip( $_SERVER['HTTP_FORWARDED'] ) ) {
		return $_SERVER['HTTP_FORWARDED'];
	}
	// return unreliable ip since all else failed
	return $_SERVER['REMOTE_ADDR'];
}

/**
 * Ensures an ip address is both a valid IP and does not fall within
 * a private network range.
 */
function gdk_validate_ip( $ip) {
	if ( strtolower( $ip) === 'unknown') {
		return false;
	}
	// generate ipv4 network address
	$ip = ip2long( $ip);
	// if the ip is set and not equivalent to 255.255.255.255
	if ( $ip !== false && $ip !== -1) {
		// make sure to get unsigned long representation of ip
		// due to discrepancies between 32 and 64 bit OSes and
		// signed numbers ( ints default to signed in PHP)
		$ip = sprintf( '%u', $ip);
		// do private network range checking
		if ( $ip >= 0 && $ip <= 50331647) return false;
		if ( $ip >= 167772160 && $ip <= 184549375) return false;
		if ( $ip >= 2130706432 && $ip <= 2147483647) return false;
		if ( $ip >= 2851995648 && $ip <= 2852061183) return false;
		if ( $ip >= 2886729728 && $ip <= 2887778303) return false;
		if ( $ip >= 3221225984 && $ip <= 3221226239) return false;
		if ( $ip >= 3232235520 && $ip <= 3232301055) return false;
		if ( $ip >= 4294967040) return false;
	}
	return true;
}

//Ajax报错方式
function gdk_die($ErrMsg) {
    if(gdk_option('gdk_ajax') || GDK_IAM_AJAX ){//define( 'GDK_IAM_AJAX', true );  由主题插入functiom 自己声明是Ajax,防止用户搞错
        header('HTTP/1.1 405 Method Not Allowed');
        header('Content-Type: text/plain;charset=UTF-8');
        exit($ErrMsg);
    }else{
        wp_die($ErrMsg);
    }
}

//面包屑导航
function gdk_breadcrumbs($delimiter = '»',$hometitle = 'Home') {
    $before = '<span class="current">';
    // 在当前链接前插入
    $after = '</span>';
    // 在当前链接后插入
    if ( !is_home() && !is_front_page() || is_paged() ) {
        echo '<div itemscope itemtype="http://schema.org/WebPage" id="crumbs">'.__( 'You are here:' , 'cmp' );
        global $post;
        $homeLink = home_url();
        echo ' <a itemprop="breadcrumb" href="' . $homeLink . '">' . $hometitle . '</a> ' . $delimiter . ' ';
        if ( is_category() ) {
            // 分类 存档
            global $wp_query;
            $cat_obj = $wp_query->get_queried_object();
            $thisCat = $cat_obj->term_id;
            $thisCat = get_category($thisCat);
            $parentCat = get_category($thisCat->parent);
            if ($thisCat->parent != 0) {
                $cat_code = get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' ');
                echo $cat_code = str_replace ('<a','<a itemprop="breadcrumb"', $cat_code );
            }
            echo $before . '' . single_cat_title('', false) . '' . $after;
        } elseif ( is_day() ) {
            // 天 存档
            echo '<a itemprop="breadcrumb" href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
            echo '<a itemprop="breadcrumb"  href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
            echo $before . get_the_time('d') . $after;
        } elseif ( is_month() ) {
            // 月 存档
            echo '<a itemprop="breadcrumb" href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
            echo $before . get_the_time('F') . $after;
        } elseif ( is_year() ) {
            // 年 存档
            echo $before . get_the_time('Y') . $after;
        } elseif ( is_single() && !is_attachment() ) {
            // 文章
            if ( get_post_type() != 'post' ) {
                // 自定义文章类型
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                echo '<a itemprop="breadcrumb" href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';
                echo $before . get_the_title() . $after;
            } else {
                // 文章 post
                $cat = get_the_category();
                $cat = $cat[0];
                $cat_code = get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
                echo $cat_code = str_replace ('<a','<a itemprop="breadcrumb"', $cat_code );
                echo $before . get_the_title() . $after;
            }
        } elseif ( !is_single() && !is_page() && get_post_type() != 'post' ) {
            $post_type = get_post_type_object(get_post_type());
            echo $before . $post_type->labels->singular_name . $after;
        } elseif ( is_attachment() ) {
            // 附件
            $parent = get_post($post->post_parent);
            $cat = get_the_category($parent->ID);
            $cat = $cat[0];
            echo '<a itemprop="breadcrumb" href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
            echo $before . get_the_title() . $after;
        } elseif ( is_page() && !$post->post_parent ) {
            // 页面
            echo $before . get_the_title() . $after;
        } elseif ( is_page() && $post->post_parent ) {
            // 父级页面
            $parent_id  = $post->post_parent;
            $breadcrumbs = array();
            while ($parent_id) {
                $page = get_page($parent_id);
                $breadcrumbs[] = '<a itemprop="breadcrumb" href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
                $parent_id  = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
            echo $before . get_the_title() . $after;
        } elseif ( is_search() ) {
            // 搜索结果
            echo $before ;
            printf( __( 'Search Results for: %s', 'cmp' ),  get_search_query() );
            echo  $after;
        } elseif ( is_tag() ) {
            //标签 存档
            echo $before ;
            printf( __( 'Tag Archives: %s', 'cmp' ), single_tag_title( '', false ) );
            echo  $after;
        } elseif ( is_author() ) {
            // 作者存档
            global $author;
            $userdata = get_userdata($author);
            echo $before ;
            printf( __( 'Author Archives: %s', 'cmp' ),  $userdata->display_name );
            echo  $after;
        } elseif ( is_404() ) {
            // 404 页面
            echo $before;
            _e( 'Not Found', 'cmp' );
            echo  $after;
        }
        if ( get_query_var('paged') ) {
            // 分页
            if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() )
                            echo sprintf( __( '( Page %s )', 'cmp' ), get_query_var('paged') );
        }
        echo '</div>';
    }
}

//设置cookie数据
function gdk_set_cookie($key, $value, $expire){
	$expire	= ($expire < time())?$expire+time():$expire;
	$secure = ('https' === parse_url(get_option('home'), PHP_URL_SCHEME));
	setcookie($key, $value, $expire, COOKIEPATH, COOKIE_DOMAIN, $secure);
    if ( COOKIEPATH != SITECOOKIEPATH ){
        setcookie($key, $value, $expire, SITECOOKIEPATH, COOKIE_DOMAIN, $secure);
    }
    $_COOKIE[$key] = $value;
}

//判断是否是电话号码,是号码返回 true  不是返回false
function gdk_is_mobile_number($number){
	return (bool)preg_match('/^0{0,1}(1[3,5,8][0-9]|14[5,7]|166|17[0,1,3,6,7,8]|19[8,9])[0-9]{8}$/', $number);
}


//获取纯文本
function gdk_plain_text($text) {
	$text = wp_strip_all_tags($text);
	$text = str_replace('"', '', $text);
	$text = str_replace('\'', '', $text);
	$text = str_replace("\r\n", ' ', $text);
	$text = str_replace("\n", ' ', $text);
	$text = str_replace("  ", ' ', $text);
	return trim($text);
}

// 获取第一段
function gdk_first_p($text) {
	if($text) {
		$text = explode("\n", trim(strip_tags($text)));
		$text = trim($text['0']);
	}
	return $text;
}

//获取当前页面链接
function gdk_get_current_url(){
    $ssl		= (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? true:false;
    $sp			= strtolower($_SERVER['SERVER_PROTOCOL']);
    $protocol	= substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
    $port		= $_SERVER['SERVER_PORT'];
    $port		= ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
    $host		= $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'];
    return $protocol . '://' . $host . $port . $_SERVER['REQUEST_URI'];
}

//发起HTTP请求
function gdk_http_request($url, $args=array(), $err_args=array()) {
	$args = wp_parse_args( $args, array(
	        'timeout'			=> 3,
            'method'			=> '',
	        'body'				=> array(),
	        'sslverify'			=> false,
	        'blocking'			=> true,	// 如果不需要立刻知道结果，可以设置为 false
	        'stream'			=> false,	// 如果是保存远程的文件，这里需要设置为 true
	        'filename'			=> null,	// 设置保存下来文件的路径和名字
	        'need_json_decode'	=> true,//对结果进行解码,一般都需要
	        'need_json_encode'	=> false,//对发起参数编码
	        // 'headers'		=> array('Accept-Encoding'=>'gzip;'),	//使用压缩传输数据
	        // 'headers'		=> array('Accept-Encoding'=>''),
	        // 'compress'		=> false,
	        'decompress'		=> true,
	    ));
	$need_json_decode	= $args['need_json_decode'];
	$need_json_encode	= $args['need_json_encode'];
	$method				= ($args['method'])?strtoupper($args['method']):($args['body']?'POST':'GET');
	unset($args['need_json_decode']);
	unset($args['need_json_encode']);
	unset($args['method']);
	if($method == 'GET') {
		$response = wp_remote_get($url, $args);
	} elseif($method == 'POST') {
		if($need_json_encode && is_array($args['body'])) {
			$args['body']	= json_encode($args['body']);
		}
		$response = wp_remote_post($url, $args);
	} elseif($method == 'HEAD') {
		if($need_json_encode && is_array($args['body'])) {
			$args['body']	= json_encode($args['body']);
		}
		$response = wp_remote_head($url, $args);
	} else {
		if($need_json_encode && is_array($args['body'])) {
			$args['body']	= json_encode($args['body']);
		}
		$response = wp_remote_request($url, $args);
	}
	if(is_wp_error($response)) {
		trigger_error($url."\n".$response->get_error_code().' : '.$response->get_error_message()."\n".var_export($args['body'],true));
		return $response;
	}
	$headers	= $response['headers'];
	$response	= $response['body'];
	if($need_json_decode || isset($headers['content-type']) && strpos($headers['content-type'], '/json')) {
		if($args['stream']) {
			$response	= file_get_contents($args['filename']);
		}
		$response	= json_decode($response,true);
		if(is_wp_error($response)) {
			return $response;
		}
	}
	extract(wp_parse_args($err_args,  array(
	        'errcode'	=>'errcode',
	        'errmsg'	=>'errmsg',
	        'detail'	=>'detail',
	        'success'	=>0,
	    )));
	if(isset($response[$errcode]) && $response[$errcode] != $success) {
		$errcode	= $response[$errcode];
		$errmsg		= isset($response[$errmsg])?$response[$errmsg]:'';
		if(isset($response[$detail])) {
			$detail	= $response[$detail];
			trigger_error($url."\n".$errcode.' : '.$errmsg."\n".var_export($detail,true)."\n".var_export($args['body'],true));
			return new WP_Error($errcode, $errmsg, $detail);
		} else {
			trigger_error($url."\n".$errcode.' : '.$errmsg."\n".var_export($args['body'],true));
			return new WP_Error($errcode, $errmsg);
		}
	}
	return $response;
}

//根据腾讯视频网址或者ID互相转化
function gdk_get_qq_vid($id_or_url){
    if(filter_var($id_or_url, FILTER_VALIDATE_URL)){
        if(preg_match('#https://v.qq.com/x/page/(.*?).html#i',$id_or_url, $matches)){
            return $matches[1];
        }elseif(preg_match('#https://v.qq.com/x/cover/.*/(.*?).html#i',$id_or_url, $matches)){
            return $matches[1];
        }else{
            return '';
        }
    }else{
        return $id_or_url;
    }
}

//根据秒拍id或者网站获取视频直连
function get_video_mp4($id_or_url){
    if(filter_var($id_or_url, FILTER_VALIDATE_URL)){
        if(preg_match('#http://www.miaopai.com/show/(.*?).htm#i',$id_or_url, $matches)){
            return 'http://gslb.miaopai.com/stream/'.esc_attr($matches[1]).'.mp4';
        }elseif(preg_match('#https://v.qq.com/x/page/(.*?).html#i',$id_or_url, $matches)){
            return get_qqv_mp4($matches[1]);
        }elseif(preg_match('#https://v.qq.com/x/cover/.*/(.*?).html#i',$id_or_url, $matches)){
            return get_qqv_mp4($matches[1]);
        }else{
            return str_replace(['%3A','%2F'], [':','/'], urlencode($id_or_url));
        }
    }else{
        return get_qqv_mp4($id_or_url);
    }
}

//获取腾讯视频
function get_qqv_mp4($vid) {
	if(strlen($vid) > 20) {
		return new WP_Error('invalid_qqv_vid', '非法的腾讯视频 ID');
	}
	$mp4 = wp_cache_get($vid, 'qqv_mp4');
	if($mp4 === false) {
		$response	= gdk_http_request('http://vv.video.qq.com/getinfo?otype=json&platform=11001&vid='.$vid, array(
		            'timeout'			=>4,
		            'need_json_decode'	=>false
		        ));
		if(is_wp_error($response)) {
			return $response;
		}
		$response	= trim(substr($response, strpos($response, '{')),';');
		$response	= json_decode($response);
		if(is_wp_error($response)) {
			return $response;
		}
		if(empty($response['vl'])) {
			return new WP_Error('illegal_qqv', '该腾讯视频不存在或者为收费视频！');
		}
		$u		= $response['vl']['vi'][0];
		$p0		= $u['ul']['ui'][0]['url'];
		$p1		= $u['fn'];
		$p2		= $u['fvkey'];
		$mp4	= $p0.$p1.'?vkey='.$p2;
		wp_cache_set($vid, $mp4, 'qqv_mp4', HOUR_IN_SECONDS*6);
	}
	return $mp4;
}

//字符串转数组,默认分隔符是:,
function gdk_str2arr($data, $delimiter = ',') {
	// 数组原样返回
	if (is_array($data)) {
		return $data;
	}
	// 字符串处理
	$string = (string)$data;
	if (empty($string)) {
		$result = [];
	} elseif (preg_match('/^{.*?}$/', $string) || preg_match('/^\[.*?]$/', $string)) {
		$result = json_decode($string, true);
	} elseif (preg_match('/^a:.*?(})$/', $string)) {
		$result = unserialize($string, null);
	} elseif (strpos($string, $delimiter) >= 1) {
		$result = explode($delimiter, $string);
	} else {
		$result = [];
	}
	if (!is_array($result) || count($result) < 1) {
		return [];
	}
	return $result;
}

//根据连接特征获取网盘连接来源
function gdk_panlinks($links) {
    if(!filter_var($url, FILTER_VALIDATE_URL)) return false;
    if(in_string($links,'baidu')){
        $linknane = '百度网盘';
    }elseif(in_string($links,'yunpan')){
        $linknane = '360云盘';
    }elseif(in_string($links,'lanzous')){
        $linknane = '蓝奏网盘';
    }elseif(in_string($links,'189')){
        $linknane = '天翼云盘';
    }elseif(in_string($links,'mega')){
        $linknane = 'MEGA云盘';
    }elseif(in_string($links,'yadi.sk')){
        $linknane = '毛子云盘';
    }elseif(in_string($links,'cdn')){
        $linknane = 'CDN';
    }elseif(in_string($links,'ctfile')){
        $linknane = '360云盘';
    }elseif(in_string($links,'weiyun')){
        $linknane = '腾讯微云';
    }else{
        $linknane = '下载';
    }
    return $linknane;
}

//一个简单可重复使用的邮件模板
function mail_temp($mail_title,$mail_cotent,$link,$link_title){
	?>
	<div style="width:500px;margin:auto">
    <div style="background:#2695f3;color:#FFF;padding:20px 10px;"><?php echo $mail_title;?></div>
    <div style="padding:10px;margin:5px;border-bottom:dashed 1px #ddd;"><?php echo $mail_cotent;?></div>
    <a href="<?php echo $link;?>" style="display:block;margin:auto;margin-top:40px;padding:10px;width:107px;outline:0;border:1px solid #2695f3;border-radius:25px;color:#2695f3;text-align:center;font-weight:700;font-size:14pxtext-decoration:none;" rel="noopener" target="_blank"><?php echo $link_title;?></a>
    <br><br>
    <div style="color:#cecece;font-size: 12px;">本邮件为系统自动发送，请勿回复。<br>
    如果不想被此类邮件打扰,请前往 <a style="color: #cecece;" href="<?php echo home_url();?>" rel="noopener" target="_blank"><?php echo get_option('blogname');?></a> 留言说明,由我们来操作处理。
    </div>
</div>
	<?php
}

//获取所有站点分类id,带缓存
function gdk_category(){
    $cat_ids = get_transient('gdk_category');
    if (false === $cat_ids) {
        $categories = get_terms('category', 'hide_empty=0');
        $k = [];
        foreach ($categories as $categorie) {
            $k[] = $categorie->term_id;
        }
        $cat_ids = implode(",", $k);
        set_transient('gdk_category', $cat_ids, 60*60*24*5);//缓存5天
    }
    $cat_ids = explode(",", $cat_ids);
    foreach ($cat_ids as $catid) {
        $cat_name = get_cat_name($catid);
        $output = '<span>' . $cat_name . "=(<b>" . $catid . '</b>)</span>&nbsp;&nbsp;';
        echo $output;
    }
}


/*使用字符串转数组分类标签获取信息
*$term 分类还是标签, tag是标签,cat是分类
*$meta 需要获取的具体项目,参数des=描述,参数keyword=关键词,参数img=图片
*$id 分类还是标签的id,为空显示当前分类或者标签数据
*数据来源于分类/标签的图片描述
*/
function gdk_term_meta($term,$meta,$id) {
	if($term == 'cat') {
		$term_meta = gdk_str2arr(category_description($id),'@@');
	} elseif($term == 'tag') {
		$term_meta = gdk_str2arr(tag_description($id),'@@');
	} else {
		return false;
	}
	switch ($meta) {
		case 'des':
		    $result = $term_meta[0];
		break;
		case 'keyword':
		    $result = $term_meta[1];
		break;
		case 'img':
		    $result = $term_meta[2];
		break;
		default:
            return false;
	}
	return $result;
}


//输出缩略图地址
function gdk_thumbnail_src() {
    global $post;
    $gdk_thumbnail_src = '';
    if ($values = get_post_custom_values('gdk_thumb')) { //输出自定义域图片地址
        $values = get_post_custom_values('gdk_thumb');
        $gdk_thumbnail_src = $values[0];
    } elseif (has_post_thumbnail()) { //如果有特色缩略图，则输出缩略图地址
        $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID) , 'full');
        $gdk_thumbnail_src = $thumbnail_src[0];
    } else {
        ob_start();
        ob_end_clean();
        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
        $gdk_thumbnail_src = $matches[1][0]; //获取该图片 src
        if (empty($gdk_thumbnail_src)) { //如果日志中没有图片，则显示随机图片
            $random = mt_rand(1, 12);
            echo GDK_BASE_URL.'assets/img/thumb/' . $random . '.jpg';
        }
    };
    echo $gdk_thumbnail_src;
}


//生成订单号编码
function gdk_order_id(){
	date_default_timezone_set('Asia/Shanghai');
	$order_id = 'E' . date("YmdHis") . mt_rand(10000, 99999);
	return $order_id;
}


//获取云落的远程通知，加入缓存，1天一次
function gdk_get_Yunluo_Notice(){
	$Yunluo_Notice = get_transient('Yunluo_Notice');
	if(false === $Yunluo_Notice){
        $Yunluo_Notice = wp_remote_get('https://u.gitcafe.net/api/notice.txt')['body'];
		if ( is_array( $Yunluo_Notice ) && !is_wp_error($Yunluo_Notice) && $Yunluo_Notice['response']['code'] == '200' ) {
			set_transient('Yunluo_Notice', $Yunluo_Notice, 60*60*12);//缓存12小时
		}else{
			set_transient('Yunluo_Notice', '有点小尴尬哈啊，服务器菌暂时有点累了呢，先休息一会儿~，', 60*60*2);//缓存2小时
		}
    }
    return $Yunluo_Notice;
}

//获取页面id，并且不可重用
function gdk_page_id( $pagephp ) {
    global $wpdb;
    $pagephp = esc_sql($pagephp);
    $pageid = $wpdb->get_row("SELECT `post_id` FROM `{$wpdb->postmeta}` WHERE `meta_value` = 'pages/{$pagephp}.php'", ARRAY_A)['post_id'];
    return $pageid;
}

//根据订单描述金币数据，d=订单号 u=用户id
function gdk_check( $d , $u = null) {
	global $wpdb;
	$des = " WHERE `description` = '" . $d . "'";
	$userid = "";
	if ( isset( $u ) && ( $u !== null ) ) {
		$userid = " AND `user_id` = '" . $u . "'";
	}
	$result = $wpdb->query("SELECT `point_id` FROM " . GDK_Points_Database::points_get_table("users") . $des . $userid . " AND `status` = 'accepted' LIMIT 3", ARRAY_A);
	return $result;//0=无订单结果，1=有订单结果，>1均为异常重复入库数据
}

//导航单页函数
function gdk_get_the_link_items($id = null) {
    $bookmarks = get_bookmarks('orderby=date&category=' . $id);
    $output = '';
    if (!empty($bookmarks)) {
        $output.= '<div class="link_items fontSmooth">';
        foreach ($bookmarks as $bookmark) {
            $output.= '<div class="link_item"><a class="link_item_inner apollo_' . $bookmark->link_rating . '" rel="nofollow" href="' . $bookmark->link_url . '" title="' . $bookmark->link_description . '" target="_blank" ><span class="sitename sitecolor_' . mt_rand(1, 14) . '">' . $bookmark->link_name . '</span></a></div>';
        }
        $output.= '</div>';
    }
    return $output;
}

function gdk_get_link_items() {
    $linkcats = get_terms('link_category', 'orderby=count&hide_empty=1&exclude=' . gdk_option('gdk_linkpage_cat'));
    if (!empty($linkcats)) {
        foreach ($linkcats as $linkcat) {
            $result.= '<h2 class="link_title">' . $linkcat->name . '</h2>';
            if ($linkcat->description) $result.= '<div class="link_description">' . $linkcat->description . '</div>';
            $result.= gdk_get_the_link_items($linkcat->term_id);
        }
    } else {
        $result = gdk_get_the_link_items();
    }
    return $result;
}

/*
 * Payjs支付操作函数
 * 订单标题
 * 订单备注
 * $_POST['money'] = 提交的金额,$_POST['way'] = 支付方式,支付宝为alipay,不设置默认微信,
 */
function payjs_action($body,$attach){
	$config = [
	    'mchid' => gdk_option('gdk_payjs_id'),   // 配置商户号
	    'key'   => gdk_option('gdk_payjs_key'),   // 配置通信密钥
	];
	// 初始化
	$payjs = new GDK_Payjs($config);
	$data = [
	    'body' => $body,   // 订单标题
	    'attach' => $attach,   // 订单备注
	    'out_trade_no' => gdk_order_id(),// 订单号
	    'total_fee' => intval($_POST['money'])*100,// 金额,单位:分
	    'notify_url' => GDK_BASE_URL.'/public/notify.php',//异步通知文件
	    'hide' => '1'
	];
	$result['money'] = intval($_POST['money']);//RMB金额
	$result['trade_no'] = $data['out_trade_no'];
	if( $_POST['way'] == 'alipay' ) {
		$data['type'] = 'alipay';
		$result['way'] = '支付宝';
	} else {
		$result['way'] = '微信';
	}
	if(gdk_is_mobile()) {
		$rst = $payjs->cashier($data);//手机使用收银台
		$result['img'] = $rst;
	} else {
		$rst = $payjs->native($data);//电脑使用扫码
		$result['img'] = $rst['code_url'];
    }
    if(in_string($attach,'PP')){//如果是付费可见,增加一个参数
        $result['mode'] = '1';//1=付费可见
    }else{
        $result['mode'] = '0';
    }

    exit(implode('|',$result));//以字符串形式返回并停止运行
}


//接受payjs支付结果推送
function payjs_notify() {
	// 配置通信参数
	$config = [
	    'mchid' => gdk_option('gdk_payjs_id'),   // 配置商户号
	    'key'   => gdk_option('gdk_payjs_key'),   // 配置通信密钥
	];
	$payjs = new GDK_Payjs($config);
	$data = $payjs->notify();//需要做签名性检查
	// 对返回码判断
	if($data['return_code'] == 1) {
		echo 'success';
		return $data;
	} else {
		exit($data['return_msg']);
	}
}


//充值按钮
function buy_points(){
    if(is_user_logged_in()) {//logined
        $result = '
        <a data-fancybox="pay_fancybox" data-src="#pay_fancybox" href="javascript:;" class="button">点击充值</a>
        <form id="pay_fancybox" name="pay_form" style="display: none; width: 100%; max-width: 500px;" class="pure-form">
                <h2 class="mb-3">积分充值</h2>
                <p>请在下面输入充值金额以及支付工具,微信支付宝都可以,如果下面选项中有支付宝一般建议支付宝</p>
                <p class="alert info">本站支付比例为: 1 RMB = '.gdk_option('gdk_rate').'金币</p></blockquote>
                <label for="money">支付金额</label>
                <input name="money" id="money" min="1" value="2" type="number" required>
                <br /><label for="pay_way">支付方式</label>';
                if( gdk_option('gdk_payjs_alipay')){
                    $result .= '
                    <label><input name="pay_way" type="radio" value = "alipay" checked/> 支付宝</label>  &nbsp;&nbsp;&nbsp;&nbsp;<label><input name="pay_way" type="radio" value = "wechat" /> 微信</label>';
                }else{
                    $result .= '<br /><label><input name="pay_way" type="radio" value = "wechat" checked/> 微信</label>';
                }
                $result .= '
                <p class="mb-0 text-right">
                    <input data-fancybox-close type="button" id="submit_pay" data-action="pay_points" data-id="'.get_current_user_id().'" class="pure-button pure-button-primary" value="提交">
                </p>
            </form>';
    }else{// no login
        $result = '<div class=\'alert info\'>本页面需要您登录才可以操作，请先 <a target="_blank" href="'.esc_url( wp_login_url( get_permalink() ) ).'">点击登录</a>  或者<a href="'.esc_url( wp_registration_url() ).'">立即注册</a></div>';
    }
    return $result;
}


function login_modal(){
    $result = '<a data-fancybox="login_fancybox" data-src="#login_fancybox" href="javascript:;">登录</a>
    <div id="login_fancybox" style="width: 100%; max-width: 500px;overflow:auto;display:none;">';
    $result .= wp_login_form(array(
         'echo' => false,
         'value_remember' => true,
		'value_username' => '请输入用户名...'
        ));
    $result .= '</div>';

    return $result;

}

/**开始微信* */
//生成随机字符
//sk是12位随机字符, key是域名@sk
function gdk_weauth_token(){
    $strs = 'QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm';
    $sk = substr(str_shuffle($strs),mt_rand(0,strlen($strs)-11),12);//12位
    set_transient($sk.'-OK', 1, 30);//缓存  get_transient($sk.'OK') == 1
    $key = $_SERVER['HTTP_HOST'].'@'.$sk;
    return $key;
  }

  function gdk_weauth_qr(){
    $qr64 = [];
    $qr64['key'] = gdk_weauth_token();
    $qr64['qrcode'] = gdk_http_request('https://wa.isdot.net/qrcode?str='.$qr64['key'])['qrcode'];
    return $qr64;
  }

  function weauth_rewrite_rules($wp_rewrite){
      if (get_option('permalink_structure')) {
          $new_rules['^weauth'] = 'index.php?user=$matches[1]&sk=$matches[2]';
          $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
      }
  }
  add_action('generate_rewrite_rules', 'weauth_rewrite_rules');

/**
 * 微信登陆按钮
 */
function weixin_login_btn(){
    $result = '<a id="weixin_login_btn" href="javascript:;" data-action="gdk_weauth_qr_gen" class="button weixin_login_btn">微信登陆</a><span id="weauth_key" class="hide"></span>';
	if(is_user_logged_in()){
    $user_id = get_current_user_id();
    $email = get_user_by('id', $user_id)->user_email;
    if ($user_id > 0) {
        if (!empty($email)) {
            $result .= '<script>window.localStorage.setItem(\'ls-bind\',1);</script>';
        }
    }
	$result .= '<p>您已登陆</p><p>您的ID是:'.$user_id.'</p><p>您的邮箱是:'.$email.'</p>';
    }
    return $result;
}

function weauth_oauth_redirect(){
    $url = home_url();
    wp_redirect( $url );
    exit;
}

/***
function weauth_oauth(){
    $weauth_user = $_GET['user'];
    $weauth_sk = esc_attr($_GET['sk']);
    $weauth_res = get_transient($weauth_sk.'-OK');
    if (empty($weauth_res) && $weauth_res !== 1) return;
    $weauth_user = stripslashes($weauth_user);
    $weauth_user = json_decode($weauth_user, true);

    $nickname = $weauth_user['nickName'];
    $wxavatar = $weauth_user['avatarUrl'];
    $openid = $weauth_user['openid'];
    $login_name = 'wx_' . wp_create_nonce($openid);

    if (is_user_logged_in()) {
        $this_user = wp_get_current_user();
        $user_id = $this_user->ID;
        update_user_meta($user_id, 'wx_openid', $openid);
        update_user_meta($user_id, 'simple_local_avatar', $wxavatar);
        weauth_oauth_redirect();
    } else {
        $weauth_user = get_users(array(
          'meta_key ' => 'wx_openid',
          'meta_value' => $openid
              )
         );
        if (is_wp_error($weauth_user) || !count($weauth_user)) {
            $random_password = wp_generate_password(12, false);
            $userdata = array(
              'user_login' => $login_name,
              'display_name' => $nickname,
              'user_pass' => $random_password,
              'nickname' => $nickname
            );
            $user_id = wp_insert_user($userdata);
            update_user_meta($user_id, 'wx_openid', $openid);
            update_user_meta($user_id, 'simple_local_avatar', $wxavatar);
        } else {
            $user_id = $weauth_user[0]->ID;
        }
    }
    set_transient($weauth_sk . '-login', $user_id, 20);//用于登录的随机数，有效期为20秒
  
}

//初始化
function weauth_oauth_init(){
    if (isset($_GET['user']) && isset($_GET['sk'])){
        weauth_oauth();
    }
}
add_action('parse_request','weauth_oauth_init');

*/

function create_user_id($userdata){
    $nickname = $userdata[1];
    $wxavatar = $userdata[6];
    $openid   = $userdata[7];
    $password = wp_generate_password(12, false);
    $login_name = 'wx_' . wp_create_nonce($openid);

    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        update_user_meta($user_id, 'wx_openid', $openid);
        update_user_meta($user_id, 'wx_avatar', $wxavatar);
    } else {
        $weauth_user = get_users(array(
          'meta_key ' => 'wx_openid',
          'meta_value' => $openid
              )
         );
        if (is_wp_error($weauth_user) || !count($weauth_user)) {
            
            $user_info = array(
              'user_login' => $login_name,
              'display_name' => $nickname,
              'user_pass' => $password,
              'nickname' => $nickname
            );
            $user_id = wp_insert_user($user_info);
            update_user_meta($user_id, 'wx_openid', $openid);
            update_user_meta($user_id, 'wx_avatar', $wxavatar);
        } else {
            $user_id = $weauth_user[0]->ID;
        }
    }
    return $user_id;
}

function get_weauth_oauth(){
    $weauth_user = trim($_GET['user']);//weauth发来用户信息
    $weauth_sk = trim($_GET['sk']);//weauth返回的12位sk信息
    $weauth_res = get_transient($weauth_sk.'-OK');
    if (empty($weauth_res) && $weauth_res !== 1) return;
    $weauth_user = stripslashes($weauth_user);
    $weauth_user = json_decode($weauth_user, true);

    $oauth_result = implode('|',$weauth_user);
    error_log('COM---info--'.$oauth_result);
    set_transient($weauth_sk.'-info', $oauth_result, 60*2);
    echo 'success';
    exit;

}
add_action('parse_request','get_weauth_oauth');
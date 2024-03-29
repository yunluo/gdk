<?php

function gdk_log($log, $dump = null)
{
    if (isset($dump)) {
        $log = var_dump($log);
    }
    echo "<script>console.info('log:".$log."');</script>";
}

function gdk_rand_color()
{
    $arr = [
        'red',
        'green',
        'blue',
        'yellow',
    ];
    $rndKey = array_rand($arr);
    echo $arr[$rndKey];
}

//获取友情链接ID，默认是第一个创建的分类
function gdk_link_id()
{
    $arr = get_terms('link_category', 'orderby=id&hide_empty=0');

    return $arr[0]->term_id;
}
/**
 * 获取摘要
 *
 * @param mixed      $length
 * @param null|mixed $post
 * @param mixed      $echo
 * @param mixed      $more
 */
function gdk_print_excerpt($length, $post = null, $echo = true, $more = '...')
{
    global $post;
    $text = $post->post_excerpt;

    if ('' == $text) {
        $text = get_the_content(); //获取文字
        $text = apply_filters('the_content', $text);
        $text = strtr($text, [']]>' => ']]>']);
    }

    $text = strip_shortcodes($text);
    $text = strip_tags($text);

    $excerpt = wp_trim_words($text, $length, $more);

    if (isset($excerpt)) {
        $result = apply_filters('the_excerpt', $excerpt);
    }
    if (true == $echo) {
        echo $result;
    } else {
        return $result;
    }
}

function nc_comment_add_at($comment_text, $comment = '')
{
    if (!empty($comment) && $comment->comment_parent > 0) {
        $comment_text = '<a rel="nofollow" class="comment_at" href="#comment-'.$comment->comment_parent.'">@'.get_comment_author($comment->comment_parent).'</a> '.$comment_text;
    }

    return $comment_text;
}

function gdk_record_visitors()
{
    if (is_singular()) {
        global $post;
        $post_ID = $post->ID;
        if ($post_ID) {
            $post_views = (int) get_post_meta($post_ID, 'views', true);
            if (!update_post_meta($post_ID, 'views', ($post_views + 1))) {
                add_post_meta($post_ID, 'views', 1, true);
            }
        }
    }
}

function nc_post_views($before = '(点击 ', $after = ' 次)', $echo = 1)
{
    global $post;
    $post_ID = $post->ID;
    $views = (int) get_post_meta($post_ID, 'views', true);
    if ($echo) {
        echo $before, number_format($views), $after;
    } else {
        return $views;
    }
}

/**
 * Load a component into a template while supplying data.
 *
 * @param string $slug   the slug name for the generic template
 * @param array  $params An associated array of data that will be extracted into the templates scope
 * @param bool   $output whether to output component or return as string
 *
 * @return string
 */
function nc_get_template_part_with_vars($slug, array $params = [], $output = true)
{
    if (!$output) {
        ob_start();
    }
    $template_file = locate_template("{$slug}.php", false, false);
    extract(['template_params' => $params], EXTR_SKIP);

    require $template_file;
    if (!$output) {
        return ob_get_clean();
    }
}

function nc_ajax_load_comments()
{
    global $wp_query;

    $type = sanitize_text_field($_POST['type']);
    $paged = sanitize_text_field($_POST['paged']);

    $q = sanitize_text_field($_POST['query']);

    if ($paged < 1 || $paged > $_POST['commentcount']) {
        wp_die();
    }

    if ('page' === $type) {
        $wp_query = new WP_Query(['page_id' => $q, 'cpage' => $paged]);
    }

    if ('post' === $type) {
        $wp_query = new WP_Query(['p' => $q, 'cpage' => $paged]);
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
    if ('newest' == get_option('default_comments_page')) {
        $next_page = $page_number - 1;
    } else {
        $next_page = 2;
    }

    return $next_page;
}

function nc_like_init($key, $direct = false)
{
    // $direct === true 时不计 cookie
    $id = $_POST['id'];
    $action = $_POST['do_action'];
    $lh_raters = get_post_meta($id, $key, true);
    $domain = ('localhost' != $_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : false;

    if ('do' == $action) {
        $expire = time() + 99999999;
        if (!isset($_COOKIE[$key.'_'.$id]) || $direct) {
            gdk_set_cookie($key.'_'.$id, $id, $expire);
            if (!$lh_raters || !is_numeric($lh_raters)) {
                update_post_meta($id, $key, 1);
            } else {
                update_post_meta($id, $key, ($lh_raters + 1));
            }
        }
    }
    if ('undo' == $action && !$direct) {
        $expire = time() - 1;
        if (isset($_COOKIE[$key.'_'.$id])) {
            gdk_set_cookie($key.'_'.$id, $id, $expire);
            update_post_meta($id, $key, ($lh_raters - 1));
        }
    }
    echo get_post_meta($id, $key, true);

    exit;
}

function nc_timeago($ptime = null, $post = null)
{
    if (null === $post) {
        global $post;
    }
    $ptime = $ptime ?: get_post_time('G', false, $post);

    return human_time_diff($ptime, current_time('timestamp')).'前';
}

function nc_get_translated_role_name($user_id)
{
    $data = get_userdata($user_id);
    $roles = $data->roles;
    if (array_key_exists('administrator', $roles)) {
        return __('Administrator', 'jimu');
    }
    if (array_key_exists('editor', $roles)) {
        return __('Certified Editor', 'jimu');
    }
    if (array_key_exists('author', $roles)) {
        return __('Special Author', 'jimu');
    }
    if (array_key_exists('subscriber', $roles)) {
        return __('Subscriber', 'jimu');
    }

    return __('Contributor', 'jimu');
}

function nc_get_meta($key, $single = true)
{
    global $post;

    return get_post_meta($post->ID, $key, $single);
}

function nc_the_meta($key, $placeholder = '')
{
    echo nc_get_meta($key, true) ?: $placeholder;
}

//判定是否是手机
function gdk_is_mobile()
{
    $ua = $_SERVER['HTTP_USER_AGENT'];
    if (empty($ua)) {
        return false;
    }
    if ((in_string($ua, 'Mobile') && false === strpos($ua, 'iPad')) // many mobile devices (all iPh, etc.)
         || in_string($ua, 'Android') || in_string($ua, 'NetType/') || in_string($ua, 'Kindle') || in_string($ua, 'MQQBrowser') || in_string($ua, 'Opera Mini') || in_string($ua, 'Opera Mobi') || in_string($ua, 'HUAWEI') || in_string($ua, 'TBS/') || in_string($ua, 'Mi') || in_string($ua, 'iPhone')) {
        return true;
    }

    return false;
}

//判断是否是登陆页面
function is_login()
{
    return array_key_exists($GLOBALS['pagenow'], ['wp-login.php', 'wp-register.php']);
}

//判断字符串内是否有指定字符串
function in_string($text, $find)
{
    if (false !== strpos($text, $find)) {
        return true;
    }

    return false;
}

//判断是否是微信
function gdk_is_weixin()
{
    if (in_string($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
        return true;
    }

    return false;
}
//获取浏览器信息
function gdk_getBrowser()
{
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = $u_agent;
    $platform = '';
    $version = '';
    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'Linux';
    } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'Mac';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'Windows';
    }
    // Next get the name of the useragent yes seperately and for good reason
    if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
        $bname = 'Internet Explorer';
        $ub = 'MSIE';
    } elseif (preg_match('/Firefox/i', $u_agent)) {
        $bname = 'Mozilla Firefox';
        $ub = 'Firefox';
    } elseif (preg_match('/Chrome/i', $u_agent)) {
        $bname = 'Google Chrome';
        $ub = 'Chrome';
    } elseif (preg_match('/Safari/i', $u_agent)) {
        $bname = 'Apple Safari';
        $ub = 'Safari';
    } elseif (preg_match('/Opera/i', $u_agent)) {
        $bname = 'Opera';
        $ub = 'Opera';
    } elseif (preg_match('/Netscape/i', $u_agent)) {
        $bname = 'Netscape';
        $ub = 'Netscape';
    }
    // finally get the correct version number
    $known = ['Version', $ub, 'other'];
    $pattern = '#( ?<browser>'.join('|', $known).
        ')[/ ]+( ?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
    if (isset($matches['browser']) && is_array($matches['browser'])) {
        // see how many we have
        $i = count($matches['browser']);
        if (1 != $i) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, 'Version') < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }
    } else {
        $version = '?';
    }

    return [
        'userAgent' => $u_agent,
        'name' => $bname,
        'version' => $version,
        'platform' => $platform,
        'pattern' => $pattern,
    ];
}

//获取IP地址
function gdk_get_ip()
{
    $proxy_headers = ['CLIENT_IP', 'FORWARDED', 'FORWARDED_FOR', 'FORWARDED_FOR_IP', 'HTTP_CLIENT_IP', 'HTTP_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED_FOR_IP', 'HTTP_PC_REMOTE_ADDR', 'HTTP_PROXY_CONNECTION', 'HTTP_VIA', 'HTTP_X_FORWARDED', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED_FOR_IP', 'HTTP_X_IMFORWARDS', 'HTTP_XROXY_CONNECTION', 'VIA', 'X_FORWARDED', 'X_FORWARDED_FOR'];
    foreach ($proxy_headers as $proxy_header) {
        if (isset($_SERVER[$proxy_header])) {
            if (gdk_validate_ip($_SERVER[$proxy_header])) {
                return $_SERVER[$proxy_header];
            }
            if (false !== stristr(',', $_SERVER[$proxy_header])) {
                $proxy_header_temp = trim(array_shift(explode(',', $_SERVER[$proxy_header])));
                if (($pos_temp = in_string($proxy_header_temp, ':'))) {
                    $proxy_header_temp = substr($proxy_header_temp, 0, $pos_temp);
                }
                if (gdk_validate_ip($proxy_header_temp)) {
                    return $proxy_header_temp;
                }
            }
        }
    }
    if (gdk_validate_ip($_SERVER['REMOTE_ADDR'])) {
        return $_SERVER['REMOTE_ADDR'];
    }
}

/**
 * Ensures an ip address is both a valid IP and does not fall within
 * a private network range.
 *
 * @param mixed $ip
 */
function gdk_validate_ip($ip)
{
    if ('unknown' === strtolower($ip)) {
        return false;
    }
    // generate ipv4 network address
    $ip = ip2long($ip);
    // if the ip is set and not equivalent to 255.255.255.255
    if (false !== $ip && -1 !== $ip) {
        // make sure to get unsigned long representation of ip
        // due to discrepancies between 32 and 64 bit OSes and
        // signed numbers ( ints default to signed in PHP)
        $ip = sprintf('%u', $ip);
        // do private network range checking
        if ($ip >= 0 && $ip <= 50331647) {
            return false;
        }

        if ($ip >= 167772160 && $ip <= 184549375) {
            return false;
        }

        if ($ip >= 2130706432 && $ip <= 2147483647) {
            return false;
        }

        if ($ip >= 2851995648 && $ip <= 2852061183) {
            return false;
        }

        if ($ip >= 2886729728 && $ip <= 2887778303) {
            return false;
        }

        if ($ip >= 3221225984 && $ip <= 3221226239) {
            return false;
        }

        if ($ip >= 3232235520 && $ip <= 3232301055) {
            return false;
        }

        if ($ip >= 4294967040) {
            return false;
        }
    }

    return true;
}

//Ajax报错方式
function gdk_die($ErrMsg)
{
    header('HTTP/1.1 405 Method Not Allowed');
    header('Content-Type: text/plain;charset=UTF-8');

    exit($ErrMsg);
}

//面包屑导航
function gdk_breadcrumbs($delimiter = '»', $hometitle = 'Home')
{
    $before = '<span class="current active">';
    // 在当前链接前插入
    $after = '</span>';
    // 在当前链接后插入
    if (!is_home() && !is_front_page() || is_paged()) {
        echo '<div itemscope itemtype="http://schema.org/WebPage" id="crumbs" class="cm-breadcrumb">';
        global $post;
        $homeLink = home_url();
        echo ' <a itemprop="breadcrumb" href="'.$homeLink.'">'.$hometitle.'</a> '.$delimiter.' ';
        if (is_category()) {
            // 分类 存档
            global $wp_query;
            $cat_obj = $wp_query->get_queried_object();
            $thisCat = $cat_obj->term_id;
            $thisCat = get_category($thisCat);
            $parentCat = get_category($thisCat->parent);
            if (0 != $thisCat->parent) {
                $cat_code = get_category_parents($parentCat, true, ' '.$delimiter.' ');
                echo $cat_code = str_replace('<a', '<a itemprop="breadcrumb"', $cat_code);
            }
            echo $before.''.single_cat_title('', false).''.$after;
        } elseif (is_day()) {
            // 天 存档
            echo '<a itemprop="breadcrumb" href="'.get_year_link(get_the_time('Y')).'">'.get_the_time('Y').'</a> '.$delimiter.' ';
            echo '<a itemprop="breadcrumb"  href="'.get_month_link(get_the_time('Y'), get_the_time('m')).'">'.get_the_time('F').'</a> '.$delimiter.' ';
            echo $before.get_the_time('d').$after;
        } elseif (is_month()) {
            // 月 存档
            echo '<a itemprop="breadcrumb" href="'.get_year_link(get_the_time('Y')).'">'.get_the_time('Y').'</a> '.$delimiter.' ';
            echo $before.get_the_time('F').$after;
        } elseif (is_year()) {
            // 年 存档
            echo $before.get_the_time('Y').$after;
        } elseif (is_single() && !is_attachment()) {
            // 文章
            if ('post' != get_post_type()) {
                // 自定义文章类型
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                echo '<a itemprop="breadcrumb" href="'.$homeLink.'/'.$slug['slug'].'/">'.$post_type->labels->singular_name.'</a> '.$delimiter.' ';
                echo $before.get_the_title().$after;
            } else {
                // 文章 post
                $cat = get_the_category();
                $cat = $cat[0];
                $cat_code = get_category_parents($cat, true, ' '.$delimiter.' ');
                echo $cat_code = str_replace('<a', '<a itemprop="breadcrumb"', $cat_code);
                echo $before.get_the_title().$after;
            }
        } elseif (!is_single() && !is_page() && 'post' != get_post_type()) {
            $post_type = get_post_type_object(get_post_type());
            echo $before.$post_type->labels->singular_name.$after;
        } elseif (is_attachment()) {
            // 附件
            $parent = get_post($post->post_parent);
            $cat = get_the_category($parent->ID);
            $cat = $cat[0];
            echo '<a itemprop="breadcrumb" href="'.get_permalink($parent).'">'.$parent->post_title.'</a> '.$delimiter.' ';
            echo $before.get_the_title().$after;
        } elseif (is_page() && !$post->post_parent) {
            // 页面
            echo $before.get_the_title().$after;
        } elseif (is_page() && $post->post_parent) {
            // 父级页面
            $parent_id = $post->post_parent;
            $breadcrumbs = [];
            while ($parent_id) {
                $page = get_page($parent_id);
                $breadcrumbs[] = '<a itemprop="breadcrumb" href="'.get_permalink($page->ID).'">'.get_the_title($page->ID).'</a>';
                $parent_id = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            foreach ($breadcrumbs as $crumb) {
                echo $crumb.' '.$delimiter.' ';
            }

            echo $before.get_the_title().$after;
        } elseif (is_search()) {
            // 搜索结果
            echo $before;
            printf(__('Search Results for: %s', 'cmp'), get_search_query());
            echo $after;
        } elseif (is_tag()) {
            //标签 存档
            echo $before;
            printf(__('Tag Archives: %s', 'cmp'), single_tag_title('', false));
            echo $after;
        } elseif (is_author()) {
            // 作者存档
            global $author;
            $userdata = get_userdata($author);
            echo $before;
            printf(__('Author Archives: %s', 'cmp'), $userdata->display_name);
            echo $after;
        } elseif (is_404()) {
            // 404 页面
            echo $before;
            _e('Not Found', 'cmp');

            echo $after;
        }
        if (get_query_var('paged')) {
            // 分页
            if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author()) {
                echo sprintf(__('( Page %s )', 'cmp'), get_query_var('paged'));
            }
        }
        echo '</div>';
    }
}

//设置cookie数据
function gdk_set_cookie($key, $value, $expire)
{
    $expire = ($expire < time()) ? $expire + time() : $expire;
    $secure = ('https' === parse_url(get_option('home'), PHP_URL_SCHEME));
    setcookie($key, $value, $expire, COOKIEPATH, COOKIE_DOMAIN, $secure);
    if (COOKIEPATH != SITECOOKIEPATH) {
        setcookie($key, $value, $expire, SITECOOKIEPATH, COOKIE_DOMAIN, $secure);
    }
    $_COOKIE[$key] = $value;
}

//判断是否是电话号码,号码返回 true  不是返回false
function gdk_is_mobile_number($number)
{
    return (bool) preg_match('/^0{0,1}(1[3,5,8][0-9]|14[5,7]|166|17[0,1,3,6,7,8]|19[8,9])[0-9]{8}$/', $number);
}

//获取纯文本
function gdk_plain_text($text)
{
    $text = wp_strip_all_tags($text);
    $replace = ['"' => '', '\'' => '', "\r\n" => ' ', "\n" => ' ', '  ' => ' '];
    $text = strtr($text, $replace);

    return trim($text);
}

// 获取第一段
function gdk_first_p($text)
{
    if ($text) {
        $text = explode("\n", trim(strip_tags($text)));
        $text = trim($text['0']);
    }

    return $text;
}

//获取当前页面链接
function gdk_get_current_url()
{
    $ssl = (!empty($_SERVER['HTTPS']) && 'on' == $_SERVER['HTTPS']) ? true : false;
    $sp = strtolower($_SERVER['SERVER_PROTOCOL']);
    $protocol = substr($sp, 0, strpos($sp, '/')).(($ssl) ? 's' : '');
    $port = $_SERVER['SERVER_PORT'];
    $port = ((!$ssl && '80' == $port) || ($ssl && '443' == $port)) ? '' : ':'.$port;
    $host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'];

    return $protocol.'://'.$host.$port.$_SERVER['REQUEST_URI'];
}

//发起HTTP请求
function gdk_http_request($url, $args = [], $err_args = [])
{
    $args = wp_parse_args($args, [
        'timeout' => 3,
        'method' => '',
        'body' => [],
        'sslverify' => false,
        'blocking' => true, // 如果不需要立刻知道结果，可以设置为 false
        'stream' => false, // 如果是保存远程的文件，这里需要设置为 true
        'filename' => null, // 设置保存下来文件的路径和名字
        'need_json_decode' => true, //对结果进行解码,一般都需要
        'need_json_encode' => false, //对发起参数编码
        // 'headers'        => array('Accept-Encoding'=>'gzip;'),    //使用压缩传输数据
        // 'headers'        => array('Accept-Encoding'=>''),
        // 'compress'        => false,
        'decompress' => true,
    ]);
    $need_json_decode = $args['need_json_decode'];
    $need_json_encode = $args['need_json_encode'];
    $method = ($args['method']) ? strtoupper($args['method']) : ($args['body'] ? 'POST' : 'GET');
    unset($args['need_json_decode'], $args['need_json_encode'], $args['method']);

    if ('GET' == $method) {
        $response = wp_remote_get($url, $args);
    } elseif ('POST' == $method) {
        if ($need_json_encode && is_array($args['body'])) {
            $args['body'] = json_encode($args['body']);
        }
        $response = wp_remote_post($url, $args);
    } elseif ('HEAD' == $method) {
        if ($need_json_encode && is_array($args['body'])) {
            $args['body'] = json_encode($args['body']);
        }
        $response = wp_remote_head($url, $args);
    } else {
        if ($need_json_encode && is_array($args['body'])) {
            $args['body'] = json_encode($args['body']);
        }
        $response = wp_remote_request($url, $args);
    }
    if (is_wp_error($response)) {
        trigger_error($url."\n".$response->get_error_code().' : '.$response->get_error_message()."\n".var_export($args['body'], true));

        return $response;
    }
    $headers = $response['headers'];
    $response = $response['body'];
    if ($need_json_decode || isset($headers['content-type']) && strpos($headers['content-type'], '/json')) {
        if ($args['stream']) {
            $response = file_get_contents($args['filename']);
        }
        $response = json_decode($response, true);
        if (is_wp_error($response)) {
            return $response;
        }
    }
    extract(wp_parse_args($err_args, [
        'errcode' => 'errcode',
        'errmsg' => 'errmsg',
        'detail' => 'detail',
        'success' => 0,
    ]));
    if (isset($response[$errcode]) && $response[$errcode] != $success) {
        $errcode = $response[$errcode];
        $errmsg = isset($response[$errmsg]) ? $response[$errmsg] : '';
        if (isset($response[$detail])) {
            $detail = $response[$detail];
            trigger_error($url."\n".$errcode.' : '.$errmsg."\n".var_export($detail, true)."\n".var_export($args['body'], true));

            return new WP_Error($errcode, $errmsg, $detail);
        }
        trigger_error($url."\n".$errcode.' : '.$errmsg."\n".var_export($args['body'], true));

        return new WP_Error($errcode, $errmsg);
    }

    return $response;
}

//根据腾讯视频网址或者ID互相转化
function gdk_get_qq_vid($id_or_url)
{
    if (filter_var($id_or_url, FILTER_VALIDATE_URL)) {
        if (preg_match('#https://v.qq.com/x/page/(.*?).html#i', $id_or_url, $matches)) {
            return $matches[1];
        }
        if (preg_match('#https://v.qq.com/x/cover/.*/(.*?).html#i', $id_or_url, $matches)) {
            return $matches[1];
        }

        return '';
    }

    return $id_or_url;
}

//根据秒拍id或者网站获取视频直连
function get_video_mp4($id_or_url)
{
    if (filter_var($id_or_url, FILTER_VALIDATE_URL)) {
        if (preg_match('#http://www.miaopai.com/show/(.*?).htm#i', $id_or_url, $matches)) {
            return 'http://gslb.miaopai.com/stream/'.esc_attr($matches[1]).'.mp4';
        }
        if (preg_match('#https://v.qq.com/x/page/(.*?).html#i', $id_or_url, $matches)) {
            return get_qqv_mp4($matches[1]);
        }
        if (preg_match('#https://v.qq.com/x/cover/.*/(.*?).html#i', $id_or_url, $matches)) {
            return get_qqv_mp4($matches[1]);
        }

        return str_replace(['%3A', '%2F'], [':', '/'], urlencode($id_or_url));
    }

    return get_qqv_mp4($id_or_url);
}

//获取腾讯视频
function get_qqv_mp4($vid)
{
    if (strlen($vid) > 20) {
        return new WP_Error('invalid_qqv_vid', '非法的腾讯视频 ID');
    }
    $mp4 = wp_cache_get($vid, 'qqv_mp4');
    if (false === $mp4) {
        $response = gdk_http_request('http://vv.video.qq.com/getinfo?otype=json&platform=11001&vid='.$vid, [
            'timeout' => 4,
            'need_json_decode' => false,
        ]);
        if (is_wp_error($response)) {
            return $response;
        }
        $response = trim(substr($response, strpos($response, '{')), ';');
        $response = json_decode($response);
        if (is_wp_error($response)) {
            return $response;
        }
        if (empty($response['vl'])) {
            return new WP_Error('illegal_qqv', '该腾讯视频不存在或者为收费视频！');
        }
        $u = $response['vl']['vi'][0];
        $p0 = $u['ul']['ui'][0]['url'];
        $p1 = $u['fn'];
        $p2 = $u['fvkey'];
        $mp4 = $p0.$p1.'?vkey='.$p2;
        wp_cache_set($vid, $mp4, 'qqv_mp4', HOUR_IN_SECONDS * 6);
    }

    return $mp4;
}

//字符串转数组,默认分隔符是:,
function gdk_str2arr($data, $delimiter = ',')
{
    // 数组原样返回
    if (is_array($data)) {
        return $data;
    }
    // 字符串处理
    $string = (string) $data;
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
function gdk_panlinks($links)
{
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }

    if (in_string($links, 'baidu')) {
        $linknane = '百度网盘';
    } elseif (in_string($links, 'yunpan')) {
        $linknane = '360云盘';
    } elseif (in_string($links, 'lanzous')) {
        $linknane = '蓝奏网盘';
    } elseif (in_string($links, '189')) {
        $linknane = '天翼云盘';
    } elseif (in_string($links, 'mega')) {
        $linknane = 'MEGA云盘';
    } elseif (in_string($links, 'yadi.sk')) {
        $linknane = '毛子云盘';
    } elseif (in_string($links, 'cdn')) {
        $linknane = 'CDN';
    } elseif (in_string($links, 'ctfile')) {
        $linknane = '360云盘';
    } elseif (in_string($links, 'weiyun')) {
        $linknane = '腾讯微云';
    } else {
        $linknane = '下载';
    }

    return $linknane;
}

//一个简单可重复使用的邮件模板
function gdk_mail_temp($mail_title, $mail_cotent, $link, $link_title)
{
    return '<div style="width:500px;margin:auto">
    <h1 style="background:#2695f3;color:#fff;padding:20px 10px;">'.$mail_title.'</h1>
    <div style="padding:15px;border-bottom:dashed 1px #ddd;">'.$mail_cotent.'</div>
    <a href="'.$link.'" style="display:block;margin:auto;margin-top:40px;padding:10px;width:150px;outline:0;border:1px solid #2695f3;border-radius:25px;color:#2695f3;text-align:center;font-weight:700;font-size:14px;text-decoration:none;" rel="noopener" target="_blank">'.$link_title.'</a>
    <br><br>
    <div style="color:#cecece;font-size: 12px;">本邮件为系统自动发送，请勿回复。<br>
    如果不想被此类邮件打扰,请前往 <a style="color: #cecece;" href="'.home_url().'" rel="noopener" target="_blank">'.get_option('blogname').'</a> 留言说明,由我们来操作处理。
    </div></div>';
}

//获取所有站点分类id,带缓存
function gdk_category()
{
    $cat_ids = get_transient('gdk_category');
    if (false === $cat_ids) {
        $categories = get_terms('category', 'hide_empty=0');
        $k = [];
        foreach ($categories as $categorie) {
            $k[] = $categorie->term_id;
        }
        $cat_ids = implode(',', $k);
        set_transient('gdk_category', $cat_ids, 60 * 60 * 24 * 5); //缓存5天
    }
    $cat_ids = explode(',', $cat_ids);
    foreach ($cat_ids as $catid) {
        $cat_name = get_cat_name($catid);
        $output = '<span>'.$cat_name.'=(<b>'.$catid.'</b>)</span>&nbsp;&nbsp;';
        echo $output;
    }
}

/*使用字符串转数组分类标签获取信息
 *$term 分类还是标签, tag是标签,cat是分类
 *$meta 需要获取的具体项目,参数des=描述,参数keyword=关键词,参数img=图片
 *$id 分类还是标签的id,为空显示当前分类或者标签数据
 *数据来源于分类/标签的图片描述
 */
function gdk_term_meta($term, $meta, $id)
{
    if ('cat' == $term) {
        $term_meta = gdk_str2arr(category_description($id), '@@');
    } elseif ('tag' == $term) {
        $term_meta = gdk_str2arr(tag_description($id), '@@');
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

//CDN 缩略图处理样式
function gdk_thumb_color()
{
    switch (gdk_option('gdk_cdn_serves')) {
        case '1':
        case '3':
            return '?imageAve';

            break;

        case '2':
            return '!/exformat/hex';

            break;

        case '4':
            return '?x-oss-process=image/average-hue';

            break;

        case '5':
            return '?x-image-process=image/average-hue';

            break;

        default:
            return false;
    }
}

//CDN 缩略图处理样式
function gdk_thumb_style($width, $height)
{
    switch (gdk_option('gdk_cdn_serves')) {
        case '1':
            return '?imageView2/1/w/'.$width.'/h/'.$height;

            break;

        case '2':
            return '!/both/'.$width.'x'.$height.'/force/true';

            break;

        case '3':
            return '?imageMogr2/thumbnail/'.$width.'x'.$height.'!';

            break;

        case '4':
            return '?x-oss-process=image/resize,m_fill,h_'.$height.',w_'.$width.',limit_0';

            break;

        case '5':
            return '?x-image-process=image/resize,m_fill,h_'.$height.',w_'.$width.',limit_0';

            break;

        default:
            return false;
    }
}

//输出缩略图地址
function gdk_thumbnail_src($id = null)
{
    if (isset($id)) {
        $post_ID = $id;
        $post_content = get_post($id)->post_content;
    } else {
        global $post;
        $post_ID = $post->ID;
        $post_content = $post->post_content;
    }

    $gdk_thumb = get_post_meta($post_ID, 'gdk_thumb');
    if (isset($gdk_thumb)) {
        //输出自定义域图片地址

        $gdk_thumbnail_url = $gdk_thumb[0];
    } elseif (has_post_thumbnail()) {
        //如果有特色缩略图，则输出缩略图地址
        $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post_ID), 'full');
        $gdk_thumbnail_url = $thumbnail_src[0];
    } else {
        ob_start();
        ob_end_clean();
        preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post_content, $output);
        $gdk_thumbnail_url = $output[1][0]; //获取该图片 src
        if (empty($gdk_thumbnail_url)) {
            //如果日志中没有图片，则显示随机图片
            $gdk_thumbnail_url = GDK_BASE_URL.'assets/img/thumb/'.mt_rand(1, 12).'.jpg';
        }
    }

    return $gdk_thumbnail_url;
}

/**
 * 获取略缩图，输出img标签代码
 *
 * @param  [init] $way     缩略图方案代码，1=cdn，2=timthumb，3=aq_resize
 * @param  [init] $width   缩略图宽度
 * @param  [init] $height  缩略图高度
 * @param  [string] $atrr  img标签的属性
 *
 * @return [string]        img标签的图片代码
 */
function gdk_thumb_img($way, $width, $height, $atrr = 'class="thumb_img"')
{
    $url = gdk_thumbnail_src();
    if (1 === $way) {//cdn
        $src = $url.gdk_thumb_style($width, $height);
    } elseif (2 === $way) {
        $src = GDK_BASE_URL.'public/timthumb.php?src='.$url.'&h='.$height.'&w='.$width.'&q=90&zc=1&ct=1';
    } elseif (3 === $way) {
        $src = aq_resize($url, $width, $height, true);
        if (empty($src)) {
            $src = GDK_BASE_URL.'public/timthumb.php?src='.$url.'&h='.$height.'&w='.$width.'&q=90&zc=1&ct=1';
        }
    } else {
        return false;
    }
    echo '<img '.$atrr.' src="'.$src.'">';
}

//生成订单号编码
function gdk_order_id()
{
    date_default_timezone_set('Asia/Shanghai');

    return 'E'.date('YmdHis').mt_rand(10000, 99999);
}

//生成随机数
function randomString($length = 11)
{
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}

//获取云落的远程通知，加入缓存，12小时一次
function gdk_Remote_Notice($url = 'https: //u.gitcafe.net/api/notice.txt', $hours = 12)
{
    $Yunluo_Notice = get_transient('Yunluo_Notice');
    if (false === $Yunluo_Notice) {
        $response = wp_remote_get($url);
        if (is_array($response) && !is_wp_error($response)) {
            set_transient('Yunluo_Notice', $response['body'], 60 * 60 * $hours); //缓存12小时
        } else {
            set_transient('Yunluo_Notice', '有点小尴尬哈啊，服务器菌暂时有点累了呢，先休息一会儿~，', 60 * 60 * $hours); //缓存12小时
        }
    }

    return $Yunluo_Notice;
}

//获取页面id，并且不可重用
function gdk_page_id($pagephp)
{
    global $wpdb;
    $pagephp = esc_sql($pagephp);
    $pageid = $wpdb->get_row("SELECT `post_id` FROM `{$wpdb->postmeta}` WHERE `meta_value` = 'pages/{$pagephp}.php'", ARRAY_A)['post_id'];

    return $pageid;
}

//根据订单描述金币数据，d=订单号 u=用户id
function gdk_order_check($d, $u = null)
{
    global $wpdb;
    $des = " WHERE `description` = '".$d."'";
    $userid = '';
    if (isset($u) && (null !== $u)) {
        $userid = " AND `user_id` = '".$u."'";
    }
    $result = $wpdb->query('SELECT `point_id` FROM '.GDK_Points_Database::points_get_table('users').$des.$userid." AND `status` = 'accepted' LIMIT 3", ARRAY_A);

    return $result; //0=无订单结果，1=有订单结果，>1均为异常重复入库数据
}

function gdk_get_Version()
{
    echo '<input type="button" class="button button-secondary get_new_version" value="点击检测更新">';
}
/*
 * Payjs支付操作函数
 * 订单标题
 * 订单备注
 * $_POST['money'] = 提交的金额,$_POST['way'] = 支付方式,支付宝为alipay,不设置默认微信,
 */
function payjs_action($body, $attach, $money)
{
    $config = [
        'mchid' => gdk_option('gdk_payjs_id'), // 配置商户号
        'key' => gdk_option('gdk_payjs_key'), // 配置通信密钥
    ];
    // 初始化
    $payjs = new GDK_Payjs($config);
    $data = [
        'body' => $body, // 订单标题
        'attach' => $attach, // 订单备注
        'out_trade_no' => gdk_order_id(), // 订单号
        'total_fee' => intval($money) * 100, // 金额,单位:分
        'notify_url' => GDK_BASE_URL.'public/notify.php', //异步通知文件
        'hide' => '1',
    ];
    $result['money'] = intval($money); //RMB金额
    $result['trade_no'] = $data['out_trade_no'];
    if ('alipay' == $_POST['way']) {
        $data['type'] = 'alipay';
        $result['way'] = '支付宝';
    } else {
        $result['way'] = '微信';
    }
    if (gdk_is_mobile()) {
        $rst = $payjs->cashier($data); //手机使用收银台
        $result['img'] = $rst;
    } else {
        $rst = $payjs->native($data); //电脑使用扫码
        $result['img'] = $rst['code_url'];
    }
    if (in_string($attach, 'PP')) { //如果是付费可见,增加一个参数
        $result['mode'] = '1'; //1=付费可见
    } else {
        $result['mode'] = '0';
    }

    exit(implode('|', $result)); //以字符串形式返回并停止运行
}

//接受payjs支付结果推送
function payjs_notify()
{
    // 配置通信参数
    $config = [
        'mchid' => gdk_option('gdk_payjs_id'), // 配置商户号
        'key' => gdk_option('gdk_payjs_key'), // 配置通信密钥
    ];
    $payjs = new GDK_Payjs($config);
    $data = $payjs->notify(); //需要做签名性检查
    // 对返回码判断
    if (1 == $data['return_code']) {
        echo 'success';

        return $data;
    }

    exit($data['return_msg']);
}

//充值按钮
function buy_points()
{
    if (is_user_logged_in()) {
        //logined
        $result = '
        <a data-fancybox="pay_fancybox" data-src="#pay_fancybox" href="javascript:;" class="cm-btn primary">点击充值</a>
        <form id="pay_fancybox" name="pay_form" style="display: none; width: 100%; max-width: 500px;" class="pure-form">
                <h2 class="mb-3">积分充值</h2>
                <p>请在下面输入充值金额以及支付工具,微信支付宝都可以,如果下面选项中有支付宝一般建议支付宝</p>
                <p class="cm-alert success">本站支付比例为: 1 RMB = '.gdk_option('gdk_rate').'金币</p></blockquote>
                <label for="money">支付金额</label>
                <input name="money" id="money" min="1" value="2" type="number" required>
                <br /><label for="pay_way">支付方式</label>';
        if (gdk_option('gdk_payjs_alipay')) {
            $result .= '
                    <label><input name="pay_way" type="radio" value = "alipay" checked/> 支付宝</label>  &nbsp;&nbsp;&nbsp;&nbsp;<label><input name="pay_way" type="radio" value = "wechat" /> 微信</label>';
        } else {
            $result .= '<br /><label><input name="pay_way" type="radio" value = "wechat" checked/> 微信</label>';
        }
        $result .= '
                <p class="mb-0 cm-text-right">
                    <input data-fancybox-close type="button" id="submit_pay" data-action="pay_points" data-id="'.get_current_user_id().'" class="cm-btn primary" value="提交">
                </p>
            </form>';
    } else {
        // no login
        $result = '<div class=\'cm-alert error\'>本页面需要您登录才可以操作，请先 '.weixin_login_btn().'  或者<a href="'.esc_url(wp_registration_url()).'">立即注册</a></div>';
    }

    return $result;
}

//获取bing图
function gdk_get_bing_img()
{
    $bing_imgurl = get_transient('Bing_img');
    if (false === $bing_imgurl) {
        $arr = json_decode(file_get_contents('https://cn.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1'), true);
        $bing_imgurl = 'https://cn.bing.com'.$arr['images'][0]['url'];
        set_transient('Bing_img', $bing_imgurl, 60 * 60 * 12);
    }

    return $bing_imgurl;
}

function login_modal()
{
    $result = '<a data-fancybox="login_fancybox" data-src="#login_fancybox" href="javascript:;">登录</a>
    <div id="login_fancybox" style="width: 100%; max-width: 500px;overflow:auto;display:none;">';
    $result .= wp_login_form([
        'echo' => false,
        'value_remember' => true,
        'value_username' => '请输入用户名...',
    ]);
    $result .= '</div>';

    return $result;
}

/*开始微信*
 * 生成随机字符
 * sk是12位随机字符, key格式是域名@sk
*/
function gdk_weauth_token()
{
    $strs = 'QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm';
    $sk = substr(str_shuffle($strs), mt_rand(0, strlen($strs) - 11), 12); //12位
    set_transient($sk.'-OK', 1, 60); //1分钟缓存  get_transient($sk.'OK') == 1
    $key = $_SERVER['HTTP_HOST'].'@'.$sk;

    return $key;
}

function gdk_weauth_qr()
{
    $qr64 = [];
    $qr64['key'] = gdk_weauth_token();
    $qr64['qrcode'] = gdk_http_request('https://wa.isdot.net/qrcode?str='.$qr64['key'])['qrcode'];
    //$qr64['qrcode'] = gdk_http_request('https://api.goauth.jysafe.cn/qrcode?str='.$qr64['key'])['qrcode'];//预备使用，备胎
    return $qr64;
}

/**
 * 微信登陆按钮.
 */
function weixin_login_btn()
{
    $result = '<a id="weixin_login_btn" href="javascript:;" data-action="gdk_weauth_qr_gen" class="cm-btn primary weixin_login_btn">微信登陆</a>';
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $user = get_user_by('id', $user_id);
        if ($user_id > 0) {
            if (!empty($user->user_email)) {
                $result .= '<script>window.localStorage.setItem(\'ls-bind\',1);</script>';
            }
        }
        //$result .= '<p>您已登陆</p><p>您的ID是:'.$user_id.'</p><p>您的昵称是:'.$user->nickname.'</p>';
    }

    return $result;
}

// 获取用户ID
function create_user_id($userdata)
{
    $nickname = $userdata[0];
    $wxavatar = $userdata[6];
    $openid = $userdata[7];
    $password = wp_generate_password(12, false);
    $login_name = 'wx_'.wp_create_nonce($openid);

    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        update_user_meta($user_id, 'wx_openid', $openid);
        update_user_meta($user_id, 'wx_avatar', $wxavatar);
    } else {
        $weauth_user = get_users(
            [
                'meta_key ' => 'wx_openid',
                'meta_value' => $openid,
            ]
        );
        if (is_wp_error($weauth_user) || !count($weauth_user)) {
            $user_info = [
                'user_login' => $login_name,
                'display_name' => $nickname,
                'user_pass' => $password,
                'nickname' => $nickname,
            ];
            $user_id = wp_insert_user($user_info);
            update_user_meta($user_id, 'wx_openid', $openid);
            update_user_meta($user_id, 'wx_avatar', $wxavatar);
        } else {
            $user_id = $weauth_user[0]->ID;
        }
    }

    return $user_id;
}

//生成hover颜色
function editorial_hover_color($hex, $steps)
{
    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    $steps = max(-255, min(255, $steps));
    // Normalize into a six character long hex string
    $hex = strtr($hex, '#', '');
    if (3 == strlen($hex)) {
        $hex = str_repeat(substr($hex, 0, 1), 2).str_repeat(substr($hex, 1, 1), 2).str_repeat(substr($hex, 2, 1), 2);
    }
    // Split into three parts: R, G and B
    $color_parts = str_split($hex, 2);
    $return = '#';
    foreach ($color_parts as $color) {
        $color = hexdec($color);
        // Convert to decimal
        $color = max(0, min(255, $color + $steps));
        // Adjust color
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT);
        // Make two char hex code
    }

    return $return;
}

/**
 * Get minified css and removed space.
 *
 * @param mixed $css
 */
function pwd_minify_css($css)
{
    // Normalize whitespace
    $css = preg_replace('/\s+/', ' ', $css);
    // Remove ; before }
    $css = preg_replace('/;(?=\s*})/', '', $css);
    // Remove space after , : ; { } */ >
    $css = preg_replace('/(,|:|;|\{|}|\*\/|>) /', '$1', $css);
    // Remove space before , ; { }
    $css = preg_replace('/ (,|;|\{|})/', '$1', $css);
    // Strips leading 0 on decimal values (converts 0.5px into .5px)
    $css = preg_replace('/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $css);
    // Strips units if value is 0 (converts 0px to 0)
    $css = preg_replace('/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $css);
    // Return minified CSS
    return trim($css);
}

//生成微信验证码
function wx_captcha()
{
    date_default_timezone_set('Asia/Shanghai');
    $min = floor(date('i') / 2);
    $day = date('d');
    $day = ltrim($day, 0);
    $home = home_url();
    $wx_token = gdk_option('gdk_wxmp_token');
    $captcha = sha1($min.$home.$wx_token);

    return substr($captcha, $day, 6);
}

function gdk_post_dropdown()
{
    // Option list of all post
    $gdk_options_posts = [];
    $gdk_options_posts_obj = get_posts('posts_per_page=-1');
    foreach ($gdk_options_posts_obj as $gdk_posts) {
        $gdk_options_posts[$gdk_posts->ID] = $gdk_posts->post_title;
    }

    return $gdk_options_posts;
}

function gdk_categories_dropdown()
{
    // Option list of all categories
    $args = [
        'type' => 'post',
        'orderby' => 'name',
        'order' => 'ASC',
        'hide_empty' => 1,
        'hierarchical' => 1,
        'taxonomy' => 'category',
    ];
    $gdk_option_categories = [];
    $gdk_category_lists = get_categories($args);
    foreach ($gdk_category_lists as $gdk_category) {
        $gdk_option_categories[$gdk_category->term_id] = $gdk_category->name;
    }

    return $gdk_option_categories;
}

function gdk_tag_dropdown()
{
    // Option list of all tags
    $args = [
        'type' => 'post',
        'orderby' => 'name',
        'order' => 'ASC',
        'hide_empty' => 1,
        'hierarchical' => 1,
        'taxonomy' => 'tag',
    ];
    $gdk_option_tags = [];
    $gdk_tag_lists = get_tags($args);
    $gdk_option_tags[''] = '选择标签';
    foreach ($gdk_tag_lists as $gdk_tag) {
        $gdk_option_tags[$gdk_tag->term_id] = $gdk_tag->name;
    }

    return $gdk_option_tag;
}

//转化图片为base64格式
function gdk_base64img($image_file)
{
    $image_info = getimagesize($image_file);
    $image_data = file_get_contents($image_file);
    return 'data:'.$image_info['mime'].';base64,'.base64_encode($image_data);
}

//生成二维码
function gdk_getQrcode($url)
{
    //引入phpqrcode类库
    require_once GDK_ROOT_PATH.'/class/qrcode.class.php';
    $errorCorrectionLevel = 'L'; //容错级别
    $matrixPointSize = 6; //生成图片大小
    ob_start();
    QRcode::png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
    $data = ob_get_contents();
    ob_end_clean();

    $imageString = base64_encode($data);
    header('content-type:application/json; charset=utf-8');

    return 'data:image/jpeg;base64,'.$imageString;
}

function gdk_unzip_url($url, $where)
{
    $zippath = $where.'/'.(basename($url));
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 300);
    $downfile = fopen($zippath, 'wb');
    curl_setopt($curl, CURLOPT_FILE, $downfile);
    curl_exec($curl);
    curl_close($curl);

    require_once ABSPATH.'wp-admin/includes/file.php';
    \WP_Filesystem();
    \unzip_file($zippath, $where);
    usleep(300000);
    unlink($zippath);
}

//新窗口打开
function gdk_blank_open()
{
    if (!gdk_option('gdk_target_blank')) {
        echo 'target="_blank"';
    }
}

function gdk_guest_form()
{
    ?>
<p><textarea id="msg_content" placeholder="请输入留言内容" name="" rows="4" cols="" required></textarea></p>
<p><input type="email" id="msg_mail" placeholder="请输入邮箱" style="margin-right:30px;" required /><input id="msg_submit"
        data-action="msg_submit" type="button" value="提交"></p>
<?php
}

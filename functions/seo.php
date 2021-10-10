<?php

//加载网站地图xml
if (gdk_option('gdk_sitemap_xml')) {
    include GDK_ROOT_PATH.'public/sitemap-xml.php';

    include GDK_ROOT_PATH.'public/sitemap.php';
}

// 屏蔽蜘蛛爬取作者页面
if (gdk_option('gdk_no_author_page')) {
    function gdk_no_author_page()
    {
        if (is_author()) {
            wp_no_robots();
        }
    }
    add_action('wp_head', 'gdk_no_author_page');
}

//robots.txt优化功能
if (gdk_option('gdk_robots')) {
    add_filter('robots_txt', 'gdk_robots_txt', 10, 2);
    function gdk_robots_txt($robotext)
    {
        if (gdk_option('gdk_sitemap_xml')) {
            $sitemap = 'Sitemap: '.home_url('/sitemap.xml');
        } else {
            $sitemap = '';
        }

        return "User-agent: *
Disallow: /wp-admin/
Disallow: /wp-content/plugins/
Disallow: /wp-includes/
Disallow: /*/trackback
Disallow: /feed
Disallow: /*/feed
Disallow: /attachment/
Disallow: /wp-content/themes/
{$sitemap}";
    }
}

//文章自动内链
if (gdk_option('gdk_tag_link')) {
    function gdk_tag_link($content)
    {
        $post_tags = get_the_tags();
        if ($post_tags) {
            foreach ($post_tags as $tag) {
                $link = get_tag_link($tag->term_id);
                $keyword = $tag->name;
                $cleankeyword = stripslashes($keyword);
                $url = '<a target="_blank" href="'.$link.'" title="'.str_replace('%s', addcslashes($cleankeyword, '$'), '查看更多关于%s的文章').'">'.addcslashes($cleankeyword, '$').'</a>';
                $regEx = '\'(?!((<.*?)|(<a.*?)))('.$cleankeyword.')(?!(([^<>]*?)>)|([^>]*?</a>))\'s';
                $content = preg_replace($regEx, $url, $content, gdk_option('gdk_tag_num') ?? 5);
            }
        }

        return $content;
    }
    add_filter('the_content', 'gdk_tag_link', 1);
}

// 自动添加nofloow
if (gdk_option('gdk_nofollow')) {
    add_filter('the_content', 'gdk_nofollow');
    add_filter('the_excerpt', 'gdk_nofollow');
    function gdk_nofollow($content)
    {
        return preg_replace_callback('/<a[^>]+/', 'gdk_nofollow_callback', $content);
    }
    function gdk_nofollow_callback($matches)
    {
        $link = $matches[0];
        $site_link = get_bloginfo('url');
        if (false === strpos($link, 'rel')) {
            $link = preg_replace("%(href=\\S(?!{$site_link}))%i", 'rel="nofollow" $1', $link);
        } elseif (preg_match("%href=\\S(?!{$site_link})%i", $link)) {
            $link = preg_replace('/rel=\S(?!nofollow)\S*/i', 'rel="nofollow"', $link);
        }

        return $link;
    }
}

//百度主动推送
if (gdk_option('gdk_baidu_push')) {
    function gdk_baidu_submit($post_ID)
    {
        global $post;
        $bd_submit_site = get_bloginfo('url');
        $bd_submit_token = gdk_option('gdk_baidu_token');
        if (empty($post_ID) || empty($bd_submit_site) || empty($bd_submit_token)) {
            return;
        }

        if (1 == get_post_meta($post_ID, 'gdk_baidu_submit', true)) {
            return;
        }

        $url = get_permalink($post_ID);
        $api = $api = 'http://data.zz.baidu.com/urls?site='.$bd_submit_site.'&token='.$bd_submit_token;
        $status = $post->post_status;
        if ('' != $status && 'publish' != $status) {
            $request = new WP_Http();
            $result = $request->request($api, [
                'method' => 'POST',
                'body' => $url,
                'headers' => 'Content-Type: text/plain',
            ]);
            if (is_array($result) && !is_wp_error($result) && '200' == $result['response']['code']) {
                error_log('baidu_submit_result：'.$result['body']);
                $result = json_decode($result['body'], true);
            }
            if (array_key_exists('success', $result)) {
                add_post_meta($post_ID, 'gdk_baidu_submit', 1, true);
            }
        }
    }
    add_action('publish_post', 'gdk_baidu_submit', 0);
    add_action('wp_footer', 'gdk_baidu_auto_push', 500);
}

//百度自动推送
function gdk_baidu_auto_push()
{
    echo '<script>
        (function(){
            var bp = document.createElement(\'script\');
            var curProtocol = window.location.protocol.split(\':\')[0];
            if (curProtocol === \'https\') {
                bp.src = \'https://zz.bdstatic.com/linksubmit/push.js\';
            }
            else {
                bp.src = \'http://push.zhanzhang.baidu.com/push.js\';
            }
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(bp, s);
        })();
            </script>';
}

if (gdk_option('gdk_seo_img')) {
    //给文章图片自动添加alt和title信息
    function gdk_imagesalt($content)
    {
        global $post;
        $pattern = "/<a(.*?)href=('|\")(.*?).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>/i";
        $replacement = '<a$1href=$2$3.$4$5 alt="'.strip_tags($post->post_title).'" title="'.strip_tags($post->post_title).'"$6>';
        $content = preg_replace($pattern, $replacement, $content);

        return $content;
    }
    add_filter('the_content', 'gdk_imagesalt');
    function gdk_image_alt_tag($content)
    {
        global $post;
        preg_match_all('/<img (.*?)\/>/', $content, $images);
        if (!is_null($images)) {
            foreach ($images[1] as $index => $value) {
                $new_img = str_replace('<img', '<img alt="'.strip_tags($post->post_title).'-'.get_option('blogname').'"', $images[0][$index]);
                $content = str_replace($images[0][$index], $new_img, $content);
            }
        }

        return $content;
    }
    add_filter('the_content', 'gdk_image_alt_tag', 99999);
}

//关键字
function gdk_keywords()
{
    global $s, $post;
    $keywords = '';
    if (is_single()) {
        if (get_the_tags($post->ID)) {
            foreach (get_the_tags($post->ID) as $tag) {
                $keywords .= $tag->name.', ';
            }
        }
        foreach (get_the_category($post->ID) as $category) {
            $keywords .= $category->cat_name.', ';
        }

        $keywords = substr_replace($keywords, '', -2);
    } elseif (is_home() || is_front_page()) {
        $keywords = gdk_option('gdk_keywords');
    } elseif (is_tag()) {
        if (in_string(tag_description(), '@@')) {
            $keywords = gdk_term_meta('tag', 'keyword');
        } else {
            $keywords = single_tag_title('', false);
        }
    } elseif (is_category()) {
        if (in_string(category_description(), '@@')) {
            $keywords = gdk_term_meta('cat', 'keyword');
        } else {
            $keywords = single_cat_title('', false);
        }
    } elseif (is_search()) {
        $keywords = esc_html($s, 1);
    } else {
        $keywords = trim(wp_title('', false));
    }
    if ($keywords) {
        echo "<meta name=\"keywords\" content=\"{$keywords}\">\n";
    }
}
add_action('wp_head', 'gdk_keywords');

//网站描述
function gdk_description()
{
    global $s, $post;
    $description = '';
    $blog_name = get_bloginfo('name');
    $excerpt = $post->post_excerpt;
    if (is_singular()) {
        if (!empty($excerpt)) {
            $text = $excerpt;
        } else {
            $text = strip_shortcodes($post->post_content);
        }
        $description = trim(str_replace([
            "\r\n",
            "\r",
            "\n",
            '　',
            ' ',
        ], ' ', str_replace('"', "'", strip_tags($text))));
        if (!($description)) {
            $description = $blog_name.'-'.trim(wp_title('', false));
        }
    } elseif (is_home() || is_front_page()) {
        $description = gdk_option('gdk_description'); // 首頁要自己加
    } elseif (is_tag()) {
        if (in_string(tag_description(), '@@')) {
            $description = $blog_name."'".gdk_term_meta('tag', 'des')."'";
        } else {
            $description = $blog_name."'".single_tag_title('', false)."'";
        }
    } elseif (is_category()) {
        if (in_string(category_description(), '@@')) {
            $description = $blog_name."'".trim(strip_tags(gdk_term_meta('tag', 'des')));
        } else {
            $description = $blog_name."'".trim(strip_tags(category_description()));
        }
    } elseif (is_archive()) {
        $description = $blog_name."'".trim(wp_title('', false))."'";
    } elseif (is_search()) {
        $description = $blog_name.": '".esc_html($s, 1)."' 的搜索結果";
    } else {
        $description = $blog_name."'".trim(wp_title('', false))."'";
    }
    $description = mb_substr($description, 0, 220, 'utf-8');
    echo "<meta name=\"description\" content=\"{$description}\">\n";
}
add_action('wp_head', 'gdk_description');

//添加Open Graph Meta
function meta_og()
{
    global $post;
    if (is_single()) {
        if (has_post_thumbnail($post->ID)) {
            $img_src = gdk_thumbnail_src();
        }
        $excerpt = strip_tags($post->post_content);
        $excerpt_more = '';
        if (strlen($excerpt) > 155) {
            $excerpt = substr($excerpt, 0, 155);
            $excerpt_more = ' ...';
        }
        $excerpt = str_replace('"', '', $excerpt);
        $excerpt = str_replace("'", '', $excerpt);
        $excerptwords = preg_split('/[\n\r\t ]+/', $excerpt, -1, PREG_SPLIT_NO_EMPTY);
        array_pop($excerptwords);
        $excerpt = implode(' ', $excerptwords).$excerpt_more; ?>
<meta name="author" content="Your Name">
<meta name="description" content="<?php echo $excerpt; ?>">
<meta property="og:title" content="<?php echo the_title(); ?>">
<meta property="og:description" content="<?php echo $excerpt; ?>">
<meta property="og:type" content="article">
<meta property="og:url" content="<?php echo the_permalink(); ?>">
<meta property="og:site_name"
    content="<?php echo get_bloginfo('name'); ?>">
<meta property="og:image" content="<?php ?>">
<?php
    } else {
        return;
    }
}
add_action('wp_head', 'meta_og', 5);

//评论分页的seo处理
function gdk_canonical()
{
    global $post;
    if (get_query_var('paged') > 1) {
        echo "\n";
        echo "<link rel='canonical' href='";
        echo get_permalink($post->ID);
        echo "' />\n";
        echo '<meta name="robots" content="noindex,follow">';
    }
}
add_action('wp_head', 'gdk_canonical');

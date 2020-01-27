<?php

//加载网站地图xml
if(gdk_option('gdk_sitemap_xml')){
    include('sitemap-xml.php');
}

//robots.txt优化功能
if(gdk_option('gdk_robots')){
        add_filter('robots_txt', 'gdk_robots_txt', 10, 2);
    function gdk_robots_txt($robotext)
    {
        if(gdk_option('gdk_sitemap_xml')){
            $sitemap = 'Sitemap: ' . home_url('/sitemap.xml');
        } else {
            $sitemap = '';
        }
        $robotext = "User-agent: *
Disallow: /wp-admin/
Disallow: /wp-content/plugins/
Disallow: /wp-includes/
Disallow: /*/trackback
Disallow: /feed
Disallow: /*/feed
Disallow: /attachment/
Disallow: /wp-content/themes/
{$sitemap}";
        return $robotext;
    }
}

//文章自动内链
if(gdk_option('gdk_tag_link')){
        function gdk_tag_link($content)
        {
            $post_tags = get_the_tags();
            if ($post_tags) {
                foreach ($post_tags as $tag) {
                    $link = get_tag_link($tag->term_id);
                    $keyword = $tag->name;
        
                    $cleankeyword = stripslashes($keyword);
                    $url = '<a target="_blank" href="'.$link.'" title="'.str_replace('%s', addcslashes($cleankeyword, '$'), '查看更多关于%s的文章').'">'.addcslashes($cleankeyword, '$').'</a>';
                    $regEx = '\'(?!((<.*?)|(<a.*?)))('. $cleankeyword . ')(?!(([^<>]*?)>)|([^>]*?</a>))\'s';
                    $content = preg_replace($regEx, $url, $content, gdk_option('gdk_tag_num') ?? 5);
                }
            }
            return $content;
        }
    add_filter('the_content', 'gdk_tag_link', 1);
}


//给外部链接加上跳转

    function git_go_url($content){
        preg_match_all('/<a(.*?)href="(.*?)"(.*?)>/', $content, $matches);
        if ($matches && !is_page('about')) {
            foreach ($matches[2] as $val) {
                if (strpos($val, '://') !== false && strpos($val, home_url()) === false && !preg_match('/\\.(jpg|jpeg|png|ico|bmp|gif|tiff)/i', $val)) {
                    $content = str_replace("href=\"{$val}\"", "href=\"" . get_permalink(git_page_id('go')) . "?url={$val}\" ", $content);
                }
            }
        }
        return $content;
    }
    add_filter('the_content', 'git_go_url', 999);


//关键字
function deel_keywords() {
    global $s, $post;
    $keywords = '';
    if (is_single()) {
        if (get_the_tags($post->ID)) {
            foreach (get_the_tags($post->ID) as $tag) $keywords.= $tag->name . ', ';
        }
        foreach (get_the_category($post->ID) as $category) $keywords.= $category->cat_name . ', ';
        $keywords = substr_replace($keywords, '', -2);
    } elseif (is_home()) {
        //$keywords = git_get_option('git_keywords');
    } elseif (is_tag()) {
        $keywords = single_tag_title('', false);
    } elseif (is_category()) {
        $keywords = single_cat_title('', false);
    } elseif (is_search()) {
        $keywords = esc_html($s, 1);
    } else {
        $keywords = trim(wp_title('', false));
    }
    if ($keywords) {
        echo "<meta name=\"keywords\" content=\"$keywords\">\n";
    }
}


    add_action('wp_head', 'deel_keywords');

//网站描述
function deel_description() {
    global $s, $post;
    $description = '';
    $blog_name = get_bloginfo('name');
    $iexcerpt = $post->post_excerpt;
    if (is_singular()) {
        if (!empty($iexcerpt)) {
            $text = $iexcerpt;
        } else {
            $text = strip_shortcodes($post->post_content);
        }
        $description = trim(str_replace(array(
            "\r\n",
            "\r",
            "\n",
            "　",
            " "
        ) , " ", str_replace("\"", "'", strip_tags($text))));
        if (!($description)) $description = $blog_name . "-" . trim(wp_title('', false));
    } elseif (is_home()) {
        //$description = git_get_option('git_description'); // 首頁要自己加
    } elseif (is_tag()) {
        $description = $blog_name . "'" . single_tag_title('', false) . "'";
    } elseif (is_category()) {
        $description = trim(strip_tags(category_description()));
    } elseif (is_archive()) {
        $description = $blog_name . "'" . trim(wp_title('', false)) . "'";
    } elseif (is_search()) {
        $description = $blog_name . ": '" . esc_html($s, 1) . "' 的搜索結果";
    } else {
        $description = $blog_name . "'" . trim(wp_title('', false)) . "'";
    }
    $description = mb_substr($description, 0, 220, 'utf-8');
    echo "<meta name=\"description\" content=\"$description\">\n";
}

    add_action('wp_head', 'deel_description');


//WordPress文字标签关键词自动内链
/*
$match_num_min = git_get_option('git_autolink_1'); //一篇文章中同一個標籤少於幾次不自動鏈接
$match_num_max = git_get_option('git_autolink_2'); //一篇文章中同一個標籤最多自動鏈接幾次
function tag_sort($a, $b) {
    if ($a->name == $b->name) return 0;
    return (strlen($a->name) > strlen($b->name)) ? -1 : 1;
}
function tag_link($content) {
    global $match_num_min, $match_num_max;
    $posttags = get_the_tags();
    if ($posttags) {
        usort($posttags, "tag_sort");
        foreach ($posttags as $tag) {
            $link = get_tag_link($tag->term_id);
            $keyword = $tag->name;
            $cleankeyword = stripslashes($keyword);
            $url = "<a href=\"$link\" title=\"" . str_replace('%s', addcslashes($cleankeyword, '$') , '查看更多关于%s的文章') . "\"";
            $url.= ' target="_blank"';
            $url.= ">" . addcslashes($cleankeyword, '$') . "</a>";
            $limit = $match_num_max;
            $content = preg_replace('|(<a[^>]+>)(.*)(' . $keyword . ')(.*)(</a[^>]*>)|U' . $case, '$1$2%&&&&&%$4$5', $content);
            $content = preg_replace('|(<img)(.*?)(' . $keyword . ')(.*?)(>)|U' . $case, '$1$2%&&&&&%$4$5', $content);
            $content = preg_replace('|(<h[^>]+>)(.*)(' . $keyword . ')(.*)(</h[^>]*>)|U' . $case, '$1$2%&&&&&%$4$5', $content);
            $cleankeyword = preg_quote($cleankeyword, '\'');
            $regEx = '\'(?!((<.*?)|(<a.*?)))(' . $cleankeyword . ')(?!(([^<>]*?)>)|([^>]*?</a>))\'s' . $case;
            $content = preg_replace($regEx, $url, $content, $limit);
            $content = str_replace('%&&&&&%', stripslashes($keyword) , $content);
        }
    }
    return $content;
}

    add_filter('the_content', 'tag_link', 1);

*/

    //图片img标签添加alt，title属性
    function imagesalt($content){
        global $post;
        $pattern = "/<img(.*?)src=('|\")(.*?).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>/i";
        $replacement = '<img$1src=$2$3.$4$5 alt="' . $post->post_title . '" title="' . $post->post_title . '"$6>';
        $content = preg_replace($pattern, $replacement, $content);
        return $content;
    }
    add_filter('the_content', 'imagesalt');
    //图片A标签添加title属性
    function aimagesalt($content){
        global $post;
        $pattern = "/<a(.*?)href=('|\")(.*?).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>/i";
        $replacement = '<a$1href=$2$3.$4$5 title="' . $post->post_title . '"$6>';
        $content = preg_replace($pattern, $replacement, $content);
        return $content;
    }
    add_filter('the_content', 'aimagesalt');

//自动给文章以及评论添加nofollow属性

    function git_auto_nofollow($content)
    {
        $regexp = "<a\\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>";
        if (preg_match_all("/{$regexp}/siU", $content, $matches, PREG_SET_ORDER)) {
            if (!empty($matches)) {
                $srcUrl = get_option('siteurl');
                for ($i = 0; $i < count($matches); $i++) {
                    $tag = $matches[$i][0];
                    $tag2 = $matches[$i][0];
                    $url = $matches[$i][0];
                    $noFollow = '';
                    $pattern = '/rel\\s*=\\s*"\\s*[n|d]ofollow\\s*"/';
                    preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
                    if (count($match) < 1) {
                        $noFollow .= ' rel="nofollow" ';
                    }
                    $pos = strpos($url, $srcUrl);
                    if ($pos === false) {
                        $tag = rtrim($tag, '>');
                        $tag .= $noFollow . '>';
                        $content = str_replace($tag2, $tag, $content);
                    }
                }
            }
        }
        $content = str_replace(']]>', ']]>', $content);
        return $content;
    }
    add_filter('the_content', 'git_auto_nofollow');


//评论分页的seo处理
function canonical_for_git(){
    global $post;
    if (get_query_var('paged') > 1) {
        echo "\n";
        echo "<link rel='canonical' href='";
        echo get_permalink($post->ID);
        echo "' />\n";
        echo "<meta name=\"robots\" content=\"noindex,follow\">";
    }
}
add_action('wp_head', 'canonical_for_git');
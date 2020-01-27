<?php

//加载网站地图xml
if(gdk_option('gdk_sitemap_xml')){
    include('sitemap-xml.php');
}

// 屏蔽蜘蛛爬取作者页面
if(gdk_option('gdk_no_author_page')){
        function gdk_no_author_page()
        {
            if (is_author()) {
                wp_no_robots();
            }
        }
    add_action('wp_head', 'gdk_no_author_page');
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

// 自动添加nofloow
if(gdk_option('gdk_nofollow')){
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
        
        if (strpos($link, 'rel') === false) {
            $link = preg_replace("%(href=\S(?!$site_link))%i", 'rel="nofollow" $1', $link);
        } elseif (preg_match("%href=\S(?!$site_link)%i", $link)) {
            $link = preg_replace('/rel=\S(?!nofollow)\S*/i', 'rel="nofollow"', $link);
        }
        return $link;
    }
}



if (isset($others_seo['baidu_submit']) && $others_seo['baidu_submit']) {
    add_action('post_updated', 'nc_baidu_submit');
    function nc_baidu_submit($post_ID)
    {
        global $post;
        $bd_submit_site = get_bloginfo('url');
        $bd_submit_token = $others_seo['baidu_submit_key'];
        if (empty($post_ID) || empty($bd_submit_site) || empty($bd_submit_token)) {
            return;
        }
        $api = 'http://data.zz.baidu.com/urls?site='.$bd_submit_site.'&token='.$bd_submit_token;
        $status = $post->post_status;
        if ($status != '' && $status != 'publish') {
            $url = get_permalink($post_ID);
            $ch = curl_init();
            $options =  array(
                CURLOPT_URL => $api,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => $url,
                CURLOPT_HTTPHEADER => array('Content-Type: text/plain')
            );
            curl_setopt_array($ch, $options);
        }
    }

    function nc_baidu_auto_code(){
        echo '<script>
                (function(){
                    var bp = document.createElement(\'script\');
                    bp.src = \'https://zz.bdstatic.com/linksubmit/push.js\';
                    var s = document.getElementsByTagName("script")[0];
                    s.parentNode.insertBefore(bp, s);
                })();
            </script>';
    }
}
add_action('wp_footer', 'nc_baidu_auto_code', 500);


if(gdk_option('gdk_seo_img')) {
	//给文章图片自动添加alt和title信息
	function nc_imagesalt($content) {
		global $post;
		$pattern ="/<a(.*?)href=('|\")(.*?).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>/i";
		$replacement = '<a$1href=$2$3.$4$5 alt="'.strip_tags($post->post_title).'" title="'.strip_tags($post->post_title).'"$6>';
		$content = preg_replace($pattern, $replacement, $content);
		return $content;
	}
	add_filter('the_content', 'nc_imagesalt');
	function nc_image_alt_tag($content) {
		global $post;
		preg_match_all('/<img (.*?)\/>/', $content, $images);
		if(!is_null($images)) {
			foreach($images[1] as $index => $value) {
				$new_img = str_replace('<img', '<img alt="'.strip_tags($post->post_title).'-'.get_option('blogname').'"', $images[0][$index]);
				$content = str_replace($images[0][$index], $new_img, $content);
			}
		}
		return $content;
	}
	add_filter('the_content', 'nc_image_alt_tag', 99999);
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
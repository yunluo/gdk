<?php


add_theme_support('title-tag');
if (!function_exists('nc_filter_document_title_separator')):
    add_filter('document_title_separator', 'nc_filter_document_title_separator', 10, 1);
    add_filter('document_title_parts', 'nc_filter_document_title_parts', 10, 1);
    add_action('wp_head', 'nc_seo_meta_action', 1);

    function nc_filter_document_title_separator($var)
    {
        $nc_option = get_option('nc_option');
        $option_sep = $nc_option['seo_divider'];
        $var = isset($option_sep) ? $option_sep : $var;
        return trim($var);
    };

    function nc_filter_document_title_parts($title)
    {
        $nc_option = get_option('nc_option');
        global $paged, $page, $post;

        $taxonomy_seo = $nc_option['taxonomy_seo'];
        $seo_index_inner = $nc_option['seo_index_inner'];

        if (is_home() || is_front_page()) {
            $title_home = $seo_index_inner['seo_index_title'];
            $title['title'] = (isset($title_home) && !empty($title_home)) ? $title_home : get_bloginfo('name');
        } elseif (is_single() || is_page()) {
            $post_title = get_post_meta($post->ID, 'seo_title', true);
            $title['title'] = (isset($post_title) && !empty($post_title)) ? $post_title : get_the_title($post->ID);
        } elseif ($taxonomy_seo && is_category()) {
            $term = get_queried_object();
            $title_category = get_term_meta($term->term_id, 'taxonomy_title', true);
            $title['title'] = (isset($title_category) && !empty($title_category)) ? $title_category : get_cat_name($term->term_id);
        } elseif ($taxonomy_seo && is_tag()) {
            $term = get_queried_object();
            $title_tag = get_term_meta($term->term_id, 'taxonomy_title', true);
            $title['title'] = (isset($title_tag) && !empty($title_tag)) ? $title_tag : single_tag_title('', false);
        } elseif ($taxonomy_seo && is_tax()) {
            $term = get_queried_object();
            $title_tag = get_term_meta($term->term_id, 'taxonomy_title', true);
            $title['title'] = (isset($title_tag) && !empty($title_tag)) ? $title_tag : single_tag_title('', false);
        } elseif (is_author() && ! is_post_type_archive()) {
            $author = get_queried_object();
            if ($author) {
                $title['title'] = $author->display_name;
            }
        } elseif (is_search()) {
            $title['title'] = "搜索结果：".get_query_var('s');
        } elseif (is_404()) {
            $title['title'] = __('Page not found');
        }

        return $title;
    };

    function nc_seo_meta_action()
    {
        $nc_option = get_option('nc_option');
        $pages = get_query_var('page');
        $taxonomy_seo = $nc_option['taxonomy_seo'];

        $seo_index_inner = $nc_option['seo_index_inner'];

        if ((is_single() || is_page()) && $pages < 2) {
            global $post;
            $post_keywords = get_post_meta($post->ID, 'seo_keywords', true);
            $post_desc = get_post_meta($post->ID, 'seo_description', true);

            if (empty($single_description_range) || is_numeric($single_description_range)) {
                $post_desc_num = 140;
            } else {
                $post_desc_num = $single_description_range;
            }

            $seo_manual_des = get_post_meta($post->ID, 'seo_manual_des', true);
            $seo_manual_keywords = get_post_meta($post->ID, 'seo_manual_keywords', true);

            $tag = '';
            $tags = get_the_tags();
            if ($tags) {
                foreach ($tags as $val) {
                    $tag.=','.$val->name;
                }
            }
            $tag = ltrim($tag, ',');
            $key_meta = isset($post_keywords) ? $post_keywords : '';
            $des_meta = isset($post_desc) ? $post_desc : '';

            $pt = $post->post_excerpt ? $post->post_excerpt : preg_replace('/\s+/', '', strip_tags(apply_filters('the_content', $post->post_content)));
            $excerpt = mb_strimwidth($pt, 0, $post_desc_num, '', get_bloginfo('charset'));

            if ((empty($key_meta) || !$seo_manual_keywords) && isset($tag)) {
                $keywords = $tag;
            } else {
                $keywords = $key_meta;
            }

            if (empty($des_meta) || !$seo_manual_des) {
                $description = $excerpt;
            } else {
                $description = $des_meta;
            }

            if ($keywords) {
                echo '<meta name="keywords" content="'.$keywords.'" />';
                echo "\n";
            }

            if ($description) {
                echo '<meta name="description" content="'.esc_attr($description).'" />';
                echo "\n";
            }
        }

        if ((is_home() || is_front_page()) && !is_paged()) {
            $keywords = $seo_index_inner['seo_index_keywords'];
            $description = $seo_index_inner['seo_index_description'];

            if ($keywords) {
                echo '<meta name="keywords" content="'.$keywords.'" />';
                echo "\n";
            }
            if ($description) {
                echo '<meta name="description" content="'.esc_attr(stripslashes($description)).'" />';
                echo "\n";
            }
        }

        if ($taxonomy_seo && ((is_category() || is_tag() || is_tax('special')) && !is_paged())) {
            $term = get_queried_object();
        
            $keywords = get_term_meta($term->term_id, 'taxonomy_keywords', true);
            $description = get_term_meta($term->term_id, 'taxonomy_desc', true);

            if ($keywords) {
                echo '<meta name="keywords" content="'.$keywords.'" />';
                echo "\n";
            }
            if ($description) {
                echo '<meta name="description" content="'.esc_attr(stripslashes($description)).'" />';
                echo "\n";
            }
        }
    }
endif;


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
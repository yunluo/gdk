<?php
$nc_option = get_option('nc_option');
$others_seo = $nc_option['others_seo'];

if ($others_seo['noindex_author_page']) {
    if (!function_exists('nc_no_index_author_page')):
        function nc_no_index_author_page()
        {
            if (is_author()) {
                wp_no_robots();
            }
        }
    add_action('wp_head', 'nc_no_index_author_page');
    endif;
}

if ($others_seo['sitemap_xml']) {
    $GLOBALS['nice_sitemap_xml'] = true;
    include(NC_OPTIMIZEUP_DIR . '/functions/sitemap-xml.php');
}

if ($others_seo['auto_nofollow']) {
    if (!function_exists('nc_nofollow')):
        add_filter('the_content', 'nc_nofollow');
    add_filter('the_excerpt', 'nc_nofollow');
        
    function nc_nofollow($content)
    {
        return preg_replace_callback('/<a[^>]+/', 'nc_nofollow_callback', $content);
    }
        
    function nc_nofollow_callback($matches)
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
    endif;
}

if ($others_seo['auto_tag_link_switcher']) {
    if (!function_exists('nc_tag_link')):
        function nc_tag_link($content)
        {
            $post_tags = get_the_tags();

            if ($post_tags) {
                foreach ($post_tags as $tag) {
                    $link = get_tag_link($tag->term_id);
                    $keyword = $tag->name;
        
                    $cleankeyword = stripslashes($keyword);
                    $url = '<a target="_blank" href="'.$link.'" title="'.str_replace('%s', addcslashes($cleankeyword, '$'), __('View all posts in %s')).'">'.addcslashes($cleankeyword, '$').'</a>';
                    $regEx = '\'(?!((<.*?)|(<a.*?)))('. $cleankeyword . ')(?!(([^<>]*?)>)|([^>]*?</a>))\'s';
                    $content = preg_replace($regEx, $url, $content, $others_seo['auto_tag_link_switcher_limit'] ?? 5);
                }
            }
            return $content;
        }
    add_filter('the_content', 'nc_tag_link', 1);
    endif;
}
if ($general_options['improve_robots_txt']) {
    if (!function_exists('nc_robots_txt')):
        add_filter('robots_txt', 'nc_robots_txt', 10, 2);

    function nc_robots_txt($robotext)
    {
        if (isset($GLOBALS['nice_sitemap_xml'])) {
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
    endif;
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
    add_action('wp_footer', 'nc_baidu_auto_code', 500);
    function nc_baidu_auto_code()
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
}

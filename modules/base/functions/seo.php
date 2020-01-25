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

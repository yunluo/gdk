<?php

/*
            /$$
    /$$    /$$$$
   | $$   |_  $$    /$$$$$$$
 /$$$$$$$$  | $$   /$$_____/
|__  $$__/  | $$  |  $$$$$$
   | $$     | $$   \____  $$
   |__/    /$$$$$$ /$$$$$$$/
          |______/|_______/
================================
        Keep calm and get rich.
                    Is the best.

    @Author: Dami
    @Date:   2018-10-28 21:57:45
    @Last Modified by:   Dami
    @Last Modified time: 2019-03-21 20:50:50

*/
if (! defined('ABSPATH')) {
    exit;
}

if (!function_exists('acf_add_options_page')) {
    include(NC_STORE_ROOT_PATH . 'library/framework/acf.php');

    add_filter('acf/settings/path', 'nc_store_settings_path');
     
    function nc_store_settings_path($path)
    {
     
        // update path
        $path = NC_STORE_ROOT_PATH . 'library/framework/';
        
        // return
        return $path;
    }
     
    add_filter('acf/settings/dir', 'nc_store_settings_dir');
     
    function nc_store_settings_dir($dir)
    {
     
        // update path
        $dir = NC_BASE_URL . 'library/framework/';
        
        // return
        return $dir;
    }

    add_action('acf/settings/save_json', 'nc_config_save_path');

    function nc_config_save_path()
    {
        if (isset($_POST['acf_field_group']['config_save_path']) && $_POST['acf_field_group']['config_save_path'] != 'default') {
            $path = WP_CONTENT_DIR . '/' . $_POST['acf_field_group']['config_save_path'];

            return $path;
        }
    }

    function nicetheme_load_config()
    {
        do_action('acf/include_fields');
    }
    add_action('after_setup_theme', 'nicetheme_load_config', 999999);

    add_filter('acf/settings/show_admin', '__return_false');

    add_filter('acf/settings/remove_wp_meta_box', '__return_false');
}

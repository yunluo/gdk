<?php

function nc_optimizeup_enqueue_script() {
    wp_register_script('nicetheme-optimizeup', plugin_dir_url( __FILE__ ) . 'assets/main.js', array( 'jquery' ));
    wp_enqueue_script('nicetheme-optimizeup');
    wp_localize_script( 'nicetheme-optimizeup', 'nicetheme',
        array(
            "ajax_url" => admin_url("admin-ajax.php"),
        )
    );
}
add_action('admin_enqueue_scripts', 'nc_optimizeup_enqueue_script');

function nc_optimizeup_enqueue_script_frontend() {
    if (!is_admin()) {
        wp_register_style( 'code_prettify_css', NC_BASE_URL.'assets/css/gdk.css', array(), NC_STORE_VER, 'all' );
        wp_register_script('code_prettify_js', NC_BASE_URL.'assets/js/prettify.min.js', array('jquery'), NC_STORE_VER, true);
        wp_enqueue_script('code_prettify_js');
        wp_enqueue_style( 'code_prettify_css' );
    }
}
add_action('wp_enqueue_scripts', 'nc_optimizeup_enqueue_script_frontend');
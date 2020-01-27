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
        wp_register_style( 'jimu-css', NC_BASE_URL . 'modules/jimu.css', array(), NC_OPTIMIZEUP_VERSION, 'all' );
        wp_register_script('jimu-js', NC_BASE_URL . 'modules/jimu.js', array('jquery'), NC_OPTIMIZEUP_VERSION, true);
        wp_enqueue_script('jimu-js');
        wp_enqueue_style( 'jimu-css' );
    }
}
add_action('wp_enqueue_scripts', 'nc_optimizeup_enqueue_script_frontend');
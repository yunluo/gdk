<?php

function gdk_admin_script() {
    ?>
    <script>
    jQuery(function ($) {
        $("#content").pasteUploadImage(ajaxurl);
    });
    </script>
            <?php
}



function nc_optimizeup_enqueue_script($hook) {
    //wp_register_script('nicetheme-optimizeup', plugin_dir_url( __FILE__ ) . 'assets/main.js', array( 'jquery' ));
    wp_register_script('paste-upload-image', GDK_BASE_URL.'assets/js/paste-upload-image.js', array( 'jquery' ), GDK_PLUGIN_VER, true);
    //wp_enqueue_script('nicetheme-optimizeup');
    wp_enqueue_script('paste-upload-image');
    wp_register_script('sweetalert','https://cdn.bootcss.com/sweetalert/2.1.2/sweetalert.min.js', [], GDK_PLUGIN_VER, true);

}
add_action('admin_enqueue_scripts', 'nc_optimizeup_enqueue_script');

function nc_optimizeup_enqueue_script_frontend() {
    if (!is_admin()) {
        wp_register_style( 'gdk_css', GDK_BASE_URL.'assets/css/gdk.css', [], GDK_PLUGIN_VER, 'all' );
        wp_register_script('code_prettify_js', GDK_BASE_URL.'assets/js/prettify.min.js', array('jquery'), GDK_PLUGIN_VER, true);
        wp_register_script('fancybox_js', GDK_BASE_URL.'assets/js/fancybox.min.js', array('jquery'), GDK_PLUGIN_VER, true);
        wp_register_script('lazyload_js', GDK_BASE_URL.'assets/js/lazyload.min.js', array('jquery'), GDK_PLUGIN_VER, true);
        wp_enqueue_style( 'gdk_css' );
        wp_enqueue_script('code_prettify_js');
        wp_enqueue_script('lazyload_js');
        wp_enqueue_script('fancybox_js');
        
    }
}
add_action('wp_enqueue_scripts', 'nc_optimizeup_enqueue_script_frontend');


//
function gdk_admin_assets() {
	gdk_admin_script();
}
add_action('admin_footer','gdk_admin_assets');
<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }


function gdk_admin_enqueue_script($hook_suffix) {
if($hook_suffix == 'post.php'|| $hook_suffix == 'post-new.php'){
    wp_register_script('paste-upload-image', GDK_BASE_URL.'assets/js/paste-upload-image.js', array( 'jquery' ), GDK_PLUGIN_VER, true);
    wp_enqueue_script('paste-upload-image');
    wp_localize_script('paste-upload-image', 'pui_vars', array('pui_nonce' => wp_create_nonce('pui-nonce')));

}
    

}
add_action('admin_enqueue_scripts', 'gdk_admin_enqueue_script');

function gdk_enqueue_script_frontend() {
    if (!is_admin()) {
        //wp_enqueue_style( 'pure_css', GDK_BASE_URL.'assets/css/pure.css', [], GDK_PLUGIN_VER, 'all' );
        wp_enqueue_style( 'milligram_css', GDK_BASE_URL.'assets/css/milligram.min.css', [], GDK_PLUGIN_VER, 'all' );
        wp_enqueue_style( 'gdk_css', GDK_BASE_URL.'assets/css/gdk.css', [], GDK_PLUGIN_VER, 'all' );
        wp_deregister_script('jquery');
        wp_enqueue_script('jquery', GDK_BASE_URL.'assets/js/jQuery.min.js', false, GDK_PLUGIN_VER, true);//加载自定义jQuery2.0.3
        wp_enqueue_script('code_prettify_js', GDK_BASE_URL.'assets/js/prettify.min.js', array('jquery'), GDK_PLUGIN_VER, true);
        wp_enqueue_script('fancybox_js', GDK_BASE_URL.'assets/js/fancybox.min.js', array('jquery'), GDK_PLUGIN_VER, true);
        wp_enqueue_script('lazyload_js', GDK_BASE_URL.'assets/js/lazyload.min.js', array('jquery'), GDK_PLUGIN_VER, true);
        wp_enqueue_script('sweetalert_js','https://cdn.jsdelivr.net/combine/npm/sweetalert@2.0.0,npm/qrious@4.0.2', [], GDK_PLUGIN_VER, true);
        wp_enqueue_script('gdk_js', GDK_BASE_URL.'assets/js/gdk.js', array('jquery'), GDK_PLUGIN_VER, true);
        wp_localize_script('gdk_js', 'ajax', [
            'url'=> admin_url('admin-ajax.php'), 
            'pass_nonce' => wp_create_nonce('pass_nonce'),
            'pay_points' => wp_create_nonce('pay_points'),
            'check_pay_points' => wp_create_nonce('check_pay_points'),
            'check_pay_view' => wp_create_nonce('check_pay_view'),
            'check_code' => wp_create_nonce('check_code'),
            'gdk_weauth_qr_gen' => wp_create_nonce('gdk_weauth_qr_gen'),
            'gdk_weauth_check' => wp_create_nonce('gdk_weauth_check'),
        ]);
        
        
    }
}
add_action('wp_enqueue_scripts', 'gdk_enqueue_script_frontend');


//后台脚本
function gdk_admin_script() {
    ?>
    <script>
jQuery(function($) {
    /* bengin */
    if ($("#ed_toolbar").length > 0) {
    /***文章编辑器 */
    QTags.addButton('ipre', '代码高亮', '<pre class="prettyprint linenums" >\n\n</pre>', "");
    $("#content").pasteUploadImage(ajaxurl); //ajax img upload
    $(".insert-shortcodes").click(function() { //shortcode botton
        if ($(".shortcodes-wrap").hasClass("is-active")) {
            $(".shortcodes-wrap").removeClass("is-active")
        } else {
            $(".shortcodes-wrap").addClass("is-active")
        }
    });
    $(".add-shortcode").click(function() {
        send_to_editor(" " + $(this).data("shortcodes") + " ");
        $(".shortcodes-wrap").removeClass("is-active");
        return false
    });
}

if ($("#replysubmit").length > 0) {
    /**评论框 */
    $("textarea").keypress(function(e) {
        if (e.ctrlKey && e.which == 13 || e.which == 10) {
            $("#replybtn").click();
        }
    });
}
	




    
    /**end**/
     
});
    </script>
            <?php
	
}
add_action('admin_footer','gdk_admin_script');

function gdk_admin_style() {
    ?><style>*{font-family: "Microsoft YaHei";}.shortcodes-wrap{background:#fff;border: 1px solid #ccc;box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.24);padding: 10px;position: absolute;top:54px;width:500px;display:none}.is-active.shortcodes-wrap{display:block}.insert-shortcodes{padding-left:35px!important}#insert-shortcode-button {background: url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj4gPHBhdGggZD0iTTI5MC41OSAxOTJjLTIwLjE4IDAtMTA2LjgyIDEuOTgtMTYyLjU5IDg1Ljk1VjE5MmMwLTUyLjk0LTQzLjA2LTk2LTk2LTk2LTE3LjY3IDAtMzIgMTQuMzMtMzIgMzJzMTQuMzMgMzIgMzIgMzJjMTcuNjQgMCAzMiAxNC4zNiAzMiAzMnYyNTZjMCAzNS4zIDI4LjcgNjQgNjQgNjRoMTc2YzguODQgMCAxNi03LjE2IDE2LTE2di0xNmMwLTE3LjY3LTE0LjMzLTMyLTMyLTMyaC0zMmwxMjgtOTZ2MTQ0YzAgOC44NCA3LjE2IDE2IDE2IDE2aDMyYzguODQgMCAxNi03LjE2IDE2LTE2VjI4OS44NmMtMTAuMjkgMi42Ny0yMC44OSA0LjU0LTMyIDQuNTQtNjEuODEgMC0xMTMuNTItNDQuMDUtMTI1LjQxLTEwMi40ek00NDggOTZoLTY0bC02NC02NHYxMzQuNGMwIDUzLjAyIDQyLjk4IDk2IDk2IDk2czk2LTQyLjk4IDk2LTk2VjMybC02NCA2NHptLTcyIDgwYy04Ljg0IDAtMTYtNy4xNi0xNi0xNnM3LjE2LTE2IDE2LTE2IDE2IDcuMTYgMTYgMTYtNy4xNiAxNi0xNiAxNnptODAgMGMtOC44NCAwLTE2LTcuMTYtMTYtMTZzNy4xNi0xNiAxNi0xNiAxNiA3LjE2IDE2IDE2LTcuMTYgMTYtMTYgMTZ6Ij48L3BhdGg+IDwvc3ZnPg==) no-repeat left/30%;background-position: 10% 40%;}#wp-content-media-buttons > div> a:nth-child(20){background:#f6003c;border-color:#f6003c;color:#fff;}.wp-block{max-width:45pc}.wp-block[data-align=wide]{max-width:810pt}.wp-block[data-align=full]{max-width:none}#activity-widget #the-comment-list .avatar {width: 50px;}
    </style>
    <?php
}
add_action('admin_head', 'gdk_admin_style');
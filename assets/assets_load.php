<?php

if (!defined('ABSPATH')) {exit;}

function gdk_admin_enqueue_script($hook_suffix)
{
    if ($hook_suffix == 'post.php' || $hook_suffix == 'post-new.php') {
        wp_register_script('paste-upload-image', GDK_BASE_URL . 'assets/js/paste-upload-image.js', array('jquery'), GDK_PLUGIN_VER, true);
        wp_enqueue_script('paste-upload-image');
        wp_localize_script('paste-upload-image', 'pui_vars', array('pui_nonce' => wp_create_nonce('pui-nonce')));
    }
}
add_action('admin_enqueue_scripts', 'gdk_admin_enqueue_script');

function gdk_enqueue_script_frontend()
{
    if (!is_admin()) {
        wp_enqueue_style('pure_css', 'https://cdn.jsdelivr.net/npm/css-mint@2.0.7/build/css-mint.min.css', false, GDK_PLUGIN_VER, 'all');
        wp_enqueue_style('font-awesome', GDK_BASE_URL . 'assets/css/font-awesome.min.css', false, GDK_PLUGIN_VER, 'all');
        //wp_enqueue_style( 'milligram_css', GDK_BASE_URL.'assets/css/milligram.min.css', false, GDK_PLUGIN_VER, 'all' );
        wp_enqueue_style('gdk_css', GDK_BASE_URL . 'assets/css/gdk.css', false, GDK_PLUGIN_VER, 'all');
        wp_deregister_script('jquery');
        wp_enqueue_script('jquery', 'https://cdn.jsdelivr.net/npm/jquery@2.1.0/dist/jquery.min.js', false, GDK_PLUGIN_VER, true); //加载自定义jQuery2.0.3
        
        //wp_enqueue_script('code_prettify_js', GDK_BASE_URL . 'assets/js/prettify.min.js', array('jquery'), GDK_PLUGIN_VER, true);
        //wp_enqueue_script('fancybox_js', GDK_BASE_URL . 'assets/js/fancybox.min.js', array('jquery'), GDK_PLUGIN_VER, true);
        wp_enqueue_script('libs_js', GDK_BASE_URL . 'assets/js/libs.min.js', array('jquery'), GDK_PLUGIN_VER, true);
        //wp_enqueue_script('sweetalert_js', 'https://cdn.jsdelivr.net/combine/npm/sweetalert@2.0.0,npm/qrious@4.0.2', [], GDK_PLUGIN_VER, true);
        wp_enqueue_script('gdk_js', GDK_BASE_URL . 'assets/js/gdk.js', array('jquery'), GDK_PLUGIN_VER, true);
        wp_localize_script('gdk_js', 'gdk', [
            'ajaxurl'           => admin_url('admin-ajax.php'),
            'pass_nonce'        => wp_create_nonce('pass_nonce'),
            'pay_points'        => wp_create_nonce('pay_points'),
            'check_pay_points'  => wp_create_nonce('check_pay_points'),
            'check_pay_view'    => wp_create_nonce('check_pay_view'),
            'check_code'        => wp_create_nonce('check_code'),
            'gdk_weauth_qr_gen' => wp_create_nonce('gdk_weauth_qr_gen'),
            'gdk_weauth_check'  => wp_create_nonce('gdk_weauth_check'),
            'gdk_payjs_alipay'  => gdk_option('gdk_payjs_alipay'),
            'user_id'           => get_current_user_id(),
        ]);
    }
}
add_action('wp_enqueue_scripts', 'gdk_enqueue_script_frontend');

//后台脚本
function gdk_admin_script()
{
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
        send_to_editor("" + $(this).data("shortcodes") + "");
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
add_action('admin_footer', 'gdk_admin_script');

function gdk_admin_style()
{
    ?><style>
body{font-family:"Microsoft YaHei"}
.shortcodes-icon {display:inline-block;width:20px;height:20px;line-height:1;vertical-align:middle;margin:0 2px}
.insert-shortcodes {position: relative; color: #3b88c3; border-color: #3b88c3;}
.shortcodes-wrap {background:#fff;border:1px solid #ccc;box-shadow:2px 2px 3px rgba(0,0,0,0.24);padding:10px;position:absolute;top:54px;width:500px;display:none}
.is-active.shortcodes-wrap {display:block}
#wp-content-media-buttons > div> a:nth-child(20){background:#f6003c;border-color:#f6003c;color:#fff}
.wp-block {max-width:45pc}
.wp-block[data-align=wide] {max-width:810pt}
.wp-block[data-align=full] {max-width:none}
#activity-widget #the-comment-list .avatar{width:50px}
.form-field-download_name,.form-field-download_size{width:45%;float:left}
.form-field-download_link{clear:both}
    </style>
    <?php
}
add_action('admin_head', 'gdk_admin_style');
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
        wp_enqueue_style( 'uikit', 'https://cdn.jsdelivr.net/npm/uikit@3.7.4/dist/css/uikit.min.css', false, GDK_PLUGIN_VER, 'all' );

        wp_enqueue_style('gdk', GDK_BASE_URL . 'assets/css/gdk.css', false, GDK_PLUGIN_VER, 'all');

        wp_deregister_script('jquery');
        wp_enqueue_script('jquery', 'https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js', false, GDK_PLUGIN_VER, true); //加载自定义jQuery2.0.3
            // Comment Reply
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
  
        wp_enqueue_script('libs', GDK_BASE_URL . 'assets/js/libs.min.js', ['jquery'], GDK_PLUGIN_VER, true);
        wp_enqueue_script('uikit', 'https://cdn.jsdelivr.net/npm/uikit@3.7.4/dist/js/uikit.min.js', [], GDK_PLUGIN_VER, true);
        wp_enqueue_script('uikit-icon', 'https://cdn.jsdelivr.net/npm/uikit@3.7.4/dist/js/uikit-icons.min.js', [], GDK_PLUGIN_VER, true);
        wp_enqueue_script('gdk', GDK_BASE_URL . 'assets/js/gdk.js', ['jquery'], GDK_PLUGIN_VER, true);
        wp_localize_script('gdk', 'gdk', [
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
			'msg_nonce'         => wp_create_nonce('msg_nonce'),
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
    if ($("#wp-content-editor-tools").length > 0) {
    /***文章编辑器 */
	QTags.addButton('h2', 'H2标题', '<h2>', '</h2>');
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
        send_to_editor( $(this).data("shortcodes") );
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
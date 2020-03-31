<?php

//短代码集合

//小工具运行短代码
add_filter('widget_text', 'shortcode_unautop');
add_filter('widget_text', 'do_shortcode');

//积分充值短代码
function gdk_potin()
{
    return buy_points();
}
add_shortcode('gdk_potin_btn', 'gdk_potin');

//
function gdk_login_fancybox()
{
    return weixin_login_btn();
}
add_shortcode('gdk_login_btn', 'gdk_login_fancybox');

//添加钮Download
function gdk_DownloadUrl($atts, $content = null)
{
    extract(shortcode_atts(array(
        'href' => 'http://',
    ), $atts));
    return '<a class="dl" href="' . $href . '" target="_blank" rel="nofollow"><i class="fa fa-cloud-download"></i>' . $content . '</a>';
}
add_shortcode('dl', 'gdk_DownloadUrl');
//添加钮git
function gdk_GithubUrl($atts, $content = null)
{
    extract(shortcode_atts(array(
        'href' => 'http://',
    ), $atts));
    return '<a class="dl" href="' . $href . '" target="_blank" rel="nofollow"><i class="fa fa-github-alt"></i>' . $content . '</a>';
}
add_shortcode('gt', 'gdk_GithubUrl');
//添加钮Demo
function gdk_DemoUrl($atts, $content = null)
{
    extract(shortcode_atts(array(
        'href' => 'http://',
    ), $atts));
    return '<a class="dl" href="' . $href . '" target="_blank" rel="nofollow"><i class="fa fa-external-link"></i>' . $content . '</a>';
}
add_shortcode('dm', 'gdk_DemoUrl');
//使用短代码添加回复后可见内容开始
function gdk_reply_to_read($atts, $content = null)
{
    extract(shortcode_atts(array(
        'notice' => '<div class="alert info pull-center"><p class="reply-to-read">注意：本段内容须成功“<a href="' . get_permalink() . '#respond" title="回复本文">回复本文</a>”后“<a href="javascript:window.location.reload();" title="刷新本页">刷新本页</a>”方可查看！</p></div>',
    ), $atts));
    $email   = null;
    $user_ID = get_current_user_id();
    if ($user_ID > 0) {
        $email = get_user_by('id', $user_ID)->user_email;
        //对博主直接显示内容
        $admin_email = get_bloginfo('admin_email');
        if ($email == $admin_email) {
            return $content;
        }
    } elseif (isset($_COOKIE['comment_author_email_' . COOKIEHASH])) {
        $email = str_replace('%40', '@', $_COOKIE['comment_author_email_' . COOKIEHASH]);
    } else {
        return $notice;
    }
    if (empty($email)) {
        return $notice;
    }
    global $wpdb;
    $post_id = get_the_ID();
    $query   = "SELECT `comment_ID` FROM {$wpdb->comments} WHERE `comment_post_ID`={$post_id} and `comment_approved`='1' and `comment_author_email`='{$email}' LIMIT 1";
    if ($wpdb->get_results($query)) {
        return do_shortcode($content);
    } else {
        return $notice;
    }
}
add_shortcode('reply', 'gdk_reply_to_read');

//绿色提醒框
function gdk_toz($atts, $content = null)
{
    return '<div class="alert success">' . $content . '</div>';
}
add_shortcode('v_notice', 'gdk_toz');
//红色提醒框
function gdk_toa($atts, $content = null)
{
    return '<div class="alert error">' . $content . '</div>';
}
add_shortcode('v_error', 'gdk_toa');
//黄色提醒框
function gdk_toc($atts, $content = null)
{
    return '<div class="alert warning">' . $content . '</div>';
}
add_shortcode('v_warn', 'gdk_toc');

//蓝色提醒框
function gdk_tod($atts, $content = null)
{
    return '<div class="alert primary">' . $content . '</div>';
}
add_shortcode('v_blue', 'gdk_tod');
//蓝边文本框
function gdk_toe($atts, $content = null)
{
    return '<div  class="alert">' . $content . '</div>';
}
add_shortcode('v_tips', 'gdk_toe');

//灵魂按钮
function gdk_tom($atts, $content = null)
{
    extract(shortcode_atts(array(
        'href' => 'http://',
    ), $atts));
    return '<a class="cm-btn success" href="' . $href . '" target="_blank" rel="nofollow">' . $content . '</a>';
}
add_shortcode('lhb', 'gdk_tom');
//添加视频按钮
function gdk_too($atts, $content = null)
{
    extract(shortcode_atts(array(
        'play' => '0',
    ), $atts));
    if ($play == 0) {
        return '<video style="width:100%;" src="' . $content . '" controls preload >您的浏览器不支持HTML5的 video 标签，无法为您播放！</video>';
    }
    if ($play == 1) {
        return '<video style="width:100%;" src="' . $content . '" controls preload autoplay >您的浏览器不支持HTML5的 video 标签，无法为您播放！</video>';
    }
}
add_shortcode('video', 'gdk_too');
//添加音频按钮
function gdk_tkk($atts, $content = null)
{
    extract(shortcode_atts(array(
        'play' => '0',
    ), $atts));
    if ($play == 0) {
        return '<audio style="width:100%;" src="' . $content . '" controls loop>您的浏览器不支持 audio 标签。</audio>';
    }
    if ($play == 1) {
        return '<audio style="width:100%;" src="' . $content . '" controls autoplay loop>您的浏览器不支持 audio 标签。</audio>';
    }
}
add_shortcode('audio', 'gdk_tkk');
//弹窗下载
function gdk_ton($atts, $content = null)
{
    extract(shortcode_atts(array(
        'href'     => 'http://',
        'filename' => '',
        'filesize' => '',
        'filedown' => '',
    ), $atts));
    return '<a class="lhb" id="showdiv" href="#fancydlbox" >文件下载</a><div id="fancydlbox" style="cursor:default;display:none;width:800px;"><div class="part" style="padding:20px 0;"><h2>下载声明:</h2> <div class="fancydlads" align="left"><p>' . gdk_option('gdk_fancydlcp') . '</p></div></div><div class="part" style="padding:20px 0;"><h2>文件信息：</h2> <div class="dlnotice" align="left"><p>文件名称：' . $filename . '<br />文件大小：' . $filesize . '<br />发布日期：' . get_the_modified_time('Y年n月j日') . '</p></div></div><div class="part" id="download_button_part"><a id="download_button" target="_blank" href="' . $href . '"><span></span>' . $filedown . '</a> </div><div class="part" style="padding:20px 0;"><div class="moredl" style="text-align:center;">[更多地址] : ' . $content . '</div></div><div class="dlfooter">' . gdk_option('gdk_fancydlad') . '</div></div>';
}
add_shortcode('fanctdl', 'gdk_ton');


//下载单页短代码
function gdk_download($atts, $content = null)
{
    extract(shortcode_atts(array('title' => '点击下载',), $atts));
    return '<a class="cm-btn primary" href="' . home_url() . '?dl=' . get_the_ID() . '" target="_blank" rel="nofollow">' . $title . '</a>';
}
add_shortcode('pdownload', 'gdk_download');

//为WordPress添加展开收缩功能
function gdk_xcollapse($atts, $content = null)
{
    extract(shortcode_atts(array(
        'title' => '',
    ), $atts));
    return '<div style="margin: 0.5em 0;"><div class="xControl"><a href="javascript:void(0)" class="collapseButton xButton"><i class="fa fa-plus-square" ></i> ' . $title . '</a><div style="clear: both;"></div></div><div class="xContent" style="display: none;">' . $content . '</div></div>';
}
add_shortcode('collapse', 'gdk_xcollapse');
//简单的下载面板
function gdk_xdltable($atts, $content = null)
{
    extract(shortcode_atts(array(
        'file' => '',
        'size' => '',
    ), $atts));
    return '<table class="dltable"><tbody><tr><td style="background-color:#F9F9F9;" rowspan="3"><p>文件下载</p></td><td><i class="fa fa-list-alt"></i>&nbsp;&nbsp;文件名称：' . $file . '</td><td><i class="fa fa-th-large"></i>&nbsp;&nbsp;文件大小：' . $size . '</td></tr><tr><td colspan="2"><i class="fa fa-volume-up"></i>&nbsp;&nbsp;下载声明：' . gdk_option('gdk_dltable_b') . '</td></tr><tr><td colspan="2"><i class="fa fa-download"></i>&nbsp;&nbsp;下载地址：' . $content . '</td></tr></tbody></table>';
}
add_shortcode('dltable', 'gdk_xdltable');
//网易云音乐
function gdk_music163($atts, $content = null)
{
    extract(shortcode_atts(array(
        'play' => '1',
    ), $atts));
    return '<iframe style="width:100%;max-height:86px;" frameborder="no" border="0" marginwidth="0" marginheight="0" src="http://music.163.com/outchain/player?type=2&id=' . $content . '&auto=' . $play . '&height=66"></iframe>';
}
add_shortcode('netmusic', 'gdk_music163');
//登录可见
function gdk_login_to_read($atts, $content = null)
{
    $logina = '<a target="_blank" href="' . esc_url(wp_login_url(get_permalink())) . '">登录</a>';
    extract(shortcode_atts(array(
        'notice' => '<div class="alert info pull-center"><p class="reply-to-read" style="color: blue;">注意：本段内容须“' . $logina . '”后方可查看！</p></div>',
    ), $atts));
    if (is_user_logged_in() && !is_null($content) && !is_feed()) {
        return '<fieldset class="fieldset"><legend class="legend">隐藏内容</legend><p>' . $content . '</p></fieldset>';
    }
    return $notice;
}
add_shortcode('vip', 'gdk_login_to_read');

// 部分内容输入密码可见
function gdk_secret_view($atts, $content = null)
{
    $pid = get_the_ID();
    add_post_meta($pid, '_pass_content', $content, true) or update_post_meta($pid, '_pass_content', $content);
    if (current_user_can('administrator')) {
        return '<fieldset class="fieldset"><legend class="legend">隐藏内容</legend><p>' . $content . '</p></fieldset>';
    } //admin show
    return '<div class="cm-grid cm-card pass_viewbox">
   <div class="cm-row">
      <div class="cm-col-md-4">
         <img src="'.gdk_option('gdk_mp_qr').'" class="cm-resp-img">
      </div>
      <div class="cm-col-md-8" style="margin-top:4%;">
         <div class="hide_content_info" style="margin:10px 0">
			<div class="cm-alert primary">本段内容已被隐藏，您需要扫码关注微信公众号申请验证码查看，发送【验证码】获取验证码，验证码2分钟有效</div>
		<input type="text" id="pass_view" placeholder="输入验证码并提交" style="width:70%"> &nbsp;&nbsp;<input id="submit_pass_view" class="cm-btn success" data-action="gdk_pass_view" data-id="' . $pid . '" type="button" value="提交">
         </div>
      </div>
   </div>
</div>';

}
add_shortcode('wxcaptcha', 'gdk_secret_view');

// 支持文章和页面运行PHP代码
function gdk_php_include($attr)
{
    $file       = $attr['file'];
    $upload_dir = wp_upload_dir();
    $folder     = $upload_dir['basedir'] . '/php-content' . "/{$file}.php";
    ob_start();
    include $folder;
    return ob_get_clean();
}
add_shortcode('phpcode', 'gdk_php_include');

//给文章加内链短代码
function gdk_insert_posts($atts, $content = null)
{
    extract(shortcode_atts(array(
        'ids' => '',
    ), $atts));
    global $post;
    $content     = '';
    $postids     = explode(',', $ids);
    $inset_posts = get_posts(array(
        'post__in' => $postids,
    ));
    foreach ($inset_posts as $key => $post) {
        setup_postdata($post);
        $content .= '<div class="neilian"><div class="fll"><a target="_blank" href="' . get_permalink() . '" class="fll linkss"><i class="fa fa-link fa-fw"></i>  ';
        $content .= get_the_title();
        $content .= '</a><p class="note">';
        $content .= get_the_excerpt();
        $content .= '</p></div><div class="frr"><a target="_blank" href="' . get_permalink() . '"><img src=';
        $content .= gdk_thumbnail_src();
        $content .= ' class="neilian-thumb"></a></div></div>';
    }
    wp_reset_postdata();
    return $content;
}
add_shortcode('neilian', 'gdk_insert_posts');

//给文章加内链短代码
function gdk_insert_temp($atts, $content = null)
{
    extract(shortcode_atts(array('id' => ''), $atts));
    $data    = get_post($id);
    $content = $data->post_content;
    return $content;
}
add_shortcode('temp', 'gdk_insert_temp');

//快速插入列表
function gdk_list_shortcode_handler($atts, $content = '')
{
    $content = trim($content);
    $lists   = explode("\n", $content);
    $output  = '';
    foreach ($lists as $li) {
        if (trim($li) != '') {
            $output .= "<li>{$li}</li>";
        }
    }
    $output = "<ul>" . $output . "</ul>\n";
    return $output;
}
add_shortcode('list', 'gdk_list_shortcode_handler');

//表格短代码
function gdk_table_shortcode_handler($atts, $content = '')
{
    extract(shortcode_atts(['width' => '100%'], $atts));
    $output  = '';
    $content = trim($content);
    $trs     = explode("\r\n", $content);
    $ths     = explode("  ", $trs[0]); //表头数组
    $output .= '<thead><tr>';
    //var_dump($ths);
    foreach ($ths as $th) {
        $th = trim($th);
        $output .= '<th>' . $th . '</th>';
    }
    $output .= '</tr></thead>';
    $output .= '<tbody>';
    unset($trs[0]);
    foreach ($trs as $tr) {
        $tr = trim($tr);
        if ($tr) {
            $tds = explode("  ", $tr);
            $output .= '<tr>';
            foreach ($tds as $td) {
                $td = trim($td);
                if ($td) {
                    $output .= '<td>' . $td . '</td>';
                }
            }
            $output .= '</tr>';
        }
    }
    $output .= '</tbody>';
    $width  = ' width="' . $width . '"';
    $output = '<table class="gdk-table"' . $width . ' >' . $output . '</table>\n';

    return $output;
}
add_shortcode('table', 'gdk_table_shortcode_handler');

add_shortcode('youku', function ($atts, $content = '') {
    extract(shortcode_atts(array(
        'width'  => '510',
        'height' => '498',
    ), $atts));

    $width  = (isset($_GET['width']) && intval($_GET['width'])) ? intval($_GET['width']) : $width; // 用于 JSON 接口
    $height = round($width / 4 * 3);

    if (preg_match('#http://v.youku.com/v_show/id_(.*?).html#i', $content, $matches)) {
        return '<iframe class="wpjam_video" height=' . esc_attr($height) . ' width=' . esc_attr($width) . ' src="http://player.youku.com/embed/' . esc_attr($matches[1]) . '" frameborder=0 allowfullscreen></iframe>';
    }
});

add_shortcode('qqv', function ($atts, $content = '') {
    extract(shortcode_atts(array(
        'width'  => '510',
        'height' => '498',
    ), $atts));

    $width  = (isset($_GET['width']) && intval($_GET['width'])) ? intval($_GET['width']) : $width; // 用于 JSON 接口
    $height = round($width / 4 * 3);

    if (preg_match('#//v.qq.com/iframe/player.html\?vid=(.+)#i', $content, $matches)) {
        //var_dump($matches);exit();
        return '<iframe class="wpjam_video" height=' . esc_attr($height) . ' width=' . esc_attr($width) . ' src="http://v.qq.com/iframe/player.html?vid=' . esc_attr($matches[1]) . '" frameborder=0 allowfullscreen></iframe>';
    } elseif (preg_match('#//v.qq.com/iframe/preview.html\?vid=(.+)#i', $content, $matches)) {
        //var_dump($matches);exit();
        return '<iframe class="wpjam_video" height=' . esc_attr($height) . ' width=' . esc_attr($width) . ' src="http://v.qq.com/iframe/player.html?vid=' . esc_attr($matches[1]) . '" frameborder=0 allowfullscreen></iframe>';
    }
});

add_shortcode('tudou', function ($atts, $content = '') {
    extract(shortcode_atts(array(
        'width'  => '480',
        'height' => '400',
    ), $atts));

    $width  = (isset($_GET['width']) && intval($_GET['width'])) ? intval($_GET['width']) : $width; // 用于 JSON 接口
    $height = round($width / 4 * 3);

    if (preg_match('#http://www.tudou.com/programs/view/(.*?)#i', $content, $matches)) {
        return '<iframe class="wpjam_video" width=' . esc_attr($width) . ' height=' . esc_attr($height) . ' src="http://www.tudou.com/programs/view/html5embed.action?code=' . esc_attr($matches[1]) . '" frameborder=0 allowfullscreen></iframe>';
    }
});

add_shortcode('sohutv', function ($atts, $content = '') {
    extract(shortcode_atts(array(
        'width'  => '510',
        'height' => '498',
    ), $atts));

    $width  = (isset($_GET['width']) && intval($_GET['width'])) ? intval($_GET['width']) : $width; // 用于 JSON 接口
    $height = round($width / 4 * 3);

    if (preg_match('#http://tv.sohu.com/upload/static/share/share_play.html\#(.+)#i', $content, $matches)) {
        //var_dump($matches);exit();
        return '<iframe class="wpjam_video" height=' . esc_attr($height) . ' width=' . esc_attr($width) . ' src="http://tv.sohu.com/upload/static/share/share_play.html#' . esc_attr($matches[1]) . '" frameborder=0 allowfullscreen></iframe>';
    }
});

//付费可见短代码
function gdk_pay_nologin($atts, $content = '')
{
    extract(shortcode_atts(array('money' => '1'), $atts));
    $pid = get_the_ID(); //文章ID
    add_post_meta($pid, '_pay_content', $content, true) or update_post_meta($pid, '_pay_content', $content); //没有新建,有就更新
    if (current_user_can('administrator')) {
        return '<fieldset class="fieldset"><legend class="legend">隐藏内容</legend><p>' . $content . '</p></fieldset>';
    } //admin show
    $pay_log   = get_post_meta($pid, 'pay_log', true); //购买记录数据
    $pay_arr   = explode(",", $pay_log);
    $pay_count = count($pay_arr); //已购买人数
    $notice    = '<fieldset id="hide_notice" class="fieldset ta-center"><legend class="legend ta-left">付费内容</legend>';
    $notice .= '<p>当前隐藏内容需要支付</p><span class="cm-coin">' . $money . '元</span>';
    $notice .= '<p>已有<span class="red">' . $pay_count . '</span>人支付</p>';
    $notice .= '<p><button id="pay_view" type="button" data-action="pay_view" data-money="' . $money . '" data-id="' . $pid . '">立即查看</button></p>';
    $notice .= '</fieldset>';
    return $notice;
}
add_shortcode('pax', 'gdk_pay_nologin');

//WordPress 段代码按钮集合
function gdk_shortcode_list()
{
    $wpshortcodes = [
        '横线'      => '<hr />',
        'H2标题'    => '<h2> </h2>',
        'H3标题'    => '<h3> </h3>',
        '记号笔'     => '<mark> </mark>',
        '链接按钮'    => '[dm href=] [/dm]',
        '下载按钮'    => '[dl href=] [/dl]',
        '透明按钮'    => '[lhb href=] [/lhb]',
        '视频按钮'    => '[video play=0] [/video]',
        '音频按钮'    => '[audio play=0] [/audio]',
        '绿色通知'    => '[v_notice]

[/v_notice]',
        '红色警告'    => '[v_error]

[/v_error]',
        '黄色错误'    => '[v_warn]

[/v_warn]',
        '蓝色提示'    => '[v_blue]

[/v_blue]',
        '默认提示'    => '[v_tips]

[/v_tips]',
        '隐藏收缩'    => '[collapse title=\'\']

[/collapse]',
        '回复可见'    => '[reply]

[/reply]',
        '登陆可见'    => '[vip]

[/vip]',
        '微信验证码可见' => '[wxcaptcha]

[/wxcaptcha]',
        '积分购买可见'  => '[pay point=\'10\']这里是需要付费的内容[/pay]',
        '游客付费可见'  => '[pax money=1]

[/pax]',
        '弹窗下载'    => '[fanctdl filename=\'这里填写文件名\' filepass=\'这里填写文件密码什么的\' href=\'这里填写的主下载链接\' filedown=\'这里填写的是文件的主下载名称\']这里填写的文件的辅助下载链接，可写多个,空格间隔[/fanctdl]',
        '面板下载'    => '[dltable file=\'在此处写下文件名称\' pass=\'在这里写下文件密码\']这里填写的文件的辅助下载链接，可写多个,空格间隔[/dltable]',
        '单页下载'    => '[pdownload title=]',
        '文章内链'    => '[neilian ids=]',
        '无序列表'    => '[list]

[/list]',
        '表格简码'    => '[table]

[/table]',
    ];
    $output = '';
    foreach ($wpshortcodes as $name => $alt) {
        $output .= '<a class="add-shortcode ed_button button button-small" data-shortcodes="' . $alt . '">' . $name . '</a>';
    }
    return $output;
}

function gdk_shortcode_button($context)
{
    $context = '<a class="button insert-shortcodes" title="添加简码" data-editor="content" href="javascript:;"><span class="dashicons dashicons-twitter shortcodes-icon"></span>短代码</a><div class="shortcodes-wrap">' . gdk_shortcode_list() . '</div>';
    return $context;
}
add_action('media_buttons_context', 'gdk_shortcode_button');

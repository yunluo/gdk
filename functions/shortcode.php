<?php

//短代码集合 



//添加钮Download
function DownloadUrl($atts, $content = null) {
    extract(shortcode_atts(array(
        "href" => 'http://'
    ) , $atts));
    return '<a class="dl" href="' . $href . '" target="_blank" rel="nofollow"><i class="fa fa-cloud-download"></i>' . $content . '</a>';
}
add_shortcode("dl", "DownloadUrl");
//添加钮git
function GithubUrl($atts, $content = null) {
    extract(shortcode_atts(array(
        "href" => 'http://'
    ) , $atts));
    return '<a class="dl" href="' . $href . '" target="_blank" rel="nofollow"><i class="fa fa-github-alt"></i>' . $content . '</a>';
}
add_shortcode('gt', 'GithubUrl');
//添加钮Demo
function DemoUrl($atts, $content = null) {
    extract(shortcode_atts(array(
        "href" => 'http://'
    ) , $atts));
    return '<a class="dl" href="' . $href . '" target="_blank" rel="nofollow"><i class="fa fa-external-link"></i>' . $content . '</a>';
}
add_shortcode('dm', 'DemoUrl');
//使用短代码添加回复后可见内容开始
function reply_to_read($atts, $content = null) {
    extract(shortcode_atts(array(
        "notice" => '<blockquote><center><p class="reply-to-read" style="color: blue;">注意：本段内容须成功“<a href="' . get_permalink() . '#respond" title="回复本文">回复本文</a>”后“<a href="javascript:window.location.reload();" title="刷新本页">刷新本页</a>”方可查看！</p></center></blockquote>'
    ) , $atts));
    $email = null;
    $user_ID = get_current_user_id();
    if ($user_ID > 0) {
        $email = get_user_by('id', $user_ID)->user_email;
        //对博主直接显示内容
        $admin_email = get_bloginfo('admin_email');
        if ($email == $admin_email) {
            return $content;
        }
    } else if (isset($_COOKIE['comment_author_email_' . COOKIEHASH])) {
        $email = str_replace('%40', '@', $_COOKIE['comment_author_email_' . COOKIEHASH]);
    } else {
        return $notice;
    }
    if (empty($email)) {
        return $notice;
    }
    global $wpdb;
    $post_id = get_the_ID();
    $query = "SELECT `comment_ID` FROM {$wpdb->comments} WHERE `comment_post_ID`={$post_id} and `comment_approved`='1' and `comment_author_email`='{$email}' LIMIT 1";
    if ($wpdb->get_results($query)) {
        return do_shortcode($content);
    } else {
        return $notice;
    }
}
add_shortcode('reply', 'reply_to_read');

//绿色提醒框
function toz($atts, $content = null) {
    return '<div id="sc_notice">' . $content . '</div>';
}
add_shortcode('v_notice', 'toz');
//红色提醒框
function toa($atts, $content = null) {
    return '<div id="sc_error">' . $content . '</div>';
}
add_shortcode('v_error', 'toa');
//黄色提醒框
function toc($atts, $content = null) {
    return '<div id="sc_warn">' . $content . '</div>';
}
add_shortcode('v_warn', 'toc');
//灰色提醒框
function tob($atts, $content = null) {
    return '<div id="sc_tips">' . $content . '</div>';
}
add_shortcode('v_tips', 'tob');
//蓝色提醒框
function tod($atts, $content = null) {
    return '<div id="sc_blue">' . $content . '</div>';
}
add_shortcode('v_blue', 'tod');
//蓝边文本框
function toe($atts, $content = null) {
    return '<div  class="sc_act">' . $content . '</div>';
}
add_shortcode('v_act', 'toe');

//灵魂按钮
function tom($atts, $content = null) {
    extract(shortcode_atts(array(
        "href" => 'http://'
    ) , $atts));
    return '<a class="lhb" href="' . $href . '" target="_blank" rel="nofollow">' . $content . '</a>';
}
add_shortcode('lhb', 'tom');
//添加视频按钮
function too($atts, $content = null) {
    extract(shortcode_atts(array(
        "play" => '0'
    ) , $atts));
    if ($play == 0) {
        return '<video style="width:100%;" src="' . $content . '" controls preload >您的浏览器不支持HTML5的 video 标签，无法为您播放！</video>';
    }
    if ($play == 1) {
        return '<video style="width:100%;" src="' . $content . '" controls preload autoplay >您的浏览器不支持HTML5的 video 标签，无法为您播放！</video>';
    }
}
add_shortcode('video', 'too');
//添加音频按钮
function tkk($atts, $content = null) {
    extract(shortcode_atts(array(
        "play" => '0'
    ) , $atts));
    if ($play == 0) {
        return '<audio style="width:100%;" src="' . $content . '" controls loop>您的浏览器不支持 audio 标签。</audio>';
    }
    if ($play == 1) {
        return '<audio style="width:100%;" src="' . $content . '" controls autoplay loop>您的浏览器不支持 audio 标签。</audio>';
    }
}
add_shortcode('audio', 'tkk');
//弹窗下载
function ton($atts, $content = null) {
    extract(shortcode_atts(array(
        "href" => 'http://',
        "filename" => '',
        "filesize" => '',
        "filedown" => ''
    ) , $atts));
    return '<a class="lhb" id="showdiv" href="#fancydlbox" >文件下载</a><div id="fancydlbox" style="cursor:default;display:none;width:800px;"><div class="part" style="padding:20px 0;"><h2>下载声明:</h2> <div class="fancydlads" align="left"><p>' . git_get_option('git_fancydlcp') . '</p></div></div><div class="part" style="padding:20px 0;"><h2>文件信息：</h2> <div class="dlnotice" align="left"><p>文件名称：' . $filename . '<br />文件大小：' . $filesize . '<br />发布日期：' . get_the_modified_time('Y年n月j日') . '</p></div></div><div class="part" id="download_button_part"><a id="download_button" target="_blank" href="' . $href . '"><span></span>' . $filedown . '</a> </div><div class="part" style="padding:20px 0;"><div class="moredl" style="text-align:center;">[更多地址] : ' . $content . '</div></div><div class="dlfooter">' . git_get_option('git_fancydlad') . '</div></div>';
}
add_shortcode('fanctdl', 'ton');
//代码演示短代码
function git_demo($atts, $content = null) {
    return '<a class="lhb" href="' . get_permalink(git_page_id('demo')) . '?pid=' . get_the_ID() . '" target="_blank" rel="nofollow">' . $content . '</a>';
}
add_shortcode('demo', 'git_demo');
//下载单页短代码
function git_download($atts, $content = null) {
    return '<a class="lhb" href="' . get_permalink(git_page_id('download')) . '?pid=' . get_the_ID() . '" target="_blank" rel="nofollow">' . $content . '</a>';
}
add_shortcode('download', 'git_download');

//为WordPress添加展开收缩功能
function xcollapse($atts, $content = null) {
    extract(shortcode_atts(array(
        "title" => ""
    ) , $atts));
    return '<div style="margin: 0.5em 0;"><div class="xControl"><a href="javascript:void(0)" class="collapseButton xButton"><i class="fa fa-plus-square" ></i> ' . $title . '</a><div style="clear: both;"></div></div><div class="xContent" style="display: none;">' . $content . '</div></div>';
}
add_shortcode('collapse', 'xcollapse');
//简单的下载面板
function xdltable($atts, $content = null) {
    extract(shortcode_atts(array(
        "file" => "",
        "size" => ""
    ) , $atts));
    return '<table class="dltable"><tbody><tr><td style="background-color:#F9F9F9;" rowspan="3"><p>文件下载</p></td><td><i class="fa fa-list-alt"></i>&nbsp;&nbsp;文件名称：' . $file . '</td><td><i class="fa fa-th-large"></i>&nbsp;&nbsp;文件大小：' . $size . '</td></tr><tr><td colspan="2"><i class="fa fa-volume-up"></i>&nbsp;&nbsp;下载声明：' . git_get_option('git_dltable_b') . '</td></tr><tr><td colspan="2"><i class="fa fa-download"></i>&nbsp;&nbsp;下载地址：' . $content . '</td></tr></tbody></table>';
}
add_shortcode('dltable', 'xdltable');
//网易云音乐
function music163($atts, $content = null) {
    extract(shortcode_atts(array(
        "play" => "1"
    ) , $atts));
    return '<iframe style="width:100%;max-height:86px;" frameborder="no" border="0" marginwidth="0" marginheight="0" src="http://music.163.com/outchain/player?type=2&id=' . $content . '&auto=' . $play . '&height=66"></iframe>';
}
add_shortcode('netmusic', 'music163');
//登录可见
function login_to_read($atts, $content = null) {
    $logina = '<a target="_blank" href="' . esc_url(wp_login_url(get_permalink())) . '">登录</a>';
    extract(shortcode_atts(array(
        "notice" => '<blockquote><center><p class="reply-to-read" style="color: blue;">注意：本段内容须“' . $logina . '”后方可查看！</p></center></blockquote>'
    ) , $atts));
    if (is_user_logged_in() && !is_null($content) && !is_feed()) {
        return '<div class="e-secret"><fieldset><legend>隐藏的内容</legend>
	' . $content . '<div class="clear"></div></fieldset></div>';
    }
    return $notice;
}
add_shortcode('vip', 'login_to_read');
// 部分内容输入密码可见
function e_secret($atts, $content = null) {
    if (!isset($_COOKIE['weixin_fensi']) && isset($_POST['e_secret_key']) && $_POST['e_secret_key'] == git_get_option('git_mp_code')) {
        setcookie('weixin_fensi', 10086, time() + 2592000, COOKIEPATH, COOKIE_DOMAIN, false); //30天时间
        return '<script type="text/javascript">window.location = document.referrer;</script>';
    }
    extract(shortcode_atts(array(
        'wx' => null
    ) , $atts));
    if ($_COOKIE['weixin_fensi'] == '10086' || strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return '<div class="e-secret"><fieldset><legend>隐藏的内容</legend>
	' . $content . '<div class="clear"></div></fieldset></div>';
    } else {
        if ($wx == '1') {
            return '<div class="wxbox"><img class="wxpic" src="' . git_get_option('git_mp_qr') . '" alt="' . git_get_option('git_mp_name') . '" title="' . git_get_option('git_mp_name') . '" align="right"><form method="post" name="e-secret" action="' . $_SERVER["REQUEST_URI"] . '"><span class="yzts" style="font-size:18px;">验证码：</span><input name="e_secret_key" id="verifycode" value="" type="text"><input id="verifybtn" name="" value="提交查看" type="submit"></form><div class="wxtips">' . git_get_option('git_mp_tips') . '</div><div class="cl"></div></div>';
        } else {
            return '<form class="e-secret" method="post" name="e-secret" action="' . $_SERVER["REQUEST_URI"] . '"><label>输入密码查看加密内容：</label><input type="password" name="e_secret_key" class="euc-y-i" maxlength="50"><input type="submit" class="euc-y-s" value="确定"><div class="euc-clear"></div></form>';
        }
    }
}
add_shortcode('secret', 'e_secret');

// 支持文章和页面运行PHP代码
function php_include($attr) {
    $file = $attr['file'];
    $upload_dir = wp_upload_dir();
    $folder = $upload_dir['basedir'] . '/php-content' . "/{$file}.php";
    ob_start();
    include $folder;
    return ob_get_clean();
}
add_shortcode('phpcode', 'php_include');

//给文章加内链短代码
function git_insert_posts($atts, $content = null) {
    extract(shortcode_atts(array(
        'ids' => ''
    ) , $atts));
    global $post;
    $content = '';
    $postids = explode(',', $ids);
    $inset_posts = get_posts(array(
        'post__in' => $postids
    ));
    foreach ($inset_posts as $key => $post) {
        setup_postdata($post);
        $content.= '<div class="neilian"><div class="fll"><a target="_blank" href="' . get_permalink() . '" class="fll linkss"><i class="fa fa-link fa-fw"></i>  ';
        $content.= get_the_title();
        $content.= '</a><p class="note">';
        $content.= get_the_excerpt();
        $content.= '</p></div><div class="frr"><a target="_blank" href="' . get_permalink() . '"><img src=';
        $content.= link_the_thumbnail_src();
        $content.= ' class="neilian-thumb"></a></div></div>';
    }
    wp_reset_postdata();
    return $content;
}
add_shortcode('neilian', 'git_insert_posts');


//快速插入列表
function git_list_shortcode_handler($atts, $content = '') {
    $lists = explode("\n", $content);
    $ouput = '';
    foreach ($lists as $li) {
        if (trim($li) != '') {
            $output.= "<li>{$li}</li>";
        }
    }
    $output = "<ul>" . $output . "</ul>\n";
    return $output;
}
add_shortcode('list', 'git_list_shortcode_handler');


//表格短代码
function table_shortcode_handler( $atts, $content='' ) {
    extract( shortcode_atts( ['width' => '100%'], $atts ) );
    $output = '';
    $content = trim($content);
    $trs = explode("\r\n", $content);
    $ths = explode("  ", $trs[0]);//表头数组
    $output .= '<thead><tr>';
    //var_dump($ths);
    foreach($ths as $th){
        $output .= '<th>'.$th.'</th>';
    }
    $output .= '</tr></thead>';
    $output .= '<tbody>';
    unset($trs[0]);
    foreach($trs as $tr){
        $tr = trim($tr);
        if($tr){
            $tds = explode("  ", $tr);
            $output .= '<tr>';
            foreach($tds as $td){
                $td = trim($td);
                if($td){
                    $output .= '<td>'.$td.'</td>';
                }
            }
            $output .= '</tr>';
        }
    }
    $output .= '</tbody>';
    $width = ' width="'.$width.'"';
    $output = '<table class="gdk-table"'.$width.' >'.$output.'</table>';

    return $output;
}
add_shortcode( 'table', 'table_shortcode_handler' );

//WordPress 段代码按钮集合
function gdk_shortcode_list() {
    $wpshortcodes = [
        '横线'=>'<hr />',
        'H2标题'=>'<h2> </h2>',
        'H3标题'=>'<h3> </h3>',
        '记号笔'=>'<mark> </mark>',
        '链接按钮'=>'[dm href=] [/dm]',
        '下载按钮'=>'[dl href=] [/dl]',
        '透明按钮'=>'[lhb href=] [/lhb]',
        '视频按钮'=>'[video play=0] [/video]',
        '音频按钮'=>'[audio play=0] [/audio]',
        '绿色通知'=>'[v_notice] [/v_notice]',
        '红色警告'=>'[v_error] [/v_error]',
        '黄色错误'=>'[v_warn] [/v_warn]',
        '灰色提示'=>'[v_tips] [/v_tips]',
        '蓝边提示'=>'[v_act] [/v_act]',
        '隐藏收缩'=>'[collapse title=\'\'] [/collapse]',
        '回复可见'=>'[reply] [/reply]',
        '登陆可见'=>'[vip] [/vip]',
        '密码可见'=>'[secret wx=0] [/secret]',
        '积分购买可见'=>'[pay point=\'10\']这里是需要付费的内容[/pay]',
        '游客付费可见'=>'[pax money=1]',
        '弹窗下载'=>'[fanctdl filename=\'这里填写文件名\' filepass=\'这里填写文件密码什么的\' href=\'这里填写的主下载链接\' filedown=\'这里填写的是文件的主下载名称\']这里填写的文件的辅助下载链接，可写多个,空格间隔[/fanctdl]',
        '面板下载'=>'[dltable file=\'在此处写下文件名称\' pass=\'在这里写下文件密码\']这里填写的文件的辅助下载链接，可写多个,空格间隔[/dltable]',
        '单页下载'=>'[pdownload title=]',
        '文章内链'=>'[neilian ids=]',
        '无序列表'=>'[list] [/list]',
        '表格简码'=>'[table] [/table]'
    ];
    $output = '';
    foreach ($wpshortcodes as $name => $alt) {
        $output.= '<a class="add-shortcode ed_button button button-small" data-shortcodes="' . $alt . '">' . $name . '</a>';
    }
    return $output;
}

function gdk_shortcode_button($context) {
    $context = '<a id="insert-shortcode-button" style="position:relative" class="button insert-shortcodes add_shortcodes" title="添加简码" data-editor="content" href="javascript:void(0)">短代码</a><div class="shortcodes-wrap">' . gdk_shortcode_list() . '</div>';
    return $context;
}
add_action('media_buttons_context', 'gdk_shortcode_button');
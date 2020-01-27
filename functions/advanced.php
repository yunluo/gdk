<?php

//头像解决方案
function gdk_switch_get_avatar( $avatar ) {
	switch (gdk_option('gdk_switch_get_avatar')) {
		case 1:
		  $avatarsrc = 'https://cdn.jsdelivr.net/gh/yunluo/GitCafeApi/avatar/' . mt_rand(1, 1999) . '.jpg';
		$avatar = "<img src=$avatar src class='avatar rand_avatar photo' />";
		break;
		case 2:
		  $avatar = preg_replace("/http[s]{0,1}:\/\/(secure|www|\d).gravatar.com\/avatar\//","//cdn.v2ex.com/gravatar/",$avatar);
		break;
		default:
		  $avatar = preg_replace("/http[s]{0,1}:\/\/(secure|www|\d).gravatar.com\/avatar\//","//dn-qiniu-avatar.qbox.me/avatar/",$avatar);
	}
	return $avatar;
}
add_filter('get_avatar', 'gdk_switch_get_avatar');

//fancybox图片灯箱效果
function fancybox($content) {
    $pattern = "/<a(.*?)href=('|\")([^>]*).(bmp|gif|jpeg|jpg|png|swf)('|\")(.*?)>(.*?)<\\/a>/i";
    $replacement = '<a$1href=$2$3.$4$5 rel="box" class="fancybox"$6>$7</a>';
    $content = preg_replace($pattern, $replacement, $content);
    return $content;
}
add_filter('the_content', 'fancybox');

//邮箱SMTP设置
if (gdk_option('gdk_smtp')) {
function gdk_smtp( $phpmailer ) {
	$phpmailer->FromName = gdk_option('gdk_smtp_mail'); //邮箱地址
	$phpmailer->Host = gdk_option('gdk_smtp_host');//服务器地址
	$phpmailer->Port = gdk_option('gdk_smtp_port'); //端口
	$phpmailer->Username = gdk_option('gdk_smtp_username'); //昵称
	$phpmailer->Password = gdk_option('gdk_smtp_password'); //密码
	$phpmailer->From = gdk_option('gdk_smtp_mail'); //邮箱地址
	$phpmailer->SMTPAuth = true; 
	$phpmailer->SMTPSecure = 'ssl';
	$phpmailer->IsSMTP();
}
add_action('phpmailer_init', 'gdk_smtp');
}

//七牛CDN
if (gdk_option('gdk_cdn')) {
    add_action('wp_loaded', 'gdk_cdn_start');
    function gdk_cdn_start() {
        ob_start('gdk_cdn_replace');
    }
    function gdk_cdn_replace($html) {
        $local_host = home_url(); //博客域名
        $qiniu_host = gdk_option('git_cdnurl_b'); //七牛域名
        $cdn_exts = gdk_option('git_cdnurl_format'); //扩展名（使用|分隔）
        $cdn_dirs = gdk_option('git_cdnurl_dir'); //目录（使用|分隔）
        $cdn_dirs = str_replace('-', '\-', $cdn_dirs);
        if ($cdn_dirs) {
            $regex = '/' . str_replace('/', '\/', $local_host) . '\/((' . $cdn_dirs . ')\/[^\s\?\\\'\"\;\>\<]{1,}.(' . $cdn_exts . '))([\"\\\'\s\?]{1})/';
            $html = preg_replace($regex, $qiniu_host . '/$1$4', $html);
        } else {
            $regex = '/' . str_replace('/', '\/', $local_host) . '\/([^\s\?\\\'\"\;\>\<]{1,}.(' . $cdn_exts . '))([\"\\\'\s\?]{1})/';
            $html = preg_replace($regex, $qiniu_host . '/$1$3', $html);
        }
        return $html;
    }
}

//CDN水印
if (gdk_option('git_cdn_water')) {
    function cdn_water($content){
        if (get_post_type() == 'post') {
            $pattern = "/<img(.*?)src=('|\")(.*?).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>/i";
            $replacement = '<img$1src=$2$3.$4!water.jpg$5$6>';
            $content = preg_replace($pattern, $replacement, $content);
        }
        return $content;
    }
    add_filter('the_content', 'cdn_water');
}

//自动替换媒体库图片的域名
if (is_admin() && gdk_option('git_cdnurl_b') && gdk_option('git_adminqn_b')) {
    function attachment_replace($text) {
        $replace = array(
             home_url()  => gdk_option('git_cdnurl_b')
        );
        $text = str_replace(array_keys($replace) , $replace, $text);
        return $text;
    }
    add_filter('wp_get_attachment_url', 'attachment_replace');
}

//一个简单可重复使用的邮件模板
function mail_temp($mail_title,$mail_cotent,$link,$link_title){
	?>
	<div style="width:500px;margin:auto">
    <div style="background:#2695f3;color:#FFF;padding:20px 10px;"><?php echo $mail_title;?></div>
    <div style="padding:10px;margin:5px;border-bottom:dashed 1px #ddd;"><?php echo $mail_cotent;?></div>
    <a href="<?php echo $link;?>" style="display:block;margin:auto;margin-top:40px;padding:10px;width:107px;outline:0;border:1px solid #2695f3;border-radius:25px;color:#2695f3;text-align:center;font-weight:700;font-size:14pxtext-decoration:none;" rel="noopener" target="_blank"><?php echo $link_title;?></a>
    <br><br>
    <div style="color:#cecece;font-size: 12px;">本邮件为系统自动发送，请勿回复。<br>
    如果不想被此类邮件打扰,请前往 <a style="color: #cecece;" href="<?php echo home_url();?>" rel="noopener" target="_blank"><?php echo get_option('blogname');?></a> 留言说明,由我们来操作处理。
    </div>
</div>
	<?php
}
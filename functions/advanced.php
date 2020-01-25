<?php

//头像解决方案
function gdk_switch_get_avatar( $avatar ) {
	switch (gdk_get_option('gdk_switch_get_avatar')) {
		case 1:
		  $avatarsrc = 'https://cdn.jsdelivr.net/gh/yunluo/GitCafeApi/avatar/' . mt_rand(1, 1999) . '.jpg';
		$avatar = "<img src=$avatarsrc class='avatar rand_avatar photo' />";
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



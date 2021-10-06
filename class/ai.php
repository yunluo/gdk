<?php

include 'ai/AipNlp.php'; //文本分析接口
//include 'ai/AipContentCensor.php'; //评论审核接口





function gdk_newsSummary($post_ID){

// 你的 APPID AK SK
$APP_ID = '22837593';
$API_KEY = 'hYYE9TWp0T5Um5b0p1FUALlw';
$SECRET_KEY = 'otXMPD6TGGWKjKnfm9aB9USErsQrH2HD';

$Nlpclient = new AipNlp($APP_ID, $API_KEY, $SECRET_KEY);
$post = get_post( $post_ID );
	$content = '从官网上面看统信操作系统，目前分为三个版本，第一种是统信操作系统专业版，然后是统信操作系统个人版，然后是统信操作系统社区版，也就是现在的深度系统，这里面要注意一点，由于深度系统本身已经非常有知名度，而且在海外也有一定的知名度，所以统信操作系统社区并没有改名，仍然使用深度系统名字。
在三个版本区别当中，很明显专业版是提供给党政军等机构使用，个人版可以给个人使用的，然后社区板和个人版差不多，但是权限要比个人版要更多也更自由一点，其中专业版是需要付费的，然后个人版是有增值服务的，然后社区版是完全免费的。
这里面有一点要说明一下个人版是没有root权限的，如果需要root权限，还要开通开发者权限，开发者权限需要注册的，然后社区版是默认自带root权限的。没有root的权限，对于很多linux系统爱好者来说是不太友好的，但是对于个人用户来说，这个却是非常重要的，因为自带root权限对一些对电脑不太熟悉的人来说实在是太危险了，很容易把电脑或者系统搞坏的。';


$maxSummaryLen = 300;

// 带参数调用新闻摘要接口
$result = $Nlpclient->newsSummary($content, $maxSummaryLen);

var_dump($result);


$my_post = array(
	'ID' => $post_ID,
'post_excerpt' => $result //摘要信息

);


//入库

//wp_insert_post( $my_post );

}
add_action('publish_post', 'gdk_newsSummary', 0);
add_action('update_post', 'gdk_newsSummary', 0);
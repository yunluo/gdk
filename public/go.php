<?php

add_action('query_vars', 'gdk_go_add_query_vars');

add_action('template_redirect', 'gdk_go_redirect');

function gdk_go_add_query_vars($public_query_vars){
		$public_query_vars[] = 'go';
    return $public_query_vars;
}

function gdk_go_redirect(){
	global $wp,$wp_query;
	$b =  $wp_query->query_vars['go']??'';
	if ($b){
		gdk_go_action();
    exit;
	}
}


function gdk_go_action(){
$t_url = preg_replace('/^go=(.*)$/i','$1',$_SERVER["QUERY_STRING"]);
if(!empty($t_url)) {
	preg_match('/(http|https):\/\//',$t_url,$matches);
	if($matches){
		$url=$t_url;
		$title='页面加载中,请稍候...';
	} else {
		$title='加载中...';
		echo "<script>setTimeout(function(){window.opener=null;window.close();}, 2000);</script>";
	}
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="refresh" content="1;url='<?php echo $url;?>';">
<title><?php echo $title;?></title>
<style type="text/css">body{background:#f5f7fa;margin:0}.loader{animation:fadein 2s;position:absolute;top:0;left:0;right:0;bottom:0;background-color:#f5f7fa}@keyframes fadein{0%{opacity:0}to{opacity:1}}.loader-inner{position:absolute;z-index:300;top:40%;left:50%;transform:translate(-50%,-50%)}@keyframes rotate_pacman_half_up{0%{transform:rotate(270deg)}50%{transform:rotate(360deg)}to{transform:rotate(270deg)}}@keyframes rotate_pacman_half_down{0%{transform:rotate(90deg)}50%{transform:rotate(0)}to{transform:rotate(90deg)}}@keyframes pacman-balls{75%{opacity:.7}to{transform:translate(-100px,-6.25px)}}.pacman>div:nth-child(2){animation:pacman-balls 1s 0s infinite linear}.pacman>div:nth-child(3){animation:pacman-balls 1s .33s infinite linear}.pacman>div:nth-child(4){animation:pacman-balls 1s .66s infinite linear}.pacman>div:nth-child(5){animation:pacman-balls 1s .99s infinite linear}.pacman>div:first-of-type{animation:rotate_pacman_half_up .5s 0s infinite}.pacman>div:first-of-type,.pacman>div:nth-child(2){width:0;height:0;border-right:25px solid transparent;border-top:25px solid #7a57d1;border-left:25px solid #7a57d1;border-bottom:25px solid #7a57d1;border-radius:25px}.pacman>div:nth-child(2){animation:rotate_pacman_half_down .5s 0s infinite;margin-top:-50px}.pacman>div:nth-child(3),.pacman>div:nth-child(4),.pacman>div:nth-child(5),.pacman>div:nth-child(6){background-color:#7a57d1;width:15px;height:15px;border-radius:100%;margin:2px;width:10px;height:10px;position:absolute;transform:translate(0,-6.25px);top:25px;left:75pt}.loader-text{margin:20px 0 0 -1pc;display:block;font-size:18px}</style>
</head>
<body>
<div class="loader">
<div class="loader-inner pacman">
<div></div><div></div><div></div><div></div><div></div> 
<span class="loader-text">页面跳转中, 请稍候…</span>
</div>
</div>
</body>
</html>
<?php
}
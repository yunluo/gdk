<?php

if (gdk_option('gdk_cdn'))                  add_action('wp_loaded', 'gdk_cdn_start');//七牛CDN
if (gdk_option('gdk_link_go'))     add_filter('the_content','gdk_link_go',999);// 外链GO跳转
if (gdk_option('gdk_smtp'))         add_action('phpmailer_init', 'gdk_smtp');//SMTP
if (gdk_option('gdk_cdn_water'))     add_filter('the_content', 'gdk_cdn_water');//CDN水印



//文章首尾添加自定义内容
function gdk_add_content($content) {
	$before = gdk_option('gdk_artical_top');
	$after = gdk_option('gdk_artical_bottom');
	$content = $before.'<br>'.$content.'<br>'.$after;
	return $content;
}
add_filter('the_content', 'gdk_add_content');

//头像解决方案
function gdk_switch_get_avatar( $avatar ) {
	switch (gdk_option('gdk_switch_get_avatar')) {
		case 1:
		  $rand_avatar = 'https://cdn.jsdelivr.net/gh/yunluo/GitCafeApi/avatar/' . mt_rand(1, 1999) . '.jpg';
		$avatar = "<img src=\"$rand_avatar\" class='avatar rand_avatar photo' />";
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

//懒加载
function lazyload($content) {
	if (!is_feed() || !is_robots()) {
		$content = preg_replace('/<img(.+)src=[\'"]([^\'"]+)[\'"](.*)>/i', "<img\$1data-original=\"\$2\" \$3>\n<noscript>\$0</noscript>", $content);
	}
	return $content;
}
add_filter('the_content', 'lazyload');
//fancybox图片灯箱效果
function gdk_fancybox($content) {
	$pattern = "/<a(.*?)href=('|\")([^>]*).(bmp|gif|jpeg|jpg|png|swf)('|\")(.*?)>(.*?)<\\/a>/i";
	$replacement = '<a$1href=$2$3.$4$5 data-fancybox="gallery" rel="box" class="fancybox"$6>$7</a>';
	$content = preg_replace($pattern, $replacement, $content);
	return $content;
}
add_filter('the_content', 'gdk_fancybox');

//GO跳转
function gdk_link_go($content) {
	if(file_exists(ABSPATH.'go.php')) {
		$gourl = home_url().'/go.php';
	} else {
		$gourl = GDK_BASE_URL.'class/go.php';
	}
	preg_match_all('/<a(.*?)href="(.*?)"(.*?)>/',$content,$matches);
	if($matches) {
		foreach($matches[2] as $val) {
			if(in_string($val,'://') && !in_string($val,home_url()) && !preg_match('/\.(jpg|jepg|png|ico|bmp|gif|tiff)/i',$val) && !preg_match('/(ed2k|thunder|Flashget|flashget|qqdl):\/\//i',$val)) {
				$content=str_replace("href=\"$val\"", "href=\"".$gourl."?url=$val\" ",$content);
			}
		}
	}
	return $content;
}

//邮箱SMTP设置
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

// CDN
function gdk_cdn_start() {
	ob_start('gdk_cdn_replace');
}
function gdk_cdn_replace($html) {
	$local_host = home_url();//博客域名
	$cdn_host = gdk_option('gdk_cdn_host');//cdn域名
	$cdn_exts = gdk_option('gdk_cdn_ext');//扩展名（使用|分隔）
	$cdn_dirs = gdk_option('gdk_cdn_dir');//目录（使用|分隔）
	$cdn_dirs = str_replace('-', '\-', $cdn_dirs);
	if ($cdn_dirs) {
		$regex = '/' . str_replace('/', '\/', $local_host) . '\/((' . $cdn_dirs . ')\/[^\s\?\\\'\"\;\>\<]{1,}.(' . $cdn_exts . '))([\"\\\'\s\?]{1})/';
            $html = preg_replace($regex, $cdn_host . '/$1$4', $html);
        } else {
            $regex = '/' . str_replace('/', '\/', $local_host) . '\/([^\s\?\\\'\"\;\>\<]{1,}.(' . $cdn_exts . '))([\"\\\'\s\?]{1})/';
            $html = preg_replace($regex, $cdn_host . '/$1$3', $html);
        }
        return $html;
    }


//CDN水印
function gdk_cdn_water($content) {
	if (get_post_type() == 'post') {
		$pattern = "/<img(.*?)src=('|\")(.*?).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>/i";
		$replacement = '<img$1src=$2$3.$4!water.jpg$5$6>';
		$content = preg_replace($pattern, $replacement, $content);
	}
	return $content;
}


//自动替换媒体库图片的域名
if (is_admin() && gdk_option('gdk_cdn')) {
    function attachment_replace($text) {
        $replace = array(
             home_url()  => gdk_option('gdk_cdn')
        );
        $text = str_replace(array_keys($replace) , $replace, $text);
        return $text;
    }
    add_filter('wp_get_attachment_url', 'attachment_replace');
}



//压缩html代码
if (git_get_option('git_compress')) {
    function wp_compress_html(){
        function wp_compress_html_main($buffer){
            if ( substr( ltrim( $buffer ), 0, 5) == '<?xml' ) return $buffer;
            $initial = strlen($buffer);
            $buffer = explode("<!--wp-compress-html-->", $buffer);
            $count = count($buffer);
            for ($i = 0; $i <= $count; $i++) {
                if (stristr($buffer[$i], '<!--wp-compress-html no compression-->')) {
                    $buffer[$i] = str_replace("<!--wp-compress-html no compression-->", " ", $buffer[$i]);
                } else {
                    $buffer[$i] = str_replace("\t", " ", $buffer[$i]);
                    $buffer[$i] = str_replace("\n\n", "\n", $buffer[$i]);
                    $buffer[$i] = str_replace("\n", "", $buffer[$i]);
                    $buffer[$i] = str_replace("\r", "", $buffer[$i]);
                    while (stristr($buffer[$i], '  ')) {
                        $buffer[$i] = str_replace("  ", " ", $buffer[$i]);
                    }
                }
                $buffer_out .= $buffer[$i];
            }
            $final = strlen($buffer_out);
            if ($initial !== 0) {
                $savings = ($initial - $final) / $initial * 100;
            } else {
                $savings = 0;
            }
            $savings = round($savings, 2);
            $buffer_out .= "\n<!--压缩前的大小: {$initial} bytes; 压缩后的大小: {$final} bytes; 节约：{$savings}% -->";
            return $buffer_out;
        }
            ob_start("wp_compress_html_main");
    }
    add_action('get_header', 'wp_compress_html');
    function git_unCompress($content)
    {
        if (preg_match_all('/(crayon-|<?xml|script|textarea|<\\/pre>)/i', $content, $matches)) {
            $content = '<!--wp-compress-html--><!--wp-compress-html no compression-->' . $content;
            $content .= '<!--wp-compress-html no compression--><!--wp-compress-html-->';
        }
        return $content;
    }
    add_filter('the_content', 'git_unCompress');
}

<?php

add_action('query_vars', 'gdk_edl_add_query_vars');

add_action('template_redirect', 'gdk_edl_redirect');

function gdk_edl_add_query_vars($public_query_vars){
		$public_query_vars[] = 'dl';
    return $public_query_vars;
}

function gdk_edl_redirect(){
	global $wp,$wp_query;
	$b =  $wp_query->query_vars['dl']?? '';
	if ($b){
		gdk_edl();
    exit;
	}
}

//前端界面
function gdk_edl() {
header('Content-type: text/html; charset=utf-8');
$pid = isset( $_GET['dl'] ) ? trim(htmlspecialchars($_GET['dl'], ENT_QUOTES)) : '';
if( !$pid ) {
	wp_die('<h1>下载页面不是直接打开的哦</h1>');
}
$link = get_permalink( $pid );
$title = get_the_title($pid);
$download_name = get_post_meta( $pid, 'gdk_download_name', true );
$download_size = get_post_meta( $pid, 'gdk_download_size', true );
$download_link = get_post_meta( $pid, 'gdk_download_link', true );
if(empty($download_name)) $download_name = '不知名文件';
if(empty($download_size)) $download_size = '未知大小';
if(empty($download_link)) wp_die('不填写文件下载链接是不可以的哦');

?>
<!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<!-- Favicons -->
		<title><?php echo $title;?> - <?php echo get_bloginfo('name');?>下载</title>
		<!-- Social tags -->
		<meta name="keywords" content="<?php echo $title;?>">
		<meta name="description" content="<?php echo $title;?>">
		<!-- Documentation extras -->
		<style>[tooltip]{position:relative}[tooltip]:after,[tooltip]:before{text-transform:none;font-size:.9em;line-height:1;user-select:none;pointer-events:none;position:absolute;display:none;opacity:0}[tooltip]:before{content:'';border:5px solid transparent;z-index:1001}[tooltip]:after{content:attr(tooltip);font-family:Helvetica,sans-serif;text-align:center;min-width:3em;max-width:21em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;padding:1ch 1.5ch;border-radius:.3ch;box-shadow:0 1em 2em -.5em rgba(0,0,0,.35);background:#333;color:#fff;z-index:1000}[tooltip]:hover:after,[tooltip]:hover:before{display:block}[tooltip='']:after,[tooltip='']:before{display:none!important}[tooltip]:not([flow]):before,[tooltip][flow^=up]:before{bottom:100%;border-bottom-width:0;border-top-color:#333}[tooltip]:not([flow]):after,[tooltip][flow^=up]:after{bottom:calc(100% + 5px)}[tooltip]:not([flow]):after,[tooltip]:not([flow]):before,[tooltip][flow^=up]:after,[tooltip][flow^=up]:before{left:50%;transform:translate(-50%,-.5em)}@keyframes tooltips-vert{to{opacity:.9;transform:translate(-50%,0)}}@keyframes tooltips-horz{to{opacity:.9;transform:translate(0,-50%)}}[tooltip]:not([flow]):hover:after,[tooltip]:not([flow]):hover:before,[tooltip][flow^=up]:hover:after,[tooltip][flow^=up]:hover:before{animation:tooltips-vert .3s ease-out forwards}
        .navbar{display:none}footer,nav{display:block}body{margin:0;background-color:#fafafa;color:#212529;text-align:left;font-weight:400;font-size:1rem;line-height:1.5}h2,h3{margin-top:0;margin-bottom:.5rem;color:inherit;font-weight:400;line-height:1.2}.blockquote-footer{display:block;color:#6c757d;font-size:80%}.blockquote-footer:before{content:"\2014 \00A0"}.container{margin-right:auto;margin-left:auto;padding-right:15px;padding-left:15px;width:100%}@media (min-width:576px){.container{max-width:540px}}@media (min-width:768px){.container{max-width:45pc}}@media (min-width:992px){.container{max-width:60pc}}@media (min-width:1200px){.container{max-width:855pt}}.row{display:flex;margin-right:-15px;margin-left:-15px;flex-wrap:wrap}.col-md-12,.col-md-5{position:relative;padding-right:15px;padding-left:15px;width:100%;min-height:1px}@media (min-width:768px){.col-md-5{max-width:41.666667%;flex:0 0 41.666667%}.col-md-12{max-width:100%;flex:0 0 100%}}.navbar{position:relative;padding:.5rem 1rem}.navbar,.navbar>.container{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between}.navbar-brand{display:inline-block;margin-right:1rem;padding-top:.3125rem;padding-bottom:.3125rem;white-space:nowrap;font-size:1.25rem;line-height:inherit}.navbar-brand:focus,.navbar-brand:hover{text-decoration:none}@media (max-width:991.98px){.navbar-expand-lg>.container{padding-right:0;padding-left:0}}@media (min-width:992px){.navbar-expand-lg{flex-flow:row;justify-content:flex-start}.navbar-expand-lg>.container{flex-wrap:nowrap}}.card-body{padding:1.25rem;flex:1 1 auto}.card-title{margin-bottom:.75rem}.card-header{margin-bottom:0;padding:.75rem 1.25rem;border-bottom:1px solid rgba(0,0,0,.12);background-color:#fff}.card-header:first-child{border-radius:calc(.25rem - 1px) calc(.25rem - 1px) 0 0}.list-group{margin-bottom:0;padding-left:0}.list-group-item{position:relative;display:block;margin-bottom:0;padding:.75rem 1.25rem;border:0 solid rgba(0,0,0,.125);background-color:inherit}.list-group-item:first-child{border-top-right-radius:0;border-top-left-radius:0}.list-group-item:last-child{margin-bottom:0;border-bottom-right-radius:0;border-bottom-left-radius:0}.list-group-item:focus,.list-group-item:hover{z-index:1;text-decoration:none}.list-group-item:disabled{background-color:inherit;color:#6c757d}.fixed-top{top:0;position:fixed;right:0;left:0;z-index:1030}.mr-auto{margin-right:auto!important}.ml-auto{margin-left:auto!important}.text-center{text-align:center!important}.list-group{display:flex;padding:.5rem 0;flex-direction:column;flex-grow:1}.list-group-item{display:flex;padding:1rem;line-height:1;flex-flow:row wrap;align-items:center}.list-group-item:first-child{margin-right:2rem}.navbar{margin-bottom:20px;padding:.625rem 0;border:0;border-radius:3px;background-color:#fff!important;box-shadow:0 4px 18px 0 rgba(0,0,0,.12),0 7px 10px -5px rgba(0,0,0,.15);color:#555}.navbar.fixed-top{border-radius:0}.navbar .navbar-brand{position:relative;padding:.625rem 0;height:50px;color:inherit;font-size:1.125rem;line-height:30px}.navbar>.container{flex:1}.card-title{font-weight:700;font-family:Roboto Slab,Times New Roman,serif;color:#3c4858;text-decoration:none}.card-nav-tabs{margin-top:45px}.card-nav-tabs .card-header{margin-top:-30px!important}.page-header{display:flex;margin:0;padding:0;height:100vh;border:0;background-position:50%;background-size:cover;align-items:center}.page-header>.container{color:#fff}.header-filter{position:relative}.header-filter:after,.header-filter:before{position:absolute;top:0;left:0;z-index:1;display:block;width:100%;height:100%;content:""}.header-filter:before{background:rgba(0,0,0,.5)}.header-filter .container{position:relative;z-index:2}footer{display:flex;padding:.9375rem 0;text-align:center}footer ul{margin-bottom:0;padding:0;list-style:none}footer ul li{display:inline-block}footer ul li a{position:relative;display:block;padding:.9375rem;border-radius:3px;color:inherit;text-transform:uppercase;font-weight:500;font-size:9pt}footer ul li a,footer ul li a:hover{text-decoration:none}footer .pull-center{float:none;display:inline-block}.card{margin-top:30px;margin-bottom:30px;width:100%;border:0;border-radius:6px;background:#fff;box-shadow:0 2px 2px 0 rgba(0,0,0,.14),0 3px 1px -2px rgba(0,0,0,.2),0 1px 5px 0 rgba(0,0,0,.12);color:rgba(0,0,0,.87)}.card .card-title{margin-top:.625rem}.card .card-body{padding:.9375rem 1.875rem}.card .card-header{margin-top:-30px;margin-right:15px;margin-left:15px;padding:1rem 15px;border:0;border-radius:3px;background:linear-gradient(60deg,#eee,#bdbdbd)}.card .card-header-primary{background:linear-gradient(60deg,#ab47bc,#7b1fa2);box-shadow:0 5px 20px 0 rgba(0,0,0,.2),0 13px 24px -11px rgba(156,39,176,.6)}.card [class*=header-]{color:#fff}.signup-page .page-header{display:inherit;height:auto;min-height:100vh}.signup-page .page-header .container{padding-top:20vh}.signup-page .footer .container{padding:0}.signup-page .footer a{color:#fff}@media (max-width:991px){[class*=navbar-expand-]>.container{padding-right:15px;padding-left:15px}.navbar .navbar-translate{position:relative;display:flex;width:100%;transition:transform .5s cubic-bezier(.685,.0473,.346,1);-ms-flex-pack:justify!important;justify-content:space-between!important;-ms-flex-align:center;align-items:center}}*{font-family:Microsoft YaHei}ul li{list-style-type:none}.card-header-up{margin-bottom:-25px;text-align:center}.card-up{margin-right:5px!important;margin-left:5px!important;padding-right:5px!important;padding-left:10px!important}#important{margin-top:-3rem}.ml-up{padding-right:10px;padding-left:10px}.item-title{font-size:.85rem}.file-name{font-size:1.5rem}.card-body-up{margin-bottom:60px}.blockquote-footer{margin-top:-50px}.cloud-item{padding-left:30px}.navbar-brand{color:#9c27b0!important}.post-title{color:#fafafa!important}.card-header{margin-bottom:25px}.card .card-header{margin-right:0!important;margin-left:0!important;border-bottom-right-radius:0!important;border-bottom-left-radius:0!important}.cloud-item li{display:inline-block;margin:25px 0}.dlinks{position:relative;margin-right:15px;padding:.5rem 1.5rem;border:1px solid;border-color:#2196f3;border-radius:2px;background:#2196f3;color:#fcfcfc!important;text-decoration:none!important;font-size:14px;cursor:pointer}a,a:hover,a:visited{color:#fafafa;text-decoration:none}
      </style>
	</head>
	<body class="signup-page ">
    <nav class="navbar nav-open-absolute fixed-top navbar-expand-lg " color-on-scroll="100" id="sectionsNav" style="color: #3e3947;">
            <div class="container">
	            <div class="navbar-translate">
		            <a class="navbar-brand" href="./"><?php echo get_bloginfo('name');?></a>
	            </div>
            </div>
        </nav>

		<div class="page-header header-filter" filter-color="purple" style="background-image: url(<?php echo get_bing_img();?>); background-size: cover; background-position: top center;">
			<div class="container" >
				<div class="row" id="important">
					<div class="col-md-12 ml-auto mr-auto ml-up">
					    <div class="card card-nav-tabs">
						    <div class="card-header card-header-primary card-header-up">
							    <h2><a href="<?php echo $link;?>" class="post-title"><?php echo $title;?></a></h2>
						    </div>
						    <div class="card-body card-up">
								    <h3 class="card-title text-center file-name"><?php echo $download_name;?></h3>
								    <div class="card-body card-body-up" id="card-main">
									    <div class="row">
										    <div class="col-md-5 ml-auto">
												<ul class="list-group" >
													<li class="list-group-item">
														<span class="item-title">文件大小：<span>
														<?php echo $download_size;?>
													</li>
												</ul>
										    </div>

										    <div class="col-md-5 mr-auto">
											    <ul class="list-group">
												    <li class="list-group-item">
													    <span class="item-title">更新日期：</span>
														<?php echo get_post($pid)->post_modified;?>
												    </li>
											    </ul>
										    </div>
                                            <div class="list-group">
													    <ul class="cloud-item ">
                                                        <?php
                                                            $download_link = trim($download_link);
                                                            $links = explode("\n", $download_link);
                                                            foreach ($links as $link) {
                                                                $download_link = trim($link);
	                                                            $link = explode(",", $link);
	                                                            echo '<li><a class="dlinks item-title" href="' . esc_url(trim($link[0])) . '"target="_blank" rel="nofollow" tooltip="' . esc_attr(trim($link[2])) . '">' . trim($link[1]) . '</a></li>';
                                                            }
                                                        ?>
													    </ul>
									    </div></div>
								    </div>
							    <footer class="blockquote-footer"> Silence is gold.</footer>
						    </div>
					    </div>
				    </div>
			    </div>
			</div>
			<footer class="footer b-footer">
				<div class="container">
					<nav class="pull-center">
						<ul>
							<li>
								<a href="<?php echo home_url();?>" target="_blank"><?php echo get_bloginfo('name');?></a>
							</li>
						</ul>
					</nav>
				</div>
			</footer>
		</div>
        <?php echo apply_filters( 'gdk_filter_download_page_code', '' );?>
	</body>
</html>
<?php }


//页脚


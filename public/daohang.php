<?php
/**
 * 导航页面
 */

function gdk_daohang_html_api_handlers($template)
{
    $hook = explode('-', get_query_var('daohang'));
    if (isset($hook[0]) && $hook[0] === 'gdkk') {

        if (isset($hook[1])) {
            status_header(404);
            header('HTTP/1.0 404 Not Found');
            $GLOBALS['wp_query']->set_404();
            include get_query_template('404');
            exit;
        }
        $daohang = get_transient('gdk-daohang-html');

        if (false === $daohang || empty($daohang)) {
            $daohang = gdk_create_html_daohang();
            set_transient('gdk-daohang-html', $daohang);
        }
        $daohang;
        return;
    }

    return $template;
}

add_filter('template_include', 'gdk_daohang_html_api_handlers', 99);

//导航单页函数
function gdk_get_the_link_items($id = null)
{
    $bookmarks = get_bookmarks('orderby=date&category=' . $id);
    $output    = '';
    if (!empty($bookmarks)) {
        foreach ($bookmarks as $bookmark) {/* $bookmark->link_description */
		$icon = $bookmark->link_image ?? 'https://img12.360buyimg.com/ddimg/jfs/t1/130599/4/13000/527/5f8ef17aE60de6b3e/4da08e59f8e12dec.png';
		
            $output .= '<div class="xs-6 sm-6 md-4 lg-3">
            <div class="card"><a class="card-heading link-tooltip bg-lvs' . mt_rand(1,25) . '" href="' . $bookmark->link_url . '" target="_blank"><span class="card-icon"><img src="' . $icon . '"></span><span class="card-title">' . $bookmark->link_name . '</span></a><div class="card-body"> ' . $bookmark->link_notes . '</div></div></div>';
        }
    }
    return $output;
}

function gdk_get_link_items()
{   
    $linkid = gdk_link_id();
    $linkcats = get_terms('link_category', 'orderby=count&hide_empty=1&exclude=' . $linkid );
    $result   = '';
    foreach ($linkcats as $linkcat) {
        $result .= '<a id="' . $linkcat->term_id . '"></a><div class="panel">
            <div class="panel-title card"><img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzODQgNTEyIj4gPHBhdGggZD0iTTMzNiAwSDQ4QzIxLjQ5IDAgMCAyMS40OSAwIDQ4djQ2NGwxOTItMTEyIDE5MiAxMTJWNDhjMC0yNi41MS0yMS40OS00OC00OC00OHptMCA0MjguNDNsLTE0NC04NC0xNDQgODRWNTRhNiA2IDAgMCAxIDYtNmgyNzZjMy4zMTQgMCA2IDIuNjgzIDYgNS45OTZWNDI4LjQzeiI+PC9wYXRoPiA8L3N2Zz4=">' . $linkcat->name . '</div>
            <div class="panel-body">
                <div class="row">';
        $result .= gdk_get_the_link_items($linkcat->term_id);
        $result .= '</div></div></div>';
    }
    return $result;
}

function gdk_create_html_daohang()
{
    ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>网址导航 - <?php echo get_bloginfo('name'); ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="X-UA-COMPATIBLE" content="IE=edge">
    <style type="text/css">
@keyframes fadeIn{0%{opacity:0;-webkit-animation-timing-function:cubic-bezier(0.3,0,0,1);animation-timing-function:cubic-bezier(0.3,0,0,1)}76.92%{opacity:1;-webkit-animation-timing-function:linear;animation-timing-function:linear}100%{opacity:1}}@keyframes fadeOut{0%{opacity:1;-webkit-animation-timing-function:cubic-bezier(0.3,0,0,1);animation-timing-function:cubic-bezier(0.3,0,0,1)}76.92%{opacity:0.5;-webkit-animation-timing-function:linear;animation-timing-function:linear}100%{opacity:0}}*{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}*:before,*:after{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}html{font-size:10px;-webkit-tap-highlight-color:transparent}body{font-family:Helvetica,Arial,"PingFang SC","Microsoft YaHei","WenQuanYi Micro Hei","tohoma,sans-serif";font-size:14px;line-height:1.42857143;color:#333;background-color:#fff;margin:0;padding:0}@media screen and (max-width:767px){body{background-color:#f9f9f9;padding-right:0 !important}}input,button,select,textarea{font-family:inherit;font-size:inherit;line-height:inherit;-webkit-appearance:none;box-shadow:none}::-moz-selection{background:#111;color:#fff}::selection{background:#111;color:#fff}a{text-decoration:none}a:hover{color:#3d83ff}::-webkit-scrollbar{width:4px;height:4px;background-color:#222}::-webkit-scrollbar-track{-webkit-box-shadow:inset 0 0 6px rgba(0,0,0,0.3);background-color:#222}::-webkit-scrollbar-thumb{width:2px;background-color:#888}.container{padding-right:10px;padding-left:10px;margin-right:auto;margin-left:auto}.container:after,.row:after,.row-mobile:after{clear:both;content:"";display:table}@media (min-width:768px){.container{width:750px}}@media (min-width:992px){.container{width:970px}}@media screen and (max-width:767px){.hidden-xs{display:none}}.container-fluid{padding-right:10px;padding-left:10px;margin-right:auto;margin-left:auto}.row{margin-right:-10px;margin-left:-10px}.row-mobile{margin-right:-10px;margin-left:-10px}@media screen and (max-width:767px){.row-mobile{margin-left:0;margin-right:0}}.md-3-5,.md-8-5,.xs-1,.sm-1,.md-1,.lg-1,.xs-2,.sm-2,.md-2,.lg-2,.xs-3,.sm-3,.md-3,.lg-3,.xs-4,.sm-4,.md-4,.lg-4,.xs-5,.sm-5,.md-5,.lg-5,.xs-6,.sm-6,.md-6,.lg-6,.xs-7,.sm-7,.md-7,.lg-7,.xs-8,.sm-8,.md-8,.lg-8,.xs-9,.sm-9,.md-9,.lg-9,.xs-10,.sm-10,.md-10,.lg-10,.xs-11,.sm-11,.md-11,.lg-11,.xs-12,.sm-12,.md-12,.lg-12{position:relative;min-height:1px;padding-right:10px;padding-left:10px}.xs-1,.xs-2,.xs-3,.xs-4,.xs-5,.xs-6,.xs-7,.xs-8,.xs-9,.xs-10,.xs-11,.xs-12{float:left}.xs-12{width:100%}.xs-11{width:91.66666667%}.xs-10{width:83.33333333%}.xs-9{width:75%}.xs-8{width:66.66666667%}.xs-7{width:58.33333333%}.xs-6{width:50%}.xs-5{width:41.66666667%}.xs-4{width:33.33333333%}.xs-3{width:25%}.xs-2{width:16.66666667%}.xs-1{width:8.33333333%}@media (min-width:768px){.sm-1,.sm-2,.sm-3,.sm-4,.sm-5,.sm-6,.sm-7,.sm-8,.sm-9,.sm-10,.sm-11,.sm-12{float:left}.sm-12{width:100%}.sm-11{width:91.66666667%}.sm-10{width:83.33333333%}.sm-9{width:75%}.sm-8{width:66.66666667%}.sm-7{width:58.33333333%}.sm-6{width:50%}.sm-5{width:41.66666667%}.sm-4{width:33.33333333%}.sm-3{width:25%}.sm-2{width:16.66666667%}.sm-1{width:8.33333333%}}@media (min-width:992px){.md-3-5,.md-8-5,.md-1,.md-2,.md-3,.md-4,.md-5,.md-6,.md-7,.md-8,.md-9,.md-10,.md-11,.md-12{float:left}.md-12{width:100%}.md-11{width:91.66666667%}.md-10{width:83.33333333%}.md-9{width:75%}.md-8-5{width:70.833333333%}.md-8{width:66.66666667%}.md-7{width:58.33333333%}.md-6{width:50%}.md-5{width:41.66666667%}.md-4{width:33.33333333%}.md-3-5{width:29.16666666%}.md-3{width:25%}.md-2{width:16.66666667%}.md-1{width:8.33333333%}.md-offset-12{margin-left:100%}.md-offset-11{margin-left:91.66666667%}.md-offset-10{margin-left:83.33333333%}.md-offset-9{margin-left:75%}.md-offset-8{margin-left:66.66666667%}.md-offset-7{margin-left:58.33333333%}.md-offset-6{margin-left:50%}.md-offset-5{margin-left:41.66666667%}.md-offset-4{margin-left:33.33333333%}.md-offset-3{margin-left:25%}.md-offset-2{margin-left:16.66666667%}.md-offset-1{margin-left:8.33333333%}.md-offset-0{margin-left:0}}@media (min-width:1300px){.lg-1,.lg-2,.lg-3,.lg-4,.lg-5,.lg-6,.lg-7,.lg-8,.lg-9,.lg-10,.lg-11,.lg-12{float:left}.lg-12{width:100%}.lg-11{width:91.66666667%}.lg-10{width:83.33333333%}.lg-9{width:75%}.lg-8{width:66.66666667%}.lg-7{width:58.33333333%}.lg-6{width:50%}.lg-5{width:41.66666667%}.lg-4{width:33.33333333%}.lg-3{width:25%}.lg-2{width:16.66666667%}.lg-1{width:8.33333333%}.lg-pull-12{right:100%}.lg-pull-11{right:91.66666667%}.lg-pull-10{right:83.33333333%}.lg-pull-9{right:75%}.lg-pull-8{right:66.66666667%}.lg-pull-7{right:58.33333333%}.lg-pull-6{right:50%}.lg-pull-5{right:41.66666667%}.lg-pull-4{right:33.33333333%}.lg-pull-3{right:25%}.lg-pull-2{right:16.66666667%}.lg-pull-1{right:8.33333333%}.lg-pull-0{right:auto}.lg-push-12{left:100%}.lg-push-11{left:91.66666667%}.lg-push-10{left:83.33333333%}.lg-push-9{left:75%}.lg-push-8{left:66.66666667%}.lg-push-7{left:58.33333333%}.lg-push-6{left:50%}.lg-push-5{left:41.66666667%}.lg-push-4{left:33.33333333%}.lg-push-3{left:25%}.lg-push-2{left:16.66666667%}.lg-push-1{left:8.33333333%}.lg-push-0{left:auto}.lg-offset-12{margin-left:100%}.lg-offset-11{margin-left:91.66666667%}.lg-offset-10{margin-left:83.33333333%}.lg-offset-9{margin-left:75%}.lg-offset-8{margin-left:66.66666667%}.lg-offset-7{margin-left:58.33333333%}.lg-offset-6{margin-left:50%}.lg-offset-5{margin-left:41.66666667%}.lg-offset-4{margin-left:33.33333333%}.lg-offset-3{margin-left:25%}.lg-offset-2{margin-left:16.66666667%}.lg-offset-1{margin-left:8.33333333%}.lg-offset-0{margin-left:0}}.board{box-shadow:0px 0px 6px #e3e3e3;background:#fff}.card{box-shadow:0px 2px 0px rgba(170,170,170,0.1);background:#fff;margin-bottom:20px;border-radius:3px;margin-left:2px;margin-right:2px}.card .card-heading{overflow:hidden;margin-bottom:7px;display:block;cursor:pointer;padding:10px 18px 0;color:#333}.card .card-heading:hover{color:#3d83ff}.card .card-heading .card-icon{width:32px;height:32px;float:left;display:block}.card .card-heading .card-icon img{display:block;width:100%;height:100%}.card .card-heading .card-title{display:block;padding-left:40px;margin-top:5px;font-weight:700;font-size:15px;color:white}.card .card-body{cursor: pointer;color:#666666;font-size:12px;text-overflow:ellipsis;overflow:hidden;margin-bottom:2px;padding: 8px 6px;}.card .card-footer{padding:0 18px 8px}.card .card-footer table{width:100%}.card .card-footer table td{text-align:center;cursor:pointer;color:#bbb}.card .card-footer table .done{color:#ff1677}.card .card-footer table .btn-view{text-align:left;font-size:12px;color:#bbb}.card .card-footer table .btn-view a{color:#bbb}.panel-body .card:hover{box-shadow:0 4px 4px rgba(170,170,170,0.2)}.panel{background-color:#f9f9f9;border-radius:6px;padding:20px 20px;margin-bottom:40px}@media screen and (max-width:767px){.panel{padding:0}}.panel .panel-title{padding:8px 12px;display:inline-block;margin-bottom:16px;font-size:12px;font-weight:900;color:#666}.panel .panel-title .iconfont{font-weight:100}.panel .panel-body{overflow:hidden}.category-item{height:88px;display:flex;align-items:center;justify-content:center;color:#333}.category-item i{font-size:32px;margin-right:15px}.mobile-header-wrap{display:none;z-index:100;height:60px;position:fixed;top:0;left:0;right:0;background-color:#171717}@media screen and (max-width:767px){.mobile-header-wrap{display:block}}.mobile-header-wrap .mobile-logo{display:block;text-decoration:none;margin:10px auto;width:fit-content;}.mobile-header-wrap .mobile-logo img{width:auto;height:40px;display:block}.btn-mobile-sidenav{left:0;top:0;width:40px;height:100%;color:#ddd;cursor:pointer;display:none}@media screen and (max-width:767px){.btn-mobile-sidenav{display:block}}.nav-bar-animate span{opacity:1;-webkit-transform:rotate(45deg);transform:rotate(45deg)}.nav-bar-animate span:nth-last-child(3){opacity:1;-webkit-transform:rotate(-45deg);transform:rotate(-45deg)}.nav-bar-animate span:nth-last-child(2){opacity:0}.nav-bar{padding:24px 50px 40px 16px;position:fixed;z-index:1000}.nav-bar span{margin-left:auto;margin-right:auto;display:block;width:20px;height:1px;margin-bottom:6px;position:relative;background:#fff;border-radius:3px;z-index:1;-webkit-transform-origin:20px;transform-origin:20px;-webkit-transition:background 0.5s cubic-bezier(0.77,0.2,0.05,1),opacity 0.55s ease,-webkit-transform 0.5s cubic-bezier(0.77,0.2,0.05,1);transition:background 0.5s cubic-bezier(0.77,0.2,0.05,1),opacity 0.55s ease,-webkit-transform 0.5s cubic-bezier(0.77,0.2,0.05,1);transition:transform 0.5s cubic-bezier(0.77,0.2,0.05,1),background 0.5s cubic-bezier(0.77,0.2,0.05,1),opacity 0.55s ease;transition:transform 0.5s cubic-bezier(0.77,0.2,0.05,1),background 0.5s cubic-bezier(0.77,0.2,0.05,1),opacity 0.55s ease,-webkit-transform 0.5s cubic-bezier(0.77,0.2,0.05,1)}.hide-sidenav{transform:translate(-230px,0) !important}.show-sidenav{transform:translate(0,0) !important}.sidenav-mark{background:rgba(0,0,0,0.75);height:100%;left:0;position:fixed;top:60px;width:100%;-webkit-animation-name:fadeIn;animation-name:fadeIn;-webkit-animation-duration:530ms;animation-duration:530ms;-webkit-animation-timing-function:linear;animation-timing-function:linear;-webkit-animation-direction:normal;animation-direction:normal;-webkit-animation-delay:0s;animation-delay:0s;z-index:199}.sidenav{-webkit-overflow-scrolling:touch;width:230px;position:fixed;z-index:200;top:0;left:0;bottom:-100px;background-color:#171717;padding:31px 0;color:#ccc}@media screen and (min-width:768px) and (max-width:991px){.sidenav{width:180px}}@media screen and (max-width:767px){.sidenav{transform:translate(-230px,0);transition:transform 430ms cubic-bezier(0.3,0,0,1)}}.sidenav .btn-mobile-close{display:none;position:absolute;top:0px;padding-top:18px;padding-left:12px;width:180px;height:40px}@media screen and (max-width:767px){.sidenav .btn-mobile-close{display:block}}.sidenav .copyright{display:block;text-align:center;color:#686868;font-size:12px;padding-top:12px}.sidenav .copyright:hover{color:#fff}.sidenav .logo{position:relative;display:block;text-align:center}.sidenav .logo img{width:114px;display:block;margin:0 auto;z-index:2}.sidenav .logo img.christmas{position:absolute;z-index:3;top:-11px;left:76px;width:42px;height:auto}
@media screen and (min-width:768px) and (max-width:991px){.sidenav .logo img.christmas{top:-11px;left:51px}}.sidenav .site-description{color:#686868;text-align:center;margin-bottom:20px;font-size:12px}.sidenav .nav-item{border-top:1px solid #1f1f1f}.sidenav .nav-item:last-child{border-bottom:1px solid #2e2e2e}.sidenav .nav-item:hover{background-color:#fff}.sidenav .nav-item:hover a{color:#000}.sidenav .nav-item a{color:#fff;text-align:center;font-size:14px;font-weight:700;padding:7px;display:block}.sidenav .nav-item a .csz{display:block;text-align:center;font-size:20px;font-weight:200}.sidenav .nav-item a span{text-align:center;position:relative;top:-2px}.sidenav .nav-item.active{background-color:#fff}.sidenav .nav-item.active a{color:#000}
.sidenav .nav-tags{display:none;list-style:none;padding:0;overflow:scroll;overflow-x:hidden;margin:0}
.sidenav .nav-tags .active{background-color:#222}.sidenav .nav-tags .active a{color:#fff}.sidenav .nav-tags li{padding-right:0}.sidenav .nav-tags li:hover{background:#222}.sidenav .nav-tags li a{text-decoration:none;color:#686868;display:block;padding:9px;font-size:12px;text-align:center}.sidenav .nav-tags li a:hover{color:#fff}.sidenav .nav-tags li a .csz{font-size:16px;margin-right:8px}.sidenav-ad{font-size:12px;color:#686868;display:block;text-align:center;padding-top:12px;border-top:1px solid #1f1f1f}.sidenav-ad:hover{color:#fff}.tool{margin-bottom:20px}.tool .tool-img{margin-bottom:6px;height:106px;border-radius:4px;position:relative}.tool .tool-img img{display:block;width:auto;height:106px;margin:0 auto}.tool .tool-img .tool-platform{display:none;position:absolute;bottom:0;width:100%;height:30px;background:rgba(0,0,0,0.7);text-align:right;padding-right:10px;padding-top:6px}.tool .tool-img .tool-platform i{color:#fff;font-size:14px;margin-left:8px}.tool .tool-title{font-size:14px;font-weight:800;color:#171717}.tool .tool-body{font-size:12px;color:#bbb}@media screen and (max-width:767px){body{padding-right:0 !important}}.main-wrap{margin-left:230px}@media screen and (min-width:768px) and (max-width:991px){.main-wrap{margin-left:180px}}@media screen and (max-width:767px){.main-wrap{margin-left:0}}.friends-description{font-size:12px;color:#bbb;margin-top:4px;float:right}@media screen and (max-width:767px){.friends-description{display:none}}.friends-email{font-size:12px;color:#000;margin-top:4px;float:right}@media screen and (max-width:767px){.friends-email{display:none}}.main{padding-top:30px;margin:0 auto}@media screen and (min-width:1300px){.main{width:1050px}}@media screen and (min-width:992px) and (max-width:1299px){.main{width:760px}}@media screen and (min-width:768px) and (max-width:991px){.main{width:580px}}@media screen and (max-width:767px){.main{margin:0 10px;padding-top:68px}}.main .selected-nav{padding:16px 20px;border-radius:6px;margin-bottom:17px;height:55px;overflow:hidden}@media screen and (max-width:767px){.main .selected-nav{padding:16px 12px}}.main .selected-nav .nav-left{float:left;position:relative;z-index:10}.main .selected-nav .nav-left .selected-nav-cn{font-size:16px;font-weight:700;color:#666;margin-right:6px;display:inline-block}.main .selected-nav .nav-left .selected-nav-en{color:#bbb;display:inline-block}.main .selected-nav .selected-nav-link-wrapper{margin-right:74px;transform:translateY(-43px);line-height:22px}.main .selected-nav .selected-nav-link-wrapper .selected-nav-link-item{margin-bottom:20px;text-align:right}.main .selected-nav .selected-nav-link-wrapper .selected-nav-link-item .selected-nav-description,.main .selected-nav .selected-nav-link-wrapper .selected-nav-link-item .selected-nav-linkname{font-size:12px}@media screen and (max-width:767px){.main .selected-nav .selected-nav-link-wrapper .selected-nav-link-item .selected-nav-description,.main .selected-nav .selected-nav-link-wrapper .selected-nav-link-item .selected-nav-linkname{display:none}}.main .selected-nav .selected-nav-link-wrapper .selected-nav-link-item .selected-nav-description{color:#bbb;margin-right:6px;margin-top:5px}.main .selected-nav .selected-nav-link-wrapper .selected-nav-link-item .selected-nav-linkname{margin-top:5px}.main .selected-nav .selected-nav-link-wrapper .selected-nav-link-item .selected-nav-linkname a{color:#000}.main .selected-nav .selected-nav-link-wrapper .selected-nav-link-item .selected-nav-linkname a:hover{color:#3d83ff}.main .selected-nav .navigator{float:right;margin-top:-2px;margin-left:10px}/*@media screen and (max-width:767px){.main .selected-nav .navigator{display:none}*/}.main .selected-nav .navigator a{display:inline-block;border:1px solid #ececf5;height:27px;width:32px;text-align:center;transition:all 0.5s}.main .selected-nav .navigator a i{font-size:12px;font-weight:900;color:gray;line-height:27px}.main .selected-nav .navigator a:hover{background:#f2f2f2}.main .selected-nav .navigator a.up{border-radius:2px 0 0 2px}.main .selected-nav .navigator a.down{border-left:none;border-radius:0 2px 2px 0}.select-bar{list-style:none;padding:0;overflow:hidden}.select-bar ul{display:inline-block;float:right;box-shadow:0px 0px 6px #e3e3e3;background:#fff;margin-right:1px;border-radius:4px;padding:0;margin-top:0}.select-bar li{display:inline-block}.select-bar li:last-child a{border-bottom-right-radius:4px;border-top-right-radius:4px;border-bottom-left-radius:0;border-top-left-radius:0}.select-bar li a{border-bottom-left-radius:4px;border-top-left-radius:4px;display:inline-block;color:#171717;font-size:14px;padding:3px 10px}.select-bar li a.active{background-color:#171717;color:#fff}.page img{display:block;max-width:100%;height:auto;margin:12px auto}.page p{margin:8px 0}#link-tooltip{position:absolute;z-index:100;top:200px;left:300px;min-width:80px;max-width:220px;width:auto;font-size:15px;font-weight:600;padding:8px 12px;background:#171717;background:#eee;border-radius:4px;line-height:18px;color:#A1A7B7;display:none}@media screen and (max-width:767px){#link-tooltip{display:none !important}}#link-tooltip .tooltip-title{color:#171717;margin-bottom:4px}#link-tooltip .tooltip-content{color:#171717;font-size:13px;font-weight:100;white-space:nowrap;text-overflow:ellipsis;overflow:hidden}.footer{margin-top:38px;margin-bottom:18px;color:#bbb;font-size:12px;text-align:left}@media screen and (max-width:767px){.footer{text-align:center}
.card-body{ height:43px;}
}.footer a{margin-left:10px;color:#000}.footer a:hover{color:#3d83ff}@media screen and (max-width:767px){.footer .footer-link{display:block}}.footer .footer-top-border{width:70%;height:1px;margin:0 auto}.footer-at{overflow:hidden}.footer-at a{display:block}.footer-at a img{display:block;width:100%;height:auto}.bg-lvs1{background-color:#2ecc71}.bg-lvs2{background-color:#27ae60}.bg-lvs3{background-color:#3498db}.bg-lvs4{background-color:#2980b9}.bg-lvs5{background-color:#9b59b6}.bg-lvs6{background-color:#8e44ad}.bg-lvs7{background-color:#34495e}.bg-lvs8{background-color:#2c3e50}.bg-lvs9{background-color:#f1c40f}.bg-lvs10{background-color:#f39c12}.bg-lvs11{background-color:#2470a0}.bg-lvs12{background-color:#a696c8}.bg-lvs13{background-color:#060608}.bg-lvs14{background-color:#ff585d}.bg-lvs15{background-color:#9dd3a8}.bg-lvs16{background-color:#dd0a35}.bg-lvs17{background-color:#ff8a5c}.bg-lvs18{background-color:#f5587b}.bg-lvs19{background-color:#24a8ac}.bg-lvs20{background-color:#0087cb}.bg-lvs21{background-color:#ffa200}.bg-lvs22{background-color:#014955}.bg-lvs23{background-color:#08ffc8}.bg-lvs24{background-color:#204969}.bg-lvs25{background-color:#2d248a}.panel-title img{width:15px;margin:0 8px;}
.card-title{text-overflow: ellipsis;white-space: nowrap;}
    </style>
</head>
<body>
    <header class="mobile-header-wrap">
        <a class="mobile-logo" href="/"><img src="<?php echo GDK_BASE_URL ?>assets/img/logo.png" alt="logo"></a>
    </header>
    <div class="btn-mobile-sidenav">
        <div class="nav-bar">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <!-- sidenav -->
    <div class="sidenav">
        <a class="logo" href="">
            <img src="<?php echo GDK_BASE_URL; ?>assets/img/logo.png" alt="logo">
        </a>
        <br>
        <div class="site-description">
            <?php echo get_bloginfo('description', 'display'); ?>
        </div>

        <!-- tool -->
        <?php
$linkcats1 = get_terms('link_category', 'orderby=count&hide_empty=1&exclude='. gdk_link_id() );
    echo '<ul class="nav-tags">';
    foreach ($linkcats1 as $linkcat) {
        echo '<li><a href="#' . $linkcat->term_id . '">' . $linkcat->name . '</a></li>';
    }
    echo '</ul>';
    echo '<a class="copyright" href="/">&#169; ' . get_bloginfo('name') . '</a>
                </div>
                <div class="main-wrap">
                    <div class="main">';
    echo gdk_get_link_items();
    ?>
            <div class="footer-at row">
                <div class="xs-12 sm-12 md-6" style="margin-bottom: 10px;">
                    <a target="_blank" href="https://gitcafe.net">
                        极客公园
                    </a>
                </div>
                <div class="xs-12 sm-12 md-6">
                    <a target="_blank" href="https://vkey.yunluo.workers.dev/">
                        微力同步神KEY
                    </a>
                </div>
            </div>
            <footer class="footer">
                <div class="footer-top-border"></div>
                Copyright &#169; 2016-2022 网址导航 - <?php echo get_bloginfo('name'); ?>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/zepto@1.2.0/dist/zepto.min.js"></script>
    <script type="text/javascript">
        $(function () {
            /*
             *锚点点击跳转
             */
            var href = "";
            var pos = 0;
            $(".nav-tags a").click(function (e) {
                $(".nav-tags li").each(function () {
                    $(this).removeClass("active");
                });
                $(this).parent("li").addClass("active");
                e.preventDefault();
                href = $(this).attr("href");
                pos = $(href).position().top - 30;
                $("html,body").animate({ scrollTop: pos }, 500);
            });
            $(".nav-tags").css("display", "block");

            $(".btn-mobile-sidenav").click(function () {
                if ($(this).find(".nav-bar").hasClass("nav-bar-animate")) {
                    $(this).find(".nav-bar").removeClass("nav-bar-animate");
                    $(".sidenav").removeClass("show-sidenav").addClass("hide-sidenav");
                    $(".mobile-header-wrap .sidenav-mark").animate({
                        opacity: 0,
                    }, 500, function () {
                        $(this).remove();
                    });
                    $('body').css({
                        'overflow': 'auto'
                    });
                } else {
                    $(this).find(".nav-bar").addClass("nav-bar-animate");
                    $(".sidenav").addClass("show-sidenav").removeClass("hide-sidenav");
                    $(".mobile-header-wrap").append("<div class='sidenav-mark'></div>");
                    $('body').css({
                        'overflow': 'hidden'
                    });
                }
            });
        });
    </script>
</body>
</html>
<?php
}
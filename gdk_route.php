<?php
/*
Plugin Name: GitCafe Development Kit 极客公园开发套件
Plugin URI: https://gitcafe.net/
Description: 为WordPress主题开发提供底层支持
Version: 0.0.2
Author: 云落
Author URI: https://gitcafe.net/
Compatible:5.3.2
*/

//万能的开头
if ( ! defined( 'ABSPATH' ) ) { exit; }

//版本和兼容检查
function deactivate_myself() {
	deactivate_plugins( plugin_basename( __FILE__ ) );
	wp_die('启动失败，GDK插件不兼容Git主题且需要运行在 PHP 7.2 版本及更高的环境下。');
}

if (defined('GIT_URL')) {
	add_action('update_option_active_plugins', 'deactivate_myself');
}

if (!version_compare(PHP_VERSION, '7.2', '>=')) {
  add_action('update_option_active_plugins', 'deactivate_myself');
}

//定义各种常量
define('GDK_PLUGIN_VER', '0.0.1'); 
define('GDK_PLUGIN_FILE', __FILE__);//插件入口文件
define('GDK_BASE_URL', plugin_dir_url( __FILE__ ) );//插件目录url
define('GDK_ROOT_PATH', plugin_dir_path( __FILE__ ) );//插件目录路径

//加载各种资源
include('framework/frame_load.php');//加载后台框架
include('class/class_load.php');//加载各种类
include('functions/func_load.php');//加载函数
include('assets/assets_load.php');//加载静态资源

//激活插件之后创建必要页面
//register_activation_hook(__FILE__, 'gdk_plugin_activate');
<?php

/*

  	Plugin Name: GitCafe Development Kit 极客公园开发套件
	Plugin URI: https://www.nicetheme.cn/nicetheme-plugins-store.html
	Description: nicetheme 的一些基本操作
	Version: 0.3.5
	Author URI: http://www.nicetheme.cn
	Nicetheme Module: jimu
	Text Domain: jimu
	Compatible:

*/
if ( ! defined( 'ABSPATH' ) ) { exit; }

function deactivate_myself() {
	deactivate_plugins( plugin_basename( __FILE__ ) );
	wp_die('启动失败，积木插件需要运行在 PHP 7.2 版本及更高的环境下。');
}
if (!version_compare(PHP_VERSION, '7.2.26', '>=')) {
  add_action('update_option_active_plugins', 'deactivate_myself');
}

define('NC_STORE_VER', '0.3.5'); 
define('NC_STORE_FILE', __FILE__);//插件入口文件
define('NC_BASE_URL', plugin_dir_url( __FILE__ ) );//插件目录url
define('NC_STORE_ROOT_PATH', plugin_dir_path( __FILE__ ) );//插件目录路径
include('library/nc-base/kernel/kernel.php');
include('library/nc-base/static-load.php');
include('library/nc-base/add-menu-page.php');


if (version_compare(PHP_VERSION, '7.2.26', '>=')) {
	include('modules/field-group/Field_Group_Values.php');
	include('modules/field-group/get_all_custom_field_meta.php');
}

include('modules/base/main.php');

function nc_store_pluggable_include() {
	include( 'modules/pluggable/load.php' );
}
add_action( 'after_setup_theme', 'nc_store_pluggable_include', 99999 );

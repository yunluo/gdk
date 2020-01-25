<?php
/*
  	Plugin Name: NICETHEME 基础拓展
	Plugin URI: https://www.nicetheme.cn
	Description: WordPress 常用优化操作 & SEO
	Version: 1.0
	Compatible: Cosy|Grace|nicetheme|LivingCoral
	Nicetheme Module: OptimizeUp
    Author: PCDotFan
	Author URI: https://www.nicetheme.cn
*/

define( 'NC_OPTIMIZEUP_DIR', dirname(__FILE__) );
define( 'NC_OPTIMIZEUP_RELATIVE_DIR', NC_OPTIMIZEUP_DIR );
define( 'NC_OPTIMIZEUP_CONF_DIR', NC_OPTIMIZEUP_DIR . '/conf/' ); 
define( 'NC_OPTIMIZEUP_VERSION', '1.0' );
define( 'NC_OPTIMIZEUP__FILE__', __FILE__ );
define( 'NC_OPTIMIZEUP_URL', plugin_dir_url(__FILE__) );

add_action( 'plugins_loaded', 'nc_optimizeup_init' );
function nc_optimizeup_init() {
	// nc store check
	if( !defined('NC_STORE_ROOT_PATH') ){

		add_action( 'admin_notices', 'nc_optimizeup_init_check' );
		function nc_optimizeup_init_check(){
			$html = '<div class="notice notice-error">
				<p><b>错误：</b> 网站优化 积木 缺少依赖插件 <code>WP 积木</code> 请先安装并启用 <code>WP 积木</code> 插件。</p>
			</div>';
			echo $html;
		}

	} else {

		load_theme_textdomain('jimu', NC_OPTIMIZEUP_DIR . '/languages');

		acf_add_options_sub_page(
			array(
				'page_title'      => '网站优化 积木',
				'menu_title'      => '网站优化 积木',
				'menu_slug'       => 'nc-optimizeup-options',
				'parent_slug'     => 'nc-modules-store',
				'capability'      => 'manage_options',
				'update_button'   => '保存',
				'updated_message' => '设置已保存！'
			)
		);


		add_filter('nc_save_json_paths', 'nc_optimizeup_acf_json_save_point');

		function nc_optimizeup_acf_json_save_point( $path ) {

		    $path[] = NC_OPTIMIZEUP_DIR . '/conf';

		    return $path;

		}

		add_filter('acf/settings/load_json', 'nc_optimizeup_acf_json_load_point');

		function nc_optimizeup_acf_json_load_point( $paths ) {

		    $paths[] = NC_OPTIMIZEUP_DIR . '/conf';

		    return $paths;

		}
		
		function nc_optimizeup_enqueue_script() {
			wp_register_script('nicetheme-optimizeup', plugin_dir_url( __FILE__ ) . 'assets/main.js', array( 'jquery' ));
			wp_enqueue_script('nicetheme-optimizeup');
			wp_localize_script( 'nicetheme-optimizeup', 'nicetheme',
				array(
					"ajax_url" => admin_url("admin-ajax.php"),
				)
			);
		}
		add_action('admin_enqueue_scripts', 'nc_optimizeup_enqueue_script');

		function nc_optimizeup_enqueue_script_frontend() {
			if (!is_admin()) {
				wp_register_style( 'jimu-css', NC_BASE_URL . 'modules/jimu.css', array(), NC_OPTIMIZEUP_VERSION, 'all' );
				wp_register_script('jimu-js', NC_BASE_URL . 'modules/jimu.js', array('jquery'), NC_OPTIMIZEUP_VERSION, true);
				wp_enqueue_script('jimu-js');
				wp_enqueue_style( 'jimu-css' );
			}
		}
		add_action('wp_enqueue_scripts', 'nc_optimizeup_enqueue_script_frontend');

		function nc_set_main_option() { 
			$field_group_json = 'group_5beacfbc334a4.json';
			$option_config = json_decode(file_get_contents(NC_OPTIMIZEUP_CONF_DIR.$field_group_json), true); 
			$nc_option = get_all_custom_field_meta('option', $option_config);
			update_option('nc_option', $nc_option, true);
		} 
		add_action('acf/save_post', 'nc_set_main_option'); 

		$nc_option = get_option('nc_option');

		get_field('seo_switcher', 'option'); // trick
 
		if (false == $nc_option) nc_set_main_option();
		
		include_once NC_OPTIMIZEUP_DIR . '/functions/utils.php';
		include_once NC_OPTIMIZEUP_DIR . '/functions/rocket.php';
		include_once NC_OPTIMIZEUP_DIR . '/functions/usability.php';
		if ($nc_option['seo_switcher']) {
			include_once NC_OPTIMIZEUP_DIR . '/functions/seo.php';
		}
		if ($nc_option['smtp_switcher']) {
			include_once NC_OPTIMIZEUP_DIR . '/functions/smtp.php';
		}

		if ($nc_option['general']['anti_spam_switcher']) {
			include_once NC_OPTIMIZEUP_DIR . '/functions/anti-spam.php';
		}
		include_once NC_OPTIMIZEUP_DIR . '/functions/seo-extra.php';
		include_once NC_OPTIMIZEUP_DIR . '/functions/database.php';
		
		if( isset( $nc_option['open_cdn_replace'] ) && $nc_option['open_cdn_replace'] == 'enable' ){
			$GLOBALS['nc_cdn_replace_list'] = $nc_option['cdn_replace_list'];
			include_once NC_OPTIMIZEUP_DIR . '/functions/cdn.php';
		}

		if( isset( $nc_option['wp-admin-access-control'] ) && $nc_option['wp-admin-access-control'] == 'enable' ){
			$GLOBALS['wp-admin-access-group'] = $nc_option['wp-admin-access-group'];
			include_once NC_OPTIMIZEUP_DIR . '/functions/wp-admin-access-control.php';
		}

		function wp_admin_access_group_select( $field ) {
			global $wp_roles;
	
			$roles = $wp_roles->roles;
		
			$choices = [];

			foreach ($roles as $key => $value) {
				$choices[$key] = translate_user_role($value['name']);
			}

			$field['choices'] = $choices;
		  
			return $field;
		
		}
		add_action( 'acf/load_field/name=wp-admin-access-group', 'wp_admin_access_group_select', 10, 1 );

		if( isset( $nc_option['nice-security-access'] ) && $nc_option['nice-security-access'] == 'enable' ){
			$GLOBALS['nice-security-access-path'] = $nc_option['nice-security-access-path'];
			include_once NC_OPTIMIZEUP_DIR . '/functions/nice-security-access.php';
		}

	}
}



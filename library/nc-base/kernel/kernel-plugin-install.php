<?php

/*
            /$$            
    /$$    /$$$$            
   | $$   |_  $$    /$$$$$$$
 /$$$$$$$$  | $$   /$$_____/
|__  $$__/  | $$  |  $$$$$$ 
   | $$     | $$   \____  $$
   |__/    /$$$$$$ /$$$$$$$/
          |______/|_______/ 
================================
        Keep calm and get rich.
                    Is the best.

  	@Author: Dami
  	@Date:   2018-10-14 16:45:02
  	@Last Modified by:   Dami
  	@Last Modified time: 2019-09-13 11:48:31

*/
/**
 * plugin install
 */

function nc_store_plugin_install_program( $url ){

	include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' ); //for plugins_api..

	//includes necessary for Plugin_Upgrader and Plugin_Installer_Skin
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	include_once( ABSPATH . 'wp-admin/includes/file.php' );
	include_once( ABSPATH . 'wp-admin/includes/misc.php' );
	include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
	include('kernel-install-skin.php');

	remove_action( 'upgrader_process_complete', 'wp_update_plugins' );
	remove_action( 'upgrader_process_complete', 'wp_update_themes' );
	
	$upgrader = new Plugin_Upgrader( new Nc_Store_Skin() );

	if( $upgrader->install( $url, array( 'clear_update_cache' => false ) ) ){

		$plugin_file = $upgrader->plugin_info();

		return $plugin_file;

	}else{
		return false;
	}

}
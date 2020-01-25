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
  	@Date:   2018-10-14 19:10:55
  	@Last Modified by:   Dami
  	@Last Modified time: 2018-12-08 19:19:55

*/

/**
 * 积木激活
 */

function nc_store_plugin_activate(){


	if( isset( $_GET['page'] ) &&  'nc-modules-store' === $_GET['page'] && isset( $_GET['activate'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'nc-store-activate-plugin-'.urldecode( $_GET['activate'] ) ) ){

		if( !current_user_can('manage_options') ) die('蛤？');

		$plugin = urldecode( $_GET['activate'] );
		$result = activate_plugin( $plugin );

		if( !is_wp_error( $result ) ){

			new Nc_Store_Notice(
				'积木启用成功！', 
				array( 
					'notice-success', 
					'is-dismissible' 
				)
			);

		}else{

			new Nc_Store_Notice(
				'积木启用出现错误！', 
				array( 
					'notice-error', 
					'is-dismissible' 
				)
			);

		} 


	}


}
add_action( 'admin_init', 'nc_store_plugin_activate' );

/**
 * 积木停用
 */
function nc_store_plugin_deactivate(){

	if( isset( $_GET['page'] ) &&  'nc-modules-store' === $_GET['page'] && isset( $_GET['deactivate'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'nc-store-deactivate-plugin-'.urldecode( $_GET['deactivate'] ) ) ){

		if( !current_user_can('manage_options') ) die('蛤？');

		$plugin = urldecode( $_GET['deactivate'] );
		$result = deactivate_plugins( $plugin );

		if( !is_wp_error( $result ) ){

			new Nc_Store_Notice(
				'停用成功！', 
				array( 
					'notice-success', 
					'is-dismissible' 
				)
			);

		}else{

			new Nc_Store_Notice(
				'积木停用出现错误！', 
				array( 
					'notice-error', 
					'is-dismissible' 
				)
			);

		} 


	}

}
add_action( 'admin_init', 'nc_store_plugin_deactivate' );

/**
 * 删除积木
 */
function nc_store_delete_plugin(){


	if( isset( $_GET['page'] ) &&  'nc-modules-store' === $_GET['page'] && isset( $_GET['delete-module'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'nc-store-delete-module-'.urldecode( $_GET['delete-module'] ) ) ){

		if( !current_user_can('manage_options') ) die('蛤？');

		$plugin = array( urldecode( $_GET['delete-module'] ) );

		$plugin = array_filter($plugin, 'is_plugin_inactive');

		$delete_result = delete_plugins( $plugin );

		if( !is_wp_error( $delete_result ) ){

			new Nc_Store_Notice(
				'积木删除成功！', 
				array( 
					'notice-success', 
					'is-dismissible' 
				)
			);

		}else{

			new Nc_Store_Notice(
				'积木删除出现错误！', 
				array( 
					'notice-error', 
					'is-dismissible' 
				)
			);

		} 


	}

}
add_action( 'admin_init', 'nc_store_delete_plugin' );

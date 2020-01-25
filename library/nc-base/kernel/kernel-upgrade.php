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
  	@Date:   2018-12-29 04:48:55
  	@Last Modified by:   Dami
  	@Last Modified time: 2019-08-20 10:53:09

*/

function nicetheme_store_upgrade_check(){

	$api_domain = NICETHEME_STORE_API_DOMAIN;
	$api_url = $api_domain . '/wp-json/nicetheme-store/v1/get-nc-store';

	$response = wp_remote_get( $api_url );

	if ( is_array( $response ) && $response['response']['code'] == 200 ) {
		$body = json_decode( $response['body'] );
		
		if( isset( $body->status ) && $body->status == 200 ){

			if( version_compare( NC_STORE_VER, $body->version ) < 0 ){

				$this_plugin = str_replace( WP_PLUGIN_DIR . '/', '', NC_STORE_FILE );

				$update_plugins = get_site_transient( 'update_plugins' );

				$nc_store_data = new stdClass();

				$nc_store_data->id = 'nicetheme.cn/nc-store';
				$nc_store_data->slug = 'nc-store';
				$nc_store_data->tested = get_bloginfo('version');
				$nc_store_data->plugin = $this_plugin;
				$nc_store_data->new_version = $body->version;
				$nc_store_data->package = $body->package;

				$update_plugins->response[$this_plugin] = $nc_store_data;

				$update_plugins->checked[$this_plugin] = NC_STORE_VER;

				$update_plugins->last_checked = time();
				
				update_option( 'nc_store_update_info', $nc_store_data, false );

				set_site_transient( 'update_plugins', $update_plugins );

				return array(
					'status'      => 200,
					'msg'         => '检查到全新的版本： ' . $body->version . ' 是否现在更新？',
					'_ajax_nonce' => wp_create_nonce( 'updates' ),
					'plugin'      => $this_plugin,
					'slug'        => 'nc-store',
					'action'      => 'update-plugin'				
				);

			}else{

				return array(
					'status' => 201,
					'msg'    => '已经是最新的版本了'
				);

			}

		}else{

			return array(
				'status' => 500,
				'msg'    => '远程通信失败'
			);

		}

	}else{

		return array(
			'status' => 500,
			'msg'    => '远程通信失败'
		);

	}


}

function nicetheme_store_upgrade(){

	$check_time = get_option( 'nicetheme_store_upgrade_check_time', false );

	if( current_user_can('manage_options') ){

		if( $check_time === false || $check_time < time() ){
		
			nicetheme_store_upgrade_check();

			if( $check_time === false ){
				add_option( 'nicetheme_store_upgrade_check_time', strtotime('+7 days'), '', 'no' );
			}else{
				update_option( 'nicetheme_store_upgrade_check_time', strtotime('+7 days') );
			}

		}

	}


}
add_action( 'admin_init', 'nicetheme_store_upgrade' );

function nc_store_check_update(){

	if( current_user_can('manage_options') ){

		echo json_encode( nicetheme_store_upgrade_check() );

	}else{

		echo json_encode( array(
			'status' => 500,
			'msg'    => '无权操作'
		) );

	}
	die();
}
add_action( 'wp_ajax_nc-store-check-update', 'nc_store_check_update' );
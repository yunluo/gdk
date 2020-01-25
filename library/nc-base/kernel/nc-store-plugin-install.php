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
  	@Date:   2018-10-14 15:22:58
  	@Last Modified by:   Dami
  	@Last Modified time: 2019-03-20 23:43:25

*/
if ( ! defined( 'ABSPATH' ) ) { exit; }

function nc_store_plugin_install(){

	if( !current_user_can('manage_options') ) die('蛤？');

	$module_id = sanitize_text_field( $_POST['module'] );

	$store = new NicethemeStoreRequest(
		'install-verification', 
		array( 
			'method' => 'POST', 
			'body' => array(
				'type' => 'module',
				'item' => $module_id
			) 
		) 
	);



	$ret_data = json_decode( $store->request() );

	if( $ret_data->status == 200 ){

		$install_program = nc_store_plugin_install_program( NICETHEME_STORE_API_URL . NICETHEME_STORE_API_VERSION . '/module-package/' . $module_id . '/' . $ret_data->code );

		if( $install_program ){

			$msg = array(
				'status' => 200,
				'url'    => admin_url( 'admin.php?page=nc-modules-store&activate=' . urlencode( $install_program ) . '&_wpnonce=' . wp_create_nonce( 'nc-store-activate-plugin-'.$install_program ) ),
				'msg'    => '安装成功'
			);

		}else{

			$msg = array(
				'status' => 500,
				'msg'    => '安装失败'
			);

		}

	}else{

		$msg = array(
			'status' => 500,
			'msg'    => $ret_data->msg
		);

	}

	echo json_encode( $msg );
	die();
}
add_action( 'wp_ajax_nc-store-plugin-install', 'nc_store_plugin_install' );
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
  	@Date:   2018-11-25 15:13:46
  	@Last Modified by:   Dami
  	@Last Modified time: 2019-08-20 10:49:38

*/

function nc_store_plugin_updata(){

	if( !current_user_can('manage_options') ) die('蛤？');

	$transient = get_site_transient( 'update_plugins' );

	$module = isset( $_POST['module'] ) && !empty( $_POST['module'] ) ? $_POST['module'] : '';


	if( $module ){

		$module_info = $transient->response[$module];

		if( isset( $module_info ) && isset( $module_info->nc_id ) && !empty( $module_info->nc_id ) ){

			$store = new NicethemeStoreRequest(
				'install-verification', 
				array( 
					'method' => 'POST', 
					'body' => array(
						'type' => 'module',
						'item' => $module_info->nc_id
					) 
				) 
			);

			$ret_data = json_decode( $store->request() );

			if( $ret_data->status == 200 ){

				$module_data = new stdClass();

				$module_data->slug = $module_info->nc_id;
				$module_data->nc_id = $module_info->nc_id;
				$module_data->package = NICETHEME_STORE_API_URL . NICETHEME_STORE_API_VERSION . '/module-package/' . $module_info->nc_id . '/' . $ret_data->code;
				

				$transient->response[$module] = $module_data;

				$transient->last_checked = time();
				set_site_transient( 'update_plugins', $transient ); 

				$is_plugin_active = is_plugin_active( $module );

				$install_program = @nc_store_plugin_updata_program( $module );

				if( $install_program ){

					if( $is_plugin_active ){

						if( !is_wp_error( activate_plugin( $install_program ) ) ){

							$msg = json_encode( array(
								'status' => 200,
								'msg'    => '积木已升级并成功启用！'
							) );

						}else{
							$msg = json_encode( array(
								'status' => 200,
								'msg'    => '积木升级成功，但启用失败。请手动启用！'
							) );
						}

					}else{

						$msg = json_encode( array(
							'status' => 200,
							'msg'    => '积木升级成功！'
						) );

					}

				}else{
					$msg = json_encode( array(
						'status' => 500,
						'msg'    => '升级失败，请稍后再试。'
					) );
				}

			}else{

				$msg = json_encode( array(
					'status' => 500,
					'msg'    => $ret_data->msg
				) );

			}

		}else{

			$msg = json_encode( array(
				'status' => 500,
				'msg'    => '没有可升级的积木'
			) );

		}

	}else{

		$msg = json_encode( array(
			'status' => 500,
			'msg'    => '错误的请求'
		) );

	}

	echo $msg;
	die();

}
add_action( 'wp_ajax_nc-store-plugin-updata', 'nc_store_plugin_updata');
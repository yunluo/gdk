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
  	@Date:   2018-11-19 06:56:44
  	@Last Modified by:   Dami
  	@Last Modified time: 2019-08-20 10:54:49

*/

function nicetheme_module_filter( $plugins ){

	delete_option( 'nc_store_update_info' );

	foreach ($plugins as $key => $value) {
		if( !empty( $value['Nicetheme Module'] ) && $value['Nicetheme Module'] != 'jimu' ){
			unset($plugins[$key]);
		}
	}
	return $plugins;
}
add_filter( 'all_plugins', 'nicetheme_module_filter' );


// 获取所有积木
function nc_store_all_modules( $check_update = false ){

	$all_plugins = get_plugins();
	$modules = array();

	foreach ($all_plugins as $key => $value) {
		if( !empty( $value['Nicetheme Module'] ) && $value['Nicetheme Module'] != 'jimu' ){
			$modules[$key] = $value;
		}
	}

	if( !empty( $modules ) && $check_update ){

		$store = new NicethemeStoreRequest(
			'modules_check', 
			array( 
				'method' => 'POST', 
				'body' => array(
					'modules' => serialize( $modules )
				)
			) 
		);

		$ret_modules = json_decode( $store->request() );

		if( isset( $ret_modules->status ) && $ret_modules->status == 200 ){
			$modules = (array)$ret_modules->modules;
		}

	}

	return $modules;

}

// 升级信息附加
add_filter( 'site_transient_update_plugins', 'nc_store_append_transient' );
function nc_store_append_transient( $transient ){

	$nc_store = get_option( 'nc_store_update_info');

	if( !empty( $nc_store ) ){

		$transient->response[$nc_store->plugin] = $nc_store;

	}

	return $transient;


}

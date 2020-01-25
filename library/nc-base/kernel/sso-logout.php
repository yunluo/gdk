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
  	@Date:   2018-11-14 15:15:25
  	@Last Modified by:   Dami
  	@Last Modified time: 2018-12-28 22:21:13

*/
function nc_store_sso_logout(){

	$user = get_userdata( get_current_user_id() );

	if( !empty($user->roles) && in_array('administrator', $user->roles) ){

		delete_option( 'NC_STORE_USER_DATA' );
		delete_option( 'NC_STORE_SECRET' );

		echo json_encode( array('status' => 'ok') );

		die();

	}
}
add_action( 'wp_ajax_nicetheme-store-logout', 'nc_store_sso_logout' );
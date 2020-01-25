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
  	@Last Modified time: 2019-08-25 21:15:36

*/
function nc_store_sso_login(){

	$user = get_userdata( get_current_user_id() );

	if( 
		isset( $_GET['callback'] ) &&
		$_GET['callback'] == 'nicetheme_sso_login' && 
		isset( $_GET['secret'] ) &&
		!empty( $_GET['secret'] ) &&
		!empty($user->roles) && 
		in_array('administrator', $user->roles) 
	){

    
    	$secret = urldecode( $_GET['secret'] );

		$store = new NicethemeStoreRequest(
			'user_login', 
			array( 
				'method' => 'POST', 
				'body' => array(
					'secret'   => $secret
				) 
			) 
		);

		$ret_data = json_decode( $store->request() );

		if( isset( $ret_data->status ) && $ret_data->status == 200 ){

			update_option( 'NC_STORE_USER_DATA', $ret_data->user_data );
			update_option( 'NC_STORE_SECRET', $secret );

			
			echo '
				<script>
					var url = window.parent.location.href;
					window.parent.location.href = url + "&status=200&msg='.urlencode('登录成功').'";
				</script>
			';

		}else{

			if( !isset( $ret_data->msg ) ){
				$ret_data = new stdClass();
				$ret_data->msg = '登录失败';
			}

			delete_option( 'NC_STORE_USER_DATA' );
			delete_option( 'NC_STORE_SECRET' );

			echo '
				<script>
					window.parent.location.href = window.parent.location.href + "&status=401&msg=" + "'.urlencode($ret_data->msg).'";
				</script>
			';

		}

		die();

	}
}
add_action( 'admin_init', 'nc_store_sso_login' );
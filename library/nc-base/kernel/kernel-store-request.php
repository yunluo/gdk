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
  	@Date:   2018-11-15 13:20:43
  	@Last Modified by:   Dami
  	@Last Modified time: 2019-10-02 18:37:07

*/

class NicethemeStoreRequest {
	

	private $api_url = NICETHEME_STORE_API_URL;

	private $api_version = NICETHEME_STORE_API_VERSION;

	private $action;

	private $request_url;

	private $headers = array( 'SOURCE' => 'NICETHEME-STORE' );

	private $request;


	private function init( $action, $request ){

		$this->request_url = $this->api_url . $this->api_version . '/' . $action;

		$request['user-agent'] = 'NICETHEME-STORE/' . NC_STORE_VER .'; ' . admin_url();
		$request['timeout'] = 60;

		$secret = get_option( 'NC_STORE_SECRET' ); 
		if( !empty( $secret ) ){
			$request['headers'] = array_merge( 
				$this->headers, 
				array( 
					'CALLBACKURL' => admin_url(),
					'SECRET'      => $secret
				) 
			);
		}else{
			$request['headers'] = array_merge( $this->headers, array( 'CALLBACKURL' => admin_url() ) );
		}

		$this->request = $request;

	}

	public function request(){

		$res = wp_remote_request( $this->request_url, $this->request );

		if( is_wp_error($res) ){

			add_filter('https_ssl_verify', '__return_false');
			add_filter('https_local_ssl_verify', '__return_false');
			
			$res = wp_remote_request( $this->request_url, $this->request );

		}

		if( !is_wp_error($res) && ($res['response']['code'] == 200 || $res['response']['code'] == 201) ) {
		    return $res['body'];
		}else{
			return false;
		}

	}

	function __construct( $action, $request = '' ){
	
		$this->init( $action, $request );

	}





}
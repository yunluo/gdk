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
  	@Date:   2018-11-19 16:30:33
  	@Last Modified by:   Dami
  	@Last Modified time: 2019-03-19 14:58:17

*/

function nicetheme_wp_http_headers_hook( $args, $url ){

	$nicetheme_url = NICETHEME_STORE_API_DOMAIN;

	if( stripos( $url, $nicetheme_url ) !== false ){

		$args['user-agent'] = 'NICETHEME-STORE/' . NC_STORE_VER .'; ' . admin_url();

		if( !isset( $args['headers']['SOURCE'] ) )

			$args['headers']['SOURCE'] = 'NICETHEME-STORE';

		if( !isset( $args['headers']['CALLBACKURL'] ) )

			$args['headers']['CALLBACKURL'] = admin_url();

		if( !isset( $args['headers']['SECRET'] ) ){

			$secret = get_option( 'NC_STORE_SECRET' ); 
			$args['headers']['SECRET'] = $secret;

		}

	}

	if( stripos( $url, 'api.wordpress.org/plugins/update-check' ) !== false ){

		if( isset( $args['body']['plugins'] ) && !empty( $args['body']['plugins'] ) ){

			$plugins = json_decode( $args['body']['plugins'], true );

			if( isset( $plugins['plugins'] ) && !empty( $plugins['plugins'] ) ){

				foreach ($plugins['plugins'] as $key => $value) {
					if( isset( $value['Nicetheme Module'] ) && !empty( $value['Nicetheme Module'] ) ){
						unset( $plugins['plugins'][$key] );
					}
				}

				$args['body']['plugins'] = wp_json_encode( $plugins );

			}

		}

	}

	return $args;

}

add_filter( 'http_request_args', 'nicetheme_wp_http_headers_hook', 10, 2 );
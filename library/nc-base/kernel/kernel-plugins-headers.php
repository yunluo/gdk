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
  	@Date:   2018-11-19 06:56:27
  	@Last Modified by:   Dami
  	@Last Modified time: 2018-11-19 10:19:00

*/
function nicetheme_module_headers( $headers ){
	
	if ( !in_array( 'Nicetheme Module', $headers ) )
        $headers[] = 'Nicetheme Module';

    if ( !in_array( 'Compatible', $headers ) )
        $headers[] = 'Compatible';

    return $headers;
}
add_filter( 'extra_plugin_headers', 'nicetheme_module_headers' );
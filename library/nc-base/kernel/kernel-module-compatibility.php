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
  	@Date:   2018-11-19 12:58:13
  	@Last Modified by:   Dami
  	@Last Modified time: 2018-11-19 13:58:50

*/
function module_compatibility( $module ){

	if( $module['Compatible'] == 'all' || empty( $module['Compatible'] ) ){
		return true;
	}else{

		$current_theme = wp_get_theme();

		$Compatible = explode('|', $module['Compatible']);

		if( in_array( $current_theme->name, $Compatible ) ){
			return true;
		}else{
			return false;
		}

	}

}
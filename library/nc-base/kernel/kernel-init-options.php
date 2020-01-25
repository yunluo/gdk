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
  	@Date:   2018-10-15 17:13:54
  	@Last Modified by:   Dami
  	@Last Modified time: 2018-10-15 17:30:38

*/
function nc_store_trigger_init_options(){

	do_action( 'nc_store_options_init' );

}
add_action( 'init', 'nc_store_trigger_init_options', 1 );
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
  	@Date:   2018-10-13 14:24:31
  	@Last Modified by:   Dami
  	@Last Modified time: 2019-03-20 18:53:42

*/
if ( ! defined( 'ABSPATH' ) ) { exit; }

function nicetheme_modules_menu() {

    add_menu_page(
        '积木',
        '积木',
        'manage_options',
        'nc-modules-store',
        '',
        'dashicons-icon-nicetheme-logo--border',
        60
    );

    add_submenu_page( 'nc-modules-store', '积木箱子', '积木箱子', 'manage_options', 'nc-modules-store', function(){
        include('home.php');
    } );


    acf_add_options_sub_page(
        array(
            'page_title'      => '积木介绍',
            'menu_title'      => '积木介绍',
            'menu_slug'       => 'nc-intro',
            'parent_slug'     => 'nc-modules-store',
            'capability'      => 'manage_options',
            'update_button'   => '保存',
            'updated_message' => '设置已保存！'
        )
    );

}
add_action( 'admin_menu', 'nicetheme_modules_menu' );


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
  	@Date:   2018-10-14 15:19:46
  	@Last Modified by:   Dami
  	@Last Modified time: 2019-08-25 21:16:07

*/
if ( ! defined( 'ABSPATH' ) ) { exit; }

define('NICETHEME_STORE_API_DOMAIN', 'https://www.nicetheme.cn');

define('NICETHEME_STORE_API_URL', NICETHEME_STORE_API_DOMAIN . '/wp-json/nicetheme-store/');

define('NICETHEME_STORE_API_VERSION', 'v1');


include('utils.php');

include('kernel-notice.php');

include('kernel-framework-init.php');

include('kernel-plugin-install.php');

include('kernel-plugin-updata.php');

include('kernel-plugin-action.php');

include('kernel-init-options.php');

include('kernel-store-request.php');

include('kernel-plugins-headers.php');

include('kernel-plugins-filter.php');

include('kernel-module-compatibility.php');

include('kernel-wp-http-headers.php');

include('kernel-upgrade.php');

include('sso-login.php');

include('sso-logout.php');
/**
 * ajax action
 */
include('nc-store-plugin-install.php');

include('nc-store-plugin-updata.php');


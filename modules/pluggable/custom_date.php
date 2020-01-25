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
  	@Date:   2019-08-02 11:36:30
  	@Last Modified by:   Dami
  	@Last Modified time: 2019-08-02 21:38:32

*/

if( !function_exists('timeago') ){
	function timeago( $ptime ) {
	    $ptime = strtotime($ptime);

	    $current_offset = get_option('gmt_offset');

	    if( $current_offset > 0 ){

	    	$etime = strtotime( date_i18n( 'Y-m-d H:i:s' ) ) - $current_offset * 60 * 60 - $ptime;

	    }else if( $current_offset < 0 ){

	    	$etime = strtotime( date_i18n( 'Y-m-d H:i:s' ) ) + $current_offset * 60 * 60 - $ptime;

	    }else{
	    	$etime = strtotime( date_i18n( 'Y-m-d H:i:s' ) ) - $ptime;
	    }

	    
	    if($etime < 1) return '刚刚';
	    
	    $interval = array (
	        12 * 30 * 24 * 60 * 60  =>  date('Y-m-d', $ptime),
	        30 * 24 * 60 * 60       =>  date('m-d', $ptime),
	        7 * 24 * 60 * 60        =>  date('m-d', $ptime),
	        24 * 60 * 60            =>  '天前',
	        60 * 60                 =>  '小时前',
	        60                      =>  '分钟前',
	        1                       =>  '秒前'
	    );
	    foreach ($interval as $secs => $str) {
	        if( $etime - ( 24 * 60 * 60 ) > 0 ){
	            return $str;
	        } else {
	           $d = $etime / $secs;
	           if ($d >= 1) {
	               $r = round($d);
	               return $r . $str;
	           }
	        }

	    }
	}
}
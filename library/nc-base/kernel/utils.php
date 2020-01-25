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
  	@Date:   2018-11-19 13:23:06
  	@Last Modified by:   Dami
  	@Last Modified time: 2019-03-21 01:12:04

*/
if( !function_exists('objectToArray') ){

	function objectToArray( $d ){

	    if (is_object($d)) {
	        $d = get_object_vars($d);
	    }

	    if (is_array($d)) {
	        return array_map(__FUNCTION__, $d);
	    }else{
	        return $d;
	    }
	}
	
}

function asyn_post($url, $query){	
	
    $info = parse_url($url);
    $host = $info['host'];
    if ($info['scheme'] == 'https') {
        $port = empty($info['port']) ? 443 : $info['port'];
        $host = 'ssl://'.$host;
    } else {
        $port = empty($info['port']) ? 80 : $info['port'];
    }

    $fp = fsockopen($host, $port, $errno, $errstr, 3);
    $head = "POST ".$info['path']."?".$info["query"]." HTTP/1.1\r\n";
    $head .= "Host: ".$info['host']."\r\n";
    $head .= "Referer: http://".$info['host'].$info['path']."\r\n";
    $head .= "Content-type: application/x-www-form-urlencoded\r\n";
    $head .= "Content-Length: ".strlen(trim($query))."\r\n";
    $head .= "Connection:Close\r\n";
    $head .= "\r\n";
    $head .= trim($query);
    fwrite($fp, $head);
    fclose($fp);
}

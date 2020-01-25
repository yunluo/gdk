<?php

function nice_cdn_replace($html){

    $cdn_replace_list = isset( $GLOBALS['nc_cdn_replace_list'] ) ? $GLOBALS['nc_cdn_replace_list'] : [];

    if( !empty( $cdn_replace_list ) ){

        $patterns = [];
        $replacements = [];

        foreach ($cdn_replace_list as $key => $value) {

            if( !empty( $value['source'] ) && !empty( $value['target'] ) ){

                $patterns[] = '/http(s|):\/\/' . str_replace( '.', '\\.', $value['source']) .'\/wp-([^"\']*?)\.(jpg|png|gif|bmp|jpeg|css|js)/i';
                $replacements[] = '//' . $value['target'] . '/wp-$2.$3';

            }
            
        }
        
    }

    if( !empty($patterns) && !empty($replacements) ){

        $html = preg_replace( $patterns, $replacements, $html );

    }

    return $html;
}

ob_start("nice_cdn_replace");
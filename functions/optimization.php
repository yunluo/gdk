<?php



//禁用新版编辑器
add_filter('use_block_editor_for_post', '__return_false');
remove_action( 'wp_enqueue_scripts', 'wp_common_block_scripts_and_styles' );


// 友情链接扩展
add_filter('pre_option_link_manager_enabled', '__return_true');
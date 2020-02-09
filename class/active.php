<?php

//激活插件后,主要是创建页面

function weauth_plugin_activate() {
   		$weixin_login_id = get_option("gdk_option['weixin_login_id']");
	if (!$weixin_login_id) {
		$post1 = array(
			'post_title' => "微信登录", //这里是自动生成页面的页面标题
			'post_content' => "[weauth]", //这里是页面的内容
			'post_status' => "publish",
			'post_type' => 'page',
			'post_name' => 'weixin-login',
		);
		$page_id = wp_insert_post($post1);
		update_post_meta($page_id, "_wp_page_template", ""); //这里是生成页面的模板类型
		update_option("gdk_option['weixin_login_id']", $page_id);
	}

}
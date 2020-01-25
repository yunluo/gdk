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
  	@Date:   2017-09-16 14:42:56
  	@Last Modified by:   Dami
  	@Last Modified time: 2019-08-08 20:59:32

*/

if( !class_exists('MiCustomShare') ) :

class MiCustomShare {
	
	private $config;

	function __construct(){

		$nc_option = get_option('nc_option');

		$this->config = $nc_option['wechat_qq_share_custom_conf'];

		add_action( 'wp_enqueue_scripts', array( $this, 'mi_add_share_js' ) );

		add_action( 'wp_footer', array( $this, 'mi_add_share_info' ), 2333 );

	}

	function mi_add_share_js(){
		wp_enqueue_script( 'mi-share-js', '//qzonestyle.gtimg.cn/qzone/qzact/common/share/share.js', array(), NC_OPTIMIZEUP_VERSION, 'all' );
	}

	function get_signature_url(){
		$protocol = is_ssl() ? 'https://' : 'http://';
		$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$url = explode('#', $url);
		$url = $url[0];
		
		return $url;
	}

	//获取Access Token
	function get_access_token(){

		if( ($access_token = get_option('ws_access_token')) !== false && $access_token != '' && time() < $access_token['expire_time']){
			return $access_token['access_token'];
		}
		
		$api_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='. $this->config['share_appid'] .'&secret='. $this->config[ 'appsecret' ];
		$response = wp_remote_get($api_url);
		if ( is_array( $response ) && ! is_wp_error($response) ){
			$result = json_decode($response['body']);
		} else {
			return false;
		}
		
		$access_token['access_token'] = !$result->errcode ? $result->access_token : '';
		$access_token['expire_time'] = !$result->errcode ? time() + intval( $result->expires_in ) : '';
		update_option( 'ws_access_token', $access_token );
		
		return $access_token['access_token'];
	}

	//获取JSAPI TICKET
	function get_jsapi_ticket(){
		if( ($jsapi_ticket = get_option('wx_jsapi_ticket')) !== false && $jsapi_ticket != '' && time() < $jsapi_ticket['expire_time']){
			return $jsapi_ticket['jsapi_ticket'];
		}
		
		if( ($access_token = $this->get_access_token()) === false ) return false;
		$api_url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='. $access_token .'&type=jsapi';
		$response = wp_remote_get($api_url);
		if ( is_array( $response ) && ! is_wp_error($response) ){
			$result = json_decode($response['body']);
		}
		else{
			return false;
		}
		
		$jsapi_ticket['jsapi_ticket'] = !$result->errcode ? $result->ticket : '';
		$jsapi_ticket['expire_time'] = !$result->errcode ? time() + intval( $result->expires_in ) : '';
		update_option( 'wx_jsapi_ticket', $jsapi_ticket );
		
		return $jsapi_ticket['jsapi_ticket'];
	}

	//生成随机字符串
	function generate_noncestr( $length = 16 ){

		$noncestr = '';

		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		for( $i = 0; $i < $length; $i++ ){
			$noncestr .= $chars[ mt_rand(0, strlen($chars) - 1) ];
		}
		return $noncestr;
	}

	//生成签名
	function generate_signature($jsapi_ticket, $noncestr, $timestamp, $url){
		$str = 'jsapi_ticket='. $jsapi_ticket .'&noncestr='. $noncestr .'&timestamp='. $timestamp .'&url='. $url;
		return sha1($str);
	}

	function get_wx_config(){

		if( $this->config['share_appid'] && $this->config[ 'appsecret' ] && ( $jsapi_ticket = $this->get_jsapi_ticket() ) !== false ){
			$noncestr      = $this->generate_noncestr();
			$timestamp     = time();
			$signature_url = $this->get_signature_url();
			$signature     = $this->generate_signature($jsapi_ticket, $noncestr, $timestamp, $signature_url);

			$config = array(
				'noncestr'  => $noncestr,
				'timestamp' => $timestamp,			
				'signature' => $signature,
			);
		}else{
			$config = null;
		}

		return $config;

	}

	function get_share_info(){

		$info = array();

		if( is_single() || is_singular() ){

			global $post;

			if( has_post_thumbnail( $post->ID ) ){

				$post_thumbnail_src = get_post_thumbnail_id($post->ID);
		        $post_thumbnail_src =  wp_get_attachment_image_src($post_thumbnail_src, 'full');
		        $pic = $post_thumbnail_src[0];

			}else{

				$preg =  '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
				preg_match_all($preg, apply_filters( 'the_content', $post->post_content ), $match);

				if( isset( $match[1][0] ) && !empty( $match[1][0] ) ){

					$pic = $match[1][0];

				}else{

					$pic = $this->config['share_img'];

				}

			}

			$pic = isset( $pic ) ? $pic : NC_BASE_URL . 'library/static/default.png';

			

			$info = array(
				'title'   => $this->config['share_title_in_single'] ? $this->config[ 'share_title' ] . $this->config[ 'share_title_in_single_line' ] . get_the_title() : get_the_title(),
				'summary' => get_the_excerpt(),
				'url'     => get_permalink(),
				'pic'     => $pic,
			);


		}else{

			$pngdata = NC_BASE_URL . 'library/static/default.png';

			if( isset( $this->config[ 'share_img' ] ) && is_numeric( $this->config[ 'share_img' ] ) ){
				$att = wp_get_attachment_image_url( $this->config[ 'share_img' ], 'full' );
			}else if( isset( $this->config[ 'share_img' ] ) && !empty( $this->config[ 'share_img' ] ) ) {
				$att = $this->config[ 'share_img' ];
			}

			if( isset( $att ) ){
				$pic = $att;
			}else{
				$pic = null;
			}

			$info = array( 
				'title'   => $this->config[ 'share_title' ] ? $this->config[ 'share_title' ] : get_bloginfo( 'name' ),
				'summary' => $this->config[ 'share_summary' ] ? $this->config[ 'share_summary' ] :get_bloginfo( 'description' ),
				'url'     => $this->get_signature_url(),
				'pic'     => $pic ? $pic : $pngdata,
			);


		}

		return $info;

	}

	function mi_add_share_info(){

		$info = $this->get_share_info();

		$wxconfig = $this->get_wx_config();
		$wxappid = $this->config['share_appid'];

		if( $wxconfig && $wxappid ){
		$WXconfig = <<<WX
			WXconfig:{
				swapTitleInWX: false,
				appId: '{$wxappid}',
				timestamp: '{$wxconfig['timestamp']}',
				nonceStr: '{$wxconfig['noncestr']}',
				signature: '{$wxconfig['signature']}'
			}
WX;

		}else{
			$WXconfig = '';
		}
	

		$script = <<<SCRIPT

		<script>
	
			setShareInfo({
				title: '{$info['title']}',
				summary: '{$info['summary']}',
				pic: '{$info['pic']}',
				url: '{$info['url']}',
				{$WXconfig}
			});

		</script>
SCRIPT;

		echo $script;

	}


}

new MiCustomShare();

endif;

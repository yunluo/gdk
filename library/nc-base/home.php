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
  	@Date:   2018-10-12 15:07:49
 * @Last Modified by: suxing
 * @Last Modified time: 2019-07-22 17:55:40

*/
if ( ! defined( 'ABSPATH' ) ) { exit; }


$nc_store_user_data = get_option('NC_STORE_USER_DATA');

$secret = urlencode( get_option( 'NC_STORE_SECRET' ) );

if( isset( $_GET['tab'] ) && !empty( $_GET['tab'] ) ){
	$tab = $_GET['tab'];
}else{
	$tab = 'home';
}

?>
<?php if( isset( $_GET['status'] ) && $_GET['status'] == 200 && isset( $_GET['msg'] ) && !empty( $_GET['msg'] ) ){ ?>
	<script>
		jQuery(function(){
			layer.msg('<?php echo urldecode( $_GET['msg'] ) ?>')
		});
	</script>
<?php }else if( isset( $_GET['status'] ) && isset( $_GET['msg'] ) && !empty( $_GET['msg'] ) ){ ?>
	<script>
		jQuery(function(){
			layer.msg('<?php echo urldecode( $_GET['msg'] ) ?>')
		});
	</script>
<?php } ?>

<style>
	span.avatar img { width: 28px; height: 28px; border-radius: 28px; }
	.column-name a img { width: 128px; height: 128px; }
</style>

<div class="wrap plugin-install-tab-featured" id="nicetheme-666">
    <h1 class="wp-heading-inline"><?php isset( $nc_store_user_data->display_name ) ? print($nc_store_user_data->display_name . '的') : ''; ?>积木箱子</h1>
    <hr class="wp-header-end">
    <h2 class="screen-reader-text">过滤积木列表</h2>
    <div class="wp-filter">
        <ul class="filter-links">
        	<li class="plugin-install-featured">
                <a href="<?php echo admin_url('admin.php?page=nc-modules-store'); ?>" <?php $tab == 'home' ? printf('class="current"') : ''; ?>>已安装</a>
            </li>
            <li class="plugin-install-popular">
                <a <?php $tab == 'hot' ? printf('class="current"') : ''; ?> href="<?php echo admin_url('admin.php?page=nc-modules-store&tab=hot'); ?>">热门</a>
            </li>
            <li class="plugin-install-recommended">
                <a <?php $tab == 'bought' ? printf('class="current"') : ''; ?> href="<?php echo admin_url('admin.php?page=nc-modules-store&tab=bought'); ?>">已购</a>
            </li>
            <li>
            	<a href="javascript:;" class="nc-store-check-update">检查更新</a>
            </li>
        </ul>
        <div class="search-form search-plugins">
        	<?php if( !empty( $nc_store_user_data ) ){ ?>
				<span class="avatar">
					<?php echo $nc_store_user_data->avatar; ?>
				</span>
				<span class="name">
					<?php echo $nc_store_user_data->display_name; ?>
				</span>
				
				<?php 
					switch ( $nc_store_user_data->vip ) {
						case 1:
							echo '<span class="vip">VIP</span>';
							break;
						
						case 2:
							echo '<span class="vip">SVIP</span>';
							break;
						
						default:
							# code...
							break;
					}
				?>
				
				<a href="javascript:;" class="nicetheme-store-logout">退出</a>
        	<?php }else{ ?>
        		<a href="javascript:;" class="nicetheme-sso-login" data-login_url="<?php echo NICETHEME_STORE_API_DOMAIN; ?>/sso-login?callback_url=<?php echo urlencode( admin_url() ); ?>">登录</a>
        	<?php } ?>
        	
        </div>
    </div>
    <br class="clear">
    <form id="plugin-filter" method="post">
        <div class="wp-list-table widefat plugin-install">
            <h2 class="screen-reader-text">积木列表</h2>
            <div id="the-list">
			<?php 
				switch ( $tab ) {
					case 'home':
						load_template( NC_STORE_ROOT_PATH . 'library/nc-base/partials/installed-loop.php' );
						break;

					case 'hot':
						load_template( NC_STORE_ROOT_PATH . 'library/nc-base/partials/hot-loop.php' );
						break;

					case 'bought':
						load_template( NC_STORE_ROOT_PATH . 'library/nc-base/partials/bought-loop.php' );
						break;
					
					default:
						load_template( NC_STORE_ROOT_PATH . 'library/nc-base/partials/installed-loop.php' );
						break;
				}
			?>
            </div>
        </div>
    </form>
    <span class="spinner"></span>
</div>
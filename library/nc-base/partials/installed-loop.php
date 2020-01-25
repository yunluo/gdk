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
  	@Date:   2018-11-20 13:12:48
  	@Last Modified by:   Dami
  	@Last Modified time: 2019-08-20 10:53:19

*/

$installed_plugins = objectToArray( nc_store_all_modules( true ) );

$transient = get_site_transient( 'update_plugins' );

if( !empty( $installed_plugins ) ){
	foreach ($installed_plugins as $key => $value) {
		

		if( isset( $value['new_version'] ) ){
			$new_transient = true;
			$module_data = new stdClass();

			$module_data->slug = $value['Nicetheme Module'];
			$module_data->nc_id = $value['Nicetheme Module'];
			$module_data->package = NICETHEME_STORE_API_URL . NICETHEME_STORE_API_VERSION . '/module-package/' . $value['Nicetheme Module'];
			$module_data->new_version = $value['new_version']['module_version'];
			$module_data->tested = get_bloginfo('version');

			$transient->response[$key] = $module_data; 

		}


?>
<div class="plugin-card plugin-card-akismet">
    <div class="plugin-card-top">
        <div class="name column-name">
            <h3>
                <a href="<?php echo $value['PluginURI']; ?>">
                	<?php echo $value['Title'] ?>
                	<?php if( isset( $value['icon'] ) ){ ?>
                    <img src="<?php echo $value['icon']; ?>" class="plugin-icon">
                	<?php }else{ ?>
                		<div class="plugin-icon defualt-bg">
                			<span><?php echo $value['Title']; ?></span>
                		</div>
                	<?php } ?>
                </a>
            </h3>
        </div>
        <div class="action-links">
            <ul class="plugin-action-buttons">
            <?php if( is_plugin_active( $key ) ){ ?>
            	<li>                       		
        			<a class="install-now button aria-button-if-js" href="<?php echo admin_url( 'admin.php?page=nc-modules-store&deactivate=' . urlencode( $key ) . '&_wpnonce=' . wp_create_nonce( 'nc-store-deactivate-plugin-'.$key ) ); ?>" role="button">停用积木</a>
        		</li>
        	<?php }else{ ?>
        		<li>
        			<a class="install-now button aria-button-if-js" href="<?php echo admin_url( 'admin.php?page=nc-modules-store&activate=' . urlencode( $key ) . '&_wpnonce=' . wp_create_nonce( 'nc-store-activate-plugin-'.$key ) ); ?>" role="button">启用积木</a>
        		</li>
            	<li>
            		<a class="delete-nc-module" href="<?php echo admin_url( 'admin.php?page=nc-modules-store&delete-module=' . urlencode( $key ) . '&_wpnonce=' . wp_create_nonce( 'nc-store-delete-module-'.$key ) ); ?>">删除积木</a>
            	</li>
            <?php } ?>
                <li> 
                </li>
            </ul>
        </div>
        <div class="desc column-description">
            <p><?php echo $value['Description']; ?></p>
            <p class="authors"><a href="<?php echo $value['PluginURI']; ?>">更多详情</a></p>
        </div>
    </div>
    <div class="plugin-card-bottom">
	<?php if( isset( $value['new_version'] ) ){ ?>
        <div class="column-downloaded">
        	<strong>最新版本：</strong> <?php echo $value['new_version']['module_version']; ?>
        	<a href="javascript:;" class="nc-updata-module" data-key="<?php echo $key; ?>">现在更新</a>
        </div>
	<?php }else{ ?>
		<div class="column-downloaded"><strong>当前版本：</strong> <?php echo $value['Version']; ?></div>
	<?php } ?>	
        <div class="column-compatibility">
        	<?php if( module_compatibility( $value ) ){ ?>
				<span class="compatibility-compatible">
                	该积木 <strong>兼容</strong> 您当前使用的主题
                </span>
        	<?php }else{ ?>
				<span class="compatibility-incompatible">
                	该积木 <strong>不兼容</strong> 您当前使用的主题
                </span>
        	<?php } ?>                      	
            
        </div>
    </div>
</div>
<?php
	}
	if( isset( $new_transient ) ){
		$transient->last_checked = time();
		set_site_transient( 'update_plugins', $transient );
	}
}else{
	echo '<p class="no-themes" style="display: block;">没有已安装的积木。</p>';
}
?>
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
  	@Date:   2018-12-26 19:30:23
  	@Last Modified by:   Dami
  	@Last Modified time: 2019-01-31 05:15:35

*/
$store = new NicethemeStoreRequest(
	'get-bought-modules',
	array(
		'method' => 'POST',
		'body' => array(
			'modules' => serialize( nc_store_all_modules( false ) ),
		)
	)
);

$bought_modules = objectToArray( json_decode( $store->request() ) );

if( isset( $bought_modules['status'] ) && $bought_modules['status'] == 200 ){
	foreach ($bought_modules['modules'] as $key => $value) {
?>

<div class="plugin-card plugin-card-akismet">
    <div class="plugin-card-top">
        <div class="name column-name">
            <h3>
                <a href="<?php echo $value['PluginURI']; ?>">
                	<?php echo $value['Title'] ?>
                    <img src="<?php echo $value['icon']; ?>" class="plugin-icon" alt="">
                </a>
            </h3>
        </div>
        <div class="action-links">
            <ul class="plugin-action-buttons">

			<?php

				// 兼容优先
				if( module_compatibility( $value ) ){

					// 已购买，并安装
					if( $value['installed'] && $value['install'] ){

						// 插件是否启用
						if( is_plugin_active( $key ) ){

			?>
							<li>
			        			<a class="install-now button aria-button-if-js" href="<?php echo admin_url( 'admin.php?page=nc-modules-store&deactivate=' . urlencode( $key ) . '&_wpnonce=' . wp_create_nonce( 'nc-store-deactivate-plugin-'.$key ) ); ?>" role="button">停用积木</a>
			        		</li>
			<?php

						}else{

			?>
							<li>
			        			<a class="install-now button aria-button-if-js" href="<?php echo admin_url( 'admin.php?page=nc-modules-store&activate=' . urlencode( $key ) . '&_wpnonce=' . wp_create_nonce( 'nc-store-activate-plugin-'.$key ) ); ?>" role="button">启用积木</a>
			        		</li>
							<li>
			            		<a class="delete-nc-module" href="<?php echo admin_url( 'admin.php?page=nc-modules-store&delete-module=' . urlencode( $key ) . '&_wpnonce=' . wp_create_nonce( 'nc-store-delete-module-'.$key ) ); ?>">删除积木</a>
			            	</li>
			<?php

						}
					// 已购买，未安装
					}else if( !$value['installed'] && $value['install'] ){
						echo '<a class="install-now button aria-button-if-js nc-install" href="javascript:;" data-id="'.$value['Nicetheme Module'].'" role="button">现在安装</a>';
					}else{
						echo '<li><a class="install-now button aria-button-if-js" href="'.$value['PluginURI'].'" role="button">购买积木</a></li>';
					}



				}else{

					echo '<li><a class="install-now button aria-button-if-js disabled" href="javascript:;" role="button">不兼容</a></li>';

				}



			?>

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
        	<?php if( $value['install'] ){ ?>
        	<a href="javascript:;" class="nc-updata-module" data-key="<?php echo $key; ?>">现在更新</a>
        	<?php } ?>
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
}else{

	if( $bought_modules['status'] == 400 || $bought_modules['status'] == 401 ){

		delete_option( 'NC_STORE_USER_DATA' );
		delete_option( 'NC_STORE_SECRET' );

	}

	echo '<p class="no-themes" style="display: block;">' . $bought_modules['msg'] . '</p>';
}
?>
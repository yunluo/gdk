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
  	@Date:   2018-10-14 19:20:35
  	@Last Modified by:   Dami
  	@Last Modified time: 2018-10-14 19:30:40

*/


class Nc_Store_Notice {
	
	/**
	 * Message to be shown
	 */
	private $message;
	/**
	 * CSS classes to apply on the notice div
	 * notice-error, notice-warning, notice-success, or notice-info.
	 */
	private $css_classes = array( 'notice' );
	/**
	 * Magic starts here.
	 *
	 * @param  string  $message      Message to be shown
	 * @param  array   $css_classes  CSS classes to apply on the notice div
	 *
	 * @since 1.0.6
	 * @return void
	 */
	public function __construct( $message, $css_classes ) {
		$this->message = $message;
		if( ! empty( $css_classes ) && is_array( $css_classes ) ) {
			$this->css_classes = array_merge( $this->css_classes, $css_classes );
		}
		add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );
	}
	/**
	 * Displays admin notice on success, error, warning, etc.
	 *
	 * @since 1.0.6
	 * @return void
	 */
	public function display_admin_notice() {
		?>
		<div class="<?php echo implode( ' ', $this->css_classes ); ?>">
			<p><?php echo $this->message; ?></p>
		</div>
		<?php
	}
	
}
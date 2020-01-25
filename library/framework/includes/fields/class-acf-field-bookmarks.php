<?php

if( ! class_exists('acf_field_bookmarks') ) :

class acf_field_bookmarks extends acf_field {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function initialize() {
		
		// vars
		$this->name = 'link_cat';
		$this->label = __('Bookmarks');
		$this->category = 'relational';
		$this->defaults = array(
			'save_format' => 'id',
			'allow_null'  => 0
		);
		
	}
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function render_field( $field ) {
        $allow_null = $field['allow_null'];
		$link_cats = $this->get_bookmark_categories( $allow_null );

		if ( empty( $link_cats ) ) {
			return;
		}
		?>
		<select id="<?php esc_attr( $field['id'] ); ?>" class="<?php echo esc_attr( $field['class'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>">
		<?php foreach( $link_cats as $link_cat_id => $link_cat_name ) : ?>
			<option value="<?php echo esc_attr( $link_cat_id ); ?>" <?php selected( $field['value'], $link_cat_id ); ?>>
				<?php echo esc_html( $link_cat_name ); ?>
			</option>
		<?php endforeach; ?>
		</select>
		<?php
	}
		
	
	/*
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function render_field_settings( $field ) {
        
        acf_render_field_setting( $field, array(
			'label'        => __( 'Return Value' ),
			'instructions' => __( 'Specify the returned value on front end' ),
			'type'         => 'radio',
			'name'         => 'save_format',
			'layout'       => 'horizontal',
			'choices'      => array(
				'id'     => __( 'Bookmarks ID' ),
			),
        ) );
        
		// Register the Allow Null setting
		acf_render_field_setting( $field, array(
			'label'        => __( 'Allow Null?', 'acf' ),
			'type'         => 'radio',
			'name'         => 'allow_null',
			'layout'       => 'horizontal',
			'choices'      => array(
				1 => __( 'Yes' ),
				0 => __( 'No' ),
			),
		) );
			
	}
    
    /**
	 * Gets a list of Bookmarkss indexed by their Bookmarks IDs.
	 *
	 * @param bool $allow_null If true, prepends the null option.
	 *
	 * @return array An array of Bookmarkss indexed by their Bookmarks IDs.
	 */
    private function get_bookmark_categories( $allow_null = false ) {
		$links = get_terms( 'link_category' );

		$link_cats = array();

		if ( $allow_null ) {
			$link_cats[''] = ' - Select - ';
		}

		foreach ( $links as $link ) {
			$link_cats[$link->term_id] = $link->name;
		}

		return $link_cats;
	}
	
    /**
	 * Renders the Bookmarks Field.
	 *
	 * @param int   $value   The Bookmarks ID selected for this Bookmarks Field.
	 * @param int   $post_id The Post ID this $value is associated with.
	 * @param array $field   The array representation of the current Bookmarks Field.
	 *
	 * @return mixed The Bookmarks ID, or the Bookmarks HTML, or the Bookmarks Object, or false.
	 */
	public function format_value( $value, $post_id, $field ) {
		// bail early if no value
		if ( empty( $value ) ) {
			return false;
        }
        
		// Just return the Bookmarks Category ID
		return $value;
	}
	
}


// initialize
acf_register_field_type( 'acf_field_bookmarks' );

endif; // class_exists check

?>
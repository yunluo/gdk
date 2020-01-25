<?php

if( ! class_exists('acf_field_image_select') ) :

class acf_field_image_select extends acf_field
{
	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function initialize() {
		// vars
		$this->name        = 'image_select';
		$this->label       = __('可视化选择');
		$this->category    = __("Choice",'acf');
		$this->defaults    = array(
			'choices'            =>	array(),
			'default_value'      =>	'',
			'multiple'           => 0,
			'image_get_function' => '',
			'other_choice'       => 0,
			'save_other_choice'  => 0,
			'allow_null'         => 0,
			'return_format'      => 'value'
		);

	}
	
		
	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function render_field( $field )
	{
		// vars
		$i = 0;
		$e = '<ul class="acf-image-select-list acf-radio-list acf-bl ' . esc_attr($field['class']) . '" data-image-select-multiple="'.$field['multiple'].'">';
		
		// add choices
		if( is_array($field['choices']) )
		{
			foreach( $field['choices'] as $key => $value )
			{

				// vars
				$i++;
				$atts  = '';
				$class = '';
				
				if( ! is_array($field['value']) )
				{
					if( strval($key) === strval($field['value']) )
					{
						$atts = 'checked="checked" data-checked="checked"';
						$class = 'acf-image-select-selected';
					}
				}

				if( !empty( $field['image_get_function'] ) && function_exists( $field['image_get_function'] ) ){

					$src = call_user_func( $field['image_get_function'], esc_attr($key) );

				}else{
					$src = '';
				}

				// HTML
				$field_id = esc_attr($field['id']) . '-' . esc_attr($key);
				$e .= '<li class="acf-image-select">';
					
					$e .= '<label for="' . $field_id . '" class="'.$class.'">';
						$e .= '<input id="' . $field_id . '" class="item-input" type="radio" name="' . esc_attr($field['name']) . '" value="' . esc_attr($key) . '" ' .  $atts  . ' />';
						$e .= '<img class="item-image ' . $field_id . '-image" alt="'.$value.'" src="'.$src.'">';
						$e .= '<br/>';
						$e .= '<span class="item-title ' . $field_id . '-title">'.$value.'</span>';
					$e .= '</label>';
				$e .= '</li>';
			}
		}
		
		$e .= '</ul>';
		
		echo $e;
		
	}
	
	function render_field_settings($field) {
		$field['choices'] = acf_encode_choices($field['choices']);
		acf_render_field_setting($field, array(
			'label' => __("选择项"),
			'type'	=>	'textarea',
			'name'	=>	'choices',
			'instructions' => "输入选项，每行一个。<br /><span style='color:#BC0B0B'>请注意：</span> 各选项的键名代表调用图片获取函数时传的参数。<br>如 '<strong>Blue</strong>' 或 '<strong>blue : Blue</strong>'，调用图片将是 '<strong>function('blue')</strong>'"
		));
		acf_render_field_setting($field, array(
			'label'	=> __('默认值'),
			'type'	=>	'text',
			'name'	=>	'default_value',
		));
		acf_render_field_setting($field, array(
			'label'	=> __('允许多选？'),
			'name'	=>	'multiple',
			'type'	=> 'radio',
			'choices'	=>	array(
				1	=>	__("是",'acf'),
				0	=>	__("否",'acf'),
			),
			'layout'	=>	'horizontal',
		));

		acf_render_field_setting($field, array(
			'label'	=> _('图片获取函数'),
			'type'	=>	'text',
			'name'	=>	'image_get_function',
		));
	}
	
	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add css + javascript to assist your create_field() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	function input_admin_enqueue_scripts()
	{
        wp_enqueue_script('acf-image-select');
	}
	
	
	/*
	*  update_field()
	*
	*  This filter is appied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*  @param	$post_id - the field group ID (post_type = acf)
	*
	*  @return	$field - the modified field
	*/
	
	function update_field( $field ) {
		
		// decode choices (convert to array)
		$field['choices'] = acf_decode_choices($field['choices']);
		
		// return
		return $field;
	}
	function update_value( $value, $post_id, $field ) {
		
		// validate
		if( empty($value) ) {
		
			return $value;
			
		}
		
		
		// array
		if( is_array($value) ) {
			
			// save value as strings, so we can clearly search for them in SQL LIKE statements
			$value = array_map('strval', $value);
			
		}
		
		
		// return
		return $value;
	}
	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/
	function format_value($value, $post_id, $field)
	{
		// bail early if no value
		if( empty($value) ) {
			return $value;	
		}
		// get value
		$retvalue = esc_attr($value);
		// format value
		// return value
		return $retvalue;
	}
	
}
acf_register_field_type( 'acf_field_image_select' );
endif;
?>
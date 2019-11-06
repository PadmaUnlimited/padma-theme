<?php
class PadmaElementAPI {


	public static $elements = array();	
	public static $groups 	= array();


	public static function init() {

		add_action('padma_visual_editor_ajax_pre_get_design_editor_elements', array(__CLASS__, 'register_elements_hook'));
		add_action('padma_visual_editor_ajax_pre_get_design_editor_elements', array(__CLASS__, 'register_elements_instances_hook'));

		add_action('padma_before_get_block_styling', array(__CLASS__, 'register_elements_hook'));
		add_action('padma_before_get_block_styling', array(__CLASS__, 'register_elements_instances_hook'));

		add_action('padma_dynamic_style_design_editor_init', array(__CLASS__, 'register_elements_hook'));
		add_action('padma_dynamic_style_design_editor_init', array(__CLASS__, 'register_elements_instances_hook'));

	}


	public static function register_elements_hook() {

		if ( did_action('padma_register_elements') )
			return;

		//Add a central action where we can register all elements to.  This will be performance increase in the long run 
		//since elements will only be registered when they need to be.
		do_action('padma_register_elements');

	}


	public static function register_elements_instances_hook() {

		if ( did_action('padma_register_elements_instances') )
			return;

		//Add a central action where we can register all elements to.  This will be performance increase in the long run 
		//since elements will only be registered when they need to be.
		do_action('padma_register_elements_instances');

	}


	public static function get_all_elements() {

		return self::$elements;

	}


	public static function get_groups() {

		return self::$groups;

	}


	public static function get_element($element) {

		return padma_get($element, self::$elements);

	}


	public static function get_block_elements($block_type) {

		$children = array();

		foreach ( self::get_all_elements() as $element_id => $element_info ) {

			if ( empty($element_info['parent']) || strpos($element_info['parent'], 'block-' . $block_type) !== 0 )
				continue;

			$children[$element_id] = $element_info;

		}

		return $children; 

	}


	public static function get_instances($element) {

		if ( !is_array($element) )
			$element = self::get_element($element);

		return isset($element['instances']) ? $element['instances'] : false;

	}


	public static function get_states($element) {

		if ( !is_array($element) )
			$element = self::get_element($element);

		return isset($element['states']) ? $element['states'] : false;

	}


	public static function register_element($args) {

		if ( !is_array($args) )
			return new WP_Error('pu_elements_register_element_args_not_array', __('Error: Arguments must be an array for this element.', 'padma'), $args);

		$defaults = array(
			'group' => null,
			'parent' => null,
			'id' => null,
			'name' => null,
			'selector' => null,
			'properties' => array('fonts', 'background', 'borders', 'outlines', 'padding', 'margins', 'corners', 'box-shadow', 'lists', 'nudging', 'overflow', 'sizes', 'animation', 'transform', 'transition', 'advanced', 'filter', 'flexbox'),
			'states' => array(),
			'instances' => array(),
			'default-element' => false,
			'supports-instances' => true,
			'inspectable' => true
		);

		$item = array_merge($defaults, $args);

		//If the element is set to default, change the group to default
		if ( $item['default-element'] === true ) 
			$item['group'] = 'default-elements';

		//If requirements are not met, throw errors
		if ( !$item['id'] )
			return new WP_Error('pu_elements_register_element_no_id', __('Error: An ID is required for this element.', 'padma'), $item);

		if ( !$item['name'] )
			return new WP_Error('pu_elements_register_element_no_name', __('Error: A name is required for this element.', 'padma'), $item);	

		if ( $item['group'] === null && $item['default-element'] === false )
			return new WP_Error('pu_elements_register_element_no_group', __('Error: A group is required for this element.', 'padma'), $item);	

		if ( $item['selector'] === null  && $item['default-element'] === false )
			return new WP_Error('pu_elements_register_element_no_selector', __('Error: A CSS selector is required for this element.', 'padma'), $item);

		if ( $item['properties'] === array() )
			return new WP_Error('pu_elements_register_element_no_properties', __('Error: Properties are required for this element.', 'padma'), $item);	

		//Add the guts
		$destination =& self::$elements[$item['id']];
		$destination = $item;

		//Remove the empty options
		if ( $destination['parent'] === null )
			unset($destination['parent']);

		if ( $destination['states'] === array() ) {
			unset($destination['states']);

		/* Format states array */
		} else {

			$unformated_states_array = $destination['states'];
			$destination['states'] = array();

			foreach ( $unformated_states_array as $state_name => $state_selector ) {

				$destination['states'][strtolower($state_name)] = array(
					'name' => $state_name,
					'id' => strtolower($state_name),
					'selector' => $state_selector
				);

			}

		}

		if ( $destination['instances'] === array() )
			unset($destination['instances']);

		//The element is now registered!
		return $destination;

	}


	public static function register_element_instance($args) {

		if ( !is_array($args) )
			return new WP_Error('pu_elements_register_element_instance_args_not_array', __('Error: Arguments must be an array for this element instance.', 'padma'), $args);

		$defaults = array(
			'group' => null,
			'element' => null,
			'id' => null,
			'name' => null,
			'selector' => null,
			'layout' => null,
			'state-of' => null
		);

		$item = array_merge($defaults, $args);

		//If requirements are not met, throw errors
		if ( !$item['id'] )
			return new WP_Error('pu_elements_register_element_instance_no_id', __('Error: An ID is required for this element instance.', 'padma'), $item);

		if ( !$item['name'] )
			return new WP_Error('pu_elements_register_element_instance_no_name', __('Error: A name is required for this element instance.', 'padma'), $item);	

		if ( $item['group'] === null )
			return new WP_Error('pu_elements_register_element_instance_no_group', __('Error: A group is required for this element instance.', 'padma'), $item);	

		if ( $item['element'] === null )
			return new WP_Error('pu_elements_register_element_instance_no_parent', __('Error: A parent element is required for this element instance.', 'padma'), $item);

		if ( $item['selector'] === null )
			return new WP_Error('pu_elements_register_element_instance_no_selector', __('Error: A CSS selector is required for this element instance.', 'padma'), $item);

		//If layout is set, then set layout-name as well
		if ( $item['layout'] )
			$item['layout-name'] = PadmaLayout::get_name($item['layout']);

		//Figure out where the element will go in the elements array
		if ( isset(self::$elements[$item['element']]) )
			$destination =& self::$elements[$item['element']];
		else
			return false;

		//Make sure that the element supports instances
		if ( !padma_get('supports-instances', $destination) )
			return false;

		$destination =& $destination['instances'][$item['id']];

		//Add the guts
		$destination = $item;

		//Remove the extra options
		unset($destination['element']);
		unset($destination['group']);

		//The element instance is now registered!
		return $destination;

	}


	public static function register_group($id, $info) {

		//Group already exists
		if ( isset(self::$groups[$id]) )
			return new WP_Error('pu_elements_register_group_already_exists', __('Error: The group being registered already exists.', 'padma'), $id);

		/* Groups now accept both name and description in form of array @since 3.7 so we need backwards compatibility */
		if ( is_string($info) ) {

			$info = array(
				'name' => $info
			);

		}

		//Place group in groups array so we can track name and description
		self::$groups[$id] = $info;

		return true;

	}


}
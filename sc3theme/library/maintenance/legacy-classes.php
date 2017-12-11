<?php
class BloxElementsData_Upgrade34 {


	/* Mass Get */
	public static function get_all_elements() {

		$default_data = self::get_default_data();

		//Set up the main array to be returned
		$elements = array();

		//Get all of the design editor option groups by looking at the option groups catalog
		$option_groups = get_option( 'blox_option_groups' );

		//Pull out only the design editor groups.  Since blox_option_groups uses true for every value
		//and the group is actually the key, we must pull the keys out using array_keys
		$design_editor_groups = array_filter( array_keys( $option_groups ), create_function( '$group', 'return (strpos($group, \'design-editor-group-\') !== false);' ) );

		//Loop through all of the groups and get every element and its properties
		foreach ( $design_editor_groups as $design_editor_group ) {

			$group = get_option( 'blox_option_group_' . $design_editor_group );

			//Merge the current group into the array to be returned
			$elements = array_merge( $elements, array_map( 'maybe_unserialize', $group ) );

		}

		//Merge in the default element data
		if ( is_array( $default_data ) ) {
			$elements = blox_array_merge_recursive_simple( $default_data, $elements );
		}

		//Move default elements to the top
		foreach ( $elements as $element_id => $element_options ) {

			$element = BloxElementAPI::get_element( $element_id );

			if ( ! isset( $element['default-element'] ) || $element['default-element'] === false ) {
				continue;
			}

			$temp_id = $element_id;
			$temp_options = $element_options;

			unset( $elements[ $element_id ] );

			$elements = array_merge( array( $temp_id => $temp_options ), $elements );

		}

		return $elements;

	}


	public static function get_element_properties( $element_id ) {

		$default_data = self::get_default_data();

		//Make sure the element is registered
		if ( ! isset( BloxElementAPI::$elements[ $element_id ] ) ) {
			return new WP_Error( 'element_not_registered', __( 'The element ID is not registered.', 'blox' ), $element_id );
		}

		//Set vars up and get stuff from database
		$group = BloxElementAPI::$elements[ $element_id ]['group'];
		$element = BloxOption::get( $element_id, 'design-editor-group-' . $group, array( 'properties' => array() ) );

		if ( ! isset( $element['properties'] ) || ! is_array( $element['properties'] ) ) {
			$element['properties'] = array();
		}

		//If there are default properties for the element we're on, use them.
		if ( is_array( $default_data ) && isset( $default_data[ $element_id ] ) ) {
			$properties = array_merge( $default_data[ $element_id ]['properties'], $element['properties'] );
		} else {
			$properties = $element['properties'];
		}

		//Fetch the property
		return ( is_array( $properties ) && count( $properties ) > 0 ) ? $properties : array();

	}


	public static function get_special_element_properties( $element_id, $se_type, $se_meta ) {

		$default_data = self::get_default_data();

		//Make sure the element is registered
		if ( ! isset( BloxElementAPI::$elements[ $element_id ] ) ) {
			return new WP_Error( 'element_not_registered', __( 'The element ID is not registered.', 'blox' ), $element_id );
		}

		//Set vars up and get stuff from database
		$group = BloxElementAPI::$elements[ $element_id ]['group'];

		$element = BloxOption::get( $element_id, 'design-editor-group-' . $group, array(
			'special-element-' . $se_type => array(
				$se_meta => array()
			)
		) );

		if ( ! isset( $element[ 'special-element-' . $se_type ][ $se_meta ] ) || ! is_array( $element[ 'special-element-' . $se_type ][ $se_meta ] ) ) {
			$element[ 'special-element-' . $se_type ][ $se_meta ] = array();
		}

		$properties =& $element[ 'special-element-' . $se_type ][ $se_meta ];

		//If there are default properties for the element we're on, use them.
		if ( is_array( $default_data ) && isset( $default_data[ $element_id ][ 'special-element-' . $se_type ][ $se_meta ] ) ) {
			$properties = array_merge( $default_data[ $element_id ][ 'special-element-' . $se_type ][ $se_meta ], $properties );
		}

		//Return the data
		return ( is_array( $properties ) && count( $properties ) > 0 ) ? $properties : array();

	}


	/* Single Get */
	public static function get_property( $element_id, $property_id, $default = null ) {

		$properties = self::get_element_properties( $element_id );

		if ( $properties !== null && ! is_wp_error( $properties ) && isset( $properties[ $property_id ] ) ) {
			return blox_fix_data_type( $properties[ $property_id ] );
		} else {
			return $default;
		}

	}


	public static function get_special_element_property( $element_id, $se_type, $se_meta, $property_id, $default = null ) {

		$properties = self::get_special_element_properties( $element_id, $se_type, $se_meta );

		if ( $properties !== null && ! is_wp_error( $properties ) && isset( $properties[ $property_id ] ) ) {
			return blox_fix_data_type( $properties[ $property_id ] );
		} else {
			return $default;
		}

	}


	public static function get_inherited_property( $element_id, $property_id, $default = null ) {

		//Check for normal property first.  Need this for recursion and for instances/states.
		if ( $normal_property = self::get_property( $element_id, $property_id ) ) {
			return $normal_property;
		}

		//Check for inherit location right away.
		$inherit_location = BloxElementAPI::get_inherit_location( $element_id );

		//If inherit location does not exist, go straight to default.
		if ( ! $inherit_location ) {
			return $default;
		} //If it does exist, loop this function through again
		else {
			return self::get_inherited_property( $inherit_location, $property_id, $default );
		}

	}


	/* Setting */
	public static function set_property( $element_id, $property_id, $value, $forced_group = false ) {

		/* Allow set_property to be ran during maintenance */
		if ( ! $forced_group ) {

			//Make sure the element is registered
			if ( ! isset( BloxElementAPI::$elements[ $element_id ] ) ) {
				return new WP_Error( 'element_not_registered', __( 'The element ID is not registered.', 'blox' ), $element_id );
			}

			//Set vars up and get stuff from database
			$group = BloxElementAPI::$elements[ $element_id ]['group'];

		} else {

			$group = $forced_group;

		}

		$element = BloxOption::get( $element_id, 'design-editor-group-' . $group, array( 'properties' => array() ) );

		//Set the property
		if ( $value == 'null' ) {
			$value = null;
		}

		$element['properties'][ $property_id ] = $value;

		//Send it back to DB
		BloxOption::set( $element_id, $element, 'design-editor-group-' . $group );

		return true;

	}


	public static function set_special_element_property( $element_id, $special_element_type, $special_element_meta, $property_id, $value ) {

		//Make sure the element is registered
		if ( ! isset( BloxElementAPI::$elements[ $element_id ] ) ) {
			return new WP_Error( 'element_not_registered', __( 'The element ID is not registered.', 'blox' ), $element_id );
		}

		//Set vars up and get stuff from database
		$group = BloxElementAPI::$elements[ $element_id ]['group'];
		$element = BloxOption::get( $element_id, 'design-editor-group-' . $group, array(
			'special-element-' . $special_element_type => array(
				$special_element_meta => array()
			)
		) );

		//Set the property
		if ( $value == 'null' ) {
			$value = null;
		}

		$element[ 'special-element-' . $special_element_type ][ $special_element_meta ][ $property_id ] = $value;

		//Send it back to DB
		BloxOption::set( $element_id, $element, 'design-editor-group-' . $group );

		return true;

	}


	/* Deleting */
	public static function delete_property( $element_id, $property_id ) {

		//Make sure the element is registered
		if ( ! isset( BloxElementAPI::$elements[ $element_id ] ) ) {
			return new WP_Error( 'element_not_registered', __( 'The element ID is not registered.', 'blox' ), $element_id );
		}

		//Set vars up and get stuff from database
		$group = BloxElementAPI::$elements[ $element_id ]['group'];
		$element = BloxOption::get( $element_id, 'design-editor-group-' . $group, array( 'properties' => array() ) );

		//Remove the property or return false if it can't
		if ( isset( $element['properties'][ $property_id ] ) ) {
			unset( $element['properties'][ $property_id ] );
		} else {
			return false;
		}

		//Send it back to DB
		BloxOption::set( $element_id, $element, 'design-editor-group-' . $group );

		return true;

	}


	public static function delete_element( $element_id ) {

		//Make sure the element is registered
		if ( ! isset( BloxElementAPI::$elements[ $element_id ] ) ) {
			return new WP_Error( 'element_not_registered', __( 'The element ID is not registered.', 'blox' ), $element_id );
		}

		$group = BloxElementAPI::$elements[ $element_id ]['group'];

		//Delete the element
		BloxOption::delete( $element_id, 'design-editor-group-' . $group );

		return true;

	}


	public static function delete_all() {

		$option_groups = get_option( 'blox_option_groups', array() );

		foreach ( $option_groups as $group_name => $group_name_bool_unused ) {

			if ( strpos( $group_name, 'design-editor-group-' ) !== 0 ) {
				continue;
			}

			update_option( 'blox_option_group_' . $group_name, array() );

		}

	}


	/* Defaults */
	public static function get_default_data() {

		global $blox_default_element_data;

		return apply_filters( 'blox_element_data_defaults', $blox_default_element_data );

	}


}


class BloxLayoutOption_Upgrade34 {


	/**
	 * Set the default group for all of the database functions to get, set, and delete from.
	 **/
	protected static $default_group = 'general';


	/**
	 * Group suffix.  Used for things like previewing, etc.  If previewing, use '_preview' as the suffix.
	 **/
	public static $group_suffix = null;


	public static function init() {

		if ( blox_get( 'preview' ) && BloxCapabilities::can_user_visually_edit() ) {

			BloxOption::$group_suffix = '_preview';
			BloxLayoutOption_Upgrade34::$group_suffix = '_preview';

		}

	}


	public static function format_layout_id( $layout ) {

		//Create array to analyze last part of layout string
		$fragments = explode( '-', $layout );

		//If it's a single layout
		if ( strpos( $layout, 'single' ) !== false && is_numeric( end( $fragments ) ) ) {
			$layout = (int) end( $fragments );
		}

		//If the layout is numeric, check that it's not the blog index or front page
		if ( is_numeric( $layout ) && get_option( 'page_for_posts' ) == $layout ) {
			return 'index';
		} elseif ( is_numeric( $layout ) && get_option( 'page_on_front' ) == $layout ) {
			return 'front_page';
		}

		return $layout;

	}


	public static function get( $layout = false, $option = null, $group_name = false, $default = null ) {

		//If there's no option to retrieve, then we have nothing to retrieve.
		if ( $option === null ) {
			return null;
		}

		//If there's no group defined, define it using the default
		if ( ! $group_name ) {
			$group_name = self::$default_group;
		}

		//Make sure there is a layout to use
		if ( ! $layout ) {
			$layout = BloxLayout::get_current();
		}

		//Format layout ID
		$layout = self::format_layout_id( $layout );

		$options = get_option( 'blox_layout_options_' . str_replace( '-', '_', $layout ) . self::$group_suffix );

		if ( self::$group_suffix && ! $options ) {
			$options = get_option( 'blox_layout_options_' . str_replace( '-', '_', $layout ) );
		}

		//Option does not exist
		if ( ! $options || ! isset( $options[ $group_name ][ $option ] ) || ! is_array( $options ) ) {
			return $default;
		}

		//Option exists, let's format it
		$data = blox_fix_data_type( $options[ $group_name ][ $option ] );

		return $data;

	}


	public static function set( $layout = false, $option = null, $value = null, $group_name = false ) {

		//If there's no option, we can't set anything.
		if ( $option === null ) {
			return false;
		}

		//If there's no value, there's nothing to set.
		if ( $value === null ) {
			return false;
		}

		//If there's no group defined, define it using the default
		if ( ! $group_name ) {
			$group_name = self::$default_group;
		}

		//Make sure there is a layout to use
		if ( ! $layout ) {
			$layout = BloxLayout::get_current();
		}

		//Format layout ID
		$layout = self::format_layout_id( $layout );

		//Handle boolean values
		if ( is_bool( $value ) ) {
			$value = ( $value === true ) ? 'true' : 'false';
		}

		//Change hyphens to underscores
		$layout = str_replace( '-', '_', $layout );

		//Retrieve existing options
		$options = get_option( 'blox_layout_options_' . $layout );

		//Get layout options catalog
		$catalog = get_option( 'blox_layout_options_catalog' );

		//Make sure layout exists in catalog
		if ( ! is_array( $catalog ) ) {
			$catalog = array();
		}

		if ( ! in_array( $layout, $catalog ) ) {
			$catalog[] = $layout;
		}

		//If options aren't set, make it an array
		if ( ! is_array( $options ) ) {
			$options = array( $group_name => array() );
		}

		//Make sure group exists
		if ( ! isset( $options[ $group_name ] ) ) {
			$options[ $group_name ] = array();
		}

		//Update data on array
		$options[ $group_name ][ $option ] = $value;

		//Send data to DB
		update_option( 'blox_layout_options_' . $layout . self::$group_suffix, $options );

		if ( ! self::$group_suffix ) {
			update_option( 'blox_layout_options_catalog', $catalog );
		}

		return true;


	}


	public static function delete( $layout, $option = null, $group_name = false ) {

		//No deleting to be done if we don't have an option to delete
		if ( $option === null ) {
			return false;
		}

		//If there's no group defined, define it using the default
		if ( ! $group_name ) {
			$group_name = self::$default_group;
		}

		//Make sure there is a layout to use
		if ( ! $layout ) {
			$layout = BloxLayout::get_current();
		}

		//Format layout ID
		$layout = self::format_layout_id( $layout );

		//Retrieve options array from DB
		$options = get_option( 'blox_layout_options_' . str_replace( '-', '_', $layout ) );

		//If DB option doesn't exist, make a default array
		if ( ! is_array( $options ) ) {
			$options = array();
		}

		//Option or group doesn't exist
		if ( ! isset( $options[ $group_name ] ) || ! isset( $options[ $group_name ][ $option ] ) ) {
			return false;
		}

		//If option exists, delete the sucker
		unset( $options[ $group_name ][ $option ] );

		//If group is empty, delete it too
		if ( count( $options[ $group_name ] ) === 0 ) {
			unset( $options[ $group_name ] );
		}

		//If the options array is empty, delete the entire option and remove it from catalog
		if ( count( $options ) === 0 && ! self::$group_suffix ) {

			$removal = array( $layout );
			$catalog = array_diff( get_option( 'blox_layout_options_catalog' ), $removal );

			delete_option( 'blox_layout_options_' . $layout );
			update_option( 'blox_layout_options_catalog', $catalog );

			return true;

		}

		update_option( 'blox_layout_options_' . $layout . self::$group_suffix, $options );

		return true;


	}


	public static function delete_all_from_layout( $layout ) {

		//Format layout ID
		$layout = str_replace( '-', '_', self::format_layout_id( $layout ) );
		$catalog = array_diff( get_option( 'blox_layout_options_catalog' ), array( $layout ) );

		delete_option( 'blox_layout_options_' . $layout );
		update_option( 'blox_layout_options_catalog', $catalog );

		return true;

	}


}


class BloxBlocksData_Upgrade34 {


	protected static $schema_cache = array();


	protected static function schema_blocks_by_id( $use_cache = false ) {

		if ( $use_cache && $cached = blox_get( 'blocks-by-id', self::$schema_cache ) ) {
			return $cached;
		}

		/* Retrieve the option from DB */
		$blocks_by_id = BloxOption::get( 'blocks-by-id', 'blocks', array() );

		/* Cache it */
		self::$schema_cache['blocks-by-id'] = $blocks_by_id;

		/* Return it */

		return $blocks_by_id;

	}


	protected static function schema_blocks_by_type() {

		/* Retrieve the option from DB */
		$blocks_by_type = BloxOption::get( 'blocks-by-type', 'blocks', array() );

		/* Return it */

		return $blocks_by_type;

	}


	protected static function schema_blocks_by_layout() {

		/* Retrieve the option from DB */
		$blocks_by_layout = BloxOption::get( 'blocks-by-layout', 'blocks', array() );

		/* Return it */

		return $blocks_by_layout;

	}


	protected static function schema_layout_blocks( $layout_id ) {

		/* Retrieve the option from DB */
		$layout_blocks = BloxLayoutOption_Upgrade34::get( $layout_id, 'blocks', false, array() );

		/* Return it */

		return $layout_blocks;

	}


	public static function add_block( $layout_id, $args ) {

		//Lots of defaults here.
		$defaults = array(
			'type' => null,
			'wrapper' => null,
			'position' => array(
				'top' => 0,
				'left' => 0
			),
			'dimensions' => array(
				'width' => 0,
				'height' => 0
			),
			'settings' => array()
		);

		//Merge defaults with arguments
		$block_settings = array_merge( $defaults, $args );

		//Check requirements for block
		if ( $block_settings['type'] === $defaults['type'] ) {
			return false;
		}

		//Figure out block ID
		$block_id = ( isset( $block_settings['id'] ) && ! self::block_exists( $block_settings['id'], false ) ) ? $block_settings['id'] : self::get_available_block_id( array(), false );

		//Re-add block ID to array
		$block_settings['id'] = $block_id;

		//Get existing blocks from layout
		$layout_blocks = self::schema_layout_blocks( $layout_id );

		//Fetch the big boy option that all blocks belong to
		$blocks_by_type = self::schema_blocks_by_type();
		$blocks_by_id = self::schema_blocks_by_id();
		$blocks_by_layout = self::schema_blocks_by_layout();

		//Add the block to the layout's block array
		$layout_blocks[ $block_id ] = $block_settings;

		//Add block to global array(s)
		$blocks_by_type[ $block_settings['type'] ][ $block_id ] = $layout_id;
		$blocks_by_id[ $block_id ] = array( 'layout' => $layout_id, 'type' => $block_settings['type'] );
		$blocks_by_layout[ $layout_id ][ $block_id ] = true;

		//Update database
		BloxLayoutOption_Upgrade34::set( $layout_id, 'blocks', $layout_blocks );

		BloxOption::set( 'blocks-by-type', $blocks_by_type, 'blocks' );
		BloxOption::set( 'blocks-by-id', $blocks_by_id, 'blocks' );
		BloxOption::set( 'blocks-by-layout', $blocks_by_layout, 'blocks' );

		//All done.  Spit back ID of newly created block.
		return $block_id;

	}


	public static function update_block( $layout_id, $block_id, $args ) {

		//Get existing blocks layout
		$blocks_by_type = self::schema_blocks_by_type();
		$blocks_by_id = self::schema_blocks_by_id();

		$layout_blocks = self::schema_layout_blocks( $layout_id );

		//If block doesn't exist, go false.
		if ( ! isset( $layout_blocks[ $block_id ] ) ) {
			return false;
		}

		//Pull out block settings from block we're gonna update.
		$old_block = $layout_blocks[ $block_id ];
		$updated_block = array_merge( $old_block, $args );

		//Merge new block settings with old and update array
		$layout_blocks[ $block_id ] = $updated_block;

		//Since we're not sure if the type is being updated, we'll update it anyway for blocks-by-type and blocks-by-id
		if ( isset( $blocks_by_type[ $old_block['type'] ][ $block_id ] ) ) {
			unset( $blocks_by_type[ $old_block['type'] ][ $block_id ] );
		}

		$blocks_by_type[ $updated_block['type'] ][ $block_id ] = $layout_id;

		$blocks_by_id[ $block_id ]['type'] = $updated_block['type'];

		//Push new arrays to DB
		BloxLayoutOption_Upgrade34::set( $layout_id, 'blocks', $layout_blocks );

		BloxOption::set( 'blocks-by-type', $blocks_by_type, 'blocks' );
		BloxOption::set( 'blocks-by-id', $blocks_by_id, 'blocks' );

		//Everything OK
		return true;

	}


	public static function delete_block( $layout_id, $block_id ) {

		//Fetch options from DB
		$layout_blocks = self::schema_layout_blocks( $layout_id );

		$blocks_by_type = self::schema_blocks_by_type();
		$blocks_by_id = self::schema_blocks_by_id();
		$blocks_by_layout = self::schema_blocks_by_layout();

		//Find anomolies (going to ignore blocks by type array here)
		if ( ! isset( $layout_blocks[ $block_id ] ) ) {
			return false;
		}

		//Get block type
		$block_type = $blocks_by_id[ $block_id ]['type'];

		//Strip block out of arrays
		unset( $layout_blocks[ $block_id ] );

		unset( $blocks_by_type[ $block_type ][ $block_id ] );
		unset( $blocks_by_id[ $block_id ] );
		unset( $blocks_by_layout[ $layout_id ][ $block_id ] );

		if ( count( $blocks_by_type[ $block_type ] ) === 0 ) {
			unset( $blocks_by_type[ $block_type ] );
		}

		if ( count( $blocks_by_layout[ $layout_id ] ) === 0 ) {
			unset( $blocks_by_layout[ $layout_id ] );
		}

		//Update database
		BloxLayoutOption_Upgrade34::set( $layout_id, 'blocks', $layout_blocks );

		BloxOption::set( 'blocks-by-type', $blocks_by_type, 'blocks' );
		BloxOption::set( 'blocks-by-id', $blocks_by_id, 'blocks' );
		BloxOption::set( 'blocks-by-layout', $blocks_by_layout, 'blocks' );

		//Everything successful
		return true;

	}


	public static function delete_by_layout( $layout_id ) {

		//This function is only used when the grid is active.
		if ( ! current_theme_supports( 'blox-grid' ) ) {
			return false;
		}

		//Fetch options from DB
		$layout_blocks = self::schema_layout_blocks( $layout_id );

		$blocks_by_type = self::schema_blocks_by_type();
		$blocks_by_id = self::schema_blocks_by_id();
		$blocks_by_layout = self::schema_blocks_by_layout();

		foreach ( $layout_blocks as $block_id => $options ) {

			//Strip block out of arrays
			unset( $layout_blocks[ $block_id ] );

			unset( $blocks_by_type[ $options['type'] ][ $block_id ] );
			unset( $blocks_by_id[ $block_id ] );
			unset( $blocks_by_layout[ $layout_id ][ $block_id ] );

			if ( count( $blocks_by_type[ $options['type'] ] ) === 0 ) {
				unset( $blocks_by_type[ $options['type'] ] );
			}

			if ( count( $blocks_by_layout[ $layout_id ] ) === 0 ) {
				unset( $blocks_by_layout[ $layout_id ] );
			}

		}

		//Update database
		BloxLayoutOption_Upgrade34::set( $layout_id, 'blocks', $layout_blocks );

		BloxOption::set( 'blocks-by-type', $blocks_by_type, 'blocks' );
		BloxOption::set( 'blocks-by-id', $blocks_by_id, 'blocks' );
		BloxOption::set( 'blocks-by-layout', $blocks_by_layout, 'blocks' );

		//Everything successful
		return true;


	}


	public static function get_block( $block, $use_mirrored = false ) {

		/* If a block array is supplied, make sure it is legitimate. */
		if ( is_array( $block ) ) {

			if ( ! isset( $block['id'] ) && ! blox_get( 'new', $block, false ) ) {
				return null;
			}

			/* Fetch the block based off of ID */
		} elseif ( is_numeric( $block ) ) {

			//Get the block from blocks-by-id to get the layout
			$blocks_by_id = self::schema_blocks_by_id();

			//If block doesn't exist, go false
			if ( ! isset( $blocks_by_id[ $block ] ) ) {
				return false;
			}

			//Retrieve all blocks from layout
			$layout_blocks = self::get_blocks_by_layout( blox_get( 'layout', $blocks_by_id[ $block ] ) );

			//Make sure that the block still exists once again on the layout.
			if ( ! isset( $layout_blocks[ $block ] ) ) {
				return false;
			}

			$block = $layout_blocks[ $block ];

			/* No valid argument provided. */
		} else {

			return null;

		}

		/* Fetch the mirrored block if $use_mirrored is true */
		if ( $use_mirrored === true && $mirrored_block = self::is_block_mirrored( $block ) ) {
			$block = $mirrored_block;
		}

		return $block;

	}


	public static function get_blocks_by_layout( $layout_id, $include_design_editor_instances = false ) {

		/* Retrieve all blocks from layout */
		$layout_blocks = self::schema_layout_blocks( $layout_id );

		/* Load in elements if including Design Editor instances */
		if ( $include_design_editor_instances ) {
			BloxElementAPI::register_elements_hook();
		}

		/* Add the layout ID and design editor instances in */
		foreach ( $layout_blocks as $block_id => $block ) {

			$layout_blocks[ $block_id ]['layout'] = $layout_id;

			/* Pull in Design Editor instances if set to do so */
			if ( $include_design_editor_instances ) {

				$block_element = BloxElementAPI::get_element( 'block-' . $layout_blocks[ $block_id ]['type'] );

				/* Set up styling array */
				$layout_blocks[ $block_id ]['styling'] = array();

				/* Get block instance styling */
				$block_instance_properties = BloxElementsData::get_special_element_properties( array(
					'element' => $layout_blocks[ $block_id ]['type'] . '-block',
					'se_type' => 'instance',
					'se_meta' => $layout_blocks[ $block_id ]['type'] . '-block-' . $block_id,
					'element_group' => 'blocks'
				) );

				if ( ! empty( $block_instance_properties ) ) {

					$layout_blocks[ $block_id ]['styling'][ $layout_blocks[ $block_id ]['type'] . '-block-' . $block_id ] = array(
						'element' => $layout_blocks[ $block_id ]['type'] . '-block',
						'properties' => $block_instance_properties
					);

				}

				/* Get block children element instances (which could be a LOT) */
				foreach ( blox_get( 'children', $block_element, array() ) as $block_element_sub_element ) {

					/* Make sure that the element supports instances */
					if ( ! blox_get( 'supports-instances', $block_element_sub_element ) ) {
						continue;
					}

					$sub_element_instance_id = $block_element_sub_element['id'] . '-block-' . $block_id;

					$sub_element_instance_properties = BloxElementsData::get_special_element_properties( array(
						'element' => $block_element_sub_element['id'],
						'se_type' => 'instance',
						'se_meta' => $sub_element_instance_id,
						'element_group' => 'blocks'
					) );

					/* Only add sub element instance if there are properties present */
					if ( ! empty( $sub_element_instance_properties ) ) {

						$layout_blocks[ $block_id ]['styling'][ $sub_element_instance_id ] = array(
							'element' => $block_element_sub_element['id'],
							'properties' => $sub_element_instance_properties
						);

					}

					/* Instance states */
					if ( ! empty( $block_element_sub_element['states'] ) && is_array( $block_element_sub_element['states'] ) ) {

						foreach ( $block_element_sub_element['states'] as $instance_state_id => $instance_state_info ) {

							$actual_instance_id = $block_element_sub_element['id'] . '-block-' . $block_id . '-state-' . $instance_state_id;
							$instance_state_properties = BloxElementsData::get_special_element_properties( array(
								'element' => $block_element_sub_element['id'],
								'se_type' => 'instance',
								'se_meta' => $actual_instance_id,
								'element_group' => 'blocks'
							) );

							/* Only add instance state if there are properties present */
							if ( empty( $instance_state_properties ) ) {
								continue;
							}

							$layout_blocks[ $block_id ]['styling'][ $actual_instance_id ] = array(
								'element' => $block_element_sub_element['id'],
								'properties' => $instance_state_properties
							);

						}

					}
					/* End getting instance states */

				}


			}
			/* End putting in Design Editor instances */

		}


		return $layout_blocks;

	}


	public static function get_blocks_by_wrapper( $layout_id, $wrapper_id ) {

		$layout_blocks = self::get_blocks_by_layout( $layout_id );
		$wrapper_blocks = array();

		foreach ( $layout_blocks as $block_id => $block ) {

			if ( blox_get( 'wrapper', $block, BloxWrappers::$default_wrapper_id ) === $wrapper_id ) {
				$wrapper_blocks[ $block_id ] = $block;
			}

		}

		return $wrapper_blocks;

	}


	public static function get_blocks_by_type( $type = false ) {

		//Get all blocks from DB
		$blocks_by_type = self::schema_blocks_by_type();

		//If no type, then return it all
		if ( ! $type ) {
			return $blocks_by_type;
		}

		return ( isset( $blocks_by_type[ $type ] ) ) ? $blocks_by_type[ $type ] : null;

	}


	public static function get_all_blocks() {

		//Get a list of layouts with blocks
		if ( ! ( $block_by_layout = self::schema_blocks_by_layout() ) ) {
			return false;
		}

		$blocks = array();

		//Go through and get every layout then get the blocks for that layout and add them to the $blocks array
		foreach ( $block_by_layout as $layout => $unused_block_ids ) {

			$added_blocks = self::get_blocks_by_layout( $layout );

			//Loop through the blocks and put in the layout ID
			foreach ( $added_blocks as $block_id => $block ) {
				$added_blocks[ $block_id ]['layout'] = $layout;
			}

			//Add blocks to existing array
			$blocks = array_merge( $blocks, $added_blocks );

		}

		return $blocks;

	}


	public static function get_block_name( $block ) {

		$block = self::get_block( $block );

		//Create the default name by using the block type and ID
		$default_name = BloxBlocks::block_type_nice( $block['type'] ) . ' #' . $block['id'];

		return blox_get( 'alias', $block['settings'], $default_name );

	}


	public static function get_block_width( $block ) {

		$block = self::get_block( $block );

		$block_grid_width = blox_get( 'width', $block['dimensions'], null );

		if ( $block_grid_width === null ) {
			return null;
		}

		/* Fetch the wrapper that way we can get its Grid settings */
		$wrapper = BloxWrappers::get_wrapper( blox_get( 'wrapper', $block, 'wrapper-default' ) );

		return ( $block_grid_width * ( blox_get( 'column-width', $wrapper ) + blox_get( 'gutter-width', $wrapper ) ) ) - blox_get( 'gutter-width', $wrapper );

	}


	public static function get_block_height( $block ) {

		$block = self::get_block( $block );

		$block_grid_height = blox_get( 'height', $block['dimensions'], null );

		if ( $block_grid_height === null ) {
			return null;
		}

		return $block_grid_height;

	}


	public static function get_block_setting( $block, $setting, $default = null ) {

		$block = self::get_block( $block );

		//No block, no settings
		if ( ! $block ) {
			return $default;
		}

		if ( ! isset( $block['settings'][ $setting ] ) ) {
			return $default;
		}

		return blox_fix_data_type( $block['settings'][ $setting ] );

	}


	public static function get_available_block_id( $block_id_blacklist = array(), $use_block_id_cache = true ) {

		$id = 1;

		while ( self::block_exists( $id, $use_block_id_cache ) || in_array( (string) $id, $block_id_blacklist ) ) {

			$id ++;

		}

		return $id;

	}


	public static function is_block_mirrored( $block, $return_block_id = false ) {

		$block = self::get_block( $block );

		if ( $block && $mirrored_block_id = blox_get( 'mirror-block', $block['settings'] ) ) {

			$mirrored_block = self::block_exists( $mirrored_block_id ) ? self::get_block( $mirrored_block_id ) : false;

			if ( ! $mirrored_block ) {
				return false;
			}

			/* Insure that the block being mirrored is the same type of block */
			if ( blox_get( 'type', $mirrored_block ) != blox_get( 'type', $block ) ) {
				return false;
			}

			/* Make sure that the mirrored block isn't mirroring another block */
			$possible_mirror_of_mirror = blox_get( 'mirror-block', $mirrored_block['settings'] );

			if ( $possible_mirror_of_mirror && $mirror_of_mirror_block = self::get_block( $possible_mirror_of_mirror ) ) {
				if ( blox_get( 'type', $mirror_of_mirror_block ) == blox_get( 'type', $mirrored_block ) ) {
					return false;
				}
			}

			return $return_block_id ? $mirrored_block['id'] : $mirrored_block;

		}

		return false;

	}


	public static function block_exists( $id, $use_cache = false ) {

		$blocks_by_id = self::schema_blocks_by_id( $use_cache );

		return isset( $blocks_by_id[ $id ] );

	}


}

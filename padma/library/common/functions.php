<?php
/**
 * Regular functions to be used throughout Padma.  This file has absolutely no organizational pattern.
 **/

/**
 * Attempt to unserialize string.  If there's an error then do the preg_replace trick to correct the serialization.
 *
 * @param $string
 */
function padma_maybe_unserialize($string) {

	if ( is_serialized( $string ) ) {

		$data = maybe_unserialize( $string );

		if ( !is_array($data) ) {

			$data = maybe_unserialize(
						preg_replace_callback(
							'!s:(\d+):"(.*?)";!',
							function($matches){
								if(is_array($matches)){

									$string = '';
									foreach ($matches as $key => $value) {
										$string .= "'s:'" . strlen($value) . "':\"$value\";'";		
									}
									return $string;

								}elseif (is_string($matches)) {

									return "'s:'" . strlen($matches) . "':\"$matches\";'";

								}else{
									return;
								}
							},
							$string 
						)
					);
		}

	} else {

		$data = $string;

	}

	return $data;

}


/**
 * Wrapper of maybe_serialize() but does an is_serialized check first so it's not double serialized
 */
function padma_maybe_serialize($data) {

	if ( is_serialized($data) )
		return $data;

	return maybe_serialize($data);

}


/**
 * Simple alias for get_template_directory_uri()
 * 
 * @uses get_template_directory_uri()
 **/
function padma_url() {

	return apply_filters('padma_url', get_template_directory_uri());

}


/**
 * @todo Document
 **/
 function padma_cache_url() {

 	$uploads = wp_upload_dir();

 	return apply_filters('padma_cache_url', padma_format_url_ssl($uploads['baseurl'] . '/padma/cache'));		

 }


/**
 * A simple function to retrieve a key/value pair from the $_GET array or any other user-specified array.  This will automatically return false if the key is not set.
 * 
 * @param string Key to retrieve
 * @param array Optional array to retrieve from.  Default is $_GET
 * 
 * @return mixed
 **/
function padma_get($name, $array = false, $default = null, $fix_data_type = false) {

	if ( false === $array ) {
		$array = $_GET;
	}

	if ( (is_string($name) || is_numeric($name)) && !is_float($name) ) {

		if ( is_array($array) && isset($array[$name]) ) {
			$result = $array[$name];
		} elseif ( is_object($array) && isset($array->$name) ) {
			$result = $array->$name;
		}
	}

	if ( ! isset( $result ) ) {
		$result = $default;
	}

	return !$fix_data_type ? $result : padma_fix_data_type($result);	

}


/**
 *
 * Function to search and retrieve a key/value pair from the $_GET array or any other user-specified array. This will automatically return false if the key is not set.
 *
 */
function padma_get_search($name, $array = false, $default = null, $fix_data_type = false){

	if ( $array === false )
		$array = $_GET;

	if ( (is_string($name) || is_numeric($name)) && !is_float($name) ) {

		if ( is_array($array) ){			
			foreach ($array as $key => $value) {

				$fixed_key = preg_replace('/([a-z0-9\-])\-([0-9]+$)/i', '$1', $key);

				if( $fixed_key == $name)
					$result = $array[$key];

			}

		}elseif( is_object($array) ){
			foreach ($array as $key => $value) {

				$fixed_key = preg_replace('/([a-z0-9\-])\-([0-9]+$)/i', '$1', $array->$key);

				if( $fixed_key == $name)
					$result = $array->$key;
			}
		}

	}
	if ( !isset($result) )
		$result = $default;

	return !$fix_data_type ? $result : padma_fix_data_type($result);	


}

/**
 * Extension of padma_get().  Use this to fetch a key/value pair from the $_POST array.
 * 
 * @uses padma_get()
 * 
 * @param string Key to retrieve
 * 
 * @return mixed
 **/
function padma_post($name, $default = null) {

	return padma_get($name, $_POST, $default);

}


/**
 * @todo Document
 **/
function padma_format_url_ssl($url) {

	if ( !is_ssl() )
		return $url;

	return str_replace('http://', 'https://', $url);

}


/**
 * Retrieves the current URL.
 *
 * @return string
 **/
function padma_get_current_url() {

	debug($_SERVER);
	$prefix = padma_get( 'HTTPS', $_SERVER ) !== 'on' ? 'http://' : 'https://';
	$http_host = !isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['HTTP_X_FORWARDED_HOST'];

	return $prefix . $http_host . $_SERVER['REQUEST_URI'];

}


/**
 * @todo Document
 **/
function padma_change_to_unix_path($path) {

	return str_replace('\\', '/', $path);

}


/**
 * @todo Document
 **/
function padma_fix_data_type($data) {

	if ( is_numeric($data) ) {

		if ( (float)$data === (int) $data ) {
			return (int)$data;
		} else {
			return (float)$data;
		}

	} elseif ( $data === 'true' ) {

	 	return true;

	} elseif ( $data === 'false' ) {

	 	return false;

	} elseif ( $data === '' || $data === 'null' ) {

		return null;

	} else {

		$data = padma_maybe_unserialize($data);

		if ( !is_array($data) ) {
			return stripslashes($data);

		//If it's an array, run this function across all of the nodes in the array.
		} else {

			return array_map('padma_maybe_unserialize', $data);

		}

	}

}


/**
 * Generates the URL for the image resizer.
 * 
 * @param string $url URL to original image.
 * @param int $w Width to resize to.
 * @param int $h Height to resize to.
 * @param int $zc Determines whether or not to zoom/crop the image.
 * @uses wp_upload_dir()
 * @uses image_resize_dimensions()
 * @uses image_resize()
 *
 * @return string|array The URL to the image.
 *
 * Title		: Aqua Resizer
 * Description	: Resizes WordPress images on the fly
 * Version	: 1.1
 * Author	: Syamil MJ
 * Author URI	: http://aquagraphite.com
 * License	: WTFPL - http://sam.zoy.org/wtfpl/
 * Documentation	: https://github.com/sy4mil/Aqua-Resizer/
 **/
function padma_thumbnail() {
	_deprecated_function(__FUNCTION__, '3.1.3', 'padma_resize_image()');
	$args = func_get_args();
	return call_user_func_array('padma_resize_image', $args);
}

function padma_resize_image($url, $width = null, $height = null, $crop = true, $single = true, $upscale = true ) {

	if ( !$url )
		return null;

	Padma::load('common/image-resizer');

	$PadmaImageResize 	= PadmaImageResize::getInstance();
	$resized_image 		= $PadmaImageResize->process($url, $width, $height, $crop, false, $upscale);

	if ( is_wp_error($resized_image) )
		return $url . '#' . $resized_image->get_error_code();

	return $resized_image['url'];

}


/**
 * Detects if the browser is Internet Explorer.  Will also check if a specific version of MSIE.
 * 
 * @param int $version
 *
 * @return bool
 **/
function padma_is_ie($version_check = false) {

	$agent = ( isset($_SERVER['HTTP_USER_AGENT']) ) ? $_SERVER['HTTP_USER_AGENT'] : '';
	
	preg_match('/MSIE\s([\d.]+)/', $agent, $matches);

	if ( count($matches) === 0 || !is_array($matches) )
		return false;

	/* The user agent has a version with a decimal in it so it needs to be changed to an integer so it's 9 rather than 9.0 */
	$version = (int)$matches[1];

	if ( $version_check !== false )
		return $version_check == $version;

	return $version;

}

function wp_enqueue_multiple_scripts($scripts, $in_footer = true) {

	if ( !is_array($scripts) || count($scripts) === 0 )
		return false;

	foreach ($scripts as $script => $src) {

		if ( is_string($script) && is_string($src) ) {
			wp_enqueue_script($script, $src, false, false, $in_footer);
		} else {
			wp_enqueue_script($src, false, false, false, $in_footer);
		}

	}

}


function wp_enqueue_multiple_styles($styles) {

	if ( !is_array($styles) || count($styles) === 0 )
		return false;

	foreach ($styles as $style => $src) {

		if ( is_string($style) && is_string($src) ) {
			wp_enqueue_style($style, $src);
		} else {
			wp_enqueue_style($src);
		}

	}

}


function padma_in_numeric_range($check, $begin, $end, $allow_equals = true) {

	if ( $allow_equals && ($begin <= $check && $check <= $end) )
		return true;

	if ( !$allow_equals && ($begin < $check && $check < $end) )
		return true;

	return false;

}


function padma_remove_from_array(array &$array, $value) {

	$array = array_diff($array, array($value));

	return $array;

}


function padma_array_insert(array &$array, array $insert, $position) {

	settype($position, 'int');

	//if pos is start, just merge them
	if ( $position === 0 ) {

	    $array = array_merge($insert, $array);

	} else {

	    //if pos is end just merge them
	    if( $position >= (count($array)-1) ) {

	        $array = array_merge($array, $insert);

	    } else {

	        //split into head and tail, then merge head+inserted bit+tail
	        $head = array_slice($array, 0, $position);
	        $tail = array_slice($array, $position);
	        $array = array_merge($head, $insert, $tail);

	    }

	}

	return $array;

}


function padma_array_key_neighbors( $array, $find_key, $value_only = true ) {

	$select = $previous = $next = NULL;

	if ( ! array_key_exists( $find_key, $array ) ) {
		return FALSE;
	}

	foreach ( $array as $key => $value ) {

		$this_value = $value_only ? $value : array( $key => $value );

		if ( $key === $find_key ) {
			$select = $this_value;
			continue;
		}

		if ( null !== $select ) {
			$next = $this_value;
			break;
		}

		$previous = $this_value;

	}

	return array(
		'prev'    => $previous,
		'current' => $select,
		'next'    => $next,
	);
}


/**
 * http://php.net/manual/en/function.array-map.php#112857
 */
function padma_array_map_recursive( $callback, $array ) {
	foreach ( $array as $key => $value ) {
		if ( is_array( $array[ $key ] ) ) {
			$array[ $key ] = padma_array_map_recursive( $callback, $array[ $key ] );
		} else {
			$array[ $key ] = call_user_func( $callback, $array[ $key ] );
		}
	}

	return $array;
}



/**
 * http://www.php.net/manual/en/function.array-merge-recursive.php#104145
 **/
function padma_array_merge_recursive_simple() {

	// handle the arguments, merge one by one
	$args = func_get_args();
	$array = $args[0];

	if (!is_array($array)) {
		return $array;
	}

	for ($i = 1; $i < count($args); $i++) {

		if (is_array($args[$i])) {
			$array = padma_array_merge_recursive_simple_recurse($array, $args[$i]);
		}

	}

	return $array;

}


	function padma_array_merge_recursive_simple_recurse($array, $array1) {

		foreach ($array1 as $key => $value) {

			// create new key in $array, if it is empty or not an array
			if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key]))) {
				$array[$key] = array();
			}

			// overwrite the value in the base array
			if (is_array($value)) {
				$value = padma_array_merge_recursive_simple_recurse($array[$key], $value);
			}

			$array[$key] = $value;

		}

		return $array;

	}


function padma_format_color($color, $pound_sign = true) {

	/* Start by removing any pound sign that exists */
	$color = str_replace('#', '', $color);

	/* If the color is a hex, then re-add the pound sign */
	if ( strlen($color) == 6 && $pound_sign )
		return '#' . $color;

	return $color;	

}


/**
 * http://www.php.net/manual/en/function.get-browser.php#101125
 **/
function padma_get_browser() {

	$u_agent = $_SERVER['HTTP_USER_AGENT']; 
	$bname = 'Unknown';
	$platform = 'Unknown';
	$version = '';

	/* First get the platform */
	if ( preg_match('/linux/i', $u_agent) )
		$platform = 'linux';

	elseif ( preg_match('/macintosh|mac os x/i', $u_agent) )
		$platform = 'mac';

	elseif ( preg_match('/windows|win32/i', $u_agent) )
		$platform = 'windows';

	/* Next get the name of the useragent yes seperately and for good reason */
	if ( preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent) ) { 

		$bname = 'Internet Explorer'; 
		$ub = 'MSIE'; 

	} elseif ( preg_match('/Firefox/i', $u_agent) ) { 

		$bname = 'Mozilla Firefox'; 
		$ub = 'Firefox'; 

	} elseif ( preg_match('/Chrome/i', $u_agent) ) { 

		$bname = 'Google Chrome'; 
		$ub = 'Chrome'; 

	} elseif ( preg_match('/Safari/i', $u_agent) ) { 

		$bname = 'Apple Safari'; 
		$ub = 'Safari'; 

	} elseif ( preg_match('/Opera/i', $u_agent) ) { 

		$bname = 'Opera'; 
		$ub = 'Opera'; 

	} elseif ( preg_match('/Netscape/i', $u_agent) ) {

		$bname = 'Netscape'; 
		$ub = 'Netscape'; 

	} 

	/* Get the correct version number */
	$known = array('Version', $ub, 'other');
	$pattern = '#(?P<browser>' . join('|', $known) . ')[/ ]+(?P<version>[0-9.|a-zA-Z.]*)#';

	if  ( !preg_match_all($pattern, $u_agent, $matches) ) {
		// we have no matching number just continue
	}

	// see how many we have
	$i = count($matches['browser']);

	if ($i != 1) {

		//we will have two since we are not using 'other' argument yet
		//see if version is before or after the name
		if ( strripos($u_agent, 'Version') < strripos($u_agent, $ub) )
		   $version = $matches['version'][0];
		else
		   $version = $matches['version'][1];

	} else {
		$version = $matches['version'][0];
	}

	//Check if we have a number
	if ( $version == null || $version == '' )
		$version = '?';

	return array(
		'userAgent' => $u_agent,
		'name'      => $bname,
		'version'   => $version,
		'platform'  => $platform,
		'pattern'    => $pattern
	);

}


function padma_str_replace_json($search, $replace, $subject) {

	return json_decode(str_replace($search, $replace, json_encode($subject)), true);

}

function padma_preg_replace_json($pattern, $replace, $subject) {

	return json_decode(preg_replace($pattern, $replace, json_encode($subject)), true);

}


/* Search Form */
function padma_get_search_form($placeholder = null) {

	if ( !$placeholder )
		$placeholder = __('Type to search, then press enter', 'padma');

	$placeholder = apply_filters('padma_search_form_placeholder', $placeholder);
	$search_query = get_search_query();
	$search_input_attributes = array(
		'type' => 'text',
		'class' => 'field',
		'name' => 's',
		'id' => 's'
	);

	/* Handle the placeholder and value */
		//$search_input_attributes['placeholder'] = $placeholder;

		$search_input_attributes['value'] = $search_query ? $search_query : $placeholder;

		$search_input_attributes['onclick'] = 'if(this.value==\'' . $placeholder . '\')this.value=\'\';';
		$search_input_attributes['onblur'] = 'if(this.value==\'\')this.value=\'' . $placeholder . '\';';

	/* Turn the array into real HTML attributes */
		$search_input_attributes = apply_filters('padma_search_input_attributes', $search_input_attributes);
		$search_input_attributes_string = '';

		foreach ( $search_input_attributes as $attribute => $value ) 
			$search_input_attributes_string .= $attribute . '="' . $value . '" ';

	return '
		<form method="get" id="searchform" action="' . esc_url(home_url('/')) . '">
			<label for="s" class="assistive-text">' . __('Search', 'padma') . '</label>
			<input ' . trim($search_input_attributes_string) .' />
			<input type="submit" class="submit" name="submit" id="searchsubmit" value="' . esc_attr__('Search', 'padma') . '" />
		</form>
	';

}


function padma_human_bytes($size) {

	try {
		$units = array( 'B', 'KB', 'MB', 'GB', 'TB' );

		for ( $i = 0; $size >= 1024 && $i < 4; $i ++ ) {
			$size /= 1024;
		}

		return round( $size, 2 ) . $units[ $i ];
	} catch ( Exception $e ) {
		return "n/a";
	}

}


/**
 * See https://core.trac.wordpress.org/ticket/26809
 */
add_action('edit_form_after_editor', 'padma_meta_padma_save_post_template_bypass');
function padma_meta_padma_save_post_template_bypass() {

	global $post;

	if ( 'page' == $post->post_type && !count(wp_get_theme()->get_page_templates()) ) {

		echo '
		<!--
		Added by Padma
		See: https://core.trac.wordpress.org/ticket/26809
		-->

		<input type="hidden" name="page_template" value="default" />

		';

	}

}


function padma_register_admin_meta_box($class = null) {

	if(is_null($class))
		return;

	add_action('init', function() use ($class){
		return padma_register_admin_meta_padma_callback($class);
	});

}


function padma_register_admin_meta_padma_callback($class) {

	if ( !class_exists($class) )
		return new WP_Error('meta_padma_class_does_not_exist', __('Error: The meta box class being registered does not exist.', 'padma'), $class);

	$meta_box = new $class();
	$meta_box->register();

	return true;

}


/**
 * Padma blocks API.
 *
 * @package Padma
 * @subpackage API
 **/
function padma_register_block( $class, $block_type_url = false, $block_type_path = null, $block_type_icons = array() ) {

	global $padma_unregistered_block_types;

	if ( ! is_array( $padma_unregistered_block_types ) ) {
		$padma_unregistered_block_types = array();
	}

	$padma_unregistered_block_types[ $class ] = array(
		'block_type_url'	=>	$block_type_url,
		'block_type_path'	=>	$block_type_path,
		'block_type_icons'	=>	$block_type_icons,
	);

	return true;

}


function padma_register_visual_editor_box($class) {

	add_action('padma_visual_editor_display_init', function() use ($class){		
		return padma_register_visual_editor_box_callback($class);
	});

}


function padma_register_visual_editor_box_callback($class) {

	if ( !class_exists($class) )
		return new WP_Error('box_class_does_not_exist', __('Error: The box class being registered does not exist.', 'padma'), $class);

	$box = new $class();
	$box->register();

	return true;

}

function padma_register_visual_editor_panel($class) {

	add_action('padma_visual_editor_display_init', function() use ($class){
		return padma_register_visual_editor_panel_callback($class);
	}, 999, 1);

}


function padma_register_visual_editor_panel_callback($class) {

	if ( !class_exists($class) )
		return new WP_Error('panel_class_does_not_exist', __('Error: The panel class being registered does not exist.', 'padma'), $class);

	$panel = new $class();
	$panel->register();

	return true;

}


function padma_register_web_font_provider($class) {

	return new $class;

}


/**
 *
 * Debug function
 *
 */
if(!function_exists('debug')){	
	function debug($data=null){
		if($data)
			error_log( print_r($data,1) );
	}	
}


function padma_get_int( $string ) {

	preg_match( "/([0-9]+[\.,]?)+/", $string, $matches );

	if ( !isset( $matches[0] ) ) 
		return false;

	return $matches[0];

}




/**
 *
 * Validate date
 * https://stackoverflow.com/questions/12322824/php-preg-match-with-working-regex/12323025#12323025
 *
 */
function padma_validateDate($date, $format = 'Y-m-d H:i:s'){	
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}



/**
 * Replace URLs.
 *
 * Replace old URLs to new URLs. This method also updates all the Padma data.
 *
 * @param $from
 * @param $to
 *
 * @return string
 * @throws \Exception
 */
function padma_replace_urls( $from, $to ) {

	global $wpdb;

	$success = false;
	$from    = trim( $from );
	$to      = trim( $to );

	if ( empty( $from ) || empty( $to ) ) {
		$GLOBALS['padma_admin_save_message'] = __( 'Empty URL given.', 'padma' ) ;
		return;
	}

	if ( $from === $to ) {
		$GLOBALS['padma_admin_save_message'] = __( 'The `from` and `to` URL\'s must be different', 'padma' ) ;
		return;
	}

	$is_valid_urls = ( filter_var( $from, FILTER_VALIDATE_URL ) && filter_var( $to, FILTER_VALIDATE_URL ) );

	if ( ! $is_valid_urls ) {
		$GLOBALS['padma_admin_save_message'] = __( 'The `from` and `to` URL\'s must be valid URL\'s', 'padma' ) ;
		return;
	}

	
	/**
	 * Update all blocks settings
	 */	
	$blocks = PadmaBlocksData::get_all_blocks();
	foreach ($blocks as $block) {

		$block_id = $block['id'];
		foreach ($block['settings'] as $setting_key => $setting_value) {
			$block['settings'][$setting_key] = str_replace( $from , $to, $setting_value );
		}

		PadmaBlocksData::update_block( $block_id, $block );
	}



	/**
	 * Update all style options
	 */
	$current_skin = PadmaTemplates::get_active_id();
	$wp_options_prefix = 'pu_|template=' . PadmaOption::$current_skin . '|_';
	
	$options = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->options WHERE option_name LIKE '%s'", $wp_options_prefix . '%'), ARRAY_A);
	
	foreach ($options as $key => $option) {
		$option_id 		= $option['option_id'];
		$option_name 	= $option['option_name'];
		$option_value	= maybe_unserialize($option['option_value']);

		// Check each level 1 options
		foreach ( $option_value as $key_level_1 => $value_level_1 ) {
			
			if( is_array($value_level_1) ){

				// Check each level 2 options
				foreach ( $value_level_1 as $key_level_2 => $value_level_2 ) {

					if( is_array($value_level_2) ){

						// Check each level 3 options
						foreach ( $value_level_2 as $key_level_3 => $value_level_3 ) {

							if( is_array($value_level_3) ){

								// Check each level 4 options
								foreach ( $value_level_3 as $key_level_4 => $value_level_4 ) {

									if( is_array($value_level_4) ){
										
										// Check each level 5 options
										foreach ( $value_level_4 as $key_level_5 => $value_level_5 ) {											
											
											if( is_array($value_level_5) ){

												//

											}else{
												// Replace level 4 values
												$option_value[ $key_level_1 ][ $key_level_2 ][ $key_level_3 ][ $key_level_4 ][ $key_level_5 ] = str_replace( $from , $to, $value_level_5 );
											}
										}

									}else{
										// Replace level 4 values
										$option_value[ $key_level_1 ][ $key_level_2 ][ $key_level_3 ][ $key_level_4 ] = str_replace( $from , $to, $value_level_4 );
									}

								}
							

							}else{
								// Replace level 3 values
								$option_value[ $key_level_1 ][ $key_level_2 ][ $key_level_3 ] = str_replace( $from , $to, $value_level_3 );
							}

						}

					}else{
						// Replace level 2 values
						$option_value[ $key_level_1 ][ $key_level_2 ] = str_replace( $from , $to, $value_level_2 );
					}
				}

			}else{
				// Replace level 1 values
				$option_value[ $key_level_1 ] = str_replace( $from , $to, $value_level_1 );
			}
		}
		update_option( $option_name, $option_value );
		
	}


	$success = true;	
	return $success;
}
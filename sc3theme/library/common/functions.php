<?php
/**
 * Regular functions to be used throughout Blox.  This file has absolutely no organizational pattern.
 **/

/**
 * Returns the Blox License Key from the options or constant if define.
 **/
function blox_get_license_key($slug = false) {

	if ( !$slug )
		return false;

	if ( defined('BLOX_LICENSE_KEY') && $slug == 'blox' )
		return apply_filters('blox_license_key', trim(BLOX_LICENSE_KEY), $slug);

	return apply_filters('blox_license_key', trim(BloxOption::get_from_main_site('license-key-' . $slug)), $slug);

}


function blox_get_license_status($slug = false) {

	if ( !$slug )
		return false;

	return BloxOption::get_from_main_site('license-status-' . $slug);

}


function blox_activate_license($item_slug, $item_name = null) {

	$license = blox_get_license_key($item_slug);

	$api_params = array(
		'edd_action' => 'activate_license',
		'license' => $license,
		'item_name' => urlencode($item_name)
	);

	$response = wp_remote_get( add_query_arg( $api_params, BLOX_SITE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	// $license_data->license will be either "active" or "inactive"

	BloxOption::set('license-status-' . $item_slug, $license_data->license);

	return $license_data->license;

}


function blox_deactivate_license($item_slug, $item_name = null) {

	$license = blox_get_license_key($item_slug);

	$api_params = array(
		'edd_action' => 'deactivate_license',
		'license' => $license,
		'item_name' => urlencode($item_name)
	);

	$response = wp_remote_get( add_query_arg( $api_params, BLOX_SITE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

	if ( is_wp_error( $response ) )
		return false;

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	// $license_data->license will be either "deactivated" or "failed"
	if( $license_data->license == 'deactivated' )
		BloxOption::delete('license-status-' . $item_slug);

	return $license_data->license;

}


/**
 * Attempt to unserialize string.  If there's an error then do the preg_replace trick to correct the serialization.
 *
 * @param $string
 */
function blox_maybe_unserialize($string) {

	if ( is_serialized( $string ) ) {

		$data = maybe_unserialize( $string );

		if ( !is_array($data) ) {

			$data = maybe_unserialize(preg_replace( '!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $string ));

		}

	} else {

		$data = $string;

	}

	return $data;

}


/**
 * Wrapper of maybe_serialize() but does an is_serialized check first so it's not double serialized
 */
function blox_maybe_serialize($data) {

	if ( is_serialized($data) )
		return $data;

	return maybe_serialize($data);

}


/**
 * Simple alias for get_template_directory_uri()
 * 
 * @uses get_template_directory_uri()
 **/
function blox_url() {

	return apply_filters('blox_url', get_template_directory_uri());

}


/**
 * @todo Document
 **/
 function blox_cache_url() {

 	$uploads = wp_upload_dir();

 	return apply_filters('blox_cache_url', blox_format_url_ssl($uploads['baseurl'] . '/blox/cache'));		

 }


/**
 * A simple function to retrieve a key/value pair from the $_GET array or any other user-specified array.  This will automatically return false if the key is not set.
 * 
 * @param string Key to retrieve
 * @param array Optional array to retrieve from.  Default is $_GET
 * 
 * @return mixed
 **/
function blox_get($name, $array = false, $default = null, $fix_data_type = false) {
	
	if ( $array === false )
		$array = $_GET;
	
	if ( (is_string($name) || is_numeric($name)) && !is_float($name) ) {

		if ( is_array($array) && isset($array[$name]) )
			$result = $array[$name];
		elseif ( is_object($array) && isset($array->$name) )
			$result = $array->$name;

	}

	if ( !isset($result) )
		$result = $default;
		
	return !$fix_data_type ? $result : blox_fix_data_type($result);	
		
}


/**
 * Extension of blox_get().  Use this to fetch a key/value pair from the $_POST array.
 * 
 * @uses blox_get()
 * 
 * @param string Key to retrieve
 * 
 * @return mixed
 **/
function blox_post($name, $default = null) {
	
	return blox_get($name, $_POST, $default);
	
}


/**
 * @todo Document
 **/
function blox_format_url_ssl($url) {
	
	if ( !is_ssl() )
		return $url;
		
	return str_replace('http://', 'https://', $url);
	
}


/**
 * Retrieves the current URL.
 *
 * @return string
 **/
function blox_get_current_url() {

	$prefix = blox_get('HTTPS', $_SERVER) != 'on' ? 'http://' : 'https://';
	$http_host = !isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['HTTP_X_FORWARDED_HOST'];

	return $prefix . $http_host . $_SERVER['REQUEST_URI'];

}


/**
 * @todo Document
 **/
function blox_change_to_unix_path($path) {
	
	return str_replace('\\', '/', $path);
	
}


/**
 * @todo Document
 **/
function blox_fix_data_type($data) {
	
	if ( is_numeric($data) ) {
		
		if ( floatval($data) == intval($data) )
			return (int)$data;
		else
			return (float)$data;
		
	} elseif ( $data === 'true' ) {
		
	 	return true;
		
	} elseif ( $data === 'false' ) {
		
	 	return false;
		
	} elseif ( $data === '' || $data === 'null' ) {
		
		return null;
		
	} else {

		$data = blox_maybe_unserialize($data);
		
		if ( !is_array($data) ) {
			return stripslashes($data);
			
		//If it's an array, run this function across all of the nodes in the array.
		} else {
			
			return array_map('blox_maybe_unserialize', $data);
			
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
function blox_thumbnail() {
	_deprecated_function(__FUNCTION__, '3.1.3', 'blox_resize_image()');
	$args = func_get_args();
	return call_user_func_array('blox_resize_image', $args);
}

function blox_resize_image($url, $width = null, $height = null, $crop = true, $single = true, $upscale = true ) {

	if ( !$url )
		return null;

	Blox::load('common/image-resizer');

	$BloxImageResize = BloxImageResize::getInstance();
	$resized_image = $BloxImageResize->process($url, $width, $height, $crop, false, $upscale);

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
function blox_is_ie($version_check = false) {
	
	$agent = $_SERVER['HTTP_USER_AGENT'];
	
	preg_match('/MSIE\s([\d.]+)/', $_SERVER['HTTP_USER_AGENT'], $matches);
	
	if ( count($matches) === 0 || !is_array($matches) )
		return false;

	/* The user agent has a version with a decimal in it so it needs to be changed to an integer so it's 9 rather than 9.0 */
	$version = (int)$matches[1];

	if ( $version_check !== false )
		return $version_check == $version;

	return $version;
	
}
	
/**
 * Parses PHP using eval.
 *
 * @param string $content PHP to be parsed.
 * 
 * @return mixed PHP that has been parsed.
 **/
function blox_parse_php($content) {
	
	/* If Blox PHP parsing is disabled, then return the content now. */
	if ( defined('BLOX_DISABLE_PHP_PARSING') && BLOX_DISABLE_PHP_PARSING === true )
		return $content;
	
	/* If it's a WordPress Network setup and the current site being viewed isn't the main site, 
	   then don't parse unless BLOX_ALLOW_NETWORK_PHP_PARSING is true. */
	if ( !is_main_site() && (!defined('BLOX_ALLOW_NETWORK_PHP_PARSING') || BLOX_ALLOW_NETWORK_PHP_PARSING === false) )
		return $content;
	
	ob_start();

	$eval = eval("?>$content<?php ");
	
	if ( $eval === null ) {

		$parsed = ob_get_contents();

	} else {

		$error = error_get_last();
		$parsed = '<p><strong>Error while parsing PHP:</strong> ' . $error['message'] . '</p>';

	}

	ob_end_clean();

	return $parsed;
	
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


function blox_in_numeric_range($check, $begin, $end, $allow_equals = true) {

	if ( $allow_equals && ($begin <= $check && $check <= $end) )
		return true;

	if ( !$allow_equals && ($begin < $check && $check < $end) )
		return true;

	return false;
	
}


function blox_remove_from_array(array &$array, $value) {
	
	$array = array_diff($array, array($value));
	
	return $array;
	
}


function blox_array_insert(array &$array, array $insert, $position) {
	
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


function blox_array_key_neighbors($array, $findKey, $valueOnly = true) {
	
	if ( ! array_key_exists($findKey, $array))
		return FALSE;

	$select = $previous = $next = NULL;

	foreach($array as $key => $value) {
		
		$thisValue = $valueOnly ? $value : array($key => $value);
		
		if ($key === $findKey) {
			$select = $thisValue;
			continue;
		}
		
		if ($select !== NULL) {
			$next = $thisValue;
			break;
		}
		
		$previous = $thisValue;

	}

	return array(
		'prev' => $previous,
		'current' => $select,
		'next' => $next
	);

}


/**
 * http://php.net/manual/en/function.array-map.php#112857
 */
function blox_array_map_recursive( $callback, $array ) {
	foreach ( $array as $key => $value ) {
		if ( is_array( $array[ $key ] ) ) {
			$array[ $key ] = blox_array_map_recursive( $callback, $array[ $key ] );
		} else {
			$array[ $key ] = call_user_func( $callback, $array[ $key ] );
		}
	}

	return $array;
}



/**
 * http://www.php.net/manual/en/function.array-merge-recursive.php#104145
 **/
function blox_array_merge_recursive_simple() {
    
	// handle the arguments, merge one by one
	$args = func_get_args();
	$array = $args[0];

	if (!is_array($array)) {
		return $array;
	}

	for ($i = 1; $i < count($args); $i++) {

		if (is_array($args[$i])) {
			$array = blox_array_merge_recursive_simple_recurse($array, $args[$i]);
		}

	}

	return $array;

}


	function blox_array_merge_recursive_simple_recurse($array, $array1) {
	 
		foreach ($array1 as $key => $value) {

			// create new key in $array, if it is empty or not an array
			if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key]))) {
				$array[$key] = array();
			}

			// overwrite the value in the base array
			if (is_array($value)) {
				$value = blox_array_merge_recursive_simple_recurse($array[$key], $value);
			}

			$array[$key] = $value;

		}

		return $array;

	}


function blox_format_color($color, $pound_sign = true) {

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
function blox_get_browser() {

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


function blox_str_replace_json($search, $replace, $subject) {

	return json_decode(str_replace($search, $replace, json_encode($subject)), true);

}

function blox_preg_replace_json($pattern, $replace, $subject) {

	return json_decode(preg_replace($pattern, $replace, json_encode($subject)), true);

}


/* Search Form */
function blox_get_search_form($placeholder = null) {

	if ( !$placeholder )
		$placeholder = __('Type to search, then press enter', 'blox');

	$placeholder = apply_filters('blox_search_form_placeholder', $placeholder);
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
		$search_input_attributes = apply_filters('blox_search_input_attributes', $search_input_attributes);
		$search_input_attributes_string = '';
		
		foreach ( $search_input_attributes as $attribute => $value ) 
			$search_input_attributes_string .= $attribute . '="' . $value . '" ';
		
	return '
		<form method="get" id="searchform" action="' . esc_url(home_url('/')) . '">
			<label for="s" class="assistive-text">' . __('Search', 'blox') . '</label>
			<input ' . trim($search_input_attributes_string) .' />
			<input type="submit" class="submit" name="submit" id="searchsubmit" value="' . esc_attr__('Search', 'blox') . '" />
		</form>
	';

}


function blox_human_bytes($size) {

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
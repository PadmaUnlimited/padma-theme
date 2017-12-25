<?php
class PadmaSkinOption {


	private static function pass_method($method, array $args) {

		/* Set skins option flag */
		PadmaOption::$is_skin_option = true;

		$result = call_user_func_array(array('PadmaOption', $method), $args);

		/* Remove skin option flag */
		PadmaOption::$is_skin_option = false;

		return $result;

	}


	public static function get() {

		$args = func_get_args();
		return self::pass_method(__FUNCTION__, $args);

	}


	public static function get_group() {

		$args = func_get_args();
		return self::pass_method(__FUNCTION__, $args);

	}


	public static function set() {

		$args = func_get_args();
		return self::pass_method(__FUNCTION__, $args);

	}


	public static function set_group() {

		$args = func_get_args();
		return self::pass_method(__FUNCTION__, $args);

	}


	public static function delete() {

		$args = func_get_args();
		return self::pass_method(__FUNCTION__, $args);
		
	}


}
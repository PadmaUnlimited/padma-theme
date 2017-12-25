<?php
// Add dynamic_loop function for LoopBuddy

/*
Copyright 2011 iThemes (email: support@ithemes.com)

Written by Chris Jean
Version 0.0.1

Version History
	0.0.1 - 2011-02-28 - Chris Jean
		Initial version
*/


if ( ! function_exists( 'dynamic_loop' ) ) {
	function dynamic_loop() {
		global $dynamic_loop_handlers;
		
		if ( empty( $dynamic_loop_handlers ) || ! is_array( $dynamic_loop_handlers ) )
			return false;
		
		ksort( $dynamic_loop_handlers );
		
		foreach ( (array) $dynamic_loop_handlers as $handlers ) {
			foreach ( (array) $handlers as $function ) {
				if ( is_callable( $function ) && ( false != call_user_func( $function ) ) ) {
					return true;
				}
			}
		}
		
		return false;
	}
}

if ( ! function_exists( 'register_dynamic_loop_handler' ) ) {
	function register_dynamic_loop_handler( $function, $priority = 10 ) {
		global $dynamic_loop_handlers;

		if ( ! is_numeric( $priority ) )
			$priority = 10;
		
		if ( ! isset( $dynamic_loop_handlers ) || ! is_array( $dynamic_loop_handlers ) )
			$dynamic_loop_handlers = array();
		
		if ( ! isset( $dynamic_loop_handlers[$priority] ) || ! is_array( $dynamic_loop_handlers[$priority] ) )
			$dynamic_loop_handlers[$priority] = array();
		
		$dynamic_loop_handlers[$priority][] = $function;
	}
}
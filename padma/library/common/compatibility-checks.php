<?php

global $pagenow, $wp_version;


/* Do not throw these errors if DOING_AJAX is true or the themes page is already being viewed. */
if ( (defined('DOING_AJAX') && DOING_AJAX) || $pagenow === 'themes.php' ) {

	return;

/* Check WordPress */
} elseif ( version_compare($wp_version, '3.4', '<') ) {

	$message = '<span style="text-align: center;font-size: 26px;width: 100%;display: block;margin-bottom: 20px;">Error</span>' . sprintf( __('

		Padma requires WordPress 3.4 or higher.  You are running WordPress %s.<br /><br />

		Please deactivate Padma by going to <a href="%s">Appearance &raquo; Themes</a> and choosing a difference theme until your WordPress installation has been updated to 3.4 or higher.<br /><br />

		If the issue persists, please visit <a href="https://www.padmaunlimited.com" target="_blank">Padma Support</a>.
	', 'padma'), $wp_version, admin_url('themes.php'));

	wp_die($message);

/*	Do not Check PHP Version	*/
} elseif ( defined('PADMA_DISABLE_PHP_VERIFICATION') && PADMA_DISABLE_PHP_VERIFICATION === true) {

	return;


/* Check PHP */
} elseif (  version_compare( PHP_VERSION, '7.3', '<' ) ) {

	$message = '
		<span style="text-align: center;font-size: 26px;width: 100%;display: block;margin-bottom: 20px;">Error</span>' . sprintf( __('

		Padma requires PHP 7.3 or higher, as does WordPress 3.4 and higher.  You are running PHP %s.<br /><br />

		Please deactivate Padma by going to <a href="%s">Appearance &raquo; Themes</a> and choosing a difference theme until your PHP has been updated to 7.0 or higher.<br /><br />

		If the issue persists, please contact your web host or visit <a href="http://padmaunlimited.com" target="_blank">Padma Support</a>.
	', 'padma'), PHP_VERSION, admin_url('themes.php'));

	wp_die($message);

}

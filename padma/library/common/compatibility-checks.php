<?php

global $pagenow, $wp_version;

/* Do not throw these errors if DOING_AJAX is true or the themes page is already being viewed. */
if ( (defined('DOING_AJAX') && DOING_AJAX) || $pagenow === 'themes.php' ) {

	return;

/* Check WordPress */
} elseif ( version_compare($wp_version, '3.2', '<') ) {

	$message = '
		<span style="text-align: center;font-size: 26px;width: 100%;display: block;margin-bottom: 20px;">Error</span>

		Padma requires WordPress 3.2 or higher.  You are running WordPress ' . $wp_version . '.<br /><br />

		Please deactivate Padma by going to <a href="' . admin_url('themes.php') . '">Appearance &raquo; Themes</a> and choosing a difference theme until your WordPress installation has been updated to 3.2 or higher.<br /><br />

		If the issue persists, please visit <a href="http://padmaunlimited.com" target="_blank">Padma Support</a>.
	';

	wp_die($message);

/* Check PHP */
} elseif ( version_compare(PHP_VERSION, '5.2', '<') ) {

	$message = '
		<span style="text-align: center;font-size: 26px;width: 100%;display: block;margin-bottom: 20px;">Error</span>

		Padma requires PHP 5.2 or higher, as does WordPress 3.2 and higher.  You are running PHP ' . PHP_VERSION . '.<br /><br />

		Please deactivate Padma by going to <a href="' . admin_url('themes.php') . '">Appearance &raquo; Themes</a> and choosing a difference theme until your PHP has been updated to 5.2 or higher.<br /><br />

		If the issue persists, please contact your web host or visit <a href="http://padmaunlimited.com" target="_blank">Padma Support</a>.
	';

	wp_die($message);

}

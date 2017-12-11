<?php
/**
 * 3.8
 *
 * Fixed database for WordPress 4.2
 */
add_action( 'blox_do_upgrade_38', 'blox_do_upgrade_38' );
function blox_do_upgrade_38() {

	/* Alter MySQL schema */
	Blox::mysql_dbdelta();

}
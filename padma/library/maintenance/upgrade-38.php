<?php
/**
 * 3.8
 *
 * Fixed database for WordPress 4.2
 */
add_action( 'padma_do_upgrade_38', 'padma_do_upgrade_38' );
function padma_do_upgrade_38() {

	/* Alter MySQL schema */
	Padma::db_dbdelta();

}
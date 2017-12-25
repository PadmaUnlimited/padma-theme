<?php
/**
 * 3.8.2
 *
 * Responsive Grid defaults to enabled now
 */
add_action('padma_do_upgrade_382', 'padma_do_upgrade_382');
function padma_do_upgrade_382() {

    /* Alter MySQL schema */
    Padma::mysql_dbdelta();

    /* If responsive grid isn't enabled then set the option */
    if ( PadmaSkinOption::get('enable-responsive-grid', false, false) === false ) {
        PadmaSkinOption::set('enable-responsive-grid', false);
    }

}
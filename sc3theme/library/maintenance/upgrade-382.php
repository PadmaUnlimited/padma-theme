<?php
/**
 * 3.8.2
 *
 * Responsive Grid defaults to enabled now
 */
add_action('blox_do_upgrade_382', 'blox_do_upgrade_382');
function blox_do_upgrade_382() {

    /* Alter MySQL schema */
    Blox::mysql_dbdelta();

    /* If responsive grid isn't enabled then set the option */
    if ( BloxSkinOption::get('enable-responsive-grid', false, false) === false ) {
        BloxSkinOption::set('enable-responsive-grid', false);
    }

}
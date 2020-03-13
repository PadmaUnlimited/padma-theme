<?php
/**
 * 1.2.9
 *
 * CSS grid 
 * Add "legacy" grid system to wrappers
 */

add_action('padma_do_upgrade_129', 'padma_do_upgrade_129');
function padma_do_upgrade_129() {

    $all_wrappers = PadmaWrappersData::get_all_wrappers();

    foreach ( $all_wrappers as $wrapper_id => $wrapper) {
        
        if( !isset($wrapper['settings']['grid-system']) ){            
            
            $wrapper['settings']['grid-system'] = 'legacy';
            PadmaWrappersData::update_wrapper( $wrapper_id, $wrapper );

        }

    }


    if( PadmaSkinOption::get('grid-system') === '' ){
        PadmaSkinOption::set('grid-system', 'css-grid');
    }
}
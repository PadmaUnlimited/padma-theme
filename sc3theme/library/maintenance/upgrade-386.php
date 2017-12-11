<?php
/**
 * 3.8.6
 *
 * Fix long template names
 */
add_action('blox_do_upgrade_386', 'blox_do_upgrade_386');
function blox_do_upgrade_386() {

    global $wpdb;
    
    /* If $templates turns up false then we need to repair it using the name of options */
    $query_for_template_ids = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'blox_%template=%'" );
    $existing_templates = BloxOption::get_group('skins');
    $templates = array();
    
    $needs_repairing = false;

    foreach ( $query_for_template_ids as $query_for_template_ids_obj ) {

        $option_name_fragments = explode('|_', str_replace('blox_|template=', '', $query_for_template_ids_obj->option_name));
        $template_id = $option_name_fragments[0];
        
        if ( strlen($template_id) > 12 ) {
            $needs_repairing = true;
        }

        $templates[$template_id] = array(
            'name' => blox_get('name', blox_get($template_id, $existing_templates), $template_id),
            'id' => $template_id
        );

    }

    if ( !$needs_repairing ) {
        return false;
    }
    
    foreach ( $templates as $template_id => $template ) {

        /* Truncate the template ID to 12 characters due to varchar limit in wp_options */
        $new_template_id = substr(strtolower(str_replace(' ', '-', $template['id'])), 0, 12);
        $shortened_template_id = $new_template_id;

        $original_template_id = $template['id'];
        $original_template = $templates[ $original_template_id ];

        if ( $template['id'] == '' ) {
            $new_template_id = 'unnamed';

            $original_template = array(
                'name' => 'Unnamed'
            );
        }

        /* If the new template ID is the same as the current ID then don't do anything with this template */
        if ( $new_template_id == $original_template_id )
            continue;

        $template_unique_id_counter = 0;

        /* Check if template ID already exists.  If it does, change ID */
        while ( blox_get($new_template_id, $templates) || get_option('blox_|template=' . $new_template_id . '|_option_group_general') ) {

            $template_unique_id_counter++;
            $new_template_id = $shortened_template_id . '-' . $template_unique_id_counter;

        }

        /* Update WP option names */
        $wpdb->query( "UPDATE IGNORE $wpdb->options SET option_name = replace(option_name, 'blox_|template=$original_template_id|', 'blox_|template=$new_template_id|') WHERE option_name LIKE 'blox_|template=$original_template_id|%'" );

        $wpdb->query( "UPDATE $wpdb->bt_blocks SET template = '$new_template_id' WHERE template = '$original_template_id'" );
        $wpdb->query( "UPDATE $wpdb->bt_wrappers SET template = '$new_template_id' WHERE template = '$original_template_id'" );
        $wpdb->query( "UPDATE $wpdb->bt_layout_meta SET template = '$new_template_id' WHERE template = '$original_template_id'" );
        $wpdb->query( "UPDATE $wpdb->bt_snapshots SET template = '$new_template_id' WHERE template = '$original_template_id'" );

        /* If the current skin is the one with the name change then change that */
        if ( BloxOption::get('current-skin', 'general', BLOX_DEFAULT_SKIN) == $original_template_id ) {
            BloxOption::set('current-skin', $new_template_id);
            BloxOption::$current_skin = $new_template_id;
        }

        $templates[$new_template_id] = $original_template;
        unset($templates[$original_template_id]);

        $templates[$new_template_id]['id'] = $new_template_id;

    }

    BloxOption::set_group('skins', $templates);

}
<?php
/**
 * 1.3.1
 *
 * Missing text-underline: none; issue
 *
 * @package padma
 */

add_action(
	'padma_do_upgrade_131',
	function() {

		$design_data = PadmaSkinOption::get( 'properties', 'design', array() );

		$css_properties_to_change = array(
			// From => To.
			'list-style'      => 'list-style-type',
			'text-decoration' => 'text-decoration-line',
		);

		foreach ( $css_properties_to_change as $old_css_rule => $new_css_rule ) {
			foreach ( $design_data as $item => $properties ) {
				foreach ( $properties as $type => $rules ) {

					if ( 'properties' === $type ) {
						if ( isset( $rules[ $old_css_rule ] ) ) {
							if ( ! empty( $rules[ $old_css_rule ] ) && empty( $rules[ $new_css_rule ] ) ) {
								$design_data[ $item ][ $type ][ $new_css_rule ] = $rules[ $old_css_rule ];
								unset( $design_data[ $item ][ $type ][ $old_css_rule ] );
							}
						}
					} elseif ( 'special-element-instance' === $type || 'special-element-state' === $type ) {
						foreach ( $rules as $state => $state_rules ) {
							if ( isset( $state_rules[ $old_css_rule ] ) ) {
								if ( ! empty( $state_rules[ $old_css_rule ] ) && empty( $state_rules[ $new_css_rule ] ) ) {
									$design_data[ $item ][ $type ][ $new_css_rule ] = $state_rules[ $old_css_rule ];
									unset( $design_data[ $item ][ $type ][ $old_css_rule ] );
								}
							}
						}
					}
				}
			}
		}

		PadmaSkinOption::set( 'properties', $design_data, 'design' );
	}
);


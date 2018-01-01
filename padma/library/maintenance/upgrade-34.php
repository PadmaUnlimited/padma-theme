<?php
/**
 * Pre-3.4
 *
 * - Change block and wrapper margins to Design Editor values
 * - Convert Media blocks to Slider or Embed blocks
 **/
add_action( 'padma_do_upgrade_34', 'padma_do_upgrade_34' );
function padma_do_upgrade_34() {

	require_once PADMA_LIBRARY_DIR . '/maintenance/legacy-classes.php';

	/* Change block and wrapper margins to Design Editor values */
	PadmaElementsData_Upgrade34::set_property( 'structure', 'wrapper', 'margin-top', PadmaOption::get( 'wrapper-top-margin', 'general', 30 ) );
	PadmaElementsData_Upgrade34::set_property( 'structure', 'wrapper', 'margin-bottom', PadmaOption::get( 'wrapper-bottom-margin', 'general', 30 ) );

	PadmaElementsData_Upgrade34::set_property( 'default-elements', 'default-block', 'margin-bottom', PadmaOption::get( 'block-bottom-margin', 'general', 10 ) );

	/* Convert Media blocks to Slider or Embed blocks */
	$media_blocks = PadmaBlocksData_Upgrade34::get_blocks_by_type( 'media' );

	if ( is_array( $media_blocks ) && count( $media_blocks ) ) {

		foreach ( $media_blocks as $media_block_id => $media_block_layout_id ) {

			$media_block = PadmaBlocksData_Upgrade34::get_block( $media_block_id );

			$media_block_mode = padma_get( 'mode', $media_block['settings'], 'embed' );

			switch ( $media_block_mode ) {

				case 'embed':

					PadmaBlocksData_Upgrade34::update_block( $media_block['layout'], $media_block['id'], array(
						'type' => 'embed'
					) );

					break;

				case 'image-rotator':

					$slider_images = array();

					foreach ( padma_get( 'images', $media_block['settings'], array() ) as $media_block_image ) {

						$slider_images[] = array(
							'image' => $media_block_image,
							'image-description' => null,
							'image-hyperlink' => null
						);

					}

					PadmaBlocksData_Upgrade34::update_block( $media_block['layout'], $media_block['id'], array(
						'type' => 'slider',
						'settings' => array(
							'images' => $slider_images
						)
					) );

					break;

			}

		}

	}

}
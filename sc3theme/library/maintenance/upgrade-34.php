<?php
/**
 * Pre-3.4
 *
 * - Change block and wrapper margins to Design Editor values
 * - Convert Media blocks to Slider or Embed blocks
 **/
add_action( 'blox_do_upgrade_34', 'blox_do_upgrade_34' );
function blox_do_upgrade_34() {

	require_once BLOX_LIBRARY_DIR . '/maintenance/legacy-classes.php';

	/* Change block and wrapper margins to Design Editor values */
	BloxElementsData_Upgrade34::set_property( 'structure', 'wrapper', 'margin-top', BloxOption::get( 'wrapper-top-margin', 'general', 30 ) );
	BloxElementsData_Upgrade34::set_property( 'structure', 'wrapper', 'margin-bottom', BloxOption::get( 'wrapper-bottom-margin', 'general', 30 ) );

	BloxElementsData_Upgrade34::set_property( 'default-elements', 'default-block', 'margin-bottom', BloxOption::get( 'block-bottom-margin', 'general', 10 ) );

	/* Convert Media blocks to Slider or Embed blocks */
	$media_blocks = BloxBlocksData_Upgrade34::get_blocks_by_type( 'media' );

	if ( is_array( $media_blocks ) && count( $media_blocks ) ) {

		foreach ( $media_blocks as $media_block_id => $media_block_layout_id ) {

			$media_block = BloxBlocksData_Upgrade34::get_block( $media_block_id );

			$media_block_mode = blox_get( 'mode', $media_block['settings'], 'embed' );

			switch ( $media_block_mode ) {

				case 'embed':

					BloxBlocksData_Upgrade34::update_block( $media_block['layout'], $media_block['id'], array(
						'type' => 'embed'
					) );

					break;

				case 'image-rotator':

					$slider_images = array();

					foreach ( blox_get( 'images', $media_block['settings'], array() ) as $media_block_image ) {

						$slider_images[] = array(
							'image' => $media_block_image,
							'image-description' => null,
							'image-hyperlink' => null
						);

					}

					BloxBlocksData_Upgrade34::update_block( $media_block['layout'], $media_block['id'], array(
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
<?php
class PadmaVisualEditorIframeGrid {


	public static function display_grid_blocks($blocks, $wrapper) {
		
		echo '<div class="grid-container">';
				
			if ( is_array($blocks) ) {

				foreach ($blocks as $block_id => $block) {
						
					PadmaBlocks::display_block($block, 'grid');

				}

			}

			/* Mirrored wrapper notice */
				$mirror_wrapper = PadmaWrappersData::get_wrapper_mirror($wrapper);
				$mirror_wrapper_layout = $mirror_wrapper ? PadmaLayout::get_name(padma_get('layout', $mirror_wrapper)) : null;
				$mirror_wrapper_alias = padma_get('alias', $mirror_wrapper) ? '(' . padma_get('alias', $mirror_wrapper) . ')' : null;

				echo '<div class="wrapper-mirror-notice">
						<div>
						<h2>Wrapper Mirrored</h2>
						<p>This wrapper is mirroring the blocks in a wrapper <span class="wrapper-mirror-notice-alias">' . $mirror_wrapper_alias . '</span> <span class="wrapper-mirror-notice-layout">from "' . $mirror_wrapper_layout . '" layout</span></p>
						<small>Mirroring can be disabled via Wrapper Options in the right-click menu</small>
						</div>
					</div><!-- .wrapper-mirror-notice -->';
			/* End mirrored wrapper notice */

		echo '</div><!-- .grid-container -->';
		
	}


	public static function display_canvas() {

		echo '<!DOCTYPE HTML>
		<html lang="en">

		<head>

			<meta charset="' . get_bloginfo('charset') . '" />
			<link rel="profile" href="http://gmpg.org/xfn/11" />

			<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
			<meta http-equiv="cache-control" content="no-cache" />

			<title>Visual Editor Grid: ' . wp_title(false, false) . '</title>';

			do_action('padma_grid_iframe_head');

		echo '</head><!-- /head -->

		<body class="visual-editor-iframe-grid ' . join(' ', get_body_class()) . '">';

			$wrappers = PadmaWrappersData::get_wrappers_by_layout(PadmaLayout::get_current_in_use());
			$blocks = PadmaBlocksData::get_blocks_by_layout(PadmaLayout::get_current_in_use());
		
			echo '<div id="whitewrap">';

			foreach ( $wrappers as $wrapper_id => $wrapper ) {

				/* Setup wrapper classes */
					$wrapper_settings = padma_get('settings', $wrapper, array());
					$wrapper_classes = array('wrapper');

					$wrapper_classes[] = PadmaWrappers::is_independent_grid($wrapper) ? 'independent-grid' : null;
					$wrapper_classes[] = PadmaWrappers::is_fluid($wrapper) ? 'wrapper-fluid' : 'wrapper-fixed';
					$wrapper_classes[] = PadmaWrappers::is_grid_fluid($wrapper) ? 'wrapper-fluid-grid' : 'wrapper-fixed-grid';

					if ( PadmaWrappersData::is_wrapper_mirrored($wrapper) )
						$wrapper_classes[] = 'wrapper-mirrored';

				/* Populate wrapper with its blocks */
					$wrapper_blocks = array();

					foreach ( $blocks as $block_id => $block ) {

						/* Grab blocks belonging to this wrapper */
						if ( padma_get('wrapper_id', $block, PadmaWrappers::$default_wrapper_id) == $wrapper_id )
							$wrapper_blocks[$block_id] = $block;

						/* If last wrapper, grab all blocks on this layout with invalid wrapper IDs to make sure they're editable somewhere */
						$last_wrapper_id = array_slice(array_keys($wrappers), -1, 1);
						$last_wrapper_id = $last_wrapper_id[0];

						if ( $last_wrapper_id == $wrapper_id && !padma_get(padma_get('wrapper_id', $block, PadmaWrappers::$default_wrapper_id), $wrappers) )
							$wrapper_blocks[$block_id] = $block;

					}

				/* Output the wrapper */
				echo '<div id="wrapper-' . PadmaWrappers::format_wrapper_id($wrapper_id) . '" class="' . implode(' ', array_filter($wrapper_classes)) . '" data-wrapper-settings="' . esc_attr(json_encode($wrapper_settings)) . '" data-id="' . PadmaWrappers::format_wrapper_id($wrapper_id) . '" data-alias="' . esc_attr(stripslashes(padma_get('alias', $wrapper_settings))) . '">';

					echo '<div class="wrapper-mirror-overlay"></div><!-- .wrapper-mirror-overlay -->';
				
					self::display_grid_blocks($wrapper_blocks, $wrapper);
				
				echo '</div><!-- #wrapper-' . $wrapper_id . ' -->';

			}

		echo '<div id="wrapper-buttons-template">';
			echo '<div class="wrapper-top-margin-handle wrapper-handle wrapper-margin-handle" title="Drag to change wrapper top margin"><span></span><span></span><span></span></div>';
			echo '<div class="wrapper-drag-handle wrapper-handle tooltip tooltip-right" title="Drag to change wrapper order"><span></span><span></span><span></span></div>';
			echo '<div class="wrapper-bottom-margin-handle wrapper-handle wrapper-margin-handle" title="Drag to change wrapper bottom margin"><span></span><span></span><span></span></div>';

			echo '<div class="wrapper-options tooltip tooltip-right" title="Click to open wrapper options"><span></span></div>';
		echo '</div><!-- .wrapper-buttons -->';


		do_action('padma_grid_iframe_footer');
			
		echo '</div><!-- #whitewrap -->
		</body>
		</html>';

	}


	public static function show() {

		//Prevent any type of caching on this page
		header( 'cache-control: private, max-age=0, no-cache' );

		if ( !defined('DONOTCACHEPAGE') ) {
			define('DONOTCACHEPAGE', true);
		}

		if ( ! defined( 'DONOTCACHEDB' ) ) {
			define( 'DONOTCACHEDB', true );
		}

		if ( ! defined( 'DONOTCACHCEOBJECT' ) ) {
			define( 'DONOTCACHCEOBJECT', true );
		}

		if ( !defined('DONOTMINIFY') ) { 
			define('DONOTMINIFY', true);
		}
		
		add_action('padma_grid_iframe_head', array(__CLASS__, 'print_styles'), 12);
		add_action('padma_grid_iframe_styles', array(__CLASS__, 'enqueue_canvas_assets'));
		
		self::display_canvas();
		
	}


	public static function enqueue_canvas_assets() {

		wp_enqueue_style( 'padma-ve-iframe-grid', padma_url() . '/library/visual-editor/css/iframe-grid.css' );

		PadmaCompiler::register_file(array(
			'name' => 've-iframe-grid-dynamic',
			'format' => 'css',
			'fragments' => array(
				array('PadmaDynamicStyle', 'wrapper')
			),
			'dependencies' => array(
				BLOX_LIBRARY_DIR . '/media/dynamic/style.php'
			)
		));

	}


	public static function print_styles() {
		
		global $wp_styles;
		$wp_styles = null;
		
		do_action('padma_grid_iframe_styles');
		
		wp_print_styles();
		
	}


}
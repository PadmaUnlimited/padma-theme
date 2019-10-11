<?php

/**
 *
 * Based on Headway Toolkit 1.1.2
 *
 */

class PadmaBlocksAnywhere {

	private static $blocks = array();

	static function init() {

		add_filter( 'padma_compiler_trigger_url', array( __CLASS__, 'add_current_layout' ) );
		add_shortcode( 'padma-block', array( __CLASS__, 'create_block_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'padma_blocks_anywhere', array( __CLASS__, 'create_block_shortcode' ) );

	}


	/* we add the current layout to the compiler uncached files so that we can call it using padma_get('current-layout') later on */
	static function add_current_layout( $url ) {

	    return add_query_arg( array( 'current-layout' => PadmaLayout::get_current() ), $url );

	}


	static function create_block_shortcode( $atts ) {

		global $post;

		$this_post = $post;

		extract( shortcode_atts( array(
			'id' => '',
			'post_id' => ''
		), $atts ) );


		ob_start();

		$block = PadmaBlocksData::get_block( $id );

		/*	Register IDs to use them later	*/
		self::$blocks[] = $id;

		/* we reset the query so that we can get the global id in case the shortcode is in a index view */
		wp_reset_query();

		$current_layout = PadmaLayout::get_current();

		/* this is a fix for the front page */
		if ( $current_layout == 'front_page' && get_option( 'show_on_front' ) == 'page' )
			$post = get_post( get_option( 'page_on_front' ) );


		$layout_id = !empty( $post_id ) ? $post_id : padma_get_int( $block['layout'] );

		if ( empty( $layout_id ) && $block['type'] == 'content' && $block['settings']['mode'] == 'default' ) {

			echo '<div class="alert alert-red">' . __('This block does not have any post assigned. Please specify the post_id your would like to display as a shortcode parameter.', 'padma') . '</div>';

		} else {

			/* we modify the query if the layout has and id so that we get the appropriate content */
			if ( $layout_id )
				if ( get_post_status( $layout_id ) != 'draft' ) {

					global $wp_query;

					$args = array(
						'p' => $layout_id,
						'post_type' => 'any'
					);

					$wp_query = new WP_Query( $args );

					/* we also modifiy the global $post in case blocks would be using that */
					$post = $wp_query->post;

				}

			PadmaBlocks::display_block( $block['id'] );

			/* we reset the query again so that we can continue displaying the appropriate content */
			wp_reset_query();

		}

		$output_string = ob_get_contents();

		ob_end_clean();

		if ( empty( $output_string ) )
			return '<div class="alert alert-yellow">' . __('The padma block shortcut ID you have entered does not exist. Please enter a valid block ID!', 'padma') . '</div>';
		else
			return $output_string;


	}


	static function enqueue_assets() {

		/* we prevent this from firing in the admin and VE. we added the padma call condition for certain plugins template pages */
		if ( is_admin() || PadmaRoute::is_visual_editor() )
			return false;


		$current_layout = PadmaLayout::get_current();

		/* we enqueue assets files */
		self::get_assets( 'files' );

		if ( self::dynamic_js() != '' ) {

			/* we compile the dynamic js */
			$script_name = 'padma-blocks-anywhere-dynamic-js-layout-' . PadmaLayout::get_current();

			PadmaCompiler::register_file( array(
				'name' => $script_name,
				'format' => 'js',
				'fragments' => array(
					array( 'PadmaBlocksAnywhere', 'dynamic_js' )
				),
				'enqueue' => false
			) );

			if ( strlen( (string) self::dynamic_js() ) > 0 )
				wp_enqueue_script( $script_name, PadmaCompiler::get_url( $script_name ), array( 'jquery' ) );
		}

		if ( self::dynamic_css() != '' ) {

			/* we compile the dynamic css and the css fixes */
			$css_name = 'padma-blocks-anywhere-css-layout-' . PadmaLayout::get_current();

			$fragments['dynamic-block-css'] = array('PadmaBlocksAnywhere', 'dynamic_css');

			PadmaCompiler::register_file( array(
				'name' => $css_name,
				'format' => 'css',
				'fragments' => $fragments,
				'dependencies' => array(
					PADMA_LIBRARY_DIR . '/media/dynamic/style.php'
				)
			) );

		}

	}

	static function get_assets( $type ) {

		$current_layout = PadmaLayout::get_current();

		/* if caching is disabled, we have to query it manually */
		if ( !$current_layout )
			$current_layout = padma_get( 'current-layout' );

		if ( version_compare('0.0.17', PADMA_VERSION, '<=') )
			$layout = explode( '||', $current_layout );
		else			
			$layout = explode( '-', $current_layout );

		$page_set = get_option( 'show_on_front' ) == 'page' ? true : false;

		if ( end( $layout ) == 'index' && $page_set )
			$post_id = get_option( 'page_for_posts' );
		elseif ( end( $layout ) == 'front_page' && $page_set )
			$post_id = get_option( 'page_on_front' );
		else
			$post_id = end( $layout );

		$post = get_post( $post_id );

		$shortcodes_ids = array();

		/* we find all the blocks-anywhere shortcodes in the content in case it wasn't added in the helper */
		if ( isset( $post->post_content ) ) {

			preg_match_all( '/\[padma\-block id=["|\'](.*?)["|\']/', $post->post_content, $this_shortcodes_ids );

			foreach ( $this_shortcodes_ids[1] as $key => $id )
				array_push( $shortcodes_ids, $id );

		}

		/* we avoid duplicate just in case */
		$shortcodes_ids = array_unique( $shortcodes_ids );

		$data = '';

		if ( !empty( $shortcodes_ids ) ) {

			foreach ( $shortcodes_ids as $block_id ) {

				$block = PadmaBlocksData::get_block( $block_id );

				$block_options = padma_get( $block['id'], PadmaBlocks::$block_actions['block-objects'] );
				$original_block = null;

				if ( !$block_options )
					continue;

				/* we use the original block id if it is mirror */
				if ( $possible_mirror_id = PadmaBlocksData::is_block_mirrored( $block_options, true ) ) {

					$original_block = $block_options;

					$block['id'] = $possible_mirror_id;
					$block_options = padma_get( $block['id'], PadmaBlocks::$block_actions['block-objects'] );

				}

				/* from this point, use legacy_id if present */
				if ( $block_id = self::_get( 'legacy_id', $block_options ) ) {

					$block['id'] = $block_id;
					$block_options['id'] = $block_id;

				}


				switch ( $type ) {

					case 'js':

						if ( is_callable( array( $block_options['class'], 'dynamic_js' ) ) )
							$data .= call_user_func( array( $block_options['class'], 'dynamic_js' ), $block['id'], $block_options, $original_block );

						elseif ( is_callable( array( $block_options['class'], 'js_content' ) ) )
							$data .= call_user_func( array( $block_options['class'], 'js_content' ), $block['id'], $block_options, $original_block );

					break;

					case 'css':

						if ( is_callable( array( $block_options['class'], 'dynamic_css' ) ) )
							$data .= call_user_func( array( $block_options['class'], 'dynamic_css' ), $block['id'], $block_options, $original_block );

					break;

					case 'files':

						if ( is_callable( array( $block_options['class'], 'enqueue_action' ) ) )
							call_user_func( array( $block_options['class'], 'enqueue_action' ), $block['id'], $block_options, $original_block );

					break;

				}

			}

		}


		return $data;

	}


	static function has_assets( $block_id ) {

		$get_block = self::get_block( $block_id );
		$block_options = $get_block['block-options'];

		if ( is_callable( array( $block_options['class'], 'dynamic_js' ) ) )
			return true;

		if ( is_callable( array( $block_options['class'], 'dynamic_css' ) ) )
			return true;

		if ( is_callable( array( $block_options['class'], 'enqueue_action' ) ) )
			return true;

		return false;

	}


	static function get_block( $block_id ) {

		$block_options = padma_get( $block_id, PadmaBlocks::$block_actions['block-objects'] );
		$original_block = null;

		if ( !$block_options )
			return;

		/* we use the original block id if it is mirror */
		if ( $possible_mirror_id = PadmaBlocksData::is_block_mirrored( $block_options, true ) ) {

			$original_block = $block_options;

			$block_id = $possible_mirror_id;
			$block_options = padma_get( $block_id, PadmaBlocks::$block_actions['block-objects'] );

		}

		return array(
			'block-id' => $block_id,
			'block-options' => $block_options,
			'orginal-block' => $original_block,
		);

	}


	static function dynamic_js() {

		return self::get_assets( 'js' );

	}


	static function dynamic_css() {

		return self::get_assets( 'css' );

	}


	public static function _get( $name, $array = false, $default = null ) {

		if ( $array === false )
			$array = $_GET;

		if ( (is_string( $name ) || is_numeric( $name )) && !is_float( $name ) ) {

			if ( is_array( $array ) && isset( $array[$name] ) )
				return $array[$name];
			elseif ( is_object( $array ) && isset( $array->$name ) )
				return $array->$name;
			elseif ( is_string( $array ) )
				if ( $name == $array )
					return true;
				else
					return false;

		}

		return $default;	

	}

}
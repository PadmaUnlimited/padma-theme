<?php

/**
 *
 * Based on AdvancedGutenbergBlocks\Services\Blocks
 *
 */

class PadmaGutenbergBlocks {

	private static $categories = array();
	private static $blocks_categories = array();

	public function __construct(){
		self::$categories = array();
	}

	public static function init() {

		if(!PadmaOption::get('padma-blocks-as-gutenberg-blocks'))
			return;

		/**
		 *
		 * Feature only for WP5+
		 *
		 */

		if(function_exists('classicpress_version_short')){
			return;
		}

		if(!function_exists('register_block_type')){
			return;
		}

		// Add Gutenberg Block categories
		add_filter( 'block_categories', array( __CLASS__, 'add_block_category' ), 10, 2 );

		// Register Padma Blocks as Gutenberg Blocks
		add_action('init', array(__CLASS__,'padma_blocks_as_gutenberg_blocks'));

	}


	/**
	 *
	 * register Padma Blocks Category
	 *
	 */

	public static function add_block_category( $categories, $post ){

		if ( !is_admin() )
			return false;

		$categories = array_merge($categories, self::get_categories());		
		return $categories;
	}


	/**
	 *
	 * Get Padma Categories
	 *
	 */
	public static function get_categories(){
		return self::$categories;
	}

	/**
	 *
	 * Add Category to array
	 *
	 */
	public static function add_categories($slug, $title){
		self::$categories[] = array(
			'slug' => $slug,
			'title' => $title
		);
	}




	/**
	 *
	 * Display Padma Blocks in Gutenberg Editor
	 *
	 */	
	public static function padma_blocks_as_gutenberg_blocks(){

		if ( !is_admin() || !PadmaOption::get('padma-blocks-as-gutenberg-blocks'))
			return;

		global $pagenow;
		if ( $pagenow != 'post.php' && $pagenow != 'post-new.php' )
			return;


		Padma::load('visual-editor/layout-selector');
		$layouts = $blocks = array();

		foreach (PadmaLayoutSelector::get_templates() as $key => $value) {

			if( ! $value['customized'])
				continue;

			$layouts[] = array(
				'id'	=> $value['id'],
				'name'	=> $value['name'],
				'url'	=> $value['url'],
			);
		}

		foreach (PadmaLayoutSelector::get_basic_pages() as $key => $value) {

			if( ! $value['customized'])
				continue;

			$layouts[] = array(
				'id'	=> $value['id'],
				'name'	=> $value['name'],
				'url'	=> $value['url'],
			);
		}

		foreach ($layouts as $key => $params) {

			self::add_categories('padma-' . $params['id'], "Padma > " . $params['name']);

			$blocks = array_merge($blocks,PadmaBlocksData::get_blocks_by_layout($params['id']));

			foreach ($blocks as $block_id => $args) {

				$block = PadmaBlocksData::get_block($block_id);				
				if( empty($block['settings']['show-as-gutenberg-block']) )
					continue;

				$block_name = ucfirst($args['type']) . ' > ';

				if( !empty($args['settings']['alias']) )
					$block_name .= $args['settings']['alias'];
				else
					$block_name .= $block_id;

				$URL = site_url() . '/?padma-trigger=block-js&block-id='.$block_id;

				wp_enqueue_script(
				    'padma-' . $block_id,
				    $URL,
				    array( 'wp-blocks', 'wp-element' ),
				    true
			  	);

				register_block_type('padma/' . $block['type']);

				self::$blocks_categories[$block_id] = $params['name'];

			}
		}


	}


	public static function block_js(){

		if(!PadmaOption::get('padma-blocks-as-gutenberg-blocks'))
			return;

		$expires = 60 * 60 * 24 * 30;

		header("Pragma: public");
		header("Cache-Control: max-age=".$expires);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
		header('Content-Type: application/javascript');

		$block_id = padma_get('block-id');
		$block = PadmaBlocksData::get_block( $block_id );

		echo self::render_js($block);


	}


	private function render_js($block){

		$block_name = '';

		if( !empty($block['settings']['alias']) )
			$block_name .= $block['settings']['alias'];
		else
			$block_name .= $block['id'];

		$block_name = strtolower($block_name);
		$block_name = str_replace(' ', '-', $block_name);
		$block_name = preg_replace('/[\W]/', '-', $block_name);
		$block_name = preg_replace('/[\-]+/', '-', $block_name);

		$category = 'padma-' . $block['layout'];


		$blockStyle = array(
			'backgroundColor' => '#900',
	        'color' => '#fff',
	        'padding' => '20px',
		);
		$blockStyle = json_encode($blockStyle);


		$supports = array(
			'customClassName' => 'false',
			'className' => 'false',
			'html' => 'true',
		);
		$supports = json_encode($supports);


		$attributes = array(
			'content' => array(
					'type' => 'string',
					'source' => 'html',
				)
		);
		$attributes = json_encode($attributes);

		$js = '( function( blocks, element, __, components) {

				var el = element.createElement;
				var blockStyle = \'' . $blockStyle . '\' ;

			    blocks.registerBlockType( "padma/' . $block_name . '", {

			        title: "'. $block_name .'",
			        icon: "grid-view",
			        keywords: [ "padma","'.$block_name.'" ],
			        category: "'.$category.'",
			        supports: ' . $supports . ',
					attributes: ' . $attributes . ',									
			        edit: function() {
			        	return el(
			        		"div",
			        		{},
			        		"[padma-block id=\''. $block['id'] . '\']"
			        	)
			        	},
			        save: function() {
			            return el(
			                "div",
			                {  },
			              	"[padma-block id=\''. $block['id'] . '\']"
			            );
			        },
			    } );
			}(
			    window.wp.blocks,
			    window.wp.element,
			    window.wp.i18n,
			    window.wp.components,
			) );';

		return $js;
	}
}

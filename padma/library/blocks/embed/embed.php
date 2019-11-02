<?php

class PadmaEmbedBlock extends PadmaBlockAPI {


	public $id;
	public $name;
	public $options_class;
	public $description;
	public $categories;

	function __construct(){

		$this->id = 'embed';	
		$this->name = __('Embed','padma');
		$this->options_class = 'PadmaEmbedBlockOptions';	
		$this->description = __('The Embed block allows you to embed YouTube, Vimeo, or any other popular oEmbed supported service.','padma');
		$this->categories = array('core','media');
	}


	function init() {

		add_filter('oembed_result', array(__CLASS__, 'add_embed_wmode_transparent'));
		add_filter('oembed_result', array(__CLASS__, 'add_iframe_wmode_transparent'));

	}


	function content($block) {

		if ( $embed_url = parent::get_setting($block, 'embed-url', false) ) {

			$block_width = PadmaBlocksData::get_block_width($block);
			$block_height = PadmaBlocksData::get_block_height($block);	

			$embed_code = wp_oembed_get($embed_url, array(
				'width' => $block_width,
				'height' => $block_height,
			));

			//Make the width and height exactly what the block's dimensions are.
			$embed_code = preg_replace(array('/width="\d+"/i', '/height="\d+"/i'), array('width="' . $block_width . '"', 'height="' . $block_height . '"'), $embed_code);

			echo $embed_code;

		} else {

			echo '<div class="alert alert-yellow"><p>' . __('There is no content to display. Please enter a valid embed URL in the visual editor.','padma') . '</p></div>';

		}

	}


	/**
	 * Added to fix the issue of Flash appearing over drop down menus.
	 **/
	public static function add_embed_wmode_transparent($html) {

		//If no <embed> exists, don't do anything.
		if ( strpos($html, '<embed ') === false )
			return $html;

		return str_replace('</param><embed', '</param><param name="wmode" value="transparent"></param><embed wmode="transparent" ', $html);	

	}


	/**
	 * If the oEmbed HTML is using an iframe instead of <embed>, add a query var to the URL of the iframe to tell it to use wmode=transparent. 
	 **/
	public static function add_iframe_wmode_transparent($html) {

		//If no iframe exists, don't do anything.
		if ( strpos($html, '<iframe') === false )
			return $html;

		$url_search = preg_match_all('/src=[\'\"](.*?)[\'\"]/', $html, $url);		
		$url = $url[1][0];

		//Add the query var
		$url = add_query_arg(array('wmode' => 'transparent'), $url);

		//Place the URL back in
		return preg_replace('/src=[\'\"](.*?)[\'\"]/', 'src="' . $url . '"', $html);

	}

	function setup_elements() {

		$this->register_block_element(array(
			'id' => 'iframe',
			'name' => 'iframe',
			'selector' => '.fluid-width-video-wrapper iframe',
		));

		$this->register_block_element(array(
			'id' => 'object',
			'name' => 'Object',
			'selector' => '.fluid-width-video-wrapper object',
		));

		$this->register_block_element(array(
			'id' => 'embed',
			'name' => 'embed',
			'selector' => '.fluid-width-video-wrapper embed',
		));

		$this->register_block_element(array(
			'id' => 'div',
			'name' => 'div',
			'selector' => 'div',
		));

	}


}


class PadmaEmbedBlockOptions extends PadmaBlockOptionsAPI {

	public $tabs;
	public $inputs;

	function __construct($block_type_object){

		parent::__construct($block_type_object);

		$this->tabs = array(
			'embed-options' => __('Embed Options','padma')
		);

		$this->inputs = array(
			'embed-options' => array(
				'embed-notice' => array(
					'name' => 'embed-notice',
					'type' => 'notice',
					'notice' => __('Enter the URL <strong>(No HTML)</strong> to the media you wish to embed.  We support most major video and photo sites including (but not limited to) YouTube, Vimeo, Flickr, blip.tv, Hulu, and more.  <em>Need more info about oEmbed?  <a href="http://codex.wordpress.org/Embeds" target="_blank">Read More &rarr;</a></em>','padma')
				),

				'embed-url' => array(
					'type' => 'text',
					'name' => 'embed-url',
					'label' => __('Embed URL','padma'),
					'default' => null,
					'placeholder' => __('URL of Media','padma')
				)
			)
		);

	}

}
<?php

blox_register_block('BloxListingsBlock', blox_url() . '/library/blocks/listings');

class BloxListingsBlock extends BloxBlockAPI {
	
	
	public $id = 'listings';
	
	public $name = 'Listings';
	
	public $options_class = 'BloxListingsBlockOptions';

	public $description = 'List out your posts, custom post types, categories, tags, custom taxonomies, authors, pages, and comments.';

	static $block = null;


	function init() {
		
		require_once 'block-options.php';

		require_once BLOX_LIBRARY_DIR . '/blocks/listings/content-display.php';

		
	}

	function setup_elements() {

		$this->register_block_element(array(
			'id' => 'list-items',
			'name' => 'List Container',
			'selector' => 'ul.list-items'
		));

		$this->register_block_element(array(
			'id' => 'list-item',
			'name' => 'List Item',
			'selector' => 'ul.list-items li'
		));

		$this->register_block_element(array(
			'id' => 'list-item-link',
			'name' => 'List Item Link',
			'selector' => 'ul.list-items li a'
		));
		
	}

	function content($block) {

		$listing_block_display = new BloxListingBlockDisplay($block);
		$listing_block_display->display();

	}


	
	
}
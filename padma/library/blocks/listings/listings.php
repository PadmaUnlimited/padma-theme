<?php

class PadmaListingsBlock extends PadmaBlockAPI {
	
	
	public $id = 'listings';	
	public $name = 'Listings';	
	public $options_class = 'PadmaListingsBlockOptions';
	public $description = 'List out your posts, custom post types, categories, tags, custom taxonomies, authors, pages, and comments.';
	static $block = null;
	public $categories 	= array('core','content');


	function init() {
		
		require_once 'block-options.php';

		require_once PADMA_LIBRARY_DIR . '/blocks/listings/content-display.php';

		
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

		$listing_block_display = new PadmaListingBlockDisplay($block);
		$listing_block_display->display();

	}


	
	
}
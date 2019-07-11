<?php

class PadmaContentBlock extends PadmaBlockAPI {
	
	
	public $id 				= 'content';	
	public $name 			= 'Content';		
	public $options_class 	= 'PadmaContentBlockOptions';
	public $description 	= 'Main content area to show the current page\'s content or the latest posts.  This is considered the "Loop" in other themes.';
	public $categories 		= array('core','content');	
			
	
	function init() {
		
		/* Load dependencies */
		require_once PADMA_LIBRARY_DIR . '/blocks/content/content-display.php';
		
		/* Set up the comments template */
		add_filter('comments_template', array(__CLASS__, 'add_blank_comments_template'), 5);
		
		/* Set up editor style */
		add_filter('mce_css', array(__CLASS__, 'add_editor_style'));

		/* Add .comment class to all pingbacks */
		add_filter('comment_class', array(__CLASS__, 'add_comment_class_to_all_types'));

	}

	
	public static function add_blank_comments_template() {
		
		return PADMA_LIBRARY_DIR . '/blocks/content/comments-template.php';
		
	}


	public static function add_comment_class_to_all_types($classes) {
		
		if ( !is_array($classes) )
			$classes = implode(' ', trim($classes));
				
		$classes[] = 'comment';
		
		return array_filter(array_unique($classes));
		
	}

	
	public static function add_editor_style($css) {
		
		if ( PadmaOption::get('disable-editor-style', false, false) )
			return $css;
		
		if ( !current_theme_supports('editor-style') )
			return $css;
			
		if ( !current_theme_supports('padma-design-editor') )
			return $css;

		PadmaCompiler::register_file(array(
			'name' => 'editor-style',
			'format' => 'css',
			'fragments' => array(
				'padma_content_block_editor_style'
			),
			'dependencies' => array(PADMA_LIBRARY_DIR . '/blocks/content/editor-style.php'),
			'enqueue' => false
		));

		return $css . ',' . PadmaCompiler::get_url('editor-style');

	}

	public static function dynamic_css($block_id, $block) {

		$css = '';

		if ( parent::get_setting($block, 'enable-column-layout') ) {

			$gutter_width = parent::get_setting($block, 'post-gutter-width', '20');

			$css = '';

			if ( PadmaResponsiveGrid::is_enabled() ) {
				$css .= '@media only screen and (min-width: ' . PadmaBlocksData::get_block_width($block) . 'px) {';
			}

				$css .= '#block-' . $block_id . ' .loop .entry-row .hentry {';

					$css .= 'margin-left: ' . self::width_as_percentage($gutter_width, $block) . '%;';
					$css .= 'width: ' . self::width_as_percentage(self::get_column_width($block), $block) . '%;';

				$css .= '}';

			if ( PadmaResponsiveGrid::is_enabled() ) {
				$css .= '}';
			}

		}

		return $css . "\n";

		
	}

	static function get_column_width($block) {

		$block_width = PadmaBlocksData::get_block_width($block);

		$columns = parent::get_setting($block, 'posts-per-row', '2');
		$gutter_width = parent::get_setting($block, 'post-gutter-width', '20');

		$total_gutter = $gutter_width * ($columns-1);

		$columns_width = (($block_width - $total_gutter) / $columns);

		return $columns_width; 
	}

	/* To make the layout responsive
	 * Works out a percentage value equivalent of the px value 
	 * using common responsive formula: target_width / container_width * 100
	 */	
	static function width_as_percentage($target = '', $block) {
		$block_width = PadmaBlocksData::get_block_width($block);
		
		if ($block_width > 0 )
			return ($target / $block_width)*100;

		return false;
	}

	
	function setup_elements() {

		$this->register_block_element(array(
			'id' => 'article',
			'name' => 'Article',
			'selector' => 'article',			
		));
		
		/* Classic Editor */
			$this->register_block_element(array(
				'id' => 'entry-container-hentry',
				'name' => 'Entry Container',
				'selector' => '.hentry',
				'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow', 'animation', 'transform', 'advanced', 'transition', 'outlines')
			));

				$this->register_block_element(array(
					'id' => 'page-container',
					'name' => 'Page Entry Container',
					'parent' => 'entry-container-hentry',
					'selector' => '.type-page'
				));

				$this->register_block_element(array(
					'id' => 'entry-container',
					'name' => 'Post Entry Container',
					'parent' => 'entry-container-hentry',
					'selector' => '.type-post'
				));

			
			$this->register_block_element(array(
				'id' => 'entry-row',
				'name' => 'Entry Row',
				'selector' => '.entry-row'
			));

			$this->register_block_element(array(
				'id' => 'title',
				'name' => 'Title',
				'selector' => '.entry-title',
				'states' => array(
					'Hover' => '.entry-title:hover', 
					'Clicked' => '.entry-title:active'
				)
			));
			
			$this->register_block_element(array(
				'id' => 'archive-title',
				'name' => 'Archive Title',
				'selector' => '.archive-title'
			));
			
			$this->register_block_element(array(
				'id' => 'entry-content',
				'name' => 'Body Text',
				'description' => 'All text including &lt;p&gt; elements',
				'selector' => 'div.entry-content, div.entry-content p'
			));
			
			$this->register_block_element(array(
				'id' => 'entry-content-hyperlinks',
				'name' => 'Body Hyperlinks',
				'selector' => 'div.entry-content a',				
				'states' => array(
					'Hover' => 'div.entry-content a:hover', 
					'Clicked' => 'div.entry-content a:active'
				)
			));

			$this->register_block_element(array(
				'id' => 'entry-content-images',
				'name' => 'Images',
				'selector' => 'div.entry-content img',
				'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow', 'animation', 'sizes')
			));

			$this->register_block_element( array(
				'id'         => 'entry-content-image-captions',
				'name'       => 'Image Captions',
				'selector'   => 'div.entry-content .wp-caption',
				'properties' => array( 'background', 'borders', 'padding', 'corners', 'box-shadow', 'animation' )
			) );

				$this->register_block_element( array(
					'id'       => 'entry-content-image-caption-image',
					'parent'   => 'entry-content-image-captions',
					'name'     => 'Images in Captions',
					'selector' => 'div.entry-content .wp-caption img',
					'properties' => array( 'background', 'borders', 'padding', 'corners', 'box-shadow', 'animation' )
				) );

				$this->register_block_element( array(
					'id'         => 'entry-content-image-caption-text',
					'parent'     => 'entry-content-image-captions',
					'name'       => 'Caption Text',
					'selector'   => 'div.entry-content .wp-caption .wp-caption-text'
				) );

			$this->register_block_element(array(
				'id' => 'entry-meta',
				'name' => 'Meta',
				'selector' => 'div.entry-meta'
			));

				$this->register_block_element(array(
					'id' => 'entry-meta-above',
					'name' => 'Meta Above Content',
					'selector' => 'div.entry-meta-above',
					'parent' => 'entry-meta'
				));			

				$this->register_block_element(array(
					'id' => 'entry-meta-below',
					'name' => 'Meta Below Content',
					'selector' => 'footer.entry-utility-below',
					'parent' => 'entry-meta'
				));			

				$this->register_block_element(array(
					'id' => 'entry-meta-links',
					'name' => 'Meta Hyperlinks',
					'selector' => 'div.entry-meta a, footer.entry-meta a',
					'parent' => 'entry-meta',					
					'states' => array(
					'Hover' => 'div.entry-meta a:hover, footer.entry-meta a:hover', 
					'Clicked' => 'div.entry-meta a:active, footer.entry-meta a:active'
				)
				));

				$this->register_block_element(array(
					'id' => 'entry-meta-author',
					'name' => 'Author Avatar Image',
					'selector' => '.avatar',
					'parent' => 'entry-meta'
				));

				$this->register_block_element(array(
					'id' => 'entry-meta-publisher',
					'name' => 'Publisher Logo container',
					'selector' => '.publisher-img',
					'parent' => 'entry-meta'
				));

				$this->register_block_element(array(
					'id' => 'entry-meta-publisher-image-container',
					'name' => 'Publisher Logo image container',
					'selector' => '.publisher-img .logo',
					'parent' => 'entry-meta'
				));

				$this->register_block_element(array(
					'id' => 'entry-meta-publisher-image-link',
					'name' => 'Publisher Logo link',
					'selector' => '.publisher-img .logo a',
					'parent' => 'entry-meta'
				));

				$this->register_block_element(array(
					'id' => 'entry-meta-publisher-image-file',
					'name' => 'Publisher Logo image',
					'selector' => '.publisher-img .logo a img',
					'parent' => 'entry-meta'
				));

				$this->register_block_element(array(
					'id' => 'entry-meta-publisher-meta',
					'name' => 'Publisher Logo meta data',
					'selector' => '.publisher-img meta',
					'parent' => 'entry-meta'
				));

				$this->register_block_element(array(
					'id' => 'entry-date',
					'name' => 'Post Entry Date',
					'parent' => 'entry-meta',
					'selector' => '.entry-date'
				));
			
			$this->register_block_element(array(
				'id' => 'heading',
				'name' => 'Heading',
				'selector' => 'div.entry-content h3, div.entry-content h2, div.entry-content h1'
			));

				$this->register_block_element(array(
					'id' => 'heading-h1',
					'parent' => 'heading',
					'name' => 'H1',
					'selector' => 'div.entry-content h1',
					'parent' => 'heading'
				));

				$this->register_block_element(array(
					'id' => 'heading-h2',
					'parent' => 'heading',
					'name' => 'H2',
					'selector' => 'div.entry-content h2'
				));

				$this->register_block_element(array(
					'id' => 'heading-h3',
					'parent' => 'heading',
					'name' => 'H3',
					'selector' => 'div.entry-content h3'
				));
			
			$this->register_block_element(array(
				'id' => 'sub-heading',
				'name' => 'Sub Heading',
				'selector' => 'div.entry-content h4, div.entry-content h5'
			));

				$this->register_block_element(array(
					'id' => 'sub-heading-h4',
					'parent' => 'sub-heading',
					'name' => 'H4',
					'selector' => 'div.entry-content h4'
				));

				$this->register_block_element(array(
					'id' => 'sub-heading-h5',
					'parent' => 'sub-heading',
					'name' => 'H5',
					'selector' => 'div.entry-content h5'
				));

				$this->register_block_element(array(
					'id' => 'content-ul-lists',
					'name' => 'Unordered Lists',
					'description' => '&lt;UL&gt;',
					'selector' => 'div.entry-content ul',
				));

				$this->register_block_element(array(
					'id' => 'content-ul-list-item',
					'name' => 'Unordered List Items',
					'description' => '&lt;LI&gt;',
					'selector' => 'div.entry-content ul li',					
				));

				$this->register_block_element(array(
					'id' => 'content-ol-lists',
					'name' => 'Ordered Lists',
					'description' => '&lt;OL&gt;',
					'selector' => 'div.entry-content ol',					
				));

				$this->register_block_element(array(
					'id' => 'content-list-item',
					'name' => 'Ordered List Items',
					'description' => '&lt;LI&gt;',
					'selector' => 'div.entry-content ol li',					
				));

			$this->register_block_element(array(
				'id' => 'post-thumbnail',
				'name' => 'Featured Image',
				'selector' => '.block-type-content a.post-thumbnail img',
				'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow', 'animation')
			));
			
			$this->register_block_element(array(
				'id' => 'more-link',
				'name' => 'Continue Reading Button',
				'selector' => 'div.entry-content a.more-link',
				'states' => array(
					'Hover' => 'div.entry-content a.more-link:hover',
					'Clicked' => 'div.entry-content a.more-link:active'
				)
			));
			
			$this->register_block_element(array(
				'id' => 'loop-navigation-link',
				'name' => 'Loop Navigation Button',
				'selector' => 'div.loop-navigation div.nav-previous a, div.loop-navigation div.nav-next a',
				'states' => array(
					'Hover' => 'div.loop-navigation div.nav-previous a:hover, div.loop-navigation div.nav-next a:hover',
					'Clicked' => 'div.loop-navigation div.nav-previous a:active, div.loop-navigation div.nav-next a:active'
				)
			));

			$this->register_block_element(array(
				'id' => 'comments-wrapper',
				'name' => 'Comments',
				'selector' => 'div#comments'
			));
			
			$this->register_block_element(array(
				'id' => 'comments-area',
				'name' => 'Comments Area',
				'selector' => 'ol.commentlist',
				'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow'),
				'parent' => 'comments-wrapper'
			));
			
			$this->register_block_element(array(
				'id' => 'comments-area-headings',
				'name' => 'Comments Area Headings',
				'selector' => 'div#comments h3',
				'parent' => 'comments-wrapper'
			));

			$this->register_block_element(array(
				'id' => 'comment-container',
				'name' => 'Comment Container',
				'selector' => 'li.comment',
				'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow', 'animation'),
				'parent' => 'comments-wrapper'
			));

			$this->register_block_element(array(
				'id' => 'comments-textarea',
				'name' => 'Add Comment Textarea',
				'selector' => '#comment',
				'parent' => 'comments-wrapper'
			));
			
			$this->register_block_element(array(
				'id' => 'comment-author',
				'name' => 'Comment Author',
				'selector' => 'li.comment .comment-author',
				'parent' => 'comments-wrapper'
			));
			
			$this->register_block_element(array(
				'id' => 'comment-meta',
				'name' => 'Comment Meta',
				'selector' => 'li.comment .comment-meta',
				'parent' => 'comments-wrapper'
			));

			$this->register_block_element(array(
				'id' => 'comment-meta-count',
				'name' => 'Comment Meta Count',
				'selector' => 'a.entry-comments',
				'parent' => 'comments-wrapper'
			));
			
			$this->register_block_element(array(
				'id' => 'comment-body',
				'name' => 'Comment Body',
				'selector' => 'li.comment .comment-body p',
				'properties' => array('fonts'),
				'parent' => 'comments-wrapper'
			));
			
			$this->register_block_element(array(
				'id' => 'comment-reply-link',
				'name' => 'Comment Reply Link',
				'selector' => 'a.comment-reply-link',
				'states' => array(
					'Hover' => 'a.comment-reply-link:hover',
					'Clicked' => 'a.comment-reply-link:active'
				),
				'parent' => 'comments-wrapper'
			));

			$this->register_block_element(array(
				'id' => 'comment-form-input-label',
				'name' => 'Comment Form Input Label',
				'selector' => 'div#respond label',
				'properties' => array('fonts'),
				'parent' => 'comments-wrapper'
			));

		/* End Classic Container */


		/*	Gutenberg */

			$this->register_block_element(array(
				'id' => 'gutenberg-audio-block',
				'name' => 'Gutenberg audio block',
				'selector' => '.wp-block-audio',
			));

			$this->register_block_element(array(
				'id' => 'gutenberg-video-block',
				'name' => 'Gutenberg video block',
				'selector' => '.wp-block-video',
			));

			$this->register_block_element(array(
				'id' => 'gutenberg-file-block',
				'name' => 'Gutenberg file block',
				'selector' => '.wp-block-file',
			));

			$this->register_block_element(array(
				'id' => 'gutenberg-image-block',
				'name' => 'Gutenberg image block',
				'selector' => '.wp-block-image',
			));

			$this->register_block_element(array(
				'id' => 'gutenberg-gallery-block',
				'name' => 'Gutenberg gallery block',
				'selector' => '.wp-block-gallery',
			));

			$this->register_block_element(array(
				'id' => 'gutenberg-gallery-block-item',
				'name' => 'Gutenberg gallery item',
				'selector' => '.wp-block-gallery-item',
			));

			$this->register_block_element(array(
				'id' => 'gutenberg-cover-block',
				'name' => 'Gutenberg cover block',
				'selector' => '.wp-block-cover',
			));

			$this->register_block_element(array(
				'id' => 'gutenberg-code-block',
				'name' => 'Gutenberg code block',
				'selector' => '.wp-block-code',
			));
			
			$this->register_block_element(array(
				'id' => 'gutenberg-preformatted-block',
				'name' => 'Gutenberg preformatted block',
				'selector' => '.wp-block-preformatted',
			));

			$this->register_block_element(array(
				'id' => 'gutenberg-pullquote-block',
				'name' => 'Gutenberg pullquote block',
				'selector' => '.wp-block-pullquote',
			));

			$this->register_block_element(array(
				'id' => 'gutenberg-table-block',
				'name' => 'Gutenberg table block',
				'selector' => '.wp-block-table',
			));
			
			$this->register_block_element(array(
				'id' => 'gutenberg-button-block',
				'name' => 'Gutenberg button block',
				'selector' => '.wp-block-button',
			));

			$this->register_block_element(array(
				'id' => 'gutenberg-columns-block',
				'name' => 'Gutenberg columns block',
				'selector' => '.wp-block-columns',
			));
			
			$this->register_block_element(array(
				'id' => 'gutenberg-media-text-block',
				'name' => 'Gutenberg media-text block',
				'selector' => '.wp-block-media-text',
			));

			$this->register_block_element(array(
				'id' => 'gutenberg-separator-block',
				'name' => 'Gutenberg separator block',
				'selector' => '.wp-block-separator',
			));

			$this->register_block_element(array(
				'id' => 'gutenberg-archives-block',
				'name' => 'Gutenberg archives block',
				'selector' => '.wp-block-archives',
			));

			$this->register_block_element(array(
				'id' => 'gutenberg-categories-block',
				'name' => 'Gutenberg categories block',
				'selector' => '.wp-block-categories',
			));

			$this->register_block_element(array(
				'id' => 'gutenberg-categories-block',
				'name' => 'Gutenberg categories block',
				'selector' => '.wp-block-categories .cat-item',
			));

			$this->register_block_element(array(
				'id' => 'gutenberg-latest-comments-block',
				'name' => 'Gutenberg latest-comments block',
				'selector' => '.wp-block-latest-comments',
			));			

			$this->register_block_element(array(
				'id' => 'gutenberg-categories-block',
				'name' => 'Gutenberg categories block',
				'selector' => '.wp-block-categories',
			));			

			$this->register_block_element(array(
				'id' => 'gutenberg-embed-block',
				'name' => 'Gutenberg embed block',
				'selector' => '.wp-block-embed',
			));			

		/*	End Gutenberg */


		/**
		 *
		 * Custom Fields
		 *
		 */
		$this->register_block_element(array(
			'id' => 'custom-fields',
			'name' => 'Custom Fields Group',
			'selector' => '.custom-fields',			
		));

			$this->register_block_element(array(
				'id' => 'custom-fields-div',
				'name' => 'Custom Fields Div',
				'selector' => '.custom-fields div',			
			));

			$this->register_block_element(array(
				'id' => 'custom-fields-p',
				'name' => 'Custom Fields text',
				'selector' => '.custom-fields p',
			));

			$this->register_block_element(array(
				'id' => 'custom-fields-a',
				'name' => 'Custom Fields Link',
				'selector' => '.custom-fields a',
			));

			$this->register_block_element(array(
				'id' => 'custom-fields-h1',
				'name' => 'Custom Fields H1',
				'selector' => '.custom-fields h1',
			));

			$this->register_block_element(array(
				'id' => 'custom-fields-h2',
				'name' => 'Custom Fields H2',
				'selector' => '.custom-fields h2',
			));

			$this->register_block_element(array(
				'id' => 'custom-fields-h3',
				'name' => 'Custom Fields H3',
				'selector' => '.custom-fields h3',
			));

			$this->register_block_element(array(
				'id' => 'custom-fields-h4',
				'name' => 'Custom Fields H4',
				'selector' => '.custom-fields h4',
			));

			$this->register_block_element(array(
				'id' => 'custom-fields-h5',
				'name' => 'Custom Fields H5',
				'selector' => '.custom-fields h5',
			));

			$this->register_block_element(array(
				'id' => 'custom-fields-h6',
				'name' => 'Custom Fields H6',
				'selector' => '.custom-fields h6',
			));

			$this->register_block_element(array(
				'id' => 'custom-fields-span',
				'name' => 'Custom Fields span',
				'selector' => '.custom-fields span',
			));
		
		
		
	}
	
	
	function content($block) {

		$block['custom-fields'] = $this->get_custom_fields($block);
		$content_block_display = new PadmaContentBlockDisplay($block);
		$content_block_display->display();
		
	}

	function get_custom_fields($block){

		$custom_fields_show = $custom_fields_label = $custom_fields_position = array();

		foreach ($block['settings'] as $name => $value) {
			
			$data = explode('-', $name);
			$post_type = $data[3];

			$custom_field = $name;
			$custom_field = str_replace('custom-field-show-' . $post_type . '-', '' , $custom_field);
			$custom_field = str_replace('custom-field-position-' . $post_type . '-', '' , $custom_field);
			$custom_field = str_replace('custom-field-label-' . $post_type . '-', '' , $custom_field);
			
			if ( strpos($name, 'custom-field-show') !== false ){				
				if($value){
					$custom_fields_show[$post_type][$custom_field] = $value;
				}				
			}
			
			if ( strpos($name, 'custom-field-position') !== false )
				$custom_fields_position[$post_type][$custom_field] = $value;

			if ( strpos($name, 'custom-field-label') !== false )
				$custom_fields_label[$post_type][$custom_field] = $value;
						
		}

		$data = array();

		foreach ($custom_fields_position as $post_type => $custom_fields) {
			foreach ($custom_fields as $field_name => $position) {
				if($custom_fields_show[$post_type][$field_name]){
					$label = $custom_fields_label[$post_type][$field_name];
					$data[$position][$post_type][$field_name] = $label;					
				}
			}
		}

		return $data;
	}
	
	
}


class PadmaContentBlockOptions extends PadmaBlockOptionsAPI {
	
	
	public $tab_notices = array(
		'mode' => 'The content block is extremely versatile.  If the default mode is selected, it will do what you expect it to do.  For example, if you add this on a page, it will display that page\'s content.  If you add it on the Blog Index layout, it will list the posts like a normal blog template and if you add this box on a category layout, it will list posts of that category.  If you wish to change what the content block displays, change the mode to <em>Custom Query</em> and use the settings in the <em>Query Filters</em> tab.',
		'query-setup' => 'For more control over queries and how the query is displayed, Padma works perfectly out-of-the-box with <a href="http://pluginbuddy.com/purchase/loopbuddy/" target="_blank">LoopBuddy</a>.',
		'meta' => '
			<p>The entry meta is the information that appears below the post title and below the post content.  By default, it will contain information about the entry author, the categories, and comments.</p>
			<p><strong>Available Variables:</strong></p>
			<p>%date% &bull; %modified_date% &bull; %time% &bull; %comments% &bull; %comments_no_link% &bull; %respond% &bull; %author% &bull; %author_no_link% &bull; %categories% &bull; %tags% &bull; %publisher% &bull; %publisher_img% &bull; %publisher_no_img%</p>
		'
	);
	
	
	public $tabs = array(
		'mode' 				=> 'Mode',
		'query-filters' 	=> 'Query Filters',
		'display' 			=> 'Display',
		'custom-fields'		=> 'Custom Fields',
		'meta' 				=> 'Meta',		
		'comments' 			=> 'Comments',
		'post-thumbnails' 	=> 'Featured Images'
	);

	
	public $inputs = array(
		
		'mode' => array(
			'mode' => array(
				'type' => 'select',
				'name' => 'mode',
				'label' => 'Query Mode',
				'tooltip' => '',
				'options' => array(
					'default' => 'Default Behavior',
					'custom-query' => 'Custom Query'
				),
				'toggle'    => array(
					'custom-query' => array(
						'show' => array(
							'li#sub-tab-query-filters'
						)
					),
					'default' => array(
						'hide' => array(
							'li#sub-tab-query-filters'
						)
					)
				)
			)
		),

		'query-filters' => array(
			'page-fetch-query-heading' => array(
				'name' => 'page-fetch--query-heading',
				'type' => 'heading',
				'label' => 'Fetch a Page'
			),

			'fetch-page-content' => array(
				'type' => 'select',
				'name' => 'fetch-page-content',
				'label' => 'Fetch Page Content',
				'tooltip' => 'Query options have no effect if you have chosen to Fetch a Page',
				'options' => 'get_pages()'
			),

			'custom-query-heading' => array(
				'name' => 'custom-query-heading',
				'type' => 'heading',
				'label' => 'Query Filters',
				'tooltip' => 'Query options have no effect if you have chosen to Fetch a Page\'s content above'
			),
			
			'categories' => array(
				'type' => 'multi-select',
				'name' => 'categories',
				'label' => 'Categories',
				'tooltip' => '',
				'options' => 'get_categories()'
			),
			
			'categories-mode' => array(
				'type' => 'select',
				'name' => 'categories-mode',
				'label' => 'Categories Mode',
				'tooltip' => '',
				'options' => array(
					'include' => 'Include',
					'exclude' => 'Exclude'
				)
			),

			'enable-tags' => array(
				'type' => 'checkbox',
				'name' => 'tags-filter',
				'label' => 'Tags Filter',
				'tooltip' => 'Check this to allow the tags filter show.',
				'default' => false,
				'toggle'    => array(
					'false' => array(
						'hide' => array(
							'#input-tags'
						)
					),
					'true' => array(
						'show' => array(
							'#input-tags'
						)
					)
				)
			),
			'tags' => array(
				'type' => 'multi-select',
				'name' => 'tags',
				'label' => 'Tags',
				'tooltip' => '',
				'options' => 'get_tags()'
			),

			
			'post-type' => array(
				'type' => 'multi-select',
				'name' => 'post-type',
				'label' => 'Post Type',
				'tooltip' => '',
				'options' => 'get_post_types()',
				'callback' => 'reloadBlockOptions(block.id)'
			),

			'post-status' => array(
				'type' => 'multi-select',
				'name' => 'post-status',
				'label' => 'Post Status',
				'tooltip' => '',
				'options' => 'get_post_status()'
			),
			
			'author' => array(
				'type' => 'multi-select',
				'name' => 'author',
				'label' => 'Author',
				'tooltip' => '',
				'options' => 'get_authors()'
			),
			
			'number-of-posts' => array(
				'type' => 'integer',
				'name' => 'number-of-posts',
				'label' => 'Number of Posts',
				'tooltip' => '',
				'default' => 10
			),
			
			'offset' => array(
				'type' => 'integer',
				'name' => 'offset',
				'label' => 'Offset',
				'tooltip' => 'The offset is the number of entries or posts you would like to skip.  If the offset is 1, then the first post will be skipped.',
				'default' => 0
			),
			
			'order-by' => array(
				'type' => 'select',
				'name' => 'order-by',
				'label' => 'Order By',
				'tooltip' => '',
				'options' => array(
					'date' => 'Date',
					'title' => 'Title',
					'rand' => 'Random',
					'comment_count' => 'Comment Count',
					'ID' => 'ID',
					'author' => 'Author',
					'type' => 'Post Type',
					'menu_order' => 'Custom Order'
				)
			),
			
			'order' => array(
				'type' => 'select',
				'name' => 'order',
				'label' => 'Order',
				'tooltip' => '',
				'options' => array(
					'desc' => 'Descending',
					'asc' => 'Ascending',
				)
			),
			'byid-include' => array(
				'type' => 'text',
				'name' => 'byid-include',
				'label' => 'Include by ID',
				'tooltip' => 'In both Include and Exclude by ID, you use a comma separated list of IDs of your post type.'
				),

			'byid-exclude' => array(
				'type' => 'text',
				'name' => 'byid-exclude',
				'label' => 'Exclude by ID',
				'tooltip' => 'In both Include and Exclude by ID, you use a comma separated list of IDs of your post type.'
			)
		),
	
		'display' => array(
			'read-more-text' => array(
				'type' => 'text',
				'label' => 'Read More Text',
				'name' => 'read-more-text',
				'default' => 'Continue Reading',
				'tooltip' => 'If excerpts are being shown or a featured post is truncated using WordPress\' read more shortcode, then this will be shown after the excerpt or truncated content.'
			),
			
			'show-titles' => array(
				'type' => 'checkbox',
				'name' => 'show-titles',
				'label' => 'Show Titles',
				'default' => true,
				'tooltip' => 'If you wish to only show the content and meta of the entry, you can hide the entry (post or page) titles with this option.'
			),

			'link-titles'  => array(
				'type' => 'checkbox',
				'name' => 'link-titles',
				'label' => 'Link Titles?',
				'default' => true,
				'tooltip' => 'If you wish to turn off the link to Post/Page titles, uncheck this'
			),

			'show-archive-title'  => array(
				'type' => 'checkbox',
				'name' => 'show-archive-title',
				'label' => 'Show Archive Title?',
				'default' => true,
				'tooltip' => 'If you wish to turn off the page title on archive layouts (e.g. category, tag, etc), uncheck this'
			),

			'show-archive-title-type' => array(
				'type' => 'select',
				'name' => 'show-archive-title-type',
				'default' => 'normal',
				'options' => array(
					'normal' => 'Normal',
					'only-archive-name' => 'Only archive name',
					'show-custom-archive-title' => 'Custom title',					
				),
				'label' => 'Archive Title type',
				'tooltip' => 'Display normal title, only archive (category, tag, etc) or Custom Archive Title',
				'toggle' => array(
					'normal' => array(
						'hide' => array(
							'#input-custom-archive-title',							
						)
					),
					'only-archive-name' => array(
						'hide' => array(
							'#input-custom-archive-title',
						)
					),
					'show-custom-archive-title' => array(
						'show' => array(
							'#input-custom-archive-title',
						)
					),
				)


			),

			'custom-archive-title'  => array(
				'type' => 'text',
				'name' => 'custom-archive-title',
				'label' => 'Custom Archive title',
				'tooltip' => 'Use custom title for archive. Use %archive% for category, tag, etc: example "Category Archives: %archive%"'				
			),
			
			'show-readmore' => array(
				'type' => 'checkbox',
				'name' => 'show-readmore',
				'label' => 'Show Read More',
				'default' => true,
				'tooltip' => 'Show and hide the continue reading or read more text/button.'
			),
			
			'entry-content-display' => array(
				'type' => 'select',
				'name' => 'entry-content-display',
				'label' => 'Entry Content Display',
				'tooltip' => 'The entry content is the actual body of the entry.  This is what you enter in the rich text area when creating an entry (post or page).  When set to normal, Padma will determine if full entries or excerpts should be displayed based off of the <em>Featured Posts</em> setting and what page is being displayed.<br /><br /><strong>Tip:</strong> Set this to <em>Hide Entry Content</em> to create a simple listing of posts.',
				'default' => 'normal',
				'options' => array(
					'normal' => 'Normal',
					'full-entries' => 'Show Full Entries',
					'excerpts' => 'Show Excerpts',
					'hide' => 'Hide Entry Content'
				),
				'toggle' => array(
					'normal' => array(
						'show' => array(
							'#input-custom-excerpts-heading',
							'#input-custom-excerpts',
						)
					),
					'excerpts' => array(
						'show' => array(
							'#input-custom-excerpts-heading',
							'#input-custom-excerpts',
						)
					),
					'full-entries' => array(
						'hide' => array(
							'#input-custom-excerpts-heading',
							'#input-custom-excerpts',
						)
					),
					'hide' => array(
						'hide' => array(
							'#input-custom-excerpts-heading',
							'#input-custom-excerpts',
						)
					)
				)
			),
			
			'show-entry' => array(
				'type' => 'checkbox',
				'name' => 'show-entry',
				'label' => 'Show Entry',
				'default' => true,
				'tooltip' => 'By default, the entries will always be shown.  However, there may be certain cases where you wish to show the entry content in one Content Block, but the comments in another.  With this option, you can do that.'
			),
			
			'comments-visibility' => array(
				'type' => 'select',
				'name' => 'comments-visibility',
				'label' => 'Comments Visibility',
				'default' => 'auto',
				'options' => array(
					'auto' => 'Automatic',
					'hide' => 'Always Hide Comments',
					'show' => 'Always Show Comments'
				),
				'tooltip' => 'When set to automatic, the comments will only show on single post pages.  However, there may be times where you want to force comment visibility to allow comments on pages.  Or, you may hide the comments if you wish to not see them at all.<br /><br /><strong>Tip:</strong> Create unique layouts by using this option in conjunction with the Show Entry option to show the entry content in one Content Block and show the comments in another Content Block.'
			),
			
			'featured-posts' => array(
				'type' => 'integer',
				'name' => 'featured-posts',
				'label' => 'Featured Posts',
				'default' => 1,
				'tooltip' => 'Featured posts are the posts where all of the content is displayed, unless limited by using the WordPress more tag.  After the featured posts are displayed, the content will automatically switch to showing automatically truncated excerpts.'
			),

			'paginate' => array(
				'type' => 'checkbox',
				'name' => 'paginate',
				'label' => 'Show Older/Newer Posts Navigation',
				'tooltip' => 'On archive layouts: Show links at the bottom of the loop for the visitor to view older or newer posts.',
				'default' => true
			),
			
			'show-single-post-navigation' => array(
				'type' => 'checkbox',
				'name' => 'show-single-post-navigation',
				'label' => 'Show Single Post Navigation',
				'default' => true,
				'tooltip' => 'By default, Padma will show links to the previous and next posts below an entry when viewing only one entry at a time.  You can choose to hide those links with this option.',
				'toggle' => array(

					'true' => array(
						'show' => '#input-show-single-post-navigation-enable-tax'
						),
					'false' => array(
						'hide' => array(
						'#input-show-single-post-navigation-enable-tax',
						'#input-show-single-post-navigation-tax'
						)
					)
				),
				
			),

			'show-single-post-navigation-enable-tax' => array(
				'type' => 'checkbox',
				'name' => 'show-single-post-navigation-enable-tax',
				'label' => 'Single Post Navigation: Same Tax?',
				'default' => false,
				'tooltip' => 'If you have Show Single Post Navigation turned on, by default WordPress/Padma will show links the next and previous post in chronological order. If you want the next/previous posts to only link to posts in the same taxonomy as the current post, enable this.',
				'toggle' => array(

					'true' => array(
						'show' => '#input-show-single-post-navigation-tax'
						),
					'false' => array(
						'hide' => '#input-show-single-post-navigation-tax'
						)
				),
			),

			'show-single-post-navigation-tax' => array(
				'type' => 'select',
				'name' => 'show-single-post-navigation-tax',
				'label' => 'Single Post Navigation Taxonomy',
				'default' => 'category',
				'tooltip' => 'If you have enabled Same Tax for Single Post Navigation, you can choose which taxonomy you want it to apply to.  By default, it will apply to the category taxonomy.',
				'options' => 'get_taxonomies()'
			),

			'show-edit-link' => array(
				'type' => 'checkbox',
				'name' => 'show-edit-link',
				'label' => 'Show Edit Link',
				'default' => true,
				'tooltip' => 'The edit link is a convenient link that will be shown next to the post title.  It will take you straight to the WordPress admin to edit the entry.'
			),
			
			'custom-excerpts-heading' => array(
				'name' => 'custom-excerpts-heading',
				'type' => 'heading',
				'label' => 'Custom Excerpts'
			),

			'custom-excerpts' => array(
				'type' => 'checkbox',
				'name' => 'custom-excerpts',
				'label' => 'Custom Excerpts Length',
				'default' => false,
				'tooltip' => 'By default the excerpts are set to 55 words. This can be far too many and a PHP hook will need to be set to change it. Instead you can set a custom amount here by specifically stating the number of words you want to show.',
				'toggle' => array(
					'true' => array(
						'show' => '#input-excerpts-length'
						),
					'false' => array(
						'hide' => '#input-excerpts-length'
						)
				),
			),
			
			'excerpts-length' => array(
				'type' => 'integer',
				'name' => 'excerpts-length',
				'label' => 'Excerpts Length',
				'default' => '55',
				'tooltip' => 'Control the length of the excerpt. By default they are set to display 55 words. This setting allows you to reduce or even lengthen that as you like and can be very handy to customise the look of your archive pages.'
			),

			'column-layout-heading' => array(
				'name' => 'column-layout-heading',
				'type' => 'heading',
				'label' => 'Column Layout'
			),

			'enable-column-layout' => array(
				'type' => 'checkbox',
				'name' => 'enable-column-layout',
				'label' => 'Enable Column Layout',
				'default' => false,
				'tooltip' => 'Enable this option to display articles side by side as columns.',
				'toggle'    => array(
					'true' => array(
						'show' => array(
							'#input-posts-per-row',
							'#input-post-gutter-width',
							'#input-post-bottom-margin'
						)
					),
					'false' => array(
						'hide' => array(
							'#input-posts-per-row',
							'#input-post-gutter-width',
							'#input-post-bottom-margin'
						)
					)
				)
			),

			'posts-per-row' => array(
				'type' => 'slider',
				'name' => 'posts-per-row',
				'label' => 'Posts Per Row',
				'slider-min' => 1,
				'slider-max' => 10,
				'slider-interval' => 1,
				'tooltip' => '',
				'default' => 2,
				'tooltip' => 'How many posts to show per row.',
				'callback' => ''
			),

			'post-gutter-width' => array(
				'type' => 'slider',
				'name' => 'post-gutter-width', 
				'label' => 'Gutter Width',
				'slider-min' => 0,
				'slider-max' => 100,
				'slider-interval' => 1,
				'default' => 15,
				'unit' => 'px',
				'tooltip' => 'The amount of horizontal spacing between posts.'
			)
		),

		'custom-fields' => array(),
		
		'meta' => array(
			'show-entry-meta-post-types' => array(
				'type' => 'multi-select',
				'name' => 'show-entry-meta-post-types',
				'label' => 'Entry Meta Display (Per Post Type)',
				'tooltip' => 'Choose which post types you wish for the entry meta to appear on.',
				'options' => 'get_post_types()',
				'default' => array('post')
			),
			
			'entry-meta-above' => array(
				'type' => 'textarea',
				'label' => 'Meta Above Content',
				'name' => 'entry-meta-above',
				'default' => 'Posted on %date% by %author% &bull; %comments%'
			),
			
			'entry-utility-below' => array(
				'type' => 'textarea',
				'label' => 'Meta Below Content',
				'name' => 'entry-utility-below',
				'default' => 'Filed Under: %categories%'
			),
			
			'date-format' => array(
				'type' => 'select',
				'name' => 'date-format',
				'label' => 'Date Format'
			),

			'time-format' => array(
				'type' => 'select',
				'name' => 'time-format',
				'label' => 'Time Format'
			),

			'comments-meta-heading' => array(
				'name' => 'comments-meta-heading',
				'type' => 'heading',
				'label' => 'Comments Meta'
			),

				'comment-format' => array(
					'type' => 'text',
					'label' => 'Comment Format &ndash; More Than 1 Comment',
					'name' => 'comment-format',
					'default' => '%num% Comments',
					'tooltip' => 'Controls what the %comments% and %comments_no_link% variables will output in the entry meta if there is <strong>more than 1 comment</strong> on the entry.'
				),
				
				'comment-format-1' => array(
					'type' => 'text',
					'label' => 'Comment Format &ndash; 1 Comment',
					'name' => 'comment-format-1',
					'default' => '%num% Comment',
					'tooltip' => 'Controls what the %comments% and %comments_no_link% variables will output in the entry meta if there is <strong>just 1 comment</strong> on the entry.'
				),
				
				'comment-format-0' => array(
					'type' => 'text',
					'label' => 'Comment Format &ndash; 0 Comments',
					'name' => 'comment-format-0',
					'default' => '%num% Comments',
					'tooltip' => 'Controls what the %comments% and %comments_no_link% variables will output in the entry meta if there are <strong>0 comments</strong> on the entry.'

				),
				
				'respond-format' => array(
					'type' => 'text',
					'label' => 'Respond Format',
					'name' => 'respond-format',
					'default' => 'Leave a comment!',
					'tooltip' => 'Determines the %respond% variable for the entry meta.'
				)
		),
		
		'comments' => array(
			'comments-area' => array(
				'name' => 'comments-area',
				'type' => 'heading',
				'label' => 'Comments Area Heading'
			),

				'comments-area-heading' => array(
					'type' => 'text',
					'label' => 'Comments Area Heading Format',
					'name' => 'comments-area-heading',
					'default' => '%responses% to <em>%title%</em>',
					'tooltip' => 'Heading above all comments.
					<br />
					<br /><strong>Available Variables:</strong>
					<ul>
						<li>%responses%</li>
						<li>%title%</li>
					</ul>'
				),
				
				'comments-area-heading-responses-number' => array(
					'type' => 'text',
					'label' => 'Responses Format &ndash; More Than 1 Comment',
					'name' => 'comments-area-heading-responses-number',
					'default' => '%num% Responses',
					'tooltip' => 'Controls what the %responses% variable will output in the comments area heading if there is <strong>more than 1 comment</strong> on the entry.'
				),
				
				'comments-area-heading-responses-number-1' => array(
					'type' => 'text',
					'label' => 'Responses Format &ndash; 1 Comment',
					'name' => 'comments-area-heading-responses-number-1',
					'default' => 'One Response',
					'tooltip' => 'Controls what the %responses% variable will output in the comments area heading if there is <strong>just 1 comment</strong> on the entry.'
				),

			'reply-area-heading' => array(
				'name' => 'reply-area-heading',
				'type' => 'heading',
				'label' => 'Reply Area'
			),

				'leave-reply' => array(
					'type' => 'text',
					'label' => 'Comment Form Title',
					'name' => 'leave-reply',
					'default' => 'Leave a reply',
					'tooltip' => 'This is the text that displays above the comment form.'
				),

				'leave-reply-to' => array(
					'type' => 'text',
					'label' => 'Reply Form Title',
					'name' => 'leave-reply-to',
					'default' => 'Leave a Reply to %s',
					'tooltip' => 'The title of comment form when replying to a comment.'
				),

				'cancel-reply-link' => array(
					'type' => 'text',
					'label' => 'Cancel Reply Text',
					'name' => 'cancel-reply-link',
					'default' => 'Cancel reply',
					'tooltip' => 'The text for the cancel reply button.'
				),

				'label-submit-text' => array(
					'type' => 'text',
					'label' => 'Submit Text',
					'name' => 'label-submit-text',
					'default' => 'Post Comment',
					'tooltip' => 'The submit button text.'
				)
		),

		'post-thumbnails' => array(

			'show-post-thumbnails' => array(
				'type' => 'checkbox',
				'name' => 'show-post-thumbnails',
				'label' => 'Show Featured Images',
				'default' => true,
				'toggle'    => array(
					'true' => array(
						'show' => array(
							'#input-featured-image-as-background'
						)
					),
					'false' => array(
						'hide' => array(
							'#input-featured-image-as-background'
						)
					)
				)
			),

			'featured-image-as-background' => array(
				'type' => 'checkbox',
				'name' => 'featured-image-as-background',
				'label' => 'Use featured image as background',
				'default' => false,
			),

			'post-thumbnails-link' => array(
				'type' => 'select',
				'name' => 'post-thumbnails-link',
				'label' => 'Link Featured Image',
				'default' => 'entry',
				'options' => array(
					'entry' => 'Entry (Default)',
					'media' => 'Attachment Page',
					'none' => 'None'
				),
				'tooltip' => 'By default, Padma will create a link around the featured image which links back to the post. Choose no link or to link to the image\'s attachment page instead.'
			),

			'post-thumbnail-position' => array(
				'type' => 'select',
				'name' => 'post-thumbnail-position',
				'label' => 'Image Position',
				'default' => 'left',
				'options' => array(
					'left' => 'Left of Title',
					'right' => 'Right of Title',
					'left-content' => 'Left of Content',
					'right-content' => 'Right of Content',
					'above-title' => 'Above Title',
					'above-content' => 'Above Content',
					'below-content' => 'Below Content'
				)
			),

			'use-entry-thumbnail-position' => array(
				'type' => 'checkbox',
				'name' => 'use-entry-thumbnail-position',
				'label' => 'Use Per-Entry Featured Image Positions',
				'default' => true,
				'tooltip' => 'In the WordPress write panel, there is a Padma meta box that allows you to change the featured image position for the entry being edited.<br /><br />By default, the block will use that value, but you may uncheck this so that the blocks thumbnail position is always used.'
			),

			'thumbnail-sizing-heading' => array(
				'name' => 'thumbnail-sizing-heading',
				'type' => 'heading',
				'label' => 'Featured Image Sizing'
			),

				'post-thumbnail-size' => array(
					'type' => 'slider',
					'name' => 'post-thumbnail-size',
					'label' => 'Featured Image Size (Left/Right)',
					'default' => 125,
					'slider-min' => 20,
					'slider-max' => 400,
					'slider-interval' => 1,
					'tooltip' => 'Adjust the size of the featured image sizes.  This is used for both the width and height of the images.',
					'unit' => 'px'
				),

				'post-thumbnail-height-ratio' => array(
					'type' => 'slider',
					'name' => 'post-thumbnail-height-ratio',
					'label' => 'Featured Image Height Ratio (Above Title/Content)',
					'default' => 35,
					'slider-min' => 10,
					'slider-max' => 200,
					'slider-interval' => 5,
					'tooltip' => 'Adjust the height of feature images when set to the above title or above content positions.  This value controls what percent the height of the image will be in regards to the width of the block.<br /><br />Example: If the block width is 500 pixels and the ratio is 50% then the feature image size will be 500px by 250px.',
					'unit' => '%'
				),

				'crop-post-thumbnails' => array(
					'type' => 'checkbox',
					'name' => 'crop-post-thumbnails',
					'label' => 'Crop Featured Images',
					'default' => true
				)
		)
		
	);
	

	function modify_arguments($args = false) {
		
		global $pluginbuddy_loopbuddy;
		
		if ( class_exists('pluginbuddy_loopbuddy') && isset($pluginbuddy_loopbuddy) ) {
			
			//Remove the old tabs
			unset($this->tabs['mode']);
			unset($this->tabs['meta']);
			unset($this->tabs['display']);
			unset($this->tabs['query-filters']);
			unset($this->tabs['post-thumbnails']);

			unset($this->inputs['mode']);
			unset($this->inputs['meta']);
			unset($this->inputs['display']);
			unset($this->inputs['query-filters']);
			unset($this->inputs['post-thumbnails']);
			
			//Add in new tabs
			$this->tabs['loopbuddy'] = 'LoopBuddy';
			
			$this->inputs['loopbuddy'] = array(
				'loopbuddy-query' => array(
					'type' => 'select',
					'name' => 'loopbuddy-query',
					'label' => 'LoopBuddy Query',
					'options' => 'get_loopbuddy_queries()',
					'tooltip' => 'Select a LoopBuddy query to the right.  Queries determine what content (posts, pages, etc) will be displayed.  You can modify/add queries in the WordPress admin under LoopBuddy.',
					'default' => ''
				),
				
				'loopbuddy-layout' => array(
					'type' => 'select',
					'name' => 'loopbuddy-layout',
					'label' => 'LoopBuddy Layout',
					'options' => 'get_loopbuddy_layouts()',
					'tooltip' => 'Select a LoopBuddy layout to the right.  Layouts determine how the query will be displayed.  This includes the order of the content in relation to the title, meta, and so on.  You can modify/add layouts in the WordPress admin under LoopBuddy.',
					'default' => ''
				)
			);
			
			$this->tab_notices = array(
				'loopbuddy' => '<strong>Even though we have the options to choose a LoopBuddy layout and query here, we recommend you configure LoopBuddy using its <a href="' . admin_url('admin.php?page=pluginbuddy_loopbuddy'). '" target="_blank">options panel</a>.</strong><br /><br />The options below are more useful if you\'re using two Content Blocks on one layout and wish to configure them separately.  <strong>Note:</strong> You MUST have a query selected in order to also select a LoopBuddy layout.'
			);
			
			return;
			
		}

		if ( class_exists('SWP_Query') ) {

			$this->inputs['display']['swp-heading'] = array(
					'name'  => 'swp-heading',
					'type'  => 'heading',
					'label' => 'SearchWP'
			);

			$this->inputs['display']['swp-engine'] = array(
				'type'    => 'select',
				'name'    => 'swp-engine',
				'label'   => 'SearchWP Engine',
				'options' => 'get_swp_engines()',
				'tooltip' => 'If you wish to display the results of a supplemented SearchWP engine, please select the engine here.',
				'default' => ''
			);

		}

		$this->inputs['meta']['date-format']['options'] = array(
			'wordpress-default' => 'WordPress Default',
			'F j, Y' => date('F j, Y'),
			'm/d/y' => date('m/d/y'),
			'd/m/y' => date('d/m/y'),
			'M j' => date('M j'),
			'M j, Y' => date('M j, Y'),
			'F j' => date('F j'),
			'F jS' => date('F jS'),
			'F jS, Y' => date('F jS, Y')
		);

		$this->inputs['meta']['time-format']['options'] = array(
			'wordpress-default' => 'WordPress Default',
			'g:i A' => date('g:i A'),
			'g:i A T' => date('g:i A T'),
			'g:i:s A' => date('g:i:s A'),
			'G:i' => date('G:i'),
			'G:i T' => date('G:i T')
		);


		/**
		 *
		 * Custom Fields support
		 *
		 */
		
		$custom_fields = PadmaQuery::get_meta($this->block['settings']['post-type']);

		if(count($custom_fields)==0){

			$this->tab_notices['custom-fields'] = 'The selected post type does not have custom fields.';

		}else{

			$inputs = array();

			foreach ($custom_fields as $post_type => $fields) {
				
				$heading = 'custom-fields-'.$post_type.'-heading';
				
				$inputs[$heading] = array(
					'name' => $heading,
					'type' => 'heading',
					'label' => 'Custom Fields for: "' . $post_type . '".'
				);
				
				foreach ($fields as $field_name => $posts_total) {

					// Custom field name
					$name = 'custom-field-show-' . $post_type . '-' . $field_name;

					// Custom field position
					$label = 'custom-field-label-' . $post_type . '-' . $field_name;					

					// Custom field position
					$position = 'custom-field-position-' . $post_type . '-' . $field_name;

					// Custom field input
					$inputs[$name] = array(
						'type' => 'checkbox',
						'name' => $name,
						'label' => 'Show "' . $field_name .'"',
						'tooltip' => 'Check this to allow show ' . $field_name,
						'default' => false,
						'toggle'    => array(
							'false' => array(
								'hide' => array(
									'#input-' . $position,
									'#input-' . $label
								)
							),
							'true' => array(
								'show' => array(
									'#input-' . $position,
									'#input-' . $label
								)
							)
						)
					);					
					
					// Custom field label input
					$inputs[$label] = array(
						'type' => 'text',
						'name' => $label,
						'label' => '"'.$field_name .'" label',
						'default' => '',
					);
					
					// Custom field position input
					$inputs[$position] = array(
						'type' => 'select',
						'name' => $position,
						'label' => '"'.$field_name .'" position',
						'default' => 'below',
						'options' => array(
							'above' => 'Above',
							'after-title' => 'After Title',
							'below' => 'Below'
						)
					);

				}
			}

			$this->inputs['custom-fields'] = $inputs;

		}
		
	}
	
	
	function get_pages() {
		
		$page_options = array('&ndash; Do Not Fetch &ndash;');
		
		$page_select_query = get_pages();
		
		foreach ($page_select_query as $page)
			$page_options[$page->ID] = $page->post_title;
		
		return $page_options;
		
	}

	
	function get_categories() {

		return PadmaQuery::get_categories($this->block['settings']['post-type']);

	}

	function get_tags() {
		
		$tag_options = array();
		$tags_select_query = get_terms('post_tag');
		foreach ($tags_select_query as $tag)
			$tag_options[$tag->term_id] = $tag->name;
		$tag_options = (count($tag_options) == 0) ? array('text'	 => 'No tags available') : $tag_options;
		return $tag_options;
	
	}
	
	
	function get_authors() {
		
		$author_options = array();
		
		$authors = get_users(array(
			'orderby' => 'post_count',
			'order' => 'desc',
			'who' => 'authors'
		));
		
		foreach ( $authors as $author )
			$author_options[$author->ID] = $author->display_name;
			
		return $author_options;
		
	}
	
	
	function get_post_types() {
		
		$post_type_options = array();

		$post_types = get_post_types(false, 'objects'); 
			
		foreach($post_types as $post_type_id => $post_type){
			
			//Make sure the post type is not an excluded post type.
			if(in_array($post_type_id, array('revision', 'nav_menu_item'))) 
				continue;
			
			$post_type_options[$post_type_id] = $post_type->labels->name;
		
		}
		
		return $post_type_options;
		
	}

	function get_taxonomies() {

		$taxonomy_options = array('&ndash; Default: Category &ndash;');

		$taxonomy_select_query=get_taxonomies(false, 'objects', 'or');

		
		foreach ($taxonomy_select_query as $taxonomy)
			$taxonomy_options[$taxonomy->name] = $taxonomy->label;
		
		
		return $taxonomy_options;
		
	}

	function get_post_status() {
		
		return get_post_stati();
		
	}


	function get_swp_engines() {

		$options = array('&ndash; Select an Engine &ndash;');

		if ( !function_exists('SWP') ) {
			return $options;
		}

		$searcbtp = SWP();

		if ( !is_array( $searcbtp->settings['engines']) ) {
			return $options;
		}

		foreach ( $searcbtp->settings['engines'] as $engine => $engine_settings ) {

			if ( empty( $engine_settings['searcbtp_engine_label'] ) ) {
				continue;
			}

			$options[$engine] = $engine_settings['searcbtp_engine_label'];

		}

		return $options;

	}


	function get_loopbuddy_queries() {
		
		$loopbuddy_options = get_option('pluginbuddy_loopbuddy');
		
		$queries = array(
			'' => '&ndash; Use Default Query &ndash;'
		);
				
		foreach ( $loopbuddy_options['queries'] as $query_id => $query ) {
						
			$queries[$query_id] = $query['title'];
			
		}
		
		return $queries;
		
	}

	
	function get_loopbuddy_layouts() {
		
		$loopbuddy_options = get_option('pluginbuddy_loopbuddy');
		
		$layouts = array(
			'' => '&ndash; Use Default Layout &ndash;'
		);
				
		foreach ( $loopbuddy_options['layouts'] as $layout_id => $layout ) {
			
			$layouts[$layout_id] = $layout['title'];
			
		}
		
		return $layouts;
		
	}
}

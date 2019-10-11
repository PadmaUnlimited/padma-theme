<?php

class PadmaSlideDeckBlock extends PadmaBlockAPI {


	public $id;
	public $name;
	public $options_class;
	public $description;
	public $categories;


	function __construct(){

		$this->id = 'slidedeck';	
		$this->name = __('SlideDeck 2','padma');
		$this->options_class = 'PadmaSlideDeckBlockOptions';
		$this->description = __('Conveniently add SlideDecks anywhere on any layout.','padma'); 
		/* This will be shown in the block type selector */
		$this->categories 	= array('core','content', 'media');		

	}

	/** 
	 * Anything in here will be displayed when the block is being displayed.
	 **/
	function content($block) {

		global $SlideDeckPlugin;

		/* Make sure SlideDeck is activated and working */
			if ( !is_object($SlideDeckPlugin) ) {

				echo '<div class="alert alert-red"><p>' . __('SlideDeck must be installed and activated in order for the SlideDeck block to work properly.','padma') . '</p></div>';
				return;

			}

		/* Get the chosen SlideDeck ID */
			$slidedeck_id = parent::get_setting($block, 'slidedeck-id', null);

		/* Make sure that there's a selected SlideDeck */
			if ( empty($slidedeck_id) ) {

				echo '<div class="alert alert-red"><p>' . __('Please choose a SlideDeck to display.','padma') . '</p></div>';
				return;

			}

			$slidedeck_query = $SlideDeckPlugin->SlideDeck->get($slidedeck_id);

			if ( empty($slidedeck_query) ) {

				echo '<div class="alert alert-red"><p>' . __('The SlideDeck you previously chose must\'ve been deleted or moved elsewhere.  Please select another SlideDeck to display.') . '</p></div>';
				return;

			}

		/* Setup arguments */
			$args = array(
				'id' => $slidedeck_id,
				'width' => null,
				'height' => null
			);

			if ( parent::get_setting($block, 'use-block-size', true) ) {

				$args['width'] = PadmaBlocksData::get_block_width($block);
				$args['height'] = PadmaBlocksData::get_block_height($block);
				$args['proportional'] = false;

			}


			if ( PadmaRoute::is_visual_editor_iframe() )
				$args['iframe'] = true;

			if ( !PadmaRoute::is_visual_editor_iframe() && PadmaResponsiveGrid::is_active() )
				$args['ress'] = true;

			/* Work around for iframe dimensions */
				$GLOBALS['slidedeck-width'] = $args['width'];
				$GLOBALS['slidedeck-height'] = $args['height'];

				add_filter('slidedeck_dimensions', array(__CLASS__, 'modify_slidedeck_iframe_size_for_ajax'), 10, 5);
			/* End work around for iframe dimensions */

		/* Show the SlideDeck! */
			echo $SlideDeckPlugin->shortcode($args);

		/* Remove any filters if necessary */
			remove_filter('slidedeck_dimensions', array(__CLASS__, 'modify_slidedeck_iframe_size_for_ajax'));

			if ( isset($GLOBALS['slidedeck-width']) )
				unset($GLOBALS['slidedeck-width']);

			if ( isset($GLOBALS['slidedeck-height']) )
				unset($GLOBALS['slidedeck-height']);
		/* End removing filters */

	}


		public static function modify_slidedeck_iframe_size_for_ajax(&$width, &$height, &$outer_width, &$outer_height, &$slidedeck) {

			$width 			= $GLOBALS['slidedeck-width'];
			$height 		= $GLOBALS['slidedeck-height'];

			$outer_width 	= $GLOBALS['slidedeck-width'];
			$outer_height 	= $GLOBALS['slidedeck-height'];

			return true;

		}


}


class PadmaSlideDeckBlockOptions extends PadmaBlockOptionsAPI {


	public $tabs;
	public $inputs;


	function __construct(){

		$this->tabs = array(
			'settings-tab' => __('Settings','padma')
		);

		$this->inputs = array(
			'settings-tab' => array(
				'slidedeck-dashboard-link' => array(
					'type' => 'notice',
					'name' => 'slidedeck-dashboard-link',
					'notice' => ''
				),
				'slidedeck-id' => array(
					'type' => 'select',
					'name' => 'slidedeck-id', //This will be the setting you retrieve from the database.
					'label' => __('Choose a SlideDeck to Display','padma'),
					'default' => '',
					'options' => 'get_slidedecks()',
					'tooltip' => __('Select the SlideDeck you wish to display','padma'),
				),

				'use-block-size' => array(
					'type' => 'checkbox',
					'name' => 'use-block-size',
					'label' => __('Use Block Size for SlideDeck','padma'),
					'default' => true,
					'tooltip' => __('Choose whether or not you\'d like to use the block\'s size to dictate the SlideDeck\'s size.  If you choose not to, it will use the size defined in the SlideDeck\'s settings.','padma')
				)
			)
		);
	}


	function get_slidedecks() {

		global $SlideDeckPlugin;

		$slidedecks = $SlideDeckPlugin->SlideDeck->get(null, 'post_title', 'ASC', 'publish');

		$options = array(
			'' => __('&ndash; Select a SlideDeck &ndash;','padma')
		);

		foreach ( $slidedecks as $slidedeck )
			$options[$slidedeck['id']] = $slidedeck['title'];

		return $options;

	}


	function modify_arguments($args = false) {

		/* Since we can't call functions when declaring a property, we must put in the admin links here that way we can use admin_url() */
			$this->inputs['settings-tab']['slidedeck-dashboard-link']['notice'] = '
			    <strong>' . __('SlideDeck Quick Links:','padma') . '</strong>&nbsp;
				<a href="' . admin_url('admin.php?page=' . SLIDEDECK2_BASENAME) . '" target="_blank">' . __('Add/Manage SlideDecks','padma') . '</a> | 
				<a href="' . admin_url('admin.php?page=' . SLIDEDECK2_BASENAME . '/lenses') . '" target="_blank">' . __('Lenses','padma') . '</a> | 
				<a href="' . admin_url('admin.php?page=' . SLIDEDECK2_BASENAME . '/options') . '" target="_blank">' . __('Advanced Options','padma') . '</a>
			';

	}


}
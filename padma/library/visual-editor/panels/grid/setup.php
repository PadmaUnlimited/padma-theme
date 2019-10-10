<?php
class GridSetupPanel extends PadmaVisualEditorPanelAPI {
	
	public $id = 'setup';
	public $name;
	public $mode = 'grid';	
	public $tabs;	
	public $tab_notices;	
	public $inputs;
	
	function __construct(){

		$this->tabs = array(
			'grid' => 'Grid',
			'responsive-grid' => __('Responsive Grid','padma')
		);
		
		$this->name = 'Setup';
		
		$this->tab_notices = array(
			
			'grid' => __('<strong>Note:</strong> the content in the grid above will not reflect how your site actually looks.  The content inside the blocks is to give you a general reference while you wireframe and build the layout to your site.<br /><br />The settings below are <strong>global</strong> and are not customized on a per-layout basis.','padma'),
			
			'responsive-grid' => __('The Padma Responsive Grid allows the powerful grid in Padma Base to be custom-tailored depending on the device that the visitor is viewing the site from.  Please note: some sites may benefit from having the responsive grid enabled while other will not.  As the designer of the website, it is up to you to decide.  The responsive grid can be enabled or disabled at any time.','padma')
		);

		$this->inputs = array(
			'grid' => array(
				'columns' => array(
					'type' => 'slider',
					'name' => 'columns',
					'label' => __('Default Column Count','padma'), /* Column count is default only because you can't change it on the fly */
					'default' => 24,
					'tooltip' => __('The column count is the number of columns in the grid.  This is represented by the grey regions on the grid.<br /><br /><strong>This will NOT affect wrappers that are already created.  It only affects wrappers that are created after this setting is changed.</strong>','padma'),
					'slider-min' => 6,
					'slider-max' => 24,
					'slider-interval' => 1,
					'callback' => 'Padma.defaultGridColumnCount = value.toString();updateGridWidthInput($(input).parents(".sub-tabs-content"));'
				),

				'column-width' => array(
					'type' => 'slider',
					'name' => 'column-width',
					'label' => __('Global Column Width','padma'),
					'default' => 26,
					'tooltip' => __('The column width is the amount of space inside of each column.  This is represented by the grey regions on the grid.','padma'),
					'unit' => 'px',
					'slider-min' => 10,
					'slider-max' => 120,
					'slider-interval' => 1,
					'callback' => 'Padma.globalGridColumnWidth = value.toString();$i("div.wrapper:not(.independent-grid)").each(function() { $(this).padmaGrid("updateGridCSS"); });updateGridWidthInput($(input).parents(".sub-tabs-content"));'
				),
				
				'gutter-width' => array(
					'type' => 'slider',
					'name' => 'gutter-width',
					'label' => __('Global Gutter Width','padma'),
					'default' => 22,
					'tooltip' => __('The gutter width is the amount of space between each column.  This is the space between each of the grey regions on the grid.','padma'),
					'unit' => 'px',
					'slider-min' => 0,
					'slider-max' => 60,
					'slider-interval' => 1,
					'callback' => 'Padma.globalGridGutterWidth = value.toString();$i("div.wrapper:not(.independent-grid)").each(function() { $(this).padmaGrid("updateGridCSS"); });updateGridWidthInput($(input).parents(".sub-tabs-content"));'
				),
				
				'grid-width' => array(
					'type' => 'integer',
					'unit' => 'px',
					'default' => 1130,
					'name' => 'grid-width',
					'label' => __('Global Grid Width','padma'),
					'readonly' => true
				)
			),
			
			'responsive-grid' => array(
				'enable-responsive-grid' => array(
					'type' => 'checkbox',
					'name' => 'enable-responsive-grid',
					'label' => __('Enable Responsive Grid','padma'),
					'default' => true,
					'tooltip' => __('If Padma\'s responsive grid is enabled, the grid will automatically adjust depending on the visitor\'s device (computer, iPhone, iPad, etc).  Enabling the responsive grid can be extremely beneficial for some websites, but may not be wortbthile for other websites.  If the responsive grid is enabled, the user will always have the option to disable the responsive grid via a link in the footer block.<br /><br /><strong>Please Note:</strong> with the responsive grid enabled, the exact pixel widths of blocks may differ very slightly from when it is <em>disabled</em>.','padma')
				),
				
				'responsive-video-resizing' => array(
					'type' => 'checkbox',
					'name' => 'responsive-video-resizing',
					'label' => __('Responsive Video Resizing','padma'),
					'default' => true,
					'tooltip' => __('If the Responsive Grid is enabled and the user visits the site when there are YouTube, Vimeo, or any other videos, then the videos will not resize properly unless then is checked.','padma')
				)
			)
		);

	}
}
padma_register_visual_editor_panel('GridSetupPanel');
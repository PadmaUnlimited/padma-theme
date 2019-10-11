<?php

padma_register_admin_meta_box('PadmaMetaBoxTemplate');
class PadmaMetaBoxTemplate extends PadmaAdminMetaBoxAPI {

	protected $id;	
	protected $name;				
	protected $context;			
	protected $inputs;

	public function __construct(){

		$this->id = 'template';
		$this->name = __( 'Shared Layout', 'padma');
		$this->context = 'side';
		$this->inputs = array(
			'template' => array(
				'id' => 'template',
				'type' => 'select',
				'options' => array(),
				'description' => __('Assign a shared layout to this entry.  Shared layouts can be added and modified in the Padma Visual Editor.','padma'),
				'blank-option' => __('&ndash; Do Not Use Shared Layout &ndash;','padma')
			)
		);

	}

	protected function modify_arguments($post = false) {

		$this->inputs['template']['options'] = PadmaLayout::get_templates();

		$post_type = get_post_type_object( $post->post_type );

		$this->inputs['template']['description'] = str_replace('entry', strtolower($post_type->labels->singular_name), $this->inputs['template']['description']);

	}

}

padma_register_admin_meta_box('PadmaMetaBoxTitleControl');
class PadmaMetaBoxTitleControl extends PadmaAdminMetaBoxAPI {

	protected $id;
	protected $name;
	protected $context;
	protected $inputs;

	public function __construct(){

		$this->id = 'alternate-title';	
		$this->name = 'Title Control';				
		$this->context = 'side';			
		$this->inputs = array(
			'hide-title' => array(
				'id' => 'hide-title',
				'name' => __('Hide Title','padma'),
				'type' => 'select',
				'blank-option' => __('&ndash; Do Not Hide Title &ndash;','padma'),
				'options' => array(
					'singular' => __('Hide on Single View','padma'),
					'list' => __('Hide in Index and Archives','padma'),
					'both' => __('Hide on Single View, Index, and Archives','padma')
				),
				'description' => __('Choose whether or not you would like to hide the title for this entry.  This can be useful if you have advanced formatting in this entry.','padma'),
			),

			'alternate-title' => array(
				'id' => 'alternate-title',
				'name' => __('Alternate Title','padma'),
				'type' => 'text',
				'description' => __('Using the alternate page title, you can override the title that\'s displayed in the Content Block of the page.  Doing this, you can have a shorter page title in the navigation menu and <code>&lt;title&gt;</code>, but have a longer and more descriptive title in the actual page content.','padma')
			)
		);
	}


}


padma_register_admin_meta_box('PadmaMetaBoxDisplay');
class PadmaMetaBoxDisplay extends PadmaAdminMetaBoxAPI {

	protected $id;	
	protected $name;
	protected $inputs;

	public function __construct(){

		$this->id = 'display';
		$this->name = __('Display','padma');
		$this->inputs = array(
			'css-class' => array(
				'id' => 'css-class',
				'name' => __('Custom CSS Class(es)','padma'),
				'type' => 'text',
				'description' => __('If you are familiar with <a href="http://www.w3schools.com/css/" target="_blank">CSS</a> and would like to style this entry by targeting a certain CSS class (or classes), then you may enter them in here.  The class will be added to the <strong>entry container\'s class</strong> along with the <strong>body class</strong> if only this entry is being viewed (e.g. single post or page view). Classes can be separated with spaces and/or commas.','padma')
			)
		);
	}

}


padma_register_admin_meta_box('PadmaMetaBoxPostThumbnail');
class PadmaMetaBoxPostThumbnail extends PadmaAdminMetaBoxAPI {

	protected $id;
	protected $name;
	protected $context;
	protected $priority;
	protected $inputs;

	public function __construct(){

		$this->id = 'post-thumbnail';		
		$this->name = __('Featured Image Position','padma');				
		$this->context = 'side';
		$this->priority = 'low';				
		$this->inputs = array(
			'position' => array(
				'id' => 'position',
				'name' => __('Featured Image Position','padma'),
				'type' => 'radio',
				'options' => array(
					'' => __('Use Block Default','padma'),
					'left' => __('Left of Title','padma'),
					'right' => __('Right of Title','padma'),
					'left-content' => __('Left of Content','padma'),
					'right-content' => __('Right of Content','padma'),
					'above-title' => __('Above Title','padma'),
					'above-content' => __('Above Content','padma'),
					'below-content' => __('Below Content','padma')
				),
				'description' => __('Set the position of the featured image for this entry.','padma'),
				'default' => '',
				'group' => 'post-thumbnail'
			),
		);
	}

}


if ( !PadmaSEO::is_disabled() )
	padma_register_admin_meta_box('PadmaMetaBoxSEO');

class PadmaMetaBoxSEO extends PadmaAdminMetaBoxAPI {

	protected $id;
	protected $name;
	protected $post_type_supports_id;
	protected $priority;
	protected $inputs;

	public function __construct(){

		$this->id = 'seo';		
		$this->name = 'Search Engine Optimization (SEO)';			
		$this->post_type_supports_id = 'padma-seo';		
		$this->priority = 'high';				

		$this->inputs = array(

			'seo-preview' => array(
				'id' => 'seo-preview',
				'type' => 'seo-preview'
			),

			'title' => array(
				'id' => 'title',
				'group' => 'seo',
				'name' => __('Title','padma'),
				'type' => 'text',
				'description' => __('Custom <code>&lt;title&gt;</code> tag','padma')
			),

			'description' => array(
				'id' => 'description',
				'group' => 'seo',
				'name' => __('Description','padma'),
				'type' => 'textarea',
				'description' => __('Custom <code>&lt;meta&gt;</code> description','padma')
			),

			'noindex' => array(
				'id' => 'noindex',
				'group' => 'seo',
				'name' => __('<code>noindex</code> this entry.','padma'),
				'type' => 'checkbox',
				'description' => __('Index/NoIndex tells the engines whether the entry should be crawled and kept in the engines\' index for retrieval. If you check this box to opt for <code>noindex</code>, the entry will be excluded from the engines.  <strong>Note:</strong> if you\'re not sure what this does, do not check this box.','padma')
			),

			'nofollow' => array(
				'id' => 'nofollow',
				'group' => 'seo',
				'name' => __('<code>nofollow</code> links in this entry.','padma'),
				'type' => 'checkbox',
				'description' => __('Follow/NoFollow tells the engines whether links on the entry should be crawled. If you check this box to employ "nofollow," the engines will disregard the links on the entry both for discovery and ranking purposes.  <strong>Note:</strong> if you\'re not sure what this does, do not check this box.','padma')
			),

			'noarchive' => array(
				'id' => 'noarchive',
				'group' => 'seo',
				'name' => __('<code>noarchive</code> links in this entry.','padma'),
				'type' => 'checkbox',
				'description' => __('Noarchive is used to restrict search engines from saving a cached copy of the entry. By default, the engines will maintain visible copies of all pages they indexed, accessible to searchers through the "cached" link in the search results.  Check this box to restrict search engines from storing cached copies of this entry.','padma')
			),

			'nosnippet' => array(
				'id' => 'nosnippet',
				'group' => 'seo',
				'name' => __('<code>nosnippet</code> links in this entry.','padma'),
				'type' => 'checkbox',
				'description' => __('Nosnippet informs the engines that they should refrain from displaying a descriptive block of text next to the entry\'s title and URL in the search results.','padma')
			),

			'noodp' => array(
				'id' => 'noodp',
				'group' => 'seo',
				'name' => __('<code>noodp</code> links in this entry.','padma'),
				'type' => 'checkbox',
				'description' => __('NoODP is a specialized tag telling the engines not to grab a descriptive snippet about a page from the Open Directory Project (DMOZ) for display in the search results.','padma')
			),

			'noydir' => array(
				'id' => 'noydir',
				'group' => 'seo',
				'name' => __('<code>noydir</code> links in this entry.','padma'),
				'type' => 'checkbox',
				'description' => __('NoYDir, like NoODP, is specific to Yahoo!, informing that engine not to use the Yahoo! Directory description of a page/site in the search results.','padma')
			),

			'redirect-301' => array(
				'id' => 'redirect-301',
				'group' => 'seo',
				'name' => __('301 Permanent Redirect','padma'),
				'type' => 'text',
				'description' => __('The 301 Permanent Redirect can be used to forward an old post or page to a new or different location.  If you ever move a page or change a page\'s permalink, use this to forward your visitors to the new location.<br /><br /><em>Want more information?  Read more about <a href="http://support.google.com/webmasters/bin/answer.py?hl=en&answer=93633" target="_blank">301 Redirects</a>.</em>','padma')
			),

		);
	}


	protected function input_seo_preview() {

		global $post;

		$date = get_the_time('M j, Y') ? get_the_time('M j, Y') : mktime('M j, Y');
		$date_text = ( $post->post_type == 'post' ) ? $date . ' ... ' : null;

		echo '<h4 id="seo-preview-title">Search Engine Result Preview</h4>';

			echo '<div id="seo-preview">';

				echo '<h4 title="Click To Edit">' . get_bloginfo('name') . '</h4>';
				echo '<p id="seo-preview-description" title="Click To Edit">' . $date_text . '<span id="text"></span></p>';

				echo '<p id="seo-preview-bottom"><span id="seo-preview-url">' . str_replace('http://', '', home_url()) . '</span> - <span>Cached</span> - <span>Similar</span></p>';

			echo '</div><!-- #seo-preview -->';

		echo '<small id="seo-preview-disclaimer">' . __('Remember, this is only a predicted search engine result preview.  There is no guarantee that it will look exactly this way.  However, it will look similar.','padma') . '</small>';

	}


	protected function input_text_with_counter($input) {

		echo '
			<tr class="label">
				<th valign="top" scope="row">
					<label for="' . $input['attr-id'] . '">' . $input['name'] . '</label>
				</th>
			</tr>

			<tr>
				<td>
					<input type="text" value="' . esc_attr($input['value']) . '" id="' . $input['attr-id'] . '" name="' . $input['attr-name'] . '" />
				</td>
			</tr>

			<tr class="character-counter">
				<td>
					<span>130</span><div class="character-counter-box"><div class="character-counter-inside"></div></div>
				</td>
			</tr>
		';

	}


	protected function modify_arguments($post = false) {

		//Do not use this box if the page being edited is the front page since they can edit the setting in the configuration.
		if ( get_option('page_on_front') == padma_get('post') && get_option('show_on_front') == 'page' ) {

			$this->info = sprintf( __('<strong>Configure the SEO settings for this page (Front Page) in the Padma Search Engine Optimization settings tab in <a href="%" target="_blank">Padma &raquo; Configuration</a>.</strong>','padma'), admin_url('admin.php?page=padma-options#tab-seo') );

			$this->inputs = array();

			return;

		}

		//Setup the defaults for the title and checkboxes
		$current_screen = get_current_screen();
		$seo_templates_query = PadmaOption::get('seo-templates', 'general', PadmaSEO::output_layouts_and_defaults());
		$seo_templates = padma_get('single-' . $current_screen->id, $seo_templates_query, array());

		$title_template = str_replace(array('%sitename%', '%SITENAME%'), get_bloginfo('name'), padma_get('title', $seo_templates));

		echo '<input type="hidden" id="title-seo-template" value="' . $title_template . '" />';

		$this->inputs['noindex']['default'] = padma_get('noindex', $seo_templates);
		$this->inputs['nofollow']['default'] = padma_get('nofollow', $seo_templates);
		$this->inputs['noarchive']['default'] = padma_get('noarchive', $seo_templates);
		$this->inputs['nosnippet']['default'] = padma_get('nosnippet', $seo_templates);
		$this->inputs['noodp']['default'] = padma_get('noodp', $seo_templates);
		$this->inputs['noydir']['default'] = padma_get('noydir', $seo_templates);


	}

}
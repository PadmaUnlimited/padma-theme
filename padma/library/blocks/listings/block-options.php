<?php

namespace Padma;
class PadmaListingsBlockOptions extends PadmaBlockOptionsAPI {

	public $tabs;
	public $inputs;


	function __construct($block_type_object){

		parent::__construct($block_type_object);

		$this->tabs = array(
			'listing-type' => __('Select Listing Type','padma'),
			'posts-pages-filters' => __('Posts &amp; Pages Filters','padma'),
			'taxonomy-options' => __('Taxonomy Options','padma')
		);

		$this->inputs = array(
			'listing-type' => array(

				'listing-type' => array(
					'type' => 'select',
					'name' => 'listing-type',
					'label' => __('List?','padma'),
					'tooltip' => __('Select a type of list output and then configure it with the options on the left.','padma'),
					'options' => array(
						'taxonomy' => __('Taxonomy (category, tag etc)','padma'),
						'content' => __('Posts or Pages (custom posts)','padma'),
						'authors' => __('Authors','padma')
					),
					'default' => 'taxonomy',
					'toggle'    => array(
						'taxonomy' => array(
							'show' => array(
								'#sub-tab-taxonomy-options'
							),
							'hide' => array(
								'#sub-tab-posts-pages-filters'
							)
						),
						'content' => array(
							'hide' => array(
								'#sub-tab-taxonomy-options'
							),
							'show' => array(
								'#sub-tab-posts-pages-filters'
							)
						)
					)
				)

			),

			'taxonomy-options'	=> array(

				'terms-select-taxonomy-heading' => array(
					'name' => 'terms-select-taxonomy-heading',
					'type' => 'heading',
					'label' => __('Select Taxonomy','padma')
				),

				'select-taxonomy' => array(
					'label' => __('Select Taxonomy to display','padma'),
					'type' => 'select',
					'name' => 'select-taxonomy',
					'options' => 'get_taxonomies()',
					'default' => 'category',
				),

				'terms-options-sorting-heading' => array(
					'name' => 'terms-options-sorting-heading',
					'type' => 'heading',
					'label' => __('Sort Taxonomy','padma')
				),

				'terms-orderby' => array(
					'type' => 'select',
					'name' => 'terms-orderby',
					'label' => __('Order By?','padma'),
					'tooltip' => __('Sort term alphabetically, by unique Term ID, or by the count of items in that Term','padma'),
					'options' => array(
						'none' => __('None','padma'),
						'ID' => 'ID',
						'name' => __('Name','padma'),
						'slug' => __('Slug','padma'),
						'count' => __('Count','padma'),
						//'term_group' => 'Term Group'
					),
					'default' => 'name'
				),

				'terms-order' => array(
					'type' => 'select',
					'name' => 'terms-order',
					'label' => __('Order?','padma'),
					'tooltip' => __('Sort order for term (either ascending or descending).','padma'),
					'options' => array(
						'DESC' => __('Descending','padma'),
						'ASC' => __('Ascending','padma')
					),
					'default' => 'ASC'
				),

				'terms-options-filter-heading' => array(
					'name' => 'terms-options-filter-heading',
					'type' => 'heading',
					'label' => __('Filter Taxonomy','padma')
				),

				'terms-number' => array(
					'type' => 'slider',
					'slider-min' => 0,
					'slider-max' => 30,
					'slider-interval' => 1,
					'name' => 'terms-number',
					'label' => __('Number of terms','padma'),
					'default' => '10',
					'tooltip' => __('Sets the number of terms to display. Default 0 for no limit.','padma')
				),

				'terms-child-of' => array(
					'type' => 'select',
					'name' => 'terms-child-of',
					'label' => __('Child Of','padma'),
					'options' => 'get_listing_terms()',
					'default' => '',
					'tooltip' => __('Only display terms that are children of what you specify here.','padma')
				),

				'terms-exclude' => array(
					'type' => 'multi-select',
					'name' => 'terms-exclude',
					'label' => __('Exclude','padma'),
					'options' => 'get_listing_terms()',
					'default' => '',
					'tooltip' => __('Exclude one or more term from the results.','padma')
				),

				'terms-include' => array(
					'type' => 'multi-select',
					'name' => 'terms-include',
					'label' => __('Include','padma'),
					'options' => 'get_listing_terms()',
					'default' => '',
					'tooltip' => __('Only include certain terms in the list.','padma')
				),

				'terms-slug' => array(
					'name' => 'terms-slug',
					'type' => 'text',
					'label' => 'Slug',
					'tooltip' => __('Returns terms whose "slug" matches this value. Default is empty string.','padma')
				),

				'terms-options-display-heading' => array(
					'name' => 'terms-options-display-heading',
					'type' => 'heading',
					'label' => __('Display Taxonomy','padma')
				),

				'terms-hide-empty' => array(
					'type' => 'checkbox',
					'name' => 'terms-hide-empty', 
					'label' => __('Hide Empty?','padma'),
					'tooltip' => __('Toggles the display of term with no posts.','padma'),
					'default' => true
				),

				'terms-hierarchical' => array(
					'type' => 'checkbox',
					'name' => 'terms-hierarchical', 
					'label' => __('Hierarchical?','padma'),
					'tooltip' => __('Whether to include terms that have non-empty descendants .','padma'),
					'default' => true
				)

			),

			'posts-pages-filters' => array(

				'number-of-posts' => array(
					'type' => 'integer',
					'name' => 'number-of-posts',
					'label' => __('Number of Posts','padma'),
					'tooltip' => '',
					'default' => 5
				),

				'posts-pages-post-type-heading' => array(
					'name' => 'posts-pages-post-type-heading',
					'type' => 'heading',
					'label' => __('Filter Content','padma')
				),

				'post-type' => array(
					'type' => 'select',
					'name' => 'post-type',
					'label' => __('Post Type','padma'),
					'tooltip' => '',
					'options' => 'get_post_types()',
					'toggle'    => array(
						'0' => array(
							'hide' => array(
								'#input-post-taxonomy-filter',
								'#input-terms'
							)
						)
					),
					'callback' => 'reloadBlockOptions()'
				),

				'post-taxonomy-filter' => array(
					'label' => __('Select Taxonomy to filter','padma'),
					'type' => 'select',
					'name' => 'post-taxonomy-filter',
					'options' => 'get_taxonomies()',
					'default' => 'category',
					'toggle'    => array(
						'0' => array(
							'hide' => array(
								'#input-terms'
							)
						)
					),
					'callback' => '
						reloadBlockOptions()'
				),

				'terms' => array(
					'type' => 'multi-select',
					'name' => 'terms',
					'tooltip' => ''
				),

				'author' => array(
					'type' => 'multi-select',
					'name' => 'author',
					'label' => __('Author','padma'),
					'tooltip' => '',
					'options' => 'get_authors()'
				),

				'offset' => array(
					'type' => 'integer',
					'name' => 'offset',
					'label' => __('Offset','padma'),
					'tooltip' => __('The offset is the number of entries or posts you would like to skip.  If the offset is 1, then the first post will be skipped.','padma'),
					'default' => 0
				),

				'posts-pages-sort-heading' => array(
					'name' => 'posts-pages-sort-heading',
					'type' => 'heading',
					'label' => __('Sort Content','padma')
				),

				'order-by' => array(
					'type' => 'select',
					'name' => 'order-by',
					'label' => __('Order By','padma'),
					'tooltip' => '',
					'options' => array(
						'date' => __('Date','padma'),
						'title' => __('Title','padma'),
						'rand' => __('Random','padma'),
						'comment_count' => __('Comment Count','padma'),
						'ID' => 'ID'
					)
				),

				'order' => array(
					'type' => 'select',
					'name' => 'order',
					'label' => __('Order','padma'),
					'tooltip' => '',
					'options' => array(
						'desc' => __('Descending','padma'),
						'asc' => __('Ascending','padma'),
					)
				)
			),
		);

	}

	function modify_arguments($args = false) {

		$block = $args['block'];

		/* Content Options */
		$taxomomy = PadmaBlockAPI::get_setting($block, 'post-taxonomy-filter');

		$terms = self::get_listing_terms($taxomomy);
		$label = self::get_taxonomy_label($taxomomy);
		$post_type = PadmaBlockAPI::get_setting($block, 'post-type');
		$taxonomies = self::get_taxonomies($post_type);

		$this->inputs['posts-pages-filters']['terms']['options'] = $terms;
		$this->inputs['posts-pages-filters']['terms']['label'] = $label;
		$this->inputs['posts-pages-filters']['post-taxonomy-filter']['options'] = $taxonomies;

		/* Taxonomy Options */
		$this->inputs['taxonomy-options']['select-taxonomy']['options'] = self::get_taxonomies();

		$taxomomy = PadmaBlockAPI::get_setting($block, 'select-taxonomy');

		$terms = self::get_listing_terms($taxomomy);
		$label = self::get_taxonomy_label($taxomomy);
		$this->inputs['taxonomy-options']['terms']['label'] = $label;
		$this->inputs['taxonomy-options']['terms-child-of']['options'] = $terms;
		$this->inputs['taxonomy-options']['terms-exclude']['options'] = $terms;
		$this->inputs['taxonomy-options']['terms-include']['options'] = $terms;

	}

	function get_taxonomies($post_type='') {

		if (!empty($post_type)) {
			$post_type = array($post_type);
			$args=array(
			  'object_type' => $post_type 
			);
		} else {
			$args = '';
		}

		$output = 'objects';
		$operator = 'and';

		$taxonomy_options = array('&ndash; Do not filter &ndash;');

		$taxonomy_select_query=get_taxonomies($args,$output,$operator);

		if  ($taxonomy_select_query) {
		  foreach ($taxonomy_select_query as $taxonomy)
			$taxonomy_options[$taxonomy->name] = $taxonomy->label;
		} 

		return $taxonomy_options;

	}

	function get_listing_terms($taxonomy='category') {

		if ( !$taxonomy )
			$taxonomy = 'category';

		$taxonomy_label = $this->get_taxonomy_label($taxonomy);

		$terms_options = array('&ndash; Select '. $taxonomy_label .' &ndash;');

		$terms = get_terms( $taxonomy, 'orderby=id&hide_empty=0' );

		if ( !$terms )
			return;

		foreach ($terms as $term)
			$terms_options[$term->term_id] = $term->name;

		return $terms_options;

	}

	function get_taxonomy_label($taxonomy) {

		if ( !$taxonomy )
			$taxonomy = 'category';

		$args = array(
		  'name' => $taxonomy
		);
		$output = 'objects'; // or objects		
		$taxonomy_select_query=get_taxonomies($args,$output);; 

		if  ($taxonomy_select_query) {
		  foreach ($taxonomy_select_query as $taxonomy)
			return $taxonomy->label;
		} 

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

	function get_pages() {

		$page_options = array('&ndash; Default &ndash;');

		$page_select_query = get_pages();

		foreach ($page_select_query as $page)
			$page_options[$page->ID] = $page->post_title;

		return $page_options;

	}

	function get_post_types() {

		$post_type_options = array('&ndash; All Post Types &ndash;');

		$post_types = get_post_types(false, 'objects'); 

		foreach($post_types as $post_type_id => $post_type){

			//Make sure the post type is not an excluded post type.
			if(in_array($post_type_id, array('revision', 'nav_menu_item'))) 
				continue;

			$post_type_options[$post_type_id] = $post_type->labels->name;

		}

		return $post_type_options;

	}

}
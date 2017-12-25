<?php

class PadmaListingsBlockOptions extends PadmaBlockOptionsAPI {

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
	
	public $tabs = array(
		'listing-type' => 'Select Listing Type',
		'posts-pages-filters' => 'Posts &amp; Pages Filters',
		'taxonomy-options' => 'Taxonomy Options'
	);

	public $inputs = array(
		'listing-type' => array(

			'listing-type' => array(
				'type' => 'select',
				'name' => 'listing-type',
				'label' => 'List?',
				'tooltip' => 'Select a type of list output and then configure it with the options on the left.',
				'options' => array(
					'taxonomy' => 'Taxonomy (category, tag etc)',
					'content' => 'Posts or Pages (custom posts)',
					'authors' => 'Authors'
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
				'label' => 'Select Taxonomy'
			),

			'select-taxonomy' => array(
				'label' => 'Select Taxonomy to display',
				'type' => 'select',
				'name' => 'select-taxonomy',
				'options' => 'get_taxonomies()',
				'default' => 'category',
			),

			'terms-options-sorting-heading' => array(
				'name' => 'terms-options-sorting-heading',
				'type' => 'heading',
				'label' => 'Sort Taxonomy'
			),

			'terms-orderby' => array(
				'type' => 'select',
				'name' => 'terms-orderby',
				'label' => 'Order By?',
				'tooltip' => 'Sort term alphabetically, by unique Term ID, or by the count of items in that Term',
				'options' => array(
					'none' => 'None',
					'ID' => 'ID',
					'name' => 'Name',
					'slug' => 'Slug',
					'count' => 'Count',
					//'term_group' => 'Term Group'
				),
				'default' => 'name'
			),

			'terms-order' => array(
				'type' => 'select',
				'name' => 'terms-order',
				'label' => 'Order?',
				'tooltip' => 'Sort order for term (either ascending or descending).',
				'options' => array(
					'DESC' => 'Descending',
					'ASC' => 'Ascending'
				),
				'default' => 'ASC'
			),

			'terms-options-filter-heading' => array(
				'name' => 'terms-options-filter-heading',
				'type' => 'heading',
				'label' => 'Filter Taxonomy'
			),

			'terms-number' => array(
				'type' => 'slider',
				'slider-min' => 0,
				'slider-max' => 30,
				'slider-interval' => 1,
				'name' => 'terms-number',
				'label' => 'Number of terms',
				'default' => '10',
				'tooltip' => 'Sets the number of terms to display. Default 0 for no limit.'
			),

			'terms-child-of' => array(
				'type' => 'select',
				'name' => 'terms-child-of',
				'label' => 'Child Of',
				'options' => 'get_listing_terms()',
				'default' => '',
				'tooltip' => 'Only display terms that are children of what you specify here.'
			),

			'terms-exclude' => array(
				'type' => 'multi-select',
				'name' => 'terms-exclude',
				'label' => 'Exclude',
				'options' => 'get_listing_terms()',
				'default' => '',
				'tooltip' => 'Exclude one or more term from the results.'
			),

			'terms-include' => array(
				'type' => 'multi-select',
				'name' => 'terms-include',
				'label' => 'Include',
				'options' => 'get_listing_terms()',
				'default' => '',
				'tooltip' => 'Only include certain terms in the list.'
			),

			'terms-slug' => array(
				'name' => 'terms-slug',
				'type' => 'text',
				'label' => 'Slug',
				'tooltip' => 'Returns terms whose "slug" matches this value. Default is empty string.'
			),

			'terms-options-display-heading' => array(
				'name' => 'terms-options-display-heading',
				'type' => 'heading',
				'label' => 'Display Taxonomy'
			),

			'terms-hide-empty' => array(
				'type' => 'checkbox',
				'name' => 'terms-hide-empty', 
				'label' => 'Hide Empty?',
				'tooltip' => 'Toggles the display of term with no posts.',
				'default' => true
			),

			'terms-hierarchical' => array(
				'type' => 'checkbox',
				'name' => 'terms-hierarchical', 
				'label' => 'Hierarchical?',
				'tooltip' => 'Whether to include terms that have non-empty descendants .',
				'default' => true
			)

		),

		'posts-pages-filters' => array(

			'number-of-posts' => array(
				'type' => 'integer',
				'name' => 'number-of-posts',
				'label' => 'Number of Posts',
				'tooltip' => '',
				'default' => 5
			),

			'posts-pages-post-type-heading' => array(
				'name' => 'posts-pages-post-type-heading',
				'type' => 'heading',
				'label' => 'Filter Content'
			),

			'post-type' => array(
				'type' => 'select',
				'name' => 'post-type',
				'label' => 'Post Type',
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
				'callback' => '
					reloadBlockOptions()'
			),

			'post-taxonomy-filter' => array(
				'label' => 'Select Taxonomy to filter',
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
				'label' => 'Author',
				'tooltip' => '',
				'options' => 'get_authors()'
			),
			
			'offset' => array(
				'type' => 'integer',
				'name' => 'offset',
				'label' => 'Offset',
				'tooltip' => 'The offset is the number of entries or posts you would like to skip.  If the offset is 1, then the first post will be skipped.',
				'default' => 0
			),
			
			'posts-pages-sort-heading' => array(
				'name' => 'posts-pages-sort-heading',
				'type' => 'heading',
				'label' => 'Sort Content'
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
					'ID' => 'ID'
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
			)
		),
	);

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
<?php
class BloxListingBlockDisplay {
		
	var $count = 0;	
		
	var $query = array();
	
	
	function __construct($block) {
		
		$this->block = $block;
		
		/* Bring in the WordPress pagination variable. */
		$this->paged = get_query_var('paged') ? get_query_var('paged') : 1;
		
	}
	
	
	/**
	 * Created this function to make the call a little shorter.
	 **/
	function get_setting($setting, $default = null) {
		
		return BloxBlockAPI::get_setting($this->block, $setting, $default);
		
	}
	
	function display($args = array()) {

		$block = $this->block;

		$listing_type = BloxBlockAPI::get_setting($block, 'listing-type', 'taxonomy');

		echo '<ul class="list-items">';

			switch ($listing_type) {
			case 'taxonomy':

				$this->display_taxonomy($args);

				break;

			case 'content':

				$this->loop($args);

				wp_reset_query();

				break;
			
			default:
				break;
		}

		echo '</ul>';
		
	}
	
	
	function loop($args = array()) {
						
		if ( !dynamic_loop() ) {
			
			$this->setup_query();
						
				if ( !$this->query->have_posts() ) {
					
					echo '<div class="entry-content">';
						echo apply_filters('blox_search_no_results', __('<p>Sorry, there was no content that matched your search.</p>', 'blox'));
					echo '</div>';
					
				}
			
				while ( $this->query->have_posts() ) {
				
					$this->query->the_post();
					
					$this->count++;
		
					$this->display_entry(array('count' => $this->count));
				
				}
						
		}
							
	}

	function display_taxonomy($args = array()) {

		$block = $this->block;

		$taxonomy = BloxBlockAPI::get_setting($block, 'select-taxonomy', 'category');
		
		$args = array(
		    'orderby'       => BloxBlockAPI::get_setting($block, 'terms-orderby', 'name'), 
		    'order'         => BloxBlockAPI::get_setting($block, 'terms-order', 'ASC'),
		    'hide_empty'    => BloxBlockAPI::get_setting($block, 'terms-hide-empty', 0), 
		    'exclude'       => BloxBlockAPI::get_setting($block, 'terms-exclude', array()),
		    'exclude_tree'  => array(), 
		    'include'       => BloxBlockAPI::get_setting($block, 'terms-include', array()),
		    'number'        => BloxBlockAPI::get_setting($block, 'terms-number', '10'), 
		    'fields'        => 'all', 
		    'slug'          => BloxBlockAPI::get_setting($block, 'terms-slug', ''), 
		    'parent'        => '',
		    'hierarchical'  => BloxBlockAPI::get_setting($block, 'terms-hierarchical', true), 
		    'child_of'      => 0, 
		    'get'           => '', 
		    'name__like'    => '',
		    'pad_counts'    => false, 
		    'offset'        => '', 
		    'search'        => '', 
		    'cache_domain'  => 'core'
		); 
		
		$terms = get_terms( $taxonomy, $args );

		$count = count($terms);
		if ( $count > 0 ){
		    foreach ( $terms as $term ) {
		      	$term_link = get_term_link( $term, $taxonomy  );
			    if( is_wp_error( $term_link ) )
			        continue;
			    //We successfully got a link. Print it out.
			    echo '<li><a href="' . $term_link . '">' . $term->name . '</a></li>';
		    }
		}
	}
	
	function setup_query() {
				
		/* Setup Query Options */
		$query_options = array();

		$query_options['post_type'] = $this->get_setting('post-type', false);

		$taxonomy = $this->get_setting('post-taxonomy-filter', 'category');

		/* Set taxonomy query if specific terms set */
		$terms = $this->get_setting('terms', array());

		if (empty($terms)) {

			$terms = get_terms($taxonomy,
	        array(
	            'orderby' => 'slug',
	            'order' => 'ASC',
	            'fields' => 'ids',
	        ));

		} else {
			$query_options['tax_query'] =  array(
		        array(
		            'taxonomy' => $taxonomy,
		            'field' => 'id',
		            'terms' => $terms,
		       ),
		    );
		}

		

		//Post Limit
		$query_options['posts_per_page'] = $this->get_setting('number-of-posts', '5');
		//End Post Limit

		if ( is_array($this->get_setting('author')) )
			$query_options['author'] = trim(implode(',', $this->get_setting('author')), ', ');

		//Offset
		$query_options['offset'] = $this->get_setting('offset', '0');

		//Order by
		$query_options['orderby'] = $this->get_setting('order-by', 'date');
		$query_options['order'] = $this->get_setting('order', 'desc');
		//End order by
		
		//Initiate query instance
		$this->query = new WP_Query($query_options);
		
	}
	
		
	function display_entry($args = array()) {
		
		global $post;
		
		/* Setup generic variables */
			$post_id = get_the_id();
			$post_class = $this->entry_class();
			$post_permalink = get_permalink();
			$post_type = get_post_type();
		/* End generic variables */

		/* Setup Titles */
			$hide_title = BloxLayoutOption::get($post_id, 'hide-title', false, true);

			$alternate_title = BloxLayoutOption::get($post_id, 'alternate-title', false, true);

			$post_title = (isset($alternate_title) && $alternate_title) ? $alternate_title : get_the_title();
			$post_title_tooltip = sprintf(esc_attr__('%s', 'blox'), the_title_attribute('echo=0'));
			
			$post_title_link = '<a href="' . $post_permalink . '" title="' . $post_title_tooltip . '" rel="bookmark">' . $post_title . '</a>';	
		/* End Titles */


			echo '<li id="post-' . $post_id . '" class="' . $post_class . '">';


				echo $post_title_link;	


			echo '</li>';
	}
	
	/**
	 * Assembles the classes for the posts.
	 *
	 * @global object $post
	 * @global int $blog_post_alt
	 * 
	 * @param bool $print Determines whether or not to echo the post classes.
	 * 
	 * @return bool|string If $print is true, then echo the classes, otherwise just return them as a string. 
	 **/
	function entry_class() {

		global $post, $blog_post_alt, $authordata;
		
		$c = get_post_class();

		if ( !isset($blog_post_alt) ) 
			$blog_post_alt = 1;

		if ( is_object($authordata) )
			$c[] = 'author-' . sanitize_title_with_dashes(strtolower($authordata->user_login));

		if ( ++$blog_post_alt % 2 )
			$c[] = 'alt';
			
		//Add the custom classes from the meta box
		if ( $custom_css_class = BloxLayoutOption::get(get_the_id(), 'css-class', null, true) ) {
			
			$custom_css_classes = str_replace('  ', ' ', str_replace(',', ' ', esc_attr(strip_tags($custom_css_class))));

			$c = array_merge($c, array_filter(explode(' ', $custom_css_classes)));
			
		}

		//Add column class only if layout enabled and it is not singular in default mode
		if ( $this->get_setting('enable-column-layout', false) ) {

			if ( !(is_singular() && $this->get_setting('mode') == 'default') ) {
				$c[] = 'post-column column-' . $this->count;
			}

		}

		$c[] = $this->get_setting('mode');	

		$c = join(' ', $c);

		return $c;

	}

}
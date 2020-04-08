<?php

class PadmaContentBlockDisplay {

	var $count = 0;			
	var $query = array();


	function __construct($block) {

		$this->block = $block;

		if ( get_query_var( 'paged' ) ) { 			
			$this->paged = get_query_var( 'paged' );

		}elseif ( get_query_var( 'page' ) ) { 
			$this->paged = get_query_var( 'page' );

		}else { 
			$this->paged = 1; 
		}
		$this->add_hooks();

	}


	/**
	 * Created this function to make the call a little shorter.
	 **/
	function get_setting($setting, $default = null) {

		return PadmaBlockAPI::get_setting($this->block, $setting, $default);

	}


	function add_hooks() {

		if ( !class_exists('pluginbuddy_loopbuddy') ) {

			add_filter('the_content_more_link', array($this, 'more_link'));

			add_filter('excerpt_more', '__return_null');
			add_filter('wp_trim_excerpt', array($this, 'excerpt_more_link'));

		}

		add_filter('the_content', array($this, 'filter_nofollow_links_in_post'));				

	}


	function remove_hooks() {

		remove_filter( 'the_content_more_link', array( $this, 'more_link' ) );

		remove_filter( 'excerpt_more', '__return_null' );
		remove_filter( 'wp_trim_excerpt', array( $this, 'excerpt_more_link' ) );

		remove_filter('the_content', array($this, 'filter_nofollow_links_in_post'));

	}


	function display($args = array()) {

		/* Use the generic_content action if it's set.  See http://core.trac.wordpress.org/ticket/20509 */
			if ( $this->get_setting('mode', 'default') == 'default' && has_action('generic_content') && !did_action('generic_content') ) {
				return do_action( 'generic_content' );
			}


		/* Populate JS variable with wp_query global that way loading the block content with AJAX still shows the correct content */
			if ( PadmaRoute::is_visual_editor_iframe() && $this->get_setting('mode', 'default') == 'default' ) {

				echo '<script type="text/javascript">';
				echo 'PADMA_WP_Query_Vars = ' . json_encode($GLOBALS['wp_query']->query_vars) . ';';			

				$postCount = 0;
				foreach ($GLOBALS['posts'] as $key => $value) {
					echo 'localStorage[\'visual-editor-block-post-data-'.$this->block['id'].'-'.$postCount.'\'] = JSON.stringify(' . json_encode($value->ID) . ');';
					++$postCount;
				}

				echo '</script>';

			}

		/* If LoopBuddy is activated, we'll strictly rely on it for the query setup and how the content is displayed. */
			if (class_exists('pluginbuddy_loopbuddy')) {

				global $pluginbuddy_loopbuddy;

				$loopbuddy_query = $this->get_setting('loopbuddy-query', -1);
				$loopbuddy_layout = $this->get_setting('loopbuddy-layout', -1);

				if ( isset($pluginbuddy_loopbuddy) && $loopbuddy_query !== -1 ) {
					echo $pluginbuddy_loopbuddy->render_loop($loopbuddy_query, $loopbuddy_layout);

					$this->remove_hooks();

					return;
				}

			}

		/* Display the 404 text if it's a 404 (has to be default behavior) */
			if ( is_404() && $this->get_setting('mode', 'default') == 'default' && !padma_get('ve-live-content-query', $this->block, false) ) {
				$this->remove_hooks();

				return $this->display_404();
			}

		/* Display loop like normal if nothing else fires first */
			$this->loop($args);
			$this->remove_hooks();

			wp_reset_query();

	}


	function loop($args = array()) {


		$defaults = array('archive' => false);
		extract($defaults);
		extract($args, EXTR_OVERWRITE);


		if ( !dynamic_loop() ) {

			$this->setup_query();

			if ($this->get_setting('show-archive-title', true))

				$this->show_query_title();

			if ($this->get_setting('show-entry', true) || $this->get_setting('comments-visibility') != "hide" ) {

				echo '<div class="loop">';

				if ( $this->query instanceof SWP_Query ) {

					$swp_engine = $this->get_setting( 'swp-engine' );
					$swp_search  = isset( $_REQUEST[ 'swpquery_' . $swp_engine ] ) ? sanitize_text_field( $_REQUEST[ 'swpquery_' . $swp_engine ] ) : '';

					$have_posts = ! empty( $swp_search ) && ! empty( $this->query->posts );

				} else if ( $this->query instanceof WP_Query ) {

					$have_posts = $this->query->have_posts();

				} else {

					$have_posts = false;

				}

				if ( !$have_posts && ( $this->query instanceof SWP_Query || ( is_search() && $this->get_setting( 'mode', 'default' ) == 'default' ) ) ) {

					echo '<div class="entry-content">';
						echo apply_filters('padma_search_no_results', __('<p>Sorry, there was no content that matched your search.</p>', 'padma'));
					echo '</div>';

				}

				if ( $this->query instanceof SWP_Query ) {

					foreach ( $this->query->posts as $swp_post ) {

						setup_postdata($swp_post);
						$GLOBALS['post'] = $swp_post;

						$this->count ++;

						$this->possible_row_open();

						$this->display_entry( array( 'count' => $this->count ) );

						$this->possible_row_close();

					}

					wp_reset_postdata();

				} else {

					while ( $this->query->have_posts() ) {

						$this->query->the_post();

						$this->count ++;

						$this->possible_row_open();

						$this->display_entry( array( 'count' => $this->count ) );

						$this->possible_row_close();

					}

				}

				echo '</div>';
			}

			$this->display_pagination();

		}

	}


		function possible_row_open() {

			if ( isset($this->row_open) && $this->row_open )
				return;

			if ( $this->get_setting('mode', 'default') == 'default' && is_singular() )
				return;

			if ( !(is_search() || $this->paged > 1) && $this->count <= $this->get_setting('featured-posts', 1) )
				return;

			echo '<div class="entry-row">';

			$this->row_open = true;

		}


		function possible_row_close() {

 	 		$posts_per_row = ($this->get_setting( 'enable-column-layout' ) && ! ( is_singular() && $this->get_setting( 'mode', 'default' ) == 'default' )) ? $this->get_setting( 'posts-per-row', '2' ) : 1;

			/* If a row isn't open then we don't have anything to close */
			if ( !isset($this->row_open) || !$this->row_open )
				return false;

			$featured_posts_on_page = !(is_search() || $this->paged > 1) ? $this->get_setting('featured-posts', 1) : 0;

			/* Only run the every nth post check if it's not the last post.  If it is the last post, then we have to close the row no matter what. */
			if ( $this->count < $this->query->post_count ) {

				/* Normal check to close.  Close every nth post */
				if ( ($this->count - $featured_posts_on_page) % $posts_per_row !== 0 )
					return false;

			}

			echo '</div>';

			$this->row_open = false;

		}


	function show_query_title() {


		if ( $this->query instanceof SWP_Query ){

			$searcbtp = SWP();

			$swp_engine = $this->get_setting( 'swp-engine' );
			$swp_engine_info = $searcbtp->settings['engines'][$swp_engine];
			$swp_search = isset( $_REQUEST[ 'swpquery_' . $swp_engine ] ) ? sanitize_text_field( $_REQUEST[ 'swpquery_' . $swp_engine ] ) : '';

			$return = '<h1 class="archive-title search-title">';
			$return .= apply_filters( 'padma_search_title', sprintf( __( $swp_engine_info['searcbtp_engine_label'] . ' Results for: %s', 'padma' ), '<span>' . $swp_search . '</span>' ) );
			$return .= '</h1>';

			echo apply_filters( 'padma_query_title', $return );
			return;

		}

		/* Stop this function if it's a custom query, index, front page, or singular. */
		if (( $this->get_setting('mode', 'default') != 'default' || is_home() || is_front_page() || is_singular() || get_post_type() == 'forum' )  || (is_archive() && !$this->get_setting('show-archive-title', true))) {
			return;
		}

		$queried_object = get_queried_object();

		$return = '';

		/* Date Archives */
		if ( is_date() ) {

			$return .= '<h1 class="archive-title date-archive-title">';

				if ( is_day() )
					$return .= apply_filters('padma_archive_title', sprintf( __( 'Daily Archives: %s', 'padma' ), '<span>' . get_the_date() . '</span>'));

				elseif ( is_month() )
					$return .= apply_filters('padma_archive_title', sprintf( __( 'Monthly Archives: %s', 'padma' ), '<span>' . get_the_date('F Y') . '</span>'));

				elseif ( is_year() )
					$return .= apply_filters('padma_archive_title', sprintf( __( 'Yearly Archives: %s', 'padma' ), '<span>' . get_the_date('Y') . '</span>' ));

				else 
					$return .= apply_filters('padma_archive_title', __( 'Blog Archives', 'padma'));

			$return .= '</h1>';

		}

		/* Category Archives */
		else if ( is_category() ) {

			$return .= '<h1 class="archive-title category-title">';

			if( $this->get_setting('show-archive-title-type', 'normal') == 'normal'){

				$return .= apply_filters('padma_category_title', sprintf(__('Category Archives: %s', 'padma'), '<span>' . single_cat_title('', false) . '</span>'));


			}elseif ( $this->get_setting('show-archive-title-type') == 'only-archive-name' ) {			

				$return .= apply_filters('padma_category_title', '<span>' . single_cat_title('', false) . '</span>');


			}elseif ( $this->get_setting('show-archive-title-type') == 'show-custom-archive-title' ) {


				$custom_title = $this->get_setting('custom-archive-title','Category Archives');
				$custom_title = str_replace('%archive%', '<span>' . single_cat_title('', false) . '</span>', $custom_title);
				$return .= apply_filters('padma_category_title', $custom_title);

			}

			$return .= '</h1>';
			$category_description = category_description();

			if ( !empty($category_description) ){
				$return .= apply_filters('padma_category_archive_meta', '<div class="archive-meta category-archive-meta">' . $category_description . '</div>');
			}

		}

		/* Author Archives */
		else if ( is_author() ) {

			$author = $queried_object;						
			$author_url = esc_url(get_the_author_meta('google_profile', $author->ID));

			$return .= '<h1 class="archive-title author-title">';

				if ( strpos($author_url, 'http') === 0 )
					$return .= sprintf(__( 'Author Archives: %s', 'padma'), '<span class="vcard"><a class="url fn n" href="' . $author_url . '" title="' . esc_attr($author->display_name) . '" rel="author">' . $author->display_name . '</a></span>');

				else
					$return .= sprintf(__( 'Author Archives: %s', 'padma'), '<span class="vcard">' . $author->display_name . '</span>');

			$return .= '</h1>';

		}

		/* Search */
		else if ( is_search() ) {

			$return .= '<h1 class="archive-title search-title">';
				$return .= apply_filters('padma_search_title', sprintf(__('Search Results for: %s', 'padma'), '<span>' . get_search_query() . '</span>'));
			$return .= '</h1>';

		}

		/* Tag Archives */
		else if ( is_tag() ) {

			$return .= '<h1 class="archive-title search-title">';
				$return .= apply_filters('padma_tag_title', sprintf(__('Tag Archives: %s', 'padma'), '<span>' . single_tag_title('', false) . '</span>'));
			$return .= '</h1>';

			$tag_description = tag_description();
			if ( !empty($tag_description) )
				$return .= apply_filters('padma_tag_archive_meta', '<div class="archive-meta tag-archive-meta">' . $tag_description . '</div>');

		}

		/* Custom Post Type Archives */
		else if ( is_post_type_archive() ) {

			$return .= '<h1 class="archive-title post-type-archive-title">';
				$return .= apply_filters('padma_post_type_archive_title', $queried_object->labels->name);
			$return .= '</h1>';

		}

		/* Custom Taxonomy Archives */
		else if ( is_tax() ) {

			$taxonomy = get_taxonomy($queried_object->taxonomy);
			$term = get_term($queried_object->term_id, $queried_object->taxonomy);

			$return .= '<h1 class="archive-title taxonomy-archive-title">';
				$return .= apply_filters('padma_taxonomy_archive_title', $taxonomy->labels->singular_name . ': <span>' . $term->name . '</span>');
			$return .= '</h1>';

			$term_description = term_description();
			if ( !empty($term_description) )
				$return .= apply_filters('padma_term_archive_meta', '<div class="archive-meta term-archive-meta">' . $term_description . '</div>');		

		}

		echo apply_filters('padma_query_title', $return);

	}


	function setup_query() {

		if ( $this->get_setting( 'swp-engine' ) && class_exists( 'SWP_Query' ) ) {

			/* Setup Query Options */
			$swp_query_options = array(
				'engine' => $this->get_setting( 'swp-engine' )
			);

			$swp_engine = $swp_query_options['engine'];

			$swp_query_options['page'] = isset( $_REQUEST[ 'swppg_' . $swp_engine ] ) ? absint( $_REQUEST[ 'swppg_' . $swp_engine ] ) : 1;
			$swp_query_options['s']    = isset( $_REQUEST[ 'swpquery_' . $swp_engine ] ) ? sanitize_text_field( $_REQUEST[ 'swpquery_' . $swp_engine ] ) : '';

			if ( $swp_query_options['s'] ) {
				$this->query = new SWP_Query( $swp_query_options );

				return;
			}

		}

		if ( $this->get_setting('mode', 'default') == 'default' ) {

			if ( padma_post( 'wpQueryVars' ) && is_array( padma_post( 'wpQueryVars' ) ) ) {


				$query_options = padma_post( 'wpQueryVars' );

				if( ! is_array($query_options['post_type']) ){
					$query_options['post_type'] = array('post','page');
				}

				$this->query = new WP_Query( $query_options );
				$GLOBALS['wp_query'] = $this->query;


			} else {

				$this->query = $GLOBALS['wp_query'];

			}

		} else {

			/* Setup Query Options */
			$query_options = array();

			//If we're just fetching a page, we can simply do that.  Otherwise, we have to use all of the query filters.
			if ( $this->get_setting('fetch-page-content', false) ) {

				$query_options['page_id'] = $this->get_setting('fetch-page-content', false);

			} else {

				// Include / Exclude by ID

				if($this->get_setting('byid-include') ) 
					$query_options['post__in'] = explode(',', $this->get_setting('byid-include'));

				if($this->get_setting('byid-exclude') ) 
					$query_options['post__not_in'] = explode(',', $this->get_setting('byid-exclude'));
				// End Include / Exclude by ID

				//Categories
				if($this->get_setting('categories-mode', 'include') == 'include')
					$query_options['category__in'] = $this->get_setting('categories', array());

				if($this->get_setting('categories-mode', 'include') == 'exclude')
					$query_options['category__not_in'] = $this->get_setting('categories', array());
				//Categories

				$query_options['post_type'] = $this->get_setting('post-type', false);

				//Post Limit
					$query_options['posts_per_page'] = $this->get_setting('number-of-posts', 10);
				//End Post Limit

				if ( is_array($this->get_setting('author')) )
					$query_options['author'] = trim(implode(',', $this->get_setting('author')), ', ');

				//Order by
				$query_options['orderby'] = $this->get_setting('order-by', 'date');
				$query_options['order'] = $this->get_setting('order', 'desc');
				//End order by

				$query_options['offset'] = $this->get_setting('offset', 0);

				if ( $this->get_setting('paginate', true) ) {

					$query_options['paged'] = $this->paged;


					if( $query_options['paged'] > 1 ){

						$query_options['offset'] = $this->get_setting('number-of-posts', 10) * ($query_options['paged'] - 1);

						if( $this->get_setting('offset', 0) >= 1 ){
							$query_options['offset'] += $this->get_setting('offset');
						}

					}					

				}

			} //End else conditional for either page fetching or custom query filters

			$this->query = new WP_Query($query_options);

		}

	}


	function display_entry($args = array()) {

		global $post;

		$defaults = array(
			'count' => false, 
			'single' => false
		);

		$args = array_merge($defaults, $args);

		if ( $this->get_setting('show-entry', true) ) {

			/* Setup generic variables */
				$post_id 		= get_the_id();
				$post_class 	= $this->entry_class();
				$post_permalink = get_permalink();
				$post_type 		= get_post_type();
			/* End generic variables */

			/* Meta */
				$entry_meta_display_post_types = $this->get_setting('show-entry-meta-post-types', array('post'));

				if ( is_array($entry_meta_display_post_types) && in_array($post_type, $entry_meta_display_post_types) ) {

					$entry_meta_above = $this->parse_meta($this->get_setting('entry-meta-above', 'Posted on %date% by %author% &bull; %comments%'));
					$entry_utility_below = $this->parse_meta($this->get_setting('entry-utility-below', 'Filed Under: %categories%'));

					if ( $entry_meta_above )
						$entry_meta_above = '<div class="entry-meta entry-meta-above">' . padma_parse_php($entry_meta_above) . '</div>';

					if ( $entry_utility_below )
						$entry_utility_below = '<footer class="entry-utility entry-utility-below entry-meta">' . padma_parse_php($entry_utility_below) . '</footer>';

				} else {

					$entry_meta_above = null;
					$entry_utility_below = null;

				}
			/* End Meta */

			/* Setup Titles */
				$hide_title = PadmaLayoutOption::get($post_id, 'hide-title', false, true, false);
				$alternate_title = PadmaLayoutOption::get($post_id, 'alternate-title', false, true);

				$post_title = (isset($alternate_title) && $alternate_title) ? $alternate_title : get_the_title();
				$post_title_tooltip = sprintf(esc_attr__('%s', 'padma'), the_title_attribute('echo=0'));

				/* Show <h1> for titles if it's a singlular page, use <h3> for archives, and <h2> for everything else. */
				if ( is_singular() && $this->get_setting('mode', 'default') == 'default' )
					$title_tag = 'h1';
				elseif ( is_archive() || is_search() )
					$title_tag = 'h3';
				else
					$title_tag = 'h2';

				/* If the post is singular or the post type is a page being displayed through content fetching, don't put a link in the title. */
				if ( ( ( is_singular() && $this->get_setting('mode', 'default') != 'custom-query' ) || !$this->get_setting('link-titles', true) ) && !is_a( $this->query, 'SWP_Query' ) )
					$post_title_link = $post_title;	
				else
					$post_title_link = '<a href="' . $post_permalink . '" title="' . $post_title_tooltip . '" rel="bookmark">' . $post_title . '</a>';	
			/* End Titles */

			do_action('padma_before_entry', $args);

            $schema_itemtype = $post_type == 'post' ? 'Article' : 'CreativeWork';

			if(	$this->get_setting('featured-image-as-background', false)){

				$featured_image = apply_filters('padma_featured_image_src', wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full'));
				$featured_image_url = apply_filters('padma_featured_image_url', $featured_image[0]);

				echo '<article id="post-' . $post_id . '" class="' . $post_class . '" style="background-image: url('.$featured_image_url.');">';

			}else{

				echo '<article id="post-' . $post_id . '" class="' . $post_class . '">';
			}


			/**
			 *
			 * Custom Fields "Above"
			 *
			 */			
			if(isset($this->block['custom-fields']['above']) && is_array($this->block['custom-fields']['above']) && count($this->block['custom-fields']['above'])>0){

				echo '<div class="'. implode(' ', apply_filters('padma_content_custom_fields_class', array('custom-fields', 'custom-fields-above') ) )  .'">';

				foreach ($this->block['custom-fields']['above'] as $post_type => $custom_fields) {

					foreach ($custom_fields as $field_name => $label) {

						$group_tag = apply_filters('padma_content_custom_fields_group_tag', 'div' );
						$label_tag = apply_filters('padma_content_custom_fields_label_tag', 'label' );
						$field_tag = apply_filters('padma_content_custom_fields_field_tag', 'div' );

						$custom_field_content = get_post_meta($post_id,$field_name,true);
						$custom_field_content = apply_filters('padma_content_custom_fields_field_content', $custom_field_content );

						if($custom_field_content){

							// open tag
							echo '<' . $group_tag . ' class="custom-fields-group">';

							if($label)
								echo '<'.$label_tag.'>'. $label . '</'.$label_tag.'>';

							echo '<'.$field_tag.'>'. $custom_field_content . '</'.$field_tag.'>';

							// close tag
							echo '</' . $group_tag . '>';										
						}
					}
				}

				echo '</div>';
			}



			/**
			 *
			 * Schema 
			 *
			 */			

			echo PadmaSchema::article($post);



			echo '<link itemprop="mainEntityOfPage" href="'.get_permalink($post_id).'" />';

					do_action('padma_entry_open', $args);		

					//Show post thumbnail
					$this->display_thumbnail($post, 'above-title');

					// only open header tag if show titles is on or entry meta above is present so an empty <header> tag does not get output if titles are hidden and no meta above is present
					if ( $this->get_setting( 'show-titles', true ) || $entry_meta_above ) {
						echo '<header>';
					}

						do_action('padma_before_entry_title', $args);			


						//Show the title based on the Show Titles option
						if (
							$this->get_setting('show-titles', true)
							&& !($hide_title == 'singular' && $title_tag == 'h1')
							&& !($hide_title == 'list' && $title_tag != 'h1')
							&& !($hide_title == 'both')
						) {

							echo '<' . $title_tag . ' class="entry-title" itemprop="headline">';

								echo $post_title_link;

								if ( apply_filters('padma_show_edit_link', $this->get_setting('show-edit-link', true)) )
									edit_post_link('Edit Entry');

							echo '</' . $title_tag . '>';

						}

						do_action('padma_after_entry_title', $args);


						/**
						 *
						 * Custom Fields "After Title"
						 *
						 */
						if(isset($this->block['custom-fields']['after-title']) && is_array($this->block['custom-fields']['after-title']) && count($this->block['custom-fields']['after-title'])>0){

							echo '<div class="'. implode(' ', apply_filters('padma_content_custom_fields_class', array('custom-fields', 'custom-fields-after-title') ) )  .'">';

							foreach ($this->block['custom-fields']['after-title'] as $post_type => $custom_fields) {

								foreach ($custom_fields as $field_name => $label) {

									$group_tag = apply_filters('padma_content_custom_fields_group_tag', 'div' );
									$label_tag = apply_filters('padma_content_custom_fields_label_tag', 'label' );
									$field_tag = apply_filters('padma_content_custom_fields_field_tag', 'div' );

									$custom_field_content = get_post_meta($post_id,$field_name,true);
									$custom_field_content = apply_filters('padma_content_custom_fields_field_content', $custom_field_content );

									if($custom_field_content){

										// open tag
										echo '<' . $group_tag . ' class="custom-fields-group">';

										if($label)
											echo '<'.$label_tag.'>'. $label . '</'.$label_tag.'>';

										echo '<'.$field_tag.'>'. $custom_field_content . '</'.$field_tag.'>';

										// close tag
										echo '</' . $group_tag . '>';										
									}
								}
							}

							echo '</div>';
						}

					echo $entry_meta_above;

					if ( $this->get_setting( 'show-titles', true ) || $entry_meta_above ) {
						echo '</header>';
					}

					$this->display_thumbnail($post, 'above-content');

					$this->display_entry_content($args);

					$this->display_thumbnail( $post, 'below-content' );

					echo $entry_utility_below;

					do_action('padma_entry_close', $args);			


			/**
			 *
			 * Custom Fields "Below"
			 *
			 */
			if(isset($this->block['custom-fields']['below']) && is_array($this->block['custom-fields']['below']) && count($this->block['custom-fields']['below'])>0){

				echo '<div class="'. implode(' ', apply_filters('padma_content_custom_fields_class', array('custom-fields', 'custom-fields-below') ) )  .'">';

				foreach ($this->block['custom-fields']['below'] as $post_type => $custom_fields) {

					foreach ($custom_fields as $field_name => $label) {

						$group_tag = apply_filters('padma_content_custom_fields_group_tag', 'div' );
						$label_tag = apply_filters('padma_content_custom_fields_label_tag', 'label' );
						$field_tag = apply_filters('padma_content_custom_fields_field_tag', 'div' );

						$custom_field_content = get_post_meta($post_id,$field_name,true);
						$custom_field_content = apply_filters('padma_content_custom_fields_field_content', $custom_field_content );

						if($custom_field_content){

							// open tag
							echo '<' . $group_tag . ' class="custom-fields-group">';

							if($label)
								echo '<'.$label_tag.'>'. $label . '</'.$label_tag.'>';

							echo '<'.$field_tag.'>'. $custom_field_content . '</'.$field_tag.'>';

							// close tag
							echo '</' . $group_tag . '>';										
						}
					}
				}

				echo '</div>';
			}

				echo '</article>';

				do_action('padma_after_entry', $args);

				$this->display_post_navigation();		

		} //show-entry check			

		$this->display_comments($args);

	}


	function display_entry_content($args) {

		global $post;

		$entry_content_display = $this->get_setting('entry-content-display', 'normal');

		$show_full_entries = false;
		$show_excerpts = false;

		if ( $entry_content_display == 'hide' )
			return null;

		/* Figure out whether the full entry or excerpt should be displayed */
			if ( $entry_content_display == 'full-entries' ) {

				$show_full_entries = true;

			} elseif ( $entry_content_display == 'excerpts' ) {

				$show_excerpts = true;

			} elseif ( $args['count'] > $this->get_setting('featured-posts', 1) && !(is_singular() && $this->get_setting('mode', 'default') == 'default') ) {

				$show_excerpts = true;

			} elseif ( $this->query instanceof SWP_Query || is_search() || $this->paged > 1 ) {

				$show_excerpts = true;

			} else {

				$show_full_entries = true;

			}

		do_action('padma_before_entry_content', $args);

		if ( $show_full_entries || get_post_type() == 'forum' ) {

			echo '<div class="entry-content" itemprop="text">';

				$this->display_thumbnail( $post, 'inside-content' );

				/* Force WordPress to respect the  <!--more--> tag if using a custom query */
				if ( $this->get_setting('mode', 'default') == 'custom-query' ) {

					global $more; 
					$more = false;
					the_content();
					$more = true; 

				} else {

					the_content();

				}

				wp_link_pages(array( 'before' => '<div class="page-link">' . __( 'Pages:', 'padma' ), 'after' => '</div>' ));

			echo '</div>';

		} elseif ( $show_excerpts ) {

			echo '<div class="entry-summary entry-content" itemprop="text">';

				$this->display_thumbnail( $post, 'inside-content' );

				if( ! $this->get_setting( 'custom-excerpts', 0 ) ){
					the_excerpt();
				} else {
					echo $this->excerpt_more_link( wp_trim_words( get_the_excerpt(), $this->get_setting( 'excerpts-length', 55 ) ) );
				}

			echo '</div>';
		}

		do_action('padma_after_entry_content', $args);

	}


	function display_404() {

		$args = array(
			'404' => true
		);

		$post_id = 'system-404';
		$post_class = 'page system-page system-404 hentry';

		do_action('padma_before_entry', $args);		

		echo '<div id="post-' . $post_id . '" class="' . $post_class . '">';

			do_action('padma_entry_open', $args);		

			do_action('padma_before_entry_title', $args);			

				echo '<h1 class="entry-title">' . __('Whoops!  Page Not Found', 'padma') . '</h1>';

			do_action('padma_after_entry_title', $args);			

			do_action('padma_before_entry_content', $args);			

				echo '<div class="entry-content">';

					echo __('<p>Don\'t fret, you didn\'t do anything wrong.  It appears that the page you are looking for does not exist or has been moved elsewhere.</p>', 'padma');

					echo sprintf(__('<p>If you keep ending up here, please head back to our <a href="%s">homepage</a> or try the search form below.</p>', 'padma'), home_url());

					get_search_form(true);

				echo '</div>';

			do_action('padma_after_entry_content', $args);

			do_action('padma_entry_close', $args);			

			echo '</div>';

		do_action('padma_after_entry', $args);

	}


	function display_comments($hook_args) {

		global $post;
		global $withcomments;
		global $padma_comments_template_args;

		add_filter('padma_comment_form_args', array($this, 'modify_comment_args'));		

		/* If the block is set to always hide the comments, then don't do any more checks. */
		if ( $this->get_setting('comments-visibility', 'auto') == 'hide' )
			return false;


		/* Only do these checks if the visibility is set to auto. */
		if ( $this->get_setting('comments-visibility', 'auto') == 'auto' ) {

			$post_type = get_post_type();

			if ( !is_singular() )
			 	return false;

			if ( $post_type != 'post' )
				return false;

			if ( $this->get_setting('mode', 'default') == 'custom-query' )
				return false;

		}

		/* We're all good.  Show the comments. */
		do_action('padma_before_entry_comments', $hook_args);		

		$withcomments = true;

		/* Display Padma comments and send args to the comments_template() via a global variable */
		$padma_comments_template_args = array(
			'comments-area-heading-responses-number-1' => $this->get_setting('comments-area-heading-responses-number-1', 'One Response'),
			'comments-area-heading-responses-number' => $this->get_setting('comments-area-heading-responses-number', '%num% Responses'),
			'comments-area-heading' => $this->get_setting('comments-area-heading', '%responses% to <em>%title%</em>')
		);

		comments_template();

		do_action('padma_after_entry_comments', $hook_args);		

	}


		function modify_comment_args() {

			$leave_reply = $this->get_setting('leave-reply', 'Leave a reply');
			$title_reply_to = $this->get_setting('leave-reply-to', 'Leave a Reply to %s');
			$cancel_reply_text = $this->get_setting('cancel-reply-link', 'Cancel reply');
			$submit_text = $this->get_setting('label-submit-text', 'Post Comment');

			$comments_args = array(
				'comment_notes_before' => null,
				'comment_notes_after' => null,
				'cancel_reply_link' => __('Discard Reply', 'padma'),
				'title_reply' => $leave_reply,
				'title_reply_to' => $title_reply_to,
				'cancel_reply_link' => $cancel_reply_text,
				'label_submit' => $submit_text
			);

			return $comments_args;

		}


	function display_pagination($position = 'below') {

	 	if ( $this->query->max_num_pages <= 1 || !$this->get_setting('paginate', true) )
			return;

		echo '<div id="nav-' . $position . '" class="loop-navigation loop-utility loop-utility-' . $position . '" itemscope itemtype="http://schema.org/SiteNavigationElement">';

			/* If wp_pagenavi() plugin is activated, just use it. */
			if ( $this->query instanceof SWP_Query ) {

				$swp_engine = $this->get_setting('swp-engine');

				$swp_pagination = paginate_links( array(
						'format'  => '?swppg_' . $swp_engine . '=%#%',
						'current' => isset( $_REQUEST[ 'swppg_' . $swp_engine ] ) ? absint( $_REQUEST[ 'swppg_' . $swp_engine ] ) : 1,
						'total'   => $this->query->max_num_pages
				) );

				echo $swp_pagination;

			} else if ( function_exists('wp_pagenavi') ) {

				wp_pagenavi();

			} else {

				$older_posts_text = __('<span class="meta-nav">&larr;</span> Older posts', 'padma');
				$newer_posts_text = __('Newer posts <span class="meta-nav">&rarr;</span>', 'padma');

				echo '<div class="nav-previous" itemprop="url">' . get_next_posts_link($older_posts_text, $this->query->max_num_pages) . '</div>';
				echo '<div class="nav-next" itemprop="url">' . get_previous_posts_link($newer_posts_text) . '</div>';

			}

		echo '</div>';


	}


	function display_thumbnail($post, $area = 'above-title') {

		if ( !has_post_thumbnail() || !$this->get_setting('show-post-thumbnails', true) || !apply_filters('padma_featured_image_src', wp_get_attachment_image_src(get_post_thumbnail_id(), 'full')) || $this->get_setting('featured-image-as-background', false))
			return;

		$entry_thumbnail_position = $this->get_setting('use-entry-thumbnail-position', true) ? PadmaLayoutOption::get($post->ID, 'position', null, true, 'post-thumbnail') : false;
		$position = $entry_thumbnail_position ? $entry_thumbnail_position : $this->get_setting('post-thumbnail-position', 'left');

		if (
			( $area == 'above-content' && ! ( $position == 'above-content'  ) ) ||
			( $area == 'inside-content' && ! ( $position == 'left-content' || $position == 'right-content' ) ) ||
			( $area == 'above-title' && ! ( $position == 'above-title' || $position == 'left' || $position == 'right' ) ) ||
			( $area == 'below-content' && ! ( $position == 'below-content' ) )
		) {
			return;
		}

		/* Get the size for cropping */
			if ( $position == 'left' || $position == 'right'  || $position == 'left-content' || $position == 'right-content' )  {
				$thumbnail_width = $this->get_setting('post-thumbnail-size', 125);
				$thumbnail_height = $thumbnail_width;
			} else {
				$thumbnail_width = ( $this->get_setting( 'enable-column-layout' ) && ! ( is_singular() && $this->get_setting( 'mode', 'default' ) == 'default' ) ) ? PadmaContentBlock::get_column_width($this->block) : PadmaBlocksData::get_block_width($this->block);
				$thumbnail_height = $thumbnail_width * ($this->get_setting('post-thumbnail-height-ratio', 35) * .01);
			}

		/* Get the image URL */
			if ( $this->get_setting('crop-post-thumbnails', true) ) {

				$thumbnail = apply_filters('padma_featured_image_src', wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'));  

				$thumbnail_url = apply_filters('padma_featured_image_url', padma_resize_image($thumbnail[0], $thumbnail_width, $thumbnail_height));

			} else {

				$thumbnail = apply_filters('padma_featured_image_src', wp_get_attachment_image_src(get_post_thumbnail_id(), array(
					$thumbnail_width, 
					$thumbnail_height
				)));  

				$thumbnail_url = apply_filters('padma_featured_image_url', $thumbnail[0]);

				$thumbnail_width = 'auto';
				$thumbnail_height = 'auto';

			}

		if ( $this->get_setting( 'post-thumbnails-link', 'entry' ) !== 'none' ) {

			$thumbnail_link = $this->get_setting( 'post-thumbnails-link', 'entry' );

			if ( $thumbnail_link == 'entry' ) {

				$thumb_link = get_permalink();

			} elseif( $thumbnail_link == 'custom' ){

				$thumb_link = $this->get_setting( 'post-thumbnails-custom-link' );

			} else {

				$thumb_link = get_attachment_link( get_post_thumbnail_id() );

			}

			$target = '';
			if ( $this->get_setting( 'post-thumbnails-link-new-tab', false ) ){
				$target = '_blank';
			}


			echo '<a href="' . $thumb_link . '" target="'.$target.'" class="post-thumbnail post-thumbnail-' . $position . '">
				<img src="' . esc_url( $thumbnail_url ) . '" alt="' . the_title_attribute( 'echo=0' ) . '" width="' . $thumbnail_width . '" height="' . $thumbnail_height . '" itemprop="image" />
			</a>';


		} else {

			echo '<img src="' . esc_url( $thumbnail_url ) . '" class="post-thumbnail post-thumbnail-' . $position . '" alt="' . the_title_attribute( 'echo=0' ) . '" width="' . $thumbnail_width . '" height="' . $thumbnail_height . '" itemprop="image" />';

		}

	}


	function display_post_navigation() {

		if ( !is_single() )
			return false;

		if ( !$this->get_setting('show-single-post-navigation', true) )
			return false;

		if ( $this->get_setting('mode', 'default') == 'custom-query' )
			return false;

		if ( $this->get_setting('show-single-post-navigation-enable-tax', true))

			if( !$this->get_setting('show-single-post-navigation-tax' ))

				$enable_tax = 'category';  // the default
			else 
				$enable_tax = $this->get_setting( 'show-single-post-navigation-tax' );

		else 
			$enable_tax = '';


		echo '<div id="nav-below" class="loop-navigation single-post-navigation loop-utility loop-utility-below" itemscope itemtype="http://schema.org/SiteNavigationElement">';

			echo '<div class="nav-previous" itemprop="url">';
				previous_post_link('%link', '<span class="meta-nav">&larr;</span> %title', $this->get_setting('show-single-post-navigation-enable-tax'), ' ', $enable_tax);
			echo '</div>';

			echo '<div class="nav-next" itemprop="url">';
				next_post_link('%link', '%title <span class="meta-nav">&rarr;</span>', $this->get_setting('show-single-post-navigation-enable-tax'), ' ', $enable_tax);
			echo '</div>';

		echo '</div>';


	}


	function parse_meta($meta) {

		global $post, $authordata;

		$variables = array(
			'date',
			'modified_date',
			'time',
			'comments',
			'comments_no_link',
			'respond',
			'author',
			'author_no_link',
			'categories',
			'tags',
			'publisher',
			'publisher_img',
			'publisher_no_img',
			'edit'
		);

		foreach ( $variables as $variable ) {

			if ( strpos($meta, '%' . $variable . '%') === false )
				continue;

			switch ( $variable ) {

				case 'date':

					$date_format = $this->get_setting('date-format', 'wordpress-default');
					$date = ($date_format != 'wordpress-default') ? get_the_time($date_format) : get_the_date();

					$replacement['date'] = '<time class="entry-date published updated" itemprop="datePublished" datetime="' . get_the_time( 'c' ) . '">' . $date . '</time>';

				case 'modified_date':

					$date_format = $this->get_setting('date-format', 'wordpress-default');
					$date = ($date_format != 'wordpress-default') ? get_the_time($date_format) : get_the_modified_date();

					$replacement['modified_date'] = '<time class="entry-date published modified" itemprop="dateModified" datetime="' . get_the_time( 'c' ) . '">' . $date . '</time>';

				break;

				case 'time':

					$time_format = $this->get_setting('time-format', 'wordpress-default');
					$time = ($date_format != 'wordpress-default') ? get_the_time($time_format) : get_the_time();

					$replacement['time'] = '<time class="entry-time" datetime="' . get_the_time( 'c' ) . '">' . $time . '</time>';

				break;

				case 'comments':
				case 'comments_no_link':

					$comments_number = (int)get_comments_number($post->ID);

					if ( $comments_number === 0 ) 
						$comments_format = stripslashes($this->get_setting('comment-format-0', '%num% Comments'));
					elseif ( $comments_number == 1 ) 
						$comments_format = stripslashes($this->get_setting('comment-format-1', '%num% Comment'));
					elseif ( $comments_number > 1 ) 
						$comments_format = stripslashes($this->get_setting('comment-format', '%num% Comments'));

					$comments = str_replace('%num%', $comments_number, $comments_format);

					$replacement['comments'] = '<a href="' . get_comments_link() . '" title="' . sprintf(__('%s &ndash; Comments', 'padma'), the_title_attribute('echo=0')) . '" class="entry-comments">' . $comments . '</a>';
					$replacement['comments_no_link'] = $comments;

				break;

				case 'respond':

					$respond_format = stripslashes($this->get_setting('respond-format', 'Leave a comment!'));

					$replacement['respond'] = '<a href="' . get_permalink() . '#respond" title="' . sprintf(__('Respond to %s', 'padma'), the_title_attribute('echo=0')) . '" class="entry-respond">' . $respond_format . '</a>';

				break;


				case 'author':

					$replacement['author'] = '<span class="entry-author vcard">';
					$replacement['author'] .= '<a class="author-link fn nickname url" href="' . get_author_posts_url($authordata->ID) . '" title="' . sprintf(__('View all posts by %s', 'padma'), $authordata->display_name) . '">';

					$replacement['author'] .= '<span class="entry-author-name">' . $authordata->display_name . '</span>';
					$replacement['author'] .= '</a>';
					$replacement['author'] .= '</span>';

				break;


				case 'author_no_link':

					$replacement['author_no_link'] = $authordata->display_name;

				break;

				case 'categories':
					$replacement['categories'] = get_the_category_list(', ');
				break;

				case 'tags':
					$replacement['tags'] = (get_the_tags() != NULL) ? get_the_tag_list('<span class="tag-links" itemprop="keywords">','<span class="tag-sep">, </span>','</span>') : '';
				break;


				case 'publisher':

					$blog_id = (is_multisite()) ? get_current_blog_id(): 0;

					if(!has_custom_logo($blog_id))
						return;					

					$custom_logo_id = get_theme_mod( 'custom_logo' );
					$image = wp_get_attachment_image_src( $custom_logo_id , 'full' );

					$replacement['publisher'] = '<script type="application/ld+json">';
					$replacement['publisher'] .= '{';					
					$replacement['publisher'] .= '"@context": "http://schema.org/",';					
					$replacement['publisher'] .= '"@type": "Organization",';					
					$replacement['publisher'] .= '"url": "'.site_url().'",';					
					$replacement['publisher'] .= '"logo": "'.$image[0].'"';					
					$replacement['publisher'] .= '}';
					$replacement['publisher'] .= '</script>';

				break;



				case 'publisher_img':

					$blog_id = (is_multisite()) ? get_current_blog_id(): 0;

					if(!has_custom_logo($blog_id))
						return;					

					$custom_logo_id = get_theme_mod( 'custom_logo' );
					$image = wp_get_attachment_image_src( $custom_logo_id , 'full' );


					add_filter( 'get_custom_logo', function($html, $blog_id){
						return str_replace('itemprop="logo"', '', $html);
					}, 10, 2 );

					$replacement['publisher_img'] = '<div class="publisher-img" itemprop="publisher" itemscope itemtype="https://schema.org/Organization">';
					$replacement['publisher_img'] .= '<div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">';
					$replacement['publisher_img'] .= get_custom_logo();
					$replacement['publisher_img'] .= '<meta itemprop="url" content="'.$image[0].'">';
					$replacement['publisher_img'] .= '<meta itemprop="width" content="'.$image[1].'">';
					$replacement['publisher_img'] .= '<meta itemprop="height" content="'.$image[2].'">';
					$replacement['publisher_img'] .= '</div>';
					$replacement['publisher_img'] .= '<meta itemprop="name" content="'.$authordata->display_name.'">';
				    $replacement['publisher_img'] .= '</div>';	

				break;




				case 'publisher_no_img':

					$blog_id = (is_multisite()) ? get_current_blog_id(): 0;

					if(!has_custom_logo($blog_id))
						return;					

					$custom_logo_id = get_theme_mod( 'custom_logo' );
					$image = wp_get_attachment_image_src( $custom_logo_id , 'full' );

					$replacement['publisher_no_img'] = '<div class="publisher-no-img" itemprop="publisher" itemscope itemtype="https://schema.org/Organization">';
					$replacement['publisher_no_img'] .= '<div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">';
					//$replacement['publisher'] .= get_custom_logo();
					$replacement['publisher_no_img'] .= '<meta itemprop="url" content="'.$image[0].'">';
					$replacement['publisher_no_img'] .= '<meta itemprop="width" content="'.$image[1].'">';
					$replacement['publisher_no_img'] .= '<meta itemprop="height" content="'.$image[2].'">';
					$replacement['publisher_no_img'] .= '</div>';
					$replacement['publisher_no_img'] .= '<meta itemprop="name" content="'.$authordata->display_name.'">';
				    $replacement['publisher_no_img'] .= '</div>';					

				break;

				case 'edit':
					$replacement['edit'] = null;
				break;


			}

			$meta = str_replace('%' . $variable . '%', $replacement[$variable], $meta);

		}

		return apply_filters('padma_meta', $meta);

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
		if ( $custom_css_class = PadmaLayoutOption::get(get_the_id(), 'css-class', null, true) ) {

			$custom_css_classes = str_replace('  ', ' ', str_replace(',', ' ', esc_attr(strip_tags($custom_css_class))));

			$c = array_merge($c, array_filter(explode(' ', $custom_css_classes)));

		}

		$c[] = $this->get_setting('mode');	

		$c = join(' ', $c);

		return $c;

	}


	function more_link($more_link = null) {

		global $post;

		if ( !$this->get_setting('show-readmore', true) )
			return false;

		$more_text = $this->get_setting('read-more-text', 'Continue Reading');
		$more_link = '<a href="'. get_permalink($post->ID) . '" class="more-link">' . $more_text . '</a>';

		return apply_filters('padma_more_link', ' ' . $more_link);

	}


	function excerpt_more_link($excerpt) {

		return $excerpt . $this->more_link();

	}


	function filter_nofollow_links_in_post($text) {

		global $post;

		if ( !is_object($post) || empty($post->ID) || !PadmaSEO::is_seo_checkbox_enabled('nofollow', $post->ID) )
			return $text;

		preg_match_all("/<a.*? href=\"(.*?)\".*?>(.*?)<\/a>/i", $text, $links);
		$match_count = count($links[0]);

		for ( $i=0; $i < $match_count; $i++ ) {

			if ( !preg_match("/rel=[\"\']*nofollow[\"\']*/",$links[0][$i]) ) {

				preg_match_all("/<a.*? href=\"(.*?)\"(.*?)>(.*?)<\/a>/i", $links[0][$i], $link_text);

				$text = str_replace('>' . $link_text[3][0] . '</a>', ' rel="nofollow">' . $link_text[3][0] . '</a>', $text);

			}

		}

		return $text;

	}

}

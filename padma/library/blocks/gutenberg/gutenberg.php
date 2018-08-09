<?php

padma_register_block('PadmaGutenbergBlock', padma_url() . '/library/blocks/gutenberg');

class PadmaGutenbergBlock extends PadmaBlockAPI {
	
	
	public $id 				= 'gutenberg';	
	public $name 			= 'Gutenberg editor';		
	public $options_class 	= 'PadmaGutenbergBlockOptions';
	public $description 	= 'Enables gutenberg editor into a block.';
			
	
	function init() {

        if(!function_exists('gutenberg_pre_init')){
			return;
		}
		
	}

	function content($block) {

		if(!function_exists('gutenberg_pre_init')){
			
			echo "<h1>Gutenberg was not found, you can install the plugin from <a href='https://wordpress.org/plugins/gutenberg/'>https://wordpress.org/plugins/gutenberg/</a></h1>";
			return;
		}

		global $post;

		/*
		add_action( 'admin_enqueue_scripts', 'gutenberg_editor_scripts_and_styles' );
		add_filter( 'screen_options_show_screen', '__return_false' );
		add_filter( 'admin_body_class', 'gutenberg_add_admin_body_class' );
		*/

		/**
		 * Remove the emoji script as it is incompatible with both React and any
		 * contenteditable fields.
		 */
		//remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );

		require_once(ABSPATH . '/wp-admin/includes/screen.php');
		require_once(ABSPATH . '/wp-admin/includes/class-wp-screen.php');
		set_current_screen();
		debug(WP_Screen::get());
		/*
		require_once(ABSPATH . '/wp-admin/includes/template.php');
		
		the_gutenberg_project();
		*/

		/*
		add_action( 'admin_enqueue_scripts', 'gutenberg_editor_scripts_and_styles' );
		add_filter( 'screen_options_show_screen', '__return_false' );
		add_filter( 'admin_body_class', 'gutenberg_add_admin_body_class' );
		*/

		/**
		 * Remove the emoji script as it is incompatible with both React and any
		 * contenteditable fields.
		 */
		//remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );

		
		//require_once(ABSPATH . '/wp-admin/includes/theme.php');


		//global $post_type_object;

		//do_action( 'admin_enqueue_scripts', $hook_suffix );

		/** This action is documented in wp-admin/admin-header.php */
		//do_action( "admin_print_styles-$hook_suffix" );

		/** This action is documented in wp-admin/admin-header.php */
		//do_action( 'admin_print_styles' );

		/** This action is documented in wp-admin/admin-header.php */
		//do_action( "admin_print_scripts-$hook_suffix" );

		/** This action is documented in wp-admin/admin-header.php */
		//do_action( 'admin_print_scripts' );

		/** This action is documented in wp-admin/admin-header.php */
		//do_action( "admin_head-$hook_suffix" );

		/** This action is documented in wp-admin/admin-header.php */
		//do_action( 'admin_head' );

		/** This action is documented in wp-admin/admin-footer.php */
		//do_action( 'admin_footer', $hook_suffix );

		/** This action is documented in wp-admin/admin-footer.php */
		//do_action( "admin_print_footer_scripts-$hook_suffix" );

		/** This action is documented in wp-admin/admin-footer.php */
		//do_action( 'admin_print_footer_scripts' );

		$content_block_display = new PadmaGutenbergBlockDisplay($block);
		$content_block_display->display();
		
	}
	
}

class PadmaGutenbergBlockDisplay {
		
	var $count = 0;		
	var $query = array();
	
	
	function __construct($block) {
		
		$this->block = $block;
		
		/* Bring in the WordPress pagination variable. */
		$this->paged = get_query_var('paged') ? get_query_var('paged') : 1;
		
		$this->add_hooks();

	}
		
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
				
				$loopbuddy_query 	= $this->get_setting('loopbuddy-query', -1);
				$loopbuddy_layout 	= $this->get_setting('loopbuddy-layout', -1);
							
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

				if ( is_a( $this->query, 'SWP_Query' ) ) {

					$swp_engine = $this->get_setting( 'swp-engine' );
					$swp_search = isset( $_REQUEST[ 'swpquery_' . $swp_engine ] ) ? sanitize_text_field( $_REQUEST[ 'swpquery_' . $swp_engine ] ) : '';
					$have_posts = ! empty( $swp_search ) && ! empty( $this->query->posts );

				} else if ( is_a($this->query, 'WP_Query') ) {

					$have_posts = $this->query->have_posts();

				} else {

					$have_posts = false;

				}

				if ( !$have_posts && ( is_a( $this->query, 'SWP_Query' ) || ( is_search() && $this->get_setting( 'mode', 'default' ) == 'default' ) ) ) {

					echo '<div class="entry-content">';
						echo apply_filters('padma_search_no_results', __('<p>Sorry, there was no content that matched your search.</p>', 'padma'));
					echo '</div>';
					
				}

				if ( is_a( $this->query, 'SWP_Query' ) ) {

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

						$this->display_entry( array( 'count' => $this->count ) ) ;

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


		if ( is_a( $this->query, 'SWP_Query' ) ) {

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
				$return .= apply_filters('padma_category_title', sprintf(__('Category Archives: %s', 'padma'), '<span>' . single_cat_title('', false) . '</span>'));
			$return .= '</h1>';
			
			$category_description = category_description();
			if ( !empty($category_description) )
				$return .= apply_filters('padma_category_archive_meta', '<div class="archive-meta category-archive-meta">' . $category_description . '</div>');
			
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

					if ($this->get_setting('offset', 0) >= 1 && $query_options['paged'] > 1){
						$query_options['offset'] = $this->get_setting('offset', 0) + $this->get_setting('number-of-posts', 10) * ($query_options['paged'] - 1);
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
			
			echo '<article id="post-' . $post_id . '" class="' . $post_class . '" itemscope itemtype="http://schema.org/' . apply_filters('padma_entry_schema', $schema_itemtype, $post_type) . '">';

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

					echo $entry_meta_above;

					if ( $this->get_setting( 'show-titles', true ) || $entry_meta_above ) {
						echo '</header>';
					}

					$this->display_thumbnail($post, 'above-content');

					$this->display_entry_content($args);

					$this->display_thumbnail( $post, 'below-content' );

					echo $entry_utility_below;

					do_action('padma_entry_close', $args);			

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
			
			} elseif ( is_a( $this->query, 'SWP_Query' ) || is_search() || $this->paged > 1 ) {
				
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

				the_excerpt();

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
			if ( is_a($this->query, 'SWP_Query') ) {

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

		if ( !has_post_thumbnail() || !$this->get_setting('show-post-thumbnails', true) || !apply_filters('padma_featured_image_src', wp_get_attachment_image_src(get_post_thumbnail_id(), 'full')) )
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
				$thumbnail_width = ( $this->get_setting( 'enable-column-layout' ) && ! ( is_singular() && $this->get_setting( 'mode', 'default' ) == 'default' ) ) ? PadmaGutenbergBlock::get_column_width($this->block) : PadmaBlocksData::get_block_width($this->block);
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

			} else {

				$thumb_link = get_attachment_link( get_post_thumbnail_id() );

			}

			echo '<a href="' . $thumb_link . '" class="post-thumbnail post-thumbnail-' . $position . '">
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
			'time',
			'comments',
			'comments_no_link',
			'respond',
			'author',
			'author_no_link',
			'categories',
			'tags',
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
				case 'author_no_link':

					$replacement['author'] = '<span class="entry-author vcard" itemprop="author" itemscope itemtype="http://schema.org/Person"><a class="author-link fn nickname url" href="' . get_author_posts_url($authordata->ID) . '" title="' . sprintf(__('View all posts by %s', 'padma'), $authordata->display_name) . '" itemprop="url"><span class="entry-author-name" itemprop="name">' . $authordata->display_name . '</span></a></span>';
					$replacement['author_no_link'] = $authordata->display_name;

				break;

				case 'categories':
					$replacement['categories'] = get_the_category_list(', ');
				break;

				case 'tags':
					$replacement['tags'] = (get_the_tags() != NULL) ? get_the_tag_list('<span class="tag-links" itemprop="keywords">','<span class="tag-sep">, </span>','</span>') : '';
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

class PadmaGutenbergBlockOptions extends PadmaBlockOptionsAPI {
	
	
	public $tab_notices = array(
		'mode' => 'The Gutenberg block is extremely versatile.  If the default mode is selected, it will do what you expect it to do.  For example, if you add this on a page, it will display that page\'s content.  If you add it on the Blog Index layout, it will list the posts like a normal blog template and if you add this box on a category layout, it will list posts of that category.  If you wish to change what the content block displays, change the mode to <em>Custom Query</em> and use the settings in the <em>Query Filters</em> tab.',
		'query-setup' => 'For more control over queries and how the query is displayed, Padma works perfectly out-of-the-box with <a href="http://pluginbuddy.com/purchase/loopbuddy/" target="_blank">LoopBuddy</a>.',
		'meta' => '
			<p>The entry meta is the information that appears below the post title and below the post content.  By default, it will contain information about the entry author, the categories, and comments.</p>
			<p><strong>Available Variables:</strong></p>
			<p>%date% &bull; %time% &bull; %comments% &bull; %comments_no_link% &bull; %respond% &bull; %author% &bull; %author_no_link% &bull; %categories% &bull; %tags%</p>
		'
	);
	
	
	public $tabs = array(
		'mode' 				=> 'Mode',
		'query-filters' 	=> 'Query Filters',
		'display' 			=> 'Display',
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

			'fetch-page-gutenberg' => array(
				'type' => 'select',
				'name' => 'fetch-page-gutenberg',
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
				'callback' => 'reloadBlockOptions()'
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

			
			'show-readmore' => array(
				'type' => 'checkbox',
				'name' => 'show-readmore',
				'label' => 'Show Read More',
				'default' => true,
				'tooltip' => 'Show and hide the continue reading or read more text/button.'
			),
			
			'entry-gutenberg-display' => array(
				'type' => 'select',
				'name' => 'entry-gutenberg-display',
				'label' => 'Entry Content Display',
				'tooltip' => 'The entry content is the actual body of the entry.  This is what you enter in the rich text area when creating an entry (post or page).  When set to normal, Padma will determine if full entries or excerpts should be displayed based off of the <em>Featured Posts</em> setting and what page is being displayed.<br /><br /><strong>Tip:</strong> Set this to <em>Hide Entry Content</em> to create a simple listing of posts.',
				'default' => 'normal',
				'options' => array(
					'normal' => 'Normal',
					'full-entries' => 'Show Full Entries',
					'excerpts' => 'Show Excerpts',
					'hide' => 'Hide Entry Content'
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
				'default' => true
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
					'left-gutenberg' => 'Left of Content',
					'right-gutenberg' => 'Right of Content',
					'above-title' => 'Above Title',
					'above-gutenberg' => 'Above Content',
					'below-gutenberg' => 'Below Content'
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
		
	}
	
	
	function get_pages() {
		
		$page_options = array('&ndash; Do Not Fetch &ndash;');
		
		$page_select_query = get_pages();
		
		foreach ($page_select_query as $page)
			$page_options[$page->ID] = $page->post_title;
		
		return $page_options;
		
	}

	
	function get_categories() {
		
		$category_options = array();
		
		$categories_select_query = get_categories();
		
		foreach ($categories_select_query as $category)
			$category_options[$category->term_id] = $category->name;

		return $category_options;
		
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
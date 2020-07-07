<?php

namespace Padma;
class PadmaLayout {


	public static $sep = '||';


	public static function init() {

		add_action('padma_flush_cache', array(__CLASS__, 'clear_status_transient'));

	}


	public static function clear_status_transient() {

		delete_transient('pu_customized_layouts_template_' . PadmaOption::$current_skin);
		delete_transient('pu_layouts_with_templates_template_' . PadmaOption::$current_skin);

	}


	public static function format_old_id($old_id) {

		$layout_fragments = explode('-', $old_id);
		$last_layout_fragment = end($layout_fragments);

		/* Don't touch template IDs */
		if ( strpos($old_id, 'template-') === 0 )
			return $old_id;

		/* If only a numeric ID is provided then turn it into new format */
		if ( is_numeric($old_id) || is_numeric($last_layout_fragment) ) {

			$post_id = is_numeric($old_id) ? $old_id : $last_layout_fragment;

			$post = get_post($post_id);

			if ( $post ) {

				return implode(self::$sep, array(
					'single',
					$post->post_type,
					$post_id
				));

			}

		}

		/* Replace underscores with separator */
		$old_id = str_replace(array(
			'single_',
			'archive_',
			'post_type_',
			'post_tag_'
		), array(
			'single' . self::$sep,
			'archive' . self::$sep,
			'post_type' . self::$sep,
			'post_tag' . self::$sep
		), $old_id);

		/* Change all hyphens to separator */
		return str_replace('-', self::$sep, $old_id);

	}


	/**
	 * Check if layout exists via PadmaLayout::get_name()
	 **/
	public static function exists($layout_id) {

		$name = self::get_name($layout_id);

		if ( !$name || $name == '(No Title)' || strpos($name, '(Unregistered Post Type: ') === 0 )
			return false;

		return true;

	}


	/**
	 * Returns current layout
	 * 
	 * @return mixed
	 **/
	public static function get_current() {

		//If the user is viewing the site through the iframe and the mode is set to Layout, then display that exact layout.
		if ( padma_get('ve-layout') && (PadmaRoute::is_visual_editor_iframe() || PadmaRoute::is_visual_editor()) ) 
			return urldecode(padma_get('ve-layout'));

		$current_hierarchy = self::get_current_hierarchy();

		return apply_filters('padma_current_layout', end($current_hierarchy));

	}


	/**
	 * Traverses up the hierarchy tree to figure out which layout is being used.
	 * 
	 * @return mixed
	 **/
	public static function get_current_in_use($visual_editor_force = false) {

		//If the user is viewing the site through the iframe and the mode is set to Layout, then display that exact layout.
			if ( padma_get('ve-layout') && !$visual_editor_force ) {

                if ( PadmaRoute::is_visual_editor_iframe('grid') || (PadmaRoute::is_visual_editor() && padma_get('visual-editor-mode') == 'grid') ) {
                    return padma_get('ve-layout');
                }

                if ( PadmaRoute::is_visual_editor_iframe('design') ) {

                    if ( strpos(padma_get('ve-layout'), 'template-') === 0 || padma_get('ve-layout-customized') == 'true' ) {

                        return padma_get('ve-layout');

                    }

                }

			} 

		//Get hierarchy
		$hierarchy = array_reverse(self::get_current_hierarchy());

		//Loop through entire hierarchy to find which one is customized or has a template
		foreach ( $hierarchy as $layout ) {

			$status = self::get_status($layout);

			//If the layout isn't customized or using a template, skip to next, otherwise we return the current layout in the next line.
			if ( $status['customized'] === false && $status['template'] === false )
				continue;

			//If the layout has a template assigned to it, use the template.  Templates will take precedence over customized status.
			if ( $status['template'] )
				return 'template-' . $status['template'];

			//If it's a customized layout, then use the layout itself after making sure there are blocks on the layout
			if ( $status['customized'] )
				return $layout;

		}

		//If there's still not a customized layout, loop through the top-level layouts and find the first one that's customized.
		$top_level_layouts = array(
			'index',
			'single',
			'archive',
			'four04'
		);

		if ( get_option('show_on_front') == 'page' )
			$top_level_layouts[] = 'front_page';

		foreach ( $top_level_layouts as $top_level_layout ) {

			$status = self::get_status($top_level_layout);

			if ( $status['customized'] === false && $status['template'] === false )
				continue;

			//If the layout has a template assigned to it, use the template.  Templates will take precedence over customized status.
			if ( $status['template'] )
				return 'template-' . $status['template'];

			//If it's a customized layout and the layout has blocks, then use the layout itself
			if ( $status['customized'] && count(PadmaBlocksData::get_blocks_by_layout($top_level_layout)) > 0 )
				return $top_level_layout;

		}

		//If there STILL isn't a customized layout, just return the top level of the current layout.
		return apply_filters('padma_current_layout_in_use', end($hierarchy));

	}


	/**
	 * Returns name of the current layout being viewed.
	 * 
	 * @return string
	 **/
	public static function get_current_name() {

		return self::get_name(self::get_current());

	}


	/**
	 * Returns the current hierarchy. 
	 * 
	 * @return array
	 **/
	public static function get_current_hierarchy() {

		/* WPML Front page/Blog compatibility */
		global $sitepress;

		if ( !empty($GLOBALS['padma_current_hierarchy']) ) {
			return apply_filters('padma_current_layout_hierarchy', $GLOBALS['padma_current_hierarchy']);
		}

        if ( PadmaRoute::is_visual_editor() && padma_get( 've-layout' ) ) {
            return explode(PadmaLayout::$sep, urldecode(padma_get('ve-layout')));
        }

		$current_layout = array();
		$queried_object = get_queried_object();

		//Now the fun begins
		if ( is_home() || ( get_option( 'show_on_front' ) == 'posts' && is_front_page() ) ) {

			$current_layout[] = 'index';

			if ( method_exists( $sitepress, 'get_current_language' ) ) {
				$current_layout[] = 'index' . self::$sep . 'wpml_' . $sitepress->get_current_language();
			}

		} elseif ( is_front_page() && ! is_home() ) {

			$current_layout[] = 'front_page';

			if ( method_exists( $sitepress, 'get_current_language' ) ) {
				$current_layout[] = 'front_page' . self::$sep . 'wpml_' . $sitepress->get_current_language();
			}

		} elseif ( is_singular() ) {

			$post = $queried_object;
			$post_type = get_post_type_object($post->post_type);

			$current_layout[] = 'single';

			if ( $post_type->name )
				$current_layout[] = 'single' . self::$sep . $post_type->name;

			$posts = array(
				$post->ID
			);

			while ( $post->post_parent != 0 ) {

				$post = get_post($post->post_parent);
				$posts[] = $post->ID;

			}

			foreach ( array_reverse($posts) as $post_id )
				if ( $post_type->name && $post_id )
					$current_layout[] = 'single' . self::$sep . $post_type->name . self::$sep . $post_id;

		} elseif ( is_archive() || is_search() ) {

			$current_layout[] = 'archive';

			if ( is_date() ) {

				$current_layout[] = 'archive' . self::$sep . 'date';

			} elseif ( is_author() ) {

				$current_layout[] = 'archive' . self::$sep . 'author';
				$current_layout[] = 'archive' . self::$sep . 'author' . self::$sep . $queried_object->ID;

			} elseif ( is_category() ) {

				$category = $queried_object;
				$ancestor_categories = array();

				$current_layout[] = 'archive' . self::$sep . 'category';

				/* Ancestor categories */
					while ( $category->category_parent != 0 ) {
						$category = get_category($category->category_parent);
						$ancestor_categories[] = $category->term_id;
					}

					foreach ( array_reverse($ancestor_categories) as $ancestor_category_id )
						$current_layout[] = 'archive' . self::$sep . 'category' . self::$sep . $ancestor_category_id;

				/* Original queried category */
				$current_layout[] = 'archive' . self::$sep . 'category' . self::$sep . $queried_object->term_id;

			} elseif ( is_search() ) {

				$current_layout[] = 'archive' . self::$sep . 'search';

			} elseif ( is_tag() ) {

				$current_layout[] = 'archive' . self::$sep . 'post_tag';
				$current_layout[] = 'archive' . self::$sep . 'post_tag' . self::$sep . $queried_object->term_id;

			} elseif ( is_tax() ) {

				$current_layout[] = 'archive' . self::$sep . 'taxonomy';
				$current_layout[] = 'archive' . self::$sep . 'taxonomy' . self::$sep . $queried_object->taxonomy;
				$current_layout[] = 'archive' . self::$sep . 'taxonomy' . self::$sep . $queried_object->taxonomy . self::$sep . $queried_object->term_id;

			} elseif ( is_post_type_archive() ) {

				$post_type = get_query_var( 'post_type' );

				if ( is_array( $post_type ) ) {
					$post_type = reset( $post_type );
				}

				$post_type_obj = get_post_type_object( $post_type );

				$current_layout[] = 'archive' . self::$sep . 'post_type';
				$current_layout[] = 'archive' . self::$sep . 'post_type' . self::$sep . $post_type_obj->name;

			}

		} elseif ( is_404() ) {

			$current_layout[] = 'four04';

			if ( method_exists( $sitepress, 'get_current_language' ) ) {
				$current_layout[] = 'four04' . self::$sep . 'wpml_' . $sitepress->get_current_language();
			}

		}		

		//I think we're finally done.
		if ( count($current_layout) ) {
			$GLOBALS['padma_current_hierarchy'] = $current_layout;
		}

		return apply_filters('padma_current_layout_hierarchy', $current_layout);

	}


	/**
	 * Returns friendly name of the layout specified.
	 * 
	 * @return string
	 **/
	public static function get_name($layout, $retry = false) {

		if ( !$layout )
			return null;

		$layout_parts = explode(self::$sep, $layout);
		$id = end($layout_parts);

		if ( is_numeric($layout_parts[0]) )
			return get_the_title($id) ? stripslashes(get_the_title($id)) : '(No Title)';

		switch ( $layout_parts[0] ) {

			case 'front_page':

				if ( isset($layout_parts[1]) && strpos($layout_parts[1], 'wpml') !== false ) {

					$language_id = str_replace('wpml_', '', $layout_parts[1]);

					return 'Front Page (WPML: ' . strtoupper($language_id) . ')';

				}

				return 'Front Page';

			break;

			case 'index':

				if ( isset( $layout_parts[1] ) && strpos( $layout_parts[1], 'wpml' ) !== false ) {

					$language_id = str_replace( 'wpml_', '', $layout_parts[1] );

					return 'Blog Index (WPML: ' . strtoupper( $language_id ) . ')';

				}

				return 'Blog Index';
			break;

			case 'single':
				if ( $id == 'single' )
					return 'Single';

				if ( is_numeric($id) )
					return get_the_title($id) ? stripslashes(get_the_title($id)) : '(No Title)';

				//If everything else hasn't triggered, then it's a post type
				$id = str_replace('single' . self::$sep, '', $layout);
				$post_type = get_post_type_object($id);

				if ( !is_object($post_type) )
					return '(Unregistered Post Type: ' . $id . ')';

				return stripslashes($post_type->labels->singular_name);
			break;

			case 'archive':
				if ( $id == 'archive' )
					return 'Archive';

				switch($layout_parts[1]) {

					case 'category':
						if ( $id == 'category' )
							return 'Category';

						$term = get_term($id, 'category');

						return $term->name ? stripslashes($term->name) : '(No Title)';
					break;

					case 'search':
						return 'Search';
					break;

					case 'date':
						return 'Date';
					break;

					case 'author':
						if ( $id == 'author' )
							return 'Author';

						$user_data = get_userdata($id);

						return stripslashes($user_data->display_name);
					break;

					case 'post_tag':
						if ( $id == 'post_tag' ) 
							return 'Post Tag';

						$term = get_term($id, 'post_tag');

						return $term->name ? stripslashes($term->name) : '(No Title)';
					break;

					case 'taxonomy':
						if ( $id == 'taxonomy' ) 
							return 'Taxonomy';

						$taxonomy_fragments = explode(self::$sep, str_replace('archive' . self::$sep . 'taxonomy' . self::$sep, '', $layout));

						if ( is_numeric(end($taxonomy_fragments)) ) {

							$term_id = array_pop($taxonomy_fragments);

							$term = get_term($term_id, implode(self::$sep, $taxonomy_fragments));

							return isset($term->name) ? $term->name : '(No Title)';

						} elseif ( $taxonomy = get_taxonomy(implode(self::$sep, $taxonomy_fragments)) ) {

							return $taxonomy->labels->singular_name ? stripslashes($taxonomy->labels->singular_name) : '(No Title)';

						}
					break;

					case 'post_type':
						if ( $id == 'post_type' )
							return 'Post Type';

						//If everything else hasn't triggered, then it's a post type
						$id = str_replace('archive' . self::$sep . 'post_type' . self::$sep, '', $layout);
						$post_type = get_post_type_object($id);

						if ( !is_object($post_type) )
							return null;

						return stripslashes($post_type->labels->singular_name);
					break;

					case 'post_format':
						if ( $id == 'post_format' )
							return 'Post Format';

						$term = get_term($id, 'post_format');

						return stripslashes($term->name);
					break;

				}

			break;

			case 'four04':
				return '404 Layout';
			break;

		}

		/* Template names */
		if ( strpos($layout_parts[0], 'template-') === 0 ) {

			$templates = self::get_templates();
			$template_id = str_replace('template-', '', $layout_parts[0]);

			if ( isset($templates[$template_id]) )
				return stripslashes($templates[$template_id]);
			else
				return null;

		}


		/* If it's no bueno, give it one more shot by replacing underscores with hyphens */
		if ( !$retry ) {

			return self::get_name(str_replace('_', '-', $layout), true);

		}

		return false;

	}


	public static function get_layout_parents_names($layout) {

		$layout_id_fragments = explode( PadmaLayout::$sep, $layout );

		$name_prefix = '';

		if ( count( $layout_id_fragments ) > 1 ) {

			$top_level_names = array(
				'front_page' => 'Front Page',
				'index' => 'Blog Index',
				'single' => 'Single',
				'archive' => 'Archive',
				'four04' => '404 Layout'
			);

			$name_prefix = strtr( $layout_id_fragments[0], $top_level_names ) . ' &rsaquo; ';

			if ( $layout_id_fragments[0] == 'archive' ) {

				$taxonomy_slug = false;

				if ( $layout_id_fragments[1] == 'taxonomy' ) {

					if ( count( $layout_id_fragments ) >= 3 ) {
						$name_prefix .= 'Taxonomy &rsaquo; ';

						if ( count( $layout_id_fragments ) >= 4 ) {
							$taxonomy_slug = $layout_id_fragments[2];
						}
					}

				} else {
					$taxonomy_slug = $layout_id_fragments[1];
				}

				if ( $taxonomy_slug ) {

					$taxonomy_object = get_taxonomy( $taxonomy_slug );

					if ( $taxonomy_object ) {
						$name_prefix .= $taxonomy_object->labels->singular_name . ' &rsaquo; ';
					} else {
						$name_prefix .= $taxonomy_slug  . ' (Invalid Taxonomy) &rsaquo; ';
					}

				}

			} else if ( $layout_id_fragments[0] == 'single' ) {

				$post_type_object = get_post_type_object( $layout_id_fragments[1] );

				$name_prefix .= $post_type_object->labels->singular_name . ' &rsaquo; ';

			}

		}

		return $name_prefix;

	}



	/**
	 * Gets the status of the layout.  This will tell if it's customized, using a template, or none of the previous mentioned.
	 * 
	 * @return string
	 **/
	public static function get_status($layout, $include_post_status = false) {

		$layout_parts = explode(self::$sep, $layout);
		$layout_end_part = end($layout_parts);

		/* Get the customized transient */
			$transient_id_customized_layouts = 'pu_customized_layouts_template_' . PadmaOption::$current_skin;
			$customized_layouts = get_transient( $transient_id_customized_layouts );

			if ( !is_array($customized_layouts) ) {
				$customized_layouts = self::set_layout_status_customized_transient();
			}

		/* Get the templates status transient */
			$transient_id_layouts_with_templates = 'pu_layouts_with_templates_template_' . PadmaOption::$current_skin;
			$layouts_with_templates = get_transient($transient_id_layouts_with_templates);

			if ( !is_array($layouts_with_templates) ) {
				$layouts_with_templates = self::set_layout_status_templates_transient();
			}

		$customized = ( in_array($layout, $customized_layouts) ) ? true : false;

		$template = false;

		/* Get the template */
			$possible_template = ( $layout_parts[0] == 'single' && is_numeric($layout_end_part) ) ? padma_get($layout_end_part, $layouts_with_templates) : padma_get($layout, $layouts_with_templates);

			if ( $possible_template && self::template_exists($possible_template) )
				$template = str_replace('template-', '', $possible_template);
		/* End getting template */

		$status = array(
			'customized' => $customized,
			'template' => $template
		);		

		/* If set to include post status and this is a single layout, fetch it */
		if ( $include_post_status && $layout_parts[0] == 'single' && is_numeric($layout_end_part) ) {

			/* Change status IDs to friendly statuses */
			$possible_statuses = array('publish', 'pending', 'draft', 'future', 'private');
			$friendly_status_names = array('Published', 'Pending Review', 'Draft', 'Scheduled', 'Private');

			$status['post_status'] = str_replace($possible_statuses, $friendly_status_names, get_post_status($layout_end_part));

		}

		/* Check if the layout is set to the blog page or the homepage.  If so return false on both. */
			if ( is_numeric($layout_end_part) && $layout_parts[0] == 'single' && $layout_parts[1] == 'page' ) {

				if ( $layout_end_part == get_option('page_on_front') || $layout_end_part == get_option('page_for_posts') ) {

					return array(
						'customized' => false,
						'template' => false
					);

				}

			} 

		return $status;

	}


	public static function set_layout_status_customized_transient() {

		$transient_id_customized_layouts = 'pu_customized_layouts_template_' . PadmaOption::$current_skin;

		global $wpdb;

		$customized_layouts = array_unique( $wpdb->get_col( $wpdb->prepare( "SELECT layout FROM $wpdb->pu_blocks WHERE template = '%s'", PadmaOption::$current_skin ) ) );

		set_transient( $transient_id_customized_layouts, $customized_layouts );

		return $customized_layouts;

	}


	public static function set_layout_status_templates_transient() {

		$transient_id_layouts_with_templates = 'pu_layouts_with_templates_template_' . PadmaOption::$current_skin;

		global $wpdb;

		$templated_layouts_pu_meta = $wpdb->get_results( $wpdb->prepare( "SELECT layout, meta_value FROM $wpdb->pu_layout_meta WHERE meta_key = '%s' AND meta_value <> '' AND template = '%s'", 'template', PadmaOption::$current_skin ) );
		$templated_layouts_wp_meta = $wpdb->get_results( $wpdb->prepare( "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = '%s' AND meta_value <> ''", '_pu_|template=' . PadmaOption::$current_skin . '|_template' ) );

		$layouts_with_templates = array();

		foreach ( array_merge( $templated_layouts_pu_meta, $templated_layouts_wp_meta ) as $templated_layout ) {

			$template_id = isset( $templated_layout->layout ) ? $templated_layout->layout : $templated_layout->post_id;

			$layouts_with_templates[ $template_id ] = $templated_layout->meta_value;

		}

		set_transient( $transient_id_layouts_with_templates, $layouts_with_templates );

		return $layouts_with_templates;

	}


	public static function is_customized($layout) {

		$layout_status = self::get_status($layout);

		return $layout_status['customized'] && !$layout_status['template'] ? true : false;

	}


	/** 
	 * Simple function to query for all Padma layout templates from the database.
	 * 
	 * @return array
	 **/
	public static function get_templates() {

		$templates = PadmaSkinOption::get('list', 'templates', array());

		return $templates;

	}


	public static function template_exists($id) {

		$templates = PadmaSkinOption::get('list', 'templates', array());

		return isset($templates[$id]);

	}


	public static function add_template($template_name = null) {

		$templates = PadmaSkinOption::get('list', 'templates', array());
		$last_template_id = PadmaSkinOption::get('last-id', 'templates', 0);

		/* These  two variables be used for when a blocks/wrappers imported ID is different than the one that it ends up with... e.g. skin importing to line up instances */
		$block_id_translations = array();
		$wrapper_id_translations = array();

		/* Build name */
			$id = $last_template_id + 1;
			$template_name = $template_name ? $template_name : 'Template ' . $id;

		/* Add template to templates array so it can be sent to DB */
			$templates[$id] = $template_name;

		/* Send array to DB */
			PadmaSkinOption::set('list', $templates, 'templates');
			PadmaSkinOption::set('last-id', $id, 'templates');

		return array(
			'id' => $id, 
			'name' => $template_name
		);

	}


	/**
	 * Deletion method that will handle deleting blocks, wrappers, design editor instances, and removing any rows in wp_options
	 * 
	 * @param string ID of layout being deleted
	 * @param bool Whether or not to delete all of the layout options pertaining to this layout or only ones that belong to the active skin
	 **/
	public static function delete_layout($layout_id, $skin_only = true) {

		//Delete the blocks for the page/post.  This will also delete the block instances */
		PadmaBlocksData::delete_by_layout($layout_id);

		//Delete wrappers and wrapper instances
		PadmaWrappersData::delete_by_layout($layout_id);

		//Delete the layout options for this layout
		if ( $skin_only ) {
			PadmaLayoutOption::delete_all_from_layout($layout_id);
		} else {
			PadmaLayoutOption::delete_all_from_layout($layout_id, true);
		}

	}


	/**
	 * Get the **BEST** URL for a layout ID
	 **/
	public static function get_url($layout) {

		$layout_fragments = explode(self::$sep, $layout);

		switch ( $layout_fragments[0] ) {

			/* Blog Index */
			case 'index':

				if ( get_option('show_on_front') == 'page' && get_option('page_for_posts') )
					return get_permalink(get_option('page_for_posts'));

				return home_url();

			break;


			/* Front Page */
			case 'front_page':

				return home_url();

			break;


			/* Singles */
			case 'single':

				/* If an ID is provided, go straight to it */
				if ( isset($layout_fragments[2]) && $permalink = get_permalink($layout_fragments[2]) )
					return $permalink;

				/* Otherwise, go to the post type (force post if post type isn't present... e.g. layout is just "single") */
				$post_type = isset($layout_fragments[1]) ? $layout_fragments[1] : array(
					'post',
					'page'
				);

				$query = get_posts(array(
					'numberposts' => 3,
					'post_type' => $post_type
				));

				foreach ( $query as $query_post ) {

					if ( empty( $query_post->ID ) )
						continue;

					if ( !$permalink = get_permalink( $query_post->ID ) )
						continue;

					if ( $query_post->ID == get_option( 'page_for_posts' ) || $query_post->ID == get_option( 'page_on_front' ) )
						continue;

					return $permalink;

				}

			break;


			/* Archives */
			case 'archive':

				$type = isset($layout_fragments[1]) ? $layout_fragments[1] : 'category';

				switch ( $type ) {

					case 'category':

						/* Category ID provided */
						if ( isset( $layout_fragments[2] ) && is_numeric( $layout_fragments[2] ) ) {

							$cat = $layout_fragments[2];

						/* No category ID provided, get one with posts */
						} else {

							$categories = get_terms( 'category', array(
								'orderby' => 'count',
								'order' => 'desc',
								'hide_empty' => true
							) );

							if ( count( $categories ) ) {
								$cat = $categories[0]->term_id;
							} else {
								$cat = 0;
							}

						}

						return home_url( '?cat=' . $cat );

					break;

					case 'date':

						return home_url('?m=' . date('Y'));

					break;

					case 'author':

						/* Author ID Provided */
						if ( isset($layout_fragments[2]) && is_numeric($layout_fragments[2]) ) {

							$author_id = $layout_fragments[2];

						/* Author ID not provided, use logged in user */
						} else {

							$current_user = wp_get_current_user();
							$author_id = $current_user->ID;

						}

						return home_url('?author=' . $author_id);

					break;

					case 'search':

						/* Provide a word that will likely pull up some content */
						return home_url('?s=and');

					break;

					case 'post_type':

						if ( isset($layout_fragments[2]) && $post_type = $layout_fragments[2] )
							return home_url('?post_type=' . $post_type);

					break;

					case 'post_tag':

						/* Tag provided */
						if ( isset( $layout_fragments[2] ) && is_numeric( $layout_fragments[2] ) ) {

							$tag = get_term($layout_fragments[2], 'post_tag');

						/* No tag provided, get one with posts */
						} else {

							$tags = get_terms( 'post_tag', array(
								'orderby' => 'count',
								'order' => 'desc',
								'hide_empty' => true
							) );

							if ( count( $tags ) ) {
								$tag = $tags[0];
							} else {
								return home_url();
							}

						}

						return home_url( '?tag=' . $tag->slug );

					break;

					case 'taxonomy':

						/* Taxonomy provided */
						if ( isset($layout_fragments[2]) && $tax = $layout_fragments[2] ) {

							/* Term Provided */
							if ( isset($layout_fragments[3]) ) {

								$term = get_term($layout_fragments[3], $tax);
								$term_slug = isset($term->slug) ? $term->slug : null;

							/* No term provided */
							} else {

								$terms = get_terms($tax, array(
									'orderby' => 'count',
									'order' => 'desc',
									'hide_empty' => true
								));

								if ( !empty($terms[0]->slug) )
									$term_slug = $terms[0]->slug;

							}

							if ( !empty($tax) && !empty($term_slug) )
							return home_url('?' . $tax . '=' . $term_slug);

						}

					break;

				}

			break;


			/* 404 */
			case 'four04':

				return home_url('404trigger-' . rand(100, 99999));

			break;

		} /* End $layout switch */

		/* Catch All Default */
		return home_url();

	}

}
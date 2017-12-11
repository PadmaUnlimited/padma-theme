<?php
class BloxLayoutSelector {


	private static $query_limit = 30;


	public static function format_layout_for_knockout( $id, $info ) {

		$layout_status = BloxLayout::get_status( $id );
		$post_status   = blox_get( 'post_status', $layout_status );

		$layout = array(
			'id'           => $id,
			'name'         => blox_get( 'name', $info ) ? blox_get( 'name', $info ) : BloxLayout::get_name( $id ),
			'url'          => blox_get( 'url', $info ) ? blox_get( 'url', $info ) : BloxLayout::get_url( $id ),
			'customized'   => BloxLayout::is_customized( $id ),
			'template'     => blox_get( 'template', $layout_status ),
			'templateName' => blox_get( 'template', $layout_status ) ? BloxLayout::get_name( 'template-' . blox_get( 'template', $layout_status ) ) : null,
			'children'     => blox_get( 'children', $info ) === false ? false : array(),
			'ajaxChildren' => !blox_get( 'ajaxChildren', $info ) ? false : true,
			'postStatus'   => ( $post_status && $post_status != 'Published' ) ? $post_status : false,
			'noEdit'       => blox_get('noEdit', $info, false)
		);

		if ( !empty( $info['children'] ) && is_array( $info['children'] ) ) {

			$layout['children'] = array();

			foreach ( $info['children'] as $child_id => $child_info ) {
				$layout['children'][] = self::format_layout_for_knockout( $child_id, $child_info );
			}

		}

		return $layout;

	}


	public static function get_basic_pages() {

		$pages = array();

		/* Index & Front Page */
		$pages['index'] = array();

		if ( get_option( 'show_on_front' ) == 'page' ) {

			$pages['front_page'] = array();

		}

		/* WPML Compatibility for index, front page, and 404 */
		if ( function_exists( 'icl_get_languages' ) ) {

			if ( isset( $pages['front_page'] ) ) {
				$pages['front_page']['children'] = array();
			}

			$pages['index']['children'] = array();

			foreach ( icl_get_languages() as $language_id => $language_info ) {

				if ( isset( $pages['front_page'] ) ) {
					$pages['front_page']['children'][ 'front_page' . BloxLayout::$sep . 'wpml_' . $language_id ] = array();
				}

				$pages['index']['children'][ 'index' . BloxLayout::$sep . 'wpml_' . $language_id ] = array();
				$pages['four04']['children'][ 'four04' . BloxLayout::$sep . 'wpml_' . $language_id ] = array();
			}

		}

		/* Single */
		$pages['single'] = array( 'children' => array() );

		foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $post_type ) {
			$pages['single']['children']['single' . BloxLayout::$sep . $post_type->name] = array('ajaxChildren' => true);
		}

		/* Archives */
		$pages['archive'] = array(
			'children' => array(
				'archive' . BloxLayout::$sep . 'category'    => array('ajaxChildren' => true),
				'archive' . BloxLayout::$sep . 'search'      => array(),
				'archive' . BloxLayout::$sep . 'date'        => array(),
				'archive' . BloxLayout::$sep . 'author'      => array('ajaxChildren' => true),
				'archive' . BloxLayout::$sep . 'post_tag'    => array('ajaxChildren' => true),
				'archive' . BloxLayout::$sep . 'taxonomy'    => array('ajaxChildren' => true),
				'archive' . BloxLayout::$sep . 'post_type'   => array('ajaxChildren' => true),
				'archive' . BloxLayout::$sep . 'post_format' => array('ajaxChildren' => true)
			)
		);

		/* 404 */
		$pages['four04'] = array();


		/* Remove Blog Index layout if Settings Reading is configured oddly */
		if ( get_option( 'show_on_front' ) == 'page' && !get_option( 'page_for_posts' ) ) {
			unset($pages['index']);
		}

		/* Format the array for Knockout */
		$formatted_layouts = array();

		foreach ( $pages as $layout_id => $layout_info ) {
			$formatted_layouts[] = self::format_layout_for_knockout( $layout_id, $layout_info );
		}

		return $formatted_layouts;

	}


	public static function get_templates() {

		$templates = BloxSkinOption::get( 'list', 'templates', array() );

		/* Format the array for Knockout */
		$formatted_layouts = array();

		foreach ( $templates as $layout_id => $layout_info ) {
			$formatted_layouts[] = self::format_layout_for_knockout( 'template-' . $layout_id, $layout_info );
		}

		return $formatted_layouts;

	}


	public static function get_layout_children($layout_id, $offset = 0) {

		$layout_id_fragments = explode(BloxLayout::$sep, $layout_id);

		if ( count( $layout_id_fragments ) === 1 ) {
			return null;
		}

		switch ( $layout_id_fragments[0] ) {

			case 'single':

				$post_type = $layout_id_fragments[1];
				$post_parent = !empty( $layout_id_fragments[2] ) ? $layout_id_fragments[2] : 0;

				$layouts = self::query_posts($post_type, $post_parent, $offset);

				break;

			case 'archive':

				switch ( $layout_id_fragments[1] ) {

					case 'category':
						$parent = !empty( $layout_id_fragments[2] ) ? $layout_id_fragments[2] : 0;

						$layouts = self::query_terms( 'category', false, $parent, $offset );
						break;

					case 'author':
						$layouts = self::query_authors($offset);
						break;

					case 'post_tag':
						$layouts = self::query_terms( 'post_tag', false, 0, $offset );
						break;

					case 'taxonomy':

						if ( empty( $layout_id_fragments[2] ) ) {

							$taxonomies_query = get_taxonomies( array( 'public' => true, '_builtin' => false ), 'objects' );
							$exclude          = array( 'link_category' );
							$taxonomies       = array();

							$layouts = array();

							foreach ( $taxonomies_query as $slug => $taxonomy ) {

								$layouts['archive' . BloxLayout::$sep . 'taxonomy' . BloxLayout::$sep . $slug] = array('ajaxChildren' => true);

							}

						} else {

							$parent = !empty( $layout_id_fragments[3] ) ? $layout_id_fragments[3] : 0;

							$layouts = self::query_terms( $layout_id_fragments[2], true, $parent, $offset );

						}


						break;

					case 'post_type':
						$post_types = get_post_types( array( 'public' => true ), 'objects' );
						$excluded_post_types = array( 'post', 'page', 'attachment' );

						$layouts = array();

						foreach ( $post_types as $post_type ) {

							//If excluded, skip it
							if ( in_array( $post_type->name, $excluded_post_types ) )
								continue;

							$layouts['archive' . BloxLayout::$sep . 'post_type' . BloxLayout::$sep . $post_type->name] = array();

						}

						break;

					case 'post_format':
						$layouts = self::query_terms( 'post_format', false, 0, $offset );
						break;

				}

				break;

		}

		/* Format the array for Knockout */
		$formatted_layouts = array();

		foreach ( $layouts as $layout_id => $layout_info ) {
			$formatted_layouts[] = self::format_layout_for_knockout( $layout_id, $layout_info );
		}

		return $formatted_layouts;

	}


	public static function query_posts( $post_type = 'post', $post_parent = 0, $offset = 0 ) {

		$query = get_posts( array(
			'post_type'   => $post_type,
			'post_parent' => $post_parent,
			'numberposts' => self::$query_limit,
			'post_status' => array( 'publish', 'pending', 'draft', 'future', 'private' ),
			'offset' 	  => $offset
		) );

		$posts = array();

		foreach ( $query as $post ) {

			$posts['single' . BloxLayout::$sep . $post_type . BloxLayout::$sep . $post->ID] = array();

			if ( $post_type == 'page' && ($post->ID == get_option( 'page_on_front' ) || $post->ID == get_option( 'page_for_posts' ) ) ) {
				$posts[ 'single' . BloxLayout::$sep . $post_type . BloxLayout::$sep . $post->ID ]['noEdit'] = true;
			}

			$has_children_query = get_posts( array(
				'post_type'   => $post_type,
				'post_parent' => $post->ID,
				'numberposts' => 1,
				'post_status' => array( 'publish', 'pending', 'draft', 'future', 'private' )
			) );

			if ( $has_children_query ) {
				$posts['single' . BloxLayout::$sep . $post_type . BloxLayout::$sep . $post->ID]['ajaxChildren'] = true;
			}

		}

		return $posts;

	}


	public static function query_terms($taxonomy, $add_taxnomy_prefix = false, $parent = 0, $offset = 0) {

		$query = get_terms( $taxonomy, array(
			'parent' => $parent,
			'number' => self::$query_limit,
			'offset' => $offset,
			'hide_empty' => false
		) );

		$terms = array();

		foreach ( $query as $term ) {

			$taxonomy_id = $add_taxnomy_prefix ? 'taxonomy' . BloxLayout::$sep . $taxonomy : $taxonomy;

			$terms['archive' . BloxLayout::$sep . $taxonomy_id . BloxLayout::$sep . $term->term_id] = array();

			$has_children_query = get_terms( $taxonomy, array(
				'parent' => $term->term_id,
				'hide_empty' => false,
				'number' => 1
			) );

			if ( $has_children_query ) {
				$terms['archive' . BloxLayout::$sep . $taxonomy_id . BloxLayout::$sep . $term->term_id]['ajaxChildren'] = true;
			}

		}

		return $terms;

	}


	public static function query_authors($offset = 0) {

		$author_query = get_users( array(
			'who'    => 'authors',
			'fields' => 'ID',
			'offset' => $offset,
			'orderby' => 'post_count',
			'number' => self::$query_limit
		) );

		$authors = array();

		foreach ( $author_query as $author_id ) {

			$authors['archive' . BloxLayout::$sep . 'author' . BloxLayout::$sep . $author_id] = array();

		}

		return $authors;

	}


	public static function query_layouts($query) {

		global $wpdb;

		if ( empty($query) || strlen($query) <= 2 ) {
			return false;
		}

		$results = array();

		/* Posts */
		$posts_query = $wpdb->prepare("SELECT ID, post_title, post_status, post_type FROM $wpdb->posts WHERE $wpdb->posts.post_title LIKE '%s' AND $wpdb->posts.post_type != 'revision' ORDER BY $wpdb->posts.post_title", '%' . $query . '%');

		foreach ( $wpdb->get_results( $posts_query ) as $post ) {

			$post_type_layout_id = 'single' . BloxLayout::$sep . $post->post_type;
			$layout_id = $post_type_layout_id . BloxLayout::$sep . $post->ID;

			if ( !isset($results[ $post_type_layout_id ]) ) {

				$post_type_object = get_post_type_object( $post->post_type );

				$results[ $post_type_layout_id ] = self::format_layout_for_knockout( $post_type_layout_id, array(
					'name' => 'Single &rsaquo; ' . $post_type_object->labels->name,
					'children' => array()
				));

			}

			$results[ $post_type_layout_id ]['children'][] = self::format_layout_for_knockout($layout_id, array(
				'name' => $post->post_title,
				'url' => trailingslashit(home_url()) . '?p=' . $post->ID
			));

		}

		/* Archives/Terms */
		foreach ( get_taxonomies( array( 'public' => true, '_builtin' => true ), 'objects' ) as $slug => $taxonomy ) {

			$terms = get_terms( $slug, array(
				'name__like' => $query
			) );

			if ( !empty($terms ) ) {

				if ( $taxonomy->_builtin ) {
					$taxonomy_layout_id = 'archive' . BloxLayout::$sep . $slug;
				} else {
					$taxonomy_layout_id = 'archive' . BloxLayout::$sep . 'taxonomy' . BloxLayout::$sep . $slug;
				}

				$taxonomy_object = get_taxonomy( $slug );

				$results[ $taxonomy_layout_id ] = self::format_layout_for_knockout( $taxonomy_layout_id, array(
					'name'     => 'Archive &rsaquo; ' . $taxonomy_object->labels->name,
					'children' => array()
				) );

				foreach ( $terms as $term ) {

					$layout_id = $taxonomy_layout_id . BloxLayout::$sep . $term->term_id;

					$results[ $taxonomy_layout_id ]['children'][] = self::format_layout_for_knockout($layout_id, array(
						'name' => $term->name
					));

				}

			}

		}

		/* Users */
		$user_query = get_users( array(
			'fields' => array(
				'ID',
				'display_name'
			),
			'search' => '*' . $query . '*'
		) );

		if ( !empty($user_query) ) {

			$authors_layout_id = 'archive' . BloxLayout::$sep . 'author';

			$results[ $authors_layout_id ] = self::format_layout_for_knockout( $authors_layout_id, array(
				'name' => 'Archive &rsaquo; Author',
				'children' => array()
			) );

		}

		foreach ( $user_query as $user ) {

			$layout_id = $authors_layout_id . BloxLayout::$sep . $user->ID;

			$results[ $authors_layout_id ]['children'][] = self::format_layout_for_knockout($layout_id, array(
				$user->display_name
			));

		}

		return array_values($results);


	}


}
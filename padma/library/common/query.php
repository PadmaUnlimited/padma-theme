<?php

namespace Padma;
class PadmaQuery{

	function __construct(){

	}

	public static function init() {

	}


	/**
	 *
	 * Query posts
	 *
	 */
	public static function get_posts($block){

		$categories = PadmaBlockAPI::get_setting($block, 'categories', array());
		$categories_mode = PadmaBlockAPI::get_setting($block, 'categories-mode', array());
		$enable_tags = PadmaBlockAPI::get_setting($block, 'enable-tags', array());
		$tags = PadmaBlockAPI::get_setting($block, 'tags', array());
		$post_type = PadmaBlockAPI::get_setting($block, 'post-type', array());
		$post_status = PadmaBlockAPI::get_setting($block, 'post-status', array());
		$author = PadmaBlockAPI::get_setting($block, 'author', array());
		$number_of_posts = PadmaBlockAPI::get_setting($block, 'number-of-posts', array());
		$offset = PadmaBlockAPI::get_setting($block, 'offset', array());
		$order_by = PadmaBlockAPI::get_setting($block, 'order-by', array());
		$order = PadmaBlockAPI::get_setting($block, 'order', array());
		$byid_include = PadmaBlockAPI::get_setting($block, 'byid-include', array());
		$byid_exclude = PadmaBlockAPI::get_setting($block, 'byid-exclude', array());

		if(!is_array($byid_include))
			$byid_include = explode(',', $byid_include);

		if(!is_array($byid_exclude))
			$byid_exclude = explode(',', $byid_exclude);

		$args = array(
			'category__in' 		=> $categories,
			'posts_per_page'	=> $number_of_posts,
			'post_type' 		=> $post_type,
			'post_status' 		=> $post_status,
			'offset' 			=> $offset,
			'orderby' 			=> $order_by,
			'order' 			=> $order,			
			'tag__in'          	=> $tags,
			'author__in'   		=> $author,
		);

		if(count($byid_exclude)>0 && $byid_exclude[0] != '')
			$args['post__not_in'] = $byid_exclude;

		if(count($byid_include)>0 && $byid_include[0] != '')
			$args['post__in'] = $byid_include;

		$Query = new WP_Query( $args );

		return $Query->posts;
	}


	/**
	 *
	 * Query categories
	 *
	 */	
	public static function get_categories($post_types = array('post')) {

		if( ! is_array($post_types) )
			$post_types = array($post_types);

		$taxonomies = array();

		if( is_array($post_types) && count($post_types) > 0){
			foreach ($post_types as $key => $post_type) {
				foreach (get_object_taxonomies( $post_type ) as $key => $taxonomy) {					
					$taxonomies[] = $taxonomy;
				}
			}
		}else{
			$taxonomies[] = 'category';
		}

		$terms = get_terms( array(
		    'taxonomy' => $taxonomies,
		    'hide_empty' => false,
		));

		foreach ($terms as $category)
			$category_options[$category->term_id] = $category->name;

		return $category_options;

	}


	/**
	 *
	 * Query tags
	 *
	 */
	public static function get_tags($taxonomy = 'post_tag') {

		$tag_options = array();
		$tags_select_query = get_terms($taxonomy);

		foreach ($tags_select_query as $tag)
			$tag_options[$tag->term_id] = $tag->name;

		$tag_options = (count($tag_options) == 0) ? array('text' => __('No tags available','padma') ) : $tag_options;

		return $tag_options;
	}


	/**
	 *
	 * Query Authors
	 *
	 */
	public static function get_authors() {

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


	/**
	 *
	 * Query posts types
	 *
	 */
	public static function get_post_types() {

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


	/**
	 *
	 * Query taxonomies
	 *
	 */
	public static function get_taxonomies() {

		$taxonomy_options = array( __('&ndash; Default: Category &ndash;','padma') );

		$taxonomy_select_query=get_taxonomies(false, 'objects', 'or');


		foreach ($taxonomy_select_query as $taxonomy)
			$taxonomy_options[$taxonomy->name] = $taxonomy->label;


		return $taxonomy_options;

	}

	/**
	 *
	 * Query posts status
	 *
	 */	
	public static function get_post_status() {

		return get_post_stati();

	}


	/**
	 *
	 * Query Meta 
	 *
	 */
	public static function get_meta($post_types = array('post')){

		if(!is_array($post_types))
			$post_types = array($post_types);

		$custom_fields = array();

		if(is_array($post_types)){

			foreach ($post_types as $key => $post_type) {

				// get post for each post types		
				$posts = get_posts(array(
				    'post_type'   => $post_type,
				    'post_status' => 'publish',
				    'posts_per_page' => -1,
				    'fields' => 'ids'
				    )
				);

				// get custom keys for every post
				foreach ($posts as $key => $post_id) {

					// Get all meta for each post
					foreach ( get_post_meta($post_id) as $custom_field_name => $custom_field_values) {

						//Note: the if test excludes values for WordPress internally maintained custom keys such as _edit_last and _edit_lock.
					    if ( '_' == $custom_field_name{0} )
					        continue;

						foreach ($custom_field_values as $key => $value) {

						    // Exclude serialized data
						    if(is_serialized($value))
						    	continue;

						    if( isset($custom_fields[$post_type][$custom_field_name]) )
						    	$custom_fields[$post_type][$custom_field_name]++;
						    else
						    	$custom_fields[$post_type][$custom_field_name] = 1;

						}
					}				
				}
			}

		}

        return $custom_fields;
	}

}
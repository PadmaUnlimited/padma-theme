<?php
class PadmaSchema {
	
	function __construct(){

	}
	
	public static function init() {
		
		if(PadmaOption::get('disable-schema-support'))
			return;

		require_once PADMA_LIBRARY_DIR . '/common/lib/schema-org/vendor/autoload.php';

	}


	/**
	 *
	 * Article data
	 *
	 */
	public static function article($post){

		if(is_null($post))
			return;

		/**
		 *
		 * Author
		 *
		 */		
		$author = get_the_author_meta('first_name', $post->post_author) . ' ' . get_the_author_meta('last_name', $post->post_author);

		if(trim($author) == '')
			$author = get_the_author_meta('display_name', $post->post_author);

		if(trim($author) == '')
			$author = get_the_author_meta('nickname', $post->post_author);

		if(trim($author) == '')
			$author = get_the_author_meta('user_nicename', $post->post_author);
	

		/**
		 *
		 * Site Image
		 *
		 */
		$blog_id = (is_multisite()) ? get_current_blog_id(): 0;
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		$site_image = wp_get_attachment_image_src( $custom_logo_id , 'full' );
				

		/**
		 *
		 * Article Image
		 *
		 */
		
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' )[0];			




		/**
		 *
		 * Schema
		 *
		 */		
		$article = Spatie\SchemaOrg\Schema::Article()
					->mainEntityOfPage(get_permalink($post->ID))					
					->headLine($post->post_title)
					->dateCreated(new Datetime($post->post_date))
					->datePublished(new Datetime($post->post_date))
					->dateModified(new Datetime($post->post_modified))
					->author(
						Spatie\SchemaOrg\Schema::Person()
							->name($author)
							->url(get_author_posts_url($post->post_author))
					)
					->publisher(
						Spatie\SchemaOrg\Schema::Organization()
							->name(get_bloginfo('name'))
							->url(site_url())
							->logo(
								Spatie\SchemaOrg\Schema::ImageObject()
								->url($site_image[0])
							)
					);

		if($site_image[0] && !$image){
			$image = $site_image[0];	
		}

		if($image)
			$article->image($image);

		return $article->toScript();

	}
}
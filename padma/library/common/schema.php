<?php
/**
 * Padma Unlimited Theme.
 *
 * @package padma
 */

/**
 * Padma Schema main class.
 */
class PadmaSchema {

	/**
	 * Init Method
	 *
	 * @return void
	 */
	public static function init() {

		if ( PadmaOption::get( 'disable-schema-support' ) ) {
			return;
		}
	}


	/**
	 *
	 * Article data
	 *
	 * @param object $post Post data.
	 * @return void
	 */
	public static function article( $post ) {

		if ( is_null( $post ) ) {
			return;
		}

		/**
		 *
		 * Author
		 */
		$author = get_the_author_meta( 'first_name', $post->post_author ) . ' ' . get_the_author_meta( 'last_name', $post->post_author );

		if ( trim( $author ) === '' ) {
			$author = get_the_author_meta( 'display_name', $post->post_author );
		}

		if ( trim( $author ) === '' ) {
			$author = get_the_author_meta( 'nickname', $post->post_author );
		}

		if ( trim( $author ) === '' ) {
			$author = get_the_author_meta( 'user_nicename', $post->post_author );
		}

		/**
		 *
		 * Site Image
		 */
		$blog_id        = ( is_multisite() ) ? get_current_blog_id() : 0;
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		$site_image     = wp_get_attachment_image_src( $custom_logo_id, 'full' );

		if ( false !== $site_image ) {
			$site_image = $site_image[0];
		}

		/**
		 *
		 * Article Image
		 */
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
		if ( false !== $image ) {
			$image = $image[0];
		}

		/**
		 *
		 * Schema
		 */
		$article = Spatie\SchemaOrg\Schema::Article()
					->mainEntityOfPage( get_permalink( $post->ID ) )
					->headLine( $post->post_title )
					->author(
						Spatie\SchemaOrg\Schema::Person()
							->name( $author )
							->url( get_author_posts_url( $post->post_author ) )
					)
					->publisher(
						Spatie\SchemaOrg\Schema::Organization()
							->name( get_bloginfo( 'name' ) )
							->url( site_url() )
							->logo(
								Spatie\SchemaOrg\Schema::ImageObject()
								->url( $site_image )
							)
					);

		if ( $site_image && ! $image ) {
			$image = $site_image;
		}

		if ( $image ) {
			$article->image( $image );
		}

		/**
		 *
		 * Check dates
		 * https://www.facebook.com/groups/padmaunlimitedEN/permalink/688903958279742/
		 */

		if ( padma_validateDate( $post->post_date ) ) {
			$article->dateCreated( new Datetime( $post->post_date ) );
		}

		if ( padma_validateDate( $post->post_date ) ) {
			$article->datePublished( new Datetime( $post->post_date ) );
		}

		if ( padma_validateDate( $post->post_date ) ) {
			$article->dateModified( new Datetime( $post->post_modified ) );
		}

		return $article->toScript();

	}
}

<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php echo get_bloginfo('charset'); ?>" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<?php

	global $post, $current_screen, $wp_meta_boxes;

	include( ABSPATH . '/wp-load.php' );
	include( ABSPATH . '/wp-admin/includes/admin.php' );

	if ( isset( $_GET['post'] ) )
	 	$post_id = $post_ID = (int) $_GET['post'];
	elseif ( isset( $_POST['post_ID'] ) )
	 	$post_id = $post_ID = (int) $_POST['post_ID'];
	else
	 	$post_id = $post_ID = 0;

	if ( $post_id )
		$post = get_post( $post_id );


	/**
	 *
	 * WP includes
	 *
	 */
	
	require_once(ABSPATH . '/wp-admin/includes/screen.php');
	//require_once(ABSPATH . '/wp-admin/includes/template.php');
	//require_once(ABSPATH . '/wp-admin/includes/theme.php');
	require_once(ABSPATH . '/wp-admin/includes/class-wp-screen.php');


	/**
	 *
	 * gutenberg includes
	 *
	 */ 
	if(!function_exists('gutenberg_pre_init')){

		require_once PADMA_LIBRARY_DIR . '/blocks/gutenberg/functions.php';

	}

	gutenberg_pre_init();
 	
 	// Assets
 	do_action( 'admin_head' );

?>
<style type="text/css">
</style>
</head>
<body>
<?php 
	// Editor
	//the_gutenberg_project();
	gutenberg_init(false,$post);


	// Footer
	wp_footer();
?>
</body>
</html>
<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php echo get_bloginfo('charset'); ?>" />
<link rel="profile" href="http://gmpg.org/xfn/11" />


<?php

	if ( isset( $_GET['post'] ) )
	 	$post_id = $post_ID = (int) $_GET['post'];
	elseif ( isset( $_POST['post_ID'] ) )
	 	$post_id = $post_ID = (int) $_POST['post_ID'];
	else
	 	$post_id = $post_ID = 0;

	if ( $post_id )
		$post = get_post( $post_id );
?>
<iframe id="padma-content-editor-iframe" src="<?php echo admin_url('/post.php?post='.$post_id.'&action=edit'); ?>" style="height:100vh;width:100%;border:none;"></iframe>
<?php

	/*	
	exit();	


	global $post_type, $post_type_object, $post, $typenow, $current_screen, $menu, $submenu, $parent_file, $submenu_file;


	include( ABSPATH . '/wp-load.php' );
	include( ABSPATH . '/wp-admin/includes/post.php' );
	include( ABSPATH . '/wp-admin/includes/comment.php' );
	include( ABSPATH . '/wp-admin/includes/template.php' );
	include( ABSPATH . '/wp-admin/includes/screen.php' );
	include( ABSPATH . '/wp-admin/includes/theme.php' );
	include( ABSPATH . '/wp-admin/includes/class-wp-screen.php' );
	include( ABSPATH . '/wp-admin/includes/list-table.php' );
	include( ABSPATH . '/wp-admin/menu.php' );
	include( ABSPATH . '/wp-admin/includes/meta-boxes.php' );
	
	
	if ( ! defined( 'WP_ADMIN' ) ) {
   		define( 'WP_ADMIN', true );
   	}
	//include( ABSPATH . '/wp-admin/edit-form-advanced.php' );
	//include( ABSPATH . '/wp-admin/includes/template.php' );


	if ( apply_filters( 'replace_editor', false, $post ) === true ) {
		die;
	}
	

	if ( isset( $_GET['post'] ) )
	 	$post_id = $post_ID = (int) $_GET['post'];
	elseif ( isset( $_POST['post_ID'] ) )
	 	$post_id = $post_ID = (int) $_POST['post_ID'];
	else
	 	$post_id = $post_ID = 0;

	if ( $post_id )
		$post = get_post( $post_id );

	if ( $post ) {
		$post_type = $post->post_type;
		$post_type_object = get_post_type_object( $post_type );
	}

	$typenow = $post->post_type;

	if ( ! $post )
		wp_die( __( 'You attempted to edit an item that doesn&#8217;t exist. Perhaps it was deleted?' ) );

	if ( ! $post_type_object )
		wp_die( __( 'Invalid post type.' ) );

	if ( ! in_array( $typenow, get_post_types( array( 'show_ui' => true ) ) ) ) {
		wp_die( __( 'Sorry, you are not allowed to edit posts in this post type.' ) );
	}

	if ( ! current_user_can( 'edit_post', $post_id ) )
		wp_die( __( 'Sorry, you are not allowed to edit this item.' ) );

	if ( 'trash' == $post->post_status )
		wp_die( __( 'You can&#8217;t edit this item because it is in the Trash. Please restore it and try again.' ) );

	if ( ! empty( $_GET['get-post-lock'] ) ) {
		check_admin_referer( 'lock-post_' . $post_id );
		wp_set_post_lock( $post_id );
		wp_redirect( get_edit_post_link( $post_id, 'url' ) );
		exit();
	}


	$post_type = $post->post_type;
	if ( 'post' == $post_type ) {
		$parent_file = "edit.php";
		$submenu_file = "edit.php";
		$post_new_file = "post-new.php";
	} elseif ( 'attachment' == $post_type ) {
		$parent_file = 'upload.php';
		$submenu_file = 'upload.php';
		$post_new_file = 'media-new.php';
	} else {
		if ( isset( $post_type_object ) && $post_type_object->show_in_menu && $post_type_object->show_in_menu !== true )
			$parent_file = $post_type_object->show_in_menu;
		else
			$parent_file = "edit.php?post_type=$post_type";
		$submenu_file = "edit.php?post_type=$post_type";
		$post_new_file = "post-new.php?post_type=$post_type";
	}

	/**
	 * Allows replacement of the editor.
	 *
	 * @since 4.9.0
	 *
	 * @param boolean      Whether to replace the editor. Default false.
	 * @param object $post Post object.
	 */
	/*
	if ( apply_filters( 'replace_editor', false, $post ) === true ) {
		exit;
	}

	if ( ! wp_check_post_lock( $post->ID ) ) {
		$active_post_lock = wp_set_post_lock( $post->ID );

		if ( 'attachment' !== $post_type )
			wp_enqueue_script('autosave');
	}

	$title = $post_type_object->labels->edit_item;
	$post = get_post($post_id, OBJECT, 'edit');

	if ( post_type_supports($post_type, 'comments') ) {
		wp_enqueue_script('admin-comments');
		enqueue_comment_hotkeys_js();
	}

	if ( empty( $current_screen ) )
		set_current_screen( $pagenow );

	
	include( ABSPATH . 'wp-admin/edit-form-advanced.php' );
	*/

?>
<style type="text/css">

	body{
		padding: 0;
		margin: 0;
	}
	a.edit-on-wp{
		float: left;
	}
</style>
</head>
<body>
<a target="_blank" onclick="window.close();" href="<?php echo admin_url('/post.php?post='.$post_id.'&action=edit'); ?>" class="ve-btn edit-on-wp">Open on WordPress</a>

<?php 
wp_footer();
?>
<script type="text/javascript">
	jQuery('#padma-content-editor-iframe').load( function() {
	    jQuery('#padma-content-editor-iframe').contents().find("head")
	      .append(jQuery("<style type='text/css'> 	\
	      										\
	        #wpadminbar{	\
				display: none;	\
			}	\
			#padma-content-editor-iframe #adminmenuwrap{	\
				display: none;	\
			}	\
			</style>"));
	});
</script>
</body>
</html>
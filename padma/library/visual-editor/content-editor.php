<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php echo get_bloginfo('charset'); ?>" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<?php

	global $post_type, $post_type_object, $post, $typenow;


	include( ABSPATH . '/wp-load.php' );
	//include( ABSPATH . '/wp-admin/edit-form-advanced.php' );
	//include( ABSPATH . '/wp-admin/includes/template.php' );
	//include( ABSPATH . '/wp-admin/includes/meta-boxes.php' );


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

?>
<style type="text/css">
	a.ve-btn{
		display: inline-block;
	    margin: 20px 20px 20px 20px;
	    background: #1E8CBE;
	    border-color: #0074A2;
	    -webkit-box-shadow: inset 0 1px 0 rgba(120, 200, 230, 0.6);
	    box-shadow: inset 0 1px 0 rgba(120, 200, 230, 0.6);
	    color: #FFFFFF;
	    padding: 10px;		
	    text-decoration: none;
	    font-family: helvetica, arial;
	}
	a.ve-save{
	    float: right;		
	}
	a.edit-on-wp{
		float: left;
	}
</style>
</head>
<body>
<?php


	$settings = array();
	wp_editor($post->post_content, 've-content-editor', $settings);

?>
<a target="_blank" onclick="window.close();" href="<?php echo admin_url('/post.php?post='.$post_id.'&action=edit'); ?>" class="ve-btn edit-on-wp">Edit on WordPress</a>
<!--<a onclick="" href="#" class="ve-btn ve-save">Save</a>-->
<?php 
wp_footer();
?>
<script type="text/javascript">
	/*
	jQuery(document).on('click','.ve-btn.ve-save',function(){
		jQuery.ajax({
		    url: "<?php echo esc_js( admin_url( 'admin-ajax.php' ) ) ?>",
		    type: "POST",
		    data: {
		        action: "iajax_save",
		        button:  $( this ).val()
		    }
		});
	})*/
</script>
</body>
</html>
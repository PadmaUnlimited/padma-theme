<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php echo get_bloginfo('charset'); ?>" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<?php

	include( ABSPATH . '/wp-load.php' );

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
	 * CSS and JS for content editor
	 *
	 */
	wp_enqueue_script('padma_content_editor', padma_url() . '/library/visual-editor/scripts-src/modules/design/content-editor.js', array('jquery', 'padma_jquery_qtip'));
	wp_enqueue_style('padma_content_editor', padma_url() . '/library/visual-editor/css/content-editor.css');
	wp_enqueue_style('padma_content_editor', padma_url() . '/library/visual-editor/css/editor.css');

?>
<style type="text/css">
	.content-editor{
		display: none;
	}
	#ve-loading-overlay{
		display: block;
	}
</style>
</head>
<body>	
<div id="ve-loading-overlay">
	<div class="lotus">
		<div class="lotus_leaf"></div>
		<div class="lotus_leaf"></div>
		<div class="lotus_leaf"></div>
		<div class="lotus_leaf"></div>
		<div class="lotus_leaf"></div>
		<div class="lotus_leaf"></div>
		<div class="lotus_leaf"></div>
		<div class="lotus_leaf"></div>
	</div>
</div>
<div class="content-editor">
	<a target="_blank" onclick="window.close();" href="<?php echo admin_url('/post.php?post='.$post_id.'&action=edit'); ?>" class="ve-btn edit-on-wp">Open on WordPress</a>
	<iframe id="padma-content-editor-iframe" src="<?php echo admin_url('/post.php?post='.$post_id.'&action=edit'); ?>" style="height:100vh;width:100%;border:none;"></iframe>	
</div>
<?php 

wp_enqueue_script('jquery');
wp_footer();

?>
<script type="text/javascript">

	function prepare(callback){		
		jQuery('#padma-content-editor-iframe').load( function() {
		    jQuery('#padma-content-editor-iframe').contents().find("head").append(
		    	jQuery("<link rel='stylesheet' href='<?php echo padma_url() . '/library/visual-editor/css/content-editor.css'; ?>'>")
		    );		
		},callback());
	}

	function hideEditor(){
		jQuery('div#ve-loading-overlay').show();
		jQuery('.content-editor').hide();
	}

	function start(){
		prepare(function(){
			setTimeout(function () {
				jQuery('div#ve-loading-overlay').hide();
				jQuery('.content-editor').show();
			}, 2000);
		});
	}

	jQuery(document).ready(function(){
		start();
	});

</script>
</body>
</html>
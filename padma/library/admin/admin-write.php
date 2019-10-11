<?php
class PadmaAdminWrite {


	public static function init() {

		/* Load the default meta boxes */
		Padma::load('admin/admin-meta-boxes');

		add_action('delete_post', array(__CLASS__, 'delete_post'));

		add_filter('get_sample_permalink_html', array(__CLASS__, 'open_in_visual_editor_button'), 10, 4);

	}


	public static function delete_post($postid) {

		$post = get_post($postid);

		/* If the post type is a revision then don't do anything. */
		if ( $post->post_type == 'revision' )
			return false;

		/* Figure out the layout ID */
		$layout_id = 'single' . PadmaLayout::$sep . $post->post_type . PadmaLayout::$sep . $postid;

		/* Delete everything from the layout including blocks, wrapper, design editor instances, and the wp_options rows */
		PadmaLayout::delete_layout($layout_id, false);

	}


	public static function open_in_visual_editor_button($return, $id, $new_title, $new_slug) {

		global $post;

		if ( !isset($post->ID) || !is_numeric($post->ID) || $post->post_status != 'publish' || !PadmaCapabilities::can_user_visually_edit() )
			return $return;

		$layout_id = 'single' . PadmaLayout::$sep . $post->post_type . PadmaLayout::$sep . $post->ID;

		if ( get_option('show_on_front') === 'page' ) {

			if ( $post->ID == get_option('page_on_front') )
				$layout_id = 'front_page';

			if ( $post->ID == get_option('page_for_posts') )
				$layout_id = 'index';

		}

		$visual_editor_url = home_url('/?visual-editor=true&ve-layout=' . urlencode($layout_id));

		$return .= '<span id="padma-open-in-ve-btn" style="margin-right: 3px;"><a href="' . $visual_editor_url . '" class="button button-primary button-small" target="_blank">' . __('Open in Visual Editor','padma') . '</a></span>';

		return $return;

	}


}
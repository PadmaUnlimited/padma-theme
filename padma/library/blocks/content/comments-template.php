<?php
global $post;
global $padma_comments_template_args;

echo '<div id="comments">';

	if ( !post_password_required() ) { 

		if ( have_comments() ) {

			/* Comments Area Heading */
			echo '<h3 id="comments">';

				/* Comments Area Responses Formatting */
					$comments_number = (int)get_comments_number($post->ID);

					if ( $comments_number == 1  ) 
						$comments_heading_responses_format = stripslashes(padma_get('comments-area-heading-responses-number-1', $padma_comments_template_args, __('One Response','padma') ));
					else
						$comments_heading_responses_format = stripslashes(padma_get('comments-area-heading-responses-number', $padma_comments_template_args, '%num% ' . __('Responses','padma') ));

					$comments_heading_replacements = array(
						'responses' => str_replace('%num%', $comments_number, $comments_heading_responses_format),
						'title' => get_the_title()
					);
				/* End Comments Area Responses Formatting */
				
				echo str_replace(array('%responses%', '%title%'), $comments_heading_replacements, padma_get('comments-area-heading', $padma_comments_template_args, '%responses% to <em>%title%</em>'));

			echo '</h3>';
			/* End Comments Area Heading */
			
			echo '<ol class="commentlist">';
			
				wp_list_comments(apply_filters('padma_comments_args', array(
					'avatar_size' => 44,
					'format' => 'html5'
				))); 

			echo '</ol>';

			echo '<div class="comments-navigation">';
				echo '<div class="alignleft">';
					paginate_comments_links();
				echo '</div>';
			echo '</div>';

		} else {

			if ( $post->comment_status != 'open' ) {

				if ( is_single() ) {
					
					$comments_closed = apply_filters('padma_comments_closed', __('Sorry, comments are closed for this post.', 'padma'));
					
					echo '<p class="comments-closed">' . $comments_closed . '</p>';
					
				}

			}

		}

		comment_form(apply_filters('padma_comment_form_args', array()));

	} else {

		echo '<p class="nocomments">' . __('This post is password protected.  Please enter the password to view the comments.', 'padma') . '</p>';

	}

echo '</div>';
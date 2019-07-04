<?php
if ( !class_exists('PadmaPinBoardCoreBlock') ) {

	padma_register_block('PadmaPinBoardCoreBlock', padma_url() . '/library/blocks/pin-board');
	class PadmaPinBoardCoreBlock extends PadmaBlockAPI {


		public $id 				= 'pin-board';
		public $name 			= 'Pin Board';
		public $description 	= 'Use to display your content in a masonry grid like Pinterest.';
		public $options_class 	= 'PadmaPinBoardCoreBlockOptions';
		public $categories 		= array('core','content');


		public static function init() {

			add_action('wp_ajax_padma_pin_board_infinite_scroll', array(__CLASS__, 'infinite_scroll_content'));
			add_action('wp_ajax_nopriv_padma_pin_board_infinite_scroll', array(__CLASS__, 'infinite_scroll_content'));

		}


		public static function enqueue_action($block_id, $block = false) {

			global $wp_query;

			if ( parent::get_setting($block, 'paginate', true) ) {
				add_filter('redirect_canonical', array(__CLASS__, 'disable_redirect_canonical'));
			}

			/* CSS */
			wp_enqueue_style('padma-pin-board', padma_url() . '/library/blocks/pin-board/css/pin-board.css');

			/* JS */
			wp_enqueue_script('padma-pin-board', padma_url() . '/library/blocks/pin-board/js/pin-board.js', array('jquery'));

			/* Variables */
			wp_localize_script('padma-pin-board', 'PadmaPinBoard', array(
				'ajaxURL' => admin_url('admin-ajax.php'),
				'isArchive' => is_archive(),
				'isSearch' => is_search(),
				'wpQueryVars' => json_encode($wp_query->query_vars)
			));

		}


		public static function disable_redirect_canonical($redirect_url) {

			return false;

		}


		public static function dynamic_js($block_id, $block = false) {

			if ( !$block )
				$block = PadmaBlocksData::get_block($block_id);

			$infinite_scroll = intval(parent::get_setting($block, 'infinite-scroll', true));

			$js = "
			jQuery(document).ready(function() {
				setupPinBoardBlock({
					blockID: '" . $block_id . "',
					effects: {
						hoverFocus: " . (parent::get_setting($block, 'hover-focus', false) ? 'true' : 'false') . ",
						infiniteScroll: " . $infinite_scroll . "
					},
					columns: " . parent::get_setting($block, 'columns', 3) . ",
					columnsSmartphone: " . parent::get_setting($block, 'columns-smartphone', 2) . ",
					gutterWidth: " . parent::get_setting($block, 'gutter-width', 15) . "
				});
			});
			";
			
			return $js;

		}


		public static function dynamic_css($block_id, $block = false) {

			if ( !$block ) {
				$block = PadmaBlocksData::get_block( $block_id );
			}

			$gutter_width 		= parent::get_setting( $block, 'gutter-width', 15 );
			$columns_desktop 	= parent::get_setting( $block, 'columns', 3 );
			$columns_smartphone = parent::get_setting( $block, 'columns-smartphone', 2 );

			$width_calc_expression_desktop = ( 100 / $columns_desktop ) . '% - ' . ( $gutter_width * ( ( $columns_desktop - 1 ) / $columns_desktop ) ) . 'px';
			$width_calc_expression_smartphone = ( 100 / $columns_smartphone ) . '% - ' . ( $gutter_width * ( ( $columns_smartphone - 1 ) / $columns_smartphone ) ) . 'px';

			return '
			#block-' . $block_id . ' .pin-board-pin,
			#block-' . $block_id . ' .pin-board-column-sizer {
				width: ' . ( 100 / $columns_desktop ) . ' %;
				width: -webkit-calc(' . $width_calc_expression_desktop . ');
				width: -moz-calc(' . $width_calc_expression_desktop . ');
				width: -o-calc(' . $width_calc_expression_desktop . ');
				width: calc(' . $width_calc_expression_desktop . ');

				margin-bottom: ' . parent::get_setting($block, 'pin-bottom-margin', 15) . 'px; 
			}

			#block-' . $block_id . ' .pin-board-gutter-sizer {
				width: ' . parent::get_setting( $block, 'gutter-width', 15 ) . 'px;
			}

			@media only screen and (max-width: 620px) {
				#block-' . $block_id . ' .pin-board-pin,
				#block-' . $block_id . ' .pin-board-column-sizer {
					width: ' . ( 100 / $columns_smartphone ) . ' %;
					width: -webkit-calc(' . $width_calc_expression_smartphone . ');
					width: -moz-calc(' . $width_calc_expression_smartphone . ');
					width: -o-calc(' . $width_calc_expression_smartphone . ');
					width: calc(' . $width_calc_expression_smartphone . ');
				}
			}
			';

		}


		/**
		 * Anything in here will be displayed when the block is being displayed.
		 **/
		public function content($block) {

			global $wp_query, $post;

			if ( padma_post('isAjax') ) {
				$is_archive = padma_post( 'isArchive' );
				$is_search 	= padma_post( 'isSearch' );
			} else {
				$is_archive = is_archive();
				$is_search 	= is_search();
			}

			$columns 			= parent::get_setting($block, 'columns', 3);
			$approx_pin_width 	= (PadmaBlocksData::get_block_width($block) / $columns);

			/* Element Visibility */
				$show_images = parent::get_setting($block, 'show-images', true);
				$show_titles = parent::get_setting($block, 'show-titles', true);

				/* Meta */
					$show_author 		= parent::get_setting($block, 'show-author', false);
					$show_categories 	= parent::get_setting($block, 'show-category', false);
					$show_tags 			= parent::get_setting($block, 'show-tags', false);
					$show_post_type		= parent::get_setting($block, 'show-post-type', false);
					$show_datetime 		= parent::get_setting($block, 'show-datetime', false);
					$datetime_verb 		= parent::get_setting($block, 'datetime-verb', 'Posted');
					$relative_times 	= parent::get_setting($block, 'relative-times', true);
					
					//$entry_meta_above 	= $this->parse_meta(parent::get_setting('entry-meta-above', 'Posted on %date% by %author% &bull; %comments%'));


				/* Content */
					$show_continue 				= parent::get_setting($block, 'show-continue', false);
					$content_to_show 			= parent::get_setting($block, 'content-to-show', 'excerpt');
					$show_text_when_no_image 	= parent::get_setting($block, 'show-text-if-no-image', false);
					$titles_position 			= parent::get_setting($block, 'titles-position', 'below');
					$titles_link_to_post 		= parent::get_setting($block, 'titles-link-to-post', true);

			/* Images */
					$crop_images_vertically = parent::get_setting($block, 'crop-vertically', false);
					$image_click_action 	= parent::get_setting($block, 'image-click-action', 'post');

					if ( $image_click_action == 'popup' ) {
						add_thickbox();
					}

			/* Social Stuff */
				/* Pinterest */
					$show_pinterest_button 	= parent::get_setting($block, 'show-pinterest-button', false);

				/* Twitter */
					$show_twitter_button 	= parent::get_setting($block, 'show-twitter-button', false);

					$twitter_username 		= parent::get_setting($block, 'twitter-username', null);
					$twitter_hashtag 		= parent::get_setting($block, 'twitter-hashtag', null);

				/* Facebook */
					$show_facebook_button 	= parent::get_setting($block, 'show-facebook-button', false);
					$facebook_button_verb 	= parent::get_setting($block, 'facebook-button-verb', 'like');
			/* End Social Stuff */

			$infinite_scroll = parent::get_setting($block, 'infinite-scroll', true);

			/* Setup Query */
				if ( parent::get_setting( $block, 'mode', 'default' ) != 'custom' && ($is_archive || $is_search) ) {
					$query_args = padma_post( 'wpQueryVars' ) ? json_decode(stripslashes(padma_post('wpQueryVars')), ARRAY_A) : $wp_query->query_vars;
				} else {
					$query_args = array();
				}

				/* Default Query Defaults */
					$query_args['post_type'] 		= 'post';
					$query_args['posts_per_page'] 	= get_option('posts_per_page');

				/* Pagination */
					$paged_var 				= get_query_var('paged') ? get_query_var('paged') : get_query_var('page');
					$pin_board_ajax_paged 	= padma_post( 'pinBoardAjaxPaged' );
					$query_args['paged'] 	= $pin_board_ajax_paged ? $pin_board_ajax_paged : $paged_var;

					if ( !$query_args['paged'] ) {
						$query_args['paged'] = null;
					}

			/* Custom query overrides */
				if ( parent::get_setting($block, 'mode', 'default') == 'custom' ) {

					/* Post type */
					if ( parent::get_setting( $block, 'post-type' ) )
						$query_args['post_type'] = parent::get_setting( $block, 'post-type' );


					/* Taxonomies */
						$terms_list = parent::get_setting($block, 'categories', false);

						if ( $terms_list ) {

							$query_args['tax_query'] = array(
								array(
									'taxonomy' => parent::get_setting( $block, 'taxonomies', 'category' ),
									'field'    => 'slug',
									'terms'    => parent::get_setting( $block, 'categories', 'category' ),
									'operator' => parent::get_setting( $block, 'categories-mode', 'include' ) == 'exclude' ? 'NOT IN' : 'IN'
								),
							);

						}

					/* Author Filter */
						if ( is_array(parent::get_setting($block, 'author')) )
							$query_args['author'] 	= trim(implode(',', parent::get_setting($block, 'author')), ', ');

					/* Pin limit */
					$query_args['posts_per_page'] 	= parent::get_setting( $block, 'pins-per-page', 10 );

					/* Order */
					$query_args['orderby'] 	= parent::get_setting( $block, 'order-by', 'date' );
					$query_args['order'] 	= parent::get_setting( $block, 'order', 'DESC' );

					/* Offset */
					if ( parent::get_setting( $block, 'offset' ) !== null ) {

						if ( !empty($query_args['paged']) ) {
							$query_args['offset'] = parent::get_setting( $block, 'offset' ) + ( ( $query_args['paged'] - 1 ) * parent::get_setting( $block, 'pins-per-page', 10 ) );
						} else {
							$query_args['offset'] = parent::get_setting( $block, 'offset' );
						}

					}

					$exclude_current_post = parent::get_setting( $block, 'exclude-current-post', false );

				}

				/* Query! */
				$original_wp_query 	= $wp_query;
				$wp_query 			= new WP_Query($query_args);
				/* End Query Setup */

				echo '<div class="pin-board" data-pin-board-ajax-paged="' . padma_get('paged', $query_args, 1) . '" data-pin-board-mode="' . parent::get_setting( $block, 'mode', 'default' ) . '">' . "\n";

					echo '<div class="pin-board-gutter-sizer"></div>' . "\n";
					echo '<div class="pin-board-column-sizer"></div>' . "\n\n";


				$current_post_id = $post->ID;

				while ( $wp_query->have_posts() ) {


					do_action('padma_before_pin_board_pin_setup');

					$wp_query->the_post();

					// Exclude current post from the pinboard
					if( $exclude_current_post && $current_post_id == get_the_ID())
						continue;

					/* If only images are being shown and there's no thumbnail, then don't show the pin. */
					if ( !($show_images && has_post_thumbnail()) && !$content_to_show && !$show_titles && !$show_text_when_no_image )
						continue;

					$title_for_attribute 	= the_title_attribute(array('echo' => false));
					$pin_classes 			= get_post_class();
					$pin_classes[] 			= has_post_thumbnail() ? 'pin-board-pin-has-image' : 'pin-board-pin-no-image';

					do_action('padma_before_pin_board_pin_open');

					echo '<div class="pin-board-pin ' . implode(' ', $pin_classes) . '">' . "\n";

						do_action('padma_after_pin_board_pin_open');

						/* Titles above */
							if ( $show_titles && $titles_position == 'above') {

								echo '<h3 class="entry-title">';
								if ($titles_link_to_post) {
									echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
								} else {
									echo get_the_title();
								}
								echo '</h3>' . "\n";

							}
						/* End Titles below */

						/* Thumbnail */
							if ( has_post_thumbnail() && $show_images ) {

								$thumbnail_id = get_post_thumbnail_id();

								$thumbnail_width = $approx_pin_width + 30; /* Add a 30px buffer to insure that image will be large enough */

								//$crop_vertically
								if ( $crop_images_vertically ) {

									$thumbnail_height 	= round($approx_pin_width * 0.75);
									$thumbnail_object 	= wp_get_attachment_image_src($thumbnail_id, 'full');
									$thumbnail_url 		= padma_resize_image($thumbnail_object[0], $thumbnail_width, $thumbnail_height);
									$full_image_url 	= $thumbnail_object[0];

								} else {

									$thumbnail_object 	= wp_get_attachment_image_src($thumbnail_id, 'full');
									$thumbnail_url 		= padma_resize_image($thumbnail_object[0], $thumbnail_width);
									$full_image_url 	= $thumbnail_object[0];

								}

								do_action('padma_before_pin_thumbnail');

								echo '<div class="pin-board-pin-thumbnail">' . "\n";

									if ( $image_click_action == 'post' ) {

										echo '<a href="' . get_permalink() . '" class="post-thumbnail" title="' . $title_for_attribute . '">';
											echo '<img src="' . esc_url($thumbnail_url) . '" alt="' . $title_for_attribute . '" />';
										echo '</a>' . "\n";

									} elseif ($image_click_action == 'popup') {

										echo '<a href="' . esc_url($full_image_url) . '" class="thickbox post-thumbnail" rel="pinboard-'.$block['id'].'" title="' . $title_for_attribute . '">';
											echo '<img src="' . esc_url($thumbnail_url) . '" alt="' . $title_for_attribute . '" />';
										echo '</a>' . "\n";

									} else {

										echo '<a class="post-thumbnail"><img src="' . esc_url($thumbnail_url) . '" alt="' . $title_for_attribute . '" /></a>' . "\n";

									}
									if ( $show_pinterest_button || $show_twitter_button || $show_facebook_button ) {

										echo '<div class="pin-board-pin-thumbnail-social">' . "\n";

											if ( $show_facebook_button )
												self::facebook_button(get_permalink(), $facebook_button_verb);

											if ( $show_twitter_button )
												self::twitter_button(get_permalink(), $title_for_attribute, $twitter_username, $twitter_hashtag);

											if ( $show_pinterest_button ) {

												$full_size_image = wp_get_attachment_image_src($thumbnail_id, 'full');
												$full_size_image_url = $full_size_image[0];

												self::pinterest_button(get_permalink(), $full_size_image_url);

											}

										echo '</div>' . "\n";

									}

								echo '</div>' . "\n\n";

								do_action('padma_after_pin_thumbnail');

							}
						/* End Thumbnail */

						echo '<div class="below-thumb">' . "\n";

						/* Titles below */
							if ( $show_titles && $titles_position == 'below') {

								echo '<h3 class="entry-title">';
								if ($titles_link_to_post) {
									echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
								} else {
									echo get_the_title();
								}
								echo '</h3>' . "\n";

							}
						/* End Titles below */

						/* Meta */
							if ( $show_author || $show_datetime || $show_categories  || $show_tags || $show_post_type) {

								global $authordata;

								do_action('padma_before_pin_meta');

								echo '<div class="entry-meta">' . "\n";

									if ( $show_datetime ) {
										echo '<span class="entry-date published" title="' . get_the_time('c') . '">' . ($datetime_verb ? $datetime_verb . ' ' : '') . self::relative_time($relative_times) . '</span> ';
									}

									if ( $show_author ) {
										echo '<em class="author-by">by</em> <a class="author-link fn nickname url" href="' . get_author_posts_url($authordata->ID) . '" title="View all entries by ' . $authordata->display_name . '">' . $authordata->display_name . '</a>';
									}

									if ( $show_categories ) {
										echo '<div class="entry-categories">' . get_the_category_list(', ') . '</div>';
									}

									if ( $show_tags ) {
										echo '<div class="entry-tags">' . get_the_tag_list('', ', ') . '</div>';
									}

									if ( $show_post_type ) {
										echo '<div class="entry-post-type">' . get_post_type() . '</div>';
									}
									

								echo '</div>' . "\n";

								do_action('padma_after_pin_meta');

							}
						/* End Meta */

						/* Excerpts/Content */
								do_action('padma_before_pin_content');

								if ( ($show_text_when_no_image && !has_post_thumbnail()) || ($content_to_show && !$show_text_when_no_image)) {

									echo '<div class="pin-board-pin-text entry-content">' . "\n";

									if ($content_to_show == 'excerpt') {
											add_filter('excerpt_more', array(__CLASS__, 'excerpt_more'));
											the_excerpt();
											remove_filter('excerpt_more', array(__CLASS__, 'excerpt_more'));
									} elseif ( $content_to_show == 'content') {
										the_content();
									}

									echo '</div>' . "\n";

								}

								do_action('padma_after_pin_content');

						/* End Excerpts */


						/* Backup social buttons if no image is present */
							if ( (!has_post_thumbnail() || !$show_images) && ($show_twitter_button || $show_facebook_button) ) {

								echo '<div class="pin-board-pin-social">' . "\n";

									if ( $show_twitter_button )
										self::twitter_button(get_permalink(), $title_for_attribute, $twitter_username, $twitter_hashtag);

									if ( $show_facebook_button )
										self::facebook_button(get_permalink(), $facebook_button_verb);

								echo '</div>' . "\n";

							}
						/* End backup social buttons */

						echo '</div>' . "\n";

						do_action('padma_before_pin_board_pin_close');

					echo '</div>' . "\n\n";

					do_action('padma_after_pin_board_pin_close');

					do_action('padma_after_pin_board_pin_setup');

				} // End while loop

				echo '</div>' . "\n";

			if ( parent::get_setting($block, 'paginate', true) || $infinite_scroll ) {

				do_action('padma_before_pin_board_pagination');
				self::pagination($wp_query, $infinite_scroll, parent::get_setting($block, 'enumerate', false));
				do_action('padma_after_pin_board_pagination');
			}

			$wp_query = $original_wp_query;

		}


			public static function infinite_scroll_content() {

				$block = PadmaBlocksData::get_block(padma_post('block'));

				if ( is_array($block) && $block['type'] == 'pin-board' ) {
					do_action( 'padma_block_content_pin-board', $block );
				}

				die();

			}


		/**
		 * Register elements to be edited by the Padma Design Editor
		 **/

		public function setup_elements() {

			$this->register_block_element(array(
				'id' 			=> 'pin',
				'name' 			=> 'Pin',
				'selector' 		=> '.pin-board-pin',				
			));

			$this->register_block_element(array(
				'id' 			=> 'pin-board-pin-thumbnail',
				'name' 			=> 'Pin Thumbnail',
				'selector' 		=> '.pin-board-pin-thumbnail',
			));

			$this->register_block_element(array(
				'id' 			=> 'pin-board-pin-thumbnail-link',
				'name' 			=> 'Pin Thumbnail Link',
				'selector' 		=> '.pin-board-pin-thumbnail a',
			));

			$this->register_block_element(array(
				'id' 			=> 'pin-board-pin-thumbnail-link-img',
				'name' 			=> 'Pin Thumbnail Image',
				'selector' 		=> '.pin-board-pin-thumbnail a img',
			));

			$this->register_block_element(array(
				'id' 			=> 'pin-below-thumb',
				'name' 			=> 'Pin Below Thumb',
				'selector' 		=> '.pin-board-pin .below-thumb',				
			));	

			$this->register_block_element(array(
				'id' 			=> 'pin-title',
				'name' 			=> 'Pin Title',
				'selector'		=> '.pin-board-pin .entry-title',
				'states' 		=> array(
						'Hover' => '.pin-board-pin .entry-title a:hover',
					)
			));

			$this->register_block_element(array(
				'id' 			=> 'pin-title link',
				'name' 			=> 'Pin Title Link',
				'selector'		=> '.pin-board-pin .entry-title a',				
				'states' 		=> array(
						'Hover' => '.pin-board-pin .entry-title a:hover',
					)
			));

			$this->register_block_element(array(
				'id' 			=> 'pin-meta',
				'name' 			=> 'Pin Meta',
				'selector' 		=> '.pin-board-pin .entry-meta',				
			));

				$this->register_block_element(array(
					'parent' 		=> 'pin-meta',
					'id' 			=> 'pin-meta-author',
					'name' 			=> 'Author',
					'selector' 		=> '.pin-board-pin .entry-meta .author-link',					
				));

				$this->register_block_element(array(
					'parent' 		=> 'pin-meta',
					'id' 			=> 'pin-meta-categories',
					'name' 			=> 'Categories',
					'selector' 		=> '.pin-board-pin .entry-meta .entry-categories',					
				));

				$this->register_block_element(array(
					'parent' 		=> 'pin-meta',
					'id' 			=> 'pin-meta-tags',
					'name' 			=> 'Tags',
					'selector' 		=> '.pin-board-pin .entry-meta .entry-tags',					
				));

				$this->register_block_element(array(
					'parent' 		=> 'pin-meta',
					'id' 			=> 'pin-meta-categories-link',
					'name' 			=> 'Categories Link',
					'selector' 		=> '.pin-board-pin .entry-meta .entry-categories a',					
				));

			$this->register_block_element(array(
				'id' 			=> 'pin-text',
				'name' 			=> 'Pin Text',
				'selector' 		=> '.pin-board-pin .entry-content',
			));

			$this->register_block_element(array(
				'id' 			=> 'pin-content-a',
				'name' 			=> 'Pin Content Links',
				'selector' 		=> '.pin-board-pin .entry-content a',
			));

			$this->register_block_element(array(
				'id' 			=> 'pin-content-img',
				'name' 			=> 'Pin Content Image',
				'selector' 		=> '.pin-board-pin .entry-content img',
			));

			$this->register_block_element(array(
				'id' 			=> 'pagination-button',
				'name' 			=> 'Pagination Button',
				'selector' 		=> '.pin-board-pagination a',
				'states' 		=> array(
						'Hover' => '.pin-board-pagination a:hover',
					)
			));
			$this->register_block_element(array(
				'id' 			=> 'pagination-text',
				'name' 			=> 'Pagination Current Page',
				'selector' 		=> '.pin-board-pagination span.page-numbers.current',
			));

		}


		private static function pinterest_button($url, $image_url) {

			if ( !$url || !$image_url )
				return;

			echo '<a href="http://pinterest.com/pin/create/button/?url=' . rawurlencode($url) . '&media=' . rawurlencode($image_url) . '" class="pin-it-button" count-layout="horizontal"><img border="0" src="" data-src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>';

		}


		private static function twitter_button($url, $title, $username = '', $hashtag = '') {

			if ( !$url )
				return;

			echo '<iframe allowtransparency="true" frameborder="0" scrolling="no" data-src="http://platform.twitter.com/widgets/tweet_button.1340179658.html#_=1343335678535&amp;count=none&amp;hashtags=' . str_replace('#', '', $hashtag) . '&amp;id=twitter-widget-0&amp;lang=en&amp;original_referer=' . rawurlencode($url) . '&amp;related=' . $username . '&amp;size=m&amp;text=' . rawurlencode($title) . '&amp;url=' . rawurlencode($url) . '" class="twitter-share-button" title="Twitter Tweet Button"></iframe>';

		}


		private static function facebook_button($url, $verb = 'like') {

			if ( !$url )
				return;

			echo '<iframe class="facebook-share-button facebook-' . $verb . '-button" data-src="//www.facebook.com/plugins/like.php?href=' . rawurlencode($url) . '&amp;send=false&amp;layout=button_count&amp;width=90&amp;show_faces=false&amp;action=' . strtolower($verb) . '&amp;colorscheme=light&amp;font=lucida+grande&amp;height=21" scrolling="no" frameborder="0" allowTransparency="true"></iframe>';

		}


		private static function pagination($query, $infinite_scroll = true, $enumerate) {

			$previous_paged_global = ( ! empty( $GLOBALS['paged'] ) ) ? $GLOBALS['paged'] : null;
			$GLOBALS['paged'] = get_query_var( 'paged' );

			echo '<div class="pin-board-pagination">';

			if ( $enumerate ) {

				$big = 999999999; // need an unlikely integer

				echo '<span class="nav-previous">';
					echo get_previous_posts_link( '&larr; Previous' );
				echo '</span>';

				echo paginate_links( array(
					'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'format' => '?paged=%#%',
					'current' => max( 1, get_query_var('paged') ),
					'total' => $query->max_num_pages,
					'prev_next' => false
				) );

				echo '<span class="nav-next">';
					echo get_next_posts_link( 'Next &rarr;', $query->max_num_pages );
				echo '</span>';

			} else {


				echo '<span class="nav-next">';
					echo get_next_posts_link( '&larr; Older', $query->max_num_pages );
				echo '</span>';

				echo '<span class="nav-previous">';
					echo get_previous_posts_link( 'Newer &rarr;' );
				echo '</span>';

			}

			if ( $previous_paged_global !== null ) {
				$GLOBALS['paged'] = $previous_paged_global;
			}

			echo '</div><!-- .pin-board-pagination -->';
		}


			private static function previous_posts_link($label) {

				global $paged;

				if ( null === $label )
					$label = __( '&laquo; Previous Page', 'padma' );

				if ( $paged > 1 ) {
					$attr = apply_filters( 'previous_posts_link_attributes', '' );
					return '<a href="' . previous_posts( false ) . "\" $attr>". preg_replace( '/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label ) .'</a>';
				}

			}


		private static function relative_time($relative = true) {

			if ( $relative ) {

				$post_date = get_the_time('U');
				$delta = time() - $post_date;

				if ( $delta < 60 ) {
				    return 'less than a minute ago';

				} elseif ($delta >= 60 && $delta <= 120){
				    return 'about a minute ago';

				} elseif ($delta >= 120 && $delta <= (60*60)) {
				    return strval(round(($delta/60),0)) . ' minutes ago';

				} elseif ($delta >= (60*60) && $delta <= (120*60)){
				    return 'about an hour ago';

				} elseif ($delta >= (120*60) && $delta <= (24*60*60)){
				    return strval(round(($delta/3600),0)) . ' hours ago';

				}

			}

			return get_the_date('M j, Y');

		}


		public static function excerpt_more($more) {

			return '...';

		}


	}


	class PadmaPinBoardCoreBlockOptions extends PadmaBlockOptionsAPI {

		public $taxonomy_list;

		public $terms_list;

		public $tabs = array(
			'pin-setup' 		=> 'Pin Setup',
			'query-filters' 	=> 'Query Filters',
			'pagination' 		=> 'Pagination/Infinite Scroll',
			'text' 				=> 'Text',
			'meta' 				=> 'Meta',
			'images' 			=> 'Images',
			'effects' 			=> 'Effects',
			'social' 			=> 'Social'
		);


		public $inputs = array(
			'pin-setup' => array(
				'mode' => array(
					'type' 		=> 'select',
					'name' 		=> 'mode',
					'label' 	=> 'Mode',
					'tooltip' 	=> 'If you would like to modify the default behaviour, select custom query. <br/><strong>Note:</strong>On archive pages, it\'s not advisable to use a custom query if the block is displaying the archive results.<br/>On search pages, it may be necessary to limit results to only certain content types.',
					'options' 	=> array(
							'default' 	=> 'Default behaviour',
							'custom' 	=> 'Custom query',
						),
					'toggle' 	=> array(
						'default' => array(
								'hide' => 'li#sub-tab-query-filters'
							),
						'custom' => array(
							'show' => 'li#sub-tab-query-filters'
						)
					)
				),
				'layout-heading' => array(
					'name' 	=> 'layout-heading',
					'type' 	=> 'heading',
					'label' => 'Layout',
				),
				'columns' => array(
					'type' 				=> 'slider',
					'name' 				=> 'columns',
					'label' 			=> 'Columns',
					'slider-min' 		=> 1,
					'slider-max' 		=> 7,
					'slider-interval'	=> 1,
					'default' 			=> 3,
					'tooltip' 			=> 'Set how many pins to display horizontally'
				),

				'columns-smartphone' => array(
					'type' 				=> 'slider',
					'name' 				=> 'columns-smartphone',
					'label' 			=> 'Columns (iPhone/Smartphone)',
					'slider-min' 		=> 1,
					'slider-max' 		=> 7,
					'slider-interval' 	=> 1,
					'default' 			=> 2,
					'tooltip' 			=> 'Set how many pins to display horizontally for iPhones and smartphones.  <strong>Recommended setting: 1 or 2</strong>'
				),

				'gutter-width' => array(
					'type' 				=> 'slider',
					'name' 				=> 'gutter-width',
					'label' 			=> 'Gutter Width',
					'slider-min' 		=> 0,
					'slider-max' 		=> 100,
					'slider-interval' 	=> 1,
					'default' 			=> 15,
					'unit' 				=> 'px',
					'tooltip' 			=> 'The amount in space between pins horizontally.'
				),

				'pin-bottom-margin' => array(
					'type' 				=> 'slider',
					'name' 				=> 'pin-bottom-margin',
					'label' 			=> 'Pin Bottom Margin',
					'slider-min' 		=> 0,
					'slider-max' 		=> 50,
					'slider-interval' 	=> 1,
					'default' 			=> 15,
					'unit' 				=> 'px',
					'tooltip' 			=> 'The amount of space on the bottom of each pin.'
				)
			),

			'query-filters' => array(
				'pins-per-page' => array(
					'type' 		=> 'integer',
					'name' 		=> 'pins-per-page',
					'label' 	=> 'Pins Per Page',
					'default' 	=> 10,
					'tooltip' 	=> 'Determines how many pins to load at one time before loading more via pagination or <em>infinite scrolling</em>.'
				),

				'offset' => array(
					'type' 		=> 'integer',
					'name' 		=> 'offset',
					'label' 	=> 'Offset',
					'tooltip' 	=> 'The offset is the number of entries or posts you would like to skip.  If the offset is 1, then the first post will be skipped.',
					'default' 	=> 0
				),

				'filters-heading' => array(
					'name' 	=> 'filters-heading',
					'type' 	=> 'heading',
					'label' => 'Filters',
				),

				'post-type' => array(
					'type' 		=> 'multi-select',
					'name' 		=> 'post-type',
					'label' 	=> 'Post Type',
					'tooltip' 	=> 'Choose a post type to display. If none are selected, it will automatically default to all.',
					'default' 	=> 'any',
					'options' 	=> 'get_post_types()'
				),
				'taxonomies' => array(
					'type' 		=> 'select',
					'name' 		=> 'taxonomies',
					'label' 	=> 'Taxonomy',
					'default' 	=> 'category',
					'options' 	=> array( 'none' => 'No taxonomies' ),
					'tooltip' 	=> 'Select the taxonomy to filter pins on .',
					'callback' 	=> 'reloadBlockOptions()'
				),
				// For simplicity with migrating from categories to all taxonomies, these next two have kept the same names. In the future a function could be written to port them to a correctly named variable
				'categories' => array(
					'type' 		=> 'multi-select',
					'name' 		=> 'categories',
					'label' 	=> 'Terms',
					'default' 	=> '',
					'options' 	=> array( 'none' => 'No terms' ),
					'tooltip' 	=> 'Filter the pins that are shown by the selected taxonomy\'s terms.'
				),
				'categories-mode' => array(
					'type' 		=> 'select',
					'name' 		=> 'categories-mode',
					'label' 	=> 'Terms Mode',
					'tooltip' 	=> '',
					'options' 	=> array(
						'include' => 'Include',
						'exclude' => 'Exclude'
					),
					'tooltip' 	=> 'If this is set to <em>include</em>, then only the pins that match the terms filter will be shown.  If set to <em>exclude</em>, all pins that match the selected terms will not be shown.'
				),
				'author' => array(
					'type' 		=> 'multi-select',
					'name' 		=> 'author',
					'label' 	=> 'Author',
					'tooltip' 	=> '',
					'options' 	=> 'get_authors()'
				),
				'exclude-current-post' => array(
					'type' => 'checkbox',
					'name' => 'exclude-current-post',
					'label' => 'Exclude Current Post',
					'default' => false,
					'tooltip' => 'Enabling this option will exclude the current Post from the pinboard, usefull when are creating a "related post" block.'
				),

				'order-heading' => array(
					'name' 	=> 'order-heading',
					'type' 	=> 'heading',
					'label' => 'Order',
				),
				'order-by' => array(
					'type' 		=> 'select',
					'name' 		=> 'order-by',
					'label' 	=> 'Order By',
					'tooltip' 	=> '',
					'options' 	=> array(
						'date' 	=> 'Date',
						'title' => 'Title',
						'rand' 	=> 'Random',
						'ID' 	=> 'ID'
					)
				),
				'order' => array(
					'type' => 'select',
					'name' => 'order',
					'label' => 'Order',
					'tooltip' => '',
					'options' => array(
						'desc' => 'Descending',
						'asc' => 'Ascending',
					)
				)
			),

			'pagination' => array(
				'paginate' => array(
					'type' => 'checkbox',
					'name' => 'paginate',
					'label' => 'Paginate Pins',
					'default' => true,
					'tooltip' => 'Enabling pagination adds buttons to the bottom of the pin board to go to the next/previous page.  <strong>Note:</strong> If infinite scrolling is enabled, pagination will be hidden.'
				),

				'enumerate' => array(
					'type' => 'checkbox',
					'name' => 'enumerate',
					'label' => 'Enumerate pagination',
					'default' => false,
					'tooltip' => 'If pagination is displayed, enabling this will also show page number navigation.'
				),

				'infinite-scroll' => array(
					'type' => 'checkbox',
					'name' => 'infinite-scroll',
					'label' => 'Infinite Scrolling',
					'default' => true,
					'tooltip' => 'Infinite scrolling allows your visitors to view all of your pins without the need for them to click a button to continue to the next page.  The pins will be loaded automatically simply by scrolling.'
				)
			),

			'text' => array(
				'show-titles' => array(
						'type' => 'checkbox',
						'name' => 'show-titles',
						'label' => 'Show Titles',
						'default' => true
					),

				'titles-position' => array(
						'type' => 'select',
						'name' => 'titles-position',
						'label' => 'Titles position',
						'default' => 'below',
						'options' => array('above' => 'Above','below' => 'Below')
					),

				'titles-link-to-post' => array(
						'type' => 'checkbox',
						'name' => 'titles-link-to-post',
						'label' => 'Titles link to post',
						'default' => true,
						'tooltip' => 'Open the post when the user clicks on the title'
					),

				'content-to-show' => array(
						'type' => 'select',
						'name' => 'content-to-show',
						'label' => 'Content To Show',
						'options' => array(
							'' => '&ndash; Do Not Show Content &ndash;',
							'excerpt' => 'Excerpts',
							'content' => 'Full Content'
						),
						'default' => 'excerpt',
						'tooltip' => 'The content is the written text or HTML for the entry.  This is edited in the WordPress admin panel.'
					),

				'show-text-if-no-image' => array(
						'type' => 'checkbox',
						'name' => 'show-text-if-no-image',
						'label' => 'Only show content when no featured image',
						'default' => false,
						'tooltip' => 'If enabled, regardless of the content chosen in <em>Content to Show</em> will only show content for pins with no featured image.'
					),

			),

			'meta' => array(

				'show-author' 	=> array(
						'type' 		=> 'checkbox',
						'name' 		=> 'show-author',
						'label' 	=> 'Meta: Show Author "byline"',
						'default' 	=> false,
						'tooltip' 	=> '<strong>Example:</strong> <em>by</em> Author Name'
					),

				'show-category' => array(
						'type' 		=> 'checkbox',
						'name' 		=> 'show-category',
						'label' 	=> 'Meta: Show Categories',
						'default' 	=> false
					),

				'show-tags' => array(
						'type' 		=> 'checkbox',
						'name' 		=> 'show-tags',
						'label' 	=> 'Meta: Show Tags',
						'default' 	=> false
					),

				'show-post-type' => array(
						'type' 		=> 'checkbox',
						'name' 		=> 'show-post-type',
						'label' 	=> 'Meta: Show Post Type',
						'default' 	=> false
					),

				'show-datetime' => array(
						'type' 		=> 'checkbox',
						'name' 		=> 'show-datetime',
						'label' 	=> 'Meta: Show Date/Time',
						'default' 	=> false
					),

				'datetime-verb' => array(
						'type' 		=> 'text',
						'name' 		=> 'datetime-verb',
						'label' 	=> 'Meta: Posted Verb',
						'default' 	=> 'Posted',
						'tooltip'	=> 'The posted verb will be placed before the time.  For instance, you may want to use "Listed" for real estate rather than "Posted"'
					),
			),

			'images' => array(
				'show-images' => array(
					'type' => 'checkbox',
					'name' => 'show-images',
					'label' => 'Show Images',
					'default' => true,
				),
				'images-click-action' => array(
					'type' => 'select',
					'name' => 'image-click-action',
					'label' => 'Image click action',
					'default' => 'link',
					'tooltip' => 'Choose the action when user clicks on an image.',
					'options' => array(
						'post'  => 'Open post',
						'popup' => 'Popup original image',
						'none'  => 'Do nothing'
					)
				),

				'crop-vertically' => array(
					'type' => 'checkbox',
					'name' => 'crop-vertically',
					'label' => 'Crop Vertically',
					'default' => false,
					'tooltip' => 'Trim all images to have the same height.  The trimmed/cropped height is roughly 75% of the width.'
				)
			),

			'effects' => array(
				'hover-focus' => array(
					'type' => 'checkbox',
					'name' => 'hover-focus',
					'label' => 'Hover Focus',
					'default' => false,
					'tooltip' => 'If enabled, the hovered pin will be focused while all others will be faded out.'
				)
			),

			'social' => array(
				'show-pinterest-button' => array(
					'type' => 'checkbox',
					'name' => 'show-pinterest-button',
					'label' => 'Pinterest: Show "Pin It" Button',
					'default' => false,
					'tooltip' => 'Show a Pinterest "Pin It" button inside of the images.',
				),

				'show-twitter-button' => array(
					'type' => 'checkbox',
					'name' => 'show-twitter-button',
					'label' => 'Twitter: Show Tweet Button',
					'default' => false,
					'tooltip' => 'Show a tweet button either inside of the post image or by the title.',
				),

				'twitter-username' => array(
					'type' => 'text',
					'name' => 'twitter-username',
					'label' => 'Twitter: Your Username'
				),

				'twitter-hashtag' => array(
					'type' => 'text',
					'name' => 'twitter-hashtag',
					'label' => 'Twitter: Hashtag to put in tweets (Optional)'
				),

				'show-facebook-button' => array(
					'type' => 'checkbox',
					'name' => 'show-facebook-button',
					'label' => 'Facebook: Show Like/Share Button',
					'default' => false,
					'tooltip' => 'Show a Facebook share/like button either inside of the post image or by the title.',
				),

				'facebook-button-verb' => array(
					'type' => 'select',
					'label' => 'Facebook: Button Verb',
					'name' => 'facebook-button-verb',
					'options' => array(
						'like' => 'Like',
						'recommend' => 'Recommend'
					),
					'default' => 'like'
				)
			)
		);


		public function modify_arguments($args = false) {
			$block = $args['block'];

			$this->taxonomy_list 	= self::get_taxonomy_list();
			$this->inputs['query-filters']['taxonomies']['options'] = $this->taxonomy_list;

			$tax_slug 			= PadmaBlockAPI::get_setting($block, 'taxonomies', 'category');
			$this->terms_list 	= self::get_tax_terms($tax_slug);

			$this->inputs['query-filters']['categories'] = array(
				'type' 		=> 'multi-select',
				'name' 		=> 'categories',
				'label' 	=> 'Terms',
				'default' 	=> '',
				'options' 	=> $this->terms_list[$tax_slug],
				'tooltip' 	=> 'Filter the pins that are shown by the selected taxonomy\'s terms.'
			);

			$callback = '
					if ( !$("body").hasClass("visual-editor-mode-grid") ) {
						var hoverFocusState = input.parents(".sub-tabs-content-container").find("#input-hover-focus input").val().toBool();
						var infiniteScrollState = input.parents(".sub-tabs-content-container").find("#input-infinite-scroll").val().toBool();
						window.frames[0].setupPinBoardBlock({
							blockID: getBlockID(block),
							effects: {
								hoverFocus: hoverFocusState,
								infiniteScroll: infiniteScrollState,
							},
							columns: parseInt(input.parents(".sub-tabs-content-container").find("#input-columns input[type=\'hidden\']").val()),
							columnsSmartphone: parseInt(input.parents(".sub-tabs-content-container").find("#input-columns-smartphone input[type=\'hidden\']").val()),
							gutterWidth: parseInt(input.parents(".sub-tabs-content-container").find("#input-gutter-width input[type=\'hidden\']").val())
						});
					}
				';

			/* Add the callback to all options */
			foreach ( $this->inputs as $tab_id => $inputs ){
				foreach ( $this->inputs[$tab_id] as $input_id => $input_options ){
					if ( !padma_get('callback', $this->inputs[$tab_id][$input_id]) ){
						$this->inputs[$tab_id][$input_id]['callback'] = $callback;
					}
				}
			}

		}


		public static function get_categories() {

			$category_options = array();

			$categories_select_query = get_categories();

			foreach ($categories_select_query as $category)
				$category_options[$category->term_id] = $category->name;

			return $category_options;

		}


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

		public static function get_taxonomy_list() {

			$custom_tax = get_taxonomies();
			$exclude_list = array('nav_menu','link_category','post_format');
			$tax_array = array();

			foreach ($custom_tax as $tax) {
				if (!in_array($tax, $exclude_list)) {
					$tax_array[$tax] = ucwords(str_replace(array('_','-'), ' ', $tax));
				}
			}

			return $tax_array;

		}

		public static function get_tax_terms($taxonomies, $keys_only = false) {

			if ( !is_array($taxonomies) )
				$taxonomies = array($taxonomies => $taxonomies);

			$terms = array();

			foreach ( $taxonomies as $key => $tax_name ) {

				$term_list = get_terms($key, 'hide_empty=0');

				foreach ($term_list as $term) {

					if ( $keys_only ) {
						$terms[] = $term->slug;
					} else {
						$terms[$key][$term->slug] = $term->name;
					}

				}

				if ( !$keys_only && count($terms[$key]) == 0 ) {
					$terms[$key]['none'] = 'No terms found for this taxonomy';
				}

			}

			return $terms;

		}

	}


	/**
	 * Prevent 404ing from breaking Infinite Scrolling
	 **/
	add_action('status_header', 'pu_pin_board_block_prevent_404');
	function pu_pin_board_block_prevent_404($status) {

		if ( strpos($status, '404') && get_query_var('paged') && padma_get('pb') )
			return 'HTTP/1.1 200 OK';

		return $status;

	}


	/**
	 * Prevent WordPress redirect from messing up pin board infinite scroll
	 */
	add_filter('redirect_canonical', 'pu_pin_board_block_redirect');
	function pu_pin_board_block_redirect($redirect_url) {

		if ( padma_get('pb') )
			return false;

		return $redirect_url;

	}



	/*
	function parse_meta($meta) {

		global $post, $authordata;

		$variables = array(
			'date',
			'time',
			'comments',
			'comments_no_link',
			'respond',
			'author',
			'author_no_link',
			'categories',
			'tags',
			'edit'
		);

		foreach ( $variables as $variable ) {

			if ( strpos($meta, '%' . $variable . '%') === false )
				continue;

			switch ( $variable ) {

				case 'date':

					$date_format = $this->get_setting('date-format', 'wordpress-default');
					$date = ($date_format != 'wordpress-default') ? get_the_time($date_format) : get_the_date();

					$replacement['date'] = '<time class="entry-date published updated" itemprop="datePublished" datetime="' . get_the_time( 'c' ) . '">' . $date . '</time>';

				break;

				case 'time':

					$time_format = $this->get_setting('time-format', 'wordpress-default');
					$time = ($date_format != 'wordpress-default') ? get_the_time($time_format) : get_the_time();

					$replacement['time'] = '<time class="entry-time" datetime="' . get_the_time( 'c' ) . '">' . $time . '</time>';

				break;

				case 'comments':
				case 'comments_no_link':

					$comments_number = (int)get_comments_number($post->ID);

					if ( $comments_number === 0 ) 
						$comments_format = stripslashes($this->get_setting('comment-format-0', '%num% Comments'));
					elseif ( $comments_number == 1 ) 
						$comments_format = stripslashes($this->get_setting('comment-format-1', '%num% Comment'));
					elseif ( $comments_number > 1 ) 
						$comments_format = stripslashes($this->get_setting('comment-format', '%num% Comments'));

					$comments = str_replace('%num%', $comments_number, $comments_format);
					
					$replacement['comments'] = '<a href="' . get_comments_link() . '" title="' . sprintf(__('%s &ndash; Comments', 'padma'), the_title_attribute('echo=0')) . '" class="entry-comments">' . $comments . '</a>';
					$replacement['comments_no_link'] = $comments;

				break;

				case 'respond':

					$respond_format = stripslashes($this->get_setting('respond-format', 'Leave a comment!'));
					
					$replacement['respond'] = '<a href="' . get_permalink() . '#respond" title="' . sprintf(__('Respond to %s', 'padma'), the_title_attribute('echo=0')) . '" class="entry-respond">' . $respond_format . '</a>';

				break;

				case 'author':
				case 'author_no_link':

					$replacement['author'] = '<span class="entry-author vcard" itemprop="author" itemscope itemtype="http://schema.org/Person"><a class="author-link fn nickname url" href="' . get_author_posts_url($authordata->ID) . '" title="' . sprintf(__('View all posts by %s', 'padma'), $authordata->display_name) . '" itemprop="url"><span class="entry-author-name" itemprop="name">' . $authordata->display_name . '</span></a></span>';
					$replacement['author_no_link'] = $authordata->display_name;

				break;

				case 'categories':
					$replacement['categories'] = get_the_category_list(', ');
				break;

				case 'tags':
					$replacement['tags'] = (get_the_tags() != NULL) ? get_the_tag_list('<span class="tag-links" itemprop="keywords">','<span class="tag-sep">, </span>','</span>') : '';
				break;

				case 'edit':
					$replacement['edit'] = null;
				break;


			}

			$meta = str_replace('%' . $variable . '%', $replacement[$variable], $meta);

		}

		return apply_filters('padma_meta', $meta);
		
	}*/
}

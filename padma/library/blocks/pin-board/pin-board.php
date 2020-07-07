<?php

namespace Padma;

if ( !class_exists('PadmaPinBoardCoreBlock') ) {

	class PadmaPinBoardCoreBlock extends PadmaBlockAPI {


		public $id;
		public $name;
		public $description;
		public $options_class;
		public $categories;


		function __construct(){

			$this->id = 'pin-board';
			$this->name = __('Pin Board','padma');
			$this->description = __('Use to display your content in a masonry grid like Pinterest.','padma');
			$this->options_class = 'PadmaPinBoardCoreBlockOptions';
			$this->categories = array('core','content');

		}


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
				width: ' . ( 100 / $columns_desktop ) . '%;
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


			// Get custom fields
			$block['custom-fields'] = $this->get_custom_fields($block);

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

				if( isset($post) )
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

					
					// POST ID
					$post_id 		= get_the_id();

					echo '<div class="pin-board-pin ' . implode(' ', $pin_classes) . '" data-post-id="'. get_the_ID() .'">' . "\n";

						do_action('padma_after_pin_board_pin_open');


						/**
						 *
						 * Custom Fields "Above"
						 *
						 */
						
						if(isset($block['custom-fields']['above']) && is_array($block['custom-fields']['above']) && count($block['custom-fields']['above'])>0){
							
							echo '<div class="'. implode(' ', apply_filters('padma_pin_board_pin_custom_fields_class', array('custom-fields', 'custom-fields-above') ) )  .'">';


							foreach ($block['custom-fields']['above'] as $post_type => $custom_fields) {

								foreach ($custom_fields as $field_name => $label) {

									$group_tag = apply_filters('padma_pin_board_pin_custom_fields_group_tag', 'div' );
									$label_tag = apply_filters('padma_pin_board_pin_custom_fields_label_tag', 'label' );
									$field_tag = apply_filters('padma_pin_board_pin_custom_fields_field_tag', 'div' );

									$custom_field_content = get_post_meta($post_id,$field_name,true);
									$custom_field_content = apply_filters('padma_pin_board_pin_custom_fields_field_content', $custom_field_content );

									if($custom_field_content){

										// open tag
										echo '<' . $group_tag . ' class="custom-fields-group">';

										if($label)
											echo '<'.$label_tag.'>'. $label . '</'.$label_tag.'>';

										echo '<'.$field_tag.'>'. $custom_field_content . '</'.$field_tag.'>';

										// close tag
										echo '</' . $group_tag . '>';										
									}
								}
							}

							echo '</div>';
						}

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
						/* End Titles above */

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

								/**
								 * Srcset
								 */
								$srcset = wp_get_attachment_image_srcset( $thumbnail_id );

								do_action('padma_before_pin_thumbnail');

								echo '<div class="pin-board-pin-thumbnail">' . "\n";

									if ( $image_click_action == 'post' ) {

										echo '<a href="' . get_permalink() . '" class="post-thumbnail" title="' . $title_for_attribute . '">';
											echo '<img src="' . esc_url($thumbnail_url) . '" alt="' . $title_for_attribute . '" srcset="' . esc_attr( $srcset ) . '"/>';
										echo '</a>' . "\n";

									} elseif ($image_click_action == 'popup') {

										echo '<a href="' . esc_url($full_image_url) . '" class="thickbox post-thumbnail" rel="pinboard-'.$block['id'].'" title="' . $title_for_attribute . '">';
											echo '<img src="' . esc_url($thumbnail_url) . '" alt="' . $title_for_attribute . '" srcset="' . esc_attr( $srcset ) . '"/>';
										echo '</a>' . "\n";

									} else {

										echo '<a class="post-thumbnail"><img src="' . esc_url($thumbnail_url) . '" alt="' . $title_for_attribute . '"  srcset="' . esc_attr( $srcset ) . '" /></a>' . "\n";

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


								/**
						 		 *
								 * Custom Fields "Before Content"
								 *
								 */
								if(isset($block['custom-fields']['before-content']) && is_array($block['custom-fields']['before-content']) && count($block['custom-fields']['before-content'])>0){

									echo '<div class="'. implode(' ', apply_filters('padma_pin_board_pin_custom_fields_class', array('custom-fields', 'custom-fields-before-content') ) )  .'">';

									foreach ($block['custom-fields']['before-content'] as $post_type => $custom_fields) {

										foreach ($custom_fields as $field_name => $label) {

											$group_tag = apply_filters('padma_pin_board_pin_custom_fields_group_tag', 'div' );
											$label_tag = apply_filters('padma_pin_board_pin_custom_fields_label_tag', 'label' );
											$field_tag = apply_filters('padma_pin_board_pin_custom_fields_field_tag', 'div' );

											$custom_field_content = get_post_meta($post_id,$field_name,true);
											$custom_field_content = apply_filters('padma_pin_board_pin_custom_fields_field_content', $custom_field_content );

											if($custom_field_content){

												// open tag
												echo '<' . $group_tag . ' class="custom-fields-group">';

												if($label)
													echo '<'.$label_tag.'>'. $label . '</'.$label_tag.'>';

												echo '<'.$field_tag.'>'. $custom_field_content . '</'.$field_tag.'>';

												// close tag
												echo '</' . $group_tag . '>';										
											}
										}
									}

									echo '</div>';
								}
								/**
						 		 *
								 * END Custom Fields "Before Content"
								 *
								 */

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


								/**
			 					 *
								 * Custom Fields "Below"
								 *
								 */
								if(isset($block['custom-fields']['below']) && is_array($block['custom-fields']['below']) && count($block['custom-fields']['below'])>0){

									echo '<div class="'. implode(' ', apply_filters('padma_pin_board_pin_custom_fields_class', array('custom-fields', 'custom-fields-below') ) )  .'">';

									foreach ($block['custom-fields']['below'] as $post_type => $custom_fields) {

										foreach ($custom_fields as $field_name => $label) {

											$group_tag = apply_filters('padma_pin_board_pin_custom_fields_group_tag', 'div' );
											$label_tag = apply_filters('padma_pin_board_pin_custom_fields_label_tag', 'label' );
											$field_tag = apply_filters('padma_pin_board_pin_custom_fields_field_tag', 'div' );

											$custom_field_content = get_post_meta($post_id,$field_name,true);
											$custom_field_content = apply_filters('padma_pin_board_pin_custom_fields_field_content', $custom_field_content );

											if($custom_field_content){

												// open tag
												echo '<' . $group_tag . ' class="custom-fields-group">';

												if($label)
													echo '<'.$label_tag.'>'. $label . '</'.$label_tag.'>';

												echo '<'.$field_tag.'>'. $custom_field_content . '</'.$field_tag.'>';

												// close tag
												echo '</' . $group_tag . '>';										
											}
										}
									}

									echo '</div>';
								}

								/**
			 					 *
								 * END Custom Fields "Below"
								 *
								 */

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
		 *
		 * Get custom fields
		 *
		 */
		
		function get_custom_fields($block){

			$custom_fields_show = $custom_fields_label = $custom_fields_position = array();

			foreach ($block['settings'] as $name => $value) {

				$data = explode('-', $name);
				$post_type = (!empty($data[3])) ? $data[3]: null;

				if(is_null($post_type))
					continue;

				$custom_field = $name;
				$custom_field = str_replace('custom-field-show-' . $post_type . '-', '' , $custom_field);
				$custom_field = str_replace('custom-field-position-' . $post_type . '-', '' , $custom_field);
				$custom_field = str_replace('custom-field-label-' . $post_type . '-', '' , $custom_field);

				if ( strpos($name, 'custom-field-show') !== false ){				
					if($value){
						$custom_fields_show[$post_type][$custom_field] = $value;
					}				
				}

				if ( strpos($name, 'custom-field-position') !== false )
					$custom_fields_position[$post_type][$custom_field] = $value;

				if ( strpos($name, 'custom-field-label') !== false )
					$custom_fields_label[$post_type][$custom_field] = $value;

			}

			$data = array();

			foreach ($custom_fields_position as $post_type => $custom_fields) {
				foreach ($custom_fields as $field_name => $position) {
					if($custom_fields_show[$post_type][$field_name]){
						$label = $custom_fields_label[$post_type][$field_name];
						$data[$position][$post_type][$field_name] = $label;					
					}
				}
			}

			return $data;
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
				'name' 			=> __('Pin Thumbnail','padma'),
				'selector' 		=> '.pin-board-pin-thumbnail',
			));

			$this->register_block_element(array(
				'id' 			=> 'pin-board-pin-thumbnail-link',
				'name' 			=> __('Pin Thumbnail Link','padma'),
				'selector' 		=> '.pin-board-pin-thumbnail a',
			));

			$this->register_block_element(array(
				'id' 			=> 'pin-board-pin-thumbnail-link-img',
				'name' 			=> __('Pin Thumbnail Image','padma'),
				'selector' 		=> '.pin-board-pin-thumbnail a img',
				'states' => array(
					'Hover' => '.pin-board-pin-thumbnail a img:hover',
				)
			));


			$this->register_block_element(array(
				'id' 			=> 'pin-below-thumb',
				'name' 			=> __('Pin Below Thumb','padma'),
				'selector' 		=> '.pin-board-pin .below-thumb',				
			));	

			$this->register_block_element(array(
				'id' 			=> 'pin-title',
				'name' 			=> __('Pin Title','padma'),
				'selector'		=> '.pin-board-pin .entry-title',
				'states' 		=> array(
						'Hover' => '.pin-board-pin .entry-title a:hover',
					)
			));

			$this->register_block_element(array(
				'id' 			=> 'pin-title link',
				'name' 			=> __('Pin Title Link','padma'),
				'selector'		=> '.pin-board-pin .entry-title a',				
				'states' 		=> array(
						'Hover' => '.pin-board-pin .entry-title a:hover',
					)
			));

			$this->register_block_element(array(
				'id' 			=> 'pin-meta',
				'name' 			=> __('Pin Meta','padma'),
				'selector' 		=> '.pin-board-pin .entry-meta',				
			));

				$this->register_block_element(array(
					'parent' 		=> 'pin-meta',
					'id' 			=> 'pin-meta-author',
					'name' 			=> __('Author','padma'),
					'selector' 		=> '.pin-board-pin .entry-meta .author-link',					
				));

				$this->register_block_element(array(
					'parent' 		=> 'pin-meta',
					'id' 			=> 'pin-meta-categories',
					'name' 			=> __('Categories','padma'),
					'selector' 		=> '.pin-board-pin .entry-meta .entry-categories',					
				));

				$this->register_block_element(array(
					'parent' 		=> 'pin-meta',
					'id' 			=> 'pin-meta-tags',
					'name' 			=> __('Tags','padma'),
					'selector' 		=> '.pin-board-pin .entry-meta .entry-tags',					
				));

				$this->register_block_element(array(
					'parent' 		=> 'pin-meta',
					'id' 			=> 'pin-meta-categories-link',
					'name' 			=> __('Categories Link','padma'),
					'selector' 		=> '.pin-board-pin .entry-meta .entry-categories a',					
				));

			$this->register_block_element(array(
				'id' 			=> 'pin-text',
				'name' 			=> __('Pin Text','padma'),
				'selector' 		=> '.pin-board-pin .entry-content',
			));

			$this->register_block_element(array(
				'id' 			=> 'pin-content-a',
				'name' 			=> __('Pin Content Links','padma'),
				'selector' 		=> '.pin-board-pin .entry-content a',
			));

			$this->register_block_element(array(
				'id' 			=> 'pin-content-img',
				'name' 			=> __('Pin Content Image','padma'),
				'selector' 		=> '.pin-board-pin .entry-content img',
			));

			$this->register_block_element(array(
				'id' 			=> 'pagination-button',
				'name' 			=> __('Pagination Button','padma'),
				'selector' 		=> '.pin-board-pagination a',
				'states' 		=> array(
						'Hover' => '.pin-board-pagination a:hover',
					)
			));
			$this->register_block_element(array(
				'id' 			=> 'pagination-text',
				'name' 			=> __('Pagination Current Page','padma'),
				'selector' 		=> '.pin-board-pagination span.page-numbers.current',
			));

			/**
			 *
			 * Custom Fields
			 *
			 */
			$this->register_block_element(array(
				'id' => 'custom-fields',
				'name' => __('Custom Fields Container','padma'),
				'selector' => '.custom-fields',			
			));
			$this->register_block_element(array(
				'id' => 'custom-fields-group',
				'name' => __('Custom Fields Group','padma'),
				'selector' => '.custom-fields-group',
			));

				$this->register_block_element(array(
					'id' => 'custom-fields-div',
					'name' => __('Custom Fields Image','padma'),
					'selector' => '.custom-fields image',			
				));

				$this->register_block_element(array(
					'id' => 'custom-fields-div',
					'name' => __('Custom Fields Div','padma'),
					'selector' => '.custom-fields div',			
				));

				$this->register_block_element(array(
					'id' => 'custom-fields-p',
					'name' => __('Custom Fields text','padma'),
					'selector' => '.custom-fields p',
				));

				$this->register_block_element(array(
					'id' => 'custom-fields-a',
					'name' => __('Custom Fields Link','padma'),
					'selector' => '.custom-fields a',
				));

				$this->register_block_element(array(
					'id' => 'custom-fields-h1',
					'name' => __('Custom Fields H1','padma'),
					'selector' => '.custom-fields h1',
				));

				$this->register_block_element(array(
					'id' => 'custom-fields-h2',
					'name' => __('Custom Fields H2','padma'),
					'selector' => '.custom-fields h2',
				));

				$this->register_block_element(array(
					'id' => 'custom-fields-h3',
					'name' => __('Custom Fields H3','padma'),
					'selector' => '.custom-fields h3',
				));

				$this->register_block_element(array(
					'id' => 'custom-fields-h4',
					'name' => __('Custom Fields H4','padma'),
					'selector' => '.custom-fields h4',
				));

				$this->register_block_element(array(
					'id' => 'custom-fields-h5',
					'name' => __('Custom Fields H5','padma'),
					'selector' => '.custom-fields h5',
				));

				$this->register_block_element(array(
					'id' => 'custom-fields-h6',
					'name' => __('Custom Fields H6','padma'),
					'selector' => '.custom-fields h6',
				));

				$this->register_block_element(array(
					'id' => 'custom-fields-span',
					'name' => __('Custom Fields span','padma'),
					'selector' => '.custom-fields span',
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
		public $tabs;
		public $inputs;

		function __construct($block_type_object){

			parent::__construct($block_type_object);

			$this->tabs = array(
				'pin-setup' 		=> __('Pin Setup','padma'),
				'query-filters' 	=> __('Query Filters','padma'),
				'pagination' 		=> __('Pagination/Infinite Scroll','padma'),
				'text' 				=> __('Text','padma'),
				'meta' 				=> __('Meta','padma'),
				'images' 			=> __('Images','padma'),
				'custom-fields'		=> __('Custom Fields','padma'),
				'effects' 			=> __('Effects','padma'),
				'social' 			=> __('Social','padma')
			);


			$this->inputs = array(
				'pin-setup' => array(
					'mode' => array(
						'type' 		=> 'select',
						'name' 		=> 'mode',
						'label' 	=> __('Mode','padma'),
						'tooltip' 	=> __('If you would like to modify the default behaviour, select custom query. <br/><strong>Note:</strong>On archive pages, it\'s not advisable to use a custom query if the block is displaying the archive results.<br/>On search pages, it may be necessary to limit results to only certain content types.','padma'),
						'options' 	=> array(
								'default' 	=> __('Default behaviour','padma'),
								'custom' 	=> __('Custom query','padma'),
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
						'label' => __('Layout','padma'),
					),
					'columns' => array(
						'type' 				=> 'slider',
						'name' 				=> 'columns',
						'label' 			=> __('Columns','padma'),
						'slider-min' 		=> 1,
						'slider-max' 		=> 7,
						'slider-interval'	=> 1,
						'default' 			=> 3,
						'tooltip' 			=> __('Set how many pins to display horizontally','padma')
					),

					'columns-smartphone' => array(
						'type' 				=> 'slider',
						'name' 				=> 'columns-smartphone',
						'label' 			=> __('Columns (iPhone/Smartphone)','padma'),
						'slider-min' 		=> 1,
						'slider-max' 		=> 7,
						'slider-interval' 	=> 1,
						'default' 			=> 2,
						'tooltip' 			=> __('Set how many pins to display horizontally for iPhones and smartphones.  <strong>Recommended setting: 1 or 2</strong>','padma')
					),

					'gutter-width' => array(
						'type' 				=> 'slider',
						'name' 				=> 'gutter-width',
						'label' 			=> __('Gutter Width','padma'),
						'slider-min' 		=> 0,
						'slider-max' 		=> 100,
						'slider-interval' 	=> 1,
						'default' 			=> 15,
						'unit' 				=> 'px',
						'tooltip' 			=> __('The amount in space between pins horizontally.','padma')
					),

					'pin-bottom-margin' => array(
						'type' 				=> 'slider',
						'name' 				=> 'pin-bottom-margin',
						'label' 			=> __('Pin Bottom Margin','padma'),
						'slider-min' 		=> 0,
						'slider-max' 		=> 50,
						'slider-interval' 	=> 1,
						'default' 			=> 15,
						'unit' 				=> 'px',
						'tooltip' 			=> __('The amount of space on the bottom of each pin.','padma')
					)
				),

				'query-filters' => array(
					'pins-per-page' => array(
						'type' 		=> 'integer',
						'name' 		=> 'pins-per-page',
						'label' 	=> __('Pins Per Page','padma'),
						'default' 	=> 10,
						'tooltip' 	=> __('Determines how many pins to load at one time before loading more via pagination or <em>infinite scrolling</em>.','padma')
					),

					'offset' => array(
						'type' 		=> 'integer',
						'name' 		=> 'offset',
						'label' 	=> __('Offset','padma'),
						'tooltip' 	=> __('The offset is the number of entries or posts you would like to skip.  If the offset is 1, then the first post will be skipped.','padma'),
						'default' 	=> 0
					),

					'filters-heading' => array(
						'name' 	=> 'filters-heading',
						'type' 	=> 'heading',
						'label' => __('Filters','padma'),
					),

					'post-type' => array(
						'type' 		=> 'multi-select',
						'name' 		=> 'post-type',
						'label' 	=> __('Post Type','padma'),
						'tooltip' 	=> __('Choose a post type to display. If none are selected, it will automatically default to all.','padma'),
						'default' 	=> 'any',
						'options' 	=> 'get_post_types()',
						'callback' => 'reloadBlockOptions(block.id)'
					),
					'taxonomies' => array(
						'type' 		=> 'select',
						'name' 		=> 'taxonomies',
						'label' 	=> __('Taxonomy','padma'),
						'default' 	=> 'category',
						'options' 	=> array( 'none' => 'No taxonomies' ),
						'tooltip' 	=> __('Select the taxonomy to filter pins on .','padma'),
						'callback' 	=> 'reloadBlockOptions()'
					),
					// For simplicity with migrating from categories to all taxonomies, these next two have kept the same names. In the future a function could be written to port them to a correctly named variable
					'categories' => array(
						'type' 		=> 'multi-select',
						'name' 		=> 'categories',
						'label' 	=> __('Terms','padma'),
						'default' 	=> '',
						'options' 	=> array( 'none' => 'No terms' ),
						'tooltip' 	=> __('Filter the pins that are shown by the selected taxonomy\'s terms.','padma')
					),
					'categories-mode' => array(
						'type' 		=> 'select',
						'name' 		=> 'categories-mode',
						'label' 	=> __('Terms Mode','padma'),
						'tooltip' 	=> '',
						'options' 	=> array(
							'include' => __('Include','padma'),
							'exclude' => __('Exclude','padma')
						),
						'tooltip' 	=> __('If this is set to <em>include</em>, then only the pins that match the terms filter will be shown.  If set to <em>exclude</em>, all pins that match the selected terms will not be shown.','padma')
					),
					'author' => array(
						'type' 		=> 'multi-select',
						'name' 		=> 'author',
						'label' 	=> __('Author','padma'),
						'tooltip' 	=> '',
						'options' 	=> 'get_authors()'
					),
					'exclude-current-post' => array(
						'type' => 'checkbox',
						'name' => 'exclude-current-post',
						'label' => __('Exclude Current Post','padma'),
						'default' => false,
						'tooltip' => __('Enabling this option will exclude the current Post from the pinboard, usefull when are creating a "related post" block.','padma')
					),

					'order-heading' => array(
						'name' 	=> 'order-heading',
						'type' 	=> 'heading',
						'label' => __('Order','padma'),
					),
					'order-by' => array(
						'type' 		=> 'select',
						'name' 		=> 'order-by',
						'label' 	=> __('Order By','padma'),
						'tooltip' 	=> '',
						'options' 	=> array(
							'date' 	=> __('Date','padma'),
							'title' => __('Title','padma'),
							'rand' 	=> __('Random','padma'),
							'ID' 	=> 'ID'
						)
					),
					'order' => array(
						'type' => 'select',
						'name' => 'order',
						'label' => __('Order','padma'),
						'tooltip' => '',
						'options' => array(
							'desc' => __('Descending','padma'),
							'asc' => __('Ascending','padma'),
						)
					)
				),

				'pagination' => array(
					'paginate' => array(
						'type' => 'checkbox',
						'name' => 'paginate',
						'label' => __('Paginate Pins','padma'),
						'default' => true,
						'tooltip' => __('Enabling pagination adds buttons to the bottom of the pin board to go to the next/previous page.  <strong>Note:</strong> If infinite scrolling is enabled, pagination will be hidden.','padma')
					),

					'enumerate' => array(
						'type' => 'checkbox',
						'name' => 'enumerate',
						'label' => __('Enumerate pagination','padma'),
						'default' => false,
						'tooltip' => __('If pagination is displayed, enabling this will also show page number navigation.','padma')
					),

					'infinite-scroll' => array(
						'type' => 'checkbox',
						'name' => 'infinite-scroll',
						'label' => __('Infinite Scrolling','padma'),
						'default' => true,
						'tooltip' => __('Infinite scrolling allows your visitors to view all of your pins without the need for them to click a button to continue to the next page.  The pins will be loaded automatically simply by scrolling.','padma')
					)
				),

				'text' => array(
					'show-titles' => array(
							'type' => 'checkbox',
							'name' => 'show-titles',
							'label' => __('Show Titles','padma'),
							'default' => true
						),

					'titles-position' => array(
							'type' => 'select',
							'name' => 'titles-position',
							'label' => __('Titles position','padma'),
							'default' => 'below',
							'options' => array('above' => __('Above','padma'),'below' => __('Below','padma') )
						),

					'titles-link-to-post' => array(
							'type' => 'checkbox',
							'name' => 'titles-link-to-post',
							'label' => __('Titles link to post','padma'),
							'default' => true,
							'tooltip' => __('Open the post when the user clicks on the title','padma')
						),

					'content-to-show' => array(
							'type' => 'select',
							'name' => 'content-to-show',
							'label' => __('Content To Show','padma'),
							'options' => array(
								'' => __('&ndash; Do Not Show Content &ndash;','padma'),
								'excerpt' => __('Excerpts','padma'),
								'content' => __('Full Content','padma')
							),
							'default' => 'excerpt',
							'tooltip' => __('The content is the written text or HTML for the entry.  This is edited in the WordPress admin panel.','padma')
						),

					'show-text-if-no-image' => array(
							'type' => 'checkbox',
							'name' => 'show-text-if-no-image',
							'label' => __('Only show content when no featured image','padma'),
							'default' => false,
							'tooltip' => __('If enabled, regardless of the content chosen in <em>Content to Show</em> will only show content for pins with no featured image.','padma')
						),

				),

				'meta' => array(

					'show-author' 	=> array(
							'type' 		=> 'checkbox',
							'name' 		=> 'show-author',
							'label' 	=> __('Meta: Show Author "byline"','padma'),
							'default' 	=> false,
							'tooltip' 	=> __('<strong>Example:</strong> <em>by</em> Author Name','padma')
						),

					'show-category' => array(
							'type' 		=> 'checkbox',
							'name' 		=> 'show-category',
							'label' 	=> __('Meta: Show Categories','padma'),
							'default' 	=> false
						),

					'show-tags' => array(
							'type' 		=> 'checkbox',
							'name' 		=> 'show-tags',
							'label' 	=> __('Meta: Show Tags','padma'),
							'default' 	=> false
						),

					'show-post-type' => array(
							'type' 		=> 'checkbox',
							'name' 		=> 'show-post-type',
							'label' 	=> __('Meta: Show Post Type','padma'),
							'default' 	=> false
						),

					'show-datetime' => array(
							'type' 		=> 'checkbox',
							'name' 		=> 'show-datetime',
							'label' 	=> __('Meta: Show Date/Time','padma'),
							'default' 	=> false
						),

					'datetime-verb' => array(
							'type' 		=> 'text',
							'name' 		=> 'datetime-verb',
							'label' 	=> __('Meta: Posted Verb','padma'),
							'default' 	=> __('Posted','padma'),
							'tooltip'	=> __('The posted verb will be placed before the time.  For instance, you may want to use "Listed" for real estate rather than "Posted"','padma')
						),
				),

				'images' => array(
					'show-images' => array(
						'type' => 'checkbox',
						'name' => 'show-images',
						'label' => __('Show Images','padma'),
						'default' => true,
					),
					'images-click-action' => array(
						'type' => 'select',
						'name' => 'image-click-action',
						'label' => __('Image click action','padma'),
						'default' => 'link',
						'tooltip' => __('Choose the action when user clicks on an image.','padma'),
						'options' => array(
							'post'  => __('Open post','padma'),
							'popup' => __('Popup original image','padma'),
							'none'  => __('Do nothing','padma')
						)
					),

					'crop-vertically' => array(
						'type' => 'checkbox',
						'name' => 'crop-vertically',
						'label' => __('Crop Vertically','padma'),
						'default' => false,
						'tooltip' => __('Trim all images to have the same height.  The trimmed/cropped height is roughly 75% of the width.','padma')
					)
				),

				'custom-fields' => array(),

				'effects' => array(
					'hover-focus' => array(
						'type' => 'checkbox',
						'name' => 'hover-focus',
						'label' => __('Hover Focus','padma'),
						'default' => false,
						'tooltip' => __('If enabled, the hovered pin will be focused while all others will be faded out.','padma')
					)
				),

				'social' => array(
					'show-pinterest-button' => array(
						'type' => 'checkbox',
						'name' => 'show-pinterest-button',
						'label' => __('Pinterest: Show "Pin It" Button','padma'),
						'default' => false,
						'tooltip' => __('Show a Pinterest "Pin It" button inside of the images.','padma'),
					),

					'show-twitter-button' => array(
						'type' => 'checkbox',
						'name' => 'show-twitter-button',
						'label' => __('Twitter: Show Tweet Button','padma'),
						'default' => false,
						'tooltip' => __('Show a tweet button either inside of the post image or by the title.','padma'),
					),

					'twitter-username' => array(
						'type' => 'text',
						'name' => 'twitter-username',
						'label' => __('Twitter: Your Username','padma')
					),

					'twitter-hashtag' => array(
						'type' => 'text',
						'name' => 'twitter-hashtag',
						'label' => __('Twitter: Hashtag to put in tweets (Optional)','padma')
					),

					'show-facebook-button' => array(
						'type' => 'checkbox',
						'name' => 'show-facebook-button',
						'label' => __('Facebook: Show Like/Share Button','padma'),
						'default' => false,
						'tooltip' => __('Show a Facebook share/like button either inside of the post image or by the title.','padma'),
					),

					'facebook-button-verb' => array(
						'type' => 'select',
						'label' => __('Facebook: Button Verb','padma'),
						'name' => 'facebook-button-verb',
						'options' => array(
							'like' => __('Like','padma'),
							'recommend' => __('Recommend','padma')
						),
						'default' => 'like'
					)
				)
			);
		}


		public function modify_arguments($args = false) {

			$block = $args['block'];

			$this->taxonomy_list 	= self::get_taxonomy_list();
			$this->inputs['query-filters']['taxonomies']['options'] = $this->taxonomy_list;

			$tax_slug 			= PadmaBlockAPI::get_setting($block, 'taxonomies', 'category');
			$this->terms_list 	= self::get_tax_terms($tax_slug);

			$this->inputs['query-filters']['categories'] = array(
				'type' 		=> 'multi-select',
				'name' 		=> 'categories',
				'label' 	=> __('Terms','padma'),
				'default' 	=> '',
				'options' 	=> $this->terms_list[$tax_slug],
				'tooltip' 	=> __('Filter the pins that are shown by the selected taxonomy\'s terms.','padma')
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


			/**
			 *
			 * Custom Fields support
			 *
			 */


			$post_types = $custom_fields = array();

			if( !empty($this->block['settings']['mode']) && $this->block['settings']['mode'] == 'custom' ){

				if( isset($this->block['settings']['post-type']) )
					$post_types = $this->block['settings']['post-type'];
				else
					$post_types = array('post');

			}else{
				$post_types = get_post_types();
			}

			$custom_fields = PadmaQuery::get_meta($post_types);		

			if(count($custom_fields)==0){

				if($this->block['settings']['mode'] == 'custom')
					$this->tab_notices['custom-fields'] = __('The selected post type does not have custom fields.','padma');
				else
					$this->tab_notices['custom-fields'] = __('There is not custom fields to show.','padma');

			}else{

				$inputs = array();

				foreach ($custom_fields as $post_type => $fields) {

					$heading = 'custom-fields-'.$post_type.'-heading';

					$inputs[$heading] = array(
						'name' => $heading,
						'type' => 'heading',
						'label' => 'Custom Fields for: "' . $post_type . '".'
					);

					foreach ($fields as $field_name => $posts_total) {

						// Custom field name
						$name = 'custom-field-show-' . $post_type . '-' . $field_name;

						// Custom field position
						$label = 'custom-field-label-' . $post_type . '-' . $field_name;					

						// Custom field position
						$position = 'custom-field-position-' . $post_type . '-' . $field_name;

						// Custom field input
						$inputs[$name] = array(
							'type' => 'checkbox',
							'name' => $name,
							'label' => 'Show "' . $field_name .'"',
							'tooltip' => 'Check this to allow show ' . $field_name,
							'default' => false,
							'toggle'    => array(
								'false' => array(
									'hide' => array(
										'#input-' . $position,
										'#input-' . $label
									)
								),
								'true' => array(
									'show' => array(
										'#input-' . $position,
										'#input-' . $label
									)
								)
							)
						);					

						// Custom field label input
						$inputs[$label] = array(
							'type' => 'text',
							'name' => $label,
							'label' => '"'.$field_name .'" label',
							'default' => '',
						);

						// Custom field position input
						$inputs[$position] = array(
							'type' => 'select',
							'name' => $position,
							'label' => '"'.$field_name .'" position',
							'default' => 'below',
							'options' => array(
								'above' => 'Above',
								'before-content' => 'Before content',
								'below' => 'Below'
							)
						);

					}
				}

				$this->inputs['custom-fields'] = $inputs;

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
					$terms[$key]['none'] = __('No terms found for this taxonomy','padma');
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

}
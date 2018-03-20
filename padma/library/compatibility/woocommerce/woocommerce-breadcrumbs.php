<?php
add_filter('padma_breadcrumbs', 'padma_wc_breadcrumbs');
function padma_wc_breadcrumbs($breadcrumbs) {

	/* Product Archive Page */
	if ( is_post_type_archive('product') && get_option('page_on_front') !== woocommerce_get_page_id('shop') ) {

		$shop_page_id = woocommerce_get_page_id('shop');

		$shop_name = $shop_page_id ? get_the_title($shop_page_id) : ucwords(get_option('woocommerce_shop_slug'));

		if ( is_search() ) {

			$breadcrumbs[] = __('Search results for &ldquo;', 'woocommerce') . get_search_query();
			$breadcrumbs[get_post_type_archive_link('product')] = $shop_name;

		} else {

			$breadcrumbs[get_post_type_archive_link('product')] = $shop_name;

		}

	}
	/* End Product Archive Page */

	/* Shop Taxonomy Archives */
	if ( is_tax('product_cat') || is_tax('product_tag') ) {

		$shop_url = get_option('woocommerce_prepend_shop_page_to_urls');
		$shop_page_id = woocommerce_get_page_id('shop');
		$shop_title = get_the_title($shop_page_id);

		if ( 'yes' == $shop_url && $shop_page_id && get_option('page_on_front') !== $shop_page_id )
			$breadcrumbs[get_permalink($shop_page_id)] = $shop_title;

	}

	/* Shop Categories */
	if ( is_tax( 'product_cat' ) ) {

		$term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));

		/* Get parent categories */
		$parents = array();
		$parent = $term->parent;

		while ( $parent ) {

			$parents[] = $parent;

			$new_parent = get_term_by('id', $parent, get_query_var('taxonomy'));
			$parent = $new_parent->parent;

		}

		/* Output parent categories */
		if ( !empty($parents) ) {

			$parents = array_reverse($parents);

			foreach ( $parents as $parent ) {

				$item = get_term_by('id', $parent, get_query_var('taxonomy'));

				$breadcrumbs[get_term_link($item->slug, 'product_cat')] = $item->name;

			}

		}

		/* Add current category */
		$breadcrumbs[] = single_term_title('', false);

	}

	/* Shop Tags */
	if ( is_tax('product_tag') ) {

		$breadcrumbs[] = __('Products tagged &ldquo;', 'pu_sf_wooc') . single_term_title('', false) . _x('&rdquo;', 'endquote', 'pu_sf_wooc');

	}
	/* End Shop Taxonomy Archives */

	/* Single Product */
	if ( is_singular('product') ) {

		/* Remove the current title at end of breadcrumbs.  This is easier than trying to put the following items before this item being removed */
		array_pop($breadcrumbs);

		global $post;

		/* Shop prefix */
		$shop_url = get_option('woocommerce_prepend_shop_page_to_products');
		$shop_page_id = woocommerce_get_page_id('shop');
		$shop_title = get_the_title($shop_page_id);

		if ( 'yes' == $shop_url && $shop_page_id && get_option('page_on_front') !== $shop_page_id )
			$breadcrumbs[get_permalink($shop_page_id)] = $shop_title;

		/* Product categories */
		if ( $terms = wp_get_object_terms($post->ID, 'product_cat') ) {

			$term = current($terms);
			$parents = array();
			$parent = $term->parent;

			/* Get parent categories */
			while ( $parent ) {

				$parents[] = $parent;

				$new_parent = get_term_by('id', $parent, 'product_cat');
				$parent = $new_parent->parent;

			}

			/* Output parent categories */
			if ( !empty($parents) ) {

				$parents = array_reverse($parents);

				foreach ( $parents as $parent ) {

					$item = get_term_by('id', $parent, 'product_cat');
					$breadcrumb[get_term_link($item->slug, 'product_cat')] = $item->name;

				}

			}

			/* Output main category */
			$breadcrumbs[get_term_link($term->slug, 'product_cat')] = $term->name;

		}

		/* Current product */
		$breadcrumbs[] = get_the_title();

	}
	/* End Single Product */

	return $breadcrumbs;

}
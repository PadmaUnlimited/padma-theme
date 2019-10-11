<?php
class PadmaResponsiveGridDynamicMedia {


	static function content() {

		$content = self::computers();	
		$content .= self::generic_mobile();	
		$content .= self::ipad_landscape();
		$content .= self::ipad_portrait();
		$content .= self::smartphones();

		return apply_filters('padma_responsive_grid_css', $content);

	}


	static function computers() {

		return '
			/* --- Computers (Laptops/Desktops) --- */
			@media only screen and (min-width: 1024px) {

				/* Responsive Block Hiding */
				.responsive-block-hiding-device-computers {
					display: none !important;
				}

			}
		';

	}


	static function generic_mobile() {

		return '
			/* --- Generic Mobile --- */
			@media only screen and (max-width: 1024px) {

				/* Take the minimum height off of blocks. */
				.responsive-grid-active .block {
					min-height: inherit !important;
					height: auto !important;
				}

				.responsive-grid-active .block img,
				.responsive-grid-active .block .wp-caption {
					max-width: 100%;
					height: auto;
				}

				.responsive-grid-active .block-type-footer p.footer-responsive-grid-link-container {
					display: block;
				}

				.responsive-grid-active .block-type-image img {
					position: static !important;
				}

			}
		';

	}


	static function ipad_landscape() {

		return '
			/* --- iPad Landscape --- */
			@media only screen and (min-width : 600px) and (max-width: 1024px) and (orientation : landscape) {

				/* Responsive Block Hiding */
				.responsive-block-hiding-device-tablets-landscape {
					display: none !important;
				}

			}
		';

	}


	static function ipad_portrait() {

		return '
			/* --- iPad Portrait --- */
			@media only screen and (min-width : 600px) and (max-width : 1024px) and (orientation : portrait) {

				/* Responsive Block Hiding */
				.responsive-block-hiding-device-tablets-portrait {
					display: none !important;
				}

			}
		';

	}


	static function smartphones() {

		return '
			/* --- Smartphones and small Tablet PCs --- */
			@media only screen and (max-width : 600px) {

				/* Remove wrapper margins where necessary. Remove top margin from top wrapper as well as side margins */
					.responsive-grid-active div.wrapper:first-child { 
						margin-top: 0 !important; 
					}

					.responsive-grid-active div.wrapper {
						margin-left: 0 !important;
						margin-right: 0 !important;
					}

				/* Set all blocks/columns to be 100% width */
				.responsive-grid-active .block, .responsive-grid-active .row, .responsive-grid-active .column {
					width: 100% !important;
					margin-left: 0 !important;
					margin-right: 0 !important;
				}

				/* Responsive Block Hiding */
				.responsive-block-hiding-device-smartphones {
					display: none !important;
				}

				/* Navigation Block */
					.responsive-grid-active .block-type-navigation {
						height: auto;
					}

					.responsive-grid-active .block-type-navigation .selectnav { display: block; }
					.responsive-grid-active .block-type-navigation ul.menu.selectnav-active { display: none; }
				/* End Navigation Block */

				/* Content Block */
					.responsive-grid-active .block-type-content a.post-thumbnail {
						width: 100%;
						margin: 20px 0;
						text-align: center;
					}

						.responsive-grid-active .block-type-content a.post-thumbnail img {
							max-width: 100%;
							height: auto;
						}

					.responsive-grid-active .block-type-content .loop-navigation {
						text-align: center;
					}

						.responsive-grid-active .block-type-content .loop-navigation .nav-previous, 
						.responsive-grid-active .block-type-content .loop-navigation .nav-next {
							float: none;
							margin: 0 10px;
						}

						.responsive-grid-active .block-type-content .loop-navigation .nav-next {
							margin-top: 20px;
						}
				/* End Content Block */

				/* Footer Block */
				.responsive-grid-active .block-type-footer div.footer > * {
					clear: both;
					float: none;
					display: block;
					margin: 15px 0;
					text-align: center;
				}
				/* End Footer Block */

			}
		';

	}


	static function fitvids() {

		return 'if(window.jQuery){ jQuery(document).ready(function() { jQuery(document).fitVids(); }); }';

	}


}
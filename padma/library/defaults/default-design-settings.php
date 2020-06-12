<?php
global $padma_core_default_element_data;

$padma_core_default_element_data = array(
	/* Defaults */
	'default-text' => array(
		'properties' => array(
			'font-size' => '14',
			'font-family' => 'arial',
			'line-height' => '100',
			'color' => '555555'
		)
	),

	'default-hyperlink' => array(
		'properties' => array(
			'color' => '555555'
		)
	),

	'default-blockquote' => array(
		'properties' => array(
			'color' => '666666',
			'padding-top' => '5',
			'padding-right' => '0',
			'padding-bottom' => '5',
			'padding-left' => '25',
			'border-left-width' => '2',
			'border-style' => 'solid',
			'border-color' => '666666',
			'margin-top' => '15',
			'margin-right' => '0',
			'margin-bottom' => '15',
			'margin-left' => '20'

		)
	),

	'default-block' => array(
		'properties' => array(
			'overflow' => 'hidden',
			'margin-bottom' => '10'
		)
	),

	'block-title' => array(
		'properties' => array(
			'font-size' => '22',
			'line-height' => '150'
		)
	),

	'block-subtitle' => array(
		'properties' => array(
			'font-size' => '14',
			'font-styling' => 'italic',
			'color'	=> '999999'
		)
	),

	/* Structure */
	'body' => array(
		'properties' => array(
			'background-color' => 'dddddd'
		)
	),

	'wrapper' => array(
		'properties' => array(
			'background-color' => 'ffffff',
			'padding-top' => '15',
			'padding-right' => '15',
			'padding-bottom' => '15',
			'padding-left' => '15',
			'box-shadow-color' => '',
			'box-shadow-blur' => '0',
			'box-shadow-horizontal-offset' => '0',
			'box-shadow-vertical-offset' => '0',
			'margin-top' => '0',
			'margin-bottom' => '0'
		)
	),

	/* Header Block */
	'block-header-site-title' => array(
		'properties' => array(
			'color' => '222222',
			'font-size' => '34',
			'line-height' => '100',
			'text-decoration' => 'none',
			'text-decoration-line' => 'none',
			'margin-top' => '20',
			'margin-right' => '10',
			'margin-bottom' => '0',
			'margin-left' => '10'
		)
	),

	'block-header-site-tagline' => array(
		'properties' => array(
			'color' => '999999',
			'font-size' => '15',
			'line-height' => '120',
			'font-styling' => 'italic',
			'margin-top' => '10',
			'margin-right' => '10',
			'margin-bottom' => '20',
			'margin-left' => '10'
		)
	),

	/* Navigation Block */
	'block-navigation' => array(
		'properties' => array(
			'border-top-width' => '0',
			'border-bottom-width' => '0',
			'border-left-width' => '0',
			'border-right-width' => '0',
			'border-color' => 'eeeeee',
			'border-style' => 'solid',
			'overflow' => 'visible'
		)
	),

	'block-navigation-menu-item' => array(
		'properties' => array(
			'text-decoration' => 'none',
			'text-decoration-line' => 'none',
			'color' => '888888',
			'capitalization' => 'uppercase',
			'padding-right' => '15',
			'padding-left' => '15'
		),
		'special-element-state' => array(
			'selected' => array(
				'color' => '222222'
			),
			'hover' => array(
				'color' => '555555'
			)
		)
	),

	'block-navigation-sub-nav-menu' => array(
		'properties' => array(
			'background-color' => 'eeeeee'
		)
	),

	/* Widget Block */
	'block-widget-area-widget' => array(
		'properties' => array(
			'line-height' => '150',
			'padding-top' => '5',
			'padding-right' => '10',
			'padding-bottom' => '5',
			'padding-left' => '10',
			'margin-top' => '15'
		)
	),

	'block-widget-area-widget-title' => array(
		'properties' => array(
			'font-size' => '13',
			'border-style' => 'solid',
			'border-top-width' => '1',
			'border-bottom-width' => '1',
			'border-left-width' => '0',
			'border-right-width' => '0',
			'border-color' => 'eeeeee',
			'letter-spacing' => '1',
			'capitalization' => 'uppercase',
			'line-height' => '250',
			'color' => '111111',
			'margin-bottom' => '10'
		)
	),

	'block-widget-area-widget-links' => array(
		'properties' => array(
			'color' => '333333'
		)
	),

	/* Content Block */
	'block-content-entry-container' => array(
		'properties' => array(
			'border-style' => 'solid',
			'border-top-width' => '0',
			'border-bottom-width' => '1',
			'border-left-width' => '0',
			'border-right-width' => '0',
			'border-color' => 'efefef',
			'padding-bottom' => '30'
		)
	),

	'block-content-title' => array(
		'properties' => array(
			'font-size' => '24',
			'color' => '333333',
			'line-height' => '130'
		)
	),

	'block-content-archive-title' => array(
		'properties' => array(
			'font-size' => '24',
			'color' => '555555',
			'line-height' => '110',
			'border-bottom-width' => '1',
			'border-color' => 'eeeeee',
			'border-style' => 'solid',
			'padding-bottom' => '15'
		)
	),

	'block-content-entry-meta' => array(
		'properties' => array(
			'line-height' => '120',
			'color' => '818181'
		)
	),

	'block-content-entry-content' => array(
		'properties' => array(
			'color' => '555555',
			'font-size' => '14',
			'line-height' => '180'
		)
	),

	'block-content-heading' => array(
		'properties' => array(
			'font-size' => '20',
			'line-height' => '180'
		)
	),

	'block-content-sub-heading' => array(
		'properties' => array(
			'font-size' => '16',
			'line-height' => '180'
		)
	),

	'block-content-more-link' => array(
		'properties' => array(
			'background-color' => 'eeeeee',
			'text-decoration' => 'none',
			'text-decoration-line' => 'none',
			'border-top-left-radius' => '4',
			'border-top-right-radius' => '4',
			'border-bottom-right-radius' => '4',
			'border-bottom-left-radius' => '4',
			'padding-top' => '2',
			'padding-right' => '6',
			'padding-bottom' => '2',
			'padding-left' => '6'
		),
		'special-element-state' => array(
			'hover' => array(
				'background-color' => 'e7e7e7'
			)
		)
	),

	'block-content-loop-navigation-link' => array(
		'properties' => array(
			'background-color' => 'e1e1e1',
			'text-decoration' => 'none',
			'text-decoration-line' => 'none',
			'border-top-left-radius' => '4',
			'border-top-right-radius' => '4',
			'border-bottom-right-radius' => '4',
			'border-bottom-left-radius' => '4',
			'padding-top' => '4',
			'padding-right' => '8',
			'padding-bottom' => '4',
			'padding-left' => '8',
			'line-height' => '130'
		),
		'special-element-state' => array(
			'hover' => array(
				'background-color' => 'd5d5d5'
			)
		)
	),

	'block-content-post-thumbnail' => array(
		'properties' => array(
			'border-top-width' => '1',
			'border-right-width' => '1',
			'border-bottom-width' => '1',
			'border-left-width' => '1',
			'border-color' => 'eeeeee',
			'border-style' => 'solid',
			'padding-top' => '3',
			'padding-right' => '3',
			'padding-bottom' => '3',
			'padding-left' => '3'
		)
	),

	'block-content-comments-area-headings' => array(
		'properties' => array(
			'color' => '333333',
			'font-size' => '18',
			'line-height' => '130'
		)
	),

	'block-content-comment-container' => array(
		'properties' => array(
			'padding-left' => '64'
		)
	),

	'block-content-comment-author' => array(
		'properties' => array(
			'font-size' => '18',
			'line-height' => '120'
		)
	),

	'block-content-comment-meta' => array(
		'properties' => array(
			'color' => '888888',
			'font-size' => '14'
		)
	),

	'block-content-comment-body' => array(
		'properties' => array(
			'font-size' => '14',
			'line-height' => '170'
		)
	),

	'block-content-comment-reply-link' => array(
		'properties' => array(
			'font-size' => '12',
			'background-color' => 'eeeeee',
			'text-decoration' => 'none',
			'text-decoration-line' => 'none',
			'border-top-left-radius' => '4',
			'border-top-right-radius' => '4',
			'border-bottom-right-radius' => '4',
			'border-bottom-left-radius' => '4',
			'padding-top' => '3',
			'padding-right' => '6',
			'padding-bottom' => '3',
			'padding-left' => '6'
		),
		'special-element-state' => array(
			'hover' => array(
				'background-color' => 'e7e7e7'
			)
		)
	),

	'block-content-comment-form-input-label' => array(
		'properties' => array(
			'font-size' => '14',
			'line-height' => '220',
			'color' => '888888'
		)
	),


	/* Slider */
	'block-slider-slider-container' => array(
		'properties' => array(
			'overflow' => 'visible',
			'margin-bottom' => '30'
		)
	),

	'block-slider-slider-viewport' => array(
		'properties' => array(
			'overflow' => 'hidden'
		)
	),

	'block-slider-slider-caption' => array(
		'properties' => array(
			'background-color' => 'rgba(0, 0, 0, 0.6)',
			'color' => 'ffffff',
			'font-size' => '14',
			'line-height' => '150',
			'padding-top' => '20',
			'padding-right' => '20',
			'padding-bottom' => '20',
			'padding-left' => '20',
			'position' => 'absolute',
			'text-align' =>'center'
		)
	),

	'block-slider-slider-paging' => array(
		'properties' => array(
			'position' => 'absolute',
			'text-align' => 'center'
		)
	),

	'block-slider-slider-direction-nav-link' => array(
		'properties' => array(
			'margin-top' => '-20',
			'position' => 'absolute',
			'background-image' => padma_url() . '/library/blocks/slider/assets/bg_direction_nav.png',
			'background-repeat' => 'no-repeat',
			'background-position' => 'left top'
		)
	),

	'block-slider-slider-direction-nav-next' => array(
		'properties' => array(
			'background-position' => 'right top'
		)
	),

	'block-slider-slider-paging-link' => array(
		'properties' => array(
			'background-color' => 'rgba(0,0,0,0.5)',
			'border-top-left-radius' => '20',
			'border-top-right-radius' => '20',
			'border-bottom-right-radius' => '20',
			'border-bottom-left-radius' => '20',
			'margin-left' => '2',
			'margin-right' => '2'
		),
		'special-element-state' => array(
				'hover' => array(
					'background-color' => 'rgba(0,0,0,0.7)'
				),
				'active' => array(
					'background-color' => 'rgba(0,0,0,0.9)'
				)
			)
	),


	/* Text Block */
		'block-text-heading' => array(
			'properties' => array(
				'font-size' => '20',
				'line-height' => '180'
			)
		),

		'block-text-sub-heading' => array(
			'properties' => array(
				'font-size' => '16',
				'line-height' => '180'
			)
		),
	/* End Text Block */


	/* Pin Board */
		'block-pin-board-pin' => array(
			'properties' => array(
				'padding-top' => 1,
				'padding-right' => 1,
				'padding-bottom' => 1,
				'padding-left' => 1,

				'background-color' => 'ffffff',

				'border-color' => 'eeeeee',
				'border-style' => 'solid',
				'border-top-width' => 1,
				'border-right-width' => 1,
				'border-bottom-width' => 1,
				'border-left-width' => 1,

				'box-shadow-color' => 'eee',
				'box-shadow-blur' => 3,
				'box-shadow-horizontal-offset' => 0,
				'box-shadow-vertical-offset' => 2
			)
		),

		'block-pin-board-pin-title' => array(
			'properties' => array(
				'padding-top' => 15,
				'padding-right' => 15,
				'padding-left' => 15,
				'font-size' => 18,
				'line-height' => 120,
				'text-decoration' => 'none',
				'text-decoration-line' => 'none',
			),
			'special-element-state' => array(
				'hover' => array(
					'text-decoration' => 'underline',
					'text-decoration-line' => 'underline',
				)
			)
		),

		'block-pin-board-pin-text' => array(
			'properties' => array(
				'font-size' => 12,
				'line-height' => 150,

				'padding-right' => 15,
				'padding-left' => 15
			)
		),

		'block-pin-board-pin-meta' => array(
			'properties' => array(
				'font-size' => 12,
				'line-height' => 120,

				'padding-right' => 15,
				'padding-left' => 15,

				'color' => '888888'
			)
		),

		'block-pin-board-pagination-button' => array(
			'properties' => array(
				'text-decoration' => 'none',
				'text-decoration-line' => 'none',
				'background-color' => 'eeeeee',

				'border-top-left-radius' => 4,
				'border-top-right-radius' => 4,
				'border-bottom-right-radius' => 4,
				'border-bottom-left-radius' => 4,

				'padding-top' => 5,
				'padding-right' => 9,
				'padding-bottom' => 5,
				'padding-left' => 9
			),
			'special-element-state' => array(
				'hover' => array(
					'background-color' => 'e7e7e7'
				)
			)
		),
	/* End Pin Board */


	/* Footer */
	'block-footer' => array(
		'properties' => array(
			'border-top-width' => '0',
			'border-right-width' => '0',
			'border-bottom-width' => '0',
			'border-left-width' => '0',
			'border-color' => 'eeeeee',
			'border-style' => 'solid'
		)
	),

	'block-footer-copyright' => array(
		'properties' => array(
			'color' => '666666'
		)
	),

	'block-footer-padma-attribution' => array(
		'properties' => array(
			'color' => '666666'
		)
	),

	'block-footer-administration-panel' => array(
		'properties' => array(
			'color' => '666666'
		)
	),

	'block-footer-go-to-top' => array(
		'properties' => array(
			'color' => '666666'
		)
	),

	'block-footer-responsive-grid-link' => array(
		'properties' => array(
			'color' => '666666'
		)
	)
);

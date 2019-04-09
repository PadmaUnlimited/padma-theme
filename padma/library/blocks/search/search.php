<?php
padma_register_block('PadmaSearchBlock', padma_url() . '/library/blocks/search');

class PadmaSearchBlock extends PadmaBlockAPI {


	public $id = 'search';
	public $name = 'Search';
	public $fixed_height = false;
	public $description = 'This will output the default search form';
	public $options_class = 'PadmaSearchBlockOptions';
	public $categories 	= array('core','content', 'forms');


	function setup_elements() {

		$this->register_block_element(array(
			'id' => 'search_wrap',
			'name' => 'Search Form',
			'selector' => '.search-form'
		));

		$this->register_block_element(array(
			'id' => 'search_input',
			'name' => 'Search Input',
			'selector' => '.search-form input.field'
		));

		$this->register_block_element(array(
			'id' => 'search_button',
			'name' => 'Search Button',
			'selector' => '.search-form .submit'
		));

	}


	function content($block) {

		$swp_engine = $this->get_setting( $block, 'swp-engine' );

		if ( $swp_engine && function_exists('SWP') ) {

			$search_query = isset( $_REQUEST[ 'swpquery_' . $swp_engine ] ) ? sanitize_text_field( $_REQUEST[ 'swpquery_' . $swp_engine ] ) : '';
			$action = get_permalink();
			$input_name = 'swpquery_' . $swp_engine;

		} else {
			$search_query = get_search_query();
			$action = home_url( '/' );
			$input_name = 's';

		}

		$button_hidden_class = parent::get_setting( $block, 'show-button', true ) ? 'search-button-visible' : 'search-button-hidden';

		echo '<form method="get" id="searchform-' . $block['id'] . '" class="search-form ' . $button_hidden_class . '" action="' . esc_html( $action ) . '">' . "\n";

			if ( parent::get_setting( $block, 'show-button', true ) ) {
				echo '<input type="submit" class="submit" name="submit" id="searchsubmit-' . $block['id'] . '" value="' . esc_attr( parent::get_setting( $block, 'search-button', 'Search' ) ) . '" />' . "\n";
			}

			printf('<div><input id="search-' . $block['id'] . '" class="field" type="text" name="%1$s" value="%2$s" placeholder="%3$s" /></div>' . "\n",
				$input_name,
				$search_query ? esc_attr($search_query) : '',
				esc_attr(parent::get_setting($block, 'search-placeholder', 'Enter search term and hit enter.'))
			);

		echo '</form>' . "\n";

	}

}


class PadmaSearchBlockOptions extends PadmaBlockOptionsAPI {

	public $tabs = array(
		'general' => 'General'
	);

	public $inputs = array(
		'general' => array(
			'search-placeholder' => array(
				'name' => 'search-placeholder',
				'label' => 'Input Text Placeholder',
				'type' => 'text',
				'tooltip' => 'The placeholder is text that will be shown in the Search input and immediately removed after you start typing in the search input.',
				'default' => 'Enter search term and hit enter.'
			),

			'show-button' => array(
				'name'    => 'show-button',
				'label'   => 'Show Search Button',
				'type'    => 'checkbox',
				'default' => true,
				'toggle' => array(
					'true' => array(
						'show' => '#input-search-button'
					),
					'false' => array(
						'hide' => '#input-search-button'
					)
				)
			),

			'search-button' => array(
				'name' => 'search-button',
				'label' => 'Button Text',
				'type' => 'text',
				'tooltip' => 'This will update the Search button text.',
				'default' => 'Search'
			)
		)
	);

	public function modify_arguments( $args ) {

		if ( class_exists( 'SWP_Query' ) ) {

			$this->inputs['general']['swp-engine'] = array(
					'type'    => 'select',
					'name'    => 'swp-engine',
					'label'   => 'SearchWP Engine',
					'options' => 'get_swp_engines()',
					'tooltip' => 'If you wish to display the results of a supplemented SearchWP engine, please select the engine here.',
					'default' => ''
			);

		}

	}

	function get_swp_engines() {

		$options = array( '&ndash; Select an Engine &ndash;' );

		if ( ! function_exists( 'SWP' ) ) {
			return $options;
		}

		$searcbtp = SWP();

		if ( ! is_array( $searcbtp->settings['engines'] ) ) {
			return $options;
		}

		foreach ( $searcbtp->settings['engines'] as $engine => $engine_settings ) {

			if ( empty( $engine_settings['searcbtp_engine_label'] ) ) {
				continue;
			}

			$options[ $engine ] = $engine_settings['searcbtp_engine_label'];

		}

		return $options;

	}


}
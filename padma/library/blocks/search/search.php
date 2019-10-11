<?php

class PadmaSearchBlock extends PadmaBlockAPI {


	public $id;
	public $name;
	public $fixed_height;
	public $description;
	public $options_class;
	public $categories;


	function __construct(){

		$this->id = 'search';
		$this->name = __('Search','padma');
		$this->fixed_height = false;
		$this->description = __('This will output the default search form','padma');
		$this->options_class = 'PadmaSearchBlockOptions';
		$this->categories 	= array('core','content', 'forms');		

	}

	function setup_elements() {

		$this->register_block_element(array(
			'id' => 'search_wrap',
			'name' => __('Search Form','padma'),
			'selector' => '.search-form'
		));

		$this->register_block_element(array(
			'id' => 'search_input',
			'name' => __('Search Input','padma'),
			'selector' => '.search-form input.field'
		));

		$this->register_block_element(array(
			'id' => 'search_button',
			'name' => __('Search Button','padma'),
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
				echo '<input type="submit" class="submit" name="submit" id="searchsubmit-' . $block['id'] . '" value="' . esc_attr( parent::get_setting( $block, 'search-button', __('Search','padma') ) ) . '" />' . "\n";
			}

			printf('<div><input id="search-' . $block['id'] . '" class="field" type="text" name="%1$s" value="%2$s" placeholder="%3$s" /></div>' . "\n",
				$input_name,
				$search_query ? esc_attr($search_query) : '',
				esc_attr(parent::get_setting($block, 'search-placeholder', __('Enter search term and hit enter.','padma') ) )
			);

		echo '</form>' . "\n";

	}

}


class PadmaSearchBlockOptions extends PadmaBlockOptionsAPI {

	public $tabs;
	public $inputs;


	function __construct(){

		$this->tabs = array(
			'general' => 'General'
		);

		$this->inputs = array(
			'general' => array(
				'search-placeholder' => array(
					'name' => 'search-placeholder',
					'label' => __('Input Text Placeholder','padma'),
					'type' => 'text',
					'tooltip' => __('The placeholder is text that will be shown in the Search input and immediately removed after you start typing in the search input.','padma'),
					'default' => __('Enter search term and hit enter.','padma')
				),

				'show-button' => array(
					'name'    => 'show-button',
					'label'   => __('Show Search Button','padma'),
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
					'label' => __('Button Text','padma'),
					'type' => 'text',
					'tooltip' => 'This will update the Search button text.',
					'default' => 'Search'
				)
			)
		);
	}

	public function modify_arguments( $args ) {

		if ( class_exists( 'SWP_Query' ) ) {

			$this->inputs['general']['swp-engine'] = array(
					'type'    => 'select',
					'name'    => 'swp-engine',
					'label'   => __('SearchWP Engine','padma'),
					'options' => 'get_swp_engines()',
					'tooltip' => __('If you wish to display the results of a supplemented SearchWP engine, please select the engine here.','padma'),
					'default' => ''
			);

		}

	}

	function get_swp_engines() {

		$options = array( __('&ndash; Select an Engine &ndash;','padma') );

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
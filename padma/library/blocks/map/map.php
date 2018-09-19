<?php

padma_register_block('PadmaMapBlock', padma_url() . '/library/blocks/map');



add_action('padma_visual_editor_scripts', array('PadmaMapBlock','map_block_admin_js'), 1);


class PadmaMapBlock extends PadmaBlockAPI {

	public $id 				= 'map';
	public $name 			= 'Map';
	public $options_class 	= 'PadmaMapBlockOptions';
	public $description 	= 'Easily display your locations on a map';
	public $fixed_height 	= true;


	public static function init() {		
		//wp_enqueue_script('padma-map', padma_url() . '/library/blocks/map/js/maps.js' );
		wp_enqueue_script('googlemap.js', '//maps.google.com/maps/api/js?libraries='.$libraries.'&v=quarterly&key=' . parent::get_setting($block, 'api-key'));
		wp_enqueue_script('maplace.js', padma_url() . '/library/blocks/map/js/maplace.js', array('jquery'));		
	}


	public static function map_block_admin_js(){
		wp_enqueue_script('googlemap.js', '//maps.google.com/maps/api/js?libraries='.$libraries.'&v=quarterly&key=' . parent::get_setting($block, 'api-key'));
	}
	public static function dynamic_js($block_id, $block) {

		$markers = parent::get_setting($block, 'markers', array());

		foreach ( $markers as $marker_index => $marker ) {

			if ( empty($markers[$marker_index]['lat']) || empty($markers[$marker_index]['lon']) ) {
				unset($markers[$marker_index]);
				continue;
			}

			$markers[$marker_index]['zoom'] = intval($markers[$marker_index]['zoom']);

		}

		debug($markers);

		$styles_option = '';

		if ( $style = parent::get_setting($block, 'style', '') ) {

			$style_json_path = PADMA_LIBRARY_DIR . '/blocks/map/styles/' . $style . '.json';

			if ( file_exists($style_json_path) ) {

				$style_file_handler = fopen( $style_json_path, 'r' );
				$style_json = fread( $style_file_handler, filesize( $style_json_path ) );
				fclose( $style_file_handler );

				if ( $style_json ) {

					$styles_option = ',
				styles: {
					"' . $style . '": ' . $style_json . '
				}
				';

				}

			}

		}

		if(parent::get_setting($block, 'controls')){
			$controls_on_map = 'true';
		}else{
			$controls_on_map = 'false';			
		}

		//debug($block);
		$js = '
		(function() {
			$(document).ready(function() {
			    new Maplace({
			    	map_div: "#block-' . $block_id . ' .map-block-gmap",
			        locations: ' . json_encode($markers) . ',
			        controls_on_map: '. $controls_on_map .',
			        generate_controls: false,
					map_options: {
						disableDefaultUI: true
					}
					' . $styles_option . '
			    }).Load();
			});

		});';

		return $js;
		/*
		return '
			(function ($){			
				$(document).ready(function() {
				
					var maxTime = 10000;
					var timeGoneBy = 0;

                    var interval = setInterval(function() { 
                                        	
                    	timeGoneBy += 100;
                    	                        
                        if ( typeof google != "undefined" ) {
                        
                            new Maplace({
								map_div: "#block-' . $block_id . ' .map-block-gmap",
								locations: ' . json_encode($markers) . ',
								controls_on_map: false,
								generate_controls: false,
								map_options: {
									disableDefaultUI: true
								}
								' . $styles_option . '
							}).Load();
						
							clearInterval(interval);
                        
                        } else if ( timeGoneBy >= maxTime ) {
                            clearInterval(interval);
                        }

                    }, 100);					
				});

			})(jQuery);
		';*/

	}

	public function content($block) {

		$libraries = 'geometry,places';

		wp_enqueue_script('googlemap.js', '//maps.google.com/maps/api/js?libraries='.$libraries.'&v=quarterly&key=' . parent::get_setting($block, 'api-key'));
		wp_enqueue_script('maplace.js', padma_url() . '/library/blocks/map/js/maplace.js', array('jquery'));


		if(parent::get_setting($block, 'controls'))
			echo '<div id="controls"></div>';
			
		echo '<div class="map-block-gmap"></div>';
		echo '<div id="gmap-dropdown"></div><div id="gmap-list"></div>';

	}

	public function setup_elements() {

		$elements = array(
			array(
				'id' => 'map',
				'name' => 'Map',
				'selector' => '.map-block-gmap',
				'properties' => array('background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'overflow', 'animation', 'sizes', 'effects')
			)
		);

		foreach ( $elements as $element )
			$this->register_block_element( $element );

	}

}


class PadmaMapBlockOptions extends PadmaBlockOptionsAPI {
	
	public $tabs = array(
		'locations' 	=> 'Locations',
		'style' 		=> 'Map Style',
		'settings' 	=> 'Settings',
	);


	public $inputs = array(
		
		'locations' => array(
			array(
				'name' => 'markers',
				'type' => 'repeater',
				'label' => 'Map Marker',
				'tooltip' => '',
				'inputs' => array(
					array(
						'type' => 'text',
						'name' => 'search',
						'label' => 'Search Location',
						'default' => null,
						'callback' => 'autocomplete()'
					),
					array(
						'type' => 'hidden',
						'name' => 'lat',
						'label' => 'Latitude',
						'default' => null,
					),
					array(
						'type' => 'hidden',
						'name' => 'lon',
						'label' => 'Longitude',
						'default' => null,
					),
					array(
						'type' => 'slider',
						'name' => 'zoom',
						'label' => 'Zoom',
						'default' => 14,
						'slider-min' => 0,
						'slider-max' => 20,
						'slider-interval' => 1,
					),
					array(
						'type' => 'wysiwyg',
						'name' => 'html',
						'label' => 'Popup Content',
						'default' => null
					),
				)
			)
		),
		'style' => array(
			array(
				'name' => 'style',
				'type' => 'select',
				'label' => 'Map Style',
				'options' => 'get_styles()'
			)
		),
		'settings' => array(
			array(
				'name' => 'api-key',
				'label' => 'API Key',
				'type' => 'text',
			),
			'controls' => array(
				'name' => 'controls',
				'label' => 'Controls',
				'type' => 'checkbox',
				'default' => false,
			),
		)
	);

	public function autocomplete(){
//		debug($this->block['id']);
		$js = '$(this).keyup(function(){
					var defaultBounds = new google.maps.LatLngBounds(
						new google.maps.LatLng(-33.8902, 151.1759),
						new google.maps.LatLng(-33.8474, 151.2631)
					);

					var input = document.getElementById("#input-'.$this->block['id'].'-search");
					var options = {
						bounds: defaultBounds,
						types: ["address,establishment"]
					};
					autocomplete = new google.maps.places.Autocomplete(input, options);					
		});';
		return $js;
	}
	public $open_js_callback = array();

	public static function get_styles() {

		$path = PADMA_LIBRARY_DIR . '/blocks/map/styles/';

		$results = scandir( $path );

		$styles = array(
			'' => 'Default'
		);

		foreach ( $results as $result ) {
			if ( $result === '.' or $result === '..' or $result === '.DS_Store' ) {
				continue;
			}

			if ( ! is_dir( $path . '/' . $result ) ) {
				$styles[ preg_replace( "/\\.[^.\\s]{3,4}$/", "", $result ) ] = preg_replace( "/\\.[^.\\s]{3,4}$/", "", $result );
			}
		}

		return $styles;

	}
	
	public function modify_arguments($args) {
		/*
		$this->open_js_callback = '


			var mapBlockAutocomplete = function( element ) {

				this.container = $(element);
		        this.init( this.container );
		        this.listen();

		    }

		    mapBlockAutocomplete.prototype = {

		        init: function( selector ) {

		        	var input = $(selector).find("#input-' . $args['block']['id'] . '-search")[0];
		        	var options = {
					  bounds: defaultBounds,
					  types: ["address"]
					};
	    	        var autocomplete = new google.maps.places.Autocomplete(input,options);

	    	        console.log(input);
	    	        console.log(autocomplete);

	    	        google.maps.event.addListener(autocomplete, "place_changed", function () {

	    	            var place = autocomplete.getPlace();

	    	            console.log(place);
	    	            if ( place.hasOwnProperty("geometry") ) {

	    	            	console.log(place.geometry);
	    	            	console.log(place.geometry.location);

	    		            selector.find("#input-' . $args['block']['id'] . '-lat").val(place.geometry.location.lat()).trigger(\'change\');
	    		            selector.find("#input-' . $args['block']['id'] . '-lon").val(place.geometry.location.lng()).trigger(\'change\');

							$(input).trigger(\'keyup\');

	    				}

	    	        });

	    			$(input).css( "width", "260px");

		        },
		        listen: function() {

		        	var that = this;

		        	this.container.on( "click", ".add-group", function() {

		        		setTimeout( function(){

		        			that.init( that.container.next() );

		        		}, 500);

		        	});

		        }

		    };

			$("#block-' . $args['block']['id'] . '-tab .sub-tabs-content-container .repeater-group:not(.repeater-group-template)").each( function() {
				console.log("damm");
				console.log($.data(this, "mapBlockAutocomplete"));
				if ( !$.data(this, "mapBlockAutocomplete") )
					$.data(this, "mapBlockAutocomplete", new mapBlockAutocomplete(this));

			});

		';*/
		
	}
}



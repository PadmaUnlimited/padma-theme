<?php

padma_register_block('PadmaMapBlock', padma_url() . '/library/blocks/map');



//add_action('padma_visual_editor_scripts', array('PadmaMapBlock','map_block_admin_js'), 1);


class PadmaMapBlock extends PadmaBlockAPI {

	public $id 				= 'map';
	public $name 			= 'Map';
	public $options_class 	= 'PadmaMapBlockOptions';
	public $description 	= 'Easily display your locations on a map';
	public $fixed_height 	= true;


	public static function init() {		
		//wp_enqueue_script('padma-map', padma_url() . '/library/blocks/map/js/maps.js' );	
		
	}


	public static function map_block_admin_js(){
		//wp_enqueue_script('googlemap.js', '//maps.google.com/maps/api/js?libraries=geometry,places&v=quarterly&key=' . parent::get_setting($block, 'api-key'));
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

		debug($markers);

		$js = '
			jQuery(document).ready(function() {
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
			});';

		return $js;
	}

	
	public function content($block) {

		debug($block);

		$libraries = 'geometry,places';

		wp_enqueue_script('googlemap.js', '//maps.google.com/maps/api/js?libraries=geometry,places&v=quarterly&key=' . parent::get_setting($block, 'api-key'));
		wp_enqueue_script('maplace.js', padma_url() . '/library/blocks/map/js/maplace.js', array('jquery'));


		if(parent::get_setting($block, 'controls'))
			echo '<div id="controls"></div>';
			
		echo '<div class="map-block-gmap"></div>';
		echo '<div id="gmap-block-'.$block['id'].'"></div><div id="gmap-list"></div>';
		echo '<div id="infowindow-content-block-'.$block['id'].'" style="display: none;">';
		echo '	<img src="" width="16" height="16" id="place-icon">';
		echo '	<span id="place-name"  class="title"></span><br>';
		echo '	<span id="place-address"></span>';
		echo '</div>';

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
			'search-for' => array(
				'name' => 'search-for',
				'label' => 'Search for',
				'type' => 'select',
				'default' => 'establishment',
				'options' => array(
					'establishment' => "Establishments",
					'address' 		=> "Addresses",
					'geocode' 		=> "Geocodes",
					'(regions)' 	=> "Regions",
					'(cities)' 		=> "Cities",
				),
			),
		)
	);

	public function autocomplete(){
		
		$maps_url = '//maps.google.com/maps/api/js?libraries=places,geometry&v=quarterly&key=' . $this->block['settings']['api-key'];

		if(!$this->block['settings']['search-for']){
			$searchFor = 'establishment';
		}else{
			$searchFor = $this->block['settings']['search-for'];
		}
		
		$js = '
				function initMap() {

					var map = new google.maps.Map($i("gmap-'.$this->block['id'].'"), {
					  center: {lat: -33.8688, lng: 151.2195},
					  zoom: 13
					});
					
					var input = document.getElementById("input-'.$this->block['id'].'-search");
					var types = "'.$searchFor.'"
					var strictBounds = false;
					var autocomplete = new google.maps.places.Autocomplete(input);

					autocomplete.bindTo("bounds", map);

					autocomplete.setFields(["address_components", "geometry", "icon", "name"]);

					var infowindow = new google.maps.InfoWindow();
					var infowindowContent = $i("#infowindow-content-'.$this->block['id'].'").html();
					infowindow.setContent(infowindowContent);
					var marker = new google.maps.Marker({
						map: map,
						anchorPoint: new google.maps.Point(0, -29)
					});


					autocomplete.addListener("place_changed", function() {
					  
						infowindow.close();
						marker.setVisible(false);
						var place = autocomplete.getPlace();

						if (!place.geometry) {				    
							window.alert("No details available for input: " + place.name);
							return;
						}

						if (place.geometry.viewport) {
							map.fitBounds(place.geometry.viewport);
						} else {
							map.setCenter(place.geometry.location);
							map.setZoom(17);
						}

						marker.setPosition(place.geometry.location);
						marker.setVisible(true);


						var address = "";
						if (place.address_components) {
							address = [
								(place.address_components[0] && place.address_components[0].short_name || ""),
								(place.address_components[1] && place.address_components[1].short_name || ""),
								(place.address_components[2] && place.address_components[2].short_name || "")
							].join(" ");
						}

						$("#input-'.$this->block['id'].'-lat").val(place.geometry.location.lat());
						$("#input-'.$this->block['id'].'-lon").val(place.geometry.location.lng());

						infowindowContent.children["place-icon"].src = place.icon;
						infowindowContent.children["place-name"].textContent = place.name;
						infowindowContent.children["place-address"].textContent = address;
						infowindow.open(map, marker);

						dataHandleInput($("#input-'.$this->block['id'].'-search"), $("#input-'.$this->block['id'].'-search").val());

					});
					
				}
				$(this).keyup(function(){
					if( $("#block-'.$this->block['id'].'-tab #input-'.$this->block['id'].'-api-key").val() == "" ){

						showNotification({
							id: "maps-block-no-api-key",
							message: "Please setup your Google Maps API Key",
							closeTimer: 5000,
							closable: true,
							error: true
						});
						return;

					}else{

						if(typeof window.google == "undefined"){					

							var script = document.createElement("script");
							script.src = "'.$maps_url.'";
							document.head.appendChild(script);
						
						}else{
							initMap();
						}
					}
				});
			';
			/*
			$(this).keyup(function(){
				if( $("#block-'.$this->block['id'].'-tab #input-'.$this->block['id'].'-api-key").val() == "" ){

					showNotification({
						id: "maps-block-no-api-key",
						message: "Please setup your Google Maps API Key",
						closeTimer: 5000,
						closable: true,
						error: true
					});
					return;

				}else{

					if(typeof window.google == "undefined"){					

						var script = document.createElement("script");
						script.src = "'.$maps_url.'";
						document.head.appendChild(script);
					
					}else{

						var defaultBounds = new window.google.maps.LatLngBounds(
							new window.google.maps.LatLng(-33.8902, 151.1759),
							new window.google.maps.LatLng(-33.8474, 151.2631)
						);

						var input = document.getElementById("input-'.$this->block['id'].'-search");
						var options = {
							bounds: defaultBounds,
							types: ["'.$searchFor.'"]
						};
						
						autocomplete = new google.maps.places.Autocomplete(input, options);
						console.log(autocomplete);
					}
					
				}		
			});';
			*/
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
	}
}



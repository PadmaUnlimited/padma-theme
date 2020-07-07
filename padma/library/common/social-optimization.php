<?php

namespace Padma;

class PadmaSocialOptimization {


	public static function init() {

		add_action('wp_loaded', array(__CLASS__, 'allow_rel_attr'));

		add_filter('user_contactmethods', array(__CLASS__, 'add_padma_contact_methods'));

	}


	public static function allow_rel_attr() {

		global $allowedtags;

		$allowedtags['a']['rel'] = array();

	}


	public static function add_padma_contact_methods($contact_methods) {

		//Add Extra Profile Links
		$contact_methods['twitter'] = 'Twitter URL';
		$contact_methods['facebook'] = 'Facebook URL';
		$contact_methods['google_profile'] = 'Google Profile URL';
		$contact_methods['about_page_URL'] = 'About Page URL';

		return $contact_methods;

	}


}
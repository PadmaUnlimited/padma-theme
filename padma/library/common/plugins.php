<?php

class PadmaPlugins{

	public static function init() {

		/**
		 *
		 * If option "Do not recommend plugin installation" is on, Padma will not recommended installation of plugins "Updater" and "Services"
		 *
		 */

		if( PadmaOption::get( 'do-not-recommend-plugin-installation' ) ) {
			return;
		}

		add_action( 'tgmpa_register', array(__CLASS__,'padma_register_required_plugins'));

	}

	/**
	 * Register the required plugins for this theme.
	 *	
	 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
	 */
	public static function padma_register_required_plugins() {

		$padma_advanced_slug = 'padma-advanced';

		if ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'padma-advanced-premium/padma-advanced.php' ) ) {
			$padma_advanced_slug = 'padma-advanced-premium';
		}
		/*
		 * Array of plugin arrays. Required keys are name and slug.
		 * If the source is NOT from the .org repo, then source is also required.
		 */
		$plugins = array(
			array(
				'name'         => 'Padma Advanced', // The plugin name.
				'slug'         => $padma_advanced_slug, // The plugin slug (typically the folder name).
				'required'     => false, // If false, the plugin is only 'recommended' instead of required.
			),
		);

		/*
		 * Array of configuration settings. Amend each line as needed.
		 *
		 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
		 * strings available, please help us make TGMPA even better by giving us access to these translations or by
		 * sending in a pull-request with .po file(s) with the translations.
		 *
		 * Only uncomment the strings in the config array if you want to customize the strings.
		 */
		$config = array(
			'id'           => 'padma',                 // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',                      // Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'parent_slug'  => 'themes.php',            // Parent menu slug.
			'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,                   // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.
		);

		tgmpa( $plugins, $config );
	}


}




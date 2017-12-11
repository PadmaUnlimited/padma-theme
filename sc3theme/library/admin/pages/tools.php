<?php
global $wpdb;
?>
<h2 class="nav-tab-wrapper big-tabs-tabs">
	<a class="nav-tab" href="#tab-system-info">System Info</a>
	<a class="nav-tab" href="#tab-snapshots">Snapshots</a>
	<a class="nav-tab" href="#tab-reset">Reset</a>
</h2>

<?php do_action('blox_admin_save_message'); ?>


<div class="big-tabs-container">
			
	<div class="big-tab" id="tab-system-info-content">
						
		<div id="system-info">
				
			<h3 class="title" style="margin-bottom: 10px;"><strong>System Info</strong></h3>

			<p class="description">
				Copy and paste this information into support/forums if requested.
				<br /><br />
				<strong>Please copy all of the content in the text area below and paste it as-is in the requested forum discussion.</strong>
			</p>
			
			<?php
			$browser = blox_get_browser();

			$post_count = wp_count_posts('post');
			$page_count = wp_count_posts('page');

			$snapshots_info = BloxDataSnapshots::get_table_info();
			?>

<textarea readonly="readonly" id="system-info-textarea" title="To copy the system info, click below then press Ctrl + C (PC) or Cmd + C (Mac).">

    ### Begin System Info ###

	Child Theme:		<?php echo BLOX_CHILD_THEME_ACTIVE ? (function_exists('wp_get_theme') ? wp_get_theme() : get_current_theme()) . "\n" : "N/A\n" ?>

    Multi-site: 		<?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n" ?>
	
    SITE_URL:  			<?php echo site_url() . "\n"; ?>
    HOME_URL:			<?php echo home_url() . "\n"; ?>
    	
    Blox Version:  	<?php echo BLOX_VERSION . "\n"; ?>
    WordPress Version:	<?php echo get_bloginfo('version') . "\n"; ?>
    
    PHP Version:		<?php echo PHP_VERSION . "\n"; ?>
    MySQL Version:		<?php echo $wpdb->db_version() . "\n"; ?>
    Web Server Info:	<?php echo $_SERVER['SERVER_SOFTWARE'] . "\n"; ?>
    GD Support:			<?php echo function_exists('gd_info') ? "Yes\n" : "***WARNING*** No\n"; ?>
    
    PHP Memory Limit:	<?php echo ini_get('memory_limit') . "\n"; ?>
    PHP Post Max Size:	<?php echo ini_get('post_max_size') . "\n"; ?>
    
    WP_DEBUG: 			<?php echo defined('WP_DEBUG') ? WP_DEBUG ? 'Enabled' . "\n" : 'Disabled' . "\n" : 'Not set' . "\n" ?>
	SCRIPT_DEBUG: 		<?php echo defined('SCRIPT_DEBUG') ? SCRIPT_DEBUG ? 'Enabled' . "\n" : 'Disabled' . "\n" : 'Not set' . "\n" ?>
    Debug Mode: 		<?php echo BloxOption::get('debug-mode', false, false) ? 'Enabled' . "\n" : 'Disabled' . "\n" ?>
    
	Show On Front: 		<?php echo get_option('show_on_front') . "\n" ?>
	Page On Front: 		<?php echo get_option('page_on_front') . "\n" ?>
	Page For Posts: 	<?php echo get_option('page_for_posts') . "\n" ?>

	Number of Posts:	~<?php echo $post_count->publish . "\n" ?>
	Number of Pages:	~<?php echo $page_count->publish . "\n" ?>
	Number of Blocks: 	~<?php echo count(BloxBlocksData::get_all_blocks()) . "\n" ?>

	Snapshots:          <?php echo $snapshots_info['count']; ?> snapshots taking up <?php echo $snapshots_info['size']; ?> of disk space.

	Responsive Grid: 	<?php echo BloxResponsiveGrid::is_enabled() ? 'Enabled' . "\n" : 'Disabled' . "\n" ?>
    
    Caching Allowed: 	<?php echo BloxCompiler::can_cache() ? 'Yes' . "\n" : 'No' . "\n"; ?>
    Caching Enabled: 	<?php echo BloxCompiler::caching_enabled() ? 'Yes' . "\n" : 'No' . "\n"; ?>
    Caching Plugin: 	<?php echo BloxCompiler::is_plugin_caching() ? BloxCompiler::is_plugin_caching() . "\n" : 'No caching plugin active' . "\n" ?>
    
	SEO Plugin: 		<?php echo BloxSEO::plugin_active() ? BloxSEO::plugin_active() . "\n" : 'No SEO plugin active' . "\n" ?>

    Operating System:	<?php echo ucwords($browser['platform']) . "\n"; ?>
    Browser:			<?php echo $browser['name'] . "\n"; ?>
    Browser Version:	<?php echo $browser['version'] . "\n"; ?>
    
    Full User Agent:
    <?php echo $browser['userAgent'] . "\n"; ?>


    WEB FONTS IN USE:
<?php
$webfonts_in_use = BloxWebFontsLoader::get_fonts_in_use();

if ( is_array($webfonts_in_use) && count($webfonts_in_use) ) {

	foreach ( $webfonts_in_use as $provider => $fonts )
		foreach ( $fonts as $font )
			echo '    ' . $provider . ': ' . $font . "\n";

} else {

	echo '    None' . "\n";

}
?>
    
    
    ACTIVE PLUGINS:
<?php
$plugins = get_plugins();
$active_plugins = get_option('active_plugins', array());

if ( is_array($active_plugins) && count($active_plugins) ) {

	foreach ( $plugins as $plugin_path => $plugin ) {
		
		//If the plugin isn't active, don't show it.
		if ( !in_array($plugin_path, $active_plugins) )
			continue;
		
		echo '    ' . $plugin['Name'] . ' ' . $plugin['Version'] . "\n";
		
		if ( isset($plugin['PluginURI']) )
			echo '    ' . $plugin['PluginURI'] . "\n";
			
		echo "\n";
		
	}

} else {

	echo '    None' . "\n\n";

}
?>
    ### End System Info ###

</textarea>

		</div><!-- #system-info -->

	</div><!-- #tab-system-info-content -->

	<div class="big-tab" id="tab-snapshots-content">

		<h3 class="title" style="margin-bottom: 10px;"><strong>Snapshots</strong></h3>

		<p class="description">
			There are currently <?php echo $snapshots_info['count']; ?> snapshots taking up <?php echo $snapshots_info['size']; ?> of disk space.<br /><br />
			You can delete individual snapshots in the Visual Editor under Snapshots if you do not wish to delete all snapshots.
		</p>

		<form method="post" id="blox-delete-snapshots">
			<input type="hidden" value="<?php echo wp_create_nonce( 'blox-delete-snapshots-nonce' ); ?>" name="blox-delete-snapshots-nonce" id="blox-delete-snapshots-nonce" />

			<input type="submit" value="Delete All Snapshots" class="button button-primary blox-medium-button" name="blox-delete-snapshots" id="blox-delete-snapshots" onclick="return confirm('Caution! This will delete ALL snapshots. This means you will not be able to rollback your site until you create new snapshots.  \'OK\' to delete, \'Cancel\' to stop');" />
		</form>
		<!-- #reset -->

	</div>
	<!-- #tab-reset-content -->

	<div class="big-tab" id="tab-reset-content">
			
		<?php if ( defined('BLOX_ALLOW_RESET') && BLOX_ALLOW_RESET === true ): ?>
		<?php if ( !isset($GLOBALS['blox_reset_success']) || $GLOBALS['blox_reset_success'] == false ): ?>
		<div class="alert-red reset-alert alert">
			<h3>Warning</h3>
			
			<p>Clicking the <em>Reset</em> button below will delete <strong>ALL</strong> existing Blox data including, but not limited to: Blocks, Design Settings, and Blox Search Engine Optimization settings.</p>
			
			<form method="post" id="reset-blox">
				<input type="hidden" value="<?php echo wp_create_nonce('blox-reset-nonce'); ?>" name="blox-reset-nonce" id="blox-reset-nonce" />
				
				<input type="submit" value="Reset Blox" class="button alert-big-button" name="reset-blox" id="reset-blox-submit" onclick="return confirm('Warning! ALL existing Blox data, including, but not limited to: Blocks, Design Settings, and Blox Search Engine Optimization settings will be deleted. This cannot be undone. \'OK\' to delete, \'Cancel\' to stop');" />
			</form><!-- #reset -->
		</div>
		<?php endif; ?>
		<?php else: ?>
		<div class="alert-yellow reset-info alert">
			<h3>Blox Theme Reset Disabled</h3>

			<p>For your security, resetting Blox Theme is disabled.</p>

			<p>If you wish to reset your Blox installation, please <span style="font-weight: 600;color: #fff;background: #2f2f2f; padding: 2px 4px;">add the code below to your wp-config.php file</span>. <p>Please make sure to add the code above this line in your wp.config.php file:  <code> /* That's all, stop editing! Happy blogging. */</code><br />Not sure how to edit your wp-config.php file?  Please see <a href="http://codex.wordpress.org/Editing_wp-config.php" target="_blank">Editing wp-config.php</a> in the official WordPress documentation.</p>

			<textarea class="code" style="width: 400px;height:45px;resize:none;margin: 10px 0 10px;" readonly="readonly">define('BLOX_ALLOW_RESET', true);</textarea>
		</div>
		<?php endif; ?>
		
	</div><!-- #tab-reset-content -->
		
</div>
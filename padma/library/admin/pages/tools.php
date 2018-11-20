<?php
global $wpdb, $post;
?>
<h2 class="nav-tab-wrapper big-tabs-tabs">
	<a class="nav-tab" href="#tab-system-info">System Info</a>
	<a class="nav-tab" href="#tab-snapshots">Snapshots</a>
	<a class="nav-tab" href="#tab-reset">Reset</a>
</h2>

<?php do_action('padma_admin_save_message'); ?>


<div class="big-tabs-container">
			
	<div class="big-tab" id="tab-system-info-content">
						
		<div id="system-info">
				
			<h3 class="title" style="margin-bottom: 10px;"><strong>System Info</strong></h3>


<?php
if ( apply_filters( 'replace_editor', false, $post ) === true ) {

}?>

			<p class="description">
				Copy and paste this information into support/forums if requested.
				<br /><br />
				<strong>Please copy all of the content in the text area below and paste it as-is in the requested forum discussion.</strong>
			</p>
			
			<?php
			$browser = padma_get_browser();

			$post_count = wp_count_posts('post');
			$page_count = wp_count_posts('page');

			$snapshots_info = PadmaDataSnapshots::get_table_info();
			?>

<textarea readonly="readonly" id="system-info-textarea" title="To copy the system info, click below then press Ctrl + C (PC) or Cmd + C (Mac).">

    ### Begin System Info ###

	Child Theme:		<?php echo PADMA_CHILD_THEME_ACTIVE ? (function_exists('wp_get_theme') ? wp_get_theme() : get_current_theme()) . "\n" : "N/A\n" ?>

    Multi-site: 		<?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n" ?>
	
    SITE_URL:  			<?php echo site_url() . "\n"; ?>
    HOME_URL:			<?php echo home_url() . "\n"; ?>
    	
    Padma Version:  	<?php echo PADMA_VERSION . "\n"; ?>
    WordPress Version:	<?php echo get_bloginfo('version') . "\n"; ?>
    
    PHP Version:		<?php echo PHP_VERSION . "\n"; ?>
    MySQL Version:		<?php echo $wpdb->db_version() . "\n"; ?>
    Web Server Info:	<?php echo $_SERVER['SERVER_SOFTWARE'] . "\n"; ?>
    GD Support:			<?php echo function_exists('gd_info') ? "Yes\n" : "***WARNING*** No\n"; ?>
    
    PHP Memory Limit:	<?php echo ini_get('memory_limit') . "\n"; ?>
    PHP Post Max Size:	<?php echo ini_get('post_max_size') . "\n"; ?>
    
    WP_DEBUG: 			<?php echo defined('WP_DEBUG') ? WP_DEBUG ? 'Enabled' . "\n" : 'Disabled' . "\n" : 'Not set' . "\n" ?>
	SCRIPT_DEBUG: 		<?php echo defined('SCRIPT_DEBUG') ? SCRIPT_DEBUG ? 'Enabled' . "\n" : 'Disabled' . "\n" : 'Not set' . "\n" ?>
    Debug Mode: 		<?php echo PadmaOption::get('debug-mode', false, false) ? 'Enabled' . "\n" : 'Disabled' . "\n" ?>
    
	Show On Front: 		<?php echo get_option('show_on_front') . "\n" ?>
	Page On Front: 		<?php echo get_option('page_on_front') . "\n" ?>
	Page For Posts: 	<?php echo get_option('page_for_posts') . "\n" ?>

	Number of Posts:	~<?php echo $post_count->publish . "\n" ?>
	Number of Pages:	~<?php echo $page_count->publish . "\n" ?>
	Number of Blocks: 	~<?php echo count(PadmaBlocksData::get_all_blocks()) . "\n" ?>

	Snapshots:          <?php echo $snapshots_info['count']; ?> snapshots taking up <?php echo $snapshots_info['size']; ?> of disk space.

	Responsive Grid: 	<?php echo PadmaResponsiveGrid::is_enabled() ? 'Enabled' . "\n" : 'Disabled' . "\n" ?>
    
    Caching Allowed: 	<?php echo PadmaCompiler::can_cache() ? 'Yes' . "\n" : 'No' . "\n"; ?>
    Caching Enabled: 	<?php echo PadmaCompiler::caching_enabled() ? 'Yes' . "\n" : 'No' . "\n"; ?>
    Caching Plugin: 	<?php echo PadmaCompiler::is_plugin_caching() ? PadmaCompiler::is_plugin_caching() . "\n" : 'No caching plugin active' . "\n" ?>
    
	SEO Plugin: 		<?php echo PadmaSEO::plugin_active() ? PadmaSEO::plugin_active() . "\n" : 'No SEO plugin active' . "\n" ?>

    Operating System:	<?php echo ucwords($browser['platform']) . "\n"; ?>
    Browser:			<?php echo $browser['name'] . "\n"; ?>
    Browser Version:	<?php echo $browser['version'] . "\n"; ?>
    
    Full User Agent:
    <?php echo $browser['userAgent'] . "\n"; ?>


    WEB FONTS IN USE:
<?php
$webfonts_in_use = PadmaWebFontsLoader::get_fonts_in_use();

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

		<form method="post" id="padma-delete-snapshots">
			<input type="hidden" value="<?php echo wp_create_nonce( 'padma-delete-snapshots-nonce' ); ?>" name="padma-delete-snapshots-nonce" id="padma-delete-snapshots-nonce" />

			<input type="submit" value="Delete All Snapshots" class="button button-primary padma-medium-button" name="padma-delete-snapshots" id="padma-delete-snapshots" onclick="return confirm('Caution! This will delete ALL snapshots. This means you will not be able to rollback your site until you create new snapshots.  \'OK\' to delete, \'Cancel\' to stop');" />
		</form>
		<!-- #reset -->

	</div>
	<!-- #tab-reset-content -->

	<div class="big-tab" id="tab-reset-content">
			
		<?php if ( defined('PADMA_ALLOW_RESET') && PADMA_ALLOW_RESET === true ): ?>
		<?php if ( !isset($GLOBALS['padma_reset_success']) || $GLOBALS['padma_reset_success'] == false ): ?>
		<div class="alert-red reset-alert alert">
			<h3>Warning</h3>
			
			<p>Clicking the <em>Reset</em> button below will delete <strong>ALL</strong> existing Padma data including, but not limited to: Blocks, Design Settings, and Padma Search Engine Optimization settings.</p>
			
			<form method="post" id="reset-padma">
				<input type="hidden" value="<?php echo wp_create_nonce('padma-reset-nonce'); ?>" name="padma-reset-nonce" id="padma-reset-nonce" />
				
				<input type="submit" value="Reset Padma" class="button alert-big-button" name="reset-padma" id="reset-padma-submit" onclick="return confirm('Warning! ALL existing Padma data, including, but not limited to: Blocks, Design Settings, and Padma Search Engine Optimization settings will be deleted. This cannot be undone. \'OK\' to delete, \'Cancel\' to stop');" />
			</form><!-- #reset -->
		</div>
		<?php endif; ?>
		<?php else: ?>
		<div class="alert-yellow reset-info alert">
			<h3>Padma Theme Reset Disabled</h3>

			<p>For your security, resetting Padma Theme is disabled.</p>

			<p>If you wish to reset your Padma installation, please <span style="font-weight: 600;color: #fff;background: #2f2f2f; padding: 2px 4px;">add the code below to your wp-config.php file</span>. <p>Please make sure to add the code above this line in your wp.config.php file:  <code> /* That's all, stop editing! Happy blogging. */</code><br />Not sure how to edit your wp-config.php file?  Please see <a href="http://codex.wordpress.org/Editing_wp-config.php" target="_blank">Editing wp-config.php</a> in the official WordPress documentation.</p>

			<textarea class="code" style="width: 400px;height:45px;resize:none;margin: 10px 0 10px;" readonly="readonly">define('PADMA_ALLOW_RESET', true);</textarea>
		</div>
		<?php endif; ?>
		
	</div><!-- #tab-reset-content -->
		
</div>
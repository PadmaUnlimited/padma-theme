<!DOCTYPE HTML>
<html lang="en" style="background: #eee;">

<head>
	
	<meta charset="<?php bloginfo('charset'); ?>" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta http-equiv="cache-control" content="no-cache" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	
	<title>Visual Editor: Loading</title>
	
	<?php do_action('blox_visual_editor_head'); ?>

</head><!-- /head -->

<!-- This background color has been inlined to reduce the white flicker during loading. -->
<body class="wp-core-ui visual-editor-open visual-editor-mode-<?php echo BloxVisualEditor::get_current_mode() . ' ' . join(' ', get_body_class()); ?>" style="background: #1c1c1c;">
	
	<?php do_action('blox_visual_editor_body_open'); ?>
	
	<div id="ve-loading-overlay">
		<div class="cog-container"><div class="cog-bottom-left"></div><div class="cog-top-right"></div></div>
	</div><!-- #ve-loading-overlay -->
	
	<div id="menu">
		<span id="logo"></span>
	
		<ul id="modes" class="top-menu-nav">
			<?php do_action('blox_visual_editor_modes'); ?>
		</ul>
	
		<?php do_action('blox_visual_editor_menu'); ?>
	
		<div id="menu-right">
	
			<?php do_action('blox_visual_editor_menu_mode_buttons'); ?>

			<ul class="top-menu-nav">
				<li id="snapshots-button">
					<span>Snapshots</span>
				</li>

				<?php do_action('blox_visual_editor_menu_links'); ?>
			</ul>
	
			<div id="save-button-container" class="save-button-container" style="margin-right:-76px;">
				<span id="save-button" class="save-button">Save</span>
			</div>
	
		</div><!-- #menu-right -->
	</div><!-- #menu -->

	
	<!-- Big Boy iframe -->
	<div id="iframe-container">
		<?php
		$layout_url = BloxVisualEditor::get_current_mode() == 'grid' ? home_url() : BloxLayout::get_url(BloxLayout::get_current());

        $current_layout_status = BloxLayout::get_status(BloxLayout::get_current());
	
		$iframe_url = add_query_arg(array(
			've-iframe' => 'true',
			've-layout' => urlencode(BloxLayout::get_current()),
            've-layout-customized' => blox_get('customized', $current_layout_status, false) ? 'true' : 'false',
            've-iframe-mode' => BloxVisualEditor::get_current_mode(),
			'rand' => rand(1, 999999)
		), $layout_url);
	
		echo '<iframe id="content" class="content" src="' . $iframe_url . '" scrolling="yes" sandbox="allow-same-origin allow-scripts"></iframe>';
	
		?>
		
		<div id="iframe-overlay"></div>
		<div id="iframe-loading-overlay"><div class="cog-container"><div class="cog-bottom-left"></div><div class="cog-top-right"></div></div></div>
	</div>
	<!-- #iframe#content -->
	
	<div id="panel">
	
		<div id="panel-top-container">
	
			<ul id="panel-top">
	
				<?php do_action('blox_visual_editor_panel_top_tabs'); ?>
	
			</ul><!-- #ul#panel-top -->
	
			<ul id="panel-top-right">
	
				<?php do_action('blox_visual_editor_panel_top_right'); ?>
	
			</ul><!-- #ul#panel-top -->
	
		</div><!-- #div#panel-top-container -->
	
		<?php do_action('blox_visual_editor_content'); ?>
	
	</div><!-- div#panel -->
	
	
	<?php
	if ( has_action('blox_visual_editor_side_panel') ) {
	
		echo '<div id="side-panel-container">
	
			<div id="side-panel">';
	
				do_action('blox_visual_editor_side_panel');
	
		echo '</div><!-- #side-panel -->
	
		</div><!-- #side-panel-container -->';
	
	}
	?>
	
	
	<div id="boxes">
		<?php do_action('blox_visual_editor_boxes'); ?>
	</div><!-- div#boxes -->
	
	<?php do_action('blox_visual_editor_footer'); ?>
	
	<div id="notification-center"></div>
	
</body>
</html>
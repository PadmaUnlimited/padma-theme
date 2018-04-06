<!DOCTYPE HTML>
<html lang="en" style="background: #eee;">
<head>
	
	<meta charset="<?php bloginfo('charset'); ?>" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta http-equiv="cache-control" content="no-cache" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	
	<title>Visual Editor: Loading</title>
	<link rel="shortcut icon" type="image/png" href="<?php echo padma_url() . "/library/visual-editor/images/logo.png"; ?>"/>
	
	<?php do_action('padma_visual_editor_head'); ?>

</head><!-- /head -->

<!-- This background color has been inlined to reduce the white flicker during loading. -->
<body class="wp-core-ui visual-editor-open visual-editor-mode-<?php echo PadmaVisualEditor::get_current_mode() . ' ' . join(' ', get_body_class()); ?>" style="background: #1c1c1c;">
	
	<?php do_action('padma_visual_editor_body_open'); ?>
	
	<div id="ve-loading-overlay">
		<div class="cog-container"><div class="cog-bottom-left"></div><div class="cog-top-right"></div></div>
	</div><!-- #ve-loading-overlay -->
	
	<div id="menu">
		<span id="logo"></span>
	
		<ul id="modes" class="top-menu-nav">
			<?php do_action('padma_visual_editor_modes'); ?>
		</ul>
		
		<?php do_action('padma_visual_editor_menu'); ?>


		<!--	Device Preview options	 -->
		<div class="devices-wrapper">
			<div class="devices">
				<button type="button" class="preview-desktop" aria-pressed="false" data-device="desktop">
				</button>
				<button type="button" class="preview-tablet" aria-pressed="false" data-device="tablet">
				</button>
				<button type="button" class="preview-mobile" aria-pressed="true" data-device="mobile">
				</button>
			</div>
		</div>
	
		<div id="menu-right">
	
			<?php do_action('padma_visual_editor_menu_mode_buttons'); ?>

			<ul class="top-menu-nav">
				<li id="switch-mode">
					<div class="toggle-mode">
						<div class="icon light"></div>
						<div class="toggle-switch">
							<label class="switch">
								<input type="checkbox" id="switch-style">
								<div class="slider round"></div>
							</label>
						</div>
						<div class="icon night"></div>
					</div>
				</li>
				<li id="snapshots-button">
					<span>Snapshots</span>
				</li>

				<?php do_action('padma_visual_editor_menu_links'); ?>
			</ul>
	
			<div id="save-button-container" class="save-button-container" style="margin-right:-76px;">
				<span id="save-button" class="save-button">Save</span>
			</div>
	
		</div><!-- #menu-right -->
	</div><!-- #menu -->

	
	<!-- Big Boy iframe -->
	<div id="customize-preview" class="wp-full-overlay-main">
		<div id="iframe-container">
			<?php

			$layout_url = PadmaVisualEditor::get_current_mode() == 'grid' ? home_url() : PadmaLayout::get_url(PadmaLayout::get_current());

	        $current_layout_status = PadmaLayout::get_status(PadmaLayout::get_current());
		
			$iframe_url = add_query_arg(array(
				've-iframe' 				=> 'true',
				've-layout' 				=> urlencode(PadmaLayout::get_current()),
	            've-layout-customized' 		=> padma_get('customized', $current_layout_status, false) ? 'true' : 'false',
	            've-iframe-mode' 			=> PadmaVisualEditor::get_current_mode(),
				'rand' 						=> rand(1, 999999)
			), $layout_url);
		
			echo '<iframe id="content" class="content" src="' . $iframe_url . '" scrolling="yes" sandbox="allow-same-origin allow-scripts"></iframe>';
		
			?>
			
			<div id="iframe-overlay"></div>
			<div id="iframe-loading-overlay"><div class="cog-container"><div class="cog-bottom-left"></div><div class="cog-top-right"></div></div></div>
		</div>
	</div>	
	<!-- #iframe#content -->
	
	<div id="panel">
	
		<div id="panel-top-container">
	
			<ul id="panel-top">
	
				<?php do_action('padma_visual_editor_panel_top_tabs'); ?>
	
			</ul><!-- #ul#panel-top -->
	
			<ul id="panel-top-right">
	
				<?php do_action('padma_visual_editor_panel_top_right'); ?>
	
			</ul><!-- #ul#panel-top -->
	
		</div><!-- #div#panel-top-container -->
	
		<?php do_action('padma_visual_editor_content'); ?>
	
	</div><!-- div#panel -->
	
	
	<?php
	if ( has_action('padma_visual_editor_side_panel') ) {
	
		echo '<div id="side-panel-container">
	
			<div id="side-panel">';
	
				do_action('padma_visual_editor_side_panel');
	
		echo '</div><!-- #side-panel -->
	
		</div><!-- #side-panel-container -->';
	
	}
	?>
	
	
	<div id="boxes">
		<?php do_action('padma_visual_editor_boxes'); ?>
	</div><!-- div#boxes -->
	
	<?php do_action('padma_visual_editor_footer'); ?>
	
	<div id="notification-center"></div>
	
</body>
</html>
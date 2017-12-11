<div class="blox-small-wrap blox-getting-started-wrap">


	<div class="clearfix"></div>
	<?php if ( is_main_site() ): 	?>
		<div class="blox-sub-title" style="text-align:center"><img src="<?php echo blox_url() . '/library/admin/images/blox-theme-logo-square-250.png'; ?>" alt=""><h4 align=center>Congratulations! You installed Blox Theme Builder. Now you're ready to start making Blox!</h4></div>

	<?php endif; ?>

	<div id="blox-getting-started-ve-link-container">
		<input type="submit" value="Enter the Visual Editor"
		       class="blox-big-button button-primary action" id="blox-getting-started-ve-link" name=""
		       onclick="window.location.href = '<?php echo home_url() . '/?visual-editor=true'; ?>'"/>

		<p>
			You can hide this page by changing the <em>Default Admin Page</em> in <a href="<?php echo admin_url( 'admin.php?page=blox-options' ); ?>" target="_blank">Blox » Options</a>.
		</p>
	</div>

	<h2>New to Blox Theme?  Keep reading!</h2>

	<p>You navigate to all of Blox's core features in the WordPress admin menu. Here's a brief overview of each.</p>

	<div class="blox-infobox-row">
		<div class="blox-infobox big inrow">
			<h3>Getting Started</h3>

			<p>You are here! If you ever get stuck or need to extend your Blox theme installation, this is the place to
				start.</p>

			<p>If you're totally new to Blox, it's highly recommended you read our <a
					href="http://bloxtheme.com/dashboard/docs/"
					target=_"blank">Blox Beginner's Guide</a>.</p>
		</div>
		<div class="blox-infobox big">
			<h3><a href="<?php echo admin_url( 'admin.php?page=blox-visual-editor' ); ?>">Visual Editor</a></h3>

			<p>The Visual Editor is the magic. It is where you design and style your amazing website.</p>

			<p>Check out this document for a quick overview of the <a
					href="http://docs.bloxtheme.com/article/26-the-basics-of-customizing-a-layout" target="_blank">basics
					of the Visual Editor</a></p>
		</div>
	</div>

	<div class="blox-infobox-row">
		<div class="blox-infobox big inrow">
			<h3><a href="<?php echo admin_url( 'admin.php?page=blox-options' ); ?>">Options</a></h3>
			<p>This is the place to go to tweak your Blox theme site, with things like Google Analytics, SEO, favicons and other more advanced settings.</p>
		</div>
		<div class="blox-infobox big">
			<h3><a href="<?php echo admin_url( 'admin.php?page=blox-tools' ); ?>">Tools</a></h3>
			<p>If you log a request on the forums, it is very useful to provide system info and Tools is the place to find it.</p>
			<p>And if you really just want to wipe the slate, and start afresh, there's a big red button for that.</p>
		</div>
	</div>

	<h2>Need help?</h2>
	<p>If you ever run into any problems, you can visit our forums or check out the documentation</p>

	<div class="blox-infobox-row blox-infobox-icon-row">
		<div class="blox-infobox blox-infobox-icon inrow">
			<span class="dashicons dashicons-groups"></span>
			<a class="big" href="http://bloxtheme.com/ticket-form/" target="_blank">Support</a>
		</div>

		<div class="blox-infobox blox-infobox-icon">
			<span class="dashicons dashicons-book-alt bigfix"></span>
			<a class="big" href="http://bloxtheme.com/dashboard/docs/" target="_blank">Documentation</a>
		</div>
	</div>

	<h2>Extending Blox Theme</h2>
	<p>We have a wonderful community of third party developers who are creating beautiful Templates that contain all the design and styling already done for you, and fantastically useful Blocks, which provide even more layout possibilites, saving you heaps of time in setting up your designs, such as galleries, sliders, advanced content display and utility blocks.</p>


	<div class="blox-infobox-row blox-infobox-icon-row">
		<div class="blox-infobox inrow blox-infobox-icon">
			<span class="dashicons dashicons-welcome-widgets-menus"></span>
			<a class="big" href="http://bloxtheme.com/extend/" target="_blank">Templates</a>
		</div>

		<div class="blox-infobox blox-infobox-icon">
			<span class="dashicons dashicons-screenoptions"></span>
			<a class="big" href="http://bloxtheme.com/extend/" target="_blank">Blocks</a>
		</div>
	</div>

</div>

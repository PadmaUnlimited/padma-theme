<div class="padma-small-wrap padma-getting-started-wrap">


	<div class="clearfix"></div>
	<?php if ( is_main_site() ): 	?>
		<div class="padma-sub-title" style="text-align:center"><img src="<?php echo padma_url() . '/library/admin/images/padma-theme-logo-square-250.png'; ?>" alt=""><h4 align=center>Congratulations! You installed Padma Theme Builder. Now you're ready to start making Padma!</h4></div>

	<?php endif; ?>

	<div id="padma-getting-started-ve-link-container">
		<input type="submit" value="Enter the Visual Editor"
		       class="padma-big-button button-primary action" id="padma-getting-started-ve-link" name=""
		       onclick="window.location.href = '<?php echo home_url() . '/?visual-editor=true'; ?>'"/>

		<p>
			You can hide this page by changing the <em>Default Admin Page</em> in <a href="<?php echo admin_url( 'admin.php?page=padma-options' ); ?>" target="_blank">Padma » Options</a>.
		</p>
	</div>

	<h2>New to Padma Theme?  Keep reading!</h2>

	<p>You navigate to all of Padma's core features in the WordPress admin menu. Here's a brief overview of each.</p>

	<div class="padma-infobox-row">
		<div class="padma-infobox big inrow">
			<h3>Getting Started</h3>

			<p>You are here! If you ever get stuck or need to extend your Padma theme installation, this is the place to
				start.</p>

			<p>If you're totally new to Padma, it's highly recommended you read our <a
					href="http://padmatheme.com/dashboard/docs/"
					target=_"blank">Padma Beginner's Guide</a>.</p>
		</div>
		<div class="padma-infobox big">
			<h3><a href="<?php echo admin_url( 'admin.php?page=padma-visual-editor' ); ?>">Visual Editor</a></h3>

			<p>The Visual Editor is the magic. It is where you design and style your amazing website.</p>

			<p>Check out this document for a quick overview of the <a
					href="http://docs.padmatheme.com/article/26-the-basics-of-customizing-a-layout" target="_blank">basics
					of the Visual Editor</a></p>
		</div>
	</div>

	<div class="padma-infobox-row">
		<div class="padma-infobox big inrow">
			<h3><a href="<?php echo admin_url( 'admin.php?page=padma-options' ); ?>">Options</a></h3>
			<p>This is the place to go to tweak your Padma theme site, with things like Google Analytics, SEO, favicons and other more advanced settings.</p>
		</div>
		<div class="padma-infobox big">
			<h3><a href="<?php echo admin_url( 'admin.php?page=padma-tools' ); ?>">Tools</a></h3>
			<p>If you log a request on the forums, it is very useful to provide system info and Tools is the place to find it.</p>
			<p>And if you really just want to wipe the slate, and start afresh, there's a big red button for that.</p>
		</div>
	</div>

	<h2>Need help?</h2>
	<p>If you ever run into any problems, you can visit our forums or check out the documentation</p>

	<div class="padma-infobox-row padma-infobox-icon-row">
		<div class="padma-infobox padma-infobox-icon inrow">
			<span class="dashicons dashicons-groups"></span>
			<a class="big" href="http://padmatheme.com/ticket-form/" target="_blank">Support</a>
		</div>

		<div class="padma-infobox padma-infobox-icon">
			<span class="dashicons dashicons-book-alt bigfix"></span>
			<a class="big" href="http://padmatheme.com/dashboard/docs/" target="_blank">Documentation</a>
		</div>
	</div>

	<h2>Extending Padma Theme</h2>
	<p>We have a wonderful community of third party developers who are creating beautiful Templates that contain all the design and styling already done for you, and fantastically useful Blocks, which provide even more layout possibilites, saving you heaps of time in setting up your designs, such as galleries, sliders, advanced content display and utility blocks.</p>


	<div class="padma-infobox-row padma-infobox-icon-row">
		<div class="padma-infobox inrow padma-infobox-icon">
			<span class="dashicons dashicons-welcome-widgets-menus"></span>
			<a class="big" href="http://padmatheme.com/extend/" target="_blank">Templates</a>
		</div>

		<div class="padma-infobox padma-infobox-icon">
			<span class="dashicons dashicons-screenoptions"></span>
			<a class="big" href="http://padmatheme.com/extend/" target="_blank">Blocks</a>
		</div>
	</div>

</div>

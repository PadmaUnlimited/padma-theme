<?php

defined('ABSPATH') or die("No script kiddies please!");

?>
<div class="padma-admin-container">

	<img class="padma-logo" src="<?php echo padma_url() . '/library/admin/images/padma-theme-logo-square-250.png'; ?>">

	<div class="padma-admin-row menu">
		<a href="javascript:void(0)" onclick="openTabAdmin(event, 'welcome');">
			<div class="padma-admin-title tablink padma-admin-border-red"><?php _e('Welcome','padma'); ?></div>
		</a><a href="javascript:void(0)" onclick="openTabAdmin(event, 'options');">
			<div class="padma-admin-title tablink"><?php _e('Options','padma'); ?></div>
		</a><a href="javascript:void(0)" onclick="openTabAdmin(event, 'need-help');">
			<div class="padma-admin-title tablink"><?php _e('Need help?','padma'); ?></div>
		</a><a href="javascript:void(0)" onclick="openTabAdmin(event, 'unlimited-growth');">
			<div class="padma-admin-title tablink"><?php _e('Unlimited growth','padma'); ?></div>
		</a>
	</div>

	<div id="welcome" class="padma-admin-tab" style="">	
		<div class="content">

			<h1><?php _e('Welcome!','padma'); ?></h1>

			<p><?php _e('Your <strong>Padma Theme Builder</strong> installation is ready.','padma'); ?></p>
			
			<p><strong><?php _e('Start creating now!','padma'); ?></strong></p>

			<br>

			<p><?php _e('To hide this page just change the Default Admin Page in <a href="?page=padma-options">Padma » Options</a>.','padma'); ?></p>

			<div class="separator"></div>

			<h2><?php _e('Padma | Unlimited - Core features in the WordPress dashboard / admin menu.','padma'); ?></h2>
			<div class="box">
				<h3><?php _e('Padma | Unlimited - Welcome!','padma'); ?></h3>
				<p><?php _e('(This page)','padma'); ?></p>
				<p><?php _e('Get access to ►','padma'); ?></p>
				<p><?php _e('General Information, Documentation and Support for <b>Padma</b> | Unlimited Theme Builder.','padma'); ?></p>
				<p><?php _e('Blocks and templates to expand <b>Padma</b> | Unlimited Theme Builder possibilities.','padma'); ?></p>
			</div>
			

			<h2><?php _e('Padma starter users','padma'); ?></h2>
			<div class="box">
				<p><?php _e('Please read Padma | Unlimited Theme Builder <a href="https://docs.padmaunlimited.com/">Documentation</a>.','padma'); ?></p>
			</div>


			<div class="box">
				<h3><?php _e('Padma | Unlimited Visual Editor','padma'); ?></h3>
				<p><?php _e('Padma | Unlimited Visual Editor is a powerful tool to design WordPress website layouts and templates.  Easily customize almost every visual element of your websites through a graphical interface (Code can be added easily using the integrated code editor if required).','padma'); ?></p>
				<p><?php _e('Learn more about the platform in the document <a rel="noopener" href="https://docs.padmaunlimited.com/blog/basics/before-using-the-visual-editor/">"Introduction to Padma | Unlimited Visual Editor". </a>','padma'); ?></p>
				<a href="<?php echo home_url() . '/?visual-editor=true'; ?>" class="access-to-unlimited-editor"><span class="text"><?php _e('Access <b>Padma</b> | Unlimited Editor','padma'); ?></span></a>
			</div>
			<div class="box">
				<h3><?php _e('Additional Blocks available!','padma'); ?></h3>
				<p><?php _e('Check out Padma Advanced plugin.','padma'); ?></p>				
			</div>
		</div>
	</div>

	<div id="options" class="padma-admin-tab" style="display:none">
		<div class="content">
			<div class="box">
				<h3><?php _e('Padma | Options','padma'); ?></h3>
				<p><?php _e('Setup your Google Analytics, SEO, favicons and other more advanced settings.','padma'); ?></p>
			</div>
			<h2 class="center"><?php _e('Padma | Tools','padma'); ?></h2>
			<div class="box">
				<h3><?php _e('System info.','padma'); ?></h3>
				<p><?php _e('To open a ticket or if you log a help request on the forums, please provide this system information.','padma'); ?></p>
			</div>
			<div class="box">
				<h3><?php _e('Snapshots','padma'); ?></h3>
				<p><?php _e('To free up some disk space, please delete Padma | Theme Builder snapshots.','padma'); ?></p>
			</div>
			<div class="box">
				<h3><?php _e('Reset','padma'); ?></h3>
				<p><?php _e('Instructions to reset your Padma | Unlimited WordPress Theme Builder installation.','padma'); ?></p>
			</div>
		</div>
	</div>

	<div id="need-help" class="padma-admin-tab" style="display:none">
		<div class="content">
			<h2 class="center"><?php _e('Help','padma'); ?></h2>
			<p><?php _e('Padma | Unlimited Theme Builder provides professional support and comprehensive documentation to help you bring your projects alive.','padma'); ?></p>			
			<div class="separator"></div>
			<div class="box">
				<h3><?php _e('Padma | Community','padma'); ?></h3>
				<p><?php _e('Join our community, get involved and help each other across multiple channels.','padma'); ?></p>
				<p><a href="https://www.facebook.com/padmaunlimited/" target="_blank"><?php _e('Facebook Page','padma'); ?></a></p>
				<p><a href="https://www.facebook.com/groups/367999217036886/" target="_blank"><?php _e('Facebook Users Group (English)','padma'); ?></a></p>
				<p><a href="https://www.facebook.com/groups/291445981578459/" target="_blank"><?php _e('Facebook Users Group (Español)','padma'); ?></a></p>
				<p><a href="https://twitter.com/PadmaUnlimited" target="_blank">Twitter</a></p>
				<p><a href="https://join.slack.com/t/padma-unlimited/shared_invite/enQtNTAxMzM1NjcwNTc5LWVmZjliNDRhZTQ1Y2FhZDY3ZjdkNzMzYzRmMzEwMDEyMWY0MjllYzJhYTk4ZTMxODEzNjk5NzE1YzMyMjFjNmY" target="_blank">Slack</a></p>
			</div>
			<div class="box">
				<h3><?php _e('Padma | Documentation','padma'); ?></h3>
				<p><?php _e('Register with us and get free access to our in- depth documentation. <a target="_blank" href="https://docs.padmaunlimited.com/" rel="noopener">docs.padmaunlimited.com</a>','padma'); ?></p>
			</div>
		</div>
	</div>

	<div id="unlimited-growth" class="padma-admin-tab" style="display:none">
		<div class="content">
			<h2 class="center"><?php _e('Padma | Unlimited Growth','padma'); ?></h2>			
			<p><?php _e('Develop, share and put on the market Templates and Custom Blocks.','padma'); ?></p>
			<p><?php _e('Working together will increase the community growth so, get involved!','padma'); ?></p>
			<p><?php _e('Design faster and Customize your Workflow with useful tools.','padma'); ?></p>
			<div class="separator"></div>
			<div class="box">
				<h3><?php _e('Padma | Unlimited Templates','padma'); ?></h3>
				<p><?php _e('<strong>Padma | Unlimited Templates</strong> minimize your project development process, streamlining the design stage leading a faster content creation and upload.','padma'); ?></p>
			</div>
			<div class="box">
				<h3><?php _e('Padma | Unlimited Blocks','padma'); ?></h3>
				<p><?php _e('Extend the functionality of your Padma | Unlimited WordPress Theme Builder installation by adding useful custom blocks to your Projects.','padma'); ?></p>
			</div>
		</div>
	</div>

</div>
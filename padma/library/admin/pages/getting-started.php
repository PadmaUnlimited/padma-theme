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
			<p><strong><?php _e('You can start creating now!','padma'); ?></strong></p>
			<br>
			<p><?php _e('If you want to hide this page just change the Default Admin Page in <a href="?page=padma-options">Padma » Options</a>.','padma'); ?></p>
			<div class="separator"></div>
			<h2><?php _e('Padma | unlimited - Core features in the WordPress dashboard / admin menu.','padma'); ?></h2>
			<div class="box">
				<h3><?php _e('Padma | unlimited - Welcome!','padma'); ?></h3>
				<p><?php _e('(This page)','padma'); ?></p>
				<p><?php _e('Get access to General Information, Documentation & Support for Padma | unlimited - WordPress Theme Builder.','padma'); ?></p>
				<p><?php _e('Get access to blocks and templates to improve your Padma | unlimited Theme Builder.','padma'); ?></p>
				<br>
				<p><?php _e('If you are new to Padma | unlimited, please read our <a href="https://docs.padmaunlimited.com/">Free WordPress Theme Builder Documentation</a>','padma'); ?></p>
			</div>
			<div class="box">
				<h3><?php _e('Padma | unlimited Editor','padma'); ?></h3>
				<p><?php _e('Padma | unlimited Editor is a powerful tool to design & customize your layouts and edit the visual style of your WordPress website through a graphical interface.','padma'); ?></p>
				<p><?php _e('Learn more abou it in the document <a rel="noopener" href="https://docs.padmaunlimited.com/blog/basics/before-using-the-visual-editor/">Introduction to the Padma | unlimited Editor.</a>','padma'); ?></p>
				<a href="<?php echo home_url() . '/?visual-editor=true'; ?>" class="access-to-unlimited-editor"><span class="text"><?php _e('Access Padma | Unlimited Editor','padma'); ?></span><span class="line -right"></span><span class="line -top"></span><span class="line -left"></span><span class="line -bottom"></span></a>
			</div>
			<div class="box">
				<h3><?php _e('Additional Blocks available!','padma'); ?></h3>
				<a href="https://dashboard.padmaunlimited.com/login"><?php _e('Register to Padma Services to get access to additional Plugins and Blocks','padma'); ?></a>				
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
				<p><?php _e('You should provide this system information If you want to open a ticket or if you log a help request on the forums','padma'); ?></p>
			</div>
			<div class="box">
				<h3><?php _e('Snapshots','padma'); ?></h3>
				<p><?php _e('Here you can delete the snapshots of Padma Theme Builder to free up some disk space.','padma'); ?></p>
			</div>
			<div class="box">
				<h3><?php _e('Reset','padma'); ?></h3>
				<p><?php _e('Instructions to reset your <strong>Padma | Unlimited</strong> WordPress Theme Builder installation.','padma'); ?></p>
			</div>
		</div>
	</div>

	<div id="need-help" class="padma-admin-tab" style="display:none">
		<div class="content">
			<h2 class="center"><?php _e('Need help?','padma'); ?></h2>
			<p><strong><?php _e('Padma | unlimited WordPress Theme Builder</strong> provides professional support and comprehensive documentation to help you bring your projects alive.','padma'); ?></p>			
			<div class="separator"></div>
			<div class="box">
				<h3><?php _e('Padma | Community','padma'); ?></h3>
				<p><?php _e('Join to our great community, we help us each other across different channels.','padma'); ?></p>
				<p><a href="https://www.facebook.com/padmaunlimited/" target="_blank"><?php _e('Facebook Page','padma'); ?></a></p>
				<p><a href="https://www.facebook.com/groups/367999217036886/" target="_blank"><?php _e('Facebook Users Group (English)','padma'); ?></a></p>
				<p><a href="https://www.facebook.com/groups/291445981578459/" target="_blank"><?php _e('Facebook Users Group (Español)','padma'); ?></a></p>
				<p><a href="https://twitter.com/PadmaUnlimited" target="_blank">Twitter</a></p>
				<p><a href="https://join.slack.com/t/padma-unlimited/shared_invite/enQtNTAxMzM1NjcwNTc5LTM2YzQ0ODRhYzBmZDc4N2UwOWE0MjBlMmQyZmQ2MTdjZTgyNjg1Mzk4ZjVlNGIxYjZkMjlmMTNhNmE3OWQ1YjY" target="_blank">Slack</a></p>
			</div>
			<div class="box">
				<h3><?php _e('Padma | Documentation','padma'); ?></h3>
				<p><?php _e('Register with us and get <strong>free access</strong> to our complete and comprehensive documentation. <a target="_blank" href="https://docs.padmaunlimited.com/" rel="noopener">docs.padmaunlimited.com</a>','padma'); ?></p>
			</div>
		</div>
	</div>

	<div id="unlimited-growth" class="padma-admin-tab" style="display:none">
		<div class="content">
			<h2 class="center"><?php _e('Padma | Unlimited Growth','padma'); ?></h2>			
			<p><?php _e('Develop, share and sale Templates & Custom Blocks. Working together will increase community growth possibilities. Design faster & Customize your Workflow with useful tools. Don´t hesitate to invest in other strategic areas to your business.','padma'); ?></p>
			<div class="separator"></div>
			<div class="box">
				<h3><?php _e('Padma | Unlimited Templates','padma'); ?></h3>
				<p><?php _e('<strong>Padma | unlimited Templates</strong> minimize your project development process, streamlining the design stage leading a faster content creation and upload.','padma'); ?></p>
			</div>
			<div class="box">
				<h3><?php _e('Padma | Unlimited Blocks','padma'); ?></h3>
				<p><?php _e('Extend the functionality of your Padma | unlimited WordPress Theme Builder installation by adding useful custom blocks to your Projects.','padma'); ?></p>
				<p><?php _e('Services: Documentation, Lifesaver (Migrate from HTW/Blox), Child Theme, Templates on cloud, site monitor.','padma'); ?></p>				
				<p><?php _e('Shortcode block: integration & management of Woocommerce content. Contact form 7 & Gravity forms.','padma'); ?></p>
				<p><?php _e('Get into Padma Services to unlock the Padma Unlimited potential.','padma'); ?></p>
			</div>
		</div>
	</div>

</div>
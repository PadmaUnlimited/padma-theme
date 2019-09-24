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
				<h3><?php _e('If you like Padma | Unlimited','padma'); ?></h3>
				<p><?php _e('Please consider to donate to our project','padma'); ?></p>
				<table>
					<tr>
						<td>
							<form style="text-align: center;" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
								<input name="cmd" type="hidden" value="_s-xclick"><br>
								<input name="encrypted" type="hidden" value="-----BEGIN PKCS7-----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-----END PKCS7----- "><br>
								<input alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" type="image"><br>
								<img src="https://www.paypalobjects.com/es_XC/i/scr/pixel.gif" alt="" width="1" height="1" border="0" class="jetpack-lazy-image--handled" data-lazy-loaded="1"><noscript>&lt;img src="https://www.paypalobjects.com/es_XC/i/scr/pixel.gif" alt="" width="1" height="1" border="0" /&gt;</noscript>
							</form>
						</td>
						<td>
							<a href="https://www.patreon.com/bePatron?u=11838968">
								<img src="https://www.padmaunlimited.com/wp-content/uploads/2018/09/become-a-patron.png">
							</a>
						</td>
					</tr>
				</table>
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
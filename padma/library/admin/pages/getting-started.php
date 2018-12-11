<?php

defined('ABSPATH') or die("No script kiddies please!");

?>
<div class="padma-admin-container">

	<img class="padma-logo" src="<?php echo padma_url() . '/library/admin/images/padma-theme-logo-square-250.png'; ?>">

	<div class="padma-admin-row menu">
		<a href="javascript:void(0)" onclick="openTabAdmin(event, 'welcome');">
			<div class="padma-admin-title tablink padma-admin-border-red">Welcome</div>
		</a><a href="javascript:void(0)" onclick="openTabAdmin(event, 'options');">
			<div class="padma-admin-title tablink">Options</div>
		</a><a href="javascript:void(0)" onclick="openTabAdmin(event, 'need-help');">
			<div class="padma-admin-title tablink">Need help?</div>
		</a><a href="javascript:void(0)" onclick="openTabAdmin(event, 'unlimited-growth');">
			<div class="padma-admin-title tablink">Unlimited growth</div>
		</a>
	</div>

	<div id="welcome" class="padma-admin-tab" style="">	
		<div class="content">
			<h1>Welcome!</h1>
			<p>Your <strong>Padma Theme Builder</strong> installation is ready.</p>
			<p><strong>You can start creating now!</strong></p>
			<br>
			<p>If you want to hide this page just change the Default Admin Page in <a href="?page=padma-options">Padma » Options</a>.</p>
			<div class="separator"></div>
			<h2>Padma | unlimited - Core features in the WordPress dashboard / admin menu.</h2>
			<div class="box">
				<h3>Padma | unlimited - Welcome!</h3>
				<p>(This page)</p>
				<p>Get access to General Information, Documentation & Support for Padma | unlimited - WordPress Theme Builder.</p>
				<p>Get access to blocks and templates to improve your Padma | unlimited Theme Builder.</p>
				<br>
				<p>If you are new to Padma | unlimited, please read our <a href="https://docs.padmaunlimited.com/">Free WordPress Theme Builder Documentation</a></p>
			</div>
			<div class="box">
				<h3>Padma | unlimited Editor</h3>
				<p>Padma | unlimited Editor is a powerful tool to design & customize your layouts and edit the visual style of your WordPress website through a graphical interface.</p>
				<p>Learn more abou it in the document <a href="https://docs.padmaunlimited.com/blog/basics/before-using-the-visual-editor/">Introduction to the Padma | unlimited Editor.</a></p>
				<a href="<?php echo home_url() . '/?visual-editor=true'; ?>" class="access-to-unlimited-editor"><span class="text">Access Padma | unlimited Editor</span><span class="line -right"></span><span class="line -top"></span><span class="line -left"></span><span class="line -bottom"></span></a>
			</div>
			<div class="box">
				<h3>If you like Padma | Unlimited</h3>
				<p>Please consider to donate to our project</p>
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
				<h3>Padma | Options</h3>
				<p>Setup your Google Analytics, SEO, favicons and other more advanced settings.</p>				
			</div>
			<h2 class="center">Padma | Tools</h2>
			<div class="box">
				<h3>System info.</h3>
				<p>You should provide this system information If you want to open a ticket or if you log a help request on the forums</p>
			</div>
			<div class="box">
				<h3>Snapshots</h3>
				<p>Here you can delete the snapshots of Padma Theme Builder to free up some disk space.</p>
			</div>
			<div class="box">
				<h3>Reset</h3>
				<p>Instructions to reset your <strong>Padma | Unlimited</strong> WordPress Theme Builder installation.</p>
			</div>
		</div>
	</div>

	<div id="need-help" class="padma-admin-tab" style="display:none">
		<div class="content">
			<h2 class="center">Need help?</h2>
			<p><strong>Padma | unlimited WordPress Theme Builder</strong> provides professional support and comprehensive documentation to help you bring your projects alive.</p>			
			<div class="separator"></div>
			<div class="box">
				<h3>Padma | Community</h3>
				<p>Join to our great community, we help us each other across different channels.</p>
				<p><a href="https://www.facebook.com/padmaunlimited/" target="_blank">Facebook Page</a></p>
				<p><a href="https://www.facebook.com/groups/367999217036886/" target="_blank">Facebook Users Grup (English)</a></p>
				<p><a href="https://www.facebook.com/groups/291445981578459/" target="_blank">Facebook Users Grup (Español)</a></p>
				<p><a href="https://twitter.com/PadmaUnlimited" target="_blank">Twitter</a></p>
				<p><a href="https://join.slack.com/t/padma-unlimited/shared_invite/enQtNTAxMzM1NjcwNTc5LTM2YzQ0ODRhYzBmZDc4N2UwOWE0MjBlMmQyZmQ2MTdjZTgyNjg1Mzk4ZjVlNGIxYjZkMjlmMTNhNmE3OWQ1YjY" target="_blank">Slack</a></p>
			</div>
			<!--
			<div class="box">
				<h3>Padma | Technical Support</h3>
				<p>24/7 professional support to help you solve any Padma | unlimited WordPress Theme Builder or Padma plugins incompatibilities or bugs.</p>
				<p>Support to migrate your website to Padma | unlimited WordPress Theme Builder.</p>				
			</div>
		    -->
			<div class="box">
				<h3>Padma | Documentation</h3>
				<p>Register with us and get <strong>free access</strong> to our complete and comprehensive documentation. <a target="_blank" href="https://docs.padmaunlimited.com/">docs.padmaunlimited.com</a></p>
			</div>
		</div>
	</div>

	<div id="unlimited-growth" class="padma-admin-tab" style="display:none">
		<div class="content">
			<h2 class="center">Padma | unlimited Growth</h2>
			<!--<p>Find third party developers original Templates & custom Blocks, designed to increase the possibilities to grow up  your projects. Design faster. Customize your Workflow with useful tools. Invest in other strategic areas.</p>-->
			<p>Develop, share and sale Templates & Custom Blocks. Working together will increase community growth possibilities. Design faster & Customize your Workflow with useful tools. Don´t hesitate to invest in other strategic areas to your business.</p>
			<div class="separator"></div>
			<div class="box">
				<h3>Padma | unlimited Templates</h3>
				<p><strong>Padma | unlimited Templates</strong> minimize your project development process, streamlining the design stage leading a faster content creation and upload.</p>
			</div>
			<div class="box">
				<h3>Padma | unlimited Blocks</h3>
				<p>Extend the functionality of your Padma | unlimited WordPress Theme Builder installation by adding useful custom blocks to your Projects.</p>
				<p>Services: Documentation, Lifesaver (Migrate from HTW/Blox), Child Theme, Templates on cloud, site monitor.</p>				
				<p>Shortcode block: integration & management of Woocommerce content. Contact form 7 & Gravity forms.</p>
				<p>Get into Padma Services to unlock the Padma Unlimited potential.</p>
			</div>
		</div>
	</div>

</div>
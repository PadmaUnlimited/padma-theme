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
		</a><a href="javascript:void(0)" onclick="openTabAdmin(event, 'custom-services');">
			<div class="padma-admin-title tablink">Custom services</div>
		</a>
	</div>

	<div id="welcome" class="padma-admin-tab" style="">	
		<div class="content">
			<h1>Welcome!</h1>
			<p>Your <strong>Padma Theme Builder</strong> installation is ready.</p>
			<p><strong>You can start creating now!</strong></p>
			<br>
			<p>If you want to hide this page just change the Default Admin Page in <a href="?page=padma-options">Padma » Options</a>.</p>
			<!--<h2>Padma | unlimited - WordPress Theme Builder |  Useful information</h2>-->
			<div class="separator"></div>
			<h2>Padma | unlimited - Core features in the WordPress dashboard / admin menu.</h2>
			<div class="box">
				<h3>Padma | unlimited - Welcome!</h3>
				<p>(This page)</p>
				<p>Get access to General Information, Documentation & Support for Padma | unlimited - WordPress Theme Builder.</p>
				<p>Get access to blocks and templates to improve your Padma | unlimited Theme Builder.</p>
				<br>
				<p>If you are new to Padma | unlimited, please read our <a href="#">WordPress Theme Builder first steps.</a></p>
			</div>
			<div class="box">
				<h3>Padma | unlimited Editor</h3>
				<p>Padma | unlimited Editor is a powerful tool to design & customize your layouts and edit the visual style of your WordPress website through a graphical interface.</p>
				<p>Learn more abou it in the document <a href="#">Introduction to the Padma | unlimited Editor.</a></p>
				<a href="<?php echo home_url() . '/?visual-editor=true'; ?>" class="access-to-unlimited-editor"><span class="text">Access Padma | unlimited Editor</span><span class="line -right"></span><span class="line -top"></span><span class="line -left"></span><span class="line -bottom"></span></a>
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
				<h3>Padma | Technical Support</h3>
				<p>24/7 professional support to help you solve any Padma | unlimited WordPress Theme Builder or Padma plugins incompatibilities or bugs.</p>
				<p>Support to migrate your website to Padma | unlimited WordPress Theme Builder.</p>				
			</div>
			<div class="box">
				<h3>Padma | Documentation</h3>
				<p>Register with us and get <strong>free access</strong> to our complete and comprehensive documentation.</p>
			</div>
		</div>
	</div>

	<div id="unlimited-growth" class="padma-admin-tab" style="display:none">
		<div class="content">
			<h2 class="center">Padma | unlimited Growth</h2>
			<p>Find third party developers original Templates & custom Blocks, designed to increase the possibilities to grow up  your projects. Design faster. Customize your Workflow with useful tools. Invest in other strategic areas.</p>
			<p>Develop, share and sale Templates & Custom Blocks. Working together will increase community growth possibilities. Design faster & Customize your Workflow with useful tools. Don´t hesitate to invest in other strategic areas to your business.</p>
			<div class="separator"></div>
			<div class="box">
				<h3>Padma | unlimited Templates</h3>
				<p><strong>Padma | unlimited Templates</strong> minimize your project development process, streamlining the design stage leading a faster content creation and upload.</p>
				<p><a href="">Access template gallery</a> | <a href="">Register to get access our template gallery</a></p>

			</div>
			<div class="box">
				<h3>Padma | unlimited Blocks</h3>
				<p>Extend the functionality of your Padma | unlimited WordPress Theme Builder installation by adding useful custom blocks to your Projects.</p>
				<p>Contact block: easy & responsive tool for contact pages management.</p>
				<p>Shortcode block: integration & management of Woocommerce content. Contact form 7 & Gravity forms.</p>
				<p>Services: Documentation, Backups (soon), WP Admin lock (soon), Site monitor (soon), Website auditing (soon), Templates on cloud (soon), Template market (soon), Life-Saver blocks (soon).</p>
				<p><a href="">Access unlimited blocks gallery</a> | <a href="">Register to get access unlimited blocks gallery</a></p>
			</div>
		</div>
	</div>

	<div id="custom-services" class="padma-admin-tab" style="display:none">
		<div class="content">
			<h2 class="center">Could not find what you are looking for?</h2>
			<h2 class="center">Custom Services will cover your business needs.</h2>			
			<div class="separator"></div>
			<div class="box">
				<h3>Padma | Custom Services</h3>
				<p>Website customization support.<br>(Adjusts our tools to your needs. Get help from our development team to customize your Padma experience)</p>
				<p>Website infrastructure advisory and configuration.<br>(Web server, web hosting, CDN configuration & improvements)</p>
				<p>Website design support.<br>(CSS, Templates, Shared layouts, SEO, Responsiveness)</p>			
				<p><a href="">Request a quote</a> | <a href="">Register to request a quote</a></p>
			</div>
		</div>
	</div>

</div>
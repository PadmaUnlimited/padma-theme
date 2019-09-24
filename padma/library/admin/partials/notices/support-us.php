<?php defined( 'ABSPATH' ) or exit; ?>

<div class="notice notice-info padma-unlimited-notice-rate">

	<img alt="Padma Unlimited" src="<?php echo get_template_directory_uri() . '/library/admin/images/padma-theme-logo-square-250.png'; ?>" class="avatar avatar-120 photo" height="120" width="120">

	<div class="padma-unlimited-notice-rate-content">

		<div class="padma-unlimited-notice-rate-content-text">
			<p><?php _e( 'Hello', 'padma' ); ?>,</p>
			<p><?php _e( 'Our team has been working really hard to bring this powerful tool to you. We hope you like it.', 'padma' ); ?></p>
			<h4><?php _e( 'Aim to collaborate?', 'padma' ); ?></h4>
			<p><?php _e( 'Your support is vital for success…', 'padma' ); ?></p>
			<ul>
				<li><?php _e( '- Become a sponsor via Patron or PayPal', 'padma' ); ?></li>
				<li><?php _e( '- Report errors in: https://www.padmaunlimited.com/bug-report/', 'padma' ); ?></li>
				<li><?php _e( '- Collaborate coding through GitHub', 'padma' ); ?></li>
				<li><?php _e( '- Suggest functionalities, blocks or plugins', 'padma' ); ?></li>
				<li><?php _e( '- Join our social networks', 'padma' ); ?></li>
				<li><?php _e( '- Share Padma Unlimited Builder with colleagues and friends', 'padma' ); ?></li>
				<li><?php _e( '- Spread the word!', 'padma' ); ?></li>
			</ul>			
			<p><?php _e( 'Let\'s build together!', 'padma' ); ?></p>
			<p><?php _e( '@PadmaTeam', 'padma' ); ?></p>
		</div>

		<p class="padma-unlimited-notice-rate-actions">			
			<a href="https://www.patreon.com/bePatron?u=11838968" class="button button-primary" target="_blank"><?php _e( 'Become a Patron', 'padma' ); ?></a>
			<a href="https://www.padmaunlimited.com/support-padma-unlimited-wordpress-template-builder/" class="button button-primary" target="_blank"><?php _e( 'Other support options', 'padma' ); ?></a>
			<a href="https://www.padmaunlimited.com/community" class="button button-secundary" target="_blank"><?php _e( 'Join to our community', 'padma' ); ?></a>			
			<a href="<?php echo self::get_dismiss_link(); ?>" class="padma-unlimited-notice-rate-dismiss"><?php _e( 'Dismiss', 'padma' ); ?></a>
		</p>

	</div>

</div>

<style>
	.padma-unlimited-notice-rate {
		position: relative;
		padding: 15px 20px;
	}
	.padma-unlimited-notice-rate .avatar {
		position: absolute;
		left: 20px;
		top: 20px;
	}
	.padma-unlimited-notice-rate-content {
		margin-left: 140px;
	}
	.padma-unlimited-notice-rate-content-text p {
		font-size: 15px;
	}
	p.padma-unlimited-notice-rate-actions {
		margin-top: 15px;
	}
	p.padma-unlimited-notice-rate-actions a {
		vertical-align: middle !important;
	}
	p.padma-unlimited-notice-rate-actions a + a {
		margin-left: 20px;
	}
	.padma-unlimited-notice-rate-dismiss {
		position: absolute;
		top: 10px;
		right: 10px;
		padding: 10px 15px 10px 21px;
		font-size: 13px;
		line-height: 1.23076923;
		text-decoration: none;
	}
	.padma-unlimited-notice-rate-dismiss:before {
		position: absolute;
		top: 8px;
		left: 0;
		margin: 0;
		-webkit-transition: all .1s ease-in-out;
		transition: all .1s ease-in-out;
		background: 0 0;
		color: #b4b9be;
		content: "\f153";
		display: block;
		font: 400 16px / 20px dashicons;
		height: 20px;
		text-align: center;
		width: 20px;
	}
	.padma-unlimited-notice-rate-dismiss:hover:before {
		color: #c00;
	}
</style>

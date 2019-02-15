<?php defined( 'ABSPATH' ) or exit; ?>

<div class="notice notice-info padma-unlimited-notice-rate">

	<img alt="Padma Unlimited" src="<?php echo get_stylesheet_directory_uri() . '/library/admin/images/padma-theme-logo-square-250.png'; ?>" class="avatar avatar-120 photo" height="120" width="120">

	<div class="padma-unlimited-notice-rate-content">

		<div class="padma-unlimited-notice-rate-content-text">
			<p><?php _e( 'Hello', 'padma-unlimited' ); ?>,</p>
			<p><?php _e( 'Our team has been working really hard to bring this powerful tool to you. We hope you like it.', 'padma-unlimited' ); ?></p>
			<h4><?php _e( 'Aim to collaborate?', 'padma-unlimited' ); ?></h4>
			<p><?php _e( 'Your support is vital for success…', 'padma-unlimited' ); ?></p>
			<ul>
				<li><?php _e( '- Become a sponsor via Patron', 'padma-unlimited' ); ?></li>
				<li><?php _e( '- Report errors in: https://www.padmaunlimited.com/bug-report/', 'padma-unlimited' ); ?></li>
				<li><?php _e( '- Collaborate coding through GitHub', 'padma-unlimited' ); ?></li>
				<li><?php _e( '- Suggest functionalities, blocks or plugins', 'padma-unlimited' ); ?></li>
				<li><?php _e( '- Join our social networks', 'padma-unlimited' ); ?></li>
				<li><?php _e( '- Share Padma Unlimited Builder with colleagues and friends', 'padma-unlimited' ); ?></li>
				<li><?php _e( '- Spread the word!', 'padma-unlimited' ); ?></li>
			</ul>			
			<p><?php _e( 'Let\'s build together!', 'padma-unlimited' ); ?></p>
			<p><?php _e( '@PadmaTeam', 'padma-unlimited' ); ?></p>
		</div>

		<p class="padma-unlimited-notice-rate-actions">			
			<a href="https://www.patreon.com/bePatron?u=11838968" class="button button-primary" target="_blank"><?php _e( 'Become a Patron', 'padma-unlimited' ); ?></a>
			<a href="https://www.padmaunlimited.com/community" class="button button-secundary" target="_blank"><?php _e( 'Join to our community', 'padma-unlimited' ); ?></a>
			<!--<a href="<?php ///echo self::get_dismiss_link( true ); ?>"><?php //_e( 'Remind me later', 'padma-unlimited' ); ?></a>-->
			<a href="<?php echo self::get_dismiss_link(); ?>" class="padma-unlimited-notice-rate-dismiss"><?php _e( 'Dismiss', 'padma-unlimited' ); ?></a>
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

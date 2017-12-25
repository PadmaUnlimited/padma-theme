<h2 class="nav-tab-wrapper big-tabs-tabs">
	<a class="nav-tab" href="#tab-general">General</a>
</h2>

<?php do_action('padma_admin_save_message'); ?>
<?php do_action('padma_admin_save_error_message'); ?>

<form method="post">
	<input type="hidden" value="<?php echo wp_create_nonce('padma-admin-nonce'); ?>" name="padma-admin-nonce" id="padma-admin-nonce" />

	<div class="big-tabs-container">

		<div class="big-tab" id="tab-general-content">

			<?php
			if ( is_main_site() ) {
			?>

				<div class="license-key-input-table">

					<h3 class="title">License Keys</h3>

					<p>
						Please enter your license key(s) here.  This will be used to authenticate your site so you can take advantage of the automatic updates.
						<br /><br />You may find your license key(s) in the <a href="http://padmatheme.com/dashboard" target="_blank">Padma Members Dashboard</a>.
					</p>

					<table class="form-table">

						<?php foreach ( apply_filters('padma_updater_products', array()) as $item_slug => $item_name ): ?>

							<tr valign="top">
								<th scope="row">
									<label for="<?php echo 'license-key-' . $item_slug ?>"><?php echo $item_name; ?></label>
								</th>
								<td>
									<input type="text" class="large-text" value="<?php echo esc_attr(padma_get_license_key($item_slug)); ?>" placeholder="Enter License Key" id="<?php echo 'license-key-' . $item_slug ?>" name="padma-admin-input[<?php echo 'license-key-' . $item_slug ?>]" />
									<?php
									if ( padma_get_license_key($item_slug) ) {

										$status = padma_get_license_status($item_slug);

										if ( $status == 'valid' ) {
									?>
											<input type="submit" class="button-secondary" name="padma-licenses[deactivate][<?php echo $item_slug; ?>]" value="<?php _e('Deactivate License'); ?>" />
											<span style="color:green;"><?php _e('Active'); ?></span>
										<?php } else { ?>
											<input type="submit" class="button-secondary" name="padma-licenses[save-and-activate][<?php echo $item_slug; ?>]" value="<?php _e('Save & Activate License'); ?>" />
										<?php } ?>
									<?php } else { ?>
										<input type="submit" class="button-secondary" name="padma-licenses[save-and-activate][<?php echo $item_slug; ?>]" value="<?php _e('Save & Activate License'); ?>" />
									<?php } ?>
								</td>
							</tr>

						<?php endforeach; ?>

					</table>

				</div><!-- .license-key-input-table -->

			<?php
			} else {
		}
		?>

			<?php
			?>


			<?php

			?>

		</div><!-- #tab-general-content -->



		</div>


	</div>



</form>

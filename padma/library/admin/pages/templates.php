<div id="export-template" style="display: none;">
	<h3><?php _e('Export Template','padma'); ?></h3>
	<p><?php _e('Fill out the information below to export all design settings, layouts, and blocks as a Padma Template','padma'); ?></p>

	<form id="export-template-form">
		<table class="form-table">
			<tbody>
			<tr valign="top">
				<th scope="row"><label for="template-export-name"><?php _e('Template Name','padma'); ?></label></th>
				<td><input id="template-export-name" type="text" name="skin-export-info[name]" class="regular-text" /></td>
			</tr>

			<?php
			$current_user = wp_get_current_user();
			?>

			<tr valign="top">
				<th scope="row"><label for="template-export-author"><?php _e('Template Author','padma'); ?></label></th>
				<td><input id="template-export-author" type="text" name="skin-export-info[author]" class="regular-text" value="<?php echo $current_user->display_name; ?>" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="template-export-version"><?php _e('Template Version','padma'); ?></label></th>
				<td><input id="template-export-version" type="text" name="skin-export-info[version]" placeholder="e.g. 1.0" class="medium-text" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="template-export-image"><?php _e('Template Image','padma'); ?></label></th>
				<td>
					<button id="template-export-image-button" class="button-secondary">
						<span class="wp-media-buttons-icon"></span>
						<?php _e('Select Image','padma'); ?>
					</button>
					<input id="template-export-image" type="hidden" name="skin-export-info[image-url]" class="medium-text" />
					<img src="" id="template-export-image-preview" style="display: none;" />
				</td>
			</tr>
			</tbody>
		</table>

		<p class="submit">
			<input type="submit" name="submit" id="export-template-submit" class="button button-primary" value="<?php _e('Export Template','padma'); ?>">
		</p>
	</form>
</div>

<h2><?php _e('Padma Templates','padma'); ?>
	<a href="#" class="add-new-h2" id="install-template"><?php _e('Install Template','padma'); ?></a>
	<a href="#TB_inline?width=500&height=600&inlineId=export-template" class="add-new-h2 thickbox" id="export-template"><?php _e('Export Current Template','padma'); ?></a>
</h2>

<div id="padma-admin-notifications"></div>

<div class="theme-browser" id="padma-templates-browser">
	<div class="themes padma-templates">
		<!-- ko foreach: templates -->
			<div class="theme padma-template" tabindex="0" aria-describedby="padma-action padma-name" data-bind="attr: { 'data-template-id': id }, css: { 'active': $parent.active().id == id, 'missing-image': !$data['image-url'], 'template-installing': (typeof $data['installing'] != 'undefined' && $data['installing']) }">

				<div class="theme-screenshot">
					<span class="template-loading-indicator" data-bind="visible: (typeof $data['installing'] != 'undefined' && $data['installing'])"></span>
					<img src="" alt="" data-bind="visible: $data['image-url'], attr: { 'src': $data['image-url'] }" />
				</div>

				<div class="theme-author" data-bind="text: 'By ' + author, visible: author"></div>

				<h3 class="theme-name" id="padma-name"><span data-bind="visible: $parent.active().id == id"><?php _e('Active: ','padma'); ?></span><!-- ko text: name --><!-- /ko --> <!-- ko text:version --><!-- /ko --></h3>

				<div class="theme-actions" data-bind="visible: (typeof $data['installing'] == 'undefined' || !$data['installing'])">
					<a href="#" class="button button-secondary delete-template" data-bind="click: $parent.deleteSkin, visible: (id != $parent.active().id && id != 'base')"><?php _e('Delete','padma'); ?></a>
					<a class="button button-primary" href="#" data-bind="click: $parent.activateSkin, visible: id != $parent.active().id"><?php _e('Activate','padma'); ?></a>
				</div>

			</div>
		<!-- /ko -->

		<?php 
		if(class_exists('padmaServices')){

			$padmaServices 	= new padmaServices();
			$padmaServices->setToken(get_option('padma_service_token'));
			$data 	= $padmaServices->getDashboardData();

			if( is_array($data->templates) && count($data->templates) > 0){
				echo '<hr class="templates">';
				echo '<h3>' . __('Templates available on your Padma Services Account','padma') . '</h3>';

				foreach ($data->templates as $key => $template) {

						$template 	= (array)$template;
						$id 		= $template['id'];
						$name 		= $template['name'];
						$screenshot = $template['image'];

						?>

						<div class="theme padma-template" tabindex="0">

							<div class="theme-screenshot">
								<span class="template-loading-indicator"></span>
								<img src="<?php echo $screenshot; ?>" alt="" />
							</div>

							<h3 class="theme-name" id="padma-name"><span><?php _e('Available: ','padma'); ?></span><?php echo $name; ?></h3>

							<div class="theme-actions">
								<a class="button button-primary install-cloud-template" id="template-<?php echo $id; ?>" data-token="<?php echo get_option('padma_service_token'); ?>" href="#"><?php _e('Install','padma'); ?></a>
							</div>

						</div>

					<?php
				} // foreach

				echo '<hr class="templates">';
			}
		}

		?>

		<div class="theme add-new-theme" id="add-blank-template">
			<a href="#">
				<div class="theme-screenshot"><span></span></div>
				<h3 class="theme-name"><?php _e('Add Blank Template','padma'); ?></h3>
			</a>
		</div>
	</div>
	<br class="clear">
</div>

<form id="upload-skin">
	<input type="file" />
</form>
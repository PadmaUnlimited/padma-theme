<div id="export-template" style="display: none;">
	<h3>Export Template</h3>
	<p>Fill out the information below to export all design settings, layouts, and blocks as a Padma Template</p>

	<form id="export-template-form">
		<table class="form-table">
			<tbody>
			<tr valign="top">
				<th scope="row"><label for="template-export-name">Template Name</label></th>
				<td><input id="template-export-name" type="text" name="skin-export-info[name]" class="regular-text" /></td>
			</tr>

			<?php
			$current_user = wp_get_current_user();
			?>

			<tr valign="top">
				<th scope="row"><label for="template-export-author">Template Author</label></th>
				<td><input id="template-export-author" type="text" name="skin-export-info[author]" class="regular-text" value="<?php echo $current_user->display_name; ?>" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="template-export-version">Template Version</label></th>
				<td><input id="template-export-version" type="text" name="skin-export-info[version]" placeholder="e.g. 1.0" class="medium-text" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="template-export-image">Template Image</label></th>
				<td>
					<button id="template-export-image-button" class="button-secondary">
						<span class="wp-media-buttons-icon"></span>
						Select Image
					</button>
					<input id="template-export-image" type="hidden" name="skin-export-info[image-url]" class="medium-text" />
					<img src="" id="template-export-image-preview" style="display: none;" />
				</td>
			</tr>
			</tbody>
		</table>

		<p class="submit">
			<input type="submit" name="submit" id="export-template-submit" class="button button-primary" value="Export Template">
		</p>
	</form>
</div>
<div id="save-on-cloud" style="display: none;">
	<h3>Save Template on Cloud</h3>
	<p>Fill out the information below to export all design settings, layouts, and blocks as a Padma Template</p>

	<form id="save-template-on-cloud-form">
		<table class="form-table">
			<tbody>
			<tr valign="top">
				<th scope="row"><label for="template-save-on-cloud-name">Name</label></th>
				<td><input id="template-save-on-cloud-name" type="text" name="skin-save-on-cloud-info[name]" class="regular-text" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="template-save-on-cloud-description">Description</label></th>
				<td><input id="template-save-on-cloud-description" type="text" name="skin-save-on-cloud-info[description]" class="regular-text" /></td>
			</tr>

			<?php
			$current_user = wp_get_current_user();
			?>

			<tr valign="top">
				<th scope="row"><label for="template-save-on-cloud-author">Author</label></th>
				<td><input id="template-save-on-cloud-author" type="text" name="skin-save-on-cloud-info[author]" class="regular-text" value="<?php echo $current_user->display_name; ?>" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="template-save-on-cloud-version">Version</label></th>
				<td><input id="template-save-on-cloud-version" type="text" name="skin-save-on-cloud-info[version]" placeholder="e.g. 1.0" class="medium-text" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="template-save-on-cloud-image">Image</label></th>
				<td>
					<button id="template-save-on-cloud-image-button" class="button-secondary">
						<span class="wp-media-buttons-icon"></span>
						Select Image
					</button>
					<input id="template-save-on-cloud-image" type="hidden" name="skin-save-on-cloud-info[image-url]" class="medium-text" />
					<img src="" id="template-save-on-cloud-image-preview" style="display: none;" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="template-save-on-cloud-visibility">Visibility</label><br><small> (Private: Only for me, Public: Available to sell in market)</small></th>
				<td><select id="template-save-on-cloud-visibility" name="skin-save-on-cloud-info[visibility]" class="medium-text">
					<option value='private'>Private</option>
					<option value='public'>Public</option>
				</select>
			</tr>

			<!--
			<tr valign="top">
				<th scope="row"><label for="template-save-on-cloud-price">Price (USD)</label></th>
				<td><input id="template-save-on-cloud-price" type="text" name="skin-save-on-cloud-info[price]" placeholder="19" class="medium-text" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="template-save-on-cloud-preview">Preview URL</label></th>
				<td><input id="template-save-on-cloud-preview" type="text" name="skin-save-on-cloud-info[preview]" placeholder="" class="medium-text" /></td>
			</tr>
			-->
			</tbody>
		</table>

		<p class="submit">
			<input type="submit" name="save-template-on-cloud" id="save-template-on-cloud-submit" class="button button-primary" value="Save Template on Cloud">
		</p>
	</form>
</div>

<h2>Padma Templates
	<a href="#" class="add-new-h2" id="install-template">Install Template</a>
	<a href="#TB_inline?width=500&height=600&inlineId=export-template" class="add-new-h2 thickbox" id="export-template">Export Current Template</a>
	<a href="#TB_inline?width=500&height=600&inlineId=save-on-cloud" class="add-new-h2 thickbox" id="export-template">Save Template on Cloud</a>
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

				<h3 class="theme-name" id="padma-name"><span data-bind="visible: $parent.active().id == id">Active: </span><!-- ko text: name --><!-- /ko --> <!-- ko text:version --><!-- /ko --></h3>

				<div class="theme-actions" data-bind="visible: (typeof $data['installing'] == 'undefined' || !$data['installing'])">
					<a href="#" class="button button-secondary delete-template" data-bind="click: $parent.deleteSkin, visible: (id != $parent.active().id && id != 'base')">Delete</a>
					<a class="button button-primary" href="#" data-bind="click: $parent.activateSkin, visible: id != $parent.active().id">Activate</a>
				</div>

			</div>
		<!-- /ko -->
		
		<?php 
		if(class_exists('padmaServices')){

			$padmaServices 	= new padmaServices();
			$padmaServices->setToken(get_option('padma_service_token'));
			$data 	= $padmaServices->getDashboardData();

			if( count($data->templates) > 0){
				echo '<hr class="templates">';
				echo "<h3>Templates available on your Padma Services Account</h3>";

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

							<h3 class="theme-name" id="padma-name"><span>Available: </span><?php echo $name; ?></h3>

							<div class="theme-actions">
								<a class="button button-primary install-cloud-template" id="template-<?php echo $id; ?>" data-token="<?php echo get_option('padma_service_token'); ?>" href="#">Install</a>
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
				<h3 class="theme-name">Add Blank Template</h3>
			</a>
		</div>
	</div>
	<br class="clear">
</div>

<form id="upload-skin">
	<input type="file" />
</form>
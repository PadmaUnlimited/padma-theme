<div id="export-template" style="display: none;">
	<h3>Export Template</h3>
	<p>Fill out the information below to export all design settings, layouts, and blocks as a Blox Template</p>

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

		<p class="submit"><input type="submit" name="submit" id="export-template-submit" class="button button-primary" value="Export Template"></p>
	</form>
</div>

<h2>Blox Templates <a href="#" class="add-new-h2" id="install-template">Install Template</a> <a href="#TB_inline?width=500&height=600&inlineId=export-template" class="add-new-h2 thickbox" id="export-template">Export Current Template</a></h2>

<div id="blox-admin-notifications"></div>

<div class="theme-browser" id="blox-templates-browser">
	<div class="themes blox-templates">
		<!-- ko foreach: templates -->
			<div class="theme blox-template" tabindex="0" aria-describedby="blox-action blox-name" data-bind="attr: { 'data-template-id': id }, css: { 'active': $parent.active().id == id, 'missing-image': !$data['image-url'], 'template-installing': (typeof $data['installing'] != 'undefined' && $data['installing']) }">

				<div class="theme-screenshot">
					<span class="template-loading-indicator" data-bind="visible: (typeof $data['installing'] != 'undefined' && $data['installing'])"></span>
					<img src="" alt="" data-bind="visible: $data['image-url'], attr: { 'src': $data['image-url'] }" />
				</div>

				<div class="theme-author" data-bind="text: 'By ' + author, visible: author"></div>

				<h3 class="theme-name" id="blox-name"><span data-bind="visible: $parent.active().id == id">Active: </span><!-- ko text: name --><!-- /ko --> <!-- ko text:version --><!-- /ko --></h3>

				<div class="theme-actions" data-bind="visible: (typeof $data['installing'] == 'undefined' || !$data['installing'])">
					<a href="#" class="button button-secondary delete-template" data-bind="click: $parent.deleteSkin, visible: (id != $parent.active().id && id != 'base')">Delete</a>
					<a class="button button-primary" href="#" data-bind="click: $parent.activateSkin, visible: id != $parent.active().id">Activate</a>
				</div>

			</div>
		<!-- /ko -->

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
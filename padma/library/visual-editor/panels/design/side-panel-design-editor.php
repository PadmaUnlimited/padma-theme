<?php
class PadmaSidePanelDesignEditor {


	public static function init() {

		if ( PadmaVisualEditor::get_current_mode() != 'design' )
			return false;

		add_action('padma_visual_editor_side_panel', array(__CLASS__, 'template'));

		add_action('padma_visual_editor_footer', array(__CLASS__, 'live_css_textarea'));

	}


	public static function live_css_textarea() {

		echo '<textarea id="live-css-content" name="live-css" data-group="general" style="display:none;">' . esc_textarea(PadmaSkinOption::get('live-css', false, null, false, false)) . '</textarea>';

	}


	public static function template() {

		echo '<div id="side-panel-top">';
			self::element_selector();
		echo '</div><!-- #side-panel-top -->';

		echo '<div id="side-panel-bottom">';
			self::editor();
		echo '</div><!-- #side-panel-bottom -->';

	}


	public static function element_selector() {

		echo '
			<ul id="element-selector-tabs">
				<li><a href="#design-editor-element-selector-container">Navigator</a></li>
				<li><a href="#design-editor-styles-container">Styles</a></li>

				<span id="side-panel-collapse-arrow" title="' . __('Toggle Design Editor','padma') . '" class="tooltip-top-right"></span>
			</ul>
		';

		echo '<div id="design-editor-element-selector-container">';

			echo '<ul id="design-editor-element-selector">';

			echo '</ul><!-- #design-editor-element-selector -->';

			echo '<span class="button button-blue" id="element-selector-show-all-elements">' . __('Show All Elements','padma') . '</span>';
			echo '<span class="button" id="element-selector-show-current-layout-elements">' . __('Show Current Layout Elements','padma') . '</span>';

		echo '</div><!-- #design-editor-element-selector-container -->';


		echo '<div id="design-editor-styles-container">';


			echo '<div id="design-editor-styles-nothing-selected" class="design-editor-styles-message">';

				echo '<p>' . __('You <strong>have not selected an element</strong> to edit.','padma') . '</p>';
				echo '<p>' . __('Use the inspector to inspect an element you want to edit.','padma') . '</p>';

				// Pending update docs
				//echo '<a href="http://docs.padmaunlimited.com/article/49-the-inspector" target="_blank">Learn more about the inspector</a>';

			echo '</div><!-- #design-editor-styles-nothing-selected -->';


			echo '<div id="design-editor-styles-no-styles" class="design-editor-styles-message">';

				echo '<p>' . __('This element does not have any customized properties or instances.','padma') . '</p>';

			echo '</div><!-- #design-editor-styles-nothing-selected -->';


			echo '<ul id="design-editor-styles">';

			echo '</ul><!-- #design-editor-styles -->';

		echo '</div><!-- #design-editor-styles-container -->';

	}


	public static function editor() {

		echo '
			<div class="design-editor-info" style="display: none;">
					<div class="design-editor-selection">
						<strong>' . __('Editing:','padma') . '</strong>

						<span class="design-editor-selection-details">
							<strong class="design-editor-selected-element"></strong>
							for <strong class="design-editor-selection-details-layout">all layouts</strong>
							<span class="design-editor-selection-details-state-container"><span class="design-editor-selection-details-state-before"></span> <strong class="design-editor-selection-details-state"></strong></span>
						</span>

						<span class="button button-small design-editor-info-button customize-element-for-layout">' . __('Customize For Current Layout','padma') . '</span>
						<span class="button button-small design-editor-info-button customize-for-regular-element">' . __('Customize Regular Element','padma') . '</span>
					</div>
				</div><!-- .design-editor-info -->

			<div class="design-editor-options-filter">
				<input type="text" id="options-filter" placeholder="Filter" title="Filter options">
				<a class="options-filter-reset"><span>x</span></a>
				<a class="options-filter-only-modified">' . __('Show only modified options','padma') . '<input type="checkbox" id="options-filter-only-modified"></a>
			</div>
			<div class="design-editor-options-container">
			
				<div class="design-editor-options" style="display:none;"></div><!-- .design-editor-options -->
						
			</div><!-- .design-editor-options-container -->
		';

	}

}
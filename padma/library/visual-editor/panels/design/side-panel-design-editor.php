<?php
class PadmaSidePanelDesignEditor {


	public static function init() {

		if ( PadmaVisualEditor::get_current_mode() != 'design' )
			return false;

		add_action('padma_visual_editor_side_panel', array(__CLASS__, 'template'));

		add_action('padma_visual_editor_footer', array(__CLASS__, 'live_css_textarea'));

	}


	public static function live_css_textarea() {

		echo '<textarea id="live-css" name="live-css" data-group="general" style="display:none;">' . esc_textarea(PadmaSkinOption::get('live-css', false, null, false, false)) . '</textarea>';

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

				<span id="side-panel-collapse-arrow" title="Toggle Design Editor" class="tooltip-top-right"></span>
			</ul>
		';

		echo '<div id="design-editor-element-selector-container">';

			echo '<ul id="design-editor-element-selector">';

			echo '</ul><!-- #design-editor-element-selector -->';

			echo '<span class="button button-blue" id="element-selector-show-all-elements">Show All Elements</span>';
			echo '<span class="button" id="element-selector-show-current-layout-elements">Show Current Layout Elements</span>';

		echo '</div><!-- #design-editor-element-selector-container -->';


		echo '<div id="design-editor-styles-container">';


			echo '<div id="design-editor-styles-nothing-selected" class="design-editor-styles-message">';

				echo '<p>You <strong>have not selected an element</strong> to edit.</p>';
				echo '<p>Use the inspector to inspect an element you want to edit.</p>';

				echo '<a href="http://docs.padmatheme.com/article/49-the-inspector" target="_blank">Learn more about the inspector</a>';

			echo '</div><!-- #design-editor-styles-nothing-selected -->';


			echo '<div id="design-editor-styles-no-styles" class="design-editor-styles-message">';

				echo '<p>This element does not have any customized properties or instances.</p>';

			echo '</div><!-- #design-editor-styles-nothing-selected -->';


			echo '<ul id="design-editor-styles">';

			echo '</ul><!-- #design-editor-styles -->';

		echo '</div><!-- #design-editor-styles-container -->';

	}


	public static function editor() {

		echo '
			<div class="design-editor-info" style="display: none;">
					<div class="design-editor-selection">
						<strong>Editing:</strong>

						<span class="design-editor-selection-details">
							<strong class="design-editor-selected-element"></strong>
							for <strong class="design-editor-selection-details-layout">all layouts</strong>
							<span class="design-editor-selection-details-state-container"><span class="design-editor-selection-details-state-before"></span> <strong class="design-editor-selection-details-state"></strong></span>
						</span>

						<span class="button button-small design-editor-info-button customize-element-for-layout">Customize For Current Layout</span>
						<span class="button button-small design-editor-info-button customize-for-regular-element">Customize Regular Element</span>
					</div>
				</div><!-- .design-editor-info -->

			<div class="design-editor-options-container">
			
				<div class="design-editor-options" style="display:none;"></div><!-- .design-editor-options -->
						
			</div><!-- .design-editor-options-container -->
		';

	}

}
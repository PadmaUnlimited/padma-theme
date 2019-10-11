<?php
padma_register_visual_editor_box('PadmaGridManagerBox');
class PadmaGridManagerBox extends PadmaVisualEditorBoxAPI {

	/**
	 *	Slug/ID of panel.  Will be used for HTML IDs and whatnot.
	 **/
	protected $id = 'grid-manager';


	/**
	 * Name of panel.  This will be shown in the title.
	 **/
	protected $title;

	protected $description;


	/**
	 * Which mode to put the panel on.
	 **/
	protected $mode = 'grid';

	protected $center = false;

	protected $width = 600;

	protected $height = 420;

	protected $closable = true;

	protected $draggable = false;

	protected $resizable = false;

	protected $black_overlay = true;

	protected $black_overlay_opacity = 0.3;

	protected $black_overlay_iframe = true;

	protected $load_with_ajax = true;

	protected $load_with_ajax_callback = 'afterGridManagerLoad();';


	function __construct(){
		$this->title = __('Grid Manager','padma');
		$this->description = __('Choose a preset or a page to clone','padma');
	}
	public function content() {

		$current_layout = padma_post('layout');

		$pages_to_clone_select_options = self::clone_pages_options();
		$templates_to_assign_select_options = self::templates_to_assign_select_options();

?>
		<ul id="grid-manager-tabs" class="tabs">
			<?php			
			if ( $pages_to_clone_select_options !== '' || $templates_to_assign_select_options !== '' ) {

				echo '<li><a href="#grid-manager-tab-clone-page">Clone Existing Layout</a></li>';
				echo '<li><a href="#grid-manager-tab-presets">Presets</a></li>';

			} else {

				echo '<li><a href="#grid-manager-tab-presets">Presets</a></li>';

			}

			if ( $templates_to_assign_select_options !== '' && strpos($current_layout, 'template-') === false ){
				echo '<li><a href="#grid-manager-tab-assign-template">Use Shared Layout</a></li>';
			}

			echo '<li><a href="#grid-manager-tab-import-export">Import/Export</a></li>';
			?>
		</ul>

		<div id="grid-manager-tab-presets" class="tab-content">

			<div id="grid-manager-presets-step-1">	
				<div class="grid-manager-presets-row">
					<span class="layout-preset layout-preset-selected" id="layout-right-sidebar" title="Content | Sidebar">
						<img src="<?php echo padma_url() . '/library/visual-editor/images/layouts/layout-right-sidebar.png'; ?>" alt="" />
					</span>

					<span class="layout-preset" id="layout-left-sidebar" title="Sidebar | Content">
						<img src="<?php echo padma_url() . '/library/visual-editor/images/layouts/layout-left-sidebar.png'; ?>" alt="" />
					</span>

					<span class="layout-preset" id="layout-two-right" title="Content | Sidebar 1 | Sidebar 2">
						<img src="<?php echo padma_url() . '/library/visual-editor/images/layouts/layout-two-right.png'; ?>" alt="" />
					</span>
				</div>

				<div class="grid-manager-presets-row">
					<span class="layout-preset" id="layout-two-both" title="Sidebar 1 | Content | Sidebar 2">
						<img src="<?php echo padma_url() . '/library/visual-editor/images/layouts/layout-two-both.png'; ?>" alt="" />
					</span>

					<span class="layout-preset" id="layout-all-content" title="Content">
						<img src="<?php echo padma_url() . '/library/visual-editor/images/layouts/layout-all-content.png'; ?>" alt="" />
					</span>
				</div>
			</div><!-- #grid-manager-presets-step-1 -->

			<div id="grid-manager-presets-step-2">

				<h4>Select Which Blocks to Mirror</h4>

				<p class="grid-manager-info">To save time, Padma allows you to "mirror" your blocks.  If you already have a widget area or sidebar that's configured, you may choose to use it by using the select boxes below.</p>

				<div id="grid-manager-presets-mirroring-column-1" class="grid-manager-presets-mirroring-column">
					<div id="grid-manager-presets-mirroring-select-header">
						<h5>Header</h5>

						<div class="select-container">
							<select>
								<option value="">&mdash; Do Not Mirror &mdash;</option>
								<?php
								echo self::get_blocks_select_options_for_mirroring('header');
								?>
							</select>
						</div><!-- .select-container -->
					</div>

					<div id="grid-manager-presets-mirroring-select-navigation">
						<h5>Navigation</h5>

						<div class="select-container">
							<select>
								<option value="">&mdash; Do Not Mirror &mdash;</option>
								<?php
								echo self::get_blocks_select_options_for_mirroring('navigation');
								?>
							</select>
						</div><!-- .select-container -->
					</div>

					<div id="grid-manager-presets-mirroring-select-content">
						<h5>Content</h5>

						<div class="select-container">
							<select>
								<option value="">&mdash; Do Not Mirror &mdash;</option>
								<?php
								echo self::get_blocks_select_options_for_mirroring('content');
								?>
							</select>
						</div><!-- .select-container -->
					</div>
				</div>

				<div id="grid-manager-presets-mirroring-column-2" class="grid-manager-presets-mirroring-column">
					<div id="grid-manager-presets-mirroring-select-sidebar-1">
						<h5>Sidebar 1</h5>

						<div class="select-container">
							<select>
								<option value="">&mdash; Do Not Mirror &mdash;</option>
								<?php
								echo self::get_blocks_select_options_for_mirroring('widget-area');
								?>
							</select>
						</div><!-- .select-container -->
					</div>

					<div id="grid-manager-presets-mirroring-select-sidebar-2">
						<h5>Sidebar 2</h5>

						<div class="select-container">
							<select>
								<option value="">&mdash; Do Not Mirror &mdash;</option>
								<?php
								echo self::get_blocks_select_options_for_mirroring('widget-area');
								?>
							</select>
						</div><!-- .select-container -->
					</div>

					<div id="grid-manager-presets-mirroring-select-footer">
						<h5>Footer</h5>

						<div class="select-container">
							<select>
								<option value="">&mdash; Do Not Mirror &mdash;</option>
								<?php
								echo self::get_blocks_select_options_for_mirroring('footer');
								?>
							</select>
						</div><!-- .select-container -->
					</div>
				</div>

			</div><!-- #grid-manager-presets-step-2 -->

			<div class="grid-manager-buttons">
				<span class="grid-manager-use-empty-grid">Use Empty Grid</span>

				<?php
				if ( $pages_to_clone_select_options !== '' ) {

					$next_button_style = null;
					$use_button_style = ' style="display: none;"';

				} else {

					$next_button_style = ' style="display: none;"';
					$use_button_style = null;

				}

				echo '<span id="grid-manager-button-preset-next" class="button grid-manager-button-next"' . $next_button_style . '>Next &rarr;</span>';
				echo '<span id="grid-manager-button-preset-use-preset" class="button grid-manager-button-next"' . $use_button_style . '>Finish &rarr;</span>';
				echo '<span id="grid-manager-button-preset-previous" class="button grid-manager-button-previous" style="display: none;">&larr; Previous</span>';
				?>
			</div>

		</div><!-- #grid-manager-tab-presets -->

		<?php
		if ( $pages_to_clone_select_options !== '' || $templates_to_assign_select_options !== '' ) {
		?>
		<div id="grid-manager-tab-clone-page" class="tab-content">

			<h4>Choose a Layout to Clone</h4>

			<?php
			echo '<div class="select-container"><select id="grid-manager-pages-to-clone">';

				echo '<optgroup label="&mdash; Pages &mdash;">';

				echo $pages_to_clone_select_options;

                echo '</optgroup>';

                echo '<optgroup label="&mdash; Shared Layouts &mdash;">';

				echo $templates_to_assign_select_options;

                echo '</optgroup>';

            echo '</select></div><!-- .select-container -->';
			?>

			<div class="grid-manager-buttons">
				<span class="grid-manager-use-empty-grid">Use Empty Grid</span>

				<span id="grid-manager-button-clone-page" class="button grid-manager-button-next">Clone Layout &rarr;</span>
			</div>

		</div><!-- #grid-manager-tab-clone-page -->
		<?php
		}


		if ( $templates_to_assign_select_options !== '' && strpos($current_layout, 'template-') === false ) {
		?>
		<div id="grid-manager-tab-assign-template" class="tab-content">

			<h4>Choose a Shared Layout</h4>

			<?php
			echo '<div class="select-container"><select id="grid-manager-assign-template">';

				echo '<option value="" disabled="disabled">&mdash; Select a Shared Layout &mdash;</option>';

				echo $templates_to_assign_select_options;

			echo '</select></div><!-- .select-container -->';
			?>

			<div class="grid-manager-buttons">
				<span class="grid-manager-use-empty-grid">Use Empty Grid</span>

				<span id="grid-manager-button-assign-template" class="button grid-manager-button-next">Assign Layout &rarr;</span>
			</div>

		</div><!-- #grid-manager-tab-assign-template -->
		<?php
		}
		?>

		<div id="grid-manager-tab-import-export" class="tab-content">

			<div id="grid-manager-import" class="grid-manager-buttons grid-manager-import-export-group">
				<h4>Import Layout</h4>
				<p>Select the Padma Layout file you would like to import.<br /><br /><strong>Note:</strong> When you browse to and select a file below the imported layout's blocks will automatically be added to the current layout.</p>
				<input type="file" />
				<span class="button" id="grid-manager-import-select-file">Select File &amp; Import</span>
			</div><!-- #grid-manager-import -->

			<div id="grid-manager-export" class="grid-manager-buttons grid-manager-import-export-group">
				<h4>Export Current Layout</h4>
				<p>Clicking on the button below will package up the current layout and its blocks into a file to be saved and imported later.</p>
				<span class="button" id="grid-manager-export-download-file">Download Export File</span>
			</div><!-- #grid-manager-export -->

		</div><!-- #grid-manager-tab-import-export -->

	<?php
	}


	static function get_blocks_select_options_for_mirroring($block_type) {

		$return = '';	

		$blocks = PadmaBlocksData::get_blocks_by_type($block_type);

		//If there are no blocks, then just return the Do Not Mirror option.
		if ( !isset($blocks) || !is_array($blocks) )
			return $return;

		foreach ( $blocks as $block_id => $block ) {

			//Get the block instance
			$block = PadmaBlocksData::get_block($block_id);

			//If the block is mirrored, skip it
			if ( padma_get('mirror-block', $block['settings'], false) )
				continue;

			//If the block is in the same layout as the current block, then do not allow it to be used as a block to mirror.
			if ( $block['layout'] == padma_post('layout') )
				continue;

			//Create the default name by using the block type and ID
			$default_name = PadmaBlocks::block_type_nice($block['type']);

			//If we can't get a name for the layout, then things probably aren't looking good.  Just skip this block.
			if ( !($layout_name = PadmaLayout::get_name($block['layout'])) )
				continue;

			//Get alias if it exists, otherwise use the default name
			$return .= '<option value="' . $block['id'] . '">' . padma_get('alias', $block['settings'], $default_name) . ' &ndash; ' . $layout_name . '</option>';  

		}

		return $return;

	}


	static function clone_pages_options() {

		$return = '';

		if ( !$customized_layouts = get_transient( 'pu_customized_layouts_template_' . PadmaOption::$current_skin ) ) {
			return $return;
		}

		foreach ( $customized_layouts as $id ) {

			$name_prefix = PadmaLayout::get_layout_parents_names($id);

			$return .= '<option value="' . $id . '">' . $name_prefix . PadmaLayout::get_name($id) . '</option>';

		}

		return $return;		

	}


	static function templates_to_assign_select_options() {

		$templates = PadmaLayout::get_templates();

		$return = '';

		foreach ( $templates as $id => $name) {

			$return .= '<option value="template-' . $id . '">' . $name . '</option>';

		}

		return $return;

	}


}
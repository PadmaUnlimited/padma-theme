<?php
padma_register_visual_editor_box('PadmaSnapshotsBox');
class PadmaSnapshotsBox extends PadmaVisualEditorBoxAPI {


	/**
	 *	Slug/ID of panel.  Will be used for HTML IDs and whatnot.
	 **/
	protected $id = 'snapshots';


	/**
	 * Name of panel.  This will be shown in the title.
	 **/
	protected $title = 'Snapshots';

	protected $description;


	/**
	 * Which mode to put the panel on.
	 **/
	protected $mode = 'all';

	protected $center = true;

	protected $width = 400;

	protected $height = 500;

	protected $min_width = 350;

	protected $min_height = 200;

	protected $closable = true;

	protected $draggable = true;

	protected $resizable = false;

	function __construct(){
		$this->description = __('Restore your work with snapshots.','padma');
	}


	public function content() {

		echo '
		<span class="button button-blue" data-bind="click: saveSnapshot">Save Snapshot</span>
		<span class="spinner"></span>

		<ul id="snapshots-list" data-bind="foreach: snapshots">
			<li data-bind="attr: {id: \'snapshot-\' + id}">
				<span class="snapshot-timestamp" data-bind="text: $parent.formatSnapshotDatetime(timestamp)"></span>
				<span class="snapshot-delete" data-bind="click: $parent.deleteSnapshot" title="Delete Snapshot">Delete</span>

				<span class="button button-small" data-bind="click: $parent.rollbackToSnapshot">Rollback</span>

				<p class="snapshot-comments" data-bind="text:comments, visible: comments"></p>
			</li>
		</ul>
		';

	}


}
define(['jquery', 'knockout'], function($, ko) {

	var snapshots = {
		init: function() {

			snapshots.bind();
			snapshots.setupViewModel();

		},

		setupViewModel: function() {

			Blox.viewModels.snapshots = {
				snapshots: ko.observableArray(Blox.snapshots),
				formatSnapshotDatetime: function(datetime) {

					var datetimeFrags = datetime.split(/[- :]/);

					return new Date(Date.UTC(datetimeFrags[0], datetimeFrags[1] - 1, datetimeFrags[2], datetimeFrags[3], datetimeFrags[4], datetimeFrags[5])).toLocaleString();

				},
				rollbackToSnapshot: function(data, event) {

					if ( !confirm("Are you sure you wish to rollback?\n\nYou will lose all between this snapshot and now unless you save another snapshot.") )
						return false;

					var button = $(event.target);

					if ( button.attr('disabled') )
						return false;

					/* Disable button temporarily */
					button.attr('disabled', true);
					button.addClass('button-depressed');
					button.text('Rolling Back..');

					/* Rollback */
					$.post(Blox.ajaxURL, {
						security: Blox.security,
						action: 'padma_visual_editor',
						method: 'rollback_to_snapshot',
						layout: Blox.viewModels.layoutSelector.currentLayout(),
						snapshot_id: data.id,
						mode: Blox.mode
					}, function(response) {

						if ( typeof response.error != 'undefined' )
							return;

						showNotification({
							id: 'rolled-back-successfully',
							message: 'Successfully rolled back to snapshot.<br /><br /><strong>Refreshing Visual Editor in 3 seconds</strong>.',
							success: true
						});

						button.text('Rolled Back!');

						/* Reload the Visual Editor */
						setTimeout(function() {
							allowVEClose();
							document.location.reload(true);
						}, 1000);

					});

				},
				deleteSnapshot: function(data, event) {

					if ( !confirm("Are you sure you wish to delete this snapshot?\n\nYou cannot undo this or restore another snapshot to bring this snapshot back.") )
						return false;

					var button = $(event.target);

					if ( button.hasClass('deletion-in-progress') )
						return false;

					/* Disable button temporarily */
					button.addClass('deletion-in-progress');

					/* Delete snapshot */
					$.post(Blox.ajaxURL, {
						security: Blox.security,
						action: 'padma_visual_editor',
						method: 'delete_snapshot',
						layout: Blox.viewModels.layoutSelector.currentLayout(),
						snapshot_id: data.id,
						mode: Blox.mode
					}, function (response) {

						if ( typeof response.error != 'undefined' )
							return;

						showNotification({
							id: 'deleted-snapshot-successfully',
							message: 'Successfully deleted snapshot.',
							success: true
						});

						Blox.viewModels.snapshots.snapshots.remove(data);

					});


				},
				saveSnapshot: function(data, event) {

					var button = $(event.target);

					if ( button.attr('disabled') )
						return false;

					/* Disable button temporarily */
					button.attr('disabled', true);
					button.text('Saving Snapshot...');

					/* Add the snapshot */
					button.siblings('.spinner').show();

					/* Prompt for comments about snapshot */
					var snapshotComments = prompt("(Optional)\n\nEnter name or description of the changes in this snapshot.");

					$.post(Blox.ajaxURL, {
						security: Blox.security,
						action: 'padma_visual_editor',
						method: 'save_snapshot',
						layout: Blox.viewModels.layoutSelector.currentLayout(),
						mode: Blox.mode,
						snapshot_comments: snapshotComments
					}, function(response) {

						if ( typeof response.timestamp == 'undefined' )
							return;

						showNotification({
							id: 'snapshot-saved',
							message: 'Snapshot saved.',
							success: true
						});

						Blox.viewModels.snapshots.snapshots.unshift({
							id: response.id,
							timestamp: response.timestamp,
							comments: response.comments
						});

						button.text('Save Snapshot');
						button.removeAttr('disabled');
						button.siblings('.spinner').hide();

					});

				}
			}

			$(document).ready(function() {
				ko.applyBindings(Blox.viewModels.snapshots, $('#box-snapshots').get(0));
			});

		},

		bind: function() {


		}
	}


	return snapshots;

});
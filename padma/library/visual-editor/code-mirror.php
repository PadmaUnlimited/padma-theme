<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php echo get_bloginfo('charset'); ?>" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<?php

	$baseURL 	= padma_url() . '/library/visual-editor/scripts';
	$mode 		= $_GET['mode'];

	if($mode == 'html'){
		$mode = 'htmlmixed';
		$mode_text = 'custom code';
	}else{
		$mode_text = $mode;
	}
	/**
	 *
	 * Load Styles
	 *
	 */	
	$styles 	= array(
		'/deps/code-mirror/codemirror.css',

		// Dialog
		'/deps/code-mirror/addon/dialog/dialog.css',

		// Lint
		'/deps/code-mirror/addon/lint/lint.css',

		// Hint
		'/deps/code-mirror/addon/hint/show-hint.css',

		// Search
		'/deps/code-mirror/addon/search/matchesonscrollbar.css',
	);

	if(in_array('night', $_COOKIE)){
		if($_COOKIE['night']=='true'){
			$styles[] = '/deps/code-mirror/theme/night.css';
		}		
	}

	?><link rel="stylesheet" href="<?php echo padma_url() . '/library/admin/css/admin-padma.css'; ?> "><?php
	foreach ($styles as $key => $file) {
		?><link rel="stylesheet" href="<?php echo $baseURL . $file ; ?> "><?php
	}

	/**
	 *
	 * Load Scripts
	 *
	 */
	$scripts = array(
		'/deps/code-mirror/codemirror.js',

		// Dialog
		'/deps/code-mirror/addon/dialog/dialog.js',

		// Lint
		'/deps/csslint.js',
		'/deps/code-mirror/addon/lint/lint.js',
		'/deps/code-mirror/addon/lint/css-lint.js',

		// Hint
		'/deps/code-mirror/addon/hint/show-hint.js',
		'/deps/code-mirror/addon/hint/css-hint.js',

		// Edit
		'/deps/code-mirror/addon/edit/closebrackets.js',
		'/deps/code-mirror/addon/edit/closetag.js',
		'/deps/code-mirror/addon/edit/continuelist.js',
		//'/deps/code-mirror/addon/edit/matchtags.js',
		'/deps/code-mirror/addon/edit/matchbrackets.js',
		'/deps/code-mirror/addon/edit/trailingspace.js',

		// Display
		'/deps/code-mirror/addon/display/placeholder.js',

		// Selection
		'/deps/code-mirror/addon/selection/active-line.js',		
		'/deps/code-mirror/addon/selection/mark-selection.js',		
		'/deps/code-mirror/addon/selection/selection-pointer.js',

		// Search
		'/deps/code-mirror/addon/search/search.js',
		'/deps/code-mirror/addon/search/searchcursor.js',
		'/deps/code-mirror/addon/search/match-highlighter.js',
		'/deps/code-mirror/addon/search/matchesonscrollbar.js',
		'/deps/code-mirror/addon/search/jump-to-line.js',
	);

	$scripts[] = '/deps/code-mirror/mode/'.$mode.'/'.$mode.'.js';

?>
<style type="text/css">
	body{
		margin: 0;
		padding: 0;
		height: 100vh;
	}
	.CodeMirror{
		height: 100vh;
		background-color: : #f8f8f8;
	}
	/* Loading indicator */
	@-webkit-keyframes spin {
		from {
			-webkit-transform: rotate(0deg);
		}
		to {
			-webkit-transform: rotate(360deg);
		}
	}

	@-moz-keyframes spin {
		from {
			-moz-transform: rotate(0deg);
		}
		to {
			-moz-transform: rotate(360deg);
		}
	}

	@-ms-keyframes spin {
		from {
			-ms-transform: rotate(0deg);
		}
		to {
			-ms-transform: rotate(360deg);
		}
	}

	.live-css-loading-indicator {
		position: absolute;
		display: block;
		width: 80px;
		height: 80px;
		left: 50%;
		top: 50%;
		margin: -40px 0 0 -40px;
		text-align: center;
		-webkit-animation: spin 1500ms infinite linear;
		-moz-animation: spin 1500ms infinite linear;
		animation: spin 1500ms infinite linear;
		background-image: url(data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgZGF0YS1pY29uPSJjb2ciIHdpZHRoPSIxMjgiIGhlaWdodD0iMTI4IiBjbGFzcz0iaWNvbmljIGljb25pYy1jb2cgaWNvbmljLW9yaWVudGF0aW9uLXNxdWFyZSIgdmlld0JveD0iMCAwIDEyOCAxMjgiPgogIDxnIGRhdGEtd2lkdGg9IjEyOCIgZGF0YS1oZWlnaHQ9IjEyOCIgY2xhc3M9Imljb25pYy1sZyIgZGlzcGxheT0iaW5saW5lIj4KICAgIDxwYXRoIGQ9Ik0xMjggNzMuOXYtMTkuOWwtMTQuMy0zLjZjLTEuMi00LjItMi44LTguMy01LTEybDcuNS0xMi43LTE0LjEtMTQuMS0xMi43IDcuNmMtMy43LTIuMS03LjgtMy44LTEyLTVsLTMuNS0xNC4yaC0xOS45bC0zLjYgMTQuM2MtNC4yIDEuMi04LjMgMi44LTEyIDVsLTEyLjctNy41LTE0IDE0IDcuNiAxMi43Yy0yLjEgMy43LTMuOCA3LjgtNSAxMmwtMTQuMyAzLjZ2MTkuOWwxNC4zIDMuNmMxLjIgNC4yIDIuOCA4LjMgNSAxMmwtNy41IDEyLjcgMTQuMSAxNC4xIDEyLjctNy42YzMuNyAyLjEgNy44IDMuOCAxMiA1bDMuNyAxNC4zaDE5LjdsMy42LTE0LjNjNC4yLTEuMiA4LjMtMi44IDEyLTVsMTIuNyA3LjUgMTQuMS0xNC4xLTcuNi0xMi43YzIuMS0zLjcgMy44LTcuOCA1LTEybDE0LjItMy42em0tNjQgMjQuMWMtMTguOCAwLTM0LTE1LjItMzQtMzRzMTUuMi0zNCAzNC0zNCAzNCAxNS4yIDM0IDM0LTE1LjIgMzQtMzQgMzR6IgogICAgY2xhc3M9Imljb25pYy1jb2ctYm9keSBpY29uaWMtZWxlbWVudC1maWxsIiAvPgogIDwvZz4KPC9zdmc+);
		background-size: 80px;
		opacity: 0.6;
	}
	.live-css-loading-indicator.hidden {
		display: none;
	}
	a.how-to{
	    position: fixed;
    	right: 10px;
    	z-index: 1000;
	}
</style>
</head>
<body>
	<a target="_blank" href="https://docs.padmaunlimited.com/blog/basics/live-css-hotkeys/" class="how-to">How to?</a>
	<textarea id="code" name="code" class="code" placeholder="Your awesome <?php echo $mode_text; ?> goes here"></textarea>
<?php
foreach ($scripts as $key => $file) {
	?><script type="text/javascript" src="<?php echo $baseURL . $file; ?>" charset="utf-8"></script><?php
}
?>
<div id="ve-loading-overlay">
	<div class="lotus">
		<div class="lotus_leaf"></div>
		<div class="lotus_leaf"></div>
		<div class="lotus_leaf"></div>
		<div class="lotus_leaf"></div>
		<div class="lotus_leaf"></div>
		<div class="lotus_leaf"></div>
		<div class="lotus_leaf"></div>
		<div class="lotus_leaf"></div>
	</div>
</div>
</body>
</html>
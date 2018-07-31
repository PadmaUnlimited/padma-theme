<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php echo get_bloginfo('charset'); ?>" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<?php

	$baseURL 	= padma_url() . '/library/visual-editor/' . ((defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? 'scripts-src' : 'scripts-src');
	$mode 		= $_GET['mode'];
	if($mode == 'html'){
		$mode = 'htmlmixed';
	}
	/**
	 *
	 * Load Styles
	 *
	 */	
	$styles 	= array(
						'/deps/code-mirror/codemirror.css',						
						'/deps/code-mirror/addon/hint/show-hint.css',
						);

	if($_COOKIE['night']=='true'){
		$styles[] = '/deps/code-mirror/theme/night.css';
	}

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
		'/deps/code-mirror/addon/hint/show-hint.js',
		'/deps/code-mirror/addon/hint/css-hint.js',
		'/deps/code-mirror/addon/edit/closebrackets.js',
		'/deps/code-mirror/addon/display/placeholder.js',
		'/deps/code-mirror/addon/selection/active-line.js',		
	);
	//'/deps/code-mirror/mode/htmlmixed/htmlmixed.js',
	$scripts[] = '/deps/code-mirror/mode/'.$mode.'/'.$mode.'.js';

	


?>

<style type="text/css">
	body{
		margin: 0;
		padding: 0;
		height: 100vh;4
	}
	.CodeMirror{
		height: 100vh;
		background-color: : #f8f8f8;
	}

</style>
</head>
<body>
	<textarea id="code" name="code" class="code" placeholder="Your awesome <?php echo $mode; ?> goes here"></textarea>
<?php
foreach ($scripts as $key => $file) {
	?><script type="text/javascript" src="<?php echo $baseURL . $file; ?> " charset="utf-8"></script><?php
}
?>
</body>
</html>
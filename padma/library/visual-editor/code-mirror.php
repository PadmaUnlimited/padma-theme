<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php echo get_bloginfo('charset'); ?>" />
<link rel="profile" href="http://gmpg.org/xfn/11" />

<link rel="stylesheet" href="<?php echo padma_url() . '/library/visual-editor/' . ((defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? 'scripts-src' : 'scripts-src') . '/deps/code-mirror/codemirror.css'; ?> ">

<link rel="stylesheet" href="<?php echo padma_url() . '/library/visual-editor/' . ((defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? 'scripts-src' : 'scripts-src') . '/deps/code-mirror/addon/hint/show-hint.css'; ?> ">

<script type="text/javascript" src="<?php echo padma_url() . '/library/visual-editor/' . ((defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? 'scripts-src' : 'scripts-src') . '/deps/code-mirror/codemirror.js'; ?> " charset="utf-8"></script>
<script type="text/javascript" src="<?php echo padma_url() . '/library/visual-editor/' . ((defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? 'scripts-src' : 'scripts-src') . '/deps/code-mirror/mode/css/css.js'; ?> " charset="utf-8"></script>
<script type="text/javascript" src="<?php echo padma_url() . '/library/visual-editor/' . ((defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? 'scripts-src' : 'scripts-src') . '/deps/code-mirror/addon/hint/show-hint.js'; ?> " charset="utf-8"></script>
<script type="text/javascript" src="<?php echo padma_url() . '/library/visual-editor/' . ((defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? 'scripts-src' : 'scripts-src') . '/deps/code-mirror/addon/hint/css-hint.js'; ?> " charset="utf-8"></script>
<script type="text/javascript" src="<?php echo padma_url() . '/library/visual-editor/' . ((defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? 'scripts-src' : 'scripts-src') . '/deps/code-mirror/addon/selection/active-line.js'; ?> " charset="utf-8"></script>

</head>
<body>
	<textarea id="code" name="code"></textarea>
	<script>
		var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
			extraKeys: {"Ctrl-Space": "autocomplete"},
			styleActiveLine: true,
			lineNumbers: true,
    		lineWrapping: true,
		});
    </script>
</body>
</html>
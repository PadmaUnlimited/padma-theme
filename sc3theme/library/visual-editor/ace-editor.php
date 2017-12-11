<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>

<head>

<meta charset="<?php echo get_bloginfo('charset'); ?>" />
<link rel="profile" href="http://gmpg.org/xfn/11" />

<style type="text/css">
#ace-editor {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
}
</style>

<script type="text/javascript" src="<?php echo blox_url() . '/library/visual-editor/' . ((defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? 'scripts-src' : 'scripts-src') . '/deps/ace/ace.js'; ?> " charset="utf-8"></script>

</head>

<body>

	<div id="ace-editor"></div>

</body>
</html>
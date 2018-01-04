// Copyright (c) 2015, Fujana Solutions - Moritz Maleck. All rights reserved.
// For licensing, see LICENSE.md

CKEDITOR.plugins.add( 'imageuploader', {
    init: function( editor ) {
        editor.config.filebrowserBrowseUrl = '/wp-content/themes/Padmatheme/library/visual-editor/scripts-src/deps/ckeditor/plugins/imageuploader/imgbrowser.php';
    }
});

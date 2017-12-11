<?php
function blox_content_block_editor_style() {
	
	$body_bg = BloxElementsData::get_property('block-content-entry-container', 'background-color', 'ffffff');	
	$body_color = BloxElementsData::get_property('block-content-entry-content', 'color', '333333');
	$body_font_family = BloxElementsData::get_property('block-content-entry-content', 'font-family', 'helvetica, sans-serif');
	$body_font_size = BloxElementsData::get_property('block-content-entry-content', 'font-size', '13');
	$body_line_height = BloxElementsData::get_property('block-content-entry-content', 'line-height', '180');

	$body_headings_size = BloxElementsData::get_property('block-content-heading', 'font-size', '20');
	$body_sub_headings_size = BloxElementsData::get_property('block-content-sub-heading', 'font-size', '16');

	if ( !($body_hyperlink_color = BloxElementsData::get_property('block-content-entry-content-hyperlinks', 'color', null)) )
		$body_hyperlink_color = $body_color;

	return '
		* {
			font-size: ' . $body_font_size . 'px;
			font-family: ' . $body_font_family . ';
			font-style: inherit;
			font-weight: inherit;
			line-height: ' . $body_line_height . '%;
			color: inherit;
		}
		body {
			background: #' . $body_bg . ';
			color: #' . $body_color . ';
			font-size: ' . $body_font_size . 'px;
			font-family: ' . $body_font_family . ';
			line-height: ' . $body_line_height . '%;
		}

		/* Headings */
		h1,h2,h3,h4,h5,h6 {
			clear: both;
		}

		h1,
		h2 {
			color: #000;
			font-weight: bold;
			margin: 0 0 20px;
			font-size: ' . $body_headings_size . 'px;
		}

		h3, h4, h5, h6 {
			margin: 0 0 15px;
			font-size: ' . $body_sub_headings_size . 'px;
		}

		hr {
			background-color: #ccc;
			border: 0;
			height: 1px;
			margin: 0 0 15px;
		}

		/* Text elements */
		p {
			margin: 0 0 15px;
		}
		
		/* Lists */
		ul, ol {
			padding: 0 0 0 40px;
			margin: 15px 0;
		}
		
		ul ul, ol ol { margin: 0; } /* Lists inside lists should not have the margin on them. */	

	    ul li { list-style: disc; }
	    ul ul li { list-style: circle; }
	    ul ul ul li { list-style: square; }
	    
	    ol li { list-style: decimal; }
	    ol ol li { list-style: lower-alpha; }
	    ol ol ol li { list-style: lower-roman; }
		
		strong {
			font-weight: bold;
		}
		cite, em, i {
			font-style: italic;
		}
		cite {
			border: none;
		}
		pre {
			background: #f4f4f4;
			font: 13px "Courier 10 Pitch", Courier, monospace;
			line-height: 1.5;
			margin-bottom: 1.625em;
			padding: 0.75em 1.625em;
		}
		code {
			font: 13px Monaco, Consolas, "Andale Mono", "DejaVu Sans Mono", monospace;
		}
		abbr, acronym {
			border-bottom: 1px dotted #666;
			cursor: help;
		}

		/* Links */
		a,
		a em,
		a strong {
			color: #' . $body_hyperlink_color . ';
			text-decoration: underline;
			cursor: pointer;
		}
		a:focus,
		a:active,
		a:hover {
			text-decoration: none;
		}

		/* Alignment */
		.alignleft {
			display: inline;
			float: left;
			margin-right: 1.625em;
		}
		.alignright {
			display: inline;
			float: right;
			margin-left: 1.625em;
		}
		.aligncenter {
			clear: both;
			display: block;
			margin-left: auto;
			margin-right: auto;
		}


		.alert {
			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
			border-radius: 3px;
			box-shadow: 0 1px 0 #fff inset;
			padding: 5px 20px;
			margin: 20px 0;
			display: block;
		}

			.alert p {
				margin: 10px 0;
				line-height: 160%;
			}

		.alert-green {
			border: 1px solid #97B48A;
			background-color: #CBECA0;
		}

		.alert-red {
			border: 1px solid #CFADB3;
			color: #832525;
			background-color: #FAF2F5;
		}

		.alert-yellow {
			border: 1px solid #E6DB55;
			background-color: #FFFBCC;
			color: #424242;
		}

		.alert-gray, .alert-grey {
			border: 1px solid #CCC;
			color: #424242;
			background-color: #EEE;
		}

		.alert-blue {
			border: 1px solid #92CAE4;
			color: #205791;
			background-color: #D5EDF8;
		}

		.alert a {
			color: inherit;
		}
		';
	
}
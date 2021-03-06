<?php
add_action( 'edit_form_after_editor', 'blox_meta_padma_save_post_template_bypass' );
function blox_meta_padma_save_post_template_bypass() {
	return padma_meta_padma_save_post_template_bypass();
}
function blox_register_admin_meta_box( $class ) {
	return padma_register_admin_meta_box( $class );
}
function blox_register_block( $class, $block_type_url = false ) {
	return padma_register_block( $class, $block_type_url );
}
function blox_register_visual_editor_box( $class ) {
	return padma_register_visual_editor_box( $class );
}
function blox_register_visual_editor_box_callback( $class ) {
	return padma_register_visual_editor_box_callback( $class );
}
function blox_register_visual_editor_panel( $class ) {
	return padma_register_visual_editor_panel( $class );
}
function blox_register_visual_editor_panel_callback( $class ) {
	return padma_register_visual_editor_panel_callback( $class );
}
function blox_maybe_unserialize( $string ) {
	return padma_maybe_unserialize( $string );
}
function blox_maybe_serialize( $data ) {
	return padma_maybe_serialize( $data );
}
function blox_url() {
	return padma_url();
}
function blox_cache_url() {
	return padma_cache_url();
}
function blox_get( $name, $array = false, $default = null, $fix_data_type = false ) {
	return padma_get( $name, $array, $default, $fix_data_type );
}
function blox_post( $name, $default = null ) {
	return padma_post( $name, $default );
}
function blox_format_url_ssl( $url ) {
	return padma_format_url_ssl( $url );
}
function blox_get_current_url() {
	return padma_get_current_url();
}
function blox_change_to_unix_path( $path ) {
	return padma_change_to_unix_path( $path );
}
function blox_fix_data_type( $data ) {
	return padma_fix_data_type( $data );
}
function blox_thumbnail() {
	return padma_thumbnail();
}
function blox_resize_image( $url, $width = null, $height = null, $crop = true, $single = true, $upscale = true  ) {
	return padma_resize_image( $url, $width, $height, $crop, $single, $upscale );
}
function blox_is_ie( $version_check = false ) {
	return padma_is_ie( $version_check );
}
function blox_parse_php( $content ) {
	return padma_parse_php( $content );
}
function blox_in_numeric_range( $check, $begin, $end, $allow_equals = true ) {
	return padma_in_numeric_range( $check, $begin, $end, $allow_equals );
}
function blox_remove_from_array( array &$array, $value ) {
	return padma_remove_from_array( $array, $value );
}
function blox_array_insert( array &$array, array $insert, $position ) {
	return padma_array_insert( $array, $insert, $position );
}
function blox_array_key_neighbors( $array, $findKey, $valueOnly = true ) {
	return padma_array_key_neighbors( $array, $findKey, $valueOnly );
}
function blox_array_map_recursive(  $callback, $array  ) {
	return padma_array_map_recursive(  $callback, $array  );
}
function blox_array_merge_recursive_simple() {
	return padma_array_merge_recursive_simple();
}
function blox_array_merge_recursive_simple_recurse( $array, $array1 ) {
	return padma_array_merge_recursive_simple_recurse( $array, $array1 );
}
function blox_format_color( $color, $pound_sign = true ) {
	return padma_format_color( $color, $pound_sign );
}
function blox_get_browser() {
	return padma_get_browser();
}
function blox_str_replace_json( $search, $replace, $subject ) {
	return padma_str_replace_json( $search, $replace, $subject );
}
function blox_preg_replace_json( $pattern, $replace, $subject ) {
	return padma_preg_replace_json( $pattern, $replace, $subject );
}
function blox_get_search_form( $placeholder = null ) {
	return padma_get_search_form( $placeholder );
}
function blox_human_bytes( $size ) {
	return padma_human_bytes( $size );
}
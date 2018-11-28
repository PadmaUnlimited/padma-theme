<?php

trait HeadwayAdminMetaBoxAPITrait {
	public function register(){
		parent::register();
	}
	public function init(){
		parent::init();
	}
	public function box(){
		parent::box();
	}
	public function save($post_ID){
		parent::save($post_ID);
	}
	protected function modify_arguments($post = false){
		parent::modify_arguments($post);
	}
	protected function input_text($input){
		parent::input_text($input);
	}
	protected function input_textarea($input){
		parent::input_textarea($input);
	}
	protected function input_checkbox($input){
		parent::input_checkbox($input);
	}
	protected function input_select($input){
		parent::input_select($input);
	}
	protected function input_radio($input){
		parent::input_radio($input);
	}
	protected function input_pages($input){
		parent::input_pages($input);
	}
}
abstract class HeadwayAdminMetaBoxAPI extends PadmaAdminMetaBoxAPI {
	use HeadwayAdminMetaBoxAPITrait;
}


trait HeadwayBlockAPITrait {
	public function register(){
		parent::register();
	}
	public function setup_main_block_element(){
		parent::setup_main_block_element();
	}
	public function options_panel($block, $layout){
		parent::options_panel($block, $layout);
	}
	public function content($block){
		parent::content($block);
	}
	public function title_and_subtitle($block){
		parent::title_and_subtitle($block);
	}
	public function register_block_element($args){
		parent::register_block_element($args);
	}
	public static function get_setting($block, $setting, $default = null){
		parent::get_setting($block, $setting, $default);
	}
}
abstract class HeadwayBlockAPI extends PadmaBlockAPI {
	use HeadwayBlockAPITrait;
}

trait HeadwayVisualEditorPanelAPITrait {
	public function register(){
		parent::register();
	}
	public function modify_arguments($args){
		parent::modify_arguments($args);
	}
	public function parse_function_args($array){
		parent::parse_function_args($array);
	}
	public function panel_link(){
		parent::panel_link();
	}
	public function build_panel($id){
		parent::build_panel($id);
	}
	public function panel_content($args = false){

	}
	public function sub_tab_content($id, $name = false){
		parent::sub_tab_content($id, $name);
	}
	public function create_inputs($tab){
		parent::create_inputs($tab);
	}
	public function render_input($input){
		parent::render_input($input);
	}
	public function repeater($input){
		parent::repeater($input);
	}
	public function repeater_group($input, $group_index = null, $counter = null) {
		parent::repeater_group($input, $group_index, $counter = null);
	}
	public function input_checkbox($input){
		parent::input_checkbox($input);
	}
	public function input_text($input){
		parent::input_text($input);
	}
	public function input_textarea($input){
		parent::input_textarea($input);
	}
	public function input_code($input){
		parent::input_code($input);
	}
	public function input_wysiwyg($input){
		parent::input_wysiwyg($input);
	}
	public function input_integer($input){
		parent::input_integer($input);
	}
	public function input_select($input){
		parent::input_select($input);
	}
	private static function input_select_output_option($value, $text, $input_value){
		parent::input_select_output_option($value, $text, $input_value);
	}
	public function input_multi_select($input){
		parent::input_multi_select($input);
	}
	public function input_colorpicker($input){
		parent::input_colorpicker($input);
	}
	public function input_image($input){
		parent::input_image($input);
	}
	public function input_slider($input){
		parent::input_slider($input);
	}
	public function input_heading($input){
		parent::input_heading($input);
	}
	public function input_notice($input){
		parent::input_notice($input);
	}
	public function input_raw_html($input){
		parent::input_raw_html($input);
	}
	public function input_button($input){
		parent::input_button($input);
	}
	public function input_import_file($input){
		parent::input_import_file($input);
	}

}
abstract class HeadwayVisualEditorPanelAPI extends PadmaVisualEditorPanelAPI {
	use HeadwayVisualEditorPanelAPITrait;
}
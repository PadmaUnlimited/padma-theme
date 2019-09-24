<?php

class PadmaAdminBar {
	
	
	public static function init() {
		
		add_action('admin_bar_menu', array(__CLASS__, 'add_admin_bar_nodes'), 75);
		
	}
	
	
	public static function remove_admin_bar() {

		show_admin_bar(false);
		remove_action('wp_head', '_admin_bar_bump_cb');
				
	}
	
	
	public static function add_admin_bar_nodes() {
		
		if ( !PadmaCapabilities::can_user_visually_edit() )
			return;
		
		global $wp_admin_bar;
			
		$default_visual_editor_mode = current_theme_supports('padma-grid') ? 'grid' : 'design';


		//Padma Root
		$wp_admin_bar->add_menu(array(
			'id' 		=> 'padma', 
			'title' 	=> PadmaSettings::get('menu-name'), 
			'href' 		=> 	add_query_arg(array(
											'visual-editor' 		=> 'true',
											'visual-editor-mode' 	=> $default_visual_editor_mode,
											've-layout' 			=> urlencode(PadmaLayout::get_current())), 
										home_url())
		));
		

		//Visual Editor
		$wp_admin_bar->add_menu(array(
			'parent' 	=> 'padma',
			'id' 		=> 'padma-ve', 
			'title' 	=> __('Visual Editor','padma'),  
			'href' 		=>  add_query_arg(array(
											'visual-editor' 		=> 'true',
											'visual-editor-mode' 	=> $default_visual_editor_mode,
											've-layout' 			=> urlencode( PadmaLayout::get_current() )),
											home_url())
		));
			

		//Grid
		if ( current_theme_supports('padma-grid') ) {

			$wp_admin_bar->add_menu(array(
				'parent' 	=> 'padma-ve',
				'id' 		=> 'padma-ve-grid', 
				'title' 	=> __('Grid','padma'),  
				'href' 		=>  add_query_arg(array(
												'visual-editor' 		=> 'true',
												'visual-editor-mode' 	=> 'grid',
												've-layout' 			=> urlencode( PadmaLayout::get_current() )),
												 home_url())
			));

		}
			
		
		//Design Editor
		$wp_admin_bar->add_menu(array(
			'parent' 	=> 'padma-ve',
			'id' 		=> 'padma-ve-design', 
			'title' 	=> __('Design','padma'), 
			'href' 		=> add_query_arg(array(
											'visual-editor' 		=> 'true',
											'visual-editor-mode' 	=>
											'design', 've-layout' 	=> urlencode( PadmaLayout::get_current() )),
											 home_url())
		));

		//Templates
		$wp_admin_bar->add_menu(array(
			'parent' 	=> 'padma',
			'id' 		=> 'padma-admin-templates',
			'title' 	=> __('Templates','padma'),
			'href' 		=> admin_url('admin.php?page=padma-templates')
		));
			
		//Admin Options
		$wp_admin_bar->add_menu(array(
			'parent' 	=> 'padma',
			'id' 		=> 'padma-admin-options', 
			'title' 	=> __('Options','padma'),  
			'href' 		=> admin_url('admin.php?page=padma-options')
		));

					$wp_admin_bar->add_menu(array(
						'parent' => 'padma-admin-options',
						'id' => 'padma-admin-options-general', 
						'title' => __('General','padma'),  
						'href' => admin_url('admin.php?page=padma-options#tab-general')
					));
					
					$wp_admin_bar->add_menu(array(
						'parent' => 'padma-admin-options',
						'id' => 'padma-admin-options-seo', 
						'title' => __('Search Engine Optimization','padma'),  
						'href' => admin_url('admin.php?page=padma-options#tab-seo')
					));
					
					$wp_admin_bar->add_menu(array(
						'parent' => 'padma-admin-options',
						'id' => 'padma-admin-options-scripts',
						'title' => __('Scripts/Analytics','padma'),  
						'href' => admin_url('admin.php?page=padma-options#tab-scripts')
					));
					
					$wp_admin_bar->add_menu(array(
						'parent' => 'padma-admin-options',
						'id' => 'padma-admin-options-visual-editor',
						'title' => __('Visual Editor','padma'),  
						'href' => admin_url('admin.php?page=padma-options#tab-visual-editor')
					));
					
					$wp_admin_bar->add_menu(array(
						'parent' => 'padma-admin-options',
						'id' => 'padma-admin-options-advanced',
						'title' => __('Advanced','padma'),  
						'href' => admin_url('admin.php?page=padma-options#tab-advanced')
					));
					
			//Admin Tools
				$wp_admin_bar->add_menu(array(
					'parent' => 'padma',
					'id' => 'padma-admin-tools', 
					'title' => __('Tools','padma'),  
					'href' => admin_url('admin.php?page=padma-tools')
				));

					$wp_admin_bar->add_menu(array(
						'parent' => 'padma-admin-tools',
						'id' => 'padma-admin-tools-system-info', 
						'title' => __('System Info','padma'),  
						'href' => admin_url('admin.php?page=padma-tools#tab-system-info')
					));

					if(is_admin()){
						$wp_admin_bar->add_menu(array(
							'parent' => 'padma-admin-tools',
							'id' => 'padma-admin-tools-clear-cache', 
							'title' => __('Clear cache','padma'),  
							'href' => 'javascript:;'
						));						
					}
					
					$wp_admin_bar->add_menu(array(
						'parent' => 'padma-admin-tools',
						'id' => 'padma-admin-tools-reset', 
						'title' => __('Reset','padma'),  
						'href' => admin_url('admin.php?page=padma-tools#tab-reset')
					));
					
	}
	
	
}
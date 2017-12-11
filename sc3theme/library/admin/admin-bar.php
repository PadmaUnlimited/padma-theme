<?php
class BloxAdminBar {
	
	
	public static function init() {
		
		add_action('admin_bar_menu', array(__CLASS__, 'add_admin_bar_nodes'), 75);
		
	}
	
	
	public static function remove_admin_bar() {

		show_admin_bar(false);
		remove_action('wp_head', '_admin_bar_bump_cb');
				
	}
	
	
	public static function add_admin_bar_nodes() {
		
		if ( !BloxCapabilities::can_user_visually_edit() )
			return;
		
		global $wp_admin_bar;
			
		$default_visual_editor_mode = current_theme_supports('blox-grid') ? 'grid' : 'design';
						
		//Blox Root
		$wp_admin_bar->add_menu(array(
			'id' => 'blox', 
			'title' => 'Blox Theme', 
			'href' => add_query_arg(array('visual-editor' => 'true', 'visual-editor-mode' => $default_visual_editor_mode, 've-layout' => urlencode(BloxLayout::get_current())), home_url())
		));
		
			//Visual Editor
				$wp_admin_bar->add_menu(array(
					'parent' => 'blox',
					'id' => 'blox-ve', 
					'title' => 'Visual Editor',  
					'href' =>  add_query_arg(array('visual-editor' => 'true', 'visual-editor-mode' => $default_visual_editor_mode, 've-layout' => urlencode( BloxLayout::get_current() )), home_url())
				));
				
					//Grid
						if ( current_theme_supports('blox-grid') ) {

							$wp_admin_bar->add_menu(array(
								'parent' => 'blox-ve',
								'id' => 'blox-ve-grid', 
								'title' => 'Grid',  
								'href' =>  add_query_arg(array('visual-editor' => 'true', 'visual-editor-mode' => 'grid', 've-layout' => urlencode( BloxLayout::get_current() )), home_url())
							));

						}
			
					//Design Editor
						$wp_admin_bar->add_menu(array(
							'parent' => 'blox-ve',
							'id' => 'blox-ve-design', 
							'title' => 'Design',  
							'href' => add_query_arg(array('visual-editor' => 'true', 'visual-editor-mode' => 'design', 've-layout' => urlencode( BloxLayout::get_current() )), home_url())
						));

			//Templates
				$wp_admin_bar->add_menu(array(
					'parent' => 'blox',
					'id' => 'blox-admin-templates',
					'title' => 'Templates',
					'href' => admin_url('admin.php?page=blox-templates')
				));
			
			//Admin Options
				$wp_admin_bar->add_menu(array(
					'parent' => 'blox',
					'id' => 'blox-admin-options', 
					'title' => 'Options',  
					'href' => admin_url('admin.php?page=blox-options')
				));

					$wp_admin_bar->add_menu(array(
						'parent' => 'blox-admin-options',
						'id' => 'blox-admin-options-general', 
						'title' => 'General',  
						'href' => admin_url('admin.php?page=blox-options#tab-general')
					));
					
					$wp_admin_bar->add_menu(array(
						'parent' => 'blox-admin-options',
						'id' => 'blox-admin-options-seo', 
						'title' => 'Search Engine Optimization',  
						'href' => admin_url('admin.php?page=blox-options#tab-seo')
					));
					
					$wp_admin_bar->add_menu(array(
						'parent' => 'blox-admin-options',
						'id' => 'blox-admin-options-scripts',
						'title' => 'Scripts/Analytics',  
						'href' => admin_url('admin.php?page=blox-options#tab-scripts')
					));
					
					$wp_admin_bar->add_menu(array(
						'parent' => 'blox-admin-options',
						'id' => 'blox-admin-options-visual-editor',
						'title' => 'Visual Editor',  
						'href' => admin_url('admin.php?page=blox-options#tab-visual-editor')
					));
					
					$wp_admin_bar->add_menu(array(
						'parent' => 'blox-admin-options',
						'id' => 'blox-admin-options-advanced',
						'title' => 'Advanced',  
						'href' => admin_url('admin.php?page=blox-options#tab-advanced')
					));
					
			//Admin Tools
				$wp_admin_bar->add_menu(array(
					'parent' => 'blox',
					'id' => 'blox-admin-tools', 
					'title' => 'Tools',  
					'href' => admin_url('admin.php?page=blox-tools')
				));

					$wp_admin_bar->add_menu(array(
						'parent' => 'blox-admin-tools',
						'id' => 'blox-admin-tools-system-info', 
						'title' => 'System Info',  
						'href' => admin_url('admin.php?page=blox-tools#tab-system-info')
					));
					
					$wp_admin_bar->add_menu(array(
						'parent' => 'blox-admin-tools',
						'id' => 'blox-admin-tools-reset', 
						'title' => 'Reset',  
						'href' => admin_url('admin.php?page=blox-tools#tab-reset')
					));
					
	}
	
	
}
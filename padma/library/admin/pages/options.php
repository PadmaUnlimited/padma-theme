<h2 class="nav-tab-wrapper big-tabs-tabs">
	<a class="nav-tab" href="#tab-general">General</a>
	<a class="nav-tab" href="#tab-seo">Search Engine Optimization</a>
	<a class="nav-tab" href="#tab-scripts">Scripts/Analytics</a>
	<a class="nav-tab" href="#tab-visual-editor">Visual Editor</a>
	<a class="nav-tab" href="#tab-advanced">Advanced</a>
	<a class="nav-tab" href="#tab-compatibility">Compatibility</a>
</h2>

<?php do_action('padma_admin_save_message'); ?>
<?php do_action('padma_admin_save_error_message'); ?>

<form method="post">
	<input type="hidden" value="<?php echo wp_create_nonce('padma-admin-nonce'); ?>" name="padma-admin-nonce" id="padma-admin-nonce" />

	<div class="big-tabs-container">

		<div class="big-tab" id="tab-general-content">

			<?php
			if ( is_main_site() ) {
			?>


				<h3 class="title title-hr">General</h3>

			<?php
			} else {

				echo '<h3 class="title">General</h3>';

			}

			$form = array(
				array(
					'id' => 'favicon',
					'size' => 'large',
					'type' => 'text',
					'label' => 'Favicon URL',
					'value' => PadmaOption::get('favicon'),
					'description' => 'A favicon is the little image that sits next to your address in the favorites menu and on tabs.  If you do not know how to save an image as an icon you can go to <a href="http://www.favicon.cc/" target="_blank">favicon.cc</a> and draw or import an image.'
				),

				array(
					'id' => 'feed-url',
					'size' => 'large',
					'type' => 'text',
					'label' => 'Feed URL',
					'description' => 'If you use any service like <a href="http://feedburner.google.com/" target="_blank">FeedBurner</a>, type the feed URL here.',
					'value' => PadmaOption::get('feed-url')
				)
			);

			PadmaAdminInputs::generate($form);
			?>

			<h3 class="title title-hr">Admin Preferences</h3>

			<?php
			$form = array(
				array(
					'id' 		=> 'menu-setup',
					'type' 		=> 'radio',
					'label' 	=> 'Default Admin Page',
					'value' 	=> PadmaOption::get('menu-setup', false, 'getting-started'),
					'radios' 	=> array(
						array(
							'value' => 'getting-started',
							'label' => 'Getting Started'
						),

						array(
							'value' => 'visual-editor',
							'label' => 'Visual Editor'
						),

						array(
							'value' => 'options',
							'label' => 'Options'
						)
					),
					'description' => 'Select which admin page you would like to be directed to when you click on "Padma" in the WordPress Admin.'
				),
				array(
					'type' 	=> 'checkbox',
					'label' => 'Do not recommend plugin installation',
					'checkboxes' => array(
						array(
							'id' 		=> 'do-not-recommend-plugin-installation',
							'label' 	=> 'Hide recommended plugin notice',
							'checked' 	=> PadmaOption::get('do-not-recommend-plugin-installation', false, false)
						)
					),
					'description' => 'If on, Padma will not recommend install "Updater" and "Services" plugin'
				)
			);

			PadmaAdminInputs::generate($form);
			?>

		</div><!-- #tab-general-content -->


		<div class="big-tab" id="tab-seo-content">

			<?php
			if ( PadmaSEO::is_disabled() ) {

				switch ( PadmaSEO::plugin_active() ) {

					case 'aioseop':
						echo '<div class="alert alert-yellow"><p>Padma has detected that you are using the All In One SEO pack plugin.  In order to reduce conflicts and save resources, Padma\'s SEO functionality has been disabled.</p></div>';
					break;

					case 'wpseo':
						echo '<div class="alert alert-yellow"><p>Padma has detected that you are using Yoast\'s WordPress SEO plugin.  In order to reduce conflicts and save resources, Padma\'s SEO functionality has been disabled.</p></div>';
					break;

					default:
						echo '<div class="alert alert-yellow"><p>Padma\'s SEO functionality is disabled.</p></div>';
						break;

				}

			} else {
			?>

				<h3 class="title" id="seo-templates-title">SEO Templates</h3>

				<div id="seo-templates">
					<div id="seo-templates-hidden-inputs">
						<?php
						/* SETUP THE TYPES OF SEO TEMPLATE INPUTS */
						$seo_template_inputs = array(
							'title',
							'description',
							'noindex',
							'nofollow',
							'noarchive',
							'nosnippet',
							'noodp',
							'noydir'
						);

						/* GENERATE HIDDEN INPUTS */
						$seo_options = PadmaOption::get('seo-templates', 'general', array());

						foreach (PadmaSEO::output_layouts_and_defaults() as $page => $defaults) {

							foreach ($seo_template_inputs as $input) {

								$name_attr = 'name="padma-admin-input[seo-templates][' . $page . '][' . $input . ']"';

								$default = isset($defaults[$input]) ? $defaults[$input] : null;

								$page_options = padma_get($page, $seo_options, array());
								$value = padma_get($input, $page_options, $default);

								echo '<input type="hidden" id="seo-' . $page . '-' . $input . '"' . $name_attr . ' value="' . stripslashes(esc_attr($value)) . '" />';

							}

						}
						?>
					</div>

					<div id="seo-templates-header">
						<span>Select a Template:</span>
						<select>
							<option value="index">Blog Index</option>

							<?php
							if ( get_option('show_on_front') == 'page' )
								echo '<option value="front_page">Front Page</option>';
							?>

							<optgroup label="Single">
								<?php
								$post_types = get_post_types(array('public' => true), 'objects');

								foreach($post_types as $post_type)
									echo '<option value="single-' . $post_type->name . '">' . $post_type->label . '</option>';
								?>
							</optgroup>

							<optgroup label="Archive">
								<option value="archive-category">Category</option>
								<option value="archive-search">Search</option>
								<option value="archive-date">Date</option>
								<option value="archive-author">Author</option>
								<option value="archive-post_tag">Post Tag</option>
								<option value="archive-post_type">Post Type</option>
								<option value="archive-taxonomy">Taxonomy</option>
							</optgroup>

							<option value="four04">404</option>

						</select>
					</div><!-- #seo-templates-header -->

					<div id="seo-templates-inputs">

						<?php
						$form = array(
							array(
								'id' => 'title',
								'type' => 'text',
								'size' => 'large',
								'label' => 'Title',
								'description' => 'The title is the main text that describes the page. It is the single most important on-page SEO element (behind overall content).  The title appears at the top of the web browser when viewing the page, in browser tabs, search engine results, and external websites.  <strong>Tip:</strong> it is best that the title stays below 70 characters.<br /><br /><a href="http://www.seomoz.org/learn-seo/title-tag" target="_blank">Learn more about Titles &raquo;</a>',
								'no-submit' => true
							),

							array(
								'id' => 'description',
								'type' => 'paragraph',
								'cols' => 60,
								'rows' => 3,
								'label' => '<code>&lt;meta&gt;</code> Description',
								'description' => 'Meta description tags, while not important to search engine rankings, are extremely important in gaining user click-through from search engine result pages (SERPs). These short paragraphs are your opportunity to advertise content to searchers and let them know exactly what the given page has with regard to what they’re looking for. <strong>Tip:</strong> a good description is around 150 characters.<br /><br /><a href="http://www.seomoz.org/learn-seo/meta-description" target="_blank">Learn more about &lt;meta&gt; Descriptions &raquo;</a>',
								'no-submit' => true
							)
						);

						PadmaAdminInputs::generate($form);
						?>

						<div class="hr"></div>

						<p><strong>You may use the following variables in the Title and Description inputs above:</strong></p>

						<ul>
							<li><code>%title%</code> &mdash; Will retrieve the title of whatever post, archive, or page is being displayed.</li>
							<li><code>%sitename%</code> &mdash; Will retrieve the name of the site.  This can be set in <a href="<?php echo admin_url('options-general.php'); ?>" target="_blank">Settings &raquo; General</a>.</li>
							<li><code>%tagline%</code> &mdash; Will retrieve the tagline/description of the site.  This can be set in <a href="<?php echo admin_url('options-general.php'); ?>" target="_blank">Settings &raquo; General</a>.</li>
							<li><code>%meta%</code> &mdash; Used only on taxonomy archives to display the term name.</li>
						</ul>

						<h3 id="seo-templates-advanced-options-title" class="title title-hr">Advanced Options <span>Show &darr;</span></h3>

						<div id="seo-templates-advanced-options">
							<?php
							$form = array(
								array(
									'type' => 'checkbox',
									'label' => 'Page Indexing',
									'checkboxes' => array(
										array(
											'id' => 'noindex',
											'label' => 'Enable <code>noindex</code>',
											'no-submit' => true
										)
									),
									'description' => 'Index/NoIndex tells the engines whether the page should be crawled and kept in the engines\' index for retrieval. If you check this box to opt for <code>noindex</code>, the page will be excluded from the engines.  <strong>Note:</strong> if you\'re not sure what this does, do not check this box.'
								),

								array(
									'type' => 'checkbox',
									'label' => 'Link Following',
									'checkboxes' => array(
										array(
											'id' => 'nofollow',
											'label' => 'Enable <code>nofollow</code>',
											'no-submit' => true
										)
									),
									'description' => 'Follow/NoFollow tells the engines whether links on the page should be crawled. If you check this box to employ "nofollow," the engines will disregard the links on the page both for discovery and ranking purposes.  <strong>Note:</strong> if you\'re not sure what this does, do not check this box.'
								),

								array(
									'type' => 'checkbox',
									'label' => 'Page Archiving',
									'checkboxes' => array(
										array(
											'id' => 'noarchive',
											'label' => 'Enable <code>noarchive</code>',
											'no-submit' => true
										)
									),
									'description' => 'Noarchive is used to restrict search engines from saving a cached copy of the page. By default, the engines will maintain visible copies of all pages they indexed, accessible to searchers through the "cached" link in the search results.  Check this box to restrict search engines from storing cached copies of this page.'
								),

								array(
									'type' => 'checkbox',
									'label' => 'Effects',
									'checkboxes' => array(
										array(
											'id' => 'nosnippet',
											'label' => 'Enable <code>nosnippet</code>',
											'no-submit' => true
										)
									),
									'description' => 'Nosnippet informs the engines that they should refrain from displaying a descriptive block of text next to the page\'s title and URL in the search results.'
								),

								array(
									'type' => 'checkbox',
									'label' => 'Open Directory Project',
									'checkboxes' => array(
										array(
											'id' => 'noodp',
											'label' => 'Enable <code>NoODP</code>',
											'no-submit' => true
										)
									),
									'description' => 'NoODP is a specialized tag telling the engines not to grab a descriptive snippet about a page from the Open Directory Project (DMOZ) for display in the search results.'
								),

								array(
									'type' => 'checkbox',
									'label' => 'Yahoo! Directory',
									'checkboxes' => array(
										array(
											'id' => 'noydir',
											'label' => 'Enable <code>NoYDir</code>',
											'no-submit' => true
										)
									),
									'description' => 'NoYDir, like NoODP, is specific to Yahoo!, informing that engine not to use the Yahoo! Directory description of a page/site in the search results.'
								)
							);

							PadmaAdminInputs::generate($form);
							?>
						</div><!-- #seo-templates-advanced-options -->

					</div><!-- #seo-templates-inputs -->
				</div><!-- #seo-templates-content -->

				<div id="seo-description" class="alert alert-yellow"><p>Unfamiliar with <em>Search Engine Optimization</em>?  <a href="http://www.seomoz.org/beginners-guide-to-seo/" target="_blank">Learn More &raquo;</a></p></div>

				<h3 class="title title-hr">Content <code>nofollow</code> Links</h3>

				<?php
				$form = array(
					array(
						'type' => 'checkbox',
						'label' => 'Comment Authors\' URL',
						'checkboxes' => array(
							array(
								'id' => 'nofollow-comment-author-url',
								'label' => 'Add nofollow To Comment Authors\' URL',
								'checked' => PadmaOption::get('nofollow-comment-author-url', 'general', false)
							)
						),
						'description' => 'Adding nofollow to the comment authors\' URLs will tell search engines to not visit their website and to stay on yours. Many bloggers frown upon this, which can sometimes discourage comments. Only enable this if you are 100% sure you know you want to.'
					)
				);

				PadmaAdminInputs::generate($form);
				?>

			<?php
			}
			?>

		</div><!-- #tab-seo -->


		<div class="big-tab" id="tab-scripts-content">

			<?php
			$form = array(
				array(
					'id' => 'header-scripts',
					'type' => 'paragraph',
					'cols' => 90,
					'rows' => 8,
					'label' => 'Header Scripts',
					'description' => 'Anything here will go in the <code>&lt;head&gt;</code> of the website. If you are using <a href="http://google.com/analytics" target="_blank">Google Analytics</a>, paste the code provided here. <strong>Do not place plain text in this!</strong>',
					'allow-tabbing' => true,
					'value' => PadmaOption::get('header-scripts')
				),

				array(
					'id' => 'footer-scripts',
					'type' => 'paragraph',
					'cols' => 90,
					'rows' => 8,
					'label' => 'Footer Scripts',
					'description' => 'Anything here will be inserted before the <code>&lt;/body&gt;</code> tag of the website. <strong>Do not place plain text in this!</strong>',
					'allow-tabbing' => true,
					'value' => PadmaOption::get('footer-scripts')
				)
			);

			PadmaAdminInputs::generate($form);
			?>

		</div><!-- #tab-scripts-content -->


		<div class="big-tab" id="tab-visual-editor-content">

			<?php
			$form = array(
				array(
					'type' => 'checkbox',
					'label' => 'Tooltips',
					'checkboxes' => array(
						array(
							'id' => 'disable-visual-editor-tooltips',
							'label' => 'Disable Tooltips in the Visual Editor',
							'checked' => PadmaOption::get('disable-visual-editor-tooltips', false, false)
						)
					),
					'description' => 'If you ever feel that the tooltips are too invasive in the visual editor, you can disable them here.  Tooltips are the black speech bubbles that appear to assist you when you are not sure what an option is or how it works.'
				)
			);

			PadmaAdminInputs::generate($form);
			?>

		</div>


		<div class="big-tab" id="tab-advanced-content">

			<h3 class="title">Automatic Updates</h3>
			<?php
			$form = array(
				array(
					'type' => 'checkbox',
					'label' => 'Disable Automatic Core Updates',
					'checkboxes' => array(
						array(
							'id' => 'disable-automatic-core-updates',
							'label' => 'Disable Automatic Core Updates',
							'checked' => PadmaOption::get('disable-automatic-core-updates', false, false)
						)
					),
					'description' => 'By default, Padma will attempt to update automatically, but if this option is checked automatic updates will not happen.'
				),
				array(
					'type' => 'checkbox',
					'label' => 'Disable Automatic Plugin Updates',
					'checkboxes' => array(
						array(
							'id' => 'disable-automatic-plugin-updates',
							'label' => 'Disable Automatic Plugin Updates',
							'checked' => PadmaOption::get('disable-automatic-plugin-updates', false, false)
						)
					),
					'description' => 'By default, Updater plugin will attempt to update Padma Plugins automatically, but if this option is checked automatic updates for plugins will not happen.'
				),
			);

			PadmaAdminInputs::generate($form);
			?>



			<h3 class="title">Caching &amp; Compression</h3>

			<?php
			$form = array(
				array(
					'type' => 'checkbox',
					'label' => 'Asset Caching',
					'checkboxes' => array(
						array(
							'id' => 'disable-caching',
							'label' => 'Disable Padma Caching',
							'checked' => PadmaOption::get('disable-caching', false, false)
						)
					),
					'description' => 'By default, Padma will attempt to cache all CSS and JavaScript that it generates.  However, there may be rare circumstances where disabling the cache will help with certain issues.<br /><br /><em><strong>Important:</strong> Disabling the Padma cache will cause an <strong>increase in page load times</strong> and <strong>increase the strain your web server</strong> will undergo on every page load.'
				),

				array(
					'type' => 'checkbox',
					'label' => 'Dependency Query Variables',
					'checkboxes' => array(
						array(
							'id' => 'remove-dependency-query-vars',
							'label' => 'Remove Query Variables from Dependency URLs',
							'checked' => PadmaOption::get('remove-dependency-query-vars', false, false)
						)
					),
					'description' => 'To leverage browser caching, Padma can tell WordPress to not put query variables on static assets such as CSS and JavaScript files.'
				)
			);

			PadmaAdminInputs::generate($form);
			?>

			<h3 class="title title-hr">Admin</h3>

			<?php
			$form = array(
				array(
					'type' => 'checkbox',
					'label' => 'Version Number',
					'checkboxes' => array(
						array(
							'id' => 'hide-menu-version-number',
							'label' => 'Hide Padma Version Number From Menu',
							'checked' => PadmaOption::get('hide-menu-version-number', false, true)
						)
					),
					'description' => 'Check this if you wish to have the Menu say "Padma" instead of "Padma ' .PADMA_VERSION . '"'
				),

				array(
					'type' => 'checkbox',
					'label' => 'Editor Style',
					'checkboxes' => array(
						array(
							'id' => 'disable-editor-style',
							'label' => 'Disable Editor Style',
							'checked' => PadmaOption::get('disable-editor-style', false, false)
						)
					),
					'description' => 'By default, Padma will take any settings in the Design Editor and add them to <a href="http://codex.wordpress.org/TinyMCE" target="_blank">WordPress\' TinyMCE editor</a> style.  Use this option to prevent that.'
				)
			);

			PadmaAdminInputs::generate($form);
			?>

			<h3 class="title title-hr">Debugging</h3>

			<div class="alert alert-red"><p>The following option should only be checked if a member of the Padma Themes team asks you to do so.</p></div>

			<?php
			$form = array(
				array(
					'type' => 'checkbox',
					'label' => 'Debug Mode',
					'checkboxes' => array(
						array(
							'id' => 'debug-mode',
							'label' => 'Enable Debug Mode',
							'checked' => PadmaOption::get('debug-mode', false, false)
						)
					),
					'description' => 'Having Debug Mode enabled will allow the Padma Themes team to access the Visual Editor for support purposes, but <strong>will not allow changes to be saved<strong>.'
				)
			);

			PadmaAdminInputs::generate($form);
			?>

		</div>

		<div class="big-tab" id="tab-compatibility-content">

			<h3 class="title">Headway</h3>

			<?php
			$form = array(
				array(
					'type' 	=> 'checkbox',
					'label' => 'Headway support',
					'checkboxes' => array(
						array(
							'id' 		=> 'headway-support',
							'label' 	=> 'Enable Headway classes support',
							'checked' 	=> PadmaOption::get('headway-support', false, false)
						)
					),
					'description' => 'If on, Padma will attempt support all PHP classes related to Headway. This allows to you use blocks like Headway Rocket and similar. <strong>Important:</strong> This feature is Experimental.'
				)
			);

			PadmaAdminInputs::generate($form);
			?>

		</div>


	</div>

	<div class="hr hr-submit" style="display: none;"></div>

	<p class="submit" style="display: none;">
		<input type="submit" name="padma-submit" value="Save Changes" class="button-primary padma-save" />
	</p>

</form>

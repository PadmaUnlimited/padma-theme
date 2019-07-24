<div id="content-selector-select">
	<div id="content-selector-select-content">
		<span id="currently-editing">Content to show:</span><span id="current-content"><?php echo get_the_title(); ?></span>
		<span id="content-selector-select-content-arrow"></span>
	</div>

	<div id="content-selector">

		<div id="content-selector-pages-container">
			<script type="text/html" id="content-page-template">
				<li class="content-item" data-bind="css: {
					'has-children': children().length || ajaxChildren,
					'has-ajax-children': ajaxChildren,
					'content-item-customized': customized,
					'content-item-template-used': id == template,					
				}">
					<span class="content-has-customized-children tooltip" title="This content has customized children.">&bull;</span>

					<span class="content content-page" data-bind="attr: {'data-content-id': id, 'data-layout-url': url}, css: {'content-open': $('#content-selector-pages-search-results').is(':visible')}">
						<strong data-bind="html: name"></strong>

						<span class="status status-post-status" data-bind="visible: postStatus, text: '(' + postStatus + ')'"></span>

						<span class="status status-template" data-bind="attr: {'data-template-id': template},text: templateName;"></span>
						<span class="show-this button button-blue content-selector-button">Show this</span>
					</span>

					<ul data-bind="template: {name: 'content-page-template', foreach: children()}"></ul>

					<span class="load-more-contents button content-selector-button" data-bind="visible: typeof ajaxShowMore != 'undefined' && ajaxShowMore()">Load More...</span>
				</li>
			</script>

			<div id="content-selector-pages" class="content-selector-content">
				<div class="cog-container" data-bind="visible: searching()">
					<div class="cog-bottom-left"></div>
					<div class="cog-top-right"></div>
				</div>

				<ul data-bind="visible: search().length && !searching(), template: {name: 'content-page-template', foreach:search()}" id="content-selector-pages-search-results"></ul>
				<ul data-bind="visible: !search().length && !searching(), template: {name: 'content-page-template', foreach:pages()}" id="content-selector-pages-content"></ul>
			</div><!-- div#content-selector-pages -->

			<div id="content-search-input-container" class="content-selector-bottom-input">
				<form>
					<input type="search" placeholder="Type to Search..." value="" id="content-search-input" pattern=".{3,}" class="allow-enter-key" title="Your search must be 3 characters or longer." />
					<span class="button" id="content-search-submit">Search</span>
				</form>
			</div>
		</div><!-- #content-selector-pages -->
	</div>
</div>
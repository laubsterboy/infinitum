<?php
/**
 * Title: Query List
 * Slug: infinitum/query-list
 * Block Types: core/query
 * Categories: posts, infinitum
 * Keywords: query, list
 * Source: theme
 * Viewport Width: 400
 */
?>
<!-- wp:query {"query":{"perPage":"12","pages":0,"offset":"1","postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"displayLayout":{"type":"flex","columns":1},"layout":{"type":"constrained"}} -->
<div class="wp-block-query">
	<!-- wp:post-template -->
	<!-- wp:columns -->
	<div class="wp-block-columns">
		<!-- wp:column {"width":"33.3333%"} -->
		<div class="wp-block-column" style="flex-basis:33.3333%">
		<!-- wp:post-featured-image {"isLink":true} /-->
		</div>
		<!-- /wp:column -->
		
		<!-- wp:column {"verticalAlignment":"center","width":"66.6666%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:66.6666%">
			<!-- wp:group -->
			<div class="wp-block-group">
				<!-- wp:pattern {"slug":"infinitum/post-summary"} /-->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
	<!-- /wp:post-template -->

	<!-- wp:query-no-results -->
		<!-- wp:paragraph {"placeholder":"Add text or blocks that will display when a query returns no results."} -->
		<p>Nothing Found</p>
		<!-- /wp:paragraph -->
	<!-- /wp:query-no-results -->

	<!-- wp:query-pagination {"paginationArrow":"arrow","layout":{"type":"flex","justifyContent":"space-between"}} -->
		<!-- wp:query-pagination-previous {"label":"Newer Posts"} /-->
		<!-- wp:query-pagination-next {"label":"Older Posts"} /-->
	<!-- /wp:query-pagination -->
</div>
<!-- /wp:query -->
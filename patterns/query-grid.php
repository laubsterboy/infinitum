<?php
/**
 * Title: Query Grid
 * Slug: infinitum/query-grid
 * Block Types: core/query
 * Categories: posts infinitum
 * Keywords: query grid
 * Source: theme
 */
?>
<!-- wp:query {"query":{"perPage":"12","pages":0,"offset":"1","postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"displayLayout":{"type":"flex","columns":3},"layout":{"type":"constrained"}} -->
<div class="wp-block-query">
	<!-- wp:post-template -->
		<!-- wp:post-featured-image {"isLink":true} /-->
		<!-- wp:pattern {"slug":"infinitum/post-summary"} /-->
		<!-- wp:spacer {"height":"var(--wp--preset--spacing--1)"} -->
		<div style="height:var(--wp--preset--spacing--1)" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->
	<!-- /wp:post-template -->

	<!-- wp:query-pagination {"paginationArrow":"arrow","layout":{"type":"flex","justifyContent":"space-between"}} -->
		<!-- wp:query-pagination-previous {"label":"Newer Posts"} /-->
		<!-- wp:query-pagination-next {"label":"Older Posts"} /-->
	<!-- /wp:query-pagination -->
</div>
<!-- /wp:query -->
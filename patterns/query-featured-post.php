<?php
/**
 * Title: Query Featured Post
 * Slug: infinitum/query-featured-post
 * Block Types: core/query
 * Categories: posts, infinitum
 * Keywords: Query, featured, post
 * Source: theme
 */
?>
<!-- wp:query {"query":{"perPage":"1","pages":0,"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude"}, "align":"full", "layout":{"type":"constrained"}} -->
<div class="wp-block-query alignnone">
	<!-- wp:post-template -->
	<?php // TODO: conditionally display the column layout based on if there is a featured image or not ?>
	<!-- wp:columns -->
	<div class="wp-block-columns">
		<!-- wp:column {"width":"60%"} -->
		<div class="wp-block-column" style="flex-basis:60%">
		<!-- wp:post-featured-image {"isLink":true} /-->
		</div>
		<!-- /wp:column -->
		
		<!-- wp:column {"verticalAlignment":"center","width":"40%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:40%">
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
</div>
<!-- /wp:query -->
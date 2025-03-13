<?php
/**
 * Title: Header
 * Slug: infinitum/header
 * Categories: header, infinitum
 * Block Types: core/template-part/header
 * Keywords: header
 * Source: theme
 */
?>
<!-- wp:group {"layout":{"inherit":true}} -->
<div class="wp-block-group">
    <!-- wp:group {"align":"none","style":{"spacing":{"padding":{"bottom":"var:preset|spacing|1)","top":"var:preset|spacing|1)"}}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
	<div class="wp-block-group alignnone" style="padding-top:var(--wp--preset--spacing--1);padding-bottom:var(--wp--preset--spacing--1)">
		<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"left","verticalAlignment":"center"}} -->
		<div class="wp-block-group">
			<!-- wp:site-logo {"width":100,"shouldSyncIcon":false,"className":"is-style-default","style":{"color":{}}} /-->
			<!-- wp:site-title {"level":0,"fontSize":"500"} /-->
		</div>
		<!-- /wp:group -->

		<!-- wp:infinitum/drawer {"drawerID:"0","autoOffsetTop":true,"nestCloseButton":false} /-->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
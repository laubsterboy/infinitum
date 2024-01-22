<?php
/**
 * Title: Hidden 404
 * Slug: infinitum/hidden-404
 * Inserter: no
 * Source: theme
 */
?>
<!-- wp:spacer {"height":"var(--wp--preset--spacing--2)"} -->
<div style="height:var(--wp--preset--spacing--2)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"level":1,"align":"none"} -->
<h1 class="alignnone"><?php echo esc_html_x( '404', 'Error code for a webpage that is not found.', 'infinitum' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:group {"align":"none","layout":{"type":"default"},"style":{"spacing":{"margin":{"top":"var(--wp--preset--spacing--1)"}}}} -->
<div class="wp-block-group alignnone" style="margin-top:var(--wp--preset--spacing--1)">
	<!-- wp:paragraph -->
	<p><?php echo esc_html_x( 'This page could not be found.', 'Message to convey that a webpage could not be found', 'infinitum' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:search {"label":"<?php echo esc_html_x( 'Search', 'label', 'infinitum' ); ?>","placeholder":"<?php echo esc_attr_x( 'Search...', 'placeholder for search field', 'infinitum' ); ?>","showLabel":false,"width":100,"widthUnit":"%","buttonText":"<?php esc_attr_e( 'Search', 'infinitum' ); ?>","buttonUseIcon":true,"align":"center"} /-->
</div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"var(--wp--preset--spacing--2)"} -->
<div style="height:var(--wp--preset--spacing--2)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->
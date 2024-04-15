<?php
/**
 * Title: Footer
 * Slug: infinitum/footer
 * Categories: footer, infinitum
 * Block Types: core/template-part/footer
 * Keywords: footer
 * Source: theme
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|2","bottom":"var:preset|spacing|2"}}},"layout":{"inherit":true,"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--2); padding-bottom:var(--wp--preset--spacing--2);">
	<!-- wp:columns -->
	<div class="wp-block-columns">
		<!-- wp:column {"width":"25%"} -->
		<div class="wp-block-column" style="flex-basis:25%">
			<!-- wp:site-logo {"align":"center"} /-->
			<!-- wp:site-title {"level":0,"fontSize":"500","style":{"spacing":{"margin":{"top":"var:preset|spacing|1-2"}}},"textAlign":"center"} /-->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"25%"} -->
		<div class="wp-block-column" style="flex-basis:25%">
			<!-- wp:heading {"fontSize":"100"} --><h2 class="wp-block-heading has-100-font-size">About</h2><!-- /wp:heading -->
			<!-- wp:paragraph --><p><a href="#">About Us</a></p><!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"25%"} -->
		<div class="wp-block-column" style="flex-basis:25%">
			<!-- wp:heading {"fontSize":"100"} --><h2 class="wp-block-heading has-100-font-size">Team</h2><!-- /wp:heading -->
			<!-- wp:paragraph --><p><a href="#">Our Team</a></p><!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"25%"} -->
		<div class="wp-block-column" style="flex-basis:25%">
			<!-- wp:heading {"fontSize":"100"} --><h2 class="wp-block-heading has-100-font-size">Social</h2><!-- /wp:heading -->
			<!-- wp:social-links -->
			<ul class="wp-block-social-links">
				<!-- wp:social-link {"url":"https://www.facebook.com","service":"facebook"} /-->
				<!-- wp:social-link {"url":"https://www.instagram.com","service":"instagram"} /-->
				<!-- wp:social-link {"url":"https://twitter.com","service":"x"} /-->
				<!-- wp:social-link {"url":"https://youtube.com","service":"youtube"} /-->
			</ul>
			<!-- /wp:social-links -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->

	<!-- wp:group {"fontSize":"90","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"left"}} -->
	<div class="wp-block-group has-90-font-size">
		<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"infinitum/copyright"}}}} --><p></p><!-- /wp:paragraph -->
		<!-- wp:paragraph --><p><a href="#">Privacy Policy</a></p><!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
<?php
/**
 * Integration
 *
 * @package Infinitum
 * @since 0.0.1
 */

namespace infinitum\inc\integrations;

abstract class Integration extends \infinitum\inc\classes\Addon {
	protected readonly \infinitum\inc\Theme $theme;
	public readonly string $dir;
	public readonly string $uri;

    /**
	 * Integration construct
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return void
	 */
	public function __construct($theme, $dir, $uri) {
		$this->theme = $theme;
		$this->dir = $dir;
		$this->uri = $uri;

		$this->set_hooks();
	}
}

?>

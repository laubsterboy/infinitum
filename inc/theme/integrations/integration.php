<?php
/**
 * Integration
 *
 * @package Infinitum
 * @since 0.0.1
 */

namespace infinitum\inc\theme\integrations;

abstract class Integration {
	protected $dir = '';
	protected $theme = null;
	protected $uri = '';

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
	
	
	
	protected function set_hooks() {

	}
}

?>

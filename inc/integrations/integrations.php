<?php
/**
 * Integrations Manager
 *
 * @package Infinitum
 * @since 0.0.1
 */

namespace infinitum\inc\integrations;

class Integrations {
	protected $integrations = array();
	public readonly string $dir;
	public readonly \infinitum\inc\Theme $theme;
	public readonly string $uri;

    /**
	 * Drawers construct
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

		$this->load();
		$this->instantiate();
	}



	/**
	 * Instantiate integrations
	 * 
	 * @since 0.0.1
	 * 
	 * @access protected
	 * 
	 * @return void
	 */
	protected function instantiate() {
		$this->integrations['beaver-builder'] = new beaver_builder\Beaver_Builder($this->theme, $this->dir . 'beaver-builder/', $this->uri . 'beaver-builder/');
	}



	/**
	 * Load
	 * 
	 * @since 0.0.1
	 * 
	 * @access protected
	 * 
	 * @return void
	 */
	protected function load() {
		require_once get_theme_file_path('inc/integrations/integration.php');
		require_once get_theme_file_path('inc/integrations/beaver-builder/beaver-builder.php');
	}
}

?>

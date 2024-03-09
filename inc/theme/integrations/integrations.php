<?php
/**
 * Integrations Manager
 *
 * @package Infinitum
 * @since 0.0.1
 */

namespace infinitum\inc\theme\integrations;

class Integrations {
	protected $dir = '';
	protected $integrations = array();
	protected $theme = null;
	protected $uri = '';

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

		$this->set_hooks();
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
		require_once get_theme_file_path('inc/theme/integrations/integration.php');
		require_once get_theme_file_path('inc/theme/integrations/beaver-builder/beaver-builder.php');
	}



	protected function set_hooks() {
		
	}
}

?>

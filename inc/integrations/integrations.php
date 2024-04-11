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

		$this->load_and_instantiate();
	}



	/**
	 * Conditionally loads and instantiates integrations if the 3rd party plugin they rely on is installed
	 * 
	 * @since 0.0.1
	 * 
	 * @access protected
	 * 
	 * @return void
	 */
	protected function load_and_instantiate(): void {
		require_once get_theme_file_path('inc/integrations/integration.php');

		$integrations = array();

		// Beaver Builder Integration
		$beaver_builder_path = get_theme_file_path('inc/integrations/beaver-builder/beaver-builder.php');

		if (file_exists($beaver_builder_path)) {
			require_once $beaver_builder_path;

			$integrations['beaver-builder'] = new beaver_builder\Beaver_Builder($this->theme, $this->dir . 'beaver-builder/', $this->uri . 'beaver-builder/');
		}

		// TODO: Add an apply_filters here to be able to add Integrations via plguins

		// Only add official Integrations to the integrations class property to ensure they have the proper interface
		foreach ($integrations as $slug => $instance) {
			if (is_a($instance, __NAMESPACE__ . '\Integration')) {
				$this->integrations[$slug] = $instance;
			}
		}
	}



	public function theme_activation($old_theme_name = null, $old_theme = null): void {
		foreach ($this->integrations as $integration) {
			$integration->theme_activation($old_theme_name, $old_theme);
		}
	}



	public function theme_deactivation($new_name, $new_theme, $old_theme): void {
		foreach ($this->integrations as $integration) {
			$integration->theme_deactivation($new_name, $new_theme, $old_theme);
		}
	}
}

?>

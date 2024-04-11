<?php
/**
 * Addon class
 *
 * @package Infinitum
 * @since 0.0.1
 */

namespace infinitum\inc\classes;

abstract class Addon implements \infinitum\inc\interfaces\Addon {
	protected function set_hooks() {

	}



	public function theme_activation($old_theme_name = null, $old_theme = null): void {

	}



	public function theme_deactivation($new_name, $new_theme, $old_theme): void {

	}
}

?>

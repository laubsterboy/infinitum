<?php

/**
 * Addon Interface
 * 
 * @since 0.0.1
 */

namespace infinitum\inc\interfaces;

interface Addon {
	public function theme_activation($old_theme_name = null, $old_theme = null);
	public function theme_deactivation($new_name, $new_theme, $old_theme);
}
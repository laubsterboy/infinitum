<?php
/**
 * Beaver Builder Integration
 *
 * @package Infinitum
 * @since 0.0.1
 */

namespace infinitum\inc\integrations\beaver_builder;

class Beaver_Builder extends \infinitum\inc\integrations\Integration {
    /**
	 * Integration construct
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return void
	 */
	public function __construct($theme, $dir, $uri) {
		parent::__construct($theme, $dir, $uri);
	}



	/**
	 * Inserts a new field array into an existing Beaver Builder $form
	 * 
	 * @since 0.0.1
	 * 
	 * @param $form 				array|object	The form to insert the new items into
	 * @param $name					string			The name of the item
	 * @param $item 				array			The item (tab, section, field) to insert
	 * @param $type					string			The type of item being inserted into the form. Can be 'tab', 'section', or 'field'. Default is 'field'.
	 * @param $match_type			string			The search paramter to determine a match. For $type of 'tab' and 'section' this should be set to 'name'. For $type of 'field' this can be 'name' or 'type' (to match a field type)
	 * @param $tab_match			string			The tab to insert the item into. Default is '', which will search all tabs for a match for the $section or $field
	 * @param $section_match		string			The section name to insert the item into. Default is '', which will search all sections for a match for the $field
	 * @param $field_match			string			The field name to insert the item before, into, or after.
	 * @param $position				string			To position to insert the new item. Can be 'start', 'before', 'replace', 'after', or 'end'. 'start' and 'end' generally require $tab_match and $section_match to have valid values. Default is 'end'.
	 */
	public function insert_form_item($form, $name = '', $item = array(), $type = 'field', $match_type = 'name', $tab_match = '', $section_match = '', $field_match = '', $position = 'end') {
		$form = (array) $form;
		$_form = $form;
		$has_tabs = false;
		
		// Check that the $form is an array
		if (!is_array($form)) return $form;

		// Check that $name is not empty
		if (empty($name) || !is_string($name)) return $form;

		// Check that $item is not empty
		if (empty($item) || !is_array($item)) return $form;

		// Normalize the $type
		if (!is_string($type) || !in_array($type, array('tab', 'section', 'field'), true)) $type = 'field';

		// Normalize the $match_type
		if (!is_string($match_type) || !in_array($match_type, array('name', 'type'), true)) $match_type = 'name';

		// Check that the various matches are strings
		if (!is_string($tab_match)) $tab_match = '';
		if (!is_string($section_match)) $section_match = '';
		if (!is_string($field_match)) $field_match = '';

		// Normalize the $position
		if (!is_string($position) || !in_array($position, array('start', 'before', 'replace', 'after', 'end'), true)) $position = 'end';

		// Some forms use a 'tabs' array and other forms have the tabs as top level array indexes
		if (isset($form['tabs'])) {
			$form = $form['tabs'];
			$has_tabs = true;
		}

		/*
		$form => array(
			'tabs' => array( // Not all $forms have this array
				'tab_name' => array(
					'sections' => array(
						'section_name' => array(
							'fields' => array(
								'field_name' => array()
							)
						)
					)
				)
			)
		);

		$form['tabs']['tab_name']['sections']['section_name']['fields']['field_name'];
		*/

		// Tabs
		if ($type === 'tab') {
			if ($position === 'start') {
				$form = array_merge(array($name => $item), $form);
			} else if ($position === 'replace' && isset($form[$tab_match])) {
				$form[$tab_match] = $item;
			} else if ($position === 'end') {
				$form[$name] = $item;
			} else if ($position === 'before' || $position === 'after') {
				$tab_keys = array_keys($form);
				$new_form = array();

				foreach ($tab_keys as $tab_key) {
					if ($tab_match === $tab_key) {
						if ($position === 'before') {
							$new_form[$name] = $item;
							$new_form[$tab_key] = $form[$tab_key];
						} else if ($position === 'after') {
							$new_form[$tab_key] = $form[$tab_key];
							$new_form[$name] = $item;
						} else {
							$new_form[$tab_key] = $form[$tab_key];
						}
					} else {
						$new_form[$tab_key] = $form[$tab_key];
					}
				}

				$form = $new_form;
			}
		} else {
			foreach ($form as $tab_name => $tab) {
				if (isset($tab['sections']) && ($type === 'section' || $type === 'field')) {
					// Sections
					if ($type === 'section' && $position === 'start' && !empty($tab_match) && $tab_match === $tab_name) {
						$form[$tab_name]['sections'] = array_merge(array($name => $item), $form[$tab_name]['sections']);
					} else if ($type === 'section' && $position === 'replace' && $match_type === 'name' && isset($form[$tab_name]['sections'][$section_match])) {
						$form[$tab_name]['sections'][$section_match] = $item;
					} else if ($type === 'section' && $position === 'end' && !empty($tab_match) && $tab_match === $tab_name) {
						$form[$tab_name]['sections'][$name] = $item;
					} else if (($type === 'section' || $type === 'field') && ($position !== 'start' && $position !== 'end') && ($position === 'before' || $position === 'after')) {
						$new_sections = array();
	
						if ($type === 'section') {
							foreach ($tab['sections'] as $section_name => $section) {
								if ($section_match === $section_name) {
									if ($position === 'before') {
										$new_sections[$name] = $item;
										$new_sections[$section_name] = $section;
									} else if ($position === 'after') {
										$new_sections[$section_name] = $section;
										$new_sections[$name] = $item;
									} else {
										// No match, add the section without any change
										$new_sections[$section_name] = $section;
									}
								} else {
									// No match, add the section without any change
									$new_sections[$section_name] = $section;
								}
							}
						}
	
						// Update the sections prior to looping through the fields
						$form[$tab_name]['sections'] = $new_sections;
	
						if ($type === 'field') {
							foreach ($tab['sections'] as $section_name => $section) {
								if (isset($section['fields'])) {
									// Fields
									if ($position === 'start' && !empty($tab_match) && $tab_match === $tab_name && !empty($section_match) && $section_match === $section_name) {
										$form[$tab_name]['sections'][$section_name]['fields'] = array_merge(array($name => $item), $form[$tab_name]['sections'][$section_name]['fields']);
									} else if ($position === 'replace' && $match_type === 'name' && !empty($field_match) && isset($form[$tab_name]['sections'][$section_name]['fields'][$field_match])) {
										$form[$tab_name]['sections'][$section_name]['fields'][$field_match] = $item;
									} else if ($position === 'end' && !empty($tab_match) && $tab_match === $tab_name && !empty($section_match) && $section_match === $section_name) {
										$form[$tab_name]['sections'][$section_name]['fields'][$name] = $item;
									} else if (($position !== 'start' && $position !== 'end') && ($position === 'before' || ($position === 'replace' && $match_type === 'type') || $position === 'after')) {
										$new_fields = array();
			
										foreach ($section['fields'] as $field_name => $field) {
											if ($match_type === 'type' && !isset($field['type'])) {
												$new_fields[$field_name] = $field;
												continue;
											}
			
											if (($match_type === 'name' && $field_match === $field_name) || ($match_type === 'type' && $field_match === $field['type'])) {
												if ($position === 'before') {
													$new_fields[$name] = $item;
													$new_fields[$field_name] = $field;
												} else if ($position === 'replace') {
													$new_fields[$field_name] = $item;
												} else if ($position === 'after') {
													$new_fields[$field_name] = $field;
													$new_fields[$name] = $item;
												} else {
													// No $position match, add the field without any change
													$new_fields[$field_name] = $field;
												}
											} else {
												// No match, add the field without any change
												$new_fields[$field_name] = $field;
											}
										}
			
										$form[$tab_name]['sections'][$section_name]['fields'] = $new_fields;
									}
								}
							}
						}
					}
				}
			}
		}

		if ($has_tabs) {
			$_form['tabs'] = $form;
			$form = $_form;
		}

		return $form;
	}



	/**
	 * Checks to see if the Beaver Builder page builder plugin is active for the current post/page
	 * meaning that the page builder UI/editor is currently in use.
	 * 
	 * @since 0.0.1
	 * 
	 * @return boolean
	 */
	public function is_beaver_builder_active() {
		$active = false;

		if (class_exists('\FLBuilderModel') && \FLBuilderModel::is_builder_active()) {
			$active = true;
		}

		return apply_filters('infinitum_beaver_builder_active', $active);
	}



	/**
	 * Checks to see if the Beaver Builder page builder plugin is activated and is enabled for the current post/page
	 * meaning that the page builder has been used to edit the post/page.
	 * 
	 * @since 0.0.1
	 * 
	 * @param integer $post_id	The id of the post to check if Beaver Builder is enabled
	 * @return boolean
	 */
	public function is_beaver_builder_enabled($post_id = false) {
		$enabled = false;

		if (is_numeric($post_id)) {
			// Beaver Builder is_builder_enabled doesn't allow for passing a post ID, wo check the post meta manually
			$enabled = get_post_meta($post_id, '_fl_builder_enabled');
		} else if (class_exists('\FLBuilderModel') && \FLBuilderModel::is_builder_enabled()) {
			$enabled = true;
		}

		return apply_filters('infinitum_beaver_builder_enabled', filter_var($enabled, FILTER_VALIDATE_BOOLEAN));
	}



	/**
	 * Checks if the Beaver Builder plugin is installed and active
	 * 
	 * @since 0.0.1
	 * 
	 * @return boolean
	 */
	public function is_beaver_builder_installed() {
		return class_exists('FLBuilderModel') ? true : false;
	}



	protected function maybe_set_page_template() {
		// Check if Beaver Builder is allowed to edit this post type
		if (class_exists('\FLBuilderModel') && !in_array(get_post_type(), \FLBuilderModel::get_post_types())) return false;

		// Check if Beaver Builder is active
		if (!$this->is_beaver_builder_active()) return false;

		// Set the template (only if the current template value is blank)
		$this->theme->set_page_template(get_the_ID(), 'blank-full-width', array(''));
	}



	/**
	 * Registers Beaver Builder forms (generally adding to existing forms)
	 * 
	 * @since 0.0.1
	 * 
	 * @return array
	 */
	protected function register_settings_form(string $slug, $form, $id) {
		if (!empty($slug)) {
			$form_file = $this->dir . 'forms/' . $slug . '.php';

			if (file_exists($form_file)) {
				include $form_file;
			}
		}

		return $form;
	}



	/**
	 * Renders CSS and appends it to the $css variable. Expects the $css_file to echo CSS rules.
	 * 
	 * @since 0.0.1
	 * 
	 * @param string 	$slug				The slug/name of the css file to render
	 * @param string 	$css				The CSS rules that are being appended
	 * @param array		$nodes				The rows/columns/modules that make up the Beaver Builder layout being rendered
	 * @param object	$global_settings	The Beaver Builder global settings
	 * @return string
	 */
	protected function render_css(string $slug, $css, $nodes, $global_settings) {
		if (!empty($slug)) {
			$css_file = $this->dir . 'css/' . $slug . '.css.php';

			if (file_exists($css_file)) {
				ob_start();

				include $css_file;

				$css .= ob_get_clean();
			}
		}

		return $css;
	}



	protected function set_hooks() {
		add_filter('fl_builder_custom_fields', array($this, 'wp_hook_fl_builder_custom_fields'));
		add_filter('fl_builder_register_settings_form', array($this, 'wp_hook_fl_builder_register_settings_form'), 10, 2);
		add_filter('fl_builder_render_css', array($this, 'wp_hook_fl_builder_render_css'), 10, 3);
		add_filter('fl_builder_settings_form_defaults', array($this, 'wp_hook_fl_builder_settings_form_defaults'), 10, 2);
		add_action('fl_builder_ui_enqueue_scripts', array($this, 'wp_hook_fl_builder_ui_enqueue_scripts'));
		add_filter('fl_builder_ui_js_config', array($this, 'wp_hook_fl_builder_ui_js_config'));
		add_action('init', array($this, 'wp_hook_init'));
		add_action('wp', array($this, 'wp_hook_wp'));
		add_action('wp_enqueue_scripts', array($this, 'wp_hook_wp_enqueue_scripts'), 10);
	}



	public function wp_hook_fl_builder_custom_fields($fields) {
		$fields['infinitum-spacing'] = $this->dir . 'fields/infinitum-spacing/infinitum-spacing.php';
		$fields['infinitum-typography'] = $this->dir . 'fields/infinitum-typography/infinitum-typography.php';

		return $fields;
	}



	public function wp_hook_fl_builder_register_settings_form($form, $id) {
		$form = $this->register_settings_form('infinitum-spacing', $form, $id);
		$form = $this->register_settings_form('infinitum-typography', $form, $id);

		return $form;
	}



	public function wp_hook_fl_builder_render_css($css, $nodes, $global_settings) {
		$css = $this->render_css('infinitum-spacing', $css, $nodes, $global_settings);
		$css = $this->render_css('infinitum-typography', $css, $nodes, $global_settings);

		return $css;
	}



	public function wp_hook_fl_builder_settings_form_defaults($defaults, $form_type) {
		$content_width = $this->theme->get_theme_setting('contentWidth');
		$typography = $this->theme->get_theme_setting('typography');
		
		if ($form_type === 'global') {
			$defaults->auto_spacing = 0;
			
			if (!empty($typography['fontSizeRoot'])) {
				$defaults->responsive_base_fontsize = $typography['fontSizeRoot'];
			}
			
			$defaults->default_heading_selector = '';

			$defaults->show_default_heading = 0;

			// Disable responsive column max width
			$defaults->responsive_col_max_width = 0;

			// Row Width
			$defaults->row_width = $content_width;

			// Large Breakpoint
			$defaults->large_breakpoint = 1200;
			$defaults->medium_breakpoint = 992;
			$defaults->responsive_breakpoint = 768;

			// Row Margins
			$defaults->row_margins_top = 0;
			$defaults->row_margins_right = 0;
			$defaults->row_margins_bottom = 0;
			$defaults->row_margins_left = 0;
			$defaults->row_margins_top_large = 0;
			$defaults->row_margins_right_large = 0;
			$defaults->row_margins_bottom_large = 0;
			$defaults->row_margins_left_large = 0;
			$defaults->row_margins_top_medium = 0;
			$defaults->row_margins_right_medium = 0;
			$defaults->row_margins_bottom_medium = 0;
			$defaults->row_margins_left_medium = 0;
			$defaults->row_margins_top_responsive = 0;
			$defaults->row_margins_right_responsive = 0;
			$defaults->row_margins_bottom_responsive = 0;
			$defaults->row_margins_left_responsive = 0;

			// Row Padding
	        $defaults->row_padding_top = 0;
			$defaults->row_padding_right = 0;
			$defaults->row_padding_bottom = 0;
			$defaults->row_padding_left = 0;
			$defaults->row_padding_top_large = 0;
			$defaults->row_padding_right_large = 0;
			$defaults->row_padding_bottom_large = 0;
			$defaults->row_padding_left_large = 0;
			$defaults->row_padding_top_medium = 0;
			$defaults->row_padding_right_medium = 0;
			$defaults->row_padding_bottom_medium = 0;
			$defaults->row_padding_left_medium = 0;
			$defaults->row_padding_top_responsive = 0;
			$defaults->row_padding_right_responsive = 0;
			$defaults->row_padding_bottom_responsive = 0;
			$defaults->row_padding_left_responsive = 0;

			// Column Margins
			$defaults->column_margins_top = 0;
			$defaults->column_margins_right = 0;
			$defaults->column_margins_bottom = 0;
			$defaults->column_margins_left = 0;
			$defaults->column_margins_top_large = 0;
			$defaults->column_margins_right_large = 0;
			$defaults->column_margins_bottom_large = 0;
			$defaults->column_margins_left_large = 0;
			$defaults->column_margins_top_medium = 0;
			$defaults->column_margins_right_medium = 0;
			$defaults->column_margins_bottom_medium = 0;
			$defaults->column_margins_left_medium = 0;
			$defaults->column_margins_top_responsive = 0;
			$defaults->column_margins_right_responsive = 0;
			$defaults->column_margins_bottom_responsive = 0;
			$defaults->column_margins_left_responsive = 0;

			// Column Padding
			$defaults->column_padding_top = 0;
			$defaults->column_padding_right = 0;
			$defaults->column_padding_bottom = 0;
			$defaults->column_padding_left = 0;
			$defaults->column_padding_top_large = 0;
			$defaults->column_padding_right_large = 0;
			$defaults->column_padding_bottom_large = 0;
			$defaults->column_padding_left_large = 0;
			$defaults->column_padding_top_medium = 0;
			$defaults->column_padding_right_medium = 0;
			$defaults->column_padding_bottom_medium = 0;
			$defaults->column_padding_left_medium = 0;
			$defaults->column_padding_top_responsive = 0;
			$defaults->column_padding_right_responsive = 0;
			$defaults->column_padding_bottom_responsive = 0;
			$defaults->column_padding_left_responsive = 0;

			// Module Margins
			$defaults->module_margins_top = 0;
			$defaults->module_margins_right = 0;
			$defaults->module_margins_bottom = 0;
			$defaults->module_margins_left = 0;
			$defaults->module_margins_top_large = 0;
			$defaults->module_margins_right_large = 0;
			$defaults->module_margins_bottom_large = 0;
			$defaults->module_margins_left_large = 0;
			$defaults->module_margins_top_medium = 0;
			$defaults->module_margins_right_medium = 0;
			$defaults->module_margins_bottom_medium = 0;
			$defaults->module_margins_left_medium = 0;
			$defaults->module_margins_top_responsive = 0;
			$defaults->module_margins_right_responsive = 0;
			$defaults->module_margins_bottom_responsive = 0;
			$defaults->module_margins_left_responsive = 0;
		}

		return $defaults;
	}



	public function wp_hook_fl_builder_ui_enqueue_scripts() {
		// Infinitum Spacing field
		wp_enqueue_style('infinitum-spacing-field', $this->uri . 'fields/infinitum-spacing/infinitum-spacing.css', '0.0.1');
	}



	public function wp_hook_fl_builder_ui_js_config($config = array()) {
		if (array_key_exists('responsiveFields', $config)) {
			$config['responsiveFields'][] = 'infinitum-spacing';
			$config['responsiveFields'][] = 'infinitum-typography';
		}

		return $config;
	}



	public function wp_hook_init() {
		$breadcrumbs_module_path = $this->dir . 'modules/infinitum-breadcrumbs/infinitum-breadcrumbs.php';

		if ($this->is_beaver_builder_installed()) {
			if (file_exists($breadcrumbs_module_path)) {
				require_once $breadcrumbs_module_path;

				modules\infinitum_breadcrumbs\Infinitum_Breadcrumbs_Module::register($this->theme);
			}
		}
	}



	public function wp_hook_wp() {
		// Maybe set the page template
		$this->maybe_set_page_template();
	}



	public function wp_hook_wp_enqueue_scripts() {
		if ($this->is_beaver_builder_installed()) {
			wp_enqueue_style('infinitum-beaver-builder', $this->uri . 'css/beaver-builder.css', '0.0.1');

			if ($this->is_beaver_builder_active()) {
				
			}
		}
	}
}

?>

<?php

$defaults = array(
	'font_size' => '0',
	'line_height' => '0'
);
$media_sizes = array('default', 'large', 'medium', 'responsive');
$base_setting_name = 'infinitum_typography';

foreach ($nodes as $node_types_key => $node_types) {
	if (!empty($node_types)) {
		foreach ($node_types as $key => $node) {
			$all_typography_settings = array();
			$setting_names = array();
			$node_selector = '.fl-node-' . $node->node;

			if ($node->type === 'row') {
				if (isset($node->settings->{$base_setting_name})) {
					$setting_names[$base_setting_name] = array(
						'selector' => $node_selector . ' > .fl-row-content-wrap',
						'important' => false
					);
				}

				$all_typography_settings[] = array(
					'settings' => $node->settings,
					'setting_names' => $setting_names
				);
			} else if ($node->type === 'column') {
				if (isset($node->settings->{$base_setting_name})) {
					$setting_names[$base_setting_name] = array(
						'selector' => $node_selector . ' > .fl-col-content',
						'important' => false,
					);
				}

				$all_typography_settings[] = array(
					'settings' => $node->settings,
					'setting_names' => $setting_names
				);
			} else if ($node->type === 'module') {
				if (isset($node->settings->{$base_setting_name})) {
					$setting_names[$base_setting_name] = array(
						'selector' => $node_selector . ' > .fl-module-content',
						'important' => false
					);
				}

				$field_forms = array();

				// Loop through the module form
				if (isset($node->form)) {
					$tabs = $node->form;

					if (isset($tabs['tabs'])) {
						$tabs = $tabs['tabs'];
					}

					foreach ($tabs as $tab_name => $tab) {
						if (isset($tab['sections'])) {
							foreach ($tab['sections'] as $section_name => $section) {
								if (isset($section['fields'])) {
									foreach ($section['fields'] as $field_name => $field) {
										// Field is a 'form' so save this for later
										if ($field['type'] === 'form' && !empty($field['form'])) {

											$field_form = \FLBuilderModel::get_settings_form($field['form']);

											if (!empty($field_form)) {
												$field_forms[] = array(
													'setting_name' => $field_name,
													'form' => $field_form
												);
											}
										}

										// Field is a 'typography' - add to the list to potentially render
										if ($field['type'] === 'typography') {
											$selector = '.fl-module' . $node_selector;
											$setting_name = str_replace($field['type'], $base_setting_name, $field_name);
											$important = false;
											
											if (isset($field['preview']) && is_array($field['preview']) && !empty($field['preview']['selector'])) {
												// Heading: {node}.fl-module-heading .fl-heading
												$selector = $field['preview']['selector'];

												if (stripos($selector, '{node}') !== false) {
													// field preview selector already includes a placeholder for the $node selector
													$selector = str_replace('{node}', $node_selector, $selector);
												} else {
													// field preview selector doesn't have a placeholder and expects to prepend context specific selectors
													$selector_parts = explode(',', $selector);

													foreach ($selector_parts as $selector_key => $selector_part) {
														$selector_parts[$selector_key] = '.fl-module' . $node_selector . ' ' . trim($selector_part);
													}

													$selector = implode(', ', $selector_parts);
												}
											} else {
												// Target specifig modules that don't include preview selector data in the form AND the default selector isn't specific enough to work properly
												if ($node->slug === 'button-group') {
													$selector .= ' a.fl-button';
													$selector .= ',' . PHP_EOL . $selector . ':visited';
												} else if ($node->slug === 'countdown') {
													if ($setting_name === 'number_infinitum_typography') {
														$selector .= ' .fl-countdown .fl-countdown-unit-number';
													} else if ($setting_name === 'label_infinitum_typography') {
														$selector .= ' .fl-countdown .fl-countdown-unit-label';
													}
												}
											}

											if (isset($field['preview']) && is_array($field['preview']) && isset($field['preview']['important']) && is_bool($field['preview']['important'])) {
												$important = true;
											}

											$setting_names[$setting_name] = array(
												'selector' => $selector,
												'important' => $important
											);
										}
									}
								}
							}
						}
					}
				}

				$all_typography_settings[] = array(
					'settings' => $node->settings,
					'setting_names' => $setting_names
				);

				$field_forms_setting_names = array();

				// Loop through the forms that were registered separately from the module form
				if (!empty($field_forms)) {
					foreach ($field_forms as $field_form) {
						if (isset($field_form['form'])) {
							$tabs = $field_form['form'];
		
							if (isset($tabs['tabs'])) {
								$tabs = $tabs['tabs'];
							}
		
							foreach ($tabs as $tab_name => $tab) {
								if (isset($tab['sections'])) {
									foreach ($tab['sections'] as $section_name => $section) {
										if (isset($section['fields'])) {
											foreach ($section['fields'] as $field_name => $field) {
												// Field is a 'typography' - add to the list to potentially render
												if ($field['type'] === 'typography') {
													$replacement_ID = '-{ID}';
													$selector = isset($setting_names[$base_setting_name]) ? $setting_names[$base_setting_name]['selector'] : '.fl-module' . $node_selector;
													$important = false;
													$setting_name = str_replace($field['type'], $base_setting_name, $field_name);

													if (isset($field['preview']) && is_array($field['preview']) && !empty($field['preview']['selector'])) {
														// Heading: {node}.fl-module-heading .fl-heading
														$selector = $field['preview']['selector'];
			
														if (stripos($selector, '{node}') !== false) {
															// field preview selector already includes a placeholder for the $node selector
															$selector = str_replace('{node}', $node_selector, $selector);
														} else {
															// field preview selector doesn't have a placeholder and expects to prepend context specific selectors
															$selector_parts = explode(',', $selector);
			
															foreach ($selector_parts as $selector_key => $selector_part) {
																$selector_parts[$selector_key] = '.fl-module' . $node_selector . ' ' . trim($selector_part);
															}
			
															$selector = implode(', ', $selector_parts);
														}
													}

													if ($node->slug === 'content-slider') {
														$replacement_ID = 'fl-slide-{ID}';

														if ($setting_name === 'title_infinitum_typography') {
															$selector .= ' .fl-slide-title';
														} else if ($setting_name === 'text_infinitum_typography') {
															$selector .= ' .fl-slide-text';
														}
													} else if ($node->slug === 'pricing-table') {
														$replacement_ID = 'fl-pricing-table-column-{ID}';

														if ($setting_name === 'price_infinitum_typography') {
															$selector .= ' .fl-pricing-table-price';
														} else if ($setting_name === 'ribbon_infinitum_typography') {
															$selector .= ' .fl-pricing-ribbon .fl-pricing-ribbon-content';
														} else if ($setting_name === 'title_infinitum_typography') {
															$selector .= ' h2.fl-pricing-table-title';
														}
													}

													$selector = str_replace($node_selector, $node_selector . ' [class*="' . $replacement_ID . '"]', $selector);

													if (isset($field['preview']) && is_array($field['preview']) && isset($field['preview']['important']) && is_bool($field['preview']['important'])) {
														$important = true;
													}

													if (!empty($selector)) {
														$field_forms_setting_names[] = array(
															'setting_name' => $field_form['setting_name'],
															'setting_names' => array(
																$setting_name => array(
																	'selector' => $selector,
																	'important' => $important
																)
															)
														);
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}

				if (!empty($field_forms_setting_names)) {
					foreach ($field_forms_setting_names as $field_forms_setting_name) {
						if (isset($node->settings->{$field_forms_setting_name['setting_name']})) {
							foreach ($node->settings->{$field_forms_setting_name['setting_name']} as $field_forms_settings_key => $field_forms_settings) {
								foreach ($field_forms_setting_name['setting_names'] as $field_forms_field_name => $field_forms_field_data) {
									$field_forms_field_data['selector'] = str_replace('{ID}', $field_forms_settings_key, $field_forms_field_data['selector']);
								}

								$all_typography_settings[] = array(
									'settings' => $field_forms_settings,
									'setting_names' => array(
										$field_forms_field_name => $field_forms_field_data
									)
								);
							}
						}
					}
				}
			}

			// If no setting names have been added then skip this $node
			if (empty($all_typography_settings)) continue;

			foreach ($media_sizes as $media_size) {
				if (!empty($all_typography_settings)) {
					foreach ($all_typography_settings as $settings) {
						foreach ($settings['setting_names'] as $setting_name => $setting_data) {
							$important = $setting_data['important'] === true ? ' !important' : '';

							if ($media_size !== 'default') {
								$setting_name .= '_' . $media_size;
							}

							if (isset($settings['settings']->{$setting_name}) && (is_array($settings['settings']->{$setting_name}) || is_object($settings['settings']->{$setting_name}))) {
								$node_typography_settings = (array) $settings['settings']->{$setting_name};

								// Font Size
								if (!empty($node_typography_settings['font_size']) && $node_typography_settings['font_size'] !== $defaults['font_size']) {
									\FLBuilderCSS::rule(array(
										'selector'		=> $setting_data['selector'],
										'media'			=> $media_size,
										'props'			=> array(
											'font-size'		=> 'var(--wp--preset--font-size--' . $node_typography_settings['font_size'] . ')' . '/* ' . $node->name . ': ' . $setting_name . ' */' . $important,
											'line-height'	=> 'var(--wp--custom--infinitum--typography--line-height)' . $important // Must be set alongside the font-size to force a recalculation of the line-height variable
										)
									));
								}

								// Line Height
								if (!empty($node_typography_settings['line_height']) && $node_typography_settings['line_height'] !== $defaults['line_height']) {
									\FLBuilderCSS::rule(array(
										'selector'		=> $setting_data['selector'],
										'media'			=> $media_size,
										'props'			=> array(
											'line-height'	=> 'var(--wp--custom--infinitum--typography--' . $node_typography_settings['line_height'] . '-line-height)' . $important
										)
									));
								}
							}
						}
					}
				}
			}
		}
	}
}
\FLBuilderCSS::render();
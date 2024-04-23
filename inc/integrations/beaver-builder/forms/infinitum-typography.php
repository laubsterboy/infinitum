<?php

// Skip these form $id's
if (in_array($id, array('styles'), true)) return $form;

// Check if this $form contains a "typography" field
$wp_global_settings = wp_get_global_settings(array(), array('origin' => 'base'));
$font_sizes = array();
$font_size_options = array('0' => '(inherited)');
$line_height_options = array(
	'0' => '(inherited)',
	'heading' => 'Heading',
	'body' => 'Body'
);

if (!empty($wp_global_settings['typography']['fontSizes']['theme']) && is_array($wp_global_settings['typography']['fontSizes']['theme'])) {
	$font_sizes = $wp_global_settings['typography']['fontSizes']['theme'];
}

foreach ($font_sizes as $font_size) {
	if (!empty($font_size['slug'] && !empty($font_size['name']))) {
		$font_size_options[$font_size['slug']] = $font_size['name'];
	} else if (!empty($font_size['slug'])) {
		$font_size_options[$font_size['slug']] = $font_size['slug'];
	}
}

if (!empty($font_size_options)) {
	$field = array(
		'type'			=> 'infinitum-typography',
		'label'			=> 'Theme Typography',
		'options'		=> array(
			'font_size' 	=> $font_size_options,
			'line_height'	=> $line_height_options
		),
		'responsive'	=> array(
			'default'		=> array(
				'default'		=> '0',
				'large'			=> '0',
				'medium'		=> '0',
				'responsive'	=> '0'
			)
		)
	);

	$form = $this->insert_form_item($form, 'infinitum_typography', true, $field, 'field', 'type', '', '', 'typography', 'before');
}
<?php

if ($id !== 'row' && $id !== 'col' && $id !== 'module_advanced') return $form;

$wp_global_settings = wp_get_global_settings(array(), array('origin' => 'base'));
$default_spacing = array(
	'row' => array(
		'margin' => '0',
		'padding' => '0'
	),
	'col' => array(
		'margin' => '0',
		'padding' => '0'
	),
	'module_advanced' => array(
		'margin' => '1'
	)
);
$spacing_sizes = array();
$spacing_options = array('0' => '0');

if (!empty($wp_global_settings['spacing']['spacingSizes']['theme']) && is_array($wp_global_settings['spacing']['spacingSizes']['theme'])) {
	$spacing_sizes = $wp_global_settings['spacing']['spacingSizes']['theme'];
}

foreach ($spacing_sizes as $spacing_size) {
	if (!empty($spacing_size['slug']) && !empty($spacing_size['name'])) {
		$spacing_options[$spacing_size['slug']] = $spacing_size['name'];
	}
}

$spacing_options['inherit'] = __('(inherited)', $this->theme->get_textdomain());

$margin_fields = array();

$margin_fields['infinitum_advanced_margins'] = array(
	'type'				=> 'infinitum-spacing',
	'label'				=> 'Margins',
	'options'			=> $spacing_options,
	'preview'			=> array(
		'type'				=> 'refresh'
	),
	'responsive'		=> array(
		'default'			=> array(
			'default'			=> array('top' => $default_spacing[$id]['margin'], 'right' => $default_spacing[$id]['margin'], 'bottom' => $default_spacing[$id]['margin'], 'left' => $default_spacing[$id]['margin']),
			'large'				=> array('top' => 'inherit', 'right' => 'inherit', 'bottom' => 'inherit', 'left' => 'inherit'),
			'medium'			=> array('top' => 'inherit', 'right' => 'inherit', 'bottom' => 'inherit', 'left' => 'inherit'),
			'responsive'		=> array('top' => 'inherit', 'right' => 'inherit', 'bottom' => 'inherit', 'left' => 'inherit')
		)
	)
);

$padding_fields = array();

if ($id === 'row' || $id === 'col') {
	$padding_fields['infinitum_advanced_padding'] = array(
		'type'				=> 'infinitum-spacing',
		'label'				=> 'Padding',
		'options'			=> $spacing_options,
		'preview'			=> array(
			'type'				=> 'refresh'
		),
		'responsive'		=> array(
			'default'			=> array(
				'default'			=> array('top' => $default_spacing[$id]['padding'], 'right' => $default_spacing[$id]['padding'], 'bottom' => $default_spacing[$id]['padding'], 'left' => $default_spacing[$id]['padding']),
				'large'				=> array('top' => 'inherit', 'right' => 'inherit', 'bottom' => 'inherit', 'left' => 'inherit'),
				'medium'			=> array('top' => 'inherit', 'right' => 'inherit', 'bottom' => 'inherit', 'left' => 'inherit'),
				'responsive'		=> array('top' => 'inherit', 'right' => 'inherit', 'bottom' => 'inherit', 'left' => 'inherit')
			),
		)
	);
}

if ($id === 'row' || $id === 'col') {
	/**
	 * Rows and Columns
	 */
	$spacing_section = array(
		'infinitum_spacing'	=> array(
			'title'				=> __('Theme Spacing', $this->theme->get_textdomain()),
			'fields'			=> array_merge($margin_fields, $padding_fields)
		)
	);

	if (isset($form['tabs']['advanced']['sections'])) {
		$form['tabs']['advanced']['sections'] = array_merge($spacing_section, $form['tabs']['advanced']['sections']);
	}
} else if ($id === 'module_advanced') {
	/**
	 * Module advanced tab
	 */
	$spacing_section = array(
		'infinitum_spacing'	=> array(
			'title'				=> __('Theme Spacing', $this->theme->get_textdomain()),
			'fields'			=> $margin_fields
		)
	);

	if (isset($form['sections'])) {
		$form['sections'] = array_merge($spacing_section, $form['sections']);
	}

	// Give the module margins section a title
	if ($id === 'module_advanced' && isset($form['sections']['margins']) && !isset($form['sections']['margins']['title'])) {
		$form['sections']['margins']['title'] = __('Spacing', 'fl-builder');
	}
}
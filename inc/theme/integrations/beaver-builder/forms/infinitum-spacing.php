<?php

if ($id !== 'row' && $id !== 'col' && $id !== 'module_advanced') return $form;

$wp_global_settings = wp_get_global_settings();
$default_spacing = array(
	'row' => array(
		'margin' => 0,
		'padding' => 0
	),
	'col' => array(
		'margin' => 0,
		'padding' => 0
	),
	'module_advanced' => array(
		'margin' => 1
	)
);
$spacing_sizes = array();
$spacing_options = array();

$selector = '';

if ($id === 'row') {
	$selector .= '.fl-row-content-wrap';
} else if ($id === 'col') {
	$selector .= '.fl-col-content';
} else if ($id === 'module_advanced') {
	$selector .= '.fl-module-content';
}

if (!empty($wp_global_settings['spacing']['spacingSizes']['theme']) && is_array($wp_global_settings['spacing']['spacingSizes']['theme'])) {
	$spacing_sizes = $wp_global_settings['spacing']['spacingSizes']['theme'];
}

foreach ($spacing_sizes as $spacing_size) {
	if (!empty($spacing_size['slug']) && !empty($spacing_size['name'])) {
		$spacing_options[$spacing_size['slug']] = $spacing_size['name'];
	}
}

$spacing_options['custom'] = __('Custom', $this->theme->get_text_domain());

$margin_fields = array();

$margin_fields['infinitum_advanced_margins'] = array(
	'type'				=> 'infinitum-spacing',
	'label'				=> 'Margins',
	'default'			=> $default_spacing[$id]['margin'],
	'options'			=> $spacing_options,
	// TODO: Add instant preview so adjustments to the spacing can be seen without a delay
	/*'preview'			=> array(
		'type'				=> 'css',
		'selector'			=> $selector,
		'property'			=> 'margin',
		//'format_value'		=> 'var(--wp--preset--spacing--%s)'
	),*/
	'responsive'		=> true
);

$padding_fields = array();

if ($id === 'row' || $id === 'col') {
	$padding_fields['infinitum_advanced_padding'] = array(
		'type'				=> 'infinitum-spacing',
		'label'				=> 'Padding',
		'default'			=> $default_spacing[$id]['padding'],
		'options'			=> $spacing_options,
		// TODO: Add instant preview so adjustments to the spacing can be seen without a delay
		/*'preview'			=> array(
			'type'				=> 'css',
			'selector'			=> $selector,
			'property'			=> 'padding',
			//'format_value'		=> 'var(--wp--preset--spacing--%s)'
		),*/
		'responsive'		=> true
	);
}

if ($id === 'row' || $id === 'col') {
	/**
	 * Rows and Columns
	 */
	$spacing_section = array(
		'infinitum_spacing'	=> array(
			'title'				=> 'Theme Spacing',
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
			'title'				=> 'Theme Spacing',
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
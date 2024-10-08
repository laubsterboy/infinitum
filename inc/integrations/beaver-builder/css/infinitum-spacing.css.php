<?php

$directions = array('top', 'right', 'bottom', 'left');
$media_sizes = array('default', 'large', 'medium', 'responsive');
$default_spacing = array(
	'row' => array(
		'margin' => array(
			'default' => 0,
			'large' => 'inherit',
			'medium' => 'inherit',
			'responsive' => 'inherit'
		),
		'padding' => array(
			'default' => 0,
			'large' => 'inherit',
			'medium' => 'inherit',
			'responsive' => 'inherit'
		)
	),
	'column' => array(
		'margin' => array(
			'default' => 0,
			'large' => 'inherit',
			'medium' => 'inherit',
			'responsive' => 'inherit'
		),
		'padding' => array(
			'default' => 0,
			'large' => 'inherit',
			'medium' => 'inherit',
			'responsive' => 'inherit'
		)
	),
	'module' => array(
		'margin' => array(
			'default' => 1,
			'large' => 'inherit',
			'medium' => 'inherit',
			'responsive' => 'inherit'
		)
	)
);
$properties = array(
	'margin' => 'margins',
	'padding' => 'padding'
);

foreach ($nodes as $node_types_key => $node_types) {
	if (!empty($node_types)) {
		foreach ($node_types as $key => $node) {
			$node_selector = '.fl-node-' . $node->node;

			$selector = $node_selector . ' > ';
			
			if ($node->type === 'row') {
				$selector .= '.fl-row-content-wrap';
			} else if ($node->type === 'column') {
				$selector .= '.fl-col-content';
			} else if ($node->type === 'module') {
				$selector .= '.fl-module-content';

				if ($node->slug === 'box') {
					$selector = $node_selector;
				}
			}

			foreach ($media_sizes as $media_size) {
				foreach ($properties as $property_name_singular => $property_name_plural) {
					$setting_name = 'infinitum_advanced_' . $property_name_plural;

					if ($media_size !== 'default') {
						$setting_name .= '_' . $media_size;
					}

					if (isset($node->settings->{$setting_name}) && is_array($node->settings->{$setting_name})) {
						$node_settings = $node->settings->{$setting_name};

						foreach ($node_settings as $direction => $setting_value) {
							if ($setting_value != '' && $setting_value != $default_spacing[$node->type][$property_name_singular][$media_size]) {
								\FLBuilderCSS::rule(array(
									'selector'					=> $selector,
									'media'						=> $media_size,
									'props'						=> array(
										$property_name_singular . '-' . $direction 	=> 'var(--wp--preset--spacing--' . $setting_value . ')'
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

\FLBuilderCSS::render();
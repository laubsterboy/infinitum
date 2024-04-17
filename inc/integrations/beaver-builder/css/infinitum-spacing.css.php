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
			$node_id = '.fl-node-' . $node->node;

			$selector = $node_id . ' > ';
			
			if ($node->type === 'row') {
				$selector .= '.fl-row-content-wrap';
			} else if ($node->type === 'column') {
				$selector .= '.fl-col-content';
			} else if ($node->type === 'module') {
				$selector .= '.fl-module-content';
			}

			foreach ($directions as $direction) {
				foreach ($media_sizes as $media_size) {
					foreach ($properties as $property_name_singular => $property_name_plural) {
						$setting_name = 'infinitum_advanced_' . $property_name_plural . '_' . $direction;

						if ($media_size !== 'default') {
							$setting_name .= '_' . $media_size;
						}

						if (isset($node->settings->{$setting_name})) {
							$setting_value = $node->settings->{$setting_name};

							if ($setting_value != '' && $setting_value != $default_spacing[$node->type][$property_name_singular][$media_size] && $setting_value != 'inherit') {
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
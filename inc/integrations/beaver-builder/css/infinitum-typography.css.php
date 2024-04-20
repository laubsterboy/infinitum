<?php

$defaults = array(
	'font_size' => '0',
	'line_height' => '0'
);
$media_sizes = array('default', 'large', 'medium', 'responsive');

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

				if ($node->name === 'Button' || $node->name === 'Button Group') {
					$selector .= ' a.fl-button';
					$selector .= ', ' . $selector . ':visited';
				} else if ($node->name === 'Heading') {
					$selector .= ' > .fl-heading';
				}
			}

			if (!isset($node->settings->infinitum_typography)) continue;

			foreach ($media_sizes as $media_size) {
				$setting_name = 'infinitum_typography';

				if ($media_size !== 'default') {
					$setting_name .= '_' . $media_size;
				}

				if (isset($node->settings->{$setting_name}) && is_array($node->settings->{$setting_name})) {
					$node_typography_settings = $node->settings->{$setting_name};

					// Font Size
					if (!empty($node_typography_settings['font_size']) && $node_typography_settings['font_size'] !== $defaults['font_size']) {
						\FLBuilderCSS::rule(array(
							'selector'		=> $selector,
							'media'			=> $media_size,
							'props'			=> array(
								'font-size'		=> 'var(--wp--preset--font-size--' . $node_typography_settings['font_size'] . ')',
								'line-height'	=> 'var(--wp--custom--infinitum--typography--line-height)' // Must be set alongside the font-size to force a recalculation of the line-height variable
							)
						));
					}

					// Line Height
					if (!empty($node_typography_settings['line_height']) && $node_typography_settings['line_height'] !== $defaults['line_height']) {
						\FLBuilderCSS::rule(array(
							'selector'		=> $selector,
							'media'			=> $media_size,
							'props'			=> array(
								'line-height'	=> 'var(--wp--custom--infinitum--typography--' . $node_typography_settings['line_height'] . '-line-height)'
							)
						));
					}
				}
			}
		}
	}
}

\FLBuilderCSS::render();
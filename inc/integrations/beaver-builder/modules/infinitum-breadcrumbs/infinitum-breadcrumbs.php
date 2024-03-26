<?php

/**
 * @class Infinitum_Breadcrumbs_Module
 * 
 * @since 0.0.1
 */

namespace infinitum\inc\integrations\beaver_builder\modules\infinitum_breadcrumbs;

class Infinitum_Breadcrumbs_Module extends \FLBuilderModule {
	protected readonly \infinitum\inc\Theme $theme;

	public function __construct() {
		$this->theme = \infinitum\inc\Theme::get_instance();

		parent::__construct(array(
			'name'				=> __('Breadcrumbs', $this->theme->get_text_domain()),
			'description'		=> __('Show the breadcrumbs for the current post or page.', $this->theme->get_text_domain()),
			'category'			=> __('Theme', $this->theme->get_text_domain()),
			'dir'				=> $this->theme->get_dir() . 'inc/theme/integrations/beaver-builder/modules/infinitum-breadcrumbs/',
			'url'				=> $this->theme->get_uri() . 'inc/theme/integrations/beaver-builder/modules/infinitum-breadcrumbs/',
			'partial_refresh'	=> true,
			'include_wrapper'	=> false
		));
	}



	public function render() {
		return $this->theme->breadcrumbs->render_breadcrumbs($this->settings->separator);
	}



	public static function register($theme = null) {
		if (is_null($theme)) return false;

		\FLBuilder::register_module(__NAMESPACE__ . '\Infinitum_Breadcrumbs_Module', array(
			'general'		=> array(
				'title'			=> __('General', $theme->get_text_domain()),
				'sections'		=> array(
					'content'		=> array(
						'title'			=> __('Content', $theme->get_text_domain()),
						'fields'			=> array(
							'separator'		=> array(
								'type'			=> 'text',
								'label'			=> __('Separator', $theme->get_text_domain()),
								'default'		=> $theme->breadcrumbs->defaults['separator']
							)
						)
					)
				)
			)
		));
	}
}
<?php

/**
 * Infinitum Theme
 * 
 * @since 0.0.1
 */

namespace infinitum\inc;

class Theme {
    /**
     * Instance
     * 
     * @since 0.0.1
     * @access protected
     * @var Theme $instance
     */
    protected static $instance = null;

	/**
	 * Text Domain
	 * 
	 * @since 0.0.1
	 * @access protected
	 * @var string $text_domain
	 */
	protected $text_domain = 'infinitum';

	/**
	 * Version
	 * 
	 * @since 0.0.1
	 * @access protected
	 * @var string $version
	 */
	protected $version = '0.0.0infinitum';

	/**
	 * Additional Featured Image
	 * 
	 * @since 0.0.1
	 * @access protected
	 * @var Additional_Featured_Image $additional_featured_image
	 */
	protected $additional_featured_image = null;
	
	/**
	 * Drawers
	 * 
	 * @since 0.0.1
	 * @access protected
	 * @var Drawers $drawers
	 */
	protected $drawers = null;

	/**
	 * Dir
	 * 
	 * @since 0.0.1
	 * @access protected
	 * @var string $dir
	 */
	protected $dir = '';

	/**
	 * Current singular post ID
	 * 
	 * @since 0.0.1
	 * @access protected
	 * @var int $current_singular_post_id
	 */
	protected $current_singular_post_id = 0;

	/**
	 * URI
	 * 
	 * @since 0.0.1
	 * @access protected
	 * @var string $uri
	 */
	protected $uri = '';

	/**
	 * The Theme construct method
	 * 
	 * @since 0.0.1
	 */
    protected function __construct() {
		$this->dir = get_template_directory();
		$this->uri = get_template_directory_uri();

		// Load dependencies
		$this->load();

		$this->additional_featured_image = new theme\additional_featured_image\Additional_Featured_Image(null, $this->dir . '/inc/theme/additional-featured-image/', $this->uri . '/inc/theme/additional-featured-image/');
		$this->drawers = new theme\drawers\Drawers($this->dir . '/inc/theme/drawers/', $this->uri . '/inc/theme/drawers/');

        $this->set_hooks();
    }



	protected function add_featured_images_support() {
		$post_types = get_post_types(array('public' => true));
		
		foreach ($post_types as $post_type_slug => $post_type_label) {
			if (!post_type_supports($post_type_slug, 'thumbnail')) {
				unset($post_types[$post_type_slug]);
			}
		}

		$this->additional_featured_image->register(array(
			'id' => 'header-image',
			'title' => 'Header Image',
			'screen' => $post_types,
			'context' => 'side',
			'priority' => 'default',
			'callback_args' => null
		));
	}



	/**
	 * Enqueues scripts and styles on the front-end
	 * 
	 * @since 0.0.1
	 * 
	 * @return void
	 */
	protected function enqueue_scripts_styles() {
		
	}



	/**
	 * Enqueues scripts and styles in the Editor iframe and the front-end
	 * 
	 * @since 0.0.1
	 * 
	 * @return void
	 */
	protected function enqueue_block_assets() {
		wp_enqueue_style('infinitum');
    }



	/**
	 * Enqueues scripts and styles in the editor (not in the iframe)
	 * 
	 * @since 0.0.1
	 * 
	 * @return void
	 */
    protected function enqueue_block_editor_assets() {
		
    }



    protected function enqueue_block_styles() {
        // Array of block slugs
        $block_styles = [];
    
        foreach ($block_styles as $block_slug) {
            $args = [
                'handle' => 'infinitum-' . $block_slug,
                'src' => get_theme_file_uri('assets/css/blocks/' . $block_slug . '.css'),
                'path' => get_theme_file_path('assets/css/blocks/' . $block_slug . '.css')
            ];
    
            wp_enqueue_block_style('core/' . $block_slug, $args);
        }
    }



	protected function get_post_header_image_id($thumbnail_id, $post) {
		$header_image_attachment_id = $this->additional_featured_image->get_attachment_id($post, 'header-image');

		if ((is_singular() && is_a($post, '\WP_Post') && $post->ID == $this->current_singular_post_id) && !empty($header_image_attachment_id) && is_numeric($header_image_attachment_id)) {
			$thumbnail_id = $header_image_attachment_id;
		}

		return $thumbnail_id;
	}



	/**
	 * Get a custom setting from the theme.json file
	 * 
	 * @since 0.0.1
	 * 
	 * @param string $setting	The custom setting to retrieve
	 * @return mixed
	 */
	public function get_theme_setting(string $setting) {
		$theme_settings = $this->get_theme_settings();
		$value = false;

		if (!empty($theme_settings[$setting])) {
			$value = $theme_settings[$setting];
		}

		// If $value is numeric convert from string to float or int
		if (is_numeric($value)) {
			if (stripos($value, '.') !== false) {
				$value = floatval($value);
			} else {
				$value = intval($value);
			}
		}

		return $value;
	}



	/**
	 * Get custom settings from the theme.json file
	 * 
	 * @since 0.0.1
	 * 
	 * @return array
	 */
	public function get_theme_settings() {
		$global_settings = wp_get_global_settings();
		$theme_settings = array();

		if (!empty($global_settings['custom']['infinitum'])) {
			$theme_settings = $global_settings['custom']['infinitum'];
		}

		return $theme_settings;
	}



    public static function instance() {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }



	protected function load() {
		require_once get_theme_file_path('inc/theme/additional-featured-image/additional-featured-image.php');
		require_once get_theme_file_path('inc/theme/drawers/drawers.php');
	}



	/**
	 * Registers block pattern categories
	 * 
	 * @since 0.0.1
	 */
	protected function register_block_pattern_categories() {
		register_block_pattern_category(
			'infinitum',
			array(
				'label' => __('Infinitum', $this->text_domain)
			)
		);
	}



	protected function register_scripts_styles() {
		// Scripts
		wp_register_script('infinitum-modal-js', get_template_directory_uri() . '/assets/js/modal.js', array(), $this->version, true);

		// Styles
		wp_register_style('infinitum-modal-css', get_template_directory_uri() . '/assets/css/modal.css', array(), $this->version);
        wp_register_style('infinitum', get_template_directory_uri() . '/style.css', array(), $this->version);
	}



	/*
	// Likely don't need this since it's being accomplished by filtering the featured image ID - this was needed to fix the issue of the "pattern" no longer being rendered if a template is edited in the Editor (such as single.html)
	protected function render_block_pattern_post_header_image($block_content, $parsed_block, $block) {
        if ($parsed_block['attrs']['slug'] === 'infinitum/post-header-image') {
            $header_image = $this->additional_featured_image->get_image(get_the_ID(), 'header-image', 'post-thumbnail');

            if (!empty($header_image)) {
				$blocks = parse_blocks('<!-- wp:image {"align":"wide","className":"infinitum-post-header-image wp-block-post-featured-image","sizeSlug":"post-thumbnail","linkDestination":"none"} --><figure class="wp-block-image alignwide size-post-thumbnail infinitum-post-header-image">' . $header_image . '</figure><!-- /wp:image -->');
				$block_content = '';

				foreach ($blocks as $block) {
					$block_content .= render_block($block);
				}
            }
        }

        return $block_content;
    }
	*/



    protected function set_hooks() {
		// WP Init
		add_action('init', array($this, 'wp_hook_init'));

		// Image Size Names
		add_filter('image_size_names_choose', array($this, 'wp_hook_image_size_names_choose'));

		// Post Thumbnail ID
		add_filter('post_thumbnail_id', array($this, 'wp_hook_post_thumbnail_id'), 10, 2);

		// WP After Setup Theme
        add_action('after_setup_theme', array($this, 'wp_hook_after_setup_theme'));

		// Theme Activation / Deactivation
        add_action('after_switch_theme', array($this, 'wp_hook_after_switch_theme'), 10, 2);
		add_action('switch_theme', array($this, 'wp_hook_switch_theme'), 10, 3);

		// Block Assets (front-end and back-end)
		add_action('enqueue_block_assets', array($this, 'wp_hook_enqueue_block_assets'));

		// Block Editor Assets
        add_action('enqueue_block_editor_assets', array($this, 'wp_hook_enqueue_block_editor_assets'));

		// Block Patterns Filter
		add_filter('render_block_core/pattern', array($this, 'wp_hook_render_block_core__pattern'), 10, 3);

		// Enqueue Scripts and Styles (front-end)
		add_action('wp_enqueue_scripts', array($this, 'wp_hook_wp_enqueue_scripts'));

		// WP
		add_action('wp', array($this, 'wp_hook_wp'));
    }



	protected function set_image_size_names($size_names) {
		$full = $size_names['full'];

		unset($size_names['full']);

		$size_names['infinitum-extra-large'] = __('Extra Large', $this->text_domain);
		$size_names['full'] = $full;

		return $size_names;
	}



	protected function set_image_sizes() {
		$content_width = $this->get_theme_setting('contentWidth');
		$featured_image_size_ratio = $this->get_theme_setting('featuredImageSizeRatio');

		// Set Featured Image size
		set_post_thumbnail_size(round($content_width * 2), round($content_width * 2 * $featured_image_size_ratio), array('center', 'center'));

		// Add Image Size(s)
		add_image_size('infinitum-extra-large', round($content_width * 2), 9999, false);
	}



	/**
	 * Set the current singular post ID right after the main query. This can be used
	 * to distinguish between the main query and sub queries such as the Query Block
	 * 
	 * @since 0.0.1
	 * 
	 * @return void
	 */
	protected function set_post_id() {
		$this->current_singular_post_id = get_the_ID();
	}



    public function theme_activation($old_theme_name = null, $old_theme = null) {
		$content_width = $this->get_theme_setting('contentWidth');
		$content_width_wide_ratio = $this->get_theme_setting('contentWidthWideRatio');

		// Set Infinitum version
		update_option('infinitum_version', $this->version);

		// Set Image sizes
		update_option('thumbnail_size_w', strval(round($content_width * 0.25)));
		update_option('thumbnail_size_h', strval(round($content_width * 0.25)));
		update_option('medium_size_w', strval(round($content_width * 0.5)));
		update_option('medium_size_h', strval(round($content_width * 0.5)));
		update_option('large_size_w', strval(round($content_width * $content_width_wide_ratio)));
		update_option('large_size_h', strval(round($content_width * $content_width_wide_ratio)));
	}



    public function theme_deactivation($new_name, $new_theme, $old_theme) {
		delete_option('infinitum_version');
	}



    public function wp_hook_after_setup_theme() {
        $this->enqueue_block_styles();
    }



    public function wp_hook_after_switch_theme($old_name, $old_theme) {
        $this->theme_activation($old_name, $old_theme);
    }



	public function wp_hook_enqueue_block_assets() {
        $this->enqueue_block_assets();
    }



    public function wp_hook_enqueue_block_editor_assets() {
        $this->enqueue_block_editor_assets();
    }



	public function wp_hook_image_size_names_choose($size_names) {
		$size_names = $this->set_image_size_names($size_names);

		return $size_names;
	}



    public function wp_hook_init() {
		// Featured Images
		$this->add_featured_images_support();

		// Image Sizes
		$this->set_image_sizes();

		// Register Block Pattern Categories
		$this->register_block_pattern_categories();
		
		// Register Scripts and Styles
		$this->register_scripts_styles();
    }



	public function wp_hook_post_thumbnail_id($thumbnail_id, $post) {
		$thumbnail_id = $this->get_post_header_image_id($thumbnail_id, $post);

		return $thumbnail_id;
	}



	public function wp_hook_render_block_core__pattern($block_content, $parsed_block, $block) {
		//$block_content = $this->render_block_pattern_post_header_image($block_content, $parsed_block, $block);

		return $block_content;
	}



    public function wp_hook_switch_theme($new_name, $new_theme, $old_theme) {
        $this->theme_deactivation($new_name, $new_theme, $old_theme);
    }



    public function wp_hook_wp_enqueue_scripts() {
        $this->enqueue_scripts_styles();
    }



	public function wp_hook_wp() {
		$this->set_post_id();
	}
}
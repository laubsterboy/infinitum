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
	 * Additional Featured Image
	 * 
	 * @since 0.0.1
	 * @access public
	 * @var Additional_Featured_Image $additional_featured_image
	 */
	public readonly theme\additional_featured_image\Additional_Featured_Image $additional_featured_image;

	/**
	 * Breadcrumbs
	 * 
	 * @since 0.0.1
	 * @access public
	 * @var Breadcrumbs $breadcrumbs
	 */
	public readonly theme\breadcrumbs\Breadcrumbs $breadcrumbs;
	
	/**
	 * Drawers
	 * 
	 * @since 0.0.1
	 * @access public
	 * @var Drawers $drawers
	 */
	public readonly theme\drawers\Drawers $drawers;

	/**
	 * Dir
	 * 
	 * @since 0.0.1
	 * @access public
	 * @var string $dir
	 */
	public readonly string $dir;

	/**
	 * Current singular post ID
	 * 
	 * @since 0.0.1
	 * @access protected
	 * @var int $current_singular_post_id
	 */
	protected $current_singular_post_id = 0;

	/**
	 * Integrations
	 * 
	 * @since 0.0.1
	 * @access public
	 * @var Integrations $integrations
	 */
	public readonly integrations\Integrations $integrations;

	/**
	 * Text Domain
	 * 
	 * @since 0.0.1
	 * @access public
	 * @var string $textdomain
	 */
	public readonly string $textdomain;

	/**
	 * URI
	 * 
	 * @since 0.0.1
	 * @access public
	 * @var string $uri
	 */
	public readonly string $uri;

	/**
	 * Updator
	 * 
	 * @since 0.0.1
	 * @access protected
	 * @var EDD_Theme_Updater_Admin $updater
	 */
	protected $updater;

	/**
	 * Version
	 * 
	 * @since 0.0.1
	 * @access public
	 * @var string $version
	 */
	public readonly string $version;

	/**
	 * The Theme construct method
	 * 
	 * @since 0.0.1
	 */
    protected function __construct() {
		$this->textdomain = 'infinitum';
		$this->version = '0.0.0infinitum';
		$this->dir = trailingslashit(get_template_directory());
		$this->uri = trailingslashit(get_template_directory_uri());

		// Load dependencies
		$this->load();

		// Initiate core theme features
		$this->additional_featured_image = new theme\additional_featured_image\Additional_Featured_Image(null, $this->dir . 'inc/theme/additional-featured-image/', $this->uri . 'inc/theme/additional-featured-image/');
		$this->breadcrumbs = new theme\breadcrumbs\Breadcrumbs($this->dir . 'inc/theme/breadcrumbs/', $this->uri . 'inc/theme/breadcrumbs/');
		$this->drawers = new theme\drawers\Drawers($this->dir . 'inc/theme/drawers/', $this->uri . 'inc/theme/drawers/');

		// Initiate 3rd party integrations
		$this->integrations = new integrations\Integrations($this, $this->dir . 'inc/integrations/', $this->uri . 'inc/integrations/');

		// Set hooks
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
	 * Block Binding Callback for "copyright"
	 * 
	 * This is used so the copyright symbol and date can be dynamically updated to reflect the current year
	 * and all of the text can be internationalized.
	 * 
	 * @since 0.0.1
	 * 
	 * @param array $source_args
	 * @param WP_Block $block_instance
	 * @param string $attribute_name
	 * @return string
	 */
	public function block_binding_callback_copyright(array $source_args, \WP_Block $block_instance, string $attribute_name): string {
		$copyright_holder = apply_filters('infinitum_block_binding_copyright_holder', get_bloginfo('name'));
		$copyright = __('Copyright &copy; ' . date('Y') . ' ' . $copyright_holder, $this->get_textdomain());

		return apply_filters('infinitum_block_binding_copyright', $copyright);
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

		if (is_child_theme()) {
			wp_enqueue_style('infinitum-child-theme');
		}
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



	public function get_dir() {
		return $this->dir;
	}



	public static function get_instance() {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }



	protected function get_post_header_image_id($thumbnail_id, $post) {
		$header_image_attachment_id = $this->additional_featured_image->get_attachment_id($post, 'header-image');

		if ((is_singular() && is_a($post, '\WP_Post') && $post->ID == $this->current_singular_post_id) && !empty($header_image_attachment_id) && is_numeric($header_image_attachment_id)) {
			$thumbnail_id = $header_image_attachment_id;
		}

		return $thumbnail_id;
	}



	public function get_textdomain() {
		return $this->textdomain;
	}



	/**
	 * Get the fonts defined in theme.json
	 * 
	 * @since 0.0.1
	 * 
	 * @return array
	 */
	public function get_theme_fonts(): array {
		$fonts = \WP_Font_Face_Resolver::get_fonts_from_theme_json();

		return $fonts;
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
		$global_settings = wp_get_global_settings(array(), array('origin' => 'base'));
		$theme_settings = array();

		if (!empty($global_settings['custom']['infinitum'])) {
			$theme_settings = $global_settings['custom']['infinitum'];
		}

		return $theme_settings;
	}



	protected function load() {
		// Load Interfaces
		require_once get_theme_file_path('inc/interfaces/addon.php');
		
		// Load Classes
		require_once get_theme_file_path('inc/classes/addon.php');

		// Load Theme Features
		require_once get_theme_file_path('inc/theme/additional-featured-image/additional-featured-image.php');
		require_once get_theme_file_path('inc/theme/breadcrumbs/breadcrumbs.php');
		require_once get_theme_file_path('inc/theme/drawers/drawers.php');

		// Load Theme Updater
		if (!class_exists('EDD_Theme_Updater_Admin')) {
			require_once get_theme_file_path('inc/theme/updater/theme-updater-admin.php');
		}

		// Load 3rd Party Integrations
		require_once get_theme_file_path('inc/integrations/integrations.php');
	}



	/**
	 * Loads the theme textdomain if it's available
	 * 
	 * @since 0.0.1
	 * 
	 * @return void
	 */
	protected function load_textdomain(): void {
		load_theme_textdomain($this->textdomain, $this->dir . 'languages');
	}



	public function get_uri() {
		return $this->uri;
	}



	/**
	 * Register block bindings sources (added in WP 6.5)
	 * 
	 * @since 0.0.1
	 * 
	 * @return void
	 */
	protected function register_block_bindings_sources(): void {
		register_block_bindings_source('infinitum/copyright', array(
			'label' => __('Copyright', $this->get_textdomain()),
			'get_value_callback' => array($this, 'block_binding_callback_copyright')
		));
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
				'label' => __('Infinitum', $this->textdomain)
			)
		);
	}



	protected function register_scripts_styles() {
		// Scripts
		wp_register_script('infinitum-modal-js', get_template_directory_uri() . '/assets/js/modal.js', array(), $this->version, true);

		// Styles
		wp_register_style('infinitum-modal-css', get_template_directory_uri() . '/assets/css/modal.css', array(), $this->version);
        wp_register_style('infinitum', get_template_directory_uri() . '/style.css', array(), $this->version);

		if (is_child_theme()) {
			$child_theme = wp_get_theme();

			wp_register_style('infinitum-child-theme', get_stylesheet_directory_uri() . '/style.css', array('infinitum'), $child_theme->get('Version'));
		}
	}



	/**
	 * Remove theme features that are unwanted
	 * 
	 * @since 0.0.1
	 * 
	 * @return void
	 */
	protected function remove_theme_support(): void {
		// Hide core patterns
		remove_theme_support('core-block-patterns');
		
		// Don't create starter content
		remove_theme_support('starter-content');
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
		$full = null;

		if (array_key_exists('full', $size_names)) {
			$full = $size_names['full'];

			unset($size_names['full']);
		}
		

		$size_names['infinitum-extra-large'] = __('Extra Large', $this->textdomain);

		if (!empty($full)) {
			$size_names['full'] = $full;
		}

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



	/**
	 * Set the page template (attribute on hierarchical post types) on a given post
	 * 
	 * @since 0.0.1
	 * 
	 * @param int|WP_Post	$post_id	The post to update the template
	 * @param string		$template	The new template name to use
	 * @param array			$allowed_previous_template	An array of previous values that should be overridden with the new template. Any current template values not found in this array will not allow the page template to be set. Set to an empty array for all values to be overridden.
	 * @return bool
	 */
	public function set_page_template($post_id, $template, $allowed_previous_template = array()): bool {
		$post = get_post($post_id);
		$post_type = get_post_type($post);

		// Check if $post is real
		if (!is_a($post, '\WP_Post')) return false;

		// Check if the post type supports templates (get post type and check if 'hierarchical' is true)
		if (!post_type_supports($post_type, 'page-attributes')) return false;

		// Check if the $template is a string and one of the currently available templates
		if (!is_string($template) || !array_key_exists($template, wp_get_theme()->get_page_templates($post, $post_type))) return false;
		
		$current_template = get_post_meta($post->ID, '_wp_page_template', true);

		// Check if the current template meta value should be changed
		if (empty($allowed_previous_template) || (is_array($allowed_previous_template) && in_array($current_template, $allowed_previous_template, true))) {
			if ($template == $current_template) return false;

			// Update the page template
			return update_post_meta($post->ID, '_wp_page_template', $template);
		}

		return false;
	}



	/**
	 * Configure the EDD Updater Admin
	 * 
	 * @since 0.0.1
	 * @return void
	 */
	protected function set_updater_config(): void {
		$theme = wp_get_theme('infinitum');
		$author = $theme->exists() ? $theme->get('Author') : '';

		$this->updater = new \EDD_Theme_Updater_Admin(array(
			'remote_api_url'	=> 'https://store.johnrussell.dev',
			'item_name'			=> 'Infinitum Theme',
			'theme_slug'		=> 'infinitum',
			'version'			=> $this->version,
			'author'			=> $author,
			'download_id'		=> '',
			'renew_url'			=> '',
			'beta'				=> false,
			'item_id'			=> ''
		),
		array(
			'theme-license'             => __('Theme License', $this->textdomain),
			'enter-key'                 => __('Enter your theme license key.', $this->textdomain),
			'license-key'               => __('License Key', $this->textdomain),
			'license-action'            => __('License Action', $this->textdomain),
			'deactivate-license'        => __('Deactivate License', $this->textdomain),
			'activate-license'          => __('Activate License', $this->textdomain),
			'status-unknown'            => __('License status is unknown.', $this->textdomain),
			'renew'                     => __('Renew?', $this->textdomain),
			'unlimited'                 => __('unlimited', $this->textdomain),
			'license-key-is-active'     => __('License key is active.', $this->textdomain),
			/* translators: the license expiration date */
			'expires%s'                 => __('Expires %s.', $this->textdomain),
			'expires-never'             => __('Lifetime License.', $this->textdomain),
			/* translators: 1. the number of sites activated 2. the total number of activations allowed. */
			'%1$s/%2$-sites'            => __('You have %1$s / %2$s sites activated.', $this->textdomain),
			'activation-limit'          => __('Your license key has reached its activation limit.', $this->textdomain),
			/* translators: the license expiration date */
			'license-key-expired-%s'    => __('License key expired %s.', $this->textdomain),
			'license-key-expired'       => __('License key has expired.', $this->textdomain),
			/* translators: the license expiration date */
			'license-expired-on'        => __('Your license key expired on %s.', $this->textdomain),
			'license-keys-do-not-match' => __('License keys do not match.', $this->textdomain),
			'license-is-inactive'       => __('License is inactive.', $this->textdomain),
			'license-key-is-disabled'   => __('License key is disabled.', $this->textdomain),
			'license-key-invalid'       => __('Invalid license.', $this->textdomain),
			'site-is-inactive'          => __('Site is inactive.', $this->textdomain),
			/* translators: the theme name */
			'item-mismatch'             => __('This appears to be an invalid license key for %s.', $this->textdomain),
			'license-status-unknown'    => __('License status is unknown.', $this->textdomain),
			'update-notice'             => __("Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update.", $this->textdomain),
			'error-generic'             => __('An error occurred, please try again.', $this->textdomain),
		));
	}



	/**
	 * Theme activation
	 * 
	 * @since 0.0.1
	 * 
	 * @see wp_hook_after_switch_theme
	 */
    public function theme_activation($old_theme_name = null, $old_theme = null): void {
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

		// Trigger Theme Features
		$this->additional_featured_image->theme_activation($old_theme_name, $old_theme);
		$this->breadcrumbs->theme_activation($old_theme_name, $old_theme);
		$this->drawers->theme_activation($old_theme_name, $old_theme);

		// Trigger Integrations
		$this->integrations->theme_activation($old_theme_name, $old_theme);
	}



	/**
	 * Theme deactivation
	 * 
	 * @since 0.0.1
	 * 
	 * @see wp_hook_switch_theme
	 */
    public function theme_deactivation($new_name, $new_theme, $old_theme): void {
		delete_option('infinitum_version');

		// Trigger Integrations
		$this->integrations->theme_deactivation($new_name, $new_theme, $old_theme);
	}



    public function wp_hook_after_setup_theme() {
		$this->load_textdomain();
		$this->remove_theme_support();
        $this->enqueue_block_styles();
		$this->set_updater_config();
    }



	/**
	 * WordPress action for after a theme has been switched and is called on the theme code being activated
	 * 
	 * @since 0.0.1
	 * 
	 * @param string	$old_name	The name of the old theme
	 * @param WP_Theme	$old_theme	The WP_Theme instance of the old theme, if the old theme is still installed
	 */
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



    public function wp_hook_init(): void {
		// Featured Images
		$this->add_featured_images_support();

		// Image Sizes
		$this->set_image_sizes();

		// Register Block Bindings Sources
		$this->register_block_bindings_sources();

		// Register Block Pattern Categories
		$this->register_block_pattern_categories();
		
		// Register Scripts and Styles
		$this->register_scripts_styles();
    }



	public function wp_hook_post_thumbnail_id($thumbnail_id, $post) {
		$thumbnail_id = $this->get_post_header_image_id($thumbnail_id, $post);

		return $thumbnail_id;
	}



	public function wp_hook_render_block_core__pattern($block_content, $parsed_block, $block): string {
		// TODO: Remove this method if it's not needed
		//$block_content = $this->render_block_pattern_post_header_image($block_content, $parsed_block, $block);

		return $block_content;
	}



	/**
	 * WordPress action for when a theme is switched, but runs on the theme code being deactivated NOT on the new/activated theme code
	 * 
	 * @since 0.0.1
	 * 
	 * @param string	$new_name	The name of the theme being activated
	 * @param WP_Theme	$new_theme	The WP_Theme object for the theme being activated
	 * @param WP_Theme	$old_theme	The WP_Theme object for the theme being deactivated
	 * @return void
	 */
    public function wp_hook_switch_theme($new_name, $new_theme, $old_theme): void {
        $this->theme_deactivation($new_name, $new_theme, $old_theme);
    }



    public function wp_hook_wp_enqueue_scripts(): void {
        $this->enqueue_scripts_styles();
    }



	/**
	 * WordPress action that is triggered after plugins are loaded, the theme is loaded, and the current post/page/url query is set, but before the page template is loaded
	 * 
	 * @since 0.0.1
	 * 
	 * @return void
	 */
	public function wp_hook_wp(): void {
		$this->set_post_id();
	}
}
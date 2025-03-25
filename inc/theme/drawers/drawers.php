<?php
/**
 * Drawers Manager
 *
 * @package Infinitum
 * @since 0.0.1
 */

namespace infinitum\inc\theme\drawers;

class Drawers extends \infinitum\inc\classes\Addon {
    protected $drawer_post_type_slug = 'drawer';
	protected readonly string $dir;
	protected readonly array $new_drawer_defaults;
	protected readonly string $uri;

    /**
	 * Drawers construct
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return void
	 */
	public function __construct($dir, $uri) {
		$this->dir = $dir;
		$this->uri = $uri;

		$this->new_drawer_defaults = array(
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_content' => '',
			'post_status' => 'publish',
			'post_type' => $this->get_post_type_slug()
		);

		$this->set_hooks();
	}



	/**
	 * Create starter content so the default header drawer will have something to show
	 * 
	 * @since 0.0.1
	 * 
	 * @return void
	 */
	protected function create_starter_content(): void {
		$header_posts = $this->get_posts(array(
			'name' => 'header'
		));

		// If no "header" post is found then create the starter drawer
		if (empty($header_posts)) {
			wp_insert_post(wp_parse_args(array(
				'post_content' => '<!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Search","width":100,"widthUnit":"%","buttonText":"Search"} /--><!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","orientation":"horizontal"}} /-->',
				'post_title' => 'Header'
			), $this->new_drawer_defaults));
		}
	}
	


	public function get_post_type_slug() {
		return $this->drawer_post_type_slug;
	}



	/**
	 * Gets the first published post created
	 * 
	 * @since 0.0.1
	 * 
	 * @return WP_Post|false
	 */
	public function get_first_post() {
		$post = false;
		$posts = $this->get_posts(array(
			'numberposts' => 1,
			'order' => 'ASC',
			'orderby' => 'date',
			'post_status' => 'publish'
		));

		if (is_array($posts) && !empty($posts)) {
			$post = $posts[0];
		}

		return $post;
	}



	/**
	 * Get drawer posts
	 * 
	 * @since 0.0.1
	 * 
	 * @param array	$args	The args to pass to 'get_posts'
	 * @return array		Array of post objects or post IDs
	 */
    public function get_posts($args = array()) {
        $default_args = array(
            'numberposts' => -1,
            'orderby' => 'title'
        );

        if (is_array($args)) {
            $args = wp_parse_args($args, $default_args);
        } else {
            $args = $default_args;
        }

		// Force the drawers post type
        $args['post_type'] = $this->get_post_type_slug();

        return get_posts($args);
    }



	/**
	 * Register the Drawer blocks
	 * 
	 * @since 0.0.1
	 */
	protected function register_blocks() {
		register_block_type($this->dir . 'blocks/drawer/');
	}



	/**
	 * Register the Drawer custom post type
	 * 
	 * @since 0.0.1
	 * 
	 * @return void
	 */
    protected function register_post_type() {
		register_post_type($this->get_post_type_slug(), apply_filters('infinitum_custom_post_type_args', array(
			'labels' => array('name' => 'Drawers',
				'singular_name' => 'Drawers',
				'add_new' => 'Add New',
				'add_new_item' => 'Add Drawers',
				'edit_item' => 'Edit Drawers',
				'new_item' => 'New Drawers',
				'view_item' => 'View Drawers',
				'search_items' => 'Search Drawers',
				'not_found' =>  'No Drawers found',
				'not_found_in_trash' => 'No Drawers found in trash',
				'parent_item_colon' => ''),
			'public' => true,
			'exclude_from_search' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => 'themes.php',
			'show_in_nav_menus' => false,
			'query_var' => true,
			'capability_type' => 'page',
			'hierarchical' => true,
			'show_in_rest' => true,
			'supports' => array('title', 'editor', 'revisions'),
			'taxonomies' => array(),
			'rewrite' => array('slug' => 'drawers', 'with_front' => false),
			'menu_position' => 1,
			'has_archive' => true), $this->get_post_type_slug())
		);
	}
	


	public function render_drawer($post_id = 0, $location = '') {
		ob_start();

		$post_id = apply_filters('infinitum_drawers_render_drawer_post_id', $post_id, $location);

		$content = ob_get_clean();

		// Check that the post ID is valid and that there is no content
		if ((empty($post_id) || !is_numeric($post_id)) && empty($content)) return;

		// Get the content of the post ID
		if (empty($content)) {
			$post = get_post($post_id);
			$content = get_the_content(null, false, $post);
		}

		if (!empty($content)) {
			echo '<div class="drawer-container drawer-' . $location . '-container">';
			do_action('infinitum_drawers_render_drawer_before', $post_id, $location);
			echo $content;
			do_action('infinitum_drawers_render_drawer_after', $post_id, $location);
			echo '</div>';
		}
	}



	public function render_drawer_block($block_attributes, $content) {
		$classes = array();
		$data_attributes = array();
		$drawer_id = 0;
		$drawer_post = null;
		$content = 'Select a drawer';
		$context = '';
		$wrapper = '<div %s %s data-wp-interactive="infinitum/drawer" %s>%s</div>';
		$interactivity_data_context = array('isOpen' => false);

		// Interactivity Data Context
		if (function_exists('wp_interactivity_data_wp_context')) {
			$context = wp_interactivity_data_wp_context($interactivity_data_context);
		}

		// Drawer ID
		if (!empty($block_attributes['drawerID']) && is_numeric($block_attributes['drawerID'])) {
			$drawer_id = intval($block_attributes['drawerID']);
		}

		// Drawer Post
		if ($drawer_id !== 0) {
			$drawer_post = get_post($drawer_id);
		} else {
			// Use the first Drawer post created, if another has not yet been selected
			$drawer_post = $this->get_first_post();
		}

		// Open / Close Buttons
		if ($block_attributes['nestCloseButton']) {
			$classes[] = 'wp-block-infinitum-drawer--nested-close-button';
		} else {
			$classes[] = 'wp-block-infinitum-drawer--sibling-close-button';
		}

		// Data attributes
		if (!empty($block_attributes['autoOffsetBottom'])) {
			$data_attributes[] = 'data-auto-offset-bottom="true"';
		}

		if (!empty($block_attributes['autoOffsetLeft'])) {
			$data_attributes[] = 'data-auto-offset-left="true"';
		}

		if (!empty($block_attributes['autoOffsetRight'])) {
			$data_attributes[] = 'data-auto-offset-right="true"';
		}

		if (!empty($block_attributes['autoOffsetTop'])) {
			$data_attributes[] = 'data-auto-offset-top="true"';
		}

		if (!empty($block_attributes['scrollToViewModal'])) {
			$data_attributes[] = 'data-scroll-to-view-modal="true"';
		}

		// Render Drawer if $post is a WP_Post object
		if (is_a($drawer_post, '\WP_Post') && !empty($drawer_post->ID)) {
			$content = $this->render_drawer_button_open($drawer_post, $block_attributes);
			if ($block_attributes['nestCloseButton'] !== true) {
				$content .= $this->render_drawer_button_close($drawer_post, $block_attributes);
			}
			$content .= $this->render_drawer_modal($drawer_post, $block_attributes);
		} else {
			$content = 'Drawer not found';
		}

		return sprintf($wrapper, get_block_wrapper_attributes(array('class' => implode(' ', $classes))), $context, implode(' ', $data_attributes), $content);
	}



	protected function render_drawer_button_close($drawer_post, $block_attributes) {
		$show_icon = !empty($block_attributes['showIconClose']) ? true : false;
		$show_label = !empty($block_attributes['labelClose']) ? true : false;
		$markup_icon__close = ($show_icon || (!$show_icon && !$show_label)) ? '<svg class="wp-block-infinitum-drawer__button-icon wp-block-infinitum-drawer__button-icon--close" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"></path></svg>' : '';
		$markup_aria_label = $show_label ? '' : ' aria-label="Open ' . $drawer_post->post_title . '"';
		$markup_label__close = $show_label ? '<span class="wp-block-infinitum-drawer__button-label wp-block-infinitum-drawer__button-label--close has-90-font-size">' . $block_attributes['labelClose'] . '</span>' : '';

		return  '<button class="wp-block-infinitum-drawer__button--close wp-block-infinitum-drawer__button"' . $markup_aria_label . ' data-wp-on--click="actions.close">' . $markup_icon__close . $markup_label__close . '</button>';
	}



	protected function render_drawer_button_open($drawer_post, $block_attributes) {
		$show_icon = !empty($block_attributes['showIconOpen']) ? true : false;
		$show_label = !empty($block_attributes['labelOpen']) ? true : false;
		$markup_icon__open = ($show_icon || (!$show_icon && !$show_label)) ? '<svg class="wp-block-infinitum-drawer__button-icon wp-block-infinitum-drawer__button-icon--open" width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="4" y="8" width="16" height="2"></rect><rect x="4" y="14" width="16" height="2"></rect></svg>' : '';
		$markup_aria_label = $show_label ? '' : ' aria-label="Open ' . $drawer_post->post_title . '"';
		$markup_label__open = $show_label ? '<span class="wp-block-infinitum-drawer__button-label wp-block-infinitum-drawer__button-label--open has-90-font-size">' . $block_attributes['labelOpen'] . '</span>' : '';

		return  '<button class="wp-block-infinitum-drawer__button--open wp-block-infinitum-drawer__button"' . $markup_aria_label . ' data-wp-on--click="actions.open">' . $markup_icon__open . $markup_label__open . '</button>';
	}



	protected function render_drawer_modal($drawer_post, $block_attributes) {
		/**
		 * @see get_block_wrapper_attributes
		 */
		$attributes = array(
			'class' => '',
			'id' => '',
			'style' => ''
		);
		$normalized_attributes = array();

		// Custom classes
		$attributes['class'] = 'wp-block-infinitum-drawer__modal has-global-padding is-layout-constrained ' . $attributes['class'];

		foreach ($attributes as $key => $value) {
			if (!empty($value)) {
				$normalized_attributes[] = $key . '="' . esc_attr($value) .'"';
			}
		}

		// Content
		$content = get_the_content(null, false, $drawer_post);

		if (has_blocks($content)) {
			$blocks = parse_blocks($content);
			$content = '';

			foreach ($blocks as $block) {
				$content .= render_block($block);
			}
		}

		$markup = '<div ' . implode(' ', $normalized_attributes) . ' data-wp-interactive="infinitumDrawer" aria-label="' . $drawer_post->post_title . ' Modal" aria-modal="true" role="dialog">';
		if ($block_attributes['nestCloseButton']) {
			$markup .= '<div class="wp-block-infinitum-drawer__button--close-container wp-block-group">' . $this->render_drawer_button_close($drawer_post, $block_attributes) . '</div>';
		}
		$markup .= $content;
		$markup .= '</div>';

		return $markup;
	}



	protected function set_hooks() {
		add_action('init', array($this, 'wp_hook_init'));
		add_filter('block_type_metadata_settings', array($this, 'wp_hook_block_type_metadata_settings'), 10, 2);
	}



	public function theme_activation($old_theme_name = null, $old_theme = null): void {
		$this->create_starter_content();
	}



	public function wp_hook_block_type_metadata_settings($settings, $metadata) {
		if ($metadata['name'] === 'infinitum/drawer') {
			$settings['render_callback'] = array($this, 'render_drawer_block');
		}

		return $settings;
	}



	public function wp_hook_init() {
		// Register the Drawer post type
		$this->register_post_type();

		// Register Blocks
		$this->register_blocks();
	}
}

?>

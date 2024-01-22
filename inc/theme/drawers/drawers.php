<?php
/**
 * Drawers Manager
 *
 * @package Infinitum
 * @since 0.0.1
 */

namespace infinitum\inc\theme\drawers;

class Drawers {
    protected $drawer_post_type_slug = 'drawer';
	protected $dir = '';
	protected $uri = '';

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

		$this->set_hooks();
	}



	/**
	 * Gets the post ID assigned to a given drawer location
	 * 
	 * @since 0.0.1
	 * @access public
	 * 
	 * @return boolean|integer
	 */
	public function get_drawer_id($location = '') {
		$post_id = false;

		if (!empty($location) && is_string($location)) {
			if ($location === 'header') {
				$post_id = 0; // TODO: Update this if need be, likely don't need this method at all
			}
		}

		return $post_id;
	}
	


	public function get_post_type_slug() {
		return $this->drawer_post_type_slug;
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
        $args['post_type'] = $this->drawer_post_type_slug;

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
		register_post_type($this->drawer_post_type_slug, apply_filters('infinitum_custom_post_type_args', array(
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
			'capability_type' => 'post',
			'hierarchical' => false,
			'show_in_rest' => true,
			'supports' => array('title', 'editor', 'revisions'),
			'taxonomies' => array('category', 'post_tag'),
			'rewrite' => array('slug' => 'drawers', 'with_front' => false),
			'menu_position' => 1,
			'has_archive' => true), $this->drawer_post_type_slug)
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
		$drawer_id = 0;
		$markup = '<div>Select a drawer</div>';

		// Drawer ID
		if (!empty($block_attributes['drawerID']) && is_numeric($block_attributes['drawerID'])) {
			$drawer_id = intval($block_attributes['drawerID']);
		}

		if (!empty($drawer_id)) {
			$drawer_post = get_post($drawer_id);

			if (is_a($drawer_post, '\WP_Post') && !empty($drawer_post->ID)) {
				$markup = '<div class="infinitum-block-drawer">';
				$markup .= $this->render_drawer_button_open($drawer_post, $block_attributes);
				$markup .= $this->render_drawer_modal($drawer_post, $block_attributes);
				$markup .= '</div>';
			}
		}

		return $markup;
	}



	protected function render_drawer_button_close($drawer_post, $block_attributes) {
		$show_icon = !empty($block_attributes['showIconClose']) ? true : false;
		$show_label = !empty($block_attributes['labelClose']) ? true : false;
		$markup_icon__close = ($show_icon || (!$show_icon && !$show_label)) ? '<svg class="infinitum-block-drawer__button-icon infinitum-block-drawer__button-icon--close" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"></path></svg>' : '';
		$markup_aria_label = $show_label ? '' : ' aria-label="Open ' . $drawer_post->post_title . '"';
		$markup_label__close = $show_label ? '<span class="infinitum-block-drawer__button-label infinitum-block-drawer__button-label--close">' . $block_attributes['labelClose'] . '</span>' : '';

		return  '<button class="infinitum-block-drawer__button--close infinitum-block-drawer__button"' . $markup_aria_label . '>' . $markup_icon__close . $markup_label__close . '</button>';
	}



	protected function render_drawer_button_open($drawer_post, $block_attributes) {
		$show_icon = !empty($block_attributes['showIconOpen']) ? true : false;
		$show_label = !empty($block_attributes['labelOpen']) ? true : false;
		$markup_icon__open = ($show_icon || (!$show_icon && !$show_label)) ? '<svg class="infinitum-block-drawer__button-icon infinitum-block-drawer__button-icon--open" width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="4" y="8" width="16" height="2"></rect><rect x="4" y="14" width="16" height="2"></rect></svg>' : '';
		$markup_aria_label = $show_label ? '' : ' aria-label="Open ' . $drawer_post->post_title . '"';
		$markup_label__open = $show_label ? '<span class="infinitum-block-drawer__button-label infinitum-block-drawer__button-label--open">' . $block_attributes['labelOpen'] . '</span>' : '';

		return  '<button class="infinitum-block-drawer__button--open infinitum-block-drawer__button"' . $markup_aria_label . '>' . $markup_icon__open . $markup_label__open . '</button>';
	}



	protected function render_drawer_modal($drawer_post, $block_attributes) {
		/**
		 * @see get_block_wrapper_attributes
		 */
		$attributes = wp_parse_args(\WP_Block_Supports::get_instance()->apply_block_supports(), array(
			'class' => '',
			'id' => '',
			'style' => ''
		));
		$normalized_attributes = array();

		// Custom classes
		$attributes['class'] = 'infinitum-block-drawer__modal wp-block-infinitum-drawer has-global-padding is-layout-constrained ' . $attributes['class'];

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

		$markup = '<aside ' . implode(' ', $normalized_attributes) . ' aria-label="' . $drawer_post->post_title . ' Modal" role="dialog">';
		$markup .= '<div class="infinitum-block-drawer__button--close-container wp-block-group">' . $this->render_drawer_button_close($drawer_post, $block_attributes) . '</div>';
		$markup .= $content;
		$markup .= '</aside>';

		return $markup;
	}



	protected function set_hooks() {
		add_action('init', array($this, 'wp_hook_init'));
		add_filter('block_type_metadata_settings', array($this, 'wp_hook_block_type_metadata_settings'), 10, 2);
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

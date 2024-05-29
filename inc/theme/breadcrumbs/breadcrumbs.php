<?php
/**
 * Breadcrumbs Manager
 *
 * @package Infinitum
 * @since 0.0.1
 */

namespace infinitum\inc\theme\breadcrumbs;

class Breadcrumbs extends \infinitum\inc\classes\Addon {
	public readonly array $defaults;
	public readonly string $dir;
	public readonly string $uri;

    /**
	 * Breadcrumbs construct
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return void
	 */
	public function __construct($dir, $uri) {
		$this->dir = $dir;
		$this->uri = $uri;

		$this->defaults = array(
			'separator' => '&raquo;'
		);

		$this->set_hooks();
	}



	/**
	 * Register the Breadcrumbs blocks
	 * 
	 * @since 0.0.1
	 */
	protected function register_blocks() {
		register_block_type($this->dir . 'blocks/breadcrumbs/');
	}



	/**
	 * Renders the breadcrumbs for the current post/page/archive/etc
	 * 
	 * @since 0.0.1
	 * 
	 * @param string	$separator	The separator to use between each breadcrumb
	 * @return string
	 */
	public function render_breadcrumbs($separator = ''): string {
	    global $wp, $post, $wp_locale;
		$post_type_object = get_post_type_object(get_post_type());
		$post_type_archive_link = false;
		$post_type_archive_link_node = array();
		$queried_object = get_queried_object();

	    $output = '';
		$output_root_node = array();
		$output_ancestor_node = array();
		$output_current_node = array();
		$output_schema = '';
		$output_schema_nodes = array(array('id' => esc_url(home_url('/')), 'name' => 'Home'));
		$output_schema_nodes_count = 0;
		$separator = empty($separator) ? $this->defaults['separator'] : $separator;
		$separator = ' ' . str_replace(' ', '', $separator) . ' ';

		// Shared nodes
		if (!empty($post_type_object) && $post_type_object->has_archive === true) {
			$post_type_archive_link = get_post_type_archive_link($post_type_object->name);

			if (!empty($post_type_archive_link)) {
				$post_type_archive_link_node = array('id' => $post_type_archive_link, 'name' => $post_type_object->labels->name);
			} else {
				$post_type_archive_page = get_page_by_path($post_type_object->rewrite['slug']);

				if (is_a($post_type_archive_page, 'WP_Post')) {
					$post_type_archive_link_node = array('id' => get_permalink($post_type_archive_page->ID), 'name' => get_the_title($post_type_archive_page->ID));
				}
			}
		}

	    if (!is_front_page() && !is_archive() && !is_search() && !is_404() && !(isset($_GET['paged']) && !empty($_GET['paged']))) {
	        if (is_page() || is_single()) {
				if (is_single()) {
					if (!empty($post_type_archive_link_node)) {
						// Schema
						$output_schema_nodes[] = $post_type_archive_link_node;
					}
				}

				if ($post->post_parent) {
					$ancestors = get_post_ancestors($post->ID);
					$ancestors = array_reverse($ancestors);
					foreach ($ancestors as $ancestor) {
						// Schema
						$output_schema_nodes[] = array('id' => get_permalink($ancestor), 'name' => get_the_title($ancestor));
					}
				}

				// Schema
				$output_schema_nodes[] = array('id' => get_permalink($post->ID), 'name' => get_the_title());
	        } else if (is_home()) {
				// Schema
				$output_schema_nodes[] = array('id' => get_permalink(get_option('page_for_posts')), 'name' => single_post_title('', false));
			}
	    } else if (is_archive()) {
			if (is_author()) {
				global $wp_rewrite;
				$user_id = get_query_var('author');
				$user = get_user_by('id', $user_id);
				$author_directory_page = get_page_by_path($wp_rewrite->author_base);
	
				if (!is_null($author_directory_page)) {
					// Schema
					$output_schema_nodes[] = array('id' => get_permalink($author_directory_page), 'name' => $author_directory_page->post_title);
				}
	
				// Schema
				$output_schema_nodes[] = array('id' => get_author_posts_url($user_id), 'name' => $user->display_name);
			} else if (is_category() || is_tag() || is_tax()) {
				$queried_object_taxonomy = get_taxonomy($queried_object->taxonomy);

				if (count($queried_object_taxonomy->object_type) === 1) {
					if (!empty($post_type_archive_link_node)) {
						// Schema
						$output_schema_nodes[] = $post_type_archive_link_node;
					}
				}
				// Schema
				$output_schema_nodes[] = array('id' => get_term_link($queried_object->term_id), 'name' => $queried_object->name);
	        } else if (is_date()) {
				if (is_day()) {
					// Schema
					$output_schema_nodes[] = array('id' => get_year_link(get_query_var('year')), 'name' => get_query_var('year'));
					$output_schema_nodes[] = array('id' => get_month_link(get_query_var('year'), get_query_var('monthnum')), 'name' => $wp_locale->get_month(get_query_var('monthnum')));
					$output_schema_nodes[] = array('id' => get_day_link(get_query_var('year'), get_query_var('monthnum'), get_query_var('day')), 'name' => 'Archive for ' . $wp_locale->get_weekday(get_the_time('w')) . ' the ' . get_the_time('jS'));
				} else if (is_month()) {
					// Schema
					$output_schema_nodes[] = array('id' => get_year_link(get_query_var('year')), 'name' => get_query_var('year'));
					$output_schema_nodes[] = array('id' => get_month_link(get_query_var('year'), get_query_var('monthnum')), 'name' => 'Archive for ' . get_the_time('F'));
				} else if (is_year()) {
					// Schema
					$output_schema_nodes[] = array('id' => get_year_link(get_query_var('year')), 'name' => 'Archive for ' . get_the_time('Y'));
				}
			} else {
				// Schema
				if (!empty($post_type_object)) {
					$output_schema_nodes[] = array('id' => get_post_type_archive_link($post_type_object->name), 'name' => $post_type_object->labels->name);
				}
			}
		} else if (is_search()) {
			// Schema
			$output_schema_nodes[] = array('id' => home_url(add_query_arg(array(),$wp->request) . '?s=' . get_search_query()), 'name' => 'Search Results');
		} else if (is_404()) {
			$output_schema_nodes[] = array('id' => '', 'name' => 'Not Found');
		} else if (isset($_GET['paged']) && !empty($_GET['paged'])) {
			// Schema
			$output_schema_nodes[] = array('id' => home_url(add_query_arg(array(), $wp->request)), 'name' => 'Blog Archives');
		}

		// Render the schema structured data
		if (!empty($output_schema_nodes)) {
			/**
			 * Allows the schema structured data of the breadcrumbs to be modified before it's output.
			 *
			 * @since 0.0.1
			 *
			 * @param array $output_schema_nodes	An array of arrays (ids and names).
			 */
			$output_schema_nodes = apply_filters('infinitum_render_breadcrumbs_schema_nodes', $output_schema_nodes);

			$output_schema_nodes_count = count($output_schema_nodes);

			$output_schema .= '<script type="application/ld+json">' . PHP_EOL;
			$output_schema .= '{' . PHP_EOL;
			$output_schema .= "\t" . '"@context": "https://schema.org",' . PHP_EOL;
			$output_schema .= "\t" . '"@type": "BreadcrumbList",' . PHP_EOL;
			$output_schema .= "\t" . '"itemListElement": [' . PHP_EOL;

			foreach ($output_schema_nodes as $key => $node) {
				if (empty($node['id']) || $key === $output_schema_nodes_count - 1) {
					$node_markup = '<span>' . $node['name'] . '</span>';
				} else {
					$node_markup = '<a href="' . $node['id'] . '" title="' . $node['id'] . '">' . $node['name'] . '</a>';
					$output_schema .= "\t" . '{' . PHP_EOL;
					$output_schema .= "\t\t" . '"@type": "ListItem",' . PHP_EOL;
					$output_schema .= "\t\t" . '"position": ' . ($key + 1) . ',' . PHP_EOL;
					$output_schema .= "\t\t" . '"item":' . PHP_EOL;
					$output_schema .= "\t\t" . '{' . PHP_EOL;
					$output_schema .= "\t\t\t" . '"@id": "' . $node['id'] . '",' . PHP_EOL;
					$output_schema .= "\t\t\t" . '"name": "' . $node['name'] . '"' . PHP_EOL;
					$output_schema .= "\t\t" . '}' . PHP_EOL;

					if ($key + 2 < $output_schema_nodes_count) {
						$output_schema .= "\t" . '},' . PHP_EOL;
					} else {
						$output_schema .= "\t" . '}' . PHP_EOL;
					}
				}

				// Set individual nodes for markup
				if ($key === 0) $output_root_node[] = $node_markup;
				if ($output_schema_nodes_count > 2 && $key > 0 && $key < ($output_schema_nodes_count - 1)) $output_ancestor_node[] = $node_markup;
				if ($key === $output_schema_nodes_count - 1) $output_current_node[] = $node_markup;
			}

			$output_schema .= ']}' . PHP_EOL;
			$output_schema .= '</script>';

			// Output no schema if there are no items to populate the itemListElement (likely this is the homepage)
			if ($output_schema_nodes_count === 1) $output_schema = '';

			$output .= $output_schema;
		}

		/**
		 * Allows the root node of the breadcrumbs to be modified before it's output.
		 *
		 * @since 0.0.1
		 *
		 * @param string $output_root_node
		 */
		$output_root_node = apply_filters('infinitum_render_breadcrumbs_root_node', implode('', $output_root_node));

		// Render the root node
		if (!empty($output_root_node)) {
			$output .= $output_root_node;
		}

		/**
		 * Allows the ancestor nodes of the breadcrumbs to be modified before it's output.
		 *
		 * @since 0.0.1
		 *
		 * @param array $output_ancestor_node	An array of strings (anchor tags).
		 */
		$output_ancestor_node = apply_filters('infinitum_render_breadcrumbs_ancestor_node', $output_ancestor_node);

		// Render the list of ancestor nodes
		if (!empty($output_ancestor_node)) {
			if (!empty($output_root_node)) $output .= $separator;

			if (count($output_ancestor_node) <= 2) {
				$output .= implode($separator, $output_ancestor_node);
			} else {
				$output .= '<ul class="breadcrumbs-menu"><li class="menu-item"><span> . . . </span><ul class="children">';
				$output_ancestor_node = array_reverse($output_ancestor_node);
				foreach ($output_ancestor_node as $node) {
					$output .= '<li class="menu-item">' . $node . '</li>';
				}
				$output .= '</ul></li></ul>';
			}
		}

		/**
		 * Allows the current node of the breadcrumbs to be modified before it's output.
		 *
		 * @since 0.0.1
		 *
		 * @param string $output_current_node
		 */
		$output_current_node = apply_filters('infinitum_render_breadcrumbs_current_node', implode('', $output_current_node));

		// Render the current post/page node (title)
		if (!empty($output_current_node)) {
			if (!empty($output_root_node) || !empty($output_ancestor_node)) $output .= $separator;

			$output .= $output_current_node;
		}

		// Wrap the output in the breadcrumbs container
		$output = '<nav class="breadcrumbs" aria-label="You are here:"><div class="breadcrumbs-items">' . $output . '</div></nav>';

		return $output;
	}



	public function render_breadcrumbs_block($block_attributes, $content) {
		$markup = '';
		$separator = $this->defaults['separator'];

		// Separator
		if (!empty($block_attributes['separator'])) {
			$separator = sanitize_text_field($block_attributes['separator']);
		}

		$markup = '<div ' . get_block_wrapper_attributes() . '>';
		$markup .= $this->render_breadcrumbs($separator);
		$markup .= '</div>';

		return $markup;
	}



	protected function set_hooks() {
		add_action('init', array($this, 'wp_hook_init'));
		add_filter('block_type_metadata_settings', array($this, 'wp_hook_block_type_metadata_settings'), 10, 2);
	}



	public function wp_hook_block_type_metadata_settings($settings, $metadata) {
		if ($metadata['name'] === 'infinitum/breadcrumbs') {
			$settings['render_callback'] = array($this, 'render_breadcrumbs_block');
		}

		return $settings;
	}



	public function wp_hook_init() {
		// Register Blocks
		$this->register_blocks();
	}
}

?>

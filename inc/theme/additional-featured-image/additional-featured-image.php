<?php
/**
 * Additional Featured Image
 * 
 * Basically duplicating this plugin - https://plugins.trac.wordpress.org/browser/multiple-post-thumbnails/trunk/multi-post-thumbnails.php
 *
 * @package Infinitum
 * @since 0.0.1
 */

namespace infinitum\inc\theme\additional_featured_image;

class Additional_Featured_Image {

	protected $additional_images = array();
	protected $current_post_type = null;
	protected $namespace = 'jrd';
	protected $dir = '';
	protected $uri = '';

	/**
	 * Construct
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return void
	 */
	public function __construct($namespace, $dir, $uri) {
		if (!empty($namespace) && is_string($namespace)) {
			$this->namespace = $namespace;
		}
		
		$this->dir = $dir;
		$this->uri = $uri;

		$this->set_hooks();
	}



	/**
	 * Adds the Additional Featured Image metaboxes, all that have been registered
	 * 
	 * @since 0.0.1
	 * @access public
	 * 
	 * @param string	$post_type	The current post type being edited
	 * @param WP_Post	$post		The WP_Post object of the current post being edited
	 * @return void
	 */
	public function add_meta_box($post_type, $post) {
		foreach ($this->additional_images as $image) {
			add_meta_box($image['id'], $image['title'], array($this, 'meta_box_callback'), $image['screen'], $image['context'], $image['priority'], $image['callback_args']);
		}
	}



	/**
	 * Removes an attachment from being a post featured image if the attachment
	 * has been deleted from the media library.
	 * 
	 * @since 0.0.1
	 * @access public
	 * 
	 * @param integer	$attachment_id	The ID of the attachment being deleted from the media library
	 * @return void
	 */
	public function delete_attachment($attachment_id) {
		global $wpdb;

		if (empty($this->additional_images)) return;

		$additional_images_meta_keys = $this->get_meta_keys();
		$in_keys = implode(',', array_fill(0, count($additional_images_meta_keys), '%s'));
		$query = $wpdb->prepare("DELETE FROM $wpdb->postmeta WHERE meta_key IN ($in_keys) AND meta_value = %d", array_merge($additional_images_meta_keys, array($attachment_id)));

		$wpdb->query($query);
	}



	/**
	 * Enqueues scripts and styles in the editor (not in the content iframe)
	 * 
	 * @since 0.0.1
	 * 
	 * @return void
	 */
    public function enqueue_block_editor_assets() {
		wp_enqueue_style($this->namespace . '-additional-featured-image', $this->uri . 'assets/css/additional-featured-image.css', array(), '0.0.1');
		wp_enqueue_script($this->namespace . '-additional-featured-image', $this->uri . 'assets/js/additional-featured-image.js', array('jquery'), '0.0.1');

		wp_add_inline_script($this->namespace . '-additional-featured-image', 'const AFI = ' . json_encode(array(
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'namespace' => $this->namespace
		)), 'before');
    }



	public function get_attachment_id($post, $metabox_id) {
		$post_id = 0;

		if (is_numeric($post)) {
			$post_id = intval($post);
		} else if (is_a($post, '\WP_Post')) {
			$post_id = $post->ID;
		} else {
			return false;
		}

		return get_post_meta($post_id, $this->get_meta_key($metabox_id), true);
	}



	/**
	 * Gets and returns an image/attachment info by using the post ID and metabox_id
	 * 
	 * @since 0.0.1
	 * @access public
	 * 
	 * @param integer	$post_id	The post ID that should contain the additional featured image
	 * @param string	$metabox_id	The metabox ID of the additional featured image. Used to determine which featured image to get
	 * @param string	$size		
	 */
	public function get_image($post_id, $metabox_id, $size = 'original', $icon = false, $attr = array()) {
		$attachmend_id = $this->get_attachment_id($post_id, $metabox_id);
		$image = wp_get_attachment_image($attachmend_id, $size, false, $attr);

		return $image;
	}



	/**
	 * Gets and returns an image/attachment URL by using the post ID and metabox_id
	 * 
	 * @since 0.0.1
	 * @access public
	 * 
	 * @param integer	$post_id	The post ID that should contain the additional featured image
	 * @param string	$metabox_id	The metabox ID of the additional featured image. Used to determine which featured image to get
	 * @param string	$size		The image size to get
	 * @return string
	 */
	public function get_image_url($post_id, $metabox_id, $size = 'original') {
		$attachment_id = $this->get_attachment_id($post_id, $metabox_id);
		$image = wp_get_attachment_image_src($attachment_id, $size);
		$url = '';

		if (!empty($image) && is_array($image)) {
			$url = $image[0];
		}

		return $url;
	}



	/**
	 * Gets the meta key (for post meta) from the metabox_id
	 * 
	 * @since 0.0.1
	 * @access public
	 * 
	 * @param string	$metabox_id	The metabox_id used to generate the meta key
	 * @return string
	 */
	public function get_meta_key($metabox_id) {
		return $this->namespace . '_afi_' . str_replace('-', '_', $metabox_id) . '_attachment_id';
	}



	/**
	 * Gets an array of all meta keys for all of the additional featured images registered
	 * 
	 * @since 0.0.1
	 * @access public
	 * 
	 * @return array
	 */
	public function get_meta_keys() {
		$metabox_ids = array_keys($this->additional_images);
		$additional_images_meta_keys = array();

		foreach ($metabox_ids as $metabox_id) {
			$additional_images_meta_keys[] = $this->get_meta_key($metabox_id);
		}

		return $additional_images_meta_keys;
	}



	/**
	 * Checks if the current editor being used is the block editor or the classic editor
	 * 
	 * @since 0.0.1
	 * @access protected
	 * 
	 * @return boolean
	 */
	protected function is_block_editor() {
		$is_block_editor = false;
		$current_post_type = get_post_type();

		if (!is_null($this->current_post_type)) {
			$current_post_type = $this->current_post_type;
		}

		if (function_exists('use_block_editor_for_post_type')) {
			$is_block_editor = use_block_editor_for_post_type($current_post_type);
		}

		return $is_block_editor;
	}



	/**
	 * Hides the additional featured image added meta values from custom fields
	 * 
	 * @since 0.0.1
	 * @access public
	 * 
	 * @param boolean	$protected	The boolean value of the meta_key
	 * @param string	$meta_key	The meta key in question
	 * @return boolean
	 */
	public function is_protected_meta($protected, $meta_key) {
		if (in_array($meta_key, $this->get_meta_keys())) {
			$protected = true;
		}

		return $protected;
	}



	/**
	 * The callback for each metabox to render the form content
	 * 
	 * @since 0.0.1
	 * @access public
	 * 
	 * @param WP_Post	$post		The post being edited
	 * @param array		$metabox	The registered metabox that is being rendered
	 * @return void
	 */
	public function meta_box_callback($post, $metabox) {
		echo $this->render_meta_box($post->ID, $metabox['id']);
	}
	


	/**
	 * Registers a new Additional Featured Image
	 * 
	 * Once registered the class automatically handles the metabox and saving
	 * meta data. The only thing needed to do is to render the featured image
	 * someplace using the $additional_featured_image->get_image_url method.
	 * 
	 * @since 0.0.1
	 * @access public
	 * 
	 * @param array		$args	The arguments to use to register a new featured image
	 * @return boolean
	 */
	public function register($args = array()) {
		if (empty($args) || !is_array($args)) return false;

		$args = wp_parse_args($args, array(
			'id' => '',
			'title' => '',
			'screen' => '',
			'context' => 'side',
			'priority' => 'default',
			'callback_args' => null
		));

		if ((empty($args['id']) || array_key_exists($args['id'], $this->additional_images)) || empty($args['title'])) return false;

		$this->additional_images[$args['id']] = $args;

		return true;
	}



	/**
	 * Renders the Additional Featured Image metabox
	 * 
	 * @since 0.0.1
	 * @access protected
	 * 
	 * @param integer	$post_id	The post ID of the post being edited
	 * @param string	$metabox_id	The metabox ID being rendered
	 * @param boolean	$wrap		Whether to wrap the metabox with a <div> wrapping element
	 * @param integer	$attachment_id	The attachment ID to use to render as the selected additional featured image, otherwise the post meta is used
	 * @return string
	 */
	protected function render_meta_box($post_id, $metabox_id, $wrap = true, $attachment_id = null) {
		$html = '';
		$attachment_id = !is_null($attachment_id) ? $attachment_id : $this->get_attachment_id($post_id, $metabox_id);
		$nonce = wp_create_nonce($this->namespace . '_additional_featured_image_set');
		$data_attributes = 'data-metabox-id="' . $metabox_id . '" data-nonce="' . $nonce . '" data-post-id="' . $post_id . '"';

		if ($wrap) $html .= '<div class="' . $this->namespace . '-additional-featured-image-meta-box" ' . $data_attributes . '>';
		
		if (empty($attachment_id)) {
			$html .= $this->render_meta_box_image_set();
			$html .= $this->render_meta_box_image_input($metabox_id, $attachment_id);
		} else {
			$image = wp_get_attachment_image_src($attachment_id, 'post-thumbnail');

			$html .= $this->render_meta_box_image_preview($image[0]);
			$html .= $this->render_meta_box_image_actions();
			$html .= $this->render_meta_box_image_input($metabox_id, $attachment_id);
		}

		if ($wrap) $html .= '</div>';

		return $html;
	}



	protected function render_meta_box_image_actions() {
		$html = '';
		$actions = '';

		$actions .= $this->render_meta_box_image_replace();
		$actions .= $this->render_meta_box_image_remove();

		if ($this->is_block_editor()) {
			$html = '<div class="components-flex ' . $this->namespace . '-additional-featured-image__actions">' . $actions . '</div>';
		} else {
			$html = $actions;
		}

		return $html;
	}



	/**
	 * Renders the meta box input element which is used to save the attachment ID
	 * for the given Additional Featured Image
	 * 
	 * @since 0.0.1
	 * @access protected
	 * 
	 * @param string	$metabox_id		The metabox ID being rendered
	 * @param integer	$attachmend_id	The attachment ID to be rendered
	 * @return string
	 */
	protected function render_meta_box_image_input($metabox_id, $attachment_id) {
		$html = '<input type="hidden" class="' . $this->namespace . '-additional-featured-image__input" name="' . esc_attr($this->get_meta_key($metabox_id)) . '" value="' . esc_attr($attachment_id) . '" />';

		return $html;
	}



	/**
	 * Renders the meta box preview element
	 * 
	 * @since 0.0.1
	 * @access protected
	 * 
	 * @param string	$image_url		The URL of the image being previewed
	 * @return string
	 */
	protected function render_meta_box_image_preview($image_url) {
		return '<button type="button" class="' . $this->namespace . '-additional-featured-image__preview" aria-label="Edit or update the image"><div class="components-responsive-wrapper"><img src="' . $image_url . '" alt="" /></div></button>';
	}



	/**
	 * Renders the meta box remove element
	 * 
	 * @since 0.0.1
	 * @access protected
	 * 
	 * @return string
	 */
	protected function render_meta_box_image_remove() {
		$html = '';
		$class = $this->namespace . '-additional-featured-image__remove';

		if ($this->is_block_editor()) {
			$html = '<button type="button" class="components-button editor-post-featured-image__action ' . $class . '">Remove</button>';
		} else {
			$html = '<a href="#" class="' . $class . '">Remove</a>';
		}

		return $html;
	}



	/**
	 * Renders the meta box replace element
	 * 
	 * @since 0.0.1
	 * @access protected
	 * 
	 * @return string
	 */
	protected function render_meta_box_image_replace() {
		$html = '';

		if ($this->is_block_editor()) {
			$html = '<button type="button" class="components-button editor-post-featured-image__action ' . $this->namespace . '-additional-featured-image__replace">Replace</button>';
		} else {
			$html = '<p class="howto">Click the image to edit or update</p>';
		}

		return $html;
	}



	/**
	 * Renders the meta box set (select image) element
	 * 
	 * @since 0.0.1
	 * @access protected
	 * 
	 * @return string
	 */
	protected function render_meta_box_image_set() {
		$html = '';
		$class = $this->namespace . '-additional-featured-image__set';

		if ($this->is_block_editor()) {
			$html = '<button type="button" class="' . $class . '">Set image</button>';
		} else {
			$html = '<a href="#" class="' . $class . '">Set image</a>';
		}

		return $html;
	}



	/**
	 * Saves the Additional Featured Image(s) when the post is saved
	 * 
	 * @since 0.0.1
	 * @access public
	 * 
	 * @param integer	$post_id	The post ID being saved
	 * @return void
	 */
	public function save_post($post_id) {
		foreach (array_keys($this->additional_images) as $metabox_id) {
			$meta_key = $this->get_meta_key($metabox_id);
			if (!isset($_POST[$meta_key])) continue;

			$attachment_id = intval($_POST[$meta_key]);

			// Update the post meta or delete it depending on what value was passed
			if (!empty(wp_get_attachment_image($attachment_id))) {
				update_post_meta($post_id, $meta_key, $attachment_id);
			} else if (empty($attachment_id)) {
				delete_post_meta($post_id, $meta_key);
			}
		}
	}



	/**
	 * Sets the necessary WordPress hooks
	 * 
	 * @since 0.0.1
	 * @access protected
	 * 
	 * @return void
	 */
	protected function set_hooks() {
		add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
		add_action('add_meta_boxes', array($this, 'add_meta_box'), 10, 2);
		add_action('wp_ajax_' . $this->namespace . '_additional_featured_image_update', array($this, 'update_meta_box_ajax'));
		add_action('delete_attachment', array($this, 'delete_attachment'));
		add_filter('is_protected_meta', array($this, 'is_protected_meta'), 20, 2);
		add_filter('save_post', array($this, 'save_post'));
	}



	/**
	 * Updates the Additional Featured Image meta box via AJAX
	 * 
	 * @since 0.0.1
	 * @access public
	 * 
	 * @return void
	 */
	public function update_meta_box_ajax() {
		check_ajax_referer($this->namespace . '_additional_featured_image_set');

		$attachment_id = intval($_POST['attachment_id']);
		$metabox_id = sanitize_text_field($_POST['metabox_id']);
		$post_id = intval($_POST['post_id']);
		$response = array(
			'error' => true,
			'html' => ''
		);

		if (!empty($metabox_id) && is_string($metabox_id) && array_key_exists($metabox_id, $this->additional_images) && is_int($attachment_id) && is_int($post_id)) {
			$this->current_post_type = get_post_type($post_id);

			$response['html'] = $this->render_meta_box($post_id, $metabox_id, false, $attachment_id);
			$response['error'] = false;
		}

		echo json_encode($response);

		wp_die();
	}
}
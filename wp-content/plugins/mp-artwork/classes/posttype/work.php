<?php
/*
  Class MP_Artwork_Work
 * 
 * add post type work
 */


class MP_Artwork_Works {

    private $prefix;
    public $postTypeName = 'Works';
    public $postTypeSlug = 'works';

    public function __construct($prefix) {
        $this->prefix = $prefix;

        $this->postTypeName = get_option($this->get_prefix().'post_type_name');
        $this->postTypeSlug = $this->posttype_name_sanitize_text(get_option($this->get_prefix().'post_type_slug'));
        if (empty($this->postTypeName)) {
            $this->postTypeName = __("Works", 'mp-artwork');
        }
        if (empty($this->postTypeSlug)) {
            $this->postTypeSlug = "works";
        }
        add_action('init', array($this, 'works_register'));
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('save_post', array($this, 'save'));
        register_activation_hook(__FILE__, array($this, 'plugin_activate'));
    }

    /**
     * Get prefix.
     *
     * @access public
     * @return sting
     */
    private function get_prefix() {
        return $this->prefix . '_';
    }

    public function plugin_activate() {
        $this->works_register();
        flush_rewrite_rules(true);
    }

    public function posttype_name_sanitize_text($txt) {
        $txt = strip_tags($txt, '');
        $txt = preg_replace("/[^a-zA-Z0-9-]+/", "", $txt);
        $txt = substr(strtolower($txt), 0, 19);
        return wp_kses_post(force_balance_tags($txt));
    }

    public function categories_register() {
        $labels_cat = array(
            'name' => _x('Categories', 'taxonomy general name', 'mp-artwork'),
            'singular_name' => _x('Category', 'taxonomy singular name', 'mp-artwork'),
            'search_items' => __('Search Categories', 'mp-artwork'),
            'popular_items' => __('Popular Categories', 'mp-artwork'),
            'all_items' => __('All Categories', 'mp-artwork'),
            'parent_item' => null,
            'parent_item_colon' => null,
            'edit_item' => __('Edit Category', 'mp-artwork'),
            'update_item' => __('Update Category', 'mp-artwork'),
            'add_new_item' => __('Add New Category', 'mp-artwork'),
            'new_item_name' => __('New Category Name', 'mp-artwork'),
            'separate_items_with_commas' => __('Separate categories with commas', 'mp-artwork'),
            'add_or_remove_items' => __('Add or remove categories', 'mp-artwork'),
            'choose_from_most_used' => __('Choose from the most used categories', 'mp-artwork'),
            'not_found' => __('No categories found.', 'mp-artwork'),
            'menu_name' => __('Categories', 'mp-artwork'),
        );

        $args_cat = array(
            'hierarchical' => false,
            'labels' => $labels_cat,
            'show_ui' => true,
            'show_admin_column' => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var' => true,
            'rewrite' => true,
        );

        register_taxonomy('category_' . $this->postTypeSlug, $this->postTypeSlug, $args_cat);
    }

    public function tags_register() {
        $labels_tag = array(
            'name' => _x('Tags', 'taxonomy general name', 'mp-artwork'),
            'singular_name' => _x('Tag', 'taxonomy singular name', 'mp-artwork'),
            'search_items' => __('Search Tags', 'mp-artwork'),
            'popular_items' => __('Popular Tags', 'mp-artwork'),
            'all_items' => __('All Tags', 'mp-artwork'),
            'parent_item' => null,
            'parent_item_colon' => null,
            'edit_item' => __('Edit Tag', 'mp-artwork'),
            'update_item' => __('Update Tag', 'mp-artwork'),
            'add_new_item' => __('Add New Tag', 'mp-artwork'),
            'new_item_name' => __('New Tag Name', 'mp-artwork'),
            'separate_items_with_commas' => __('Separate tags with commas', 'mp-artwork'),
            'add_or_remove_items' => __('Add or remove tags', 'mp-artwork'),
            'choose_from_most_used' => __('Choose from the most used tags', 'mp-artwork'),
            'not_found' => __('No tags found.', 'mp-artwork'),
            'menu_name' => __('Tags', 'mp-artwork'),
        );

        $args_tag = array(
            'hierarchical' => false,
            'labels' => $labels_tag,
            'show_ui' => true,
            'show_admin_column' => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var' => true,
            'rewrite' => true,
        );

        register_taxonomy('post_tag_' . $this->postTypeSlug, $this->postTypeSlug, $args_tag);
    }

    public function works_register() {
        $this->categories_register();
        flush_rewrite_rules(true);
        $this->tags_register();
        flush_rewrite_rules(true);
        $args = array(
            'label' => $this->postTypeName,
            'singular_label' => $this->postTypeName,
            'public' => true,
            'show_ui' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'rewrite' => true,
            'taxonomies' => array('category_' . $this->postTypeSlug, 'post_tag_' . $this->postTypeSlug),
            'supports' => array('title', 'editor', 'thumbnail', 'post-formats', 'comments')
        );

        register_post_type($this->postTypeSlug, $args);
    }

    /**
     * Adds the meta box container.
     */
    public function add_meta_box($post_type) {
        $post_types = array($this->postTypeSlug);     //limit meta box to certain post types
        if (in_array($post_type, $post_types)) {
            add_meta_box(
                    'work_meta_box_option'
                    , __('Options', 'mp-artwork')
                    , array($this, 'render_meta_box_options_work')
                    , $post_type
                    , 'advanced'
            );
        }
    }

    /**
     * Save the meta when the post is saved.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function save($post_id) {

        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */

        // Check if our nonce is set.
        if (!isset($_POST['work_meta_box_option_nonce']))
            return $post_id;

        $nonce = $_POST['work_meta_box_option_nonce'];

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($nonce, 'work_meta_box_option'))
            return $post_id;

        // If this is an autosave, our form has not been submitted,
        //     so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;

        // Check the user's permissions.
        if ('page' == $_POST['post_type']) {

            if (!current_user_can('edit_page', $post_id))
                return $post_id;
        } else {

            if (!current_user_can('edit_post', $post_id))
                return $post_id;
        }

        /* OK, its safe for us to save the data now. */

        $work_bg = ( isset($_POST['work_bg']) ? sanitize_html_class($_POST['work_bg']) : '' );
		$work_bg_color = ( isset($_POST['work_bg_color']) ? sanitize_text_field($_POST['work_bg_color']) : '' );

        update_post_meta($post_id, '_work_bg', $work_bg);
        update_post_meta($post_id, '_work_bg_color', $work_bg_color);
    }

    /**
     * Render Meta Box content.
     *
     * @param WP_Post $post The post object.
     */
    public function render_meta_box_options_work($post) {

        // Add an nonce field so we can check for it later.
        wp_nonce_field('work_meta_box_option', 'work_meta_box_option_nonce');

        // Use get_post_meta to retrieve an existing value from the database.

        $workbg = (get_post_meta($post->ID, '_work_bg', true)) ? get_post_meta($post->ID, '_work_bg', true) : "work-wrapper-light";
        $workbgcolor = get_post_meta($post->ID, '_work_bg_color', true);

        // Display the form, using the current value.
        ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="slide_layout"><?php _e('Text color on the Front Page:', 'mp-artwork'); ?></label>
                    </th> 
                    <td> 
                        <fieldset>
                            <label>
                                <input type="radio" name="work_bg" value="work-wrapper-light" <?php checked($workbg, 'work-wrapper-light'); ?> ><?php _e('Light text', 'mp-artwork'); ?>
                            </label><br>
                            <label>
                                <input type="radio" name="work_bg" value="work-wrapper-dark" <?php checked($workbg, 'work-wrapper-dark'); ?> ><?php _e('Dark text', 'mp-artwork'); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
				<tr>
                    <th scope="row">
                        <label for="work_bg_color"><?php _e('Alternative background color:', 'mp-artwork'); ?></label>
                    </th> 
                    <td> 
                        <fieldset>
                            <input type="text" name="work_bg_color" value="<?php echo $workbgcolor; ?>" class="mp-artwork-color-field">
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
    }

}



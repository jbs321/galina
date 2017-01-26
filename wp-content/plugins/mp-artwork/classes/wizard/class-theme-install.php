<?php
/**
 * Setup Wizard Class
 *
 * Takes new users through some basic steps to setup their store.
 *
 * @package WordPress
 * @subpackage Artwork
 * @since Artwork 1.1.0
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * MP_Artwork_Admin_Setup_Wizard class
 */
class MP_Artwork_Plugin_Admin_Setup_Wizard {

    private $prefix;

    /** @var string Currenct Step */
    private $step = '';

    /** @var array Steps for the setup wizard */
    private $steps = array();
    private $theme_name;

    /**
     * Hook in tabs.
     */
    public function __construct($prefix) {
        $this->prefix = $prefix;
        add_action('admin_menu', array($this, 'admin_menus'));
        $theme_info = wp_get_theme();
        $this->theme_name = $theme_info->get('Name');
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

    /**
     * Add admin menus/screens.
     */
    public function admin_menus() {
        add_theme_page(__('Theme Wizard', 'mp-artwork'), __('Theme Wizard', 'mp-artwork'), 'edit_theme_options', 'theme-setup', array($this, 'setup_wizard'));
        //call register settings function
        add_action('admin_init', array($this, 'register_post_type_name_settings'));
    }

    public function register_post_type_name_settings() {
        register_setting('theme-work-settings-group', $this->get_prefix() . 'post_type_name');
        register_setting('theme-work-settings-group', $this->get_prefix() . 'post_type_slug');
    }

    public function posttype_name_sanitize_text($txt) {
        $txt = strip_tags($txt, '');
        $txt = preg_replace("/[^a-zA-Z0-9-]+/", "", $txt);
        $txt = substr(strtolower($txt), 0, 19);
        return wp_kses_post(force_balance_tags($txt));
    }

    function post_type_name_page() {
        $theme_post_type_name = get_option($this->get_prefix() . 'post_type_name');
        $theme_post_type_slug = $this->posttype_name_sanitize_text(get_option($this->get_prefix() . 'post_type_slug'));
        if (empty($theme_post_type_name)) {
            $theme_post_type_name = __("Works", 'mp-artwork');
        }
        if (empty($theme_post_type_slug)) {
            $theme_post_type_slug = "works";
        }
        ?>
        <div class="wrap">
            <form method="post" action="options.php">
                <?php settings_fields('theme-work-settings-group'); ?>
                <?php do_settings_sections('theme-work-settings-group'); ?>
                <div class="form-group">
                    <input class="form-control" type="text" placeholder="Works" name="<?php echo $this->get_prefix(); ?>post_type_name" value="<?php echo esc_attr($theme_post_type_name); ?>" />
                </div>
                <br/>
                <div class="form-group">
                    <input class="form-control" type="text" placeholder="works"  name="<?php echo $this->get_prefix(); ?>post_type_slug" value="<?php echo esc_attr($theme_post_type_slug); ?>" />
                    <p class="description"><?php _e('The "slug" is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'mp-artwork'); ?></p>
                </div>
                <?php submit_button(); ?>               
            </form>
        </div>
        <?php
    }

    /**
     * Show the setup wizard
     */
    public function setup_wizard() {
        if (empty($_GET['page']) || 'theme-setup' !== $_GET['page']) {
            return;
        }
        $this->steps = array(
            'introduction' => array(
                'name' => __('Start', 'mp-artwork'),
                'view' => array($this, 'setup_introduction'),
                'handler' => ''
            ),
            'section' => array(
                'name' => __('Front Page Setup', 'mp-artwork'),
                'view' => array($this, 'setup_section'),
                'handler' => ''
            ),
            'customizer' => array(
                'name' => __('Customizer', 'mp-artwork'),
                'view' => array($this, 'setup_customizer'),
                'handler' => ''
            ),
            'templates' => array(
                'name' => __('Works Archive', 'mp-artwork'),
                'view' => array($this, 'setup_page_templates'),
                'handler' => ''
            ),
            'plugins' => array(
                'name' => __('Plugins', 'mp-artwork'),
                'view' => array($this, 'setup_plugins'),
                'handler' => ''
            ),
            'install_plugins' => array(
                'name' => __('Install Plugins', 'mp-artwork'),
                'view' => array($this, 'setup_install_plugins'),
                'handler' => ''
            ),
            'import_dummy' => array(
                'name' => __('Sample Content', 'mp-artwork'),
                'view' => array($this, 'setup_import'),
                'handler' => ''
            ),
            'pages' => array(
                'name' => __('Pages', 'mp-artwork'),
                'view' => array($this, 'setup_pages'),
                'handler' => ''
            ),
            'install_pages' => array(
                'name' => __('Install Pages', 'mp-artwork'),
                'view' => array($this, 'setup_install_pages'),
                'handler' => ''
            ),
            'post_type_name' => array(
                'name' => __('Post Type', 'mp-artwork'),
                'view' => array($this, 'setup_post_type_name'),
                'handler' => ''
            ),
            'ready' => array(
                'name' => __('Ready', 'mp-artwork'),
                'view' => array($this, 'setup_ready'),
                'handler' => ''
            )
        );
        $this->step = isset($_GET['step']) ? sanitize_key($_GET['step']) : current(array_keys($this->steps));
        $this->setup_wizard_header();
        $this->setup_wizard_steps();
        ?>
        <div class="welcome-panel wizard-panel">
            <?php
            $this->setup_wizard_content();
            $this->setup_wizard_footer();
            ?>
        </div>
        <?php
        exit;
    }

    public function get_next_step_link() {
        $keys = array_keys($this->steps);
        return add_query_arg('step', $keys[array_search($this->step, array_keys($this->steps)) + 1], remove_query_arg('translation_updated'));
    }

    public function get_before_step_link($step) {
        $keys = array_keys($this->steps);
        return add_query_arg('step', $keys[array_search($this->step, array_keys($this->steps)) - $step], remove_query_arg('translation_updated'));
    }

    /**
     * Output the steps
     */
    public function setup_wizard_steps() {
        $ouput_steps = $this->steps;
        $i = 0;
        ?>
        <ol class="theme-setup-steps subsubsub">
            <?php
            foreach ($ouput_steps as $step_key => $step) :
                $i++;
                ?>
                <li style="color:inherit;" >
                    <?php
                    $text = esc_html($step['name']);
                    if (array_search($this->step, array_keys($this->steps)) >= array_search($step_key, array_keys($this->steps)) || $this->step === $step_key) {
                        $text = '<strong>' . esc_html($step['name']) . '</strong>';
                    }
                    echo $text;
                    if ($i < sizeof($ouput_steps)): echo " > ";
                    endif;
                    ?>
                </li>
            <?php endforeach; ?>
        </ol>
        <hr style="clear: both; margin: 40px 0 1em;"/>
        <?php
    }

    /**
     * Output the content for the current step
     */
    public function setup_wizard_content() {
        echo '<div class="theme-setup-content welcome-panel-content">';
        call_user_func($this->steps[$this->step]['view']);
        echo '</div>';
    }

    /**
     * Introduction step
     */
    public function setup_introduction() {
        ?>

        <h2><?php _e('Welcome to the Artwork Theme!', 'mp-artwork'); ?></h2>
        <table class="form-table"> 
            <tbody>
                <tr>
                    <td>
                        <p><?php _e('Thank you for choosing Artwork theme. This Quick Guided Tour will show you how to:', 'mp-artwork'); ?></p>
                        <ul>
                            <li>- <?php _e('Customize this theme in a few steps', 'mp-artwork'); ?></li>
                            <li>- <?php _e('Change colors, texts and images', 'mp-artwork'); ?></li>
                            <li>- <?php _e('Import sample content', 'mp-artwork'); ?></li>
                            <li>- <?php _e('Install plugins', 'mp-artwork'); ?></li>
                        </ul>
                        <p class="description"><?php _e('If you don&rsquo;t want to go through the wizard, you can', 'mp-artwork'); ?>
                            <a href="<?php echo esc_url(admin_url('themes.php')); ?>" class="" ><?php _e('skip and return to the WordPress dashboard.', 'mp-artwork'); ?></a>
                            <?php _e('This Wizard is always available under Appearance > Theme Wizard menu.', 'mp-artwork'); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
        <p class="theme_setup-actions step text-center">
            <a href="<?php echo esc_url($this->get_next_step_link()); ?>" class="button button-primary button-large"><?php _e('Let\'s Go!', 'mp-artwork'); ?></a>          
        </p>
        <?php
    }

    /**
     * Customizer setup
     */
    public function setup_customizer() {
        ?>
        <p class="theme_setup-actions step " style="float:right; margin: -0.3em 0 0;">
            <a href="<?php echo esc_url($this->get_next_step_link()); ?>" class="button button-primary button-large"><?php _e('Continue', 'mp-artwork'); ?></a>
        </p>  
        <h2><?php _e('Customizer', 'mp-artwork'); ?></h2>
        <table class="form-table"> 
            <tbody>
                <tr>
                    <td colspan="2">
                        <p><?php _e('One of the main tools of this theme is the WordPress Customizer. Navigate to Appearance > Customize to change logo, website title, contact information, colors, menu items and so on. Once you are happy with your changes, click Save&Publish to reflect them on your live site.', 'mp-artwork'); ?></p>
                    </td>
                </tr>
                <tr colspan="2">
                    <td>
                        <h3><?php _e('1. Site Identity', 'mp-artwork'); ?></h3>
                    </td>
                </tr>
                <tr >
                    <td>
                        <img src="<?php echo MP_ARTWORK_PLUGIN_PATH . 'images/admin-customizer.png'; ?>">
                    </td>
                    <td>
                        <p><?php _e('The Site Identity section in customizer allows you to change the site title, description, and control whether or not you want to display them in the header.', 'mp-artwork'); ?></p>
                    </td>
                </tr>
                <tr colspan="2">
                    <td>
                        <h3><?php _e('2. Theme Colors', 'mp-artwork'); ?></h3>
                    </td>
                </tr>
                <tr >
                    <td>
                        <img src="<?php echo MP_ARTWORK_PLUGIN_PATH . 'images/admin-customizer-colors.png'; ?>">
                    </td>
                    <td>
                        <p><?php _e('The Colors section in customizer allows you to choose predefined colors or select your own colors.', 'mp-artwork'); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <hr/>
        <p class="theme_setup-actions step text-right" style="float:right; margin-top: 0.5em;">
            <a href="<?php echo esc_url($this->get_next_step_link()); ?>" class="button button-primary button-large"><?php _e('Continue', 'mp-artwork'); ?></a>
        </p>
        <p class="theme_setup-actions step text-right">
            <a href="<?php echo esc_url(admin_url('themes.php')); ?>" class="button button-large" ><?php _e('Exit', 'mp-artwork'); ?></a>
        </p>
        <?php
    }

    /**
     * Locale settings
     */
    public function setup_section() {
        ?>
        <p class="theme_setup-actions step " style="float:right; margin: -0.3em 0 0;">
            <a href="<?php echo esc_url($this->get_next_step_link()); ?>" class="button button-primary button-large"><?php _e('Continue', 'mp-artwork'); ?></a>

        </p> 
        <h2><?php _e(' Front Page Setup', 'mp-artwork'); ?></h2>
        <table class="form-table"> 
            <tbody>
                <tr>
                    <td colspan="2">
                        <p><?php _e('Front Page is the main page of your website that may display posts from Dashboard > Works menu. This theme created to showcase diverse artworks of artists and photographers. It can be easily adapted for needs of both art experts and fans in building online art studios and portfolios. How to setup your Front Page:', 'mp-artwork'); ?></p>
                    </td>
                </tr>
                <tr colspan="2">
                    <td>
                        <h3><?php _e('1. Create Front Page', 'mp-artwork'); ?></h3>
                    </td>
                </tr>
                <tr >
                    <td>
                        <img src="<?php echo MP_ARTWORK_PLUGIN_PATH . '/images/admin-customizer-frontpage.png'; ?>">
                    </td>
                    <td>
                        <ul>
                            <li><?php _e('1. Follow this link to ', 'mp-artwork'); ?> <a href="<?php echo esc_url(admin_url('post-new.php?post_type=page')); ?>" target="_blank"><?php _e('Create New Page', 'mp-artwork'); ?></a></li>
                            <li><?php _e('2. Name it "Home" or "Front Page"', 'mp-artwork'); ?></li>
                            <li><strong><?php _e('3. Choose "Front Page" template', 'mp-artwork'); ?></strong></li>
                            <li><?php _e('4. Press Publish button', 'mp-artwork'); ?></li>
                        </ul>
                        <p class="description"><?php _e('For more information, please view', 'mp-artwork'); ?> <a href="https://codex.wordpress.org/Pages#Creating_Pages" target="_blank"><?php _e('documentation', 'mp-artwork'); ?></a></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <h3><?php _e('2. Setup Front Page', 'mp-artwork'); ?></h3>
                    </td>
                </tr>
                <tr >
                    <td>
                        <img src="<?php echo MP_ARTWORK_PLUGIN_PATH . '/images/admin-customizer-frontpage-1.png'; ?>">
                    </td>

                    <td>
                        <ul>
                            <li><?php _e('1. Navigate to ', 'mp-artwork'); ?> <a href="<?php echo esc_url(admin_url('options-reading.php')); ?>" target="_blank"><?php _e('Settings -> Reading', 'mp-artwork'); ?></a></li>
                            <li><?php _e('2. In Settings -> Reading, set "Front page displays" to "A static page"', 'mp-artwork'); ?></li>
                            <li><?php _e('2. In Settings -> Reading, set "Front page" to "Home" or "Front Page" you\'ve created in first step', 'mp-artwork'); ?></li>
                            <li><?php _e('3. Scroll down and Save changes', 'mp-artwork'); ?></li>
                        </ul>
                        <p class="description"><?php _e('For more information, please view', 'mp-artwork'); ?> <a href="https://codex.wordpress.org/Creating_a_Static_Front_Page" target="_blank"><?php _e('documentation', 'mp-artwork'); ?></a></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <h3><?php _e('3. Setup Posts Page (Blog)', 'mp-artwork'); ?></h3>
                    </td>
                </tr>
                <tr >
                    <td>
                        <img src="<?php echo MP_ARTWORK_PLUGIN_PATH . '/images/admin-customizer-frontpage-2.png'; ?>">
                    </td>
                    <td>
                        <ul>
                            <li><?php _e('1. Create new page and name it "Blog"', 'mp-artwork'); ?></li>
                            <li><?php _e('2. In Settings -> Reading, set "Posts page" to "Blog"', 'mp-artwork'); ?></li>
                            <li><?php _e('3. Scroll down and Save changes', 'mp-artwork'); ?></li>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <hr/>
        <p class="theme_setup-actions step text-right" style="float:right; margin-top: 0.5em;">
            <a href="<?php echo esc_url($this->get_next_step_link()); ?>" class="button button-primary button-large"><?php _e('Continue', 'mp-artwork'); ?></a>
        </p>
        <p class="theme_setup-actions step text-right">
            <a href="<?php echo esc_url(admin_url('themes.php')); ?>" class="button button-large" ><?php _e('Exit', 'mp-artwork'); ?></a>
        </p>
        <?php
    }

    /**
     * Setup pages 
     */
    public function setup_pages() {
        $keys = array_keys($this->steps);
        $url = add_query_arg('step', $keys[array_search($this->step, array_keys($this->steps)) + 2], remove_query_arg('translation_updated'));
        ?>
        <p class="theme_setup-actions step text-right" style="float:right; margin-top: 0.5em;">
            <a href="<?php echo esc_url($url); ?>" class="button button-large"><?php _e('Skip', 'mp-artwork'); ?></a>
        </p>
        <h2><?php _e('This theme recommends to create the following pages:', 'mp-artwork'); ?></h2>
        <br>
        <form  method="post" action="<?php echo esc_url($this->get_next_step_link()); ?>">
            <?php if (!$this->check_pages()) { ?>
                <div class="checkbox" style="margin-bottom: 15px;">
                    <label>
                        <input type="checkbox" value="1" checked="checked" name="pages[about]"><?php _e('About page for Artwork theme', 'mp-artwork') ?>
                        <p class="description"><?php _e('Adds About page to this theme.', 'mp-artwork') ?></p>
                    </label>
                </div>
            <?php } else { ?>
				<p><?php _e('You have installed all recommended pages', 'mp-artwork') ?></p>
			<?php } ?>
            <hr/>
            <p class="theme_setup-actions step text-right" style="float:right; margin-top: 0.5em;">
                <?php
                ?>
                <a href="<?php echo esc_url($url); ?>" class="button button-large"><?php _e('Skip', 'mp-artwork'); ?></a>
                <?php if (!$this->check_pages()) : ?>
                    <input class="button button-primary" type="submit" value="<?php _e('Create', 'mp-artwork'); ?>"> 
                <?php endif; ?>
            </p>
            <p class="theme_setup-actions step text-right">
                <a href="<?php echo esc_url(admin_url('themes.php')); ?>" class="button" ><?php _e('Exit', 'mp-artwork'); ?></a>
            </p>


        </form>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery('.wizard-panel input[type="checkbox"]').click(function () {
                    if (jQuery(this).is(":checked")) {
                        jQuery(this).parent().find('input[type="checkbox"]').attr('value', '1');
                    } else {
                        jQuery(this).parent().find('input[type="checkbox"]').attr('value', '0');
                    }
                    jQuery('.wizard-panel input[type="submit"]').attr("disabled", "disabled");
                    jQuery('.wizard-panel input[type="checkbox"]').each(function () {
                        if (jQuery(this).is(":checked")) {
                            jQuery('.wizard-panel input[type="submit"]').removeAttr("disabled");
                        }
                    });

                });
            });
        </script>

        <?php
    }

    public function setup_install_pages() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery("html, body").animate({scrollTop: jQuery(document).height()}, 1000);
            });
        </script>
        <p class="theme_setup-actions step " style="float:right; margin: -0.3em 0 0;">
            <a href="<?php echo esc_url($this->get_next_step_link()); ?>" class="button button-primary button-large"><?php _e('Continue', 'mp-artwork'); ?></a>
        </p>  
        <h2><?php _e('Installing the pages', 'mp-artwork'); ?></h2>
        <?php
        $array = $_POST["pages"];
        if (sizeof($array) > 0) {
            if (array_key_exists("about", $array)) {
                echo '<h4>';
                echo __('About page', 'mp-artwork');
                echo '</h4>';
                $this->create_page();
            }
        } else {
            wp_redirect(esc_url(admin_url('admin.php?page=theme-setup')));
        }
        ?>
        <br>
        <hr/>
        <p class="theme_setup-actions step text-right" style="float:right; margin-top: 0.5em;">
            <a href="<?php echo esc_url($this->get_next_step_link()); ?>" class="button button-primary button-large"><?php _e('Continue', 'mp-artwork'); ?></a>

        </p>
        <p class="theme_setup-actions step text-right">
            <a href="<?php echo esc_url(admin_url('themes.php')); ?>" class="button button-large" ><?php _e('Exit', 'mp-artwork'); ?></a>
        </p>
        <?php
    }

    /**
     * Page Templates
     */
    public function setup_page_templates() {
        ?> 
        <p class="theme_setup-actions step " style="float:right; margin: -0.3em 0 0;">
            <?php
            if ($this->check_plugins()) :
                $keys = array_keys($this->steps);
                $url = add_query_arg('step', $keys[array_search($this->step, array_keys($this->steps)) + 3], remove_query_arg('translation_updated'));
                ?>
                <a href="<?php echo esc_url($url); ?>" class="button button-primary button-large"><?php _e('Continue', 'mp-artwork'); ?></a>
            <?php else: ?>
                <a href="<?php echo esc_url($this->get_next_step_link()); ?>" class="button button-primary button-large"><?php _e('Continue', 'mp-artwork'); ?></a>
            <?php endif; ?>
        </p>  
        <h2><?php _e('Works Archive', 'mp-artwork'); ?></h2>
        <table class="form-table"> 
            <tbody>
                <tr >
                    <td>
            <center><img src="<?php echo MP_ARTWORK_PLUGIN_PATH . 'images/admin-page-templates.png'; ?>"></center>
        </td>
        <td>
            <h3><?php _e('How to showcase your works in two columns:', 'mp-artwork'); ?></h3>
            <ul>
                <li><?php _e('1. Follow this link to ', 'mp-artwork'); ?> <a href="<?php echo esc_url(admin_url('post-new.php?post_type=page')); ?>" target="_blank"><?php _e('Create New Page', 'mp-artwork'); ?></a></li>
                <li><?php _e('2. Name it "Works"', 'mp-artwork'); ?></li>
                <li><?php _e('3. Choose "Works Archive" page template', 'mp-artwork'); ?></li>
                <li><?php _e('4. Press Publish button', 'mp-artwork'); ?></li>
                <li><?php _e('5. Add page to main menu', 'mp-artwork'); ?></li>
            </ul>
        </td>
        </tr>
        </tbody>
        </table>
        <br>
        <hr/>
        <p class="theme_setup-actions step text-right" style="float:right; margin-top: 0.5em;">
            <?php
            if ($this->check_plugins()) :
                $keys = array_keys($this->steps);
                $url = add_query_arg('step', $keys[array_search($this->step, array_keys($this->steps)) + 3], remove_query_arg('translation_updated'));
                ?>
                <a href="<?php echo esc_url($url); ?>" class="button button-primary button-large"><?php _e('Continue', 'mp-artwork'); ?></a>
            <?php else: ?>
                <a href="<?php echo esc_url($this->get_next_step_link()); ?>" class="button button-primary button-large"><?php _e('Continue', 'mp-artwork'); ?></a>
            <?php endif; ?>
        </p>
        <p class="theme_setup-actions step text-right">
            <a href="<?php echo esc_url(admin_url('themes.php')); ?>" class="button button-large" ><?php _e('Exit', 'mp-artwork'); ?></a>
        </p>
        <?php
    }

    /**
     * Setup plugin 
     */
    public function setup_plugins() {
        $installed_plugins = get_plugins();
        ?>
        <form  method="post" action="<?php echo esc_url($this->get_next_step_link()); ?>">
            <p class="theme_setup-actions step" style="float:right; margin: -0.3em 0 0;">
                <?php
                $keys = array_keys($this->steps);
                $url = add_query_arg('step', $keys[array_search($this->step, array_keys($this->steps)) + 2], remove_query_arg('translation_updated'));
                ?>
                <a href="<?php echo esc_url($url); ?>" class="button"><?php _e('Skip', 'mp-artwork'); ?></a>
                <input class="button button-primary" type="submit" value="Install Plugins">
            </p>
            <h2><?php _e('This theme recommends the following free plugins:', 'mp-artwork'); ?></h2>
            <br>
            <?php
            if (!isset($installed_plugins['mp-artwork/mp-artwork.php'])) :
                ?>
                <div class="checkbox" style="margin-bottom: 15px;">
                    <label>
                        <input type="checkbox" value="1" checked="checked" name="plugins[mp_artwork_install]">
                        <?php _e('Artwork Theme Engine', 'mp-artwork') ?>
                        <p class="description"><?php _e('Adds Works post type, theme wizard and page templates to this theme.', 'mp-artwork') ?></p>
                    </label>
                </div>
            <?php elseif (is_plugin_inactive('mp-artwork/mp-artwork.php')) : ?>
                <div class="checkbox" style="margin-bottom: 15px;">
                    <label>
                        <input type="checkbox" value="1" checked="checked" name="plugins[mp_artwork_activate]">
                        <?php _e('Artwork Theme Engine', 'mp-artwork') ?>
                        <p class="description"><?php _e('Activate this plugin.', 'mp-artwork') ?></p>
                    </label>
                </div>
                <?php
            endif;
            if ($this->theme_name === 'Artwork Lite'):
                if (!isset($installed_plugins['motopress-content-editor-lite/motopress-content-editor.php'])) :
                    ?>
                    <div class="checkbox" style="margin-bottom: 15px;">
                        <label>
                            <input type="checkbox" value="1" checked="checked" name="plugins[motopress_lite_install]">
                            <?php _e('MotoPress Content Editor Lite', 'mp-artwork') ?>
                            <p class="description"><?php _e('Enhances the standard WordPress editor and enables to build websites visually. It\'s complete solution for building responsive pages without coding and simply by dragging and dropping content elements.', 'mp-artwork') ?></p>
                        </label>
                    </div>
                <?php elseif (is_plugin_inactive('motopress-content-editor-lite/motopress-content-editor.php')) : ?>
                    <div class="checkbox" style="margin-bottom: 15px;">
                        <label>
                            <input type="checkbox" value="1" checked="checked" name="plugins[motopress_lite_activate]">
                            <?php _e('MotoPress Content Editor Lite', 'mp-artwork') ?>
                            <p class="description"><?php _e('Activate this plugin.', 'mp-artwork') ?></p>
                        </label>
                    </div>
                    <?php
                endif;
            else:
                if (!isset($installed_plugins['motopress-content-editor/motopress-content-editor.php'])) :
                    ?>
                    <div class="checkbox" style="margin-bottom: 15px;">
                        <label>
                            <input type="checkbox" value="1" checked="checked" name="plugins[motopress_install]">
                            <?php _e('MotoPress Content Editor', 'mp-artwork') ?>
                            <p class="description"><?php _e('Enhances the standard WordPress editor and enables to build websites visually. It\'s complete solution for building responsive pages without coding and simply by dragging and dropping content elements.', 'mp-artwork') ?></p>
                        </label>
                    </div>
                <?php elseif (is_plugin_inactive('motopress-content-editor/motopress-content-editor.php')) : ?>
                    <div class="checkbox" style="margin-bottom: 15px;">
                        <label>
                            <input type="checkbox" value="1" checked="checked" name="plugins[motopress_activate]">
                            <?php _e('MotoPress Content Editor', 'mp-artwork') ?>
                            <p class="description"><?php _e('Activate this plugin.', 'mp-artwork') ?></p>
                        </label>
                    </div>
                    <?php
                endif;
            endif;
            ?>                
            <?php
            if (!isset($installed_plugins['wordpress-importer/wordpress-importer.php'])) :
                ?>
                <div class="checkbox" style="margin-bottom: 15px;">
                    <label>
                        <input type="checkbox" value="1" checked="checked" name="plugins[importer_install]">
                        <?php _e('WordPress Importer', 'mp-artwork') ?>
                        <p class="description"><?php _e('Import posts, pages, comments, custom fields, categories, tags and more from a WordPress export file.', 'mp-artwork') ?></p>
                    </label>
                </div>
            <?php elseif (is_plugin_inactive('wordpress-importer/wordpress-importer.php')) : ?>
                <div class="checkbox" style="margin-bottom: 15px;">
                    <label>
                        <input type="checkbox" value="1" checked="checked" name="plugins[importer_activate]">
                        <?php _e('WordPress Importer', 'mp-artwork') ?>
                        <p class="description"><?php _e('Activate this plugin.', 'mp-artwork') ?></p>
                    </label>
                </div>
            <?php endif; ?>

            <?php
            if (!isset($installed_plugins['regenerate-thumbnails/regenerate-thumbnails.php'])) :
                ?>
                <div class="checkbox" style="margin-bottom: 15px;">
                    <label>
                        <input type="checkbox" value="1" checked="checked" name="plugins[regenerate_thumbnails_install]">
                        <?php _e('Regenerate Thumbnails', 'mp-artwork') ?>
                        <p class="description"><?php _e('Allows you to regenerate the thumbnails for your images if you\'ve changed a theme. Available under Dashboard - Tools - Regenerate Thumbnails menu.', 'mp-artwork') ?></p>

                    </label>
                </div>
            <?php elseif (is_plugin_inactive('regenerate-thumbnails/regenerate-thumbnails.php')) : ?>
                <div class="checkbox" style="margin-bottom: 15px;">
                    <label>
                        <input type="checkbox" value="1" checked="checked" name="plugins[regenerate_thumbnails_activate]" >
                        <?php _e('Regenerate Thumbnails', 'mp-artwork') ?>
                        <p class="description"><?php _e('Activate this plugin.', 'mp-artwork') ?></p>
                    </label>
                </div>
            <?php endif; ?>
            <hr/>
            <p class="theme_setup-actions step text-right" style="float:right; margin-top: 0.5em;">
                <?php
                $keys = array_keys($this->steps);
                $url = add_query_arg('step', $keys[array_search($this->step, array_keys($this->steps)) + 2], remove_query_arg('translation_updated'));
                ?>
                <a href="<?php echo esc_url($url); ?>" class="button button-large"><?php _e('Skip', 'mp-artwork'); ?></a>

                <input class="button button-primary" type="submit" value="Install Plugins"> </p>
            <p class="theme_setup-actions step text-right">
                <a href="<?php echo esc_url(admin_url('themes.php')); ?>" class="button" ><?php _e('Exit', 'mp-artwork'); ?></a>
            </p>


        </form>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                var check = false;
                jQuery(".wizard-panel input[type='checkbox']").each(function () {
                    if (jQuery(this).is(":checked")) {
                        check = true;
                    }
                });
                if (!check) {
                    jQuery('.wizard-panel input[type="submit"]').attr("disabled", "disabled");
                }
                jQuery('.wizard-panel input[type="checkbox"]').click(function () {
                    if (jQuery(this).is(":checked")) {
                        jQuery(this).parent().find('input[type="checkbox"]').attr('value', '1');
                    } else {
                        jQuery(this).parent().find('input[type="checkbox"]').attr('value', '0');
                    }
                    jQuery('.wizard-panel input[type="submit"]').attr("disabled", "disabled");
                    jQuery('.wizard-panel input[type="checkbox"]').each(function () {
                        if (jQuery(this).is(":checked")) {
                            jQuery('.wizard-panel input[type="submit"]').removeAttr("disabled");
                        }
                    });

                });
            });
        </script>
        <?php
    }

    public function setup_install_plugins() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery("html, body").animate({scrollTop: jQuery(document).height()}, 1000);
            });
        </script>
        <p class="theme_setup-actions step " style="float:right; margin: -0.3em 0 0;">
            <a href="<?php echo esc_url($this->get_next_step_link()); ?>" class="button button-primary button-large"><?php _e('Continue', 'mp-artwork'); ?></a>
        </p> 
        <h2><?php _e('Installing the plugins (this may take awhile)', 'mp-artwork'); ?></h2>
        <?php
        if ($_POST) {
            $array = $_POST["plugins"];
            if (sizeof($array) > 0) {
                require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
                if (array_key_exists("mp_artwork_install", $array) || array_key_exists("mp_artwork_activate", $array)) {
                    echo __('<h4>Works for Artwork theme</h4>', 'mp-artwork');
                }
                if (array_key_exists("mp_artwork_install", $array)) {
                    $plugin = 'mp-artwork';
                    $url = get_template_directory_uri() . '/assets/mp-artwork.zip';
                    $this->install_plugin($plugin, $url);
                    $plugin_path = 'mp-artwork/mp-artwork.php';
                    $plugin_name = __('Artwork Theme Engine', 'mp-artwork');
                    echo '<p>' . sprintf(__('Activating %s plugin...', 'mp-artwork'), $plugin_name) . '</p>';
                    $this->activate_plugin($plugin_name, $plugin_path);
                }
                if (array_key_exists("mp_artwork_activate", $array)) {
                    $plugin_path = 'mp-artwork/mp-artwork.php';
                    $plugin_name = __('Artwork Theme Engine', 'mp-artwork');
                    echo '<p>' . sprintf(__('Activating %s plugin...', 'mp-artwork'), $plugin_name) . '</p>';
                    $this->activate_plugin($plugin_name, $plugin_path);
                }
                if ($this->theme_name === 'Artwork Lite'):
                    if (array_key_exists("motopress_lite_install", $array) || array_key_exists("motopress_lite_activate", $array)):
                        echo __('<h4>MotoPress Content Editor Lite</h4>', 'mp-artwork');
                    endif;
                    if (array_key_exists("motopress_lite_install", $array)) :
                        $plugin = 'motopress-content-editor-lite';
                        $url = 'https://downloads.wordpress.org/plugin/motopress-content-editor-lite.zip';
                        $this->install_plugin($plugin, $url);
                        $plugin_path = 'motopress-content-editor-lite/motopress-content-editor.php';
                        $plugin_name = __('MotoPress Content Editor Lite', 'mp-artwork');
                        echo '<p>' . sprintf(__('Activating %s plugin...', 'mp-artwork'), $plugin_name) . '</p>';
                        $this->activate_plugin($plugin_name, $plugin_path);
                    endif;
                    if (array_key_exists("motopress_lite_activate", $array)) :
                        $plugin_path = 'motopress-content-editor-lite/motopress-content-editor.php';
                        $plugin_name = __('MotoPress Content Editor Lite', 'mp-artwork');
                        echo '<p>' . sprintf(__('Activating %s plugin...', 'mp-artwork'), $plugin_name) . '</p>';
                        $this->activate_plugin($plugin_name, $plugin_path);
                    endif;
                else:
                    if (array_key_exists("motopress_install", $array) || array_key_exists("motopress_activate", $array)) :
                        echo __('<h4>MotoPress Content Editor</h4>', 'mp-artwork');
                    endif;
                    if (array_key_exists("motopress_install", $array)) :
                        $plugin = 'motopress-content-editor';
                        $url = get_template_directory_uri() . '/assets/motopress-content-editor.zip';
                        $this->install_plugin($plugin, $url);
                        $plugin_path = 'motopress-content-editor/motopress-content-editor.php';
                        $plugin_name = __('MotoPress Content Editor', 'mp-artwork');
                        echo '<p>' . sprintf(__('Activating %s plugin...', 'mp-artwork'), $plugin_name) . '</p>';
                        $this->activate_plugin($plugin_name, $plugin_path);
                    endif;
                    if (array_key_exists("motopress_activate", $array)) :
                        $plugin_path = 'motopress-content-editor/motopress-content-editor.php';
                        $plugin_name = __('MotoPress Content Editor', 'mp-artwork');
                        echo '<p>' . sprintf(__('Activating %s plugin...', 'mp-artwork'), $plugin_name) . '</p>';
                        $this->activate_plugin($plugin_name, $plugin_path);
                    endif;
                endif;
                if (array_key_exists("importer_install", $array) || array_key_exists("importer_activate", $array)) {
                    echo __('<h4>WordPress Importer</h4>', 'mp-artwork');
                }
                if (array_key_exists("importer_install", $array)) {
                    $plugin = 'wordpress-importer';
                    $url = 'https://downloads.wordpress.org/plugin/wordpress-importer.0.6.1.zip';
                    $this->install_plugin($plugin, $url);
                    $plugin_path = 'wordpress-importer/wordpress-importer.php';
                    $plugin_name = __('WordPress Importer', 'mp-artwork');
                    echo '<p>' . sprintf(__('Activating %s plugin...', 'mp-artwork'), $plugin_name) . '</p>';
                    $this->activate_plugin($plugin_name, $plugin_path);
                }
                if (array_key_exists("importer_activate", $array)) {
                    $plugin_path = 'wordpress-importer/wordpress-importer.php';
                    $plugin_name = __('WordPress Importer', 'mp-artwork');
                    echo '<p>' . sprintf(__('Activating %s plugin...', 'mp-artwork'), $plugin_name) . '</p>';
                    $this->activate_plugin($plugin_name, $plugin_path);
                }
                if (array_key_exists("regenerate_thumbnails_install", $array) || array_key_exists("regenerate_thumbnails_activate", $array)) {
                    echo __('<h4>Regenerate Thumbnails</h4>', 'mp-artwork');
                }
                if (array_key_exists("regenerate_thumbnails_install", $array)) {
                    $plugin = 'regenerate-thumbnails';
                    $url = 'https://downloads.wordpress.org/plugin/regenerate-thumbnails.zip';
                    $this->install_plugin($plugin, $url);
                    $plugin_path = 'regenerate-thumbnails/regenerate-thumbnails.php';
                    $plugin_name = __('Regenerate Thumbnails', 'mp-artwork');
                    echo '<p>' . sprintf(__('Activating %s plugin...', 'mp-artwork'), $plugin_name) . '</p>';
                    $this->activate_plugin($plugin_name, $plugin_path);
                }
                if (array_key_exists("regenerate_thumbnails_activate", $array)) {
                    $plugin_path = 'regenerate-thumbnails/regenerate-thumbnails.php';
                    $plugin_name = __('Regenerate Thumbnails', 'mp-artwork');
                    echo '<p>' . sprintf(__('Activating %s plugin...', 'mp-artwork'), $plugin_name) . '</p>';
                    $this->activate_plugin($plugin_name, $plugin_path);
                }
            }
        }
        ?>

        <br>
        <hr/>
        <p class="theme_setup-actions step text-right" style="float:right; margin-top: 0.5em;">
            <a href="<?php echo esc_url($this->get_next_step_link()); ?>" class="button button-primary button-large"><?php _e('Continue', 'mp-artwork'); ?></a>

        </p>
        <p class="theme_setup-actions step text-right">
            <a href="<?php echo esc_url(admin_url('themes.php')); ?>" class="button button-large" ><?php _e('Exit', 'mp-artwork'); ?></a>
        </p>
        <?php
    }

    /**
     * import Dummy 
     */
    public function setup_import() {
        ?>
        <p class="theme_setup-actions step " style="float:right; margin: -0.3em 0 0;">
            <a href="<?php echo esc_url($this->get_next_step_link()); ?>" class="button button-primary button-large"><?php _e('Continue', 'mp-artwork'); ?></a>
        </p> 
        <h2 class="text-left"><?php _e('Sample content', 'mp-artwork'); ?></h2>

        <?php
        if ($this->check_import()):
            ?>
            <p><?php _e('Import sample content', 'mp-artwork'); ?></p>
            <p><?php _e('You can import sample content to make your site look similar to the demo. This is optional but provides a good starting point. Demo examples of the posts, works, and settings will be imported to your new website.', 'mp-artwork'); ?></p>
            <div class="import_preloader_text hide_theme_preloader_text" style="display: none;"><?php _e('Loading. Wait few minutes...', 'mp-artwork'); ?></div>
            <p class="start_import theme_setup-actions step pull-left"><button  class="button button-primary" id="start_import" data-filename="<?php echo MP_ARTWORK_PLUGIN_DIR_PATH . '/assets/artwork-sample-content.xml'; ?>"><?php _e('Import Sample Content', 'mp-artwork'); ?></button> </p>                

            <?php
        else:
            ?>
            <p><?php _e('You can import sample content to make your site look similar to the demo. This is optional but provides a good starting point. Demo examples of the posts, works, and settings will be imported to your new website.', 'mp-artwork'); ?></p>
            <p><?php _e('To import test content you need activate "WordPress Importer" and "Artwork Theme Engine" plugins.', 'mp-artwork'); ?></p>
            <p class="theme_setup-actions step pull-left">
                <a href="<?php echo esc_url($this->get_before_step_link(2)); ?>" class="button button-primary"><?php _e('Activate plugins', 'mp-artwork'); ?></a>          
            </p>
        <?php
        endif;
        ?>
        <br>
        <hr/>
        <p class="theme_setup-actions step text-right" style="float:right; margin-top: 0.5em;">
            <a href="<?php echo esc_url($this->get_next_step_link()); ?>" class="button button-primary button-large"><?php _e('Continue', 'mp-artwork'); ?></a>
        </p>
        <p class="theme_setup-actions step text-right">
            <a href="<?php echo esc_url(admin_url('themes.php')); ?>" class="button button-large" ><?php _e('Exit', 'mp-artwork'); ?></a>
        </p>
        <?php
    }

    public function setup_post_type_name() {
        deactivate_plugins('wordpress-importer/wordpress-importer.php');
        ?><p class="theme_setup-actions step " style="float:right; margin: -0.3em 0 0;">
            <a href="<?php echo esc_url($this->get_next_step_link()); ?>" class="button button-primary button-large"><?php _e('Continue', 'mp-artwork'); ?></a>
        </p>  
        <h2><?php _e('Post type', 'mp-artwork'); ?></h2>
        <table class="form-table"> 
            <tbody>
                <tr>
                    <td colspan="2">
                        <p><?php _e('Give your projects more suitable titles by renaming default &rdquo;Works&rdquo; (e.g. Portfolio, Photos). Please keep in mind that this change can be applied only once during the theme installation. If your &rdquo;Works&rdquo; are renamed for already existing posts, there is a risk to lose these posts&rsquo; data.', 'mp-artwork'); ?></p>
                        <h3><?php _e('You can change post type name:', 'mp-artwork'); ?></h3>
                        <?php $this->post_type_name_page(); ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php if (!has_nav_menu('primary')) : ?>
            <h2><?php _e('Select menu', 'mp-artwork'); ?></h2>
            <table class="form-table"> 
                <tbody>
                    <tr>
                        <td colspan="2">
                            <a href="<?php echo esc_url(admin_url('nav-menus.php')); ?>" class="" target="_blank"><?php _e('Appearance > menus', 'mp-artwork'); ?></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
        <hr/>
        <p class="theme_setup-actions step text-right" style="float:right; margin-top: 0.5em;">
            <a href="<?php echo esc_url($this->get_next_step_link()); ?>" class="button button-primary button-large"><?php _e('Continue', 'mp-artwork'); ?></a>
        </p>
        <p class="theme_setup-actions step text-right">
            <a href="<?php echo esc_url(admin_url('themes.php')); ?>" class="button button-large" ><?php _e('Exit', 'mp-artwork'); ?></a>
        </p>
        <?php
    }

    /**
     * Ready 
     */
    public function setup_ready() {
        ?>
        <h2 class="text-center"><?php _e('You are Ready!', 'mp-artwork'); ?></h2>
        <p><?php _e('Thank you for choosing Proift theme.', 'mp-artwork'); ?></p>
        <hr/>
        <p class="theme_setup-actions step">
            <a href="<?php echo esc_url(admin_url('customize.php')); ?>" class="button button-primary button-large"><?php _e('Customize this theme', 'mp-artwork'); ?></a>
            <a href="<?php echo esc_url(admin_url('themes.php')); ?>" class="button button-large" ><?php _e('Return to the WordPress Dashboard', 'mp-artwork'); ?></a>
        </p>       
        <?php
    }

    public function check_import() {
        if (is_plugin_active('mp-artwork/mp-artwork.php') && is_plugin_active('wordpress-importer/wordpress-importer.php')) :
            return true;
        endif;
        return false;
    }

    public function check_plugins() {
        if ($this->theme_name === 'Artwork Lite'):
            if ((is_plugin_active('mp-artwork/mp-artwork.php') && is_plugin_active('regenerate-thumbnails/regenerate-thumbnails.php') && is_plugin_active('motopress-content-editor-lite/motopress-content-editor.php'))) {
                return true;
            } else {
                return false;
            }
        else:
            if ((is_plugin_active('wordpress-importer/wordpress-importer.php') && is_plugin_active('mp-artwork/mp-artwork.php') && is_plugin_active('regenerate-thumbnails/regenerate-thumbnails.php') && is_plugin_active('motopress-content-editor/motopress-content-editor.php'))) {
                return true;
            } else {
                return false;
            }
        endif;
    }

    public function check_pages() {
        global $wpdb;
        $slug = 'about';
        $page_found = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'publish' AND post_name = %s LIMIT 1;", $slug));
        if ($page_found)
            return true;
        return false;
    }

    /*
     * Dismiss Theme Wizard admin notice 
     */

    private function wizard_dismiss() {
        update_user_meta(get_current_user_id(), $this->get_prefix() . 'wizard_dismiss', 1);
    }

    /**
     * Setup Wizard Header
     */
    public function setup_wizard_header() {
        $this->wizard_dismiss();
        ?>
        <div class="wrap">
            <h2 class="text-center" style="margin-top: 0px;"><?php _e('Theme Quick Guided Tour', 'mp-artwork'); ?></h2>
            <?php
        }

        /**
         * Setup Wizard Footer
         */
        public function setup_wizard_footer() {
            ?>
        </div>
        <?php
    }

    function install_plugin($plugin, $url) {
        $title = '';
        $upgrader = new Plugin_Upgrader(
                $skin = new Plugin_Upgrader_Skin(
                compact('url', 'plugin', '', 'title')
                )
        );
        // Perform plugin insatallation from source url
        $upgrader->install($url);
        //Flush plugins cache so we can make sure that the installed plugins list is always up to date
        wp_cache_flush();
    }

    function activate_plugin($plugin_name, $plugin_path) {
        $result = activate_plugin($plugin_path);

        if (is_wp_error($result)) {
            echo '<p>' . $plugin_name . ' ' . __('plugin is not activated', 'mp-artwork') . '</p>';
        } else {
            echo '<p>' . $plugin_name . ' ' . __('plugin is activated', 'mp-artwork') . '</p>';
        }
    }

    function create_page() {
        global $wpdb;
        echo '<p>' . __('Creating About page...', 'mp-artwork') . '</p>';
        $slug = 'about';
        $trashed_page_found = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug));
        if ($trashed_page_found) {
            $page_id = $trashed_page_found;
            $page_data = array(
                'ID' => $page_id,
                'post_status' => 'publish',
            );
            wp_update_post($page_data);
        } else {
            $page_data = array(
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_author' => 1,
                'post_name' => $slug,
                'post_title' => 'About',
                'post_content' => '',
                'post_parent' => 0,
                'comment_status' => 'closed'
            );
            $page_id = wp_insert_post($page_data);
        }
        echo '<p>' . __('About page is created', 'mp-artwork') . '</p>';
    }

}

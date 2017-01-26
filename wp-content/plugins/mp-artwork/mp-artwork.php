<?php
/*
 * Plugin Name: Artwork Theme Engine
 * Description: Adds Works post type, theme wizard and page templates to Artwork theme.
 * Version: 1.2.1
 * Author: MotoPress
 * Author URI: http://www.getmotopress.com/
 * License: GPLv2 or later
 * Text Domain: mp-artwork
 * Domain Path: /languages
 */

if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
	define( 'WP_LOAD_IMPORTERS', true );
}

if ( ! class_exists( 'MP_Artwork_Plugin' ) ) :

	final class MP_Artwork_Plugin {

		/**
		 * The single instance of the class.
		 */
		protected static $_instance = null;
		private $prefix;
		public $galleryPost;

		/**
		 * Main MP_Artwork_Plugin Instance.
		 *
		 * Ensures only one instance of WooCommerce is loaded or can be loaded.
		 *
		 * @since
		 * @static
		 * @see MP_Artwork_Plugin_Instance()
		 * @return MP_Artwork_Plugin - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function __construct() {
			if ( $this->is_artwork_theme() ) {
				$this->prefix = 'mp_artwork';
				/*
				 *  Path to classes folder in Plugin
				 */
				defined( 'MP_ARTWORK_PLUGIN_CLASS_PATH' ) || define( 'MP_ARTWORK_PLUGIN_CLASS_PATH', plugin_dir_path( __FILE__ ) . 'classes/' );
				defined( 'MP_ARTWORK_PLUGIN_PATH' ) || define( 'MP_ARTWORK_PLUGIN_PATH', plugin_dir_url( __FILE__ ) );
				defined( 'MP_ARTWORK_PLUGIN_DIR_PATH' ) || define( 'MP_ARTWORK_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
				$this->include_files();
				/*
				 * Post gallery
				 */
				require MP_ARTWORK_PLUGIN_CLASS_PATH . 'inc/post-gallery.php';
				$this->galleryPost = new MP_Artwork_Plugin_Gallery( $this->prefix );

				add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			}
			add_action( 'plugins_loaded', array( $this, 'mp_artwork_plugins_loaded' ) );


		}

		/**
		 * Load plugin textdomain.
		 *
		 * @access public
		 * @return void
		 */
		function mp_artwork_plugins_loaded() {
			load_plugin_textdomain( 'mp-artwork', false, basename( dirname( __FILE__ ) ) . '/languages/' );

			if ( current_user_can( 'edit_theme_options' ) ) {
				add_action( 'admin_notices', array( $this, 'wizard_admin_notice' ) );
			}
		}

		/**
		 * Get prefix.
		 *
		 * @access public
		 * @return sting
		 */
		public function get_prefix() {
			return $this->prefix . '_';
		}

		/**
		 * Get prefix.
		 *
		 * @access public
		 * @return sting
		 */
		public function is_artwork_theme() {
			$info = wp_get_theme();
			$name = $info->get( 'Name' );
			if ( $name === 'Artwork' || $name === 'Artwork Lite' ) {
				return true;
			}

			return false;
		}

		public function include_files() {
			/*
			 * Include posttype work
			 */
			include_once MP_ARTWORK_PLUGIN_CLASS_PATH . 'posttype/work.php';
			new MP_Artwork_Works( $this->prefix );
			/*
			 * Include front page
			 */
			include_once MP_ARTWORK_PLUGIN_CLASS_PATH . 'front/front-page.php';
			new MP_Artwork_Front_Page( $this->prefix );
			/*
			 * Include about page
			 */
			include_once MP_ARTWORK_PLUGIN_CLASS_PATH . 'about/about-page.php';
			new MP_Artwork_About_Page( $this->prefix );
			/*
			 * Include templates page
			 */
			include_once MP_ARTWORK_PLUGIN_CLASS_PATH . 'templates/page-templater-init.php';
			add_action( 'plugins_loaded', array( 'MP_Artwork_Page_Templater', 'get_instance' ) );

			/*
			 * Inclide customizer for sections
			 */
			include_once MP_ARTWORK_PLUGIN_CLASS_PATH . '/customiser/customiser.php';
			new MP_Artwork_Plugin_Customizer( $this->prefix );

			/*
			 * Importer dummy data
			 */
			include_once MP_ARTWORK_PLUGIN_CLASS_PATH . 'wizard/theme-importer-dummy-data.php';

			/*
			 * Activate theme
			 */
			include_once MP_ARTWORK_PLUGIN_CLASS_PATH . 'wizard/class-theme-install.php';
			new MP_Artwork_Plugin_Admin_Setup_Wizard( $this->prefix );

			/*
			 * Include Widgets Registrator
			 */
			include_once MP_ARTWORK_PLUGIN_CLASS_PATH . 'widget/Registrator.php';
			new MP_Artwork_Plugin_Widget_Registrator();
		}

		/**
		 * Get the template path.
		 * @return string
		 */
		public function template_path() {
			return apply_filters( 'mp_artwork_template_path', 'mp_artwork/' );
		}

		/**
		 * Enqueue scripts  for admin.
		 *
		 * @access public
		 * @return void
		 */
		function admin_scripts( $hook ) {
			if ( ! did_action( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}
			if ( wp_register_script( $this->get_prefix() . 'plugin_widget', MP_ARTWORK_PLUGIN_PATH . 'js/widget.js', array( 'jquery' ), '1.1.7' ) ) {
				wp_enqueue_script( $this->get_prefix() . 'plugin_widget' );
			}
			wp_enqueue_script( $this->get_prefix() . 'importer', MP_ARTWORK_PLUGIN_PATH . 'js/importer.min.js', array( 'jquery' ), '1.1.7' );
			wp_localize_script( $this->get_prefix() . 'importer', 'sysvar', array( 'adminajax' => admin_url( 'admin-ajax.php' ), ) );
			//color_picker
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( $this->get_prefix() . 'admin', MP_ARTWORK_PLUGIN_PATH . 'js/admin.js', array( 'wp-color-picker' ), '1.1.7' );



		}


		/**
		 * Get the plugin path.
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/*
		 * Theme Wizard admin notice
		 */

		public function wizard_admin_notice() {
			$this->wizard_dismiss();
			$isThemeActivation = apply_filters( $this->get_prefix() . 'activation', true );
			if ( $isThemeActivation && ! get_user_meta( get_current_user_id(), $this->get_prefix() . 'wizard_dismiss', true ) ) :
				?>
				<div class="notice notice-success is-dismissible">
					<p>
						<strong><?php _e( 'You&#8217;ve installed Artwork theme. Click &#34;Run Theme Wizard&#34; to view a quick guided tour of theme functionality and complete the installation.', 'mp-artwork' ); ?></strong>
					</p>
					<p>
						<a class="button button-primary"
						   href="<?php echo esc_url( admin_url( 'themes.php?page=theme-setup&' . $this->get_prefix() . 'wizard-dismiss=dismiss_admin_notices' ) ); ?>"><strong><?php _e( 'Run Theme Wizard', 'mp-artwork' ); ?></strong></a>
						<a class="button"
						   href="<?php echo esc_url( admin_url( 'themes.php' . '?' . $this->get_prefix() . 'wizard-dismiss=dismiss_admin_notices' ) ); ?>"
						   class="dismiss-notice" target="_parent"><strong><?php _e( 'Skip', 'mp-artwork' ); ?></strong></a>
					</p>
				</div>
				<?php
			endif;
		}

		/*
		 * Dismiss Theme Wizard admin notice
		 */

		private function wizard_dismiss() {
			if ( isset( $_GET[ $this->get_prefix() . 'wizard-dismiss' ] ) ) {
				update_user_meta( get_current_user_id(), $this->get_prefix() . 'wizard_dismiss', 1 );
			}
		}

	}

	/**
	 * Main instance of MP_Artwork_Plugin_Instance.
	 *
	 * Returns the main instance of WC to prevent the need to use globals.
	 *
	 * @since
	 * @return
	 */
	function MP_Artwork_Plugin_Instance() {
		return MP_Artwork_Plugin::instance();
	}

// Global for backwards compatibility.
	$GLOBALS['MP_Artwork_Plugin_Instance'] = MP_Artwork_Plugin_Instance();

	/*
	 * Include functions
	 */
	if ( MP_Artwork_Plugin_Instance()->is_artwork_theme() ) {
		include_once MP_ARTWORK_PLUGIN_CLASS_PATH . 'inc/mp_artwork_functions.php';
	}
endif;
                                

<?php
/**
 * Advanced WooCommerce Product Sales Reporting - Statistics & Forecast - Main Class
 *
 * @version 4.2.0
 * @since   4.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_ARS
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPFactory_WC_ARS' ) ) :

	/**
	 * WPFactory_WC_ARS class.
	 *
	 * @version 4.2.0
	 * @since   4.0.0
	 */
	final class WPFactory_WC_ARS {

		/**
		 * Plugin version.
		 *
		 * @since 4.0.0
		 *
		 * @var string
		 */
		public $version = WPFACTORY_WC_ARS_VERSION;

		/**
		 * The single instance of the class.
		 *
		 * @version 4.2.0
		 * @since   4.0.0
		 *
		 * @var WPFactory_WC_ARS
		 */
		protected static $instance = null;

		/**
		 * Main WPFactory_WC_ARS Instance.
		 *
		 * Ensures only one instance of WPFactory_WC_ARS is loaded or can be loaded.
		 *
		 * @version 4.2.0
		 * @since   4.0.0
		 *
		 * @static
		 *
		 * @return WPFactory_WC_ARS
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * WPFactory_WC_ARS Constructor.
		 *
		 * @version 4.1.8
		 * @since   4.0.0
		 *
		 * @access public
		 */
		public function __construct() {
			// Check for active WooCommerce plugin.
			if ( ! function_exists( 'WC' ) ) {
				return;
			}

			// Admin.
			if ( is_admin() ) {
				$this->admin();
			}
		}

		/**
		 * Admin.
		 *
		 * @version 4.0.0
		 * @since   4.0.0
		 */
		public function admin() {
			// Load libs.
			require_once plugin_dir_path( WPFACTORY_WC_ARS_FILE ) . 'vendor/autoload.php';

			// Action links.
			add_filter(
				'plugin_action_links_' . plugin_basename( WPFACTORY_WC_ARS_FILE ),
				array( $this, 'action_links' )
			);

			// "Recommendations" page.
			add_action( 'init', array( $this, 'add_cross_selling_library' ) );

			// Settings.
			add_filter( 'admin_menu', array( $this, 'add_settings' ), 11 );
		}

		/**
		 * Action links.
		 *
		 * @version 4.1.7
		 * @since   4.0.0
		 *
		 * @param mixed $links Action links for the plugin.
		 *
		 * @return array
		 */
		public function action_links( $links ) {
			$custom_links = array();

			$custom_links[] = '<a href="' . admin_url( 'admin.php?page=webd-woocommerce-reporting-statistics' ) . '">' .
				__( 'Reports', 'webd-woocommerce-advanced-reporting-statistics' ) .
			'</a>';

			$pro_url        = 'https://extend-wp.com/product/woocommerce-advanced-reporting-statistics/';
			$custom_links[] = '<a target="_blank" style="font-weight: bold; color: green;" href="' . $pro_url . '">' .
				__( 'Go Pro', 'webd-woocommerce-advanced-reporting-statistics' ) .
			'</a>';

			return array_merge( $custom_links, $links );
		}

		/**
		 * Add cross selling library.
		 *
		 * @version 4.0.0
		 * @since   4.0.0
		 */
		public function add_cross_selling_library() {
			if ( ! class_exists( '\WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling' ) ) {
				return;
			}

			$cross_selling = new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling();
			$cross_selling->setup( array( 'plugin_file_path' => WPFACTORY_WC_ARS_FILE ) );
			$cross_selling->init();
		}

		/**
		 * Add settings.
		 *
		 * @version 4.1.7
		 * @since   4.0.0
		 */
		public function add_settings() {
			if ( ! class_exists( 'WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu' ) ) {
				return;
			}

			$admin_menu = WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu::get_instance();

			add_submenu_page(
				$admin_menu->get_menu_slug(),
				__( 'Reporting & Statistics', 'webd-woocommerce-advanced-reporting-statistics' ),
				__( 'Reporting & Statistics', 'webd-woocommerce-advanced-reporting-statistics' ),
				'manage_woocommerce', // phpcs:ignore WordPress.WP.Capabilities.Unknown
				'webd-woocommerce-reporting-statistics',
				array( $this, 'output_settings' ),
				30
			);
		}

		/**
		 * Output settings.
		 *
		 * @version 4.0.0
		 * @since   4.0.0
		 */
		public function output_settings() {
			do_action( 'wpfactory_wc_ars_output_settings' );
		}

		/**
		 * Plugin URL.
		 *
		 * @version 4.0.0
		 * @since   4.0.0
		 *
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugin_dir_url( WPFACTORY_WC_ARS_FILE ) );
		}

		/**
		 * Plugin path.
		 *
		 * @version 4.0.0
		 * @since   4.0.0
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( WPFACTORY_WC_ARS_FILE ) );
		}
	}

endif;

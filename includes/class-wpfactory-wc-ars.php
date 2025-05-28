<?php
/**
 * Advanced WooCommerce Product Sales Reporting - Statistics & Forecast - Main Class
 *
 * @version 4.0.0
 * @since   4.0.0
 *
 * @author  WPFactory
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPFactory_WC_ARS' ) ) :

final class WPFactory_WC_ARS {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 4.0.0
	 */
	public $version = WPFACTORY_WC_ARS_VERSION;

	/**
	 * @var   WPFactory_WC_ARS The single instance of the class
	 * @since 4.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main WPFactory_WC_ARS Instance.
	 *
	 * Ensures only one instance of WPFactory_WC_ARS is loaded or can be loaded.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 *
	 * @static
	 * @return  WPFactory_WC_ARS - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * WPFactory_WC_ARS Constructor.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 *
	 * @access  public
	 */
	function __construct() {

		// Check for active WooCommerce plugin
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		// Set up localisation
		add_action( 'init', array( $this, 'localize' ) );

		// Admin
		if ( is_admin() ) {
			$this->admin();
		}

	}

	/**
	 * localize.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 */
	function localize() {
		load_plugin_textdomain(
			'webd-woocommerce-reporting-statistics',
			false,
			dirname( plugin_basename( WPFACTORY_WC_ARS_FILE ) ) . '/lang/'
		);
	}

	/**
	 * admin.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 */
	function admin() {

		// Load libs
		require_once plugin_dir_path( WPFACTORY_WC_ARS_FILE ) . 'vendor/autoload.php';

		// "Recommendations" page
		add_action( 'init', array( $this, 'add_cross_selling_library' ) );

		// Settings
		add_filter( 'admin_menu', array( $this, 'add_settings' ), 11 );

	}

	/**
	 * add_settings.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 */
	function add_settings() {

		if ( ! class_exists( 'WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu' ) ) {
			return;
		}

		$admin_menu = WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu::get_instance();

		add_submenu_page(
			$admin_menu->get_menu_slug(),
			__( 'Reporting & Statistics', 'webd-woocommerce-reporting-statistics' ),
			__( 'Reporting & Statistics', 'webd-woocommerce-reporting-statistics' ),
			'manage_woocommerce',
			'webd-woocommerce-reporting-statistics',
			array( $this, 'output_settings' ),
			30
		);

	}

	/**
	 * output_settings.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 */
	function output_settings() {
		do_action( 'wpfactory_wc_ars_output_settings' );
	}

	/**
	 * add_cross_selling_library.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 */
	function add_cross_selling_library() {

		if ( ! class_exists( '\WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling' ) ) {
			return;
		}

		$cross_selling = new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling();
		$cross_selling->setup( array( 'plugin_file_path' => WPFACTORY_WC_ARS_FILE ) );
		$cross_selling->init();

	}

	/**
	 * plugin_url.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 *
	 * @return  string
	 */
	function plugin_url() {
		return untrailingslashit( plugin_dir_url( WPFACTORY_WC_ARS_FILE ) );
	}

	/**
	 * plugin_path.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 *
	 * @return  string
	 */
	function plugin_path() {
		return untrailingslashit( plugin_dir_path( WPFACTORY_WC_ARS_FILE ) );
	}

}

endif;

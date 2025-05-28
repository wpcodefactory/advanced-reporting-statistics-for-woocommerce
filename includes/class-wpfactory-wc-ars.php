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

		// Admin
		if ( is_admin() ) {
			$this->admin();
		}

	}

	/**
	 * admin.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 */
	function admin() {
		return true;
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

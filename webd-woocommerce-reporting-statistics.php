<?php
/*
 * Plugin Name: Advanced WooCommerce Product Sales Reporting - Statistics & Forecast
 * Plugin URI: https://extend-wp.com/advanced-reporting-statistics-plugin-for-woocommerce/
 * Description: A comprehensive WordPress Plugin for WooCommerce Reports, Statistics, Analytics & Forecasting Tool for Orders, Sales, Products, Countries, Payment Gateways Shipping, Tax, Refunds, Top Products.
 * Version: 4.1.3-dev
 * Author: WPFactory
 * Author URI: https://wpfactory.com
 * WC requires at least: 2.2
 * WC tested up to: 10.4
 * Requires Plugins: woocommerce
 * Text Domain: webd-woocommerce-reporting-statistics
 * Domain Path: /langs
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Created On: 23-01-2019
 * Updated On: 26-12-2025
 */

defined( 'ABSPATH' ) || exit;

defined( 'WPFACTORY_WC_ARS_VERSION' ) || define( 'WPFACTORY_WC_ARS_VERSION', '4.1.3-dev-20260113-2231' );

defined( 'WPFACTORY_WC_ARS_FILE' ) || define( 'WPFACTORY_WC_ARS_FILE', __FILE__ );

require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpfactory-wc-ars.php';

if ( ! function_exists( 'wpfactory_wc_ars' ) ) {
	/**
	 * Returns the main instance of WPFactory_WC_ARS to prevent the need to use globals.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 */
	function wpfactory_wc_ars() {
		return WPFactory_WC_ARS::instance();
	}
}

add_action( 'plugins_loaded', 'wpfactory_wc_ars' );

/**
 * includes.
 */
include_once( plugin_dir_path(__FILE__) ."/helper-class.php");
include_once( plugin_dir_path(__FILE__) ."/class-admin.php");

/**
 * webdWoocommerceReportingStatistics class.
 *
 * @version 4.1.3
 *
 * @todo    (v4.0.0) cleanup notification (e.g., `push_not` AJAX action)
 * @todo    (v4.0.0) remove the "GO PRO" tab?
 * @todo    (v4.0.0) Plugin Check (PCP)
 * @todo    (v4.0.0) remove `public $name`, `public $slug`, `public $proUrl`?
 * @todo    (v4.0.0) cleanup
 */
class webdWoocommerceReportingStatistics extends webdWoocommerceReportingStatisticsAdmin {

	public $plugin = 'webdWoocommerceReportingStatistics';
	public $name   = 'Webd Woocommerce Advanced Reporting & Statistics';
	public $slug   = 'webd-woocommerce-reporting-statistics';
	public $proUrl = 'https://extend-wp.com/product/woocommerce-advanced-reporting-statistics/';

	/**
	 * Constructor.
	 *
	 * @version 4.1.3
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'BackEndScripts' ) );

		add_action( 'wpfactory_wc_ars_output_settings', array( $this, 'init' ) );

		register_activation_hook( __FILE__, array( $this, 'onActivation' ) );

		add_action( 'admin_init', array( $this, 'settingsSection' ) );

		if (
			isset( $_GET['page'] ) &&
			'webd-woocommerce-reporting-statistics' === $_GET['page']
		) {
			add_action( 'admin_footer', array( $this, 'proModal' ) );
		}

		add_action( 'wp_ajax_stat_extensions', array( $this,'extensions' ) );

		// Deactivation survey
		include( plugin_dir_path( __FILE__ ) . '/lib/codecabin/plugin-deactivation-survey/deactivate-feedback-form.php' );
		add_filter( 'codecabin_deactivate_feedback_form_plugins', function ( $plugins ) {
			$plugins[] = (object) array(
				'slug'    => 'webd-woocommerce-advanced-reporting-statistics',
				'version' => '3.1',
			);
			return $plugins;
		} );

	}

	/**
	 * onActivation.
	 */
	public function onActivation() {
		require_once(ABSPATH .'/wp-admin/includes/plugin.php');
		$pro = "/woocommerce-reporting-statistics-pro/woocommerce-reporting-statistics-pro.php";
		deactivate_plugins($pro);

		$order_fields = array(  "_id","_date_created", "_status",  "_payment_method_title","_coupon_codes" , "_billing_first_name" , "_billing_last_name" ,  "_billing_country", "_shipping_total" , "_total_discount" , "_total_tax" , "_total_refunded" ,"_subtotal" , "_total" );
		update_option($this->plugin."_custom_fields", $order_fields );

		$default_status = ['wc-completed', 'wc-processing', 'wc-on-hold', 'wc-refunded' ];
		update_option( $this->plugin.'_status' , $default_status );
	}

	/**
	 * BackEndScripts.
	 *
	 * @version 4.0.0
	 *
	 * @todo    (v4.0.0) add `version`, etc.
	 */
	public function BackEndScripts() {
		$screen = get_current_screen();
		if ( 'wpfactory_page_webd-woocommerce-reporting-statistics' !== $screen->base ) {
			return;
		}

		wp_enqueue_style( "webd-woocommerce-reporting-statistics"."adminCss", plugins_url( "/css/backend.css?v=bvc", __FILE__ ) );

		wp_enqueue_script('jquery');

		wp_enqueue_script( 'jquery-ui-datepicker' );

		wp_enqueue_style( 'jquery-ui-style', plugins_url( "/css/jquery-ui.css", __FILE__ ), true);

		wp_enqueue_script('jquery-ui-accordion');

		wp_enqueue_script("jquery-ui-tabs");

		wp_enqueue_script( "webd-woocommerce-reporting-statistics"."charts", plugins_url( "/js/chart.js", __FILE__ ), null, true);

		wp_enqueue_script( "webd-woocommerce-reporting-statistics"."adminJs", plugins_url( "/js/backend.js?v=bvc", __FILE__ ) , array('jquery','jquery-ui-accordion','jquery-ui-tabs','jquery-ui-datepicker') , null, true);

		wp_enqueue_style( "webd-woocommerce-reporting-statistics"."_fa", plugins_url( '/css/font-awesome.min.css', __FILE__ ));

		wp_localize_script(
			"webd-woocommerce-reporting-statistics"."adminJs",
			"webdWoocommerceReportingStatistics",
			array(
				'thispluginpage' => admin_url("/admin.php?page=".$this->slug),
				'url'            => admin_url( 'admin-ajax.php' ),
				'plugin_url'     => plugins_url( '', __FILE__ ),
				'ajax_url'       => admin_url( 'admin-ajax.php' ),
				'siteUrl'        => site_url(),
				'plugin_wrapper' => "webd-woocommerce-reporting-statistics",
				'select'         => esc_html__( 'Select...', $this->plugin ),
				'orders_loading' => esc_html__( "Your orders are loading please wait...", $this->plugin ),
				'orders_loaded'  => esc_html__( "All orders are loaded!", $this->plugin ),
				'no_orders'      => esc_html__( "No orders found...", $this->plugin ),
				'page'           => $this->slug,
				'tab'            => array( 'all' ),
				'custom_fields'  => get_option( $this->plugin."_custom_fields" ),
				'currency'       => get_woocommerce_currency_symbol(),
				'limit'          => get_option( $this->plugin."queryLimit" , 500 ),
			)
		);

	}

	/**
	 * init.
	 */
	public function init() {
		print "<div class='". $this->plugin ."'>";
		$this->adminHeader();
		$this->adminSettings();
		$this->adminFooter();
		print "</div>";
	}

	/**
	 * initStats.
	 */
	public function initStats() {
		print "<div class='". $this->plugin ."'>";
		$this->adminHeader();
		echo "<h3>". esc_html__('Statistics',"webd-woocommerce-reporting-statistics") ."</h3>";
		echo do_shortcode( '[adStats]' );
		$this->adminFooter();
		print "</div>";
	}

}

$initialize = new webdWoocommerceReportingStatistics();

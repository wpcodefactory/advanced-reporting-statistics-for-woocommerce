<?php
/*
 * Plugin Name: Advanced WooCommerce Product Sales Reporting - Statistics & Forecast
 * Plugin URI: https://extend-wp.com/advanced-reporting-statistics-plugin-for-woocommerce/
 * Description: A comprehensive WordPress Plugin for WooCommerce Reports, Statistics, Analytics & Forecasting Tool for Orders, Sales, Products, Countries, Payment Gateways Shipping, Tax, Refunds, Top Products.
 * Version: 4.0.0-dev
 * Author: WPFactory
 * Author URI: https://wpfactory.com
 * WC requires at least: 2.2
 * WC tested up to: 9.8
 * Requires Plugins: woocommerce
 * Text Domain: webd-woocommerce-reporting-statistics
 * Domain Path: /lang
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Created On: 23-01-2019
 * Updated On: 28-05-2025
 */

defined( 'ABSPATH' ) || exit;

defined( 'WPFACTORY_WC_ARS_VERSION' ) || define( 'WPFACTORY_WC_ARS_VERSION', '4.0.0-dev-20250528-1444' );

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
 */
class webdWoocommerceReportingStatistics extends webdWoocommerceReportingStatisticsAdmin {

		public $plugin       = 'webdWoocommerceReportingStatistics';
		public $name         = 'Webd Woocommerce Advanced Reporting & Statistics';
		public $shortName    = 'Reporting & Statistics';
		public $slug         = 'webd-woocommerce-reporting-statistics';
		public $dashicon     = 'dashicons-editor-table';
		public $proUrl       = 'https://extend-wp.com/product/woocommerce-advanced-reporting-statistics/';
		public $menuPosition = '50';
		public $description  = 'Advanced Reporting Analytics and Statistical Analysic to manage Woocommerce Eshop efficiently.';

		public $localizeBackend;
		public $localizeFrontend;

		public function __construct() {

			add_action('admin_enqueue_scripts', array($this, 'BackEndScripts') );
			add_action('admin_menu', array($this, 'SettingsPage') );
			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this, 'Links') );


			register_activation_hook( __FILE__,  array($this, 'onActivation') );
			register_deactivation_hook( __FILE__,  array($this, 'onDeactivation') );

			add_action('plugins_loaded', 'translate');
			add_action("admin_init", array($this, 'settingsSection') );

			if( isset( $_GET['page'] ) && $_GET['page'] == 'webd-woocommerce-reporting-statistics' ) {
				add_action("admin_footer", array($this,"proModal" ) );
			}



			add_action( 'wp_ajax_nopriv_stat_extensions', array( $this,'extensions' ) );
			add_action( 'wp_ajax_stat_extensions', array( $this,'extensions' ) );

			add_action( 'before_woocommerce_init', function() {
				if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
				}
			} );

			// deactivation survey

			include( plugin_dir_path(__FILE__) .'/lib/codecabin/plugin-deactivation-survey/deactivate-feedback-form.php');
			add_filter('codecabin_deactivate_feedback_form_plugins', function($plugins) {

				$plugins[] = (object)array(
					'slug'		=> 'webd-woocommerce-advanced-reporting-statistics',
					'version'	=> '3.1'
				);

				return $plugins;

			});

			register_activation_hook( __FILE__, array( $this, 'notification_hook' ) );

			add_action( 'admin_notices', array( $this,'notification' ) );
			add_action( 'wp_ajax_nopriv_push_not',array( $this, 'push_not'  ) );
			add_action( 'wp_ajax_push_not', array( $this, 'push_not' ) );


		}

		public function notification(){

			$screen = get_current_screen();
			if ( 'woocommerce_page_webd-woocommerce-reporting-statistics'  !== $screen->base )
			return;

			/* Check transient, if available display notice */
			if( get_transient( $this->plugin ."_notification" ) ){
				?>
				<div class="updated notice  webdWoocommerceReportingStatistics_notification">
					<a href="#" class='dismiss' style='float:right;padding:4px' >close</a>
					<h4><i><?php esc_attr( print $this->name );?> | <?php esc_html_e( "Add your Email below & get ", 'imue' ); ?><strong><?php esc_html_e( "discounts", 'webd-woocommerce-reporting-statistics' ); ?></strong><?php esc_html_e( " in our pro plugins at", 'webd-woocommerce-reporting-statistics' ); ?> <a href='https://extend-wp.com' target='_blank' >extend-wp.com!</a></i></h4>

					<form method='post' id='webdWoocommerceReportingStatistics_signup'>
						<p>
						<input required type='email' name='woopei_email' />
						<input required type='hidden' name='product' value='2071' />
						<input type='submit' class='button button-primary' name='submit' value='<?php esc_html_e("Sign up", "webd-woocommerce-reporting-statistics" ); ?>' />
						</p>
					</form>
				</div>
				<?php
			}
		}


		public function push_not(){

			delete_transient( $this->plugin ."_notification" );

		}
		public function notification_hook() {
			set_transient( $this->plugin ."_notification", true );
		}

		public function translate() {
			load_plugin_textdomain( $this->slug, false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
		}

		public function onActivation(){
			require_once(ABSPATH .'/wp-admin/includes/plugin.php');
			$pro = "/woocommerce-reporting-statistics-pro/woocommerce-reporting-statistics-pro.php";
			deactivate_plugins($pro);

			$order_fields = array(  "_id","_date_created", "_status",  "_payment_method_title","_coupon_codes" , "_billing_first_name" , "_billing_last_name" ,  "_billing_country", "_shipping_total" , "_total_discount" , "_total_tax" , "_total_refunded" ,"_subtotal" , "_total" );
			update_option($this->plugin."_custom_fields", $order_fields );

			$default_status = ['wc-completed', 'wc-processing', 'wc-on-hold', 'wc-refunded' ];
			update_option( $this->plugin.'_status' , $default_status );

		}

		public function onDeactivation() {

		}

		public function BackEndScripts(){

			$screen = get_current_screen();
			if ( 'woocommerce_page_webd-woocommerce-reporting-statistics'  !== $screen->base )
			return;

			wp_enqueue_style( "webd-woocommerce-reporting-statistics"."adminCss", plugins_url( "/css/backend.css?v=bvc", __FILE__ ) );
			wp_enqueue_style( "webd-woocommerce-reporting-statistics"."adminCss");

			wp_enqueue_script('jquery');
            wp_enqueue_script( 'jquery-ui-datepicker' );
		    wp_enqueue_style( 'jquery-ui-style', plugins_url( "/css/jquery-ui.css", __FILE__ ), true);
			wp_enqueue_script('jquery-ui-accordion');
			wp_enqueue_script("jquery-ui-tabs");
			wp_enqueue_script( "webd-woocommerce-reporting-statistics"."charts", plugins_url( "/js/chart.js", __FILE__ ), null, true);
			wp_enqueue_script( "webd-woocommerce-reporting-statistics"."adminJs", plugins_url( "/js/backend.js?v=bvc", __FILE__ ) , array('jquery','jquery-ui-accordion','jquery-ui-tabs','jquery-ui-datepicker') , null, true);

			wp_enqueue_style( "webd-woocommerce-reporting-statistics"."_fa", plugins_url( '/css/font-awesome.min.css', __FILE__ ));

			$page = $this->slug;
			$tab = array( 'all' );
			$custom_fields = get_option( $this->plugin."_custom_fields" );

			$queryLimit = get_option( $this->plugin."queryLimit" , 500 );

			$this->localizeBackend = array(
				'thispluginpage'=> admin_url("/admin.php?page=".$this->slug),
				'url' => admin_url( 'admin-ajax.php' ),
				'plugin_url' => plugins_url( '', __FILE__ ),
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'siteUrl'	=>	site_url(),
				'plugin_wrapper'=> "webd-woocommerce-reporting-statistics",
				'select'	=>	esc_html__( 'Select...', $this->plugin ),
				'orders_loading' => esc_html__ ( "Your orders are loading please wait...", $this->plugin ),
				'orders_loaded' => esc_html__ ( "All orders are loaded!", $this->plugin ),
				'no_orders' => esc_html__ ( "No orders found...", $this->plugin ),
				'page' => $page,
				'tab' => $tab,
				'custom_fields'=> $custom_fields,
				'currency'=> get_woocommerce_currency_symbol(),
				'limit' => $queryLimit
			);
			wp_localize_script("webd-woocommerce-reporting-statistics"."adminJs", "webdWoocommerceReportingStatistics" , $this->localizeBackend );
			wp_enqueue_script( "webd-woocommerce-reporting-statistics"."adminJs");
		}


		public function SettingsPage(){
			add_submenu_page( 'woocommerce', $this->shortName, $this->shortName, 'manage_woocommerce', $this->slug, array($this, 'init') );
		}

		public function Links($links){
			$mylinks=array();
			$mylinks[] .=  '<a href="' . admin_url( "admin.php?page=".$this->slug ) . '">' . esc_html__( "Reports", "webd-woocommerce-reporting-statistics" ) . '</a>';
			$mylinks[] .=  '<a target="_blank" href="' . $this->proUrl . '">' . esc_html__( "PRO Version", "webd-woocommerce-reporting-statistics" ) . '</a>';
			return array_merge( $links, $mylinks );
		}

		public function init(){
			print "<div class='". $this->plugin ."'>";
			$this->adminHeader();
			$this->adminSettings();
			$this->adminFooter();
			print "</div>";
		}
		public function initStats(){
			print "<div class='". $this->plugin ."'>";
			$this->adminHeader();
			echo "<h3>". esc_html__('Statistics',"webd-woocommerce-reporting-statistics") ."</h3>";
			echo do_shortcode( '[adStats]' );

			$this->adminFooter();
			print "</div>";
		}

}

$initialize = new webdWoocommerceReportingStatistics();

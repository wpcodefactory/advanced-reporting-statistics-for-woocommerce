<?php
/**
 * Advanced WooCommerce Product Sales Reporting - Statistics & Forecast - OrderProcessorHelp Class
 *
 * @author  WPFactory
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Utilities\OrderUtil;

class OrderProcessorHelp{

	public $plugin = 'webdWoocommerceReportingStatistics';
	public $orders_info = array(  "_id","_transaction_id", "_date_created", "_date_modified", "_date_completed" , "_date_paid" ,  "_status", "_products", "_currency" , "_discount_tax" , "_discount_total" , "_shipping_tax" , "_shipping_total" , "_total_discount" , "_total_tax" , "_total_refunded" , "_total_tax_refunded" , "_total_shipping_refunded" , "_total_fees","_subtotal" , "_total" , "_item_count_refunded" , "_total_qty_refunded" , "_item_count" ,  "_payment_method" , "_payment_method_title","_coupon_codes"  , "_billing_first_name" , "_billing_last_name" , "_billing_company" , "_billing_address_1" , "_billing_address_2" , "_billing_city" , "_billing_state" , "_billing_postcode" , "_billing_country" , "_billing_email" , "_billing_phone" , "_shipping_first_name" , "_shipping_last_name" , "_shipping_company" , "_shipping_address_1" , "_shipping_address_2" , "_shipping_city" , "_shipping_state" , "_shipping_postcode" , "_shipping_country" , "_shipping_method" , "_customer_id" , "_customer_ip_address"  );
	private $products = [];

	protected static $instance = NULL;
	private $datediff = '';
	public static function get_instance()
	{
		if ( NULL === self::$instance )
			self::$instance = new self;

		return self::$instance;
	}

	public function __construct() {


		add_action( 'wp_ajax_getOrders', array( $this,'getOrders' ) );
		add_action( 'wp_ajax_nopriv_getOrders', array( $this,'getOrders' ) );

		add_action( 'wp_ajax_get_orders', array( $this,'get_orders' ) );
		add_action( 'wp_ajax_nopriv_get_orders', array( $this,'get_orders' ) );

		add_action( 'wp_ajax_get_customers', array( $this,'get_customers' ) );
		add_action( 'wp_ajax_nopriv_get_customers', array( $this,'get_customers' ) );

		add_action( 'wp_ajax_get_countries', array( $this,'get_countries' ) );
		add_action( 'wp_ajax_nopriv_get_countries', array( $this,'get_countries' ) );

		add_action( 'wp_ajax_get_payments', array( $this,'get_payments' ) );
		add_action( 'wp_ajax_nopriv_get_payments', array( $this,'get_payments' ) );

		add_action( 'wp_ajax_get_coupons', array( $this,'get_coupons' ) );
		add_action( 'wp_ajax_nopriv_get_coupons', array( $this,'get_coupons' ) );

		add_action( 'wp_ajax_get_products', array( $this,'get_products' ) );
		add_action( 'wp_ajax_nopriv_get_products', array( $this,'get_products' ) );

		add_action( 'wp_ajax_get_categories', array( $this,'get_categories' ) );
		add_action( 'wp_ajax_nopriv_get_categories', array( $this,'get_categories' ) );

		add_action( 'wp_ajax_display_orders_by_period', array( $this,'display_orders_by_period' ) );
		add_action( 'wp_ajax_nopriv_display_orders_by_period', array( $this,'display_orders_by_period' ) );

	}


	public function periodFilter( $period ){

		global $wpdb;
		$theperiod = ( isset( $period ) && $period =='month'  ) ? '%Y-%m' : '%Y' ;
		if( OrderUtil::custom_orders_table_usage_is_enabled() ) {

			$query = "SELECT DISTINCT DATE_FORMAT(date_created_gmt, '{$theperiod}' ) AS period FROM ".$wpdb->prefix."wc_orders where type='shop_order' GROUP BY period ORDER BY period DESC";

		}else{

			$query = "SELECT DISTINCT DATE_FORMAT(post_date, '{$theperiod}' ) AS period FROM ".$wpdb->prefix."posts where post_type='shop_order' GROUP BY period ORDER BY period DESC";
		}
		$periods = $wpdb->get_results( $query );
		if( $periods ) {
			return $periods ;
		}

	}

	public function display_orders_by_period() {

		if( $_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] == 'display_orders_by_period' ){

			global $wpdb;

			$default_status = ['wc-completed', 'wc-processing', 'wc-on-hold', 'wc-refunded' ];
			$status = get_option( $this->plugin.'_status' , $default_status );

			// POST VARIABLES FROM FILTER FORM


			$customer_id = (empty($_POST['customer'])) ? null : sanitize_text_field( $_POST['customer'] );
			$order_status = (empty($_POST['order_status'])) ?  $status :  [ $_POST['order_status'] ];

			$period = ( isset( $_POST['tab'] ) && $_POST['tab'] =='months'  ) ? '%Y-%m' : '%Y' ;



			// Query completed orders with order date and total sales.
			if( OrderUtil::custom_orders_table_usage_is_enabled() ) {

				$query = "SELECT DATE_FORMAT( date_created_gmt, '{$period}' ) AS period, ";


					$query .= "
						SUM(orders.total_amount ) AS total,
						COUNT(orders.id) AS num_orders,
						SUM(orders.tax_amount) AS tax,
						COUNT(meta_refunds.meta_value) AS refund_count,
						SUM(meta_refunds.meta_value) AS refund ,
						SUM(operational_data.shipping_total_amount) AS shipping,
						SUM(operational_data.discount_total_amount) AS discount 	";


				$query .= "
					FROM {$wpdb->prefix}wc_orders AS orders
					LEFT JOIN {$wpdb->prefix}wc_orders_meta AS meta_refunds ON ( orders.id = meta_refunds.order_id OR orders.parent_order_id = meta_refunds.order_id ) AND meta_refunds.meta_key = '_refund_amount'
					LEFT JOIN {$wpdb->prefix}wc_order_operational_data AS operational_data ON orders.id = operational_data.order_id ";


				$query .= " WHERE orders.type IN( 'shop_order','shop_order_refund' ) AND  orders.status IN ('" . implode("','", $order_status) . "') ";


				// Add the customer ID filter if provided
				if ($customer_id) {
					$query .= " AND orders.customer_id = '{$customer_id}' ";
				}

			}else{

				$query = "SELECT DATE_FORMAT( post_date, '{$period}' ) AS period,

						   SUM( meta_total.meta_value )  AS total,
						   SUM( meta_shipping.meta_value) AS shipping,
						   SUM( meta_tax.meta_value) AS tax,
						   SUM( meta_discount.meta_value) AS discount,
						   count(ID) AS num_orders,
						   SUM(  meta_refunds.meta_value) AS refund,
						   COUNT(meta_refunds.meta_value) AS refund_count

						   FROM {$wpdb->prefix}posts
						   ";


					$query .= "
						LEFT JOIN {$wpdb->prefix}postmeta AS meta_total ON {$wpdb->prefix}posts.ID = meta_total.post_id AND meta_total.meta_key = '_order_total'
						LEFT JOIN {$wpdb->prefix}postmeta AS meta_tax ON {$wpdb->prefix}posts.ID = meta_tax.post_id AND meta_tax.meta_key = '_order_tax'
						LEFT JOIN {$wpdb->prefix}postmeta AS meta_shipping ON {$wpdb->prefix}posts.ID = meta_shipping.post_id AND meta_shipping.meta_key = '_order_shipping'
						LEFT JOIN {$wpdb->prefix}postmeta AS meta_refunds ON ( {$wpdb->prefix}posts.ID = meta_refunds.post_id OR {$wpdb->prefix}posts.post_parent = meta_refunds.post_id ) AND {$wpdb->prefix}posts.post_type = 'shop_order_refund' AND meta_refunds.meta_key = '_refund_amount'
						LEFT JOIN {$wpdb->prefix}postmeta AS meta_discount ON {$wpdb->prefix}posts.ID = meta_discount.post_id AND meta_discount.meta_key = '_cart_discount' ";



				if ( $customer_id ) {
					$query .= " LEFT JOIN {$wpdb->prefix}postmeta AS meta_customer ON ( {$wpdb->prefix}posts.ID = meta_customer.post_id OR {$wpdb->prefix}posts.post_parent = meta_customer.post_id ) AND meta_customer.meta_key = '_customer_user'  ";
				}
				$query .= "
					WHERE post_type IN(  'shop_order','shop_order_refund' ) AND {$wpdb->prefix}posts.post_status IN ('" . implode("','", $order_status) . "')";

				// Add the product_id filter if provided
				if ( $product_id ) {
					$query .= "  AND product.meta_value = '{$product_id}' OR variation.meta_value = '{$product_id}' ";
				}

				// Add the customer ID filter if provided
				if ($customer_id) {
					$query .= " AND meta_customer.meta_value = '{$customer_id}'";
				}

			}
			$query .= " GROUP BY period ORDER BY period DESC ";

			$results = $wpdb->get_results( $query );

			$message = '';
			$response = array(
				'name' => array(),
				'total' => array(),
				'totals' => '',
				'results' => '',
				'forecast' => '',
				'average' => '',
				'periods' => '',
				'message' => '',
			);

			// Display the results in a table.
			if( $results ){

				$tax_amount=0;
				$total_sales=0;
				$num_orders=0;
				$refunds=0;
				$shipping=0;
				$discount=0;
				$net = 0;
				$totals = array();

				if(isset($_POST['order_status']) && !empty($_POST['order_status'])){
					$order_status =  [ $_POST['order_status'] ] ;
					$message = "<h3> ". esc_html__('Orders with Status',$this->plugin) ." ".esc_html( implode("','", $order_status) )." </h3>";
				}

				if(isset($_POST['customer']) && !empty($_POST['customer'])){
					$user = get_user_by( 'id', $_POST['customer'] );
					$message .= "<h3> for ". esc_html( $user->first_name ) . " " . esc_html( $user->last_name ) . " </h3>";
				}



				foreach( $results as $row ) {

					$tax_amount += $row->tax;
					$num_orders += $row->num_orders ;
					$refunds += $row->refund;
					$shipping += $row->shipping;
					$discount += $row->discount;


					$net += $row->total - $row->tax - $row->shipping + $row->discount;
					$total_sales += $row->total;
					$topush = $row->total + $row->refund - $row->tax - $row->shipping;
					if( $topush == 0 ) $topush = 0.1;


					array_push( $totals , $topush );

					$number_orders =  (  $row->num_orders =='1' ) ?  $row->num_orders : $row->num_orders;

					if ( $product_id || $product_category ) {

						$thegross = $row->total + $row->tax + $row->shipping;
						$thenet  = $row->total;


					}else{

						$thegross = $row->total + $row->refund;
						$thenet  = $row->total + $row->refund - $row->tax - $row->shipping;

					}

					$response['periods'] .= "<tr><td>". esc_html( $row->period )  . "</td><td>". esc_html( $number_orders )  . "</td><td>". wc_price( $row->tax ) . "</td><td>".  wc_price( $row->shipping )  . "</td><td>".   wc_price( $row->discount )  . "</td><td>". wc_price( $row->refund ) . "</td><td>". wc_price( $thegross ) . "</td><td>". wc_price( $thenet ) . "</td></tr>";

					array_push( $response['name'] , esc_html( $row->period ) );

				}

				$total_sales = ( $total_sales <0 ) ? $total_sales = 0 :  $total_sales;
				$net = ( $net <0 ) ? $net = 0 :  $net;

				$response['totals'] .= '<td>TOTALS</td><td>' . esc_html( $num_orders ). '</td><td class="tax" >' . wc_price( $tax_amount ). '</td><td class="shipping">' . wc_price( $shipping ). '</td><td class="discount">' . wc_price( $discount ). '</td><td class="refund">' . wc_price( $refunds ). '</td><td class="gross">' . wc_price( $total_sales ). '</td><td class="net">' . wc_price( $net ). '</td>';

				$average = $total_sales / count( $results );


				if( array_sum ($totals ) != '' && count(  $totals ) >1 ){
					$forecast  = ( $period =='month'  ) ? $this->forecastHoltWinters( array_reverse( $totals ) , 2, 4 )[0] : $this->forecastHoltWinters( array_reverse( $totals ) , 1, 1 )[0] ;
				}else $forecast = $average;

				$response['average'] =  wc_price( $average ) ;
				$response['forecast'] =  wc_price( $forecast ) ;
				$response['total'] = $totals ;
				$response['message'] = $message ;
				$response['results'] = count( $results ) ;

			}else{

				$nomessage = "<h3> ". esc_html__( 'No Orders ',$this->plugin) . "</h3>" ;

				if(isset($_POST['order_status']) && !empty($_POST['order_status'])){
					$order_status = sanitize_text_field( $_POST['order_status'] );
					$nomessage .= "<h3> ". esc_html__(' with Status: ',$this->plugin)  . esc_html( $order_status ). "</h3>" ;
				}

				if( isset( $_POST['customer'] ) && !empty( $_POST['customer'] ) ){
					$user = get_user_by( 'id', $_POST['customer'] );
					$nomessage .= "<h3> ". esc_html( ' for customer: ' . $user->first_name." " .$user->last_name ) . "</h3>" ;
				}


				$response['message'] = $nomessage;
				$response['results'] = 0 ;

			}

			echo json_encode( $response );

			wp_die();

		}
	}


	public function filter_orders(){

		$filters = array();

			$date = current_time( 'mysql' ) ;
			$today = date('Y-m-d', strtotime( $date ) );

			if( isset( $_POST['tab'] ) && ( $_POST['tab'] == 'months' || $_POST['tab'] == 'years' ) ){
				$default = date( 'Y-m-d' , strtotime( $date . "- 20 years" ) );

			}else{
				$default = date( 'Y-m-d' , strtotime( "first day of this month" ) );
			}

			if( !empty( $_POST['selected'] ) ){

				$dayfilter = array( 'date_created' =>  sanitize_text_field( $_POST['selected'] ) . "..." . $today ); //'date_created' =>  $from . "..." . $to
			}else{
				$dayfilter = array( 'date_created' =>  ">=" . $default ); //'date_created' =>  $from . "..." . $to
			}

			$status = array();

			$default_status = ['wc-completed', 'wc-processing', 'wc-on-hold', 'wc-refunded' ];
			$status = get_option( $this->plugin.'_status' , $default_status );


			$customer = (empty( $_POST['customer'] ) ) ? '' : $_POST['customer'];
			$order_status = ( empty( $_POST['order_status'] ) ) ?  $status : $_POST['order_status'];


			$filters = [

				'customer_id' => sanitize_text_field( $customer ),
				'status' =>  $order_status ,

			];

			 $filters = array_merge(  $dayfilter , $filters );

		return $filters;

	}



	public function getOrders() {

		if( $_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] == 'getOrders'   ){

			global $woocommerce;


			$args = array(
				'type' => 'shop_order',
				'paginate' => true,
			);
			$args = array_merge( $args , $this->filter_orders() );

			$results = wc_get_orders( $args );

			if( isset( $_POST['offset'] ) ){
			   $offset = $_POST['offset'];
			}else $offset = 0;
			$limit = get_option( $this->plugin."queryLimit" , 500 );

			$args = array(
				'limit' => sanitize_text_field( $limit ),
				'offset' => sanitize_text_field( $offset ),
				'orderby' => 'date', //has no effect as its a meta field
				'order' => 'DESC',
				'type' => 'shop_order',
			);

			$args = array_merge( $args , $this->filter_orders() );

			error_log( serialize( $args ) );

			if( strstr( $args['date_created'], "..." ) ){

				$dates = explode( "..." , $args['date_created'] );
				$datediff = strtotime( $dates[1] ) - strtotime( $dates[0] );
			}else{
				$dates = explode( ">=" , $args['date_created'] );
				$from  = strtotime( $dates[1] );
				$date = current_time( 'mysql' ) ;
				$to = date('Y-m-d', strtotime( $date ) );
				$datediff =   strtotime( $to ) - $from ;

			}


			$resultss = wc_get_orders( $args );

			$orders = array();
			$orderIds = array();
			$order_data = array();

			$date = current_time( 'mysql' ) ;
			$today = date('Y-m-d', strtotime( $date ) );

			$message ='';

			if(isset($_POST['order_status']) && !empty($_POST['order_status'])){
				$order_status =  [ $_POST['order_status'] ] ;
				$message = "<h3> ". esc_html__('Orders with Status',$this->plugin) ." ".esc_html( implode("','", $order_status) )." </h3>";
			}

			if( isset( $_POST['tab'] ) && $_POST['tab'] == 'all' || !isset( $_POST['tab'] ) ){

				if( isset($_POST['selected']) && !empty($_POST['selected'])){
					$message .= "<h3>". esc_html__('Analysis for period',$this->plugin)." ".date('d/m/Y',strtotime(esc_attr($_POST['selected'])))." to ".date('d/m/Y')."</h3>";

					$this->datediff = strtotime(date('Y-m-d')) - strtotime($_POST['selected']);
					$this->datediff = abs(round( $this->datediff / (60 * 60 * 24)));

				}else{
				   $message .= "<h3>". esc_html__( 'This Month Analysis',$this->plugin)."</h3>";
					$this->datediff = strtotime( $today ) - strtotime(date('Y-m-01'));
					$this->datediff = round( $this->datediff / (60 * 60 * 24));

				}

			}
			error_log($this->datediff );

			if(isset($_POST['customer']) && !empty($_POST['customer'])){
				$user = get_user_by( 'id', $_POST['customer'] );
				$message .= "<h3> for ". esc_html( $user->first_name ) . " " . esc_html( $user->last_name ) . " </h3>";
			}

			if(isset($_POST['billing_city']) && !empty($_POST['billing_city'])){
				$message .= "<h3> for ".esc_html( $_POST['city'] )." </h3>";
			}


			if( $resultss ){

				foreach( $resultss as $order ){

					if( isset($_POST['cat']) && !empty($_POST['cat']) || isset($_POST['product']) && !empty($_POST['product'])  ){

						array_push( $orderIds, $order->get_id() );
					}

					if(  !empty( $orderIds )){
						$order_data['total_orders'] = count( $orderIds );
					}else $order_data['total_orders'] = $results->total;

					if( ( !empty( $orderIds ) && in_array( $order->get_id(), $orderIds ) ) || empty( $orderIds ) ){

						$order_data['message'] = sanitize_text_field( $message ) ;
						$order_data['days'] = round($datediff / (60 * 60 * 24));

						//$order_data['days'] = $this->datediff;

						$order_data['order_id'] = $order->get_id();

						$order_data['date_created'] = ( method_exists( $order, 'get_date_created' ) ) ? date( "Y-m-d", strtotime( $order->get_date_created() ) ): '';

						$order_data['subtotal'] = ( method_exists( $order, 'get_subtotal' ) ) ? $order->get_subtotal()  : '';
						$order_data['total'] = ( method_exists( $order, 'get_total' ) ) ? $order->get_total()  : '';
						$order_data['total_tax'] = ( method_exists( $order, 'get_total_tax' ) ) ? $order->get_total_tax() : '';
						$order_data['total_refunded'] = ( method_exists( $order, 'get_total_refunded' ) ) ? $order->get_total_refunded() : '';
						$order_data['total_discount'] = ( method_exists( $order, 'get_total_discount' ) ) ? $order->get_total_discount() : '';
						$order_data['shipping_total'] = ( method_exists( $order, 'get_shipping_total' ) ) ? $order->get_shipping_total()  : '';

						if( $order->get_total() - $order->get_total_refunded() == 0 ){
							$order_data['subtotal'] = 0;
							$order_data['total'] = 0;
							$order_data['total_tax']  = 0;
							$order_data['total_discount']  = 0;
							$order_data['shipping_total']  = 0;
						}

						$quantity = array();
						$products = array();
						$categories = array();

						foreach ( $order->get_items() as $item_id => $item ) {

							$product_id = ( $item->get_variation_id() !=0 ) ? $item->get_variation_id() : $item->get_product_id();

							array_push( $quantity , $item->get_quantity() );

							$products[] = array( 'name'=> get_the_title( $product_id ), 'quantity'=>$item->get_quantity(), 'total'=>$item->get_subtotal() , 'sku'=> get_post_meta( $product_id ,'_sku', true ) );

							$terms = wp_get_post_terms( $product_id,'product_cat');
							foreach ( $terms as $term ) {
								$categories[] = array( 'discount'=> $order->get_total_discount(), 'refund'=> $order->get_total_refunded(), "name"=>$term->name,"quantity"=>$item->get_quantity(),"total"=>$item->get_subtotal()  );
							}


						}
						$order_data['quantity'] = $quantity;
						$order_data['products'] = $products;

						array_push( $orders , $order_data );

					}

				}

				echo json_encode( $orders );

			}else{

				$nodata = array();

				$todayDisplay = date('d/m/Y', strtotime( $date ) );

				$nomessage = "<h3> ". esc_html__( 'No Orders ',$this->plugin) . "</h3>" ;

				if(isset($_POST['order_status']) && !empty($_POST['order_status'])){
					$order_status = sanitize_text_field( $_POST['order_status'] );
					$nomessage .= "<h3> ". esc_html__(' with Status: ',$this->plugin)  . esc_html( $order_status ). "</h3>" ;
				}

				if( isset( $_POST['tab'] ) && $_POST['tab'] == 'all' || !isset( $_POST['tab'] ) ){

					if( isset($_POST['selected']) && !empty($_POST['selected'])){

						$nomessage .= "<h3> ".esc_html__( ' for ',$this->plugin).date( 'd/m/Y',strtotime( esc_attr( $_POST['selected'] ) ) )." ".esc_html__( 'to', $this->plugin ) ." ". esc_html( $todayDisplay ) . "</h3>" ;


					}else $nomessage .="<h3> ".esc_html__( ' for ',$this->plugin) . date('F'). "</h3>" ;

				}



				if( isset( $_POST['customer'] ) && !empty( $_POST['customer'] ) ){
					$user = get_user_by( 'id', $_POST['customer'] );
					$nomessage .= "<h3> ". esc_html( ' for customer: ' . $user->first_name." " .$user->last_name ) . "</h3>" ;
				}


				$nodata['message'] = $nomessage;
				$nodata['total_orders'] = 0;
				array_push( $orders , $nodata );

				echo json_encode( $orders );

			}

			wp_die();

		}

	}

	public function get_orders() {

		if( is_admin() && isset( $_POST['action'] ) &&  $_POST['action'] =='get_orders' ){


			if( isset( $_POST['page'] ) ) $page = $_POST['page'];
			if( isset( $_POST['ids'] ) ) $ids = $_POST['ids'];

			$ids = array_map( 'sanitize_text_field', $ids );


			$args = array(
					'paginate' => true,
					'limit' =>100,
					'page' => $page,
					'post__in' => $ids,
					'orderby' => 'date',
					'order' => 'DESC',
			);


			$orders = wc_get_orders( $args );



			$query = $orders->orders;
			$response = array(
				'max_num_pages' => $orders->max_num_pages,
				'orders' => '',
			);


			if( !empty( $orders ) ) {

				foreach( $query as $order ) {


							 $response['orders'] .= "<tr>";

								$id  = $order->get_id();
								$date  = date( "d-m-Y", strtotime( $order->get_date_created() ) );
								$payment  = ( method_exists( $order, 'get_payment_method_title' ) ) ? $order->get_payment_method_title() : '';
								$coupons  = $this->get_coupon_used( $order->get_id() );
								$first_name  = ( method_exists( $order, 'get_billing_first_name' ) ) ? $order->get_billing_first_name() : '';
								$last_name  = ( method_exists( $order, 'get_billing_first_name' ) ) ? $order->get_billing_last_name() : '';
								$country  = ( method_exists( $order, 'get_billing_country' ) ) ? WC()->countries->countries[ $order->get_billing_country() ] : '' ;
								$discount  = ( method_exists( $order, 'get_total_discount' ) ) ? $order->get_total_discount() : '';
								$shipping  = ( method_exists( $order, 'get_shipping_total' ) ) ? $order->get_shipping_total()  : '';
								$tax  = ( method_exists( $order, 'get_total_tax' ) ) ? $order->get_total_tax() : '';
								$total  = ( method_exists( $order, 'get_total' ) ) ? $order->get_total()  : '';
								$refunds  = ( method_exists( $order, 'get_total_refunded' ) ) ? $order->get_total_refunded() : '';
								$net  = ( method_exists( $order, 'get_subtotal' ) ) ? $order->get_subtotal()  : '';

							$response['orders'] .= "<tr><td>". $id  . "</td><td>". esc_html( $date ) . "</td><td>". esc_html( $payment )  . "</td><td>". esc_html( $coupons ) . "</td><td>". esc_html( $first_name  . "  ". $last_name ) . "</td><td>". esc_html( $country ) . "</td><td>". wc_price( $discount ) . "</td><td>". wc_price( $shipping )  . "</td><td>".  wc_price( $tax ) . "</td><td>".  wc_price( $total ) . "</td><td>".  wc_price( $refunds ) . "</td><td>".  wc_price( $net )  . "</td></tr>";

				}
			}


			echo json_encode( $response );
			wp_die();


		}
	}

	public function get_customers() {

		if( is_admin() && isset( $_POST['action'] ) &&  $_POST['action'] =='get_customers'  ){

			global $wpdb;

			if( isset( $_POST['ids'] ) ) $ids = $_POST['ids'];
			$ids = array_map( 'sanitize_text_field', $ids );

			// Query completed orders with order date and total sales.

			if( OrderUtil::custom_orders_table_usage_is_enabled() ) {

				$parameter = 'billing_email';

				$query = "SELECT orders.".$parameter." AS data,
						   SUM(orders.total_amount ) AS total,
						   SUM( orders.tax_amount ) AS tax,
						   COUNT(orders.id) AS num_orders,
						   address.first_name as first_name,
						   address.last_name as last_name,
						   address.company as company,
						   address.city as city,
						   address.state as state,
						   address.email as email,
						   address.phone as phone,
						   address.country as country
						   FROM ". $wpdb->prefix."wc_orders as orders
						   LEFT JOIN " . $wpdb->prefix ."wc_order_addresses as address ON orders.id = address.order_id AND orders.".$parameter." = address.email
						WHERE address.address_type='billing' AND orders.id  IN ('" . implode("','", $ids ) . "') ";

			$query .= "
				GROUP BY email
				ORDER BY total DESC
			";

			}else{

				$query="
					SELECT
						billing_email.meta_value AS billing_email,
						billing_first_name.meta_value AS first_name,
						billing_last_name.meta_value AS last_name,
						billing_country.meta_value AS country,
						billing_city.meta_value AS city,
						billing_phone.meta_value AS phone,
						billing_state.meta_value AS state,
						billing_company.meta_value AS company,
						SUM(order_total.meta_value) AS total,
						SUM(order_tax.meta_value) AS tax,
						COUNT( p.ID ) AS num_orders
					FROM
						". $wpdb->prefix."posts AS p
					LEFT JOIN  ". $wpdb->prefix."postmeta AS billing_email ON p.ID = billing_email.post_id AND billing_email.meta_key = '_billing_email'
					LEFT JOIN  ". $wpdb->prefix."postmeta AS billing_first_name ON p.ID = billing_first_name.post_id AND billing_first_name.meta_key = '_billing_first_name'
					LEFT JOIN ". $wpdb->prefix."postmeta AS billing_last_name ON p.ID = billing_last_name.post_id AND billing_last_name.meta_key = '_billing_last_name'
					LEFT JOIN ". $wpdb->prefix."postmeta AS billing_country ON p.ID = billing_country.post_id AND billing_country.meta_key = '_billing_country'
					LEFT JOIN ". $wpdb->prefix."postmeta AS billing_city ON p.ID = billing_city.post_id AND billing_city.meta_key = '_billing_city'
					LEFT JOIN ". $wpdb->prefix."postmeta AS billing_phone ON p.ID = billing_phone.post_id AND billing_phone.meta_key = '_billing_phone'
					LEFT JOIN ". $wpdb->prefix."postmeta AS billing_state ON p.ID = billing_state.post_id AND billing_state.meta_key = '_billing_state'
					LEFT JOIN ". $wpdb->prefix."postmeta AS billing_company ON p.ID = billing_company.post_id AND billing_company.meta_key = '_billing_company'
					LEFT JOIN ". $wpdb->prefix."postmeta AS order_total ON p.ID = order_total.post_id AND order_total.meta_key = '_order_total'
					LEFT JOIN ". $wpdb->prefix."postmeta AS order_tax ON p.ID = order_tax.post_id AND order_tax.meta_key = '_order_tax'
					WHERE p.ID  IN ('" . implode("','", $ids ) . "')
				";
				$query .= "
					GROUP BY billing_email.meta_value
					ORDER BY total DESC
				";
			}

			$data = $wpdb->get_results( $query );

			$response = array(
				'customers' => '',
			);

			if( $data ){

				foreach( $data as $d ){

						$name  = $d->first_name . " " . $d->last_name;
						$state  = $d->state;
						$city  = $d->city;
						$company  = $d->company;
						$country  = ( $d->country != '' ) ? WC()->countries->countries[ $d->country ] : '' ;
						$phone  = $d->phone;
						$email  = $d->email;
						$tax  = $d->state;
						$total  = $d->total;
						$tax  = $d->tax;
						$num_orders  = $d->num_orders;

						$response['customers'] .= "<tr><td>". esc_html( $name ) . "</td><td>". esc_html( $phone ) . "</td><td>". esc_html( $email ) . "</td><td>". esc_html( $country ) . "</td><td>". esc_html( $state ) . "</td><td>". esc_html( $city ). "</td><td>". esc_html( $company ) . "</td><td>". esc_html( $num_orders ) . "</td><td>".  esc_html( round( $tax , 2 ) ) . "</td><td>". esc_html(  round( $total , 2 ) ) . "</td></tr>";
				}
			}

			echo json_encode( $response );
			wp_die();


		}
	}

	public function get_countries() {

		if( is_admin() && isset( $_POST['action'] ) &&  $_POST['action'] =='get_countries' ){

			global $wpdb;

			if( isset( $_POST['ids'] ) ) $ids = $_POST['ids'];
			$ids = array_map( 'sanitize_text_field', $ids );

			// Query completed orders with order date and total sales.
			if( OrderUtil::custom_orders_table_usage_is_enabled() ) {
				$parameter = 'country';
				$query = "SELECT DISTINCT address.".$parameter." AS country,
						   SUM(orders.total_amount ) AS total,
						   SUM( orders.tax_amount ) AS tax,
						   COUNT(orders.id) AS num_orders,
						   address.country as country
						   FROM ". $wpdb->prefix."wc_order_addresses as address
						   LEFT JOIN " . $wpdb->prefix ."wc_orders as orders ON orders.id = address.order_id
						WHERE address.address_type='billing' AND orders.id  IN ('" . implode("','", $ids ) . "') ";

			}else{
				$parameter = 'country';

				$query = "
					SELECT billing_country.meta_value AS ".$parameter." ,
					SUM(order_total.meta_value) AS total,
					SUM(order_tax.meta_value) AS tax,
					COUNT( order_total.post_id ) AS num_orders
					FROM {$wpdb->prefix}postmeta AS order_total
					LEFT JOIN {$wpdb->prefix}postmeta AS order_tax ON order_total.post_id = order_tax.post_id
					LEFT JOIN {$wpdb->prefix}postmeta AS billing_country ON order_total.post_id = billing_country.post_id
					WHERE order_total.post_id  IN ('" . implode("','", $ids ) . "') AND
						order_total.meta_key = '_order_total' AND
						order_tax.meta_key = '_order_tax' AND
						billing_country.meta_key = '_billing_country'
					";

			}
			$query .= "
				GROUP BY country
				ORDER BY total DESC
			";
			$data = $wpdb->get_results( $query );


			$response = array(
				'name' => array(),
				'total' => array(),
				'countries' => '',
			);

			if( $data ){

				foreach( $data as $d ){

						$country  = ( $d->country != '' ) ? WC()->countries->countries[ $d->country ] : '' ;
						$total  = $d->total;
						$tax  = $d->tax;
						$num_orders  = $d->num_orders;
						$response['countries'] .= "<tr><td>". esc_html( $country ) . "</td><td>". esc_html( $num_orders ) . "</td><td>".  esc_html( round( $tax , 2 ) ) . "</td><td>".  esc_html( round( $total , 2 ) ) . "</td></tr>";
						array_push( $response['name'] , esc_html( $country ) );
						array_push( $response['total'] , round( $total , 2 ) );
				}
			}


			echo json_encode( $response );
			wp_die();


		}
	}


	public function get_payments() {

		if( is_admin() && isset( $_POST['action'] ) &&  $_POST['action'] =='get_payments' ){

			global $wpdb;

			if( isset( $_POST['ids'] ) ) $ids = $_POST['ids'];
			$ids = array_map( 'sanitize_text_field', $ids );

			// Query completed orders with order date and total sales.

			if( OrderUtil::custom_orders_table_usage_is_enabled() ) {
				$parameter = 'payment_method_title';
				$query = "SELECT DISTINCT orders.".$parameter." AS payment,
						   SUM(orders.total_amount ) AS total,
						   SUM( orders.tax_amount ) AS tax,
						   COUNT(orders.id) AS num_orders
						   FROM ". $wpdb->prefix."wc_orders as orders
						WHERE orders.id  IN ('" . implode("','", $ids ) . "') ";

			}else{

				$parameter = '_payment_method_title';

				$query = "
					SELECT payment_method.meta_value AS payment,
					SUM(order_total.meta_value) AS total,
					SUM(order_tax.meta_value) AS tax,
					COUNT( order_total.post_id ) AS num_orders
					FROM {$wpdb->prefix}postmeta AS order_total
					LEFT JOIN {$wpdb->prefix}postmeta AS order_tax ON order_total.post_id = order_tax.post_id
					LEFT JOIN {$wpdb->prefix}postmeta AS payment_method ON order_total.post_id = payment_method.post_id
					WHERE order_total.post_id  IN ('" . implode("','", $ids ) . "') AND
						order_total.meta_key = '_order_total' AND
						order_tax.meta_key = '_order_tax' AND
						payment_method.meta_key = '_payment_method_title'
					";


			}
			$query .= "
				GROUP BY payment
				ORDER BY total DESC
			";
			$data = $wpdb->get_results( $query );


			$response = array(
				'name' => array(),
				'total' => array(),
				'payments' => '',
			);

			if( $data ){

				foreach( $data as $d ){

						$payment  = $d->payment;
						$total  = $d->total;
						$tax  = $d->tax;
						$num_orders  = $d->num_orders;
						$response['payments'] .= "<tr><td>". esc_html( $payment ) . "</td><td>". esc_html( $num_orders ) . "</td><td>".  esc_html( round( $tax , 2 ) ) . "</td><td>". esc_html( round( $total , 2 ) ) . "</td></tr>";
						array_push( $response['name'] , esc_html( $payment ) );
						array_push( $response['total'] , round( $total , 2 ) );
				}
			}


			echo json_encode( $response );
			wp_die();


		}
	}

	public function get_coupons() {

		if( is_admin() && isset( $_POST['action'] ) &&  $_POST['action'] =='get_coupons'  ){

			global $wpdb;

			if( isset( $_POST['ids'] ) ) $ids = $_POST['ids'];
			$ids = array_map( 'sanitize_text_field', $ids );

			// Query completed orders with order date and total sales.
			if( OrderUtil::custom_orders_table_usage_is_enabled() ) {
				$parameter = 'coupon_id';
				$query = "SELECT DISTINCT coupons.".$parameter." AS coupon,
						   SUM(coupons.discount_amount ) AS total
						   FROM ". $wpdb->prefix."wc_order_coupon_lookup as coupons
						WHERE coupons.order_id  IN ('" . implode("','", $ids ) . "') ";

			}else{
				$parameter = 'coupon';
				$query = "
					SELECT oi.order_item_name AS ".$parameter." ,
					SUM(im.meta_value) AS total
					FROM {$wpdb->prefix}woocommerce_order_itemmeta AS im
					JOIN {$wpdb->prefix}woocommerce_order_items AS oi ON im.order_item_id = oi.order_item_id
					WHERE im.meta_key = 'discount_amount' AND oi.order_item_type = 'coupon' AND oi.order_id  IN ('" . implode("','", $ids ) . "')
				";

			}
			$query .= "
				GROUP BY coupon
				ORDER BY total DESC
			";
			$data = $wpdb->get_results( $query );


			$response = array(
				'name' => array(),
				'total' => array(),
				'coupons' => '',
			);

			if( $data ){

				foreach( $data as $d ){

						$coupon  = $d->coupon;
						global $woocommerce;
						$coupon = new WC_Coupon( $coupon );
						$coupon = $coupon->get_code();
						$total  = $d->total;
						$response['coupons'] .= "<tr><td>".  esc_html( $coupon ) . "</td><td>".   esc_html(  round( $total , 2 ) ) . "</td></tr>";
						array_push( $response['name'] ,  esc_html( $coupon ) );
						array_push( $response['total'] , round( $total , 2 ) );
				}
			}


			echo json_encode( $response );
			wp_die();


		}
	}

	public function get_products() {

		if( is_admin() && isset( $_POST['action'] ) &&  $_POST['action'] =='get_products'  ){

			global $wpdb;

			if( isset( $_POST['ids'] ) ) $ids = $_POST['ids'];
			$ids = array_map( 'sanitize_text_field', $ids );

			$prod = ( !empty( $_POST['product'] ) ) ? sanitize_text_field( $_POST['product'] ) : null;
			$cat = ( !empty( $_POST['cat'] ) ) ? sanitize_text_field( $_POST['cat'] ) : null;

			// Query completed orders with order date and total sales.
			if( OrderUtil::custom_orders_table_usage_is_enabled() ) {
				$query = "SELECT DISTINCT product_id as product, variation_id as variation,
						  SUM(products.product_net_revenue ) AS total,
						  SUM(products.product_qty ) AS num_products
						  FROM ". $wpdb->prefix."wc_order_product_lookup as products
						  ";

				if( $cat != null ){
					$query .= "

						LEFT JOIN {$wpdb->prefix}term_relationships AS rel ON  ( product_id = rel.object_id || variation_id = rel.object_id )
						LEFT JOIN {$wpdb->prefix}term_taxonomy AS tax ON rel.term_taxonomy_id = tax.term_taxonomy_id AND  tax.taxonomy = 'product_cat'
						LEFT JOIN {$wpdb->prefix}terms AS terms ON tax.term_id = terms.term_id

					";

				}
				$query .= " WHERE products.order_id  IN ('" . implode("','", $ids ) . "')  ";
				if( $prod != null )	$query .= " AND ( product_id = ". $prod  ." OR variation_id = ". $prod  ." ) ";

			}else{

				$query = "
				SELECT
					products.meta_value as product,
					variations.meta_value as variation,
					SUM(order_itemmeta_line_subtotal.meta_value) as total,
					SUM(order_itemmeta_line_tax.meta_value) as tax,
					SUM( qty.meta_value )  AS num_products
				FROM
					{$wpdb->prefix}woocommerce_order_items as items
				JOIN
					{$wpdb->prefix}woocommerce_order_itemmeta as products ON items.order_item_id = products.order_item_id AND products.meta_key = '_product_id'
				LEFT JOIN
					{$wpdb->prefix}woocommerce_order_itemmeta as variations ON items.order_item_id = variations.order_item_id AND variations.meta_key = '_variation_id'
				JOIN
					{$wpdb->prefix}woocommerce_order_itemmeta as order_itemmeta_line_subtotal ON items.order_item_id = order_itemmeta_line_subtotal.order_item_id AND order_itemmeta_line_subtotal.meta_key = '_line_subtotal'
				JOIN
					{$wpdb->prefix}woocommerce_order_itemmeta as order_itemmeta_line_tax ON items.order_item_id = order_itemmeta_line_tax.order_item_id AND order_itemmeta_line_tax.meta_key = '_line_tax'
				JOIN
					{$wpdb->prefix}woocommerce_order_itemmeta as qty ON items.order_item_id = qty.order_item_id AND qty.meta_key = '_qty'
				";
					if( $cat != null ){
						$query .= "

							LEFT JOIN {$wpdb->prefix}term_relationships AS rel ON  ( products.meta_value = rel.object_id || variations.meta_value = rel.object_id )
							LEFT JOIN {$wpdb->prefix}term_taxonomy AS tax ON rel.term_taxonomy_id = tax.term_taxonomy_id AND  tax.taxonomy = 'product_cat'
							LEFT JOIN {$wpdb->prefix}terms AS terms ON tax.term_id = terms.term_id

						";

					}

				$query .= " WHERE items.order_id  IN ('" . implode("','", $ids ) . "') ";
				if( $prod != null )	$query .= " AND ( products.meta_value = ". $prod  ." OR variations.meta_value = ". $prod  ." ) ";
			}

			if( $cat != null ) 	 $query .= " AND terms.term_id = ".$cat;


			$query .= "
				GROUP BY product, variation
				ORDER BY total DESC
			";
			$data = $wpdb->get_results( $query );


			$response = array(
				'name' => array(),
				'total' => array(),
				'products' => '',
			);

			if( $data ){

				foreach( $data as $d ){

						$product  = ( $d->product !=0 ) ? $d->product : $d->variation ;
						$sku = get_post_meta( $product, '_sku', true );
						$total  = $d->total;
						$num_products  = $d->num_products;
						$response['products'] .= "<tr><td>". get_the_title( $product )  . "</td><td>".  esc_html( $sku ) . "</td><td>".  esc_html( $num_products ) . "</td><td>".  esc_html( round( $total , 2 ) ) . "</td></tr>";
						array_push( $response['name'] ,  esc_html( get_the_title( $product ) ) );
						array_push( $response['total'] , round( $total , 2 ) );
				}
			}


			echo json_encode( $response );
			wp_die();


		}
	}


	public function get_categories() {

		if( is_admin() &&  isset( $_POST['action'] ) &&  $_POST['action'] =='get_categories'   ){

			global $wpdb;

			if( isset( $_POST['ids'] ) ) $ids = $_POST['ids'];
			$ids = array_map( 'sanitize_text_field', $ids );

			$cat = ( !empty( $_POST['cat'] ) ) ? sanitize_text_field( $_POST['cat'] ) : null;
			$prod = ( !empty( $_POST['product'] ) ) ? sanitize_text_field( $_POST['product'] ) : null;

			if( OrderUtil::custom_orders_table_usage_is_enabled() ) {

				$query = "
						SELECT DISTINCT terms.name as term ,
						 SUM(products.product_net_revenue ) AS total,
						 SUM(products.product_qty ) AS num_products
						 FROM {$wpdb->prefix}wc_order_product_lookup	AS products
						LEFT JOIN {$wpdb->prefix}term_relationships AS rel ON  ( products.product_id = rel.object_id || products.variation_id = rel.object_id )
						LEFT JOIN {$wpdb->prefix}term_taxonomy AS tax ON rel.term_taxonomy_id = tax.term_taxonomy_id AND  tax.taxonomy = 'product_cat'
						LEFT JOIN {$wpdb->prefix}terms AS terms ON tax.term_id = terms.term_id
						WHERE products.order_id  IN ('" . implode("','", $ids ) . "')  AND terms.name IS NOT NULL  ";

				if( $prod != null )	$query .= " AND ( products.product_id = ". $prod  ." OR products.variation_id = ". $prod  ." ) ";

			}else{
				$query = "
						SELECT DISTINCT terms.name as term ,
						SUM( total.meta_value  )  AS total,
						SUM( qty.meta_value )  AS num_products
						FROM {$wpdb->prefix}woocommerce_order_items as order_items
						LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS total ON order_items.order_item_id = total.order_item_id  AND total.meta_key = '_line_total'
						LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS qty ON order_items.order_item_id = qty.order_item_id  AND qty.meta_key = '_qty'
						LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS product ON order_items.order_item_id = product.order_item_id  AND product.meta_key = '_product_id'
						LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS variation ON order_items.order_item_id = variation.order_item_id  AND variation.meta_key = '_variation_id'
						LEFT JOIN {$wpdb->prefix}term_relationships AS rel ON  ( product.meta_value = rel.object_id || variation.meta_value = rel.object_id )
						LEFT JOIN {$wpdb->prefix}term_taxonomy AS tax ON rel.term_taxonomy_id = tax.term_taxonomy_id AND  tax.taxonomy = 'product_cat'
						LEFT JOIN {$wpdb->prefix}terms AS terms ON tax.term_id = terms.term_id
						WHERE  order_items.order_id  IN ('" . implode("','", $ids ) . "')  AND terms.name IS NOT NULL ";

				if( $prod != null )	$query .= " AND ( variation.meta_value = ". $prod  ." OR product.meta_value = ". $prod  ." ) ";
			}
			if( $cat != null )	$query .= " AND terms.term_id = ".$cat;


			$query .= "
				GROUP BY term
				ORDER BY total DESC
			";
			$data = $wpdb->get_results( $query );


			$response = array(
				'name' => array(),
				'total' => array(),
				'categories' => '',
			);

			if( $data ){

				foreach( $data as $d ){

						$category  = $d->term ;
						$total  = $d->total;
						$num_products  = $d->num_products;
						$response['categories'] .= "<tr><td>". esc_html( $category ) . "</td><td>". esc_html( $num_products ) . "</td><td>".  esc_html( round( $total , 2 ) ) . "</td></tr>";
						array_push( $response['name'] , esc_html( $category ) );
						array_push( $response['total'] , round( $total , 2 ) );
				}
			}


			echo json_encode( $response );
			wp_die();


		}
	}

	public function product_cat( $categories, $post ) {
			$subcats = get_term_children( $categories, 'product_cat' );
			foreach($subcats as $cat){

				 if ( has_term( $cat, 'product_cat', $post ) ) {
						return "1";
					}
			}

	}

	private function get_coupon_used( $order_id ) {

		if ( wc_coupons_enabled() ) {

			$order = wc_get_order( $order_id );
			if ( version_compare( WC()->version, '3.7', '>=' ) ) {
				$coupons = $order->get_coupon_codes();
			}else{
				$coupons = $order->get_used_coupons();
			}

			if (!empty( $coupons ) ) {
				return implode( ', ', $coupons );
			} else {
				return '';
			}

		}else return  '';
	}

	public function forecastHoltWinters($anData, $nForecast = 2, $nSeasonLength = 4, $nAlpha = 0.2, $nBeta = 0.01, $nGamma = 0.01, $nDevGamma = 0.1) {
		$search = '0';
		$replace = '1';
		array_walk($anData,
			function (&$v) use ($search, $replace){
				$v = str_replace($search, $replace, $v);
			}
		);

		$i=1;
		// Calculate an initial trend level
		$nTrend1 = '';
		for($i = 0; $i < $nSeasonLength; $i++) {
			$anData[$i] = isset($anData[1]) ? $anData[1] : null;
			//$nTrend1 += $anData[$i];
		}
		$nTrend1 = $nSeasonLength;

		$nTrend2 = 1;
		for($i = $nSeasonLength; $i < 2*$nSeasonLength; $i++) {
			$anData[$i] = isset($anData[1]) ? $anData[1] : null;
		  $nTrend2 += $anData[$i];
		}
		$nTrend2 /= $nSeasonLength;

		$nInitialTrend = ($nTrend2 - $nTrend1) / $nSeasonLength;

		// Take the first value as the initial level
		$nInitialLevel = $anData[0];

		// Build index
		$anIndex = array();
		foreach($anData as $nKey => $nVal) {
			$anIndex[$nKey] = $nVal / ($nInitialLevel + ($nKey + 1) * $nInitialTrend);
		}

		// Build season buffer
		$anSeason = array_fill(0, count($anData), 0);
		for($i = 0; $i < $nSeasonLength; $i++) {
		  $anSeason[$i] = ($anIndex[$i] + $anIndex[$i+$nSeasonLength]) / 2;
		}

		// Normalise season
		 $Total = array_sum($anSeason);
		 $Total = isset($Total) & !empty($Total) ? $Total : '0.1';

		$nSeasonFactor = $nSeasonLength / $Total;
		foreach($anSeason as $nKey => $nVal) {
		  $anSeason[$nKey] *= $nSeasonFactor;
		}

		$anHoltWinters = array();
		$anDeviations = array();
		$nAlphaLevel = $nInitialLevel;
		$nBetaTrend = $nInitialTrend;
		foreach($anData as $nKey => $nVal) {
		  $nTempLevel = $nAlphaLevel;
		  $nTempTrend = $nBetaTrend;

		  $nAlphaLevel = @($nAlpha * $nVal / $anSeason[$nKey]) + (1.0 - $nAlpha) * ($nTempLevel + $nTempTrend);
		  $nBetaTrend = $nBeta * ($nAlphaLevel - $nTempLevel) + ( 1.0 - $nBeta ) * $nTempTrend;

		  $anSeason[$nKey + $nSeasonLength] = $nGamma * $nVal / $nAlphaLevel + (1.0 - $nGamma) * $anSeason[$nKey];

		  $anHoltWinters[$nKey] = ($nAlphaLevel + $nBetaTrend * ($nKey + 1)) * $anSeason[$nKey];
		  $anDeviations[$nKey] = $nDevGamma * abs($nVal - $anHoltWinters[$nKey]) + (1-$nDevGamma)
					  * (isset($anDeviations[$nKey - $nSeasonLength]) ? $anDeviations[$nKey - $nSeasonLength] : 0);
		}

		$anForecast = array();
		$nLast = end($anData);
		for($i = 1; $i <= $nForecast; $i++) {
		   $nComputed = round($nAlphaLevel + $nBetaTrend * $anSeason[$nKey + $i]);
		   if ($nComputed < 0) { // wildly off due to outliers
			 $nComputed = $nLast;
		   }
		   $anForecast[] = $nComputed;
		}

		return $anForecast;
	}


	public function divide($a, $b){
		try {
			if(@($a / $b) === false) return INF; // covers PHP5
			return @($a / $b); // covers PHP7
		} catch (DivisionByZeroError $e) {
			return INF; // covers PHP8
		}
	}

	public function Median($Array) {
	  return Quartile_50($Array);
	}

	public function Quartile_25($Array) {
	  return Quartile($Array, 0.25);
	}

	public function Quartile_50($Array) {
	  return Quartile($Array, 0.5);
	}

	public function Quartile_75($Array) {
	  return Quartile($Array, 0.75);
	}

	public function Quartile($Array, $Quartile) {
	  $pos = (count($Array) - 1) * $Quartile;

	  $base = floor($pos);
	  $rest = $pos - $base;

	  if( isset($Array[$base+1]) ) {
		return $Array[$base] + $rest * ($Array[$base+1] - $Array[$base]);
	  } else {
		return $Array[$base];
	  }
	}

	public function Average($Array) {
	  return array_sum($Array) / count($Array);
	}

	public function StdDev($Array) {
	  if( count($Array) < 2 ) {
		return;
	  }

	  $avg = Average($Array);

	  $sum = 0;
	  foreach($Array as $value) {
		$sum += pow($value - $avg, 2);
	  }

	  return sqrt((1 / (count($Array) - 1)) * $sum);
	}

	public function random_color_part() {
		return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
	}

	public function getRandomColor() {
		return "#".$this->random_color_part() . $this->random_color_part() . $this->random_color_part();
	}


}

$OrderProcessorHelp = OrderProcessorHelp::get_instance();;
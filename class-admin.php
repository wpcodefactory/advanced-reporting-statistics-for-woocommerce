<?php
/**
 * Advanced WooCommerce Product Sales Reporting - Statistics & Forecast - webdWoocommerceReportingStatisticsAdmin Class
 *
 * @version 4.1.0
 *
 * @author  WPFactory
 */

defined( 'ABSPATH' ) || exit;

class webdWoocommerceReportingStatisticsAdmin {

	/**
	 * tab.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 */
	public $tab;

	/**
	 * activeTab.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 */
	public $activeTab;

	public $allowed_html = array(
			'a' => array(
				'style' => array(),
				'href' => array(),
				'title' => array(),
				'class' => array(),
				'id'=>array(),
				'target'=>array(),
			),
			'i' => array('style' => array(),'class' => array(),'id'=>array() ),
			'br' => array('style' => array(),'class' => array(),'id'=>array() ),
			'em' => array('style' => array(),'class' => array(),'id'=>array() ),
			'strong' => array('style' => array(),'class' => array(),'id'=>array() ),
			'h1' => array('style' => array(),'class' => array(),'id'=>array() ),
			'h2' => array('style' => array(),'class' => array(),'id'=>array() ),
			'h3' => array('style' => array(),'class' => array(),'id'=>array() ),
			'h4' => array('style' => array(),'class' => array(),'id'=>array() ),
			'h5' => array('style' => array(),'class' => array(),'id'=>array() ),
			'h6' => array('style' => array(),'class' => array(),'id'=>array() ),
			'img' => array('style' => array(),'class' => array(),'id'=>array() ),
			'p' => array('style' => array(),'class' => array(),'id'=>array() ),
			'div' => array('style' => array(),'class' => array(),'id'=>array() ),
			'section' => array('style' => array(),'class' => array(),'id'=>array() ),
			'ul' => array('style' => array(),'class' => array(),'id'=>array() ),
			'li' => array('style' => array(),'class' => array(),'id'=>array() ),
			'ol' => array('style' => array(),'class' => array(),'id'=>array() ),
			'video' => array('style' => array(),'class' => array(),'id'=>array() ),
			'blockquote' => array('style' => array(),'class' => array(),'id'=>array() ),
			'figure' => array('style' => array(),'class' => array(),'id'=>array() ),
			'figcaption' => array('style' => array(),'class' => array(),'id'=>array() ),
			'style' => array(),
			'button' => array(
				'class' => array(),
			),

			'input' => array(
				'type' => array(),
				'class' => array(),
				'placeholder' => array(),
				'disabled' => array(),
			),
			'option' => array(
				'value' => array(),
				'stock' => array(),
				'quantity' => array(),
				'price' => array(),
				'id' => array(),
			),
			'iframe' => array(
				'height' => array(),
				'src' => array(),
				'width' => array(),
				'allowfullscreen' => array(),
				'style' => array(),
				'class' => array(),
				'id'=>array()
			),
			'img' => array(
				'alt' => array(),
				'src' => array(),
				'title' => array(),
				'style' => array(),
				'class' => array(),
				'width' => array(),
				'height' => array(),
				'id'=>array()
			),
			'video' => array(
				'width' => array(),
				'height' => array(),
				'controls'=>array(),
				'class' => array(),
				'id'=>array()
			),
			'source' => array(
				'src' => array(),
				'type' => array(),
				'class' => array(),
				'id'=>array()
			),
		);

	private $orders = [];
	private $theproducts = [];
	private $orderMonths = [];
	private $orderYears = [];
	private $chunkSize = 1000;
	private $countries = [];
	private $cities = [];
	private $payments = [];

	private $total;
	private $subtotal;
	private $shipping;
	private $taxes;
	private $refunds;
	private $discounts;
	private $products;
	private $net;
	private $saleEvery;
	private $datediff;
	private $uniqueDates = [];
	private $avg;

	public function proModal(){ ?>
		<div id="webdWoocommerceReportingStatisticsModal">
		  <!-- Modal content -->
		  <div class="modal-content">
			<div class='clearfix'><span class="close">&times;</span></div>
			<div class='clearfix verticalAlign'>
				<div class='columns2'>
					<center>
						<img style='width:90%' src='<?php echo plugins_url( 'images/'.$this->slug.'-pro.png', __FILE__ ); ?>' style='width:100%' />
					</center>
				</div>

				<div class='columns2'>
					<h3><?php  esc_html_e('Go PRO and get more important features!',"webd-woocommerce-reporting-statistics");?></h3>

					<p><i class='fa fa-check'></i> <?php  esc_html_e('Get Reports by Product',"webd-woocommerce-reporting-statistics");?></p>
					<p><i class='fa fa-check'></i> <?php  esc_html_e('Get Reports by Product Category',"webd-woocommerce-reporting-statistics");?></p>
					<p><i class='fa fa-check'></i> <?php  esc_html_e('Get Reports by City, Country, Payment Method',"webd-woocommerce-reporting-statistics");?></p>
					<p><i class='fa fa-check'></i> <?php  esc_html_e('Next Months Forecast per Product / Category',"webd-woocommerce-reporting-statistics");?></p>
					<p><i class='fa fa-check'></i> <?php  esc_html_e('Custom Report Builder including custom fields',"webd-woocommerce-reporting-statistics");?></p>
					<p><i class='fa fa-check'></i> <?php  esc_html_e('Custom Set From - To date to Search',"webd-woocommerce-reporting-statistics");?></p>
					<p><i class='fa fa-check'></i> <?php  esc_html_e('Get Reports by selecting Month or Year',"webd-woocommerce-reporting-statistics");?></p>
					<p><i class='fa fa-check'></i> <?php  esc_html_e('Export reports in Excel',"webd-woocommerce-reporting-statistics");?></p>
					<p><i class='fa fa-check'></i> <?php  esc_html_e('Current Selling Products Low Stock Products Reminder!',"webd-woocommerce-reporting-statistics");?> </p>
					<p><i class='fa fa-check'></i> <?php  esc_html_e('Low Stock Products Report',"webd-woocommerce-reporting-statistics");?> </p>
					<p><i class='fa fa-check'></i> <?php  esc_html_e('Email Report',"webd-woocommerce-reporting-statistics");?></p>
					<p class='bottomToUp'><center><a target='_blank' class='proUrl' href='<?php print $this->proUrl; ?>'><?php  esc_html_e('GET IT HERE',"webd-woocommerce-reporting-statistics");?></a></center></p>
				</div>
			</div>
		  </div>
		</div>
		<?php
	}

	/**
	 * adminHeader.
	 *
	 * @version 4.0.0
	 */
	public function adminHeader(){
		?><h1><?php esc_html_e( 'Advanced WooCommerce Product Sales Reporting - Statistics & Forecast', 'webd-woocommerce-reporting-statistics' ); ?></h1><?php
	}

	/**
	 * adminSettings.
	 *
	 * @version 4.1.0
	 *
	 * @todo    (v4.1.0) clean up "More extensions" tab (`extendwp_extensions`)
	 */
	public function adminSettings(){
				global $product;

				$this->tab = array( 'all' => esc_html__('GENERAL',"webd-woocommerce-reporting-statistics"),'years' => esc_html__('PER YEAR',"webd-woocommerce-reporting-statistics"),'months' => esc_html__('PER MONTH',"webd-woocommerce-reporting-statistics"),'top' => esc_html__('TOP 5 PRODUCTS',"webd-woocommerce-reporting-statistics"));
				if(isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab']=='all' ) ){
					$this->activeTab = $_GET['tab'] ;
				}else $this->activeTab = 'all';
				echo '<h3 class="nav-tab-wrapper" >';
				foreach( $this->tab as $tab => $name ){
					$class = ( $tab == $this->activeTab ) ? ' nav-tab-active' : '';
					echo "<a class='nav-tab".$class." contant' href='?page=".$this->slug."&tab=".$tab."'>".$name."</a>";
				}
				?>
				<a class='nav-tab  proVersion' href="#" style='background:#42b72a;color:#fff' ><?php  esc_html_e('GO PRO',"webd-woocommerce-reporting-statistics");?></a>
				<?php
				echo "</h3>";

				?>

				<?php
				$selected = '';

				if( ( isset( $_GET['tab'] ) && $_GET['tab'] =='all' ) || !isset( $_GET['tab'] ) ){

					$selected = date('Y-m-d', strtotime("first day of this month"));

					$this->displayFilterForm();
					$this->displayOrders();

				}elseif( isset($_GET['tab']) && ( $_GET['tab'] =='years' || $_GET['tab'] =='months' ) ){

					$this->displayFilterForm();
					$this->displayOrdersBy( 'month');

				}elseif( isset($_GET['tab']) && ( $_GET['tab'] =='years') ){

					$this->displayFilterForm();
					$this->displayOrdersBy( 'year' );

				}elseif(isset($_GET['tab']) && $_GET['tab'] =='stock'){?>
				<div class='premium_msg'>
				<p>
					<strong>
					<?php  esc_html_e('Only available on PRO Version',"webd-woocommerce-reporting-statistics");?> <a class='premium_button' target='_blank'  href='<?php print $this->proUrl;?>'><?php  esc_html_e('Get it Here',"webd-woocommerce-reporting-statistics");?></a>
					</strong>
				</p>
				</div>
				<div class='report_widgt'>

					<div id="tabs3" class='clearfix'>
						<ul>
							<li><a href="#selling"><?php  esc_html_e( 'Selling Products Low Stock Reminder',"webd-woocommerce-reporting-statistics" );?></a></li>
							<li><a href="#noselling"><?php  esc_html_e( 'All Time Low Stock Products',"webd-woocommerce-reporting-statistics" );?></a></li>
						</ul>
						<?php $this->sellingProdStock(); ?>
						<?php $this->LowStock(); ?>
					</div>
				</div>
				<?php }elseif( isset( $_GET['tab'] ) && $_GET['tab'] =='top' ){ ?>
						<div class='report_widge'>
							<?php  $this->topSellers(); ?>
						</div>
				<?php }elseif(isset( $_GET['tab'] ) && $_GET['tab'] =='email' ){ ?>
				<div class='premium_msg'>
				<p>
					<strong>
					<?php  esc_html_e('Only available on PRO Version',"webd-woocommerce-reporting-statistics");?> <a class='premium_button' target='_blank'  href='<?php print $this->proUrl;?>'><?php  esc_html_e('Get it Here',"webd-woocommerce-reporting-statistics");?></a>
					</strong>
				</p>
				</div>
					<form method="post" id='<?php print "webd-woocommerce-reporting-statistics"; ?>Form'
					action= "<?php echo admin_url( "admin.php?page=".$this->slug."&tab=email" ); ?>">
					<?php
						settings_fields( "webd-woocommerce-reporting-statistics"."general-options" );
						do_settings_sections( "webd-woocommerce-reporting-statistics"."general-options" );

					?></form>

				<?php }
	}

	/**
	 * adminFooter.
	 *
	 * @version 4.0.0
	 */
	public function adminFooter(){ ?>
		<div class='clearfix'>
		<hr>
		<div></div>
		<?php $this->rating(); ?>
		<div class='get_ajax'></div>
		</div>
		<?php
	}

	public function defaultStatus(){

			$default_status = get_option( $this->plugin.'_status' );

			?>

			<select multiple name="<?php print $this->plugin.'_status';?>[]" id='<?php print $this->plugin.'_status';?>'>

			<option value=''><?php esc_html_e( 'Choose Status...', $this->plugin );?></option>
			<?php

			foreach( wc_get_order_statuses() as $key=>$value){

				$in = in_array( $key, $default_status ) ? "selected" : "";

				print "<option value='".esc_attr(  $key  )."'  ".$in ." >".esc_attr( $value )."</option>";
			}
			?>
			</select>
			<?php
	}

	public function rating(){
	?>
		<div class="notices notice-success rating is-dismissible">

			<?php esc_html_e( "You like this plugin? ", 'smw' ); ?><i class='fa fa-smile-o' ></i> <?php esc_html_e( "Then please give us ", "webd-woocommerce-reporting-statistics" ); ?>
				<a target='_blank' href='https://wordpress.org/support/plugin/webd-woocommerce-advanced-reporting-statistics/reviews/#new-post'>
					<i class='fa fa-star' ></i><i class='fa fa-star' ></i><i class='fa fa-star' ></i><i class='fa fa-star' ></i><i class='fa fa-star' ></i>
				</a>

		</div>
	<?php
	}

	public function sendReport(){
		?>
		<select class='proVersion' >
			<option   readonly value=''><?php  esc_html_e('Select...',"webd-woocommerce-reporting-statistics");?></option>
			<option value='yes'><?php  esc_html_e('yes',"webd-woocommerce-reporting-statistics");?></option>
			<option value='no'><?php  esc_html_e('no',"webd-woocommerce-reporting-statistics");?></option>
		</select>
		<?php
	}

	public function emailForReport(){
		?>
		<input type="text" readonly placeholder='Email to send - PRO Version' class='proVersion' />
		<?php
	}

	public function emailFrequency(){
		?>
		<select  readonly class='proVersion' >
			<option   value=''><?php  esc_html_e('Select...',"webd-woocommerce-reporting-statistics");?></option>
			<option value='daily'><?php  esc_html_e('Daily',"webd-woocommerce-reporting-statistics");?></option>
			<option value='weekly'><?php  esc_html_e('Weekly',"webd-woocommerce-reporting-statistics");?></option>
			<option value='monthly'><?php  esc_html_e('Monthly',"webd-woocommerce-reporting-statistics");?></option>
		</select>
		<?php
	}

	public function settingsSection(){
		add_settings_section("webd-woocommerce-reporting-statistics"."general", "", null, "webd-woocommerce-reporting-statistics"."general-options");
		add_settings_field('sendReport',"Send Report", array($this, 'sendReport'),  "webd-woocommerce-reporting-statistics"."general-options", "webd-woocommerce-reporting-statistics"."general");
		add_settings_field('emailFrequency',"Email Frequency", array($this, 'emailFrequency'),  "webd-woocommerce-reporting-statistics"."general-options", "webd-woocommerce-reporting-statistics"."general");
		add_settings_field('emailForReport',"Emai for Report", array($this, 'emailForReport'),  "webd-woocommerce-reporting-statistics"."general-options", "webd-woocommerce-reporting-statistics"."general");
	}

	public function displayOrdersBy( $period ) {

		echo '<div class="column1 periods">';

			?><center class='results_found'></center><?php
			echo "<div class='periods_table'>";

			echo "<table class='widefat striped' >";
				echo "<thead><th>" . esc_html( ucfirst( $period ) ) . "</th><th>" . esc_html__( "# Orders" ,"webd-woocommerce-reporting-statistics" ) . "</th><th>" . esc_html__( "Tax" , "webd-woocommerce-reporting-statistics" ) . "</th><th>" . esc_html__( "Shipping" , "webd-woocommerce-reporting-statistics" ) . "</th><th>" . esc_html__( "Discount" , "webd-woocommerce-reporting-statistics" ) . "</th><th>" . esc_html__( "Refunds" , "webd-woocommerce-reporting-statistics" ) . "</th><th class='gross_label'>" . esc_html__( "Gross Total" , "webd-woocommerce-reporting-statistics" ) . "</th><th class='net_label'>" . esc_html__( "Net Total" , "webd-woocommerce-reporting-statistics" ) ."</th>";
				?>
				<tr class='totals'><td><?php esc_html_e( 'TOTALS',$this->plugin ); ?></td><td class='num_orders'></td><td class='tax'></td><td class='shipping'></td><td class='discount'></td><td class='refund'><td class='total'></td><td class='net'></td></tr>

				<?php print "</thead>";
				echo "<tbody>";

				echo "</tbody>";
			echo '</table>';

			echo '</div>';

			print "<div class='report_widget columns2 em '><b><i class='fa fa-2x fa-filter'></i> ". esc_html__( 'AVERAGE SALES', "webd-woocommerce-reporting-statistics" ) . " <span class='avg_period'></span></b></div>";
			print "<div class='report_widget columns2 em'><b><i class='fa fa-2x fa-signal'></i> ". esc_html__( 'NEXT ', "webd-woocommerce-reporting-statistics" ) . esc_html( strtoupper( $period ) ) . esc_html__( ' SALES FORECAST', "webd-woocommerce-reporting-statistics" ) . " <span class='forecast'></span></b></div>";

		echo '</div>';

		?>
		<div  class="chart-container column1 byPeriod periods" style="position: relative" ></div>

		<div class='no_orders'>
			<center>
				<?php print "<h3> ". esc_html__( 'No Orders found...',"webd-woocommerce-reporting-statistics" ) ." </h3>"; ?>
			</center>
		</div>

		<?php
	}

	public function progress(){
		?>
			<div class='progress text-center'></div>
			<div class='progressBar'><span style='visibility:hidden'>..</span></div>
		<?php
	}

	public function displayFilterForm() {

		$this->progress();
		?>

		<div class='reportCalendar'>

		<form method='post'  id='selectDates' autocomplete="off" role="presentation" >

				<select name='customer' class='dateFilter'>
					<option value=''><?php esc_html_e( 'Customer..',"webd-woocommerce-reporting-statistics" );?></option>
				<?php

				// get customer
				$customer_query = new WP_User_Query(
					array(
						'role'	  =>	'customer',
					)
				);
				$custs = $customer_query->get_results();

						foreach($custs as $res){
							$user_info = get_userdata( $res->ID );
							echo "<option  value='".esc_attr( $res->ID )."'>" . esc_attr( $user_info->first_name . " " . $user_info->last_name ) . "</option>";
						}
				?>
				</select>

				<select class='dateFilter proVersionPreselect' style='background:#eee;' id="pt-filter-by-date">
					<option value=""><?php  esc_html_e('Query by...',"webd-woocommerce-reporting-statistics");?></option>
					<option class='proVersionPreselected' style='background:#eee;' value="proVersionPreselected"><?php  esc_html_e('Product - in PRO',"webd-woocommerce-reporting-statistics");?></option>
					<option class='proVersionPreselected' style='background:#eee;' value="proVersionPreselected"><?php  esc_html_e('Category - in PRO',"webd-woocommerce-reporting-statistics");?></option>
					<option class='proVersionPreselected' style='background:#eee;' value="proVersionPreselected"><?php  esc_html_e('City - in PRO',"webd-woocommerce-reporting-statistics");?></option>
					<option class='proVersionPreselected' style='background:#eee;' value="proVersionPreselected"><?php  esc_html_e('Country - in PRO',"webd-woocommerce-reporting-statistics");?></option>
					<option class='proVersionPreselected' style='background:#eee;' value="proVersionPreselected"><?php  esc_html_e('Payment Method - in PRO',"webd-woocommerce-reporting-statistics");?></option>

				</select>

				<select name='order_status' id='order_status' class='dateFilter'>
					<option value=''><?php esc_html_e('Order Status', "webd-woocommerce-reporting-statistics" );?></option>

						<?php
							foreach( wc_get_order_statuses() as $key=>$value){
								print "<option value='".esc_attr($key)."' >".esc_attr($value)."</option>";
							}
						?>

				</select>

			<?php if( empty( $_REQUEST['tab'] ) || ( isset( $_REQUEST['tab'] ) &&  $_REQUEST['tab'] ==='all' ) ){ ?>
				<?php $date = current_time( 'mysql' ) ;
				$today = date("Y-m-d", strtotime( $date ) );
				?>

				<select name="selected" class='dateFilter ' id='selected' >
					<option value=""><?php  esc_html_e('Preselected Period',"webd-woocommerce-reporting-statistics");?></option>
					<option value="<?php print esc_attr( $today ); ?>"><?php  esc_html_e( 'Today',"webd-woocommerce-reporting-statistics" );?></option>
					<option value="<?php print date( 'Y-m-d', strtotime( $date . "- 1 day" ) ); ?>"><?php  esc_html_e( 'Yesterday',"webd-woocommerce-reporting-statistics" );?></option>
					<option value="<?php print date( 'Y-m-d', strtotime( $date . "- 7 days" ) ); ?>"><?php  esc_html_e( 'This Week',"webd-woocommerce-reporting-statistics" );?></option>
					<option value="<?php print date( 'Y-m-d', strtotime( "first day of this month" ) ); ?>"><?php  esc_html_e( 'This Month',"webd-woocommerce-reporting-statistics" );?></option>
					<option value="<?php print date( 'Y-m-d', strtotime( $date . "- 2 months" ) ); ?>"><?php  esc_html_e( 'Last 2 Months',"webd-woocommerce-reporting-statistics" );?></option>
					<option value="<?php print date( 'Y-m-d', strtotime( $date . "- 3 months" ) ); ?>"><?php  esc_html_e( 'Last 3 Months', "webd-woocommerce-reporting-statistics" );?></option>
					<option value="<?php print date( 'Y-m-d', strtotime( $date . "- 6 months" ) ); ?>"><?php  esc_html_e( 'Last 6 Months', "webd-woocommerce-reporting-statistics" );?></option>
				</select>

				<select name='month' class='dateFilter proVersionPreselect' style='background:#eee;' id="pt-filter-by-date">

					<option value=''><?php esc_html_e( 'Select Month..',"webd-woocommerce-reporting-statistics" );?></option>
					<?php
					foreach( OrderProcessorHelp::get_instance()->periodFilter( 'month') as $period ){
						print "<option value='proVersionPreselected'>". esc_attr( $period->period ) ."</option>";
					}
					?>
				</select>
				<select name='year' class='dateFilter proVersionPreselect' style='background:#eee;' id="pt-filter-by-date" >

					<option value=''><?php esc_html_e( 'Select Year..',"webd-woocommerce-reporting-statistics" );?></option>
					<?php
					foreach( OrderProcessorHelp::get_instance()->periodFilter( 'year') as $period ){
						print "<option value='proVersionPreselected'>". esc_attr( $period->period ) ."</option>";
					}
					?>

				</select>

				 <input  placeholder='From'  class="from datepicker dateFilter" readonly name='from' value='' />
				 <input  placeholder='To'  class="to datepicker dateFilter"  readonly name='to' value='' />

			 <?php } ?>

			 <?php wp_nonce_field("webd-woocommerce-reporting-statistics"); ?>
			 <input type='submit' class='button button-primary' />
		 </form>

		 </div>
	<?php

	}

	public function overview(){

		?>

			<center class='results_found'></center>

			<div class='overview clearfix'>
				<div class='flexmeContainer'>
					<?php

					print "<div class='report_widget'><h3><i class='fa fa-2x fa-signal' ></i> " . esc_html__( "GROSS SALES", $this->plugin )." <br/><small><i>".esc_html__( 'after tax, shipping, discount & refunds' , $this->plugin )."</i></small><hr/> <span class='total'></span></h3></div>";
					print "<div class='report_widget'><h3><i class='fa fa-2x fa-signal' ></i> " . esc_html__( "NET SALES", $this->plugin )." <br/><small><i>".esc_html__( 'before tax, shipping, discount, after refunds' , $this->plugin )."</i></small><hr/> <span class='subtotal'></span></h3></div>";
					print "<div class='report_widget'><h3><i class='fa fa-2x fa-truck' ></i> " . esc_html__( "SHIPPING", $this->plugin )." <hr/> <span class='shipping'></span></h3></div>";
					print "<div class='report_widget'><h3><i class='fa fa-2x fa-percent' ></i> ". esc_html__( "TAXES", $this->plugin )." <hr/> <span class='tax'></span></h3></div>";
					print "<div class='report_widget'><h3><i class='fa fa-2x fa-thumbs-down' ></i> " . esc_html__( "REFUNDS", $this->plugin )." <hr/> <span class='refund'></span></h3></div>";
					print "<div class='report_widget'><h3><i class='fa fa-2x fa-tag' ></i> " . esc_html__( "DISCOUNTS", $this->plugin )." <hr/> <span class='discount'></span></h3></div>";
					print "<div class='report_widget'><h3><i class='fa fa-2x fa-pie-chart' ></i> " . esc_html__( "#PRODUCTS PURCHASED", $this->plugin )." <hr/> <span class='num_products'></span></h3></div>";
					print "<div class='report_widget'><h3><i class='fa fa-2x fa-pie-chart' ></i> " . esc_html__( "#ORDERS", $this->plugin )." <hr/> <span class='num_orders'></span></h3></div>";
					print "<div class='report_widget em'><h3><i class='fa fa-2x fa-filter' ></i> " . esc_html__( "AVG NET SALES / day", $this->plugin )." <hr/> <span class='avg'></span></h3></div>";
					print "<div class='report_widget em'><h3><i class='fa fa-2x fa-clock-o' ></i> " . esc_html__( "SALES EVERY", $this->plugin )." <hr/> <span class='salesEvery'></span>" . esc_html__( " days", $this->plugin )."</h3></div>";
				?>
				</div>
			</div>
			<?php
	}

	public function displayOrders() {

		$this->overview();
		?>
		<div id="tabs2" class='clearfix'>
			<ul>

				<li><a href="#orders"><?php  esc_html_e( 'Orders',"webd-woocommerce-reporting-statistics"  );?></a></li>
				<li><a href="#customers"><?php  esc_html_e( 'Customers', "webd-woocommerce-reporting-statistics" );?></a></li>
				<li><a href="#products"><?php  esc_html_e( 'Products',"webd-woocommerce-reporting-statistics" );?></a></li>
				<li><a href="#categories"><?php  esc_html_e( 'Categories', "webd-woocommerce-reporting-statistics" );?></a></li>
				<li><a href="#countries"><?php  esc_html_e( 'Countries', "webd-woocommerce-reporting-statistics" );?></a></li>
				<li><a href="#payment"><?php  esc_html_e( 'Payment Methods',"webd-woocommerce-reporting-statistics" );?></a></li>
				<li><a href="#coupons"><?php  esc_html_e( 'Coupons', "webd-woocommerce-reporting-statistics" );?></a></li>

			</ul>

			<?php $this->orders(); ?>
			<?php $this->payments(); ?>
			<?php $this->customers(); ?>
			<?php $this->countries(); ?>
			<?php $this->products(); ?>
			<?php $this->categories(); ?>
			<?php $this->coupons(); ?>

		</div>

		<?php

	}

	public function orders( ){?>

		<div id='orders'>

			<div class='column1'>
				<h3 class='text-center'><i class='fa fa-pie-chart' ></i> <?php  esc_html_e( 'ORDERS PLACED', "webd-woocommerce-reporting-statistics" );?> </h3>

				<p><button class='proVersion custom_fields' ><?php esc_html_e( 'Choose your custom fields - PRO ' , 'webd-woocommerce-reporting-statistics' ); ?></p>
				<button class='proVersion'><i class='fa fa-file-excel-o '></i> <?php  esc_html_e('Export / PRO',"webd-woocommerce-reporting-statistics");?></button>
				<input type="text" class="search" placeholder="<?php  esc_html_e( 'Search...', "webd-woocommerce-reporting-statistics" );?>"></input>

				<table class="widefat striped ordersToExport" >
					<thead>
							<th><?php  esc_html_e( 'Order ID','webd-woocommerce-reporting-statistic' );?></th>
							<th><?php  esc_html_e( 'Date','webd-woocommerce-reporting-statistic' );?></th>
							<th><?php  esc_html_e( 'Payment Method','webd-woocommerce-reporting-statistic' );?></th>
							<th><?php  esc_html_e( 'Coupons','webd-woocommerce-reporting-statistic' );?></th>
							<th><?php  esc_html_e( 'Customer Name','webd-woocommerce-reporting-statistic' );?></th>
							<th><?php  esc_html_e( 'Customer Country','webd-woocommerce-reporting-statistic' );?></th>
							<th><?php  esc_html_e( 'Discount','webd-woocommerce-reporting-statistic' );?></th>
							<th><?php  esc_html_e( 'Shipping Cost','webd-woocommerce-reporting-statistic' );?></th>
							<th><?php  esc_html_e( 'Taxes','webd-woocommerce-reporting-statistic' );?></th>
							<th><?php  esc_html_e( 'Gross Total','webd-woocommerce-reporting-statistic' );?></th>
							<th><?php  esc_html_e( 'Refunds','webd-woocommerce-reporting-statistic' );?></th>
							<th><?php  esc_html_e( 'Net Total','webd-woocommerce-reporting-statistic' );?></th>
						</tr>
					</thead>
					<tbody>
				   </tbody>

				</table>
			</div>
			<div class="orders-pagination">
				<!-- Pagination will be loaded here -->
			</div>
		</div>
		<?php
	}

	public function payments( ){
	?>
		<div id='payment'>

			<h3 class='text-center'><i class='fa fa-money' ></i> <?php  esc_html_e( 'PAYMENT METHODS', "webd-woocommerce-reporting-statistics" );?></h3>
			<div class='columns2'>
				<button class='proVersion'><i class='fa fa-file-excel-o '></i> <?php  esc_html_e('Export / PRO',"webd-woocommerce-reporting-statistics");?></button>
				<input type="text" class="search" placeholder="<?php  esc_html_e( 'Search...', "webd-woocommerce-reporting-statistics" );?>"></input>
				<table class="widefat striped" >
					<thead>
					   <tr class="row-title">
							<th><?php  esc_html_e( 'Payment Method',"webd-woocommerce-reporting-statistics" );?></th>
							<th><?php  esc_html_e( 'Orders', "webd-woocommerce-reporting-statistics" );?></th>
							<th><?php  esc_html_e( 'Tax', "webd-woocommerce-reporting-statistics" );?></th>
							<th><?php  esc_html_e( 'Sales', "webd-woocommerce-reporting-statistics" );?></th>
						</tr>
						<tr class='totals'><td><?php esc_html_e( 'TOTALS', "webd-woocommerce-reporting-statistics" ); ?></td><td class='num_orders'></td><td class='tax'></td><td class='total'></td></tr>
					</thead>
					<tbody>
				   </tbody>

				</table>

			</div>
			<div class="chart-container columns2 byPayment" style="position: relative">
				<canvas id="byPayment"></canvas>
			</div>

		</div>
	<?php
	}

	public function customers(){
		?>
		<div id='customers'>
			<h3 class='text-center'><i class='fa fa-users' ></i> <?php  esc_html_e( 'CUSTOMERS', "webd-woocommerce-reporting-statistics" );?> </h3>
			<div class='column1'>
				<button class='proVersion'><i class='fa fa-file-excel-o '></i> <?php  esc_html_e('Export / PRO',"webd-woocommerce-reporting-statistics");?></button>
				<input type="text" class="search" placeholder="<?php  esc_html_e( 'Search...', "webd-woocommerce-reporting-statistics" );?>"></input>
				<table class="widefat striped" id='custs'>
					<thead>
					   <tr class="row-title">
							<th><?php  esc_html_e( 'Customer Name',"webd-woocommerce-reporting-statistics" );?></th>
							<th><?php  esc_html_e( 'Phone',"webd-woocommerce-reporting-statistics" );?></th>
							<th><?php  esc_html_e( 'Email',"webd-woocommerce-reporting-statistics" );?></th>
							<th><?php  esc_html_e( 'Country',"webd-woocommerce-reporting-statistics" );?></th>
							<th><?php  esc_html_e( 'State',"webd-woocommerce-reporting-statistics" );?></th>
							<th><?php  esc_html_e( 'City',"webd-woocommerce-reporting-statistics" );?></th>
							<th><?php  esc_html_e( 'Company',"webd-woocommerce-reporting-statistics" );?></th>
							<th><?php  esc_html_e( '# of Orders',"webd-woocommerce-reporting-statistics" );?></th>
							<th><?php  esc_html_e( 'Tax',"webd-woocommerce-reporting-statistics" );?></th>
							<th><?php  esc_html_e( 'Sales',"webd-woocommerce-reporting-statistics" );?></th>
						</tr>
						<tr class='totals'><td><?php esc_html_e( 'TOTALS',$this->plugin ); ?></td><td></td><td></td><td></td><td></td><td></td><td></td><td class='num_orders'></td><td class='tax'></td><td class='total'></td></tr>
					</thead>
					<tbody>
				   </tbody>

				</table>
			</div>
		</div>
	<?php

	}

	public function countries(){
		?>

		<div  id='countries'>

			<h3 class='text-center'><i class='fa fa-globe' ></i> <?php  esc_html_e( 'COUNTRIES', "webd-woocommerce-reporting-statistics" );?></h3>
			<div class='columns2' >
				<button class='proVersion'><i class='fa fa-file-excel-o '></i> <?php  esc_html_e('Export / PRO',"webd-woocommerce-reporting-statistics");?></button>
				<input type="text" class="search" placeholder="<?php  esc_html_e( 'Search...', "webd-woocommerce-reporting-statistics" );?>"></input>
				<table class="widefat striped" >
					<thead>
					   <tr class="row-title">
							<th><?php  esc_html_e('Country', "webd-woocommerce-reporting-statistics" );?></th>
							<th><?php  esc_html_e('# of Orders', "webd-woocommerce-reporting-statistics" );?></th>
							<th><?php  esc_html_e('Tax',"webd-woocommerce-reporting-statistics" );?></th>
							<th><?php  esc_html_e('Sales', "webd-woocommerce-reporting-statistics" );?></th>
						</tr>
						<tr class='totals'><td><?php esc_html_e( 'TOTALS', "webd-woocommerce-reporting-statistics" ); ?></td><td class='num_orders'></td><td class='tax'></td><td class='total'></td></tr>
					</thead>
					<tbody>
				   </tbody>

				</table>
			</div>
			<div class="chart-container byCountry columns2" ></div>

		</div>
	<?php
	}

	public function products(){
	?>
		<div id='products'>

			<h3 class='text-center'><i class='fa fa-pie-chart' ></i> <?php  esc_html_e( 'PRODUCTS',"webd-woocommerce-reporting-statistics" );?></h3>

			<div class='column1'>
				<center><i><?php esc_html_e( "Amounts before tax and total discount" ,"webd-woocommerce-reporting-statistics" ); ?></i><center>
				<button class='proVersion'><i class='fa fa-file-excel-o '></i> <?php  esc_html_e('Export / PRO',"webd-woocommerce-reporting-statistics");?></button>
				<input type="text" class="search" placeholder="<?php  esc_html_e( 'Search...',"webd-woocommerce-reporting-statistics" );?>"></input>
				<table class="widefat striped" >
					<thead>
					   <tr class="row-title">
							<th><?php  esc_html_e( 'Product',"webd-woocommerce-reporting-statistics" );?></th>
							<th><?php  esc_html_e( 'SKU',"webd-woocommerce-reporting-statistics" );?></th>
							<th><?php  esc_html_e( 'Items Sold',"webd-woocommerce-reporting-statistics" );?></th>
							<th><?php  esc_html_e( 'Sales',"webd-woocommerce-reporting-statistics" );?></th>
						</tr>
						<tr class='totals'><td><?php esc_html_e( 'TOTALS',"webd-woocommerce-reporting-statistics" ); ?></td><td></td><td class='num_products'></td><td class='sum_products'></td></tr>
					</thead>
					<tbody>
				   </tbody>
				</table>
			</div>
			<div class="chart-container productChart" ></div>

		</div>
	<?php

	}

	public function categories(){

	?>
		<div id='categories'>
			<h3 class='text-center'><i class='fa fa-tag' ></i> <?php  esc_html_e( 'CATEGORIES', "webd-woocommerce-reporting-statistics" );?></h3>
			<div class='columns2'>

				<center><i><?php esc_html_e( "Amounts before tax and total discount","webd-woocommerce-reporting-statistics" ); ?></i></center>
				<input type="text" class="search" placeholder="<?php  esc_html_e( 'Search...',"webd-woocommerce-reporting-statistics"  );?>"></input>
				<table class="widefat striped" >
					<thead>
					   <tr class="row-title">
							<th><?php  esc_html_e( 'Category', "webd-woocommerce-reporting-statistics" );?></th>
							<th><?php  esc_html_e( 'Items Sold', "webd-woocommerce-reporting-statistics" );?></th>
							<th><?php  esc_html_e( 'Sales', "webd-woocommerce-reporting-statistics" );?></th>
						</tr>
						<tr class='totals'><td><?php esc_html_e( 'TOTALS', "webd-woocommerce-reporting-statistics" ); ?></td><td class='num_products'></td><td class='sum_products'></td></tr>
					</thead>
					<tbody>
				   </tbody>

				</table>
			</div>
			<div class="chart-container categoriesChart columns2" ></div>

		</div>

	<?php

	}

	public function coupons(){

		?>
		<div id='coupons'>

			<h3 class='text-center'><i class='fa fa-tag' ></i> <?php  esc_html_e( 'COUPONS', "webd-woocommerce-reporting-statistics" );?></h3>
			<div class='columns2'>
				<input type="text" class="search" placeholder="<?php  esc_html_e( 'Search...', "webd-woocommerce-reporting-statistics" );?>"></input>
				<table class="widefat striped" >
					<thead>
					   <tr class="row-title">
							<th><?php  esc_html_e( 'Coupon', "webd-woocommerce-reporting-statistics" );?></th>
							<th><?php  esc_html_e( 'Total', "webd-woocommerce-reporting-statistics" );?></th>
						</tr>
						<tr class='totals'><td><?php esc_html_e( 'TOTALS', "webd-woocommerce-reporting-statistics" ); ?></td><td class='discount'></td></tr>
					</thead>
					<tbody>
				   </tbody>
				</table>
			</div>
			<div class="chart-container couponCharts columns2" ></div>

		</div>
	<?php

	}

	public function topSellers(){
			global $woocommerce;
			include_once(WC()->plugin_path().'/includes/admin/reports/class-wc-admin-report.php');
				$wc_report = new WC_Admin_Report();

				$data = $wc_report->get_order_report_data( array(
					'data' => array(
						'_qty' => array(
							'type' => 'order_item_meta',
							'order_item_type' => 'line_item',
							'function' => 'SUM',
							'name' => 'quantity'
						),
						'_line_subtotal' => array(
							'type' => 'order_item_meta',
							'order_item_type' => 'line_item',
							'function' => 'SUM',
							'name' => 'gross'
						),
						'_product_id' => array(
							'type' => 'order_item_meta',
							'order_item_type' => 'line_item',
							'function' => '',
							'name' => 'product_id'
						),
						'order_item_name' => array(
							'type'     => 'order_item',
							'function' => '',
							'name'     => 'order_item_name',
						),
					),
					'group_by'     => 'product_id',
					'order_by'     => 'quantity DESC',
					'query_type' => 'get_results',

					'limit' => 5,
					'order_types' => wc_get_order_types( 'order_count' ),
					'order_status' => array( 'completed','processing','onhold','refunded'),
				) );

				if(!empty($data)){
				?><br/><hr/>
				<h3 class='text-center'><i class='fa fa-tag' ></i> <?php  esc_html_e('ALL TIMES TOP 5 SELLING PRODUCTS',"webd-woocommerce-reporting-statistics");?></h3>
				<table class='widefat striped'>
					<thead>
						<th class="manage-column column-count" scope="col"><?php  esc_html_e('Category',"webd-woocommerce-reporting-statistics");?></th>
						<th class="manage-column column-count" scope="col"><?php  esc_html_e('Product',"webd-woocommerce-reporting-statistics");?></th>
						<th class="manage-column column-count" scope="col"><?php  esc_html_e('Quantity',"webd-woocommerce-reporting-statistics");?></th>
						<th class="manage-column column-count" scope="col"><?php  esc_html_e('Sales',"webd-woocommerce-reporting-statistics");?></th>
					</thead>

					<tbody>

					<?php
					foreach($data as $d){
						$terms = wp_get_post_terms( $d->product_id, 'product_cat' );
						print "<tr><td>";
						foreach ( $terms as $term ) {
							 $cat_id = $term->name;
							 print $term->name ."<br/>";

						}
						//$d->product_id
						print "</td>";
						print "<td>".esc_attr($d->order_item_name) . "</td><td>".esc_attr($d->quantity)."</td><td>" .wc_price(esc_attr($d->gross)) ."</td>" ;
						print "</tr>";
					}
					?>
					</tbody>
				 </table>
				<?php
				}else print esc_html__("There is no sale yet!","webd-woocommerce-reporting-statistics");
	}

	public function LowStock(){
			?>
		<div id='noselling'>
			<div class='column1'>
			<h3 class='text-center title'><i class='fa fa-pie-chart' ></i> <?php  esc_html_e('All Time Low Stock Products( < 5 items )',"webd-woocommerce-reporting-statistics");?></h3>
			<table class='widefat'>
			<thead>
				<tr>
					<th><?php  esc_html_e('Title',"webd-woocommerce-reporting-statistics");?></th>
					<th><?php  esc_html_e('Stock',"webd-woocommerce-reporting-statistics");?></th>
					<th><?php  esc_html_e('Sales',"webd-woocommerce-reporting-statistics");?></th>
				</tr>
			</thead>
			<tbody>
			<tr><td rowspan="3"><h1 class='proVersion'><a href='#'><?php  esc_html_e('PRO VERSION ONLY- get it Here!',"webd-woocommerce-reporting-statistics");?></a></h1></td></tr>
		</tbody>
		 <button class='proVersion'><i class='fa fa-file-excel-o '></i> <?php  esc_html_e('Export / PRO',"webd-woocommerce-reporting-statistics");?></button><input type="text" class="search proVersion" readonly placeholder="<?php  esc_html_e('Search / PRO.',"webd-woocommerce-reporting-statistics");?>"></input>
		</table>
		</div>
	<?php
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

	public function divide($a, $b){
		try {
			if(@($a / $b) === false) return INF; // covers PHP5
			return @($a / $b); // covers PHP7
		} catch (DivisionByZeroError $e) {
			return INF; // covers PHP8
		}
	}

	public function sellingProdStock(){
		?>
		<div id='selling'>
			<div class='column1'>
			<h3 class='text-center'><i class='fa fa-pie-chart' ></i> <?php  esc_html_e('Last 30 days Selling Products Stock Reminder',"webd-woocommerce-reporting-statistics");?></h3>
			<table class="widefat striped" >
				<thead>
				   <tr class="row-title">
						<th><?php  esc_html_e('Product',"webd-woocommerce-reporting-statistics");?></th>
						<th><?php  esc_html_e('Items Sold',"webd-woocommerce-reporting-statistics");?></th>
						<th><?php  esc_html_e('In Stock',"webd-woocommerce-reporting-statistics");?></th>
						<th><?php  esc_html_e('Sales Amount',"webd-woocommerce-reporting-statistics");?></th>
						<th><?php  esc_html_e('Sales Every (days)',"webd-woocommerce-reporting-statistics");?></th>
						<th><?php  esc_html_e('Avg Quantity Sold / Order',"webd-woocommerce-reporting-statistics");?></th>
						<th><?php  esc_html_e('Next Order Forecasted Quantity',"webd-woocommerce-reporting-statistics");?></th>
						<th><?php  esc_html_e('Days Left to Out of Stock',"webd-woocommerce-reporting-statistics");?></th>
					</tr>
				</thead>
				<tbody>
					<tr><td rowspan="8"><h1 class='proVersion '><a href='#'><?php  esc_html_e('PRO VERSION ONLY- get it Here!',"webd-woocommerce-reporting-statistics");?></a></h1></td></tr>
			   </tbody>
				<button class='proVersion'><i class='fa fa-file-excel-o '></i> <?php  esc_html_e('Export / PRO',"webd-woocommerce-reporting-statistics");?></button><input type="text" class="search proVersion" readonly placeholder="<?php  esc_html_e('Search / PRO.',"webd-woocommerce-reporting-statistics");?>"></input>
			</table>
			</div>
		</div>
		<?php
	}

	public function extensions(){

		if( is_admin() && current_user_can( 'administrator' ) ){

			$response = wp_remote_get( "https://extend-wp.com/wp-json/products/v2/product/category/excel" );
			if( is_wp_error( $response ) ) {
				return;
			}
			$posts = json_decode( wp_remote_retrieve_body( $response ) );

			if( empty( $posts ) ) {
				return;
			}

			if( !empty( $posts ) ) {
				echo "<div id='extendwp_extensions_popup'>";
					echo "<div class='extendwp_extensions_content '>";
						?>
						<span class="extendwp_close">&times;</span>
						<h2><i><?php esc_html_e( 'More Plugins to Make your life easier by ExtendWP!', "webd-woocommerce-reporting-statistics" ); ?></i></h2>
						<hr/>
						<?php
						print "<div class='extend_flex'>";
						foreach( $posts as $post ) {

							if( $post->class == 'webdWoocommerceReportingStatisticsPro'   ){

							}else{

								echo "<div class='columns3'><a target='_blank' href='".esc_url( $post->url )."' /><img src='".esc_url( $post->image )."' /></a>
								<h3><a target='_blank' href='".esc_url( $post->url )."' />". esc_html( $post->title ) . "</a></h3>
								<div>". wp_kses( $post->excerpt, $this->allowed_html )."</div>
								<a class='button_extensions button-primary' target='_blank' href='".esc_url( $post->url )."' />". esc_html__( 'Get it here', "webd-woocommerce-reporting-statistics" ) . " <i class='fa fa-angle-double-right'></i></a>
								</div>";
							}

						}
						print "</div>";
					echo '</div>';
				echo '</div>';
			}

		}
	}
}

/**
 * Advanced WooCommerce Product Sales Reporting - Statistics & Forecast - Backend JS
 *
 * @author  WPFactory
 */

(function( $ ) {

		$("#webdWoocommerceReportingStatisticsModal .close").click(function(e){
			e.preventDefault();
			$("#webdWoocommerceReportingStatisticsModal").fadeOut();
		});

		var modal = document.getElementById('webdWoocommerceReportingStatisticsModal');

		// When the user clicks anywhere outside of the modal, close it
		window.onclick = function(event) {
			if (event.target == modal) {
				modal.style.display = "none";
			}
		}

		$(".webdWoocommerceReportingStatistics .proVersion").click(function(e){
			e.preventDefault();
			$("#webdWoocommerceReportingStatisticsModal").slideDown();

		});

		$('.webdWoocommerceReportingStatistics #selectDate input[type="submit"]').attr('disabled','disabled');

		$(".webdWoocommerceReportingStatistics .proVersionPreselect").change(function(e){
			e.preventDefault();
			if($(this).val() =='proVersionPreselected' ){
				$("#webdWoocommerceReportingStatisticsModal").slideDown();
				$('.webdWoocommerceReportingStatistics #selectDate input[type="submit"]').attr('disabled','disabled');
			}else $('.webdWoocommerceReportingStatistics #selectDate input[type="submit"]').removeAttr('disabled');

		});

	$('.webdWoocommerceReportingStatistics .nav-tab-wrapper a').click(function(e){

		if( $(this).hasClass("proVersion") ){
			e.preventDefault();
			$("#webdWoocommerceReportingStatisticsModal").slideDown();

		}

	});

		//EXTENSIONS
		$(".webdWoocommerceReportingStatistics .extendwp_extensions").click(function(e){
			e.preventDefault();

			if( $('.webdWoocommerceReportingStatistics #extendwp_extensions_popup').length > 0 ) {

				$(".webdWoocommerceReportingStatistics .get_ajax #extendwp_extensions_popup").fadeIn();

				$(".webdWoocommerceReportingStatistics #extendwp_extensions_popup .extendwp_close").click(function(e){
					e.preventDefault();
					$(".webdWoocommerceReportingStatistics #extendwp_extensions_popup").fadeOut();
				});
				var extensions = document.getElementById('extendwp_extensions_popup');
				window.onclick = function(event) {
					if (event.target === extensions) {
						extensions.style.display = "none";
						localStorage.setItem('hideIntro', '1');
					}
				}
			}else{
				var action = 'stat_extensions';
				$.ajax({
					type: 'POST',
					url: webdWoocommerceReportingStatistics.ajax_url,
					data: {
						"action": action
					},
					 beforeSend: function(data) {
						$("html, body").animate({ scrollTop: 0 }, "slow");
						$('.webdWoocommerceReportingStatistics').addClass('loading');
					},
					success: function (response) {
						$('.webdWoocommerceReportingStatistics').removeClass('loading');
						if( response !='' ){
							$('.webdWoocommerceReportingStatistics .get_ajax' ).css('visibility','hidden');
							$('.webdWoocommerceReportingStatistics .get_ajax' ).append( response );
							$('.webdWoocommerceReportingStatistics .get_ajax #extendwp_extensions_popup' ).css('visibility','visible');
							$(".webdWoocommerceReportingStatistics .get_ajax #extendwp_extensions_popup").fadeIn();

							$(".webdWoocommerceReportingStatistics #extendwp_extensions_popup .extendwp_close").click(function(e){
								e.preventDefault();
								$(".webdWoocommerceReportingStatistics #extendwp_extensions_popup").fadeOut();
							});
							var extensions = document.getElementById('extendwp_extensions_popup');
							window.onclick = function(event) {
								if (event.target === extensions) {
									extensions.style.display = "none";
									localStorage.setItem('hideIntro', '1');
								}
							}
						}
					},
					error:function(response){
						console.log('ERROR');
					}
				});
			}
		});

	load();

	function load(){

		function delay(callback, ms) {
		  var timer = 0;
		  return function() {
			var context = this, args = arguments;
			clearTimeout(timer);
			timer = setTimeout(function () {
			  callback.apply(context, args);
			}, ms || 0);
		  };
		}

	/*DATEPICKER*/
	$('.webdWoocommerceReportingStatistics .datepicker').datepicker({
	   //dateFormat: 'yy-mm-dd', //maybe you want something like this
	   dateFormat: 'dd-mm-yy',
		showButtonPanel: true
	});
	$('.webdWoocommerceReportingStatistics .datepicker').change(function(){
		$(this).val('PRO Version Only');
	});

	/*ACTIVATE ACCORDION*/
		$( ".webdWoocommerceReportingStatistics  #accordion" ).accordion({
			collapsible: true
		});

	/*ACTIVATE TABS*/
		$( ".webdWoocommerceReportingStatistics #tabs" ).tabs();
		$( ".webdWoocommerceReportingStatistics #tabs2" ).tabs();
		$( ".webdWoocommerceReportingStatistics #tabs3" ).tabs();

		$(".webdWoocommerceReportingStatistics .search").keyup(function () {
			var value = this.value.toLowerCase().trim();
			var table = $(this).parent();
			$(table).find('tr').each(function (index) {
				if (!index) return;
				$(this).find("td").each(function () {
					var id = $(this).text().toLowerCase().trim();
					var not_found = (id.indexOf(value) == -1);

					$(this).closest('tr').toggle(!not_found);
					return not_found;
				});

				if($(this).css('display') == 'none'){
					$(this).addClass("noExl");
				}else $(this).removeClass("noExl");
			});
		});
	}

		$("#webdWoocommerceReportingStatistics_signup").on('submit',function(e){
			e.preventDefault();
			var dat = $(this).serialize();
			$.ajax({

				url:	"https://extend-wp.com/wp-json/signups/v2/post",
				data:  dat,
				type: 'POST',
				beforeSend: function(data) {
						console.log(dat);
				},
				success: function(data){
					alert(data);
				},
				complete: function(data){
					dismiss();
				}
			});
		});

		function dismiss(){

				var ajax_options = {
					action: 'push_not',
					data: 'title=1',
					nonce: 'push_not',
					url: webdWoocommerceReportingStatistics.ajax_url,
				};

				$.post( webdWoocommerceReportingStatistics.ajax_url, ajax_options, function(data) {
					$(".webdWoocommerceReportingStatistics_notification").fadeOut();
				});

		}

		$(".webdWoocommerceReportingStatistics_notification .dismiss").on('click',function(e){
				dismiss();
				console.log('clicked');

		});

	// get url parameters function
	$.urlParam = function(name){
		var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec( window.location.href );
		if (results==null) {
		   return null;
		}
		return decodeURI(results[1]) || 0;
	}

	// Main variables and arrays

	var totalOrders='';
	const tabs = webdWoocommerceReportingStatistics.tab;
	const page = webdWoocommerceReportingStatistics.page;
	const custom_fields = webdWoocommerceReportingStatistics.custom_fields;
	const limit = webdWoocommerceReportingStatistics.limit;
	if( custom_fields ) custom_fields.unshift( "order_id" );
	var days = '';
	var unique_dates = [];
	var total = 0;
	var sumOrders = 0;
	var avg_per_day = total / days;
	var subtotal = 0;
	var sumProducts = 0;
	var numProducts = 0;
	var shipping = 0;
	var tax = 0;
	var discount = 0;
	var refund = 0;
	var width = 1;
	var offset = 0;
	var ordersTable = $('#orders table tbody');
	var customersTable = $('#customers table tbody');
	var productsTable = $('#products table tbody');
	var countriesTable = $('#countries table tbody');
	var paymentsTable = $('#payment table tbody');
	var couponsTable = $('#coupons table tbody');
	var categoriesTable = $('#categories table tbody');
	var orders=[];
	var customerstoGroup=[];
	var customers=[];
	var products=[];
	var productstoGroup=[];
	var categories=[];
	var categoriestoGroup=[];
	var countriestoGroup=[];
	var countries=[];
	var paymentstoGroup=[];
	var payments=[];
	var couponstoGroup=[];
	var coupons=[];
	var customTable = $('#custom table tbody');
	var customTableHead = $('#custom table thead');
	var custom=[];
	var order_id = 0;
	var periodsTable = $('.periods_table table tbody');
	var periodstoGroup=[];
	var periods = [];
	var colors = [];
	var custom_labels=[];
	var ordersLength=1;
	const totals=[];

	// Filters variables and arrays
	var theoffset = 0;
	var monthsFilter = $('.reportCalendar #month');
	var yearsFilter = $('.reportCalendar #year');
	var countriesFilter = $('.reportCalendar #billing_country');
	var citiesFilter = $('.reportCalendar #billing_city');
	var paymentsFilter = $('.reportCalendar #payment_method');
	var querybyCity = webdWoocommerceReportingStatistics.querybyCity;
	var querybyCountry = webdWoocommerceReportingStatistics.querybyCountry;
	var querybyPayment = webdWoocommerceReportingStatistics.querybyPayment;
	var countriesArray = [];
	var  countrycodesArray = [];
	var citiesArray = [];
	var paymentsidsArray = [];
	var paymentsArray = [];
	var monthsArray = [];
	var yearsArray = [];
	var objectLength=1;
	var categoriesidsArray = [];
	var categoriessArray = [];
	var categoriesFilter =  $('.reportCalendar #cat');
	var sumitOrders = '';
	var currency = webdWoocommerceReportingStatistics.currency;
	var paypal = 0;
	var net = 0;
	var populated = 0;

	var orderids =[];

	function getOrders( ) {

				if ( ordersLength !=0 && ( totalOrders=='' || sumOrders < totalOrders )  ) {

					var selected = $("#selected").val();
					var order_status =  $("#order_status").val();
					var customer = $("#customer").val();

					$.ajax({
						type: 'POST',
						url: webdWoocommerceReportingStatistics.ajax_url,
						data: {
							'action': 'getOrders',
							'tab': $.urlParam('tab'),
							'action': 'getOrders',
							'offset': offset,
							'selected': selected,
							'order_status': order_status,
							'customer': customer,
							'_wpnonce': 'webdWoocommerceReportingStatistics',
						},
						beforeSend: function()  {

							 $(".progress").html( webdWoocommerceReportingStatistics.orders_loading );
						},
						success: function ( result ) {

							var obj = JSON.parse(result);
							sumOrders += obj.length << 0; // here with add the number of elements in object to check if all orders are displayed and STOP the ajax!!

							$(".webdWoocommerceReportingStatistics .results_found").html( "<h3>" + obj[0]['message'] + "</h3>" );

							console.log( obj[0]['days'] );
							if(  $.isEmptyObject( obj ) || obj[0]['total_orders'] ==0 ){
								ordersLength = 0;
								noOrders();
								width = 100 *100 ;

							}else{

								totalOrders = obj[0]['total_orders'] << 0;
								days = obj[0]['days'] << 0;

								$.each(obj, function(key,val) {

									if( $.inArray( val.date_created , unique_dates ) == -1) unique_dates.push( val.date_created );

									orderids.push( val.order_id );

									subtotal +=  parseFloat( val.subtotal); // grouped results for overviw table
									total +=  parseFloat( val.total); // grouped results for overviw table
									tax += parseFloat( val.total_tax ); // grouped results for overviw table
									discount +=  parseFloat( val.total_discount ) // grouped results for overviw table
									refund +=    parseFloat( val.total_refunded );
									shipping +=    parseFloat( val.shipping_total );

									// POPULATE PRODUCTS NUMBER
									if( $.urlParam('page') == page && !$.urlParam('tab') || $.urlParam('tab') == 'all' ){

										var newproducts = val.products;
										$.each(newproducts, function(key,val) {
											 numProducts += val.quantity << 0;
											 sumProducts += val.total << 0;

										});

									}

								});

								$(".subtotal").html( subtotal.toFixed(2) + currency );
								$(".shipping").html( shipping.toFixed(2) + currency );
								$(".total").html( total.toFixed(2)+currency );
								$(".tax").html( tax.toFixed(2)+currency );
								$(".discount").html( discount.toFixed(2)  +currency );
								$(".shipping").html( shipping.toFixed(2) + currency);
								$(".num_orders").html( sumOrders );
								$(".num_products").html( numProducts );
								$(".sum_products").html( sumProducts + currency );
								$(".refund").html( refund.toFixed(2) + currency );

								if( total - refund ==0 ){
									$(".avg").html( "0" );
								}else $(".avg").html( ( ( total - refund ) / days ).toFixed(2)  +currency );
								$(".salesEvery").html( parseInt( days / unique_dates.length ) );

								offset += limit << 0;
								width = ( sumOrders / totalOrders  ) *100 ;

							}

							$(".progressBar").css( "width",  width + "%" );

							if( totalOrders != 0 && totalOrders != ''  ){

								$(".webdWoocommerceReportingStatistics .column1, .webdWoocommerceReportingStatistics .columns2,.webdWoocommerceReportingStatistics .overview").show();
									$("	.webdWoocommerceReportingStatistics #tabs2").css( 'visibility','visible' );
									$(".webdWoocommerceReportingStatistics .progress, .webdWoocommerceReportingStatistics .results_found, .webdWoocommerceReportingStatistics .overview").show();
									$(".webdWoocommerceReportingStatistics .no_orders").hide();
							}

						},complete: function(result){

								getOrders();
						}
					});

				}else{

					if( totalOrders != 0 && totalOrders != ''  ){

						load_orders();
						load_customers();
						load_countries();
						load_payments();
						load_coupons();
						load_products();
						load_categories();

					}

					$(".progress").html( webdWoocommerceReportingStatistics.orders_loaded );

					setTimeout(function(){
						$(".progress").fadeOut();
						$(".progressBar").fadeOut();
					},3000);

				}
			}

	var paged = 1; // Start from the first page

	function load_orders() {

		$.ajax({
			url: webdWoocommerceReportingStatistics.ajax_url,
			type: 'post',
			data: {
				action: 'get_orders',
				page: paged,
				ids: orderids
			},
			success: function(response) {
				var data = JSON.parse(response);

				if(data.orders.trim().length == 0){
					// If there are no orders, hide the pagination
					$('.orders-pagination').hide();
				} else {

					$('#orders table tbody').html(data.orders);

					// Update the pagination
					$('.orders-pagination').html('');
					if( data.max_num_pages >1 ){
						for( var i = 1; i <= data.max_num_pages; i++ ) {
							if( i == paged ) {
								$('.orders-pagination').append(' <a class="current" >' + i + '</a> ');
							} else {
								$('.orders-pagination').append(' <a class="page-numbers" >' + i + '</a> ');
							}
						}
					}
				}

			}
		});
	}

	function load_customers() {

		$.ajax({
			url: webdWoocommerceReportingStatistics.ajax_url,
			type: 'post',
			data: {
				action: 'get_customers',
				ids: orderids
			},
			success: function(response) {

				var data = JSON.parse(response);

				$('#customers table tbody').html(data.customers);

			}
		});
	}
	function load_countries() {

		$.ajax({
			url: webdWoocommerceReportingStatistics.ajax_url,
			type: 'post',
			data: {
				action: 'get_countries',
				ids: orderids
			},
			success: function(response) {

				var data = JSON.parse(response);

				countries_colors=[];
				countriesChartLabels=[];
				countriesChartTotals=[];

				$.each(data.name, function(key,val) {
					countries_colors.push( getRandomColor() );
					countriesChartLabels.push( val );
				});
				$.each(data.total, function(key,val) {
					countriesChartTotals.push( val );
				});
				$('#countries table tbody').html(data.countries);

				if (countriesChartTotals.length !== 0)chart( 'bar', 'Countries', 'byCountry', countriesChartLabels,  countriesChartTotals, countries_colors );

			}
		});
	}

	function load_payments() {

		$.ajax({
			url: webdWoocommerceReportingStatistics.ajax_url,
			type: 'post',
			data: {
				action: 'get_payments',
				ids: orderids
			},
			success: function(response) {

				var data = JSON.parse(response);

				payments_colors=[];
				paymentsChartLabels=[];
				paymentsChartTotals=[];

				$.each(data.name, function(key,val) {
					payments_colors.push( getRandomColor() );
					paymentsChartLabels.push( val );
				});
				$.each(data.total, function(key,val) {
					paymentsChartTotals.push( val );
				});
				$('#payment table tbody').html(data.payments);

				if (paymentsChartTotals.length !== 0)chart( 'bar', 'Payments', 'byPayment', paymentsChartLabels,  paymentsChartTotals, payments_colors );

			}
		});
	}

	function load_coupons() {

		$.ajax({
			url: webdWoocommerceReportingStatistics.ajax_url,
			type: 'post',
			data: {
				action: 'get_coupons',
				ids: orderids
			},
			success: function(response) {

				var data = JSON.parse(response);
				coupons_colors=[];
				couponsChartLabels	=[];
				couponsChartTotals	=[];

				$.each(data.name, function(key,val) {
					coupons_colors.push( getRandomColor() );
					couponsChartLabels.push( val );
				});
				$.each(data.total, function(key,val) {
					couponsChartTotals.push( val );
				});
				$('#coupons table tbody').html(data.coupons);

				if (couponsChartTotals.length !== 0)chart( 'pie', 'Coupons', 'couponCharts', couponsChartLabels,  couponsChartTotals, coupons_colors );

			}
		});
	}

	function load_products() {

		var product = $("#product").val();
		var cat = $("#cat").val();

		$.ajax({
			url: webdWoocommerceReportingStatistics.ajax_url,
			type: 'post',
			data: {
				action: 'get_products',
				ids: orderids,
				product: product,
				cat: cat
			},
			success: function(response) {

				var data = JSON.parse(response);

				products_colors=[];
				productsChartLabels	=[];
				productsChartTotals	=[];

				$.each(data.name, function(key,val) {
					products_colors.push( getRandomColor() );
					productsChartLabels.push( val );
				});
				$.each(data.total, function(key,val) {
					productsChartTotals.push( val );
				});
				$('#products table tbody').html( data.products );

				if (productsChartTotals.length !== 0)chart( 'bar','Products', 'productChart', productsChartLabels,  productsChartTotals, products_colors );

			}
		});
	}

	function load_categories() {

		var cat = $("#cat").val();
		var product = $("#product").val();

		$.ajax({
			url: webdWoocommerceReportingStatistics.ajax_url,
			type: 'post',
			data: {
				action: 'get_categories',
				ids: orderids,
				cat: cat,
				product: product
			},
			success: function(response) {

				var data = JSON.parse(response);

				categories_colors=[];
				categoriesChartLabels	=[];
				categoriesChartTotals	=[];

				$.each(data.name, function(key,val) {
					categories_colors.push( getRandomColor() );
					categoriesChartLabels.push( val );
				});
				$.each(data.total, function(key,val) {
					categoriesChartTotals.push( val );
				});
				$('#categories table tbody').html( data.categories );

				if (categoriesChartTotals.length !== 0)chart( 'pie','Categories', 'categoriesChart', categoriesChartLabels,  categoriesChartTotals, categories_colors );

			}
		});
	}

	function display_orders_by_period() {

		var cat = $("#cat").val();
		var product = $("#product").val();
		var order_status =  $("#order_status").val();
		var billing_country = $("#billing_country").val();
		var customer = $("#customer").val();
		var billing_city = $("#billing_city").val();
		var payment = $("#payment_method").val();

		$.ajax({
			url: webdWoocommerceReportingStatistics.ajax_url,
			type: 'post',
			data: {
				action: 'display_orders_by_period',
				tab: $.urlParam('tab'),
				order_status: order_status,
				customer: customer
			},
			beforeSend: function()  {

				$(".progress").html( webdWoocommerceReportingStatistics.orders_loading );
				$('.periods table , .columns2' ).hide();
			},
			success: function(response) {

				var data = JSON.parse(response);

				if( data.results != 0 ){

					$(".webdWoocommerceReportingStatistics .column1, .webdWoocommerceReportingStatistics .columns2 , .webdWoocommerceReportingStatistics .progress, .webdWoocommerceReportingStatistics .results_found").show();
					$(".webdWoocommerceReportingStatistics .no_orders").hide();
					$('.periods table , .columns2' ).show();

					periods_colors=[];
					periodsChartLabels	=[];
					periodsChartTotals	=[];

					$.each(data.name, function(key,val) {
						periods_colors.push( getRandomColor() );
						periodsChartLabels.push( val );
					});
					$.each(data.total, function(key,val) {
						periodsChartTotals.push( val );
					});

					$('.periods .avg_period').html( data.average );
					$('.periods .forecast').html( data.forecast );
					$('.periods_table table tbody').html( data.periods );
					$('.periods_table table .totals').html(  data.totals );

					gross_label = $('.gross_label').text();
					net_label = $('.net_label').text();

					if( order_status == 'wc-refunded' ) {
						$('.columns2').hide();
						$('.gross_label').text( gross_label + " Refunded" );
						$('.net_label').text( net_label + " Refunded" );
					}else{
						$('.columns2').show();
						$('.gross_label').text( gross_label  );
						$('.net_label').text( net_label  );
					}

					if (periodsChartTotals.length !== 0) chart( 'bar', 'Period', 'byPeriod', periodsChartLabels.reverse(),  periodsChartTotals.reverse(), periods_colors );

				}
				$(".webdWoocommerceReportingStatistics .results_found").html( data.message );

				$(".progress").html( webdWoocommerceReportingStatistics.orders_loaded );
					setTimeout(function(){
						$(".progress").fadeOut();
						$(".progressBar").fadeOut();
					},3000);
			}
		});
	}

	$(document).on('click', '.orders-pagination .page-numbers', function( e) {
		e.preventDefault();
		//console.log('Load More button clicked'); // Debugging line
		paged = parseInt($(this).text()); // Set the page number to the number clicked
		$('#orders table tbody').html(''); // Clear the orders container
		load_orders(); // Load the orders for the clicked page number
	});

	function noOrders(){

		setTimeout(function(){
			$(".progressBar").fadeOut();
		},3000 );

		$(".webdWoocommerceReportingStatistics .progress").hide();
		$(".webdWoocommerceReportingStatistics .overview").hide();
		$(".webdWoocommerceReportingStatistics #tabs2").css('visibility','hidden');
	}

	// Run getOrders only if url parameter specific
	if ( ( $.urlParam('page') == page && !$.urlParam('tab') ) || ( $.urlParam('page') == page && $.inArray( $.urlParam('tab') , tabs ) !== -1 ) ){
		getOrders();
	}
	if( $.urlParam('page') == page && ( $.urlParam('tab') =='months'  || $.urlParam('tab') =='years' ) ){
		display_orders_by_period();
	}

	// On form filters submission , rerun the getOrders function
	$('.reportCalendar form input[type=submit] ').click(function(e){

		e.preventDefault();
		totalOrders = '';
		sumOrders = 0;
		subtotal = 0;
		days = '';
		unique_dates = [];
		total = 0;
		sumOrders = 0;
		net = 0;
		sumProducts = 0;
		numProducts = 0;
		shipping = 0;
		total = 0;
		tax = 0;
		discount = 0;
		refund = 0;
		offset = 0;
		theoffset = 0;
		width:1;
		month='';
		year='';
		selected='';
		from='';
		to='';
		ordersLength=1;
		orderids =[];

		$( ".subtotal, .total , .tax, .discount, .shipping, .num_orders, .num_products, .sum_products, .refund, .avg, .salesEvery , .avg_period, .shipping , .results_found ").html( '' );
		$( ".chart-container canvas").remove();

		$.ajax({
			url: window.location.href,
			data: $(".reportCalendar form ").serialize(),
			type: 'POST',
			beforeSend: function() {
				//console.log( $(".reportCalendar form ").serialize() );
					if( custom_fields) customTableHead.empty();
					$(".webdWoocommerceReportingStatistics table tbody").empty();
					//$(".webdWoocommerceReportingStatistics canvas").remove();
					$(".webdWoocommerceReportingStatistics .tableexport-caption").remove();
					$(".progress").html("");
					$(".progress").show();
					$(".progressBar").css('width','0px');
					$(".progressBar").show();
			},
			success: function(response){

				if ( ( $.urlParam('page') == page && !$.urlParam('tab') ) || ( $.urlParam('page') == page && $.inArray( $.urlParam('tab') , tabs ) !== -1 ) ){
					getOrders();

				}
				if( $.urlParam('page') == page && ( $.urlParam('tab') =='months'  || $.urlParam('tab') =='years' ) ){
					display_orders_by_period();
				}

			}
		});

	});

	// help function to pick a random color for graphs
	function getRandomColor() {
		  var letters = '0123456789ABCDEF'.split('');
		  var color = '#';
		  for (var i = 0; i < 6 ; i++ ) {
			color += letters[Math.floor(Math.random() * 16)];
		}
		return color;
	}

	// main chart function for charts in reports
	function chart( type='bar', text, id , xValues, yValues, color ){

		$( ".webdWoocommerceReportingStatistics ."+id ).append("<canvas id='"+id+"'></canvas>");
		new Chart( id, {
		  type: type,
		  data: {
			labels: xValues,
			datasets: [{
			  backgroundColor: color,
			  data: yValues
			}]
		  },
		  options: {
			legend: {display: false},
			title: {
			  display: true,
			  text: text
			}
		  }
		});
	}

})( jQuery )

=== Advanced Reporting and Statistics for WooCommerce ===
Contributors: wpcodefactory
Tags: reporting, woocommerce statistics, sales report, analytics, woocommerce sales
Stable tag: 4.1.9
Requires at least: 4.6
Requires PHP: 7.0
Tested up to: 7.1
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Comprehensive WooCommerce reporting plugin: sales reports, statistics, analytics & forecasts for orders, products, customers, countries & more.

== Description ==

<p>A comprehensive WordPress Plugin for Advanced WooCommerce Reporting of Product Sales, Statistics, Analytics & Forecasting Tool for Orders, Sales, Products, Countries, Payment Gateways Shipping, Tax, Refunds, Top Products</p>

<p>Get Sales Report  - Reports of Product Sales for WooCommerce for either Completed Orders or Filter By Order Status </p>

== Fully compatible with WooCommerce HPOS Orders Tables ==

= Advanced WooCommerce Reporting Features =

* Visual Presentation with Bar & Charts
* WooCommerce Statistics for your Sales and Orders
* Search and Get Reports for Today, Yesterday, This Week, This Month, Last 2,3,6 Months
* Get reports by Customer
* Detailed Sales WooCommerce Reports
* Orders Sales Report
* Customers Sales Report
* Countries Sales Report
* WooCommerce Report By Payment Method
* WooCommerce Product Sales Report
* Product Category Sales Report
* Coupons Report
* Total Sales Analysis Grouped by Year, Month
* Next Month's Sales Forecast for WooCommerce
* TOP 5 WooCommerce Products Report
* 100% Responsive
* 100% with WooCommerce HPOS Orders Storage tables

**[Get PRO Version with Search Product-Category ability, Low Stock Reminders, Email Report & Export Capabilities](https://extend-wp.com/product/woocommerce-advanced-reporting-statistics/ "Advanced WooCommerce Reporting - Statistics & Forecast PRO")**

= PRO Features =

* Next Month's Forecast for Category or Product
* Advanced Search Customly defined specific period | From - To dates
* Search and Get Product Sales Reports by Month
* Search and Get Product Sales Reports by Year
* Get reports by City, Country, Payment Method
* Search & Sort List Reports with input field
* WooCommerce Sales Reports by Product
* WooCommerce Sales Reports by Category
* Next Month's Sales Forecast for Specified Product
* Next Month's Sales Forecast for Specified Category
* WooCommerce Stock Reports: Low Stock Reporting - you get a report for products low in stock, independent of sales
* WooCommerce Stock Reports: LLatest Selling Products Low Stock Reminder - report for low stock of products that actually sell!
* Email Report - get email on a Weekly or Monthly basis. The email includes This Month's, This Week's Report & Low Stock Management Report.
* Export Reports in excel file - save reports in xlsx format for further analysis

**[Get PRO Version with Search Product-Category ability, Low Stock Reminders, Email Report & Export Capabilities](https://extend-wp.com/product/woocommerce-advanced-reporting-statistics/ "Advanced WooCommerce Reporting - Statistics & Forecast PRO")**

[youtube https://www.youtube.com/watch?v=ZsjD-FUEOJs&rel=0]

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins` directory and unzip, or install the plugin through the WordPress plugins screen directly.
2. Activate Advanced Reporting - WooCommerce Statistics & Forecast through the 'Plugins' screen in WordPress
3. Use the Reporting & Statistics submenu  from WooCommerce to view the Sales Reports, Statistics and Forecast.

== Frequently Asked Questions ==

= Where to find forecasted Sales? =
Navigate to 'PER MONTH' tab and at the bottom of the table you will find a Next Month's Forecast, along with average sales per month.

= Orders are not loaded! what to do? =
Check that wp-cron is active . You can install WP Cron control plugin . check the events . Check WP CRON events for fetchOrders_for_stats action and press run now . This will download yours orders to text file.

= Where do I report security bugs found in this plugin? =
Please report security bugs found in the source code of the plugin through the [Patchstack Vulnerability Disclosure Program](https://patchstack.com/database/vdp/9e5fc701-d85e-4678-a0b4-5d5f26c56bf8). The Patchstack team will assist you with verification, CVE assignment, and notify the developers of this plugin.

== External services ==

This plugin connects to services provided by ExtendWP (https://extend-wp.com), operated by WPFactory, for the following purposes:

* Deactivation feedback: when you deactivate the plugin, the optional deactivation survey sends the reason you select and any comment you type to ExtendWP's feedback API (https://extend-wp.com/wp-json/feedback/v2/post). Nothing is sent unless you submit the survey.
* Extensions popup: when an administrator views the plugin's admin pages, the plugin fetches a list of other ExtendWP plugins from ExtendWP's products API (https://extend-wp.com/wp-json/products/v2/product/category/excel) to show them in the "More plugins" popup. No personal data is sent with this request.

ExtendWP's terms of service: https://wpfactory.com/terms-conditions/
ExtendWP's privacy policy: https://wpfactory.com/privacy-policy/

== Screenshots ==

1. Advanced WooCommerce Reporting - Statistics & Forecast | General Analysis - by date - here you find orders, customers, products, categories, coupons, countries, payment gateways report and tables.
2. Advanced WooCommerce Reporting - Statistics & Forecast | Analysis Grouped by year
3. Advanced WooCommerce Reporting - Statistics & Forecast | Analysis Grouped by Month and next Month's Forecast

== Changelog ==

= 4.1.9 - 29/10/2026 =
* Fix - Out of date libraries upgraded: Chart.js 4.6.1, WPFactory Admin Menu 1.1.4 and WPFactory Cross-selling 1.1.7.
* Dev - Documented the ExtendWP external services with terms and privacy links.
* Dev - "Requires at least" WordPress version set to major-only format (4.6).
* Dev - "Requires PHP" raised to 7.0 to match the actual code requirements.
* Dev - Free/Pro separation: removed the PRO-only product and category report filtering implementation from the free `get_products` and `get_categories` AJAX handlers (the filter UI stays as a PRO-only upsell).
* Dev - Added `wpfactory_wc_ars_report_sql_filter` filter for report query extensibility (default behavior unchanged).
* Dev - Code refactoring.
* Dev - `get_products`/`get_categories` now return an empty response when no order IDs are posted.
* Fix - Activation no longer deactivates the Pro version automatically (not allowed by the WordPress.org plugin guidelines). While Pro is active, the free version now stays activated but dormant, with an explanatory notice on the Plugins screen.
* Fix - WooCommerce HPOS compatibility is now declared from the main plugin file, so the plugin is not listed as incompatible with WooCommerce features while the Pro version is active.

= 4.1.8 - 27/10/2026 =
* Dev - Minimum required WordPress version raised to 4.6.0.

= 4.1.7 - 23/10/2026 =
* Fix - Improved escaping.
* Fix - AJAX requests now verify a nonce, and all request input is validated and sanitized.
* Fix - `date()` replaced with `gmdate()` for timezone-safe date handling.
* Fix - Deactivation survey scripts/styles enqueued with explicit versions and in the footer.
* Fix - Text domain updated to match the WordPress.org plugin slug (`webd-woocommerce-advanced-reporting-statistics`).
* Fix - Customer dropdown: blank labels fixed and the General tab customer filter now works.
* Fix - Customer filter now also matches guest orders placed with the customer's email.
* Fix - Customer names in reports fall back to display name or email.
* Dev - Language files (POT/PO/MO) renamed to the new text domain.
* Dev - Plugin renamed to "Advanced Reporting and Statistics for WooCommerce" for trademark compliance.
* Dev - Report SQL queries now use `$wpdb->prepare()` with placeholders.
* Dev - Minimum required WordPress version raised to 3.6.0.
* Dev - Removed the redundant `load_plugin_textdomain()` call (translations auto-load since WordPress 4.6).
* Dev - PHPCS / Plugin Check (PCP) security and code-quality issues addressed.
* Tested up to: 7.1.
* WC tested up to: 11.2.

= 4.1.6 - 24/05/2026 =
* Tested up to: 7.0.
* WC tested up to: 10.7.

= 4.1.5 - 09/02/2026 =
* Plugin name updated.
* WC tested up to: 10.5.

= 4.1.4 - 19/01/2026 =
* Fix - Security - "SQL Injection" vulnerability fixed (`wpdb::prepare()` applied).

= 4.1.3 - 14/01/2026 =
* Fix - Security - "SQL Injection" vulnerability fixed.
* Fix - Security - "Sensitive Data Exposure" vulnerability fixed.
* Dev - Code refactoring.
* Dev - Coding standards improved.

= 4.1.2 - 26/12/2025 =
* Fix - Security - Input sanitized.
* Dev - Code refactoring.

= 4.1.1 - 23/12/2025 =
* WC tested up to: 10.4.
* Tested up to: 6.9.

= 4.1.0 - 24/10/2025 =
* Fix - Security - Input sanitized.
* Fix - Text domain fixed.
* Dev - "More extensions" tab removed.
* Dev - Code refactoring and cleanup.
* Dev - Coding standards improved.
* WC tested up to: 10.3.

= 4.0.3 - 22/09/2025 =
* WC tested up to: 10.1.

= 4.0.2 - 10/06/2025 =
* Fix - Fix plugin icon.

= 4.0.1 - 06/06/2025 =
* Dev - Updated plugin icon and banner.

= 4.0.0 - 29/05/2025 =
* Fix - Translation loading fixed.
* Dev - PHP v8.2 compatibility (dynamic properties).
* Dev - Plugin settings moved to the "WPFactory" menu.
* Dev - "Recommendations" added.
* Dev - Code refactoring and cleanup.
* WC tested up to: 9.8.
* Tested up to: 6.8.

= 3.1 =
* Bug fixes.

= 3.0 =
* Bug fixes, include helper class file, check wp and woo latest compatibility.

= 2.9 =
* Bug fixes, include helper class file, check wp and woo latest compatibility.

= 2.8 =
* Code rewritten to handle larger orders queries - pagination added. No wp-cron required.

= 2.3 =
* Code rewritten to handle larger orders queries. Prerequisite is active wp-cron. Order data are saved in text file.
* Updates when new orders created or Order status updated.
* Added feature to query by Customer.
* Check WooCommerce - WP compatibility.

= 2.2 =
* Fix css issues.

= 2.1 =
* Fix disabled in js.
* Show sign up on plugin page only.

= 2.0 =
* Fix countries bar chart labels.

= 1.9 =
* Fix order edit link.

= 1.8 =
* Fix sorting on months report.

= 1.7 =
* Compatible with HPOS WooCommerce custom Orders table.

= 1.6 =
* Added feature to get Orders by Status - dropdown.
* Fixed get_used_coupons checking WooCommerce Version.
* Fixed isset php warnings.
* Fixed issue in customers tab.
* Fixed issue in forecast function.

= 1.5 =
* Check compatibility, fix translation ready strings.

= 1.4 =
* manage_options capability changed to manage_woocommerce so shop_manager role can access the plugin.

= 1.2 =
* Check wp compatibility - remove wp-load call.

= 1.1 =
* Fix sort table function breaking wp table sorting.

= 1.0 =
* Initial release.

== Upgrade Notice ==

= 3.1 =
Bug fixes.

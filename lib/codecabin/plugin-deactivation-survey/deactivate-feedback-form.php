<?php

namespace codecabin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(!is_admin())
	return;

global $pagenow;

if($pagenow != "plugins.php")
	return;

if(defined('CODECABIN_DEACTIVATE_FEEDBACK_FORM_INCLUDED'))
	return;
define('CODECABIN_DEACTIVATE_FEEDBACK_FORM_INCLUDED', true);

add_action('admin_enqueue_scripts', function() {
	
	// Enqueue scripts
	wp_enqueue_script('remodal', plugin_dir_url(__FILE__) . 'remodal.min.js', array(), '1.0.0', true);
	wp_enqueue_style('remodal', plugin_dir_url(__FILE__) . 'remodal.css', array(), '1.0.0');
	wp_enqueue_style('remodal-default-theme', plugin_dir_url(__FILE__) . 'remodal-default-theme.css', array(), '1.0.0');
	
	wp_enqueue_script('codecabin-deactivate-feedback-form', plugin_dir_url(__FILE__) . 'deactivate-feedback-form.js', array(), '1.0.0', true);
	wp_enqueue_style('codecabin-deactivate-feedback-form', plugin_dir_url(__FILE__) . 'deactivate-feedback-form.css', array(), '1.0.0');
	
	// Localized strings
	wp_localize_script('codecabin-deactivate-feedback-form', 'codecabin_deactivate_feedback_form_strings', array(
		'quick_feedback'			=> __('We are sorry to see you go...', 'webd-woocommerce-advanced-reporting-statistics'),
		'foreword'					=> __('If you would be kind enough, please tell us why you\'re deactivating?', 'webd-woocommerce-advanced-reporting-statistics'),
		'better_plugins_name'		=> __('Please tell us which plugin?', 'webd-woocommerce-advanced-reporting-statistics'),
		'please_tell_us'			=> __('Please tell us the reason so we can improve the plugin', 'webd-woocommerce-advanced-reporting-statistics'),
		'do_not_attach_email'		=> __('Do not send my e-mail address with this feedback', 'webd-woocommerce-advanced-reporting-statistics'),
		
		'brief_description'			=> __('Please give us any feedback that could help us improve', 'webd-woocommerce-advanced-reporting-statistics'),
		
		'cancel'					=> __('Cancel', 'webd-woocommerce-advanced-reporting-statistics'),
		'skip_and_deactivate'		=> __('Skip &amp; Deactivate', 'webd-woocommerce-advanced-reporting-statistics'),
		'submit_and_deactivate'		=> __('Submit &amp; Deactivate', 'webd-woocommerce-advanced-reporting-statistics'),
		'please_wait'				=> __('Please wait', 'webd-woocommerce-advanced-reporting-statistics'),
		'thank_you'					=> __('Thank you!', 'webd-woocommerce-advanced-reporting-statistics')
	));
	
	// Plugins
	$plugins = apply_filters('codecabin_deactivate_feedback_form_plugins', array());
	
	// Reasons
	$defaultReasons = array(
		'suddenly-stopped-working'	=> __('The plugin suddenly stopped working', 'webd-woocommerce-advanced-reporting-statistics'),
		'plugin-broke-site'			=> __('The plugin broke my site', 'webd-woocommerce-advanced-reporting-statistics'),
		'no-longer-needed'			=> __('I don\'t need this plugin any more', 'webd-woocommerce-advanced-reporting-statistics'),
		'found-better-plugin'		=> __('I found a better plugin', 'webd-woocommerce-advanced-reporting-statistics'),
		'temporary-deactivation'	=> __('It\'s a temporary deactivation, I\'m troubleshooting', 'webd-woocommerce-advanced-reporting-statistics'),
		'other'						=> __('Other', 'webd-woocommerce-advanced-reporting-statistics')
	);
	
	foreach($plugins as $plugin)
	{
		$plugin->reasons = apply_filters('codecabin_deactivate_feedback_form_reasons', $defaultReasons, $plugin);
	}
	
	// Send plugin data
	wp_localize_script('codecabin-deactivate-feedback-form', 'codecabin_deactivate_feedback_form_plugins', $plugins);
});

/**
 * Hook for adding plugins, pass an array of objects in the following format:
 *  'slug'		=> 'plugin-slug'
 *  'version'	=> 'plugin-version'
 * @return array The plugins in the format described above
 */
add_filter('codecabin_deactivate_feedback_form_plugins', function($plugins) {
	return $plugins;
});


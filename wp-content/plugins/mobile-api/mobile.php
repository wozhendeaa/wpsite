<?php
/**
 * Plugin Name: 2code Mobile API
 * Plugin URI: https://2code.info/
 * Description: 2code Mobile API.
 * Version: 3.4.3
 * Author: 2code
 * Author URI: https://2code.info/
 * License: GPL2
 *
 * Text Domain: mobile-api
 * Domain Path: /languages/
 */

$dir = mobile_api_dir();

define('mobile_api_base','api');
define('mobile_api_json_wp',rest_get_url_prefix());
define('mobile_api_version','3.4.3');

/* Updater */
require_once plugin_dir_path(__FILE__).'updater/elitepack-config.php';

/* Functions */
@include_once "$dir/functions/functions.php";
@include_once "$dir/functions/themes.php";
@include_once "$dir/functions/lang-options.php";
@include_once "$dir/functions/notification.php";

/* APIs */
@include_once "$dir/apis/apis.php";
@include_once "$dir/apis/delete.php";
@include_once "$dir/apis/notification.php";
@include_once "$dir/apis/question.php";
@include_once "$dir/apis/reactions.php";
@include_once "$dir/apis/user.php";

/* Rest */
@include_once "$dir/mobile-options.php";
@include_once "$dir/config-options.php";
@include_once "$dir/config-language.php";
@include_once "$dir/singletons/api.php";
@include_once "$dir/singletons/query.php";
@include_once "$dir/singletons/introspector.php";
@include_once "$dir/singletons/response.php";
@include_once "$dir/models/post.php";
@include_once "$dir/models/comment.php";
@include_once "$dir/models/category.php";
@include_once "$dir/models/tag.php";
@include_once "$dir/models/author.php";
@include_once "$dir/models/attachment.php";
@include_once "$dir/json-api-user.php";

/* Load plugin textdomain */
function mobile_api_load_textdomain() {
	load_plugin_textdomain('mobile-api',false,dirname(plugin_basename(__FILE__)).'/languages/');
}
add_action('plugins_loaded','mobile_api_load_textdomain');

add_action('admin_enqueue_scripts','mobile_api_enqueue_scripts',99);
function mobile_api_enqueue_scripts() {
	wp_enqueue_style("mobile-style",plugins_url('css/mobile-admin.css',__FILE__),array(),mobile_api_version);
}

function mobile_api_init() {
	global $mobile_api;
	if (phpversion() < 5) {
		add_action('admin_notices', 'mobile_api_php_version_warning');
		return;
	}
	if (!class_exists('MOBILE_API')) {
		add_action('admin_notices', 'mobile_api_class_warning');
		return;
	}
	add_filter('rewrite_rules_array', 'mobile_api_rewrites');
	$mobile_api = new MOBILE_API();
}

function mobile_api_php_version_warning() {
	echo "<div id=\"json-api-warning\" class=\"updated fade\"><p>Sorry, JSON API requires PHP version 5.0 or greater.</p></div>";
}

function mobile_api_class_warning() {
	echo "<div id=\"json-api-warning\" class=\"updated fade\"><p>Oops, MOBILE_API class not found. If you've defined a MOBILE_API_DIR constant, double check that the path is correct.</p></div>";
}

function mobile_api_activation() {
	// Add the rewrite rule on activation
	global $wp_rewrite;
	add_filter('rewrite_rules_array', 'mobile_api_rewrites');
	$wp_rewrite->flush_rules();
}

function mobile_api_deactivation() {
	// Remove the rewrite rule on deactivation
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

// Rewrite
function mobile_api_rewrites($wp_rules) {
	$base = mobile_api_base;
	if (empty($base)) {
		return $wp_rules;
	}
	$mobile_api_rules = array(
		"$base\$" => 'index.php?json=info',
		"$base/(.+)\$" => 'index.php?json=$matches[1]'
	);
	return array_merge($mobile_api_rules, $wp_rules);
}

// JSON dir
function mobile_api_dir() {
	if (defined('MOBILE_API_DIR') && file_exists(MOBILE_API_DIR)) {
		return MOBILE_API_DIR;
	} else {
		return dirname(__FILE__);
	}
}

// Add initialization and activation hooks
add_action('init','mobile_api_init');
register_activation_hook("$dir/json-api.php", 'mobile_api_activation');
register_deactivation_hook("$dir/json-api.php", 'mobile_api_deactivation');

// Require composer.
require __DIR__ . '/vendor/autoload.php';

// Require classes.
require __DIR__ . '/class-auth.php';
require __DIR__ . '/class-setup.php';

new MOBILEAPIAuth\Setup();

define('MOBILE_API_CORS_ENABLE', true);
define('MOBILE_API_SECRET_KEY', AUTH_KEY);?>
<?php

/**
 * Plugin Name:             TikTok Feed (Beta)
 * Plugin URI:              https://quadlayers.com/documentation/tiktok-feed/
 * Description:             Display beautiful and responsive galleries on your website from your TikTok feed account.
 * Version:                 4.0.1
 * Text Domain:             wp-tiktok-feed
 * Author:                  QuadLayers
 * Author URI:              https://quadlayers.com
 * License:                 GPLv3
 * Domain Path:             /languages
 * Request at least:        4.7.0
 * Tested up to:            6.2
 * Requires PHP:            5.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'QLTTF_PLUGIN_NAME', 'TikTok Feed (Beta)' );
define( 'QLTTF_PLUGIN_VERSION', '4.0.1' );
define( 'QLTTF_PLUGIN_FILE', __FILE__ );
define( 'QLTTF_PLUGIN_DIR', __DIR__ . DIRECTORY_SEPARATOR );
define( 'QLTTF_DOMAIN', 'qlttf' );
define( 'QLTTF_PREFIX', QLTTF_DOMAIN );
define( 'QLTTF_WORDPRESS_URL', 'https://wordpress.org/plugins/wp-tiktok-feed/' );
define( 'QLTTF_REVIEW_URL', 'https://wordpress.org/support/plugin/wp-tiktok-feed/reviews/?filter=5#new-post' );
define( 'QLTTF_DEMO_URL', 'https://quadlayers.com/demo/tiktok-feed/?utm_source=qlttf_admin' );
define( 'QLTTF_PURCHASE_URL', 'https://quadlayers.com/portfolio/tiktok-feed/?utm_source=qlttf_admin' );
define( 'QLTTF_SUPPORT_URL', 'https://quadlayers.com/account/support/?utm_source=qlttf_admin' );
define( 'QLTTF_DOCUMENTATION_URL', 'https://quadlayers.com/documentation/tiktok-feed/?utm_source=qlttf_admin' );
define( 'QLTTF_DOCUMENTATION_API_URL', 'https://quadlayers.com/documentation/tiktok-feed/api/?utm_source=qlttf_admin' );
define( 'QLTTF_DOCUMENTATION_ACCOUNT_URL', 'https://quadlayers.com/documentation/tiktok-feed/account/?utm_source=qlttf_admin' );
define( 'QLTTF_GROUP_URL', 'https://www.facebook.com/groups/quadlayers' );
define( 'QLTTF_DEVELOPER', false );
define( 'QLTTF_TIKTOK_URL', 'https://www.tiktok.com' );
define( 'QLTTF_PREMIUM_SELL_URL', 'https://quadlayers.com/portfolio/tiktok-feed/?utm_source=qlttf_admin' );
define( 'QLTTF_ACCOUNT_URL', admin_url( 'admin.php?page=qlttf_backend&tab=accounts' ) );

/**
 * Load composer autoload
 */
require_once __DIR__ . '/vendor/autoload.php';
/**
 * Load vendor_packages packages
 */
require_once __DIR__ . '/vendor_packages/wp-i18n-map.php';
require_once __DIR__ . '/vendor_packages/wp-dashboard-widget-news.php';
require_once __DIR__ . '/vendor_packages/wp-plugin-table-links.php';
require_once __DIR__ . '/vendor_packages/wp-notice-plugin-promote.php';
/**
 * Load plugin classes
 */
require_once __DIR__ . '/lib/class-plugin.php';

register_activation_hook(
	QLTTF_PLUGIN_FILE,
	function() {
		do_action( QLTTF_PREFIX . '_activation' );
	}
);

<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die( -1 );
}

/**
 * Load composer autoload
 */
require_once __DIR__ . '/vendor/autoload.php';

if ( ! is_multisite() ) {

	$model_accounts = new QuadLayers\TTF\Models\Accounts();
	$model_feeds    = new QuadLayers\TTF\Models\Feeds();
	$model_settings = new QuadLayers\TTF\Models\Settings();

	$settings = $model_settings->get();

	if ( ! empty( $settings['flush'] ) ) {

		$model_accounts->delete_table();
		$model_feeds->delete_table();
		$model_settings->delete_table();

	}
}

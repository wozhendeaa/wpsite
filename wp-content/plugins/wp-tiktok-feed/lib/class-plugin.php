<?php

namespace QuadLayers\TTF;

final class Plugin {

	protected static $instance;

	private function __construct() {
		/**
		 * Load plugin textdomain.
		 */
		load_plugin_textdomain( 'wp-tiktok-feed', false, QLTTF_PLUGIN_DIR . '/languages/' );
		/**
		 * Load plugin classes.
		 */
		Api\Rest\Routes_Library::instance();
		Backend\Load::instance();
		Frontend\Load::instance();
		/**
		 * Load plugin functions.
		 */
		do_action( 'qlttf_init' );
	}

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

}

Plugin::instance();


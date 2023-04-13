<?php
/**
 * Setup MOBILEAPIAuth.
 *
 * @package mobile-api
 */

namespace MOBILEAPIAuth;

/**
 * Setup MOBILEAPIAuthe.
 */
class Setup {
	/**
	 * Setup action & filter hooks.
	 */
	public function __construct() {
		$auth = new MOBILEAPIAuthe();
		add_action( 'rest_api_init', array( $auth, 'register_rest_routes' ) );
		add_filter( 'rest_api_init', array( $auth, 'add_cors_support' ) );
		add_filter( 'rest_pre_dispatch', array( $auth, 'rest_pre_dispatch' ), 10, 3 );
		add_filter( 'determine_current_user', array( $auth, 'determine_current_user' ) );
	}
}?>
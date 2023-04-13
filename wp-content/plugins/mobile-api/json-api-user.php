<?php
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

define('MOBILE_API_USER_HOME', dirname(__FILE__));

function mobile_api_action_links( $links ) {
	$links[] = '<a href="'. esc_url( get_admin_url(null, '/admin.php?page=mobile-api') ) .'">Mobile API</a>';
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'mobile_api_action_links', 10, 1 );

add_filter('mobile_api_controllers', 'mobile_api_pimJsonApiController');
add_filter('mobile_api_user_controller_path', 'mobile_api_setUserControllerPath');
add_action('init', 'mobile_api_user_checkAuthCookie', 100);

function mobile_api_pimJsonApiController($aControllers) {
	$aControllers[] = 'User';
	return $aControllers;
}

function mobile_api_setUserControllerPath($sDefaultPath) {
	return dirname(__FILE__) . '/controllers-api/User.php';
}

function mobile_api_user_checkAuthCookie($sDefaultPath) {
	global $mobile_api;

	if ($mobile_api->query->cookie) {
		$user_id = wp_validate_auth_cookie($mobile_api->query->cookie, 'logged_in');
		if ($user_id) {
			$user = get_userdata($user_id);

			wp_set_current_user($user->ID, $user->user_login);
		}
	}
}?>
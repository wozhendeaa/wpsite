<?php /* Notifications */
add_action('rest_api_init','mobile_api_notifications');
function mobile_api_notifications() {
	register_rest_route('mobile/v1','/notifications/',array(
		'callback' => 'mobile_api_notifications_endpoint',
		'permission_callback' => '__return_true',
	));
}
function mobile_api_notifications_endpoint($data) {
	$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
	if (!$auth) {
		$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
	}
	if (!$auth) {
		return array("status" => false,"error" => esc_html__('Authorization header not found.','mobile-api'));
	}
	if (!is_user_logged_in()) {
		return array("status" => false,"error" => esc_html__('Please login to see your notifications.','mobile-api'));
	}
	$page = $data->get_param('page');
	$return = mobile_api_get_notifications($page);
	if (!is_array($return) && $return == "no_notifications") {
		return array("status" => false,"error" => esc_html__('There are no notifications yet.','mobile-api'));
	}else {
		return array_merge(array("status" => true),$return);
	}
}
/* Dismiss all notifications */
add_action('rest_api_init','mobile_api_dismiss_notifications');
function mobile_api_dismiss_notifications() {
	register_rest_route('mobile/v1','/dismiss_notifications/',array(
		'callback' => 'mobile_api_dismiss_notifications_endpoint',
		'permission_callback' => '__return_true',
	));
}
function mobile_api_dismiss_notifications_endpoint($data) {
	$_POST["mobile"] = true;
	$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
	if (!$auth) {
		$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
	}
	if (!$auth) {
		return array("status" => false,"error" => esc_html__('Authorization header not found.','mobile-api'));
	}
	if (!is_user_logged_in()) {
		return array("status" => false,"error" => esc_html__('Please login to dismiss your notifications.','mobile-api'));
	}
	$user_id = get_current_user_id();
	mobile_api_dismiss_all_notifications($user_id);
	$device_token = $data->get_param('device_token');
	if ($device_token != "") {
		mobile_api_custom_notifications(0,array("badge0"),$device_token);
	}
	return array("status" => true,"user" => mobile_api_user_info($user_id));
}
/* Update notifications */
add_action('rest_api_init','mobile_api_update_notifications');
function mobile_api_update_notifications() {
	register_rest_route('mobile/v1','/update_notifications/',array(
		'callback' => 'mobile_api_update_notifications_endpoint',
		'permission_callback' => '__return_true',
	));
}
function mobile_api_update_notifications_endpoint($data) {
	$_POST["mobile"] = true;
	$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
	if (!$auth) {
		$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
	}
	if (!$auth) {
		return array("status" => false,"error" => esc_html__('Authorization header not found.','mobile-api'));
	}
	if (!is_user_logged_in()) {
		return array("status" => false,"error" => esc_html__('Please login to see your notifications.','mobile-api'));
	}
	mobile_api_update_notifications_themes();
	return array("status" => true);
}
/* Stop notifications */
add_action('rest_api_init','mobile_api_stop_notifications');
function mobile_api_stop_notifications() {
	register_rest_route('mobile/v1','/stop_notifications/',array(
		'methods' => 'POST',
		'callback' => 'mobile_api_stop_notifications_endpoint',
		'permission_callback' => '__return_true',
	));
}
function mobile_api_stop_notifications_endpoint($data) {
	$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
	if (!$auth) {
		$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
	}
	if (!$auth) {
		return array("status" => false,"error" => esc_html__('Authorization header not found.','mobile-api'));
	}
	$user_id = get_current_user_id();
	if ($user_id == 0) {
		return array("status" => false,"error" => esc_html__('Please login to see your notifications.','mobile-api'));
	}
	$stop = $data->get_param('stop');
	if ($stop == "on") {
		update_user_meta($user_id,"mobile_send_notification",$stop);
	}else {
		update_user_meta($user_id,"mobile_send_notification",$stop);
	}
	return array("status" => true);
}
/* Read notifications */
add_action('rest_api_init','mobile_api_read_notification');
function mobile_api_read_notification() {
	register_rest_route('mobile/v1','/read_notifications/',array(
		'methods' => 'GET',
		'callback' => 'mobile_api_read_notifications_endpoint',
		'permission_callback' => '__return_true',
	));
}
function mobile_api_read_notifications_endpoint($data) {
	$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
	if (!$auth) {
		$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
	}
	if (!$auth) {
		return array("status" => false,"error" => esc_html__('Authorization header not found.','mobile-api'));
	}
	$user_id = get_current_user_id();
	if ($user_id == 0) {
		return array("status" => false,"error" => esc_html__('Please login to see your notifications.','mobile-api'));
	}
	$id = $data->get_param('id');
	if ($id == '') {
		return array("status" => false,"error" => esc_html__('Include id var in your request.','mobile-api'));
	}
	mobile_api_read_notifications($user_id,$id);
	return array("status" => true,"readed" => true,"user" => mobile_api_user_info($user_id));
}
/* Silent notifications */
add_action('rest_api_init','mobile_api_silent_notification');
function mobile_api_silent_notification() {
	register_rest_route('mobile/v1','/silent_notifications/',array(
		'methods' => 'POST',
		'callback' => 'mobile_api_silent_notifications_endpoint',
		'permission_callback' => '__return_true',
	));
}
function mobile_api_silent_notifications_endpoint($data) {
	$device_token = $data->get_param('device_token');
	if ($device_token != "") {
		mobile_api_custom_notifications(0,array("badge0"),$device_token);
		return true;
	}
}
/* All notifications */
add_action('rest_api_init','mobile_api_all_notifications');
function mobile_api_all_notifications() {
	register_rest_route('mobile/v1','/all_notifications/',array(
		'callback' => 'mobile_api_all_notifications_endpoint',
		'permission_callback' => '__return_true',
	));
}
function mobile_api_all_notifications_endpoint($data) {
	$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
	if (!$auth) {
		$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
	}
	if (!$auth) {
		return array("status" => false,"error" => esc_html__('Authorization header not found.','mobile-api'));
	}
	if (!is_user_logged_in()) {
		return array("status" => false,"error" => esc_html__('Please login to see your notifications.','mobile-api'));
	}
	$page = $data->get_param('page');
	$return = mobile_api_get_notifications($page,"all");
	if (!is_array($return) && $return == "no_notifications") {
		return array("status" => false,"count_total" => 0,"count" => 0,"dismiss_all" => false,"pages" => 0,"error" => esc_html__('There are no notifications yet.','mobile-api'));
	}else {
		return array_merge(array("status" => true,"dismiss_all" => true),$return);
	}
}
?>
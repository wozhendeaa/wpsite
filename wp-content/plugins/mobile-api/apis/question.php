<?php
/* Close question */
add_action('rest_api_init','mobile_api_close_question');
function mobile_api_close_question() {
	register_rest_route('mobile/v1','/close/',array(
		'callback' => 'mobile_api_close_question_endpoint',
		'permission_callback' => '__return_true',
	));
}
function mobile_api_close_question_endpoint($data) {
	$id = $data->get_param('id');
	$_POST["mobile"] = true;
	$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
	if (!$auth) {
		$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
	}
	if (!$auth) {
		return array("status" => false,"error" => esc_html__('Authorization header not found.','mobile-api'));
	}
	if ($id == "") {
		return array("status" => false,"error" => esc_html__('Please add the question id.','mobile-api'));
	}
	mobile_api_question_close($id);
	return array("status" => true);
}
/* Open question */
add_action('rest_api_init','mobile_api_open_question');
function mobile_api_open_question() {
	register_rest_route('mobile/v1','/open/',array(
		'callback' => 'mobile_api_open_question_endpoint',
		'permission_callback' => '__return_true',
	));
}
function mobile_api_open_question_endpoint($data) {
	$id = $data->get_param('id');
	$_POST["mobile"] = true;
	$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
	if (!$auth) {
		$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
	}
	if (!$auth) {
		return array("status" => false,"error" => esc_html__('Authorization header not found.','mobile-api'));
	}
	if ($id == "") {
		return array("status" => false,"error" => esc_html__('Please add the question id.','mobile-api'));
	}
	mobile_api_question_open($id);
	return array("status" => true);
}
/* Bump questions */
add_action('rest_api_init','mobile_api_bump_question');
function mobile_api_bump_question() {
	register_rest_route('mobile/v1','/bump/',array(
		'methods' => 'POST',
		'callback' => 'mobile_api_bump_question_endpoint',
		'permission_callback' => '__return_true',
	));
}
function mobile_api_bump_question_endpoint($data) {
	$id = $data->get_param('id');
	$points = $data->get_param('points');
	$_POST["mobile"] = true;
	$_POST["post_id"] = $id;
	$_POST["input_add_point"] = $points;
	$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
	if (!$auth) {
		$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
	}
	if (!$auth) {
		return array("status" => false,"error" => esc_html__('Authorization header not found.','mobile-api'));
	}
	if ($id == "") {
		return array("status" => false,"error" => esc_html__('Please add the question id.','mobile-api'));
	}
	if ($points == "") {
		return array("status" => false,"error" => esc_html__('Please add the number of points.','mobile-api'));
	}
	$return = mobile_api_bump_questions();
	if ($return == "get_points") {
		return array("status" => true);
	}else {
		return array("status" => false,"error" => $return);
	}
}?>
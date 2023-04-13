<?php
/* Reactions */
add_action('rest_api_init','mobile_api_reactions');
function mobile_api_reactions() {
	register_rest_route('mobile/v1','/reactions/',array(
		'methods' => 'POST',
		'callback' => 'mobile_api_reactions_endpoint',
		'permission_callback' => '__return_true',
	));
}
function mobile_api_reactions_endpoint($data) {
	$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
	if (!$auth) {
		$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
	}
	if (!$auth) {
		return array("status" => false,"error" => esc_html__('Authorization header not found.','mobile-api'));
	}
	$post_id = (int)$data->get_param('post_id');
	$comment_id = (int)$data->get_param('comment_id');
	$type = esc_html($data->get_param('type'));
	$like = esc_html($data->get_param('like'));
	$_POST["mobile"] = true;
	$_POST["post_id"] = $post_id;
	$_POST["comment_id"] = $comment_id;
	$_POST["type"] = $type;
	$_POST["like"] = $like;
	if (has_wpqa()) {
		$return = wpqa_reactions($_POST);
	}
	$return = (isset($return)?$return:array());
	return array("status" => true,"return" => $return);
}?>
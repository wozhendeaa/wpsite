<?php
/* Delete */
add_action('rest_api_init','mobile_api_delete');
function mobile_api_delete() {
	register_rest_route('mobile/v1','/delete/',array(
		'callback' => 'mobile_api_delete_endpoint',
		'permission_callback' => '__return_true',
	));
}
function mobile_api_delete_endpoint($data) {
	$type = $data->get_param('type');
	$id = $data->get_param('id');
	$name = $data->get_param('name');
	$attach_id = $data->get_param('attach_id');
	$_POST["mobile"] = $_GET["mobile"] = true;
	$_POST["id"] = $_GET["id"] = $id;
	$_POST["name"] = $_GET["name"] = $name;
	$_POST["attach_id"] = $_GET["attach_id"] = $attach_id;
	$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
	if (!$auth) {
		$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
	}
	if (!$auth) {
		return array("status" => false,"error" => esc_html__('Authorization header not found.','mobile-api'));
	}
	$user_id = get_current_user_id();
	if ($attach_id > 0 && $type != "attachment_m") {
		wp_delete_attachment($attach_id,true);
	}
	if ($type == "user") {
		require_once(ABSPATH.'wp-admin/includes/user.php');
		wp_delete_user($user_id,0);
	}else if ($type == "post" || $type == mobile_api_questions_type || $type == mobile_api_asked_questions_type || $type == "posts" || $type == "group") {
		mobile_api_delete_posts($_POST,$id);
	}else if ($type == "comment_meta") {
		delete_comment_meta($id,$name);
	}else if ($type == "post_meta") {
		delete_post_meta($id,$name);
	}else if ($type == "attachment_m") {
		$attachment_m = get_post_meta($id,'attachment_m',true);
		if (isset($attachment_m) && is_array($attachment_m) && !empty($attachment_m)) {
			foreach ($attachment_m as $key => $value) {
				if ($value["added_file"] == $attach_id) {
					unset($attachment_m[$key]);
					wp_delete_attachment($value["added_file"],true);
				}
			}
		}
		if (isset($attachment_m) && is_array($attachment_m) && !empty($attachment_m)) {
			update_post_meta($id,'attachment_m',$attachment_m);
		}else {
			delete_post_meta($id,'attachment_m');
		}
	}else if ($type == "message") {
		$get_post = get_post($id);
		if (isset($get_post->ID)) {
			$post_author = $get_post->post_author;
			$message_user_id = get_post_meta($id,'message_user_id',true);
			mobile_api_delete_messages($id,$post_author,$user_id,$message_user_id);
		}
	}else if ($type == "comment") {
		mobile_api_delete_comments($id);
	}else if ($type == "user_meta" && $user_id > 0 && $name != "") {
		delete_user_meta($user_id,$name);
	}
	return array("status" => true);
}
?>
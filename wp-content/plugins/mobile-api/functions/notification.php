<?php // Mobile notifications
add_action(mobile_api_name_of_prefix()."_action_notifications_activities","mobile_api_action_notifications",1,12);
function mobile_api_action_notifications($notification_id,$user_id,$another_user_id,$username,$post_id,$comment_id,$text,$type,$more_text,$type_of_item,$new,$array) {
	if ($type == "notification" || $type == "notifications") {
		$mobile_send_notification = get_user_meta($user_id,"mobile_send_notification",true);
		if ($mobile_send_notification == "" || $mobile_send_notification == "on" || $mobile_send_notification == 1) {
			$devices_token = get_user_meta($user_id,"devices_token",true);
			if (is_array($devices_token) && !empty($devices_token)) {
				$app_key = mobile_api_options("app_key");

				if (has_askme()) {
					$notification_result = get_user_meta($user_id,$user_id."_notifications_".$notification_id,true);
				}else {
					$notification_result = mobile_api_notification_activity_result(get_post($notification_id),"notification");
				}
				$body = strip_tags(mobile_api_show_notifications($notification_result));
				$title = htmlspecialchars_decode(esc_attr(get_bloginfo('name','display')));

				$data["screen"] = "screenA";
				$data["click_action"] = "FLUTTER_NOTIFICATION_CLICK";
				if ($text == "add_message_user") {
					$data["type"] = "messages";
					//$data["type"] = "main";
					//$data["id"] = "messages";
					//home, sections, favorites, settings
					if ($text == "seen_message" || $text == "approved_message") {
						$data["id"] = "sent";
					}else {
						$data["id"] = "inbox";
					}
				}else if ($post_id > 0) {
					$post_type = get_post_type($post_id);
					if ($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type || $post_type == "post") {
						$data["type"] = $post_type;
						$data["id"] = $post_id;
					}
				}else if ($another_user_id > 0) {
					$data["type"] = "profile";
					$data["id"] = $another_user_id;
				}
				$data["body"] = $body;
				$data["title"] = $title;
				$data["messages"] = true;

				$notification["body"] = $body;
				$notification["title"] = $title;
				$notification["sound"] = "default";
				$notification["vibrate"] = "1";
				$notification["badge"] = "1";
				$notification["alert"] = "1";

				$fields = array(
					"body"             => json_encode($body),
					"title"            => json_encode($title),
					"registration_ids" => $devices_token,
					"notification"     => $notification,
					"data"             => $data,
					"priority"         => "high",
					//status
				);

				$args = array(
					'method'   => 'POST',
					'timeout'  => 45,
				    'blocking' => true,
				    'body'     => json_encode($fields),
					'headers'  => array(
						'Authorization' => 'key='.$app_key,
						'Content-Type' 	=> 'application/json; charset=utf-8'
					)
				);
				$response = wp_remote_post('https://fcm.googleapis.com/fcm/send',$args);
			}
		}
	}
}

// Mobile custom notifications
function mobile_api_custom_notifications($user_id,$array = array(),$custom_devices_token = "") {
	if ($custom_devices_token != "") {
		$devices_token = array($custom_devices_token);
	}else {
		if (is_array($user_id)) {
			if (!empty($user_id)) {
				foreach ($user_id as $value) {
					$devices_token_array = get_user_meta($value,"devices_token",true);
					if (is_array($devices_token_array) && !empty($devices_token_array)) {
						foreach ($devices_token_array as $devices) {
							$devices_token[] = $devices;
						}
					}
				}
			}
		}else {
			$devices_token = get_user_meta($user_id,"devices_token",true);
		}
	}
	if (isset($devices_token) && is_array($devices_token) && !empty($devices_token)) {
		$app_key = mobile_api_options("app_key");
		if (is_array($array) && in_array("badge0",$array)) {
			$fields = array(
				"notification"  => array(
					"badge" => 0
				),
				"priority" => "high",
				"registration_ids"  => $devices_token,
				"content_available" => true,
			);
		}else if (is_array($array) && in_array("logout",$array)) {
			$fields = array(
				"registration_ids"  => $devices_token,
				"content_available" => true,
				"data"              => array(
					"version" => 1,
					"logout"  => true
				),
			);
		}else if (is_array($array) && in_array("options",$array)) {
			$fields = array(
				"registration_ids"  => $devices_token,
				"content_available" => true,
				"data"              => array(
					"version" => "new",
				),
			);
		}else {
			$fields = array(
				"registration_ids"  => $devices_token,
				"content_available" => true,
				"data"              => array(
					"version" => 1,
				),
			);
		}

		$args = array(
			'method'   => 'POST',
			'timeout'  => 45,
		    'blocking' => true,
		    'body'     => json_encode($fields),
			'headers'  => array(
				'Authorization' => 'key='.$app_key,
				'Content-Type' 	=> 'application/json; charset=utf-8'
			)
		);
		$response = wp_remote_post('https://fcm.googleapis.com/fcm/send',$args);
	}
}

add_filter("send_password_change_email","mobile_api_password_changed",1,2);
function mobile_api_password_changed($return,$user) {
	if (isset($user["ID"])) {
		mobile_api_custom_notifications($user["ID"],array("logout"));
	}
	return false;
}

add_action('delete_user','mobile_api_delete_user');
function mobile_api_delete_user($user_id) {
	mobile_api_custom_notifications($user_id,array("logout"));
}

add_action('wpqa_register_welcome','mobile_api_send_custom_notifications',2,1);
function mobile_api_send_custom_notifications($user_id) {
	mobile_api_custom_notifications($user_id);
}

// Update the options
add_action(mobile_api_theme_name."_update_options","mobile_api_send_notification");
function mobile_api_send_notification() {
	$live_change_groups = mobile_api_options("mobile_live_change_groups");
	if (is_array($live_change_groups) && !empty($live_change_groups)) {
		foreach ($live_change_groups as $key => $value) {
			if ($value == 1) {
				$live_change_groups[$key] = $key;
			}
		}
	}
	$live_change_specific_users = mobile_api_options("mobile_live_change_specific_users");
	$live_change_specific_users = explode(",",$live_change_specific_users);
	$args = array(
		'role__in' => (isset($live_change_groups) && is_array($live_change_groups)?$live_change_groups:array()),
		'fields'   => 'ID',
	);
	$query = new WP_User_Query($args);
	$query = $query->get_results();
	$query = array_merge($query,$live_change_specific_users);
	if (isset($query) && !empty($query)) {
		foreach ($query as $user) {
			if ($user > 0) {
				$users[] = $user;
			}
		}
	}
	if (isset($users) && is_array($users) && !empty($users)) {
		mobile_api_custom_notifications($users,array("options"));
	}
}?>
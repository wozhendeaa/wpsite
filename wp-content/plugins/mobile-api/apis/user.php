<?php
/* Get current user */
add_action('rest_api_init','mobile_api_currentuser');
function mobile_api_currentuser() {
	register_rest_route('mobile/v1','/currentuser/',array(
		'callback' => 'mobile_api_currentuser_endpoint',
		'permission_callback' => '__return_true',
	));
}
function mobile_api_currentuser_endpoint($data) {
	$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
	if (!$auth) {
		$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
	}
	if (!$auth) {
		return array("status" => false,"error" => esc_html__('Authorization header not found.','mobile-api'));
	}
	return array("status" => true,"user" => mobile_api_user_info(get_current_user_id()));
}
/* Get user info */
add_action('rest_api_init','mobile_api_userinfo');
function mobile_api_userinfo() {
	register_rest_route('mobile/v1','/userinfo/',array(
		'callback' => 'mobile_api_userinfo_endpoint',
		'permission_callback' => '__return_true',
	));
}
function mobile_api_userinfo_endpoint($data) {
	$user_id = $data->get_param('user_id');
	$user_id = ($user_id != ""?(int)$user_id:'');
	$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
	if (!$auth) {
		$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
	}
	if ($user_id == "") {
		if (!$auth) {
			return array("status" => false,"error" => esc_html__('Authorization header not found.','mobile-api'));
		}
		if (!is_user_logged_in()) {
			return array("status" => false,"error" => esc_html__('Please login to see your profile.','mobile-api'));
		}
	}
	$get_current_user_id = get_current_user_id();
	$is_super_admin = is_super_admin($get_current_user_id);
	$get_user_id = ($user_id > 0?$user_id:$get_current_user_id);
	$first_info = mobile_api_user_info($get_user_id);
	$show_point_favorite = get_user_meta($user_id,"show_point_favorite",true);
	$owner = false;
	if ($get_current_user_id == $user_id) {
		$owner = true;
	}

	mobile_api_update_post_stats(0,$get_user_id);

	$following_me = get_user_meta($get_user_id,"following_me",true);
	$block_users = mobile_api_options("block_users");
	if ($block_users == mobile_api_checkbox_value) {
		if ($get_current_user_id > 0) {
			$get_block_users = get_user_meta($get_current_user_id,mobile_api_action_prefix()."_block_users",true);
		}
	}
	if (is_array($following_me) && !empty($following_me) && isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)) {
		$following_me = array_diff($following_me,$get_block_users);
	}
	$following_me  = (is_array($following_me) && !empty($following_me)?get_users(array('fields' => 'ID','include' => $following_me,'orderby' => 'registered')):array());
	$user_following = (int)(is_array($following_me)?count($following_me):0);
	if (is_array($following_me)) {
		$sliced_array = array_slice($following_me,0,3);
		foreach ($sliced_array as $key => $value) {
			$following[] = mobile_api_user_avatar_link(array("user_id" => $value,"size" => 128));
		}
	}
	if (has_askme()) {
		$user_profile_pages = mobile_api_options("user_profile_pages");
		if (is_array($user_profile_pages) && !empty($user_profile_pages)) {
			$ask_question_to_users = mobile_api_options('ask_question_to_users');
			$pay_ask = mobile_api_options('pay_ask');
			foreach ($user_profile_pages as $key => $value) {
				if (isset($value["value"]) && $value["value"] == $key) {
					$tabs_type = mobile_api_questions_type;
					$post_type = array();
					if ($key == "answers" || $key == "best-answers" || $key == "followers-answers") {
						$post_type = array("post_type" => "answer");
						$tabs_type = "answer";
					}else if ($key == "posts" || $key == "followers-posts") {
						$post_type = array("post_type" => "post");
						$tabs_type = "post";
					}else if ($key == "comments" || $key == "followers-comments") {
						$post_type = array("post_type" => "comment");
						$tabs_type = "comment";
					}else if ($key == "groups" || $key == "joined_groups" || $key == "managed_groups") {
						$post_type = array("post_type" => "group");
						$tabs_type = "group";
					}else if ($key == "asked" || $key == "asked-questions") {
						$post_type = array("post_type" => mobile_api_asked_questions_type);
						$tabs_type = mobile_api_asked_questions_type;
					}
					if ((!mobile_api_groups() && ($key == "groups" || $key == "joined_groups" || $key == "managed_groups")) || (($key == "asked" || $key == "asked-questions") && $ask_question_to_users != mobile_api_checkbox_value) || ($ask_question_to_users == mobile_api_checkbox_value && $key == "asked-questions" && $get_user_id != $get_current_user_id) || ($show_point_favorite != mobile_api_checkbox_value && !$owner && $key == "favorites") || ($show_point_favorite != mobile_api_checkbox_value && !$owner && $key == "followed") || ($show_point_favorite != mobile_api_checkbox_value && !$owner && $key == "followers-questions") || ($show_point_favorite != mobile_api_checkbox_value && !$owner && $key == "followers-answers") || ($show_point_favorite != mobile_api_checkbox_value && !$owner && $key == "followers-posts") || ($show_point_favorite != mobile_api_checkbox_value && !$owner && $key == "followers-comments") || ($show_point_favorite != mobile_api_checkbox_value && !$owner && $pay_ask == mobile_api_checkbox_value && $key == "paid-questions" && $pay_ask != "on" && $pay_ask != 1) || $key == "points" || $key == "i_follow" || $key == "followers") {
						unset($user_profile_pages[$key]);
					}else {
						$lang_tab = mobile_api_options(str_ireplace("-","","lang_profiletab".$key));
						$profile_tabs[] = array_merge($post_type,array("title" => ($lang_tab != ""?$lang_tab:$value["sort"]),"json" => mobile_api_get_profile_tabs."?get_tab=".str_ireplace("-","_","get_".$key)."&type=".$tabs_type));
					}
				}
			}
		}
	}else {
		$profile_page_menu = 'profile_page_menu';
		$locations = get_nav_menu_locations();
		if (isset($locations[$profile_page_menu])) {
		    $menu_profile_items = wp_get_nav_menu_items($locations[$profile_page_menu]);
			if (is_array($menu_profile_items) && !empty($menu_profile_items)) {
				$ask_question_to_users = mobile_api_options('ask_question_to_users');
				$pay_ask = mobile_api_options('pay_ask');
				foreach ($menu_profile_items as $menu_key => $menu_value) {
					if (strpos($menu_value->url,'#wpqa-') !== false) {
						$first_one = str_ireplace("#wpqa-","",$menu_value->url);
						$first_name = str_ireplace("#wpqa-","",$menu_value->title);
						$tabs_type = mobile_api_questions_type;
						$post_type = array();
						if ($first_one == "answers" || $first_one == "best-answers" || $first_one == "best_answers" || $first_one == "followers-answers" || $first_one == "followers_answers") {
							$post_type = array("post_type" => "answer");
							$tabs_type = "answer";
						}else if ($first_one == "posts" || $first_one == "followers-posts" || $first_one == "followers_posts") {
							$post_type = array("post_type" => "post");
							$tabs_type = "post";
						}else if ($first_one == "comments" || $first_one == "followers-comments" || $first_one == "followers_comments") {
							$post_type = array("post_type" => "comment");
							$tabs_type = "comment";
						}else if ($first_one == "groups" || $first_one == "joined_groups" || $first_one == "managed_groups") {
							$post_type = array("post_type" => "group");
							$tabs_type = "group";
						}else if ($first_one == "asked" || $first_one == "asked-questions" || $first_one == "asked_questions") {
							$post_type = array("post_type" => mobile_api_asked_questions_type);
							$tabs_type = mobile_api_asked_questions_type;
						}
						if ((!mobile_api_groups() && ($first_one == "groups" || $first_one == "joined_groups" || $first_one == "managed_groups")) || (($first_one == "asked" || $first_one == "asked-questions") && $ask_question_to_users != mobile_api_checkbox_value) || ($ask_question_to_users == mobile_api_checkbox_value && $first_one == "asked-questions" && $get_user_id != $get_current_user_id) || ($show_point_favorite != mobile_api_checkbox_value && !$owner && $first_one == "favorites") || ($show_point_favorite != mobile_api_checkbox_value && !$owner && $first_one == "followed") || ($show_point_favorite != mobile_api_checkbox_value && !$owner && ($first_one == "followers-questions" || $first_one == "followers_questions")) || ($show_point_favorite != mobile_api_checkbox_value && !$owner && ($first_one == "followers-answers" || $first_one == "followers_answers")) || ($show_point_favorite != mobile_api_checkbox_value && !$owner && ($first_one == "followers-posts" || $first_one == "followers_posts")) || ($show_point_favorite != mobile_api_checkbox_value && !$owner && ($first_one == "followers-comments" || $first_one == "followers_comments")) || ($show_point_favorite != mobile_api_checkbox_value && !$owner && $pay_ask == mobile_api_checkbox_value && $first_one == "paid-questions" && $pay_ask != "on" && $pay_ask != 1) || $first_one == "points" || $first_one == "i_follow" || $first_one == "following" || $first_one == "followers" || $first_one == "poll" || $first_one == "login" || $first_one == "login-popup" || $first_one == "signup" || $first_one == "signup-popup" || $first_one == "lost-password" || $first_one == "lost-password-popup" || $first_one == "add-category" || $first_one == "add-question" || $first_one == "add-question-popup" || $first_one == "add-group" || $first_one == "add-post" || $first_one == "add-post-popup" || $first_one == "subscriptions" || $first_one == "buy-points" || $first_one == "all-questions" || $first_one == "all-groups" || $first_one == "profile" || $first_one == "edit-profile" || $first_one == "password" || $first_one == "privacy" || $first_one == "mail-settings" || $first_one == "delete-account" || $first_one == "transactions" || $first_one == "withdrawals" || $first_one == "financial" || $first_one == "blocking" || $first_one == "pending-questions" || $first_one == "pending-posts" || $first_one == "notifications" || $first_one == "activities" || $first_one == "referrals" || $first_one == "messages" || $first_one == "logout") {
							unset($menu_profile_items[$menu_key]);
						}else {
							$profile_tabs[] = array_merge($post_type,array("title" => $first_name,"json" => mobile_api_get_profile_tabs."?get_tab=".str_ireplace("-","_","get_".$first_one)."&type=".$tabs_type));
						}
					}
				}
			}
		}
	}

	$custom_permission = mobile_api_options("custom_permission");
	$send_message = mobile_api_options("send_message");
	$send_message_no_register = mobile_api_options("send_message_no_register");
	$user_block_message = get_user_meta($user_id,"user_block_message",true);
	$received_message = get_user_meta($user_id,"received_message",true);
	$block_message = get_user_meta($user_id,"block_message",true);
	if (is_user_logged_in()) {
		$user_is_login = get_userdata($get_current_user_id);
		$roles = $user_is_login->allcaps;
	}
	if ($is_super_admin || $custom_permission != "on" || ($custom_permission == "on" && ($get_current_user_id > 0 && !$is_super_admin && isset($roles["send_message"])) || ($get_current_user_id == 0 && $send_message == "on"))) {
		if ($is_super_admin || ((($get_current_user_id == 0 && $send_message_no_register == mobile_api_checkbox_value) || $get_current_user_id > 0) && ($block_message != "on" && $block_message != 1) && ($received_message == "on" || $received_message == 1) && (empty($user_block_message) || (isset($user_block_message) && is_array($user_block_message) && !in_array($user_id,$user_block_message))))) {
			$allow_to_receive_messages = true;
		}
	}

	$second_info = array(
		"receive_messages" => (isset($allow_to_receive_messages)?true:false),
		"following"        => $user_following,
		"user_following"   => (isset($following) && is_array($following)?$following:array()),
		"cover"            => mobile_api_user_cover_link(array("user_id" => $get_user_id)),
		"tabs"             => (isset($profile_tabs) && is_array($profile_tabs) && !empty($profile_tabs)?$profile_tabs:array())
	);

	$following_you  = get_user_meta($get_user_id,"following_you",true);
	$third_info = (($get_current_user_id > 0 && $get_current_user_id != $get_user_id)?array("followed" => (!empty($following_you) && in_array($get_current_user_id,$following_you)?true:false)):array());

	$fourth_info = array();
	if ($block_users == mobile_api_checkbox_value && !$is_super_admin && $get_current_user_id > 0 && $get_current_user_id != $get_user_id && isset($get_block_users)) {
		if (!empty($get_block_users) && in_array($user_id,$get_block_users)) {
			$fourth_info = array("unblock" => true);
		}else {
			$fourth_info = array("block" => true);
		}
	}

	$fifth_info = array();
	$report_users = mobile_api_options("report_users");
	if ($report_users == mobile_api_checkbox_value && $get_user_id != $get_current_user_id && $get_current_user_id > 0) {
		$fifth_info = array(
			"mobile_report_user" => true,
		);
	}

	$sixth_info = array();
	$user_meta_avatar = mobile_api_avatar_name();
	$user_meta_cover = (has_wpqa()?wpqa_cover_name():"");
	$user_meta = get_user_meta($get_current_user_id,$user_meta_avatar,true);
	if ($get_current_user_id > 0 && ((!is_array($user_meta) && $user_meta != "") || (is_array($user_meta) && isset($user_meta["id"]) && $user_meta["id"] != 0))) {
		$sixth_info["delete"]["avatar"]["api"] = mobile_api_delete.'?type=user_meta&name='.$user_meta_avatar.'&attach_id='.(is_array($user_meta) && isset($user_meta["id"]) && $user_meta["id"] != 0?$user_meta['id']:$user_meta);
		$sixth_info["delete"]["avatar"]["alert"] = esc_html__('Are you sure you want to delete the image?','mobile-api');
	}
	if ($user_meta_cover != "") {
		$user_meta = get_user_meta($get_current_user_id,$user_meta_cover,true);
		if ($get_current_user_id > 0 && ((!is_array($user_meta) && $user_meta != "") || (is_array($user_meta) && isset($user_meta["id"]) && $user_meta["id"] != 0))) {
			$sixth_info["delete"]["cover"]["api"] = mobile_api_delete.'?type=user_meta&name='.$user_meta_cover.'&attach_id='.(is_array($user_meta) && isset($user_meta["id"]) && $user_meta["id"] != 0?$user_meta['id']:$user_meta);
			$sixth_info["delete"]["cover"]["alert"] = esc_html__('Are you sure you want to delete the image?','mobile-api');
		}
	}
	
	return array_merge(array("status" => true),$first_info,$second_info,$third_info,$fourth_info,$fifth_info,$sixth_info);
}
/* Get countries */
add_action('rest_api_init','mobile_api_countries');
function mobile_api_countries() {
	register_rest_route('mobile/v1','/countries/',array(
		'callback' => 'mobile_api_countries_endpoint',
		'permission_callback' => '__return_true',
	));
}
function mobile_api_countries_endpoint($data) {
	$get_countries = mobile_api_get_countries();
	return $get_countries;
}
?>
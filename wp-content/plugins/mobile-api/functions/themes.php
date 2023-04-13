<?php define("mobile_api_theme_name",get_option("get_theme_name"));
/* Has WPQA plugin */
if (!function_exists('has_wpqa')):
	function has_wpqa() {
		return class_exists('WPQA');
	}
endif;
define("mobile_api_excerpt_type",(has_wpqa()?wpqa_excerpt_type:"characters"));
/* Has mobile social */
if (!function_exists('mobile_api_social')):
	function mobile_api_social() {
		return function_exists('mobile_app_social_init');
	}
endif;
/* Has mobile groups */
if (!function_exists('mobile_api_groups')):
	function mobile_api_groups() {
		return function_exists('mobile_app_groups_init');
	}
endif;
/* Has mobile search */
if (!function_exists('mobile_api_search')):
	function mobile_api_search() {
		return function_exists('mobile_app_search_init');
	}
endif;
/* Has mobile messages */
if (!function_exists('mobile_api_messages')):
	function mobile_api_messages() {
		return function_exists('mobile_app_messages_init');
	}
endif;
/* Has Questy theme */
if (!function_exists('has_questy')) {
	function has_questy() {
		$get_theme_name = get_option("get_theme_name");
		return ($get_theme_name == "questy"?true:false);
	}
}
/* Has Knowly theme */
if (!function_exists('has_knowly')) {
	function has_knowly() {
		$get_theme_name = get_option("get_theme_name");
		return ($get_theme_name == "knowly"?true:false);
	}
}
/* Has Himer theme */
if (!function_exists('has_himer')) {
	function has_himer() {
		$get_theme_name = get_option("get_theme_name");
		return ($get_theme_name == "himer"?true:false);
	}
}
/* Has Discy theme */
if (!function_exists('has_discy')) {
	function has_discy() {
		$get_theme_name = get_option("get_theme_name");
		return ($get_theme_name == "discy"?true:false);
	}
}
/* Has Ask Me theme */
if (!function_exists('has_askme')) {
	function has_askme() {
		$get_theme_name = get_option("get_theme_name");
		return ($get_theme_name == "askme"?true:false);
	}
}
/* Theme checkbox value */
function mobile_api_checkbox_value() {
	$get_theme_name = get_option("get_theme_name");
	if ($get_theme_name == "askme") {
		$checkbox_value = 1;
	}else {
		$checkbox_value = "on";
	}
	return $checkbox_value;
}
define("mobile_api_checkbox_value",mobile_api_checkbox_value());
/* Questions */
function mobile_api_questions_type() {
	if (has_askme()) {
		$return = (defined("ask_questions_type")?ask_questions_type:"question");
	}else if (has_wpqa()) {
		$return = (defined("wpqa_questions_type")?wpqa_questions_type:"question");
	}
	if (isset($return)) {
		return $return;
	}
}
define("mobile_api_questions_type",mobile_api_questions_type());
/* Asked questions */
function mobile_api_asked_questions_type() {
	if (has_askme()) {
		$return = (defined("ask_asked_questions_type")?ask_asked_questions_type:"asked-question");
	}else if (has_wpqa()) {
		$return = (defined("wpqa_asked_questions_type")?wpqa_asked_questions_type:"asked-question");
	}
	if (isset($return)) {
		return $return;
	}
}
define("mobile_api_asked_questions_type",mobile_api_asked_questions_type());
/* Question categories */
function mobile_api_question_categories() {
	if (has_askme()) {
		$return = (defined("ask_question_category")?ask_question_category:"question-category");
	}else if (has_wpqa()) {
		$return = (defined("wpqa_question_categories")?wpqa_question_categories:"question-category");
	}
	if (isset($return)) {
		return $return;
	}
}
define("mobile_api_question_categories",mobile_api_question_categories());
/* Question tags */
function mobile_api_question_tags() {
	if (has_askme()) {
		$return = (defined("ask_question_tags")?ask_question_tags:"question_tags");
	}else if (has_wpqa()) {
		$return = (defined("wpqa_question_tags")?wpqa_question_tags:"question_tags");
	}
	if (isset($return)) {
		return $return;
	}
}
define("mobile_api_question_tags",mobile_api_question_tags());
/* Knowledgebases */
function mobile_api_knowledgebase_type() {
	if (has_wpqa()) {
		$return = (defined("wpqa_knowledgebase_type")?wpqa_knowledgebase_type:"knowledgebase");
	}
	if (isset($return)) {
		return $return;
	}
}
define("mobile_api_knowledgebase_type",mobile_api_knowledgebase_type());
/* Knowledgebase categories */
function mobile_api_knowledgebase_categories() {
	if (has_wpqa()) {
		$return = (defined("wpqa_knowledgebase_categories")?wpqa_knowledgebase_categories:"kb");
	}
	if (isset($return)) {
		return $return;
	}
}
define("mobile_api_knowledgebase_categories",mobile_api_knowledgebase_categories());
/* Knowledgebase tags */
function mobile_api_knowledgebase_tags() {
	if (has_wpqa()) {
		$return = (defined("wpqa_knowledgebase_tags")?wpqa_knowledgebase_tags:"kb_tags");
	}
	if (isset($return)) {
		return $return;
	}
}
define("mobile_api_knowledgebase_tags",mobile_api_knowledgebase_tags());
/* Theme multicheck type */
function mobile_api_multicheck_type() {
	$get_theme_name = get_option("get_theme_name");
	if ($get_theme_name == "askme") {
		$multicheck_type = "multicheck_3";
	}else {
		$multicheck_type = "multicheck";
	}
	return $multicheck_type;
}
define("mobile_api_multicheck_type",mobile_api_multicheck_type());
/* Theme color */
function mobile_api_theme_color() {
	if (has_discy()) {
		$theme_color = "#2d6ff7";
	}else if (has_himer()) {
		$theme_color = "#2e6ffd";
	}else if (has_knowly()) {
		$theme_color = "#1eb88d";
	}else if (has_questy()) {
		$theme_color = "#1173ee";
	}else {
		$theme_color = "#ff7361";
	}
	return $theme_color;
}
define("mobile_api_theme_color",mobile_api_theme_color());
/* Theme name */
function mobile_api_theme_name() {
	if (has_discy()) {
		$theme_name = "Discy";
	}else if (has_himer()) {
		$theme_name = "Himer";
	}else if (has_knowly()) {
		$theme_name = "Knowly";
	}else if (has_questy()) {
		$theme_name = "Questy";
	}else {
		$theme_name = "Ask Me";
	}
	return $theme_name;
}
/* Theme prefix */
function mobile_api_theme_prefix() {
	if (has_discy()) {
		$theme_prefix = "discy";
	}else if (has_himer()) {
		$theme_prefix = "himer";
	}else if (has_knowly()) {
		$theme_prefix = "knowly";
	}else if (has_questy()) {
		$theme_prefix = "questy";
	}else {
		$theme_prefix = "vbegy";
	}
	return $theme_prefix;
}
define("mobile_api_theme_prefix",mobile_api_theme_prefix());
/* Meta prefix */
function mobile_api_meta_prefix() {
	if (has_discy()) {
		$theme_prefix = "discy";
	}else if (has_himer()) {
		$theme_prefix = "himer";
	}else if (has_knowly()) {
		$theme_prefix = "knowly";
	}else if (has_questy()) {
		$theme_prefix = "questy";
	}else {
		$theme_prefix = "vbegy";
	}
	return $theme_prefix;
}
define("mobile_api_meta_prefix",mobile_api_meta_prefix()."_");
/* Author prefix */
function mobile_api_author_prefix() {
	if (has_discy()) {
		$theme_prefix = "discy";
	}else if (has_himer()) {
		$theme_prefix = "himer";
	}else if (has_knowly()) {
		$theme_prefix = "knowly";
	}else if (has_questy()) {
		$theme_prefix = "questy";
	}else {
		$theme_prefix = "vbegy";
	}
	return $theme_prefix;
}
define("mobile_api_author_prefix",mobile_api_author_prefix()."_");
/* Action prefix */
function mobile_api_action_prefix() {
	if (has_wpqa()) {
		$theme_prefix = "wpqa";
	}else {
		$theme_prefix = "askme";
	}
	return $theme_prefix;
}
/* Theme options */
function mobile_api_options_name() {
	if (has_askme()) {
		$return = askme_options;
	}else if (has_discy()) {
		$return = discy_options;
	}else if (has_himer()) {
		$return = himer_options;
	}else if (has_knowly()) {
		$return = knowly_options;
	}else if (has_questy()) {
		$return = questy_options;
	}
	if (isset($return)) {
		return $return;
	}
}
/* Question poll */
define("mobile_api_question_poll",(has_wpqa()?"wpqa_question_poll_mobile":"mobile_api_question_poll"));
/* Theme Settings */
function mobile_api_options($name) {
	if (has_askme() || has_wpqa()) {
		if (has_askme()) {
			$data = askme_options($name);
		}else {
			$data = wpqa_options($name);
		}
		return $data;
	}
}
/* Get avatar link */
function mobile_api_user_avatar_link($args = array()) {
	if (has_askme()) {
		$return = askme_get_user_avatar_link($args);
	}else if (has_wpqa()) {
		$return = wpqa_get_user_avatar_link($args);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Emails */
function mobile_api_send_mail($args = array()) {
	if (has_askme()) {
		$return = askme_send_mail($args);
	}else if (has_wpqa()) {
		$return = wpqa_send_mail($args);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Email content */
function mobile_api_email_code($content,$mail = "",$schedule = "",$user_id = "") {
	if (has_askme()) {
		$return = askme_email_code($content,$mail,$schedule,$user_id);
	}else if (has_wpqa()) {
		$return = wpqa_email_code($content,$mail,$schedule,$user_id);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Send email */
function mobile_api_send_mails($args = array()) {
	if (has_askme()) {
		$return = askme_send_mails($args);
	}else if (has_wpqa()) {
		$return = wpqa_send_mails($args);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Get cover */
function mobile_api_user_cover_link($args = array()) {
	if (has_wpqa()) {
		$return = wpqa_get_user_cover_link($args);
	}else {
		$return = "";
	}
	if (isset($return)) {
		return $return;
	}
}
/* Check privacy */
function mobile_api_check_user_privacy($user_id,$privacy_key) {
	if (has_wpqa()) {
		$return = wpqa_check_user_privacy($user_id,$privacy_key);
	}else {
		$return = true;
	}
	if (isset($return)) {
		return $return;
	}
}
/* Get badge */
function mobile_api_get_badge($author_id,$return_badge = "",$points = "",$category_points = "") {
	if (has_wpqa()) {
		$return = wpqa_get_badge($author_id,$return_badge,$points,$category_points);
	}else if (has_askme()) {
		$return = vpanel_get_badge($author_id,$return_badge);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Get points for badges page */
function mobile_api_get_points() {
	if (has_askme()) {
		$return = askme_get_points();
	}else if (has_wpqa()) {
		$return = wpqa_get_points();
	}
	if (isset($return)) {
		return $return;
	}
}
function mobile_api_get_points_name($key) {
	if (has_askme()) {
		$return = askme_get_points_name($key);
	}else if (has_wpqa()) {
		$return = wpqa_get_points_name($key);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Get the avatar meta */
function mobile_api_avatar_name() {
	if (has_askme()) {
		$return = askme_avatar_name();
	}else if (has_wpqa()) {
		$return = wpqa_avatar_name();
	}
	if (isset($return)) {
		return $return;
	}
}
/* Report */
function mobile_api_report($data,$type) {
	if (has_askme()) {
		if ($type == "answer") {
			$return = report_c($data);
		}else if ($type == "user") {
			$return = askme_report_user($data);
		}else {
			$return = report_q($data);
		}
	}else if (has_wpqa()) {
		if ($type == "answer") {
			$return = wpqa_report_c($data);
		}else if ($type == "user") {
			wpqa_report_user($data);
		}else {
			$return = wpqa_report_q($data);
		}
	}
	if (isset($return)) {
		return $return;
	}
}
/* Count posts */
function mobile_api_count_posts_by_user($user_id,$post_type = null,$post_status = "publish",$category = 0,$date = 0) {
	if (has_askme()) {
		$return = askme_count_posts_by_user($user_id,$post_type,$post_status,$category,$date);
	}else if (has_wpqa()) {
		$return = wpqa_count_posts_by_user($user_id,$post_type,$post_status,$category,$date);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Under review */
function mobile_api_under_review() {
	if (has_askme()) {
		$return = "ask_under_review";
	}else if (has_wpqa()) {
		$return = "wpqa_under_review";
	}
	if (isset($return)) {
		return $return;
	}
}
/* Question poll */
function mobile_api_question_poll() {
	if (has_askme()) {
		$return = "askme_question_poll";
	}else if (has_wpqa()) {
		$return = "wpqa_question_poll";
	}
	if (isset($return)) {
		return $return;
	}
}
/* Mobile poll */
function mobile_api_poll() {
	if (has_askme()) {
		$return = "askme_poll";
	}else if (has_wpqa()) {
		$return = "wpqa_poll";
	}
	if (isset($return)) {
		return $return;
	}
}
/* Forgot password */
function mobile_api_forget() {
	if (has_askme()) {
		$return = "ask-forget";
	}else if (has_wpqa()) {
		$return = "wpqa_forget";
	}
	if (isset($return)) {
		return $return;
	}
}
function mobile_api_forgot_password($data) {
	if (has_askme()) {
		$return = ask_process_lost_pass($data);
	}else if (has_wpqa()) {
		$return = wpqa_pass_jquery($data);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Resend confirmation */
function mobile_api_resend_confirmation($user_id,$edit = "") {
	if (has_askme()) {
		$return = askme_resend_confirmation($user_id,$edit);
	}else if (has_wpqa()) {
		$return = wpqa_resend_confirmation($user_id,$edit);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Add and remove favorite */
function mobile_api_favorite($action) {
	if (has_askme()) {
		if ($action == "add") {
			$return = add_favorite();
		}else {
			$return = remove_favorite();
		}
	}else if (has_wpqa()) {
		if ($action == "add") {
			$return = wpqa_add_favorite();
		}else {
			$return = wpqa_remove_favorite();
		}
	}
	if (isset($return)) {
		return $return;
	}
}
/* Group actions */
function mobile_api_actions_in_groups($action) {
	if (has_askme()) {
		if ($action == "join_group") {
			//
		}else if ($action == "leave_group") {
			//
		}else if ($action == "cancel_request_group") {
			//
		}else if ($action == "accept_invite") {
			//
		}else if ($action == "decline_invite") {
			//
		}else if ($action == "request_group") {
			//
		}else if ($action == "approve_request_all_group") {
			//
		}else if ($action == "decline_request_all_group") {
			//
		}else if ($action == "approve_request_group") {
			//
		}else if ($action == "decline_request_group") {
			//
		}else if ($action == "block_user_group") {
			//
		}else if ($action == "unblock_user_group") {
			//
		}else if ($action == "remove_user_group") {
			//
		}else if ($action == "remove_moderator_group") {
			//
		}else if ($action == "agree_posts_group") {
			//
		}else if ($action == "new_user_group") {
			//
		}else if ($action == "add_group_user") {
			//
		}
	}else if (has_wpqa()) {
		if ($action == "join_group") {
			$return = wpqa_join_group();
		}else if ($action == "leave_group") {
			$return = wpqa_leave_group();
		}else if ($action == "cancel_request_group") {
			$return = wpqa_cancel_request_group();
		}else if ($action == "accept_invite") {
			$return = wpqa_accept_invite();
		}else if ($action == "decline_invite") {
			$return = wpqa_decline_invite();
		}else if ($action == "request_group") {
			$return = wpqa_request_group();
		}else if ($action == "approve_request_all_group") {
			$return = wpqa_approve_request_all_group();
		}else if ($action == "decline_request_all_group") {
			$return = wpqa_decline_request_all_group();
		}else if ($action == "approve_request_group") {
			$return = wpqa_approve_request_group();
		}else if ($action == "decline_request_group") {
			$return = wpqa_decline_request_group();
		}else if ($action == "block_user_group") {
			$return = wpqa_block_user_group();
		}else if ($action == "unblock_user_group") {
			$return = wpqa_unblock_user_group();
		}else if ($action == "remove_user_group") {
			$return = wpqa_remove_user_group();
		}else if ($action == "remove_moderator_group") {
			$return = wpqa_remove_moderator_group();
		}else if ($action == "agree_posts_group") {
			$return = wpqa_agree_posts_group();
		}else if ($action == "new_user_group") {
			$return = mobile_app_groups_new_user_group();
		}else if ($action == "add_group_user") {
			$return = wpqa_add_group_user();
		}
	}
	if (isset($return)) {
		return $return;
	}
}
/* Add and remove likes */
function mobile_api_like_posts($action) {
	if (has_askme()) {
		if ($action == "add") {
			//
		}else {
			//
		}
	}else if (has_wpqa()) {
		if ($action == "add") {
			$return = wpqa_posts_like();
		}else {
			$return = wpqa_posts_unlike();
		}
	}
	if (isset($return)) {
		return $return;
	}
}
/* Follow or unfollow question */
function mobile_api_question_follow($action) {
	if (has_askme()) {
		if ($action == "add") {
			$return = question_follow();
		}else {
			$return = question_unfollow();
		}
	}else if (has_wpqa()) {
		if ($action == "add") {
			$return = wpqa_question_follow();
		}else {
			$return = wpqa_question_unfollow();
		}
	}
	if (isset($return)) {
		return $return;
	}
}
/* Submit a new poll */
function mobile_api_submit_question_poll($data) {
	if (has_askme()) {
		$return = askme_question_poll($data);
	}else if (has_wpqa()) {
		$return = wpqa_question_poll($data);
	}
	if (isset($return)) {
		return $return;
	}
}
/* WPQA or Ask Me */
function mobile_api_name_of_prefix() {
	if (has_askme()) {
		$return = "askme";
	}else if (has_wpqa()) {
		$return = "wpqa";
	}
	if (isset($return)) {
		return $return;
	}
}
/* Add a new vote */
function mobile_api_add_vote($post,$type,$action) {
	if (has_askme()) {
		if (($type == mobile_api_questions_type || $type == mobile_api_asked_questions_type) && $action == "up") {
			$count = question_vote_up($post);
		}else if (($type == mobile_api_questions_type || $type == mobile_api_asked_questions_type) && $action == "down") {
			$count = question_vote_down($post);
		}else if ($type == "answer" && $action == "up") {
			$count = comment_vote_up($post);
		}else if ($type == "answer" && $action == "down") {
			$count = comment_vote_down($post);
		}
	}else if (has_wpqa()) {
		if (($type == mobile_api_questions_type || $type == mobile_api_asked_questions_type) && $action == "up") {
			$count = wpqa_question_vote_up($post);
		}else if (($type == mobile_api_questions_type || $type == mobile_api_asked_questions_type) && $action == "down") {
			$count = wpqa_question_vote_down($post);
		}else if ($type == "answer" && $action == "up") {
			$count = wpqa_comment_vote_up($post);
		}else if ($type == "answer" && $action == "down") {
			$count = wpqa_comment_vote_down($post);
		}
	}
	if (isset($count)) {
		return $count;
	}
}
/* Following users */
function mobile_api_following($action,$post = array()) {
	if ($action == "follow") {
		if (has_askme()) {
			$return = following_me($post);
		}else if (has_wpqa()) {
			$return = wpqa_following_you($post);
		}
	}else {
		if (has_askme()) {
			$return = following_not($post);
		}else if (has_wpqa()) {
			$return = wpqa_following_not($post);
		}
	}
	if (isset($return)) {
		return $return;
	}
}
/* Blocking users */
function mobile_api_blocking() {
	if (has_askme()) {
		$return = askme_block_user();
	}else if (has_wpqa()) {
		$return = wpqa_block_user();
	}
	if (isset($return)) {
		return $return;
	}
}
/* Notifications and activities */
function mobile_api_notifications_activities($user_id = "",$another_user_id = "",$username = "",$post_id = "",$comment_id = "",$text = "",$type = "notifications",$more_text = "",$type_of_item = "") {
	if (has_askme()) {
		$return = askme_notifications_activities($user_id,$another_user_id,$username,$post_id,$comment_id,$text,$type,$more_text,$type_of_item);
	}else if (has_wpqa()) {
		$return = wpqa_notifications_activities($user_id,$another_user_id,$username,$post_id,$comment_id,$text,$type,$more_text,$type_of_item);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Get notification and activity result */
function mobile_api_notification_activity_result($post,$type = "notification",$admin = "") {
	if (has_askme()) {
		$return = askme_notification_activity_result($post,$type,$admin);
	}else if (has_wpqa()) {
		$return = wpqa_notification_activity_result($post,$type,$admin);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Show notifications */
function mobile_api_show_notifications($notification_title) {
	if (has_askme()) {
		$return = askme_show_notifications($notification_title);
	}else if (has_wpqa()) {
		$return = wpqa_show_notifications($notification_title,"","");
	}
	if (isset($return)) {
		return $return;
	}
}
/* Count notifications */
function mobile_api_count_notifications($user_id) {
	if (has_askme()) {
		$return = (int)get_user_meta($user_id,$user_id."_notifications",true);
	}else if (has_wpqa()) {
		$return = (int)mobile_api_count_posts_by_user($user_id,"notification","publish");
	}
	if (isset($return)) {
		return $return;
	}
}
/* Count new notifications */
function mobile_api_count_new_notifications($user_id) {
	if (has_askme()) {
		$return = askme_count_new_notifications($user_id);
	}else if (has_wpqa()) {
		$return = wpqa_count_new_notifications($user_id,"publish");
	}
	if (isset($return)) {
		return $return;
	}
}
/* Dismiss all notifications */
function mobile_api_dismiss_all_notifications($user_id) {
	if (has_askme()) {
		update_user_meta($user_id,$user_id.'_new_notifications',0);
	}else if (has_wpqa()) {
		$args = array(
			"post_type"      => "notification",
			"author"         => $user_id,
			"post_status"    => "publish",
			'posts_per_page' => -1,
			"meta_query"     => array(array("key" => "notification_new","compare" => "=","value" => 1))
		);
		$notifications_query = new WP_Query($args);
		if ($notifications_query->have_posts()) {
			while ( $notifications_query->have_posts() ) { $notifications_query->the_post();
				$notification_post = $notifications_query->post;
				update_post_meta($notification_post->ID,"notification_new",0);
			}
		}
	}
}
/* Read the notification */
function mobile_api_read_notifications($user_id,$id = "") {
	if (has_askme()) {
		$notification_one = get_user_meta($user_id,$user_id."_notifications_".$id,true);
		if (isset($notification_one["new"]) && $notification_one["new"] == 1) {
			$num = get_user_meta($user_id,$user_id.'_new_notifications',true);
			$num--;
			update_user_meta($user_id,$user_id.'_new_notifications',($num > 0?$num:0));
			unset($notification_one["new"]);
			update_user_meta($user_id,$user_id."_notifications_".$id,$notification_one);
		}
	}else if (has_wpqa()) {
		update_post_meta($id,"notification_new",0);
	}
}
function mobile_api_get_notifications($paged = "",$all = "") {
	if ($paged == "") {
		$paged = mobile_api_paged();
	}
	$number     = get_option('posts_per_page');
	$user_id    = get_current_user_id();
	$icon       = "fa-bell";
	$background = "#4be1ab";
	$color      = "#FFFFFF";
	if (has_askme()) {
		$_notifications = get_user_meta($user_id,$user_id."_notifications",true);
		for ($notifications_number = 1; $notifications_number <= $_notifications; $notifications_number++) {
			$notification_one[] = get_user_meta($user_id,$user_id."_notifications_".$notifications_number);
		}
		if (isset($notification_one) and is_array($notification_one)) {
			$notification = array_reverse($notification_one);
			$current = max(1,$paged);
			$total_posts = count($notification);
			$total = ($number > 0?ceil($total_posts/$number):0);
			$start = ($current - 1) * $number;
			$end = $start + $number;
			$end = ($total_posts < $end) ? $total_posts : $end;
			for ($i=$start;$i < $end ;++$i ) {
				$show_notifications = false;
				$type_id_array = array();
				$notification_result = $notification[$i][0];
				if ($all == "") {
					if (isset($notification_result["new"]) && $notification_result["new"] == 1) {
						$show_notifications = true;
					}
				}else {
					$show_notifications = true;
				}
				if ($show_notifications == true) {
					$notifications_json = false;
					$notification_type = "general";
					$notification_title = $notification_result["text"];
					if ($notification_title == "add_message_user") {
						$notification_type = "messages";
						if ($notification_title == "seen_message" || $notification_title == "approved_message") {
							$type_id_array = array("type_id" => "sent");
						}else {
							$type_id_array = array("type_id" => "inbox");
						}
					}else if ($notification_title == "approved_post" || $notification_title == "add_post" || $notification_title == "poll_question" || $notification_title == "question_vote_up" || $notification_title == "question_vote_down" || $notification_title == "add_question" || $notification_title == "add_question_user" || $notification_title == "question_favorites" || $notification_title == "question_remove_favorites" || $notification_title == "follow_question" || $notification_title == "unfollow_question" || $notification_title == "approved_question") {
						$post_id = (int)(!empty($notification_result["post_id"])?$notification_result["post_id"]:"");
						if ($post_id > 0) {
							$get_the_permalink = get_the_permalink($post_id);
							$get_post_status = get_post_status($post_id);
							if ($post_id > 0 && isset($get_the_permalink) && $get_the_permalink != "" && isset($get_post_status) && $get_post_status != "trash") {
								$type_id_array = array("type_id" => $post_id);
							}
						}
						$notification_type = ($notification_title == "approved_post" || $notification_title == "add_post"?"post":"question");
						$notifications_json = ($post_id > 0 && isset($get_the_permalink) && $get_the_permalink != "" && isset($get_post_status) && $get_post_status != "trash"?mobile_api_get_post."/?id=".$post_id."&post_type=".$notification_type:false);
					}else if ($notification_title == "answer_vote_up" || $notification_title == "answer_vote_down" || $notification_title == "point_back" || $notification_title == "select_best_answer" || $notification_title == "point_removed" || $notification_title == "cancel_best_answer" || $notification_title == "answer_asked_question" || $notification_title == "answer_question" || $notification_title == "answer_question_follow" || $notification_title == "reply_answer" || $notification_title == "approved_answer") {
						$post_id = (!empty($notification_result["post_id"])?$notification_result["post_id"]:"");
						$comment_id = (!empty($notification_result["comment_id"])?$notification_result["comment_id"]:"");
						if ($comment_id != "") {
							$get_comment = get_comment($notification_result["comment_id"]);
						}
						if ($post_id != "" && !empty($get_comment) && isset($get_comment->comment_approved) && $get_comment->comment_approved != "spam" && $get_comment->comment_approved != "trash") {
							$notification_type = "answer";
							$notification_type = "question";
							$type_id_array = array("type_id" => $post_id);
						}
						$notifications_json = ($post_id != "" && !empty($get_comment) && isset($get_comment->comment_approved) && $get_comment->comment_approved != "spam" && $get_comment->comment_approved != "trash"?mobile_api_get_post."/?id=".$post_id."&post_type=".mobile_api_questions_type:false);
					}else if ($notification_title == "user_follow" || $notification_title == "user_unfollow") {
						$another_user_id = (int)(!empty($notification_result["another_user_id"])?$notification_result["another_user_id"]:"");
						if ($another_user_id > 0) {
							$profile_url = get_author_posts_url($another_user_id);
							if ($another_user_id > 0 && isset($profile_url) && $profile_url != "") {
								$type_id_array = array("type_id" => $another_user_id);
							}
						}
						$notification_type = "profile";
						$notifications_json = ($another_user_id > 0 && isset($profile_url) && $profile_url != ""?mobile_api_get_userinfo."?user_id=".$another_user_id:false);
					}else if ($notification_title == "approved_comment") {
						$notification_type = "comment";
					}else if ($notification_title == "approved_message" || $notification_title == "add_message_user" || $notification_title == "seen_message") {
						$notification_type = "message";
					}

					$icon = mobile_api_notification_icons($notification_title,(isset($post_id)?$post_id:0),(isset($comment_id)?$comment_id:0),(isset($another_user_id)?$another_user_id:0),$notification_result);
					$background = mobile_api_notification_background($notification_title);
					$notifications[] = array_merge($type_id_array,array(
						"id"             => ($total_posts-$i),
						"title"          => $notification_title,
						"icon"           => $icon,
						"background"     => $background,
						"color"          => $color,
						"darkBackground" => $background,
						"darkColor"      => $color,
						"type"           => $notification_type,
						"text"           => strip_tags(mobile_api_show_notifications($notification_result)),
						"date"           => human_time_diff($notification_result["time"],current_time('timestamp'))." ".esc_html__("ago","mobile-api"),
						"dismiss"        => (isset($notification_result["new"]) && $notification_result["new"] == 1?true:false)
					),array("json" => $notifications_json));
				}
			}
		}else {
			return "no_notifications";
		}
		return array(
			'pages' => $total,
			'count' => $total_posts,
			'notifications' => (isset($notifications)?$notifications:array())
		);
	}else if (has_wpqa()) {
		$args = array('author' => $user_id,'post_type' => 'notification','posts_per_page' => $number,'paged' => $paged);
		if ($all == "") {
			$args = array_merge($args,array('meta_query' => array(array('type' => 'numeric','key' => 'notification_new','value' => 1,'compare' => '='))));
		}
		$notifications_query = new WP_Query($args);
		$k = 0;
		if ($notifications_query->have_posts()) {
			while ( $notifications_query->have_posts() ) { $notifications_query->the_post();
				$type_id_array = array();
				$k++;
				$notification_post = $notifications_query->post;
				$notification_result = mobile_api_notification_activity_result($notification_post,"notification");
				$notifications_json = false;
				$notification_type = "general";
				$notification_title = $notification_post->post_title;
				if ($notification_title == "add_message_user") {
					$notification_type = "messages";
					if ($notification_title == "seen_message" || $notification_title == "approved_message") {
						$type_id_array = array("type_id" => "sent");
					}else {
						$type_id_array = array("type_id" => "inbox");
					}
				}else if ($notification_title == "approved_post" || $notification_title == "add_post" || $notification_title == "poll_question" || $notification_title == "question_vote_up" || $notification_title == "question_vote_down" || $notification_title == "add_question" || $notification_title == "add_question_user" || $notification_title == "question_favorites" || $notification_title == "question_remove_favorites" || $notification_title == "follow_question" || $notification_title == "unfollow_question" || $notification_title == "approved_question" || $notification_title == "request_group" || $notification_title == "approved_group" || $notification_title == "add_group_moderator" || $notification_title == "approve_request_group" || $notification_title == "add_group_invitations" || $notification_title == "unblocked_group" || $notification_title == "remove_group_moderator" || $notification_title == "decline_request_group" || $notification_title == "removed_user_group" || $notification_title == "blocked_group" || $notification_title == "approved_posts" || $notification_title == "posts_like" || $notification_title == "question_reaction_like" || $notification_title == "question_remove_reaction" || $notification_title == "question_reaction_love" || $notification_title == "question_reaction_hug" || $notification_title == "question_reaction_haha" || $notification_title == "question_reaction_wow" || $notification_title == "question_reaction_sad" || $notification_title == "question_reaction_angry" || $notification_title == "posts_reaction_like" || $notification_title == "posts_remove_reaction" || $notification_title == "posts_reaction_love" || $notification_title == "posts_reaction_hug" || $notification_title == "posts_reaction_haha" || $notification_title == "posts_reaction_wow" || $notification_title == "posts_reaction_sad" || $notification_title == "posts_reaction_angry" || $notification_title == "comment_reaction_like" || $notification_title == "comment_remove_reaction" || $notification_title == "comment_reaction_love" || $notification_title == "comment_reaction_hug" || $notification_title == "comment_reaction_haha" || $notification_title == "comment_reaction_wow" || $notification_title == "comment_reaction_sad" || $notification_title == "comment_reaction_angry") {
					$post_id = (int)get_post_meta($notification_post->ID,"notification_post_id",true);
					if ($post_id > 0) {
						$get_the_permalink = get_the_permalink($post_id);
						$get_post_status = get_post_status($post_id);
						if ($post_id > 0 && isset($get_the_permalink) && $get_the_permalink != "" && isset($get_post_status) && $get_post_status != "trash") {
							$type_id_array = array("type_id" => $post_id);
						}
					}
					if ($post_id > 0 && isset($get_the_permalink) && $get_the_permalink != "" && isset($get_post_status) && $get_post_status != "trash") {
						$notification_type = ($notification_title == "approved_post" || $notification_title == "add_post"?"post":"question");
					}
					if (mobile_api_groups()) {
						if ($notification_title == "request_group" || $notification_title == "approved_group" || $notification_title == "add_group_moderator" || $notification_title == "approve_request_group" || $notification_title == "add_group_invitations" || $notification_title == "unblocked_group" || $notification_title == "remove_group_moderator" || $notification_title == "decline_request_group" || $notification_title == "removed_user_group" || $notification_title == "blocked_group") {
							$notification_type = "group";
						}else if ($notification_title == "approved_posts" || $notification_title == "posts_like" || $notification_title == "posts_reaction_like" || $notification_title == "posts_remove_reaction" || $notification_title == "posts_reaction_love" || $notification_title == "posts_reaction_hug" || $notification_title == "posts_reaction_haha" || $notification_title == "posts_reaction_wow" || $notification_title == "posts_reaction_sad" || $notification_title == "posts_reaction_angry" || $notification_title == "comment_reaction_like" || $notification_title == "comment_remove_reaction" || $notification_title == "comment_reaction_love" || $notification_title == "comment_reaction_hug" || $notification_title == "comment_reaction_haha" || $notification_title == "comment_reaction_wow" || $notification_title == "comment_reaction_sad" || $notification_title == "comment_reaction_angry") {
							$notification_type = "groupPost";
						}
					}
					$notifications_json = ($post_id > 0 && isset($get_the_permalink) && $get_the_permalink != "" && isset($get_post_status) && $get_post_status != "trash"?mobile_api_get_post."/?id=".$post_id."&post_type=".$notification_type:false);
				}else if ($notification_title == "answer_vote_up" || $notification_title == "answer_vote_down" || $notification_title == "point_back" || $notification_title == "select_best_answer" || $notification_title == "point_removed" || $notification_title == "cancel_best_answer" || $notification_title == "answer_asked_question" || $notification_title == "answer_question" || $notification_title == "answer_question_follow" || $notification_title == "reply_answer" || $notification_title == "approved_answer" || $notification_title == "answer_reaction_like" || $notification_title == "answer_remove_reaction" || $notification_title == "answer_reaction_love" || $notification_title == "answer_reaction_hug" || $notification_title == "answer_reaction_haha" || $notification_title == "answer_reaction_wow" || $notification_title == "answer_reaction_sad" || $notification_title == "answer_reaction_angry") {
					$post_id = (int)get_post_meta($notification_post->ID,"notification_post_id",true);
					$comment_id = (int)get_post_meta($notification_post->ID,"notification_comment_id",true);
					if ($comment_id != "") {
						$get_comment = get_comment($comment_id);
					}
					if ($post_id != "" && !empty($get_comment) && isset($get_comment->comment_approved) && $get_comment->comment_approved != "spam" && $get_comment->comment_approved != "trash") {
						$notification_type = "answer";
						$notification_type = "question";
						$type_id_array = array("type_id" => $post_id);
					}
					$notifications_json = ($post_id != "" && !empty($get_comment) && isset($get_comment->comment_approved) && $get_comment->comment_approved != "spam" && $get_comment->comment_approved != "trash"?mobile_api_get_post."/?id=".$post_id."&post_type=".mobile_api_questions_type:false);
				}else if ($notification_title == "user_follow" || $notification_title == "user_unfollow") {
					$another_user_id = (int)get_post_meta($notification_post->ID,"notification_another_user_id",true);
					if ($another_user_id > 0) {
						$profile_url = get_author_posts_url($another_user_id);
						if ($another_user_id > 0 && isset($profile_url) && $profile_url != "") {
							$type_id_array = array("type_id" => $another_user_id);
						}
					}
					if ($another_user_id > 0 && isset($profile_url) && $profile_url != "") {
						$notification_type = "profile";
					}
					$notifications_json = ($another_user_id > 0 && isset($profile_url) && $profile_url != ""?mobile_api_get_userinfo."?user_id=".$another_user_id:false);
				}else if ($notification_title == "approved_comment") {
					$notification_type = "comment";
				}else if ($notification_title == "approved_message" || $notification_title == "add_message_user" || $notification_title == "seen_message") {
					$notification_type = "message";
				}

				$icon = mobile_api_notification_icons($notification_title,(isset($post_id)?$post_id:0),(isset($comment_id)?$comment_id:0),(isset($another_user_id)?$another_user_id:0),$notification_result);
				$background = mobile_api_notification_background($notification_title);
				$notifications[] = array_merge($type_id_array,array(
					"id"             => $notification_post->ID,
					"title"          => $notification_title,
					"icon"           => $icon,
					"background"     => $background,
					"color"          => $color,
					"darkBackground" => $background,
					"darkColor"      => $color,
					"type"           => $notification_type,
					"text"           => strip_tags(mobile_api_show_notifications($notification_result)),
					"date"           => $notification_result["time"],
					"dismiss"        => (isset($notification_result["new"]) && $notification_result["new"] == 1?true:false),
				),array("json" => $notifications_json));
			}
		}else {
			return "no_notifications";
		}
		wp_reset_postdata();
		$count_total = (int)$notifications_query->found_posts;
		return array(
			'count' => $number,
			'count_total' => $count_total,
			'pages' => ($number > 0?ceil($count_total/$number):0),
			'notifications' => (isset($notifications)?$notifications:false)
		);
	}
}
function mobile_api_notification_background($notification_title,$background = "#4be1ab") {
	if ($notification_title == "gift_site" || $notification_title == "points_referral" || $notification_title == "referral_membership" || $notification_title == "admin_add_points" || $notification_title == "seen_message" || $notification_title == "select_best_answer" || $notification_title == "accept_invite" || $notification_title == "add_message_user" || $notification_title == "add_message" || $notification_title == "question_reaction_haha" || $notification_title == "answer_reaction_haha" || $notification_title == "posts_reaction_haha" || $notification_title == "comment_reaction_haha" || $notification_title == "question_reaction_hug" || $notification_title == "answer_reaction_hug" || $notification_title == "posts_reaction_hug" || $notification_title == "comment_reaction_hug" || $notification_title == "question_reaction_sad" || $notification_title == "answer_reaction_sad" || $notification_title == "posts_reaction_sad" || $notification_title == "comment_reaction_sad") {
		$background = "#f3d351";
	}else if ($notification_title == "answer_question" || $notification_title == "reply_answer" || $notification_title == "answer_question_follow" || $notification_title == "accepted_withdrawal_points" || $notification_title == "requested_money" || $notification_title == "request_group" || $notification_title == "add_group_invitations" || $notification_title == "approved_category" || $notification_title == "accepted_category" || $notification_title == "question_reaction_like" || $notification_title == "answer_reaction_like" || $notification_title == "posts_reaction_like" || $notification_title == "comment_reaction_like") {
		$background = "#2e6ffd";
	}else if ($notification_title == "canceled_category" || $notification_title == "question_vote_down" || $notification_title == "answer_vote_down" || $notification_title == "admin_remove_points" || $notification_title == "question_remove_favorites" || $notification_title == "unfollow_question" || $notification_title == "user_unfollow" || $notification_title == "cancel_best_answer" || $notification_title == "rejected_withdrawal_points" || $notification_title == "remove_group_moderator" || $notification_title == "decline_request_group" || $notification_title == "removed_user_group" || $notification_title == "blocked_group" || $notification_title == "delete_reason" || $notification_title == "delete_question" || $notification_title == "delete_post" || $notification_title == "delete_answer" || $notification_title == "delete_comment" || $notification_title == "report_answer" || $notification_title == "report_user" || $notification_title == "delete_inbox_message" || $notification_title == "delete_send_message" || $notification_title == "delete_group" || $notification_title == "delete_posts" || $notification_title == "posts_unlike" || $notification_title == "decline_invite" || $notification_title == "question_remove_reaction" || $notification_title == "answer_remove_reaction" || $notification_title == "posts_remove_reaction" || $notification_title == "comment_remove_reaction" || $notification_title == "question_reaction_love" || $notification_title == "answer_reaction_love" || $notification_title == "posts_reaction_love" || $notification_title == "comment_reaction_love" || $notification_title == "question_reaction_wow" || $notification_title == "answer_reaction_wow" || $notification_title == "posts_reaction_wow" || $notification_title == "comment_reaction_wow" || $notification_title == "question_reaction_angry" || $notification_title == "answer_reaction_angry" || $notification_title == "posts_reaction_angry" || $notification_title == "comment_reaction_angry" || $notification_title == "posts_like") {
		$background = "#fe5339";
	}
	return $background;
}
function mobile_api_notification_icons($notification_title,$post_id = 0,$comment_id = 0,$another_user_id = 0,$notification_result = array(),$icon = "fa-bell") {
	if ($notification_title == "accepted_category" || $notification_title == "canceled_category") {
		$icon = "0xec21";
	}else if ($notification_title == "question_vote_up" || $notification_title == "answer_vote_up") {
		$icon = "0xe9c6";
	}else if ($notification_title == "question_vote_down" || $notification_title == "answer_vote_down") {
		$icon = "0xe9c7";
	}else if ($notification_title == "gift_site" || $notification_title == "points_referral" || $notification_title == "referral_membership" || $notification_title == "admin_add_points") {
		$icon = "0xe9ba";
	}else if ($notification_title == "admin_remove_points" || $notification_title == "question_remove_favorites") {
		$icon = "0xe9cc";
	}else if ($notification_title == "add_message_user" || $notification_title == "seen_message") {
		$icon = "0xed57";
	}else if ($notification_title == "question_favorites") {
		$icon = "0xe9cb";
	}else if ($notification_title == "follow_question" || $notification_title == "user_follow") {
		$icon = "0xe939";
	}else if ($notification_title == "unfollow_question" || $notification_title == "user_unfollow") {
		$icon = "0xed8f";
	}else if ($notification_title == "answer_asked_question" || $notification_title == "select_best_answer" || $notification_title == "cancel_best_answer" || $notification_title == "answer_question" || $notification_title == "reply_answer" || $notification_title == "answer_question_follow" || $notification_title == "approved_answer" || $notification_title == "approved_comment") {
		$icon = "0xe933";
	}else if ($notification_title == "rejected_withdrawal_points" || $notification_title == "accepted_withdrawal_points" || $notification_title == "requested_money") {
		$icon = "0xe989";
	}else if ($notification_title == "request_group" || $notification_title == "approved_group" || $notification_title == "approved_posts") {
		$icon = "0xf079";
	}else if ($notification_title == "add_group_moderator" || $notification_title == "approve_request_group" || $notification_title == "add_group_invitations" || $notification_title == "unblocked_group") {
		$icon = "0xe939";
	}else if ($notification_title == "remove_group_moderator" || $notification_title == "decline_request_group" || $notification_title == "removed_user_group" || $notification_title == "blocked_group") {
		$icon = "0xed8f";//0xed91
	}else if ($notification_title == "posts_like") {
		$icon = "0xeca1";
	}else if ($notification_title == "question_reaction_like" || $notification_title == "answer_reaction_like" || $notification_title == "posts_reaction_like" || $notification_title == "comment_reaction_like" || $notification_title == "question_remove_reaction" || $notification_title == "answer_remove_reaction" || $notification_title == "posts_remove_reaction" || $notification_title == "comment_remove_reaction") {
		$icon = "0xf00c";
	}else if ($notification_title == "question_reaction_love" || $notification_title == "answer_reaction_love" || $notification_title == "posts_reaction_love" || $notification_title == "comment_reaction_love") {
		$icon = "fa-heart";
	}else if ($notification_title == "question_reaction_hug" || $notification_title == "answer_reaction_hug" || $notification_title == "posts_reaction_hug" || $notification_title == "comment_reaction_hug") {
		$icon = "fa-kiss-wink-heart";
	}else if ($notification_title == "question_reaction_haha" || $notification_title == "answer_reaction_haha" || $notification_title == "posts_reaction_haha" || $notification_title == "comment_reaction_haha") {
		$icon = "fa-laugh-squint";
	}else if ($notification_title == "question_reaction_wow" || $notification_title == "answer_reaction_wow" || $notification_title == "posts_reaction_wow" || $notification_title == "comment_reaction_wow") {
		$icon = "fa-surprise";
	}else if ($notification_title == "question_reaction_sad" || $notification_title == "answer_reaction_sad" || $notification_title == "posts_reaction_sad" || $notification_title == "comment_reaction_sad") {
		$icon = "fa-frown-open";
	}else if ($notification_title == "question_reaction_angry" || $notification_title == "answer_reaction_angry" || $notification_title == "posts_reaction_angry" || $notification_title == "comment_reaction_angry") {
		$icon = "fa-tired";
	}else if ((!empty($post_id) && $post_id > 0) || $notification_title == "cronjob_question" || $notification_title == "cronjob_post" || $notification_title == "cronjob_questions" || $notification_title == "cronjob_posts") {
		$icon = "0xe932";
	}else if (!empty($comment_id) && $comment_id > 0) {
		$icon = "0xe933";
	}else if (((!empty($another_user_id) && $another_user_id > 0) || !empty($notification_result["username"])) && $notification_title != "admin_add_points" && $notification_title != "admin_remove_points") {
		$icon = "0xf078";//0xe922
	}else if ($notification_title == "action_comment" || $notification_title == "action_post" || $notification_title == "delete_reason" || $notification_title == "delete_question" || $notification_title == "delete_post" || $notification_title == "delete_answer" || $notification_title == "delete_comment") {
		$icon = "0xea47";
	}else {
		$icon = "0xe80a";
	}
	return $icon;
}
/* Get meta name for visits */
function mobile_api_post_stats() {
	if (has_askme()) {
		$return = askme_get_meta_stats();
	}else if (has_wpqa()) {
		$return = (wpqa_plugin_version >= "5.4"?wpqa_get_meta_stats():"post_stats");
	}
	if (isset($return)) {
		return $return;
	}
}
/* Update post stats */
function mobile_api_update_post_stats($post_id = 0,$user_id = 0) {
	if (has_askme()) {
		$return = askme_update_post_stats($post_id,$user_id);
	}else if (has_wpqa()) {
		$return = wpqa_update_post_stats($post_id,$user_id);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Get post stats */
function mobile_api_get_post_stats($post_id = 0,$user_id = 0) {
	if (has_askme()) {
		$return = askme_get_post_stats($post_id,$user_id);
	}else if (has_wpqa()) {
		$return = wpqa_get_post_stats($post_id,$user_id);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Process new questions */
function mobile_api_process_new_questions($data,$user = "") {
	if (has_askme()) {
		if (isset($data["form_type"])) {
			$data["post_type"] = $data["form_type"];
		}
		if (isset($data["question_poll"]) && $data["question_poll"] == "on") {
			$data["question_poll"] = 1;
		}
		if (isset($data["remember_answer"]) && $data["remember_answer"] == "on") {
			$data["remember_answer"] = 1;
		}
		if (isset($data["private_question"]) && $data["private_question"] == "on") {
			$data["private_question"] = 1;
		}
		if (isset($data["anonymously_question"]) && $data["anonymously_question"] == "on") {
			$data["anonymously_question"] = 1;
		}
		if (isset($data["video_description"]) && $data["video_description"] == "on") {
			$data["video_description"] = 1;
		}
		if (isset($data["terms_active"]) && $data["terms_active"] == "on") {
			$data["agree_terms"] = 1;
		}
		$return = process_new_posts($data);
	}else if (has_wpqa()) {
		$return = wpqa_process_new_questions($data,$user);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Process edit question */
function mobile_api_process_edit_questions($data,$user = "") {
	if (has_askme()) {
		if (isset($data["form_type"])) {
			$data["post_type"] = $data["form_type"];
		}
		if (isset($data["question_poll"]) && $data["question_poll"] == "on") {
			$data["question_poll"] = 1;
		}
		if (isset($data["remember_answer"]) && $data["remember_answer"] == "on") {
			$data["remember_answer"] = 1;
		}
		if (isset($data["private_question"]) && $data["private_question"] == "on") {
			$data["private_question"] = 1;
		}
		if (isset($data["video_description"]) && $data["video_description"] == "on") {
			$data["video_description"] = 1;
		}
		$return = process_edit_questions($data,$user);
	}else if (has_wpqa()) {
		$return = wpqa_process_edit_questions($data,$user);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Process edit comment */
function mobile_api_process_edit_comments($data) {
	if (has_askme()) {
		$return = process_edit_comments($data);
	}else if (has_wpqa()) {
		$return = wpqa_process_edit_comments($data);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Process register */
function mobile_api_signup_process($data) {
	if (has_askme()) {
		$return = askme_signup_process($data);
	}else if (has_wpqa()) {
		$return = wpqa_signup_jquery($data);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Process edit profile */
function mobile_api_process_edit_profile($data,$user_id) {
	if (has_askme()) {
		$return = askme_process_edit_profile_form($data,$user_id);
	}else if (has_wpqa()) {
		$return = wpqa_process_edit_profile_form($data,$user_id);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Process new posts */
function mobile_api_process_new_posts($data) {
	if (has_askme()) {
		if (isset($data["form_type"])) {
			$data["post_type"] = $data["form_type"];
		}
		if (isset($data["agree_terms"]) && $data["agree_terms"] == "on") {
			$data["agree_terms"] = 1;
		}
		$return = process_new_posts($data);
	}else if (has_wpqa()) {
		$return = wpqa_process_new_posts($data,$user);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Process edit posts */
function mobile_api_process_edit_posts($data) {
	if (has_askme()) {
		if (isset($data["form_type"])) {
			$data["post_type"] = $data["form_type"];
		}
		$return = process_vpanel_edit_posts($data);
	}else if (has_wpqa()) {
		$return = wpqa_process_edit_posts($data);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Process new groups */
function mobile_api_process_new_groups($data) {
	if (has_askme()) {
		//
	}else if (has_wpqa()) {
		$return = wpqa_process_new_groups($data);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Process new group posts */
function mobile_api_process_new_group_posts($data) {
	if (has_askme()) {
		//
	}else if (has_wpqa()) {
		$return = wpqa_process_new_group_posts($data);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Process send message */
function mobile_api_process_new_messages($data) {
	if (has_askme()) {
		if (isset($data["form_type"])) {
			$data["post_type"] = $data["form_type"];
		}
		$return = process_new_messages($data);
	}else if (has_wpqa()) {
		$return = wpqa_process_new_messages($data);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Notification send message */
function mobile_api_send_message_publish($get_post,$user_id,$get_message_user) {
	if (has_askme()) {
		$return = askme_notification_send_message($get_post,$user_id,$get_message_user);
	}else if (has_wpqa()) {
		$return = wpqa_notification_send_message($get_post,$user_id,$get_message_user);
	}
	if (isset($return)) {
		return $return;
	}
}
/* View message */
function mobile_api_message_view() {
	if (has_askme()) {
		$return = ask_message_view();
	}else if (has_wpqa()) {
		$return = wpqa_message_view();
	}
	if (isset($return)) {
		return $return;
	}
}
/* Notifications ask question */
function mobile_api_notifications_ask_question($post_id,$question_username,$user_id,$not_user,$anonymously_user,$get_current_user_id,$approved = false) {
	if (has_askme()) {
		$return = askme_notifications_ask_question($post_id,$question_username,$user_id,$not_user,$anonymously_user,$get_current_user_id,$approved);
	}else if (has_wpqa()) {
		$return = wpqa_notifications_ask_question($post_id,$question_username,$user_id,$not_user,$anonymously_user,$get_current_user_id,$approved);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Notifications add post */
function mobile_api_notifications_add_post($post_id,$post_username,$not_user,$get_current_user_id,$approved = false) {
	if (has_askme()) {
		$return = askme_notifications_add_post($post_id,$post_username,$not_user,$get_current_user_id,$approved);
	}else if (has_wpqa()) {
		$return = wpqa_notifications_add_post($post_id,$post_username,$not_user,$get_current_user_id,$approved);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Default group */
function mobile_api_default_group() {
	if (has_askme()) {
		askme_default_group();
	}else if (has_wpqa()) {
		wpqa_default_group();
	}
}
/* Count the comments */
function mobile_api_count_comments($post_id,$return = "count_post_all",$count_meta = "like_comments_only") {
	if (has_askme()) {
		$return = askme_count_comments($post_id,$return,$count_meta);
	}else if (has_wpqa()) {
		$return = wpqa_count_comments($post_id,$return,$count_meta);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Resize the images */
function mobile_api_resize_by_url($url,$img_width_f,$img_height_f,$gif = false) {
	if (has_askme()) {
		$return = askme_resize_by_url($url,$img_width_f,$img_height_f,$gif);
	}else if (has_wpqa()) {
		$return = wpqa_get_aq_resize_url($url,$img_width_f,$img_height_f,$gif);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Resize the images with id */
function mobile_api_get_aq_resize_img_url($img_width_f,$img_height_f,$img_lightbox = "",$thumbs = "",$gif = false,$title = "") {
	if (has_askme()) {
		$return = askme_resize_url($img_width_f,$img_height_f,$thumbs,$gif);
	}else if (has_wpqa()) {
		$return = wpqa_get_aq_resize_img_url($img_width_f,$img_height_f,$img_lightbox,$thumbs,$gif,$title);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Private post */
function mobile_api_private_post($post_id,$first_user,$second_user,$post_type = mobile_api_questions_type) {
	if (has_askme()) {
		$return = ask_private($post_id,$first_user,$second_user);
	}else if (has_wpqa()) {
		$return = wpqa_private($post_id,$first_user,$second_user,$post_type);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Private answer */
function mobile_api_private_answer($comment_id,$first_user,$second_user,$post_author = 0) {
	if (has_askme()) {
		$return = ask_private_answer($comment_id,$first_user,$second_user,$post_author);
	}else if (has_wpqa()) {
		$return = wpqa_private_answer($comment_id,$first_user,$second_user,$post_author);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Add best answer */
function mobile_api_add_best_answer() {
	if (has_askme()) {
		$return = best_answer();
	}else if (has_wpqa()) {
		$return = wpqa_best_answer_a();
	}
	if (isset($return)) {
		return $return;
	}
}
/* Remove best answer */
function mobile_api_remove_best_answer() {
	if (has_askme()) {
		$return = best_answer_re();
	}else if (has_wpqa()) {
		$return = wpqa_best_answer_re();
	}
	if (isset($return)) {
		return $return;
	}
}
/* Message reply */
function mobile_api_message_reply() {
	if (has_askme()) {
		$return = ask_message_reply();
	}else if (has_wpqa()) {
		$return = wpqa_message_reply();
	}
	if (isset($return)) {
		return $return;
	}
}
/* Message actions */
function mobile_api_actions_in_messages($action) {
	if (has_askme()) {
		if ($action == "block_user") {
			$return = ask_block_message();
		}else if ($action == "unblock_user") {
			$return = ask_unblock_message();
		}
	}else if (has_wpqa()) {
		if ($action == "block_user") {
			$return = wpqa_block_message();
		}else if ($action == "unblock_user") {
			$return = wpqa_unblock_message();
		}
	}
	if (isset($return)) {
		return $return;
	}
}
/* Delete posts */
function mobile_api_delete_posts($data,$post_id) {
	if (has_askme()) {
		$get_post = get_post($post_id);
		askme_delete_posts($data,$post);
	}else if (has_wpqa()) {
		wpqa_delete_post($data,$post);
	}
}
/* Delete messages */
function mobile_api_delete_messages($post_id,$post_author,$user_id,$message_user_id) {
	if (has_askme()) {
		askme_delete_messages($post_id,$post_author,$user_id,$message_user_id);
	}else if (has_wpqa()) {
		$message_user_array = get_post_meta($post_id,'message_user_array',true);
		wpqa_delete_messages($post_id,$post_author,$user_id,$message_user_id,$message_user_array);
	}
}
/* Delete comments */
function mobile_api_delete_comments($comment_id) {
	if (has_askme()) {
		askme_delete_comment($comment_id);
	}else if (has_wpqa()) {
		wpqa_delete_comment();
	}
}
/* Close question */
function mobile_api_question_close($question_id) {
	if (has_askme()) {
		question_close($question_id);
	}else if (has_wpqa()) {
		wpqa_question_close($question_id);
	}
}
/* Open question */
function mobile_api_question_open($question_id) {
	if (has_askme()) {
		question_open($question_id);
	}else if (has_wpqa()) {
		wpqa_question_open($question_id);
	}
}
/* Bump questions */
function mobile_api_bump_questions() {
	if (has_askme()) {
		$return = askme_add_point();
	}else if (has_wpqa()) {
		$return = wpqa_add_point();
	}
	if (isset($return)) {
		return $return;
	}
}
/* Update notifications */
function mobile_api_update_notifications_themes() {
	if (has_askme()) {
		update_notifications();
	}else if (has_wpqa()) {
		wpqa_update_notifications();
	}
}
/* Get countries */
function mobile_api_get_countries() {
	if (has_askme()) {
		$return = vpanel_get_countries();
	}else if (has_wpqa()) {
		$return = apply_filters('wpqa_get_countries',false);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Feed page */
add_filter("mobile_api_main_pages","mobile_api_feed_page");
function mobile_api_feed_page($main_pages) {
	if (has_wpqa()) {
		$main_pages = array_merge($main_pages,
			array("feed" => array("type" => "feed","name" => esc_html__('Feed page','mobile-api')))
		);
	}
	return $main_pages;
}
add_filter("mobile_api_options_main_pages","mobile_api_options_feed_page");
function mobile_api_options_feed_page($main_pages) {
	if (has_wpqa()) {
		$main_pages = array_merge($main_pages,
			array("feed" => esc_html__('Feed page','mobile-api'))
		);
	}
	return $main_pages;
}
/* Get video */
function mobile_api_video_iframe($video_type,$video_id,$meta_type = "",$meta_name = "",$meta_id = 0) {
	if (has_askme()) {
		$return = askme_video_iframe($video_type,$video_id,$meta_type,$meta_name,$meta_id);
	}else if (has_wpqa()) {
		$return = wpqa_video_iframe($video_type,$video_id,$meta_type,$meta_name,$meta_id);
	}
	if (isset($return)) {
		return $return;
	}
}
/* Add points */
function mobile_api_add_points($user_id,$get_points,$relation,$message,$post_id = 0,$comment_id = 0,$another_user_id = 0,$points_type = "points",$items = true) {
	if (has_askme()) {
		askme_add_points($user_id,$get_points,$relation,$message,$post_id,$comment_id,$another_user_id,$points_type,$items);
	}else if (has_wpqa()) {
		wpqa_add_points($user_id,$get_points,$relation,$message,$post_id,$comment_id,$another_user_id,$points_type,$items);
	}
}
/* App meta */
add_filter("mobile_api_config_array","mobile_api_meta_config",1,2);
function mobile_api_meta_config($array,$mobile_api_options) {
	$app_name = (isset($mobile_api_options["app_name"])?$mobile_api_options["app_name"]:"");
	$application_icon = (isset($mobile_api_options["application_icon"])?mobile_api_image_url_id($mobile_api_options["application_icon"]):"");
	$splash_screen_background = (isset($mobile_api_options["splash_screen_background"])?$mobile_api_options["splash_screen_background"]:"");
	$application_splash_screen = (isset($mobile_api_options["application_splash_screen"])?mobile_api_image_url_id($mobile_api_options["application_splash_screen"]):"");
	$app_key_id = (isset($mobile_api_options["app_key_id"])?$mobile_api_options["app_key_id"]:"");
	$app_issuer_id = (isset($mobile_api_options["app_issuer_id"])?$mobile_api_options["app_issuer_id"]:"");
	$authkey_content = (isset($mobile_api_options["authkey_content"])?$mobile_api_options["authkey_content"]:"");
	$app_apple_id = (isset($mobile_api_options["app_apple_id"])?$mobile_api_options["app_apple_id"]:"");
	$android_notification_color = (isset($mobile_api_options["android_notification_color"])?$mobile_api_options["android_notification_color"]:"");
	$android_notification_icon = (isset($mobile_api_options["android_notification_icon"])?mobile_api_image_url_id($mobile_api_options["android_notification_icon"]):"");

	$mobile_api_custom_baseurl = apply_filters("mobile_api_custom_baseurl",false);
	$activate_custom_baseurl = (isset($mobile_api_options["activate_custom_baseurl"])?$mobile_api_options["activate_custom_baseurl"]:"");
	$custom_baseurl = (isset($mobile_api_options["custom_baseurl"])?$mobile_api_options["custom_baseurl"]:"");
	if ($mobile_api_custom_baseurl != "") {
		$base_url = $mobile_api_custom_baseurl;
	}else if ($activate_custom_baseurl == mobile_api_checkbox_value && $custom_baseurl != "") {
		$base_url = $custom_baseurl;
	}else {
		$base_url = esc_url(home_url("/"));
	}
	if (substr($base_url,-1) != "/") {
		$base_url = $base_url."/";
	}
	$array["baseUrl"] = $base_url;

	if ($app_name != "") {
		$array["meta"]["app"]["appName"] = $app_name;
	}
	if ($application_icon != "") {
		$array["meta"]["app"]["appIcon"] = $application_icon;
	}

	if ($splash_screen_background != "") {
		$array["meta"]["splash"]["color"] = str_ireplace("#","",$splash_screen_background);
	}
	if ($application_splash_screen != "") {
		$array["meta"]["splash"]["image"] = $application_splash_screen;
	}

	if ($app_key_id != "") {
		$array["meta"]["ios"]["keyId"] = $app_key_id;
	}
	if ($app_issuer_id != "") {
		$array["meta"]["ios"]["issuerId"] = $app_issuer_id;
	}
	if ($authkey_content != "") {
		$array["meta"]["ios"]["authKey"] = $authkey_content;
	}
	if ($app_apple_id != "") {
		$array["meta"]["ios"]["appId"] = $app_apple_id;
	}

	if ($android_notification_icon != "" && $android_notification_color != "") {
		$array["meta"]["notifications"]["icon"] = $android_notification_icon;
		$array["meta"]["notifications"]["color"] = $android_notification_color;
	}
	$array["meta"]["applink"] = $base_url;
	$deeplink_replace = preg_replace('/[^a-zA-Z0-9._\-]/','',strtolower($app_name));
	$deeplink_replace = ($deeplink_replace != ""?$deeplink_replace:"mobileapp");
	$array["meta"]["deeplink"] = str_ireplace(array("https","http"),$deeplink_replace,$base_url);
	// with type and id if you want to open any post
	return $array;
}?>
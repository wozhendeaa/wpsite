<?php
/*
Controller name: Posts
Controller description: Data manipulation methods for posts
*/

class MOBILE_API_Posts_Controller {

	public function faqs() {
		global $mobile_api;
		$id = $mobile_api->query->id ? $mobile_api->query->id : '';
		if (empty($id)) {
			$mobile_api->error(esc_html__("Please tnclude id var in your request.","mobile-api"));
		}
		$faqs_meta = get_post_meta($id,mobile_api_theme_prefix."_faqs",true);
		$faqs = array();
		if (is_array($faqs_meta) && !empty($faqs_meta)) {
			foreach ($faqs_meta as $key => $value) {
				$faqs[] = array("text" => mobile_api_kses_stip($value["text"]),"textarea" => mobile_api_kses_stip($value["textarea"]));
			}
		}
		return array("faqs" => $faqs);
	}

	public function recent_comments() {
		global $mobile_api;
		$type = ($mobile_api->query->post_type?$mobile_api->query->post_type:'');
		$type = ($type == "answers"?array(mobile_api_questions_type):array($type));
		$type = (!empty($type)?$type:array("post"));
		return $mobile_api->introspector->get_recent_comments($type);
	}

	public function comments() {
		global $mobile_api;
		$id = $mobile_api->query->id ? $mobile_api->query->id : '';
		$type = $mobile_api->query->post_type ? $mobile_api->query->post_type : '';
		if (empty($id)) {
			$mobile_api->error(esc_html__("Please tnclude id var in your request.","mobile-api"));
		}
		do_action("mobile_api_get_comments");
		$comments = mobile_api_count_comments($id);
		$count = array("count" => (int)($comments > 0?$comments:0));
		$activate_male_female = apply_filters("mobile_api_activate_male_female",false);
		if ($activate_male_female == true && $type != "post") {
			$count_comment_only = mobile_api_options("count_comment_only");
			//$meta = ($count_comment_only == mobile_api_checkbox_value?"male_count_comments":"male_comment_count");
			$male_comment_count = (int)mobile_api_count_comments($id,"male_count_comments","like_meta");

			//$meta = ($count_comment_only == mobile_api_checkbox_value?"female_count_comments":"female_comment_count");
			$female_comment_count = (int)mobile_api_count_comments($id,"female_count_comments","like_meta");

			//$meta = ($count_comment_only == mobile_api_checkbox_value?"other_count_comments":"other_comment_count");
			$count_post_comments = (int)mobile_api_count_comments($id,"count_post_comments","like_meta");
			$other_gender_comments = ($count_post_comments-($male_comment_count+$female_comment_count));
			$count = array("comment_count" => $count,"male_comment_count" => $male_comment_count,"female_comment_count" => $female_comment_count,"other_comment_count" => $other_gender_comments);
		}
		return array_merge($count,array("comments" => $mobile_api->introspector->get_comments($id,$type)));
	}
	
	public function delete_post() {
		global $mobile_api;
		$post = $mobile_api->introspector->get_current_post();
		if (empty($post)) {
			$mobile_api->error(esc_html__("Post not found.","mobile-api"));
		}
		if (!current_user_can('edit_post', $post->ID)) {
			$mobile_api->error(esc_html__("You need to login with a user that has the edit_post capacity for that post.","mobile-api"));
		}
		if (!current_user_can('delete_posts')) {
			$mobile_api->error(esc_html__("You need to login with a user that has the delete_posts capacity.","mobile-api"));
		}
		if ($post->post_author != get_current_user_id() && !current_user_can('delete_other_posts')) {
			$mobile_api->error(esc_html__("You need to login with a user that has the delete_other_posts capacity.","mobile-api"));
		}
		nocache_headers();
		wp_delete_post($post->ID);
		return array();
	}
	
}?>
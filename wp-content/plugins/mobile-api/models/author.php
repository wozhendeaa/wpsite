<?php

class MOBILE_API_Author {
	
	var $id;          // Integer
	var $slug;        // String
	var $name;        // String
	var $first_name;  // String
	var $last_name;   // String
	var $nickname;    // String
	var $url;         // String
	var $description; // String
	
	// Note:
	//   MOBILE_API_Author objects can include additional values by using the
	//   author_meta query var.
	
	function __construct($id = 0,$type = "author",$custom = null) {
		if ($id > 0 && !isset($custom->user_id) && !isset($custom->post_author)) {
			$this->id = esc_html($id);
		} else if (isset($custom->user_id)) {
			$this->id = esc_html($custom->user_id);
		} else if (isset($custom->post_author)) {
			$this->id = esc_html($custom->post_author);
		} else {
			$this->id = esc_html(get_the_author_meta('ID'));
		}
		if ($type == "post" || $type == "comment") {
			$this->id = esc_html($type == "post"?$custom->post_author:$custom->user_id);
		}
		if (isset($custom->post_type) && $custom->post_type == "message") {
			$this->id = esc_html($id);
		}
		$this->id = (int)$this->id;
		if ($this->id > 0) {
			$verified_user = get_the_author_meta('verified_user',$this->id);
			$author_username = get_the_author_meta('display_name', $this->id);
		}else {
			if ($type == "post" || $type == "comment") {
				$custom_id = ($type == "post"?$custom->ID:$custom->comment_ID);
			}
			if ($type == "post" && isset($custom_id) && $custom_id > 0) {
				$anonymously_question = get_post_meta($custom_id,"anonymously_question",true);
				$anonymously_user = get_post_meta($custom_id,"anonymously_user",true);
				if (($anonymously_question == "on" || $anonymously_question == 1) && $anonymously_user != "") {
					$author_username = esc_html__('Anonymous','mobile-api');
				}else {
					$author_email = get_post_meta($custom_id,(isset($custom->post_type) && ($custom->post_type == mobile_api_questions_type || $custom->post_type == mobile_api_asked_questions_type)?mobile_api_questions_type:(isset($custom->post_type) && $custom->post_type == "message"?"message":"post"))."_email",true);
					$author_username = get_post_meta($custom_id,(isset($custom->post_type) && ($custom->post_type == mobile_api_questions_type || $custom->post_type == mobile_api_asked_questions_type)?mobile_api_questions_type:(isset($custom->post_type) && $custom->post_type == "message"?"message":"post"))."_username",true);
					$author_username = ($author_username != ""?$author_username:esc_html__('[Deleted User]','mobile-api'));
				}
			}else if (($type == "comment" && $custom->comment_author == "") || ($type == "post" && ($anonymously_question == "on" || $anonymously_question == 1) && ($this->id == 0 || $this->id == ""))) {
				$author_username = esc_html__('Anonymous','mobile-api');
			}else if ($type == "comment" && $custom->comment_author != "") {
				$author_username = $custom->comment_author;
			}
		}
		$active_points_category = mobile_api_options("active_points_category");
		if ($active_points_category == "on" || $active_points_category == 1) {
			$categories_user_points = get_user_meta($this->id,"categories_user_points",true);
			if (is_array($categories_user_points) && !empty($categories_user_points)) {
				foreach ($categories_user_points as $category) {
					$points_category_user[$category] = (int)get_user_meta($this->id,"points_category".$category,true);
				}
				arsort($points_category_user);
				$first_category = (is_array($points_category_user)?key($points_category_user):"");
				$first_points = reset($points_category_user);
			}
		}
		$badge_color = mobile_api_get_badge($this->id,"color",(isset($first_points)?$first_points:""));
		$privacy_bio = mobile_api_check_user_privacy($this->id,"bio");
		$privacy_credential = mobile_api_check_user_privacy($this->id,"credential");
		$privacy_website = mobile_api_check_user_privacy($this->id,"website");
		$this->set_value('slug', 'user_nicename');
		$this->name = $author_username;
		$this->set_value('first_name', 'first_name');
		$this->set_value('last_name', 'last_name');
		$this->set_value('nickname', 'nickname');
		if ($privacy_bio == true) {
			$this->set_value('description', 'description');
		}
		if ($privacy_website == true) {
			$this->set_value('url', 'user_url');
		}
		if ($privacy_credential == true) {
			$profile_credential = get_user_meta($this->id,"profile_credential",true);
			$profile_credential = ($profile_credential != ""?$profile_credential:"false");
		}
		if (isset($custom_id) && $custom_id > 0) {
			$avatar_array = array("user_id" => (isset($this->id) && $this->id > 0?$this->id:0),"size" => 128,"comment" => $custom,"post" => $custom_id,"name" => $author_username,"email" => (isset($author_email) && $author_email != ""?$author_email:""));
		}else {
			$avatar_array = array("user_id" => $this->id,"size" => 128);
		}
		$this->avatar = mobile_api_user_avatar_link($avatar_array);
		$this->verified = (isset($verified_user) && ($verified_user == 1 || $verified_user == "on")?true:false);
		$this->badge = array("name" => strip_tags(mobile_api_get_badge($this->id,"name",(isset($first_points)?$first_points:""))),"color" => ($badge_color != ""?$badge_color:""),"textColor" => "FFFFFF","textColorDark" => "FFFFFF");
		$this->profile_credential = ($privacy_credential == true?$profile_credential:"false");
		$following_you  = get_user_meta($this->id,"following_you",true);
		$get_current_user_id = get_current_user_id();
		if ($get_current_user_id > 0 && $get_current_user_id != $this->id) {
			$this->followed = (!empty($following_you) && in_array($get_current_user_id,$following_you)?true:false);
		}
		$this->set_author_meta();
		$this->custom_author_meta($this->id);
	}
	
	function set_value($key, $wp_key = false) {
		if (!$wp_key) {
			$wp_key = $key;
		}
		$this->$key = get_the_author_meta($wp_key, $this->id);
	}
	
	function set_custom_value($key, $value) {
		$this->$key = $value;
	}
	
	function set_author_meta() {
		global $mobile_api;
		if (!$mobile_api->query->author_meta) {
			return;
		}
		$protected_vars = array(
			'user_login',
			'user_pass',
			'user_email',
			'user_activation_key'
		);
		$vars = explode(',', $mobile_api->query->author_meta);
		$vars = array_diff($vars, $protected_vars);
		foreach ($vars as $var) {
			$this->set_value($var);
		}
	}
	
	function custom_author_meta($user_id) {
		$vars = array();
		$vars = apply_filters("mobile_api_author_meta",$vars,$user_id);
		if (is_array($vars) && !empty($vars)) {
			foreach ($vars as $key => $var) {
				$this->set_custom_value($key,$var);
			}
		}
	}
	
}?>
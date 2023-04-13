<?php

class MOBILE_API_Post {
	
	// Note:
	//   MOBILE_API_Post objects must be instantiated within The Loop.
	
	var $id;             // Integer 
	var $type;           // String
	var $slug;           // String
	var $url;            // String
	var $status;         // String ("draft", "published", or "pending")
	var $title;          // String
	var $title_plain;    // String
	var $content;        // String (modified by read_more query var)
	var $excerpt;        // String
	var $date;           // String (modified by date_format query var)
	var $modified;       // String (modified by date_format query var)
	var $categories;     // Array of objects
	var $tags;           // Array of objects
	var $author;         // Object
	var $attachments;    // Array of objects
	var $comment_status; // String ("open" or "closed")
	var $thumbnail;      // String
	var $custom_fields;  // Object (included by using custom_fields query var)
	
	function __construct($wp_post = null,$single = '',$edit_post = '') {
		if (!empty($wp_post)) {
			$this->import_wp_object($wp_post,$single,$edit_post);
		}
		do_action("mobile_api_{$this->type}_constructor", $this);
	}

	function create($values = null) {
		unset($values['id']);
		if (empty($values) || empty($values['title'])) {
			$values = array(
				'title' => 'Untitled',
				'content' => ''
			);
		}
		return $this->save($values);
	}
	
	function update($values) {
		$values['id'] = $this->id;
		return $this->save($values);
	}
	
	function save($values = null) {
		global $mobile_api, $user_ID;
		
		$wp_values = array();
		
		if (!empty($values['id'])) {
			$wp_values['ID'] = $values['id'];
		}
		
		if (!empty($values['type'])) {
			$wp_values['post_type'] = $values['type'];
		}
		
		if (!empty($values['status'])) {
			$wp_values['post_status'] = $values['status'];
		}
		
		if (!empty($values['title'])) {
			$wp_values['post_title'] = $values['title'];
		}
		
		if (!empty($values['content'])) {
			$wp_values['post_content'] = $values['content'];
		}
		
		if (!empty($values['author'])) {
			$author = $mobile_api->introspector->get_author_by_login($values['author']);
			$wp_values['post_author'] = $author->id;
		}
		
		if (isset($values['categories'])) {
			$categories = explode(',', $values['categories']);
			foreach ($categories as $category_slug) {
				$category_slug = trim($category_slug);
				$category = $mobile_api->introspector->get_category_by_slug($category_slug);
				if (empty($wp_values['post_category'])) {
					$wp_values['post_category'] = array($category->id);
				} else {
					array_push($wp_values['post_category'], $category->id);
				}
			}
		}
		
		if (isset($values['tags'])) {
			$tags = explode(',', $values['tags']);
			foreach ($tags as $tag_slug) {
				$tag_slug = trim($tag_slug);
				if (empty($wp_values['tags_input'])) {
					$wp_values['tags_input'] = array($tag_slug);
				} else {
					array_push($wp_values['tags_input'], $tag_slug);
				}
			}
		}
		
		if (isset($wp_values['ID'])) {
			$this->id = wp_update_post($wp_values);
		} else {
			$this->id = wp_insert_post($wp_values);
		}
		
		if (!empty($_FILES['attachment'])) {
			include_once ABSPATH . '/wp-admin/includes/file.php';
			include_once ABSPATH . '/wp-admin/includes/media.php';
			include_once ABSPATH . '/wp-admin/includes/image.php';
			$attachment_id = media_handle_upload('attachment', $this->id);
			$this->attachments[] = new MOBILE_API_Attachment($attachment_id);
			unset($_FILES['attachment']);
		}
		
		$wp_post = get_post($this->id);
		$this->import_wp_object($wp_post);
		
		return $this->id;
	}
	
	function import_wp_object($wp_post,$single = '',$edit_post = '') {
		global $mobile_api, $post;
		$show_custom_fields = true;
		$this->id = (int)$wp_post->ID;
		setup_postdata($wp_post);
		$post_type = $wp_post->post_type;
		$post_author = (int)$wp_post->post_author;
		$user_id = get_current_user_id();
		$is_super_admin = is_super_admin($user_id);
		$get_permalink = (has_wpqa() && mobile_api_groups() && $post_type == "posts"?wpqa_custom_permalink($this->id,"view_posts_group","view_group_post"):get_permalink($this->id));
		$post_from_front = get_post_meta($this->id,'post_from_front',true);
		$date_format = mobile_api_options("date_format");
		$date_format = ($date_format != ""?$date_format:get_option("date_format"));
		$format_date_ago = mobile_api_options("format_date_ago");
		$format_date_ago_types = mobile_api_options("format_date_ago_types");
		if ($format_date_ago == mobile_api_checkbox_value && (($post_type == "post" && isset($format_date_ago_types["posts"]) && $format_date_ago_types["posts"] == "posts") || ($post_type == mobile_api_knowledgebase_type && isset($format_date_ago_types["knowledgebases"]) && $format_date_ago_types["knowledgebases"] == "knowledgebases") || ((($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type)) && isset($format_date_ago_types["questions"]) && $format_date_ago_types["questions"] == "questions"))) {
			$time_string = human_time_diff(get_the_time('U',$this->id),current_time('timestamp'))." ".esc_html__("ago","mobile-api");
			$modified_string = human_time_diff(strtotime($wp_post->post_modified),current_time('timestamp'))." ".esc_html__("ago","mobile-api");
		}else {
			$time_string = esc_html(get_the_time($date_format,$this->id));
			$modified_string = date($date_format, strtotime($wp_post->post_modified));
		}
		$mobile_adv = mobile_api_options("mobile_adv");
		$mobile_banner_adv = mobile_api_options("mobile_banner_adv");
		$custom_permission = mobile_api_options("custom_permission");
		if (is_user_logged_in()) {
			$user_is_login = get_userdata($user_id);
			$roles = $user_is_login->allcaps;
		}
		$show_ads = true;
		if ($custom_permission == mobile_api_checkbox_value) {
			if (($custom_permission == mobile_api_checkbox_value && $user_id > 0 && !is_super_admin($user_id) && empty($roles["without_ads"])) || ($custom_permission == mobile_api_checkbox_value && $user_id == 0)) {
				$show_ads = true;
			}else {
				$show_ads = false;
			}
		}
		$show_ads = apply_filters("mobile_api_show_ads",$show_ads);
		if ($single == 'single') {
			$ads = apply_filters("mobile_api_posts_ads",array(),$this->id,$post_type,$show_ads);
			if (isset($ads) && is_array($ads) && !empty($ads)) {
				$this->set_value('ads', $ads);
			}
		}

		if (has_wpqa()) {
			$result = wpqa_reaction_results('wpqa_reactions',$user_id,$this->id,0);
			$this->set_value('reactions', $result);
		}

		$this->set_value('type', $post_type);
		$this->set_value('share', $get_permalink);
		$this->set_value('slug', $wp_post->post_name);
		$this->set_value('url', $get_permalink);
		$this->set_value('status', $wp_post->post_status);
		$this->set_value('title', htmlspecialchars_decode($wp_post->post_title));
		$this->set_value('title_plain', strip_tags($this->title));
		if ($edit_post == "edit") {
			$this->set_value('content', htmlspecialchars_decode($wp_post->post_content));
		}else {
			$this->set_content_value('not_return',$this->id,do_blocks($wp_post->post_content),$post_from_front,$edit_post);
		}
		$the_excerpt = mobile_api_kses_stip(apply_filters('the_excerpt',get_the_excerpt($this->id)),$edit_post);
		$last_word_start = strrpos($the_excerpt,' ') + 1;
		$last_word = substr($the_excerpt,$last_word_start);
		$activate_male_female = apply_filters("mobile_api_activate_male_female",false);
		if ($activate_male_female == true) {
			$gender = get_the_author_meta((has_askme()?'sex':'gender'),$post_author);
			$gender = ($gender == "male" || $gender == 1?"male":"").($gender == "female" || $gender == 2?"female":"").($gender == "other" || $gender == 3?"other":"");
		}
		$the_excerpt = (strpos($last_word,"[&hellip;]") !== false?str_replace(array("[&hellip;]","[...]"),"<span class='wpqa-read-more'".($activate_male_female == true?" gender='".($gender != ""?$gender:"other")."'":"").">".esc_html__('Read more','mobile-api')."</span>",$the_excerpt):$the_excerpt);
		$the_excerpt = htmlspecialchars_decode($the_excerpt);
		$this->set_value('excerpt', ($the_excerpt != ""?$the_excerpt:mobile_api_preg_replace(do_blocks($wp_post->post_content))));
		$video_desc_active_loop = mobile_api_options("video_desc_active_loop");
		if ($video_desc_active_loop == mobile_api_checkbox_value) {
			$video_desc_loop = mobile_api_options("video_desc_loop");
			$this->set_value('video_excerpt', ($video_desc_loop == "before"?"before":"after"));
		}
		$this->set_value('date', $time_string);
		$this->set_value('modified', $modified_string);
		$this->set_author_value($post_author,"post",$wp_post);

		if (($post_type == 'post' || $post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) && $single == 'single') {
			$moderators_permissions = (has_wpqa()?wpqa_user_moderator($user_id):"");
			$moderator_categories = (has_wpqa()?wpqa_user_moderator_categories($user_id,$this->id):"");
		}

		if (mobile_api_groups()) {
			$mobile_group_red = mobile_api_options("mobile_group_red");
			$mobile_group_green = mobile_api_options("mobile_group_green");
			if ($post_type == 'group') {
				$group_moderators = get_post_meta($this->id,"group_moderators",true);
				if ($user_id > 0 && isset($group_moderators) && is_array($group_moderators) && in_array($user_id,$group_moderators)) {
					$allow_group_moderators = true;
				}
				if ($is_super_admin || ($user_id > 0 && $post_author == $user_id) || isset($allow_group_moderators)) {
					$mobile_sticky_post = true;
				}
				if ($is_super_admin || ($user_id > 0 && $user_id == $post_author)) {
					$mobile_delete = true;
				}
				if ($single == 'single') {
					$group_edit = mobile_api_options("group_edit");
					if ($custom_permission == mobile_api_checkbox_value) {
						if (is_user_logged_in()) {
							$edit_other_groups = (isset($roles["edit_other_groups"]) && $roles["edit_other_groups"] == 1?true:false);
						}
					}
					if (has_wpqa()) {
						$custom_moderators_permissions = get_user_meta($user_id,mobile_api_author_prefix."custom_moderators_permissions",true);
						if ($custom_moderators_permissions == mobile_api_checkbox_value) {
							$wpqa_user_permissions = wpqa_user_permissions($user_id);
							$edit_other_groups = (isset($wpqa_user_permissions['edit_groups']) && $wpqa_user_permissions['edit_groups'] == "edit_groups"?true:false);
						}
					}
					if ($is_super_admin || ($group_edit == mobile_api_checkbox_value && ((isset($edit_other_groups) && $edit_other_groups == true && isset($allow_group_moderators)) || ($post_author == $user_id)) && $user_id > 0) || isset($allow_group_moderators)) {
						if ($is_super_admin || ($group_edit == mobile_api_checkbox_value && ((isset($edit_other_groups) && $edit_other_groups == true && isset($allow_group_moderators)) || ($post_author == $user_id) && $user_id > 0))) {
							$mobile_edit = true;
						}else {
							$mobile_edit_rule = true;
						}
					}
				}
			}
			if ($post_type == 'posts') {
				$posts_delete = mobile_api_options("posts_delete");
				$edit_delete_posts_comments = mobile_api_options("edit_delete_posts_comments");
				$group_id = get_post_meta($this->id,"group_id",true);
				if ($group_id > 0) {
					$group_moderators = get_post_meta($group_id,"group_moderators",true);
					if ($user_id > 0 && isset($group_moderators) && is_array($group_moderators) && in_array($user_id,$group_moderators)) {
						$allow_group_moderators = true;
					}
					$post_author_group = get_post($group_id);
					$post_author_group = $post_author_group->post_author;
				}

				if ($is_super_admin || ($user_id > 0 && isset($post_author_group) && $post_author_group == $user_id) || isset($allow_group_moderators)) {
					$mobile_sticky_post = true;
				}

				$custom_posts_edit = mobile_api_options("posts_edit");
				if (($custom_posts_edit == mobile_api_checkbox_value && $post_author == $user_id && $user_id > 0 && $wp_post->post_status == "publish") || $is_super_admin || (isset($edit_delete_posts_comments["edit"]) && $edit_delete_posts_comments["edit"] == "edit" && isset($allow_group_moderators))) {
					$mobile_edit = true;
					$allow_to_delete_thumbnail = true;
				}

				if (($posts_delete == mobile_api_checkbox_value && $post_author == $user_id) || (isset($edit_delete_posts_comments["delete"]) && $edit_delete_posts_comments["delete"] == "delete" && isset($allow_group_moderators)) || $is_super_admin) {
					$mobile_delete = true;
				}
			}
			if (isset($mobile_sticky_post)) {
				$this->set_value('mobile_sticky_post', true);
			}
		}

		if ($post_type == 'post' && $single == 'single') {
			$post_delete   = mobile_api_options("post_delete");
			$can_edit_post = mobile_api_options("can_edit_post");
			$edit = ($is_super_admin || ((($user_id == $post_author && $post_author > 0)) && $can_edit_post == mobile_api_checkbox_value) || ($moderator_categories == true && isset($moderators_permissions['edit']) && $moderators_permissions['edit'] == "edit")?true:false);
			$delete = ($is_super_admin || ((($user_id == $post_author && $post_author > 0)) && $post_delete == mobile_api_checkbox_value) || ($moderator_categories == true && isset($moderators_permissions['delete']) && $moderators_permissions['delete'] == "delete")?true:false);
			if ($edit == true) {
				$mobile_edit = true;
				$allow_to_delete_thumbnail = true;
			}
			if ($delete == true) {
				$mobile_delete = true;
			}
		}

		if ($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) {
			$anonymously_user = get_post_meta($this->id,'anonymously_user',true);
			$mobile_setting_follow_questions = mobile_api_options("mobile_setting_follow_questions");
			$question_follow_loop = mobile_api_options("question_follow_loop");
			if ($mobile_setting_follow_questions == mobile_api_checkbox_value) {
				$get_question_user_id = get_post_meta($this->id,"user_id",true);
				$following_questions = get_post_meta($this->id,'following_questions',true);
				$can_follow = ($user_id > 0 && $user_id != $get_question_user_id && ($user_id != $post_author || ($anonymously_user != "" && $anonymously_user != $user_id))?true:false);
				$custom_follow = false;
				if (has_askme()) {
					if (($custom_permission != mobile_api_checkbox_value || (is_super_admin($user_id) || ($user_id > 0 && isset($roles["follow_question"]) && $roles["follow_question"] == 1))) && $can_follow == true) {
						$custom_follow = true;
					}
				}else if ($can_follow == true) {
					$custom_follow = true;
				}
				if ($custom_follow == true) {
					if (isset($following_questions) && is_array($following_questions) && in_array($user_id,$following_questions)) {
						if ($single == 'single' || ($question_follow_loop == mobile_api_checkbox_value && $single != 'single')) {
							$this->set_value('mobile_can_unfollow', true);
						}
						$this->set_value('mobile_activated_follow', true);
					}else {
						if ($single == 'single' || ($question_follow_loop == mobile_api_checkbox_value && $single != 'single')) {
							$this->set_value('mobile_can_follow', true);
						}
					}
				}
			}
		}

		if (($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) && $single == 'single') {
			$question_edit = mobile_api_options("question_edit");
			$question_delete = mobile_api_options("question_delete");
			$edit   = ($is_super_admin || ((($user_id == $post_author && $post_author > 0) || ($anonymously_user == $user_id && $post_author > 0)) && $question_edit == mobile_api_checkbox_value) || ($moderator_categories == true && isset($moderators_permissions['edit']) && $moderators_permissions['edit'] == "edit")?true:false);
			$delete = ($is_super_admin || ((($user_id == $post_author && $post_author > 0) || ($anonymously_user == $user_id && $post_author > 0)) && $question_delete == mobile_api_checkbox_value) || ($moderator_categories == true && isset($moderators_permissions['delete']) && $moderators_permissions['delete'] == "delete")?true:false);
			if ($edit == true) {
				$mobile_edit = true;
				$allow_to_delete_thumbnail = true;
				$added_file = get_post_meta($this->id,"added_file",true);
				if ($added_file != "") {
					$this->delete_attachment[] = array("delete" => array("api" => mobile_api_delete.'?type=post_meta&name=added_file&&attach_id='.$added_file.'&id='.$this->id,"alert" => esc_html__('Are you sure you want to delete the attachment?','mobile-api')));
				}
				$attachment_m = get_post_meta($this->id,"attachment_m",true);
				if (isset($attachment_m) && is_array($attachment_m) && !empty($attachment_m)) {
					foreach ($attachment_m as $key => $value) {
						$this->delete_attachment[] = array("delete" => array("api" => mobile_api_delete.'?type=attachment_m&&attach_id='.$value["added_file"].'&id='.$this->id,"alert" => esc_html__('Are you sure you want to delete the attachment?','mobile-api')));
					}
				}
			}
			if ($delete == true) {
				$mobile_delete = true;
			}
			$question_close = mobile_api_options("question_close");
			$close = ($is_super_admin || ((($user_id == $post_author && $post_author > 0) || ($anonymously_user == $user_id && $post_author > 0) || ($moderator_categories == true && isset($moderators_permissions['close']) && $moderators_permissions['close'] == "close")) && $question_close == mobile_api_checkbox_value)?true:false);
			if ($close == true) {
				$closed_question = get_post_meta($this->id,"closed_question",true);
				if ($closed_question == 1) {
					$this->set_value('mobile_open_question', true);
					$this->set_value('close_open_api', mobile_api_open.'?id='.$this->id);
				}else {
					$this->set_value('mobile_close_question', true);
					$this->set_value('close_open_api', mobile_api_close.'?id='.$this->id);
				}
			}
			$question_bump = mobile_api_options("question_bump");
			$active_points = mobile_api_options("active_points");
			$comments = mobile_api_count_comments($this->id);
			if ($user_id > 0 && $question_bump == mobile_api_checkbox_value && $active_points == mobile_api_checkbox_value && ($comments == "" || $comments == 0) && $user_id == $post_author) {
				$this->set_value('mobile_bump_question', true);
				$this->set_value('bump_question_api', mobile_api_bump);
			}
		}

		if (isset($allow_to_delete_thumbnail)) {
			$featured_image = get_post_meta($this->id,'_thumbnail_id',true);
			if ($featured_image != "") {
				$get_attachment_url = wp_get_attachment_url($featured_image);
				if ($get_attachment_url != "") {
					if (isset($mobile_edit)) {
						$this->delete_featured_image["delete"]["api"] = mobile_api_delete.'?type=post_meta&name=_thumbnail_id&attach_id='.$featured_image.'&id='.$this->id;
						$this->delete_featured_image["delete"]["alert"] = esc_html__('Are you sure you want to delete the image?','mobile-api');
					}
				}
			}
		}

		if (isset($mobile_edit)) {
			$this->set_value('mobile_edit', true);
		}

		if (isset($mobile_edit_rule)) {
			$this->set_value('mobile_edit_rule', true);
		}

		if (isset($mobile_edit) || isset($mobile_edit_rule)) {
			$this->set_value('mobile_edit_api', mobile_api_get_post."/?id=".$this->id."&edit=edit&post_type=".$post_type);
		}

		if (isset($mobile_delete)) {
			$this->set_value('mobile_delete', true);
			$this->set_value('delete_api', mobile_api_delete.'?type='.$post_type.'&id='.$this->id);
		}

		if (mobile_api_groups() && $post_type == 'posts' && $wp_post->post_status == 'draft') {
			$group_posts_id = get_post_meta($this->id,"group_id",true);
			$posts_actions["button_1"] = [
				[
					"action" => "agree",
					"name" => esc_html__("Agree","mobile-api"),
					"color" => "ffffff",
					"darkColor" => "ffffff",
					"background" => $mobile_group_green,
					"darkbackground" => $mobile_group_green,
					"api" => mobile_api_group_actions_2."?action=agree_posts_group&id=".$this->id."&user_id=".$post_author
				]
			];
			$blocked_users = get_post_meta($group_posts_id,"blocked_users_array",true);
			if (!isset($group_moderators)) {
				$group_moderators = get_post_meta($group_id,"group_moderators",true);
			}
			if ($is_super_admin || $post_author == $user_id || ($user_id > 0 && is_array($group_moderators) && in_array($user_id,$group_moderators))) {
				if (isset($blocked_users) && is_array($blocked_users) && in_array($post_author,$blocked_users)) {
					$posts_actions["button_2"] = [
						[
							"action" => "unblock",
							"name" => esc_html__("Unblock","mobile-api"),
							"color" => "ffffff",
							"darkColor" => "ffffff",
							"background" => $mobile_group_green,
							"darkbackground" => $mobile_group_green,
							"api" => mobile_api_group_actions_2."?action=unblock_user_group&id=".$group_posts_id."&user_id=".$post_author
						]
					];
				}else {
					$posts_actions["button_2"] = [
						[
							"action" => "remove",
							"name" => esc_html__("Remove","mobile-api"),
							"color" => "ffffff",
							"darkColor" => "ffffff",
							"background" => $mobile_group_red,
							"darkbackground" => $mobile_group_red,
							"api" => mobile_api_group_actions_2."?action=remove_user_group&id=".$group_posts_id."&user_id=".$post_author,
							"alert" => esc_html__('Are you sure you want to remove the user from the group?','mobile-api')
						]
					];
					$posts_actions["button_3"] = [
						[
							"action" => "block",
							"name" => esc_html__("Block","mobile-api"),
							"color" => "ffffff",
							"darkColor" => "ffffff",
							"background" => $mobile_group_red,
							"darkbackground" => $mobile_group_red,
							"api" => mobile_api_group_actions_2."?action=block_user_group&id=".$group_posts_id."&user_id=".$post_author,
							"alert" => esc_html__('Are you sure you want to block the user from the group?','mobile-api')
						]
					];
				}
			}
			if (isset($posts_actions)) {
				$this->set_value('posts_actions',$posts_actions);
			}
		}
		if ($single == 'single') {
			if (mobile_api_groups() && $post_type == 'group') {
				$add_post_button = $invite_users = $assign_moderators = $approve_all = $decline_all = $counts_1 = $counts_2 = array();
				$blocked_users = get_post_meta($this->id,"blocked_users_array",true);
				$group_requests_array = get_post_meta($this->id,"group_requests_array",true);
				$group_privacy = get_post_meta($this->id,"group_privacy",true);
				$group_users_array = get_post_meta($this->id,"group_users_array",true);
				$group_allow_posts = get_post_meta($this->id,"group_allow_posts",true);
				$group_moderators = get_post_meta($this->id,"group_moderators",true);
				$group_invitation = get_post_meta($this->id,"group_invitation",true);
				$group_comments = get_post_meta($this->id,"group_comments",true);
				if ($user_id > 0 && isset($group_moderators) && is_array($group_moderators) && in_array($user_id,$group_moderators)) {
					$allow_group_moderators = true;
				}
				if (!$is_super_admin && is_array($blocked_users) && in_array($user_id,$blocked_users)) {
					$group_permissions[] = array("blocked" => esc_html__("Sorry, you blocked from this group.","mobile-api"));
				}
				if ($group_privacy == "public" || ($group_privacy == "private" && ($is_super_admin || (is_array($group_users_array) && in_array($user_id,$group_users_array))))) {
					// it's opening
				}else {
					$group_permissions[] = array("closed" => esc_html__("Sorry, this is a private group.","mobile-api"));
				}
				if ($user_id > 0 && ($is_super_admin || $post_author == $user_id || (($group_allow_posts == "all" || $group_allow_posts == "admin_moderators") && isset($allow_group_moderators)) || ($group_allow_posts == "all" && is_array($group_users_array) && in_array($user_id,$group_users_array)))) {
					$mobile_primary = mobile_api_options("mobile_primary");
					$mobile_primary_dark = mobile_api_options("dark_mobile_primary");
					$mobile_primary_dark = ($mobile_primary_dark != ""?$mobile_primary_dark:$mobile_primary);
					$mobile_primary = str_replace("#","",$mobile_primary);
					$mobile_primary_dark = str_replace("#","",$mobile_primary_dark);
					$add_post_button = array("button_1" => array(
							"name" => esc_html__("Add Post","mobile-api"),
							"color" => "ffffff",
							"darkColor" => "ffffff",
							"background" => $mobile_primary,
							"darkbackground" => $mobile_primary_dark,
							"api" => "add_group_post"
						)
					);
				}else {
					$group_permissions[] = array("cantPost" => true);
				}
				if ($wp_post->post_status == 'publish' && (($user_id > 0 && $group_comments == mobile_api_checkbox_value) || isset($allow_group_moderators) || $is_super_admin)) {
					// Can comment
				}else {
					$group_permissions[] = array("cantComment" => true);
				}
				if (isset($group_permissions) && is_array($group_permissions) && !empty($group_permissions)) {
					$this->set_value('group_permissions',$group_permissions);
				}
				if ($is_super_admin || ($user_id > 0 && $user_id == $post_author)) {
					// Owner
				}else if (!$is_super_admin && $user_id > 0) {
					if (is_array($group_users_array) && in_array($user_id,$group_users_array)) {
						$join_leave_text = esc_html__("Leave","mobile-api");
						$join_leave_class = "leave_group";
						$button_color = $mobile_group_red;
					}else {
						if (is_array($group_requests_array) && in_array($user_id,$group_requests_array)) {
							$join_leave_text = esc_html__("Cancel the request","mobile-api");
							$join_leave_class = "cancel_request_group";
							$button_color = $mobile_group_red;
						}else {
							$group_invitations = get_post_meta($this->id,"group_invitations",true);
							if (is_array($group_invitations) && in_array($user_id,$group_invitations)) {
								$join_leave_text = esc_html__("Accept invite","mobile-api");
								$join_leave_class = "accept_invite";
								$button_color = $mobile_group_green;
								$join_leave_text_2 = esc_html__("Decline invite","mobile-api");
								$join_leave_class_2 = "decline_invite";
								$button_color_2 = $mobile_group_red;
							}else {
								$join_leave_text = esc_html__("Join","mobile-api");
								if ($group_privacy == "public") {
									$join_leave_class = "join_group";
									$button_color = $mobile_group_green;
								}else {
									$join_leave_class = "request_group";
									$button_color = $mobile_group_green;
								}
							}
						}
					}
					$group_actions["button_1"] = [
						[
							"action" => $join_leave_class,
							"name" => $join_leave_text,
							"color" => "ffffff",
							"darkColor" => "ffffff",
							"background" => $button_color,
							"darkbackground" => $button_color,
							"api" => mobile_api_group_actions."?action=".$join_leave_class."&id=".$this->id
						]
					];
					if (isset($join_leave_class_2)) {
						$group_actions["button_2"] = [
							[
								"action" => $join_leave_class_2,
								"name" => $join_leave_text_2,
								"color" => "ffffff",
								"darkColor" => "ffffff",
								"background" => $button_color_2,
								"darkbackground" => $button_color_2,
								"api" => mobile_api_group_actions."?action=".$join_leave_class_2."&id=".$this->id
							]
						];
					}
					if (isset($group_actions)) {
						$this->set_value('group_actions',$group_actions);
					}
				}
				if ($user_id > 0 && ($is_super_admin || $post_author == $user_id || (($group_invitation == "all" || $group_invitation == "admin_moderators") && isset($allow_group_moderators)) || ($group_invitation == "all" && is_array($group_users_array) && in_array($user_id,$group_users_array)))) {
					$invite_users = array(
						"invite_users" => "true",
						"api_find" => mobile_api_find_user_group."?action=invite&group_id=".$this->id."&search=",
						"api_add" => mobile_api_add_user_group."?action=add&group_id=".$this->id."&user_id="
					);
				}
				if ($is_super_admin || ($post_author == $user_id && $user_id > 0)) {
					$assign_moderators = array(
						"assign_moderators" => "true",
						"api_find" => mobile_api_find_user_group."?action=moderator&group_id=".$this->id."&search=",
						"api_add" => mobile_api_add_user_group."?action=moderator&group_id=".$this->id."&user_id="
					);
				}
				if (isset($group_requests_array) && is_array($group_requests_array) && !empty($group_requests_array)) {
					$approve_all = array("button_1" => array(
							"name" => esc_html__("Approve all","mobile-api"),
							"color" => "ffffff",
							"darkColor" => "ffffff",
							"background" => $mobile_group_green,
							"darkbackground" => $mobile_group_green,
							"api" => mobile_api_group_actions."?action=approve_request_all_group&id=".$this->id
						)
					);
					$decline_all = array("button_2" => array(
							"name" => esc_html__("Decline all","mobile-api"),
							"color" => "ffffff",
							"darkColor" => "ffffff",
							"background" => $mobile_group_red,
							"darkbackground" => $mobile_group_red,
							"api" => mobile_api_group_actions."?action=decline_request_all_group&id=".$this->id
						)
					);
				}

				$view_users_group = mobile_api_options("view_users_group");
				if ($is_super_admin || (isset($view_users_group[$group_privacy]) && $view_users_group[$group_privacy] == $group_privacy && is_array($group_users_array) && !empty($group_users_array) && in_array($user_id,$group_users_array)) || ($post_author == $user_id && $user_id > 0) || isset($allow_group_moderators)) {
					$allow_to_see_tab = true;
				}
				if ($is_super_admin || ($post_author == $user_id && $user_id > 0) || isset($allow_group_moderators)) {
					$moderators = true;
				}
				if (isset($moderators)) {
					$group_pages = array("group","edit","group_requests","pending_posts","group_users","group_admins","blocked_users");
					if ($group_privacy == "public") {
						$group_pages = array_diff($group_pages,array("group_requests"));
					}
					if (!$is_super_admin && $user_id > 0 && $user_id != $post_author) {
						$group_pages = array_diff($group_pages,array("group_admins"));
					}
				}else {
					$group_pages = array("group","group_users");
				}
				if (isset($group_pages) && is_array($group_pages) && !empty($group_pages)) {
					foreach ($group_pages as $key) {
						if ($key == "group") {
							$group_tabs[] = array_merge(array("type" => "posts","name" => esc_html__("Discussion","mobile-api"),"api" => mobile_api_group_posts."?group_id=".$this->id."&count=3"),$invite_users,$add_post_button);
						}else if ($key == "group_requests" && isset($allow_to_see_tab)) {
							$group_requests_array = (is_array($group_requests_array) && !empty($group_requests_array)?$group_requests_array:array());
							$group_requests_array = count($group_requests_array);
							$counts_requests = ($group_requests_array > 0?($group_requests_array <= 99?$group_requests_array:"99+"):"");
							$counts_1 = ($counts_requests != ""?array("counts" => "{$counts_requests}"):array());
							$group_tabs[] = array_merge(array("type" => "users","name" => esc_html__("Requests","mobile-api"),"api" => mobile_api_group_users."?group_id=".$this->id."&type=requests&count=3"),$counts_1,$approve_all,$decline_all);
						}else if ($key == "pending_posts" && isset($allow_to_see_tab)) {
							$count_posts_by_type = wpqa_count_group_posts_by_type("posts","draft",$this->id);
							$counts_posts = ($count_posts_by_type > 0?($count_posts_by_type <= 99?$count_posts_by_type:"99+"):"");
							$counts_2 = ($counts_posts != ""?array("counts" => "{$counts_posts}"):array());
							$group_tabs[] = array_merge(array("type" => "posts","name" => esc_html__("Group Posts","mobile-api"),"api" => mobile_api_group_posts."?group_id=".$this->id."&post_status=draft&count=3"),$counts_2);
						}else if ($key == "group_users" && isset($allow_to_see_tab)) {
							$group_tabs[] = array_merge(array("type" => "users","name" => esc_html__("Users","mobile-api"),"api" => mobile_api_group_users."?group_id=".$this->id."&type=users&count=3"),$invite_users);
						}else if ($key == "group_admins" && isset($allow_to_see_tab)) {
							$group_tabs[] = array_merge(array("type" => "users","name" => esc_html__("Admins","mobile-api"),"api" => mobile_api_group_users."?group_id=".$this->id."&type=admins&count=3"),$assign_moderators);
						}else if ($key == "blocked_users" && isset($allow_to_see_tab)) {
							$group_tabs[] = array("type" => "users","name" => esc_html__("Blocked Users","mobile-api"),"api" => mobile_api_group_users."?group_id=".$this->id."&type=blocked&count=3");
						}
					}
				}
				if (isset($group_tabs) && is_array($group_tabs) && !empty($group_tabs)) {
					$this->set_value('group_tabs',$group_tabs);
				}
			}
			if (($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type)) {
				$question_category = wp_get_post_terms($this->id,mobile_api_question_categories,array("fields" => "all"));
				if (isset($question_category[0])) {
					$category_new = get_term_meta($question_category[0]->term_id,prefix_terms."new",true);
					$category_special = get_term_meta($question_category[0]->term_id,prefix_terms."special",true);
				}
				$add_answer = mobile_api_options("add_answer");
			}else {
				$add_comment = mobile_api_options("add_comment");
			}
			if (($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type)) {
				$yes_new = 1;
				if (have_comments()) {
					if (isset($question_category[0]) && $category_new == mobile_api_checkbox_value) {
						$yes_new = 0;
						if ($user_id > 0 && $post_author != $user_id && $anonymously_user != $user_id) {
							$yes_new = 1;
						}
						if ($is_super_admin) {
							$yes_new = 0;
						}
					}else {
						$yes_new = 0;
					}
				}else {
					if (isset($question_category[0]) && $category_new == mobile_api_checkbox_value) {
						if (isset($post_author) && $user_id > 0 && (($post_author == $user_id) || ($anonymously_user == $user_id))) {
							$yes_new = 1;
						}
						if ($this->id && $user_id > 0 && (($post_author == $user_id) || ($anonymously_user == $user_id))) {
							$yes_new = 1;
						}
					}else if (isset($question_category[0]) && $category_new != "on") {
						$yes_new = 0;
					}
					
					if (empty($question_category[0]) || $is_super_admin) {
						$yes_new = 0;
					}
				}
			}

			$activate_login = mobile_api_options("activate_login");
			$no_allow_to_answer = true;
			if (($post_type != mobile_api_questions_type && $post_type != mobile_api_asked_questions_type) || (($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) && $yes_new != 1)) {
				if (($post_type == 'post' && ($is_super_admin || $custom_permission != mobile_api_checkbox_value || (is_user_logged_in() && $custom_permission == mobile_api_checkbox_value && isset($roles["add_comment"]) && $roles["add_comment"] == 1)) || (!is_user_logged_in() && $add_comment == mobile_api_checkbox_value)) || (($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) && ($is_super_admin || $custom_permission != mobile_api_checkbox_value || (is_user_logged_in() && $custom_permission == mobile_api_checkbox_value && isset($roles["add_answer"]) && $roles["add_answer"] == 1) || (!is_user_logged_in() && $add_answer == mobile_api_checkbox_value)))) {
					if ($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) {
						$yes_special = 1;
						if (have_comments()) {
							$yes_special = 0;
						}else {
							if ($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) {
								if (isset($question_category[0]) && $category_special == mobile_api_checkbox_value) {
									if (isset($post_author) && $user_id > 0 && (($post_author == $user_id) || ($anonymously_user == $user_id))) {
										$yes_special = 1;
									}
								}else if (isset($question_category[0]) && $category_special != "on") {
									$yes_special = 0;
								}
								
								if (!isset($question_category[0]) || $is_super_admin) {
									$yes_special = 0;
								}
							}
						}
					}
					if (($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) && $yes_special == 1) {
						$no_allow_to_answer = false;
						$cantanswer = esc_html__("Sorry this question is a special, The admin must answer first.","mobile-api");
					}else {
						$answer_per_question = mobile_api_options("answer_per_question");
						if ($answer_per_question == mobile_api_checkbox_value && !$is_super_admin && $user_id > 0) {
							$answers_question = get_comments(array('post_id' => $this->id,'user_id' => $user_id,'parent' => 0));
						}
						if (($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) && !$is_super_admin && $answer_per_question == mobile_api_checkbox_value && $user_id > 0 && isset($answers_question) && is_array($answers_question) && !empty($answers_question)) {
			                $no_allow_to_answer = false;
							$cantanswer = esc_html__("You have already answered this question.","mobile-api");
						}
					}
				}else {
					$no_allow_to_answer = false;
					if ($activate_login != 'disabled' && !is_user_logged_in()) {
						$cantanswer = esc_html__("You must login to add an answer.","mobile-api");
						$mustlogin = true;
					}else {
						$cantanswer = esc_html__("Sorry, you do not have permission to answer to this question.","mobile-api");
					}
				}
			}else {
				$no_allow_to_answer = false;
				$cantanswer = esc_html__("Sorry, you do not have permission to answer to this question.","mobile-api");
			}

			if ($no_allow_to_answer == true) {
				if ($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) {
					$closed_question = get_post_meta($this->id,"closed_question",true);
				}
				$closed_post = apply_filters(mobile_api_action_prefix()."_closed_post",false,$post);

				$pay_answer = mobile_api_options("pay_answer");
				$custom_pay_answer = get_post_meta($this->id,"custom_pay_answer",true);
				if ($custom_pay_answer == mobile_api_checkbox_value) {
					$pay_answer = get_post_meta($this->id,"pay_answer",true);
				}
				$pay_answer = apply_filters(mobile_api_action_prefix().'_pay_answer',$pay_answer);
				if (isset($closed_post) && $closed_post == 1) {
					$cantanswer = apply_filters(mobile_api_action_prefix()."_closed_post_text",false);
				}else if (($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) && isset($closed_question) && ($closed_question == 1 || $closed_question == "on")) {
					$cantanswer = esc_html__("Sorry this question is closed.","mobile-api");
				}else if (($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) && $pay_answer == mobile_api_checkbox_value && !is_user_logged_in()) {
					if ($activate_login != 'disabled') {
						$cantanswer = esc_html__("You must login to add an answer.","mobile-api");
					}else {
						$cantanswer = esc_html__("Sorry, you do not have permission to answer to this question.","mobile-api");
					}
				}
			}
		}
		if ($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) {
			$private_question = false;
			if (isset($cantanswer) && $cantanswer != "") {
				$this->set_value('cantAddAnswer', $cantanswer);
			}
			if (isset($mustlogin)) {
				$this->set_value('noAnswersMessageRedirect', true);
			}
			$show_answer = mobile_api_options("show_answer");
			if ($is_super_admin || $custom_permission != mobile_api_checkbox_value || (is_user_logged_in() && $custom_permission == mobile_api_checkbox_value && isset($roles["show_answer"]) && $roles["show_answer"] == 1) || (!is_user_logged_in() && $show_answer == mobile_api_checkbox_value)) {
			}else {
				$this->set_value('noAnswersMessage', esc_html__("Sorry, you do not have permission to view answers.","mobile-api"));
			}
			if ($custom_permission == mobile_api_checkbox_value && !is_user_logged_in() && $show_answer != mobile_api_checkbox_value) {
				$this->set_value('cantAnswerMessage', true); // Please login to see the answer.
			}
			$yes_private = mobile_api_private_post($this->id,$post_author,$user_id,$post_type);
			if ($yes_private != 1) {
				$show_custom_fields = false;
				$private_question = true;
				$this->set_value('private_question', true);
				$this->set_value('share', '');
				$this->set_value('slug', '');
				$this->set_value('url', '');
				$this->set_value('title', '');
				$this->set_value('title_plain', '');
				$this->set_value('content', '');
				$this->set_value('excerpt', '');
				$this->set_value('date', '');
				$this->set_value('modified', '');
			}
			if ($private_question != true) {
				$polled = false;
				$askme_question_poll = get_post_meta($this->id,"askme_question_poll",true);
				if (!is_user_logged_in()) {
					$poll_mobile = get_post_meta($this->id,mobile_api_question_poll,true);
					$poll_mobile = (isset($poll_mobile) && is_array($poll_mobile) && !empty($poll_mobile)?$poll_mobile:array());
					$device_token = esc_html($mobile_api->query->device_token);
				}else {
					$question_poll = get_post_meta($this->id,mobile_api_question_poll(),true);
					if (empty($question_poll) || !is_array($question_poll)) {
						update_post_meta($this->id,mobile_api_question_poll(),array());
					}
					$question_poll = (isset($question_poll) && is_array($question_poll) && !empty($question_poll)?$question_poll:array());
				}
				if ((is_user_logged_in() && isset($question_poll) && is_array($question_poll) && in_array($user_id,$question_poll)) || (!is_user_logged_in() && isset($poll_mobile) && is_array($poll_mobile) && in_array($device_token,$poll_mobile))) {
					$polled = true;
				}
				if (has_askme()) {
					$user_login_id = get_user_by("id",$user_id);
					$_favorites = get_user_meta($user_id,$user_login_id->user_login."_favorites",true);
				}else {
					$_favorites = get_user_meta($user_id,$user_id."_favorites",true);
				}
				$this->set_value('favorite',(is_array($_favorites) && in_array($this->id,$_favorites)?true:false));
				$this->set_value('polled',$polled);
				$this->set_tax_cats_value('','',mobile_api_question_categories);
				$this->set_tax_tags_value('','',mobile_api_question_tags);
			}
		}else if ($post_type == mobile_api_knowledgebase_type) {
			$private_knowledgebase = false;
			$yes_private = mobile_api_private_post($this->id,$post_author,$user_id,$post_type);
			if ($yes_private != 1) {
				$show_custom_fields = false;
				$private_knowledgebase = true;
				$this->set_value('private_knowledgebase', true);
				$this->set_value('share', '');
				$this->set_value('slug', '');
				$this->set_value('url', '');
				$this->set_value('title', '');
				$this->set_value('title_plain', '');
				$this->set_value('content', esc_html__("Please login to see the content.","mobile-api"));
				$this->set_value('excerpt', '');
				$this->set_value('date', '');
				$this->set_value('modified', '');
			}
			if ($private_knowledgebase != true) {
				$polled = false;
				$this->set_tax_cats_value('','',mobile_api_knowledgebase_categories);
				$this->set_tax_tags_value('','',mobile_api_knowledgebase_tags);
			}
		}else {
			$show_comment = mobile_api_options("show_comment");
			if ($is_super_admin || $custom_permission != mobile_api_checkbox_value || (is_user_logged_in() && $custom_permission == mobile_api_checkbox_value && isset($roles["show_comment"]) && $roles["show_comment"] == 1) || (!is_user_logged_in() && $show_comment == mobile_api_checkbox_value)) {
			}else {
				$this->set_value('noAnswersMessage', esc_html__("Sorry, you do not have permission to view comments.","mobile-api"));
			}
			if ($custom_permission == mobile_api_checkbox_value && !is_user_logged_in() && $show_comment != mobile_api_checkbox_value) {
				$this->set_value('cantAnswerMessage', true); // Please login to see the comment.
			}
			$this->set_tax_cats_value('','','category');
			$this->set_tax_tags_value('','','post_tag');
		}
		$this->set_value('comment_status', $wp_post->comment_status);
		if ($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type || $post_type == "post") {
			if ($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) {
				$activate_related_posts = mobile_api_options("app_related_questions");
			}else {
				$activate_related_posts = mobile_api_options("app_related_posts");
			}
			if ($activate_related_posts == mobile_api_checkbox_value && $single == 'single') {
				$this->set_related_posts();
			}
		}
		if ($show_custom_fields == true) {
			$this->set_thumbnail_value();
			$this->set_custom_fields_value($edit_post);
		}
		if ($post_type != mobile_api_questions_type && $post_type != mobile_api_asked_questions_type) {
			$this->set_custom_taxonomies($post_type);
		}
		$this->attachments = array();
		do_action("mobile_api_import_wp_post", $this, $wp_post);
	}
	
	function set_value($key, $value) {
		global $mobile_api;
		if ($mobile_api->include_value($key)) {
			$this->$key = $value;
		} else {
			unset($this->$key);
		}
	}
		
	function set_related_posts() {
		global $mobile_api, $post;
		$date_format = mobile_api_options("date_format");
		$date_format = ($date_format != ""?$date_format:get_option("date_format"));
		$format_date_ago = mobile_api_options("format_date_ago");
		$format_date_ago_types = mobile_api_options("format_date_ago_types");
		if ($format_date_ago == mobile_api_checkbox_value && (($post_type == "post" && isset($format_date_ago_types["posts"]) && $format_date_ago_types["posts"] == "posts") || ($post_type == mobile_api_knowledgebase_type && isset($format_date_ago_types["knowledgebases"]) && $format_date_ago_types["knowledgebases"] == "knowledgebases") || ((($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type)) && isset($format_date_ago_types["questions"]) && $format_date_ago_types["questions"] == "questions"))) {
			$time_string = human_time_diff(get_the_time('U',$post->id),current_time('timestamp'))." ".esc_html__("ago","mobile-api");
			$modified_string = human_time_diff(strtotime($post->post_modified),current_time('timestamp'))." ".esc_html__("ago","mobile-api");
		}else {
			$time_string = esc_html(get_the_time($date_format,$post->ID));
			$modified_string = date($date_format, strtotime($post->post_modified));
		}
		if ($post->post_type == mobile_api_questions_type || $post->post_type == mobile_api_asked_questions_type) {
			$related_style = mobile_api_options("app_related_style_questions");
			$related_number = mobile_api_options("app_related_number_questions");
			$query_related = mobile_api_options("app_query_related_questions");
		}else {
			$related_style = mobile_api_options("app_related_style");
			$related_number = mobile_api_options("app_related_number");
			$query_related = mobile_api_options("app_query_related");
		}
		if ($query_related == "tags" && $post->post_type != mobile_api_asked_questions_type) {
			if ($post->post_type == mobile_api_questions_type) {
				$term_list = wp_get_post_terms($this->id, mobile_api_question_tags, array('fields' => 'ids'));
				$related_query_ = array('tax_query' => array(array('taxonomy' => mobile_api_question_tags,'field' => 'id','terms' => $term_list,'operator' => 'IN')));
			}else {
				$term_list = wp_get_post_terms($this->id, 'post_tag', array("fields" => "ids"));
				$related_query_ = array('tag__in' => $term_list);
			}
		}else if ($query_related == "author" || $post->post_type == mobile_api_asked_questions_type) {
			$related_query_ = array('author' => $post->post_author);
		}else {
			if ($post->post_type == mobile_api_questions_type) {
				$categories = wp_get_post_terms($this->id,mobile_api_question_categories,array('fields' => 'ids'));
				$related_query_ = array('tax_query' => array(array('taxonomy' => mobile_api_question_categories,'field' => 'id','terms' => $categories,'operator' => 'IN')));
			}else {
				$categories = get_the_category($this->id);
				$category_ids = array();
				foreach ($categories as $l_category) {
					$category_ids[] = $l_category->term_id;
				}
				$related_query_ = array('category__in' => $category_ids);
			}
		}

		$meta_query = array();
		if ($post->post_type == mobile_api_questions_type || $post->post_type == mobile_api_asked_questions_type) {
			$meta_query = array(
				array("meta_query" => array(
						'relation' => 'OR',
						array("key" => "private_question", "compare" => "NOT EXISTS"),
						array("key" => "private_question", "compare" => "=", "value" => 0),
					)
				)
			);
		}
		if ($post->post_type == mobile_api_knowledgebase_type) {
			$meta_query = array(
				array("meta_query" => array(
						'relation' => 'OR',
						array("key" => "private_knowledgebase", "compare" => "NOT EXISTS"),
						array("key" => "private_knowledgebase", "compare" => "=", "value" => 0),
					)
				)
			);
		}
		
		$block_users = mobile_api_options("block_users");
		$author__not_in = array();
		if ($block_users == mobile_api_checkbox_value) {
			$user_id = get_current_user_id();
			if ($user_id > 0) {
				$get_block_users = get_user_meta($user_id,mobile_api_action_prefix()."_block_users",true);
				if (is_array($get_block_users) && !empty($get_block_users)) {
					$author__not_in = array("author__not_in" => $get_block_users);
				}
			}
		}

		$args = array_merge($meta_query,$related_query_,$author__not_in,array('post_type' => $post->post_type,'post__not_in' => array($this->id),'posts_per_page'=> $related_number,'cache_results' => false,'no_found_rows' => true));
		$related_query = new WP_Query($args);
		$count_total = count($related_query->posts);

		if (($query_related == "tags" || $query_related == "author") && $post->post_type != mobile_api_asked_questions_type && !$related_query->have_posts()) {
			if ($post->post_type == mobile_api_questions_type) {
				$categories = wp_get_post_terms($this->id,mobile_api_question_categories,array('fields' => 'ids'));
				$related_query_ = array('tax_query' => array(array('taxonomy' => mobile_api_question_categories,'field' => 'id','terms' => $categories,'operator' => 'IN')));
			}else {
				$categories = get_the_category($this->id);
				$category_ids = array();
				foreach ($categories as $l_category) {
					$category_ids[] = $l_category->term_id;
				}
				$related_query_ = array('category__in' => $category_ids);
			}
			$args = array_merge($meta_query,$related_query_,$author__not_in,array('post__not_in' => array($this->id),'posts_per_page'=> $related_number,'cache_results' => false,'no_found_rows' => true));
			$related_query = new WP_Query($args);
		}
		$related_posts = array(
			"style" => $related_style,
			"count" => $count_total,
		    "count_total" => $count_total,
		    "posts" => array()
		);

		if ($related_query->have_posts()) {
			while ( $related_query->have_posts() ) : $related_query->the_post();
				if ($related_style == "with_images") {
					$attachment_id = get_post_thumbnail_id($post->ID);
					$thumbnail_size = $this->get_thumbnail_size();
					$attachment = $mobile_api->introspector->get_attachment($attachment_id);
					$image = $attachment->images[$thumbnail_size];
					$image_url = $image->url;
					if ($image_url != "") {
						$last_image = $image_url;
					}else {
						$image_url = wp_get_attachment_image_src($attachment_id,$thumbnail_size);
						$last_image = $image_url[0];
					}
				}
				$thumbnail_value = (isset($last_image) && $last_image != ""?$last_image:"");
				if ($post->post_type == mobile_api_questions_type || $post->post_type == mobile_api_asked_questions_type) {
					$categories = $this->set_tax_cats_value('return',$post->ID,mobile_api_question_categories);
					$tags = $this->set_tax_tags_value('return',$post->ID,mobile_api_question_tags);
				}else if ($post->post_type == mobile_api_knowledgebase_type) {
					$categories = $this->set_tax_cats_value('return',$post->ID,mobile_api_knowledgebase_categories);
					$tags = $this->set_tax_tags_value('return',$post->ID,mobile_api_knowledgebase_tags);
				}else {
					$categories = $this->set_tax_cats_value('return',$post->ID,'category');
					$tags = $this->set_tax_tags_value('return',$post->ID,'post_tag');
				}
				$get_permalink = get_permalink($post->ID);
				$comments = mobile_api_count_comments($post->id);
				$last_comments = (int)($comments > 0?$comments:$post->comment_count);
				$post_from_front = get_post_meta($post->ID,'post_from_front',true);
				$related_posts["posts"][] = array(
					"id" => $post->ID,
					"title_plain" => strip_tags($post->post_title),
					"content" => $this->set_content_value('return',$post->ID,do_blocks($post->post_content),$post_from_front),
					"share" => $get_permalink,
					"date" => $time_string,
					"modified" => $modified_string,
					"categories" => $categories,
					"tags" => $tags,
					"thumbnail" => $thumbnail_value,
					"comment_count" => $last_comments,
				);
			endwhile;
		}
		$this->related_posts = $related_posts;
	}
	
	function set_content_value($retrun = '',$id = '',$content = '',$post_from_front = '',$edit_post = '') {
		global $mobile_api;
		if ($mobile_api->include_value('content')) {
			$content = ($content != ''?$content:get_the_content(esc_html__('Read more','mobile-api')));
			$content = mobile_api_kses_stip($content,$post_from_front,$edit_post);
			$content = mobile_api_preg_replace($content);
			$content = make_clickable($content);
			if ($retrun == 'return') {
				return $content;
			}else {
				$this->content = $content;
			}
		}else {
			if ($retrun == '') {
				unset($this->content);
			}
		}
	}
	
	function set_tax_cats_value($retrun = '',$id = '',$tax = '') {
		global $mobile_api;
		if ($mobile_api->include_value('categories')) {
			if ($retrun == 'return') {
				$categories = array();
			}else {
				$this->categories = array();
			}
			if ($wp_categories = get_the_terms(($id != ''?$id:$this->id),$tax)) {
				foreach ($wp_categories as $wp_category) {
					$category = new MOBILE_API_Category($wp_category);
					if ($retrun == 'return') {
						$categories[] = $category;
					}else {
						$this->categories[] = $category;
					}
				}
			}
			return $categories;
		} else {
			if ($retrun == '') {
				unset($this->categories);
			}
		}
	}
	
	function set_tax_tags_value($retrun = '',$id = '',$tax = '') {
		global $mobile_api;
		if ($mobile_api->include_value('tags')) {
			if ($retrun == 'return') {
				$tags = array();
			}else {
				$this->tags = array();
			}
			if ($wp_tags = wp_get_object_terms( ($id != ''?$id:$this->id), $tax )) {
				foreach ($wp_tags as $wp_tag) {
					if ($retrun == 'return') {
						$tags[] = new MOBILE_API_Tag($wp_tag);
					}else {
						$this->tags[] = new MOBILE_API_Tag($wp_tag);
					}
				}
			}
			return $tags;
		} else {
			if ($retrun == '') {
				unset($this->tags);
			}
		}
	}
	
	function set_author_value($author_id,$type,$post) {
		global $mobile_api;
		if ($mobile_api->include_value('author')) {
			$this->author = new MOBILE_API_Author($author_id,"post",$post);
		} else {
			unset($this->author);
		}
	}
	
	function set_comments_value() {
		global $mobile_api;
		if ($mobile_api->include_value('comments')) {
			$this->comments = $mobile_api->introspector->get_comments($this->id);
		} else {
			unset($this->comments);
		}
	}
	
	function set_attachments_value() {
		global $mobile_api;
		if ($mobile_api->include_value('attachments')) {
			$this->attachments = $mobile_api->introspector->get_attachments($this->id);
		} else {
			unset($this->attachments);
		}
	}
	
	function set_thumbnail_value($id = '') {
		global $mobile_api;
		if (!$mobile_api->include_value('thumbnail') || !function_exists('get_post_thumbnail_id')) {
			unset($this->thumbnail);
			return;
		}
		$attachment_id = get_post_thumbnail_id(($id != ''?$id:$this->id));
		if (!$attachment_id) {
			unset($this->thumbnail);
			return;
		}
		$thumbnail_size = $this->get_thumbnail_size();
		$this->thumbnail_size = $thumbnail_size;
		$attachment = $mobile_api->introspector->get_attachment($attachment_id);
		$image = $attachment->images[$thumbnail_size];
		$image_url = $image->url;
		if ($image_url != "") {
			$last_image = $image_url;
		}else {
			$image_url = wp_get_attachment_image_src($attachment_id,$thumbnail_size);
			$last_image = $image_url[0];
		}
		if (!isset($last_image)) {
			unset($this->thumbnail);
			return;
		}
		$this->thumbnail = $last_image;
		if (is_array($attachment->images) && !empty($attachment->images)) {
			$this->thumbnail_images = $attachment->images;
		}else if ($this->thumbnail != "") {
			$this->thumbnail_images = array("full" => array("url" => $this->thumbnail));
		}
	}
	
	function set_custom_fields_value($edit_post = '') {
		global $mobile_api;
		if ($mobile_api->include_value('custom_fields')) {
			$wp_custom_fields = get_post_meta($this->id,'',true);
			$this->custom_fields = new stdClass();
			if ($mobile_api->query->custom_fields) {
				$keys = explode(',', $mobile_api->query->custom_fields);
			}
			$activate_male_female = apply_filters("mobile_api_activate_male_female",false);
			if ($activate_male_female == true) {
				$info_edited = '';
				$gender_author = ($post_author > 0?get_user_meta($post_author,'gender',true):"");
				$gender_post = get_post_meta($question_id,'wpqa_post_gender',true);
				if ($gender_author != "" && $gender_author != $gender_post) {
					$info_edited = esc_html__("Some of main user info (Ex: name or gender) has been edited since this post has been added","mobile-api");
					if (isset($wp_custom_fields["wpqa_post_type"])) {
						$post_type = array_map('maybe_unserialize',$wp_custom_fields["wpqa_post_type"]);
						$post_type = (isset($post_type[0])?$post_type[0]:$post_type);
					}
					if (!isset($post_type)) {
						$get_post = get_post($this->id);
						$post_type = $get_post->post_type;
					}
					if ($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) {
						$info_edited = esc_html__("Some of main user info (Ex: name or gender) has been edited since this question has been asked","mobile-api");
					}else if ($post_type == "posts") {
						$info_edited = esc_html__("Some of main user info (Ex: name or gender) has been edited since this group post has been added","mobile-api");
					}else if ($post_type == "message") {
						$info_edited = esc_html__("Some of main user info (Ex: name or gender) has been edited since this message has been sent","mobile-api");
					}
					if ($info_edited != "") {
						$this->custom_fields->infoEdited = $info_edited;
					}
				}
			}
			foreach ($wp_custom_fields as $key => $value) {
				if ($mobile_api->query->custom_fields) {
					if (in_array($key, $keys)) {
						$this->custom_fields->$key = $wp_custom_fields[$key];
					}
				}else if (substr($key, 0, 1) != '_') {
					if ($key == mobile_api_meta_prefix.'contact_map') {
						$this->custom_fields->$key = '';
					}else if ($key == "what_post" || $key == mobile_api_theme_prefix()."_what_post") {
						$mobile_api_theme_prefix = mobile_api_theme_prefix();
						if ($value[0] == "video") {
							$key = "video_description";
							$this->custom_fields->$key[] = "on";
							$key = "video_type";
							$this->custom_fields->$key[] = $wp_custom_fields[$mobile_api_theme_prefix."_video_post_type"][0];
							$key = "video_id";
							$this->custom_fields->$key[] = $wp_custom_fields[$mobile_api_theme_prefix."_video_post_id"][0];
						}else if ($value[0] == "slideshow") {
							if ($wp_custom_fields[$mobile_api_theme_prefix."_slideshow_type"][0] == "upload_images") {
								$slideshow_images = array();
								$slideshow_images_full = array();
								$upload_images = unserialize($wp_custom_fields[$mobile_api_theme_prefix."_upload_images"][0]);
								if (is_array($upload_images) && !empty($upload_images)) {
									foreach ($upload_images as $att) {
							    	    $src = wp_get_attachment_image_src($att,'full');
							    	    if (isset($src[0]) && $src[0] != "") {
							    	    	$slideshow_images[] = mobile_api_resize_by_url(esc_url($src[0]),700,250);
							    	    	$slideshow_images_full[] = esc_url($src[0]);
							    	    }
							    	}
							    }
							}else if ($wp_custom_fields[$mobile_api_theme_prefix."_slideshow_type"][0] == "custom_slide") {
								$slideshow_images = array();
								$slideshow_images_full = array();
								$upload_images = unserialize($wp_custom_fields[$mobile_api_theme_prefix."_slideshow_post"][0]);
								if (is_array($upload_images) && !empty($upload_images)) {
									foreach ($upload_images as $key_slide => $value_slide) {
						    			if (isset($value_slide['image_url']['id']) && (int)$value_slide['image_url']['id'] != "") {
							    		    $src = wp_get_attachment_image_src($value_slide['image_url']['id'],'full');
							    		    if (isset($src[0]) && $src[0] != "") {
								    	    	$slideshow_images[] = mobile_api_resize_by_url(esc_url($src[0]),700,250);
							    	    		$slideshow_images_full[] = esc_url($src[0]);
								    	    }
							    		}
						    		}
						    	}
							}
							if (isset($slideshow_images) && is_array($slideshow_images) && !empty($slideshow_images)) {
								$key = "slideshow_images";
								$this->custom_fields->$key = $slideshow_images;
							}
							if (isset($slideshow_images_full) && is_array($slideshow_images_full) && !empty($slideshow_images_full)) {
								$key = "slideshow_images_full";
								$this->custom_fields->$key = $slideshow_images_full;
							}
						}
					}else if ($key == "ask") {
						if (isset($wp_custom_fields["question_poll"])) {
							$question_poll = array_map('maybe_unserialize',$wp_custom_fields["question_poll"]);
							$question_poll = (isset($question_poll[0])?$question_poll[0]:$question_poll);
						}
						if ($question_poll == 1 || $question_poll == mobile_api_checkbox_value) {
							$asks = array_map('maybe_unserialize',$value);
							$asks = (isset($asks[0])?$asks[0]:$asks);
							if (is_array($asks) && !empty($asks)) {
								foreach ($asks as $key_poll => $value_poll) {
									$image = (($wp_custom_fields["question_image_poll"][0] == "on" || $wp_custom_fields["question_image_poll"][0] == 1) && (isset($value_poll["image"]) && isset($value_poll["image"]["url"]))?array("image" => $value_poll["image"]["url"]):array());
									$this->custom_fields->$key[] = array_merge(array("title" => (isset($value_poll["title"]) && $value_poll["title"] != ""?htmlspecialchars_decode($value_poll["title"]):""),"id" => (string)$value_poll["id"]),$image);
								}
							}
						}
					}else if ($key == mobile_api_poll()) {
						if (isset($wp_custom_fields["ask"])) {
							$asks = array_map('maybe_unserialize',$wp_custom_fields["ask"]);
							$asks = (isset($asks[0])?$asks[0]:$asks);
						}
						$poll_question = array_map('maybe_unserialize',$value);
						$poll_question = (isset($poll_question[0])?$poll_question[0]:$poll_question);
						if (isset($asks) && is_array($asks)) {
							$key_k = 0;
							foreach ($asks as $key_ask => $value_ask) {
								$key_k++;
								$sort_polls[$key_k]["id"] = (string)$asks[$key_ask]["id"];
								$sort_polls[$key_k]["title"] = (isset($value_ask["title"])?htmlspecialchars_decode($value_ask["title"]):"");
								$sort_polls[$key_k]["value"] = (isset($asks[$key_ask]["value"]) && $asks[$key_ask]["value"] != ""?$asks[$key_ask]["value"]:(isset($poll_question[$key_ask]["value"])?$poll_question[$key_ask]["value"]:0));
								$sort_polls[$key_k]["user_ids"] = (isset($asks[$key_ask]["user_ids"])?$asks[$key_ask]["user_ids"]:(isset($poll_question[$key_ask]["user_ids"])?$poll_question[$key_ask]["user_ids"]:array()));
							}
						}
						$key = "wpqa_poll";
						if (isset($sort_polls) && is_array($sort_polls) && !empty($sort_polls) && isset($wp_custom_fields["question_poll"]) && isset($wp_custom_fields["question_poll"][0]) && ($wp_custom_fields["question_poll"][0] == "on" || $wp_custom_fields["question_poll"][0] == 1)) {
							foreach ($sort_polls as $key_poll => $value_poll) {
								$this->custom_fields->$key[] = array("title" => htmlspecialchars_decode($value_poll["title"]),"id" => $value_poll["id"],"value" => $value_poll["value"],"user_ids" => $value_poll["user_ids"]);
							}
						}
					}else if ($key == mobile_api_question_poll()) {
						$question_poll = array_map('maybe_unserialize',$value);
						$question_poll = (isset($question_poll[0])?$question_poll[0]:$question_poll);
						$key = "wpqa_question_poll";
						$this->custom_fields->$key = $question_poll;
					}else if ($key == "favorites_questions") {
						$favorites_questions = array_map('maybe_unserialize',$value);
						$favorites_questions = (isset($favorites_questions[0])?$favorites_questions[0]:$favorites_questions);
						if (is_array($favorites_questions) && !empty($favorites_questions)) {
							$this->custom_fields->$key = count($favorites_questions);
						}else {
							$this->custom_fields->$key = 0;
						}
					}else if ($key == "following_questions") {
						$following_questions = array_map('maybe_unserialize',$value);
						$following_questions = (isset($following_questions[0])?$following_questions[0]:$following_questions);
						if (is_array($following_questions) && !empty($following_questions)) {
							$following_questions = (is_array($following_questions) && !empty($following_questions)?get_users(array('fields' => 'ID','include' => $following_questions,'orderby' => 'registered')):array());
							$this->custom_fields->$key = count($following_questions);
						}else {
							$this->custom_fields->$key = 0;
						}
					}else if ($key == "attachment_m") {
						$attachment_m = array_map('maybe_unserialize',$value);
						$attachment_m = (isset($attachment_m[0])?$attachment_m[0]:$attachment_m);
						if (is_array($attachment_m) && !empty($attachment_m)) {
							foreach ($attachment_m as $key_attachment => $value_attachment) {
								if (isset($value_attachment["added_file"]) && $value_attachment["added_file"] != "") {
									$type = get_post_mime_type($value_attachment["added_file"]);
									$link = wp_get_attachment_url($value_attachment["added_file"]);
									if ($type != "" && $link != "") {
										$attachment_array[] = array(
											"type" => $type,
											"link" => $link,
										);
									}
								}
							}
						}
						if (is_array($attachment_array) && !empty($attachment_array)) {
							$this->custom_fields->$key = $attachment_array;
						}
					}else if ($key == "the_best_answer") {
						$this->custom_fields->$key = true;
					}else if ($key == "sticky") {
						$this->custom_fields->$key = $wp_custom_fields[$key];
					}else if ($key == "group_image" || $key == "group_cover") {
						$group_image = array_map('maybe_unserialize',$value);
						$group_image = (isset($group_image[0])?$group_image[0]:$group_image);
						if ((is_array($group_image) && isset($group_image["id"])) || (!is_array($group_image) && $group_image != "")) {
							$group_image = (is_array($group_image) && isset($group_image["id"])?$group_image["id"]:$group_image);
							$get_attachment_image = wp_get_attachment_url($group_image);
							$this->custom_fields->$key = ($get_attachment_image != ""?$get_attachment_image:"");
							// $this->custom_fields->$key = mobile_api_get_aq_resize_img_url(700,250,"",$group_image);
							$get_post_g = get_post($this->id);
							if ($get_post_g->post_type == "group") {
								$user_id = get_current_user_id();
								$is_super_admin = is_super_admin($user_id);
								$group_moderators = get_post_meta($this->id,"group_moderators",true);
								if ($user_id > 0 && isset($group_moderators) && is_array($group_moderators) && in_array($user_id,$group_moderators)) {
									$allow_group_moderators = true;
								}

								if ($is_super_admin || ($group_edit == mobile_api_checkbox_value && $get_post_g->post_author == $user_id && $user_id > 0) || isset($allow_group_moderators)) {
									$this->custom_fields->delete_images[$key]["api"] = mobile_api_delete.'?type=post_meta&name='.$key.'&attach_id='.$group_image.'&id='.$this->id;
									$this->custom_fields->delete_images[$key]["alert"] = esc_html__('Are you sure you want to delete the image?','mobile-api');
								}
							}
						}else {
							$this->custom_fields->$key = "";
						}
					}else if ($key == "group_privacy") {
						$group_privacy = array_map('maybe_unserialize',$value);
						$group_privacy = (isset($group_privacy[0])?$group_privacy[0]:$group_privacy);
						$this->custom_fields->$key = array($group_privacy,($group_privacy == "public"?esc_html__("Public group","mobile-api"):esc_html__("Private group","mobile-api")));
					}else if ($key == "group_invitation") {
						$group_invitation = array_map('maybe_unserialize',$value);
						$group_invitation = (isset($group_invitation[0])?$group_invitation[0]:$group_invitation);
						$this->custom_fields->$key = array($group_invitation,($group_invitation == "all"?esc_html__("All group members","mobile-api"):($group_invitation == "admin_moderators"?esc_html__("Admin and moderators","mobile-api"):esc_html__("Admin only","mobile-api"))));
					}else if ($key == "group_allow_posts") {
						$group_allow_posts = array_map('maybe_unserialize',$value);
						$group_allow_posts = (isset($group_allow_posts[0])?$group_allow_posts[0]:$group_allow_posts);
						$this->custom_fields->$key = array($group_allow_posts,($group_allow_posts == "all"?esc_html__("All group members","mobile-api"):($group_allow_posts == "admin_moderators"?esc_html__("Admin and moderators","mobile-api"):esc_html__("Admin only","mobile-api"))));
					}else if ($key == "group_approval" || $key == "group_comments" || $key == "group_posts" || $key == "group_users") {
						$group_key = array_map('maybe_unserialize',$value);
						$group_key = (isset($group_key[0])?$group_key[0]:$group_key);
						$this->custom_fields->$key = $group_key;
					}else if ($key == "group_users_array" || $key == "group_moderators" || $key == "sticky_posts") {
						$group_key = array_map('maybe_unserialize',$value);
						$group_key = (isset($group_key[0])?$group_key[0]:$group_key);
						$this->custom_fields->$key = (is_array($group_key)?array_values($group_key):$group_key);
					}else if ($key == "group_rules") {
						$group_rules = array_map('maybe_unserialize',$value);
						$group_rules = (isset($group_rules[0])?$group_rules[0]:$group_rules);
						$this->custom_fields->$key = mobile_api_kses_stip($group_rules,$edit_post);
					}else {
						$this->custom_fields->$key = $wp_custom_fields[$key];
					}
				}
			}
			if (isset($wp_custom_fields["comment_count"])) {
				$comment_count = array_map('maybe_unserialize',$wp_custom_fields["comment_count"]);
				$comment_count = (isset($comment_count[0])?$comment_count[0]:$comment_count);
			}
			$comment_count = (isset($comment_count)?$comment_count:0);
			$comments = mobile_api_count_comments($this->id);
			$last_comments = ($comments > 0?array("{$comments}"):(is_array($comment_count)?$comment_count:array("{$comment_count}")));
			$key = "count_post_all";
			$this->custom_fields->$key = $last_comments;
			$key = "comment_count";
			$this->custom_fields->$key = $last_comments;
			if ($activate_male_female == true) {
				$count_comment_only = mobile_api_options("count_comment_only");
				//$meta = ($count_comment_only == mobile_api_checkbox_value?"male_count_comments":"male_comment_count");
				$male_comment_count = (int)mobile_api_count_comments($this->id,"male_count_comments","like_meta");

				//$meta = ($count_comment_only == mobile_api_checkbox_value?"female_count_comments":"female_comment_count");
				$female_comment_count = (int)mobile_api_count_comments($this->id,"female_count_comments","like_meta");

				//$meta = ($count_comment_only == mobile_api_checkbox_value?"other_count_comments":"other_comment_count");
				$other_comment_count = (int)mobile_api_count_comments($this->id,"other_count_comments","like_meta");

				$count_post_comments = (int)mobile_api_count_comments($this->id,"count_post_comments","like_meta");
				$other_gender_comments = ($count_post_comments-($male_comment_count+$female_comment_count));
			}
			$key = "male_comment_count";
			$this->custom_fields->$key = (isset($male_comment_count)?$male_comment_count:0);
			$key = "female_comment_count";
			$this->custom_fields->$key = (isset($female_comment_count)?$female_comment_count:0);
			$key = "other_comment_count";
			$this->custom_fields->$key = (isset($other_comment_count)?$other_comment_count:0);
			$key = "other_gender_comments";
			$this->custom_fields->$key = (isset($other_gender_comments) && $other_gender_comments > 0?$other_gender_comments:0);
			$key = "question_vote";
			$this->custom_fields->$key = array(isset($wp_custom_fields[$key]) && isset($wp_custom_fields[$key][0])?$wp_custom_fields[$key][0]:"0");
			$key = "post_stats";
			$post_stats = mobile_api_post_stats();
			$this->custom_fields->$key = array(isset($wp_custom_fields[$post_stats]) && isset($wp_custom_fields[$post_stats][0])?$wp_custom_fields[$post_stats][0]:"0");
		}else {
			unset($this->custom_fields);
		}
	}
	
	function set_custom_taxonomies($type) {
		global $mobile_api;
		$taxonomies = get_taxonomies(array(
			'object_type' => array($type),
			'public'   => true,
			'_builtin' => false
		), 'objects');
		foreach ($taxonomies as $taxonomy_id => $taxonomy) {
			$taxonomy_key = "taxonomy_$taxonomy_id";
			if (!$mobile_api->include_value($taxonomy_key)) {
				continue;
			}
			$taxonomy_class = $taxonomy->hierarchical ? 'MOBILE_API_Category' : 'MOBILE_API_Tag';
			$terms = get_the_terms($this->id, $taxonomy_id);
			$this->$taxonomy_key = array();
			if (!empty($terms)) {
				$taxonomy_terms = array();
				foreach ($terms as $term) {
					$taxonomy_terms[] = new $taxonomy_class($term);
				}
				$this->$taxonomy_key = $taxonomy_terms;
			}
		}
	}
	
	function get_thumbnail_size() {
		global $mobile_api;
		if ($mobile_api->query->thumbnail_size) {
			return $mobile_api->query->thumbnail_size;
		} else if (function_exists('get_intermediate_image_sizes')) {
			$sizes = get_intermediate_image_sizes();
			if (in_array('post-thumbnail', $sizes)) {
				return 'post-thumbnail';
			}
		}
		return 'thumbnail';
	}
	
}?>
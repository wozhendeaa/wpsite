<?php

class MOBILE_API_Comment {
	
	var $id;      // Integer
	var $name;    // String
	var $url;     // String
	var $date;    // String
	var $content; // String
	var $parent;  // Integer
	var $author;  // Object (only if the user was registered & logged in)
	
	function __construct($wp_comment = null,$edit = "") {
		if ($wp_comment) {
			$this->import_wp_object($wp_comment,$edit);
		}
	}
	
	function import_wp_object($wp_comment,$edit = "") {
		global $mobile_api;
		$content = apply_filters('comment_text',$wp_comment->comment_content);
		$this->id = (int)$wp_comment->comment_ID;
		$user_id = get_current_user_id();
		$is_super_admin = is_super_admin($user_id);
		$comment_user_id = (int)$wp_comment->user_id;
		$post_id = $wp_comment->comment_post_ID;
		$get_post = get_post($post_id);
		$post_author = $get_post->post_author;
		$post_type = $get_post->post_type;
		$post_title = $get_post->post_title;
		$date_format = mobile_api_options("date_format");
		$date_format = ($date_format != ""?$date_format:get_option("date_format"));
		$time_format = mobile_api_options("time_format");
		$time_format = ($time_format != ""?$time_format:get_option("time_format"));
		$format_date_ago = mobile_api_options("format_date_ago");
		$format_date_ago_types = mobile_api_options("format_date_ago_types");
		if ($format_date_ago == "on" && (($post_type == "post" && isset($format_date_ago_types["comments"]) && $format_date_ago_types["comments"] == "comments") || ((($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) || (isset($answer) && $answer == "answer")) && isset($format_date_ago_types["answers"]) && $format_date_ago_types["answers"] == "answers") || ($post_type == "posts" && isset($format_date_ago_types["group_comments"]) && $format_date_ago_types["group_comments"] == "group_comments"))) {
			$comment_date = mysql2date("d-m-Y g:i a", $wp_comment->comment_date, false);
			$time_string = human_time_diff(strtotime($comment_date),current_time('timestamp'))." ".esc_html__("ago","mobile-api");
		}else {
			$comment_date = mysql2date($time_format,$wp_comment->comment_date,true);
			$time_string = sprintf(esc_html__('%1$s at %2$s','mobile-api'),get_comment_date($date_format,$this->id),$comment_date);
		}
		$this->comment_approved = $wp_comment->comment_approved;
		$anonymously_user = get_comment_meta($this->id,'anonymously_user',true);
		$featured_image = get_comment_meta($this->id,'featured_image',true);
		$added_file = get_comment_meta($this->id,'added_file',true);
		$this->post_title = $post_title;
		$this->post_id = $post_id;
		$this->name = ($anonymously_user != ""?esc_html__("Anonymous","mobile-api"):$wp_comment->comment_author);
		$this->url = $wp_comment->comment_author_url;
		$this->date = $time_string;
		if ($edit == "edit") {
			$this->content = htmlspecialchars_decode(stripslashes($content));
		}else {
			$string = '';
			$this->content = apply_filters("mobile_api_before_comment",$string,$this->id);
			$this->content .= mobile_api_kses_stip(do_blocks(mobile_api_preg_replace(htmlspecialchars_decode(stripslashes($content)))));
			$this->content .= apply_filters("mobile_api_after_comment",$string,$this->id);
		}
		$comment_excerpt_count = apply_filters('mobile_api_answer_number',300);
		$strlen_comment = strlen(wp_html_excerpt($wp_comment->comment_content,$comment_excerpt_count+10));
		if ($strlen_comment > $comment_excerpt_count) {
			$this->excerpt = htmlspecialchars_decode(wp_html_excerpt($wp_comment->comment_content,$comment_excerpt_count));
		}
		$this->parent = (int)$wp_comment->comment_parent;
		if ($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) {
			$video_answer_description = get_comment_meta($this->id,'video_answer_description',true);
			$answer_private_answer = get_comment_meta($this->id,'private_answer',true);
			$this->answer_private_answer = ($answer_private_answer == "on" || $answer_private_answer == 1 || $answer_private_answer == true?"on":0);
			$yes_private_answer = mobile_api_private_answer($this->id,$comment_user_id,$user_id,$post_author);
			$best_answer_comment = get_comment_meta($this->id,"best_answer_comment",true);
			$comment_vote = (int)get_comment_meta($this->id,"comment_vote",true);
			$user_best_answer_filter = apply_filters(mobile_api_action_prefix()."_user_best_answer_filter",true);
			$active_best_answer = mobile_api_options("active_best_answer");
			$best_answer_userself = mobile_api_options("best_answer_userself");
			$user_best_answer = esc_attr(get_the_author_meta('user_best_answer',$user_id));
			$the_best_answer = get_post_meta($get_post->ID,"the_best_answer",true);
			if ($user_best_answer_filter == true && (($user_id > 0 && $active_best_answer == mobile_api_checkbox_value && (($comment_user_id != $user_id && $user_id == $post_author) || ($best_answer_userself == mobile_api_checkbox_value && $comment_user_id == $user_id && $user_id == $post_author))) || $user_best_answer == mobile_api_checkbox_value || $is_super_admin)) {
				if ($the_best_answer != 0 && ($best_answer_comment == "best_answer_comment" || $the_best_answer == $this->id)) {
					$this->remove_best_answer = true;
				}
				if ($the_best_answer == 0 || $the_best_answer == "") {
					$this->add_best_answer = true;
				}
			}
			if ($best_answer_comment == "best_answer_comment" || $the_best_answer == $this->id) {
				$this->best_answer = $best_answer_comment;
			}
			$this->comment_vote = $comment_vote;
		}
		$this->author = new MOBILE_API_Author($comment_user_id,"comment",$wp_comment);
		if (has_wpqa()) {
			$result = wpqa_reaction_results('wpqa_reactions',$user_id,$post_id,$this->id);
			$this->reactions = $result;
		}
		$can_delete_comment = mobile_api_options("can_delete_comment");
		$edit_delete_posts_comments = mobile_api_options("edit_delete_posts_comments");
		$can_edit_comment = mobile_api_options("can_edit_comment");
		$can_edit_comment_after = (int)mobile_api_options("can_edit_comment_after");
		$can_edit_comment_after = (isset($can_edit_comment_after) && $can_edit_comment_after > 0?$can_edit_comment_after:0);
		if (version_compare(phpversion(), '5.3.0', '>')) {
			$time_now = strtotime(current_time('mysql'),date_create_from_format('Y-m-d H:i',current_time('mysql')));
		}else {
			list($year, $month, $day, $hour, $minute, $second) = sscanf(current_time('mysql'),'%04d-%02d-%02d %02d:%02d:%02d');
			$datetime = new DateTime("$year-$month-$day $hour:$minute:$second");
			$time_now = strtotime($datetime->format('r'));
		}
		$time_edit_comment = strtotime('+'.$can_edit_comment_after.' hour',strtotime($wp_comment->comment_date));
		$time_end = ($time_now-$time_edit_comment)/60/60;
		$edit_comment = get_comment_meta($this->id,"edit_comment",true);
		if ($post_type == "posts" && has_wpqa()) {
			$activate_delete = wpqa_group_delete_comments($post_id,$is_super_admin,$can_delete_comment,$comment_user_id,$user_id,$edit_delete_posts_comments);
			$activate_edit = wpqa_group_edit_comments($post_id,$is_super_admin,$can_edit_comment,$comment_user_id,$user_id,$edit_delete_posts_comments);
		}
		if ((isset($activate_edit) && $activate_edit == true) || (has_wpqa() && ($is_super_admin || ($can_edit_comment == mobile_api_checkbox_value && $comment_user_id == $user_id && $comment_user_id != 0 && $user_id != 0 && ($can_edit_comment_after == 0 || $time_end <= $can_edit_comment_after))))) {
			$yes_can_edit = true;
		}
		if ($post_type == "posts" && $featured_image != "") {
			$get_attachment_url = wp_get_attachment_url($featured_image);
			if ($get_attachment_url != "") {
				$this->featured_image = $get_attachment_url;
				if (isset($yes_can_edit)) {
					$this->delete_featured_image["delete"]["api"] = mobile_api_delete.'?type=comment_meta&name=featured_image&attach_id='.$featured_image.'&id='.$this->id;
					$this->delete_featured_image["delete"]["alert"] = esc_html__('Are you sure you want to delete the image?','mobile-api');
				}
			}
		}
		if ($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) {
			if ($yes_private_answer == 1) {
				if ($featured_image != "") {
					$get_attachment_url = wp_get_attachment_url($featured_image);
					if ($get_attachment_url != "") {
						$this->featured_image = $get_attachment_url;
						if (isset($yes_can_edit)) {
							$this->delete_featured_image["delete"]["api"] = mobile_api_delete.'?type=comment_meta&name=featured_image&attach_id='.$featured_image.'&id='.$this->id;
							$this->delete_featured_image["delete"]["alert"] = esc_html__('Are you sure you want to delete the image?','mobile-api');
						}
					}
				}
				if ($added_file != "") {
					$this->attachment = array("type" => get_post_mime_type($added_file),"link" => wp_get_attachment_url($added_file));
					if (isset($yes_can_edit)) {
						$this->attachment["delete"]["api"] = mobile_api_delete.'?type=comment_meta&name=added_file&attach_id='.$added_file.'&id='.$this->id;
						$this->attachment["delete"]["alert"] = esc_html__('Are you sure you want to delete the attachment?','mobile-api');
					}
				}
				$this->video_answer_description = $video_answer_description;
				if ($video_answer_description == "on" || $video_answer_description == 1) {
					$video_answer_type = get_comment_meta($this->id,'video_answer_type',true);
					$video_answer_id = get_comment_meta($this->id,'video_answer_id',true);
					$this->video_answer_type = $video_answer_type;
					$this->video_answer_id = $video_answer_id;
				}
			}else {
				$this->private_answer = true;
				$this->content = $this->name = $this->url = $this->date = '';
				unset($this->attachment);
				unset($this->video_answer_id);
				unset($this->excerpt);
			}
		}
		if ($wp_comment->comment_approved != 1) {
			$this->status = "pending";
		}
		if ((isset($activate_delete) && $activate_delete == true) || (($can_delete_comment == mobile_api_checkbox_value && $comment_user_id == $user_id && $comment_user_id > 0 && $user_id > 0) || $is_super_admin)) {
			$this->mobile_delete = true;
			$this->delete_api = mobile_api_delete.'?type=comment&id='.$this->id;
		}
		if (isset($yes_can_edit)) {
			$this->mobile_edit = true;
			$this->edit_api = mobile_api_edit_comment.'?type=comment&id='.$this->id;
		}
		$activate_male_female = apply_filters("mobile_api_activate_male_female",false);
		if ($activate_male_female == true) {
			$info_edited = '';
			$gender_author = ($comment_user_id > 0?get_user_meta($comment_user_id,'gender',true):"");
			$gender_comment = get_comment_meta($this->id,'wpqa_comment_gender',true);
			if ($gender_author != "" && $gender_author != $gender_comment) {
				$info_edited = esc_html__("Some of main user info (Ex: name or gender) has been edited since this comment has been added","mobile-api");
				if ($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) {
					$info_edited = esc_html__("Some of main user info (Ex: name or gender) has been edited since this answer has been added","mobile-api");
				}
			}
			if ($info_edited != "") {
				$this->infoEdited = $info_edited;
			}
			if ($gender_comment == 1) {
				$this->gender = "male";
			}else if ($gender_comment == 2) {
				$this->gender = "female";
			}else {
				$this->gender = "other";
			}
		}
	}
	
	function handle_submission() {
		global $comment;
		add_action('comment_id_not_found', array(&$this, 'comment_id_not_found'));
		add_action('comment_closed', array(&$this, 'comment_closed'));
		add_action('comment_on_draft', array(&$this, 'comment_on_draft'));
		add_filter('comment_post_redirect', array(&$this, 'comment_post_redirect'));
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$activate_male_female = apply_filters("mobile_api_activate_male_female",false);
		if ($activate_male_female == true) {
			// Gender
			// $_REQUEST['gender']
		}
		$_POST['comment_post_ID'] = $_REQUEST['post_id'];
		$_POST['author'] = $_REQUEST['name'];
		$_POST['email'] = $_REQUEST['email'];
		$_POST['url'] = empty($_REQUEST['url']) ? '' : $_REQUEST['url'];
		$_POST['comment'] = $_REQUEST['content'];
		$_POST['parent'] = $_REQUEST['parent'];
		include ABSPATH . 'wp-comments-post.php';
	}
	
	function comment_id_not_found() {
		global $mobile_api;
		$mobile_api->error(sprintf(__("Post ID %s not found.","mobile-api"),$_REQUEST['post_id']));
	}
	
	function comment_closed() {
		global $mobile_api;
		$mobile_api->error(esc_html__("Post is closed for comments.","mobile-api"));
	}
	
	function comment_on_draft() {
		global $mobile_api;
		$mobile_api->error(esc_html__("You cannot comment on unpublished posts.","mobile-api"));
	}
	
	function comment_post_redirect() {
		global $comment, $mobile_api;
		$status = ($comment->comment_approved) ? true : 'pending';
		$new_comment = new MOBILE_API_Comment($comment);
		$mobile_api->response->respond($new_comment, $status);
	}
	
}?>
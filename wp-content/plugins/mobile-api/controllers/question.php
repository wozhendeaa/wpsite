<?php
/*
Controller name: Question
Controller description: Ask a new question
*/

class MOBILE_API_Question_Controller {
	function ask_question() {
		global $mobile_api;
		if (isset($_POST)) {
			$data = $_POST;
			$data = apply_filters("mobile_api_question_data_filter",$data);
			if (isset($data["ask"]) && !empty($data["ask"])) {
				$data["ask"] = json_decode(stripslashes($data["ask"]),true);
				$data["ask"] = $data["ask"]["list"];
				$firstKey = array_key_first($data["ask"]);
				if ($firstKey == 0) {
					array_unshift($data["ask"],"");
					unset($data["ask"][0]);
				}
			}
			if (isset($data["ask"]) && isset($_FILES['question_images_poll']) && !empty($_FILES['question_images_poll'])) {
				foreach($_FILES["question_images_poll"]["name"] as $key => $value) {
					$explode = explode("_",$value);
					$key_ask = $explode[0];
					$found_key = array_search($key_ask,array_column($data["ask"],'id'));
					$found_key = $found_key+1;
					$_FILES["ask"]['name'][$found_key] = array('image' => ltrim($value,$key_ask."_"));
					$_FILES["ask"]['type'][$found_key] = array('image' => $_FILES["question_images_poll"]['type'][$key]);
					$_FILES["ask"]['tmp_name'][$found_key] = array('image' => $_FILES["question_images_poll"]['tmp_name'][$key]);
					$_FILES["ask"]['error'][$found_key] = array('image' => $_FILES["question_images_poll"]['error'][$key]);
					$_FILES["ask"]['size'][$found_key] = array('image' => $_FILES["question_images_poll"]['size'][$key]);
				}
			}
			if (isset($_FILES['attachment_m']) && !empty($_FILES['attachment_m'])) {
				foreach($_FILES["attachment_m"]["name"] as $key => $value) {
					$_FILES["attachment_m"]['name'][$key] = array('file_url' => $value);
					$_FILES["attachment_m"]['type'][$key] = array('file_url' => $_FILES["attachment_m"]['type'][$key]);
					$_FILES["attachment_m"]['tmp_name'][$key] = array('file_url' => $_FILES["attachment_m"]['tmp_name'][$key]);
					$_FILES["attachment_m"]['error'][$key] = array('file_url' => $_FILES["attachment_m"]['error'][$key]);
					$_FILES["attachment_m"]['size'][$key] = array('file_url' => $_FILES["attachment_m"]['size'][$key]);
				}
			}
			$data["form_type"] = "add_question";
			$data["mobile"] = true;
			$get_current_user_id = get_current_user_id();
			$return = mobile_api_process_new_questions($data,(isset($data["user_id"])?"user":""));
			if (is_wp_error($return)) {
				$mobile_api->error(strip_tags(str_ireplace(array(" :&nbsp;",":&nbsp;","&#039;"),array(":",":","'"),$return->get_error_message())));
			}else {
				$get_post = get_post($return);
				update_post_meta($return,'added_mobile_app','yes');
				if ($get_post->post_type == mobile_api_questions_type || $get_post->post_type == mobile_api_asked_questions_type) {
					if ($get_post->post_status == "draft") {
						mobile_api_notifications_activities($get_current_user_id,"","","","","approved_question","activities","",mobile_api_questions_type);
						return array("status" => "pending","pending" => __("Your question was successfully added, It's under review.","mobile-api"));
					}else {
						$the_author = 0;
						if ($get_post->post_author == 0) {
							$the_author = get_post_meta($return,'question_username',true);
						}
						$user_id = get_post_meta($return,"user_id",true);
						if ($user_id == "") {
							$anonymously_user = get_post_meta($return,"anonymously_user",true);
							$not_user = ($get_post->post_author > 0?$get_post->post_author:0);
							mobile_api_notifications_ask_question($return,$the_author,$user_id,$not_user,$anonymously_user,$get_current_user_id);
							if (has_wpqa()) {
								wpqa_post_publish($get_post,$not_user);
								wpqa_count_posts($get_post->post_type,$get_post->post_author,"+");
							}
						}
						
						update_post_meta($return,'post_approved_before','yes');
						$way_sending_notifications = mobile_api_options("way_sending_notifications_questions");
						$schedules_time_notification = mobile_api_options("schedules_time_notification_questions");
						if ($way_sending_notifications == "cronjob" && $schedules_time_notification != "") {
							update_post_meta($return,'wpqa_post_scheduled_email','yes');
							update_post_meta($return,'wpqa_post_scheduled_notification','yes');
						}

						if ($get_current_user_id > 0) {
							mobile_api_notifications_activities($get_current_user_id,"","",$return,"","add_question","activities","",mobile_api_questions_type);
						}
						if ($get_post->post_author != $user_id && $user_id > 0) {
							mobile_api_notifications_activities($user_id,$get_post->post_author,"",$return,"","add_question_user","notifications","",mobile_api_questions_type);
						}
						return array("status" => true,"question_id" => $return);
					}
				}
				exit;
			}
		}else {
			$mobile_api->error(esc_html__("Your request not POST.","mobile-api"));
		}
	}

	function edit_question() {
		global $mobile_api;
		if (isset($_POST)) {
			$data = $_POST;
			$data = apply_filters("mobile_api_edit_question_data_filter",$data);
			if (isset($data["ask"]) && !empty($data["ask"])) {
				$data["ask"] = json_decode(stripslashes($data["ask"]),true);
				$data["ask"] = $data["ask"]["list"];
				$firstKey = array_key_first($data["ask"]);
				if ($firstKey == 0) {
					array_unshift($data["ask"],"");
					unset($data["ask"][0]);
				}
			}
			if (isset($data["ask"]) && isset($_FILES['question_images_poll']) && !empty($_FILES['question_images_poll'])) {
				foreach($_FILES["question_images_poll"]["name"] as $key => $value) {
					$found_key = array_search($key_ask,array_column($data["ask"],'id'));
					$explode = explode("_",$value);
					$key_ask = $explode[0];
					$found_key = array_search($key_ask,array_column($data["ask"],'id'));
					$_FILES["ask"]['name'][$found_key] = array('image' => ltrim($value,$key_ask."_"));
					$_FILES["ask"]['type'][$found_key] = array('image' => $_FILES["question_images_poll"]['type'][$key]);
					$_FILES["ask"]['tmp_name'][$found_key] = array('image' => $_FILES["question_images_poll"]['tmp_name'][$key]);
					$_FILES["ask"]['error'][$found_key] = array('image' => $_FILES["question_images_poll"]['error'][$key]);
					$_FILES["ask"]['size'][$found_key] = array('image' => $_FILES["question_images_poll"]['size'][$key]);
				}
			}
			if (isset($_FILES['attachment_m']) && !empty($_FILES['attachment_m'])) {
				foreach($_FILES["attachment_m"]["name"] as $key => $value) {
					$_FILES["attachment_m"]['name'][$key] = array('file_url' => $value);
					$_FILES["attachment_m"]['type'][$key] = array('file_url' => $_FILES["attachment_m"]['type'][$key]);
					$_FILES["attachment_m"]['tmp_name'][$key] = array('file_url' => $_FILES["attachment_m"]['tmp_name'][$key]);
					$_FILES["attachment_m"]['error'][$key] = array('file_url' => $_FILES["attachment_m"]['error'][$key]);
					$_FILES["attachment_m"]['size'][$key] = array('file_url' => $_FILES["attachment_m"]['size'][$key]);
				}
			}
			$data["form_type"] = "edit_question";
			$data["mobile"] = true;
			$get_current_user_id = get_current_user_id();
			$return = mobile_api_process_edit_questions($data,(isset($data["user_id"])?"user":""));
			if (is_wp_error($return)) {
				$mobile_api->error(strip_tags(str_ireplace(array(" :&nbsp;",":&nbsp;","&#039;"),array(":",":","'"),$return->get_error_message())));
			}else {
				$get_post = get_post($return);
				if ($get_post->post_type == mobile_api_questions_type || $get_post->post_type == mobile_api_asked_questions_type) {
					$user_id = get_current_user_id();
					$post_status = $get_post->post_status;
					$moderators_permissions = wpqa_user_moderator($user_id);

					if ($post_status != "draft" || ($post_status == "draft" && isset($moderators_permissions['edit']) && $moderators_permissions['edit'] == "edit") || is_super_admin($user_id)) {
						return array("status" => true,"question_id" => $return);
						// if ($post_status == "draft" && isset($moderators_permissions['edit']) && $moderators_permissions['edit'] == "edit") {
						// 	wp_redirect(wpqa_get_profile_permalink($user_id,"pending_questions"));
						// 	wp_redirect(get_permalink($return));
						// }
					}else {
						return array("status" => "pending","pending" => __("Your question has been Edited successfully. The question is under review.","mobile-api"));
					}
				}
				exit;
			}
		}else {
			$mobile_api->error(esc_html__("Your request not POST.","mobile-api"));
		}
	}

	function submit_poll() {
		global $mobile_api;
		if (isset($_POST)) {
			$data = $_POST;
			if (isset($data["post_id"]) && isset($data["poll_id"])) {
				$poll_user_only = mobile_api_options("poll_user_only");
				if (!is_user_logged_in()) {
					if ($poll_user_only == "on" || $poll_user_only == 1) {
						$mobile_api->error(esc_html__("Please login to vote and see the results.","mobile-api"));
					}else {
						$poll_mobile = get_post_meta($data["post_id"],mobile_api_question_poll,true);
						$poll_mobile = (isset($poll_mobile) && is_array($poll_mobile) && !empty($poll_mobile)?$poll_mobile:array());
						$device_token = esc_html($mobile_api->query->device_token);
						if (is_array($poll_mobile) && in_array($device_token,$poll_mobile)) {
							$mobile_api->error(esc_html__("Sorry, you cannot poll on the same question more than once.","mobile-api"));
						}
						if (empty($poll_mobile)) {
							$update = update_post_meta($data["post_id"],mobile_api_question_poll,array($device_token));
						}else if (is_array($poll_mobile) && !in_array($device_token,$poll_mobile)) {
							$update = update_post_meta($data["post_id"],mobile_api_question_poll,array_merge($poll_mobile,array($device_token)));
						}
					}
				}
				if (!is_numeric($data['poll_id'])) {
					$data['poll_id'] = json_decode(stripslashes($data['poll_id']),true);
				}
				$asks = get_post_meta($data["post_id"],"ask",true);
				$multicheck_poll = get_post_meta($data["post_id"],"multicheck_poll",true);
				if ($multicheck_poll == "on") {
					if (isset($data['poll_id']) && is_array($data['poll_id'])) {
						foreach ($data['poll_id'] as $key => $value) {
							$data['pollfrom'][$key]["value"] = $value;
						}
					}
				}
				$question_poll = mobile_api_submit_question_poll($data);
				if ($question_poll == "must_login") {
					$mobile_api->error(esc_html__("Please login to vote and see the results.","mobile-api"));
				}else if ($question_poll == "no_poll") {
					$mobile_api->error(esc_html__("Sorry, you cannot poll on the same question more than once.","mobile-api"));
				}else {
					$poll_question = get_post_meta($data["post_id"],mobile_api_poll(),true);
					if (is_array($poll_question) && !empty($poll_question)) {
						$poll_return = array();
						if (isset($asks) && is_array($asks)) {
							$key_k = 0;
							foreach ($asks as $key_ask => $value_ask) {
								$key_k++;
								$sort_polls[$key_k]["id"] = (int)$asks[$key_ask]["id"];
								$sort_polls[$key_k]["title"] = (isset($value_ask["title"])?esc_html($value_ask["title"]):"");
								$sort_polls[$key_k]["image"] = (isset($value_ask["image"])?esc_url(wpqa_image_url_id($value_ask["image"])):null);
								$sort_polls[$key_k]["value"] = (isset($asks[$key_ask]["value"]) && $asks[$key_ask]["value"] != ""?$asks[$key_ask]["value"]:(isset($poll_question[$key_ask]["value"])?$poll_question[$key_ask]["value"]:0));
								$sort_polls[$key_k]["user_ids"] = (isset($asks[$key_ask]["user_ids"])?$asks[$key_ask]["user_ids"]:(isset($poll_question[$key_ask]["user_ids"])?$poll_question[$key_ask]["user_ids"]:array()));
							}
						}
						if (isset($sort_polls) && is_array($sort_polls) && !empty($sort_polls)) {
							foreach ($sort_polls as $key_poll => $value_poll) {
								$poll_return[] = array("title" => $value_poll["title"],"image" => $value_poll["image"],"id" => $value_poll["id"],"value" => $value_poll["value"],"user_ids" => $value_poll["user_ids"]);
							}
						}
						return array("results" => $poll_return);
					}else {
						$mobile_api->error(esc_html__("No votes.","mobile-api"));
					}
				}
			}else {
				$mobile_api->error(esc_html__("You must include post_id and poll_id vars in your request.","mobile-api"));
			}
		}else {
			$mobile_api->error(esc_html__("Your request not POST.","mobile-api"));
		}
	}

	function poll_results() {
		global $mobile_api;
		if (isset($_GET)) {
			$data = $_GET;
			if (isset($data["post_id"])) {
				$question_poll = get_post_meta($data["post_id"],"question_poll",true);
				if ($question_poll != "on" && $question_poll != 1) {
					$mobile_api->error(esc_html__("This is not a poll.","mobile-api"));
				}
				$asks = get_post_meta($data["post_id"],"ask",true);
				$poll_question = get_post_meta($data["post_id"],mobile_api_poll(),true);
				if (is_array($poll_question) && !empty($poll_question)) {
					$poll_return = array();
					if (isset($asks) && is_array($asks)) {
						$key_k = 0;
						foreach ($asks as $key_ask => $value_ask) {
							$key_k++;
							$sort_polls[$key_k]["id"] = (int)$asks[$key_ask]["id"];
							$sort_polls[$key_k]["title"] = (isset($value_ask["title"])?htmlspecialchars_decode($value_ask["title"]):"");
							$sort_polls[$key_k]["image"] = (isset($value_ask["image"])?esc_url(wpqa_image_url_id($value_ask["image"])):null);
							$sort_polls[$key_k]["value"] = (isset($asks[$key_ask]["value"]) && $asks[$key_ask]["value"] != ""?$asks[$key_ask]["value"]:(isset($poll_question[$key_ask]["value"])?$poll_question[$key_ask]["value"]:0));
							$sort_polls[$key_k]["user_ids"] = (isset($asks[$key_ask]["user_ids"])?$asks[$key_ask]["user_ids"]:(isset($poll_question[$key_ask]["user_ids"])?$poll_question[$key_ask]["user_ids"]:array()));
						}
					}
					if (isset($sort_polls) && is_array($sort_polls) && !empty($sort_polls)) {
						foreach ($sort_polls as $key_poll => $value_poll) {
							$poll_return[] = array("title" => htmlspecialchars_decode($value_poll["title"]),"image" => ($value_poll["image"] != ""?$value_poll["image"]:null),"id" => $value_poll["id"],"value" => $value_poll["value"],"user_ids" => $value_poll["user_ids"]);
						}
					}
					return array("results" => $poll_return);
				}else {
					$mobile_api->error(esc_html__("No votes.","mobile-api"));
				}
			}else {
				$mobile_api->error(esc_html__("You must include post_id var in your request.","mobile-api"));
			}
		}
	}

	function submit_vote() {
		global $mobile_api;
		if (isset($_POST)) {
			$data = $_POST;
			if (isset($data["id"]) && isset($data["type"]) && isset($data["action"])) {
				$post["id"] = (int)$data["id"];
				if ($data["type"] == mobile_api_questions_type || $data["type"] == mobile_api_asked_questions_type) {
					$o_count = (int)get_post_meta($post["id"],'question_vote',true);
				}else if ($data["type"] == "answer") {
					$o_count = (int)get_comment_meta($post["id"],'comment_vote',true);
				}
				if (is_user_logged_in()) {
					$user_id = get_current_user_id();
					if (($data["type"] == mobile_api_questions_type || $data["type"] == mobile_api_asked_questions_type) && ($data["action"] == "up" || $data["action"] == "down")) {
						$get_post = get_post($post["id"]);
						$anonymously_user = get_post_meta($post["id"],'anonymously_user',true);
						if (($get_post->post_author > 0 && $get_post->post_author == $user_id) || ($anonymously_user == $user_id)) {
							return array("status" => false,"error" => esc_html__("Sorry, you cannot vote your question.","mobile-api"),"count" => $o_count);
						}
					}else if ($data["type"] == "answer" && ($data["action"] == "up" || $data["action"] == "down")) {
						$get_comment = get_comment($post["id"]);
						if ($get_comment->user_id > 0 && $get_comment->user_id == $user_id) {
							return array("status" => false,"error" => esc_html__("Sorry, you cannot vote your answer.","mobile-api"),"count" => $o_count);
						}
					}
				}else {
					$mobile_api_name_of_prefix = mobile_api_name_of_prefix();
					$active_vote_unlogged = mobile_api_options("active_vote_unlogged");
					if ($active_vote_unlogged != "on" && $active_vote_unlogged != 1) {
						return array("status" => false,"error" => esc_html__("Voting is available to members only.","mobile-api"),"count" => $o_count);
					}
					if ($data["type"] == mobile_api_questions_type || $data["type"] == mobile_api_asked_questions_type) {
						$count_up = get_post_meta($post["id"],$mobile_api_name_of_prefix.'_question_vote_up_mobile',true);
						$count_down = get_post_meta($post["id"],$mobile_api_name_of_prefix.'_question_vote_down_mobile',true);
					}else if ($data["type"] == "answer") {
						$count_up = get_comment_meta($post["id"],$mobile_api_name_of_prefix.'_answer_vote_up_mobile',true);
						$count_down = get_comment_meta($post["id"],$mobile_api_name_of_prefix.'_answer_vote_down_mobile',true);
					}
					$count_up = (isset($count_up) && is_array($count_up) && !empty($count_up)?$count_up:array());
					$count_down = (isset($count_down) && is_array($count_down) && !empty($count_down)?$count_down:array());
					$device_token = $mobile_api->query->device_token;
					if (($data["type"] == mobile_api_questions_type || $data["type"] == mobile_api_asked_questions_type) && $data["action"] == "up") {
						if (is_array($count_up) && in_array($device_token,$count_up)) {
							return array("status" => false,"error" => esc_html__("Sorry, you cannot vote on the same question more than once.","mobile-api"),"count" => $o_count);
						}
						if (is_array($count_down) && in_array($device_token,$count_down)) {
							$count_down = mobile_api_remove_item_by_value($count_down,$device_token);
							update_post_meta($post["id"],$mobile_api_name_of_prefix."_question_vote_down_mobile",$count_down);
							$activate_question_vote_down = true;
						}
						if (!isset($activate_question_vote_down)) {
							if (empty($count_up)) {
								$update = update_post_meta($post["id"],$mobile_api_name_of_prefix."_question_vote_up_mobile",array($device_token));
							}else if (is_array($count_up) && !in_array($device_token,$count_up)) {
								$update = update_post_meta($post["id"],$mobile_api_name_of_prefix."_question_vote_up_mobile",array_merge($count_up,array($device_token)));
							}
						}
					}else if (($data["type"] == mobile_api_questions_type || $data["type"] == mobile_api_asked_questions_type) && $data["action"] == "down") {
						if (is_array($count_down) && in_array($device_token,$count_down)) {
							return array("status" => false,"error" => esc_html__("Sorry, you cannot vote on the same question more than once.","mobile-api"),"count" => $o_count);
						}
						if (is_array($count_up) && in_array($device_token,$count_up)) {
							$count_up = mobile_api_remove_item_by_value($count_up,$device_token);
							update_post_meta($post["id"],$mobile_api_name_of_prefix."_question_vote_up_mobile",$count_up);
							$activate_question_vote_up = true;
						}
						if (!isset($activate_question_vote_up)) {
							if (empty($count_down)) {
								$update = update_post_meta($post["id"],$mobile_api_name_of_prefix."_question_vote_down_mobile",array($device_token));
							}else if (is_array($count_down) && !in_array($device_token,$count_down)) {
								$update = update_post_meta($post["id"],$mobile_api_name_of_prefix."_question_vote_down_mobile",array_merge($count_down,array($device_token)));
							}
						}
					}else if ($data["type"] == "answer" && $data["action"] == "up") {
						if (is_array($count_up) && in_array($device_token,$count_up)) {
							return array("status" => false,"error" => esc_html__("Sorry, you cannot vote on the same answer more than once.","mobile-api"),"count" => $o_count);
						}
						if (is_array($count_down) && in_array($device_token,$count_down)) {
							$count_down = mobile_api_remove_item_by_value($count_down,$device_token);
							update_comment_meta($post["id"],$mobile_api_name_of_prefix."_answer_vote_down_mobile",$count_down);
							$activate_answer_vote_down = true;
						}
						if (!isset($activate_answer_vote_down)) {
							if (empty($count_up)) {
								$update = update_comment_meta($post["id"],$mobile_api_name_of_prefix."_answer_vote_up_mobile",array($device_token));
							}else if (is_array($count_up) && !in_array($device_token,$count_up)) {
								$update = update_comment_meta($post["id"],$mobile_api_name_of_prefix."_answer_vote_up_mobile",array_merge($count_up,array($device_token)));
							}
						}
					}else if ($data["type"] == "answer" && $data["action"] == "down") {
						if (is_array($count_down) && in_array($device_token,$count_down)) {
							return array("status" => false,"error" => esc_html__("Sorry, you cannot vote on the same answer more than once.","mobile-api"),"count" => $o_count);
						}
						if (is_array($count_up) && in_array($device_token,$count_up)) {
							$count_up = mobile_api_remove_item_by_value($count_up,$device_token);
							update_comment_meta($post["id"],$mobile_api_name_of_prefix."_answer_vote_up_mobile",$count_up);
							$activate_answer_vote_up = true;
						}
						if (!isset($activate_answer_vote_up)) {
							if (empty($count_down)) {
								$update = update_comment_meta($post["id"],$mobile_api_name_of_prefix."_answer_vote_down_mobile",array($device_token));
							}else if (is_array($count_down) && !in_array($device_token,$count_down)) {
								$update = update_comment_meta($post["id"],$mobile_api_name_of_prefix."_answer_vote_down_mobile",array_merge($count_down,array($device_token)));
							}
						}
					}
				}
				$count = mobile_api_add_vote($post,$data["type"],$data["action"]);
				if (isset($count) && is_numeric($count)) {
					return array("count" => (int)$count);
				}else if (isset($count)) {
					return array("status" => false,"error" => $count,"count" => $o_count);
				}
			}else {
				$mobile_api->error(esc_html__("You must include id, type, and action vars in your request.","mobile-api"));
			}
		}else {
			$mobile_api->error(esc_html__("Your request not POST.","mobile-api"));
		}
	}
}?>
<?php
/*
Controller name: Post
Controller description: Add a new post
*/

class MOBILE_API_Post_Controller {
	function add_post() {
		global $mobile_api;
		if (isset($_POST)) {
			$_POST['mobile'] = true;
			$_POST['form_type'] = "add_post";
			$data = $_POST;
			if (isset($data["form_type"]) && $data["form_type"] == "add_post") {
				if (isset($_FILES['featured_image'])) {
					$_FILES['attachment'] = $_FILES['featured_image'];
				}
				$return = mobile_api_process_new_posts($data);
				if (is_wp_error($return)) {
					$mobile_api->error(strip_tags(str_ireplace(array(" :&nbsp;",":&nbsp;","&#039;"),array(":",":","'"),$return->get_error_message())));
				}else {
					$get_post = get_post($return);
					if ($get_post->post_type == "post") {
						$get_current_user_id = get_current_user_id();
						if ($get_post->post_status == "draft") {
							mobile_api_notifications_activities($get_current_user_id,"","","","","approved_post","activities","","post");
							return array("status" => "pending","pending" => __("Your post was successfully added, It's under review.","mobile-api"));
						}else {
							$the_author = 0;
							if ($get_post->post_author == 0) {
								$the_author = get_post_meta($return,'post_username',true);
							}
							$not_user = ($get_post->post_author > 0?$get_post->post_author:0);
							mobile_api_notifications_add_post($return,$the_author,$not_user,$get_current_user_id);
							if (has_wpqa()) {
								wpqa_post_publish($get_post,$not_user);
								wpqa_count_posts($get_post->post_type,$get_post->post_author,"+");
							}
							update_post_meta($return,'post_approved_before','yes');
							$way_sending_notifications = mobile_api_options("way_sending_notifications_posts");
							$schedules_time_notification = mobile_api_options("schedules_time_notification_posts");
							if ($way_sending_notifications == "cronjob" && $schedules_time_notification != "") {
								update_post_meta($return,'wpqa_post_scheduled_email','yes');
								update_post_meta($return,'wpqa_post_scheduled_notification','yes');
							}
							return array("status" => true,"post_id" => $return);
						}
					}
					exit;
				}
			}else {
				$mobile_api->error(esc_html__("You must include form_type var in your request.","mobile-api"));
			}
		}else {
			$mobile_api->error(esc_html__("Your request not POST.","mobile-api"));
		}
	}

	function edit_post() {
		global $mobile_api;
		if (isset($_POST)) {
			$data = $_POST;
			$data = apply_filters("mobile_api_edit_post_data_filter",$data);
			$data["form_type"] = "edit_post";
			$data["mobile"] = true;
			if (isset($_FILES['featured_image'])) {
				$_FILES['attachment'] = $_FILES['featured_image'];
			}
			$return = mobile_api_process_edit_posts($data);
			if (is_wp_error($return)) {
				$mobile_api->error(strip_tags(str_ireplace(array(" :&nbsp;",":&nbsp;","&#039;"),array(":",":","'"),$return->get_error_message())));
			}else {
				$user_id = get_current_user_id();
	   			$get_post = get_post($return);
	   			$post_status = $get_post->post_status;
	   			$moderators_permissions = wpqa_user_moderator($user_id);

				if ($post_status != "draft" || ($post_status == "draft" && isset($moderators_permissions['edit']) && $moderators_permissions['edit'] == "edit") || is_super_admin($user_id)) {
					return array("status" => true,"post_id" => $return);
					// if ($post_status == "draft" && isset($moderators_permissions['edit']) && $moderators_permissions['edit'] == "edit") {
					// 	wp_redirect(wpqa_get_profile_permalink($user_id,"pending_posts"));
					// }
				}else {
	   				return array("status" => "pending","pending" => __("Your post was successfully edited, It's under review.","mobile-api"));
	   			}
			}
		}else {
			$mobile_api->error(esc_html__("Your request not POST.","mobile-api"));
		}
	}
}?>
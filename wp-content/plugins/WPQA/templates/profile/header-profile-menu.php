<?php

/* @author    2codeThemes
*  @package   WPQA/templates/profile
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if (isset($header_profile_items) && is_array($header_profile_items) && !empty($header_profile_items) && isset($user_id)) {
	echo "<ul class='sub-menu'>";
		foreach ($header_profile_items as $menu_key => $menu_value) {
			if (strpos($menu_value->url,'#wpqa-') !== false) {
				$tab_item = str_ireplace("#wpqa-","",$menu_value->url);
				$header_profile_items[$menu_key]->wpqa_tab_item = $tab_item;
			}else {
				$header_profile_items[$menu_key]->wpqa_tab_item = $menu_value->url;
				$tab_item = $menu_value->url;
			}
			if (isset($tab_item)) {
				if ($tab_item == "asked" || $tab_item == "asked_questions") {
					$ask_question_to_users = wpqa_options("ask_question_to_users");
					if ($ask_question_to_users == "on") {
						$tab_item_available = true;
					}else {
						unset($header_profile_items[$menu_key]);
					}
				}else if ($tab_item == "paid_questions") {
					$pay_ask = wpqa_options("pay_ask");
					if ($pay_ask == "on") {
						$tab_item_available = true;
					}else {
						unset($header_profile_items[$menu_key]);
					}
				}else if ($tab_item == "points") {
					if ($active_points == "on") {
						$tab_item_available = true;
					}else {
						unset($header_profile_items[$menu_key]);
					}
				}else if ($tab_item == "activities") {
					if ($active_activity_log == "on") {
						$tab_item_available = true;
					}else {
						unset($header_profile_items[$menu_key]);
					}
				}else if ($tab_item == "notifications") {
					if ($active_notifications == "on") {
						$tab_item_available = true;
					}else {
						unset($header_profile_items[$menu_key]);
					}
				}else if ($tab_item == "referrals") {
					if ($active_referral == "on") {
						$tab_item_available = true;
					}else {
						unset($header_profile_items[$menu_key]);
					}
				}else if ($tab_item == "subscriptions") {
					$subscriptions_payment = wpqa_options("subscriptions_payment");
					if ($subscriptions_payment == "on") {
						$tab_item_available = true;
					}else {
						unset($header_profile_items[$menu_key]);
					}
				}else if ($tab_item == "messages") {
					if ($active_message == "on") {
						$tab_item_available = true;
					}else {
						unset($header_profile_items[$menu_key]);
					}
				}else if ($tab_item == "pending-questions" || $tab_item == "pending-posts") {
					if (isset($user_moderator) && ($is_super_admin || ($user_moderator == "on" && $active_moderators == "on"))) {
						$tab_item_available = true;
					}else {
						unset($header_profile_items[$menu_key]);
					}
				}
			}
		}
		foreach ($header_profile_items as $menu_key => $menu_value) {
			$tab_item = $menu_value->wpqa_tab_item;
			if ($tab_item != "") {
				$tab_class = "";
				if ($tab_item == "profile") {
					$last_url = wpqa_profile_url($user_id);
				}else if ($tab_item == "edit-profile") {
					$last_url = wpqa_get_profile_permalink($user_id,"edit");
				}else if ($tab_item == "mail-settings") {
					$last_url = esc_url(wpqa_get_profile_permalink($user_id,"mails"));
				}else if ($tab_item == "delete-account") {
					$last_url = esc_url(wpqa_get_profile_permalink($user_id,"delete"));
				}else if ($tab_item == "all-questions") {
					$last_url = esc_url_raw(get_post_type_archive_link(wpqa_questions_type));
				}else if ($tab_item == "poll") {
					$last_url = esc_url_raw(add_query_arg(array("type" => "poll"),get_post_type_archive_link(wpqa_questions_type)));
				}else if ($tab_item == "all-groups") {
					$last_url = esc_url_raw(get_post_type_archive_link("group"));
				}else if ($tab_item == "login") {
					$last_url = wpqa_login_permalink();
				}else if ($tab_item == "login-popup") {
					$last_url = wpqa_login_permalink();
					$tab_class = "login-panel";
				}else if ($tab_item == "signup") {
					$last_url = wpqa_signup_permalink();
				}else if ($tab_item == "signup-popup") {
					$last_url = wpqa_signup_permalink();
					$tab_class = "signup-panel";
				}else if ($tab_item == "lost-password") {
					$last_url = wpqa_lost_password_permalink();
				}else if ($tab_item == "lost-password-popup") {
					$last_url = wpqa_lost_password_permalink();
					$tab_class = "lost-password";
				}else if ($tab_item == "add-category") {
					$last_url = wpqa_add_category_permalink();
				}else if ($tab_item == "add-question") {
					$last_url = wpqa_add_question_permalink();
				}else if ($tab_item == "add-question-popup") {
					$last_url = wpqa_add_question_permalink();
					$tab_class = "wpqa-question";
				}else if ($tab_item == "add-group") {
					$last_url = wpqa_add_group_permalink();
				}else if ($tab_item == "add-post") {
					$last_url = wpqa_add_post_permalink();
				}else if ($tab_item == "add-post-popup") {
					$last_url = wpqa_add_post_permalink();
					$tab_class = "wpqa-post";
				}else if ($tab_item == "subscriptions") {
					$last_url = wpqa_subscriptions_permalink();
				}else if ($tab_item == "buy-points") {
					$last_url = wpqa_buy_points_permalink();
				}else if ($tab_item == "logout") {
					$last_url = wpqa_get_logout();
				}else {
					$tab_item = str_ireplace("-","_",$tab_item);
					$last_url = wpqa_get_profile_permalink($user_id,$tab_item);
				}
			}else {
				$last_url = $menu_value->url;
			}
			if (isset($last_url) && $last_url != "") {
				echo "<li class='menu-item".(isset($selected) && $selected == true?" active-tab":"")."'>
					<a".(isset($tab_class)?" class='".$tab_class."'":"")." href='".esc_url($last_url)."'>
					".apply_filters('wpqa_menu_title',$menu_value->title,$menu_value,"header_profile_menu");
					include wpqa_get_template("menu-counts.php","profile/");
				echo "</a>
				</li>";
			}
		}
	echo "</ul>";
}
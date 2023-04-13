<?php

/* @author    2codeThemes
*  @package   WPQA/framework
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Fetch options */
function wpqa_parse_str($string) {
	if ('' == $string) {
		return false;
	}
	$result = array();
	$pairs  = explode('&',$string);
	foreach ($pairs as $key => $pair) {
		parse_str($pair,$params);
		$k = key($params);
		if (!isset($result[$k])) {
			$result += $params;
		}else {
			if (is_array($result[$k]) && is_array($params[$k])) {
				$result[$k] = wpqa_array_merge_distinct($result[$k],$params[$k]);
			}
		}
	}

	return $result;
}
function wpqa_array_merge_distinct(array $array1,array $array2) {
	$merged = $array1;
	foreach ($array2 as $key => $value) {
		if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
			$merged[$key] = wpqa_array_merge_distinct($merged[$key],$value);
		}else if (is_numeric($key) && isset($merged[$key])) {
			$merged[] = $value;
		}else {
			$merged[$key] = $value;
		}
	}
	return $merged;
}
/* Update options */
function wpqa_update_options() {
	$user_id = get_current_user_id();
	if (is_super_admin($user_id)) {
		$_POST['data'] = stripslashes($_POST['data']);
		$values = wpqa_parse_str($_POST['data']);
		if (!isset($values['saving_nonce']) || !wp_verify_nonce($values['saving_nonce'],'saving_nonce')) {
			echo 3;
		}else {
			do_action(wpqa_prefix_theme."_update_options",$values);
			$setting_options = $values[wpqa_options];
			unset($setting_options['export_setting']);
			$setting_options = apply_filters(wpqa_prefix_theme."_options_values",$setting_options);
			/* Roles */
			global $wp_roles;
			if (isset($setting_options["roles"])) {$k = 0;
				foreach ($setting_options["roles"] as $value_roles) {$k++;
					$is_group = get_role($value_roles["id"]);
					if (isset($value_roles["new"]) && $value_roles["new"] == "new") {
						if (!isset($is_group)) {
							$is_group = add_role($value_roles["id"],ucfirst($value_roles["group"]),array('read' => false));
							$is_group->add_cap('new');
						}
					}
					if (isset($is_group)) {
						$roles_array = array("ask_question","ask_question_payment","show_question","add_answer","add_answer_payment","show_answer","add_group","add_group_payment","add_post","add_post_payment","add_category","send_message","upload_files","approve_question","approve_group","edit_other_groups","approve_answer","approve_post","approve_comment","approve_question_media","approve_answer_media","show_knowledgebase","without_ads","show_post","add_comment","show_comment");
						if (isset($roles_array) && !empty($roles_array)) {
							foreach ($roles_array as $roles_key) {
								if (isset($value_roles[$roles_key]) && $value_roles[$roles_key] == "on") {
									$is_group->add_cap($roles_key);
								}else {
									$is_group->remove_cap($roles_key);
								}
							}
						}
					}
				}
			}
			/* Sidebar */
			$sidebars_widgets = get_option("sidebars_widgets");
			if (isset($setting_options["sidebars"]) && is_array($setting_options["sidebars"]) && !empty($setting_options["sidebars"])) {
				foreach ($setting_options["sidebars"] as $sidebar) {
					$sidebar_name = sanitize_title(esc_html($sidebar["name"]));
					$key = array($sidebar_name => (isset($sidebars_widgets[$sidebar_name]) && is_array($sidebars_widgets[$sidebar_name])?$sidebars_widgets[$sidebar_name]:array()));
					if ((is_array($sidebars_widgets) && empty($sidebars_widgets)) || !is_array($sidebars_widgets) || $sidebars_widgets == "") {
						$sidebars_widgets = $key;
					}else if (is_array($sidebars_widgets) && !in_array($key,$sidebars_widgets)) {
						$sidebars_widgets = array_merge($sidebars_widgets,$key);
					}
				}
			}
			update_option("sidebars_widgets",$sidebars_widgets);
			$wpqa_registered_sidebars = array();
			foreach ($GLOBALS['wp_registered_sidebars'] as $sidebar) {
				$wpqa_registered_sidebars[$sidebar['id']] = $sidebar['name'];
			}
			update_option("wpqa_registered_sidebars",$wpqa_registered_sidebars);
			/* Schedules */
			if (isset($setting_options["schedules_time_hour"])) {
				$schedules_time_hour = get_option("schedules_time_hour");
				if ($setting_options["schedules_time_hour"] != $schedules_time_hour) {
					update_option("schedules_time_hour",$setting_options["schedules_time_hour"]);
					delete_option(wpqa_prefix_theme."_schedules_time");
				}
			}
			if (isset($setting_options["schedules_time_day"])) {
				$schedules_time_day = get_option("schedules_time_day");
				if ($setting_options["schedules_time_day"] != $schedules_time_day) {
					update_option("schedules_time_day",$setting_options["schedules_time_day"]);
					delete_option(wpqa_prefix_theme."_schedules_time");
				}
			}
			if (isset($setting_options["schedules_time_hour_post"])) {
				$schedules_time_hour_post = get_option("schedules_time_hour_post");
				if ($setting_options["schedules_time_hour_post"] != $schedules_time_hour_post) {
					update_option("schedules_time_hour_post",$setting_options["schedules_time_hour_post"]);
					delete_option(wpqa_prefix_theme."_schedules_time_post");
				}
			}
			if (isset($setting_options["schedules_time_day_post"])) {
				$schedules_time_day_post = get_option("schedules_time_day_post");
				if ($setting_options["schedules_time_day_post"] != $schedules_time_day_post) {
					update_option("schedules_time_day_post",$setting_options["schedules_time_day_post"]);
					delete_option(wpqa_prefix_theme."_schedules_time_post");
				}
			}
			if (isset($setting_options["activate_currencies"])) {
				$activate_currencies = get_option("activate_currencies");
				if ($setting_options["activate_currencies"] != $activate_currencies) {
					update_option("activate_currencies",$setting_options["activate_currencies"]);
					echo 2;
				}
			}
			if (isset($setting_options["question_schedules"]) && $setting_options["question_schedules"] != "on") {
				wp_clear_scheduled_hook("wpqa_scheduled_mails_daily");
				wp_clear_scheduled_hook("wpqa_scheduled_mails_weekly");
				wp_clear_scheduled_hook("wpqa_scheduled_mails_monthly");
			}
			if (isset($setting_options["post_schedules"]) && $setting_options["post_schedules"] != "on") {
				wp_clear_scheduled_hook("wpqa_scheduled_mails_daily_post");
				wp_clear_scheduled_hook("wpqa_scheduled_mails_weekly_post");
				wp_clear_scheduled_hook("wpqa_scheduled_mails_monthly_post");
			}
			if (isset($setting_options["way_sending_notifications_questions"]) && $setting_options["way_sending_notifications_questions"] != "cronjob") {
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_daily_question");
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_twicedaily_question");
			}
			if (isset($setting_options["way_sending_notifications_posts"]) && $setting_options["way_sending_notifications_posts"] != "cronjob") {
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_daily_post");
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_twicedaily_post");
			}
			if (isset($setting_options["way_sending_notifications_answers"]) && $setting_options["way_sending_notifications_answers"] != "cronjob") {
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_daily_answer");
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_twicedaily_answer");
			}
			if (isset($setting_options["way_sending_notifications_questions"]) && $setting_options["way_sending_notifications_questions"] != "cronjob" && isset($setting_options["way_sending_notifications_posts"]) && $setting_options["way_sending_notifications_posts"] != "cronjob" && isset($setting_options["way_sending_notifications_answers"]) && $setting_options["way_sending_notifications_answers"] != "cronjob") {
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_hourly");
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_twicehourly");
			}
			/* Payments */
			$pay_ask               = (isset($setting_options['pay_ask'])?$setting_options['pay_ask']:0);
			$payment_type_ask      = (isset($setting_options['payment_type_ask'])?$setting_options['payment_type_ask']:0);
			$pay_to_sticky         = (isset($setting_options['pay_to_sticky'])?$setting_options['pay_to_sticky']:0);
			$payment_type_sticky   = (isset($setting_options['payment_type_sticky'])?$setting_options['payment_type_sticky']:0);
			$subscriptions_payment = (isset($setting_options['subscriptions_payment'])?$setting_options['subscriptions_payment']:0);
			$buy_points_payment    = (isset($setting_options['buy_points_payment'])?$setting_options['buy_points_payment']:0);
			$pay_answer            = (isset($setting_options['pay_answer'])?$setting_options['pay_answer']:0);
			$payment_type_answer   = (isset($setting_options['payment_type_answer'])?$setting_options['payment_type_answer']:0);
			$currency_code         = (isset($setting_options['currency_code'])?$setting_options['currency_code']:"USD");
			$pay_to_anything       = apply_filters("wpqa_filter_pay_to_anything",false);
			if (($pay_ask == "on" && $payment_type_ask != "points") || ($pay_to_sticky == "on" && $payment_type_sticky != "points") || $subscriptions_payment == "on" || $buy_points_payment == "on" || ($pay_answer == "on" && $payment_type_answer != "points") || $pay_to_anything == true) {
				$payment_methods = (isset($setting_options['payment_methodes'])?$setting_options['payment_methodes']:array());
				$stripe_test = (isset($setting_options['stripe_test'])?$setting_options['stripe_test']:"");
				$secret_key = (isset($setting_options[($stripe_test == "on"?"test_":"")."secret_key"])?$setting_options[($stripe_test == "on"?"test_":"")."secret_key"]:"");
				if (isset($payment_methods["stripe"]["value"]) && $payment_methods["stripe"]["value"] == "stripe" && $secret_key != "") {
					$array = array(
						"monthly"  => array("key" => "monthly","name" => esc_html__("Monthly membership","wpqa")),
						"3months"  => array("key" => "3months","name" => esc_html__("Three months membership","wpqa")),
						"6months"  => array("key" => "6months","name" => esc_html__("Six Months membership","wpqa")),
						"yearly"   => array("key" => "yearly","name" => esc_html__("Yearly membership","wpqa")),
						"2years"   => array("key" => "2years","name" => esc_html__("Two Years membership","wpqa")),
						"lifetime" => array("key" => "lifetime","name" => esc_html__("Lifetime membership","wpqa")),
					);
					require_once plugin_dir_path(dirname(__FILE__)).'payments/stripe/init.php';
					$stripe = new \Stripe\StripeClient($secret_key);
					if (isset($setting_options["coupons"]) && is_array($setting_options["coupons"]) && !empty($setting_options["coupons"])) {
						foreach ($setting_options["coupons"] as $key => $value) {
							$coupon_name = preg_replace('/[^a-zA-Z0-9._\-]/','',strtolower($value['coupon_name']));
							$coupon_amount = (int)$value['coupon_amount'];
							$coupon_type = $value['coupon_type'];
							$coupon_id = $coupon_amount.'_'.$coupon_name;
							try {
								$get_coupon = $stripe->coupons->retrieve($coupon_id);
							}catch ( \Stripe\Exception\CardException $e ) {
								$result_error_coupon = $e->getError()->message;
							}catch ( Exception $e ) {
								$result_error_coupon = $e->getMessage();
							}
							if (!isset($result_error_coupon) || (isset($result_error_coupon) && $result_error_coupon != "")) {
								if ($coupon_type == "percent") {
									$coupon_type = array('percent_off' => $coupon_amount);
								}else {
									$coupon_type = array('amount_off' => $coupon_amount);
								}
								$coupon_array = array(
									'duration' => 'once',//repeating
									'id'       => $coupon_id
								);
								try {
									$coupon = $stripe->coupons->create(array_merge($coupon_type,$coupon_array));
								}catch ( \Stripe\Exception\CardException $e ) {
									$result_error_coupon = $e->getError()->message;
								}catch ( Exception $e ) {
									$result_error_coupon = $e->getMessage();
								}
							}
						}
					}
				}
			}
			/* Old emails */
			$mail_issue_fixed = get_option("wpqa_mail_issue_fixed");
			if ($mail_issue_fixed != "done" && (isset($setting_options['email_template']) && $setting_options['email_template'] != "") || (isset($setting_options['email_template_to']) && $setting_options['email_template_to'] != "")) {
				$parse = parse_url(get_site_url());
				$whitelist = array(
					'127.0.0.1',
					'::1'
				);
				if (in_array($_SERVER['REMOTE_ADDR'],$whitelist) || $parse['host'] == "2code.info") {
					$not_replace = true;
				}
				
				if (isset($setting_options['email_template']) && $setting_options['email_template'] != "" && !isset($not_replace)) {
					if (strpos($setting_options['email_template'],'@2code.info') !== false) {
						$setting_options['email_template'] = "no_reply@".$parse['host'];
					}
				}
				if (isset($setting_options['email_template_to']) && $setting_options['email_template_to'] != "" && !isset($not_replace)) {
					if (strpos($setting_options['email_template_to'],'@2code.info') !== false || strpos($setting_options['email_template_to'],'2codethemes@') !== false || strpos($setting_options['email_template_to'],'vbegy.info@') !== false) {
						$setting_options['email_template_to'] = get_bloginfo("admin_email");
					}
				}
				update_option("wpqa_mail_issue_fixed","done");
			}
			/* Register */
			if (isset($setting_options['activate_register']) && $setting_options['activate_register'] == "enabled") {
				update_option("users_can_register",true);
			}else {
				delete_option("users_can_register");
			}
			/* Old themes */
			if (isset($setting_options['old_themes']) && $setting_options['old_themes'] != "nothing" && $setting_options['old_themes'] != "") {
				$old_theme_name = $setting_options['old_themes'];
				$old_theme = get_option("old_".$old_theme_name);
				if ($old_theme != "done") {
					$wpqa_options = get_option($old_theme_name."_options");
					if (is_array($wpqa_options) && !empty($wpqa_options)) {
						$setting_options = $wpqa_options;
					}
					update_option("old_".$old_theme_name,"done");
					echo 2;
				}
			}
			/* Save */
			update_option(wpqa_options,$setting_options);
			update_option("FlushRewriteRules",true);
		}
	}
	die();
}
add_action('wp_ajax_wpqa_update_options','wpqa_update_options');
/* Import options */
function wpqa_import_options() {
	$user_id = get_current_user_id();
	if (is_super_admin($user_id)) {
		$saving_nonce = (isset($_POST["saving_nonce"])?esc_html($_POST["saving_nonce"]):"");
		if (!wp_verify_nonce($saving_nonce,'saving_nonce')) {
			echo 3;
		}else {
			$values = $_POST['data'];
			if ($values != "") {
				$data = wpqa_base_decode($values);
				$data = json_decode($data,true);
				$array_options = array(wpqa_options,"sidebars");
				foreach ($array_options as $option) {
					if (isset($data[$option])) {
						update_option($option,$data[$option]);
					}else{
						delete_option($option);
					}
				}
				echo 2;
				update_option("FlushRewriteRules",true);
				die();
			}
			update_option("FlushRewriteRules",true);
		}
	}
	die();
}
add_action('wp_ajax_wpqa_import_options','wpqa_import_options');
/* Reset options */
function wpqa_reset_options() {
	$user_id = get_current_user_id();
	if (is_super_admin($user_id)) {
		$saving_nonce = (isset($_POST["saving_nonce"])?esc_html($_POST["saving_nonce"]):"");
		if (!wp_verify_nonce($saving_nonce,'saving_nonce')) {
			echo 3;
		}else {
			$options = wpqa_admin_options();
			foreach ($options as $option) {
				if (isset($option['id']) && isset($option['std'])) {
					$option_res[$option['id']] = $option['std'];
				}
			}
			update_option(wpqa_options,$option_res);
			update_option("FlushRewriteRules",true);
		}
	}
	die();
}
add_action('wp_ajax_wpqa_reset_options','wpqa_reset_options');
/* Delete role */
function wpqa_delete_role() {
	$roles_val = $_POST["roles_val"];
	if (get_role($roles_val)) {
		remove_role($roles_val);
	}
}
add_action('wp_ajax_wpqa_delete_role','wpqa_delete_role');
/* Admin live search */
function wpqa_admin_live_search() {
	$search_value = esc_html($_POST['search_value']);
	if ($search_value != "") {
		$search_value_ucfirst = ucfirst(esc_html($_POST['search_value']));
		$wpqa_admin_options = wpqa_admin_options();
		$k = 0;
		if (isset($wpqa_admin_options) && is_array($wpqa_admin_options)) {?>
			<ul>
				<?php foreach ($wpqa_admin_options as $key => $value) {
					if (isset($value["type"]) && $value["type"] != "content" && $value["type"] != "info" && $value["type"] != "heading" && $value["type"] != "heading-2" && $value['type'] != "heading-3" && ((isset($value["name"]) && $value["name"] != "" && (strpos($value["name"],$search_value) !== false || strpos($value["name"],$search_value_ucfirst) !== false)) || (isset($value["desc"]) && $value["desc"] != "" && (strpos($value["desc"],$search_value) !== false || strpos($value["desc"],$search_value_ucfirst) !== false)))) {
						$find_resluts = true;
						$k++;
						if ((isset($value["name"]) && $value["name"] != "" && (strpos($value["name"],$search_value) !== false || strpos($value["name"],$search_value_ucfirst) !== false))) {?>
							<li><a href="section-<?php echo esc_html($value["id"])?>"><?php echo str_ireplace($search_value,"<strong>".$search_value."</strong>",esc_html($value["name"]))?></a></li>
						<?php }else {?>
							<li><a href="section-<?php echo esc_html($value["id"])?>"><?php echo str_ireplace($search_value,"<strong>".$search_value."</strong>",esc_html($value["desc"]))?></a></li>
						<?php }
						if ($k == 10) {
							break;
						}
					}
				}
				if (!isset($find_resluts)) {?>
					<li><?php esc_html_e("Sorry, no results.","wpqa")?></li>
				<?php }?>
			</ul>
		<?php }
	}
	die();
}
add_action('wp_ajax_wpqa_admin_live_search','wpqa_admin_live_search');
/* Categories ajax */
function wpqa_categories_ajax() {
	$name = (isset($_POST["name"])?esc_html($_POST["name"]):"");
	$name_2 = (isset($_POST["name_2"])?esc_html($_POST["name_2"]):"");
	$tabs = (isset($_POST["tabs"])?esc_html($_POST["tabs"]):"");
	if ($tabs == "yes") {
		echo '<li><label class="selectit"><input value="on" type="checkbox" name="'.$name.'[show_all_categories]">'.esc_html__('Show All Categories',"wpqa").'</label></li>';
	}
	echo wpqa_categories_checklist_admin(array("name" => $name.$name_2,"id" => $name.$name_2));
	die();
}
add_action('wp_ajax_wpqa_categories_ajax','wpqa_categories_ajax');
/* Recreate menus */
function wpqa_recreate_menus() {
	$wpqa_custom_queries = get_option("wpqa_custom_queries");
	wp_delete_nav_menu("Profile Page Tabs");
	wp_delete_nav_menu("Header Profile Menu");
	$wpqa_custom_queries = wpqa_remove_item_by_value($wpqa_custom_queries,"profile_page_menu");
	$wpqa_custom_queries = wpqa_remove_item_by_value($wpqa_custom_queries,"header_profile_menu");
	update_option("wpqa_custom_queries",$wpqa_custom_queries);
	die();
}
add_action('wp_ajax_wpqa_recreate_menus','wpqa_recreate_menus');?>
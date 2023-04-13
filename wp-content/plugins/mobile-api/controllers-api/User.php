<?php

/*
Controller name: User
Controller description: User Registration, Authentication, User Info, and User Meta methods
*/

class MOBILE_API_User_Controller
{

	/**
	 * Returns an Array with registered userid & valid cookie
	 * @param String username: username to register
	 * @param String email: email address for user registration
	 * @param String user_pass: user_pass to be set (optional)
	 * @param String display_name: display_name for user
	 */

	public function register() {
		global $mobile_api;
		$activate_register = mobile_api_options("activate_register");
		$activate_login = mobile_api_options("activate_login");

		if ($activate_register == "disabled") {
			$mobile_api->error(esc_html__("Sorry, the register is disabled.","mobile-api"));
		}

		if (!$mobile_api->query->username) {
			$mobile_api->error(esc_html__("You must include username var in your request.","mobile-api"));
		}else {
			$username = sanitize_user($mobile_api->query->username);
		}

		if (!$mobile_api->query->email) {
			$mobile_api->error(esc_html__("You must include email var in your request.","mobile-api"));
		}else {
			$email = sanitize_email($mobile_api->query->email);
		}

		if ($mobile_api->query->display_name) {
			$display_name = sanitize_text_field($mobile_api->query->display_name);
		}

		$user_pass = sanitize_text_field($_REQUEST['user_pass']);

		$invalid_usernames = array('admin');

		if (!validate_username($username) || in_array($username, $invalid_usernames)) {
			$mobile_api->error(esc_html__("Username is invalid.","mobile-api"));
		}else if (username_exists($username)) {
			$mobile_api->error(esc_html__("Username already exists.","mobile-api"));
		}else {
			if (!is_email($email)) {
				$mobile_api->error(esc_html__("E-mail address is invalid.","mobile-api"));
			}else if (email_exists($email)) {
				$mobile_api->error(esc_html__("E-mail address is already in use.","mobile-api"));
			}else {
				if (!isset($_REQUEST['user_pass'])) {
					$user_pass             = wp_generate_password();
					$_REQUEST['user_pass'] = $user_pass;
				}

				$_REQUEST['user_name'] = $username;
				$_REQUEST['user_login'] = $username;
				$_REQUEST['user_email'] = $email;
				$_REQUEST['pass1'] = $_REQUEST['pass2'] = $user_pass;
				$user_meta_avatar = mobile_api_avatar_name();
				if (isset($_FILES['avatar'])) {
					$_FILES[$user_meta_avatar] = $_FILES['avatar'];
				}

				if (has_wpqa()) {
					$user_meta_cover = wpqa_cover_name();
					if (isset($_FILES['cover'])) {
						$_FILES[$user_meta_cover] = $_FILES['cover'];
					}
				}

				$allowed_params = array('user_login', 'user_email', 'user_pass', 'display_name', 'user_nicename', 'user_url', 'nickname', 'first_name',
					'last_name', 'description', 'rich_editing', 'user_registered', 'role', 'jabber', 'aim', 'yim',
					'comment_shortcuts', 'admin_color', 'use_ssl', 'show_admin_bar_front',
				);

				foreach ($_REQUEST as $field => $value) {
					if (in_array($field, $allowed_params)) {
						$user[$field] = trim(sanitize_text_field($value));
					}
				}
				$user['role'] = mobile_api_default_group();
				$_REQUEST['form_type'] = 'wpqa-signup';
				if (has_askme()) {
					if (isset($_REQUEST['agree_terms']) == "on") {
						$_REQUEST['agree_terms'] = 1;
					}
					if (isset($_REQUEST['gender'])) {
						$_REQUEST['sex'] = $_REQUEST['gender'];
					}
					$_REQUEST['form_type'] = 'ask-signup';
				}
				if (isset($_REQUEST['website'])) {
					$_REQUEST['url'] = $_REQUEST['website'];
				}
				$_REQUEST['mobile'] = true;
				$return = mobile_api_signup_process($_REQUEST);
				if (is_wp_error($return)) {
					$mobile_api->error(strip_tags(wptexturize(str_ireplace(array(" :&nbsp;",":&nbsp;","&#039;"),array(":",":","'"),$return->get_error_message()))));
				}else {
					$user_id = $return;
				}

			}
		}

		$device_token = $mobile_api->query->device_token;

		if (isset($user_id)) {
			if (is_array($_REQUEST['custom_fields'])) {
				foreach ($_REQUEST['custom_fields'] as $field => $val) {
					$data[$field] = update_user_meta($user_id, $field, $val);
				}
			}

			$data = array(
				'username' => $username,
				'password' => $user_pass
			);

			$response = wp_remote_post(site_url(mobile_api_json_wp.'/'.mobile_api_base.'/v1/token'),array(
				'method'      => 'POST',
				'timeout'     => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'body'        => $data,
				'cookies'     => array(),
			));
			if (is_wp_error($response)) {
				$mobile_api->error($response->get_error_message());
			}
			$response_body = wp_remote_retrieve_body($response);
			$result        = json_decode($response_body,true);
			if (isset($result["user"]["id"])) {
				update_user_meta($user_id,"mobile_api_token",$result["token"]);
				mobile_api_update_devices_token($user_id,$device_token);
			}
		}

		if (isset($result["user"]["id"])) {
			$user_id = $result["user"]["id"];
			update_user_meta($user_id,"mobile_api_token",$result["token"]);
			mobile_api_update_devices_token($user_id,$device_token);
			if ($activate_login == "disabled") {
				$result = array("status" => true,"message" => esc_html__("You are registered now, and maybe the admin will contact you.","mobile-api"));
			}
			return $result;
		}else {
			$mobile_api->error(esc_html__("Error in the generate token.","mobile-api"));
		}

	}

	public function login() {
		global $mobile_api;

		$activate_login = mobile_api_options("activate_login");
		if ($activate_login == "disabled") {
			$mobile_api->error(esc_html__("Sorry, the login is disabled.","mobile-api"));
		}

		foreach ($_POST as $k => $val) {
			if (isset($_POST[$k])) {
				$mobile_api->query->$k = $val;
			}
		}

		if (!$mobile_api->query->username && !$mobile_api->query->email) {
			$mobile_api->error(esc_html__("You must include username or email var in your request to generate token.","mobile-api"));
		}

		if (!$mobile_api->query->password) {
			$mobile_api->error(esc_html__("You must include a password var in your request.","mobile-api"));
		}

		if ($mobile_api->query->email) {
			if (is_email($mobile_api->query->email)) {
				if (!email_exists($mobile_api->query->email)) {
					$mobile_api->error(esc_html__("email does not exist.","mobile-api"));
				}
			}else {
				$mobile_api->error(esc_html__("Invalid email address.","mobile-api"));
			}
		}

		$data = array(
			'username' => $mobile_api->query->username,
			'password' => $mobile_api->query->password
		);

		$response = wp_remote_post(site_url(mobile_api_json_wp.'/'.mobile_api_base.'/v1/token'),array(
			'method'      => 'POST',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'body'        => $data,
			'cookies'     => array(),
		));
		if (is_wp_error($response)) {
			$mobile_api->error($response->get_error_message());
		}
		$response_body = wp_remote_retrieve_body($response);
		$result        = json_decode($response_body,true);
		$result        = apply_filters("mobile_api_before_login",$result);
		if (isset($result["user"]["id"])) {
			$user_id = $result["user"]["id"];
			update_user_meta($user_id,"mobile_api_token",$result["token"]);
			mobile_api_update_devices_token($user_id,$mobile_api->query->device_token);
			return $result;
		}else if (isset($result['message']) && $result['message'] != "") {
			$mobile_api->error($result['message']);
		}else {
			$mobile_api->error(esc_html__("Error in the generate token.","mobile-api"));
		}
	}

	public function forgot_password() {
		global $mobile_api;
		$data = $_GET;
		if (isset($data['form_type']) && $data['form_type'] == "wpqa_forget" && has_askme()) {
			$data['form_type'] = 'ask-forget';
		}
		if (isset($data['form_type']) && $data['form_type'] == mobile_api_forget()) {
			$return = mobile_api_forgot_password($data);
			if (is_wp_error($return)) {
				$mobile_api->error(strip_tags($return->get_error_message()));
			}else {
				return array("status" => true);
			}
		}
	}

	public function logout() {
		global $mobile_api;
		$user_id = get_current_user_id();
		$device_token = $mobile_api->query->device_id;
		if ($device_token != "" && $user_id > 0) {
			mobile_api_remove_devices_token($user_id,$device_token);
		}
		return true;
	}

	public function get_avatar() {
		global $mobile_api;
		if (!$mobile_api->query->size) {
			$mobile_api->error(esc_html__("You must include size var in your request.","mobile-api"));
		}
		$user_id = get_current_user_id();
		$avatar = mobile_api_user_avatar_link(array("user_id" => $user_id,"size" => $mobile_api->query->size,"user_name" => get_the_author_meta('display_name',$user_id)));
		return array('avatar' => $avatar);
	}

	public function confirm_account() {
		global $mobile_api;
		$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
		if (!$auth) {
			$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
		}
		if (!$auth) {
			$mobile_api->error(esc_html__('Authorization header not found.','mobile-api'));
		}
		if (!is_user_logged_in()) {
			$mobile_api->error(esc_html__("Please login first.","mobile-api"));
		}
		$user_id = get_current_user_id();
		$user_email = get_user_meta($user_id,"wpqa_edit_email",true);
		mobile_api_resend_confirmation($user_id,($user_email != ""?"edit":""));
		return array("message" => esc_html__("Check your mail again.","mobile-api"));
	}

	public function get_profile_tabs() {
		global $mobile_api;
		$user_id = (isset($_GET['user_id']) && $_GET['user_id']?(int)$_GET['user_id']:'');
		$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
		if (!$auth) {
			$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
		}
		if ($user_id == "") {
			if (!$auth) {
				$mobile_api->error(esc_html__('Authorization header not found.','mobile-api'));
			}
			if (!is_user_logged_in()) {
				$mobile_api->error(esc_html__("Please login to see your profile.","mobile-api"));
			}
			$get_current_user_id = get_current_user_id();
		}
		$get_user_id = ($user_id > 0?$user_id:get_current_user_id());
		$get_tab = esc_attr(isset($_GET['get_tab']) && $_GET['get_tab']?$_GET['get_tab']:'');
		if ($get_tab != '') {
			$get_tab = str_ireplace('get_','',$get_tab);
			$get_tab_another = $get_tab;
			$get_tab = str_ireplace('_','-',$get_tab);
			if (has_askme()) {
				$user_profile_pages = mobile_api_options("user_profile_pages");
				if ((isset($user_profile_pages[$get_tab]['value']) && $user_profile_pages[$get_tab]['value'] == $get_tab) || (isset($user_profile_pages[$get_tab_another]['value']) && $user_profile_pages[$get_tab_another]['value'] == $get_tab_another)) {
					$available_tab = true;
				}
			}else {
				$profile_page_menu = 'profile_page_menu';
				$locations = get_nav_menu_locations();
				if (isset($locations[$profile_page_menu])) {
				    $menu_profile_items = wp_get_nav_menu_items($locations[$profile_page_menu]);
					if (is_array($menu_profile_items) && !empty($menu_profile_items)) {
						foreach ($menu_profile_items as $menu_key => $menu_value) {
							if (strpos($menu_value->url,'#wpqa-') !== false) {
								$first_one = str_ireplace("#wpqa-","",$menu_value->url);
								$first_one = str_ireplace("_","-",$first_one);
								if ($first_one == $get_tab || $first_one == $get_tab_another) {
									$available_tab = true;
								}
							}
						}
					}
				}
			}
			if (isset($available_tab)) {
				$paged = ($mobile_api->query->paged != ""?(int)$mobile_api->query->paged:($mobile_api->query->page != ""?(int)$mobile_api->query->page:1));
				$active_points = mobile_api_options('active_points');
				$ask_question_to_users = mobile_api_options('ask_question_to_users');
				$pay_ask = mobile_api_options('pay_ask');
				if ($get_tab == "followers-answers" || $get_tab == "followers-comments" || $get_tab == "followers-questions" || $get_tab == "followers-posts" || $get_tab == "followers_answers" || $get_tab == "followers_comments" || $get_tab == "followers_questions" || $get_tab == "followers_posts") {
					$following_me = get_user_meta($get_user_id,"following_me",true);
					$block_users = mobile_api_options("block_users");
					$author__not_in = array();
					if ($block_users == mobile_api_checkbox_value) {
						if ($get_user_id > 0) {
							$get_block_users = get_user_meta($get_user_id,mobile_api_action_prefix()."_block_users",true);
						}
					}
					if (is_array($following_me) && !empty($following_me) && isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)) {
						$following_me = array_diff($following_me,$get_block_users);
					}
				}
				if ($get_tab == "answers" || $get_tab == "best-answers" || $get_tab == "best_answers" || $get_tab == "comments" || $get_tab == "followers-answers" || $get_tab == "followers-comments" || $get_tab == "followers_answers" || $get_tab == "followers_comments") {
					$comments_merge = array(($get_tab == "followers-answers" || $get_tab == "followers-comments" || $get_tab == "followers_answers" || $get_tab == "followers_comments"?"author__in":"user_id") => ($get_tab == "followers-answers" || $get_tab == "followers-comments" || $get_tab == "followers_answers" || $get_tab == "followers_comments"?$following_me:$get_user_id));
					$introspector = new MOBILE_API_Introspector();
					$post_type = ($get_tab == "answers" || $get_tab == "best-answers" || $get_tab == "best_answers" || $get_tab == "followers-answers" || $get_tab == "followers_answers"?mobile_api_questions_type:"post");
					$answers_sort = mobile_api_options("mobile_answers_sort");
					$rows_per_page = get_option('posts_per_page');
					$paged   = mobile_api_paged();
					$offset  = ($paged -1) * $rows_per_page;
					$current = max(1,$paged);
					if (($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) && isset($answers_sort) && $answers_sort == "reacted") {
						$comments_args = array('orderby' => array('meta_value_num' => 'DESC','comment_date' => 'ASC'),'meta_key' => 'wpqa_reactions_count','order' => 'DESC');
					}else if (($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) && isset($answers_sort) && $answers_sort == "voted") {
						$comments_args = array('orderby' => array('meta_value_num' => 'DESC','comment_date' => 'ASC'),'meta_key' => 'comment_vote','order' => 'DESC');
					}else if (isset($answers_sort) && $answers_sort == "oldest") {
						$comments_args = array('orderby' => 'comment_date','order' => 'ASC');
					}else {
						$comments_args = array('orderby' => 'comment_date','order' => 'DESC');
					}
					if ($get_tab == "best-answers" || $get_tab == "best_answers") {
						$meta_query = array("meta_query" => array(array("key" => "best_answer_comment","compare" => "=","value" => "best_answer_comment")));
						$comments_args = array();
						$total = mobile_api_count_best_answers_meta($get_user_id);
					}else {
						$meta_query = array();
						$total = mobile_api_count_comments_meta($post_type,$get_user_id);
					}
					$comments_args = array_merge($comments_merge,$meta_query,$comments_args,array('post_type' => $post_type,'parent' => 0,'status' => 'approve','number' => $rows_per_page,'offset' => $offset));
					$comments = get_comments($comments_args);
					$i = $s = 0;
					$replies = array();
					if (is_array($comments) && !empty($comments)) {
						foreach($comments as $comment) {
							$comments[$i] = new MOBILE_API_Comment($comment);
							$comments[$i]->replies = $introspector->get_replies($replies,$comment->comment_ID,$post_id,$post_type);
							$i++;
						}
					}
					return array("count" => $rows_per_page,"count_total" => $total,"pages" => ceil($total/$rows_per_page),"comments" => $comments);
				}else {
					if ($get_tab == "questions" || $get_tab == "posts" || (mobile_api_groups() && ($get_tab == "groups" || $get_tab_another == "joined_groups" || $get_tab_another == "managed_groups"))) {
						$array_data = array("author" => $get_user_id);
						$post_type = "post";
						if ($get_tab == "questions") {
							$post_type = mobile_api_questions_type;
						}else if ($get_tab == "groups" || $get_tab_another == "joined_groups" || $get_tab_another == "managed_groups") {
							$post_type = "group";
							if ($get_tab_another == "joined_groups") {
								$joined_groups = true;
								$groups_array = get_user_meta($get_user_id,"groups_array",true);
								$user_groups = (isset($joined_groups) && $joined_groups == true?array("post__in" => $groups_array):array());
							}else if ($get_tab_another == "managed_groups") {
								$managed_groups = true;
								$groups_moderator_array = get_user_meta($get_user_id,"groups_moderator_array",true);
								$user_groups = (isset($managed_groups) && $managed_groups == true?array("post__in" => $groups_moderator_array):array());
							}
							if (isset($user_groups) && ((!isset($joined_groups) && !isset($groups_moderator_array)) || (isset($groups_array) && is_array($groups_array) && !empty($groups_array) && count($groups_array) > 0) || (isset($groups_moderator_array) && is_array($groups_moderator_array) && !empty($groups_moderator_array) && count($groups_moderator_array) > 0))) {
								$array_data = $user_groups;
							}
						}
						$query = array_merge($array_data,array("post_type" => $post_type,"paged" => $paged,"ignore_sticky_posts" => 1));
					}else if ($get_tab == "polls") {
						$query = array("author" => $get_user_id,"post_type" => mobile_api_questions_type,"paged" => $paged,"meta_query" => array(array("key" => "question_poll","value" => mobile_api_checkbox_value,"compare" => "=")));
					}else if ($get_tab == "favorites") {
						if (has_askme()) {
							$user_login_id = get_user_by("id",$get_user_id);
							$_favorites = get_user_meta($get_user_id,$user_login_id->user_login."_favorites",true);
						}else {
							$_favorites = get_user_meta($get_user_id,$get_user_id."_favorites",true);
						}
						if (is_array($_favorites) && !empty($_favorites) && count($_favorites) > 0) {
							$query = array("post_type" => mobile_api_questions_type,"paged" => $paged,"post__in" => $_favorites);
						}
					}else if ($ask_question_to_users == mobile_api_checkbox_value && ($get_tab == "asked" || $get_tab == "asked-questions" || $get_tab == "asked_questions")) {
						if ($get_tab == "asked") {
							$meta_asked = array("key" => "user_is_comment","value" => true,"compare" => "=");
						}else {
							$meta_asked = array("key" => "user_is_comment","compare" => "NOT EXISTS");
						}
						$query = array("post_type" => mobile_api_asked_questions_type,"paged" => $paged,"meta_query" => array(array_merge(array($meta_asked),array(array("type" => "numeric","key" => "user_id","value" => (int)$get_user_id,"compare" => "=")))));
					}else if (($pay_ask == "on" || $pay_ask == 1) && ($get_tab == "paid-questions" || $get_tab == "paid_questions")) {
						$query = array("author" => $get_user_id,"post_type" => mobile_api_questions_type,"paged" => $paged,"meta_query" => array(array('type' => 'numeric',"key" => "_paid_question","value" => 'paid',"compare" => "=")));
					}else if ($get_tab == "followed") {
						$following_questions_user = get_user_meta($get_user_id,"following_questions",true);
						if (is_array($following_questions_user) && !empty($following_questions_user) && count($following_questions_user) > 0) {
							$query = array("post_type" => mobile_api_questions_type,"paged" => $paged,"post__in" => $following_questions_user);
						}
					}else if ($get_tab == "followers-questions" || $get_tab == "followers-posts" || $get_tab == "followers_questions" || $get_tab == "followers_posts") {
						if (is_array($following_me) && count($following_me) > 0) {
							$query = array("post_type" => ($get_tab == "followers-questions" || $get_tab == "followers_questions"?mobile_api_questions_type:"post"),"paged" => $paged,"author__in" => $following_me,"ignore_sticky_posts" => 1);
						}
					}
					if (isset($query)) {
						$result = $mobile_api->introspector->get_posts($query,'counts');
					}
					$count = (int)(isset($query)?count($result->posts):0);
					$count_total = (int)(isset($query)?$result->found_posts:0);
					return array(
						'count'       => $count,
						'count_total' => $count_total,
						'pages'       => (isset($query) && $count_total > 0 && $count > 0?ceil($count_total/$count):0),
						'posts'       => (isset($query)?$result->posts:array()),
					);
				}
			}else {
				$mobile_api->error(esc_html__("This tab is not available.","mobile-api"));
			}
		}else {
			$mobile_api->error(esc_html__("Include get_tab var in your request.","mobile-api"));
		}
	}

	public function blocking() {
		global $mobile_api;
		$get_current_user_id = get_current_user_id();
		if ($get_current_user_id == 0) {
			$mobile_api->error(esc_html__("Please login to check your blocking users.","mobile-api"));
		}
		$get_block_users = get_user_meta($get_current_user_id,mobile_api_action_prefix()."_block_users",true);
		if (is_array($get_block_users) && !empty($get_block_users)) {
			$number      = get_option('posts_per_page');
			$paged       = mobile_api_paged();
			$offset      = ($paged-1)*$number;
			$users       = get_users(array('fields' => 'ID','include' => $get_block_users,'orderby' => 'registered'));
			$query       = get_users(array('offset' => $offset,'number' => $number,'include' => $get_block_users,'orderby' => 'registered'));
			$total_users = count($users);
			$total_query = count($query);
			$total_pages = ceil($total_users/$number);
			$current     = max(1,$paged);
			foreach ($query as $user) {
				$active_points_category = mobile_api_options("active_points_category");
				if ($active_points_category == "on" || $active_points_category == 1) {
					$categories_user_points = get_user_meta($user->ID,"categories_user_points",true);
					if (is_array($categories_user_points) && !empty($categories_user_points)) {
						foreach ($categories_user_points as $category) {
							$points_category_user[$category] = (int)get_user_meta($user->ID,"points_category".$category,true);
						}
						arsort($points_category_user);
						$first_category = (is_array($points_category_user)?key($points_category_user):"");
						$first_points = reset($points_category_user);
					}
				}
				$verified_user = get_the_author_meta('verified_user',$user->ID);
				$badge_color = mobile_api_get_badge($user->ID,"color",(isset($first_points)?$first_points:""));
				$users_array[] = array(
					"id"          => $user->ID,
					"displayname" => $user->display_name,
					"avatar"      => mobile_api_user_avatar_link(array("user_id" => $user->ID,"size" => 128)),
					"verified"    => ($verified_user == 1 || $verified_user == "on"?true:false),
					"badge"       => array("name" => strip_tags(mobile_api_get_badge($user->ID,"name",(isset($first_points)?$first_points:""))),"color" => ($badge_color != ""?$badge_color:""),"textColor" => "FFFFFF","textColorDark" => "FFFFFF")
				);
			}
		}
		return array(
			"count" => (int)(isset($users) && is_array($users)?count($users):0),
			"pages" => (isset($total_pages)?$total_pages:0),
			"users" => (isset($users_array)?$users_array:array())
		);
	}
	
	public function block() {
		global $mobile_api;
		$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
		if (!$auth) {
			$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
		}
		if (!$auth) {
			$mobile_api->error(esc_html__('Authorization header not found.','mobile-api'));
		}
		$id = esc_attr(isset($_GET['id']) && $_GET['id']?$_GET['id']:'');
		$action = esc_attr(isset($_GET['action']) && $_GET['action']?$_GET['action']:'');
		if ($id == '') {
			$mobile_api->error(esc_html__("Include id var in your request.","mobile-api"));
		}
		if ($action == '') {
			$mobile_api->error(esc_html__("Include action var in your request.","mobile-api"));
		}
		$user_id = get_current_user_id();
		$_POST['mobile'] = true;
		$_POST['block_type'] = $action;
		$explode = explode(",",$id);
		foreach ($explode as $value) {
			if ($value > 0) {
				$_POST['user_id'] = $value;
				$return = mobile_api_blocking();
			}
		}
		return array(
			"blocked" => ($action == "block"?true:false)
		);
	}

	public function following() {
		global $mobile_api;
		$user_id = (isset($_GET['user_id']) && $_GET['user_id']?(int)$_GET['user_id']:'');
		if ($user_id == '') {
			$mobile_api->error(esc_html__("Include user_id var in your request.","mobile-api"));
		}
		$get_current_user_id = get_current_user_id();
		$following_me = get_user_meta($user_id,"following_me",true);
		$block_users = mobile_api_options("block_users");
		if ($block_users == mobile_api_checkbox_value) {
			if ($user_id > 0) {
				$get_block_users = get_user_meta($user_id,mobile_api_action_prefix()."_block_users",true);
			}
		}
		if (is_array($following_me) && !empty($following_me) && isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)) {
			$following_me = array_diff($following_me,$get_block_users);
		}
		if (is_array($following_me) && !empty($following_me)) {
			$number      = get_option('posts_per_page');
			$paged       = mobile_api_paged();
			$offset      = ($paged-1)*$number;
			$users       = get_users(array('fields' => 'ID','include' => $following_me,'orderby' => 'registered'));
			$query       = get_users(array('offset' => $offset,'number' => $number,'include' => $following_me,'orderby' => 'registered'));
			$total_users = count($users);
			$total_query = count($query);
			$total_pages = ceil($total_users/$number);
			$current     = max(1,$paged);
			foreach ($query as $user) {
				$active_points_category = mobile_api_options("active_points_category");
				if ($active_points_category == "on" || $active_points_category == 1) {
					$categories_user_points = get_user_meta($user->ID,"categories_user_points",true);
					if (is_array($categories_user_points) && !empty($categories_user_points)) {
						foreach ($categories_user_points as $category) {
							$points_category_user[$category] = (int)get_user_meta($user->ID,"points_category".$category,true);
						}
						arsort($points_category_user);
						$first_category = (is_array($points_category_user)?key($points_category_user):"");
						$first_points = reset($points_category_user);
					}
				}
				$verified_user = get_the_author_meta('verified_user',$user->ID);
				$following_you  = get_user_meta($user->ID,"following_you",true);
				$second_info = (($get_current_user_id > 0 && $get_current_user_id != $user->ID)?array("followed" => (!empty($following_you) && in_array($get_current_user_id,$following_you)?true:false)):array());
				$badge_color = mobile_api_get_badge($user->ID,"color",(isset($first_points)?$first_points:""));
				$users_array[] = array_merge(array(
					"id"          => $user->ID,
					"displayname" => $user->display_name,
					"avatar"      => mobile_api_user_avatar_link(array("user_id" => $user->ID,"size" => 128)),
					"verified"    => ($verified_user == 1 || $verified_user == "on"?true:false),
					"badge"       => array("name" => strip_tags(mobile_api_get_badge($user->ID,"name",(isset($first_points)?$first_points:""))),"color" => ($badge_color != ""?$badge_color:""),"textColor" => "FFFFFF","textColorDark" => "FFFFFF"),
				),$second_info);
			}
		}
		return array(
			"count" => (int)(isset($users) && is_array($users)?count($users):0),
			"pages" => (isset($total_pages)?$total_pages:0),
			"users" => (isset($users_array)?$users_array:array())
		);
	}

	public function followers() {
		global $mobile_api;
		$user_id = (isset($_GET['user_id']) && $_GET['user_id']?(int)$_GET['user_id']:'');
		if ($user_id == '') {
			$mobile_api->error(esc_html__("Include user_id var in your request.","mobile-api"));
		}
		$get_current_user_id = get_current_user_id();
		$following_you = get_user_meta($user_id,"following_you",true);
		if (is_array($following_you) && !empty($following_you)) {
			$number      = get_option('posts_per_page');
			$paged       = mobile_api_paged();
			$offset      = ($paged-1)*$number;
			$users       = get_users(array('fields' => 'ID','include' => $following_you,'orderby' => 'registered'));
			$query       = get_users(array('offset' => $offset,'number' => $number,'include' => $following_you,'orderby' => 'registered'));
			$total_users = count($users);
			$total_query = count($query);
			$total_pages = ceil($total_users/$number);
			$current     = max(1,$paged);
			foreach ($query as $user) {
				$active_points_category = mobile_api_options("active_points_category");
				if ($active_points_category == "on" || $active_points_category == 1) {
					$categories_user_points = get_user_meta($user->ID,"categories_user_points",true);
					if (is_array($categories_user_points) && !empty($categories_user_points)) {
						foreach ($categories_user_points as $category) {
							$points_category_user[$category] = (int)get_user_meta($user->ID,"points_category".$category,true);
						}
						arsort($points_category_user);
						$first_category = (is_array($points_category_user)?key($points_category_user):"");
						$first_points = reset($points_category_user);
					}
				}
				$verified_user = get_the_author_meta('verified_user',$user->ID);
				$following_you  = get_user_meta($user->ID,"following_you",true);
				$second_info = (($get_current_user_id > 0 && $get_current_user_id != $user->ID)?array("followed" => (!empty($following_you) && in_array($get_current_user_id,$following_you)?true:false)):array());
				$badge_color = mobile_api_get_badge($user->ID,"color",(isset($first_points)?$first_points:""));
				$users_array[] = array_merge(array(
					"id"          => $user->ID,
					"displayname" => $user->display_name,
					"avatar"      => mobile_api_user_avatar_link(array("user_id" => $user->ID,"size" => 128)),
					"verified"    => ($verified_user == 1 || $verified_user == "on"?true:false),
					"badge"       => array("name" => strip_tags(mobile_api_get_badge($user->ID,"name",(isset($first_points)?$first_points:""))),"color" => ($badge_color != ""?$badge_color:""),"textColor" => "FFFFFF","textColorDark" => "FFFFFF"),
				),$second_info);
			}
		}
		return array(
			"count" => (int)(isset($users) && is_array($users)?count($users):0),
			"pages" => (isset($total_pages)?$total_pages:0),
			"users" => (isset($users_array)?$users_array:array())
		);
	}

	public function edit_profile() {
		global $mobile_api;
		$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
		if (!$auth) {
			$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
		}
		if (!$auth) {
			$mobile_api->error(esc_html__('Authorization header not found.','mobile-api'));
		}
		$user_id = get_current_user_id();
		if (!is_user_logged_in()) {
			$mobile_api->error(esc_html__("Please login to edit profile.","mobile-api"));
		}
		if (empty($_POST['email'])) {
			$mobile_api->error(esc_html__("There are required fields (Email).","mobile-api"));
		}
		if (!is_email(trim($_POST['email']))) {
			$mobile_api->error(esc_html__("Please write correctly email.","mobile-api"));
		}
		$user = get_user_by('id',$user_id);
		if (email_exists($_POST['email']) && $user->user_email != $_POST['email']) {
			$mobile_api->error(esc_html__("This email is already registered, please choose another one.","mobile-api"));
		}
		if (isset($_POST['password']) && $_POST['password'] != "") {
			$password = $_POST['pass1'] = $_POST['pass2'] = sanitize_text_field($_POST['password']);
		}
		$_POST["mobile"] = true;
		$user_meta_avatar = mobile_api_avatar_name();
		if (isset($_FILES['avatar'])) {
			$_FILES[$user_meta_avatar] = $_FILES['avatar'];
		}

		if (has_wpqa()) {
			$user_meta_cover = wpqa_cover_name();
			if (isset($_FILES['cover'])) {
				$_FILES[$user_meta_cover] = $_FILES['cover'];
			}
			$array_following = array("selected_tags","unselected_tags","selected_catgeories","unselected_catgeories");
			foreach ($array_following as $value) {
				if (isset($_POST[$value])) {
					$explode = explode(",",$_POST[$value]);
					$type = (($value == "selected_tags" || $value == "unselected_tags")?"tag":"category");
					$action = (($value == "selected_tags" || $value == "selected_catgeories")?"follow":"unfollow");
					$_POST['tax_type'] = $type;
					foreach ($explode as $value_2) {
						$_POST['tax_id'] = $value_2;
						if ($action == "follow") {
							wpqa_follow_cat();
						}else {
							wpqa_unfollow_cat();
						}
					}
				}
			}
		}
		if (has_askme()) {
			if (isset($_POST['gender'])) {
				$_POST['sex'] = $_POST['gender'];
			}
			if (isset($_POST['follow_email']) && $_POST['follow_email'] == "on") {
				$_POST['follow_email'] = 1;
			}
			if (isset($_POST['received_message']) && $_POST['received_message'] == "on") {
				$_POST['received_message'] = 1;
			}
			if (isset($_POST['show_point_favorite']) && $_POST['show_point_favorite'] == "on") {
				$_POST['show_point_favorite'] = 1;
			}
			if (isset($_POST['received_email']) && $_POST['received_email'] == "on") {
				$_POST['received_email'] = 1;
			}
			if (isset($_POST['received_email_post']) && $_POST['received_email_post'] == "on") {
				$_POST['received_email_post'] = 1;
			}
			if (isset($_POST['age']) && $_POST['age'] != "") {
				$_POST['age'] = (date_create($_POST['age'])?date_diff(date_create($_POST['age']),date_create('today'))->y:$_POST['age']);
			}
		}
		if (isset($_POST['website'])) {
			$_POST['url'] = $_POST['website'];
		}

		$_POST['admin_bar_front'] = get_user_meta($user_id,"show_admin_bar_front",true);

		$return = mobile_api_process_edit_profile($_POST,$user_id);
		if (is_wp_error($return)) {
			$mobile_api->error(strip_tags(wptexturize(str_ireplace(array(" :&nbsp;",":&nbsp;","&#039;"),array(":",":","'"),$return->get_error_message()))));
		}else {
			$user_id = $return;
		}
		return array("updated" => true,"user" => mobile_api_user_info($user_id,"login"));
	}

	public function upload_avatar() {
		global $mobile_api;
		$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
		if (!$auth) {
			$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
		}
		if (!$auth) {
			$mobile_api->error(esc_html__('Authorization header not found.','mobile-api'));
		}
		if (!is_user_logged_in()) {
			$mobile_api->error(esc_html__("Please login to edit profile.","mobile-api"));
		}
		$user_id = get_current_user_id();
		$user_meta_avatar = mobile_api_avatar_name();
		if (isset($_FILES['avatar'])) {
			$_FILES[$user_meta_avatar] = $_FILES['avatar'];
		}else {
			$mobile_api->error(esc_html__("Please upload your image.","mobile-api"));
		}
		if (isset($_FILES[$user_meta_avatar])) {
			require_once(ABSPATH.'wp-admin/includes/image.php');
			require_once(ABSPATH.'wp-admin/includes/file.php');
		}

		if (isset($_FILES[$user_meta_avatar]) && !empty($_FILES[$user_meta_avatar]['name'])) :
			$your_avatar = wp_handle_upload($_FILES[$user_meta_avatar],array('test_form' => false),current_time('mysql'));
			if ($your_avatar && isset($your_avatar["url"])) :
				$filename = $your_avatar["file"];
				$filetype = wp_check_filetype( basename( $filename ), null );
				$wp_upload_dir = wp_upload_dir();
				
				$attachment = array(
					'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
					'post_mime_type' => $filetype['type'],
					'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
					'post_content'   => '',
					'post_status'    => 'inherit'
				);
				$attach_id = wp_insert_attachment($attachment,$filename);
				$attach_data = wp_generate_attachment_metadata($attach_id,$filename);
				wp_update_attachment_metadata($attach_id,$attach_data);
				$meta_for_avatar = $attach_id;
			endif;
			if (isset($your_avatar['error']) && $your_avatar) :
				$mobile_api->error(esc_html__('Error in upload the image : ','mobile-api') . $your_avatar['error']);
			endif;
		endif;
		if (isset($meta_for_avatar)) {
			update_user_meta($user_id,$user_meta_avatar,$meta_for_avatar);
		}
		return array("updated" => true);
	}

	public function upload_cover() {
		global $mobile_api;
		if (!has_wpqa()) {
			$mobile_api->error(esc_html__('The cover is not available.','mobile-api'));
		}
		$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
		if (!$auth) {
			$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
		}
		if (!$auth) {
			$mobile_api->error(esc_html__('Authorization header not found.','mobile-api'));
		}
		$user_id = get_current_user_id();
		if (!is_user_logged_in()) {
			$mobile_api->error(esc_html__("Please login to edit profile.","mobile-api"));
		}
		$user_meta_cover = wpqa_cover_name();
		if (isset($_FILES['cover'])) {
			$_FILES[$user_meta_cover] = $_FILES['cover'];
		}else {
			$mobile_api->error(esc_html__("Please upload your image.","mobile-api"));
		}
		if (isset($_FILES[$user_meta_cover])) {
			require_once(ABSPATH.'wp-admin/includes/image.php');
			require_once(ABSPATH.'wp-admin/includes/file.php');
		}
		
		if (isset($_FILES[$user_meta_cover]) && !empty($_FILES[$user_meta_cover]['name'])) :
			$your_cover = wp_handle_upload($_FILES[$user_meta_cover],array('test_form' => false),current_time('mysql'));
			if ($your_cover && isset($your_cover["url"])) :
				$filename = $your_cover["file"];
				$filetype = wp_check_filetype( basename( $filename ), null );
				$wp_upload_dir = wp_upload_dir();
				
				$attachment = array(
					'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
					'post_mime_type' => $filetype['type'],
					'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
					'post_content'   => '',
					'post_status'    => 'inherit'
				);
				$attach_id = wp_insert_attachment($attachment,$filename);
				$attach_data = wp_generate_attachment_metadata($attach_id,$filename);
				wp_update_attachment_metadata($attach_id,$attach_data);
				$meta_for_cover = $attach_id;
			endif;
			if (isset($your_cover['error']) && $your_cover) :
				$mobile_api->error(esc_html__('Error in upload the image : ','mobile-api') . $your_cover['error']);
			endif;
		endif;
		if (isset($meta_for_cover)) {
			update_user_meta($user_id,$user_meta_cover,$meta_for_cover);
		}
		return array("updated" => true);
	}

	public function get_following() {
		global $mobile_api;
		$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
		if (!$auth) {
			$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
		}
		if (!$auth) {
			$mobile_api->error(esc_html__('Authorization header not found.','mobile-api'));
		}
		$user_id = get_current_user_id();
		if (!is_user_logged_in()) {
			$mobile_api->error(esc_html__("Please login to see your following.","mobile-api"));
		}
		$type = esc_attr(isset($_GET['type']) && $_GET['type']?$_GET['type']:'');
		if ($type == "category" || $type == "tag") {
			$term_user_key = ($type == "tag"?"user_tag_follow":"user_cat_follow");
			$cat_type = ($type == "tag"?mobile_api_question_tags:mobile_api_question_categories);
			$user_cat_follow = get_user_meta($user_id,$term_user_key,true);
			$number       = esc_attr(isset($_GET['count']) && $_GET['count']?$_GET['count']:get_option('posts_per_page'));
			$paged        = mobile_api_paged();
			$offset       = ($paged-1)*$number;
			$cats         = get_terms($cat_type,array('hide_empty' => 0,'include' => $user_cat_follow));
			$terms        = get_terms($cat_type,array(
				'orderby'    => $cat_sort,
				'order'      => $cat_order,
				'number'     => $number,
				'offset'     => $offset,
				'hide_empty' => 0,
				'include'    => $user_cat_follow
			));
			$total_users  = count($cats);
			$total_query  = count($terms);
			$total_pages  = ceil($total_users/$number);
			$current      = max(1,$paged);
			foreach ($terms as $term) {
				$cats_array[] = array(
					"term_id" => $term->term_id,
					"name"    => $term->name,
				);
			}
			return array(
				"count" => (int)(isset($cats) && is_array($cats)?count($cats):0),
				"pages" => (isset($total_pages)?$total_pages:0),
				"cats"  => (isset($cats_array)?$cats_array:array())
			);
		}else {
			$following_me = get_user_meta($user_id,"following_me",true);
			$block_users = mobile_api_options("block_users");
			if ($block_users == mobile_api_checkbox_value) {
				if ($user_id > 0) {
					$get_block_users = get_user_meta($user_id,mobile_api_action_prefix()."_block_users",true);
				}
			}
			if (is_array($following_me) && !empty($following_me) && isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)) {
				$following_me = array_diff($following_me,$get_block_users);
			}
			$number       = esc_attr(isset($_GET['count']) && $_GET['count']?$_GET['count']:get_option('posts_per_page'));
			$paged        = mobile_api_paged();
			$offset       = ($paged-1)*$number;
			$users        = get_users(array('fields' => 'ID','include' => $following_me,'orderby' => 'registered'));
			$query        = get_users(array('offset' => $offset,'include' => $following_me,'number' => $number,'orderby' => 'registered'));
			$total_users  = count($users);
			$total_query  = count($query);
			$total_pages  = ceil($total_users/$number);
			$current      = max(1,$paged);
			foreach ($query as $user) {
				$users_array[] = array(
					"id"          => $user->ID,
					"displayname" => $user->display_name,
				);
			}
			return array(
				"count" => (int)(isset($users) && is_array($users)?count($users):0),
				"pages" => (isset($total_pages)?$total_pages:0),
				"users" => (isset($users_array)?$users_array:array())
			);
		}
	}

	public function get_favorites() {
		global $mobile_api;
		$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
		if (!$auth) {
			$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
		}
		if (!$auth) {
			$mobile_api->error(esc_html__('Authorization header not found.','mobile-api'));
		}
		$user_id = get_current_user_id();
		if (!is_user_logged_in()) {
			$mobile_api->error(esc_html__("Please login to see your favorites.","mobile-api"));
		}
		if (has_askme()) {
			$user_login_id = get_user_by("id",$user_id);
			$_favorites = get_user_meta($user_id,$user_login_id->user_login."_favorites",true);
		}else {
			$_favorites = get_user_meta($user_id,$user_id."_favorites",true);
		}
		if (is_array($_favorites) && !empty($_favorites) && count($_favorites) > 0) {
			$query = array("post_type" => mobile_api_questions_type,"paged" => $paged,"post__in" => $_favorites);
		}
		$result = $mobile_api->introspector->get_posts($query,'counts');
		$count_posts_favourites = mobile_api_options("count_posts_favourites");
		$count = (int)$count_posts_favourites;
		$count_total = (int)(isset($result->found_posts)?$result->found_posts:0);
		return array(
			'count'       => $count,
			'count_total' => $count_total,
			'pages'       => ($count > 0?ceil($count_total/$count):0),
			'posts'       => $result->posts,
		);
	}

	public function favorite() {
		global $mobile_api;
		$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
		if (!$auth) {
			$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
		}
		if (!$auth) {
			$mobile_api->error(esc_html__('Authorization header not found.','mobile-api'));
		}
		$user_id = get_current_user_id();
		if (!is_user_logged_in()) {
			$mobile_api->error(esc_html__("Please login to see your favorites.","mobile-api"));
		}
		$action = esc_attr(isset($_GET['action']) && $_GET['action']?$_GET['action']:'');
		$id = esc_attr(isset($_GET['id']) && $_GET['id']?$_GET['id']:'');
		if ($id == '') {
			$mobile_api->error(esc_html__("Include id var in your request.","mobile-api"));
		}
		if ($action == '') {
			$mobile_api->error(esc_html__("Include action var in your request.","mobile-api"));
		}
		$_POST['mobile'] = true;
		$_POST['post_id'] = $id;
		mobile_api_favorite($action);
		return array(
			"favorite" => ($action == "add"?true:false)
		);
	}

	public function followed_questions() {
		global $mobile_api;
		$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
		if (!$auth) {
			$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
		}
		if (!$auth) {
			$mobile_api->error(esc_html__('Authorization header not found.','mobile-api'));
		}
		$user_id = get_current_user_id();
		if (!is_user_logged_in()) {
			$mobile_api->error(esc_html__("Please login to see your following.","mobile-api"));
		}
		$following_questions = get_user_meta($user_id,"following_questions",true);
		if (is_array($following_questions) && !empty($following_questions) && count($following_questions) > 0) {
			$query = array("post_type" => mobile_api_questions_type,"paged" => $paged,"post__in" => $following_questions);
		}
		$result = $mobile_api->introspector->get_posts($query,'counts');
		$count = (int)(isset($query)?count($result->posts):0);
		$count_total = (int)(isset($query)?$result->found_posts:0);
		return array(
			'count'       => $count,
			'count_total' => $count_total,
			'pages'       => (isset($query) && $count_total > 0 && $count > 0?ceil($count_total/$count):0),
			'posts'       => (isset($query)?$result->posts:array()),
		);
	}

	public function question_follow() {
		global $mobile_api;
		$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
		if (!$auth) {
			$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
		}
		if (!$auth) {
			$mobile_api->error(esc_html__('Authorization header not found.','mobile-api'));
		}
		$user_id = get_current_user_id();
		if (!is_user_logged_in()) {
			$mobile_api->error(esc_html__("Please login to see your following.","mobile-api"));
		}
		$action = esc_attr(isset($_GET['action']) && $_GET['action']?$_GET['action']:'');
		$id = esc_attr(isset($_GET['id']) && $_GET['id']?$_GET['id']:'');
		if ($id == '') {
			$mobile_api->error(esc_html__("Include id var in your request.","mobile-api"));
		}
		if ($action == '') {
			$mobile_api->error(esc_html__("Include action var in your request.","mobile-api"));
		}
		$_POST['mobile'] = true;
		$_POST['post_id'] = $id;
		mobile_api_question_follow($action);
		$following_questions = get_post_meta($id,"following_questions",true);
		$following_questions = (is_array($following_questions) && !empty($following_questions)?get_users(array('fields' => 'ID','include' => $following_questions,'orderby' => 'registered')):array());
		return array(
			"followed" => ($action == "add"?true:false),
			"followers" => (is_array($following_questions) && !empty($following_questions)?count($following_questions):0)
		);
	}

	public function post_comment() {
		global $mobile_api;

		if (!$mobile_api->query->cookie) {
			$mobile_api->error(esc_html__("You must include a cookie var in your request. Use the login method.","mobile-api"));
		}

		$user_id = wp_validate_auth_cookie($mobile_api->query->cookie, 'logged_in');

		if (!$user_id) {
			$mobile_api->error(esc_html__("Invalid cookie. Use the login method.","mobile-api"));
		}

		if (!$mobile_api->query->post_id) {
			$mobile_api->error(esc_html__("No post specified. Include post_id var in your request.","mobile-api"));
		} elseif (!$mobile_api->query->content) {
			$mobile_api->error(esc_html__("Please include content var in your request.","mobile-api"));
		}

		if (!isset($mobile_api->query->comment_status)) {
			$mobile_api->error(esc_html__("Please include comment_status var in your request. Possible values are comment_status=1 (approved) or comment_status=hold (not-approved)","mobile-api"));
		} else {
			$comment_status = $mobile_api->query->comment_status;
		}

		if ($comment_status == 'hold') {
			$comment_status = 0;
		}

		$user_info = get_userdata($user_id);

		$time  = current_time('mysql');
		$agent = $_SERVER['HTTP_USER_AGENT'];
		$ip    = $_SERVER['REMOTE_ADDR'];

		$data = array(
			'comment_post_ID'      => $mobile_api->query->post_id,
			'comment_author'       => $user_info->user_login,
			'comment_author_email' => $user_info->user_email,
			'comment_author_url'   => $user_info->user_url,
			'comment_content'      => $mobile_api->query->content,
			'comment_type'         => '',
			'comment_parent'       => 0,
			'user_id'              => $user_info->ID,
			'comment_author_IP'    => $ip,
			'comment_agent'        => $agent,
			'comment_date'         => $time,
			'comment_approved'     => $comment_status,
		);

		$comment_id = wp_insert_comment($data);

		return array(
			"comment_id" => $comment_id,
		);
	}

	public function get_rewarded() {
		$user_id = get_current_user_id();
		$points_rewarded = (int)mobile_api_options("points_rewarded");
		if ($points_rewarded > 0) {
			mobile_api_add_points($user_id,$points_rewarded,"+","rewarded_adv");
		}
	}
}
?>
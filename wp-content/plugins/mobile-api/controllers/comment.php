<?php
/*
Controller name: Comment
Controller description: Comment/trackback submission methods
*/

class MOBILE_API_Comment_Controller {
  
	function submit_comment() {
		global $mobile_api;
		nocache_headers();
		if (empty($_REQUEST['post_id'])) {
			$mobile_api->error(esc_html__("No post specified. Include post_id var in your request.","mobile-api"));
		}
		$user_id = get_current_user_id();
		if ($user_id == 0) {
			if (empty($_REQUEST['name']) || empty($_REQUEST['email']) || empty($_REQUEST['content'])) {
				$mobile_api->error(esc_html__("Please include all required arguments (name, email, content).","mobile-api"));
			}else if (!is_email($_REQUEST['email'])) {
				$mobile_api->error(esc_html__("Please enter a valid email address.","mobile-api"));
			}
		}else {
			if (empty($_REQUEST['content'])) {
				$mobile_api->error(esc_html__("Please include all required arguments (content).","mobile-api"));
			}
		}
		if (has_askme()) {
			if (isset($_POST['anonymously_answer']) && $_POST['anonymously_answer'] == "on") {
				$_POST['anonymously_answer'] == 1;
			}
			if (isset($_POST['agree_terms']) && $_POST['agree_terms'] == "on") {
				$_POST['agree_terms'] == 1;
			}
			if (isset($_POST['video_answer_description']) && $_POST['video_answer_description'] == "on") {
				$_POST['video_answer_description'] == 1;
			}
		}
		$get_post_type_comment = get_post_type((int)$_REQUEST['post_id']);
		$comment_limit = (int)mobile_api_options(($get_post_type_comment == mobile_api_questions_type || $get_post_type_comment == mobile_api_asked_questions_type?"answer_limit":"comment_limit"));
		$comment_min_limit = (int)mobile_api_options(($get_post_type_comment == mobile_api_questions_type || $get_post_type_comment == mobile_api_asked_questions_type?"answer_min_limit":"comment_min_limit"));
		$comment_text = strip_tags($_REQUEST['content']);
		$comment_text = str_replace(array('<p>','</p>','<br>'),'',$comment_text);
		if ($comment_min_limit > 0 && strlen($comment_text) < $comment_min_limit) {
			$mobile_api->error(esc_html__("Sorry, The minimum characters is","mobile-api")." ".$comment_min_limit);
		}
		if ($comment_limit > 0 && strlen($comment_text) > $comment_limit) {
			$mobile_api->error(esc_html__("Sorry, The maximum characters is","mobile-api")." ".$comment_limit);
		}
		$_POST['added_mobile_app'] == 'yes';
		$pending = new MOBILE_API_Comment();
		$terms_active_comment = mobile_api_options("terms_active_comment");
		$activate_editor_reply = mobile_api_options("activate_editor_reply");
		if (!isset($_POST['form_type']) && $activate_editor_reply != "on" && $activate_editor_reply != 1 && ($terms_active_comment == "on" || $terms_active_comment == 1) && $_POST['agree_terms'] != "on" && $_POST['agree_terms'] != 1) {
			$mobile_api->error(esc_html__("There are required fields (Agree of the terms).","mobile-api"));
		}
		$return = $pending->handle_submission();
		if (is_wp_error($return)) {
			$mobile_api->error(strip_tags($return->get_error_message()));
		}else {
			return $return;
		}
	}

	function best_answer() {
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
		$_POST['mobile'] = true;
		$_POST['comment_id'] = $id;
		if ($action == "add") {
			mobile_api_add_best_answer();
		}else {
			mobile_api_remove_best_answer();
		}
		return array(
			"best_answer" => ($action == "add"?true:false)
		);
	}

	function get_comment() {
		global $mobile_api;
		if (isset($_POST)) {
			$data = $_POST;
			$comment_id = $mobile_api->query->comment_id;
			$edit = $mobile_api->query->edit;
			$comment = get_comment($comment_id);
			return new MOBILE_API_Comment($comment,"edit");
		}else {
			$mobile_api->error(esc_html__("Your request not POST.","mobile-api"));
		}
	}

	function edit_comment() {
		global $mobile_api;
		if (isset($_POST)) {
			$data = $_POST;
			$data = apply_filters("mobile_api_edit_comment_data_filter",$data);
			$data["form_type"] = "edit_comment";
			$data["mobile"] = true;
			if (isset($data["content"])) {
				if (has_askme()) {
					$data["comment_content"] = $data["content"];
				}else if (has_wpqa()) {
					$data["comment"] = $data["content"];
				}
			}
			$return = mobile_api_process_edit_comments($data);
			if (is_wp_error($return)) {
				$mobile_api->error(strip_tags(str_ireplace(array(" :&nbsp;",":&nbsp;","&#039;"),array(":",":","'"),$return->get_error_message())));
			}else {
				$comment = get_comment($return);
				$approved = $comment->comment_approved;
				$status = ($approved == 1 || $approved == "approved") ? true : 'pending';
				$new_comment = new MOBILE_API_Comment($comment);
				$mobile_api->response->respond($new_comment, $status);
			}
		}else {
			$mobile_api->error(esc_html__("Your request not POST.","mobile-api"));
		}
	}
 
	public function register_typeform() {
		global $mobile_api, $WishListMemberInstance;	  

		$inputJSON = file_get_contents('php://input');
		$json_input= json_decode( $inputJSON, TRUE ); //convert JSON into array
		$data_array = $json_input;

		foreach ($data_array['form_response']['answers'] as $k) {
			if ($k['field']['id']==56083034) $username= sanitize_user( strtolower(str_replace(' ', '.', $k['text'])));
			elseif ($k['field']['id']==56083040) $email= sanitize_email($k['email']);
		}

		while (username_exists($username)) {
			$i++;
			$username = $username.'.'.$i;
		}

		if ($mobile_api->query->display_name) $display_name = sanitize_text_field( $mobile_api->query->display_name );
		$user_pass = sanitize_text_field( $_REQUEST['user_pass'] );

		//Add usernames we don't want used

		$invalid_usernames = array( 'admin' );

		//Do username validation
		if ( !validate_username( $username ) || in_array( $username, $invalid_usernames ) ) {
			$mobile_api->error(esc_html__("Username is invalid.","mobile-api"));
		}else if ( username_exists( $username ) ) {
			$mobile_api->error(esc_html__("Username already exists.","mobile-api"));
		}else {
			if ( !is_email( $email ) ) {
				$mobile_api->error(esc_html__("E-mail address is invalid.","mobile-api"));
			}elseif (email_exists($email)) {
				$mobile_api->error(esc_html__("E-mail address is already in use.","mobile-api"));
			}else {
				//Everything has been validated, proceed with creating the user

				//Create the user
				if (!isset($_REQUEST['user_pass'])) {
					$user_pass = wp_generate_password();
					$_REQUEST['user_pass'] = $user_pass;
				}

				$_REQUEST['user_login'] = $username;
				$_REQUEST['user_email'] = $email;

				$allowed_params = array('user_login', 'user_email', 'user_pass', 'display_name', 'user_nicename', 'user_url', 'nickname', 'first_name',
					'last_name', 'description', 'rich_editing', 'user_registered', 'jabber', 'aim', 'yim',
					'comment_shortcuts', 'admin_color', 'use_ssl', 'show_admin_bar_front'
				);


				foreach($_REQUEST as $field => $value){
					if (in_array($field, $allowed_params) ) $user[$field] = trim(sanitize_text_field($value));
				}
				$user_id = register_new_user( $username, $email );
			}
		}

		if ($user_id && $reference) update_user_meta($user_id,'reference',$reference);
		$expiration = time() + apply_filters('mobile_api_auth_cookie_expiration', 1209600, $user_id, true);
		$cookie = wp_generate_auth_cookie($user_id, $expiration, 'logged_in');

		return array( 
			"cookie" => $cookie,	
			"user_id" => $user_id	
		);
	}
	
}?>
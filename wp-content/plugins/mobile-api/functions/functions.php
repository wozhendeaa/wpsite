<?php function mobile_api_comments_clauses($clauses) {
	global $wpdb;
	$clauses["groupby"] = "{$wpdb->comments}.comment_post_ID";
	return $clauses;
}
/* Count custom comments */
function mobile_api_count_custom_comments($post_type,$specific_date = "",$count_custom_posts = "") {
	global $wpdb;
	$date_query = "";
	if ($specific_date != "") {
		$date_query = "AND $wpdb->comments.comment_date > NOW() - INTERVAL ".str_replace("hours","hour",$specific_date);
	}
	$count = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT($wpdb->posts.ID)
			FROM $wpdb->posts
			LEFT JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id )
			LEFT JOIN $wpdb->postmeta AS mt1 ON ($wpdb->posts.ID = mt1.post_id AND mt1.meta_key = 'user_id' )
			".($date_query != ""?"LEFT JOIN $wpdb->comments ON ($wpdb->posts.ID = $wpdb->comments.comment_post_ID)":"")."
			WHERE 
			( ( $wpdb->postmeta.meta_key = 'count_post_all' AND $wpdb->postmeta.meta_value > 0 ) 
			AND mt1.post_id IS NULL ) 
			".($count_custom_posts != ""?"AND $wpdb->posts.ID IN (".$count_custom_posts.")":"")."
			".$date_query."
			AND $wpdb->posts.post_type = %s 
			AND ($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private')"
			,$post_type
		)
	);
	return $count;
}
/* Count comments */
function mobile_api_comments_of_post_type($post_type = null,$user_id = 0,$date = array(),$search = "",$parent = "") {
	global $wpdb;
	$post_type = (is_array($post_type)?$post_type:($post_type != ""?$post_type:"post"));
	$custom_post_type = "AND (";
	if (is_array($post_type)) {
		$key = 0;
		foreach ($post_type as $value) {
			if ($key != 0) {
				$custom_post_type .= " OR ";
			}
			$custom_post_type .= "$wpdb->posts.post_type = '$value'";
			$key++;
		}
	}else {
		$custom_post_type .= "$wpdb->posts.post_type = '$post_type'";
	}
	$custom_post_type .= ")";
	$date_query = "";
	if (is_array($date) && !empty($date)) {
		$date_query = "AND ( ( ";
		$date_query .= "YEAR( $wpdb->comments.comment_date ) = ".(isset($date["year"]) && $date["year"] != ""?$date["year"]:date("Y"));
		if (isset($date["month"]) && $date["month"] != "") {
			$date_query .= " AND MONTH( $wpdb->comments.comment_date ) = ".$date["month"];
		}
		if (isset($date["day"]) && $date["day"] != "") {
			$date_query .= " AND DAY( $wpdb->comments.comment_date ) = ".$date["day"];
		}
		$date_query .= " ) )";
	}
	$search = ($search != ""?" AND ($wpdb->comments.comment_author LIKE '%".$search."%' OR $wpdb->comments.comment_author_email LIKE '%".$search."%' OR $wpdb->comments.comment_author_url LIKE '%".$search."%' OR $wpdb->comments.comment_author_IP LIKE '%".$search."%' OR $wpdb->comments.comment_content LIKE '%".$search."%')":"");
	$count = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(comment_ID)
			FROM $wpdb->comments JOIN $wpdb->posts
			ON $wpdb->posts.ID = $wpdb->comments.comment_post_ID
			WHERE $wpdb->comments.comment_approved = %s
			".$search."
			".$custom_post_type."
			".($user_id > 0?"AND $wpdb->comments.user_id = $user_id":"")."
			".$date_query."
			".($parent == 0?" AND $wpdb->comments.comment_parent = 0":"")."
			AND ($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private')"
			,1
		)
	);
	return $count;
}
/* Count best answers */
function mobile_api_count_best_answers($user_id = 0,$date = "") {
	global $wpdb;
	$date_query = "";
	if (is_array($date) && !empty($date)) {
		$date_query = "AND ( ( ";
		$date_query .= "YEAR( $wpdb->comments.comment_date ) = ".(isset($date["year"]) && $date["year"] != ""?$date["year"]:date("Y"));
		if (isset($date["month"]) && $date["month"] != "") {
			$date_query .= " AND MONTH( $wpdb->comments.comment_date ) = ".$date["month"];
		}
		if (isset($date["day"]) && $date["day"] != "") {
			$date_query .= " AND DAY( $wpdb->comments.comment_date ) = ".$date["day"];
		}
		$date_query .= " ) )";
	}
	$count = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(ID)
			FROM $wpdb->comments JOIN $wpdb->commentmeta
			ON $wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id
			LEFT JOIN $wpdb->posts
			ON $wpdb->comments.comment_post_ID = $wpdb->posts.ID
			WHERE ($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private')
			".($user_id > 0?"AND user_id = $user_id":"")."
			".$date_query."
			AND ( $wpdb->posts.post_type = '".mobile_api_questions_type."' OR $wpdb->posts.post_type = '".mobile_api_asked_questions_type."' )
			AND $wpdb->comments.comment_approved = '1'
			AND $wpdb->commentmeta.meta_key = %s)"
			,'best_answer_comment'
		)
	);
	return $count;
}
/* hex2rgb */
function mobile_hex2rgb($hex) {
	$hex = str_replace("#","",$hex);
	if (strlen($hex) == 3) {
		$r = hexdec(substr($hex,0,1).substr($hex,0,1));
		$g = hexdec(substr($hex,1,1).substr($hex,1,1));
		$b = hexdec(substr($hex,2,1).substr($hex,2,1));
	}else {
		$r = hexdec(substr($hex,0,2));
		$g = hexdec(substr($hex,2,2));
		$b = hexdec(substr($hex,4,2));
	}
	$rgb = array($r, $g, $b);
	return $rgb;
}
/* Count posts */
function mobile_count_posts_meta($post_type,$user_id) {
	$count = (int)get_user_meta($user_id,mobile_api_action_prefix()."_".$post_type."s_count",true);
	return $count;
}
/* Count comments */
function mobile_api_count_comments_meta($post_type,$user_id = 0) {
	$mobile_api_action_prefix = mobile_api_action_prefix();
	if ($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) {
		$meta = $mobile_api_action_prefix."_answers_count";
	}else {
		$meta = $mobile_api_action_prefix."_comments_count";
	}
	$count = (int)get_user_meta($user_id,$meta,true);
	return $count;
}
/* Count best answers */
function mobile_api_count_best_answers_meta($user_id = 0) {
	$count = (int)get_user_meta($user_id,mobile_api_action_prefix()."_count_best_answers",true);
	return $count;
}
/* Get user info */
function mobile_api_user_info($user_id = 0,$type = "") {
	$mobile_api_action_prefix = mobile_api_action_prefix();
	$user = get_userdata($user_id);

	$owner = false;
	$get_current_user_id = get_current_user_id();
	if ($get_current_user_id == $user_id) {
		$owner = true;
	}
	/* questions */
	$questions_count = (int)get_user_meta($user_id,$mobile_api_action_prefix."_questions_count",true);
	/* answers */
	$answers_count = (int)get_user_meta($user_id,$mobile_api_action_prefix."_answers_count",true);
	/* the_best_answer */
	$the_best_answer = (int)get_user_meta($user_id,$mobile_api_action_prefix."_count_best_answers",true);
	/* points */
	$points = (int)get_user_meta($user_id,"points",true);
	/* posts */
	$posts_count = (int)get_user_meta($user_id,$mobile_api_action_prefix."_posts_count",true);
	/* comments */
	$comments_count = (int)get_user_meta($user_id,$mobile_api_action_prefix."_comments_count",true);
	/* notifications */
	$notifications_count = mobile_api_count_notifications($user_id);
	$new_notifications_count = (int)mobile_api_count_new_notifications($user_id);
	/* messages */
	$active_message = mobile_api_options("active_message");
	$new_messages = ($active_message == mobile_api_checkbox_value?get_user_meta($user_id,$mobile_api_action_prefix."_new_messages_count",true):0);
	$new_messages = ($new_messages > 0?$new_messages:0);
	/* followers */
	$following_you = get_user_meta($user_id,"following_you",true);
	$following_you = (is_array($following_you) && !empty($following_you)?get_users(array('fields' => 'ID','include' => $following_you,'orderby' => 'registered')):array());
	$user_follwers = (int)(is_array($following_you)?count($following_you):0);
	if (is_array($following_you)) {
		$sliced_array = array_slice($following_you,0,3);
		foreach ($sliced_array as $key => $value) {
			if ($value > 0) {
				$followers[] = mobile_api_user_avatar_link(array("user_id" => $value,"size" => 128));
			}
		}
	}
	$active_points_category = mobile_api_options("active_points_category");
	if ($active_points_category == "on" || $active_points_category == 1) {
		$categories_user_points = get_user_meta($user_id,"categories_user_points",true);
		if (is_array($categories_user_points) && !empty($categories_user_points)) {
			foreach ($categories_user_points as $category) {
				$points_category_user[$category] = (int)get_user_meta($user_id,"points_category".$category,true);
			}
			arsort($points_category_user);
			$first_category = (is_array($points_category_user)?key($points_category_user):"");
			$first_points = reset($points_category_user);
		}
	}
	$verified_user = get_the_author_meta('verified_user',$user_id);
	$badge_color = mobile_api_get_badge($user_id,"color",(isset($first_points)?$first_points:""));
	$user_group = (isset($user->roles) && is_array($user->roles)?reset($user->roles):(isset($user->caps) && is_array($user->caps)?key($user->caps):""));
	$roles = (isset($user->allcaps)?$user->allcaps:array());
	$edit_email = get_user_meta($user_id,"wpqa_edit_email",true);
	$user_message = ($user_group == "activation"?esc_html__("A confirmation mail has been sent to your registered email account, If you have not received the confirmation mail, kindly Click here to re-send another confirmation mail.","mobile-api"):"");
	$user_message = ($edit_email != ""?esc_html__("A confirmation mail has been sent to your new email account, If you have not received the confirmation mail, kindly Click here to re-send another confirmation mail.","mobile-api"):"");
	$under_review = mobile_api_under_review();
	$is_super_admin = is_super_admin($user_id);
	$custom_permission = mobile_api_options("custom_permission");
	if (!$is_super_admin && $custom_permission == mobile_api_checkbox_value && $user_id > 0) {
		if (!isset($roles["ask_question"]) || (isset($roles["ask_question"]) && $roles["ask_question"] != 1)) {
			$not_allow_ask = esc_html__("Sorry, you do not have permission to ask questions.","mobile-api");
		}
		if (!isset($roles["show_question"]) || (isset($roles["show_question"]) && $roles["show_question"] != 1)) {
			$not_see_questions = esc_html__("Sorry, you do not have permission to view questions.","mobile-api");
		}
		if (!isset($roles["add_answer"]) || (isset($roles["add_answer"]) && $roles["add_answer"] != 1)) {
			$not_allow_answer = esc_html__("Sorry, you do not have permission to add answers.","mobile-api");
		}
		if (!isset($roles["show_answer"]) || (isset($roles["show_answer"]) && $roles["show_answer"] != 1)) {
			$not_see_answers = esc_html__("Sorry, you do not have permission to view answers.","mobile-api");
		}
		if (!isset($roles["add_post"]) || (isset($roles["add_post"]) && $roles["add_post"] != 1)) {
			$not_allow_post = esc_html__("Sorry, you do not have permission to add posts.","mobile-api");
		}
		if (!isset($roles["show_post"]) || (isset($roles["show_post"]) && $roles["show_post"] != 1)) {
			$not_see_posts = esc_html__("Sorry, you do not have permission to view posts.","mobile-api");
		}
		if (!isset($roles["add_comment"]) || (isset($roles["add_comment"]) && $roles["add_comment"] != 1)) {
			$not_allow_comment = esc_html__("Sorry, you do not have permission to add comments.","mobile-api");
		}
		if (!isset($roles["show_comment"]) || (isset($roles["show_comment"]) && $roles["show_comment"] != 1)) {
			$not_see_comments = esc_html__("Sorry, you do not have permission to view comments.","mobile-api");
		}
		if (!isset($roles["show_knowledgebase"]) || (isset($roles["show_knowledgebase"]) && $roles["show_knowledgebase"] != 1)) {
			$not_see_knowledgebases = esc_html__("Sorry, you do not have permission to view articles.","mobile-api");
		}
		if (!isset($roles["add_group"]) || (isset($roles["add_group"]) && $roles["add_group"] != 1)) {
			$not_allow_group = esc_html__("Sorry, you do not have permission to add groups.","mobile-api");
		}
		if (!isset($roles["send_message"]) || (isset($roles["send_message"]) && $roles["send_message"] != 1)) {
			$not_allow_message = esc_html__("Sorry, you do not have permission to send message.","mobile-api");
		}
	}
	$user_message = ($user_group == $under_review?esc_html__("Your account is under review, You will be notified via mail when it has been approved.","mobile-api"):$user_message);
	$user_message = ($user_message != ""?array("user_message" => $user_message):array());
	$confirm_account = ($user_group == "activation" || $edit_email != ""?array("confirm_account" => true):array());
	
	$not_allow_ask = (isset($not_allow_ask) || $user_group == "activation" || $user_group == $under_review?array("not_allow_ask" => true):array());
	$not_see_questions = (isset($not_see_questions)?array("not_see_questions" => $not_see_questions):array());
	$not_allow_answer = (isset($not_allow_answer) || $user_group == "activation" || $user_group == $under_review?array("not_allow_answer" => true):array());
	$not_see_answers = (isset($not_see_answers)?array("not_see_answers" => $not_see_answers):array());
	$not_allow_post = (isset($not_allow_post) || $user_group == "activation" || $user_group == $under_review?array("not_allow_post" => true):array());
	$not_see_posts = (isset($not_see_posts)?array("not_see_posts" => $not_see_posts):array());
	$not_allow_comment = (isset($not_allow_comment) || $user_group == "activation" || $user_group == $under_review?array("not_allow_comment" => true):array());
	$not_see_comments = (isset($not_see_comments)?array("not_see_comments" => $not_see_comments):array());
	$not_see_knowledgebases = (isset($not_see_knowledgebases)?array("not_see_knowledgebases" => $not_see_knowledgebases):array());
	$not_allow_group = (isset($not_allow_group) || $user_group == "activation" || $user_group == $under_review?array("not_allow_group" => true):array());
	$not_allow_message = (isset($not_allow_message) || $user_group == "activation" || $user_group == $under_review?array("not_allow_message" => true):array());

	$mobile_send_notification = get_user_meta($user_id,"mobile_send_notification",true);

	$facebook = get_the_author_meta('facebook',$user_id);
	$tiktok = get_the_author_meta('tiktok',$user_id);
	$twitter = get_the_author_meta('twitter',$user_id);
	$youtube = get_the_author_meta('youtube',$user_id);
	$vimeo = get_the_author_meta('vimeo',$user_id);
	$linkedin = get_the_author_meta('linkedin',$user_id);
	$instagram = get_the_author_meta('instagram',$user_id);
	$pinterest = get_the_author_meta('pinterest',$user_id);

	$get_countries = mobile_api_get_countries();
	$user_filter = apply_filters("mobile_api_user_filter",array(),$user_id);
	
	$privacy_email = mobile_api_check_user_privacy($user_id,"email");
	$privacy_bio = mobile_api_check_user_privacy($user_id,"bio");
	$privacy_credential = mobile_api_check_user_privacy($user_id,"credential");
	$privacy_website = mobile_api_check_user_privacy($user_id,"website");
	$privacy_country = mobile_api_check_user_privacy($user_id,"country");
	$privacy_city = mobile_api_check_user_privacy($user_id,"city");
	$privacy_age = mobile_api_check_user_privacy($user_id,"age");
	$privacy_phone = mobile_api_check_user_privacy($user_id,"phone");
	$privacy_gender = mobile_api_check_user_privacy($user_id,"gender");
	if ($privacy_credential == true) {
		$profile_credential = get_user_meta($user_id,"profile_credential",true);
		$profile_credential = ($profile_credential != ""?$profile_credential:"false");
	}
	if ($privacy_website == true) {
		$user_url = (isset($user->user_url) && $user->user_url != ""?esc_url($user->user_url):"false");
	}
	if ($privacy_country == true) {
		$country = get_the_author_meta('country',$user_id);
		$country = ($country != ""?$country:"false");
	}
	if ($privacy_city == true) {
		$city = get_the_author_meta('city',$user_id);
		$city = ($city != ""?$city:"false");
	}
	if ($privacy_age == true) {
		$age = get_the_author_meta('age',$user_id);
		$age = ($age != ""?$age:"false");
	}
	$last_age = ($privacy_age == true && isset($age) && $age != "" && $age != "false"?(date_create($age)?date_diff(date_create($age),date_create('today'))->y:$age):"");
	if ($privacy_phone == true) {
		$phone = get_the_author_meta('phone',$user_id);
		$phone = ($phone != ""?$phone:"false");
	}
	if ($privacy_gender == true) {
		$gender = get_the_author_meta((has_askme()?'sex':'gender'),$user_id);
		$gender = ($gender != ""?$gender:"false");
	}

	$show_point_favorite = get_the_author_meta('show_point_favorite',$user_id);
	$received_message = get_the_author_meta('received_message',$user_id);
	$question_schedules = get_the_author_meta('question_schedules',$user_id);
	$post_schedules = get_the_author_meta('post_schedules',$user_id);
	$received_email = get_the_author_meta('received_email',$user_id);
	$received_email_post = get_the_author_meta('received_email_post',$user_id);
	$new_payment_mail = get_the_author_meta('new_payment_mail',$user_id);
	$send_message_mail = get_the_author_meta('send_message_mail',$user_id);
	$answer_on_your_question = get_the_author_meta('answer_on_your_question',$user_id);
	$answer_question_follow = get_the_author_meta('answer_question_follow',$user_id);
	$notified_reply = get_the_author_meta('notified_reply',$user_id);
	$unsubscribe_mails = get_the_author_meta('unsubscribe_mails',$user_id);
	$follow_email = get_the_author_meta('follow_email',$user_id);

	$user_checkboxes = array(
		"show_point_favorite" => (has_askme() && $show_point_favorite == 1?"on":$show_point_favorite),
		"received_message" => (has_askme() && $received_message == 1?"on":$received_message),
		"question_schedules" => $question_schedules,
		"post_schedules" => $post_schedules,
		"received_email" => (has_askme() && $received_email == 1?"on":$received_email),
		"received_email_post" => ($received_email_post == 1?"on":$received_email_post),
		"new_payment_mail" => $new_payment_mail,
		"send_message_mail" => (has_askme() && $send_message_mail == 1?"on":$send_message_mail),
		"answer_on_your_question" => $answer_on_your_question,
		"answer_question_follow" => $answer_question_follow,
		"notified_reply" => $notified_reply,
		"unsubscribe_mails" => $unsubscribe_mails,
		"follow_email" => (has_askme() && $follow_email == 1?"on":$follow_email),
	);

	$gender = (isset($gender) && $gender != ""?($gender == "male" || $gender == 1?"male":"").($gender == "female" || $gender == 2?"female":"").($gender == "other" || $gender == 3?"other":""):"");

	$user_saved_values = array(
		"countryEditProfile" => ($privacy_country == true && isset($country) && $country != "" && $country != "false"?$country:""),
		"ageEditProfile" => ($privacy_age == true && isset($age) && $age != "" && $age != "false"?$age:""),
		"genderEditProfile" => ($privacy_gender == true && isset($gender) && $gender != "" && $gender != "false"?$gender:""),
	);

	$get_badge = mobile_api_get_badge($user_id,"name",(isset($first_points)?$first_points:""));

	$array_merge = array_merge(array(
		"id"                 => $user_id,
		"username"           => (isset($user->user_login)?$user->user_login:""),
		"nicename"           => (isset($user->nickname)?$user->nickname:""),
		"email"              => (($privacy_email == true || $owner || $type == "login") && isset($user->user_email)?$user->user_email:""),
		"website"            => ($privacy_website == true && isset($user_url) && $user_url != "" && $user_url != "false"?$user_url:""),
		"registered"         => (isset($user->user_registered)?$user->user_registered:""),
		"displayname"        => (isset($user->display_name)?$user->display_name:""),
		"firstname"          => (isset($user->user_firstname)?$user->user_firstname:""),
		"lastname"           => (isset($user->last_name)?$user->last_name:""),
		"nickname"           => (isset($user->nickname)?$user->nickname:""),
		"facebook"           => ($facebook != ""?$facebook:""),
		"tiktok"             => ($tiktok != ""?$tiktok:""),
		"twitter"            => ($twitter != ""?$twitter:""),
		"youtube"            => ($youtube != ""?$youtube:""),
		"vimeo"              => ($vimeo != ""?$vimeo:""),
		"linkedin"           => ($linkedin != ""?$linkedin:""),
		"instagram"          => ($instagram != ""?$instagram:""),
		"pinterest"          => ($pinterest != ""?$pinterest:""),
		"country"            => ($privacy_country == true && isset($country) && $country != "" && $country != "false" && isset($get_countries[$country])?$get_countries[$country]:""),
		"city"               => ($privacy_city == true && isset($city) && $city != "" && $city != "false"?$city:""),
		"age"                => ($privacy_age == true && isset($last_age) && $last_age != "" && $last_age != "false"?"{$last_age}":""),
		"phone"              => ($privacy_phone == true && isset($phone) && $phone != "" && $phone != "false"?$phone:""),
		"gender"             => ($privacy_gender == true && isset($gender) && $gender != "" && $gender != "false"?($gender == "male" || $gender == 1?esc_html__("Male","mobile-api"):"").($gender == "female" || $gender == 2?esc_html__("Female","mobile-api"):"").($gender == "other" || $gender == 3?esc_html__("Other","mobile-api"):""):""),
		"description"        => ($privacy_bio == true?(isset($user->user_description)?$user->user_description:""):""),
		"capabilities"       => (isset($user->wp_capabilities)?$user->wp_capabilities:""),
		"user_group"         => ($owner?$user_group:""),
		"avatar"             => mobile_api_user_avatar_link(array("user_id" => $user_id,"size" => 128,"user_name" => (isset($user->display_name)?$user->display_name:""))),
		"avatar_small"       => mobile_api_user_avatar_link(array("user_id" => $user_id,"size" => 50,"user_name" => (isset($user->display_name)?$user->display_name:""))),
		"cover"              => mobile_api_user_cover_link(array("user_id" => $user_id)),
		"points"             => $points,
		"followers"          => $user_follwers,
		"user_followers"     => (isset($followers) && is_array($followers)?$followers:array()),
		"questions"          => $questions_count,
		"answers"            => $answers_count,
		"best_answers"       => $the_best_answer,
		"posts"              => $posts_count,
		"comments"           => $comments_count,
		"notifications"      => $notifications_count,
		"new_notifications"  => $new_notifications_count,
		"new_messages"       => ($owner?$new_messages:""),
		"verified"           => ($verified_user == 1 || $verified_user == "on"?true:false),
		"badge"              => array("name" => ($get_badge != ""?strip_tags($get_badge):""),"color" => ($badge_color != ""?$badge_color:""),"textColor" => "FFFFFF","textColorDark" => "FFFFFF"),
		"profile_credential" => ($privacy_credential == true && $profile_credential != "false"?$profile_credential:""),
		"admin"              => ($owner && $is_super_admin?true:false),
		"pushNo"             => ($owner && ($mobile_send_notification == "" || $mobile_send_notification == "on" || $mobile_send_notification == 1)?true:false),
		"user_stats"         => (int)mobile_api_get_post_stats(0,$user_id),
	),$user_checkboxes,$user_message,$confirm_account,$not_allow_ask,$not_allow_answer,$not_allow_post,$not_allow_comment,$not_see_questions,$not_see_answers,$not_see_posts,$not_see_comments,$not_see_knowledgebases,$not_allow_group,$not_allow_message,$user_filter,$user_saved_values);
	return $array_merge;
}
/* HTML tags */
function mobile_api_html_tags() {
	global $allowedposttags,$allowedtags;
	$allowedposttags['math'] = array('xmlns' => true);
	$allowedposttags['mspace'] = array('linebreak' => true);
	$allowedposttags['mfrac'] = array('bevelled' => true);
	$allowedposttags['mfenced'] = array('open' => true,'close' => true);
	$allowedposttags['menclose'] = array('notation' => true);
	$allowedposttags['mi'] = array('mathvariant' => true);
	$allowedposttags['mo'] = array('largeop' => true);
	$allowedposttags['mstyle'] = array('displaystyle' => true);
	$allowedposttags['img'] = array('alt' => true, 'class' => true, 'id' => true, 'title' => true, 'src' => true);
	$allowedposttags['a'] = array('href' => true, 'title' => true, 'target' => true, 'class' => true);
	$allowedposttags['div'] = array('class' => true);
	$allowedposttags['span'] = array('style' => true);
	$allowedtags['math'] = array('xmlns' => true);
	$allowedtags['mspace'] = array('linebreak' => true);
	$allowedtags['mfrac'] = array('bevelled' => true);
	$allowedtags['mfenced'] = array('open' => true,'close' => true);
	$allowedtags['menclose'] = array('notation' => true);
	$allowedtags['mi'] = array('mathvariant' => true);
	$allowedtags['mo'] = array('largeop' => true);
	$allowedtags['mstyle'] = array('displaystyle' => true);
	$allowedtags['img'] = array('src' => true);
	$allowedtags['a'] = array('href' => true);
	$allowedtags['iframe'] = array('src' => true);
	$allowedtags['source'] = array('src' => true);
	$allowedtags['span'] = array('style' => true);
	$allowedtags['div'] = array('class' => true, 'theme' => true, 'language' => true);
	$allowedtags['pre'] = array('class' => true, 'data-enlighter-language' => true, 'theme' => true, 'language' => true);
	$array = array('p','blockquote','hr','br','ul','ol','li','dl','dt','dd','table','td','tr','th','thead','tbody','h1','h2','h3','h4','h5','h6','cite','em','address','big','ins','sub','sup','tt','var','msqrt','mn','munder','mrow','msubsup','mroot','msup','msub','mover','munderover','mtable','mtr','mtd');
	foreach ($array as $value) {
		$allowedposttags[$value] = array();
		$allowedtags[$value] = array();
	}
}
/* Kses stip */
function mobile_api_kses_stip($value,$post_from_front = '',$edit = '') {
	if ($post_from_front == "from_front") {
		$value = nl2br($value);
		if ($edit != "edit") {
			$value = preg_replace('#(<br */?>\s*)+#i','<p>',$value);
		}
	}else {
		$value = preg_replace('#(<br */?>\s*)+#i','<br />',$value);
	}
	if ($edit == "edit") {
		$value = str_replace(array("\r","\n","\r\n"),'<br/>',$value);
		$value = preg_replace("/\r\n|\r|\n/",'<br/>',$value);
	}
	$value = preg_replace('#(<caption */?>\s*)+#i','<shortcaption>',$value);
	$value = preg_replace('/\[\/caption\]/i','</shortcaption>',$value);
	$value = preg_replace('/\[video[^\]]+mp4="([^"^\]]+)"\]\[\/video]/',"<br><div class=\"wp-video\"><source src=\"$1\"/></div>",$value);
	$value = mobile_api_deslash($value);
	$value = strip_shortcodes($value);
	//$value = wp_kses($value,mobile_api_html_tags());
	$value = apply_filters("mobile_api_value_content",$value);
	return (substr($value,-4) == "<br>"?substr($value,0,-4):$value);
}
/* Preg replace */
function mobile_api_preg_replace($content) {
	$content = preg_replace("/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i","<div class=\"wp-video\"><source src=\"https://www.youtube.com/watch?v=$2\"/></div>",$content);
	$content = preg_replace('#<iframe(.*?)(?:src="https?://)?(?:www\.)?(?:youtu\.be/|youtube-nocookie\.com(?:/embed/|/v/|/watch?.*?v=)|youtube\.com(?:/embed/|/v/|/watch?.*?v=))([\w\-]{10,12}).*<\/iframe>#x','<div class="wp-video"><source src="https://www.youtube.com/watch?v=$2"></div>',$content);
	$content = mobile_api_deslash($content);
	$content = preg_replace('/\\\\\[(.*)\\\\\]/','<div class="latex">$1</div>',$content);
	return $content;
}
/* deslash */
function mobile_api_deslash($content) {
	$content = preg_replace("/\\\+'/","'",$content);
	$content = preg_replace('/\\\+"/','"',$content);
	return $content;
}
/* Remove item from array */
function mobile_api_remove_item_by_value($array,$val = '',$preserve_keys = true) {
	if (empty($array) || !is_array($array)) {
		return false;
	}
	if (!in_array($val,$array)) {
		return $array;
	}
	foreach ($array as $key => $value) {
		if ($value == $val) unset($array[$key]);
	}
	return ($preserve_keys === true)?$array:array_values($array);
}
/* Add devices token */
function mobile_api_update_devices_token($user_id,$device_token) {
	$devices_token = get_user_meta($user_id,"devices_token",true);
	if (empty($devices_token)) {
		update_user_meta($user_id,"devices_token",array($device_token));
	}else if (is_array($devices_token) && !in_array($device_token,$devices_token)) {
		update_user_meta($user_id,"devices_token",array_merge($devices_token,array($device_token)));
	}
}
/* Remove devices token */
function mobile_api_remove_devices_token($user_id,$device_token) {
	$devices_token = get_user_meta($user_id,"devices_token",true);
	if (is_array($devices_token) && in_array($device_token,$devices_token)) {
		$devices_token = mobile_api_remove_item_by_value($devices_token,$device_token);
		update_user_meta($user_id,"devices_token",$devices_token);
	}
}
/* Duplicate comments */
add_filter('duplicate_comment_id','mobile_api_duplicate_comment_id',9,1);
function mobile_api_duplicate_comment_id($dupe_id) {
	if ($dupe_id > 0) {
		$rest_api_slug = get_option( 'permalink_structure' ) ? rest_get_url_prefix() : '?rest_route=/';
		$base_api = mobile_api_base;

		$valid_api_uri = strpos( $_SERVER['REQUEST_URI'], $rest_api_slug );
		$valid_api_uri_2 = strpos( $_SERVER['REQUEST_URI'], $base_api );

		if ($valid_api_uri || $valid_api_uri_2) {
			global $mobile_api;
			$mobile_api->error(__("Duplicate comment detected; it looks as though you've already said that!","mobile-api"));
		}else {
			return $dupe_id;
		}
	}else {
		return $dupe_id;
	}
}
/* Quickly comments */
add_filter("comment_flood_filter","mobile_api_comment_flood_filter",9,3);
function mobile_api_comment_flood_filter($return,$time_lastcomment, $time_newcomment) {
	$rest_api_slug = get_option( 'permalink_structure' ) ? rest_get_url_prefix() : '?rest_route=/';
	$base_api = mobile_api_base;

	$valid_api_uri = strpos( $_SERVER['REQUEST_URI'], $rest_api_slug );
	$valid_api_uri_2 = strpos( $_SERVER['REQUEST_URI'], $base_api );

	if ($valid_api_uri || $valid_api_uri_2) {
		if (($time_newcomment - $time_lastcomment) < 15) {
			global $mobile_api;
			$mobile_api->error(esc_html__("You are posting comments too quickly. Slow down.","mobile-api"));
		}
	}
}
/* Get attachment id */
function mobile_api_get_attachment_id($image_url) {
	global $wpdb;
	$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid RLIKE '%s';", $image_url ));
	if (isset($attachment[0]) && $attachment[0] != "") {
		return $attachment[0];
	}
}
/* Check image id or URL */
function mobile_api_image_url_id($url_id) {
	if (is_numeric($url_id)) {
		$image = wp_get_attachment_url($url_id);
	}
	
	if (!isset($image)) {
		if (is_array($url_id)) {
			if (isset($url_id['id']) && $url_id['id'] != '' && $url_id['id'] != 0) {
				$image = wp_get_attachment_url($url_id['id']);
			}else if (isset($url_id['url']) && $url_id['url'] != '') {
				$id    = mobile_api_get_attachment_id($url_id['url']);
				$image = ($id?wp_get_attachment_url($id):'');
			}
			$image = (isset($image) && $image != ''?$image:$url_id['url']);
		}else {
			if (isset($url_id) && $url_id != '') {
				$id    = mobile_api_get_attachment_id($url_id);
				$image = ($id?wp_get_attachment_url($id):'');
			}
			$image = (isset($image) && $image != ''?$image:$url_id);
		}
	}
	if (isset($image) && $image != "") {
		return $image;
	}
}
/* After comment added */
add_action('comment_post','mobile_api_comment_question');
function mobile_api_comment_question($comment_id) {
	if (isset($_POST['added_mobile_app'])) {
		update_comment_meta($comment_id,'added_mobile_app','yes');
	}
}
/* Paged */
function mobile_api_paged() {
	if (get_query_var("paged") != "") {
		$paged = (int)get_query_var("paged");
	}else if (get_query_var("page") != "") {
		$paged = (int)get_query_var("page");
	}
	if (get_query_var("paged") > get_query_var("page") && get_query_var("paged") > 0) {
		$paged = (int)get_query_var("paged");
	}
	if (get_query_var("page") > get_query_var("paged") && get_query_var("page") > 0) {
		$paged = (int)get_query_var("page");
	}
	if (!isset($paged) || (isset($paged) && $paged <= 1)) {
		$paged = 1;
	}
	return $paged;
}
/* Custom search for the users */
function mobile_api_custom_search_users($user_query) {
	$search_value = $user_query->query_vars;
	if (is_array($search_value) && isset($search_value['search'])) {
		$search_value = str_replace("*","",$search_value['search']);
		$search_value = apply_filters("mobile_api_search_value_filter",$search_value);
		global $wpdb;
		$user_query->query_where .= " 
		OR (ID IN (SELECT user_id FROM $wpdb->usermeta
		WHERE (($wpdb->usermeta.meta_key='nickname' OR $wpdb->usermeta.meta_key='first_name' OR $wpdb->usermeta.meta_key='last_name')
			AND ($wpdb->usermeta.meta_value LIKE '".$wpdb->esc_like($search_value)."' OR $wpdb->usermeta.meta_value RLIKE '".$wpdb->esc_like($search_value)."'))
		)
		OR ($wpdb->users.ID LIKE '".$wpdb->esc_like($search_value)."' OR $wpdb->users.ID RLIKE '".$wpdb->esc_like($search_value)."') 
		OR ($wpdb->users.user_email LIKE '".$wpdb->esc_like($search_value)."' OR $wpdb->users.user_email RLIKE '".$wpdb->esc_like($search_value)."') 
		OR ($wpdb->users.user_url LIKE '".$wpdb->esc_like($search_value)."' OR $wpdb->users.user_url RLIKE '".$wpdb->esc_like($search_value)."') 
		OR ($wpdb->users.display_name LIKE '".$wpdb->esc_like($search_value)."' OR $wpdb->users.display_name RLIKE '".$wpdb->esc_like($search_value)."') 
		OR ($wpdb->users.user_login LIKE '".$wpdb->esc_like($search_value)."' OR $wpdb->users.user_login RLIKE '".$wpdb->esc_like($search_value)."') 
		OR ($wpdb->users.user_nicename LIKE '".$wpdb->esc_like($search_value)."' OR $wpdb->users.user_nicename RLIKE '".$wpdb->esc_like($search_value)."'))";
	}
}
/* Excerpt any text */
function mobile_api_excerpt_any($excerpt_length,$content,$more = '...',$excerpt_type = mobile_api_excerpt_type) {
	$excerpt_length = (isset($excerpt_length) && $excerpt_length != ""?$excerpt_length:5);
	$content = strip_tags($content);
	if ($excerpt_type == "characters") {
		$content = mb_substr($content,0,$excerpt_length,"UTF-8");
	}else {
		$words = explode(' ',$content,$excerpt_length + 1);
		if (count(explode(' ',$content)) > $excerpt_length) {
			array_pop($words);
			array_push($words,'');
			$content = implode(' ',$words);
			$content = $content.$more;
		}
	}
	return $content;
}
/* Random token */
function mobile_api_token($length){
	$token = "";
	$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
	$codeAlphabet.= "0123456789";
	$max = strlen($codeAlphabet);
	for ($i=0; $i < $length; $i++) {
		$token .= $codeAlphabet[random_int(0, $max-1)];
	}
	return $token;
}?>
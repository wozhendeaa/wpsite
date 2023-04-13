<?php /* Mobile config */
function mobile_api_config() {
	$array_options = array(
		"category" => "category",
		"date" => "date",
		"author_image" => "authorImage",
		"author" => "author",
		"question_vote" => "questionRank",
		"poll" => "showPull",
		"tags" => "tags",
		"answer_button" => "answerButton",
		"answers_count" => "answersCount",
		"views_count" => "viewsCount",
		"followers_count" => "followersCount",
		"favourite" => "save",
	);
	$array_options_single = array(
		"category" => "category",
		"date" => "date",
		"author_image" => "authorImage",
		"author" => "author",
		"question_vote" => "questionRank",
		"tags" => "tags",
		"answer_button" => "answerButton",
		"answers_count" => "answersCount",
		"views_count" => "viewsCount",
		"followers_count" => "followersCount",
		"favourite" => "save",
		"share" => "share",
	);
	$array_options_posts = array(
		"category" => "category",
		"date" => "date",
		"author_image" => "authorImage",
		"author" => "author",
		"tags" => "tags",
		"comment_button" => "answerButton",
		"comments_count" => "answersCount",
		"views_count" => "viewsCount",
	);
	$array_options_posts_single = array(
		"category" => "category",
		"date" => "date",
		"author_image" => "authorImage",
		"author" => "author",
		"tags" => "tags",
		"comment_button" => "answerButton",
		"comments_count" => "answersCount",
		"views_count" => "viewsCount",
		"share" => "share",
	);
	$array_options_comments = array(
		"author_image" => "authorImage",
		"author" => "author",
	);

	$mobile_api_options = get_option(mobile_api_options_name());

	$user_id = get_current_user_id();
	if ($user_id > 0) {
		$user_info = get_userdata($user_id);
		$roles = $user_info->allcaps;
		$user_group = (isset($user_info) && is_array($user_info->roles)?reset($user_info->roles):(isset($user_info) && is_array($user_info->caps)?key($user_info->caps):""));
	}

	$follow_category = (isset($mobile_api_options["follow_category"])?$mobile_api_options["follow_category"]:"");
	$active_notifications = (isset($mobile_api_options["active_notifications"])?$mobile_api_options["active_notifications"]:"");
	
	$force_update = (isset($mobile_api_options["force_update"])?$mobile_api_options["force_update"]:"");
	$android_version = (isset($mobile_api_options["android_version"])?$mobile_api_options["android_version"]:"");
	$ios_version = (isset($mobile_api_options["ios_version"])?$mobile_api_options["ios_version"]:"");
	$app_lang = (isset($mobile_api_options["app_lang"])?$mobile_api_options["app_lang"]:"");
	$ask_question_to_users = (isset($mobile_api_options["ask_question_to_users"])?$mobile_api_options["ask_question_to_users"]:"");
	$active_reports = (isset($mobile_api_options["active_reports"])?$mobile_api_options["active_reports"]:"");
	$active_logged_reports = (isset($mobile_api_options["active_logged_reports"])?$mobile_api_options["active_logged_reports"]:"");
	$app_skin = (isset($mobile_api_options["app_skin"])?$mobile_api_options["app_skin"]:"");
	$activate_switch_mode = (isset($mobile_api_options["activate_switch_mode"])?$mobile_api_options["activate_switch_mode"]:"");
	$activate_stop_notification = (isset($mobile_api_options["activate_stop_notification"])?$mobile_api_options["activate_stop_notification"]:"");
	$activate_dark_from_header = (isset($mobile_api_options["activate_dark_from_header"])?$mobile_api_options["activate_dark_from_header"]:"");
	$addaction = (isset($mobile_api_options["addaction_mobile"])?$mobile_api_options["addaction_mobile"]:"");
	$addaction_mobile_action = (isset($mobile_api_options["addaction_mobile_action"])?$mobile_api_options["addaction_mobile_action"]:"");
	$mobile_addaction_question = (isset($mobile_api_options["mobile_addaction_question"])?$mobile_api_options["mobile_addaction_question"]:"");
	$mobile_addaction_post = (isset($mobile_api_options["mobile_addaction_post"])?$mobile_api_options["mobile_addaction_post"]:"");

	$mobile_time_login = (isset($mobile_api_options["mobile_time_login"])?$mobile_api_options["mobile_time_login"]:"");
	$mobile_metric = "days";
	if ($mobile_time_login == "hours") {
		$mobile_metric = "hours";
	}else if ($mobile_time_login == "minutes") {
		$mobile_metric = "minutes";
	}else if ($mobile_time_login == "seconds") {
		$mobile_metric = "seconds";
	}
	$mobile_time_login_time = (int)(isset($mobile_api_options["mobile_time_login_".$mobile_metric])?$mobile_api_options["mobile_time_login_".$mobile_metric]:7);
	$mobile_time_login_time = ($mobile_time_login_time > 0?$mobile_time_login_time:7);

	$onboardmodels = (isset($mobile_api_options["onboardmodels_mobile"])?$mobile_api_options["onboardmodels_mobile"]:"");
	$onboardmodels_img_1 = (isset($mobile_api_options["onboardmodels_img_1_mobile"])?mobile_api_image_url_id($mobile_api_options["onboardmodels_img_1_mobile"]):"");
	$onboardmodels_img_2 = (isset($mobile_api_options["onboardmodels_img_2_mobile"])?mobile_api_image_url_id($mobile_api_options["onboardmodels_img_2_mobile"]):"");
	$onboardmodels_img_3 = (isset($mobile_api_options["onboardmodels_img_3_mobile"])?mobile_api_image_url_id($mobile_api_options["onboardmodels_img_3_mobile"]):"");
	$onboardmodels_title_1 = (isset($mobile_api_options["onboardmodels_title_1_mobile"])?$mobile_api_options["onboardmodels_title_1_mobile"]:"");
	$onboardmodels_title_2 = (isset($mobile_api_options["onboardmodels_title_2_mobile"])?$mobile_api_options["onboardmodels_title_2_mobile"]:"");
	$onboardmodels_title_3 = (isset($mobile_api_options["onboardmodels_title_3_mobile"])?$mobile_api_options["onboardmodels_title_3_mobile"]:"");
	$onboardmodels_subtitle_1 = (isset($mobile_api_options["onboardmodels_subtitle_1_mobile"])?$mobile_api_options["onboardmodels_subtitle_1_mobile"]:"");
	$onboardmodels_subtitle_2 = (isset($mobile_api_options["onboardmodels_subtitle_2_mobile"])?$mobile_api_options["onboardmodels_subtitle_2_mobile"]:"");
	$onboardmodels_subtitle_3 = (isset($mobile_api_options["onboardmodels_subtitle_3_mobile"])?$mobile_api_options["onboardmodels_subtitle_3_mobile"]:"");

	$app_apple_id = (isset($mobile_api_options["app_apple_id"])?$mobile_api_options["app_apple_id"]:"");

	$text_size_app = (isset($mobile_api_options["text_size_app"])?$mobile_api_options["text_size_app"]:"");
	$rate_app = (isset($mobile_api_options["rate_app"])?$mobile_api_options["rate_app"]:"");
	$edit_profile_app = (isset($mobile_api_options["edit_profile_app"])?$mobile_api_options["edit_profile_app"]:"");
	$notifications_page_app = (isset($mobile_api_options["notifications_page_app"])?$mobile_api_options["notifications_page_app"]:"");
	$delete_account = (isset($mobile_api_options["delete_account"])?$mobile_api_options["delete_account"]:"");
	$delete_account_groups = (isset($mobile_api_options["delete_account_groups"])?$mobile_api_options["delete_account_groups"]:"");
	$delete_account_location = (isset($mobile_api_options["delete_account_location"])?$mobile_api_options["delete_account_location"]:"");
	$about_us_app = (isset($mobile_api_options["about_us_app"])?$mobile_api_options["about_us_app"]:"");
	$about_us_page_app = (isset($mobile_api_options["about_us_page_app"])?$mobile_api_options["about_us_page_app"]:"");
	$privacy_policy_page_app = (isset($mobile_api_options["privacy_policy_page_app"])?$mobile_api_options["privacy_policy_page_app"]:"");
	$terms_page_app = (isset($mobile_api_options["terms_page_app"])?$mobile_api_options["terms_page_app"]:"");
	$faqs_app = (isset($mobile_api_options["faqs_app"])?$mobile_api_options["faqs_app"]:"");
	$faqs_page_app = (isset($mobile_api_options["faqs_page_app"])?$mobile_api_options["faqs_page_app"]:"");
	$contact_us_app = (isset($mobile_api_options["contact_us_app"])?$mobile_api_options["contact_us_app"]:"");
	$share_app = (isset($mobile_api_options["share_app"])?$mobile_api_options["share_app"]:"");
	$share_title = (isset($mobile_api_options["share_title"])?$mobile_api_options["share_title"]:"");
	$share_image = (isset($mobile_api_options["share_image"])?mobile_api_image_url_id($mobile_api_options["share_image"]):"");
	$share_android = (isset($mobile_api_options["share_android"])?$mobile_api_options["share_android"]:"");
	$share_ios = (isset($mobile_api_options["share_ios"])?$mobile_api_options["share_ios"]:"");

	$logo_position = (isset($mobile_api_options["mobile_logo_position"])?$mobile_api_options["mobile_logo_position"]:"");
	$mobile_logo = (isset($mobile_api_options["mobile_logo"])?mobile_api_image_url_id($mobile_api_options["mobile_logo"]):"");
	$mobile_logo_dark = (isset($mobile_api_options["mobile_logo_dark"])?mobile_api_image_url_id($mobile_api_options["mobile_logo_dark"]):"");

	$bottom_bar_activate = (isset($mobile_api_options["bottom_bar_activate"])?$mobile_api_options["bottom_bar_activate"]:"");
	$add_bottom_bars = (isset($mobile_api_options["add_bottom_bars"])?$mobile_api_options["add_bottom_bars"]:"");

	$side_navbar_activate = (isset($mobile_api_options["side_navbar_activate"])?$mobile_api_options["side_navbar_activate"]:"");
	$add_sidenavs = (isset($mobile_api_options["add_sidenavs"])?$mobile_api_options["add_sidenavs"]:"");

	$home_page_app = (int)(isset($mobile_api_options["home_page_app"])?$mobile_api_options["home_page_app"]:"");
	$count_posts_home = (int)(isset($mobile_api_options["count_posts_home"])?$mobile_api_options["count_posts_home"]:"");
	$count_posts_home = ($count_posts_home > 0?$count_posts_home:10);
	$mobile_setting_home = (isset($mobile_api_options["mobile_setting_home"])?$mobile_api_options["mobile_setting_home"]:"");

	$mobile_setting_home_posts = (isset($mobile_api_options["mobile_setting_home_posts"])?$mobile_api_options["mobile_setting_home_posts"]:"");

	$ads_mobile_top = (isset($mobile_api_options["ads_mobile_top"])?$mobile_api_options["ads_mobile_top"]:"");
	$ads_mobile_bottom = (isset($mobile_api_options["ads_mobile_bottom"])?$mobile_api_options["ads_mobile_bottom"]:"");

	$read_more_answer = (isset($mobile_api_options["read_more_answer"])?$mobile_api_options["read_more_answer"]:"");

	$activate_male_female = apply_filters("mobile_api_activate_male_female",false);
	
	if ($home_page_app > 0) {
		$question_bump = (isset($mobile_api_options["question_bump"])?$mobile_api_options["question_bump"]:"");
		$active_points = (isset($mobile_api_options["active_points"])?$mobile_api_options["active_points"]:"");
		$post_id = $home_page_app;
		$home_tabs_options = get_post_meta($post_id,mobile_api_theme_prefix."_home_tabs",true);

		$feed_slug                     = get_post_meta($post_id,mobile_api_meta_prefix."feed_slug",true);
		$recent_questions_slug         = get_post_meta($post_id,mobile_api_meta_prefix."recent_questions_slug",true);
		$questions_for_you_slug        = get_post_meta($post_id,mobile_api_meta_prefix."questions_for_you_slug",true);
		$most_answers_slug             = get_post_meta($post_id,mobile_api_meta_prefix."most_answers_slug",true);
		$question_bump_slug            = get_post_meta($post_id,mobile_api_meta_prefix."question_bump_slug",true);
		$answers_slug                  = get_post_meta($post_id,mobile_api_meta_prefix."answers_slug",true);
		$answers_might_like_slug       = get_post_meta($post_id,mobile_api_meta_prefix."answers_might_like_slug",true);
		$answers_for_you_slug          = get_post_meta($post_id,mobile_api_meta_prefix."answers_for_you_slug",true);
		$most_visit_slug               = get_post_meta($post_id,mobile_api_meta_prefix."most_visit_slug",true);
		$most_reacted_slug             = get_post_meta($post_id,mobile_api_meta_prefix."most_reacted_slug",true);
		$most_vote_slug                = get_post_meta($post_id,mobile_api_meta_prefix."most_vote_slug",true);
		$no_answers_slug               = get_post_meta($post_id,mobile_api_meta_prefix."no_answers_slug",true);
		$recent_posts_slug             = get_post_meta($post_id,mobile_api_meta_prefix."recent_posts_slug",true);
		$posts_visited_slug            = get_post_meta($post_id,mobile_api_meta_prefix."posts_visited_slug",true);
		$random_slug                   = get_post_meta($post_id,mobile_api_meta_prefix."random_slug",true);
		$question_new_slug             = get_post_meta($post_id,mobile_api_meta_prefix."question_new_slug",true);
		$question_sticky_slug          = get_post_meta($post_id,mobile_api_meta_prefix."question_sticky_slug",true);
		$question_polls_slug           = get_post_meta($post_id,mobile_api_meta_prefix."question_polls_slug",true);
		$question_followed_slug        = get_post_meta($post_id,mobile_api_meta_prefix."question_followed_slug",true);
		$question_favorites_slug       = get_post_meta($post_id,mobile_api_meta_prefix."question_favorites_slug",true);
		$poll_feed_slug                = get_post_meta($post_id,mobile_api_meta_prefix."poll_feed_slug",true);
		$recent_knowledgebases_slug    = get_post_meta($post_id,mobile_api_meta_prefix."recent_knowledgebases_slug",true);
		$random_knowledgebases_slug    = get_post_meta($post_id,mobile_api_meta_prefix."random_knowledgebases_slug",true);
		$sticky_knowledgebases_slug    = get_post_meta($post_id,mobile_api_meta_prefix."sticky_knowledgebases_slug",true);
		$knowledgebases_visited_slug   = get_post_meta($post_id,mobile_api_meta_prefix."knowledgebases_visited_slug",true);
		$knowledgebases_voted_slug     = get_post_meta($post_id,mobile_api_meta_prefix."knowledgebases_voted_slug",true);

		$feed_slug_2                   = get_post_meta($post_id,mobile_api_meta_prefix."feed_slug_2",true);
		$recent_questions_slug_2       = get_post_meta($post_id,mobile_api_meta_prefix."recent_questions_slug_2",true);
		$questions_for_you_slug_2      = get_post_meta($post_id,mobile_api_meta_prefix."questions_for_you_slug_2",true);
		$most_answers_slug_2           = get_post_meta($post_id,mobile_api_meta_prefix."most_answers_slug_2",true);
		$question_bump_slug_2          = get_post_meta($post_id,mobile_api_meta_prefix."question_bump_slug_2",true);
		$answers_slug_2                = get_post_meta($post_id,mobile_api_meta_prefix."answers_slug_2",true);
		$answers_might_like_slug_2     = get_post_meta($post_id,mobile_api_meta_prefix."answers_might_like_slug_2",true);
		$answers_for_you_slug_2        = get_post_meta($post_id,mobile_api_meta_prefix."answers_for_you_slug_2",true);
		$most_visit_slug_2             = get_post_meta($post_id,mobile_api_meta_prefix."most_visit_slug_2",true);
		$most_reacted_slug_2           = get_post_meta($post_id,mobile_api_meta_prefix."most_reacted_slug_2",true);
		$most_vote_slug_2              = get_post_meta($post_id,mobile_api_meta_prefix."most_vote_slug_2",true);
		$no_answers_slug_2             = get_post_meta($post_id,mobile_api_meta_prefix."no_answers_slug_2",true);
		$recent_posts_slug_2           = get_post_meta($post_id,mobile_api_meta_prefix."recent_posts_slug_2",true);
		$posts_visited_slug_2          = get_post_meta($post_id,mobile_api_meta_prefix."posts_visited_slug_2",true);
		$random_slug_2                 = get_post_meta($post_id,mobile_api_meta_prefix."random_slug_2",true);
		$question_new_slug_2           = get_post_meta($post_id,mobile_api_meta_prefix."question_new_slug_2",true);
		$question_sticky_slug_2        = get_post_meta($post_id,mobile_api_meta_prefix."question_sticky_slug_2",true);
		$question_polls_slug_2         = get_post_meta($post_id,mobile_api_meta_prefix."question_polls_slug_2",true);
		$question_followed_slug_2      = get_post_meta($post_id,mobile_api_meta_prefix."question_followed_slug_2",true);
		$question_favorites_slug_2     = get_post_meta($post_id,mobile_api_meta_prefix."question_favorites_slug_2",true);
		$poll_feed_slug_2              = get_post_meta($post_id,mobile_api_meta_prefix."poll_feed_slug_2",true);
		$recent_knowledgebases_slug_2  = get_post_meta($post_id,mobile_api_meta_prefix."recent_knowledgebases_slug_2",true);
		$random_knowledgebases_slug_2  = get_post_meta($post_id,mobile_api_meta_prefix."random_knowledgebases_slug_2",true);
		$sticky_knowledgebases_slug_2  = get_post_meta($post_id,mobile_api_meta_prefix."sticky_knowledgebases_slug_2",true);
		$knowledgebases_visited_slug_2 = get_post_meta($post_id,mobile_api_meta_prefix."knowledgebases_visited_slug_2",true);
		$knowledgebases_voted_slug_2   = get_post_meta($post_id,mobile_api_meta_prefix."knowledgebases_voted_slug_2",true);

		$feed_slug                     = ($feed_slug != ""?$feed_slug:"feed");
		$recent_questions_slug         = ($recent_questions_slug != ""?$recent_questions_slug:"recent-questions");
		$questions_for_you_slug        = ($questions_for_you_slug != ""?$questions_for_you_slug:"questions-for-you");
		$most_answers_slug             = ($most_answers_slug != ""?$most_answers_slug:"most-answers");
		$question_bump_slug            = ($question_bump_slug != ""?$question_bump_slug:"question-bump");
		$answers_slug                  = ($answers_slug != ""?$answers_slug:"answers");
		$answers_might_like_slug       = ($answers_might_like_slug != ""?$answers_might_like_slug:"answers-might-like");
		$answers_for_you_slug          = ($answers_for_you_slug != ""?$answers_for_you_slug:"answers-for-you");
		$most_visit_slug               = ($most_visit_slug != ""?$most_visit_slug:"most-visit");
		$most_reacted_slug             = ($most_reacted_slug != ""?$most_reacted_slug:"most-reacted");
		$most_vote_slug                = ($most_vote_slug != ""?$most_vote_slug:"most-vote");
		$random_slug                   = ($random_slug != ""?$random_slug:"random");
		$question_new_slug             = ($question_new_slug != ""?$question_new_slug:"new");
		$question_sticky_slug          = ($question_sticky_slug != ""?$question_sticky_slug:"sticky");
		$question_polls_slug           = ($question_polls_slug != ""?$question_polls_slug:"polls");
		$question_followed_slug        = ($question_followed_slug != ""?$question_followed_slug:"followed");
		$question_favorites_slug       = ($question_favorites_slug != ""?$question_favorites_slug:"favorites");
		$no_answers_slug               = ($no_answers_slug != ""?$no_answers_slug:"no-answers");
		$poll_feed_slug                = ($poll_feed_slug != ""?$poll_feed_slug:"poll-feed");
		$recent_posts_slug             = ($recent_posts_slug != ""?$recent_posts_slug:"recent-posts");
		$posts_visited_slug            = ($posts_visited_slug != ""?$posts_visited_slug:"posts-visited");
		$recent_knowledgebases_slug    = ($recent_knowledgebases_slug != ""?$recent_knowledgebases_slug:"recent-knowledgebases");
		$random_knowledgebases_slug    = ($random_knowledgebases_slug != ""?$random_knowledgebases_slug:"random-knowledgebases");
		$sticky_knowledgebases_slug    = ($sticky_knowledgebases_slug != ""?$sticky_knowledgebases_slug:"sticky-knowledgebases");
		$knowledgebases_visited_slug   = ($knowledgebases_visited_slug != ""?$knowledgebases_visited_slug:"knowledgebases-visited");
		$knowledgebases_voted_slug     = ($knowledgebases_voted_slug != ""?$knowledgebases_voted_slug:"knowledgebases-voted");

		$feed_slug_2                   = ($feed_slug_2 != ""?$feed_slug_2:"feed-time");
		$recent_questions_slug_2       = ($recent_questions_slug_2 != ""?$recent_questions_slug_2:"recent-questions-time");
		$questions_for_you_slug_2      = ($questions_for_you_slug_2 != ""?$questions_for_you_slug_2:"questions-for-you-time");
		$most_answers_slug_2           = ($most_answers_slug_2 != ""?$most_answers_slug_2:"most-answers-time");
		$question_bump_slug_2          = ($question_bump_slug_2 != ""?$question_bump_slug_2:"question-bump-time");
		$answers_slug_2                = ($answers_slug_2 != ""?$answers_slug_2:"answers-time");
		$answers_might_like_slug_2     = ($answers_might_like_slug_2 != ""?$answers_might_like_slug_2:"answers-might-like-time");
		$answers_for_you_slug_2        = ($answers_for_you_slug_2 != ""?$answers_for_you_slug_2:"answers-for-you-time");
		$most_visit_slug_2             = ($most_visit_slug_2 != ""?$most_visit_slug_2:"most-visit-time");
		$most_reacted_slug_2           = ($most_reacted_slug_2 != ""?$most_reacted_slug_2:"most-reacted-time");
		$most_vote_slug_2              = ($most_vote_slug_2 != ""?$most_vote_slug_2:"most-vote-time");
		$random_slug_2                 = ($random_slug_2 != ""?$random_slug_2:"random-time");
		$question_new_slug_2           = ($question_new_slug_2 != ""?$question_new_slug_2:"new-time");
		$question_sticky_slug_2        = ($question_sticky_slug_2 != ""?$question_sticky_slug_2:"sticky-time");
		$question_polls_slug_2         = ($question_polls_slug_2 != ""?$question_polls_slug_2:"polls-time");
		$question_followed_slug_2      = ($question_followed_slug_2 != ""?$question_followed_slug_2:"followed-time");
		$question_favorites_slug_2     = ($question_favorites_slug_2 != ""?$question_favorites_slug_2:"favorites-time");
		$no_answers_slug_2             = ($no_answers_slug_2 != ""?$no_answers_slug_2:"no-answers-time");
		$poll_feed_slug_2              = ($poll_feed_slug_2 != ""?$poll_feed_slug_2:"poll-feed-time");
		$recent_posts_slug_2           = ($recent_posts_slug_2 != ""?$recent_posts_slug_2:"recent-posts-time");
		$posts_visited_slug_2          = ($posts_visited_slug_2 != ""?$posts_visited_slug_2:"posts-visited-time");
		$recent_knowledgebases_slug_2  = ($recent_knowledgebases_slug_2 != ""?$recent_knowledgebases_slug_2:"recent-knowledgebases-time");
		$random_knowledgebases_slug_2  = ($random_knowledgebases_slug_2 != ""?$random_knowledgebases_slug_2:"random-knowledgebases-time");
		$sticky_knowledgebases_slug_2  = ($sticky_knowledgebases_slug_2 != ""?$sticky_knowledgebases_slug_2:"sticky-knowledgebases-time");
		$knowledgebases_visited_slug_2 = ($knowledgebases_visited_slug_2 != ""?$knowledgebases_visited_slug_2:"knowledgebases-visited-time");
		$knowledgebases_voted_slug_2   = ($knowledgebases_voted_slug_2 != ""?$knowledgebases_voted_slug_2:"knowledgebases-voted-time");
	}

	$count_posts_categories = (int)(isset($mobile_api_options["count_posts_categories"])?$mobile_api_options["count_posts_categories"]:"");
	$count_posts_categories = ($count_posts_categories > 0?$count_posts_categories:6);
	$mobile_setting_categories = (isset($mobile_api_options["mobile_setting_categories"])?$mobile_api_options["mobile_setting_categories"]:"");

	$mobile_setting_categories_posts = (isset($mobile_api_options["mobile_setting_categories_posts"])?$mobile_api_options["mobile_setting_categories_posts"]:"");

	$count_posts_search = (int)(isset($mobile_api_options["count_posts_search"])?$mobile_api_options["count_posts_search"]:"");
	$count_posts_search = ($count_posts_search > 0?$count_posts_search:3);
	$mobile_setting_search = (isset($mobile_api_options["mobile_setting_search"])?$mobile_api_options["mobile_setting_search"]:"");

	$count_posts_favourites = (int)(isset($mobile_api_options["count_posts_favourites"])?$mobile_api_options["count_posts_favourites"]:"");
	$count_posts_favourites = ($count_posts_favourites > 0?$count_posts_favourites:10);
	$mobile_setting_favourites = (isset($mobile_api_options["mobile_setting_favourites"])?$mobile_api_options["mobile_setting_favourites"]:"");

	$count_posts_followed = (int)(isset($mobile_api_options["count_posts_followed"])?$mobile_api_options["count_posts_followed"]:"");
	$count_posts_followed = ($count_posts_followed > 0?$count_posts_followed:10);
	$mobile_setting_followed = (isset($mobile_api_options["mobile_setting_followed"])?$mobile_api_options["mobile_setting_followed"]:"");

	$count_posts_questions = (int)(isset($mobile_api_options["count_posts_questions"])?$mobile_api_options["count_posts_questions"]:"");
	$count_posts_questions = ($count_posts_questions > 0?$count_posts_questions:6);
	$mobile_setting_questions = (isset($mobile_api_options["mobile_setting_questions"])?$mobile_api_options["mobile_setting_questions"]:"");

	$count_posts_blog = (int)(isset($mobile_api_options["count_posts_blog"])?$mobile_api_options["count_posts_blog"]:"");
	$count_posts_blog = ($count_posts_blog > 0?$count_posts_blog:6);
	$mobile_setting_blog = (isset($mobile_api_options["mobile_setting_blog"])?$mobile_api_options["mobile_setting_blog"]:"");

	$menu_style_of_report = (isset($mobile_api_options["menu_style_of_report"])?$mobile_api_options["menu_style_of_report"]:"");
	$mobile_setting_single = (isset($mobile_api_options["mobile_setting_single"])?$mobile_api_options["mobile_setting_single"]:"");

	$mobile_setting_single_post = (isset($mobile_api_options["mobile_setting_single_post"])?$mobile_api_options["mobile_setting_single_post"]:"");

	$count_comments_mobile = (isset($mobile_api_options["count_comments_mobile"])?$mobile_api_options["count_comments_mobile"]:"");
	$vote_answer_mobile = (isset($mobile_api_options["vote_answer_mobile"])?$mobile_api_options["vote_answer_mobile"]:"");
	$mobile_setting_comments = (isset($mobile_api_options["mobile_setting_comments"])?$mobile_api_options["mobile_setting_comments"]:"");
	$mobile_setting_answers = (isset($mobile_api_options["mobile_setting_answers"])?$mobile_api_options["mobile_setting_answers"]:"");

	$custom_permission = (isset($mobile_api_options["custom_permission"])?$mobile_api_options["custom_permission"]:"");
	$show_ads = true;
	if ($custom_permission == mobile_api_checkbox_value) {
		if (($custom_permission == mobile_api_checkbox_value && $user_id > 0 && !is_super_admin($user_id) && empty($roles["without_ads"])) || ($custom_permission == mobile_api_checkbox_value && $user_id == 0)) {
			$show_ads = true;
		}else {
			$show_ads = false;
		}
	}
	$show_ads = apply_filters("mobile_api_show_ads",$show_ads);

	$mobile_adv = (isset($mobile_api_options["mobile_adv"])?$mobile_api_options["mobile_adv"]:"");
	$funding_choices = (isset($mobile_api_options["funding_choices"])?$mobile_api_options["funding_choices"]:"");
	$mobile_banner_adv = (isset($mobile_api_options["mobile_banner_adv"])?$mobile_api_options["mobile_banner_adv"]:"");
	$ad_mob_android = (isset($mobile_api_options["ad_mob_android"])?$mobile_api_options["ad_mob_android"]:"");
	$ad_mob_ios = (isset($mobile_api_options["ad_mob_ios"])?$mobile_api_options["ad_mob_ios"]:"");
	$ad_banner_android = (isset($mobile_api_options["ad_banner_android"])?$mobile_api_options["ad_banner_android"]:"");
	$ad_banner_ios = (isset($mobile_api_options["ad_banner_ios"])?$mobile_api_options["ad_banner_ios"]:"");
	$mobile_ads = (isset($mobile_api_options["mobile_ads"])?$mobile_api_options["mobile_ads"]:"");

	$banner_posts = (isset($mobile_ads["banner_posts"]) && $mobile_ads["banner_posts"] == "banner_posts"?"true":"");
	$banner_comments = (isset($mobile_ads["banner_comments"]) && $mobile_ads["banner_comments"] == "banner_comments"?"true":"");
	$mobile_ad_posts_position = (isset($mobile_api_options["mobile_ad_posts_position"])?$mobile_api_options["mobile_ad_posts_position"]:"");
	$mobile_ad_comments_position = (isset($mobile_api_options["mobile_ad_comments_position"])?$mobile_api_options["mobile_ad_comments_position"]:"");

	$mobile_ad_html_before_home = (isset($mobile_api_options["mobile_ad_html_before_home"])?$mobile_api_options["mobile_ad_html_before_home"]:"");
	$mobile_ad_html_before_home_type = (isset($mobile_api_options["mobile_ad_html_before_home_type"])?$mobile_api_options["mobile_ad_html_before_home_type"]:"");
	$mobile_ad_html_before_home_img = (isset($mobile_api_options["mobile_ad_html_before_home_img"])?mobile_api_image_url_id($mobile_api_options["mobile_ad_html_before_home_img"]):"");
	$mobile_ad_html_before_home_href = (isset($mobile_api_options["mobile_ad_html_before_home_href"])?$mobile_api_options["mobile_ad_html_before_home_href"]:"");
	$mobile_ad_html_before_home_code = (isset($mobile_api_options["mobile_ad_html_before_home_code"])?$mobile_api_options["mobile_ad_html_before_home_code"]:"");

	$mobile_ad_html_after_home = (isset($mobile_api_options["mobile_ad_html_after_home"])?$mobile_api_options["mobile_ad_html_after_home"]:"");
	$mobile_ad_html_after_home_type = (isset($mobile_api_options["mobile_ad_html_after_home_type"])?$mobile_api_options["mobile_ad_html_after_home_type"]:"");
	$mobile_ad_html_after_home_img = (isset($mobile_api_options["mobile_ad_html_after_home_img"])?mobile_api_image_url_id($mobile_api_options["mobile_ad_html_after_home_img"]):"");
	$mobile_ad_html_after_home_href = (isset($mobile_api_options["mobile_ad_html_after_home_href"])?$mobile_api_options["mobile_ad_html_after_home_href"]:"");
	$mobile_ad_html_after_home_code = (isset($mobile_api_options["mobile_ad_html_after_home_code"])?$mobile_api_options["mobile_ad_html_after_home_code"]:"");

	$mobile_interstitial_adv = (isset($mobile_api_options["mobile_interstitial_adv"])?$mobile_api_options["mobile_interstitial_adv"]:"");
	$ad_interstitial_android = (isset($mobile_api_options["ad_interstitial_android"])?$mobile_api_options["ad_interstitial_android"]:"");
	$ad_interstitial_ios = (isset($mobile_api_options["ad_interstitial_ios"])?$mobile_api_options["ad_interstitial_ios"]:"");
	$ad_interstitial_count = (isset($mobile_api_options["ad_interstitial_count"])?$mobile_api_options["ad_interstitial_count"]:"");
	$ad_interstitial_count = ($ad_interstitial_count != ""?$ad_interstitial_count:0);
	
	$mobile_rewarded_adv = (isset($mobile_api_options["mobile_rewarded_adv"])?$mobile_api_options["mobile_rewarded_adv"]:"");
	$ad_rewarded_android = (isset($mobile_api_options["ad_rewarded_android"])?$mobile_api_options["ad_rewarded_android"]:"");
	$ad_rewarded_ios = (isset($mobile_api_options["ad_rewarded_ios"])?$mobile_api_options["ad_rewarded_ios"]:"");
	$ad_rewarded_count = (isset($mobile_api_options["ad_rewarded_count"])?$mobile_api_options["ad_rewarded_count"]:"");
	$ad_rewarded_count = ($ad_rewarded_count != ""?$ad_rewarded_count:0);

	$loginbackground = (isset($mobile_api_options["loginbackground"])?$mobile_api_options["loginbackground"]:"");
	$errorcolor = (isset($mobile_api_options["errorcolor"])?$mobile_api_options["errorcolor"]:"");
	$errortextcolor = (isset($mobile_api_options["errortextcolor"])?$mobile_api_options["errortextcolor"]:"");
	$alertcolor = (isset($mobile_api_options["alertcolor"])?$mobile_api_options["alertcolor"]:"");
	$alerttextcolor = (isset($mobile_api_options["alerttextcolor"])?$mobile_api_options["alerttextcolor"]:"");
	$successcolor = (isset($mobile_api_options["successcolor"])?$mobile_api_options["successcolor"]:"");
	$successtextcolor = (isset($mobile_api_options["successtextcolor"])?$mobile_api_options["successtextcolor"]:"");
	$tooltipmenucolor = (isset($mobile_api_options["tooltipmenucolor"])?$mobile_api_options["tooltipmenucolor"]:"");
	$highlightcolor = (isset($mobile_api_options["highlightcolor"])?$mobile_api_options["highlightcolor"]:"");
	$highlighttextcolor = (isset($mobile_api_options["highlighttextcolor"])?$mobile_api_options["highlighttextcolor"]:"");
	$closequestionbuttoncolor = (isset($mobile_api_options["closequestionbuttoncolor"])?$mobile_api_options["closequestionbuttoncolor"]:"");
	$closequestionbackgroundcolor = (isset($mobile_api_options["closequestionbackgroundcolor"])?$mobile_api_options["closequestionbackgroundcolor"]:"");
	$openquestionbuttoncolor = (isset($mobile_api_options["openquestionbuttoncolor"])?$mobile_api_options["openquestionbuttoncolor"]:"");
	$openquestionbackgroundcolor = (isset($mobile_api_options["openquestionbackgroundcolor"])?$mobile_api_options["openquestionbackgroundcolor"]:"");
	$favouritecolor = (isset($mobile_api_options["favouritecolor"])?$mobile_api_options["favouritecolor"]:"");
	$unfavouritecolor = (isset($mobile_api_options["unfavouritecolor"])?$mobile_api_options["unfavouritecolor"]:"");
	$bestanswercolor = (isset($mobile_api_options["bestanswercolor"])?$mobile_api_options["bestanswercolor"]:"");
	$removebestanswercolor = (isset($mobile_api_options["removebestanswercolor"])?$mobile_api_options["removebestanswercolor"]:"");
	$addbestanswercolor = (isset($mobile_api_options["addbestanswercolor"])?$mobile_api_options["addbestanswercolor"]:"");
	$appbarbackgroundcolor = (isset($mobile_api_options["appbarbackgroundcolor"])?$mobile_api_options["appbarbackgroundcolor"]:"");
	$tabbarbackgroundcolor = (isset($mobile_api_options["tabbarbackgroundcolor"])?$mobile_api_options["tabbarbackgroundcolor"]:"");
	$bottombarbackgroundcolor = (isset($mobile_api_options["bottombarbackgroundcolor"])?$mobile_api_options["bottombarbackgroundcolor"]:"");
	$appbarcolor = (isset($mobile_api_options["appbarcolor"])?$mobile_api_options["appbarcolor"]:"");
	$tabbaractivetextcolor = (isset($mobile_api_options["tabbaractivetextcolor"])?$mobile_api_options["tabbaractivetextcolor"]:"");
	$tabbarindicatorcolor = (isset($mobile_api_options["tabbarindicatorcolor"])?$mobile_api_options["tabbarindicatorcolor"]:"");
	$tabbartextcolor = (isset($mobile_api_options["tabbartextcolor"])?$mobile_api_options["tabbartextcolor"]:"");
	$checkboxactivecolor = (isset($mobile_api_options["checkboxactivecolor"])?$mobile_api_options["checkboxactivecolor"]:"");
	$bottombaractivecolor = (isset($mobile_api_options["bottombaractivecolor"])?$mobile_api_options["bottombaractivecolor"]:"");
	$bottombarinactivecolor = (isset($mobile_api_options["bottombarinactivecolor"])?$mobile_api_options["bottombarinactivecolor"]:"");
	$mobile_primary = (isset($mobile_api_options["mobile_primary"])?$mobile_api_options["mobile_primary"]:"");
	$mobile_secondary = (isset($mobile_api_options["mobile_secondary"])?$mobile_api_options["mobile_secondary"]:"");
	$secondaryvariant = (isset($mobile_api_options["secondaryvariant"])?$mobile_api_options["secondaryvariant"]:"");
	$mobile_background = (isset($mobile_api_options["mobile_background"])?$mobile_api_options["mobile_background"]:"");
	$sidemenutextcolor = (isset($mobile_api_options["sidemenutextcolor"])?$mobile_api_options["sidemenutextcolor"]:"");
	$scaffoldbackgroundcolor = (isset($mobile_api_options["scaffoldbackgroundcolor"])?$mobile_api_options["scaffoldbackgroundcolor"]:"");
	$buttontextcolor = (isset($mobile_api_options["buttontextcolor"])?$mobile_api_options["buttontextcolor"]:"");
	$dividercolor = (isset($mobile_api_options["dividercolor"])?$mobile_api_options["dividercolor"]:"");
	$shadowcolor = (isset($mobile_api_options["shadowcolor"])?$mobile_api_options["shadowcolor"]:"");
	$buttonsbackgroudcolor = (isset($mobile_api_options["buttonsbackgroudcolor"])?$mobile_api_options["buttonsbackgroudcolor"]:"");
	$activate_input_border_bottom = (isset($mobile_api_options["activate_input_border_bottom"])?$mobile_api_options["activate_input_border_bottom"]:"");
	$inputsbackgroundcolor = (isset($mobile_api_options["inputsbackgroundcolor"])?$mobile_api_options["inputsbackgroundcolor"]:"");
	$input_border_bottom_color = (isset($mobile_api_options["input_border_bottom_color"])?$mobile_api_options["input_border_bottom_color"]:"");
	$settingbackgroundcolor = (isset($mobile_api_options["settingbackgroundcolor"])?$mobile_api_options["settingbackgroundcolor"]:"");
	$settingtextcolor = (isset($mobile_api_options["settingtextcolor"])?$mobile_api_options["settingtextcolor"]:"");
	$verifiedcolor = (isset($mobile_api_options["verifiedcolor"])?$mobile_api_options["verifiedcolor"]:"");

	$loginbackground_dark = (isset($mobile_api_options["dark_loginbackground"])?$mobile_api_options["dark_loginbackground"]:"");
	$errorcolor_dark = (isset($mobile_api_options["dark_errorcolor"])?$mobile_api_options["dark_errorcolor"]:"");
	$errortextcolor_dark = (isset($mobile_api_options["dark_errortextcolor"])?$mobile_api_options["dark_errortextcolor"]:"");
	$alertcolor_dark = (isset($mobile_api_options["dark_alertcolor"])?$mobile_api_options["dark_alertcolor"]:"");
	$alerttextcolor_dark = (isset($mobile_api_options["dark_alerttextcolor"])?$mobile_api_options["dark_alerttextcolor"]:"");
	$successcolor_dark = (isset($mobile_api_options["dark_successcolor"])?$mobile_api_options["dark_successcolor"]:"");
	$successtextcolor_dark = (isset($mobile_api_options["dark_successtextcolor"])?$mobile_api_options["dark_successtextcolor"]:"");
	$tooltipmenucolor_dark = (isset($mobile_api_options["dark_tooltipmenucolor"])?$mobile_api_options["dark_tooltipmenucolor"]:"");
	$highlightcolor_dark = (isset($mobile_api_options["dark_highlightcolor"])?$mobile_api_options["dark_highlightcolor"]:"");
	$highlighttextcolor_dark = (isset($mobile_api_options["dark_highlighttextcolor"])?$mobile_api_options["dark_highlighttextcolor"]:"");
	$closequestionbuttoncolor_dark = (isset($mobile_api_options["dark_closequestionbuttoncolor"])?$mobile_api_options["dark_closequestionbuttoncolor"]:"");
	$closequestionbackgroundcolor_dark = (isset($mobile_api_options["dark_closequestionbackgroundcolor"])?$mobile_api_options["dark_closequestionbackgroundcolor"]:"");
	$openquestionbuttoncolor_dark = (isset($mobile_api_options["dark_openquestionbuttoncolor"])?$mobile_api_options["dark_openquestionbuttoncolor"]:"");
	$openquestionbackgroundcolor_dark = (isset($mobile_api_options["dark_openquestionbackgroundcolor"])?$mobile_api_options["dark_openquestionbackgroundcolor"]:"");
	$favouritecolor_dark = (isset($mobile_api_options["dark_favouritecolor"])?$mobile_api_options["dark_favouritecolor"]:"");
	$unfavouritecolor_dark = (isset($mobile_api_options["dark_unfavouritecolor"])?$mobile_api_options["dark_unfavouritecolor"]:"");
	$bestanswercolor_dark = (isset($mobile_api_options["dark_bestanswercolor"])?$mobile_api_options["dark_bestanswercolor"]:"");
	$removebestanswercolor_dark = (isset($mobile_api_options["dark_removebestanswercolor"])?$mobile_api_options["dark_removebestanswercolor"]:"");
	$addbestanswercolor_dark = (isset($mobile_api_options["dark_addbestanswercolor"])?$mobile_api_options["dark_addbestanswercolor"]:"");
	$appbarbackgroundcolor_dark = (isset($mobile_api_options["dark_appbarbackgroundcolor"])?$mobile_api_options["dark_appbarbackgroundcolor"]:"");
	$tabbarbackgroundcolor_dark = (isset($mobile_api_options["dark_tabbarbackgroundcolor"])?$mobile_api_options["dark_tabbarbackgroundcolor"]:"");
	$bottombarbackgroundcolor_dark = (isset($mobile_api_options["dark_bottombarbackgroundcolor"])?$mobile_api_options["dark_bottombarbackgroundcolor"]:"");
	$appbarcolor_dark = (isset($mobile_api_options["dark_appbarcolor"])?$mobile_api_options["dark_appbarcolor"]:"");
	$tabbaractivetextcolor_dark = (isset($mobile_api_options["dark_tabbaractivetextcolor"])?$mobile_api_options["dark_tabbaractivetextcolor"]:"");
	$tabbarindicatorcolor_dark = (isset($mobile_api_options["dark_tabbarindicatorcolor"])?$mobile_api_options["dark_tabbarindicatorcolor"]:"");
	$tabbartextcolor_dark = (isset($mobile_api_options["dark_tabbartextcolor"])?$mobile_api_options["dark_tabbartextcolor"]:"");
	$checkboxactivecolor_dark = (isset($mobile_api_options["dark_checkboxactivecolor"])?$mobile_api_options["dark_checkboxactivecolor"]:"");
	$bottombaractivecolor_dark = (isset($mobile_api_options["dark_bottombaractivecolor"])?$mobile_api_options["dark_bottombaractivecolor"]:"");
	$bottombarinactivecolor_dark = (isset($mobile_api_options["dark_bottombarinactivecolor"])?$mobile_api_options["dark_bottombarinactivecolor"]:"");
	$mobile_primary_dark = (isset($mobile_api_options["dark_mobile_primary"])?$mobile_api_options["dark_mobile_primary"]:"");
	$mobile_secondary_dark = (isset($mobile_api_options["dark_mobile_secondary"])?$mobile_api_options["dark_mobile_secondary"]:"");
	$secondaryvariant_dark = (isset($mobile_api_options["dark_secondaryvariant"])?$mobile_api_options["dark_secondaryvariant"]:"");
	$mobile_background_dark = (isset($mobile_api_options["dark_mobile_background"])?$mobile_api_options["dark_mobile_background"]:"");
	$sidemenutextcolor_dark = (isset($mobile_api_options["dark_sidemenutextcolor"])?$mobile_api_options["dark_sidemenutextcolor"]:"");
	$scaffoldbackgroundcolor_dark = (isset($mobile_api_options["dark_scaffoldbackgroundcolor"])?$mobile_api_options["dark_scaffoldbackgroundcolor"]:"");
	$buttontextcolor_dark = (isset($mobile_api_options["dark_buttontextcolor"])?$mobile_api_options["dark_buttontextcolor"]:"");
	$dividercolor_dark = (isset($mobile_api_options["dark_dividercolor"])?$mobile_api_options["dark_dividercolor"]:"");
	$shadowcolor_dark = (isset($mobile_api_options["dark_shadowcolor"])?$mobile_api_options["dark_shadowcolor"]:"");
	$buttonsbackgroudcolor_dark = (isset($mobile_api_options["dark_buttonsbackgroudcolor"])?$mobile_api_options["dark_buttonsbackgroudcolor"]:"");
	$inputsbackgroundcolor_dark = (isset($mobile_api_options["dark_inputsbackgroundcolor"])?$mobile_api_options["dark_inputsbackgroundcolor"]:"");
	$input_border_bottom_color_dark = (isset($mobile_api_options["dark_input_border_bottom_color"])?$mobile_api_options["dark_input_border_bottom_color"]:"");
	$settingbackgroundcolor_dark = (isset($mobile_api_options["dark_settingbackgroundcolor"])?$mobile_api_options["dark_settingbackgroundcolor"]:"");
	$settingtextcolor_dark = (isset($mobile_api_options["dark_settingtextcolor"])?$mobile_api_options["dark_settingtextcolor"]:"");
	$verifiedcolor_dark = (isset($mobile_api_options["dark_verifiedcolor"])?$mobile_api_options["dark_verifiedcolor"]:"");

	$loginbackground_dark = ($loginbackground_dark != ""?$loginbackground_dark:$loginbackground);
	$errorcolor_dark = ($errorcolor_dark != ""?$errorcolor_dark:$errorcolor);
	$errortextcolor_dark = ($errortextcolor_dark != ""?$errortextcolor_dark:$errortextcolor);
	$alertcolor_dark = ($alertcolor_dark != ""?$alertcolor_dark:$alertcolor);
	$alerttextcolor_dark = ($alerttextcolor_dark != ""?$alerttextcolor_dark:$alerttextcolor);
	$successcolor_dark = ($successcolor_dark != ""?$successcolor_dark:$successcolor);
	$successtextcolor_dark = ($successtextcolor_dark != ""?$successtextcolor_dark:$successtextcolor);
	$tooltipmenucolor_dark = ($tooltipmenucolor_dark != ""?$tooltipmenucolor_dark:$tooltipmenucolor);
	$highlightcolor_dark = ($highlightcolor_dark != ""?$highlightcolor_dark:$highlightcolor);
	$highlighttextcolor_dark = ($highlighttextcolor_dark != ""?$highlighttextcolor_dark:$highlighttextcolor);
	$closequestionbuttoncolor_dark = ($closequestionbuttoncolor_dark != ""?$closequestionbuttoncolor_dark:$closequestionbuttoncolor);
	$closequestionbackgroundcolor_dark = ($closequestionbackgroundcolor_dark != ""?$closequestionbackgroundcolor_dark:$closequestionbackgroundcolor);
	$openquestionbuttoncolor_dark = ($openquestionbuttoncolor_dark != ""?$openquestionbuttoncolor_dark:$openquestionbuttoncolor);
	$openquestionbackgroundcolor_dark = ($openquestionbackgroundcolor_dark != ""?$openquestionbackgroundcolor_dark:$openquestionbackgroundcolor);
	$favouritecolor_dark = ($favouritecolor_dark != ""?$favouritecolor_dark:$favouritecolor);
	$unfavouritecolor_dark = ($unfavouritecolor_dark != ""?$unfavouritecolor_dark:$unfavouritecolor);
	$bestanswercolor_dark = ($bestanswercolor_dark != ""?$bestanswercolor_dark:$bestanswercolor);
	$removebestanswercolor_dark = ($removebestanswercolor_dark != ""?$removebestanswercolor_dark:$removebestanswercolor);
	$addbestanswercolor_dark = ($addbestanswercolor_dark != ""?$addbestanswercolor_dark:$addbestanswercolor);
	$appbarbackgroundcolor_dark = ($appbarbackgroundcolor_dark != ""?$appbarbackgroundcolor_dark:$appbarbackgroundcolor);
	$tabbarbackgroundcolor_dark = ($tabbarbackgroundcolor_dark != ""?$tabbarbackgroundcolor_dark:$tabbarbackgroundcolor);
	$bottombarbackgroundcolor_dark = ($bottombarbackgroundcolor_dark != ""?$bottombarbackgroundcolor_dark:$bottombarbackgroundcolor);
	$appbarcolor_dark = ($appbarcolor_dark != ""?$appbarcolor_dark:$appbarcolor);
	$tabbaractivetextcolor_dark = ($tabbaractivetextcolor_dark != ""?$tabbaractivetextcolor_dark:$tabbaractivetextcolor);
	$tabbarindicatorcolor_dark = ($tabbarindicatorcolor_dark != ""?$tabbarindicatorcolor_dark:$tabbarindicatorcolor);
	$tabbartextcolor_dark = ($tabbartextcolor_dark != ""?$tabbartextcolor_dark:$tabbartextcolor);
	$checkboxactivecolor_dark = ($checkboxactivecolor_dark != ""?$checkboxactivecolor_dark:$checkboxactivecolor);
	$bottombaractivecolor_dark = ($bottombaractivecolor_dark != ""?$bottombaractivecolor_dark:$bottombaractivecolor);
	$bottombarinactivecolor_dark = ($bottombarinactivecolor_dark != ""?$bottombarinactivecolor_dark:$bottombarinactivecolor);
	$mobile_primary_dark = ($mobile_primary_dark != ""?$mobile_primary_dark:$mobile_primary);
	$mobile_secondary_dark = ($mobile_secondary_dark != ""?$mobile_secondary_dark:$mobile_secondary);
	$secondaryvariant_dark = ($secondaryvariant_dark != ""?$secondaryvariant_dark:$secondaryvariant);
	$mobile_background_dark = ($mobile_background_dark != ""?$mobile_background_dark:$mobile_background);
	$sidemenutextcolor_dark = ($sidemenutextcolor_dark != ""?$sidemenutextcolor_dark:$sidemenutextcolor);
	$scaffoldbackgroundcolor_dark = ($scaffoldbackgroundcolor_dark != ""?$scaffoldbackgroundcolor_dark:$scaffoldbackgroundcolor);
	$buttontextcolor_dark = ($buttontextcolor_dark != ""?$buttontextcolor_dark:$buttontextcolor);
	$dividercolor_dark = ($dividercolor_dark != ""?$dividercolor_dark:$dividercolor);
	$shadowcolor_dark = ($shadowcolor_dark != ""?$shadowcolor_dark:$shadowcolor);
	$buttonsbackgroudcolor_dark = ($buttonsbackgroudcolor_dark != ""?$buttonsbackgroudcolor_dark:$buttonsbackgroudcolor);
	$inputsbackgroundcolor_dark = ($inputsbackgroundcolor_dark != ""?$inputsbackgroundcolor_dark:$inputsbackgroundcolor);
	$input_border_bottom_color_dark = ($input_border_bottom_color_dark != ""?$input_border_bottom_color_dark:$input_border_bottom_color);
	$settingbackgroundcolor_dark = ($settingbackgroundcolor_dark != ""?$settingbackgroundcolor_dark:$settingbackgroundcolor);
	$settingtextcolor_dark = ($settingtextcolor_dark != ""?$settingtextcolor_dark:$settingtextcolor);
	$verifiedcolor_dark = ($verifiedcolor_dark != ""?$verifiedcolor_dark:$verifiedcolor);

	$loginbackground = str_replace("#","",$loginbackground);
	$errorcolor = str_replace("#","",$errorcolor);
	$errortextcolor = str_replace("#","",$errortextcolor);
	$alertcolor = str_replace("#","",$alertcolor);
	$alerttextcolor = str_replace("#","",$alerttextcolor);
	$successcolor = str_replace("#","",$successcolor);
	$successtextcolor = str_replace("#","",$successtextcolor);
	$tooltipmenucolor = str_replace("#","",$tooltipmenucolor);
	$highlightcolor = str_replace("#","",$highlightcolor);
	$highlighttextcolor = str_replace("#","",$highlighttextcolor);
	$closequestionbuttoncolor = str_replace("#","",$closequestionbuttoncolor);
	$closequestionbackgroundcolor = str_replace("#","",$closequestionbackgroundcolor);
	$openquestionbuttoncolor = str_replace("#","",$openquestionbuttoncolor);
	$openquestionbackgroundcolor = str_replace("#","",$openquestionbackgroundcolor);
	$favouritecolor = str_replace("#","",$favouritecolor);
	$unfavouritecolor = str_replace("#","",$unfavouritecolor);
	$bestanswercolor = str_replace("#","",$bestanswercolor);
	$removebestanswercolor = str_replace("#","",$removebestanswercolor);
	$addbestanswercolor = str_replace("#","",$addbestanswercolor);
	$appbarbackgroundcolor = str_replace("#","",$appbarbackgroundcolor);
	$tabbarbackgroundcolor = str_replace("#","",$tabbarbackgroundcolor);
	$bottombarbackgroundcolor = str_replace("#","",$bottombarbackgroundcolor);
	$appbarcolor = str_replace("#","",$appbarcolor);
	$tabbaractivetextcolor = str_replace("#","",$tabbaractivetextcolor);
	$tabbarindicatorcolor = str_replace("#","",$tabbarindicatorcolor);
	$tabbartextcolor = str_replace("#","",$tabbartextcolor);
	$checkboxactivecolor = str_replace("#","",$checkboxactivecolor);
	$bottombaractivecolor = str_replace("#","",$bottombaractivecolor);
	$bottombarinactivecolor = str_replace("#","",$bottombarinactivecolor);
	$mobile_primary = str_replace("#","",$mobile_primary);
	$mobile_secondary = str_replace("#","",$mobile_secondary);
	$secondaryvariant = str_replace("#","",$secondaryvariant);
	$mobile_background = str_replace("#","",$mobile_background);
	$sidemenutextcolor = str_replace("#","",$sidemenutextcolor);
	$scaffoldbackgroundcolor = str_replace("#","",$scaffoldbackgroundcolor);
	$buttontextcolor = str_replace("#","",$buttontextcolor);
	$dividercolor = str_replace("#","",$dividercolor);
	$shadowcolor = str_replace("#","",$shadowcolor);
	$buttonsbackgroudcolor = str_replace("#","",$buttonsbackgroudcolor);
	$inputsbackgroundcolor = "rgba(".implode(",",mobile_hex2rgb($inputsbackgroundcolor)).",0.04)";
	$input_border_bottom_color = "rgba(".implode(",",mobile_hex2rgb($input_border_bottom_color)).",0.1)";
	$settingbackgroundcolor = str_replace("#","",$settingbackgroundcolor);
	$settingtextcolor = str_replace("#","",$settingtextcolor);
	$verifiedcolor = str_replace("#","",$verifiedcolor);

	$loginbackground_dark = str_replace("#","",$loginbackground_dark);
	$errorcolor_dark = str_replace("#","",$errorcolor_dark);
	$errortextcolor_dark = str_replace("#","",$errortextcolor_dark);
	$alertcolor_dark = str_replace("#","",$alertcolor_dark);
	$alerttextcolor_dark = str_replace("#","",$alerttextcolor_dark);
	$successcolor_dark = str_replace("#","",$successcolor_dark);
	$successtextcolor_dark = str_replace("#","",$successtextcolor_dark);
	$tooltipmenucolor_dark = str_replace("#","",$tooltipmenucolor_dark);
	$highlightcolor_dark = str_replace("#","",$highlightcolor_dark);
	$highlighttextcolor_dark = str_replace("#","",$highlighttextcolor_dark);
	$closequestionbuttoncolor_dark = str_replace("#","",$closequestionbuttoncolor_dark);
	$closequestionbackgroundcolor_dark = str_replace("#","",$closequestionbackgroundcolor_dark);
	$openquestionbuttoncolor_dark = str_replace("#","",$openquestionbuttoncolor_dark);
	$openquestionbackgroundcolor_dark = str_replace("#","",$openquestionbackgroundcolor_dark);
	$favouritecolor_dark = str_replace("#","",$favouritecolor_dark);
	$unfavouritecolor_dark = str_replace("#","",$unfavouritecolor_dark);
	$bestanswercolor_dark = str_replace("#","",$bestanswercolor_dark);
	$removebestanswercolor_dark = str_replace("#","",$removebestanswercolor_dark);
	$addbestanswercolor_dark = str_replace("#","",$addbestanswercolor_dark);
	$appbarbackgroundcolor_dark = str_replace("#","",$appbarbackgroundcolor_dark);
	$tabbarbackgroundcolor_dark = str_replace("#","",$tabbarbackgroundcolor_dark);
	$bottombarbackgroundcolor_dark = str_replace("#","",$bottombarbackgroundcolor_dark);
	$appbarcolor_dark = str_replace("#","",$appbarcolor_dark);
	$tabbaractivetextcolor_dark = str_replace("#","",$tabbaractivetextcolor_dark);
	$tabbarindicatorcolor_dark = str_replace("#","",$tabbarindicatorcolor_dark);
	$tabbartextcolor_dark = str_replace("#","",$tabbartextcolor_dark);
	$checkboxactivecolor_dark = str_replace("#","",$checkboxactivecolor_dark);
	$bottombaractivecolor_dark = str_replace("#","",$bottombaractivecolor_dark);
	$bottombarinactivecolor_dark = str_replace("#","",$bottombarinactivecolor_dark);
	$mobile_primary_dark = str_replace("#","",$mobile_primary_dark);
	$mobile_secondary_dark = str_replace("#","",$mobile_secondary_dark);
	$secondaryvariant_dark = str_replace("#","",$secondaryvariant_dark);
	$mobile_background_dark = str_replace("#","",$mobile_background_dark);
	$sidemenutextcolor_dark = str_replace("#","",$sidemenutextcolor_dark);
	$scaffoldbackgroundcolor_dark = str_replace("#","",$scaffoldbackgroundcolor_dark);
	$buttontextcolor_dark = str_replace("#","",$buttontextcolor_dark);
	$dividercolor_dark = str_replace("#","",$dividercolor_dark);
	$shadowcolor_dark = str_replace("#","",$shadowcolor_dark);
	$buttonsbackgroudcolor_dark = str_replace("#","",$buttonsbackgroudcolor_dark);
	$inputsbackgroundcolor_dark = str_replace("#","",$inputsbackgroundcolor_dark);
	$input_border_bottom_color_dark = "rgba(".implode(",",mobile_hex2rgb($input_border_bottom_color_dark)).",0.1)";
	$settingbackgroundcolor_dark = str_replace("#","",$settingbackgroundcolor_dark);
	$settingtextcolor_dark = str_replace("#","",$settingtextcolor_dark);
	$verifiedcolor_dark = str_replace("#","",$verifiedcolor_dark);

	$activate_register = (isset($mobile_api_options["activate_register"])?$mobile_api_options["activate_register"]:"");
	$activate_login = (isset($mobile_api_options["activate_login"])?$mobile_api_options["activate_login"]:"");
	$register_items = (isset($mobile_api_options["register_items"])?$mobile_api_options["register_items"]:"");
	$gender_other = (isset($mobile_api_options["gender_other"])?$mobile_api_options["gender_other"]:"");
	$site_users_only = (isset($mobile_api_options["site_users_only"])?$mobile_api_options["site_users_only"]:"");
	$mobile_logged_only = (isset($mobile_api_options["mobile_logged_only"])?$mobile_api_options["mobile_logged_only"]:"");
	$site_users_only = apply_filters("mobile_api_users_only",$site_users_only);
	$activate_custom_register_link = (isset($mobile_api_options["activate_custom_register_link"])?$mobile_api_options["activate_custom_register_link"]:"");
	$custom_register_link = (isset($mobile_api_options["custom_register_link"])?$mobile_api_options["custom_register_link"]:"");
	$terms_active_register = (isset($mobile_api_options["terms_active_register"])?$mobile_api_options["terms_active_register"]:"");

	$edit_profile_items_1 = (isset($mobile_api_options["edit_profile_items_1"])?$mobile_api_options["edit_profile_items_1"]:"");
	$edit_profile_items_2 = (isset($mobile_api_options["edit_profile_items_2"])?$mobile_api_options["edit_profile_items_2"]:"");
	$edit_profile_items_3 = (isset($mobile_api_options["edit_profile_items_3"])?$mobile_api_options["edit_profile_items_3"]:"");
	$edit_profile_items_4 = (isset($mobile_api_options["edit_profile_items_4"])?$mobile_api_options["edit_profile_items_4"]:"");
	$active_message = (isset($mobile_api_options["active_message"])?$mobile_api_options["active_message"]:"");
	$url_required_profile = (isset($mobile_api_options["url_required_profile"])?$mobile_api_options["url_required_profile"]:"");
	$url_profile = (isset($mobile_api_options["url_profile"])?$mobile_api_options["url_profile"]:"");
	$profile_picture_profile = (isset($mobile_api_options["profile_picture_profile"])?$mobile_api_options["profile_picture_profile"]:"");
	$payment_available = (has_wpqa()?wpqa_payment_available():false);
	$question_schedules = (isset($mobile_api_options["question_schedules"])?$mobile_api_options["question_schedules"]:"");
	$question_schedules_groups = (isset($mobile_api_options["question_schedules_groups"])?$mobile_api_options["question_schedules_groups"]:"");
	$post_schedules = (isset($mobile_api_options["post_schedules"])?$mobile_api_options["post_schedules"]:"");
	$post_schedules_groups = (isset($mobile_api_options["post_schedules_groups"])?$mobile_api_options["post_schedules_groups"]:"");

	$send_email_and_notification_question = (isset($mobile_api_options["send_email_and_notification_question"])?$mobile_api_options["send_email_and_notification_question"]:"");
	$send_email_new_question_value = "send_email_new_question";
	$send_email_question_groups_value = "send_email_question_groups";
	if ($send_email_and_notification_question == "both") {
		$send_email_new_question_value = "send_email_new_question_both";
		$send_email_question_groups_value = "send_email_question_groups_both";
	}
	$send_email_new_question = (isset($mobile_api_options[$send_email_new_question_value])?$mobile_api_options[$send_email_new_question_value]:"");
	$send_email_question_groups = (isset($mobile_api_options[$send_email_question_groups_value])?$mobile_api_options[$send_email_question_groups_value]:"");

	$send_email_and_notification_post = (isset($mobile_api_options["send_email_and_notification_post"])?$mobile_api_options["send_email_and_notification_post"]:"");
	$send_email_new_post_value = "send_email_new_post";
	$send_email_post_groups_value = "send_email_post_groups";
	if ($send_email_and_notification_post == "both") {
		$send_email_new_post_value = "send_email_new_post_both";
		$send_email_post_groups_value = "send_email_post_groups_both";
	}
	$send_email_new_post = (isset($mobile_api_options[$send_email_new_post_value])?$mobile_api_options[$send_email_new_post_value]:"");
	$send_email_post_groups = (isset($mobile_api_options[$send_email_post_groups_value])?$mobile_api_options[$send_email_post_groups_value]:"");
	
	$mobile_setting_dislike = (isset($mobile_api_options["mobile_setting_dislike"])?$mobile_api_options["mobile_setting_dislike"]:"");
	$ask_question_items = (isset($mobile_api_options["ask_question_items"])?$mobile_api_options["ask_question_items"]:"");
	$categories_question = (isset($ask_question_items["categories_question"]) && isset($ask_question_items["categories_question"]["value"]) && $ask_question_items["categories_question"]["value"] == "categories_question"?true:false);
	$question_category_required = (isset($mobile_api_options["question_category_required"])?$mobile_api_options["question_category_required"]:"");
	$content_question_required = (isset($mobile_api_options["comment_question"])?$mobile_api_options["comment_question"]:"");
	$poll_only = (isset($mobile_api_options["poll_only"])?$mobile_api_options["poll_only"]:"");
	$custom_poll_groups = (isset($mobile_api_options["custom_poll_groups"])?$mobile_api_options["custom_poll_groups"]:"");
	$poll_groups = (isset($mobile_api_options["poll_groups"])?$mobile_api_options["poll_groups"]:"");
	$multicheck_poll = (isset($mobile_api_options["multicheck_poll"])?$mobile_api_options["multicheck_poll"]:"");
	$poll_image = (isset($mobile_api_options["poll_image"])?$mobile_api_options["poll_image"]:"");
	$poll_image_title = (isset($mobile_api_options["poll_image_title"])?$mobile_api_options["poll_image_title"]:"");
	$poll_image_title_required = (isset($mobile_api_options["poll_image_title_required"])?$mobile_api_options["poll_image_title_required"]:"");
	$question_tags_number_min_limit = (int)(isset($mobile_api_options["question_tags_number_min_limit"])?$mobile_api_options["question_tags_number_min_limit"]:"");

	$ask_user_items = (isset($mobile_api_options["ask_user_items"])?$mobile_api_options["ask_user_items"]:"");
	$add_question_default_user = (isset($mobile_api_options["add_question_default_user"])?$mobile_api_options["add_question_default_user"]:"");
	$content_user_question_required = (isset($mobile_api_options["content_ask_user"])?$mobile_api_options["content_ask_user"]:"");

	$ask_question_no_register = (isset($mobile_api_options["ask_question_no_register"])?$mobile_api_options["ask_question_no_register"]:"");
	$add_post_no_register = (isset($mobile_api_options["add_post_no_register"])?$mobile_api_options["add_post_no_register"]:"");
	$send_message_no_register = (isset($mobile_api_options["send_message_no_register"])?$mobile_api_options["send_message_no_register"]:"");
	$poll_user_only = (isset($mobile_api_options["poll_user_only"])?$mobile_api_options["poll_user_only"]:"");
	$active_vote_unlogged = (isset($mobile_api_options["active_vote_unlogged"])?$mobile_api_options["active_vote_unlogged"]:"");
	$post_comments_user = (isset($mobile_api_options["post_comments_user"])?$mobile_api_options["post_comments_user"]:"");

	$terms_checked_register = (isset($mobile_api_options["terms_checked_register"])?$mobile_api_options["terms_checked_register"]:"");
	$add_question_default = (isset($mobile_api_options["add_question_default"])?$mobile_api_options["add_question_default"]:"");
	$terms_checked_comment = (isset($mobile_api_options["terms_checked_comment"])?$mobile_api_options["terms_checked_comment"]:"");
	$private_checked_answer = (isset($mobile_api_options["private_checked_answer"])?$mobile_api_options["private_checked_answer"]:"");
	$video_checked_answer = (isset($mobile_api_options["video_checked_answer"])?$mobile_api_options["video_checked_answer"]:"");
	$anonymously_checked_answer = (isset($mobile_api_options["anonymously_checked_answer"])?$mobile_api_options["anonymously_checked_answer"]:"");

	$ask_question = (isset($mobile_api_options["ask_question"])?$mobile_api_options["ask_question"]:"");
	$show_question = (isset($mobile_api_options["show_question"])?$mobile_api_options["show_question"]:"");
	$add_answer = (isset($mobile_api_options["add_answer"])?$mobile_api_options["add_answer"]:"");
	$show_answer = (isset($mobile_api_options["show_answer"])?$mobile_api_options["show_answer"]:"");
	$add_post = (isset($mobile_api_options["add_post"])?$mobile_api_options["add_post"]:"");
	$send_message = (isset($mobile_api_options["send_message"])?$mobile_api_options["send_message"]:"");
	$show_post = (isset($mobile_api_options["show_post"])?$mobile_api_options["show_post"]:"");
	$add_comment = (isset($mobile_api_options["add_comment"])?$mobile_api_options["add_comment"]:"");
	$show_comment = (isset($mobile_api_options["show_comment"])?$mobile_api_options["show_comment"]:"");
	$show_knowledgebase = (isset($mobile_api_options["show_knowledgebase"])?$mobile_api_options["show_knowledgebase"]:"");

	$add_post_items = (isset($mobile_api_options["add_post_items"])?$mobile_api_options["add_post_items"]:"");
	$content_post = (isset($add_post_items["content_post"]) && isset($add_post_items["content_post"]["value"]) && $add_post_items["content_post"]["value"] == "content_post"?true:false);

	$featured_image_answer = (isset($mobile_api_options["featured_image_answer"])?$mobile_api_options["featured_image_answer"]:"");
	$answer_anonymously = (isset($mobile_api_options["answer_anonymously"])?$mobile_api_options["answer_anonymously"]:"");
	$private_answer = (isset($mobile_api_options["private_answer"])?$mobile_api_options["private_answer"]:"");
	$answer_video = (isset($mobile_api_options["answer_video"])?$mobile_api_options["answer_video"]:"");
	$attachment_answer = (isset($mobile_api_options["attachment_answer"])?$mobile_api_options["attachment_answer"]:"");
	$terms_active_comment = (isset($mobile_api_options["terms_active_comment"])?$mobile_api_options["terms_active_comment"]:"");

	$block_users = (isset($mobile_api_options["block_users"])?$mobile_api_options["block_users"]:"");
	$question_follow = (isset($mobile_api_options["question_follow"])?$mobile_api_options["question_follow"]:"");
	$cover_image = (isset($mobile_api_options["cover_image"])?$mobile_api_options["cover_image"]:"");
	$active_reaction = (isset($mobile_api_options["active_reaction"])?$mobile_api_options["active_reaction"]:"");
	$reaction_items = (isset($mobile_api_options["reaction_items"])?$mobile_api_options["reaction_items"]:"");
	$active_reaction_answers = (isset($mobile_api_options["active_reaction_answers"])?$mobile_api_options["active_reaction_answers"]:"");

	$activate_captcha_mobile = (isset($mobile_api_options["activate_captcha_mobile"])?$mobile_api_options["activate_captcha_mobile"]:"");
	$captcha_positions = (isset($mobile_api_options["captcha_positions"])?$mobile_api_options["captcha_positions"]:"");
	$captcha_login = (isset($captcha_positions["login"]) && $captcha_positions["login"] == "login"?true:false);
	$captcha_register = (isset($captcha_positions["register"]) && $captcha_positions["register"] == "register"?true:false);
	$captcha_answer = (isset($captcha_positions["answer"]) && $captcha_positions["answer"] == "answer"?true:false);
	$captcha_question = (isset($captcha_positions["question"]) && $captcha_positions["question"] == "question"?true:false);
	$site_key_recaptcha_mobile = (isset($mobile_api_options["site_key_recaptcha_mobile"])?$mobile_api_options["site_key_recaptcha_mobile"]:"");
	$secret_key_recaptcha_mobile = (isset($mobile_api_options["secret_key_recaptcha_mobile"])?$mobile_api_options["secret_key_recaptcha_mobile"]:"");

	$mobile_notifications_tabs = (isset($mobile_api_options["mobile_notifications_tabs"])?$mobile_api_options["mobile_notifications_tabs"]:"");

	$mobile_answers_icon = (isset($mobile_api_options["mobile_answers_icon"])?$mobile_api_options["mobile_answers_icon"]:"0xe907");
	$mobile_best_answers_icon = (isset($mobile_api_options["mobile_best_answers_icon"])?$mobile_api_options["mobile_best_answers_icon"]:"0xe906");
	$mobile_delete_icon = (isset($mobile_api_options["mobile_delete_icon"])?$mobile_api_options["mobile_delete_icon"]:"0xf041");
	$mobile_close_icon = (isset($mobile_api_options["mobile_close_icon"])?$mobile_api_options["mobile_close_icon"]:"0xedf1");
	$mobile_open_icon = (isset($mobile_api_options["mobile_open_icon"])?$mobile_api_options["mobile_open_icon"]:"0xedf0");
	$mobile_favourite_icon = (isset($mobile_api_options["mobile_favourite_icon"])?$mobile_api_options["mobile_favourite_icon"]:"0xe931");
	$mobile_unfavourite_icon = (isset($mobile_api_options["mobile_unfavourite_icon"])?$mobile_api_options["mobile_unfavourite_icon"]:"0xe9cb");
	$mobile_views_icon = (isset($mobile_api_options["mobile_views_icon"])?$mobile_api_options["mobile_views_icon"]:"fa-eye");
	$activate_verified_icon = (isset($mobile_api_options["activate_verified_icon"])?$mobile_api_options["activate_verified_icon"]:"");
	$mobile_verified_icon = (isset($mobile_api_options["mobile_verified_icon"])?$mobile_api_options["mobile_verified_icon"]:"0xef82");
	$activate_vote_icons = (isset($mobile_api_options["activate_vote_icons"])?$mobile_api_options["activate_vote_icons"]:"");
	$mobile_upvote_icon = (isset($mobile_api_options["mobile_upvote_icon"])?$mobile_api_options["mobile_upvote_icon"]:"0xe9c6");
	$mobile_downvote_icon = (isset($mobile_api_options["mobile_downvote_icon"])?$mobile_api_options["mobile_downvote_icon"]:"0xe9c7");
	$mobile_social_icon_style = (isset($mobile_api_options["mobile_social_icon_style"])?$mobile_api_options["mobile_social_icon_style"]:"");

	$activate_mobile_construction = (isset($mobile_api_options["activate_mobile_construction"])?$mobile_api_options["activate_mobile_construction"]:"");
	$construction_title = (isset($mobile_api_options["construction_title"])?$mobile_api_options["construction_title"]:"");
	$construction_content = (isset($mobile_api_options["construction_content"])?$mobile_api_options["construction_content"]:"");
	$construction_image = (isset($mobile_api_options["construction_image"])?mobile_api_image_url_id($mobile_api_options["construction_image"]):"");
	$activate_construction_icon = (isset($mobile_api_options["activate_construction_icon"])?$mobile_api_options["activate_construction_icon"]:"");
	$activate_construction_button = (isset($mobile_api_options["activate_construction_button"])?$mobile_api_options["activate_construction_button"]:"");
	$construction_button_text = (isset($mobile_api_options["construction_button_text"])?$mobile_api_options["construction_button_text"]:"");
	$construction_button_url = (isset($mobile_api_options["construction_button_url"])?$mobile_api_options["construction_button_url"]:"");
	$construction_button_color = (isset($mobile_api_options["construction_button_color"])?$mobile_api_options["construction_button_color"]:"");

	$author_visits = (isset($mobile_api_options["author_visits"])?$mobile_api_options["author_visits"]:"");

	$get_version = (int)get_option("mobile-config-version",1);

	$array["version"] = $get_version;

	$check_now = get_option("mobile_check_now");
	$array["checkWhen"] = ($check_now != ""?"notNow":"now");
	
	if ($author_visits == mobile_api_checkbox_value) {
		$array["visits"]["users"] = "true";
	}

	$construction_button_color = str_replace("#","",$construction_button_color);
	$construction_button_color = ($construction_button_color != ""?$construction_button_color:mobile_api_theme_color);

	if ($activate_mobile_construction == mobile_api_checkbox_value) {
		$array["expired"] = "true";
		if ($construction_title != "") {
			$array["expiredTitle"] = $construction_title;
		}
		if ($construction_content != "") {
			$array["expiredBody"] = $construction_content;
		}
		if ($construction_image != "") {
			$array["expiredImage"] = $construction_image;
		}
		if ($activate_construction_button == mobile_api_checkbox_value && $construction_button_text != "") {
			$array["expiredButtonTitle"] = $construction_button_text;
			$array["expiredColor"] = $construction_button_color;
			$array["expiredGotoUrl"] = $construction_button_url;
		}
		if ($activate_construction_icon == mobile_api_checkbox_value) {
			$array["expiredIcon"] = "true";
		}
	}

	$show_rtl = apply_filters("mobile_api_show_rtl",false);
	if (is_rtl() || $show_rtl == true) {
		$array["rtl"] = "true";
	}
	if ($force_update == mobile_api_checkbox_value && $android_version != "") {
		$array["update"]["android"] = $android_version;
	}
	if ($force_update == mobile_api_checkbox_value && $ios_version != "" && $app_apple_id != "") {
		$array["update"]["ios"] = $ios_version;
		$array["iosStoreID"] = $app_apple_id;
	}
	$array["themeMode"] = "ThemeMode.".$app_skin;
	$array["statusBarWhiteForeground"] = "true";
	if ($activate_input_border_bottom != mobile_api_checkbox_value) {
		$array["inputsWithBackground"] = "true";
	}

	if ($read_more_answer != mobile_api_checkbox_value) {
		$array["fullText"] = "true";
	}

	$array["inActiveDuration"]["duration"] = "{$mobile_time_login_time}";
	$array["inActiveDuration"]["metric"] = "{$mobile_metric}";

	if ($onboardmodels == mobile_api_checkbox_value) {
		$array["onboardModels"] = [
			[
				"title"    => $onboardmodels_title_1,
				"subTitle" => $onboardmodels_subtitle_1,
				"image"    => $onboardmodels_img_1,
			],
			[
				"title"    => $onboardmodels_title_2,
				"subTitle" => $onboardmodels_subtitle_2,
				"image"    => $onboardmodels_img_2,
			],
			[
				"title"    => $onboardmodels_title_3,
				"subTitle" => $onboardmodels_subtitle_3,
				"image"    => $onboardmodels_img_3,
			],
		];
	}

	foreach ($array_options_comments as $key => $value) {
		if (isset($mobile_setting_comments[$key]) && $mobile_setting_comments[$key] == $key) {
			if ($value == "author") {
				$array["postCommentAuthor"] = "true";
				$array["postCommentOwnerRank"] = "true";
			}else if ($value == "authorImage") {
				$array["postCommentImageAuthor"] = "true";
			}
		}
	}

	foreach ($array_options_comments as $key => $value) {
		if (isset($mobile_setting_answers[$key]) && $mobile_setting_answers[$key] == $key) {
			if ($value == "author") {
				$array["answerAuthor"] = "true";
				$array["answerOwnerRank"] = "true";
			}else if ($value == "authorImage") {
				$array["answerImageAuthor"] = "true";
			}
		}
	}

	if ($home_page_app > 0) {
		if (is_array($home_tabs_options) && !empty($home_tabs_options)) {
			$post_options["count"] = "{$count_posts_home}";
			if ($show_ads == true && $banner_posts == "true") {
				$post_options["adsCount"] = $mobile_ad_posts_position;
			}
			foreach ($array_options_posts as $key => $value) {
				if (isset($mobile_setting_home_posts[$key]) && $mobile_setting_home_posts[$key] == $key) {
					if ($value == "author") {
						$post_options["author"] = "true";
						$post_options["ownerRank"] = "true";
					}else {
						$post_options[$value] = "true";
					}
				}
			}
			foreach ($home_tabs_options as $key => $value) {
				if (isset($value["value"]) && isset($value["cat"])) {
					if (is_numeric($value["value"]) && $value["value"] > 0) {
						$get_tax = get_term($value["value"]);
						if (isset($get_tax->term_id) && $get_tax->term_id > 0) {
							$home_tabs[] = array("key" => $key,"tabs_type" => "question","postLayout" => "PostLayout.questionPost","title" => esc_html($get_tax->name),"url" => mobile_api_posts_category."?id=".$get_tax->term_id."&taxonomy=".$get_tax->taxonomy."&type=question");
						}else {
							$home_tabs[] = array("key" => $key,"tabs_type" => "question","postLayout" => "PostLayout.questionPost","title" => esc_html__("All Questions","mobile-api"),"url" => mobile_api_posts."?post_type=".mobile_api_questions_type."&page=1&type=question");
						}
					}else {
						$home_tabs[] = array("key" => $key,"tabs_type" => "question","postLayout" => "PostLayout.questionPost","title" => esc_html__("All Questions","mobile-api"),"url" => mobile_api_posts."?post_type=".mobile_api_questions_type."&page=1&type=question");
					}
				}else if (isset($value["value"]) && $value["value"] == $key) {
					if ($key == "answers" || $key == "answers-might-like" || $key == "answers-for-you" || $key == "answers-2" || $key == "answers-might-like-2" || $key == "answers-for-you-2") {
						$post_type = "answer";
					}else if ($key == "recent-posts" || $key == "posts-visited" || $key == "recent-posts-2" || $key == "posts-visited-2") {
						$post_type = "post";
					}else {
						$post_type = mobile_api_questions_type;
					}
					if ($key == "answers-might-like" || $key == "answers-for-you" || $key == "answers-might-like-2" || $key == "answers-for-you-2") {
						unset($home_tabs_options[$key]);
					}else {
						$tab_name = str_ireplace("-2","WithTime",$key);
						$tab_name = str_ireplace("-","",strtolower($tab_name));
						$value["sort"] = (isset($mobile_api_options["lang_tab".$tab_name])?$mobile_api_options["lang_tab".$tab_name]:"");
						if (isset($value["sort"]) && $value["sort"] != "") {
							if ($key == "answers" || $key == "answers-might-like" || $key == "answers-for-you" || $key == "answers-2" || $key == "answers-might-like-2" || $key == "answers-for-you-2") {
								$home_tabs[] = array(
									"key" => $key,
									"tabs_type" => "answer",
									"title" => $value["sort"],
									"url" => mobile_api_tabs_content."?get_tab=".str_ireplace("-","_","get_".$key)."&type=answer",
									"postLayout" => "PostLayout.answerPost"
								);
							}else if ($key == "comments" || $key == "comments-2") {
								$home_tabs[] = array(
									"key" => $key,
									"tabs_type" => "comment",
									"title" => $value["sort"],
									"url" => mobile_api_tabs_content."?get_tab=".str_ireplace("-","_","get_".$key)."&type=comment",
									"postLayout" => "PostLayout.commentPost"
								);
							}else if ($key == "recent-posts" || $key == "posts-visited" || $key == "recent-posts-2" || $key == "posts-visited-2") {
								$home_tabs[] = array(
									"key" => $key,
									"title" => $value["sort"],
									"url" => mobile_api_tabs_content."?get_tab=".str_ireplace("-","_","get_".$key),
									"postLayout" => "PostLayout.blogPost",
									"options" => $post_options
								);
							}else {
								$lang_tab = (isset($mobile_api_options[str_ireplace("-","","lang_tab".$key)])?$mobile_api_options[str_ireplace("-","","lang_tab".$key)]:"");
								$home_tabs[] = array("key" => $key,"title" => ($lang_tab != ""?$lang_tab:$value["sort"]),"url" => mobile_api_tabs_content."?get_tab=".str_ireplace("-","_","get_".$key));
							}
						}
					}
				}
			}
		}
	}

	$homepage_options = [
		"sort" => "latest",
		"count" => "{$count_posts_home}",
	];

	foreach ($array_options as $key => $value) {
		if (isset($mobile_setting_home[$key]) && $mobile_setting_home[$key] == $key) {
			if ($value == "questionRank") {
				$homepage_options[$value]["upvote"] = "true";
				if ($mobile_setting_dislike != mobile_api_checkbox_value) {
					$homepage_options[$value]["downvote"] = "true";
				}
			}else if ($value == "author") {
				$homepage_options["author"] = "true";
				$homepage_options["ownerRank"] = "true";
			}else {
				$homepage_options[$value] = "true";
			}
			if ($activate_male_female == true) {
				$homepage_options["genderAnswersCount"] = "true";
			}
			if ($show_ads == true && $banner_posts == "true") {
				$homepage_options["adsCount"] = $mobile_ad_posts_position;
			}
		}
	}

	if ($show_ads == true && $mobile_adv == mobile_api_checkbox_value && $ads_mobile_top == mobile_api_checkbox_value) {
		if ($mobile_ad_html_before_home == mobile_api_checkbox_value) {
			if ($mobile_ad_html_before_home_type == "display_code") {
				$content = $mobile_ad_html_before_home_code;
				if ($content != "") {
					$ad_before_home_html = true;
					$ad_before_home_array = [
						"postLayout" => "PostLayout.htmlAd",
						"content"    => $content
					];
				}
			}else if ($mobile_ad_html_before_home_img != "") {
				if ($mobile_ad_html_before_home_href != "") {
					$ad_before_home_html = true;
					$ad_before_home_array = [
						"postLayout" => "PostLayout.imageAd",
						"img" => $mobile_ad_html_before_home_img,
						"action" => "url",
						"target" => $mobile_ad_html_before_home_href,
					];
				}else {
					$ad_before_home_html = true;
					$ad_before_home_array = [
						"postLayout" => "PostLayout.htmlAd",
						"content"    => "<img src='".$mobile_ad_html_before_home_img."'>"
					];
				}
			}
		}else {
			$ad_before_home_array = [
				"postLayout" => "PostLayout.adMob",
				"adSize"     => "full_banner"
			];
		}
		if (isset($ad_before_home_array)) {
			$array["homePage"]["sections"][] = $ad_before_home_array;
		}
	}

	if (isset($home_advanced)) {
		$array["homePage"]["sections"][] = array("postLayout" => "PostLayout.tabs","loadMore" => "true");
		$array["homePage"]["homeLayout"] = "HomeLayout.advanced";
	}

	if (isset($home_tabs[0]["key"]) && ($home_tabs[0]["key"] == "recent-posts" || $home_tabs[0]["key"] == "posts-visited" || $home_tabs[0]["key"] == "recent-posts-2" || $home_tabs[0]["key"] == "posts-visited-2")) {
		$home_post_tab = true;
	}

	if (isset($home_advanced)) {
		$array["tabs"][] = [
			"loadMore"   => "true",
			"postLayout" => (isset($home_post_tab)?"PostLayout.blogPost":"PostLayout.questionPost"),
			"options"    => (isset($home_post_tab)?$post_options:$homepage_options),
			"url"        => (isset($home_tabs) && is_array($home_tabs) && !empty($home_tabs)?$home_tabs[0]['url']:(isset($home_post_tab)?mobile_api_posts."?post_type=post":mobile_api_posts."?post_type=".mobile_api_questions_type)),
		];
	}else {
		$array["homePage"]["sections"][] = [
			"loadMore"   => "true",
			"postLayout" => (isset($home_post_tab)?"PostLayout.blogPost":"PostLayout.questionPost"),
			"options"    => (isset($home_post_tab)?$post_options:$homepage_options),
			"url"        => (isset($home_tabs) && is_array($home_tabs) && !empty($home_tabs)?$home_tabs[0]['url']:(isset($home_post_tab)?mobile_api_posts."?post_type=post":mobile_api_posts."?post_type=".mobile_api_questions_type)),
		];
	}

	if (isset($home_tabs) && is_array($home_tabs) && !empty($home_tabs)) {
		$array["tabs"] = [
			"tabsLayout" => "TabsLayout.tab1",
			"homeTab"    => $home_tabs[0]['title'],
		];
		if (isset($home_tabs) && is_array($home_tabs) && !empty($home_tabs) && count($home_tabs) > 1) {
			$count_tabs = 0;
			foreach ($home_tabs as $key => $value) {
				$count_tabs++;
				if ((!isset($home_advanced) && $count_tabs > 1) || isset($home_advanced)) {
					if ($value["key"] == "answers" || $value["key"] == "answers-might-like" || $value["key"] == "answers-for-you" || $value["key"] == "answers-2" || $value["key"] == "answers-might-like-2" || $value["key"] == "answers-for-you-2" || $value["key"] == "comments" || $value["key"] == "comments-2") {
						$array["tabs"]["tabs"][] = [
							"key" => $value["key"],
							"tabs_type" => $value["tabs_type"],
							"title" => $value["title"],
							"url" => $value["url"],
							"postLayout" => $value["postLayout"]
						];
					}else if ($value["key"] == "recent-posts" || $value["key"] == "posts-visited" || $value["key"] == "recent-posts-2" || $value["key"] == "posts-visited-2") {
						$array["tabs"]["tabs"][] = [
							"title" => $value["title"],
							"url" => $value["url"],
							"postLayout" => $value["postLayout"],
							"options" => $value["options"]
						];
					}else {
						$array["tabs"]["tabs"][] = [
							"title" => $value["title"],
							"url" => $value["url"]
						];
					}
				}
			}
		}

		$array["tabs"]["postLayout"] = "PostLayout.questionPost";
		$array["tabs"]["options"] = [
			"count" => "{$count_posts_home}"
		];
		foreach ($array_options as $key => $value) {
			if (isset($mobile_setting_home[$key]) && $mobile_setting_home[$key] == $key) {
				if ($value == "questionRank") {
					$array["tabs"]["options"][$value]["upvote"] = "true";
					if ($mobile_setting_dislike != mobile_api_checkbox_value) {
						$array["tabs"]["options"][$value]["downvote"] = "true";
					}
				}else if ($value == "author") {
					$array["tabs"]["options"]["author"] = "true";
					$array["tabs"]["options"]["ownerRank"] = "true";
				}else {
					$array["tabs"]["options"][$value] = "true";
				}
			}
		}
	}else {
		$array["tabs"]["tabsLayout"] = "TabsLayout.tab1";
		$array["tabs"]["postLayout"] = "PostLayout.questionPost";
		$array["tabs"]["options"] = [
			"count" => "{$count_posts_home}",
			"sort" => "Sort.latest"
		];
		foreach ($array_options as $key => $value) {
			if (isset($mobile_setting_home[$key]) && $mobile_setting_home[$key] == $key) {
				if ($value == "questionRank") {
					$array["tabs"]["options"][$value]["upvote"] = "true";
					if ($mobile_setting_dislike != mobile_api_checkbox_value) {
						$array["tabs"]["options"][$value]["downvote"] = "true";
					}
				}else if ($value == "author") {
					$array["tabs"]["options"]["author"] = "true";
					$array["tabs"]["options"]["ownerRank"] = "true";
				}else {
					$array["tabs"]["options"][$value] = "true";
				}
			}
		}
	}

	if ($show_ads == true && $mobile_adv == mobile_api_checkbox_value && $ads_mobile_bottom == mobile_api_checkbox_value) {
		if ($mobile_ad_html_after_home == mobile_api_checkbox_value) {
			if ($mobile_ad_html_after_home_type == "display_code") {
				$content = $mobile_ad_html_after_home_code;
				if ($content != "") {
					$ad_after_home_html = true;
					$ad_after_home_array = [
						"postLayout" => "PostLayout.htmlAd",
						"content"    => $content
					];
				}
			}else if ($mobile_ad_html_after_home_img != "") {
				if ($mobile_ad_html_after_home_href != "") {
					$ad_after_home_html = true;
					$ad_after_home_array = [
						"postLayout" => "PostLayout.imageAd",
						"img" => $mobile_ad_html_after_home_img,
						"action" => "url",
						"target" => $mobile_ad_html_after_home_href,
					];
				}else {
					$ad_after_home_html = true;
					$ad_after_home_array = [
						"postLayout" => "PostLayout.htmlAd",
						"content"    => "<img src='".$mobile_ad_html_after_home_img."'>"
					];
				}
			}
		}else {
			$ad_after_home_array = [
				"postLayout" => "PostLayout.adMob",
				"adSize"     => "full_banner"
			];
		}
		if (isset($ad_after_home_array)) {
			$array["homePage"]["sections"][] = $ad_after_home_array;
		}
	}

	$array["blog"]["url"] = mobile_api_posts."?post_type=post";
	$array["blog"]["postLayout"] = "PostLayout.blogPost";
	$array["blog"]["blogPage"]["count"] = "{$count_posts_blog}";
	$array["blog"]["blogPage"]["sort"] = "Sort.latest";
	foreach ($array_options_posts as $key => $value) {
		if (isset($mobile_setting_blog[$key]) && $mobile_setting_blog[$key] == $key) {
			if ($value == "author") {
				$array["blog"]["blogPage"]["author"] = "true";
				$array["blog"]["blogPage"]["ownerRank"] = "true";
			}else {
				$array["blog"]["blogPage"][$value] = "true";
			}
		}
	}

	$array["blog"]["category"]["postLayout"] = "PostLayout.blogPost";
	$array["blog"]["category"]["options"]["count"] = "{$count_posts_categories}";
	$array["blog"]["category"]["options"]["sort"] = "Sort.latest";
	foreach ($array_options_posts as $key => $value) {
		if (isset($mobile_setting_categories_posts[$key]) && $mobile_setting_categories_posts[$key] == $key) {
			if ($value == "author") {
				$array["blog"]["category"]["options"]["author"] = "true";
				$array["blog"]["category"]["options"]["ownerRank"] = "true";
			}else {
				$array["blog"]["category"]["options"][$value] = "true";
			}
		}
	}

	foreach ($array_options_posts_single as $key => $value) {
		if (isset($mobile_setting_single_post[$key]) && $mobile_setting_single_post[$key] == $key) {
			if ($value == "author") {
				$array["blog"]["single"]["author"] = "true";
				$array["blog"]["single"]["ownerRank"] = "true";
			}else {
				$array["blog"]["single"][$value] = "true";
			}
		}
	}

	$array["blog"]["single"]["moreButton"] = "true";

	$array["blog"]["content"] = "true";

	if ($terms_active_comment == mobile_api_checkbox_value) {
		$array["blog"]["commentTerms"] = "true";
	}
	$array["archives"]["comments"] = [
		"postLayout" => "PostLayout.commentPost",
		"url" => mobile_api_comments,
		"options" => array(
			"count" => "{$count_comments_mobile}",
			"sort" => "Sort.latest",
		)
	];
	$array["archives"]["comments"]["options"]["date"] = "true";

	$array["archives"]["answers"] = [
		"postLayout" => "PostLayout.answerPost",
		"url" => mobile_api_answers,
		"options" => array(
			"count" => "{$count_comments_mobile}",
			"sort" => "Sort.latest",
		)
	];
	$array["archives"]["answers"]["options"]["date"] = "true";
	if ($vote_answer_mobile == mobile_api_checkbox_value) {
		$array["archives"]["answers"]["options"]["questionRank"]["upvote"] = "true";
		if ($mobile_setting_dislike != mobile_api_checkbox_value) {
			$array["archives"]["answers"]["options"]["questionRank"]["downvote"] = "true";
		}
	}

	$array["archives"]["categories"] = [
		"layout" => "CategoriesLayout.cat6",
		"url" => mobile_api_get_categories."?taxonomy=".mobile_api_question_categories,
		"blogsUrl" => mobile_api_get_categories."?taxonomy=category"
	];

	$array["archives"]["category"] = [
		"postLayout" => "PostLayout.questionPost",
		"options" => [
			"count" => "{$count_posts_categories}",
			"sort" => "Sort.latest",
		],
	];
	foreach ($array_options as $key => $value) {
		if (isset($mobile_setting_categories[$key]) && $mobile_setting_categories[$key] == $key) {
			if ($value == "questionRank") {
				$array["archives"]["category"]["options"][$value]["upvote"] = "true";
				if ($mobile_setting_dislike != mobile_api_checkbox_value) {
					$array["archives"]["category"]["options"][$value]["downvote"] = "true";
				}
			}else if ($value == "author") {
				$array["archives"]["category"]["options"]["author"] = "true";
				$array["archives"]["category"]["options"]["ownerRank"] = "true";
			}else {
				$array["archives"]["category"]["options"][$value] = "true";
			}
		}
	}

	$array["archives"]["questions"] = [
		"postLayout" => "PostLayout.questionPost",
		"url" => mobile_api_posts."?post_type=".mobile_api_questions_type."&type=page&page=1",
		"options" => [
			"count" => "{$count_posts_questions}",
			"sort" => "Sort.latest",
		],
	];
	foreach ($array_options as $key => $value) {
		if (isset($mobile_setting_questions[$key]) && $mobile_setting_questions[$key] == $key) {
			if ($value == "questionRank") {
				$array["archives"]["questions"]["options"][$value]["upvote"] = "true";
				if ($mobile_setting_dislike != mobile_api_checkbox_value) {
					$array["archives"]["questions"]["options"][$value]["downvote"] = "true";
				}
			}else if ($value == "author") {
				$array["archives"]["questions"]["options"]["author"] = "true";
				$array["archives"]["questions"]["options"]["ownerRank"] = "true";
			}else {
				$array["archives"]["questions"]["options"][$value] = "true";
			}
		}
	}

	$array["archives"]["search"] = [
		"postLayout" => "PostLayout.questionPost",
		"options" => [
			"count" => "{$count_posts_search}",
			"sort" => "Sort.latest",
		],
	];
	foreach ($array_options as $key => $value) {
		if (isset($mobile_setting_search[$key]) && $mobile_setting_search[$key] == $key) {
			if ($value == "questionRank") {
				$array["archives"]["search"]["options"][$value]["upvote"] = "true";
				if ($mobile_setting_dislike != mobile_api_checkbox_value) {
					$array["archives"]["search"]["options"][$value]["downvote"] = "true";
				}
			}else if ($value == "author") {
				$array["archives"]["search"]["options"]["author"] = "true";
				$array["archives"]["search"]["options"]["ownerRank"] = "true";
			}else {
				$array["archives"]["search"]["options"][$value] = "true";
			}
		}
	}

	$array["archives"]["favorites"] = [
		"postLayout" => "PostLayout.questionPost",
		"url" => mobile_api_favorites,
		"options" => [
			"count" => "{$count_posts_favourites}",
			"sort" => "Sort.latest",
		],
	];
	foreach ($array_options as $key => $value) {
		if (isset($mobile_setting_favourites[$key]) && $mobile_setting_favourites[$key] == $key) {
			if ($value == "questionRank") {
				$array["archives"]["favorites"]["options"][$value]["upvote"] = "true";
				if ($mobile_setting_dislike != mobile_api_checkbox_value) {
					$array["archives"]["favorites"]["options"][$value]["downvote"] = "true";
				}
			}else if ($value == "author") {
				$array["archives"]["favorites"]["options"]["author"] = "true";
				$array["archives"]["favorites"]["options"]["ownerRank"] = "true";
			}else {
				$array["archives"]["favorites"]["options"][$value] = "true";
			}
		}
	}

	$array["archives"]["followed"] = [
		"postLayout" => "PostLayout.questionPost",
		"url" => mobile_api_followed."?count=".$count_posts_followed,
		"options" => [
			"count" => "{$count_posts_followed}",
			"sort" => "Sort.latest",
		],
	];
	foreach ($array_options as $key => $value) {
		if (isset($mobile_setting_followed[$key]) && $mobile_setting_followed[$key] == $key) {
			if ($value == "questionRank") {
				$array["archives"]["followed"]["options"][$value]["upvote"] = "true";
				if ($mobile_setting_dislike != mobile_api_checkbox_value) {
					$array["archives"]["followed"]["options"][$value]["downvote"] = "true";
				}
			}else if ($value == "author") {
				$array["archives"]["followed"]["options"]["author"] = "true";
				$array["archives"]["followed"]["options"]["ownerRank"] = "true";
			}else {
				$array["archives"]["followed"]["options"][$value] = "true";
			}
		}
	}

	foreach ($array_options_single as $key => $value) {
		if (isset($mobile_setting_single[$key]) && $mobile_setting_single[$key] == $key) {
			if ($value == "author") {
				$array["archives"]["single"]["author"] = "true";
				$array["archives"]["single"]["ownerRank"] = "true";
			}else {
				$array["archives"]["single"][$value] = "true";
			}
		}
	}
	
	if ($menu_style_of_report == "menu") {
		$array["archives"]["single"]["moreButton"] = "true";
	}

	if ($bottom_bar_activate == mobile_api_checkbox_value || $side_navbar_activate == mobile_api_checkbox_value) {
		$main_pages = array(
			"home"            => array("type" => "home","name" => esc_html__('Home','mobile-api')),
			"ask"             => array("type" => "addQuestion","name" => esc_html__('Ask Question','mobile-api')),
			"categories"      => array("type" => "sections","name" => esc_html__('Categories','mobile-api')),
			"favorite"        => array("type" => "favourites","name" => esc_html__('Favorite','mobile-api')),
			"followed"        => array("type" => "followed","name" => esc_html__('Followed Questions','mobile-api')),
			"settings"        => array("type" => "settings","name" => esc_html__('Settings','mobile-api')),
			"questions"       => array("type" => "questions","name" => esc_html__('Questions','mobile-api')),
			"blog"            => array("type" => "blogs","name" => esc_html__('Blog','mobile-api')),
			"users"           => array("type" => "users","name" => esc_html__('Users','mobile-api')),
			"post_categories" => array("type" => "blogSections","name" => esc_html__('Post Categories','mobile-api')),
			"search"          => array("type" => "search","name" => esc_html__('Search','mobile-api')),
			"contact_us"      => array("type" => "contactUs","name" => esc_html__('Contact Us','mobile-api')),
			"post"            => array("type" => "addBlog","name" => esc_html__('Add Post','mobile-api')),
			"points"          => array("type" => "badges","name" => esc_html__('Badges and points','mobile-api')),
			"answers"         => array("type" => "answers","name" => esc_html__('Answers','mobile-api')),
			"comments"        => array("type" => "comments","name" => esc_html__('Comments','mobile-api')),
			"notifications"   => array("type" => "notifications","name" => esc_html__('Notifications','mobile-api')),
		);
		$main_pages = apply_filters("mobile_api_main_pages",$main_pages);
	}

	if ($bottom_bar_activate == mobile_api_checkbox_value) {
		if (is_array($add_bottom_bars) && !empty($add_bottom_bars)) {
			$bottom_bar_count = 0;
			foreach ($add_bottom_bars as $key => $value) {
				if ((isset($value['type']) && $value['type'] == "main") || (isset($value['type']) && $value['type'] == "q_category") || (isset($value['type']) && $value['type'] == "p_category") || (isset($value['type']) && $value['type'] == "page") || (isset($value['type']) && $value['type'] == "webview")) {
					$bottom_bar_count++;
					if ($bottom_bar_count < 5) {
						if ($value['type'] == "main") {
							$get_key = $main_pages[$value['main']];
							if ($get_key["type"] == "feed") {
								$array["bottomBar"]["navigators"][] = [
									"title" => (isset($value['title']) && $value['title'] != ""?$value['title']:$get_key["name"]),
									"icon" => (isset($value['icon']) && $value['icon'] != ""?$value['icon']:"0xe826"),
									"type" => "NavigationType.feed",
									"bottom_bar_icon_enable" => "true",
									"title_enable" => "true",
									"api" => mobile_api_posts."?post_type=".mobile_api_questions_type."&feed=".$value["feed"],
									"page_id" => $value["feed"],
								];
							}else {
								$array["bottomBar"]["navigators"][] = [
									"title" => (isset($value['title']) && $value['title'] != ""?$value['title']:$get_key["name"]),
									"icon" => (isset($value['icon']) && $value['icon'] != ""?$value['icon']:"0xe826"),
									"type" => "NavigationType.main",
									"main" => "MainPage.".$get_key["type"],
									"bottom_bar_icon_enable" => "true",
									"title_enable" => "true"
								];
							}
						}else if ($value['type'] == "q_category" || $value['type'] == "p_category") {
							$term_id = (int)$value[$value['type']];
							$get_term = get_term($term_id);
							if (isset($get_term->term_id) && $get_term->term_id > 0) {
								$array["bottomBar"]["navigators"][] = [
									"title" => (isset($value['title']) && $value['title'] != ""?$value['title']:$get_term->name),
									"type" => ($get_term->taxonomy == mobile_api_question_categories?"NavigationType.category":"NavigationType.blogCategory"),
									"icon" => (isset($value['icon']) && $value['icon'] != ""?$value['icon']:($get_term->taxonomy == mobile_api_question_categories?"0xe819":"0xedcb")),
									"url" => mobile_api_posts_category."?id=".$get_term->term_id."&taxonomy=".$get_term->taxonomy,
									"bottom_bar_icon_enable" => "true",
									"title_enable" => "true"
								];
							}
						}else if ($value['type'] == "page") {
							$page_id = (int)$value['page'];
							$get_permalink = get_permalink($page_id);
							if ($get_permalink != "") {
								$array["bottomBar"]["navigators"][] = [
									"title" => (isset($value['title']) && $value['title'] != ""?$value['title']:get_the_title($page_id)),
									"type" => "NavigationType.page",
									"icon" => (isset($value['icon']) && $value['icon'] != ""?$value['icon']:"0xe81c"),
									"url" => mobile_api_get_post."/?id=".$page_id."&post_type=page",
									"bottom_bar_icon_enable" => "true",
									"title_enable" => "true"
								];
							}
						}else if ($value['type'] == "webview") {
							$link = esc_url($value['link']);
							$page_id = (int)$value['webview'];
							if ($link == "") {
								$get_permalink = get_permalink($page_id);
							}
							if ($link != "" || (isset($get_permalink) && $get_permalink != "")) {
								$array["bottomBar"]["navigators"][] = [
									"title" => (isset($value['title']) && $value['title'] != ""?$value['title']:($page_id > 0?get_the_title($page_id):"")),
									"type" => "NavigationType.webview",
									"icon" => (isset($value['icon']) && $value['icon'] != ""?$value['icon']:"0xe81c"),
									"url" => ($link != ""?$link:$get_permalink),
									"bottom_bar_icon_enable" => "true",
									"title_enable" => "true"
								];
							}
						}
					}
				}
			}
		}else {
			$array["bottomBar"]["navigators"][] = [
				"title" => "Home",
				"icon" => "0xe800",
				"type" => "NavigationType.main",
				"main" => "MainPage.home",
				"bottom_bar_icon_enable" => "true",
				"title_enable" => "true"
			];
		}
	}

	if ($side_navbar_activate == mobile_api_checkbox_value) {
		$array["sideNavbar"] = [
			"icon" => (is_rtl()?"0xe90d":"0xe808"),
			//"bold" => "true"
		];
		if (is_array($add_sidenavs) && !empty($add_sidenavs)) {
			foreach ($add_sidenavs as $key => $value) {
				if ((isset($value['type']) && $value['type'] == "main") || (isset($value['type']) && $value['type'] == "q_category") || (isset($value['type']) && $value['type'] == "p_category") || (isset($value['type']) && $value['type'] == "page") || (isset($value['type']) && $value['type'] == "webview")) {
					if ($value['type'] == "main") {
						$get_key = $main_pages[$value['main']];
						if ($get_key["type"] == "feed") {
							$array["sideNavbar"]["navigators"][] = [
								"title" => (isset($value['title']) && $value['title'] != ""?$value['title']:$get_key["name"]),
								"icon" => (isset($value['icon']) && $value['icon'] != ""?$value['icon']:"0xe826"),
								"type" => "NavigationType.feed",
								"side_menu_tab_icon" => "true",
								"api" => mobile_api_posts."?post_type=".mobile_api_questions_type."&feed=".$value["feed"],
								"page_id" => $value["feed"],
							];
						}else {
							$array["sideNavbar"]["navigators"][] = [
								"title" => (isset($value['title']) && $value['title'] != ""?$value['title']:$get_key["name"]),
								"icon" => (isset($value['icon']) && $value['icon'] != ""?$value['icon']:"0xe826"),
								"type" => "NavigationType.main",
								"main" => "MainPage.".$get_key["type"],
								"side_menu_tab_icon" => "true"
							];
						}
					}else if ($value['type'] == "q_category" || $value['type'] == "p_category") {
						$term_id = (int)$value[$value['type']];
						$get_term = get_term($term_id);
						if (isset($get_term->term_id) && $get_term->term_id > 0) {
							$array["sideNavbar"]["navigators"][] = [
								"title" => (isset($value['title']) && $value['title'] != ""?$value['title']:$get_term->name),
								"type" => ($get_term->taxonomy == mobile_api_question_categories?"NavigationType.category":"NavigationType.blogCategory"),
								"icon" => (isset($value['icon']) && $value['icon'] != ""?$value['icon']:($get_term->taxonomy == mobile_api_question_categories?"0xe819":"0xedcb")),
								"url" => mobile_api_posts_category."?id=".$get_term->term_id."&taxonomy=".$get_term->taxonomy,
								"side_menu_tab_icon" => "true"
							];
						}
					}else if ($value['type'] == "page") {
						$page_id = (int)$value['page'];
						$get_permalink = get_permalink($page_id);
						if ($get_permalink != "") {
							$array["sideNavbar"]["navigators"][] = [
								"title" => (isset($value['title']) && $value['title'] != ""?$value['title']:get_the_title($page_id)),
								"type" => "NavigationType.page",
								"icon" => (isset($value['icon']) && $value['icon'] != ""?$value['icon']:"0xe81c"),
								"url" => mobile_api_get_post."/?id=".$page_id."&post_type=page",
								"side_menu_tab_icon" => "true"
							];
						}
					}else if ($value['type'] == "webview") {
						$link = esc_url($value['link']);
						$page_id = (int)$value['webview'];
						if ($link == "") {
							$get_permalink = get_permalink($page_id);
						}
						if ($link != "" || (isset($get_permalink) && $get_permalink != "")) {
							$array["sideNavbar"]["navigators"][] = [
								"title" => (isset($value['title']) && $value['title'] != ""?$value['title']:($page_id > 0?get_the_title($page_id):"")),
								"type" => "NavigationType.webview",
								"icon" => (isset($value['icon']) && $value['icon'] != ""?$value['icon']:"0xe81c"),
								"url" => ($link != ""?$link:$get_permalink),
								"side_menu_tab_icon" => "true"
							];
						}
					}
				}
			}
		}else {
			$array["sideNavbar"]["navigators"][] = [
				"title" => "Home",
				"icon" => "0xe800",
				"type" => "NavigationType.main",
				"main" => "MainPage.home",
				"side_menu_tab_icon" => "true"
			];
		}
	}

	$array["appBar"] = [
		"layout" => "AppBarLayout.header2", // or header8 
		"position" => "LogoPosition.".$logo_position,
		"searchIcon" => (is_rtl()?"0xe821":"0xe820"),
	];
	if ($activate_dark_from_header == mobile_api_checkbox_value) {
		$array["appBar"]["darkMode"]["light"] = "0xe918";
		$array["appBar"]["darkMode"]["dark"] = "0xe918";
	}
	//$array["appBar"]["notificationsIcon"] = "fa-bell";
	//$array["appBar"]["loginIcon"] = "0xe81f";
	//$array["appBar"]["profileIcon"] = "true";
	//$array["appBar"]["searchBar"] = "true"; // with header8 only

	$array["logo"] = [
		"light" => $mobile_logo,
		"dark" => ($mobile_logo_dark != ""?$mobile_logo_dark:$mobile_logo)
	];

	$array["styling"]["ThemeMode.light"] = [
		"dividerLayout" => "DividerLayout.thin",
		"errorColor" => $errorcolor,
		"errorTextColor" => $errortextcolor,
		"alertColor" => $alertcolor,
		"alertTextColor" => $alerttextcolor,
		"alertLinkColor" => $alerttextcolor,
		"successColor" => $successcolor,
		"successTextColor" => $successtextcolor,
		"tooltipMenuColor" => $tooltipmenucolor,
		"highLightColor" => $highlightcolor,
		"highLightTextColor" => $highlighttextcolor,
		"closeQuestionButtonColor" => $closequestionbuttoncolor,
		"closeQuestionBackgroundColor" => $closequestionbackgroundcolor,
		"openQuestionButtonColor" => $openquestionbuttoncolor,
		"openQuestionBackgroundColor" => $openquestionbackgroundcolor,
		"favouriteColor" => $favouritecolor,
		"unFavouriteColor" => $unfavouritecolor,
		"bestAnswerColor" => $bestanswercolor,
		"removeBestAnswerColor" => $removebestanswercolor,
		"addBestAnswerColor" => $addbestanswercolor,
		"appBarBackgroundColor" => $appbarbackgroundcolor,
		"tabBarBackgroundColor" => $tabbarbackgroundcolor,
		"bottomBarBackgroundColor" => $bottombarbackgroundcolor,
		"appBarColor" => $appbarcolor,
		"tabBarActiveTextColor" => $tabbaractivetextcolor,
		"tabBarIndicatorColor" => $tabbarindicatorcolor,
		"tabBarTextColor" => $tabbartextcolor,
		"checkboxActiveColor" => $checkboxactivecolor,
		"bottomBarActiveColor" => $bottombaractivecolor,
		"bottomBarInActiveColor" => $bottombarinactivecolor,
		"primary" => $mobile_primary,
		"secondary" => $mobile_secondary,
		"secondaryVariant" => $secondaryvariant,
		"background" => $mobile_background,
		"sidemenutextcolor" => $sidemenutextcolor,
		"scaffoldBackgroundColor" => $scaffoldbackgroundcolor,
		"buttonTextColor" => $buttontextcolor,
		"dividerColor" => $dividercolor,
		"shadowColor" => $shadowcolor,
		"buttonsbackgroudcolor" => $buttonsbackgroudcolor,
		"settingBackgroundColor" => $settingbackgroundcolor,
		"settingTextColor" => $settingtextcolor,
		"verifiedColor" => $verifiedcolor,
		"inputsbackgroundcolor" => ($activate_input_border_bottom == mobile_api_checkbox_value?$input_border_bottom_color:$inputsbackgroundcolor),
		"facebook" => "1877f2",
		"tiktok" => "000000",
		"instagram" => "c32aa3",
		"pinterest" => "bd081c",
		"twitter" => "1da1f2",
		"youtube" => "ff0000",
		"vimeo" => "1ab7ea",
		"linkedin" => "0a66c2"
	];
	$array["styling"]["ThemeMode.dark"] = [
		"dividerLayout" => "DividerLayout.thin",
		"errorColor" => $errorcolor_dark,
		"errorTextColor" => $errortextcolor_dark,
		"alertColor" => $alertcolor_dark,
		"alertTextColor" => $alerttextcolor_dark,
		"alertLinkColor" => $alerttextcolor_dark,
		"successColor" => $successcolor_dark,
		"successTextColor" => $successtextcolor_dark,
		"tooltipMenuColor" => $tooltipmenucolor_dark,
		"highLightColor" => $highlightcolor_dark,
		"highLightTextColor" => $highlighttextcolor_dark,
		"closeQuestionButtonColor" => $closequestionbuttoncolor_dark,
		"closeQuestionBackgroundColor" => $closequestionbackgroundcolor_dark,
		"openQuestionButtonColor" => $openquestionbuttoncolor_dark,
		"openQuestionBackgroundColor" => $openquestionbackgroundcolor_dark,
		"favouriteColor" => $favouritecolor_dark,
		"unFavouriteColor" => $unfavouritecolor_dark,
		"bestAnswerColor" => $bestanswercolor_dark,
		"removeBestAnswerColor" => $removebestanswercolor_dark,
		"addBestAnswerColor" => $addbestanswercolor_dark,
		"appBarBackgroundColor" => $appbarbackgroundcolor_dark,
		"tabBarBackgroundColor" => $tabbarbackgroundcolor_dark,
		"bottomBarBackgroundColor" => $bottombarbackgroundcolor_dark,
		"appBarColor" => $appbarcolor_dark,
		"tabBarActiveTextColor" => $tabbaractivetextcolor_dark,
		"tabBarIndicatorColor" => $tabbarindicatorcolor_dark,
		"tabBarTextColor" => $tabbartextcolor_dark,
		"checkboxActiveColor" => $checkboxactivecolor_dark,
		"bottomBarActiveColor" => $bottombaractivecolor_dark,
		"bottomBarInActiveColor" => $bottombarinactivecolor_dark,
		"primary" => $mobile_primary_dark,
		"secondary" => $mobile_secondary_dark,
		"secondaryVariant" => $secondaryvariant_dark,
		"background" => $mobile_background_dark,
		"sidemenutextcolor" => $sidemenutextcolor_dark,
		"scaffoldBackgroundColor" => $scaffoldbackgroundcolor_dark,
		"buttonTextColor" => $buttontextcolor_dark,
		"dividerColor" => $dividercolor_dark,
		"shadowColor" => $shadowcolor_dark,
		"buttonsbackgroudcolor" => $buttonsbackgroudcolor_dark,
		"settingBackgroundColor" => $settingbackgroundcolor_dark,
		"settingTextColor" => $settingtextcolor_dark,
		"verifiedColor" => $verifiedcolor_dark,
		"inputsbackgroundcolor" => ($activate_input_border_bottom == mobile_api_checkbox_value?$input_border_bottom_color_dark:$inputsbackgroundcolor_dark),
		"facebook" => "1877f2",
		"tiktok" => "000000",
		"instagram" => "c32aa3",
		"pinterest" => "bd081c",
		"twitter" => "1da1f2",
		"youtube" => "ff0000",
		"vimeo" => "1ab7ea",
		"linkedin" => "0a66c2"
	];

	$array["typography"]["headline1"] = [
		"fontFamily" => "Open Sans",
		"fontSize" => "26",
		"lineHeight" => "1.30",
		"fontWeight" => "FontWeight.w300"
	];
	$array["typography"]["headline2"] = [
		"fontFamily" => "Open Sans",
		"fontSize" => "17",
		"lineHeight" => "1.444",
		"fontWeight" => "FontWeight.w600"
	];
	$array["typography"]["headline3"] = [
		"fontFamily" => "Open Sans",
		"fontSize" => "14",
		"lineHeight" => "1.30",
		"fontWeight" => "FontWeight.w600"
	];
	$array["typography"]["headline4"] = [
		"fontFamily" => "Open Sans",
		"fontSize" => "14",
		"lineHeight" => "1.333",
		"fontWeight" => "FontWeight.w400"
	];
	$array["typography"]["headline5"] = [
		"fontFamily" => "Open Sans",
		"fontSize" => "20",
		"lineHeight" => "1.30",
		"fontWeight" => "FontWeight.w800"
	];
	$array["typography"]["subtitle1"] = [
		"fontFamily" => "Open Sans",
		"fontSize" => "12",
		"lineHeight" => "1.30",
		"fontWeight" => "FontWeight.w500"
	];
	$array["typography"]["subtitle2"] = [
		"fontFamily" => "Open Sans",
		"fontSize" => "11",
		"lineHeight" => "1.30",
		"fontWeight" => "FontWeight.w400"
	];
	$array["typography"]["bodyText1"] = [
		"fontFamily" => "Open Sans",
		"fontSize" => "16",
		"lineHeight" => "1.30",
		"fontWeight" => "FontWeight.w500"
	];
	$array["typography"]["bodyText2"] = [
		"fontFamily" => "Open Sans",
		"fontSize" => "14",
		"lineHeight" => "1.30",
		"fontWeight" => "FontWeight.w400"
	];

	if ($show_ads == true && $mobile_adv == mobile_api_checkbox_value) {
		if ($ad_mob_android != "") {
			$array["adMob"]["androidAppId"] = $ad_mob_android;
			$array["meta"]["ads"]["google"]["id"] = $ad_mob_android;
		}
		if ($ad_mob_ios != "") {
			$array["adMob"]["iosAppId"] = $ad_mob_ios;
			$array["meta"]["ads"]["ios"]["id"] = $ad_mob_ios;
			if ($funding_choices == mobile_api_checkbox_value) {
				$array["funding_choices"] = "true";
			}
		}

		if ($mobile_banner_adv == mobile_api_checkbox_value) {
			$array_ads = array(
				"top" => array("key" => "top","name" => "top"),
				"bottom" => array("key" => "bottom","name" => "bottom"),
				"before_home" => array("key" => "before_home","name" => "beforeHome"),
				"post_top" => array("key" => "post_top","name" => "topPost"),
				"post_bottom" => array("key" => "post_bottom","name" => "bottomPost"),
				"before_comments" => array("key" => "before_comments","name" => "beforeComments"),
				"after_post" => array("key" => "after_post","name" => "afterPost"),
				"posts" => array("key" => "banner_posts","name" => "posts","position" => true),
				"comments" => array("key" => "banner_comments","name" => "comments","position" => true),
				"banner_webview" => array("key" => "banner_webview","name" => "webView"),
			);

			if (is_array($array_ads) && !empty($array_ads)) {
				foreach ($array_ads as $key => $value) {
					$banner_type = (isset($mobile_ads[$value["key"]]) && $mobile_ads[$value["key"]] == $value["key"]?"true":"");
					$mobile_ad_html = (isset($mobile_api_options["mobile_ad_html_".$key])?$mobile_api_options["mobile_ad_html_".$key]:"");
					$mobile_ad_html_type = (isset($mobile_api_options["mobile_ad_html_".$key."_type"])?$mobile_api_options["mobile_ad_html_".$key."_type"]:"");
					$mobile_ad_html_img = (isset($mobile_api_options["mobile_ad_html_".$key."_img"])?mobile_api_image_url_id($mobile_api_options["mobile_ad_html_".$key."_img"]):"");
					$mobile_ad_html_href = (isset($mobile_api_options["mobile_ad_html_".$key."_href"])?$mobile_api_options["mobile_ad_html_".$key."_href"]:"");
					$mobile_ad_html_code = (isset($mobile_api_options["mobile_ad_html_".$key."_code"])?$mobile_api_options["mobile_ad_html_".$key."_code"]:"");

					if ($banner_type == "true") {
						$ad_array = array();
						$ad_html = false;
						$ad_type = "banner";
						if ($mobile_ad_html == mobile_api_checkbox_value) {
							$ad_type = "html";
							if ($mobile_ad_html_type == "display_code") {
								$content = $mobile_ad_html_code;
								if ($content != "") {
									$ad_html = true;
									$ad_array = ["content" => $content];
								}
							}else if ($mobile_ad_html_img != "") {
								if ($mobile_ad_html_href != "") {
									$ad_html = true;
									$ad_type = "image";
									$ad_array = [
										"postLayout" => "PostLayout.imageAd",
										"img" => $mobile_ad_html_img,
										"action" => "url",
										"target" => $mobile_ad_html_href,
									];
								}else {
									$ad_html = true;
									$ad_array = ["content" => "<img src='".$mobile_ad_html_img."'>"];
								}
							}
						}
						if (is_array($ad_array) && !empty($ad_array)) {
							if ($key == "posts") {
								$array["adMob"][$ad_type]["positions"]["questions"] = $ad_array;
								$array["adMob"][$ad_type]["positions"]["blogs"] = $ad_array;
							}else if ($key == "comments") {
								$array["adMob"][$ad_type]["positions"]["answers"] = $ad_array;
								$array["adMob"][$ad_type]["positions"]["comments"] = $ad_array;
							}else if (isset($value["name"])) {
								$array["adMob"][$ad_type]["positions"][$value["name"]] = $ad_array;
							}
						}
					}

					if (isset($value["name"]) && $banner_type == "true" && $ad_html != true) {
						$array["adMob"]["banner"]["positions"][$value["name"]] = "true";
						if ($key == "posts") {
							$array["adMob"]["banner"]["positions"]["questions"] = "true";
							$array["adMob"]["banner"]["positions"]["posts"] = "true";
							$array["adMob"]["banner"]["positions"]["blogs"] = "true";
						}
						if ($key == "comments") {
							$array["adMob"]["banner"]["positions"]["answers"] = "true";
							$array["adMob"]["banner"]["positions"]["comments"] = "true";
						}
					}
				}
			}

			if ($ad_banner_android != "") {
				$array["adMob"]["banner"]["androidBannerId"] = $ad_banner_android;
			}
			if ($ad_banner_ios != "") {
				$array["adMob"]["banner"]["iosBannerId"] = $ad_banner_ios;
			}
			if ($banner_posts == "true") {
				$array["tabs"]["options"]["adsCount"] = $mobile_ad_posts_position;
				$array["blog"]["category"]["options"]["adsCount"] = $mobile_ad_posts_position;
				$array["archives"]["category"]["options"]["adsCount"] = $mobile_ad_posts_position;
				$array["archives"]["questions"]["options"]["adsCount"] = $mobile_ad_posts_position;
				$array["archives"]["search"]["options"]["adsCount"] = $mobile_ad_posts_position;
				$array["archives"]["favorites"]["options"]["adsCount"] = $mobile_ad_posts_position;
				$array["archives"]["followed"]["options"]["adsCount"] = $mobile_ad_posts_position;
			}

			if ($banner_comments == "true") {
				$array["archives"]["comments"]["options"]["adsCount"] = $mobile_ad_comments_position;
				$array["archives"]["answers"]["options"]["adsCount"] = $mobile_ad_comments_position;
				$array["blog"]["single"]["options"]["adsCount"] = $mobile_ad_comments_position;
				$array["archives"]["single"]["options"]["adsCount"] = $mobile_ad_comments_position;
			}
		}

		if ($mobile_interstitial_adv == mobile_api_checkbox_value) {
			$array["adMob"]["interstatial"]["positions"]["beforePost"] = "true";
			$array["adMob"]["interstatial"]["count"] = "$ad_interstitial_count";
			$array["adMob"]["interstitialAndroidId"] = $ad_interstitial_android;
			$array["adMob"]["interstitialIosId"] = $ad_interstitial_ios;
			$array["archives"]["single"]["ads"]["interstitial"]["afterCount"] = "$ad_interstitial_count"; // 0 each time will open the ads, 2 will open the ads after 2 times from open the question
			$array["blog"]["single"]["ads"]["interstitial"]["afterCount"] = "$ad_interstitial_count"; // 0 each time will open the ads, 2 will open the ads after 2 times from open the post
		}
		if ($mobile_rewarded_adv == mobile_api_checkbox_value) {
			$array["adMob"]["rewardedAndroidId"] = $ad_rewarded_android;
			$array["adMob"]["rewardedIosId"] = $ad_rewarded_ios;
			$array["archives"]["single"]["ads"]["rewarded"]["afterCount"] = "$ad_rewarded_count"; // 0 each time will open the ads, 2 will open the ads after 2 times from open the question
			$array["blog"]["single"]["ads"]["rewarded"]["afterCount"] = "$ad_rewarded_count"; // 0 each time will open the ads, 2 will open the ads after 2 times from open the post
		}
	}

	if ($activate_switch_mode == mobile_api_checkbox_value) {
		$array["settingsPage"]["darkMode"] = "true";
	}
	if ($activate_stop_notification == mobile_api_checkbox_value) {
		$array["settingsPage"]["pushNotifications"] = "true";
		$array["default"]["pushNotifications"] = "true";
	}
	if ($text_size_app == mobile_api_checkbox_value) {
		$array["settingsPage"]["textSize"] = "true";
	}
	if ($rate_app == mobile_api_checkbox_value) {
		$array["settingsPage"]["rateApp"] = "true";
	}
	if ($edit_profile_app == mobile_api_checkbox_value) {
		$array["settingsPage"]["editProfile"] = "true";
	}
	if ($delete_account == mobile_api_checkbox_value) {
		if (isset($user_group) && is_array($delete_account_groups) && in_array($user_group,$delete_account_groups)) {
			$array["settingsPage"]["deleteAccount"] = "true";
			if ($delete_account_location == "edit_profile") {
				$array["deleteAccountLocation"] = "editProfilePage";
			}
		}
	}
	if ($notifications_page_app == mobile_api_checkbox_value) {
		$array["settingsPage"]["notifications"] = "true";
	}
	$show_rtl_settings = apply_filters("mobile_api_show_rtl_settings",false);
	if ($show_rtl_settings == true) {
		$array["settingsPage"]["rtl"] = "true";
	}
	if ($about_us_app == mobile_api_checkbox_value) {
		if ($about_us_page_app != "" && $about_us_page_app > 0) {
			$get_permalink = get_permalink($about_us_page_app);
			if ($get_permalink != "") {
				$array["settingsPage"]["aboutUs"] = mobile_api_get_post."/?id=".$about_us_page_app."&post_type=page";
			}
		}
	}
    if ($privacy_policy_page_app != "" && $privacy_policy_page_app > 0) {
		$get_permalink = get_permalink($privacy_policy_page_app);
		if ($get_permalink != "") {
			$array["settingsPage"]["privacyPolicy"] = mobile_api_get_post."/?id=".$privacy_policy_page_app."&post_type=page";
		}
	}
	if ($terms_page_app != "" && $terms_page_app > 0) {
		$get_permalink = get_permalink($terms_page_app);
		if ($get_permalink != "") {
			$array["settingsPage"]["termsAndConditions"] = mobile_api_get_post."/?id=".$terms_page_app."&post_type=page";
		}
	}
	if ($faqs_app == mobile_api_checkbox_value) {
		if ($faqs_page_app != "" && $faqs_page_app > 0) {
			$get_permalink = get_permalink($faqs_page_app);
			if ($get_permalink != "") {
				$array["settingsPage"]["faq"] = mobile_api_faqs."?id=".$faqs_page_app;
			}
		}
	}
	if ($contact_us_app == mobile_api_checkbox_value) {
		$array["settingsPage"]["contactUs"] = mobile_api_contact;
	}
	if ($share_app == mobile_api_checkbox_value && $share_title != "" && $share_image != "") {
		$array["settingsPage"]["shareApp"] = [
			"title" => $share_title,
			"image" => $share_image,
		];
		if ($share_android != "") {
			$array["settingsPage"]["shareApp"]["android"] = $share_android;
		}
		if ($share_ios != "") {
			$array["settingsPage"]["shareApp"]["ios"] = $share_ios;
		}
	}

	if ($follow_category == mobile_api_checkbox_value && !has_askme()) {
		$array["settingsPage"]["following"]["categories"] = "true";
		$array["settingsPage"]["following"]["tags"] = "true";
	}
	$array["settingsPage"]["following"]["people"] = "true";
	
	$array["defaultLayout"] = "Layout.standard";
	$array["searchApi"] = mobile_api_search."?find=";
	$array["commentsApi"] = mobile_api_post_comments;
	$array["commentAdd"] = mobile_api_submit_comment."/";
	$thread_comments = get_option("thread_comments");
	if ($thread_comments == true) {
		$thread_comments_depth = get_option("thread_comments_depth");
		$array["commentReply"] = "true";
		$array["commentReplyDepth"] = $thread_comments_depth;
	}
	$array["relatedPostsApi"] = mobile_api_posts."?post_type=".mobile_api_questions_type."&count=6";
	$array["lang"] = ($app_lang != ""?$app_lang:"en");

	$array["loginModule"]["status"] = ($activate_login != "disabled"?"true":"false");
	$array["loginModule"]["type"] = "LoginType.".($site_users_only == mobile_api_checkbox_value || $mobile_logged_only == mobile_api_checkbox_value || $site_users_only == "required"?"required":"optional");
	$array["loginModule"]["backgroundColor"] = ($loginbackground != ""?$loginbackground:"ffffff");
	$array["loginModule"]["backgroundColorDark"] = ($loginbackground_dark != ""?$loginbackground_dark:"1a1a1a");
	$array["loginModule"]["profileIcons"]["followers"] = "true";
	$array["loginModule"]["profileIcons"]["questions"] = "true";
	if ($active_notifications == mobile_api_checkbox_value) {
		$array["loginModule"]["profileIcons"]["notifications"] = "true";
	}

	$array_register_module = array();
	$array_register = array("nickname" => "nickname","first_name" => "first_name","last_name" => "last_name","display_name" => "display_name","image_profile" => "avatar","cover" => "cover","country" => "country","city" => "city","phone" => "phone","gender" => "gender","age" => "age");
	if (is_array($register_items) && !empty($register_items)) {
		foreach ($register_items as $key => $value) {
			if (isset($array_register[$key]) && $value["value"] == $key) {
				$array_register_module[$array_register[$key]] = "true";
				if ($key == "image_profile") {
					$required_register = (isset($mobile_api_options["profile_picture_required_register"])?$mobile_api_options["profile_picture_required_register"]:"");
					if ($required_register == mobile_api_checkbox_value) {
						$array_register_module[$array_register[$key]."_required"] = "true";
					}
				}else if ($key == "cover") {
					$required_register = (isset($mobile_api_options["profile_cover_required_register"])?$mobile_api_options["profile_cover_required_register"]:"");
					if ($required_register == mobile_api_checkbox_value) {
						$array_register_module[$array_register[$key]."_required"] = "true";
					}
				}else if ($key != "nickname") {
					$required_register = (isset($mobile_api_options[$key."_required_register"])?$mobile_api_options[$key."_required_register"]:"");
					if ($required_register == mobile_api_checkbox_value) {
						$array_register_module[$array_register[$key]."_required"] = "true";
					}
				}
			}
		}
	}
	if ($terms_active_register == mobile_api_checkbox_value) {
		$array_register_module["agree_terms"] = "true";
	}
	if ($gender_other == mobile_api_checkbox_value) {
		$array_register_module["showUnspecifiedGenders"] = "true";
	}
	$array_register_module = apply_filters("mobile_api_array_register_module",$array_register_module);
	$last_register_module = (isset($array_register_module) && is_array($array_register_module) && !empty($array_register_module)?$array_register_module:"true");
	$last_register_module = apply_filters("mobile_api_last_register_module",$last_register_module);
	if ($activate_register != "disabled" && $last_register_module != "false") {
		$array["registerModule"] = ($activate_custom_register_link == mobile_api_checkbox_value && $custom_register_link != ""?array("link" => esc_url($custom_register_link)):$last_register_module);
	}

	$array_profile_module = array();
	$array_profile = array("email" => "email","nickname" => "nickname","first_name" => "first_name","last_name" => "last_name","display_name" => "display_name","image_profile" => "avatar","cover" => "cover","country" => "country","city" => "city","phone" => "phone","gender" => "gender","age" => "age");
	if (is_array($edit_profile_items_1) && !empty($edit_profile_items_1)) {
		foreach ($edit_profile_items_1 as $key => $value) {
			if ($key == "email" || (isset($array_profile[$key]) && $value["value"] == $key)) {
				$array_profile_module[$array_profile[$key]] = "true";
				if ($key == "image_profile") {
					$required_profile = (isset($mobile_api_options["profile_picture_required"])?$mobile_api_options["profile_picture_required"]:"");
					if ($required_profile == mobile_api_checkbox_value) {
						$array_profile_module[$array_profile[$key]."_required"] = "true";
					}
				}else if ($key == "cover") {
					$required_profile = (isset($mobile_api_options["profile_cover_required"])?$mobile_api_options["profile_cover_required"]:"");
					if ($required_profile == mobile_api_checkbox_value) {
						$array_profile_module[$array_profile[$key]."_required"] = "true";
					}
				}else if ($key != "nickname") {
					$required_profile = (isset($mobile_api_options[$key."_required"])?$mobile_api_options[$key."_required"]:"");
					if ($required_profile == mobile_api_checkbox_value) {
						$array_profile_module[$array_profile[$key]."_required"] = "true";
					}
				}
			}
		}
	}
	if ($gender_other == mobile_api_checkbox_value) {
		$array_profile_module["showUnspecifiedGenders"] = "true";
	}

	$array_profile_2 = array("facebook" => "facebook","twitter" => "twitter","tiktok" => "tiktok","youtube" => "youtube","vimeo" => "vimeo","linkedin" => "linkedin","instagram" => "instagram","pinterest" => "pinterest");
	if (is_array($edit_profile_items_2) && !empty($edit_profile_items_2)) {
		foreach ($edit_profile_items_2 as $key => $value) {
			if (isset($array_profile_2[$key]) && $value["value"] == $key) {
				$array_profile_module[$array_profile_2[$key]] = "true";
			}
		}
	}

	$array_profile_3 = array("website" => "website","bio" => "bio","profile_credential" => "profile_credential","private_pages" => "private_pages","received_message" => "received_message");
	if ($active_message != mobile_api_checkbox_value) {
		unset($array_profile_3["received_message"]);
	}
	if (is_array($edit_profile_items_3) && !empty($edit_profile_items_3)) {
		foreach ($edit_profile_items_3 as $key => $value) {
			if (isset($array_profile_3[$key]) && $value["value"] == $key) {
				$array_profile_module[$array_profile_3[$key]] = "true";
				if ($key == "profile_credential") {
					$required_profile = (isset($mobile_api_options[$key."_required"])?$mobile_api_options[$key."_required"]:"");
					if ($required_profile == mobile_api_checkbox_value) {
						$array_profile_module[$array_profile_3[$key]."_required"] = "true";
					}
				}else if ($key == "website" && $url_required_profile == mobile_api_checkbox_value) {
					$array_profile_module["website_required_profile"] = "true";
				}
			}
		}
	}

	$array_profile_4 = array("question_schedules" => "question_schedules","post_schedules" => "post_schedules","send_emails" => "send_emails","send_emails_post" => "send_emails_post","new_payment_mail" => "new_payment_mail","send_message_mail" => "send_message_mail","answer_on_your_question" => "answer_on_your_question","answer_question_follow" => "answer_question_follow","notified_reply" => "notified_reply","unsubscribe_mails" => "unsubscribe_mails");
	$send_emails = ($send_email_new_question == mobile_api_checkbox_value && is_array($send_email_question_groups) && isset($user_group) && in_array($user_group,$send_email_question_groups)?true:false);
	$send_emails_post = ($send_email_new_post == mobile_api_checkbox_value && is_array($send_email_post_groups) && isset($user_group) && in_array($user_group,$send_email_post_groups)?true:false);
	$question_schedules = ($question_schedules == mobile_api_checkbox_value && is_array($question_schedules_groups) && isset($user_group) && in_array($user_group,$question_schedules_groups)?true:false);
	$post_schedules = ($post_schedules == mobile_api_checkbox_value && is_array($post_schedules_groups) && isset($user_group) && in_array($user_group,$post_schedules_groups)?true:false);
	if ($send_emails != true) {
		unset($array_profile_4["send_emails"]);
	}
	if ($send_emails_post != true) {
		unset($array_profile_4["send_emails_post"]);
	}
	if ($question_schedules != true) {
		unset($array_profile_4["question_schedules"]);
	}
	if ($post_schedules != true) {
		unset($array_profile_4["post_schedules"]);
	}
	if ($active_message != mobile_api_checkbox_value) {
		unset($array_profile_4["send_message_mail"]);
	}
	if ($payment_available != true) {
		unset($array_profile_4["new_payment_mail"]);
	}

	if (is_array($edit_profile_items_4) && !empty($edit_profile_items_4)) {
		foreach ($edit_profile_items_4 as $key => $value) {
			if (isset($array_profile_4[$key]) && $value["value"] == $key) {
				$array_profile_module[$array_profile_4[$key]] = "true";
			}
		}
	}

	if (has_askme()) {
		$array_profile_askme = array("avatar" => "avatar","bio" => "bio","website" => "website","facebook" => "facebook","twitter" => "twitter","tiktok" => "tiktok","youtube" => "youtube","linkedin" => "linkedin","instagram" => "instagram","pinterest" => "pinterest","private_pages" => "private_pages","follow_email" => "follow_email","send_emails" => "send_emails","send_emails_post" => "send_emails_post","received_message" => "received_message");
		if ($url_profile != mobile_api_checkbox_value) {
			unset($array_profile_askme["website"]);
		}
		if ($profile_picture_profile != mobile_api_checkbox_value) {
			unset($array_profile_askme["avatar"]);
		}
		if ($send_emails != true) {
			unset($array_profile_askme["send_emails"]);
		}
		if ($send_emails_post != true) {
			unset($array_profile_askme["send_emails_post"]);
		}
		if ($active_message != mobile_api_checkbox_value) {
			unset($array_profile_askme["received_message"]);
		}

		if (is_array($array_profile_askme) && !empty($array_profile_askme)) {
			foreach ($array_profile_askme as $key => $value) {
				$array_profile_module[$array_profile_askme[$key]] = "true";
				if ($key == "website" && $url_required_profile == mobile_api_checkbox_value) {
					$array_profile_module["website_required_profile"] = "true";
				}
			}
		}
	}
	
	$last_profile_module = (isset($array_profile_module) && is_array($array_profile_module) && !empty($array_profile_module)?$array_profile_module:"");
	$array["editProfileModule"] = $last_profile_module;

	$array["icons"]["answers"] = $mobile_answers_icon;
	$array["icons"]["bestAnswers"] = $mobile_best_answers_icon;
	$array["icons"]["deleteAccountIcon"] = $mobile_delete_icon; // or false if I want to delete it.
	$array["icons"]["deleteIcon"] = $mobile_delete_icon;
	$array["icons"]["closeQuestion"] = $mobile_close_icon;
	$array["icons"]["openQuestion"] = $mobile_open_icon;
	$array["icons"]["favourite"] = $mobile_favourite_icon;
	$array["icons"]["unfavourite"] = $mobile_unfavourite_icon;
	$array["icons"]["views"] = $mobile_views_icon;
	$array["icons"]["other"] = "0xea93";
	$array["icons"]["infoEdited"] = "fa-exclamation";
	//$array["icons"]["logoutIcon"] = "false";
	if ($activate_verified_icon == mobile_api_checkbox_value) {
		$array["icons"]["verified"] = $mobile_verified_icon;
	}
	if ($activate_vote_icons == mobile_api_checkbox_value) {
		$array["icons"]["upvote"] = $mobile_upvote_icon;
		$array["icons"]["downvote"] = $mobile_downvote_icon;
	}
	if ($mobile_social_icon_style == "icons") {
		$array["icons"]["facebook"] = "fa-facebook";
		$array["icons"]["tiktok"] = "fa-tiktok";
		$array["icons"]["instagram"] = "fa-instagram";
		$array["icons"]["pinterest"] = "fa-pinterest";
		$array["icons"]["twitter"] = "fa-twitter";
		$array["icons"]["youtube"] = "fa-youtube";
		$array["icons"]["vimeo"] = "fa-vimeo";
		$array["icons"]["linkedin"] = "fa-linkedin";
		$array["icons"]["website"] = "0xed2c";
	}
	if ($active_reaction == mobile_api_checkbox_value) {
		$array["icons"]["reactions"] = "0xf00c";
	}

	$array_question = array("title_question" => "title","categories_question" => "category","tags_question" => "tags","poll_question" => "poll","featured_image" => "featureImage","comment_question" => "content","anonymously_question" => "anonymously","video_desc_active" => "videoDescription","private_question" => "privateQuestion","remember_answer" => "remember","terms_active" => "terms","attachment_question" => "attachments");
	if (is_array($ask_question_items) && !empty($ask_question_items)) {
		foreach ($ask_question_items as $key => $value) {
			if (isset($array_question[$key]) && $value["value"] == $key) {
				if ($key != "poll_question" || ($key == "poll_question" && ($custom_poll_groups != mobile_api_checkbox_value || ($custom_poll_groups == mobile_api_checkbox_value && isset($user_group) && isset($poll_groups[$user_group]) && $poll_groups[$user_group] == $user_group)))) {
					$array["addQuestion"][$array_question[$key]] = "true";
				}
			}
		}
	}

	if ($content_question_required == mobile_api_checkbox_value) {
		$array["addQuestion"]["contentRequired"] = "true";
	}
	if ($categories_question == true && $question_category_required == mobile_api_checkbox_value) {
		$array["addQuestion"]["categoryRequired"] = "true";
	}
	if ($question_tags_number_min_limit > 0) {
		$array["addQuestion"]["tagsRequired"] = "true";
	}
	if ($custom_poll_groups != mobile_api_checkbox_value || ($custom_poll_groups == mobile_api_checkbox_value && isset($user_group) && isset($poll_groups[$user_group]) && $poll_groups[$user_group] == $user_group)) {
		if ($poll_only == mobile_api_checkbox_value) {
			$array["addQuestion"]["pollOnly"] = "true";
		}
		if ($multicheck_poll == mobile_api_checkbox_value) {
			$array["addQuestion"]["multicheckPoll"] = "true";
		}
		if ($poll_image == mobile_api_checkbox_value) {
			$array["addQuestion"]["imagePoll"] = "true";
			if ($poll_image_title == mobile_api_checkbox_value) {
				$array["addQuestion"]["imagePollTitle"] = "true";
				if ($poll_image_title_required == mobile_api_checkbox_value) {
					$array["addQuestion"]["imagePollTitleRequired"] = "true";
				}
			}
		}
	}

	$array_question = array("title_question" => "title","comment_question" => "content","anonymously_question" => "anonymously","private_question" => "privateQuestion","remember_answer" => "remember","terms_active" => "terms");
	if (is_array($ask_user_items) && !empty($ask_user_items)) {
		foreach ($ask_user_items as $key => $value) {
			if (isset($array_question[$key]) && $value["value"] == $key) {
				$array["addQuestion"]["askUser"][$array_question[$key]] = "true";
			}
		}
	}
	
	if ($content_user_question_required == mobile_api_checkbox_value) {
		$array["addQuestion"]["askUser"]["contentRequired"] = "true";
	}

	$array_question = array("title_question" => "title","categories_question" => "category","tags_question" => "tags","poll_question" => "poll","featured_image" => "featureImage","comment_question" => "content","video_desc_active" => "videoDescription","private_question" => "privateQuestion","remember_answer" => "remember","attachment_question" => "attachments");
	if (is_array($ask_question_items) && !empty($ask_question_items)) {
		foreach ($ask_question_items as $key => $value) {
			if (isset($array_question[$key]) && $value["value"] == $key) {
				if ($key != "poll_question" || ($key == "poll_question" && ($custom_poll_groups != mobile_api_checkbox_value || ($custom_poll_groups == mobile_api_checkbox_value && isset($user_group) && isset($poll_groups[$user_group]) && $poll_groups[$user_group] == $user_group)))) {
					$array["editQuestion"][$array_question[$key]] = "true";
				}
			}
		}
	}

	if ($content_question_required == mobile_api_checkbox_value) {
		$array["editQuestion"]["contentRequired"] = "true";
	}
	if ($categories_question == true && $question_category_required == mobile_api_checkbox_value) {
		$array["editQuestion"]["categoryRequired"] = "true";
	}
	if ($question_tags_number_min_limit > 0) {
		$array["editQuestion"]["tagsRequired"] = "true";
	}
	if ($custom_poll_groups != mobile_api_checkbox_value || ($custom_poll_groups == mobile_api_checkbox_value && isset($user_group) && isset($poll_groups[$user_group]) && $poll_groups[$user_group] == $user_group)) {
		if ($poll_only == mobile_api_checkbox_value) {
			$array["editQuestion"]["pollOnly"] = "true";
		}
		if ($multicheck_poll == mobile_api_checkbox_value) {
			$array["editQuestion"]["multicheckPoll"] = "true";
		}
		if ($poll_image == mobile_api_checkbox_value) {
			$array["editQuestion"]["imagePoll"] = "true";
			if ($poll_image_title == mobile_api_checkbox_value) {
				$array["editQuestion"]["imagePollTitle"] = "true";
				if ($poll_image_title_required == mobile_api_checkbox_value) {
					$array["editQuestion"]["imagePollTitleRequired"] = "true";
				}
			}
		}
	}

	$array_question = array("title_question" => "title","comment_question" => "content","private_question" => "privateQuestion","remember_answer" => "remember");
	if (is_array($ask_user_items) && !empty($ask_user_items)) {
		foreach ($ask_user_items as $key => $value) {
			if (isset($array_question[$key]) && $value["value"] == $key) {
				$array["editQuestion"]["askUser"][$array_question[$key]] = "true";
			}
		}
	}
	
	if ($content_user_question_required == mobile_api_checkbox_value) {
		$array["editQuestion"]["askUser"]["contentRequired"] = "true";
	}

	$array["addBlog"]["title"] = "true";
	$array["addBlog"]["category"] = "true";

	$array_post = array("tags_post" => "tags","featured_image" => "featureImage","content_post" => "content","terms_active" => "terms");
	if (is_array($add_post_items) && !empty($add_post_items)) {
		foreach ($add_post_items as $key => $value) {
			if (isset($array_post[$key]) && $value["value"] == $key) {
				$array["addBlog"][$array_post[$key]] = "true";
			}
		}
	}

	if ($content_post == true) {
		$array["addBlog"]["contentRequired"] = "true";
	}
	$array["addBlog"]["categoryRequired"] = "true";

	$array["editBlog"]["title"] = "true";
	$array["editBlog"]["category"] = "true";

	$array_post = array("tags_post" => "tags","featured_image" => "featureImage","content_post" => "content");
	if (is_array($add_post_items) && !empty($add_post_items)) {
		foreach ($add_post_items as $key => $value) {
			if (isset($array_post[$key]) && $value["value"] == $key) {
				$array["editBlog"][$array_post[$key]] = "true";
			}
		}
	}

	if ($content_post == true) {
		$array["editBlog"]["contentRequired"] = "true";
	}
	$array["editBlog"]["categoryRequired"] = "true";

	$array["addAnswer"]["content"] = "true";
	if ($featured_image_answer == mobile_api_checkbox_value) {
		$array["addAnswer"]["featureImage"] = "true";
	}
	if ($answer_anonymously == mobile_api_checkbox_value) {
		$array["addAnswer"]["anonymously"] = "true";
	}
	if ($private_answer == mobile_api_checkbox_value) {
		$array["addAnswer"]["private"] = "true";
	}
	if ($answer_video == mobile_api_checkbox_value) {
		$array["addAnswer"]["videoDescription"] = "true";
	}
	if ($terms_active_comment == mobile_api_checkbox_value) {
		$array["addAnswer"]["terms"] = "true";
	}
	if ($attachment_answer == mobile_api_checkbox_value) {
		$array["addAnswer"]["attachments"] = "true";
	}

	$array["editComment"]["content"] = "true";
	$array["editAnswer"]["content"] = "true";
	if ($featured_image_answer == mobile_api_checkbox_value) {
		$array["editAnswer"]["featureImage"] = "true";
	}
	if ($private_answer == mobile_api_checkbox_value) {
		$array["editAnswer"]["private"] = "true";
	}
	if ($answer_video == mobile_api_checkbox_value) {
		$array["editAnswer"]["videoDescription"] = "true";
	}
	if ($attachment_answer == mobile_api_checkbox_value) {
		$array["editAnswer"]["attachments"] = "true";
	}

	if ($terms_checked_register == mobile_api_checkbox_value) {
		$array["default"]["registerTerms"] = "true";
	}
	if (isset($add_question_default["poll"]) && $add_question_default["poll"] == "poll") {
		$array["default"]["addQuestionsPoll"] = "true";
	}
	if (isset($add_question_default["multicheck_poll"]) && $add_question_default["multicheck_poll"] == "multicheck_poll") {
		$array["default"]["addQuestionsMulticheckPoll"] = "true";
	}
	if (isset($add_question_default["video"]) && $add_question_default["video"] == "video") {
		$array["default"]["addQuestionsVideos"] = "true";
	}
	if (isset($add_question_default["notified"]) && $add_question_default["notified"] == "notified") {
		$array["default"]["addQuestionsRemember"] = "true";
	}
	if (isset($add_question_default["private"]) && $add_question_default["private"] == "private") {
		$array["default"]["addQuestionsPrivate"] = "true";
	}
	if (isset($add_question_default["anonymously"]) && $add_question_default["anonymously"] == "anonymously") {
		$array["default"]["addQuestionsAnonymous"] = "true";
	}
	if (isset($add_question_default["terms"]) && $add_question_default["terms"] == "terms") {
		$array["default"]["addQuestionsTerms"] = "true";
	}
	if (isset($add_question_default["poll_image"]) && $add_question_default["poll_image"] == "poll_image") {
		$array["default"]["addQuestionsImagePoll"] = "true";
	}
	if (isset($add_question_default_user["terms"]) && $add_question_default_user["terms"] == "terms") {
		$array["default"]["addUserQuestionTerms"] = "true";
	}
	if (isset($add_question_default_user["notified"]) && $add_question_default_user["notified"] == "notified") {
		$array["default"]["addUserQuestionRemember"] = "true";
	}
	if (isset($add_question_default_user["private"]) && $add_question_default_user["private"] == "private") {
		$array["default"]["addUserQuestionPrivate"] = "true";
	}
	if (isset($add_question_default_user["anonymously"]) && $add_question_default_user["anonymously"] == "anonymously") {
		$array["default"]["addUserQuestionAnonymous"] = "true";
	}
	if ($terms_checked_comment == mobile_api_checkbox_value) {
		$array["default"]["addAnswerTerms"] = "true";
	}
	if ($private_checked_answer == mobile_api_checkbox_value) {
		$array["default"]["addAnswerPrivate"] = "true";
	}
	if ($anonymously_checked_answer == mobile_api_checkbox_value) {
		$array["default"]["addAnswerAnonymous"] = "true";
	}
	if ($video_checked_answer == mobile_api_checkbox_value) {
		$array["default"]["addAnswerVideo"] = "true";
	}
	if ($terms_checked_comment == mobile_api_checkbox_value) {
		$array["default"]["addCommentTerms"] = "true";
	}
	$array["default"]["addBlogTerms"] = "true";

	if ($addaction == mobile_api_checkbox_value) {
		$mobile_addaction_question = ($mobile_addaction_question != ""?$mobile_addaction_question:"0xe965");
		$mobile_addaction_post = ($mobile_addaction_post != ""?$mobile_addaction_post:"0xf0ca");
		$array["addAction"]["icon"] = ($addaction_mobile_action == "post"?$mobile_addaction_post:$mobile_addaction_question);
		$array["addAction"]["target"] = ($addaction_mobile_action == "post"?"addBlog":"addQuestion");
	}

	if ($ask_question_to_users == mobile_api_checkbox_value) {
		$array["askUser"] = "true";
	}

	if ($active_reports == mobile_api_checkbox_value) {
		$array["report"] = "true";
	}
	if ($active_logged_reports != mobile_api_checkbox_value) {
		$array["visitor"]["report"] = "true";
	}
	if ($ask_question_no_register == mobile_api_checkbox_value && ($custom_permission != mobile_api_checkbox_value || ($custom_permission == mobile_api_checkbox_value && $ask_question == mobile_api_checkbox_value))) {
		$array["visitor"]["addQuestion"] = "true";
	}
	if ($add_post_no_register == mobile_api_checkbox_value && ($custom_permission != mobile_api_checkbox_value || ($custom_permission == mobile_api_checkbox_value && $add_post == mobile_api_checkbox_value))) {
		$array["visitor"]["addPost"] = "true";
	}
	if ($send_message_no_register == mobile_api_checkbox_value && ($custom_permission != mobile_api_checkbox_value || ($custom_permission == mobile_api_checkbox_value && $send_message == mobile_api_checkbox_value))) {
		$array["visitor"]["sendMessage"] = "true";
	}
	if ($poll_user_only == mobile_api_checkbox_value) {
		$array["visitor"]["addPoll"] = "true";
	}
	if ($active_vote_unlogged == mobile_api_checkbox_value) {
		$array["visitor"]["addVote"] = "true";
	}
	$visitor_can_answer = $visitor_can_comment = false;
	if ($custom_permission == mobile_api_checkbox_value && $add_answer == mobile_api_checkbox_value) {
		$visitor_can_answer = true;
	}
	if ($custom_permission == mobile_api_checkbox_value && $add_comment == mobile_api_checkbox_value) {
		$visitor_can_comment = true;
	}
	if ($custom_permission != mobile_api_checkbox_value && !get_option('comment_registration')) {
		$visitor_can_answer = $visitor_can_comment = true;
	}
	if ((has_askme() && $post_comments_user == mobile_api_checkbox_value)) {
		$visitor_can_answer = $visitor_can_comment = false;
	}
	if ($visitor_can_answer == true) {
		$array["visitor"]["addAnswer"] = "true";
	}
	if ($visitor_can_comment == true) {
		$array["visitor"]["addComment"] = "true";
	}

	if ($custom_permission == mobile_api_checkbox_value) {
		if ($show_question != mobile_api_checkbox_value) {
			$array["visitor"]["not_see_questions"] = "true";
		}else if ($show_answer != mobile_api_checkbox_value) {
			$array["visitor"]["not_see_answers"] = "true";
		}else if ($show_post != mobile_api_checkbox_value) {
			$array["visitor"]["not_see_posts"] = "true";
		}else if ($show_comment != mobile_api_checkbox_value) {
			$array["visitor"]["not_see_comments"] = "true";
		}else if ($show_knowledgebase != mobile_api_checkbox_value) {
			$array["visitor"]["not_see_article"] = "true";
		}
	}

	if ($activate_captcha_mobile == mobile_api_checkbox_value && ($captcha_login == true || $captcha_register == true || $captcha_answer == true || $captcha_question == true) && $site_key_recaptcha_mobile != "" && $secret_key_recaptcha_mobile != "") {
		$array["reCaptcha"]["siteKey"] = $site_key_recaptcha_mobile;
		$array["reCaptcha"]["secretKey"] = $secret_key_recaptcha_mobile;
		if ($captcha_login == true) {
			$array["reCaptcha"]["positions"]["login"] = "true";
		}
		if ($captcha_register == true) {
			$array["reCaptcha"]["positions"]["register"] = "true";
		}
		if ($captcha_answer == true) {
			$array["reCaptcha"]["positions"]["answer"] = "true";
		}
		if ($captcha_question == true) {
			$array["reCaptcha"]["positions"]["question"] = "true";
		}
	}

	if (!is_array($mobile_notifications_tabs) || (is_array($mobile_notifications_tabs) && isset($mobile_notifications_tabs["unread"]) && $mobile_notifications_tabs["unread"])) {
		$show_unread_notifications = true;
	}
	if (is_array($mobile_notifications_tabs) && isset($mobile_notifications_tabs["all"]) && $mobile_notifications_tabs["all"]) {
		$show_all_notifications = true;
	}
	if (isset($show_unread_notifications) || (!isset($show_unread_notifications) && !isset($show_all_notifications))) {
		$array["notifications"]["unread"] = "true";
	}
	if (isset($show_all_notifications)) {
		$array["notifications"]["all"] = "true";
	}

	if ($active_reaction == mobile_api_checkbox_value) {
		$array["reactions"]["reaction"]["like"] = "true";
		if (!is_array($reaction_items) || (is_array($reaction_items) && empty($reaction_items))) {
			$reaction_items = array(
				"love"  => array("value" => "love"),
				"hug"   => array("value" => "hug"),
				"haha"  => array("value" => "haha"),
				"wow"   => array("value" => "wow"),
				"sad"   => array("value" => "sad"),
				"angry" => array("value" => "angry"),
			);
		}
		if (is_array($reaction_items) && !empty($reaction_items)) {
			foreach ($reaction_items as $key => $value) {
				if (isset($value["value"]) && $value["value"] == $key) {
					$array["reactions"]["reaction"]["{$key}"] = "true";
				}
			}
		}
		$array["reactions"]["questions"] = "true";
		$array["reactions"]["loopQuestions"] = "true";
		if ($active_reaction_answers == mobile_api_checkbox_value) {
			$array["reactions"]["answers"] = "true";
		}
	}

	$array["basicUrls"]["submitVoteUrl"] = mobile_api_submit_vote;
	$array["basicUrls"]["favourite"] = mobile_api_user_favorite;
	if ($question_follow == mobile_api_checkbox_value) {
		$array["basicUrls"]["followQuestion"] = mobile_api_question_follow;
	}
	$array["basicUrls"]["register"] = mobile_api_register;
	$array["basicUrls"]["login"] = mobile_api_login;
	$array["basicUrls"]["logout"] = mobile_api_logout;
	$array["basicUrls"]["forgotPassword"] = mobile_api_forgot_password;
	$array["basicUrls"]["currentUser"] = mobile_api_currentuser;
	$array["basicUrls"]["userInfo"] = mobile_api_userinfo;
	$array["basicUrls"]["getPost"] = mobile_api_get_post;
	$array["basicUrls"]["follow"] = mobile_api_follow;
	$array["basicUrls"]["faq"] = mobile_api_faqs;
	$array["basicUrls"]["pollResults"] = mobile_api_poll_results;
	$array["basicUrls"]["submitPoll"] = mobile_api_submit_poll;
	$array["basicUrls"]["followers"] = mobile_api_followers;
	$array["basicUrls"]["followings"] = mobile_api_following;
	if ($block_users == mobile_api_checkbox_value) {
		$array["basicUrls"]["block"] = mobile_api_user_block;
		$array["basicUrls"]["getBlockedUsers"] = mobile_api_user_blocking;
	}
	$array["basicUrls"]["getTags"] = mobile_api_tags."?taxonomy=".mobile_api_question_tags;
	$array["basicUrls"]["getTagsPosts"] = mobile_api_post_tags."?taxonomy=post_tag";
	$array["basicUrls"]["editProfile"] = mobile_api_edit_profile;
	$array["basicUrls"]["uploadAvatar"] = mobile_api_upload_avatar;
	if ($cover_image == mobile_api_checkbox_value) {
		$array["basicUrls"]["uploadCover"] = mobile_api_upload_cover;
	}
	$array["basicUrls"]["submitComment"] = mobile_api_submit_comment;
	$array["basicUrls"]["getComment"] = mobile_api_get_comment;
	$array["basicUrls"]["editComment"] = mobile_api_edit_comment;
	$array["basicUrls"]["bestAnswerAction"] = mobile_api_best_answer;
	$array["basicUrls"]["getUserIndex"] = mobile_api_get_users;
	$array["basicUrls"]["askQuestion"] = mobile_api_ask_question;
	$array["basicUrls"]["editQuestion"] = mobile_api_edit_question;
	$array["basicUrls"]["addBlog"] = mobile_api_add_post;
	$array["basicUrls"]["editBlog"] = mobile_api_edit_post;
	$array["basicUrls"]["badges"] = mobile_api_points;
	$array["basicUrls"]["get_rewarded"] = mobile_api_get_rewarded;
	$array["basicUrls"]["getPostWPJSON"] = mobile_api_get_post."/?post_type=post";
	$array["basicUrls"]["questionCategories"] = mobile_api_get_categories."?taxonomy=".mobile_api_question_categories."&type=question";
	$array["basicUrls"]["postCategories"] = mobile_api_get_categories."?taxonomy=category&type=post";
	$array["basicUrls"]["followCategories"] = mobile_api_get_categories."?taxonomy=".mobile_api_question_categories."&type=follow";
	$array["basicUrls"]["notifications"] = mobile_api_notifications;
	$array["basicUrls"]["allNotifications"] = mobile_api_all_notifications;
	$array["basicUrls"]["readNotification"] = mobile_api_read_notifications;
	$array["basicUrls"]["dismissNotification"] = mobile_api_dismiss_notifications;
	$array["basicUrls"]["removeToken"] = mobile_api_silent_notifications;
	$array["basicUrls"]["stopNotifications"] = mobile_api_stop_notifications;
	$array["basicUrls"]["deleteAccount"] = mobile_api_delete."?type=user";
	$array["basicUrls"]["completeFollowing"] = mobile_api_complete_following;
	$array["basicUrls"]["getRegisterCountries"] = mobile_api_register_countries;
	$array["basicUrls"]["report"] = mobile_api_report;
	if ($active_reaction == mobile_api_checkbox_value) {
		$array["basicUrls"]["reactions"] = mobile_api_reactions;
	}
	$array["basicAPI"] = mobile_api_base;

	$array["translations"] = mobile_api_language();

	$array = apply_filters("mobile_api_config_array",$array,$mobile_api_options);

	if (is_array($array) && !empty($array)) {
		foreach ($array as $key => $value) {
			$array_json[$key] = $value;
		}
	}

	return (isset($array_json) && !empty($array_json)?$array_json:array());
}
/* Mobile options API */
add_action('rest_api_init','mobile_api_config_option');
function mobile_api_config_option() {
	register_rest_route('mobile/v1','/options/',array(
		'methods'             => 'GET',
		'callback'            => 'mobile_api_config_option_endpoint',
        'permission_callback' => '__return_true'
	));
}
function mobile_api_config_option_endpoint() {
	$array_json = mobile_api_config();
	return $array_json;
}
/* Mobile meta API */
add_action('rest_api_init','mobile_api_config_meta');
function mobile_api_config_meta() {
	register_rest_route('mobile/v1','/meta/',array(
		'methods'             => 'GET',
		'callback'            => 'mobile_api_config_meta_endpoint',
        'permission_callback' => '__return_true'
	));
}
function mobile_api_config_meta_endpoint() {
	$mobile_api_options = get_option(mobile_api_options_name());
	$array_json = mobile_api_meta_config(array(),$mobile_api_options);
	return $array_json;
}?>
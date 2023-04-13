<?php
/*
Controller name: Core
Controller description: Basic introspection methods
*/

class MOBILE_API_Core_Controller {

	public function test() {
		global $mobile_api;
		$args = array(
			'method'      => 'POST',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'body'        => $_REQUEST,
		);
		add_filter('https_ssl_verify', '__return_false');
		$api_url  = esc_url(home_url("/").mobile_api_login);
		$get_request = wp_remote_get($api_url,$args);
		$request = wp_remote_retrieve_body($get_request);
		$response = json_decode($request);
		$token = $response->token;

		$args = array(
			'method'      => 'POST',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array('Authorization' => "Bearer $token")
		);
		add_filter('https_ssl_verify', '__return_false');
		$api_url  = esc_url(home_url("/").);
		$get_request = wp_remote_get($api_url,$args);
		$request = wp_remote_retrieve_body($get_request);
		$response = json_decode($request);
		return $response;
	}

	public function token() {
		global $mobile_api;
		$user_id = get_current_user_id();
		return ($user_id > 0?"It's working":"error");
	}

	public function options() {
		$array_json = mobile_api_config();
		$array_json["status"] = "removestatus";
		return $array_json;
	}

	public function meta() {
		$mobile_api_options = get_option(mobile_api_options_name());
		$array_json = mobile_api_meta_config(array(),$mobile_api_options);
		$array_json["status"] = "removestatus";
		return $array_json;
	}

	public function points() {
		global $mobile_api;
		$points_json = $badges_json = array();
		$points_array = mobile_api_get_points();
		$points_details = mobile_api_options("points_details");
		$badges_details = mobile_api_options("badges_details");
		$badges_style  = mobile_api_options("badges_style");
		$badges_groups = mobile_api_options("badges_groups");
		if (is_array($points_array) && !empty($points_array)) {
			$count = 0;
			foreach ($points_array as $key => $value) {
				if ($value["points"] > 0) {
					$points_json[] = array("id" => $count,"points" => $value["points"],"description" => mobile_api_get_points_name($key));
					$count++;
				}
			}
		}
		$badge_key = "badge_points";
		if ($badges_style == "by_groups_points") {
			$badges = mobile_api_options("badges_groups_points");
		}else if ($badges_style == "by_questions") {
			$badges = mobile_api_options("badges_questions");
			$badge_key = "badge_questions";
		}else if ($badges_style == "by_answers") {
			$badges = mobile_api_options("badges_answers");
			$badge_key = "badge_answers";
		}else {
			$badges = mobile_api_options("badges");
		}
		if (is_array($badges) && !empty($badges)) {
			$points_badges = array_column($badges,$badge_key);
	    	array_multisort($points_badges,SORT_ASC,$badges);
			if (($badges_style != "by_groups" && isset($badges) && is_array($badges))) {
				$count = 0;
				foreach ($badges as $badges_k => $badges_v) {
					if (isset($badges_v[$badge_key]) && $badges_v[$badge_key] != "") {
						$badge_value = (int)$badges_v[$badge_key];
						if ($badges_style == "by_questions") {
							$badge_keys = _n("Question","Questions",$badge_value,"mobile-api");
						}else if ($badges_style == "by_answers") {
							$badge_keys = _n("Answer","Answers",$badge_value,"mobile-api");
						}else {
							$badge_keys = _n("Point","Points",$badge_value,"mobile-api");
						}
						$badges_json[] = array("id" => $count,"points" => $badge_value,"key" => $badge_keys,"value" => $badge_value,"name" => strip_tags(stripslashes($badges_v["badge_name"])),"color" => esc_html($badges_v["badge_color"]),"description" => mobile_api_kses_stip(stripslashes($badges_v["badge_details"])));
						$count++;
					}
				}
			}
		}
		return array(
			"points_description" => ($points_details != ""?mobile_api_kses_stip(nl2br(stripslashes($points_details))):""),
			"points" => (is_array($points_json) && !empty($points_json)?$points_json:array()),
			"badges_description" => ($badges_details != ""?mobile_api_kses_stip(nl2br(stripslashes($badges_details))):""),
			"badges" => (is_array($badges_json) && !empty($badges_json)?$badges_json:array())
		);
	}
	
	public function tabs() {
		global $mobile_api;
		$home_page_app = mobile_api_options('home_page_app');
		if ($home_page_app > 0) {
			$home_page_id  = $home_page_app;
			$question_bump = mobile_api_options('question_bump');
			$get_home_tabs = get_post_meta($home_page_id,mobile_api_theme_prefix."_home_tabs",true);
			if (is_array($get_home_tabs) && !empty($get_home_tabs)) {
				$user_id = get_current_user_id();
				foreach ($get_home_tabs as $key => $value) {
					$tabs_type = mobile_api_questions_type;
					$post_type = $post_layout = "";
					if (isset($value["value"]) && isset($value["cat"])) {
						if (is_numeric($value["value"]) && $value["value"] > 0) {
							$get_tax = get_term($value["value"]);
							if (isset($get_tax->term_id) && $get_tax->term_id > 0) {
								$array["title"] = esc_html($get_tax->name);
								$array["json"] = mobile_api_posts_category."?id=".$get_tax->term_id."&taxonomy=".$get_tax->taxonomy."&type=".$tabs_type;
							}else {
								$array["title"] = esc_html__("All Questions","mobile-api");
								$array["json"] = mobile_api_posts."?post_type=".$tabs_type."&page=1&type=".$tabs_type;
							}
						}else {
							$array["title"] = esc_html__("All Questions","mobile-api");
							$array["json"] = mobile_api_posts."?post_type=".$tabs_type."&page=1&type=".$tabs_type;
						}
						if ($post_layout != "") {
							$array["post_layout"] = $post_layout;
						}
						if ($post_type != "") {
							$array["post_type"] = $post_type;
						}
						if (isset($array)) {
							$home_tabs["tabs"][] = $array;
						}
						if (isset($array["post_layout"])) {
							unset($array["post_layout"]);
						}
						if (isset($array["post_type"])) {
							unset($array["post_type"]);
						}
					}else if (isset($value["value"]) && $value["value"] == $key) {
						if ($question_bump != mobile_api_checkbox_value && $key == "question-bump" || $key == "question-bump-2") {
							continue;
						}
						if ($key == "answers" || $key == "answers-might-like" || $key == "answers-for-you" || $key == "answers-2" || $key == "answers-might-like-2" || $key == "answers-for-you-2") {
							$post_type = "answer";
							$tabs_type = "answer";
							$post_layout = "PostLayout.answerPost";
						}else if ($key == "comments" || $key == "comments-2") {
							$post_type = "comment";
							$tabs_type = "comment";
							$post_layout = "PostLayout.commentPost";
						}else if ($key == "recent-posts" || $key == "posts-visited" || $key == "recent-posts-2" || $key == "posts-visited-2") {
							$post_type = "post";
							$tabs_type = "post";
							$post_layout = "PostLayout.blogPost";
						}
						$lang_tab = mobile_api_options(str_ireplace("-","","lang_tab".$key));
						$array["title"] = ($lang_tab != ""?$lang_tab:$value["sort"]);
						$array["json"] = mobile_api_tabs_content."?get_tab=".str_ireplace("-","_","get_".$key)."&type=".$tabs_type;
						if ($post_layout != "") {
							$array["post_layout"] = $post_layout;
						}
						if ($post_type != "") {
							$array["post_type"] = $post_type;
						}
						if (isset($array)) {
							$home_tabs["tabs"][] = $array;
						}
						if (isset($array["post_layout"])) {
							unset($array["post_layout"]);
						}
						if (isset($array["post_type"])) {
							unset($array["post_type"]);
						}
					}
				}
			}else {
				$mobile_api->error(esc_html__("No tab was found.","mobile-api"));
			}
			if (isset($home_tabs) && is_array($home_tabs) && !empty($home_tabs)) {
				return $home_tabs;
			}
		}else {
			$mobile_api->error(esc_html__("No homepage was found.","mobile-api"));
		}
	}
	
	public function get_tabs_content() {
		global $mobile_api,$wpdb;
		$posts = array();
		$count = $found_posts = $max_num_pages = 0;
		$get_tab = esc_attr(isset($_GET['get_tab']) && $_GET['get_tab']?$_GET['get_tab']:'');
		if ($get_tab != '') {
			$home_page_app = mobile_api_options('home_page_app');
			if ($home_page_app > 0) {
				if (is_user_logged_in()) {
					$is_user_logged_in = true;
				}
				$order_post = 'DESC';
				$updated_answers = mobile_api_options('updated_answers');
				$home_page_id = $home_page_app;
				$theme_home_tabs = get_post_meta($home_page_id,mobile_api_theme_prefix.'_home_tabs',true);
				$get_tab = str_ireplace('get_','',$get_tab);
				$get_tab = str_ireplace('_','-',$get_tab);
				if (isset($theme_home_tabs[$get_tab]['value']) && $theme_home_tabs[$get_tab]['value'] == $get_tab) {
					if ($get_tab == 'feed-2') {
						$specific_date = get_post_meta($home_page_id,mobile_api_theme_prefix."_date_feed",true);
					}else if ($get_tab == 'recent-questions-2') {
						$specific_date = get_post_meta($home_page_id,mobile_api_theme_prefix."_date_recent_questions",true);
					}else if ($get_tab == 'questions-for-you-2') {
						$specific_date = get_post_meta($home_page_id,mobile_api_theme_prefix."_date_questions_for_you",true);
					}else if ($get_tab == 'most-answers-2') {
						$specific_date = get_post_meta($home_page_id,mobile_api_theme_prefix."_date_most_answered",true);
					}else if ($get_tab == 'no-answers-2') {
						$specific_date = get_post_meta($home_page_id,mobile_api_theme_prefix."_date_no_answers",true);
					}else if ($get_tab == 'most-visit-2') {
						$specific_date = get_post_meta($home_page_id,mobile_api_theme_prefix."_date_most_visited",true);
					}else if ($get_tab == 'most-reacted-2') {
						$specific_date = get_post_meta($home_page_id,mobile_api_theme_prefix."_date_most_reacted",true);
					}else if ($get_tab == 'most-vote-2') {
						$specific_date = get_post_meta($home_page_id,mobile_api_theme_prefix."_date_most_voted",true);
					}else if ($get_tab == 'random-2') {
						$specific_date = get_post_meta($home_page_id,mobile_api_theme_prefix."_date_random_questions",true);
					}else if ($get_tab == 'new-questions-2') {
						$specific_date = get_post_meta($home_page_id,mobile_api_theme_prefix."_date_new_questions",true);
					}else if ($get_tab == 'sticky-questions-2') {
						$specific_date = get_post_meta($home_page_id,mobile_api_theme_prefix."_date_sticky_questions",true);
					}else if ($get_tab == 'polls-2') {
						$specific_date = get_post_meta($home_page_id,mobile_api_theme_prefix."_date_poll_questions",true);
					}else if ($get_tab == 'followed-2') {
						$specific_date = get_post_meta($home_page_id,mobile_api_theme_prefix."_date_followed_questions",true);
					}else if ($get_tab == 'favorites-2') {
						$specific_date = get_post_meta($home_page_id,mobile_api_theme_prefix."_date_favorites_questions",true);
					}else if ($get_tab == 'recent-posts-2') {
						$specific_date = get_post_meta($home_page_id,mobile_api_theme_prefix."_date_recent_posts",true);
					}else if ($get_tab == 'posts-visited-2') {
						$specific_date = get_post_meta($home_page_id,mobile_api_theme_prefix."_date_posts_visited",true);
					}else if ($get_tab == 'question-bump-2') {
						$specific_date = get_post_meta($home_page_id,mobile_api_theme_prefix."_date_question_bump",true);
					}else if ($get_tab == 'answers-2') {
						$specific_date = get_post_meta($home_page_id,mobile_api_theme_prefix."_date_answers",true);
					}else if ($get_tab == 'answers-might-like-2') {
						$specific_date = get_post_meta($home_page_id,mobile_api_theme_prefix."_date_answers_might_like",true);
					}else if ($get_tab == 'answers-for-you-2') {
						$specific_date = get_post_meta($home_page_id,mobile_api_theme_prefix."_date_answers_for_you",true);
					}else if ($get_tab == 'poll-feed-2') {
						$specific_date = get_post_meta($home_page_id,mobile_api_theme_prefix."_date_poll_feed",true);
					}
					if (isset($specific_date)) {
						if ($specific_date == "24" || $specific_date == "48" || $specific_date == "72" || $specific_date == "96" || $specific_date == "120" || $specific_date == "144") {
							$specific_date = $specific_date." hours";
						}else if ($specific_date == "week" || $specific_date == "month" || $specific_date == "year") {
							$specific_date = "1 ".$specific_date;
						}
					}
					$specific_date_array = (isset($specific_date) && $specific_date != "" && $specific_date != "all"?array('date_query' => array(array('after' => $specific_date.' ago'))):array());

					$post_display            = get_post_meta($home_page_id,mobile_api_theme_prefix."_question_display_r",true);
					$post_single_category    = get_post_meta($home_page_id,mobile_api_theme_prefix."_question_single_category_r",true);
					$post_categories         = get_post_meta($home_page_id,mobile_api_theme_prefix."_question_categories_r",true);
					$post_exclude_categories = get_post_meta($home_page_id,mobile_api_theme_prefix."_question_exclude_categories_r",true);
					$post_posts              = get_post_meta($home_page_id,mobile_api_theme_prefix."_question_questions_r",true);

					$categories_a = $exclude_categories_a = array();
					if (isset($post_categories) && is_array($post_categories)) {
						$categories_a = $post_categories;
					}

					if (isset($post_exclude_categories) && is_array($post_exclude_categories)) {
						$exclude_categories_a = $post_exclude_categories;
					}

					if (isset($post_display)) {
						if ($post_display == "single_category") {
							$custom_catagories_updated = $post_single_category;
							$cats_post = array('tax_query' => array(array('taxonomy' => mobile_api_question_categories,'field' => 'id','terms' => $post_single_category,'operator' => 'IN')));
						}else if ($post_display == "categories") {
							$custom_catagories_updated = $categories_a;
							$cats_post = array('tax_query' => array(array('taxonomy' => mobile_api_question_categories,'field' => 'id','terms' => $categories_a,'operator' => 'IN')));
						}else if ($post_display == "exclude_categories") {
							$custom_catagories_updated = $exclude_categories_a;
							$cats_post = array('tax_query' => array(array('taxonomy' => mobile_api_question_categories,'field' => 'id','terms' => $exclude_categories_a,'operator' => 'NOT IN')));
						}else if ($post_display == "custom_posts") {
							$custom_posts_updated = $post_posts;
							$custom_posts = explode(",",$post_posts);
							$cats_post = array('post__in' => $custom_posts);
						}else {
							$cats_post = array();
						}
					}

					if (is_tax(mobile_api_question_categories) && isset($category_id) && $category_id > 0) {
						$post_display = "single_category";
						$all_tax_updated = $category_id;
					}
					$custom_catagories_updated = (isset($custom_catagories_updated) && is_array($custom_catagories_updated) && !empty($custom_catagories_updated)?$custom_catagories_updated:(isset($custom_catagories_updated) && !is_array($custom_catagories_updated) && $custom_catagories_updated != ""?array($custom_catagories_updated):""));
					$include_posts = (isset($post_display) && $post_display == "custom_posts"?"AND $wpdb->posts.ID IN (".$custom_posts_updated.")":"");
					$custom_catagories_query = (isset($post_display) && ($post_display == "single_category" || $post_display == "categories") && is_array($custom_catagories_updated) && !empty($custom_catagories_updated)?" AND $wpdb->term_relationships.term_taxonomy_id IN (".implode(",",$custom_catagories_updated).")":(isset($post_display) && $post_display == "exclude_categories" && is_array($custom_catagories_updated) && !empty($custom_catagories_updated)?" NOT IN (".implode(",",$custom_catagories_updated).")":""));
					$custom_catagories_updated = (isset($custom_catagories_updated) && $custom_catagories_updated != ""?"AND $wpdb->term_relationships.term_taxonomy_id".$custom_catagories_query:"");
					$custom_catagories_updated = (isset($all_tax_updated) && $all_tax_updated != ""?"AND $wpdb->term_relationships.term_taxonomy_id IN (".$all_tax_updated.")":$custom_catagories_updated);
					$custom_catagories_where = ($custom_catagories_updated != ""?"LEFT JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) ":"");

					$user_id = get_current_user_id();
					$question_bump = mobile_api_options('question_bump');
					$active_points = mobile_api_options('active_points');
					$login_required = array();
					$count_posts_home = mobile_api_options('count_posts_home');
					$rows_per_page = (int)($count_posts_home > 0?$count_posts_home:get_option('posts_per_page'));
					if ($get_tab == 'answers' || $get_tab == 'answers-2' || $get_tab == 'answers-might-like' || $get_tab == 'answers-might-like-2' || $get_tab == 'answers-for-you' || $get_tab == 'answers-for-you-2') {
						$introspector = new MOBILE_API_Introspector();
						$post_type = ($get_tab == 'answers' || $get_tab == 'answers-2'?mobile_api_questions_type:"post");
						$answers_sort = mobile_api_options("mobile_answers_sort");
						$paged = mobile_api_paged();
						$offset  = ($paged -1) * $rows_per_page;
						$current = max(1,$paged);
						if ($get_tab == 'answers-might-like' || $get_tab == 'answers-might-like-2' || $get_tab == 'answers-for-you' || $get_tab == 'answers-for-you-2') {
							$post_type = mobile_api_questions_type;
							if ($get_tab == 'answers-might-like' || $get_tab == 'answers-might-like-2') {
								$user_cat_follow = get_user_meta($user_id,"user_cat_follow",true);
								$category_list = (is_array($user_cat_follow) && !empty($user_cat_follow)?$user_cat_follow:array());
								$user_tag_follow = get_user_meta($user_id,"user_tag_follow",true);
								$tag_list = (is_array($user_tag_follow) && !empty($user_tag_follow)?$user_tag_follow:array());
								$tag_posts = get_objects_in_term($tag_list,mobile_api_question_tags);
							}else if ($get_tab == 'answers-for-you' || $get_tab == 'answers-for-you-2') {
								$category_list = get_user_meta($user_id,"wpqa_for_you_cats",true);
								$tag_list = get_user_meta($user_id,"wpqa_for_you_tags",true);
								$tag_posts = get_objects_in_term($tag_list,mobile_api_question_tags);
							}

							$cat_posts = get_objects_in_term($category_list,mobile_api_question_categories);
							$posts = array_merge($tag_posts,$cat_posts);
							$posts_array = array("post__in" => $posts);
							$count_custom_posts = (isset($posts) && is_array($posts) && !empty($posts)?implode(",",$posts):(isset($posts) && !is_array($posts)?$posts:""));
						}

						if (($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) && isset($answers_sort) && $answers_sort == "reacted") {
							$comments_args = array('orderby' => array('meta_value_num' => 'DESC','comment_date' => 'ASC'),'meta_key' => 'wpqa_reactions_count','order' => 'DESC');
						}else if (($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) && isset($answers_sort) && $answers_sort == "voted") {
							$comments_args = array('orderby' => array('meta_value_num' => 'DESC','comment_date' => 'ASC'),'meta_key' => 'comment_vote','order' => 'DESC');
						}else if (isset($answers_sort) && $answers_sort == "oldest") {
							$comments_args = array('orderby' => 'comment_date','order' => 'ASC');
						}else {
							$comments_args = array('orderby' => 'comment_date','order' => 'DESC');
						}

						$author__not_in = array();
				    	$block_users = mobile_api_options("block_users");
						if ($block_users == mobile_api_checkbox_value) {
							if ($user_id > 0) {
								$get_block_users = get_user_meta($user_id,mobile_api_action_prefix()."_block_users",true);
								if (is_array($get_block_users) && !empty($get_block_users)) {
									$author__not_in = array("post_author__not_in" => $get_block_users,"author__not_in" => $get_block_users);
								}
							}
						}

						do_action("mobile_api_answers_tab");

						add_filter('comments_clauses','mobile_api_comments_clauses');

						if ($get_tab == 'answers-might-like' || $get_tab == 'answers-might-like-2' || $get_tab == 'answers-for-you' || $get_tab == 'answers-for-you-2') {
							$total = mobile_api_count_custom_comments($post_type,(isset($specific_date) && $specific_date != ""?$specific_date:""),(isset($count_custom_posts) && $count_custom_posts != ""?$count_custom_posts:""));
							$comments = (isset($posts) && is_array($posts) && !empty($posts)?get_comments(array_merge($author__not_in,$specific_date_array,$posts_array,$comments_args,array('status' => 'approve','post_type' => $post_type,'number' => $rows_per_page,'offset' => $offset))):array());
						}else {
							$total = mobile_api_comments_of_post_type($post_type);
							$comments_args = array_merge($author__not_in,$comments_args,$specific_date_array,array('post_type' => $post_type,'parent' => 0,'status' => 'approve','number' => $rows_per_page,'offset' => $offset));
							$comments = get_comments($comments_args);
						}

						$i = $s = 0;
						if (is_array($comments) && !empty($comments)) {
							$replies = array();
							foreach($comments as $comment) {
								$comments[$i] = new MOBILE_API_Comment($comment);
								$get_replies = $introspector->get_replies($replies,$comment->comment_ID,$post_id,$post_type);
								$comments[$i]->replies = (is_array($get_replies) && !empty($get_replies)?$get_replies:array());
								$i++;
							}
						}
						return array_merge($login_required,array("count" => $rows_per_page,"count_total" => $total,"pages" => ceil($total/$rows_per_page),"comments" => $comments));
					}else {
						$activate_login = mobile_api_options("activate_login");
						if ($activate_login != 'disabled' && !isset($is_user_logged_in) && ($get_tab == 'feed' || $get_tab == 'feed-2' || $get_tab == 'poll-feed' || $get_tab == 'poll-feed-2' || $get_tab == 'followed' || $get_tab == 'followed-2' || $get_tab == 'favorites' || $get_tab == 'favorites-2')) {
							if ($get_tab == 'feed' || $get_tab == 'feed-2' || $get_tab == 'poll-feed' || $get_tab == 'poll-feed-2') {
								$login_home_feed = get_post_meta($home_page_id,mobile_api_theme_prefix."login_home_feed",true);
								if ($login_home_feed == "login") {
									$message = esc_html__("You must login to see your feed.","mobile-api");
								}
							}else if ($get_tab == 'followed' || $get_tab == 'followed-2') {
								$message = esc_html__("You must login to see your followed.","mobile-api");
							}else if ($get_tab == 'favorites' || $get_tab == 'favorites-2') {
								$message = esc_html__("You must login to see your favorites.","mobile-api");
							}
							if (isset($message)) {
								$login_required = array("requiredMessage" => $message,"loginRequired" => true);
							}
						}
						if (($get_tab == 'feed' && isset($is_user_logged_in)) || ($get_tab == 'feed-2' && isset($is_user_logged_in)) || ($get_tab == 'poll-feed' && isset($is_user_logged_in)) || ($get_tab == 'poll-feed-2' && isset($is_user_logged_in))) {
							$show_sticky = true;
							$order_feed = "DESC";
							$home_feed = get_post_meta($home_page_id,mobile_api_theme_prefix."_home_feed",true);
							$user_sort = get_post_meta($home_page_id,mobile_api_theme_prefix."_user_sort_home_feed",true);
							$users_per = get_post_meta($home_page_id,mobile_api_theme_prefix."_users_per_home_feed",true);
							$cat_sort = get_post_meta($home_page_id,mobile_api_theme_prefix."_cat_sort_home_feed",true);
							$cat_per = get_post_meta($home_page_id,mobile_api_theme_prefix."_cat_per_home_feed",true);
							$tag_sort = get_post_meta($home_page_id,mobile_api_theme_prefix."_tag_sort_home_feed",true);
							$tag_per = get_post_meta($home_page_id,mobile_api_theme_prefix."_tag_per_home_feed",true);
							$number_of_users = get_post_meta($home_page_id,mobile_api_theme_prefix."_users_home_feed",true);
							$number_of_categories = get_post_meta($home_page_id,mobile_api_theme_prefix."_categories_home_feed",true);
							$number_of_tags = get_post_meta($home_page_id,mobile_api_theme_prefix."_tags_home_feed",true);
							$cat_sort = ($cat_sort == "followers"?"meta_value_num":$cat_sort);
							$tag_sort = ($tag_sort == "followers"?"meta_value_num":$tag_sort);
							$show_custom_error = true;
							$paged = mobile_api_paged();
							$count_posts_home = mobile_api_options('count_posts_home');
							$post_number = (isset($post_number) && $post_number != ""?$post_number:$count_posts_home);
							$post_number = (int)($post_number > 0?$post_number:get_option('posts_per_page'));
							$offset = ($paged -1) * $post_number;
							$specific_date_feed = (isset($specific_date) && $specific_date != "" && $specific_date != "all"?date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." -".$specific_date)):"");

							$block_users = mobile_api_options("block_users");
							$author__not_in = array();
							if ($block_users == mobile_api_checkbox_value) {
								if ($user_id > 0) {
									$get_block_users = get_user_meta($user_id,mobile_api_action_prefix()."_block_users",true);
								}
							}

							$following_me = get_user_meta($user_id,"following_me",true);
							if (is_array($following_me) && !empty($following_me) && isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)) {
								$following_me = array_diff($following_me,$get_block_users);
							}

							$user_cat_follow = get_user_meta($user_id,"user_cat_follow",true);
							$user_tag_follow = get_user_meta($user_id,"user_tag_follow",true);

							$user_following = (is_array($following_me) && !empty($following_me)?implode(",",$following_me):"");
							$user_cat_follow = apply_filters("mobile_api_user_cat_follow",$user_cat_follow);
							$cat_following = (is_array($user_cat_follow) && !empty($user_cat_follow)?implode(",",$user_cat_follow):"");
							$tag_following = (is_array($user_tag_follow) && !empty($user_tag_follow)?implode(",",$user_tag_follow):"");
							$all_following = ($cat_following != ""?$cat_following:"").($cat_following != "" && $tag_following != ""?",":"").($tag_following != ""?$tag_following:"");

							$user_following_if = ((isset($home_feed["users"]["value"]) && $home_feed["users"]["value"] === "0") || $number_of_users == 0 || ($number_of_users > 0 && is_array($following_me) && count($following_me) >= $number_of_users)?"yes":"no");
							$cat_following_if = ((isset($home_feed["cats"]["value"]) && $home_feed["cats"]["value"] === "0") || $number_of_categories == 0 || ($number_of_categories > 0 && is_array($user_cat_follow) && count($user_cat_follow) >= $number_of_categories)?"yes":"no");
							$tag_following_if = ((isset($home_feed["tags"]["value"]) && $home_feed["tags"]["value"] === "0") || $number_of_tags == 0 || ($number_of_tags > 0 && is_array($user_tag_follow) && count($user_tag_follow) >= $number_of_tags)?"yes":"no");

							$user_count_already = (is_array($following_me)?count($following_me):0);
							$cat_count_already = (is_array($user_cat_follow)?count($user_cat_follow):0);
							$tag_count_already = (is_array($user_tag_follow)?count($user_tag_follow):0);

							if ($user_following_if == "yes" && $cat_following_if == "yes" && $tag_following_if == "yes" && ($all_following != "" || $user_following != "")) {
								$get_block_users = get_user_meta($user_id,mobile_api_action_prefix()."_block_users",true);
								$user_following = (isset($user_following) && $user_following != ""?$user_following.",".$user_id:$user_id);
								$feed_updated = "COALESCE((SELECT MAX(comment_date) FROM $wpdb->comments wpc WHERE wpc.comment_post_id = $wpdb->posts.id),$wpdb->posts.post_date)";
								$order_by_updated = ($updated_answers == mobile_api_checkbox_value?$feed_updated:"$wpdb->posts.post_date");
								$is_poll_feed = ($get_tab == 'poll-feed' && is_user_logged_in()) || ($get_tab == 'poll-feed-2' && is_user_logged_in()?true:false);
								$custom_where = "(
									".($all_following != ""?"$wpdb->term_relationships.term_taxonomy_id IN (".$all_following.")".($user_following != ""?" OR":"")." ":"")."
									".($user_following != ""?"$wpdb->posts.post_author IN (".$user_following.") ":"")."
								)
								".(isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)?" AND $wpdb->posts.post_author NOT IN (".implode(",",$get_block_users).") ":"")."
								AND ( mt1.post_id IS NULL )
								".(isset($is_poll_feed) && $is_poll_feed == true?"AND ( mt2.meta_key = 'question_poll' AND mt2.meta_value = 'on')":"")."
								".($specific_date_feed != ""?"AND ( $wpdb->posts.post_date >= '".$specific_date_feed."' ) ":"")."
								AND $wpdb->posts.post_type = '".mobile_api_questions_type."'
								AND ($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private')";
								
								$total_query = $wpdb->get_var(
									$wpdb->prepare(
										"SELECT COUNT(*)
										from (
											SELECT DISTINCT $wpdb->posts.ID
											FROM $wpdb->posts
											".($all_following != ""?"LEFT JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) ":"")."
											LEFT JOIN $wpdb->postmeta AS mt1 ON ($wpdb->posts.ID = mt1.post_id AND mt1.meta_key = '%s' )
											".(isset($is_poll_feed) && $is_poll_feed == true?"LEFT JOIN $wpdb->postmeta AS mt2 ON ($wpdb->posts.ID = mt2.post_id )":"")."
											WHERE $custom_where
										) derived_count_table"
										,'user_id'
									)
								);
								
								$query_sql = $wpdb->prepare(
									"SELECT DISTINCT $wpdb->posts.*
									FROM $wpdb->posts
									".($all_following != ""?"LEFT JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) ":"")."
									LEFT JOIN $wpdb->postmeta AS mt1 ON ($wpdb->posts.ID = mt1.post_id AND mt1.meta_key = 'user_id' )
									".(isset($is_poll_feed) && $is_poll_feed == true?"LEFT JOIN $wpdb->postmeta AS mt2 ON ($wpdb->posts.ID = mt2.post_id )":"")."
									WHERE %s=1
									AND $custom_where
									ORDER BY $order_by_updated DESC
									LIMIT $post_number OFFSET $offset"
									,1
								);
								$feed_query = $wpdb->get_results($query_sql);
								if (is_array($feed_query) && !empty($feed_query)) :
									$output = array();
									foreach ($feed_query as $post) {
										$new_post = new MOBILE_API_Post($post);
										$output[] = $new_post;
									}
									$posts = $output;
								endif;
								$count = $rows_per_page;
								$max_num_pages = (isset($total_query)?ceil($total_query/$post_number):0);
								$found_posts = (int)(isset($total_query)?$total_query:0);
							}else {
								if (!isset($is_user_logged_in)) {
									$query = array('post_type' => mobile_api_questions_type);
									if ($updated_answers == mobile_api_checkbox_value) {
										$show_loop_updated = true;
										$custom_sql = true;
									}
								}else {
									$show_custom_following = true;

									if (isset($home_feed) && is_array($home_feed) && !empty($home_feed)) {
										foreach ($home_feed as $key => $value) {
											if ($key == "users" && isset($home_feed["users"]["value"]) && $home_feed["users"]["value"] == "users") {
												$role__not_in = array('role__not_in' => array("wpqa_under_review","activation","ban_group"));
												$args         = array_merge($role__not_in,array(
																'number'     => $users_per,
																'orderby'    => 'registered',
																'exclude'    => $user_id
															)
												);
												$query        = new WP_User_Query($args);
												foreach ($query->get_results() as $user) {
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
													$second_info = (($user_id > 0 && $user_id != $user->ID)?array("followed" => (!empty($following_you) && in_array($user_id,$following_you)?true:false)):array());
													$badge_color = mobile_api_get_badge($user->ID,"color",(isset($first_points)?$first_points:""));
													$users_array[] = array_merge(array(
															"id"          => $user->ID,
															"displayname" => $user->display_name,
															"avatar"      => mobile_api_user_avatar_link(array("user_id" => $user->ID,"size" => 128)),
															"verified"    => ($verified_user == 1 || $verified_user == "on"?true:false),
															"badge"       => array("name" => strip_tags(mobile_api_get_badge($user->ID,"name",(isset($first_points)?$first_points:""))),"color" => ($badge_color != ""?$badge_color:""),"textColor" => "FFFFFF","textColorDark" => "FFFFFF"),
													),$second_info);
												}
												$following["users"]["fetch"] = $users_array;
												$following["users"]["how"] = (($number_of_users-$user_count_already) > 0 && ($number_of_users > 0 || $user_following_if != "yes")?sprintf(esc_html__("Choose %s or more users to continue.","mobile-api"),($number_of_users-$user_count_already)):"");
												$following["users"]["more"] = true;
											}else if ($key == "cats" && isset($home_feed["cats"]["value"]) && $home_feed["cats"]["value"] == "cats") {
												$cats_tax = mobile_api_question_categories;
												$meta_query = ($tag_sort == "meta_value_num"?array('meta_query' => array("relation" => "or",array("key" => "cat_follow_count","compare" => "NOT EXISTS"),array("key" => "cat_follow_count","value" => 0,"compare" => ">="))):array());
												$exclude = apply_filters('wpqa_exclude_question_category',array());
												$terms = get_terms($cats_tax,array_merge($exclude,$meta_query,array(
													'orderby'    => $cat_sort,
													'order'      => $order_feed,
													'number'     => $cat_per,
													'hide_empty' => 0,
												)));

												$following["cats"]["fetch"] = array_map(array(&$this,'get_catobject'),$terms);
												$following["cats"]["how"] = (($number_of_categories-$cat_count_already) > 0 && ($number_of_categories > 0 || $cat_following_if != "yes")?sprintf(esc_html__("Choose %s or more categories to continue.","mobile-api"),($number_of_categories-$cat_count_already)):"");
												$following["cats"]["more"] = true;
											}else if ($key == "tags" && isset($home_feed["tags"]["value"]) && $home_feed["tags"]["value"] == "tags") {
												$tags_tax = mobile_api_question_tags;
												$meta_query = ($tag_sort == "meta_value_num"?array('meta_query' => array("relation" => "or",array("key" => "tag_follow_count","compare" => "NOT EXISTS"),array("key" => "tag_follow_count","value" => 0,"compare" => ">="))):array());
												$exclude = apply_filters('wpqa_exclude_question_category',array());
												$terms = get_terms($tags_tax,array_merge($exclude,$meta_query,array(
													'orderby'    => $tag_sort,
													'order'      => $order_feed,
													'number'     => $tag_per,
													'hide_empty' => 0,
												)));

												$following["tags"]["fetch"] = array_map(array(&$this,'get_tagobject'),$terms);
												$following["tags"]["how"] = (($number_of_tags-$tag_count_already) > 0 && ($number_of_tags > 0 || $tag_following_if != "yes")?sprintf(esc_html__("Choose %s or more tags to continue.","mobile-api"),($number_of_tags-$tag_count_already)):"");
												$following["tags"]["more"] = true;
											}
										}
									}

									if (!isset($following["users"])) {
										$following["users"]["fetch"] = array();
									}

									if (!isset($following["cats"])) {
										$following["cats"]["fetch"] = array();
									}

									if (!isset($following["tags"])) {
										$following["tags"]["fetch"] = array();
									}

									$following["complete"] = ($user_following_if == "yes" && $cat_following_if == "yes" && $tag_following_if == "yes" && ($all_following != "" || $user_following != "")?true:false);
									$following["page_id"] = $home_page_id;
									$following["custom_layout"] = "true";
									return $following;
								}
							}
						}else if (($get_tab == 'feed' && !isset($is_user_logged_in)) || $get_tab == 'recent-questions' || ($get_tab == 'feed-2' && !isset($is_user_logged_in)) || ($get_tab == 'poll-feed' && !isset($is_user_logged_in)) || ($get_tab == 'poll-feed-2' && !isset($is_user_logged_in)) || $get_tab == 'recent-questions-2') {
							$show_sticky = true;
							$main_query = array('post_type' => mobile_api_questions_type);
							$query = $custom_args = array_merge((isset($cats_post) && is_array($cats_post)?$cats_post:array()),$main_query,$specific_date_array);
							if ($updated_answers == mobile_api_checkbox_value) {
								$is_poll_feed = ($get_tab == 'poll-feed' && !isset($is_user_logged_in)) || ($get_tab == 'poll-feed-2' && !isset($is_user_logged_in)?true:false);
								$show_loop_updated = true;
								$custom_sql = true;
							}
						}else if (isset($is_user_logged_in) && ($get_tab == 'questions-for-you' || $get_tab == 'questions-for-you-2')) {
							$category_list = get_user_meta($user_id,"wpqa_for_you_cats",true);
							$tag_list = get_user_meta($user_id,"wpqa_for_you_tags",true);
							$cats_post = array('tax_query' => array('relation' => 'OR',array('taxonomy' => mobile_api_question_categories,'field' => 'id','terms' => $category_list,'operator' => 'IN'),array('taxonomy' => mobile_api_question_tags,'field' => 'id','terms' => $tag_list,'operator' => 'IN')));
							$query = $custom_args = array_merge($cats_post,$specific_date_array,array("post_type" => mobile_api_questions_type));
							$show_sticky = true;
							if ($updated_answers == mobile_api_checkbox_value) {
								$category_list_updated = (is_array($category_list) && !empty($category_list)?implode(",",$category_list):"");
								$tag_list_updated = (is_array($tag_list) && !empty($tag_list)?implode(",",$tag_list):"");
								$all_tax_updated = ($category_list_updated != ""?$category_list_updated:"").($category_list_updated != "" && $tag_list_updated != ""?",":"").($tag_list_updated != ""?$tag_list_updated:"");
								$show_loop_updated = true;
								$custom_sql = true;
							}
						}else if ($get_tab == 'most-answers' || $get_tab == 'most-answers-2') {
							$main_query = array('post_type' => mobile_api_questions_type,'orderby' => 'comment_count','order' => 'DESC');
							$query = array_merge((isset($cats_post) && is_array($cats_post)?$cats_post:array()),$main_query,$specific_date_array);
						}else if ($get_tab == 'no-answers' || $get_tab == 'no-answers-2') {
							$main_query = array('post_type' => mobile_api_questions_type,'comment_count' => '0');
							$query = array_merge((isset($cats_post) && is_array($cats_post)?$cats_post:array()),$main_query,$specific_date_array);
						}else if ($get_tab == 'most-visit' || $get_tab == 'most-visit-2') {
							$post_meta_stats = mobile_api_options('post_meta_stats');
							$post_meta_stats = ($post_meta_stats != ''?$post_meta_stats:'post_stats');
							$main_query = array('post_type' => mobile_api_questions_type,'orderby' => array('question_visit_order' => 'DESC'),'meta_query' => array('question_visit_order' => array('type' => 'numeric','key' => $post_meta_stats,'value' => 0,'compare' => '>=')));
							$query = array_merge((isset($cats_post) && is_array($cats_post)?$cats_post:array()),$main_query,$specific_date_array);
						}else if ($get_tab == 'most-reacted' || $get_tab == 'most-reacted-2') {
							$main_query = array('post_type' => mobile_api_questions_type,'orderby' => array('question_reacted_order' => 'DESC'),'meta_query' => array('question_reacted_order' => array('type' => 'numeric','key' => 'wpqa_reactions_count','value' => 0,'compare' => '>=')));
							$query = array_merge((isset($cats_post) && is_array($cats_post)?$cats_post:array()),$main_query,$specific_date_array);
						}else if ($get_tab == 'most-vote' || $get_tab == 'most-vote-2') {
							$main_query = array('post_type' => mobile_api_questions_type,'orderby' => array('question_vote_order' => 'DESC'),'meta_query' => array('question_vote_order' => array('type' => 'numeric','key' => 'question_vote','value' => 0,'compare' => '>=')));
							$query = array_merge((isset($cats_post) && is_array($cats_post)?$cats_post:array()),$main_query,$specific_date_array);
						}else if ($get_tab == 'random' || $get_tab == 'random-2') {
							$main_query = array('post_type' => mobile_api_questions_type,'orderby' => 'rand');
							$query = array_merge((isset($cats_post) && is_array($cats_post)?$cats_post:array()),$main_query,$specific_date_array);
						}else if ($get_tab == 'new-questions' || $get_tab == 'new-questions-2') {
							$main_query = array('post_type' => mobile_api_questions_type);
							$query = array_merge((isset($cats_post) && is_array($cats_post)?$cats_post:array()),$main_query,$specific_date_array);
						}else if ($get_tab == 'sticky-questions' || $get_tab == 'sticky-questions-2') {
							$sticky_questions = get_option('sticky_questions');
							$main_query = array('post_type' => mobile_api_questions_type,'nopaging' => true,'post__in' => $sticky_questions,'meta_query' => array('key' => 'sticky','compare' => '=','value' => 1));
							$query = array_merge((isset($cats_post) && is_array($cats_post)?$cats_post:array()),$main_query,$specific_date_array);
						}else if ($get_tab == 'polls' || $get_tab == 'polls-2') {
							$show_sticky = true;
							$main_query = array('post_type' => mobile_api_questions_type,'ignore_sticky_posts' => 1,'meta_query' => array(array('key' => 'question_poll','value' => mobile_api_checkbox_value,'compare' => '=')));
							$query = array_merge((isset($cats_post) && is_array($cats_post)?$cats_post:array()),$main_query,$specific_date_array);
						}else if ($get_tab == 'followed' || $get_tab == 'followed-2') {
							if ($user_id > 0) {
								$following_questions_user = get_user_meta($user_id,"following_questions",true);
								if (is_array($following_questions_user) && !empty($following_questions_user) && count($following_questions_user) > 0) {
									$main_query = array('post_type' => mobile_api_questions_type,"post__in" => $following_questions_user);
									$query = array_merge($main_query,$specific_date_array);
								}else {
									$mobile_api->error(esc_html__("There are no questions yet.","mobile-api"));
								}
							}
						}else if ($get_tab == 'favorites' || $get_tab == 'favorites-2') {
							if ($user_id > 0) {
								if (has_askme()) {
									$user_login_id = get_user_by("id",$user_id);
									$_favorites = get_user_meta($user_id,$user_login_id->user_login."_favorites",true);
								}else {
									$_favorites = get_user_meta($user_id,$user_id."_favorites",true);
								}
								if (is_array($_favorites) && !empty($_favorites) && count($_favorites) > 0) {
									$main_query = array('post_type' => mobile_api_questions_type,"post__in" => $_favorites);
									$query = array_merge($main_query,$specific_date_array);
								}else {
									$mobile_api->error(esc_html__("There are no questions yet.","mobile-api"));
								}
							}
						}else if ($question_bump == mobile_api_checkbox_value && ($active_points == "on" || $active_points == 1) && ($get_tab == 'question-bump' || $get_tab == 'question-bump-2')) {
							$main_query = array('post_type' => mobile_api_questions_type,'comment_count' => '0','orderby' => array('question_points_order' => $order_post),'meta_query' => array('question_points_order' => array('type' => 'numeric','key' => 'question_points','value' => 0,'compare' => '>=')));
							$query = array_merge((isset($cats_post) && is_array($cats_post)?$cats_post:array()),$main_query,$specific_date_array);
						}else if ($get_tab == 'recent-posts' || $get_tab == 'recent-posts-2') {
							$main_query = array('post_type' => 'post');
							$query = array_merge($main_query,$specific_date_array);
						}else if ($get_tab == 'posts-visited' || $get_tab == 'posts-visited-2') {
							$post_meta_stats = mobile_api_options('post_meta_stats');
							$post_meta_stats = ($post_meta_stats != ''?$post_meta_stats:'post_stats');
							$main_query = array('post_type' => 'post','orderby' => array('post_visit_order' => 'DESC'),'meta_query' => array('post_visit_order' => array('type' => 'numeric','key' => $post_meta_stats,'value' => 0,'compare' => '>=')));
							$query = array_merge($main_query,$specific_date_array);
						}

						$sticky_questions = apply_filters("mobile_api_sticky_questions",get_option('sticky_questions'));
						if (isset($show_sticky) && $show_sticky == true) {
							$sticky_posts = $this->get_sticky_posts($sticky_questions,$user_id,$get_tab,(isset($custom_args) && is_array($custom_args)?$custom_args:array()));
						}
						$sticky_posts = (isset($sticky_posts) && is_array($sticky_posts) && !empty($sticky_posts)?$sticky_posts:array());

						if ($updated_answers == mobile_api_checkbox_value && isset($show_loop_updated)) {
							$paged = mobile_api_paged();
							$count_posts_home = mobile_api_options('count_posts_home');
							$post_number = (isset($post_number) && $post_number != ""?$post_number:$count_posts_home);
							$post_number = (int)($post_number > 0?$post_number:get_option('posts_per_page'));
							$offset = ($paged -1) * $post_number;
							$sticky_questions = (is_array($sticky_questions) && !empty($sticky_questions)?"AND $wpdb->posts.ID NOT IN (".implode(",",$sticky_questions).")":"");
							if (!isset($get_block_users)) {
								$get_block_users = get_user_meta($user_id,mobile_api_action_prefix()."_block_users",true);
							}
							$blocked_users = (isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)?"AND $wpdb->posts.post_author NOT IN (".implode(",",$get_block_users).")":"");
							$specific_date = (isset($specific_date) && $specific_date != "" && $specific_date != "all"?$specific_date.' ago':"");
							$date = ($specific_date != ""?"AND ($wpdb->posts.post_date > '".date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s"). $specific_date))."')":"");
							$feed_updated = "COALESCE((SELECT MAX(comment_date) FROM $wpdb->comments wpc WHERE wpc.comment_post_id = $wpdb->posts.id),$wpdb->posts.post_date)";
							$custom_where = "AND ( mt1.post_id IS NULL )
							".(isset($is_poll_feed) && $is_poll_feed == true?"AND ( mt2.meta_key = 'question_poll' AND mt2.meta_value = 'on')":"")."
							AND $wpdb->posts.post_type = '".mobile_api_questions_type."'
							AND ($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private')";
							
							$total_query = $wpdb->get_var(
								$wpdb->prepare(
									"SELECT COUNT(*)
									from (
										SELECT DISTINCT $wpdb->posts.ID
										FROM $wpdb->posts
										$custom_catagories_where
										LEFT JOIN $wpdb->postmeta AS mt1 ON ($wpdb->posts.ID = mt1.post_id AND mt1.meta_key = '%s' )
										".(isset($is_poll_feed) && $is_poll_feed == true?"LEFT JOIN $wpdb->postmeta AS mt2 ON ($wpdb->posts.ID = mt2.post_id )":"")."
										WHERE '1'=1
										$custom_catagories_updated $sticky_questions $blocked_users $date $custom_where
									) derived_count_table"
									,'user_id'
								)
							);

							$query_sql = $wpdb->prepare(
								"SELECT DISTINCT $wpdb->posts.*
								FROM $wpdb->posts
								$custom_catagories_where
								LEFT JOIN $wpdb->postmeta AS mt1 ON ($wpdb->posts.ID = mt1.post_id AND mt1.meta_key = 'user_id' )
								".(isset($is_poll_feed) && $is_poll_feed == true?"LEFT JOIN $wpdb->postmeta AS mt2 ON ($wpdb->posts.ID = mt2.post_id )":"")."
								WHERE %s=1
								$custom_catagories_updated $sticky_questions $blocked_users $date $custom_where
								ORDER BY $feed_updated $order_post
								LIMIT $post_number OFFSET $offset"
								,1
							);

							$feed_query = $wpdb->get_results($query_sql);
							if (is_array($feed_query) && !empty($feed_query)) :
								$output = array();
								foreach ($feed_query as $post) {
									$new_post = new MOBILE_API_Post($post);
									$output[] = $new_post;
								}
								$posts = $output;
							endif;
							$count = $rows_per_page;
							$max_num_pages = (isset($total_query)?ceil($total_query/$post_number):0);
							$found_posts = (int)(isset($total_query)?$total_query:0);
						}else if (isset($query)) {
							$array_data = $this->not_include_sticky($sticky_posts,$sticky_questions,$user_id);
							$query = array_merge($query,$array_data);
							$result = $mobile_api->introspector->get_posts($query,"counts");
							$count = $rows_per_page;
							$found_posts = (int)$result->found_posts;
							$max_num_pages = ($count > 0?ceil($found_posts/$count):0);
							$posts = $result->posts;
						}
						$posts = array_merge($sticky_posts,$posts);
						if (!isset($show_custom_following)) {
							return array_merge($login_required,array(
								'count'       => $count,
								'count_total' => $found_posts,
								'pages'       => $max_num_pages,
								'posts'       => $posts,
							));
						}
					}
				}else {
					$mobile_api->error(esc_html__("This tab is not available.","mobile-api"));
				}
			}else {
				$mobile_api->error(esc_html__("No homepage template in this site.","mobile-api"));
			}
		}else {
			$mobile_api->error(esc_html__("Include get_tab var in your request.","mobile-api"));
		}
	}

	public function complete_following() {
		global $mobile_api;
		$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
		if (!$auth) {
			$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
		}
		if (!$auth) {
			$mobile_api->error(esc_html__('Authorization header not found.','mobile-api'));
		}
		$page_id = esc_attr(isset($_POST['page_id']) && $_POST['page_id']?$_POST['page_id']:'');
		if ($page_id == '') {
			$mobile_api->error(esc_html__("Include page_id var in your request.","mobile-api"));
		}
		$user_id = get_current_user_id();

		$block_users = mobile_api_options("block_users");
		$author__not_in = array();
		if ($block_users == mobile_api_checkbox_value) {
			if ($user_id > 0) {
				$get_block_users = get_user_meta($user_id,mobile_api_action_prefix()."_block_users",true);
			}
		}

		$following_me = get_user_meta($user_id,"following_me",true);
		if (is_array($following_me) && !empty($following_me) && isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)) {
			$following_me = array_diff($following_me,$get_block_users);
		}

		$user_cat_follow = get_user_meta($user_id,"user_cat_follow",true);
		$user_tag_follow = get_user_meta($user_id,"user_tag_follow",true);

		$user_following = (is_array($following_me) && !empty($following_me)?implode(",",$following_me):"");
		$user_cat_follow = apply_filters("mobile_api_user_cat_follow",$user_cat_follow);
		$cat_following = (is_array($user_cat_follow) && !empty($user_cat_follow)?implode(",",$user_cat_follow):"");
		$tag_following = (is_array($user_tag_follow) && !empty($user_tag_follow)?implode(",",$user_tag_follow):"");
		$all_following = ($cat_following != ""?$cat_following:"").($cat_following != "" && $tag_following != ""?",":"").($tag_following != ""?$tag_following:"");

		$home_feed = get_post_meta($page_id,mobile_api_theme_prefix."_home_feed",true);
		$number_of_users = get_post_meta($page_id,mobile_api_theme_prefix."_users_home_feed",true);
		$number_of_categories = get_post_meta($page_id,mobile_api_theme_prefix."_categories_home_feed",true);
		$number_of_tags = get_post_meta($page_id,mobile_api_theme_prefix."_tags_home_feed",true);

		$user_following_if = ((isset($home_feed["users"]["value"]) && $home_feed["users"]["value"] === "0") || $number_of_users == 0 || ($number_of_users > 0 && is_array($following_me) && count($following_me) >= $number_of_users)?"yes":"no");
		$cat_following_if = ((isset($home_feed["cats"]["value"]) && $home_feed["cats"]["value"] === "0") || $number_of_categories == 0 || ($number_of_categories > 0 && is_array($user_cat_follow) && count($user_cat_follow) >= $number_of_categories)?"yes":"no");
		$tag_following_if = ((isset($home_feed["tags"]["value"]) && $home_feed["tags"]["value"] === "0") || $number_of_tags == 0 || ($number_of_tags > 0 && is_array($user_tag_follow) && count($user_tag_follow) >= $number_of_tags)?"yes":"no");
		return ($user_following_if == "yes" && $cat_following_if == "yes" && $tag_following_if == "yes" && ($all_following != "" || $user_following != "")?array("status" => true):array("status" => false));
	}

	public function get_sticky_posts($sticky_posts,$user_id = 0,$get_tab = '',$custom_args = array(),$post_type = mobile_api_questions_type,$group_id = 0) {
		global $mobile_api;
		$paged = mobile_api_paged();
		if (isset($sticky_posts) && is_array($sticky_posts) && !empty($sticky_posts) && $paged == 1) {
			if (isset($custom_args) && is_array($custom_args) && !empty($custom_args)) {
				$custom_args = $custom_args;
			}else {
				$custom_args = array();
			}

			$block_users = mobile_api_options("block_users");
			$author__not_in = array();
			if ($block_users == mobile_api_checkbox_value) {
				if ($user_id > 0) {
					$get_block_users = get_user_meta($user_id,mobile_api_action_prefix()."_block_users",true);
					if (is_array($get_block_users) && !empty($get_block_users)) {
						$author__not_in = array("author__not_in" => $get_block_users);
					}
				}
			}
			
			$query_sticky_meta = array("key" => "sticky","compare" => "=","value" => 1);
			$mobile_api_show_sticky = apply_filters('mobile_api_show_sticky',false);
			
			if ($mobile_api_show_sticky == true || $get_tab == 'polls' || $get_tab == 'polls-2') {
				$query_sticky = array_merge($custom_args,$author__not_in,array("nopaging" => true,"post_type" => $post_type,"post__in" => $sticky_posts,"meta_query" => array(array('relation' => 'AND',array("key" => "question_poll","value" => "on","compare" => "LIKE"),$query_sticky_meta))));
				$mobile_api_show_sticky = apply_filters('mobile_api_sticky_args',$query_sticky,$custom_args,$sticky_posts,$query_sticky_meta);
			}else {
				$query_sticky = array_merge($custom_args,$author__not_in,array("nopaging" => true,"post_type" => $post_type,"post__in" => $sticky_posts,"meta_query" => $query_sticky_meta));
			}
		}
		if (isset($query_sticky) && is_array($query_sticky) && !empty($query_sticky)) {
			$last_sticky_posts = $mobile_api->introspector->get_posts($query_sticky);
		}
		return (isset($last_sticky_posts) && is_array($last_sticky_posts) && !empty($last_sticky_posts)?$last_sticky_posts:array());
	}

	public function not_include_sticky($sticky_posts,$sticky_questions,$user_id) {
		$sticky_posts = (isset($sticky_posts) && is_array($sticky_posts) && !empty($sticky_posts)?$sticky_posts:array());
		if (isset($sticky_questions) && is_array($sticky_questions) && !empty($sticky_questions)) {
			$block_users = mobile_api_options("block_users");
			$author__not_in = array();
			if ($block_users == mobile_api_checkbox_value) {
				if ($user_id > 0) {
					$get_block_users = get_user_meta($user_id,mobile_api_action_prefix()."_block_users",true);
					if (is_array($get_block_users) && !empty($get_block_users)) {
						$author__not_in = array("author__not_in" => $get_block_users);
					}
				}
			}
			$post__not_in = array("post__not_in" => $sticky_questions);
			$array_data = array_merge($author__not_in,$post__not_in,(is_array($array_data)?$array_data:array()));
		}
		return (isset($array_data) && is_array($array_data) && !empty($array_data)?$array_data:array());
	}

	public function get_recent_posts() {
		global $mobile_api;
		if ($mobile_api->query->post_type == "posts") {
			$mobile_api->error(esc_html__("Sorry, this is a available.","mobile-api"));
		}
		if (is_user_logged_in()) {
			$is_user_logged_in = true;
		}
		if (isset($mobile_api->query->feed) && $mobile_api->query->feed > 0 && isset($is_user_logged_in)) {
			$user_id = get_current_user_id();
			$feed_page = $mobile_api->query->feed;
			$orderby_question_q = get_post_meta($feed_page,mobile_api_theme_prefix."_orderby_question_q",true);
			if ($orderby_question_q == "feed") {
				$show_sticky = true;
				global $wpdb;
				$order_feed = "DESC";
				$page_feed = get_post_meta($feed_page,mobile_api_theme_prefix."_feed",true);
				$user_sort = get_post_meta($feed_page,mobile_api_theme_prefix."_user_sort_feed",true);
				$users_per = get_post_meta($feed_page,mobile_api_theme_prefix."_users_per_feed",true);
				$cat_sort = get_post_meta($feed_page,mobile_api_theme_prefix."_cat_sort_feed",true);
				$cat_per = get_post_meta($feed_page,mobile_api_theme_prefix."_cat_per_feed",true);
				$tag_sort = get_post_meta($feed_page,mobile_api_theme_prefix."_tag_sort_feed",true);
				$tag_per = get_post_meta($feed_page,mobile_api_theme_prefix."_tag_per_feed",true);
				$number_of_users = get_post_meta($feed_page,mobile_api_theme_prefix."_users_feed",true);
				$number_of_categories = get_post_meta($feed_page,mobile_api_theme_prefix."_categories_feed",true);
				$number_of_tags = get_post_meta($feed_page,mobile_api_theme_prefix."_tags_feed",true);
				$cat_sort = ($cat_sort == "followers"?"meta_value_num":$cat_sort);
				$tag_sort = ($tag_sort == "followers"?"meta_value_num":$tag_sort);
				$show_custom_error = true;
				$paged = mobile_api_paged();
				$count_posts_home = mobile_api_options('count_posts_home');
				$post_number = (isset($post_number) && $post_number != ""?$post_number:$count_posts_home);
				$post_number = (int)($post_number > 0?$post_number:get_option('posts_per_page'));
				$offset = ($paged -1) * $post_number;

				$block_users = mobile_api_options("block_users");
				$author__not_in = array();
				if ($block_users == mobile_api_checkbox_value) {
					if ($user_id > 0) {
						$get_block_users = get_user_meta($user_id,mobile_api_action_prefix()."_block_users",true);
					}
				}

				$following_me = get_user_meta($user_id,"following_me",true);
				if (is_array($following_me) && !empty($following_me) && isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)) {
					$following_me = array_diff($following_me,$get_block_users);
				}

				$user_cat_follow = get_user_meta($user_id,"user_cat_follow",true);
				$user_tag_follow = get_user_meta($user_id,"user_tag_follow",true);

				$user_following = (is_array($following_me) && !empty($following_me)?implode(",",$following_me):"");
				$user_cat_follow = apply_filters("mobile_api_user_cat_follow",$user_cat_follow);
				$cat_following = (is_array($user_cat_follow) && !empty($user_cat_follow)?implode(",",$user_cat_follow):"");
				$tag_following = (is_array($user_tag_follow) && !empty($user_tag_follow)?implode(",",$user_tag_follow):"");
				$all_following = ($cat_following != ""?$cat_following:"").($cat_following != "" && $tag_following != ""?",":"").($tag_following != ""?$tag_following:"");

				$user_following_if = ((isset($page_feed["users"]["value"]) && $page_feed["users"]["value"] === "0") || $number_of_users == 0 || ($number_of_users > 0 && is_array($following_me) && count($following_me) >= $number_of_users)?"yes":"no");
				$cat_following_if = ((isset($page_feed["cats"]["value"]) && $page_feed["cats"]["value"] === "0") || $number_of_categories == 0 || ($number_of_categories > 0 && is_array($user_cat_follow) && count($user_cat_follow) >= $number_of_categories)?"yes":"no");
				$tag_following_if = ((isset($page_feed["tags"]["value"]) && $page_feed["tags"]["value"] === "0") || $number_of_tags == 0 || ($number_of_tags > 0 && is_array($user_tag_follow) && count($user_tag_follow) >= $number_of_tags)?"yes":"no");

				$user_count_already = (is_array($following_me)?count($following_me):0);
				$cat_count_already = (is_array($user_cat_follow)?count($user_cat_follow):0);
				$tag_count_already = (is_array($user_tag_follow)?count($user_tag_follow):0);

				if ($user_following_if == "yes" && $cat_following_if == "yes" && $tag_following_if == "yes" && ($all_following != "" || $user_following != "")) {
					$updated_answers = mobile_api_options('updated_answers');
					$get_block_users = get_user_meta($user_id,mobile_api_action_prefix()."_block_users",true);
					$user_following = (isset($user_following) && $user_following != ""?$user_following.",".$user_id:$user_id);
					$feed_updated = "COALESCE((SELECT MAX(comment_date) FROM $wpdb->comments wpc WHERE wpc.comment_post_id = $wpdb->posts.id),$wpdb->posts.post_date)";
					$order_by_updated = ($updated_answers == mobile_api_checkbox_value?$feed_updated:"$wpdb->posts.post_date");
					$custom_where = "(
						".($all_following != ""?"$wpdb->term_relationships.term_taxonomy_id IN (".$all_following.")".($user_following != ""?" OR":"")." ":"")."
						".($user_following != ""?"$wpdb->posts.post_author IN (".$user_following.") ":"")."
					)
					".(isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)?" AND $wpdb->posts.post_author NOT IN (".implode(",",$get_block_users).") ":"")."
					AND ( mt1.post_id IS NULL )
					AND $wpdb->posts.post_type = '".mobile_api_questions_type."'
					AND ($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private')";
					
					$total_query = $wpdb->get_var(
						$wpdb->prepare(
							"SELECT COUNT(*)
							from (
								SELECT DISTINCT $wpdb->posts.ID
								FROM $wpdb->posts
								".($all_following != ""?"LEFT JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) ":"")."
								LEFT JOIN $wpdb->postmeta AS mt1 ON ($wpdb->posts.ID = mt1.post_id AND mt1.meta_key = '%s' )
								WHERE $custom_where
							) derived_count_table"
							,'user_id'
						)
					);
					
					$query_sql = $wpdb->prepare(
						"SELECT DISTINCT $wpdb->posts.*
						FROM $wpdb->posts
						".($all_following != ""?"LEFT JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) ":"")."
						LEFT JOIN $wpdb->postmeta AS mt1 ON ($wpdb->posts.ID = mt1.post_id AND mt1.meta_key = 'user_id' )
						WHERE %s=1
						AND $custom_where
						ORDER BY $order_by_updated DESC
						LIMIT $post_number OFFSET $offset"
						,1
					);

					$feed_query = $wpdb->get_results($query_sql);
					if (is_array($feed_query) && !empty($feed_query)) :
						$output = array();
						foreach ($feed_query as $post) {
							$new_post = new MOBILE_API_Post($post);
							$output[] = $new_post;
						}
						$posts = $output;
					endif;
					$count = $rows_per_page;
					$max_num_pages = (isset($total_query)?ceil($total_query/$post_number):0);
					$found_posts = (int)(isset($total_query)?$total_query:0);
					return array(
						'count'       => $count,
						'count_total' => $found_posts,
						'pages'       => $max_num_pages,
						'posts'       => $posts,
					);
				}else {
					if (isset($is_user_logged_in)) {
						$show_custom_following = true;

						if (isset($page_feed) && is_array($page_feed) && !empty($page_feed)) {
							foreach ($page_feed as $key => $value) {
								if ($key == "users" && isset($page_feed["users"]["value"]) && $page_feed["users"]["value"] == "users") {
									$role__not_in = array('role__not_in' => array("wpqa_under_review","activation","ban_group"));
									$args         = array_merge($role__not_in,array(
													'number'     => $users_per,
													'orderby'    => 'registered',
													'exclude'    => $user_id
												)
									);
									$query        = new WP_User_Query($args);
									foreach ($query->get_results() as $user) {
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
										$second_info = (($user_id > 0 && $user_id != $user->ID)?array("followed" => (!empty($following_you) && in_array($user_id,$following_you)?true:false)):array());
										$badge_color = mobile_api_get_badge($user->ID,"color",(isset($first_points)?$first_points:""));
										$users_array[] = array_merge(array(
												"id"          => $user->ID,
												"displayname" => $user->display_name,
												"avatar"      => mobile_api_user_avatar_link(array("user_id" => $user->ID,"size" => 128)),
												"verified"    => ($verified_user == 1 || $verified_user == "on"?true:false),
												"badge"       => array("name" => strip_tags(mobile_api_get_badge($user->ID,"name",(isset($first_points)?$first_points:""))),"color" => ($badge_color != ""?$badge_color:""),"textColor" => "FFFFFF","textColorDark" => "FFFFFF"),
										),$second_info);
									}
									$following["users"]["fetch"] = $users_array;
									$following["users"]["how"] = (($number_of_users-$user_count_already) > 0 && ($number_of_users > 0 || $user_following_if != "yes")?sprintf(esc_html__("Choose %s or more users to continue.","mobile-api"),($number_of_users-$user_count_already)):"");
									$following["users"]["more"] = true;
								}else if ($key == "cats" && isset($page_feed["cats"]["value"]) && $page_feed["cats"]["value"] == "cats") {
									$cats_tax = mobile_api_question_categories;
									$meta_query = ($tag_sort == "meta_value_num"?array('meta_query' => array("relation" => "or",array("key" => "cat_follow_count","compare" => "NOT EXISTS"),array("key" => "cat_follow_count","value" => 0,"compare" => ">="))):array());
									$exclude = apply_filters('wpqa_exclude_question_category',array());
									$terms = get_terms($cats_tax,array_merge($exclude,$meta_query,array(
										'orderby'    => $cat_sort,
										'order'      => $order_feed,
										'number'     => $cat_per,
										'hide_empty' => 0,
									)));

									$following["cats"]["fetch"] = array_map(array(&$this,'get_catobject'),$terms);
									$following["cats"]["how"] = (($number_of_categories-$cat_count_already) > 0 && ($number_of_categories > 0 || $cat_following_if != "yes")?sprintf(esc_html__("Choose %s or more categories to continue.","mobile-api"),($number_of_categories-$cat_count_already)):"");
									$following["cats"]["more"] = true;
								}else if ($key == "tags" && isset($page_feed["tags"]["value"]) && $page_feed["tags"]["value"] == "tags") {
									$tags_tax = mobile_api_question_tags;
									$meta_query = ($tag_sort == "meta_value_num"?array('meta_query' => array("relation" => "or",array("key" => "tag_follow_count","compare" => "NOT EXISTS"),array("key" => "tag_follow_count","value" => 0,"compare" => ">="))):array());
									$exclude = apply_filters('wpqa_exclude_question_category',array());
									$terms = get_terms($tags_tax,array_merge($exclude,$meta_query,array(
										'orderby'    => $tag_sort,
										'order'      => $order_feed,
										'number'     => $tag_per,
										'hide_empty' => 0,
									)));

									$following["tags"]["fetch"] = array_map(array(&$this,'get_tagobject'),$terms);
									$following["tags"]["how"] = (($number_of_tags-$tag_count_already) > 0 && ($number_of_tags > 0 || $tag_following_if != "yes")?sprintf(esc_html__("Choose %s or more tags to continue.","mobile-api"),($number_of_tags-$tag_count_already)):"");
									$following["tags"]["more"] = true;
								}
							}
						}
						
						if (!isset($following["users"])) {
							$following["users"]["fetch"] = array();
						}

						if (!isset($following["cats"])) {
							$following["cats"]["fetch"] = array();
						}

						if (!isset($following["tags"])) {
							$following["tags"]["fetch"] = array();
						}

						$following["complete"] = ($user_following_if == "yes" && $cat_following_if == "yes" && $tag_following_if == "yes" && ($all_following != "" || $user_following != "")?true:false);
						$following["page_id"] = $feed_page;
						$following["custom_layout"] = "true";
						return $following;
					}
				}
			}

			if (!isset($show_custom_following)) {
				return array_merge($login_required,array(
					'count'       => $count,
					'count_total' => $found_posts,
					'pages'       => $max_num_pages,
					'posts'       => $posts,
				));
			}
		}
		if ($mobile_api->query->post_type == mobile_api_questions_type || $mobile_api->query->post_type == mobile_api_asked_questions_type) {
			$user_id = get_current_user_id();
			$sticky_questions = apply_filters("mobile_api_sticky_questions",get_option('sticky_questions'));
			$sticky_posts = $this->get_sticky_posts($sticky_questions,$user_id);
			global $wp_query;
			$query = array();

			$query = array_merge($query, $wp_query->query);

			if ($mobile_api->query->page) {
				$query['paged'] = $mobile_api->query->page;
			}

			if (isset($query['count']) && $query['count'] != "") {
				$query['posts_per_page'] = (int)$query['count'];
			}else if ($mobile_api->query->count && !isset($query['posts_per_page'])) {
				$query['posts_per_page'] = $mobile_api->query->count;
			}

			if ($mobile_api->query->post_type) {
				$query['post_type'] = $mobile_api->query->post_type;
			}
			if ((isset($query["post_type"]) && ($query["post_type"] == mobile_api_questions_type || $query["post_type"] == mobile_api_asked_questions_type)) || (isset($query["tax_query"]) && ($query["tax_query"] == mobile_api_question_categories || $query["tax_query"] == mobile_api_question_tags))) {
				$query["meta_query"] = (isset($query["meta_query"])?$query["meta_query"]:array());
				$query["meta_query"] = $query["meta_query"];
			}

			$block_users = mobile_api_options("block_users");
			if ($block_users == mobile_api_checkbox_value) {
				if ($user_id > 0) {
					$get_block_users = get_user_meta($user_id,mobile_api_action_prefix()."_block_users",true);
					if (is_array($get_block_users) && !empty($get_block_users)) {
						if (isset($query["author"]) && $query["author"] != "" && in_array($query["author"],$get_block_users)) {
							$get_block_users = mobile_api_remove_item_by_value($get_block_users,$query["author"]);
						}else {
							$query["author__not_in"] = $get_block_users;
						}
					}
				}
			}

			if (isset($query["json"])) {
				unset($query["json"]);
			}

			if (isset($query["tax_query"]) && isset($query["taxonomy"])) {
				unset($query["taxonomy"]);
			}
		}
		$sticky_posts = (isset($sticky_posts) && is_array($sticky_posts) && !empty($sticky_posts)?$sticky_posts:array());
		$array_data = $this->not_include_sticky($sticky_posts,$sticky_questions,$user_id);
		$query = (is_array($query)?$query:array());
		$query = array_merge($query,$array_data);
		if ($mobile_api->query->post_type == mobile_api_questions_type || $mobile_api->query->post_type == mobile_api_asked_questions_type) {
			$get_posts = $mobile_api->introspector->get_posts($query);
		}else {
			$get_posts = $mobile_api->introspector->get_posts();
		}
		$posts = (isset($get_posts->posts)?$get_posts->posts:$get_posts);
		return $this->posts_result(array_merge($sticky_posts,$posts),count($posts));
	}

	public function get_group_posts() {
		global $mobile_api;
		$user_id = get_current_user_id();
		$group_id = (int)$mobile_api->query->group_id;
		if (mobile_api_groups() && $group_id > 0) {
			$group_permissions = array();
			$blocked_users = get_post_meta($group_id,"blocked_users_array",true);
			$is_super_admin = is_super_admin($user_id);
			if (!$is_super_admin && is_array($blocked_users) && in_array($user_id,$blocked_users)) {
				$mobile_api->error(esc_html__("Sorry, you blocked from this group.","mobile-api"));
			}
			$group_privacy = get_post_meta($group_id,"group_privacy",true);
			$group_users_array = get_post_meta($group_id,"group_users_array",true);
			if ($group_privacy == "public" || ($group_privacy == "private" && ($is_super_admin || (is_array($group_users_array) && in_array($user_id,$group_users_array))))) {
				//it's opening
			}else {
				$mobile_api->error(esc_html__("Sorry, this is a private group.","mobile-api"));
			}
			$sticky_group_posts = ($mobile_api->query->post_status != "draft"?apply_filters("mobile_api_sticky_group_posts",get_post_meta($group_id,"sticky_posts",true),$group_id):array());
			$sticky_posts = $this->get_sticky_posts($sticky_group_posts,$user_id,'',array("ignore_sticky_posts" => 1),'posts',$group_id);
			global $wp_query,$post;
			$query = array();

			$query = array_merge($query, $wp_query->query);

			if ($mobile_api->query->page) {
				unset($query["page"]);
				$query['paged'] = $mobile_api->query->page;
			}

			if (isset($query["json"])) {
				unset($query["json"]);
			}

			if (isset($query['count']) && $query['count'] != "") {
				$query['posts_per_page'] = (int)$query['count'];
			}else if ($mobile_api->query->count && !isset($query['posts_per_page'])) {
				$query['posts_per_page'] = $mobile_api->query->count;
			}

			$query['post_type'] = 'posts';
			$query['ignore_sticky_posts'] = 1;
			if ($mobile_api->query->post_status == "draft") {
				$group_moderators = get_post_meta($group_id,"group_moderators",true);
				$get_group = get_post($group_id);
				if ($is_super_admin || ($user_id > 0 && $get_group->post_author == $user_id) || ($user_id > 0 && is_array($group_moderators) && in_array($user_id,$group_moderators))) {
					$query['post_status'] = $mobile_api->query->post_status;
				}else {
					$mobile_api->error(esc_html__("Sorry, this is a private page.","mobile-api"));
				}
			}

			$query['meta_query'] = array(array("type" => "numeric","key" => "group_id","value" => $group_id,"compare" => "="));
			$sticky_posts = ($mobile_api->query->post_status != "draft" && isset($sticky_posts) && is_array($sticky_posts) && !empty($sticky_posts)?$sticky_posts:array());
			$array_data = $this->not_include_sticky($sticky_posts,$sticky_group_posts,$user_id);
			$query = array_merge($query,$array_data);
			$get_posts = new WP_Query($query);
			$output = array();
			if ($get_posts->have_posts()) {
				while ( $get_posts->have_posts() ) { $get_posts->the_post();
					if ($wp_posts) {
						$new_post = $post;
					} else {
						$new_post = new MOBILE_API_Post($post);
					}
					$output[] = $new_post;
				}
			}
			$posts = $output;
			return $this->posts_result(array_merge($sticky_posts,$posts),count($posts),$get_posts->found_posts);
		}else {
			return array(
				"count" => 0,
				"count_total" => 0,
				"pages" => 0,
				"posts" => array(),
			);
		}
	}
	
	public function get_posts() {
		global $mobile_api;
		if ($mobile_api->query->post_type == "posts") {
			$mobile_api->error(esc_html__("Sorry, this is a available.","mobile-api"));
		}
		$url = parse_url($_SERVER['REQUEST_URI']);
		$defaults = array(
			'ignore_sticky_posts' => true
		);
		$query = wp_parse_args($url['query']);
		unset($query['json']);
		unset($query['post_status']);
		$query = array_merge($defaults, $query);
		$posts = $mobile_api->introspector->get_posts($query,"counts");
		$result = $this->posts_result(isset($posts->posts)?$posts->posts:$posts);
		$result['query'] = $query;
		return $result;
	}
	
	public function get_post() {
		global $mobile_api, $post;
		$post = $mobile_api->introspector->get_current_post();
		if ($post) {
			$previous = get_adjacent_post(false, '', true);
			$next = get_adjacent_post(false, '', false);
			mobile_api_update_post_stats($post->ID);
			$response = array(
				'post' => new MOBILE_API_Post($post,'single',($mobile_api->query->edit == "edit"?"edit":""))
			);
			if ($previous) {
				$response['previous_url'] = get_permalink($previous->ID);
			}
			if ($next) {
				$response['next_url'] = get_permalink($next->ID);
			}
			return $response;
		} else {
			$mobile_api->error(esc_html__("Not found.","mobile-api"));
		}
	}
	
	public function check_post() {
		global $mobile_api, $post;
		$post = $mobile_api->introspector->get_current_post();
		if ($post->post_status == "publish") {
			return array("status" => true);
		} else {
			$mobile_api->error(esc_html__("Not found.","mobile-api"));
		}
	}

	public function check_now() {
		global $mobile_api;
		if (isset($mobile_api->query->status) && $mobile_api->query->status != "") {
			if ($mobile_api->query->status == "notNow") {
				update_option("mobile_check_now",$mobile_api->query->status);
			}else {
				delete_option("mobile_check_now");
			}
		}
		return "done";
	}

	public function get_page() {
		global $mobile_api;
		extract($mobile_api->query->get(array('id', 'slug', 'page_id', 'page_slug', 'children')));
		if ($id || $page_id) {
			if (!$id) {
				$id = $page_id;
			}
			$posts = $mobile_api->introspector->get_posts(array(
				'page_id' => $id
			));
		} else if ($slug || $page_slug) {
			if (!$slug) {
				$slug = $page_slug;
			}
			$posts = $mobile_api->introspector->get_posts(array(
				'pagename' => $slug
			));
		} else {
			$mobile_api->error(esc_html__("Include id or slug var in your request.","mobile-api"));
		}
		
		// Workaround for https://core.trac.wordpress.org/ticket/12647
		if (empty($posts)) {
			$url = $_SERVER['REQUEST_URI'];
			$parsed_url = parse_url($url);
			$path = $parsed_url['path'];
			if (preg_match('#^http://[^/]+(/.+)$#', get_bloginfo('url'), $matches)) {
				$blog_root = $matches[1];
				$path = preg_replace("#^$blog_root#", '', $path);
			}
			if (substr($path, 0, 1) == '/') {
				$path = substr($path, 1);
			}
			$posts = $mobile_api->introspector->get_posts(array('pagename' => $path));
		}
		
		if (count($posts) == 1) {
			if (!empty($children)) {
				$mobile_api->introspector->attach_child_posts($posts[0]);
			}
			return array(
				'page' => $posts[0]
			);
		} else {
			$mobile_api->error(esc_html__("Not found.","mobile-api"));
		}
	}
	
	public function get_date_posts() {
		global $mobile_api;
		if ($mobile_api->query->date) {
			$date = preg_replace('/\D/', '', $mobile_api->query->date);
			if (!preg_match('/^\d{4}(\d{2})?(\d{2})?$/', $date)) {
				$mobile_api->error(esc_html__("Specify a date var in one of YYYY or YYYY-MM or YYYY-MM-DD formats.","mobile-api"));
			}
			$request = array('year' => substr($date, 0, 4));
			if (strlen($date) > 4) {
				$request['monthnum'] = (int)substr($date, 4, 2);
			}
			if (strlen($date) > 6) {
				$request['day'] = (int)substr($date, 6, 2);
			}
			$posts = $mobile_api->introspector->get_posts($request,"counts");
		} else {
			$mobile_api->error(esc_html__("Include date var in your request.","mobile-api"));
		}
		return $this->posts_result(isset($posts->posts)?$posts->posts:$posts);
	}
	
	public function get_category_posts() {
		global $mobile_api;
		$category = $mobile_api->introspector->get_current_category();
		if (!$category) {
			$mobile_api->error(esc_html__("Not found.","mobile-api"));
		}
		$user_id = get_current_user_id();
		$numberposts = empty($mobile_api->query->count) ? -1 : $mobile_api->query->count;
		$args = array(
			"posts_per_page" => $numberposts,
			"tax_query" => array(array("taxonomy" => $category->taxonomy,"field" => "id","terms" => $category->term_id))
		);
		$sticky_questions = apply_filters("mobile_api_sticky_questions",get_option('sticky_questions'));
		$sticky_posts = $this->get_sticky_posts($sticky_questions,$user_id,'',array("tax_query" => array(array("taxonomy" => $category->taxonomy,"field" => "id","terms" => $category->term_id))));
		$sticky_posts = (isset($sticky_posts) && is_array($sticky_posts) && !empty($sticky_posts)?$sticky_posts:array());
		$array_data = $this->not_include_sticky($sticky_posts,$sticky_questions,$user_id);
		$args = array_merge($args,$array_data);
		$wp_query = $mobile_api->introspector->get_posts($args,"counts");
		$object_key = strtolower(substr(get_class($category),9));
		$count = (int)count($wp_query->posts);
		$count_total = (int)$wp_query->found_posts;

		$custom_permission = mobile_api_options("custom_permission");
		$show_ads = true;
		if ($custom_permission == mobile_api_checkbox_value) {
			if ($user_id > 0) {
				$roles = $user_info->allcaps;
			}
			if (($custom_permission == mobile_api_checkbox_value && $user_id > 0 && !is_super_admin($user_id) && empty($roles["without_ads"])) || ($custom_permission == mobile_api_checkbox_value && $user_id == 0)) {
				$show_ads = true;
			}else {
				$show_ads = false;
			}
		}
		$show_ads = apply_filters("mobile_api_show_ads",$show_ads);
		$ads = apply_filters("mobile_api_category_ads",array(),$category->term_id,$show_ads);
		$ads_merge = (isset($ads) && is_array($ads) && !empty($ads)?array('ads' => $ads):array());

		return array_merge($ads_merge,array(
			'count'       => $count,
			'count_total' => $count_total,
			'pages'       => ($count > 0?ceil($count_total/$count):0),
			$object_key   => $category,
			'posts'       => array_merge($sticky_posts,$wp_query->posts),
		));
	}
	
	public function get_tag_posts() {
		global $mobile_api;
		$tag = $mobile_api->introspector->get_current_tag();
		if (!$tag) {
			$mobile_api->error(esc_html__("Not found.","mobile-api"));
		}
		$user_id = get_current_user_id();
		$args = array(
			"tax_query" => array(array("taxonomy" => $tag->taxonomy,"field" => "id","terms" => $tag->term_id))
		);
		$sticky_questions = apply_filters("mobile_api_sticky_questions",get_option('sticky_questions'));
		$sticky_posts = $this->get_sticky_posts($sticky_questions,$user_id,'',array("tax_query" => array(array("taxonomy" => $tag->taxonomy,"field" => "id","terms" => $tag->term_id))));
		$sticky_posts = (isset($sticky_posts) && is_array($sticky_posts) && !empty($sticky_posts)?$sticky_posts:array());
		$array_data = $this->not_include_sticky($sticky_posts,$sticky_questions,$user_id);
		$args = array_merge($args,$array_data);
		$wp_query = $mobile_api->introspector->get_posts($args,"counts");
		$object_key = strtolower(substr(get_class($tag), 9));
		$count = (int)count($wp_query->posts);
		$count_total = (int)$wp_query->found_posts;
		return array(
			'count'       => $count,
			'count_total' => $count_total,
			'pages'       => ($count > 0?ceil($count_total/$count):0),
			$object_key   => $tag,
			'posts'       => array_merge($sticky_posts,$wp_query->posts),
		);
	}
	
	public function get_author_posts() {
		global $mobile_api;
		$author = $mobile_api->introspector->get_current_author();
		if (!$author) {
			$mobile_api->error(esc_html__("Not found.","mobile-api"));
		}
		$posts = $mobile_api->introspector->get_posts(array(
			'author' => $author->id
		),"counts");
		return $this->posts_with_object_result($posts, $author);
	}
	
	public function get_search_results() {
		global $mobile_api;
		$post_type = ($mobile_api->query->post_type?$mobile_api->query->post_type:mobile_api_questions_type);
		$search = $mobile_api->query->find;
		if ($search != "") {
			if ($post_type == "users") {
				$number = mobile_api_options("mobile_users_page");
				$following_users = mobile_api_options("mobile_users_roles_page");
				return $mobile_api->introspector->users($number,$following_users,$search);
			}else if ($post_type == "comments" || $post_type == "answers") {
				if ($post_type == "comments") {
					$type = array("post");
				}else {
					$type = array(mobile_api_questions_type);
				}
				return $mobile_api->introspector->get_recent_comments($type,$search);
			}else {
				$meta_query = array();
				if ($post_type == "question") {
					$asked_questions_search = mobile_api_options("asked_questions_search");
					$meta_query = ($asked_questions_search == mobile_api_checkbox_value?array("meta_query" => array("relation" => "or",array("key" => "user_id","compare" => "EXISTS"),array("key" => "question_private","compare" => "NOT EXISTS"),array("key" => "user_id","compare" => "NOT EXISTS"))):array("meta_query" => array(array("key" => "question_private","compare" => "NOT EXISTS"))));
					$post_type_array = ($asked_questions_search == mobile_api_checkbox_value?array(mobile_api_questions_type,mobile_api_asked_questions_type):array($post_type));
				}
				$posts = $mobile_api->introspector->get_posts(array_merge($meta_query,array(
					's' => $search,
					'post_type' => (isset($post_type_array)?$post_type_array:$post_type))
				),"counts");
				$count = (int)count($posts->posts);
				$count_total = (int)$posts->found_posts;
				return array(
					'count'       => $count,
					'count_total' => $count_total,
					'pages'       => ($count > 0?ceil($count_total/$count):0),
					'posts'       => $posts->posts,
				);
			}
		}else {
			$mobile_api->error(esc_html__("Include search var in your request.","mobile-api"));
		}
	}
	
	public function get_date_index() {
		global $mobile_api;
		$permalinks = $mobile_api->introspector->get_date_archive_permalinks();
		$tree = $mobile_api->introspector->get_date_archive_tree($permalinks);
		return array(
			'permalinks' => $permalinks,
			'tree' => $tree
		);
	}
	
	public function get_category_index() {
		global $mobile_api;
		$search = $mobile_api->query->find;
		$search_args = array();
		if ($search != "") {
			$search_args = array('search' => $search);
		}
		$args = array();
		if (!empty($mobile_api->query->parent)) {
			$args['parent'] = $mobile_api->query->parent;
		}
		if (!empty($mobile_api->query->taxonomy)) {
			$args['taxonomy'] = $mobile_api->query->taxonomy;
		}else {
			$args['taxonomy'] = 'category';
		}
		$args['hide_empty'] = false;

		$number_option = 4*get_option('posts_per_page',10);
		if ($mobile_api->query->type == mobile_api_questions_type || $mobile_api->query->type == mobile_api_asked_questions_type) {
			$number = mobile_api_options("mobile_question_categories");
		}else if ($mobile_api->query->type == "follow") {
			$number = mobile_api_options("mobile_api_following_categories");
		}else {
			$number = mobile_api_options("mobile_categories_page");
		}
		$number = ($number > 0?$number:$number_option);
		if ($mobile_api->query->type == mobile_api_questions_type || $mobile_api->query->type == mobile_api_asked_questions_type) {
			$cat_order = apply_filters('mobile_api_cat_order_ask_question',"DESC");// ASC
			$cat_sort  = apply_filters('mobile_api_cat_sort_ask_question',"count");// followers - name
			$number    = apply_filters('mobile_api_categories_per_page_ask_question',$number);
		}else if ($mobile_api->query->type == "follow") {
			$cat_order = apply_filters('mobile_api_cat_order_follow',"DESC");// ASC
			$cat_sort  = apply_filters('mobile_api_cat_sort_follow',"count");// followers - name
			$number    = apply_filters('mobile_api_categories_per_page_follow',$number);
		}else {
			$cat_order = apply_filters('mobile_api_cat_order',"DESC");// ASC
			$cat_sort  = apply_filters('mobile_api_cat_sort',"count");// followers - name
			$number    = apply_filters('mobile_api_categories_per_page',$number);
		}
		$number_query = ($number > 0?array('number' => $number):array());
		$paged        = ($mobile_api->query->paged != ""?(int)$mobile_api->query->paged:($mobile_api->query->page != ""?(int)$mobile_api->query->page:1));
		$offset       = ($paged-1)*$number;
		$cat_sort     = ($cat_sort == "followers"?"meta_value_num":$cat_sort);
		$meta_query   = ($cat_sort == "meta_value_num"?array('meta_query' => array("relation" => "or",array("key" => "cat_follow_count","compare" => "NOT EXISTS"),array("key" => "cat_follow_count","value" => 0,"compare" => ">="))):array());
		$meta_query   = apply_filters("mobile_api_meta_query_get_category_index",$meta_query);
		$parent_cats  = mobile_api_options("mobile_parent_categories");
		$parent = array('parent' => 0);
		if ($parent_cats == mobile_api_checkbox_value) {
			$parent = array();
		}
		do_action("mobile_api_category_index");
		if ($number > 0) {
			$cats = (array) get_terms($args['taxonomy'],array_merge($search_args,$parent,array('hide_empty' => $args['hide_empty'])));
		}
		$wp_cats = (array) get_terms($args['taxonomy'],array_merge($search_args,$parent,$meta_query,$number_query,array('orderby' => $cat_sort,'order' => $cat_order,'offset' => $offset,'hide_empty' => $args['hide_empty'])));
		$count_cats = ($number > 0 && isset($cats)?$cats:$wp_cats);
		return array(
			'total'      => count($count_cats),
			'pages'      => ($number > 0?ceil(count($count_cats)/$number):0),
			'count'      => count($wp_cats),
			'categories' => array_map(array(&$this,'get_catobject'),$wp_cats)
		);
	}

	public function get_catobject($wp_cat) {
		if (!$wp_cat) {
			return null;
		}
		return new MOBILE_API_Category($wp_cat);
	}
	
	public function get_tag_index() {
		global $mobile_api;
		$search = $mobile_api->query->find;
		$search_args = array();
		if ($search != "") {
			$search_args = array('search' => $search);
		}
		$args = array();
		if (!empty($mobile_api->query->taxonomy)) {
			$args['taxonomy'] = $mobile_api->query->taxonomy;
		}else {
			$args['taxonomy'] = 'post_tag';
		}
		$args['hide_empty'] = false;
		
		$number     = apply_filters('mobile_api_tags_per_page',2*get_option('posts_per_page',10));
		$paged      = ($mobile_api->query->paged != ""?(int)$mobile_api->query->paged:($mobile_api->query->page != ""?(int)$mobile_api->query->page:1));
		$offset     = ($paged-1)*$number;
		$tag_order  = apply_filters('mobile_api_tag_order',"DESC");// ASC
		$tag_sort   = apply_filters('mobile_api_tag_sort',"count");// followers - name
		$tag_sort   = ($tag_sort == "followers"?"meta_value_num":$tag_sort);
		$meta_query = ($tag_sort == "meta_value_num"?array('meta_query' => array("relation" => "or",array("key" => "tag_follow_count","compare" => "NOT EXISTS"),array("key" => "tag_follow_count","value" => 0,"compare" => ">="))):array());
		$tags       = (array) get_terms($args['taxonomy'],array_merge($search_args,array('hide_empty' => $args['hide_empty'])));
		$wp_tags    = (array) get_terms($args['taxonomy'],array_merge($search_args,$meta_query,array('orderby' => $tag_sort,'order' => $tag_order,'number' => $number,'offset' => $offset,'hide_empty' => $args['hide_empty'])));

		return array(
			'total' => count($tags),
			'pages' => ($number > 0?ceil(count($tags)/$number):0),
			'count' => count($wp_tags),
			'tags'  => array_map(array(&$this,'get_tagobject'),$wp_tags)
		);
	}

	public function get_tagobject($wp_tag) {
		if (!$wp_tag) {
			return null;
		}
		return new MOBILE_API_Tag($wp_tag);
	}

	public function get_user_index() {
		global $mobile_api;
		if ($mobile_api->query->type == "follow") {
			$number = mobile_api_options("mobile_api_following_pages");
			$following_users = mobile_api_options("mobile_api_following_users");
		}else {
			$number = mobile_api_options("mobile_users_page");
			$following_users = mobile_api_options("mobile_users_roles_page");
		}
		$number = ($mobile_api->query->count?$mobile_api->query->count:$number);
		return $mobile_api->introspector->users($number,$following_users);
	}

	public function get_group_users() {
		global $mobile_api;
		$number = ($mobile_api->query->count > 0?$mobile_api->query->count:get_option('posts_per_page',10));
		if ($mobile_api->query->type == "requests") {
			$users = get_post_meta($mobile_api->query->group_id,"group_requests_array",true);
		}else if ($mobile_api->query->type == "users") {
			$users = get_post_meta($mobile_api->query->group_id,"group_users_array",true);
		}else if ($mobile_api->query->type == "admins") {
			$users = get_post_meta($mobile_api->query->group_id,"group_moderators",true);
		}else if ($mobile_api->query->type == "blocked") {
			$users = get_post_meta($mobile_api->query->group_id,"blocked_users_array",true);
		}
		return (mobile_api_groups()?$mobile_api->introspector->group_users($number,$mobile_api->query->group_id,$users,$mobile_api->query->type):array());
	}
	
	public function follow() {
		global $mobile_api;
		$auth = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:false;
		if (!$auth) {
			$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])?$_SERVER['REDIRECT_HTTP_AUTHORIZATION']:false;
		}
		if (!$auth) {
			$mobile_api->error(esc_html__('Authorization header not found.','mobile-api'));
		}
		$id = esc_attr(isset($_GET['id']) && $_GET['id']?$_GET['id']:'');
		$type = esc_attr(isset($_GET['type']) && $_GET['type']?$_GET['type']:'');
		$action = esc_attr(isset($_GET['action']) && $_GET['action']?$_GET['action']:'');
		if ($id == '') {
			$mobile_api->error(esc_html__("Include id var in your request.","mobile-api"));
		}
		if ($type == '') {
			$mobile_api->error(esc_html__("Include type var in your request.","mobile-api"));
		}
		if ($action == '') {
			$mobile_api->error(esc_html__("Include action var in your request.","mobile-api"));
		}
		if (!has_wpqa() && ($type == "category" || $type == "tag")) {
			$mobile_api->error(esc_html__('The following is not available.','mobile-api'));
		}
		$user_id = get_current_user_id();
		$_POST['mobile'] = true;
		$explode = explode(",",$id);
		if ($type == "category" || $type == "tag") {
			$_POST['tax_type'] = $type;
			foreach ($explode as $value) {
				$_POST['tax_id'] = $value;
				if ($action == "follow") {
					wpqa_follow_cat();
				}else {
					wpqa_unfollow_cat();
				}
			}
		}else {
			foreach ($explode as $value) {
				if ($value > 0) {
					$_POST['following_var_id'] = $value;
					$return = mobile_api_following($action,$_POST);
				}
			}
		}
		return array(
			"followed" => ($action == "follow"?true:false)
		);
	}
	
	public function get_author_index() {
		global $mobile_api;
		$authors = $mobile_api->introspector->get_authors();
		return array(
			'count' => count($authors),
			'authors' => array_values($authors)
		);
	}
	
	public function get_page_index() {
		global $mobile_api;
		$pages = array();
		$post_type = $mobile_api->query->post_type ? $mobile_api->query->post_type : 'page';
		
		$numberposts = empty($mobile_api->query->count) ? -1 : $mobile_api->query->count;
		$wp_posts = get_posts(array(
			'post_type' => $post_type,
			'post_parent' => 0,
			'order' => 'ASC',
			'orderby' => 'menu_order',
			'numberposts' => $numberposts
		));
		foreach ($wp_posts as $wp_post) {
			$pages[] = new MOBILE_API_Post($wp_post);
		}
		foreach ($pages as $page) {
			$mobile_api->introspector->attach_child_posts($page);
		}
		return array(
			'pages' => $pages
		);
	}

	public function report() {
		global $mobile_api;
		if (isset($_POST)) {
			$data = $_POST;
			if ((isset($data["post_id"]) || isset($data["user_id"])) && isset($data["type"])) {
				if (!isset($data["explain"]) || (isset($data["explain"]) && $data["explain"] == "")) {
					$mobile_api->error(esc_html__("Please fill the reason field.","mobile-api"));
				}
				if ($data["type"] == "answer" && (!isset($data["answer_id"]) || (isset($data["answer_id"]) && $data["answer_id"] == ""))) {
					$mobile_api->error(esc_html__("You must include answer_id var in your request.","mobile-api"));
				}
				if ($data["type"] == "user" && (!isset($data["user_id"]) || (isset($data["user_id"]) && $data["user_id"] == ""))) {
					$mobile_api->error(esc_html__("You must include user_id var in your request.","mobile-api"));
				}
				if ($data["type"] == "user") {
					$data["report_id"] = $data["user_id"];
				}else {
					$data["post_id"] = $data["post_id"];
					$data["report_id"] = $data["answer_id"];
				}
				$return = mobile_api_report($data,$data["type"]);
				if ($return == "reviewed_answer") {
					return array("message" => esc_html__("You reported an answer. It will be reviewed shortly, The answer is under review.","mobile-api"));
				}else if ($return == "reviewed_question") {
					return array("message" => esc_html__("You reported a question. It will be reviewed shortly, The question is under review.","mobile-api"));
				}else if ($return == "deleted_successfully") {
					return array("message" => esc_html__("Deleted successfully.","mobile-api"));
				}else {
					return array("message" => esc_html__("Thank you, your report will be reviewed shortly.","mobile-api"));
				}
			}else {
				$mobile_api->error(esc_html__("You must include post_id and type vars in your request.","mobile-api"));
			}
		}else {
			$mobile_api->error(esc_html__("Your request not POST.","mobile-api"));
		}
	}
	
	protected function get_object_posts($object, $id_var, $slug_var) {
		global $mobile_api;
		$object_id = "{$type}_id";
		$object_slug = "{$type}_slug";
		extract($mobile_api->query->get(array('id', 'slug', $object_id, $object_slug)));
		if ($id || $object_id) {
			if (!$id) {
				$id = $object_id;
			}
			$posts = $mobile_api->introspector->get_posts(array(
				$id_var => $id
			),"counts");
		} else if ($slug || $object_slug) {
			if (!$slug) {
				$slug = $object_slug;
			}
			$posts = $mobile_api->introspector->get_posts(array(
				$slug_var => $slug
			),"counts");
		} else {
			$mobile_api->error(sprintf(__("No %s specified. Include id or slug var in your request.","mobile-api"),$type));
		}
		return $posts;
	}
	
	protected function posts_result($posts,$count = "",$found_posts = "") {
		global $wp_query;
		$count = (int)($count != ""?$count:count($posts));
		$count_total = (int)($found_posts != ""?$found_posts:$wp_query->found_posts);
		return array(
			'count'       => $count,
			'count_total' => $count_total,
			'pages'       => ($count > 0?ceil($count_total/$count):0),
			'posts'       => $posts
		);
	}
	
	protected function posts_object_result($posts, $object) {
		global $wp_query;
		$object_key = strtolower(substr(get_class($object), 9));
		$count = (int)count($posts);
		$count_total = (int)$wp_query->found_posts;
		return array(
			'count'     => $count,
			'pages'     => ceil($count_total/$count),
			$object_key => $object,
			'posts'     => $posts
		);
	}
	
	protected function posts_with_object_result($wp_query, $object) {
		$object_key = strtolower(substr(get_class($object), 9));
		$count = (int)count($wp_query->posts);
		$count_total = (int)$wp_query->found_posts;
		return array(
			'count'       => $count,
			'count_total' => $count_total,
			'pages'       => ceil($count_total/$count),
			$object_key   => $object,
			'posts'       => $wp_query->posts,
		);
	}
	
}?>
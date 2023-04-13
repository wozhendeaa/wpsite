<?php

class MOBILE_API_Introspector
{

	public function get_posts($query = false, $wp_counts = false, $wp_posts = false)
	{
		global $post;
		$output = array();
		$post_query = new WP_Query($this->set_posts_query($query));
		if ($post_query->have_posts()) {
			while ( $post_query->have_posts() ) { $post_query->the_post();
				if ($wp_posts) {
					$new_post = $post;
				} else {
					$new_post = new MOBILE_API_Post($post);
				}
				$output[] = $new_post;
			}
		}
		if ($wp_counts == 'counts') {
			$post_query->posts = $output;
			$output = $post_query;
		}
		return $output;
	}

	public function get_date_archive_permalinks()
	{
		$archives = wp_get_archives('echo=0');
		preg_match_all("/href='([^']+)'/", $archives, $matches);
		return $matches[1];
	}

	public function get_date_archive_tree($permalinks)
	{
		$tree = array();
		foreach ($permalinks as $url) {
			if (preg_match('#(\d{4})/(\d{2})#', $url, $date)) {
				$year  = $date[1];
				$month = $date[2];
			} else if (preg_match('/(\d{4})(\d{2})/', $url, $date)) {
				$year  = $date[1];
				$month = $date[2];
			} else {
				continue;
			}
			$count = $this->get_date_archive_count($year, $month);
			if (empty($tree[$year])) {
				$tree[$year] = array(
					$month => $count,
				);
			} else {
				$tree[$year][$month] = $count;
			}
		}
		return $tree;
	}

	public function get_date_archive_count($year, $month)
	{
		if (!isset($this->month_archives)) {
			global $wpdb;
			$post_counts = $wpdb->get_results("
				SELECT DATE_FORMAT(post_date, '%Y%m') AS month, COUNT(ID) AS post_count
				FROM $wpdb->posts
				WHERE post_status = 'publish' AND post_type = 'post' GROUP BY month
			");
			$this->month_archives = array();
			foreach ($post_counts as $post_count) {
				$this->month_archives[$post_count->month] = $post_count->post_count;
			}
		}
		return $this->month_archives["$year$month"];
	}

	public function get_categories($args = null)
	{
		$wp_categories = (array) get_terms( $args );
		$categories    = array();
		foreach ($wp_categories as $wp_category) {
			$categories[] = $this->get_category_object($wp_category);
		}
		return $categories;
	}

	public function get_current_post()
	{
		global $mobile_api;
		extract($mobile_api->query->get(array('id', 'slug', 'post_id', 'post_slug')));
		if ($id || $post_id) {
			if (!$id) {
				$id = $post_id;
			}
			$posts = $this->get_posts(array(
				'p' => $id,
				'post_type' => 'any',
			),false , true);
		} else if ($slug || $post_slug) {
			if (!$slug) {
				$slug = $post_slug;
			}
			$posts = $this->get_posts(array(
				'name' => $slug,
				'post_type' => 'any',
			), false , true);
		} else {
			$mobile_api->error(esc_html__("Include id or slug var in your request.","mobile-api"));
		}
		if (!empty($posts)) {
			return $posts[0];
		} else {
			return null;
		}
	}

	public function get_current_category()
	{
		global $mobile_api;
		extract($mobile_api->query->get(array('id', 'slug', 'category_id', 'category_slug')));
		$taxonomy = $mobile_api->query->get("taxonomy");
		if ($id || $category_id) {
			if (!$id) {
				$id = $category_id;
			}
			return $this->get_category_by_id($id,$taxonomy);
		} else if ($slug || $category_slug) {
			if (!$slug) {
				$slug = $category_slug;
			}
			return $this->get_category_by_slug($slug,$taxonomy);
		} else {
			$mobile_api->error(esc_html__("Include id or slug var in your request.","mobile-api"));
		}
		return null;
	}

	public function get_category_by_id($category_id,$taxonomy)
	{
		$wp_category = get_term_by('id', $category_id, $taxonomy);
		return $this->get_category_object($wp_category);
	}

	public function get_category_by_slug($category_slug,$taxonomy)
	{
		$wp_category = get_term_by('slug', $category_slug, $taxonomy);
		return $this->get_category_object($wp_category);
	}

	public function get_tags($args)
	{
		$wp_tags = (array) get_terms( $args );
		return array_map(array(&$this, 'get_tag_object'), $wp_tags);
	}

	public function get_current_tag()
	{
		global $mobile_api;
		extract($mobile_api->query->get(array('id', 'slug', 'tag_id', 'tag_slug')));
		$taxonomy = $mobile_api->query->get("taxonomy");
		if ($id || $tag_id) {
			if (!$id) {
				$id = $tag_id;
			}
			return $this->get_tag_by_id($id,$taxonomy);
		} else if ($slug || $tag_slug) {
			if (!$slug) {
				$slug = $tag_slug;
			}
			return $this->get_tag_by_slug($slug,$taxonomy);
		} else {
			$mobile_api->error(esc_html__("Include id or slug var in your request.","mobile-api"));
		}
		return null;
	}

	public function get_tag_by_id($tag_id,$taxonomy)
	{
		$wp_tag = get_term_by('id', $tag_id, $taxonomy);
		return $this->get_tag_object($wp_tag);
	}

	public function get_tag_by_slug($tag_slug)
	{
		$wp_tag = get_term_by('slug', $tag_slug, $taxonomy);
		return $this->get_tag_object($wp_tag);
	}

	public function get_authors()
	{
		global $wpdb;
		$author_ids = $wpdb->get_col("
			SELECT u.ID, m.meta_value AS last_name
			FROM $wpdb->users AS u, $wpdb->usermeta AS m
			WHERE m.user_id = u.ID AND m.meta_key = 'last_name' ORDER BY last_name
		");
		$all_authors    = array_map(array(&$this, 'get_author_by_id'), $author_ids);
		$active_authors = array_filter($all_authors, array(&$this, 'is_active_author'));
		return $active_authors;
	}

	public function get_current_author()
	{
		global $mobile_api;
		extract($mobile_api->query->get(array('id', 'slug', 'author_id', 'author_slug')));
		if ($id || $author_id) {
			if (!$id) {
				$id = $author_id;
			}
			return $this->get_author_by_id($id);
		} else if ($slug || $author_slug) {
			if (!$slug) {
				$slug = $author_slug;
			}
			return $this->get_author_by_login($slug);
		} else {
			$mobile_api->error(esc_html__("Include id or slug var in your request.","mobile-api"));
		}
		return null;
	}

	public function get_author_by_id($id)
	{
		$id = get_the_author_meta('ID', $id);
		if (!$id) {
			return null;
		}
		return new MOBILE_API_Author($id);
	}

	public function get_author_by_login($login) {
		global $wpdb;
		$id = $wpdb->get_var($wpdb->prepare("
		SELECT ID
			FROM $wpdb->users
			WHERE user_nicename = %s
		", $login));
		return $this->get_author_by_id($id);
	}

	public function users($number,$following_users,$search = "",$role__not_in = array()) {
		if ($search != "") {
			add_action('pre_user_query','mobile_api_custom_search_users');
		}
		$user_id         = get_current_user_id();
		$number          = (int)($number > 0?$number:get_option('posts_per_page'));
		$paged           = mobile_api_paged();
		$offset          = ($paged-1)*$number;
		$following_array = (is_array($following_users) && !empty($following_users)?array('role__in' => $following_users):array());
		$role__not_in = (is_array($role__not_in) && !empty($role__not_in)?array('role__not_in' => $role__not_in):array());
		$args            = array_merge($following_array,$role__not_in,array(
							'meta_query' => ($search != ""?array('relation' => 'OR',array("key" => "first_name","value" => $search,"compare" => "RLIKE")):array()),
							'search'     => ($search != ""?'*'.$search.'*':''),
							'offset'     => $offset,
							'number'     => $number,
							'orderby'    => 'registered',
							'exclude'    => $user_id
						)
		);
		$query           = new WP_User_Query($args);
		$total_query     = $query->get_total();
		$total_pages     = ceil($total_query/$number);
		$current         = max(1,$paged);
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
		if ($search != "") {
			remove_action('pre_user_query','mobile_api_custom_search_users');
		}
		return array(
			"total" => $total_query,
			"count" => $number,
			"pages" => (isset($total_pages)?$total_pages:0),
			"users" => (isset($users_array)?$users_array:array())
		);
	}

	public function group_users($number,$group_id,$group_users,$type) {
		if (mobile_api_groups() && isset($group_users) && is_array($group_users) && !empty($group_users)) {
			$active_points_category = mobile_api_options("active_points_category");
			if ($type == "users" || $type == "blocked") {
				$blocked_users = get_post_meta($group_id,"blocked_users_array",true);
			}
			if ($type == "users" || $type == "admins") {
				$group_moderators = get_post_meta($group_id,"group_moderators",true);
			}
			$user_id     = get_current_user_id();
			$number      = (int)($number > 0?$number:get_option('posts_per_page'));
			$paged       = mobile_api_paged();
			$offset      = ($paged-1)*$number;
			$args        = array(
				'include' => $group_users,
				'offset'  => $offset,
				'number'  => $number,
				'orderby' => 'registered',
			);
			$query       = new WP_User_Query($args);
			$total_query = $query->get_total();
			$total_pages = ceil($total_query/$number);
			$current     = max(1,$paged);
			foreach ($query->get_results() as $user) {
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
				$buttons = array();
				$mobile_group_red = mobile_api_options("mobile_group_red");
				$mobile_group_green = mobile_api_options("mobile_group_green");
				if ($type == "requests") {
					$buttons = array(
						"button_1" => array(
							"name" => esc_html__("Approve","mobile-api"),
							"color" => "ffffff",
							"darkColor" => "ffffff",
							"background" => $mobile_group_green,
							"darkbackground" => $mobile_group_green,
							"api" => mobile_api_group_actions_2."?action=approve_request_group&id=".$group_id."&user_id=".$user->ID
						),
						"button_2" => array(
							"name" => esc_html__("Decline","mobile-api"),
							"color" => "ffffff",
							"darkColor" => "ffffff",
							"background" => $mobile_group_red,
							"darkbackground" => $mobile_group_red,
							"api" => mobile_api_group_actions_2."?action=decline_request_group&id=".$group_id."&user_id=".$user->ID
						)
					);
				}else if ($type == "users") {
					if (is_super_admin($user_id) || $group_author == $user_id || ($user_id > 0 && is_array($group_moderators) && in_array($user_id,$group_moderators))) {
						if (isset($group_moderators) && is_array($group_moderators) && in_array($user->ID,$group_moderators)) {
						}else {
							if (isset($blocked_users) && is_array($blocked_users) && in_array($user->ID,$blocked_users)) {
								$buttons = array(
									"button_1" => array(
										"name" => esc_html__("Unblock","mobile-api"),
										"color" => "ffffff",
										"darkColor" => "ffffff",
										"background" => $mobile_group_green,
										"darkbackground" => $mobile_group_green,
										"api" => mobile_api_group_actions_2."?action=unblock_user_group&id=".$group_id."&user_id=".$user->ID
									)
								);
							}else if ($user_id != $user->ID) {
								$buttons = array(
									"button_1" => array(
										"name" => esc_html__("Remove","mobile-api"),
										"color" => "ffffff",
										"darkColor" => "ffffff",
										"background" => $mobile_group_red,
										"darkbackground" => $mobile_group_red,
										"api" => mobile_api_group_actions_2."?action=remove_user_group&id=".$group_id."&user_id=".$user->ID,
										"alert" => esc_html__('Are you sure you want to remove the user from the group?','mobile-api')
									),
									"button_2" => array(
										"name" => esc_html__("Block","mobile-api"),
										"color" => "ffffff",
										"darkColor" => "ffffff",
										"background" => $mobile_group_red,
										"darkbackground" => $mobile_group_red,
										"api" => mobile_api_group_actions_2."?action=block_user_group&id=".$group_id."&user_id=".$user->ID,
										"alert" => esc_html__('Are you sure you want to block the user from the group?','mobile-api')
									)
								);
							}
						}
					}
				}else if ($type == "admins") {
					$get_post = get_post($group_id);
					$group_author = $get_post->post_author;
					if (!is_super_admin($user->ID) && ($group_author != 0 && $group_author != $user->ID) && isset($group_moderators) && is_array($group_moderators) && in_array($user->ID,$group_moderators)) {
						$buttons = array(
							"button_1" => array(
								"name" => esc_html__("Remove Moderator","mobile-api"),
								"color" => "ffffff",
								"darkColor" => "ffffff",
								"background" => $mobile_group_red,
								"darkbackground" => $mobile_group_red,
								"api" => mobile_api_group_actions_2."?action=remove_moderator_group&id=".$group_id."&user_id=".$user->ID,
								"alert" => esc_html__('Are you sure you want to remove the moderator from the group?','mobile-api')
							)
						);
					}
				}else if ($type == "blocked") {
					if (isset($group_moderators) && is_array($group_moderators) && in_array($user->ID,$group_moderators)) {
					}else if (isset($blocked_users) && is_array($blocked_users) && in_array($user->ID,$blocked_users)) {
						$buttons = array(
							"button_1" => array(
								"name" => esc_html__("Unblock","mobile-api"),
								"color" => "ffffff",
								"darkColor" => "ffffff",
								"background" => $mobile_group_green,
								"darkbackground" => $mobile_group_green,
								"api" => mobile_api_group_actions_2."?action=unblock_user_group&id=".$group_id."&user_id=".$user->ID
							)
						);
					}
				}
				$users_array[] = array_merge(array(
						"id"          => $user->ID,
						"displayname" => $user->display_name,
						"avatar"      => mobile_api_user_avatar_link(array("user_id" => $user->ID,"size" => 128)),
						"verified"    => ($verified_user == 1 || $verified_user == "on"?true:false),
						"badge"       => array("name" => strip_tags(mobile_api_get_badge($user->ID,"name",(isset($first_points)?$first_points:""))),"color" => ($badge_color != ""?$badge_color:""),"textColor" => "FFFFFF","textColorDark" => "FFFFFF"),
				),$buttons);
			}
		}
		return array(
			"total" => (isset($total_query)?$total_query:0),
			"count" => $number,
			"pages" => (isset($total_pages)?$total_pages:0),
			"users" => (isset($users_array)?$users_array:array())
		);
	}

	public function get_recent_comments($post_type = array(),$search = "") {
		$search_args = array();
		if ($search != "") {
			$search_args = array('search' => $search);
		}
		$post_type = (!empty($post_type)?$post_type:array("post"));
		$post_type = (in_array("answers",$post_type)?array(mobile_api_questions_type):$post_type);
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

		$author__not_in = array();
    	$block_users = mobile_api_options("block_users");
		if ($block_users == mobile_api_checkbox_value) {
			$user_id = get_current_user_id();
			if ($user_id > 0) {
				$get_block_users = get_user_meta($user_id,mobile_api_action_prefix()."_block_users",true);
				if (is_array($get_block_users) && !empty($get_block_users)) {
					$author__not_in = array("post_author__not_in" => $get_block_users,"author__not_in" => $get_block_users);
				}
			}
		}

		$parent_args = array();
		if ($search == "") {
			$parent_args = array('parent' => 0);
		}

		$comments_args = array_merge($author__not_in,$search_args,$comments_args,$parent_args,array('post_type' => $post_type,'status' => 'approve','number' => $rows_per_page,'offset' => $offset));
		$comments = get_comments($comments_args);
		$i = $s = 0;
		if (is_array($comments) && !empty($comments)) {
			$replies = array();
			foreach($comments as $comment) {
				$comments[$i] = new MOBILE_API_Comment($comment);
				$get_replies = $this->get_replies($replies,$comment->comment_ID,$post_id,$post_type);
				$comments[$i]->replies = (is_array($get_replies) && !empty($get_replies)?$get_replies:array());
				$i++;
			}
		}
		$total = mobile_api_comments_of_post_type($post_type,0,array(),$search,0);
		return array("count" => $rows_per_page,"count_total" => $total,"pages" => ceil($total/$rows_per_page),"comments" => $comments);
	}

	public function get_comments($post_id,$post_type = "") {
		$post_type = (isset($post_type)?$post_type:get_post_type($post_id));
		if ($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) {
			$the_best_answer = get_post_meta($post_id,"the_best_answer",true);
			$answers_sort = mobile_api_options("mobile_answers_sort");
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
			$user_id = get_current_user_id();
			if ($user_id > 0) {
				$get_block_users = get_user_meta($user_id,mobile_api_action_prefix()."_block_users",true);
				if (is_array($get_block_users) && !empty($get_block_users)) {
					$author__not_in = array("post_author__not_in" => $get_block_users,"author__not_in" => $get_block_users);
				}
			}
		}

		$comments_args = (isset($the_best_answer) && $the_best_answer != ""?array_merge($comments_args,array('comment__not_in' => array($the_best_answer))):$comments_args);
		$comments_1 = (isset($the_best_answer) && $the_best_answer != ""?get_comments(array_merge($author__not_in,array('post_id' => $post_id,'status' => 'approve','comment__in' => array($the_best_answer)))):array());
		$comments_args = array_merge($author__not_in,$comments_args,array('post_id' => $post_id,'parent' => 0,'status' => 'approve'));
		$comments_2 = get_comments($comments_args);
		$comments = array_merge($comments_1,$comments_2);
		$i = 0;
		if (isset($the_best_answer) && $the_best_answer != "") {
			foreach($comments as $comment) {
				if ($comment->comment_ID == $the_best_answer) {
					$get_best_answer = $i;
				}
				$i++;
			}
			if (isset($get_best_answer)) {
				$key = $get_best_answer;
				$temp = array($key => $comments[$key]);
				unset($comments[$key]);
				$comments = array_merge($temp,$comments);
			}
		}
		$i = 0;
		if (is_array($comments) && !empty($comments)) {
			$replies = array();
			foreach($comments as $comment) {
				$comments[$i] = new MOBILE_API_Comment($comment);
				$get_replies = $this->get_replies($replies,$comment->comment_ID,$post_id,$post_type);
				$comments[$i]->replies = (is_array($get_replies) && !empty($get_replies)?$get_replies:array());
				$i++;
			}
		}
		return $comments;
	}

	public function get_replies($replies,$comment_id,$post_id,$post_type)
	{
		$answers_sort = mobile_api_options("mobile_answers_sort");
		if (($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) && isset($answers_sort) && $answers_sort == "reacted") {
			$orderby = array('orderby' => array('meta_value_num' => 'DESC','comment_date' => 'ASC'),'meta_key' => 'wpqa_reactions_count','order' => 'DESC');
		}else if (($post_type == mobile_api_questions_type || $post_type == mobile_api_asked_questions_type) && isset($answers_sort) && $answers_sort == "voted") {
			$orderby = array('orderby' => array('meta_value_num' => 'DESC','comment_date' => 'ASC'),'meta_key' => 'comment_vote','order' => 'DESC');
		}else if (isset($answers_sort) && $answers_sort == "oldest") {
			$orderby = array('orderby' => 'comment_date','order' => 'ASC');
		}else {
			$orderby = array('orderby' => 'comment_date','order' => 'DESC');
		}
		$comments_args = array(
			'post_id' => $post_id,
			'parent'  => $comment_id,
			'status'  => 'approve'
		);

		$author__not_in = array();
    	$block_users = mobile_api_options("block_users");
		if ($block_users == mobile_api_checkbox_value) {
			$user_id = get_current_user_id();
			if ($user_id > 0) {
				$get_block_users = get_user_meta($user_id,mobile_api_action_prefix()."_block_users",true);
				if (is_array($get_block_users) && !empty($get_block_users)) {
					$author__not_in = array("post_author__not_in" => $get_block_users,"author__not_in" => $get_block_users);
				}
			}
		}

		$child_comments = get_comments(array_merge($author__not_in,$orderby,$comments_args));
		if (is_array($child_comments) && !empty($child_comments)) {
			foreach($child_comments as $comment) {
				$replies[] = new MOBILE_API_Comment($comment);
				$replies = $this->get_replies($replies,$comment->comment_ID,$post_id,$post_type);
			}
		}
		return $replies;
	}

	public function get_attachments($post_id)
	{
		$wp_attachments = get_children(array(
			'post_type'        => 'attachment',
			'post_parent'      => $post_id,
			'orderby'          => 'menu_order',
			'order'            => 'ASC',
			'suppress_filters' => false,
		));
		$attachments = array();
		if (!empty($wp_attachments)) {
			foreach ($wp_attachments as $wp_attachment) {
				$attachments[] = new MOBILE_API_Attachment($wp_attachment);
			}
		}
		return $attachments;
	}

	public function get_attachment($attachment_id)
	{
		global $wpdb;
		$wp_attachment = $wpdb->get_row(
			$wpdb->prepare("
				SELECT *
				FROM $wpdb->posts
				WHERE ID = %d
			", $attachment_id)
		);
		return new MOBILE_API_Attachment($wp_attachment);
	}

	public function attach_child_posts(&$post)
	{
		$post->children = array();
		$wp_children    = get_posts(array(
			'post_type'        => $post->type,
			'post_parent'      => $post->id,
			'order'            => 'ASC',
			'orderby'          => 'menu_order',
			'numberposts'      => -1,
			'suppress_filters' => false,
		));
		foreach ($wp_children as $wp_post) {
			$new_post         = new MOBILE_API_Post($wp_post);
			$new_post->parent = $post->id;
			$post->children[] = $new_post;
		}
		foreach ($post->children as $child) {
			$this->attach_child_posts($child);
		}
	}

	protected function get_category_object($wp_category)
	{
		if (!$wp_category) {
			return null;
		}
		return new MOBILE_API_Category($wp_category);
	}

	protected function get_tag_object($wp_tag)
	{
		if (!$wp_tag) {
			return null;
		}
		return new MOBILE_API_Tag($wp_tag);
	}

	protected function is_active_author($author)
	{
		if (!isset($this->active_authors)) {
			$this->active_authors = explode(',', wp_list_authors(array(
				'html'          => false,
				'echo'          => false,
				'exclude_admin' => false,
			)));
			$this->active_authors = array_map('trim', $this->active_authors);
		}
		return in_array($author->name, $this->active_authors);
	}

	protected function set_posts_query($query = false)
	{
		global $mobile_api, $wp_query;

		$query = (is_array($query)?$query:array());
		
		if (!isset($query['s']) || (isset($query['s']) && $query['s'] == '')) {
			$query = array_merge($query, $wp_query->query);
		}

		if ($mobile_api->query->page) {
			$query['paged'] = $mobile_api->query->page;
		}

		if (isset($query['count']) && $query['count'] != "") {
			$query['posts_per_page'] = (int)$query['count'];
		}else if ($mobile_api->query->count && !isset($query['posts_per_page'])) {
			$query['posts_per_page'] = $mobile_api->query->count;
		}

		if ($mobile_api->query->post_type && (!isset($query['s']) || (isset($query['s']) && $query['s'] == ''))) {
			$query['post_type'] = $mobile_api->query->post_type;
		}
		if ($mobile_api->query->id != "" && $mobile_api->query->post_type == mobile_api_questions_type) {
			$query['post_type'] = array(mobile_api_questions_type,mobile_api_asked_questions_type);
		}
		if ((isset($query["post_type"]) && ($query["post_type"] == mobile_api_questions_type || $query["post_type"] == mobile_api_asked_questions_type)) || (isset($query["tax_query"]) && ($query["tax_query"] == mobile_api_question_categories || $query["tax_query"] == mobile_api_question_tags))) {
			$query["meta_query"] = (isset($query["meta_query"])?$query["meta_query"]:array());
			$query["meta_query"] = $query["meta_query"];
		}

		$block_users = mobile_api_options("block_users");
		if ($block_users == mobile_api_checkbox_value) {
			$user_id = get_current_user_id();
			if ($user_id > 0) {
				$get_block_users = get_user_meta($user_id,mobile_api_action_prefix()."_block_users",true);
				if (is_array($get_block_users) && !empty($get_block_users) && !isset($query["author"])) {
					$query["author__not_in"] = $get_block_users;
				}
			}
		}

		if (isset($query["json"])) {
			unset($query["json"]);
		}

		if (isset($query["tax_query"]) && isset($query["taxonomy"])) {
			unset($query["taxonomy"]);
		}

		$query = apply_filters("mobile_api_query_post",$query);

		return $query;

		if (!empty($query)) {
			do_action('mobile_api_query', $wp_query);
		}
	}
}?>
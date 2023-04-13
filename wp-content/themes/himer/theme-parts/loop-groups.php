<?php include locate_template("includes/group-setting.php");
$user_id = get_current_user_id();
$wpqa_user_id = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
$is_super_admin = is_super_admin($user_id);
$paged = himer_paged();
$group_display = ($group_display == "private" || $group_display == "public"?array("meta_query" => array(array("key" => "group_privacy","value" => $group_display,"compare" => "="))):array());
$group_search = (isset($group_search)?$group_search:array());
if (wpqa_is_user_joined_groups()) {
	$joined_groups = true;
	$groups_array = get_user_meta($wpqa_user_id,"groups_array",true);
	$user_groups = (isset($joined_groups) && $joined_groups == true?array("post__in" => $groups_array):array());
}else if (wpqa_is_user_managed_groups()) {
	$managed_groups = true;
	$groups_moderator_array = get_user_meta($wpqa_user_id,"groups_moderator_array",true);
	$user_groups = (isset($managed_groups) && $managed_groups == true?array("post__in" => $groups_moderator_array):array());
}else {
	$user_groups = (wpqa_is_user_groups()?array("author" => $wpqa_user_id):array());
}
if (isset($user_groups) && ((!isset($joined_groups) && !isset($groups_moderator_array)) || (isset($groups_array) && is_array($groups_array) && !empty($groups_array) && count($groups_array) > 0) || (isset($groups_moderator_array) && is_array($groups_moderator_array) && !empty($groups_moderator_array) && count($groups_moderator_array) > 0))) {
	$array_data = array_merge($group_search,$user_groups,$group_display,$orderby_array,array("post_type" => "group","paged" => $paged,"posts_per_page" => $group_number));
}
if (isset($array_data) && is_array($array_data) && !empty($array_data)) {
	$query_wp = new WP_Query($array_data);
}
$date_format = himer_options("date_format");
$date_format = ($date_format?$date_format:get_option("date_format"));
$site_width = himer_options("site_width");
if (isset($query_wp) && $query_wp->have_posts()) :?>
	<section class="content_groups row block-section-div group-articles post-articles loop-section">
		<div class="row-boot">
			<?php $k_ad_p = -1;
			while ($query_wp->have_posts()) : $query_wp->the_post();
				if (isset($GLOBALS['post'])) {
					$group_data = $GLOBALS['post'];
				}
				$post_id = $group_data->ID;
				$the_title = get_the_title($post_id);
				$group_privacy = get_post_meta($post_id,"group_privacy",true);
				$group_cover = get_post_meta($post_id,"group_cover",true);
				$group_image = get_post_meta($post_id,"group_image",true);
				$group_users = (int)get_post_meta($post_id,"group_users",true);
				$group_posts = (int)get_post_meta($post_id,"group_posts",true);
				$group_users_array = get_post_meta($post_id,"group_users_array",true);
				$group_requests_array = get_post_meta($post_id,"group_requests_array",true);
				$group_invitations = get_post_meta($post_id,"group_invitations",true);
				$join_leave_text = $join_leave_class = $join_leave_text_2 = $join_leave_class_2 = "";
				echo '<article class="'.join(' ',get_post_class("col col6 col-boot-sm-6",$post_id)).'">
					<div class="group-item card group-card">
						<div class="group_cover group__cover">';
							if ((is_array($group_cover) && isset($group_cover["id"])) || (!is_array($group_cover) && $group_cover != "")) {
								$img_width = 344;
								if ($site_width > 1170) {
									$mins_width = ($site_width-1170);
									$img_width = round($img_width+($mins_width/2));
								}
								$img_height = 229;
								echo '<a href="'.get_permalink($post_id).'">'.wpqa_get_aq_resize_img($img_width,$img_height,"",(is_array($group_cover) && isset($group_cover["id"])?$group_cover["id"]:$group_cover),"no",$the_title).'</a>';
							}
						echo '</div>
						<div class="card-body">
							<div class="group_avatar group__avatar mb-3">';
								if ((is_array($group_image) && isset($group_image["id"])) || (!is_array($group_image) && $group_image != "")) {
									echo '<a href="'.get_permalink($post_id).'">'.wpqa_get_aq_resize_img(80,80,"",(is_array($group_image) && isset($group_image["id"])?$group_image["id"]:$group_image),"no",$the_title,"","rounded-circle").'</a>';
								}else {
									echo '<div class="group_img"></div>';
								}
							echo '</div>
							<h3 class="group__name"><a href="'.get_permalink($post_id).'">'.$the_title.'</a></h3>
							<ul class="groups__stats list-unstyled d-flex flex-wrap">
								<li>'.($group_privacy == "public"?esc_html__("Public group","himer"):esc_html__("Private group","himer")).'</li>
								<li>'.sprintf(_n("%s User","%s Users",$group_users,"himer"),wpqa_count_number($group_users)).'</li>
								<li>'.sprintf(_n("%s Post","%s Posts",$group_posts,"himer"),wpqa_count_number($group_posts)).'</li>';
								$active_post_stats = himer_options("active_post_stats");
								$groups_visits = himer_options("groups_visits");
								if (has_wpqa() && $active_post_stats == "on" && $groups_visits == "on") {
									$post_stats = wpqa_get_post_stats($post_id);
									echo '<li>'.sprintf(_n("%s View","%s Views",$post_stats,"himer"),wpqa_count_number($post_stats)).'</li>';
								}
							echo '</ul>';
							$followers = $last_followers = 0;
							if (isset($group_users_array) && is_array($group_users_array)) {
								$followers = count($group_users_array);
							}
							
							if ($followers > 0) {
								$last_followers = $followers-5;
								if (isset($group_users_array) && is_array($group_users_array)) {
									$sliced_array = array_slice($group_users_array,0,5);
									echo '<ul class="mutual__friends list-unstyled d-flex flex-wrap align-items-center">';
									foreach ($sliced_array as $key => $value) {
										echo '<li>'.wpqa_get_user_avatar(array("user_id" => $value,"size" => "26","class" => "rounded-circle")).'</li>';
									}
									if ($last_followers > 0) {
										echo '<li class="pl-2">+ '.wpqa_count_number($last_followers).'</li>';
									}
									echo '</ul>';
								}
							}
							if ($is_super_admin || ($user_id > 0 && $user_id == $group_data->post_author)) {
								echo '<div class="group-buttons d-flex justify-content-between"><a href="'.esc_url_raw(add_query_arg(array("activate_delete" => true,"delete" => $post_id,"wpqa_delete_nonce" => wp_create_nonce("wpqa_delete_nonce")),get_permalink($post_id))).'" class="button-default delete-group btn btn__danger btn__large__height" data-id="'.$post_id.'">'.esc_html__("Delete","himer").'</a></div>';
							}else if (!$is_super_admin && $user_id > 0) {
								if (is_array($group_users_array) && in_array($user_id,$group_users_array)) {
									$join_leave_text = esc_html__("Leave","himer");
									$join_leave_class = "user_in_group btn__danger";
								}else {
									if (is_array($group_requests_array) && in_array($user_id,$group_requests_array)) {
										$join_leave_text = esc_html__("Cancel the request","himer");
										$join_leave_class = "cancel_request_group btn__danger";
									}else {
										if (is_array($group_invitations) && in_array($user_id,$group_invitations)) {
											$join_leave_text = esc_html__("Accept invite","himer");
											$join_leave_class = "accept_invite btn__success";
											$join_leave_text_2 = esc_html__("Decline invite","himer");
											$join_leave_class_2 = "decline_invite btn__danger";
										}else {
											$join_leave_text = esc_html__("Join","himer");
											if ($group_privacy == "public") {
												$join_leave_class = "user_out_group btn__primary";
											}else {
												$join_leave_class = "request_group btn__primary";
											}
										}
									}
								}
								if ((isset($join_leave_text) && $join_leave_text != "") || (isset($join_leave_text_2) && $join_leave_text_2 != "")) {
									echo '<div class="group_join group-buttons d-flex justify-content-between">
										<div class="cover_loader wpqa_hide"><div class="small_loader loader_2"></div></div>';
										if (isset($join_leave_text) && $join_leave_text != "") {
											echo '<a href="#" class="button-default btn btn__large__height hide_button_too '.$join_leave_class.'" data-id="'.$post_id.'">'.$join_leave_text.'</a>';
										}
										if (isset($join_leave_text_2) && $join_leave_text_2 != "") {
											echo '<a href="#" class="button-default btn btn__large__height hide_button_too '.$join_leave_class_2.'" data-id="'.$post_id.'">'.$join_leave_text_2.'</a>';
										}
									echo '</div>';
								}
							}
						echo '</div>
					</div>
				</article>';
			endwhile;?>
		</div>
	</section><!-- End section -->
	<?php if (has_wpqa()) :
		$count_total = (int)$query_wp->found_posts;
		$max_num_pages = ceil($count_total/$group_number);
		if (has_wpqa()) {
			wpqa_load_pagination(array(
				"post_pagination" => ($group_pagination != ""?$group_pagination:"pagination"),
				"max_num_pages" => $max_num_pages,
				"its_post_type" => "group",
				"wpqa_query" => $query_wp,
			));
		}
	endif;
else:
	echo '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("There are no groups yet.","himer").'</p></div>';
endif;?>
<?php wp_reset_postdata();?>
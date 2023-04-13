<?php add_action("himer_group_cover","himer_group_cover");
function himer_group_cover() {
	if (wpqa_is_edit_groups() || is_singular("group") || wpqa_is_group_requests() || wpqa_is_group_users() || wpqa_is_group_admins() || wpqa_is_blocked_users() || wpqa_is_posts_group() || wpqa_is_view_posts_group() || wpqa_is_edit_posts_group()) {
		$group_cover_activate = "on";
		if ($group_cover_activate == "on") {
			$group_id = wpqa_group_id();
			if ($group_id > 0) {
				$user_id = get_current_user_id();
				$is_super_admin = is_super_admin($user_id);
				$get_group = get_post($group_id);
				if (isset($get_group->ID)) {
					$blocked_users_array = get_post_meta($group_id,"blocked_users_array",true);
					$group_moderators = get_post_meta($group_id,"group_moderators",true);
					$blocked_users_array = (is_array($blocked_users_array)?$blocked_users_array:array());
					if ($is_super_admin || ($user_id > 0 && is_array($group_moderators) && in_array($user_id,$group_moderators)) || ($user_id > 0 && $user_id == $get_group->post_author) || !in_array($user_id,$blocked_users_array)) {
						$get_permalink = get_permalink($group_id);
						$the_title = $get_group->post_title;
						$group_privacy = get_post_meta($group_id,"group_privacy",true);
						$group_image = get_post_meta($group_id,"group_image",true);
						$group_users = (int)get_post_meta($group_id,"group_users",true);
						$group_posts = (int)get_post_meta($group_id,"group_posts",true);
						$group_users_array = get_post_meta($group_id,"group_users_array",true);
						$group_requests_array = get_post_meta($group_id,"group_requests_array",true);
						$group_invitations = get_post_meta($group_id,"group_invitations",true);
						$group_invitations = (is_array($group_invitations) && !empty($group_invitations)?$group_invitations:array());
						$group_cover = get_post_meta($group_id,"group_cover",true);
						if (($group_cover && !is_array($group_cover)) || (is_array($group_cover) && isset($group_cover["id"]) && $group_cover["id"] != 0)) {
							$group_cover_img = wpqa_get_cover_url($group_cover,"","");
						}
						if (wpqa_is_view_posts_group()) {
							$is_view_posts_group = true;
						}
						do_action("wpqa_action_before_group_cover",$group_id);
						do_action("himer_action_before_group_cover",$group_id);
						$group_cover_fixed = "fixed";
						echo '<div class="wrap-group-cover'.($group_cover_fixed == "fixed"?" group_fixed_cover container-boot":"").'">
							<div class="card group-card group-card-full mb-0 '.(isset($group_cover_img) && $group_cover_img != ""?"group_has_cover":"group_no_cover").'">
								<div class="group__cover">
									<div class="group_cover"></div>
									<div class="container-boot">';
										if ((is_array($group_image) && isset($group_image["id"])) || (!is_array($group_image) && $group_image != "")) {
											echo '<div class="group__avatar">'.wpqa_get_aq_resize_img(130,130,"",(is_array($group_image) && isset($group_image["id"])?$group_image["id"]:$group_image),"no",$the_title,"srcset","rounded-circle").'</div>';
										}
									echo '</div>
								</div><!-- /.group__cover -->
								<div class="card-body">
									<div class="'.($group_cover_fixed == "fixed"?"container-fixed":"container-boot").'">
										<div class="row-boot align-items-center">
											<div class="col-boot-sm-12 col-boot-md-6 col-boot-lg-7 col-boot-xl-8">
												<h3 class="group__name">'.(isset($is_view_posts_group)?"<a href='".$get_permalink."'>":"").$the_title.(isset($is_view_posts_group)?"</a>":"").'</h3>
												<ul class="groups__stats list-unstyled d-flex flex-wrap">
													<li>'.($group_privacy == "public"?esc_html__("Public group","himer"):esc_html__("Private group","himer")).'</li>
													<li><span class="cover-count">'.himer_count_number($group_users).'</span> '._n("User","Users",$group_users,"himer").'</li>
													<li><span class="cover-count">'.himer_count_number($group_posts).'</span> '._n("Post","Posts",$group_posts,"himer").'</li>';
													$active_post_stats = himer_options("active_post_stats");
													$groups_visits = himer_options("groups_visits");
													if (has_wpqa() && $active_post_stats == "on" && $groups_visits == "on") {
														$post_stats = wpqa_get_post_stats($group_id);
														echo '<li><span class="cover-count">'.himer_count_number($post_stats).'</span> '._n("View","Views",$post_stats,"himer").'</li>';
													}
												echo '</ul>
											</div><!-- /.col-boot-xl-8 -->
											<div class="col-boot-sm-12 col-boot-md-6 col-boot-lg-5 col-boot-xl-4">
												<div class="group__actions  d-flex flex-wrap align-items-center justify-content-right">';
													if ($is_super_admin || ($user_id > 0 && $user_id == $get_group->post_author)) {
														echo '<div><a href="'.esc_url_raw(add_query_arg(array("activate_delete" => true,"delete" => $group_id,"wpqa_delete_nonce" => wp_create_nonce("wpqa_delete_nonce")),$get_permalink)).'" class="button-default btn btn__sm btn__danger delete-group" data-id="'.$group_id.'">'.esc_html__("Delete","himer").'</a></div>';
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
														echo '<div class="group_join">
															<div class="cover_loader himer_hide"><div class="small_loader loader_2"></div></div>
															<a href="#" class="button-default btn btn__sm hide_button_too '.$join_leave_class.'" data-id="'.$group_id.'">'.$join_leave_text.'</a>';
															if (isset($join_leave_class_2)) {
																echo '<a href="#" class="button-default btn btn__sm hide_button_too '.$join_leave_class_2.'" data-id="'.$group_id.'">'.$join_leave_text_2.'</a>';
															}
														echo '</div>';
													}
												echo '</div><!-- /.group__actions -->
											</div><!-- /.col-boot-xl-4 -->
										</div><!-- /.row-boot -->
									</div>
								</div>
							</div><!-- /.group-card -->
						</div>';
					}
				}
			}
		}
	}
}
do_action("himer_group_cover");?>
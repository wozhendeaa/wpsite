<section class="widget card stats-widget">
	<div class="card-header d-flex align-items-center">
		<h2 class="card-title mb-0 d-flex align-items-center"><i class="icon-stats-bars font-xxl card-title__icon"></i><span><?php esc_html_e("Our Statistics","himer")?></span></h2>
	</div>	
	<div class="widget-wrap stats-card">
		<ul class="<?php echo ("style_2" == $hero_stats_style?"stats-inner-2":"stats-inner list-unstyled mb-0").($hero_stats_style == "style_3"?" stats-inner-3 stats-card-layout2":"")?>">
			<?php if (isset($hero_stats) && is_array($hero_stats) && !empty($hero_stats)) {
				$count_comment_only = himer_options("count_comment_only");
				foreach ($hero_stats as $key => $value) {
					if (isset($value["value"]) && $value["value"] == $key) {?>
						<li class="stats-card__item stats-<?php echo stripcslashes($value["value"])?>">
							<div class="<?php echo ("style_1" == $hero_stats_style?"d-flex justify-content-between stats-card__item_div":"").($hero_stats_style == "style_3"?"d-flex flex-column stats-card__item_div":"")?>">
								<?php if ($hero_stats_style == "style_1" || $hero_stats_style == "style_3") {
									echo '<div class="stats-card__item__icon">';
								}
								if ($value["value"] == "questions") {
									echo '<i class="icon-book-open"></i>';
								}else if ($value["value"] == "posts") {
									echo '<i class="icon-user"></i>';
								}else if ($value["value"] == "answers") {
									echo '<i class="icon-comment"></i>';
								}else if ($value["value"] == "comments") {
									echo '<i class="icon-chat"></i>';
								}else if ($value["value"] == "best_answers") {
									echo '<i class="icon-graduation-cap"></i>';
								}else if ($value["value"] == "users") {
									echo '<i class="icon-users"></i>';
								}else if ($value["value"] == "groups") {
									echo '<i class="icon-android-contacts"></i>';
								}else if ($value["value"] == "group_posts") {
									echo '<i class="icon-document-text"></i>';
								}
								do_action("wpqa_widget_stats_icons",$value);
								if ($hero_stats_style == "style_1" || $hero_stats_style == "style_3") {
									echo '</div>
									<div class="stats-card__item__text w-100">';
								}?>
								<span class="<?php echo ("style_2" == $hero_stats_style?"stats-text-2":"stats-text")?>">
									<?php if ($value["value"] == "questions") {
										$question_count = wp_count_posts(wpqa_questions_type);
										$questions_count = (isset($question_count->publish)?$question_count->publish:0);
										$asked_question_count = wp_count_posts(wpqa_asked_questions_type);
										$asked_questions_count = (isset($asked_question_count->publish)?$asked_question_count->publish:0);
										$questions_count = $questions_count+$asked_questions_count;
										echo _n("Question","Questions",$questions_count,"himer");
									}else if ($value["value"] == "posts") {
										$posts_count = wp_count_posts("post")->publish;
										echo _n("Post","Posts",$posts_count,"himer");
									}else if ($value["value"] == "answers") {
										$answers_count = wpqa_comments_of_post_type(array(wpqa_questions_type,wpqa_asked_questions_type),0,array(),"",($count_comment_only == "on"?0:""));
										echo _n("Answer","Answers",$answers_count,"himer");
									}else if ($value["value"] == "comments") {
										$comments_count = wpqa_comments_of_post_type("post",0,array(),"",($count_comment_only == "on"?0:""));
										echo _n("Comment","Comments",$comments_count,"himer");
									}else if ($value["value"] == "best_answers") {
										$best_answers_count = (int)get_option("wpqa_best_answer");
										echo _n("Best Answer","Best Answers",$best_answers_count,"himer");
									}else if ($value["value"] == "users") {
										$count_users = count_users();
										$users_count = 0;
										foreach ($count_users["avail_roles"] as $role => $count) {
											if ($role != "wpqa_under_review" && $role != "activation" && $role != "ban_group") {
												$users_count += $count;
											}
										}
										$users_count = (int)$users_count;
										echo _n("User","Users",$users_count,"himer");
									}else if ($value["value"] == "groups") {
										$groups_count = wp_count_posts("group");
										$groups_count = (isset($groups_count->publish)?$groups_count->publish:0);
										echo _n("Group","Groups",$groups_count,"himer");
									}else if ($value["value"] == "group_posts") {
										$group_posts_count = wpqa_count_group_posts_by_type("posts");
										echo _n("Group Post","Group Posts",$group_posts_count,"himer");
									}
									do_action("wpqa_widget_stats_text",$value);
									echo ("style_2" == $hero_stats_style?" : ":"")?>
								</span>
								<span class="<?php echo ("style_2" == $hero_stats_style?"stats-value-2":"stats-value")?>">
									<?php if ($value["value"] == "questions") {
										echo himer_count_number($questions_count);
									}else if ($value["value"] == "posts") {
										echo himer_count_number($posts_count);
									}else if ($value["value"] == "answers") {
										echo himer_count_number($answers_count);
									}else if ($value["value"] == "comments") {
										echo himer_count_number($comments_count);
									}else if ($value["value"] == "best_answers") {
										echo himer_count_number($best_answers_count);
									}else if ($value["value"] == "users") {
										echo himer_count_number($users_count);
									}else if ($value["value"] == "groups") {
										echo himer_count_number($groups_count);
									}else if ($value["value"] == "group_posts") {
										echo himer_count_number($group_posts_count);
									}
									do_action("wpqa_widget_stats_count",$value);?>
								</span>
								<?php if ($hero_stats_style == "style_1" || $hero_stats_style == "style_3") {
									echo '</div>';
								}?>
							</div>
						</li>
					<?php }
				}
			}?>
		</ul>
	</div>
</section>
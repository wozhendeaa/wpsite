<div class="hero-layout<?php echo ("full" != $hero_layout?" hero-wrap":"").($hero_posts_slider == "on"?" slider-item":"")?>">
	<?php $featured_image = get_post_meta($post->ID,'_thumbnail_id',true);?>
	<div class="hero-big-banner hero-banner d-flex flex-column justify-content-end<?php echo esc_attr($hero_questions_answer == "on"?" hero-big-banner-answer":"")?>"<?php echo stripcslashes($featured_image != ""?himer_image_style(himer_get_aq_resize_image_url(770,400)):"")?>>
		<div class="bg-overlay"></div>
		<div class="hero-big-banner-wrap">
			<?php $terms = wp_get_object_terms($post->ID,wpqa_question_tags);
			if (isset($terms) && is_array($terms) && !empty($terms)) {
				$terms = array_slice($terms,0,4);
				echo '<ul class="hero__tags list-unstyled d-flex flex-wrap">';
					$terms_array = array();
					foreach ($terms as $term) :
						if (isset($term->slug) && isset($term->name)) {
							$get_term_link = get_term_link($term);
							if (is_string($get_term_link)) {
								echo '<li><a href="'.$get_term_link.'">'.$term->name.'</a></li>';
							}
						}
					endforeach;
				echo '</ul>';
			}?>
			<h3 class="hero__title"><a href="<?php echo the_permalink()?>"><?php echo himer_excerpt_title(30)?></a></h3>
			<div class="hero__meta d-flex flex-wrap align-items-center">
				<ul class="hero__meta-comments list-unstyled d-flex flex-wrap mb-0">
					<?php if ($activate_male_female == true && $post_type == wpqa_questions_type) {
						$count_comments_female = (int)(has_wpqa()?wpqa_count_comments($post->ID,"female_count_comments","like_meta"):get_comments_number());
						$count_comments_male = (int)(has_wpqa()?wpqa_count_comments($post->ID,"male_count_comments","like_meta"):get_comments_number());
						$gender_answers_other = himer_options("gender_answers_other");
						if ($gender_answers_other == "on") {
							$count_post_comments = (int)(has_wpqa()?wpqa_count_comments($post->ID,"count_post_comments","like_meta"):get_comments_number());
							$count_comments_other = (int)($count_post_comments-($count_comments_female+$count_comments_male));
						}?>
						<li class="her-user"><a href="<?php echo get_permalink()?>#comments-female"><i class="icon-android-chat"></i><?php echo himer_count_number($count_comments_female)?></a></li>
						<li class="him-user"><a href="<?php echo get_permalink()?>#comments-male"><i class="icon-android-chat"></i><?php echo himer_count_number($count_comments_male)?></a></li>
						<?php if ($gender_answers_other == "on" && $count_comments_other > 0) {?>
							<li class="other-user"><a href="<?php echo get_permalink()?>#comments-other"><i class="icon-android-chat"></i><?php echo himer_count_number($count_comments_other)?></a></li>
						<?php }
					}else {
						$count_post_all = (int)(has_wpqa()?wpqa_count_comments($post->ID):get_comments_number());?>
						<li><a href="<?php echo get_permalink()?>#comments"><i class="icon-android-chat"></i><?php echo himer_count_number($count_post_all)?></a></li>
					<?php }?>
				</ul><!-- /.hero__author-comments -->
				<div class="hero__author-meta d-flex flex-wrap align-items-center">
					<?php $post_author = $post->post_author;
					$get_gender_class = (has_wpqa()?wpqa_get_gender_class($post_author,$post->ID):"");?>
					<div class="author__avatar <?php echo esc_attr($get_gender_class)?>">
						<?php $question_email = "";
						if ($post_author > 0) {
							$question_username = get_the_author_meta('display_name',$post_author);
						}else {
							$anonymously_question = himer_post_meta("anonymously_question","",false);
							$anonymously_user = himer_post_meta("anonymously_user","",false);
							if (($anonymously_question == "on" || $anonymously_question == 1) && $anonymously_user != "") {
								$question_username = esc_html__('Anonymous','himer');
							}else {
								$question_email = himer_post_meta("question_email","",false);
								$question_username = himer_post_meta("question_username","",false);
								$question_username = ($question_username != ""?$question_username:esc_html__('[Deleted User]','himer'));
							}
						}
						do_action("wpqa_action_avatar_link",array("user_id" => (isset($post_author) && $post_author > 0?$post_author:0),"size" => 25,"span" => "span","class" => "rounded-circle","name" => $question_username,"email" => (isset($question_email) && $question_email != ""?$question_email:"")));?>
					</div>
					<h2 class="author__title mb-0">
						<?php if ($post_author > 0) {
							$get_author_profile = (has_wpqa()?wpqa_profile_url($post_author):"");
							if (isset($get_author_profile)) {
								echo '<a class="post-author" href="'.esc_url($get_author_profile).'">';
							}
							echo esc_html($question_username);
							if (isset($get_author_profile)) {
								echo '</a>';
							}
						}else {
							echo '<span class="question-author-un">'.esc_html($question_username).'</span>';
						}?>
					</h2>
				</div><!-- /.hero__author-meta -->
			</div><!-- /.hero__meta -->
		</div>
	</div><!-- /.hero-banner -->
	<?php if ("full" != $hero_layout && $hero_posts == "questions" && $hero_questions_answer == "on") {
		$comments = array();
		$meta_query_female = array("meta_query" => array(array("key" => "wpqa_comment_gender","value" => 2,"compare" => "=")));
		$meta_query_male = array("meta_query" => array(array("key" => "wpqa_comment_gender","value" => 1,"compare" => "=")));
		$meta_query_best = array("meta_query" => array(array("key" => "best_answer_comment","value" => "best_answer_comment","compare" => "=")));
		$author__not_in = array();
		$block_users = himer_options("block_users");
		if ($block_users == "on") {
			$user_id = get_current_user_id();
			if ($user_id > 0) {
				$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
				if (is_array($get_block_users) && !empty($get_block_users)) {
					$author__not_in = array("author__not_in" => $get_block_users);
				}
			}
		}
		$args_all = array('orderby' => array('meta_value_num' => 'DESC','comment_date' => 'ASC'),'meta_key' => 'comment_vote','order' => 'DESC');
		$args = array('parent' => 0,'post_id' => $post->ID,'status' => 'approve','number' => 1);
		if ($hero_questions_answer_style == "last_answer") {
			$args_last = array_merge($author__not_in,$args,$args_all);
			$comments_last = get_comments($args_last);
			$comments["last"] = $comments_last;
		}
		if ($hero_questions_answer_style == "female" || $hero_questions_answer_style == "male_female") {
			$args_female = array_merge($meta_query_female,$author__not_in,$args);
			$comments_female = get_comments($args_female);
			$comments["female"] = $comments_female;
		}
		if ($hero_questions_answer_style == "male" || $hero_questions_answer_style == "male_female") {
			$args_male = array_merge($meta_query_male,$author__not_in,$args);
			$comments_male = get_comments($args_male);
			$comments["male"] = $comments_male;
		}
		if ($hero_questions_answer_style == "best_answer") {
			$args_best = array_merge($meta_query_best,$author__not_in,$args);
			$comments_best = get_comments($args_best);
			$comments["best"] = $comments_best;
		}
		if (($hero_questions_answer_style == "best_answer" && isset($comments_best) && is_array($comments_best) && !empty($comments_best)) || (($hero_questions_answer_style == "female" || $hero_questions_answer_style == "male_female") && isset($comments_female) && is_array($comments_female) && !empty($comments_female)) || (($hero_questions_answer_style == "male" || $hero_questions_answer_style == "male_female") && isset($comments_male) && is_array($comments_male) && !empty($comments_male))) {?>
			<div class="hero-answers-wrap">
				<?php foreach ($comments as $key => $comments_value) {
					if (is_array($comments_value) && !empty($comments_value)) {
						foreach ($comments_value as $comment) {
							if ($key == "male") {
								$class = "him-user";
								$title = esc_html__("Him Answer","himer");
							}else if ($key == "female") {
								$class = "her-user";
								$title = esc_html__("Her Answer","himer");
							}else if ($key == "best") {
								$class = "head-best-answer";
								$title = esc_html__("Best Answer","himer");
							}else if ($key == "last") {
								$class = "head-last-answer";
								$title = esc_html__("Last Answer","himer");
							}
							$comment_id = (int)$comment->comment_ID;?>
							<div class="answers-area mb-0<?php echo (isset($class)?" ".$class:"")?>">
								<?php if (isset($title)) {
									echo '<div class="answers__header"><h2 class="answers__title mb-0">'.$title.'</h2></div>';
								}?>
								<div class="answers__body">
									<article class="widget widget-article<?php echo (isset($class)?" ".$class:"")?>">
										<div class="article-body">
											<p class="article__description">
												<a href="<?php echo get_comment_link($comment_id)?>">
													<?php $comment_excerpt_count = apply_filters('himer_answer_number',300);
													echo wp_html_excerpt($comment->comment_content,$comment_excerpt_count);?>
												</a>
											</p>
										</div><!-- /.article-body -->
										<div class="d-flex align-items-center">
											<?php $comment_user_id = (int)$comment->user_id;
											echo wpqa_get_avatar_link(array("user_id" => $comment_user_id,"size" => 25,"span" => "span","class" => "rounded-circle"))?>
											<div class="d-flex align-items-center flex-wrap">
												<?php $user = get_user_by('id',$comment_user_id);
												$deleted_user = ($comment_user_id > 0 && isset($user->display_name)?$user->display_name:($comment_user_id == 0?$comment->comment_author:"delete"));
												if ($comment_user_id > 0 && $deleted_user != "delete") {
													$get_author_profile = (has_wpqa()?wpqa_profile_url($comment_user_id):"");
												}else {
													$get_author_profile = ($comment->comment_author_url != "" && $deleted_user != "delete"?$comment->comment_author_url:"himer_No_site");
												}
												if ($get_author_profile != "" && $get_author_profile != "himer_No_site") {?>
													<a class="article__author mr-3" href="<?php echo esc_url($get_author_profile)?>">
												<?php }
													$anonymously_user = get_comment_meta($comment_id,"anonymously_user",true);
													echo ("delete" == $deleted_user?esc_html__("[Deleted User]","himer"):($anonymously_user != ""?esc_html__("Anonymous","himer"):$deleted_user));
												if ($get_author_profile != "" && $get_author_profile != "himer_No_site") {?>
													</a>
												<?php }?>
											</div>
										</div><!-- /.article-header -->
									</article><!-- /.widget-article -->
								</div><!-- /.answers__body -->
							</div><!-- /.answers-area -->
						<?php }
					}
				}?>
			</div>
		<?php }
	}?>
</div>
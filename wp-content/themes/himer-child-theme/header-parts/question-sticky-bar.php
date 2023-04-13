<div class="sticky-content bg-white">
	<div class="container">
		<div class="row-boot align-items-center">
			<div class="col-boot-sm-12 col-boot-md-12 col-boot-lg-8 py-1">
				<nav class="breadcrumb-wrapper d-flex align-items-center">
					<?php $question_navigation = himer_options("question_navigation");
					$question_nav_category = himer_options("question_nav_category");
					if ($question_navigation == "on") {
						if ($question_nav_category == "on") {
							$previous_post = get_previous_post(true,'',wpqa_question_categories);
							$next_post = get_next_post(true,'',wpqa_question_categories);
						}else {
							$previous_post = get_previous_post();
							$next_post = get_next_post();
						}
						$get_gender_class = (has_wpqa()?wpqa_get_gender_class($post->post_author,$post->ID):"");
						if ((isset($next_post) && is_object($next_post)) || (isset($previous_post) && is_object($previous_post))) {?>
							<div class="breadcrumb-navs d-flex <?php echo esc_attr($get_gender_class)?>">
								<?php if (isset($next_post) && is_object($next_post)) {?>
									<a href="<?php echo get_permalink($next_post->ID)?>" class="breadcrumb-navs__item mr-1"><i class="icon-arrow-left-b"></i></a>
								<?php }
								if (isset($previous_post) && is_object($previous_post)) {?>
									<a href="<?php echo get_permalink($previous_post->ID)?>" class="breadcrumb-navs__item"><i class="icon-arrow-right-b"></i></a>
								<?php }?>
							</div>
						<?php }
					}?>
					<h2 class="sticky__title mb-0"><?php echo himer_excerpt_title(10)?></h2>
				</nav><!-- /.breadcrumb-wrapper -->
			</div><!-- /.col-boot-lg-8 -->
			<div class="col-boot-sm-12 col-boot-md-12 col-boot-lg-4 d-flex justify-content-lg-end py-1">
				<div class="answers-stats d-flex">
					<?php $activate_male_female = apply_filters("wpqa_activate_male_female",false);
					if ($activate_male_female == true) {
						$count_comments_female = (int)(has_wpqa()?wpqa_count_comments($post->ID,"female_count_comments","like_meta"):get_comments_number());
						$count_comments_male = (int)(has_wpqa()?wpqa_count_comments($post->ID,"male_count_comments","like_meta"):get_comments_number());?>
						<div class="her-user"><a class="answer-meta-gender answer-meta-her" href="<?php echo get_permalink()?>#comments-female"><i class="icon-female answer__icon"></i><span class="answer__count"><?php echo himer_count_number($count_comments_female)?></span></a></div>
						<div class="him-user"><a class="answer-meta-gender answer-meta-him" href="<?php echo get_permalink()?>#comments-male"><i class="icon-male answer__icon"></i><span class="answer__count"><?php echo himer_count_number($count_comments_male)?></span></a></div>
						<?php $gender_answers_other = himer_options("gender_answers_other");
						if ($gender_answers_other == "on") {
							$count_post_comments = (int)(has_wpqa()?wpqa_count_comments($post->ID,"count_post_comments","like_meta"):get_comments_number());
							$count_comments_other = (int)($count_post_comments-($count_comments_female+$count_comments_male));
						}
						if ($gender_answers_other == "on" && $count_comments_other > 0) {?>
							<div class="other-user"><a class="answer-meta-gender answer-meta-other" href="<?php echo get_permalink()?>#comments-other"><i class="icon-android-chat answer__icon"></i><span class="answer__count"><?php echo himer_count_number($count_comments_other)?></span></a></div>
						<?php }
					}else {
						$count_post_all = (int)(has_wpqa()?wpqa_count_comments($post->ID):get_comments_number());?>
						<div><a class="comments-link" href="<?php echo get_permalink()?>#comments"><i class="icon-android-chat answer__icon"></i><span class="answer__count"><?php echo himer_count_number($count_post_all)?></span></a></div>
					<?php }?>
				</div><!-- /.answers-stats -->
				<?php $question_meta = himer_options("question_meta");
				$question_answer = (isset($question_meta["question_answer"]) && $question_meta["question_answer"] == "question_answer"?"on":"");
				if ($post->comment_status == "open" && $question_answer == "on") {?>
					<a href="<?php echo get_permalink()?>#respond" class="meta-answer btn btn__primary btn__semi__height"><?php esc_html_e("Leave An Answer!","himer")?></a>
				<?php }?>
			</div><!-- /.col-boot-lg-4-->
		</div><!-- /.row-boot -->
	</div><!-- /.container -->
</div><!-- /.sticky-content -->
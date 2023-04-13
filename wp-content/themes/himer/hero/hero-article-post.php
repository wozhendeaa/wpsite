<div class="col-boot-sm-6 col-boot-md-4 col-boot-lg-3 hero-layout3">
	<article class="widget widget-article her-user">
		<div class="article__img">
			<?php $featured_image = get_post_meta($post->ID,'_thumbnail_id',true);
			if ($featured_image != "") {
				echo himer_get_aq_resize_img(270,167);
			}?>
		</div>
		<div class="article-body">
			<?php $terms = wp_get_object_terms($post->ID,($post_type == wpqa_questions_type?wpqa_question_tags:'post_tag'));
			if (isset($terms) && is_array($terms) && !empty($terms)) {
				$terms = array_slice($terms,0,2);
				echo '<ul class="tags-list d-flex flex-wrap list-unstyled">';
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
			}
			$count_comments_female = (int)(has_wpqa()?wpqa_count_comments($post->ID,"female_count_comments","like_meta"):get_comments_number());
			$count_comments_male = (int)(has_wpqa()?wpqa_count_comments($post->ID,"male_count_comments","like_meta"):get_comments_number());
			$gender_answers_other = himer_options("gender_answers_other");
			if ($gender_answers_other == "on") {
				$count_post_comments = (int)(has_wpqa()?wpqa_count_comments($post->ID,"count_post_comments","like_meta"):get_comments_number());
				$count_comments_other = (int)($count_post_comments-($count_comments_female+$count_comments_male));
			}?>
			<h4 class="article__title"><a href="<?php echo the_permalink()?>"><?php echo himer_excerpt_title(15)?></a></h4>
			<footer class="article-footer d-flex align-items-center justify-content-between">
				<div class="footer-meta d-flex align-items-center<?php echo esc_attr($activate_male_female == true && $post_type == wpqa_questions_type && $gender_answers_other == "on" && $count_comments_other > 0?" footer-meta-other":"")?>">
					<a class="article__date color-body mr-3" href="<?php echo get_permalink()?>"><?php echo esc_html(get_the_time(himer_date_format,$post->ID));?></a>
					<ul class="footer-meta__comments list-unstyled mb-0 d-flex">
						<?php if ($activate_male_female == true && $post_type == wpqa_questions_type) {?>
							<li class="her-user"><a href="<?php echo get_permalink()?>#comments-female"><i class="icon-android-chat"></i><?php echo himer_count_number($count_comments_female)?></a></li>
							<li class="him-user"><a href="<?php echo get_permalink()?>#comments-male"><i class="icon-android-chat"></i><?php echo himer_count_number($count_comments_male)?></a></li>
							<?php if ($gender_answers_other == "on" && $count_comments_other > 0) {?>
								<li class="other-user"><a href="<?php echo get_permalink()?>#comments-other"><i class="icon-android-chat"></i><?php echo himer_count_number($count_comments_other)?></a></li>
							<?php }
						}else {
							$count_post_all = (int)(has_wpqa()?wpqa_count_comments($post->ID):get_comments_number());?>
							<li><a href="<?php echo get_permalink()?>#comments"><i class="icon-android-chat"></i><?php echo himer_count_number($count_post_all)?></a></li>
						<?php }?>
					</ul>
				</div><!-- /.footer-meta -->
			</footer><!-- /.article-footer -->
		</div><!-- /.article-body -->
	</article>
</div><!-- /.col-boot-lg-3 -->
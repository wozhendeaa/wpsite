<?php $featured_image = get_post_meta($post->ID,'_thumbnail_id',true);?>
<div class="hero-banner hero-banner-mini d-flex flex-column justify-content-end"<?php echo stripcslashes($featured_image != ""?himer_image_style(himer_get_aq_resize_image_url(370,203)):"")?>>
	<div class="bg-overlay"></div>
	<div class="hero-banner-mini-wrap">
		<h3 class="hero__title"><a href="<?php echo the_permalink()?>"><?php echo himer_excerpt_title(15)?></a></h3>
		<div class="hero__meta d-flex flex-wrap align-items-center">
			<a class="hero__meta-date mr-3" href="<?php echo get_permalink()?>"><?php echo esc_html(get_the_time(himer_date_format,$post->ID));?></a>
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
		</div><!-- /.hero__meta -->
	</div>
</div><!-- /.hero-banner -->
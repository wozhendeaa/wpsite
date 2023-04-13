<?php if (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)) {
	$related_number        = $related_number_question;
	$excerpt_related_title = $related_title_question;
	$query_related         = $query_related_question;
}
$excerpt_related_title = isset($excerpt_related_title) ? $excerpt_related_title : 10;
$related_number        = $related_number ? $related_number : 4;
if (is_singular("post")) {
	$related_number_sidebar = $related_number_sidebar ? $related_number_sidebar : 6;
	$related_number_sidebar = $related_style == "links"?$related_number:$related_number_sidebar;
	$related_number_full    = $related_number_full ? $related_number_full : 8;
	$related_number_full    = $related_style == "links"?$related_number:$related_number_full;
}

$get_question_user_id = get_post_meta($post->ID,"user_id",true);
if ($query_related == "tags" && esc_html($get_question_user_id) == "") {
	if (is_singular(wpqa_questions_type)) {
		$term_list = wp_get_post_terms($post->ID, wpqa_question_tags, array('fields' => 'ids'));
		$related_query_ = array('tax_query' => array(array('taxonomy' => wpqa_question_tags,'field' => 'id','terms' => $term_list,'operator' => 'IN')));
	}else {
		$term_list = wp_get_post_terms($post->ID, 'post_tag', array("fields" => "ids"));
		$related_query_ = array('tag__in' => $term_list);
	}
}else if ($query_related == "author" || esc_html($get_question_user_id) != "") {
	$related_query_ = (esc_html($get_question_user_id) != ""?array():array('author' => $post->post_author));
}else {
	if (is_singular(wpqa_questions_type)) {
		$categories = wp_get_post_terms($post->ID,wpqa_question_categories,array('fields' => 'ids'));
		$related_query_ = array('tax_query' => array(array('taxonomy' => wpqa_question_categories,'field' => 'id','terms' => $categories,'operator' => 'IN')));
	}else {
		$categories = get_the_category($post->ID);
		$category_ids = array();
		foreach ($categories as $l_category) {
			$category_ids[] = $l_category->term_id;
		}
		$related_query_ = array('category__in' => $category_ids);
	}
}

if (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type) || (isset($related_style) && $related_style == "links")) {
	$args_images = array();
}else {
	$args_images = array('meta_key' => '_thumbnail_id');
}

if (is_single()) {
	if ($theme_sidebar == "centered") {
		$post_width = 341;
		$post_height = 190;
		$related_post_columns = "col6 col-boot-sm-6";
	}else if ($theme_sidebar == "menu_sidebar") {
		$post_width = 184;
		$post_height = 110;
		$related_post_columns = "col4 col-boot-sm-4";
		if (is_singular("post")) {
			$related_number = $related_number_sidebar;
		}
	}else if ($theme_sidebar == "menu_left") {
		$post_width = 284;
		$post_height = 165;
		$related_post_columns = "col4 col-boot-sm-4";
		if (is_singular("post")) {
			$related_number = $related_number_sidebar;
		}
	}else if ($theme_sidebar == "full") {
		$post_width = 255;
		$post_height = 150;
		$related_post_columns = "col3 col-boot-sm-3";
		if (is_singular("post")) {
			$related_number = $related_number_full;
		}
	}else {
		$post_width = 256;
		$post_height = 150;
		$related_post_columns = "col4 col-boot-sm-4";
		if (is_singular("post")) {
			$related_number = $related_number_sidebar;
		}
	}
}

$show_defult_image = apply_filters('himer_show_defult_image_post',true);
if ($show_defult_image != true) {
	$post_width = "";
	$post_height = "";
}

$block_users = himer_options("block_users");
$author__not_in = array();
if ($block_users == "on") {
	$user_id = get_current_user_id();
	if ($user_id > 0) {
		$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
		if (is_array($get_block_users) && !empty($get_block_users)) {
			$author__not_in = array("author__not_in" => $get_block_users);
		}
	}
}

$args = array_merge($args_images,$related_query_,$author__not_in,array('post_type' => $post->post_type,'post__not_in' => array($post->ID),'posts_per_page'=> $related_number,'cache_results' => false,'no_found_rows' => true,"meta_query" => array((esc_html($get_question_user_id) != ""?array("type" => "numeric","key" => "user_id","value" => (int)$get_question_user_id,"compare" => "="):array()))));
$related_query = new WP_Query($args);

if (($query_related == "tags" || $query_related == "author") && !$related_query->have_posts()) {
	if (is_singular(wpqa_questions_type)) {
		$categories = wp_get_post_terms($post->ID,wpqa_question_categories,array('fields' => 'ids'));
		$related_query_ = array('tax_query' => array(array('taxonomy' => wpqa_question_categories,'field' => 'id','terms' => $categories,'operator' => 'IN')));
	}else {
		$categories = get_the_category($post->ID);
		$category_ids = array();
		foreach ($categories as $l_category) {
			$category_ids[] = $l_category->term_id;
		}
		$related_query_ = array('category__in' => $category_ids);
	}
	$args = array_merge($args_images,$related_query_,$author__not_in,array('post_type' => $post->post_type,'post__not_in' => array($post->ID),'posts_per_page'=> $related_number,'cache_results' => false,'no_found_rows' => true,"meta_query" => array((esc_html($get_question_user_id) != ""?array("type" => "numeric","key" => "user_id","value" => (int)$get_question_user_id,"compare" => "="):array()))));
	$related_query = new WP_Query($args);
}

if ($related_query->have_posts()) {
	if (is_singular(wpqa_questions_type)) {
		$question_related_style = himer_options("question_related_style");
		if ($question_related_style == "box_style") {
			$question_related_slider = himer_options("question_related_slider");
			$k = 0;?>
			<h3 class="section__ttile"><?php esc_html_e('Related Questions',"himer")?></h3>
			<div class="popular-questions mb-2rem">
				<div class="card">
					<div class="card-body <?php echo stripcslashes($question_related_slider =="on"?"slider-wrap slider-questions-wrap":"related-questions-wrap")?>">
						<?php if ($question_related_slider == "on") {
							echo '<div class="slider-owl">';
						}
							while ( $related_query->have_posts() ) : $related_query->the_post();
								$k++;
								if ($k == 1) {
									echo '<div class="'.($question_related_slider == "on"?'slider-item ':'').'row-boot row-gutter-20">';
								}?>
								<article class="col-boot-sm-6 col-boot-md-6">
									<div class="widget widget-article <?php echo stripcslashes($question_related_slider == "on"?'mb-0':'mb-4')?>">
										<div class="article-body">
											<h4 class="article__title"><a href="<?php echo get_permalink()?>" title="<?php printf('%s', the_title_attribute('echo=0')); ?>" rel="bookmark"><?php echo himer_excerpt_title($excerpt_related_title)?></a></h4>
										</div><!-- /.article-body -->
										<footer class="article-footer d-flex align-items-center justify-content-between">
											<div class="footer-meta d-flex flex-wrap align-items-center">
												<a class="article__date color-body mr-3" href="<?php echo get_permalink()?>" title="<?php printf('%s', the_title_attribute('echo=0')); ?>" rel="bookmark"><?php echo esc_html(get_the_time(himer_date_format,$post->ID));?></a>
												<ul class="footer-meta__comments list-unstyled mb-0 d-flex">
													<?php if ($activate_male_female == true) {
														$gender_answers_other = himer_options("gender_answers_other");
														$count_comments_female = (int)(has_wpqa()?wpqa_count_comments($post->ID,"female_count_comments","like_meta"):get_comments_number());
														$count_comments_male = (int)(has_wpqa()?wpqa_count_comments($post->ID,"male_count_comments","like_meta"):get_comments_number());
														if ($gender_answers_other == "on") {
															$count_post_comments = (int)(has_wpqa()?wpqa_count_comments($post->ID,"count_post_comments","like_meta"):get_comments_number());
															$count_comments_other = (int)($count_post_comments-($count_comments_female+$count_comments_male));
														}?>
														<li class="her-user"><a href="<?php echo get_permalink()?>#comments-female"><i class="icon-android-chat"></i><?php echo himer_count_number($count_comments_female)?></a></li>
														<li class="him-user"><a href="<?php echo get_permalink()?>#comments-male"><i class="icon-android-chat"></i><?php echo himer_count_number($count_comments_male)?></a></li>
														<?php if ($gender_answers_other == "on" && $count_comments_other > 0) {?>
															<li class="other-user"><a href="<?php echo get_permalink()?>#comments-other"><i class="icon-android-chat"></i><?php echo himer_count_number($count_comments_other)?></a></li>
														<?php }
													}else {?>
														<li><a href="<?php echo get_permalink()?>#comments"><i class="icon-android-chat"></i><?php himer_meta("","","on","","","",$post->ID,$post)?></a></li>
													<?php }?>
												</ul>
											</div><!-- /.footer-meta -->
										</footer><!-- /.article-footer -->
									</div>
								</article><!-- /.widget-article -->
								<?php if ($k == 2) {
									echo '</div>';
									$k = 0;
								}
							endwhile;
							if ($question_related_slider == "on") {
								echo '</div>';
							}?>
					</div><!-- /.card-body -->
				</div><!-- /.card -->
			</div><!-- /.popular-questions -->
		<?php }
	}

	if (is_singular("post") || (is_singular(wpqa_questions_type) && $question_related_style != "box_style")) {?>
		<div class="related-post block-section-div<?php echo (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type) || $related_style == "links"?" related-post-links":"").(is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)?" related-questions":"")?>">
			<div class="post-inner">
				<h3 class="section-title"><?php echo (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)?esc_html__('Related Questions',"himer"):esc_html__('Related Posts',"himer"))?></h3>
				<?php if (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type) || $related_style == "links") {
					echo '<ul class="list-unstyled mb-0">';
				}else {
					echo '<div class="row row-boot">';
				}
					while ( $related_query->have_posts() ) : $related_query->the_post();
						if ((is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type) || $related_style == "links") && $excerpt_related_title > 0) {?>
							<li>
								<a<?php echo apply_filters("himer_post_link_target","")?> href="<?php the_permalink();?>" title="<?php printf('%s', the_title_attribute('echo=0')); ?>" rel="bookmark"><i class="icon-right-thin"></i><?php himer_excerpt_title($excerpt_related_title)?></a>
							</li>
						<?php }else {?>
							<div class="col <?php echo esc_attr($related_post_columns)?>">
								<div <?php post_class('clearfix');?>>
									<div class="related-image">
										<a<?php echo apply_filters("himer_post_link_target","")?> href="<?php echo esc_url(get_permalink())?>">
											<?php echo (isset($post_width) && isset($post_height)?himer_get_aq_resize_img($post_width,$post_height):"");?>
										</a>
									</div>
									<?php if ($date_in_related == "on" || $comment_in_related == "on") {?>
										<div class="post-meta clearfix">
											<?php himer_meta($date_in_related,"",$comment_in_related,"","","",$post->ID,$post)?>
										</div>
									<?php }?>
									<h2 class="post-title"><a class="post-title"<?php echo apply_filters("himer_post_link_target","")?> href="<?php echo esc_url(get_permalink())?>" title="<?php printf('%s', the_title_attribute('echo=0')); ?>" rel="bookmark"><?php himer_excerpt_title($excerpt_related_title)?></a></h2>
								</div>
							</div>
						<?php }
					endwhile;
				if (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type) || $related_style == "links") {
					echo '</ul>';
				}else {
					echo '</div>';
				}?>
				<div class="clearfix"></div>
			</div>
		</div><!-- End related-post -->
	<?php }
}
wp_reset_postdata();?>
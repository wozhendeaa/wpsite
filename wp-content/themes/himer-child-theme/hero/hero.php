<?php $activate_male_female = apply_filters("wpqa_activate_male_female",false);
$post__not_in = array();
$custom_heros = himer_post_meta("custom_heros");
if ((is_single() || is_page()) && $custom_heros == "on") {
	$hero_h_logged = apply_filters("himer_hero_logged",himer_post_meta("hero_h_logged"));
}else {
	$hero_h_logged = apply_filters("himer_hero_logged",himer_options("hero_h_logged"));
}
if ((is_user_logged_in() && ($hero_h_logged == "logged" || $hero_h_logged == "both")) || (!is_user_logged_in() && ($hero_h_logged == "unlogged" || $hero_h_logged == "both"))) {
	if ((is_single() || is_page()) && $custom_heros == "on") {
		$hero_h = apply_filters("himer_hero",himer_post_meta("hero_h"));
		$hero_layout = apply_filters("himer_hero_layout",himer_post_meta("hero_layout"));
	}else {
		$hero_h = apply_filters("himer_hero",himer_options("hero_h"));
		$hero_layout = apply_filters("himer_hero_layout",himer_options("hero_layout"));
		$hero_h_home_pages = apply_filters("himer_hero_home_pages",himer_options("hero_h_home_pages"));
		$hero_h_pages = apply_filters("himer_hero_h_pages",himer_options("hero_h_pages"));
		$hero_h_pages = explode(",",$hero_h_pages);
	}
	$hero_h = apply_filters("himer_hero_h",$hero_h);
	$hero_h_home_pages = apply_filters("himer_hero_home_pages",(isset($hero_h_home_pages)?$hero_h_home_pages:""));
	if ($hero_h == "on" && ($custom_heros == "on" || (((is_front_page() || is_home()) && $hero_h_home_pages == "home_page") || $hero_h_home_pages == "all_pages" || ($hero_h_home_pages == "all_posts" && is_singular("post")) || ($hero_h_home_pages == "all_questions" && (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type))) || ($hero_h_home_pages == "custom_pages" && is_page() && isset($hero_h_pages) && is_array($hero_h_pages) && isset($post->ID) && in_array($post->ID,$hero_h_pages))))) {?>
		<section class="hero-section<?php echo ("left" == $hero_layout?" hero-section-left":"")?>">
			<div class="container">
				<?php if ((is_single() || is_page()) && $custom_heros == "on") {
					$custom_hero = apply_filters("himer_custom_hero",himer_post_meta("custom_hero"));
					$custom_hero_slides = himer_post_meta("custom_hero_slides");
				}else {
					$custom_hero = apply_filters("himer_custom_hero",himer_options("custom_hero"));
					$custom_hero_slides = himer_options("custom_hero_slides");
				}
				$custom_hero_slides = apply_filters("himer_custom_hero_slides",$custom_hero_slides);
				if ($custom_hero == "custom") {
					echo "<div class='col-boot-sm-12 col-boot-md-12 col-boot-lg-12'>".do_shortcode(stripslashes($custom_hero_slides))."</div>";
				}else {
					if ((is_single() || is_page()) && $custom_heros == "on") {
						$add_hero_slides = apply_filters("himer_heros",himer_post_meta("add_hero_slides"));
						$hero_sidebar = himer_post_meta("hero_sidebar");
						$hero_posts = himer_post_meta("hero_posts");
						$hero_stats = himer_post_meta("hero_stats");
						$hero_stats_style = himer_post_meta("hero_stats_style");
						$hero_questions_answer = himer_post_meta("hero_questions_answer");
						$hero_questions_answer_style = himer_post_meta("hero_questions_answer_style");
						$hero_posts_slider = himer_post_meta("hero_posts_slider");
						$hero_posts_number = himer_post_meta("hero_posts_number");
						$hero_posts_full = himer_post_meta("hero_posts_full");
						$hero_small_posts_meta = himer_post_meta("hero_small_posts_meta");
					}else {
						$add_hero_slides = apply_filters("himer_heros",himer_options("add_hero_slides"));
						$hero_sidebar = himer_options("hero_sidebar");
						$hero_posts = himer_options("hero_posts");
						$hero_stats = himer_options("hero_stats");
						$hero_stats_style = himer_options("hero_stats_style");
						$hero_questions_answer = himer_options("hero_questions_answer");
						$hero_questions_answer_style = himer_options("hero_questions_answer_style");
						$hero_posts_slider = himer_options("hero_posts_slider");
						$hero_posts_number = himer_options("hero_posts_number");
						$hero_posts_full = himer_options("hero_posts_full");
						$hero_small_posts_meta = himer_options("hero_small_posts_meta");
					}
					$post_type = ($hero_posts == 'questions'?wpqa_questions_type:'post');
					$add_hero_slides = apply_filters("himer_add_hero_slides",$add_hero_slides);
					if ($custom_hero == "posts" && $hero_posts_slider == "on") {
						if ($hero_layout != "full") {
							if ($hero_sidebar == "small_banner") {
								$activate_slider = true;
								$end_slider = 2;
							}else if ($hero_sidebar != "small_banner") {
								$activate_slider = true;
							}else if ($hero_posts_number > 3) {
								$activate_slider = true;
								$end_slider = 3;
							}
						}else if ($hero_layout == "full") {
							if (($hero_posts_full == "banner_article" && $hero_posts_number > 3) || ($hero_posts_full == "big_banner" && $hero_posts_number > 2) || ($hero_posts_full == "article_style" && $hero_posts_number > 4) || ($hero_posts_full == "small_banner" && $hero_posts_number > 6)) {
								if ($hero_posts_full == "banner_article") {
									$end_slider = 3;
								}else if ($hero_posts_full == "big_banner") {
									$end_slider = 2;
								}else if ($hero_posts_full == "article_style") {
									$end_slider = 4;
								}else if ($hero_posts_full == "small_banner") {
									$end_slider = 6;
								}
								$activate_slider = true;
							}
						}
					}
					if ("full" == $hero_layout) {
						if ($custom_hero == "posts") {
							if ($hero_posts_full == "banner_article") {
								$row_class = "col-boot-lg-6";
							}else {
								$row_class = "col-boot-lg-12";
							}
						}else {
							$row_class = "col-boot-lg-12 mb-4";
						}
					}else {
						$row_class = "col-boot-lg-8";
					}?>
					<div class="row-boot<?php echo stripcslashes($hero_layout != "full" && $hero_sidebar == "stats" && $custom_hero == "posts"?" hero_layout_stats":"").(($hero_layout == "full" || ($hero_sidebar == "small_banner" && $hero_layout != "full")) && isset($activate_slider)?" js-activate-slider":"").($custom_hero == "posts" && $hero_posts_slider == "on"?" hero-wrap-slider":"")?>"<?php echo (isset($activate_slider) && isset($end_slider)?" data-js='".$end_slider."'":"")?>>
						<?php if (($hero_layout == "full" || ($hero_sidebar == "small_banner" && $hero_layout != "full")) && isset($activate_slider)) {
							echo '<div class="slider-wrap"><div class="slider-owl">';
						}
						if ((($custom_hero == "posts" && "full" != $hero_layout) || ($custom_hero == "posts" && "full" == $hero_layout && ($hero_posts_full == "banner_article" || $hero_posts_full == "big_banner"))) || ($custom_hero == "slider" && is_array($add_hero_slides) && !empty($add_hero_slides))) {?>
								<?php if ($custom_hero == "posts" && $hero_posts_full == "big_banner") {
									$its_big_questions = true;
								}
								if (!isset($its_big_questions)) {?>
									<div class="col-boot-sm-12 col-boot-md-12 <?php echo esc_attr($row_class)?>">
										<div class="slider-wrap">
											<?php if ($custom_hero == "slider" && is_array($add_hero_slides) && !empty($add_hero_slides) && count($add_hero_slides) > 1) {?>
												<div class='slider-owl'>
											<?php }
								}
								if ($hero_sidebar != "small_banner" && $hero_layout != "full" && isset($activate_slider)) {
									echo '<div class=""><div class="slider-owl">';
								}
								if (($custom_hero == "posts" && "full" != $hero_layout) || ($custom_hero == "posts" && "full" == $hero_layout && ($hero_posts_full == "banner_article" || $hero_posts_full == "big_banner"))) {
									$question_args = ($post_type == wpqa_questions_type?array('orderby' => 'meta_value_num','order' => 'DESC',"meta_query" => array(array('type' => 'numeric',"key" => "count_post_comments"))):array());
									$posts_per_page = (($hero_layout == "full" && $hero_posts_full == "banner_article") || ($hero_layout != "full" && !isset($activate_slider))?1:($hero_layout != "full" && $hero_sidebar == "small_banner"?1:$hero_posts_number));
									$posts_per_page = ($posts_per_page > 0?$posts_per_page:1);
									$args = array_merge($question_args,array('post_type' => $post_type,'posts_per_page' => $posts_per_page,'cache_results' => false,'no_found_rows' => true));
									$question_query = new WP_Query($args);
									if ($question_query->have_posts()) {
										while ($question_query->have_posts()) : $question_query->the_post();
											if ($hero_posts_full == "big_banner") {
												echo '<div class="col-boot-sm-12 col-boot-md-12 col-boot-lg-6"><div class="slider-wrap"><div>';
											}
											$post__not_in[] = $post->ID;
											include locate_template("hero/hero-big-post.php");
											if ($hero_posts_full == "big_banner") {
												echo '</div></div></div>';
											}
										endwhile;
									}
									wp_reset_postdata();
								}else if ($custom_hero == "slider" && is_array($add_hero_slides) && !empty($add_hero_slides)) {
									include locate_template("hero/hero-slider.php");
								}
								if ($hero_sidebar != "small_banner" && $hero_layout != "full" && isset($activate_slider)) {
									echo '</div></div>';
								}
								if (!isset($its_big_questions)) {?>
											</div>
										</div>
									<?php if ($custom_hero == "slider" && is_array($add_hero_slides) && !empty($add_hero_slides) && count($add_hero_slides) > 1) {?>
										</div>
									<?php }
								}?>
						<?php }
						if ($hero_layout == "full" && $custom_hero == "posts" && ($hero_posts_full == "banner_article" || $hero_posts_full == "article_style")) {
							$post__not_in_array = (is_array($post__not_in) && !empty($post__not_in)?array('post__not_in' => $post__not_in):array());
							$question_args = ($post_type == wpqa_questions_type?array('orderby' => 'meta_value_num','order' => 'DESC',"meta_query" => array(array('type' => 'numeric',"key" => "count_post_comments"))):array());
							$posts_per_page = ($hero_posts_full == "article_style"?$hero_posts_number:($hero_posts_number-1));
							$posts_per_page = ($posts_per_page > 0?$posts_per_page:1);
							$args = array_merge($post__not_in_array,$question_args,array('post_type' => $post_type,'posts_per_page' => $posts_per_page,'cache_results' => false,'no_found_rows' => true));
							$question_query = new WP_Query($args);
							if ($question_query->have_posts()) {
								$k = 0;
								while ($question_query->have_posts()) : $question_query->the_post();
									$k++;
									$post__not_in[] = $post->ID;
									if ($hero_posts_full != "article_style" && $hero_posts_slider == "on" && $k == 3) {?>
										<div class="col-boot-sm-12 col-boot-md-12 col-boot-lg-6">
											<div class="slider-wrap">
												<div>
													<?php include locate_template("hero/hero-big-post.php");
													$k = 0;?>
												</div>
											</div>
										</div>
									<?php }else {
										include locate_template("hero/hero-article-post.php");
									}
								endwhile;
							}
							wp_reset_postdata();
						}
					}
					if ($hero_layout != "full") {
						$sidebar_class = "col-boot-lg-4";
						if ($hero_layout == "full" && $custom_hero == "posts") {
							$sidebar_class = "col-boot-lg-6";
						}else if ($hero_sidebar == "small_banner") {
							$sidebar_class .= " hero-layout col-boot-lg-4 d-flex flex-column";
						}
						if (has_wpqa()) {?>
							<div class="col-boot-sm-12 col-boot-md-12 <?php echo esc_attr($sidebar_class)?>">
								<?php if ($hero_sidebar == "small_banner") {
									$post__not_in_array = (is_array($post__not_in) && !empty($post__not_in)?array('post__not_in' => $post__not_in):array());
									$question_args = ($post_type == wpqa_questions_type?array('orderby' => 'meta_value_num','order' => 'DESC',"meta_query" => array(array('type' => 'numeric',"key" => "count_post_comments"))):array());
									$posts_per_page = ($hero_posts_slider == "on"?($hero_layout != "full" && $hero_sidebar == "small_banner"?($hero_posts_number-1):$hero_posts_number):2);
									$posts_per_page = ($posts_per_page > 0?$posts_per_page:1);
									$args = array_merge($post__not_in_array,$question_args,array('post_type' => $post_type,'posts_per_page' => $posts_per_page,'cache_results' => false,'no_found_rows' => true));
									$question_query = new WP_Query($args);
									if ($question_query->have_posts()) {
										$k = 0;
										while ($question_query->have_posts()) : $question_query->the_post();
											$k++;
											$post__not_in[] = $post->ID;
											if ($hero_posts_slider == "on" && $k == 3) {?>
												</div>
												<div class="col-boot-sm-12 col-boot-md-12 col-boot-lg-8">
													<div class="slider-wrap">
														<div>
															<?php include locate_template("hero/hero-big-post.php");
															$k = 0;?>
														</div>
													</div>
												</div>
												<div class="col-boot-sm-12 col-boot-md-12 <?php echo esc_attr($sidebar_class)?>">
											<?php }else {
												if ($k == 1) {
													include locate_template("hero/hero-small-big-banner.php");
												}else {
													include locate_template("hero/hero-small-banner.php");
												}
											}
										endwhile;
									}
									wp_reset_postdata();
								}else {
									include locate_template("hero/hero-stats.php");
								}?>
							</div><!-- /.col-boot-lg-4 -->
							<?php if ($hero_sidebar == "small_banner" && $hero_posts_number > 3 && $hero_posts_slider != "on") {
								$post__not_in_array = (is_array($post__not_in) && !empty($post__not_in)?array('post__not_in' => $post__not_in):array());
								$question_args = ($post_type == wpqa_questions_type?array('orderby' => 'meta_value_num','order' => 'DESC',"meta_query" => array(array('type' => 'numeric',"key" => "count_post_comments"))):array());
								$posts_per_page = ($hero_posts_number-3);
								$posts_per_page = ($posts_per_page > 0?$posts_per_page:1);
								$args = array_merge($post__not_in_array,$question_args,array('post_type' => $post_type,'posts_per_page' => $posts_per_page,'cache_results' => false,'no_found_rows' => true));
								$question_query = new WP_Query($args);
								if ($question_query->have_posts()) {
									$k = 0;
									while ($question_query->have_posts()) : $question_query->the_post();
										$k++;
										$post__not_in[] = $post->ID;?>
										<div class="col-boot-sm-12 col-boot-md-12 <?php echo esc_attr($sidebar_class)?>">
											<?php include locate_template("hero/hero-small-banner.php");?>
										</div>
									<?php endwhile;
								}
								wp_reset_postdata();
							}
						}
					}else if ($custom_hero == "posts" && $hero_posts_full == "small_banner") {
						$post__not_in_array = (is_array($post__not_in) && !empty($post__not_in)?array('post__not_in' => $post__not_in):array());
						$question_args = ($post_type == wpqa_questions_type?array('orderby' => 'meta_value_num','order' => 'DESC',"meta_query" => array(array('type' => 'numeric',"key" => "count_post_comments"))):array());
						$posts_per_page = $hero_posts_number;
						$posts_per_page = ($posts_per_page > 0?$posts_per_page:1);
						$args = array_merge($post__not_in_array,$question_args,array('post_type' => $post_type,'posts_per_page' => $posts_per_page,'cache_results' => false,'no_found_rows' => true));
						$question_query = new WP_Query($args);
						if ($question_query->have_posts()) {
							$k = 0;
							while ($question_query->have_posts()) : $question_query->the_post();
								$k++;
								$post__not_in[] = $post->ID;?>
								<div class="col-boot-sm-12 col-boot-md-12 col-boot-lg-4 hero-layout col-boot-lg-4 d-flex flex-column">
									<?php if ($hero_small_posts_meta == "on") {
										include locate_template("hero/hero-small-big-banner.php");
									}else {
										include locate_template("hero/hero-small-banner.php");
									}?>
								</div>
							<?php endwhile;
						}
						wp_reset_postdata();
					}
					if (($hero_layout == "full" || ($hero_sidebar == "small_banner" && $hero_layout != "full")) && isset($activate_slider)) {
						echo '</div></div>';
					}?>
				</div><!-- /.row-boot -->
			</div><!-- /.container -->
		</section>
	<?php }
}?>
<?php /* Template Name: Badges */
get_header();
	include locate_template("theme-parts/logged-only.php");
	$page_id        = $post_id_main = $post->ID;
	$active_points  = himer_options("active_points");
	$badges_details = himer_options("badges_details");
	$badges_style   = himer_options("badges_style");
	$badges_groups  = himer_options("badges_groups");
	$badge_key = "badge_points";
	if ($badges_style == "by_groups_points") {
		$badges = himer_options("badges_groups_points");
	}else if ($badges_style == "by_questions") {
		$badges = himer_options("badges_questions");
		$badge_key = "badge_questions";
	}else if ($badges_style == "by_answers") {
		$badges = himer_options("badges_answers");
		$badge_key = "badge_answers";
	}else {
		$badges = himer_options("badges");
	}
	include locate_template("theme-parts/the-content.php");?>
	<div class="page-sections">
		<?php if ($active_points == "on") {
			$points_columns = himer_post_meta("badges_points_columns");
			$points_details = himer_options("points_details");
			$points_array = (has_wpqa()?wpqa_get_points():array());
			if (is_array($points_array) && !empty($points_array)) {?>
				<div class="page-section page-section-points">
					<div class="page-wrap-content">
						<h2 class="post-title-3"><i class="icon-trophy"></i><?php esc_html_e("Points System","himer")?></h2>
						<?php if (isset($points_details) && $points_details != "") {?>
							<div class="post-content-text mb-2rem"><p><?php echo do_shortcode(himer_kses_stip(nl2br(stripslashes($points_details))))?></p></div>
						<?php }?>
						<div class="points-section">
							<ul class="row row-boot list-unstyled mb-0">
								<?php foreach ($points_array as $key => $value) {
									if (isset($value["points"]) && $value["points"] > 0) {
										$value_points = (int)$value["points"];?>
										<li class="col col-boot <?php echo ("2col" == $points_columns?"col6 col-boot-sm-6 col-boot-md-6":"col4 col-boot-sm-4 col-boot-md-4")?>">
											<div class="point-section points-card">
												<div class="point-div d-flex align-items-center">
													<i class="icon-trophy points__icon mr-3"></i>
													<div class="d-flex flex-column">
														<span class="points__count"><?php echo himer_count_number($value_points)?></span>
														<span class="points__name"><?php echo _n("Point","Points",$value_points,"himer")?></span>
													</div>
												</div>
												<p class="points__desc mb-0"><?php echo wpqa_get_points_name($key)?></p>
											</div>
										</li>
									<?php }
								}?>
							</ul>
						</div>
					</div><!-- End page-wrap-content -->
				</div><!-- End page-section -->
			<?php }
		}

		$buy_points_section = himer_post_meta("buy_points_section");
		if ($buy_points_section == "on") {
			$active_points = wpqa_options("active_points");
			$buy_points_payment = wpqa_options("buy_points_payment");
			if ($active_points == "on" && $buy_points_payment == "on") {
				$buy_points = wpqa_options("buy_points");
				echo '<div class="page-section page-section-buy-points">'.wpqa_buy_points_section($buy_points).'</div>';
			}
		}
		
		if (($badges_style != "by_groups" && isset($badges) && is_array($badges)) || ($badges_style == "by_groups" && isset($badges_groups) && is_array($badges_groups) && isset($badges_details) && $badges_details != "")) {?>
			<div class="page-section page-section-badges">
				<div class="page-wrap-content">
					<h2 class="post-title-3"><i class="icon-ribbon-b"></i><?php esc_html_e("Badges System","himer")?></h2>
					<?php if (isset($badges_details) && $badges_details != "") {?>
						<div class="post-content-text mb-2rem"><p><?php echo do_shortcode(himer_kses_stip(nl2br(stripslashes($badges_details))))?></p></div>
					<?php }
					if ($badges_style != "by_groups") {?>
						<div class="badges-section">
							<ul class="list-unstyled mb-0">
								<?php $points_badges = array_column($badges,$badge_key);
								array_multisort($points_badges,SORT_ASC,$badges);
								foreach ($badges as $badges_k => $badges_v) {
									if (isset($badges_v[$badge_key]) && $badges_v[$badge_key] != "") {
										$badge_values = (int)$badges_v[$badge_key];?>
										<li>
											<div class="badge-section badge-card d-flex flex-wrap">
												<div class="badge-div badge__meta">
													<span class="badge-span badge" style="background-color: <?php echo esc_html($badges_v["badge_color"])?>;"><?php echo strip_tags(stripslashes($badges_v["badge_name"]),"<i>")?></span>
													<div class="point-div d-flex align-items-center">
														<i class="icon-ribbon-b badge__icon mr-2"></i>
														<span class="badge__count mr-1"><?php echo himer_count_number($badge_values)?></span>
														<span class="badge__name">
															<?php if ($badges_style == "by_questions") {
																echo _n("Question","Questions",$badge_values,"himer");
															}else if ($badges_style == "by_answers") {
																echo _n("Answer","Answers",$badge_values,"himer");
															}else {
																echo _n("Point","Points",$badge_values,"himer");
															}?>
														</span>
													</div>
												</div>
												<div class="badge__info"><p class="badge__desc mb-0"><?php echo himer_kses_stip(stripslashes($badges_v["badge_details"]))?></p></div>
											</div>
										</li>
									<?php }
								}?>
							</ul>
						</div>
					<?php }?>
				</div><!-- End page-wrap-content -->
			</div><!-- End page-section -->
		<?php }
		do_action("himer_after_badge_section");?>
	</div><!-- End page-sections -->
<?php get_footer();?>
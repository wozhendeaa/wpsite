<?php $custom_call_action = (is_page() || is_single()?himer_post_meta("custom_call_action"):"");
if ($custom_call_action == "on") {
	$action_button = himer_post_meta("action_button");
	$action_logged = himer_post_meta("action_logged");
}else {
	$action_button = himer_options("action_button");
	$action_logged = himer_options("action_logged");
}
$action_logged = apply_filters("himer_action_logged",$action_logged);
if ((is_user_logged_in() && ($action_logged == "logged" || $action_logged == "both")) || (!is_user_logged_in() && ($action_logged == "unlogged" || $action_logged == "both"))) {
	if ($custom_call_action == "on") {
		$call_action = himer_post_meta("call_action");
		$action_image_video = himer_post_meta("action_image_video");
		$video_id = himer_post_meta("action_video_id");
		$video_type = himer_post_meta("action_video_type");
		$video_mp4 = himer_post_meta("action_video_mp4");
		$video_m4v = himer_post_meta("action_video_m4v");
		$video_webm = himer_post_meta("action_video_webm");
		$video_ogv = himer_post_meta("action_video_ogv");
		$video_wmv = himer_post_meta("action_video_wmv");
		$video_flv = himer_post_meta("action_video_flv");
		$custom_embed = himer_post_meta("action_custom_embed");
		$action_skin = himer_post_meta("action_skin");
		$action_style = himer_post_meta("action_style");
		$action_headline = himer_post_meta("action_headline");
		$action_paragraph = himer_post_meta("action_paragraph");
	}else {
		$call_action = himer_options("call_action");
		$action_image_video = himer_options("action_image_video");
		$video_id = himer_options("action_video_id");
		$video_type = himer_options("action_video_type");
		$video_mp4 = himer_options("action_video_mp4");
		$video_m4v = himer_options("action_video_m4v");
		$video_webm = himer_options("action_video_webm");
		$video_ogv = himer_options("action_video_ogv");
		$video_wmv = himer_options("action_video_wmv");
		$video_flv = himer_options("action_video_flv");
		$custom_embed = himer_options("action_custom_embed");
		$action_home_pages = himer_options("action_home_pages");
		$action_pages = himer_options("action_pages");
		$action_pages = explode(",",$action_pages);
		$action_skin = himer_options("action_skin");
		$action_style = himer_options("action_style");
		$action_headline = himer_options("action_headline");
		$action_paragraph = himer_options("action_paragraph");
	}
	if ($video_id != "") {
		$type = (has_wpqa()?wpqa_video_iframe($video_type,$video_id,"options","action_video_id"):"");
	}
	$video_mp4 = (isset($video_mp4) && $video_mp4 != ""?'<source src="'.$video_mp4.'" type="video/mp4">':"");
	$video_m4v = (isset($video_m4v) && $video_m4v != ""?'<source src="'.$video_m4v.'" type="video/m4v">':"");
	$video_webm = (isset($video_webm) && $video_webm != ""?'<source src="'.$video_webm.'" type="video/webm">':"");
	$video_ogv = (isset($video_ogv) && $video_ogv != ""?'<source src="'.$video_ogv.'" type="video/ogv">':"");
	$video_wmv = (isset($video_wmv) && $video_wmv != ""?'<source src="'.$video_wmv.'" type="video/wmv">':"");
	$video_flv = (isset($video_flv) && $video_flv != ""?'<source src="'.$video_flv.'" type="video/flv">':"");
	$call_action = apply_filters("himer_call_action",$call_action);
	$action_headline = apply_filters("himer_action_headline",$action_headline);
	$action_paragraph = apply_filters("himer_action_paragraph_2",$action_paragraph);
	$action_home_pages = apply_filters("himer_action_home_pages",(isset($action_home_pages)?$action_home_pages:""));
	if ($call_action == "on" && (($custom_call_action == "on") || (((is_front_page() || is_home()) && $action_home_pages == "home_page") || $action_home_pages == "all_pages" || ($action_home_pages == "all_posts" && is_singular("post")) || ($action_home_pages == "all_questions" && (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type))) || ($action_home_pages == "custom_pages" && is_page() && isset($action_pages) && is_array($action_pages) && isset($post->ID) && in_array($post->ID,$action_pages))))) {?>
		<div class="call-action-unlogged call-action-<?php echo esc_attr($action_skin).' call-action-'.esc_attr($action_style)?>">
			<?php if ($action_image_video == "video") {
				if ($video_type == "html5") {
					echo '<div class="call-action-video">
						<video autoplay loop>'.$video_mp4.$video_m4v.$video_webm.$video_ogv.$video_wmv.$video_flv.esc_html__("Your browser does not support the video tag.","himer").'</video>
					</div>';
				}else if ($video_type == "embed" && $custom_embed != "") {
					echo '<div class="call-action-video">'.$custom_embed.'</div>';
				}else if (isset($type) && $type != "") {
					echo '<div class="call-action-video video-type-'.$video_type.'"><iframe frameborder="0" allow="autoplay" height="100%" width="100%" src="'.$type.'?autoplay=1&loop=1'.(isset($video_id) && $video_id != ""?"&playlist=".$video_id:"").'"></iframe></div>';
				}
			}
			if ($action_image_video != "video") {?>
				<div class="call-action-opacity"></div>
			<?php }?>
			<div class="the-main-container container">
				<div class="call-action-wrap row row-boot">
					<div class="<?php echo ("style_1" == $action_style?"col6 col-boot-sm-6":"col12 col-boot-sm-12")?>">
						<?php if ($action_headline != "") {?>
							<h3><?php echo himer_kses_stip(stripslashes($action_headline))?></h3>
						<?php }
						if ($action_paragraph != "") {?>
							<p><?php echo do_shortcode(himer_kses_stip(nl2br(stripslashes($action_paragraph))))?></p>
						<?php }
					if ($action_style == "style_1") {?>
						</div>
						<div class="col3 col-boot-sm-3">
					<?php }
					$show_action = false;
					if (is_user_logged_in()) {
						if (($action_logged == "logged" || $action_logged == "both") && ($action_button != "login" && $action_button != "signup")) {
							$show_action = true;
						}
					}else {
						if (($action_logged == "unlogged" || $action_logged == "both") || ($action_button == "login" || $action_button == "signup")) {
							$show_action = true;
						}
					}
					$himer_signup_call_action = apply_filters('himer_signup_call_action',true);
					if ($show_action == true && $himer_signup_call_action == true) {
						if ($action_button == "question") {
							$filter_class = "question";
							$action_button_class = "wpqa-question";
							$action_button_link = (has_wpqa()?wpqa_add_question_permalink():"#");
							$action_button_text = esc_html__("Ask A Question","himer");
						}else if ($action_button == "post") {
							$filter_class = "post";
							$action_button_class = "wpqa-post";
							$action_button_link = (has_wpqa()?wpqa_add_post_permalink():"#");
							$action_button_text = esc_html__("Add A New Post","himer");
						}else if ($action_button == "login") {
							$activate_login = himer_options("activate_login");
							if ($activate_login != 'disabled') {
								$filter_class = "login";
								$action_button_class = "login-panel";
								$action_button_link = (has_wpqa()?wpqa_login_permalink():"#");
								$action_button_text = esc_html__("Login","himer");
							}
						}else if ($action_button == "signup") {
							$activate_register = himer_options("activate_register");
							if ($activate_register != 'disabled') {
								$filter_class = "signup";
								$action_button_class = "signup-panel";
								$action_button_link = (has_wpqa()?wpqa_signup_permalink():"#");
								$action_button_text = esc_html__("Create A New Account","himer");
							}
						}else {
							$filter_class = $action_button_class = "";
							if ($custom_call_action == "on") {
								$action_button_target = himer_post_meta("action_button_target");
								$action_button_link = himer_post_meta("action_button_link");
								$action_button_text = himer_post_meta("action_button_text");
							}else {
								$action_button_target = himer_options("action_button_target");
								$action_button_link = himer_options("action_button_link");
								$action_button_text = himer_options("action_button_text");
							}
						}
						$action_button_target = ($action_button == "custom" && isset($action_button_target) && $action_button_target == "new_page"?"_blank":"_self");?>
						<a target="<?php echo esc_attr($action_button_target)?>" class="<?php echo esc_attr($action_button_class)?> button-default btn <?php echo ("dark" != $action_skin?"btn__primary":"btn__secondary")?> call-action-button<?php echo apply_filters('wpqa_pop_up_class','').(isset($filter_class) && $filter_class != ''?apply_filters('wpqa_pop_up_class_'.$filter_class,''):'')?>" href="<?php echo esc_url($action_button_link)?>"><?php echo esc_html($action_button_text)?></a>
					<?php }else {
						do_action("himer_after_button_call_action");
					}?>
					</div>
					<?php do_action("himer_after_call_action");?>
				</div>
			</div><!-- End the-main-container -->
		</div><!-- End call-action-unlogged -->
	<?php }
}?>
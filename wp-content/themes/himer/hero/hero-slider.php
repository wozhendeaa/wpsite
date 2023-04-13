<?php $first_tag = (is_front_page() || is_home()?"h1":"h2");
foreach ($add_hero_slides as $key => $value) {
	$title         = (isset($value["title"])?$value["title"]:"");
	$paragraph     = (isset($value["paragraph"])?$value["paragraph"]:"");
	$button_active = (isset($value["button_active"])?$value["button_active"]:"");
	$button        = (isset($value["button"])?$value["button"]:"");
	$button_style  = (isset($value["button_style"])?$value["button_style"]:"");?>
	<div id="hero-item-<?php echo esc_attr($key)?>" class="slider-item hero-item hero-item-<?php echo esc_attr($key)?>">
		<div class="cta-banner">
			<div>
				<div class="cover-opacity"></div>
				<?php echo stripslashes($title != ""?"<".$first_tag." class='cta-banner__title'>".$title."</".$first_tag.">":"").
				stripslashes($paragraph != ""?"<p class='cta-banner__description'>".$paragraph."</p>":"");
				if ($button_active == "on") {
					if ($button == "question") {
						$filter_class = "question";
						$slider_button_class = "wpqa-question";
						$button_link = (has_wpqa()?wpqa_add_question_permalink():"#");
						$button_text = esc_html__("Ask A Question","himer");
					}else if ($button == "post") {
						$filter_class = "post";
						$slider_button_class = "wpqa-post";
						$button_link = (has_wpqa()?wpqa_add_post_permalink():"#");
						$button_text = esc_html__("Add A New Post","himer");
					}else if (!is_user_logged_in() && $button == "login") {
						$activate_login = himer_options("activate_login");
						if ($activate_login != 'disabled') {
							$filter_class = "login";
							$slider_button_class = "login-panel";
							$button_link = (has_wpqa()?wpqa_login_permalink():"#");
							$button_text = esc_html__("Login","himer");
						}
					}else if (!is_user_logged_in() && $button == "signup") {
						$activate_register = himer_options("activate_register");
						if ($activate_register != 'disabled') {
							$filter_class = "signup";
							$slider_button_class = "signup-panel";
							$button_link = (has_wpqa()?wpqa_signup_permalink():"#");
							$button_text = esc_html__("Create A New Account","himer");
						}
					}else if ($button == "custom") {
						$filter_class = "";
						$button_target = (isset($value["button_target"])?$value["button_target"]:"");
						$slider_button_class = "";
						$button_link = (isset($value["button_link"])?$value["button_link"]:"");
						$button_text = (isset($value["button_text"])?$value["button_text"]:"");
					}
					$button_target = ($button == "custom" && isset($button_target) && $button_target == "new_page"?"_blank":"_self");
					if (isset($slider_button_class)) {?>
						<a target="<?php echo esc_attr($button_target)?>" class="<?php echo esc_attr("hero-button-".$button_style." ".$slider_button_class)?> button-default btn btn__white slider-button<?php echo apply_filters('wpqa_pop_up_class','').(isset($filter_class) && $filter_class != ''?apply_filters('wpqa_pop_up_class_'.$filter_class,''):'')?>" href="<?php echo esc_url($button_link)?>"><?php echo esc_html($button_text)?></a>
					<?php }
				}?>
			</div>
		</div>
	</div>
<?php }?>
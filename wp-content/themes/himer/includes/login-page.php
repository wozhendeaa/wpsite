<?php if ($under_construction == "on") {
	$register_style     = "style_2";
	$register_headline  = himer_options("construction_headline");
	$register_paragraph = himer_options("construction_paragraph");
	$construction_redirect = himer_options("construction_redirect");
	if ($construction_redirect != "") {
		wp_redirect(esc_url($construction_redirect));
		die();
	}
}else if (isset($wp_page_template) && $wp_page_template == "template-landing.php") {
	$is_landing = true;
	$home_page = (int)himer_post_meta("home_page");
	if (is_user_logged_in()) {
		if (is_home() || is_front_page()) {
			if ($home_page != "" && $home_page > 0) {
				wp_redirect(esc_url(get_permalink($home_page)));
				exit;
			}
		}else {
			wp_redirect(esc_url(home_url('/')));
			exit;
		}
	}
	$register_style     = himer_post_meta("register_style");
	$register_menu      = himer_post_meta("register_menu");
	$register_headline  = himer_post_meta("register_headline");
	$register_paragraph = himer_post_meta("register_paragraph");

	$register_big_button = himer_post_meta("register_big_button");
	$register_big_button_target = himer_post_meta("register_big_button_target");
	$register_big_button_link = himer_post_meta("register_big_button_link");
	$register_big_button_text = himer_post_meta("register_big_button_text");
	$register_first_button = himer_post_meta("register_first_button");
	$register_second_button = himer_post_meta("register_second_button");
	$register_second_button_target = himer_post_meta("register_second_button_target");
	$register_second_button_link = himer_post_meta("register_second_button_link");
	$register_second_button_text = himer_post_meta("register_second_button_text");

	$custom_logo = himer_post_meta("custom_logo");
	if ($custom_logo == "on") {
		$logo_display = "custom_image";
		$logo_img     = himer_image_url_id(himer_post_meta("logo_landing"));
		$retina_logo  = himer_image_url_id(himer_post_meta("logo_landing_retina"));
		$logo_height  = himer_post_meta("logo_landing_height");
		$logo_width   = himer_post_meta("logo_landing_width");
	}
}else {
	$register_style     = himer_options("register_style");
	$register_menu      = himer_options("register_menu");
	$register_headline  = himer_options("register_headline");
	$register_paragraph = himer_options("register_paragraph");

	$register_big_button = himer_options("register_big_button");
	$register_big_button_target = himer_options("register_big_button_target");
	$register_big_button_link = himer_options("register_big_button_link");
	$register_big_button_text = himer_options("register_big_button_text");
	$register_first_button = himer_options("register_first_button");
	$register_second_button = himer_options("register_second_button");
	$register_second_button_target = himer_options("register_second_button_target");
	$register_second_button_link = himer_options("register_second_button_link");
	$register_second_button_text = himer_options("register_second_button_text");
}
$its_not_login = true;
$footer_copyrights = himer_options("footer_copyrights");?>
<div class="login-page-cover"></div>
<div class="login-opacity"></div>
<div class="the-main-container">
	<?php if ($under_construction != "on") {?>
		<header class="header header-transparent">
			<nav class="navbar navbar-expand-lg" itemscope="" itemtype="https://schema.org/SiteNavigationElement">
				<div class="container">
					<?php include locate_template("header-parts/logo.php");?>
					<button class="open-mobileMenu d-block d-lg-none" type="button"><i class="icon-navicon-round"></i></button>
					<div class="collapse navbar-collapse" id="mainNavigation">
						<button type="button" class="close-mobileMenu d-block d-lg-none btn btn__danger btn__sm"><i class="icon-close-round"></i></button>
						<?php wp_nav_menu(array('container' => '','menu_class' => 'navbar-nav menu ml-auto','menu' => $register_menu));?>
					</div><!-- /.navbar-collapse -->
					<?php if ($register_big_button == "on" && $register_big_button_text != "") {?>
						<ul class="header-actions list-unstyled mb-0 d-flex align-items-center">
							<li>
								<a href="<?php echo esc_url($register_big_button_link)?>"<?php echo ("new_page" == $register_big_button_target?" target='_blank'":"")?> class="btn btn__white btn__large__height"><?php echo esc_html($register_big_button_text)?></a>
							</li>
						</ul><!-- /.header-actions -->
					<?php }?>
				</div><!-- /.container -->
			</nav><!-- /.navabr -->
		</header><!-- /.Header -->
	<?php }
	$confirm_email = (has_wpqa()?wpqa_users_confirm_mail():"");?>
	<main class="page-main<?php echo (isset($_POST["form_type"]) && $_POST["form_type"] == "wpqa-signup"?" page-signup-wrap":"").($confirm_email == "yes" || $register_style == "style_2"?" main-login-2":"").($under_construction == "on"?" under-construction":"").(isset($is_landing)?" is-landing":"")?>">
		<section class="landing bg-overlay">
			<div class="container landing-inner">
				<?php do_action("wpqa_show_session");
				if ($under_construction != "on" && $confirm_email == "yes") {
					wpqa_check_user_account(true);
				}else {?>
					<div class="main-landing-div row-boot align-items-center">
						<div class="first-landing-div my-3 col-boot-md-12<?php echo ("on" == $under_construction?"":($register_style == "style_2"?" mb-5":" col-boot-lg-6 col-boot-xl-5"))?>">
							<?php if ($register_headline != "") {?>
								<h1 class="landing__title text-white"><?php echo stripslashes($register_headline)?></h1>
							<?php }
							if ($register_paragraph != "") {?>
								<p class="landing__desc text-white"><?php echo do_shortcode(stripslashes($register_paragraph))?></p>
							<?php }
							if ($under_construction != "on") {?>
								<div class="landing__actions d-flex flex-wrap">
									<?php if (has_wpqa() && $register_first_button != "off") {
										$activate_register = himer_options("activate_register");
										$activate_login = himer_options("activate_login");
										if ($activate_register != "disabled" && $register_first_button == "signup") {
											$class_login_button = "signup-panel-un";
											$url_login_button = wpqa_signup_permalink();
										}else if ($activate_login != 'disabled') {
											$class_login_button = "login-panel-un";
											$url_login_button = wpqa_login_permalink();
										}
										$activate_register != "disabled"?>
										<a href="<?php echo esc_url($url_login_button)?>" class="btn <?php echo esc_attr($class_login_button)?> btn__white btn__extra__height"><?php esc_html_e("Join Now!","himer")?></a>
									<?php }
									if ($register_second_button == "on" && $register_second_button_text != "") {?>
										<a href="<?php echo esc_url($register_second_button_link)?>"<?php echo ("new_page" == $register_second_button_target?" target='_blank'":"")?> class="btn btn__white btn__extra__height btn__outlined"><?php echo esc_html($register_second_button_text)?></a>
									<?php }?>
								</div>
							<?php }?>
						</div><!-- /.col-boot-xl-5 -->
						<?php if ($under_construction != "on") {?>
							<div class="second-landing-div col-boot-md-12 <?php echo ("style_2" == $register_style?"justify-content-center":"col-boot-lg-6 col-boot-xl-5 offset-xl-2 justify-content-end")?> my-3 form-container d-flex">
								<div class="landing-form">
									<?php if (has_wpqa()) {
										wpqa_head_content("login",$its_not_login);
									}?>
								</div><!-- /.login-popup__form -->
							</div><!-- /.col-boot-xl-5 -->
						<?php }?>
					</div><!-- /.row-boot -->
				<?php }?>
				<div class="footer-landing-div row-boot">
					<div class="col-boot-12">
						<p class="copyrights text-white text-center mb-0"><?php echo stripslashes($footer_copyrights)?></p>
					</div>
				</div>
			</div><!-- /.container -->
		</section>
	</main>
</div>
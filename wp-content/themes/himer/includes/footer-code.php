							<?php $confirm_email = (has_wpqa()?wpqa_users_confirm_mail():"");
							$footer_style = himer_options("footer_style");
							$footer_copyrights = himer_options("footer_copyrights");
							$widget_icons = himer_options("widget_icons");
							$sidebar_style = himer_options("sidebar_style");
							if (is_author() || (has_wpqa() && wpqa_is_user_profile())) {
								$is_user_profile = true;
								$sidebar_style = himer_options("author_sidebar_style");
							}
							$custom_page_setting = himer_post_meta("custom_page_setting");
							if ((is_single() || is_page()) && isset($custom_page_setting) && $custom_page_setting == "on") {
								$sticky_sidebar = himer_post_meta("sticky_sidebar_s");
							}else {
								$sticky_sidebar = himer_options("sticky_sidebar");
							}
							if ($confirm_email != "yes" && $site_users_only != "yes") {
								$adv_404 = himer_options("adv_404");
								if (is_404() && $adv_404 == "on") {
									$adv_404 = "on";
								}else {
									$adv_404 = "";
								}
								if (($adv_404 != "on" && is_404()) || !is_404()) {
									$after_content_adv_filter = apply_filters("himer_after_content_adv_filter",true);
									if (has_wpqa() && wpqa_plugin_version >= "5.8" && $after_content_adv_filter == true) {
										echo wpqa_ads("content_adv_type","content_adv_link","content_adv_code","content_adv_href","content_adv_img","","on","aalan-footer","on");
									}
								}
							}?>
						</div><!-- /.wrap-main-content -->
					</div><!-- /.col-boot-lg-8 -->
					<?php if ($confirm_email != "yes" && $site_users_only != "yes") {
						$dynamic_sidebar = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_sidebar_layout():"");
						$sidebar_where = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_sidebars("sidebar_where"):"");
						$author_widget = himer_options("author_widget");
						if ($dynamic_sidebar != "" && is_active_sidebar($dynamic_sidebar) && ($sidebar_where == "menu_sidebar" || $sidebar_where == "sidebar")) {?>
							<div class="col-boot-sm-12 col-boot-md-12 warp-sidebar <?php echo ("menu_sidebar" == $sidebar_where?"col-boot-lg-3":"col-boot-lg-4")?>">
								<aside class="sidebar <?php echo apply_filters("himer_sidebar_class",false).("sidebar" == $footer_style?" footer-sidebar":"")?> sidebar-width float_l<?php echo ("on" != $widget_icons?" no-widget-icons":"").($sticky_sidebar == "sidebar" || $sticky_sidebar == "side_menu_bar"?" fixed-sidebar":"").($sidebar_style == "style_2"?" sidebar-style-2":"").((is_author() || (has_wpqa() && wpqa_is_user_profile())) && $author_widget == "on"?" author_widget_activated":"")?>">
									<?php if (isset($is_user_profile)) {
										if ($author_widget == "on") {
											echo wpqa_author_widget();
										}
									}
									get_sidebar();
									if ($footer_style == "sidebar") {
										$footer_menu = himer_options("footer_menu");?>
										<footer class="footer-layout2">
											<nav class="footer-widget-nav">
												<?php wp_nav_menu(array('container' => '','menu_class' => 'list-unstyled d-flex flex-wrap mb-2','menu_id' => 'footer_menu','menu' => $footer_menu));?>
												<?php if ($footer_copyrights != "") {?>
													<p class="footer__copyright"><?php echo stripslashes($footer_copyrights)?></p>
												<?php }?>
											</nav>
										</footer><!-- /.Footer -->
									<?php }?>
								</aside>
							</div><!-- /.col-boot-lg-4 -->
						<?php }
					}?>
				</div><!-- /.row-boot -->
			</div><!-- /.container -->
		</section>
	</main><!-- End page-main -->
	<?php $blog_h_where = himer_options("blog_h_where");
	if ($blog_h_where == "footer") {
		include locate_template("includes/blog-header-footer.php");
	}
	
	$sort_footer_elements = himer_options("sort_footer_elements");
	if ($footer_style != "sidebar") {
		$footer_skin = himer_options("footer_skin");
		
		$top_footer = himer_options("top_footer");
		$footer_widget_icons = himer_options("footer_widget_icons");
		$top_footer_padding_top = himer_options("top_footer_padding_top");
		$top_footer_padding_bottom = himer_options("top_footer_padding_bottom");
		$footer_layout = himer_options("footer_layout");
		
		$add_footer = himer_options("add_footer");
		
		$bottom_footer = himer_options("bottom_footer");
		$footer_padding_top = himer_options("footer_padding_top");
		$footer_padding_bottom = himer_options("footer_padding_bottom");
		$footer_mail = himer_options("footer_mail");
		$footer_phone = himer_options("footer_phone");
		
		$top_footer_padding = "";
		if ((isset($top_footer_padding_top) && $top_footer_padding_top != "" && $top_footer_padding_top > 0) || (isset($top_footer_padding_bottom) && $top_footer_padding_bottom != "" && $top_footer_padding_bottom > 0)) {
			$top_footer_padding .= " style='";
			if (isset($top_footer_padding_top) && $top_footer_padding_top != "" && $top_footer_padding_top > 0) {
				$top_footer_padding .= "padding-top:".$top_footer_padding_top."px;";
			}
			if (isset($top_footer_padding_bottom) && $top_footer_padding_bottom != "" && $top_footer_padding_bottom > 0) {
				$top_footer_padding .= "padding-bottom:".$top_footer_padding_bottom."px;";
			}
			$top_footer_padding .= "'";
		}
		
		$footer_padding = "";
		if ((isset($footer_padding_top) && $footer_padding_top != "" && $footer_padding_top > 0) || (isset($footer_padding_bottom) && $footer_padding_bottom != "" && $footer_padding_bottom > 0)) {
			$footer_padding .= " style='";
			if (isset($footer_padding_top) && $footer_padding_top != "" && $footer_padding_top > 0) {
				$footer_padding .= "padding-top:".$footer_padding_top."px;";
			}
			if (isset($footer_padding_bottom) && $footer_padding_bottom != "" && $footer_padding_bottom > 0) {
				$footer_padding .= "padding-bottom:".$footer_padding_bottom."px;";
			}
			$footer_padding .= "'";
		}
		
		if (empty($sort_footer_elements) || count($sort_footer_elements) <> 2 || (isset($sort_footer_elements[0]) && isset($sort_footer_elements[0]["value"]) && $sort_footer_elements[0]["value"] != "top_footer" && $sort_footer_elements[0]["value"] != "bottom_footer")) {
			$sort_footer_elements = array(array("value" => "top_footer","name" => "Top footer"),array("value" => "bottom_footer","name" => "Bottom footer"));
		}
		
		if ($top_footer == "on" || $bottom_footer == "on") {?>
			<footer class="footer<?php echo ("on" != $footer_widget_icons?" no-widget-icons":"").($footer_skin == "light"?" footer-light":"").($footer_skin == "dark"?" footer-dark":"")?>" itemscope="" itemtype="https://schema.org/WPFooter">
				<?php do_action("himer_before_inner_footer");?>
				<div id="inner-footer" class="wrap clearfix">
					<?php if (isset($sort_footer_elements) && is_array($sort_footer_elements)) {
						foreach ($sort_footer_elements as $key_r => $value_r) {
							if ($confirm_email != "yes" && $site_users_only != "yes" && isset($value_r["value"]) && $value_r["value"] == "top_footer" && $top_footer == "on") {?>
								<div class="top-footer"<?php echo stripcslashes($top_footer_padding)?>>
									<div class="container">
										<aside class="row-boot">
											<h3 class="screen-reader-text"><?php esc_html_e('Footer','himer')?></h3>
											<div class="<?php echo ("footer_1c" == $footer_layout?"col-boot-12":"").($footer_layout == "footer_2c"?"col-boot-sm-12 col-boot-md-6 col-boot-lg-6":"").($footer_layout == "footer_3c"?"col-boot-sm-12 col-boot-md-6 col-boot-lg-4":"").($footer_layout == "footer_4c"?"col-boot-sm-12 col-boot-md-6 col-boot-lg-3":"").($footer_layout == "footer_5c"?"col-boot-sm-12 col-boot-md-12 col-boot-lg-3":"")?>">
												<?php dynamic_sidebar('footer_1c_sidebar');?>
											</div>
											
											<?php if ($footer_layout != "footer_1c") {?>
												<div class="<?php echo ("footer_2c" == $footer_layout?"col-boot-sm-12 col-boot-md-6 col-boot-lg-6":"").($footer_layout == "footer_3c"?"col-boot-sm-12 col-boot-md-6 col-boot-lg-4":"").($footer_layout == "footer_4c"?"col-boot-sm-6 col-boot-md-6 col-boot-lg-3":"").($footer_layout == "footer_5c"?"col-boot-sm-4 col-boot-md-4 col-boot-lg-2":"")?>">
													<?php dynamic_sidebar('footer_2c_sidebar');?>
												</div>
											<?php }
											
											if ($footer_layout != "footer_1c" && $footer_layout != "footer_2c") {?>
												<div class="<?php echo ("footer_3c" == $footer_layout?"col-boot-sm-12 col-boot-md-6 col-boot-lg-4":"").($footer_layout == "footer_4c"?"col-boot-sm-6 col-boot-md-6 col-boot-lg-3":"").($footer_layout == "footer_5c"?"col-boot-sm-4 col-boot-md-4 col-boot-lg-2":"")?>">
													<?php dynamic_sidebar('footer_3c_sidebar');?>
												</div>
											<?php }
											
											if ($footer_layout != "footer_1c" && $footer_layout != "footer_2c" && $footer_layout != "footer_3c") {?>
												<div class="<?php echo ("footer_4c" == $footer_layout?"col-boot-sm-6 col-boot-md-6 col-boot-lg-3":"").($footer_layout == "footer_5c"?"col-boot-sm-4 col-boot-md-4 col-boot-lg-2":"")?>">
													<?php dynamic_sidebar('footer_4c_sidebar');?>
												</div>
											<?php }
											
											if ($footer_layout == "footer_5c") {?>
												<div class="col-boot-sm-12 col-boot-md-12 col-boot-lg-3">
													<?php dynamic_sidebar('footer_5c_sidebar');?>
												</div>
											<?php }?>
										</aside>
									</div><!-- /.container -->
								</div>
								<?php if (isset($add_footer) && is_array($add_footer) && !empty($add_footer)) {
									$k_footer = 0;
									foreach ($add_footer as $add_footer_k => $add_footer_v) {
										$k_footer++;
										$background_color = $add_footer_v["background_color"];
										$padding_bottom = $add_footer_v["padding_bottom"];
										$padding_top = $add_footer_v["padding_top"];
										$layout = $add_footer_v["layout"];
										$first_column = $add_footer_v["first_column"];
										$second_column = $add_footer_v["second_column"];
										$third_column = $add_footer_v["third_column"];
										$fourth_column = $add_footer_v["fourth_column"];
										$fifth_column = $add_footer_v["fifth_column"];
										$top_footer_style = "";
										if ((isset($padding_top) && $padding_top != "" && $padding_top > 0) || (isset($padding_bottom) && $padding_bottom != "" && $padding_bottom > 0) || (isset($background_color) && $background_color != "")) {
											$top_footer_style .= " style='";
											if (isset($padding_top) && $padding_top != "" && $padding_top > 0) {
												$top_footer_style .= "padding-top:".$padding_top."px;";
											}
											if (isset($padding_bottom) && $padding_bottom != "" && $padding_bottom > 0) {
												$top_footer_style .= "padding-bottom:".$padding_bottom."px;";
											}
											if (isset($background_color) && $background_color != "") {
												$top_footer_style .= "background-color:".$background_color.";";
											}
											$top_footer_style .= "'";
										}?>
										<div class="top-footer"<?php echo stripcslashes($top_footer_style)?>>
											<div class="container">
												<aside class="row-boot">
													<h3 class="screen-reader-text"><?php echo esc_html__('Footer','himer')." ".$k_footer?></h3>
													<div class="<?php echo ("footer_1c" == $layout?"col-boot-12":"").($layout == "footer_2c"?"col-boot-sm-12 col-boot-md-6 col-boot-lg-6":"").($layout == "footer_3c"?"col-boot-sm-12 col-boot-md-6 col-boot-lg-4":"").($layout == "footer_4c"?"col-boot-sm-6 col-boot-md-6 col-boot-lg-3":"").($layout == "footer_5c"?"col-boot-sm-12 col-boot-md-12 col-boot-lg-3":"")?>">
														<?php dynamic_sidebar(sanitize_title($first_column));?>
													</div>
													
													<?php if ($layout != "footer_1c") {?>
														<div class="<?php echo ("footer_2c" == $layout?"col-boot-sm-12 col-boot-md-6 col-boot-lg-6":"").($layout == "footer_3c"?"col-boot-sm-12 col-boot-md-6 col-boot-lg-4":"").($layout == "footer_4c"?"col-boot-sm-6 col-boot-md-6 col-boot-lg-3":"").($layout == "footer_5c"?"col-boot-sm-4 col-boot-md-4 col-boot-lg-2":"")?>">
															<?php dynamic_sidebar(sanitize_title($second_column));?>
														</div>
													<?php }
													
													if ($layout != "footer_1c" && $layout != "footer_2c") {?>
														<div class="<?php echo ("footer_3c" == $layout?"col-boot-sm-12 col-boot-md-6 col-boot-lg-4":"").($layout == "footer_4c"?"col-boot-sm-6 col-boot-md-6 col-boot-lg-3":"").($layout == "footer_5c"?"col-boot-sm-4 col-boot-md-4 col-boot-lg-2":"")?>">
															<?php dynamic_sidebar(sanitize_title($third_column));?>
														</div>
													<?php }
													
													if ($layout != "footer_1c" && $layout != "footer_2c" && $layout != "footer_3c") {?>
														<div class="<?php echo ("footer_4c" == $layout?"col-boot-sm-6 col-boot-md-6 col-boot-lg-3":"").($layout == "footer_5c"?"col-boot-sm-4 col-boot-md-4 col-boot-lg-2":"")?>">
															<?php dynamic_sidebar(sanitize_title($fourth_column));?>
														</div>
													<?php }
													
													if ($layout == "footer_5c") {?>
														<div class="col-boot-sm-12 col-boot-md-12 col-boot-lg-3">
															<?php dynamic_sidebar(sanitize_title($fifth_column));?>
														</div>
													<?php }?>
												</aside>
											</div>
										</div>
									<?php }
								}?>
							<?php }else if (isset($value_r["value"]) && $value_r["value"] == "bottom_footer" && $bottom_footer == "on" && isset($footer_copyrights) && $footer_copyrights != "") {?>
								<div class="bottom-footer"<?php echo stripcslashes($footer_padding)?>>
									<div class="container">
										<p class="credits"><?php echo stripslashes($footer_copyrights)?></p>
									</div><!-- End the-main-container -->
								</div><!-- End bottom-footer -->
							<?php }
						}
					}?>
				</div><!-- End inner-footer -->
			</footer><!-- /.Footer -->
		<?php }
	}
	do_action("himer_after_wrap");
	$go_up_button = himer_options("go_up_button");
	if ($go_up_button == "on") {?>
		<button id="scrollTopBtn" class="go-up"><i class="icon-ios-arrow-up"></i></button>
	<?php }
	$ask_button = himer_options("ask_button");
	if ($ask_button == "on") {?>
		<a href="<?php echo (has_wpqa()?wpqa_add_question_permalink():"#")?>" title="<?php esc_attr_e("Ask a question","himer")?>" class="ask-button wpqa-question<?php echo apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_question','')?>"><i class="icon-pencil"></i></a>
	<?php }
	$skin_switcher = himer_options('skin_switcher');
	if ($skin_switcher == 'on') {
		$skin_switcher_position = himer_options('skin_switcher_position');
		if ($skin_switcher_position == "footer") {
			if (is_user_logged_in()) {
				$user_id = get_current_user_id();
				$get_dark = get_user_meta($user_id,'wpqa_get_dark',true);
			}else {
				$uniqid_cookie = himer_options('uniqid_cookie');
				$get_dark = (isset($_COOKIE[$uniqid_cookie.'wpqa_get_dark'])?$_COOKIE[$uniqid_cookie.'wpqa_get_dark']:'');
			}
			$site_skin = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_dark_skin():"");
			$get_dark = ($get_dark != ''?($get_dark == 'dark'?'dark':'light'):$site_skin);?>
			<div class="dark-light-switcher dark-light-switcher-2 <?php echo ('dark' == $get_dark?'light-switcher':'dark-switcher')?>"><input type="checkbox" class="switcher-dark<?php echo ('dark' == $get_dark?'':' switcher-dark-checked')?>"><span></span></div>
		<?php }
	}?>
</div><!-- End wrap -->
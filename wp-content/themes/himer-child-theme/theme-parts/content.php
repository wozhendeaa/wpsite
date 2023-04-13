<?php if ((isset($blog_h) && $blog_h == "blog_h" && !is_array($sort_meta_title_image)) || !is_array($sort_meta_title_image) || (empty($blog_h) && is_single()) || $post_style != "style_3") {
	$sort_meta_title_image = array(array("value" => "meta_title",'name' => esc_html__('Meta and title','himer'),"default" => "yes"),array("value" => "image",'name' => esc_html__('Image','himer'),"default" => "yes"));
}

if (empty($post) && isset($GLOBALS['post'])) {
	$post_data = $post = $GLOBALS['post'];
}else {
	$post_data = $post;
}
$post_author = $post_data->post_author;
$post_id = $post_data->ID;
$post_type = $post_data->post_type;

$user_id                = get_current_user_id();
$is_super_admin         = is_super_admin($user_id);
$active_moderators      = himer_options("active_moderators");
$pending_posts          = (has_wpqa() && wpqa_is_pending_posts() && ($is_super_admin || $active_moderators == "on") && wpqa_is_user_owner() && ($is_super_admin || (isset($moderator_categories) && is_array($moderator_categories) && !empty($moderator_categories)))?true:false);
$pending_posts_page     = (has_wpqa() && wpqa_is_pending_posts()?true:false);
$moderators_permissions = (has_wpqa()?wpqa_user_moderator($user_id):"");
$moderator_categories   = (has_wpqa()?wpqa_user_moderator_categories($user_id,$post_id):"");
$post_link_target       = apply_filters("himer_post_link_target","");

$questions_position = himer_options("between_questions_position");
$adv_type_repeat = himer_options("between_adv_type_repeat");
if (has_wpqa() && wpqa_plugin_version >= "5.8" && !isset($blog_h) && isset($k_ad_p) && (($k_ad_p == $questions_position) || ($adv_type_repeat == "on" && $k_ad_p != 0 && $k_ad_p % $questions_position == 0))) {
	echo wpqa_ads("between_adv_type","between_adv_link","between_adv_code","between_adv_href","between_adv_img","","","aalan-inside".($post_style == "style_3"?" adv-style-3".$post_columns:""),"on");
}

$count_post_all = (int)(has_wpqa()?wpqa_count_comments($post_id):get_comments_number());
$what_post = himer_post_meta("what_post","",false);
$himer_thumbnail_id = himer_post_meta("_thumbnail_id","",false);
$get_gender_class = (has_wpqa()?wpqa_get_gender_class($post_author,$post_id):"");
$activate_male_female = apply_filters("wpqa_activate_male_female",false);
if (is_singular("post") && $activate_male_female == true) {
	$info_edited = '';
	$gender_author = ($post_author > 0?get_user_meta($post_author,'gender',true):"");
	$gender_post = get_post_meta($post_id,'wpqa_post_gender',true);
	if ($gender_author != "" && $gender_author != $gender_post) {
		$info_edited = esc_html__("Some of main user info (Ex: name or gender) has been edited since this post has been added","himer");
	}
}

$show_featured_image  = "";
if (has_post_thumbnail()) {
	$show_featured_image = 1;
	if ($featured_image == "on" && empty($blog_h) && isset($wp_page_template) && ($wp_page_template == "template-blog.php" || $wp_page_template == "template-home.php")) {
		$show_featured_image = 0;
	}else if ($featured_image == "on" && is_singular()) {
		$show_featured_image = 0;
	}else if ($featured_image == "on" && is_category()) {
		$show_featured_image = 0;
	}else if ($featured_image == "on") {
		$show_featured_image = 0;
	}
}else {
	$show_featured_image = 1;
	$himer_image = himer_image();
	if (!empty($himer_image) && $featured_image == "on") {
		$show_featured_image = 0;
	}
}

if ((is_single() && empty($blog_h)) || (is_page() && empty($blog_h) && (empty($wp_page_template) || ($wp_page_template != "template-blog.php" && $wp_page_template != "template-home.php")))) {
	$post_style = "";
}

$featured_style = "";
if (((is_single() && empty($blog_h)) || (is_page() && empty($blog_h) && (empty($wp_page_template) || ($wp_page_template != "template-blog.php" && $wp_page_template != "template-home.php")))) && isset($featured_image_style) && $featured_image_style != "" && $featured_image_style != "default") {
	$featured_style = " featured_style_2".(isset($featured_image_width) && $featured_image_style == "custom_size" && $featured_image_width > 350?" featured_style_350":"");
}

$custom_permission = himer_options("custom_permission");
$show_post = himer_options("show_post");
if (is_user_logged_in()) {
	$user_is_login = get_userdata($user_id);
	$roles = $user_is_login->allcaps;
}
if ('post' !== $post_type || ('post' === $post_type && ($custom_permission != "on" || $is_super_admin || (is_user_logged_in() && isset($roles["show_post"]) && $roles["show_post"] == 1) || (!is_user_logged_in() && $show_post == "on") || ($user_id > 0 && $user_id == $post_author)))) {
	$post_class = 'article-post article-post-only clearfix';
	$post_class .= (empty($blog_h) && isset($wp_page_template) && ($wp_page_template == "template-categories.php" || $wp_page_template == "template-tags.php" || $wp_page_template == "template-users.php")?'':' block-section-div');
	$post_class .= $featured_style;
	$post_class .= ($post_data->post_content == " post-no-content"?" post--content":"");
	$post_class .= ($post_style != "style_2" && $post_style != "style_3"?" post-style-1":"");
	$post_class .= ($post_style == "style_2"?" post-style-2":"");
	$post_class .= ($post_style == "style_3"?" post-style-3 post-with-columns".$post_columns.($masonry_style == "on"?" post-masonry":""):"");
	$post_class .= ($get_gender_class != ""?" ".$get_gender_class:"");
	if (isset($blog_h) && $blog_h == "blog_h") {?>
		<div id="post-<?php echo (int)$post_id?>" <?php post_class($post_class);?>>
	<?php }else {?>
		<article id="post-<?php echo (int)$post_id?>" <?php post_class($post_class);?>>
			<?php do_action("wpqa_post_article",$post_id,$post_type,(is_sticky()?"sticky":""),$user_id,$post_author);
			if (is_sticky()) {?>
				<span class="pinned-ribbon-lg d-none d-md-block"><span><?php esc_html_e("Pinned","himer")?></span></span>
			<?php }
			if (is_singular("post")) {
				do_action("wpqa_post_content",$post_id,$user_id,$post_author);
			}else {
				do_action("wpqa_post_content_loop",$post_id,$user_id,$post_author);
			}
	}
		if ($pending_posts) {?>
			<div class="load_span"><span class="loader_2"></span></div>
		<?php }?>
		<div class="single-inner-content">
			<?php if (isset($sort_meta_title_image) && is_array($sort_meta_title_image)) {
				foreach ($sort_meta_title_image as $sort_meta_title_image_key => $sort_meta_title_image_value) {
					if (isset($sort_meta_title_image_value["value"]) && $sort_meta_title_image_value["value"] == "image") {
						if ($post_style == "style_2" || $post_style == "style_3") {
							if ($what_post != "none") {
								include locate_template("theme-parts/banner.php");
							}
							do_action("wpqa_before_post_list",$post_id,$post_data,(isset($blog_h)?$blog_h:""));?>
							<div class="post-list<?php echo ("none" == $what_post || (!$what_post || $what_post == "image" || $what_post == "image_lightbox" || $what_post == "audio") && (!$himer_thumbnail_id || ($featured_image != 0 && $featured_image == "on"))?" post-list-0":"")?>">
						<?php }
					}else if (isset($sort_meta_title_image_value["value"]) && $sort_meta_title_image_value["value"] == "meta_title") {?>
						<header class="article-header<?php echo (((empty($blog_h) && isset($wp_page_template) && ($wp_page_template == "template-blog.php" || $wp_page_template == "template-home.php")) || !is_page()) && !is_attachment() && $author_by == "on"?"":" header-no-author").($author_by != "on" && $post_date != "on" && $title_post != "on" && $category_post != "on" && $post_comment != "on" && $post_views != "on"?" header-no-meta":"")?>">
							<?php if ((isset($blog_h) && $blog_h == "blog_h") || ((isset($wp_page_template) && ($wp_page_template == "template-blog.php" || $wp_page_template == "template-home.php")) || !is_page()) && !is_attachment()) {
								if ($post_style == "style_2" || $post_style == "style_3") {
									$category_post = $author_by = "";
									if ($post_style == "style_3") {
										$read_more = $post_share = "";
									}
								}
								if ($post_date == "on" || $category_post == "on" || $post_comment == "on" || $post_views == "on") {
									do_action("himer_before_post_meta",$post_data);?>
									<div class="post-meta">
										<?php himer_meta($post_date,$category_post,$post_comment,"","",$post_views,$post_id,$post_data)?>
									</div>
									<?php do_action("himer_after_post_meta",$post_data);
								}
							}
							
							if (empty($blog_h) && isset($wp_page_template) && ($wp_page_template == "template-categories.php" || $wp_page_template == "template-tags.php" || $wp_page_template == "template-users.php")) {
							}else {
								include locate_template("theme-parts/title.php");
								do_action("himer_after_title",$post_data);
							}
							if (((empty($blog_h) && isset($wp_page_template) && ($wp_page_template == "template-blog.php" || $wp_page_template == "template-home.php")) || !is_page()) && !is_attachment() && $author_by == "on") {
								echo "<div class='post-author-article'>";
									if ($post_author > 0) {
										echo sprintf(esc_html_x( '%s', 'post author', 'himer' ),'<a class="post-author author__avatar d-flex justify-content-center align-items-center" rel="author" href="' . esc_url(has_wpqa()?wpqa_profile_url($post_author):"") . '">');
										do_action("wpqa_user_avatar",array("user_id" => $post_author,"size" => "27","class" => "rounded-circle"));
										echo '<span>'.esc_html(get_the_author()).'</span></a>';
										if (isset($info_edited) && $info_edited != "") {
											echo '<i class="icon-alert tooltip-n icon-info-edited" title="'.$info_edited.'"></i>';
										}
									}else {
										$post_username = himer_post_meta("post_username","",false);
										echo esc_html($post_username);
									}
								echo "</div>";
							}
							if ($post_style != "style_2" && $post_style != "style_3" && $what_post != "none") {
								include locate_template("theme-parts/banner.php");
							}
							do_action("himer_content_before_header");?>
						</header>
					<?php }
				}
			}?>
			
			<div class="post-wrap-content<?php echo ((is_page() && empty($blog_h) && (empty($wp_page_template) || ($wp_page_template != "template-blog.php" && $wp_page_template != "template-home.php")))?"":" post-content ").(is_attachment()?" post-attachment ":"")?>">
				<div class="<?php echo (empty($blog_h) && isset($wp_page_template) && $wp_page_template == "template-contact.php"?"post-contact":"post-content-text")?>">
					<?php do_action("himer_before_post_content",$post_id,$post_data);
					$get_the_content = get_the_content();
					$get_the_content = apply_filters('the_content',$get_the_content);
					if ((is_single() && empty($blog_h)) || (is_page() && empty($blog_h) && (empty($wp_page_template) || ($wp_page_template != "template-blog.php" && $wp_page_template != "template-home.php")))) {
						if (is_attachment()) {
							$site_width = (int)himer_options("site_width");
							$mins_width = ($site_width > 1170?$site_width-1170:0);
							if (wp_attachment_is_image()) {
								if ($theme_sidebar == "menu_sidebar") {
									$img_width = 612+$mins_width;
									$img_height = 410+($mins_width/2);
								}else if ($theme_sidebar == "menu_left") {
									$img_width = 912+$mins_width;
									$img_height = 600+($mins_width/2);
								}else if ($theme_sidebar == "full") {
									$img_width = 1108+$mins_width;
									$img_height = 700+($mins_width/2);
								}else if ($theme_sidebar == "centered") {
									$img_width = 768+$mins_width;
									$img_height = 510+($mins_width/2);
								}else {
									$img_width = 829+$mins_width;
									$img_height = 550+($mins_width/2);
								}
								$img_url = wp_get_attachment_url();
								$image = himer_get_aq_resize_url($img_url,$img_width,$img_height);?>
								<div class="wp-caption aligncenter">
									<img width="<?php echo esc_attr($img_width)?>" height="<?php echo esc_attr($img_height)?>" class="attachment-<?php echo esc_attr($img_width)?>x<?php echo esc_attr($img_height)?>" alt="<?php echo esc_attr( get_the_title() ); ?>" src="<?php echo esc_url($image)?>">
									<?php if (!empty($post_data->post_excerpt)) {?>
										<p class="wp-caption-text"><?php echo get_the_excerpt(); ?></p>
									<?php }?>
								</div>
							<?php }else {?>
								<a href="<?php echo wp_get_attachment_url(); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php echo basename( get_permalink() ); ?></a><br>
								<p><?php if ( !empty( $post_data->post_excerpt ) ) the_excerpt(); ?></p>
							<?php }?>
							<div class="post-inner">
								<div class="post-inner-content"><?php echo make_clickable($get_the_content)?></div>
							</div><!-- End post-inner -->
						<?php }else {
							$show_post_filter = apply_filters('himer_show_post_filter',true);
							if ($show_post_filter == true) {
								do_action("himer_before_content",$post_id);
								echo make_clickable($get_the_content);
								do_action("himer_after_content",$post_id);
							}
						}
					}else {
						$show_full_text = apply_filters("himer_show_full_text",true);
						if ($show_full_text == true && strpos($get_the_content,'more-link') === false && $post_data->post_content != "") {?>
							<div class="all_not_single_post_content"><p><?php himer_excerpt($post_excerpt,$excerpt_type);?></p></div>
							<?php if ($pending_posts) {?>
								<div class='all_single_post_content himer_hide'>
									<?php echo make_clickable($get_the_content)?>
								</div>
							<?php }
						}else {
							echo make_clickable($get_the_content);
						}
					}?>
				</div>
				<?php if (empty($blog_h) && isset($wp_page_template) && $wp_page_template == "template-faqs.php") {
					$custom_faqs = himer_post_meta("faqs");
					include locate_template("theme-parts/faqs.php");
				}
				if (empty($blog_h) && isset($wp_page_template) && $wp_page_template == "template-categories.php") {
					include locate_template("theme-parts/categories.php");
				}
				if (empty($blog_h) && isset($wp_page_template) && $wp_page_template == "template-tags.php") {
					include locate_template("theme-parts/tags.php");
				}
				if (empty($blog_h) && isset($wp_page_template) && $wp_page_template == "template-users.php") {
					include locate_template("theme-parts/users.php");
				}
				if ( (is_single() && empty($blog_h)) || (is_page() && empty($blog_h) && (empty($wp_page_template) || ($wp_page_template != "template-blog.php" && $wp_page_template != "template-home.php"))) ) {
					wp_link_pages(array('before' => '<div class="pagination post-pagination">','after' => '</div>','link_before' => '<span>','link_after' => '</span>'));
					
					if ( $post_tags == "on" && 'post' === $post_type ) {
						$terms = wp_get_object_terms( $post_id, 'post_tag' );
						if (isset($terms) && is_array($terms) && !empty($terms)) {
							echo '<div class="tagcloud">';
								$terms_array = array();
								foreach ($terms as $term) :
									if (isset($term->slug) && isset($term->name)) {
										$get_term_link = get_term_link($term);
										if (is_string($get_term_link)) {
											echo '<a href="'.$get_term_link.'">'.$term->name.'</a>';
										}
									}
								endforeach;
							echo "</div>";
						}
					}
					if (is_singular("post")) {
						include locate_template("theme-parts/newsletter.php");
					}
				}
				do_action("wpqa_after_post_tags",$post_id,$post_data);?>
			</div>
			
			<?php do_action("wpqa_before_post_footer",$post_id,$post_data,(isset($blog_h)?$blog_h:""));

			if (!is_page_template("template-users.php") && !is_page_template("template-contact.php") && !is_page_template("template-faqs.php") && !is_page_template("template-categories.php") && !is_page_template("template-tags.php")) {?>
				<footer<?php echo (true == $pending_posts?" class='pending-post-footer'":"")?>>
					<div class="mt-2<?php echo (true == $pending_posts?"":" d-flex flex-wrap align-items-center justify-content-between")?>">
						<?php do_action("himer_action_before_edit_post",$post_id,(isset($blog_h)?$blog_h:""));
							if ( ((is_page() && empty($blog_h) && (empty($wp_page_template) || ($wp_page_template != "template-blog.php" && $wp_page_template != "template-home.php")))) && get_edit_post_link() ) {
								edit_post_link(sprintf(esc_html__( 'Edit %s', 'himer' ),the_title( '<span class="screen-reader-text">"', '"</span>', false )),'<span class="edit-link">','</span>');
							}
							
							if (is_single() && empty($blog_h) && !is_attachment()) {
								$post_delete   = himer_options("post_delete");
								$can_edit_post = himer_options("can_edit_post");
								$edit = ($is_super_admin || ((($user_id == $post_author && $post_author > 0)) && $can_edit_post == "on") || ($moderator_categories == true && isset($moderators_permissions['edit']) && $moderators_permissions['edit'] == "edit")?true:false);
								$delete = ($is_super_admin || ((($user_id == $post_author && $post_author > 0)) && $post_delete == "on") || ($moderator_categories == true && isset($moderators_permissions['delete']) && $moderators_permissions['delete'] == "delete")?true:false);
								if (has_wpqa() && ($edit == true || $delete == true)) {
									if ($edit == true) {
										echo '<span class="edit-link"><a class="custom-post-link" href="'.wpqa_edit_permalink($post_id,"post").'">'.esc_html__("Edit","himer").'</a></span>';
									}
									if ($delete == true) {
										echo '<span class="delete-link post-delete"><a class="custom-post-link" href="'.esc_url_raw(add_query_arg(array("activate_delete" => true,"delete" => $post_id,"wpqa_delete_nonce" => wp_create_nonce("wpqa_delete_nonce")),get_permalink($post_id))).'">'.esc_html__("Delete","himer").'</a></span>';
									}
								}
								do_action("himer_content_after_links");
							}
						if ($pending_posts) {
							wpqa_review_post($post_data,$is_super_admin,$moderators_permissions);
						}else {
							if ( (strpos(get_the_content(),'more-link') !== false || $read_more == "on") && !is_single() && (empty($blog_h) && (isset($wp_page_template) && ($wp_page_template == "template-blog.php" || $wp_page_template == "template-home.php")) || !is_page()) ) {?>
								<a<?php echo esc_attr($post_link_target)?> class="post-read-more btn btn__primary btn__sm" href="<?php echo esc_url(has_wpqa() && $post_type == "posts"?wpqa_custom_permalink($post_id,"view_posts_group","view_group_post"):get_permalink($post_id))?>" rel="bookmark" title="<?php esc_attr_e('Read','himer')?> <?php the_title()?>"><?php esc_html_e('Read more','himer')?></a>
							<?php }
							if (has_wpqa() && empty($blog_h)) {
								$share_facebook = (isset($post_share["share_facebook"]["value"])?$post_share["share_facebook"]["value"]:"");
								$share_twitter  = (isset($post_share["share_twitter"]["value"])?$post_share["share_twitter"]["value"]:"");
								$share_linkedin = (isset($post_share["share_linkedin"]["value"])?$post_share["share_linkedin"]["value"]:"");
								$share_whatsapp = (isset($post_share["share_whatsapp"]["value"])?$post_share["share_whatsapp"]["value"]:"");
								wpqa_share($post_share,$share_facebook,$share_twitter,$share_linkedin,$share_whatsapp);
							}
						}?>
					</div>
				</footer>
			<?php }
			if ($post_style == "style_2" || $post_style == "style_3") {?>
				</div><!-- End post-list -->
				<?php do_action("wpqa_after_post_list",$post_id,$post_data);
			}?>
		</div><!-- End single-inner-content -->
	<?php if (isset($blog_h) && $blog_h == "blog_h") {?>
		</div>
	<?php }else {
		if (is_single()) {
			do_action('himer_after_post_article',$post_id);
		}?>
		</article><!-- End article -->
	<?php }

	if ( ( (is_single() && empty($blog_h)) || (is_page() && empty($blog_h) && (empty($wp_page_template) || ($wp_page_template != "template-blog.php" && $wp_page_template != "template-home.php"))) ) && !is_attachment() ) :
		if (empty($order_sections)) :
			$order_sections = array(
				"author"        => array("sort" => esc_html__("About the author","himer"),"value" => "author"),
				"next_previous" => array("sort" => esc_html__("Next and Previous articles","himer"),"value" => "next_previous"),
				"advertising"   => array("sort" => esc_html__("Advertising","himer"),"value" => "advertising"),
				"related"       => array("sort" => esc_html__("Related articles","himer"),"value" => "related"),
				"comments"      => array("sort" => esc_html__("Comments","himer"),"value" => "comments"),
			);
		endif;
		foreach ($order_sections as $key_r => $value_r) :
			if ($value_r["value"] == "") :
				unset($order_sections[$key_r]);
			else :
				if (!is_page_template("template-blog.php") && !is_page_template("template-home.php") && !is_page_template("template-users.php") && !is_page_template("template-contact.php") && !is_page_template("template-faqs.php") && !is_page_template("template-categories.php") && !is_page_template("template-tags.php") && $value_r["value"] == "author" && isset($post_author)) :
					$the_author_meta_description = get_the_author_meta("description",$post_author);
					if ($the_author_meta_description != "") :
						do_action("wpqa_author",array("user_id" => $post_author,"author_page" => "single-author","owner" => "","type_post" => "","widget" => "single-author","class" => "card member-card text-center mb-4"));
					endif;
				elseif (is_single() && $value_r["value"] == "next_previous") :
					if ($post_nav_category == "on") {
						$previous_post = get_previous_post(true,'','category');
						$next_post = get_next_post(true,'','category');
					}else {
						$previous_post = get_previous_post();
						$next_post = get_next_post();
					}
					if ((isset($previous_post) && is_object($previous_post)) || (isset($next_post) && is_object($next_post))) :?>
						<div class="page-navigation block-section-div page-navigation-single clearfix">
							<?php do_action("himer_content_before_previous")?>
							<div class="row row-boot">
								<?php if (isset($previous_post) && is_object($previous_post)) {?>
									<div class="col col6 col-boot-sm-6 col-nav-previous">
										<div class="nav-previous">
											<div class="navigation-content">
												<span class="navigation-i"><i class="icon-arrow-left-c"></i></span>
												<span class="navigation-text"><?php esc_html_e('Previous article',"himer");?></span>
												<div class="clearfix"></div>
												<?php previous_post_link('%link');?>
											</div>
										</div>
									</div>
								<?php }
								if (isset($next_post) && is_object($next_post)) {?>
									<div class="col col6 col-boot-sm-6 col-nav-next">
										<div class="nav-next">
											<div class="navigation-content">
												<span class="navigation-i"><i class="icon-arrow-right-c"></i></span>
												<span class="navigation-text"><?php esc_html_e('Next article',"himer");?></span>
												<div class="clearfix"></div>
												<?php next_post_link('%link')?>
											</div>
										</div>
									</div>
								<?php }?>
							</div>
						</div><!-- End page-navigation -->
					<?php endif;
				elseif (has_wpqa() && wpqa_plugin_version >= "5.8" && !is_page_template("template-blog.php") && !is_page_template("template-home.php") && !is_page_template("template-users.php") && !is_page_template("template-contact.php") && !is_page_template("template-faqs.php") && !is_page_template("template-categories.php") && !is_page_template("template-tags.php") && $value_r["value"] == "advertising") :
					echo wpqa_ads("share_adv_type","share_adv_link","share_adv_code","share_adv_href","share_adv_img","","on","aalan-inside");
				elseif (is_single() && $value_r["value"] == "related") :
					include locate_template("theme-parts/related.php");
				elseif (!is_page_template("template-blog.php") && !is_page_template("template-home.php") && !is_page_template("template-users.php") && !is_page_template("template-contact.php") && !is_page_template("template-faqs.php") && !is_page_template("template-categories.php") && !is_page_template("template-tags.php") && $value_r["value"] == "comments" && (comments_open() || $count_post_all > 0)) :
					comments_template();
				endif;
			endif;
		endforeach;
	endif;
}else {
	echo '<article class="private-post article-post block-section-div clearfix">
		<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you do not have permission to view posts.","himer").'</p></div>
		'.(has_wpqa()?wpqa_paid_subscriptions(true):'').'
	</article>';
}?>
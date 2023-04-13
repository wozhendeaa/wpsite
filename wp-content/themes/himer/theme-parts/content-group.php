<?php if (isset($post_data)) {
	$post_data = $post_data;
}else if (isset($GLOBALS['post'])) {
	$post_data = $post = $GLOBALS['post'];
}else {
	$post_data = $post;
}
$post_author = $post_data->post_author;
$post_id = $post_data->ID;
$in_view_posts_group = wpqa_is_view_posts_group();
$active_reaction = himer_options("active_reaction");
$active_reaction_group_posts = himer_options("active_reaction_group_posts");
$is_sticky = ($post_data->post_status == 'publish'?is_sticky($post_id):false);
$edit_delete_posts_comments = himer_options("edit_delete_posts_comments");
$group_id = get_post_meta($post_id,"group_id",true);
$group_moderators = get_post_meta($group_id,"group_moderators",true);
$activate_male_female = apply_filters("wpqa_activate_male_female",false);
if ($activate_male_female == true) {
	$info_edited = '';
	$gender_author = ($post_author > 0?get_user_meta($post_author,'gender',true):"");
	$gender_post = get_post_meta($post_id,'wpqa_post_gender',true);
	if ($gender_post == "" && $gender_author != "") {
		update_post_meta($post_id,'wpqa_post_gender',$gender_author);
	}
	if ($gender_author != "" && $gender_author != $gender_post) {
		$info_edited = esc_html__("Some of main user info (Ex: name or gender) has been edited since this group post has been added","himer");
	}
}
if ($user_id > 0 && isset($group_moderators) && is_array($group_moderators) && in_array($user_id,$group_moderators)) {
	$allow_group_moderators = true;
}
$format_date_ago = himer_options("format_date_ago");
$format_date_ago_types = himer_options("format_date_ago_types");
if (!isset($format_date_ago_types["group_posts"]) || ($format_date_ago == "on" && isset($format_date_ago_types["group_posts"]) && $format_date_ago_types["group_posts"] == "group_posts")) {
	$last_time = human_time_diff(get_the_time('U',$post_id),current_time('timestamp'))." ".esc_html__("ago","himer");
}else {
	$last_time = get_the_time(himer_date_format,$post_id);
}
$posts_class = 'article-post clearfix content_group_item block-section-div ';
$posts_class .= (has_wpqa()?wpqa_get_gender_class($post_author,$post_id):"");?>
<article id="post-<?php echo (int)$post_id?>" <?php post_class($posts_class)?>>
	<?php if ($is_sticky == true) {?>
		<span class="pinned-ribbon-lg d-none d-md-block"><span><?php esc_html_e("Pinned","himer")?></span></span>
	<?php }?>
	<div class="content_group_item_header">
		<div class="group_avatar"><?php do_action("wpqa_action_avatar_link",array("user_id" => $post_author,"size" => 27,"span" => "span","pop" => "pop","class" => "rounded-circle"));?></div>
		<div class="col12 big-group-col">
			<div class="header-info">
				<div class="title">
					<h3>
						<?php $display_name = get_the_author_meta("display_name",$post_author);?>
						<a href="<?php echo (has_wpqa()?wpqa_profile_url($post_author):"");?>" title="<?php echo esc_attr($display_name);?>"><?php echo esc_html($display_name);?></a>
						<?php do_action("wpqa_verified_user",$post_author);
						do_action("wpqa_get_badge",$post_author,"","","category_points");?>
					</h3>
					<div class="posts-action">
						<?php if (isset($info_edited) && $info_edited != "") {
							echo '<i class="icon-alert tooltip-n icon-info-edited" title="'.$info_edited.'"></i>';
						}?>
						<a class="post-time" href="<?php echo wpqa_custom_permalink($post_id,"view_posts_group","view_group_post")?>"><i class="icon-lifebuoy"></i><?php echo esc_html($last_time);?></a>
						<?php $posts_delete = himer_options("posts_delete");
						if (($post_data->post_status == 'publish' && $posts_delete == "on" && $post_author == $user_id) || (isset($edit_delete_posts_comments["delete"]) && $edit_delete_posts_comments["delete"] == "delete" && isset($allow_group_moderators)) || $is_super_admin) {?>
							<a class="posts-delete btn btn__danger btn__sm" href="<?php echo esc_url_raw(add_query_arg(array("activate_delete" => true,"delete" => $post_id,"wpqa_delete_nonce" => wp_create_nonce("wpqa_delete_nonce")),wpqa_custom_permalink($post_id,"view_posts_group","view_group_post")))?>"><i class="icon-trash"></i><?php esc_html_e("Delete","himer")?></a>
						<?php }?>
					</div>
				</div>
				
			</div>
		</div>
	</div>
	<?php $featured_image_group_posts = himer_options("featured_image_group_posts");
	if ($featured_image_group_posts == "on") {
		$featured_image = get_post_meta($post_id,"_thumbnail_id",true);
		if ($featured_image != "") {
			$img_url = wp_get_attachment_url($featured_image,"full");
			if ($img_url != "") {
    			$featured_image_group_posts_lightbox = himer_options("featured_image_group_posts_lightbox");
    			$featured_image_group_posts_width = himer_options("featured_image_group_posts_width");
    			$featured_image_group_posts_height = himer_options("featured_image_group_posts_height");
    			$featured_image_group_posts_width = ($featured_image_group_posts_width != ""?$featured_image_group_posts_width:260);
    			$featured_image_group_posts_height = ($featured_image_group_posts_height != ""?$featured_image_group_posts_height:185);
    			$link_url = ($featured_image_group_posts_lightbox == "on"?$img_url:wpqa_custom_permalink($post_id,"view_posts_group","view_group_post"));
    			$last_image = himer_get_aq_resize_img($featured_image_group_posts_width,$featured_image_group_posts_height,"",$featured_image);
    			if (isset($last_image) && $last_image != "") {
    	    		echo "<div class='featured_image_group_posts'><a href='".$link_url."'>".$last_image."</a></div>
    	    		<div class='clearfix'></div>";
    			}
    		}
		}
	}
	$get_the_content = get_the_content();
	$get_the_content = apply_filters('the_content',$get_the_content);
	echo make_clickable($get_the_content);
	$editor_group_post_comments = himer_options("editor_group_post_comments");
	$featured_image_group_post_comments = himer_options("featured_image_group_post_comments");
	$posts_like = get_post_meta($post_id,"posts_like",true);
	$post_like_all = (is_array($posts_like)?count($posts_like):0);
	$group_comments = get_post_meta($group_id,"group_comments",true);
	$custom_posts_edit = himer_options("posts_edit");

	$count_post_all = (int)wpqa_count_comments($post_id);
	if ($activate_male_female == true) {
		$male_count_comments = wpqa_count_comments($post_id,"male_count_comments","like_meta");
		$female_count_comments = wpqa_count_comments($post_id,"female_count_comments","like_meta");
	}
	if ($in_view_posts_group) {
		if (has_wpqa() && ($count_post_all == 0 || $count_post_all === "" || ($activate_male_female == true && ($male_count_comments == 0 || $male_count_comments === "" || $female_count_comments == 0 || $female_count_comments === "")))) {
			wpqa_update_comments_count($post_id);
			$count_post_all = (int)wpqa_count_comments($post_id);
		}
	}

	if (is_user_logged_in() && ($is_super_admin || ($post_data->post_status == 'publish' && $custom_posts_edit == "on" && $post_author == $user_id) || (isset($edit_delete_posts_comments["edit"]) && $edit_delete_posts_comments["edit"] == "edit" && isset($allow_group_moderators)))) {
		$show_group_footer = true;
	}

	if ($post_data->post_status == 'publish') {
		if ((is_user_logged_in() && $group_comments == "on") || isset($allow_group_moderators) || $is_super_admin) {
			$show_group_footer = true;
		}
	}else if (is_user_logged_in() && ($is_super_admin || (isset($edit_delete_posts_comments["edit"]) && $edit_delete_posts_comments["edit"] == "edit" && isset($allow_group_moderators)))) {
		$show_group_footer = true;
	}

	if (isset($show_group_footer)) {?>
		<footer class="question-footer posts-footer">
			<?php if ($post_data->post_status == 'publish') {
				$count_comments_female = (int)(has_wpqa()?wpqa_count_comments($post_id,"female_count_comments","like_meta"):get_comments_number());
				$count_comments_male = (int)(has_wpqa()?wpqa_count_comments($post_id,"male_count_comments","like_meta"):get_comments_number());
				$gender_answers_other = himer_options("gender_answers_other");
				if ($gender_answers_other == "on") {
					$count_post_comments = (int)(has_wpqa()?wpqa_count_comments($post_id,"count_post_comments","like_meta"):get_comments_number());
					$count_comments_other = (int)($count_post_comments-($count_comments_female+$count_comments_male));
				}?>
				<ul class="footer-meta<?php echo (true == $activate_male_female && $gender_answers_other == "on" && $count_comments_other > 0?" footer-meta-other":"")?>">
					<?php if ($active_reaction == "on" && $active_reaction_group_posts == "on") {?>
						<li class="react-meta reaction-id" data-id="<?php echo (int)$post_id?>">
							<?php do_action("wpqa_show_reactions","",$post_id);?>
						</li>
					<?php }else {?>
						<li class="posts-likes<?php echo (is_user_logged_in()?" posts-likes-links":"")?>">
							<div class="small_loader loader_2"></div>
							<?php if (is_user_logged_in()) {
								if (is_array($posts_like) && in_array($user_id,$posts_like)) {
									$class = "unlike-posts";
									$title = esc_html__("Unlike","himer");
								}else {
									$class = "like-posts";
									$title = esc_html__("Like","himer");
								}?>
								<a href="#" class="<?php echo esc_attr($class)?> tooltip-n" data-id="<?php echo (int)$post_id?>" original-title="<?php echo esc_attr($title)?>">
							<?php }?>
							<i class="icon-heart"></i>
							<span><?php echo himer_count_number($post_like_all)?></span>
							<?php echo " ".sprintf(_n("Like","Likes",$post_like_all,"himer"),$post_like_all);
							if (is_user_logged_in()) {?>
								</a>
							<?php }?>
						</li>
					<?php }
					if ($activate_male_female == true) {?>
						<li class="answer-meta-gender answer-meta-her posts-comments"><a href="<?php echo wpqa_custom_permalink($post_id,"view_posts_group","view_group_post")?>#comments-female"><i class="icon-comment"></i><span class="question-span"><?php echo himer_count_number($count_comments_female)?></span></a></li>
						<li class="answer-meta-gender answer-meta-him posts-comments"><a href="<?php echo wpqa_custom_permalink($post_id,"view_posts_group","view_group_post")?>#comments-male"><i class="icon-comment"></i><span class="question-span"><?php echo himer_count_number($count_comments_male)?></span></a></li>
						<?php if ($gender_answers_other == "on" && $count_comments_other > 0) {?>
							<li class="answer-meta-gender answer-meta-other posts-comments"><a href="<?php echo wpqa_custom_permalink($post_id,"view_posts_group","view_group_post")?>#comments-other"><i class="icon-comment"></i><span class="question-span"><?php echo himer_count_number($count_comments_other)?></span></a></li>
						<?php }
					}else {?>
						<li class="posts-comments"><a href="<?php echo wpqa_custom_permalink($post_id,"view_posts_group","view_group_post")?>#group-comments"><i class="icon-comment"></i><span class="question-span"><?php echo sprintf(_n("%s Comment","%s Comments",$count_post_all,"himer"),$count_post_all)?></span></a></li>
					<?php }?>
				</ul>
			<?php }?>
			<div class="posts-footer-div<?php echo ('publish' == $post_data->post_status?'':' posts-footer-div-review')?>">
				<?php $group_id = get_post_meta($post_id,"group_id",true);
				$group_comments = get_post_meta($group_id,"group_comments",true);
				$group_moderators = get_post_meta($group_id,"group_moderators",true);
				$custom_posts_edit = himer_options("posts_edit");
				if (is_user_logged_in() && ($is_super_admin || ($post_data->post_status == 'publish' && $custom_posts_edit == "on" && $post_author == $user_id) || (isset($edit_delete_posts_comments["edit"]) && $edit_delete_posts_comments["edit"] == "edit" && isset($allow_group_moderators)))) {?>
					<a class="button-default edit-group-posts btn btn__success btn__sm" href="<?php echo wpqa_custom_permalink($post_id,"edit_posts_group","edit_group_post")?>"><i class="icon-pencil"></i></a>
				<?php }
				if ($post_data->post_status == 'publish') {
					if ((is_user_logged_in() && $group_comments == "on") || isset($allow_group_moderators) || $is_super_admin) {?>
						<a class="meta-answer meta-comment-a meta-group-comments btn btn__primary btn__sm<?php echo (!$in_view_posts_group && $editor_group_post_comments == "on"?"":" meta-group-comments-ajax")?>" href="<?php echo wpqa_custom_permalink($post_id,"view_posts_group","view_group_post")?>#respond"><?php esc_html_e("Comment","himer")?></a>
					<?php }
				}else if (is_user_logged_in() && ($is_super_admin || (isset($edit_delete_posts_comments["edit"]) && $edit_delete_posts_comments["edit"] == "edit" && isset($allow_group_moderators)))) {
					$group_users_array = get_post_meta($group_id,"group_users_array",true);
					echo '<div class="group_review_button">
						<div class="cover_loader himer_hide"><div class="small_loader loader_2"></div></div>';
						echo '<a href="#" class="button-default agree_posts_group btn btn__success btn__sm" data-group="'.$post_id.'" data-user="'.$post_author.'">'.esc_html__("Agree","himer").'</a>';
						if (isset($group_users_array) && is_array($group_users_array) && in_array($post_author,$group_users_array)) {
							$blocked_users = get_post_meta($group_id,"blocked_users_array",true);
							if (isset($blocked_users) && is_array($blocked_users) && in_array($post_author,$blocked_users)) {
								echo '<a href="#" class="button-default unblock_user_group btn btn__success btn__sm" data-group="'.$group_id.'" data-user="'.$post_author.'">'.esc_html__("Unblock","himer").'</a>';
							}else {
								echo '<a href="#" class="button-default remove_user_group btn btn__primary btn__sm" data-group="'.$group_id.'" data-user="'.$post_author.'">'.esc_html__("Remove","himer").'</a>
								<a href="#" class="button-default block_user_group btn btn__danger btn__sm" data-group="'.$group_id.'" data-user="'.$post_author.'">'.esc_html__("Block","himer").'</a>';
							}
						}
					echo '</div>';
				}?>
			</div>
		</footer>
	<?php }?>
	<div class="embed_comments">
		<div class="clearfix"></div>
		<?php if ((is_user_logged_in() && $group_comments == "on") || isset($allow_group_moderators) || $is_super_admin) {?>
			<!-- Write-comment -->
			<div class="write_comment himer_hide">
				<?php include locate_template("comment-parts/comment-group-form.php");?>
			</div>
		<?php }
		$show_number = 2;
		$number = (!$in_view_posts_group?array('number' => $show_number):array());
		$paged = himer_paged();
		$comments_per_page = get_option('comments_per_page');
		$offset = ($paged -1) * $comments_per_page;
		$offset = ($in_view_posts_group && get_option('page_comments')?array('offset' => $offset,'number' => $comments_per_page):array());
		$args = array('post_id' => $post_id,'status' => 'approve','orderby' => 'comment_date','order' => 'DESC');
		$author__not_in = array();
    	$block_users = himer_options("block_users");
		if ($block_users == "on") {
			if ($user_id > 0) {
				$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
				if (is_array($get_block_users) && !empty($get_block_users)) {
					$author__not_in = array("author__not_in" => $get_block_users);
				}
			}
		}
		if ($activate_male_female == true && $in_view_posts_group) {
			$activate_comments = true;
		}else {
			$comments_args = get_comments(array_merge($author__not_in,$number,$offset,$args));
		}
		if ((isset($comments_args) && is_array($comments_args) && !empty($comments_args)) || isset($activate_comments)) {
			if (!$in_view_posts_group && $count_post_all > $show_number) {
				echo '<a class="button-default load-more-comments btn btn__primary btn__block" href="'.wpqa_custom_permalink($post_id,"view_posts_group","view_group_post").'#group-comments">'.sprintf(_n("View %s more comment","View %s more comments",($count_post_all-$show_number),"himer"),($count_post_all-$show_number)).'</a>';
			}
			if ($activate_male_female == true && $in_view_posts_group) {?>
				</div></div></div>
				<?php $array = array("female","male","other");
				if (is_array($array) && !empty($array)) {?>
					<div id="group-comments">
						<?php foreach ($array as $type) {
							$get_comments = $meta_query = $comments_args = array();
							$comments_args = array_merge($author__not_in,$number,$offset,$args);
							if ($type == "other") {
								$count_comments_other = (int)(has_wpqa()?wpqa_count_comments($post_id,$type."_count_comments","like_meta"):get_comments_number());
								if ($count_comments_other == 0) {
									$count_post_comments = wpqa_comment_counter($post_id,"parent");
									$count_comments_other = $count_post_comments-($male_count_comments+$female_count_comments);
									$count_comments_other = ($count_comments_other > 0?$count_comments_other:0);
								}
								$values[] = $count_comments = (int)$count_comments_other;
							}else {
								$values[] = $count_comments = (int)(has_wpqa()?wpqa_count_comments($post_id,$type."_count_comments","like_meta"):get_comments_number());
							}
							$max_num_pages = ceil($count_comments/$comments_per_page);
							if (isset($max_num_pages) && $max_num_pages >= $paged) {
								$meta_key = "wpqa_comment_gender";
								if ($type == "male") {
									$meta_value = "1";
									$class_value = "him-user";
								}else if ($type == "female") {
									$meta_value = "2";
									$class_value = "her-user";
								}else if ($type == "other") {
									$meta_value = "3";
									$class_value = "other-user";
								}
								$meta_query = (isset($meta_value) && $meta_value != ""?array("parent" => 0,"meta_query" => array(array("key" => $meta_key,"value" => $meta_value,"compare" => "="))):array());
								if ($type == "other") {
									$meta_query = array("parent" => 0,"meta_query" => array('relation' => 'or',array("key" => $meta_key,"compare" => "NOT EXISTS"),array(array("key" => $meta_key,"value" => 1,"compare" => "!="),array("key" => $meta_key,"value" => 2,"compare" => "!="))));
								}
								$comments_args = array_merge($meta_query,$comments_args);
								$get_comments = get_comments($comments_args);
								if ($count_comments > 0) {?>
									<div id="comments-<?php echo esc_attr($type)?>" class="post-section block-section-div answers-area<?php echo (isset($class_value)?" ".$class_value:"")?>">
										<div class="post-inner">
											<div class="answers-tabs answers__header">
												<?php if ($type == "female") {
													$title = sprintf(_n("%s Her Comment","%s Her Comments",$count_comments,"himer"),$count_comments);
												}else if ($type == "male") {
													$title = sprintf(_n("%s Him Comment","%s Him Comments",$count_comments,"himer"),$count_comments);
												}else {
													$title = sprintf(_n("%s Comment","%s Comments",$count_comments,"himer"),$count_comments);
												}?>
												<h3 class="section-title"><span><?php echo ("female" == $type?"<i class='icon-female icon-gender-section'></i>":"").("male" == $type?"<i class='icon-male icon-gender-section'></i>":"").$title;?></h3>
											</div><!-- End answers-tabs -->
											<ol<?php echo (true != $activate_male_female && $in_view_posts_group?' id="group-comments"':"")?> class="commentlist clearfix">
												<?php wp_list_comments(array("callback" => "wpqa_comment","comment_type" => "comment_group","comments_type" => $type),(isset($get_comments) && is_array($get_comments) && !empty($get_comments)?$get_comments:$comments_args));?>
												</li>
											</ol>
			    						<div class="clearfix"></div>
										</div><!-- End post-inner -->
									</div><!-- End post-section -->
			    				<?php }
						    }
						}?>
					</div>
				<?php }?>
				<div><div><div>
			<?php }else {?>
				<ol<?php echo (true != $activate_male_female && $in_view_posts_group?' id="group-comments"':"")?> class="commentlist clearfix">
					<?php wp_list_comments(array("callback" => "wpqa_comment","comment_type" => "comment_group","group_comments" => $group_comments,"allow_group_moderators" => (isset($allow_group_moderators)?$allow_group_moderators:false)),$comments_args);?>
					</li>
				</ol>
			<?php }
		}?>
	</div>
</article>
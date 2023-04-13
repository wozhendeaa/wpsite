<?php $custom_permission = himer_options("custom_permission");
$user_id = get_current_user_id();
if (is_user_logged_in()) {
	$user_is_login = get_userdata($user_id);
	$roles = $user_is_login->allcaps;
}
$post_type = $post->post_type;

$place_comment_form = himer_options($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?'place_answer_form':'place_comment_form');
$place_comment_form = ($place_comment_form != ""?$place_comment_form:"after");

$wpqa_server = apply_filters('wpqa_server','SCRIPT_FILENAME');
if (!empty($wpqa_server) && 'comments.php' == basename($wpqa_server)) :
	die (esc_html__('Please do not load this page directly. Thanks!',"himer"));
endif;
if ( post_password_required() ) : ?>
	<p class="no-comments">
		<?php if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
			esc_html_e("This question is password protected. Enter the password to view answers.","himer");
		}else {
			esc_html_e("This post is password protected. Enter the password to view comments.","himer");
		}?>
	</p>
	<?php return;
endif;

if ($place_comment_form == "before") {
	include locate_template("comment-parts/comment-form.php");
}

$activate_male_female = apply_filters("wpqa_activate_male_female",false);
$count_comments = $count_post_all = (int)(has_wpqa()?wpqa_count_comments($post->ID):get_comments_number());
if (has_wpqa() && $activate_male_female == true) {
	$male_count_comments = wpqa_count_comments($post->ID,"male_count_comments","like_meta");
	$female_count_comments = wpqa_count_comments($post->ID,"female_count_comments","like_meta");
}
if (has_wpqa() && ($count_post_all == 0 || $count_post_all === "" || ($activate_male_female == true && ($male_count_comments == 0 || $male_count_comments === "" || $female_count_comments == 0 || $female_count_comments === "")))) {
	wpqa_update_comments_count($post->ID);
	$count_post_all = (int)wpqa_count_comments($post->ID);
}
if ($activate_male_female == true && ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type)) {
	$the_best_answer = get_post_meta($post->ID,"the_best_answer",true);
	if ($the_best_answer != "" && $the_best_answer > 0) {
		$block_users = himer_options("block_users");
		if ($block_users == "on") {
			if ($user_id > 0) {
				$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
				$get_comment = get_comment($the_best_answer);
				if (is_array($get_block_users) && !empty($get_block_users) && in_array($get_comment->user_id,$get_block_users)) {
					$the_best_answer = "";
				}
			}
		}
	}
	if ($the_best_answer != "") {
		$array = array("best","female","male","other");
	}else {
		$array = array("female","male","other");
	}
}else {
	$array = array("single");
}
$k_ad = 1;
$get_the_permalink = get_the_permalink($post->ID);
if ( have_comments() && $count_post_all > 0 ) :?>
	<div id="comments">
		<?php $cpage = get_query_var("cpage");
		$comments_per_page = get_option('comments_per_page');
		if (is_array($array) && !empty($array)) {
			foreach ($array as $type) {
				if ($type != "single" && $type != "best") {
					if ($type == "other") {
						$count_comments_other = (int)(has_wpqa()?wpqa_count_comments($post->ID,$type."_count_comments","like_meta"):get_comments_number());
						if ($count_comments_other == 0) {
							$count_post_comments = wpqa_comment_counter($post->ID,"parent");
							$count_comments_other = $count_post_comments-($male_count_comments+$female_count_comments);
							$count_comments_other = ($count_comments_other > 0?$count_comments_other:0);
						}
						$values[] = $count_comments = (int)$count_comments_other;
					}else {
						$values[] = $count_comments = (int)(has_wpqa()?wpqa_count_comments($post->ID,$type."_count_comments","like_meta"):get_comments_number());
					}
					$max_num_pages = ceil($count_comments/$comments_per_page);
				}
				if ($type == "single" || ($type == "best" && $cpage < 2) || ($type != "single" && $type != "best" && isset($max_num_pages) && $max_num_pages >= $cpage)) {
					if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
						$custom_answer_tabs = himer_post_meta("custom_answer_tabs");
						if ($custom_answer_tabs == "on") {
							$answers_tabs = himer_post_meta('answers_tabs');
						}else {
							$answers_tabs = himer_options('answers_tabs');
						}
						$answers_tabs = apply_filters("wpqa_answers_tabs",$answers_tabs);
						$answers_tabs_keys = array_keys($answers_tabs);
						if (isset($answers_tabs) && is_array($answers_tabs)) {
							$a_count = 0;
							while ($a_count < count($answers_tabs)) {
								if (isset($answers_tabs[$answers_tabs_keys[$a_count]]["value"]) && $answers_tabs[$answers_tabs_keys[$a_count]]["value"] != "" && $answers_tabs[$answers_tabs_keys[$a_count]]["value"] != "0") {
									$first_one = $a_count;
									break;
								}
								$a_count++;
							}
							
							if (isset($first_one) && $first_one !== "") {
								$first_one = $answers_tabs[$answers_tabs_keys[$first_one]]["value"];
							}
							
							if (isset($_GET["show"]) && $_GET["show"] != "") {
								$first_one = $_GET["show"];
							}
						}
						if (isset($first_one) && $first_one !== "") {
							$answers_tabs_foreach = apply_filters("wpqa_answers_tabs_foreach",true,$answers_tabs,$first_one);
						}
					}

					$show_answer = himer_options("show_answer");
					$show_comment = himer_options("show_comment");
					$is_super_admin = is_super_admin($user_id);
					if ($user_id > 0) {
						$include_unapproved = array($user_id);
					}else {
						$unapproved_email = wp_get_unapproved_comment_author_email();
						if ($unapproved_email) {
							$include_unapproved = array($unapproved_email);
						}
					}
					$meta_key = "wpqa_comment_gender";
					if ($type == "best") {
						$meta_key = "best_answer_comment";
						$meta_value = "best_answer_comment";
						$class_value = "best-answers";
					}else if ($type == "male") {
						$meta_value = "1";
						$class_value = "him-user";
					}else if ($type == "female") {
						$meta_value = "2";
						$class_value = "her-user";
					}else if ($type == "other") {
						$meta_value = "3";
						$class_value = "other-user";
					}
					if (isset($meta_value)) {
						$meta_query_array = array("meta_query" => array(array("key" => $meta_key,"value" => $meta_value,"compare" => "=")));
						$meta_query_array = ($type == "best"?$meta_query_array:array_merge($meta_query_array,array("parent" => 0)));
					}
					$meta_query = (isset($meta_value)?$meta_query_array:array());
					if ($type == "other") {
						$meta_query = array("parent" => 0,"meta_query" => array('relation' => 'or',array("key" => $meta_key,"compare" => "NOT EXISTS"),array(array("key" => $meta_key,"value" => 1,"compare" => "!="),array("key" => $meta_key,"value" => 2,"compare" => "!="))));
					}
					$include_unapproved_args = (isset($include_unapproved)?array('include_unapproved' => $include_unapproved):array());
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
					$get_comments_args = array_merge($meta_query,$author__not_in,$include_unapproved_args,array('post_id' => $post->ID,'status' => 'approve'));
					if (($post_type != wpqa_questions_type && $post_type != wpqa_asked_questions_type && ($is_super_admin || $custom_permission != "on" || (is_user_logged_in() && $custom_permission == "on" && isset($roles["show_comment"]) && $roles["show_comment"] == 1) || (!is_user_logged_in() && $show_comment == "on"))) || (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && ($is_super_admin || $custom_permission != "on" || (is_user_logged_in() && $custom_permission == "on" && isset($roles["show_answer"]) && $roles["show_answer"] == 1) || (!is_user_logged_in() && $show_answer == "on")))) {
						if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
							if (isset($first_one) && $first_one !== "") {
								if ($first_one == 'reacted') {
									$comments_args = get_comments(array_merge($get_comments_args,array('orderby' => array('meta_value_num' => 'DESC','comment_date' => 'ASC'),'meta_key' => 'wpqa_reactions_count','order' => 'DESC')));
								}else if ($first_one == 'votes') {
									$comments_args = get_comments(array_merge($get_comments_args,array('orderby' => array('meta_value_num' => 'DESC','comment_date' => 'ASC'),'meta_key' => 'comment_vote','order' => 'DESC')));
								}else if ($first_one == 'oldest') {
									$comments_args = get_comments(array_merge($get_comments_args,array('orderby' => 'comment_date','order' => 'ASC')));
								}else if ($first_one == 'recent') {
									$comments_args = get_comments(array_merge($get_comments_args,array('orderby' => 'comment_date','order' => 'DESC')));
								}else if ($first_one == 'random') {
									$comments_args = get_comments(array_merge($get_comments_args,array('orderby' => 'rand','order' => 'DESC')));
									shuffle($comments_args);
								}
							}else {
								$comments_args = get_comments(array_merge($get_comments_args,array('orderby' => 'comment_date','order' => 'DESC')));
							}
						}
						if ($count_comments > 0) {?>
							<div id="comments-<?php echo esc_attr($type)?>" class="post-section block-section-div answers-area<?php echo (isset($class_value)?" ".$class_value:"")?>">
								<div class="post-inner">
									<?php $filter_show_comments = apply_filters("himer_filter_show_comments",true,$post_type,$post->ID);
									if ($filter_show_comments == true) {
										if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {?>
											<div class="answers-tabs answers__header">
										<?php }
											if (wpqa_questions_type == $post_type || wpqa_asked_questions_type == $post_type) {
												if ($type == "best") {
													$title = esc_html__("Best Answer","himer");
												}else if ($type == "female") {
													$title = sprintf(_n("%s Her Answer","%s Her Answers",$count_comments,"himer"),$count_comments);
												}else if ($type == "male") {
													$title = sprintf(_n("%s Him Answer","%s Him Answers",$count_comments,"himer"),$count_comments);
												}else {
													$title = sprintf(_n("%s Answer","%s Answers",$count_comments,"himer"),$count_comments);
												}
											}else {
												$title = sprintf(_n("%s Comment","%s Comments",$count_comments,"himer"),$count_comments);
											}?>
											<h3 class="section-title<?php echo (wpqa_questions_type == $post_type || $post_type == wpqa_asked_questions_type?"":" comments__header")?>"><span><?php echo ("female" == $type?"<i class='icon-female icon-gender-section'></i>":"").("male" == $type?"<i class='icon-male icon-gender-section'></i>":"").("best" == $type?"<i class='icon-android-done icon-gender-section'></i>":"").$title;?></h3>
										<?php if (isset($answers_tabs_foreach) && $answers_tabs_foreach == true && ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && isset($first_one) && $first_one !== "") {
											if ($type != "best") {?>
												<div class="dropdown custom-dropdown dropdown-as-select">
													<?php if ((isset($_GET["show"]) && $_GET["show"] === "oldest") || $first_one === "oldest") {
														$selected_value = esc_html__("Oldest","himer");
													}else if ((isset($_GET["show"]) && $_GET["show"] === "recent") || $first_one === "recent") {
														$selected_value = esc_html__("recent","himer");
													}else if ((isset($_GET["show"]) && $_GET["show"] === "random") || $first_one === "random") {
														$selected_value = esc_html__("Random","himer");
													}else if ((isset($_GET["show"]) && $_GET["show"] === "reacted") || $first_one === "reacted") {
														$selected_value = esc_html__("Reacted","himer");
													}else {
														$selected_value = esc_html__("Voted","himer");
													}?>
													<button class="dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php esc_html_e("Sort by","himer")?> <span class="selected-option"><?php echo stripcslashes($selected_value)?></span></button>
													<div class="dropdown-menu">
														<?php foreach ($answers_tabs as $key => $value) {
															if ($key == "reacted" && isset($answers_tabs["reacted"]["value"]) && $answers_tabs["reacted"]["value"] == "reacted") {?>
																<a class="dropdown-item" href="<?php echo esc_url_raw(add_query_arg(array("show" => "reacted"),$get_the_permalink))?>#comments"><?php esc_html_e("Reacted","himer")?></a>
															<?php }else if ($key == "votes" && isset($answers_tabs["votes"]["value"]) && $answers_tabs["votes"]["value"] == "votes") {?>
																<a class="dropdown-item" href="<?php echo esc_url_raw(add_query_arg(array("show" => "votes"),$get_the_permalink))?>#comments"><?php esc_html_e("Voted","himer")?></a>
															<?php }else if ($key == "oldest" && isset($answers_tabs["oldest"]["value"]) && $answers_tabs["oldest"]["value"] == "oldest") {?>
																<a class="dropdown-item" href="<?php echo esc_url_raw(add_query_arg(array("show" => "oldest"),$get_the_permalink))?>#comments"><?php esc_html_e("Oldest","himer")?></a>
															<?php }else if ($key == "recent" && isset($answers_tabs["recent"]["value"]) && $answers_tabs["recent"]["value"] == "recent") {?>
																<a class="dropdown-item" href="<?php echo esc_url_raw(add_query_arg(array("show" => "recent"),$get_the_permalink))?>#comments"><?php esc_html_e("Recent","himer")?></a>
															<?php }else if ($key == "random" && isset($answers_tabs["random"]["value"]) && $answers_tabs["random"]["value"] == "random") {?>
																<a class="dropdown-item" href="<?php echo esc_url_raw(add_query_arg(array("show" => "random"),$get_the_permalink))?>#comments"><?php esc_html_e("Random","himer")?></a>
															<?php }
														}?>
													</div>
												</div>
											<?php }
										}
										if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {?>
											</div><!-- End answers-tabs -->
										<?php }
										if (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && isset($first_one) && $first_one !== "" && isset($answers_tabs)) {
											do_action("wpqa_answers_after_tabs",$answers_tabs,$first_one);
										}
										if (($post_type != wpqa_questions_type && $post_type != wpqa_asked_questions_type && ($is_super_admin || $custom_permission != "on" || (is_user_logged_in() && $custom_permission == "on" && isset($roles["show_comment"]) && $roles["show_comment"] == 1) || (!is_user_logged_in() && $show_comment == "on"))) || (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && ($is_super_admin || $custom_permission != "on" || (is_user_logged_in() && $custom_permission == "on" && isset($roles["show_answer"]) && $roles["show_answer"] == 1) || (!is_user_logged_in() && $show_answer == "on")))) {?>
											<ol class="list-unstyled mb-0 commentlist clearfix">
												<?php if (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && isset($first_one) && $first_one !== "") {
													$comments_args = (isset($comments_args)?$comments_args:array());
													$comments_args = apply_filters("wpqa_comments_args",$comments_args,$first_one,$post->ID);
												}
												$read_more_answer = himer_options("read_more_answer");
												$comment_read_more = (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && $read_more_answer == "on"?array('comment_read_more' => true):array());
												$answers_sort = esc_html(isset($_GET["show"]) && $_GET["show"] !== ""?$_GET["show"]:(isset($first_one) && $first_one !== ""?$first_one:""));
												$answers_sort = ($answers_sort != ""?array("answers_sort" => $answers_sort):array());
												$comments_type = ($type != ""?array("comments_type" => $type):array());
												$list_comments_args = array_merge($answers_sort,$comments_type,array('callback' => 'wpqa_comment'),$comment_read_more);
												if (isset($comments_args)) {
													$comment_order = get_option('comment_order');
													if ($comment_order == "desc") {
														$comments_args = array_reverse($comments_args);
													}
													wp_list_comments($list_comments_args,$comments_args);
												}else {
													$show_comments = apply_filters("wpqa_show_comments",true);
													if ($show_comments == true) {
														$comments_args = get_comments(array_merge($get_comments_args,array('orderby' => 'comment_date','order' => 'DESC')));
														if (isset($comments_args) && is_array($comments_args) && !empty($comments_args)) {
															wp_list_comments($list_comments_args,$comments_args);
														}else {
															wp_list_comments($list_comments_args);
														}
													}
												}?>
											</ol><!-- End commentlist -->
										<?php }else {
											if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
												echo '<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you do not have permission to view answers.","himer").' '.(has_wpqa()?wpqa_paid_subscriptions():'').'</p></div>';
											}else {
												echo '<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you do not have permission to view comments.","himer").' '.(has_wpqa()?wpqa_paid_subscriptions():'').'</p></div>';
											}
										}
									}?>
									<div class="clearfix"></div>
								</div><!-- End post-inner -->
							</div><!-- End post-section -->
						<?php }
					}
				}
			}
		}?>
	</div><!-- End comments -->
	
	<?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
		<div class="pagination comments-pagination">
			<?php if (isset($values) && is_array($values) && !empty($values)) {
				$count_post_all = max($values);
			}
			$max_num_pages = ceil($count_post_all/$comments_per_page);
			$comment_pagination = himer_options(($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?'answer_pagination':'comment_pagination'));
			if ($comment_pagination != "pagination") {
				if (has_wpqa()) {
					wpqa_load_pagination(array(
						"post_pagination" => $comment_pagination,
						"max_num_pages" => $max_num_pages,
						"it_answer_pagination" => true,
						"its_post_type" => wpqa_questions_type,
						"its_answer" => true,
					));
				}
			}else {
				paginate_comments_links(array('total' => $max_num_pages,'prev_text' => '<i class="icon-arrow-left-b"></i>', 'next_text' => '<i class="icon-arrow-right-b"></i>'));
			}?>
		</div><!-- End comments-pagination -->
		<div class="clearfix"></div>
	<?php endif;
endif;

if ($place_comment_form == "after") {
	include locate_template("comment-parts/comment-form.php");
}?>
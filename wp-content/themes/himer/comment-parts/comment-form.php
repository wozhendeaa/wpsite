<?php $comments_open = apply_filters("himer_comments_open",true);
if ($comments_open == true && comments_open()) {
	$can_add_answer = apply_filters("wpqa_can_add_answer",true,$user_id,$custom_permission,(isset($roles)?$roles:array()),$post);
	if ($can_add_answer == true) {
		echo '<div id="respond-all" class="'.($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?'':'block-section-div').(isset($edit_delete)?' respond-edit-delete himer_hide':'').'">';
			$comment_editor = himer_options(($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?'answer_editor':'comment_editor'));
			$fields =  array(
				'author' => '<div class="form-input"><input class="form-control" type="text" name="author" value="" id="comment_name" aria-required="true" placeholder="'.esc_attr__('Your Name',"himer").'"></div>',
				'email'  => '<div class="form-input form-input-last"><input class="form-control" type="email" name="email" value="" id="comment_email" aria-required="true" placeholder="'.esc_attr__('Email',"himer").'"></div>',
				'url'    => '<div class="form-input form-input-full"><input class="form-control" type="url" name="url" value="" id="comment_url" placeholder="'.esc_attr__('URL',"himer").'"></div>',
			);

			if (isset($comment_editor) && $comment_editor == "on") {
				$settings = array("textarea_name" => "comment","media_buttons" => true,"textarea_rows" => 10);
				$settings = apply_filters('wpqa_comment_editor_setting',$settings);
				ob_start();
				$rand = rand(1,1000);
				wp_editor("","comment-".$rand,$settings);
				$comment_contents = ob_get_clean();
			}else {
				$comment_contents = '<textarea cols="58" rows="8" class="form-control" id="comment" name="comment" aria-required="true" placeholder="'.apply_filters("himer_filter_textarea_comment".($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?"_question":""),($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?esc_html__("Answer","himer"):esc_html__("Comment","himer"))).'"></textarea>';
			}

			$activate_register = himer_options("activate_register");
			$activate_login = himer_options("activate_login");

			$comment_form_args = array(
				'must_log_in'          => ($activate_login != 'disabled'?'<div class="alert-message warning"><i class="icon-flag"></i><p>'.($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?esc_html__("You must login to add an answer.","himer"):esc_html__("You must login to add a new comment.","himer")).'</p></div>'.do_shortcode("[wpqa_login]".'<div class="pop-footer pop-footer-comments">'.(has_wpqa() && $activate_register != 'disabled'?wpqa_signup_in_popup():'').'</div>'):''),
				'logged_in_as'         =>  '<p class="comment-login">'.esc_html__('Logged in as',"himer").'<a class="comment-login-login" href="'.esc_url(has_wpqa()?wpqa_profile_url($user_id):"").'"><i class="icon-person"></i>'.esc_html($user_identity).'</a><a class="comment-login-logout" href="'.wp_logout_url(get_permalink()).'" title="'.esc_attr__("Log out of this account","himer").'"><i class="icon-log-out"></i>'.esc_html__('Log out',"himer").'</a></p>',
				'title_reply'          => ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?esc_html__("Leave an answer","himer"):esc_html__("Leave a comment","himer")),
				'title_reply_to'       => ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?esc_html__("Leave an reply to %s","himer"):esc_html__("Leave a comment to %s","himer")),
				'title_reply_before'   => (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && !is_user_logged_in() && !get_option('comment_registration')?'<div class="button-default btn btn__primary btn__extra__height btn__block show-answer-form">'.esc_html__("Leave an answer","himer").'</div>':'').'<h3 class="'.($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?'section__ttile':'section-title').(($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && !is_user_logged_in()?' comment-form-hide':'').'">',
				'title_reply_after'    => '</h3>',
				'class_form'           => 'post-section comment-form'.(($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && !is_user_logged_in()?' comment-form-hide':'').($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?' answers-form block-section-div':''),
				'comment_notes_after'  => '',
				'comment_notes_before' => '',
				'comment_field'        => '<div class="form-input form-textarea'.(isset($comment_editor) && $comment_editor == "on"?" form-comment-editor":" form-comment-normal").'">'.$comment_contents.'</div>',
				'fields'               => apply_filters('comment_form_default_fields',$fields),
				'label_submit'         => esc_html__("Submit","himer"),
				'class_submit'         => 'button-default button-hide-click btn btn__primary'.($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?' button-default-question':''),
				'cancel_reply_before'  => '<div class="cancel-comment-reply">',
				'cancel_reply_after'   => '</div>',
				'format'               => 'html5'
			);
			comment_form(apply_filters("himer_filter_comment_form",$comment_form_args,$post_type),$post->ID);
		echo '</div>';
	}
}else {
	do_action("himer_action_if_comments_closed");
}?>
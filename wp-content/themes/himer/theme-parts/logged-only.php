<?php $logged_only = himer_post_meta("logged_only");
if ($logged_only == "on" && !is_user_logged_in()) {
	echo '<article class="article-post block-section-div article-logged-only clearfix">
		<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry, log in to see the content.","himer").'</p></div>
	</article>';
	get_footer();
	die();
}?>
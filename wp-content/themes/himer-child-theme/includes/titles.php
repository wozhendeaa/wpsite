<?php /* Question title */
add_filter("wpqa_add_edit_question_before_form","himer_question_before_form",1,2);
function himer_question_before_form($return,$type) {
	return '<div class="card-header d-flex align-items-center flex-wrap justify-content-between card-header-2">
		<h2 class="card-title mb-0 d-flex align-items-center">
			<i class="icon-android-textsms font-xl card-title__icon"></i>
			<span>'.($type == "add"?esc_html__("Ask A Question","himer"):esc_html__("Edit Question","himer")).'</span>
		</h2>
	</div><!-- /.card-header -->';
}
/* Group title */
add_filter("wpqa_add_edit_group_before_form","himer_group_before_form",1,2);
function himer_group_before_form($return,$type) {
	return '<div class="card-header d-flex align-items-center flex-wrap justify-content-between card-header-2">
		<h2 class="card-title mb-0 d-flex align-items-center">
			<i class="icon-android-contacts font-xl card-title__icon"></i>
			<span>'.($type == "add"?esc_html__("Create A Group","himer"):esc_html__("Edit Group","himer")).'</span>
		</h2>
	</div><!-- /.card-header -->';
}
/* Post title */
add_filter("wpqa_add_edit_post_before_form","himer_post_before_form",1,2);
function himer_post_before_form($return,$type) {
	return '<div class="card-header d-flex align-items-center flex-wrap justify-content-between card-header-2">
		<h2 class="card-title mb-0 d-flex align-items-center">
			<i class="icon-ios-bookmarks font-xl card-title__icon"></i>
			<span>'.($type == "add"?esc_html__("Add A Post","himer"):esc_html__("Edit Post","himer")).'</span>
		</h2>
	</div><!-- /.card-header -->';
}
?>
<form action="<?php echo esc_url(site_url( '/wp-comments-post.php' ))?>" method="post" enctype="multipart/form-data">
	<?php if ($featured_image_group_post_comments == "on") {
		echo '<div class="wpqa_form">
	        <label for="featured_image">'.esc_html__('Featured image','himer').'</label>
	        <div class="fileinputs">
	        	<input type="file" name="featured_image" id="featured_image">
	        	<div class="fakefile">
	        		<button type="button">'.esc_html__('Select file','himer').'</button>
	        		<span>'.esc_html__('Browse','himer').'</span>
	        	</div>
	        	<i class="icon-camera"></i>
	        </div>
	    </div>
	    <div class="clearfix"></div>';
	}?>
	<div>
		<div class="form-input form-textarea form-comment-normal">
			<?php if ($editor_group_post_comments == "on") {
				$settings = array("textarea_name" => "comment","media_buttons" => true,"textarea_rows" => 10);
				$settings = apply_filters('wpqa_comment_editor_setting',$settings);
				ob_start();
				wp_editor("","comment",$settings);
				$comment_contents = ob_get_clean();
			}else {
				$comment_contents = '<textarea cols="58" rows="8" class="form-control" name="comment" aria-required="true" placeholder="'.apply_filters("himer_filter_textarea_comment_group",esc_html__("Comment","himer")).'"></textarea>';
			}
			echo '<div class="form-input form-textarea'.($editor_group_post_comments == "on"?" form-comment-editor":" form-comment-normal").'">'.$comment_contents.'</div>'?>
		</div>
	</div>
	<div class="cancel-comment-reply"><?php cancel_comment_reply_link(esc_html__("Click here to cancel reply.","himer"));?></div>
	<p class="form-submit<?php echo (wpqa_is_view_posts_group()?"":" mb-0")?>">
		<span class="load_span"><span class="loader_2"></span></span>
		<button name="submit" type="submit" class="button-default button-hide-click btn btn__primary"><?php esc_html_e('Comment','himer')?></button>
	</p>
	<?php comment_id_fields($post_data->ID);
	do_action('comment_form', $post_data->ID);?>
</form>
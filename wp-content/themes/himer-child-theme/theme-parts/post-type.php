<?php $post_head_style = "";
if ($what_post == "quote" || $what_post == "link" || $what_post == "twitter" || $what_post == "facebook" || $what_post == "instagram" || $what_post == "soundcloud") {
	$himer_padding_top = himer_post_meta("padding_top");
	$himer_padding_right = himer_post_meta("padding_right");
	$himer_padding_bottom = himer_post_meta("padding_bottom");
	$himer_padding_left = himer_post_meta("padding_left");
	$post_head_background_transparent = himer_post_meta("post_head_background_transparent");
	$post_head_background = himer_post_meta("post_head_background");
	$post_head_background_img = himer_post_meta("post_head_background_img");
	$post_head_background_repeat = himer_post_meta("post_head_background_repeat");
	$post_head_background_fixed = himer_post_meta("post_head_background_fixed");
	$post_head_background_position_x = himer_post_meta("post_head_background_position_x");
	$post_head_background_position_y = himer_post_meta("post_head_background_position_y");
	$post_head_background_full = himer_post_meta("post_head_background_full");
	$himer_link_color = himer_post_meta("link_color");
	$himer_quote_color = himer_post_meta("quote_color");
	if ((isset($himer_padding_top) && $himer_padding_top != "") || (isset($himer_padding_right) && $himer_padding_right != "") || (isset($himer_padding_bottom) && $himer_padding_bottom != "") || (isset($himer_padding_left) && $himer_padding_left != "") || (isset($post_head_background) && $post_head_background != "") || $post_head_background_transparent == "on" || (isset($post_head_background_img) && $post_head_background_img != "") || (isset($himer_link_color) && $himer_link_color != "") || (isset($himer_quote_color) && $himer_quote_color != "")) {
		$post_head_style .= " style='";
		$post_head_style .= (isset($himer_padding_top) && $himer_padding_top != ""?"padding-top:".$himer_padding_top."px;":"");
		$post_head_style .= (isset($himer_padding_right) && $himer_padding_right != ""?"padding-right:".$himer_padding_right."px;":"");
		$post_head_style .= (isset($himer_padding_bottom) && $himer_padding_bottom != ""?"padding-bottom:".$himer_padding_bottom."px;":"");
		$post_head_style .= (isset($himer_padding_left) && $himer_padding_left != ""?"padding-left:".$himer_padding_left."px;":"");
		$post_head_style .= (isset($himer_link_color) && $himer_link_color != ""?"color:".$himer_link_color.";":"");
		$post_head_style .= (isset($himer_quote_color) && $himer_quote_color != ""?"color:".$himer_quote_color.";":"");
		if ($post_head_background_transparent == "on") {
			$post_head_style .= "background-color: transparent !important;";
		}else {
			$post_head_style .= (isset($post_head_background) && $post_head_background != ""?"background-color:".$post_head_background.";":"");
		}
		if (isset($post_head_background_img) && $post_head_background_img != "") {
			$post_head_style .= (isset($post_head_background_img) && $post_head_background_img != ""?"background-image:url(".$post_head_background_img.");":"");
			$post_head_style .= (isset($post_head_background_repeat) && $post_head_background_repeat != ""?"background-repeat:".$post_head_background_repeat.";":"");
			$post_head_style .= (isset($post_head_background_fixed) && $post_head_background_fixed != ""?"background-attachment:".$post_head_background_fixed.";":"");
			$post_head_style .= (isset($post_head_background_position_x) && $post_head_background_position_x != ""?"background-position-x:".$post_head_background_position_x.";":"");
			$post_head_style .= (isset($post_head_background_position_y) && $post_head_background_position_y != ""?"background-position-y:".$post_head_background_position_y.";":"");
			$post_head_style .= (isset($post_head_background_full) && $post_head_background_full == "on"?"-webkit-background-size: cover;-moz-background-size: cover;-o-background-size: cover;background-size: cover;":"");
		}
		$post_head_style .= "'";
	}
}
$himer_quote_author = himer_post_meta("quote_author");
$himer_quote_content = himer_post_meta("quote_content");
$himer_quote_style = himer_post_meta("quote_style");

$himer_link_target = himer_post_meta("link_target");
$himer_link = himer_post_meta("link");
$himer_link_title = himer_post_meta("link_title");
$himer_link_style = himer_post_meta("link_style");

if ($what_post == "quote") {?>
	<div class="post-inner-quote" <?php echo stripcslashes($post_head_style)?>>
		<div class="quote">
			<blockquote>
				<div class="quote-inner">
					<div class="post-type"><i class="fa fa-quote-left"></i></div>
					
					<div class="post-inner-content"><p><?php echo esc_html($himer_quote_content);?></p></div>
					<?php if ($himer_quote_author != "") {?>
						<cite class="author">- <?php echo esc_html($himer_quote_author)?></cite>
					<?php }?>
				</div>
			</blockquote>
		</div><!-- End quote -->
		<div class="clearfix"></div>
	</div><!-- End post-inner-quote -->
<?php }else if ($what_post == "link") {?>
	<a <?php echo stripcslashes($post_head_style)?> href="<?php echo esc_url($himer_link)?>" <?php echo ("style_2" == $himer_link_target?"target='_blank'":"")?> class="post-inner-link link">
		<div class="post-type"><i class="fa fa-link"></i></div>
		<div class="post-link-inner">
			<?php echo esc_html($himer_link_title)?>
			<span><?php echo esc_url($himer_link)?></span>
		</div><!-- End post-link-inner -->
	</a><!-- End post-inner-link -->
<?php }?>
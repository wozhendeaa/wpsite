<?php if ($newsletter_blog == "on" && $newsletter_action != "") {?>
	<div class="blog-widget blog-widget-newsletter">
		<h5 class="blog-widget__title">
			<i class="icon-paper-airplane mr-2"></i>
			<span><?php esc_html_e("Newsletter","himer")?></span>
		</h5>
		<form class="d-flex validate" action="<?php echo esc_attr($newsletter_action)?>" method="post" name="mc-embedded-subscribe-form" target="_blank" novalidate>
			<input type="email" name="EMAIL" class="form-control mr-2" placeholder="<?php esc_attr_e("Your Email","himer")?>">
			<button type="submit" name="subscribe" class="btn btn__primary"><?php esc_html_e("Subscribe","himer")?></button>
		</form>
	</div><!-- /.blog-widget-newsletter -->
<?php }?>
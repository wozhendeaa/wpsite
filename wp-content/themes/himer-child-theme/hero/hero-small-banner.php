<?php $featured_image = get_post_meta($post->ID,'_thumbnail_id',true);?>
<div class="hero-banner hero-banner-mini hero-banner-very-mini d-flex flex-column justify-content-end"<?php echo stripcslashes($featured_image != ""?himer_image_style(himer_get_aq_resize_image_url(370,203)):"")?>>
	<div class="bg-overlay"></div>
	<div class="hero-banner-mini-wrap">
		<h3 class="hero__title mb-0"><a href="<?php echo the_permalink()?>"><?php echo himer_excerpt_title(15)?></a></h3>
	</div>
</div><!-- /.hero-banner -->
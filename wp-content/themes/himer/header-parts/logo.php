<?php if ($logo_display == "custom_image") {?>
    <a class="navbar-brand logo-img" href="<?php echo esc_url(home_url('/'));?>" title="<?php echo esc_attr(get_bloginfo('name','display'))?>">
    	<?php if ((isset($logo_img) && $logo_img != "") || ($retina_logo == "" && isset($logo_img) && $logo_img != "")) {?>
            <img title="<?php echo esc_attr(get_bloginfo('name','display'))?>" height="<?php echo esc_attr($logo_height)?>" width="<?php echo esc_attr($logo_width)?>" class="logo <?php echo ("on" == $skin_switcher && $custom_dark_logo == "on" && ((isset($dark_logo_img) && $dark_logo_img != "") || (isset($dark_retina_logo) && $dark_retina_logo == "" && isset($dark_logo_img) && $dark_logo_img != ""))?"light-logo ":"").("" == $retina_logo && isset($logo_img) && $logo_img != ""?"retina_screen":"default_screen")?>" alt="<?php echo esc_attr(get_bloginfo('name','display'))?> <?php esc_html_e('Logo','himer')?>" src="<?php echo esc_url($logo_img)?>">
        <?php }
        if (isset($retina_logo) && $retina_logo != "") {?>
            <img title="<?php echo esc_attr(get_bloginfo('name','display'))?>" height="<?php echo esc_attr($logo_height)?>" width="<?php echo esc_attr($logo_width)?>" class="retina_screen<?php echo ("on" == $skin_switcher && $custom_dark_logo == "on"?" light-logo":"")?>" alt="<?php echo esc_attr(get_bloginfo('name','display'))?> <?php esc_html_e('Logo','himer')?>" src="<?php echo esc_url($retina_logo)?>">
        <?php }
        if ((isset($dark_logo_img) && $dark_logo_img != "") || (isset($dark_retina_logo) && $dark_retina_logo == "" && isset($dark_logo_img) && $dark_logo_img != "")) {?>
            <img title="<?php echo esc_attr(get_bloginfo('name','display'))?>" height="<?php echo esc_attr($logo_height)?>" width="<?php echo esc_attr($logo_width)?>" class="dark-logo default-logo logo <?php echo (isset($dark_retina_logo) && $dark_retina_logo && isset($dark_logo_img) && $dark_logo_img != ""?"default_screen":"retina_screen")?>" alt="<?php echo esc_attr(get_bloginfo('name','display'))?> <?php esc_html_e('Logo','himer')?>" src="<?php echo esc_url($dark_logo_img)?>">
        <?php }
        if (isset($dark_retina_logo) && $dark_retina_logo != "") {?>
            <img title="<?php echo esc_attr(get_bloginfo('name','display'))?>" height="<?php echo esc_attr($logo_height)?>" width="<?php echo esc_attr($logo_width)?>" class="dark-logo retina_screen" alt="<?php echo esc_attr(get_bloginfo('name','display'))?> <?php esc_html_e('Logo','himer')?>" src="<?php echo esc_url($dark_retina_logo)?>">
        <?php }?>
    </a>
<?php }else {?>
	<a href="<?php echo esc_url(home_url('/'));?>" title="<?php echo esc_attr(get_bloginfo('name','display'))?>" class='logo-name navbar-brand'><?php echo esc_html(get_bloginfo('name','display'))?></a>
<?php }?>
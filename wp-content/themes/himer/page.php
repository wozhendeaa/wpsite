<?php get_header();
	include locate_template("theme-parts/logged-only.php");
	$theme_sidebar_all = $theme_sidebar = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_sidebars("sidebar_where"):"");
	include locate_template("includes/loop-setting.php");
	if ( have_posts() ) :?>
		<div class="post-articles">
			<?php while ( have_posts() ) : the_post();
				include locate_template("theme-parts/content.php");
			endwhile;?>
		</div><!-- End post-articles -->
	<?php else :
		include locate_template("theme-parts/content-none.php");
	endif;
get_footer();?>
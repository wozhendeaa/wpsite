<?php if ( have_posts() ) :
	while (have_posts()) : the_post();
		$get_the_content = get_the_content();
		$get_the_content = apply_filters('the_content',$get_the_content);
		echo make_clickable($get_the_content);
	endwhile;
endif;?>
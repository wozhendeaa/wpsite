<?php
$royal_news_magazine_options = royal_news_magazine_theme_options();
$three_column_post_category = $royal_news_magazine_options['three_column_post_category'];
$three_column_post_title = $royal_news_magazine_options['three_column_post_title'];
?>

<?php
if ($three_column_post_category && 'none' != $three_column_post_category) {
    $args = [
        'post_type' => 'post',
        'posts_per_page' => 5,
        'post_status' => 'publish',
        'order' => 'desc',
        'orderby' => 'menu_order date',
        'tax_query' => [
            'relation' => 'AND',
            [
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => [$three_column_post_category],
            ],
        ],
    ];
} else {
    $args = [
        'post_type' => 'post',
        'posts_per_page' => 5,
        'post_status' => 'publish',
        'order' => 'desc',
        'orderby' => 'menu_order date',
    ];
}

$blog_query = new WP_Query($args);
$loop = 0;

if ($blog_query->have_posts()): ?>




<div class="top-small-carousel-section section">

    
	<div class="container">
		<div class="row">
            <div class="col-md-12">
            <div class="title-wrap">
                            <?php
                            
                             if ($three_column_post_title):
                                echo '<h3>' . esc_html($three_column_post_title) . '</h3>';
                          
                           endif; ?>
                           <span class="line-dots"></span>
                        </div>
			<div class="card-slider-wrap threecolumn">

            <?php
            while ($blog_query->have_posts()):

                $blog_query->the_post();

                    $image_src = wp_get_attachment_image_src(
                        get_post_thumbnail_id(),
                        'thumbnail'
                    );

                if($image_src){
                    $url = $image_src[0];
                    }
                ?>

				<div class="small-carousel-wraps">
					<div class="small-carousel-content-wrap">
						<div class="carousel-thumb">
                        
                        <a href="<?php echo esc_url(get_the_permalink()); ?>"><img src="<?php echo esc_url($url); ?>"></a>
						</div>
						<div class="post-content">
							<h3>
								<a href="<?php echo esc_url(get_the_permalink()); ?>"><?php the_title(); ?></a>
							</h3>
							<ul class="post-meta">
                            <li class="meta-date"><a href="<?php echo esc_url(
                                royal_news_magazine_archive_link($post)
                            ); ?>"><time class="entry-date published" datetime="<?php echo esc_url(
    royal_news_magazine_archive_link($post)
); ?>"><?php echo esc_html(the_time(get_option('date_format'))); ?></time>
                                                </a></li>

							</ul>
						</div>
					</div>
				</div>

                <?php
            endwhile;
            wp_reset_postdata();
            ?>
			</div>
		</div>
        </div>
	</div>
</div>

<?php endif;
?>


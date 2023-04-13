<?php
$royal_news_magazine_options = royal_news_magazine_theme_options();
$second_blog_carousel = $royal_news_magazine_options['second_blog_carousel'];
$second_blog_section_title = $royal_news_magazine_options['second_blog_section_title'];
?>

<?php
if ($second_blog_carousel && 'none' != $second_blog_carousel) {
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
                'terms' => [$second_blog_carousel],
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




<div class="blog-carousel-section section bottom-carousel">
<div class="container">
        <div class="row">
      
                        <div class="section-title">
                            <?php
                            
                             if ($second_blog_section_title):
                                echo '<h2>' . esc_html($second_blog_section_title) . '</h2>';
                          
                           endif; ?>
                        </div>
                   
                </div>
        </div>

	<div class="container">
		<div class="row">
			<div class="card-slider-wrap threecolumn">

            <?php
            while ($blog_query->have_posts()):

                $blog_query->the_post();

                    $image_src = wp_get_attachment_image_src(
                        get_post_thumbnail_id(),
                        'royal-news-magazine-blog-thumbnail-img'
                    );

                if($image_src){
                    $url = $image_src[0];
                    }
                ?>

				<div class="carousel-wraps">
					<div class="carousel-content-wrap">
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
                                                <li class="meta-comment"><a
                                                    href="<?php echo esc_url(
                                                        get_comments_link(
                                                            get_the_ID()
                                                        )
                                                    ); ?>"><?php printf(
    /* translators: 1: number of comments */ _nx(
        '%1$s Comment',
        '%1$s Comments',
        get_comments_number(),
        '',
        'royal-news-magazine'
    ),
    number_format_i18n(get_comments_number())
); ?></a></li>
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

<?php endif;
?>


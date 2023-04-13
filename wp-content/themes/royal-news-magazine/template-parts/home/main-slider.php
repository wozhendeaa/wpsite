<?php
$royal_news_magazine_options = royal_news_magazine_theme_options();
$main_banner_category = $royal_news_magazine_options['main_banner_category'];
$main_banner_second_column_category = $royal_news_magazine_options['main_banner_second_column_category'];
$main_banner_third_column_category = $royal_news_magazine_options['main_banner_third_column_category'];

$main_slider_title = $royal_news_magazine_options['main_slider_title'];
$main_banner_second_column_title = $royal_news_magazine_options['main_banner_second_column_title'];
$main_banner_third_column_title = $royal_news_magazine_options['main_banner_third_column_title'];
$content_length = '250';
?>



<div class="main-slider-section" id="primary">
    <div class="container">
    <div class="row">

    <div class="col-md-6">
    <div class="title-wrap">
                            <?php
                            
                             if ($main_slider_title):
                                echo '<h3>' . esc_html($main_slider_title) . '</h3>';
                          
                           endif; ?>
                           <span class="line-dots"></span>
                        </div>

         <?php   if ($main_banner_category && 'none' != $main_banner_category) {
    $args = [
        'post_type' => 'post',
        'posts_per_page' => 3,
        'post_status' => 'publish',
        'order' => 'desc',
        'orderby' => 'menu_order date',
        'tax_query' => [
            'relation' => 'AND',
            [
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => [$main_banner_category],
            ],
        ],
    ];
} else {
    $args = [
        'post_type' => 'post',
        'posts_per_page' => 3,
        'post_status' => 'publish',
        'order' => 'desc',
        'orderby' => 'menu_order date',
    ];
}

$blog_query = new WP_Query($args);
$loop = 0;

if ($blog_query->have_posts()): ?>
            <div class="main-slider">
                
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

                                if (!empty($image_src)) {
                                    $image_style =
                                        "style='background-image:url(" . esc_url($url) . ")'"; ?>
                                    <?php
                                } else {
                                    $image_style = '';
                                }
                            ?>
                        <div class="main-slider-wrap">
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
                            <p><?php echo wp_kses_post(
                                        royal_news_magazine_get_excerpt(
                                            $blog_query->post->ID,
                                            $content_length
                                        )
                                    ); ?></p>
						</div>  
					</div>
                        <?php endwhile;
                        wp_reset_postdata();
                        ?>
                </div>
                <?php endif;
        ?>
        </div>
        

                                  

        <div class="col-md-3">
        <div class="title-wrap">
                            <?php
                            
                             if ($main_banner_second_column_title):
                                echo '<h3>' . esc_html($main_banner_second_column_title) . '</h3>';
                          
                           endif; ?>
                           <span class="line-dots"></span>
                        </div>

            <?php   if ($main_banner_second_column_category && 'none' != $main_banner_second_column_category) {
    $args = [
        'post_type' => 'post',
        'posts_per_page' => 6,
        'post_status' => 'publish',
        'order' => 'desc',
        'orderby' => 'menu_order date',
        'tax_query' => [
            'relation' => 'AND',
            [
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => [$main_banner_second_column_category],
            ],
        ],
    ];
} else {
    $args = [
        'post_type' => 'post',
        'posts_per_page' => 6,
        'post_status' => 'publish',
        'order' => 'desc',
        'orderby' => 'menu_order date',
    ];
}

$second_blog_query = new WP_Query($args);
$loop = 0;
if ($second_blog_query->have_posts()): ?>
            <div class="no-thumbnail-section">
                
                    <?php
                        while ($second_blog_query->have_posts()):
                            $second_blog_query->the_post();

                            ?>
                        <div class="no-thumbnail-wrap">
						
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
                        <?php endwhile;
                        wp_reset_postdata();
                        ?>
                </div>
                <?php endif;
        ?>


        </div>




        <div class="col-md-3">
        <div class="title-wrap">
                            <?php
                            
                             if ($main_banner_third_column_title):
                                echo '<h3>' . esc_html($main_banner_third_column_title) . '</h3>';
                          
                           endif; ?>
                           <span class="line-dots"></span>
                        </div>

            <?php   if ($main_banner_third_column_category && 'none' != $main_banner_third_column_category) {
    $args = [
        'post_type' => 'post',
        'posts_per_page' => 4,
        'post_status' => 'publish',
        'order' => 'desc',
        'orderby' => 'menu_order date',
        'tax_query' => [
            'relation' => 'AND',
            [
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => [$main_banner_third_column_category],
            ],
        ],
    ];
} else {
    $args = [
        'post_type' => 'post',
        'posts_per_page' => 4,
        'post_status' => 'publish',
        'order' => 'desc',
        'orderby' => 'menu_order date',
    ];
}

$third_blog_query = new WP_Query($args);
$loop = 0;
if ($third_blog_query->have_posts()): ?>
            <div class="with-thumbnail-section">
                
                <?php
                    while ($third_blog_query->have_posts()):
                        $third_blog_query->the_post();

                        $image_src = wp_get_attachment_image_src(
                            get_post_thumbnail_id(),
                            'thumbnail'
                        );
                        if($image_src){
                            $url = $image_src[0];
                            }

                            if (!empty($image_src)) {
                                $image_style =
                                    "style='background-image:url(" . esc_url($url) . ")'"; ?>
                                <?php
                            } else {
                                $image_style = '';
                            }
                        ?>
                    <div class="with-thumbnail-wrap">
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
                    <?php endwhile;
                    wp_reset_postdata();
                    ?>
            </div>
            <?php endif;
        ?>

        </div>
    </div>
</div>
</div>



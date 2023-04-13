<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package royal_news_magazine
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php if ( is_archive() || is_home()||is_search()) :
	royal_news_magazine_post_thumbnail(); 
	endif;?>
	<header class="entry-header">
		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;

		if ( 'post' === get_post_type() ) :
			?>
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
<li>
<span class="author vcard">
                            <?php esc_html_e('By' , 'royal-news-magazine'); ?>&nbsp;<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php echo esc_html(get_the_author()); ?></a>
                        </span>
</li>
							</ul>
		<?php endif; ?>
	</header><!-- .entry-header -->

	<?php if ( is_single() ) :
	royal_news_magazine_post_thumbnail(); 
	endif;?>

	<div class="entry-content">
            <?php

            global $numpages;
           
			if (is_archive() || is_home()||is_search()):
				echo wp_kses_post(royal_news_magazine_get_excerpt($post->ID, 200));
		else:
			the_content(sprintf(wp_kses(__('Continue reading<span class="screen-reader-text"> "%s"</span>', 'royal-news-magazine'),array('span' => array('class' => array(),),)),get_the_title()));
		endif;
            
            if(is_single()) {
                wp_link_pages(array(
                    'before' => '<div class="page-links">' . esc_html__('Pages:', 'royal-news-magazine'),
                    'after' => '</div>',
                    'link_before' => '<span>',
                    'link_after'  => '</span>',
                ));
            }
            ?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php royal_news_magazine_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->

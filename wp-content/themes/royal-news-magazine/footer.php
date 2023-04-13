<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package royal_news_magazine
 */


$royal_news_magazine_options = royal_news_magazine_theme_options();

$show_prefooter = $royal_news_magazine_options['show_prefooter'];

?>

<footer id="colophon" class="site-footer">


	<?php if ($show_prefooter== 1){ ?>
	    <section class="royal-news-magazine-footer-sec">
	        <div class="container">
	            <div class="row">
					<div class="col-md-12">
					<div class="footer-border">
	                <?php if (is_active_sidebar('royal_news_magazine_footer_1')) : ?>
	                    <div class="col-md-3">
	                        <?php dynamic_sidebar('royal_news_magazine_footer_1') ?>
	                    </div>
	                    <?php
	                else: royal_news_magazine_blank_widget();
	                endif; ?>
	                <?php if (is_active_sidebar('royal_news_magazine_footer_2')) : ?>
	                    <div class="col-md-3">
	                        <?php dynamic_sidebar('royal_news_magazine_footer_2') ?>
	                    </div>
	                    <?php
	                else: royal_news_magazine_blank_widget();
	                endif; ?>
	                <?php if (is_active_sidebar('royal_news_magazine_footer_3')) : ?>
	                    <div class="col-md-3">
	                        <?php dynamic_sidebar('royal_news_magazine_footer_3') ?>
	                    </div>
	                    <?php
	                else: royal_news_magazine_blank_widget();
	                endif; ?>

					<?php if (is_active_sidebar('royal_news_magazine_footer_4')) : ?>
	                    <div class="col-md-3">
	                        <?php dynamic_sidebar('royal_news_magazine_footer_4') ?>
	                    </div>
	                    <?php
	                else: royal_news_magazine_blank_widget();
	                endif; ?>
					</div>
					</div>
	            </div>
	        </div>
	    </section>
	<?php } ?>

		<div class="site-info">
		<p><?php esc_html_e('Powered By WordPress', 'royal-news-magazine');
                    esc_html_e(' | ', 'royal-news-magazine') ?>
                    <span><a target="_blank" rel="nofollow"
                       href="<?php echo esc_url('https://elegantblogthemes.com/theme/royal-news-magazine-best-newspaper-and-magazine-wordpress-theme/'); ?>"><?php esc_html_e('Royal News Magazine' , 'royal-news-magazine'); ?></a></span>
                </p>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
